<?php
if (is_user_logged_in()) {
    if (strpos($_SERVER['HTTP_REFERER'], 'abonnement') > 0 && strpos($_SERVER['HTTP_REFERER'], '?') <= 0 && !count($_GET))
    {
        wp_redirect($_SERVER['HTTP_REFERER'] . '?logg_inn=success');
    }
} ?>
<?php if(isset($_REQUEST['pkg'])):
    # ALTER TABLE  `wptest_braintree_users_subscriptions` ADD  `subscription_plan` VARCHAR( 20 ) NOT NULL DEFAULT  'mattevideo' AFTER  `subscription_id` ;
 ?>
<?php $data = $responce['data'];?>
<div class="register_content">
    <form method="post" action="" id="confirm-data">
        <?php $text = '';?>
        <?php if($_REQUEST['pkg'] == 1){
            $text = utf8_encode('<h5>Bindingstid: 6 månder</h5><h5>Månedspris: 99 kr</h5>');
        ?>
            <input type="hidden" name="package" id="package" value="99_kr_plan">
        <?php }else if($_REQUEST['pkg'] == 2){
            $text = utf8_encode('<h5>Bindingstid: 3 månder</h5><h5>Månedspris: 149 kr</h5>');
        ?>
            <input type="hidden" name="package" id="package" value="149_kr_plan">
        <?php }else if($_REQUEST['pkg'] == 3){
            $text = utf8_encode('<h5>Bindingstid: 1 månder</h5><h5>Månedspris: 199 kr</h5>');
        ?>
            <input type="hidden" name="package" id="package" value="199_kr_plan">
        <?php }?>
        <div class="register_left">
            <?php if($responce['status'] == 'error'):?>
                <div class="error">
                    <?php echo $responce['message'];?>
                </div>
            <?php endif;?>
            <h2>Mattevideo abonnement</h2>
            <?php echo $text;?><br />
            <div class="register_form">
                <div class="register_form_left">
                    <input type="hidden" name="step" value="4" />
                    <input type="hidden" name="save" value="subscribe" />
                    <input name="first_name" type="text" value="<?php echo (isset($data['first_name']))?$data['first_name']:''; ?>" class="field less_width first" autocomplete="off" placeholder="Fornavn" />
                    <input name="last_name" type="text" value="<?php echo (isset($data['last_name']))?$data['last_name']:''; ?>" class="field less_width" autocomplete="off" placeholder="Etternavn" />
                    <input name="email" type="text" value="<?php echo (isset($data['email']))?$data['email']:''; ?>" class="field" autocomplete="off" placeholder="Epost" />
                    <input name="retype_email" type="text" value="<?php echo (isset($data['retype_email']))?$data['retype_email']:''; ?>" class="field" autocomplete="off" placeholder="Gjenta epost" />
                </div>
                <div class="clear"></div>
            </div>
            <div class="visa_card_bg">
                <h1>Betalingsinformasjon</h1>
                <h2>KORTNUMMER</h2>
                <input type="text" size="20" autocomplete="off" data-encrypted-name="number" name="number" class="field large" value="<?php echo (isset($data['number']))?$data['number']:''; ?>" />
                <div class="field_row">
                    <div class="filed_title">GYLDIG T.O.M  <span class="title_b"> CVC KODE <a href="javascript:void(0);" id="questionMark">(?)</a></span></div>
                    <?php echo monthDropdown("month", 'month_field', $data['month']);?>
                    <select data-encrypted-name="exp-year" name="exp-year" class="year_field small">
                        <?php
                            $start = date('Y');
                            $end = $start + 11;
                            for($i=$start;$i<=$end;$i++){
                                echo '<option value="'.$i.'"'.((isset($data['year']) && $data['year'] == $i)?' selected':'').'>'.$i.'</option>';
                            }
                        ?>
                    </select>
                    <input type="text" size="4" autocomplete="off" value="<?php echo (isset($data['cvv']))?$data['cvv']:''; ?>" data-encrypted-name="cvv" name="cvv" class="code_field small" />
                    <div id="cvv-info-div">
                        <img src="wp-content/plugins/braintree-payment/images/140422-cvc-kode.png" alt="">
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
            <div class="checkbox_area">
                <div class="checkbox">
                    <input id="terms" type="checkbox" name="terms" value="1"<?php //echo (isset($data) && !empty($data)?' checked="checked"':'');?> />
                    <label for="terms">
                        <span></span>
                    </label>
                </div>
                <div class="checkbox_text"><?php echo utf8_encode('Jeg har lest kjøpsvilkårene (<a href="/kjopsvilkar" class="visa_links" target="_blank">her</a>). Betalingen behandles via betalingsløsning Braintree. Brukernavn og passord sendes på epost.');?></div>
                <div class="clear"></div>
            </div>
            <div class="register_btn">
                <input type="submit" value="Opprett abonnement"<?php //echo (!isset($data) || empty($data)?' class="disabled"':'');?> />
            </div>
        </div>
        <div class="clear"></div>
    </form>
