var _selected = null;
jQuery(window).ready(function(){
    var c = getUrlParameter('c');
    var q = getUrlParameter('q');
    var y = getUrlParameter('y');
    var p = getUrlParameter('_p');
    if(c && q && y){
        setTimeout(function () {
            jQuery('a[data-tab-id="tab2"]').trigger('click');
            var terms = [];
            var element = jQuery('.a-year-list');
            var course = element.data('course');
            terms[0] = {year: y, spring: 1, autumn: 1};
            var element = 'ex';
            var term = c;
            var parent = p;
            sendAjax({action: 'loadExercises', course: course, terms: terms, selected: element, term: term, parent: parent, question: q}, 'POST', 'loadExercises');
        }, 1000);
    }
    if(jQuery('.a-year-list').length > 0) {
        loadDiagram(jQuery('.a-year-list'));
    }
    jQuery('body').on('click', function (event) {
        var target = jQuery( event.target );
        if((!target.is('.m-content-detail') && target.parents('.m-content-detail').length == 0) || target.is('.close')){
            if(jQuery('.m-content-detail').is(':visible')) {
                jQuery('.a-mark-section').showDown('slower');
                jQuery('.a-rating-section').showDown('slower');
                jQuery('.m-content-detail').hideUp('slower');
                //jQuery('.modal-overlay').hide();
                jQuery('.a-main-content').css('height', '');
                controlScroll(false);
            }
        } else if(target.is('a.see_alts')){
            var parent = target.parents('div.block:first');
            parent.find('form.form-two').slideToggle(400);
            parent.find('div.hide_alt_block').removeClass('hide');
            parent.find('div.alt_block').addClass('hide');
            //changeHeight();
        } else if(target.is('a.hidealt')){
            var parent = target.parents('div.block:first');
            parent.find('form.form-two').slideToggle(400);
            parent.find('div.hide_alt_block').addClass('hide');
            parent.find('div.alt_block').removeClass('hide');
            //changeHeight();
        } else if(target.is('a.deliever')){
            var is_correct = false;
            var parent = target.parents('div.block:first');
            parent.find('.myrow').removeClass('a-right').removeClass('a-wrong');
            if(!parent.find('input[type="radio"][name="alt"]').is(':checked')) {
                alert("Please select any option");
                return;
            }
            var selected = parent.find('input[type="radio"][name="alt"]:checked').val();
            var correct = parent.find('input#correct').val();
            parent.find('p.messages').addClass('hide');
            if (selected == correct) {
                is_correct = true;
                parent.find('p.messages.right_'+correct).removeClass('hide');
                parent.find('input[type="radio"][name="alt"]:checked').parents('.myrow:first').addClass('a-right');
            } else {
                parent.find('p.messages.wrong_'+selected).removeClass('hide');
                parent.find('input[type="radio"][name="alt"]:checked').parents('.myrow:first').addClass('a-wrong');
            }
            saveAttempt(parent, is_correct);
        } else if(target.is('a.sort')){
            var terms = [];
            var element = jQuery('.a-year-list');
            var course = element.data('course');
            element.find('li.active').each(function (index) {
                var year = jQuery(this).data('year');
                var spring = 1;
                var autumn = 1;
                if(jQuery('a.a-term.a-active').data('term') == 'spring'){
                    spring = 1;
                    autumn = 0;
                }else if(jQuery('a.a-term.a-active').data('term') == 'autumn'){
                    spring = 0;
                    autumn = 1;
                }
                if(spring == 1 || autumn == 1) {
                    terms[index] = {year: year, spring: spring, autumn: autumn};
                }
            });
            var element = target.data('element');
            var term = target.data('term');
            var parent = target.data('parent');
            var sortby = target.data('sortby');
            var sorttype = target.data('sorttype');
            if(sorttype == 'ASC'){
                target.data('sorttype', 'DESC');
            } else {
                target.data('sorttype', 'ASC');
            }
            sendAjax({action: 'loadExercises', course: course, terms: terms, selected: element, term: term, parent: parent, sort_by: sortby, sort_type: sorttype}, 'POST', 'reLoadExercises');
        }
    })
});
jQuery(document).ready(function () {
    jQuery('.a-year-list li a.year').on('click', function () {
        if(jQuery(this).parent('li').hasClass('active')){
            jQuery(this).parent('li').removeClass('active');
        } else {
            jQuery(this).parent('li').addClass('active');
        }
        loadDiagram(jQuery('.a-year-list'));
    });

    jQuery('.a-diagram-section').on('click', 'a', function () {
        var selected = jQuery(this).data('element');
        _selected = selected;
        loadDiagram(jQuery('.a-year-list'), selected);
    });

    jQuery(document).on('click', 'a.a-term', function () {
        if(jQuery('.m-content-detail').is(':visible')) {
            jQuery('.m-content-detail').slideToggle(400);
            jQuery('.a-mark-section').slideToggle(400);
        }
        var terms = [];
        var element = jQuery('.a-year-list');
        var course = element.data('course');
        var anchor = jQuery(this);
        jQuery('a.a-term').removeClass('a-active');
        jQuery(this).addClass('a-active');
        element.find('li.active').each(function (index) {
            var year = jQuery(this).data('year');
            var spring = 1;
            var autumn = 1;
            if(anchor.data('term') == 'spring'){
                spring = 1;
                autumn = 0;
            }else if(anchor.data('term') == 'autumn'){
                spring = 0;
                autumn = 1;
            }
            if(spring == 1 || autumn == 1) {
                terms[index] = {year: year, spring: spring, autumn: autumn};
            }
        });
        var element = '';
        var term = 'all';
        var parent = 0;
        sendAjax({action: 'loadExercises', course: course, terms: terms, selected: element, term: term, parent: parent}, 'POST', 'loadExercises');
    });

    /*Handling hover events*/
    jQuery(document).on('hover' , '.a-diagram-section .a-exercises' , function() {
        jQuery(this).css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-second-row').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-second-row').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-second-row-bottom').children('span').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-left').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-left').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-right').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-right').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-right-bottom').children('span').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-left-bottom').children('span').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-forth-left').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-forth-left').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-forth-right').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-forth-right').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-forth-left').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-forth-right').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-third-left-bottom .a-text-bottom').css({'background':'#77e6fb', 'color':'#fff'});
    });

    jQuery(document).on('mouseout' , '.a-diagram-section .a-exercises' , function() {
        jQuery(this).removeAttr('style');
        jQuery('.a-diagram-section .a-second-row').children('a').removeAttr('style');
        jQuery('.a-diagram-section .a-second-row').removeAttr('style');
        jQuery('.a-diagram-section .a-second-row-bottom').children('span').removeAttr('style');
        jQuery('.a-diagram-section .a-third-left').children('a').removeAttr('style');
        jQuery('.a-diagram-section .a-third-left').removeAttr('style');
        jQuery('.a-diagram-section .a-third-right').children('a').removeAttr('style');
        jQuery('.a-diagram-section .a-third-right').removeAttr('style');
        jQuery('.a-diagram-section .a-third-right-bottom').children('span').removeAttr('style');
        jQuery('.a-diagram-section .a-third-left-bottom').children('span').removeAttr('style');
        jQuery('.a-diagram-section .a-forth-left').children('a').removeAttr('style');
        jQuery('.a-diagram-section .a-forth-left').removeAttr('style');
        jQuery('.a-diagram-section .a-forth-right').children('a').removeAttr('style');
        jQuery('.a-diagram-section .a-forth-right').removeAttr('style');
        jQuery('.a-diagram-section .a-forth-left').children('a').removeAttr('style');
        jQuery('.a-diagram-section .a-forth-right').children('a').removeAttr('style');
        jQuery('.a-third-left-bottom .a-text-bottom').removeAttr('style');
    });

    jQuery(document).on('hover' , '.a-diagram-section .a-without-tools' , function() {
        jQuery(this).css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-second-row-bottom .a-without-bottom').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-left').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-left').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-left-bottom').children('span').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-left-col .a-forth-left').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-left-col .a-forth-left').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-left-col .a-forth-right').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-left-col .a-forth-right').css({'background':'#77e6fb', 'color':'#fff'});
    });

    jQuery(document).on('mouseout' , '.a-diagram-section .a-without-tools' , function() {
        jQuery(this).removeAttr('style');
        jQuery('.a-second-row-bottom .a-without-bottom').removeAttr('style');
        jQuery('.a-diagram-section .a-third-left').children('a').removeAttr('style');
        jQuery('.a-diagram-section .a-third-left').removeAttr('style');
        jQuery('.a-diagram-section .a-third-left-bottom').children('span').removeAttr('style');
        jQuery('.a-forth-left-col .a-forth-left').children('a').removeAttr('style');
        jQuery('.a-forth-left-col .a-forth-left').removeAttr('style');
        jQuery('.a-forth-left-col .a-forth-right').children('a').removeAttr('style');
        jQuery('.a-forth-left-col .a-forth-right').removeAttr('style');
    });

    jQuery(document).on('hover' , '.a-diagram-section .a-third-left .a-calculation-questions' , function() {
        jQuery(this).css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-third-left-bottom .a-calculation-bottom').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-left-col .a-forth-left').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-left-col .a-forth-left').css({'background':'#77e6fb', 'color':'#fff'});
    });

    jQuery(document).on('mouseout' , '.a-diagram-section .a-calculation-questions' , function() {
        jQuery(this).removeAttr('style');
        jQuery('.a-third-left-bottom .a-calculation-bottom').removeAttr('style');
        jQuery('.a-forth-left-col .a-forth-left').children('a').removeAttr('style');
        jQuery('.a-forth-left-col .a-forth-left').removeAttr('style');
    });

    jQuery(document).on('hover' , '.a-diagram-section .a-third-right .a-calculation-questions' , function() {
        jQuery(this).css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-third-right-bottom .a-calculation-bottom').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-right-col .a-forth-left').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-right-col .a-forth-left').css({'background':'#77e6fb', 'color':'#fff'});
    });

    jQuery(document).on('mouseout' , '.a-diagram-section .a-third-right .a-calculation-questions' , function() {
        jQuery(this).removeAttr('style');
        jQuery('.a-third-right-bottom .a-calculation-bottom').removeAttr('style');
        jQuery('.a-forth-right-col .a-forth-left').children('a').removeAttr('style');
        jQuery('.a-forth-right-col .a-forth-left').removeAttr('style');
    });

    jQuery(document).on('hover' , '.a-diagram-section .a-third-right .a-text-questions' , function() {
        jQuery(this).css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-third-right-bottom .a-text-bottom').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-right-col .a-forth-right').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-right-col .a-forth-right').css({'background':'#77e6fb', 'color':'#fff'});
    });

    jQuery(document).on('mouseout' , '.a-diagram-section .a-third-right .a-text-questions' , function() {
        jQuery(this).removeAttr('style');
        jQuery('.a-third-right-bottom .a-text-bottom').removeAttr('style');
        jQuery('.a-forth-right-col .a-forth-right').children('a').removeAttr('style');
        jQuery('.a-forth-right-col .a-forth-right').removeAttr('style');
    });

    jQuery(document).on('hover' , '.a-diagram-section .a-with-tools' , function() {
        jQuery(this).css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-second-row-bottom .a-with-bottom').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-right').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-right').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-diagram-section .a-third-right-bottom').children('span').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-right-col .a-forth-left').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-right-col .a-forth-left').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-right-col .a-forth-right').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-right-col .a-forth-right').css({'background':'#77e6fb', 'color':'#fff'});
    });

    jQuery(document).on('mouseout' , '.a-diagram-section .a-with-tools' , function() {
        jQuery(this).removeAttr('style');
        jQuery('.a-second-row-bottom .a-with-bottom').removeAttr('style');
        jQuery('.a-diagram-section .a-third-right').children('a').removeAttr('style');
        jQuery('.a-diagram-section .a-third-right').removeAttr('style');
        jQuery('.a-diagram-section .a-third-right-bottom').children('span').removeAttr('style');
        jQuery('.a-forth-right-col .a-forth-left').children('a').removeAttr('style');
        jQuery('.a-forth-right-col .a-forth-left').removeAttr('style');
        jQuery('.a-forth-right-col .a-forth-right').children('a').removeAttr('style');
        jQuery('.a-forth-right-col .a-forth-right').removeAttr('style');
    });

    jQuery(document).on('hover' , '.a-third-left .a-text-questions' , function() {
        jQuery(this).css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-left-col .a-forth-right').children('a').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-forth-left-col .a-forth-right').css({'background':'#77e6fb', 'color':'#fff'});
        jQuery('.a-third-left-bottom .a-text-bottom').css({'background':'#77e6fb', 'color':'#fff'});
    });

    jQuery(document).on('mouseout' , '.a-third-left .a-text-questions' , function() {
        jQuery(this).removeAttr('style');
        jQuery('.a-forth-left-col .a-forth-right').children('a').removeAttr('style');
        jQuery('.a-forth-left-col .a-forth-right').removeAttr('style');
        jQuery('.a-third-left-bottom .a-text-bottom').removeAttr('style');
    });

    jQuery(document).on('mouseout' , '.a-third-left .a-text-questions' , function() {
        jQuery(this).removeAttr('style');
        jQuery('.a-forth-left-col .a-forth-right').children('a').removeAttr('style');
        jQuery('.a-forth-left-col .a-forth-right').removeAttr('style');
        jQuery('.a-third-left-bottom .a-text-bottom').removeAttr('style');
    });

    /*end hover events*/

    jQuery('.a-diagram-section').on('click' , '.a-exercises' , function() {
        clearDiagram();
        var element = jQuery(this).parents('.a-diagram-section');
        if(jQuery(this).hasClass('a-active')){
            jQuery(this).removeClass('a-active');
            element.find('span').removeClass('a-active');
            element.find('a').removeClass('a-active');
            element.find('div.a-forth-right').removeClass('a-active');
            element.find('div.a-forth-left').removeClass('a-active');
            element.find('div.a-third-left').removeClass('a-active');
            element.find('div.a-third-right').removeClass('a-active');
            element.find('div.a-second-row').removeClass('a-active');
        } else {
            element.find('span').addClass('a-active');
            element.find('a').addClass('a-active');
            element.find('div.a-forth-right').addClass('a-active');
            element.find('div.a-forth-left').addClass('a-active');
            element.find('div.a-third-left').addClass('a-active');
            element.find('div.a-third-right').addClass('a-active');
            element.find('div.a-second-row').addClass('a-active');
        }
    });
    jQuery('.a-diagram-section').on('click' , '.a-without-tools' , function() {
        clearDiagram();
        if(jQuery(this).hasClass('a-active')){
            jQuery(this).removeClass('a-active');
            jQuery('.a-second-row-bottom .a-without-bottom').removeClass('a-active');
            jQuery('.a-diagram-section .a-third-left').children('a').removeClass('a-active');
            jQuery('.a-diagram-section .a-third-left').removeClass('a-active');
            jQuery('.a-diagram-section .a-third-left-bottom').children('span').removeClass('a-active');
            jQuery('.a-forth-left-col .a-forth-left').children('a').removeClass('a-active');
            jQuery('.a-forth-left-col .a-forth-left').removeClass('a-active');
            jQuery('.a-forth-left-col .a-forth-right').children('a').removeClass('a-active');
            jQuery('.a-forth-left-col .a-forth-right').removeClass('a-active');
        } else {
            jQuery(this).addClass('a-active');
            jQuery('.a-second-row-bottom .a-without-bottom').addClass('a-active');
            jQuery('.a-diagram-section .a-third-left').children('a').addClass('a-active');
            jQuery('.a-diagram-section .a-third-left').addClass('a-active');
            jQuery('.a-diagram-section .a-third-left-bottom').children('span').addClass('a-active');
            jQuery('.a-forth-left-col .a-forth-left').children('a').addClass('a-active');
            jQuery('.a-forth-left-col .a-forth-left').addClass('a-active');
            jQuery('.a-forth-left-col .a-forth-right').children('a').addClass('a-active');
            jQuery('.a-forth-left-col .a-forth-right').addClass('a-active');
        }
    });
    jQuery('.a-diagram-section').on('click' , '.a-third-left .a-calculation-questions' , function() {
        clearDiagram();
        if(jQuery(this).hasClass('a-active')){
            jQuery(this).removeClass('a-active');
            jQuery('.a-third-left-bottom .a-calculation-bottom').removeClass('a-active');
            jQuery('.a-forth-left-col .a-forth-left').children('a').removeClass('a-active');
            jQuery('.a-forth-left-col .a-forth-left').removeClass('a-active');
        } else {
            jQuery(this).addClass('a-active');
            jQuery('.a-third-left-bottom .a-calculation-bottom').addClass('a-active');
            jQuery('.a-forth-left-col .a-forth-left').children('a').addClass('a-active');
            jQuery('.a-forth-left-col .a-forth-left').addClass('a-active');
        }
    });
    jQuery('.a-diagram-section').on('click' , '.a-third-right .a-calculation-questions' , function() {
        clearDiagram();
        if(jQuery(this).hasClass('a-active')) {
            jQuery(this).removeClass('a-active');
            jQuery('.a-third-right-bottom .a-calculation-bottom').removeClass('a-active');
            jQuery('.a-forth-right-col .a-forth-left').children('a').removeClass('a-active');
            jQuery('.a-forth-right-col .a-forth-left').removeClass('a-active');
        } else{
            jQuery(this).addClass('a-active');
            jQuery('.a-third-right-bottom .a-calculation-bottom').addClass('a-active');
            jQuery('.a-forth-right-col .a-forth-left').children('a').addClass('a-active');
            jQuery('.a-forth-right-col .a-forth-left').addClass('a-active');
        }
    });
    jQuery('.a-diagram-section').on('click' , '.a-third-right .a-text-questions' , function() {
        clearDiagram();
        if(jQuery(this).hasClass('a-active')){
            jQuery(this).removeClass('a-active');
            jQuery('.a-third-right-bottom .a-text-bottom').removeClass('a-active');
            jQuery('.a-forth-right-col .a-forth-right').children('a').removeClass('a-active');
            jQuery('.a-forth-right-col .a-forth-right').removeClass('a-active');
        } else {
            jQuery(this).addClass('a-active');
            jQuery('.a-third-right-bottom .a-text-bottom').addClass('a-active');
            jQuery('.a-forth-right-col .a-forth-right').children('a').addClass('a-active');
            jQuery('.a-forth-right-col .a-forth-right').addClass('a-active');
        }
    });
    jQuery('.a-diagram-section').on('click' , '.a-with-tools' , function() {
        clearDiagram();
        if(jQuery(this).hasClass('a-active')){
            jQuery(this).removeClass('a-active');
            jQuery('.a-second-row-bottom .a-with-bottom').removeClass('a-active');
            jQuery('.a-diagram-section .a-third-right').children('a').removeClass('a-active');
            jQuery('.a-diagram-section .a-third-right').removeClass('a-active');
            jQuery('.a-diagram-section .a-third-right-bottom').children('span').removeClass('a-active');
            jQuery('.a-forth-right-col .a-forth-left').children('a').removeClass('a-active');
            jQuery('.a-forth-right-col .a-forth-left').removeClass('a-active');
            jQuery('.a-forth-right-col .a-forth-right').children('a').removeClass('a-active');
            jQuery('.a-forth-right-col .a-forth-right').removeClass('a-active');
        } else {
            jQuery(this).addClass('a-active');
            jQuery('.a-second-row-bottom .a-with-bottom').addClass('a-active');
            jQuery('.a-diagram-section .a-third-right').children('a').addClass('a-active');
            jQuery('.a-diagram-section .a-third-right').addClass('a-active');
            jQuery('.a-diagram-section .a-third-right-bottom').children('span').addClass('a-active');
            jQuery('.a-forth-right-col .a-forth-left').children('a').addClass('a-active');
            jQuery('.a-forth-right-col .a-forth-left').addClass('a-active');
            jQuery('.a-forth-right-col .a-forth-right').children('a').addClass('a-active');
            jQuery('.a-forth-right-col .a-forth-right').addClass('a-active');
        }
    });
    jQuery(document).on('click' , '.a-third-left .a-text-questions' , function() {
        clearDiagram();
        if(jQuery(this).hasClass('a-active')) {
            jQuery(this).removeClass('a-active');
            jQuery('.a-forth-left-col .a-forth-right').children('a').removeClass('a-active');
            jQuery('.a-forth-left-col .a-forth-right').removeClass('a-active');
            jQuery('.a-third-left-bottom .a-text-bottom').removeClass('a-active');
        } else {
            jQuery(this).addClass('a-active');
            jQuery('.a-forth-left-col .a-forth-right').children('a').addClass('a-active');
            jQuery('.a-forth-left-col .a-forth-right').addClass('a-active');
            jQuery('.a-third-left-bottom .a-text-bottom').addClass('a-active');
        }
    });
    jQuery(document).on('click' , '.a-min' , function() {
        clearDiagram();
        if(jQuery(this).hasClass('a-active')) {
            jQuery(this).removeClass('a-active');
        } else {
            jQuery(this).addClass('a-active');
        }
    });
    jQuery(document).on('click' , '.a-over-min' , function() {
        clearDiagram();
        if(jQuery(this).hasClass('a-active')) {
            jQuery(this).removeClass('a-active');
        } else {
            jQuery(this).addClass('a-active');
        }
    });
    jQuery('a[data-tab-id="tab2"]').on('click', function () {
        jQuery('html,body').animate({scrollTop: jQuery('.a-diagram-section').offset().top - 15}, 'slower');
    })
    if(jQuery('.a-diagram-section').length > 0) {
        //jQuery('html,body').animate({scrollTop: jQuery('.a-diagram-section').offset().top}, 'slower');
    }
    jQuery('.a-rating-section').on('click', '.d-block-opener', function() {
        var terms = [];
        var element = jQuery('.a-year-list');
        var course = element.data('course');
        element.find('li.active').each(function (index) {
            var year = jQuery(this).data('year');
            var spring = 1;
            var autumn = 1;
            if(spring == 1 || autumn == 1) {
                terms[index] = {year: year, spring: spring, autumn: autumn};
            }
        });
        var element = jQuery(this).data('element');
        var term = jQuery(this).data('term');
        var parent = jQuery(this).data('parent');
        sendAjax({action: 'loadExercises', course: course, terms: terms, selected: element, term: term, parent: parent}, 'POST', 'loadExercises');
    });
    jQuery('body').on('change', '.solution_setup', function () {
        if(jQuery(this).val() == 'single'){
            jQuery('.alt_ans').hide();
        }else{
            jQuery('.alt_ans').show();
        }
    })
});

