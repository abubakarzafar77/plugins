<?php
if (is_user_logged_in())
{


    if (strpos($_SERVER['HTTP_REFERER'], 'logg-inn') > 0 && strpos($_SERVER['HTTP_REFERER'], '?') <= 0 && !count($_GET))
    {
        wp_redirect($_SERVER['HTTP_REFERER'] . '?logg_inn=success');
    }
}
?>

<?php
$data = $responce['data'];
?>
<?php
if ($responce['status'] == 'error')
{
    ?>
    <div class="error">
        <?php echo $responce['message']; ?>
    </div>
    <?php
}
else
{
    ?>
    <div class="success">
        <?php echo $responce['message']; ?>
    </div>
    <?php
}
?>
<div id="payment_box">


    <?php
    //	show logged in view: show user or show subscription		
    if (is_user_logged_in())
    {



        $user_data = wp_get_current_user();

        if (!empty($_GET['view']) && $_GET['view'] == 'show_user')
        {
            ?>

            <div class="logged_in user_profile">

                <?php
                if ($old_user)
                {
                    $user_subscription = new MattevideoSubscriptionController($user_data->ID);
                    update_user_meta($user_data->ID, 'sbr_status', $user_subscription->getStatus());
                    ?>
                    <h2>Din profil</h2>
                    <dl class="user_data">
                        <dt>Brukernavn:</dt>
                        <dd><?php echo $user_data->user_login; ?></dd>
                        <dt>E-post:</dt>
                        <dd><?php echo $user_data->user_email; ?></dd>
                        <dt>Status:	</dt>
                        <dd><?php echo $user_subscription->getStatus(); ?></dd>
                    </dl>
                    <a href="?view=show_subscription">Endre status</a>

                <?php
                }
                else
                {
                    ?>
                    <h2>Din profil</h2>
                    <dl class="user_data">
                        <dt>Brukernavn:</dt>
                        <dd><?php echo $user_data->user_login; ?></dd>
                        <dt>E-post:</dt>
                        <dd><?php echo $user_data->user_email; ?></dd>
                        <dt>Status:	</dt>
                        <dd>
            <?php $this->show_subscription_status(); ?>
                        </dd>
                    </dl>
                    <dl class="din_profile">
                        <?php
                        if ($this->return_subscription_status() == 'Expired')
                        {
                            $this->show_unsubscribe_form_link_for_expire();
                        }
                        else
                        {
                            $this->show_unsubscribe_form_link();
                        }
                        ?>

            <?php $this->show_update_profile_link(); ?>
                        <dd>
                            <a href="<?php echo get_edit_user_link(); ?>" target="_blank">Endre passord eller brukerdetaljer</a>
                        </dd>
                    </dl>
        <?php } ?>


                <?php /* ?>&nbsp;&nbsp;<a href="?view=set_secret">Sett Secret Question</a>				<?php */ ?>

                <?php /* ?><!--  GROUP EMAIL SETTING -->
                  <br />
                  <span style='color:#000; margin:10px 0 10px 0; font-family:Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold;'>Motta e-post invitasjon for kollokvigrupper fra:</span>

                  <form action="logg-inn?view=show_user" id="save_email_setting_form" method="post">

                  <?php
                  $user_data = wp_get_current_user();
                  $user_id = $user_data->ID;

                  global $wpdb;

                  $q = "SELECT * FROM groups_email_settings  WHERE user_id = '$user_id' ";

                  $email_setting = $wpdb->get_row($q);

                  if($email_setting){

                  $settings = unserialize($email_setting->setting);

                  $main_topics = $settings['main_topics'];
                  $all_groups = $settings['all_groups'];
                  $no_groups = $settings['no_groups'];
                  }

                  ?>
                  <input type="hidden" name="save_email_setting" value="true">
                  <label style="width:100%; line-height:18px; color:#000; text-align:left;"><input style="margin-bottom: 10px; width: auto; height: auto; display: inline;" name="all_email" type="checkbox" <?php echo (isset($all_groups) && $all_groups == 1)? ' checked="checked "': '' ; ?> class="all_email" onclick='$(".email_topics_chk").prop("checked", true);$(".no_email").prop("checked", false);' > Alle grupper</label>
                  <?php
                  $main_categories = get_categories('include=341,13,3,130');
                  foreach ($main_categories as $main_cat) {
                  $name = $main_cat->name;
                  $id = $main_cat->term_id;
                  $checked = "";
                  if (isset($main_topics)) {
                  if (@in_array($id, $main_topics)) {
                  $checked = ' checked="checked" ';
                  }
                  }
                  ?>

                  <label style="width:100%; line-height:18px; color:#000; text-align:left;"><input <?php echo $checked; ?> style="margin-bottom: 10px; width: auto; height: auto; display: inline;" type="checkbox" name="email_topics[]" class="email_topics_chk" value="<?php echo $id; ?>" onclick='$(".all_email").prop("checked", false);$(".no_email").prop("checked", false);'> <?php echo $name; ?> grupper</label>
                  <?php
                  }
                  ?>
                  <label style="width:100%; line-height:18px; color:#000; text-align:left;"><input style="margin-bottom: 10px; width: auto; height: auto; display: inline;" name="no_email" <?php echo (isset($no_groups) && $no_groups == 1)? ' checked="checked "': '' ; ?> type="checkbox" class="no_email" onclick='$(".email_topics_chk").prop("checked", false);$(".all_email").prop("checked", false);' > Ingen grupper</label>
                  <a href='javascript:void(0);' onclick='$("#save_email_setting_form").submit();'>Lagre innstillinger</a>
                  </form>

                  <!--  GROUP EMAIL SETTING END --><?php */ ?>

            </div>

            <?php
        }
        else if (!empty($_GET['view']) && $_GET['view'] == 'show_subscription' &&
                get_user_meta($user_data->ID, 'sbr_status', true))
        {
            ?>


            <div class="logged_in user_profile">
                <?php
                $subscriptionStatus = 'Deactivated';
                if ($old_user)
                {
                    $user_data          = wp_get_current_user();
                    $user_subscription  = new MattevideoSubscriptionController($user_data->ID);
                    $subscriptionStatus = $user_subscription->getStatus();
                }
                ?>

                <h2>Din profil</h2>
                <?php if ($subscriptionStatus == 'Active')
                {
                    ?>
                    <p>
                        Om du ønsker å avslutte ditt abonnement 
                        gjøres det ved å følge linken under.
                    </p>
                    <p>
                        Kontakt gjerne vår kundeservice ved 
                        spørsmål, tlf 98 60 61 58
                    </p>

                    <form action="logg-inn?view=show_user" method="post" id="deactivate">

                        <input type="hidden" name="todo" value="deactivate" />
                        <input type="submit" value="Deaktiver abonnement" /> 
                    </form>

                <?php
                }
                elseif ($subscriptionStatus == 'Cancelled')
                {
                    ?>
                    <p>
                        Om du ønsker å avslutte ditt abonnement 
                        gjøres det ved å følge linken under.
                    </p>
                    <p>
                        Kontakt gjerne vår kundeservice ved 
                        spørsmål, tlf 98 60 61 58
                    </p>

                    <form action="logg-inn?view=show_user" method="post" id="activate">

                        <input type="hidden" name="todo" value="activate" />
                        <input type="submit" value="Reaktiver abonnement" /> 
                    </form>

        <?php
        }
        else if ($subscriptionStatus == 'Deactivated' || $subscriptionStatus == 'Expired')
        {
            ?>
                    <p>
                        Om du ønsker å aktivere ditt abonnement 
                        gjøres det ved å kontakte telefonnummeret under, eller sende en mail til ... .
                    </p>
                    <p>
                        Kontakt gjerne vår kundeservice ved 
                        spørsmål, tlf 98 60 61 58
                    </p>

            <?php }
            ?>

            </div>


        <?php
    }
    else if (!empty($_GET['view']) && $_GET['view'] == 'cancel_reason')
    {
        ?>
            <div class="logged_in user_profile" style="width: 290px; padding: 20px;">
                <form action="?step=1&cancel_subscription=true" method="post" id="canel_subscription_form" >
                    <h2>Endre status for abonnement</h2>
                    <p>
                        Takk for at du har brukt Mattevideo. Vennligst fyll ut feltene under før du avslutter ditt abonnement. Alle svar er anonyme.
                    </p>

                    <br>
                    <br>
                    <style>
                        .reson_radio{
                            display: inline !important;
                            margin: 10px 4px 0 0 !important;
                        }
                        .reason_txt{
                            margin: 5px auto 0 50px !important;
                        }
                    </style>
                    <div>
                        Min alder
                        <input style="display: inline-block; width: 100%;" type="text" name="studentAge" id="studentAge">
                    </div>
                    <p>Student status:</p>
                    <select id="student_status" style="height:30px;" required="true" name="reason">
                        <option selected="selected" value="">Select an option</option>
                        <option value="Elev ved vanlig vgs">Elev ved vanlig vgs</option>
                        <option value="Elev ved privatskole">Elev ved privatskole</option>
                        <option value="Privatist uten klasseromsundervisning">Privatist uten klasseromsundervisning</option>
                        <option value="Annet">Annet</option>
                    </select>
                    <br/>
                    <br/>
                    <p><input class="hidden"  style="display: inline-block; width: 100%;" cclass="reason_txt" type="text"  autocomplete="off" name="reason_txt" id="reason_txt" ></p>
                    <!--                    <div>
                                            <input class="reson_radio first" type="checkbox" name="reason" id="reason" checked="checked" autocomplete="off" >
                                            Elev ved vanlig vgs
                                        </div>
                                        <div>
                                            <input class="reson_radio first" type="checkbox" name="reason_other" id="reason_other" autocomplete="off" >
                                            Elev ved privatskole
                                        </div>-->
                    <!--<div>-->
                        <!--<input class="reson_radio first" type="checkbox" name="reason_an_other" id="reason_an_other" autocomplete="off" >-->
                    <!--Privatist uten klasseromsundervisning-->
                    <!--<br>-->
                    <!--#######QQQQ########-->
                    <!--<p><input class="reson_radio first" type="checkbox" name="otherFirst" id="otherFirst"  autocomplete="off" > Annet <input  style="display: inline-block"  disabled cclass="reason_txt" type="text"  autocomplete="off" name="reason_txt" id="reason_txt" ></p>-->
                    <!--#######QQQQ##########-->
<!--                    <p>Annet <input  style="display: inline-block; width: 100%;" cclass="reason_txt" type="text"  autocomplete="off" name="reason_txt" id="reason_txt" ></p>-->
                    <!--</div>-->
                    <br/>
                    <p>Har bruker av mattevideo vært den samme som har betalt abonnementet?</p>
                    <!--#######QQQQ###########-->

                    <!--                    <div>
                                            <input class="reson_radio second" type="checkbox" name="reason_new" id="reason_new" checked="checked" autocomplete="off" >
                                            Ja, jeg har selv brukt mattevideo og betalt selv. 
                                        </div>
                                        <div>
                                            <input class="reson_radio second" type="checkbox" name="reason_new_other" id="reason_new_other"  autocomplete="off" >
                                            Nei, en av mine foreldre eller foresatte har betalt abonnementet.
                                            <br>
                                            <p><input class="reson_radio second" type="checkbox" name="otherSec" id="otherSec" autocomplete="off" > Annet <input style="display: inline-block" class="" disabled type="text"  autocomplete="off" name="reason_txt_new" id="reason_txt_new" ></p>
                                        </div>-->
                    <!--########QQQQQ#########-->

                    <select id="q2"  style="height:30px; width: 100%;" id required="true" name="reason_new">
                        <option value="" selected="selected">Select an option</option>
                        <option value=" Ja jeg har selv brukt mattevideo og betalt selv.">Ja, jeg har selv brukt mattevideo og betalt selv.</option>
                        <option value="Nei en av mine foreldre eller foresatte har betalt abonnementet. ">Nei, en av mine foreldre eller foresatte har betalt abonnementet.</option>
                        <option value="Annet">Annet</option>
                    </select>
                    <br/>
                    <br/>
                    <p><input class="hidden" style="display: inline-block;width: 100%;" class="" type="text"  autocomplete="off" name="reason_txt_new" id="reason_txt_new" ></p>
                    <!--<p> Annet <input style="display: inline-block;width: 100%;" class="" type="text"  autocomplete="off" name="reason_txt_new" id="reason_txt_new" ></p>-->


                    <?php
                    if ($this->return_subscription_status() == 'Expired')
                    {
                        $this->show_unsubscribe_link_for_expire();
                    }
                    else
                    {
                        
                        $this->show_unsubscribe_link();
                    }
                    ?>
                    <p>Ved avbestilling slettes all betalingsinformasjon. Ved reaktivering må denne informasjonen fylles inn på nytt.</p>
                </form>
            </div>
            <?php
        }
        else
        {

            //	show logged in box	
            //	if this is a mobile user, add meta for video player
            if (get_user_meta($user_data->ID, 'sbr_status', true) == '')
            {
                global $wpdb;

                $experationDate = $wpdb->get_var($wpdb->prepare("SELECT subscriptionValidToDate 
																FROM wptest_sendegaSMS_subscription 
																WHERE wp_user_id = %d", $user_data->ID
                ));
                update_user_meta($user_data->ID, 'sendega_experation_time', $experationDate);
            }
            ?>

            <div class="logged_in">
        <?php
        $user_data = wp_get_current_user();
        ?>

                <h2>Logget inn som</h2>

                <a href="?view=show_user" class="settings">
                    <img src="<?php echo plugins_url('NetAxept/img/btn-crank.png') ?>" />
                </a>

        <?php ?>
                <p><?php echo $user_data->user_login ?></p>

                <form action="" method="post" id="logout">
                    <input type="hidden" value="log_out" name="todo" />
                    <input type="submit" value="Logg ut" />
                </form>
            </div>

            <?php
        }
    }
    else
    {
        //	show log in box	
        ?>
    <?php if (!empty($_GET['view']) && $_GET['view'] == 'forgot_password')
    {
        ?>
            <div class="logg_in">
                <h1>Glemt passord</h1>
                <form action="" method="POST" id="forgot_password">
                    <input type="hidden" name="todo" value="forgot_password" />
                    <div class="payment_input">
                        <input type="text" name="epost" value="" placeholder="Epost" style="margin:0 auto 0;"/>
                        <p style="margin: 5px auto 20px; display: block; width: 175px;">
                            <a href="<?php echo home_url(); ?>/logg-inn" style="color:#b7b7b7;">&lt;&lt;&nbsp;Tilbake</a>
                        </p>
                    </div>  
                    <div class="payment_input">
                        <input type="submit" value="Generere passord" style="margin:5px auto;" />
                    </div>
                    <div class="payment_input">
                        <p style="margin: 0px auto; display: block; width: 175px; color:#b7b7b7">Nytt passord sendes på epost.</p>
                    </div>
                </form>
            </div>
                <?php
                }
                elseif (!empty($_GET['view']) && $_GET['view'] == 'forgot_username')
                {
                    ?>
            <div class="logg_in">
                <form action="" method="POST" id="recover_username">
                    <input type="hidden" name="todo" value="recover_username" />
                    <h1>Glemt brukernavn ?</h1>
        <?php if (isset($_REQUEST['user_name']))
        {
            ?>
                        <h4>brukernavn: <?php echo base64_decode($_REQUEST['user_name']); ?></h4>
                        <br />
        <?php } ?>
                    <div class="payment_input">
                        <p style="margin: 10px auto; display: block; width: 250px;">
                            Ditt brukernavn er epostadressen du er registrert med hos Mattevideo.
                        </p>
                        <p style="margin: 0px auto; display: block; width: 250px;">
                            <!--Ta kontakt med brukerstøtte tlf 98 60 61 58/ <a href="mailto:ksondresen@gmail.no">ksondresen@gmail.no</a> om dette ikke fungerer.-->
                            Ta kontakt med brukerstøtte tlf 98 60 61 58/ <a href="mailto:kontakt@mattevideo.no">kontakt@mattevideo.no</a> om dette ikke fungerer.
                        </p>
                    </div>
                    <div class="payment_input">
                        <p style="margin: 10px auto; display: block; width: 250px;">
                            <a href="<?php echo home_url(); ?>/logg-inn" style="color:#b7b7b7;">&lt;&lt;&nbsp;Tilbake</a>
                        </p>
                    </div>
                    <?php /* ?><div class="payment_input">
                      <select name="secret_question" style=" background: none repeat scroll 0 0 #EEEEEE;border-radius: 3px 3px 3px 3px;display: block;font-weight: bold;height: 24px;letter-spacing: 1px;margin: 0 auto 12px;width: 170px;">
                      <option value="childhood_nickname">Hva var din barndom kallenavn?</option>
                      <option value="old_child_middle_name">Hva er den midterste navnet ditt eldste barn?</option>
                      <option value="old_sibling_middle_name">Hva er din eldste søsken mellomnavn?</option>
                      <option value="first_stuffed_animal">Hva var navnet på ditt første utstoppede dyr?</option>
                      <option value="first_job_city_town">I hvilken by eller sted var din første jobb?</option>
                      <option value="spouse_mother_maiden_name">Hva er din ektefelles mors pikenavn?</option>
                      <option value="childhood_hero">Hvem var din barndoms helt?</option>
                      <option value="pet_name">Hva er navnet på kjæledyret ditt?</option>
                      <option value="eyes_color">Hva er fargen på øynene dine?</option>
                      <option value="favourite_animal">Hva er ditt favoritt dyr?</option>
                      <option value="favourite_team">Hva er ditt favorittlag?</option>
                      <option value="favourite_movie">Hva er din favorittfilm?</option>
                      </select>
                      </div>
                      <div class="payment_input">
                      <input type="text" name="secret_answer" value="" placeholder="hemmelig svar"/>
                      </div>
                      <div class="payment_input">
                      <a href="<?php echo home_url();?>/logg-inn" style="margin: 0px auto; display: block; width: 175px; color:#626262;">Tilbake</a>
                      </div>
                      <div class="payment_input">
                      <input type="submit" value="gjenopprette brukernavn" />
                      </div><?php */ ?>
                </form>
            </div>
    <?php
    }
    else
    {
        ?>
            <div class="logg_in">
                <h1>Logg inn</h1>

                <form action="" method="POST" id="login">
                    <div class="payment_input">
                        <input type="text" name="m_brukernavn" value="" placeholder="Brukernavn" style="margin:0 auto 0;"/>
                        <p style="margin:5px auto 15px; display: block; width: 175px;">
                            <a href="?view=forgot_username" style="color:#b7b7b7;">Glemt brukernavn?</a>
                        </p>
                    </div>
                    <div class="payment_input">
                        <input type="password" name="m_passord" value="" placeholder="Passord" style="margin:0 auto 0;"/>
                        <p style="margin:5px auto 15px; display: block; width: 175px;">
                            <a href="?view=forgot_password" style="color:#b7b7b7;">Glemt passord?</a>
                        </p>
                    </div>                    
                    <div class="payment_input">
                        <input type="submit" value="Gå videre" />
                    </div>
                </form>
            </div>
    <?php } ?>
    <?php
}
//	show payments box	
?>

    <div class="start_subscription">

        <h1>Bli abonnent<br/>
            - kun 99 kr pr mnd</h1>

        <form method="post" action="" id="confirm-data">
            <input type="hidden" name="step" value="2" />
            <div class="payment_input">
                <input type="text" name="fname" value="<?php echo (isset($data['fname'])) ? $data['fname'] : ''; ?>" placeholder="Fornavn" />
            </div>
            <div class="payment_input">
                <input type="text" name="lname" value="<?php echo (isset($data['lname'])) ? $data['lname'] : ''; ?>" placeholder="Etternavn" />
            </div>
            <div class="payment_input">
                <input type="text" name="cellnumber" placeholder="Mobilnummer" value="<?php echo (isset($data['cellnumber'])) ? $data['cellnumber'] : ''; ?>" />
            </div>
            <div class="payment_input">
                <input type="text" name="email" placeholder="Epost" value="<?php echo (isset($data['email'])) ? $data['email'] : ''; ?>" />
            </div>

            <div class="payment_input">
                <input type="text" name="retype_email" placeholder="Bekreft Epost" value="<?php echo (isset($_POST['retype_email'])) ? $_POST['retype_email'] : ''; ?>" />
            </div>
            <div class="payment_input">
<?php if (isset($_GET['test']))
{
    ?>
                    <input type="hidden" value="test" name="test" />
<?php } ?>
                <input type="submit" value="Gå videre" />
            </div>
        </form>
        <p>Meld deg på og start læringen i dag! Som abonnent får du eget brukernavn og passord som gir deg ubegrenset tilgang til alle våre kurs, videoer og quiz. Betaling skjer via PayPals sikre kortportal "Braintree", der du betaler med Visa eller MasterCard. Brukernavn og passord sendes på epost.
        </p>
        <p>Det er ingen bindingstid, og du kan når som helst avslutte ditt abonnement via nettsiden, eller via vår kundeservice på tlf <b>98 60 61 58</b> / <b>kontakt@mattevideo.no</b>. Kjøpsvilkår finner du <a href="http://www.mattevideo.no/kjopsvilkar">her</a>.
        </p>
    </div>

</div>