<?php
require_once '../../_config.php';  
require_once '_include.php';
require_once '_global.php';

require_once '../../assets/vendor/autoload.php'; 
use Aws\S3\S3Client;

$obj = null;

$code = $_GET['code'];
$module = $_GET['module'];

$criteria = '';

switch ($module){  
    case 'trucking-service-work-order':  
                        require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceWorkOrder.class.php'; 
                    
                        $obj = new TruckingServiceWorkOrder(); 
                        $rs = $obj->searchDataRow(array($obj->tableName.'.pkey', $obj->tableName.'.code', $obj->tableName.'.verificationcode'), ' and ' . $obj->tableName.'.code = ' .$obj->oDbCon->paramString($code) .$criteria);
        
                        if (empty($rs)) endForDataNotFoundError();
                    
                        $fileName = md5(strtolower($rs[0]['code'])).'.png'; // perlu md5 utk menghilangkan karakter seperti '/'
                        $presignedUrl =  $obj->createPresignedURL(DOMAIN_NAME.'/'.$obj->uploadQRFolder.$fileName);
        
                        // cek sekali, kalo blm pernah ad, buat dulu
                        // pake === agar strict
                        if ($presignedUrl === false){
                            $obj->generateWorkOrderQR(array('code' => $rs[0]['code']));
                            $presignedUrl =  $obj->createPresignedURL(DOMAIN_NAME.'/'.$obj->uploadQRFolder.$fileName); 
                        }
        
                        $result = array( 'response_code' => 200,
                                         'data' => array('verification_code' => $rs[0]['verificationcode'],
                                                         'url' => $presignedUrl
                                                        )
                                        );
                     break;
}
 

echo json_encode($result);
die;
 
?>