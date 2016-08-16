$(document).ready(function()
{
	$('.uploadfile').fancybox(
	{
		'titlePosition'		: 'inside',
		'transitionIn'		: 'none',
		'transitionOut'		: 'none'
	});
	
	
	$('.properties').fancybox(
	{
		'titlePosition'		: 'inside',
		'transitionIn'		: 'none',
		'transitionOut'		: 'none'
	});
});

function deleteFile(element)
{
	element.parent().parent().parent().parent().remove();
	//ajax call
}

function refreshFolder()
{
	var data = {};
	$('.calendar_Box').load('dirlist.html', data, function(retval)
	{
		$('.properties').fancybox(
		{
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none'
		});
	})
}
$(document).ready(function()
{
	$('.main h2 a').click(function()
	{
		var alreadyactive = false;
		if ($(this).parent().hasClass('active')) alreadyactive = true;
		$('.main h2.active').parent().find('div.project').slideUp();
		$('div.project').removeClass('active');
		$('.main h2').removeClass('active');
		if (alreadyactive) return false;
		$(this).parent().parent().find('div.project').slideDown();
		$(this).parent().parent().find('div.project').addClass('active');
		$(this).parent().addClass('active');
	});
});

var timerrunning = false;
var interval = false;
var secs = 0;

function toggleTimer(button)
{
	if (!timerrunning)
	{
		timerrunning = true;
		interval = setInterval('updateTimer()', 1000);
		secs = 0;
		$('#clock').val('0 sec');
		button.val('Stop');
	} else
	{
		timerrunning = false;
		if (interval!==false) clearInterval(interval);
		interval = false;
		button.val('Start');
	}
}

function updateTimer()
{
	secs++;
	$('#clock').val(secs > 59 ? ((Math.floor(secs/60))+' min') : (secs+' sec'));
}

function changeDay(type)
{
	/*element.parent().find('.nextday').show();
	var old = element.parent().parent().parent().parent().find('.info > div');
	var parent = old.parent();
	old.animate({ left: -old.width()+'px' }, 1000, function()
	{
		parent.load('timetracking_otherday.html', { }, function(retval)
		{
			parent.find('div').eq(0).fadeIn();
			$('.info .box li a').fancybox({'titlePosition': 'inside', 'transitionIn': 'none', 'transitionOut': 'none'});
		});
	});*/
    if ( $('#different_balance').html() != '€ 0.00' && $('#different_balance').html() != '€ -0.00' ) {
        alert( "U dient in te voeren!")    
    } else {
        var dateString = $('#kas_date').val();
        var actualDate = new Date(dateString); // convert to actual date
        var delta = type=='past' ? -1 : 1;
        var newDate = new Date(actualDate.getFullYear(), actualDate.getMonth(), actualDate.getDate()+delta);
        $('#kas_date').val($.datepicker.formatDate('yy-mm-dd', newDate));
        $('#overige_form').submit();
    }
}

function changeEmployee(element)
{
	if (element.parent().hasClass('selected')) return false;
	element.parent().parent().find('li').removeClass('selected');
	element.parent().addClass('selected');
	
	var old = $('.main h2.active').parent().find('.info > div');
	var parent = old.parent();
	old.animate({ left: -old.width()+'px' }, 1000, function()
	{
		parent.load('timetracking_otherday.html', { }, function(retval)
		{
			parent.find('div').eq(0).fadeIn();
			$('.info .box li a').fancybox({'titlePosition': 'inside', 'transitionIn': 'none', 'transitionOut': 'none'});
		});
	});
}

