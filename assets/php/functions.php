<?php

if(!is_file('.htaccess')){
  copy('assets/default/default.htaccess', '.htaccess');
}

if(!is_file('config.php')){
  copy('assets/default/default.config.php', 'config.php');
}

function isFile($folder,$file) {
  $return = 0;
  //echo $folder.$file;die;
  if(file_exists('categories/'.$folder.$file) || file_exists('categories/pages/'.$folder.$file)){
    $return = 1;
  }
  return $return;
}

function getComment($comment){
  $file=fopen($comment, 'r');
  $filecontent = fread($file, filesize($comment));
  $sect = explode('=-=-=',$filecontent); // Seperates post metadata from content.
  $contentMeta = parseMeta($sect[0]);
  $myHtml = Markdown($sect[1]);
  $contentMeta["post"] = $myHtml; // Reorganises some of the elements of the site's HTML, cleaning up where markdown does weird things to code
  $link = explode('/', $comment);
  fclose($file);
  return $contentMeta;
}

function formatDate($pubdate,$finalFormat){
  return date($finalFormat, strtotime($pubdate));
}

function generatePaging($perPage, $total, $site, $current){
  $pages = ceil($total/$perPage);
  $toreturn = '<section id="pagination"><span>Pages:</span><ol>';
  for ($i = 1; $i <= $pages; $i++) {
    $toreturn .= '<li><a ';
    if($i == $current){ $toreturn .= ' class="current" ';}
    $toreturn .= 'href="/'.$site->slug[0].'/page/'.$i.'/">'.$i.'</a></li>';
  }
  $toreturn .= '</ol></section>';
  return $toreturn;
}

function parseMeta($meta){
  $postmeta = explode("\n",$meta); // Gets individual meta fields.
  foreach ($postmeta as $meta) {
    $topost = explode(': ',$meta);
    if($topost[0] != '' && isset($topost[1])){
      $contentMeta[$topost[0]] = $topost[1];
    } elseif($topost[0] != '') {
      $topost[0] = str_replace(':', '', $topost[0]);
      $contentMeta[$topost[0]] = '';
    }
  }
  return $contentMeta;
}

function getTags($search,$site){
  $page->noindex = 1;
  // Wee_ Article path
  $path = 'categories';
  $search = urldecode($search);

  // Open the article path of the Journal
  $journeyDirectory = scandir( $path );

  foreach ($journeyDirectory as $category) {
    //echo $category;
    if(!in_array($category, $site->nofeed)){
      //$cat = scandir( $path.'/'.$category );
      if(file_exists($path.'/'.$category.'/index.'.$site->ext)){
        $cat = preg_split( '/\r\n|\r|\n/', file_get_contents($path.'/'.$category.'/index.'.$site->ext));
      }else{
        $cat=scandir($file, 1);
      }
      foreach ($cat as $post) {
        if($post != '.' && $post != '..'){
          $dirArray[] = $category.'/'.$post;
        }
      }
    }
  }

  // Count elements in array
  $indexLength = count( $dirArray );

  // Sort them
  sort($dirArray);

  // Loop through the array of files and stack directories ( the posts ) only
  for($i=0; $i < $indexLength; $i++) {

    if( is_dir( $path.'/'.$dirArray[$i] ) ) {

      $postList[] = $path.'/'.$dirArray[$i];

    }
  }

  $taggedPosts = array();

  // Go through each post
  foreach( $postList as $post ) {

    // Load post file and attach contents to $content
    $postFile = $post.'/post.'.$site->ext;
    $file = fopen($postFile, 'r');
    $content = fread($file, filesize($postFile));
    fclose($file);

    // Explosions! Explode the meta and assign the header
    $meta = explode('=-=-=', $content);

    foreach(parseMeta($meta[0]) as $key => $value){
      $key = strtolower($key);
      $page->$key = $value;
    }
    $tags = explode(',', $page->tags);

    // Go through each of this posts tags
    foreach( $tags as $tag ) {
      // Lazy way to get post url
      $url = str_replace('categories/', '/', $post).'/';

      // If this post has the in-search tag
      if( trim(strtolower($tag)) === strtolower($search) ) {
        if(isset($page->link)) { $url = $page->link; }
        $taggedPosts[$url] = $page->title;
        if(isset($page->link)) { unset($page->link); }

      }

    }

  }

  $taggedLength = count( $taggedPosts );

  $content = '<h1>'.$taggedLength.' tagged Posts for <em>'.$search.'</em> </h1>';

    if( $taggedLength != 0 ) {
      $content .= '<ul>';
      foreach( $taggedPosts as $key => $value ) {

        $content .= '<a href="'.$key.'">'.$value.'</a>'.'<br/>'."\n";

      }
      $content .= '</li>';
    } else {

      $content .= '<p>There are no posts with the the tag <em>'. $search.'.</p>';

    }
    $page->title = 'Posts Tagged: '.$search;
    $page->contactme = 0;
    $page->content = $content;
    return $page;
}

