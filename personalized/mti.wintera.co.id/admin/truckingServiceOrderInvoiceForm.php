<?php 
include '../../../_config.php'; 
require_once '../../../_include-v2.php'; 

includeClass('TruckingServiceOrderInvoice.class.php');
$truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice());
$salesOrderInvoiceReceipt =  createObjAndAddToCol(new SalesOrderInvoiceReceipt());
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());
$customer =  createObjAndAddToCol(new Customer());
$termOfPayment =  createObjAndAddToCol(new TermOfPayment());
$paymentMethod =  createObjAndAddToCol(new PaymentMethod());
$currency =  createObjAndAddToCol(new Currency());
$warehouse =  createObjAndAddToCol(new Warehouse());
$employee = createObjAndAddToCol(new Employee());
$location = createObjAndAddToCol(new Location());
        
$obj= $truckingServiceOrderInvoice;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'truckingServiceOrderInvoiceList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 
 
$editPaymentMethodInactiveCriteria = '';
$editPaymentToInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';
$customCodeInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = ''; 
$editCurrencyInactiveCriteria = '';
$usePPNDetail = $obj->loadSetting('usePPNDetail');
$useWorkOrderOption = $obj->loadSetting('useWorkOrderOptionInInvoice');

$_POST['trDate'] = date('d / m / Y');
$_POST['trDateCustomerTax'] =  date('d / m / Y');
$_POST['hidCurrentCurrencyKey'] = 1;  // default IDR
$_POST['trStartDate'] = date('d / m / Y');
$_POST['trEndDate'] = date('d / m / Y'); 


$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber'; 

$rsSalesOrderInvoiceDetail = array(); 
$rsPaymentMethodDetail = array();
$rsInvoiceDP = array();

$rs = prepareOnLoadData($obj);  

$downpaymentType = false;
$notDownpaymentField = '';
$showInvoiceRef = "display:none";

$arrInvoiceTo = array( '1' => $obj->lang['customer'],
                        '2' => $obj->lang['consignee']);

$showVA = 'display:none;';

$isActiveModuleTax = $obj->isActiveModule('invoicetax');

$tablekey = $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key'];

$rsFileDetail = array(); 
$rsInvoiceTaxFile = array();

