<?php
/*
Template Name: Home
*/
get_header(); ?>

<?php //echo do_shortcode('[owl-carousel category="home" items="1" autoPlay="true" stopOnHover="true" animateOut="fadeOut" pagination="false" singleItem="true"]'); ?>

<?php echo do_shortcode('[add-static-block type="home-top-block"]'); ?>
<?php echo do_shortcode('[add-static-block type="centro-educativo-block"]'); ?>
<?php echo do_shortcode('[add-static-block type="consultas-externas-block"]'); ?>
<?php echo do_shortcode('[add-static-block type="testimonial-block"]'); ?>
<?php echo do_shortcode('[add-static-block type="equipo-medico-block"]'); ?>
<?php echo do_shortcode('[add-static-block type="especialidades-block"]'); ?>

<div class="home-top-banner full-image-bg section">
    <div class="row">
        <div class="banner-content large-7 medium-8 columns large-text-left small-text-center paddingR30">
            <h1><span>Security API</span> for <br>App Developers</h1>
            <h5 class="hide-for-small">We see a world where people aren’t afraid of<br/>clicking on the wrong link. </h5>
            <p class="hide-for-small" style="padding-right: 20px;">If users can open web links inside your app, <br/>our API will protect them from Phishing, <br/>Malware and Pornography.</p>
            <a href="<?php echo home_url(); ?>/signup" class="btn-start">Get Started for Free</a>
        </div>
    </div>
</div>

