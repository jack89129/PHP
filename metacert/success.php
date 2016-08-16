<?php 
/*
Template Name: Success Page Template
*/

get_header(); 

// Get Subscriber Email Address
$addr = base64_decode($_REQUEST['addr']);
$key = base64_decode($_REQUEST['key']);
?>

<div class="success-wrapper section">
<div class="row">
    <div class="large-12 columns small-text-center">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/check.png" />
        <h3>Your registration as been successful.</h3>
        <h4>Your API key is <?=$key?><br>
We recommend you read <a href="https://metacert.com/api-documentation/getting-started/">Getting Started with MetaCert</a>
<br>Feel free to email us if you have any questions <a href="mailto:partners@metacert.com">partners@metacert.com</a>
</h4>
        <!--<a href="http://plaza.mimbio.es/metacert" class="link-home">Back to Metacert.com</a>-->
    </div>
</div>
</div>

<?php get_footer(); ?>