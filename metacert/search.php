<?php get_header();
 
/* Get Top Categories */
$args = array(
    'depth' => 1,
    'taxonomy' => 'category'
);

$categories = get_categories( $args );
$active_slug = 'blog';

?>

<div class="blog-menu-wrapper">
    <div class="row">
        <div class="large-8 medium-8 columns">
            <ul>
            <?php foreach ( $categories as $category ): ?>
                <?php if ( $category->name != "Uncategorized" ): ?>
                <li><a href="#" <?php if ($category->slug == $active_slug) { echo 'class="active"'; } ?>><?=$category->name?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
            </ul>
        </div>
        <div class="large-3 medium-4 columns no-padding">
            <div class="search-wrapper">
                <form id="frm-search" action="https://metacert.com" method="get" role="search">
                    <input type="text" class="search-input" name="s" id="s" placeholder="Search" />
                    <input type="submit" class="btn-search" value="Search" />
                </form>
            </div>
        </div>
    </div>
</div>

<div class="blog-page-wrapper">
 <div class="row">
	<div class="blog-main small-12 large-8 medium-8 columns" role="main">

		<?php do_action('foundationPress_before_content'); ?>

		<h2><?php _e('Search Results for', 'FoundationPress'); ?> "<?php echo get_search_query(); ?>"</h2>

	<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>
                     <?php if ( get_post_type() == 'page' ) continue; ?>
			<?php get_template_part( 'content', 'blog' ); ?>
		<?php endwhile; ?>

		<?php else : ?>
			<?php get_template_part( 'content', 'none' ); ?>

	<?php endif;?>

	<?php do_action('foundationPress_before_pagination'); ?>

	<?php if ( function_exists('FoundationPress_pagination') ) { FoundationPress_pagination(); } else if ( is_paged() ) { ?>

		<nav id="post-nav">
			<div class="post-previous"><?php next_posts_link( __( '&larr; Older posts', 'FoundationPress' ) ); ?></div>
			<div class="post-next"><?php previous_posts_link( __( 'Newer posts &rarr;', 'FoundationPress' ) ); ?></div>
		</nav>
	<?php } ?>

	<?php do_action('foundationPress_after_content'); ?>

	</div>
	<?php get_sidebar(); ?>
 </div>
</div>
<?php get_footer(); ?>
