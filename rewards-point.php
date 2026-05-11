<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
 
$rs = $rewardsPoint->searchData('','',true,' and '.$rewardsPoint->tableName.'.statuskey > 1 and '.$rewardsPoint->tableName.'.customerkey = ' . $rewardsPoint->oDbCon->paramString(USERKEY),' order by trdate desc, pkey desc');
 
$totalPoint = 0;
for ($i=0;$i<count($rs);$i++){
	if ($rs[$i]['statuskey'] == 2 || $rs[$i]['statuskey'] == 3)
		$totalPoint += $rs[$i]['point'];
}

$arrTwigVar ['totalPoint'] =  $totalPoint;
$arrTwigVar ['rewardsPoint'] =  $rs;
 
echo $twig->render('rewards-point.html', $arrTwigVar);

?>
