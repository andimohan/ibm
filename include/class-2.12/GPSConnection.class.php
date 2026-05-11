<?php
class GPSConnection extends BaseClass{
 
   function __construct($odbc = ''){ 
	   
		parent::__construct();
  	    
        $this->includeClassDependencies(array( 
            'TruckingServiceWorkOrder.class.php',
            'GPS.class.php',
			'Car.class.php'
        ));
  
	   
	    // tracker type : 1 => vehicle number, 2 => tracker id, 3 => code
	    $this->opt = array('getAllVehicle' => false,
						    'connectionTimeOut' => 3,
						    'trackerType'=> 1
						  );
	   
	    if (!empty($odbc)) $this->oDbCon = $odbc;	// utk connect ke databaes subdomain 
		
	    $this->updateCredential(false);
        $this->overwriteConfig();
        
	}
	
    
    function getActiveGPS(){
        $gps = new GPS(); 
        $gps->oDbCon = $this->oDbCon; // utk compatible dengan frontend
        
        $rsGPS = $gps->searchDataRow(array($gps->tableName.'.pkey', 'lower('.$gps->tableName.'.code'.') as code',$gps->tableName.'.username',$gps->tableName.'.password',$gps->tableName.'.apikey'),
										 ' and ' . $gps->tableName.'.statuskey = 1 '
									);
        
        return $rsGPS;
        
    }
    
	function updateCredential($forceReset=false){
		
		// update semua aj, harusnya gk byk
		
		
		if (!defined('GPS_CONNECTION')){
//			$gps = new GPS(); 
//		    $gps->oDbCon = $this->oDbCon; // utk compatible dengan frontend
//
//			$rsGPS = $gps->searchDataRow(array($gps->tableName.'.pkey', 'lower('.$gps->tableName.'.code'.') as code',$gps->tableName.'.username',$gps->tableName.'.password',$gps->tableName.'.apikey'),
//										 ' and ' . $gps->tableName.'.statuskey = 1 '
//									);
            $rsGPS = $this->getActiveGPS();
			$rsGPS = array_column($rsGPS,null,'code');
			 
			define('GPS_CONNECTION',$rsGPS);
		}

	}
  
 
	function getGPSObj($provider){
		// perlu karena setiap API GPS endpoint dan responseny berbeda2
		
		switch($provider){
			case 'accugps' :  	require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSAccu.class.php';  
								return new GPSAccu($provider);
								break;
			case 'inovatrack' :  	require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSInova.class.php';  
								return new GPSInova($provider);
								break;
			case 'mceasy' :  	require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSMcEasy.class.php';  
								return new GPSMcEasy($provider);
								break;
			case 'easygo' :  	require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSEasyGo.class.php';  
								return new GPSEasyGo($provider);
								break;
			case 'gpsid' :  	require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSid.class.php';  
								return new GPSid($provider);
								break;
			case 'barstow' :  	require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSBarstow.class.php';  
								return new GPSBarstow($provider);
								break;
			case 'solofleet' :  	require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GPSSoloFleet.class.php';  
								return new GPSSoloFleet($provider);
								break;
		}
		
		return null;
		
	}
	
    function getAccessToken(){}

    // function createDO(){}

	function createFleet($workOrderKey, $arrLocation, $driverid, $carid){}
      
    function updateWorkOrderRequestId($workOrderKey, $requestId){
        // update ulang request id ke SPK agar tau mana yg sdh ke plot
        
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        
        $truckingServiceWorkOrder->updateGPSRequestId($workOrderKey, $requestId);
        
    }
    
	function getGPSData($sn = array()){ 
		return array();	
	}
	
