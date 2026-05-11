<?php 
require_once '../../../_config.php';
require_once '../../../_include-v2.php';

includeClass(array('EMKLPurchaseOrder.class.php','Tax.class.php'));
$emklPurchaseOrderExport = createObjAndAddToCol(new EMKLPurchaseOrder(EMKL['jobType']['export']));
$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['export']));
$emklJobOrderHeaderExport = createObjAndAddToCol(new EMKLJobOrderHeader(EMKL['jobType']['export']));
$paymentMethod = createObjAndAddToCol(new PaymentMethod());
$supplier = createObjAndAddToCol(new Supplier());
$service = createObjAndAddToCol(new Service(SERVICE));
$currency = createObjAndAddToCol(new Currency());
$container = createObjAndAddToCol(new Container());
$templateEMKLPurchaseItem = createObjAndAddToCol(new TemplateEMKLPurchaseItem()); 
$termOfPayment = createObjAndAddToCol(new TermOfPayment()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 
$currencyRate = createObjAndAddToCol(new CurrencyRate());
$tax = createObjAndAddToCol( new Tax()); 

$obj= $emklPurchaseOrderExport;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
 
$formAction = 'emklPurchaseOrderExportList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

//$rsStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','textcolor');   
$rs = prepareOnLoadData($obj); 
$rsBuyDetail = array();
$rsPaymentMethodDetail = array();
$rsCostTemplate = $templateEMKLPurchaseItem->searchData($templateEMKLPurchaseItem->tableName.'.statuskey',1,true,' order by name asc');

$_POST['trDate'] = date('d / m / Y'); 
$_POST['etdPol'] = date('d / m / Y'); 
$_POST['etaPod'] = date('d / m / Y'); 
$_POST['selTypeOfJob'] = EMKL['jobType']['export'];
$_POST['chkIsReimburse'] = -1;

$editWarehouseInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$activeCurrency = 'IDR';
    
$rsJOType = $obj->getTableKeyAndObj($emklJobOrderExport->tableName,array('key'));
$rsJOHeaderType = $obj->getTableKeyAndObj($emklJobOrderHeaderExport->tableName,array('key'));
    
$arrReimburseTypeOpt = array(); 
$arrJODetail = array();
$rsJODetail = array();
$rsVolumeDetail = array();


if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
    $rsBuyDetail = $obj->getDetailWithRelatedInformation($id); 
    $rsJODetail = $emklJobOrderExport->getDetailByColumn('refkey', $rs[0]['refkey']); 
    
    if (empty($rs[0]['refcashadvancekey'])){
        $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
    }else{
        $arrCashAdvance = array();
        
        array_push($arrCashAdvance,
                    array(
                        'pkey' => 0,
                        'paymentkey' => -1,
                        'amount' => $rs[0]['grandtotal'],
                        
                    )
                  );
        
        $rsPaymentMethodDetail = $arrCashAdvance;
    } 
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y '); 
	$_POST['selCurrency'] = $rs[0]['currencykey']; 
	$rsCurrency = $currency->getDataRowById($rs[0]['currencykey']);
	$activeCurrency = $rsCurrency[0]['name'];
    $_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'],2);   
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal'],2); 
    $_POST['total'] = $obj->formatNumber($rs[0]['grandtotal'],2);
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal'],2); 
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax']; 
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue'],2) ;  
	$_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'] ;
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance'],2) ;  
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment'],2) ;  
    $_POST['refInvoiceCode'] =  $rs[0]['refinvoicecode'] ; 

    $_POST['selJOType'] =  $rs[0]['reftabletype'] ; 
    
    if($rs[0]['reftabletype']==$rsJOType['key']){
        // JO
        $rsEmkl = $emklJobOrderExport->searchData($emklJobOrderExport->tableName.'.pkey',$rs[0]['refkey']); 
        $rsVolumeDetail = $emklJobOrderExport->getDetailVolume($rs[0]['refkey']);
        $_POST['hidJobOrderKey'] = $rs[0]['refkey'] ;  
        $_POST['jobOrderCode'] = $rsEmkl[0]['code'] ;
        $_POST['terminal'] = $rsEmkl[0]['terminalname'];
        $_POST['depot'] = $rsEmkl[0]['depotname']; 
    }else{
         // JO HEADER 
         $rsEmkl = $emklJobOrderHeaderExport->searchData($emklJobOrderHeaderExport->tableName.'.pkey',$rs[0]['refjoheaderkey']); 
         $rsVolumeDetail =  $emklJobOrderHeaderExport->getDetailWithRelatedInformation($rs[0]['refjoheaderkey']);
         $_POST['hidJobHeaderKey'] = $rs[0]['refjoheaderkey'] ;  
         $_POST['jobHeaderCode'] = $rsEmkl[0]['code'] ;
    }
    

    $_POST['weight'] = $obj->formatNumber($rsEmkl[0]['weight'], 2); 
    $_POST['volume'] = $obj->formatNumber($rsEmkl[0]['volume'], 2); 
    $_POST['hidContainerKey'] = $rsEmkl[0]['itemkey']; 
    


    $_POST['selTypeOfJob'] = $rsEmkl[0]['jobtypekey'];
    $_POST['selAirSea'] = $rsEmkl[0]['transportationtypekey'];
    $_POST['selContainerType'] = $rsEmkl[0]['loadcontainertypekey'];
    $_POST['containerName'] = $rsEmkl[0]['containername']; 
    $_POST['poNumber'] = $rsEmkl[0]['invoicenumber']; 
    $_POST['bookingNumber'] = $rsEmkl[0]['bookingnumber'];
    $_POST['shipperName'] = $rsEmkl[0]['customername'];
    $_POST['mblNumber'] = $rsEmkl[0]['mblnumber']; 
    $_POST['containerNumber'] = $rsEmkl[0]['containernumber']; 
    $_POST['etdPol'] = $obj->formatDBDate($rsEmkl[0]['etdpol'],'d / m / Y ');
    $_POST['etaPod'] = $obj->formatDBDate($rsEmkl[0]['etapod'],'d / m / Y '); 
    //$_POST['customerName'] = $rsEmkl[0]['customername'];
    $_POST['pol'] = $rsEmkl[0]['polname'];
    $_POST['pod'] = $rsEmkl[0]['podname']; 
    $_POST['location'] = $rsEmkl[0]['stuffing'];
 
        
    if (!empty($rs[0]['supplierkey'])){
        $_POST['hidSupplierKey'] = $rs[0]['supplierkey']; 
		$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
		$_POST['supplierName'] = $rsSupplier[0]['name']; 
	}
    
	$_POST['trDesc'] = $rs[0]['trdesc'];  
	$_POST['chkIsReimburse'] = $rs[0]['isreimburse'];    
	$_POST['selWarehouseKey'] = $rs[0]['warehousekey']; 
    $_POST['totalPPH'] =  $obj->formatNumber($rs[0]['totalpph'],2); 
    
    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
    $editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
    $editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';

}else{
	 
	// sementara
	$rsCurrency = $currency->searchDataRow(array( $currency->tableName.'.pkey'),' and '.$currency->tableName.'.statuskey = 1');
	$currencykey = (!empty($rsCurrency)) ? $rsCurrency[0]['pkey'] : 1;
	$rsRate = $currencyRate->getCurrencyLastRate($currencykey); 
	$_POST['currencyRate'] = $obj->formatNumber($rsRate[0]['rate'],2);

}

