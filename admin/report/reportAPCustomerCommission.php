<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('AP.class.php', 'APCustomerCommission.class.php', 'Warehouse.class.php', 'Customer.class.php'));
$apCustomerCommission = createObjAndAddToCol(new APCustomerCommission());
$warehose = createObjAndAddToCol(new Warehouse());
$customer = createObjAndAddToCol(new Customer());

include '_global.php';

$obj = $apCustomerCommission;
$securityObject = 'reportAPCustomerCommission'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));
$_POST['selStatus[]'] = array(1,2); 

$arrFilterInformation = array();


//date
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'code', 'width' => "100px");
$arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "150px", 'format' => 'date');
$arrDataStructure['duedate'] = array('title' => ucwords($obj->lang['duedate']), 'dbfield' => 'duedate', 'width' => "150px", 'format' => 'date');
$arrDataStructure['refkey'] = array('title' => ucwords($obj->lang['reference']), 'dbfield' => 'refcode', 'width' => "90px");
$arrDataStructure['aptype'] = array('title' => ucwords($obj->lang['transactionType']), 'dbfield' => 'aptypename', 'width' => "150px");
$arrDataStructure['warehouse'] = array('title' => ucwords($obj->lang['warehouse']), 'dbfield' => 'warehousename', 'width' => "80px");
$arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'dbfield' => 'customername', 'width' => "150px");
$arrDataStructure['amount'] = array('title' => ucwords($obj->lang['amount']), 'dbfield' => 'amount', 'width' => "100px", 'format' => 'number');
$arrDataStructure['outstanding'] = array('title' => ucwords($obj->lang['outstanding']), 'dbfield' => 'outstanding', 'width' => "100px", 'format' => 'number');
$arrDataStructure['note'] = array('title' => ucwords($obj->lang['note']), 'dbfield' => 'trdesc', 'width' => "150px");
$arrDataStructure['statusname'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "70px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['apCustomerCommissionreport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';

    if (isset($_POST) && !empty($_POST['code'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code=' . $class->oDbCon->paramString($_POST['code']);
        array_push($arrFilterInformation, array("label" => $class->lang['code'], 'filter' =>  $_POST['code']));
    }
    if (isset($_POST) && !empty($_POST['refcode'])) {
        $criteria .= ' AND ' . $obj->tableName . '.refcode=' . $class->oDbCon->paramString($_POST['refcode']);
        array_push($arrFilterInformation, array("label" => $class->lang['reference'], 'filter' =>  $_POST['refcode']));
    }

    if (isset($_POST) && !empty($_POST['selWarehouse'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));

        $criteria .= ' AND ' . $obj->tableName . '.warehousekey in(' . $key . ')';

        $rsCriteria =  $warehose->searchDataRow(array($warehose->tableName . '.name'), 'and pkey in (' . $key . ')');
        $arrWarehose = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrWarehose, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrWarehose);
        array_push($arrFilterInformation, array("label" => $obj->lang['warehouse'], 'filter' => $statusName));
    }
    if (isset($_POST) && !empty($_POST['selApType'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selApType']));

        $criteria .= ' AND ' . $obj->tableName . '.aptype in(' . $key . ')';

        $rsCriteria =  $obj->getApType($key);
        $arrApType = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrApType, $rsCriteria[$k]['name']);

        $apTypeName = implode(", ", $arrApType);
        array_push($arrFilterInformation, array("label" => $obj->lang['transactionType'], 'filter' => $apTypeName));
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
    if (isset($_POST) && !empty($_POST['selCustomer'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));

        $criteria .= ' AND ' . $obj->tableName . '.customerkey in(' . $key . ')';
        $rsCriteria =  $customer->searchDataRow(array($customer->tableName . '.name'), 'and pkey in (' . $key . ')');
        $arrCustomer = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrCustomer, $rsCriteria[$k]['name']);

        $customerName = implode(", ", $arrCustomer);
        array_push($arrFilterInformation, array("label" => $obj->lang['customer'], 'filter' => $customerName));
    }

    if (isset($_POST) && !empty($_POST['trStartDate'])) {
        $criteria .= ' AND ' . $obj->tableName . '.trdate between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ', 'Y-m-d 23:59');
        array_push($arrFilterInformation, array("label" => $obj->lang['date'], 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
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
}

$arrApType = $class->generateComboboxOpt(array('data' => $obj->getApType(), 'label' => 'name'));
$arrWarehouse = $class->generateComboboxOpt(array('data' => $warehose->searchDataRow(array($warehose->tableName . '.name', $warehose->tableName . '.pkey')), 'label' => 'name'));
$arrCustomer = $class->generateComboboxOpt(array('data' => $customer->searchDataRow(array($customer->tableName . '.name', $customer->tableName . '.pkey',), 'and statuskey=1'), 'label' => 'name'));
$arrStatus = $class->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputRefCode'] =  $class->inputText('refcode');
$arrTwigVar['inputSelApTypee'] =  $class->inputSelect('selApType[]', $arrApType, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

//date
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
//date

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportAPCustomerCommission.html', $arrTwigVar);