<?php
require_once '../../_config.php';
require_once '../../_include-v2.php';

includeClass('ReceivingPurchaseJewelry.class.php');
$receivingPurchaseJewelry = createObjAndAddToCol(new ReceivingPurchaseJewelry());

$obj = $receivingPurchaseJewelry;

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {

        case 'searchData':

            $order = 'order by ' . $obj->tableName . '.code asc';

            $arrCriteria = array();

            if (isset($_GET['pkey'])) {
                $_GET['pkey'] = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                array_push($arrCriteria, $obj->tableName . '.pkey = ' . $obj->oDbCon->paramString($_GET['pkey']));
            }

            if (isset($_GET['term']) && !empty($_GET['term']))
                array_push($arrCriteria, '(' . $obj->tableName . '.code like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%') . ')');

            array_push($arrCriteria, $obj->tableName . '.statuskey in (2,3)');

            $criteria = implode(' and ', $arrCriteria);
            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

            $rs = $obj->searchDataForAutoComplete('', '', false, $criteria, $order);
            
            echo json_encode($rs);
            break;
        
        case 'getTotalQty' :

                if(!isset($_GET['pkey']) || empty($_GET['pkey'])) die;

                $pkey = $_GET['pkey'];

                $rs = $obj->getTotalQtyReceiving($pkey);

                echo json_encode($rs);
            break;

    }
}

die;

?>