function saveAttempt(parent, is_correct) {
    var exercise_id = parent.data('exercise-id');
    var course = parent.data('course');
    var correct = (is_correct?1:0);
    var alt_choose = parent.find('input[type="radio"][name="alt"]:checked').val();
    alt_choose = 'alt'+alt_choose;
    sendAjax({action: 'saveAttempt', exercise_id: exercise_id, course: course, alt_choose: alt_choose, correct: correct}, 'POST', 'saveAttempt');
}


function clearDiagram() {
    var element = jQuery('.a-exercises').parents('.a-diagram-section');
    element.removeClass('a-active');
    element.find('span').removeClass('a-active');
    element.find('a').removeClass('a-active');
    element.find('div.a-forth-right').removeClass('a-active');
    element.find('div.a-forth-left').removeClass('a-active');
    element.find('div.a-third-left').removeClass('a-active');
    element.find('div.a-third-right').removeClass('a-active');
    element.find('div.a-second-row').removeClass('a-active');
}

function highligtDiagram() {
    var element = jQuery('.a-exercises').parents('.a-diagram-section');
    element.addClass('a-active');
    element.find('span').addClass('a-active');
    element.find('a').addClass('a-active');
    element.find('div.a-forth-right').addClass('a-active');
    element.find('div.a-forth-left').addClass('a-active');
    element.find('div.a-third-left').addClass('a-active');
    element.find('div.a-third-right').addClass('a-active');
    element.find('div.a-second-row').addClass('a-active');
}

