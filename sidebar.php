<?php
	global $post;
?>
<div id="main-sidebar">
    <?php 
    	if ($post->ID == Sidebar::PAGE_ID_SAMPLE_MENUS || wp_get_post_parent_id($post->ID) == Sidebar::PAGE_ID_SAMPLE_MENUS): 
    		dynamic_sidebar(Sidebar::SIDEBAR_ID_SAMPLE_MENUS); 
    	endif;
    	if ($post->ID == Sidebar::PAGE_ID_WEDDINGS_EVENTS || wp_get_post_parent_id($post->ID) == Sidebar::PAGE_ID_WEDDINGS_EVENTS):
    		dynamic_sidebar(Sidebar::SIDEBAR_ID_SAMPLE_MENUS);
    	endif;
        
    ?>
</div>

