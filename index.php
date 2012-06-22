<?php
ob_start('ob_gzhandler');
include 'assets/php/functions.php'; // php functions
include 'assets/php/config.php'; // configuration
include 'config.php';
include 'assets/php/Akismet.class.php';
include 'assets/php/markdown.php'; // markdown -> HTML
include 'assets/php/markdown_extended.php'; // markdown -> HTML
include 'assets/php/markdownify.php'; // HTML -> markdown
include 'assets/php/markdownify_extra.php'; // HTML -> markdown extra - uses markdownify

$site->cachefile = 'cache/'.str_replace('/','-',substr($site->query, 1, -1)).'.html';
if($site->cachefile == 'cache/.html'){
  $site->cachefile = 'cache/home.html';
}

$cacheChk = 0;

if(isset($site->slug[1]) && file_exists('categories/'.$site->slug[0].'/'.$site->slug[1]."/post.".$site->ext)){
  $file = 'categories/'.$site->slug[0].'/'.$site->slug[1].'/post.'.$site->ext;
}elseif(file_exists('categories/pages/'.$site->slug[0].'/post.'.$site->ext)){
  $file = 'categories/pages/'.$site->slug[0].'/post.'.$site->ext;
}elseif(is_dir('categories/'.$site->slug[0])){
  $file = 'categories/'.$site->slug[0];
  $cacheChk = 1;
}

if(isset($site->noindex)){
  $cacheChk = 1;
}

//if(isset($site->noindex) || isset($site->category) && $site->category || isset($site->tag) && $site->tag || $cacheChk){
if($cacheChk){
  $cachetime = 60 * 60;  //  1 Hour Cache
}
// Serve from the cache if it is younger than $cachetime, and a newer version of the page doesn't exist
if (
  !isset($cachetime)
  &&
  $site->cache->active
  &&
  file_exists($site->cachefile)
  &&
  (filemtime($file) - @filemtime($site->cachefile)) <= 0
  &&
  !isset($_POST['important-input'])
  ||
  isset($cachetime)
  &&
  (time() - @filemtime($site->cachefile)) <= $cachetime
  ) {
    include($site->cachefile);
    echo '<!-- Cached copy, generated '.date('r', filemtime($site->cachefile)).' by Wee_ CMS -->';
    exit;
}

if($site->slug[0] == '' && !$site->redir){
  $site->slug[0] = $site->home;
} elseif($site->slug[0] == '' && $site->redir) {
  header('Location: /'.$site->home.'/');
  die;
}

$page = parseURL($site->slug,$site);

if($site->cache->active && !isset($site->error) && !isset($_POST['important-input'])){
  ob_start(); // Start the output buffer
}

if(isset($page->noindex)){
  header('X-Robots-Tag: noindex', true);
  header('HTTP/1.0 410 Gone');
}

if($site->error){
  if(!is_dir('cache/404')){
    mkdir("cache/404", 0777);
  }
  session_start();
  if(!isset($_SESSION['org_referer'])){
      $_SESSION['org_referer'] = $_SERVER['HTTP_REFERER'];
  }
  $ourFileName = 'cache/404/'.str_replace('/','-',substr($site->query, 1, -1)).'.txt';
  $ourFileHandle = fopen($ourFileName, 'w');// or die("can't open file");
  fwrite($ourFileHandle, 'Referring URL: '.$_SESSION['org_referer']);
  fclose($ourFileHandle);
}

include 'themes/'.$site->theme.'/header.php';
echo $page->content;

if($site->comments != 'none' && !isset($site->error) && !isset($page->noindex) && !isset($page->nocomments) && !$site->page && !in_array($site->slug[0], $site->nocomments)){
  include 'assets/php/comments.php';
}
if(isset($page->contactme) && $page->contactme){
  include 'assets/php/contact.php';
}

include 'themes/'.$site->theme.'/footer.php';

if($site->cache->active && !isset($site->error) && !isset($_POST['important-input'])){
  $fp = fopen($site->cachefile, 'w');
  if($site->cache->compress && $site->cache->compress){
    $pageContents = preg_replace( '/(?:(?<=\>)|(?<=\/\>))(\s+)(?=\<\/?)/','', ob_get_contents() );
  } else {
    $pageContents = ob_get_contents();
  }
  fwrite($fp, $pageContents);
  fclose($fp);
  ob_end_flush(); // Send the output to the browser
}

# Purge Cached copies of comment submissions
if($site->cache->active && isset($site->purge) && file_exists($site->purge)){
  unlink($site->purge);
}