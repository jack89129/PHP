// building select nav for mobile width only
$(function(){
	// building h6 item to toggle the main nav menu
	$('<h6 class="open_nav">Top Nav</h6>').appendTo('#topHeader div.sixteen');

	$('#topHeader h6').click(
		function(){
			$('#topHeader #topNav').slideToggle(150);
		}
	);

});
//end



// building select nav for mobile width only
$(function(){
	
	// building h6 item to toggle the main nav menu
	$('<h6 class="open_nav">Nav Items</h6>').appendTo('#mainNav nav');

	$('#mainNav nav h6').click(
		function(){
			$('#mainNav nav > ul').slideToggle(150);
		}
	);

});
//end


/*============= cart content slide effect =================*/
$(function(){
	$('#mainNav #cart').mouseenter(
		function(){
			//$(this).children('div.cart_content').slideDown(160);
            $(this).children('div.cart_content').css('display', "block");
		})
        .mouseleave(
		function(){
			//$(this).children('div.cart_content').slideUp(160);
            $(this).children('div.cart_content').css('display', "none");
		}        
	);
});


/*======= end it ===========*/


/*===================== sub menu slide ===============*/
$(function(){
	$('nav ul li').hover(
		function () {
			// hide the css default behavir
			$(this).children('ul.submenu').css('display', 'none');
			//show its submenu
			$(this).children('ul.submenu').slideDown(150);
			$(this).children('div.dropdown_3columns').slideDown(150);
		}, 
		function () {
			//hide its submenu
			$(this).children('ul.submenu').slideUp(150);	
			$(this).children('div.dropdown_3columns').slideUp(150);		
		}
	);
});
/*==================== end it ===================*/


/*========= dropkick plugin for select form =============*/
$(function(){
	$(".default").dropkick();
});
/*======== end it ==========*/


/*============ delete item from dropdown cart ========*/
$(function(){
	$('#mainNav #cart div.cart_content ul li').find('a.remove_item').click(
		function(e){
			e.preventDefault();
            $(this).parents('li')
            .animate({ "opacity": "hide" }, "slow");
            var pid = $(this).parents('li').attr('title');
            $.post('http://www.avaxo.be/shop/remove', {id:pid}).success(function(data){
                $('#cart_dropdown_total').html(': €'+data.total_price.toFixed(2));
                var vat = data.total_price*0.21;
                var total = data.total_price*1.21;
                $('#cart_dropdown_total_bottom').html('Subtotaal : €' + data.total_price.toFixed(2) + '<br/>BTW : €' + vat.toFixed(2) + '<br/>Totaal : €'+total.toFixed(2));
                var pCount = parseInt($('#cart_dropdown_count').html());
                $('#cart_dropdown_count').html(pCount-1);
            });
		}
	);
});
/*======= end it =========*/

/*============ delete item from shopping cart table (cart.html)  ========*/
$(function(){
	$('table.cart_table tbody td').find('a.delete_item').click(
		function(e){
			e.preventDefault();
            
			if(confirm("Are you sure?")){
				$(this).parents('tr')
				.animate({ "opacity": "hide" }, "slow");
                var pid = $(this).parents('tr').attr('title');
                $.post('http://www.avaxo.be/shop/remove', {id:pid}).success(function(data){
                    $('#cart_sub_total').html('€'+data.total_price.toFixed(2));
                    $('#cart_total').html('€'+data.total_price.toFixed(2));
                });
			}
		}
	);
});
/*======= end it =========*/



// display your twiter feed here
$(function(){
    $(".tweet").tweet({
       username: "seaofclouds",
        join_text: "auto",
        avatar_size: 0,
        count: 1,
        auto_join_text_default: "we said,", 
        auto_join_text_ed: "we",
        auto_join_text_ing: "we were",
        auto_join_text_reply: "we replied to",
        auto_join_text_url: "we were checking out",
        loading_text: "loading tweets..."
    });
      
});//end


/*============== home page accrdain ==============*/
$(function(){
	
	$(".home_news h3").eq(2).addClass("active");
	$(".home_news div").hide();
	$(".home_news div").eq(2).show();
	
	$(".home_news h3").click(function(){
		$(this).next("div").slideToggle("fast").siblings("div:visible").slideUp("fast");
		$(this).toggleClass("active");
		$(this).siblings("h3").removeClass("active");
	});

});
/*=========== end it ===========*/



