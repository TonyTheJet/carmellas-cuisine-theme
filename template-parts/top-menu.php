<?php
	if (isset($_GET['test'])):
?>
		<div id="top-menu" class="col-xs-12 col-sm-10 col-md-8 col-lg-6 col-sm-offset-1 col-md-offset-2 col-lg-offset-3">
			<span id="top-menu-clickable">
				<span id="top-menu-open-clickables"><i class="fa fa-bars"></i> MENU</span>
				<span id="top-menu-close-clickables" class="hidden"><i class="fa fa-window-close-o"></i> CLOSE</span>
			</span>
			<ul class="col-xs-12">
				<li><a href="<?php bloginfo('url'); ?>/sample-menus">Sample Menus</a></li>
				<li><a href="<?php bloginfo('url'); ?>/contact-us">Contact Us</a></li>
				<li><a href="<?php bloginfo('url'); ?>/key-vendors-partners">Key Vendors & Partners</a></li>
			</ul>
		</div>
<?php
	endif;