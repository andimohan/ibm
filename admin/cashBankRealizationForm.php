<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('CashBankRealization.class.php');
$cashBankRealization = createObjAndAddToCol(new CashBankRealization()); 
$paymentMethod = createObjAndAddToCol(new PaymentMethod()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
$truckingServiceOrder =  createObjAndAddToCol(new TruckingServiceOrder());
$customer =  createObjAndAddToCol(new Customer());
$consignee =  createObjAndAddToCol(new Consignee());

$obj=  $cashBankRealization;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'cashBankRealizationList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$editPaymentMethodInactiveCriteria = '';
$display = 'display:none;';

$rsCost = array();

$_POST['trDate'] = date('d / m / Y');
$editWarehouseInactiveCriteria = '';
$rs = prepareOnLoadData($obj);   

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsCost = $obj->getDetailWithRelatedInformation($id);  
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
    
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['trDesc'] = $rs[0]['trdesc'];
    $_POST['total'] = $obj->formatNumber($rs[0]['total']);
    $_POST['totalRealization'] = $obj->formatNumber($rs[0]['totalrealization']); 
    $_POST['hidRefKey'] = $rs[0]['refkey'];
    $_POST['refCode'] = $rs[0]['refcode'];
    $_POST['hidRefKey2'] = $rs[0]['refkey2'];
    $_POST['refCode2'] = $rs[0]['refcode2'];
    $_POST['hidRefKey3'] = $rs[0]['refkey3'];
    $_POST['refCode3'] = $rs[0]['refcode3']; 
    
    $rsJO = $truckingServiceOrder->getDataRowById($rs[0]['jokey']);
    $_POST['shipmentNumber'] = $rsJO[0]['shipmentnumber'];
	$_POST['doNumber'] = $rsJO[0]['donumber'];
    
    $_POST['totalReceived'] =$obj->formatNumber($rs[0]['totalreceived']); 
    $_POST['totalPayment'] =$obj->formatNumber($rs[0]['totalpayment']); 
    $_POST['employeeAR'] =$obj->formatNumber($rs[0]['employeear']); 
    $_POST['balance'] =$obj->formatNumber($rs[0]['balance']); 
     
    $_POST['hidEmployeeKey'] = $rs[0]['employeekey']; 
	if (!empty($rs[0]['employeekey'])){
		$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
		$_POST['employeeName'] = $rsEmployee[0]['name'];
	} 
    
	if (!empty($rsJO[0]['customerkey'])){
		$rsCustomer = $customer->getDataRowById($rsJO[0]['customerkey']);
		$_POST['customerName'] = $rsCustomer[0]['name'];
	} 
    
	if (!empty($rsJO[0]['consigneekey'])){
		$rsConsignee = $consignee->getDataRowById($rsJO[0]['consigneekey']);
		$_POST['consigneeName'] = $rsConsignee[0]['name'];
	} 
    
   	$_POST['hidCOASettlementKey'] = $rs[0]['settlementcoakey'];
	if (!empty($rs[0]['settlementcoakey'])){
		$rsCOA = $chartOfAccount->getDataRowById($rs[0]['settlementcoakey']);
		$_POST['COASettlementName'] = $rsCOA[0]['code'].' - '.$rsCOA[0]['name'] ;
	}  	

    if($rs[0]['balance'] > 0)
 $display = '';
    $editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
  
       
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');  
/*$arrCashOutType = array(); 
$tableKey = $obj->getTableKeyAndObj($truckingServiceOrder->tableName);
$arrCashOutType[$tableKey['key']] = 'Job Order'; 
$tableKey = $obj->getTableKeyAndObj($truckingServiceWorkOrder->tableName);
$arrCashOutType[$tableKey['key']] = 'Surat Perintah Kerja';*/

$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
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
         var cashBankRealization = new CashBankRealization(tabID); 
    
         prepareHandler(cashBankRealization); 
        
         var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    },
                                    refCode: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.reference[1]
                                            }, 
                                        }
                                    }, 
                                    refCode2: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.reference[1]
                                            }, 
                                        }
                                    }, 
                                    employeeName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.employee[1]
                                            }, 
                                        }
                                    }, 	   
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
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputSelect('selWarehouse', $arrWarehouse, array('readonly' => true)); ?>  
                            </div> 
                        </div>
                     <!--   <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['transactionType']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php //echo $obj->inputSelect('hidRefTable', $arrCashOutType, array('readonly' => true)); ?>
                            </div> 
                        </div> -->
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['cashOut']; ?></label> 
                            <div class="col-xs-9"> 
                            
                                              <?php     
                                                   echo $obj->inputAutoComplete(array( 
                                                                                            'revalidateField' => true, 
                                                                                            'element' => array('value' => 'refCode',
                                                                                                               'key' => 'hidRefKey'),
                                                                                            'source' => array(
                                                                                                                'url' => 'ajax-trucking-cost-cash-out.php',
                                                                                                                'data' => array(  'action' =>'searchData', 'statuskey' => '(3)' )
                                                                                                            ), 
                                                                                            'callbackFunction' => 'getTabObj().updateReference()'  
                                                                                          )
                                                                                    );  


                                                ?> 
                                        </div>
                                        
                            </div>  
                         

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['refCode']; ?></label> 
                            <div class="col-xs-9"> 
                                <div class="flex"> 
                                     <div  class="consume">
                                          <?php     
                                               echo $obj->inputHidden('hidRefKey2'); 
                                               echo $obj->inputText('refCode2', array('readonly' => true));    
                                            ?>  
                                    </div>
                                    <div  class="consume spk">
                                          <?php     
                                               echo $obj->inputHidden('hidRefKey3'); 
                                               echo $obj->inputText('refCode3', array('readonly' => true));    
                                            ?> 
                                    </div>
                               </div>
                            </div> 
                        </div> 
                         
                          <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['si']; ?> / <?php echo $obj->lang['bookingNumber']; ?></label> 
                            <div class="col-xs-9"> 
                                <div class="flex"> 
                                     <div  class="consume">
                                          <?php  echo $obj->inputText('doNumber', array('readonly' => true)); ?>  
                                    </div>
                                    <div  class="consume">
                                          <?php  echo $obj->inputText('shipmentNumber', array('readonly' => true)); ?> 
                                    </div>
                               </div>
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['customer']; ?></label>  
                            <div class="col-xs-9">  
                                <?php echo $obj->inputText('customerName', array('readonly' => true)); ?>      
                            </div> 
                        </div> 
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['consignee']; ?></label>  
                            <div class="col-xs-9"> <?php echo $obj->inputText('consigneeName',array('readonly' => true));   ?>  </div> 
                        </div>  
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['employee']; ?></label>  
                            <div class="col-xs-9">  
                                <?php echo $obj->inputText('employeeName', array('readonly' => true)); ?>    
                                <?php echo $obj->inputHidden('hidEmployeeKey', array('readonly' => true)); ?>    
                            </div> 
                        </div> 
                        
