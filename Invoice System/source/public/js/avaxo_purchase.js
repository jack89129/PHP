var purchase_enable_auto_save = true
var purchase_auto_save_ready = false
$(document).ready(function(){

    if($('[name="purchase[id]"]').val() != 0){
        purchase_init_log($('[name="purchase[id]"]').val());
    }

    //purchase_check_contact_name();

    $('[name="purchase[intro]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var html = $(this).html().trim();
            html = html == '<br>' ? '' : html;
        $('[name="purchase[intro]"]').val(html);
    }).blur(purchase_auto_save);

    $('[name="purchase[info]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
            var html = $(this).html().trim();
            html = html == '<br>' ? '' : html;
            $('[name="purchase[info]"]').val(html);
    }).blur(purchase_auto_save);;

    $('[name="purchase[discount]"]').keydown(function(event){
        purchase_numeric(this, event, false);
    }).keyup(function(){
        purchase_recalc_total();
    }).change(function(){
        purchase_auto_save();
    });
	
	$("#downloadpurchase").click(function(){
		if( $('[name="purchase[id]"]').val() == 0 ){
			purchase_auto_save().success(function(){
				window.location = baseUrl + "/purchases/index/pdf/id/" + $('[name="purchase[id]"]').val();
			});
			return;
		}
		window.location = baseUrl + "/purchases/index/pdf/id/" + $('[name="purchase[id]"]').val();
	});
	
	
	//$('[name="purchase[contact_id]"]').combobox();

	// on client change get client data and set it to the fields
	$('[name="purchase[contact_id]"]').change(function(){
        disable_loader = true;
		$.post( baseUrl + '/purchases/index/contact-changed', {id:this.value}, function(data) {
			$('#contact_number').text(data.number);
		});
		//purchase_auto_save()
	})
	
	if( $('[name="purchase[contact_id]"]').val() > 0 ){
		$('[name="purchase[contact_id]"]').change();
	}
	
	$('[name^="row"]').each(function(index, element){
		purchase_init_product(element.value)
	})

    $('[name="contact[company_name]"]').autocomplete({
        source: function( request, response ) {
            disable_loader = true;
            $.ajax({
                url: baseUrl + "/purchases/index/contact-autocomplete",
                method: 'post',
                data: {
                    limit: 10,
                    field: 'company_name',  
                    term: request.term
                },
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return {
                            label: String(item.company_name).trim(),
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
                purchase_contact_select(ui.item.data);
            }
        },

        change: function(event, ui){
        }
    }).change(function(){
        purchase_check_contact_name();
    });
    
    $('[name="contact[firstname2]"]').autocomplete({
        source: function( request, response ) {
            disable_loader = true;
            $.ajax({
                url: baseUrl + "/purchases/index/contact-autocomplete",
                method: 'post',
                data: {
                    limit: 10,
                    field: 'firstname2',
                    term: request.term
                },
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return {
                            label: String(item.firstname2).trim(),
                            value: item.firstname2,
                            data: item
                        }
                    }));
                }
            });
        },
        minLength: 0,
        select: function( event, ui ) {
            if( ui.item ){
                purchase_contact_select(ui.item.data);
            }
        },

        change: function(event, ui){
        }
    }).change(function(){
        purchase_check_contact_name();
    });
    
    $('[name="contact[lastname2]"]').autocomplete({
        source: function( request, response ) {
            disable_loader = true;
            $.ajax({
                url: baseUrl + "/purchases/index/contact-autocomplete",
                method: 'post',
                data: {
                    limit: 10,
                    field: 'lastname2',
                    term: request.term
                },
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return {
                            label: String(item.lastname2).trim(),
                            value: item.lastname2,
                            data: item
                        }
                    }));
                }
            });
        },
        minLength: 0,
        select: function( event, ui ) {
            if( ui.item ){
                purchase_contact_select(ui.item.data);
            }
        },

        change: function(event, ui){
        }
    }).change(function(){
        purchase_check_contact_name();
    });
    
    $('[name="contact[vat_number]"]').blur(function(){
        var vat = $(this).val();                                    
        $.post( baseUrl + '/contacts/index/validate-vat', {vat:vat}, function(data) {
            if ( data.is_valid == true ) {
                $('#contact_vat_status').css('color', '#73C83F');
                $('#contact_vat_status').html("V");
                $('#contact_is_b2b').val('1');
            } else {
                $('#contact_vat_status').css('color', '#CC0000');
                $('#contact_vat_status').html("X");
                $('#contact_is_b2b').val('0');
            }
        });
    });
	
	
	// change purchase number
	// date picker for purchase date
	$('#purchase_number').click(function(){
		var input = $($(this).parent().children('input')[0]);
		if( input.css('display') == 'none' ){
			input.parent().children('span').css('display', 'none');
			input.css('display', 'block');
			input.val($(this).text());
			input.focus()
		}
	}).css('cursor', 'pointer');
	
	$('[name="purchase[number]"]').blur(function(){
		$('#purchase_number').text(this.value).css('display', 'block');
		$(this).css('display', 'none');
		purchase_auto_save()
	}).keypress(function(event){
		if( event.keyCode == 13 ){
			$(this).blur()
		}
	});
	
	// date picker for purchase date
	$('#purchase_date').click(function(){
		var input = $($(this).parent().children('input')[0]);
		if( input.css('display') == 'none' ){
			input.parent().children('span').css('display', 'none');
			input.css('display', 'block');
			input.val($(this).text());
			input.datepicker({dateFormat: "dd-mm-yy", onSelect:function(){
				$('#purchase_date').text($('[name="purchase[invoice_time]"]').val()).css('display','block');
				$(this).css('display', 'none');
				purchase_auto_save()
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
                $('#expire_date').text($('[name="purchase[expire_time]"]').val()).css('display','block');
                $(this).css('display', 'none');
                purchase_auto_save()
            }})
            input.focus()
        }
    }).css('cursor', 'pointer');

    $('[name="vat_included"]').change(function(){
        totals = $('[id^="product_total_"]');
        for( var i = 0; i < totals.length; i++ ){
            var index = purchase_product_name_to_index(totals[i].id);
            var priceTxt  = $('[name="product[' + index + '][price]"]').val();
            var price     = Number(char_replace(char_replace(priceTxt, '.', ''), ',', '.'));
            var vat             = Number($('[name="product[' + index + '][vat]"]').val());

            /*if( this.value ){
                price = price + ( price * (vat/100));
            }else{
                price = price / (1 + (vat/100));
            } */

            $('[name="product[' + index + '][price]"]').val(price).blur();
            purchase_recalc_product(index);
        }

        purchase_auto_save();
    });

	purchase_init_sorting()

	purchase_row_index = $('#purchase_row_index').val();
	purchase_auto_save_ready = true;
	purchase_recalc_total()
    reload_attachments(0);
});


