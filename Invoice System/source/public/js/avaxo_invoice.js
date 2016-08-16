var invoice_enable_auto_save = true
var invoice_auto_save_ready = false
$(document).ready(function(){

    if($('[name="invoice[id]"]').val() != 0){
        invoice_init_log($('[name="invoice[id]"]').val());
    }

    $('[name="invoice[intro]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        $('[name="invoice[intro]"]').val($(this).html().trim());
    }).blur(invoice_auto_save);

    $('[name="invoice[info]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        $('[name="invoice[info]"]').val($(this).html().trim());
    }).blur(invoice_auto_save);;

	$("#sendinvoice-button").fancybox({onStart: invoice_open_final});
    $('#contact-add-link').fancybox({onStart: function(){
        $('#contact-add-form input').val('');
    }});

    $('[name="invoice[discount]"]').keydown(function(event){
        invoice_numeric(this, event, false);
    }).keyup(function(){
        invoice_recalc_total();
    }).change(function(){
        invoice_auto_save();
    });
	
	$("#downloadinvoice").click(function(){
		if( $('[name="invoice[id]"]').val() == 0 ){
			invoice_auto_save().success(function(){
				window.location = baseUrl + "/invoices/index/pdf/id/" + $('[name="invoice[id]"]').val();
			});
			return;
		}
		window.location = baseUrl + "/invoices/index/pdf/id/" + $('[name="invoice[id]"]').val();
	});
	
	
	//$('[name="invoice[contact_id]"]').combobox();

	// on client change get client data and set it to the fields
	$('[name="invoice[contact_id]"]').change(function(){
        disable_loader = true;
		$.post( baseUrl + '/invoices/index/contact-changed', {id:this.value}, function(data) {
			$('#contact_number').text(data.number);
		});
		//invoice_auto_save()
	})
	
	if( $('[name="invoice[contact_id]"]').val() > 0 ){
		$('[name="invoice[contact_id]"]').change();
	}
	
	$('[name^="row"]').each(function(index, element){
		invoice_init_product(element.value)
	})

    $('[name="contact[company_name]"]').autocomplete({
        source: function( request, response ) {
            disable_loader = true;
            $.ajax({
                url: baseUrl + "/invoices/index/contact-autocomplete",
                method: 'post',
                data: {
                    limit: 10,
                    term: request.term,
                    field: 'company_name'
                },
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return {
                            /*label: String(item.firstname + ' ' + item.lastname).trim(),*/
                            label: String(item.company_name).trim(),
                            value: item.company_name,
                            data: item
                        }
                    }));
                }
            });
        },
        minLength: 0,
        select: function( event, ui ) {
            if( ui.item ){
                console.log('selected');
                invoice_contact_select(ui.item.data);
            }
        },

        change: function(event, ui){
        }
    }).change(function(){
        invoice_check_contact_name('[name="contact[company_name]"]');
        invoice_recalc_total();
    });
    
    $('[name="contact[firstname]"]').autocomplete({
        source: function( request, response ) {
            disable_loader = true;
            $.ajax({
                url: baseUrl + "/invoices/index/contact-autocomplete",
                method: 'post',
                data: {
                    limit: 10,
                    term: request.term,
                    field: 'firstname'
                },
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return {
                            /*label: String(item.firstname + ' ' + item.lastname).trim(),*/
                            label: String(item.firstname).trim(),
                            value: item.firstname,
                            data: item
                        }
                    }));
                }
            });
        },
        minLength: 0,
        select: function( event, ui ) {
            if( ui.item ){
                invoice_contact_select(ui.item.data);
            }
        },

        change: function(event, ui){
        }
    }).change(function(){
        invoice_check_contact_name('[name="contact[firstname]"]');
        invoice_recalc_total();
    });
    
    $('[name="contact[lastname]"]').autocomplete({
        source: function( request, response ) {
            disable_loader = true;
            $.ajax({
                url: baseUrl + "/invoices/index/contact-autocomplete",
                method: 'post',
                data: {
                    limit: 10,
                    term: request.term,
                    field: 'lastname'
                },
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return {
                            /*label: String(item.firstname + ' ' + item.lastname).trim(),*/
                            label: String(item.lastname).trim(),
                            value: item.lastname,
                            data: item
                        }
                    }));
                }
            });
        },
        minLength: 0,
        select: function( event, ui ) {
            if( ui.item ){
                invoice_contact_select(ui.item.data);
            }
        },

        change: function(event, ui){
        }
    }).change(function(){
        invoice_check_contact_name('[name="contact[lastname]"]');
        invoice_recalc_total();
    });
	
    $('[name="contact[vat_number]"]').blur(function(){
        var vat = $(this).val();                                    
        $.post( baseUrl + '/contacts/index/validate-vat', {vat:vat}, function(data) {
            if ( data.is_valid == true ) {
                $('#contact_vat_status').css('color', '#73C83F');
                $('#contact_vat_status').html("V");
                $('#contact_is_b2b').val('1');
                $('[name="invoice[expire_time]"]').val($('#invoice_b2b_payment_end_date').val());
                $('#expire_date').text($('#invoice_b2b_payment_end_date').val());                   
            } else {
                $('#contact_vat_status').css('color', '#CC0000');
                $('#contact_vat_status').html("X");
                $('#contact_is_b2b').val('0');
                $('[name="invoice[expire_time]"]').val($('#invoice_b2c_payment_end_date').val());
                $('#expire_date').text($('#invoice_b2c_payment_end_date').val());
            }
            invoice_recalc_total();
        });                          
    });
	                       
	// change invoice number 
	// date picker for invoice date
	$('#invoice_number').click(function(){
		var input = $($(this).parent().children('input')[0]);
		if( input.css('display') == 'none' ){
			input.parent().children('span').css('display', 'none');
			input.css('display', 'block');
			input.val($(this).text());
			input.focus()
		}
	}).css('cursor', 'pointer');
	
	$('[name="invoice[number]"]').blur(function(){
		$('#invoice_number').text(this.value).css('display', 'block');
		$(this).css('display', 'none');
		invoice_auto_save()
	}).keypress(function(event){
		if( event.keyCode == 13 ){
			$(this).blur()
		}
	});
	
	// date picker for invoice date
	$('#invoice_date').click(function(){
		var input = $($(this).parent().children('input')[0]);
		if( input.css('display') == 'none' ){
			input.parent().children('span').css('display', 'none');
			input.css('display', 'block');
			input.val($(this).text());
			input.datepicker({dateFormat: "dd-mm-yy", onSelect:function(){
				$('#invoice_date').text($('[name="invoice[invoice_time]"]').val()).css('display','block');
				$(this).css('display', 'none');
				invoice_auto_save()
			}})
			input.focus()
		}
	}).css('cursor', 'pointer');

    // date picker for expire date
    $('#expire_date').click(function(){
        var input = $($(this).parent().children('input')[0]);
        if( input.css('display') == 'none' ){
            input.parent().children('span').css('display', 'none');
            input.css('display', 'block');
            input.val($(this).text());
            input.datepicker({dateFormat: "dd-mm-yy", onSelect:function(){
                $('#expire_date').text($('[name="invoice[expire_time]"]').val()).css('display','block');
                $(this).css('display', 'none');
                invoice_recalc_total();
                invoice_auto_save()
            }})
            input.focus()
        }
    }).css('cursor', 'pointer');
	
	$("#sendinvoice-form").submit(function(event){
        $('[name=final]').val(1);
        invoice_save();
		return false;
	});
	
	$('[name="invoice[notice]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).focus(function(){
        $(this).addClass('gray-input');
    }).blur(function(){
        $(this).removeClass('gray-input');
        invoice_auto_save();
    }).bind('hallomodified', function(){
        $('[name="invoice[notice]"]').val($(this).html().trim());
    });
	/*
	$('[name="vat_included"]').change(function(){
		totals = $('[id^="product_total_"]');
		for( var i = 0; i < totals.length; i++ ){
			var index = invoice_product_name_to_index(totals[i].id);
			var price           = Number($('[name="product[' + index + '][price]"]').val());
			var vat             = Number($('[name="product[' + index + '][vat]"]').val());
			
			if( this.checked ){
				price = price + ( price * (vat/100));
			}else{
				price = price / (1 + (vat/100));
			}
			
			$('[name="product[' + index + '][price]"]').val(price).blur();
			invoice_recalc_product(index);
		}
		
		invoice_auto_save();
	});
	*/
	$('[name="vat_included"]').change(function() {
		totals = $('[id^="product_total_"]');
		for (var i = 0; i < totals.length; i++ ) {
			var index	  = invoice_product_name_to_index(totals[i].id);
            var priceTxt  = $('[name="product[' + index + '][price]"]').val();
            var price     = Number(char_replace(char_replace(priceTxt, '.', ''), ',', '.'));
			var vat		  = Number($('[name="product[' + index + '][vat]"]').val());
			
			if ($(this).val() == "1") {
				//price = price + (price * (vat / 100));
			} else {                                         
				//price = price / (1 + (vat / 100));
			}
			
			$('[name="product[' + index + '][price]"]').val(number_format(price, 2, ',', '.')).blur();
			invoice_recalc_product(index);
		}
		
		invoice_auto_save();
	});     

	invoice_init_sorting()
	
	//setInterval(invoice_auto_save, 5000)
	invoice_row_index = $('#invoice_row_index').val();
    $('[name="product['+(invoice_row_index-1)+'][price]"]').on('keypress', function(event){
        if ( event.keyCode == 9 ) {
           invoice_add_row();
        }
    });
	invoice_auto_save_ready = true;
	invoice_recalc_total()
});


