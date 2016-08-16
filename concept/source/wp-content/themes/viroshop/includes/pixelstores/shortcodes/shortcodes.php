<?php

//	Shortcodes CSS	
	add_action('wp_print_styles', 'add_shortcodes_stylesheet');
	function add_shortcodes_stylesheet() {
		$styleUrl = get_bloginfo('template_directory') . '/includes/pixelstores/shortcodes/shortcodes.css';		
		wp_register_style('shortcodesStyleSheets', $styleUrl);
		wp_enqueue_style( 'shortcodesStyleSheets');
	}

//	Contact form handler
	add_action( 'wp_ajax_nopriv_contact-submit', 'contact_form_submit' );
	add_action( 'wp_ajax_contact-submit', 'contact_form_submit' );
	
	function contact_form_submit() {
		parse_str($_POST['serialize'], $whatever);
		$name = trim($whatever['name']);
		$email = trim($whatever['email']);
		$message = trim($whatever['message']);
		
	 	if (empty($name) && empty($email)) {
			$hasError = true;
			echo 'nameemailfail';
		}	
		else if (empty($name)) {
			$hasError = true;
			echo 'namefail';
		}  	
		else if (empty($email)) {
			$hasError = true;
			echo 'emailfail';
		}
		else if (!eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($whatever['email']))) {
			$hasError = true; 
			echo 'emailincorrect';
		}
		else {
			$emailTo = of_get_option('viro_contact_email');
			$subject = 'Contact Form Submission from '.$name;
			$body = "Name: $name \n\nEmail: $email \n\nMessage: $message";
			$headers = 'From: '.$name.' <'.$emailTo.'>' . "\r\n" . 'Reply-To: ' . $email;
			
			wp_mail($emailTo, $subject, $body, $headers);
		}
		die(); 
	}