/*=========== scrollbar ===============*/
$(function(){
	// scroll for the html, body tags
	$('html').niceScroll({
		cursorcolor:"#f9f9f9",
		background: "#d7d7d7",
		cursorborder:"1px solid #d7d7d7",
		cursoropacitymin : 1,
		cursorwidth : 8,
		cursorborderradius :0
	});

	// custom scroll for the tabs in product page
	$('div.product_tabs #tabs div.tabContent').niceScroll({
		cursorcolor:"#f9f9f9",
		background: "#d7d7d7",
		cursorborder:"1px solid #d7d7d7",
		cursoropacitymin : 1,
		cursorwidth : 8,
		cursorborderradius :0
	});

	// custom scroll for the accordian in product page2
	$('.product_info div.acc, .product_review div.acc').niceScroll({
		cursorcolor:"#f9f9f9",
		background: "#d7d7d7",
		cursorborder:"1px solid #d7d7d7",
		cursoropacitymin : 1,
		cursorwidth : 8,
		cursorborderradius :0
	});

});

/*========== end them ==============*/



/*============== product page two accrdain ==============*/
$(function(){
	
	$(".product_info h6").eq(0).addClass("active");
	$(".product_info div.acc").hide();
	$(".product_info div.acc").eq(0).show();
	
	$(".product_info h6").click(function(){
		$(this).next("div.acc").slideToggle("fast").siblings("div:visible").slideUp("fast");
		$(this).toggleClass("active");
		$(this).siblings("h6").removeClass("active");
	});

});
/*=========== end it ===========*/


/*============== product page two accrdain ==============*/
$(function(){
	
	$(".product_review h6").eq(0).addClass("active");
	$(".product_review div.acc").hide();
	$(".product_review div.acc").eq(0).show();
	
	$(".product_review h6").click(function(){
		$(this).next("div.acc").slideToggle("fast").siblings("div:visible").slideUp("fast");
		$(this).toggleClass("active");
		$(this).siblings("h6").removeClass("active");
	});

});
/*=========== end it ===========*/



/*========== slide the related product in the product page ===========*/
$(function(){

	var viewWidth = $(window).width(); 

	if(viewWidth  <= 940 & viewWidth > 768 ) {
		$("div.pro_relates_content").jCarouselLite({
		   btnNext: ".pagers .related_nxt",
		   btnPrev: ".pagers .related_prev",
		   visible: 3
		});

	} else if(viewWidth <= 767 & viewWidth > 479) {
		$("div.pro_relates_content").jCarouselLite({
		   btnNext: ".pagers .related_nxt",
		   btnPrev: ".pagers .related_prev",
		   visible: 2
		});

	} else if(viewWidth <= 479) {
		$("div.pro_relates_content").jCarouselLite({
		   btnNext: ".pagers .related_nxt",
		   btnPrev: ".pagers .related_prev",
		   visible: 1
		});
	} else {
		$("div.pro_relates_content").jCarouselLite({
		   btnNext: ".pagers .related_nxt",
		   btnPrev: ".pagers .related_prev",
		   visible: 4
		});
	}
});
/*=========== end it ===========*/ 


/*================= product images hover effect ===============*/
$(function(){

	$('ul.product_show li').hover(

		function(){
			$(this).children('div.img').children('a').children('img').stop().animate({width:'130%', height:'130%', left:-30, top:-30}, 300);
			$(this).children('div.img').children('div.hover_over').stop().animate({opacity:1},300);
			$(this).find('div.hover_over').children('a.link').stop().animate({'left':'30%'}, 300);
			$(this).find('div.hover_over').children('a.cart').stop().animate({'right':'30%'}, 300);
		},
		function(){
			$(this).children('div.img').children('a').children('img').stop().animate({width:'100%', height:'100%', left:0, top:0}, 300);
			$(this).children('div.img').children('div.hover_over').stop().animate({opacity:0},300);
			$(this).find('div.hover_over').children('a.link').stop().animate({'left':'0'}, 300);
			$(this).find('div.hover_over').children('a.cart').stop().animate({'right':'0'}, 300);
		}

	);

});
/*=========== end it =========*/


/*========== category list =============*/
$(function(){
	$('div.category ul li ul').hide();
	$('div.category ul li:has("ul") a').click(
		function(e){
            $('div.category ul li:has("ul") a').next('ul.nested').hide();
            $('div.category ul li:has("ul") a').removeClass('active');
			e.preventDefault();
			$(this).toggleClass('active');
			$(this).next('ul.nested').slideToggle(400);
		}
	);
    $('div.category ul li:has("ul") a').eq(0).click();
});
/*====== end it ======*/


