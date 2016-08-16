<?php

add_action('admin_init','optionscheck_change_santiziation', 100);
function optionscheck_change_santiziation() {
    remove_filter( 'of_sanitize_textarea', 'of_sanitize_textarea' );
    add_filter( 'of_sanitize_textarea', 'custom_sanitize_textarea' );
}

function custom_sanitize_textarea($input) {
    global $allowedposttags;
    $custom_allowedtags["embed"] = array(
      "src" => array(),
      "type" => array(),
      "allowfullscreen" => array(),
      "allowscriptaccess" => array(),
      "height" => array(),
          "width" => array()
      );
      $custom_allowedtags["script"] = array();
      $custom_allowedtags = array_merge($custom_allowedtags, $allowedposttags);
      $output = wp_kses( $input, $custom_allowedtags);
    return $output;
}

function optionsframework_option_name() {
	// This gets the theme name from the stylesheet
	$themename = get_option( 'stylesheet' );
	$themename = preg_replace("/\W/", "_", strtolower($themename) );

	$optionsframework_settings = get_option( 'optionsframework' );
	$optionsframework_settings['id'] = $themename;
	update_option( 'optionsframework', $optionsframework_settings );
}

function optionsframework_options() {
	global $shortname;

//	Sidebar styles
	$defined_sidebar = array(
		'modern' => __('Modern', 'pixelstores'),
		'classic' => __('Classic', 'pixelstores')
);
	
	$options = array();

	$options[] = array(
		'name' => __('General', 'pixelstores'),
		'type' => 'heading');
		
	$options[] = array(
		'name' => __('Responsive', 'pixelstores'),
		'desc' => __('Responsive mobile friendly design.', 'pixelstores'),
		"id" => $shortname . '_responsive',
		'std' => '1',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Menu Icons', 'pixelstores'),
		'desc' => __('Enable icon styles for the header menu.', 'pixelstores'),
		'id' => $shortname . '_menu_css',
		'type' => 'checkbox');


	$options[] = array(
		'name' => __('Products Per Page', 'pixelstores'),
		'desc' => __('How many products to show per page in the Catalogue.', 'pixelstores'),
		'id' => $shortname . '_products_pp',
		'class' => 'mini',
		'std' => '9',
		'type' => 'text');

	$options[] = array(
		'name' => __('Custom CSS', 'pixelstores'),
		'desc' => __('Enable the custom stlyesheet in the /assets/ directory.', 'pixelstores'),
		'id' => $shortname . '_custom_css',
		'type' => 'checkbox');
	
	$options[] = array(
		'name' => __('Contact Email', 'pixelstores'),
		'id' => $shortname . '_contact_email',
		'std' => get_bloginfo('admin_email'),
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Google Analytics', 'pixelstores'),
		'desc' => __('Insert your analytical code.', 'pixelstores'),
		'id' => $shortname . '_analytical',
		'type' => 'textarea');						

	$options[] = array(
		'name' => __('Homepage', 'pixelstores'),
		'type' => 'heading');

	$options[] = array(
		'name' => __('Featured Products', 'pixelstores'),
		'desc' => __('Display featured products on the homepage.', 'pixelstores'),
		'id' => $shortname . '_featured_products',
		'std' => '1',
		'type' => 'checkbox');
		
	$options[] = array(
		'name' => __('Featured Text', 'pixelstores'),
		'id' => $shortname . '_featured_text',
		'std' => 'Featured Items',
		'type' => 'text');

	$options[] = array(
		'name' => __('Products', 'pixelstores'),
		'desc' => __('How many products to show.', 'pixelstores'),
		'id' => $shortname . '_featured_show',
		'class' => 'mini',
		'std' => '12',
		'type' => 'text');

	$options[] = array(
		'name' => __('Display', 'pixelstores'),
		'desc' => __('How many products to scroll.', 'pixelstores'),
		'id' => $shortname . '_featured_scroll',
		'std' => '1',
		'class' => 'mini',
		'type' => 'text');
		
	$options[] = array(
		'name' => __('Branding', 'pixelstores'),
		'type' => 'heading' );


	$options[] = array( 
		'name' => __('Sidebar Skin', 'pixelstores'),
		'id' => $shortname . '_sidebar_skin',
		"std" => "0",
		"type" => "select",
		"options" => $defined_sidebar );
		
	$options[] = array(
		'name' => __('Favicon', 'pixelstores'),
		'desc' => __('Upload your favicon.', 'pixelstores'),
		'id' => $shortname . '_favicon',
		'type' => 'upload');
	
	$options[] = array(
	'name' => __('Blog', 'pixelstores'),
	'type' => 'heading' );
	
	$options[] = array(
		'name' => __('Blog Posts', 'pixelstores'),
		'desc' => __('Number of blog posts per page.', 'pixelstores'),
		'id' => $shortname . '_posts',
		'std' => '5',
		'type' => 'text');
	
	$options[] = array(
		'name' => __('Breadcrumbs', 'pixelstores'),
		'desc' => __('Display breadcrumbs on post.', 'pixelstores'),
		"id" => $shortname . '_breadcrumbs',
		'std' => '1',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Featured Image', 'pixelstores'),
		'desc' => __('Display featured image on blog post.', 'pixelstores'),
		"id" => $shortname . '_featured_image',
		'std' => '1',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Share Buttons', 'pixelstores'),
		'desc' => __('Display social share buttons on post.', 'pixelstores'),
		"id" => $shortname . '_social',
		'std' => '1',
		'type' => 'checkbox');

	$options[] = array(
		'name' => __('Twitter ID', 'pixelstores'),
		'id' => $shortname . '_twitter_id',
		'type' => 'text');
			
	$options[] = array(
	'name' => __('Footer', 'pixelstores'),
	'type' => 'heading' );
	
	$options[] = array(
		'name' => __('Copyright', 'pixelstores'),
		'desc' => __('Custom copyright footer text.', 'pixelstores'),
		'id' => $shortname . '_copyright',
		'type' => 'text');

	return $options;
}