function initCollapsable(parent){
    parent = parent == undefined ? '' : parent;

    $(parent + " .news_Box .collapse h2").click(function() {
        if($(this).parent().find("div.info").is(":visible")) {
            ($(this).parent().find("div.info").slideUp());
            var myDiv = $("div");
            myDiv.clearQueue();
            ($(this).find("a").removeClass("i-up"));
            ($(this).find("a").addClass("i-down"));

        }
    });

    $(parent + " .news_Box .collapse h2").click(function() {
        if($(this).parent().find("div.info").is(":hidden")) {
            ($(this).parent().find("div.info").slideDown());
            var myDiv = $("div");
            myDiv.clearQueue();
            ($(this).find("a").removeClass("i-down"));
            ($(this).find("a").addClass("i-up"));
        }
    });

}


$(document).ready(function()
{
    initCollapsable();
	$('.uploadfile').fancybox(
	{
		'titlePosition'		: 'inside',
		'transitionIn'		: 'none',
		'transitionOut'		: 'none'
	});
	
	$('.properties').fancybox(
	{
		'titlePosition'		: 'inside',
		'transitionIn'		: 'none',
		'transitionOut'		: 'none'
	});
});

function deleteFile(element)
{
	element.parent().parent().parent().parent().remove();
	//ajax call
}

function refreshFolder()
{
	var data = {};
	$('.calendar_Box').load('dirlist.html', data, function(retval)
	{
		$('.properties').fancybox(
		{
			'titlePosition'		: 'inside',
			'transitionIn'		: 'none',
			'transitionOut'		: 'none'
		});
	})
}

function changeCal(element, type, x)
{
	//x to be implemented, day/week/month number
	var data = { 'x': x };
	element.parent().parent().find('li').removeClass('selected');
	element.parent().addClass('selected');
	
	if (type=='d')
	{
		$('.calendar_Box').load('table-day.html', data, function(retval)
		{
			$('.time .gray').click(function()
			{
				var vindex = $('.time ul li').index($(this).parent());
				
				var startleft = ($('.time li > div.one').width()) + parseInt($('.time li > div.one').css('padding-right').replace('px', '')) +
								parseInt($('.calendar_Box').css('padding-left').replace('px', '')) +
								($('.time li > div').eq(1).width()/2) - ($('.add-date').width()/2);
				
				var starttop = $('.calendar_Box > p').height() + parseInt($('.calendar_Box > p').css('padding-top').replace('px', '')) + parseInt($('.calendar_Box > p').css('padding-bottom').replace('px', '')) - 144 + $('.calendar_Box > h2').height();
				
				var newtop = starttop + (vindex+0.5)*($('.calendar_Box .time.sub li div').height() + parseInt($('.calendar_Box .time.sub li div').css('padding-top').replace('px', '')) + parseInt($('.calendar_Box .time.sub li div').css('padding-bottom').replace('px', ''))) + vindex ; //+borders
				
				if ($(this).offset().top - $(".calendar_Box").offset().top < 150)
				{
					$(".add-date").addClass("topArrow");
					newtop += 170;
				}
				
				$('.add-date').css('left', startleft+'px');
				$('.add-date').css('top', newtop+'px');
				$('.add-date').parent().show();
			});
	
			$('.fancybox').fancybox();
		});
		
	} else if (type=='w')
	{
		$('.calendar_Box').load('table-week.html', data, function(retval)
		{
			$('.time .up, .time .down').click(function()
			{					
				var vindex = $('.time ul li').index($(this).parent().parent());
				var hindex = $(this).parent().parent().children().index($(this).parent());					
				
				var starttop = $('ul.name1').height() + parseInt($('ul.name1').css('padding-top').replace('px', '')) + parseInt($('ul.name1').css('padding-bottom').replace('px', '')) - 144;
				
				var startleft = ($('.time li > div.one').width()) + parseInt($('.time li > div.one').css('padding-right').replace('px', '')) + 
								($('.time li > div.two').width()/2) - ($('.add-date').width()/2) +
								parseInt($('.calendar_Box').css('padding-left').replace('px', ''));
				
				var newleft = startleft+((hindex-1)*$('.time li > div.two').width());
				var newtop = starttop+(vindex+1)*((($('.time li > div.two > div.up').height())*2)+2);
				if ($(this).offset().left - $(".calendar_Box").offset().left < 100 && $(this).offset().top - $(".calendar_Box").offset().top < 150)
				{
					$(".add-date").addClass("leftTopArrow");
					newleft += 270;
					newtop += 80;
				}
				else if ($(this).offset().top - $(".calendar_Box").offset().top < 150)
				{
					$(".add-date").addClass("topArrow");
					newtop += 170;
				}
				else if ($(this).offset().left - $(".calendar_Box").offset().left < 100)
				{
					$(".add-date").addClass("leftArrow");
					newleft += 270;
					newtop += 80;
				}
		
				$('.add-date').css('left', newleft+'px');
				$('.add-date').css('top', newtop+'px');
				$('.add-date').parent().show();
			});
			
			$('.fancybox').fancybox();
		});
		
	} else if (type=='m')
	{
		$('.calendar_Box').load('table-month.html', data, function(retval)
		{
			attachMonthPopup();
			
			$('#agendapeople input').click(function()
			{
				updateAgenda();
			})
			
			$('.fancybox').fancybox();
		});
	}
}

