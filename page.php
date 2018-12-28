<?php
/*
Template Name: Home Page
*/
	get_header();
	while ( have_posts() ) : the_post();
?>
	<div class="container-fluid secondary-page-wrapper">
        <div class="page-wrapper row">
         
            
            <div class="col-xs-12 col-sm-10 col-lg-8 col-sm-offset-1 col-lg-offset-2">
                <div id="secondary-page-title" class="row">

                  
                    <h1 class="col-xs-12"><?php the_title(); ?></h1>

                    
                </div>
                <div id="main-sidebar" class="pull-right col-xs-12 col-sm-6">    
                     <?php get_sidebar(); ?>
                </div>
                    <div class="secondary-page-content pull-left col-xs-12 col-sm-6">
                        <div class="row secondary-page-content-wrapper">
                        	<div class="row">
                        		
                        		<?php the_content(); ?> 
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <?php
                                    if (!in_array($_SERVER['REQUEST_URI'], ['/sg-subscription-success'])):
                                        dynamic_sidebar(Sidebar::SIDEBAR_ID_NEWSLETTER);
                                    endif;
                                ?>
                            </div>
                        </div>
                    </div>    
	        </div> 
	    </div> 
	</div><!--.secondary-page-wrapper--> 
<?php
	// End the loop.
	endwhile;
	get_footer(); 