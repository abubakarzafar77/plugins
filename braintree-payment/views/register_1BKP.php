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
                        <p class=p-detail-text><?php echo utf8_encode('For å lykkes med et mattekurs kreves ca 120 timer med egenstudier. Vi i mattevideo har laget et verktøy som gjør at du kan bruke denne tiden best mulig. Det handler om å få bedre læringsopplevelser, lære raskere og oppnå bedre resultater.');?></div>
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
                            <input type="submit" name="forgotpassword" id="forgotpassword" value="<?php echo utf8_encode('Send nytt passord på epost');?>" />
                        </div>
                    </form>
                </div>
                <div class=col-md-6>
                    <h3 class=form-heading-bx>Bli mattevideobruker</h3>
                    <div>
                        <form>
                            <div class=p-dropdown>
                                <a class="btn btn-default btn-select btn-select-light">
                                    <input type=hidden class=btn-select-input name=""> <span class=btn-select-value><?php echo utf8_encode('99 kr pr måned - 6 månders bindingstid');?></span> <span class="btn-select-arrow fa"><img src="/wp-content/plugins/braintree-payment/images/arrow-down.png"/></span>
                                    <ul>
                                        <li data-package="1"><?php echo utf8_encode('99 kr pr måned - 6 månders bindingstid');?></li>
                                        <li data-package="2"><?php echo utf8_encode('149 kr pr måned - 3 månders bindingstid');?></li>
                                        <li data-package="3"><?php echo utf8_encode('199 kr pr måned - 1 månder bindingstid');?></li>
                                    </ul>
                                </a>
                            </div>
                            <div class=p-btn-ged><a href="javascript://" onclick="gotoNext(jQuery(this));"><?php echo utf8_encode('Gå videre');?></a>
                            </div>
                        </form>
                        <ul class=p-listing-rw>
                            <li><?php echo utf8_encode('Alle abonnementene gir full tilgang til alt vårt innhold.');?></li>
                            <li><?php echo utf8_encode('Brukernavn og passord sendes umiddelbart på epost.');?></li>
                            <li><?php echo utf8_encode('100% fornøyd garanti. Kjøpsvilkår finner du <a href="/kjopsvilkar" target="_blank">her</a>');?></li>
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
                        <h3><?php echo utf8_encode('"Det er så kjedelig!!"');?></h3>
                        <p><?php echo utf8_encode('Er læreren din kjedelig? Ikke vår! <br />Læreren vår Arne har over 17 års erfaring fra klasserommet og vet akkurat hva som skal til for å motiverer deg til å lykkes.');?></p>
                    </div>
                </div>
            </div>
            <div class="custm-right-box pl30">
                <div class="p-right-col-main p-col2">
                    <div class="p-right-inner half-box">
                        <div class="chracter6 teacher"><img src="/wp-content/plugins/braintree-payment/images/teacher.png">
                        </div>
                        <div class="p-inner-text teacher-bullets">
                            <div class=p-headin-b><?php echo utf8_encode('Få en lærer som motiverer');?></div>
                            <ul class=p-listing-points>
                                <li><span><?php echo utf8_encode('Flink til å forklare');?></span></li>
                                <li><span><?php echo utf8_encode('17 års erfaring');?></span></li>
                                <li><span><?php echo utf8_encode('Glimt i øyet');?></span></li>
                            </ul>
                        </div>
                        <div class=less-more-btn><a href="javascript://">Les<br>mer</a>
                        </div>
                    </div>
                </div>
                <div class=p-right-col-main2>
                    <div class="p-right-inner half-box">
                        <div class=p-inner-text2>
                            <p><?php echo utf8_encode('Vår faglærer heter Arne Hovland og har utdannelse fra Universitet i Oslo innenfor matematikk, fysikk og pedagogikk, med hovedfag på høyere nivå.');?></p>
                            <p><?php echo utf8_encode('Arne har jobbet som lærer i 17 år, og har erfaring fra flere av norges største videregående skoler. Arne ble hentet inn til mattevideo fordi han er genuint opptatt av å motivere, og har gjennom mange år fått svært høye score på elevundersøkelser.');?></p>
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
                        <h3><?php echo utf8_encode('"Det er så mye å lære!"');?></h3>
                        <p><?php echo utf8_encode('Synes du pensum kan være tungt? <br />Vi har skapt et inspirerende og effektivt læringsunivers med synlig lærer og tavle, slik at du lærer mer og beholder fremdriften.');?></div>
                </div>
            </div>
            <div class="custm-right-box pl30 lae-forte">
                <div class="p-right-col-main p-col4">
                    <div class=p-right-inner>
                        <div class="chracter6 whiteboard"><img src="/wp-content/plugins/braintree-payment/images/whiteboard.png">
                        </div>
                        <div class=p-inner-text>
                            <div class=p-headin-b><?php echo utf8_encode('Lær fortere med riktig undervisningsteknikk');?></div>
                            <ul class="p-listing-points greybullets">
                                <li><span>Klassisk tavleundervisning</span></li>
                                <li><span><?php echo utf8_encode('Synlig lærer, tankekart og håndskrift');?></span></li>
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
                            <p><?php echo utf8_encode('Synlig lærer gir best læringsopplevelse. Studier av læring viser at 60-70% av all informasjon mellom mennesker utveksles med kroppsspråk og ansiktsuttrykk.');?></p>
                            <p><?php echo utf8_encode('Tavlen bygger på læring med penn-og papirmetoden, som aktiverer flest av kroppen sanser som øye-hånd koordinasjon, språksenteret og det visuelle sentret i hjernen.');?></p>
                            <p><?php echo utf8_encode('Klassisk tavleundervisning gir størst læringsutbytte.');?></p>
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
                        <h3><?php echo utf8_encode('"Åh nei!! Jeg leste feil til prøven!"');?></h3>
                        <p><?php echo utf8_encode('Ikke all informasjon du finner på internett er like seriøs. Hos mattevideo er du trygg på at alt er faglig kvalitetssikret, slik at du stiller med riktig kunnskap til prøven.');?></div>
                </div>
            </div>
            <div class="custm-right-box pl30 paper-box">
                <div class="p-right-col-main p-col6">
                    <div class=p-right-inner>
                        <div class="chracter6 chracter-set1 paper"><img src="/wp-content/plugins/braintree-payment/images/paper.png">
                        </div>
                        <div class=p-inner-text>
                            <div class="p-headin-b pl30 paperheading"><?php echo utf8_encode('Vær trygg på at du lærer det rette');?></div>
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
                            <p><?php echo utf8_encode('Når vi legger ut et kurs dekker vi alt. Vi filmer pensum på to måter.');?></p>
                            <p><?php echo utf8_encode('Vi filmer all teori, det vil si kunnskapen du trenger for å forstå matematikk. Og vi filmer oppgavevideoer hvor vi viser deg eksempler på hvordan du kan bruke teorien i praktiske problemstillinger. På denne måten kan du enklere skille på det som ligger bak (teori) og matematikken du møter i hverdagen (oppgaver)');?></p>
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
                        <p><?php echo utf8_encode('Ikke kast bort tiden du har satt av til lekser på å surfe rundt på google. Mattevideo gjør det enkelt å fokusere på tema du skal lære.');?></div>
                </div>
            </div>
            <div class="custm-right-box pl30 lightbulb-box">
                <div class="p-right-col-main p-col8">
                    <div class="p-right-inner half-box bulb-half-box">
                        <div class="chracter6 chracterset2 lightbulb"><img src="/wp-content/plugins/braintree-payment/images/lightbulb.png">
                        </div>
                        <div class="p-inner-text bulbinner">
                            <div class="p-headin-b pl30 lightbulb-header"><?php echo utf8_encode('Få fullstendig oversikt');?></div>
                            <ul class="p-listing-points greybullets">
                                <li><span><?php echo utf8_encode('Lærebokveiviser og temasøk');?></span></li>
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
                            <p><?php echo utf8_encode('Som student må man være bevisst hvordan man bruker tid og energi. Vi har gjort vårt ytterste for at du kan komme igang med læringen fra første sekund du setter deg ned.');?></p>
                            <p><?php echo utf8_encode('Lærebokveiviser og temasøk får deg raskt til rett tema. Beskrivende tekster under hver video med matematiskeformler og illustrasjoner gir en umiddelbart forståelse av hva de forskjellige videoen handler om.');?></p>
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
                        <p><?php echo utf8_encode('Det er fort gjort å miste oversikten, eller havne bak de andre i klassen. Hos oss kan du pause, spole, og se videoene så mange ganger du vil.');?></div>
                </div>
            </div>
            <div class="custm-right-box pl30">
                <div class="p-right-col-main p-col10">
                    <div class="p-right-inner half-box yoga-half-box">
                        <div class="chracter6 chracterset3 yoga"><img src="/wp-content/plugins/braintree-payment/images/yoga.png">
                        </div>
                        <div class="p-inner-text yogainner">
                            <div class=p-headin-b><?php echo utf8_encode('Få ro til å lære i eget tempo');?></div>
                            <ul class="p-listing-points greybullets">
                                <li><span>Spol, pause og se om igjen</span></li>
                                <li><span><?php echo utf8_encode('Få tips om studieteknikk');?></span></li>
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
                            <p><?php echo utf8_encode('Det er mange fordeler ved å ha sin egen lærer tilgjengelig på videoer daøgnet rundt. Med videoer kan du repetere de vanskelige temane så mange ganger du trenger, og du kan spol og pause underveis. TIl hvert tema får du også tips om studieteknikk.');?></p>
                            <p><?php echo utf8_encode('Med mattevideo i ryggen er du trygg på at du er godt rustet til delprøver og eksamen gjennom skoleåret.');?></p>
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