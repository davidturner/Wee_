<?php 
$dir = 'cache/';
foreach(glob($dir.'*.*') as $v){
    unlink($v);
}
?>