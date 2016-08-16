<?php

class SocialWidget extends WP_Widget
{
    function SocialWidget(){
		$widget_ops = array('description' => 'Displays Social Networks');
		$control_ops = array('height' => 300);
		parent::WP_Widget(false,$name='VS Social',$widget_ops,$control_ops);
    }

  /* Displays the Widget in the front-end */
    function widget($args, $instance){
		extract($args);
		$title = empty($instance['title']) ? 'Follow Us' : $instance['title'];
		$email = empty($instance['email']) ? 'Email' : $instance['email'];
		$emailurl = empty($instance['emailurl']) ? '' : $instance['emailurl'];
		$facebook = empty($instance['facebook']) ? 'Facebook' : $instance['facebook'];
		$facebookurl = empty($instance['facebookurl']) ? '' : $instance['facebookurl'];
		$twitter = empty($instance['twitter']) ? 'Twitter' : $instance['twitter'];
		$twitterurl = empty($instance['twitterurl']) ? '' : $instance['twitterurl'];
		$rssfeed = empty($instance['rssfeed']) ? 'RSS Feed' : $instance['rssfeed'];
		$rssfeedurl = empty($instance['rssfeedurl']) ? '' : $instance['rssfeedurl'];

		{
		if ( $title ) ?>	
<?php 

}

	
	{ ?>

<?php if(!empty($title)) { echo $before_widget; ?>
	
<h4 class="widgettitle"><?php echo $title; ?></h4>

<ul class="social">

<?php 

if(!empty($facebookurl)) { echo '<li><a class="facebook" href="' . $facebookurl . '">' . $facebook . '</a></li>'; } 
if(!empty($twitterurl)) { echo '<li><a class="twitter" href="' . $twitterurl . '">' . $twitter . '</a></li>'; } 
if(!empty($emailurl)) { echo '<li><a class="email" href="' . $emailurl . '">' . $email . '</a></li>'; } 
if(!empty($rssfeedurl)) { echo '<li><a class="rssfeed" href="' . $rssfeedurl . '">' . $rssfeed . '</a></li>'; } 
?>
</ul>


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
		$instance['email'] = stripslashes($new_instance['email']);
		$instance['emailurl'] = stripslashes($new_instance['emailurl']);
		$instance['facebook'] = stripslashes($new_instance['facebook']);		
		$instance['facebookurl'] = stripslashes($new_instance['facebookurl']);
		$instance['twitter'] = stripslashes($new_instance['twitter']);		
		$instance['twitterurl'] = stripslashes($new_instance['twitterurl']);
		$instance['rssfeed'] = stripslashes($new_instance['rssfeed']);
		$instance['rssfeedurl'] = stripslashes($new_instance['rssfeedurl']);
		return $instance;
	}

  /*Creates the form for the widget in the back-end. */
    function form($instance){
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('title'=>'Follow Us', 'email'=>'Email', 'emailurl'=>'', 'facebook'=>'Facebook', 'facebookurl'=>'', 'twitter'=>'Twitter', 'twitterurl'=>'', 'rssfeed'=>'RSS Feed', 'rssfeedurl'=>'') );

		$title = htmlspecialchars($instance['title']);
		$email = htmlspecialchars($instance['email']);
		$emailurl = htmlspecialchars($instance['emailurl']);
		$facebook = htmlspecialchars($instance['facebook']);
		$facebookurl = htmlspecialchars($instance['facebookurl']);
		$twitter = htmlspecialchars($instance['twitter']);
		$twitterurl = htmlspecialchars($instance['twitterurl']);
		$rssfeed = htmlspecialchars($instance['rssfeed']);
		$rssfeedurl = htmlspecialchars($instance['rssfeedurl']);		
		# Title
		echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';

		# Email Field
		echo '<p><label for="' . $this->get_field_id('email') . '">' . 'Email Title:' . '</label><input class="widefat" id="' . $this->get_field_id('email') . '" name="' . $this->get_field_name('email') . '" type="text" value="' . $email . '" /></p>';
		echo '<p><input class="widefat" id="' . $this->get_field_id('emailurl') . '" name="' . $this->get_field_name('emailurl') . '" type="text" placeholder="http://" value="' . $emailurl . '" /></p>';

		# Facebook Field
		echo '<p><label for="' . $this->get_field_id('facebook') . '">' . 'Facebook Title:' . '</label><input class="widefat" id="' . $this->get_field_id('facebook') . '" name="' . $this->get_field_name('facebook') . '" type="text" value="' . $facebook . '" /></p>';
		echo '<p><input class="widefat" id="' . $this->get_field_id('facebookurl') . '" name="' . $this->get_field_name('facebookurl') . '" type="text" placeholder="http://" value="' . $facebookurl . '" /></p>';

		# Twitter Field
		echo '<p><label for="' . $this->get_field_id('twitter') . '">' . 'Twitter Title:' . '</label><input class="widefat" id="' . $this->get_field_id('twitter') . '" name="' . $this->get_field_name('twitter') . '" type="text" value="' . $twitter . '" /></p>';
		echo '<p><input class="widefat" id="' . $this->get_field_id('twitterurl') . '" name="' . $this->get_field_name('twitterurl') . '" type="text" placeholder="http://" value="' . $twitterurl . '" /></p>';

		# RSS Field
		echo '<p><label for="' . $this->get_field_id('rssfeed') . '">' . 'RSS Title:' . '</label><input class="widefat" id="' . $this->get_field_id('rssfeed') . '" name="' . $this->get_field_name('rssfeed') . '" type="text" value="' . $rssfeed . '" /></p>';
		echo '<p><input class="widefat" id="' . $this->get_field_id('rssfeedurl') . '" name="' . $this->get_field_name('rssfeedurl') . '" type="text" placeholder="http://" value="' . $rssfeedurl . '" /></p>';



	}

}// end LatestReviewsWidget class

function SocialWidgetInit() {
  register_widget('SocialWidget');
}

add_action('widgets_init', 'SocialWidgetInit');

?>