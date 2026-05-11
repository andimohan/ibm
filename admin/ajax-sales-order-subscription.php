<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';   

includeClass(array('SalesOrderSubscription.class.php'));
$salesOrderSubscription = createObjAndAddToCol( new SalesOrderSubscription()); 

$obj = $salesOrderSubscription;    
$arrCriteria = array();  
//array_push ($arrCriteria, $obj->tableName.'.statuskey = 2');  

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';
if (isset($_GET) && !empty($_GET['action'])) {
    switch ( $_GET['action']){  
            
        case 'getDetailForInvoice' : 
            if (!isset($_GET['pkey'])) die;
            
            $pkey = $_GET['pkey'];
                
            $criteria = implode(' and ', $arrCriteria);  
            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   
            $rsData = $obj->getItemForInvoice($pkey);
            
            echo json_encode($rsData); 
            break; 
            
    }
}
  
die;
  
?>