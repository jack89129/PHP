<?php get_header(); ?>

	<section id="wrapper" class="container clearfix">
	
		<?php get_sidebar(); ?>				
		
		<section id="content">
			<?php if (have_posts()) : ?>			
				<?php while (have_posts()) : the_post(); ?>
				
					<header class="page-header">
						<h1 class="page-title"><?php the_title(); ?></h1>
					</header>
									
					<div class="inner-container">
						<?php get_sliders(); ?>
						<?php the_content(''); ?>
					</div>
					
				<?php endwhile; ?>					
			<?php endif; ?>
		</section>
	</section>
			
<?php get_footer(); ?>