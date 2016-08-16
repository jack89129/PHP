function invoice_payment_load(id, elem, invoice_number){
    if (elem) {
        $('#payment-list .label_check').removeClass('c_on');
        $(elem).parent().addClass('c_on');
    }
    $.post(baseUrl + '/invoices/index/payment-dialog-invoice-load', {id:id}).success(function(data){
        $('#payment-content').html(data.invoice);
        $('#payment-form').unbind('submit');
        $('#payment-form').submit(function(){
            var is_email_notify = 0;
            var to_email;
            $('#payment-form input').each(function() {
                if ($(this).attr('name') == 'email[send]' && $(this).attr('checked')) {
                    is_email_notify = 1;
                }
                if ($(this).attr('name') == 'email[email]') {
                    to_email = $(this).val();
                }
            });
            var payment_method = "";
            $('#payment-form select').each(function() {
                if ( $(this).attr('name') == 'payment[payment_method]' ) {
                    payment_method = $(this).val();
                }
            });
            var kas_date = $('#kas_date').val();
            var page_type = $('#page_type').val();
            var message = $('#payment-form textarea').val();
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
        $('#payment-list .label_check').removeClass('c_on');
        $(elem).parent().addClass('c_on');
    }
    $.post(baseUrl + '/purchases/index/payment-dialog-purchase-load', {id:id}).success(function(data){
        $('#payment-content').html(data.purchase);
        $('#payment-form').unbind('submit');
        $('#payment-form').submit(function(){
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
                                   
        $("#invoice-payment-dialog").html(
            "<form id='payment-form' action='#'>\
                <fieldset>\
                <h2>'Betalingen'</h2>\
                <div class='left-container'>\
                    <div id='payment-list' style='outline:none; height:400px; overflow-y:auto;'>\
                    </div>\
                </div>\
                <div id='payment-content' class='right-container'>\
                </div>\
                <div class='clear'></div>\
            </fieldset>\
        </form>");             
        
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
                          if ( !(data[i].number.indexOf("INFAC") !== -1) ) {                      
                            listHTML += "<input type='radio' class='styled' value='"+data[i].id+"' name='invoice' onclick=\"invoice_payment_load('"+data[i].id+"', this, '"+data[i].number+"');\"></label>";
                          } else {
                              listHTML += "<input type='radio' class='styled' value='"+data[i].id+"' name='invoice' onclick=\"purchase_payment_load('"+data[i].id+"', this, '"+data[i].number+"');\"></label>";
                          }
                          listHTML += "<span class='link'>"+data[i].number+"</span></label></li>";
                      }
                      $('#payment-list').html(listHTML);
                      if ( !(data[0].number.indexOf("INFAC") !== -1) ) {                      
                          invoice_payment_load(data[0].id, null, data[0].number);
                      } else {
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

function wholesaler_numeric(element, event, decimal){
    if( !(
           event.keyCode == 9
        || (decimal && element.value.indexOf('.') == -1 && (event.keyCode == 110 || event.keyCode == 190)) // . (dot)
        || event.keyCode == 8                              // backspace
        || event.keyCode == 46                             // delete
        || (event.keyCode >= 35 && event.keyCode <= 40)    // arrow keys/home/end
        || (event.keyCode >= 48 && event.keyCode <= 57)    // numbers on keyboard
        || (event.keyCode >= 96 && event.keyCode <= 105)   // number on keypad
        )
        ) {
            event.preventDefault(); 
    }
}

var wholesaler_row_index = 0; 

function wholesaler_product_name_to_index(name){
    return name.match(/\d+/)[0];
}

function wholesaler_init_sorting(){
    // sorting
    $("#sortableproducts").sortable({handle: ".sorthandle"});
}

function wholesaler_add_row()     
{
    wholesaler_row_index++;
    $.post(baseUrl + '/wholesalers/index/add-row', {index: wholesaler_row_index}, function(html){
        $('#sortableproducts').append(html)
        var row = $('#sortableproducts').children().last()
        row.find('select.dropdown').easySelectBox()
        var index = wholesaler_product_name_to_index(row.find('[name^="row"]')[0].name);
        wholesaler_init_product(index)
        $('[name="product[' + index + '][qty]"]').focus();        
    });
    wholesaler_init_sorting();
}

function wholesaler_init_product(index) {
    wholesaler_init_product_qty_change(index)
    wholesaler_init_product_vat_change(index);
    wholesaler_init_product_price_change(index);
    wholesaler_init_product_description_change(index);
    wholesaler_init_product_tag_change(index);
}

function wholesaler_init_product_qty_change(index){
    var product = $('[name="product[' + index + '][qty]"]')
    //product.change(purchase_auto_save);
    product.keyup(function(){
        //purchase_recalc_product(purchase_product_name_to_index(this.name))
    })
    
    product.keydown(function(event){
        wholesaler_numeric(this, event)
    })
}

function wholesaler_init_product_vat_change(index){
    $('[name="product[' + index + '][vat]"]')
    .combobox({input_class:'vat-drop-down'})
    /*.change(function(event, skipsave){
        purchase_recalc_product(purchase_product_name_to_index(this.name));
        if( skipsave == undefined ){
            //purchase_auto_save();
        }
    });*/
}

function wholesaler_init_product_price_change(index){
    var product = $('[name="product[' + index + '][price]"]')
    
    //product.change(purchase_auto_save);
    product.keyup(function(){
        //wholesaler_recalc_product(purchase_product_name_to_index(this.name))
    })
    
    product.blur(function(){
        $(this).val(Number($(this).val()).toFixed(2))
    })
    
    product.keydown(function(event){
        wholesaler_numeric(this, event, true)
    })
}

function wholesaler_init_product_description_change(index){
    var product = $('[name="product[' + index + '][description]"]')
    //product.change(purchase_auto_save);

    product.parent().find('.wysiwyg').focus(function(){
        if( $(this).text().trim().length == 0 ){
            $(this).autocomplete('search', '');
        }
    });

    product.parent().find('.wysiwyg').blur(function(){
        //purchase_auto_save();
    })

    product.parent().find('.wysiwyg').bind('hallomodified', function(){
        var index = wholesaler_product_name_to_index(this.id);
        $('[name="product[' + index + '][description]"]').val($(this).html().trim());

        if( $(this).text().trim().length == 0 ){
            $('[name="product[' + index + '][product_id]"]').val(0);
        }
    })

    product.parent().find('.wysiwyg').autocomplete({
        source: function( request, response ) {
            disable_loader = true;
            $.ajax({
                url: baseUrl + "/purchases/index/product-autocomplete",
                method: 'post',
                data: {
                    limit: 10,
                    term: request.term.trim()
                },
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return {
                            label: item.label ? item.label : (item.article_code.length ? '[' + item.article_code + '] ' : '') + item.name,
                            value: item.name,
                            data: item
                        }
                    }));
                }
            });
        },
        minLength: 0,
        select: function( event, ui ) {
            if( ui.item ){
                var index = wholesaler_product_name_to_index(this.id);
                //purchase_update_product(ui.item.data.id, index);
                $('#product_tag_' + index).val(ui.item.data.expense_category_path);
                $('[name="product[' + index + '][tag_id]"]').val(ui.item.data.expense_tag_id);
                $('[name="product[' + index + '][vat]"]').val(ui.item.data.vat).trigger('change', [true]);
            }
        },
        
        change: function(event, ui){
        }
    });

    product.parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    });
}

function wholesaler_init_product_tag_change(index){
    var product = $('#product_tag_' + index);
    //product.change(purchase_auto_save);
    product.focus(function(){
//       $(this).val('');
//       $('[name="product[' + purchase_product_name_to_index(this.id) + '][tag_id]"]').val(0);
    });

    product.keydown(function(event){
        if( event.keyCode == 9 && $(this).parents('.product1').next().length == 0 ){
            wholesaler_add_row();
        }
    });

    product.focus(function(){
        $(this).autocomplete('search', '');
    });
    
    product.autocomplete({
        source: function( request, response ) {
            disable_loader = true;
            $.ajax({
                url: baseUrl + "/purchases/index/category-autocomplete",
                data: {
                    limit: 20,
                    term: request.term
                },
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return {
                            label: '(' + item.number + ') - ' + item.name,
                            value: '(' + item.number + ') - ' + item.name,
                            data: item
                        }
                    }));
                }
            });
        },
        minLength: 0,
        select: function( event, ui ) {
            if( ui.item ){
                $('[name="product[' + wholesaler_product_name_to_index(this.id) + '][tag_id]"]').val(ui.item.data.id);
                //purchase_auto_save();
            }
        },

        change: function(event, ui){
        }
    });
}

function wholesaler_init_product_discount_change(index){
    var discount = $('[name="product[' + index + '][discount]"]');

    discount.keydown(function(event){
        wholesaler_numeric(this, event, false);
    }).keyup(function(){
        //wholesaler_recalc_product(purchase_product_name_to_index(this.name));
    });

    discount.change(function(){
        //wholesaler_auto_save();
    })
}


function wholesaler_remove_row(row){
    var container = $(row).parents('.product1').first()
    container.fadeOut('slow', function() { container.remove(); });
}