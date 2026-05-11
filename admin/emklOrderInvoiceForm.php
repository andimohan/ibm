<?php 
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array('EMKLOrderInvoice.class.php','Warehouse.class.php','PaymentMethod.class.php','SalesOrderInvoiceReceipt.class.php','EMKLInvoiceReceipt.class.php','ItemUnit.class.php'));

$emklOrderInvoice = new EMKLOrderInvoice();
$currency = new Currency();
$warehouse = new Warehouse();
$customer = new Customer();
$paymentMethod = new PaymentMethod();
$termOfPayment = new TermOfPayment();
$emklInvoiceReceipt =  createObjAndAddToCol(new EMKLInvoiceReceipt());
$itemUnit = new ItemUnit();

$obj= $emklOrderInvoice;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'emklOrderInvoiceList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 
 
$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';
$customCodeInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = ''; 

$_POST['trDate'] = date('d / m / Y');
$_POST['trDateCustomerTax'] =  date('d / m / Y');

$usePPNDetail = $obj->loadSetting('usePPNDetail');
$readonlyInvoiceAddress = true;
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber'; 

$rsSalesOrderInvoiceDetail = array(); 
$rsPaymentMethodDetail = array();
$rsInvoiceDP = array();

$rs = prepareOnLoadData($obj);  

$downpaymentType = false;
$notDownpaymentField = '';

$activeCurrency = 'IDR';
$arrInvoiceAddress = array();

$rateHeaderType = $obj->loadSetting('useInvoiceRateForGL');
$readonlyHeaderRate = ($obj->loadSetting('readonlyHeaderRate') == 1 ) ? true : false; 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	  
    $rsSalesOrderInvoiceDetail = $obj->getDetailWithRelatedInformation($id);
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
    $rsReceipt = $emklInvoiceReceipt->getInvoiceReceipt($id,' and '.$emklInvoiceReceipt->tableName.'.statuskey in (2,3) ');
    $rsInvoiceDP = $obj->getDownpaymentDetail($id); 
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	  
    $readonlyInvoiceAddress = ($rs[0]['refinvoiceaddresskey'] == -999) ? false : true;
    
	$_POST['invoicetaxnumber'] = $rs[0]['invoicetaxnumber'] ;
	
	// karena multiple address blm reserved pkey
	if($rs[0]['refinvoiceaddresskey'] > 0){
		$rsInvoiceAddress = $customer->getMultipleAddress($rs[0]['customerkey'],1, '', ' and '.$customer->tableMultipleAddress.'.name = '.$obj->oDbCon->paramString($rs[0]['invoiceaddressname'])) ;
		$_POST['selInvoiceAddress'] = $rsInvoiceAddress[0]['pkey'];

		// kal oblm diproses, update ulang addresnya
		if($rs[0]['statuskey'] == 1){
		 $_POST['invoiceAddress'] =  $rsInvoiceAddress[0]['address'];
        }
	}
	
    $customertaxdate = (!empty($rs[0]['customertaxdate'])) ? $obj->formatDBDate($rs[0]['customertaxdate'],'d / m / Y') : date('d / m / Y');
    $_POST['trDateCustomerTax'] = $customertaxdate;
    
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCurrentCustomerName'] = $rsCustomer[0]['name'] ; 
//	$_POST['hidCurrentInvoiceName'] = $rs[0]['invoicename'] ; 
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['hidCurrentCustomerKey'] = $rsCustomer[0]['pkey'] ;  
    
    $rsEmployee = $employee->getDataRowById($rs[0]['approvedbykey']);
    if(!empty($rsEmployee)){
        $_POST['approvedName'] = $rsEmployee[0]['name'] ;
        $_POST['hidApprovedName'] = $rsEmployee[0]['name'] ; 
    }
	
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']); 
    if ($rs[0]['finaldiscounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 
         
	$_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'],$finalDiscDecimal);  
    $_POST['tax23Percentage'] = $obj->formatNumber($rs[0]['tax23percentage'],2);
    $_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['invoiceTaxNumber'] = $rs[0]['invoicetaxnumber'] ; 
	 
    	
    if (!empty($rsReceipt)){ 
       $_POST['receiptCode'] = $rsReceipt[0]['code'];
       $_POST['receiptDate'] =  $obj->formatDBDate($rsReceipt[0]['trdate']);
       $_POST['receivedDate'] =  $obj->formatDBDate($rsReceipt[0]['receiveddate']); 
       $_POST['recipientName'] = $rsReceipt[0]['recipientname'];
    } 
  
    $_POST['selTypeOfJob'] = $rs[0]['jobtypekey'];
    $_POST['selAirSea'] = $rs[0]['transportationtypekey'];
 	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
    $customCodeInactiveCriteria = ' or  '.$customCode->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['customcodekey']);  
    $editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
 
	$arrInvoiceAddress = $obj->generateComboboxOpt(array('data' => $customer->getAddressForInvoice($rs[0]['customerkey'])),'','',array('rel-address' => 'value'));
}

