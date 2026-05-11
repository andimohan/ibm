<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CustomerDownpaymentSettlement.class.php');
$customerDownpaymentSettlement = createObjAndAddToCol( new CustomerDownpaymentSettlement()); 
$customer = createObjAndAddToCol( new Customer()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$currency = createObjAndAddToCol( new Currency()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 
$cashBank = createObjAndAddToCol( new CashBank()); 
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount()); 


$obj= $customerDownpaymentSettlement;
$customerDownpayment = $obj->getDownpaymentObj();
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'customerDownpaymentSettlementList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$rsDPSettlementDetail = array();
$rsDPSettlementMethodDetail = array();
$rsDPSettlementCost = array();
$arrAvailableVoucher = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['trStartDate'] = date('d / m / Y');
$_POST['trEndDate'] = date('d / m / Y'); 
$_POST['hidCurrentCurrencyKey'] = 1;  // default IDR
$rs = prepareOnLoadData($obj);  

// pake settingan tablekeyy saja, karena terkadang IDR user jg mau 2 decimal
// $decimalPrice = (empty($rs[0]['currencykey']) || $rs[0]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;  

$displayCoa = 'display:none';
//        $display = 'display:none';


if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsDPSettlementDetail = $obj->getDetailById($id); 
    
//    $rsDPSettlementMethodDetail = $obj->getPaymentMethodDetail($id);
    
    $rsDPSettlementCost = $obj->getCostDetail($id);  
	  
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCurrentCustomerKey'] = $rsCustomer[0]['pkey'] ;   
	$_POST['hidCurrentCustomerName'] = $rsCustomer[0]['name'] ; 
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['trDesc'] = $rs[0]['trnotes'];
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']); 
//	$_POST['totalDiscount'] = $obj->formatNumber($rs[0]['totaldiscount']); 
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;
//	$_POST['pph23'] =  $obj->formatNumber($rs[0]['prepaidtax23']) ;
   	$_POST['selWarehouseKey'] = $rs[0]['warehousekey'];  
	$_POST['chkDatePeriod'] = $rs[0]['usedateperiod'];   
	$_POST['trStartDate'] = $obj->formatDBDate($rs[0]['startdateperiod'],'d / m / Y');
	$_POST['trEndDate'] = $obj->formatDBDate($rs[0]['enddateperiod'],'d / m / Y');
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment']);  

    $_POST['selCurrency'] = $rs[0]['currencykey']; 
    $_POST['selDPSettlementType'] = $rs[0]['typekey']; 
    if($rs[0]['typekey'] == 1){ 
        $display = '';
        
        if(ADV_FINANCE && TEST_VOUCHER){ 
            $rsDPSettlementMethodDetail = $obj->getPaymentVoucherDetail($id);  
            $arrAvailableVoucher = $class->convertForCombobox($rsDPSettlementMethodDetail,'cashbankvoucherkey','voucherlabel');  
            
            $existingVoucherKey = array_column($rsDPSettlementMethodDetail,'cashbankvoucherkey');
            $otherVoucher = $cashBank->getAvailableVoucher($rs[0]['customerkey'],' and '.$cashBank->tableName.'.pkey not in ('.$obj->oDbCon->paramString($existingVoucherKey,',').')');
                  
            foreach($otherVoucher as $voucherItem){ 
                $arrAvailableVoucher[$voucherItem['pkey']]['label'] = $voucherItem['voucherlabel'];
                $arrAvailableVoucher[$voucherItem['pkey']]['rel'] = array('rel-amount' => $voucherItem['outstanding']); 
            }  
        }else{ 
			$rsDPSettlementMethodDetail = $obj->getPaymentMethodDetail($id);
		}
    }else{

        $display = 'display:none';
        $displayCoa = '';
        $_POST['totalCOA'] = $obj->formatNumber($rs[0]['totalcoa']);  
        $_POST['subtotalCOA'] = $obj->formatNumber($rs[0]['totalcoa']); 
        
        if(!empty($rs[0]['coakey'])){
            $rsCOAHeader = $chartOfAccount->getDataRowById($rs[0]['coakey']);
            $_POST['COAName'] = $rsCOAHeader[0]['code'].' - '.$rsCOAHeader[0]['name'] ;
            $_POST['hidCOAKey'] = $rs[0]['coakey'] ;
        }
    }
    
    $_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'],2);
	$_POST['hidCurrentCurrencyKey'] = $rs[0]['currencykey'] ;    
    
 	$editCurrencyInactiveCriteria = ' or  '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);  
 	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
  
} 


$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' =>' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 
$arrPaymentMethod = $paymentMethod->generateComboboxOpt(null,array('criteria' => ' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'));
$arrCurrency = $currency->generateComboboxOpt(null,array('criteria' =>' and ('.$currency->tableName.'.statuskey = 1)'));

