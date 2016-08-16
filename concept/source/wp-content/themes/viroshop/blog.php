<?php
/*
Template Name: Blog
*/
?>
<?php 	get_header();
		global $wp_query;
		$page_id = $wp_query->get_queried_object_id();
?>	
	<section id="wrapper" class="container clearfix">
		
		<?php get_sidebar(); ?>				
		
		<section id="content">		
			<header class="page-header">
				<h1 class="page-title"><?php echo get_the_title($page_id); ?></h1>
			</header>
			
			<?php query_posts('post_type=post&post_status=publish&posts_per_page='. of_get_option('viro_posts') .'&paged='. get_query_var('paged')); ?>
			
			<div class="inner-container">
							
					<section class="blog">
		
						<?php if( have_posts() ): ?>
							<?php while( have_posts() ): the_post(); ?>        
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
												<li><strong><?php _e("Author", "pixelstores"); ?>: </strong><?php the_author_link(); ?></li>
												<li><strong><?php _e("Category", "pixelstores"); ?>: </strong><?php the_category(', '); ?></li>
												<?php the_tags('<li><strong>Tags: </strong> ',', ','</li>'); ?>
												<li><?php comments_popup_link(__('0 comments','pixelstores'),__('1 comment','pixelstores'),__('% comments','pixelstores')); ?></li>
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
								<div class="navigation">
									<span class="newer"><?php previous_posts_link(__('« Newer','pixelstores')) ?></span> <span class="older"><?php next_posts_link(__('Older »','pixelstores')) ?></span>
								</div>
							<?php else: ?>
								<p><?php _e('No articles found.','pixelstores'); ?></p>
							<?php endif; wp_reset_query(); ?>
					
					</section>					
				</div>

		</section>
	</section>
			
<?php get_footer(); ?>