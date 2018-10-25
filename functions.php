<?php

	//include classes
	include('model/HomePopUp.php');
	include('model/Sidebar.php');

    //theme setup
    show_admin_bar(true);
    add_theme_support('post-thumbnails');
	
	//shortcodes
	add_shortcode('add_page_featurettes', 'page_featurettes');
    add_shortcode('secondary_links', 'cc_secondary_links');
    
    //actions
	add_action('init', 'cc_register_custom_posts');
    add_action( 'widgets_init', 'cc_widgets_init' );
    
    //functions

/**
 * gets the featurettes for a given page
 *
 * @param $args
 *
 * @return string
 */
    function page_featurettes($args){
    	
		$html = '<div class="row">';
		if (!empty($args['page_id'])):
			$page_id = $args['page_id'];			
			$pages_arr = get_posts(
				array(
					'order' => 'ASC',
					'orderby' => 'menu_order',
					'post_parent' => $page_id,
					'post_status' => 'publish',
					'post_type' => 'page',
					'posts_per_page' => 3
				)
			);
			if (!empty($pages_arr) && is_array($pages_arr)):
				foreach ($pages_arr as $page):
					
					//check if it has a featured image
					$image = wp_get_attachment_image_src(get_post_thumbnail_id($page->ID), 'full' );
					
					$html .= '<div class="col-xs-12 col-sm-4">';
						if (!empty($image)):
							$html .= '<div class="featured-image"><img src="' . $image[0] . '" alt="' . $page->post_title . ' image" class="img-responsive" /></div>'; 
						endif;
						$html .= '<h3>' . $page->post_title . '</h3>';
						$html .= apply_filters('the_content', $page->post_content);
					$html .= '</div>';
				endforeach;
			endif;
		endif;
		$html .= '</div>';
		
		return $html;
    }

    function cc_register_custom_posts(){

	    // meal pick-up date
	    register_post_type('cc_meal_pickup_date', [
		    'description' => 'Meal Pick-Up Schedules',
		    'hierarchical' => true,
		    'labels' => [
			    'add_new' => 'Add New Date',
			    'all_items' => 'All Dates',
			    'archives' => 'Date Archives',
			    'attributes' => 'Item Attributes',
			    'edit_item' => 'Edit Date',
			    'featured_image' => 'Date Image',
			    'insert_into_item' => 'Insert in Date',
			    'name' => 'Meal Dates',
			    'new_item' => 'New Date',
			    'not_found' => 'Item not found',
			    'not_found_in_trash' => 'Item not found in trash',
			    'parent_item_colon' => 'Parent Item:',
			    'search_items' => 'Search Dates',
			    'singular_name' => 'Date',
			    'uploaded_to_this_item' => 'Uploaded to this item',
			    'view_item' => 'View Item',
			    'view_items' => 'View Dates'
		    ],
		    'menu_icon' => get_bloginfo('stylesheet_directory') . '/images/meal-pickup-date-icon.png',
		    'menu_position' => -1,
		    'public' => false,
		    'show_in_menu' => true,
		    'show_ui' => true,
		    'supports' => [
			    'page-attributes',
			    'custom-fields',
			    'editor',
			    'excerpt',
			    'title',
			    'trackbacks'
		    ]
	    ]);

    	// menu items
	    register_post_type('cc_menu_item', [
		    'description' => 'Menu items sold by Carmella\'s Cuisine',
	    	'hierarchical' => true,
	    	'labels' => [
		    	'add_new' => 'Add New Menu Item',
		    	'all_items' => 'All Items',
		    	'archives' => 'Menu Item Archives',
		    	'attributes' => 'Item Attributes',
		    	'edit_item' => 'Edit Menu Item',
		    	'featured_image' => 'Menu Item Image',
		    	'insert_into_item' => 'Insert in menu item',
		    	'name' => 'Menu Items',
			    'new_item' => 'New Menu Item',
				'not_found' => 'Item not found',
				'not_found_in_trash' => 'Item not found in trash',
				'parent_item_colon' => 'Parent Item:',
				'search_items' => 'Search Items',
			    'singular_name' => 'Menu Item',
			    'uploaded_to_this_item' => 'Uploaded to this item',
			    'view_item' => 'View Item',
			    'view_items' => 'View Items'
		    ],
		    'menu_icon' => get_bloginfo('stylesheet_directory') . '/images/menu-item-icon.png',
		    'menu_position' => 0,
		    'public' => false,
		    'show_in_menu' => true,
		    'show_ui' => true,
		    'supports' => [
		    	'page-attributes',
		    	'custom-fields',
		    	'editor',
		    	'excerpt',
		    	'thumbnail',
		    	'title',
			    'trackbacks'
		    ]
	    ]);


	    // menu items
	    register_post_type('cc_orders', [
		    'capabilities' => [
		    	'read_post'
		    ],
	    	'description' => 'Orders from Carmella\'s Cuisine Customers',
		    'hierarchical' => true,
		    'labels' => [
			    'add_new' => 'Add New Order',
			    'all_items' => 'All Items',
			    'archives' => 'Order Archives',
			    'attributes' => 'Item Attributes',
			    'edit_item' => 'Edit Order',
			    'featured_image' => 'Order Image',
			    'insert_into_item' => 'Insert in Order',
			    'name' => 'Orders',
			    'new_item' => 'New Order',
			    'not_found' => 'Item not found',
			    'not_found_in_trash' => 'Item not found in trash',
			    'parent_item_colon' => 'Parent Item:',
			    'search_items' => 'Search Items',
			    'singular_name' => 'Order',
			    'uploaded_to_this_item' => 'Uploaded to this item',
			    'view_item' => 'View Item',
			    'view_items' => 'View Items'
		    ],
		    'menu_icon' => get_bloginfo('stylesheet_directory') . '/images/order-icon.png',
		    'menu_position' => 1,
		    'public' => false,
		    'show_in_menu' => true,
		    'show_ui' => true,
		    'supports' => [
			    'page-attributes',
			    'custom-fields',
			    'editor',
			    'excerpt',
			    'thumbnail',
			    'title',
			    'trackbacks'
		    ]
	    ]);




    }
    
    /**
    * renders a promotional overlay pop-up
    * 
    * @return string
    */
    function cc_render_home_popup(){
		
		//vars
		$pop_up = new HomePopUp();
		if ($pop_up->is_active()):
			$html = '<div id="home-pop-up" class="col-xs-10 col-xs-offset-1 col-md-6 col-md-offset-3">';
			$html .= $pop_up->get_post()->post_content;
			$html .= 	'<div class="close-link-wrapper text-center" type="button"><button class="btn btn-default home-popup-close">Close</button></div>';
			$html .= '</div>';
			$html .= '<div class="semi-opaque-overlay"></div>';
			$html .= '<script type="text/javascript" src="' . get_bloginfo('stylesheet_directory') . '/js/home-pop-up.js"></script>';
		else:
			$html = '<!--no pop-up to render-->';		
		endif;
		
		return $html;
    }
    
    /**
     * returns a string of hard-coded secondary page links
     * 
     * @return string
     */
    function cc_secondary_links(){
        return '
            <div class="secondary-links row text-center">
                <!--<div class="row">
                    <div class="col-xs-12 text-left">
                        <h3>Menus</h3>
                    </div>
                </div>--> 
               <div class="row">
                    <div class="col-xs-12 col-md-4">
                        <a href="' . get_bloginfo('url') . '/sample-menus" class="btn btn-lg btn-success-outline">DINNER &amp; BUFFET MENUS</a>
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <a href="' . get_bloginfo('url') . '/craft-services" class="btn btn-lg btn-success-outline">CRAFT SERVICES & CORPORATE</a>
                    </div>
                    <div class="col-xs-12 col-md-4">
                        <a href="' . get_bloginfo('url') . '/weddings-events" class="btn btn-lg btn-success-outline">WEDDING &amp; EVENTS MENUS</a>
                    </div> 
                </div>
            </div>
        ';
    } 
    
    /**
    @@cc_subpages
    * returns an array of sub-pages for a given page ID  
    * 
    * @param int $post_id
    * @param string $post_type
    * @param string $post_status
    * @param string $order_by
    * @param string $order
    * @return WP_Post[]
    */
    function cc_subpages($post_id, $post_type = 'page', $post_status = 'publish', $order_by = 'menu_order', $order = 'ASC'){
            $pages = get_posts(
                    array(
                            'order' => $order,
                            'orderby' => $order_by,
                            'post_parent' => $post_id,
                            'posts_per_page' => 99,
                            'post_status' => $post_status,
                            'post_type' => $post_type
                    )
            );

            return $pages;
    }
    
    /**
    * Register our sidebars and widgetized areas.
    *
    */
   function cc_widgets_init() {

       register_sidebar( array(
           'name'          => 'Sample Menus Sidebar',
           'id'            => Sidebar::SIDEBAR_ID_SAMPLE_MENUS,
           'before_widget' => '<div>',
           'after_widget'  => '</div>',
           'before_title'  => '<h2 class="rounded">',
           'after_title'   => '</h2>',
       ) );
       
       register_sidebar( array(
           'name'          => 'Weddings &amp; Events Sidebar',
           'id'            => Sidebar::SIDEBAR_ID_WEDDINGS_EVENTS,
           'before_widget' => '<div>',
           'after_widget'  => '</div>',
           'before_title'  => '<h2 class="rounded">',
           'after_title'   => '</h2>',
       ) );
        
       
   }