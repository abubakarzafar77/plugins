<?php
/*
Plugin Name: Sendega SMS
Plugin URI: http://kumicode.com/sendegaSMS
Description: 
Version: 1.0
Author: Øyvind Dahl
Author URI: http://kumicode.com
License: GPL
*/





if(!class_exists("SendegaSMS")){
	
	class SendegaSMS{
		
		var $sendegaSMSVersionOptionName = "SendegaSMSVersion";
		var $sendegaSMSVersion = "1.0";
		
		var $monthlyAmount = 9900; // i øre
		//var $monthlyAmount = 0; // i øre
		
		//	tables
		var $subscriptionTableName = "sendegaSMS_subscription";
		var $subscriptionRequestTableName = "sendegaSMS_subscriptionRequest";
		var $subscriptionRequestLogTableName = "sendegaSMS_subscriptionRequestLog";
		var $paymentsTableName = "sendegaSMS_payments";
		
		
		//
		var $requestStatus = array( 0 => 'Nothing',
									100 => 'Initiated with phone number',
									200 => 'Existing user approved subscription',
									201 => 'New user approved subscription',
									300 => 'Request finished successfully',
									500 => 'Continue subscription payment request OK',
									900 => 'Request ended. Restart from customer nessesary',
									901 => 'Request failed. Customers balance to low'
									
		);
		
		
		var $smsMessages = array(	'SMS_1ab' => 'Takk for at du bestiller tilgang til mattevideo.no. For å starte ditt abonnement, svar "Matte OK" på denne sms’en. ',
									'SMS_2ab' => 'Kjære bruker. Vi har nå mottatt betaling for ditt abonnement! Du kan nå logge inn med brukernavn: %s og passord: %s Takk for at du bruker mattevideo.no',
									
									'SMS_3' => 'Kjære bruker. Takk for at du er abonnent hos mattevideo.no. Du har nå gylding tilgang en ny måned.',
									'SMS_4ab' => 'Kjære bruker. Mattevideo greide ikke å trekke abonnements beløpet fra din mobil. Dette kan skyldes at ditt kontantkort er tomt, eller andre tekniske problemer. Kontakt din telefonoperatør for nærmere feilsøk, og prøv igjen senere for å aktiver ditt abonnement hos mattevideo.no',
									'SMS_5' => 'Kjære bruker. Vi har nå mottatt din stopp sms. Ditt abonnement avsluttes fra og med neste måned. Velkommen tilbake ved en senere anledning.'
								);
		
		function SendegaSMS(){
			
			/*
			*	Shortcode
			*/
			add_shortcode('SendegaSMS_number_input', array($this, 'printSignUpForm'));
			add_shortcode('SendegaSMS_incomingSMS', array($this, 'incomingSMS'));
			add_shortcode('SendegaSMS_deliveryReport', array($this, 'incomingDeliveryReport'));
		
			
			/*
			*	Menu items	
			*/
			add_action('admin_menu', array($this ,'register_controll_center'));
			
			
		}	
		
		
		
		
		/*
		*
		*	=createNewPayment
		*
		*/
		function createNewPayment($subscriptionId, $status = "", $errorCode = "", $subscriptionRequestId = '', $sendSMSDate = '0000-00-00 00:00:00'){
			
			global $wpdb;
			$insertData = array(	'subscriptionId' => $subscriptionId,
									'status' => $status,
									'statusCode' => $errorCode,
									'subscriptionRequestId' => $subscriptionRequestId,
									'smsSendt' => $sendSMSDate,
									'created' => date('Y-m-d H:i:s'),
									'modified' => date('Y-m-d H:i:s'));
									
			$createResult = $wpdb->insert($wpdb->prefix.$this->paymentsTableName, $insertData);
			$paymentId = $wpdb->insert_id;
			
			$updateSubscriptionRequestData = array('paymentId' => $paymentId);
			$updateSubscriptionRequestWhere = array('id' => $subscriptionRequestId);
			$updateSubscriptionResult = $wpdb->update($wpdb->prefix.$this->subscriptionTableName, $updateSubscriptionRequestData, $updateSubscriptionRequestWhere);
			
			return $paymentId;
		}
		
		
		
		
		
		
		
		/*
		*	=createNewSubscription
		*
		*
		*/
		function createNewSubscription($wp_user_id, $msisdn_int, $msisdn_string, $subscriptionStatus = '', $subscriptionValidToDate = '', $paymentId = ''){
			global $wpdb;
			
			$insertData = array('wp_user_id' => $wp_user_id,
								'msisdn_int' => $msisdn_int,
								'msisdn_string' => $msisdn_string,
								'subscriptionStatus' => $subscriptionStatus,
								'subscriptionValidToDate' => $subscriptionValidToDate,
								'paymentId' => $paymentId);
			
			$insertResult = $wpdb->insert($wpdb->prefix.$this->subscriptionTableName, $insertData);
			
			return $wpdb->insert_id;
		}
		
		
		
		
		
		
		
		/*
		*	=createNewSubscriptionRequest
		*
		*	$subscriptionData = array(	'wp_user_id' => $userId,
										'msisdn_int' => 4795926551,
										'msisdn_string' = +47 95 92 65 51,
										'dateRequested' = date('Y-m-d H:i:s'),
										'status' = 'initiated',
										'statusCode' = 5,
										);
		*
		*	returns subscriptinRequest->id
		*/
		function createNewSubscriptionRequest($subscriptionData){
			
			global $wpdb;
			
			$insertResult = $wpdb->insert(	$wpdb->prefix.$this->subscriptionRequestTableName,
											$subscriptionData
										);
			return $wpdb->insert_id;
		}
		
		
		
		
		
		
		/*
		*	=createNewSubscriptionRequestLogItem
		*
		*	logData = array(	'subscriptionRequestId',
								'date',
								'status',
								'statusCode'
								);
		*
		*/
		function createNewSubscriptionRequestLogItem($logData){
			global $wpdb;
			$wpdb->insert($wpdb->prefix.$this->subscriptionRequestLogTableName, $logData);	
			return $wpdb->insert_id;	
		}
		
		
		
		
		/*
		*
		*
		*
		*/
		function firstPaymentFailed($subscriptionRequest, $incomingData){
			
			global $wpdb;
			
			//	update payment
			$paymentUpdateData = array('status' => 'Payment Failed',
										'modified' => date('Y-m-d H:i:s'),
										'smsReported' => date('Y-m-d H:i:s'));
			$paymentWhereData = array('subscriptionRequestId' => $subscriptionRequest->id); 
			$updatePaymentResult = $wpdb->update($wpdb->prefix.$this->paymentsTableName, $paymentUpdateData, $paymentWhereData);
			
			//	update subscriptionRequest
			$this->updateSubscriptionRequest($subscriptionRequest->id, $this->requestStatus[901], 0);
			
			//	update subscription
			$subscriptionStatus = 'Deactive';
			$msisdn = $incomingData['msisdn'];
			
			$msisdn_int = $subscriptionRequest->msisdn_int;
			$userId = $subscriptionRequest->wp_user_id;
			
			$subscriptionUpdateData = array('subscriptionStatus' => $subscriptionStatus);
			$subscriptionWhereData = array('wp_user_id' => $userId);
			
			$updateResult = $wpdb->update($wpdb->prefix.$this->subscriptionTableName, $subscriptionUpdateData, $subscriptionWhereData);

			
			
			//	send Payment failed sms
			$this->sendPaymentFailedSMS($incomingData['msisdn']);
		}
		
		
		
		
		
		
		/*
		*
		*	=firstPaymentOK
		*
		*
		*/
		function firstPaymentOK($subscriptionRequest, $incomingData){
			
			global $wpdb;
			
			$this->updateLog($subscriptionRequest->id, 'First payment ok');
			
			
			//	updatePayment
			$paymentUpdateData = array('status' => 'Payment OK',
										'modified' => $this->now(),
										'smsReported' => $this->now());
			$paymentWhereData = array('subscriptionRequestId' => $subscriptionRequest->id); 
			
			$updatePaymentResult = $wpdb->update($wpdb->prefix.$this->paymentsTableName, $paymentUpdateData, $paymentWhereData);
			
			
			//	updateSubscriptionRequest
			$this->updateSubscriptionRequest($subscriptionRequest->id, $this->requestStatus[300], 0);
			
			
			
			//	update subcription active and valid to next month
			$subscriptionValidToDate = date('Y-m-d H:i:s', strtotime('+1 month'));
			$subscriptionStatus = 'Active';
			$msisdn = $incomingData['msisdn'];	
			
			$msisdn_int = $subscriptionRequest->msisdn_int;
			$userId = $subscriptionRequest->wp_user_id;
			
			$subscriptionUpdateData = array('subscriptionStatus' => $subscriptionStatus,
											'subscriptionValidToDate' => $subscriptionValidToDate);
			$subscriptionWhereData = array('wp_user_id' => $userId);
			
			$updateResult = $wpdb->update($wpdb->prefix.$this->subscriptionTableName, $subscriptionUpdateData, $subscriptionWhereData);
		
			
						
			$subscription = $wpdb->get_row("SELECT id FROM ".$wpdb->prefix.$this->subscriptionTableName." WHERE wp_user_id = '".$userId."'" );		
			$subscriptionId = $subscription->id;
			
			//	!!! set up next payment
			/*
				This function is deactivated because it started to send out multiple sms to customer, due to unknown problems (probably sendega)
				
			*/
			$nextSubscriptionPaymentRequestDate = date('Y-m-d H:i:s',  strtotime('now') + 7500);
			$nextSubscriptionPaymentRequestDate = date('Y-m-d H:i:s',  strtotime('+3 weeks') + 3600);
		
			$this->setUpNextPayment($subscriptionId, $nextSubscriptionPaymentRequestDate);
			
		}
		
		
		
		/*
		*	=getUserSubscription
		*
		*
		*/
		function getUserSubscription($msisdn_int){
			global $wpdb;
			$subscriptionQuery = "SELECT * FROM ".$wpdb->prefix.$this->subscriptionTableName." WHERE msisdn_int = '".$msisdn_int."'";
			$subscriptionResult = $wpdb->get_row($subscriptionQuery);
			
			if($subscriptionResult != null){
				return $subscriptionResult;
			}else{
				return false;
			} 
		
		}
		
		
		
		
		
		/*
		*	=getUserSubscriptionId
		*
		*
		*/
		function getUserSubscriptionId($msisdn_int){
			global $wpdb;
			$subscriptionQuery = "SELECT id FROM ".$wpdb->prefix.$this->subscriptionTableName." WHERE msisdn_int = '".$msisdn_int."'";
			$subscriptionResult = $wpdb->get_row($subscriptionQuery);
			
			if($subscriptionResult != null){
				return $subscriptionResult->id;
			}else{
				return false;
			} 
		
		}
		
		
		
		
		/*
		*
		*	=includeSendegaSMSStyle
		*
		*/
		function includeSendegaSMSStyle(){
			$src = WP_PLUGIN_URL."/SendegaSMS/SendegaSMSStyle.css";
			wp_register_style( "SendegaSMSStyle2000", $src);
			wp_enqueue_style( "SendegaSMSStyle2000" ); 
		}
		
		
		
		
		
		/*
		*
		*
		*
		*
		*/
		function insertMysqlTables(){
			
			global $wpdb;
					
			//	***	insert subscription table
			
			
			$sqlSubscription = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix.$this->subscriptionTableName."(
								id mediumint(9) NOT NULL AUTO_INCREMENT,
								paymentId mediumint(9),
								wp_user_id mediumint(9) NOT NULL,
								msisdn_string VARCHAR( 255 ) NOT NULL,
								msisdn_int BIGINT(16) NOT NULL,
								subscriptionValidToDate datetime DEFAULT '0000-00-00 0:00' NOT NULL,
								subscriptionStatus varchar(510),
								UNIQUE KEY id (id),
								FOREIGN KEY (paymentId) REFERENCES ".$wpdb->prefix.$this->paymentsTableName." (id)
								);"; 
			
		
			$result = dbDelta($sqlSubscription);
			$now = $this->now();
			
			//	insert example data
			$insertExampleDataResult = $wpdb->insert(	$wpdb->prefix.$this->subscriptionTableName,
														array(	'id' => 666,
																'wp_user_id' => 1,
																'msisdn_string' => '+47 96 58 96 58',
																'msisdn_int' => '4796589658',
																'subscriptionValidToDate' => $now,
																'subscriptionStatus' => 'Active')
														); 
													
			/**/
			
			
			//	***	insert Subscription Request table 
			$sqlSubscriptionRequest = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix.$this->subscriptionRequestTableName."(
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					wp_user_id mediumint(9),
					msisdn_int bigint(16),
					msisdn_string varchar(255),
					dateRequested datetime DEFAULT '0000-00-00 00:00' NOT NULL,
					status varchar(510),
					statusCode mediumint(10),
					errorCode mediumint(16),
					UNIQUE KEY(id)
					);";
			
			$result = dbDelta($sqlSubscriptionRequest);
			
			//	insert example data
			
			$wpdb->insert(	$wpdb->prefix.$this->subscriptionRequestTableName,
														array(	'wp_user_id' => 1,
																'msisdn_int' => 4796589658,
																'msisdn_string' => '+47 96 58 96 58', 
																'status' => 'requested a payment',
																'statusCode' => 1,
																'dateRequested' => $now,
																'errorCode' => '0')
														);
			
			/**/
			
			
			
			/*
			*	***	insert Payments request log table
			*/
			$sqlPayments = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix.$this->paymentsTableName."(
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					subscriptionId mediumint(9) NOT NULL,
					subscriptionRequestId mediumint(9) NOT NULL,
					smsSendt datetime DEFAULT '0000-00-00 00:00' NOT NULL,
					smsReported datetime DEFAULT '0000-00-00 00:00' NOT NULL,
					created datetime DEFAULT '0000-00-00 00:00' NOT NULL,
					modified datetime DEFAULT '0000-00-00 00:00' NOT NULL,	
					subscriptionPeriodFrom datetime DEFAULT '0000-00-00 00:00' NOT NULL,	
					subscriptionPeriodTo datetime DEFAULT '0000-00-00 00:00' NOT NULL,	
					status varchar(510),
					statusCode int(16),
					UNIQUE KEY(id),
					FOREIGN KEY(subscriptionId) REFERENCES ".$wpdb->prefix.$this->subscriptionTableName."(id),
					FOREIGN KEY(subscriptionRequestId) REFERENCES ".$wpdb->prefix.$this->subscriptionRequestTableName."(id)
					);";
			
			$result = dbDelta($sqlPayments);
			
			//	insert example data
			
			$insertExampleDataResult = $wpdb->insert(	$wpdb->prefix.$this->paymentsTableName,
														array(	'subscriptionId' => 666,
																'subscriptionRequestId' => 1,
																'created' => '2012-03-28 05:19:19',
																'modified' => '2012-03-28 07:19:19',
																'status' => 'Payment OK',
																'statusCode' => '0')
														);	
			
			
			
			
			
			/*
			*	***	insert Subscription Request Log table
			*/
			$sqlSubscriptionRequestLog = "CREATE TABLE IF NOT EXISTS ".$wpdb->prefix.$this->subscriptionRequestLogTableName."(
					id mediumint(9) NOT NULL AUTO_INCREMENT,
					subscriptionRequestId mediumint(9) NOT NULL,
					date datetime DEFAULT '0000-00-00 00:00' NOT NULL,
					status varchar(510),
					statusCode int(16),
					UNIQUE KEY(id),
					FOREIGN KEY(subscriptionRequestId) REFERENCES ".$wpdb->prefix.$this->subscriptionRequestTableName."(id)
					);";
			
			$result = dbDelta($sqlSubscriptionRequestLog);
			
			
			
			//	insert example data
			$now = $this->now();
			$insertExampleDataResult = $wpdb->insert(	$wpdb->prefix.$this->subscriptionRequestLogTableName,
														array(	'subscriptionRequestId' => 1,
																'date' => $now,
																'status' => 'initiated',
																'statusCode' => '0')
														);
			if(!$insertExampleDataResult){
				echo 'Error in installing example data into subscription request log table<br/>';
			}											
			$now = $this->now();
			$insertExampleDataResult = $wpdb->insert(	$wpdb->prefix.$this->subscriptionRequestLogTableName,
														array(	'subscriptionRequestId' => 1,
																'date' => $now,
																'status' => 'Sending something',
																'statusCode' => '0')
														);
														
			if(!$insertExampleDataResult){
				echo 'Error in installing example data into subscription request log table<br/>';
			}
			
			$now =  $this->now();
			$insertExampleDataResult = $wpdb->insert(	$wpdb->prefix.$this->subscriptionRequestLogTableName,
														array(	'subscriptionRequestId' => 1,
																'date' => $now,
																'status' => 'Something more happend here',
																'statusCode' => '0')
														);
			if(!$insertExampleDataResult){
				echo 'Error in installing example data into subscription request log table<br/>';
			}
			$now = $this->now();
			$insertExampleDataResult = $wpdb->insert(	$wpdb->prefix.$this->subscriptionRequestLogTableName,
														array(	'subscriptionRequestId' => 1,
																'date' => $now,
																'status' => 'Finish',
																'statusCode' => '0')
														);
													
			/**/
			
														
			
		
			/**/
			return $result;
		
		}
		
		
		
 
		
		
		/*
		*	=incomingDeliveryReport
		*
		*/
		function incomingDeliveryReport(){
			global $wpdb;
			
			
			$userId = '';
			$paymentId = '';
			
			$subscriptionRequest = null;		
			
			
			/*
			*	Get incoming POST Data and stuff it into array
			*/
			$msgid = (!empty($_POST['msgid']) ? esc_html($_POST['msgid']) : '');
			$clientmsgId = (!empty($_POST['clientmsgId']) ? esc_html($_POST['clientmsgId']) : '');
			$msisdn = (!empty($_POST['msisdn']) ? esc_html($_POST['msisdn']) : '');
			$errorcode = (!empty($_POST['errorcode']) ? esc_html($_POST['errorcode']) : '');
			$errormessage = (!empty($_POST['errormessage']) ? esc_html($_POST['errormessage']) : '');
			$status = (!empty($_POST['status']) ? esc_html($_POST['status']) : '');
			$statustext = (!empty($_POST['statustext']) ? esc_html($_POST['statustext']) : '');
			$operatorerrorcode = (!empty($_POST['opperatorerrorcode']) ? esc_html($_POST['opperatorerrorcode']) : '');
			$registered = (!empty($_POST['registered']) ? esc_html($_POST['registered']) : '');
			$sendtime = (!empty($_POST['sendtime']) ? esc_html($_POST['sendtime']) : '');
			$delivered = (!empty($_POST['delivered']) ? esc_html($_POST['delivered']) : '');
			
			
			$incomingSMSData = array(	'msgid' => $msgid,
										'clientmsgId' => $clientmsgId,	
										'msisdn' => $msisdn,
										'errorcode' => $errorcode,
										'errormessage' => $errormessage,
										'status' => $status,
										'statustext' => $statustext,
										'operatorerrorcode' => $operatorerrorcode,
										'registered' => $registered,
										'sendtime' => $sendtime,
										'delivered' => $delivered
									);
			
			//	Logg it
			$this->updateLog('0', 'incomingDelivery. msgid '.$incomingSMSData['msgid'].', status: '.$incomingSMSData['status'].', msisdn: '.$incomingSMSData['msisdn'].', message: '.$incomingSMSData['errormessage'] );
			
			$debug = false;
			if(!$msgid){
				
				if($_GET['debug'] == "on"){
					
					if(!empty($_GET['tel'])){
						$tel = $_GET['tel'];
					}else{
						$tel = 4795926551;
					}
					
					
					
					
					if($_GET['status'] == 4){
						
						$incomingSMSData = array('msgid' => '',
												'clientmsgId' => '',
												'msisdn' => $tel,
												'errorCode' => '',
												'errormessage' => '',
												'status' => '4',
												'statusText' => '',
												'operatorCode' => '',
												'registered' => '',
												'sendtime' => '',
												'delivered' => '');
						$this->printInPre($incomingSMSData);
												
					}else if($_GET['status'] == 5){
						$incomingSMSData = array('msgid' => '',
												'clientmsgId' => '',
												'msisdn' => $tel,
												'errorCode' => '',
												'errormessage' => '',
												'status' => '5',
												'statusText' => '',
												'operatorCode' => '',
												'registered' => '',
												'sendtime' => '',
												'delivered' => '');
						$this->printInPre($incomingSMSData);
					}
				}else{
					return;
				}
			}
			
			
			
			/*
			*	Get subscriptionRequest from db with phonenumber. 
			*	It can only be up to 1 subscription request with statusCode != 0 at any time
			*/						
			$subscriptionRequestQuery = "SELECT * FROM ".$wpdb->prefix.$this->subscriptionRequestTableName." WHERE msisdn_int = '".$incomingSMSData['msisdn']."' AND statusCode != 0 ";
			$subscriptionRequest = $wpdb->get_row($subscriptionRequestQuery);
				
				
			
			/*
			*	find out what the next step for the request is
			*/
			if($subscriptionRequest != null){
				switch($subscriptionRequest->statusCode){
					
					//	1: waiting for the first 
					case 1:
					
						//	first sms delivery report	
						if($incomingSMSData['status'] == 4){
						
							//	update subscription request status to 2
							$updateData = array('statusCode' => 2);
							$whereData = array('id' => $subscriptionRequest->id);
						
							if($_GET['debug'] == "on"){
								$this->printInPre($updateData);
								$this->printInPre($whereData);
							}
						
							$wpdb->update($wpdb->prefix.$this->subscriptionRequestTableName, $updateData, $whereData);
							$this->updateLog($subscriptionRequest->id, 'SMS to customer was sendt successfully: subscription request status set to 2');
							
						}else{
							
							//	terminate subscriptuon request: update subscription request status to 0
							$updateData = array('statusCode' => 0);
							$whereData = array('id' => $subscriptionRequest->id);
						
							if($_GET['debug'] == "on"){
								$this->printInPre($updateData);
								$this->printInPre($whereData);
							}
							
							$wpdb->update($wpdb->prefix.$this->subscriptionRequestTableName, $updateData, $whereData);
							$this->updateLog($subscriptionRequest->id, 'SMS to customer failed: subscription request status set to 0');
						}
						
						
						break;
					
					case 2:
						//	if here: sendegas delivery report is lagging
						break;
					
					
					//	case 3: delivery report for the first sms with price group	
					case 3:
						
				
						if($incomingSMSData['status'] == 4){
							$this->firstPaymentOK($subscriptionRequest, $incomingSMSData);		
						}else{
							$this->firstPaymentFailed($subscriptionRequest, $incomingSMSData);		
						}
						
						break;
					 
					
					case 5:
					
						//	delivery report for the subsequent sms with pricegroup 
						if($incomingSMSData['status'] == 4){
							$this->subsequentPaymentOK($subscriptionRequest, $incomingSMSData);
						}else{
							$this->subsequentPaymentFailed($subscriptionRequest, $incomingSMSData);
						}
						break;
				}
			
			}else{
				
				echo '<h3>En feil oppstod</h3><p></p>';
							
			}
			
		}
		
		
		
		
		
		
		
		
		/*
		*
		*	=incomingSMS
		*	Checks if the incoming number and get the user values
		*	
		*
		*/
		function incomingSMS(){
			global $wpdb;
			
			$userId = '';
			$paymentId = '';
			$subscriptionId = '';
			$subscriptionRequest = null;		
			
			
			$msgid = (!empty($_POST['msgid']) ? esc_html($_POST['msgid']) : '');
			$msisdn = (!empty($_POST['msisdn']) ? esc_html($_POST['msisdn']) : '');
			$msg = (!empty($_POST['msg']) ? esc_html($_POST['msg']) : '');
			$mms = (!empty($_POST['mms']) ? esc_html($_POST['mms']) : '');
			$mmsdata = (!empty($_POST['mmsdata']) ? esc_html($_POST['mmsdata']) : '');
			$shortcode = (!empty($_POST['shortcode']) ? esc_html($_POST['shortcode']) : '');
			$mcc = (!empty($_POST['mcc']) ? esc_html($_POST['mcc']) : '');
			$mnc = (!empty($_POST['mnc']) ? esc_html($_POST['mnc']) : '');
			$pricegroup = (!empty($_POST['pricegroup']) ? esc_html($_POST['pricegroup']) : '');
			$keyword = (!empty($_POST['keyword']) ? esc_html($_POST['keyword']) : '');
			$keywordid = (!empty($_POST['keywordid']) ? esc_html($_POST['keywordid']) : '');
			$errorcode = (!empty($_POST['errorcode']) ? esc_html($_POST['errorcode']) : '');
			$errormessage = (!empty($_POST['errormessage']) ? esc_html($_POST['errormessage']) : '');
		
			
			$incomingSMSData = array(	'msgid' => $msgid,
										'msisdn' => $msisdn,
										'msg' => $msg,
										'mms' => $mms,
										'mmsdata' => $mmsdata,
										'shortcode' => $shortcode,
										'mcc' => $mcc,
										'mnc' => $mnc,
										'keyword' => $keyword,
										'keywordid' => $keywordid,
										'errorcode' => $errorcode,
										'errormessage' => $errormessage
									);
			if(!$msgid){
				
				if(!empty($_GET['debug']) && $_GET['debug'] == "on" && $_GET['token'] = 'knspgdns39cnjksd_ndi438(732ndudvo88(23iub'){
					
					
					if(!empty($_GET['tel'])){
					
						$tel = $_GET['tel'];
					}else{
						$tel = 0000111122;
					}
					
					if(!empty($_GET['key'])){
						$key = $_GET['key'];
					}else{
						$key = 'Ok';
					}
					
					$incomingSMSData = array(	'msgid' => '9871sfddf-984sdgfad-d5sfd4sadfg-5d4s-s5d4gfs56',
												'msisdn' => $tel,
												'msg' => 'matte '.$key,
												'mms' => $mms,
												'mmsdata' => $mmsdata,
												'shortcode' => $key,
												'mcc' => $mcc,
												'mnc' => $mnc,
												'keyword' => $key,
												'keywordid' => $keywordid,
												'errorcode' => $errorcode,
												'errormessage' => $errormessage
								);
								
					$this->printInPre($incomingSMSData);
												
				}else{
					return;
				}
			}
			
			//log it
			$dataDump = implode(", ", $incomingSMSData);
				
			$this->updateLog('0', 'incomingSMS: '.$dataDump);
			
			//	get the subcription request for incoming msisdn which is active ( statusCode > 0 )					
			$subscriptionRequestQuery = "SELECT * FROM ".$wpdb->prefix.$this->subscriptionRequestTableName." WHERE msisdn_int = '".$incomingSMSData['msisdn']."' AND statusCode != 0 ";
			$subscriptionRequest = $wpdb->get_row($subscriptionRequestQuery);
			
			if( $subscriptionRequest == null ){
				
				//	Tell customer they have to visit mattevideo.no and write in the phonenumber there
				$this->sendErrorSMS($incomingSMSData['msisdn'], 'Din ordre ble feil utført. Vennligst gå inn på mattevideo.no og følg rettningslinjene for sms bestilling der.');				
				
			} else {
			
				// customer said yes, lets set up the account, and try to charge customer!
				$keywordInCapitals = strtoupper($incomingSMSData['keyword']);
				
				if($subscriptionRequest->statusCode == 2  && $keywordInCapitals == "OK"){
					
					
					$this->updateLog($subscriptionRequest->id, 'Customers approvement sms recieved');
					$username = $incomingSMSData['msisdn'];
					
					// check if username exists			
					if(($userId = username_exists( $username ))){
						
						$this->updateLog($subscriptionRequest->id, 'Customer exists, reactivate old subscription');
						$this->updateSubscriptionRequest($subscriptionRequest->id, $this->requestStatus[200], 3);	
						
						//	create a new password for user and update user account
						$newPassword = wp_generate_password(8);
						$userData = array('ID' => $userId, 'user_pass' => $newPassword);
						$updatedUserId = wp_update_user($userData);
						$this->updateLog($subscriptionRequest->id, 'Updated user: '.$userId.'  with password: '.$newPassword);
						
						
						$this->updateLog($subscriptionRequest->id, 'Sendt reactivation sms to customer with new password: '.$newPassword);
						$sendResult = $this->sendReActivationSMS($incomingSMSData['msisdn'], $newPassword);
						
						
					}else{
						
						//	create new password and user
						$password = wp_generate_password(8);
						$userFakeMail = $username.time()."@mattevideo.no";
						$userId = wp_create_user( $username, $password, $userFakeMail );
						
						$this->updateLog($subscriptionRequest->id, 'Creating new user: '.$username.', id: '.$userId.', intern fake mail: '. $userFakeMail);	
						$this->updateSubscriptionRequest($subscriptionRequest->id, $this->requestStatus[201], 3);	
						
				
						$this->updateLog($subscriptionRequest->id, 'Sendt activation sms to customer');			
						$sendResult = $this->sendActivationSMS($incomingSMSData['msisdn'], $password);
						
					}
					
					
					// update subscription request with wp_user_id
					$updateData = array('status' => $this->requestStatus[201],
										'wp_user_id' => $userId);
					$whereData = array('id' => $subscriptionRequest->id);
					$updateResult = $wpdb->update($wpdb->prefix.$this->subscriptionRequestTableName, $updateData, $whereData);
					
					$this->updateLog($subscriptionRequest->id, 'updated request with wp_user_id: '.$userId);
					
					//	get existing subscription or create a new one
					if(($subscriptionId = $this->getUserSubscriptionId($subscriptionRequest->msisdn_int))){
	
						//	update subscription
						$updateData = array('subscriptionStatus' => 'Pending');
						$whereData = array('id' => $subscriptionId);
						$updateSubscriptionResult = $wpdb->update($wpdb->prefix.$this->subscriptionTableName, $updateData, $whereData);
										
					}else{
						
						// create new subscription
						$subscriptionId = $this->createNewSubscription($userId, $incomingSMSData['msisdn'], $incomingSMSData['msisdn'], 'Pending');
					}
					
					
					
					/*	create new payment */
					$paymentId = $this->createNewPayment($subscriptionId, 'Initiated', 1);
					$updateData = array('smsSendt' => $this->now(),
										'subscriptionRequestId' => $subscriptionRequest->id);
					$whereData = array('id'=> $paymentId);
					$updatePaymentResult = $wpdb->update($wpdb->prefix.$this->paymentsTableName, $updateData, $whereData);
					
					
					// update requests and logs				
					if($sendResult->Success){	
						
						$this->updateLog($subscriptionRequest->id, $sendResult->ErrorMessage.' MessageID: '.$sendResult->MessageID, $sendResult->ErrorNumber);
						$this->updateSubscriptionRequest($subscriptionRequest->id, $this->requestStatus[300], 3);
					
						
											
						
					}else{
						
						$logItemParams = array(	'subscriptionRequestId' => $subscriptionRequest->id,
												'date' => $this->now(),
												'status' => 'Subscription request sms failed '.$sendResult->ErrorMessage.' MessageID: '.$sendResult->MessageID,
												'statusCode' => 0 );
												
						$this->createNewSubscriptionRequestLogItem($logItemParams);
						
						$this->updateSubscriptionRequest($subscriptionRequest->id, $this->requestStatus[901], 3);
									
					}	
				
				
				}else if($subscriptionRequest->statusCode == 5  && $keywordInCapitals == "STOPP"){
					
					//	customer sendt STOPP to 2440
					$this->updateLog($subscriptionRequest->id, 'Subscription Request stopped by customer');
					
					//	update subscriprion request
					$subscriptionRequestUpdateData = array('statusCode' => 0,	
															'status' => 'Cancelled by user');
					$subscriptionRequestWhereData = array('id' => $subscriptionRequest->id);
					$wpdb->update($wpdb->prefix.$this->subscriptionRequestTableName, $subscriptionRequestUpdateData, $subscriptionRequestWhereData);
					
					//	update payment
					$paymentUpdateData = array('status' => 'Cancelled',
												'modified' => $this->now(),
												'smsReported' => $this->now());
					$paymentWhereData = array('subscriptionRequestId' => $subscriptionRequest->id);
					$wpdb->update($wpdb->prefix.$this->paymentsTableName, $paymentUpdateData, $paymentWhereData);
					
					// update subscription
					$subscriptionUpdateData = array('subscriptionStatus' => 'Cancelled');
					$subscriptionWhereData = array('msisdn_int' => $subscriptionRequest->msisdn_int);
					$wpdb->update($wpdb->prefix.$this->subscriptionTableName, $subscriptionUpdateData, $subscriptionWhereData);
					
					//	send sms to customer
					$this->sendCancellationConfirmedSMS($incomingSMSData['msisdn']);
					
					
				}else{
				
					// Tell customer they dont have any subscription Requests	
					//	Inform customer about mattevideo.no
					$this->sendErrorSMS($incomingSMSData['msisdn'], 'Du må bekrefter meldingen med å skrive "Matte OK", eller matte stopp ved å stoppe abonnementet');				
						
				}
			}
				
		}
		
		
		
		
		
		
		/*
		*
		*
		*
		*/
		function installSendegaSMSPlugin(){
		
			global $wpdb;
			
			
			add_option($this->sendegaSMSVersionOptionName, $this->sendegaSMSVersion);
			require_once(ABSPATH.'wp-admin/includes/upgrade.php');
			
			$insertTablesResult = $this->insertMysqlTables();
				
		}
		
		
		
		
		
		/*
		*	=now
		*	param 0 : datetime | timestamp
		*/
		function now($format = 'datetime'){
		
			if($format == 'timestamp'){
				return time() + 7200;
				
			}else if($format == 'datetime'){
				$timestampNow = time() + 7200;
				return date('Y-m-d H:i:s', $timestampNow );
			}
		
		}
		
		
		
		
		
		
		
		/*
		*
		**
		*
		*/
		function remove_non_numeric($string) {
			return preg_replace('/\D/', '', $string);
		
		}
		
		
		
		
		
		
		
		
		/*
		*
		*
		*
		*/
		function prefix_add_my_stylesheet(){
			$src = WP_PLUGIN_URL."/SendegaSMS/SendegaSMSStyle.css";
			wp_register_style( "SendegaSMSStyle2000", $src);
			wp_enqueue_style( "SendegaSMSStyle2000" );
		}
		
		
		
		
		/*
		*
		*	=printActiveSubscriptions
		*
		*
		*/
		// Print Active Subscriptions
		 function printActiveSubscriptions(){
			 
			global $wpdb;
			$subscriptionQuery = "	SELECT * FROM ".$wpdb->prefix.$this->subscriptionTableName." 
									WHERE subscriptionStatus LIKE 'Active' 
									ORDER BY id DESC LIMIT 400"; 
									
			$subscriptionRows = $wpdb->get_results($subscriptionQuery);
			
			echo '<h3>Active subscriptions: '.count($subscriptionRows).'</h3>';
			
			foreach($subscriptionRows as $subscriptions){
				echo '<ul class="active">';
				$this->printSubscriptionBox($subscriptions, true, true);
				echo '</ul>';
			}
			 
		 }
		
		
		
		
		/*
		*
		*	=printCancelledSubscription
		*
		*
		*/
		 function printCancelledSubscription(){
			 
			global $wpdb;
			$subscriptionQuery = "	SELECT * FROM ".$wpdb->prefix.$this->subscriptionTableName." 
									WHERE subscriptionStatus LIKE 'Cancelled' 
									ORDER BY id DESC LIMIT 400"; 
									
			$subscriptionRows = $wpdb->get_results($subscriptionQuery);
			
			echo '<h3>Cancelled subscriptions: '.count($subscriptionRows).'</h3>';
			
			foreach($subscriptionRows as $subscriptions){
				echo '<ul class="cancelled">';
				$this->printSubscriptionBox($subscriptions);
				echo '</ul>';
			}
			 
		 }
		
		
		
		
		
		
		/*
		*
		* 
		*
		*/
		// !!! admin controll center
		function printControllCenter(){
			
			$this->includeSendegaSMSStyle();
			//	get all subscriptions with payments and log items
			
			global $wpdb;
			
			
			echo '<div class="wrap">
					<h2>Sendega SMS Controll Center</h2>';
			
			//	resend update message
			if(!empty($_POST['resend_msisdn']) && !empty($_POST['todo'])){
				if($_POST['todo'] == 'updateAccount'){
					$current_msisdn = $_POST['resend_msisdn'];
					$current_date = date('Y-m-d H:i:s');
					$result = $this->sendNextSubscriptionPaymentRequestSMS($current_msisdn, $current_date);
					echo '<p class="info">Account update sms sendt to '.$current_msisdn.' on datetime: '.$current_date.' </p>';
				}
			}
			
			$this->printActiveSubscriptions();
			
			$this->printCancelledSubscription();
			
			$this->printPendingSubscriptions();
			
			
			echo '<h2>Logg</h2>';
			for($i = 1; $i <=5; $i++){
			
				$subscriptionRequestQuery = "SELECT * FROM ".$wpdb->prefix.$this->subscriptionRequestTableName." WHERE statusCode = ".$i." ORDER BY id DESC LIMIT 10";
				$subscriptionRequests = $wpdb->get_results($subscriptionRequestQuery);
				
				echo '<div class="subscriptions_box">
						<h2>StatusCode: '.$i.'</h2>';
				foreach($subscriptionRequests as $subscriptionRequest){
					
					echo '<div class="more_info">';
					
					echo '<label>'.$subscriptionRequest->statusCode.' '.$subscriptionRequest->dateRequested.' '.$subscriptionRequest->statusCode.' '.$subscriptionRequest->status.': '.$subscriptionRequest->msisdn_string.' '.$subscriptionRequest->msisdn_int.'</label>';
					
					
					$logQuery = "SELECT * FROM ".$wpdb->prefix.$this->subscriptionRequestLogTableName." WHERE subscriptionRequestId = '".$subscriptionRequest->id."'";
					
					$logResults = $wpdb->get_results($logQuery);
					
					echo '<div class="log_items">';
					
					foreach($logResults as $logResult){
						echo $logResult->date.' '.$logResult->status.'<br/>';
						
						
					}
					echo '	</div>
							</div>';
				}
				
				echo '</div>';
		
			}
			
			
			$subscriptionRequestQuery = "SELECT * FROM ".$wpdb->prefix.$this->subscriptionRequestTableName." WHERE statusCode = 0 ORDER BY id DESC LIMIT 10";
				$subscriptionRequests = $wpdb->get_results($subscriptionRequestQuery);
				
				echo '<div class="subscriptions_box">
						<h2>Resently requests</h2>';
				foreach($subscriptionRequests as $subscriptionRequest){
					
					echo '<div class="more_info">';
					
					echo '<label>'.$subscriptionRequest->statusCode.' '.$subscriptionRequest->dateRequested.' '.$subscriptionRequest->statusCode.' '.$subscriptionRequest->status.': '.$subscriptionRequest->msisdn_string.' '.$subscriptionRequest->msisdn_int.'</label>';
					
					
					$logQuery = "SELECT * FROM ".$wpdb->prefix.$this->subscriptionRequestLogTableName." WHERE subscriptionRequestId = '".$subscriptionRequest->id."'";
					
					$logResults = $wpdb->get_results($logQuery);
					
					echo '<div class="log_items">';
					
					foreach($logResults as $logResult){
						echo $logResult->date.' '.$logResult->status.'<br/>';
						
						
					}
					echo '	</div>
							</div>';
				}
				
				echo '</div>';
		

			
			echo '</div>';
		}
		
		
		
		
		
		
		
		
		function printInPre($stuff){
			
			echo '<pre>';
			print_r($stuff);
			echo '</pre>';
			
		}
		
		
		
		
		/*
		*
		*
		*/
		function printPaymentBox($paymentData){
			
			echo '<li class="payment_box '.$paymentData->status.'">
					<div>'.$paymentData->status.'</div>
					<div>'.$paymentData->smsSendt.'</div>
					<div>'.$paymentData->smsReported.'</div>
				</li>';
			
		}
		
		
		
		
		
		
		
		
		
		
		/*
		*
		*	=printPendingSubscriptions
		*
		*
		*/
		 function printPendingSubscriptions(){
			 
			global $wpdb;
			$subscriptionQuery = "	SELECT * FROM ".$wpdb->prefix.$this->subscriptionTableName." 
									WHERE subscriptionStatus LIKE 'Pending' 
									ORDER BY id DESC LIMIT 400"; 
									
			$subscriptionRows = $wpdb->get_results($subscriptionQuery);
			
			echo '<h3>Pending subscriptions: '.count($subscriptionRows).'</h3>';
			
			foreach($subscriptionRows as $subscriptions){
				echo '<ul>';
				$this->printSubscriptionBox($subscriptions);
				echo '</ul>';
			}
			 
		 }
		
		
		
		
		
		
		
		
		
		/*
		*	=printSignUpForm
		*	=step1
		*
		*
		*/
		function printSignUpForm(){
			
			$content = '';
			$phoneNumberIsValid = false;
			$string_number = '';
			$int_number = '';
			
			
			//	checking incoming post data
			if(!empty($_POST['mattevideo_msisdn'])){
				$string_number = esc_html($_POST['mattevideo_msisdn']); 
				$int_number = $this->remove_non_numeric($string_number);
			}
			
			
			//	checking input
			switch(strlen($int_number)){
				
				case 8:
					$int_number = "47".$int_number;
					$phoneNumberIsValid = true;
					break;
					
				case 10:
					$phoneNumberIsValid = true;
					break;
						
				default:
					$phoneNumberIsValid = false;
					if(!empty($_POST['send'])) $errorMessage = "Nummeret er ikke gyldig";
					break;
			}


			if (!$phoneNumberIsValid){
				
				$this->includeSendegaSMSStyle();
				
				//	print form 
				$content = '
				
				<h3>Opprett bruker<br/>- kun 99kr pr mnd</h3>
				<p>
					Det lønner seg å abonnere på mattevideo.no. Med vårt abonnement får du eget brukernavn og passord som gir deg ubegrenset tilgang til alt innhold.
				</p>
				<ol>
					<li>Skriv inn ditt mobilnummer under og trykk send.</li>
				<form action="" method="post">
				<input type="hidden" name="send" value="number">
				<div class="phone_number_input_box">
					<div id="phone_input">
						<input type="tel" name="mattevideo_msisdn" value="'.(!empty($_POST['mattevideo_msisdn'])? $_POST['mattevideo_msisdn'] : '').'"/><br/>
						<span>'.(!empty($errorMessage)? $errorMessage: '').'</span>
					</div>
					<div id="phone_submit">
						<input type="image" src="'.plugins_url( 'send-knapp.png' , __FILE__ ).'" />
					</div>
				</div>
				<div style="clear:both"></div>
				</form></li>
				
					<li>Du mottar straks en bestilling’s sms. Svar Matte OK på denne sms’en.</li>
					<li>Du mottar nå passord og brukernavn på en ny sms. Ditt abonnement er aktivert</li>
					<li>Du kan nå logge deg inn på mattevideo.no</li>
				</ol>
				<p>
				Du kan når som helst avslutte ditt abonnement ved å sende Matte Stopp til 2440, da avsluttes ditt abonnement fra og med neste måned. Som abonnent blir din mobil trukket 99 kr pr mnd som belastes ditt kotantkort eller mobilregning. Kontakt gjerne vår kundeservice på tlf 98 60 61 58 om du har spørsmål. Kjøpsvilkår finner du <a title="Kjøpsvilkår" href="http://www.mattevideo.no/kjopsvilkar">her</a>.
				</p>';
				
			}else{
			
				//	check if phone number already has a subscriptionRequest
				global $wpdb;
				$existingSubscriptionRequest = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.$this->subscriptionRequestTableName." WHERE msisdn_int = '".$int_number."' AND statusCode != 0");
				
				
				if($existingSubscriptionRequest != null){
					
					if($existingSubscriptionRequest->statusCode == 2){
						$content = '<h2>Det er allerede sendt et bestillings sms til dette nummeret. Svar "Matte OK" på den allerede sendte smsen. Hvis du ikke har denne smsen, send "Matte OK" til 2440.</h2>';
					}else if($existingSubscriptionRequest->statusCode == 4 || $existingSubscriptionRequest->statusCode == 5){
						$content = '<h2>Dette nummeret er allerede abonnent hos mattevideo.</h2>';
					}else{
					/**/
						//	if customer is requesting more than one subscriptions
						$content = '<h2>Det har oppstått en feil</h2> <p>Feilen er sendt til Mattevideo, og vil se på feilen så raskt som mulig.</p>';
					}
				
				
				//	customer do not have a valid subscriptionRequest					
				}else{

					// Check if username exists
					if(($user_id = username_exists($int_number))){
											
						if(($subscription = $this->getUserSubscription($int_number))){
							
							
							if($subscription->subscriptionStatus == 'Active'){
								//	user exists with active subscription
								$content = '<div class="phone_number_input_box">
											<h2>Du har allerede et gyldig abonnement!</h2>
										</div>';
							
							}else{
								
								//	user exists and subscription exists, but subscription is not active
								
								//	re activate subscription
								$subscriptionRequestParams = array(	'wp_user_id' => $user_id,
																'msisdn_int' => $int_number,
																'msisdn_string' => $string_number,
																'dateRequested' => $this->now(),
																'status' => $this->requestStatus[100],
																'statusCode' => 5,
																'errorCode' => 0
																);
								$subscriptionRequestId = $this->createNewSubscriptionRequest($subscriptionRequestParams);
							
								$this->updateLog($subscriptionRequestId, 'Sending sms to existing customer');
								
								$sendResult = $this->sendReConfirmSubscriptionSMS($int_number);
							
							}
							
						}else{
							
							//	user exists but dont have any subscription yet
							$this->updateLog($subscriptionRequestId, 'Creating new subscription for existing user');
							
							//	create new subscriptionRequest
							$subscriptionRequestParams = array(	'wp_user_id' => $user_id,
																'msisdn_int' => $int_number,
																'msisdn_string' => $string_number,
																'dateRequested' => $this->now(),
																'status' => $this->requestStatus[100],
																'statusCode' => 1,
																'errorCode' => 0
																);
							$subscriptionRequestId = $this->createNewSubscriptionRequest($subscriptionRequestParams);
							
							
							//	send sms to customer
							$this->updateLog($subscriptionRequestId, 'Sending confirmation sms to customer');
							$sendResult = $this->sendConfirmSubscriptionSMS($int_number);
							
						}
						
							
							
							
					// user name does not exists
					}else{
					
						// create new subscription request
						$subscriptionRequestParams = array(	'wp_user_id' => '',
															'msisdn_int' => $int_number,
															'msisdn_string' => $string_number,
															'dateRequested' => $this->now(),
															'status' => $this->requestStatus[100],
															'statusCode' => 1,
															'errorCode' => 0				
															);
															
						$subscriptionRequestId = $this->createNewSubscriptionRequest($subscriptionRequestParams);
						
						$this->updateLog($subscriptionRequestId, 'Sending sms to new customer from serverip: '.$_SERVER['SERVER_ADDR']);	
						
						$sendResult = $this->sendConfirmSubscriptionSMS($int_number);

					}
					
					
					if($sendResult->Success){	
						
						$this->updateLog($subscriptionRequestId, $sendResult->ErrorMessage.' MessageID: '.$sendResult->MessageID, $sendResult->ErrorNumber);
						
						$this->updateSubscriptionRequest($subscriptionRequestId, 'Waiting delivery report', 1);
						$content = '<h2>Takk for at du bestilte tilgang til mattevideo.no!</h2>
									<p>Du mottar straks en bestilling’s sms. Svar <strong>Matte OK</strong> på denne sms’en for å aktivere ditt abonnement. </p>
									<p>Ikke mottatt bestilling’s sms? Ikke nøl med å kontakte oss på tlf 98 60 61 58 eller epost: kjartan@hjemmesidefilm.no</p>
									';
						
						
					}else{
						
						/*$logItemParams = array(	'subscriptionRequestId' => $subscriptionRequestId,
												'date' => $this->now(),
												'status' => 'Subscription request sms failed',
												'statusCode' => 0
												);
					
						$this->createNewSubscriptionRequestLogItem($logItemParams);
						*/
						$this->updateLog($subscriptionRequestId, $sendResult->ErrorMessage.' MessageID: '.$sendResult->MessageID, $sendResult->ErrorNumber);
						
						$this->updateSubscriptionRequest($subscriptionRequestId, $this->requestStatus[900], 0);
						$content = '<h2>Obs!</h2><p>En feil oppstod når vi prøvde å sende en sms til deg.</p>';	
						//	printFail() print feilmeldingen =inprogress !!!	
					}
				}
			}
			
			$wrap = '
					<div id="start-her">
						<div class="start-her-content">
						'.$content.'
						</div>
					</div>
					';
			
			return $wrap;
		}
		
		
		
		
		
		
		
		
		
		/*
		*
		*
		*
		*/
		// !!! Print subscription box
		function printSubscriptionBox($subscriptionData, $showPayments = true, $showSendSmsButtonActive = false){
			
			if(strtotime('+1 week') > strtotime($subscriptionData->subscriptionValidToDate)) $sendSmsButtonActive = true;
			
			echo '<li class="subscriptions_box" >
						<div class="user-data">
						<label class="user">User: '.$subscriptionData->msisdn_string.'</label>
						<label class="status">'.$subscriptionData->subscriptionStatus.'</label>
						<label class="validto">Valid to: '.$subscriptionData->subscriptionValidToDate.'</label>';
			
			if($sendSmsButtonActive && $showSendSmsButtonActive){
				echo'		<form action="options-general.php?page=sendegasms" method="post">
							<input type="hidden" name="resend_msisdn" value="'.$subscriptionData->msisdn_string.'" />
							<input type="hidden" name="todo" value="updateAccount" />
							<input type="submit" name="updateSubscription" value="Update account" '.($sendSmsButtonActive ? "" : 'disabled="disabled"').' />
						</form>';
			}
			echo		'</div>';
						
			//get 
			if($showPayments){
				global $wpdb;
				
				$paymentsQuery = "SELECT * FROM ".$wpdb->prefix.$this->paymentsTableName." WHERE subscriptionId = '".$subscriptionData->id."' ORDER BY id ASC";
				$paymentsRows = $wpdb->get_results($paymentsQuery);
				echo '<ul class="payment_box"><li class="payment_box '.$paymentData->status.'">
								<div>Status:</div>
								<div>Sms sendt:</div>
								<div>Sms reported:</div>
							</li>';
				foreach($paymentsRows as $paymentsRow){
					
					$this->printPaymentBox($paymentsRow);
					
				}
				echo '</ul>';
			}
			echo '</li>';
		}
		
		
		
		
		
		
		
		
		/*
		*
		*
		*/
		function register_controll_center(){
			add_submenu_page('options-general.php', 'Sendega SMS Controll Center', 'SMS Controll Center', 'add_users', 'sendegasms', array($this,'printControllCenter'));
		}
		
		
		
		
		
		
		/*
		*
		*	=sendActivationSMS
		*
		*
		*/
		function sendActivationSMS($phoneNumber, $password){
			require_once ABSPATH.'wp-content/plugins/SendegaSMS/classes/Sendega.php';
			
			$msg = sprintf($this->smsMessages['SMS_2ab'], $phoneNumber, $password);
			
			$deliveryReportUrl = get_bloginfo('url').'/deliveryReport';
			$this->updateLog('0', 'SendActivationSMS phonenumber: '.$phoneNumber.', deliveryReportUrl: '.$deliveryReportUrl);
			
			$sender = new Sendega(get_bloginfo('url').'/deliveryReport');
			
			$sendResult = $sender->sendSms($phoneNumber, $msg, $extId = 0, $priceGroup = $this->monthlyAmount);
			
			return $sendResult;
		}
		
		
		
		
		
		/*
		*	=sendCancellationConfirmedSMS
		*
		*
		*/
		function sendCancellationConfirmedSMS($phoneNumber){
		
			require_once ABSPATH.'wp-content/plugins/SendegaSMS/classes/Sendega.php';
			$deliveryReportUrl = get_bloginfo('url').'/deliveryReport';
			$this->updateLog('0', 'deliveryReportUrl: '.$deliveryReportUrl);
			
			$sender = new Sendega(get_bloginfo('url').'/deliveryReport');
			$msg = $this->smsMessages['SMS_5'];
			
			$sendResult = $sender->sendSms($phoneNumber, $msg, $extId = 0, $priceGroup = 0);
			
			return $sendResult;
		}		
				
			
			
				
		
				
		/*
		*
		*	=sendConfirmSubscriptionSMS
		*
		*
		*/
		function sendConfirmSubscriptionSMS($phoneNumber){
		
			if(empty($phoneNumber)){
				echo 'Error no msisdn: <br/>';
				return false;
			}
			require_once ABSPATH.'wp-content/plugins/SendegaSMS/classes/Sendega.php';
			$msg = $this->smsMessages['SMS_1ab'];
			$deliveryReportUrl = get_bloginfo('url').'/deliveryReport';
			$this->updateLog('0', 'deliveryReportUrl: '.$deliveryReportUrl);
			
			$sender = new Sendega(get_bloginfo('url').'/deliveryReport');
			
			$sendResult = $sender->sendSms($phoneNumber, $msg, $extId = 0, $priceGroup = 0);
			
			return $sendResult;
		}
		
		
		
		
		
		/*
		*
		*	=sendErrorSMS
		*
		*/
		function sendErrorSMS($phoneNumber, $msg){
			require_once ABSPATH.'wp-content/plugins/SendegaSMS/classes/Sendega.php';
			
			$sender = new Sendega(get_bloginfo('url').'/deliveryReport');
			$deliveryReportUrl = get_bloginfo('url').'/deliveryReport';
			$this->updateLog('0', 'deliveryReportUrl: '.$deliveryReportUrl);
			
			$sendResult = $sender->sendSms($phoneNumber, $msg, $extId = 0, $priceGroup = 0);
			
			return $sendResult;
		}
		
		
		
		
		
		
		/*
		*	=sendNextSubscriptionPaymentRequestSMS
		*
		*
		*/
		function sendNextSubscriptionPaymentRequestSMS($msisdn, $dateToSendSMS){
			
			require_once ABSPATH.'wp-content/plugins/SendegaSMS/classes/Sendega.php';
			
			$sender = new Sendega(get_bloginfo('url').'/deliveryReport');
			
			$sendResult = $sender->sendSms($msisdn, $this->smsMessages['SMS_3'], '', $this->monthlyAmount, $dateToSendSMS);

			return $sendResult;
			
		}
		
		
		
		
		/*
		*	=TEST_sendNextSubscriptionPaymentRequestSMS
		*
		*
		*/
		function TEST_sendNextSubscriptionPaymentRequestSMS($msisdn, $dateToSendSMS){
			
			require_once ABSPATH.'wp-content/plugins/SendegaSMS/classes/Sendega.php';
			
			$sender = new Sendega(get_bloginfo('url').'/deliveryReport');
			
			$sendResult = $sender->sendSms($msisdn, $this->smsMessages['SMS_3'], '', 0, $dateToSendSMS);

			return $sendResult;
			
		}
		
		
		
		
		
		/*
		*	=sendPaymentFailedSMS
		*
		*/
		function sendPaymentFailedSMS($phoneNumber){
			require_once ABSPATH.'wp-content/plugins/SendegaSMS/classes/Sendega.php';
			
			$msg = $this->smsMessages['SMS_4ab'];
			
			$sender = new Sendega(get_bloginfo('url').'/deliveryReport');
			
			$sendResult = $sender->sendSms($phoneNumber, $msg, $extId = 0, $priceGroup = $this->monthlyAmount);
			
			return $sendResult;
		}
		
		
		
		
		
		
		
		/*
		*	=sendReActivationSMS
		*
		*
		*/
		function sendReActivationSMS($phoneNumber, $password){
			require_once ABSPATH.'wp-content/plugins/SendegaSMS/classes/Sendega.php';
			$msg = sprintf($this->smsMessages['SMS_2ab'], $phoneNumber, $password);
			
			$sender = new Sendega(get_bloginfo('url').'/deliveryReport');
			
			$sendResult = $sender->sendSms($phoneNumber, $msg, $extId = 0, $priceGroup = $this->monthlyAmount);
			
			return $sendResult;
		}
		
		
		
		
		
		
		
		
		
		
		
		/*
		*
		*	=sendReConfirmSubscription
		*
		*
		*/
		function sendReConfirmSubscriptionSMS($phoneNumber){
			
			require_once ABSPATH.'wp-content/plugins/SendegaSMS/classes/Sendega.php';
			$msg = $this->smsMessages['SMS_1ab'];
			
			$sender = new Sendega(get_bloginfo('url').'/deliveryReport');
			
			$sendResult = $sender->sendSms($phoneNumber, $msg, $extId = 0, $priceGroup = 0);
			
			return $sendResult;
		
		
		}
		
		
		
		
		
		/*
		*	=setUpNextPayment
		*
		*/
		function setUpNextPayment($subscriptionId, $smsSendDate){
			
			global $wpdb;
			
			$subsciption = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.$this->subscriptionTableName." WHERE id = '".$subscriptionId."'");
			
			//	create new subscriptionRequest			
			$subscriptionData = array(	'wp_user_id' => $subsciption->wp_user_id,
										'msisdn_int' => $subsciption->msisdn_int,
										'msisdn_string' => $subsciption->msisdn_int,
										'dateRequested' => $this->now(),
										'status' => 'Initiated continuing  payment request',
										'statusCode' => 5,
										);
			
			$subscriptionRequestId = $this->createNewSubscriptionRequest($subscriptionData);			
			
			
			//	create new payment
			$paymentId = $this->createNewPayment($subscriptionId, 'Pending', 5, $subscriptionRequestId, $smsSendDate);
			
			//	Log
			$this->updateLog($subscriptionRequestId, 'Setting up sms with pricegroup: '.$this->monthlyAmount.' for date: '.$smsSendDate.' to '.$subsciption->msisdn_int);
			
			
			/*
				This function is deactivated because it started to send out multiple sms to customer, due to unknown problems
				
			*/
			//	sendNextSubscriptionRequestSMS
			//$sendResult = $this->sendNextSubscriptionPaymentrequestSMS($subsciption->msisdn_int, $smsSendDate);							
			
			//	Log result
			/*
			if(!empty($sendResult->ErrorMessage)) $errorMessage = $sendResult->ErrorMessage;
			if(!empty($sendResult->Success)) $success = $sendResult->Success;
			if(!empty($sendResult->ErrorNumber)) $errorNumber = $sendResult->ErrorNumber;
			if(!empty($sendResult->MessageID)) $messageId = $sendResult->MessageID;
			
			*/
			/*
				Old update log for functionality above
			*/
			//$this->updateLog($subscriptionRequestId, "Send result. Error: ".$errorMessage. ". Success: " . $success . " messageId: " . $messageId , $errorNumber);
			
			$this->updateLog($subscriptionRequestId, "Created a new payment and subscriptionRequest. Did not send any sms.");
			
		}
		
		
		
		
		/*
		*	=subsequentPaymentFailed
		*
		*/
		function subsequentPaymentFailed($subscriptionRequest, $incomingData){
			
			global $wpdb;
			
			
			//	update payment
			$paymentUpdateData = array('status' => 'Payment Failed',
										'modified' => $this->now(),
										'smsReported' => $this->now());
			$paymentWhereData = array('subscriptionRequestId' => $subscriptionRequest->id); 
			$updatePaymentResult = $wpdb->update($wpdb->prefix.$this->paymentsTableName, $paymentUpdateData, $paymentWhereData);
			
			//	update subscriptionRequest
			//	update subscriptionRequest
			$this->updateSubscriptionRequest($subscriptionRequest->id, $this->requestStatus[901], 0);
			
			
			//	update subscription
			$subscriptionStatus = 'Deactive';
			$msisdn = $incomingData['msisdn'];
			
			$msisdn_int = $subscriptionRequest->msisdn_int;
			$userId = $subscriptionRequest->wp_user_id;
			
			$subscriptionUpdateData = array('subscriptionStatus' => $subscriptionStatus);
			$subscriptionWhereData = array('wp_user_id' => $userId);
			
			$updateResult = $wpdb->update($wpdb->prefix.$this->subscriptionTableName, $subscriptionUpdateData, $subscriptionWhereData);
		
			
			//	tell customer
			$this->sendPaymentFailedSMS($incomingData['msisdn']);
		}
		
		
		
		
		
		
		
		
		
		/*
		*
		*	=subsequentPaymentOK
		*
		*/
		function subsequentPaymentOK($subscriptionRequest, $incomingData){
			
			global $wpdb;
			
			$this->updateLog($subscriptionRequest->id, 'Subsequent payment ok');
			
			
			//	Update Payment
			$paymentUpdateData = array('status' => 'Payment OK',
										'modified' => $this->now(),
										'smsReported' => $this->now());
			$paymentWhereData = array('subscriptionRequestId' => $subscriptionRequest->id); 
			
			$updatePaymentResult = $wpdb->update($wpdb->prefix.$this->paymentsTableName, $paymentUpdateData, $paymentWhereData);
			
			
			//	updateSubscriptionRequest
			$this->updateSubscriptionRequest($subscriptionRequest->id, $this->requestStatus[300], 0);
			
				
			//	update subscription
			/*
			$updateData = array("status" => $this->requestStatus[300]);
			$whereData = array("id" => $subscriptionRequest->id);
			
			$this->printInPre($updateData);
			$this->printInPre($whereData);
			
			$updateSubscriptionRequestResult = $wpdb->update($wpdb->prefix.$this->subscriptionRequestTableName, $updateData, $whereData);
			
			*/
			
			//	update subscription
			
			$msisdn_int = $subscriptionRequest->msisdn_int;
			$userId = $subscriptionRequest->wp_user_id;
			
			$subscription = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix.$this->subscriptionTableName." WHERE wp_user_id = '".$userId."'" );
			
			//echo '$subscription->subscriptionValidToDate: '.$subscription->subscriptionValidToDate.'<br/>';
			
			$currentlyValidTo = strtotime($subscription->subscriptionValidToDate);
			
			
			$nextMonth = strtotime('+1 month');
			$now = $this->now('timestamp');
			$month = $nextMonth - $now;
			
			$newValidToDate = $currentlyValidTo + $month;
				
			$updateData = array('subscriptionValidToDate' => date('Y-m-d H:i:s', $newValidToDate),
								'subscriptionStatus' => 'Active');
								
			$whereData = array('id' => $subscription->id);
			$updateResult = $wpdb->update($wpdb->prefix.$this->subscriptionTableName, $updateData, $whereData);		
				
			
			$subscriptionId = $subscription->id;
			
			// !!! 	set up next payment
			
			$nextSubscriptionPaymentRequestDate = date('Y-m-d H:i:s', strtotime('now') + 7500);
			$nextSubscriptionPaymentRequestDate = date('Y-m-d H:i:s', strtotime('+1 month') + 3600);
			
			$this->setUpNextPayment($subscriptionId, $nextSubscriptionPaymentRequestDate);
		}
		
		
		
		
		
		/*
		*
		*
		*
		*/
		function uninstallSendegaSMSPlugin(){
			global $wpdb;
			require_once(ABSPATH.'wp-admin/includes/upgrade.php');
			
			//	drop tables
			//	inprogress: need backup function!!!
			/*
			$dropTableSql = "DROP TABLE ".$wpdb->prefix.$this->subscriptionTableName;
			$dropResult = $wpdb->query($dropTableSql);
			
			if(!$dropResult){
				echo $dropResult;
			}
			
			$dropTableSql = "DROP TABLE ".$wpdb->prefix.$this->subscriptionRequestTableName;
			$dropResult = $wpdb->query($dropTableSql);
			
			if(!$dropResult){
				echo $dropResult;
			}
			
			$dropTableSql = "DROP TABLE ".$wpdb->prefix.$this->subscriptionRequestLogTableName;
			$dropResult = $wpdb->query($dropTableSql);
			
			if(!$dropResult){
				echo $dropResult;
			}			
			
			$dropTableSql = "DROP TABLE ".$wpdb->prefix.$this->paymentsTableName;
			$dropResult = $wpdb->query($dropTableSql);
			
			if(!$dropResult){
				echo $dropResult;
			}
			*/
		}
		
		
		
		
		/*
		*	=updateLog
		*
		*/
		function updateLog($subscriptionId, $message, $statusCode = 0){
			
			$logData = array(	'subscriptionRequestId' => $subscriptionId,
										'date' => date('Y-m-d H:i:s'),
										'status' => $message,
										'statusCode' => $statusCode);
								
			$this->createNewSubscriptionRequestLogItem($logData);
		
		}
		
		
		
		/*
		*
		* =updatePayment
		*
		*
		*/
		function updatePayment($paymentId, $status, $statusCode){
			
			global $wpdb;
			$updateData = array('status' => $status, 'statusCode' => $statusCode);
			$whereData = array('id' => $paymentId);
			$updateResult = $wpdb->update($wpdb-prefix.$this->paymentsTableName, $updateData, $whereData);
			
			return $updateResult;
			
		}
		
		
		
		
		
		
		/*
		*	=updateSubscription
		*
		*
		*
		*/
		function updateSubscription($subscriptionId, $subscriptionValidToDate, $subsriptionStatus){
			global $wpdb;
			$updateData = array('subscriptionValidToDate' => $subscriptionValidToDate,
								'subscriptionStatus' => $subsriptionStatus);
			$whereData = array('id' => $subscriptionId);				
			$updateResult = $wpdb->update($wpdb->prefix.$this->subscriptionTableName, $updateData, $whereData);
			
			return $updateResult;
		}
		
		
		
		/*
		*
		* =updateSubscriptionRequest
		*
		*
		*/
		function updateSubscriptionRequest($subscriptionRequestId, $updateMessage, $statusCode, $updateErrorCode = 0){
			global $wpdb;
			
			if(empty($subscriptionRequestId)){
				
			}
			
			$updateData = array('status' => $updateMessage, 
								'statusCode' => $statusCode,
								'errorCode' => $updateErrorCode);
				
			$whereData = array('id' => $subscriptionRequestId);
			$updateResult = $wpdb->update($wpdb->prefix.$this->subscriptionRequestTableName, $updateData, $whereData);
			
			if(!$updateResult){
				//echo 'Error on updating subscriptionRequest<br/>';
			}	
		
		}
		
		
		
		
		
		
		
		
		/*
		*
		*
		*
		*/
		function userHasValidSubscription($userName){
			
			global $wpdb;
			$wp_user_id = username_exists($userName);
			$result = $wpdb->get_row(" SELECT * FROM ".$wpdb->prefix.$this->subscriptionTableName." WHERE wp_user_id = '$wp_user_id'");
			
			if($result->status == "Active"){
				return $result;
			}else{
				return false;
			}
		}
		
		
		
		
	
	
	
	
	}//	end of kapittelvelger
}


