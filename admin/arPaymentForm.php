<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('ARPayment.class.php');
$arPayment = createObjAndAddToCol( new ARPayment()); 
$customer = createObjAndAddToCol( new Customer()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$currency = createObjAndAddToCol( new Currency()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 
$salesOrder = createObjAndAddToCol( new SalesOrder()); 
$truckingServiceOrderInvoice = createObjAndAddToCol( new TruckingServiceOrderInvoice()); 
$emklOrderInvoice = createObjAndAddToCol( new EMKLOrderInvoice()); 
$cashBank = createObjAndAddToCol( new CashBank());
$tax = createObjAndAddToCol(new Tax()); 

$obj= $arPayment;
$ar = $obj->getARObj();
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'arPaymentList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$rsARPaymentDetail = array();
$rsARPaymentMethodDetail = array();
$rsARDP = array();
$rsARCost = array();
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
    
	$rsARPaymentDetail = $obj->getDetailById($id); 
    
    if (empty($rs[0]['nettingkey'])){ 
        
        if(ADV_FINANCE && TEST_VOUCHER){ 
            $rsARPaymentMethodDetail = $obj->getPaymentVoucherDetail($id);  
            $arrAvailableVoucher = $class->convertForCombobox($rsARPaymentMethodDetail,'cashbankvoucherkey','voucherlabel');  
            
            $existingVoucherKey = array_column($rsARPaymentMethodDetail,'cashbankvoucherkey');
            
            $otherVoucher = $cashBank->getAvailableVoucher($rs[0]['customerkey'],
                                                           ' and  '.$cashBank->tableName.'.credittype = 1 and '.$cashBank->tableName.'.pkey not in ('.$obj->oDbCon->paramString($existingVoucherKey,',').')',
                                                           true,
                                                           1);
                  
            foreach($otherVoucher as $voucherItem){ 
                $arrAvailableVoucher[$voucherItem['pkey']]['label'] = $voucherItem['voucherlabel'];
                $arrAvailableVoucher[$voucherItem['pkey']]['rel'] = array('rel-amount' => $voucherItem['outstanding']); 
            }  
        }else{ 
			$rsARPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
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
        
        $rsARPaymentMethodDetail = $arrNettingPayment;
    }
    
    
    $rsARDP = $obj->getDownpaymentDetail($id);  
    $rsARCost = $obj->getCostDetail($id);  
	  
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCurrentCustomerKey'] = $rsCustomer[0]['pkey'] ;   
	$_POST['hidCurrentCustomerName'] = $rsCustomer[0]['name'] ; 
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['trDesc'] = $rs[0]['trnotes'];
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']); 
	$_POST['totalDiscount'] = $obj->formatNumber($rs[0]['totaldiscount']); 
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;
	$_POST['pph23'] =  $obj->formatNumber($rs[0]['prepaidtax23']) ;
   	$_POST['selWarehouseKey'] = $rs[0]['warehousekey'];  
	$_POST['chkDatePeriod'] = $rs[0]['usedateperiod'];   
	$_POST['trStartDate'] = $obj->formatDBDate($rs[0]['startdateperiod'],'d / m / Y', array('returnOnEmpty' => true, 'value' => '00 / 00 / 0000'));
	$_POST['trEndDate'] = $obj->formatDBDate($rs[0]['enddateperiod'],'d / m / Y', array('returnOnEmpty' => true, 'value' => '00 / 00 / 0000'));
   	$_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment']);  
	 
    $_POST['selCurrency'] = $rs[0]['currencykey']; 
    $_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'],2);
	$_POST['hidCurrentCurrencyKey'] = $rs[0]['currencykey'] ;      
	$_POST['hidCurrentCurrencyRate'] = $rs[0]['rate'] ;      
    
 	$editCurrencyInactiveCriteria = ' or  '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);  
 	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';

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

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 
$arrCurrency = $currency->generateComboboxOpt(null,array('criteria' =>' and ('.$currency->tableName.'.statuskey = 1)')); 
 
//if(empty($rs[0]['nettingkey']))
//	$arrPaymentMethod = $paymentMethod->generateComboboxOpt(null,array('criteria' => ' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'));
//else
//	$arrPaymentMethod = $paymentMethod->generateComboboxOpt(array('data' => NETTING_PAYMENT));  

$rsPaymentMethod = (empty($rs[0]['nettingkey'])) ? $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')',' order by '.$paymentMethod->tableName.'.name asc') : NETTING_PAYMENT;
$arrPaymentMethod = $obj->convertForCombobox($rsPaymentMethod,'pkey','name');    
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
                        CURRENCY : <?php echo json_encode(CURRENCY); ?>, 
                        ADV_FINANCE : <?php echo (ADV_FINANCE) ? "true" : "false"; ?>,
                        USE_STORAGE : <?php echo ($useStorage) ? "true" : "false"; ?>
                        };
        
         var arPayment = new ARPayment(tabID,tablekey, <?php echo json_encode($rs); ?>,varConstant, "<?php echo $obj->uploadFileFolder; ?>", <?php echo json_encode($rsItemFile); ?>);
    
         prepareHandler(arPayment);

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
                              <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['note']); ?></div> 
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
                                            <ul class="file-list"></ul>
                                            <div style="clear:both; height:1em; "></div>
                                            <div class="file-uploader">
                                                <noscript>
                                                    <p>Please enable JavaScript to use file uploader.</p>
                                                </noscript>
                                            </div>
                                        </div>
                                        <!-- file uploader -->
                                        <?php if (!empty($rs) && in_array($rs[0]['statuskey'], array(2))) {
                                            echo $obj->inputButton('btnUpdateFile', $obj->lang['update'], array('allowedStatusForEdit' => array(1, 2, 3), 'class' => 'btn btn-primary btn-second-tone'));
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
                            <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['arCode']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:130px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:130px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['discount']); ?></div>
                            <div class="div-table-col detail-col-header" style="width:130px; text-align:right;"><?php echo ucwords($obj->lang['payingSettlement']); ?></div>
                            <!--<div class="div-table-col detail-col-header" style="width:100px;"><?php echo ucwords($obj->lang['PPhType']); ?></div> -->
                            <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['PPhValue']); ?></div>
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0"')); ?></div>
                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                          </div>
                    </div>      
                </div>
                
				<?php
                  	  
                    $totalRows = count($rsARPaymentDetail);
            
            
                    $rsARCol = $ar->searchDataRow(array('pkey','refkey', 'code', 'refcode', 'refcode2','rate','amount'),
                                                ' and '.$ar->tableName.'.pkey in ('.$obj->oDbCon->paramString( array_column($rsARPaymentDetail,'arkey'), ',').') ');
            
                    $rsARCol = array_column($rsARCol,null,'pkey');
            
                    for ($i=0;$i<=$totalRows; $i++){  
					    $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false;  
                        
                        $_POST['refCode[]']  = '';
                        $_POST['doNumber[]']  = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                        } else {  
						    //$rsAR = $ar->getDataRowById($rsARPaymentDetail[$i]['arkey']);  
                            $rsAR = $rsARCol[$rsARPaymentDetail[$i]['arkey']];
                            
                            $_POST['hidDetailKey[]'] =  $rsARPaymentDetail[$i]['pkey'];
                            $_POST['hidARKey[]'] =  $rsARPaymentDetail[$i]['arkey']; 
                            $_POST['arCode[]'] =  $rsAR['code'];
                            $_POST['refCode[]'] =  $rsAR['refcode'];
                            $_POST['refCode2[]'] =  $rsAR['refcode2'];
                            //$doNumber = $ar->getDoNumber($rsAR['refheaderkey']);
                      	    $_POST['arAmount[]'] =  $obj->formatNumber($rsAR['amount']);
                            $_POST['doNumber[]'] =  $rsAR['refcode2'];
		   	                $_POST['outstanding[]'] =   $obj->formatNumber($rsARPaymentDetail[$i]['outstanding']); 
                            $_POST['amount[]'] =   $obj->formatNumber($rsARPaymentDetail[$i]['amount']); 
                            $_POST['discount[]'] =   $obj->formatNumber($rsARPaymentDetail[$i]['discount']); 
                            $_POST['taxPPH[]'] =   $obj->formatNumber($rsARPaymentDetail[$i]['taxamount']);
                            $_POST['selPPhType[]'] = $rsARPaymentDetail[$i]['pphtype']; 
                            $_POST['chkPick[]'] =  1;
                             
                        }
                       
                 ?>        
                 
              <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col"  style="padding: 0.3em 0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row"> 
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputHidden('hidDetailKey[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                                        <?php echo $obj->inputText('arCode[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                                        <?php echo $obj->inputHidden('hidARKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail" style="width:130px;"><?php echo $obj->inputNumber('arAmount[]',array('overwritePost' => $overwrite, 'readonly' => true,'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                    <div class="div-table-col detail-col-detail" style="width:130px;"><?php echo $obj->inputNumber('outstanding[]',array('overwritePost' => $overwrite,'readonly' => true, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                    <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('discount[]',array('overwritePost' => $overwrite,  'disabled' => $disabled, 'etc' => 'style="text-align:right";')); ?></div> 
                                    <div class="div-table-col detail-col-detail" style="width:130px;"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => 'style="text-align:right";')); ?></div> 
                                    <!--<div class="div-table-col detail-col-detail"  style="width:100px;"><?php echo $obj->inputSelect('selPPhType[]', $arrPPh); ?></div>-->
                                    <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('taxPPH[]',array('overwritePost' => $overwrite,'disabled' => $disabled,  'etc' => 'style="text-align:right";')); ?></div> 
                                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="text-align:center; width: 35px;"><?php echo $obj->inputCheckBox('chkPick[]',  array('value'=> 1, 'disabled' => $disabled) ); ?></div>
                                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button')); ?> </div>
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
                                    <div class="div-table-col detail-col-detail row-header" style="width: 50px">
                                    <?php echo $obj->lang['si']; ?>
                                  </div>
                                    <?php } ?> 
                                    <div class="div-table-col detail-col-detail">    
                                    <?php echo $obj->inputText('doNumber[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                 
                                  </div>    
                                  <div class="div-table-col detail-col-detail"></div>
                            </div>
                            
                        </div> 
                            
                    </div>
                </div> 
                
                <?php  } ?>   
                   
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
                                        <?php echo $obj->inputNumber('totalReceived', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
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
                                           </div>                                   
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
                                $totalRows = count($rsARDP);
                                for($i=0;$i<=$totalRows;$i++) {
                                        $class =  'transaction-detail-row';
                                        $overwrite = true; 
                                        $disabled = false; 

                                        if ($i == $totalRows ){
                                            $class = 'downpayment-row-template row-template'; 
                                            $overwrite = false; 
                                            $disabled = true; 
                                        } else {   
                                            $_POST['hidDetailDownpaymentKey[]'] = $rsARDP[$i]['pkey'];
                                            $_POST['hidDownpaymentKey[]'] = $rsARDP[$i]['downpaymentkey'];
                                            $_POST['downpaymentCode[]'] = $rsARDP[$i]['refcode'];
                                            $_POST['downpaymentAmount[]'] = $obj->formatNumber($rsARDP[$i]['amount']); 
                                        }
                            ?> 

                            <div class="div-table-row form-group <?php echo $class; ?>">
                                <div class="div-table-col-3" style="text-align:right;">  
                                        <?php echo $obj->inputHidden('hidDetailDownpaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                        <?php echo $obj->inputHidden('hidDownpaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?> 
                                        <?php echo  $obj->inputText('downpaymentCode[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                </div>  
                                <div class="div-table-col-3" style="width:180px"> 
                                       <?php echo $obj->inputNumber('downpaymentAmount[]', array('overwritePost' => $overwrite, 'add-class'=>'mnv-detail-field', 'disabled' => $disabled, 'etc' => 'style="text-align:right;" ')); ?>
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

                                    $totalRows = count($rsARCost);
                                    for($i=0;$i<=$totalRows;$i++) {
                                            $class =  'transaction-detail-row';
                                            $overwrite = true; 
                                            $disabled = false; 

                                            if ($i == $totalRows ){
                                                $class = 'cost-row-template row-template'; 
                                                $overwrite = false; 
                                                $disabled = true; 
                                            } else {   
                                                $_POST['hidDetailCostKey[]'] = $rsARCost[$i]['pkey'];
                                                $_POST['hidCostKey[]'] = $rsARCost[$i]['costkey'];
                                                $_POST['costName[]'] = $rsARCost[$i]['costname'];
                                                $_POST['costAmount[]'] = $obj->formatNumber($rsARCost[$i]['amount']); 
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
                                            <?php echo $obj->inputCollapsibleNumber('totalPayment', array( 'readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
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
