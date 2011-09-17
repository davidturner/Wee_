<?php
ob_start("ob_gzhandler"); // Cheap and cheery gzip compression
$content = '';
include_once "config.php"; // User Settings
include_once "resources/markdown.php"; // Markdown converts markdown format to the HTMLs
include_once "resources/Akismet.class.php";
include_once "resources/markdownify.php";
include_once "resources/markdownify_extra.php";

$md = new Markdownify_Extra;

/* Akismet Setup Stuff */
if(defined("akismet")){
  $WordPressAPIKey = akismet;
  $MyBlogURL = url; 
  $akismet = new Akismet(url ,akismet);
}

$path = $_SERVER['REQUEST_URI'];

$bits = explode("/", $path); // Breaks the information collected above into manageable chunks, used to define what the page does.
if($bits[1]==''){ $bits[1] = home; } // Determines home page based on defined constant in config.php  
function getpaging($total,$current,$perPage,$category){ // This function exists to provide pagination for category pages of the site.
  $pages = ceil($total/PostsPerPage);
  $start = 1;
  if($pages != 1 && $total != (PostsPerPage +1)){
  $toreturn = '<section id="pagination"><span>Pages:</span><ol>';
    while ($start <= $pages) {
      $toreturn .= '<li><a ';
      if($start == $current){ $toreturn .= 'class="current" ';}
      $toreturn .= 'href="'.url.'/'.$category.'/page/'.$start.'/">'.$start.'</a></li>';
      $start++;
    }
    $toreturn .= '</ol></section>';
  }else{
    $toreturn = '';
  }
  return $toreturn;
}
function toDisplay($myText,$category='',$splode = false){
  global $bits, $contentMeta;
  $file=fopen($myText, 'r');
  $filecontent = fread($file, filesize($myText));
  $sect = explode('=-=-=',$filecontent); // Seperates post metadata from content.
    $contentMeta = parseMeta($sect[0]);
  if (!defined('title')) {
    if($splode && $category){
      define('title',$category);
    }else{
      define('title',$contentMeta['Title']);
      define('description',$contentMeta['Desc']);
      define('keywords',$contentMeta['Tags']);
    }
  }
  $myHtml = Markdown($sect[1]);
  $content = '<article class="'.$bits[1].'">'."\n\t".str_replace("</cite></p>","</cite>",str_replace("<p><cite>","<cite>",str_replace('<p><img','<img',str_replace('/></p>','/>',$myHtml)))).'</article>'; // Reorganises some of the elements of the site's HTML, cleaning up where markdown does weird things to code
  
  $content = str_replace(array('<p></p>',' markdown="1"'),'',$content);
  $link = explode('/', $myText);
  if($splode){
    $buttonText = "Read the Full Post";
    if($category == "examples"){
      $buttonText = "View Demo";
    }
    
    $val = explode('<!--[More]-->',$content); // Removes content seperated by the <!--[More]--> tag, which is used entirely for creating post excerpts.
    if (sizeof($val)>1) {
      if(isset($contentMeta["Link"]) && $contentMeta["Link"] != ""){
        $content=$val[0].'<a href="'.$contentMeta["Link"].'" class="cta">'.$buttonText.'</a></article>';
      } else {
        $content=$val[0].'<a href="'.url.'/'.$category.'/'.str_replace(".md","",$link[2]).'/" class="cta">'.$buttonText.'</a></article>';
      }
    }
    $content = str_replace('</article><a','<a',$content); // Handles Image links
    if(defined("phpthumb")){
      $content = str_replace('img/',str_replace(".md","",$category."/".$link[2]).'/img/',$content); // Handles Image links
    }else{
      $content = str_replace('img/',str_replace(".md","",$link[2]).'/img/',$content); // Handles Image links
    }
    $content = str_replace('aud/',str_replace(".md","",$link[2]).'/aud/',$content); // Handles Audio links
    $content = str_replace('vid/',str_replace(".md","",$link[2]).'/vid/',$content); // Handles Video links
    $content = str_replace('../../'.$link[1].'/','', $content); // Converts relative links into absolute ones (to neaten up code in category view)
    $content = str_replace('"'.$link[2],'"'.url.'/'.$category.'/'.$link[2], $content); // Converts misc links into absolute links. This is for <img>, <video> and <object> elements, making video and graphics work as they are intended to. Should also work with <audio> elements too.
  }else{
    $content = str_replace('../..','', $content);
    $content = str_replace('"/','"'.url.'/', $content);
    if(defined("phpthumb")){
      $content = str_replace('img/',str_replace(".md","",$category."/".$link[2]).'/img/',$content); // Handles Image links
    }else{
      $content = str_replace('"img','"'.url.'/'.$category.'/'.$link[2].'/img',$content); // Handles Image links
    }
  }
  $content = str_replace(url.'//',url.'/pages/', $content);
  $content = str_replace("<p><video", "<video", $content);
  $content = str_replace("</video></p>", "</video>", $content);
  $content = str_replace("<p><audio", "<audio", $content);
  $content = str_replace("</audio></p>", "</audio>", $content);
  if(isset($contentMeta["Author"])){
    $author = $contentMeta["Author"];
  } else {
    $author = Author;
  }
  if(defined('dateFormat') && isset($contentMeta['Pubdate'])){
    $content = str_replace("<!--[TimeStamp]-->", '<p class="timestamp">'.(defined('dateFormatBefore') ? dateFormatBefore : '').'<time datetime="'.formatDate($contentMeta["Pubdate"],'c').'">'.formatDate($contentMeta["Pubdate"]).'</time>'.(defined('dateFormatAfter') ? str_replace("<!--[Author]-->",$author,dateFormatAfter) : '').'</p>', $content);
    if(!$splode){
      $content = str_replace("<time ", "<time pubdate ", $content);
    }
  }
  return $content;
  fclose($file);
}