function purchase_contact_select(contact){
    $('[name="purchase[contact_id]"]').val(contact.id);
    $('[name="purchase[contact_id]"]').data('contact-name', String(contact.company_name).trim());
    $('[name="purchase[contact_id]"]').data('contact-id', contact.id);
    $('[name="contact[company_name]"]').val(contact.company_name);
    $('[name="contact[firstname2]"]').val(contact.firstname2);
    $('[name="contact[lastname2]"]').val(contact.lastname2);
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
    } else {
        $('#contact_vat_status').css('color', '#CC0000');
        $('#contact_vat_status').html("X");
        $('#contact_is_b2b').val('0');
    }
    $('[name="contact[email_address]"]').val(contact.email_address);
    $('#purchase_update_exist_contact').css('display', 'inline-block');
    $('#msg_add_contact').css('display', 'none');
    
    //purchase_row_index = 0;
    //$('#sortableproducts').html('');
    
    /*purchase_id = $('#purchase_id').val();
    $.post( baseUrl +  '/wholesalers/index/show-products', {id:contact.id, purchase_id: purchase_id}, function(data) {
        for( var i in data ){
            html = data[i];
            purchase_row_index++;
            $('#sortableproducts').append(html)
            var row = $('#sortableproducts').children().last()
            row.find('select.dropdown').easySelectBox()
            var index = purchase_product_name_to_index(row.find('[name^="row"]')[0].name);
            purchase_init_product(index)
            purchase_recalc_product(index)
            //$('[name="product[' + index + '][qty]"]').focus();
            //$('[name="product[' + index + '][qty]"]').blur();
        };
        purchase_auto_save()
        purchase_contact_update_icon();
        purchase_init_sorting()
    }); */
    
    //purchase_auto_save();
    //purchase_contact_update_icon();
}

function purchase_contact_update_icon(){
    $('[name="contact[company_name]"]').removeClass('invoice-contact-add');
    $('[name="contact[company_name]"]').removeClass('invoice-contact');
    $('[name="contact[firstname2]"]').removeClass('invoice-contact-add');
    $('[name="contact[firstname2]"]').removeClass('invoice-contact');
    $('[name="contact[lastname2]"]').removeClass('invoice-contact-add');
    $('[name="contact[lastname2]"]').removeClass('invoice-contact');

    if( $('[name="purchase[contact_id]"]').val() == 0 ){
        $('[name="contact[company_name]"]').addClass('invoice-contact-add');
        $('[name="contact[firstname2]"]').addClass('invoice-contact-add');
        $('[name="contact[lastname2]"]').addClass('invoice-contact-add');
    }else{
        $('[name="contact[company_name]"]').addClass('invoice-contact');
        $('[name="contact[firstname2]"]').addClass('invoice-contact');
        $('[name="contact[lastname2]"]').addClass('invoice-contact');
    }
}