// akan deprecated
$rsItemFile = array();
$rsItemFileTax = array();
$arrWorkOrder = array();
$rsWorkOrder = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	  
    $rsSalesOrderInvoiceDetail = $obj->getDetailWithRelatedInformation($id,'','order by orderlist asc');
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);  
    $rsReceipt = $salesOrderInvoiceReceipt->getInvoiceReceipt($id,' and '.$salesOrderInvoiceReceipt->tableName.'.statuskey in (2,3) ');
    $rsInvoiceDP = $obj->getDownpaymentDetail($id); 
    
    if($isActiveModuleTax){ 
        $rsInvoiceTax = $obj->getInvoiceTax($id); 
        foreach($rsInvoiceTax as $invoiceTaxRow)
            $rsInvoiceTaxFile = array_merge($rsInvoiceTaxFile, $invoiceTaxRow['files']); 
    }
        
    // sementara, taro di header berdaasrkan pilihan ceakan payment mthod saja dulu
    $rsCompanyBank = $paymentMethod->getDataRowById($rs[0]['companybankkey']);
    
	$_POST['invoicetaxnumber'] = $rs[0]['invoicetaxnumber'];
    $_POST['selCustomCode'] = $rs[0]['customcodekey']; 
    $_POST['selWarehouseKey'] = $rs[0]['warehousekey'];
	
    $_POST['chkDatePeriod'] = $rs[0]['usedateperiod'];   
	$_POST['trStartDate'] = $obj->formatDBDate($rs[0]['startdateperiod'],'d / m / Y', array('returnOnEmpty' => true, 'value' => '00 / 00 / 0000'));
	$_POST['trEndDate'] = $obj->formatDBDate($rs[0]['enddateperiod'],'d / m / Y', array('returnOnEmpty' => true, 'value' => '00 / 00 / 0000'));
		
    if(!empty($rs[0]['locationfromkey'])) {
        $rsLocationFrom = $location->getDataRowById($rs[0]['locationfromkey']);
        $_POST['locationFromName'] = $rsLocationFrom[0]['name'];
        $_POST['hidLocationFromKey'] = $rs[0]['locationfromkey'];
    }

    if(!empty($rs[0]['locationtokey'])) {
        $rsLocationTo = $location->getDataRowById($rs[0]['locationtokey']);
        $_POST['locationToName'] = $rsLocationTo[0]['name'];
        $_POST['hidLocationToKey'] = $rs[0]['locationtokey'];
    }
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate']);
	
    if (!empty($rsReceipt)){ 
       $_POST['receiptCode'] = $rsReceipt[0]['code'];
       $_POST['receiptDate'] =  $obj->formatDBDate($rsReceipt[0]['trdate']);
       $_POST['receivedDate'] =  $obj->formatDBDate($rsReceipt[0]['receiveddate']); 
       $_POST['recipientName'] = $rsReceipt[0]['recipientname'];
    } 
    
    $_POST['hidRefInvoiceKey'] = $rs[0]['refinvoicekey'];
    if (!empty($rs[0]['refinvoicekey'])){ 
       $rsRef = $obj->getDataRowById($rs[0]['refinvoicekey']);
       $_POST['invoiceRefCode']  = $rsRef[0]['code'];   
       $showInvoiceRef = "";
    } 
     
    $customertaxdate = (!empty($rs[0]['customertaxdate'])) ? $obj->formatDBDate($rs[0]['customertaxdate']) : date('d / m / Y');
    $_POST['trDateCustomerTax'] = $customertaxdate;
    
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCurrentCustomerName'] = $rsCustomer[0]['name'] ; 
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['hidCurrentCustomerKey'] = $rsCustomer[0]['pkey'] ;  
    
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']); 
    if ($rs[0]['finaldiscounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 
    
        
	$_POST['selFinalDiscountType'] = $rs[0]['finaldiscounttype'] ;
	$_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'],$finalDiscDecimal);
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']);
    
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax'];  
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
	
    $_POST['tax23Percentage'] = $obj->formatNumber($rs[0]['tax23percentage'],2);
	$_POST['tax23Value'] = $obj->formatNumber($rs[0]['tax23value']); 
	$_POST['trDesc'] = $rs[0]['trdesc']; 
    $_POST['selTermOfPayment'] =  $rs[0]['termofpaymentkey'];  
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;
    $_POST['hidModifiedOn'] = $rs[0]['modifiedon']; 
    
    $_POST['chkTax23'] = $rs[0]['usetax23'];    
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment']); 
    $_POST['stampFee'] = $obj->formatNumber($rs[0]['stampfee']); 
	 
    $_POST['selInvoiceTo'] = $rs[0]['invoiceto'];   
    $_POST['selInvoiceNotify'] = $rs[0]['invoicenotify'];     
    
    $_POST['vanumber'] = $rs[0]['vanumber'];
    $_POST['reference1'] = $rs[0]['reference1'];
	$_POST['chkUseNotify'] = $rs[0]['usenotify'];
	$_POST['selCurrency'] = $rs[0]['currencykey']; 
    $_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'],2);
	$_POST['hidCurrentCurrencyKey'] = $rs[0]['currencykey'] ;   
    $_POST['selBank'] = $rs[0]['companybankkey'];
    $_POST['chkDownpayment'] = $rs[0]['isdownpayment'];    
    if ($rs[0]['isdownpayment']){ 
        $downpaymentType = true;
        $notDownpaymentField = 'display:none;';   
    }
    
    if($rsCompanyBank[0]['isvirtualaccount'] == 1) $showVA = '';

    $_POST['invoiceConsigneeName'] = $rs[0]['invoiceconsigneename']; 
    $_POST['invoiceConsigneeAddress'] = $rs[0]['invoiceconsigneeaddress'];
    $_POST['invoiceNotifyConsigneeName'] = $rs[0]['invoiceconsigneenotifyname']; 
    $_POST['invoiceNotifyConsigneeAddress'] = $rs[0]['invoiceconsigneenotifyaddress'];		
	  
  if($obj->useStorage){ 
    $rsFileDetail = $obj->getFileDetail($id);
  }else{ 
        $rsItemFile = array();  
        if( !empty($rs[0]['file'])){
            $rsItemFile[0]['file'] =  $rs[0]['file'];

            $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
            $destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath); 
        }

        $rsItemFileTax = array();  
        if( !empty($rs[0]['filetax'])){
            $rsItemFileTax[0]['file'] =  $rs[0]['filetax'];

            $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileTaxFolder.$id;
            $destinationPath = $obj->uploadTempDoc.$obj->uploadFileTaxFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath); 
        }

  }

  if(!empty($rs[0]['invoicesignaturekey'])) {
    $rsEmployee = $employee->getDataRowById($rs[0]['invoicesignaturekey']);
    $_POST['invoiceSignatureName'] = $rsEmployee[0]['name'];
    $_POST['hidInvoiceSignatureKey'] = $rs[0]['invoicesignaturekey'];
}
	
 	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
    $customCodeInactiveCriteria = ' or  '.$customCode->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['customcodekey']);  
    $editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
    $editPaymentToInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['companybankkey']);
  	$editCurrencyInactiveCriteria = ' or  '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);
  
}
 

$rsTOP = $termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'),'pkey','name');    
$arrPaymentTo = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentToInactiveCriteria.')'),'pkey','name','',array('rel-va' => 'isvirtualaccount'));    
$arrCustomCode =  $class->convertForCombobox($customCode->searchData($customCode->tableName.'.reftabletype',$tablekey,true,' and ('.$customCode->tableName.'.statuskey = 1 ' . $customCodeInactiveCriteria.')', ' order by '.$customCode->tableName.'.orderlist asc'),'pkey','name');  
$arrTOP = $obj->convertForCombobox($rsTOP,'pkey','name'); 
$arrCurrency = $obj->convertForCombobox($currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1' . $editCurrencyInactiveCriteria.')'),'pkey','name');

$arrInvoiceType = array();
$defaultSelectOpt = [['pkey' => 0, 'code' => '-----']];
$arrInvoiceType[1] = 'Job Order';
if(!$usePPNDetail)
    $arrInvoiceType[2] = 'Biaya';
 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
     
	jQuery(document).ready(function(){  
	 	 
        var tabID = selectedTab.newPanel[0].id;
        
         var cashTOP = Array();
         <?php 
            for ($i=0;$i<count($rsTOP);$i++){
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
            }
         ?>
        
        var varConstant = {  
				CURRENCY : <?php echo json_encode(CURRENCY); ?>, 
				usePPNDetail : <?php echo json_encode($usePPNDetail); ?>,
                tablekey : <?php echo $tablekey; ?>
		};
        
        
        
        // akan deprecated
        var fileUpload = {  
				uploadFolder : "<?php echo $obj->uploadFileFolder; ?>",
				uploaderTarget: "item-file-uploader",
				rsFile :<?php echo json_encode($rsItemFile); ?>, 
		};
        var fileTaxUpload = {  
				uploadFolder : "<?php echo $obj->uploadFileTaxFolder; ?>",
				uploaderTarget: "item-file-tax-uploader",
				rsFile :<?php echo json_encode($rsItemFileTax); ?>, 
		};
        
        var truckingServiceOrderInvoice = new TruckingServiceOrderInvoice(tabID,cashTOP,varConstant,fileUpload,fileTaxUpload);
        prepareHandler(truckingServiceOrderInvoice);     

        var fieldValidation =  { 
                               code: { 
                                    validators: {
                                        notEmpty: {
                                            message: phpErrorMsg.code[1]
                                        }, 
                                    }
                                }, 

                               customerName: { 
                                    validators: {
                                        notEmpty: {
                                            message:  phpErrorMsg.customer[1]
                                        }, 
                                    }
                                },   
                            } ; 

        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
  
    }); 
	 
