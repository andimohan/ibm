<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('LogisticSalesOrder.class.php'));
$logisticSalesOrder = createObjAndAddToCol(new LogisticSalesOrder());
$termOfPayment = createObjAndAddToCol(new TermOfPayment());
$customer = createObjAndAddToCol(new Customer());


include '_global.php';

$obj = $logisticSalesOrder;
$securityObject = 'reportLogisticSalesOrderManifest'; // sementara karena tidak pakai yg baru ,the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));
$_POST['selStatus[]'] = array(1);

$arrFilterInformation = array();

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['sttNumber']), 'dbfield' => 'code', 'width' => "150px");
$arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "100px", 'format' => 'date');
$arrDataStructure['recipientName'] = array('title' => ucwords($obj->lang['recipient']), 'dbfield' => 'recipientname', 'width' => "250px", "sortable" => false);
$arrDataStructure['bale'] = array('title' => ucwords($obj->lang['bale']), 'dbfield' => 'totalqty', 'width' => "80px", 'format' => 'number');
$arrDataStructure['weight'] = array('title' => ucwords($obj->lang['weight']) . '/ KG', 'dbfield' => 'totalweight', 'width' => "80px", 'format' => 'number', 'calculateTotal' => true);
$arrDataStructure['termofpayment'] = array('title' => ucwords($obj->lang['description']), 'dbfield' => 'termofpaymentname', 'width' => "100px", "sortable" => false);
$arrDataStructure['statusname'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "70px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['manifestReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';

    if (isset($_POST) && !empty($_POST['code'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code=' . $class->oDbCon->paramString($_POST['code']);
        array_push($arrFilterInformation, array("label" => $class->lang['sttNumber'], 'filter' =>  $_POST['code']));
    }

    if (isset($_POST) && !empty($_POST['trStartDate'])) {
        $criteria .= ' and trdate between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ', 'Y-m-d 23:59');
        array_push($arrFilterInformation, array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
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
    $tempreport = '';

    for ($i = 0; $i < count($rs); $i++) {

        
        $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }

    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation);
} else {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}
$arrRecipient = $class->convertForCombobox($customer->searchData($customer->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');
$arrStatus = $class->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputSelRecipient'] =  $class->inputSelect('SelRecipient[]', $arrRecipient, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportLogisticSalesOrderManifest.html', $arrTwigVar);
