<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  

$arrPaymentMethod = $class->convertForCombobox($paymentMethod->searchData('statuskey',1,true,' and useInPaymentConfirmation = 1'),'pkey','name');

$_POST['action'] ='generateInvoice';
$_POST['trDate'] = date('d / m / Y');

$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action');
$arrTwigVar ['inputHidInvoiceKey'] =  $class->inputHidden('hidInvoiceKey');


$arrTwigVar ['inputInvoice'] =  $class->inputText('invoiceId'); 
$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 

$arrTwigVar ['inputPaymentDate'] =  $class->inputDate('trDate'); 
$arrTwigVar ['inputAmount'] =  $class->inputNumber('amount'); 
$arrTwigVar ['inputPaymentAmount'] =  $class->inputNumber('paymentAmount'); 
$arrTwigVar ['inputBankName'] =  $class->inputText('bankName');  
$arrTwigVar ['inputBankBranch'] =  $class->inputText('bankBranch');  
$arrTwigVar ['inputBankAccountName'] =  $class->inputText('bankAccountName');  
$arrTwigVar ['inputBankAccountNumber'] =  $class->inputText('bankAccountNumber'); 
$arrTwigVar ['inputPaymentMethod'] = $class->inputSelect('selPaymentMethodKey', $arrPaymentMethod);  
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['send'] );
$arrTwigVar ['btnBack'] =   $class->inputButton('btnBack',$class->lang['transactionHistory'] );

 if (!empty(USERKEY)){
     $rsSalesOrder = $salesOrder->searchData('customerkey', USERKEY,true, ' and ' .$salesOrder->tableName.'.statuskey = 1', 'order by ' .$salesOrder->tableName.'.trdate desc');
     for($i=0;$i<count($rsSalesOrder);$i++){ 
        $rsSalesOrder[$i]['token'] = md5($rsSalesOrder[$i]['pkey'] . $rsSalesOrder[$i]['grandtotal'] . $class->secretKey);
     }
     $arrTwigVar ['rsSalesOrder'] = $rsSalesOrder; 
 }
    
echo $twig->render('payment-confirmation.html', $arrTwigVar);

?>
