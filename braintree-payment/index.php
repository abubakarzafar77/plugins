<?php
@session_start();
/*
  Plugin Name: Brain Tree Payment
  Plugin URI: http://purelogics.net
  Description: Brain Tree Payment plugin.
  Version: 1.0
  Author: Purelogics.net
  Author URI: http://purelogics.net
 */
require_once 'class/lib/Braintree.php';
require_once 'class/braintree_api.php';
require_once 'class/braintree_admin.php';
// netaxept functions
require_once('class/function.php');
class Braintree_payment
{
    public $pluginPath;
    public $pluginUrl;
    public $step   = 0;
    public $bt_api = 0;
    //
    public $old_user = FALSE;

    public function __construct()
    {
        // Set Plugin Path
        $this->pluginPath = dirname(__FILE__);
        // Set Plugin URL
        $this->pluginUrl = WP_PLUGIN_URL . '/braintree-payment';
        // load brain tree api library
        $this->bt_api = new braintree_api();
        // hooks & action
        add_shortcode('BP_PAYMENT', array($this, 'shortcode'));
        add_shortcode('BP_PAYMENT_REGISTER', array($this, 'shortcode_register'));
        $bt_admin = new Braintree_admin();
        add_action('admin_menu', array($bt_admin, 'create_admin_menu'));
        register_activation_hook(__FILE__, array($this, 'plugin_activate'));
        register_deactivation_hook(__FILE__, array($this, 'plugin_deactivate'));
    }

    public function shortcode_register(){
        $this->old_user = FALSE;
        $type = $this->get_user_subsription_type();
        if ($type == 'netaxept'){
            $this->old_user = TRUE;
        }
        $bt_admin = new Braintree_admin();
        $setting = $bt_admin->get_settings();
        if(!$setting){
            echo "Please Define API Keys from backend";
        }else{
            $step = (isset($_REQUEST['step'])) ? $_REQUEST['step'] : 1;
            $this->load_register_view($step);
        }
    }

    private function load_register_view($step){
        if ($this->is_valid_step($step)) {
            $old_user = $this->old_user;
            $responce = $this->process_payment_steps($step);
            $step = $responce['step'];
            $path = $this->pluginPath . "/views/register_" . $step . ".php";
            $file = "views/register_" . $step . ".php";
            if (!file_exists($path)) {
                echo "ERROR: view file not found ";
            } else {
                include $file;
            }
        }else {
            echo "ERROR: Invalid value of step";
        }
    }

    public function shortcode()
    {
        $this->old_user = FALSE;
        $type = $this->get_user_subsription_type();
        if ($type == 'netaxept') {
            $this->old_user = TRUE;
        }
        $bt_admin = new Braintree_admin();
        $setting = $bt_admin->get_settings();
        if (!$setting) {
            echo "Please Define API Keys from backend";
        } else {
            // extract the attributes into variables
            $step = (isset($_REQUEST['step'])) ? $_REQUEST['step'] : 1;
            switch ($step)
            {
                case 'update_profile':
                    if (!is_user_logged_in()) {
                        wp_redirect(home_url('logg-inn'));
                        exit;
                    }
                    $this->load_update_profile_view();
                    break;
                default:
                    $this->load_payment_view($step);
            }
        }
    }

    public function show_unsubscribe_form_link_for_expire($status = 'Expired')
    {
        $user_data = wp_get_current_user();
        $user_id = $user_data->ID;
        $sub_info = $this->get_subscription_info($user_id, $status);
        if ($sub_info)
        {
            echo '<dd><a href="/logg-inn?view=cancel_reason">Endre status</a></dd>';
        }
        else
        {
            echo '';
        }
    }

    public function show_user_plan(){
        $user_data = wp_get_current_user();
        $user_id = $user_data->ID;
        $sub_info = $this->get_subscription_info($user_id);
        if(isset($sub_info->subscription_plan)){
            switch($sub_info->subscription_plan){
                case 'mattevideo':
                    echo 'Old Plan';
                    break;
                default:
                    echo str_replace('_kr_plan', ' kr pr måned', $sub_info->subscription_plan);
                    break;
            }
        }
    }

    public function show_unsubscribe_form_link()
    {
        $user_data = wp_get_current_user();
        $user_id = $user_data->ID;
        $sub_info = $this->get_subscription_info($user_id);
        if ($sub_info && $sub_info->billing_end_date < time()) {
            echo '<dd><a href="/logg-inn?view=cancel_reason">Endre status</a></dd>';
        } else if(!$sub_info){
            echo '<dd><a href="/logg-inn?renew_subscription=true&step=2">Gjenoppta abonnement</a></dd>';
        }
    }

    public function show_unsubscribe_link_for_expire($status = 'Expired')
    {
        $user_data = wp_get_current_user();
        $user_id   = $user_data->ID;
        $sub_info  = $this->get_subscription_info($user_id, $status);
        if ($sub_info) {
            echo '<a id="canel_sub" href="/logg-inn?step=1&cancel_subscription=true&expire=true">Ja, avbestill mitt abonnement</a>';
        } else {
            echo '';
        }
    }

