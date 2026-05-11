<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass('MeetingPoint.class.php');
$meetingpoint = createObjAndAddToCol(new MeetingPoint());

include '_global.php';

$obj = $meetingpoint;
$securityObject = 'ReportMeetingPoint'; // the value of security object is manually inserted to handle 
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
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'code', 'width' => "150px");
$arrDataStructure['name'] = array('title' => ucwords($obj->lang['name']), 'dbfield' => 'name', 'width' => "250px");
$arrDataStructure['address'] = array('title' => ucwords($obj->lang['address']), 'dbfield' => 'address', 'width' => "300px");
$arrDataStructure['statusname'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "150px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['meetingPoint'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';

    if (isset($_POST) && !empty($_POST['name'])) {
        $criteria .= ' AND ' . $obj->tableName . '.name LIKE (' . $class->oDbCon->paramString('%' . $_POST['name'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => $class->lang['name'], 'filter' =>  $_POST['name']));
    }
    if (isset($_POST) && !empty($_POST['address'])) {
        $criteria .= ' AND ' . $obj->tableName . '.address LIKE (' . $class->oDbCon->paramString('%' . $_POST['address'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => $class->lang['address'], 'filter' =>  $_POST['address']));
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


$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrTwigVar['inputName'] =  $class->inputText('name');
$arrTwigVar['inputAddress'] =  $class->inputText('address');
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportMeetingPoint.html', $arrTwigVar);
