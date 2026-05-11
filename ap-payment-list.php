<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_include-portal.php';

includeClass(array("AP.class.php","APPayment.class.php","Customer.class.php"));
$ap = new AP();
$apPayment = new APPayment();
$customer = new Customer();

$ap->oDbCon = $CUSTOMER_CONN;
$apPayment->oDbCon = $CUSTOMER_CONN;
$customer->oDbCon = $CUSTOMER_CONN;

//cari koneksi supplierkey
$rsCust = $customer->getSupplierLink(USERKEY); 
$supplierkey = (isset($rsCust[0]['supplierkey']) && !empty($rsCust[0]['supplierkey'])) ? $rsCust[0]['supplierkey'] : 0;

$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
$arrTwigVar['pageIndex'] =  $pageIndex;

$totalrowsperpage =  $class->loadSetting('productTotalItemPerPage'); //sementara pakai ini dulu

$orderBy = ' order by trdate desc,pkey desc'; 
$now = $pageIndex * $totalrowsperpage;

$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$criteria =   ' and '.$apPayment->tableName.'.statuskey in (2,3)';
$criteria .=  ' and '.$apPayment->tableName.'.supplierkey = '.$class->oDbCon->paramString($supplierkey);

if(isset($_POST['txtSearch']) && !empty($_POST['txtSearch'])){
$key = $_POST['txtSearch'];
$criteria .=  ' and (
						'.$apPayment->tableName.'.code like '.$class->oDbCon->paramString('%'.$key.'%').'
					)'
			  ;
}


// TGL
if(isset($_POST['chkDatePeriodFilter']) && $_POST['chkDatePeriodFilter'] == 1){
	$fromDate = (isset($_POST['trStartDatePeriod']) && !empty($_POST['trStartDatePeriod'])) ? : DEFAULT_EMPTY_DATE;
	$endDate = (isset($_POST['trEndDatePeriod']) && !empty($_POST['trEndDatePeriod'])) ? : DEFAULT_EMPTY_DATE;
	
	$criteria .=  ' and '.$apPayment->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDatePeriod'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDatePeriod'],' / ','Y-m-d 23:59');
}

$rs = $apPayment->searchData('','',true,$criteria,$orderBy,$limit);
 
$rsAP = $apPayment->getDetailWithRelatedInformation(array_column($rs,'pkey'));
$rsAP = $apPayment->reindexDetailCollections($rsAP,'refkey');

$rsAPDP = $apPayment->getDownpaymentDetail(array_column($rs,'pkey'),'',false); 
$rsAPDP = $apPayment->reindexDetailCollections($rsAPDP,'refkey');

foreach($rs as $key=>$row){ 
	$rs[$key]['apDetail'] = (isset($rsAP[$row['pkey']])) ? $rsAP[$row['pkey']] : array();
	$rs[$key]['dpDetail'] = (isset($rsAPDP[$row['pkey']])) ? $rsAPDP[$row['pkey']] : array(); 
}

$totalPages = ceil( $apPayment->getTotalRows($criteria) / $totalrowsperpage);  
    
$arrTwigVar ['totalPages'] =  $totalPages;

$arrTwigVar['rsAPPayment'] =  $rs;

$arrTwigVar['getParameters'] = updateGetParameters();

echo $twig->render('ap-payment-list.html', $arrTwigVar);

?>