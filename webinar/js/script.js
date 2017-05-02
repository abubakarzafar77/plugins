var is_category_selected = 0;
var count = 0;
var totalTime = 0;
var calcTime = 0;
jQuery(document).ready(function(){
    jQuery(".check_class").click(function() {
        if(!jQuery(this).is(':checked')){
            is_category_selected = 0;
            jQuery(this).attr("checked", false);
            //jQuery('.category_row').fadeIn();
            jQuery('.w-profile-box').removeClass('border');
            var option_items = new Array();
            option_items.push('<option value="">Studiekamerat</option>');
            jQuery('#teacher').html('');
            jQuery('#teacher').append(option_items.join(''))
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: WebinarAjaxurl,
                data: {
                    'action': 'loadteachers',
                    'education_level': 'all'
                },
                success: function (data) {
                    if(data.teachers){
                        if(data.html){
                            jQuery('.w-education-row').html(data.html);
                        }else{
                            jQuery('.w-education-row').html('');
                        }
                    }
                }
            });
            jQuery('.category_row h2').text('Vre studiekamerater');
        }else {
            jQuery(".check_class").attr("checked", false);
            jQuery(this).attr("checked", true);
            is_category_selected = 1;
            jQuery('.category_row h2').text(jQuery(this).data('name'));
            $category = $(this).val();
            //jQuery('.category_row').fadeOut();
            //jQuery('.category_' + $category).fadeIn();
            jQuery('.w-profile-box').removeClass('border');
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: WebinarAjaxurl,
                data: {
                    'action': 'loadteachers',
                    'education_level': $category
                },
                success: function (data) {
                    if(data.teachers){
                        var option_items = new Array();
                        option_items.push('<option value="">Studiekamerat</option>');
                        if((data.teachers).length > 0) {
                            option_items.push('<option value="all">All teachers</option>');
                            jQuery.each(data.teachers, function (i, item) {
                                option_items.push('<option value="' + item.ID + '">' + item.first_name + ' ' + item.last_name + '</option>');
                            });
                        }
                        if(data.html){
                            jQuery('.w-education-row').html(data.html);
                        }else{
                            jQuery('.w-education-row').html('');
                        }
                        jQuery('#teacher').html('');
                        jQuery('#teacher').append(option_items.join(''));
                    }else{
                        var option_items = new Array();
                        option_items.push('<option value="">Studiekamerat</option>');
                        jQuery('#teacher').html('');
                        jQuery('#teacher').append(option_items.join(''));
                    }
                }
            });
        }
    });
    jQuery('.w-education-levels').on('click', '.w-profile-box', function(){
        if(is_category_selected){
            jQuery('.w-profile-box').removeClass('border');
            $teacher_id = jQuery(this).data('teacher-id');
            jQuery(this).addClass('border');
            jQuery('#teacher option[value='+$teacher_id+']').attr('selected','selected');
        }
    });
    jQuery('#education_level').on('change', function () {
        var education_level = jQuery(this).val();
        if(education_level != '') {
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: WebinarAjaxurl,
                data: {
                    'action': 'loadteachers',
                    'education_level': jQuery(this).val()
                },
                success: function (data) {
                    if(data.teachers){
                        var option_items = new Array();
                        option_items.push('<option value="">Teacher</option>');
                        if((data.teachers).length > 0) {
                            option_items.push('<option value="all">All teachers</option>');
                            jQuery.each(data.teachers, function (i, item) {
                                option_items.push('<option value="' + item.ID + '">' + item.first_name + ' ' + item.last_name + '</option>');
                            });
                        }
                        jQuery('#teacher').html('');
                        jQuery('#teacher').append(option_items.join(''));
                    }else{
                        var option_items = new Array();
                        option_items.push('<option value="">Teacher</option>');
                        jQuery('#teacher').html('');
                        jQuery('#teacher').append(option_items.join(''));
                    }
                }
            });
        }else{
            var option_items = new Array();
            option_items.push('<option value="">Teacher</option>');
            jQuery('#teacher').html('');
            jQuery('#teacher').append(option_items.join(''));
        }
    });
    
    jQuery('#duration').on('change', function() {
        var budget = jQuery('#duration :selected').attr('data-budget');
        if(budget == '299-kr' || budget == '590-kr'){
            jQuery('.w-datepicker').show();
            jQuery("#date_time").addClass("validate[required]");
        } else {
            jQuery('.w-datepicker').hide();
            jQuery("#date_time").removeClass("validate[required]");
        }
        jQuery('#budget').val(budget);
    });
    
//    jQuery('#date_time').datetimepicker({
//        sideBySide: true
//    });
    
    /*jQuery('#date_time').datetimepicker({
        format: "yyyy-mm-dd hh:ii",
        autoclose: true,
        pickerPosition: "bottom-left",
        minuteStep: 30,
        hoursDisabled: '0,1,2,3,4,5'
    });*/
    
//    jQuery("#webinar_files").change(function() {
//        var filename = jQuery('#webinar_files').val().replace(/C:\\fakepath\\/i, '');
//        console.log(filename);
//        jQuery('#file_upload_section').html('<div id="file_name_text"> '+ filename +' <a id="enchor_delete" href="javascript:void(0)">Delete</a></div>');

