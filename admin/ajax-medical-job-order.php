<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('MedicalJobOrder.class.php');
$medicalJobOrder = createObjAndAddToCol(new MedicalJobOrder());

$obj = $medicalJobOrder;

$fieldValue = $obj->tableName . '.code';

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
        case 'getDetailDiagnose' : 
            if (!isset($_GET) || empty($_GET['pkey'])) die; 
            $rs = $obj->getDetailDiagnose($_GET['pkey']);
            echo json_encode($rs); 
            break; 
        case 'searchDataForInvoice':
            $returnField = array('key' => $obj->tableName . '.pkey', 'value' => $fieldValue);
            //overwrite field yg di search 
            $searchFieldValue = (isset($_GET['searchField']) && !empty($_GET['searchField'])) ? explode(',', $_GET['searchField']) : $fieldValue;
            $searchOptions = array('field' => $searchFieldValue,  'key' => $_GET['term']);

			$statuskey = "2";

            $criteria = array();
            array_push($criteria, $obj->tableName . '.statuskey in (' . $statuskey . ')');

            if (isset($_GET['customerkey']) && !empty($_GET['customerkey']))
                array_push($criteria, $obj->tableName . '.customerkey = ' . $obj->oDbCon->paramString($_GET['customerkey']));
            else
                array_push($criteria, 'false'); // sementara biar kalo gk ad customer, gk ketarik semua invoicenya


            $criteria = implode(' and ', $criteria);

            $searchOptions['criteria'] = ' and ' . $criteria;

            $rsData = $obj->searchDataForAutoComplete($returnField, $searchOptions, $order);

            echo json_encode($rsData);

            break;
        case 'getTotalInvoicedAndOutstanding':
            if (empty($_GET['pkey'])) die;
    
            $rs = $obj->getTotalInvoicedAndOutstanding($_GET['pkey'], $_GET['invoiceType']);
    
            echo json_encode($rs);
            break;
        case 'getUnInvoicedItemDetail':
            if (empty($_GET['pkey'])) die;
    
            $rs = $obj->getUnInvoicedItemDetail($_GET['pkey']);
            $rs = $obj->reindexDetailCollections($rs, 'refkey');
    
            echo json_encode($rs);
            break;

        case 'getUnAprrovedDetail' : 
					
            if (!isset($_GET) || empty($_GET['pkey'])) die; 
                
            $pkey = $_GET['pkey'];
                
            $arrCriteria = array();
            array_push($arrCriteria, $obj->tableNameDetail.'.statuskey = 1');
            array_push($arrCriteria, $obj->tableItem.'.isquotation = 1');
                
            $criteria = implode(' and ', $arrCriteria);  
            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   

            $rsData = $obj->getDetailWithRelatedInformation($pkey,$criteria);
            echo json_encode($rsData); 
            break; 
    }
}

die;