    function getData($param = array()){

//        $pkey = (isset($param['pkey'])) ? $param['pkey']:  array();
        $registrationNumber = (isset($param['registrationNumber'])) ? $param['registrationNumber']: array();
        $gpsProviderKey = (isset($param['gpsProviderKey'])) ? $param['gpsProviderKey']: array();
        $warehousekey = (isset($param['warehousekey'])) ? $param['warehousekey']: array();
 
//		if($this->isJson($pkey))
//			$pkey = json_decode($pkey);
        
		if($this->isJson($registrationNumber))
			$registrationNumber = json_decode($registrationNumber);
		
//		if($this->isJson($gpsProviderKey))
//			$gpsProviderKey = json_decode($gpsProviderKey);

//		if($this->isJson($warehousekey))
//			$warehousekey = json_decode($warehousekey);


		 
		// sekalian buat agar bisa multiple vehicle
//		$arrData = array();
		
		// tentuin ambil dr obj / provider mana
		
		$car = new Car();
		$car->oDbCon = $this->oDbCon; // utk compatible dengan frontend
		 
		// pisah plat per vendor GPS
		$rsGPS = $car->getGPSInformation($param); 
		$rsCar = $rsGPS;
        
		// init 
		foreach($rsCar as $carkey=>$carRow){ 
			// standariE plat no gk ad spasi
			$rsCar[$carkey]['policenumber'] = str_replace(' ', '', $rsCar[$carkey]['policenumber']);
			$rsCar[$carkey]['gpsdata'] = array('location' => array('latitude' => 0, 'longitude' => 0)  , 'speed' => 0); // biar standart 
		}
			
		$rsGPS = $this->reindexDetailCollections($rsGPS,'providercode');
		 
		// loop utk setiap provider
        
		foreach($rsGPS as $key=>$GPSRow){
			$provider = strtolower($key);
			   
            $gpsObj = $this->getGPSObj($provider); 
        
			
			// tentuin pake registration number atau tracker id  
//			$arrProviderUseRegistrationNumber = array('inovatrack');
			
			$trackerType = 'policenumber';
			switch($gpsObj->opt['trackerType']){
				case '1' : $trackerType = 'policenumber'; break;
				case '2' : $trackerType = 'gpstrackerid'; break;
//				case '3' : $trackerType = 'code'; break;
				default :$trackerType = 'policenumber'; break;
			}
			 
			$trackerId = array_column($GPSRow, $trackerType); 
			  
			// kalo tipenya bisa narik semua, dihilangkan saja parameter nya kalo arraynya lebih dari 1 (asumsi ambil beberapa mobil)
			if($gpsObj->opt['getAllVehicle'] == true) // gk bisa pake  && count($trackerId) <> 1, mending tarik semua saja sementara, karena accu pakenya SN
				$trackerId = array();
            
			$rsGpsData = $gpsObj->getGPSData($trackerId);
            
			$rsGpsData = array_column($rsGpsData,null,$trackerType);
			 
			
			foreach($rsCar as $carkey=>$carRow){  
				foreach($rsGpsData as $gpsDataRow){

					if ($carRow[$trackerType] == $gpsDataRow[$trackerType]){
						$rsCar[$carkey]['gpsdata']['providername']  = $GPSRow[0]['providername'];
						$rsCar[$carkey]['gpsdata']['location']  = $gpsDataRow['location'];
						$rsCar[$carkey]['gpsdata']['speed']  = $gpsDataRow['speed'];
						break;
					}
				}
			} 
			 
			 
		}
		 
		$rsCar = array_column($rsCar,null,'pkey');
		
		return $rsCar;
		 
		
	}
	
	
	function getGPSGeofenceData($startDate, $endDate, $arrGeofenceId = array()){
		return array();	
	}
	
    function getGeofenceData($startDate, $endDate, $arrGeofenceId = array()){
		      
//	 	$gps = new GPS(); 
//		$rsGPS = $gps->searchDataRow(array($gps->tableName.'.pkey', $gps->tableName.'.code'), ' and '.$gps->tableName.'.statuskey = 1');
		$rsGPS = $this->getActiveGPS();
        $rsGPS = array_column($rsGPS,null,'code');
		
		// init
		$arrReturn = array(); 
		foreach($arrGeofenceId as $row){
			$arrReturn[$row] = array();
		}
		
		// loop utk setiap provider
		foreach($rsGPS as $key=>$GPSRow){
			$provider = strtolower($key);
			   
			$gpsObj = $this->getGPSObj($provider);
			  
			$rsGpsData = $gpsObj->getGPSGeofenceData($startDate, $endDate, $arrGeofenceId);
			  
			// merge berdasarkan id geofence
			foreach($rsGpsData as $geoCode=>$geoRow){ 
				$arrReturn[$geoCode] = array_merge($arrReturn[$geoCode], $geoRow);
			}
			  
			 
		}
		   
		return $arrReturn;
		 
		
	}
    
    	
	function getGPSParkingData($arrParam = array()){
		return array();	
	}
	