    public function show_unsubscribe_link()
    {
        $user_data = wp_get_current_user();
        $user_id = $user_data->ID;
        $sub_info = $this->get_subscription_info($user_id);
        if ($sub_info) {
            echo '<a id="canel_sub" href="/logg-inn?step=1&cancel_subscription=true">Ja, avbestill mitt abonnement</a>';
        } else {
            echo '<a class="abc" href="/logg-inn?renew_subscription=true">Gjenoppta abonnement</a>';
        }
    }

    public function show_update_profile_link()
    {
        $user_data = wp_get_current_user();
        $user_id = $user_data->ID;
        $sub_info = $this->get_subscription_info($user_id);
        $sub_info_expired = $this->get_subscription_info($user_id, 'Expired');
        if ($sub_info || $sub_info_expired) {
            echo '<dd><a href="/logg-inn?step=update_profile">Oppdater betalingsinformasjon</a></dd>';
        } else {
            echo '';
        }
    }

    public function show_subscription_status()
    {
        $user_data = wp_get_current_user();
        $user_id = $user_data->ID;
        $sub_info = $this->get_user_subscription_current_status($user_id);
        if ($sub_info) {
            echo $sub_info->status;
        } else {
            echo '';
        }
    }

    public function return_subscription_status()
    {
        $user_data = wp_get_current_user();
        $user_id   = $user_data->ID;
        $sub_info  = $this->get_user_subscription_current_status($user_id);
        if ($sub_info) {
            return $sub_info->status;
        } else {
            return '';
        }
    }

    private function load_payment_view($step)
    {
        if ($this->is_valid_step($step)) {
            $old_user = $this->old_user;
            $responce = $this->process_payment_steps($step);
            $step = $responce['step'];
            if(!isset($_GET['view'])){
                $_GET['view'] = 'show_user';
            }
            if ($_SERVER['HTTP_HOST'] == "localhost") {
                $path = $this->pluginPath . "\\views\step_" . $step . ".php";
            } else {
                $path = $this->pluginPath . "/views/step_" . $step . ".php";
            }
            if (!file_exists($path)) {
                echo "ERROR: view file not found ";
            } else {
                include "views/step_" . $step . ".php";
            }
        } else {
            echo "ERROR: Invalid value of step ";
        }
    }

    private function load_update_profile_view()
    {
        $responce = $this->process_update_profile();
        include "views/update_profile.php";
    }

    private function process_update_profile()
    {
        $responce = array(
            'status'  => 'ok',
            'message' => '',
            'data'    => array()
        );
        $responce['cse_key'] = $this->bt_api->cse_key;
        if (isset($_POST['save']) && $_POST['save'] == 'update_profile') {
            $user_id = $_POST['user_id'];
            $user_info = get_userdata($user_id);
            if ($user_info) {
                $braintree_id = get_user_meta($user_id, 'braintree_id');
                $braintree_id = $braintree_id[0];
                $subscribe_data['first_name'] = $_POST['first_name'];
                $subscribe_data['last_name'] = $_POST['last_name'];
                $subscribe_data['retype_email'] = $_POST['retype_email'];
                $subscribe_data['number'] = $_POST['number'];
                $subscribe_data['email'] = $_POST['email'];
                $subscribe_data['month'] = $_POST['month'];
                $subscribe_data['year'] = $_POST['exp-year'];
                $subscribe_data['cvv'] = $_POST['cvv'];
                $subscribe_data['terms'] = $_POST['terms'];
                $valid = $this->is_valid_subscribe_data($subscribe_data);
                if ($valid['status'] == 'error') {
                    $responce['status'] = 'error';
                    $responce['message'] = $valid['message'];
                    $responce['data']['subscribe_data'] = $subscribe_data;
                    return $responce;
                }
                //Added By Akbar
                $expiredFlag      = false;
                $cancelStatusFlag = false;
                if (isUserSubscribeToBrainTree($user_id)) {
                    $user_sbr = new MattevideoSubscriptionController($user_id);
                    $staus    = $this->get_subscription_info($user_id, 'Expired');
                    //$st_old = $user_sbr->getStatus();
                    if (!empty($staus)) {
                        $expiredFlag  = true;
                        $cancelStatus = $this->bt_api->cancel_user_subscription($staus->subscription_id);
                        if ($cancelStatus['status'] == 'ok') {
                            $cancelStatusFlag = true;
                        }
                    }
                }

                /** ******************************************************** */
                $rsp = $this->bt_api->update_user_profile($braintree_id, $subscribe_data);
                /* Added By Akbar *///////
                if ($rsp['status'] == 'ok' && $cancelStatusFlag && $expiredFlag) {
                    $rsp1 = $this->bt_api->subscribe_user_to_plan($rsp['data']);
                    if ($rsp1['status'] == 'error') {
                        $responce['status']  = 'error';
                        $responce['message'] = $rsp1['data'];
                        $responce['user_id'] = $user_id;
                        return $responce;
                    } else {
                        global $current_user;
                        get_currentuserinfo();
                        $user_id    = $current_user->ID;
                        $sub_id     = $rsp1['data']['subscription_id'];
                        $sub_status = $rsp1['data']['subscription_status'];
                        $created_at = time();
                        $this->save_subscription_info($user_id, $sub_id, $sub_status, $created_at, array('Initiated', 'Active', 'Expired'), $subscribe_data['cvv'], $subscribe_data['number']);
                    }
                }
                /** ******************************************************** */
                if ($rsp['status'] == 'error') {
                    $responce['status'] = 'error';
                    $responce['message'] = $rsp['data'];
                    return $responce;
                } else {
                    // Update Credit Card info send email//
                    $patterns = array();
                    $user_info = get_userdata($user_id);
                    $user_email = $user_info->user_email;
                    $this->send_email('update_card_info', $user_email, $patterns);
                    // Update Credit Card info send email//
                    $responce['status'] = 'ok';
                    $responce['message'] = 'Betalingsinformasjon ble oppdatert';
                    $this->update_cvv_code_by_subscription_id($user_id, $subscribe_data['cvv'], $subscribe_data['number']);
                }
            } else {
                $responce['status'] = 'error';
                $responce['message'] = 'User is invalid';
                return $responce;
            }
        }
        return $responce;
    }

