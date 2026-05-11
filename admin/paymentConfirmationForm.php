<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $paymentConfirmation;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'paymentConfirmationList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$_POST['trDate'] = date('d / m / Y');
$_POST['paymentDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	  
    $rsSales = $salesOrder->getDataRowById($rs[0]['refkey']);
	$_POST['salesId'] = $rsSales[0]['code'] ;
    $_POST['hidSalesKey'] = $rsSales[0]['pkey'] ;   
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['paymentDate'] = $obj->formatDBDate($rs[0]['paymentdate'],'d / m / Y');
	$_POST['trDesc'] = $rs[0]['transdesc'];
	$_POST['amount'] = $obj->formatNumber($rs[0]['amount']);  
	$_POST['paymentAmount'] = $obj->formatNumber($rs[0]['paymentamount']);  
    
	$_POST['hidPaymentMethodKey'] = $rs[0]['paymentmethodkey'];
    
    $rsPaymentMethod = $paymentMethod->getDataRowById($rs[0]['paymentmethodkey']);
    $_POST['paymentMethodName'] = $rsPaymentMethod[0]['name'];
    
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
    $_POST['customerName'] = $rsCustomer[0]['name'];
        
	$_POST['bankName'] = $rs[0]['bankname'];
	$_POST['bankAccountName'] = $rs[0]['bankaccountname'];
	$_POST['bankAccountNumber'] = $rs[0]['bankaccountnumber']; 
	$_POST['bankBranch'] = $rs[0]['branch']; 
    
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrPaymentMethod = $class->convertForCombobox($paymentMethod->searchData('statuskey',1,true,' and useInPaymentConfirmation = 1'),'pkey','name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript">  
    
    jQuery(document).ready(function(){  
	 	 
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        
         var paymentConfirmation = new PaymentConfirmation(tabID);
    
         prepareHandler(paymentConfirmation);   

          var fieldValidation =  {code: {
                                        validators: {
                                                notEmpty: {  message: phpErrorMsg.code[1] }, 
                                        }
                                    },
                                    salesId: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.salesOrder[1]
                                            }, 
                                        }
                                    },  
                
                                    selPaymentMethodKey: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.paymentMethod[1]
                                            },  
                                        }
                                    }, 
                                  
//                                   customerName: { 
//                                        validators: {
//                                            notEmpty: {
//                                                message: phpErrorMsg.customer[1]
//                                            },  
//                                        }
//                                    }, 
                                
                                  
                                    paymentAmount: {
                                        validators: { 
                                            greaterThan: {
                                            value: 0,
                                            inclusive: false,
                                            separator: ',', 
                                            message: phpErrorMsg.amount[2]
                                            }
                                        }
                                    }, 

                                    bankAccountNumber: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.bankaccountnumber[1]
                                            }, 
                                        }
                                    }, 

                                    bankAccountName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.bankaccountname[1]
                                            }, 
                                        }
                                    }, 

                                    bankName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.bank[1]
                                            }, 
                                        }
                                    }
                                } ; 
          setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
 
       
});
			
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
         <?php prepareOnLoadDataForm($obj); ?>     
       
       <div class="div-table main-tab-table-2">
              <div class="div-table-row">
                    <div class="div-table-col">  
                  		   	<div class="div-tab-panel">  
                                    <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                        </div> 
                                    </div>   
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['soCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $salesOrder,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'salesId',
                                                                                                   'key' => 'hidSalesKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-sales-order.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'statuskey' => '(1)')
                                                                                                    ),
                                                                                'callbackFunction' => 'getTabObj().updateOrderInformation()'
                                                                            )
                                                                        );  
                                            ?>
                                        </div> 
                                     </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                                 <?php echo $obj->inputText('customerName', array('readonly' => true)); ?>
                                                 <?php echo $obj->inputHidden('hidCcustomerKey'); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputNumber('amount', array( 'readonly'=>true)); ?> 
                                        </div> 
                                     </div>
                                   
                            </div>    
                  </div>
                    <div class="div-table-col">
                            <div class="div-tab-panel">  
                                    <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['paymentInformation']); ?></div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['paymentDate']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputDate('paymentDate'); ?>   
                                        </div> 
                                     </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['totalPayment']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputNumber('paymentAmount'); ?> 
                                        </div> 
                                     </div>

                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['paymentMethod']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selPaymentMethod', $arrPaymentMethod); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankName']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('bankName'); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankAccountNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('bankAccountNumber'); ?> 
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankAccountName']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('bankAccountName'); ?>  
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['branch']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('bankBranch'); ?>  
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                     </div>
                        </div>
                    </div>
           </div>
      </div>     
 	   
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>