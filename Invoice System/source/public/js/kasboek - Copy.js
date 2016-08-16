function invoice_payment_load(id, elem, invoice_number){
    if (elem) {
        $('#invoice-payment-invoice-list .label_check').removeClass('c_on');
        $(elem).parent().addClass('c_on');
    }
    $.post(baseUrl + '/invoices/index/payment-dialog-invoice-load', {id:id}).success(function(data){
        $('#invoice-payment-invoice').html(data.invoice);
        $('#invoice-payment-form').unbind('submit');
        $('#invoice-payment-form').submit(function(){
            var is_email_notify = 0;
            var to_email;
            $('#invoice-payment-form input').each(function() {
                if ($(this).attr('name') == 'email[send]' && $(this).attr('checked')) {
                    is_email_notify = 1;
                }
                if ($(this).attr('name') == 'email[email]') {
                    to_email = $(this).val();
                }
            });
            var payment_method = "";
            $('#invoice-payment-form select').each(function() {
                if ( $(this).attr('name') == 'payment[payment_method]' ) {
                    payment_method = $(this).val();
                }
            });
            var kas_date = $('#kas_date').val();
            var page_type = $('#page_type').val();
            var message = $('#invoice-payment-form textarea').val();
            $.post(baseUrl + '/overige/index/set-unpaid-invoice', {id:id, invoice_number: invoice_number, is_email: is_email_notify, mail_addr: to_email, message: message, kas_date: kas_date, payment_method: payment_method}).success(function(data){
                if ( payment_method == page_type ) {
                    var trclass =  $(".factuur-tabel > tbody").find("tr:last").attr('class');
                    if (trclass == 'factuur-odd factuur-even') {
                        var newclass = 'factuur-even';
                    } else {
                        var newclass = 'factuur-odd factuur-even';
                    }
                    var tablerow;
                    var total = parseFloat(char_replace($('#list_total').val(),',','.'));
                    tablerow = $('<tr class="'+newclass+'"><td>'+data[0].number+'</td><td>'+data[0].owner+'</td><td class="credit">&euro; '+data[0].total_sum+'</td><td class="debet"></td></tr>');
                    total += parseFloat(data[0].total_sum);
                    $('#list_total').val(total);
                    
                    $(tablerow).appendTo(".factuur-tabel").hide().fadeIn(2000);
                    var difference = parseFloat(char_replace($('#end_balance').val().substring(1), ',', '.')) - parseFloat(char_replace($('#start_balance').val().substring(1), ',', '')) - total;
                    var content = "€ " + difference.toFixed(2);
                    if ( content == '€ -0.00' ) content = '€ 0.00';
                    $('#different_balance').html(content);   
                    $('#search_invoice_number').val("");
                    $('#search_amount').val("");
                }
                $.fancybox.close();
            });
            
            return false;
        });
    });
}

function char_replace(str, from, to) {
    return str.replace(from, to);
}


function purchase_payment_load(id, elem, purchase_number){
    if (elem) {
        $('#purchase-payment-purchase-list .label_check').removeClass('c_on');
        $(elem).parent().addClass('c_on');
    }
    $.post(baseUrl + '/purchases/index/payment-dialog-purchase-load', {id:id}).success(function(data){
        $('#purchase-payment-purchase').html(data.purchase);
        $('#purchase-payment-form').unbind('submit');
        $('#purchase-payment-form').submit(function(){
            var kas_date = $('#kas_date').val();
            $.post(baseUrl + '/overige/index/set-unpaid-purchase', {id:id, purchase_number: purchase_number, kas_date: kas_date}).success(function(data){
                var trclass =  $(".factuur-tabel > tbody").find("tr:last").attr('class');
                if (trclass == 'factuur-odd factuur-even') {
                    var newclass = 'factuur-even';
                } else {
                    var newclass = 'factuur-odd factuur-even';
                }
                var tablerow;
                var total = parseFloat(char_replace($('#list_total').val(),',','.'));
                tablerow = $('<tr class="'+newclass+'"><td>'+data[0].number+'</td><td>'+data[0].owner+'</td><td class="credit"></td><td class="debet">&euro; '+data[0].total_sum+'</td></tr>');
                total -= parseFloat(data[0].total_sum);
                $('#list_total').val(total);
                
                $(tablerow).appendTo(".factuur-tabel").hide().fadeIn(2000);
                var difference = parseFloat(char_replace($('#end_balance').val().substring(1), ',', '.')) - parseFloat(char_replace($('#start_balance').val().substring(1), ',', '')) - total;
                var content = "€ " + difference.toFixed(2);
                if ( content == '€ -0.00' ) content = '€ 0.00';
                $('#different_balance').html(content);
                $('#search_invoice_number').val("");
                $('#search_amount').val("");
                $.fancybox.close();
            });
            return false;
        });
    });
}

