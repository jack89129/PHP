<?php
function example_customizer_admin() {
	add_theme_page( 'Customize', 'Customizer', 'edit_theme_options', 'customize.php' ); 	
	}
add_action ('admin_menu', 'example_customizer_admin');

function themename_customize_register($wp_customize){
	$bg_patterns = array(
			'None' => 'None',
			'retina_wood.png' => 'wood',
			'dark_dots.jpg' => 'darkdots',
			'use_your_illusion.png' => 'useyourillusion',
			'vintage_floral.gif' => 'vintagefloral',
			'low_contrast_linen.png' => 'lowcontrastlinen',
			'ruby-sundial.gif' => 'rubysundial',
			'renzler.gif' => 'renzler',
			'renzler-power-down.gif' => 'renzlerpowerdown',
			'subtle-wood.gif' => 'subtlewoodpanels',
			'charcoal.gif' => 'charcoal',
			'light-tile.gif' => 'lighttile',
			'dark-tile.gif' => 'darktile', 
			'ridge.gif' => 'ridge',
			'violet-fabric.gif' => 'violetfabric',
			'blueprint.gif' => 'blueprint',
			'peach-swirl.gif' => 'peachswirl',
			'skippy.gif' => 'skippy',
			'techno.gif' => 'techno',
			'frozen.gif' => 'frozen',
			'waves.gif' => 'waves', 
			'salem.gif' => 'salem', 
			'grunge.gif' => 'bluegrunge',
			'karachi.gif' => 'karachi', 
			'prestige-COD.gif' => 'prestigecod', 
			'chocolate-diamond.gif' => 'chocolatediamond', 
			'sapphire.gif' => 'sapphire',
			'subliminal.gif' => 'subliminal', 
			'intertwined-darkened.gif' => 'intertwineddarkened',
			'animus-mix.gif' => 'animusmix',
			'plum.gif' => 'plum',
			'paper-flower.gif' => 'paperflower',
			'ghost-tile.gif' => 'ghosttile',
			'dinpattern-stripe.gif' => 'stripe', 
			'bones.gif' => 'bones',
			'tread.gif' => 'tread', 
			'khaki.gif' => 'khaki', 
			'floral.jpg' => 'floral', 
			'brownfloral.jpg' => 'brownfloral', 
			'dark_Tire.png' => 'darktire',
			'purty_wood.png' => 'purtywood', 
			'Holes.png' => 'holes',
			'tasky_pattern.png' => 'tasky', 
			'pinstriped_suit.png' => 'pinstripedsuit', 
			'tileable_wood_texture.png' => 'tileablewood', 
			'classy_fabric.png' => 'classyfabric',
		 	'dark_geometric.png' => 'darkgeometric',
			'hixs_pattern_evolution.png' => 'hixsevolution',
			'darkdenim3.png' => 'darkdenim',
			'irongrip.png' => 'iongrip',
			'starring.png' => 'starring'
		); 	
	$color_schemes = array(
			'Custom' => 'Custom',
			'Silver' => 'Silver',
			'Black'  => 'Black',
			'Orange' => 'Orange',
			'Green'  => 'Green',
			'Pink'   => 'Pink',
            'Red'    => 'Red'
		); 	
		
 //	Background Section
	$wp_customize->add_section('viroshop_color_scheme', array(
		'title'    => __('Color Scheme', 'pixelstores'),
		'priority' => 15,
    )); 
    
      
//	Header Section
	$wp_customize->add_section('viroshop_header', array(
		'title'    => __('Header', 'pixelstores'),
		'priority' => 20,
    )); 

//	Background Section
	$wp_customize->add_section('viroshop_background', array(
		'title'    => __('Background', 'pixelstores'),
		'priority' => 25,
    )); 

//	Logo Section    		
	$wp_customize->add_section('viroshop_logo', array(
		'title'    => __('Logo', 'pixelstores'),
		'priority' => 35,
    ));    

//	Cart Section    		
	$wp_customize->add_section('viroshop_cart', array(
		'title'    => __('Shopping Cart', 'pixelstores'),
		'priority' => 45,
    ));    

//	Menu Section    		
	$wp_customize->add_section('viroshop_menu', array(
		'title'    => __('Menu', 'pixelstores'),
		'priority' => 110,
    ));
    
//	Price Tag Section    		
	$wp_customize->add_section('viroshop_price_tag', array(
		'title'    => __('Price Tag', 'pixelstores'),
		'priority' => 110,
    ));    

//	Sidebar Section    		
	$wp_customize->add_section('viroshop_sidebar', array(
		'title'    => __('Sidebar', 'pixelstores'),
		'priority' => 115,
    ));        

//	Catalogue Section    		
	$wp_customize->add_section('viroshop_catalogue', array(
		'title'    => __('Catalogue', 'pixelstores'),
		'priority' => 120,
    ));        

//  =============================
//  Color Schemes Section       =
//  ============================= 
    
//	Color Schemes
    $wp_customize->add_setting('viroshop_theme_options[viro_color_scheme]', array(
        'default'        => 'Custom',
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
    ));    	
 	$wp_customize->add_control( 'color_schemes', array(
        'settings' => 'viroshop_theme_options[viro_color_scheme]',
        'label'    => __('Colour Scheme', 'pixelstores'),
        'section' => 'viroshop_color_scheme',
        'type'    => 'select',
        'choices'    => $color_schemes,
    )); 
        
//  =============================
//  Header Section          	=
//  ============================= 
    
//	Top Margin    
    $wp_customize->add_setting('viroshop_theme_options[viro_top_margin]', array(
        'default'        => '55px',
        'capability'     => 'edit_theme_options',
        'type'           => 'option',

    ));
    $wp_customize->add_control('header_top_margin', array(
        'label'      => __('Top Margin', 'pixelstores'),
        'section'    => 'viroshop_header',
        'settings'   => 'viroshop_theme_options[viro_top_margin]',
    ));

//	Bottom Margin    
    $wp_customize->add_setting('viroshop_theme_options[viro_bottom_margin]', array(
        'default'        => '50px',
        'capability'     => 'edit_theme_options',
        'type'           => 'option',

    ));
    $wp_customize->add_control('header_bottom_margin', array(
        'label'      => __('Bottom Margin', 'pixelstores'),
        'section'    => 'viroshop_header',
        'settings'   => 'viroshop_theme_options[viro_bottom_margin]',
    ));   

//  =============================
//  Background Section          =
//  ============================= 
    
//	Background Patterns 
    $wp_customize->add_setting('viroshop_theme_options[viro_background_pattern]', array(
        'default'        => 'None',
        'capability'     => 'edit_theme_options',
        'type'           => 'option',
    ));    	
 	$wp_customize->add_control( 'background_patterns', array(
        'settings' => 'viroshop_theme_options[viro_background_pattern]',
        'label'    => __('Background Pattern', 'pixelstores'),
        'section' => 'viroshop_background',
        'type'    => 'select',
        'choices'    => $bg_patterns,
    ));   

//	Background Image    
    $wp_customize->add_setting('viroshop_theme_options[viro_background_image]', array(
        'default'		=>  get_template_directory_uri() . '/images/bg.jpg',
        'capability'	=> 'edit_theme_options',
        'type'			=> 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'viro_background_image', array(
        'label'    => __('Background Image', 'themename'),
        'section'  => 'viroshop_background',
        'settings' => 'viroshop_theme_options[viro_background_image]',
    )));    

// 	Background Color       
    $wp_customize->add_setting('viroshop_theme_options[viro_background_color]', array(
        'default'           => '#404040',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_background_color', array(
        'label'    => __('Background Color', 'pixelstores'),
        'section'  => 'viroshop_background',
        'settings' => 'viroshop_theme_options[viro_background_color]',
    )));