function loadDiagram(element, selected) {
    var terms = [];
    var course = element.data('course');
    element.find('li.active').each(function (index) {
        var year = jQuery(this).data('year');
        var spring = 1;
        var autumn = 1;
        if(spring == 1 || autumn == 1) {
            terms[index] = {year: year, spring: spring, autumn: autumn};
        }
    });
    var color = jQuery('input[name="radio"]:checked').val();
    if(color == 'i_completed'){
        jQuery('.last_completed').removeClass('disable').addClass('a-active');
        jQuery('.how_other').removeClass('a-active').addClass('disable');
    }else if(color == 'other'){
        jQuery('.last_completed').addClass('disable').removeClass('a-active');
        jQuery('.how_other').addClass('a-active').removeClass('disable');
    }
    if(selected == undefined) {
        sendAjax({action: 'loadDiagram', course: course, terms: terms, color: color}, 'POST', 'loadDiagram');
    }else{
        sendAjax({action: 'loadDiagram', course: course, terms: terms, selected: selected, color: color}, 'POST', 'loadDiagramSelected');
    }
}

function showLoading() {
    var el, viewport;

    hideLoading();

    el = jQuery('<div id="fancybox-loading"><div></div></div>').appendTo('body');
}

function hideLoading() {
    jQuery('#fancybox-loading').remove();
}