function invoice_contact_select(contact){
    if ( contact.is_intro == 1 ) {
        $('#intro_text').html("<p>Intracommunautaire levering vrij van BTW bij toepassing van artikel 39bis, 1 W.B.T.W.</p>");
        //$('.invoice-small select').html('<option value="0">0%</option>');
        //$('.invoice-small .vat-drop-down').val('0%');
        invoice_recalc_total();
    } else {
        $('#intro_text').html("<p>&nbsp;</p>");
    }           
    $('#contact_number').html(contact.formated_number);
    $('[name="invoice[contact_id]"]').val(contact.id);
    $('[name="invoice[contact_id]"]').data('contact-name', String(contact.company_name).trim());
    $('[name="invoice[contact_id]"]').data('contact-id', contact.id);
    $('[name="contact[company_name]"]').val(contact.company_name);
    $('[name="contact[firstname]"]').val(contact.firstname);
    $('[name="contact[lastname]"]').val(contact.lastname);
    $('[name="contact[address]"]').val(contact.address);
    $('[name="contact[postcode]"]').val(contact.postcode);
    $('[name="contact[city]"]').val(contact.city);
    $('[name="contact[country]"]').val(contact.country);
    $('[name="contact[vat_number]"]').val(contact.vat_number);
    $('[name="contact[is_b2b]"]').val(contact.is_b2b);
    if ( contact.is_b2b == 1 ) {
        $('#contact_vat_status').css('color', '#73C83F');
        $('#contact_vat_status').html("V");
        $('#contact_is_b2b').val('1');     
        $('[name="invoice[expire_time]"]').val($('#invoice_b2b_payment_end_date').val());
        $('#expire_date').text($('#invoice_b2b_payment_end_date').val());
    } else {
        $('#contact_vat_status').css('color', '#CC0000');
        $('#contact_vat_status').html("X");
        $('#contact_is_b2b').val('0');
        $('[name="invoice[expire_time]"]').val($('#invoice_b2c_payment_end_date').val());
        $('#expire_date').text($('#invoice_b2c_payment_end_date').val());
    }
    $('[name="contact[email_address]"]').val(contact.email_address);
    $('#invoice_update_exist_contact').css('display', 'inline-block');
    $('#msg_add_contact').css('display', 'none');
    var intro = $('[name="intro_pattern"]').val();
    intro = intro.replace('{client_firstname}', contact.firstname);
    intro = intro.replace('{client_lastname}', contact.lastname);
    intro = intro.replace('{client_number}', contact.formated_number);
    intro = intro.replace('{invoice_number}', $('#invoice_number').html());
    intro = intro.replace('{offer_number}', $('#invoice_number').html());
    intro = intro.replace('{invoice_expiration_date}', $('#expire_date').html());
    intro = intro.replace('{offer_expiration_date}', $('#expire_date').html());
    intro = intro.replace('{invoice_total_price}', ' &euro; ' + $('#total_including_vat').html());
    $('[name="invoice[intro]"]').val(intro);
    if ( intro == "" ) intro = "&nbsp;";
    $('[name="invoice[intro]"]').parent().find('div.wysiwyg').html(intro);
    var notice = $('[name="notice_pattern"]').val();
    notice = notice.replace('{client_firstname}', contact.firstname);
    notice = notice.replace('{client_lastname}', contact.lastname);
    notice = notice.replace('{client_number}', contact.formated_number);
    notice = notice.replace('{invoice_number}', $('#invoice_number').html());
    notice = notice.replace('{offer_number}', $('#invoice_number').html());
    notice = notice.replace('{invoice_expiration_date}', $('#expire_date').html());
    notice = notice.replace('{offer_expiration_date}', $('#expire_date').html());
    notice = notice.replace('{invoice_total_price}', ' &euro; ' + $('#total_including_vat').html());
    $('[name="invoice[notice]"]').val(notice);
    if ( notice == "" ) notice = "&nbsp;";
    $('[name="invoice[notice]"]').parent().find('div.wysiwyg').html(notice);
    
    invoice_contact_update_icon();
    invoice_auto_save();
}