$rsKey = $obj->getTableKeyAndObj($obj->tableName);

$rsTOP = $termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'),'pkey','name');    
$arrCustomCode =  $class->convertForCombobox($customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'],true,' and ('.$customCode->tableName.'.statuskey = 1 ' . $customCodeInactiveCriteria.')','order by orderlist asc, name asc'),'pkey','name');  
$arrTOP = $obj->convertForCombobox($rsTOP,'pkey','name'); 
$arrCurrency = $class->convertForCombobox($currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1)'),'pkey','name');
$arrUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name');

$tempInvoiceType = explode(',',$class->loadSetting('EMKLInvoiceType'));
$emklInvoiceTypeKey = array_keys(EMKL_INVOICE_TYPE);

$arrInvoiceType = array();
foreach($tempInvoiceType as $key=>$row)
	if(in_array($row,$emklInvoiceTypeKey))
	 	$arrInvoiceType[$row] = EMKL_INVOICE_TYPE[$row];

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
     
	jQuery(document).ready(function(){  
	 	 
        var tabID = selectedTab.newPanel[0].id;
        var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName,array('key'))['key'];  ?>  
        
         var cashTOP = Array();
         <?php 
            for ($i=0;$i<count($rsTOP);$i++){
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
            }
         ?>
        
        var varConstant = {  
                            CURRENCY : <?php echo json_encode(CURRENCY); ?>,
                            TAX_ROUND_TYPE : <?php  $roundType = $obj->loadSetting('invoiceTaxRoundType'); echo (empty($roundType)) ? 1 : $roundType; ?>,
                            usePPNDetail : <?php echo json_encode($usePPNDetail); ?>,
                            VAT_OUT_ROUND_TYPE : <?php echo ($obj->loadSetting('vatOutRoundType') == 1) ? 1 : 2; ?> 
                            };
        
        var emklOrderInvoice = new EMKLOrderInvoice(tabID,tablekey,cashTOP,varConstant);
        prepareHandler(emklOrderInvoice);     

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
    <?php //echo $obj->inputHidden('hidCurrentInvoiceName'); ?> 
    <?php echo $obj->inputHidden('hidTotalBeforeTaxPPH23'); ?>
    
    <?php echo $obj->inputHidden('selTypeOfJob') ?>
    <?php echo $obj->inputHidden('selAirSea') ?>
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?> / <?php echo ucwords($obj->lang['tax']); ?></label> 
                                <div class="col-xs-9"> 
                                    <div class="flex">
										<div class="consume"><?php echo $obj->inputAutoCode('code'); ?></div>
										<div>/</div>
										<div class="consume"><?php echo $obj->inputText('invoicetaxnumber', array('readonly' => true)); ?></div>
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceType']); ?> <?php if($rateHeaderType == 1) {  echo ucwords($obj->lang['rate']); } ?></label> 
                                <div class="col-xs-9"> 
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputSelect('selCustomCode', $arrCustomCode); ?></div>
                                        <div> <?php echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency')); ?>  </div>
                                        <?php if($rateHeaderType == 1) { ?> <div class="consume"><?php echo $obj->inputDecimal('currencyRate', array("readonly"=>$readonlyHeaderRate)); ?></div> <?php } ?>
                                    </div> 
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
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['undername']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('undername'); ?>  
                                </div> 
                            </div> 
                             
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recipientName']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('invoiceName'); ?>  
                                </div> 
                            </div> 
							<div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recipientAddress']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selInvoiceAddress', $arrInvoiceAddress); ?> 
                                </div> 
                            </div>  
							 <div class="form-group">
                                <label class="col-xs-3 control-label"></label> 
                                <div class="col-xs-9">  
									<?php echo  $obj->inputTextArea('invoiceAddress', array('etc' => 'style="height:10em;"', 'readonly' => $readonlyInvoiceAddress)); ?>
                                </div> 
                            </div>  
							<div style="clear:both; height:1em"></div>
                             <div class="form-group <?php echo $obj->hideOnDisabled(); ?>">
                                <label class="col-xs-3 control-label"></label> 
                                <div class="col-xs-9"> 
                                         <?php echo $obj->inputButton('btnImport',$obj->lang['showAll'],array('class' =>'btn btn-primary btn-second-tone')); ?>
                                </div> 
                            </div>  
                         </div> 
                </div>
                
                <div class="div-table-col"> 
                    
					    <div class="div-tab-panel"> 
                            <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['financialInformation']); ?></div> 
                              
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceTaxNumber']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('invoiceTaxNumber', array('readonly' => true)); ?>  
                                </div> 
                            </div> 
                             
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['paymentTo']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputSelect('selBank', $arrPaymentMethod); ?>  
                                </div> 
                            </div> 
 							<div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['approvedBy']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $employee,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'approvedName',
                                                                                                   'key' => 'hidApprovedKey'),
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
                </div>             
             </div>
      </div>
       
      <div class="mnv-checkbox-group">
        <div class="div-table mnv-transaction invoice-detail transaction-detail" style="width:100%; border-bottom:1px solid #333; "  attr-level="0">
                <div class="div-table-row"> 
                    <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row"> 
                                <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['jobOrderCode']); ?> / <?php echo ucwords($obj->lang['cost']); ?></div> 
                                <!--<div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['description']); ?></div>-->
                                <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                                <!--<div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['invoiceIssued']); ?></div>-->
                                <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['total']); ?> <span class="mnv-active-currency text-muted"><?php echo $activeCurrency; ?></span></div>
                            </div>
                        </div>
                    </div>  
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>  icon-col" style="width: 25px" > <?php echo $obj->inputCheckBox('chkPick-master', array('etc' => 'style="margin-top:0"')); ?></div> 
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
                        $showInvoice = 'display:none;';    
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
                            $_POST['hidInvoiceKey[]'] =  ''; 
                            $_POST['invoiceCode[]'] =  ''; 
                            
                            $readonly = false;

                            $showSO = 'display:none;';
                            $showCost = '';    
                            $showInvoice = '';      

                            if ( $rsSalesOrderInvoiceDetail[$i]['invoicetype'] == 1){ 
                                
                                //$readonly = (!$downpaymentType) ? true : false; 
                                $readonly = true; 
                                $showSO = '';
                                $showCost = 'display:none;';
                                $showInvoice = 'display:none;';

                                //$rsSO = $truckingServiceOrder->getDataRowById($rsSalesOrderInvoiceDetail[$i]['salesorderkey']); 
                                $sokey = $rsSalesOrderInvoiceDetail[$i]['salesorderkey'];
                                $_POST['hidSalesOrderKey[]'] =  $sokey; 
                                $_POST['hidSalesOrderHeaderKey[]'] =  $rsSalesOrderInvoiceDetail[$i]['refsalesorderheaderkey'];
                                $_POST['salesOrderCode[]'] =  $rsSalesOrderInvoiceDetail[$i]['socode'];
                                $_POST['salesOrderDate[]'] =  $obj->formatDBDate($rsSalesOrderInvoiceDetail[$i]['sodate']);  
                                $_POST['salesOrderSubtotal[]'] =   $obj->formatNumber($rsSalesOrderInvoiceDetail[$i]['salesordergrandtotal']);
                                //$_POST['salesOrderDownpayment[]'] =   $obj->formatNumber($rsSalesOrderInvoiceDetail[$i]['salesordertotalinvoiced']); 

	                            $_POST['doNumberDetail[]'] =  $rsSalesOrderInvoiceDetail[$i]['hbl'];
                                
                            } else if ($rsSalesOrderInvoiceDetail[$i]['invoicetype'] == 2){
                                $_POST['hidItemKey[]'] =  $rsSalesOrderInvoiceDetail[$i]['itemkey']; 
                                $_POST['itemName[]'] =  $rsSalesOrderInvoiceDetail[$i]['itemname'];  
							}else{
                                 $showSO = 'display:none;';
                                 $showCost = 'display:none;';
                                 $readonly = true; 

                                $rsInvoice = $obj->getDataRowById($rsSalesOrderInvoiceDetail[$i]['invoicekey']);
                                $_POST['hidInvoiceKey[]'] =  $rsSalesOrderInvoiceDetail[$i]['invoicekey']; 
                                $_POST['invoiceCode[]'] =  $rsInvoice[0]['code']; 
                                $_POST['salesOrderSubtotal[]'] =   $obj->formatNumber($rsInvoice[0]['grandtotal']);
                            }
                            
                            $_POST['hidDetailKey[]'] = $rsSalesOrderInvoiceDetail[$i]['pkey'];
                            $_POST['selInvoiceType[]'] = $rsSalesOrderInvoiceDetail[$i]['invoicetype'];
                            $_POST['detailNote[]'] =  $rsSalesOrderInvoiceDetail[$i]['description'];    
                            $_POST['amount[]'] =   $obj->formatNumber($rsSalesOrderInvoiceDetail[$i]['amount']);
                            
                            // Detail Service 
                            //if (!empty($sokey) && !$downpaymentType){
                            if (!empty($sokey) || !empty($rsSalesOrderInvoiceDetail[$i]['invoicekey'])){
                                $rsServiceDetail = $obj->getItemDetail($rsSalesOrderInvoiceDetail[$i]['pkey']); 
                                $totalDetailRows = count($rsServiceDetail); 
                                $optionRows = '';
                            }
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
                                                <div><?php echo $obj->inputText('salesOrderCode[]',array('overwritePost' => $overwrite,'etc' => 'placeholder="'.$obj->lang['pleasestarttyping'].'"', 'disabled' => $disabled )); ?><?php echo $obj->inputHidden('hidSalesOrderKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?><?php echo $obj->inputHidden('hidSalesOrderHeaderKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?></div>
                                                <!--<div style="width: 100px"><?php echo $obj->inputText('salesOrderDate[]', array('overwritePost' => $overwrite, 'readonly' => true,  'disabled' => $disabled, 'etc' => 'style="text-align:center;"  placeholder="'.$obj->lang['jobOrderDate'].'"' )); ?></div>-->
                                                <div class="consume"><?php echo $obj->inputText('doNumberDetail[]', array('overwritePost' => $overwrite, 'readonly' => true,  'disabled' => $disabled)); ?></div>
                                            </div> 
                                        </div>
                                        <div class="type-2"  style="<?php echo $showCost ; ?>"><?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'etc' => 'placeholder="'.$obj->lang['pleasestarttyping'].'"',  'disabled' => $disabled)); echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?></div>
                                        <div class="type-3"  style="<?php echo $showInvoice ; ?>"><?php echo $obj->inputText('invoiceCode[]', array('overwritePost' => $overwrite, 'etc' => 'placeholder="'.$obj->lang['pleasestarttyping'].'"',  'disabled' => $disabled)); echo $obj->inputHidden('hidInvoiceKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?></div>
                                    </div>    
                                    <div class="div-table-col detail-col-detail" style="width:110px; text-align:right;"><?php echo $obj->inputNumber('salesOrderSubtotal[]', array('overwritePost' => $overwrite, 'readonly'=>true,  'disabled' => $disabled, 'etc' => 'style="text-align:right;" ' )); ?></div> 
                                    <!--<div class="div-table-col detail-col-detail" style="width:100px; text-align:right;"><?php echo $obj->inputNumber('salesOrderDownpayment[]', array('overwritePost' => $overwrite, 'readonly'=>true,  'disabled' => $disabled, 'etc' => 'style="text-align:right;" ' )); ?></div>-->
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
                                <div class="div-table mnv-transaction transaction-detail" attr-level="1" attr-group="hidDetailItemKey"> 
                                    <div class="div-table-row"> 
                                        <div class="div-table-col detail-col-detail col-header no-border <?php echo $obj->hideOnDisabled(); ?> " style="width: 3em;"></div> 

                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:6em; text-align:right"><?php echo ucwords($obj->lang['qty']); ?></div>  
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:10em;"><?php echo ucwords($obj->lang['unit']); ?></div>  
                                        <div class="div-table-col detail-col-detail col-header no-border"><?php echo ucwords($obj->lang['services']); ?></div>
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:18em;"><?php echo ucwords($obj->lang['alias']); ?></div>
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:8em; text-align:right"><?php echo ucwords($obj->lang['price']); ?></div> 
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:5em; text-align:center;"><?php echo ucwords($obj->lang['curr']); ?></div> 
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:8em; text-align:right"><?php echo ucwords($obj->lang['rate']); ?></div> 
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:9em; text-align:right"><?php echo ucwords($obj->lang['total']); ?> <span class="mnv-active-currency text-muted"><?php echo $activeCurrency; ?></span></div> 
                                        <?php if ($usePPNDetail) {?>
                                        
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:5em; text-align:right"><?php echo ucwords($obj->lang['PPN']); ?> %</div>
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:3em; text-align:center">Inc.</div>
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:9em; text-align:right"><?php echo ucwords($obj->lang['PPN']); ?></div>
                                            <div class="div-table-col detail-col-detail col-header no-border" style="width:9em; text-align:right"><?php echo ucwords($obj->lang['total']); ?></div>
                                         
                                        <?php } ?>
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:5em;  text-align:center"><?php echo ucwords($obj->lang['tax23']); ?></div>  
                                        
                                        <?php if ($usePPNDetail) {?>      
                                        <div class="div-table-col detail-col-detail col-header no-border" style="width:3em;  text-align:center">Reim</div>    
                                        <?php } ?>
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
                                                $_POST['qtyDetail[]'] =  $obj->formatNumber($rsServiceDetail[$j]['qtyinbaseunit'],3);
                                                $_POST['selDetailItemUnit[]'] =  $rsServiceDetail[$j]['unitkey'];
                                                $_POST['hidItemDetailKey[]'] =  $rsServiceDetail[$j]['itemkey']; 
                                                $_POST['hidContainerDetailKey[]'] =  $rsServiceDetail[$j]['containerkey']; 
                                                $_POST['itemNameDetail[]'] =   $rsServiceDetail[$j]['itemname']; 
                                                $_POST['itemNameAliasDetail[]'] =   $rsServiceDetail[$j]['aliasname']; 
                                                $_POST['priceInUnitDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['priceinunit'],-2);
                                                $_POST['subtotalDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['total']); 
                                                $_POST['chkService[]'] =  1;
                                                $_POST['chkIsTax23[]'] = $rsServiceDetail[$j]['istax23'];
                                                //$_POST['selDetailCurrency[]'] = $rsServiceDetail[$j]['currencykey'];
                                                $_POST['detailRate[]'] = $obj->formatNumber($rsServiceDetail[$j]['rate'],2);
                                          

                                                $_POST['chkIsReimburse[]'] = $rsServiceDetail[$j]['isreimburse'];
                                                $_POST['chkIncludeTaxDetail[]'] = $rsServiceDetail[$j]['ispriceincludetax'];
                                                $_POST['taxDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['taxdetail'], 2);
                                                $_POST['taxValueDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['taxdetailvalue'], 2);
                                                $_POST['beforeTaxDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['beforetaxdetailvalue']);
                                                $_POST['afterTaxDetail[]'] = $obj->formatNumber($rsServiceDetail[$j]['aftertaxdetailvalue']);                                                
                                                $_POST['hidCurrencyKey[]'] = $rsServiceDetail[$j]['currencykey'];
                                                $_POST['currencyName[]'] =  $arrCurrency[$rsServiceDetail[$j]['currencykey']]['label']; 
                                                
                                            } 
                                            
                                    ?>
                                    <div class="div-table-row <?php echo $classDetail; ?>" >
                                        <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> " style="text-align:center">
                                            <?php echo $obj->inputHidden('hidDetailItemKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?><?php echo $obj->inputHidden('hidRefSODetailKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                            <?php echo $obj->inputHidden('hidContainerDetailKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                            <?php echo $obj->inputCheckBox('chkService[]',array( 'disabled' => $disabledDetail)); ?>
                                        </div>
                                        <div class="div-table-col-3">
                                            <?php echo $obj->inputNumber('qtyDetail[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control inputautodecimal label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" mnv-attr-decimal="3";')); ?>
                                        </div>
                                        <div class="div-table-col-3">
                                            <?php echo $obj->inputSelect('selDetailItemUnit[]', $arrUnit, array('overwritePost' => $overwrite,  'class' => 'form-control label-style', 'disabled' => $disabledDetail)); ?>
                                        </div> 
                            
                                        <div class="div-table-col-3">
                                            <?php echo $obj->inputText('itemNameDetail[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'class' => 'form-control label-style',  'disabled' => $disabledDetail)); ?>
                                            <?php echo $obj->inputHidden('hidItemDetailKey[]', array('overwritePost' => $overwriteDetail,  'disabled' => $disabledDetail)); ?>
                                        </div> 
                                        <div class="div-table-col-3">
                                            <?php echo $obj->inputText('itemNameAliasDetail[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control label-style',  'disabled' => $disabledDetail)); ?>
                                        </div>
                                      
                                        <div class="div-table-col-3">
                                            <?php echo $obj->inputNumber('priceInUnitDetail[]', array('readonly' => true,'overwritePost' => $overwriteDetail,'class' => 'form-control inputautodecimal label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ' )); ?>
                                        </div>
                                          <div class="div-table-col-3">
                                            <?php echo $obj->inputText('currencyName[]', array('readonly' => true, 'overwritePost' => $overwriteDetail, 'class' => 'form-control label-style',  'disabled' => $disabledDetail, 'etc' => 'style="text-align:center;" ')); ?>
                                            <?php echo $obj->inputHidden('hidCurrencyKey[]', array('overwritePost' => $overwriteDetail,  'disabled' => $disabledDetail)); ?>
                                  
                                            <?php  //echo $obj->inputSelect('selDetailCurrency[]', $arrCurrency, array('readonly' => true,'overwritePost' => $overwriteDetail,   'disabled' => $disabledDetail )); ?>
                                        </div>
                                        <div class="div-table-col-3">
                                            <?php echo $obj->inputDecimal('detailRate[]', array('readonly' => true,'overwritePost' => $overwriteDetail, 'class' => 'form-control inputautodecimal label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ')); ?>
                                        </div>
                                        <div class="div-table-col-3">
                                                <?php echo $obj->inputNumber('subtotalDetail[]', array('readonly' => true,'overwritePost' => $overwriteDetail,'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
                                        </div>

                                        <?php if ($usePPNDetail) {?>

                                            <div class="div-table-col-3" >
                                                <?php echo $obj->inputSelect('taxDetail[]', TAX_VALUE, array('overwritePost' => $overwrite, 'class' => 'form-control label-style', 'readonly' => true, 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ' . $etc, 'add-class' => 'no-padding')); ?>
                                                <?php //echo $obj->inputDecimal('taxDetail[]', array('readonly' => true,'overwritePost' => $overwriteDetail, 'class' => 'form-control inputautodecimal label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ')); ?>
                                            </div>
	                                        <div class="div-table-col-3 " style="text-align:center">
												<?php  echo $obj->inputCheckBox('chkIncludeTaxDetail[]',array('value' => false,'readonly' => true, 'disabled' => $disabledDetail)); ?>
											</div>

                                            <div class="div-table-col-3">
                                                <?php echo $obj->inputNumber('taxValueDetail[]', array('readonly' => true,'overwritePost' => $overwriteDetail, 'class' => 'form-control inputautodecimal label-style', 'disabled' => $disabledDetail, 'etc' => 'style="text-align:right;" ')); ?>
											</div> 
											<div class="div-table-col-3">
												<?php echo $obj->inputHidden('beforeTaxDetail[]', array('readonly' => true,'overwritePost' => $overwriteDetail,'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
												<?php echo $obj->inputNumber('afterTaxDetail[]', array('readonly' => true,'overwritePost' => $overwriteDetail,'class' => 'form-control inputnumber label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
											</div>  
                                        <?php } ?>
                                        <div class="div-table-col-3 " style="text-align:center">
                                            <?php  echo $obj->inputCheckBox('chkIsTax23[]',array( 'disabled' => $disabledDetail)); ?>
                                        </div>
                                        
                                        <?php if ($usePPNDetail) {?>
                                            <div class="div-table-col-3 " style="text-align:center">
                                                <?php  echo $obj->inputCheckBox('chkIsReimburse[]',array(  'readonly' => true, 'disabled' => $disabledDetail)); ?>
                                            </div>
                                        <?php } ?>
                                    </div> 
                                    <?php } ?>
                                </div>   
                        </div>   
                        <!--</div> -->
                    </div>
                    <div class="div-table-col detail-col-detail icon-col  align-top-adjust <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputCheckBox('chkPick[]', array('value' => 1, 'disabled' => $disabled)); ?></div>
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
                                 <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>"> 
                                    <div class="div-table-col-5" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['subtotal']); ?> 
                                    </div>  
                                    <div class="div-table-col-5" style="width:200px;"> 
                                        <?php echo $obj->inputNumber('subtotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                                    </div>

                                </div>

                                <!-- <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>"> 
                                    <div class="div-table-col-5"  style="text-align:right;">
                                         <?php echo ucwords($obj->lang['discount']); ?>
                                    </div>  
                                    <div class="div-table-col-5"> 
                                        <div class="flex">          
                                            <div><?php echo $obj->inputSelect('selFinalDiscountType',$obj->arrDiscountType); ?> </div>
                                            <div class="consume"> <?php echo $obj->inputNumber('finalDiscount', array ('class'=> 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;"')) ;?> </div>
                                         </div> 
                                    </div> 
                                </div>-->
                                 <div class="div-table-row  form-group not-downpayment-field" style="<?php echo $notDownpaymentField; ?>"> 
                                    <div class="div-table-col-5"></div>  
                                    <div class="div-table-col-5"></div> 
                                </div>


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
                                 
                                <?php } ?>  
                    
                                 <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-5" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['otherCost']); ?> 
                                    </div>  
                                    <div class="div-table-col-5" > 
                                         <?php echo  $obj->inputNumber('otherCost', array('etc' =>'style="text-align:right"')); ?>
                                    </div> 
                                </div>
                  
                                <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-5" style="text-align:right;">
                                        <?php echo ucwords($obj->lang['total']); ?> <span class="mnv-active-currency text-muted"><?php echo $activeCurrency; ?></span>
                                    </div>  
                                    <div class="div-table-col-5" > 
                                         <?php echo  $obj->inputNumber('total', array('readonly' => true, 'etc' =>'style="text-align:right"')); ?>
                                    </div> 
                                </div>
 
 

                                <div class="div-table-row  form-group"> 
                                      <div class="div-table-col-5"  style="text-align:right;  padding-top:2em;">
                                        <?php echo ucwords($obj->lang['tax23']); ?>
                                     </div>   
                                     <div class="div-table-col-5" style=" padding-top:2em;">
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