</script>

<style>
    .invoice-detail > .transaction-detail-row > .div-table-col {padding: 1em 0em !important /*background-color: transparent!important*/}    
    .invoice-detail .icon-col.align-top-adjust {padding-top: 1.6em !important}
</style>  
 
</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
		<?php prepareOnLoadDataForm($obj); ?>   
		<?php echo $obj->inputHidden('hidCurrentCustomerKey'); ?>
		<?php echo $obj->inputHidden('hidCurrentCustomerName'); ?> 
		<?php echo $obj->inputHidden('hidTotalBeforeTaxPPH23'); ?>
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
                                 <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?> <?php if ($isActiveModuleTax) echo ' / ' .ucwords($obj->lang['tax']); ?></label> 
                                <div class="col-xs-9"> 
                                    <div class="flex">
										<div class="consume"><?php echo $obj->inputAutoCode('code'); ?></div>
										<?php if ($isActiveModuleTax) { ?>
										<div>/</div>
										<div class="consume"><?php echo $obj->inputText('invoicetaxnumber', array('readonly' => true)); ?></div>
										<?php } ?>
									</div> 
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
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>  
                                </div> 
                            </div> 
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceType']); ?></label> 
                                <div class="col-xs-9"> 
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputSelect('selCustomCode', $arrCustomCode); ?></div>                                 
                                        <?php if(!$usePPNDetail) { ?>     
                                        <div style="margin-left:1em"><?php echo $obj->inputCheckBox('chkDownpayment'); ?></div>      
                                        <div><?php echo ucwords($obj->lang['partialInvoice']); ?></div>                           
                                        <?php } ?>                           
                                    </div> 
                                </div> 
                            </div>  
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['referenceNumber']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('reference1'); ?>  
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
                                                                                'callbackFunction' => 'getTabObj().updateCustomerInformation(this,event, ui)'
                                                                              )
                                                                        );  
                                            ?>
                                </div> 
                            </div> 
                             
                             <div class="form-group invoice-ref" style="<?php echo $showInvoiceRef; ?>">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceReference']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php  echo $obj->inputAutoComplete(array(   
                                                                                'element' => array('value' => 'invoiceRefCode',
                                                                                                   'key' => 'hidRefInvoiceKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-trucking-service-order-invoice.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'statuskey' => '2,3' )
                                                                                                )  
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
					    <div class="div-tab-panel"> 
                            <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['informationInThePrintOut']); ?></div> 
							 <div class="form-group " >
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceTo']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php  echo $obj->inputSelect('selInvoiceTo', $arrInvoiceTo , array("etc" => 'rel="to"')); ?>
                                </div> 
                            </div>
                            <div class="form-group consignee-information-to" >
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consigneeName']) ?></label>
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('invoiceConsigneeName');
                                    ?>
                                </div>
                            </div>
							 
                            <div class="form-group consignee-information-to" >
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consigneeAddress']) ?></label>
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputTextArea('invoiceConsigneeAddress', array('etc' => 'style="height:10em;"'));?>
                                </div>
                            </div>
							 
							 <div class="form-group" >
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['notify']) ?></label>
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputCheckBox('chkUseNotify'); ?>
                                </div>
                            </div>
							<div class="use-notify">
								 <div class="form-group " >
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['notifyTo']) ; ?></label> 
									<div class="col-xs-9" > 
										  <?php  echo $obj->inputSelect('selInvoiceNotify', $arrInvoiceTo , array("etc" => 'rel="notify"') ); ?>
									</div> 
								</div>
								<div class="form-group consignee-information-notify" >
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consigneeName']) ?></label>
									<div class="col-xs-9"> 
										<?php echo $obj->inputText('invoiceNotifyConsigneeName');
										?>
									</div>
								</div>
								<div class="form-group consignee-information-notify" >
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consigneeAddress']) ?></label>
									<div class="col-xs-9"> 
										<?php echo $obj->inputTextArea('invoiceNotifyConsigneeAddress', array('etc' => 'style="height:10em;"'));
										?>
									</div>
								</div>
							 </div>
							
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['paymentTo']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputSelect('selBank', $arrPaymentTo); ?>  
                                </div> 
                            </div> 
                            <div class="form-group va-col" style="<?php echo $showVA; ?>">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['virtualAccount']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('vanumber'); ?>  
                                </div> 
                            </div> 

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['signerOfTheInvoice']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $employee,
                                                                                'element' => array('value' => 'invoiceSignatureName',
                                                                                                   'key' => 'hidInvoiceSignatureKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-employee.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                )
                                                                              )
                                                                        );  
                                            ?>
                                </div> 
                            </div>
                            

                         </div> 
 
                    
					 
						  
                </div>
                 
                <div class="div-table-col"> 
                        <?php if (!empty($rs) && $rs[0]['statuskey'] > 1) {?>
                       <div class="div-tab-panel"> 
                            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['invoiceReceipt']); ?></div> 
                            <div class="form-group"> 
                               <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('receiptCode'); ?>
                                </div> 
                            </div>     
                            <div class="form-group"> 
                               <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['dateSent']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('receiptDate'); ?>
                                </div> 
                            </div>    
                            <div class="form-group"> 
                               <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['dateReceived']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('receivedDate'); ?>
                                </div> 
                            </div>    
                            <div class="form-group"> 
                               <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recipient']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('recipientName'); ?>
                                </div> 
                            </div>   
                         </div> 
                        <?php } ?>
                    
                         <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div> 
                             <div class="form-group"> 
                                    <div class="col-xs-12"> 
                                         <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                    </div> 
                              </div>  
                         </div>
					
                    <?php if($obj->useStorage) { ?> 
                             <div id="file-update-ajax" class="div-tab-panel">
                                 <div class="div-table" style="width:100%"> 
                                    <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div> 
                                    <?php echo $obj->inputUploadFilePlugin($rs,$rsFileDetail, array('allowedStatusForEdit' => array(1,2,3))); ?> 
                                 </div>
                            </div>    
                    <?php  } else { ?>
                         
                    
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
                                                <?php if (!empty($rs) && in_array($rs[0]['statuskey'], array(2,3)) ) {
                                                     echo $obj->inputButton('btnUpdateFile', $obj->lang['update'], array('allowedStatusForEdit' => array(1,2,3),'class' =>'btn btn-primary btn-second-tone'));
                                                } ?>
                                        </div>  
                                      </div>   
                                      <div class="form-group" style="margin-top:2em"> 
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxInvoice']); ?></label> 
                                        <div class="col-xs-9"> 
                                                <!-- file uploader --> 
                                                <div class="file-uploader-ui item-file-tax-uploader">
                                                    <ul class="file-list" ></ul>
                                                    <div style="clear:both; height:1em; "></div>
                                                    <div class="file-uploader">	
                                                        <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                                    </div>
                                                 </div>  
                                                <!-- file uploader -->
                                                <?php if (!empty($rs) && in_array($rs[0]['statuskey'], array(2,3)) ) {
                                                     echo $obj->inputButton('btnUpdateFileTax', $obj->lang['update'], array('allowedStatusForEdit' => array(1,2,3),'class' =>'btn btn-primary btn-second-tone'));
                                                } ?>
                                        </div>  
                                      </div>   


                                 </div>
                    
                     <?php } ?> 
                    

                        <?php if ($isActiveModuleTax) { ?>
                             <div class="div-tab-panel"> 
                                <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['taxInvoice']); ?></div>  
                                    <ul class="file-list"> 
                                     <?php for($i=0;$i<count($rsInvoiceTaxFile);$i++) {
                                        echo '<li><div class="panel"><div class="file-uploader-description"><a href="/download.php?filename=invoice-tax/'.$rsInvoiceTaxFile[$i]['refkey'].'/'.$rsInvoiceTaxFile[$i]['file'].'" target="_blank" title="'.$rsInvoiceTaxFile[$i]['file'].'">'.$rsInvoiceTaxFile[$i]['file'].'</a></div></div></li>';
                                    } ?>     
                                    </ul> 
                             </div> 
                         <?php } ?> 
                </div>   
             </div>
      </div>

          <div class="form-group <?php echo $obj->hideOnDisabled(); ?>" style="margin-left:0.2em;margin-bottom:1em"> 
		<div class="col-xl-12"> 
            <div class="flex">
                <div><?php echo $obj->inputCheckBox('chkDatePeriod'); ?></div>
                <div><?php echo $obj->inputDate('trStartDate',array('add-class' => 'import-date-period')); ?></div>
                <div>-</div>
                <div><?php echo $obj->inputDate('trEndDate',array('add-class' => 'import-date-period')); ?></div>
                <div> </div>
               <div>
                    <?php echo $obj->inputAutoComplete(
                        array(
                            'objRefer' => $location,
                            'element' => array(
                                'value' => 'locationFromName',
                                'key' => 'hidLocationFromKey'
                            ),
                            'source' => array(
                                'url' => 'ajax-location.php',
                                'data' => array('action' => 'searchData', 'statuskey' => '1')
                            )
                        )
                    );
                    ?>
                </div>
                <div>-</div>
                <div>
                    <?php echo $obj->inputAutoComplete(
                        array(
                        'objRefer' => $location,
                        'element' => array(
                            'value' => 'locationToName',
                            'key' => 'hidLocationToKey'
                        ),
                        'source' => array(
                            'url' => 'ajax-location.php',
                            'data' => array('action' => 'searchData', 'statuskey' => '1')
                          )
                        )
                    );
                    ?>
                </div>
                <div style="margin-left:1em"><?php echo $obj->inputButton('btnImport',$obj->lang['showAll'],array('class' =>'btn btn-primary btn-second-tone')); ?></div>
            </div> 
		</div> 
	</div> 
       
       
      <div class="mnv-checkbox-group">
        <div class="div-table mnv-transaction invoice-detail transaction-detail row-panel" style="width:100%; border-bottom:1px solid #333; "  attr-level="0">
                <div class="div-table-row"> 
                    <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row"> 
                                <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['jobOrderCode']); ?> / <?php echo ucwords($obj->lang['cost']); ?></div> 
                                <!--<div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['description']); ?></div>-->
                                <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['invoiceIssued']); ?></div>
                                <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['total']); ?></div>
                            </div>
                        </div>
                    </div>  
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col" style="width: 25px" > <?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0"')); ?></div> 
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"  style="width: 45px"></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col"></div>
                </div>
    
				<?php 
                           
                    $totalRows = count($rsSalesOrderInvoiceDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  

                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $readonly = true;
                        $disabled = false;
                        $sokey = '';
                        $showSO = '';
                        $showCost = 'display:none;';    
                        $soDisable = false;
                        
                        $rsServiceDetail = array();
                        $totalDetailRows = 0 ;
                        $optionRows = 'display:none';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template row-template';
                            $overwrite = false;
                            $disabled = true;

                        } else {  
                            
                            $_POST['hidSalesOrderKey[]'] = '';
                            $_POST['salesOrderCode[]'] =  '';
                            $_POST['salesOrderDate[]'] =  ''; 
                            $_POST['hidItemKey[]'] =  ''; 
                            $_POST['itemName[]'] =  ''; 
                            
                            $readonly = false;

                            $showSO = 'display:none;';
                            $showCost = '';     

                            if ( $rsSalesOrderInvoiceDetail[$i]['invoicetype'] == 1){ 
                                
                                $readonly = (!$downpaymentType) ? true : false; 
                                $showSO = '';
                                $showCost = 'display:none;';

                                //$rsSO = $truckingServiceOrder->getDataRowById($rsSalesOrderInvoiceDetail[$i]['salesorderkey']); 
                                $sokey = $rsSalesOrderInvoiceDetail[$i]['salesorderkey'];
                                $_POST['hidSalesOrderKey[]'] =  $sokey; 
                                $_POST['salesOrderCode[]'] =  $rsSalesOrderInvoiceDetail[$i]['socode'];
                                $_POST['salesOrderDate[]'] =  $obj->formatDBDate($rsSalesOrderInvoiceDetail[$i]['sodate']);  
                                $_POST['salesOrderSubtotal[]'] =   $obj->formatNumber($rsSalesOrderInvoiceDetail[$i]['salesordergrandtotal']);
                                $_POST['salesOrderDownpayment[]'] =   $obj->formatNumber($rsSalesOrderInvoiceDetail[$i]['salesordertotalinvoiced']);
                                    $_POST['selWorkOrderKey[]'] = $rsSalesOrderInvoiceDetail[$i]['workorderkey']; 
                                
                            } else{
                                $_POST['hidItemKey[]'] =  $rsSalesOrderInvoiceDetail[$i]['itemkey']; 
                                $_POST['itemName[]'] =  $rsSalesOrderInvoiceDetail[$i]['itemname'];  
                            }
                            
                            $_POST['hidDetailKey[]'] = $rsSalesOrderInvoiceDetail[$i]['pkey'];
                            $_POST['selInvoiceType[]'] = $rsSalesOrderInvoiceDetail[$i]['invoicetype'];
                            $_POST['doNumberDetail[]'] =  $rsSalesOrderInvoiceDetail[$i]['donumber'];
                            $_POST['detailNote[]'] =  $rsSalesOrderInvoiceDetail[$i]['description'];    
                            $_POST['amount[]'] =   $obj->formatNumber($rsSalesOrderInvoiceDetail[$i]['amount']);

                            $_POST['hidOrderList[]'] = $rsSalesOrderInvoiceDetail[$i]['orderlist'];
                        
                            
                            // Detail Service 
                            if (!empty($sokey) && !$downpaymentType){
                                $rsServiceDetail = $obj->getItemDetail($rsSalesOrderInvoiceDetail[$i]['pkey']);
                                $totalDetailRows = count($rsServiceDetail); 
                                $optionRows = '';
                            }

                            if($useWorkOrderOption) {
                                $rsWorkOrder = $truckingServiceWorkOrder->searchDataRow(array($truckingServiceWorkOrder->tableName.'.pkey',$truckingServiceWorkOrder->tableName.'.code',$truckingServiceWorkOrder->tableName.'.refkey'), ' and '  . $truckingServiceWorkOrder->tableName.'.refkey = '. $obj->oDbCon->paramString($sokey) .' and '. $truckingServiceWorkOrder->tableName .'.statuskey = 3');
                            }
                        } 
                        if($useWorkOrderOption) {
                            $arrWorkOrder = array_merge(
                                $defaultSelectOpt,
                                $rsWorkOrder
                            );
                            $arrWorkOrder = $obj->convertForCombobox($arrWorkOrder, 'pkey', 'code');
                        }
						
                  ?>
                
                <div class="div-table-row <?php echo $class; ?>"> 
                     <div class="div-table-col detail-col-detail" style="padding:0">
                       <!-- <div style="background-color:#dedede; border-radius:0.5em; padding: 0.5em">-->
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row"> 
                                    <div class="div-table-col detail-col-detail" style="width:100px;"> 
                                        <?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled )); ?>
                                        <?php echo $obj->inputSelect('selInvoiceType[]', $arrInvoiceType, array('overwritePost' => $overwrite, 'disabled' => ($soDisable) ? $soDisable : $disabled )); ?>
                                    </div>
                                    <div class="div-table-col detail-col-detail">
                                        <div class="type-1" style="<?php echo $showSO ; ?>"> 
                                            <div class="flex">
                                                <div><?php echo $obj->inputText('salesOrderCode[]',array('overwritePost' => $overwrite,'etc' => 'placeholder="'.$obj->lang['pleasestarttyping'].'"', 'disabled' => $disabled )); ?><?php echo $obj->inputHidden('hidSalesOrderKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?></div>
                                                <?php if($useWorkOrderOption) { ?>
                                                    <div style="width:100px;"> 
                                                        <?php echo $obj->inputSelect('selWorkOrderKey[]', $arrWorkOrder, array('overwritePost' => $overwrite, 'disabled' => $disabled )); ?>
                                                    </div>
                                                <?php } ?>
                                                <div style="width: 100px"><?php echo $obj->inputText('salesOrderDate[]', array('overwritePost' => $overwrite, 'readonly' => true,  'disabled' => $disabled, 'etc' => 'style="text-align:center;"  placeholder="'.$obj->lang['jobOrderDate'].'"' )); ?></div>
                                                <div class="consume"><?php echo $obj->inputText('doNumberDetail[]', array('overwritePost' => $overwrite, 'readonly' => true,  'disabled' => $disabled,'etc' => 'placeholder="'.$obj->lang['si'].'"')); ?></div>
                                            </div> 
                                        </div>
                                        <div class="type-2"  style="<?php echo $showCost ; ?>"><?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'etc' => 'placeholder="'.$obj->lang['pleasestarttyping'].'"',  'disabled' => $disabled)); echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?></div>
                                    </div>    
                                    <div class="div-table-col detail-col-detail" style="width:110px; text-align:right;"><?php echo $obj->inputNumber('salesOrderSubtotal[]', array('overwritePost' => $overwrite, 'readonly'=>true,  'disabled' => $disabled, 'etc' => 'style="text-align:right;" ' )); ?></div> 
                                    <div class="div-table-col detail-col-detail" style="width:100px; text-align:right;"><?php echo $obj->inputNumber('salesOrderDownpayment[]', array('overwritePost' => $overwrite, 'readonly'=>true,  'disabled' => $disabled, 'etc' => 'style="text-align:right;" ' )); ?></div> 
                                    <div class="div-table-col detail-col-detail" style="width:110px; text-align:right;"><?php echo $obj->inputNumber('amount[]', array('overwritePost' => $overwrite, 'readonly'=>$readonly,  'disabled' => $disabled, 'etc' => 'style="text-align:right;"' )); ?></div> 
                                </div> 
                            </div> 

                            <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                  <div class="div-table-col detail-col-detail"><?php echo $obj->inputTextArea('detailNote[]',array('overwritePost' => $overwrite, 'etc' => 'style="height:6em" placeholder="'.$obj->lang['note'].'"')); ?></div> 
                                  <!--<div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top;"></div>-->
                            </div>
                        </div>                  
                            <div class="options-row" style="<?php echo $optionRows ?>" > 
                                <div style="clear:both; height:1em" ></div>
                                <div class="div-table mnv-transaction transaction-detail" style="width: 100%;" attr-level="1" attr-group="hidDetailItemKey"> 
                                    <div class="div-table-row"> 
                                        <div class="div-table-col detail-col-detail col-header no-border <?php echo $obj->hideOnDisabled(); ?> " style="width: 3em; "></div> 
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:4em; text-align:right"><?php echo ucwords($obj->lang['party']); ?></div> 
                                        <div class="div-table-col detail-col-detail col-header no-border"><?php echo ucwords($obj->lang['services']); ?></div>
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:12em;"><?php echo ucwords($obj->lang['alias']); ?></div>
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:8em; text-align:right"><?php echo ucwords($obj->lang['price']); ?></div>  
                                        <?php if ($usePPNDetail) {?>
                                             <div class="div-table-col detail-col-detail col-header no-border" style="width:100px; text-align:right; padding-right:0;"></div>
                                             <div class="div-table-col detail-col-detail col-header no-border" style="width:40px; text-align:right; padding-left:0.2em;">Disc</div>
                                                        <?php } ?>
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:10em; text-align:right"><?php echo ucwords($obj->lang['subtotal']); ?></div> 
                                        <?php if ($usePPNDetail) {?>
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:5em; text-align:right"><?php echo ucwords($obj->lang['PPN']); ?> %</div> 
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:3em; text-align:center">Inc.</div> 
											 
											<div class="div-table-col detail-col-detail col-header no-border" style="width:9em; text-align:right"><?php echo ucwords($obj->lang['total']); ?></div> 
                                        <?php } ?>
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:5em; "><?php echo ucwords($obj->lang['tax23']); ?></div>  
                                    </div> 

                                    <?php 

                                        for ($j=0;$j<=$totalDetailRows; $j++){   
                                                
                                            $classDetail =  'transaction-detail-row';
                                            $overwriteDetail = true; 
                                            $disabledDetail = false; 

                                            if ($j == $totalDetailRows ){  
                                                $classDetail = 'service-row-template row-template';  
                                                $overwriteDetail = false;
                                                $disabledDetail = true; 
                                            } else {   
                                                
                                                $classDetail = 'service-detail-row ' . $classDetail;
                                                
                                                $_POST['hidDetailItemKey[]'] =  $rsServiceDetail[$j]['pkey'];
                                                $_POST['hidRefSODetailKey[]'] = $rsServiceDetail[$j]['refsodetailkey']; 
                                                $_POST['qtyDetail[]'] =  $obj->formatNumber($rsServiceDetail[$j]['qtyinbaseunit'],0);
                                                $_POST['hidItemDetailKey[]'] =  $rsServiceDetail[$j]['itemkey']; 
                                                $_POST['itemNameDetail[]'] =   $rsServiceDetail[$j]['itemname']; 
                                                $_POST['itemNameAliasDetail[]'] =   $rsServiceDetail[$j]['aliasname']; 
                                                $_POST['priceInUnitDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['priceinunit']);
                                                $_POST['subtotalDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['total']);
                                                $_POST['taxDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['taxdetail'],2);
                                                $_POST['taxValueDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['taxdetailvalue']);
                                                $_POST['beforeTaxDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['beforetaxdetailvalue']);
                                                $_POST['afterTaxDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['aftertaxdetailvalue']);
                                                $_POST['discountValueDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['discountdetailvalue']);
                                                $_POST['selDiscountDetailType[]'] = $obj->formatNumber($rsServiceDetail[$j]['discountdetailtype']);
                                                $_POST['chkService[]'] =  1;
                                                $_POST['chkIsTax23[]'] = $rsServiceDetail[$j]['istax23'];
                                                $_POST['chkIncludeTaxDetail[]'] = $rsServiceDetail[$j]['ispriceincludetax'];
                                            } 
                                            
                                    ?>
                                    <div class="div-table-row <?php echo $classDetail; ?>" >
                                        <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> " style="text-align:center">
                                            <?php echo $obj->inputHidden('hidDetailItemKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?><?php echo $obj->inputHidden('hidRefSODetailKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                            <?php echo $obj->inputCheckBox('chkService[]',array( 'disabled' => $disabledDetail)); ?>
                                        </div>
                                        <div class="div-table-col-3" >
                                            <?php echo $obj->inputInteger('qtyDetail[]', array('overwritePost' => $overwriteDetail, 'add-class' => 'label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ')); ?>
                                        </div>
                                        <div class="div-table-col-3">
                                            <?php echo $obj->inputText('itemNameDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'class' => 'form-control label-style',  'disabled' => $disabledDetail)); ?>
                                            <?php echo $obj->inputHidden('hidItemDetailKey[]', array('overwritePost' => $overwriteDetail,  'disabled' => $disabledDetail)); ?>
                                        </div>
                                        <div class="div-table-col-3" >
                                            <?php echo $obj->inputText('itemNameAliasDetail[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control label-style',  'disabled' => $disabledDetail)); ?>
                                        </div>
                                        <div class="div-table-col-3">
                                            <?php echo $obj->inputNumber('priceInUnitDetail[]', array('readonly' => true,'overwritePost' => $overwriteDetail,'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ' )); ?>
                                        </div>
                                        <?php if ($usePPNDetail) {?>
                                            <div class="div-table-col-3 "><?php echo $obj->inputNumber('discountValueDetail[]', array('overwritePost' => $overwriteDetail,'class' => 'form-control inputnumber label-style', 'etc' => 'style="text-align:right;"', 'disabled' => $disabledDetail)); ?></div>
                                            <div class="div-table-col-3"><?php echo $obj->inputSelect('selDiscountDetailType[]',$obj->arrDiscountType, array('overwritePost' => $overwriteDetail, 'add-class' => 'label-style',  'disabled' => $disabledDetail)); ?></div>
                                        <?php } ?>
                                        <div class="div-table-col-3">
                                            <?php echo $obj->inputNumber('subtotalDetail[]', array('readonly' => true,'overwritePost' => $overwriteDetail,'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
                                        </div>
                                        <?php if ($usePPNDetail) {?>
                                            <div class="div-table-col-3" >
                                                <?php echo $obj->inputDecimal('taxDetail[]', array('overwritePost' => $overwriteDetail, 'add-class' => 'label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ')); ?>
                                                <?php echo $obj->inputHidden('taxValueDetail[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control inputnumber label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ')); ?>
                                            </div>
	                                        <div class="div-table-col-3 " style="text-align:center">
												<?php  echo $obj->inputCheckBox('chkIncludeTaxDetail[]',array('disabled' => $disabledDetail)); ?>
											</div>
											<div class="div-table-col-3">
												<?php echo $obj->inputNumber('afterTaxDetail[]', array('readonly' => true,'overwritePost' => $overwriteDetail,'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
												<?php echo $obj->inputHidden('beforeTaxDetail[]', array('readonly' => true,'overwritePost' => $overwriteDetail,'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>

											</div>                                        <?php } ?>
                                        <div class="div-table-col-3 " style="text-align:center">
                                            <?php  echo $obj->inputCheckBox('chkIsTax23[]',array( 'disabled' => $disabledDetail)); ?>
                                        </div>
                                    </div> 
                                    <?php } ?>
                                </div>   
                        </div>   
                        <!--</div> -->
                    </div>
                    <div class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputCheckBox('chkPick[]', array('value' => 1, 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"  style="width: 45px">
                            <div class="flex">
                                <?php echo $obj->inputHidden('hidOrderList[]', array('readonly' => true,'overwritePost' => $overwriteDetail, 'class' => 'hid-order-list' )); ?>
                                <i class="fas arrow-nav fa-arrow-circle-up" rel="-1" rel-ctr="numberDetail[]"></i>
                                <i class="fas arrow-nav fa-arrow-circle-down " rel="1" rel-ctr="numberDetail[]"></i>
                            </div> 
                        </div> 
                    <div class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabindex="-1"')); ?></div>
                </div> 
             
                <?php } ?> 
                   
         </div>         
      </div>
      
        <div style="clear:both; height:1em;"></div> 
        <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' =>'btn btn-primary btn-second-tone')); ?></div>
       
          <div> 
              <div style="width:350px; margin-left:2em; float:right;"> 
               <!-- <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:50px; height: 1em"></div>-->
                <div class="div-table" style="width:100%" >
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['payment']); ?> 
                        </div>  
                        <div class="div-table-col-3" style="width:180px;"> 
                             <?php echo  $obj->inputSelect('selTermOfPayment', $arrTOP); ?>
                        </div> 
                        <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                    </div> 
                 </div>    
                   <div class="mnv-total-group mnv-downpayment">  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                           <?php echo $obj->lang['downpayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:180px"> 
                                            <?php echo $obj->inputCollapsibleNumber('totalDownpayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
                                    </div> 
                                    <div class="div-table-col-3  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                              </div>
                        </div>
                         
                        <div class="mnv-total-group-detail ">
                        <div class="div-table transaction-detail" style="width: 100%">
                            <?php  
                                $totalRows = count($rsInvoiceDP);
                                for($i=0;$i<=$totalRows;$i++) {
                                        $class =  'transaction-detail-row';
                                        $overwrite = true; 
                                        $disabled = false; 

                                        if ($i == $totalRows ){
                                            $class = 'downpayment-row-template row-template'; 
                                            $overwrite = false; 
                                            $disabled = true; 
                                        } else {   
                                            $_POST['hidDetailDownpaymentKey[]'] = $rsInvoiceDP[$i]['pkey'];
                                            $_POST['hidDownpaymentKey[]'] = $rsInvoiceDP[$i]['downpaymentkey'];
                                            $_POST['downpaymentCode[]'] = $rsInvoiceDP[$i]['refcode'];
                                            $_POST['downpaymentAmount[]'] = $obj->formatNumber($rsInvoiceDP[$i]['amount']); 
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
                    <div class="mnv-total-group mnv-payment-method cashTOP" >  
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
                                            $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount']); 
                                        }
                            ?> 

                            <div class="div-table-row form-group <?php echo $class; ?>">
                                <div class="div-table-col-3" style="text-align:right;">  
                                        <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                        <?php echo  $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                </div>  
                                <div class="div-table-col-3" style="width:180px"> 
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
                    <div class="div-table" style="width:100%;"> 
                            <div class="div-table-row  form-group"> 
                                <div class="div-table-col-3" style="text-align:right;">
                                    <?php echo ucwords($obj->lang['balance']); ?> 
                                </div>  
                                <div class="div-table-col-3" style="width:180px;"> 
                                    <?php echo $obj->inputNumber('balance', array ( 'readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>  
                                </div> 
                                <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                            </div> 
                      </div>  
              
                </div>
              <div class="div-table" style="float:right;">
                    <?php if (!$usePPNDetail) {?>
                                 <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>"> 
                                    <div class="div-table-col-5" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['subtotal']); ?> 
                                    </div>  
                                    <div class="div-table-col-5" style="width:200px;"> 
                                        <?php echo $obj->inputNumber('subtotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                                    </div>

                                </div>

                                 <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>"> 
                                    <div class="div-table-col-5"  style="text-align:right;">
                                         <?php echo ucwords($obj->lang['discount']); ?>
                                    </div>  
                                    <div class="div-table-col-5"> 
                                        <div class="flex">          
                                            <div><?php echo $obj->inputSelect('selFinalDiscountType',$obj->arrDiscountType); ?> </div>
                                            <div class="consume"> <?php echo $obj->inputNumber('finalDiscount', array ('class'=> 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;"')) ;?> </div>
                                         </div> 
                                    </div> 
                                </div>
                  
                                 <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>"> 
                                    <div class="div-table-col-5"></div>  
                                    <div class="div-table-col-5"></div> 
                                </div>

					<?php }?>

                                 <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-5" style="text-align:right;">
                                       <?php echo ucwords($obj->lang['beforeTax']); ?>
                                    </div>  
                                    <div class="div-table-col-5" style="width:200px;"> 
                                         <?php echo $obj->inputNumber('beforeTaxTotal', array( 'disabled' => true, 'etc' => 'style="text-align:right;"')); ?>
                                    </div>

                                </div>

                                <?php if ($usePPNDetail) {?>
                                 <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-5" style="text-align:right;">
                                       <?php echo strtoupper($obj->lang['PPN']); ?>
                                    </div>  
                                    <div class="div-table-col-5" style="width:200px;"> 
                                         <?php echo $obj->inputNumber('taxValue', array( 'readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
                                    </div>

                                </div>   
                                <?php }else{ ?>
                                    <div class="div-table-row  form-group"> 
                                          <div class="div-table-col-5"  style="text-align:right;">                                        
                                              <?php echo strtoupper($obj->lang['PPN']); ?> [Include]
                                         </div>   
                                         <div class="div-table-col-5"> 
                                             <div class="flex">    
                                                <div><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>  
                                                <div class="percentage-col"> 
													 <?php 
														if($obj->loadSetting('inputTaxValueType') == 2) 
															echo $obj->inputSelect('taxPercentage',TAX_VALUE, array('etc' => 'style="text-align:right;"', 'add-class'=>'no-padding')); 
														else
															echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"'));
													?>  
												 </div> 
                                                <div>%</div>
                                                <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                              </div> 
                                        </div> 
                                     </div> 
                            
                                <?php }?>
				  
				  				<div class="div-table-row  form-group"> 
                                    <div class="div-table-col-5" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['stampFee']); ?> 
                                    </div>  
                                    <div class="div-table-col-5" > 
                                         <?php echo  $obj->inputNumber('stampFee', array( 'etc' =>'style="text-align:right"')); ?>
                                    </div> 
                                </div>
                                <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-5" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['total']); ?> 
                                    </div>  
                                    <div class="div-table-col-5" > 
                                         <?php echo  $obj->inputNumber('total', array('readonly' => true, 'etc' =>'style="text-align:right"')); ?>
                                    </div> 
                                </div>
 								<div style="clear:both; height: 2em"></div>

				  
                                <div class="div-table-row  form-group"> 
                                      <div class="div-table-col-5"  style="text-align:right;">
                                        <?php echo ucwords($obj->lang['tax23']); ?>
                                     </div>   
                                     <div class="div-table-col-5">
                                        <div class="flex"> 
                                            <!--<div><?php echo $obj->inputCheckBox('chkTax23'); ?></div>  -->
                                            <div class="percentage-col"><?php echo $obj->inputDecimal('tax23Percentage', array('etc' => 'style="text-align:right;"')); ?></div> 
                                            <div>%</div>
                                            <div class="consume"><?php echo $obj->inputNumber('tax23Value', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
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
