<?php header("Content-Type: application/xml; charset=UTF-8");?>
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
$xml = array();                                                                                                                           
$folders = scandir('categories');
foreach ($folders as $folder) {
  if(!in_array($folder, $site->nofeed)){
    //echo $folder;
    if(file_exists('categories/'.$folder.'/index.'.$site->ext)){
      $posts = preg_split( '/\r\n|\r|\n/', file_get_contents('categories/'.$folder.'/index.'.$site->ext));
    } else {
      $posts = scandir('categories/'.$folder);
    }
    foreach ($posts as $post) {
      $file = 'categories/'.$folder.'/'.$post.'/post.md';
      if(file_exists($file)){
        $postData = parseFile($file,$site,0,0);
        if(!isset($postData->link)){
          $postData->link = $site->url.'/'.$folder.'/'.$post.'/';
        }
        $postData->content = str_replace('<article class="feed">', '',
                             str_replace('</article>','',
                             str_replace('href="/', 'href="'.$site->url.'/', 
                             str_replace('href="#', 'href="'.$site->url.'/'.$folder.'/'.$post.'/'.'#', 
                             $postData->content))));
        $postData->content=preg_replace('#(href|src)="([^:"]*)(?:")#','$1="'.$site->url.'/'.$folder.'/'.$post.'/'.'$2"',$postData->content);
        $xml[strtotime($postData->pubdate)] = '<item>'."\n\t".'<title>'.str_replace("& ","&amp; ", $postData->title).'</title>'."\n\t".'<link>'.$postData->link.'</link>'."\n\t".'<pubDate>'.date('r',strtotime($postData->pubdate)).'</pubDate>'."\n\t".'<dc:creator>'.$site->author->name.'</dc:creator>'."\n\t"."\n\t".'<description><![CDATA['.$postData->content.']]></description><content:encoded><![CDATA['.$postData->content.']]></content:encoded><guid>'.$postData->link.'</guid>'."\n".'</item>';
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
<?php die;