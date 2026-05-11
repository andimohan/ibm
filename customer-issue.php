<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('SalesOrder.class.php','Item.class.php', 'Customer.class.php'));
$salesOrder = new SalesOrder();
$customer = new Customer();
$item = new Item();

if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
 
if(!isset($_GET) || empty($_GET['id'])){
	header("location: /");
	die;
}

$pkey = $_GET['id'];

$rsCustomer = $customer->getDataRowById(USERKEY);
$name = $rsCustomer[0]['name'];
$email = $rsCustomer[0]['email'];
$phone = $rsCustomer[0]['phone'];

$rsSalesOrder = $salesOrder->searchData($salesOrder->tableName.'.pkey',$pkey,true, ' and '.$salesOrder->tableName.'.customerkey = '.$salesOrder->oDbCon->paramString(USERKEY));
if(empty($rsSalesOrder)){
    header("location: /");
    die;
}
$code = $rsSalesOrder[0]['code'];

$rsSalesOrderDetail = $salesOrder->getDetailWithRelatedInformation(array_column($rsSalesOrder,'pkey'));
$rsSalesOrderDetail = $salesOrder->reindexDetailCollections($rsSalesOrderDetail,'refkey');

$rsOrderDetail = $rsSalesOrderDetail[$rsSalesOrder[0]['pkey']];

// $salesOrder->setLog($rsOrderDetail, true);

$_POST['action'] = 'add'; 
$_POST['hidSOKey'] =$_GET['id'];  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
$arrTwigVar ['inputHidId'] =  $class->inputHidden('hidSOKey');
$arrTwigVar ['rsOrderDetail'] =  $rsOrderDetail;
$arrTwigVar ['inputPurchaseCode'] =  $class->inputText('purchaseCode', array('value' => $code, 'readonly' => 'readonly')); 
$arrTwigVar ['inputName'] =  $class->inputText('name', array('value' => $name)); 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone', array('value' => $phone)); 
$arrTwigVar ['inputEmail'] =  $class->inputText('email', array('value' => $email)); 
$arrTwigVar ['inputSubject'] =  $class->inputText('subject'); 
$arrTwigVar ['inputIssue'] =   $class->inputTextArea('issue', array('etc' => 'style="height:10em"')); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['send']); 
$arrTwigVar ['PAGE_NAME'] =  $class->lang['reportProblem'];

echo $twig->render('customer-issue.html', $arrTwigVar);

?>
