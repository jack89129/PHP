<?php get_header(); ?>

	<section id="wrapper" class="container clearfix">
	
		<?php get_sidebar(); ?>				
		
		<section id="content">
			<?php if (have_posts()) : ?>
				<?php while (have_posts()) : the_post(); ?>
					<?php the_content(''); ?>
				<?php endwhile; ?>		
			<?php endif; ?>
		</section>
		
	</section>

<?php get_footer(); ?>