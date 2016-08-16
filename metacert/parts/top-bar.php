<div id="header" class="top-bar-container contain-to-grid desktop-menu-wrapper">
    <nav class="top-bar" data-topbar="">
        <div class="logo-wrapper large-2 medium-2 small-8 columns">
            <h1 class="logo">
                <a href="<?php echo home_url(); ?>">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png" alt="<?php bloginfo('name'); ?>" width="115" height="10" class="logo-for-page"/>
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-white.png" alt="<?php bloginfo('name'); ?>" width="115" height="10" class="logo-for-home"/>
                </a>
            </h1>
        </div>
        <div class="header-main-wrapper large-8 medium-7 hide-for-small columns no-padding">
            <?php wp_nav_menu( array( 'menu' => 'Main Menu', 'menu_class' => 'nav-menu' ) ); ?>
        </div>
        <div class="quick-menu large-2 medium-3 hide-for-small columns no-padding">
            <ul class="pull-right">
                <li><a href="<?php echo home_url(); ?>/about/" class="link-about">About</a></li>
                <li><a href="<?php echo home_url(); ?>/blog/" class="link-blog">Blog</a></li>
            </ul>
        </div>
        <div class="small-4 columns show-for-small">
            <a href="javascript:;" class="toggle-menu pull-right logo-for-page"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/btn-mobile.png" /></a>
            <a href="javascript:;" class="toggle-menu pull-right logo-for-home"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/btn-mobile-home.png" /></a>
        </div>
    </nav>
</div>
<div class="mobile-menu-wrapper row">
    <div class="small-8 columns no-padding">
        <h1 class="logo">
            <a href="<?php echo home_url(); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo-white.png" alt="<?php bloginfo('name'); ?>" width="115" height="10"/>
            </a>
        </h1>
    </div>
    <div class="small-4 columns show-for-small no-padding">
        <a href="javascript:;" class="close-menu pull-right"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/menu-btn-close.png" /></a>
    </div>
    <div class="menu-wrapper small-text-center">
        <?php wp_nav_menu( array( 'menu' => 'Mobile Menu', 'menu_class' => 'mobile-menu' ) ); ?>
    </div>
</div>