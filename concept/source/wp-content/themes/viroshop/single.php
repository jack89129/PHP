<?php get_header(); ?>

	<section id="wrapper" class="container clearfix">
		
		<?php get_sidebar(); ?>				
		
		<section id="content">		
		
			<?php blog_breadcrumb(); ?>
			
				<div class="inner-container">
							
					<section class="blog">
						<?php if (have_posts()) : ?>			
							<?php while (have_posts()) : the_post(); ?>
										
								<article id="post-<?php the_ID(); ?>" class="post hentry">
												
												<header class="entry-header">
													<div class="entry-meta">
														<h1 class="entry-title"><?php the_title(); ?></h1>
														<abbr class="date published" title="<?php the_time('M'); ?> <?php the_time('d'); ?> <?php the_time('Y'); ?>">
															<small><?php the_time('M'); ?></small>
															<span class="date_day"><?php the_time('d'); ?></span>
															<span><?php the_time('Y'); ?></span>
														</abbr>		
														<?php if(of_get_option('viro_featured_image')) { ?>
															<?php blog_image(); ?>
														<?php } ?>
													</div>
												</header>
												
												<div class="post_inner">									
													<div class="post_text entry-content">
														<?php the_content(); ?>
													</div>
												</div>
												
											</article>		
				
								<?php comments_template(); ?> 
							<?php endwhile; ?>					
						<?php endif; ?>
					</section>
		
			</div>
		</section>
	</section>
			
<?php get_footer(); ?>


