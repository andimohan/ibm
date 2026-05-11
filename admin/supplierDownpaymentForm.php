<?php 

require_once '../_config.php'; 
require_once '../_include-v2.php';  
includeClass(array('Downpayment.class.php','SupplierDownpayment.class.php'));
$supplierDownpayment = createObjAndAddToCol(new SupplierDownpayment());
$currency = createObjAndAddToCol(new Currency());
$warehouse = createObjAndAddToCol(new Warehouse());
$paymentMethod = createObjAndAddToCol(new PaymentMethod());
$supplier = createObjAndAddToCol(new Supplier());
$purchaseOrder = createObjAndAddToCol(new PurchaseOrder());
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
//$truckingPurchaseOrder = createObjAndAddToCol(new TruckingPurchaseOrder());
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());
$coaLink = createObjAndAddToCol(new COALink());
$cashBank = createObjAndAddToCol(new CashBank());
$termOfPayment = createObjAndAddToCol(new TermOfPayment());
    
$obj= $supplierDownpayment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'supplierDownpaymentList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$_POST['trDate'] = date('d / m / Y');

$editWarehouseInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';

$rs = prepareOnLoadData($obj);
$rsPaymentMethodDetail = array();

$readonylyFromInvoice = false;

if (!empty($_GET['id'])){ 
    $id = $_GET['id'];
    
   if($obj->useCashbankVoucher){ 
		$rsPaymentMethodDetail = $obj->getPaymentVoucherDetail($id);  
		$arrAvailableVoucher = $class->convertForCombobox($rsPaymentMethodDetail,'cashbankvoucherkey','voucherlabel');  

		$existingVoucherKey = array_column($rsPaymentMethodDetail,'cashbankvoucherkey');
		$otherVoucher = $cashBank->getAvailableVoucher($rs[0]['customerkey'],' and '.$cashBank->tableName.'.pkey not in ('.$obj->oDbCon->paramString($existingVoucherKey,',').')');

		foreach($otherVoucher as $voucherItem){ 
			$arrAvailableVoucher[$voucherItem['pkey']]['label'] = $voucherItem['voucherlabel'];
			$arrAvailableVoucher[$voucherItem['pkey']]['rel'] = array('rel-amount' => $voucherItem['outstanding']); 
		}  
	}else{
		    
        //$rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
        if (empty($rs[0]['refcashadvancekey'])){
            $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
        }else{
            $arrCashAdvance = array();

            array_push($arrCashAdvance,
                        array(
                            'pkey' => 0,
                            'paymentkey' => -1,
                            'amount' => $rs[0]['amount'],

                        )
                      );

            $rsPaymentMethodDetail = $arrCashAdvance;
        }

	} 
	 
    

    $rsDownpaymentHistory = $obj->getUsedDPList($id);
    $rsDPSettlementHistory = $obj->getDPSettlementList($id);
    $rsDownpaymentHistory = $rsDownpaymentHistory['history'];
    $rsDPSettlementHistory = $rsDPSettlementHistory['history'];
    $rsDownpaymentAllHistory = array_merge($rsDownpaymentHistory,$rsDPSettlementHistory);
    
	$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
    
    $_POST['hidCurrentSupplierKey'] = $rsSupplier[0]['pkey'] ;   
	$_POST['hidCurrentSupplierName'] = $rsSupplier[0]['name'] ; 
    
	$_POST['supplierName'] = $rsSupplier[0]['name'] ;
	$_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ; 
	$_POST['trDesc'] = $rs[0]['trdesc']; 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
	$_POST['payment'] = $obj->formatNumber($rs[0]['payment']); 
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2); 
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']); 
	$_POST['amount'] = $obj->formatNumber($rs[0]['amount']); 
	$_POST['beforeTaxTotal'] = $obj->formatNumber($rs[0]['beforetaxtotal']); 
	$_POST['outstanding'] = $obj->formatNumber($rs[0]['outstanding']); 
	$_POST['payment'] = $obj->formatNumber($rs[0]['payment']); 
    $_POST['hidRefKey'] =  $rs[0]['refkey']; 
    $_POST['chkIncludeTax'] =  $rs[0]['ispriceincludetax']; 
    $_POST['refCode'] =  $rs[0]['refcode']; 
    $_POST['selWarehouse'] = $rs[0]['warehousekey'];
    $_POST['selDPType'] = $rs[0]['reftabletype']; 
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']); 
    $_POST['selCurrency'] = $rs[0]['currencykey']; 
    $_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'],-2); 
	$_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'] ;

