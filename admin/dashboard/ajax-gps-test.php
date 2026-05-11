<?php  
require_once '../../_config.php';

if(!isset($_GET['registrationnumber']) || empty($_GET['registrationnumber'])) return;

$registrationNumber = $_GET['registrationnumber'];
 
$baseURL = 'https://i.accugps.com/api/open/v1/'; // harus diisi 
$action = 'trackers/A4F6A012D6/location';
$url = $baseURL. $action;
 
$token = getAccessToken();

$header = array(
'Content-Type: application/json',  
'access_token: '.$token
);


$connection = curl_init(); 
curl_setopt($connection, CURLOPT_URL, $url);
curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);   
curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($connection); 
$response = json_decode($response,true);

setLog(date('d-m-Y H:i:s') . ' => response ' ,true);
setLog($response,true);

$arrReturn = array();

if($response['status'] == 200){ 

$arrTemp = array();

$arrTemp['location'] = array();
$arrTemp['location']['address'] = str_replace(', Indonesia','',$response['data']['name']);
$arrTemp['location']['latitude'] = $response['data']['latitude'];
$arrTemp['location']['longitude'] = $response['data']['longitude'];

$arrTemp['speed'] = $response['data']['speed'];

array_push( $arrReturn,$arrTemp);

}

echo json_encode($arrReturn);
die;


function setLog($msg,$alwaysShow = false ,$title = ''){ 

	// sementara
	if(is_array($msg)) $msg = print_r($msg, true);

	$path = DOC_ROOT.'log/';
	$path .= (DOMAIN_NAME) ? DOMAIN_NAME.'/' : '';

	if (!file_exists($path)) {
		mkdir($path, 0755, true);
	} 

	$filename = (empty($title)) ? md5(DOC_ROOT) : $title; 
	$filename = 'gps - ['.date('d-m-Y') .'] - '.$filename.'.txt'; 
	$filename = $path.$filename; 

	error_log ($msg.chr(13),3,$filename);
}

function getAccessToken(){

	$baseUrl = 'https://i.accugps.com/api/open/v1/'; // harus diisi 
	$action = 'login';
	$url = $baseUrl . $action;

	$payload = array(
					 'username' =>  'thomastransgps@gmail.com',
					 'password' => '123456'
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

//	$this->token = $result['data']['access_token'];

	return strval($result['data']['access_token']);
}
?>