$rsTOP = $termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrTOP = $class->convertForCombobox($rsTOP,'pkey','name');  
$rsPaymentMethod = (empty($rs[0]['refcashadvancekey'])) ? $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')') : CASH_ADVANCE;
$arrPaymentMethod = $obj->convertForCombobox($rsPaymentMethod,'pkey','name');  
//$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'),'pkey','name');    

$arrJODetailOpt = array_merge(
    [['pkey' => 0, 'code' => '-----']],
    $rsJODetail
);

$arrJODetail = $class->convertForCombobox($arrJODetailOpt,'pkey','code');
$rsCurrency = $currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1'.$editCurrencyInactiveCriteria.')');
$arrCurrencyName = array_column($rsCurrency,null,'pkey');  
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');      
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrCurrency = $class->convertForCombobox($currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1)'),'pkey','name'); 
$arrJob = $class->convertForCombobox($emklJobOrderExport->getJobType(),'pkey','name');  
$arrTransportType = $class->convertForCombobox($emklJobOrderExport->getTransportationType(),'pkey','name');  
$arrContainer = $class->convertForCombobox($emklJobOrderExport->getLoadContainer(),'pkey','name');  
$arrPPh = $tax->generateComboboxOpt(null,array('criteria' => ' and ( '.$tax->tableName.'.typekey='.$obj->oDbCon->paramString(TAX_TYPE['PPH']).' and '.$tax->tableName.'.statuskey = 1)')); 
//$arrVolume = $class->convertForCombobox($emklJobOrderImport->getVolumeUnit(),'pkey','name');  

