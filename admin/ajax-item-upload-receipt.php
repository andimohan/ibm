<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

if(!$security->isAdminLogin('ReceiptValidation',10,true));
if(!isset($_POST) || empty($_POST['action']))  die;

includeClass('ItemUploadReceipt.class.php');
$itemUploadReceipt = createObjAndAddToCol(new ItemUploadReceipt());

$obj = $itemUploadReceipt;   

include 'ajax-general.php';

switch ($_POST['action']){  
    case 'updateStatus' :   $pkey = $_POST['pkey'];
                            $statuskey = $_POST['statuskey']; 
                            $cancelReason = $_POST['cancelReason']; 
        
                            if(empty($pkey) || empty($statuskey)) die; 
        
                            // cek ulang hak akses per status
                            if(!$security->isAdminLogin('ReceiptValidation',$statuskey,true)); 
                            $response = $itemUploadReceipt->changeStatus($pkey,$statuskey,$cancelReason);
                            
                            break;
}


echo json_encode($response);
die;
  
?>