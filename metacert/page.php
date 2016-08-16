<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
<div class="banner-wrapper">
    <?php the_post_thumbnail(); ?>
</div>

<?php the_content(); ?>

<?php endwhile;?>
<?php get_footer(); ?>
