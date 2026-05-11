<?php 

require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass('AREmployee.class.php');
$arEmployee = createObjAndAddToCol( new AREmployee()); 
$employee = createObjAndAddToCol( new Employee()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer()); 
$truckingCostCashOut = createObjAndAddToCol( new TruckingCostCashOut()); 
$truckingServiceOrder = createObjAndAddToCol( new TruckingServiceOrder()); 
$truckingServiceWorkOrder = createObjAndAddToCol( new TruckingServiceWorkOrder()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 
$cashBank = createObjAndAddToCol( new CashBank());

$obj= $arEmployee;
$arPayment = $obj->getPaymentObj();
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    
$formAction = 'arEmployeeList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$_POST['dueDate'] = date('d / m / Y');
$_POST['trDate'] = date('d / m / Y');

$editWarehouseInactiveCriteria = ''; 
$editPaymentMethodInactiveCriteria = '';
$arAvailableType = array(AR_EMPLOYEE_TYPE['personalLoan']);
$arrAvailableVoucher = array();
$rsAREmployeeMethodDetail = array();


$rs = prepareOnLoadData($obj);

if (!empty($_GET['id'])){    
    
    $id = $_GET['id'];
	$rsCustomer = $employee->getDataRowById($rs[0]['customerkey']);
	$_POST['employeeName'] = $rsCustomer[0]['name'] ;
	$_POST['hidEmployeeKey'] = $rsCustomer[0]['pkey'] ; 
	$_POST['trDesc'] = $rs[0]['trdesc']; 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['dueDate'] = $obj->formatDBDate($rs[0]['duedate'],'d / m / Y');
	$_POST['amount'] = $obj->formatNumber($rs[0]['amount']); 
	$_POST['outstanding'] = $obj->formatNumber($rs[0]['outstanding']);  
	$_POST['refcode'] =  $rs[0]['refcode'];
	$_POST['refcode2'] =  $rs[0]['refcode2'];
    $_POST['selWarehouse'] = $rs[0]['warehousekey'];
    $_POST['selARType'] = $rs[0]['artype']; 
    $_POST['selPaymentMethod'] = $rs[0]['paymentmethodkey']; 
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalvoucher']); 
     
    if(!empty($rs[0]['refcustomerkey'])){
        $rsRefCustomer = $customer->getDataRowById($rs[0]['refcustomerkey']);
        $_POST['refCustomerName'] = $rsRefCustomer[0]['name'];
    }
     
    if(!empty($rs[0]['refcashoutkey'])){
        $rsCashOut = $truckingCostCashOut->getDataRowById($rs[0]['refcashoutkey']);
        $_POST['refCashOutCode'] = $rsCashOut[0]['code'];
    }
     
    if(!empty($rs[0]['refsokey'])){
        $rsSO = $truckingServiceOrder->getDataRowById($rs[0]['refsokey']);
        $_POST['refJOCode'] = $rsSO[0]['code'];
    }
     
    if(!empty($rs[0]['refwokey'])){
        $rsWO = $truckingServiceWorkOrder->getDataRowById($rs[0]['refwokey']);
        $_POST['refWOCode'] = $rsWO[0]['code'];
    }    
    if( !in_array($rs[0]['artype'],  $arAvailableType))
        array_push($arAvailableType, $rs[0]['artype']);
    

    if (ADV_FINANCE && TEST_VOUCHER) {

        $rsAREmployeeMethodDetail = $obj->getPaymentVoucherDetail($id);
        $arrAvailableVoucher = $class->convertForCombobox($rsAREmployeeMethodDetail, 'cashbankvoucherkey', 'voucherlabel');
        $existingVoucherKey = array_column($rsAREmployeeMethodDetail, 'cashbankvoucherkey');
        
        $otherVoucher = $cashBank->getAvailableVoucher(
            $rs[0]['customerkey'],
            ' and ' . $cashBank->tableName . '.pkey not in (' . $obj->oDbCon->paramString($existingVoucherKey, ',') . ')',
            true,
            3
        );

        foreach ($otherVoucher as $voucherItem) {
            $arrAvailableVoucher[$voucherItem['pkey']]['label'] = $voucherItem['voucherlabel'];
            $arrAvailableVoucher[$voucherItem['pkey']]['rel'] = array('rel-amount' => $voucherItem['outstanding']);
        }
    }
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
    $editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey  = ' . $obj->oDbCon->paramString($rs[0]['paymentmethodkey']); 
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrARType =  $class->convertForCombobox($obj->getARType($arAvailableType),'pkey','name'); 
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'),'pkey','name');    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
	
	jQuery(document).ready(function(){  
        
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var varConstant = {  
                            ADV_FINANCE: <?php echo (ADV_FINANCE) ? "true" : "false"; ?>
                        };
    
        var arEmployee = new AREmployee(tabID,<?php echo json_encode(array($rs, $rsAREmployeeMethodDetail)); ?>, varConstant);
        
        prepareHandler(arEmployee);

        var fieldValidation =  {
                                code: { 
                                    validators: {
                                        notEmpty: {
                                            message: phpErrorMsg.code[1]
                                        }, 
                                    }
                                },  
                                employeeName: { 
                                    validators: {
                                        notEmpty: {
                                            message:  phpErrorMsg.employee[1]
                                        }, 
                                    }
                                },   
                                amount: {
                                    validators: { 
                                        greaterThan: {
                                            value: 0,
                                            inclusive: false,
                                            separator: ',', 
                                            message: phpErrorMsg.amount[2]
                                        }
                                    }
                                },    
                            } ; 

        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>);
 
        
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['transactionType']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputSelect('selARType', $arrARType); ?>  
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse); ?> 
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['employee']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $employee,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'employeeName',
                                                                                                   'key' => 'hidEmployeeKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-employee.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                )  ,
                                                                                'callbackFunction' => 'getTabObj().updateVoucher(event, ui)' 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputNumber('amount'); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['outstanding']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputNumber('outstanding', array('readonly' => true)); ?> 
                                        </div> 
                                    </div>   
    
                                    <?php if((ADV_FINANCE && TEST_VOUCHER)) { ?>

                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cashBankVoucher']); ?></label>
                                            <div class="col-xs-9">
                                            
                                                <div class="mnv-total-group mnv-payment-method">  

                                                    <div class="div-table" style="width: 100%">
                                                        <div class="div-table-row  form-group"> 
                                                                <div class="div-table-col-3"  style="padding-left:0; padding-right:0"> 
                                                                    <?php echo $obj->inputCollapsibleNumber('totalPayment', array( 'readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                                                </div> 
                                                                <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                                        </div>
                                                    </div>

                                                    <div class="mnv-total-group-detail  ">
                                                        <div class="div-table transaction-detail" style="width: 100%">
                                                        <?php

                                                            $totalRows = count($rsAREmployeeMethodDetail);
                                                            for ($i = 0; $i <= $totalRows; $i++) {
                                                                $class = 'transaction-detail-row';
                                                                $overwrite = true;
                                                                $disabled = false;

                                                                if ($i == $totalRows) {
                                                                    $class = 'payment-method-row-template row-template';
                                                                    $overwrite = false;
                                                                    $disabled = true;
                                                                } else {
                                                                    $_POST['hidDetailPaymentKey[]'] = $rsAREmployeeMethodDetail[$i]['pkey'];
                                                                    $_POST['selPaymentMethod[]'] = $rsAREmployeeMethodDetail[$i]['paymentkey'];
                                                                    $_POST['selVoucher[]'] = $rsAREmployeeMethodDetail[$i]['cashbankvoucherkey'];
                                                                    $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsAREmployeeMethodDetail[$i]['amount']);
                                                                }
                                                                ?>
                                                    
                                                                <div class="div-table-row form-group <?php echo $class; ?>">
                                                                    <div class="div-table-col-3" style="text-align:right; padding-left:0; padding-right:0">
                                                                        <?php echo $obj->inputHidden('hidDetailPaymentKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                                        <?php echo  $obj->inputSelect('selVoucher[]', $arrAvailableVoucher, array('overwritePost' => $overwrite, 'disabled' => $disabled)) ?>
                                                                    </div>
                                                                    <div class="div-table-col-3" style="width:180px;padding-right:0">
                                                                        <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'add-class' => 'mnv-detail-field', 'etc' => 'style="text-align:right;"')); ?>
                                                                    </div>
                                                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                                                        <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?>
                                                                    </div>
                                                                </div>
                                                    
                                                            <?php } ?>
                                                    
                                                            <div class="div-table-row form-group ">
                                                                <div class="div-table-col-3"></div>
                                                                <div class="div-table-col-3">
                                                                    <div class="form-detail-button mnv-total-group-hide-detail" style="float:right; text-align:right;">
                                                                        <?php echo ucwords($obj->lang['hideDetail']); ?> </div>
                                                                </div>
                                                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                                                            </div>
                                                            <div class="div-table-row form-group ">
                                                                <div class="div-table-col-3 " style="height:1em"></div>
                                                                <div class="div-table-col-3 "></div>
                                                                <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                                                            </div>
                                                    
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    <?php } else { ?>

                                        <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cashBankAccount']); ?></label> 
                                            <div class="col-xs-9">  
                                            <?php echo $obj->inputSelect('selPaymentMethod', $arrPaymentMethod);
                                            ?> 
                                            </div> 
                                        </div>    

                                    <?php } ?>
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
                                   <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['reference']); ?></div>
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['transaction']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('refcode', array('readonly' => true)); ?> 
                                        </div> 
                                    </div>   
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('refCustomerName', array('readonly' => true)); ?>
                                        </div> 
                                   </div> 
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cashOut']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('refCashOutCode', array('readonly' => true)); ?>
                                        </div> 
                                   </div>
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobOrder']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('refJOCode', array('readonly' => true)); ?>
                                        </div> 
                                   </div> 
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['workOrder']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputText('refWOCode', array('readonly' => true)); ?>
                                        </div> 
                                   </div> 
                                </div>
                         
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
                                                  $rsDetailPayment = $arPayment->getDetailPaymentByARKey($_GET['id']);
                                                  for ($i=0;$i<count($rsDetailPayment);$i++){
                                                      $rsArPayment= $arPayment->getDataRowById($rsDetailPayment[$i]['refkey']);
                                                      if($rsArPayment[0]['statuskey'] == 2 || $rsArPayment[0]['statuskey'] == 3){
                                                          echo '
                                                             <div class="div-table-row"> 
                                                                 <div class="div-table-col-5" style="border-bottom:1px solid #dedede;" > 
                                                                    '.$rsArPayment[0]['code'].'
                                                                 </div> 
                                                                 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:center" > 
                                                                    '.$obj->formatDBDate($rsArPayment[0]['trdate']).'
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