function getArchive($site){
  $page->noindex = 1;
  // Wee_ Article path
  $path = 'categories';

  // Open the article path of the Journal
  $journeyDirectory = scandir( $path );

  foreach ($journeyDirectory as $category) {
    if(!in_array($category, $site->nofeed)){
      if(file_exists($path.'/'.$category.'/index.'.$site->ext)){
        $cat = preg_split( '/\r\n|\r|\n/', file_get_contents($path.'/'.$category.'/index.'.$site->ext));
      }else{
        $cat=scandir($file, 1);
      }
      foreach ($cat as $post) {
        if($post != '.' && $post != '..'){
          $dirArray[] = $category.'/'.$post;
        }
      }
    }
  }

  // Count elements in array
  $indexLength = count( $dirArray );

  // Sort them
  sort($dirArray);

  // Loop through the array of files and stack directories ( the posts ) only
  for($i=0; $i < $indexLength; $i++) {

    if( is_dir( $path.'/'.$dirArray[$i] ) ) {

      $postList[] = $path.'/'.$dirArray[$i];

    }
  }

  $archive = array();

  // Go through each post
  foreach( $postList as $post ) {

    // Load post file and attach contents to $content
    $postFile = $post.'/post.'.$site->ext;
    $file = fopen($postFile, 'r');
    $content = fread($file, filesize($postFile));
    fclose($file);

    // Explosions! Explode the meta and assign the header
    $meta = explode('=-=-=', $content);

    foreach(parseMeta($meta[0]) as $key => $value){
      $key = strtolower($key);
      $page->$key = $value;
    }
    $time = strtotime($page->pubdate);
    $url = str_replace('categories/', '/', $post).'/';
    if(isset($page->link)) { $url = $page->link; }
    $archive[$time]['title'] = $page->title;
    $archive[$time]['link'] = $url;
    if(isset($page->link)) { unset($page->link); }

  }

  krsort($archive);

  $year = '';
  $month = '';
  $list = '';
  $div = '';

  $content = '<h1>Site Archives</h1>';
  foreach ($archive as $key => $value) {
    if(date('Y', $key) != $year){
      if($list != ''){ $content .= '</ul>'."\n"; $list = ''; }
      if($div != ''){ $content .= '</div>'."\n"; $div = ''; }
      $year = date('Y', $key);
      $month = '';
      //$content .= '<h1>'.date('Y', $key).'</h1>'."\n";
    }
    if(date('F', $key) != $month){
      $month = date('F', $key);
      if($list != ''){ $content .= '</ul>'."\n"; $list = ''; }
      if($div != ''){ $content .= '</div>'."\n"; $div = ''; }
      $div = 1;
      $content .= '<div>'."\n";
      $content .= '<h2>'.date('F', $key).' '.$year.'</h2>'."\n";
      $list = 1;
      $content .= '<ul>'."\n";
    }
    $content .= '<li><a href="'.$value['link'].'">'.$value['title'].'</a></li>'."\n";
  }

  $page->title = 'Post Archive';
  $page->content = $content;
  unset($page->author);
  return $page;
}

function mimetype($ext){

  switch ($ext) {
    case 'oga':
    case 'ogg';
      return 'audio/ogg';
      break;
    case 'ogv':
      return 'video/ogg';
      break;
    case 'mp4':
      return 'video/mp4';
      break;
    case 'webm':
      return 'video/webm';
      break;
    case 'svg':
    case 'svgz';
      return 'image/svg+xml';
      break;
    case 'eot':
      return 'application/vnd.ms-fontobject';
      break;
    case 'ttf':
      return 'font/truetype';
      break;
    case 'otf':
      return 'font/opentype';
      break;
    case 'woff':
      return 'font/woff';
      break;
    case 'ico':
      return 'image/vnd.microsoft.icon';
      break;
    case 'webp':
      return 'image/webp';
      break;
    case 'png':
      return 'image/png';
      break;
    case 'jpg':
    case 'jpeg':
      return 'image/jpeg';
      break;
    case 'gif':
      return 'image/gif';
      break;
    case 'manifest':
      return 'text/cache-manifest';
      break;
    case 'css':
      return 'text/css';
      break;
    case 'mobi':
      return 'application/x-mobipocket-ebook';
      break;
    case 'epub':
      return 'application/epub+zip';
      break;
    case 'pdf':
      return 'application/pdf';
      break;
    case 'zip':
      return 'application/zip';
      break;
    case 'rar':
      return 'application/x-rar-compressed';
      break;
    default:
      return 'text/plain';
      break;
  }
}

