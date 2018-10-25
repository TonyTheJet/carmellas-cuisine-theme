<?php
/*
Template Name: Home Page
*/
	get_header();
	while ( have_posts() ) : the_post();
?>
 		<div class="container-fluid">      
            <div class="page-wrapper row">
               
               
                    <div id="body-wrapper-stuff" class="col-xs-12 col-sm-10 col-md-8 col-lg-6 col-sm-offset-1 col-md-offset-2 col-lg-offset-3">
                        Carmella's Cuisine works to provide fresh, creative, custom menus for every event we have the pleasure of catering.   
                        Our food comes from the heart.  Carmella's passionate, creative team takes care of everything to elevate your special 
                        occasion in both presentation and flavor.  Please <a href="http://www.carmellascuisine.com/sample-page/contact-us">contact us</a> to learn more about what Carmella's can do for you.


                        <div id="top-pictures" class="row">
                            <div class="col-xs-6 col-sm-4 col-lg-3">
                                <img src="<?php bloginfo('stylesheet_directory'); ?>/images/choc-dip-strawberries800.jpg" class="img-responsive outer-glow" alt="macaroons">
                            </div>
                            <div class="col-xs-6 col-sm-4 col-lg-3">
                                <img src="<?php bloginfo('stylesheet_directory'); ?>/images/mangosalad800.jpg" class="img-responsive outer-glow" alt="mango salad">
                            </div>
                            <div class="hidden-xs col-sm-4 col-lg-3">
                                <img src="<?php bloginfo('stylesheet_directory'); ?>/images/easter-pies800.jpg" class="img-responsive outer-glow" alt="lemon cookies">
                            </div>
                            <div class="hidden-xs hidden-sm hidden-md col-lg-3">    
                                <img src="<?php bloginfo('stylesheet_directory'); ?>/images/antipesto800.jpg" class="img-responsive outer-glow" alt="anti pesto">   
                            </div>

                        </div>    

                        <div class="home-page-content row">
                            <?php the_content(); ?>


                            <div class="subpages-wrapper col-xs-12">
                                    <?php 
                                            $subpages = cc_subpages($post->ID); 
                                            if (is_array($subpages) && !empty($subpages)):
                                                    foreach ($subpages as $pg):
                                    ?>
                                                            <div class="subpage-wrapper row">
                                                                    <h3 class="col-xs-12"><?php echo $pg->post_title; ?></h3>
                                                                    <div class="subpage-content col-xs-12">
                                                                            <?php echo apply_filters('the_content', $pg->post_content); ?>
                                                                	</div>
                                                        	</div>
                                <?php 
                                                endforeach;
                                        endif;
                                ?>
                              </div>  
                         </div>
                    </div>    
                </div>
        </div>   
<?php
	// End the loop.
	endwhile;
	get_footer(); 