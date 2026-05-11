<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass(array('TruckingPurchaseOrder.class.php'));

$truckingPurchaseOrder = new TruckingPurchaseOrder();

$obj = $truckingPurchaseOrder;    
$fieldValue = $obj->tableName.'.code';

$arrCriteria = array();       
include 'ajax-general.php';
    
if (isset($_GET) && !empty($_GET['action'])) {
			switch ($_GET['action']){  
                 
                case 'getItemPrice' :
                    
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                    $pokey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                    $itemkey = (empty($_GET['itemkey'])) ? 0 : $_GET['itemkey'];
              
                    $price = $obj->getItemPrice($pokey,$itemkey, true); 
                    echo json_encode($price);
                    
                    break;
            }
}
 
die;
  
?>