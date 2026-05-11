<?php

class WooWA extends BaseClass{
   
   function __construct(){
		
		parent::__construct();
        $this->secretkey =  $this->loadSetting('WAGatewayAPIKey');
    
        $this->includeClassDependencies(array(
            'APIScheduler.class.php'
        ));
       
   }
    
	
	function sendAsync($phoneNumber, $content, $scheduler = false){
		$data = array(
			  "phone_no"  => $this->formatPhoneNumber($phoneNumber),
			  "message"   => $content,
			  "skip_link" => True // This optional for skip snapshot of link in message
			);
		
		$url = 'async_send_message';
		$action = 'POST';
		
		if($scheduler){ 
			
			try{  
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);

				$apiScheduler = new APIScheduler();

				$arrData = array();
				$arrData['code'] = 'xxxxx';
				$arrData['jobtype'] = 'woowa';
				$arrData['url'] = $url;
				$arrData['action'] = $action;
				$arrData['payload'] = json_encode($data);

				$apiScheduler->addData($arrData);

			    $this->oDbCon->endTrans();

			}catch (Exception $e){
				$this->oDbCon->rollback(); 
			}
			
		}else{
			$this->execute($url,$action, $data);
		}
			
	}
	
    function execute($url,$action='GET',$payload = ''){
		
		$payload['key'] = $this->secretkey;
			
        $payload = json_encode($payload); 
        
        //$this->setLog('====== payload =======',true);
        //$this->setLog($payload,true);
        
        $baseurl = 'http://116.203.191.58/api/';
        $url = $baseurl.$url;
         
		
        $header = array(
            'Content-Type: application/json', 
  			'Content-Length: ' . strlen($payload), 
        );

        $connection = curl_init(); 

        if ($action <> 'GET') 
            curl_setopt($connection, CURLOPT_POSTFIELDS, $payload);

        curl_setopt($connection, CURLOPT_URL, $url); 
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);  
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($connection, CURLOPT_HTTPHEADER,$header); 
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $action);
		curl_setopt($connection, CURLOPT_VERBOSE, 0);
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 0);
		curl_setopt($connection, CURLOPT_TIMEOUT, 360);

        $response = curl_exec($connection); 
        //$this->setLog($response,true);
        curl_close($connection);   
        return json_decode($response,true);

    }
     
    
}

?>