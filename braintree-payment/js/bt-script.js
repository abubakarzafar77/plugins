jQuery(function(){
    jQuery('#menu-top-menu-logged-in li a').on('click', function(e){
        var href = jQuery(this).attr('href');
        if(href.indexOf('logg-inn') >= 0){
            e.preventDefault();
            jQuery('.p-inner-sub-main').toggle();
        }
    });
});
jQuery(document).ready(function($) {
    // Configure/customize these variables.
    var showChar = 150;  // How many characters are shown by default
    var ellipsestext = "";
    var moretext = "..les mer";
    var lesstext = "les mindre";


    jQuery('.more').each(function() {
        var content = jQuery(this).html();

        if(content.length > showChar) {

            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);

            var html = c + '<span class="moreellipses">' + ellipsestext+ '</span><span class="morecontent"><span>' + h + '</span>&nbsp;<a href="" class="morelink">' + moretext + '</a></span>';

            jQuery(this).html(html);
        }

    });

    jQuery(".morelink").click(function(){
        if(jQuery(this).hasClass("less")) {
            jQuery(this).removeClass("less");
            jQuery(this).html(moretext);
        } else {
            jQuery(this).addClass("less");
            jQuery(this).html(lesstext);
        }
        jQuery(this).parent().prev().toggle();
        jQuery(this).prev().toggle();
        return false;
    });
    jQuery(document).on('click', '.less-more-btn a', function(){
        if(!jQuery(this).parent().hasClass('luk')) {
            jQuery(this).parents('.custm-right-box:first').find('.p-right-col-main').hide().find('');
            jQuery(this).parents('.custm-right-box:first').find('.p-right-col-main2').show();
        }else{
            jQuery(this).parents('.custm-right-box:first').find('.p-right-col-main').show();
            jQuery(this).parents('.custm-right-box:first').find('.p-right-col-main2').hide();
        }
    })
    jQuery(document).on('click', 'a.text-c.password', function(){
        jQuery('.login-form').hide();
        jQuery('.forgotpassword-form').show();
    });
    jQuery(document).on('click', 'a.text-c.back', function(){
        jQuery('.login-form').show();
        jQuery('.forgotpassword-form').hide();
    });
    jQuery("#read-more").click(function() {
        jQuery("#read-more-div").show();
        jQuery(this).hide();
        jQuery('#read-less').show();
    });
    jQuery("#read-less").click(function() {
        jQuery("#read-more-div").hide();
        jQuery(this).hide();
        jQuery('#read-more').show();
    });
    /*jQuery('#terms').change(function(){
        if(jQuery(this).is(':checked')){
            jQuery('.register_btn input[type="submit"]').removeClass('disabled');
        }else{
            jQuery('.register_btn input[type="submit"]').addClass('disabled')
        }
    });*/

    jQuery("#questionMark").click(function() {
        jQuery("#cvv-info-div").toggle();
    });
    $('input.first').on('change', function() {

        $('input.first').not(this).prop('checked', false);
        if ($(this).prop('id') == "otherFirst")
        {
            $("#reason_txt").prop('disabled', false);
        } else {
            $("#reason_txt").val('');
            $("#reason_txt").prop('disabled', true);
        }
    });
    $('input.second').on('change', function() {

        $('input.second').not(this).prop('checked', false);
        if ($(this).prop('id') == "otherSec")
        {

            $("#reason_txt_new").prop('disabled', false);
        }
        else {
            $("#reason_txt_new").val('');
            $("#reason_txt_new").prop('disabled', true);
        }
    });

//        $("#reason").change(function() {
//            if(jQuery("#reason").is(':checked')){
//                jQuery("#reason_other").prop('checked',false);
//                jQuery("#reason_txt").val('');
//                jQuery("#reason_txt").prop('disabled', true);
//            }
//            else{
//                if(!jQuery("#reason_other").is(':checked')){
//                    jQuery("#reason").prop('checked',true);
//                }
//            }
//        });
//        
//        $("#reason_other").change(function() {
//            if(jQuery("#reason_other").is(':checked')){
//                jQuery("#reason").prop('checked',false);
//                jQuery("#reason_txt").prop('disabled', false);
//            }
//            else{
//                if(!jQuery("#reason").is(':checked')){
//                    jQuery("#reason_other").prop('checked',true);
//                }
//            }
//        });

    jQuery("#canel_sub").click(function(e) {
        e.preventDefault();
        var error = 0;
        if ($("#student_status").val() === '' || $("#q2").val() === '')
        {
            alert("Please answer all required fields!");
        }
        else
        {

            if ($("#student_status").val() == 'Annet')
            {
                if ($("#reason_txt").val() == '')
                {
                    alert('Please provide reason in text field!');
                    error = 1;
                }
            }
            if ($("#q2").val() == 'Annet')
            {
                if ($("#reason_txt_new").val() == '')
                {
                    alert('Please provide reason2');
                    error = 1;
                }
            }
            if (!error)
            {
                jQuery("#canel_subscription_form").submit();
            }
        }
    });

//    Q

    $('#student_status').change(function() {
        var selected_item = $(this).val()
        if (selected_item == "Annet") {
            $('#reason_txt').removeClass('hidden');
        } else {
            $('#reason_txt').addClass('hidden');
        }
    });
    $('#q2').change(function() {
        var selected_item = $(this).val()
        if (selected_item == "Annet") {
            $('#reason_txt_new').removeClass('hidden');
        } else {
            $('#reason_txt_new').addClass('hidden');
        }
    });


});

