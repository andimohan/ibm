<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass(array('Employee.class.php','EmployeeAttendance.class.php'));
$employeeAttendance = createObjAndAddToCol( new EmployeeAttendance()); 

$obj = $employeeAttendance;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
	 									// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'employeeAttendanceForm';

function generateQuickView($obj,$id){ 
	 
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>