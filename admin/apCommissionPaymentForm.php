<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass(array('APPayment.class.php','APCommissionPayment.class.php'));
$apCommissionPayment = createObjAndAddToCol( new APCommissionPayment());  
$apCommission = createObjAndAddToCol( new APCommission());  
$supplier = createObjAndAddToCol( new Supplier()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 
$currency = createObjAndAddToCol( new Currency()); 
$cashBank = createObjAndAddToCol( new CashBank()); 
$truckingPurchaseRefund = createObjAndAddToCol( new TruckingPurchaseRefund()); 

	
$obj= $apCommissionPayment;
$ap = $obj->getAPObj();
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'apCommissionPaymentList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$rsAPPaymentDetail = array();
$rsAPPaymentMethodDetail = array();
$rsAPDP = array();
$rsAPCost = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['trStartDate'] = date('d / m / Y');
$_POST['trEndDate'] = date('d / m / Y'); 
$_POST['hidCurrentCurrencyKey'] = 1;  // default IDR

$rs = prepareOnLoadData($obj);  
$apCommissionPaymentRate = $obj->loadSetting('APCommissionPaymentRate');
if(empty($apCommissionPaymentRate)) $apCommissionPaymentRate = 1;

//$readonlyCurrencyRate = ($apCommissionPaymentRate == 2) ? 'force-readonly' : '';
//$readonlyCurrencyRate = ($apCommissionPaymentRate == 2) ? 'readonly' : '';

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsAPPaymentDetail = $obj->getDetailById($id);
	
	if(ADV_FINANCE && TEST_VOUCHER){ 
		$rsAPPaymentMethodDetail = $obj->getPaymentVoucherDetail($id);  
		$arrAvailableVoucher = $class->convertForCombobox($rsAPPaymentMethodDetail,'cashbankvoucherkey','voucherlabel');  

		$existingVoucherKey = array_column($rsAPPaymentMethodDetail,'cashbankvoucherkey');
		$otherVoucher = $cashBank->getAvailableVoucher($rs[0]['supplierkey'],' and '.$cashBank->tableName.'.pkey not in ('.$obj->oDbCon->paramString($existingVoucherKey,',').')',false,2);
        
		foreach($otherVoucher as $voucherItem){ 
			$arrAvailableVoucher[$voucherItem['pkey']]['label'] = $voucherItem['voucherlabel'];
			$arrAvailableVoucher[$voucherItem['pkey']]['rel'] = array('rel-amount' => $voucherItem['outstanding']); 
		}  
	}else{
		$rsAPPaymentMethodDetail = $obj->getPaymentMethodDetail($id);		
	}

     
    $rsAPDP = $obj->getDownpaymentDetail($id,'',false);
    $rsAPCost = $obj->getCostDetail($id);
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
	$_POST['supplierName'] = $rsSupplier[0]['name'] ;
	$_POST['taxId'] = $rsSupplier[0]['taxid'] ;
	
	$_POST['hidCurrentSupplierName'] = $rsSupplier[0]['name'] ; 
	$_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ;  
	$_POST['hidCurrentSupplierKey'] = $rsSupplier[0]['pkey'] ; 
	$_POST['trDesc'] = $rs[0]['trnotes'];
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']); 
    $_POST['totalDiscount'] = $obj->formatNumber($rs[0]['totaldiscount']);  
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;
	$_POST['pph23'] =  $obj->formatNumber($rs[0]['payabletax23']) ;
    $_POST['selWarehouseKey'] = $rs[0]['warehousekey'];   
    
    $_POST['selCurrency'] = $rs[0]['currencykey']; 
    $_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'],2);
	$_POST['hidCurrentCurrencyKey'] = $rs[0]['currencykey'] ;      
    $_POST['chkDatePeriod'] = $rs[0]['usedateperiod'];   
	$_POST['trStartDate'] = $obj->formatDBDate($rs[0]['startdateperiod'],'d / m / Y');
	$_POST['trEndDate'] = $obj->formatDBDate($rs[0]['enddateperiod'],'d / m / Y');
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
    $editCurrencyInactiveCriteria = ' or  '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);  
 
    
} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 
$arrCurrency = $currency->generateComboboxOpt(null,array('criteria' =>' and ('.$currency->tableName.'.statuskey = 1)')); 

