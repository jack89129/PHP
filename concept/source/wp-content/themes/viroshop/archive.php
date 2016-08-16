<?php get_header(); ?>

	<section id="wrapper" class="container clearfix">
		
		<?php get_sidebar(); ?>				
		
		<section id="content">		
		
	
				<div class="inner-container">
							
					<section class="blog">
			<?php if (have_posts()) : ?>			
				<?php while (have_posts()) : the_post(); ?>
				
			
					<article id="post-<?php the_ID(); ?>" class="post hentry">
									
									<header class="entry-header">
										<div class="entry-meta">
											<abbr class="date published" title="<?php the_time('M'); ?> <?php the_time('d'); ?> <?php the_time('Y'); ?>">
												<small><?php the_time('M'); ?></small>
												<span class="date_day"><?php the_time('d'); ?></span>
												<span><?php the_time('Y'); ?></span>
											</abbr>										
											<?php blog_image(); ?>
										</div>
									</header>
									
									<div class="post_inner">
										<footer class="entry-meta">	
											<ul>
												<li><strong>><?php _e("Author", "pixelstores"); ?>: </strong><?php the_author_link(); ?></li>
												<li><strong><?php _e("Category", "pixelstores"); ?>: </strong><?php the_category(', '); ?></li>
												<?php the_tags('<li><strong>Tags: </strong> ',', ','</li>'); ?>
												<li><a href="openpost.html#comments"><?php comments_popup_link(__('0 comments','pixelstores'),__('1 comment','pixelstores'),__('% comments','pixelstores')); ?></a></li>
											</ul>
										</footer>
										<div class="post_text entry-content">
											<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
											<?php the_excerpt(); ?>
											<a href="<?php the_permalink() ?>" class='button'><?php _e("Read More", "pixelstores"); ?></a>
										</div>
									</div>
									
								</article>				
					
				<?php endwhile; ?>					
			<?php endif; ?>
		</section>
		</div>

		</section>
	</section>		
<?php get_footer(); ?>


