<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array(
    'PutAway.class.php',
    'Warehouse.class.php',
    'WarehouseLayout.class.php',
    'Pallet.class.php'
));

$pickingList = new PutAway(3);
$warehouse = new Warehouse();
$warehouseLayout = new WarehouseLayout();
$pallet = new Pallet();
$obj = $pickingList;

include '_global.php';

$securityObject = 'ReportPickingList';

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();
$detailCriteria = '';
$dataToExport = array();
$arrTemplate = array();

if (!isset($_POST['isGrouping'])) {
    $_POST['isGrouping'] = isset($_POST['isShowDetail']) ? $_POST['isShowDetail'] : 1;
}

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

$orderCriteria = array();
$orderCriteria['orderBy'] = (isset($_POST) && !empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate';
$orderCriteria['orderType'] = (isset($_POST) && !empty($_POST['hidOrderType'])) ? $_POST['hidOrderType'] : -1;

$isGrouping = (isset($_POST['isGrouping']) && $_POST['isGrouping'] == 1) ? true : false;

switch ($EXPORT_TYPE) {
    case 2:
        $arrDataStructure = array();
        $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'code', 'width' => '110px');
        $arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        if (!$isGrouping) {
                $arrDataStructure['itemcode'] = array('title' => ucwords($obj->lang['itemCode']), 'dbfield' => 'itemcode', 'width' => '140px', 'format' => 'string');
                $arrDataStructure['itemname'] = array('title' => ucwords($obj->lang['itemName']), 'dbfield' => 'itemlabel', 'width' => '600px');
                $arrDataStructure['containernumber'] = array('title' => ucwords($obj->lang['containerNumber']), 'dbfield' => 'itemreceivingcontainernumber', 'width' => '220px');
                $arrDataStructure['pallet'] = array('title' => ucwords($obj->lang['pallet']), 'dbfield' => 'palletname', 'width' => '120px');
                $arrDataStructure['zone'] = array('title' => ucwords($obj->lang['zone']), 'dbfield' => 'warehouselayoutname', 'width' => '150px');
                $arrDataStructure['receivingqty'] = array('title' => ucwords($obj->lang['inStock']), 'dbfield' => 'receivingqty', 'width' => '80px', 'format' => 'number');
                $arrDataStructure['qty'] = array('title' => ucwords($obj->lang['qty']), 'dbfield' => 'qty', 'width' => '80px', 'format' => 'number');

        }
        $arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']), 'dbfield' => 'warehousename', 'width' => '140px');
        $arrDataStructure['refcode'] = array('title' => ucwords($obj->lang['itemReceiving']), 'dbfield' => 'refcode', 'width' => '140px');
        $arrDataStructure['submissionNumber'] = array('title' => $obj->lang['submissionNumber'], 'dbfield' => 'itemreceivingsubmissionnumber', 'width' => '220px');
        $arrDataStructure['destinationzone'] = array('title' => ucwords($obj->lang['destinationZone']), 'dbfield' => 'warehouselayoutname', 'width' => '150px');
        $arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => '80px');
        break;

    default:
        $arrDataStructure = array();
        $arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => '40px', 'autoNumber' => true, 'sortable' => false);
        $arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'code', 'width' => '110px');
        $arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => '120px', 'format' => 'date', 'align' => 'center');
        if (!$isGrouping) {
                $arrDataStructure['itemcode'] = array('title' => ucwords($obj->lang['itemCode']), 'dbfield' => 'itemcode', 'width' => '140px', 'format' => 'string');
                $arrDataStructure['itemname'] = array('title' => ucwords($obj->lang['itemName']), 'dbfield' => 'itemlabel', 'width' => '600px');
                $arrDataStructure['containernumber'] = array('title' => ucwords($obj->lang['containerNumber']), 'dbfield' => 'itemreceivingcontainernumber', 'width' => '220px');
                $arrDataStructure['pallet'] = array('title' => ucwords($obj->lang['pallet']), 'dbfield' => 'palletname', 'width' => '120px');
                $arrDataStructure['zone'] = array('title' => ucwords($obj->lang['zone']), 'dbfield' => 'warehouselayoutname', 'width' => '150px');
                $arrDataStructure['receivingqty'] = array('title' => ucwords($obj->lang['inStock']), 'dbfield' => 'receivingqty', 'width' => '80px', 'format' => 'number');
                $arrDataStructure['qty'] = array('title' => ucwords($obj->lang['qty']), 'dbfield' => 'qty', 'width' => '80px', 'format' => 'number');

        }
        $arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']), 'dbfield' => 'warehousename', 'width' => '140px');
        $arrDataStructure['refcode'] = array('title' => ucwords($obj->lang['itemReceiving']), 'dbfield' => 'refcode', 'width' => '140px');
        $arrDataStructure['submissionNumber'] = array('title' => $obj->lang['submissionNumber'], 'dbfield' => 'itemreceivingsubmissionnumber', 'width' => '220px');
        $arrDataStructure['destinationzone'] = array('title' => ucwords($obj->lang['destinationZone']), 'dbfield' => 'warehouselayoutname', 'width' => '150px');
        $arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => '80px');
        break;
}

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = ucwords($obj->lang['reportPickingList']);
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
array_push($arrTemplate, $arrHeaderTemplate);

