  </div> <!--! end of #container -->

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>

	
  <!-- Change UA-XXXXX-X to be your site's ID -->
  <script>
    window._gaq = [['_setAccount','<?php echo $site->analytics;?>'],['_trackPageview'],['_trackPageLoadTime']];
    Modernizr.load(
      {
        load: '/themes/<?php echo $site->theme; ?>/js/site.js'
      },
      {
        load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
      }
    );
  </script>
  
</body>
</html>
