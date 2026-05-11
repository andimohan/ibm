<?php
class GPSETI{
 
   function __construct(){ 
       
		$this->securityObject = 'TruckingServiceWorkOrder';  
       
        $this->token = '';
           
	}
	
    function auth(){
        //$this->setLog("auth",true);
        /*$apiInformation = $this->getAPIInformation();
        
        $baseUrl = $apiInformation['baseUrl']; 
        $action = 'login';
        $url = $baseUrl . $action;

        $payload = array(
                         'username' => $apiInformation['username'],
                         'password' => $apiInformation['password'] 
                        ); 

        $payload = json_encode($payload);

        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url); 
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0); 
        curl_setopt($connection, CURLOPT_POST, 1); 
        curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($connection);
        $result =  json_decode($response,true);

        curl_close($connection);
        
        $this->token = $result['data']['access_token'];
        
        return strval($result['data']['access_token']);*/
    }
    
    function getAuthToken(){
       return 123456;
    }
    
    function getData($policenumber){
  
        $apiInformation = $this->getAPIInformation();
        
        $baseUrl = $apiInformation['baseUrl']; 
        $password = $apiInformation['password']; 
        $key = $this->getAuthToken();
        $checksum = md5('**'.$key.'^^'.$password);
        
        $action = '';
        $url = $baseUrl . $action;
 
        $url .= '?key='.$key.'&password='.$password.'&ceksum='.$checksum.'&nopol='.$policenumber;
            
        $header = array(
            'Content-Type: application/json',  
            //'access_token: '. $this->getAuthToken() 
        );
 
        
        //$this->setLog($url,true);
        
        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($connection, CURLOPT_POST, false);

        $response = curl_exec($connection); 
        $response = json_decode($response,true);
          
         /*(
                    [status] => Parkir
                    [coord] => 106.77371,-6.10706
                    [posisi] => AREA POS POLISI MUARA ANGKE,JL PENDARATAN UDANG,PLUIT,14450,JAKARTA UTARA,
                    [time] => 2020-05-11 11:24:09
                    [nopol] => B 9250 PEH
                )
        */
        
        //$this->setLog($response,true);
        
        $response = $response['data'][0];
        
        $arrReturn = array();
         
        $longltd = explode(',',$response['coord']);
        //standarize, buat jaga2 kalo ad GPS lain 
        $arrReturn['location']['address'] = str_replace(', Indonesia','',$response['posisi']);
        $arrReturn['location']['latitude'] = (isset($longltd[0])) ? $longltd[0] : '';
        $arrReturn['location']['longitude'] = (isset($longltd[1])) ? $longltd[1] : '';

        $arrReturn['vehiclestatus'] = $response['status'];
        
        //$arrReturn['speed'] = $response['data']['speed'];

        
        return $arrReturn;
    }
    
    function getAPIInformation(){
        $arr = array();
        $arr['username'] = ''; 
        $arr['password'] = '012821009mlbdgdhria';
        $arr['baseUrl'] = 'http://member.barstow-is.com/tramigo/api/trioeagle.php';
        
        return $arr;
    }
	  
    function setLog($msg,$alwaysShow = false ,$filename = ''){ 
	   
        if(!$alwaysShow && !DEBUG)  return; 
        
        if(is_array($msg)) $msg = print_r($msg, true);
        
	 	$path = DOC_ROOT.'log/';
		
		if (!file_exists($path)) {
			mkdir($path, 0755, true);
		} 
		
        $filename = (empty($filename)) ? '['.date('d-m-Y') .'] - '.md5(DOC_ROOT).'.txt' : $filename; 
        $filename = $path.$filename; 
				   
		error_log ($msg.chr(13),3,$filename);
	}
	
	

}
		
?>