function extFix($ext){
  $extensions = array(
    'ic' => 'ico',
    'jp' => 'jpg',
    'jpe' => 'jpeg',
    'pn' => 'png',
    'cs' => 'css',
    'j' => 'js',
    'sv' => 'svg',
    'zi' => 'zip',
    'htm' => 'html',
    'ht' => 'htm',
    'pd' => 'pdf',
    'epu' => 'epub',
    'mob' => 'mobi',
    'xm' => 'xml',
    'ot' => 'otf',
    'tt' => 'ttf',
    'wof' => 'woff',
    'eo' => 'eot',
    'gi' => 'gif',
    'tx' => 'txt',
    'm' => 'md',
    'tex' => 'text',
    'markdow' => 'markdown',
    'mdow' => 'mdown'
  );
  if (isset($extensions[$ext])) {
    return $extensions[$ext];
  } else {
    return $ext;
  }
}

function parseURL($slug,$site,$file=''){
  if(isset($slug[2]) && $slug[2] == 'post.'.substr($site->ext,0,-1)){
    $slug[2] = 'post.'.$site->ext;
  }
  if(isset($slug[1]) && $slug[1] == 'post.'.substr($site->ext,0,-1)){
    $slug[1] = 'post.'.$site->ext;
  }

  $ext = explode('.', end($slug));
  if(end($ext) != end($slug)){
    for ($i = 0; $i < count($ext) - 1; $i++) {
      $file .= $ext[$i].'.';
    }
    $file = $file.extFix(end($ext));
    $slugcount = count($slug)-1;
    $slug[$slugcount] = $file;
  }

  if($slug[0] == 'favicon.ico' && file_exists('assets/default/favicon.ico')){
    $mimetype = mimetype('ico');
    header('Content-type: '.$mimetype);
    include 'assets/default/favicon.ico';
    die;
  } elseif(file_exists('categories/pages'.$site->query) && !is_dir('categories/pages'.$site->query)){
    $ext = explode('.',$site->query);
    $mimetype = mimetype(end($ext));
    header('Content-type: '.$mimetype);
    include 'categories/pages'.$site->query;
    die;
  } elseif(file_exists('categories'.$site->query) && !is_dir('categories'.$site->query)){
    $ext = explode('.',$site->query);
    $mimetype = mimetype(end($ext));
    header('Content-type: '.$mimetype);
    include 'categories'.$site->query;
    die;
  } elseif($slug[0] == 'archive'){
    $site->noindex = 1;
    return getArchive($site);
  } elseif($slug[0] == 'tag' && isset($slug[1]) && $slug[1] != ''){
    $site->tag = 1;
    return getTags($slug[1],$site);
  } elseif($slug[0] == 'feed'){
    return generateFeed($site);
  } elseif($slug[0] == 'sitemap.xml') {
    return generateSitemap($site);
  } elseif(isset($slug[2]) && $slug[2] == 'post.'.$site->ext && isset($slug[1]) && is_dir('categories/'.$slug[0].'/'.$slug[1])){
    return loadMarkdown('categories/'.$slug[0].'/'.$slug[1].'/post.'.$site->ext);
  } elseif(!isset($slug[2]) && isset($slug[1]) && is_dir('categories/'.$slug[0].'/'.$slug[1]) && file_exists('categories/'.$slug[0].'/'.$slug[1].'/index.php')){
    $site->page = 0;
    $page = parseFile('categories/'.$slug[0].'/'.$slug[1].'/post.'.$site->ext,$site);
    include 'categories/'.$slug[0].'/'.$slug[1].'/index.php';
    die;
  } elseif(!isset($slug[1]) && isset($slug[0]) && is_dir('categories/pages/'.$slug[0]) && file_exists('categories/pages/'.$slug[0].'/index.php')){
    $site->page = 0;
    $page = parseFile('categories/pages/'.$slug[0].'/post.'.$site->ext,$site);
    include 'categories/pages/'.$slug[0].'/index.php';
    die;
  } elseif(!isset($slug[2]) && isset($slug[1]) && is_dir('categories/'.$slug[0].'/'.$slug[1])){
    $site->page = 0;
    return parseFile('categories/'.$slug[0].'/'.$slug[1].'/post.'.$site->ext,$site);
  } elseif(!isset($slug[2]) && !isset($slug[1]) && is_dir('categories/'.$slug[0]) || isset($slug[1]) && $slug[1] == 'page' && is_dir('categories/'.$slug[0])){
    $site->category = 1;
    return loadCategory('categories/'.$slug[0].'/',$site);
  } elseif (isset($slug[1]) && $slug[1] ==  'post.'.$site->ext && is_dir('categories/pages/'.$slug[0])) {
    return loadMarkdown('categories/pages/'.$slug[0].'/post.'.$site->ext,$site);
  } elseif (!isset($slug[1]) && is_dir('categories/pages/'.$slug[0])) {
    $site->page = 1;
    $parsedfile = parseFile('categories/pages/'.$slug[0].'/post.'.$site->ext,$site);
    return $parsedfile;
  } elseif ($slug[0] == 'force-download' && isset($slug[1]) && is_file('media/'.$slug[1])) {
    #echo 'media/'.$slug[1];die;
    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=".$slug[1]);
    $ext = explode('.',$site->query);
    $mimetype = mimetype(end($ext));
    header('Content-type: '.$mimetype);
    #header("Content-Type: application/zip");
    header("Content-Transfer-Encoding: binary");

    // Read the file from disk
    readfile('media/'.$slug[1]);die;
  } else {
    $site->page = 1;
    $site->error = 1;
    header("Status: 404 Not Found");
    return parseFile('categories/errors/404/post.'.$site->ext,$site);
  }
}

