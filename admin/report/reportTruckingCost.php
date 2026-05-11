<?php

include '../../_config.php';  
include '../../_include-v2.php';

includeClass('TruckingServiceOrder.class.php');
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$truckingServiceOrderCategory = createObjAndAddToCol(new TruckingServiceOrderCategory());
$warehouse = createObjAndAddToCol(new Warehouse());
$truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE,1)); 

include '_global.php';

$obj = $truckingServiceOrder;
$securityObject = 'reportTruckingCost'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();
$detailCriteria = '';
//$_POST['selStatus[]'] = array(2,3); 

$isGrouping = (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;

$arrDateType = array();
$arrDateType[1] = $obj->lang['transactionDate'];
$arrDateType[2] = $obj->lang['refTransactionDate'];


// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

if (!isset($_POST['trRefStartDate']) || empty($_POST['trRefStartDate'])) {
    $_POST['trRefStartDate'] = date('d / m / Y');
    $_POST['trRefEndDate'] = date('d / m / Y');
}

$orderCriteria = array();
$orderCriteria['orderBy'] = (isset($_POST) && !empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey';
$orderCriteria['orderType'] = (isset($_POST) && !empty($_POST['hidOrderType'])) ? $_POST['hidOrderType'] : 1;

// ====================== must be set before TWIG

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'width' => "130px", 'dbfield' => 'code');
$arrDataStructure['refCode'] = array('title' => ucwords($obj->lang['reference']), 'width' => "130px", 'dbfield' => 'refcode');
$arrDataStructure['si'] = array('title' => ucwords($obj->lang['si']), 'width' => "130px", 'dbfield' => 'donumber');
$arrDataStructure['category'] = array('title' => ucwords($obj->lang['category']), 'width' => "150px", 'dbfield' => 'categoryname');
$arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "100px", 'format' => 'date');
$arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']), 'dbfield' => 'warehousename', 'width' => "110px");

if (!$isGrouping) {
    $arrDataStructure['cost'] = array('title' => ucwords($obj->lang['cost']), 'width' => "150px", 'dbfield' => 'costname');
    $arrDataStructure['requestAmount'] = array('title' => ucwords($obj->lang['amount']), 'dbfield' => 'requestamount', 'width' => "100px", 'format' => 'number', 'calculateTotal' => true, 'textColor' => '568203');
}

if ($obj->useRealization()) {
    $arrDataStructure['amount'] = array('title' => ucwords($obj->lang['realization']), 'dbfield' => 'amount', 'width' => "100px", 'format' => 'number', 'calculateTotal' => true);

    if (!$isGrouping)
        $arrDataStructure['balance'] = array('title' => ucwords($obj->lang['balance']), 'dbfield' => 'balance', 'width' => "100px", 'format' => 'number', 'calculateTotal' => true);
}

