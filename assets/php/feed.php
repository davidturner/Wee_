<?php header("Content-Type: application/xml; charset=UTF-8");?>
<?php

$site->cachefile = 'cache/'.str_replace("/","-",substr($site->query, 1, -1)).'.xml';
$cachetime = 60 * 60 * 4;  // 4 Hour Cache
// Serve from the cache if it is younger than $cachetime, and a newer version of the page doesn't exist


if ($site->cache->active && file_exists($site->cachefile) && time() - $cachetime < filemtime($site->cachefile)) {
    echo file_get_contents($site->cachefile);
    echo "<!-- Cached copy, generated ".date('r', filemtime($site->cachefile))." by Wee_ CMS -->\n";
    die;
}

if($site->cache->active && !isset($site->error)){
  ob_start(); // Start the output buffer
}

?>
<?php
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	>
<channel>
	<title><?php echo $site->title; ?></title>
	<atom:link href="<?php echo $site->url; ?>/feed/" rel="self" type="application/rss+xml" />
	<link><?php echo $site->url; ?>/</link>
	<description><?php echo $site->desc; ?></description>
	<language>en</language>
	<sy:updatePeriod>daily</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>



<?php

function generateXML($folder,$site,$xml=array()){
  if(file_exists('categories/'.$folder.'/index.'.$site->ext)){
    $posts = preg_split( '/\r\n|\r|\n/', file_get_contents('categories/'.$folder.'/index.'.$site->ext));
  } else {
    $posts = scandir('categories/'.$folder);
  }
  //print_r($posts);
  foreach ($posts as $post) {
    $file = 'categories/'.$folder.'/'.$post.'/post.md';
    if(file_exists($file)){
      $postData = parseFile($file,$site,0,0);
      if(!isset($postData->link)){
        $postData->link = $site->url.'/'.$folder.'/'.$post.'/';
      }
      //print_r($postData);
      $postData->content = str_replace('<article class="feed">', '',
                           str_replace('</article>','',
                                                str_replace('feed//',$folder.'/'.$post.'/',
                           str_replace('href="/', 'href="'.$site->url.'/',
                           str_replace('href="#', 'href="'.$site->url.'/'.$folder.'/'.$post.'/'.'#',
                           $postData->content)))));
      $postData->content=preg_replace('#(href|src)="([^:"]*)(?:")#','$1="'.$site->url.'/'.$folder.'/'.$post.'/'.'$2"',$postData->content);
      $postData->content = str_replace('feed/categories/', '', $postData->content);
      $xml[strtotime($postData->pubdate)] = '<item>'."\n\t".'<title>'.str_replace("& ","&amp; ", $postData->title).'</title>'."\n\t".'<link>'.$postData->link.'</link>'."\n\t".'<pubDate>'.date('r',strtotime($postData->pubdate)).'</pubDate>'."\n\t".'<dc:creator>'.$site->author->name.'</dc:creator>'."\n\t"."\n\t".'<description><![CDATA['.$postData->content.']]></description><content:encoded><![CDATA['.$postData->content.']]></content:encoded><guid>'.$postData->link.'</guid>'."\n".'</item>';
    }
  }
  return $xml;
}

$xml = array();
if(isset($site->slug[1])){
  $folder = $site->slug[1];
  $folders = explode('+',$folder);
  if(!isset($folders[1])){
    $posts = generateXML($folder,$site);
    foreach($posts as $key => $value){
      $xml[$key] = str_replace('feed',$folder,$value);
    }
  } else {
    foreach($folders as $folder){
      $posts = generateXML($folder,$site);
      foreach($posts as $key => $value){
        $xml[$key] = str_replace('feed',$folder,$value);
      }
    }
  }
} else {
  $folders = scandir('categories');
  foreach ($folders as $folder) {
    if(!in_array($folder, $site->nofeed)){
      $posts = generateXML($folder,$site);
      foreach($posts as $key => $value){
        $xml[$key] = $value;
      }
    }
  }
}


krsort($xml);
$count = 1;
foreach($xml as $xml){
  if($count <= $site->posts->feed){
    echo $xml;
    $count++;
  }
}
?>
</channel>
</rss>
<?php

if($site->cache->active && !isset($site->error)){
  $fp = fopen($site->cachefile, 'w');
  if($site->cache->compress && $site->cache->compress){
    $pageContents = preg_replace( "/(?:(?<=\>)|(?<=\/\>))(\s+)(?=\<\/?)/","", ob_get_contents() );
  } else {
    $pageContents = ob_get_contents();
  }
  fwrite($fp, $pageContents);
  fclose($fp);
  ob_end_flush(); // Send the output to the browser
}

die;