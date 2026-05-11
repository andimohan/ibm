<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('PurchasePrice.class.php');
$purchasePrice = createObjAndAddToCol(new PurchasePrice());

$obj = $purchasePrice;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 2');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
        
	switch ($_GET['action']){  
		case 'getLatestPurchasePricing':  

            if(empty($_GET['supplierkey']) || empty($_GET['itemkey'])) die;

            $date = $_GET['date'];
            $supplierkey = $_GET['supplierkey'];
            $itemkey = $_GET['itemkey'];

			$result = $obj->getLatestPurchasePricing($supplierkey, $itemkey, $date);
		
			echo json_encode($result);  
			break;  
	}

} 

die;

?>
