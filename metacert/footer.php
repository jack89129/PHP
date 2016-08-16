</section>
<footer id="menu-footer">
    <div class="row">
        <?php do_action('foundationPress_before_footer'); ?>
        <div class="large-12 medium-12 columns small-text-center medium-text-center large-text-center">
            <div class="footer-menu-wrapper row">
                <div class="footer-left-wrapper large-6 medium-12 columns">
                    <?php wp_nav_menu( array( 'menu' => 'Main Menu', 'menu_class' => 'footer-left-menu' ) ); ?>
                </div>
                <div class="footer-right-wrapper large-6 medium-12 columns">
                    <?php foundationPress_top_bar_r(); ?>
                </div>
            </div>
        </div>
        <?php do_action('foundationPress_after_footer'); ?>
    </div>
</footer>
<footer id="footer">
    <div class="row">
	    <?php do_action('foundationPress_before_footer'); ?>
        <div class="large-12 medium-12 columns small-text-center medium-text-center large-text-center">
            <div class="copyright-wrapper">
                <div class="copyright">&copy; 2009-2015 Metacert</div>
                <div class="term-menu">
                    <?php foundationPress_top_bar_l(); ?>
                </div>
            </div>
        </div>
	    <?php do_action('foundationPress_after_footer'); ?>
    </div>
</footer>

	<?php do_action('foundationPress_layout_end'); ?>
	</div>
</div>
<?php wp_footer(); ?>
<?php do_action('foundationPress_before_closing_body'); ?>
</body>
</html>
