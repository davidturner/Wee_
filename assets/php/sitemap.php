<?php
ob_start(); // Start the output buffer
header("Content-Type: application/xml; charset=UTF-8");
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
<?php echo '</urlset>'."\n";?>
<?php die;