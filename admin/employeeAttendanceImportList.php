<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass(array('Employee.class.php','EmployeeAttendanceImport.class.php'));
$employeeAttendanceImport = createObjAndAddToCol( new EmployeeAttendanceImport()); 

$obj = $employeeAttendanceImport;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
	 									// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'employeeAttendanceImportForm';

function generateQuickView($obj,$id){ 
	 
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>