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
$criteria = '';
$criteria .=  ' and '.$ar->tableName.'.customerkey = '.$class->oDbCon->paramString(USERKEY);

if(isset($_POST['txtSearch']) && !empty($_POST['txtSearch'])){
$key = $_POST['txtSearch'];
$criteria .=  ' and (
						'.$ar->tableName.'.code like '.$class->oDbCon->paramString('%'.$key.'%').' or
						'.$ar->tableName.'.refcode like '.$class->oDbCon->paramString('%'.$key.'%').'
					)'
			  ;
}

// STATUS AR
$statuskeyCriteria = array();

if(isset($_POST['hidStatusCriteria']) && !empty($_POST['hidStatusCriteria'])){
	$statuskeyCriteria = explode(',',$_POST['hidStatusCriteria']); 
}

// buat jaga2 kalo gk dicentang semua jg
if(empty($statuskeyCriteria))
	$statuskeyCriteria = array(1,2,3);

$criteria .=   ' and '.$ar->tableName.'.statuskey in ('.$class->oDbCon->paramString($statuskeyCriteria,',').')';

$rs = $ar->searchData('','',true,$criteria,$orderBy,$limit);
$rsPayment = $arPayment->getDetailPaymentByARKey(array_column($rs,'pkey'));
$rsPayment = $arPayment->reindexDetailCollections($rsPayment,'arkey');

foreach($rs as $key=>$arRow) 
	$rs[$key]['paymentHistory'] = (isset($rsPayment[$arRow['pkey']])) ? $rsPayment[$arRow['pkey']] : array();

$totalPages = ceil( $ar->getTotalRows($criteria) / $totalrowsperpage);  
    
$arrTwigVar ['totalPages'] =  $totalPages;

$totalOutstanding = $ar->getAROutstanding(USERKEY);


$arrTwigVar['rsAR'] =  $rs;
$arrTwigVar['totalOutstanding'] =  $totalOutstanding; 

$arrStatus = $ar->getAllStatus();
array_pop($arrStatus);

$arrChkStatus = array();
$arrPostStatuskey = (isset($_POST['hidStatusCriteria'])) ? explode(',',$_POST['hidStatusCriteria']) : array();
foreach($arrStatus as $row){
	$chkValue = (in_array($row['pkey'], $arrPostStatuskey)) ? 1:0;
	array_push($arrChkStatus, array('input' => $class->inputCheckBox('chkStatus[]',array('value' => $chkValue, 'etc' => 'attr="'. $row['pkey'] .'"')),'label' => $row['status']));
}

$arrTwigVar['inputChkStatus'] =  $arrChkStatus;
$arrTwigVar['getParameters'] = updateGetParameters();

echo $twig->render('ar-outstanding-list.html', $arrTwigVar);

?>