jQuery(function(){
   jQuery('.js-category-multiple').select2({
        placeholder: "Select a category",
        allowClear: true,
        tokenSeparators: [',', ' ']
    }).on('change', function (evt) {
       var courses = [];
       jQuery("#course option:selected").each(function() {
           courses.push(jQuery(this).val());
       });
       sendAjax(ajaxurl, {action:'load_subChapters', courses: courses}, 'POST', 'sub_chapters');
    });

    var courses = [];
    jQuery("#course option:selected").each(function() {
        courses.push(jQuery(this).val());
    });
    if(courses.length > 0){
        sendAjax(ajaxurl, {action:'load_subChapters', courses: courses}, 'POST', 'sub_chapters_load');
    }

    jQuery(".duration").change(function() {
        var checked = jQuery(this).is(':checked');
        jQuery(".duration").prop('checked',false);
        if(checked) {
            jQuery(this).prop('checked',true);
        }
    });

    jQuery(".publish").change(function() {
        var checked = jQuery(this).is(':checked');
        jQuery(".publish").prop('checked',false);
        if(checked) {
            jQuery(this).prop('checked',true);
        }
    });

    jQuery(".tool").change(function() {
        var checked = jQuery(this).is(':checked');
        jQuery(".tool").prop('checked',false);
        if(checked) {
            jQuery(this).prop('checked',true);
        }
    });

    jQuery(".ex_type").change(function() {
        var checked = jQuery(this).is(':checked');
        jQuery(".ex_type").prop('checked',false);
        if(checked) {
            jQuery(this).prop('checked',true);
        }
    });

    jQuery(".corr_alternative").change(function() {
        var checked = jQuery(this).is(':checked');
        jQuery(".corr_alternative").prop('checked',false);
        if(checked) {
            jQuery(this).prop('checked',true);
        }
    });

    jQuery("#fe_createplan").validate({
        rules: {
            course: "required",
            year: "required",
            term: "required",
            exercise_name: {
                required: true,
                minlength: 2
            },
            sub_chapter: "required",
            relevant_video: {
                required: true,
                minlength: 2
            },
            duration: "required",
            tool: "required",
            ex_type: "required",
            corr_alternative: "required",
            text_context_html: "required",
            alt_1_exp: "required",
            alt_2_exp: "required",
            alt_3_exp: "required",
        },
        messages: {
            course: "Please select your course",
            year: "Please select year",
            term: "Please select term",
            exercise_name: {
                required: "Please enter exercise name",
                minlength: "Your exercise name must consist of at least 2 characters"
            },
            sub_chapter: "Please select sub chapter",
            relevant_video: {
                required: "Please enter relevant video",
                minlength: "Your relevant video text must be at least 2 characters long"
            },
            duration: "Please choose duration",
            tool: "Please choose tool",
            ex_type: "Please choose exercise type",
            corr_alternative: "Please choose correct alternative",
            text_context_html: "Please enter a etxt and context HTML",
            alt_1_exp: "Please enter Alt 1",
            alt_2_exp: "Please enter Alt 2",
            alt_3_exp: "Please enter Alt 3"
        },
        errorPlacement: function(error, element) {
            if (element.attr("type") == "checkbox") {
                error.insertAfter(jQuery(element).parents('div.tr_form-box').find('label:last'));
            }else{
                error.insertAfter(element);
            }
        }
    });

});

function showLoading() {
    var el, viewport;

    hideLoading();

    el = jQuery('<div id="fancybox-loading"><div></div></div>').appendTo('body');
}

function hideLoading() {
    jQuery('#fancybox-loading').remove();
}

function sendAjax(URL, data, method, type){
    showLoading();
    jQuery.ajax({
        type: method,
        url: URL,
        data: data,
        cache: false,
        dataType: 'json',
        success: function(response){
            if(type == 'sub_chapters' || type == 'sub_chapters_load'){
                if(response.html != "") {
                    jQuery('#sub_chapter option[value!=""]').remove();
                    jQuery('#sub_chapter').append(response.html);
                }else{
                    jQuery('#sub_chapter option[value!=""]').remove();
                }
                if(type == 'sub_chapters_load'){
                    if(jQuery('#sub_chapter_hidden').val()){
                        $val = jQuery('#sub_chapter_hidden').val();
                        jQuery('#sub_chapter').val($val);
                    }
                }
            }
            hideLoading();
        },
        error: function(){
            hideLoading();
        }
    });
}