function onBlurEndBalanceInput(obj, type) {
    var v;
    if(obj.value=='') {
        obj.value = $('#start_balance').val();
        v = parseFloat(char_replace(obj.value.substring(1), ',', ''));
    } else { 
        obj.value = char_replace(obj.value, ',', '.');
        if ( obj.value.substring(0,1) == '€' ) {
            v = parseFloat(obj.value.substring(1));
        } else {
            v = parseFloat(obj.value);
        }
        obj.value = '€ ' + v.toFixed(2); 
        obj.value = char_replace(obj.value, '.', ',');
    }
    
    var url = '/overige/';
    if ( type ) {
        url += type;
    } else {
        url += 'index';
    }
    url += '/save-balance';
    
    $.post(baseUrl + url, {kas_date:$('#kas_date').val(), amount:v}).success(function(){
        var total = parseFloat(char_replace($('#list_total').val(),',','.'));
        var difference = parseFloat(char_replace($('#end_balance').val().substring(1), ',', '.')) - parseFloat(char_replace($('#start_balance').val().substring(1), ',', '')) - total;
        var content = "€ " + difference.toFixed(2);
        if ( content == '€ -0.00' ) content = '€ 0.00';
        $('#different_balance').html(content);  
    });
}

function onSaveAfsch(obj, type) {
    if (obj.value == ''){
        return;
    } 
    var url = '/overige/';
    if ( type ) {
        url += type;
    } else {
        url += 'index';
    }
    url += '/save-afsch';
    
    $.post(baseUrl + url, {kas_date:$('#kas_date').val(), afsch:obj.value}).success(function(){
    });
}