$rsContainer = $container->searchData();
$arrContainerVolume = $class->convertForCombobox($rsContainer,'pkey','name');  
$rsContainer = array_column($rsContainer,'name','pkey');

$rsService = $service->searchData();
$rsService = array_column($rsService,'name','pkey');
 
$arrType = array();
$arrType[$rsJOHeaderType['key']] = 'Header';
$arrType[$rsJOType['key']] = 'Order';

$emklPurchaseInvoiceValidation = $obj->loadSetting('emklPurchaseInvoiceValidation');
	

$arrReimburseTypeOpt[0]['pkey'] = -1;
$arrReimburseTypeOpt[0]['name'] = '-----';
$arrReimburseTypeOpt[1]['pkey'] = 0;
$arrReimburseTypeOpt[1]['name'] = $obj->lang['selling'];
$arrReimburseTypeOpt[2]['pkey'] = 1;
$arrReimburseTypeOpt[2]['name'] = $obj->lang['reimburse'];

$arrReimburseType = $obj->generateComboboxOpt(array('data' => $arrReimburseTypeOpt));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title></title>  
<style>
    .template-item {list-style: none; padding: 0; margin: 0;}
    .template-item li {float:left; border-radius: 0.3em; margin-right: 0.5em; margin-bottom: 0.5em; display: inline-block; background-color: #dedede; border-color: #333; padding: 0.3em 0.5em; cursor: pointer}
    .template-item li:hover {background-color: #cecece;}    
</style>    
<script type="text/javascript">  
    
	jQuery(document).ready(function(){  
        
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
	 	 var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;   
             
         var varConstant = {  
                            CURRENCY : <?php echo json_encode(CURRENCY); ?>,
                            EMKL : <?php echo json_encode(EMKL); ?>,  
                            JOBTYPE : <?php echo json_encode(array_flip($arrType)); ?>,
                            };
        
               
         var cashTOP = Array();
   
         <?php 
            for ($i=0;$i<count($rsTOP);$i++){
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
            }
         ?> 
        
        
         var emklPurchaseOrder = new EMKLPurchaseOrder(tabID,tablekey,cashTOP,varConstant); 
         prepareHandler(emklPurchaseOrder);   
         
        
         var fieldValidation =  { code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    },   
								 
								 	<?php if ($emklPurchaseInvoiceValidation == 1) {  ?>
                                    refInvoiceCode: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.invoice[1]
                                            }, 
                                        }
                                    },
								    <?php } ?>
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
       <div class="div-table main-tab-table-2 header-panel">
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
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse , array('disabled' => true)); ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputDate('trDate',array('etc' => 'max-days=14')); ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['JOCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div><?php echo $obj->inputSelect('selJOType', $arrType ); ?> </div>
                                                <div class="consume">
                                                    <div class="isheader" style="margin-right:0">
                                                         <?php    
                                                            echo $obj->inputAutoComplete(array( 
                                                                                            'revalidateField' => false, 
                                                                                            'element' => array('value' => 'jobHeaderCode',
                                                                                                               'key' => 'hidJobHeaderKey'),
                                                                                            'source' =>array(
                                                                                                                'url' => 'ajax-emkl-job-order-header.php',
                                                                                                                'data' => array( 'action' =>'searchData', 'statuskey' => '(1)', 'jobtypekey' => EMKL['jobType']['export'])
                                                                                                            ) , 
                                                                                            'allowedStatusForEdit' => array(1),
                                                                                            'callbackFunction' => 'getTabObj().onChangeJobHeader('.EMKL['jobType']['export'].')'
                                                                                          )
                                                                                    );  
                                                        ?> 
                                                    </div> 
                                                    <div class="isorder" >
                                                      <?php    
                                                            echo $obj->inputAutoComplete(array( 
                                                                                            'revalidateField' => false, 
                                                                                            'element' => array('value' => 'jobOrderCode',
                                                                                                               'key' => 'hidJobOrderKey'),
                                                                                            'source' =>array(
                                                                                                                'url' => 'ajax-emkl-job-order.php',
                                                                                                                'data' => array( 'action' =>'searchData', 'statuskey' => '(1,2,3)', 'jobtypekey' => EMKL['jobType']['export'])
                                                                                                            ) , 
                                                                                            'allowedStatusForEdit' => array(1),
                                                                                            'callbackFunction' => 'getTabObj().onChangeJobOrder('.EMKL['jobType']['export'].')'
                                                                                          )
                                                                                    );  
                                                        ?> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceReference']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputText('refInvoiceCode'); ?> 
                                        </div> 
                                    </div> 
                                     
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label>  
                                        <div class="col-xs-9"> 
                                         <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $supplier,
                                                                                'revalidateField' => false, 
                                                                                'element' => array('value' => 'supplierName',
                                                                                                   'key' => 'hidSupplierKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-supplier.php',
                                                                                                    'data' => array(  'action' =>'searchData')
                                                                                                ) , 
                                                                                'allowedStatusForEdit' => array(1),
                                                                                'callbackFunction' => 'getTabObj().onChangeSupplier()'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?> / <?php echo ucwords($obj->lang['currencyRate']); ?></label> 
                                        <div class="col-xs-9  mnv-currency"> 
                                           <div class="flex">
                                               <div><?php  echo $obj->inputSelect('selCurrency', $arrCurrency); ?></div>
                                               <div class="consume"><?php echo $obj->inputAutoDecimal('currencyRate'); ?></div>
                                           </div>
                                        </div> 
                                    </div>
                                  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                          <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reimburse']); ?></label>
                                        <div class="col-xs-9">
                                            <?php //echo $obj->inputCheckBox('chkIsReimburse'); ?>
                                            <?php echo  $obj->inputSelect('chkIsReimburse', $arrReimburseType); ?>
                                        </div>
                                    </div>
                             </div>
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['jobInformation']); ?></div>
                             <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['typeOfJob']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                            <div class="consume" ><?php echo  $obj->inputSelect('selTypeOfJob', $arrJob, array('readonly' => true)); ?></div>
                                            <div ><?php echo  $obj->inputSelect('selAirSea', $arrTransportType, array('readonly' => true)); ?></div>
                                            <div ><?php echo  $obj->inputSelect('selContainerType', $arrContainer, array('readonly' => true)); ?></div>
                                            <!--<div class="lcl-only"><?php echo  $obj->inputText('containerName' , array('readonly' => true)); ?></div> -->
                                            </div>
                                        </div>  
                                    </div>
   <div class="form-group lcl-only">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['volume']); ?> / <?php echo ucwords($obj->lang['container']); ?></label> 
                                        <div class="col-xs-9">   
                                            <div class="flex">
                                                <div class="consume"><?php echo  $obj->inputDecimal('weight', array('disabled' => true)); ?></div>
                                                <div class="text-muted" style="margin-right:20px">KG</div> 
                                                <div class="consume"><?php echo  $obj->inputDecimal('volume', array('disabled' => true)); ?></div>
                                                <div class="text-muted" style="margin-right:20px">CBM</div> 
                                                <div>/</div>
                                                <div class="consume">
                                                    <?php echo $obj->inputSelect('hidContainerKey', $arrContainerVolume, array('disabled'=>true)); ?>
                                                </div>
                                            </div>
                                        </div> 
                                    </div> 
                                    
                                    <div class="form-group fcl-only">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['volume']); ?></label> 
                                    <div class="col-xs-9">  
                                        <div class="div-table mnv-container-volume mnv-transaction transaction-detail" style="width: 100%">
                                        <?php 
                                            $totalVolumeRows = count($rsVolumeDetail);
                                            for ($j=0;$j<=$totalVolumeRows; $j++){ 
                                                
                                                $class =  'transaction-detail-row'; 
                                                $disabled = true; 
                                                $style = '';

                                                if ($j == $totalVolumeRows ){
                                                    $class = 'volume-row-template ';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                    $style = 'style="display:none !important"';
                                                } else{  
                                                    $_POST['selContainerDetailVolumeKey[]'] =  $rsVolumeDetail[$j]['itemkey'];
                                                    $_POST['qtyVolume[]'] =  $obj->formatNumber($rsVolumeDetail[$j]['qty']);
                                                
                                                }
                                                $hideDeleteIcon = 'display:none;';  
                                            ?>
												
                                            <div class="div-table-row <?php echo $class; ?> odd-style-adjustment" <?php echo $style; ?> > 
                                                <div class="div-table-col"  style="padding-left:0; padding-right:0"> 
                                                    <div class="flex">     
                                                        <div style="width:100px;">
                                                            <?php echo $obj->inputNumber('qtyVolume[]', array('disabled' => $disabled )); ?>
                                                        </div>
                                                        <div class="consume">
                                                            <?php echo $obj->inputSelect('selContainerDetailVolumeKey[]', $arrContainerVolume, array( 'disabled' => $disabled )); ?>
                                                        </div> 
                                                    </div> 
                                                </div> 
                                            </div>   
                                        <?php }	 ?>  
                                        
                                    </div>
                                    </div> 
                                </div>   
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['poReference']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('poNumber', array('readonly' => true)); ?>
                                        </div> 
                                    </div> 
  				                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bookingNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('bookingNumber', array('readonly' => true)); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['exportir']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputText('shipperName', array('readonly' => true)); ?>  
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mbl']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('mblNumber', array('readonly' => true) ); ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">POL / POD</label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume"><?php echo $obj->inputText('pol', array('readonly' => true) ); ?>  </div>
                                                <div> / </div>
                                                <div class="consume"><?php echo $obj->inputText('pod', array('readonly' => true) ); ?></div>
                                            </div>   
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo strtoupper($obj->lang['etd']); ?> / <?php echo strtoupper($obj->lang['eta']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume"><?php echo $obj->inputDate('etdPol', array('etc'=>'style="text-align:center"','readonly' => true ) ); ?></div>
                                                <div> / </div>
                                                <div class="consume"><?php echo $obj->inputDate('etaPod', array('etc'=>'style="text-align:center"','readonly' => true) ); ?></div>
                                            </div>   
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['terminal']; ?> / <?php echo $obj->lang['depot']; ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume"><?php echo $obj->inputText('terminal', array('readonly' => true ) ); ?></div>
                                                <div> / </div>
                                                <div class="consume"><?php echo $obj->inputText('depot', array('readonly' => true) ); ?></div>
                                            </div>   
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label" style="padding-top:0"><?php echo $obj->lang['stuffingDestuffingLocation']; ?></label> 
                                        <div class="col-xs-9"> <?php echo $obj->inputHidden('hidLocationKey'); ?>
                                                                <?php echo $obj->inputText('location', array('readonly' => true ) ); ?></div>  
                                   </div>  
                                    <!--<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('customerName', array('etc' => 'style="height:8em;"','readonly' => true)); ?>  
                                        </div> 
                                    </div>-->
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['containerType']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('containerNumber', array('etc' => 'style="height:8em;"','readonly' => true)); ?>  
                                        </div> 
                                    </div>
                        </div>  
                         
                    </div>
           </div>
      </div>  
       
       <ul class="template-item">
        <?php foreach($rsCostTemplate as $templateRow){ ?>
        <li class="cost-template user-select-none" relkey="<?php echo $templateRow['pkey']; ?>"><?php echo $templateRow['name']; ?></li> 
        <?php }?>
       </ul>  
      <div style="clear:both; height:1em;"></div>     
 
    <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                    <div class="div-table-row">   
                        <div class="div-table-col detail-col-header" >
                                <div class="div-table" style="width:100%">
                                     <div class="div-table-row">   
                                        <div class="div-table-col  fcl-only"  style="width:100px;"><?php echo ucwords($obj->lang['containerType']); ?></div> 
                                        <div class="div-table-col " style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                                        <div class="div-table-col "  ><?php echo ucwords($obj->lang['service']); ?></div> 
                                        <div class="div-table-col "  style="width:150px;"  ><?php echo ucwords($obj->lang['JOCode']); ?></div> 
                                        <div class="div-table-col " style="width:80px;"><?php echo ucwords($obj->lang['currencyShort']); ?></div>
                                        <div class="div-table-col " style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
                                        <div class="div-table-col " style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['total']); ?></div> 
                                        <div class="div-table-col " style="width:50px; text-align:right;"></div> 
