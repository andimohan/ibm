<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 


$rsCustomerMembership = $customerMembership->searchData($customerMembership->tableName.'.customerkey ',USERKEY,true,'and '.$customerMembership->tableName.'.statuskey in(2,3)', 'order by '.$customerMembership->tableName.'.statuskey asc');
foreach($rsCustomerMembership as $key=>$row){ 
    $customermembershipkey = $row['pkey'];
    $rsAtendance = $membershipAttendance->searchData($membershipAttendance->tableName.'.customermembershipkey ',$customermembershipkey,true,'and '.$membershipAttendance->tableName.'.statuskey in (2,3)', ' order by trdate asc');
    $rsCustomerMembership[$key]['attendancedetails'] = $rsAtendance;
}

$arrTwigVar ['rsCustomerMembership'] =  $rsCustomerMembership;
echo $twig->render('attendance.html', $arrTwigVar);

?>