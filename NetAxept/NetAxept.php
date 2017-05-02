<?php

/*

Plugin Name: Mattevideo - NetAxept
Description: NetAxept payment plugin for Mattevideo.   
Version: 2
Author: Øyvind Dahl
Author URI: http://sourcetagsandcodes.no
License: GPL
Last modified: 13.06.2013

*/


require_once("classes/ClassOrder.php");
require_once("classes/ClassTerminal.php");
require_once("classes/ClassRegisterRequest.php");
require_once("classes/ClassEnvironment.php");
require_once("classes/ClassCustomer.php");
require_once("classes/ClassQueryRequest.php");
require_once("classes/ClassProcessRequest.php");
require_once("classes/ClassRecurring.php");
require_once("classes/ClassMattevideoSubscription.php");
require_once("classes/ClassMattevideoPaymentController.php");



if(!class_exists("NetAxept")){
	
	class NetAxept{
		

		var $config = null;
		
		/*
		*	__construct
		*/
		function __construct(){
			
			$this->config = parse_ini_file( "NetAxept.ini", true );
			
		}
		
		
		/*
			Activate subscription
		*/
		private function activateSubscription(){
			
			global $wpdb;
				
			$user_data = wp_get_current_user();
						
			if($user_data->ID > 0){
				
				$user_sbr = new MattevideoSubscriptionController( $user_data->ID );
								
				//	check if sbr is active
                //	
				// EDIT PL - 15-May-2014
                // if ( $user_sbr->is_active() ){ 
                if ( $user_sbr->is_Cancel() ){ 
                    
					// check if there is initiated pyments 
                    // EDIT PL - 15-May-2014
					$payments_controller = new MattevideoPaymentController();
					$result = $payments_controller->get_initiated_payments_for_sbr( $user_sbr->getSbrId() );
					
					//	create a new initiated payment
					if( !$result ){
						
						//	get current payment
                        
						$currentPayment = $payments_controller->get_current_payment_for_sbr( $user_sbr->getSbrId() );
						$this->create_next_payment_on_subscription( $currentPayment->getSbrId(),  $currentPayment->get_to_date() );
						
					}
				
				} else {
					// create a payment and 
						
					
				}
				
				//	create a new recurrying payment 
				
				//	register a sale to netaxept
				
				
				
				
				if($user_sbr){
					
					$config = json_decode(file_get_contents( dirname(__FILE__).'/config.json' ), true);
					$subject = stripslashes($config['email_templates']['reactivate_email']['subject']);
					$headers = 	"MIME-Version: 1.0\n" .
								"From: Mattevideo <ksondresen@gmail.com>\n" .
								"Content-Type: text/html; charset=\"" .
								get_option('blog_charset') . "\"\n";
					$msg = stripslashes($config['email_templates']['reactivate_email']['body']);
					
					wp_mail( $user_data->user_email, $subject, $msg, $headers );
					$user_sbr->setStatus('Active');
										
				}
			}
		}
			
		
		
		
		
		
		/*
		*	Check user input	
		
		function checkUserInput($values){
			
			$inputOK = true;

			if($values['FirstName'] == "") $inputOK = false;
			if($values['LastName'] == "") $inputOK = false;
		
			if($values['CellPhone'] == ""){
				$inputOK = false;
				
			}else if((strlen($values['CellPhone']) != 8 && strlen($values['CellPhone']) != 10)){
				$inputOK = false;	
			}
			
			if($values['Email'] == "" || !is_email($values['Email'])) $inputOK = false;
			
			return $inputOK;
		}*/
		//changed by Sofiane
		function checkUserInput($values){
			
			$inputOK = '';

			if($values['FirstName'] == "") $inputOK .= '<br>Vennligst skriv inn et gyldig fornavn';
			if($values['LastName'] == "") $inputOK .= '<br>Vennligst skriv inn et gyldig etternavn';
		
			if($values['CellPhone'] == ""){
				$inputOK .= '<br>Vennligst skriv inn et gyldig telefonnummer';
				
			}else if((strlen($values['CellPhone']) != 8 && strlen($values['CellPhone']) != 10)){
				$inputOK .= '<br>Vennligst skriv inn et gyldig telefonnummer';	
			}
			
			if($values['Email'] == "" || !is_email($values['Email'])) $inputOK .= '<br>Vennligst skriv inn en gyldig epost';
			
			return $inputOK;
		}
		
		function checkAlreadyExistsWithActive($email){
			$user_id = email_exists($email);
			$inputOK = '';
			/*if($user_id == 548){
				$user_subscription = new MattevideoSubscriptionController( $user_id );
				if($user_subscription->is_active()){
					$inputOK .= '<br>Brukeren er allerede registrert, kan du logge deg inn.';
				}
				echo "<pre>";
					print_r($user_subscription);
				exit;
			}*/
			if($user_id){
				$user_subscription = new MattevideoSubscriptionController( $user_id );
				if($user_subscription->is_active()){
					$inputOK .= '<br>En bruker er allerede registrert på denne e-posten - vennligst logg inn.';
				}
			}
			return $inputOK;
		}
		
		
		
		
		
		
		
		
		/*
		*	create_client
		*/
		function create_client($wsdl){
			
			$client = null;
			
			if (strpos($_SERVER["HTTP_HOST"], 'uapp') > 0){
	 
				// Creating new client having proxy
				$client = new SoapClient($wsdl, array('proxy_host' => "isa4", 'proxy_port' => 8080, 'trace' => true,'exceptions' => true));
		  
			}else{
		  	
		  		// Creating new client without proxy
		  		$client = new SoapClient($wsdl, array('trace' => true, 'exceptions' => true));
		  
			}
			return $client;
		}
		
		
		
		
		
		/*
		*	create_mysql_tables
		*/
		function create_mysql_tables(){
			
			//	set up 
			global $wpdb;
			require_once( ABSPATH.'wp-admin/includes/upgrade.php' );
			
			
			//	create payment log table
			$logTableName = $wpdb->prefix.$this->config['plugin_parameters']['tn_log'];
			
			if($wpdb->get_var("SHOW TABLES LIKE  '$logTableName' ") != $logTableName){
				$sql_log = "CREATE TABLE ".$logTableName."(
						log_id DOUBLE UNSIGNED NOT NULL AUTO_INCREMENT,
						log_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
						log_pmt_id text,
						log_msg text,
						log_code text,
						UNIQUE KEY log_id (log_id)
						);";
						
				dbDelta($sql_log);
						
			}					
			
			
			
			//create error log
			$errorTableName = $wpdb->prefix.$this->config['plugin_parameters']['tn_errorlog'];
			
			if($wpdb->get_var("SHOW TABLES LIKE '$errorTableName'") != $errorTableName){
				
				$sql_error = "CREATE TABLE ".$errorTableName."(
								rrl_id DOUBLE NOT NULL AUTO_INCREMENT,
								rrl_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
								rrl_pmt_id DOUBLE UNSIGNED,
								rrl_msg TEXT,
								rrl_code TEXT,
								UNIQUE KEY rrl_id (rrl_id)
								);";
				dbDelta($sql_error);
										
			}
			
			
			//	Create subscription table
			$subscriptionTableName = $wpdb->prefix.$this->config['plugin_parameters']['tn_subscription'];
			
			if($wpdb->get_var("SHOW TABLES LIKE '$subscriptionTableName'") != $subscriptionTableName){
				$sql_sbr = "CREATE TABLE ".$subscriptionTableName."(
								sbr_id DOUBLE UNSIGNED NOT NULL AUTO_INCREMENT,
								sbr_wp_user_id DOUBLE UNSIGNED,
								sbr_status TEXT,
								sbr_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
								sbr_modified TIMESTAMP,
								sbr_pan_hash TEXT,
								UNIQUE KEY sbr_id (sbr_id)
								);";
								
				dbDelta($sql_sbr);	
				
			}
			
			//	Create payment table
			$pyamentTableName = $wpdb->prefix.$this->config['plugin_parameters']['tn_payment'];
			
			if($wpdb->get_var("SHOW TABLES LIKE '$pyamentTableName'") != $pyamentTableName) {
				
				$sql_pmt = "CREATE TABLE ".$pyamentTableName."(
							pmt_id DOUBLE UNSIGNED NOT NULL AUTO_INCREMENT,
							pmt_status TEXT,
							pmt_netaxept_id TEXT,
							pmt_sbr_id DOUBLE UNSIGNED NOT NULL,
							pmt_period_from DATE DEFAULT '0000-00-00' NOT NULL,
							pmt_period_to DATE DEFAULT '0000-00-00' NOT NULL,
							pmt_payment_id TEXT,
							pmt_amount INT,
							UNIQUE KEY pmt_id (pmt_id)
							);";
							
				dbDelta($sql_pmt);	
				
			}
		}
		
		
		
		/*
			Create next payment
			Creating a new payment that can be collected in the future

		*/
		private function create_next_payment_on_subscription ( $sbr_id = null, $from_date = null ){
			
			$options = $this->get_current_options();
						
			if($sbr_id != null && $from_date != null){
				
				$nextPayment = new MattevideoPaymentController();
                // EDIT PL - 15-May-2014
                $this->delete_initiated_payment_before_cancel($sbr_id);
				$result = $nextPayment->create_new_payment_on_sbr_with_from_date( $sbr_id, $from_date, $options['subscription_price'] , TRUE); 
				// EDIT PL - 15-May-2014
			} else {
				_log( 'Netaxept.php: Could not create next payment on subscription sbr id or fromdata is null' );	
			}
			
		}
		
		
		
		/*
			Deactivate subscription
		*/
		
		private function deactivateSubscription(){
			
			$user_data = wp_get_current_user();
			
			if($user_data->ID > 0){
				$user_subr = new MattevideoSubscriptionController($user_data->ID);
				
				if($user_subr){
					$config = json_decode(file_get_contents( dirname(__FILE__).'/config.json' ), true);
					$subject = stripslashes($config['email_templates']['deactivate_email']['subject']);
					$headers = 	"MIME-Version: 1.0\n" .
								"From: Mattevideo <ksondresen@gmail.com>\n" .
								"Content-Type: text/html; charset=\"" .
								get_option('blog_charset') . "\"\n";
					$msg = stripslashes($config['email_templates']['deactivate_email']['body']);
					
					wp_mail( $user_data->user_email, $subject, $msg, $headers );
					$user_subr->setStatus('Cancelled');
                    // EDIT PL - 15-May-2014
                    $this->delete_initiated_payment_after_cancel($user_data->ID);
				
				}
			}
		}
		
		// EDIT PL - 15-May-2014
		function delete_initiated_payment_after_cancel($wp_user_id){
            
            global $wpdb;
            $table = $wpdb->prefix . "mna_subscription";
            $q = "SELECT sbr_id FROM ".$table." WHERE sbr_wp_user_id = '$wp_user_id' AND sbr_status = 'Cancelled' ORDER BY sbr_id DESC LIMIT 1 ";
            $row = $wpdb->get_row($q);
            
            if($row){
                $sub_id = $row->sbr_id;
                $table2 = $wpdb->prefix . "mna_payment";
                $q = "SELECT pmt_id,pmt_sbr_id FROM ".$table2." WHERE pmt_sbr_id = '$sub_id' AND pmt_status = 'Initiated' ORDER BY pmt_id DESC LIMIT 1 ";
                $row2 = $wpdb->get_row($q);
                if($row2){
                    $pmt_id = $row2->pmt_id;
                    $pmt_sbr_id = $row2->pmt_sbr_id;
                    $table2 = $wpdb->prefix . "mna_payment";
                    $q = "UPDATE ".$table2." SET pmt_sbr_id	= '$pmt_sbr_id$pmt_sbr_id' WHERE pmt_id = '$pmt_id' AND pmt_status = 'Initiated' ";
                    
                    $wpdb->query($q);
                }
            }
        }
        function delete_initiated_payment_before_cancel($sub_id){
            
            global $wpdb;
            
            $table2 = $wpdb->prefix . "mna_payment";
            $q = "SELECT pmt_id,pmt_sbr_id FROM ".$table2." WHERE pmt_sbr_id = '$sub_id' AND pmt_status = 'Initiated' ORDER BY pmt_id DESC LIMIT 1 ";
            $row2 = $wpdb->get_row($q);
            if($row2){
                $pmt_id = $row2->pmt_id;
                $pmt_sbr_id = $row2->pmt_sbr_id;
                $table2 = $wpdb->prefix . "mna_payment";
                $q = "UPDATE ".$table2." SET pmt_sbr_id	= '$pmt_sbr_id$pmt_sbr_id' WHERE pmt_id = '$pmt_id' AND pmt_status = 'Initiated' ";
                $wpdb->query($q);
            }
            
        }
		
		
		
		/*
		*	get_current_options
		*/
		function get_current_options(){
			$options = get_option($this->config['plugin_parameters']['pluginName']);
			
			if(!empty($options['go']) && $options['go'] == "dev"){
				
				$options['token'] = (!empty($options['dev_token'])) ? $options['dev_token'] : '';
				$options['wsdl']= (!empty($options['dev_wsdl'])) ? $options['dev_wsdl'] : '';
				$options['terminal'] = (!empty($options['dev_terminal'])) ? $options['dev_terminal'] : '';
				$options['redirect_url'] = (!empty($options['dev_redirection_url'])) ? get_bloginfo('url').'/'.$options['dev_redirection_url'] : '';
				$options['redirect_on_error'] = (!empty($options['dev_redirect_on_error'])) ? $options['dev_redirect_on_error'] : '';
			
			}else if(!empty($options['go']) && $options['go'] == 'prod'){
				
				$options['token'] = ( !empty($options['prod_token'])) ? $options['prod_token'] : '';
				$options['wsdl']  = ( !empty($options['prod_wsdl'])) ? $options['prod_wsdl'] : '';
				$options['terminal'] = ( !empty($options['prod_terminal'])) ? $options['prod_terminal'] : '';
				$options['redirect_url'] = (!empty($options['prod_redirection_url'])) ? get_bloginfo('url') . '/' . $options[ 'prod_redirection_url' ] : '';
				
				$options['redirect_on_error'] = ( !empty( $options[ 'prod_redirect_on_error' ])) ? $options[ 'prod_redirect_on_error' ] : '';
		
			}
			
			return $options;
		}
		
		
		
		
		
		/*
		*	getTransaction
		*
		*/
		function getTransaction($transactionId){
			
			global $wpdb;
			
			$transaction = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.$this->log_tableName." WHERE transactionId = '$transactionId'");
			
			return $transaction;
			
		}
		
		
		
		
		
		
		
		/*
		*	netaxept_delegate
		*	for options
		*/
		function netaxept_delegate(){
			
			if( !empty($_REQUEST['action']) ){
			
				$action = strip_tags($_REQUEST['action']);
				
				switch($action){
					
					case "subscription":
						$sbr_id = (!empty($_GET['sbr_id']) ? $_GET['sbr_id'] : null);
						$sbr_id = (!empty($_POST['subscription']) ? $_POST['subscription'] : $sbr_id);

						//	if admin has clicked on "collect_payment" button
						if(!empty($_POST['todo']) && $_POST['todo'] == 'collect_payment'){
							
							if( !empty($_POST['pmt_id']) && !empty($sbr_id)){
								$this->register_recurring_payment( $_POST['pmt_id'], $sbr_id );
								
							} else {
								_log( 'NetAxept: netaxept_delegate: could not collect, id was null' );
							}
						}
						
						if($sbr_id){
							if(isset($_GET['sbr_id'])){
								$this->show_subscription($sbr_id);
							}else{
								$page = (!empty($_POST['status']) ? '&status='.$_POST['status'] : '');
								$page .= (!empty($_POST['collect']) ? '&collect='.$_POST['collect'] : '');
								$page .= (!empty($_POST['subscription']) ? '&subscription='.$_POST['subscription'] : '');
								$page .= (!empty($_POST['recursive']) ? '&recursive='.$_POST['recursive'] : '');
								wp_redirect("?page=netaxept".$page);
							}
						}
						
						break;
					
					case "cancel_subscription":
						global $wpdb;
						$sbr_id = (!empty($_GET['sbr_id']) ? $_GET['sbr_id'] : null);
						$page = (!empty($_GET['status']) ? '&status='.$_GET['status'] : '');
						$tableNameWithPrefix = $wpdb->prefix.$this->config['plugin_parameters']['tn_subscription'];
						$data['sbr_status'] = 'Cancelled';
						$where['sbr_id'] = $sbr_id;
						$wpdb->update($tableNameWithPrefix, $data, $where);
						wp_redirect("?page=netaxept".$page);
						exit;
						break;
						
					case "activate_subscription":
						global $wpdb;
						$sbr_id = (!empty($_GET['sbr_id']) ? $_GET['sbr_id'] : null);
						$page = (!empty($_GET['status']) ? '&status='.$_GET['status'] : '');
						$tableNameWithPrefix = $wpdb->prefix.$this->config['plugin_parameters']['tn_subscription'];
						$data['sbr_status'] = 'Active';
						$where['sbr_id'] = $sbr_id;
						$wpdb->update($tableNameWithPrefix, $data, $where);
						wp_redirect("?page=netaxept".$page);
						exit;
						break;
					
					
					case "options":
						$this->show_options();
						break;
			
					case "save_settings":
						
						$merchantid = strip_tags($_POST['netaxept_merchantid']);
						$subscription_price = ($_POST['netaxept_subscription_price']);
						$netaxept_recurring = $_POST['netaxept_recurring'];
						$service_type = $_POST['netaxept_servicetype'];
						
						$token_dev = $_POST['netaxept_token_dev'];
						$wsdl_dev = $_POST['netaxept_wsdl_dev'];
						$redirection_dev = $_POST['netaxept_redirection_dev'];
						$redirection_url_dev = $_POST['netaxept_redirection_url_dev'];
						$redirect_on_error_dev = $_POST['netaxept_redirect_on_error_dev'];
						$terminal_dev = $_POST['netaxept_terminal_dev'];
						$terminal_dev_mobile = $_POST['netaxept_terminal_dev_mobile'];
						
						$token_prod = $_POST['netaxept_token_prod'];
						$wsdl_prod = $_POST['netaxept_wsdl_prod'];
						$redirection_prod = $_POST['netaxept_redirection_prod'];
						$redirection_url_prod = $_POST['netaxept_redirection_url_prod'];
						$redirect_on_error_prod = $_POST['netaxept_redirect_on_error_prod'];
						$terminal_prod = $_POST['netaxept_terminal_prod'];
						$terminal_prod_mobile = $_POST['netaxept_terminal_prod_mobile'];
						
						$go = $_POST['netaxept_go'];
						
						$values = array('merchantid' => $merchantid,
										'subscription_price' => $subscription_price,
										'recurring' => $netaxept_recurring,
										'service_type' => $service_type,
										
										'dev_token' => $token_dev,
										'dev_wsdl' => $wsdl_dev,
										'dev_redirection' => $redirection_dev,
										'dev_redirection_url' => $redirection_url_dev,
										'dev_redirect_on_error' => $redirect_on_error_dev,
										'dev_terminal' => $terminal_dev,
										'dev_terminal_mobile' => $terminal_dev_mobile,
										
										'prod_token' => $token_prod,
										'prod_wsdl' => $wsdl_prod,
										'prod_redirection' => $redirection_prod,
										'prod_redirection_url' => $redirection_url_prod,
										'prod_redirect_on_error' => $redirect_on_error_prod,
										'prod_terminal' => $terminal_prod,
										'prod_terminal_mobile' => $terminal_prod_mobile,
										
										'go' => $go
										
										);
										
						$this->save_settings($values);
						echo "<h2>New settings saved</h2>";
						$this->show_main_menu();
					
				}
				
			}else{
				$this->show_main_menu();
			}
		}
		
		
		
		
		/*
			register_recurring_payment
		*/
		private function register_recurring_payment( $pmt_id, $sbr_id ){
		
			
			if( !$pmt_id || !$sbr_id ){
				_log( 'Error: sbr_id: ' . $sbr_id . ' and pmt_id: ' . $pmt_id );
				
			}
			
			$user_sbr = new MattevideoSubscriptionController (0, $sbr_id );
			
			$wp_user_id = $user_sbr->getWpUserId();
			
			$current_payment = new MattevideoPaymentController ( 0, 0, 0, 0, 0, $pmt_id );
			
			if( $user_sbr ){
				
				$options 		= $this->get_current_options();
				
				$orderNumber	= md5(uniqid('', true));
				$ArrayOfItem 	= null; 
				
				//	new order / ny bestilling
				$subscriptionPrice = $options['subscription_price'] * 100;
				$currencyCode = $this->config['order_parameters']['currencyCode'];
				$force3DSecure = $this->config['order_parameters']['force3DSecure'];
				$updateStorePaymentInfo = $this->config['order_parameters']['updateStorePaymentInfo'];
				$mattevideo_order = new Order(	$subscriptionPrice,
												$currencyCode, 
												$force3DSecure, 
												$ArrayOfItem, 
												$orderNumber,
												$updateStorePaymentInfo );
				
				//	new enviroment
				$mattevideo_environment = new Environment(	$this->config['environment_parameters']['language'], 
															$this->config['environment_parameters']['OS'], 
															$this->config['environment_parameters']['WebServicePlatform']);
				//	 new Terminal
				$mattevideo_terminal = new Terminal(	$this->config['terminal_parameters']['autoAuth'], 
														$this->config['terminal_parameters']['paymentMethodList'], 
														$this->config['terminal_parameters']['language'], 
														$this->config['terminal_parameters']['orderDescription'], 
														$options['redirect_on_error'], 
														$options['redirect_url'].'?todo=recurring_payment' );
				
				//	new recurring
				/*
					$ExpiryDate,
			        $Frequency,
			        $Type,
			        $PanHash
				*/
				$expiryDate = '';//date('Ymd', strtotime('next year'));
		        $frequency = '';//30; //days
		        $recurringType = 'S';
		        $panHash = $user_sbr->get_pan_Hash();
			    
			    $mattevideo_recurring = new Recurring(	$expiryDate,
														$frequency,
														$recurringType,
														$panHash);
				
				//	new Customer
			    $Email = get_user_meta($wp_user_id, 'nickname', true);
			    $FirstName = get_user_meta($wp_user_id, 'first_name', true);
			    $LastName = get_user_meta($wp_user_id, 'last_name', true);
			    $PhoneNumber = '';
				
				$Address1 = '';
				$Address2 = '';
				$CompanyName = '';
				$CompanyRegistrationNumber = '';
				$Country = '';
				$customerNumber = '';
				$PostCode = '';
				$SocialSecurityNumber = '';
				$Town = '';
	
				$mattevideo_customer = new Customer(	$Address1, 
														$Address2, 
														$CompanyName, 
														$CompanyRegistrationNumber, 
														$Country, 
														$customerNumber, 
														$Email, 
														$FirstName, 
														$LastName, 
														$PhoneNumber, 
														$PostCode, 
														$SocialSecurityNumber, 
														$Town);
				
				$service_type = 'C';
				$transactionid = '';
				$transactionReconRef = '';
				
				$RegisterRequest = new RegisterRequest( $this->config['register_request_parameters']['AvtaleGiro'], 
														$this->config['register_request_parameters']['CardInfo'],
														$mattevideo_customer,
														$this->config['register_request_parameters']['description'],
														$this->config['register_request_parameters']['DnBNorDirectPayment'],
														$mattevideo_environment,
														$this->config['register_request_parameters']['MicroPayment'],
														$mattevideo_order,
														$mattevideo_recurring,
														$service_type,
														$mattevideo_terminal,
														$transactionid, //transactionid
														$transactionReconRef
														);
	
				
				$InputParametersOfRegister = array(	"token" => $options['token'], 
													"merchantId" => $options['merchantid'], 
													"request" => $RegisterRequest, 
													"soap_version" => SOAP_1_1);
				
				try{
				
					$client = $this->create_client( $options['wsdl'] );
					
					$OutputParametersOfRegister = $client->__call( 'Register' , array( "parameters" => $InputParametersOfRegister ));
					
					$transactionId = $OutputParametersOfRegister->RegisterResult->TransactionId; 
				 	
				 	/*
					*	save transaction id if everything is ok. Then process the sale to collect the ammount
					*/
					if( $client && $transactionId ){
						
						
						// update payment with transaction id
						$current_payment->setTransactionId( $transactionId );

						
						####	PROCESS OBJECT	####
						$process_description = 'Payment for period ' . $current_payment->getPeriod();
						$process_operation = 'SALE';
						$process_amount = '9900';
						$process_transactionId = $transactionId;
						$process_transactionReconRef = '';
						$ProcessRequest = new ProcessRequest( 	$process_description, 
																$process_operation, 
																$process_amount, 
																$process_transactionId, 
																$process_transactionReconRef );
						
						//	input parameters of process	
						$InputParametersOfProcess = array( 	"token"			=> $options['token'], 
															"merchantId" 	=> $options['merchantid'], 
															"request" 		=> $ProcessRequest );

						$OutputParametersOfProcess = '';
						
						try {
							$OutputParametersOfProcess = $client->__call( 'Process' , array( "parameters" => $InputParametersOfProcess ));
						
						} catch ( SoapFault $error ){
							_log( 'NetAxept.php: register_recurring_payment: Error in client call: ' . print_r($error) );
						}
						
						if($OutputParametersOfProcess){
						
							$ProcessResult = $OutputParametersOfProcess->ProcessResult; 
							
							if ($ProcessResult->ResponseCode == "OK"){
								
								//	update this payment with Collected
								$current_payment->setStatus( 'Collected' );	
								
								//	create new initiated payment for next month
								$this->create_next_payment_on_subscription( $current_payment->getSbrId(), $current_payment->get_to_date() );	
							
							} else {
								$currentPayment->updatePaymentField( 'pmt_status', 'Error: ' .$ProcessResult->ResponceCode );
															
							}
								
						}else {
							_log( 'NetAxept.php: $OutputParametersOfProcess was null' );
						
						}					
					} else {
						_log( 'NetAxept.php: $client and/or $transactionId was null' );
					}
						
				}catch( SoapFault $fault ){
					echo "<pre>";
					print_r( $fault );
					//_log( 'Netaxept.php: register_recurring_payment: ' . print_r( $fault ).'');
					echo "</pre>";
				}
				
			} else {
				_log( 'Netaxept.php: register_recurring_payment: could not create payment, no sbr found with sbr id' );
			}	
		}
		
		
		
		/*
			Register sale
		*/
		
		private function netaxept_register_sale ( $transactionId ) {
			
			if ( $transactionId ) {
				
				$options = $this->get_current_options();
				
				$client = $this->create_client($options['wsdl']);
				
				
				
				####	PROCESS OBJECT	####
				$ProcessRequest = new ProcessRequest( 	'Sale', 
														'SALE', 
														'', 
														$transactionId, 
														'' );
				
				//	input parameters of process	
				$InputParametersOfProcess = array("token"=> $options['token'], "merchantId" => $options['merchantid'], "request" => $ProcessRequest);
				
				$OutputParametersOfProcess = '';
				
				try{
					$OutputParametersOfProcess = $client->__call('Process' , array("parameters"=>$InputParametersOfProcess));
					
				}catch(SoapFault $error){
					
					_log( 'NetAxept.php: netaxept_register_sale: Error in client call' );
				}
				
				if($OutputParametersOfProcess){
					$ProcessResult = $OutputParametersOfProcess->ProcessResult; 
					
					if ($ProcessResult->ResponseCode == "OK"){
					
						
						// return true
				
					
					}else{
					
						// return false
					}

				}else{
					_log( 'Netaxept.php: netaxept_register_sale: $OutputParametersOfProcess is null' );
				}
				
					
			} else {
				_log( 'Netaxept.php: netaxept_register_sale: no transaction id' );
			}
		
			
		}
		
		
		
		
		/*
		*	save_settings
		*
		*/
		function save_settings($values){
			
			update_option( $this->config['plugin_parameters']['plugin_name'], $values );
			
		}
		
		
		
		
		
		/*
		*	subscription
		*	Shows the log
		*/
		function show_main_menu(){
			global $wpdb;
			
			$status = (isset($_REQUEST['status'])?" WHERE sbr_status='".$_REQUEST['status']."'":'');
			$tableNameWithPrefix = $wpdb->prefix.$this->config['plugin_parameters']['tn_subscription'];
			$Active = $wpdb->get_var("SELECT COUNT(*) FROM $tableNameWithPrefix WHERE sbr_status='Active'");
			$Cancelled = $wpdb->get_var("SELECT COUNT(*) FROM $tableNameWithPrefix WHERE sbr_status='Cancelled'");
			$Expired = $wpdb->get_var("SELECT COUNT(*) FROM $tableNameWithPrefix WHERE sbr_status='Expired'");
			$Initiated = $wpdb->get_var("SELECT COUNT(*) FROM $tableNameWithPrefix WHERE sbr_status='Initiated'");
			$Started = $wpdb->get_var("SELECT COUNT(*) FROM $tableNameWithPrefix WHERE sbr_status='Started'");
			$All = $wpdb->get_var("SELECT COUNT(*) FROM $tableNameWithPrefix");
			$rows = $wpdb->get_results("SELECT * FROM $tableNameWithPrefix $status ORDER BY sbr_id DESC");
			$user_id_rows = $wpdb->get_results("SELECT sbr_wp_user_id FROM $tableNameWithPrefix ", 'ARRAY_A');
			$userIds = array();
			foreach($user_id_rows as $id){
				array_push($userIds, $id['sbr_wp_user_id']); 
			}
			
			$data_view_list_subscriptions = array('heading' => 'Subscriptions',
													'user_ids' => $userIds);
			
            
            // EDIT PL - 19-May-2014
            // 
            // Fix link array
            $fix_sub_array = range(1109,1123);
            $fix_sub_array[] = 1089;
            $fix_sub_array[] = 1079;
            
            
            if(isset($_GET['fix_payment']) && $_GET['fix_payment'] == 'fixit'){
                
                
        
    
                $sub_id = $_GET['sub_id'];

                global $wpdb;

                $table2 = $wpdb->prefix . "mna_payment";
                $q = "SELECT pmt_id,pmt_sbr_id FROM ".$table2." WHERE pmt_sbr_id = '$sub_id' AND pmt_status = 'Initiated' ORDER BY pmt_id DESC LIMIT 1 ";
                $row2 = $wpdb->get_row($q);
                if($row2){
                    $pmt_id = $row2->pmt_id;
                    $pmt_sbr_id = $row2->pmt_sbr_id;
                    $table2 = $wpdb->prefix . "mna_payment";
                    $q = "UPDATE ".$table2." SET pmt_sbr_id	= '$pmt_sbr_id$pmt_sbr_id' WHERE pmt_id = '$pmt_id' AND pmt_status = 'Initiated' ";
                    $wpdb->query($q);
                }
                

                $select_query = 'select * from wptest_mna_payment where pmt_sbr_id = ' . $sub_id . ' and pmt_status LIKE "Collected" order by pmt_period_to desc limit 1';
                $payment = $wpdb->get_row( $select_query );
                $from_date = $payment->pmt_period_to;
                
                $options = $this->get_current_options();
                $nextPayment = new MattevideoPaymentController();
                
				$result = $nextPayment->create_new_payment_on_sbr_with_from_date( $sub_id, $from_date, $options['subscription_price'] , TRUE); 
                

            }
            // EDIT PL - 19-May-2014
            
            
			include 'views/view_list_subscriptions.php';
		}
		
		
		
		
		
		
		/*
		*	show_options
		*
		*/
		function show_options(){
			$values = get_option($this->config['plugin_parameters']['plugin_name']);
			$recurring_true = ($values['recurring'] == 'true') ? 'SELECTED="SELECTED"': '';
			$dev_selected = ($values['go'] == "dev") ? ' SELECTED' : '';
			$prod_selected = ($values['go'] == "prod") ? ' SELECTED' : '';
			$siteUrl = get_bloginfo('url');
			
			include 'views/view_list_options.php';
			
		}
		
		
		/*
		*	show_emails
		*
		*/
		
		function show_emails(){
			if(isset($_POST['update']) && isset($_REQUEST['id'])){
				$config = json_decode(file_get_contents( dirname(__FILE__).'/config.json' ), true);
				$config2 = $config;
				$new_template_text = $_REQUEST['template_text'];
				$new_template_subject = $_REQUEST['template_subject'];
				$config2['email_templates'][$_REQUEST['id']]['body'] = $new_template_text;
				$config2['email_templates'][$_REQUEST['id']]['subject'] = $new_template_subject;
				file_put_contents( dirname(__FILE__).'/config.json', json_encode($config2));
				$config = json_decode(file_get_contents( dirname(__FILE__).'/config.json' ), true);
				$email_template_to_edit = stripslashes($config['email_templates'][$_REQUEST['id']]['body']);
				$email_template_subject_to_edit = stripslashes($config['email_templates'][$_REQUEST['id']]['subject']);
				$template_type = $_REQUEST['id'];
				$success = "Emil text updated successfully.";
				include 'views/view_list_email_templates.php';
								
			}elseif(isset($_REQUEST['id'])){
				$config = json_decode(file_get_contents( dirname(__FILE__).'/config.json' ), true);
				$email_template_to_edit = stripslashes($config['email_templates'][$_REQUEST['id']]['body']);
				$email_template_subject_to_edit = stripslashes($config['email_templates'][$_REQUEST['id']]['subject']);
				$template_type = $_REQUEST['id'];
				include 'views/view_list_email_templates.php';
			}else{
				$config = json_decode(file_get_contents( dirname(__FILE__).'/config.json' ), true);
				include 'views/view_list_email_templates.php';
			}
			
		}
		
		
		/*
		*	mass_emails
		*
		*/
		
		function mass_emails(){
			if(isset($_POST['send'])){
				global $wpdb;
				$status = ($_REQUEST['subscription_status'] !== 'all')?' WHERE sbr_status="'.$_REQUEST['subscription_status'].'"':'';
				$config = parse_ini_file( WP_CONTENT_DIR."/plugins/NetAxept/NetAxept.ini", true );
				$sbr_table = $wpdb->prefix.$config['plugin_parameters']['tn_subscription'];
				$payment_table = $wpdb->prefix.$config['plugin_parameters']['tn_payment'];
				$users_table = $wpdb->prefix."users";
				$sql = "SELECT u.ID, u.user_email, u.display_name, sbr.sbr_status, sbr.sbr_id FROM $sbr_table sbr JOIN $users_table u ON sbr.sbr_wp_user_id=u.ID $status";
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
					$sbr_status = $user_info->sbr_status;
					$sql_payment = "select pmt_period_from, pmt_period_to from $payment_table WHERE pmt_status = 'Collected' AND pmt_sbr_id=".$user_info->sbr_id." ORDER BY pmt_id DESC LIMIT 1";
					$payment = $wpdb->get_results($sql_payment);
					$last_payment_date = '';
					if(!empty($payment)){
						$last_payment_date = $payment[0]->pmt_period_from.' - '.$payment[0]->pmt_period_to;
					}else{
						$last_payment_date = 'Have not paid yet!';
					}
					echo 'Sending email to: <strong>'.$email.'</strong>';
					echo '<br />';
					flush();
					ob_flush();
					$msg = str_replace("{FIRST_NAME}", $first_name, $msg);
					$msg = str_replace("{LAST_NAME}", $last_name, $msg);
					$msg = str_replace("{EMAIL}", $email, $msg);
					$msg = str_replace("{SBR_STATUS}", $sbr_status, $msg);
					$msg = str_replace("{LAST_PAYMENT_DATE}", $last_payment_date, $msg);
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
				include 'views/send_mass_emails.php';
								
			}else{
				include 'views/send_mass_emails.php';
			}
			
		}
		
		/*adding users paid outside the netaxept*/
		function netaxept_outside_users(){
			global $wpdb;
			$config = parse_ini_file( WP_CONTENT_DIR."/plugins/NetAxept/NetAxept.ini", true );
			$sbr_table = $wpdb->prefix.$config['plugin_parameters']['tn_subscription'];
			if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'add_user'){
				$payment = json_decode(file_get_contents( dirname(__FILE__).'/payment.json' ), true);
				$count = count($payment);
				$payment[$count]['user_id'] = $_REQUEST['user_id'];
				$payment[$count]['start_date'] = $_REQUEST['start_date'];
				$payment[$count]['end_date'] = $_REQUEST['end_date'];
				file_put_contents( dirname(__FILE__).'/payment.json', json_encode($payment));
			}elseif(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete'){
				$payment = json_decode(file_get_contents( dirname(__FILE__).'/payment.json' ), true);
				$payments = array();
				foreach($payment as $key=>$p){
					if($_REQUEST['key'] != $key){
						$payments[] = $p;
					}
				}
				file_put_contents( dirname(__FILE__).'/payment.json', json_encode($payments));
				echo "<script>window.location='admin.php?page=netaxept_outside_users&del=yes';</script>";
				exit;
			}
			$sql = 'SELECT u.ID, u.user_nicename, u.user_login, u.user_email, sb.sbr_pan_hash FROM '.$wpdb->prefix.'users u LEFT JOIN '.$sbr_table.' sb ON u.ID = sb.sbr_wp_user_id WHERE sb.sbr_pan_hash IS NULL';
			$all_users = $wpdb->get_results($sql);
			$payments = json_decode(file_get_contents( dirname(__FILE__).'/payment.json' ), true);
			include 'views/netaxept_outside_users.php';
		}
		
		
		
		
		
		/*
		*	show_netaxcept_payment_box
		*/
		function show_netaxcept_payment_box(){

			$todo = ( !empty( $_REQUEST['todo'] ) ) ? $_REQUEST['todo'] : 'deafult';

			if($todo){
				switch($todo){
					
					case "goto_step_4":
						if(!empty($_POST['transactionId'])){
							return $this->show_step_4($_POST['transactionId']);
						}
						break;
					
					case "goto_step_3":
	
						return $this->show_step_3();
						break;
					
					case "goto_step_2":
						
						if(isset($_REQUEST['user_id'])){
							$wp_user_id = $_REQUEST['user_id'];
							$values = array('FirstName' => strip_tags(get_user_meta($wp_user_id, 'first_name', true)),
											'LastName' => strip_tags(get_user_meta($wp_user_id, 'last_name', true)),
											'CellPhone' => strip_tags(''),
											'Email' => strip_tags(get_user_meta($wp_user_id, 'nickname', true))
											);
							$error = "";
						}else{
							
							$values = array(	'FirstName' => strip_tags($_POST['fname']),
												'LastName' => strip_tags($_POST['lname']),
												'CellPhone' => strip_tags($_POST['cellnumber']),
												'Email' => strip_tags($_POST['email'])
												);
							$error = $this->checkUserInput($values);
						}
						if($error==""){
							
							//Checking for registered user and is active
							$error = $this->checkAlreadyExistsWithActive($values['Email']);
							
							if($error==""){
								
								//if( !username_exists( $values['Email'] ) ){//Removed by Sofiane
									
									return $this->show_step_2($values);
								
								/*} else {
								
									$data_view_error = array( 'heading' => 'Eposten er allerede registrert',
																'text' => 'Eposten du har fylt inn er allerede registrert 
																			hos oss med et abonnement. Logg inn for å 
																			administrere abonnementet.');
									include 'views/view_error.php';
								}*///Removed by Sofiane
							}else{
								echo "<h2>En feil oppstod, prøv igjen</h2>".$error;
								return $this->show_step_1();
							}
							
						}else{
							echo "<h2>En feil oppstod, prøv igjen</h2>".$error;
							return $this->show_step_1();
						}
						break;
					
					case "deactivate" :
						
						$this->deactivateSubscription();
						return $this->show_step_1();
						break;
					
					case "activate" :
						$this->activateSubscription();
						return $this->show_step_1();
						break;
					
					case "recurring_payment" :
						$this->collect_payment();
						break;
                    
                    case "save_email_setting" :
                        
                        
                        $no_email = (isset($_POST['no_email'])) ? 1 : 0 ;
                        $all_email = (isset($_POST['all_email'])) ? 1 : 0 ;
                        $email_topics = (isset($_POST['email_topics'])) ? $_POST['email_topics'] : 0 ;
                        
                        $setting = array(
                            "all_groups" => $all_email,
                            "no_groups" => $no_email,
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
                        
                        if ($user) {
                            $q = " UPDATE groups_email_settings SET setting = '$setting' WHERE user_id = '$user_id' ";
                            $r = $wpdb->query($q);
                        }
                        else{
                            $q = " INSERT INTO groups_email_settings (user_id, setting) VALUES (
                            '$user_id',
                            '$setting'
                            ) ";
                            $r = $wpdb->query($q);
                        }
                        return $this->show_step_1();
						break;
					
					default:
						return $this->show_step_1();
					
				}
			}
							
		}
		
		
		
		
		
		
		/*
		*	=step_1
		*	=show_step_1
		*/
		function show_step_1(){
			
			$data_view_step_1 = array(  'fname' => (!empty($_POST['fname'])) ? $_POST['fname'] : '', 
										'lname' => (!empty($_POST['lname'])) ? $_POST['lname'] : '',
										'cellnumber' => (!empty($_POST['cellnumber'])) ? $_POST['cellnumber'] : '',
										'email' => (!empty($_POST['email'])) ? $_POST['email'] : '',
										'submit_text' => 'Gå videre',
										'loggin_text' => 'Logg inn'
										);
										
			include 'views/view_step_1.php';		
					
		}
		
		
		
		
		
		
		
		
		/*	=step_2
		*	=show_step_2
		*	create transaction id, verify input
		*	
		*/
		function show_step_2($values){
			
			$orderNumber	= md5(uniqid('', true));
			$ArrayOfItem 	= null; 
			$options 		= $this->get_current_options();
			
			//	new order / ny bestilling
			$mattevideo_order = new Order(	($options['subscription_price'] * 100), 
											$this->config['order_parameters']['currencyCode'], 
											$this->config['order_parameters']['force3DSecure'], 
											$ArrayOfItem, 
											$orderNumber,
											$this->config['order_parameters']['updateStorePaymentInfo']);
			
			//	new enviroment
			$mattevideo_environment = new Environment(	$this->config['environment_parameters']['language'], 
														$this->config['environment_parameters']['OS'], 
														$this->config['environment_parameters']['WebServicePlatform']);
			//	 new Terminal
			$mattevideo_terminal = new Terminal(	$this->config['terminal_parameters']['autoAuth'], 
													$this->config['terminal_parameters']['paymentMethodList'], 
													$this->config['terminal_parameters']['language'], 
													$this->config['terminal_parameters']['orderDescription'], 
													$options['redirect_on_error'], 
													$options['redirect_url'].'?todo=goto_step_3');
			
			//	new recurring
			/*
				$ExpiryDate,
		        $Frequency,
		        $Type,
		        $PanHash
			*/
			$expiryDate = '';//date('Ymd', strtotime('next year'));
	        $frequency = '';//30; //days
	        $type = 'S';
	        $panHash = '';
		    
			$mattevideo_recurring = new Recurring(	$expiryDate,
													$frequency,
													$type,
													$panHash);
			
			//	new Customer
		    $Email = $values['Email'];
		    $FirstName = $values['FirstName'];
		    $LastName = $values['LastName'];
		    $PhoneNumber = $values['CellPhone'];
		    
			if(strlen($PhoneNumber) == 8) $PhoneNumber = "47".$PhoneNumber;
			$Address1 = '';
			$Address2 = '';
			$CompanyName = '';
			$CompanyRegistrationNumber = '';
			$Country = '';
			$customerNumber = '';
			$PostCode = '';
			$SocialSecurityNumber = '';
			$Town = '';

			$mattevideo_customer = new Customer(	$Address1, 
													$Address2, 
													$CompanyName, 
													$CompanyRegistrationNumber, 
													$Country, 
													$customerNumber, 
													$Email, 
													$FirstName, 
													$LastName, 
													$PhoneNumber, 
													$PostCode, 
													$SocialSecurityNumber, 
													$Town);
													
			
			//	new request register
			$transactionid = '';
			$transactionReconRef = '';
			/*
					$AvtaleGiro,
			        $CardInfo,
			        $Customer,
			        $Description,
			        $DnBNorDirectPayment,
			        $Environment,
			        $MicroPayment,
			        $Order,
			        $Recurring,
			        $ServiceType,
			        $Terminal,
			        $TransactionId,
			        $TransactionReconRef
			*/
			$RegisterRequest = new RegisterRequest( $this->config['register_request_parameters']['AvtaleGiro'], 
													$this->config['register_request_parameters']['CardInfo'],
													$mattevideo_customer,
													$this->config['register_request_parameters']['description'],
													$this->config['register_request_parameters']['DnBNorDirectPayment'],
													$mattevideo_environment,
													$this->config['register_request_parameters']['MicroPayment'],
													$mattevideo_order,
													$mattevideo_recurring,
													$options['service_type'],
													$mattevideo_terminal,
													$transactionid, //transactionid
													$transactionReconRef
													);
			
		
			/*
			*	2	Start request
			*	
			*/
			$InputParametersOfRegister = array(	"token" => $options['token'], 
												"merchantId" => $options['merchantid'], 
												"request" => $RegisterRequest, 
												"soap_version" => SOAP_1_1);
			
			try{
				
				
				$client = $this->create_client($options['wsdl']);
				
				
				$OutputParametersOfRegister = $client->__call('Register' , array("parameters" => $InputParametersOfRegister));
								
				
				// RegisterResult
				$transactionId = $OutputParametersOfRegister->RegisterResult->TransactionId; 
			 	
			 	
				/*
				*	3 show confirm box if everything is honky dory
				*/
				if($client){
					
					// 	get user if user exists or create a new user
					$user_id = null;
					
					if($user_id = email_exists($mattevideo_customer->Email)){
					//added by Sofiane
					$updateUserResult = wp_update_user( array('ID' => $user_id, 
		 														'first_name' => $mattevideo_customer->FirstName, 
		 														'last_name' => $mattevideo_customer->LastName ));
					//end Sofiane
					
					}else{
			 			// create new user
			 			
		 				$random_password = wp_generate_password( 8, false );
		 				$user_id = wp_create_user( $mattevideo_customer->Email, $random_password, $mattevideo_customer->Email );
			 			
			 			if($user_id == 'existing_user_login' || $user_id == 'existing_user_email'){
			 				
			 			}else{
		 					$updateUserResult = wp_update_user( array('ID' => $user_id, 
		 														'first_name' => $mattevideo_customer->FirstName, 
		 														'last_name' => $mattevideo_customer->LastName ));
			 				
			 			}
			 					
					}
					
					//	check if user has subscription or create a new subscription;
					$user_subscription = new MattevideoSubscriptionController( $user_id );
										
					$user_payment = new MattevideoPaymentController( $user_subscription->getSubscriptionId(), $transactionId, $options['subscription_price'], $orderNumber);
					
					$data_view_step_2 = array(	'submit_text' => 'Ja, alt er rett',
												'pre_text' => 'Takk for at du hjelper oss å dobbeltsjekke! Når du trykker videre sendes du til betalingsløsningen, husk å ha klar BankID.',
												'merchantId' => $options['merchantid'],
												'transactionId' => $transactionId,
												'terminal' => $options['terminal'], 
												'user_id' => $user_id,
												'amount' => $options['subscription_price']);
					
					include 'views/view_step_2.php';
				
			
					
				}else{
					
					$data_view_error = array('heading' => 'En feil oppstod',
												'text' => 'En uventet feil oppstod. Vennligst prøv igjen.');
					
					include 'views/view_error.php';
					
				}
				
			}catch(SoapFault $fault){
				echo $fault->getMessage();
			}
			
		}
		
		
		function fake_show_step_2($values){
			
			$data_view_step_2 = array(	'submit_text' => 'Videre',
												'pre_text' => 'Det er viktig for at du skal kunne logge inn og se filmene at du 
												fyller inn riktig epost adresse og mobiltelefon nummer.',
												'merchantId' => $options['merchantid'],
												'transactionId' => $transactionId,
												'terminal' => $options['terminal'], 
												'amount' => $options['subscription_price']);
					
			include 'views/view_step_2.php';
			
		}
		
		
		
		
		
		
		/*
		*	=show_step_3
		*	confirm valid payment with transactionid
		*	the payment is on hold
		*/
		function show_step_3(){
			
			$options 		= $this->get_current_options();
			
			
			if (isset($_GET['transactionId']) && isset($_GET['responseCode'])){
		  		$transactionId = $_GET['transactionId'];
				$incResponseCode = $_GET['responseCode'];

			} else {
				_log( 'NetAxept.php: show_step_3: No transaction or responce code recieved' );
			}
			
			$payment = MattevideoPaymentController::withTransactionId( $transactionId );			
			
			
			if ( $incResponseCode == "OK" ){
				
				// update payment

				$payment->setStatus( 'Payment reserved' );
				
				
				$queryRequest = new QueryRequest($transactionId);
				$InputParametersOfRegister = array(	"token" => $options['token'], 
													"merchantId" => $options['merchantid'], 
													"request" => $queryRequest,
													"soap_version" => SOAP_1_1 );
				
				try{
					
					$client = $this->create_client($options['wsdl']);
					
					$OutputParametersOfRegister = $client->__call('Query' , array("parameters" => $InputParametersOfRegister));
					
					if( $payment->getSbrId() ){
						$subscription = MattevideoSubscriptionController::withId( $payment->getSbrId() );
						$subscription->setPanHash( $OutputParametersOfRegister->QueryResult->CardInformation->PanHash );
					}else {
						_log( 'No id: ' . $payment->getSbrId() . '<br/>');
					}
				}catch (SoapFault $soapError) {
				
					_log( 'NetAxept.php: show_step_3: client->call failed: ' . $soapError );
				}
				
				
				/*$data_view_step_3 = array(
		 									'body' => '<p>Takk! Din betaling er nå registrert.<br/> Trykk under så sender vi deg passord og brukernavn på e-post.
											</p>
											<form method="post" action="?">
												<input type="hidden" name="todo" value="goto_step_4">
												<input type="hidden" name="transactionId" value="'.$transactionId.'">
												<input type="submit" value="Send meg passord" style="width:120px;">
											</form> ');*/
			 	$data_view_step_3 = array('body' => '<p>Takk! Din betaling er nå registrert.</p>');
				
				//include 'views/view_step_3.php';
				
				$this->show_step_4($transactionId);
			 		
			
			}else if($incResponseCode = "Cancel"){
				
				$payment->setStatus( "Payment cancelled" );
				
				$data_view_error = array(	'heading' => 'Bestillingen ble avbrutt',
											'text' => '<p>Du avsluttet bestillingen før den var ferdig. Om du har noen spørsmål kan du sende en epost til 																	hjemmesidefilm.no. </p>
														<p>Gå til <a href="http://mattevideo">mattevideo.no</a> </p>');
					
				include 'views/view_error.php';
				
			}else{
			 		
				// TODO:	report error
				$payment->setStatus('Error step 3: code '.$incResponseCode);	
				$data_view_error = array(	'heading' => 'En feil oppstod',
											'text' => '<p>Din bestilling ble avbrutt på grunn av feil</p>
											<p>Vennligst gå til <a href="http://mattevideo.no">mattevideo.no</a>og start på nytt</p>');
					
				include 'views/view_error.php';
				
				_log( 'NetAxept.php: show_step_3: unknown responce code: ' . $incResponseCode );
				
			}
		}
		
		
		
		
		
		
		
		
		/*
		*	=show_step_4 =4
		*	user confirm the deal
		*	payment on hold is now transferred
		*/
		function show_step_4 ( $transactionId = "" ) {
			
			if ( $transactionId ) {
				
				$options = $this->get_current_options();
				
				$client = $this->create_client($options['wsdl']);
				
				
				
				####	PROCESS OBJECT	####
				$ProcessRequest = new ProcessRequest( 	'Sale', 
														'SALE', 
														'', 
														$transactionId, 
														'' );
				
				//	input parameters of process	
				$InputParametersOfProcess = array("token"=> $options['token'], "merchantId" => $options['merchantid'], "request" => $ProcessRequest);
				
				$OutputParametersOfProcess = '';
				
				try{
					$OutputParametersOfProcess = $client->__call('Process' , array("parameters"=>$InputParametersOfProcess));
					
				}catch(SoapFault $error){
					
					_log( 'NetAxept.php: show_step_4: Error in client call' );
				}
				
				if($OutputParametersOfProcess){
					$ProcessResult = $OutputParametersOfProcess->ProcessResult; 
					
					if ($ProcessResult->ResponseCode == "OK"){
					
						
						//	get info about customer
						$queryRequest = new QueryRequest($transactionId);
						$InputParametersOfQuery = array("token" => $options['token'], "merchantId" => $options['merchantid'], "request" => $queryRequest);
						$OutputParametersOfQuery = $client->__call('Query' , array("parameters"=>$InputParametersOfQuery));
						$QueryResult = $OutputParametersOfQuery->QueryResult; 
						$customerInfo = $QueryResult->CustomerInformation;
						
					
						//	get recorded info about customer
						//	$recordedTransactionInfo = $this->getTransaction($transactionId);
						
						//	check if info match
										 
				 		//	Everything is ok: update payment
				 		$payment = MattevideoPaymentController::withTransactionId($transactionId);			
				 		$payment->setStatus('Collected');
				 		
				 		//	Create next payment with from date as same as previous payments to date
				 		$this->create_next_payment_on_subscription( $payment->getSbrId(),  $payment->get_to_date());
				 		
				 		
				 		//	Everything is OK: update subscription	
				 		$subscription = MattevideoSubscriptionController::withId($payment->getSbrId());
				 		$subscription->setStatus('Active');
				 		
				 		
				 		//	TODO: send mail
				 		$to =  $customerInfo->Email;
				 		$subject = "Mattevideo abonnement";
						$headers = 	"MIME-Version: 1.0\n" .
									"From: Mattevideo <ksondresen@gmail.com>\n" .
									"Content-Type: text/html; charset=\"" .
									get_option('blog_charset') . "\"\n";
				 		//$headers = "From: Mattevideo <ksondresen@gmail.com>";
				 		$msg = '';
				 		$feedback = '';
				 		$attachments = dirname(__FILE__).'/Kjøpsvilkår og 100% fornøyd garanti.pdf';
				 		
				 		$password = wp_generate_password(8, false);
				 		wp_set_password( $password, $subscription->getWpUserId() );
				 		
				 		if ($payments_count = 1) {
							$config = json_decode(file_get_contents( dirname(__FILE__).'/config.json' ), true);
							$subject = stripslashes($config['email_templates']['payment_email']['subject']);
					 		$msg = str_replace("{PASSWORD}", $password, stripslashes($config['email_templates']['payment_email']['body']));
					 		$feedback = "Takk for din bekreftese. <br/>
					 						Brukernavn og passord sendes nå på e-post. ";
				 		
				 		} else if ( $payments_count > 1 ) {
					 		$msg = "Ditt mattevideo abonnement er oppdatert med mer tid.";
					 		$feedback = "Du har oppdatert ditt abonnement.";
				 		
				 		} else {
					 		_log( 'Netaxept.php: payments_count was 0, should have been 1 or more' );
				 		}
				 		
				 		
				 		//	send mail to user						
				 		$mailResult = wp_mail( $to, $subject, $msg, $headers, $attachments );
						
						//Send email to admin
						$admin = 'ksondresen@gmail.com';
						//$admin = 'muhammad.saleem@purelogics.net';
						$msg = 'Mattevideo bruker har registrert med e-post: '.$to.' og passord:'.$password;
						$headers = "From: $to <$to>" . "\r\n";
						wp_mail( $admin, $subject, $msg, $headers);
				 		
				 		
				 		if ( $mailResult ) {
				 		
							$data_view_step_4 = array(
														'body' => '<p>'.$feedback.'</p>');
					
							include 'views/view_step_4.php';
						
						} else {
							_log( 'Netaxept.php: send mail failed, user didnt get log in' );
						}
						
				
					
					}else{
					
						$data_view_error = array(	'heading' => 'En feil oppstod',
													'text' => '<p>En feil oppstod. Vennligst prøv igjen.</p>
												<p>Gå til <a href="http://mattevideo">mattevideo.no</a> </p>');
					
						include 'views/view_error.php';
					}

				}else{
					_log( 'Netaxept.php: $OutputParametersOfProcess is null' );
				}
				
					
			} else {
				_log( 'Netaxept.php: no transaction id' );
			}
		
		}
		
		
		
		/*
		*	show_subscription($sbr_id)
		*/
		function show_subscription($sbr_id){
			global $wpdb;
			
			if(empty($sbr_id)){
				$sbr_id = (!empty($_POST['sbr_id']) ? $_POST['sbr_id'] : null);
			}	
			
			if(!empty($sbr_id)){		
				$sbr_tableNameWithPrefix = $wpdb->prefix.$this->config['plugin_parameters']['tn_subscription'];
				$pmt_tableNameWithPrefix = $wpdb->prefix.$this->config['plugin_parameters']['tn_payment'];
				
				$sbr = $wpdb->get_results("SELECT * FROM $sbr_tableNameWithPrefix WHERE sbr_id = $sbr_id");
				$pmt = $wpdb->get_results("SELECT * FROM $pmt_tableNameWithPrefix WHERE pmt_sbr_id = $sbr_id ORDER BY pmt_id DESC");
				
				$data_view_show_subscription = array('heading' => $sbr[0]->sbr_status,
														'sbr' => $sbr[0],
														'pmt' => $pmt); 
				
				include 'views/view_show_subscription.php';
				
			}else{
				_log( 'Netaxept.php: show_subscription: sbr_id is null' );
			}
		}
		
		
		
		
		
		/*
		*	show_user ()
		*/
		function show_user(){
			
			
		}
		
		
		
		
		
		
		/*
		*	prints_r whatever between <pre></pre> tags.
		*/
		function printInPre($toBePrinted){
			echo '<pre>';
			print_r($toBePrinted);
			echo '</pre>';		
		}
		
		
		
		
		
	
		
		
		
		
		
		
		public function update_user_sbr_status($username){
		
			global $wpdb;

	     	if(!username_exists($username)) {
				return;
			}
	        
	        $user_data = get_user_by('login', $username);
	        
			$user_sbr = new MattevideoSubscriptionController($user_data->ID);
			//$user_sbr->update_user_sbr_status();
			
		}	
		
		
		
		
	
	}	
	
	
}


/*	1	*/
if(class_exists("NetAxept")){
	$netAxept = new NetAxept();
}


/*	2	*/
if(!function_exists("netAxeptDelegate")){
	
	function netAxeptDelegate(){
		
	
		global $netAxept;
		if(!isset($netAxept)){
			return;
		}
		
		//	Add main menu page	
		add_menu_page('NetAxept Log', 'NetAxept', "activate_plugins", "netaxept", array(&$netAxept, 'netaxept_delegate'));
		
		//	add options page
		add_submenu_page('netaxept', 'NetAxept Options', 'Options', 'activate_plugins', 'netaxept_options', array(&$netAxept, 'show_options'));
		
		//	add Email page
		add_submenu_page('netaxept', 'NetAxept Emails', 'Email Templates', 'activate_plugins', 'email_templates', array(&$netAxept, 'show_emails'));
		
		//	add Email page
		add_submenu_page('netaxept', 'NetAxept Send Mass Emails', 'Send Mass Emails', 'activate_plugins', 'mass_emails', array(&$netAxept, 'mass_emails'));
		
		//	add manually paid users
		add_submenu_page('netaxept', 'Users Paid Outside NetAxept', 'Users Paid Outside NetAxept', 'activate_plugins', 'netaxept_outside_users', array(&$netAxept, 'netaxept_outside_users'));
	
	}
	
}

/*	3	*/
if($netAxept){
	
	/*
	*	Action
	*/
	add_action('admin_menu', 'netAxeptDelegate');
    
    // Edit By PL - 6-6-2014
	//add_action('wp_authenticate', array(&$netAxept, 'update_user_sbr_status' ));
	
	/*
	*	Shortcode
	*/
	add_shortcode('netaxept', array(&$netAxept, 'show_netaxcept_payment_box'));
	
	/*
	*	css Style
	*/
	$pathToStyleFile = WP_PLUGIN_URL."/NetAxept/NetAxept_style.css";
	wp_register_style('kumicode_netaxept_style', $pathToStyleFile);
	wp_enqueue_style('kumicode_netaxept_style');
	
	/*
	*	Activation code
	*/
	register_activation_hook(__FILE__, array(&$netAxept, 'create_mysql_tables'));
	
	
}

?>