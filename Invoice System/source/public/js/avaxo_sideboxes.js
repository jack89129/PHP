  $(function()
    {
        $('.scroll-pane').jScrollPane();
        $('.fancybox').fancybox();
		
		$('.overlaybox').each(function()
		{
			var overlaybox = $(this);
			overlaybox.find('.radio, .styled').on("click", function()
			{
				overlaybox.find('.selected').removeClass('selected');
				overlaybox.find('.greybg').removeClass('greybg');
				$(this).parent().parent().parent().addClass('greybg');
			});
		});
    });

	$(document).ready(function(){

		$('select.dropdown').easySelectBox();

		$('.link_Box li.sub').hover(function(){ 
			$(this).addClass('hover'); 
		}, 
		function(){ 
			$(this).removeClass('hover'); 
		});
		
		
	});

    $(function()
    {
		var paneelement = $('#pane1').jScrollPane();
		var paneapi = paneelement.data('jsp');
		
		$('.right ul li a').bind(
			'click',
			function()
			{
				//alert($pane);
				if ($('#letter_'+$(this).html()).size()==1)
				{
					$('.right li').removeClass('selected');
					$(this).parent().addClass('selected');
					
					paneapi.scrollToElement('#letter_'+$(this).html(), true, true);
					$('.left li').removeClass('selected');
					$('#letter_'+$(this).html()).parent().parent().addClass('selected');
				}

				return false;
			}
		);
    });

    $(document).ready(function() {

		$("a#example1").fancybox();

        $("a#example2").fancybox({
            'overlayShow'	: false,
            'transitionIn'	: 'elastic',
            'transitionOut'	: 'elastic'
        });

        $("a#example3").fancybox({
            'transitionIn'	: 'none',
            'transitionOut'	: 'none'	
        });

        $("a#example4").fancybox({
            'opacity'		: true,
            'overlayShow'	: false,
            'transitionIn'	: 'elastic',
            'transitionOut'	: 'none'
        });

        $("a#example5").fancybox();

        $("a#example6").fancybox({
            'titlePosition'		: 'outside',
            'overlayColor'		: '#000',
            'overlayOpacity'	: 0.9
        });

        $("a#example7").fancybox({
            'titlePosition'	: 'inside'
        });

        $("a#example8").fancybox({
            'titlePosition'	: 'over'
        });

        $("a[rel=example_group]").fancybox({
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'titlePosition' 	: 'over',
            'titleFormat'		: function(title, currentArray, currentIndex, currentOpts) {
                return '<span id="fancybox-title-over">Image ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '</span>';
            }
        });
    
        $("#various1").fancybox({
            'titlePosition'		: 'inside',
            'transitionIn'		: 'none',
            'transitionOut'		: 'none'
        });

        $("#various2").fancybox({
            'titlePosition'		: 'inside',
            'transitionIn'		: 'none',
            'transitionOut'		: 'none'
        });

        $("#various2").fancybox();

        $("#various3").fancybox({
            'width'				: '75%',
            'height'			: '75%',
            'autoScale'			: false,
            'transitionIn'		: 'none',
            'transitionOut'		: 'none',
            'type'				: 'iframe'
        });

        $("#various4").fancybox({
            'padding'			: 0,
            'autoScale'			: false,
            'transitionIn'		: 'none',
            'transitionOut'		: 'none'
        });
    });       

    $(window).load(function(){
        $(".leftWinBtn").click(function() {
            $("#leftwinbox").toggle();
            $('.scroll-pane').jScrollPane();

        });
        $(".rightwinbtn").click(function() {
            $("#rightwinbox").toggle();
            $('.scroll-pane').jScrollPane();
        });
        $(".bottomwinbtn").click(function() {
            $(".vries-Box").toggle();
            $('.scroll-pane').jScrollPane();
        });
		
		$('#navigation ul li a').click(function(){ $('.fancybox').fancybox(); });
			
			
    });
	$(function () {
		$('.checkall').click(function () {
			$(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
		});
	});

