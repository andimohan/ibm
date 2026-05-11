<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array("JobApplication.class.php"));
$jobApplication = new JobApplication(); 

	foreach ($_POST as $k => $v) { 
		if (!is_array($v))
			 $v = trim($v);  
		
		$arr[$k] = $v;     
	}  
 
	$arrReturn = array(); 
	$arr['code'] = 'XXXXX';
	$arr['hidRefJobOpportunity'] = $_POST['hidRefJobOpportunity'];
	$arr['name']  = $_POST['name'];
	$arr['email'] = $_POST['email'];
	$arr['phone'] = $_POST['phone'];
	$arr['selSex'] = $_POST['selSex'];
	$arr['address'] = $_POST['address'];

	$arr['hidDetailitem-file-uploaderKey'] = array('');
	$arr['hidNameitem-file-uploader'] = explode(',',$_POST['item-file-uploader']);
	$arr['item-file-uploader'] = $_POST['item-file-uploader'];
    $arr['token-item-file-uploader'] = $_POST['token-item-file-uploader'];
  	$arr['portfolioURL'] =  $_POST['portfolioURL'];
	$arr['latestRole'] = $_POST['latestRole'];
	$arr['latestCompany'] = $_POST['latestCompany']; 
	$arr['trStartDate'] = '01 / '.$_POST['selStartMonth'].' / '. $_POST['selStartYear'];
	$arr['trEndDate'] = '01 / '.$_POST['selEndMonth'].' / '. $_POST['selEndYear'];
	$arr['selReference'] = $_POST['selReference']; 
	$arr['selConsideration'] = $_POST['selConsideration']; 
	$arr['chkStillWork'] =  $_POST['chkStillWork']; 
    $arr['createdBy'] = 0;
	$arr['selStatus'] = 1;

	$arrReturn = $jobApplication->addData($arr);

	echo json_encode($arrReturn);  
	die; 
	
?>