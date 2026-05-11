<?php
class GPSSoloFleet extends GPSConnection{
 
   function __construct($provider){ 
	   
		parent::__construct();
	   
        $this->provider = $provider; // harus diisi 
        $this->baseURL = 'https://api.solofleet.com/'; // harus diisi 
	   
        $this->username = GPS_CONNECTION[$provider]['username']; // harus diisi 
        $this->password = GPS_CONNECTION[$provider]['password']; // harus diisi 
        $this->apikey = GPS_CONNECTION[$provider]['apikey']; // harus diisi 
  	  
	    $this->opt['getAllVehicle'] = true;
	   
	}
	
	function setAPIKey(){
	  
	}
	 
    
    function getGPSData($sn = array()){  
		 
		if(!is_array($sn)) 	$sn = array($sn);
          
        $action = 'vehiclelivequeryvehiclejson?username='.$this->username.'&password='.$this->password;
		
        $url = $this->baseURL . $action;
        
        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
//        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection); 
        $response = json_decode($response,true);

		//$this->setLog($response,true); 
		//$this->setLog('>>>>',true);
//		
//		
        $arrReturn = array();
         
			
        foreach($response as $row){
            
            $arrTemp = array();

            $arrTemp['location'] = array();
            $arrTemp['location']['address'] = $row['streetName']; // masih pake coordinate
            $arrTemp['location']['latitude'] = $row['latitude'];
            $arrTemp['location']['longitude'] = $row['longtitude'];

            $arrTemp['policenumber'] = trim(str_replace(' ','',$row['alias'])); // biar standart

            $arrTemp['speed'] = $row['speed'];

            array_push( $arrReturn,$arrTemp);
        }

 
        
        return $arrReturn;
    }
        
    function getDailyReport($sn,$param=array()){  
		  
        $currDate = date('d / m / Y');
        $startDate = (isset($param['startDate'])) ? $param['startDate'] : $currDate;
        $endDate = (isset($param['endDate'])) ? $param['endDate'] : $currDate;
            
        $startDate = str_replace('\'','',$this->oDbCon->paramDate($startDate,' / '));
        $dt = new DateTime($startDate); 
        $startDate = $dt->format(DateTime::ATOM); // 2025-12-30T00:00:00+07:00  (if server TZ is +07:00)
            
        $endDate = str_replace('\'','',$this->oDbCon->paramDate($endDate,' / '));
        $dt = new DateTime($endDate); 
        $endDate = $dt->format(DateTime::ATOM); // 2025-12-30T00:00:00+07:00  (if server TZ is +07:00)
 
        // harus per mobil  
        $action = 'jsondailyreport?username='.$this->username.'&password='.$this->password.'&gprsid='.$sn.'&filterStartDate='.$startDate.'&filterEndDate='.$endDate.'&type=json';
	  
        $url = $this->baseURL . $action;
        
        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url); 
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection); 
        $response = json_decode($response,true);

		//$this->setLog($response,true); 
		//$this->setLog('>>>>',true);
            
        return $response;
//		
//		
        //$arrReturn = array();
         
			
        //foreach($response as $row){
        //    
        //    $arrTemp = array();
//
        //    $arrTemp['location'] = array();
        //    $arrTemp['location']['address'] = $row['streetName']; // masih pake coordinate
        //    $arrTemp['location']['latitude'] = $row['latitude'];
        //    $arrTemp['location']['longitude'] = $row['longtitude'];
//
        //    $arrTemp['policenumber'] = trim(str_replace(' ','',$row['alias'])); // biar standart
//
        //    $arrTemp['speed'] = $row['speed'];
//
        //    array_push( $arrReturn,$arrTemp);
        //}

 
        
        //return $arrReturn;
    }
 

}
		
?>