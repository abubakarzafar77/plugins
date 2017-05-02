<div class="register_content inner">
    <div class="confirm_information j-pay-popup">
        <form method="post" action="?page=<?php echo $view;?>&offer=<?php echo $offer;?>&job=<?php echo $job;?>" id="payment-form">
            <input type="hidden" name="first_name" value="<?php echo $user_info->first_name; ?>" />
            <input type="hidden" name="last_name" value="<?php echo $user_info->last_name; ?>" />
            <input type="hidden" name="amount" value="<?php echo trim(str_replace(array('kr', 'Kr', 'kR', 'KR'), '', $offer_details->offer_amount));?>" />
            <input type="hidden" name="save" value="pay">
            <input type="hidden" name="offer_id" value="<?php echo $offer; ?>" />
            <input type="hidden" name="job" value="<?php echo $job; ?>" />
            <h2>Choosed <?php echo $model->get_teacher_name($offer_details->offer_teacher_id);?> and pay (<?php echo $offer_details->offer_amount;?>)</h2>
            <div class="clear" style="height: 10px;"></div>
            <?php if($profile_id):?>
                <label for="existing_id"><input type="radio" name="existing" id="existing_id" value="<?php echo $profile_id;?>" style="opacity: 1;" /> Pay Using existing cards</label>
                <label for="new_card"><input type="radio" name="existing" id="new_card" value="0" style="opacity: 1;" /> Pay using new credit card</label>
            <?php endif;?>
            <div class="visa_card_bg"<?php echo ($profile_id?' style="display: none;"':"")?>>
                <h1>Betalingsinformasjon</h1>
                <h2>KORTNUMMER</h2>
                <input type="text" size="20" autocomplete="off" data-encrypted-name="number" name="number" class="field large validate[required]" />
                <div class="field_row">
                    <div class="filed_title">GYLDIG T.O.M  <span class="title_b"> CVC KODE </span></div>
                    <?php echo monthDropdown("month", 'month_field validate[required]');?>
                    <select data-encrypted-name="exp-year" name="exp-year" class="year_field small validate[required]">
                        <?php
                        $start = date('Y');
                        $end = $start + 11;
                        for($i=$start;$i<=$end;$i++){
                            echo '<option value="'.$i.'">'.$i.'</option>';
                        }
                        ?>
                    </select>
                    <input type="text" size="4" autocomplete="off" value="" data-encrypted-name="cvv" name="cvv" class="code_field small validate[required,custom[onlyLetterNumber]]" />
                    <div class="clear"></div>
                </div>
            </div>
            <div class="clear" style="height: 20px;"></div>
            <div class="register_btn">
                <input type="submit" value=" Pay " style="width: 89% !important;" />
            </div>
            <div class="clear"></div>
        </form>
    </div>
</div>
<script type="text/javascript">
    jQuery(window).ready(function(){
        jQuery(".large").mask("9999 9999 9999 9999");
        jQuery('input[name="existing"]').on('change', function(){
            if(jQuery(this).attr('id') == 'new_card'){
                jQuery('.visa_card_bg').fadeIn();
            }else{
                jQuery('.visa_card_bg').fadeOut();
            }
        });
        jQuery('#payment-form').validationEngine({
            onValidationComplete: function(form, status){
                if(status == true){
                    var offer_id = jQuery('input[name="offer_id"]').val();
                    var job = jQuery('input[name="job"]').val();
                    jQuery.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: WebinarAjaxurl,
                        data: form.serialize()+'&action=pay',
                        success: function (data) {
                            if(data.success){
                                jQuery('#btn_'+offer_id+'_'+job).parent().html('<strong>PAID</strong>');
                                jQuery('.my_jobs').before('<div class="col-md-12"><div class="alert alert-success">'+data.success+'</div></div>');
                                jQuery.fancybox.close();
                                setTimeout(function(){
                                    window.location.href = '<?php echo home_url('/studiekamerat?page=scheduled_sessions&job_id='.$job);?>';
                                }, 500)
                            }else{
                                jQuery('.my_jobs').before('<div class="col-md-12"><div class="alert alert-danger">'+data.error+'</div></div>');
                                jQuery.fancybox.close();
                            }
                        }
                    });
                }
            }
        });
    });
</script>