function validateLogin(form, functioncall){
    var isValid = false;
    form.find("input").not('input[type="hidden"], input[type="submit"], input[type="button"]').each(function(){
        if(jQuery(this).attr('type') == 'submit'){
            return;
        }
        if(jQuery(this).val() == ""){
            isValid = false;
            jQuery(this).css('border', '1px solid rgb(185, 74, 72)');
        }else{
            isValid = true;
            jQuery(this).css('border', '0px');
        }
    });
    if(isValid === true){
        LogmeIn(form, functioncall);
    }
}
function LogmeIn(form, functioncall){
    //form.find('p.status').show().html('<img src="'+VeiviserAjax.homeurl+'/wp-content/plugins/braintree-payment/images/ajaxloader.gif" />');
    if(functioncall == 'login') {
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: VeiviserAjax.ajaxurl,
            data: {
                'action': 'ajaxlogin',
                'username': form.find('#m_brukernavn:first').val(),
                'password': form.find('#m_passord:first').val(),
                'security': form.find('#security:first').val()
            },
            success: function (data) {
                if (data.loggedin == true) {
                    document.location.href = VeiviserAjax.homeurl;
                }else{
                    form.find('p.status').text(data.message).show();
                }
            }
        });
    }else if(functioncall == 'forgot'){
        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: VeiviserAjax.ajaxurl,
            data: {
                'action': 'ajaxforgot',
                'email': form.find('#email:first').val(),
                'fpassword': form.find('#fpassword:first').val(),
            },
            success: function (data) {
                form.find('p.status').text(data.message).show();
            }
        });
    }
}
function gotoNext(that){
    var selected = jQuery.trim(that.parents('form:first').find('span.btn-select-value').text());
    var protocol = 'https://';
    var package = 0;
    that.parents('form:first').find('ul li').each(function(){
        if(jQuery.trim(jQuery(this).text()) == selected){
            package = jQuery(this).data('package');
        }
    });
    if((window.location.hostname).indexOf('dev.') >= 0){
        redirect = (window.location.href) + '?pkg=' + package;
    }else {
        redirect = (window.location.href).replace('http://', protocol) + '?pkg=' + package;
    }
    window.location.href = redirect;
}
function showForgot(){
    jQuery('.col-md-6.login').hide();
    jQuery('.col-md-6.forgotpassword').show();
}
function showLogin(){
    jQuery('.col-md-6.login').show();
    jQuery('.col-md-6.forgotpassword').hide();
}