<?php 
// untuk Paus

require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass(array('EmployeeCommission.class.php'));

$employeeCommission = new EmployeeCommission(); 
$obj = $employeeCommission; 
$rsData = $obj->generateEmployeeCommission();

die ("die");


echo 'Done';

?>