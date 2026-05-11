<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('Employee.class.php')); 
$employee = createObjAndAddToCol(new Employee());

include '_global.php';

$obj = $employee;
$securityObject = 'reportSecurityPrivileges'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$arrPrivilegesType = array(
    '' => '-----',
    '10' => $obj->lang['view'],
    '11' => $obj->lang['add'] ,
    '12' => $obj->lang['delete'],
    '1' => $obj->lang['changeStatus']
);


$arrFilterInformation = array();
$detailCriteria = '';
// $obj->setLog($security->generateSecurityObject(),true);
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "60px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['user'] = array('title' => ucwords($obj->lang['user']), 'width' => "200px", 'dbfield' => 'employeename', "sortable" => false);
$arrDataStructure['moduleName'] = array('title' => ucwords($obj->lang['moduleName']), 'width' => "200px", 'dbfield' => 'modulename', "sortable" => false);
$arrDataStructure['view'] = array('title' => ucwords($obj->lang['view']), 'width' => "100px", 'dbfield' => 'viewdata', "sortable" => false, 'align' => 'center');
$arrDataStructure['add'] = array('title' => ucwords($obj->lang['add'] . '/' .$obj->lang['edit']), 'width' => "100px", 'dbfield' => 'adddata', "sortable" => false, 'align' => 'center');
$arrDataStructure['delete'] = array('title' => ucwords($obj->lang['delete']), 'width' => "100px", 'dbfield' => 'deletedata', "sortable" => false, 'align' => 'center');
$arrDataStructure['changeStatus'] = array('title' => ucwords($obj->lang['changeStatus']), 'width' => "100px", 'dbfield' => 'changestatus', "sortable" => false);

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['securityPrivilegesReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';


    if (isset($_POST) && !empty($_POST['selUser'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selUser']));

        $criteria .= ' AND ' . $obj->tableSecurityAccess . '.userkey in(' . $key . ')';

        $rsCriteria = $employee->searchData('', '', true, ' and ' . $employee->tableName . '.pkey in (' . $key . ')');

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['user'], 'filter' => $statusName));

    }
    
    
    if (isset($_POST) && !empty($_POST['selSecurityObject'])) {
 
        $criteria .= ' AND ' . $obj->tableSecurityObject . '.pkey in(' . $class->oDbCon->paramString($_POST['selSecurityObject'],',') . ')';
  
        $rsCriteria = $security->getSecurityObject( $_POST['selSecurityObject']);
         
        
        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['modulename']);

        $statusName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['module'], 'filter' => $statusName));

    }

    if (isset($_POST) && !empty($_POST['selPrivilegesType'])) {
        $keys = [];
        switch ($_POST['selPrivilegesType']) {
            case '1':
                $keys = [1,2,3,4,5,6,7,8,9]; //status
                break;
            default:
                $keys = [$_POST['selPrivilegesType']];
                break;

        }
 
        $criteria .= ' AND ' . $obj->tableSecurityAccess . '.statuskey in (' . $class->oDbCon->paramString($keys, ',') . ')';

        array_push($arrFilterInformation, array("label" => "Jenis Hak Akses", 'filter' => $arrPrivilegesType[$_POST['selPrivilegesType']]));
    }


//    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'userkey'; // order by harus dr kolom yg terdaftar saja


//    $order = 'order by ' . $orderBy . ' ' . $orderType;
  
    $order = 'order by ' . $obj->tableEmployee . '.name  asc, '.$obj->tableSecurityObject.'.orderlist asc';
  
    
    $rsData = $obj->getDataSecurityPrivilegesForReport($criteria,$order);
    $rsData = $obj->reindexDetailCollections($rsData, 'indexkey');
    //$obj->setLog($rsData, true);

    $actionStatus = [10,11,12];
    $arrData = array();

    foreach($rsData as $key => $rsdata) {

        $viewdata = '-';
        $adddata = '-';
        $deletedata = '-';
        $changeStatus = [];
        $rsStatus = '';

        foreach($rsdata as $data) {

             foreach ($rsdata as $data) {
                // Periksa adddata, editdata, dan deletedata
                if ($data['viewdata'] ==  1) $viewdata = 'Ya';
                if ($data['adddata'] == 1) $adddata = 'Ya';
                if ($data['deletedata'] == 1) $deletedata = 'Ya';

                // Simpan statuskey ke changeStatus jika tidak ada dalam $actionStatus
                if (!in_array($data['statuskey'], $actionStatus)) {
                    $changeStatus[] = $data['statuskey'];
                }
            }
        }

        
        if($rsdata[0]['modulestatus'] !== 'view_only') {
            $rsStatus = $security->getAllStatus($rsdata[0]['modulestatus']);
        }

        $arrData[] = [
            'indexkey' => $rsdata[0]['indexkey'],
            'userkey' => $rsdata[0]['userkey'],
            'employeename' => $rsdata[0]['employeename'],
            'modulecode' => $rsdata[0]['modulecode'],
            'modulename' => $rsdata[0]['modulename'],
            'modulestatus' => $rsdata[0]['modulestatus'],
            'viewdata' => $viewdata,
            'adddata' => $adddata,
            'deletedata' => $deletedata,
            'arrstatuskey' => $changeStatus,
            'arrstatus' => $rsStatus
        ];

    }
    
    //$obj->setLog($arrData, true);
    $rs  = array();

    $rs = (!empty($arrData) ?  $arrData : array());

    $tempreport = ''; 

    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="' . count($arrHeaderTemplate['dataStructure']) . '"></td></tr>';

    for ($i = 0; $i < count($rs); $i++) {

        $rsStatus = $rs[$i]['arrstatus'];

        $statusData = [];
        foreach($rsStatus as $status) {
            if(in_array($status['pkey'], $rs[$i]['arrstatuskey'])) {
                array_push($statusData, $status['status']);
            }
        }
        $rs[$i]['changestatus'] = implode(',<br>',$statusData);

        $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }

    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);

} else {

}

$arrUser = $class->convertForCombobox($obj->searchData($obj->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');


$rsSecurityObject = $security->generateSecurityObject();
$arrSecurityObject =  $class->convertForCombobox($rsSecurityObject,'pkey','modulename');


$arrTwigVar['inputSelUser'] = $class->inputSelect('selUser[]', $arrUser, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelPrivilegesType'] = $class->inputSelect('selPrivilegesType', $arrPrivilegesType);
$arrTwigVar['inputSelSecurityObject'] = $class->inputSelect('selSecurityObject[]', $arrSecurityObject, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;
echo $twig->render('reportSecurityPrivileges.html', $arrTwigVar);
?>