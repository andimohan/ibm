<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('DebitNote.class.php');
$debitNote = createObjAndAddToCol( new DebitNote()); 
$supplier = createObjAndAddToCol( new Supplier()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$currency = createObjAndAddToCol( new Currency());  
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 
$emklJobOrder = createObjAndAddToCol( new EmklJobOrder()); 

$obj= $debitNote;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'debitNoteList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$_POST['trDate'] = date('d / m / Y');
$_POST['hidCurrentCurrencyKey'] = 1; 
$decimalPrice = 0;

$editWarehouseInactiveCriteria = ''; 
$editCurrencyInactiveCriteria = ''; 
$editPaymentMethodInactiveCriteria = '';
$rsPaymentMethodDetail = array();
$rsDetail = array(); 

$rs = prepareOnLoadData($obj);  
$inputClass = 'inputnumber'; // biar gk perlu update dr JS

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	  
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
    
	$decimalPrice = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;
	$inputClass = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 'inputnumber' : 'inputdecimal';
  
    
	$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
	$_POST['supplierName'] = $rsSupplier[0]['name'] ;
	$_POST['hidCurrentSupplierName'] = $rsSupplier[0]['name'] ; 
	$_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ;  
	$_POST['hidCurrentSupplierKey'] = $rsSupplier[0]['pkey'] ;
    $_POST['hidCurrentCurrencyKey'] = $rs[0]['currencykey'] ;      
	$_POST['hidCurrentCurrencyRate'] = $rs[0]['rate'] ;      
    
	$_POST['grandTotal'] = $obj->formatNumber($rs[0]['grandtotal'],$decimalPrice);
    
    
    if(!empty($rs[0]['joborderkey'])) {
        $rsJO = $emklJobOrder->getDataRowById($rs[0]['joborderkey']);
        $_POST['hidJobOrderKey'] = $rsJO[0]['pkey'];
        $_POST['jobOrderCode'] = $rsJO[0]['code'];
    }
     
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);

    if(ADV_FINANCE && TEST_VOUCHER){ 
        $rsPaymentMethodDetail = $obj->getPaymentVoucherDetail($id);  
        $arrAvailableVoucher = $class->convertForCombobox($rsPaymentMethodDetail,'cashbankvoucherkey','voucherlabel');  

        $existingVoucherKey = array_column($rsPaymentMethodDetail,'cashbankvoucherkey');
        $otherVoucher = $cashBank->getAvailableVoucher($rs[0]['supplierkey'],' and '.$cashBank->tableName.'.pkey not in ('.$obj->oDbCon->paramString($existingVoucherKey,',').')',true,1);
        foreach($otherVoucher as $voucherItem){ 
            $arrAvailableVoucher[$voucherItem['pkey']]['label'] = $voucherItem['voucherlabel'];
            $arrAvailableVoucher[$voucherItem['pkey']]['rel'] = array('rel-amount' => $voucherItem['outstanding']); 
        }  
    }

    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);   
    $editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
    $editCurrencyInactiveCriteria = ' or  '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);  
 
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrCurrency = $obj->convertForCombobox($currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1' . $editCurrencyInactiveCriteria.')'),'pkey','name');
$arrDNType = $obj->generateComboboxOpt(array('data' => $obj->getDebitNoteType(),'label' => 'name')); 

