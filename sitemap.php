<?php 
include_once "config.php";
if(cache){
  $cachefile = 'cache/sitemap.xml';
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
header("Content-Type: application/xml; charset=UTF-8");
?>
<?='<?xml version="1.0" encoding="UTF-8"?>'."\n";?>
<?='<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";?>
<?php
// Get all categories, start with pages
$noList = array('.','..','.DS_Store','pages');
//print_r($categories);

function printDir($dir,$folder = false){
  global $noList;
  if(file_exists("categories/".$dir.'/index.md')){
    $pages = preg_split( '/\r\n|\r|\n/', file_get_contents("categories/".$dir.'/index.md'));
  }else{
    $pages = scandir("categories/".$dir);
  }
  foreach ($pages as $page) {
    if(!in_array($page, $noList)){
      $myText = "categories/".$dir."/".$page."/post.md";
      if($folder){
        $loc = url.$dir."/".$page."/";
        $lastmod = date("Y-m-d",filemtime("categories/".$dir."/".$page."/"));
        $changefreq = "always";
        $priority = "0.8";
      }else{
        $loc = url.'/'.$dir."/".$page."/";
        $lastmod = date("Y-m-d",filemtime($myText));
        $changefreq = "monthly";
        $priority = "1";
      }
      $loc = str_replace("pages/", "", $loc);
      echo "  ".'<url>'."\n";
      echo "    ".'<loc>'.$loc/*URL*/.'</loc>'."\n";
      echo "    ".'<lastmod>'.$lastmod/*last modified date*/.'</lastmod>'."\n";
      echo "    ".'<changefreq>'.$changefreq/*Change Frequency (always|hourly|daily|weekly|monthly|yearly|never)*/.'</changefreq>'."\n";
      echo "    ".'<priority>'.$priority/*Priority (0-1)*/.'</priority>'."\n";
      echo "  ".'</url>'."\n";  
    }
  }
}

printDir('pages');
printDir('',true);

$categories=scandir("categories");
foreach ($categories as $category) {
  if(!in_array($category, $noList)){
    printDir($category);
  }
}
?>

<?='</urlset>'."\n";?>

<?php
if(cache){
  $fp = fopen($cachefile, 'w');
  fwrite($fp, ob_get_contents());
  fclose($fp);
  ob_end_flush(); // Send the output to the browser
}