    private function process_payment_steps($step)
    {
        $responce = array(
            'status'  => 'ok',
            'message' => '',
            'step'    => $step,
            'data'    => array()
        );
        switch ($step)
        {
            case '1':
                if (isset($_GET['cancel_subscription']) && $_GET['cancel_subscription'] == 'true') {
                    if (is_user_logged_in()) {
                        global $current_user;
                        get_currentuserinfo();
                        $user_id = $current_user->ID;
                        $sub_info = $this->get_subscription_info($user_id, array('Active', 'Expired'));
                        if ($sub_info) {
                            $subscription_id = $sub_info->subscription_id;
                            $rsp = $this->bt_api->cancel_user_subscription($subscription_id);
                            $id = $sub_info->id;
                            $update_at = time();
                            if ($rsp['status'] == 'error') {
                                $responce['status'] = 'error';
                                $responce['message'] = $rsp['data'];
                                $this->change_subscription_status($id, "Canceled", $update_at);
                                return $responce;
                            } else {
                                $this->change_subscription_status($id, $rsp['data']['subscription_status'], $update_at);
                                $responce['status'] = 'success';
                                $responce['message'] = 'Abonnementet med abonnement ID: ' . $subscription_id . ' er kansellert ';
                                //** save reason ** //
                                if (isset($_POST['studentAge'])) {
                                    $reason_data['student_age'] = $_POST['studentAge'];
                                }
                                if (isset($_POST['reason'])) {
                                    $reason_data['type'] = $_POST['reason'];
                                }
                                if ($reason_data['type'] == "Annet") {
                                    $reason_data['type'] = $_POST['reason_txt'];
                                }
                                if (isset($_POST['reason_new'])) {
                                    $reason_data['text_other'] = $_POST['reason_new'];
                                }
                                if ($reason_data['text_other'] == 'Annet') {
                                    $reason_data['type_other'] = "Annet Video";
                                    $reason_data['text_other'] = $_POST['reason_txt_new'];
                                }
                                $user_info = get_userdata($user_id);
                                $user_email = $user_info->user_email;
                                $reason_data['user_id'] = $user_id;
                                $reason_data['email'] = $user_email;
                                $reason_data['created_at'] = $update_at;
                                $this->save_cancel_reason($reason_data);
                                // ** EMAIL cancel subscription ** //
                                $patterns = array();
                                $user_info = get_userdata($user_id);
                                $user_email = $user_info->user_email;
                                $this->send_email('deactivate_email', $user_email, $patterns);
                                // ** ** //
                                return $responce;
                            }
                        } else {
                            $responce['status'] = 'error';
                            $responce['message'] = 'Your subscription with subscription ID : ' . $subscription_id . ' not found ';
                            return $responce;
                        }
                    } else {
                        wp_redirect(home_url('logg-inn'));
                        exit;
                    }
                } else if (isset($_GET['renew_subscription']) && $_GET['renew_subscription'] == 'true') {
                    if (is_user_logged_in()) {
                        global $current_user;
                        get_currentuserinfo();
                        $user_id = $current_user->ID;
                    }
                    $user_info = get_userdata($user_id);
                    $user_info->phone = get_user_meta($user_id, 'phone');
                    $user_info->phone = $user_info->phone[0];
                    $responce['data']['fname'] = $user_info->first_name;
                    $responce['data']['lname'] = $user_info->last_name;
                    $responce['data']['cellnumber'] = $user_info->phone;
                    $responce['data']['email'] = $user_info->user_email;
                }
                if (isset($_POST['save_email_setting']) && $_POST['save_email_setting'] == 'true') {
                    $no_email = (isset($_POST['no_email'])) ? 1 : 0;
                    $all_email = (isset($_POST['all_email'])) ? 1 : 0;
                    $email_topics = (isset($_POST['email_topics'])) ? $_POST['email_topics'] : 0;
                    $setting = array(
                        "all_groups"  => $all_email,
                        "no_groups"   => $no_email,
                        "main_topics" => $email_topics
                    );
                    $setting = serialize($setting);
                    $user_data = wp_get_current_user();
                    $user_id = $user_data->ID;
                    global $wpdb;
                    $q2 = "SELECT id FROM groups_email_settings  WHERE user_id = '$user_id' ";
                    $user = $wpdb->get_row($q2);
                    if ($user) {
                        $q = " UPDATE groups_email_settings SET setting = '$setting' WHERE user_id = '$user_id' ";
                        $r = $wpdb->query($q);
                    } else {
                        $q = " INSERT INTO groups_email_settings (user_id, setting) VALUES ('$user_id','$setting') ";
                        $r = $wpdb->query($q);
                    }
                }
                if (isset($_GET['view']) && $_GET['view'] == 'show_user') {
                    if (isset($_POST['todo']) && $_POST['todo'] == 'deactivate') {
                        deactivateSubscription();
                    }

                    if (isset($_POST['todo']) && $_POST['todo'] == 'activate') {
                        activateSubscription();
                    }
                }
                return $responce;
                break;
            case '2':
                $responce['cse_key'] = $this->bt_api->cse_key;
                if (isset($_POST) && !empty($_POST)) {
                    $valid = $this->validate_input_fields();
                    if ($valid['status'] == 'error') {
                        $responce['status'] = 'error';
                        $responce['message'] = $valid['message'];
                        $responce['step'] = '1';
                        $responce['data'] = $_POST;
                        return $responce;
                    }
                    $user_name = $_POST['email'];
                    $user_email = $_POST['email'];
                    $first_name = $_POST['fname'];
                    $last_name = $_POST['lname'];
                    $phone = $_POST['cellnumber'];
                    $user_id = username_exists($user_name);
                    if (!$user_id and email_exists($user_email) == false) {
                        $random_password = mt_rand(100001, 999999);
                        $_SESSION['random_password'] = $random_password;
                        $user_id = wp_create_user($user_name, $random_password, $user_email);
                        // ** SEND EMAIL USER   ** //
                        $patterns = array(
                            '{PASSWORD}' => $random_password
                        );
                        wp_update_user(array('ID' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name));
                        add_user_meta($user_id, 'phone', $phone);
                        add_user_meta($user_id, 'user_password', $random_password);
                    } else {
                        global $wpdb;
                        $table = $wpdb->prefix . "braintree_users_subscriptions";
                        $query = "SELECT * FROM $table where user_id=$user_id";
                        $user  = $wpdb->get_row($query);
                        if ($user->status != "Initiated") {
                            $responce['step']    = '1';
                            $responce['status']  = 'error';
                            $message = "<p>Det er allerede opprettet en bruker på denne adressen, vennligst logg inn med tilsendt brukernavn og passord om du ønsker å gjøre endringer i abonnementet</p>";
                            $responce['message'] = ($message);
                            return $responce;
                        }
                    }
                    $created_at = time();
                    $this->save_subscription_info($user_id, '0', 'Initiated', $created_at, array('Initiated', 'Active', 'Expired'));
                    $user_info = get_userdata($user_id);
                    $responce['step'] = '2';
                    $user_info->phone = get_user_meta($user_id, 'phone');
                    $user_info->phone = $user_info->phone[0];
                    $responce['data']['user_info'] = $user_info;
                } else {
                    if (isset($_GET['renew_subscription']) && $_GET['renew_subscription'] == 'true') {
                        if (is_user_logged_in())
                        {
                            global $current_user;
                            get_currentuserinfo();
                            $user_id = $current_user->ID;
                            $created_at = time();
                            $sub_info = $this->get_subscription_info($user_id, 'Canceled', ' ORDER BY id DESC');
                            $this->save_subscription_info($user_id, '0', 'Initiated', $created_at, array('Initiated', 'Active', 'Expired'));
                            $user_info = get_userdata($user_id);
                            $responce['step'] = '2';
                            $user_info->phone = get_user_meta($user_id, 'phone');
                            $user_info->phone = $user_info->phone[0];
                            $responce['data']['user_info'] = $user_info;
                            $responce['data']['sub_info'] = $sub_info;
                        }
                        else
                        {
                            wp_redirect(home_url('logg-inn'));
                            exit;
                        }
                    }
                    else
                    {
                        wp_redirect(home_url('logg-inn'));
                        exit;
                    }
                }
                return $responce;
                break;
            case '3':
                $responce['data']['cse_key'] = $this->bt_api->cse_key;
                $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : $responce['user_id'];
                // if user is not valid
                if (!$user_id || $user_id <= 0) {
                    $responce['status'] = 'error';
                    $responce['message'] = 'User was not found';
                    $responce['step'] = '1';
                    return $responce;
                }
                $user_info = get_userdata($user_id);
                if ($user_info) {
                    $responce['data']['user_info'] = $user_info;
                    $created_at = time();
                    $this->save_subscription_info($user_id, '0', 'Initiated', $created_at, array('Initiated', 'Active', 'Expired'));
                } else {
                    $responce['status'] = 'error';
                    $responce['message'] = 'User was not found';
                    $responce['step'] = '1';
                    return $responce;
                }
                return $responce;
                break;
            case '4':
                if (isset($_POST['save']) && $_POST['save'] == 'subscribe') {
                    if(isset($_POST['package'])){
                        $this->bt_api->plan_name = $_POST['package'];
                    }
                    $subscribe_data['first_name'] = $_POST['first_name'];
                    $subscribe_data['last_name'] = $_POST['last_name'];
                    $subscribe_data['retype_email'] = $_POST['retype_email'];
                    $subscribe_data['number'] = $_POST['number'];
                    $subscribe_data['email'] = $_POST['email'];
                    $subscribe_data['month'] = $_POST['month'];
                    $subscribe_data['year'] = $_POST['exp-year'];
                    $subscribe_data['cvv'] = $_POST['cvv'];
                    $subscribe_data['terms'] = $_POST['terms'];
                    $valid = $this->is_valid_subscribe_data($subscribe_data);
                    if ($valid['status'] == 'error') {
                        $responce['status'] = 'error';
                        $responce['message'] = $valid['message'];
                        $responce['step'] = '1';
                        $responce['data'] = $subscribe_data;
                        return $responce;
                    }else{
                        $user_email = $subscribe_data['email'];
                        $user_id = username_exists($user_email);
                        if (!$user_id && email_exists($user_email) == false) {
                            $user_name = $user_email;
                            $first_name = $subscribe_data['first_name'];
                            $last_name = $subscribe_data['last_name'];
                            $random_password = mt_rand(100001, 999999);
                            $_SESSION['random_password'] = $random_password;
                            $user_id = wp_create_user($user_name, $random_password, $user_email);
                            $patterns = array(
                                '{PASSWORD}' => $random_password
                            );
                            wp_update_user(array('ID' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name));
                            add_user_meta($user_id, 'user_password', $random_password);
                        }else{
                            global $wpdb;
                            $table = $wpdb->prefix . "braintree_users_subscriptions";
                            $query = "SELECT * FROM $table where user_id=$user_id";
                            $user  = $wpdb->get_row($query);
                            if ($user->status == "Active" || $user->status == 'Expired') {
                                $responce['step']    = '1';
                                $responce['status']  = 'error';
                                $message = "<p>Det er allerede opprettet en bruker på denne adressen, vennligst logg inn med tilsendt brukernavn og passord om du ønsker å gjøre endringer i abonnementet</p>";
                                $responce['message'] = $message;
                                $responce['data'] = $subscribe_data;
                                return $responce;
                            }
                        }
                        $created_at = time();
                        $this->save_subscription_info($user_id, '0', 'Initiated', $created_at, array('Initiated', 'Active', 'Expired'));
                        $user_info = get_userdata($user_id);
                        if ($user_info) {
                            $responce['data']['user_info'] = $user_info;
                        }
                        $braintree_id = get_user_meta($user_id, 'braintree_id');
                        // if user brain tree id exist then
                        if ($braintree_id) {
                            $braintree_id = $braintree_id[0];
                            if ($valid['status'] == 'error') {
                                $responce['status'] = 'error';
                                $responce['message'] = $valid['message'];
                                $responce['step'] = '1';
                                $responce['user_id'] = $user_id;
                                $responce['data'] = $subscribe_data;
                                return $responce;
                            }
                            $rsp = $this->bt_api->update_user_profile($braintree_id, $subscribe_data);
                            if ($rsp['status'] == 'error') {
                                $responce['status'] = 'error';
                                $responce['message'] = $rsp['data'];
                                $responce['data'] = $subscribe_data;
                                $responce['step'] = '1';
                                $responce['user_id'] = $user_id;
                                return $responce;
                            }
                            $responce['step'] = 4;
                            $sub_info = $this->get_subscription_info($user_id, array('Active', 'Expired'));
                            if ($sub_info == NULL) {
                                $rsp = $this->bt_api->subscribe_user_to_plan($braintree_id);
                                if ($rsp['status'] == 'error') {
                                    $responce['status'] = 'error';
                                    $responce['message'] = $rsp['data'];
                                    $responce['data'] = $subscribe_data;
                                    $responce['user_id'] = $user_id;
                                    $responce['step'] = 1;
                                    return $responce;
                                } else {
                                    $sub_id = $rsp['data']['subscription_id'];
                                    $sub_status = $rsp['data']['subscription_status'];
                                    $created_at = time();
                                    $this->save_subscription_info($user_id, $sub_id, $sub_status, $created_at, array('Initiated', 'Active', 'Expired'), $subscribe_data['cvv'], $subscribe_data['number']);
                                    $patterns = array();
                                    $user_email = $user_info->user_email;
                                    $this->send_email('reactivate_email', $user_email, $patterns);
                                }
                            } else {
                                $responce['status'] = 'error';
                                $responce['message'] = "<p>Du abonnerer allerede på denne planen.</p>";
                                $responce['step'] = 1;
                                $responce['user_id'] = $user_id;
                                return $responce;
                            }
                            return $responce;
                        } else {
                            // else create brain tree profile
                            $user_info->braintree_id = '0';
                            $rsp = $this->bt_api->create_user_profile($subscribe_data);
                            if ($rsp['status'] == 'error') {
                                $responce['status'] = 'error';
                                $responce['message'] = $rsp['data'];
                                $responce['step'] = '1';
                                $responce['user_id'] = $user_id;
                                $responce['data'] = $subscribe_data;
                                return $responce;
                            } else {
                                $responce['step'] = 4;
                                $braintree_id = $rsp['data'];
                                add_user_meta($user_id, 'braintree_id', $braintree_id);
                                $rsp = $this->bt_api->subscribe_user_to_plan($braintree_id);
                                if ($rsp['status'] == 'error') {
                                    $responce['status'] = 'error';
                                    $responce['message'] = $rsp['data'];
                                    $responce['user_id'] = $user_id;
                                    $responce['step'] = 1;
                                    $responce['data'] = $subscribe_data;
                                    $random_password = get_user_meta($user_id, 'user_password', TRUE);
                                    $patterns = array(
                                        '{PASSWORD}' => $random_password
                                    );
                                    $user_email = $user_info->user_email;
                                    $this->send_email('payment_email', $user_email, $patterns);
                                    return $responce;
                                } else {
                                    $sub_id = $rsp['data']['subscription_id'];
                                    $sub_status = $rsp['data']['subscription_status'];
                                    $created_at = time();
                                    //$rsp['data']['result'];
                                    $this->save_subscription_info($user_id, $sub_id, $sub_status, $created_at, array('Initiated', 'Active', 'Expired'), $subscribe_data['cvv'], $subscribe_data['number']);
                                    $random_password = get_user_meta($user_id, 'user_password', TRUE);
                                    $patterns = array(
                                        '{PASSWORD}' => $random_password
                                    );
                                    $user_email = $user_info->user_email;
                                    $this->send_email('payment_email', $user_email, $patterns);
                                }
                                return $responce;
                            }
                        }
                    }
                }
                break;
            default:
                if (!is_user_logged_in()) {
                    wp_redirect(home_url('logg-inn'));
                    exit;
                }
                return false;
                break;
        }
    }

