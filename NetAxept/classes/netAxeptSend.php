<?php

class Send {

	function Send(){
	
		$params["username"]	= "6803";
		$params["password"]	= "pS5D773Ylx";
		$params["sender"]	= "MatteFilm.no";
		$params["pricegroup"] = 0;
		$params["contentTypeID"] = 1;
		$params["contentHeader"] = "";
		$params["ageLimit"]	= 0;
		$params["sendDate"]	= "";
		$params["refID"]	= "";
		$params["priority"]	= 0;
		$params["gwID"]	= 0;
		$params["pid"]	= 0;
		$params["dcs"]	= 0;
		
	}
	
	/*
	*	Send standar sms (standar delivery report url)
	*/
	function sendSms($msisdn, $msg){
		
		if(strlen($msisdn) && strlen($msg)){
		
			$params["username"]	= "6803";
			$params["password"]	= "pS5D773Ylx";
			$params["sender"]	= "2440";
			$params["pricegroup"] = 0; // i ¿re
			$params["contentTypeID"] = 1; //2
			$params["contentHeader"] = "";
			$params["ageLimit"]	= 0;
			$params["sendDate"]	= "";
			$params["refID"]	= "";
			$params["priority"]	= 0;
			$params["gwID"]	= 0;
			$params["pid"]	= 0;
			$params["dcs"]	= 0;
			
			$params["dlrUrl"]	= "http://mattevideo.no/incoming/receiveDeliveryReport.php";	
			$params["extID"]	= md5($msisdn.$key);	
			$params["destination"]	= $msisdn;
			$params["content"]	= $msg;
			
			try{
			$content = new SoapClient( "https://smsc.sendega.com/Content.asmx?wsdl",
										array( 'trace'	=> true, 
												'exceptions' => true)
										);
			
			}catch (SoapFault $e){
				echo '<pre>';
				print_r($e);
				echo ' </pre>';
			}
			/*
			$response = $content->Send($params);
			$serverResult = $response->SendResult;
			
			if( $serverResult->Success ){
				return "Message was sent. Id: ".$serverResult->MessageID;
			}else{
				return "Message was not sent. Errornumber: ".$serverResult->ErrorNumber.", Errormessage: ".$serverResult->ErrorMessage;
			}	
			*/
		}else{
		
			//echo "Missing parameters: sendSms($msisdn, $msg)<br/>"; 
		}
	}
	
	
	/*
	*	Production (with pricegroup)
	*	Send standar sms (standar delivery report url)
	*/
	function sendSmsWithPriceGroup($msisdn, $msg, $priceGroup){
		
		$params["username"]	= "6803";
		$params["password"]	= "pS5D773Ylx";
		$params["sender"]	= "2440";
		$params["pricegroup"] = $priceGroup; // i øre
		$params["contentTypeID"] = 2; //2
		$params["contentHeader"] = "";
		$params["ageLimit"]	= 0;
		$params["sendDate"]	= "";
		$params["refID"]	= "";
		$params["priority"]	= 0;
		$params["gwID"]	= 0;
		$params["pid"]	= 0;
		$params["dcs"]	= 0;
		
		$params["dlrUrl"]	= "http://mattevideo.no/incoming/receiveDeliveryReport.php";	
		$params["extID"]	= md5($msisdn.$key);	
		$params["destination"]	= $msisdn;
		$params["content"]	= $msg;
		
		$content = new SoapClient( "https://smsc.sendega.com/Content.asmx?wsdl",
									array( 'trace'	=> true, 
											'exceptions' => true)
									);
		/*$response = $content->Send($params);
		
		$serverResult = $response->SendResult;
		
		if( $serverResult->Success ){
			return "Message was sent. Id: ".$serverResult->MessageID;
		}else{
			return "Message was not sent. Errornumber: ".$serverResult->ErrorNumber.", Errormessage: ".$serverResult->ErrorMessage;
		}*/	
	}
	
	
	/*
	*	Send update key sms (update key delivery report url)
	*/
	function sendUpdateKeySms($msisdn, $msg, $extID){
		$params["username"]	= "6803";
		$params["password"]	= "pS5D773Ylx";
		$params["sender"]	= "2440";
		$params["pricegroup"] = 0;
		$params["contentTypeID"] = 1;
		$params["contentHeader"] = "";
		$params["ageLimit"]	= 0;
		$params["sendDate"]	= "";
		$params["refID"]	= "";
		$params["priority"]	= 0;
		$params["gwID"]	= 0;
		$params["pid"]	= 0;
		$params["dcs"]	= 0;
		
		$params["dlrUrl"]	= "http://mattevideo.no/incoming/receiveUpdateKeyReport.php";	
		$params["extID"]	= $extID;
		$params["destination"]	= $msisdn;
		$params["content"]	= $msg;
		
		$content = new SoapClient( "https://smsc.sendega.com/Content.asmx?wsdl",
									array( 'trace'	=> true, 
											'exceptions' => true)
									);
		/*
		$response = $content->Send($params);
		$serverResult = $response->SendResult;
		
		if( $serverResult->Success ){
			return "Message was sent. Id: ".$serverResult->MessageID;
		}else{
			return "Message was not sent. Errornumber: ".$serverResult->ErrorNumber.", Errormessage: ".$serverResult->ErrorMessage;
		}*/		
	}
	
	
	/*
	*	Production (with price group)
	*	Send update key sms (update key delivery report url)
	*/
	function sendUpdateKeySmsWithPriceGroup($msisdn, $msg, $extID, $priceGroup){
		$params["username"]	= "6803";
		$params["password"]	= "pS5D773Ylx";
		$params["sender"]	= "2440";
		$params["pricegroup"] = $priceGroup;
		$params["contentTypeID"] = 2;
		$params["contentHeader"] = "";
		$params["ageLimit"]	= 0;
		$params["sendDate"]	= "";
		$params["refID"]	= "";
		$params["priority"]	= 0;
		$params["gwID"]	= 0;
		$params["pid"]	= 0;
		$params["dcs"]	= 0;
		
		$params["dlrUrl"]	= "http://mattevideo.no/incoming/receiveUpdateKeyReport.php";	
		$params["extID"]	= $extID;
		$params["destination"]	= $msisdn;
		$params["content"]	= $msg;
		
		$content = new SoapClient( "https://smsc.sendega.com/Content.asmx?wsdl",
									array( 'trace'	=> true, 
											'exceptions' => true)
									);
		/*$response = $content->Send($params);
		$serverResult = $response->SendResult;
		
		if( $serverResult->Success ){
			return "Message was sent. Id: ".$serverResult->MessageID;
		}else{
			return "Message was not sent. Errornumber: ".$serverResult->ErrorNumber.", Errormessage: ".$serverResult->ErrorMessage;
		}	*/	
	}


}

?>