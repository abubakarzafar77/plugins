<?php

/**
 * Description of braintree_admin
 *
 * @author 
 */
class Braintree_admin {

    protected $pluginPath;
    protected $pluginUrl;

    function __construct() {
        // Set Plugin Path
        $this->pluginPath = dirname(__FILE__);

        // Set Plugin URL
        $this->pluginUrl = WP_PLUGIN_URL . '/braintree-payment';
    }

    function create_admin_menu() {
        //this is the main item for the menu
        add_menu_page(
                'Brain Tree', //page title
                'Brain Tree', //menu title
                'manage_options', //capabilities
                'brain_tree_menu', //menu slug
                array($this, 'show_subscribe_user_page') //function
        );

        //this is a submenu
        add_submenu_page('brain_tree_menu', //parent slug
                'Configrations', //page title
                'Configrations', //menu title
                'manage_options', //capability
                'manage_setting', //menu slug
                array($this, 'show_configration_page')); //function
    //
        add_submenu_page(null, //parent slug
                'Subscribed Users', //page title
                'Subscribed Users', //menu title
                'manage_options', //capability
                'subscribed_users', //menu slug
                array($this, 'show_subscribe_user_page')); //function
        
        add_submenu_page('brain_tree_menu', //parent slug
                'Email Templates', //page title
                'Email Templates', //menu title
                'manage_options', //capability
                'bt-email_templates', //menu slug
                array($this, 'show_email_tamplates')); //function
        
        add_submenu_page('brain_tree_menu', //parent slug
                'Send Mass Emails', //page title
                'Send Mass Emails', //menu title
                'manage_options', //capability
                'bt-mass_emails', //menu slug
                array($this, 'show_mass_emails_page')); //function
        add_submenu_page('brain_tree_menu', //parent slug
                'Users paid outside Braintree', //page title
                'Users paid outside Braintree', //menu title
                'manage_options', //capability
                'bt-outside_user', //menu slug
                array($this, 'show_outside_users_page')); //function
        add_submenu_page('brain_tree_menu', //parent slug
                'Cancelling data form', //page title
                'Cancelling data form', //menu title
                'manage_options', //capability
                'bt-export-cancel-reason', //menu slug
                array($this, 'create_excel_file')); //function
        
        add_submenu_page('brain_tree_menu', //parent slug
                ' ', //page title
                ' ', //menu title
                'manage_options', //capability
                'bt-payment_detail', //menu slug
                array($this, 'show_payment_detail_page')); //function

        add_submenu_page('brain_tree_menu', //parent slug
            'Update login page text', //page title
            'Update login page', //menu title
            'manage_options', //capability
            'login_page_text', //menu slug
            array($this, 'login_page_text')); //function
        
        
        
        
       
    }

    function login_page_text(){
        if(isset($_POST['update'])){
            $filename = dirname(__FILE__).'/../login.json';
            $myfile = fopen($filename, "w") or die("Unable to open file!");
            $login_page_text = json_encode(array('html'=>$_POST['login_page_text']));
            fwrite($myfile, $login_page_text);
            fclose($myfile);
        }
        $page_text = json_decode(file_get_contents( dirname(__FILE__).'/../login.json' ), true);
        include dirname(__FILE__)."../../views/login_page_text.php";
    }
    
