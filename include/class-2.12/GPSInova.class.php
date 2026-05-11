<?php
class GPSInova extends GPSConnection{
 
   function __construct($provider){ 
	   
		parent::__construct();
	   
	   //	   https://api.inovatrack.com/v1/api/data/GetVehicles?memberCode=tangguh&password=XZmSAepx2QycgRLA&vehicles=B9528UIW|B9372UEL
	   
        $this->provider = $provider; // harus diisi 
        $this->baseURL = 'https://api.inovatrack.com/v1/api/data/'; // harus diisi 
	   
        $this->username = GPS_CONNECTION[$provider]['username']; // harus diisi 
        $this->password = GPS_CONNECTION[$provider]['password']; // harus diisi 
	    
	    // setup username dan password  
	    // load sekali aj kalo blm ada
//	    require_once DOC_ROOT. 'connections/_connection.php'; 
//	    if (isset(GPS_CONNECTION[$this->provider])){
//		    $this->username = GPS_CONNECTION[strtolower($this->provider)]['username'];
//		    $this->password = GPS_CONNECTION[strtolower($this->provider)]['password'];
//	    }
	    
	    $this->opt['getAllVehicle'] = true; 
	     
	}
	
    function getAccessToken(){
 
       
    }
    
    function getGPSData($registrationNumber = array()){ 
	 
		if(!is_array($registrationNumber))
			$registrationNumber = array($registrationNumber);
         
         // karena Accu hanya bisa narik 1 mobil
		$registrationNumber = implode ('|',$registrationNumber);
		
		$vehicleParam = (!empty($registrationNumber)) ? '&vehicles=' . str_replace(' ','',$registrationNumber) : '';
        $action = 'GetVehicles?memberCode='.$this->username.'&password='.$this->password.$vehicleParam;
        $url = $this->baseURL . $action;
  
//		$this->setLog(date('d-m-Y H:i:s') . ' => ' .$url,true);
		
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
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, 3); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection); 
        $response = json_decode($response,true);
          
//		$this->setLog(date('d-m-Y H:i:s') . ' => response ' ,true);
//		$this->setLog($response,true);
//		
        $arrReturn = array();
          
		$arrTemp = array();

		foreach($response as $row){
			$arrTemp['location'] = array();
			//standarize, buat jaga2 kalo ad GPS lain 
			$arrTemp['location']['address'] = $row['Location'];
			$arrTemp['location']['latitude'] = $row['Latitude'];
			$arrTemp['location']['longitude'] = $row['Longitude'];
			$arrTemp['policenumber'] = trim(str_replace(' ','',$row['VehicleNumber'])); // biar standart
			$arrTemp['trackerid'] = trim(str_replace(' ','',$row['VehicleId'])); // biar standart
			$arrTemp['speed'] = $row['Speed'];

			array_push( $arrReturn,$arrTemp); 
		} 
        
        return $arrReturn;
    }
 

}
		
?>