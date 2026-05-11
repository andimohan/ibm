<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('MeetingSchedule.class.php', 'MeetingPoint.class.php'));
$meetingSchedule = createObjAndAddToCol(new MeetingSchedule());
$meetingPoint = createObjAndAddToCol(new MeetingPoint());


include '_global.php';

$obj = $meetingSchedule;
$securityObject = 'reportMeetingSchedule'; // the value of security object is manually inserted to handle 
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
$arrDataStructure['meetingDate'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "150px", 'format' => 'datetime');
$arrDataStructure['language'] = array('title' => ucwords($obj->lang['language']), 'dbfield' => 'languagename', 'width' => "150px");
$arrDataStructure['link'] = array('title' => ucwords($obj->lang['meetingLink']), 'dbfield' => 'meetinglink', 'width' => "250px");
$arrDataStructure['location'] = array('title' => ucwords($obj->lang['location']), 'dbfield' => 'meetingpointname', 'width' => "250px");
$arrDataStructure['meetingOnlineOffline'] = array('title' => ucwords($obj->lang['meetingType']), 'dbfield' => 'meetingonlineofflinename', 'width' => "150px");
$arrDataStructure['statusname'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "70px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['meetingScheduleReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();
array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';

    if (isset($_POST) && !empty($_POST['code'])) {
        $criteria .= ' AND ' . $obj->tableName . '.code LIKE (' . $class->oDbCon->paramString('%' . $_POST['code'] . '%') . ')';
        array_push($arrFilterInformation, array("label" => $class->lang['code'], 'filter' =>  $_POST['code']));
    }
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59'); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if (isset($_POST) && !empty($_POST['selOnlineOffline'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selOnlineOffline']));

        $criteria .= ' AND ' . $obj->tableName . '.meetingonlineoffline in(' . $key . ')';

        $rsCriteria =  $obj->getOnlineOfflineById($key);
        $arrOnlineOffline = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrOnlineOffline, $rsCriteria[$k]['name']);

        $OnlineOfflineName = implode(", ", $arrOnlineOffline);
        array_push($arrFilterInformation, array("label" => $obj->lang['meetingType'], 'filter' => $OnlineOfflineName));
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
    
    if (isset($_POST) && !empty($_POST['selMeetingPoint'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selMeetingPoint']));

        $criteria .= ' AND ' . $obj->tableName . '.locationkey in(' . $key . ')';

        $rsCriteria =  $meetingPoint->searchDataRow(array($meetingPoint->tableName . '.name', $meetingPoint->tableName . '.pkey'), 'and pkey in(' . $key . ')');

        $arrTempLocation = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempLocation, $rsCriteria[$k]['name']);

        $locationName = implode(", ", $arrTempLocation);
        array_push($arrFilterInformation, array("label" => $class->lang['location'], 'filter' => $locationName));
    }
    
    if (isset($_POST) && !empty($_POST['selLanguage'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selLanguage']));

        $criteria .= ' AND ' . $obj->tableName . '.language in(' . $key . ')';

        $rsCriteria =  $obj->getLanguageById($key);

        $arrTempLanguage = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempLanguage, $rsCriteria[$k]['language']);

        $LanguageName = implode(", ", $arrTempLanguage);
        array_push($arrFilterInformation, array("label" => $class->lang['language'], 'filter' => $LanguageName));
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
else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrOnlineOffline = $obj->generateComboboxOpt(array('data' => $meetingSchedule->getOnlineOffline(), 'label' => 'name')); //gini y iyaa ok 
$arrLanguage = $class->generateComboboxOpt(array('data' => $meetingSchedule->getLanguage(), 'label' => 'language'));
$arrMeetingPoint = $class->generateComboboxOpt(array('data' => $meetingPoint->searchDataRow(array($meetingPoint->tableName . '.pkey', $meetingPoint->tableName . '.name')), 'label' => 'name'));
$arrStatus = $class->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));

$arrTwigVar['inputCode'] =  $class->inputText('code');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputSelOnlineOffline'] =  $meetingSchedule->inputSelect('selOnlineOffline[]', $arrOnlineOffline, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelMeetingPoint'] =  $class->inputSelect('selMeetingPoint[]', $arrMeetingPoint, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelLanguage'] =  $class->inputSelect('selLanguage[]', $arrLanguage, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportMeetingSchedule.html', $arrTwigVar);
