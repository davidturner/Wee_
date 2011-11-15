<?php
include_once "config.php";
if(cache){
  $cachefile = 'cache/feed.xml';
  $cachetime = 60 * 60;  //  1 Hour Cache
  // Serve from the cache if it is younger than $cachetime
  if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
      //include($cachefile);
      header("Content-Type: application/xml; charset=UTF-8");
      $fd = fopen($cachefile,"r");
      $info = fread($fd, filesize($cachefile));
      echo $info;
      fclose($fd);
      echo "\n<!-- Cached copy, generated ".date('r', filemtime($cachefile))." -->\n";
      exit;
  }
  ob_start(); // Start the output buffer
}
?>
<?php header("Content-Type: application/xml; charset=UTF-8");?>
<?php
$path = str_replace(array("/feed/","/feed"), '', $_SERVER['REQUEST_URI']);
$bits = explode("/", $path); // DANGER! EXPLOSIVES!
include_once "resources/markdown.php"; // Markdown converts markdown format to the HTMLs
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
	<title><?php echo SiteName; ?></title>
	<atom:link href="<?php echo url; ?>/feed/" rel="self" type="application/rss+xml" />
	<link><?php echo url; ?>/</link>    
	<description><?php echo Desc; ?></description>
	<language>en</language>
	<sy:updatePeriod>daily</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>



<?php
$xml = array();                                                                                                                           
function toDisplay($myText){   
  global $xml;   
  $file=fopen($myText, 'r');
  $filecontent = fread($file, filesize($myText)); 
  $sect = explode('=-=-=',$filecontent);
  $postmeta = explode("\n",$sect[0]);
  foreach ($postmeta as $meta) {
      $topost = explode(': ',$meta);  
      //echo $topost[0];
      if(isset($topost[1])){
      $content[$topost[0]] = $topost[1]; 
    }else{
      $content[$topost[0]] = '';
    }
  }
  $myHtml = Markdown($sect[1]);
  $stuff = explode('</h1>',$myHtml);
  $title = $content["Title"];
  if(isset($content["Link"]) && $content["Link"] != ""){
    $link = $content["Link"];
  } else {
    $link = url.'/'.str_replace("categories/","",str_replace("post.".fileExt,"", $myText));
  }
  
  if (defined('phpthumb')) {
  	$str = str_replace("##FILE##","",phpthumb);
  	$myHtml = str_replace('"img/','"'.url.$str.str_replace("categories/", "", str_replace("post.".fileExt,"", $myText)).'img/',$myHtml);
  } else {
  	$myHtml = str_replace('"img/','"'.$link.'img/',$myHtml);
  }
  $myHtml = str_replace('"vid/','"'.$link.'vid/',$myHtml);
  $myHtml = str_replace('"aud/','"'.$link.'aud/',$myHtml);
  //$myHTML = str_replace('img/',str_replace(".".fileExt,"",$link[2]).'/img/',$myHTML); // Handles Image links
  //$myHTML = str_replace('vid/',str_replace(".".fileExt,"",$link[2]).'/vid/',$myHTML); // Handles Audio links
  $xmlPost = str_replace(array("<!--[TimeStamp]-->",
  "<!--[More]-->",' markdown="1"','../..'), "", 
  str_replace('"/','"'.url.'/',
  //str_replace('../..','', 
  str_replace("</cite></p>","</cite>",
  str_replace("<p><cite>","<cite>",
  //str_replace(' markdown="1"','',
  str_replace('<p><img','<img',
  str_replace('href="#', 'href="'.$link.'#',
  str_replace('/></p>','/>',$myHtml)))))));
  $xmlPost = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $xmlPost);
  $xml["".strtotime($content["Pubdate"])] = '<item>'."\n\t".'<title>'.str_replace("& ","&amp; ", $title).'</title>'."\n\t".'<link>'.$link.'</link>'."\n\t".'<pubDate>'.date('r',strtotime($content["Pubdate"])).'</pubDate>'."\n\t".'<dc:creator>'.AUTHOR.'</dc:creator>'."\n\t"."\n\t"./*'<description>'.str_replace("<", "&lt;", str_replace(">", "&gt;", $xmlPost)).'</description>'.*/'<description><![CDATA['.
   $xmlPost.
   ']]></description><content:encoded><![CDATA['.
    $xmlPost.
    ']]></content:encoded><guid>'.$link.'</guid>'."\n".'</item>';  
  fclose($file);
}
function theLoop($toLoop){ if(is_dir($toLoop)) : $category=$toLoop; if(file_exists($category."/index.".fileExt) && file_get_contents($category."/index.".fileExt) != ""){
  $posts = preg_split( '/\r\n|\r|\n/', file_get_contents($category."/index.".fileExt));
  //print_r($posts);
}else{
  $posts=scandir($category, 1);
}; foreach($posts as $post) : if($post!='.' && $post!='..') : $myText=$toLoop.'/'.$post.'/post.'.fileExt; toDisplay($myText); endif; endforeach; endif; }   

if($bits[0]!=''){
  theLoop("categories/".$bits[0]);
}else{
  if ($handle = opendir('categories')) :
  	while (false !== ($theFile = readdir($handle))) : if (!in_array($theFile,explode(',',nofeed))) : $myText = 'categories/'.$theFile; theloop($myText); endif; endwhile; closedir($handle); endif;
}       
//print_r($xml);
krsort($xml);
//print_r($xml);
$count = 0;
foreach($xml as $xml){
  $count++;
  if($count <= FeedTotal){
    echo $xml;
  }
}
?>
</channel>
</rss>
<?php
if(cache){
  $fp = fopen($cachefile, 'w');
  fwrite($fp, ob_get_contents());
  fclose($fp);
  ob_end_flush(); // Send the output to the browser
}