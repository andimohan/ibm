<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('ARPayment.class.php','AREmployee.class.php','AREmployeePayment.class.php'));
$arEmployeePayment = createObjAndAddToCol( new AREmployeePayment());  
$arEmployee = createObjAndAddToCol( new AREmployee());  
$employee = createObjAndAddToCol( new Employee()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 
$cashBank = createObjAndAddToCol( new CashBank()); 

$obj= $arEmployeePayment;
 
$ar = $obj->getARObj();
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'arEmployeePaymentList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';

$rsAREmployeePaymentDetail = array();
$rsARPaymentMethodDetail = array();
$arrAvailableVoucher = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['trStartDate'] = date('d / m / Y');
$_POST['trEndDate'] = date('d / m / Y'); 

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsAREmployeePaymentDetail = $obj->getDetailWithRelatedInformation($id);
     
    if (!empty($rs[0]['nettingkey'])){
         $arrNettingPayment = array();
        
        array_push($arrNettingPayment,
                    array(
                        'pkey' => 0,
                        'paymentkey' => -1,
                        'amount' => $rs[0]['grandtotal'], 
                    )
                  );
        
        $rsARPaymentMethodDetail = $arrNettingPayment;
    
    }else if (!empty($rs[0]['refapemployeecommissionkey'])){
            $arrAPEmployeeCommission = array();
        
            array_push($arrAPEmployeeCommission,
                        array(
                            'pkey' => 0,
                            'paymentkey' => -2,
                            'amount' => $rs[0]['grandtotal'],
                            
                        )
                    );
            
            $rsARPaymentMethodDetail = $arrAPEmployeeCommission;
        
    }else{
        // payment normal
        if(ADV_FINANCE && TEST_VOUCHER){ 
            $rsARPaymentMethodDetail = $obj->getPaymentVoucherDetail($id,'',3);  
            $arrAvailableVoucher = $class->convertForCombobox($rsARPaymentMethodDetail,'cashbankvoucherkey','voucherlabel');  
            
            $existingVoucherKey = array_column($rsARPaymentMethodDetail,'cashbankvoucherkey');
            
            $otherVoucher = $cashBank->getAvailableVoucher($rs[0]['customerkey'],
                                                           ' and  '.$cashBank->tableName.'.credittype = 1 and '.$cashBank->tableName.'.pkey not in ('.$obj->oDbCon->paramString($existingVoucherKey,',').')',
                                                           true,
                                                           3);
                  
            foreach($otherVoucher as $voucherItem){ 
                $arrAvailableVoucher[$voucherItem['pkey']]['label'] = $voucherItem['voucherlabel'];
                $arrAvailableVoucher[$voucherItem['pkey']]['rel'] = array('rel-amount' => $voucherItem['outstanding']); 
            }  
        }else{ 
                $rsARPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
        }
    }
    
    
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$rsEmployee = $employee->getDataRowById($rs[0]['customerkey']);
	$_POST['employeeName'] = $rsEmployee[0]['name'] ;
	$_POST['hidCurrentEmployeeName'] = $rsEmployee[0]['name'] ; 
	$_POST['hidEmployeeKey'] = $rsEmployee[0]['pkey'] ;  
	$_POST['hidCurrentEmployeeKey'] = $rsEmployee[0]['pkey'] ; 
	$_POST['trDesc'] = $rs[0]['trnotes'];
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']); 
    $_POST['selWarehouseKey'] = $rs[0]['warehousekey']; 
    $_POST['chkDatePeriod'] = $rs[0]['usedateperiod'];   
	$_POST['trStartDate'] = $obj->formatDBDate($rs[0]['startdateperiod'],'d / m / Y');
	$_POST['trEndDate'] = $obj->formatDBDate($rs[0]['enddateperiod'],'d / m / Y');
	 
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
  
} 

$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
 
if(!empty($rs[0]['nettingkey'])){
    $rsPaymentMethod = NETTING_PAYMENT;
}else if(!empty($rs[0]['refapemployeecommissionkey'])){
    $rsPaymentMethod = COMMISSION_PAYMENT;
}else{
    $rsPaymentMethod = $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')');
}
 
