<?php

include_once 'WPAlchemy/MetaBox.php';
include_once 'WPAlchemy/MediaAccess.php';

$wpalchemy_media_access = new WPAlchemy_MediaAccess();

$pixelstore_mb = new WPAlchemy_MetaBox(array
(
	'types' => array('page'),
	'id' => '_custom_meta', // underscore prefix hides fields from the custom fields area
	'title' => 'Slider Properties',
	'template' => STYLESHEETPATH . '/includes/custom_fields/custom/meta.php'
));

$pixelstore_mb_side_sidebar = new WPAlchemy_MetaBox(array
(
	'types' => array('page','product'),
	'id' => '_custom_meta_one', // underscore prefix hides fields from the custom fields area
	'title' => 'Sidebar',
	'context' => 'side',
	'priority' => 'low',
	'template' => STYLESHEETPATH . '/includes/custom_fields/custom/meta_sidebar.php'
));

$pixelstore_mb_side = new WPAlchemy_MetaBox(array
(
	'types' => array('page'),
	'id' => '_custom_meta_two', // underscore prefix hides fields from the custom fields area
	'title' => 'Background Properties',
	'context' => 'side',
	'priority' => 'low',
	'template' => STYLESHEETPATH . '/includes/custom_fields/custom/meta_side.php'
));

function load_meta_scripts() {
	global $pagenow, $typenow;
	if (empty($typenow) && !empty($_GET['post'])) {
		$post = get_post($_GET['post']);
		$typenow = $post->post_type;
	}
	if (is_admin() && $typenow=='page') {
		wp_enqueue_style( 'metabox', get_template_directory_uri() . '/includes/custom_fields/skin/meta.css', false, '1.0.0' );
		wp_enqueue_script( 'jshashtable', get_template_directory_uri() . '/includes/custom_fields/skin/js/jshashtable-2.1_src.js', array( 'jquery' ) ); 
		wp_enqueue_script( 'numberformatter', get_template_directory_uri() . '/includes/custom_fields/skin/js/jquery.numberformatter-1.2.3.js', array( 'jquery' ) ); 
		wp_enqueue_script( 'tmpl', get_template_directory_uri() . '/includes/custom_fields/skin/js/tmpl.js', array( 'jquery' ) ); 
		wp_enqueue_script( 'dependClass', get_template_directory_uri() . '/includes/custom_fields/skin/js/jquery.dependClass-0.1.js', array( 'jquery' ) ); 
		wp_enqueue_script( 'draggable', get_template_directory_uri() . '/includes/custom_fields/skin/js/draggable-0.1.js"', array( 'jquery' ) ); 
		wp_enqueue_script( 'slider', get_template_directory_uri() . '/includes/custom_fields/skin/js/jquery.slider.js', array( 'jquery' ) ); 
		wp_enqueue_script( 'slickswitch', get_template_directory_uri() . '/includes/custom_fields/skin/js/jquery.slickswitch.js', array( 'jquery' ) ); 
	}
}	
add_action('admin_enqueue_scripts', 'load_meta_scripts');