<?php
include '../../_config.php';
include '../../_include-v2.php'; 

includeClass('TruckingServiceWorkOrder.class.php');
$truckingServiceWorkOrder = createObjAndAddToCol( new TruckingServiceWorkOrder());
$employee = createObjAndAddToCol(new Employee());


include '_global.php';

$obj = $truckingServiceWorkOrder;
$securityObject = 'reportCostByDriver'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$_POST['selStatus[]'] = array(2, 3);

$arrFilterInformation = array();
$detailCriteria = '';
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['recipient'] = array('title' => ucwords($obj->lang['recipient']), 'width' => "180px", 'dbfield' => 'employeename');
$arrDataStructure['transactionCode'] = array('title' => ucwords($obj->lang['code']), 'width' => "140px", 'dbfield' => 'code'); 
$arrDataStructure['transactionDate'] = array('title' => ucwords($obj->lang['date']), 'width' => "100px", 'dbfield' => 'trdate','align' => 'center', 'format'=>'date'); 
$arrDataStructure['costName'] = array('title' => ucwords($obj->lang['costName']), 'width' => "200px", 'dbfield' => 'costname');
$arrDataStructure['cost'] = array('title' => ucwords($obj->lang['request']), 'width' => "120px", 'dbfield' => 'requestamount', 'align' => 'right', 'format' => 'number','calculateTotal'=>true);
$arrDataStructure['realization'] = array('title' => ucwords($obj->lang['realization']), 'width' => "120px", 'dbfield' => 'amount', 'align' => 'right', 'format' => 'number','calculateTotal'=>true);

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['costReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])) {

	$criteria = '';
    $employeeCriteria = '';

    if (isset($_POST) && !empty($_POST['trStartDate'])) {
        $criteria .= ' and  '. $obj->tableDriverCost . '.trdate between ' . $class->oDbCon->paramDate($_POST['trStartDate'], ' / ') . ' AND ' . $class->oDbCon->paramDate($_POST['trEndDate'], ' / ');
        array_push($arrFilterInformation, array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate']));
    }

    if (isset($_POST) && !empty($_POST['selEmployee'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selEmployee']));
        $employeeCriteria .= ' AND  employeekey in(' . $key . ')'; // gk perlu ad nama table

        $rsCriteria = $employee->searchData('', '', true, ' and ' . $employee->tableName . '.pkey in (' . $key . ')');

        $arrTempEmployee = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempEmployee, $rsCriteria[$k]['name']);

        $employeeName = implode(", ", $arrTempEmployee);
        array_push($arrFilterInformation, array("label" => $obj->lang['recipient'], 'filter' => $employeeName));

    }

// karena status kedua table berbeda
    // if (isset($_POST) && !empty($_POST['selStatus'])) {
    //     $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));
    //     $criteria .= ' AND ' . $obj->tableName . '.statuskey in(' . $key . ')';
    //     $rsCriteria = $obj->getStatusById($key);
    //     $arrTempStatus = array();
    //     for ($k = 0; $k < count($rsCriteria); $k++)
    //         array_push($arrTempStatus, $rsCriteria[$k]['status']);
    //     $statusName = implode(", ", $arrTempStatus);
    //     array_push($arrFilterInformation, array("label" => 'Status', 'filter' => $statusName));
    // }

    if (isset($_POST) && !empty($_POST['transactionCode'])) {
        $criteria .= ' AND ' . $obj->tableDriverCost . '.code LIKE (' . $class->oDbCon->paramString('%' . $_POST['transactionCode'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => $class->lang['transactionCode'], 'filter' => $_POST['transactionCode']));
    }

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by ' . $orderBy . ' ' . $orderType;

    $rs = $obj->getDataForCostByDriverReport($employeeCriteria, $criteria, $order);
    $tempreport = '';
    // ============================= GENERATE DATA ============================= 

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


$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
//$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputTransactionCode'] = $class->inputText('transactionCode'); 
$arrTwigVar['inputSelEmployee'] = $class->inputSelect('selEmployee[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));


$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;
echo $twig->render('reportCostByDriver.html', $arrTwigVar);

?>
