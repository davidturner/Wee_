<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <title><?=title.Divider.SiteName; ?></title>
  <?php if(!defined('noindex')){ ?><meta name="description" content="<?php echo description; ?>" />
  <meta name="keywords" content="<?php echo keywords; ?>" />
  <?php } else { ?>
  <meta name="robots" content="noindex,follow" />
  <?php } ?>

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <?php if(file_exists($_SERVER['DOCUMENT_ROOT'].str_replace($bits[1],"categories/".$bits[1],$path)."single.css")){ ?>
  <link rel="stylesheet" media="screen" href="css/single.css" />
  <?php } else { ?>
  <link rel="stylesheet" href="/template/<?php echo theme; ?>/css/style.css">
  <?php } ?>
  
  <?php if(file_exists($_SERVER['DOCUMENT_ROOT'].str_replace($bits[1],"categories/".$bits[1],$path)."extra.css")){ ?>
  <link rel="stylesheet" media="screen" href="extra.css" />
  <?php } ?>
  <link rel="author" href="/humans.txt" />
  <script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.0.6/modernizr.min.js"></script>
</head>

<body>
  <div id="container">
    <header>
      <nav>
        <ul>
          <li><a href="/"<?=($bits[1]=="home")?'class="active"':'';?>>Home</a></li>
          <li><a href="/journal/"<?=($bits[1]=="journal")?'class="active"':'';?>>Journal</a></li>
          <li><a href="/feed/">RSS Feed</a></li>
        </ul>
      </nav>
    </header>
    <div id="main" role="main">
      