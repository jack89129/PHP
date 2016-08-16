<?php get_header(); ?>

	<?php get_sliders(); ?>

	<section id="wrapper" class="container clearfix">
		
		<?php get_sidebar(); ?>				
		
			<section id="content">				
				
				<?php if (of_get_option('viro_featured_products'))  { echo do_shortcode('[vs_featured_products title="'.of_get_option('viro_featured_text').'" per_page="'.of_get_option('viro_featured_show').'" orderby="date" order="desc"]'); } ?>
				
					<?php if (have_posts()) : ?>			
						<?php while (have_posts()) : the_post(); ?>
						
							<div class="inner-container">
								<?php the_content(''); ?>
							</div>
							
						<?php endwhile; ?>					
					<?php endif; ?>
				</section>	
	
	</section>		

<?php get_footer(); ?>