<?php 
require_once '../_config.php'; 
require_once '../_include.php';   

$obj = $salesOrderCarService; 
$arrCriteria = array();  

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';  
 
if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                 
                case 'getPackageDetail' :
                    // TODO : perlu standarisasi
                    
                    if (!isset($_GET) ||  empty($_GET['detailkey']))  
                        die;  
                    
                     $rs = $obj->getPackageDetail($_GET['detailkey']); 
                     echo json_encode($rs);
                    
                    break;
                     
                    
            }
}
 
die;
  
?>