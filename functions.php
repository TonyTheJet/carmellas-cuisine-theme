<?php

//include classes
include('model/HomePopUp.php');
include('model/Sidebar.php');

//theme setup
show_admin_bar(true);
add_theme_support('post-thumbnails');

//shortcodes
add_shortcode('add_page_featurettes', 'page_featurettes');
add_shortcode('meal_order_page', 'cc_meal_order_page');
add_shortcode('secondary_links', 'cc_secondary_links');

//actions
add_action('init', 'cc_register_custom_posts');
add_action('widgets_init', 'cc_widgets_init');

// AJAX
if (!empty($_POST['action']) && $_POST['action'] === 'fetch_items_for_pickup_date'):
	cc_ajax_fetch_items_for_pickup_date();
endif;
// end AJAX




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

    function cc_ajax_fetch_items_for_pickup_date(){
    	$return_arr = [
		    'message' => 'No post ID supplied',
    		'pickup_date' => null,
		    'pickup_date_date_string' => '',
		    'pickup_date_items' => [],
    		'success' => false
	    ];
    	if (!empty($_POST['post_id'])):
	        $post = get_post((int) $_POST['post_id']);
    	    if (!empty($post) && $post->post_type === 'cc_meal_pickup_date'):
		        $return_arr['pickup_date_date_string'] = get_post_meta($post->ID, 'pick_up_date', true);
    	        $menu_items_for_date = get_post_meta($post->ID, 'menu_items_for_date', true);
    	        $i = 0;
    	        foreach ($menu_items_for_date as $menu_item_id):
	                $menu_item = get_post($menu_item_id);
    	            $return_arr['pickup_date_items'][$i]['basic_data'] = get_post($menu_item_id);
    	            $return_arr['pickup_date_items'][$i]['bulk_price'] = get_post_meta($menu_item_id, 'bulk_price', true);
		            $return_arr['pickup_date_items'][$i]['minimum_bulk_price_quantity'] = get_post_meta($menu_item_id, 'minimum_bulk_price_quantity', true);
    	            $return_arr['pickup_date_items'][$i]['price'] = get_post_meta($menu_item_id, 'price', true);
    	            $return_arr['pickup_date_items'][$i]['thumbnail_url'] = get_the_post_thumbnail_url($menu_item_id, 'post-thumbnail');
    	            $i++;
	            endforeach;


		        $return_arr['message'] = 'success';
		        $return_arr['pickup_date'] = $post;
		        $return_arr['success'] = true;
	        endif;
    	endif;

    	echo json_encode($return_arr);

    	exit();
    }

    function cc_meal_order_page(){

    	// query the upcoming stuff
	    $pickup_dates = cc_upcoming_pickup_dates();
		$pickup_dates_html = '';
		if (empty($pickup_dates)):
			$pickup_dates_html = 'No dates currently available';
		else:
			foreach ($pickup_dates as $pickup):
				$date = get_field('pick_up_date', $pickup->ID, true);
				$menu_items = get_field('menu_items_for_date', $pickup->ID, false);
				$items_html = '';
				foreach ($menu_items as $item_id):
					$item = get_post($item_id);
					$items_html .= '<li>' . $item->post_title . '</li>';
				endforeach;

				$pickup_dates_html .= '
					<div class="pick-up-date col-xs-6 col-sm-4 col-md-3" data-date_id="' . $pickup->ID .'">
						<div class="pick-up-date-inner">
							<h2>' . $date . '</h2>
							<h3>Menu Options</h3>
							<ul>
								' . $items_html . '
							</ul>
						</div>
					</div>
				';
			endforeach;
		endif;

    	return '
    	    <form id="order-meal-app">
    	    	<div id="order-meal-step-list-wrapper" class="row">
    	    		<div id="order-meal-step-1" class="order-meal-step active">
    	    			<h2>1. Choose Pick-Up Date</h2>
					</div>
					<div id="order-meal-step-2" class="order-meal-step">
						<h2>2. Choose Items and Quantities</h2>
					</div>
					<div id="order-meal-step-3" class="order-meal-step">
						<h2>3. Enter Your Information</h2>
					</div>
					<div id="order-meal-step-4" class="order-meal-step">
						<h2>4. Receive Confirmation Email</h2>
					</div>
				</div>
				<div id="order-meal-body-wrapper">
					<div id="order-meal-step-1-body" class="order-meal-step-body">
						<div class="row">
							<div class="col-xs-12">
								<h1>Choose a Meal Pick-Up Date</h1>
							</div>
							' . $pickup_dates_html . '
						</div>
						<div class="row">
							<div class="col-xs-12 text-right">
								<button type="button" class="btn btn-primary load-step" data-load_step="2" id="step-1-continue-btn" disabled>Continue</button>
							</div>
						</div>
					</div>
					<div id="order-meal-step-2-body" class="order-meal-step-body hidden">
						<div class="row">
							<div class="col-xs-12">
								<h1>Please Choose Your Items</h1>
								<div id="meal-items"><img class="center-block" src="' . get_bloginfo('stylesheet_directory') . '/images/eclipse-1s-200px.gif' . '" /></div>
								<div class="row">
									<div class="col-xs-12 col-md-8 col-md-offset-2 col-lg-6 col-lg-offset-6">
										<table class="table table-condensed table-bordered text-right" id="totals-row">
											<tr>
												<td>
													<strong>Subtotal:</strong> 
												</td>
												<td>
													$<span id="order-subtotal">0.00</span>
												</td>
											</tr>
											<tr>
												<td>
													<strong>Sales Tax:</strong> 
												</td>
												<td>
													$<span id="order-sales_tax">0.00</span>
												</td>
											</tr>
											<tr>
												<td>
													<strong>TOTAL:</strong> 		
												</td>
												<td>
													$<span id="order-total">0.00</span>
												</td>
											</tr>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-12">
								<div class="pull-left">
									<button type="button" class="btn btn-default load-step" data-load_step="1">Back</button>
								</div>
								<div class="pull-right">
									<button type="button" class="btn btn-primary load-step" id="step-2-continue-btn" disabled data-load_step="3">Continue</button>
								</div>
							</div>
						</div>
						
					</div>
					<div id="order-meal-step-3-body" class="order-meal-step-body hidden">
						<div class="row">
							<div class="col-xs-12 col-md-6">
								<h1>Enter Your Information</h1>
								<div class="form-group">
									<label for="customer-name">Name</label>
									<input type="text" id="customer-name" class="form-control customer-info" maxlength="100" placeholder="Jane Doe" required />
								</div>
								<div class="form-group">
									<label for="customer-email">Email</label>
									<input type="email" id="customer-email" class="form-control customer-info" maxlength="100" placeholder="my-email@gmail.com" required />
								</div>
								<div class="form-group">
									<label for="customer-phone">Phone</label>
									<input type="text" id="customer-phone" class="form-control customer-info" maxlength="100" placeholder="801.123.4560" required />
								</div>
								<div class="form-group">
									<label for="customer-notes">Notes</label>
									<textarea class="form-control customer-info" id="customer-notes" placeholder="Allergic to nuts; Will pick up around 3:30 p.m., etc."></textarea>
								</div>
							</div>
							<div class="col-xs-12 col-md-6">
								<h1>Order Summary</h1>
								<p><strong><u>Payment is made upon pick-up</u></strong> via Venmo, credit card, or cash.</p>
								<table class="table table-striped table-bordered table-condensed table-responsive" id="order-summary">
									<thead>
										<tr>
											<th>Item</th>
											<th>Quantity</th>
											<th>Price ea.</th>
										</tr>
									</thead>
									<tbody>
									
									</tbody>
									<tfoot>
										<tr>
											<td colspan="2">Subtotal</td>
											<td id="order-summary-subtotal" class="text-right">$0.00</td>
										</tr>
										<tr>
											<td colspan="2">Sales Tax</td>
											<td id="order-summary-sales_tax" class="text-right">$0.00</td>
										</tr>
										<tr class="active">
											<td colspan="2"><strong class="secondary-color">Amount Due at Pick-Up</strong></td>
											<td class="text-right"><strong id="order-summary-total">$0.00</strong></td>
										</tr>
									</tfoot>
								</table>
							</div>
							<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
							  <div class="modal-dialog" role="document">
							    <div class="modal-content">
							      <div class="modal-header">
							        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							        <h4 class="modal-title" id="myModalLabel">Confirm Order</h4>
							      </div>
							      <div class="modal-body">
							      		Are you sure you\'d like to place this order for $<span id="order-total"></span>?
							      </div>
							      <div class="modal-footer">
							        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							        <button type="button" class="btn btn-primary">Save changes</button>
							      </div>
							    </div>
							  </div>
							</div>
						</div>
						<div class="pull-left">
							<button type="button" class="btn btn-default load-step" data-load_step="2">Back</button>
						</div>
						<div class="pull-right"><button type="submit" class="btn btn-primary load-step" id="step-3-continue-btn" disabled data-load_step="4">Place Order</button></div>
					</div>
					<div id="order-meal-step-4-body" class="order-meal-step-body hidden">
						<div class="row">
							<div class="col-xs-12">
								<h1>SUCCESS!</h1>
								Thank you for your order! Your order details are below. Please arrive between 11:00 a.m. and 6:00 p.m. on the date of your pick-up. <u>Payment is made upon pick-up in the form of either cash or credit card</u>.
							</div>
						</div>
					</div>
				</div>
    	    </form>
    	    <script type="text/javascript">
    	    	var ajaxurl = "' . admin_url('admin-ajax.php') . '";
    	    	var stylesheet_directory = "' . get_bloginfo('stylesheet_directory') . '";
			</script>
    	    <script type="text/javascript" src="' . get_bloginfo('stylesheet_directory') . '/js/meal-order.js"></script>
    	';
    }

    function cc_upcoming_pickup_dates(){
    	$raw_posts = get_posts(
    		[
    			'numberposts' => 100,
    			'order' => 'DESC',
    			'orderby' => 'date',
    			'post_status' => 'publish',
    			'post_type' => 'cc_meal_pickup_date'
		    ]
	    );

    	// filter them to just future posts, and a maximum of 12
	    $filtered_posts = [];
	    foreach ($raw_posts as $post):
	        if (count($filtered_posts) >= 12):
		        break;
	        endif;

	        $last_pickup_datetime = new DateTime(get_field('orderby_date_time', $post->ID, false));
	        if ($last_pickup_datetime->getTimestamp() > time()):
		        $filtered_posts[] = $post;
	        endif;
	    endforeach;

	    // reverse the order of the array so the customer gets the earliest date first
    	return array_reverse($filtered_posts);
    }

    function cc_register_custom_posts(){

	    // meal pick-up date
	    register_post_type('cc_meal_pickup_date', [
		    'description' => 'Meal Pick-Up Schedules',
		    'hierarchical' => true,
		    'labels' => [
			    'add_new_item' => 'Add New Pick-Up Date',
			    'all_items' => 'All Pick-Up Dates',
			    'archives' => 'Date Archives',
			    'attributes' => 'Date Attributes',
			    'edit_item' => 'Edit Pick-Up Date',
			    'featured_image' => 'Date Image',
			    'insert_into_item' => 'Insert in Date',
			    'name' => 'Pick-Up Dates',
			    'new_item' => 'New Pick-Up Date',
			    'not_found' => 'Item not found',
			    'not_found_in_trash' => 'Item not found in trash',
			    'parent_item_colon' => 'Parent Item:',
			    'search_items' => 'Search Dates',
			    'singular_name' => 'Pick-Up Date',
			    'uploaded_to_this_item' => 'Uploaded to this item',
			    'view_item' => 'View Item',
			    'view_items' => 'View Dates'
		    ],
		    'menu_icon' => get_bloginfo('stylesheet_directory') . '/images/meal-pickup-date-icon.png',
		    'menu_position' => -2,
		    'public' => false,
		    'show_in_menu' => true,
		    'show_ui' => true,
		    'supports' => [
			    'custom-fields',
			    'page-attributes',
			    'title'
		    ]
	    ]);

    	// menu items
	    register_post_type('cc_menu_item', [
		    'description' => 'Menu items sold by Carmella\'s Cuisine',
	    	'hierarchical' => true,
	    	'labels' => [
		    	'add_new' => 'Add New',
		    	'add_new_item' => 'Add New Menu Item',
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
		    'menu_position' => -1,
		    'public' => false,
		    'show_in_menu' => true,
		    'show_ui' => true,
		    'supports' => [
		    	'page-attributes',
		    	'custom-fields',
		    	'editor',
		    	'excerpt',
		    	'thumbnail',
		    	'title'
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
			    'add_new_item' => 'Add New Order',
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