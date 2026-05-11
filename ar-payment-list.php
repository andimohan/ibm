<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_include-portal.php';

includeClass(array("AR.class.php","ARPayment.class.php"));
$ar = new AR();
$arPayment = new ARPayment();

$ar->oDbCon = $CUSTOMER_CONN;
$arPayment->oDbCon = $CUSTOMER_CONN;


$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
$arrTwigVar['pageIndex'] =  $pageIndex;

$totalrowsperpage =  $class->loadSetting('productTotalItemPerPage'); //sementara pakai ini dulu

$orderBy = ' order by trdate desc,pkey desc'; 
$now = $pageIndex * $totalrowsperpage;

$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$criteria =   ' and '.$arPayment->tableName.'.statuskey in (2,3)';
$criteria .=  ' and '.$arPayment->tableName.'.customerkey = '.$class->oDbCon->paramString(USERKEY);

if(isset($_POST['txtSearch']) && !empty($_POST['txtSearch'])){
$key = $_POST['txtSearch'];
$criteria .=  ' and (
						'.$arPayment->tableName.'.code like '.$class->oDbCon->paramString('%'.$key.'%').'
					)'
			  ;
}


// TGL
if(isset($_POST['chkDatePeriodFilter']) && $_POST['chkDatePeriodFilter'] == 1){
	$fromDate = (isset($_POST['trStartDatePeriod']) && !empty($_POST['trStartDatePeriod'])) ? : DEFAULT_EMPTY_DATE;
	$endDate = (isset($_POST['trEndDatePeriod']) && !empty($_POST['trEndDatePeriod'])) ? : DEFAULT_EMPTY_DATE;
	
	$criteria .=  ' and '.$arPayment->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDatePeriod'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDatePeriod'],' / ','Y-m-d 23:59');
}

$rs = $arPayment->searchData('','',true,$criteria,$orderBy,$limit);
$rsAR = $arPayment->getDetailWithRelatedInformation(array_column($rs,'pkey'));
$rsAR = $arPayment->reindexDetailCollections($rsAR,'refkey');

$rsARDP = $arPayment->getDownpaymentDetail(array_column($rs,'pkey')); 
$rsARDP = $arPayment->reindexDetailCollections($rsARDP,'refkey');

foreach($rs as $key=>$row) { 
	$rs[$key]['arDetail'] = (isset($rsAR[$row['pkey']])) ? $rsAR[$row['pkey']] : array();
	$rs[$key]['dpDetail'] = (isset($rsARDP[$row['pkey']])) ? $rsARDP[$row['pkey']] : array(); 
}

$totalPages = ceil( $arPayment->getTotalRows($criteria) / $totalrowsperpage);  
    
$arrTwigVar ['totalPages'] =  $totalPages;

$arrTwigVar['rsARPayment'] =  $rs; 

$arrTwigVar['getParameters'] = updateGetParameters();

echo $twig->render('ar-payment-list.html', $arrTwigVar);

?>