//    });
    
    jQuery("#webinar_files").change(function(){
        for (var i = 0; i < jQuery(this).get(0).files.length; ++i) {
            jQuery('#file_upload_section').append('<div id="file_name_text_'+i+'" date-objec="'+jQuery(this).get(0).files[i].name+'"> '+ jQuery(this).get(0).files[i].name +' <a class="deletePic" id="'+i+'" href="javascript:void(0)"> X </a><br /></div>');
        }
    });    
    
    
    
    
    jQuery(document).on("click", ".deletePic", function(e) {
        var imgId = jQuery(this).attr('id');
        var imgName = jQuery("#file_name_text_" + imgId).attr('date-objec');
        var deleteFiles=jQuery("#deleteFile").val();
        jQuery("#deleteFile").val(deleteFiles + '<=>' + imgName);
        jQuery("#file_name_text_" + imgId).remove();
    });
    
    
    var divs = jQuery('.my_jobs>div');
    var now = 0; // currently shown div
    //divs.hide().first().show(); // hide all divs except first
    jQuery(".next_job").click(function() {
        divs.eq(now).hide();
        now = (now + 1 < divs.length) ? now + 1 : 0;
        divs.eq(now).show(); // show next
        pagenum = now + 1;
        $('.shown').text(pagenum);
    });
    
    jQuery(".previous_job").click(function() {
        divs.eq(now).hide();
        now = (now > 0) ? now - 1 : divs.length - 1;
        divs.eq(now).show(); // show previous
        pagenum = now + 1;
        $('.shown').text(pagenum);
    });
    
    jQuery("._next_job").click(function() {
        var activeNum = jQuery(".activePage").text();
        console.log(jQuery( "span:first-child" ).prev());
        jQuery( "span" ).next().addClass(".activePage").first();
    });
    
    jQuery("._previous_job").click(function() {
        var activeNum = jQuery(".activePage").text();
        console.log(jQuery( "span:first-child" ).prev());
        jQuery( "span" ).prev().addClass(".activePage").first();
    });
    
    jQuery(".fancybox").fancybox({
        maxWidth	: 800,
        maxHeight	: 800,
        fitToView	: false,
        width		: '40%',
        height		: '50%',
        autoSize	: false,
        closeClick	: false,
        openEffect	: 'none',
        closeEffect	: 'none'
    });
    jQuery('.offer-form,#post-webinar-form').validationEngine();
    
    
    jQuery("#packages_deal").change(function(){
        var packages_deal =  jQuery("#packages_deal").val();
        totalTime = packages_deal;
        jQuery("#totalTime").html(totalTime);
    });    
    
    jQuery(document).on("click", ".timezCls", function(e) {
        var timez_id = jQuery(this).attr('id');
        var data = jQuery(this).attr('data');
        
        if(data == '30') {
            calcTime = parseFloat(calcTime) - .5;
        } else if(data == '1') {
            calcTime = parseFloat(calcTime) - 1;
        } else if(data == '1.5') {
            calcTime = parseFloat(calcTime) - 1.5;
        } else if(data == '2') {
            calcTime = parseFloat(calcTime) - 2;
        } else if(data == '3') {
            calcTime = parseFloat(calcTime) - 3;
        }
        jQuery("#timez_"+ timez_id).remove();
        jQuery("#clacTime").html(calcTime);
    });
    
    
    jQuery('#schedule-meeting-form').validationEngine('attach', {
        onValidationComplete: function( form, status ) {
            if( status == true ) { 
                var packages_deal =  jQuery("#packages_deal").val();
                var date_time_1 =  jQuery("#date_time_1").val();
                var duration =  jQuery("#duration").val();

                var time = (duration == '30'?' minutes':' hours');

                var _duration = (duration == '30'?.5:duration);

                if(calcTime + parseFloat(_duration) > packages_deal){
                    alert('You can\'t select hours more than the package deal.');
                    return;
                }

                if(duration == '30') {
                    calcTime = parseFloat(calcTime) + .5;
                } else if(duration == '1') {
                    calcTime = parseFloat(calcTime) + 1;
                } else if(duration == '1.5') {
                    calcTime = parseFloat(calcTime) + 1.5;
                } else if(duration == '2') {
                    calcTime = parseFloat(calcTime) + 2;
                } else if(duration == '3') {
                    calcTime = parseFloat(calcTime) + 3;
                }
                
                
                if ( packages_deal !="" &&  date_time_1 !="" && duration !="") {
                    
                    var html = '<div class="timez" id="timez_'+ count +'">\n\
                        <input type="hidden" name="_date_time[]" value="'+ date_time_1 +'" />\n\
                        <input type="hidden" name="_duration[]" value="'+ duration +'" />\n\
                        <span> '+date_time_1+' </span> <span> '+ duration +' '+time+'</span> <span class="timez_cross"><a href="javascript:void(0)" class="timezCls" id="'+ count +'" data="'+duration+'">&times;</a></span></div>';
                    
                    count+=1;
                    jQuery("#clacTime").html(calcTime);
                    jQuery("#selectedRow").append(html);
                    //clacHours
                    //selectedRow
                }
                
            }
        }  
    });
    
    
});


jQuery("#send_offer_teacher").on('click', function(e) {
    e.preventDefault();
    var isValid = jQuery("#email_address_of_student").validationEngine('validate', '#email_address_of_student');
    var isValidP = jQuery("#packages_deal").validationEngine('validate', '#packages_deal');

    if( isValid == true && isValidP == true ) {
        var packages_deal =  jQuery("#packages_deal").val();

        if(calcTime < packages_deal){
            alert('You can\'t continue untill you have selected proper package time.');
            return;
        }

        jQuery('#schedule-meeting-form').validationEngine('detach');
        jQuery("#schedule-meeting-form").submit();
        jQuery(document).on('submit','form#schedule-meeting-form', function() {
            
        });
    
        //jQuery('#schedule-meeting-form').submit();
    
    }
});

function validateEmail() {
    var isValid = jQuery("#email_address_of_student").validationEngine('validate', '#email_address_of_student');
    if( isValid == true ) {
        jQuery('#schedule-meeting-form').validationEngine('detach');
        jQuery('#schedule-meeting-form').submit();
    }
}