function invoice_contact_update_icon(obj){
    $(obj).removeClass('invoice-contact-add');
    $(obj).removeClass('invoice-contact');
    $('[name="contact[company_name]"]').removeClass('invoice-contact-add');
    $('[name="contact[company_name]"]').removeClass('invoice-contact');
    $('[name="contact[firstname]"]').removeClass('invoice-contact-add');
    $('[name="contact[firstname]"]').removeClass('invoice-contact');
    $('[name="contact[lastname]"]').removeClass('invoice-contact-add');
    $('[name="contact[lastname]"]').removeClass('invoice-contact');
    if( $('[name="invoice[contact_id]"]').val() == 0 ){
        $('[name="contact[company_name]"]').addClass('invoice-contact-add');
        $('[name="contact[firstname]"]').addClass('invoice-contact-add');
        $('[name="contact[lastname]"]').addClass('invoice-contact-add');
    }else{
        $('[name="contact[company_name]"]').addClass('invoice-contact');
        $('[name="contact[firstname]"]').addClass('invoice-contact');
        $('[name="contact[lastname]"]').addClass('invoice-contact');
    }
}

function invoice_check_contact_name(obj){                   
    if( $('[name="invoice[contact_id]"]').data('contact-name') != $('[name="contact[company_name]"]').val() ){
        $('[name="invoice[contact_id]"]').val(0);
        $('#invoice_update_exist_contact').css('display', 'none');
        $('#msg_add_contact').css('display', 'block'); 
        invoice_contact_update_icon(obj);
    }else{
        $('[name="invoice[contact_id]"]').val($('[name="invoice[contact_id]"]').data('contact-id'));
        invoice_contact_update_icon(obj);
    }                                                                                      
    //invoice_auto_save();
}