/*======= Testimonial Tab in about page ==========*/
$(function(){
	$('#testimonial #tab_outer ul li a').click(function(e){
		e.preventDefault();
		$('#testimonial #tab_outer div').hide();
		$('#testimonial #tab_outer ul li').removeClass('currentTestimonial');
		$('#testimonial #tab_outer div').filter(this.hash).fadeIn(400);
		$(this).parent('li').addClass('currentTestimonial');
	}).filter(':first').click();
});
/*============== end it =============*/


/*========== side panel widget =========*/
// open and hide the side panel
$(function(){
	$('#sideWidget a.WidgetLink').click(function(e){
		e.preventDefault();
		var left = $('#sideWidget').css('left');
		if(left <= '-168px'){
			$("#sideWidget").animate({
				left: 10
			});
		}else{
			$("#sideWidget").animate({
				left: '-168px'
			});
		}
	});
});

// change the background body
$(function(){
	/* cookie vars */
	var cookie_name = "shoploop_bg";
	var cookie_options = { path: '/', expires: 30 };
	var get_cookie = $.cookie('shoploop_bg');
	if(get_cookie == null){get_cookie = 'soft_wallpaper';}

	// backgrounds
	$('#active-bg').remove();
	$('head')
	.append('<link rel="stylesheet" id="active-bg" href="css/bg/'+get_cookie+'.css">');

	$("#sideWidget div.bgPatterns").find('a').bind('click', function(e) {
		e.preventDefault();
		var bgName = $(this).text();
		$.cookie(cookie_name, bgName, cookie_options);
		$('head')
		.append('<link rel="stylesheet" id="active-bg" href="css/bg/'+bgName+'.css">');
	});
});
/*=========== end it ==========*/


/*===================== tabs in cart.html page ==================*/
$(function(){
	var tabDivs = $('div.cart_tabs div.cart_tabs_content > div');

	tabDivs.hide().filter(':first').show();
	$('div.cart_tabs ul.cart_tabs_nav li a').on('click', function(e){
		e.preventDefault();
		tabDivs.hide();
		tabDivs.filter(this.hash).fadeIn(300);
		$('div.cart_tabs ul.cart_tabs_nav li a').removeClass('active_tab');
		$(this).addClass('active_tab');

	});
});
/*========= end it =========*/


/*========== slide the brands in the about page ===========*/
$(function(){
	var viewWidth = $(window).width(); 

	if(viewWidth  <= 940 & viewWidth > 768 ) {
		$("div.brands div.brandOuter").jCarouselLite({
		   btnNext: ".pagers .brand_nxt",
		   btnPrev: ".pagers .brand_prev",
		   visible: 4
		});

	} else if(viewWidth <= 767 & viewWidth > 479) {
		$("div.brands div.brandOuter").jCarouselLite({
		   btnNext: ".pagers .brand_nxt",
		   btnPrev: ".pagers .brand_prev",
		   visible: 3
		});

	} else if(viewWidth <= 479) {
		$("div.brands div.brandOuter").jCarouselLite({
		   btnNext: ".pagers .brand_nxt",
		   btnPrev: ".pagers .brand_prev",
		   visible: 2
		});
	} else {
		$("div.brands div.brandOuter").jCarouselLite({
		   btnNext: ".pagers .brand_nxt",
		   btnPrev: ".pagers .brand_prev",
		   visible: 5
		});
	}
});
/*=========== end it ===========*/



/*========= accordain in checkout.html page ============*/
$(function(){
	$('div.checkout_outer div.checkout_content').hide();
	/*$('div.checkout_outer h2').eq(0).click(
		function(){
            $('div.checkout_outer h2').next('div.checkout_content').slideUp();
			$(this).next('div.checkout_content').slideToggle(300);
		}
	);
    $('div.checkout_outer h2').eq(0).next('div.checkout_content').slideToggle(300);*/
});
/*=========== end it ============*/



/*========= tabs in blog and post and Product page ==========*/
$(function(){
	var tabDivs = $('#tabs #tabContentOuter > div');

	tabDivs.hide().filter(':first').show();
	$('#tabs ul.tabNav li a').on('click', function(e){
		e.preventDefault();
		tabDivs.hide();
		tabDivs.filter(this.hash).fadeIn(300);
		$('#tabs ul.tabNav li a').removeClass('currentTab');
		$(this).addClass('currentTab');

	});
});
/*=========== end them ============*/


