<?php

class ProductWidget extends WP_Widget
{
    function ProductWidget(){
		$widget_ops = array('description' => 'Displays a Product');
		$control_ops = array('height' => 300);
		parent::WP_Widget(false,$name='VS Product',$widget_ops,$control_ops);
    }

  /* Displays the Widget in the front-end */
    function widget($args, $instance){
		extract($args);
		$title = empty($instance['title']) ? 'On Sale' : $instance['title'];
		$productid = empty($instance['id']) ? '' : $instance['id'];
		{
		if ( $productid ) ?>	
<?php 

}

	
	{ ?>

<?php if(!empty($productid)) { echo $before_widget; ?>
	
<h4 class="widgettitle"><?php echo $title; ?></h4>

<ul class="productulwidget">
<?php
	$args = array( 'post_type' => 'product', 'posts_per_page' => 1, 'p' => $productid );
	$loop = new WP_Query( $args );
	while ( $loop->have_posts() ) : $loop->the_post(); global $product; ?>
			
			<li class="productwidget">	
				
				<a class="productwidgetimg" href="<?php echo get_permalink( $loop->post->ID ) ?>" title="<?php echo esc_attr($loop->post->post_title ? $loop->post->post_title : $loop->post->ID); ?>">
									
					<?php if (has_post_thumbnail( $loop->post->ID )) echo get_the_post_thumbnail($loop->post->ID, 'shop_product'); else echo '<img src="'.woocommerce_placeholder_img_src().'" alt="Placeholder" width="'.$woocommerce->get_image_size('shop_product_image_width').'px" height="'.$woocommerce->get_image_size('shop_product_image_height').'px" />'; ?>
									
									
				</a>
								
			</li>
		
<?php endwhile; wp_reset_query(); ?>
</ul><!--/.products-->


</div>
	<?php 
	echo $after_widget;
		}
}
}

  /*Saves the settings. */
    function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = stripslashes($new_instance['title']);
		$instance['id'] = stripslashes($new_instance['id']);

		return $instance;
	}

  /*Creates the form for the widget in the back-end. */
    function form($instance){
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('title'=>'On Sale', 'id'=>'') );

		$title = htmlspecialchars($instance['title']);
		$productid = htmlspecialchars($instance['id']);

		# Title
		echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';

		# ID Field
		echo '<p><label for="' . $this->get_field_id('id') . '">' . 'Product ID:' . '</label><input class="widefat" id="' . $this->get_field_id('id') . '" name="' . $this->get_field_name('id') . '" type="text" value="' . $productid . '" /></p>';
		

	}

}// end LatestReviewsWidget class

function ProductWidgetInit() {
  register_widget('ProductWidget');
}

add_action('widgets_init', 'ProductWidgetInit');

?>