//	Background Firefly
    $wp_customize->add_setting('viroshop_theme_options[viro_firefly]', array(
        'default'        => 'checked',
        'capability' => 'edit_theme_options',
        'type'       => 'option',
    )); 
    $wp_customize->add_control('firefly', array(
    
        'settings' => 'viroshop_theme_options[viro_firefly]',
        'label'    => __('Firefly', 'pixelstores'),
        'section'  => 'viroshop_background',
        'type'     => 'checkbox',
    ));

//  =============================
//  Logo Section          		=
//  ============================= 

//	Logo
    $wp_customize->add_setting('viroshop_theme_options[viro_logo]', array(
        'default'		=>  get_template_directory_uri() . '/images/logo.png',
        'capability'	=> 'edit_theme_options',
        'type'			=> 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'viro_logo', array(
        'label'    => __('Upload Logo', 'themename'),
        'section'  => 'viroshop_logo',
        'settings' => 'viroshop_theme_options[viro_logo]',
    )));
    
//  =============================
//  Shopping Cart          		=
//  ============================= 

//	Display Cart
    $wp_customize->add_setting('viroshop_theme_options[viro_cart]', array(
        'default'        => 'checked',
        'capability' => 'edit_theme_options',
        'type'       => 'option',
    )); 
    $wp_customize->add_control('shooping_cart', array( 
        'settings' => 'viroshop_theme_options[viro_cart]',
        'label'    => __('Display Cart', 'pixelstores'),
        'section'  => 'viroshop_cart',
        'type'     => 'checkbox',
    )); 
    
