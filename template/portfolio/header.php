<?php
if(url == 'http://uni.david.turner.name'){
  Header( "HTTP/1.1 301 Moved Permanently" );
  Header( "Location: ".str_replace('david.turner','davidturner',url).$path );
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html lang="en-GB" class="no-js ie6"> <![endif]--> 
<!--[if IE 7]>    <html lang="en-GB" class="no-js ie7"> <![endif]--> 
<!--[if IE 8]>    <html lang="en-GB" class="no-js ie8"> <![endif]--> 
<!--[if gt IE 8]><!--> <html lang="en-GB" class="no-js"> <!--<![endif]--> 
<head>
  <meta charset="UTF-8" />
  <title><?=title.Divider.SiteName; ?></title>
  <?php if(!defined('noindex')){ ?><meta name="description" content="<?php echo description; ?>" />
  <meta name="keywords" content="<?php echo keywords; ?>" />
  <?php } else { ?>
  <meta name="robots" content="noindex,follow" />
  <?php } ?><!--  Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width; initial-scale=1.0" />
  <link rel="profile" href="http://gmpg.org/xfn/11" />
  <link rel="stylesheet" media="screen" href="http://fonts.googleapis.com/css?family=Crimson+Text:400,400italic,700,700italic" />
  <?php
  if(file_exists($_SERVER['DOCUMENT_ROOT'].str_replace($bits[1],"categories/".$bits[1],$path)."single.css")){
  ?>
  <link rel="stylesheet" media="screen" href="css/single.css" />
  <?php
  } else {
  ?>  
  <!--[if lte IE 8]><link rel="stylesheet" media="all" href="/template/<?=theme;?>/css/site.classic.css" /><![endif]--> 
  <!--[if lt IE 8]><style>#content{width: 70%!important;}</style><![endif]--> 
  <!--[if gt IE 8]><!--><link rel="stylesheet" media="all" href="/template/<?php echo theme; ?>/css/site.current.css" /><!--<![endif]--> 
  <?php
  }
  ?>
  
  <?php
  if(file_exists($_SERVER['DOCUMENT_ROOT'].str_replace($bits[1],"categories/".$bits[1],$path)."extra.css")){
  ?>
  <link rel="stylesheet" media="screen" href="extra.css" />
  <?php
  }
  ?>
  <link rel="alternate" type="application/rss+xml" href="/feed/" />
  <link rel="canonical" href="<?=url.'/'.($bits[1] != 'home' ? $bits[1]."/" : "").(isset($bits[2]) && $bits[2] != '' ? $bits[2]."/" : "").(isset($bits[3]) && $bits[3] != '' && $bits[2] == "page" ? $bits[3]."/" : "");?>"/>
  <link rel="author" href="/humans.txt" />
  <script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.0.6/modernizr.min.js"></script>
</head>

<body>
  <?php /*
  <script>
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-16282973-1']);
    _gaq.push(['_setDomainName', 'uni.davidturner.name']);
    _gaq.push(['_trackPageview']);
    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
    })();
  </script>*/
  ?>

  <div id="container">
    <header>
      <h1 id="logo"><a href="/">David Turner</a></h1>
      <nav>
        <ul id="modules">
          <li><a href="/"<?=($bits[1]=="home")?'class="active"':'';?>>Home</a></li>
          <li><a href="/journal/"<?=($bits[1]=="journal")?'class="active"':'';?>>Journal</a></li>
          <li><a href="/examples/"<?=($bits[1]=="examples")?'class="active"':'';?>>Examples</a></li>
          <li><a href="/colophon/"<?=($bits[1]=="colophon")?'class="active"':'';?>>Colophon</a></li>
        </ul>
      </nav>
    </header>
    <section id="content">