function purchase_check_contact_name(){
    if( $('[name="purchase[contact_id]"]').data('contact-name') != $('[name="contact[company_name]"]').val() ){
        $('[name="purchase[contact_id]"]').val(0);
        $('#purchase_update_exist_contact').css('display', 'none');
        $('#msg_add_contact').css('display', 'block'); 
        purchase_contact_update_icon();
    }else{
        $('[name="purchase[contact_id]"]').val($('[name="purchase[contact_id]"]').data('contact-id'));
        purchase_contact_update_icon();
    }

    //purchase_auto_save();
}

function purchase_init_sorting(){
	// sorting
	$("#sortableproducts").sortable({handle: ".sorthandle"});
}

var purchase_row_index = 0;

function purchase_add_row()
{
	purchase_row_index++;
	$.post(baseUrl + '/purchases/index/add-row', {index: purchase_row_index}, function(html){
		$('#sortableproducts').append(html)
		var row = $('#sortableproducts').children().last()
		row.find('select.dropdown').easySelectBox()
        var index = purchase_product_name_to_index(row.find('[name^="row"]')[0].name);
		purchase_init_product(index)
        $('[name="product[' + index + '][qty]"]').focus();
		purchase_auto_save()
	});
	purchase_init_sorting()
}
  
function purchase_remove_row(row){
	var container = $(row).parents('.product1').first()
	container.fadeOut('slow', function() { container.remove(); purchase_auto_save(); });
}

function purchase_init_product(index){
	purchase_init_product_qty_change(index)
	purchase_init_product_price_change(index)
	purchase_init_product_description_change(index)
	purchase_init_product_vat_change(index);
    purchase_init_product_discount_change(index);
    purchase_init_product_tag_change(index);
}

function purchase_update_product(id, index){
	if( id == 0 ){
        $('[name="product[' + index + '][description]"]').val('');
        $('[name="product[' + index + '][product_id]"]').val(0);
		return
	}
    disable_loader = true;
	$.post( baseUrl +  '/purchases/index/product-changed', {id:id, index: index}, function(data) {
		$('[name="product[' + data.index + '][description]"]').val(data.product.name);
        $('[name="product[' + data.index + '][product_id]"]').val(data.product.id);
        //var vat_included = $('[name="vat_included"]')[0].checked;
        var vat_included = $('[name="vat_included"]').val();
        var price = Number(data.product.cost_price);
        var vat = Number($('[name="product[' + data.index + '][vat]"]').val());

        if(vat_included == 1 ){
            price = price + (price * (vat/100));
        }

        $('[name="product[' + data.index + '][price]"]').val(price).blur();
		purchase_recalc_product(data.index)
		purchase_auto_save()
	});
}

function purchase_init_product_qty_change(index){
	var product = $('[name="product[' + index + '][qty]"]')
	product.change(purchase_auto_save);
    var qty = char_replace(product.val(), '.', '');
    qty = char_replace(qty, ',', '.');
    product.val(number_format(qty, 2, ',', '.'));
    product.on('blur', function(){
        var qty = char_replace($(this).val(), '.', '');
        qty = char_replace(qty, ',', '.');
        $(this).val(number_format(qty, 2, ',', '.'));
    });
	product.keyup(function(){
		purchase_recalc_product(purchase_product_name_to_index(this.name))
	})
	
	product.keydown(function(event){
		purchase_numeric(this, event)
	})
}

function purchase_init_product_price_change(index){
	var product = $('[name="product[' + index + '][price]"]')
    
    product.change(purchase_auto_save);
    product.keyup(function(){
        purchase_recalc_product(purchase_product_name_to_index(this.name))
    })
    
    product.blur(function(){
        var v = $(this).val();
        v = char_replace(char_replace(v, '.', ''), ',', '.');
        $(this).val(number_format(v, 2, ',', '.'));
    })
    
    product.keydown(function(event){
        purchase_numeric(this, event, true)
    })  
}