// 	Total Color       
    $wp_customize->add_setting('viroshop_theme_options[viro_cart_total]', array(
        'default'           => '#dbdbdb',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_cart_total_color', array(
        'label'    => __('Total Color', 'pixelstores'),
        'section'  => 'viroshop_cart',
        'settings' => 'viroshop_theme_options[viro_cart_total]',
    )));  

// 	Total Dropshadow       
    $wp_customize->add_setting('viroshop_theme_options[viro_cart_total_shadow]', array(
        'default'           => '#272727',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_cart_total_color_shadow', array(
        'label'    => __('Total Text Shadow', 'pixelstores'),
        'section'  => 'viroshop_cart',
        'settings' => 'viroshop_theme_options[viro_cart_total_shadow]',
    )));  

// 	Cart info      
    $wp_customize->add_setting('viroshop_theme_options[viro_cart_info]', array(
        'default'           => '#adadad',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_cart_info_color', array(
        'label'    => __('Cart Info Color', 'pixelstores'),
        'section'  => 'viroshop_cart',
        'settings' => 'viroshop_theme_options[viro_cart_info]',
    )));  
    
//  =============================
//  Menu		          		=
//  ============================= 
    
//	Search Box
    $wp_customize->add_setting('viroshop_theme_options[viro_search_box]', array(
        'default'        => 'checked',
        'capability' => 'edit_theme_options',
        'type'       => 'option',
    )); 
    $wp_customize->add_control('search_box', array(
    
        'settings' => 'viroshop_theme_options[viro_search_box]',
        'label'    => __('Search Box', 'pixelstores'),
        'section'  => 'viroshop_menu',
        'type'     => 'checkbox',
    )); 
    
//  =============================
//  Price Tag	          		=
//  ============================= 

// 	Price Tag Colour       
    $wp_customize->add_setting('viroshop_theme_options[viro_price_color]', array(
        'default'           => '#fff',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_price_tag_color', array(
        'label'    => __('Text Color', 'pixelstores'),
        'section'  => 'viroshop_price_tag',
        'settings' => 'viroshop_theme_options[viro_price_color]',
    )));   

// 	Price Text Shadow      
    $wp_customize->add_setting('viroshop_theme_options[viro_price_shadow]', array(
        'default'           => '#4c5e64',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_price_tag_shadow', array(
        'label'    => __('Text Shadow', 'pixelstores'),
        'section'  => 'viroshop_price_tag',
        'settings' => 'viroshop_theme_options[viro_price_shadow]',
    ))); 

// 	Price Tag Top Gradient       
    $wp_customize->add_setting('viroshop_theme_options[viro_price_top_color]', array(
        'default'           => '#6a8088',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_price_top_gradient', array(
        'label'    => __('Top Gradient', 'pixelstores'),
        'section'  => 'viroshop_price_tag',
        'settings' => 'viroshop_theme_options[viro_price_top_color]',
    )));  

// 	Price Tag Bottom Gradient       
    $wp_customize->add_setting('viroshop_theme_options[viro_price_bottom_color]', array(
        'default'           => '#5e7279',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_price_bottom_gradient', array(
        'label'    => __('Bottom Gradient', 'pixelstores'),
        'section'  => 'viroshop_price_tag',
        'settings' => 'viroshop_theme_options[viro_price_bottom_color]',
    )));    