function sendAjax(data, method, type){
    showLoading();
    /*if(jQuery('.modal-overlay').length == 0){
        jQuery('body').append('<div class="modal-overlay"></div>');
        jQuery('body').css('position', 'relative');
    }*/
    jQuery.ajax({
        type: method,
        url: ajax_object.ajax_url,
        data: data,
        cache: false,
        dataType: 'json',
        success: function(response){
            if(type == 'loadDiagram'){
                jQuery('.a-diagram-section').html(response.diagram).show();
                jQuery('.a-rating-section').html(response.categories);
                if((data.terms).length > 0) {
                    highligtDiagram();
                } else {
                    clearDiagram();
                }
                jQuery('div.terms').remove();
                jQuery('.a-year-list').after(response.terms);
            } else if(type == 'loadDiagramSelected') {
                jQuery('.a-rating-section').html(response.categories);
            } else if(type == 'loadExercises') {
                /*if(jQuery('.m-content-detail').length == 0){
                    jQuery('body').prepend('<div class="m-content-detail"></div>');
                }*/
                jQuery('.m-content-detail').html(response.detail);
                //setTimeout(function () {
                    //var headOffset = jQuery('.a-head-section').offset();
                    //var headHeight = jQuery('.a-head-section').outerHeight();
                    //var diagramHeight = jQuery('.a-diagram-section').outerHeight() + 50;
                    //var top = headOffset.top + headHeight + 20;
                    //jQuery('.m-content-detail').css({top: top+'px', 'left': '15%'});
                    //headHeight = headHeight + 100;
                    //jQuery('.modal-overlay').show();
                    //var contentHeight = jQuery('.m-content-detail').outerHeight();
                    //var totalHeight = diagramHeight+contentHeight+headHeight;
                    //jQuery('.a-main-content').css('height', totalHeight+'px');
                jQuery('.m-content-detail').showDown('slower');
                    //jQuery('.m-content-detail').css('visibility','visible').hide().showDown('slower');
                jQuery('.a-mark-section').hideUp('slower');
                jQuery('html,body').animate({scrollTop: jQuery('.a-diagram-section').offset().top - 15}, 'slower');
                jQuery('.a-rating-section').hideUp('slower');
                controlScroll(true);
                MathJax.Hub.Queue(["Typeset",MathJax.Hub,"MathExample"]);
                    //hideLoading();
                //}, 1000);
            } else if(type == 'reLoadExercises'){
                jQuery('.m-content-detail').html(response.detail);
                MathJax.Hub.Queue(["Typeset",MathJax.Hub,"MathExample"]);
            } else if(type == 'saveAttempt'){
                //changeHeight();
            }
            //if(type != 'loadExercises') {
            hideLoading();
            //}
        },
        error: function(){
            hideLoading();
        }
    });
}
var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};