<!--
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['settlementAccount']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php 
										echo  $obj->inputAutoComplete( array(
																'objRefer' => $chartOfAccount,
																'revalidateField' => true, 
																'element' => array('value' => 'COASettlementName',
																				   'key' => 'hidCOASettlementKey'),
																'source' =>array(
																					'url' => 'ajax-coa.php',
																					'data' => array(  'action' =>'searchData', 'iscashbank' => '1' )
																				) 
													));
								?>
                            </div> 
                        </div>   
-->
                        
                       <!-- <?php if (empty($rs)){ ?> 
                            <div class="form-group"> 
                                <div class="col-xs-3"></div>
                                <div class="col-xs-9"><?php echo $obj->inputButton('btnImport',$obj->lang['update'],array( 'class' => 'btn btn-primary semi-fixed btn-second-tone')); ?></div>
                            </div>  
                        <?php } ?>-->
                        
                        
                        
                    </div>
                </div>
                
                <div class="div-table-col">   
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                        <div class="form-group"> 
                            <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                            </div> 
                        </div>   
                    </div>
                </div>
                
            </div>
      </div>   
       
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="width:60px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['costName']); ?></div>
                    <div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['note']); ?></div>  
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['cost']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['realization']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div> 
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div> 
                </div>
                
				<?php 
                            
                    $totalRows = count($rsCost);
                    for ($i=0;$i<=$totalRows; $i++){  
                                
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = ''; 
                        $readonlySettlement = false;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = 'disabled="disabled"'; 
                        } else { 
                            $_POST['hidDetailKey[]'] =  $rsCost[$i]['pkey'];   
                            $_POST['hidCostKey[]'] =$rsCost[$i]['costkey']; 
                            $_POST['refheadercostkey[]'] =$rsCost[$i]['refkey2']; 
                            $_POST['costName[]'] =  $rsCost[$i]['costname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsCost[$i]['qty']);  
                            $_POST['costValue[]'] =   $obj->formatNumber($rsCost[$i]['costvalue']);  
                            $_POST['realCostValue[]'] =   $obj->formatNumber($rsCost[$i]['realcostvalue']);  
                            $_POST['amount[]'] =   $obj->formatNumber($rsCost[$i]['amount']);  
                            $_POST['detailDesc[]'] =  $rsCost[$i]['description'];   
                            $_POST['hidSettlementType[]'] =  $rsCost[$i]['settlementtypekey'];   
                            $_POST['hidSubtotalCostValue[]'] =  $rsCost[$i]['qty'] * $rsCost[$i]['costvalue'];   
                            
                            if($rsCost[$i]['settlementtypekey'])  $readonlySettlement = true;
                        }
                    ?>
            
                 <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail"> 
                       <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                       <?php echo $obj->inputHidden('hidSettlementType[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                       <?php echo $obj->inputHidden('hidSubtotalCostValue[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                       <?php echo $obj->inputText('qty[]',array('overwritePost' => $overwrite, 'readonly' => $readonlySettlement,  'etc' => 'style="text-align:right" ', 'disabled' => $disabled )); ?>
                    </div>  
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('costName[]',array('overwritePost' => $overwrite, 'readonly' => $readonlySettlement, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('refheadercostkey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('detailDesc[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('costValue[]',array('overwritePost' => $overwrite,'readonly' => true,  'etc' => 'style="text-align:right" ',  'disabled' => $disabled)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('realCostValue[]',array('overwritePost' => $overwrite,'readonly' => true,  'etc' => 'style="text-align:right" ', 'disabled' => $disabled)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite,  'etc' => 'style="text-align:right" ' , 'disabled' => $disabled)); ?></div> 
                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"><div <?php if ($readonlySettlement) echo 'style="display:none"'; ?>><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); ?></div></div> 
                </div>
                         
                <?php  } ?>   
                   
         </div>    
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
        
            <div  style="float:right; width: 350px">
                 
                   <div class="div-table" style="width: 100%;">
                       
                     <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;"> 
                               <?php echo $obj->lang['requestAmount']; ?>
                        </div>  
                        <div class="div-table-col-3" style="width:180px;"> 
                                <?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                        </div> 
                        <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                     </div> 
                       
                     <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;"> 
                               <?php echo $obj->lang['realization']; ?>
                        </div>  
                        <div class="div-table-col-3" style="width:180px;"> 
                                <?php echo $obj->inputNumber('totalRealization', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                        </div> 
                        <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                     </div>
                       
                     <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;"> 
                               <?php echo $obj->lang['totalDifference']; ?>
                        </div>  
                        <div class="div-table-col-3" style="width:180px;"> 
                                <?php echo $obj->inputNumber('totalReceived', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                        </div> 
                        <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                     </div>
                    </div>
                  
                    <!--<div class="mnv-total-group mnv-payment-method cashTOP "  >  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['totalPayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:180px"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalPayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>
                         
                        <div class="mnv-total-group-detail">
                            <div class="div-table  transaction-detail" style="width: 100%">
                                <?php 

                                    $totalRows = count($rsPaymentMethodDetail); 
                                
                                    for($i=0;$i<=$totalRows;$i++) {
                                            $class =  'transaction-detail-row';
                                            $overwrite = true; 
                                            $disabled = false; 

                                            if ($i == $totalRows ){
                                                $class = 'payment-method-row-template row-template'; 
                                                $overwrite = false; 
                                                $disabled = true; 
                                            } else {   
                                                $_POST['hidDetailPaymentKey[]'] = $rsPaymentMethodDetail[$i]['pkey'];
                                                $_POST['selPaymentMethod[]'] = $rsPaymentMethodDetail[$i]['paymentkey'];
                                                $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount']); 
                                            }
                                ?> 

                                <div class="div-table-row form-group <?php echo $class; ?>">
                                    <div class="div-table-col-3" style="text-align:right;">  
                                            <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px"> 
                                           <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'class'=>'form-control inputnumber mnv-detail-field', 'etc' => 'style="text-align:right;" ')); ?>
                                    </div>  
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                        <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"  attrhandler="getTabObj().calculateTotal()"', 'class' =>'btn btn-link remove-button' )); ?>
                                    </div>
                                </div> 

                                <?php } ?> 

                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3"></div>   
                                    <div class="div-table-col-3">
                                        <div class="text-link-01 mnv-total-group-hide-detail" style="float:right; text-align:right;" ><?php echo ucwords($obj->lang['hideDetail']); ?> </div> 
                                    </div>
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                                </div>  
                                <div class="div-table-row form-group ">
                                    <div class="div-table-col-3 " style="height:1em"></div> <div class="div-table-col-3 "></div> <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                                </div>  

                           </div>   
                        </div>
                    </div>  -->
                    <div class="div-table"  style="width: 100%"> 
                       <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;">
                                <?php echo $obj->lang['employeeAR']; ?> 
                            </div>  
                            <div class="div-table-col-3" style="width:180px"> 
                                <?php echo $obj->inputNumber('employeeAR', array('etc' => 'style="text-align:right;"' )); ?> 
                            </div>  
                            <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>
                        <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;">
                                <?php echo $obj->lang['balance']; ?> 
                            </div>  
                            <div class="div-table-col-3" style="width:180px"> 
                                <?php echo $obj->inputNumber('balance', array( 'readonly' => true, 'etc' => 'style="text-align:right;"' )); ?> 
                            </div>  
                            <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>

                        <div class="div-table-row  form-group account" style='<?php echo $display; ?> '> 
                            <div class="div-table-col-3" style="text-align:right;">
                                <?php echo $obj->lang['settlementAccount']; ?> 
                            </div>  
                            <div class="div-table-col-3" style="width:180px"> 
                                <?php 
										echo  $obj->inputAutoComplete( array(
																'objRefer' => $chartOfAccount,
																'revalidateField' => true, 
																'element' => array('value' => 'COASettlementName',
																				   'key' => 'hidCOASettlementKey'),
																'source' =>array(
																					'url' => 'ajax-coa.php',
																					'data' => array(  'action' =>'searchData', 'iscashbank' => '1' )
																				) 
													));
								?>                            </div>  
                            <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>
                    </div>
            </div>
             
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
