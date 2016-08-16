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
	<div class="blog-main large-8 medium-8 small-12 columns" role="main">

	<?php while (have_posts()) : the_post(); ?>
        <?php $summary = wp_strip_all_tags(get_the_content()); $summary = substr($summary, 0, 200); ?>
        <meta property="og:image" content="<?php echo wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()) ); ?>" />
        <meta property="og:description" content="<?php echo $summary; ?>" />
		<article <?php post_class() ?> id="post-<?php the_ID(); ?>">
            <div class="post-wrapper">
			    <header>
				    <h1 class="entry-title"><?php the_title(); ?></h1>
				    <?php //FoundationPress_entry_meta(); ?>
			    </header>
                <div class="post-info">
                    <span><?php the_time('F j, Y') ?></span>
                    <div class="sharethis-wrapper">
                        <span class='st_facebook' st_url="<?php the_permalink(); ?>" st_title="<?php the_title(); ?>"></span>
                        <span class='st_twitter' st_url="<?php the_permalink(); ?>" st_title="<?php the_title(); ?>"></span>
                        <span class='st_linkedin' st_url="<?php the_permalink(); ?>" st_title="<?php the_title(); ?>"></span>
                        <script type="text/javascript">var switchTo5x=true;</script>
                        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/button.js"></script>
                        <script type="text/javascript">
                            stLight.options({
                                publisher: '19283-42314-23154-32153-53245',
                                onhover: false,
                                servicePopup: true,
                                popup: true,
                            });
                        </script>
                        <a href="#respond" class="link-comment" />Comment</a>
                </div>
			    <?php do_action('foundationPress_post_before_entry_content'); ?>
			    <div class="entry-content">

			    <?php if ( has_post_thumbnail() ): ?>
				    <div class="row">
					    <div class="img-wrapper">
						    <?php the_post_thumbnail('', array('class' => 'th')); ?>
					    </div>
				    </div>
			    <?php endif; ?>

			    <?php the_content(); ?>
			    </div>
			    <footer>
				    <?php wp_link_pages(array('before' => '<nav id="page-nav"><p>' . __('Pages:', 'FoundationPress'), 'after' => '</p></nav>' )); ?>
				    <p><?php the_tags(); ?></p>
			    </footer>
			    <?php do_action('foundationPress_post_before_comments'); ?>
			    <?php comments_template(); ?>
			    <?php do_action('foundationPress_post_after_comments'); ?>
            </div>
		</article>
	<?php endwhile;?>

	</div>
	<?php get_sidebar(); ?>
</div>
</div>
<?php get_footer(); ?>
