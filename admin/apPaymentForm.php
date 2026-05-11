<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('APPayment.class.php');
$apPayment = createObjAndAddToCol( new APPayment()); 
$supplier = createObjAndAddToCol( new Supplier()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$currency = createObjAndAddToCol( new Currency()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 
$truckingServiceOrder = createObjAndAddToCol( new TruckingServiceOrder()); 
$truckingServiceWorkOrder = createObjAndAddToCol( new TruckingServiceWorkOrder()); 
$purchaseOrder = createObjAndAddToCol( new PurchaseOrder()); 
$emklPurchaseOrder = createObjAndAddToCol( new EMKLPurchaseOrder());
$cashBank = createObjAndAddToCol( new CashBank()); 
$tax = createObjAndAddToCol(new Tax()); 

$obj= $apPayment;
$ap = $obj->getAPObj();
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'apPaymentList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$rsAPPaymentDetail = array();
$rsAPPaymentMethodDetail = array();
$rsAPDP = array();
$rsAPCost = array();
$arrAvailableVoucher = array();
$rsItemFile = array();
$rsFileDetail = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['trStartDate'] = date('d / m / Y');
$_POST['trEndDate'] = date('d / m / Y'); 
$_POST['hidCurrentCurrencyKey'] = 1;  // default IDR
$_POST['hidCurrentCurrencyRate'] = 1;  // default IDR

$rs = prepareOnLoadData($obj);  
$maxPaymentDays = 14;
$daysInterval = $obj->loadSetting('ARAPPaymentDayInterval');
if(!empty($daysInterval)) $maxPaymentDays = $daysInterval;

$useStorage = $obj->useStorage;
    
//$decimalPrice = -2;// (empty($rs[0]['currencykey']) || $rs[0]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;   

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsAPPaymentDetail = $obj->getDetailById($id);
    
    if (empty($rs[0]['nettingkey'])){
        if(ADV_FINANCE && TEST_VOUCHER){ 
            $rsAPPaymentMethodDetail = $obj->getPaymentVoucherDetail($id);  
            $arrAvailableVoucher = $class->convertForCombobox($rsAPPaymentMethodDetail,'cashbankvoucherkey','voucherlabel');  
                        
            $existingVoucherKey = array_column($rsAPPaymentMethodDetail,'cashbankvoucherkey');
            $otherVoucher = $cashBank->getAvailableVoucher($rs[0]['supplierkey'],' and  '.$cashBank->tableName.'.credittype = -1 and '.$cashBank->tableName.'.pkey not in ('.$obj->oDbCon->paramString($existingVoucherKey,',').')',true,2);
            foreach($otherVoucher as $voucherItem){ 
                $arrAvailableVoucher[$voucherItem['pkey']]['label'] = $voucherItem['voucherlabel'];
                $arrAvailableVoucher[$voucherItem['pkey']]['rel'] = array('rel-amount' => $voucherItem['outstanding']); 
            }  
        }else{ 
        	$rsAPPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
		}
    }else{
        
        $arrNettingPayment = array();
        
        array_push($arrNettingPayment,
                    array(
                        'pkey' => 0,
                        'paymentkey' => -1,
                        'amount' => $rs[0]['grandtotal'],
                        
                    )
                  );
        
        $rsAPPaymentMethodDetail = $arrNettingPayment;
    }
     
    $rsAPDP = $obj->getDownpaymentDetail($id,'',false);
    $rsAPCost = $obj->getCostDetail($id);
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
	$_POST['supplierName'] = $rsSupplier[0]['name'] ;
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
	$_POST['hidCurrentCurrencyRate'] = $rs[0]['rate'] ;      
    $_POST['chkDatePeriod'] = $rs[0]['usedateperiod'];   
	$_POST['trStartDate'] = $obj->formatDBDate($rs[0]['startdateperiod'],'d / m / Y', array('returnOnEmpty' => true, 'value' => '00 / 00 / 0000'));
	$_POST['trEndDate'] = $obj->formatDBDate($rs[0]['enddateperiod'],'d / m / Y', array('returnOnEmpty' => true, 'value' => '00 / 00 / 0000'));
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment']);
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
    $editCurrencyInactiveCriteria = ' or  '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);  
  
     if(  in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding']))  && !empty($rs[0]['refkey'])){
        $_POST['hidRefKey'] = $rs[0]['refkey'] ;
        $rsJO = $truckingServiceOrder->getDataRowById($rs[0]['refkey']);
        $_POST['refCode'] = $rsJO[0]['code'] ;
    } 
    

    
    //update file 
    if($useStorage){ 
        $rsFileDetail = $obj->getFileDetail($id);
    }else{ 
        $rsItemFile = $obj->getItemFile($id);
    
        if (count($rsItemFile) > 0) {
            $sourcePath = $obj->defaultDocUploadPath . $obj->uploadFileFolder . $id;
            $destinationPath = $obj->uploadTempDoc . $obj->uploadFileFolder . $id;
            $obj->deleteAll($destinationPath);

            if (!is_dir($destinationPath))
                mkdir($destinationPath, 0755, true);

            $obj->fullCopy($sourcePath, $destinationPath);
        }
    }
} 

