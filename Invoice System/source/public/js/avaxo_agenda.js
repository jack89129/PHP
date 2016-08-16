$(document).ready(function(){
    log_source_type = 'agenda';
    log_source_id = $('[name="idx"]').val();
    log_init();
     //$('[name="calc_month"]').combobox();
     //$('[name="calc_year"]').combobox();  
     $('[name="reserve[party_type]"]').combobox();  
     $('[name="reserve[created_user]"]').combobox();
     $('[name="reserve[hours]"]').combobox();
     $('#btn_sendmail').click(function(){                                                                                             
        $('[name=agenda_email]').val($('[name="reserve[mail]"]').val());
        var subject = "Reservation";
        $('[name=agenda_subject]').val(subject);
        $('#div_subject').html(subject);
        var bodyText = "<p>Dear " + $('[name="contact[firstname]"]').val() + ",<br><br />";
        bodyText += "Thank you for your interest in our company, we made an optional reservation for you on " + $('[name="reserve[reserved_date]"]').val() + " at " + $('#start_hour').val() + " for a " + $('[name="reserve[party_type]"]').children("option").filter(":selected").text() + ".<br>";
        bodyText += "We expect u to be here at "+$('#start_hour').val() + "u" + $('#start_minute').val()+", with "+$('[name="reserve[adults]"]').val()+" adults & " +$('[name="reserve[children]"]').val()+" kids<br><br>";
        bodyText += "If something is not right, please contact us!<br><br>";
        bodyText += "Regards<br>Jos</p>";
        $('[name=agenda_body]').val(bodyText);
        $('#div_body').html(bodyText);
    });
    $('#sendemail-form').submit(function(event){
        $.post(baseUrl + "/agenda/index/email", $('#sendemail-form, #sendmailform').serialize()).success(function(){
            $.fancybox.close();
            log_reload();
        });
        return false;
    });
    $('#btn_sendmail').fancybox();
     $('#start_hour').click(function(){
         $('#start_time').val($('#start_hour').val()+':'+$('#start_minute').val());
     });
     $('#start_minute').click(function(){
         $('#start_time').val($('#start_hour').val()+':'+$('#start_minute').val());
     });    
     $('#end_hour').click(function(){
         $('#end_time').val($('#end_hour').val()+':'+$('#end_minute').val());
     });
     $('#end_minute').click(function(){
         $('#end_time').val($('#end_hour').val()+':'+$('#end_minute').val());
     }); 
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
                agenda_contact_select(ui.item.data);
            }
        },

        change: function(event, ui){
        }
    }).change(function(){
        agenda_check_contact_name();
    });
    
    $('.month_Box .name a.prev').on('click', function(){
        var y = Number($('#calc_year').val());
        var m = Number($('#calc_month').val());
        if ( m == 1 ) {
            y -= 1;
            m = 12;
        } else {
            m--;
        }
        $('#calc_year').val(y);
        $('#calc_month').val(m);
        $('#agenda_calc_date').submit();
    });
    $('.month_Box .name a.next').on('click', function(){
        var y = Number($('#calc_year').val());
        var m = Number($('#calc_month').val());
        if ( m == 12 ) {
            y += 1;
            m = 1;
        } else {
            m++;
        }
        $('#calc_year').val(y);
        $('#calc_month').val(m);
        $('#agenda_calc_date').submit();
    });
    
    $('#hapje_wrapper input.hapje').each(function(idx, obj){      
        $(obj).autocomplete({
            source: function( request, response ) {
                disable_loader = true;
                $.ajax({
                    url: baseUrl + "/agenda/index/hapje-autocomplete",
                    method: 'post',
                    data: {
                        limit: 10,
                        term: request.term
                    },
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                /*label: String(item.firstname + ' ' + item.lastname).trim(),*/
                                label: String(item.value).trim(),
                                value: item.value,
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
                    $(obj).parent().find('.hapje_id').val(ui.item.data.id);
                    $(obj).data('hapje-name',ui.item.data.value);
                    $(obj).val(ui.item.data.value);
                }
            },

            change: function(event, ui){
            }
        }).change(function(){
            if( $(obj).data('hapje-name') != $(obj).val() ){
                $(obj).parent().find('.hapje_id').val(0);
            }
        });
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
                agenda_contact_select(ui.item.data);
            }
        },

        change: function(event, ui){
        }
    }).change(function(){
        agenda_check_contact_name();
    });   
    $('[name="reserve[hapje_count]"]').on('change', function(){
        var cur = $('#hapje_wrapper p').size();
        var dsp = parseInt($(this).val());
        if ( dsp == cur ) return;
        if ( dsp > cur ) {
            for ( var i=cur+1; i<=dsp; i++ ) {
                $('#hapje_wrapper').append($('<p><input type="hidden" name="hapje_id['+i+']" class="hapje_id" value="0"/><label>&nbsp;</label>Hapje'+i+' : <input type="text" class="field hapje" data-hapje-name="" name="hapje['+i+']"></p>'));
            }
        } else {
            for ( var i=dsp; i<cur; i++ ) {
                $('#hapje_wrapper p').eq($('#hapje_wrapper p').size()-1).remove();
            }
        }
        $('#hapje_wrapper input.hapje').each(function(idx, obj){
            //$(obj).unbind();
            $(obj).autocomplete({
                source: function( request, response ) {
                    disable_loader = true;
                    $.ajax({
                        url: baseUrl + "/agenda/index/hapje-autocomplete",
                        method: 'post',
                        data: {
                            limit: 10,
                            term: request.term
                        },
                        success: function( data ) {
                            response( $.map( data, function( item ) {
                                return {
                                    /*label: String(item.firstname + ' ' + item.lastname).trim(),*/
                                    label: String(item.value).trim(),
                                    value: item.value,
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
                        $(obj).parent().find('.hapje_id').val(ui.item.data.id);
                        $(obj).data('hapje-name',ui.item.data.value);
                        $(obj).val(ui.item.data.value);
                    }
                },

                change: function(event, ui){
                }
            }).change(function(){
                if( $(obj).data('hapje-name') != $(obj).val() ){
                    $(obj).parent().find('.hapje_id').val(0);
                }
            });
        });
    });
    $('.txt_prod_name').each(function(idx, obj){
        $(obj).autocomplete({
            source: function( request, response ) {
                disable_loader = true;
                $.ajax({
                    url: baseUrl + "/agenda/index/menu-autocomplete",
                    method: 'post',
                    data: {
                        limit: 10,
                        term: request.term.trim()
                    },
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                label: String(item.name).trim(),
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
                    $(obj).val(ui.item.data.name);
                    $(obj).parent().find('.menu_id').val(ui.item.data.id);
                    $(obj).data("prod-name", ui.item.data.name);
                }
            },
            
            change: function(event, ui){
            }
        }).change(function(){
            if( $(obj).data('prod-name') != $(obj).val() ){
                $(obj).parent().find('.menu_id').val(0);  
            }
        });
    });
    $('.menu_opt').each(function(idx, obj){
        $(obj).on('click', function(){
            var idx = $(this).attr('idx');
            if ( $(this).attr('checked') ) {
                $('#menu'+idx+'_aan').removeAttr('disabled');
                $('#menu'+idx+'_buf').removeAttr('disabled');
                $('#menu_opt'+idx+'_wrapper').css('display', 'block');
                if ( $('#menu_opt'+idx+'_wrapper p').length == 0 ) {
                    var item = '<input type="hidden" id="max" value="1" /><p><input type="hidden" name="menu_pid['+idx+'][0]" value="0" class="menu_id"/><input type="text" class="txt_amount field" name="menu_amount['+idx+'][0]" value="1" data-prod-name=""/><input type="text" name="menu_pname['+idx+'][0]" class="txt_prod_name field"/><a href="javascript:;" style="margin-right: 5px;" onclick="agenda_add_menu('+idx+', this)"><img class="plus" alt="" src="/images/plus.jpg" style="display: inline;"></a><a href="javascript:;" onclick="agenda_delete_menu(this)"><img class="del" alt="" src="/images/img3.jpg" style="display: inline;"></a></p>';
                    $('#menu_opt'+idx+'_wrapper').html(item);
                    var menuObj = $('[name="menu_pname['+idx+'][0]"]');
                    $(menuObj).autocomplete({
                        source: function( request, response ) {
                            disable_loader = true;
                            $.ajax({
                                url: baseUrl + "/agenda/index/menu-autocomplete",
                                method: 'post',
                                data: {
                                    limit: 10,
                                    term: request.term.trim()
                                },
                                success: function( data ) {
                                    response( $.map( data, function( item ) {
                                        return {
                                            label: String(item.name).trim(),
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
                                $(menuObj).val(ui.item.data.name);
                                $(menuObj).parent().find('.menu_id').val(ui.item.data.id);
                                $(menuObj).data("prod-name", ui.item.data.name);
                            }
                        },
                        
                        change: function(event, ui){
                        }
                    }).change(function(){
                        if( $(menuObj).data('prod-name') != $(menuObj).val() ){
                            $(menuObj).parent().find('.menu_id').val(0);
                        }
                    });
                }
            } else {
                $('#menu'+idx+'_aan').attr('disabled', 'disabled');
                $('#menu'+idx+'_buf').attr('disabled', 'disabled');
                $('#menu_opt'+idx+'_wrapper').css('display', 'none');
            }
        });
    });
});

function onReserve() {
 if ( onValidation() ) {
     var frm = $('#detailForm');
     frm.attr('action', '/agenda/index/save');
     $('#status').val('1');
     frm.submit();
 }
}

function onOptional() {
 if ( onValidation() ) {
     var frm = $('#detailForm');
     frm.attr('action', '/agenda/index/save'); 
     $('#status').val('2');
     frm.submit();
 }
}

function onDelete() {
 if ( $('[name=idx]').val() != -1 ) {
     if ( window.confirm("Bent u zeker dat u de reservatie wilt verwijderen?") ) {
         var frm = $('#detailForm');
         frm.attr('action', '/agenda/index/save'); 
         $('#status').val(0);
         frm.submit();
     }
 }
}

function onDayView() {
 var frm = $('#detailForm');
 $('#agenda_print_wrapper').slideToggle();
}

function onPrint() {
 var frm = $('#detailForm');
 frm.attr('action', '/agenda/index/print');
 frm.submit();
}

function onSelectLocation(idx) {
    if ( idx == -1 ) return;
    var frm = $('#popupForm');
    $('#agenda_id').val(idx);
    frm.submit();
}

function onValidation() {
    if ( $('[name="reserve[name]"]').val() == "" ) {
        alert("Please input name!");
        $('[name="reserve[name]"]').focus();
        return false;
    }
    if ( $('[name="reserve[adults]"]').val() == "" ) {
        alert("Please input adults!");
        $('[name="reserve[adults]"]').focus();
        return false;
    }
    if ( $('[name="reserve[children]"]').val() == "" ) {
        alert("Please input children!");
        $('[name="reserve[children]"]').focus();
        return false;
    }
    if ( $('.location input:checked').length == 0 ) {
        alert("Please select locations");
        return false;
    }
    return true;
}

function agenda_contact_select(contact) {
    $('[name="reserve[contact_id]"]').val(contact.id);
    $('[name="reserve[contact_id]"]').data('contact-name', String(contact.company_name).trim());
    $('[name="reserve[contact_id]"]').data('contact-id', contact.id);
    $('[name="contact[company_name]"]').val(contact.company_name);
    $('[name="contact[firstname]"]').val(contact.firstname);
    $('[name="contact[lastname]"]').val(contact.lastname);    
    //invoice_contact_update_icon();   
}

function agenda_check_contact_name(){
    if( $('[name="reserve[contact_id]"]').data('contact-name') != $('[name="contact[company_name]"]').val() ){
        $('[name="reserve[contact_id]"]').val(0);      
        //invoice_contact_update_icon();
    }else{
        $('[name="reserve[contact_id]"]').val($('[name="reserve[contact_id]"]').data('contact-id'));
        //invoice_contact_update_icon();
    }                     
}

function agenda_add_menu(idx, obj) {
    var max = parseInt($(obj).parent().parent().find('#max').val());
    $(obj).parent().parent().find('#max').val(max+1);
    
    var item = $('<p><input type="hidden" name="menu_pid['+idx+']['+max+']" value="0" class="menu_id"/><input type="text" class="txt_amount field" name="menu_amount['+idx+']['+max+']" value="1"/><input type="text" name="menu_pname['+idx+']['+max+']" class="txt_prod_name field" data-prod-name="" /><a href="javascript:;" style="margin-right: 5px;" onclick="agenda_add_menu('+idx+', this)"><img class="plus" alt="" src="/images/plus.jpg" style="display: inline;"></a><a href="javascript:;" onclick="agenda_delete_menu(this)"><img class="del" alt="" src="/images/img3.jpg" style="display: inline;"></a></p>');
    $(obj).parent().parent().append(item);
    var menuObj = $('[name="menu_pname['+idx+']['+max+']"]');
    $(menuObj).autocomplete({
        source: function( request, response ) {
            disable_loader = true;
            $.ajax({
                url: baseUrl + "/agenda/index/menu-autocomplete",
                method: 'post',
                data: {
                    limit: 10,
                    term: request.term.trim()
                },
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return {
                            label: String(item.name).trim(),
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
                $(menuObj).val(ui.item.data.name);
                $(menuObj).parent().find('.menu_id').val(ui.item.data.id);
                $(menuObj).data("prod-name", ui.item.data.name);
            }
        },
        
        change: function(event, ui){
        }
    }).change(function(){
        if( $(menuObj).data('prod-name') != $(menuObj).val() ){
            $(menuObj).parent().find('.menu_id').val(0);
        }
    });
}

function agenda_delete_menu(obj) {
    var count = $(obj).parent().parent().find('p').length;
    if ( count == 1 ) {
        alert('It needs to add a product at least!');
        return;
    }
    $(obj).parent().remove();
}