//	! --------- SendegaSMS class Ended ------------------ 

/**
 * Mattevideo_chapter_chooser Class
 */
class SendegaSMS_number_input_widget extends WP_Widget {

    /** constructor */
    function SendegaSMS_number_input_widget() {
        parent::WP_Widget(false, $name = 'SendegaSMS Telefonnummer');
    	
    }
	

    /*
    *	Listing chapters and subchapters (categories and subcategories)
    *
    */
    function widget($args, $instance) {
    	
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
        
        echo $before_widget;
        if ( $title ) echo $before_title . $title . $after_title;
        
        
        echo '
        		<div class="veiviser">
       				<h1 id="veiviser_header">Videoveiviser</h1>
       				<select class="veiviser" name="pensum" id="pensum">
       					<option value="">Velg mattebok:</option>
       					<option value="matematikk1t">Matematikk 1T</option>
       					<option value="sinus1t">Sinus 1T</option>
       					<option value="matematikk1p">Matematikk 1P</option>
       					<option value="sinus1p">Sinus 1P</option>
       				</select>
       				<select class="veiviser" id="chapter"><option></option></select>
       				<select class="veiviser" id="subchapter"><option></option></select>
        				
       				<div id="chapterlinks"></div>
        				
   				</div>';
        
        echo $after_widget; 
        
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }


    /** @see WP_Widget::form */
    function form($instance) {
        $title = esc_attr($instance['title']);
        ?>
         <p>
          <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label> 
          <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <?php 
    }

} // class chapterchooser 





/*	1.	*/
if(class_exists("SendegaSMS")){
	$sendegaSMS = new SendegaSMS();
}

/*	3	*/
if($sendegaSMS){


	/*
	*	Activation code
	*/
	register_activation_hook(__FILE__, array(&$sendegaSMS, 'installSendegaSMSPlugin'));
	
	
	
	
	/*
	*	Deactivation code
	*/
	register_deactivation_hook( __FILE__, array(&$sendegaSMS, 'uninstallSendegaSMSPlugin'));

}