    public function save_subscription_info($user_id, $subscription_id, $status, $created_at, $check_status = '', $cvv_code = '', $number = '')
    {
        global $wpdb;
        $table = $wpdb->prefix . "braintree_users_subscriptions";
        $sub_info = $this->get_subscription_info($user_id, $check_status);
        if ($sub_info != NULL) {
            $id = $sub_info->id;
            $existing_status = $sub_info->status;
            $update = "";
            if ($subscription_id != "0") {
                $update = ", subscription_id = '$subscription_id' ";
            }
            $update_cvv = "";
            if ($number != '') {
                $last_four_dig = substr($number, -4);
                $update_cvv = ", cvv = '$cvv_code' , number = '$last_four_dig' ";
            }
            if ($existing_status != 'Active') {
                $q = "UPDATE " . $table . " SET status = '$status', subscription_plan='{$this->bt_api->plan_name}' " . $update . $update_cvv . " WHERE id = '$id' ";
            }
        } else {
            $q = "INSERT INTO " . $table . " (user_id, subscription_id, subscription_plan, status, created_at) VALUES ('$user_id','$subscription_id', '".$this->bt_api->plan_name."','$status','$created_at') ";
        }
        $r = $wpdb->query($q);
        return $r;
    }

    public function get_subscription_info($user_id, $status = 'Active', $order_by='')
    {
        global $wpdb;
        $table = $wpdb->prefix . "braintree_users_subscriptions";
        if (is_array($status)) {
            $where = '';
            foreach ($status as $i => $stat) {
                if ($i == 0) {
                    $where .= " AND ( status = '$stat' ";
                } else {
                    $where .= " OR status = '$stat' ";
                }
            }
            $where = ($where != '') ? $where . " )" : '';
            $q = "SELECT * FROM " . $table . "  WHERE user_id = '$user_id'  " . $where .$order_by;
        } else {
            $q = "SELECT * FROM " . $table . "  WHERE user_id = '$user_id' AND status = '$status' ".$order_by;
        }
        return $wpdb->get_row($q);
    }

