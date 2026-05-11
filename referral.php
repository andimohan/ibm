<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

require_once '_include-customer-information.php';

includeClass(array('Customer.class.php','AP.class.php','APCustomerCommission.class.php','MembershipSubscription.class.php'));
$customer = new Customer();
$membershipSubscription = new MembershipSubscription();


$totalrowsperpage =  $class->loadSetting('productTotalItemPerPage'); //sementara pakai ini dulu

$rs = $customer->getDataRowById(USERKEY);

$tableKey = $class->getTableKeyAndObj($membershipSubscription->tableName,array('key'))['key'];

// GUEST

$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
$arrTwigVar['pageIndex'] =  $pageIndex;

$orderBy = '';
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

$criteria = ' and '.$customer->tableName.'.statuskey = 2 and  '.$customer->tableName.'.membershiplevel = 1 and '.$customer->tableName.'.referralkey = ' .  $class->oDbCon->paramString(USERKEY);
$rsUpcoming =  $customer->searchData('','',true,$criteria,$orderBy,$limit);  
$totalPages = ceil( $customer->getTotalRows($criteria) / $totalrowsperpage);

// AP COMMISSION	

$pageIndex =  (isset($_GET) && !empty($_GET['pageAP'])) ? $_GET['pageAP'] : 0;
$arrTwigVar['pageIndexAP'] =  $pageIndex;

$ap = new APCustomerCommission(); 


$criteria = ' and '.$ap->tableName.'.customerkey = ' .  $class->oDbCon->paramString(USERKEY).' 
									  and '.$ap->tableName.'.reftabletype = ' .$class->oDbCon->paramString($tableKey).' 
									  and '.$ap->tableName.'.statuskey in (1,2,3)';
$orderBy = '';
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

$rsAP = $ap->searchData('','',true, $criteria,$orderBy,$limit);  

$totalPagesAP = ceil( $ap->getTotalRows($criteria) / $totalrowsperpage);

$arrSOKey = array_column($rsAP,'refkey');


$rsMembershipSubscription = $membershipSubscription->searchData('','',true, ' and '.$membershipSubscription->tableName.'.pkey in ('.$class->oDbCon->paramString($arrSOKey,',').') ');  
$rsMembershipSubscription = array_column($rsMembershipSubscription,null,'pkey');

$totalAmount['outstanding']= 0;
$totalAmount['paid']= 0;

foreach($rsAP as $key=>$row){  
	$rsAP[$key]['referralname'] = $rsMembershipSubscription[$row['refkey']]['customername'];
 	$rsAP[$key]['membershipname'] = $rsMembershipSubscription[$row['refkey']]['membershiplevel'];
 
//	$totalAmount['paid']  += ($row['amount'] - $row['outstanding']) ;	 
//	$totalAmount['outstanding'] += $row['outstanding'] ;
}	

// hitung total outstanding daan pencairan
$rsAPOutstanding = $ap->searchData('','',true, $criteria);  
$summaryBalance=array();
$summaryBalance['outstanding'] =0;
$summaryBalance['paid'] =0;
foreach($rsAPOutstanding as $key=>$row){ 
 $summaryBalance['paid']  += ($row['amount'] - $row['outstanding']) ;	 
 $summaryBalance['outstanding'] += $row['outstanding'] ;
}	


// override sementara
$rs[0]['bankaccountname'] = $rs[0]['name'];
	
$arrTwigVar ['inputHidId'] =  $class->inputHidden('hidId');

$_POST['action'] ='update-bank-information';  
$_POST['bankName'] = $rs[0]['bankname'];
$_POST['bankAccountName'] = $rs[0]['bankaccountname'];
$_POST['bankAccountNumber'] = $rs[0]['bankaccountnumber'];
$_POST['taxid'] = $rs[0]['taxid'];
	
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
 
//$arrTwigVar ['inputName'] =  $class->inputText('name',array('readonly'=>true)); 
$arrTwigVar['inputBankName'] =  $class->inputText('bankName');
$arrTwigVar['inputBankAccountNumber'] =  $class->inputText('bankAccountNumber');
$arrTwigVar['inputBankAccountName'] =  $class->inputText('bankAccountName',array('readonly'=>true)); 
$arrTwigVar['inputNPWP'] =  $class->inputText('taxid'); 

$arrTwigVar['rsUpcoming'] = $rsUpcoming;
$arrTwigVar ['totalPages'] =  $totalPages; 
$arrTwigVar ['totalPagesAP'] =  $totalPagesAP; 

$arrTwigVar['rsAP'] = $rsAP;
$arrTwigVar['totalAmount'] = $totalAmount;
$arrTwigVar ['referralLink'] =  HTTP_HOST.'j='.$rs[0]['code']; 

$arrTwigVar['summaryBalance'] = $summaryBalance;

$arrTwigVar ['inputCurrentPassword'] =  $class->inputPassword('currentPassword'); 

$_POST['hidModifiedOn'] =  $rs[0]['modifiedon']; 
$arrTwigVar['hidModifiedOn'] = $class->inputHidden('hidModifiedOn'); 

$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['save']); 
 
array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 
echo $twig->render('referral.html', $arrTwigVar);

?>