function attachMonthPopup()
{
    $('.calendar li a').fancybox();
	$('.calendar li a').click(function()
	{
		/*$(".add-date").removeClass("leftArrow");
		$(".add-date").removeClass("topArrow");
		$(".add-date").removeClass("leftTopArrow");
		
		var index = $('div.calendar ul li').index($(this).parent());
		index++;
		
		var newleft = 270-(corleft*100);
		var newtop = -400 + (cortop*102);
		
		var startleft = (($('.calendar_Box .calendar li a').width() + parseInt($('.calendar_Box .calendar li a').css('padding-left').replace('px', '')))/2) - ($('.add-date').width()/2);
		var starttop = -155;
		
		var corleft = (index%7)-1;
		if (corleft==-1) corleft = 6;
		var cortop = (Math.floor(index/7));
		if (corleft==6) cortop--;
		
		var newleft = startleft + corleft + corleft*($('.calendar_Box .calendar li a').width() + parseInt($('.calendar_Box .calendar li a').css('padding-left').replace('px', '')));
		var newtop = starttop + cortop + cortop*($('.calendar_Box .calendar li a').height()+parseInt($('.calendar_Box .calendar li a').css('padding-top').replace('px', '')));

		if ($(this).offset().left - $(".calendar_Box").offset().left < 120 && $(this).offset().top - $(".calendar_Box").offset().top < 150)
		{
			$(".add-date").addClass("leftTopArrow");
			newleft += 270;
			newtop += 110;
		}
		else if ($(this).offset().top - $(".calendar_Box").offset().top < 120)
		{
			$(".add-date").addClass("topArrow");
			newtop += 220;
		}
		else if ($(this).offset().left - $(".calendar_Box").offset().left < 120)
		{
			$(".add-date").addClass("leftArrow");
			newleft += 270;
			newtop += 110;
		}
		
		$('.add-date').css('left', newleft+'px');
		$('.add-date').css('top', newtop+'px');
		//alert($(this).offset().top - $(".calendar_Box").offset().top);
		$('.add-date').parent().show();
		$('.date-item').html('<cite>Wanneer:</cite>'+$(this).html()+' '+($('#month').html())); */  
        $('#lightbox10 .scroll-pane').jScrollPane();
	});
}

$(document).ready(function()
{
	attachMonthPopup();
	
	$('#agendapeople input').click(function()
	{
		updateAgenda();
	})
});

