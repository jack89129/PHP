<?php
//	Make theme compatible
	add_action( 'after_setup_theme', 'woo_setup' );
	if ( ! function_exists( 'woo_setup' ) ){
		function woo_setup() {
			add_theme_support( 'woocommerce' );
		}
	}
	
// 	Check WooCommerce is installed first
	add_action('wp_head', 'woostore_check_environment');
	function woostore_check_environment() {
		if (!class_exists('woocommerce')) wp_die(__('WooCommerce must be installed', 'oxfordshire')); 
	}
	
//	Disable WooCommerce styles 
	define('WOOCOMMERCE_USE_CSS', false);

	if(function_exists('add_theme_support')) {
		add_image_size('shop_feature_catalog', 145, 108, true);

	}	
	
//	WP-PageNavi Pagination
	if(function_exists('wp_pagenavi')) {
		remove_action('woocommerce_pagination', 'woocommerce_pagination', 10);
		function woocommerce_pagination() {
			wp_pagenavi(); 		
		}
		add_action( 'woocommerce_pagination', 'woocommerce_pagination', 10);
	}
	
//	Remove best sellers widget
	add_action( 'widgets_init', 'unregister_woo_best_sellers' );
	function unregister_woo_best_sellers() {
		unregister_widget( 'WooCommerce_Widget_Best_Sellers' );
	}

//	Default placeholder image
	add_filter('woocommerce_placeholder_img_src', 'viroshop_placeholder_img_src');
	function viroshop_placeholder_img_src( $src ) {
		$src = get_template_directory_uri() . '/images/placeholder.jpg';
		return $src;
	}

	
//	Display 3 upsell products
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	add_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_upsells', 15);
	if (!function_exists('woocommerce_output_upsells')) {
		function woocommerce_output_upsells() {
			woocommerce_upsell_display(3,3); // Display 3 products in rows of 3
		}
	}

//	Display related products
	function woocommerce_output_related_products() {
		global $pixelstore_mb_side_sidebar;
		$metasidebar = get_post_meta(get_the_ID(), $pixelstore_mb_side_sidebar->get_the_id(), TRUE);	
		if (($metasidebar[sidebar] == "No Sidebar")) { 
			woocommerce_related_products(4,4); 
		} else {
			woocommerce_related_products(3,3); 
		}
	}

//	Remove default WooCommerce shortcode placeholders
	function viroshop_remove_woo_shortcode_tiny() {
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) return;
		if ( get_user_option('rich_editing') == 'true') :
			remove_filter('mce_external_plugins', 'woocommerce_add_shortcode_tinymce_plugin');
			add_filter('mce_external_plugins', 'viroshop_add_shortcode_tinymce_plugin');
		endif;
	}

//	Define woo shortocode generator
	function viroshop_add_shortcode_tinymce_plugin($plugin_array) {
	global $woocommerce;
	$plugin_array['WooCommerceShortcodes'] = get_template_directory_uri() . '/assets/js/editor_plugin.js';
	return $plugin_array;
}
	add_action( 'init', 'viroshop_remove_woo_shortcode_tiny' );

