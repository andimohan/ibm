<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('ILCMember.class.php'));
$ilcMember = createObjAndAddToCol(new ILCMember());


include '_global.php';

$obj = $ilcMember;
$securityObject = 'reportILCMember'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));
//$_POST['selStatus[]'] = array(1);

$arrFilterInformation = array();

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'code', 'width' => "150px");
$arrDataStructure['name'] = array('title' => ucwords($obj->lang['name']), 'dbfield' => 'name', 'width' => "100px");
$arrDataStructure['email'] = array('title' => ucwords($obj->lang['email']), 'dbfield' => 'email', 'width' => "150px");
$arrDataStructure['phone'] = array('title' => ucwords($obj->lang['phone']), 'dbfield' => 'mobile', 'width' => "150px");
$arrDataStructure['statusname'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "70px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = 'Laporan ILC Member';
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';

    if (isset($_POST) && !empty($_POST['code'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code LIKE (' . $class->oDbCon->paramString('%' . $_POST['code'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => $class->lang['code'], 'filter' =>  $_POST['code']));
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
    if (isset($_POST) && !empty($_POST['name'])) {
        $criteria .= ' AND ' . $obj->tableName . '.name LIKE (' . $class->oDbCon->paramString('%' . $_POST['name'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => $class->lang['name'], 'filter' =>  $_POST['name']));
    }
    if (isset($_POST) && !empty($_POST['email'])) {
        $criteria .= ' AND ' . $obj->tableName . '.email LIKE (' . $class->oDbCon->paramString('%' . $_POST['email'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => $class->lang['email'], 'filter' =>  $_POST['email']));
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

$arrStatus = $class->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputName'] =  $class->inputText('name');
$arrTwigVar['inputEmail'] =  $class->inputText('email');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportILCMember.html', $arrTwigVar);
?>