function getComment($myText){
  $file=fopen($myText, 'r');
  $filecontent = fread($file, filesize($myText));
  $sect = explode('=-=-=',$filecontent); // Seperates post metadata from content.
  $contentMeta = parseMeta($sect[0]);
  $myHtml = Markdown($sect[1]);
  $contentMeta["post"] = str_replace("</cite></p>","</cite>",str_replace("<p><cite>","<cite>",str_replace(' markdown="1"','',str_replace('<p><img','<img',str_replace('/></p>','/>',$myHtml))))); // Reorganises some of the elements of the site's HTML, cleaning up where markdown does weird things to code
  $link = explode('/', $myText);
  return $contentMeta;
  fclose($file);
}

function formatDate($pubdate,$finalFormat=dateFormat){
  $format = 'd-m-Y H:i';
  $date = DateTime::createFromFormat($format, $pubdate);
  return date($finalFormat, $date->format('U'));
}

function parseMeta($meta){
  $postmeta = explode("\n",$meta); // Gets individual meta fields.
  foreach ($postmeta as $meta) {
    $topost = explode(': ',$meta);
    if(isset($topost[1])){
      $contentMeta[$topost[0]] = $topost[1];
    }
  }
  return $contentMeta;
}

/*******
Caching logic starts here - Checks to see if a cached file exists
*******/ 
$cachefile = 'cache/'.str_replace("/","-",substr($path, 1, -1)).'.html';
if($cachefile == "cache/.html"){
  $cachefile = "cache/home.html";
}
if(defined('noindex')){
  $cachetime = 60 * 60;  //  1 Hour Cache
}else{
  $cachetime = 24 * 60 * 60;  //  1 Day Cache
}
if(isset($bits[2]) && file_exists('categories/'.$bits[1].'/'.$bits[2]."/post.md")){
  $file = 'categories/'.$bits[1].'/'.$bits[2]."/post.md";
}elseif(file_exists("categories/pages/".$bits[1]."/post.md")){
  $file = "categories/pages/".$bits[1]."/post.md";
}elseif(is_dir("categories/".$bits[1])){
  $file = "categories/".$bits[1];
}
//echo $file;
// Serve from the cache if it is younger than $cachetime, and a newer version of the page doesn't exist
if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile) && filemtime($file) < filemtime($cachefile) && cache && !isset($_POST["important-input"])) {
    include($cachefile);
    echo "<!-- Cached copy, generated ".date('r', filemtime($cachefile))." by Wee_ CMS -->\n";
    exit;
}