function invoice_init_sorting(){
	// sorting
	$("#sortableproducts").sortable({handle: ".sorthandle"});
}

var invoice_row_index = 0;
function invoice_add_row()
{
	invoice_row_index++;
    var intro_value = $('#intro_value').val();
	$.post(baseUrl + '/invoices/index/add-row', {index: invoice_row_index, intro: intro_value}, function(html){
		$('#sortableproducts').append(html)
		var row = $('#sortableproducts').children().last()
		row.find('select.dropdown').easySelectBox()
        var index = invoice_product_name_to_index(row.find('[name^="row"]')[0].name);
		invoice_init_product(index)
        $('[name="product[' + index + '][qty]"]').focus();
        /*$('[name="product[' + index + '][qty]"]').on('blur', function(){
            var qty = char_replace($(this).val(), '.', '');
            qty = char_replace(qty, ',', '.');
            $(this).val(number_format(qty, 2, ',', '.'));
        });*/
        $('[name="product['+(index-1)+'][price]"]').unbind('keypress');
        $('[name="product['+index+'][price]"]').on('keypress', function(event){
            if ( event.keyCode == 9 ) {
               invoice_add_row();
            }
        });
		invoice_auto_save()
	});
	invoice_init_sorting()
}

function invoice_remove_row(row){
	var container = $(row).parents('.product1').first()
	container.fadeOut('slow', function() { container.remove(); invoice_auto_save(); });
}   

function invoice_init_product(index){
	invoice_init_product_qty_change(index)
	invoice_init_product_price_change(index)
	invoice_init_product_description_change(index)
	invoice_init_product_vat_change(index);
    invoice_init_product_discount_change(index);
    invoice_init_product_tag_change(index);    
}