// 	Price Tag Corner       
    $wp_customize->add_setting('viroshop_theme_options[viro_price_corner_color]', array(
        'default'           => '#49595e',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_price_corner', array(
        'label'    => __('Corner Color', 'pixelstores'),
        'section'  => 'viroshop_price_tag',
        'settings' => 'viroshop_theme_options[viro_price_corner_color]',
    )));   


//  =============================
//  Sidebar	          			=
//  ============================= 

// 	Sidebar Title Color     
    $wp_customize->add_setting('viroshop_theme_options[viro_sidebar_color]', array(
        'default'           => '#667b83',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_sidebar_title_color', array(
        'label'    => __('Title Color', 'pixelstores'),
        'section'  => 'viroshop_sidebar',
        'settings' => 'viroshop_theme_options[viro_sidebar_color]',
    )));  

// 	Sidebar Link Color     
    $wp_customize->add_setting('viroshop_theme_options[viro_sidebar_link_color]', array(
        'default'           => '#919191',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_sidebar_link_url_color', array(
        'label'    => __('Link Color', 'pixelstores'),
        'section'  => 'viroshop_sidebar',
        'settings' => 'viroshop_theme_options[viro_sidebar_link_color]',
    ))); 

// 	Sidebar Link Hover Color 
    $wp_customize->add_setting('viroshop_theme_options[viro_sidebar_link_color_hover]', array(
        'default'           => '#667b83',
        'sanitize_callback' => 'sanitize_hex_color',
        'capability'        => 'edit_theme_options',
        'type'           => 'option',
    ));
    $wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'viro_sidebar_link_url_color_hover', array(
        'label'    => __('Link Hover Color', 'pixelstores'),
        'section'  => 'viroshop_sidebar',
        'settings' => 'viroshop_theme_options[viro_sidebar_link_color_hover]',
    ))); 

//  =============================
//  Catalogue		          	=
//  ============================= 
    
//	Prices
    $wp_customize->add_setting('viroshop_theme_options[viro_price_display]', array(
        'default'        => 'checked',
        'capability' => 'edit_theme_options',
        'type'       => 'option',
    )); 
    $wp_customize->add_control('viro_price_display', array(
    
        'settings' => 'viroshop_theme_options[viro_price_display]',
        'label'    => __('Display Price Tags', 'pixelstores'),
        'section'  => 'viroshop_catalogue',
        'type'     => 'checkbox',
    )); 
 
 //	Product Summary
    $wp_customize->add_setting('viroshop_theme_options[viro_product_summary_display]', array(
        'default'        => 'checked',
        'capability' => 'edit_theme_options',
        'type'       => 'option',
    )); 
    $wp_customize->add_control('viro_product_summary_display', array(
    
        'settings' => 'viroshop_theme_options[viro_product_summary_display]',
        'label'    => __('Display Product Summary', 'pixelstores'),
        'section'  => 'viroshop_catalogue',
        'type'     => 'checkbox',
    ));    
}

add_action('customize_register', 'themename_customize_register');


