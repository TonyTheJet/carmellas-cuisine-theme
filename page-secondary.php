<?php
/*
Template Name: Secondary Page
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
				<div class="row">
					<div>
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</div>
	</div><!--.secondary-page-wrapper-->
<?php
	// End the loop.
endwhile;
get_footer();