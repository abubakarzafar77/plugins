<?php
$user_info = $responce['data']['user_info'];
$cancel_sub_info = $responce['data']['sub_info'];
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
?>
<div class="register_content inner">
    <div class="confirm_information">
        <h2>Mattevideo abonnement</h2>
        <?php
        $url = '?page_type=success';
        if($cancel_sub_info->subscription_plan != 'mattevideo'){
            $url = '?pkg='.$pkg.'&plan='.$cancel_sub_info->subscription_plan;
        }
        ?>
        <form method="post" action="<?php echo $url;?>">
            <dl>
                <dt>Velg abonnement: </dt>
                <dd>
                    <select name="package" id="package" onchange="changeValues();">
                        <option data-pkg="1" value="99_kr_plan"<?php echo ($cancel_sub_info->subscription_plan == '99_kr_plan'?' selected':'')?>>99 kr - 6 mnd binding</option>
                        <option data-pkg="2" value="149_kr_plan"<?php echo ($cancel_sub_info->subscription_plan == '149_kr_plan'?' selected':'')?>>149 kr pr mnd - 3 mnd binding</option>
                        <option data-pkg="3" value="199_kr_plan"<?php echo ($cancel_sub_info->subscription_plan == '199_kr_plan'?' selected':'')?>>199 kr pr mnd - ingen binding</option>
                    </select>
                </dd>
                <div class="clear"></div>
                <dt>Navn: </dt>
                <dd><?php echo $user_info->first_name.' '.$user_info->last_name; ?></dd>
                <div class="clear"></div>
                <dt>E-post: </dt>
                <dd><?php echo $user_info->user_email; ?></dd>
                <div class="clear"></div>
                <?php
                echo ('<span class="99_kr_plan plan"><dd>Bindingstid: 6 månder</dd><div class="clear"></div><dd>Månedspris: 99 kr</dd></span>');
                $pkg = 1;
                ?>
                <?php
                echo ('<span class="149_kr_plan plan"><dd>Bindingstid: 3 månder</dd><div class="clear"></div><dd>Månedspris: 149 kr</dd></span>');
                $pkg = 2;
                ?>
                <?php
                echo ('<span class="199_kr_plan plan"><dd>Bindingstid: 1 månder</dd><div class="clear"></div><dd>Månedspris: 199 kr</dd></span>');
                $pkg = 3;
                ?>
            </dl>
            <input type="hidden" name="step" value="4">
            <input type="hidden" name="first_name" value="<?php echo $user_info->first_name; ?>" />
            <input type="hidden" name="last_name" value="<?php echo $user_info->last_name; ?>" />
            <input name="email" type="hidden" value="<?php echo $user_info->user_email; ?>" />
            <input name="retype_email" type="hidden" value="<?php echo $user_info->user_email; ?>" />
            <input type="hidden" name="save" value="subscribe" />
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
                <div class="checkbox_text"><?php echo 'Jeg har lest kjøpsvilkårene (<a href="/kjopsvilkar" class="visa_links" target="_blank">her</a>). Betalingen behandles via betalingsløsning Braintree. Brukernavn og passord sendes på epost.';?></div>
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
<?php /*<div id="payment_box">
	<div class="confirm_information">
		<h2 class="heading" style="font-size:1.2em;">Produkt: Mattevideo abonnement</h2>
		<dl>
			<dt>Navn:</dt>
			<dd><?php echo $user_info->first_name.' '.$user_info->last_name; ?></dd>
			<?php /*?><dt>Mobilnummer:</dt>
			<dd><?php echo $user_info->phone; ?></dd><?php
			<dt>E-post:</dt>
			<dd><?php echo $user_info->user_email; ?></dd>
			<dt>Beløp:</dt>
			<dd>99 kr</dd>
		</dl>
        <form method="post" action="?page_type=success">
        	<input type="hidden" name="step" value="4">
            <input type="hidden" name="first_name" value="<?php echo $user_info->first_name; ?>">
            <input type="hidden" name="last_name" value="<?php echo $user_info->last_name; ?>">
        	<input type="hidden" name="save" value="subscribe">
            <input type="hidden" name="user_id" value="<?php echo $user_info->id; ?>">
			
            
            <div class="visa_bg">
                <div class="visa_form">
                    <h2 class="heading">Betalingsinformasjon</h2>
                    <p class="white">KORTNUMMER</p>
                    <input type="text" size="20" autocomplete="off" data-encrypted-name="number" name="number" class="large" />
                    <div class="visa_form_tom">
                        <p class="white">GYLDIG T.O.M</p>
                        <?php echo monthDropdown("month");?>
                        <select data-encrypted-name="exp-year" name="exp-year" class="small">
						<?php 
                    
                        for($i=date('Y'); $i<=(date('Y')+11); $i++)
                         {
                          ?>
                    
                         	<option value="<?php echo $i;?>"><?php echo $i;?></option>
                        <?php
                            }
                            ?> 
                     </select> 
                    </div>
                    <div class="visa_form_cvc" style="position: relative;">
                        <p class="white">CVC KODE<img id="questionMark" src="<?php echo WP_PLUGIN_URL . "/braintree-payment/" ?>images/140422-BT-cvc-question.png" alt=""></p>
                        <input type="text" size="4" autocomplete="off" data-encrypted-name="cvv" name="cvv" class="small" />
                        <div id="cvv-info-div">
                            <img src="<?php echo WP_PLUGIN_URL . "/braintree-payment/" ?>images/140422-cvc-kode.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
            	<input type="checkbox" name="terms" id="terms"  />
                <label for="terms">Jeg har lest kjøpsvilkårene og Privacy Policy <a href="javascript:void(0)" id="read-more" > [les her]</a><a href="javascript:void(0)" id="read-less">[lukk tekst]</a></label>
                <div style="clear:both"></div>
                <div id="read-more-div" style="display: none;">
                	<p><strong>100% fornøyd garanti.</strong> Trygghet er viktig, og derfor tilbyr vi 100% fornøyd garanti. Om du har gjennomført et kjøp og ønsker refusjon, kan beløpet refunderes om du avbestiller via epost <a href="mailto:kontakt@mattevideo.no">kontakt@mattevideo.no</a> innen 24 timer fra bestilling.</p>
                    <p><strong>Kjøpsvilkår.</strong> Alle priser er i NOK og inkluderer MVA. Prisen er 99 kr pr mnd, og trekkes fra konto hver 30. dag fra og med dagen du blir medlem. Betalingen trekkes så lenge ditt abonnent er aktivert, og det er dekning på konto. Det er ingen bindingstid, og du kan når som helst avslutte ditt abonnement. Du avslutter ditt abonnement ved å logge inn på brukerinnstillingene på mattevideo.no. Tilgang til nettsiden gjelder for en enkelt person. Deling av tilgangen med andre personer kan føre til at tjenesten blir avstengt. Det må ikke kopieres fra dette verket i strid  med åndsverksloven. Vilkårene omfatter alt av betalt innhold på nettsiden <a href="http://www.mattevideo.no">mattevideo.no</a>.</p>
                    <p><strong>Privacy Policy.</strong> Mattevideo.no lagerer brukerdata som navn, epost og tlf for bruk i abonnementsordningen. Disse dataene lagres konfidensielt, sikkert, og deles ikke med tredje parts aktører.</p>
                </div>
            </div>
            <div class="formBtn" style="margin:0;">
				<input type="submit" value="Opprett abonnement" />
            	<div style="clear:both"></div>
            </div>
            <div style="clear:both"></div>
            <div class="small_text">
            	<p>Brukernavn og passord sendes umiddelbart på e-post</p>
            </div>
            
            
		</form>	
        <script src="https://js.braintreegateway.com/v1/braintree.js"></script>
        <script>
          var braintree = Braintree.create("<?php echo $cse_key ?>");
          braintree.onSubmitEncryptForm('braintree-payment-form');
        </script>
	</div>
</div>*/?>
<script type="text/javascript">
    jQuery(window).ready(function(){
        jQuery(".large").mask("9999 9999 9999 9999");
        changeValues();
    });
    function changeValues(){
        jQuery('span.plan').hide();
        jQuery('.'+jQuery('#package option:selected').val()).show();
        jQuery('#package').parents('form:first').attr('action', '?pkg='+jQuery('#package option:selected').attr('data-pkg')+'&plan='+jQuery('#package option:selected').val());
    }
</script>