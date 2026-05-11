<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$obj = $vendorWarrantyClaim;   
$fieldValue = $obj->tableName.'.refcode';

include 'ajax-general.php';


if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                    
                case 'getSNDetail' : 
                      
                    if (!isset($_GET['pkey']) || empty($_GET['pkey']))
                        die;
                    
                    $pkey = $_GET['pkey'];
                    
                    $rsSN = $obj->getSerialNumber($pkey);
                    $arrSN = array_column($rsSN, 'serialnumber');
                    $sn = implode(chr(13),$arrSN);
                    echo json_encode($sn); 
                    break;
            }
}


die;
  
?>
