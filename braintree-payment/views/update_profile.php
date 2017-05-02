<?php 
$user_data = wp_get_current_user();
if(!$user_data){
    echo '<p>You need to login first</p>'; 
}
else{
    $user_id = $user_data->ID;
    $user_info = get_userdata($user_id);
    $cse_key = $responce['cse_key'];
?>
<?php 
    if($responce['status'] == 'error'){
    ?>
        <div class="error">
            <?php echo $responce['message']; ?>
        </div>
    <?php 
    }
    else if($responce['status'] == 'ok'){
    ?>
        <div class="error">
            <?php echo $responce['message']; ?>
        </div>
    <?php 
    }
?>
    <div class="register_content inner">
        <div class="confirm_information">
            <form method="post" action="?page_type=success">
                <input type="hidden" name="step" value="update_profile">
                <input type="hidden" name="first_name" value="<?php echo $user_info->first_name; ?>" />
                <input type="hidden" name="last_name" value="<?php echo $user_info->last_name; ?>" />
                <input name="email" type="hidden" value="<?php echo $user_info->user_email; ?>" />
                <input name="retype_email" type="hidden" value="<?php echo $user_info->user_email; ?>" />
                <input type="hidden" name="save" value="update_profile">
                <input type="hidden" name="user_id" value="<?php echo $user_info->id; ?>" />
                <div class="visa_card_bg">
                    <h1>Betalingsinformasjon</h1>
                    <h2>KORTNUMMER</h2>
                    <input type="text" size="20" autocomplete="off" data-encrypted-name="number" name="number" class="field large" />
                    <div class="field_row">
                        <div class="filed_title">GYLDIG T.O.M  <span class="title_b"> CVC KODE <a href="javascript:void(0);" id="questionMark">(?)</a></span></div>
                        <?php echo monthDropdown("month", 'month_field');?>
                        <select data-encrypted-name="exp-year" name="exp-year" class="year_field small">
                            <?php
                            $start = date('Y');
                            $end = $start + 11;
                            for($i=$start;$i<=$end;$i++){
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                            ?>
                        </select>
                        <input type="text" size="4" autocomplete="off" value="" data-encrypted-name="cvv" name="cvv" class="code_field small" />
                        <div id="cvv-info-div">
                            <img src="wp-content/plugins/braintree-payment/images/140422-cvc-kode.png" alt="">
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="checkbox_area">
                    <div class="checkbox">
                        <input id="terms" type="checkbox" name="terms" value="1">
                        <label for="terms">
                            <span></span>
                        </label>
                    </div>
                    <div class="checkbox_text"><?php echo 'Jeg har lest kjøpsvilkårene (<a href="/kjopsvilkar-2" class="visa_links" target="_blank">her</a>). Betalingen behandles via betalingsløsning Braintree. Brukernavn og passord sendes på epost.';?></div>
                    <div class="clear"></div>
                </div>
                <div class="register_btn">
                    <input type="submit" value="Opprett abonnement" />
                </div>
                <div class="clear"></div>
                <div class="small_text">
                    <p>Brukernavn og passord sendes umiddelbart på e-post</p>
                </div>


            </form>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(window).ready(function(){
            jQuery(".large").mask("9999 9999 9999 9999");
        });
    </script>
<?php 
} ?>