<?php global $woocommerce; $options = get_option('viroshop_theme_options'); ?>
<!DOCTYPE html>
<!--[if IE 7]>                  <html class="ie7 no-js" lang="en">     <![endif]-->
<!--[if lte IE 8]>              <html class="ie8 no-js" lang="en">     <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--> <html class="not-ie no-js" lang="en">  <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>

	<!--[if !lte IE 6]><!-->
		<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />
	<!--<![endif]-->

	<!--[if lte IE 6]>
		<link rel="stylesheet" href="http://universal-ie6-css.googlecode.com/files/ie6.1.1.css" media="screen, projection">
	<![endif]-->
	
	<!--[if lt IE 9]>
    	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

	<?php wp_head(); ?>
</head>
<body id="shop" <?php body_class(); ?>>

<header id="header" class="container clearfix">
	<a href="<?php bloginfo('url'); ?>"><?php $logo = ($options['viro_logo'] <> '') ? $options['viro_logo'] : get_bloginfo('template_directory').'/images/logo.png'; ?><img src="<?php echo $logo; ?>" alt="<?php bloginfo('name'); ?>" id="logo"/></a>

	<?php if($options['viro_cart']) { ?>
	<div class="shopping-bag">
		<div class="bag">
			<ul>
				<li><a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View Cart', 'pixelstores'); ?>"><?php echo $woocommerce->cart->get_cart_total(); ?></a></li>
				<li class="cart-info"><a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View Cart', 'pixelstores'); ?>"><?php _e('You have', 'pixelstores'); ?> <?php echo sprintf(_n('%d item', '%d items', $woocommerce->cart->cart_contents_count, 'pixelstores'), $woocommerce->cart->cart_contents_count);?></a></li>
			</ul>
		</div>
	</div>
	<?php } ?>
<script type="text/javascript">                                       
         jQuery(document).ready(function($) {
            var w = parseInt(jQuery(window).width());    
            var h = parseInt(jQuery(window).height()); 
            jQuery('body').css('background-size', w+"px "+h+"px");
        });
        jQuery(window).resize(function() {
            var w = parseInt(jQuery(window).width());    
            var h = parseInt(jQuery(window).height()); 
            jQuery('body').css('background-size', w+"px "+h+"px");
        });
    </script>
</header>
        
<nav id="menu" class="container clearfix">	
	<ul class="sf-menu">
		<!--The primary menu  -->
		<?php wp_nav_menu( array( 'sort_column' => 'menu_order', 'container' => '', 'items_wrap' => '%3$s', 'walker' => new clean_walker(), 'container_id' => 'main-nav', 'theme_location' => 'primary', 'fallback_cb' => 'viroshop_default_menu')); ?>
	</ul>
</nav>