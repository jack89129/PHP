<?php
    // social links
    $themeOptions = get_option( 'chrs_theme_options' );
?>
<aside id="sidebar" class="blog-sidebar large-3 medium-4 small-12 columns">
    <div class="row">
        <div class="sidebar-wrapper large-12 columns no-padding">
            <div class="sidebar-about widget">
                <div class="img-wrap">
                    <img src="<?php echo get_stylesheet_directory_uri() ; ?>/assets/img/CEO.png" />
                </div>
                <div class="text-wrap">
                    <p><?php echo do_shortcode('[add-static-block type="pauls-bio"]'); ?></p>
                    <!--<p>The Irish Opportunist - A Founder, Chair, CEO, Mentor and Advisor to Internet and Mobile companies, who likes to speak his mind.</p>-->
                </div>
                <div class="clearfix"></div>
            </div>
	        <?php do_action('foundationPress_before_sidebar'); ?>
	        <?php dynamic_sidebar("sidebar-widgets"); ?>
	        <?php do_action('foundationPress_after_sidebar'); ?>
        </div>
    </div>
</aside>
