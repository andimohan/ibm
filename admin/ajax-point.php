<?php 
require_once '../_config.php';  
require_once '../_include-v2.php';  

includeClass('RewardsPoint.class.php');
$rewardsPoint = createObjAndAddToCol(new RewardsPoint());

$obj = $rewardsPoint;   

include 'ajax-general.php';
 
 

if (isset($_GET) && !empty($_GET['action'])) {
          
	switch ($_GET['action']){  
		case 'getPointValue' :  
			$point = ($_GET['point']) ? ($_GET['point']) : 0;
			$pointValue = $obj->loadSetting('rewardsPointUnitValue');
		 
			echo ($point * $pointValue); 
			break;  
	}

} 

die;
  
?>