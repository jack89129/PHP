<?php
/**
 * The default template for displaying content. Used for both single and index/archive/search.
 *
 * @subpackage FoundationPress
 * @since FoundationPress 1.0
 */
?>

<article id="testpost-<?php the_ID(); ?>" <?php post_class(); ?>>
     <?php
        $content = strip_tags(get_the_content());
        $content = strlen($content) > 330 ? substr($content, 0, 330) . '...' : $content;
        $author = get_the_author();
        $img = wp_get_attachment_url(get_post_thumbnail_id(get_the_ID()));
    ?>
    <div class="post-wrapper row">
        <div class="post-header hide-for-small">
            <h2 class="post-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
            <div class="post-info">
                <span><?php the_time('F j, Y') ?></span>
            </div>
        </div>
        <div class="post-img columns large-3 medium-3 small-4">
            <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail(); ?>
            </a>
        </div>
        <div class="post-content columns small-8 show-for-small no-padding">
            <h2 class="post-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h2>
            <a href="<?php the_permalink(); ?>" class="read-more">READ MORE</a>
        </div>
        <div class="post-content columns large-9 medium-8 hide-for-small">
            <div class="post-img show-for-small">
                <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail(); ?>
                </a>
            </div>
            <p><?=$content?></p>
            <a href="<?php the_permalink(); ?>" class="read-more">READ MORE</a>
            <div class="sharethis-wrapper">
                <span class='st_facebook' st_url="<?php the_permalink(); ?>" st_title="<?php the_title(); ?>" st_summary="<?=$content?>" st_image="<?=$img?>"></span>
                <span class='st_twitter' st_url="<?php the_permalink(); ?>" st_title="<?php the_title(); ?>" st_summary="<?=$content?>" st_image="<?=$img?>"></span>
                <span class='st_linkedin' st_url="<?php the_permalink(); ?>" st_title="<?php the_title(); ?>" st_summary="<?=$content?>" st_image="<?=$img?>"></span>
                <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/button.js"></script>
                <script type="text/javascript">
                    stLight.options({
                        publisher: '19283-42314-23154-32153-53245',
                        onhover: false,
                        servicePopup: true,
                        popup: true,
                    });
                </script>
                <a href="<?php the_permalink(); ?>/#respond" class="link-comment pull-right" />Comment</a>
            </div>
        </div>
    </div>
</article>