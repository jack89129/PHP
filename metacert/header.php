<!doctype html>
<html class="no-js" <?php language_attributes(); ?> >
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="description" content="<?php echo bloginfo('description'); ?>" />
		<meta name="keywords" content="METACERT,API,Mobile" />
		<meta property="og:type" content="website" />
		<meta property="og:url" content="<?php echo home_url(); ?>" />
		<meta property="og:title" content="<?php bloginfo( 'name' ); ?>" />
		<meta property="og:image" content="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/og-image.png" />
		<meta property="og:image:secure_url" content="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/og-image.png" />
		<meta property="og:description" content="<?php echo bloginfo('description'); ?>" />
		<meta property="twitter:site" content="<?php echo home_url(); ?>" />
		<meta property="twitter:title" content="<?php bloginfo( 'name' ); ?>" />
		<meta property="twitter:image:src" content="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/og-image.png" />
		<meta property="twitter:description" content="<?php echo bloginfo('description'); ?>" />
		<title>
            <?php bloginfo( 'name' ); //echo " | "; bloginfo('description'); ?>
        </title>
		
		<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ; ?>/css/app.css" />
		<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri() ; ?>/css/custom.css" />
		
		<link rel="icon" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/favicon.ico" type="image/x-icon">
		<link rel="icon" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/myIcon.ico" type="image/x-icon">
		<link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-144x144-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-114x114-precomposed.png">
		<link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-72x72-precomposed.png">
		<link rel="apple-touch-icon-precomposed" href="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/icons/apple-touch-icon-precomposed.png">
		<!--<script src="//use.typekit.net/ukr4odj.js"></script>
        <script>try{Typekit.load();}catch(e){}</script>-->
		<?php wp_head(); ?>

<?php /*
    <script type='text/javascript'>
var $zoho= $zoho || {salesiq:{values:{},ready:function(){$zoho.salesiq.floatbutton.visible('show');}}}; var d=document; s=d.createElement('script'); s.type='text/javascript'; s.defer=true; s.src='https://salesiq.zoho.com/metacert/float.ls?embedname=metacert'; t=d.getElementsByTagName('script')[0]; t.parentNode.insertBefore(s,t);
</script>
*/
?>

	</head>

<div id='chat-center-widget' data-color='#666666' data-chat-id='/metacert'></div> <script>(function(d,s){var js,cjs=d.getElementsByTagName(s)[0];js=d.createElement(s); js.src='//chat.center/javascripts/widget.js'; cjs.parentNode.insertBefore(js,cjs);}(document,'script'));</script>


	<body <?php body_class(); ?>>
	<?php do_action('foundationPress_after_body'); ?>
	
	<div class="off-canvas-wrap" data-offcanvas>
	<div class="inner-wrap">
	
	<?php do_action('foundationPress_layout_start'); ?>
	
	<?php get_template_part('parts/top-bar'); ?>
<section class="container" role="document">
	<?php do_action('foundationPress_after_header'); ?>