$arrDPType = array();
$arrDPType[1] = $obj->lang['refund'];
$arrDPType[2] = $obj->lang['revenue'];

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
        
         var customerDownpaymentSettlement = new CustomerDownpaymentSettlement(tabID,tablekey, <?php echo json_encode($rs); ?>,varConstant);
    
         prepareHandler(customerDownpaymentSettlement);

          var fieldValidation =  {code: {
                                        validators: {
                                                notEmpty: {  message: phpErrorMsg.code[1] }, 
                                        }
                                    },
                                  customerName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.customer[1]
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
    <?php echo $obj->inputHidden('hidCurrentCustomerKey'); ?>
    <?php echo $obj->inputHidden('hidCurrentCustomerName'); ?>
    <?php echo $obj->inputHidden('hidCurrentCurrencyKey'); ?>
    
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
                                            <?php echo $obj->inputDate('trDate',array('etc' => 'max-days=14')); ?>  
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
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
                                                                                'callbackFunction' => 'getTabObj().updateCustomerInformation(event, ui)'
                                                                              )
                                                                        );  
                                            ?>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
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
                            <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['downpaymentCode']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['payingSettlement']); ?></div>
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0"')); ?></div>
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                          </div>
                    </div>      
                </div>
                
				<?php
                  	  
                    $totalRows = count($rsDPSettlementDetail);
                    for ($i=0;$i<=$totalRows; $i++){  
					    $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false;  
                        
                        $_POST['refCode[]']  = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                        } else {  
						    $rsDownpayment = $customerDownpayment->getDataRowById($rsDPSettlementDetail[$i]['downpaymentkey']);  
                            $_POST['hidDetailKey[]'] =  $rsDPSettlementDetail[$i]['pkey'];
                            $_POST['hidDownpaymentKey[]'] =  $rsDPSettlementDetail[$i]['downpaymentkey']; 
                            $_POST['dpCode[]'] =  $rsDownpayment[0]['code'];
                            //$_POST['refCode[]'] =  $rsDownpayment[0]['refcode'];
                            //$_POST['refCode2[]'] =  $rsDownpayment[0]['refcode2'];
                            //$doNumber = $ar->getDoNumber($rsAR[0]['refheaderkey']);
                      	    $_POST['dpAmount[]'] =  $obj->formatNumber($rsDownpayment[0]['amount']);
		   	                $_POST['outstanding[]'] =   $obj->formatNumber($rsDPSettlementDetail[$i]['outstanding']); 
                            $_POST['amount[]'] =   $obj->formatNumber($rsDPSettlementDetail[$i]['amount']); 
                            $_POST['chkPick[]'] =  1;
                             
                        }
                       
                 ?>        
                 
              <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col"  style="padding: 0.3em 0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row"> 
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputHidden('hidDetailKey[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                                        <?php echo $obj->inputText('dpCode[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                                        <?php echo $obj->inputHidden('hidDownpaymentKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('dpAmount[]',array('overwritePost' => $overwrite, 'readonly' => true,'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                    <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('outstanding[]',array('overwritePost' => $overwrite,'readonly' => true, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                    <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:right";')); ?></div> 
                                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick[]',  array('value'=> 1, 'disabled' => $disabled) ); ?></div>
                                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button')); ?> </div>
                            </div>
                        </div> 
<!--
                        <div class="div-table options-row" style="width: 100%">
                            <div class="div-table-row">
                                  <div class="div-table-col detail-col-detail row-header" style="width: 50px">
                                    <?php echo $obj->lang['reference']; ?>
                                  </div> 
                                  <div class="div-table-col detail-col-detail" style="width: 150px">
                                   <?php echo $obj->inputText('refCode[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                  </div>     
                                  <div class="div-table-col detail-col-detail"></div>
                            </div>
                            
                        </div> 
-->
                            
                    </div>
                </div> 
                
                <?php  } ?>   
                   
         </div>        
                   
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' =>'btn btn-primary btn-second-tone')); ?></div>
              
          <div>     
                <div class="div-table transaction-detail" style="float:right;">
                    <div class="div-table" style="width:100%; margin-top:1em"> 
                          <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;"> 
                                      <?php echo $obj->lang['payingOffAmount']; ?>
                                </div>  
                                <div class="div-table-col-3" style="width:180px;"> 
                                        <?php echo $obj->inputNumber('totalReceived', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                </div> 
                                <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                          </div>
<!--
                          <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;"> 
                                       <?php echo $obj->lang['totalDiscount']; ?>
                                </div>  
                                <div class="div-table-col-3" style="width:180px;"> 
                                        <?php echo $obj->inputNumber('totalDiscount', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                </div> 
                                <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?>"></div> 
                           </div>
-->
<!--
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;">
                                    PPH 23 
                                </div>  
                                <div class="div-table-col-3"> 
                                    <?php echo $obj->inputNumber('pph23', array( 'readonly' => true, 'etc' => 'style="text-align:right;"' )); ?> 
                                </div>  
                                <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?>"></div>
                            </div>  
-->
                        </div>
                    
                            
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['type']; ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                           <?php echo  $obj->inputSelect('selDPSettlementType', $arrDPType); ?>
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>

                        
<!--
                    <div class="mnv-total-group mnv-cost" style="margin-top:1em">  
                            <div class="div-table" style="width: 100%">
                                  <div class="div-table-row  form-group"> 
                                        <div class="div-table-col-3" style="text-align:right;"> 
                                               <?php echo $obj->lang['totalCost']; ?>
                                        </div>  
                                        <div class="div-table-col-3"  style="width:180px"> 
                                                <?php echo $obj->inputCollapsibleNumber('totalCost', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                        </div> 
                                        <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                  </div>
                            </div>

                            <div class="mnv-total-group-detail ">
                            <div class="div-table transaction-detail" style="width: 100%">
                                <?php 

                                    $totalRows = count($rsDPSettlementCost);
                                    for($i=0;$i<=$totalRows;$i++) {
                                            $class =  'transaction-detail-row';
                                            $overwrite = true; 
                                            $disabled = false; 

                                            if ($i == $totalRows ){
                                                $class = 'cost-row-template row-template'; 
                                                $overwrite = false; 
                                                $disabled = true; 
                                            } else {   
                                                $_POST['hidDetailCostKey[]'] = $rsDPSettlementCost[$i]['pkey'];
                                                $_POST['hidCostKey[]'] = $rsDPSettlementCost[$i]['costkey'];
                                                $_POST['costName[]'] = $rsDPSettlementCost[$i]['costname'];
                                                $_POST['costAmount[]'] = $obj->formatNumber($rsDPSettlementCost[$i]['amount']); 
                                            }
                                ?> 

                                <div class="div-table-row form-group <?php echo $class; ?>">
                                    <div class="div-table-col-3" style="text-align:right;">  
                                            <?php echo $obj->inputHidden('hidDetailCostKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputText('costName[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px"> 
                                           <?php echo $obj->inputNumber('costAmount[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'add-class'=>'mnv-detail-field', 'etc' => 'style="text-align:right;"')); ?>
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
-->
                    
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['total']; ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                            <?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;" ' )); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>
                    
                    <div class="mnv-total-group mnv-payment-method" style="margin-top:1em; <?php echo $display; ?>">  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['totalPayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:180px"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalPayment', array( 'readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>
                         
                        <div class="mnv-total-group-detail ">
                        <div class="div-table transaction-detail" style="width: 100%">
                            <?php 

                                $totalRows = count($rsDPSettlementMethodDetail);
                                for($i=0;$i<=$totalRows;$i++) {
                                        $class =  'transaction-detail-row';
                                        $overwrite = true; 
                                        $disabled = false; 

                                        if ($i == $totalRows ){
                                            $class = 'payment-method-row-template row-template'; 
                                            $overwrite = false; 
                                            $disabled = true; 
                                        } else {   
                                            $_POST['hidDetailPaymentKey[]'] = $rsDPSettlementMethodDetail[$i]['pkey'];
                                            $_POST['selPaymentMethod[]'] = $rsDPSettlementMethodDetail[$i]['paymentkey'];
                                            $_POST['selVoucher[]'] = $rsDPSettlementMethodDetail[$i]['cashbankvoucherkey'];
                                            $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsDPSettlementMethodDetail[$i]['amount']); 
                                        }
                            ?> 

                            <div class="div-table-row form-group <?php echo $class; ?>">
                                <div class="div-table-col-3" style="text-align:right;">  
                                        <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                        <?php echo  (ADV_FINANCE && TEST_VOUCHER) ? $obj->inputSelect('selVoucher[]', $arrAvailableVoucher, array('overwritePost' => $overwrite, 'disabled' => $disabled))
                                                                    : $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)) 
                                        ?>
                                </div>  
                                <div class="div-table-col-3" style="width:180px">  
                                    <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'add-class'=>'mnv-detail-field', 'etc' => 'style="text-align:right;"')); ?>
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
                    
                    <div class="mnv-total-group mnv-coa" style="margin-top:1em;  <?php echo $displayCoa; ?>">  
                        <div class="div-table" style="width: 100%">
                            <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['total']. ' ' . $obj->lang['revenue']; ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalCOA', array('readonly' => true,'etc' => 'style="text-align:right;" ' )); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>    
                        <div class="mnv-total-group-detail">
                            <div class="div-table" style="width:100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3"  style="width:180px;padding-top:5.5px;"> 
                                        <?php   echo  $obj->inputAutoComplete( array(
                                                                        'objRefer' => $chartOfAccount,
                                                                        'revalidateField' => true, 
                                                                        'element' => array('value' => 'COAName',
                                                                                           'key' => 'hidCOAKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-coa.php',
                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        ) 
                                                            ));
                                        ?>  

                                    </div> 
                                    <div class="div-table-col-3" style="text-align:right;width:180px"> 
                                            <?php echo $obj->inputNumber('subtotalCOA', array( 'etc' => 'style="text-align:right;" ' )); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                       
                                </div>
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
                            <div class="div-table-col-3" style="width:180px"> 
                                <?php echo $obj->inputNumber('balance', array( 'readonly' => true, 'etc' => 'style="text-align:right;"' )); ?> 
                            </div>  
                            <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>
                       
                        
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
