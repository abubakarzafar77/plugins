<?php

require_once("netaxept/ClassMattevideoSubscription.php");
require_once("netaxept/ClassMattevideoPaymentController.php");

function deactivateSubscription() {

    $user_data = wp_get_current_user();

    if ($user_data->ID > 0) {
        $user_subr = new MattevideoSubscriptionController2($user_data->ID);
        if ($user_subr) {
            $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'), true);
            $subject = stripslashes($config['email_templates']['deactivate_email']['subject']);
            $headers = "MIME-Version: 1.0\n" .
                    "From: Mattevideo <ksondresen@gmail.com>\n" .
                    "Content-Type: text/html; charset=\"" .
                    get_option('blog_charset') . "\"\n";
            $msg = stripslashes($config['email_templates']['deactivate_email']['body']);
			//Sending copy to admin
			$admin = 'ksondresen@gmail.com';
			$admin_body = $msg;
			$admin_body .= "<br />Email was sent to: ".$user_data->user_email;
			$admin_email = wp_mail($admin, $subject, $admin_body, $headers);
			
            wp_mail($user_data->user_email, $subject, $msg, $headers);
            $user_subr->setStatus('Cancelled');
            delete_initiated_payment_after_cancel($user_data->ID);
        }
    }
}

function delete_initiated_payment_after_cancel($wp_user_id) {

    global $wpdb;
    $table = $wpdb->prefix . "mna_subscription";
    $q = "SELECT sbr_id FROM " . $table . " WHERE sbr_wp_user_id = '$wp_user_id' AND sbr_status = 'Cancelled' ORDER BY sbr_id DESC LIMIT 1 ";
    $row = $wpdb->get_row($q);

    if ($row) {
        $sub_id = $row->sbr_id;
        $table2 = $wpdb->prefix . "mna_payment";
        $q = "SELECT pmt_id,pmt_sbr_id FROM " . $table2 . " WHERE pmt_sbr_id = '$sub_id' AND pmt_status = 'Initiated' ORDER BY pmt_id DESC LIMIT 1 ";
        $row2 = $wpdb->get_row($q);
        if ($row2) {
            $pmt_id = $row2->pmt_id;
            $pmt_sbr_id = $row2->pmt_sbr_id;
            $table2 = $wpdb->prefix . "mna_payment";
            $q = "UPDATE " . $table2 . " SET pmt_sbr_id	= '$pmt_sbr_id$pmt_sbr_id' WHERE pmt_id = '$pmt_id' AND pmt_status = 'Initiated' ";

            $wpdb->query($q);
        }
    }
}

function activateSubscription() {

    global $wpdb;

    $user_data = wp_get_current_user();

    if ($user_data->ID > 0) {

        $user_sbr = new MattevideoSubscriptionController2($user_data->ID);

        //	check if sbr is active
        //	
        // EDIT PL - 15-May-2014
        // if ( $user_sbr->is_active() ){ 
        if ($user_sbr->is_Cancel()) {

            // check if there is initiated pyments 
            // EDIT PL - 15-May-2014
            $payments_controller = new MattevideoPaymentController2();
            $result = $payments_controller->get_initiated_payments_for_sbr($user_sbr->getSbrId());

            //	create a new initiated payment
            if (!$result) {

                //	get current payment

                $currentPayment = $payments_controller->get_current_payment_for_sbr($user_sbr->getSbrId());
                create_next_payment_on_subscription($currentPayment->getSbrId(), $currentPayment->get_to_date());
            }
        }
        else {
            // create a payment and 
        }

        //	create a new recurrying payment 
        //	register a sale to netaxept




        if ($user_sbr) {

            $config = json_decode(file_get_contents(dirname(__FILE__) . '/../config.json'), true);
            $subject = stripslashes($config['email_templates']['reactivate_email']['subject']);
            $headers = "MIME-Version: 1.0\n" .
                    "From: Mattevideo <ksondresen@gmail.com>\n" .
                    "Content-Type: text/html; charset=\"" .
                    get_option('blog_charset') . "\"\n";
            $msg = stripslashes($config['email_templates']['reactivate_email']['body']);
			
			//Sending copy to admin
			$admin = 'ksondresen@gmail.com';
			$admin_body = $msg;
			$admin_body .= "<br />Email was sent to: ".$user_data->user_email;
			$admin_email = wp_mail($admin, $subject, $admin_body, $headers);
			
            wp_mail($user_data->user_email, $subject, $msg, $headers);
            $user_sbr->setStatus('Active');
        }
    }
}

