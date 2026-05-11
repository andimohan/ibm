<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('AP.class.php','APCustomerCommission.class.php'));
$apCustomerCommission = createObjAndAddToCol( new APCustomerCommission()); 
$customer = createObjAndAddToCol( new Customer()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$currency = createObjAndAddToCol( new Currency()); 


$obj= $apCustomerCommission;
$apPayment = $obj->getPaymentObj();
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'apCustomerCommissionList'; 

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['dueDate'] = date('d / m / Y');
$_POST['trDate'] = date('d / m / Y');  

$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$rs = prepareOnLoadData($obj);  

$addAPType = array();

if (!empty($_GET['id'])){    
    
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ; 
	$_POST['trDesc'] = $rs[0]['trdesc']; 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['dueDate'] = $obj->formatDBDate($rs[0]['duedate'],'d / m / Y');
	$_POST['amount'] = $obj->formatNumber($rs[0]['amount'],2); 
	$_POST['outstanding'] = $obj->formatNumber($rs[0]['outstanding'],2); 
	$_POST['refcode'] =  $rs[0]['refcode'];
	$_POST['refcode2'] =  $rs[0]['refcode2'];
    $_POST['selWarehouse'] = $rs[0]['warehousekey'];
    $_POST['selAPType'] = $rs[0]['aptype']; 
	$_POST['selCurrency'] = $rs[0]['currencykey'];
    $_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'],-2) ;
    $_POST['amountIDR'] = $obj->formatNumber($rs[0]['amountidr']);
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
	$editCurrencyInactiveCriteria = ' or '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);
 
    //array_push($addAPType,$rs[0]['aptype']);
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrCurrency = $obj->convertForCombobox($currency->searchData ('','',true,' and ('.$currency->tableName.'.statuskey = 1' . $editCurrencyInactiveCriteria.')'),'pkey','name');  

$arrAPType =  $class->convertForCombobox($obj->getAPType(),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

<script type="text/javascript">  
	jQuery(document).ready(function(){  
        
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
			
		 var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
        
         var varConstant = {  
            tablekey : tablekey,
            CURRENCY : <?php echo json_encode(CURRENCY); ?>  
         };

         var apCommission = new APCommission(tabID,varConstant);
    
         prepareHandler(apCommission);   
        
         var fieldValidation =  {
                                     code: { 
                                            validators: {
                                                notEmpty: {
                                                    message: phpErrorMsg.code[1]
                                                }, 
                                            }
                                        },  
                                        supplierName: { 
                                            validators: {
                                                notEmpty: {
                                                    message:  phpErrorMsg.supplier[1]
                                                }, 
                                            }
                                        },   
                                     /*   amount: {
                                            validators: { 
                                                greaterThan: {
                                                    value: 0,
                                                    inclusive: false,
                                                    separator: ',', 
                                                    message: phpErrorMsg.amount[2]
                                                }
                                            }
                                        },  */
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
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?> </label> 
                                            <div class="col-xs-9"> 
                                                <?php echo $obj->inputText('refcode', array('readonly' => true)); ?> 
                                            </div> 
                                        </div>  
                                   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['transactionType']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputSelect('selAPType', $arrAPType); ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse); ?> 
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                    'url' => 'customerForm.php',
                                                                                                    'element' => array('value' => 'customerName',
                                                                                                           'key' => 'hidCustomerKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
                                                                                                ) 
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?>  
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['duedate']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('dueDate'); ?>   
                                        </div> 
                                    </div>
								    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?> / <?php echo ucwords($obj->lang['currencyRate']); ?></label> 
                                        <div class="col-xs-9  mnv-currency"> 
                                           <div class="flex">
                                               <div><?php  echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency')); ?></div>
                                               <div class="consume"><?php echo $obj->inputDecimal('currencyRate', array('class'=>'form-control inputnumber input-currency-rate')); ?></div>
                                           </div>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']); ?></label> 
                                        <div class="col-xs-9">  
                                           <?php echo $obj->inputNumber('amount'); ?> 
                                        </div> 
                                    </div> 
								   
								   <?php if (count($arrCurrency) > 1) {  ?>
<!--
										<div class="form-group">
											<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']." (IDR)"); ?></label> 
											<div class="col-xs-9"> 
											   <?php echo $obj->inputNumber('amountIDR', array('readonly' => true)); ?> 
											</div> 
										</div>
-->
                                   <?php }  ?>
								   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['outstanding']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputNumber('outstanding', array('readonly' => true)); ?> 
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
                    
                    <div class="div-table-col"> 
      						   <div class="div-tab-panel"> 
                                  <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['paymentDetail']); ?></div>
                                   <div class="div-table" style="width:100%">
                                        <div class="div-table-row"> 
                                             <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; width:150px;" > 
                                                <strong><?php echo ucwords($obj->lang['paymentCode']); ?></strong>
                                             </div>
                                            <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:center" > 
                                                <strong><?php echo ucwords($obj->lang['date']); ?></strong>
                                             </div>
                                             <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;" > 
                                                <strong><?php echo ucwords($obj->lang['amount']); ?></strong>
                                             </div>
                                        </div> 
                                             <?php 
                                             if (!empty($_GET['id'])){
                                                  $rsDetailPayment = $apPayment->getDetailPaymentByAPKey($_GET['id']);
                                                  for ($i=0;$i<count($rsDetailPayment);$i++){
                                                     $rsApPayment= $apPayment->getDataRowById($rsDetailPayment[$i]['refkey']);
                                                      if($rsApPayment[0]['statuskey'] == 2 || $rsApPayment[0]['statuskey'] == 3){
                                                          echo '
                                                         <div class="div-table-row"> 
                                                             <div class="div-table-col-5" style="border-bottom:1px solid #dedede;" > 
                                                                '.$rsApPayment[0]['code'].'
                                                             </div> 
                                                             <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:center" > 
                                                                '.$obj->formatDBDate($rsApPayment[0]['trdate']).'
                                                             </div> 
                                                             <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:right;" > 
                                                                '.$obj->formatNumber($rsDetailPayment[$i]['amount']).'
                                                             </div>
                                                         </div> 
                                                         '; 
                                                      }
                                                 }
                                             }
                                             ?>
                                   </div>      
                                </div>
                    </div>    
                </div>
        </div>    
                     
    <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
