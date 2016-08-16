<?php
//	Define image sizes
	add_image_size( 'home-slider', 950, 450, true ); 	// Homepage slider
	add_image_size( 'page-slider', 695, 325, true ); 	// Page slider
	add_image_size( 'blog-image', 606, 9999 );		// Single blog image

//	Remove junk from head
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'feed_links', 2);
	remove_action('wp_head', 'index_rel_link');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'feed_links_extra', 3);
	remove_action('wp_head', 'start_post_rel_link', 10, 0);
	remove_action('wp_head', 'parent_post_rel_link', 10, 0);
	remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);

	
// Unregister Widgets
	function unregister_default_wp_widgets() {
		unregister_widget('WP_Widget_Pages');
		unregister_widget('WP_Widget_Calendar');
		unregister_widget('WP_Widget_Archives');
		unregister_widget('WP_Widget_Links');
		unregister_widget('WP_Widget_Meta');
		unregister_widget('WP_Widget_Search');
		unregister_widget('WP_Widget_Categories');
		unregister_widget('WP_Widget_Recent_Comments');
		unregister_widget('WP_Widget_RSS');
		unregister_widget('WP_Widget_Tag_Cloud');
	}
	add_action('widgets_init', 'unregister_default_wp_widgets', 1);

//	Load languages
	add_action('after_setup_theme', 'viroshop_setup');
	function viroshop_setup() {
		load_theme_textdomain('pixelstores', get_template_directory() . '/assets/lang');
    	add_theme_support( 'automatic-feed-links' );
	}
	
//	Flush rules
	if ( is_admin() && isset($_GET['activated'] ) && $pagenow == 'themes.php' ) {
		$wp_rewrite->flush_rules();
	}

//	Add body class for no sidebar
	function no_sidebar_body_class($classes){
		global $pixelstore_mb_side_sidebar;
		$metasidebar = get_post_meta(get_the_ID(), $pixelstore_mb_side_sidebar->get_the_id(), TRUE);	
		if (is_product()) { if (($metasidebar[sidebar] == "No Sidebar")) { $myvar = "no-sidebar"; } }
		global $post;
		array_push($classes, $myvar);
		return $classes;
	}
	add_filter('body_class', 'no_sidebar_body_class');
	
// 	Register navigation menus
	function register_main_menus() {
		register_nav_menus(
			array(
				'primary' => __( 'Header Navigation' )
			)
		);
	}
	if (function_exists('register_nav_menus')) add_action( 'init', 'register_main_menus' );

// 	Default menu
	function viroshop_default_menu() {
		$url = admin_url( 'nav-menus.php' );
		echo '<li><a href="'. get_option('home') . '/">'. __('Home', 'pixelstores') .'</a></li>';
		echo '<li><a href="'. $url . '">'. __('Set Up Your Menu', 'pixelstores') .'</a></li>';	
	}

// 	Add navigation menu class	
	function add_menu_class($output) {
		$output= preg_replace('/menu-item/', 'first-menu-item menu-item', $output, 1);
		$output=substr_replace($output, "last-menu-item menu-item", strripos($output, "menu-item"), strlen("menu-item"));
		return $output;
	}
	add_filter('wp_nav_menu', 'add_menu_class');

//	Add search bar to menu
	function add_search_to_wp_menu ( $items, $args ) {
		$options = get_option('viroshop_theme_options');
		if($options['viro_search_box'] && 'primary' === $args -> theme_location ) {
			$search_text = __( 'Search', 'pixelstores' );
			$items .= '<li class="search">';
			$items .= '<form method="get" class="menu-search-form" action="' . get_bloginfo('home') . '/"><p><input class="text_input" type="text" value="'.$search_text.'" name="s" id="s" onfocus="if (this.value == \''.$search_text.'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''.$search_text.'\';}" /><input type="submit" class="my-wp-search" id="searchsubmit" value="'.$search_text.'" /><input type="hidden" name="post_type" value="product" /></p></form>';
			$items .= '</li>';
		}
		return $items;
		
	}
	add_filter('wp_nav_menu_items','add_search_to_wp_menu',10,2);

//	Custom menu walker
	class clean_walker extends Walker_Nav_Menu {
      function start_el(&$output, $item, $depth, $args)
      {
           global $wp_query;
           $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

           $class_names = $value = '';

           $classes = empty( $item->classes ) ? array() : (array) $item->classes;
           
                $current_indicators = array('current-menu-parent', 'current_page_item', 'current_page_parent');
        
                        $newClasses = array();
                        
                        foreach($classes as $el){
                                //check if it's indicating the current page, otherwise we don't need the class
                                if (in_array($el, $current_indicators)){ 
                                        array_push($newClasses, $el);
                                }
                        }


           $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
           $class_names = ' class="'. esc_attr( $class_names ) .'"';
           $output .= $indent . '<li' . $class_names .'>';


           $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
           $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
           $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
           $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

           if($depth != 0)
           {
                     //children stuff
           }

            $item_output = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $args->link_before .apply_filters( 'the_title', $item->title, $item->ID );
            $item_output .= '</a>';
            $item_output .= $args->after;

            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
            }
}  

//	Custom blog post breadcrumb
	function blog_breadcrumb() {
		$pages = get_pages(array(
    		'meta_key' => '_wp_page_template',
    		'meta_value' => 'blog.php',
    		'hierarchical' => 0
		));
		if(of_get_option('viro_breadcrumbs')) { 
			echo '<div id="breadcrumb">';
			echo '<a class="home" href="'. home_url() . '/">'. __('Home', 'pixelstores') .'</a> &rsaquo; ';	
			foreach($pages as $page){
				echo '<a class="home" href="'. get_page_link( $page->ID ) . '/">'. __('Blog', 'pixelstores') .'</a> &rsaquo; ';	
			}
			echo ''. the_category(' &rsaquo; ') .'</div>';	
		}
	}	

//	Blog attacchment image
	function blog_image() {
		global $_wp_additional_image_sizes;
		if(has_post_thumbnail()) { 
			echo '<figure class="image-wrapper"><a href="'.get_permalink().'">';
				the_post_thumbnail('blog-image');
			echo '</a></figure>';
		
		}
	
	}	
	
	add_action('media_buttons','add_sc_select',11);
	function add_sc_select(){
    global $shortcode_tags;
     /* ------------------------------------- */
     /* enter names of shortcode to exclude bellow */
     /* ------------------------------------- */
    $exclude = array("wp_caption", "contact", "embed");
    echo '&nbsp;<select id="sc_select"><option>Shortcode</option>';
    foreach ($shortcode_tags as $key => $val){
	    if(!in_array($key,$exclude)){
		if(substr($key, 0, 4) === 'scg_'){
	            $shortcodes_list .= '<option value="['.$key.']">'.$key.'</option>';
		}
		else{
	            $shortcodes_list .= '<option value="['.$key.'][/'.$key.']">'.$key.'</option>';
		}
    	    }
        }
     echo $shortcodes_list;
     echo '</select>';
}
	add_action('admin_head', 'button_js');
	function button_js() {
	echo '<script type="text/javascript">
	jQuery(document).ready(function(){
	   jQuery("#sc_select").change(function() {
			  send_to_editor(jQuery("#sc_select :selected").val());
        		  return false;
		});
	});
	</script>';
}

 ?>