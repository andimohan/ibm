<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass('LogisticSalesOrder.class.php', 'City.class.php');
$logisticSalesOrder = createObjAndAddToCol(new LogisticSalesOrder());
$warehouse = createObjAndAddToCol(new Warehouse());
$customer = createObjAndAddToCol(new Customer());
$city = createObjAndAddToCol(new City());

include '_global.php';

$obj = $logisticSalesOrder;
$securityObject = 'reportLogisticSalesOrder'; // sementara the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));


$_POST['selStatus[]'] = array(2, 3);

$arrFilterInformation = array();

$dataToExport = array();

/* data structure */
$arrTemplate = array();
$isShowDetail = (isset($_POST['isShowDetail']) && !empty($_POST['isShowDetail'])) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']),  'width' => "120px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "120px", 'format' => 'date');
$arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']), 'dbfield' => 'warehousename', 'width' => "100px",);
$arrDataStructure['transportation'] = array('title' => ucwords($obj->lang['transportation']), 'dbfield' => 'transportationname', 'width' => "100px",);
$arrDataStructure['sender'] = array('title' => ucwords($obj->lang['sender']), 'dbfield' => 'sendername', 'width' => "120px");
$arrDataStructure['senderCity'] = array('title' => ucwords($obj->lang['senderCity']), 'dbfield' => 'sendercityname', 'width' => "100px");
$arrDataStructure['recipient'] = array('title' => ucwords($obj->lang['recipient']), 'dbfield' => 'recipientname', 'width' => "150px");
$arrDataStructure['recipientCity'] = array('title' => ucwords($obj->lang['recipientCity']), 'dbfield' => 'recipientcityname', 'width' => "100px");
$arrDataStructure['totalCollie'] = array('title' => ucwords($obj->lang['totalCollie']), 'dbfield' => 'totalqty', 'width' => "80px", 'format' => 'number', 'calculateTotal' => true);
$arrDataStructure['totalWeight'] = array('title' => ucwords($obj->lang['totalWeight']) . ' (Kg)', 'dbfield' => 'totalweight', 'width' => "120px", 'format' => 'number', 'calculateTotal' => true);
$arrDataStructure['totalPrice'] = array('title' => ucwords($obj->lang['totalPrice']), 'dbfield' => 'totalprice', 'align' => 'right', 'width' => "100px", 'format' => 'number', 'calculateTotal' => true);
$arrDataStructure['packingFee'] = array('title' => ucwords($obj->lang['packingFee']), 'dbfield' => 'packingfee', 'align' => 'right', 'width' => "100px", 'format' => 'number', 'calculateTotal' => true);
$arrDataStructure['etccost'] = array('title' => ucwords($obj->lang['etccost']), 'dbfield' => 'etccost', 'align' => 'right', 'width' => "100px", 'format' => 'integer', 'calculateTotal' => true);
$arrDataStructure['total'] = array('title' => ucwords($obj->lang['total']), 'dbfield' => 'grandtotal', 'align' => 'right', 'width' => "100px", 'format' => 'integer', 'calculateTotal' => true);
$arrDataStructure['termofpaymentname'] = array('title' => ucwords($obj->lang['payment']), 'dbfield' => 'termofpaymentname', 'width' => "100px");
$arrDataStructure['trdesc'] = array('title' => ucwords($obj->lang['note']), 'dbfield' => 'trdesc', 'width' => "250px");
$arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "80px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['logisticSalesOrderReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if ($isShowDetail) {
    // detail ...
    $arrDataDetailStructure = array();
    $arrDataDetailStructure['description'] = array('title' => ucwords($obj->lang['description']),  'dbfield' => 'description', 'width' => "250px");
    $arrDataDetailStructure['weight'] = array('title' => ucwords($obj->lang['weight']) . ' (KG)',  'dbfield' => 'weight', 'width' => "100px", 'format' => 'number');
    $arrDataDetailStructure['detailVolume'] = array('title' => ucwords($obj->lang['lengthShort']) . ' x ' .  ucwords($obj->lang['widthShort']) . ' x ' .  ucwords($obj->lang['heightShort']) . ' (cm)',  'dbfield' => 'detailvolume', 'align' => 'center', 'width' => "100px");
    $arrDataDetailStructure['weightVolume'] = array('title' => ucwords($obj->lang['weight']) . ' (CMB)',  'dbfield' => 'cbmweight', 'width' => "100px", 'format' => 'integer');
    $arrDataDetailStructure['grandWeight'] = array('title' => ucwords($obj->lang['weight']),  'dbfield' => 'finalweight', 'width' => "100px", 'format' => 'number');
    $arrDataDetailStructure['price'] = array('title' => ucwords($obj->lang['price']),  'dbfield' => 'priceinunit', 'width' => "100px", 'format' => 'number');

    $arrDetailTemplate = array();
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate);
}