function virohop_styles() {
    wp_enqueue_style('custom-style', get_template_directory_uri() . '/css/skin.css');

	$options = get_option('viroshop_theme_options');

	switch ($options['viro_color_scheme'])
	{
		case "Silver":
			wp_enqueue_style('silver_color_scheme', get_template_directory_uri() . '/css/skins/silver.css');
		  break;
		case "Black":
			wp_enqueue_style('black_color_scheme', get_template_directory_uri() . '/css/skins/black.css');
		  break;
		case "Orange":
			wp_enqueue_style('orange_color_scheme', get_template_directory_uri() . '/css/skins/orange.css');
		  break;
		case "Green":
			wp_enqueue_style('green_color_scheme', get_template_directory_uri() . '/css/skins/green.css');
		  break;
		case "Pink":
			wp_enqueue_style('pink_color_scheme', get_template_directory_uri() . '/css/skins/pink.css');
		  break;
        case "Red":
            wp_enqueue_style('pink_color_scheme', get_template_directory_uri() . '/css/skins/red.css');
          break;
		default:
	}
	
	
	if ($options['viro_background_image']) {
		$custom_css .= "body {
							background: $options[viro_background_color] url($options[viro_background_image]) fixed no-repeat;
						}";
	} else {
		$custom_css .= "body {
							background: $options[viro_background_color] url( ". get_template_directory_uri() . "/images/patterns/$options[viro_background_pattern]) fixed no-repeat;
						}";
	}
		
	$custom_css .= "
					.woocommerce .quantity .plus,.woocommerce-page .quantity .plus,.woocommerce #content .quantity .plus,.woocommerce-page #content .quantity .plus,.woocommerce .quantity .minus,.woocommerce-page .quantity .minus,.woocommerce #content .quantity .minus,.woocommerce-page #content .quantity .minus {
						border: 1px solid $options[viro_price_bottom_color];
						background: $options[viro_price_bottom_color];
						background: -webkit-gradient(linear,left top,left bottom,from($options[viro_price_top_color]),to($options[viro_price_bottom_color]));
						background: -webkit-linear-gradient($options[viro_price_top_color],$options[viro_price_bottom_color]);
						background: -moz-linear-gradient(center top,$options[viro_price_top_color] 0%,$options[viro_price_bottom_color] 100%);
						background: -moz-gradient(center top,$options[viro_price_top_color] 0%,$options[viro_price_bottom_color] 100%);
					}
					.woocommerce #content .quantity .plus:hover, .woocommerce #content .quantity .minus:hover {
						background: $options[viro_price_bottom_color];

					}
					.woocommerce a.button.alt:hover,.woocommerce-page a.button.alt:hover,.woocommerce button.button.alt:hover,.woocommerce-page button.button.alt:hover,.woocommerce input.button.alt:hover,.woocommerce-page input.button.alt:hover,.woocommerce #respond input#submit.alt:hover,.woocommerce-page #respond input#submit.alt:hover,.woocommerce #content input.button.alt:hover,.woocommerce-page #content input.button.alt:hover {
						background-color: $options[viro_price_top_color] !important;
					}
					.woocommerce a.button.alt,.woocommerce-page a.button.alt,.woocommerce button.button.alt,.woocommerce-page button.button.alt,.woocommerce input.button.alt,.woocommerce-page input.button.alt,.woocommerce #respond input#submit.alt,.woocommerce-page #respond input#submit.alt,.woocommerce #content input.button.alt,.woocommerce-page #content input.button.alt {
						background-color: $options[viro_price_bottom_color] !important;
					}
					#header {
                   		margin-top: $options[viro_top_margin];
						margin-bottom: $options[viro_bottom_margin];
                   }            
                   .es-carousel-wrapper .slide-title, #content ul.products li .price {
						color: $options[viro_price_color];
						background: $options[viro_price_bottom_color];
						filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='$options[viro_price_top_color]', endColorstr='$options[viro_price_bottom_color]');
						background: -webkit-gradient(linear, left top, left bottom, from($options[viro_price_top_color]), to($options[viro_price_bottom_color]));
						background: -moz-linear-gradient(top, $options[viro_price_top_color], $options[viro_price_bottom_color]);
						text-shadow: $options[viro_price_shadow] 1px 1px 0;
					}					
					#content ul.products li .price:after {
						border-color: $options[viro_price_corner_color] transparent transparent $options[viro_price_corner_color];
					}
					.es-carousel-wrapper .slide-title:after {
						border-color: $options[viro_price_corner_color] $options[viro_price_corner_color] transparent transparent;
					}
					
					div.product p.price:before {
						border-color: transparent transparent transparent $options[viro_price_bottom_color];
					}
					div.product p.price {
						color: $options[viro_price_color];
						background-color: $options[viro_price_bottom_color];
					}
					ul.products li .price ins, div.product p.price ins, div.product p.price del {
						color: $options[viro_price_color];
					}
					.widgettitle {
						color: $options[viro_sidebar_color];
					}
					.widget ul li a {
						color: $options[viro_sidebar_link_color];
					}		
					.widget ul li a:hover {
						color: $options[viro_sidebar_link_color_hover];
					}	
					.bag .amount {
						color: $options[viro_cart_total];
						text-shadow: $options[viro_cart_total_shadow] 1px 1px 1px;
					}
					.bag .cart-info a {
						color: $options[viro_cart_info];
					}";
					
	if ($options['viro_top_margin']) {
    	wp_add_inline_style('custom-style',$custom_css);
    }
}
add_action('wp_enqueue_scripts', 'virohop_styles');