<!--
                                        <div class="div-table-col  pph-field" style="width:100px;"><?php echo ucwords($obj->lang['PPhType']); ?></div> 
                                        <div class="div-table-col  pph-field" style="width:80px;text-align:right;"><?php echo ucwords($obj->lang['PPhValue']); ?></div>
-->
                                        <div class="div-table-col " style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?> <span class="mnv-active-currency text-muted"><?php echo $activeCurrency; ?></span></div>  
                                      </div>
                                </div>
                        </div>    
                        <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                    </div>
               
        
                    <?php  
                        $totalRows = count($rsBuyDetail); 
                        $detailDecimalPrice = 2; // karena rupiah pun bisa ad decimal
                        for ($i=0;$i<=$totalRows; $i++){  

                            $class =  'transaction-detail-row';
                            $overwrite = true;
                            $disable = '';  
                            //$detailDecimalPrice = ($rsBuyDetail[$i]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;
                            $activeCurrencyKey =  CURRENCY['idr'] ;
                            
                            if ($i == $totalRows ){
                                $class = 'detail-row-template';
                                $overwrite = false; 
                                $disable = 'disabled="disabled"'; 
                            } else {   
                                $_POST['hidDetailKey[]'] =  $rsBuyDetail[$i]['pkey'];
                                $_POST['hidContainerDetailKey[]'] =  $rsBuyDetail[$i]['itemkey'];  
                                $_POST['containerDetailName[]'] =  $rsContainer[$rsBuyDetail[$i]['itemkey']]; 
                                $_POST['hidServiceKey[]'] =  $rsBuyDetail[$i]['servicekey']; 
                                $_POST['serviceName[]'] =  $rsService[$rsBuyDetail[$i]['servicekey']];
                                $_POST['qty[]'] = $obj->formatNumber($rsBuyDetail[$i]['qty'], 2);
                                $_POST['priceInUnit[]'] = $obj->formatNumber($rsBuyDetail[$i]['priceinunit'],$detailDecimalPrice);
                                $_POST['detailSubtotal[]'] = $obj->formatNumber($rsBuyDetail[$i]['subtotal'],$detailDecimalPrice);
                                $_POST['detailRowCurrencySubtotal[]'] =  $obj->formatNumber($rsBuyDetail[$i]['subtotalcurrency'],$detailDecimalPrice);
                                $_POST['selCurrencyDetail[]'] =  $rsBuyDetail[$i]['currencykey'];
                                $_POST['description[]'] =  $rsBuyDetail[$i]['description'];
                                $_POST['detailPPHAmount[]'] =  $obj->formatNumber($rsBuyDetail[$i]['pphamount'],$detailDecimalPrice);
                                $_POST['selJobOrderDetailKey[]'] =  $rsBuyDetail[$i]['refjoborderdetailkey'];
                                $_POST['selPPhType[]'] =  $rsBuyDetail[$i]['pphtype'];
                                $activeCurrencyKey = $rsBuyDetail[$i]['currencykey']; 
                            } 

                    ?>


                       <div class="div-table-row <?php echo $class; ?>"> 
                            <div class="div-table-col">
                                <div class="div-table" style="width:100%">
                                    <div class="div-table-row"> 
                                        <div class="div-table-col  fcl-only detail-col-detail"  style="width:100px;">
                                            <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                            <?php echo $obj->inputHidden('hidContainerDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                            <?php echo $obj->inputText('containerDetailName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                        </div>   
                                        <div class="div-table-col detail-col-detail"  style="width:80px;"><?php echo $obj->inputDecimal('qty[]', array('overwritePost' => $overwrite,'value' => 1, 'etc' => 'style="text-align:right;"', 'disabled' =>  $disable)); ?></div>
                                        <div class="div-table-col detail-col-detail">
                                            <?php echo $obj->inputHidden('hidServiceKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                            <?php echo $obj->inputText('serviceName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?> 
                                        </div> 
                                        <div class="div-table-col detail-col-detail"  style="width:150px;">
                                            <?php echo $obj->inputSelect('selJobOrderDetailKey[]', $arrJODetail,array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
                                        </div>  
                                        <div class="div-table-col detail-col-detail"  style="width:80px;"><?php echo $obj->inputSelect('selCurrencyDetail[]',$arrCurrency, array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?></div>
                                        <div class="div-table-col detail-col-detail"  style="width:100px;"><?php echo $obj->inputDecimal('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' , 'disabled' =>  $disable)); ?></div>
                                        <div class="div-table-col detail-col-detail"  style="width:120px;"><?php echo $obj->inputDecimal('detailRowCurrencySubtotal[]', array('overwritePost' => $overwrite,'readonly' => true, 'etc' => 'style="text-align:right;" ' , 'disabled' =>  $disable)); ?></div>
                                        <div class="div-table-col detail-col-detail mnv-active-currency-detail text-muted"  style="width:50px;"><?php echo $arrCurrencyName[$activeCurrencyKey]['name'] ;?></div>
<!--
                                        <div class="div-table-col detail-col-detail pph-field"  style="width:100px;"><?php echo $obj->inputSelect('selPPhType[]',$arrPPh,array('overwritePost' => $overwrite,'readonly' => $readOnly)); ?></div>
                                        <div class="div-table-col detail-col-detail pph-field"  style="width:80px;"><?php echo $obj->inputDecimal('detailPPHAmount[]', array('overwritePost' => $overwrite , 'etc' => 'style="text-align:right;" ' , 'disabled' =>  $disable)); ?></div>
-->
                                        <div class="div-table-col detail-col-detail" style="width:120px;"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite,'readonly' => true, 'etc' => 'style="text-align:right;" ' , 'disabled' =>  $disable)); ?></div>
                                    </div>
                                </div>    
                                <div class="flex">
                                     <div class="consume"><?php echo $obj->inputText('description[]',array('overwritePost' => $overwrite, 'add-class' => 'label-style','etc' =>'placeholder="'.$obj->lang['description'].'"', 'disabled' =>  $disable)); ?></div>
                                     <div class="pph-field" style="width: 6em"><?php echo $obj->inputSelect('selPPhType[]',$arrPPh,array('overwritePost' => $overwrite, 'add-class' => 'label-style','readonly' => $readOnly)); ?></div>
                                     <div class="pph-field" style="width: 8em"><?php echo   $obj->inputDecimal('detailPPHAmount[]', array('overwritePost' => $overwrite , 'add-class' => 'label-style', 'etc' => 'style="text-align:right;" ' , 'disabled' =>  $disable));?></div>
                                     <div style="width:  15.5em"></div>
                                </div>
                        </div> 
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                        </div> 

                <?php } ?>    
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
         

          <div> 
                <div style="width:350px; float:right; ">
                    <div class="div-table" style="width:100%" >
                      <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;">
                                <?php echo ucwords($obj->lang['payment']); ?> 
                            </div>  
                            <div class="div-table-col-3" style="width:180px;"> 
                                 <?php echo  $obj->inputSelect('selTermOfPaymentKey', $arrTOP); ?>
                            </div> 
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div> 
                     </div>    

                    <div class="mnv-total-group mnv-payment-method cashTOP "  >  
                        <div class="div-table" style="width: 100%">
                              <div class="div-table-row  form-group"> 
                                    <div class="div-table-col-3" style="text-align:right;"> 
                                        <?php echo $obj->lang['totalPayment']; ?>
                                    </div>  
                                    <div class="div-table-col-3"  style="width:180px"> 
                                        <?php echo $obj->inputCollapsibleNumber('totalPayment', array('format' => 'decimal', 'readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
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
                                                $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount'],2); 
                                            }
                                ?> 

                                <div class="div-table-row form-group payment-detail-row <?php echo $class; ?>">
                                    <div class="div-table-col-3" style="text-align:right;">  
                                            <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px"> 
                                           <?php echo $obj->inputDecimal('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'add-class'=>'mnv-detail-field', 'etc' => 'style="text-align:right;" ')); ?>
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
                    </div>  

                  <div class="div-table" style="width:100%; margin-top:1em">

                        <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;">
                                <?php echo ucwords($obj->lang['balance']); ?> 
                            </div>  
                            <div class="div-table-col-3" style="width:180px;"> 
                                <?php echo $obj->inputDecimal('balance', array ( 'readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>  
                            </div> 
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div> 
                  </div>    
              </div>     
              
              <div class="div-table" style="float:right; margin-right:4em">
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['subtotal']); ?> 
                        </div>  
                        <div class="div-table-col-5" style="width:200px;"> 
                             <?php echo $obj->inputDecimal('subtotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                        </div>

                    </div>
                    
                     <div class="div-table-row  form-group form-detail-field"> 
                        <div class="div-table-col-5" style="text-align:right; padding-top:2em;">
                            <?php echo ucwords($obj->lang['beforeTax']); ?>
                        </div>  
                        <div class="div-table-col-5" style="padding-top:2em;"> 
                             <?php echo $obj->inputDecimal('beforeTaxTotal',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?> 
                        </div>

                    </div>

                   <div class="div-table-row  form-group"> 
                      <div class="div-table-col-5"  style="text-align:right;">
                        <?php echo $obj->lang['tax']; ?> [Include]
                     </div>   
                     <div class="div-table-col-5"> 
                         <div class="flex">    
                            <div><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>  
                            <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"')); ?></div> 
                            <div>%</div>
                            <div class="consume"><?php echo $obj->inputDecimal('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                          </div> 
                    </div> 
                 </div>  
  
                   <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                            <?php echo ucwords($obj->lang['total']); ?> 
                        </div>  
                        <div class="div-table-col-5"> 
                             <?php echo $obj->inputDecimal('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                        </div>
                        <div class="div-table-col"> </div>
                    </div>  
  
                   <div class="div-table-row  form-group pph-field"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                            <?php echo ucwords($obj->lang['total'].' PPH'); ?> 
                        </div>  
                        <div class="div-table-col-5"> 
                             <?php echo $obj->inputDecimal('totalPPH', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                        </div>
                        <div class="div-table-col"> </div>
                    </div>  

              </div>   
              <div style="clear:both"></div>
         </div>
       
        <div class="form-button-margin"></div>
        <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>  
   
     <?php echo $obj->showDataHistory(); ?>
    
</div> 
</body>

</html>
