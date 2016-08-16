<?php

//	Add image attachment fields
	function pk_attachment_fields($form_fields, $attachment) {
		$form_fields['image_link'] = array(
			'label' => __('Image Custom Link', 'pk_translate'),
			'input' => 'text',
			'value' => get_post_meta($attachment -> ID, '_image_link', true),
			'helps' => __('Add a custom link, internal or external, to open on click over the image.', 'pk_translate')
		);
		return $form_fields;
	}
	function pk_attachment_fields_save($post, $attachment) {
		if (isset($attachment['image_link'])) update_post_meta($post['ID'], '_image_link', $attachment['image_link']);
		return $post;
	}		
	add_filter('attachment_fields_to_edit', 'pk_attachment_fields', 10, 2);
	add_filter('attachment_fields_to_save', 'pk_attachment_fields_save', 10, 2);

//	Get slider
	function get_sliders() { 
		global $post, $pixelstore_mb;
		$meta = get_post_meta(get_the_ID(), $pixelstore_mb->get_the_id(), TRUE);
		if(has_post_thumbnail()) { 
			basic_image($size); 
		} else {
			switch ($meta[slider]) {
				case 'NivoSlider': 
					nivo_slider($size);
				break;
				case 'YouTube':
					youtube_embed();
				break;
				default:		
			}
		}
	}

//	Basic image
	function basic_image($size) {
		global $_wp_additional_image_sizes;
		if ( is_home() || is_front_page()) { $size = 'home-slider'; $class = 'container'; } else { $size = 'page-slider'; $class = 'image-wrapper'; }
		$image_link_url = get_post_meta(get_post_thumbnail_id($post->ID), '_image_link', true);
		echo '<div id="basicimage" class="'.$class.'">';
		if(!empty($image_link_url)) { 	
			echo '<a href="'.$image_link_url.'">';
			the_post_thumbnail($size);
			echo '</a>';
		} else {
			the_post_thumbnail($size);
		}
		echo '</div>';
	}
	
//	NivoSlider
	function nivo_slider($size) {
		global $_wp_additional_image_sizes;
		if ( is_home() || is_front_page()) { $size = 'home-slider'; } else { $size = 'page-slider'; }
		if($images = get_children(array(
			'post_parent'    => get_the_ID(),
			'post_type'      => 'attachment',
			'numberposts'    => -1, // show all
			'post_status'    => null,
			'post_mime_type' => 'image',
			'order' => 'ASC', 
			'orderby' => 'menu_order ID'
		))) {


		echo '<div class="slider-wrapper theme-default clearfix"><div id="slider" class="nivoSlider">';
			foreach($images as $image) {
				$image_attributes = wp_get_attachment_image_src( $image->ID,$size );
				$imageurl = get_post_meta($image->ID, '_image_link', true);
				if(!empty($imageurl)) { 	
					echo '<a href="'.$imageurl.'"><img src="'.$image_attributes[0].'" data-thumb="'.$image_attributes[0].'" alt="" /></a>';
				} else {
					echo '<img src="'.$image_attributes[0].'" data-thumb="'.$image_attributes[0].'" alt="" />';
				} 
	
			}
		echo '</div></div>';
		}
	}

//	Youtube
	function youtube_embed() {
		global $post, $pixelstore_mb;
		$meta = get_post_meta(get_the_ID(), $pixelstore_mb->get_the_id(), TRUE);
		if ( is_home() || is_front_page()) {  $class = 'container';  } else { $class = 'youtube-wrapper';  }
		if ( $meta[youtube_autoplay] == 'true' ) { $autoplay = 1; } else { $autoplay = 0; }
		if ( $meta[youtube_controls] == 'true' ) { $controls = 1; } else { $controls = 0; }
		if ( $meta[youtube_showinfo] == 'true' ) { $showinfo = 1; } else { $showinfo = 0; }
		if ( $meta[youtube_autohide] == 'true' ) { $autohide = 1; } else { $autohide = 0; }
		if(!empty($meta[youtube_id])) { 	

			echo '<div class="'.$class.'"><div class="videoWrapper">';
			echo '<iframe src="http://www.youtube.com/embed/'.$meta[youtube_id].'?autoplay='.$autoplay.'&amp;controls='.$controls.'&amp;showinfo='.$showinfo.'&amp;autohide='.$autohide.'" frameborder="0" allowfullscreen></iframe>';
			echo '</div>';
						echo '</div>';

			}


	}

?>