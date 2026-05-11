<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_include-portal.php';

includeClass(array("TruckingServiceOrderInvoice.class.php", "Item.class.php","AR.class.php", "Customer.class.php", "SalesOrderInvoiceReceipt.class.php"));
$truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
$item = new Item();
$customer = new Customer();
$ar = new AR();
$salesOrderInvoiceReceipt =  new SalesOrderInvoiceReceipt();

$truckingServiceOrderInvoice->oDbCon = $CUSTOMER_CONN;
$item->oDbCon = $CUSTOMER_CONN;
$customer->oDbCon = $CUSTOMER_CONN;
$ar->oDbCon = $CUSTOMER_CONN;
$salesOrderInvoiceReceipt->oDbCon = $CUSTOMER_CONN;

$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
$arrTwigVar['pageIndex'] =  $pageIndex;

$totalrowsperpage = 10; //$class->loadSetting('productTotalItemPerPage'); //sementara pakai ini dulu

$orderBy = ' order by trdate desc,pkey desc'; 
$now = $pageIndex * $totalrowsperpage;

$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$criteria =   ' and '.$truckingServiceOrderInvoice->tableName.'.statuskey in (2,3)'; // ini tetep ahrus ad karena status invoice
$criteria .=  ' and '.$truckingServiceOrderInvoice->tableName.'.customerkey = '.$class->oDbCon->paramString(USERKEY);

if(isset($_POST['txtSearch']) && !empty($_POST['txtSearch'])){
$key = $_POST['txtSearch'];
$criteria .=  ' and (
						'.$truckingServiceOrderInvoice->tableName.'.code like '.$class->oDbCon->paramString('%'.$key.'%').'
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
	$statuskeyCriteria = array(1,2,3); // 0 utk yg payment cash/pake DP semua
//
//// kalo AR lunas, masukin jg yg statusny 0 (gk ad AR, langsung cash / byr pake DP])
if(in_array(3,$statuskeyCriteria ))
	$nonARCriteria = ' or ' . $truckingServiceOrderInvoice->tableName.'.outstanding <=0 ';


$criteria .=   ' and ( '.$truckingServiceOrderInvoice->tableAR.'.statuskey in ('.$class->oDbCon->paramString($statuskeyCriteria,',').') '.$nonARCriteria.' )';

//$truckingServiceOrderInvoice->setLog($criteria,true);

// TGL
if(isset($_POST['chkDatePeriodFilter']) && $_POST['chkDatePeriodFilter'] == 1){
	$fromDate = (isset($_POST['trStartDatePeriod']) && !empty($_POST['trStartDatePeriod'])) ? : DEFAULT_EMPTY_DATE;
	$endDate = (isset($_POST['trEndDatePeriod']) && !empty($_POST['trEndDatePeriod'])) ? : DEFAULT_EMPTY_DATE;
	
	$criteria .=  ' and '.$truckingServiceOrderInvoice->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDatePeriod'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDatePeriod'],' / ','Y-m-d 23:59');
}


$rs = $truckingServiceOrderInvoice->searchData('','',true,$criteria,$orderBy,$limit);

$rsDetailCol = $truckingServiceOrderInvoice->getDetailCollections($rs,'refkey');    

$rsAllItemDetail = $truckingServiceOrderInvoice->getItemDetail(array_column($rs,'pkey'),'refheaderkey');
    
$arrItemKey = array_unique(array_column($rsAllItemDetail,'itemkey'));

// gk perlu, patokan dari nama alias di invoice aj
//$arrCustomerItemAliasName = $customer->getItemAliasDetail(USERKEY,array_column($rsAllItemDetail,'itemkey'));
//$arrCustomerItemAliasName = array_column($arrCustomerItemAliasName,'null','itemkey');

$rsAllItemDetailCol = $truckingServiceOrderInvoice->reindexDetailCollections($rsAllItemDetail,'refkey');
$totalPages = ceil( $truckingServiceOrderInvoice->getTotalRows($criteria) / $totalrowsperpage);    

// get receipt information
$rsReceipt = $salesOrderInvoiceReceipt->getInvoiceReceipt(array_column($rs,'pkey'),' and '.$salesOrderInvoiceReceipt->tableName.'.statuskey in (2,3) ');
$rsReceipt = array_column($rsReceipt,null,'invoicekey');

foreach($rs as $key=>$row){
	$recipientName = (isset($rsReceipt[$row['pkey']]) && !empty($rsReceipt[$row['pkey']]['recipientname'])) ? $rsReceipt[$row['pkey']]['recipientname'] : ''; 
	$rs[$key]['recipientname'] = $recipientName;
     
    $rsFileDetail = $truckingServiceOrderInvoice->getFileDetail($row['pkey']);
    $rs[$key]['file'] = array(); // local storage ketimpa, gpp karena kedepan semua wajib S3 biar gk berat
    foreach($rsFileDetail as $fileRow){
        $fileUrl = $truckingServiceOrderInvoice->createPresignedURL($domainName.'/'.$truckingServiceOrderInvoice->uploadFileFolder .$row['pkey'].'/'.$fileRow['file'],'+60 minutes');  
        array_push($rs[$key]['file'],array('fileName' => $fileRow['file'] ,'url' => $fileUrl));
    }

}

     
$arrTwigVar ['totalPages'] =  $totalPages;


$totalOutstanding = $ar->getAROutstanding(USERKEY);

$arrTwigVar['totalOutstanding'] =  $totalOutstanding;

$arrTwigVar['rsInvoice'] =   $rs;
$arrTwigVar['rsDetail'] =   $rsDetailCol;   
$arrTwigVar['rsItemDetail'] =   $rsAllItemDetailCol;  


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

echo $twig->render('invoice-trucking-list.html', $arrTwigVar);

?>