//    $_POST['prepaidTax23Percentage'] = $obj->formatNumber($rs[0]['prepaidtax23percentage'],2); 
//    $_POST['prepaidTax23'] = $obj->formatNumber($rs[0]['prepaidtax23']); 
    $editCurrencyInactiveCriteria = ' or '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']); 
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
    $editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
        
    if (!empty($rs[0]['refheaderkey']))
        $readonylyFromInvoice = true;
} 
$rsTOP = $termOfPayment->searchDataRow( array($termOfPayment->tableName.'.pkey', $termOfPayment->tableName.'.name', $termOfPayment->tableName.'.duedays')
									   , ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrTOP = $obj->generateComboboxOpt(array('data' => $rsTOP));
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
//$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'),'pkey','name');    
$rsPaymentMethod = (empty($rs[0]['refcashadvancekey'])) ? $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')') : CASH_ADVANCE;
$arrPaymentMethod = $obj->convertForCombobox($rsPaymentMethod,'pkey','name');  
$arrCurrency = $class->convertForCombobox($currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1 '.$editCurrencyInactiveCriteria.')'),'pkey','name'); 
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
                            CURRENCY : <?php echo json_encode(CURRENCY); ?>,
                            useCashBankVoucher : <?php echo ($obj->useCashbankVoucher) ? "true" : "false"; ?>
                            };
        
           var cashTOP = Array();
   
         <?php 
            for ($i=0;$i<count($rsTOP);$i++){
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
            }
         ?> 
         var supplierDownpayment = new SupplierDownpayment(tabID,varConstant,cashTOP); 
         prepareHandler(supplierDownpayment);   
         
        
         var fieldValidation =  { code: { 
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
        
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  
       
    }); 
	
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
        <?php prepareOnLoadDataForm($obj); ?>
        <?php echo $obj->inputHidden('selDPType'); ?>
        <?php echo $obj->inputHidden('hidCurrentSupplierKey'); ?>
        <?php echo $obj->inputHidden('hidCurrentSupplierName'); ?>
     
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse); ?> 
                                        </div> 
                                    </div>  
                                     
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?>  
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
                                  <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['transactionInformation']); ?></div>
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $supplier,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'supplierName',
                                                                                                   'key' => 'hidSupplierKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-supplier.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ),  
										                                          'callbackFunction' => 'getTabObj().updateSupplierInformation(event, ui)'
                                                                              ));  
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['downpayment']); ?></label> 
                                        <div class="col-xs-5" > 
                                           <?php echo $obj->inputDecimal('amount', array('readonly' => $readonylyFromInvoice)); ?>
                                        </div>  
                                        <div class="col-xs-4"  style="padding-left:0"> 
                                           <?php echo $obj->inputNumber('outstanding', array('readonly' => true)); ?>
                                        </div> 
                                   </div>  
                                    
                                   <!--<div style="clear:both; height: 1em"></div>
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['beforeTax']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputNumber('beforeTaxTotal', array('readonly' => true)); ?>
                                        </div> 
                                   </div> 
                                   
                                   <div class="form-group"> 
                                      <div class="col-xs-3 control-label"> <?php echo strtoupper($obj->lang['PPN']); ?> [Include]</div>   
                                     <div class="col-xs-9"> 
                                         <div class="flex">    
                                            <div><?php echo $obj->inputCheckBox('chkIncludeTax', array('readonly' => $readonylyFromInvoice)); ?></div>  
                                            <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('readonly' => $readonylyFromInvoice)); ?></div> 
                                            <div>%</div>
                                            <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true)); ?></div>
                                          </div> 
                                    </div> 
                                 </div> 
                                 
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['subtotal']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputNumber('subtotal', array('readonly' => true)); ?>
                                        </div> 
                                   </div> 
                                   
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['tax23']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <div class="flex">  
                                            <div class="percentage-col"><?php echo $obj->inputDecimal('prepaidTax23Percentage', array('readonly' => $readonylyFromInvoice, 'etc' => ' onChange="supplierDownpayment.calculateTotal()"')); ?></div> 
                                            <div>%</div>
                                            <div class="consume"><?php echo $obj->inputNumber('prepaidTax23', array('readonly' => true)); ?></div>
                                        </div>
                                        </div> 
                                   </div>   
                                   
                                   
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['totalPayment']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo $obj->inputNumber('payment', array('readonly' => true)); ?>
                                        </div> 
                                   </div> -->
                                   <?php if (!empty($rsDownpaymentAllHistory)) { ?>
                                    <div class="div-table" style="width:100%; margin-top:20px">
                                        <div class="div-table-row"> 
                                             <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; width:100px;" > 
                                                <strong><?php echo ucwords($obj->lang['reference']); ?></strong>
                                             </div>
                                            <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:center" > 
                                                <strong><?php echo ucwords($obj->lang['date']); ?></strong>
                                             </div>
                                             <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;" > 
                                                <strong><?php echo ucwords($obj->lang['amount']); ?></strong>
                                             </div>
                        
                                        </div> 
                                             <?php  
                                                  for ($i=0;$i<count($rsDownpaymentAllHistory);$i++){ 
                                                          echo '
                                                             <div class="div-table-row"> 
                                                                 <div class="div-table-col-5" style="border-bottom:1px solid #dedede;" > 
                                                                    '.$rsDownpaymentAllHistory[$i]['code'].'
                                                                 </div> 
                                                                 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:center" > 
                                                                    '.$obj->formatDBDate($rsDownpaymentAllHistory[$i]['trdate']).'
                                                                 </div> 
                                                                 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:right;" > 
                                                                    '.$obj->formatNumber($rsDownpaymentAllHistory[$i]['amount']).'
                                                                 </div>
                                                             </div> 
                                                         ';  
                                                    } 
                                             ?>
                                   </div> 
                                   <?php } ?>
                                </div>
                                <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['payment']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['payment']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selTermOfPaymentKey', $arrTOP); ?>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-xs-3 control-label"></label>
                                <div class="col-xs-9">
                                     <div class="div-table cashTOP transaction-detail" style="width:100%">
                                <?php

                                $totalRows = count($rsPaymentMethodDetail);
                                $numberClass = (!empty($_POST['selCurrency']) && $_POST['selCurrency'] == CURRENCY['idr']) ? 'inputnumber' : 'inputdecimal';
                                for ($i = 0; $i <= $totalRows; $i++) {
                                    $class =  'transaction-detail-row';
                                    $overwrite = true;
                                    $style = '';
                                    $disabled = false;

                                    if ($i == $totalRows) {
                                        $class = 'payment-method-row-template clone-detail';
                                        $overwrite = false;
                                        $style = 'style="display:none !important"';
                                        $disabled = true;
                                    } else {

                                        $_POST['hidDetailPaymentKey[]'] = $rsPaymentMethodDetail[$i]['pkey'];
                                        $_POST['selPaymentMethod[]'] = $rsPaymentMethodDetail[$i]['paymentkey'];
                                        $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount']);

                                        if ($obj->useCashbankVoucher) {
                                            if (USE_GL) {
                                                $rsPaymentCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $rs[0]['warehousekey'], $rsPaymentMethodDetail[$i]['paymentkey']);
                                                $coakey = $rsPaymentCOA[0]['coakey'];
                                            } else {
                                                $coakey = $rsPaymentMethodDetail[$i]['paymentkey'];
                                            }

                                            $_POST['cashBankRefCode[]'] = $cashBank->getCashBankRef($id, $obj->tableName, $coakey)['code'];
                                        }
                                    }
                                ?>

                                    <div class="div-table-row form-group <?php echo $class; ?>" <?php echo $style; ?>>
                                        <div class="div-table-col-3" style="text-align:right; padding-left:0">
                                            <?php echo $obj->inputHidden('hidDetailPaymentKey[]', array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                           <?php echo  ($obj->useCashbankVoucher) ? $obj->inputSelect('selVoucher[]', $arrAvailableVoucher, array('overwritePost' => $overwrite, 'disabled' => $disabled))
																		: $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)) 
													?>
                                        </div>
                                        <div class="div-table-col-3" style="width:100px;">
                                            <?php echo $obj->inputDecimal('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:right;" ', 'class' => 'form-control ' . $numberClass)); ?>
                                        </div>
                                        <?php if ($obj->useCashbankVoucher) { ?>
                                            <div class="div-table-col detail-col-detail" style="width:180px;"><?php echo $obj->inputText('cashBankRefCode[]', array('overwritePost' => $overwrite, 'readonly' => true)); ?></div>
                                        <?php } ?>
                                        <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:30px;">
                                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('etc' => 'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?>
                                        </div>
                                    </div>

                                <?php } ?>


                                <div class="div-table-row form-group">
                                    <div class="div-table-col-3"></div>
                                    <?php if ($obj->useCashbankVoucher) { ?>
                                        <div class="div-table-col-3"></div>
                                    <?php } ?>
                                    <div class="div-table-col-3" style="text-align:right"><?php echo $obj->inputLinkButton('btnAddPayment', $obj->lang['addRows'], array('class' => 'btn btn-link', 'etc' => 'style="padding-right:0; padding-top:0px"')); ?></div>
                                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:30px;"></div>
                                </div>

                            </div>
                                </div>
                            </div>
                         

                        </div>
                    </div>    
                </div>
      </div> 
         
        <div class="form-button-panel" > 
       	    <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