$rsPaymentMethod = (empty($rs[0]['nettingkey'])) ? $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')',' order by '.$paymentMethod->tableName.'.name asc') : NETTING_PAYMENT;
$arrPaymentMethod = $obj->convertForCombobox($rsPaymentMethod,'pkey','name');    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
     
	jQuery(document).ready(function(){  
	 	 
        var tabID = selectedTab.newPanel[0].id;
        
         var varConstant = {  
                            CURRENCY : <?php echo json_encode(CURRENCY); ?> 
                            };
        
        var debitNote = new DebitNote(tabID,varConstant);
        prepareHandler(debitNote);     

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
                                            message: phpErrorMsg.supplier[1]
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
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
     <?php prepareOnLoadDataForm($obj); ?>   
    <?php echo $obj->inputHidden('hidCurrentCurrencyKey'); ?>
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputDate('trDate'); ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>  
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['type']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputSelect('selDNType', $arrDNType ); ?>  
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
                                                                                'callbackFunction' => 'getTabObj().updateSupplierInformation(this,event, ui)'
                                                                              )
                                                                        );  
                                            ?>
                                </div> 
                            </div> 

                            <?php 
                                if($obj->activeModule['emkljoborder']) {
                                    //Sementara di JO FF
                            ?>
                                <div class="form-group show-without-ap">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobOrder']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php  echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $emklJobOrder,
                                                                                    'revalidateField' => true,
                                                                                    'element' => array('value' => 'jobOrderCode',
                                                                                                    'key' => 'hidJobOrderKey'),
                                                                                    'source' =>array(
                                                                                        'url' => 'ajax-emkl-job-order.php',
                                                                                        'data' => array(  'action' =>'searchData', 'statuskey' => '(2,3)' )
                                                                                    )
                                                                                )
                                                                            );  
                                                ?>  
                                    </div> 
                                </div> 
                            <?php } ?> 
							 

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?> <span class="hide-on-ap">/ <?php echo ucwords($obj->lang['currencyRate']); ?></span></label> 
                                <div class="col-xs-9  mnv-currency"> 
                                   <div class="flex">
                                       <div><?php  echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency')); ?></div>
                                       <div class="consume hide-on-ap"><?php echo $obj->inputDecimal('currencyRate', array('class'=>'form-control inputnumber input-currency-rate')); ?></div>
                                   </div>
                                </div> 
                            </div>
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
       
      <div class="mnv-checkbox-group">
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; "  attr-level="0">
                <div class="div-table-row">  

                    <div class="div-table-col detail-col-header">
                        <span class="hide-without-ap"><?php echo ucwords($obj->lang['apCode']); ?></span>
                        <span class="show-without-ap"><?php echo ucwords($obj->lang['cost']); ?></span>
                    </div>                     <div class="div-table-col detail-col-header" style="width:130px;"><?php echo ucwords($obj->lang['refCode']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:center;"><?php echo ucwords($obj->lang['date']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:right;"><?php echo ucwords($obj->lang['debitNote']); ?></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col" style="width: 25px" > <?php echo $obj->inputCheckBox('chkPick-master', array('etc' => '')); ?></div> 
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col"></div>
                </div>
    
				<?php 
                           
                    $totalRows = count($rsDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  

                        $class =  'transaction-detail-row';
                        $overwrite = true; 
                        $disabled = false;
                        $invoicekey = '';
                        $optionRows = 'display:none';
                        $totalDetailRows = 0 ;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template row-template';
                            $overwrite = false;
                            $disabled = true;

                        } else {   
                         
                            $_POST['hidRefAPKey[]'] =  $rsDetail[$i]['refapkey']; 
                            $_POST['refCode[]'] =  $rsDetail[$i]['apcode'];
                            $_POST['refAPDate[]'] =  $obj->formatDBDate($rsDetail[$i]['refapdate'], 'd / m / Y', array('returnOnEmpty' => true));  
                            $_POST['amount[]'] =   $obj->formatNumber($rsDetail[$i]['aptotal'],$decimalPrice);
                            $_POST['debitTotal[]'] =   $obj->formatNumber($rsDetail[$i]['totaldebit'],$decimalPrice);
                            $_POST['hidDetailKey[]'] = $rsDetail[$i]['pkey'];
                            $_POST['hidRefTableType[]'] = $rsDetail[$i]['reftabletype'];
                            $_POST['refPurchaseCode[]'] = $rsDetail[$i]['refpurchasecode'];
                        
                            $_POST['hidCostKey[]'] = $rsDetail[$i]['costkey'];
                            $_POST['costName[]'] = $rsDetail[$i]['costname'];
                        } 
						
                  ?>
                
                <div class="div-table-row <?php echo $class; ?>">  
                  <div class="div-table-col detail-col-detail" >
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        <div class="hide-without-ap">
                            <?php echo $obj->inputText('refCode[]',array('overwritePost' => $overwrite, 'disabled' => $disabled )); ?>
                            <?php echo $obj->inputHidden('hidRefAPKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?> 
                            <?php echo $obj->inputHidden('hidRefTableType[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                        </div>
                        <div class="show-without-ap">
                            <?php echo $obj->inputText('costName[]',array('overwritePost' => $overwrite, 'disabled' => $disabled )); ?>
                            <?php echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?> 
                        </div>
                    </div>    
                    <div class="div-table-col detail-col-detail" style="width:130px;"><?php echo $obj->inputText('refPurchaseCode[]', array('overwritePost' => $overwrite, 'readonly'=>true,  'disabled' => $disabled)); ?></div> 
                    <div class="div-table-col detail-col-detail" style="width:130px; text-align:right;"><?php echo $obj->inputText('refAPDate[]', array('overwritePost' => $overwrite, 'readonly'=>true,  'disabled' => $disabled, 'etc' => 'style="text-align:center;"' )); ?></div> 
                    <div class="div-table-col detail-col-detail" style="width:130px; text-align:right;"><?php echo $obj->inputNumber('amount[]', array('overwritePost' => $overwrite,'class'=> 'form-control '.$inputClass, 'readonly'=>true,  'disabled' => $disabled, 'etc' => 'style="text-align:right;" ' )); ?></div> 
                    <div class="div-table-col detail-col-detail" style="width:180px; text-align:right;"><?php echo $obj->inputNumber('debitTotal[]', array('overwritePost' => $overwrite,'class'=> 'form-control '.$inputClass,  'disabled' => $disabled, 'etc' => 'style="text-align:right;"' )); ?></div> 
                    <div class="div-table-col detail-col-detail icon-col  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputCheckBox('chkPick[]', array('value' => 1, 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabindex="-1"')); ?></div>
                </div> 
             
                <?php } ?> 
                   
         </div>         
      </div>
      
        <div style="clear:both; height:1em;"></div> 
        <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' =>'btn btn-primary btn-second-tone')); ?></div>
       
            <div>  
            <div style="float:right; ">
                <div class="div-table icon-col  <?php echo $obj->hideOnDisabled(array(1)); ?>" style="float:right;">&nbsp;</div>  
                <div class="div-table icon-col  <?php echo $obj->hideOnDisabled(array(1)); ?>" style="float:right;">&nbsp;</div>   
                <div class="div-table" style="float:right">
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['total']); ?> 
                        </div>  
                        <div class="div-table-col-3" style="width:180px;"> 
                            <?php echo $obj->inputNumber('grandTotal', array ('readonly' => true, 'class'=> 'form-control '.$inputClass, 'etc' => 'style="text-align:right;"')) ;?>   
                        </div>
                    </div> 
                </div>  
                
                <div class="mnv-total-group mnv-payment-method hide-on-ap"  style="margin-top:1em">  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['totalPayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:180px"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalPayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>
                         
                        <div class="mnv-total-group-detail ">
                        <div class="div-table transaction-detail" style="width: 100%">
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
                                            $_POST['selVoucher[]'] = $rsARPaymentMethodDetail[$i]['cashbankvoucherkey'];
                                            $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount']); 
                                        }
                            ?> 

                            <div class="div-table-row form-group <?php echo $class; ?>">
                                <div class="div-table-col-3" style="text-align:right;">  
                                        <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                        <?php echo  (ADV_FINANCE && TEST_VOUCHER) ? $obj->inputSelect('selVoucher[]', $arrAvailableVoucher, array('overwritePost' => $overwrite, 'disabled' => $disabled))
                                                                    : $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)) 
                                        ?>                                </div>  
                                <div class="div-table-col-3" style="width:180px"> 
                                       <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled, 'class'=>'form-control inputnumber mnv-detail-field','etc' => 'style="text-align:right;"')); ?>
                                </div>  
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                    <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"', 'class' =>'btn btn-link remove-button' )); ?>
                                </div>
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
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
            </div>    
       </div>     
      
        <div style="clear:both"></div>
       
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
