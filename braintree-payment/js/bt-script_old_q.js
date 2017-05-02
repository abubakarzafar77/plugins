jQuery(document).ready(function($) {
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

    jQuery("#questionMark").click(function() {
        jQuery("#cvv-info-div").toggle();
    });
    $('input.first').on('change', function() {

        $('input.first').not(this).prop('checked', false);
        if($(this).prop('id') == "otherFirst")
            {
                $("#reason_txt").prop('disabled', false);
            }else{
                $("#reason_txt").val('');
                $("#reason_txt").prop('disabled', true);
            }
    });
    $('input.second').on('change', function() {

        $('input.second').not(this).prop('checked', false);
        if($(this).prop('id') == "otherSec")
            {
                
                $("#reason_txt_new").prop('disabled', false);
            }
            else{
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

        jQuery("#canel_subscription_form").submit();
    });
});