    function getParkingData($arrParam = array()){
        	      
        //	 	$gps = new GPS(); 
        //		$rsGPS = $gps->searchDataRow(array($gps->tableName.'.pkey', $gps->tableName.'.code'), ' and '.$gps->tableName.'.statuskey = 1');
        $rsGPS = $this->getActiveGPS();
        $rsGPS = array_column($rsGPS,null,'code');

		// init
		$arrReturn = array(); 
	  
		// loop utk setiap provider
		foreach($rsGPS as $key=>$GPSRow){
			$provider = strtolower($key);
			   
			$gpsObj = $this->getGPSObj($provider);
			  
			$rsGpsData = $gpsObj->getGPSParkingData($arrParam);
//            $this->setLog($rsGpsData,true);
         
            // nanti perlu dicek kalo ad 1 kendaraan pake 2 gps atau lebih
            $arrReturn = array_merge($arrReturn, $rsGpsData);
			  
			 
		}
        
        // biar index nya jadi rapi 
        $arrReturn = array_values($arrReturn); 
		   
        // gk boleh direindex berdasarkan nopol, kalo geofencenya berulang misalnya POOL, MUARA BARU, POOL
        // maka pool nya ketimpa yg pertama ??
        
        $arrReturn = $this->reindexDetailCollections($arrReturn,'policenumber');
		return $arrReturn;
    }
	
	
	function getGPSMileageData($arrParam = array()){
		return array();	
	}
	
	//function initGPSMileageData($arrParam = array()){
	//	return array();	
	//}
	//
 //   function initMileageData($arrParam = array()){
 //       
	//	$car = new Car();
	//	//$this->setLog($arrParam,true);
	//	
	//	$arrRegistrationNumber = $arrParam['registrationNumber'];
	//	if (!is_array($arrRegistrationNumber))
	//		$arrRegistrationNumber = array($arrRegistrationNumber);
	//	
	//	// pisah plat per vendor GPS 
	//	$rsGPS = $car->getGPSInformation(array('registrationNumber' => $arrRegistrationNumber));  
 //       $rsGPS = array_column($rsGPS,null,'providercode');
	// 	
	//	// init
	//	$arrReturn = array(); 
	//  
	//	// loop utk setiap provider
	//	
	//	//$this->setLog($rsGPS,true);
	//	
	//	foreach($rsGPS as $key=>$GPSRow){
	//		$provider = strtolower($key);
	//		   
	//		$gpsObj = $this->getGPSObj($provider);
	//		  
	//		$rsGpsData = $gpsObj->initGPSMileageData($arrParam); 
 //        
 //           // nanti perlu dicek kalo ad 1 kendaraan pake 2 gps atau lebih
 //           $arrReturn = array_merge($arrReturn, $rsGpsData); 
	//	}
	//	
	//	
	//	
	//	//$this->setLog('gps',true);
	//	//$this->setLog($arrReturn,true);
 //       
 //       // biar index nya jadi rapi 
 //       $arrReturn = array_values($arrReturn);
	//	
 //       // gk boleh direindex berdasarkan nopol, kalo geofencenya berulang misalnya POOL, MUARA BARU, POOL
 //       // maka pool nya ketimpa yg pertama ??
 //       
 //       $arrReturn = $this->reindexDetailCollections($arrReturn,'policenumber');
	//	return $arrReturn;
 //   }
	
	
    function getMileageData($arrParam = array()){
        
		$car = new Car();
		//$this->setLog($arrParam,true);
		
		$arrRegistrationNumber = $arrParam['registrationNumber'];
		if (!is_array($arrRegistrationNumber))
			$arrRegistrationNumber = array($arrRegistrationNumber);
		
		// pisah plat per vendor GPS 
		$rsGPS = $car->getGPSInformation(array('registrationNumber' => $arrRegistrationNumber));  
        $rsGPS = array_column($rsGPS,null,'providercode');
	 	
		// init
		$arrReturn = array(); 
	  
		// loop utk setiap provider
		
		//$this->setLog($rsGPS,true);
		
		foreach($rsGPS as $key=>$GPSRow){
			$provider = strtolower($key);
			   
			$gpsObj = $this->getGPSObj($provider);
			  
			$rsGpsData = $gpsObj->getGPSMileageData($arrParam); 
         
            // nanti perlu dicek kalo ad 1 kendaraan pake 2 gps atau lebih
            $arrReturn = array_merge($arrReturn, $rsGpsData); 
		}
		
		
		
		//$this->setLog('gps',true);
		//$this->setLog($arrReturn,true);
        
        // biar index nya jadi rapi 
        $arrReturn = array_values($arrReturn);
		
        // gk boleh direindex berdasarkan nopol, kalo geofencenya berulang misalnya POOL, MUARA BARU, POOL
        // maka pool nya ketimpa yg pertama ??
        
        $arrReturn = $this->reindexDetailCollections($arrReturn,'policenumber');
		return $arrReturn;
    }

}
		
?>
