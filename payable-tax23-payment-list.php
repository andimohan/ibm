<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_include-portal.php';

includeClass(array("APPayment.class.php","APPayableTax23Payment.class.php","Customer.class.php"));
$apPayableTax23Payment = new APPayableTax23Payment();
$customer = new Customer();

$apPayableTax23Payment->oDbCon = $CUSTOMER_CONN;
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
$criteria =   ' and '.$apPayableTax23Payment->tableName.'.statuskey in (2,3)';
$criteria .=  ' and '.$apPayableTax23Payment->tableName.'.supplierkey = '.$class->oDbCon->paramString($supplierkey);

if(isset($_POST['txtSearch']) && !empty($_POST['txtSearch'])){
$key = $_POST['txtSearch'];
$criteria .=  ' and (
						'.$apPayableTax23Payment->tableName.'.code like '.$class->oDbCon->paramString('%'.$key.'%').'
					)'
			  ;
}


// TGL
if(isset($_POST['chkDatePeriodFilter']) && $_POST['chkDatePeriodFilter'] == 1){
	$fromDate = (isset($_POST['trStartDatePeriod']) && !empty($_POST['trStartDatePeriod'])) ? : DEFAULT_EMPTY_DATE;
	$endDate = (isset($_POST['trEndDatePeriod']) && !empty($_POST['trEndDatePeriod'])) ? : DEFAULT_EMPTY_DATE;
	
	$criteria .=  ' and '.$apPayableTax23Payment->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDatePeriod'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDatePeriod'],' / ','Y-m-d 23:59');
}

$rs = $apPayableTax23Payment->searchData('','',true,$criteria,$orderBy,$limit);


// wajib S3 
// $rsItemFile = $apPayableTax23Payment->getItemFile(array_column($rs,'pkey'));
// $rsItemFile = $apPayableTax23Payment->reindexDetailCollections($rsItemFile,'refkey');

// sementara convert ke satu file dulu, meskipun skrg sudah bisa multiple file
foreach($rs as $key=>$row){ 
        
    $rsFileDetail = $apPayableTax23Payment->getFileDetail($row['pkey']);
    $rs[$key]['file'] = array(); // local storage ketimpa, gpp karena kedepan semua wajib S3 biar gk berat
    foreach($rsFileDetail as $fileRow){
        $fileUrl = $apPayableTax23Payment->createPresignedURL($domainName.'/'.$apPayableTax23Payment->uploadFileFolder .$row['pkey'].'/'.$fileRow['file'],'+60 minutes');  
        array_push($rs[$key]['file'],array('fileName' => $fileRow['file'] ,'url' => $fileUrl));
    }
    

    // $rs[$key]['file'] = (isset($rsItemFile[$row['pkey']])) ?  $rsItemFile[$row['pkey']][0]['file'] : '';
}


$rsAP = $apPayableTax23Payment->getDetailWithRelatedInformation(array_column($rs,'pkey'));
$rsAP = $apPayableTax23Payment->reindexDetailCollections($rsAP,'refkey');

foreach($rs as $key=>$row) 
	$rs[$key]['apDetail'] = (isset($rsAP[$row['pkey']])) ? $rsAP[$row['pkey']] : array();

$totalPages = ceil( $apPayableTax23Payment->getTotalRows($criteria) / $totalrowsperpage);  
    
$arrTwigVar ['totalPages'] =  $totalPages;

$arrTwigVar['rsAPPayment'] =  $rs;

$arrTwigVar['getParameters'] = updateGetParameters();

echo $twig->render('payable-tax23-payment-list.html', $arrTwigVar);

?>