    function create_excel_file(){
        
        echo "<h2>Cancelling data form</h2>";

        global $wpdb;
        $table = $wpdb->prefix."braintree_cancel_reason";
        $filename = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/braintree-payment/cancel_reason.xls";
        
        $fp = fopen($filename, "wb");
        
//        $sql = "select DATE_FORMAT(FROM_UNIXTIME(created_at),'%Y-%m-%d') as created_at, email, text from ".$table." ORDER BY id DESC";
//        $result = mysql_query($sql);
//        $num = mysql_num_rows($result);
//        echo $num; echo "<br>";
//        $insert_rows .= 'Timestamp'."\t".'email'."\t".'Why are u canelling?'."\t";
//        $insert_rows .= "\n";
//        fwrite($fp, $insert_rows);
//        while($row = mysql_fetch_array($result))
//        {
//            $text = stripslashes($row['text']);
//            $insert = $row['created_at']. "\t" .$row['email']. "\t".$text;
//            $insert .= "\n";               //       serialize($assoc)
//            fwrite($fp, $insert);
//        }
//        if (!is_resource($fp))
//        {
//            echo "cannot open excel file";
//        }
        
        
		require_once dirname(__FILE__).'/google-api-php-client/src/Google_Client.php';
        require_once dirname(__FILE__).'/google-api-php-client/src/contrib/Google_DriveService.php';
 			
        if($_GET['save'] == 'true'){
            

            $client = new Google_Client();
            // Get your credentials from the console
            $client->setClientId('321283896077-jj75252kfr17l87d6vmrramcgfcbnb7b.apps.googleusercontent.com');
            $client->setClientSecret('xViinWwR_DjrRvfTP8KLvLFp');
            $client->setRedirectUri('http://dev.mattevideo.no/wp-admin/admin.php?page=bt-export-cancel-reason&save=true');
            $client->setScopes(array('https://www.googleapis.com/auth/drive'));
            

            $service = new Google_DriveService($client);

            //echo $authUrl = $client->createAuthUrl();exit;

            //Request authorization
//            print "Please visit:\n$authUrl\n\n";
//            print "Please enter the auth code:\n";
            $authCode = trim(@fgets(STDIN));

            // Exchange authorization code for access token
            $accessToken = $client->authenticate($authCode);
            $client->setAccessToken($accessToken);

            //Insert a file
            $file = new Google_DriveFile();
            $file->setTitle('Cancelling Data Form');
            $file->setDescription('Cancelling Data Form');
            //$file->setMimeType('application/vnd.google-apps.spreadsheet');
            $file->setMimeType('text/csv');

            $sql = "select DATE_FORMAT(FROM_UNIXTIME(created_at),'%Y-%m-%d') as created_at, email, student_age,text,type,text_other from ".$table." ORDER BY id DESC";
            $r = $wpdb->get_results($sql);
            $csv_data = "Timestamp,email,Why are u canelling?,Min alder,Student status,Har bruker av mattevideo vært den samme som har betalt abonnementet?\n";
            foreach($r as $row)
            {
                $text = stripslashes($row->text);
                $date = $row->created_at;
                $email = $row->email;
                $type = $row->type;
                $student_age = $row->student_age;
                $text_other = $row->text_other;
                $csv_data .= $date.",".$email.",".$text.",".$student_age.",".$type.",".$text_other."\n";
            }
            
            $data = $csv_data;
            $createdFile = $service->files->insert($file, array(
                  'data' => $data,
                  'mimeType' => 'text/csv',
                   'convert' => true,
                ));
            if($createdFile){
                echo "File Created";
            }
            die;
        }
		
		$client = new Google_Client();
		// Get your credentials from the console
		$client->setClientId('321283896077-jj75252kfr17l87d6vmrramcgfcbnb7b.apps.googleusercontent.com');
		$client->setClientSecret('xViinWwR_DjrRvfTP8KLvLFp');
		$client->setRedirectUri('http://dev.mattevideo.no/wp-admin/admin.php?page=bt-export-cancel-reason&save=true');
		$client->setScopes(array('https://www.googleapis.com/auth/drive'));
		

		$service = new Google_DriveService($client);

		$authUrl = $client->createAuthUrl();
        
        echo "<a href='".$authUrl."' > Save file to Google Drive </a> ";
        
        
        fclose($fp);
        
        
    }
    