if(empty($rs[0]['nettingkey']))
	$arrPaymentMethod = $paymentMethod->generateComboboxOpt(null,array('criteria' => ' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'));
else
	$arrPaymentMethod = $paymentMethod->generateComboboxOpt(array('data' => NETTING_PAYMENT));  


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
            useWarehousePrivilages : <?php  echo ($obj->useWarehousePrivileges()) ?  "true" : "false"; ?>, 
            CURRENCY : <?php echo json_encode(CURRENCY); ?>, 
            ADV_FINANCE : <?php echo (ADV_FINANCE) ? "true" : "false"; ?>,
            apCommissionPaymentRate : <?php echo $apCommissionPaymentRate ; ?>
        };

         var apCommissionPayment = new APCommissionPayment(tabID, <?php echo json_encode($rs); ?>,varConstant);
    
         prepareHandler(apCommissionPayment);
         $('#defaultForm-' + tabID+ ' .input-date').datepicker('option', 'maxDate', "+14D" );
           
         var fieldValidation =  {code: {
                                        validators: {
                                                notEmpty: {  message: phpErrorMsg.code[1] }, 
                                        }
                                    },
                                  supplierName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.supplier[1]
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
    <?php echo $obj->inputHidden('hidCurrentSupplierKey'); ?>
    <?php echo $obj->inputHidden('hidCurrentSupplierName'); ?>
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
                                                                                                ) , 
                                                                                'callbackFunction' => 'getTabObj().updateSupplierInformation(event, ui)'
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div>      
								    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxRegistrationNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputText('taxId', array('readonly'=>true)); ?>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['apPeriod']); ?></label> 
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
                              <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div> 
                               <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?> 
                            </div>   
                    </div>
                </div>    
        </div>   
                                    
        
        <div class="div-table mnv-transaction transaction-detail mnv-checkbox-group" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">  
                     <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['apCode']); ?></div> 
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['discount']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['payingSettlement']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['tax23']); ?></div>
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0"')); ?></div>
                            <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col" ></div>
                       </div>
                    </div>        
                </div>
                
				<?php
                  	  
                    $totalRows = count($rsAPPaymentDetail); 
            
                    $rsAPCol = $ap->searchDataRow(array('pkey','refkey', 'code', 'refcode', 'refcode2','rate','amount'),
                                                ' and '.$ap->tableName.'.pkey in ('.$obj->oDbCon->paramString( array_column($rsAPPaymentDetail,'apkey'), ',').') ');
            
                    $rsAPCol = array_column($rsAPCol,null,'pkey');
            
                    for ($i=0;$i<=$totalRows; $i++){   
					    $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        
                        $_POST['refCode[]']  = '';
                        $_POST['refJOCode[]']  = '';
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                        } else {  
                            //$rsAP = $ap->getDataRowById($rsAPPaymentDetail[$i]['apkey']);
                            $rsAP = $rsAPCol[$rsAPPaymentDetail[$i]['apkey']];
                            
                            $_POST['hidDetailKey[]'] =  $rsAPPaymentDetail[$i]['pkey'];
                            $_POST['hidAPKey[]'] =  $rsAPPaymentDetail[$i]['apkey']; 
                            $_POST['apCode[]'] =  $rsAP['code'];
                            $_POST['arCode[]'] =  $rsAP['code'] ;
                            $_POST['refCode[]'] =  $rsAP['refcode'] ;
                            $_POST['refJOCode[]'] =  $rsAP['refcode2'] ; 
                            $_POST['hidAPRate[]'] =  $rsAP['rate'] ; 
                            $_POST['apAmount[]'] =  $obj->formatNumber($rsAP['amount']);
                            $_POST['outstanding[]'] =  $obj->formatNumber($rsAPPaymentDetail[$i]['outstanding']); 
                            $_POST['amount[]'] =   $obj->formatNumber($rsAPPaymentDetail[$i]['amount']);  
                            $_POST['discount[]'] =   $obj->formatNumber($rsAPPaymentDetail[$i]['discount']);
                            $_POST['taxPPH[]'] =   $obj->formatNumber($rsAPPaymentDetail[$i]['taxamount']); 
                            $_POST['chkPick[]'] =  1;
                            
                        }
                 ?>
            
                  <div class="div-table-row <?php echo $class; ?>">  
                    <div class="div-table-col"  style="padding: 0.3em 0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row"> 
                                <div class="div-table-col detail-col-detail">
                                    <?php echo $obj->inputHidden('hidDetailKey[]',array('disabled' => $disabled,'overwritePost' => $overwrite)); ?>
                                    <?php echo $obj->inputText('apCode[]',array('disabled' => $disabled,'overwritePost' => $overwrite)); ?>
                                    <?php echo $obj->inputHidden('hidAPKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    <?php echo $obj->inputHidden('hidAPRate[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                </div>  
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('apAmount[]',array('overwritePost' => $overwrite, 'readonly' => true, 'disabled' => $disabled, 'etc' => 'style="text-align:right"')); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('outstanding[]',array('overwritePost' => $overwrite,'readonly' => true,  'disabled' => $disabled,'etc' => 'style="text-align:right"')); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('discount[]',array('overwritePost' => $overwrite,'disabled' => $disabled, 'etc' => 'style="text-align:right"; ')); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite,'disabled' => $disabled, 'etc' => 'style="text-align:right";')); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('taxPPH[]',array('overwritePost' => $overwrite,'disabled' => $disabled, 'etc' => 'style="text-align:right";')); ?></div> 
                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick[]',  array('value'=> 1, 'disabled' => $disabled) ); ?></div>
                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button')); ?> </div>
                           </div>
                        </div> 
                        <div class="div-table options-row" style="width: 100%">
                            <div class="div-table-row">
                                  <div class="div-table-col detail-col-detail row-header" style="width: 50px">
                                    <?php echo $obj->lang['reference']; ?>
                                  </div> 
                                  <div class="div-table-col detail-col-detail" style="width: 150px">
                                   <?php echo $obj->inputText('refCode[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                  </div> 
                                 <?php if ( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding']))) { ?> 
                                  <div class="div-table-col detail-col-detail" style="width: 20px"></div>
                                    <div class="div-table-col detail-col-detail row-header" style="width: 100px">
                                    <?php echo $obj->lang['jobOrderCode']; ?>
                                  </div> 
                                    <div class="div-table-col detail-col-detail">    
                                    <?php echo $obj->inputText('refJOCode[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                 
                                  </div> 
                                <?php } ?>    
                                  <div class="div-table-col detail-col-detail"></div>
                            </div>
                            
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
                                         <?php echo $obj->lang['payingOffAmount']; ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                            <?php echo $obj->inputNumber('totalPaid', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['totalDiscount']; ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px;"> 
                                            <?php echo $obj->inputNumber('totalDiscount', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?>"></div> 
                              </div>
                             <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;">
                                    PPH 23 
                                </div>  
                                <div class="div-table-col-3"> 
                                    <?php echo $obj->inputNumber('pph23', array( 'readonly' => true, 'etc' => 'style="text-align:right;"' )); ?> 
                                </div>  
                                  <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?>"></div>
                             </div>   
                          </div>
						  
						           
                        <div class="mnv-total-group mnv-downpayment" >  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['downpayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:180px"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalDownpayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ' )); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>
                         
                        <div class="mnv-total-group-detail ">
                        <div class="div-table transaction-detail" style="width: 100%">
                            <?php  
                                $totalRows = count($rsAPDP);
                                for($i=0;$i<=$totalRows;$i++) {
                                        $class =  'transaction-detail-row';
                                        $overwrite = true; 
                                        $disabled = false; 

                                        if ($i == $totalRows ){
                                            $class = 'downpayment-row-template row-template'; 
                                            $overwrite = false; 
                                            $disabled = true; 
                                        } else {   
                                            $_POST['hidDetailDownpaymentKey[]'] = $rsAPDP[$i]['pkey'];
                                            $_POST['hidDownpaymentKey[]'] = $rsAPDP[$i]['downpaymentkey'];
                                            $_POST['downpaymentCode[]'] = $rsAPDP[$i]['refcode'];
                                            $_POST['downpaymentAmount[]'] = $obj->formatNumber($rsAPDP[$i]['amount']); 
                                        }
                            ?> 

                            <div class="div-table-row form-group <?php echo $class; ?>">
                                <div class="div-table-col-3" style="text-align:right;">  
                                        <?php echo $obj->inputHidden('hidDetailDownpaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                        <?php echo $obj->inputHidden('hidDownpaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?> 
                                        <?php echo  $obj->inputText('downpaymentCode[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                </div>  
                                <div class="div-table-col-3" style="width:180px"> 
                                       <?php echo $obj->inputNumber('downpaymentAmount[]', array('overwritePost' => $overwrite, 'class'=>'form-control inputnumber mnv-detail-field', 'disabled' => $disabled, 'etc' => 'style="text-align:right;"')); ?>
                                </div>  
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                    <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"', 'class' =>'btn btn-link remove-button' )); ?>
                                </div>
                            </div> 

                            <?php } ?> 

                            <div class="div-table-row form-group ">
                                <div class="div-table-col-3"></div>  
                                <div class="div-table-col-3"><div class="form-detail-button mnv-total-group-hide-detail" style="float:right; text-align:right;" ><?php echo ucwords($obj->lang['hideDetail']); ?></div> </div>   
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?>"></div>
                            </div>  
                            <div class="div-table-row form-group ">
                                <div class="div-table-col-3 " style="height:1em"></div> <div class="div-table-col-3 "></div> <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> "></div>
                            </div>  
                          
                       </div>   
                        </div>
                    </div> 
                            
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

                                    $totalRows = count($rsAPCost);
                                    for($i=0;$i<=$totalRows;$i++) {
                                            $class =  'transaction-detail-row';
                                            $overwrite = true; 
                                            $disabled = false; 

                                            if ($i == $totalRows ){
                                                $class = 'cost-row-template row-template'; 
                                                $overwrite = false; 
                                                $disabled = true; 
                                            } else {   
                                                $_POST['hidDetailCostKey[]'] = $rsAPCost[$i]['pkey'];
                                                $_POST['hidCostKey[]'] = $rsAPCost[$i]['costkey'];
                                                $_POST['costName[]'] = $rsAPCost[$i]['costname'];
                                                $_POST['costAmount[]'] = $obj->formatNumber($rsAPCost[$i]['amount']); 
                                            }
                                ?> 

                                <div class="div-table-row form-group <?php echo $class; ?>">
                                    <div class="div-table-col-3" style="text-align:right;">  
                                            <?php echo $obj->inputHidden('hidDetailCostKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputText('costName[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                            <?php echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px"> 
                                           <?php echo $obj->inputNumber('costAmount[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'class'=>'form-control inputnumber mnv-detail-field', 'etc' => 'style="text-align:right;"')); ?>
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
                        <div class="mnv-total-group mnv-payment-method" style="margin-top:1em">  
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
                         
                        <div class="mnv-total-group-detail ">
                        <div class="div-table transaction-detail" style="width: 100%">
                            <?php 

                                $totalRows = count($rsAPPaymentMethodDetail);
                                for($i=0;$i<=$totalRows;$i++) {
                                        $class =  'transaction-detail-row';
                                        $overwrite = true; 
                                        $disabled = false; 

                                        if ($i == $totalRows ){
                                            $class = 'payment-method-row-template row-template'; 
                                            $overwrite = false; 
                                            $disabled = true; 
                                        } else {   
                                            $_POST['hidDetailPaymentKey[]'] = $rsAPPaymentMethodDetail[$i]['pkey'];
                                            $_POST['selPaymentMethod[]'] = $rsAPPaymentMethodDetail[$i]['paymentkey'];
                                            $_POST['selVoucher[]'] = $rsAPPaymentMethodDetail[$i]['cashbankvoucherkey'];
                                            $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsAPPaymentMethodDetail[$i]['amount']); 
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
                                       <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'class'=>'form-control inputnumber mnv-detail-field','etc' => 'style="text-align:right;"')); ?>
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
                                <div class="div-table-col-3" style="width:180px;"> 
   									    <?php echo $obj->inputNumber('balance', array( 'readonly' => true, 'etc' => 'style="text-align:right;"' )); ?> 
                                </div>  
                                  <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col" ></div>
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
