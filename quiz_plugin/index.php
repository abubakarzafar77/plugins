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

    protected $pluginPath;
    protected $pluginUrl;
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

        $bt_admin = new Braintree_admin();

        add_action('admin_menu', array($bt_admin, 'create_admin_menu'));

        register_activation_hook(__FILE__, array($this, 'plugin_activate'));

        register_deactivation_hook(__FILE__, array($this, 'plugin_deactivate'));
    }

    public function shortcode()
    {

        $this->old_user = FALSE;

        $type = $this->get_user_subsription_type();



        if ($type == 'netaxept')
        {

            $this->old_user = TRUE;
        }



        $bt_admin = new Braintree_admin();

        $setting = $bt_admin->get_settings();

        if (!$setting)
        {

            echo "Please Define API Keys from backend";
        }
        else
        {

            // extract the attributes into variables

            $step = (isset($_REQUEST['step'])) ? $_REQUEST['step'] : 1;



            switch ($step)
            {



                case 'update_profile':

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

            echo '<dd><a href="?view=cancel_reason">Endre status</a></dd>';
        }
        else
        {

            echo '';
        }
    }

    public function show_unsubscribe_form_link()
    {

        $user_data = wp_get_current_user();

        $user_id = $user_data->ID;

        $sub_info = $this->get_subscription_info($user_id);

        if ($sub_info)
        {

            echo '<dd><a href="?view=cancel_reason">Endre status</a></dd>';
        }
        else
        {

            echo '<dd><a href="?renew_subscription=true&step=2">Gjenoppta abonnement</a></dd>';
        }
    }

    public function show_unsubscribe_link_for_expire($status = 'Expired')
    {

        $user_data = wp_get_current_user();
        $user_id   = $user_data->ID;
        $sub_info  = $this->get_subscription_info($user_id, $status);
        if ($sub_info)
        {
            echo '<a id="canel_sub" href="?step=1&cancel_subscription=true&expire=true">Ja, avbestill mitt abonnement</a>';
        }
        else
        {
            echo '';
        }
    }

    public function show_unsubscribe_link()
    {

        $user_data = wp_get_current_user();

        $user_id = $user_data->ID;

        $sub_info = $this->get_subscription_info($user_id);

        if ($sub_info)
        {

            echo '<a id="canel_sub" href="?step=1&cancel_subscription=true">Ja, avbestill mitt abonnement</a>';
        }
        else
        {

            echo '<a class="abc" href="?renew_subscription=true">Gjenoppta abonnement</a>';
        }
    }

    public function show_update_profile_link()
    {

        $user_data = wp_get_current_user();

        $user_id = $user_data->ID;

        $sub_info = $this->get_subscription_info($user_id);

        $sub_info_expired = $this->get_subscription_info($user_id, 'Expired');

        if ($sub_info || $sub_info_expired)
        {

            echo '<dd><a href="?step=update_profile">Endre betalings måte</a></dd>';
        }
        else
        {

            echo '';
        }

        /* if($user_id == '1586' || $user_id == '1721'){
          echo '<dd><a href="?step=update_profile">Endre betalings måte</a></dd>';
          }else{


          } */
    }

    public function show_subscription_status()
    {

        $user_data = wp_get_current_user();

        $user_id = $user_data->ID;

        $sub_info = $this->get_user_subscription_current_status($user_id);



        if ($sub_info)
        {

            echo $sub_info->status;
        }
        else
        {

            echo '';
        }
    }

    public function return_subscription_status()
    {
        $user_data = wp_get_current_user();
        $user_id   = $user_data->ID;
        $sub_info  = $this->get_user_subscription_current_status($user_id);
        if ($sub_info)
        {
            return $sub_info->status;
        }
        else
        {
            return '';
        }
    }

    private function load_payment_view($step)
    {

        $this->load_style();

        $this->load_script();

        if ($this->is_valid_step($step))
        {



            $old_user = $this->old_user;

            $responce = $this->process_payment_steps($step);

            $step = $responce['step'];

            if ($_SERVER['HTTP_HOST'] == "localhost")
            {

                $path = $this->pluginPath . "\\views\step_" . $step . ".php";
            }
            else
            {

                $path = $this->pluginPath . "/views/step_" . $step . ".php";
            }

            if (!file_exists($path))
            {

                echo "ERROR: view file not found ";
            }
            else
            {

                include "views/step_" . $step . ".php";
            }
        }
        else
        {

            echo "ERROR: Invalid value of step ";
        }
    }

    private function load_update_profile_view()
    {

        $this->load_style();

        $this->load_script();

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

        if (isset($_POST['save']) && $_POST['save'] == 'update_profile')
        {

            $user_id = $_POST['user_id'];

            $user_info = get_userdata($user_id);

            if ($user_info)
            {

                $braintree_id = get_user_meta($user_id, 'braintree_id');

                $braintree_id = $braintree_id[0];

                $subscribe_data['first_name'] = $_POST['first_name'];

                $subscribe_data['last_name'] = $_POST['last_name'];

                $subscribe_data['number'] = $_POST['number'];

                $subscribe_data['month'] = $_POST['month'];

                $subscribe_data['year'] = $_POST['exp-year'];

                $subscribe_data['cvv'] = $_POST['cvv'];

                $valid = $this->is_valid_subscribe_data($subscribe_data);

                if ($valid['status'] == 'error')
                {

                    $responce['status'] = 'error';

                    $responce['message'] = $valid['message'];

                    $responce['data']['subscribe_data'] = $subscribe_data;

                    return $responce;
                }

                $rsp = $this->bt_api->update_user_profile($braintree_id, $subscribe_data);



                if ($rsp['status'] == 'error')
                {

                    $responce['status'] = 'error';

                    $responce['message'] = $rsp['data'];

                    return $responce;
                }
                else
                {
                    // Update Credit Card info send email//
                    $patterns = array(
                    );

                    $user_info = get_userdata($user_id);

                    $user_email = $user_info->user_email;

                    $this->send_email('update_card_info', $user_email, $patterns);
                    // Update Credit Card info send email//
                    $responce['status'] = 'ok';

                    $responce['message'] = 'Profile updated successfully';

                    $this->update_cvv_code_by_subscription_id($user_id, $subscribe_data['cvv'], $subscribe_data['number']);
                }
            }
            else
            {

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

                if (isset($_GET['cancel_subscription']) && $_GET['cancel_subscription'] == 'true')
                {

                    if (is_user_logged_in())
                    {
                        global $current_user;

                        get_currentuserinfo();

                        $user_id = $current_user->ID;

                        $sub_info = $this->get_subscription_info($user_id, array('Active', 'Expired'));

                        /* if( isset($_GET['expire']) && $_GET['expire'] == 'true' ){
                          echo "IN IF";
                          $sub_info = $this->get_subscription_info($user_id,array('Active','Expired'));
                          }
                          else{
                          $sub_info = $this->get_subscription_info($user_id);
                          } */

                        if ($sub_info)
                        {

                            $subscription_id = $sub_info->subscription_id;

                            $rsp = $this->bt_api->cancel_user_subscription($subscription_id);

                            $id = $sub_info->id;

                            $update_at = time();

                            if ($rsp['status'] == 'error')
                            {

                                $responce['status'] = 'error';

                                $responce['message'] = $rsp['data'];
                                $this->change_subscription_status($id, "Canceled", $update_at);
                                return $responce;
                            }
                            else
                            {

                                $this->change_subscription_status($id, $rsp['data']['subscription_status'], $update_at);

                                $responce['status'] = 'success';

                                $responce['message'] = 'Your subscription with subscription ID : ' . $subscription_id . ' have been canceled ';



                                //** save reason ** //

                                if (isset($_POST['studentAge']))
                                {
                                    $reason_data['student_age'] = $_POST['studentAge'];
                                }
                                if (isset($_POST['reason']))
                                {
                                    $reason_data['type'] = $_POST['reason'];
                                }

                                if ($reason_data['type'] == "Annet")
                                {
                                    $reason_data['type'] = $_POST['reason_txt'];
                                }

                                if (isset($_POST['reason_new']))
                                {

                                    $reason_data['text_other'] = $_POST['reason_new'];
                                }
                                if ($reason_data['text_other'] == 'Annet')
                                {
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

                                $patterns = array(
                                );

                                $user_info = get_userdata($user_id);

                                $user_email = $user_info->user_email;

                                $this->send_email('deactivate_email', $user_email, $patterns);

                                // ** ** //



                                return $responce;
                            }
                        }
                        else
                        {


                            $responce['status'] = 'error';

                            $responce['message'] = 'Your subscription with subscription ID : ' . $subscription_id . ' not found ';

                            return $responce;
                        }
                    }
                }
                else if (isset($_GET['renew_subscription']) && $_GET['renew_subscription'] == 'true')
                {

                    if (is_user_logged_in())
                    {
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



                if (isset($_POST['save_email_setting']) && $_POST['save_email_setting'] == 'true')
                {
                    $no_email = (isset($_POST['no_email'])) ? 1 : 0;

                    $all_email = (isset($_POST['all_email'])) ? 1 : 0;

                    $email_topics = (isset($_POST['email_topics'])) ? $_POST['email_topics'] : 0;



                    $setting = array(
                        "all_groups"  => $all_email,
                        "no_groups"   => $no_email,
                        "main_topics" => $email_topics
                    );



                    $setting = serialize($setting);

                    //var_dump($setting);

                    $user_data = wp_get_current_user();

                    $user_id = $user_data->ID;

                    //var_dump($user_id);

                    global $wpdb;

                    $q2 = "SELECT id FROM groups_email_settings  WHERE user_id = '$user_id' ";

                    $user = $wpdb->get_row($q2);



                    if ($user)
                    {

                        $q = " UPDATE groups_email_settings SET setting = '$setting' WHERE user_id = '$user_id' ";

                        $r = $wpdb->query($q);
                    }
                    else
                    {

                        $q = " INSERT INTO groups_email_settings (user_id, setting) VALUES (

                        '$user_id',

                        '$setting'

                        ) ";

                        $r = $wpdb->query($q);
                    }
                }



                if (isset($_GET['view']) && $_GET['view'] == 'show_user')
                {

                    if (isset($_POST['todo']) && $_POST['todo'] == 'deactivate')
                    {

                        deactivateSubscription();
                    }

                    if (isset($_POST['todo']) && $_POST['todo'] == 'activate')
                    {

                        activateSubscription();
                    }
                }





                return $responce;

            case '2':

                $responce['cse_key'] = $this->bt_api->cse_key;

                if (isset($_POST) && !empty($_POST))
                {

                    $valid = $this->validate_input_fields();

                    if ($valid['status'] == 'error')
                    {

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

                    if (!$user_id and email_exists($user_email) == false)
                    {

                        $random_password = wp_generate_password(7, false);

                        $_SESSION['random_password'] = $random_password;



                        $user_id = wp_create_user($user_name, $random_password, $user_email);

                        // ** SEND EMAIL USER   ** //

                        $patterns = array(
                            '{PASSWORD}' => $random_password
                        );

                        //$this->send_email('payment_email', $user_email, $patterns);
                        // ** ** //



                        wp_update_user(array('ID'         => $user_id, 'first_name' => $first_name, 'last_name'  => $last_name));

                        add_user_meta($user_id, 'phone', $phone);

                        add_user_meta($user_id, 'user_password', $random_password);
                    }
                    else
                    {
                        global $wpdb;

                        $table = $wpdb->prefix . "braintree_users_subscriptions";
                        $query = "SELECT * FROM $table where user_id=$user_id";
                        $user  = $wpdb->get_row($query);
                        if ($user->status != "Initiated")
                        {
                            $responce['step']    = '1';
                            $responce['status']  = 'error';
                            $message             = "Det er allerede opprettet en bruker p� denne adressen, "
                                    . "vennligst logg inn med tilsendt brukernavn og passord om du �nsker � gj�re endringer i abonnementet";
                            $responce['message'] = utf8_encode($message);
                            return $responce;
                        }
                        /* wp_update_user( array ( 'ID' => $user_id, 'first_name' => $first_name, 'last_name'=> $last_name ) ) ;

                          update_user_meta($user_id, 'phone', $phone); */
                    }



                    $created_at = time();

                    $this->save_subscription_info($user_id, '0', 'Initiated', $created_at, array('Initiated', 'Active', 'Expired'));

                    $user_info = get_userdata($user_id);



                    $responce['step'] = '2';

                    $user_info->phone = get_user_meta($user_id, 'phone');

                    $user_info->phone = $user_info->phone[0];

                    $responce['data']['user_info'] = $user_info;
                }
                else
                {
                    if (isset($_GET['renew_subscription']) && $_GET['renew_subscription'] == 'true')
                    {
                        if (is_user_logged_in())
                        {

                            global $current_user;

                            get_currentuserinfo();

                            $user_id = $current_user->ID;

                            $created_at = time();

                            $this->save_subscription_info($user_id, '0', 'Initiated', $created_at, array('Initiated', 'Active', 'Expired'));

                            $user_info = get_userdata($user_id);



                            $responce['step'] = '2';

                            $user_info->phone = get_user_meta($user_id, 'phone');

                            $user_info->phone = $user_info->phone[0];

                            $responce['data']['user_info'] = $user_info;
                        }
                    }
                }

                return $responce;

            case '3':

                $responce['data']['cse_key'] = $this->bt_api->cse_key;



                $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : $responce['user_id'];





                // if user is not valid 

                if (!$user_id || $user_id <= 0)
                {

                    $responce['status'] = 'error';

                    $responce['message'] = 'User was not found';

                    $responce['step'] = '1';

                    return $responce;
                }



                $user_info = get_userdata($user_id);

                if ($user_info)
                {



                    $responce['data']['user_info'] = $user_info;



                    $created_at = time();

                    $this->save_subscription_info($user_id, '0', 'Initiated', $created_at, array('Initiated', 'Active', 'Expired'));
                }
                else
                {

                    $responce['status'] = 'error';

                    $responce['message'] = 'User was not found';

                    $responce['step'] = '1';

                    return $responce;
                }

                return $responce;



            case '4':

                $user_id = (isset($_POST['user_id'])) ? $_POST['user_id'] : $responce['user_id'];

                $user_info = get_userdata($user_id);

                if ($user_info)
                {

                    $responce['data']['user_info'] = $user_info;
                }

                if (isset($_POST['save']) && $_POST['save'] == 'subscribe')
                {

                    $braintree_id = get_user_meta($user_id, 'braintree_id');

                    // if user brain tree id exist then 

                    if ($braintree_id)
                    {

                        $braintree_id = $braintree_id[0];



                        $subscribe_data['first_name'] = $_POST['first_name'];

                        $subscribe_data['last_name'] = $_POST['last_name'];

                        $subscribe_data['number'] = $_POST['number'];

                        $subscribe_data['month'] = $_POST['month'];

                        $subscribe_data['year'] = $_POST['exp-year'];

                        $subscribe_data['cvv'] = $_POST['cvv'];

                        $valid = $this->is_valid_subscribe_data($subscribe_data);

                        if ($valid['status'] == 'error')
                        {

                            $responce['status'] = 'error';

                            $responce['message'] = $valid['message'];

                            $responce['step'] = '2';

                            $responce['user_id'] = $user_id;

                            $responce['data']['subscribe_data'] = $subscribe_data;

                            return $responce;
                        }

                        $rsp = $this->bt_api->update_user_profile($braintree_id, $subscribe_data);

                        if ($rsp['status'] == 'error')
                        {

                            $responce['status'] = 'error';

                            $responce['message'] = $rsp['data'];

                            $responce['step'] = '2';

                            $responce['user_id'] = $user_id;

                            return $responce;
                        }

                        $responce['step'] = 4;
                        $sub_info         = $this->get_subscription_info($user_id, array('Active', 'Expired'));

                        if ($sub_info == NULL)
                        {

                            $rsp = $this->bt_api->subscribe_user_to_plan($braintree_id);

                            if ($rsp['status'] == 'error')
                            {

                                $responce['status'] = 'error';

                                $responce['message'] = $rsp['data'];

                                $responce['user_id'] = $user_id;

                                return $responce;
                            }
                            else
                            {

                                $sub_id = $rsp['data']['subscription_id'];

                                $sub_status = $rsp['data']['subscription_status'];

                                $created_at = time();

                                //$rsp['data']['result'];

                                $this->save_subscription_info($user_id, $sub_id, $sub_status, $created_at, array('Initiated', 'Active', 'Expired'), $subscribe_data['cvv'], $subscribe_data['number']);



                                // ** SEND EMAIL Subscribe again   ** //

                                $patterns = array(
                                );

                                $user_email = $user_info->user_email;

                                $this->send_email('reactivate_email', $user_email, $patterns);

                                // ** ** //
                            }
                        }
                        else
                        {

                            $responce['status'] = 'error';

                            $responce['message'] = "You are already subscribe to this plan";

                            $responce['user_id'] = $user_id;

                            return $responce;
                        }



                        return $responce;
                    }

                    // else create brain tree profile
                    else
                    {

                        $user_info->braintree_id = '0';



                        $subscribe_data['first_name'] = $_POST['first_name'];

                        $subscribe_data['last_name'] = $_POST['last_name'];

                        $subscribe_data['number'] = $_POST['number'];

                        $subscribe_data['month'] = $_POST['month'];

                        $subscribe_data['year'] = $_POST['exp-year'];

                        $subscribe_data['cvv'] = $_POST['cvv'];

                        $valid = $this->is_valid_subscribe_data($subscribe_data);

                        if ($valid['status'] == 'error')
                        {

                            $responce['status'] = 'error';

                            $responce['message'] = $valid['message'];

                            $responce['step'] = '3';

                            $responce['user_id'] = $user_id;

                            $responce['data']['subscribe_data'] = $subscribe_data;

                            return $responce;
                        }



                        $rsp = $this->bt_api->create_user_profile($subscribe_data);

                        if ($rsp['status'] == 'error')
                        {

                            $responce['status'] = 'error';

                            $responce['message'] = $rsp['data'];

                            $responce['step'] = '2';

                            $responce['user_id'] = $user_id;

                            $responce['data']['subscribe_data'] = $subscribe_data;



                            return $responce;
                        }
                        else
                        {

                            $responce['step'] = 4;

                            $braintree_id = $rsp['data'];

                            add_user_meta($user_id, 'braintree_id', $braintree_id);

                            $rsp = $this->bt_api->subscribe_user_to_plan($braintree_id);

                            if ($rsp['status'] == 'error')
                            {

                                $responce['status'] = 'error';

                                $responce['message'] = $rsp['data'];

                                $responce['user_id'] = $user_id;

                                $responce['data']['subscribe_data'] = $subscribe_data;

                                $random_password = get_user_meta($user_id, 'user_password', TRUE);

                                $patterns = array(
                                    '{PASSWORD}' => $random_password
                                );

                                $user_email = $user_info->user_email;

                                $this->send_email('payment_email', $user_email, $patterns);

                                return $responce;
                            }
                            else
                            {

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

            default:
                echo 'there';
                exit;

                return false;
        }
    }

    public function save_subscription_info($user_id, $subscription_id, $status, $created_at, $check_status = '', $cvv_code = '', $number = '')
    {

        global $wpdb;

        $table = $wpdb->prefix . "braintree_users_subscriptions";

        $sub_info = $this->get_subscription_info($user_id, $check_status);

        if ($sub_info != NULL)
        {

            $id = $sub_info->id;

            $existing_status = $sub_info->status;

            $update = "";

            if ($subscription_id != "0")
            {

                $update = ", subscription_id = '$subscription_id' ";
            }

            $update_cvv = "";

            if ($number != '')
            {

                $last_four_dig = substr($number, -4);

                $update_cvv = ", cvv = '$cvv_code' , number = '$last_four_dig' ";
            }

            if ($existing_status != 'Active')
            {

                $q = "UPDATE " . $table . " SET status = '$status' " . $update . $update_cvv . " WHERE id = '$id' ";
            }
        }
        else
        {

            $q = "INSERT INTO " . $table . " (user_id, subscription_id, status, created_at) VALUES ('$user_id','$subscription_id','$status','$created_at') ";
        }

        $r = $wpdb->query($q);

        return $r;
    }

    public function get_subscription_info($user_id, $status = 'Active')
    {

        global $wpdb;

        $table = $wpdb->prefix . "braintree_users_subscriptions";

        if (is_array($status))
        {

            $where = '';

            foreach ($status as $i => $stat)
            {

                if ($i == 0)
                {

                    $where .= " AND ( status = '$stat' ";
                }
                else
                {

                    $where .= " OR status = '$stat' ";
                }
            }

            $where = ($where != '') ? $where . " )" : '';

            $q = "SELECT * FROM " . $table . "  WHERE user_id = '$user_id'  " . $where;
        }
        else
        {

            $q = "SELECT * FROM " . $table . "  WHERE user_id = '$user_id' AND status = '$status' ";
        }

        return $wpdb->get_row($q);
    }

    public function change_subscription_status($id, $status, $time = '')
    {

        global $wpdb;

        $table = $wpdb->prefix . "braintree_users_subscriptions";

        $update_time = "";

        if ($time != '')
        {

            $update_time = " , updated_at = '$time' ";
        }

        $q = "UPDATE " . $table . " SET status = '$status' " . $update_time . " WHERE id = '$id' ";

        return $wpdb->query($q);
    }

    public function get_user_subscription_current_status($user_id)
    {

        global $wpdb;

        $table = $wpdb->prefix . "braintree_users_subscriptions";

        $q = "SELECT * FROM " . $table . " us

            WHERE us.user_id = '" . $user_id . "'

            ORDER BY us.id DESC LIMIT 1 ";

        return $wpdb->get_row($q);
    }

    public function update_cvv_code_by_subscription_id($user_id, $cvv_code, $number)
    {

        global $wpdb;

        $table = $wpdb->prefix . "braintree_users_subscriptions";

        $q2 = "SELECT * FROM " . $table . " WHERE user_id = '$user_id' AND ( status = 'Active' OR status = 'Initiated' ) ";

        $r = $wpdb->get_row($q2);

        if ($r)
        {

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

        if (isset($step))
        {

            if ($step > 0 && $step < 5)
            {

                return TRUE;
            }
        }

        return FALSE;
    }

    public function validate_input_fields()
    {

        $rsp = array('status'  => 'ok', 'message' => '');

        $user_email = $_POST['email'];

        $first_name = $_POST['fname'];

        $last_name = $_POST['lname'];

        $cell_num = $_POST['cellnumber'];

        $confirm_email = $_POST['retype_email'];

        $message = '';



        if ($user_email == '')
        {

            $message .= '<p> Email is required </p>';
        }

//        if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
//            $message .= '<p> Email address is not valid </p>';
//        }

        if ($first_name == '')
        {

            $message .= '<p> First Name is required </p>';
        }

        if ($last_name == '')
        {

            $message .= '<p> Last Name is required </p>';
        }

        if ($first_name == $last_name)
        {

            $message .= '<p> First Name & Last name cannot be same please enter another </p>';
        }

        /* if ($cell_num == '')
          {

          $message .= '<p> Cell phone is required </p>';
          }

          if (strlen($cell_num) <= 7)
          {

          $message .= '<p> Cell phone minimum 8 digits is required </p>';
          } */

        if ($confirm_email == '')
        {
            $message .= '<p> Confirm email is required</p>';
        }
        if ($confirm_email !== $user_email)
        {
            $message .= '<p> Your confirm email does not match with Email</p>';
        }

        if ($message != '')
        {

            $rsp['status'] = 'error';

            $rsp['message'] = $message;
        }

        return $rsp;
    }

    public function is_valid_subscribe_data($data)
    {



        $rsp = array('status'  => 'ok', 'message' => '');

        $first_name = $_POST['first_name'];

        $last_name = $data['last_name'];

        $number = $data['number'];

        $month = $data['month'];

        $year = $data['year'];

        $cvv = $data['cvv'];

        $message = '';



        if ($number == '')
        {

            $message .= '<p> Card Number is required </p>';
        }

        if ($first_name == '')
        {

            $message .= '<p> First Name is required </p>';
        }

        if ($last_name == '')
        {

            $message .= '<p> Last Name is required </p>';
        }

        if ($cvv == '')
        {

            $message .= '<p> CVV number is required </p>';
        }

        if ($month == '')
        {

            $message .= '<p> Expire month is required </p>';
        }

        if ($year == '')
        {

            $message .= '<p> Expire Year is required </p>';
        }

        if ($message != '')
        {

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



        foreach ($patterns as $pattern => $val)
        {

            $body = str_replace($pattern, $val, $body);

            $subject = str_replace($pattern, $val, $subject);
        }
        $attachments = array();
        if ($type = 'payment_email')
        {
            array_push($attachments, dirname(__FILE__) . '/julekort.pdf');
            array_push($attachments, dirname(__FILE__) . '/garanti.pdf');
        }



        /* if ($type == 'payment_email')
          {

          $attachments = dirname(__FILE__) . '/Kjøpsvilkår og 100% fornøyd garanti.pdf';

          $password = $patterns['{PASSWORD}'];

          //Send email to admin

          $admin = 'ksondresen@gmail.com';

          //$admin = 'muhammad.saleem@purelogics.net';

          $admin_msg = 'Mattevideo bruker har registrert med e-post: ' . $to . ' og passord:' . $password;

          $headers_admin = "From: $to <$to>" . "\r\n";



          wp_mail($admin, $subject, $admin_msg, $headers_admin);

          //$admin = 'numan.hassan@purelogics.net';
          //wp_mail( $admin, $subject, $admin_msg, $headers_admin);
          }
          else
          { */
//        $admin       = 'ksondresen@gmail.com';
//        $attachments = dirname(__FILE__) . '/julekort.pdf';
        $admin       = 'kontakt@mattevideo.no';
        $admin_body  = $body;
        $admin_body .= "<br />Email was sent to: $to";
        $admin_email = wp_mail($admin, $subject, $admin_body, $headers, $attachments);
        //}





        $mail = wp_mail($to, $subject, $body, $headers, $attachments);



        return true;
    }

    function load_script()
    {

        echo "<script type='text/javascript' src='" . $this->pluginUrl . '/js/bt-script.js' . "' ></script>";
    }

    function load_style()
    {

        echo "<link rel='stylesheet' href='" . $this->pluginUrl . '/css/bt-style.css' . "' type='text/css' media='all' />";
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

        $q2 = "

                CREATE TABLE IF NOT EXISTS " . $table . " (

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

        $q3 = "

                CREATE TABLE IF NOT EXISTS " . $table . " (

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

        //$tables[] = $wpdb->prefix . "braintree_users_subscriptions";

        foreach ($tables as $table)
        {

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

        if ($row)
        {

            $type = 'braintree';
        }
        else
        {

            $type = 'netaxept';
        }

        return $type;
    }

}

$bt_payment = new Braintree_payment();


if (!function_exists('update_logintime_braintree'))
{

    function update_logintime_braintree($user_login)
    {



        $user = get_userdatabylogin($user_login);

        $user_id = $user->ID;



        if ($user_id)
        {

            if (get_user_meta($user_id, 'last_login', true))
            {

                update_user_meta($user_id, 'last_login', date("Y-m-d H:i:s"));
            }
            else
            {

                add_user_meta($user_id, 'last_login', date("Y-m-d H:i:s"));
            }
        }
    }

}



add_action('wp_login', 'update_logintime_braintree', 10, 1);

//for sending testing emails
//add_action('init', function() {
//    $message = "Testing amazon ses";
//    wp_mail('', 'Testing', $message);
//});

?>