    public function change_subscription_status($id, $status, $time = '')
    {
        global $wpdb;
        $table = $wpdb->prefix . "braintree_users_subscriptions";
        $update_time = "";
        if ($time != '') {
            $update_time = " , updated_at = '$time' ";
        }
        $q = "UPDATE " . $table . " SET status = '$status' " . $update_time . " WHERE id = '$id' ";
        return $wpdb->query($q);
    }

    public function get_user_subscription_current_status($user_id)
    {
        global $wpdb;
        $table = $wpdb->prefix . "braintree_users_subscriptions";
        $q = "SELECT * FROM " . $table . " us WHERE us.user_id = '" . $user_id . "' ORDER BY us.id DESC LIMIT 1 ";
        return $wpdb->get_row($q);
    }

    public function update_cvv_code_by_subscription_id($user_id, $cvv_code, $number)
    {
        global $wpdb;
        $table = $wpdb->prefix . "braintree_users_subscriptions";
        $q2 = "SELECT * FROM " . $table . " WHERE user_id = '$user_id' AND ( status = 'Active' OR status = 'Initiated' ) ";
        $r = $wpdb->get_row($q2);
        if ($r) {
            $last_four_digit = substr($number, -4);
            $subscription_id = $r->subscription_id;
            $q = "UPDATE " . $table . " SET cvv = '$cvv_code', number = '$last_four_digit' WHERE subscription_id = '$subscription_id' ";
            $wpdb->query($q);
        }
    }

