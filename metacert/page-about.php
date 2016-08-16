<?php
/*
Template Name: About
*/
get_header(); ?>

<div class="about-top-banner full-image-bg section about-content-block">
    <div class="row">
        <div class="large-6 medium-6 small-12 columns large-text-left small-text-center">
            <h1>About MetaCert</h1>
            <p>With headquarters in San Francisco, <b>MetaCert</b> is the first and only company to provide a security solution that protects consumers from mobile malware and phishing attacks on the app-layer.</p>
            <p><b>MetaCert</b> is also the first and only company to provide a Security API to help developers stop pornography from appearing inside their apps.</p>
        </div>
        <div class="large-6 medium-6 small-12 columns large-text-right small-text-center">
            <h1 class="uppercase">10 <span class="orange">BILLION</span></h1>
            <p class="no-margin">URLs classified</p>
        </div>
    </div>
</div>

<div class="section about-content-block">
    <div class="row">
        <div class="large-8 medium-8 small-12 columns large-text-left small-text-center large-offset-2 medium-offset-2">
            <p class="no-margin hide-for-small">MORE ABOUT</p>
            <h3 class="hide-for-small">WHAT <span class="orange">WE</span> DO</h3>
            <h3 class="show-for-small">MORE <span class="orange">ABOUT</span><br>WHAT WE DO</h3>
            <p>Whether it’s a mobile app or an IoT device, if it communicates over http, the MetaCert Security API can add a thin but very powerful layer of security to help protect consumers from malicious attacks over the Internet.</p>
            <p>MetaCert’s patent-pending Security API checks the reputation of http requests in real time, warning consumers of potential threats before allowing known malicious web pages (or other resources) from loading inside the app. MetaCert also offers a content-based filtering service for developers that want to block pornography inside their apps.</p>
        </div>
    </div>
</div>

<div class="section about-content-block metacert-block-section">
    <div class="row">
        <div class="large-8 medium-8 small-12 columns large-text-left small-text-center large-offset-2 medium-offset-2 paddingR30">
            <h3 class="white">OUR <span class="orange">STORY</span>?</h3>
<?php echo do_shortcode('[add-static-block type="our-story"]'); ?>
<!--            <p class="gray">MetaCert is a social enterprise. We care about the impact we have on people’s lives and we care about the impact we  ave on our environment. We’re also friendly and easy to work with.</p>
			<p class="gray">We are also the first company in the world to provide an in-app security solution that protects consumers from phishing attacks and category-based content filtering. Most devices are infected with malware and adware when consumers follow malicious links that lead to malicious apps being installed in the background. So being the first security company to address this specific problem is a pretty big deal for an early-stage startup.</p>
-->
        </div>
    </div>
</div>

<div class="section about-content-block team-block-section">
    <div class="row">
        <h3 class="white text-center">MEET OUR <span class="orange">TEAM</span></h3>
        <div class="large-10 medium-12 large-offset-1 columns">
            <div class="row">
                <ul class="small-block-grid-2 medium-block-grid-5 large-block-grid-5 team-block">
<?php /* Get Team Members */
$args = array(
            'post_type' => 'team_member',
            'order' => 'ASC',
            'orderby' => 'post_date',
            'posts_per_page' => '-1'
        );
$the_query = new WP_Query($args);
// The Loop
if ( $the_query->have_posts() ) {
    while ( $the_query->have_posts() ) {
        $the_query->the_post();
?>
        <li>
            <a class="link-member" href="javascript:;" pid="member<?php echo get_the_ID(); ?>">
                <?php the_post_thumbnail(); ?>
                <p class="profile-name"><?php echo get_the_title() ?></p>
                <p class="profile-level"><?php echo get_the_excerpt(); ?></p>
            </a>
        </li>
<?php
    }
} else {
    // no posts found
}
/* Restore original Post Data */
wp_reset_postdata();
?>
                </ul>
            </div>
            <div class="row member-page-wrapper">
                <div class="large-12 medium-12 small-12 columns small-text-center">
                <?php 
                $the_query = new WP_Query($args);
                // The Loop
                if ( $the_query->have_posts() ) {
                    while ( $the_query->have_posts() ) { $the_query->the_post(); ?>
                    <div id="member<?php the_ID(); ?>" style="display: none;" class="member-body">
                        <div class="photo-wrapper">
                            <?php the_post_thumbnail(); ?>
                            <h6 class="profile-name"><?php echo get_the_title() ?></h6>
                            <?php $cat = get_the_category(); ?>
                            <p><?php echo $cat[0]->name; ?></p>
                        </div>
                        <div class="entry-content large-text-left medium-text-left small-text-left">
                            <?php the_content(); ?>
                        </div>
                    </div>
                <?php
                    }
                } else {
                    // no posts found
                }
                /* Restore original Post Data */
                wp_reset_postdata();
                ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section about-content-block">
    <div class="row">
        <div class="large-8 medium-8 small-12 columns large-text-left small-text-center large-offset-2 medium-offset-2">
            <h3>OUR <span class="orange">INVESTORS</span></h3>
<?php echo do_shortcode('[add-static-block type="our-investors"]'); ?>
<!--            <p>MetaCert has raised $1.2M in seed funding from angel investors across the US, Canada, UK, Dubai, Abu Dhabi, Russia and Ireland.</p>
            <p>Our team of individual investors include the founding CEO, CTO, Head of Operations, Engineer 1, Engineer 2 and the two first investors from mobile video company Qik (Acquired by Skype).</p> -->
        </div>
    </div>
</div>

<div class="section mobile-gift-wrapper show-for-small">
    <div class="row">
        <div class="large-12 medium-12 columns small-text-center gift-wrapper">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/gift-mobile.png" />
            <h6>Get 150 <b>FREE</b> API calls <b>EVERY</b> month</h6>
            <a class="btn-start" href="<?php echo home_url(); ?>/signup">YES, Sign me up!</a>
        </div>
    </div>
</div>

<div class="section black-bg price-block hide-for-small">
    <div class="row">
        <div class="large-12 medium-12 columns small-text-center gift-wrapper no-margin">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/gift-icon1.png" />
            <h6>Get 150 <b>FREE</b> API calls <b>EVERY</b> month</h6>
            <a class="btn-start" href="<?php echo home_url(); ?>/signup">YES, Sign me up!</a>
        </div>
    </div>
</div>

<div class="to-top-wrapper">
  <div class="row">
    <a href="javascript:;" class="move-to-top"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/back-to-top.png"></a>
  </div>
</div>

<?php get_footer(); ?>