/*******
CMS Logic starts here
*******/ 

if(isset($bits[2]) && $bits[2]!='' && $bits[2]!='page' && $bits[1] != "pages") { // If there's a week, checks to see if post exists
  $myText='categories/'.$bits[1].'/'.$bits[2]."/post.md";
  if(file_exists($myText)){
    $content = toDisplay($myText,$bits[1]);
  }else{
    define('404',true);
    $content = toDisplay("errors/404.md");
  }
} else { /* If no week is defined, checks to see if it's a post, a page, or an error */
  $myText = "categories/pages/".$bits[1]."/post.md";
  if (file_exists($myText)){
    $content = toDisplay($myText);
  }elseif(is_dir("categories/".$bits[1]) && $bits[1] != "pages"){
    define('title',ucwords(str_replace("-", " ", $bits[1])));
    $check=1;
    if(isset($bits[3])){
      $page = $bits[3];
    }else{
      $page=1;
    }
    if($bits[1] != home){
      define('noindex', true);
    }else{
      define("description", SiteDescription);
      define("keywords", SiteKeywords);
    }
    $start = (($page * PostsPerPage) - (PostsPerPage))+1;
    $end=$page * PostsPerPage;
    $category="categories/".$bits[1];
    
    /* Check if index.md exists, if it does use it to order posts */
    if(file_exists($category."/index.md") && file_get_contents($category."/index.md") != ""){
      $posts = preg_split( '/\r\n|\r|\n/', file_get_contents($category."/index.md"));
    }else{
      $posts=scandir($category, 1);
    }
    foreach($posts as $post){
      if($post != '.' && $post != '..' && $post != '.DS_Store' && file_exists("categories/".$bits[1].'/'.$post.'/post.md') && $post != 'index.md' && 
        $check >= $start && $check <= $end){
        $myText="categories/".$bits[1].'/'.$post.'/post.md';
        if(file_exists($myText)){
          $content .= toDisplay($myText,$bits[1],readmore);
        }
      }
      $check++;
    }
    $paging = getpaging($check, $page, PostsPerPage, $bits[1]);
    ($check==1 ? $content = toDisplay("errors/soon.md") : '');
  }else{
    $myText = "categories/journal/".$bits[1]."/post.md";
    if (file_exists($myText)){
      $content = toDisplay($myText);
    }else{
      define('404',true);
      $content = toDisplay("errors/404.md");
    }
  }
}

/*******
Page Output starts here
*******/
# Determine if caching should be used, rather than work it out each time
$cache = false;
if(!defined('404') && cache && !isset($_POST["important-input"])){
  $cache = true;
}

if(defined('404')){
  header('HTTP/1.0 404 Not Found'); // If page doesn't exist, set the right header
}

