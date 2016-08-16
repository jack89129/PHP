jQuery(document).ready(function($) {
	$(".email-success").hide();

	$('#contact-submit').click(function(e){	
		e.preventDefault();  
		var element = $(this);
		var str = $("#contact-form").serialize();
		var data = {
			action: 'contact-submit',
			serialize: str,
			beforeSend: function(){ 
			}
		};

		$.post(MyAjax.ajaxurl, data,  function(response) { 
			if(response == 'nameemailfail'){
				$('#contact-form').fadeTo('slow', 0.3);
				$('#contact-form').fadeTo('slow', 1.0);
				$("#name, #email").animate({backgroundColor: "#eaf1f4", borderBottomColor: "#b6c8d0", borderLeftColor: "#b6c8d0",  borderRightColor: "#b6c8d0",  borderTopColor: "#b6c8d0"}, 800);
				return false;
			} 
			else {
				$("#name, #email").animate({backgroundColor: "#fff", borderBottomColor: "#e1e1e1", borderLeftColor: "#e1e1e1",  borderRightColor: "#e1e1e1",  borderTopColor: "#e1e1e1"}, 800);
			}
			if(response == 'namefail'){
				$('#contact-form').fadeTo('slow', 0.3);
				$('#contact-form').fadeTo('slow', 1.0);
				$("#name").animate({backgroundColor: "#eaf1f4", borderBottomColor: "#b6c8d0", borderLeftColor: "#b6c8d0",  borderRightColor: "#b6c8d0",  borderTopColor: "#b6c8d0"}, 800);
				return false;	
			}
			if(response == 'emailfail'){
				$('#contact-form').fadeTo('slow', 0.3);
				$('#contact-form').fadeTo('slow', 1.0);
				$("#email").animate({backgroundColor: "#eaf1f4", borderBottomColor: "#b6c8d0", borderLeftColor: "#b6c8d0",  borderRightColor: "#b6c8d0",  borderTopColor: "#b6c8d0"}, 800);
				return false;	
			}
			else if(response == 'emailincorrect'){
				$('#contact-form').fadeTo('slow', 0.3);
				$('#contact-form').fadeTo('slow', 1.0);
				$("#email").animate({backgroundColor: "#eaf1f4", borderBottomColor: "#b6c8d0", borderLeftColor: "#b6c8d0",  borderRightColor: "#b6c8d0",  borderTopColor: "#b6c8d0"}, 800);
				return false;	
			}
			else {
			
				$("#contact-form").fadeOut('slow', function() {
					$(".email-success").fadeIn();
    			});

			}	                                                  
		});
		return false;	
	});
});