<div id="the-api" class="section contrib-wrapper">
    <div class="row">
        <div class="large-12 medium-12 columns large-text-left small-text-center">
            <h3 class="seq-special">THE</h3>
        </div>
        <div class="large-6 medium-6 columns large-text-left small-text-center">
            <h2><span class="orange">METACERT</span> API</h2>
            <p>With just six lines of code, our Security API takes less than an hour to integrate. It provides a light, but powerful layer of security to your app by providing you with intelligence on 10 BILLION URLs so you can protect users from Phishing, Malware and Pornography. </p>
            <p>You only need to write a few lines of parsing code so your app knows how to handle classified URLs. And we'll help you with that if necessary. Integrate our API now and we'll give you 150 link validations every month, forever. No contract. No credit card. </p>
            <a href="<?php echo home_url(); ?>/api-documentation/getting-started/" class="link-get-start">API Documentation</a>
        </div>
        <div class="large-6 medium-6 columns large-text-left small-text-center">
            <div class="pink-box founder-wrapper row">
                <div class="large-8 medium-8 columns no-padding">
                    <p>“It took longer to write the press release than it did to integrate the MetaCert API with our platform. And we started to generate a new revenue stream within 24 hours.”</p>
                    <h6>Founder & CEO, AppMakr</h6>
                </div>
                <div class="large-4 medium-4 columns small-text-center">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/img/app-makr-ceo.png" />
                </div>
            </div>
            <div class="pink-box appmakr row">
                <div class="large-4 medium-4 columns small-text-center pull-right">
                    <a href="http://appmakr.com" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/assets/img/app-makr-logo.png" /></a>
                </div>
                <div class="large-8 medium-8 columns paddingL0 paddingR30">
                    <p>With over <span>1.8 million</span> apps created on the AppMakr platform and thousands more being created daily AppMakr is the largest DIY publisher of mobile apps in the world.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="home-middle-banner full-image-bg section">
    <div class="row">
        <div class="banner-content large-12 medium-12 columns">
            <div class="row">
                <div class="large-5 medium-6 columns large-text-left small-text-center">
                    <h2><b>10 BILLION</b><b class="small-font">URLs classified</b><span>& growing <br>hourly</span><b class="small-font">lookup</b><span>60 Categories<br>in<br>250ms</span>.</h2>
                </div>
                <div class="large-4 medium-4 columns large-text-left small-text-center no-padding">
                    <p>As a Developer you benefit from the World’s biggest database of categorized URLs & IP addresses.</p>
                    <p>One API for everything. Subscribe to additional categories later without having to update the API.</p>
                    <ul style="margin-left:0px;">
                        <li>60 categories including...</li>
			<li><span>Phishing & Malware</span></li>
			<li><span>Pornography</span></li> 
		        <li><span>Gambling</span></li> 
		        <li><span>Violence</span></li>	
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="uses" class="section potential-block">
    <div class="row">
        <div class="large-12 medium-12 columns">
            <h3 class="title small-text-center large-10 large-offset-1 columns">WHAT ARE THE POTENTIAL APPLICATIONS FOR <span>THE METACERT SECURITY API</span> ?</h3>
            <div class="row">

                <div class="large-6 medium-6 columns large-text-left small-text-center">
                    <div class="row">
                        <div class="large-2 medium-2 columns">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/oem-icon-light1.png" />
                        </div>
                        <div class="large-10 medium-10 columns paddingR35">
                            <h5>Team Collaboration</h5>
			    <p>Our <a href="https://marketplace.atlassian.com/plugins/com.atlassian.hipchat.metacert.2/cloud/overview" target="_blank">Security Integration for HipChat</a> is a perfect example to demonstrate how Team Collaboration and Enterprise Communication Services benefit from our API service. The Security Integration silently monitors messages inside rooms, warning users when a web link has been classified as malicious or inappropriate for work. </p>
                        </div>
                    </div>
                </div>
								
				
                <div class="large-6 medium-6 columns large-text-left small-text-center">
                    <div class="row">
                        <div class="large-2 medium-2 columns">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/dev-icon-light1.png" />
                        </div>
                        <div class="large-10 medium-10 columns">
                            <h5>Developers</h5>
                            <p>Whether you’re building a messaging app for your own company, or on an enterprise app for a client, end-users are not safe from malicious links inside your app if it has the ability to display web content.The MetaCert Security API is a low-cost security solution that can only increase your clients’ or end-users’ trust in your brand.</p>
                        </div>
                    </div>
                </div>
				<!--
                <div class="large-6 medium-6 columns large-text-left small-text-center">
                    <div class="row">
                        <div class="large-2 medium-2 columns">
                            <img src="<?php //echo get_template_directory_uri(); ?>/assets/img/pornblock-icon-light1.png" />
                        </div>
                        <div class="large-10 medium-10 columns paddingR35">
                            <h5>Porn-blocking Apps</h5>
                            <p>If you’re building an mobile app or hardware device that allows end-users to block pornography, the MetaCert Security API is perfect for you. MetaCert is the first company in the world to classify down to the folder-level. This means the Security API catches hundreds of millions of URLs that our competitors fail to block.</p>
                        </div>
                    </div>
                </div>
				-->	
            </div>
            <div class="row marginT20">

                <div class="large-6 medium-6 columns large-text-left small-text-center">
                    <div class="row">
                        <div class="large-2 medium-2 columns">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/pornblock-icon-light1.png" />
                        </div>
                        <div class="large-10 medium-10 columns paddingR35">
                            <h5>Porn-blocking Apps</h5>
                            <p>If you’re building an mobile app or hardware device that allows end-users to block pornography, the MetaCert Security API is perfect for you. MetaCert is the first company in the world to classify down to the folder-level. This means the Security API catches hundreds of millions of URLs that our competitors fail to block.</p>
                        </div>
                    </div>
                </div>
								
                <div class="large-6 medium-6 columns large-text-left small-text-center">
                    <div class="row">
                        <div class="large-2 medium-2 columns">
                            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/api-icon-light1.png" />
                        </div>
                        <div class="large-10 medium-10 columns">
                            <h5>App & API Platforms</h5>
                            <p>Over 50% of apps built on the AppMakr Platform subscribed to at least one MetaCert Security Service. Of those, over 80% subscribed to both. By offering the Security API to your developer community you benefit from a new monthly recurring revenue stream. You also stand to benefit from positive PR for offering in-app security on your platform. Our API is extremely easy to integrate and we’ll help you along the way.</p>
                        </div>
                    </div>
                </div>
				<!--
                <div class="large-6 medium-6 columns large-text-left small-text-center">
                    <div class="row">
                        <div class="large-2 medium-2 columns">
                            <img src="<?php //echo get_template_directory_uri(); ?>/assets/img/oem-icon-light1.png" />
                        </div>
                        <div class="large-10 medium-10 columns paddingR40">
                            <h5>OEMS</h5>
                            <p>Anti-virus apps do not protect your consumers from phishing attacks that take place inside apps, browsers or SMS. And this type of attack is responsible for the spread of most mobile malware today.</p>
                            <p>By protecting your consumers with the MetaCert enterprise grade Security API, you can promote your mobile device as more suitable for BYOD policies and enterprise solutions.</p>
                        </div>
                    </div>
                </div>
				-->	
           
		   
		   
		   
		    </div>
        </div>
    </div>
