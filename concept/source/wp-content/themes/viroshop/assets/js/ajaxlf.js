jQuery(document).ready(function($) {
	$("#loadplace").hide();

	$(".try-again a").click(function() {
		$("#sidebar .widget_loginformwidget").slideUp('slow', function() {
			$("#loginForm").show();
			$("#loadplace").hide();
    	});
		$("#sidebar .widget_loginformwidget").delay(1000).slideDown('slow',  function() {
			$("#loadplace").hide();
    	});		
		return false;
	});

	$('.submit').click(function(e){	
		e.preventDefault();  
		var element = $(this);
		var usernameVal = $(".username").val();	
		var str = $("#loginForm").serialize();
		var data = {
			action: 'myajax-submit',
			serialize: str,
			beforeSend: function(){ 
			}
		};

		$.post(MyAjax.ajaxurl, data,  function(response) {
			
			if(response == 'error'){
				$("#sidebar .widget_loginformwidget").slideUp('slow', function() {
					$("#loginForm").hide();
					$("#loadplace").show();
    			});
				$("#sidebar .widget_loginformwidget").delay(1000).slideDown('slow', function() {
    		
    			});
				return false;
			};
			 
			if(response == 'correct'){
 				location.reload();
				return false;
			};                                                          
		});
		return false;	
	});
});