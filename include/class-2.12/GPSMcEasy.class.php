<?php
class GPSMcEasy extends GPSConnection{
 
   function __construct($provider){ 
	   
		parent::__construct();
	   
        $this->provider = $provider; // harus diisi 
        $this->baseURL = 'https://vsms-v2-public.mceasy.com/v1/'; // harus diisi 
        $this->baseURLAPI = 'https://api.mceasy.com/';
	   
        $this->username = GPS_CONNECTION[$provider]['username']; // harus diisi 
        $this->password = GPS_CONNECTION[$provider]['password']; // harus diisi 
        $this->apikey = GPS_CONNECTION[$provider]['apikey']; // harus diisi 
  	  
	    $this->opt['getAllVehicle'] = true;
	   
	}
	
	function setAPIKey(){
        
	   // deprecated
        return;
        
        $baseUrl = $this->baseURL; 
        $action = 'api-key?email='.$this->username.'&password='.$this->password;
        $url = $baseUrl . $action;
		
//		$this->setLog($url,true);
		
		$connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection);
        
        $result =  json_decode($response,true);
		
		$this->apikey = $result['data']['key'];
//			$this->setLog($this->apikey,true);
			
        curl_close($connection);
		
	}
	
    function getAccessToken(){ 
        return $this->apikey;  
    }
    
    function getGPSData($sn = array()){  
		  
		if(!is_array($sn)) 	$sn = array($sn);
         
		// sementara gpp
		// kalo mau ambil semua kosongin
		// kalo mau ambil 1, pake array 1 saja, siapa tau kedepan bisa ambil beberapa plat
        $action = ( empty($sn) )  ? 'vehicles/statuses' : 'vehicles/'.$sn[0].'/status' ;
		
        $url = $this->baseURL . $action;
  
//		$this->setLog(date('d-m-Y H:i:s') . ' => ' .$url,true);
		
        $header = array(
            'Authorization: Bearer ' . $this->getAccessToken()
        );


        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection); 
        $response = json_decode($response,true);

//		$this->setLog($response,true); 
//		$this->setLog('>>>>',true);
		
		
        $arrReturn = array();
        
        if($response['message'] == 'Success'){ 
			
			foreach($response['data'] as $row){
				$arrTemp = array();
			 
				$arrTemp['location'] = array();
				$arrTemp['location']['address'] = $row['latitude']. ', ' .$row['longitude']; // masih pake coordinate
				$arrTemp['location']['latitude'] = $row['latitude'];
				$arrTemp['location']['longitude'] = $row['longitude'];

				$arrTemp['policenumber'] = trim(str_replace(' ','',$row['licensePlate'])); // biar standart
				
				$arrTemp['speed'] = $row['speed'];

				array_push( $arrReturn,$arrTemp);
			}
			

        } 
        
        return $arrReturn;
    }
    
    function updateFleetStatus($id,$status){
         
        $url =  'https://api.mceasy.com/fleet-planning/api/web/v1/fleet-task-instant/'.$id.'/transition'; 
        
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->getAccessToken()
        );
         
        $payload = array(
                 'status' =>  $status 
                ); 

        $payload = json_encode($payload); 
 
        
        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($connection, CURLOPT_POST, 1); 
        curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection); 
         
        
        $response = json_decode($response,true);
        
    }
    
    function createFleet($workOrderKey, $arrLocation, $driverid, $carid){
        
        
        $url =  'https://api.mceasy.com/fleet-planning/api/web/v1/fleet-task-instant' ;
        $header = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->getAccessToken()
        );
        

        $arrPoint = array();
        for ($i=0;$i<count($arrLocation);$i++){ 
            array_push($arrPoint, array(
                                        'activity_label' => 'DROP',
                                        'type' => 'DEFAULT',
                                        'note' => $arrLocation[$i]['notes'],
                                        'address' => array(
                                                'type' => 'GOOGLE_MAP',
                                                'full_name' => $arrLocation[$i]['destinationname'],
                                                'lat' => floatval($arrLocation[$i]['latitude']),
                                                'lng' => floatval($arrLocation[$i]['longitude'])
                                            )
                                        ) 
                        ); 
        }
        
        $payload = array(
                 'driver_id' =>  floatval($driverid),
                 'points' =>  $arrPoint,
                 'vehicle_id' => floatval($carid)
                ); 
 
        $payload = json_encode($payload); 
         
        
//      $this->setLog($payload,true);
         
        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($connection, CURLOPT_POST, 1); 
        curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection); 
        $response = json_decode($response,true); 
        
        $requestId= (!empty($response['data']['id'])) ? $response['data']['id'] : 0;
        
        // update status
        $this->updateFleetStatus($response['data']['id'],'SCHEDULED');
        
        $this->updateWorkOrderRequestId($workOrderKey, $requestId);
         
    }
   function getFleetTaskInstancePoint($requestId) 
    {

        $action = 'fleet-planning/api/web/v1/fleet-task-instant/'.$requestId.'/point';
		
        $url = $this->baseURLAPI . $action;
		
        $header = array(
            'Authorization: Bearer ' . $this->getAccessToken(),
            'Accept: application/json'
        );


        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot

        $response = curl_exec($connection);
        $curlError = curl_error($connection);
        $httpCode = curl_getinfo($connection, CURLINFO_HTTP_CODE);
        
        curl_close($connection);

        $this->setLog($response,true);
        $response = json_decode($response,true);

        return $response;
    }

}
		
?>