function loadMarkdown($file){
  header('Content-type: text/plain');
  echo file_get_contents($file);
  die;
}

function parseFile($file,$site,$break=0,$theme=1,$display = ''){
  $folder = str_replace('post.'.$site->ext, '', $file);
  $before = str_replace('post.'.$site->ext, 'include-before.php', $file);
  $after = str_replace('post.'.$site->ext, 'include-after.php', $file);
  if(file_exists($before)){
    //$include->before = file_get_contents($before);
    ob_start();
    include $before;
    $beforeContent = ob_get_contents();
    ob_end_clean();
    $include->before = $beforeContent;
  }
  if(file_exists($after)){
    //$include->after = file_get_contents($after);
    ob_start();
    include $after;
    $afterContent = ob_get_contents();
    ob_end_clean();
    $include->after = $afterContent;
  }
  if($theme){
    if(file_exists('themes/'.$site->theme.'/content.php')) {
      $template = file_get_contents('themes/'.$site->theme.'/content.php');
    } else {
      $template = '<article class="[[slug]]">[[content]]</article>';
    }
  } else {
    $template = '[[content]]';
  }
  $segments = explode('=-=-=', file_get_contents($file));
  foreach(parseMeta($segments[0]) as $key => $value){
    $key = strtolower($key);
    $page->$key = $value;
  }
  if($break && $site->more){
    $post = Markdown($segments[1]);
    $post = explode('<!--[More]-->', $post);
    $contents = $post[0];
    if(isset($page->link)){
      $contents .= '<a class="readmore" href="'.$page->link.'">'.$site->readmore.'</a>';
    } else {
      $contents .= '<a class="readmore" href="'.str_replace('categories', '', str_replace('post.'.$site->ext, '', $file)).'">'.$site->readmore.'</a>';
    }
  } else {
    $contents = Markdown($segments[1]);
  }
  if(isset($page->link) && $page->link != '' && !$break && $theme){
    header('Location: '.$page->link);
    die;
  }

  if(isset($page->author)){
    $author = $page->author;
  } else {
    $author = $site->author->name;
  }

  if(isset($include->before) && !$break){
    $display .= $include->before;
  }
  $display .= $contents;
  if(isset($include->after) && !$break){
    $display .= $include->after;
  }

  $content = str_replace('[[content]]', $display, $template);
  $content = str_replace('[[slug]]', (isset($site->slug[1]) ? $site->slug[1] : $site->slug[0]), $content);

  if(!isset($site->page) || !$site->page){
    $content = str_ireplace("<!--[TimeStamp]-->", '<p class="timestamp">'.(isset($site->date->format->before) ? $site->date->format->before : '').'<time datetime="'.formatDate($page->pubdate,'c').'">'.formatDate($page->pubdate,$site->date->format->structure).'</time>'.(isset($site->date->format->after) ? str_replace("<!--[Author]-->",$author,$site->date->format->after) : '').'</p>', $content);
    if(!isset($site->page) || !$site->page){
      // Tagging plx
      $tagsBoom = explode(',', $page->tags);
      $tags = '';
      foreach ($tagsBoom as $singleTag) {
        if($tags != ''){
          if($site->tag->divider !== ''){
            $tags .= '<li class="divider">'.$site->tag->divider.'</li>';
          }
        }
        if($singleTag != ''){
          $tags .= '<li><a class="tag-'.strtolower(str_replace('+','-',urlencode(trim($singleTag)))).'" href="/tag/'.urlencode(trim($singleTag)).'/">'.trim($singleTag).'</a></li>';
        }
      }
      if($tags != ''){
        if($site->tag->intro !== ''){
          $intro = '<li>'.$site->tag->intro.'</li>';
        } else {
          $intro = '';
        }
        $page->parsedTags = '<ul class="tags">'.$intro.$tags.'</ul>';
      } else {
        $page->parsedTags = '';
      }
      $content = str_replace('[[tags]]',$page->parsedTags,$content);
      $content = str_replace('<!--[tags]-->',$page->parsedTags,$content);
      $content = preg_replace('/<p>\s*(<a .*>)?\s*(<img .* \/>)\s*(<\/a>)?\s*<\/p>/iU', '\1\2\3', $content);
    }
    if(!$break){
      $content = str_replace("<time ", "<time pubdate ", $content);
    }
  }
  if(isset($site->page) && $site->page){
    $content = str_replace('href="/', 'href="'.$site->url.'/',
               str_replace('href="#', 'href="'.$site->url.'/'.$site->slug[0].'/'.'#',
               $content));
    $content = preg_replace('#(href|src)="([^:"]*)(?:")#','$1="'.$site->url.'/'.$site->slug[0].'/'.'$2"',$content);
  } elseif(isset($site->slug[1])) {
    $content = str_replace('href="/', 'href="'.$site->url.'/',
               str_replace('href="#', 'href="'.$site->url.'/'.$site->slug[0].'/'.$site->slug[1].'/'.'#',
               $content));
    $content = preg_replace('#(href|src)="([^:"]*)(?:")#','$1="'.$site->url.'/'.$site->slug[0].'/'.$site->slug[1].'/'.'$2"',$content);
  } else {
    $site->post = str_replace('categories/'.$site->slug[0].'/', '', $folder);
    $content = str_replace('href="/', 'href="'.$site->url.'/',
               str_replace('href="#', 'href="'.$site->url.'/'.$site->slug[0].'/'.$site->post.'/'.'#',
               $content));
    $content = preg_replace('#(href|src)="([^:"]*)(?:")#','$1="'.$site->url.'/'.$site->slug[0].'/'.$site->post.''.'$2"',$content);
  }

  $content = str_replace('[[tags]]', '', $content);
  $content = str_replace('<!--[tags]-->', '', $content);
  $page->content = $content;
  return $page;
}

