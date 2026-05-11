<?php

include '../../_config.php';
include '../../_include-v2.php';

includeClass(array('EmployeeAttendance.class.php')); 

$employeeAttendance = createObjAndAddToCol(new EmployeeAttendance());
$employee = createObjAndAddToCol(new Employee());

$obj = $employeeAttendance;
include '_global.php';
$securityObject = 'ReportEmployeeAttendance'; // the value of security object is manually inserted to handle 

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class

if (!$security->isAdminLogin($securityObject, 10, true));

$_POST['selStatus[]'] = array(1,2,3);
$arrStatus = $obj->getAllStatus();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title' => ucwords($obj->lang['code']), 'dbfield' => 'employeecode', 'width' => "120px");
$arrDataStructure['attendanceID'] = array('title' => ucwords($obj->lang['attendanceID']), 'dbfield' => 'attendanceid', 'width' => "120px");
$arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "120px", "format" => 'date');
$arrDataStructure['employee'] = array('title' => ucwords($obj->lang['employee']), 'dbfield' => 'employeename', 'width' => "200px");
$arrDataStructure['workDays'] = array('title' => ucwords($obj->lang['workDays']), 'dbfield' => 'totalworkdays', 'width' => "120px", 'format' => 'number');
$arrDataStructure['lateDays'] = array('title' => ucwords($obj->lang['total'] . ' ' . $obj->lang['lateDays']), 'dbfield' => 'totallatedays', 'width' => "120px", 'format' => 'number');
$arrDataStructure['lateFine'] = array('title' => ucwords($obj->lang['cut']), 'dbfield' => 'totallatefine', 'width' => "120px", 'align' => 'right', 'format' => 'number', 'calculateTotal' => true);
$arrDataStructure['halfDay'] = array('title' => ucwords($obj->lang['total'] . ' ' . $obj->lang['halfDay']), 'dbfield' => 'totalhalfday', 'width' => "120px", 'align' => 'right', 'format' => 'number');
//$arrDataStructure['halfDayFine'] = array('title' => ucwords($obj->lang['halfDayFine']), 'dbfield' => 'totalhalffine', 'width' => "120px", 'align' => 'right', 'format' => 'number', 'calculateTotal' => true);
$arrDataStructure['status'] = array('title' => ucwords($obj->lang['status']), 'dbfield' => 'statusname', 'width' => "100px");


$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['employeeAttendanceReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// ===== END FOR EXPORT SECTION   
$arrStatus = $class->convertForCombobox($arrStatus, 'pkey', 'status');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName . '.statuskey', 2, true, '', 'order by name asc'), 'pkey', 'name');

if (isset($_POST) && !empty($_POST['hidAction'])) {

   $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
   $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

   if (empty($_POST['hidRs'])) {
      $result = queryNewReport(get_defined_vars(), array('orderBy' => $orderBy, 'orderType' => $orderType));
      $rs = $result['rs'];
      $arrFilterInformation = $result['arrFilterInformation'];
   } else {
      $hidRs = json_decode($_POST['hidRs'], true);
      foreach ($hidRs as $key => $row)
         $$key = $hidRs[$key];

      //$arrFilterInformation = $hidRs['arrFilterInformation']; 
      $obj->mknatsort($rs, $orderBy, ($orderType == 'asc') ? false : true, true);
   }

   // ============================= GENERATE DATA ============================= 

   $tempreport = '';
   for ($i = 0; $i < count($rs); $i++) {

      $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);

      // ===== FOR EXPORT SECTION 
      array_push($dataToExport, $return['data']);
      // ===== END FOR EXPORT SECTION

      $tempreport .= $return['html'];

      // count subtotal for each col
      $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

   }

   $obj->generateReport($_POST, $tempreport, $arrTemplate, array('dataToExport' => $dataToExport, 'rs' => $rs), $arrFilterInformation);

} else {
   $_POST['trStartDate'] = date('d / m / Y');
   $_POST['trEndDate'] = date('d / m / Y');
}


$arrTwigVar['inputSelEmployee'] = $class->inputSelect('selEmployee[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
//filter periode
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

echo $twig->render('reportEmployeeAttendance.html', $arrTwigVar);

function queryNewReport($varCol = array(), $order)
{
   global $service;
   global $employee;
   foreach ($varCol as $key => $row) $$key = $varCol[$key];

   $criteria = '';
   $arrFilterInformation = array();
   $criteriaArr = array();

   array_push($criteriaArr, array('postVariable' => 'selEmployee',
      'fieldName' => $obj->tableName . '.employeekey',
      'label' => $obj->lang['employee'],
      'useArrayKey' => array('obj' => $employee)));

   //filter periode
   array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'),
      'fieldName' => $obj->tableName . '.trdate',
      'label' => $obj->lang['date'],
      'type' => 'daterange'));

   array_push($criteriaArr, array('postVariable' => 'selStatus',
      'type' => 'status'));


 
   $obj->createReportCriteria($criteria, $arrFilterInformation, $criteriaArr);
   $order = 'order by ' . $order['orderBy'] . ' ' . $order['orderType'];
   $rs = $obj->searchData('', '', true, $criteria, $order);

   $arrEmployeeKeys = array_column($rs, 'employeekey');
   $rsEmployee = $employee->searchData('', '', true, ' and ' . $employee->tableName.'.pkey in ('. $obj->oDbCon->paramString($arrEmployeeKeys, ',') .') ');
   $rsEmployee = array_column($rsEmployee,null,'pkey');
	   
   //manipulasi sebeum di proses/tampilkan 
   for ($i = 0; $i < count($rs); $i++) {
         $pkey = $rs[$i]['pkey'];

         $arrEmployee = $rsEmployee[$rs[$i]['employeekey']];

         $rs[$i]['employeecode'] = $arrEmployee['code'];
         $rs[$i]['attendanceid'] = $arrEmployee['attendanceid'];

         
         $rsHalfDay = $obj->getHalfDayDetail($pkey);
         $totalHalfFine = 0;
         for ($j = 0; $j < count($rsHalfDay); $j++) {
            $totalHalfFine += $rsHalfDay[$j]['latefine'];
         }

         $rs[$i]['totalhalffine'] = $totalHalfFine;

   }

   return array(
      'arrFilterInformation' => $arrFilterInformation,
      'rs' => $rs
   );
}

?>