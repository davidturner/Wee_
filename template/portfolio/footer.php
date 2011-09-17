<?php echo @$paging; ?>
    </section>
       
    <footer>
      <h3>Elsewhere online...</h3>
      <p>If you'd like to follow me elsewhere online, check out:</p>
      <a href="/feed/">RSS</a> &#8226; <a href="http://twitter.com/HerrWulf">Twitter</a> &#8226; <a href="http://forr.st/-DavidTurner">Forrst</a> &#8226; <a href="http://gplus.to/DavidTurner">Google+</a>
    </footer>
  </div><!-- #container -->
  
  
  <script>
    window._gaq = [['_setAccount','<?=ga;?>'],['_trackPageview'],['_trackPageLoadTime']];
    Modernizr.load([
      {
        load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
      }
    ]);
  </script>
</body>
</html>