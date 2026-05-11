<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';   

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

require_once '_include-customer-information.php';

includeClass(array('ActivityLog.class.php'));

$activityLog = new ActivityLog();

$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
$arrTwigVar['pageIndex'] =  $pageIndex;

$totalrowsperpage = 15; //$class->loadSetting('productTotalItemPerPage'); //sementara pakai ini dulu

$orderby = 'order by createdon desc';
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$criteria = ' and ('.$activityLog->tableName.'.refkey = '.$class->oDbCon->paramString(USERKEY).' or '.$activityLog->tableName.'.refkey = 0 )';
 
$rsActivity = $activityLog->searchData('','',true,$criteria,$orderby,$limit);
$totalActivity = count($rsActivity);
 

for($i=0;$i<$totalActivity;$i++){
    // standart GMT 7 di database 
    $rsActivity[$i]['createdon'] = $class->convertToLocalTimeZone($rsActivity[$i]['createdon'],7,$LOGIN_USER['gmt']);  
}

$totalPages = ceil( $activityLog->getTotalRows($criteria) / $totalrowsperpage);  

$activityLog->compileActivityLog($rsActivity);

$arrTwigVar['rsActivity'] =  $rsActivity;  
$arrTwigVar['totalPages'] =  $totalPages; 

array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 
  
echo $twig->render('activity-history.html', $arrTwigVar);

?>