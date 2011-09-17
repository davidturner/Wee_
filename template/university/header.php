<!DOCTYPE html>
<html lang="en-GB" class="no-js">
<head>
  <meta charset="UTF-8" />
  <title><?php if(defined('noindex') && @$bits[2]=='' || @$bits[2] =='page'){ echo $bits[1]; }else{echo title;} ?><?php echo Divider.SiteName; ?></title>
  <link rel="stylesheet" media="all" href="<?php echo url; ?>/template/<?php echo theme; ?>/style.live.css" />
  <link rel="alternate" type="application/rss+xml" href="<?php echo url; ?>/feed/<?php if($cat){echo $bits[1].'/';} ?>" />
  <link rel="author" href="/humans.txt" />
  <?php if(!defined('noindex')){ ?><meta name="description" content="<?php echo description; ?>" />
  <meta name="keywords" content="<?php echo keywords; ?>" />
  <?php } else { ?>
  <meta name="robots" content="noindex,follow" />
  <?php } ?><!--  Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width; initial-scale=1.0" />
  <script src="//cdnjs.cloudflare.com/ajax/libs/modernizr/2.0.6/modernizr.min.js"></script>
</head>

<body>
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
  </script>  

  <div id="container">
    <header>
      <h1 id="logo"><a href="<?php echo url; ?>/">David Turner</a></h1>
      <nav>
        <ul id="modules">
          <li><a href="/plog/"<?=($bits[1]=="plog")?'class="active"':'';?>>Project Log</a></li>
          <li><a href="/511/"<?=($bits[1]=="511")?'class="active"':'';?>>DES511</a></li>
          <li><a href="/533/"<?=($bits[1]=="533")?'class="active"':'';?>>COM533</a></li>
          <li><a href="/previously-on-IMD/">Previously</a></li>
          <li><a href="/feed/<?php if(defined('noindex') || isset($bits[1]) && !in_array($bits[1], explode(',',nofeed))){echo $bits[1].'/';} ?>">RSS<?php if(defined('noindex') || isset($bits[1]) && !in_array($bits[1], explode(',',nofeed))){echo ' ['.$bits[1].']';} ?></a></li>
        </ul>
      </nav>
    </header>
    <section id="content">