if ($isGrouping) {
    $arrDataDetailStructure = array();
    $arrDataDetailStructure['itemcode'] = array('title' => ucwords($obj->lang['itemCode']), 'dbfield' => 'itemcode', 'width' => '140px', 'format' => 'string');
    $arrDataDetailStructure['itemname'] = array('title' => ucwords($obj->lang['itemName']), 'dbfield' => 'itemlabel', 'width' => '600px');
    $arrDataDetailStructure['containernumber'] = array('title' => ucwords($obj->lang['containerNumber']), 'dbfield' => 'itemreceivingcontainernumber', 'width' => '220px');
    $arrDataDetailStructure['pallet'] = array('title' => ucwords($obj->lang['pallet']), 'dbfield' => 'palletname', 'width' => '120px');
    $arrDataDetailStructure['zone'] = array('title' => ucwords($obj->lang['zone']), 'dbfield' => 'warehouselayoutname', 'width' => '150px');
    $arrDataDetailStructure['receivingqty'] = array('title' => ucwords($obj->lang['inStock']), 'dbfield' => 'receivingqty', 'width' => '80px', 'format' => 'number');
    $arrDataDetailStructure['qty'] = array('title' => ucwords($obj->lang['qty']), 'dbfield' => 'qty', 'width' => '80px', 'format' => 'number');

    $arrDetailTemplate = array();
    $arrDetailTemplate['reportWidth'] = '800px';
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate);
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrWarehouseLayout = $class->convertForCombobox($warehouseLayout->searchData('', '', true, '', 'order by name asc'), 'pkey', 'name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');