function loadCategory($file,$site){
  if(isset($site->slug[2])){
    $curpage = $site->slug[2];
  } else {
    $curpage = 1;
  }
  $page->noindex = 1;

  if(file_exists($file.'index.'.$site->ext)){
    $posts = preg_split( '/\r\n|\r|\n/', file_get_contents($file.'index.'.$site->ext));
  }else{
    $posts=scandir($file, 1);
  }
  //$page = new stdClass();
  $page->title = ucwords(str_replace('-', ' ', $site->slug[0]));
  $content = '';
  $check = 1;
  $count = 0;

  $start = (($curpage * $site->posts->page) - ($site->posts->page))+1;
  $end=$curpage * $site->posts->page;

  foreach ($posts as $post) {
    if(!in_array($post, array('.', '..', '.DS_Store', 'index.'.$site->ext)) && file_exists($file.$post.'/post.'.$site->ext) &&
     $check >= $start && $check <= $end){
      $content .= parseFile($file.$post.'/post.'.$site->ext,$site,1)->content;
    }
    $check++;
    if(!in_array($post, array('.', '..', '.DS_Store', 'index.'.$site->ext)) && file_exists($file.$post.'/post.'.$site->ext)){
      $count++;
    }
  }
  if($count > $site->posts->page){
    $content .= generatePaging($site->posts->page, $count, $site, $curpage);
  }
  if($content == ''){
    $site->page = 1;
    $site->error = 1;
    return parseFile('categories/errors/404/post.'.$site->ext,$site);
  } else {
    $page->content = $content;
  }
  return $page;
}

function generateSitemap($site){
  include 'sitemap.php';
}

function generateFeed($site){
  include 'feed.php';
}