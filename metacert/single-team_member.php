<?php 

/* Team Member page template */

get_header(); ?>
<div class="member-page-wrapper section black-bg">
<div class="row">
    <div class="large-10 medium-12 small-12 columns small-text-center">
    <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class() ?> id="post-<?php the_ID(); ?>">
            <div class="photo-wrapper">
                <?php the_post_thumbnail(); ?>
                <h6 class="profile-name"><?php echo get_the_title() ?></h6>
                <?php $cat = get_the_category(); ?>
                <p><?php echo $cat[0]->name; ?></p>
            </div>
            <div class="entry-content large-text-left medium-text-left small-text-center">
                <?php the_content(); ?>
            </div>
        </article>
    <?php endwhile;?>
    </div>
</div>
</div>
<?php get_footer(); ?>