$arrTwigVar['inputCode'] = $class->inputText('code');
$arrTwigVar['inputRefCode'] = $class->inputText('refCode');
$arrTwigVar['inputSubmissionNumber'] = $class->inputText('submissionNumber');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelWarehouse'] = $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelDestination'] = $class->inputSelect('selDestination[]', $arrWarehouseLayout, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsGrouping'] = $class->inputCheckBox('isGrouping');
$arrTwigVar['isGrouping'] = $isGrouping;
$arrTwigVar['order'] = $orderCriteria;
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

if (isset($_POST) && !empty($_POST['hidAction'])) {
    $criteria = '';

    if (isset($_POST['code']) && !empty($_POST['code'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code LIKE (' . $class->oDbCon->paramString('%' . $_POST['code'] . '%') . ')';
        array_push($arrFilterInformation, array('label' => $obj->lang['code'], 'filter' => $_POST['code']));
    }

    if (isset($_POST['trStartDate']) && !empty($_POST['trStartDate'])) {
        $criteria .= ' AND ' . $obj->tableName . '.trdate between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ', 'Y-m-d 23:59:59');
        array_push($arrFilterInformation, array('label' => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
    }

    if (isset($_POST['refCode']) && !empty($_POST['refCode'])) {
        $criteria .= ' AND ' . $obj->tableName . '.refkey in (select pkey from ' . $obj->tableItemReceiving . ' where code like (' . $class->oDbCon->paramString('%' . $_POST['refCode'] . '%') . '))';
        array_push($arrFilterInformation, array('label' => $obj->lang['itemReceiving'], 'filter' => $_POST['refCode']));
    }

    if (isset($_POST['selWarehouse']) && !empty($_POST['selWarehouse'])) {
        $key = implode(',', $class->oDbCon->paramString($_POST['selWarehouse']));
        $criteria .= ' AND ' . $obj->tableName . '.warehousekey in(' . $key . ')';

        $rsCriteria = $warehouse->searchData('', '', true, ' and ' . $warehouse->tableName . '.pkey in (' . $key . ')');
        $arrTemp = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTemp, $rsCriteria[$k]['name']);
        }
        array_push($arrFilterInformation, array('label' => $obj->lang['warehouse'], 'filter' => implode(', ', $arrTemp)));
    }

    if (isset($_POST['selDestination']) && !empty($_POST['selDestination'])) {
        $key = implode(',', $class->oDbCon->paramString($_POST['selDestination']));
        $criteria .= ' AND ' . $obj->tableName . '.warehouselayoutkey in(' . $key . ')';

        $rsCriteria = $warehouseLayout->searchData('', '', true, ' and ' . $warehouseLayout->tableName . '.pkey in (' . $key . ')');
        $arrTemp = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTemp, $rsCriteria[$k]['name']);
        }
        array_push($arrFilterInformation, array('label' => $obj->lang['destinationZone'], 'filter' => implode(', ', $arrTemp)));
    }

    if (isset($_POST['selStatus']) && !empty($_POST['selStatus'])) {
        $key = implode(',', $class->oDbCon->paramString($_POST['selStatus']));
        $criteria .= ' AND ' . $obj->tableName . '.statuskey in(' . $key . ')';

        $rsCriteria = $obj->getStatusById($key);
        $arrTemp = array();
        for ($k = 0; $k < count($rsCriteria); $k++) {
            array_push($arrTemp, $rsCriteria[$k]['status']);
        }
        array_push($arrFilterInformation, array('label' => $obj->lang['status'], 'filter' => implode(', ', $arrTemp)));
    } else {
        $criteria .= ' AND ' . $obj->tableName . '.statuskey in (1,2,3)';
    }

    $orderByMap = array(
        'code' => 'code',
        'date' => 'trdate',
        'trdate' => 'trdate',
        'warehouse' => 'warehousename',
        'warehousename' => 'warehousename',
        'refcode' => 'refcode',
        'itemReceiving' => 'refcode',
        'submissionNumber' => 'itemreceivingsubmissionnumber',
        'itemreceivingsubmissionnumber' => 'itemreceivingsubmissionnumber',
        'destinationzone' => 'warehouselayoutname',
        'warehouselayoutname' => 'warehouselayoutname',
        'destinationZone' => 'warehouselayoutname',
        'status' => 'statusname',
        'statusname' => 'statusname'
    );

    $orderByKey = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate';
    $orderBy = isset($orderByMap[$orderByKey]) ? $orderByMap[$orderByKey] : 'trdate';
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    $order = 'order by ' . $orderBy . ' ' . $orderType;

    $rs = $obj->searchData('', '', true, $criteria, $order);
    $tempreport = '';

    if (!$isGrouping) {
        $arrFlatData = array();

        for ($i = 0; $i < count($rs); $i++) {
            $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'], $detailCriteria);

            if (empty($rsDetail)) {
                continue;
            }

            foreach ($rsDetail as $detailRow) {
                $flatRow = $rs[$i];
                $flatRow['itemcode'] = $detailRow['itemcode'];
                $flatRow['itemlabel'] = $detailRow['itemlabel'];
                $flatRow['itemreceivingcontainernumber'] = $detailRow['itemreceivingcontainernumber'];
                $flatRow['palletname'] = $detailRow['palletname'];
                $flatRow['warehouselayoutname'] = $detailRow['warehouselayoutname'];
                $flatRow['receivingqty'] = $detailRow['receivingqty'];
                $flatRow['qty'] = $detailRow['qty'];
                array_push($arrFlatData, $flatRow);
            }
        }

        $rs = $arrFlatData;
    }

    for ($i = 0; $i < count($rs); $i++) {
        if ($isGrouping) {
            $rsDetail = $obj->getDetailWithRelatedInformation($rs[$i]['pkey'], $detailCriteria);
            // $obj->setLog('rsDetail :'.print_r($rsDetail,true), true);
            if (!empty($rsDetail)) {
                $rs[$i]['_detail_'] = array('arrTemplate' => $arrDetailTemplate, 'data' => $rsDetail);
            }
        }

        $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);

        array_push($dataToExport, $return['data']);
        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);

    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation, $tableHeader);
}

echo $twig->render('reportPickingList.html', $arrTwigVar);

?>