/*==========zommed image with etalage plugin in product page=====*/
$(function(){

	var viewWidth = $(window).width(); 

	if(viewWidth  <= 959 & viewWidth > 768 ) {
		$('#etalage').etalage({
			thumb_image_width: 445,
			thumb_image_height: 480,
			source_image_width: 900,
			source_image_height: 1200,
			zoom_area_width: 267,
			zoom_area_height: 495,
			zoom_area_distance: 20,
			small_thumbs: 4,
			smallthumb_inactive_opacity: 1,
			smallthumbs_position:'bottom',
			autoplay_interval : 3000,
			right_to_left: true
		});

	} else if(viewWidth <= 767 & viewWidth > 479) {
		$('#etalage').etalage({
			thumb_image_width: 400,
			thumb_image_height: 360,
			source_image_width: 900,
			source_image_height: 1200,
			zoom_area_width: 340,
			zoom_area_height: 495,
			zoom_area_distance: 20,
			small_thumbs: 4,
			smallthumb_inactive_opacity: 1,
			smallthumbs_position:'bottom',
			autoplay_interval : 3000,
			zoom_element: '#zoom',
			right_to_left: true
		});

	} else if(viewWidth <= 479) {
		$('#etalage').etalage({
			thumb_image_width: 290,
			thumb_image_height: 240,
			source_image_width: 900,
			source_image_height: 1200,
			zoom_area_width: 340,
			zoom_area_height: 495,
			zoom_area_distance: 20,
			small_thumbs: 3,
			smallthumb_inactive_opacity: 1,
			smallthumbs_position:'bottom',
			autoplay_interval : 3000,
			zoom_element: '#zoom',
			right_to_left: true
		});
	} else {
		$('#etalage').etalage({
			thumb_image_width: 480,
			thumb_image_height: 480,
			source_image_width: 900,
			source_image_height: 1200,
			zoom_area_width: 340,
			zoom_area_height: 495,
			zoom_area_distance: 20,
			small_thumbs: 6,
			smallthumb_inactive_opacity: 1,
			smallthumbs_position:'left',
			autoplay_interval : 3000,
			right_to_left: true
		});
	}

});
/*============ end it ============*/


/*==========zommed image with etalage plugin in product page=====*/
$(function(){

	var viewWidth = $(window).width(); 

	if(viewWidth  <= 959 & viewWidth > 768 ) {
		$('#etalage_style_two').etalage({
			thumb_image_width: 300,
			thumb_image_height: 330,
			source_image_width: 900,
			source_image_height: 1200,
			zoom_area_width: 220,
			zoom_area_height: 365,
			zoom_area_distance: 20,
			small_thumbs: 4,
			smallthumb_inactive_opacity: 1,
			smallthumbs_position:'bottom',
			autoplay_interval : 3000,
			right_to_left: true
		});

	} else if(viewWidth <= 767 & viewWidth > 479) {
		$('#etalage_style_two').etalage({
			thumb_image_width: 400,
			thumb_image_height: 360,
			source_image_width: 900,
			source_image_height: 1200,
			zoom_area_width: 340,
			zoom_area_height: 495,
			zoom_area_distance: 20,
			small_thumbs: 4,
			smallthumb_inactive_opacity: 1,
			smallthumbs_position:'bottom',
			autoplay_interval : 3000,
			zoom_element: '#zoom',
			right_to_left: true
		});

	} else if(viewWidth <= 479) {
		$('#etalage_style_two').etalage({
			thumb_image_width: 280,
			thumb_image_height: 240,
			source_image_width: 900,
			source_image_height: 1200,
			zoom_area_width: 340,
			zoom_area_height: 495,
			zoom_area_distance: 20,
			small_thumbs: 4,
			smallthumb_inactive_opacity: 1,
			smallthumbs_position:'bottom',
			autoplay_interval : 3000,
			zoom_element: '#zoom',
			right_to_left: true
		});
	} else {
		$('#etalage_style_two').etalage({
			thumb_image_width: 385,
			thumb_image_height: 370,
			source_image_width: 900,
			source_image_height: 1200,
			zoom_area_width: 280,
			zoom_area_height: 485,
			zoom_area_distance: 20,
			small_thumbs: 4,
			smallthumb_inactive_opacity: 1,
			smallthumbs_position:'bottom',
			autoplay_interval : 3000,
			right_to_left: true
		});
	}

});
/*============ end it ============*/

// Invoke the Fancybox plugin when an image is clicked on the etalage plugin
function etalage_click_callback(image_anchor, instance_id){
	$.fancybox({
		href: image_anchor
	});
}
