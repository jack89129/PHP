<?php 
/*
Template Name: Integration Page Template
*/
get_header(); ?>
<style type="text/css">
#header li.menu-item-700 a {
    color: #ff783c;
}
</style>
<div class="api-document-wrapper">
<div class="row">
<!-- Row for main content area -->
    <div class="mobile-api-sidebar show-for-small">
        <span>Integrations</span>
        <a href="javascript:;" class="btn-api-dropdown"></a>
    </div>
    <div class="api-sidebar large-2 medium-3 columns">
        <?php wp_nav_menu( array( 'menu' => 'Integration Menu', 'menu_class' => 'api-menu' ) ); ?>
    </div>
    <div class="api-content large-8 medium-7 columns">
        <?php while (have_posts()) : the_post(); ?>
        <?php the_content(); ?>
        <?php endwhile;?>
    </div>
    <div class="totop-wrapper large-2 medium-2 columns small-text-center">
        <a href="javascript:;" class="move-to-top show-for-small"><img src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/back-to-top.png" /></a>
    </div>
    <a href="javascript:;" class="move-to-top hide-for-small"><img src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/back-to-top.png" /></a>
</div>
</div>

<script type="text/javascript">
jQuery( document ).ready(function() {
    jQuery('.api-sidebar li li a').on('click', function(){
        jQuery('.api-sidebar li.current_page_item').removeClass('current_page_item');
        jQuery('.api-sidebar li li a.active').removeClass('active');
        jQuery(this).addClass('active');
    });
    /*jQuery('.api-content.columns').height(jQuery(window).height() - 260);
    $('.totop-wrapper .move-to-top').on('click', function(){
        $("html, body").animate({ scrollTop: 0 }, 800);
        $(".api-content.columns").animate({ scrollTop: 0 }, 800);
    });*/
});
</script>
<?php get_footer(); ?>