$arrPaymentMethod = $obj->convertForCombobox($rsPaymentMethod,'pkey','name');    

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
                        CURRENCY : <?php echo json_encode(CURRENCY); ?>, 
                        ADV_FINANCE : <?php echo (ADV_FINANCE) ? "true" : "false"; ?>
                        };

         var arEmployeePayment = new AREmployeePayment(tabID, <?php echo json_encode($rs); ?>,varConstant);
    
         prepareHandler(arEmployeePayment);
           
         var fieldValidation =  {code: {
                                        validators: {
                                                notEmpty: {  message: phpErrorMsg.code[1] }, 
                                        }
                                    },
                                  employeeName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.employee[1]
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
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
    <?php prepareOnLoadDataForm($obj); ?>     
    <?php echo $obj->inputHidden('hidCurrentEmployeeKey'); ?>
    <?php echo $obj->inputHidden('hidCurrentEmployeeName'); ?>
    
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
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
                                                                                                ) , 
                                                                                'callbackFunction' => 'getTabObj().updateEmployeeInformation(event, ui)'
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['arPeriod']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div><?php echo $obj->inputCheckBox('chkDatePeriod'); ?></div>  
                                                <div class="consume"><?php echo $obj->inputDate('trStartDate',array( 'etc' => 'style="text-align:center"')); ?></div>  
                                                <div class="consume"><?php echo $obj->inputDate('trEndDate',array(  'etc' => 'style="text-align:center"')); ?></div>  
                                            </div> 
                                        </div> 
                                   </div>
                                    <div class="form-group">
                                        <div class="col-xs-3"></div>
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputButton('btnImport', $obj->lang['showAll'],array('class' =>'btn btn-primary btn-second-tone')); ?>
                                        </div> 
                                    </div>    
                                </div>
                    </div>
                    <div class="div-table-col"> 
                           <div class="div-tab-panel"> 
                              <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['note']); ?></div> 
                               <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?> 
                            </div>   
                    </div>
                </div>    
        </div>   
                                    
        
        <div class="div-table mnv-transaction transaction-detail mnv-checkbox-group" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['arCode']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:150px;"><?php echo ucwords($obj->lang['jobOrderCode']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:150px;"><?php echo ucwords($obj->lang['customer']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['payingSettlement']); ?></div>
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0" ')); ?></div> 
                            <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>
                    </div>    
                </div>
                
				<?php
                  	  
                    $totalRows = count($rsAREmployeePaymentDetail);
                    for ($i=0;$i<=$totalRows; $i++){  
                        
					    $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = ''; 
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = 'disabled="disabled"'; 
                        } else {  
                            //$rsAR = $ar->getDataRowById($rsAREmployeePaymentDetail[$i]['arkey']); 
                            $_POST['hidDetailKey[]'] =  $rsAREmployeePaymentDetail[$i]['pkey'];
                            $_POST['hidARKey[]'] =  $rsAREmployeePaymentDetail[$i]['arkey']; 
                            $_POST['arCode[]'] =  $rsAREmployeePaymentDetail[$i]['arcode'];
                            $_POST['refCode[]'] =  $rsAREmployeePaymentDetail[$i]['refcode'];
                            $_POST['jobOrderCode[]'] =  $rsAREmployeePaymentDetail[$i]['reftranscode2'];
                            $_POST['customerName[]'] =  $rsAREmployeePaymentDetail[$i]['customername'];
                            $_POST['refDate[]'] = $obj->formatDBDate($rsAREmployeePaymentDetail[$i]['refdate'],'d / m / Y');
                            $_POST['arAmount[]'] =  $obj->formatNumber($rsAREmployeePaymentDetail[$i]['amount']);
                            $_POST['outstanding[]'] =  $obj->formatNumber($rsAREmployeePaymentDetail[$i]['outstanding']); 
                            $_POST['amount[]'] =   $obj->formatNumber($rsAREmployeePaymentDetail[$i]['amount']);  
                            $_POST['chkPick[]'] =  1;
                        }
                 ?>
            
                  <div class="div-table-row <?php echo $class; ?>"> 
                        <div class="div-table-col"  style="padding: 0.3em 0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row"> 
                                <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('arCode[]',array('disabled' => $disabled,'overwritePost' => $overwrite )); ?><?php echo $obj->inputHidden('hidARKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('disabled' => $disabled,'overwritePost' => $overwrite)); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:150px;"><?php echo $obj->inputText('jobOrderCode[]',array('overwritePost' => $overwrite, 'readonly' => true)); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:150px;"><?php echo $obj->inputText('customerName[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:center"')); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('arAmount[]',array('overwritePost' => $overwrite, 'readonly' => true,'disabled' => $disabled, 'etc' => 'style="text-align:right"')); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('outstanding[]',array('overwritePost' => $overwrite,'readonly' => true, 'disabled' => $disabled, 'etc' => 'style="text-align:right"')); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite,'disabled' => $disabled, 'etc' => 'style="text-align:right";')); ?></div>  
                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick[]',  array('value'=> 1, 'disabled' => $disabled) ); ?></div>
                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button')); ?> </div>
                            </div>  
                        </div>
                        <div class="flex options-row" style="width: 100%">
                            <div class="row-header"><?php echo $obj->lang['reference']; ?></div>
                            <div><?php echo $obj->inputText('refCode[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?></div>
                            <div class="row-header"><?php echo $obj->lang['date']; ?></div>
                            <div><?php echo $obj->inputText('refDate[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?></div>
                        </div> 
                      </div>
                   </div>
                 <?php }   ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' =>'btn btn-primary btn-second-tone')); ?></div>
        
      
        <div>     
                      <div class="div-table transaction-detail" style="float:right;">
                         <div class="div-table" style="width:100%; margin-top:1em"> 
                             
                               <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                         <?php echo $obj->lang['total']; ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:165px;"> 
                                            <?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>  
                          </div>
                          
                         
                        <div class="mnv-total-group mnv-payment-method" style="margin-top:1em">  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['totalPayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:165px"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalPayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>
                         
                        <div class="mnv-total-group-detail ">
                        <div class="div-table transaction-detail" style="width: 100%">
                            <?php 

                                $totalRows = count($rsARPaymentMethodDetail);
                                for($i=0;$i<=$totalRows;$i++) {
                                        $class =  'transaction-detail-row';
                                        $overwrite = true; 
                                        $disabled = false; 

                                        if ($i == $totalRows ){
                                            $class = 'payment-method-row-template row-template'; 
                                            $overwrite = false; 
                                            $disabled = true; 
                                        } else {   
                                            $_POST['hidDetailPaymentKey[]'] = $rsARPaymentMethodDetail[$i]['pkey'];
                                            $_POST['selPaymentMethod[]'] = $rsARPaymentMethodDetail[$i]['paymentkey'];
                                            $_POST['selVoucher[]'] = $rsARPaymentMethodDetail[$i]['cashbankvoucherkey'];
                                            $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsARPaymentMethodDetail[$i]['amount']); 
                                        }
                            ?> 

                            <div class="div-table-row form-group <?php echo $class; ?>">
                                <div class="div-table-col-3" style="text-align:right;">  
                                        <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                        <?php echo  (ADV_FINANCE && TEST_VOUCHER) ? $obj->inputSelect('selVoucher[]', $arrAvailableVoucher, array('overwritePost' => $overwrite, 'disabled' => $disabled))
                                                                    : $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)) ; ?>

                                </div>  
                                 <div class="div-table-col-3" style="width:165px"> 
                                       <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'class'=>'form-control inputnumber mnv-detail-field', 'etc' => 'style="text-align:right;"')); ?>
                                </div>  
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                    <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"', 'class' =>'btn btn-link remove-button' )); ?>
                                </div>
                            </div> 

                            <?php } ?> 

                            <div class="div-table-row form-group ">
                                <div class="div-table-col-3"></div>   
                                <div class="div-table-col-3">
                                    <div class="form-detail-button mnv-total-group-hide-detail" style="float:right; text-align:right;" ><?php echo ucwords($obj->lang['hideDetail']); ?> </div> 
                                </div>
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                            </div>  
                            <div class="div-table-row form-group ">
                                <div class="div-table-col-3 " style="height:1em"></div> <div class="div-table-col-3 "></div> <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                            </div>  
                          
                       </div>   
                        </div>
                    </div> 

                    <div class="div-table"  style="width: 100%">
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;">
                                       <?php echo $obj->lang['balance']; ?>  
                                </div>  
                                <div class="div-table-col-3" style="width:165px;"> 
   									    <?php echo $obj->inputNumber('balance', array( 'readonly' => true, 'etc' => 'style="text-align:right;"' )); ?> 
                                </div>  
                                  <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col" ></div>
                            </div>
                          </div>  
                      </div>     
        </div>
         
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(array(),true); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
