<?php
class GPSEasyGo extends GPSConnection{
 
   function __construct($provider){ 
	   
		parent::__construct();
	   
        $this->provider = $provider; // harus diisi 
        $this->baseURL = 'https://vtsapi.easygo-gps.co.id/api/'; // harus diisi 
	   
        $this->username = GPS_CONNECTION[$provider]['username']; // harus diisi 
        $this->password = GPS_CONNECTION[$provider]['password']; // harus diisi 
        $this->apikey = GPS_CONNECTION[$provider]['apikey']; // harus diisi 
    
	    $this->opt['getAllVehicle'] = true; 
	   
	}
	 
    
    function getGPSData($sn = array()){  
		 
		if(!is_array($sn)) 	$sn = array($sn);
         
		// sementara gpp
		// kalo mau ambil semua kosongin
		// kalo mau ambil 1, pake array 1 saja, siapa tau kedepan bisa ambil beberapa plat
		
		// blm tau endpoint per mobil, jg sset kosong dulu semetnara
		$sn = array();
		 
        $action = ( empty($sn) )  ? 'Report/lastposition' : 'Report/lastposition' ;
		    
        $url = $this->baseURL . $action;
  
//		$this->setLog(date('d-m-Y H:i:s') . ' => ' .$url,true);
		$header = array(
			'Content-Type: application/json', 
            'token: ' .  $this->apikey
        );


		$payload = array(
				'list_nopol' => 'null', 
				'list_no_aset' => 'null', 
				'status_vehicle' =>'null', 
				'geo_code' =>'null', 
				'min_lastupdate_hour' => 'null', 
				'encrypted' => 0,
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

//		$this->setLog($response,true); 
//		$this->setLog('>>>>',true);
		
		
        $arrReturn = array();
        
        if($response['ResponseCode'] == 1){ 
			
			foreach($response['Data'] as $row){
				$arrTemp = array();
			 
				$arrTemp['location'] = array();
				$arrTemp['location']['address'] = $row['addr']; // masih pake coordinate
				$arrTemp['location']['latitude'] = $row['lat'];
				$arrTemp['location']['longitude'] = $row['lon'];

				$arrTemp['policenumber'] = trim(str_replace(' ','',$row['nopol'])); // biar standart
				$arrTemp['gpstrackerid'] = $arrTemp['policenumber']; // biar standart
				
				$arrTemp['speed'] = $row['speed'];
				$arrTemp['angle'] = $row['direction'];
				

				array_push( $arrReturn,$arrTemp);
			}
			

        } 
//        $this->setLog($arrReturn,true); 
        return $arrReturn;
    }
	
	function getGPSGeofenceData($startDate, $endDate, $arrGeofenceId = array()){
		  
		$action = 'Report/geo_location_report';
		    
        $url = $this->baseURL . $action;
   
		$header = array(
			'Content-Type: application/json', 
            'token: ' .  $this->apikey
        );


		$payload = array(
				'start_time' => str_replace('\'','',$this->oDbCon->paramDate($startDate,' / ')),
				'stop_time' => str_replace('\'','',$this->oDbCon->paramDate($endDate,' / ','Y-m-d 23:59')), 
			 	'geo_code' => $arrGeofenceId,
				'encrypted' => 0,
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
		
//		$this->setLog($response,true);
		
		if($response['ResponseCode'] <> 1) return array();
		
		$arrReturn = array();
		
		foreach($response['Data'] as $row){
				  
				$arrTemp['geoid'] = $row['geo_code'];
				$arrTemp['geoname'] = $row['geo_nm']; 
				$arrTemp['policenumber'] = strtoupper(trim(str_replace(' ','',$row['nopol']))); // biar standart
				$arrTemp['starttime'] = $row['start_time'];
				$arrTemp['stoptime'] = $row['stop_time'];
				$arrTemp['parkingduration'] = $row['duration']['value']; // in seconds
			
				array_push( $arrReturn,$arrTemp);
			}
		
		
		// reindex pake geocode 
		$arrReturn = $this->reindexDetailCollections($arrReturn,'geoid');
	
		return $arrReturn;
	}
 
    function getGPSParkingData($arrParam = array(), $getPrevDay = true){
		  
		// ambil hari sebelumya utk sambungin kalo ad yg nginep 
		$rsPrevDay = array();
		if($getPrevDay){  
				// Create DateTime object from string
				$date = DateTime::createFromFormat('d / m / Y', $arrParam['startDate']);

				// Subtract 1 day
				$date->modify('-1 day');

				// Output the previous day in d/m/Y format
				$prevParam = $arrParam;
				$prevParam['startDate'] = $date->format('d / m / Y');
				$prevParam['endDate'] = $prevParam['startDate'];

				$rsPrevDay = $this->getGPSParkingData($prevParam,false);
		}
			
        $car = new Car();
        $rsCar = $car->searchDataRow(array($car->tableName.'.pkey',$car->tableName.'.code',$car->tableName.'.policenumber'));
        
        foreach($rsCar as $key=>$row){
            $rsCar[$key]['policenumber'] = strtoupper(trim(str_replace(' ','',$row['policenumber'])));
        }
        
        $rsCar = array_column($rsCar,null,'policenumber');
            
		$action = 'Report/parking';
		    
        $url = $this->baseURL . $action;
   
		$header = array(
			'Content-Type: application/json', 
            'token: ' .  $this->apikey
        );

        
        $startDate = (isset($arrParam['startDate']) && !empty($arrParam['startDate'])) ? $arrParam['startDate'] : date('d / m / Y');
        $endDate = (isset($arrParam['endDate']) && !empty($arrParam['endDate'])) ? $arrParam['endDate'] : date('d / m / Y');
        $arrVehicleRegistrationNumber = (isset($arrParam['vehicleRegistrationNumber']) && !empty($arrParam['vehicleRegistrationNumber'])) ? $arrParam['vehicleRegistrationNumber'] : array();
            
        
		$payload = array(
				'start_time' => str_replace('\'','',$this->oDbCon->paramDate($startDate,' / ')),
				'stop_time' => str_replace('\'','',$this->oDbCon->paramDate($endDate,' / ','Y-m-d 23:59')),
				'encrypted' => 0,
		); 
        
		//$arrVehicleRegistrationNumber = array("B 9426 PEH");
		//$arrVehicleRegistrationNumber = array("B 9093 BEH"); 
        if(!empty($arrVehicleRegistrationNumber)) 
            $payload['lstNoPOL'] = $arrVehicleRegistrationNumber;
		 
		 
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
		
		if($response['ResponseCode'] <> 1) return array();
		
		$arrReturn = array();
		
        // loop utk setiap nopol, kalo pernah keluar dari geocode (asumsi response yg diberikan berurut waktunya, maka perhitungan direset)
        $currIndex = '';
        $ctrForResetGeofence = 0; // agar kalo ketemu geofence yg sama setelah diseling geofence lain, tetep kepisah
        
        
		foreach($response['Data'] as $row){
            
                $policeNumber = strtoupper(trim(str_replace(' ','',$row['nopol']))); // biar standart
                $geofenceName = strtolower(trim(str_replace(' ','',$row['parking']['geo_location_code'])));
            
                $index = $policeNumber.'-'.$geofenceName.'-'.$ctrForResetGeofence;
            
                if($currIndex == '' || $currIndex <> $index){
                    
                    // kalo blm ada
                    if(!isset($arrReturn[$index])){ 
                        $arrReturn[$index] = array();
                    }else{
                        $ctrForResetGeofence++;
                        $index = $policeNumber.'-'.$geofenceName.'-'.$ctrForResetGeofence;
                        $arrReturn[$index] = array();
                    }
                        
                        
                    $arrReturn[$index]['index'] = $index; 
                    $arrReturn[$index]['vehiclegeocodeindex'] = $policeNumber.'-'.$geofenceName; // dipisah, utk sambungin ke hari sebelumnya
                    $arrReturn[$index]['vehiclecode'] = $rsCar[$policeNumber]['code']; 
                    $arrReturn[$index]['policenumber'] = $policeNumber; 
                    $arrReturn[$index]['geoname'] = $row['parking']['geo_location_nm'];  
                    $arrReturn[$index]['geoid'] = $row['parking']['geo_location_id'];  
                    $arrReturn[$index]['parkingduration'] = $row['duration']['value']; // in seconds
                     
                    $currIndex = $index;
                    continue;
                }
             
                $arrReturn[$index]['parkingduration'] += $row['duration']['value']; // in seconds 
			 
		}
		
		// harusnya hanya jalan di current day
		if($getPrevDay){
			//convert reiindex agar tau posisi terakhir di hari sebelumnya harus sama dengan posisi pertama di hari ini
			$rsPrevReindex = $this->reindexDetailCollections(array_values($rsPrevDay),'policenumber');
			$rsCurrDay = $this->reindexDetailCollections(array_values($arrReturn),'policenumber');
			$currCar = array_column($arrReturn,'policenumber');

			//$this->setLog('PREV DAY >>>>>>>>>>>>>>>>',true);
			//$this->setLog($rsPrevReindex,true);
			//$this->setLog('<<<<<<<<<<<<<< PREV DAY',true);
			//$this->setLog('CURR DAY >>>>>>>>>>>>>>>>',true);
			//$this->setLog($rsCurrDay,true);
			//$this->setLog('<<<<<<<<<<<<<< CURR DAY',true);

			if (!empty($rsPrevDay)){
					$arrStichInformation = array();
					foreach($currCar as $registrationNumber){ 
						$prevDaylastIndex = count($rsPrevReindex[$registrationNumber]);
						$prevDaylastIndex--;
						
						if($rsPrevReindex[$registrationNumber][$prevDaylastIndex]['geoname'] == $rsCurrDay[$registrationNumber][0]['geoname']){
							// nyambung dari hari sebelumnya
							array_push($arrStichInformation,$rsPrevReindex[$registrationNumber][$prevDaylastIndex]);	
						}
					}

				$arrStichInformation = array_column($arrStichInformation, null,'policenumber' );
				
				foreach($arrReturn as $index=>$row){
					$arrReturn[$index]['parkingdurationfromprevday'] = $arrReturn[$index]['parkingduration'];
					
					// hanya boleh menambahkan jika index pertama saja
					if(isset($arrStichInformation[$row['policenumber']])){
							$arrReturn[$index]['parkingdurationfromprevday'] += $arrStichInformation[$row['policenumber']]['parkingduration'];
							unset($arrStichInformation[$row['policenumber']]);// di nol kan agar tdk menjumlahkan ke index yg lain dr nopol yg sama
						//$this->setLog($arrReturn[$index],true);
					}
				}
			}
		}
		
		
        //$this->setLog($this->reindexDetailCollections(array_values($arrReturn),'policenumber'),true);
		return $arrReturn;
	}
 
	
	function getGPSMileageData($arrParam=array()){
			 
		$action = 'Report/total_km';
		    
        $url = $this->baseURL . $action;
   
		$header = array(
			'Content-Type: application/json', 
            'token: ' .  $this->apikey
        );
		
		$startDate = (isset($arrParam['startDate']) && !empty($arrParam['startDate'])) ? $arrParam['startDate'] : date('01 / 01 / 2010');
		$endDate = (isset($arrParam['endDate']) && !empty($arrParam['endDate'])) ? $arrParam['endDate'] : date('d / m / Y');

		$arrRegistrationNumber = $arrParam['registrationNumber'];
		if(!is_array($arrRegistrationNumber)) $arrRegistrationNumber = array($arrRegistrationNumber);
		
		$payload = array(
				'start_time' => str_replace('\'','',$this->oDbCon->paramDate($startDate,' / ')),
				'stop_time' => str_replace('\'','',$this->oDbCon->paramDate($endDate,' / ','Y-m-d 23:59')),
				'lstNoPOL' => array('B 9449 UIV') ,// $arrRegistrationNumber,
				'encrypted' => 0 
		); 
          
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
		
		$this->setLog($header,true);
		$this->setLog($url,true);
		$this->setLog($payload,true);
		$this->setLog($response,true);
		
        $response = json_decode($response,true);
		 
		
		$arrReturn = array();
		if(isset($response['Data']) && !empty($response['Data'])){
			$row = $response['Data'];
			
			foreach($row as $dataRow){ 
				$policeNumber = strtoupper(trim(str_replace(' ','',$dataRow['car_plate']))); // biar standart 
				array_push($arrReturn, array('policenumber' => $policeNumber, 'mileage' => $dataRow["total_km"]) );
			}
		}
		  
		return $arrReturn;
		
	}
	
	//function getGPSMileageData($arrParam = array()){
	//	
	//	
	//	$action = 'Report/historydata';
	//	    
 //       $url = $this->baseURL . $action;
 //  
	//	$header = array(
	//		'Content-Type: application/json', 
 //           'token: ' .  $this->apikey
 //       );
	//	
	//	$startDate = (isset($arrParam['startDate']) && !empty($arrParam['startDate'])) ? $arrParam['startDate'] : date('d / m / Y');
	//	$endDate = (isset($arrParam['endDate']) && !empty($arrParam['endDate'])) ? $arrParam['endDate'] : date('d / m / Y');
//
	//	$arrRegistrationNumber = $arrParam['registrationNumber'];
	//	if(!is_array($arrRegistrationNumber)) $arrRegistrationNumber = array($arrRegistrationNumber);
	//	
	//	$payload = array(
	//			'start_time' => str_replace('\'','',$this->oDbCon->paramDate($startDate,' / ')),
	//			'stop_time' => str_replace('\'','',$this->oDbCon->paramDate($endDate,' / ','Y-m-d 23:59')),
	//			'lstNoPOL' => $arrRegistrationNumber,
	//	); 
 //        
	//  	
 //       $connection = curl_init(); 
 //       curl_setopt($connection, CURLOPT_URL, $url);
 //       curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
 //       curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
 //       curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
 //       curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
 //       curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
 //   	curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "POST");
	//	curl_setopt($connection, CURLOPT_CONNECTTIMEOUT, $this->opt['connectionTimeOut']); // 3s timeout, biar proses lain gk lemot
//
 //       $response = curl_exec($connection);
	//	
	//	//$this->setLog($payload,true);
	//	//$this->setLog($response,true);
	//	
 //       $response = json_decode($response,true);
	//	 
	//	
	//	$arrReturn = array();
	//	if(isset($response['Data']) && !empty($response['Data'])){
	//		$row = $response['Data'];
	//		
	//		foreach($row as $dataRow){ 
	//			$policeNumber = strtoupper(trim(str_replace(' ','',$dataRow['no_pol']))); // biar standart 
	//			array_push($arrReturn, array('policenumber' => $policeNumber, 'mileage' => $dataRow["odometer"], 'trdate' => $dataRow['gps_time'] ) );
	//		}
	//	}
	//	  
	//	return $arrReturn;
	//}
 

}
		
?>