</div>

<div class="section mobile-gift-wrapper show-for-small">
    <div class="row">
        <div class="large-12 medium-12 columns small-text-center gift-wrapper">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/gift-mobile.png" />
            <h6>Get 150 <b>FREE</b> calls <b>EVERY</b> month</h6>
            <a class="btn-start" href="<?php echo home_url(); ?>/signup">YES, Sign me up!</a>
        </div>
    </div>
</div>

<div id="pricing" class="section black-bg price-block">
    <div class="row">
        <div class="large-12 medium-12 columns small-text-center">
            <h3 class="title white">Pricing plans for developers</h3>
        </div>
        <div class="large-4 medium-4 columns small-text-center">
            <div class="price-box">
                <i class="mark-malware"></i><i class="mark-pornblock"></i>
                <h5>Malware & Phishing AND Pornography Filter</h5>
                <p>Full protection from 10 billion+ URLs of malware, phishing and pornography.</p>
                <div class="fee-wrapper" style="display:none;">
                    <div class="month">
                        <p>$<span>10</span>/ mo</p>
                    </div>
                    <div class="year">
                        <p>$<span>99</span>/ year</p>
                    </div>
                </div>
				<?php /*
                <div class="btn-wrapper">
                    <div class="row">
                        <div class="large-6 medium-6 small-6 columns">
                            <?php echo do_shortcode('[stripe amount="1000" payment_button_label="Monthly"][/stripe]'); ?>
                            <!--<a href="javascript:;" class="btn-standard btn-plan">Monthly</a>-->
                        </div>
                        <div class="large-6 medium-6 small-6 columns">
                            <?php echo do_shortcode('[stripe amount="9900" payment_button_label="Annual"][/stripe]'); ?>
                            <!--<a href="javascript:;" class="btn-standard btn-plan">Annual</a>-->
                        </div>
                    </div>
                </div>
				*/ ?>
				<a href="https://metacert.com/signup" class="btn-start">Get Started for Free</a>
                <!--<a href="javascript:;" class="btn-standard btn-plan">Choose this plan</a>-->
            </div>
        </div>
        <div class="large-4 medium-4 columns small-text-center">
            <div class="price-box">
                <i class="mark-malware"></i>
                <h5>Malware & Phishing</h5>
                <p>Keep your end-users safe by preventing them from unintentionally accessing sites known for phishing, malware or viruses (including links from pages that your app links to). Promote your app as “Protected by MetaCert”.</p>
                <div class="fee-wrapper" style="display:none;">
                    <div class="month">
                        <p>$<span>7</span>/ mo</p>
                    </div>
                    <div class="year">
                        <p>$<span>70</span>/ year</p>
                    </div>
                </div>
				<?php /*
                <div class="btn-wrapper">
                    <div class="row">
                        <div class="large-6 medium-6 small-6 columns">
                            <?php echo do_shortcode('[stripe amount="700" payment_button_label="Monthly"][/stripe]'); ?>
                            <!--<a href="javascript:;" class="btn-standard btn-plan">Monthly</a>-->
                        </div>
                        <div class="large-6 medium-6 small-6 columns">
                            <?php echo do_shortcode('[stripe amount="7000" payment_button_label="Annual"][/stripe]'); ?>
                            <!--<a href="javascript:;" class="btn-standard btn-plan">Annual</a>-->
                        </div>
                    </div>
                </div>
				*/ ?>
				<a href="https://metacert.com/signup" class="btn-start">Get Started for Free</a>
                <!--<a href="javascript:;" class="btn-standard btn-plan">Choose this plan</a>-->
            </div>
        </div>
        <div class="large-4 medium-4 columns small-text-center">
            <div class="price-box third">
                <i class="mark-pornblock"></i>
                <h5>Pornography Filter</h5>
                <p>Added protection to ensure that known pornographic pages are never displayed in your app by any websites that you link to, or secondary sites that they link to. Promote your app as “Protected by MetaCert”. </p>
                <div class="fee-wrapper" style="display:none;">
                    <div class="month">
                        <p>$<span>7</span>/ mo</p>
                    </div>
                    <div class="year">
                        <p>$<span>70</span>/ year</p>
                    </div>
                </div>
				<?php /*
                <div class="btn-wrapper">
                    <div class="row">
                        <div class="large-6 medium-6 small-6 columns">
                            <?php echo do_shortcode('[stripe amount="700" payment_button_label="Monthly"][/stripe]'); ?>
                            <!--<a href="javascript:;" class="btn-standard btn-plan">Monthly</a>-->
                        </div>
                        <div class="large-6 medium-6 small-6 columns">
                            <?php echo do_shortcode('[stripe amount="7000" payment_button_label="Annual"][/stripe]'); ?>
                            <!--<a href="javascript:;" class="btn-standard btn-plan">Annual</a>-->
                        </div>
                    </div>
                </div>
				*/ ?>
				<a href="https://metacert.com/signup" class="btn-start">Get Started for Free</a>
                <!--<a href="javascript:;" class="btn-standard btn-plan">Choose this plan</a>-->
            </div>
        </div>
        <div class="large-12 medium-12 columns small-text-center partnership-title">
            <h3 class="title white">Partnership Opportunities</h3>
        </div>
        <div class="large-4 medium-4 columns small-text-center">
            <div class="partnership-box">
                <i class="mark-porn"></i>
                <h5>PORN-BLOCKING APPS</h5>
                <p>MetaCert’s low-cost enterprise API is the backbone to some of the most widely used parental control applications on the market. We built a database of over 10 billion URLs so you don’t have to.<br>Start with a free trial and only pay as you grow your customer base.
                <span class="notice">Get in touch to discuss your requirements in more detail.</span></p>
                <a href="mailto:partners@metacert.com?subject=App Partnership Enquiry" class="btn-standard btn-contact">Contact us</a>
            </div>
        </div>
        <div class="large-4 medium-4 columns small-text-center">
            <div class="partnership-box">
                <i class="mark-platform"></i>
                <h5>platforms</h5>
                <p>Offering your app developers a the Security API is guaranteed to open up a new stream of recurring revenue each month. Integrate a few lines of code with absolutely no disruption to your own build or sales cycle and we’ll share the revenue.
                <span class="notice">Get in touch to discuss your requirements in more detail.</span></p>
                <a href="mailto:partners@metacert.com?subject=Platform Partnership Enquiry" class="btn-standard btn-contact">Contact us</a>
            </div>
        </div>
        <div class="large-4 medium-4 columns small-text-center">
            <div class="partnership-box">
                <i class="mark-oems"></i>
                <h5>OEMS</h5>
                <p>Protect your customers from phishing attacks and mobile malware before they even open a browser or app. Take a proactive approach to security and integrate the MetaCert Enterprise API.
                <span class="notice">Get in touch to discuss your requirements in more detail.</span></p>
                <a href="mailto:partners@metacert.com?subject=OEM Partnership Enquiry" class="btn-standard btn-contact">Contact us</a>
            </div>
        </div>
        <div class="large-12 medium-12 columns small-text-center gift-wrapper hide-for-small">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/img/gift-icon1.png" class="hide-for-small"/>
            <h6>Get 150 <b>FREE</b> calls <b>EVERY</b> month</h6>
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