# If caching is enabled, and we aren't showing a 404 error or a unique page, pages are cached into the /cache/ folder
if($cache){
  ob_start(); // Start the output buffer
}
# If you are viewing a single post's page, and there is an index.php file in addition to the post.md file, the index.php file is loaded instead. This allows for the creation of single unique pages. An example of this is http://davidturner.name/examples/Responsive-Web-Design-960-Grid-Style/
if(isset($bits[2]) && $bits[2] != "" && $bits[2] != "page" && $bits[3] == "" && file_exists("categories/".$bits[1]."/".$bits[2]."/index.php")){
  include "categories/".$bits[1]."/".$bits[2]."/index.php";
  if(isset($meta["NoCache"])){
    exit;
  }
}elseif(isset($bits[1]) && $bits[1] != "" && file_exists("categories/pages/".$bits[1]."/index.php")){
  include "categories/pages/".$bits[1]."/index.php";
  if(isset($meta["NoCache"])){
    exit;
  }
}else{
  include('template/'.theme.'/header.php'); // Get's the relevant template header file
  
  # If in a single post, it is possible to define files to be included before and after the main content.
  if(isset($bits[2]) && $bits[2] != "" && $bits[2] != "page" && $bits[3] == "" && file_exists("categories/".$bits[1]."/".$bits[2]."/include-before.php")){
    include "categories/".$bits[1]."/".$bits[2]."/include-before.php";
  }
  if(file_exists("categories/pages/".$bits[1]."/include-before.php") && !isset($bits[2]) || file_exists("categories/pages/".$bits[1]."/include-before.php") && $bits[2] == ""){
    include "categories/pages/".$bits[1]."/include-before.php";
  }
  # Whilst it is possible to create a unique page, sometimes all we want is to run a bit of PHP inside the main theme. In this instance we can create a file called include.php, which will be used instead of the processed post.md. The post.md file is still processed to get relevant meta data which can then be used in the page as per normal.
  if(isset($bits[2]) && $bits[2] != "" && $bits[2] != "page" && $bits[3] == "" && file_exists("categories/".$bits[1]."/".$bits[2]."/include.php")){
    include "categories/".$bits[1]."/".$bits[2]."/include.php";
  }elseif(isset($contentMeta['post']) && $contentMeta['post']!='<'){
    echo $contentMeta['post'];
  }else{
    if(defined('phpthumb')){
      $phpthumb = explode("##FILE##", phpthumb);
      $pattern = '/<img src="(.*)" alt="(.*)" \/>/';
      $replacement = '<img alt="$2" src="'.$phpthumb[0].'$1'.$phpthumb[1].'" />';
      $patternTitle = '/<img src="(.*)" alt="(.*)" title="(.*)" \/>/';
      $replacementTitle = '<img alt="$2" title="$3" src="'.$phpthumb[0].'$1'.$phpthumb[1].'" />';
      
      echo str_replace('categories//','categories/pages/',preg_replace($patternTitle, $replacementTitle, preg_replace($pattern, $replacement, $content)));
    }else{
      echo $content;
    }
  }
  
  # If the thee has a comments.php in it the theme in use, then we actually make stuff happen for allowing comments, and the parsing of comments.
  if (!in_array($bits[1], explode(",",nocomments)) && isset($bits[2]) && $bits[2] != "" && $bits[2] != "page" && file_exists('template/'.theme.'/comments.php')) {
    include('template/'.theme.'/comments.php');
  }
  if(isset($bits[2]) && $bits[2] != "" && $bits[2] != "page" && $bits[3] == "" && file_exists("categories/".$bits[1]."/".$bits[2]."/include-after.php")){
    include "categories/".$bits[1]."/".$bits[2]."/include-after.php";
  }
  if(file_exists("categories/pages/".$bits[1]."/include-after.php") && !isset($bits[2]) || file_exists("categories/pages/".$bits[1]."/include-after.php") && $bits[2] == ""){
    include "categories/pages/".$bits[1]."/include-after.php";
  }
  include('template/'.theme.'/footer.php');
}
if($cache){
  $fp = fopen($cachefile, 'w');
  if(defined('compressCache') && compressCache == true ){
    $pageContents = preg_replace( "/(?:(?<=\>)|(?<=\/\>))(\s+)(?=\<\/?)/","", ob_get_contents() );
  } else {
    $pageContents = ob_get_contents();
  }
  fwrite($fp, $pageContents);
  fclose($fp);
  ob_end_flush(); // Send the output to the browser
}