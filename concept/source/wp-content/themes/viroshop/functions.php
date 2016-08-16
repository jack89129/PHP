<?php
	if ( !function_exists( 'optionsframework_init' ) ) {
		define( 'OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/includes/pixelstores/inc/' );
		require_once dirname( __FILE__ ) . '/includes/pixelstores/inc/options-framework.php';
	}
	require_once(TEMPLATEPATH . '/includes/theme_customize.php'); 
	require_once(TEMPLATEPATH . '/includes/theme_sidebars.php'); 
	require_once(TEMPLATEPATH . '/includes/theme_scripts.php'); 
	require_once(TEMPLATEPATH . '/includes/theme_functions.php');
	require_once(TEMPLATEPATH . '/includes/theme_woocommerce.php');
	require_once( TEMPLATEPATH . '/includes/custom_fields/functions.php' );
	require_once(TEMPLATEPATH . '/includes/sliders/functions.php');
	require_once(TEMPLATEPATH . '/includes/pixelstores/core.php');
?>
