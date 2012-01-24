<?php
header("Content-Type: application/xml; charset=UTF-8");

$site->cachefile = 'cache/sitemap.xml';
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
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n";?>
<?php echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";?>
<?php

function printDir($dir,$site,$folder = false){
  if(file_exists("categories/".$dir.'/index.'.$site->ext)){
    $pages = preg_split( '/\r\n|\r|\n/', file_get_contents("categories/".$dir.'/index.'.$site->ext));
  }else{
    $pages = scandir("categories/".$dir);
  }
  foreach ($pages as $page) {
    if(!in_array($page, $site->nofeed)){
      $myText = "categories/".$dir."/".$page."/post.".$site->ext;
      if($folder){
        $loc = $site->url.$dir."/".$page."/";
        $lastmod = date("Y-m-d",filemtime("categories/".$dir."/".$page."/"));
        $changefreq = "always";
        $priority = "0.8";
      }elseif(file_exists($myText)){
        $loc = $site->url.'/'.$dir."/".$page."/";
        $lastmod = date("Y-m-d",filemtime($myText));
        $changefreq = "monthly";
        $priority = "1";
      }
      $loc = str_replace("pages/", "", $loc);
      if($page == $site->home){
        $loc = str_replace($site->home.'/', '', $loc);
      }
      echo "  ".'<url>'."\n";
      echo "    ".'<loc>'.$loc/*URL*/.'</loc>'."\n";
      echo "    ".'<lastmod>'.$lastmod/*last modified date*/.'</lastmod>'."\n";
      echo "    ".'<changefreq>'.$changefreq/*Change Frequency (always|hourly|daily|weekly|monthly|yearly|never)*/.'</changefreq>'."\n";
      echo "    ".'<priority>'.$priority/*Priority (0-1)*/.'</priority>'."\n";
      echo "  ".'</url>'."\n";  
    }
  }
}

printDir('pages',$site);
printDir('',$site,true);

$categories=scandir("categories");
foreach ($categories as $category) {
  if(!in_array($category, $site->nofeed)){
    printDir($category,$site);
  }
}
?>
<?php echo '</urlset>'."\n";


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