//	Contact form
	function pixelstores_contact( $atts, $content = null) {	
	extract( shortcode_atts( array(
      'title' => 'Send A Message',
      'success' => 'Thankyou for sending us an email, we will get back to you shortly.'

      ), $atts ) );
    wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-color');
	wp_enqueue_script( 'contact-request', get_template_directory_uri() . '/assets/js/contact.js', array( 'jquery' ) ); 
	wp_localize_script( 'contact-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

	$content .= '<div id="contact">
		<h3>'.$title.'</h3>
		<div class="email-success">'.$success.'</div>		
			<form id="contact-form" action="">
				<fieldset>
					<ul>
						<li class="text-field">
							<label for="name-example">
								<span class="label">'.__('Name','pixelstores').'</span>
							</label>				
							<span class="name_ico"></span>
							<input type="text" name="name" id="name"  value="" />
						</li>
						<li class="text-field">
							<label for="email-example">
								<span class="label">'.__('Email','pixelstores').'</span>
							</label>
							<span class="email_ico"></span>
							<input type="text" name="email" id="email" value="" />
						</li>
						<li class="textarea-field">
							<label for="message-example">
								<span class="label">'.__('Message','pixelstores').'</span>
							</label>
							<textarea name="message" id="message" rows="8" cols="30"></textarea>
						</li>
						<li class="submit-button">
							<input type="submit" value="'.__('Send Message','pixelstores').'" class="button" id="contact-submit" />
						</li>
					</ul>
				</fieldset>
			</form>
		</div>';
			
	return $content;
	}
	add_shortcode('contact', 'pixelstores_contact');
	
// 	Icons & Pictograms
	function pixelstores_icons( $atts ) {
		extract(shortcode_atts(array(
			'icon' => '',
			'title' => ''
		), $atts));
	 
		switch ($icon) {
			case 'new' :
				$iconurl = 'badge.png'; break;
			case 'close' :
				$iconurl = 'close.png'; break;
			case 'giftbox' :
				$iconurl = 'giftbox.png'; break;
			case 'home' :
				$iconurl = 'home.png'; break;
			case 'shield' :
				$iconurl = 'shield.png'; break;
			case 'truck' :
				$iconurl = 'shipping-truck.png'; break;
			case 'parcel' :
				$iconurl = 'parcel-box.png'; break;
			case 'bag' :
				$iconurl = 'shopping-bag.png'; break;
			case 'download' :
				$iconurl = 'download.png'; break;
			case 'open' :
				$iconurl = 'open.png'; break;
			case 'search' :
				$iconurl = 'search.png'; break;
			case 'security' :
				$iconurl = 'security-lock.png'; break;
			case 'dollar' :
				$iconurl = 'dollar.png'; break;
			case 'email' :
				$iconurl = 'email.png'; break;
			case 'basket' :
				$iconurl = 'shopping-basket.png'; break;
			case 'tag' :
				$iconurl = 'tag.png'; break;
			case 'card' :
				$iconurl = 'creditcard.png'; break;
			case 'bulb' :
				$iconurl = 'bulb.png'; break;
			case 'tele_light' :
				$iconurl = 'telephone-light.png'; break;
			case 'unlock' :
				$iconurl = 'unlock.png'; break;
			case 'card_light' :
				$iconurl = 'credit_card.png'; break;
			case 'camera' :
				$iconurl = 'camera.png'; break;
			case 'cd' :
				$iconurl = 'CD.png'; break;
			case 'currency_dollar' :
				$iconurl = 'currency_dollar.png'; break;
			case 'currency_euro' :
				$iconurl = 'currency_euro.png'; break;
			case 'currency_pound' :
				$iconurl = 'currency_pound.png'; break;
			case 'currency_yuan' :
				$iconurl = 'currency_yuan.png'; break;
			case 'gear' :
				$iconurl = 'gear.png'; break;
			case 'poll' :
				$iconurl = 'poll.png'; break;
			case 'hyperlink' :
				$iconurl = 'hyperlink.png'; break;
			case 'basket_light' :
				$iconurl = 'shopping_basket.png'; break;
			case 'puzzle' :
				$iconurl = 'puzzle.png'; break;
			case 'lock_light' :
				$iconurl = 'lock.png'; break;
			case 'cart_light' :
				$iconurl = 'shopping_cart.png'; break;
			case 'bag_light' :
				$iconurl = 'shopping_bag.png'; break;
			case 'suitcase' :
				$iconurl = 'suitcase.png'; break;
			case 'clock' :
				$iconurl = 'clock.png'; break;
			case 'monitor' :
				$iconurl = 'monitor.png'; break;
			case 'globe' :
				$iconurl = 'globe.png'; break;
			case 'new_dark' :
				$iconurl = 'label_new.png'; break;
			case 'sale_dark' :
				$iconurl = 'label_sale.png'; break;
			default :
				$iconurl = $icon;
			break;
		}
	 
		return '<img class="'.$icon.' alignleft" title="'.$title.'" src="' .get_template_directory_uri() . '/includes/pixelstores/shortcodes/images/'.$iconurl.'" alt="'.$title.'" />';
	}
	add_shortcode('icon', 'pixelstores_icons');

// 	Code & Pre
	function pixelstores_code( $attr, $content = null ) {
			$content = clean_pre($content); // Clean pre-tags
			return '<pre><code>' .
				   str_replace('<', '<', $content) . // Escape < chars
				   '</code></pre>';
	}
	add_shortcode('code', 'pixelstores_code');
	
// 	Youtube
	function pixelstores_youtube($atts) {
		extract(shortcode_atts(array(
			"id" => 'nsnTdMQMPhM',
			"autoplay" => '0',
			"controls" => '1',
			"showinfo" => '1',
			"autohide" => '0'
		), $atts));
		return '<div class="videoWrapper" style="margin-bottom: 15px;"><iframe src="http://www.youtube.com/embed/'.$id.'?autoplay='.$autoplay.'&amp;controls='.$controls.'&amp;showinfo='.$showinfo.'&amp;autohide='.$autohide.'"  frameborder="0" allowfullscreen></iframe></div>';
	
	}
	add_shortcode("youtube", "pixelstores_youtube");
	
// 	Vimeo
	function pixelstores_vimeo($atts) {
		extract(shortcode_atts(array(
			"id" => 'nsnTdMQMPhM',
			"autoplay" => '0',
			"title" => '1',
			"byline" => '1',
			"loop" => '0'
		), $atts));
		return '<div class="videoWrapper" style="margin-bottom: 15px;"><iframe src="http://player.vimeo.com/video/'.$id.'?title='.$title.'&amp;byline='.$byline.'&amp;autoplay='.$autoplay.'&amp;loop='.$loop.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe></div>';
	
	}
	add_shortcode("vimeo", "pixelstores_vimeo");
	
// 	Dailymotion
	function pixelstores_dailymotion($atts) {
		extract(shortcode_atts(array(
			"id" => 'xpaqka',
			"autoplay" => '0'
		), $atts));
			
		return '<div class="videoWrapper" style="margin-bottom: 15px;"><object>
					<param name="movie" value="http://dailymotion.virgilio.it/swf/video/'.$id.'?&amp;theme=none&amp;foreground=%23F7FFFD&amp;highlight=%23FFC300&amp;background=%23171D1B&amp;additionalInfos=1&hideInfos=1&amp;start=&amp;animatedTitle=&amp;iframe=0&amp;autoPlay='.$autoplay.'"></param>
					<param name="allowFullScreen" value="true"></param>
					<param name="allowScriptAccess" value="always"></param>
					<embed type="application/x-shockwave-flash" src="http://dailymotion.virgilio.it/swf/video/'.$id.'?&amp;theme=none&amp;foreground=%23F7FFFD&amp;highlight=%23FFC300&amp;background=%23171D1B&amp;additionalInfos=1&amp;hideInfos=1&amp;start=&amp;animatedTitle=&amp;iframe=0&amp;autoPlay='.$autoplay.'" allowfullscreen="true" allowscriptaccess="always"></embed>
				</object></div>';
	}
	add_shortcode("dailymotion", "pixelstores_dailymotion");

// 	Buttons	
	function pixelstores_button( $atts, $content = null ) {
	   extract( shortcode_atts( array(
		  'type' => '',
		  'colour' => '',
		  'url' => '#'
		  ), $atts ) );
	 
	   return '<a href="'. $url .'" class="button '. $type .' '. $colour .'">'. $content .'</a>';
	}
	add_shortcode('button', 'pixelstores_button');

// 	Divider	
	function pixelstores_divider( $atts, $content = null ) {
	   return '<hr>';
	}
	add_shortcode('divider', 'pixelstores_divider');

// 	Google Map		
	function pixelstores_googlemap($atts, $content = null) {
   extract(shortcode_atts(array(
      "height" => '325',
      "src" => ''
   ), $atts));
   return '<iframe class="gmap" height="'.$height.'" src="'.$src.'&output=embed" ></iframe>';
}
	add_shortcode("googlemap", "pixelstores_googlemap");

//	Dropcap
	function pixelstores_dropcap( $atts, $content = null ) {
		return '<span class="dropcap">' . do_shortcode($content) . '</span>';
	}
	add_shortcode('dropcap', 'pixelstores_dropcap'); 

//	Square Dropcap
	function pixelstores_square_dropcap( $atts, $content = null ) {
   		return '<span class="square-dropcap">' . do_shortcode($content) . '</span>';
	}
	add_shortcode('square_dropcap', 'pixelstores_square_dropcap');

//	Round Dropcap
	function pixelstores_round_dropcap( $atts, $content = null ) {
	   return '<span class="round-dropcap">' . do_shortcode($content) . '</span>';
	}
	add_shortcode('round_dropcap', 'pixelstores_round_dropcap');

//	One Full
	function pixelstores_one_full( $atts, $content = null ) {
		return '<div class="one_full">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
	}
	add_shortcode('one_full', 'pixelstores_one_full');

//	One Third	
	function pixelstores_one_third( $atts, $content = null ) {
	   return '<div class="one_third">' . do_shortcode($content) . '</div>';
	}
	add_shortcode('one_third', 'pixelstores_one_third');

//	One Third Last	
	function pixelstores_one_third_last( $atts, $content = null ) {
	   return '<div class="one_third last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
	}
	add_shortcode('one_third_last', 'pixelstores_one_third_last');

//	Two Third
	function pixelstores_two_third( $atts, $content = null ) {
	   return '<div class="two_third">' . do_shortcode($content) . '</div>';
	}
	add_shortcode('two_third', 'pixelstores_two_third');
	
//	One Half	
	function pixelstores_one_half( $atts, $content = null ) {
	   return '<div class="one_half">' . do_shortcode($content) . '</div>';
	}
	add_shortcode('one_half', 'pixelstores_one_half');

//	One Half Last	
	function pixelstores_one_half_last( $atts, $content = null ) {
	   return '<div class="one_half last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
	}
	add_shortcode('one_half_last', 'pixelstores_one_half_last');

//	One Fourth	
	function pixelstores_one_fourth( $atts, $content = null ) {
	   return '<div class="one_quarter">' . do_shortcode($content) . '</div>';
	}
	add_shortcode('one_fourth', 'pixelstores_one_fourth');

//	One Fourth Last	
	function pixelstores_one_fourth_last( $atts, $content = null ) {
	   return '<div class="one_quarter last">' . do_shortcode($content) . '</div><div class="clearboth"></div>';
	}
	add_shortcode('one_fourth_last', 'pixelstores_one_fourth_last');

//	Long posts should require a higher limit
	@ini_set('pcre.backtrack_limit', 500000);

?>