    function show_outside_users_page(){
        global $wpdb;
        $sbr_table = $wpdb->prefix.$config['plugin_parameters']['tn_subscription'];
        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'add_user'){
            $payment = json_decode(file_get_contents( dirname(__FILE__).'/../payment.json' ), true);
            $count = count($payment);
            $payment[$count]['user_id'] = $_REQUEST['user_id'];
            $payment[$count]['start_date'] = $_REQUEST['start_date'];
            $payment[$count]['end_date'] = $_REQUEST['end_date'];
            file_put_contents( dirname(__FILE__).'/../payment.json', json_encode($payment));
        }elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete'){
            $payment = json_decode(file_get_contents( dirname(__FILE__).'/../payment.json' ), true);
            $payments = array();
            foreach($payment as $key=>$p){
                if($_REQUEST['key'] != $key){
                    $payments[] = $p;
                }
            }
            file_put_contents( dirname(__FILE__).'/../payment.json', json_encode($payments));
            echo "<script>window.location='admin.php?page=bt-outside_user&del=yes';</script>";
            exit;
        }
        //$sql = 'SELECT u.ID, u.user_nicename, u.user_login, u.user_email, sb.sbr_pan_hash FROM '.$wpdb->prefix.'users u LEFT JOIN '.$sbr_table.' sb ON u.ID = sb.sbr_wp_user_id WHERE sb.sbr_pan_hash IS NULL';
        $user_tbl = $wpdb->prefix.'users';
        $user_sub_tbl = $wpdb->prefix.'braintree_users_subscriptions';
        $sql = "SELECT * FROM  ".$user_tbl." WHERE ID NOT IN (SELECT user_id FROM ".$user_sub_tbl." WHERE status = 'Active' GROUP BY user_id ) ";
        $all_users = $wpdb->get_results($sql);
        $payments = json_decode(file_get_contents( dirname(__FILE__).'/../payment.json' ), true);
        include dirname(__FILE__)."../../views/outside_users.php";
    }
    
    function show_mass_emails_page(){
        if(isset($_POST['send'])){
				global $wpdb;
				$status = ($_REQUEST['subscription_status'] !== 'all')?' WHERE us.status ="'.$_REQUEST['subscription_status'].'"':'';
				$config = parse_ini_file( WP_CONTENT_DIR."/plugins/NetAxept/NetAxept.ini", true );
				$sbr_table = $wpdb->prefix.$config['plugin_parameters']['tn_subscription'];
				$payment_table = $wpdb->prefix.$config['plugin_parameters']['tn_payment'];
				$users_table = $wpdb->prefix."users";
                $user_sub = $wpdb->prefix . "braintree_users_subscriptions";
                $user_sub_log = $wpdb->prefix . "braintree_log";
				//$sql = "SELECT u.ID, u.user_email, u.display_name, sbr.sbr_status, sbr.sbr_id FROM $sbr_table sbr JOIN $users_table u ON sbr.sbr_wp_user_id=u.ID $status";
				$sql = "SELECT u.ID, u.user_email, u.display_name, us.status, us.subscription_id FROM $user_sub us JOIN $users_table u ON us.user_id = u.ID $status";
				$users_info = $wpdb->get_results($sql);
				$subject = $_POST['subject'];
				$headers = 	"MIME-Version: 1.0\n" .
					"From: Mattevideo <ksondresen@gmail.com>\n" .
					"Content-Type: text/html; charset=\"" .
					get_option('blog_charset') . "\"\n";
				echo '<div class="update-nag">';
				$total_sent = 0;
				ob_start();
				$body = $_POST['body'];
				$send_emails = false;
				if(isset($_POST['send_emails'])){
					$send_emails = true;
				}
				foreach($users_info as $user_info){
					$msg = stripslashes($body);
					$first_name = get_user_meta($user_info->ID, 'first_name', true);
					$last_name = get_user_meta($user_info->ID, 'last_name', true);
					$email = $user_info->user_email;
					$display_name = $user_info->display_name;
					$sbr_status = $user_info->status;
					$subscription_id = $user_info->subscription_id;
                    
					$sql = "SELECT * FROM ".$user_sub_log." WHERE subscription_id = '$subscription_id' AND type = 'subscription_charged_successfully' ";
                    $sub_info = $wpdb->get_row($sql);
                    if($sub_info){
                        $payment_date = $sub_info->created_at;
                    }
                    else{
                       $payment_date = 'No date found'; 
                    }
                
					echo 'Sending email to: <strong>'.$email.'</strong>';
					echo '<br />';
					flush();
					ob_flush();
					$msg = str_replace("{FIRST_NAME}", $first_name, $msg);
					$msg = str_replace("{LAST_NAME}", $last_name, $msg);
					$msg = str_replace("{EMAIL}", $email, $msg);
					$msg = str_replace("{SBR_STATUS}", $sbr_status, $msg);
					$msg = str_replace("{LAST_PAYMENT_DATE}", $payment_date, $msg);
					$total_sent++;
					ob_flush();
					if($send_emails){
                         
						$sentEmail = wp_mail($email, $subject, $msg, $headers);
						if($sentEmail){
							echo 'Sent email to: <strong>'.$email.'</strong>';
							echo '<br />';
						}else{
							echo 'Failed to send email to: <strong>'.$email.'</strong>';
							echo '<br />';
						}
					}else{
						echo "Email was Supposed to sent but not actually sent: <strong>".$email."</strong>";
						echo '<br />';
						$sentEmail = true;	
					}
					flush();
					ob_flush();
					sleep(1);
				}
				echo '</div>';
				$success = "Emails sent to $total_sent people.";
				include dirname(__FILE__)."../../views/mass_email.php";
								
			}else{
				include dirname(__FILE__)."../../views/mass_email.php";
			}
        
        
    }
    
    function show_email_tamplates(){
        $this->load_admin_script();
        $this->load_admin_style();
        
        if(isset($_POST['update']) && isset($_REQUEST['id'])){
            $config = json_decode(file_get_contents( dirname(__FILE__).'/../config.json' ), true);
            $config2 = $config;
            $new_template_text = $_REQUEST['template_text'];
            $new_template_subject = $_REQUEST['template_subject'];
            $config2['email_templates'][$_REQUEST['id']]['body'] = $new_template_text;
            $config2['email_templates'][$_REQUEST['id']]['subject'] = $new_template_subject;
            file_put_contents( dirname(__FILE__).'/../config.json', json_encode($config2));
            $config = json_decode(file_get_contents( dirname(__FILE__).'/../config.json' ), true);
            $email_template_to_edit = stripslashes($config['email_templates'][$_REQUEST['id']]['body']);
            $email_template_subject_to_edit = stripslashes($config['email_templates'][$_REQUEST['id']]['subject']);
            $template_type = $_REQUEST['id'];
            $success = "Emil text updated successfully.";
            include dirname(__FILE__)."../../views/email_template.php";

        }elseif(isset($_REQUEST['id'])){
            $config = json_decode(file_get_contents( dirname(__FILE__).'/../config.json' ), true);
            $email_template_to_edit = stripslashes($config['email_templates'][$_REQUEST['id']]['body']);
            $email_template_subject_to_edit = stripslashes($config['email_templates'][$_REQUEST['id']]['subject']);
            $template_type = $_REQUEST['id'];
            include dirname(__FILE__)."../../views/email_template.php";
        }else{
            $config = json_decode(file_get_contents( dirname(__FILE__).'/../config.json' ), true);
            include dirname(__FILE__)."../../views/email_template.php";
        }
    }

    function show_configration_page() {
        $this->load_admin_script();
        $this->load_admin_style();
        
        if($_POST['submit']){
            if($_POST['tab'] == 'api_key'){
                $merchant_id = $_POST['merchant_id'];
                $public_key = $_POST['public_key'];
                $private_key = $_POST['private_key'];
                $cse_key = $_POST['cse_key'];
                $is_valid = TRUE;
                $error = '';
                if($merchant_id == ''){
                    $error .= "<p>Merchant ID is required </p>";
                    $is_valid = FALSE;
                }
                if($public_key == ''){
                    $error .= "<p>Public Key is required </p>";
                    $is_valid = FALSE;
                }
                if($private_key == ''){
                    $error .= "<p>Private Key is required </p>";
                    $is_valid = FALSE;
                }
                if($cse_key == ''){
                    $error .= "<p>CSE key is required </p>";
                    $is_valid = FALSE;
                }
                
                if($is_valid){
                    $this->save_api_keys($merchant_id, $public_key, $private_key, $cse_key);
                    $responce['status'] = 'save';
                    $responce['message'] = 'Saved successfully';
                }
                else{
                    $responce['status'] = 'error';
                    $responce['message'] = $error;
                }
            }
            else if($_POST['tab'] == 'options'){
                
                $sandbox = (isset($_POST['sandbox'])) ? '1' : '0' ;
                $this->save_more_options($sandbox);
                $responce['status'] = 'save';
                $responce['message'] = 'Saved successfully';
            }
        }
        
        $setting = $this->get_settings();
        if(!$setting){
            $setting->merchant_id = '';
            $setting->public_key = '';
            $setting->private_key = '';
            $setting->cse_key = '';
        }
        
        require dirname(__FILE__)."../../views/admin_settings.php";
    }
    function show_subscribe_user_page(){
        global $wpdb;

        $migrated_count = 0;
        if(isset($_REQUEST['migrate'])){
            $table = $wpdb->prefix . "mna_subscription";
            $table2 = $wpdb->prefix . "braintree_users_subscriptions";
            $q = "SELECT sbr_wp_user_id FROM ".$table." WHERE sbr_status = 'Active'";
            $rows = $wpdb->get_results($q);
            foreach($rows as $row){
                //echo "<br />";
                $insert = "INSERT INTO $table2 SET user_id='{$row->sbr_wp_user_id}', subscription_id='', migrated_from='NetAxept', status='Expired', billing_start_date='0', billing_end_date='0', created_at='{time()}', updated_at='{time()}', cvv='', number='', is_mail_sent='0'";
                //echo "<br />";
                $migrated_count++;
                update_user_meta($row->sbr_wp_user_id, 'sbr_status', 'Expired');
                $wpdb->query($insert);
                $update = "UPDATE {$table} SET sbr_status='Expired' WHERE sbr_wp_user_id='{$row->sbr_wp_user_id}'";
                $wpdb->query($update);
            }
        }

        $filter = (isset($_GET['filter']) && $_GET['filter'] != '' ) ? $_GET['filter'] : '' ;
        $plan = (isset($_GET['plan']) && $_GET['plan'] != '' ) ? $_GET['plan'] : '' ;

        $users = $this->get_subscribe_users($filter, $plan);
        
        $all = $this->get_subscribe_users('');
        $total_all = ($all) ? count($all) : 0 ;

        //$initiated = 0;//$this->get_subscribe_users('Initiated');
        $total_initiated = 0;//($initiated) ? count($initiated) : 0 ;
        //$expired = 0;//$this->get_subscribe_users('Expired');
        $total_expired = 0;//($expired) ? count($expired) : 0 ;
        //$canceled = 0;//$this->get_subscribe_users('Canceled');
        $total_canceled = 0;//($canceled) ? count($canceled) : 0 ;
        //$active = 0;//$this->get_subscribe_users('Active');
        $total_active = 0;//($active) ? count($active) : 0 ;
        //$old = 0;//$this->get_subscribe_users('Active', 'mattevideo');
        $total_old = 0;//($old)?count($old):0;
        //$kr_99_plan = 0;//$this->get_subscribe_users('Active', '99_kr_plan');
        $total_99 = 0;//($kr_99_plan)?count($kr_99_plan):0;
        //$kr_149_plan = 0;//$this->get_subscribe_users('Active', '149_kr_plan');
        $total_149 = 0;//($kr_149_plan)?count($kr_149_plan):0;
        //$kr_199_plan = 0;//$this->get_subscribe_users('Active', '199_kr_plan');
        $total_199 = 0;//($kr_199_plan)?count($kr_199_plan):0;

        foreach($all as $a){
            switch($a->status){
                case 'Initiated':
                    $total_initiated++;
                    break;
                case 'Expired':
                    $total_expired++;
                    break;
                case 'Canceled':
                    $total_canceled++;
                    break;
                case 'Active':
                    $total_active++;
                    switch($a->subscription_plan){
                        case 'mattevideo':
                            $total_old++;
                            break;
                        case '99_kr_plan':
                            $total_99++;
                            break;
                        case '149_kr_plan':
                            $total_149++;
                            break;
                        case '199_kr_plan':
                            $total_199++;
                            break;
                        default:
                            break;
                    }
                    break;
                default:
                    break;
            }
        }

        $table = $wpdb->prefix . "mna_subscription";
        $q = "SELECT count(*) as CNT FROM ".$table." WHERE sbr_status = 'Active'";
        $row = $wpdb->get_row($q);
        $need_migration = false;
        if($row && $row->CNT > 0){
            $need_migration = $row->CNT;
        }

        require dirname(__FILE__)."../../views/subscribe_users.php";
    }
    function show_payment_detail_page(){
        
        $users_sub = $this->get_payment_details($_GET['user']);
        
        require dirname(__FILE__)."../../views/payment_detail_page.php";
    }
    
    function load_admin_script(){
        echo "<script type='text/javascript' src='". $this->pluginUrl .'/js/admin_page.js'."' ></script>";
    }
    function load_admin_style(){
        echo "<link rel='stylesheet' href='". $this->pluginUrl .'/css/admin_page.css'."' type='text/css' media='all' />";
    }
    
    function get_settings(){
        global $wpdb;
        $table = $wpdb->prefix . "braintree_setting";
        $q = "SELECT * FROM ".$table." LIMIT 1 ";
        return $wpdb->get_row($q);
    }

    public function save_api_keys($merchant_id, $public_key, $private_key, $cse_key) {
        global $wpdb;
        $table = $wpdb->prefix . "braintree_setting";
        $setting = $this->get_settings();
        if($setting){
            $q = "UPDATE ".$table." SET merchant_id = '$merchant_id' , public_key = '$public_key' , private_key = '$private_key' , cse_key = '$cse_key' WHERE id = 1 ";
        }
        else{
            $q = "INSERT INTO ".$table." (merchant_id,public_key,private_key,cse_key) VALUES ('$merchant_id','$public_key','$private_key','$cse_key') ";
        }
        return $wpdb->query($q);
        
    }
    
    public function save_more_options($sandbox){
        global $wpdb;
        $table = $wpdb->prefix . "braintree_setting";
        $setting = $this->get_settings();
        if($setting){
            $q = "UPDATE ".$table." SET sandbox = '$sandbox' WHERE id = 1 ";
        }
        else{
            $q = "INSERT INTO ".$table." (sandbox,merchant_id,public_key,private_key,cse_key) VALUES ('$sandbox','','','','') ";
        }
        return $wpdb->query($q);
    }

    public function get_subscribe_users($filter, $plan='') {
        global $wpdb;
        $result = array();
        $table = $wpdb->prefix . "braintree_users_subscriptions";
        $table2 = $wpdb->prefix . "users";
        $where = '';
        if($filter != ''){
            $where = " HAVING us.status = '$filter' ";
        }
        if($plan){
            $where .= " AND us.subscription_plan = '$plan'";
        }
        $limit = 1000;
        $offset = 0;
        do{
            $query_result = null;
            $q = "SELECT * FROM ".$table." us
                    JOIN ".$table2." u ON u.ID = us.user_id
                    WHERE us.id IN (SELECT MAX(us.id) FROM wptest_braintree_users_subscriptions us
                    GROUP BY us.user_id) ".$where." ORDER BY us.id DESC LIMIT $offset,$limit";
            $query_result = $wpdb->get_results($q);
            $offset = $limit+$offset;
            $result = array_merge($result, $query_result);
        }while($query_result);
        return $result;
    }

    public function get_payment_details($user_id) {
        global $wpdb;
        $table = $wpdb->prefix . "braintree_users_subscriptions";
        $table2 = $wpdb->prefix . "braintree_log";
//        $q = "SELECT * FROM ".$table." us
//            JOIN ".$table2." l ON l.subscription_id = us.subscription_id
//            WHERE us.user_id = '$user_id' AND type != 'subscription_charged_successfully' ORDER BY l.id DESC ";
        $q = "SELECT 
            us.subscription_id,
            l.billing_start_date,
            l.billing_end_date,
            l.webhook_date, 
            l.type,
            l.billed_amount
            FROM ".$table." us
            JOIN ".$table2." l ON l.subscription_id = us.subscription_id
            WHERE us.user_id = '$user_id' ORDER BY l.id DESC ";
        return $wpdb->get_results($q);
    }

}




?>
