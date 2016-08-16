<?php
add_action( 'wp_ajax_nopriv_myajax-submit', 'login_form_submit' );
add_action( 'wp_ajax_myajax-submit', 'login_form_submit' );
 
function login_form_submit() {
	global $user_ID, $wpdb;
	parse_str($_POST['serialize'], $whatever);
	$username = $wpdb->escape($whatever['username']);
	$password = $wpdb->escape($whatever['password']);		
	$login_data = array();
	$login_data['user_login'] = $username;
	$login_data['user_password'] = $password;
	$user_verify = wp_signon( $login_data, true );

	if ( is_wp_error($user_verify) ) {
		echo "error";
		exit();
	} else {	
		echo "correct";
		exit();
	}			
			
	die(); 
}

class LoginFormWidget extends WP_Widget
{
    function LoginFormWidget(){
		$widget_ops = array('description' => 'Displays the Login Form');
		$control_ops = array('height' => 300);
		parent::WP_Widget(false,$name='VS Login Form',$widget_ops,$control_ops);
    }

  /* Displays the Widget in the front-end */
    function widget($args, $instance){
		extract($args);
		global $current_user;
     	get_currentuserinfo();
     	$home = get_bloginfo('url');
		$membertext = empty($instance['membertext']) ? __('Members','pixelstores') : $instance['membertext'];
		$errortext = empty($instance['errortext']) ? __('Your username or password seems to be incorrect.','pixelstores') : $instance['errortext'];
		
		if ( !is_user_logged_in() ) {
		echo $before_widget;

		if ( $title )  ?>	
<?php 

wp_enqueue_script( 'my-ajax-request', get_template_directory_uri() . '/assets/js/ajaxlf.js', array( 'jquery' ) ); 
wp_localize_script( 'my-ajax-request', 'MyAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );


echo '<div id="loadplace">
<ul>
<li class="widgettitle">'.__('Error','pixelstores').'</li>
<li class="error-field">';
echo $errortext; 
echo	'</li>
<li class="try-again"><a href="" class="button">'.__('Try Again','pixelstores').'</a></li>
</ul>         
</div>
<form id="loginForm">
<ul>
<li class="widgettitle">';
echo $membertext; 
echo '</li>
<li><input type="text" class="username" placeholder="'.__('Username','pixelstores').'" name="username" autocomplete="off"/></li>
<li><input type="password" class="password" placeholder="'.__('Password','pixelstores').'" name="password" autocomplete="off" />
<input type="submit"  value="'.__('Login','pixelstores').'" class="button submit" /></li>
</ul>         
</form>';


echo $after_widget;
?>

	<?php 
}
}

  /*Saves the settings. */
    function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['membertext'] = stripslashes($new_instance['membertext']);
		$instance['errortext'] = stripslashes($new_instance['errortext']);
		return $instance;
	}

  /*Creates the form for the widget in the back-end. */
    function form($instance){
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('membertext'=>'Members', 'errortext'=>'Your username or password seems to be incorrect.') );
		$membertext = htmlspecialchars($instance['membertext']);
		$errortext = htmlspecialchars($instance['errortext']);

		# Title
		echo '<p><label for="' . $this->get_field_id('membertext') . '">' . 'Login Title:' . '</label><input class="widefat" id="' . $this->get_field_id('membertext') . '" name="' . $this->get_field_name('membertext') . '" type="text" value="' . $membertext . '" /></p>';
		
		# Error Field
		echo '<p><label for="' . $this->get_field_id('errortext') . '">' . 'Login Error:' . '</label><input class="widefat" id="' . $this->get_field_id('errortext') . '" name="' . $this->get_field_name('errortext') . '" type="text" value="' . $errortext . '" /></p>';
	}

}

function LoginFormWidgetInit() {
  register_widget('LoginFormWidget');
}

add_action('widgets_init', 'LoginFormWidgetInit');

?>