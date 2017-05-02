<?php

class Sendega {
	
	var $params = array("username" => "6803", 
						"password" => "pS5D773Ylx",
						"sender" => "2440",
						"destination" => "0",
						"pricegroup" => 0,
						"contentTypeID" => 1,
						//"contentHeader" => "",
						"dlrUrl" => "",
						"ageLimit" => 0,
						"extID" => "",
						"sendDate" => "",
						//"refID" => "",
						"priority" => 0,
						"gwID" => 0,
						"pid" => 0,
						"dcs" => 0);
	
	var $sendegaSoapClient = "https://smsc.sendega.com/Content.asmx?wsdl";
	var $sendegaSoapParams = array( 'trace'	=> true, 'exceptions' => true);
	
	
	function Sendega($dlrUrl = ""){
		
		if(!empty($dlrUrl)){
			$this->params['dlrUrl'] = $dlrUrl;
			
		}else{
			$this->params['dlrUrl'] = "0";
		}		
	}
	
	
	
	/*
	*	Send standar sms (standar delivery report url)
	*/
	function sendSms($msisdn, $msg, $extId = 0, $priceGroup = 0, $dateToSendSMS = ""){
		
		if(empty($msisdn))
			return false;
		
		$this->params["extID"]			= $extId;	
		$this->params["destination"]	= $msisdn;
		$this->params["content"]		= $msg;
		$this->params["pricegroup"] 	= $priceGroup;
		$this->params["sendDate"] 		= $dateToSendSMS;
		
		//$vars = var_export($this->params, true);
		//$this->writeLog($vars);
		
		$soapClient = new SoapClient( $this->sendegaSoapClient, $this->sendegaSoapParams);
		
		$response = $soapClient->Send($this->params);
			
		$serverResult = $response->SendResult;
	
		return $serverResult;	
	}
	
	
	
	
	function testSend(){
	
	
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
		
		$params["dlrUrl"]	= "";	
		$params["extID"]	= "";	
		$params["destination"]	= "4795926551";
		$params["content"]	= "testing message 1";
		
		
		$content = new SoapClient( "https://smsc.sendega.com/Content.asmx?wsdl",
									array( 'trace'	=> true, 
											'exceptions' => true)
									);
		$response = $content->Send($params);
		$serverResult = $response->SendResult;
		
		if( $serverResult->Success ){
			return "Message was sent. Id: ".$serverResult->MessageID;
		}else{
			return "Message was not sent. Errornumber: ".$serverResult->ErrorNumber.", Errormessage: ".$serverResult->ErrorMessage;
		}	
	
	}
	
	
	function writeLog($data){
		
		$myFile = "log.txt";
		$fh = fopen($myFile, 'a') or die("can't open file");
		
		fwrite($fh, $data);
		
		fclose($fh);
	}
	
}

?>