$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    

$rsPaymentMethod = (empty($rs[0]['nettingkey'])) ? $paymentMethod->getDataForCommboboxWithPrivileges($editPaymentMethodInactiveCriteria) : NETTING_PAYMENT;
$arrPaymentMethod = $obj->convertForCombobox($rsPaymentMethod,'pkey','name');    

$arrCurrency = $obj->convertForCombobox($currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1' . $editCurrencyInactiveCriteria.')'),'pkey','name');
$arrPPh = $tax->generateComboboxOpt(null, array('criteria' => ' and ( ' . $tax->tableName . '.typekey=' . $obj->oDbCon->paramString(TAX_TYPE['PPH']) . ' and ' . $tax->tableName . '.statuskey = 1)', 'order' => 'order by ' . $tax->tableName . '.orderlist asc, ' . $tax->tableName . '.name asc'));


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
            TABLEKEY : tablekey,
            CURRENCY : <?php echo json_encode(CURRENCY); ?>, 
            ADV_FINANCE : <?php echo (ADV_FINANCE) ? "true" : "false"; ?>, 
            USE_STORAGE : <?php echo ($useStorage) ? "true" : "false"; ?>
        };

         var apPayment = new APPayment(tabID, <?php echo json_encode($rs); ?>,varConstant, "<?php echo $obj->uploadFileFolder; ?>", <?php echo json_encode($rsItemFile); ?>);
    
         prepareHandler(apPayment);
           
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
    <?php echo $obj->inputHidden('hidCurrentCurrencyRate'); ?>
    
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
                                            <?php echo $obj->inputDate('trDate',array('etc' => 'max-days='.$maxPaymentDays)); ?> 
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
                                   
                                   <?php if (  in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) ) { ?>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobOrder']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $truckingServiceOrder, 
                                                                                'element' => array('value' => 'refCode',
                                                                                                   'key' => 'hidRefKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-trucking-service-order.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'statuskey' => '(3,4,5,6)' )
                                                                                                )
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div>  
                                   <?php } ?>
                                   
                                   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?> / <?php echo ucwords($obj->lang['currencyRate']); ?></label> 
                                        <div class="col-xs-9  mnv-currency"> 
                                           <div class="flex">
                                               <div><?php  echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency')); ?></div>
                                               <div class="consume"><?php echo $obj->inputDecimal('currencyRate', array('class'=>'form-control inputnumber input-currency-rate')); ?></div>
                                           </div>
                                        </div> 
                                    </div>
                                </div>
                        
                                   
                                    <div class="form-group <?php echo $obj->hideOnDisabled(); ?>" style="margin-bottom:1em"> 
                                            <div class="col-xs-12"> 
                                                <div class="flex">
                                                    <div><?php echo $obj->inputCheckBox('chkDatePeriod'); ?></div>
                                                    <div><?php echo $obj->inputDate('trStartDate',array('add-class' => 'import-date-period')); ?></div>
                                                    <div>-</div>
                                                    <div><?php echo $obj->inputDate('trEndDate',array('add-class' => 'import-date-period')); ?></div>
                                                    <div style="margin-left:1em"><?php echo $obj->inputButton('btnImport',$obj->lang['showAll'],array('class' =>'btn btn-primary btn-second-tone')); ?></div>
                                                </div> 
                                            </div> 
                                        </div>  
                    </div>
                    <div class="div-table-col"> 
                           <div class="div-tab-panel"> 
                              <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div> 
                               <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?> 
                            </div>   
 
                        <?php if($useStorage) {  ?>
                             <div id="file-update-ajax" class="div-tab-panel">
                                 <div class="div-table" style="width:100%"> 
                                    <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div> 
                                    <?php echo $obj->inputUploadFilePlugin($rs,$rsFileDetail, array('allowedStatusForEdit' => array(1,2,3))); ?> 
                                 </div>
                            </div>    
                        <?php }else { ?> 
                        
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['files']); ?></div> 
                            
                            <div class="form-group"> 
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['documentFiles']); ?></label> 
                                <div class="col-xs-9"> 
                                        <!-- file uploader --> 
                                        <div class="item-file-uploader">
                                            <ul class="file-list" ></ul>
                                            <div style="clear:both; height:1em; "></div>
                                            <div class="file-uploader">	
                                                <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                            </div>
                                        </div>  
                                        <!-- file uploader -->
                                        <?php if (!empty($rs) && in_array($rs[0]['statuskey'], array(2)) ) {
                                            echo $obj->inputButton('btnUpdateFile', $obj->lang['update'], array('allowedStatusForEdit' => array(1,2,3),'class' =>'btn btn-primary btn-second-tone'));
                                        } ?>
                                </div>  
                            </div> 
                            
					    </div> 
                        
                        <?php } ?> 
                        
                    </div>
                </div>    
        </div>   
                                    
        
        <div class="div-table mnv-transaction transaction-detail mnv-checkbox-group" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">  
                     <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['apCode']); ?></div> 
                            <div class="div-table-col detail-col-header" style="width:130px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:130px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['discount']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:130px; text-align:right;"><?php echo ucwords($obj->lang['payingSettlement']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px;"><?php echo ucwords($obj->lang['PPhType']); ?></div> 
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['PPhValue']); ?></div>
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0"')); ?></div>
                            <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col" ></div>
                       </div>
                    </div>        
                </div>
                
				<?php
                  	  
                    $totalRows = count($rsAPPaymentDetail); 
              
                    $rsAPCol = $ap->searchDataRow(array('pkey','refkey', 'code', 'refcode', 'refcode2','refinvoicecode','rate','amount'),
                                                ' and '.$ap->tableName.'.pkey in ('.$obj->oDbCon->paramString( array_column($rsAPPaymentDetail,'apkey'), ',').') ');
            
                    $rsAPCol = array_column($rsAPCol,null,'pkey');
            
                    for ($i=0;$i<=$totalRows; $i++){   
					    $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        
                        $_POST['refCode[]']  = '';
                        $_POST['refJOCode[]']  = '';
                        $_POST['refInvoiceCode[]']  = '';
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
                            $_POST['refInvoiceCode[]'] =  $rsAP['refinvoicecode'] ; 
                            $_POST['apAmount[]'] =  $obj->formatNumber($rsAP['amount']);
                            $_POST['outstanding[]'] =  $obj->formatNumber($rsAPPaymentDetail[$i]['outstanding']); 
                            $_POST['amount[]'] =   $obj->formatNumber($rsAPPaymentDetail[$i]['amount']);  
                            $_POST['discount[]'] =   $obj->formatNumber($rsAPPaymentDetail[$i]['discount']);
                            $_POST['taxPPH[]'] =   $obj->formatNumber($rsAPPaymentDetail[$i]['taxamount']); 
                            $_POST['selPPhType[]'] = $rsAPPaymentDetail[$i]['pphtype']; 
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
                                </div>  
                                <div class="div-table-col detail-col-detail" style="width:130px;"><?php echo $obj->inputNumber('apAmount[]',array('overwritePost' => $overwrite, 'readonly' => true, 'disabled' => $disabled, 'etc' => 'style="text-align:right"')); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:130px;"><?php echo $obj->inputNumber('outstanding[]',array('overwritePost' => $overwrite,'readonly' => true,  'disabled' => $disabled,'etc' => 'style="text-align:right"')); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('discount[]',array('overwritePost' => $overwrite,'disabled' => $disabled, 'etc' => 'style="text-align:right"; ')); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:130px;"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite,'disabled' => $disabled, 'etc' => 'style="text-align:right";')); ?></div> 
                                <div class="div-table-col detail-col-detail"  style="width:100px;"><?php echo $obj->inputSelect('selPPhType[]', $arrPPh); ?></div>
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
                                 <?php if (  in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) ) { ?> 
                                  <div class="div-table-col detail-col-detail" style="width: 20px"></div>
                                    <div class="div-table-col detail-col-detail row-header" style="width: 120px">
                                    <?php echo (PLAN_TYPE['categorykey'] == COMPANY_TYPE['trucking']) ? $obj->lang['jobOrderCode'] :  $obj->lang['invoiceReference'] ; ?>
                                  </div> 
                                    <div class="div-table-col detail-col-detail"  style="width: 200px">    
                                    <?php echo $obj->inputText('refJOCode[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                  </div> 
                                 <?php
                                   // kalo retail, refinvoicecodenya pake refcode2, nanti perlu dipindah semua ke refinvociecode
                                 if( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking']))) {
                                ?>
                                  <div class="div-table-col detail-col-detail row-header" style="width: 120px">
                                    <?php echo  $obj->lang['invoiceReference'] ; ?>
                                  </div> 
                                  <div class="div-table-col detail-col-detail"  style="width: 200px">    
                                    <?php echo $obj->inputText('refInvoiceCode[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                  </div> 
                                 <?php } ?> 
                                 <?php } ?> 
                                  <div class="div-table-col detail-col-detail" style="text-align:right">
                                     <?php if (PLAN_TYPE['categorykey'] == COMPANY_TYPE['retail']){ ?>   
                                       <div class="row-action-panel" rel-load="0" rel-key="<?php echo $rsAPPaymentDetail[$i]['apkey']; ?>">
                                        <div class="row-show-detail" rel-action="show"><?php echo $obj->lang['showDetail']; ?><i class="arrow-detail fas fa-sort-down" style="position: relative; bottom: 0.2em"></i> </div>
                                        <div class="row-show-detail hide" rel-action="hide"><?php echo $obj->lang['hideDetail']; ?><i class="arrow-detail fas fa-sort-up" style="position: relative; top: 0.2em"></i> </div>
                                       </div>   
                                     <?php } ?>    
                                 </div>
                            </div> 
                        </div>   
                        <div class="transaction-reference-panel">
                            <div class="div-table transaction-reference-table">
                                <div class="div-table-row col-header">
                                    <div class="div-table-col-3" style="width:5em; text-align:right"><?php echo $obj->lang['number']; ?></div>
                                    <div class="div-table-col-3"><?php echo $obj->lang['itemName']; ?></div>
                                    <div class="div-table-col-3" style=" text-align:right"><?php echo $obj->lang['qty']; ?></div>
                                    <div class="div-table-col-3" ><?php echo $obj->lang['unit']; ?></div>
                                    <div class="div-table-col-3" style=" text-align:right"><?php echo $obj->lang['price']; ?></div>
                                    <div class="div-table-col-3" style=" text-align:right"><?php echo $obj->lang['total']; ?></div>
                                </div>
                                 <div class="div-table-row reference-row-template">
                                    <div class="div-table-col-3 purchase-number-row " style="text-align:right"></div>
                                    <div class="div-table-col-3 purchase-item"></div>
                                    <div class="div-table-col-3 purchase-qty is-number" style="text-align:right"></div>
                                    <div class="div-table-col-3 purchase-unit" ></div>
                                    <div class="div-table-col-3 purchase-price is-number" style="text-align:right"></div>
                                    <div class="div-table-col-3 purchase-subtotal is-number" style="text-align:right"></div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>   
                 <?php }   ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;">
              <div class="add-row-panel flex">
                  <div><?php echo $obj->inputInteger('newRowQty', array('add-class' => 'mnv-new-row-qty', 'etc' => 'style="text-align:center; width: 5em"')); ?></div>
                  <div><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' =>'btn btn-primary btn-second-tone')); ?></div>
              </div>      
          </div>
              
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
                                    <?php echo $obj->lang['withholdingTax']; ?>
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
                                        <div class="text-green-avocado flex" style="justify-content:flex-end">
                                               <div class="outstanding-currency "></div> 
                                               <div class="outstanding-downpayment inputnumber">0</div> 
                                           </div>                                    </div>  
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
                                        ?>                                </div>  
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