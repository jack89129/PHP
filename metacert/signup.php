<?php 
/*
Template Name: Signup Page Template
*/

// Register user
if ( !empty($_POST) ) {
    $hidden_data = $_REQUEST['input_data'];
    if ( $hidden_data == "" ) {
    //$firstname = $_REQUEST['firstname'];
    $email = $_REQUEST['email'];
    $btype = !empty($_REQUEST['btype']) ? $_REQUEST['btype'] : 'developer';

    if( null == username_exists( $email ) ) {

      // Generate the password and create the user
      $password = wp_generate_password( 12, false );
     
      $user_id = wp_create_user( $email, $password, $email );

      // Set the nickname
      wp_update_user(
        array(
          'ID'          =>    $user_id,
          'first_name'   =>    $firstname
        )
      );
      update_field('user_type', $btype, 'user_'.$user_id);

      // Acces to the API
      //$metacertMasterKey = "Sa5jY9JAXYwrlUVd";
      $subscriber = array(
	"email" => $email,
	"name" => $email,
	"password" => $password,
	"type" => 'Developer',
	"planName" => "trial",
	"shouldSendEmail" => false
      );
      $invitee = array ("account"=>"metacert-api","invitee" => $subscriber);
      $data_string = json_encode($invitee);
      
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "https://api.instantapi.co/v1/subscriber/invite");
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      //curl_setopt($ch, CURLOPT_CAINFO, (dirname(__FILE__) . '/COMODORSACertificationAuthority.cer'));
      curl_setopt($ch, CURLOPT_HEADER, false);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'internal-key: Sa5jY9JAXYwrlUVd', 'Content-Type: application/json', 'Content-Length: ' . strlen($data_string) ));
      $output = curl_exec($ch);
      curl_close($ch);

      $outputdecoded = json_decode($output, true);
      $key = $outputdecoded['data']['key'];

	 
       
      //Email the Key API to user
      $header = 'From: MetaCert <partners@metacert.com>';
      wp_mail($email, 'Welcome to MetaCert', 'Thanks for signing up for the MetaCert Security API!
Your API key is: '. $key .'
We recommend you first read Getting Started with MetaCert: https://metacert.com/api-documentation/getting-started
Please reply to this email if you have any questions or need support on anything.
Thanks, 
Paul & The MetaCert team
——
Paul Walsh
Founder & CEO, MetaCert
https://metacert.com', $header);
      // Email the user
      //wp_mail( $email, 'Welcome!', 'Your Password: ' . $password );
      wp_safe_redirect( home_url() . '/success?addr=' . base64_encode($email) . '&key=' . base64_encode($key) );
      exit();

    } else {
        $error_msg = "User already exist!";
    }
    
    /*
    $errors = register_new_user($email, $email);
    if ( !is_wp_error($errors) ) {
        $user_id = wp_update_user( array( 'user_login ' => $email, 'first_name' => $firstname ) );
        wp_safe_redirect( home_url() . '/success' );
        exit();
    } else {
        $error_msg = $errors->get_error_message();
    }*/
    }
}

get_header(); ?>

<div class="signup-wrapper section black-bg">
<div class="row">
    <div class="large-6 medium-8 columns small-text-center gift-wrapper large-offset-3 medium-offset-2 no-margin">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/gift-icon1.png" />
        <h6>Get 150 <b>FREE</b> calls <b>EVERY</b> month</h6>
        <p class="hide-for-small">This is a secure connection</p>
        <div class="form-wrapper large-text-left small-text-left">
            <form id="frm-signup" method="post" action="">
                <input type="hidden" name="input_data" id="input_data" value="" />
                <div class="error-wrapper" <?php if ( !empty($error_msg) ) { echo 'style="display: block"'; } ?>><?=$error_msg?></div>
<!--
                <div class="field-wrapper">
                    <p class="error"></p>
                    <input type="text" class="form-field" id="firstname" name="firstname" placeholder="First Name" value="<?=$firstname?>" / >
                </div>
-->
                <div class="field-wrapper">
                    <p class="error"></p>
                    <input type="email" class="form-field" id="email" name="email" placeholder="Email" autofocus="autofocus" tabindex="1" autocorrect="off" autocapitalize="off" value="<?=$email?>"/>
                </div>
<!--
                <div class="field-wrapper">
                    <p class="error"></p>
                    <select class="form-field" id="btype" name="btype">
                        <option value="">Are you a Developer or Company?</option>
                        <option value="developer" <?php if ( $btype == 'developer' ) echo 'selected'; ?>>Developer</option>
                        <option value="company" <?php if ( $btype == 'company' ) echo 'selected'; ?>>Company</option>
                    </select>
                </div>
-->
                <p>By filling this form I agree to the MetaCert <a href="<?php echo home_url(); ?>/api-documentation/legal">Terms of Service</a> and <a href="<?php echo home_url(); ?>/api-documentation/legal/privacy-policy/">Privacy Policy</a>.</p>
                <a href="javascript:;" class="btn-submit">Sign up</a>
                <!--<p>Already have an account? <a href="#">Sign in</a></p>-->
            </form>
        </div>
        <!--<a href="<?php echo home_url(); ?>" class="link-home">Back to Home</a>-->
    </div>
</div>
</div>

<script type="text/javascript">

function validateEmail(email) 
{
    var re = /\S+@\S+\.\S+/;
    return re.test(email);
}

jQuery( document ).ready(function() {
    var w = jQuery(window).width();
    if ( w > 320 ) {
        jQuery('.signup-wrapper select option').eq(0).html('Are you a Developer or Company?');
    } else {
        jQuery('.signup-wrapper select option').eq(0).html('Select');
    }
    jQuery('.signup-wrapper .btn-submit').on('click', function(){
        var has_error = false;
        if ( jQuery('#firstname').val() == "" ) {
            jQuery('#firstname').parent().find('p.error').html('First name is required');
            jQuery('#firstname').parent().find('p.error').css('display', 'block');
            jQuery('#firstname').addClass('error');
            has_error = true;
        }
        if ( jQuery('#email').val() == "" || !validateEmail(jQuery('#email').val()) ) {
            jQuery('#email').parent().find('p.error').html('Email is not valid');
            jQuery('#email').parent().find('p.error').css('display', 'block');
            jQuery('#email').addClass('error');
            has_error = true;
        }
        if ( jQuery('#btype').val() == "" ) {
            jQuery('#btype').parent().find('p.error').html('Please select wether you are a developer or company');
            jQuery('#btype').parent().find('p.error').css('display', 'block');
            jQuery('#btype').addClass('error');
            has_error = true;
        }
        if ( !has_error ) {
            jQuery('#frm-signup').submit();
        }
    });
    jQuery('.signup-wrapper .form-field').on('click', function(){
        jQuery(this).parent().find('p.error').css('display', 'none');
        jQuery(this).removeClass('error');
    })
});
</script>

<?php get_footer(); ?>
