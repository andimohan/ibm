<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('PackagingCode.class.php');
$packagingCode = createObjAndAddToCol(new PackagingCode());

$obj = $packagingCode;   

$arrCriteria = array();  
// array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
        case 'getAvailablePackagingByItem':

            if (empty($_GET['pkey']))
                die;

            $rsData = $obj->getAvailablePackagingCodeByItem($_GET['pkey']);

            echo json_encode($rsData);

            break;

        case 'searchPackagingCodeForReceivingPurchase' :

            $order = 'order by ' . $obj->tableName . '.code asc';

            $arrCriteria = array();

            if (isset($_GET['pkey'])) {
                array_push($arrCriteria, $obj->tableName . '.pkey = ' . $obj->oDbCon->paramString($_GET['pkey']));
            }

            if (isset($_GET['receivingpurchasekey'])) {
                $_GET['receivingpurchasekey'] = (empty($_GET['receivingpurchasekey'])) ? 0 : $_GET['receivingpurchasekey'];
                array_push($arrCriteria, $obj->tableName . '.reftransactionkey = ' . $obj->oDbCon->paramString($_GET['receivingpurchasekey']));
            }

            if (isset($_GET['itemkey']) && !empty($_GET['itemkey'])) {
                array_push($arrCriteria, $obj->tableName . '.itemkey = ' . $obj->oDbCon->paramString($_GET['itemkey']));
            }

            if (isset($_GET['term']) && !empty($_GET['term']))
                array_push($arrCriteria, '(' . $obj->tableName . '.code like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%') . ')');

            array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

            $criteria = implode(' and ', $arrCriteria);
            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

            $rs = $obj->searchDataForReceivingPurchase('', '', false, $criteria, $order);
           
            echo json_encode($rs);

            break;

            case 'getDataByBarcode' :

                $arrCriteria = array();

                if(!isset($_GET['barcode']) || empty($_GET['barcode'])) die;

                $barcode = $_GET['barcode'];

                if(!isset($_GET['pkey']) || empty($_GET['pkey'])) die;

                $pkey = $_GET['pkey'];

                array_push($arrCriteria, $obj->tableName . '.reftransactionkey = ' . $obj->oDbCon->paramString($pkey));

                $criteria = implode(' and ', $arrCriteria);
                $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

                $rs = $obj->getDataByBarcodeForReceivingPurchase($barcode, $criteria); 
                echo json_encode($rs);
            break;
    }
} 

include 'ajax-general.php';
 
die;
  
?>
