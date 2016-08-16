function setupLabel(parent) {
    if( parent == undefined ){
        parent = '';
    }

    if ($(parent + ' .label_check input, ' + parent + ' .label_check2 input, ' + parent + '.label_check3 input').length) {
        $(parent + ' .label_check, ' + parent + ' .label_check2, ' + parent + ' .label_check3').each(function(){
            $(this).removeClass('c_on');
        });

        $(parent + ' .label_check input:checked, ' + parent +  ' .label_check2 input:checked, ' + parent + ' .label_check3 input:checked').each(function(){
            $(this).parent('label').addClass('c_on');
        });                
    };
    if ($('.label_radio input').length) {
        $('.label_radio').each(function(){ 
            $(this).removeClass('r_on');
        });
        $('.label_radio input:checked').each(function(){ 
            $(this).parent('label').addClass('r_on');
        });
    };
};

function setupButtonLoader(){
	$('.button-loader').each(function(index, item){
		$(item).ajaxStart(function(){
			$(this).attr('disabled', 'disabled').css('opacity', 0.5);
		});
		
		$(item).ajaxComplete(function(){
			$(this).removeAttr('disabled').css('opacity', 1);
		});
	});
}

$(document).ready(function(){
	$('body').addClass('has-js');
    $('.label_check, .label_radio,.label_check2,.label_check3').click(function(){
        setupLabel();
    });
    setupLabel();
    setupButtonLoader();
});