function invoice_update_product(id, index){
	if( id == 0 ){
        $('[name="product[' + index + '][description]"]').val('');
        $('[name="product[' + index + '][product_id]"]').val(0);
		return
	}
    disable_loader = true;
	$.post( baseUrl +  '/invoices/index/product-changed', {id:id, index: index}, function(data) {
		$('[name="product[' + data.index + '][description]"]').val(data.product.name);
        $('[name="product[' + data.index + '][product_id]"]').val(data.product.id);
        //var vat_included = $('[name="vat_included"]')[0].checked;
        var vat_included = $('[name="vat_included"]').val();
        var price = Number(data.product.price);
        var vat = Number($('[name="product[' + data.index + '][vat]"]').val());

        if(vat_included ){
            price = price + (price * (vat/100));
        }

		$('[name="product[' + data.index + '][price]"]').val(number_format(price, 2, ',', '.')).blur();
		invoice_recalc_product(data.index)
		invoice_auto_save()
	});
}

function invoice_init_product_qty_change(index){
	var product = $('[name="product[' + index + '][qty]"]') 
	product.change(invoice_auto_save);
    var qty = char_replace(product.val(), '.', '');
    qty = char_replace(qty, ',', '.');
    product.val(number_format(qty, 2, ',', '.'));
    product.on('blur', function(){
        var qty = char_replace($(this).val(), '.', '');
        qty = char_replace(qty, ',', '.');
        $(this).val(number_format(qty, 2, ',', '.'));
    });
	product.keyup(function(){
		invoice_recalc_product(invoice_product_name_to_index(this.name))
	})
	
	product.keydown(function(event){
		invoice_numeric(this, event)
	})
}

function invoice_init_product_price_change(index){
	var product = $('[name="product[' + index + '][price]"]')
	
	product.change(invoice_auto_save);
	product.keyup(function(){
		invoice_recalc_product(invoice_product_name_to_index(this.name))
	})
	
	product.blur(function(){
        var v = $(this).val();
        v = char_replace(char_replace(v, '.', ''), ',', '.');
		$(this).val(number_format(v, 2, ',', '.'));
	})
	
	product.keydown(function(event){
		invoice_numeric(this, event, true)
	})
}

