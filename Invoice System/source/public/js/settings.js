$(document).ready(function(){
    $('[name="setting_contact[company]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_contact[company]"]').val(value);
    }).blur(setting_contact_auto_save);
    
    $('[name="setting_contact[phone]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_contact[phone]"]').val(value);
    }).blur(setting_contact_auto_save);
    
    $('[name="setting_contact[email]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_contact[email]"]').val(value);
    }).blur(setting_contact_auto_save);
    
    $('[name="setting_contact[website]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_contact[website]"]').val(value);
    }).blur(setting_contact_website_blur);
    
    $('[name="setting_contact[banknum]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_contact[banknum]"]').val(value);
    }).blur(setting_contact_auto_save);
    
    $('[name="setting_contact[kvk]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_contact[kvk]"]').val(value);
    }).blur(setting_contact_auto_save);
    
    $('[name="setting_contact[bic]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_contact[bic]"]').val(value);
    }).blur(setting_contact_auto_save);
    
    $('[name="setting_contact[btw]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_contact[btw]"]').val(value);
    }).blur(setting_contact_auto_save);
    
    $('[name="setting_contact[land]"]').combobox();
    
    $('[name="setting_contact[land]"]').on('change', function(){
        if ( $(this).val() == 'Nederland' ) {
            $('#kvk_wrapper').css('display', 'block');
        } else {
            $('#kvk_wrapper').css('display', 'none');
        }
    });
    
    $('#div_subject').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}, 
        },
        editable: true
    }).bind('hallomodified', function(){
        $('[name="subject"]').val($(this).html().trim());
    }).blur(null);                    
    
    /*$('#div_body').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        $('[name="body"]').val($(this).html().trim());
    }).blur(null);
    
    $('#div_body').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        $('[name="body"]').val($(this).html().trim());
    }).blur(null);*/
    
    $('#div_subject').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        $('[name="subject"]').val($(this).html().trim());
    }).blur(null);
    
    if ( CKEDITOR.instances.settings_invoice_mailbody ) {
        CKEDITOR.instances.settings_invoice_mailbody.on('blur', function() {
            $('[name=body]').val(CKEDITOR.instances.settings_invoice_mailbody.getData());
        });
    }
    
    if ( $('#settings_invoice_mailbody').length > 0 ) {
        CKEDITOR.replace( 'settings_invoice_mailbody', 
        {
            toolbar :
            [
                ['Bold','Italic','Strike','Underline']
            ],                              
            enterMode : CKEDITOR.ENTER_BR
        } );
    }
    
    $("#settingForm .open_mail_template").each(function(idx, obj){
        $(obj).click(function(){
            $.post(baseUrl + "/settings/invoice/fill-popup", {type: $(obj).attr('ptype')}).success(function(data){
                $('.popup_title').html(data.title);
                if ( data.type != 'notice' ) {
                    $('#div_type').val(data.type);    
                    $('[name=subject]').val(data.subject);
                    $('#div_subject').html(data.subject);
                    $('#settings_invoice_mailbody').val(data.body);          
                    CKEDITOR.instances.settings_invoice_mailbody.setData(data.body);
                    $('[name=body]').val(data.body);
                    //$('#div_body').html(data.body);  
                } else {                               
                    $('#notice_type').val(data.type);    
                    $('[name=intro]').val(data.intro);
                    $('#notice_intro').html(data.intro);
                    $('[name=footer]').val(data.footer);
                    $('#notice_footer').html(data.footer);
                }
            });
        });
        
        $(obj).fancybox();
    });
    
    $("#settingForm .offer_mail_template").each(function(idx, obj){
        $(obj).click(function(){
            $.post(baseUrl + "/settings/offer/fill-popup", {type: $(obj).attr('ptype')}).success(function(data){
                $('.popup_title').html(data.title);
                if ( data.type != 'notice' ) {
                    $('#div_type').val(data.type);    
                    $('[name=subject]').val(data.subject);
                    $('#div_subject').html(data.subject);
                    $('[name=body]').val(data.body);
                    $('#div_body').html(data.body);
                    CKEDITOR.instances.settings_invoice_mailbody.setData(data.body);
                    $('[name=body]').val(data.body);
                } else {                               
                    $('#notice_type').val(data.type);    
                    $('[name=intro]').val(data.intro);
                    $('#notice_intro').html(data.intro);
                    $('[name=footer]').val(data.footer);
                    $('#notice_footer').html(data.footer);
                }
            });
        });
        
        $(obj).fancybox();
    });
    
    $('#mailsettings-form').submit(function(event){
        $('[name=body]').val(CKEDITOR.instances.settings_invoice_mailbody.getData());
        $.post(baseUrl + "/settings/"+$(this).attr('type')+"/save-template", $('#mailsettings-form').serialize()).success(function(){
            $.fancybox.close();
            //log_reload();
        });        
        return false;
    });
    
    $('#noticesettings-form img').each(function(idx, obj) {
        $(obj).hover(
            function(){
                $(this).css("width", "50%");
                $(this).css("position", "absolute");
            }, 
            function(){
                $(this).css("width", "20%");
                $(this).css("position", "static");
            }
        );
    });
        
    $('#noticesettings-form').submit(function(event){
        $.post(baseUrl + "/settings/"+$(this).attr('type')+"/save-template", $('#noticesettings-form').serialize()).success(function(){
            $.fancybox.close();
            //log_reload();
        });
        return false;
    });
            
    $('[name="setting_invoice[subject]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_invoice[subject]"]').val(value);
    }).blur(setting_invoice_auto_save);
    
    $('[name="setting_invoice[late_subject]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_invoice[late_subject]"]').val(value);
    }).blur(setting_invoice_auto_save);
    
    $('[name="setting_contact[bankname]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_contact[bankname]"]').val(value);
    }).blur(setting_contact_auto_save);
    
    $('[name="setting_contact[banklocation]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_contact[banklocation]"]').val(value);
    }).blur(setting_contact_auto_save);
    
    $('[name="setting_invoice[urgent_subject]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_invoice[urgent_subject]"]').val(value);
    }).blur(setting_invoice_auto_save);
    
    $('[name="setting_contact[mail_from_name]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        $('[name="setting_contact[mail_from_name]"]').val(value);
    }).blur(setting_contact_auto_save);
    
    $('[name="setting_contact[mail_from_addr]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        $('[name="setting_contact[mail_from_addr]"]').val(value);
    }).blur(setting_contact_auto_save); 
    
    $('[name="setting_invoice[judge_subject]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_invoice[judge_subject]"]').val(value);
    }).blur(setting_invoice_auto_save);
    
    $('[name="setting_offer[subject]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_offer[subject]"]').val(value);
    }).blur(setting_offer_auto_save);
    
    $('[name="setting_offer[late_subject]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_offer[late_subject]"]').val(value);
    }).blur(setting_offer_auto_save);
    
    $('[name="setting_offer[urgent_subject]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_offer[urgent_subject]"]').val(value);
    }).blur(setting_offer_auto_save);
    
    $('[name="setting_offer[judge_subject]"]').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        var value = $(this).html().trim();
        value = value.replace('<br>','');
        value = value.replace('&nbsp;','');
        $('[name="setting_offer[judge_subject]"]').val(value);
    }).blur(setting_offer_auto_save);
    
    $('#save_contact').on('click', function() {
        //$.post(baseUrl + "/settings/contact/save", $('#settingForm').serialize());
        if ( $('#setting_contact_logo').val() == '' && $('#logo_img_path').val() == '' ) {
            alert("Selecteer logo bestand!");
            return;
        } else {
            if ( $('#setting_contact_logo').val() != '' && $('#logo_img_path').val() != '') {
                if ( window.confirm("Wenst u het huidige bedrijfslogo te vervangen?") ) {
                    var frmObj = document.getElementById('settingForm');
                    frmObj.action = baseUrl + "/settings/contact/upload";
                    frmObj.submit();
                }
            } else {
                var frmObj = document.getElementById('settingForm');
                frmObj.action = baseUrl + "/settings/contact/upload";
                frmObj.submit();
            }
        }
    });
    
    $('#save_invoice_settings').on('click', function() {
        $.post(baseUrl + "/settings/invoice/save", $('#mySettingForm').serialize());
    });
    
    $('#save_format').on('click', function() {
        var inum = $('[name="setting_format[invoice]"]');
        var onum = $('[name="setting_format[offer]"]');
        var pnum = $('[name="setting_format[purchase]"]');
        if ( inum.val().indexOf('[Jaar]') == -1 ) {
            alert('Je moet ook [Jaar]!');
            inum.focus();
            return;
        }
        if ( onum.val().indexOf('[Jaar]') == -1 ) {
            alert('Je moet ook [Jaar]!');
            onum.focus();
            return;
        }
        if ( pnum.val().indexOf('[Jaar]') == -1 ) {
            alert('Je moet ook [Jaar]!');
            pnum.focus();
            return;
        }
        $.post(baseUrl + "/settings/format/save", $('#settingForm').serialize());
    });
    
    $('#save_offer_settings').on('click', function() {
        $.post(baseUrl + "/settings/offer/save", $('#settingForm').serialize());
    });
    
    if ( CKEDITOR.instances.setting_invoice_intro ) {
        CKEDITOR.instances.setting_invoice_intro.on('blur', function() {
            $('#setting_invoice_intro').val(CKEDITOR.instances.setting_invoice_intro.getData());
            setting_invoice_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_offer_intro ) {
        CKEDITOR.instances.setting_offer_intro.on('blur', function() {
            $('#setting_offer_intro').val(CKEDITOR.instances.setting_offer_intro.getData());
            setting_offer_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_invoice_message ) {
        CKEDITOR.instances.setting_invoice_message.on('blur', function() {
            $('#setting_invoice_message').val(CKEDITOR.instances.setting_invoice_message.getData());
            setting_invoice_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_invoice_late_message ) {
        CKEDITOR.instances.setting_invoice_late_message.on('blur', function() {
            $('#setting_invoice_late_message').val(CKEDITOR.instances.setting_invoice_late_message.getData());
            setting_invoice_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_invoice_urgent_message ) {
        CKEDITOR.instances.setting_invoice_urgent_message.on('blur', function() {
            $('#setting_invoice_urgent_message').val(CKEDITOR.instances.setting_invoice_urgent_message.getData());
            setting_invoice_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_invoice_judge_message ) {
        CKEDITOR.instances.setting_invoice_judge_message.on('blur', function() {
            $('#setting_invoice_judge_message').val(CKEDITOR.instances.setting_invoice_judge_message.getData());
            setting_invoice_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_invoice_footer ) {
        CKEDITOR.instances.setting_invoice_footer.on('blur', function() {
            $('#setting_invoice_footer').val(CKEDITOR.instances.setting_invoice_footer.getData());
            setting_invoice_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_offer_message ) {
        CKEDITOR.instances.setting_offer_message.on('blur', function() {
            $('#setting_offer_message').val(CKEDITOR.instances.setting_offer_message.getData());
            setting_offer_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_offer_late_message ) {
        CKEDITOR.instances.setting_offer_late_message.on('blur', function() {
            $('#setting_offer_late_message').val(CKEDITOR.instances.setting_offer_late_message.getData());
            setting_offer_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_offer_urgent_message ) {
        CKEDITOR.instances.setting_offer_urgent_message.on('blur', function() {
            $('#setting_offer_urgent_urgent_message').val(CKEDITOR.instances.setting_offer_urgent_message.getData());
            setting_offer_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_offer_judge_message ) {
        CKEDITOR.instances.setting_offer_judge_message.on('blur', function() {
            $('#setting_offer_judge_message').val(CKEDITOR.instances.setting_offer_judge_message.getData());
            setting_offer_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_offer_footer ) {
        CKEDITOR.instances.setting_offer_footer.on('blur', function() {
            $('#setting_offer_footer').val(CKEDITOR.instances.setting_offer_footer.getData());
            setting_offer_auto_save();
        });
    }
    
    if ( CKEDITOR.instances.setting_about_us ) {
        CKEDITOR.instances.setting_about_us.on('blur', function() {
            $('#setting_about_us').val(CKEDITOR.instances.setting_about_us.getData());
            setting_shop_auto_save();
        });
    }
    
    $('#addr_street').on('blur', setting_contact_auto_save);
    $('#addr_num').on('blur', setting_contact_auto_save);
    $('#addr_postal').on('blur', setting_contact_auto_save);
    $('#addr_city').on('blur', setting_contact_auto_save);
    
    $('[name="setting_shop[tlink]"]').on('blur', setting_shop_auto_save);
    $('[name="setting_shop[flink]"]').on('blur', setting_shop_auto_save);
    $('[name="setting_shop[glink]"]').on('blur', setting_shop_auto_save);
    $('[name="setting_shop[vlink]"]').on('blur', setting_shop_auto_save);
    $('[name="setting_shop[llink]"]').on('blur', setting_shop_auto_save);
    $('[name="setting_shop[title]"]').on('blur', setting_shop_auto_save);
    
    $('#webshop_activation').on('click', setting_webshop_activation);
    
    $('#tableColorSelector').ColorPicker({
        color: '#0000ff',
        onShow: function (colpkr) {
            $(colpkr).fadeIn(500);
            return false;
        },
        onHide: function (colpkr) {
            $(colpkr).fadeOut(500);
            setting_contact_auto_save();
            return false;
        },
        onChange: function (hsb, hex, rgb) {
            $('#tableColorSelector div').css('backgroundColor', '#' + hex);
            $('#contact_table_color').val('#' + hex);
        }
    });
    
    $('#textColorSelector').ColorPicker({
        color: '#0000ff',
        onShow: function (colpkr) {
            $(colpkr).fadeIn(500);
            return false;
        },
        onHide: function (colpkr) {
            $(colpkr).fadeOut(500);
            setting_contact_auto_save();
            return false;
        },
        onChange: function (hsb, hex, rgb) {
            $('#textColorSelector div').css('backgroundColor', '#' + hex);
            $('#contact_text_color').val('#' + hex);
        }
    });
    
    $('#shopColorSelector').ColorPicker({
        color: '#e61e25',
        onShow: function (colpkr) {
            $(colpkr).fadeIn(500);
            return false;
        },
        onHide: function (colpkr) {
            $(colpkr).fadeOut(500);
            setting_shop_auto_save();
            return false;
        },
        onChange: function (hsb, hex, rgb) {
            $('#shopColorSelector div').css('backgroundColor', '#' + hex);
            $('#webshop_main_color').val('#' + hex);
        }
    });
    
    $('#mailsettings-form a.format_tag').each(function(idx, obj){
        $(obj).on('click', function(){
            /*var text = $('#div_body').html();             
            $('#div_body').html(text + '{' + $(this).attr('code') + '}');
            $('[name=body]').val($('#div_body').html());*/
            var text = CKEDITOR.instances.settings_invoice_mailbody.getData();
            text += '{' + $(this).attr('code') + '}';
            CKEDITOR.instances.settings_invoice_mailbody.setData(text);
            CKEDITOR.instances.settings_invoice_mailbody.focus();
        });
    });
    
    var curObj = $('#notice_intro');
    $('#notice_intro').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        $('[name="intro"]').val($(this).html().trim());
    }).blur(function(){
        curObj = $(this);
    });
    
    $('#notice_footer').parent().find('.wysiwyg').hallo({
        plugins: {
            'halloformat': {},
            'hallolink': {}
        },
        editable: true
    }).bind('hallomodified', function(){
        $('[name="footer"]').val($(this).html().trim());
    }).blur(function(){
        curObj = $(this);
    });
    $('#noticesettings-form a.format_tag').each(function(idx, obj){
        $(obj).on('click', function(){
            var text = $(curObj).html();             
            $(curObj).html(text + '{' + $(this).attr('code') + '}');
            $(curObj).parent().find('input').val($(curObj).html());
        });
    });
    
    if ( $('#upload') ) {
        $('#upload').on('click', onLogoUpload);
    }
    
    if ( $('#shop_logo_upload') ) {
        $('#shop_logo_upload').on('click', onWebshopLogoUpload);
    }
    
    if ( $('#about_logo_upload') ) {
        $('#about_logo_upload').on('click', onAboutLogoUpload);
    }
    
    if ( $('#slider_image_upload1') ) {
        $('#slider_image_upload1').on('click', onSlideImageUpload);
    }
    
    if ( $('#slider_image_upload2') ) {
        $('#slider_image_upload2').on('click', onSlideImageUpload);
    }
    
    if ( $('#slider_image_upload3') ) {
        $('#slider_image_upload3').on('click', onSlideImageUpload);
    }
    
    if ( $('#setting_condition_upload') ) {
        $('#setting_condition_upload').on('click', onConditionFileUpload);
    }
    
    $('#b2b_interest_yes').on('click', function(){
        $('[name="setting_invoice[b2b_has_interest]"]').val(1);
    });
    $('#b2b_interest_no').on('click', function(){
        $('[name="setting_invoice[b2b_has_interest]"]').val(0);
    });
    $('#b2b_term_day').on('click', function(){
        $('[name="setting_invoice[b2b_interest_term]"]').val('day');
    });
    $('#b2b_term_week').on('click', function(){
        $('[name="setting_invoice[b2b_interest_term]"]').val('week');
    });
    $('#b2b_term_off').on('click', function(){
        $('[name="setting_invoice[b2b_interest_term]"]').val('off');
    });
    $('#b2b_term_month').on('click', function(){
        $('[name="setting_invoice[b2b_interest_term]"]').val('month');
    });
    $('#b2b_autosend_yes').on('click', function(){
        $('[name="setting_invoice[b2b_auto_sendmail]"]').val(1);
    });
    $('#b2b_autosend_no').on('click', function(){
        $('[name="setting_invoice[b2b_auto_sendmail]"]').val(0);
    });
    $('#b2c_interest_yes').on('click', function(){
        $('[name="setting_invoice[b2c_has_interest]"]').val(1);
    });
    $('#b2c_interest_no').on('click', function(){
        $('[name="setting_invoice[b2c_has_interest]"]').val(0);
    });
    $('#b2c_term_day').on('click', function(){
        $('[name="setting_invoice[b2c_interest_term]"]').val('day');
    });
    $('#b2c_term_week').on('click', function(){
        $('[name="setting_invoice[b2c_interest_term]"]').val('week');
    });
    $('#b2c_term_month').on('click', function(){
        $('[name="setting_invoice[b2c_interest_term]"]').val('month');
    });
    $('#b2c_term_off').on('click', function(){
        $('[name="setting_invoice[b2c_interest_term]"]').val('off');
    });
    $('#b2c_autosend_yes').on('click', function(){
        $('[name="setting_invoice[b2c_auto_sendmail]"]').val(1);
    });
    $('#b2c_autosend_no').on('click', function(){
        $('[name="setting_invoice[b2c_auto_sendmail]"]').val(0);
    });
    
});

function onConditionFileUpload() {
    if ( $('#setting_condition_pdf').val() == '' ) {
        alert("Gelieve een algemene voorwaarden pdf te uploaden!");
        return;
    }
    var frmObj = document.getElementById('settingForm');
    frmObj.action = baseUrl + "/settings/shop/uploadcondition";
    frmObj.submit();
}

function onSlideImageUpload() {
    var idx = $(this).attr('idx');
    if ( $('#setting_slider_image'+idx).val() == '' ) {
        alert("Upload een afbeelding!");
        return;
    }
    
    var frmObj = document.getElementById('settingForm');
    frmObj.action = baseUrl + "/settings/shop/uploadslider?idx=" + idx;
    frmObj.submit();
}

function onWebshopLogoUpload() {
    if ( $('#setting_webshop_logo').val() == '' ) {
        alert("Upload een logo!");
        return;
    }
    if ( window.confirm("Wilt u het logo veranderen?") ) {
        var frmObj = document.getElementById('settingForm');
        frmObj.action = baseUrl + "/settings/shop/upload";
        frmObj.submit();
    }
}

function onAboutLogoUpload() {
    if ( $('#setting_about_logo').val() == '' ) {
        alert('Upload een "over ons" afbeelding!');
        return;
    }
    if ( window.confirm("Wilt u de huidige over-logo vervangen?") ) {
        var frmObj = document.getElementById('settingForm');
        frmObj.action = baseUrl + "/settings/shop/uploadabout";
        frmObj.submit();
    }
}

function onLogoUpload() {
    if ( $('#setting_contact_logo').val() == '' ) {
        alert("Selecteer logo bestand!");
        return;
    }
    if ( $('#logo_img_path').val() != '') {
        if ( window.confirm("Wenst u het huidige bedrijfslogo te vervangen?") ) {
            var frmObj = document.getElementById('settingForm');
            frmObj.action = baseUrl + "/settings/contact/upload";
            frmObj.submit();
        }
    } else {
        var frmObj = document.getElementById('settingForm');
        frmObj.action = baseUrl + "/settings/contact/upload";
        frmObj.submit();
    }
}

function setting_invoice_auto_save() {
    //$.post(baseUrl + "/settings/invoice/save", $('#settingForm').serialize());
}

function setting_contact_website_blur() {
    var website = $('[name="setting_contact[website]"]').val();
    if (!/^http:\/\//.test(website)) {
        website = "http://" + website;
    }
    $('[name="setting_contact[website]"]').val(website);
    $('[name="setting_contact[website]"]').parent().find('div').html(website);
    setting_contact_auto_save();
}

function setting_offer_auto_save() {
    //$.post(baseUrl + "/settings/offer/save", $('#settingForm').serialize());
}

function setting_contact_auto_save() {
    //$.post(baseUrl + "/settings/contact/save", $('#settingForm').serialize());
}

function setting_shop_auto_save() {
    $.post(baseUrl + "/settings/shop/save", $('#settingForm').serialize());
}

function setting_webshop_activation() {
    var status = $('#webshop_activation').attr('checked');
    
    if ( status ) {
        $('#setting_webshop_activation').val('on');
    } else {
        $('#setting_webshop_activation').val('off');
    }
    
    setting_shop_auto_save();
}
