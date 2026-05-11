<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('PurchaseOrderJewelry.class.php');
$purchaseOrderJewelry = createObjAndAddToCol(new PurchaseOrderJewelry());
$itemUnit = createObjAndAddToCol(new ItemUnit());

$obj = $purchaseOrderJewelry;

// include 'ajax-general.php';


if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
        case 'searchData':
            $order = 'order by ' . $obj->tableName . '.code asc';

            $arrCriteria = array();

            // bedakan parameter kosong atau tdk pernah dikirim
            if (isset($_GET['pkey'])) {
                $_GET['pkey'] = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                array_push($arrCriteria, $obj->tableName . '.pkey = ' . $obj->oDbCon->paramString($_GET['pkey']));
            }

            if (isset($_GET['isfullreceive'])) {
                $_GET['isfullreceive'] = (empty($_GET['isfullreceive'])) ? 0 : $_GET['isfullreceive'];
                array_push($arrCriteria, $obj->tableName . '.isfullreceive = ' . $obj->oDbCon->paramString($_GET['isfullreceive']));
            }

            if (isset($_GET['term']) && !empty($_GET['term']))
                array_push($arrCriteria, '(' . $obj->tableSupplier . '.name like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%') . ' or ' . $obj->tableName . '.code like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%') . ' or ' . $obj->tableName . '.refinvoicecode like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%') . ')');

            array_push($arrCriteria, $obj->tableName . '.statuskey = 2');

            $criteria = implode(' and ', $arrCriteria);
            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

            $rs = $obj->searchDataForAutoComplete('', '', false, $criteria, $order);
            for ($i = 0; $i < count($rs); $i++) {
                $rs[$i]['value'] = htmlspecialchars_decode($rs[$i]['value']);
            }

            echo json_encode($rs);
            break;
        case 'getOutstandingDetail':

            if (!isset($_GET) || empty($_GET['pkey']))
                die;

            $pokey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];

            $arrCriteria = array();
            array_push($arrCriteria, $obj->tableName.'.statuskey = 2');
            array_push($arrCriteria, 'qtyinbaseunit > receivedqtyinbaseunit');
            array_push($arrCriteria, 'qtyinpcs > receivedqtyinpcs');

            $criteria = implode(' and ', $arrCriteria);
            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

            $orderBy = ' order by '.$obj->tableNameDetail.'.number asc';
            $rs = $obj->getDetailWithRelatedInformation($pokey, $criteria,$orderBy);
            echo json_encode($rs);

            break;

            case 'getDetailForReceiving' :

                $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                
                $rsUnit = $itemUnit->searchDataRow(array($itemUnit->tableName.'.pkey',$itemUnit->tableName.'.name'), ' and ' . $itemUnit->tableName.'.statuskey = 1');
                $rsUnitCol = $obj->reindexDetailCollections($rsUnit, 'pkey');

                $rs = $obj->getDetailByColumn('pkey',$pkey,true,'','order by '.$obj->tableNameDetail.'.number asc');
                
                for($i=0;$i<count($rs);$i++){
                    $rs[$i]['unitname'] = (isset($rsUnitCol[$rs[$i]['unitkey']])) ? $rsUnitCol[$rs[$i]['unitkey']][0]['name'] : '';
                }
                
                echo json_encode($rs);

                break;
    }
}

die;

?>