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
    <?php
    //	show logged in view: show user or show subscription
    if (is_user_logged_in())
    {
        $user_data = wp_get_current_user();

        if (!empty($_GET['view']) && $_GET['view'] == 'show_user') {?>
            <?php if ($old_user) {
                $user_subscription = new MattevideoSubscriptionController($user_data->ID);
                update_user_meta($user_data->ID, 'sbr_status', $user_subscription->getStatus()); ?>
                    <div class="login_content">
                        <div class="login_left"></div>
                        <div class="login_right">
                            <h1>Din profil</h1>
                            <dl class="user_data">
                                <dt>Brukernavn:</dt>
                                <dd><?php echo $user_data->user_login; ?></dd>
                                <div class="clear"></div>
                                <dt>E-post:</dt>
                                <dd><?php echo $user_data->user_email; ?></dd>
                                <div class="clear"></div>
                                <dt>Status:	</dt>
                                <dd><?php echo $user_subscription->getStatus(); ?></dd>
                                <div class="clear"></div>
                            </dl>
                            <a href="?view=show_subscription">Endre status</a>
                        </div>
                        <div class="clear"></div>
                    </div>
                <?php
            } else {
                $status = $this->return_subscription_status();
                ?>
                <div class="login_content">
                    <div class="login_left"></div>
                    <div class="login_right">
                        <h1>Din profil</h1>
                        <dl class="user_data">
                            <dt>Brukernavn:</dt>
                            <dd><?php echo $user_data->user_login; ?></dd>
                            <div class="clear"></div>
                            <dt>E-post:</dt>
                            <dd><?php echo $user_data->user_email; ?></dd>
                            <div class="clear"></div>
                            <?php if($status == 'Active'){?>
                                <dt>Abonnementsplan:</dt>
                                <dd><?php $this->show_user_plan();?></dd>
                                <div class="clear"></div>
                            <?php }?>
                            <dt>Status:	</dt>
                            <dd>
                                <?php $this->show_subscription_status(); ?>
                            </dd>
                            <div class="clear"></div>
                        </dl>
                        <dl class="din_profile">
                            <?php
                            if ($status == 'Expired') {
                                $this->show_unsubscribe_form_link_for_expire();
                            } else {
                                $this->show_unsubscribe_form_link();
                            }
                            ?>
                            <?php $this->show_update_profile_link(); ?>
                            <dd>
                                <a href="<?php echo get_edit_user_link(); ?>" target="_blank">Endre passord eller brukerdetaljer</a>
                            </dd>
                        </dl>
                    </div>
                    <div class="clear"></div>
                </div>
            <?php } ?>
            <?php
        } else if (!empty($_GET['view']) && $_GET['view'] == 'show_subscription' && get_user_meta($user_data->ID, 'sbr_status', true)) {?>
                <?php
                $subscriptionStatus = 'Deactivated';
                if ($old_user) {
                    $user_data          = wp_get_current_user();
                    $user_subscription  = new MattevideoSubscriptionController($user_data->ID);
                    $subscriptionStatus = $user_subscription->getStatus();
                }
                ?>
                <div class="login_content">
                    <div class="login_left"></div>
                    <div class="login_right">
                        <h1>Din profil</h1>
                        <?php if ($subscriptionStatus == 'Active') {?>
                            <p><?php echo utf8_encode('Om du �nsker � avslutte ditt abonnement gj�res det ved � f�lge linken under.');?></p>
                            <p><?php echo utf8_encode('Kontakt gjerne v�r kundeservice ved sp�rsm�l, tlf 98 60 61 58');?></p>
                            <form action="logg-inn?view=show_user" method="post" id="deactivate">
                                <input type="hidden" name="todo" value="deactivate" />
                                <div class="login_btn">
                                    <input type="submit" value="Deaktiver abonnement" />
                                </div>
                            </form>
                        <?php } elseif ($subscriptionStatus == 'Cancelled') { ?>
                            <p><?php echo utf8_encode('Om du �nsker � avslutte ditt abonnement gj�res det ved � f�lge linken under.');?></p>
                            <p><?php echo utf8_encode('Kontakt gjerne v�r kundeservice ved sp�rsm�l, tlf 98 60 61 58');?></p>
                            <form action="logg-inn?view=show_user" method="post" id="activate">
                                <input type="hidden" name="todo" value="activate" />
                                <div class="login_btn">
                                    <input type="submit" value="Reaktiver abonnement" />
                                </div>
                            </form>
                        <?php } else if ($subscriptionStatus == 'Deactivated' || $subscriptionStatus == 'Expired') { ?>
                            <p><?php echo utf8_encode('Om du �nsker � aktivere ditt abonnement gj�res det ved � kontakte telefonnummeret under, eller sende en mail til....');?></p>
                            <p><?php echo utf8_encode('Kontakt gjerne v�r kundeservice ved sp�rsm�l, tlf 98 60 61 58');?></p>
                        <?php } ?>
                    </div>
                    <div class="clear"></div>
                </div>
            <?php } else if (!empty($_GET['view']) && $_GET['view'] == 'cancel_reason') {?>
                <div class="login_content">
                    <div class="login_left"></div>
                    <div class="login_right">
                        <form action="?step=1&cancel_subscription=true" method="post" id="canel_subscription_form" >
                            <h1>Endre status for abonnement</h1>
                            <p><?php echo utf8_encode('Takk for at du har brukt Mattevideo. Vennligst fyll ut feltene under f�r du avslutter ditt abonnement. Alle svar er anonyme.');?></p>
                            <div>
                                Min alder
                                <input type="text" name="studentAge" id="studentAge" class="field" />
                            </div>
                            <p>Student status:</p>
                            <select id="student_status" required="true" name="reason" class="field">
                                <option selected="selected" value="">Select an option</option>
                                <option value="Elev ved vanlig vgs">Elev ved vanlig vgs</option>
                                <option value="Elev ved privatskole">Elev ved privatskole</option>
                                <option value="Privatist uten klasseromsundervisning">Privatist uten klasseromsundervisning</option>
                                <option value="Annet">Annet</option>
                            </select>
                            <input class="hidden field" class="reason_txt" type="text"  autocomplete="off" name="reason_txt" id="reason_txt" >
                            <p><?php echo utf8_encode('Har bruker av mattevideo v�rt den samme som har betalt abonnementet?');?></p>
                            <select id="q2" required="true" name="reason_new" class="field">
                                <option value="" selected="selected">Select an option</option>
                                <option value="Ja jeg har selv brukt mattevideo og betalt selv.">Ja, jeg har selv brukt mattevideo og betalt selv.</option>
                                <option value="Nei en av mine foreldre eller foresatte har betalt abonnementet. ">Nei, en av mine foreldre eller foresatte har betalt abonnementet.</option>
                                <option value="Annet">Annet</option>
                            </select>
                            <input class="hidden field" type="text"  autocomplete="off" name="reason_txt_new" id="reason_txt_new" >
                            <?php
                            if ($this->return_subscription_status() == 'Expired') {
                                $this->show_unsubscribe_link_for_expire();
                            } else {
                                $this->show_unsubscribe_link();
                            }
                            ?>
                            <p><?php echo utf8_encode('Ved avbestilling slettes all betalingsinformasjon. Ved reaktivering m� denne informasjonen fylles inn p� nytt.');?></p>
                        </form>
                        <div class="clear"></div>
                    </div>
                </div>
            <?php
        }
        else
        {
            //	show logged in box
            //	if this is a mobile user, add meta for video player
            if (get_user_meta($user_data->ID, 'sbr_status', true) == '') {
                global $wpdb;
                $experationDate = $wpdb->get_var($wpdb->prepare("SELECT subscriptionValidToDate FROM wptest_sendegaSMS_subscription WHERE wp_user_id = %d", $user_data->ID));
                update_user_meta($user_data->ID, 'sendega_experation_time', $experationDate);
            }
            ?>
            <div class="login_content">
                <div class="login_left"></div>
                <div class="login_right">
                    <?php $user_data = wp_get_current_user(); ?>
                    <h1>Logget inn som</h1>
                    <a href="?view=show_user" class="settings">
                        <img src="<?php echo plugins_url('NetAxept/img/btn-crank.png') ?>" />
                    </a>
                    <p class="name"><?php echo $user_data->user_login ?></p>
                    <form action="" method="post" id="logout">
                        <input type="hidden" value="log_out" name="todo" />
                        <div class="login_btn">
                            <input type="submit" value="Logg ut" />
                        </div>
                    </form>
                </div>
                <div class="clear"></div>
            </div>
            <?php
        }
    }
    else
    {
        if (!empty($_GET['view']) && $_GET['view'] == 'forgot_password') {
        ?>
        <div class="login_content">
            <div class="login_left"></div>
            <div class="login_right">
                <h1>Glemt passord</h1>
                <form action="" method="POST" id="forgot_password">
                    <input type="hidden" name="todo" value="forgot_password" />
                    <input type="text" name="epost" value="" class="field" placeholder="Epost" />
                    <div class="payment_input login_btn">
                        <input type="submit" value="<?php echo utf8_encode('Generere passord');?>" />
                    </div>
                    <a href="<?php echo home_url('logg-inn'); ?>" class="filed_text">&lt;&lt;&nbsp;Tilbake</a>
                    <div class="filed_text"><?php echo utf8_encode('Nytt passord sendes p� epost.');?></div>
                </form>
            </div>
            <div class="clear"></div>
        </div>
        <?php
    }
    elseif (!empty($_GET['view']) && $_GET['view'] == 'forgot_username')
    {
        ?>
        <div class="logg_in">
            <form action="" method="POST" id="recover_username">
                <input type="hidden" name="todo" value="recover_username" />
                <div class="text_content">
                    <div class="text_content_left"></div>
                    <div class="text_content_right">
                        <h1>Glemt brukernavn?</h1>
                        <?php
                        if (isset($_REQUEST['user_name']))
                        {
                            ?>
                            <h4>brukernavn: <?php echo base64_decode($_REQUEST['user_name']); ?></h4>
                            <br />
                        <?php } ?>
                        <p>Ditt brukernavn er epostadressen du er registrert med hos Mattevideo.</p>
                        <p><?php echo utf8_encode('Ta kontakt med brukerst�tte tlf 98 60 61 58/ <a href="mailto:kontakt@mattevideo.no">kontakt@mattevideo.no</a> om dette ikke fungerer.');?></p>
                        <p><a href="<?php echo home_url(); ?>/logg-inn" style="color:#b7b7b7;">&lt;&lt;&nbsp;Tilbake</a></p>
                    </div>
                    <div class="clear"></div>
                </div>
            </form>
        </div>
        <?php
    }
    else
    {
        ?>

        <div class="login_content">
            <div class="login_left"></div>
            <div class="login_right">
                <h1>Logg inn</h1>
                <form action="" method="POST" >
                    <input name="m_brukernavn" type="text" class="field" placeholder="Brukernavn" />
                    <a href="?view=forgot_username" class="filed_text">Glemt brukernavn?</a>
                    <input name="m_passord" type="password" class="field" placeholder="Passord" />
                    <a href="?view=forgot_password" class="filed_text">Glemt passord?</a>
                    <div class="payment_input login_btn">
                        <input type="submit" value="<?php echo utf8_encode('G� videre');?>">
                    </div>
                </form>
            </div>
            <div class="clear"></div>
        </div>
    <?php } ?>
        <?php
    }
    //	show payments box
    ?>