function purchase_init_product_description_change(index){
	var product = $('[name="product[' + index + '][description]"]')
    product.change(purchase_auto_save);

    product.parent().find('.wysiwyg').focus(function(){
        if( $(this).text().trim().length == 0 ){
            $(this).autocomplete('search', '');
        }
    });

    product.parent().find('.wysiwyg').blur(function(){
        purchase_auto_save();
    })

    product.parent().find('.wysiwyg').bind('hallomodified', function(){
        var index = purchase_product_name_to_index(this.id);
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
                var index = purchase_product_name_to_index(this.id);
                purchase_update_product(ui.item.data.id, index);
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

function purchase_init_product_tag_change(index){
    var product = $('#product_tag_' + index);
    product.change(purchase_auto_save);
    product.focus(function(){
//       $(this).val('');
//       $('[name="product[' + purchase_product_name_to_index(this.id) + '][tag_id]"]').val(0);
    });

    product.keydown(function(event){
        if( event.keyCode == 9 && $(this).parents('.product1').next().length == 0 ){
            purchase_add_row();
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
                $('[name="product[' + purchase_product_name_to_index(this.id) + '][tag_id]"]').val(ui.item.data.id);
                purchase_auto_save();
            }
        },

        change: function(event, ui){
        }
    });
}

function purchase_init_product_vat_change(index){
	$('[name="product[' + index + '][vat]"]')
        .combobox({input_class:'vat-drop-down'})
        .change(function(event, skipsave){
            purchase_recalc_product(purchase_product_name_to_index(this.name));

            if( skipsave == undefined ){
                purchase_auto_save();
            }
        });
}

function purchase_init_product_discount_change(index){
    var discount = $('[name="product[' + index + '][discount]"]');

    discount.keydown(function(event){
        purchase_numeric(this, event, false);
    }).keyup(function(){
        purchase_recalc_product(purchase_product_name_to_index(this.name));
    });

    discount.change(function(){
        purchase_auto_save();
    })
}

function purchase_recalc_product(index){
    //var vat_included = $('[name="vat_included"]')[0].checked;
    var vat_included = $('[name="vat_included"]').val();
	var priceTxt  = $('[name="product[' + index + '][price]"]').val();
    var price     = Number(char_replace(char_replace(priceTxt, '.', ''), ',', '.'));
    var vat = Number($('[name="product[' + index + '][vat]"]').val())

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
	$('#product_total_' + index).text(number_format(total, 2, ',', '.'))
	purchase_recalc_total()
}

function purchase_recalc_total(){
    //var vat_included = $('[name="vat_included"]')[0].checked;
    var vat_included = $('[name="vat_included"]').val();
    var purchase_discount = Number($('[name="purchase[discount]"]').val());
    var total_discount = 0.0;
	var total_exluding_vat = 0.0
    var total_including_vat = 0.0;
	var total_vat = 0.0
	
	totals = $('[id^="product_total_"]');
	for( var i = 0; i < totals.length; i++ ){
        var index           = purchase_product_name_to_index(totals[i].id);
        var priceTxt  = $('[name="product[' + index + '][price]"]').val();
        var price     = Number(char_replace(char_replace(priceTxt, '.', ''), ',', '.'));
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
        }        */
        total_exluding_vat += current_total * ((100-discount)/100);
	}

    total_discount += total_exluding_vat * (purchase_discount/100);

	$('#total_discount').text(number_format(total_discount, 2, ',', '.'))
	$('#total_excluding_vat').text(number_format(total_exluding_vat, 2, ',', '.'))
	$('#total_vat').text(number_format(total_vat, 2, ',', '.'))
	$('#total_including_vat').text(number_format((total_including_vat), 2, ',', '.'))
}

function purchase_auto_save(){
	if( !purchase_enable_auto_save || !purchase_auto_save_ready ){
		return;
	}

    disable_loader = true;
	return purchase_save();
}

function purchase_save(){
    purchase_auto_save_ready = false;
    if ( Number($('#contact_is_b2b').val()) != 1 ) {
        $('[name="contact[vat_number]"]').val('');
    }
	return $.post(baseUrl + "/purchases/index/save", $('#purchase-form').serialize())
	.success(function(data){
		if( data.redirect ){
			purchase_enable_auto_save = false;
			window.location = data.redirect;
			return;
		}
		
		if( data.id ){
            purchase_init_log(data.id);
			$('[name="purchase[id]"]').val(data.id);
            $('[name="purchase[contact_id]"]').val(data.contact_id);
            purchase_contact_update_icon();
		}
	}).complete(function(){
        purchase_auto_save_ready = true;
    });
}

function purchase_product_name_to_index(name){
	return name.match(/\d+/)[0];
}

function purchase_init_log(id){
    if( log_source_id == undefined ){
        log_source_type = 'purchase';
        log_source_id = id;
        log_init();
    }
}

function purchase_numeric(element, event, decimal){
    if( !(
    	   event.keyCode == 9
    	|| (decimal && element.value.indexOf('.') == -1 && (event.keyCode == 110 || event.keyCode == 190)) // . (dot)
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