//	Custom featured products
	function viroshop_featured_products( $atts ) {

		extract(shortcode_atts(array(
			'title' 	=> 'Featured Items',
			'per_page' 	=> '4',
			'columns' 	=> '4',
			'orderby' => 'date',
			'order' => 'desc'
		), $atts));
		global $secondloop;
		$args = array( 'post_type' => 'product', 'posts_per_page' => $per_page, 'orderby' => $orderby, 'order' => $order, 'meta_key' => '_featured', 'meta_value' => 'yes' );
		$secondloop = new WP_Query( $args );

		if ($secondloop->have_posts()) :
			$list = '	<div id="carousel" class="es-carousel-wrapper">
						<span class="slide-title">'. $title .'</span>
						<div class="es-carousel">
							<ul>';				
							while ( $secondloop->have_posts() ) : $secondloop->the_post(); global $product, $woocommerce;
								$list .= '<li><a href="' . get_permalink() . '">';
								if (has_post_thumbnail( $loop->post->ID )) {
									$list .= ''. get_the_post_thumbnail($secondloop->post->ID, 'shop_feature_catalog',array('title' => "")) .'</a></li>';	
								} else {
									$list .= '<img src="'.get_template_directory_uri().'/images/featured-placeholder.jpg" alt="Placeholder" width="145px" height="auto" />';
								}
							endwhile;
							wp_reset_query();
							return $list . 
						'</ul>
						</div>
				</div>';
		 endif;
	
	}
	add_shortcode('vs_featured_products', 'viroshop_featured_products');
	
	add_filter('add_to_cart_fragments', 'woocommerceframework_header_add_to_cart_fragment');	
	function woocommerceframework_header_add_to_cart_fragment( $fragments ) {
		global $woocommerce;
		ob_start();		
		?>
		<script type="text/javascript">
		jQuery(".shopping-bag").click(function(){
			window.location=jQuery(this).find("a").attr("href");
		 	return false;
		});		
		</script>
		<div class="shopping-bag">
			<div class="bag">
				<ul>
					<li><a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View Cart', 'pixelstores'); ?>"><?php echo $woocommerce->cart->get_cart_total(); ?></a></li>
					<li class="cart-info"><a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View Cart', 'pixelstores'); ?>"><?php _e('You have', 'pixelstores'); ?> <?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'pixelstores'), $woocommerce->cart->cart_contents_count);?></a></li>
				</ul>
			</div>
		</div>
		<?php	
		$fragments['.bag'] = ob_get_clean();		
		return $fragments;		
	}

//	New woo options upon theme activation	
	global $pagenow;
	if ( is_admin() && isset( $_GET['activated'] ) && $pagenow == 'themes.php' ) add_action('init', 'viroshop_update_woo_options', 1);	
	function viroshop_update_woo_options() {
	
	// Image sizes
	update_option( 'woocommerce_thumbnail_image_width', '100' ); // Image gallery thumbs
	update_option( 'woocommerce_thumbnail_image_height', '100' );
 
	update_option( 'woocommerce_shop_catalog_image_size_width', '440' ); // Product category thumbs
	update_option( 'woocommerce_shop_catalog_image_size_height', '328' );
	update_option( 'woocommerce_shop_catalog_width', '440' ); // Product category thumbs		

	update_option( 'shop_catalog_image_size', array('width' => '440','height' => '328') );
	update_option( 'shop_single_image_size', array('width' => '1030','height' => '800') );

	// Hard Crop [0 = false, 1 = true]
	update_option( 'woocommerce_thumbnail_image_crop', 0 );
	update_option( 'woocommerce_single_image_crop', 1 ); 
	update_option( 'woocommerce_catalog_image_crop', 1 );
	update_option( 'woocommerce_price_trim_zeros', 'no' );
	
	
	}
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );

	add_filter('loop_shop_per_page', create_function('$cols', 'return 9;'));
	$options = get_option('viroshop_theme_options'); 
	if ($options['viro_price_display'] == "") {	
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	}

	if ($options['viro_product_summary_display'] == "") {	
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	}


//  Adjust markup on all WooCommerce pages
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
	
	add_action( 'woocommerce_before_main_content', 'viroshop_before_content', 10 );
	add_action( 'woocommerce_after_main_content', 'viroshop_after_content', 20 );
	add_action('woocommerce_before_main_content', create_function('', 'echo "<div class=\"inner-container\">";'), 30);
	
// 	Fix the layout etc
	if (!function_exists('viroshop_before_content')) {
		function viroshop_before_content() {
		?>
		<section id="wrapper" class="container clearfix">
			<?php get_sidebar(); ?>
			<section id="content">
		<?php
		}
	}
	
	if (!function_exists('viroshop_after_content')) {
		function viroshop_after_content() {
		?>
			</div>
			</section>
			</section>
			<?php
		}
	}
	
// 	Column loops to 3
global $woocommerce_loop;
$woocommerce_loop['columns'] = 3;

add_filter ( 'woocommerce_product_thumbnails_columns', 'thumbnail_columns' );
 function thumbnail_columns() {
     return 4; //Change the 5 to reflect how many columns you need
 }

?>