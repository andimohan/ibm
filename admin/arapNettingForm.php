<?php 
require_once '../_config.php';  
require_once '../_include-v2.php';  
includeClass('ARAPNetting.class.php');
$arapNetting = createObjAndAddToCol( new ARAPNetting()); 
$emklOrderInvoice = createObjAndAddToCol( new EMKLOrderInvoice()); 

$obj= $arapNetting;
$ar = $obj->getARObj();
$ap = $obj->getAPObj();
$customer = new Customer();
$supplier = new Supplier();
$warehouse = new Warehouse();
$currency = new Currency();
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'arapNettingList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

//$editPaymentMethodInactiveCriteria = '';
$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$rsARPaymentDetail = array();
$rsAPPaymentDetail = array();
$rsARDP = array();
$rsARCost = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['trStartDate'] = date('d / m / Y');
$_POST['trEndDate'] = date('d / m / Y'); 
$_POST['hidCurrentCurrencyKey'] = 1;  // default IDR

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsARPaymentDetail = $obj->getDetailARAP($obj->arapConstant['ar'],$id);
	$rsAPPaymentDetail = $obj->getDetailARAP($obj->arapConstant['ap'],$id);
     
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCurrentCustomerKey'] = $rsCustomer[0]['pkey'] ;   
	$_POST['hidCurrentCustomerName'] = $rsCustomer[0]['name'] ; 
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ; 
    
    $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
	$_POST['supplierName'] = $rsSupplier[0]['name'] ;
	$_POST['hidCurrentSupplierName'] = $rsSupplier[0]['name'] ; 
	$_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ;  
	$_POST['hidCurrentSupplierKey'] = $rsSupplier[0]['pkey'] ; 
    
	$_POST['trDesc'] = $rs[0]['trnotes']; 
   	$_POST['selWarehouseKey'] = $rs[0]['warehousekey'];  
	$_POST['chkDatePeriod'] = $rs[0]['usedateperiod'];   
	$_POST['trStartDate'] = $obj->formatDBDate($rs[0]['startdateperiod'],'d / m / Y');
	$_POST['trEndDate'] = $obj->formatDBDate($rs[0]['enddateperiod'],'d / m / Y'); 
   	$_POST['totalARAmount'] = $obj->formatNumber($rs[0]['totalar']);  
   	$_POST['totalAPAmount'] = $obj->formatNumber($rs[0]['totalap']);  
   	$_POST['totalTaxARAmount'] = $obj->formatNumber($rs[0]['totaltaxar']);  
   	$_POST['totalTaxAPAmount'] = $obj->formatNumber($rs[0]['totaltaxap']);
    $_POST['grandtotalARAmount'] = $obj->formatNumber($rs[0]['grandtotalar']);  
   	$_POST['grandtotalAPAmount'] = $obj->formatNumber($rs[0]['grandtotalap']);  
	 
    $_POST['selCurrency'] = $rs[0]['currencykey']; 
    $_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'],2);
	$_POST['hidCurrentCurrencyKey'] = $rs[0]['currencykey'] ;    
    
 	$editCurrencyInactiveCriteria = ' or  '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);  
 	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	
} 

$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrCurrency = $obj->convertForCombobox($currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1' . $editCurrencyInactiveCriteria.')'),'pkey','name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>
    .arap-subtitle {font-size: 1.2em !important; border: 0 !important}   
    .arap-show-detail{ padding-top:0.7em; margin-left:0.5em; font-size: 0.9em}
</style>    
<title></title> 
 