$arrDataStructure['cashOutCode'] = array('title' => ucwords($obj->lang['cashOut']) . ' / ' . ucwords($obj->lang['ap']), 'dbfield' => 'cashoutcode', 'width' => "150px");
$arrDataStructure['recipient'] = array('title' => ucwords($obj->lang['recipient']), 'width' => "180px", 'dbfield' => 'recipientname');
$arrDataStructure['carRegistrationNumber'] = array('title' => ucwords($obj->lang['car']), 'dbfield' => 'carregistrationnumber', 'width' => "80px");
$arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'dbfield' => 'customername', 'width' => "180px");
$arrDataStructure['consignee'] = array('title' => ucwords($obj->lang['consignee']), 'dbfield' => 'consigneename', 'width' => "180px");
$arrDataStructure['location'] = array('title' => ucwords($obj->lang['location']), 'dbfield' => 'locationname', 'width' => "100px");
$arrDataStructure['route'] = array('title' => ucwords($obj->lang['route']), 'dbfield' => 'route', 'width' => "200px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['costReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrWarehouse = $class->convertForCombobox($warehouse->searchData('', '', true, ' and (' . $warehouse->tableName . '.statuskey = 1)'), 'pkey', 'name');
$arrCategory = $class->convertForCombobox($truckingServiceOrderCategory->searchData('', '', true, ' and (' . $truckingServiceOrderCategory->tableName . '.statuskey = 1)'), 'pkey', 'name');

$rsCost = $truckingCost->searchData('','',true, ' and (' . $truckingCost->tableName . '.statuskey = 1)');
$rsCost = array_merge(array(array('pkey'=> -1, 'name' => $obj->lang['truckingFee']),array('pkey'=> -2, 'name' => $obj->lang['driverCommission']),array('pkey'=> -3, 'name' => $obj->lang['codriverCommission'])), $rsCost);
$arrCost = $class->convertForCombobox($rsCost,'pkey', 'name');


$arrTwigVar['inputCode'] = $class->inputText('code');
$arrTwigVar['inputRefCode'] = $class->inputText('refCode');
$arrTwigVar['inputTransactionType'] = $class->inputSelect('selDateType', $arrDateType);
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputRefStartDate'] = $class->inputDate('trRefStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputRefEndDate'] = $class->inputDate('trRefEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputCostName'] = $class->inputText('costName');
$arrTwigVar['inputDONumber'] = $class->inputText('doNumber');
$arrTwigVar['inputSelWarehouse'] = $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCategory'] = $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCost'] = $class->inputSelect('selCost[]', $arrCost, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsGrouping'] = $class->inputCheckBox('isGrouping');
$arrTwigVar['order'] = $orderCriteria;
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

$arrAdditionalCost = array();

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';

    if (isset($_POST) && !empty($_POST['code'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code LIKE (' . $class->oDbCon->paramString('%' . $_POST['code'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => $obj->lang['code'], 'filter' => $_POST['code']));
    }

    if (isset($_POST) && !empty($_POST['refCode'])) {
        $criteria .= ' AND ( ' . $obj->tableRef . '.code LIKE (' . $class->oDbCon->paramString('%' . $_POST['refCode'] . '%') . ') )';
        array_push($arrFilterInformation, array("label" => $obj->lang['refCode'], 'filter' => $_POST['refCode']));
    }

    if (isset($_POST) && !empty($_POST['doNumber'])) {
        $criteria .= ' AND ( ' . $obj->tableRef . '.donumber LIKE (' . $class->oDbCon->paramString('%' . $_POST['doNumber'] . '%') . ') )';
        array_push($arrFilterInformation, array("label" => $obj->lang['si'], 'filter' => $_POST['doNumber']));
    }

    if (isset($_POST) && !empty($_POST['trStartDate'])) {
        $tableName = ($_POST['selDateType'] == 1) ? $obj->tableName : $obj->tableRef;
        $criteria .= ' and  ' . $tableName . '.trdate between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ');

        array_push($arrFilterInformation, array("label" => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
    }

    // gk bisa, karena beberapa gk ad nama item
    if (isset($_POST) && !empty($_POST['costName'])) {
        $criteria .= ' AND  ' . $obj->tableItem . '.name LIKE (' . $class->oDbCon->paramString('%' . $_POST['costName'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => $obj->lang['cost'], 'filter' => $_POST['costName']));
    }

    if (isset($_POST) && !empty($_POST['selCost'])) {

        // buat dummy menandakan ada criteria category
        array_push($arrAdditionalCost,0);
        
        foreach($_POST['selCost'] as $categoryRow){
            if($categoryRow < 0)
             array_push($arrAdditionalCost,$categoryRow);
        }
        
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCost']));

        $criteria .= ' AND ' . $obj->tableItem . '.pkey in(' . $key . ')';

        $rsCriteria = $truckingCost->searchData('', '', true, ' and ' . $truckingCost->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $costName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['cost'], 'filter' => $costName));

    }

    if (isset($_POST) && !empty($_POST['selCategory'])) {
 
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));

        $criteria .= ' AND ' . $obj->tableName . '.categorykey in(' . $key . ')';

        $rsCriteria = $truckingServiceOrderCategory->searchData('', '', true, ' and ' . $truckingServiceOrderCategory->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $categoryName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['category'], 'filter' => $categoryName));

    }

    if (isset($_POST) && !empty($_POST['selWarehouse'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));

        $criteria .= ' AND ' . $obj->tableName . '.warehousekey in(' . $key . ')';

        $rsCriteria = $warehouse->searchData('', '', true, ' and ' . $warehouse->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $warehouseName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['warehouse'], 'filter' => $warehouseName));

    }

    $order = 'order by ' . $orderCriteria['orderBy'] . ' ' . (($orderCriteria['orderType'] == 1) ? 'desc' : 'asc');

    $tempreport = '';

    $rs = $obj->generateCostReport($criteria, $order, $arrAdditionalCost);

    // untuk grouping 
    if ($isGrouping) {

        $arrDataStructure['amount']['title'] = ucwords($obj->lang['totalCost']);
        $arrDataStructure['cashOutCode']['sortable'] = false;
        $arrDataStructure['recipient']['sortable'] = false;

        $rsTemp = array();
        $arrTempStructure = array();

        $useRealization = $obj->useRealization();
        $realizationPrefix = '-R';

        foreach ($rs as &$row) {

            // UDPATE COLUMN STRUCTURE
            //$obj->setLog( 'cost'.$row['costkey']. ' => ' .$row['amount']);
            $arrStructureIndex = 'cost' . $row['costkey'] . strtolower($row['costname']);
            if (!isset($arrTempStructure[$arrStructureIndex])) {
                $arrTempStructure[$arrStructureIndex] = array('title' => $row['costname'], 'dbfield' => $arrStructureIndex, 'width' => "150px", 'format' => 'number', 'sortable' => false, 'calculateTotal' => true, 'textColor' => '568203');

                if ($useRealization)
                    $arrTempStructure[$arrStructureIndex . $realizationPrefix] = array('title' => $row['costname'] . ' (R)', 'dbfield' => $arrStructureIndex . $realizationPrefix, 'width' => "150px", 'format' => 'number', 'sortable' => false, 'calculateTotal' => true, 'textColor' => '0093AF');

            }

            // UDPATE COST  AMOUNT
            // kalo baru set awal
            $rowIndex = $row['code'];
            if (!isset($rsTemp[$rowIndex])) {
                $rsTemp[$rowIndex] = $row;
                $rsTemp[$rowIndex]['requestamount'] = 0;
                $rsTemp[$rowIndex]['amount'] = 0;
                $rsTemp[$rowIndex]['balance'] = 0;
                $rsTemp[$rowIndex]['recipientname'] = '';
                $rsTemp[$rowIndex]['temprecipient'] = array();
                $rsTemp[$rowIndex]['tempcashoutcode'] = array();
            }

            // update penerima
            if (!empty($row['recipientname']) && !in_array($row['recipientname'], $rsTemp[$rowIndex]['temprecipient'])) {
                array_push($rsTemp[$rowIndex]['temprecipient'], $row['recipientname']);
                $rsTemp[$rowIndex]['recipientname'] = implode('<br>', $rsTemp[$rowIndex]['temprecipient']);
            }

            // update cash out code
            if (!empty($row['cashoutcode']) && !in_array($row['cashoutcode'], $rsTemp[$rowIndex]['tempcashoutcode'])) {
                array_push($rsTemp[$rowIndex]['tempcashoutcode'], $row['cashoutcode']);
                $rsTemp[$rowIndex]['cashoutcode'] = implode('<br>', $rsTemp[$rowIndex]['tempcashoutcode']);
            }

            // hitung subtotal cost 
            $rsTemp[$rowIndex][$arrStructureIndex] = (!isset($rsTemp[$rowIndex][$arrStructureIndex])) ? $row['requestamount'] : $rsTemp[$rowIndex][$arrStructureIndex] + $row['requestamount'];
            $rsTemp[$rowIndex][$arrStructureIndex . $realizationPrefix] = (!isset($rsTemp[$rowIndex][$arrStructureIndex . $realizationPrefix])) ? $row['amount'] : $rsTemp[$rowIndex][$arrStructureIndex . $realizationPrefix] + $row['amount'];

            $rsTemp[$rowIndex]['requestamount'] += $row['requestamount'];
            $rsTemp[$rowIndex]['amount'] += $row['amount'];
            $rsTemp[$rowIndex]['balance'] += $row['balance'];

        }

        //$sliceIndex = 6;
        //$arrDataStructure = array_slice($arrDataStructure, 0, $sliceIndex, true) +  $arrTempStructure + array_slice($arrDataStructure, $sliceIndex + 1, count($arrDataStructure)-$sliceIndex, true);

        $arrReturn = $obj->insertReportColumns(7, $arrDataStructure, $arrTempStructure, $twig, $arrTwigVar, $arrHeaderTemplate);
        $arrTemplate = $arrReturn['tableTemplate'];

        unset($row);
        $rs = $rsTemp;
    }

    foreach ($rs as $row) {

        $arrHeaderStyle = array();
        $arrHeaderStyle['recipientname']['textColor'] = ($row['isoutsource'] == 1) ? '0093AF' : '';


        if ($row['balance'] > 0) {
            $arrHeaderStyle['amount']['textColor'] = '0093AF';
            $arrHeaderStyle['balance']['textColor'] = '0093AF';
        }

        $return = $obj->formatReportRows(array('data' => $row, 'style' => $arrHeaderStyle), $arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];

        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation, $tableHeader);
}


echo $twig->render('reportTruckingCost.html', $arrTwigVar);

?>