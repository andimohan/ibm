<?php
class GPSid extends GPSConnection{
 
   function __construct($provider){ 
	   
		parent::__construct();
	   
        $this->provider = $provider; // harus diisi 
        $this->baseURL = 'https://portal.gps.id/backend/seen/public/'; // harus diisi 
	   
        $this->username = GPS_CONNECTION[$provider]['username']; // harus diisi 
        $this->password = GPS_CONNECTION[$provider]['password']; // harus diisi 
        $this->apikey = GPS_CONNECTION[$provider]['apikey']; // harus diisi 
    
	    $this->opt['getAllVehicle'] = true;
	    $this->opt['trackerType'] = 1;
	   
	}
	 
	function getAccessToken(){
		 
        $action = 'login';
        $url = $this->baseURL . $action;
		
		$header = array(
			'Content-Type: application/json', 
            'accept' => 'application/json',
        );
		
		$payload = array(
			'username' => $this->username, 
			'password' => $this->password,  
		);
		
			 
		$payload = json_encode($payload); 
	
        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
    	curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection); 
        $response = json_decode($response,true);
		  
		return $response['message']['data']['token'];
	}
    
    function getGPSData($sn = array()){  
		 
		$action = ( empty($sn) )  ? 'vehicle' : 'vehicle'; // 'vehicle/detail/imei' ; // nanti baru update by IMEI
		    
        $url = $this->baseURL . $action;
		
		$header = array(
			'Authorization: ' . $this->getAccessToken(), 
            'accept' => 'application/json',
        );
		
		$connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1); 
    	curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "GET");
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection); 
        $response = json_decode($response,true);
		
		$carList = array_column($response['message']['data'],'plate');
//		$this->setLog(implode(chr(13),$carList),true);
//		$this->setLog($response['message']['data'],true);
		
		$arrReturn = array();
		foreach($response['message']['data'] as $row){
			$arrTemp = array();

			$arrTemp['location'] = array();
			$arrTemp['location']['address'] = $row['latitude'].', '. $row['longitude']; // masih pake coordinate
			$arrTemp['location']['latitude'] = $row['latitude'];
			$arrTemp['location']['longitude'] = $row['longitude'];

			$arrTemp['policenumber'] = trim(str_replace(' ','',$row['plate'])); // biar standart

			$arrTemp['speed'] = $row['speed'];

			array_push( $arrReturn,$arrTemp);
		} 

		 return $arrReturn;
    }
 

}
		
?>