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
    <div class="register_content">
        <form method="post" action="" id="confirm-data">
            <?php $text = '';?>
            <?php if($_REQUEST['pkg'] == 1){
                $text = utf8_encode('<h5>Bindingstid: 6 m�nder</h5><h5>M�nedspris: 99 kr</h5>');
                ?>
                <input type="hidden" name="package" id="package" value="99_kr_plan">
            <?php }else if($_REQUEST['pkg'] == 2){
                $text = utf8_encode('<h5>Bindingstid: 3 m�nder</h5><h5>M�nedspris: 149 kr</h5>');
                ?>
                <input type="hidden" name="package" id="package" value="149_kr_plan">
            <?php }else if($_REQUEST['pkg'] == 3){
                $text = utf8_encode('<h5>Bindingstid: 1 m�nder</h5><h5>M�nedspris: 199 kr</h5>');
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
                    <div class="checkbox_text"><?php echo utf8_encode('Jeg har lest kj�psvilk�rene (<a href="/kjopsvilkar" class="visa_links" target="_blank">her</a>). Betalingen behandles via betalingsl�sning Braintree. Brukernavn og passord sendes p� epost.');?></div>
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
        <div class=p-upper-col>
            <div class=col-md-5><img src="/wp-content/plugins/braintree-payment/images/desktop.jpg" />
            </div>
            <div class=col-md-7>
                <div class=p-slug-main>
                    <div class=p-slug-inner>
                        <h2 class=p-heading-oss><span>120 timer</span><span class="large">egenstudier</span><br>- hvordan bruke tiden best mulig?</h2>
                        <p class=p-detail-text><?php echo utf8_encode('For � lykkes med et mattekurs kreves ca 120 timer med egenstudier. Vi i mattevideo har laget et verkt�y som gj�r at du kan bruke denne tiden best mulig. Det handler om � f� bedre l�ringsopplevelser, l�re raskere og oppn� bedre resultater.');?></div>
                </div>
            </div>
            <div class=clearfix></div>
        </div>
        <?php if(!is_user_logged_in()):?>
            <div class=p-form-box>
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
                            <input type="submit" name="forgotpassword" id="forgotpassword" value="<?php echo utf8_encode('Send nytt passord p� epost');?>" />
                        </div>
                    </form>
                </div>
                <div class=col-md-6>
                    <h3 class=form-heading-bx>Bli mattevideobruker</h3>
                    <div>
                        <form>
                            <div class=p-dropdown>
                                <a class="btn btn-default btn-select btn-select-light">
                                    <input type=hidden class=btn-select-input name=""> <span class=btn-select-value><?php echo utf8_encode('99 kr pr m�ned - 6 m�nders bindingstid');?></span> <span class="btn-select-arrow fa"><img src="/wp-content/plugins/braintree-payment/images/arrow-down.png"/></span>
                                    <ul>
                                        <li data-package="1"><?php echo utf8_encode('99 kr pr m�ned - 6 m�nders bindingstid');?></li>
                                        <li data-package="2"><?php echo utf8_encode('149 kr pr m�ned - 3 m�nders bindingstid');?></li>
                                        <li data-package="3"><?php echo utf8_encode('199 kr pr m�ned - 1 m�nder bindingstid');?></li>
                                    </ul>
                                </a>
                            </div>
                            <div class=p-btn-ged><a href="javascript://" onclick="gotoNext(jQuery(this));"><?php echo utf8_encode('G� videre');?></a>
                            </div>
                        </form>
                        <ul class=p-listing-rw>
                            <li><?php echo utf8_encode('Alle abonnementene gir full tilgang til alt v�rt innhold.');?></li>
                            <li><?php echo utf8_encode('Brukernavn og passord sendes umiddelbart p� epost.');?></li>
                            <li><?php echo utf8_encode('100% forn�yd garanti. Kj�psvilk�r finner du <a href="/kjopsvilkar" target="_blank">her</a>');?></li>
                        </ul>
                    </div>
                </div>
                <div class=clearfix></div>
            </div>
        <?php endif;?>
        <div class="p-lower-col" id="header">
            <div class="p-custom-left col-left-1">
                <div class="p-lef-col-main p-first-col">
                    <figure><img src="/wp-content/plugins/braintree-payment/images/chracter1.png" class=img-responsive>
                    </figure>
                    <div class=p-chrac-detail>
                        <h3><?php echo utf8_encode('"Det er s� kjedelig!!"');?></h3>
                        <p><?php echo utf8_encode('Er l�reren din kjedelig? Ikke v�r! <br />L�reren v�r Arne har over 17 �rs erfaring fra klasserommet og vet akkurat hva som skal til for � motiverer deg til � lykkes.');?></p>
                    </div>
                </div>
            </div>
            <div class="custm-right-box pl30">
                <div class="p-right-col-main p-col2">
                    <div class="p-right-inner half-box">
                        <div class="chracter6 teacher"><img src="/wp-content/plugins/braintree-payment/images/teacher.png">
                        </div>
                        <div class="p-inner-text teacher-bullets">
                            <div class=p-headin-b><?php echo utf8_encode('F� en l�rer som motiverer');?></div>
                            <ul class=p-listing-points>
                                <li><span><?php echo utf8_encode('Flink til � forklare');?></span></li>
                                <li><span><?php echo utf8_encode('17 �rs erfaring');?></span></li>
                                <li><span><?php echo utf8_encode('Glimt i �yet');?></span></li>
                            </ul>
                        </div>
                        <div class=less-more-btn><a href="javascript://">Les<br>mer</a>
                        </div>
                    </div>
                </div>
                <div class=p-right-col-main2>
                    <div class="p-right-inner half-box">
                        <div class=p-inner-text2>
                            <p><?php echo utf8_encode('V�r fagl�rer heter Arne Hovland og har utdannelse fra Universitet i Oslo innenfor matematikk, fysikk og pedagogikk, med hovedfag p� h�yere niv�.');?></p>
                            <p><?php echo utf8_encode('Arne har jobbet som l�rer i 17 �r, og har erfaring fra flere av norges st�rste videreg�ende skoler. Arne ble hentet inn til mattevideo fordi han er genuint opptatt av � motivere, og har gjennom mange �r f�tt sv�rt h�ye score p� elevunders�kelser.');?></p>
                        </div>
                        <div class="less-more-btn luk"><a href="javascript://"><img src="/wp-content/plugins/braintree-payment/images/arrow-left.png"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-custom-left col-left-2">
                <div class="p-lef-col-main p-col3">
                    <figure><img src="/wp-content/plugins/braintree-payment/images/chracter2.png" class=img-responsive>
                    </figure>
                    <div class=p-chrac-detail>
                        <h3><?php echo utf8_encode('"Det er s� mye � l�re!"');?></h3>
                        <p><?php echo utf8_encode('Synes du pensum kan v�re tungt? <br />Vi har skapt et inspirerende og effektivt l�ringsunivers med synlig l�rer og tavle, slik at du l�rer mer og beholder fremdriften.');?></div>
                </div>
            </div>
            <div class="custm-right-box pl30 lae-forte">
                <div class="p-right-col-main p-col4">
                    <div class=p-right-inner>
                        <div class="chracter6 whiteboard"><img src="/wp-content/plugins/braintree-payment/images/whiteboard.png">
                        </div>
                        <div class=p-inner-text>
                            <div class=p-headin-b><?php echo utf8_encode('L�r fortere med riktig undervisningsteknikk');?></div>
                            <ul class="p-listing-points greybullets">
                                <li><span>Klassisk tavleundervisning</span></li>
                                <li><span><?php echo utf8_encode('Synlig l�rer, tankekart og h�ndskrift');?></span></li>
                                <li><span>Laget av et profesjonelt filmteam</span></li>
                            </ul>
                        </div>
                        <div class=less-more-btn><a href="javascript://">Les<br>mer</a>
                        </div>
                    </div>
                </div>
                <div class=p-right-col-main2>
                    <div class=p-right-inner>
                        <div class=p-inner-text2>
                            <p><?php echo utf8_encode('Synlig l�rer gir best l�ringsopplevelse. Studier av l�ring viser at 60-70% av all informasjon mellom mennesker utveksles med kroppsspr�k og ansiktsuttrykk.');?></p>
                            <p><?php echo utf8_encode('Tavlen bygger p� l�ring med penn-og papirmetoden, som aktiverer flest av kroppen sanser som �ye-h�nd koordinasjon, spr�ksenteret og det visuelle sentret i hjernen.');?></p>
                            <p><?php echo utf8_encode('Klassisk tavleundervisning gir st�rst l�ringsutbytte.');?></p>
                        </div>
                        <div class="less-more-btn luk"><a href="javascript://"><img src="/wp-content/plugins/braintree-payment/images/arrow-left.png"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-custom-left col-left-3">
                <div class="p-lef-col-main p-col5">
                    <figure><img src="/wp-content/plugins/braintree-payment/images/chracter3.png" class=img-responsive>
                    </figure>
                    <div class=p-chrac-detail>
                        <h3><?php echo utf8_encode('"�h nei!! Jeg leste feil til pr�ven!"');?></h3>
                        <p><?php echo utf8_encode('Ikke all informasjon du finner p� internett er like seri�s. Hos mattevideo er du trygg p� at alt er faglig kvalitetssikret, slik at du stiller med riktig kunnskap til pr�ven.');?></div>
                </div>
            </div>
            <div class="custm-right-box pl30 paper-box">
                <div class="p-right-col-main p-col6">
                    <div class=p-right-inner>
                        <div class="chracter6 chracter-set1 paper"><img src="/wp-content/plugins/braintree-payment/images/paper.png">
                        </div>
                        <div class=p-inner-text>
                            <div class="p-headin-b pl30 paperheading"><?php echo utf8_encode('V�r trygg p� at du l�rer det rette');?></div>
                            <ul class=p-listing-points>
                                <li><span>Faglig kvalitetssikring - pensum fra A til</span>
                                <li><span>Gjennomarbeidede teorivideoer</span>
                                <li><span>Oppgaver rettet mot eksamen</span>
                            </ul>
                        </div>
                        <div class=less-more-btn><a href="javascript://">Les<br>mer</a>
                        </div>
                    </div>
                </div>
                <div class=p-right-col-main2>
                    <div class=p-right-inner>
                        <div class=p-inner-text2>
                            <p><?php echo utf8_encode('N�r vi legger ut et kurs dekker vi alt. Vi filmer pensum p� to m�ter.');?></p>
                            <p><?php echo utf8_encode('Vi filmer all teori, det vil si kunnskapen du trenger for � forst� matematikk. Og vi filmer oppgavevideoer hvor vi viser deg eksempler p� hvordan du kan bruke teorien i praktiske problemstillinger. P� denne m�ten kan du enklere skille p� det som ligger bak (teori) og matematikken du m�ter i hverdagen (oppgaver)');?></p>
                        </div>
                        <div class="less-more-btn luk"><a href="javascript://"><img src="/wp-content/plugins/braintree-payment/images/arrow-left.png"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-custom-left col-left-4">
                <div class="p-lef-col-main p-col7">
                    <figure><img src="/wp-content/plugins/braintree-payment/images/chracter4.png" class=img-responsive>
                    </figure>
                    <div class=p-chrac-detail>
                        <h3><?php echo utf8_encode('"Jeg kommer aldri i gang!"');?></h3>
                        <p><?php echo utf8_encode('Ikke kast bort tiden du har satt av til lekser p� � surfe rundt p� google. Mattevideo gj�r det enkelt � fokusere p� tema du skal l�re.');?></div>
                </div>
            </div>
            <div class="custm-right-box pl30 lightbulb-box">
                <div class="p-right-col-main p-col8">
                    <div class="p-right-inner half-box bulb-half-box">
                        <div class="chracter6 chracterset2 lightbulb"><img src="/wp-content/plugins/braintree-payment/images/lightbulb.png">
                        </div>
                        <div class="p-inner-text bulbinner">
                            <div class="p-headin-b pl30 lightbulb-header"><?php echo utf8_encode('F� fullstendig oversikt');?></div>
                            <ul class="p-listing-points greybullets">
                                <li><span><?php echo utf8_encode('L�rebokveiviser og temas�k');?></span></li>
                                <li><span>Matematiske formler</span></li>
                                <li><span>Illustrasjoner</span></li>
                            </ul>
                        </div>
                        <div class=less-more-btn><a href="javascript://">Les<br>mer</a>
                        </div>
                    </div>
                </div>
                <div class=p-right-col-main2>
                    <div class="p-right-inner half-box">
                        <div class=p-inner-text2>
                            <p><?php echo utf8_encode('Som student m� man v�re bevisst hvordan man bruker tid og energi. Vi har gjort v�rt ytterste for at du kan komme igang med l�ringen fra f�rste sekund du setter deg ned.');?></p>
                            <p><?php echo utf8_encode('L�rebokveiviser og temas�k f�r deg raskt til rett tema. Beskrivende tekster under hver video med matematiskeformler og illustrasjoner gir en umiddelbart forst�else av hva de forskjellige videoen handler om.');?></p>
                        </div>
                        <div class="less-more-btn luk"><a href="javascript://"><img src="/wp-content/plugins/braintree-payment/images/arrow-left.png"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-custom-left col-left-5">
                <div class="p-lef-col-main p-col9">
                    <figure><img src="/wp-content/plugins/braintree-payment/images/chracter5.png" class=img-responsive>
                    </figure>
                    <div class=p-chrac-detail>
                        <h3>"Syykt stressa!"</h3>
                        <p><?php echo utf8_encode('Det er fort gjort � miste oversikten, eller havne bak de andre i klassen. Hos oss kan du pause, spole, og se videoene s� mange ganger du vil.');?></div>
                </div>
            </div>
            <div class="custm-right-box pl30">
                <div class="p-right-col-main p-col10">
                    <div class="p-right-inner half-box yoga-half-box">
                        <div class="chracter6 chracterset3 yoga"><img src="/wp-content/plugins/braintree-payment/images/yoga.png">
                        </div>
                        <div class="p-inner-text yogainner">
                            <div class=p-headin-b><?php echo utf8_encode('F� ro til � l�re i eget tempo');?></div>
                            <ul class="p-listing-points greybullets">
                                <li><span>Spol, pause og se om igjen</span></li>
                                <li><span><?php echo utf8_encode('F� tips om studieteknikk');?></span></li>
                                <li><span>Alt innhold lett tilgjengelig</span></li>
                            </ul>
                        </div>
                        <div class=less-more-btn><a href="javascript://">Les<br>mer</a>
                        </div>
                    </div>
                </div>
                <div class=p-right-col-main2>
                    <div class="p-right-inner half-box">
                        <div class=p-inner-text2>
                            <p><?php echo utf8_encode('Det er mange fordeler ved � ha sin egen l�rer tilgjengelig p� videoer da�gnet rundt. Med videoer kan du repetere de vanskelige temane s� mange ganger du trenger, og du kan spol og pause underveis. TIl hvert tema f�r du ogs� tips om studieteknikk.');?></p>
                            <p><?php echo utf8_encode('Med mattevideo i ryggen er du trygg p� at du er godt rustet til delpr�ver og eksamen gjennom skole�ret.');?></p>
                        </div>
                        <div class="less-more-btn luk"><a href="javascript://"><img src="/wp-content/plugins/braintree-payment/images/arrow-left.png"></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class=clearfix></div>
        </div>
    </div>
<?php endif;?>