function create_next_payment_on_subscription($sbr_id = null, $from_date = null) {

    $options = get_current_options();

    if ($sbr_id != null && $from_date != null) {

        $nextPayment = new MattevideoPaymentController();
        // EDIT PL - 15-May-2014
        delete_initiated_payment_before_cancel($sbr_id);
        $result = $nextPayment->create_new_payment_on_sbr_with_from_date($sbr_id, $from_date, $options['subscription_price'], TRUE);
        // EDIT PL - 15-May-2014
    }
    else {
        _log('Netaxept.php: Could not create next payment on subscription sbr id or fromdata is null');
    }
}

function delete_initiated_payment_before_cancel($sub_id) {
    global $wpdb;

    $table2 = $wpdb->prefix . "mna_payment";
    $q = "SELECT pmt_id,pmt_sbr_id FROM " . $table2 . " WHERE pmt_sbr_id = '$sub_id' AND pmt_status = 'Initiated' ORDER BY pmt_id DESC LIMIT 1 ";
    $row2 = $wpdb->get_row($q);
    if ($row2) {
        $pmt_id = $row2->pmt_id;
        $pmt_sbr_id = $row2->pmt_sbr_id;
        $table2 = $wpdb->prefix . "mna_payment";
        $q = "UPDATE " . $table2 . " SET pmt_sbr_id	= '$pmt_sbr_id$pmt_sbr_id' WHERE pmt_id = '$pmt_id' AND pmt_status = 'Initiated' ";
        $wpdb->query($q);
    }
}

function get_current_options() {
    
    $config = parse_ini_file(plugin_dir_path( __FILE__ ).'netaxept/NetAxept.ini',true );
    
    $options = get_option($config['plugin_parameters']['pluginName']);

    if (!empty($options['go']) && $options['go'] == "dev") {

        $options['token'] = (!empty($options['dev_token'])) ? $options['dev_token'] : '';
        $options['wsdl'] = (!empty($options['dev_wsdl'])) ? $options['dev_wsdl'] : '';
        $options['terminal'] = (!empty($options['dev_terminal'])) ? $options['dev_terminal'] : '';
        $options['redirect_url'] = (!empty($options['dev_redirection_url'])) ? get_bloginfo('url') . '/' . $options['dev_redirection_url'] : '';
        $options['redirect_on_error'] = (!empty($options['dev_redirect_on_error'])) ? $options['dev_redirect_on_error'] : '';
    }
    else if (!empty($options['go']) && $options['go'] == 'prod') {

        $options['token'] = (!empty($options['prod_token'])) ? $options['prod_token'] : '';
        $options['wsdl'] = (!empty($options['prod_wsdl'])) ? $options['prod_wsdl'] : '';
        $options['terminal'] = (!empty($options['prod_terminal'])) ? $options['prod_terminal'] : '';
        $options['redirect_url'] = (!empty($options['prod_redirection_url'])) ? get_bloginfo('url') . '/' . $options['prod_redirection_url'] : '';

        $options['redirect_on_error'] = (!empty($options['prod_redirect_on_error'])) ? $options['prod_redirect_on_error'] : '';
    }

    return $options;
}

function monthDropdown($name="month", $class='', $selected=null)
{
	$dd = '<select name="'.$name.'" id="'.$name.'" class="medium '.$class.'" data-encrypted-name="month">';

	$months = array(
			1 => 'Januar (01)',
			2 => 'Februar (02)',
			3 => 'Mars (03)',
			4 => 'April (04)',
			5 => 'Mai (05)',
			6 => 'Juni (06)',
			7 => 'Juli (07)',
			8 => 'August (08)',
			9 => 'September (09)',
			10 => 'Oktober (10)',
			11 => 'November (11)',
			12 => 'Desember (12)');
	/*** the current month ***/
	$selected = is_null($selected) ? date('n', time()) : $selected;

	for ($i = 1; $i <= 12; $i++)
	{
			$dd .= '<option value="'.$i.'"';
			if ($i == $selected)
			{
					$dd .= ' selected';
			}
			/*** get the month ***/
			$dd .= '>'.ucfirst($months[$i]).'</option>';
	}
	$dd .= '</select>';
	return $dd;
}
?>