if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';

    if (isset($_POST) && !empty($_POST['code'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code=' . $obj->oDbCon->paramString($_POST['code']);
        array_push($arrFilterInformation, array("label" => 'Kode', 'filter' => $_POST['code']));
    }

    if (isset($_POST) && !empty($_POST['selStatus'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));

        $criteria .= ' AND ' . $obj->tableName . '.statuskey in(' . $key . ')';

        $rsCriteria =  $obj->getStatusById($key);
        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['status']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => 'Status', 'filter' => $statusName));
    }
    if (isset($_POST) && !empty($_POST['selTransportation'])) {

        $key = implode(",", $_POST['selTransportation']);

        $criteria .= ' AND ' . $obj->tableTransportation . '.pkey in(' . $key . ')';

        $rsCriteria =  $obj->getTransportationType($key);
        
        $arrTempName = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempName, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrTempName);
        array_push($arrFilterInformation, array("label" => $obj->lang['transportation'], 'filter' => $statusName));
        
    }

     if (isset($_POST) && !empty($_POST['trStartDate'])) {

         $criteria .= ' and ' . $obj->tableName . '.trdate between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ');
         array_push($arrFilterInformation, array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
     }
    
    if (isset($_POST) && !empty($_POST['selWarehouse'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));

        $criteria .= ' AND ' . $obj->tableName . '.warehousekey in(' . $key . ')';

        $rsCriteria = $warehouse->searchData('', '', true, ' and ' . $warehouse->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['warehouse'], 'filter' => $statusName));
    }
    if (isset($_POST) && !empty($_POST['selFromCity'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selFromCity']));

        $criteria .= ' AND sendercity.pkey in(' . $key . ')';

        $rsCriteria = $city->searchDataRow(array($city->tableName . '.name'), ' and ' . $city->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['senderCity'], 'filter' => $statusName));
    }
    if (isset($_POST) && !empty($_POST['selDestinationCity'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selDestinationCity']));

        $criteria .= ' AND recipientcity.pkey in(' . $key . ')';

        $rsCriteria = $city->searchDataRow(array($city->tableName . '.name'), ' and ' . $city->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['recipientCity'], 'filter' => $statusName));
    }
    if (isset($_POST) && !empty($_POST['SelSender'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['SelSender']));

        $criteria .= ' AND ' . $obj->tableName . '.senderkey in(' . $key . ')';

        $rsCriteria = $customer->searchDataRow(array($customer->tableName . '.name'), ' and ' . $customer->tableName . '.pkey in(' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['sender'], 'filter' => $statusName));
    }
    if (isset($_POST) && !empty($_POST['SelRecipient'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['SelRecipient']));

        $criteria .= ' AND ' . $obj->tableName . '.recipientkey in(' . $key . ')';

        $rsCriteria = $customer->searchDataRow(array($customer->tableName . '.name'), ' and ' . $customer->tableName . '.pkey in(' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['recipient'], 'filter' => $statusName));
    }


    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';


    $order = 'order by ' . $orderBy . ' ' . $orderType;
    $rs = $obj->searchData('', '', true, $criteria, $order);

    $rsDetailCol = ($isShowDetail == 1 || $isShowDetail) ? $obj->getDetailCollections($rs, 'refkey') : array();


    $tempreport = '';
    $totalRs = count($rs);
    for ($i = 0; $i < $totalRs; $i++) {

        if ($isShowDetail) {

            if (!isset($rsDetailCol[$rs[$i]['pkey']]))  continue;

            for ($j = 0; $j < count($rsDetailCol[$rs[$i]['pkey']]); $j++) {
                $rsDetailCol[$rs[$i]['pkey']][$j]['detailvolume'] = $obj->formatNumber($rsDetailCol[$rs[$i]['pkey']][$j]['length'] ). ' x ' .$obj->formatNumber( $rsDetailCol[$rs[$i]['pkey']][$j]['width']) . ' x ' . $obj->formatNumber($rsDetailCol[$rs[$i]['pkey']][$j]['height']);
            }


            $rsDetail = $rsDetailCol[$rs[$i]['pkey']];

            $rs[$i]['_detail_'] = array('arrTemplate' => $arrDetailTemplate, 'data' => $rsDetail);
        }

        $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }

    $obj->generateReport($_POST, $tempreport, $arrTemplate, array('dataToExport' => $dataToExport, 'rs' => $rs), $arrFilterInformation);
} else {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrSender = $class->convertForCombobox($customer->searchData($customer->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrCity = $class->convertForCombobox($city->searchData($city->tableName . '.statuskey', 1, true), 'pkey', 'name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrTransportation = $class->convertForCombobox($obj->getTransportationType(), 'pkey', 'name');


$arrTwigVar['inputFromCity'] =  $class->inputSelect('selFromCity[]', $arrCity, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputDestinationCity'] =  $class->inputSelect('selDestinationCity[]', $arrCity, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputTransportation'] =  $class->inputSelect('selTransportation[]', $arrTransportation, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelSender'] =  $class->inputSelect('SelSender[]', $arrSender, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelRecipient'] =  $class->inputSelect('SelRecipient[]', $arrSender, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputIsShowDetail'] =  $class->inputCheckBox('isShowDetail');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;
echo $twig->render('reportLogisticSalesOrder.html', $arrTwigVar);
