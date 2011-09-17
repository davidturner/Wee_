<?php echo @$paging; ?>
</div>
    <footer>

    </footer>
  </div> <!--! end of #container -->


  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="/template/<?php echo theme; ?>/js/jquery.1.6.2.min.js"><\/script>')</script>


  <!-- scripts concatenated and minified via ant build script-->
  <script defer src="/template/<?php echo theme; ?>/js/shiny.js"></script>
  <!-- end scripts-->

	
  <?php
  // Set your Google Analytics code in config.php, in the root folder of the site.
  ?>
  <script>
    window._gaq = [['_setAccount','<?=ga;?>'],['_trackPageview'],['_trackPageLoadTime']];
    Modernizr.load({
      load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
    });
  </script>
  
</body>
</html>
