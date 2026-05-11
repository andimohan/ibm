<?php
require_once '../../_config.php';  
require_once '_include.php';

// sementara
require_once '../../assets/vendor/autoload.php'; 
use Aws\S3\S3Client;

$OBJ = $class;  

       
// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access"); 
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
$ACTION = $_SERVER['REQUEST_METHOD'];  

if($ACTION != 'POST') endForRequestMethodError(); 

$RETURN_VALUE = array();

// POST / PUT 
$fileContent = file_get_contents("php://input"); 

$postVars = json_decode($fileContent,true); 
$fileName = (isset($postVars['file_name'])) ? $postVars['file_name'] : '';

// normalnya
$arrAuth= explode('.',$_SERVER['HTTP_AUTHORIZATION']);
$userkey = $arrAuth[1];

$RETURN_VALUE['response_code']  = 200;
$RETURN_VALUE['message'] = '';  
$RETURN_VALUE['data'] = createPresignedUploadUrl($fileName);         

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE); 
die;



 function createPresignedUploadUrl($fileName, $timeLimit='+2 minutes',$client='', $opt = array()) {
        global $class;
     
        if (empty($fileName)) return '';
        
        // filename harus dipaksa sesuai domain
        $fileName = DOMAIN_NAME . '/'.$fileName;
      
        $uploadSize = (isset($opt['uploadSize'])) ? $opt['uploadSize'] : (10 * 1024 * 1024);// 10MB max
            
        try { 
           
            if(empty($client)) $client = $class->initS3Client();
            
            $fileName = html_entity_decode($fileName, ENT_QUOTES, 'UTF-8'); 

            // Create command for uploading
        
            $cmd = $client->getCommand('PutObject', [
                'Bucket' => STORAGE['bucket'], 
                //'Bucket' => 'sandbox', // utk demo
                'Key'    => $fileName,
                'Conditions' => [
                    ["starts-with", "$Content-Type", "image/"],   // allow all images
                    ["eq", "$Content-Type", "application/pdf"],   // allow PDF
                    ["content-length-range", 1, $uploadSize] // 10MB max
                ],
            ]);
       

            // Create presigned URL
            $request = $client->createPresignedRequest($cmd,$timeLimit);

            return (string) $request->getUri();
 
        } catch (AwsException $e) {
           $class->setLog($e->getMessage(),true);
        }
        
        
    }
?>