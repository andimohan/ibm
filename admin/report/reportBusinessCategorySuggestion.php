<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('BusinessCategorySuggestion.class.php', 'Customer.class.php'));
$businessCategorySuggestion = createObjAndAddToCol(new BusinessCategorySuggestion());
$customer = createObjAndAddToCol(new Customer());
$city = createObjAndAddToCol(new City());

include '_global.php';

$obj = $businessCategorySuggestion;
$securityObject = 'ReportBusinessCategorySuggestion'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();

$_POST['selStatus[]'] = array(1,2,3);

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'code', 'width' => "100px");
$arrDataStructure['customer'] = array('title' => ucwords($obj->lang['customer']), 'dbfield' => 'customername', 'width' => "150px");
$arrDataStructure['category'] = array('title' => ucwords($obj->lang['category']), 'dbfield' => 'name', 'width' => "150px");
$arrDataStructure['descriptionPoint'] = array('title' => ucwords($obj->lang['descriptionPoint']), 'dbfield' => 'description', 'width' => "200px");
$arrDataStructure['statusname'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['businessCategorySuggestionReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';

    if (isset($_POST) && !empty($_POST['code'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code like ('.$class->oDbCon->paramString('%'.$_POST['code'].'%').')';
        array_push($arrFilterInformation, array("label" => $class->lang['code'], 'filter' =>  $_POST['code']));
    }
    if (isset($_POST) && !empty($_POST['category'])) {
        $criteria .= ' AND ' . $obj->tableName . '.name LIKE (' . $class->oDbCon->paramString('%' . $_POST['category'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => $class->lang['category'], 'filter' =>  $_POST['category']));
    }

    if (isset($_POST) && !empty($_POST['selCustomer'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));

        $criteria .= ' AND ' . $obj->tableName . '.customerkey in(' . $key . ')';

        $rsCriteria =  $customer->searchDataRow(array($customer->tableName . '.name'), 'and ' . $customer->tableName . '.pkey in (' . $key . ')');
        $arrCustomer = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrCustomer, $rsCriteria[$k]['name']);

        $customerName = implode(", ", $arrCustomer);
        array_push($arrFilterInformation, array("label" => $class->lang['customer'], 'filter' => $customerName));
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



    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';


    $order = 'order by ' . $orderBy . ' ' . $orderType;
    $rs = $obj->searchData('', '', true, $criteria, $order);
    $tempreport = '';

    for ($i = 0; $i < count($rs); $i++) {

        $arrHeaderStyle = array();
        if ($rs[$i]['statuskey'] == 2) {
            foreach ($arrDataStructure as $key => $row)
                $arrHeaderStyle[$key]['textColor'] = 'C41E3A';
        }


        $return = $obj->formatReportRows(array('data' => $rs[$i], 'style' => $arrHeaderStyle), $arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
    }

    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation);
}


$arrCustomer = $class->generateComboboxOpt(array('data' => $customer->searchDataRow(array($customer->tableName . '.name', $customer->tableName . '.pkey',), 'and ' . $customer->tableName . '.statuskey =1'), 'label' => 'name'));
$arrStatus = $class->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputCategory'] =  $class->inputText('category');
$arrTwigVar['inputCustomer'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportBusinessCategorySuggestion.html', $arrTwigVar);
