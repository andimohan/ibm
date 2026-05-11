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
$criteria = '';
$criteria .=  ' and '.$ap->tableName.'.supplierkey = '.$class->oDbCon->paramString($supplierkey);

if(isset($_POST['txtSearch']) && !empty($_POST['txtSearch'])){
$key = $_POST['txtSearch'];
$criteria .=  ' and (
						'.$ap->tableName.'.code like '.$class->oDbCon->paramString('%'.$key.'%').' or
						'.$ap->tableName.'.refinvoicecode like '.$class->oDbCon->paramString('%'.$key.'%').'
					)'
			  ;
}

// STATUS AP
$statuskeyCriteria = array();

if(isset($_POST['hidStatusCriteria']) && !empty($_POST['hidStatusCriteria'])){
	$statuskeyCriteria = explode(',',$_POST['hidStatusCriteria']); 
}

// buat jaga2 kalo gk dicentang semua jg
if(empty($statuskeyCriteria))
	$statuskeyCriteria = array(1,2,3);

$criteria .=   ' and '.$ap->tableName.'.statuskey in ('.$class->oDbCon->paramString($statuskeyCriteria,',').')';

$rs = $ap->searchData('','',true,$criteria,$orderBy,$limit);

$rsPayment = $apPayment->getDetailPaymentByAPKey(array_column($rs,'pkey'));
$rsPayment = $apPayment->reindexDetailCollections($rsPayment,'apkey');

foreach($rs as $key=>$arRow) 
	$rs[$key]['paymentHistory'] = (isset($rsPayment[$arRow['pkey']])) ? $rsPayment[$arRow['pkey']] : array();

$totalPages = ceil( $ap->getTotalRows($criteria) / $totalrowsperpage);  
    
$arrTwigVar ['totalPages'] =  $totalPages;

$totalOutstanding = $ap->getAPOutstanding($supplierkey);


$arrTwigVar['rsAP'] =  $rs;
$arrTwigVar['totalOutstanding'] =  $totalOutstanding; 

$arrStatus = $ap->getAllStatus();
array_pop($arrStatus);

$arrChkStatus = array();
$arrPostStatuskey = (isset($_POST['hidStatusCriteria'])) ? explode(',',$_POST['hidStatusCriteria']) : array();
foreach($arrStatus as $row){
	$chkValue = (in_array($row['pkey'], $arrPostStatuskey)) ? 1:0;
	array_push($arrChkStatus, array('input' => $class->inputCheckBox('chkStatus[]',array('value' => $chkValue, 'etc' => 'attr="'. $row['pkey'] .'"')),'label' => $row['status']));
}

$arrTwigVar['inputChkStatus'] =  $arrChkStatus;
$arrTwigVar['getParameters'] = updateGetParameters();

echo $twig->render('ap-outstanding-list.html', $arrTwigVar);

?>