<script type="text/javascript">  
  
	jQuery(document).ready(function(){  
	 	 
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
        
         var varConstant = {  
                        CURRENCY : <?php echo json_encode(CURRENCY); ?> 
                        };
        
         var arapNetting = new ARAPNetting(tabID, tablekey, <?php echo json_encode($rs); ?>,varConstant);
    
         prepareHandler(arapNetting);

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
    <?php echo $obj->inputHidden('hidCurrentCustomerKey'); ?>
    <?php echo $obj->inputHidden('hidCurrentCustomerName'); ?>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array(  
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php  echo $obj->inputAutoComplete(array(  
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?> / <?php echo ucwords($obj->lang['currencyRate']); ?></label> 
                                        <div class="col-xs-9  mnv-currency"> 
                                           <div class="flex">
                                               <div><?php  echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency')); ?></div>
                                               <div class="consume"><?php echo $obj->inputDecimal('currencyRate', array('class'=>'form-control inputnumber input-currency-rate')); ?></div>
                                           </div>
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
                    
         <div class="section-panel-content div-table-tab-form" style="float:left;  width:100%; "> 
             <div class="div-table" style=" width:100%; "> 
                <div class="div-table-row">
                    <div class="arap-col div-table-col-5" style="vertical-align:top; width: 50%; padding-right: 1em">  
                        <div class="div-table mnv-transaction transaction-detail mnv-ar" style="width:100%; border-bottom:1px solid #333; ">
                                <div class="div-table-caption arap-subtitle">
                                    <div style="font-size: 1.5em; float:left"><?php echo ucwords($obj->lang['accountsReceivable']); ?></div>
                                    <div class="arap-show-detail form-detail-button" style="float:left;" alt="<?php echo ucwords($obj->lang['hideDetail']); ?>"><?php echo ucwords($obj->lang['showDetail']); ?></div>
                                    <div style="float:right; "><?php echo $obj->inputNumber('grandtotalARAmount',array('readonly' => true, 'etc' => 'style="text-align:right;')); ?></div>
                                </div>
                            
                                <div class="div-table-row"> 
                                    <div class="div-table-col" style="padding:0">
                                        <div class="div-table" style="width:100%">
                                            <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['arCode']); ?></div>
                                            <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
                                            <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                                            <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['tax23']); ?></div>
                                            
                                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                          </div>
                                    </div>      
                                </div>

                                <?php

                                    $totalRows = count($rsARPaymentDetail);
                                    for ($i=0;$i<=$totalRows; $i++){  
                                        $class =  'transaction-detail-row';
                                        $overwrite = true;
                                        $disabled = false;  

                                        //$_POST['refCode[]']  = '';
                                        //$_POST['doNumber[]']  = '';

                                        if ($i == $totalRows ){
                                            $class = 'ar-row-template row-template';
                                            $overwrite = false;
                                            $disabled = true; 
                                        } else {  
                                            $rsAR = $ar->getDataRowById($rsARPaymentDetail[$i]['arkey']);  
                                            $_POST['hidDetailKey[]'] =  $rsARPaymentDetail[$i]['pkey'];
                                            $_POST['hidARKey[]'] =  $rsARPaymentDetail[$i]['arkey']; 
                                            $_POST['arCode[]'] =  $rsAR[0]['code'];
                                            $_POST['arRefCode[]'] =  $rsAR[0]['refcode'];
                                            $_POST['arRefCode2[]'] =  $rsAR[0]['refcode2'];
                                            $_POST['arOutstanding[]'] =   $obj->formatNumber($rsARPaymentDetail[$i]['outstanding']); 
                                            $_POST['arAmount[]'] =   $obj->formatNumber($rsARPaymentDetail[$i]['amount']); 
                                            $_POST['arTax23[]'] =   $obj->formatNumber($rsARPaymentDetail[$i]['taxamount']); 
                                            //$doNumber = $ar->getDoNumber($rsAR[0]['refheaderkey']);
                                            //$_POST['arAmount[]'] =  $obj->formatNumber($rsAR[0]['amount']);
                                            //$_POST['doNumber[]'] =  $rsAR[0]['refcode2'];
                                            
                                            //$_POST['amount[]'] =   $obj->formatNumber($rsARPaymentDetail[$i]['amount']); 
                                            //$_POST['chkPick[]'] =  1;

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
                                                    <div class="div-table-col detail-col-detail" style="width:110px;"><?php echo $obj->inputNumber('arOutstanding[]',array('overwritePost' => $overwrite,'readonly' => true, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                                    <div class="div-table-col detail-col-detail" style="width:110px;"><?php echo $obj->inputNumber('arAmount[]',array('overwritePost' => $overwrite, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                                    <div class="div-table-col detail-col-detail" style="width:110px;"><?php echo $obj->inputNumber('arTax23[]',array('overwritePost' => $overwrite, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button')); ?> </div>
                                            </div>
                                        </div> 
                                        <div class="div-table options-row" style="width: 100%;display:none;">
                                            <div class="div-table-row">
                                                  <div class="div-table-col detail-col-detail row-header" style="width: 80px">
                                                    <?php echo $obj->lang['reference']; ?>
                                                  </div> 
                                                  <div class="div-table-col detail-col-detail" style="width: 120px">
                                                   <?php echo $obj->inputText('arRefCode[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                                  </div>
                                                 <div class="div-table-col detail-col-detail row-header" style="width:80px">
                                                    <?php echo $obj->lang['reference'].' 2'; ?>
                                                  </div> 
                                                  <div class="div-table-col detail-col-detail" style="width: 120px">
                                                   <?php echo $obj->inputText('arRefCode2[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                                  </div> 
                                                    
                                                  <div class="div-table-col detail-col-detail"></div>
                                            </div>

                                        </div>

                                    </div>
                                </div> 

                                <?php  } ?>   

                         </div> 
                        <div style="clear:both; height:0.5em;"></div>  
                        <div class="div-table transaction-detail" style="width:100%;">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail" >
                                    <?php echo $obj->inputButton('btnAddARRows',$obj->lang['addRows'], array('class' =>'btn btn-primary btn-second-tone', 'etc' => 'style="margin-bottom:0.5em"')); ?>
                                </div> 
                                <div class="div-table-col detail-col-detail"  style="width:110px; vertical-align:top"><?php echo $obj->inputNumber('totalARAmount',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?></div>
                                <div class="div-table-col detail-col-detail"  style="width:110px; vertical-align:top"><?php echo $obj->inputNumber('totalTaxARAmount',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?></div>
                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                            </div>
                        </div> 
                        
                    </div>
                    
                    <div class="arap-col div-table-col-5" style="vertical-align:top; padding-left: 1em">  
                            <div class="div-table mnv-transaction transaction-detail mnv-ap" style="width:100%; border-bottom:1px solid #333; ">
                                <div class="div-table-caption arap-subtitle">
                                    <div style="font-size: 1.5em; float:left"><?php echo ucwords($obj->lang['accountsPayable']); ?></div>         
                                    <div class="arap-show-detail form-detail-button" style="float:left;"  alt="<?php echo ucwords($obj->lang['hideDetail']); ?>"><?php echo ucwords($obj->lang['showDetail']); ?></div>
                                    <div style="float:right; "><?php echo $obj->inputNumber('grandtotalAPAmount',array('readonly' => true, 'etc' => 'style="text-align:right;')); ?></div>
                                </div>

                                <div class="div-table-row"> 
                                    <div class="div-table-col" style="padding:0">
                                        <div class="div-table" style="width:100%">
                                            <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['apCode']); ?></div>
                                            <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
                                            <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                                            <div class="div-table-col detail-col-header" style="width:110px; text-align:right;"><?php echo ucwords($obj->lang['tax23']); ?></div>
                                            <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                          </div>
                                    </div>      
                                </div>

                                <?php

                                    $totalRows = count($rsAPPaymentDetail);
                                    for ($i=0;$i<=$totalRows; $i++){  
                                        $class =  'transaction-detail-row';
                                        $overwrite = true;
                                        $disabled = false;  
 
                                        if ($i == $totalRows ){
                                            $class = 'ap-row-template row-template';
                                            $overwrite = false;
                                            $disabled = true; 
                                        } else {  
                                            $rsAP = $ap->getDataRowById($rsAPPaymentDetail[$i]['apkey']);  
                                            $_POST['hidDetailAPKey[]'] =  $rsAPPaymentDetail[$i]['pkey'];
                                            $_POST['hidAPKey[]'] =  $rsAPPaymentDetail[$i]['apkey']; 
                                            $_POST['apCode[]'] =  $rsAP[0]['code'];
                                            $_POST['apRefCode[]'] =  $rsAP[0]['refcode'];
                                            $_POST['apRefCode2[]'] =  $rsAP[0]['refcode2'];
                                            $_POST['apOutstanding[]'] =   $obj->formatNumber($rsAPPaymentDetail[$i]['outstanding']);  
                                            $_POST['apAmount[]'] =   $obj->formatNumber($rsAPPaymentDetail[$i]['amount']);  
                                            $_POST['apTax23[]'] =   $obj->formatNumber($rsAPPaymentDetail[$i]['taxamount']);  
                                        }

                                 ?>        

                              <div class="div-table-row <?php echo $class; ?>">
                                    <div class="div-table-col"  style="padding: 0.3em 0">
                                        <div class="div-table" style="width:100%">
                                            <div class="div-table-row"> 
                                                    <div class="div-table-col detail-col-detail">
                                                        <?php echo $obj->inputHidden('hidDetailAPKey[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                                                        <?php echo $obj->inputText('apCode[]',array('disabled' => $disabled, 'overwritePost' => $overwrite)); ?>
                                                        <?php echo $obj->inputHidden('hidAPKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                                                    </div>
                                                    <div class="div-table-col detail-col-detail" style="width:110px;"><?php echo $obj->inputNumber('apOutstanding[]',array('overwritePost' => $overwrite,'readonly' => true, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                                    <div class="div-table-col detail-col-detail" style="width:110px;"><?php echo $obj->inputNumber('apAmount[]',array('overwritePost' => $overwrite, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                                    <div class="div-table-col detail-col-detail" style="width:110px;"><?php echo $obj->inputNumber('apTax23[]',array('overwritePost' => $overwrite, 'disabled' => $disabled,  'etc' => 'style="text-align:right"' )); ?></div> 
                                                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button')); ?> </div>
                                            </div>
                                        </div> 
                                        <div class="div-table options-row" style="width: 100%; display:none;">
                                            <div class="div-table-row">
                                                  <div class="div-table-col detail-col-detail row-header" style="width: 80px">
                                                    <?php echo $obj->lang['reference']; ?>
                                                  </div> 
                                                  <div class="div-table-col detail-col-detail" style="width: 120px">
                                                   <?php echo $obj->inputText('apRefCode[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                                  </div>
                                                 <div class="div-table-col detail-col-detail row-header" style="width:80px">
                                                    <?php echo $obj->lang['reference'].' 2'; ?>
                                                  </div> 
                                                  <div class="div-table-col detail-col-detail" style="width: 120px">
                                                   <?php echo $obj->inputText('apRefCode2[]',array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control label-style')); ?>
                                                  </div> 
                                                    
                                                  <div class="div-table-col detail-col-detail"></div>
                                            </div>

                                        </div>

                                    </div>
                                </div> 

                                <?php  } ?>   

                         </div> 
                        <div style="clear:both; height:0.5em;"></div>  
                        <div class="div-table transaction-detail" style="width:100%;">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail" >
                                    <?php echo $obj->inputButton('btnAddAPRows',$obj->lang['addRows'], array('class' =>'btn btn-primary btn-second-tone', 'etc' => 'style="margin-bottom:0.5em"')); ?>
                                </div> 
                                <div class="div-table-col detail-col-detail"  style="width:110px; vertical-align:top"><?php echo $obj->inputNumber('totalAPAmount',array('readonly' => true, 'etc' => 'style="text-align:right;')); ?></div>
                                <div class="div-table-col detail-col-detail"  style="width:110px; vertical-align:top"><?php echo $obj->inputNumber('totalTaxAPAmount',array('readonly' => true, 'etc' => 'style="text-align:right;')); ?></div>
                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                            </div>
                        </div> 
                        
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