function controlScroll(top) {
    if(top){
        var eTop = jQuery(".a-diagram-section").offset().top - 15;
        jQuery(document).on("scroll", function(e){
            var windowScrollTop = jQuery(window).scrollTop();
            if(windowScrollTop < eTop){
                jQuery(document).scrollTop(eTop);
            }
        });
    }else{
        jQuery(document).off("scroll");
    }
}

function triggerFilter($this){
    $id = jQuery($this).val();
    jQuery('#filters #'+$id+' a.sort').data('sorttype', 'ASC').trigger('click');
}

function changeHeight() {
    setTimeout(function () {
        var headHeight = jQuery('.a-head-section').outerHeight() + 100;
        var diagramHeight = jQuery('.a-diagram-section').outerHeight() + 50;
        var contentHeight = jQuery('.m-content-detail').outerHeight();
        var totalHeight = diagramHeight + contentHeight + headHeight;
        jQuery('.a-main-content').css('height', totalHeight + 'px');
    }, 700);
}

(function($) {
    'use strict';
    // Sort us out with the options parameters
    var getAnimOpts = function (a, b, c) {
            if (!a) { return {duration: 'normal'}; }
            if (!!c) { return {duration: a, easing: b, complete: c}; }
            if (!!b) { return {duration: a, complete: b}; }
            if (typeof a === 'object') { return a; }
            return { duration: a };
        },
        getUnqueuedOpts = function (opts) {
            return {
                queue: false,
                duration: opts.duration,
                easing: opts.easing
            };
        };
    // Declare our new effects
    $.fn.showDown = function (a, b, c) {
        var slideOpts = getAnimOpts(a, b, c), fadeOpts = getUnqueuedOpts(slideOpts);
        jQuery(this).hide().css('opacity', 0).slideDown(slideOpts).animate({ opacity: 1 }, fadeOpts);
    };
    $.fn.hideUp = function (a, b, c) {
        var slideOpts = getAnimOpts(a, b, c), fadeOpts = getUnqueuedOpts(slideOpts);
        jQuery(this).show().css('opacity', 1).slideUp(slideOpts).animate({ opacity: 0 }, fadeOpts);
    };
}(jQuery));