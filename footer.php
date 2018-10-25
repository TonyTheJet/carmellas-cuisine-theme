		<div id="footer-wrapper">
            <div class="footer-wrapper-row">
                <div id="footer-wrapper" class="col-xs-12 col-sm-12 col-md-12 col-lg-12 col-sm-offset-0 col-md-offset-0 col-lg-offset-0" style="text-align: center; padding:50px;">
                    Carmella's Cuisine is a licensed and insured catering company currently serving Salt Lake and Utah Counties 
                    <p>801.550.1679 | carmellas.cuisine@gmail.com | <a href="<?php bloginfo('url'); ?>/key-vendors-partners">Key Vendors & Partners</a> </p>
                    <p>Copyright <?php echo date('Y'); ?> CarmellasCuisine.com. All rights reserved.</p>
             
                </div>
            </div>
        </div>
        <?php
        	if (is_front_page()):
        		echo cc_render_home_popup();
        	endif;
        	wp_footer();
        ?>
        <script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-34141259-1', 'auto');
		  ga('send', 'pageview');

		</script>
	</body>
</html>