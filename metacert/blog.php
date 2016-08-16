<?php 
/*
Template Name: Blog Page Template
*/
get_header(); 

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
                <li><a href="<?php echo get_category_link($category->term_id); ?>" <?php if ($category->slug == $active_slug) { echo 'class="active"'; } ?>><?=$category->name?></a></li>
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
<!-- Row for main content area -->
    <div class="blog-main large-8 medium-8 small-12 columns" role="main">
    <?php
    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
    $query = new WP_Query( array( 'paged' => $paged, 'orderby' => array( 'post_date' => 'DESC' ) ) );
    $wp_query = $query;
?>
    <?php if ( have_posts() ) : ?>

        <?php /* Start the Loop */ ?>
        <?php while ( have_posts() ) : the_post(); ?>
            <?php get_template_part( 'content', 'blog' ); ?>
        <?php endwhile; ?>

    <?php else : ?>
        <?php get_template_part( 'content', 'none' ); ?>

    <?php endif; // end have_posts() check ?>

    <?php /* Display navigation to next/previous pages when applicable */ ?>
    <?php //if ( function_exists('FoundationPress_pagination') ) { FoundationPress_pagination(); } else if ( is_paged() ) { ?>
        <nav id="post-nav" class="small-text-center">
            <div class="post-next hide-for-small"><?php previous_posts_link( __( 'Previous', 'FoundationPress' ) ); ?></div>
            <div class="post-previous hide-for-small"><?php next_posts_link( __( 'Next', 'FoundationPress' ) ); ?></div>
		<div class="post-previous show-for-small"><?php next_posts_link( __( 'Show more', 'FoundationPress' ) ); ?></div>
<!--            <div class="post-previous show-for-small"><?php previous_posts_link( __( 'Show more', 'FoundationPress' ) ); ?></div>      -->  </nav>
    <?php //} ?>

    </div>
    <?php get_sidebar(); ?>
</div>
</div>
<?php get_footer(); ?>