    public function save_cancel_reason($data)
    {
        $user_id = $data['user_id'];
        $email = $data['email'];
        $type = $data['type'];
        $student_age = $data['student_age'];
        $type_other  = $data['type_other'];
        $text       = addslashes($data['text']);
        $text_other = addslashes($data['text_other']);
        $created_at = $data['created_at'];
        global $wpdb;
        $table = $wpdb->prefix . "braintree_cancel_reason";
        $q = "INSERT INTO " . $table . " (user_id,student_age , email, type,text_other, created_at) VALUES ('$user_id','$student_age','$email','$type','$text_other','$created_at') ";
        $wpdb->query($q);
    }

    public function is_valid_step($step)
    {
        if (isset($step)) {
            if ($step > 0 && $step < 5) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public function validate_input_fields()
    {
        $rsp = array('status' => 'ok', 'message' => '');
        $user_email = $_POST['email'];
        $first_name = $_POST['fname'];
        $last_name = $_POST['lname'];
        $cell_num = $_POST['cellnumber'];
        $confirm_email = $_POST['retype_email'];
        $message = '';
        if ($user_email == '') {
            $message .= '<p> Vennligst skriv inn epost </p>';
        }
        if ($first_name == '') {
            $message .= '<p> Vennligst skriv inn fornavn </p>';
        }
        if ($last_name == '') {
            $message .= '<p> Vennligst skriv inn etternavn </p>';
        }
        if ($first_name == $last_name) {
            $message .= '<p> Vennligst skriv inn ulike fornavn og etternavn </p>';
        }
        if ($confirm_email == '') {
            $message .= '<p> Vennligst bekreft epost </p>';
        }
        if ($confirm_email !== $user_email) {
            $message .= '<p> Ulik epost adresse oppgitt, vennligst skriv inn på nytt. </p>';
        }
        if ($message != '') {
            $rsp['status'] = 'error';
            $rsp['message'] = $message;
        }
        return $rsp;
    }

    public function is_valid_subscribe_data($data){
        $rsp = array('status' => 'ok', 'message' => '');
        $user_email = $data['email'];
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        $retype_email = $data['retype_email'];
        $number = $data['number'];
        $month = $data['month'];
        $year = $data['year'];
        $cvv = $data['cvv'];
        $terms = $data['terms'];
        $message = '';
        if ($first_name == '') {
            $message .= '<p> Vennligst skriv inn fornavn </p>';
        }
        if ($last_name == '') {
            $message .= '<p> Vennligst skriv inn etternavn </p>';
        }
        if ($user_email == '') {
            $message .= '<p> E-post er påkrevd </p>';
        }
        if ($retype_email == '') {
            $message .= '<p> Gjenta e-postadresse er nødvendig </p>';
        }
        $pattern = '/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/i';
        preg_match($pattern, $user_email, $matches);
        if($user_email != '' && empty($matches)){
            $message .= '<p> Gyldig e-post er påkrevd </p>';
        }
        preg_match($pattern, $retype_email, $matches);
        if($retype_email != '' && empty($matches)){
            $message .= '<p> Gyldig Gjenta e-postadresse er nødvendig </p>';
        }
        if($user_email !== $retype_email){
            $message .= '<p>Ulik epost adresse oppgitt, vennligst skriv inn på nytt.</p>';
        }
        if ($first_name == $last_name) {
            $message .= '<p> Vennligst skriv inn ulike fornavn og etternavn </p>';
        }
        if ($number == '') {
            $message .= '<p> Kort nummer er påkrevd </p>';
        }
        if ($cvv == '') {
            $message .= '<p> CVV nummer kreves </p>';
        }
        if ($month == '') {
            $message .= '<p> Utløps måned er nødvendig </p>';
        }
        if ($year == '') {
            $message .= '<p> Utløps År kreves </p>';
        }
        if(!$terms){
            $message .= '<p>Vennligst fyll ut alle felter, og huk av for kjøpsvilkårene.</p>';
        }
        if ($message != '') {
            $rsp['status'] = 'error';
            $rsp['message'] = $message;
        }
        return $rsp;
    }

    private function send_email($type, $to, $patterns)
    {
        $headers = "MIME-Version: 1.0\n" .
                "From: Mattevideo <kontakt@mattevideo.no>\n" .
                "Content-Type: text/html; charset=\"" .
                get_option('blog_charset') . "\"\n";
        $config = json_decode(file_get_contents(dirname(__FILE__) . '/config.json'), true);
        $subject = $config['email_templates'][$type]['subject'];
        $body = $config['email_templates'][$type]['body'];
        foreach ($patterns as $pattern => $val) {
            $body = str_replace($pattern, $val, $body);
            $subject = str_replace($pattern, $val, $subject);
        }
        $attachments = array();
        if ($type == 'payment_email') {
            $attachments[] = dirname(__FILE__) . '/garanti.pdf';
        }
        $admin       = 'kontakt@mattevideo.no';
        $admin_body  = $body;
        $admin_body .= "<br />Email was sent to: $to";
        $admin_email = wp_mail($admin, $subject, $admin_body, $headers, $attachments);
        $mail = wp_mail($to, $subject, $body, $headers, $attachments);
        return true;
    }

    public function plugin_activate()
    {
        return true;
        global $wpdb;
        $table = $wpdb->prefix . "braintree_setting";
        $q1 = "CREATE TABLE IF NOT EXISTS " . $table . " (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `merchant_id` varchar(225) NOT NULL,
                `public_key` varchar(225) NOT NULL,
                `private_key` varchar(225) NOT NULL,
                `cse_key` text NOT NULL,
                `sandbox` enum('0','1') NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`)
              ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($q1);
        $table = $wpdb->prefix . "braintree_users_subscriptions";
        $q2 = "CREATE TABLE IF NOT EXISTS " . $table . " (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `user_id` int(11) NOT NULL,
                  `subscription_id` varchar(225) NOT NULL,
                  `status` varchar(50) NOT NULL,
                  `created_at` int(10) NOT NULL,
                  PRIMARY KEY (`id`),
                  KEY `user_id` (`user_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($q2);
        $table = $wpdb->prefix . "braintree_log";
        $q3 = "CREATE TABLE IF NOT EXISTS " . $table . " (
                  `id` bigint(10) NOT NULL AUTO_INCREMENT,
                    `subscription_id` varchar(225) DEFAULT NULL,
                    `type` varchar(225) DEFAULT NULL,
                    `created_at` datetime DEFAULT NULL,
                    `response` text,
                    PRIMARY KEY (`id`)
                  ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($q3);
    }

    public function plugin_deactivate()
    {
        return true;
        global $wpdb;
        $tables[] = $wpdb->prefix . "braintree_setting";
        foreach ($tables as $table) {
            $q = "DROP TABLE " . $table;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($q);
        }
    }

    public function get_user_subsription_type()
    {
        global $wpdb;
        $user_data = wp_get_current_user();
        $user_id = $user_data->ID;
        $table = $wpdb->prefix . "braintree_users_subscriptions";
        $q = "SELECT * FROM " . $table . "  WHERE user_id = '$user_id' ";
        $row = $wpdb->get_row($q);
        if ($row) {
            $type = 'braintree';
        } else {
            $type = 'netaxept';
        }
        return $type;
    }

}
$bt_payment = new Braintree_payment();
$GLOBALS['bt_payment'] = $bt_payment;
if (!function_exists('update_logintime_braintree')) {
    function update_logintime_braintree($user_login)
    {
        $user = get_user_by( 'login', $user_login );
        $user_id = $user->ID;
        if ($user_id) {
            if (get_user_meta($user_id, 'last_login', true)) {
                update_user_meta($user_id, 'last_login', date("Y-m-d H:i:s"));
            } else {
                add_user_meta($user_id, 'last_login', date("Y-m-d H:i:s"));
            }
        }
    }

}
if(!function_exists('wp_plugin_style')){
    function wp_plugin_style() {
        global $bt_payment;
        wp_enqueue_style( 'bt-style', $bt_payment->pluginUrl.'/css/bt-style.css' );
        wp_enqueue_script( 'bt-script', $bt_payment->pluginUrl.'/js/bt-script.js', array(), '1.0.0', true );
        wp_enqueue_script( 'bt-masked-input', $bt_payment->pluginUrl.'/views/jquery.maskedinput.js', array(), '1.0.0', true );
    }
}
if(!function_exists('ajax_login')) {
    function ajax_login()
    {
        // First check the nonce, if it fails the function will break
        check_ajax_referer('ajax-login-nonce', 'security');
        // Nonce is checked, get the POST data and sign user on
        $info = array();
        $info['user_login'] = $_POST['username'];
        $info['user_password'] = $_POST['password'];
        $info['remember'] = true;

        $user_signon = wp_signon($info, false);
        if (is_wp_error($user_signon)) {
            echo json_encode(array('loggedin' => false, 'message' => __('Feil brukernavn eller passord.')));
        } else {
            echo json_encode(array('loggedin' => true, 'message' => __('Logg vellykket, omdirigere ...')));
        }
        die();
    }
}
if(!function_exists('ajax_forgot')){
    function ajax_forgot(){
        // First check the nonce, if it fails the function will break
        check_ajax_referer('ajax-forgot-password-nonce', 'fpassword');

        $userInfo = get_user_by('login', trim($_POST['email']));
        if (!empty($userInfo))
        {
            $password = mt_rand(100001, 999999);
            wp_set_password($password, $userInfo->data->ID);
            $msg = "Ditt mattevideo abonnement er klart til bruk! Logge inn med din e-postadresse og passord: $password";
            $headers = "From: Mattevideo <ksondresen@gmail.com>";
            $subject = "generere passord";
            wp_mail($userInfo->data->user_email, $subject, $msg, $headers);
        }
        else
        {
            $userInfo = get_user_by('email', trim($_POST['email']));
            if (!empty($userInfo))
            {
                $password = mt_rand(100001, 999999);
                wp_set_password($password, $userInfo->data->ID);
                $msg = "Ditt mattevideo abonnement er klart til bruk! Logge inn med din e-postadresse og passord: $password";
                $headers = "From: Mattevideo <ksondresen@gmail.com>";
                $subject = "generere passord";
                $attachments = '';
                wp_mail($userInfo->data->user_email, $subject, $msg, $headers, $attachments);
            }
        }
        echo json_encode(array('loggedin' => true, 'message' => __('Send nytt passord på epost...')));
        die();
    }
}
add_action( 'wp_enqueue_scripts', 'wp_plugin_style' );
add_action('wp_login', 'update_logintime_braintree', 10, 1);
add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
add_action( 'wp_ajax_nopriv_ajaxforgot', 'ajax_forgot' );
?>