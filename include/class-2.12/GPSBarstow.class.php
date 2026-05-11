<?php
class GPSBarstow extends GPSConnection{
 
   function __construct($provider){ 
	   
		parent::__construct();
	   
        $this->provider = $provider; // harus diisi 
        $this->baseURL = 'http://member.barstow-is.com/tramigo/api/trioeagle.php'; // harus diisi 
	   
        $this->username = GPS_CONNECTION[$provider]['username']; // harus diisi 
        $this->password = GPS_CONNECTION[$provider]['password']; // harus diisi 
 		   
	    $this->opt['getAllVehicle'] = true;
	    $this->opt['trackerType'] = 1;
 	 
	}
	
    function getAccessToken(){  
    }
    
    function getGPSData($sn = array()){ 
	  
		if(!is_array($sn))
			$sn = array($sn);
         
		//$key = mktime();
		$key = time();
		
         // karena Accu hanya bisa narik 1 mobil
        $action = '?key='.$key.'&password='.$this->password.'&ceksum='.md5("**".$key."^^". $this->password);
		if(!empty($sn)) $action .= '&nopol='.str_replace(' ','',$sn);
		 
        $url = $this->baseURL . $action;
  
//		$this->setLog($url,true);
		
        $header = array();

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
		
		foreach($response['data'] as $row){

			$arrTemp = array();
			
			$arrLoc = explode(',',$row['coord']);
			$arrTemp['location'] = array();
			$arrTemp['location']['address'] = $row['posisi'];
			$arrTemp['location']['latitude'] = $arrLoc[1];
			$arrTemp['location']['longitude'] = $arrLoc[0];

			$arrTemp['policenumber'] = trim(str_replace(' ','',$row['nopol'])); // biar standart 
			$arrTemp['gpstrackerid'] = $arrTemp['policenumber']; // biar standart 

			$arrTemp['speed'] = $row['speed'];

			array_push( $arrReturn,$arrTemp);

		}
	  
        return $arrReturn;
    }
 

}
		
?>