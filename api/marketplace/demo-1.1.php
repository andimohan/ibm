<?php   

define('API_URL', 'https://pstn.program-stok.com/api/marketplace/');
 
$url = API_URL.'products.php'; // cancel job order


$payload = array();
array_push($payload,  array('id' => 'ITM0000', 'qty' => 10 ));
array_push($payload,  array('id' => 'ITM05650', 'qty' => 2 ));



execute($url, $payload, 'POST');

function execute($url, $payload, $action, $pretty = true){ 
    
    $payload = json_encode($payload); 
    //echo  $payload.'<br>';   
        
    $auth = '123456'; //hash_hmac('sha256', $url, SECRET_KEY);

    $header = array(
        'Content-Type: application/json',  
        'Authorization: '.$auth
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

    $response = curl_exec($connection);
    
    curl_close($connection);   
     
    if($pretty){ 
        $response = json_decode($response,true);
        echo '<pre>';
        print_r($response);
        echo '</pre>';
    }else{ 
         echo $response; 
    }

    return $response;
}
?>