<!DOCTYPE html>
<html>
	<head>
		<title><?php wp_title(); ?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta http-equiv="Lang" content="en">
		<meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/css/bootstrap/bootstrap.min.css" />
        <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>
        <link rel="apple-touch-icon" sizes="57x57" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon/favicon-16x16.png">
		<link rel="manifest" href="<?php bloginfo('stylesheet_directory'); ?>/manifest.json">
		<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_directory'); ?>/style.css?v=1.0.2">
		<script type="text/javascript" src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
		<script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/bootstrap/bootstrap.min.js"></script>
        <link rel="stylesheet" href="<?php bloginfo ('stylesheet_directory'); ?>/css/font-awesome.min.css">

        <?php wp_head(); ?> 
        <?php if (!is_user_logged_in()): ?>
			<style>
				#wpadminbar{ display: none; }
				html {
				    margin-top: 0 !important;
				}
			</style>
		<?php endif; ?>   

	</head> 
	<body class="<?php if (is_user_logged_in()): ?>logged-in<?php endif; ?>">
	<?php if (1 == 2): ?>
		<div id="gloria-food-wrapper">
            <!-- If you would like to customize the button, remove or change the "class" attribute inside the <span> tag -->
            <span class="glf-button" data-glf-cuid="4e635d53-08d2-48a3-85cf-1e9528bc1aa2" data-glf-ruid="b3e73b93-105a-4ea2-a2bf-ee253b31c093" > Order Meal Pickup </span>
            <script src="https://www.fbgcdn.com/widget/js/ewm2.js" defer async ></script>
		</div>
	<?php endif; ?>
        <div id="order-pick-up-link-wrapper">
            <a href="<?php bloginfo('url'); ?>/order-meal-pick-up" class="btn btn-primary">Order Meal Pick-Up</a>
        </div>
        <div id="header-menu">
            <div class="header-menu-item">
                <i class="fa fa-phone"></i> &nbsp; 801-550-1670
            </div>
            <div class="hidden-xs header-menu-item">
                <i class="fa fa-envelope"></i> &nbsp; <a href="mailto:carmellascuisine@gmail.com">carmellascuisine@gmail.com </a>
            </div>
            <div class="header-menu-item">
                <i class="fa fa-list-alt"></i> &nbsp; <a href="http://www.carmellascuisine.com/contact-us">contact </a>
            </div>
        </div>
        <div id="header" class="container-fluid">
            <?php if (is_front_page()): ?>
	            <div id="masthead" class="col-xs-12">
	                <img src="<?php bloginfo('stylesheet_directory'); ?>/images/header_pic_home.jpg" class="img-responsive hidden-xs hidden-sm hidden-md" alt="Welcome to Carmella's Cuisine">
	                <img src="<?php bloginfo('stylesheet_directory'); ?>/images/header_pic_home_mobile.jpg" class="img-responsive hidden-sm hidden-md hidden-lg" alt="Welcome to Carmella's Cuisine">
	                <img src="<?php bloginfo('stylesheet_directory'); ?>/images/header_pic_home_md.jpg" class="img-responsive hidden-xs hidden-sm hidden-lg" alt="Welcome to Carmella's Cuisine">
	                <img src="<?php bloginfo('stylesheet_directory'); ?>/images/header_pic_home_sm.jpg" class="img-responsive hidden-xs hidden-md hidden-lg" alt="Welcome to Carmella's Cuisine">

	            </div>
            <?php endif; ?>
                <div id="secondary-masthead" class="col-sm-10 col-md-8 col-lg-6 col-sm-offset-1 col-md-offset-2 col-lg-offset-3">
                	<?php get_template_part('content-parts/secondary-menu'); ?>
                </div>
            <?php if (!is_front_page()): ?>
                <div class="col-xs-12 text-right hidden-sm hidden-md hidden-lg">
                	<a href="<?php bloginfo('url'); ?>">Back to Home</a>
                </div>
            <?php endif; ?>
        </div>