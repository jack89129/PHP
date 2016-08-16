<?php 
/*
Template Name: Register Page Template
*/

// Register user
if ( !empty($_POST) ) {
    $firstname = $_REQUEST['firstname'];
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

      // Email the user
      wp_mail( $email, 'Welcome!', 'Your Password: ' . $password );
      wp_safe_redirect( home_url() . '/success?addr=' . base64_encode($email) );
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

get_header(); ?>

<div class="signup-wrapper section black-bg">
<div class="row">
    <div class="large-6 medium-8 columns small-text-center gift-wrapper large-offset-3 medium-offset-2 no-margin">
        <h6>Malware & Phishing AND Pornography Filter</h6>
        <p>This is a secure connection</p>
        <div class="form-wrapper large-text-left small-text-left">
            <form id="frm-signup" method="post" action="">
                <div class="row">
                    <div class="error-wrapper" <?php if ( !empty($error_msg) ) { echo 'style="display: block"'; } ?>><?=$error_msg?></div>
                    <div class="large-6 medium-6 columns">
                        <div class="field-wrapper">
                            <p class="error"></p>
                            <input type="text" class="form-field" id="firstname" name="firstname" placeholder="First Name" value="<?=$firstname?>" / >
                        </div>
                    </div>
                    <div class="large-6 medium-6 columns">
                        <div class="field-wrapper">
                            <p class="error"></p>
                            <input type="text" class="form-field" id="lastname" name="lastname" placeholder="Last Name" value="<?=$lastname?>" / >
                        </div>
                    </div>
                    <div class="columns">
                        <div class="field-wrapper">
                            <p class="error"></p>
                            <input type="email" class="form-field" id="email" name="email" placeholder="Email" value="<?=$email?>"/>
                        </div>
                    </div>
                    <div class="columns">
                        <div class="field-wrapper">
                            <p class="error"></p>
                            <input type="text" class="form-field ico-card" id="card_num" name="card_num" placeholder="Card Number" value="<?=$card_num?>"/>
                        </div>
                    </div>
                    <div class="large-6 medium-6 columns">
                        <div class="row">
                            <div class="large-6 medium-6 columns">
                                <div class="field-wrapper">
                                    <p class="error"></p>
                                    <input type="text" class="form-field" id="card_date" name="card_date" placeholder="MM/YY" value="<?=$card_date?>"/>
                                </div>
                            </div>
                            <div class="large-6 medium-6 columns">
                                <div class="field-wrapper">
                                    <p class="error"></p>
                                    <input type="text" class="form-field" id="card_cvc" name="card_cvc" placeholder="CVC" value="<?=$card_cvc?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="large-6 medium-6 columns">
                        <a href="javascript:;" class="btn-type active">Monthly</a>
                    </div>
                    <div class="large-6 medium-6 columns">
                        <a href="javascript:;" class="btn-type">Annual</a>
                    </div>
                    <div class="columns">
                        <div class="price-wrapper">
                            Total: $<span class="total-price">10</span>
                        </div>
                    </div>
                    <div class="columns">
                        <a href="javascript:;" class="btn-submit">Buy Now</a>
                        <!--<p>Already have an account? <a href="#">Sign in</a></p>-->
                    </div>
                </div>
            </form>
        </div>
        <div class="signup-footer">
            <p>You agree to the MetaCert <a href="<?php echo home_url(); ?>/api-documentation/legal">Terms of Service</a> and <a href="<?php echo home_url(); ?>/api-documentation/legal/privacy-policy/">Privacy Policy</a>.</p>
        </div>
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
        if ( jQuery('#lastname').val() == "" ) {
            jQuery('#lastname').parent().find('p.error').html('Last name is required');
            jQuery('#lastname').parent().find('p.error').css('display', 'block');
            jQuery('#lastname').addClass('error');
            has_error = true;
        }
        if ( jQuery('#email').val() == "" || !validateEmail(jQuery('#email').val()) ) {
            jQuery('#email').parent().find('p.error').html('Email is not valid');
            jQuery('#email').parent().find('p.error').css('display', 'block');
            jQuery('#email').addClass('error');
            has_error = true;
        }
        /*if ( jQuery('#btype').val() == "" ) {
            jQuery('#btype').parent().find('p.error').html('Please select wether you are a developer or company');
            jQuery('#btype').parent().find('p.error').css('display', 'block');
            jQuery('#btype').addClass('error');
            has_error = true;
        }*/
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