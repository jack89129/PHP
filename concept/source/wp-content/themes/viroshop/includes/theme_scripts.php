<?php

//	JS Scripts	
	function viroshop_scripts() {
		global $pixelstore_mb, $pixelstore_mb_side;
		$sidemeta = get_post_meta(get_the_ID(), $pixelstore_mb_side->get_the_id(), TRUE);
		$options = get_option('viroshop_theme_options');
		$script_folder = get_template_directory_uri() . '/assets/js/';	
		if ( class_exists( 'woocommerce' ) ) { if(!is_product()) { $single_product = "not_product";	} }
		if (!is_admin()) {		
			wp_enqueue_script('jquery');
			wp_enqueue_script( 'vs_custom', $script_folder . 'jquery.custom.js', 'jquery', '1.0'); 
			wp_localize_script( 'vs_custom', 'vs_ajax', array( 'spark' => get_template_directory_uri() . '/assets/images/spark1.png', 'secondspark' => get_template_directory_uri() . '/assets/images/spark2.png', 'firefly' => $options['viro_firefly'], 'autoplay_featured' => of_get_option('viro_featured_auto'), 'scroll_featured' => of_get_option('viro_featured_scroll'), 'single_product' => $single_product ) );	
			wp_enqueue_script('select', $script_folder . 'jquery.selectBox.min.js');
			wp_enqueue_script('superfish', $script_folder . 'superfish.min.js');
			wp_enqueue_script('respond', $script_folder . 'respond.min.js');
			wp_enqueue_script('jeasing', $script_folder . 'jquery.easing.1.3.js');
			wp_enqueue_script('elastislide', $script_folder . 'jquery.elastislide.js');	
			wp_enqueue_script('googleplusone', 'http://apis.google.com/js/plusone.js');	
	
			if($options['viro_firefly']) {
				wp_enqueue_script('firefly', $script_folder . 'jquery.firefly.min.js');
			}			
			if(!empty($sidemeta[bgimgurl])) {
				wp_enqueue_script('flexbg', $script_folder . 'flexi-background.js');
			}
		}	
	}
	add_action('wp_enqueue_scripts', 'viroshop_scripts');

//	CSS Styles	
	function viroshop_styles() {
		if (!is_admin()) {
			wp_enqueue_style('woomod', get_template_directory_uri(). '/css/woocommerce-mod.css');
			if(of_get_option('viro_responsive')) {
				wp_enqueue_style('responsivecss', get_template_directory_uri(). '/css/responsive.css');
			}
			if(of_get_option('viro_menu_css')) {
				wp_enqueue_style('menuicons', get_template_directory_uri(). '/css/menu-icons.css');
			}
			wp_enqueue_style('googlefont', 'http://fonts.googleapis.com/css?family=PT+Sans');
			wp_enqueue_style('nivoslidercss', get_template_directory_uri(). '/includes/sliders/nivoslider/nivo-slider.css');
		}
	}
	add_action('wp_enqueue_scripts', 'viroshop_styles');

//	JS Nivoslider
	function nivoslider_scripts() {
		global $post, $pixelstore_mb;
		$meta = get_post_meta(get_the_ID(), $pixelstore_mb->get_the_id(), TRUE);		
		if (!is_admin() && !has_post_thumbnail() && $meta[slider] == "NivoSlider") {
			wp_enqueue_script( 'vs_nivoslider', get_template_directory_uri() . '/includes/sliders/nivoslider/jquery.nivosettings.js', array( 'jquery' ) ); 
			wp_localize_script('vs_nivoslider','nivo',
				array(
					'effect' => $meta[effect],
					'slices' => $meta[slices],
					'boxRows' => $meta[boxRows],
					'pauseTime' => $meta[pauseTime],
					'startSlide' => $meta[startSlide],
					'directionNav' => $meta[directionNav],
					'directionNavHide' => $meta[directionNavHide],
					'pauseOnHover' => $meta[pauseOnHover],
					'manualAdvance' => $meta[manualAdvance],
					'controlNav' => $meta[controlNav],
					'randomStart' => $meta[randomStart]
				)
			);
			wp_enqueue_script('nivoslider', get_template_directory_uri() . '/includes/sliders/nivoslider/jquery.nivo.slider.pack.js');
		}
	}	
	add_action( 'wp_enqueue_scripts', 'nivoslider_scripts' );
?>