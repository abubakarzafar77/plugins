<?php
if (is_user_logged_in()) {
    if (strpos($_SERVER['HTTP_REFERER'], 'logginn') > 0 && strpos($_SERVER['HTTP_REFERER'], '?') <= 0 && !count($_GET))
    {
        wp_redirect($_SERVER['HTTP_REFERER'] . '?logg_inn=success');
    }
} ?>
<?php if(isset($_REQUEST['pkg'])):
    # ALTER TABLE  `wptest_braintree_users_subscriptions` ADD  `subscription_plan` VARCHAR( 20 ) NOT NULL DEFAULT  'mattevideo' AFTER  `subscription_id` ;
 ?>
<?php $data = $responce['data'];?>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> 
<div class="register_content">
    <form method="post" action="" id="confirm-data">
        <?php $text = '';?>
        <?php if($_REQUEST['pkg'] == 1){
            $text = '<h5>Bindingstid: 6 månder</h5><h5>Månedspris: 99 kr</h5>';
        ?>
            <input type="hidden" name="package" id="package" value="99_kr_plan">
        <?php }else if($_REQUEST['pkg'] == 2){
            $text = '<h5>Bindingstid: 3 månder</h5><h5>Månedspris: 149 kr</h5>';
        ?>
            <input type="hidden" name="package" id="package" value="149_kr_plan">
        <?php }else if($_REQUEST['pkg'] == 3){
            $text = '<h5>Bindingstid: 1 månder</h5><h5>Månedspris: 199 kr</h5>';
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
                <div class="checkbox_text">Jeg har lest kjøpsvilkårene (<a href="/kjopsvilkar-2" class="visa_links" target="_blank">her</a>). Betalingen behandles via betalingsløsning Braintree. Brukernavn og passord sendes på epost.</div>
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
<?php if(!isset($_REQUEST['pkg'])):?>
    <div class="main-bogy-bg-image"></div>
    <div class=p-oss-main>
 
        <?php if(!is_user_logged_in()):?>
            <div class=p-form-box>
                <div class="bottom-tag">
                    <a href="/logginn">
                        <img src="/wp-content/themes/Mattevideo4/images/Prisklistremerke-logginnpage.png" alt="price tag">
                    </a>
                </div>
                <div class="col-md-6 login">
                    <h3 class=form-heading-bx>Logg inn</h3>
                    <form onsubmit="validateLogin(jQuery('#login-form'), 'login');return false;" id="login-form" autocomplete="false">
                        <p class="status" style="display: none;"><img src="/wp-content/plugins/braintree-payment/images/ajaxloader.gif"></p>
                        <input class="e-post" name="m_brukernavn" id="m_brukernavn" placeholder="Brukernavn (epost)">
                        <div class=p-pass-row>
                            <input type="password" class="p-pass" placeholder="Passord" name="m_passord" id="m_passord" />
                            <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
                            <div class="p-forgetpass" onclick="showForgot(jQuery(this));">Glemt passord?</div>
                        </div>
                        <div class="p-btn-ged">
                            <input type="submit" name="login" id="login" value="Logg inn" />
                        </div>
                    </form>
                </div>
                <div class="col-md-6 forgotpassword" style="display: none">
                    <h3 class=form-heading-bx>Glemt passord</h3>
                    <form onsubmit="validateLogin(jQuery('#forgotpassword-form'), 'forgot');return false;" id="forgotpassword-form" autocomplete="false">
                        <p class="status" style="display: none;"><img src="/wp-content/plugins/braintree-payment/images/ajaxloader.gif"></p>
                        <div class=p-pass-row>
                            <input class="e-post forgot" name="email" id="email" placeholder="Epost">
                            <?php wp_nonce_field( 'ajax-login-nonce', 'fpassword' ); ?>
                            <div class="p-forgetpass" onclick="showLogin();">Tilbake til innlogging?</div>
                        </div>
                        <div class="p-btn-ged forgot">
                            <input type="submit" name="forgotpassword" id="forgotpassword" value="Send nytt passord på epost" />
                        </div>
                    </form>
                </div>
                <div class=col-md-6>
                    <h3 class=form-heading-bx>Bli mattevideobruker</h3>
                    <div>
                        <form>
                            <div class=p-dropdown>
                                <a class="btn btn-default btn-select btn-select-light">
                                    <input type=hidden class=btn-select-input name=""> <span class=btn-select-value>99 kr pr måned - 6 månders bindingstid</span> <span class="btn-select-arrow fa"><img src="/wp-content/uploads/2016/03/arrow-down.png"/></span>
                                    <ul>
                                        <li data-package="1">99 kr pr måned - 6 månders bindingstid</li>
                                        <li data-package="2">149 kr pr måned - 3 månders bindingstid</li>
                                        <li data-package="3">199 kr pr måned - 1 månder bindingstid</li>
                                    </ul>
                                </a>
                            </div>
                            <div class=p-btn-ged><a href="javascript://" onclick="gotoNext(jQuery(this));">Gå videre</a>
                            </div>
                        </form>
                        <ul class=p-listing-rw>
                            <li>Alle abonnementene gir full tilgang til alt vårt innhold.
                            <li>Brukernavn og passord sendes umiddelbart på epost.
                            <li>100% fornøyd garanti. Kjøpsvilkår finner du <a href=#>her</a>
                        </ul>
                    </div>
                </div>
                <div class=clearfix></div>
            </div>
        <?php endif;?>
        <div class="a_banner">
            <img src="/wp-content/plugins/braintree-payment/images/222.png" alt="">
        </div>
		<div class="os-content-box">
		     <?php
			 	$page_text = json_decode(file_get_contents( dirname(__FILE__).'/../login.json' ), true);
			 	echo (isset($page_text['html'])?stripslashes($page_text['html']):'');
			 ?>
			 <div class="clr"></div>		

		</div>
		<div class="bullet-points">
		    <div class="bullet-image"> <div class="os-thumb lcd-img"><img src="/wp-content/plugins/braintree-payment/images/oss.jpg"></div></div>
			<div class="bullet-desc">
			   <ul>
			     <li><span>Dette var genialt, utrolig dyktig lærer! Takk for hjelpen!</span></li>
				 <li><span>Utrolig bra side! Til og med datteren min på 9,5 skjønner det!:)</span></li>
				 <li><span>Mattevideo har vært uvurderlig for meg dette skoleåret og har hjulpet
  meg å få en bra karakter på eksamen! Fem stjerner fra meg :)</span></li>
			   </ul>
			</div>
		</div>
<style>

div#content{padding:0 !important;margin-bottom:0 !important;}
.container{display:none;}
.default.container{display:block;}
.white-section.white_section_post{display:none;}
.space.site-footer{width:960px;}

</style>	
	
<?php endif;?>