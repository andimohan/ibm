<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('DisposalJobOrder.class.php');
$disposalJobOrder = createObjAndAddToCol(new DisposalJobOrder());
$customer = createObjAndAddToCol(new Customer());

$obj = $disposalJobOrder;

$fieldValue = array('code');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
       case 'searchDataForInvoice':
           $returnField = array('key' => $obj->tableName . '.pkey', 'value' => $fieldValue);
           //overwrite field yg di search 
           $searchFieldValue = (isset($_GET['searchField']) && !empty($_GET['searchField'])) ? explode(',', $_GET['searchField']) : $fieldValue;
           $searchOptions = array('field' => $searchFieldValue,  'key' => $_GET['term']);

           $statuskey = array(2,3,4,5);

           $criteria = array();
           array_push($criteria, $obj->tableName . '.statuskey in (' . $obj->oDbCon->paramString($statuskey,',') . ')');

           if (isset($_GET['customerkey']) && !empty($_GET['customerkey'])) {
               array_push($criteria, $obj->tableName . '.customerkey = ' . $obj->oDbCon->paramString($_GET['customerkey']));
                  array_push($criteria, $obj->tableName . '.totaluninvoiced > 0');
           } else {
               array_push($criteria, 'false'); // sementara biar kalo gk ad customer, gk ketarik semua invoicenya
           }


           $criteria = implode(' and ', $criteria);

           $searchOptions['criteria'] = ' and ' . $criteria;
//
//           $obj->setLog('$criteria >>>>',true);
//           $obj->setLog($criteria,true);
//            
           $rsData = $obj->searchDataForAutoComplete($returnField, $searchOptions, $order);
           echo json_encode($rsData);

           break;
//            
        case 'getTotalInvoicedAndOutstanding':  
           if (empty($_GET['pkey'])) die;
            
           $rs = $obj->getTotalInvoicedAndOutstanding($_GET['pkey'], $_GET['invoiceType']);

           echo json_encode($rs);
           break;
            
        case 'searchDataForWOList':
            $fieldValue = array($obj->tableName . '.code', $customer->tableName . '.name');
            $order = 'order by ' . $obj->tableName . '.code asc';
            $returnField = array('key' => $obj->tableName . '.pkey', 'value' => $fieldValue);
            $arrCriteria = array();
            array_push($arrCriteria, '(' . $obj->tableName . '.statuskey = 4 )');

            if (isset($_GET['term']) && !empty($_GET['term'])) {
                array_push($arrCriteria, '(' . $obj->tableName . '.code like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%') . ' or ' . $customer->tableName . '.name like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%') . ')');
            }

            $arrCriteria = implode(' and ', $arrCriteria);
            $searchOptions['criteria'] = ' and ' . $arrCriteria;
            $rs = $obj->searchDataForAutoComplete($returnField, $searchOptions, $order);

            echo json_encode($rs);
            break;
            
       case 'getUnInvoicedItemDetail':
           if (empty($_GET['pkey'])) die;

            $rs = $obj->getUnInvoicedItemDetail($_GET['pkey']);
           echo json_encode($rs);
           break; 

        case 'getWasteDetail':

            if (!isset($_GET['pkey'])) die;
            $pkey = $_GET['pkey'];
            $arrCriteria = array();
            if (isset($_GET['term']) && !empty($_GET['term'])) {
                array_push($arrCriteria, '(' . $obj->tableWaste . '.code like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%') . ' or ' . $obj->tableWaste . '.name like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%') . ')');
            }
            $criteria = implode(' and ', $arrCriteria);
            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

            $rsData = $obj->getWasteDetail($pkey, $criteria);
            for($i=0;$i<count($rsData);$i++){
                $rsData[$i]['pkey'] = $rsData[$i]['wastekey'];
                $rsData[$i]['value'] = $rsData[$i]['wastecodename'];
            }
            echo json_encode($rsData);
            break;
    }
}

die;