</div>
<script type="text/javascript">
    jQuery(window).ready(function(){
        jQuery(".large").mask("9999 9999 9999 9999");
    });
</script>
<?php endif;?>
<?php if(!isset($_REQUEST['pkg'])):
    $url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    //echo '<span style="color: white;">'.print_r($_SERVER).'</span>';
?>
<div class="price-col">
	<div class="text-center heading-col">Velg abonnementet<br /> som passer best for deg</div>
    <div class="col-md-12 custom_left_margin">
    	<!--pric-column-upper---->
    	<div class="col-md-4 col-sm-6 custom_width">
        		 <div class="p-inner-col">
                    <div class="p-heading-col">
                      <strong>6 <?php echo utf8_encode('månder');?> </strong>
                      <span>bindingstid</span>
                    </div>
                    <div class="p-price-col">
                        <strong>99,-</strong>
                        <span>kr pr mnd</span>
                        <a class="price-btn" href="<?php echo $url;?>?pkg=1"><?php echo utf8_encode('Gå videre');?></a>
                    </div>
                  </div>
        </div>
        <div class="col-md-4 col-sm-6 custom_width">
        		<div class="p-inner-col">
                    <div class="p-heading-col">
                      <strong>3 <?php echo utf8_encode('månder');?> </strong>
                      <span>bindingstid</span>
                    </div>
                    <div class="p-price-col">
                        <strong>149,-</strong>
                        <span>kr pr mnd</span>
                        <a class="price-btn" href="<?php echo $url;?>?pkg=2"><?php echo utf8_encode('Gå videre');?></a>
                    </div>
                  </div>	
        </div>
        <div class="col-md-4 col-sm-6 custom_width">
        		<div class="p-inner-col">
                    <div class="p-heading-col">
                      <strong>Ingen</strong>
                      <span>bindingstid</span>
                    </div>
                    <div class="p-price-col">
                        <strong>199,-</strong>
                        <span>kr pr mnd</span>
                        <a class="price-btn" href="<?php echo $url;?>?pkg=3"><?php echo utf8_encode('Gå videre');?></a>
                    </div>
                  </div>
        </div>
        <div class="clearfix"></div>
		<!----price-lower-column--->
		<div class="p-content-points">
         <div class="p-main-content">
            <div class="p-text-col">Alle abonnementene gir full tilgang til alle fag, videoer, quiz, eksamensgjennomganger og studietips.</div>
            <div class="p-text-col"><?php echo utf8_encode('Brukernavn og passord sendes umiddelbart på epost når man oppretter abonnement.');?></div>
            <div class="p-text-col"><?php echo utf8_encode('100% fornøyd garanti. Angrer du kjøpet i løpet av 24 timer får du pengene tilbake.');?></div>
            <div class="p-lowertext-col"><?php echo utf8_encode('Kjøpsvilkår finner du <a href="/kjopsvilkar" target="_blank">her</a>');?></div>
          </div>
        </div>
    </div>

</div>
<?php endif;?>