function invoice_init_product_description_change(index){
	var product = $('[name="product[' + index + '][description]"]')
    product.change(invoice_auto_save);

    product.parent().find('.wysiwyg').focus(function(){
        if( $(this).text().trim().length == 0 ){
            $(this).autocomplete('search', '');
        }
    });

    product.parent().find('.wysiwyg').blur(function(){
        invoice_auto_save();
    })

    product.parent().find('.wysiwyg').bind('hallomodified', function(){
        var index = invoice_product_name_to_index(this.id);
        $('[name="product[' + index + '][description]"]').val($(this).html().trim());

        if( $(this).text().trim().length == 0 ){
            $('[name="product[' + index + '][product_id]"]').val(0);
        }
    })

    product.parent().find('.wysiwyg').autocomplete({
        source: function( request, response ) {
            disable_loader = true;
	        $.ajax({
	            url: baseUrl + "/invoices/index/product-autocomplete",
                method: 'post',
	            data: {
	                limit: 10,
	                term: request.term.trim()
	            },
	            success: function( data ) {
	                response( $.map( data, function( item ) {
	                    return {
	                        label: item.label ? item.label : (item.article_code.length ? '[' + item.article_code + '] ' : '') + item.name,
                            //label: String(item.name).trim(),
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
                var index = invoice_product_name_to_index(this.id);
                invoice_update_product(ui.item.data.id, index);
                $('#product_tag_' + index).val(ui.item.data.income_category_path);
                $('[name="product[' + index + '][tag_id]"]').val(ui.item.data.income_tag_id);
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

function invoice_init_product_tag_change(index){
    var product = $('#product_tag_' + index);
    product.change(invoice_auto_save);
    product.focus(function(){
//       $(this).val('');
//       $('[name="product[' + invoice_product_name_to_index(this.id) + '][tag_id]"]').val(0);
    });

    product.keydown(function(event){
        if( event.keyCode == 9 && $(this).parents('.product1').next().length == 0 ){
            invoice_add_row();
        }
    });

    product.focus(function(){
        $(this).autocomplete('search', '');
    });

    product.autocomplete({
        source: function( request, response ) {
            disable_loader = true;
            $.ajax({
                url: baseUrl + "/invoices/index/category-autocomplete",
                data: {
                    limit: 10,
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
                $('[name="product[' + invoice_product_name_to_index(this.id) + '][tag_id]"]').val(ui.item.data.id);
                invoice_auto_save();
            }
        },

        change: function(event, ui){
        }
    });
}

function invoice_init_product_vat_change(index){
	$('[name="product[' + index + '][vat]"]')
        .combobox({input_class:'vat-drop-down'})
        .change(function(event, skipsave){
            invoice_recalc_product(invoice_product_name_to_index(this.name));

            if( skipsave == undefined ){
                invoice_auto_save();
            }
        });
}

function invoice_init_product_discount_change(index){
    var discount = $('[name="product[' + index + '][discount]"]');

    discount.keydown(function(event){
        invoice_numeric(this, event, false);
    }).keyup(function(){
        invoice_recalc_product(invoice_product_name_to_index(this.name));
    });

    discount.change(function(){
        invoice_auto_save();
    })
}

function invoice_recalc_product(index){
    //var vat_included = $('[name="vat_included"]')[0].checked;
    var vat_included = $('[name="vat_included"]').val();
    var priceTxt        = $('[name="product[' + index + '][price]"]').val();
    var price           = Number(char_replace(char_replace(priceTxt, '.', ''), ',', '.'));
    var vat             = Number($('[name="product[' + index + '][vat]"]').val())                

    if( vat_included == 1 ){
        price = price / (1 + (vat/100));
    }

	//var qty = Number($('[name="product[' + index + '][qty]"]').val())
    //var discount = Number($('[name="product[' + index + '][discount]"]').val())
    var qty             = char_replace($('[name="product[' + index + '][qty]"]').val(), '.', '');
    qty = Number(char_replace(qty, ',', '.'));
    var discount        = char_replace($('[name="product[' + index + '][discount]"]').val(), '.', '');
    discount = Number(char_replace(discount, ',', '.'));
	var total = (price * qty) - (price * qty) * (discount/100);
	$('#product_total_' + index).text(number_format(total, 2, ',', '.'));
	invoice_recalc_total()
}

function invoice_recalc_total(){
    //var vat_included = $('[name="vat_included"]')[0].checked;
    var vat_included = $('[name="vat_included"]').val();  
    var invoice_discount = Number($('[name="invoice[discount]"]').val());
    var total_discount = 0.0;
	var total_exluding_vat = 0.0
    var total_including_vat = 0.0;
	var total_vat = 0.0
	
	totals = $('[id^="product_total_"]');
	for( var i = 0; i < totals.length; i++ ){
        var index           = invoice_product_name_to_index(totals[i].id);
        var priceTxt        = $('[name="product[' + index + '][price]"]').val();
        var price           = Number(char_replace(char_replace(priceTxt, '.', ''), ',', '.'));
        var vat             = Number($('[name="product[' + index + '][vat]"]').val());           

        if( vat_included == 1 ){
            price = price / (1 + (vat/100));         
        }

        //var qty             = Number($('[name="product[' + index + '][qty]"]').val());
        //var discount        = Number($('[name="product[' + index + '][discount]"]').val());
        var qty             = char_replace($('[name="product[' + index + '][qty]"]').val(), '.', '');
        qty = Number(char_replace(qty, ',', '.'));
        var discount        = char_replace($('[name="product[' + index + '][discount]"]').val(), '.', '');
        discount = Number(char_replace(discount, ',', '.'));
        var current_total   = price * qty;              

        total_vat += current_total * ((100-discount)/100) * (vat/100);
        total_including_vat += current_total * ((100-discount)/100) + current_total * ((100-discount)/100) * (vat/100);

        total_discount +=  current_total * (discount/100);
        /*if( vat_included == 1 ){
            total_exluding_vat += current_total * ((100-discount)/100) - current_total * ((100-discount)/100) * (vat/100);
        } else {
            total_exluding_vat += current_total * ((100-discount)/100);
        } */
        total_exluding_vat += current_total * ((100-discount)/100);
	}                                                       

    total_discount += total_exluding_vat * (invoice_discount/100);

	$('#total_discount').text(number_format(total_discount, 2, ',', '.'))
	$('#total_excluding_vat').text(number_format(total_exluding_vat, 2, ',', '.'))
	$('#total_vat').text(number_format(total_vat, 2, ',', '.'))
	$('#total_including_vat').text(number_format((total_including_vat), 2, ',', '.'))
    var intro = $('[name="intro_pattern"]').val();
    intro = intro.replace('{invoice_total_price}', ' &euro; '+number_format((total_including_vat - total_discount), 2, ',', '.'));     
    intro = intro.replace('{client_number}', $('[name=contact_number]').val());
    intro = intro.replace('{client_firstname}', $('[name="contact[firstname]"]').val());
    intro = intro.replace('{client_lastname}', $('[name="contact[lastname]"]').val());
    intro = intro.replace('{invoice_number}', $('#invoice_number').html());
    intro = intro.replace('{offer_number}', $('#invoice_number').html());
    intro = intro.replace('{invoice_expiration_date}', $('#expire_date').html());
    intro = intro.replace('{offer_expiration_date}', $('#expire_date').html());
    if ( intro == "" ) intro = "&nbsp;";
    $('[name="invoice[intro]"]').parent().find('div.wysiwyg').html(intro);
    $('[name="invoice[intro]"]').val(intro);
    var notice = $('[name="notice_pattern"]').val();
    notice = notice.replace('{invoice_total_price}', ' &euro; '+number_format((total_including_vat - total_discount), 2, ',', '.'));     
    notice = notice.replace('{client_number}', $('[name=contact_number]').val());
    notice = notice.replace('{client_firstname}', $('[name="contact[firstname]"]').val());
    notice = notice.replace('{client_lastname}', $('[name="contact[lastname]"]').val());
    notice = notice.replace('{invoice_number}', $('#invoice_number').html());
    notice = notice.replace('{offer_number}', $('#invoice_number').html());
    notice = notice.replace('{invoice_expiration_date}', $('#expire_date').html());
    notice = notice.replace('{offer_expiration_date}', $('#expire_date').html());
    if ( notice == "" ) notice = "&nbsp;";
    $('[name="invoice[notice]"]').parent().find('div.wysiwyg').html(notice);
    $('[name="invoice[notice]"]').val(notice);
}

function invoice_auto_save(){
	if( !invoice_enable_auto_save || !invoice_auto_save_ready ){
		return;
	}

    disable_loader = true;
	return invoice_save();
}

function invoice_save(){
    invoice_auto_save_ready = false;
    if ( Number($('#contact_is_b2b').val()) != 1 ) {
        $('[name="contact[vat_number]"]').val('');
    }
	return $.post(baseUrl + "/invoices/index/save", $('#invoice-form, #sendinvoice-form').serialize())
	.success(function(data){
		if( data.redirect ){
			invoice_enable_auto_save = false;
			window.location = data.redirect;
			return;
		}
		
		if( data.id ){
            invoice_init_log(data.id);
			$('[name="invoice[id]"]').val(data.id);
            $('[name="invoice[contact_id]"]').val(data.contact_id);
            invoice_contact_update_icon();
		}
	}).complete(function(){
        invoice_auto_save_ready = true;
    });
}

function invoice_open_final(element){      	
	$.post(baseUrl + "/invoices/index/send-invoice-fill", $('#invoice-form').serialize()).success(function(data){
        $('[name="invoice[contact_id]"]').val(data.contact_id);
        $('[name="invoice[contact_id]"]').data('contact-name', String(data.company_name).trim());
        $('[name="invoice[contact_id]"]').data('contact-id', data.contact_id);
		$('[name=email]').val(data.email);
        $('[name=subject]').val(data.subject);
        $('#div_subject').html(data.subject);
        $('[name=body]').val(data.body);
        $('#div_body').html(data.body);        
	});
	
	return true;
}

function invoice_save_contact(){
    $.post(baseUrl + '/contacts/index/save-contact', $('#contact-add-form').serialize()).success(function(data){
        $('[name="invoice[contact_id]"]')
            .append($('<option>', { value : data.id })
            .text(String(data.firstname + ' ' + data.lastname).trim())).val(data.id).change();

        $.fancybox.close();
    });
}

function invoice_product_name_to_index(name){
	return name.match(/\d+/)[0];
}

function invoice_init_log(id){
    if( log_source_id == undefined ){
        log_source_type = 'invoice';
        log_source_id = id;
        log_init();
    }
}

function invoice_numeric(element, event, decimal){
    if( !(
    	   event.keyCode == 9
    	|| (decimal && element.value.indexOf('.') == -1 && (event.keyCode == 110 || event.keyCode == 190) ) // . (dot)
        || ( (event.keyCode == 109 || event.keyCode == 173) ) // -
        || event.keyCode == 188                            // comma
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