$(document).ready(function()
{
	$('.dropdown-kasboek-box h2 a').live('click', function()
	{
		var alreadyactive = false;
        var alreadyopen = false;
		if ($(this).parent().hasClass('active')) alreadyactive = true;
        if ($(this).parent().hasClass('opened')) alreadyopen = true;
        if ( alreadyopen && alreadyactive) {
            $('.dropdown-kasboek-box h2.active').parent().find('div.dropdown-kasboek-content').slideUp();
            $('div.dropdown-kasboek-content').removeClass('active');
            $('.dropdown-kasboek-box h2').removeClass('active');
            $('div.dropdown-kasboek-content').removeClass('opened');
            $('.dropdown-kasboek-box h2').removeClass('opened');
            return;
        } 
		$('div.dropdown-kasboek-content').removeClass('active');
		$('.dropdown-kasboek-box h2').removeClass('active');
		if (alreadyactive) return false;
		$(this).parent().parent().find('div.dropdown-kasboek-content').slideDown();
		$(this).parent().parent().find('div.dropdown-kasboek-content').addClass('active');
		$(this).parent().addClass('active');      
        $(this).parent().parent().find('div.dropdown-kasboek-content').addClass('opened');
        $(this).parent().addClass('opened');      
        if ( $('#end_balance').val() == "" ) $('#end_balance').val("€ 0,00");
        if ( $('#list_total').val() == "" ) $('#list_total').val("0.00");  
        if ( $('#start_balance').val() == "" ) $('#start_balance').val("€ 0.00");  
        //$('#end_balance').val(char_replace($('#end_balance').val(),',',''));
        var difference = parseFloat(char_replace($('#end_balance').val().substring(1),',','.')) - parseFloat(char_replace($('#start_balance').val().substring(1),',','')) - parseFloat(char_replace($('#list_total').val(),',','.'));
        var content = "€ " + difference.toFixed(2);
        if ( content == '€ -0.00' ) content = '€ 0.00';
        $('#different_balance').html(content);
        $('#start_saldo').html("Startsaldo voor " + $('#kasboek_date').val() + ":  " + $('#end_balance').val());
	});
    
    $("#bank_print").live('click', function(){
        var kas_date = $('#kas_date').val();
        var url = baseUrl + '/overige/excel/report/kas_date/' + kas_date;
        window.location.href = url;
    });
    
    $("#bank_month_print").live('click', function(){
        var kas_date = $('#kas_date').val();
        var url = baseUrl + '/overige/excel/month/kas_date/' + kas_date;
        window.location.href = url;
    });
    
    $("#kas_print").live('click', function(){
        var kas_date = $('#kas_date').val();
        var url = baseUrl + '/overige/print/report/kas_date/' + kas_date;
        window.location.href = url;
    });
    
    $("#kas_month_print").live('click', function(){
        var kas_date = $('#kas_date').val();
        var url = baseUrl + '/overige/print/month/kas_date/' + kas_date;
        window.location.href = url;
    });
	
	$("#new_afschrift").live('click', function(){
		var trclass =  $(".factuur-tabel > tbody").find("tr:last").attr('class');
		if (trclass == 'factuur-odd factuur-even') {
			var newclass = 'factuur-even';
		} else {
			var newclass = 'factuur-odd factuur-even';
		}
        var amount = $('#search_amount').val();
        var number = $('#invoice_number').val();
        if ( amount == "" && number == "" ) {
            alert("Gelieve ingang zoekvoorwaarde!")
            return;
        }
        amount = char_replace(amount, ',', '.');
        $('#search_amount').val(amount);
        
        if ( amount > 0 ) {
            $("#invoice-payment-dialog").html(
                "<form id='invoice-payment-form' action='#'>\
                    <fieldset>\
                    <h2>'Betalingen'</h2>\
                    <div class='left-container'>\
                        <div id='invoice-payment-invoice-list' style='outline:none; height:400px; overflow-y:auto;'>\
                        </div>\
                    </div>\
                    <div id='invoice-payment-invoice' class='right-container'>\
                    </div>\
                    <div class='clear'></div>\
                </fieldset>\
            </form>");
        } else {
            $("#invoice-payment-dialog").html(
                "<form id='purchase-payment-form' action='#'>\
                    <fieldset>\
                    <h2>'Betalingen'</h2>\
                    <div class='left-container'>\
                        <div id='purchase-payment-purchase-list' style='outline:none; height:400px; overflow-y:auto;'>\
                        </div>\
                    </div>\
                    <div id='purchase-payment-purchase' class='right-container'>\
                    </div>\
                    <div class='clear'></div>\
                </fieldset>\
            </form>");
        }
        
        $.ajax({
            url: baseUrl + "/overige/index/search-unpaid",
            method: 'post',
            data: {
                amount: amount,
                number: number
            },
            success: function( data ) {
                  if ( data.length == 0 ) {
                      alert("Er is geen resultaat!");
                  } else {
                      var listHTML = "";
                      for ( var i=0; i<data.length; i++ ) {
                          if ( i == 0) {
                            listHTML += "<li><label><label class='label_check left c_on'>";
                          } else {
                            listHTML += "<li><label><label class='label_check left'>";
                          }
                          if ( amount > 0 ) {
                            listHTML += "<input type='radio' class='styled' value='"+data[i].id+"' name='invoice' onclick=\"invoice_payment_load('"+data[i].id+"', this, '"+data[i].number+"');\"></label>";
                          } else {
                              listHTML += "<input type='radio' class='styled' value='"+data[i].id+"' name='invoice' onclick=\"purchase_payment_load('"+data[i].id+"', this, '"+data[i].number+"');\"></label>";
                          }
                          listHTML += "<span class='link'>"+data[i].number+"</span></label></li>";
                      }
                      if ( amount > 0 ) {
                          $('#invoice-payment-invoice-list').html(listHTML);
                          invoice_payment_load(data[0].id, null, data[0].number);
                      } else {
                          $('#purchase-payment-purchase-list').html(listHTML);
                          purchase_payment_load(data[0].id, null, data[0].number);
                      }
                      $('#dialog_new_afschrift').click();
                  }
            }
        });
	});
    
    $('#dialog_new_afschrift').fancybox();    
	
	$(".factuur-tabel > tbody > tr:odd").addClass("factuur-odd");
	$(".factuur-tabel > tbody > tr:not(.odd)").addClass("factuur-even");  
	
	$('#kasboek_date').datepicker({
		showOn: 'button',
		buttonText: 'Show Date',
		buttonImageOnly: true,
		buttonImage: '../images/calendar-icon.png',
		dateFormat: "DD d MM yy", 
		alignment: "top",
        onSelect: function(dateText, inst){
            var theDate = new Date(Date.parse($(this).datepicker('getDate')));
            var dateFormatted = $.datepicker.formatDate('yy-m-d', theDate);
            $("#kas_date").val(dateFormatted);
            $('#overige_form').submit();
        }
	});
	
	$('#kasboek_date').click(function() {
		$(this).datepicker('show');
	});
	
	$('#kasboek_date').change(function() {
		var text = $('<span>').html($(this).val()).appendTo(this.parentNode);
		var w = text.innerWidth();
		text.remove();
		$(this).width(w + 20);
	});
});		

function showCalendar() {
	//$('div.kasboek-algemeen div.link ul li.last a input').onclick();
	//$('div.kasboek-algemeen div.link ul li.last a input').datepicker({dateFormat: "dd-mm-yy", alignment: "top"});
}