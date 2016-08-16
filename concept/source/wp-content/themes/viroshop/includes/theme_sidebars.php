<?php

//	Register sidebars posttype
	function register_sidebars_posttype() {
		$labels = array(
			'name' 				=> __( 'Sidebars' ),
			'singular_name'		=> __( 'Sidebar' ),
			'add_new_item' 		=> __( 'Sidebar' ),
			'edit_item' 		=> __( 'Edit Sidebar' ),
			'not_found' 		=> __( 'No sidebars found' ),
			'not_found_in_trash' 		=> __( 'No sidebars found in trash' )
		);		
		$taxonomies = array('sidebar');		
		$supports = array('title');
		
		$post_type_args = array(
			'labels' 			=> $labels,
			'singular_label' 	=> __('Sidebar'),
			'public' 			=> false,
			'show_in_menu'		=> 'themes.php',
			'show_ui' 			=> true,
			'show_in_admin_bar' => true,
			'publicly_queryable'=> true,
			'query_var'			=> true,
			'capability_type' 	=> 'post',
			'has_archive' 		=> false,
			'hierarchical' 		=> false,
			'rewrite' 			=> false,
			'supports' 			=> $supports,
			'taxonomies'		=> $taxonomies
		 );
		register_post_type('sidebars',$post_type_args);
	}
	add_action('init', 'register_sidebars_posttype');

//	Change sidebars posttype titles
	add_filter( 'post_updated_messages', 'update_sidebar_generator_messages' );
	function update_sidebar_generator_messages( $messages ) {
		global $post, $post_ID;	
		$messages['sidebars'] = array(
			0	=> '', // Unused. Messages start at index 1.
			1	=> sprintf( __( 'Sidebar updated. <a href="%s">Add widgets</a>', 'pixelstores' ), admin_url( 'widgets.php' ) ),
			2	=> __( 'Sidebar updated.', 'pixelstores' ),
			3	=> __( 'Sidebar updated.', 'pixelstores' ),
			4	=> __( 'Sidebar updated.', 'pixelstores' ),
			/* translators: %s: date and time of the revision */
			5	=> isset( $_GET['revision'] ) ? sprintf( __( 'Sidebar restored to revision from %s', 'pixelstores' ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
			6	=> sprintf( __( 'Sidebar created. <a href="%s">Add widgets</a>', 'pixelstores' ), admin_url( 'widgets.php' ) ),
			7	=> __( 'Sidebar saved.', 'pixelstores' ),
			8	=> __( 'Sidebar submitted.', 'pixelstores' ),
			9	=> sprintf(__( 'Sidebar scheduled for: <strong>%1$s</strong>.', 'pixelstores' ), date_i18n( __( 'M j, Y @ G:i', 'wap8lang' ), strtotime( $post->post_date ) ) ),
			10	=> __( 'Sidebar draft updated.', 'pixelstores' ),
		);
		return $messages;
	}

//	Register sidebars
	if (function_exists('register_sidebar')){	
		$dynamic_widget_areas = $wpdb->get_col(
			"SELECT post_title
			FROM $wpdb->posts
			WHERE post_type = 'sidebars'
			AND post_author = 1 
			AND post_status IN ('publish', 'draft')"
		); 	
		register_sidebar(array('name'=>'Default Sidebar',
		  'id' => 'sidebar-default',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><!-- end .widget -->',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
		));		
		register_sidebar(array('name'=>'Shop',
		  'id' => 'sidebar-shop',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><!-- end .widget -->',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
		));		
		register_sidebar(array('name'=>'Shop Category',
		  'id' => 'sidebar-shop-category',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><!-- end .widget -->',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
		));
		register_sidebar(array('name'=>'Blog Post',
		  'id' => 'sidebar-blog-post',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><!-- end .widget -->',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
		));
		register_sidebar(array('name'=>'Blog Category',
		  'id' => 'sidebar-blog-category',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div><!-- end .widget -->',
			'before_title' => '<h4 class="widgettitle">',
			'after_title' => '</h4>',
		));
		foreach ($dynamic_widget_areas as $widget_area_name) {
			register_sidebar(array(
			   	'name'=> $widget_area_name,
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div><!-- end .widget -->',
				'before_title' => '<h4 class="widgettitle">',
				'after_title' => '</h4>'
			));
    	}
	}

//	Call sidebars
	function generated_dynamic_sidebar(){	
		global $post, $pixelstore_mb_side_sidebar;
		$meta = get_post_meta(get_the_ID(), $pixelstore_mb_side_sidebar->get_the_id(), TRUE);	
			if (is_single() && !is_product()) { 
				dynamic_sidebar('Blog Post');
			}
			elseif (is_category()) { 
				dynamic_sidebar('Blog Category');
			}			
			elseif (is_shop()) { 
				dynamic_sidebar('Shop');
			}
			elseif  (is_product_category()) { 
				dynamic_sidebar('Shop Category');
			}			
			elseif (empty($metasidebar[sidebar])) { 
				dynamic_sidebar($meta[sidebar]);
			}
			else {
				dynamic_sidebar('Default Sidebar');  
			} 
	}
?>