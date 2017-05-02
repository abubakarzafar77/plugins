var is_category_selected = 0;
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
            jQuery('.category_row h2').text('Våre studiekamerater');
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
    jQuery('#duration').on('change', function(){
        jQuery('#budget').val(jQuery('#duration :selected').attr('data-budget'));
    });
    jQuery('#date_time').datetimepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 0
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
});