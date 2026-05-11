<?php
class GPSAccu extends GPSConnection{
 
   function __construct($provider){ 
	   
		parent::__construct();
	   
        $this->provider = $provider; // harus diisi 
        $this->baseURL = 'https://i.accugps.com/api/open/v1/'; // harus diisi 
	   
        $this->username = GPS_CONNECTION[$provider]['username']; // harus diisi 
        $this->password = GPS_CONNECTION[$provider]['password']; // harus diisi 
	    
	   
	    $this->opt['getAllVehicle'] = true;
	    $this->opt['trackerType'] = 2;
						   
	    // setup username dan password  
	    // load sekali aj kalo blm ada
//	    require_once DOC_ROOT. 'connections/_connection.php'; 
//	    if (isset(GPS_CONNECTION[$this->provider])){
//		    $this->username = GPS_CONNECTION[strtolower($this->provider)]['username'];
//		    $this->password = GPS_CONNECTION[strtolower($this->provider)]['password'];
//	    }
	     	   	 
	}
	
    function getAccessToken(){
 
        $baseUrl = $this->baseURL; 
        $action = 'login';
        $url = $baseUrl . $action;

        $payload = array(
                         'username' =>  $this->username,
                         'password' => $this->password
                        ); 

        $payload = json_encode($payload);

        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url); 
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0); 
        curl_setopt($connection, CURLOPT_POST, 1); 
        curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection);
        $result =  json_decode($response,true);
 
        curl_close($connection);
        
        $this->token = (is_array($result)) ? $result['data']['access_token'] : '';
        
        return strval($this->token);
    }
    
    function getGPSData($sn = array()){ 
	 
		
		if(!is_array($sn))
			$sn = array($sn);
         
         // karena Accu hanya bisa narik 1 mobil
        $action = (empty($sn)) ? 'trackers' : 'tracker/'.$sn[0].'/location';
        $url = $this->baseURL . $action;
  
        $header = array(
            'Content-Type: application/json',  
            'access_token: '. $this->getAccessToken() 
        );


        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection); 
//		
//		$this->setLog(date('d-m-Y H:i:s') . ' => response ' ,true);
//		$this->setLog($response,true);
		
        $response = json_decode($response,true);
        if(!is_array($response)) return array();  
		
        $arrReturn = array();
        
        if($response['status'] == 200){ 
			
			foreach($response['data'] as $row){
			
				$arrTemp = array();

				$arrTemp['location'] = array();
				$arrTemp['location']['address'] = $row['latitude']. ', ' . $row['longitude'];
				$arrTemp['location']['latitude'] = $row['latitude'];
				$arrTemp['location']['longitude'] = $row['longitude'];

				$arrTemp['policenumber'] = trim(str_replace(' ','',$row['alias'])); // biar standart 
				$arrTemp['gpstrackerid'] = $row['sn']; // biar standart
				
				$arrTemp['speed'] = $row['speed'];
				$arrTemp['angle'] = $row['degree'];

				array_push( $arrReturn,$arrTemp);

        	}
		}
		 
		 
        
        return $arrReturn;
    }
 

}
		
?>