function updateAgenda()
{
	$('#agendapeople input').each(function()
	{
		$(this).is(':checked') ? $('.'+$(this).attr('id')).show() : $('.'+$(this).attr('id')).hide();
	})
}
$(function () {
	$('.checkall').click(function () {
		$(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
	});
});

var disable_loader = false;
$(document).ajaxSend(function(){
    if( !disable_loader ){
        show_loader();
    }
    disable_loader = false;
});

$(document).ajaxComplete(function(){
    $.hideCursorMessage();
})

$(document).ajaxSuccess(function(event, data){
	try {
		data = jQuery.parseJSON(data.responseText);
	}catch(e){
		data = null
	}
	
	if( !data ){
		return;
	}
	
	if( data.redirect ){
		window.location = data.redirect;
	}
	
	if( data.reload ){
		window.location.reload();
	}
	
	if( data.success ){
		//$.cursorMessage(data.success.message && data.success.message.length ? data.success.message : 'Success!', {className:'success', hideTimeout:data.success.timeout ? data.success.timeout : 3000});
	}
});

$(document).ajaxError(function(event, error){
	try {
		response = jQuery.parseJSON(error.responseText);
	}catch(e){
        response = null;
	}

	if( response && response.error ){
		alert(response.error);
		if( response.exception ){
			console.error(response.exception);
		}
	}else if( error.readyState ){
		alert('Uups! Error occured!');
	}
});


function show_loader(timeout){
    if( timeout == undefined ){
        timeout = 0;
    }

    $.cursorMessage('<img src="' + baseUrl + '/images/loader.gif" alt="loading" />', {className:'zero', hideTimeout:timeout});
}

var log_source_type;
var log_source_id;

function log_init(){
    if( log_source_type == undefined || log_source_id == undefined || log_source_id == 0 ){
        return;
    }

    $.post(baseUrl + '/log/log/log', {source_type:log_source_type, source_id: log_source_id}).success(function(data){
       $('#log').html(data.log);
       initCollapsable('#log');
    });
}

function log_reload(){
    log_init();
}

function number_format (number, decimals, dec_point, thousands_sep) {
  // http://kevin.vanzonneveld.net
  // +   original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // +     bugfix by: Michael White (http://getsprink.com)
  // +     bugfix by: Benjamin Lupton
  // +     bugfix by: Allan Jensen (http://www.winternet.no)
  // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
  // +     bugfix by: Howard Yeend
  // +    revised by: Luke Smith (http://lucassmith.name)
  // +     bugfix by: Diogo Resende
  // +     bugfix by: Rival
  // +      input by: Kheang Hok Chin (http://www.distantia.ca/)
  // +   improved by: davook
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Jay Klehr
  // +   improved by: Brett Zamir (http://brett-zamir.me)
  // +      input by: Amir Habibi (http://www.residence-mixte.com/)
  // +     bugfix by: Brett Zamir (http://brett-zamir.me)
  // +   improved by: Theriault
  // +      input by: Amirouche
  // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
  // *     example 1: number_format(1234.56);
  // *     returns 1: '1,235'
  // *     example 2: number_format(1234.56, 2, ',', ' ');
  // *     returns 2: '1 234,56'
  // *     example 3: number_format(1234.5678, 2, '.', '');
  // *     returns 3: '1234.57'
  // *     example 4: number_format(67, 2, ',', '.');
  // *     returns 4: '67,00'
  // *     example 5: number_format(1000);
  // *     returns 5: '1,000'
  // *     example 6: number_format(67.311, 2);
  // *     returns 6: '67.31'
  // *     example 7: number_format(1000.55, 1);
  // *     returns 7: '1,000.6'
  // *     example 8: number_format(67000, 5, ',', '.');
  // *     returns 8: '67.000,00000'
  // *     example 9: number_format(0.9, 0);
  // *     returns 9: '1'
  // *    example 10: number_format('1.20', 2);
  // *    returns 10: '1.20'
  // *    example 11: number_format('1.20', 4);
  // *    returns 11: '1.2000'
  // *    example 12: number_format('1.2000', 3);
  // *    returns 12: '1.200'
  // *    example 13: number_format('1 000,50', 2, '.', ' ');
  // *    returns 13: '100 050.00'
  // Strip all characters but numerical ones.
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}