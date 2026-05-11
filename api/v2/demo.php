<?php  

// secretkey for logoldemo 
$secretkey = 'bf49958d3b238b11d1a520e6899216db';

// ===================================== GET USER TOKEN FOR AUTH

$response = getUserToken('wintera', 'Wintera!234',$secretkey);

if (!isset($response['data']) || empty($response['data'])) die;

$userkey = $response['data']['userkey'];
$usertoken = $response['data']['token'];

// ==================================== START REQUEST
 
//$url = 'https://logoldemo.wintera.co.id/api/v2/trucking-service-order/'.$userkey.'/detail=1&offset=12&rowPerPage=5'; 
//$url = 'https://minerva.local/api/v2/customers/'.$userkey.'/code=CUST-20191029-0001'; 
$url = 'https://minerva.local/api/v2/customers/'.$userkey; 

// empty payload for GET method
$payload = array();  
$payload = array(
    'code' => 'DEMO0009',
    'name' => 'Andi Pratama',
    'category_name' => 'EndUser', 
    'address' => 'Building Graha 130, Mangga Dua',
    'city_name' => 'Jakarta Utara',
    'zip_code' => '14311',
    'phone' => '6420001',
    'mobile' => '0817098809',
    'fax' => '6420001' ,
    'email' => 'andi.pratama@gmail.com',
    'status' => 'aktif'
);

$action = 'POST';


$payload = urldecode(http_build_query($payload)); 
  
$data = $url . '|' . $payload . $usertoken;   
$auth = hash_hmac('sha256', $data, $secretkey);   
 
$header = array(
    'Content-Type: application/json',  
    'Authorization: '.$auth
);
  
$connection = curl_init(); 
if($action <> 'GET')
 curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 

curl_setopt($connection, CURLOPT_URL, $url); 
curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);  
curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($connection, CURLOPT_HTTPHEADER,$header); 
curl_setopt($connection, CURLOPT_CUSTOMREQUEST,$action);

$response = curl_exec($connection);
curl_close($connection);

$response = json_decode($response,true);

echo '<pre>';
print_r($response);
echo '</pre>';



// ==================================================== FUNCTION
function getUserToken($username,$password, $secretkey){
    
    // URL and Payload
    
    $url = 'https://minerva.local/api/v2/token';
    $payload = array(
            'username' => $username, 
            'password' => $password,
    ); 
    $payload = urldecode(http_build_query($payload)); 

    // header 
    $data = $url . '|' . $payload;   
    $auth = hash_hmac('sha256', $data, $secretkey);   

    $header = array(
        'Content-Type: application/json',  
        'Authorization: '.$auth
    );

    // CURL  
    $connection = curl_init(); 
    curl_setopt($connection, CURLOPT_URL, $url); 
    curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0); 
    curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
    curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($connection, CURLOPT_HTTPHEADER,$header); 
    curl_setopt($connection, CURLOPT_CUSTOMREQUEST, "POST");

    $response = curl_exec($connection);
    curl_close($connection);

    return json_decode($response,true);

}
?>