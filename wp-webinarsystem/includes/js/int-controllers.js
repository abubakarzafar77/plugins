jQuery(document).ready(function () {
    transferLivepData();
});

setInterval(function () {
    transferLivepData();
}, 5000);

function transferLivepData(){
    if(typeof theWebinarId==="undefined")
        return;
    
    var data_ob = {
        action: 'transferLivepData',
        webinar_id: theWebinarId,
        webinar_st: theWebinarstatus,
    };
    jQuery.ajax({
        url: ajaxurl,
        data: data_ob,
        dataType: 'json',
        type: 'POST',
        success: function (response) {
            console.log(JSON.stringify(response));
            
            // Get online count
            jQuery('#webinar-live-viewers').html(response.data.online_attendees.count);
            // End of get online count


            // Incentive status.
            incentiveStatusChange(response.data.incentive_stauts.isShow);
            // End of incentive status;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // Errors handled.
        }
    });
}

function incentiveStatusChange(isShow) {
    if (isShow === true) {
        jQuery('#show_incentive').show();
        jQuery('#gift_icon').css('color', '#ff002c');
        jQuery('#data_show_incentive').val('');
    } else {
        jQuery('#show_incentive').hide();
        jQuery('#gift_icon').css('color', ' #4c4c4c');
        jQuery('#data_show_incentive').val('yes');
    }
}

var theSaveQuestionButton;
var theSaveQuestionButtonVal;
jQuery(document).on('click', '#saveQuestion', function (e) {
    var ques_name = jQuery('#que_name').val();
    var ques_email = jQuery('#que_email').val();
    var quest = jQuery('#addQuestion').val();
    if (ques_email.length < 3 || !validateEmail(ques_email) || ques_name.length < 1 || quest.length < 1) {
        alert(questionFormerror);
        return false;
    }

    var datas = {'action': 'saveQuestionAjax', 'question': quest, 'name': jQuery('#que_name').val(),
        'email': jQuery('#que_email').val(), 'webinar_id': theWebinarId};
    theSaveQuestionButton = jQuery(this);
    theSaveQuestionButtonVal = theSaveQuestionButton.val();
    jQuery(this).val(questionWait);
    jQuery(this).attr('disabled', 'disabled');
    jQuery.ajax({type: 'POST', data: datas, url: ajaxurl, dataType: 'json'
    }).done(function (data) {
        jQuery('#myQuestions').show();
        theSaveQuestionButton.val(theSaveQuestionButtonVal);
        theSaveQuestionButton.removeAttr('disabled');
        jQuery('#addQuestion').val('');
        addQuestionToPage("" + data.question, "" + data.time);
    });
    //e.preventDefault();
});
function addQuestionToPage(question, time) {
    jQuery('#ques_load').prepend(jQuery('<p class="myquestion"><span>' + time + '</span>' + question + '</p>').hide().fadeIn(2000));
}

function validateEmail(email) {
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function update_incentive() {
    jQuery.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
            'action': 'updateIncentive',
            'post_id': theWebinarId,
            'status': theWebinarstatus
        }
    });
}

jQuery(document).on('click','#gift_icon',function (){
    gift_icons(); 
});

function gift_icons() {
    var data_show_incentive = jQuery('#data_show_incentive').val();
    if (data_show_incentive == 'yes') {
        incentiveStatusChange(true);
        update_incentive();
    } else {
        incentiveStatusChange(false);
        update_incentive();
    }
}