<?php
	require_once(TEMPLATEPATH . '/includes/pixelstores/widgets.php');
	require_once(TEMPLATEPATH . '/includes/pixelstores/shortcodes/shortcodes.php');
 	
 	$shortname = 'viro';

//	Favicon
 	function add_favicon(){
 		global $shortname;	
		$faviconUrl = of_get_option($shortname.'_favicon');
		if ($faviconUrl) echo('<link rel="shortcut icon" href="'.$faviconUrl.'" />');
	}
	add_action('wp_head','add_favicon');

//	Custom footer js
	function add_analytical(){
		global $shortname;	
		$analytical = of_get_option($shortname.'_analytical');
		if ($analytical) echo($analytical);
	}
	add_action('wp_footer','add_analytical');

//	Custom stylesheet		
	function add_custom_css() {
		global $shortname;	
		if (of_get_option($shortname.'_custom_css')) {
			wp_enqueue_style('cusomcss', get_template_directory_uri(). '/assets/custom.css');
		}
	}
	add_action('wp_enqueue_scripts', 'add_custom_css');

//	Add flexi_background styling
	function flexi_background () {
		global $shortname, $pixelstore_mb_side, $post; 
		$metaside = get_post_meta(get_the_ID(), $pixelstore_mb_side->get_the_id(), TRUE);
		if(!empty($metaside[bgimgurl]) && !of_get_option($shortname.'_bg_styling')) {
			echo "<style type='text/css'>body{background-attachment:fixed;background-color:#333;background-image:url(". $metaside[bgimgurl].");background-position:top center;background-repeat:no-repeat;margin:0;padding:0;background-size:cover;-moz-background-size:cover;-webkit-background-size:cover;}@media only all and (max-width: 640px) and (max-height: 426px) {body{background-size:640px 426px;-moz-background-size:640px 426px;-webkit-background-size:640px 426px;}}@media only screen and (orientation: portrait) and (device-width: 320px), (device-width: 768px) {body{-webkit-background-size:auto 100%;}}@media only screen and (orientation: landscape) and (device-width: 320px), (device-width: 768px) {body{-webkit-background-size:100% auto;}}img#expando{display:none;position:absolute;z-index:1;-ms-interpolation-mode:bicubic;}.wide img#expando,.tall img#expando{display:block;}.wide img#expando{height:auto;width:100%;}.tall img#expando{height:100%;width:auto;}</style>";
		}
		else {

		}
	}  
	add_action( 'wp_footer', 'flexi_background' ); 
	
//	Add custom styling
 	function add_custom_styling(){
 		global $shortname, $pixelstore_mb_side, $post;	
 		$background = of_get_option($shortname.'_custom_background');
		$background_path = get_template_directory_uri() . '/includes/pixelstores/inc/images/patterns/';			
		$metaside = get_post_meta(get_the_ID(), $pixelstore_mb_side->get_the_id(), TRUE);	
		echo "<style type='text/css'>";
			if (of_get_option(viro_sidebar_skin) == 'classic') { 
				echo ".widget_loginformwidget {padding: 0;}.widget {background-image: none !important;}#sidebar .widget_price_filter {border-bottom-style: none !important;}.widget_nav_menu {border-top: 1px solid #e2e2e2;border-bottom: 1px solid #f8f8f8;padding: 0 !important;margin-bottom: 20px;}.widget_nav_menu .widgettitle {display: none;}.widget_nav_menu ul.menu  li a  {padding: 6px 6px 6px 20px;border-bottom: 1px solid #e2e2e2;border-top: 1px solid #f8f8f8;text-transform: uppercase;font-size: 10px;}.widget_nav_menu ul.menu  li a:hover  {background-color: #ebebeb;}.widget_nav_menu ul.menu .sub-menu a{border-style: none;}.widget_nav_menu ul.menu .sub-menu {border-top: 1px solid #f8f8f8;border-bottom: 1px solid #e2e2e2;}"; 
					} else {			
				echo "@media only screen and (max-width: 479px) {.widget_loginformwidget {background-image: none;}}.widget_loginformwidget {padding: 0;height: 192px;}"; 
				}
		echo "</style>";

	}
	add_action('wp_footer','add_custom_styling');
?>