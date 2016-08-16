jQuery('html').removeClass('no-js').addClass('js');



jQuery(document).ready(function($) {


//	Responsive Menu
	$(function(){
		$("<select />").appendTo("#menu");
		
		$("<option />", {
		   "selected": "selected",
		   "value"   : "",
		   "text"    : "Navigate..."
		}).appendTo("nav select");
		
		// Populate dropdown with menu items
		$(".sf-menu a").each(function() {
			var el = $(this);
			$("<option />", {
				"value"   : el.attr("href"),
				"text"    : el.text()
			}).appendTo("nav select");
		});
		
		$("nav select").change(function() {
		  window.location = $(this).find("option:selected").val();
		});
	});

//	Homepage Carousel
	$('#carousel').elastislide({
				imageW 	: 145,
				speed       : 350,
				minItems	: 4,
				margin      : 20    
			});

//	Firefly
	$(function(){
		if(vs_ajax.firefly == 1) { 
			$.firefly({
				images : [vs_ajax.spark, vs_ajax.secondspark],
				total : 75,
				on:'document.body'
			});
		}	
	});
	
//	Price filter
 	$(function(){	
 		var $priceLabel = jQuery('.price_label');
 		
 		$priceLabel.hide();
 		$priceLabel.html($priceLabel.html().replace('Price: ',''));
 		$priceLabel.html($priceLabel.html().replace('—','-'));
 		
		$(".widget.widget_price_filter").hover(function() {
			$priceLabel.stop().fadeIn();
			}, function() {
    		$priceLabel.stop().fadeOut();
		});
	});

//	Shopping bag
	jQuery(".shopping-bag").click(function(){
		window.location=jQuery(this).find("a").attr("href");
		return false;
	});
	
//	Custom select
	$(".orderby, .summary select, .shipping-calculator-form select").selectBox({
		'menuTransition': 'slide',
		'menuSpeed' : 'fast'
	});
	
//	Superfish 				
	$('ul.sf-menu').superfish({
		disableHI:true,          
		dropShadows: false,
		speed: 'fast',
		delay: 0,   
		autoArrows: false
	});	
	
//  Sidebar slide down menu 
	$('.widget_nav_menu .sub-menu', this).hide();
	$('.widget_nav_menu li.current-menu-parent > .sub-menu', this).show();
	$('.widget_nav_menu li.current-menu-parent > .sub-menu', this).prev().addClass('active');
	$('.widget_nav_menu li a', this).click(function(e) {
				e.stopImmediatePropagation();
				var theElement = $(this).next();
				var parent = this.parentNode.parentNode;
	
				if($(parent).hasClass('sub-menu')) {
					if(theElement[0] === undefined) {
						window.location.href = this.href;
					}
					$(theElement).slideToggle('normal', function() {
						if ($(this).is(':visible')) {
							$(this).prev().addClass('active');
						}
						else {
							$(this).prev().removeClass('active');
						}    
					});
					return false;
				}
				else {
					if(theElement.hasClass('sub-menu') && theElement.is(':visible')) {
						if($(parent).hasClass('collapsible')) {
							$('.sub-menu:visible', parent).first().slideUp('normal', 
							function() {
								$(this).prev().removeClass('active');
							}
						);
						return false;  
					}
					return false;
				}
				if(theElement.hasClass('sub-menu') && !theElement.is(':visible')) {         
					$('.sub-menu:visible', parent).first().slideUp('normal', function() {
						$(this).prev().removeClass('active');
					});
					theElement.slideDown('normal', function() {
						$(this).prev().addClass('active');
					});
					return false;
				}
			}
		});	


	 
});

 