<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('EMKLCommission.class.php'));
$emklCommission = createObjAndAddToCol(new EMKLCommission());  
$currency = createObjAndAddToCol(new Currency());
$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder());
$paymentMethod = createObjAndAddToCol(new PaymentMethod());
$supplier = createObjAndAddToCol(new Supplier());  
$termOfPayment = createObjAndAddToCol(new TermOfPayment()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 

$obj= $emklCommission;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
 
$formAction = 'emklCommissionList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

//$rsStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','textcolor');   
$rs = prepareOnLoadData($obj); 
$rsCommissionDetail = array();
$rsPaymentMethodDetail = array();

$_POST['trDate'] = date('d / m / Y'); 
$_POST['etdPol'] = date('d / m / Y'); 
$_POST['etaPod'] = date('d / m / Y'); 
$_POST['selTypeOfJob'] = EMKL['jobType']['export'];

$editWarehouseInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$decimalPrice = ($rs[0]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;   

$activeCurrency = 'IDR';
if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
    $rsCommissionDetail = $obj->getDetailWithRelatedInformation($id); 
     
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id);
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y '); 
	$_POST['selCurrency'] = $rs[0]['currencykey']; 
	$rsCurrency = $currency->getDataRowById($rs[0]['currencykey']);
	$activeCurrency = $rsCurrency[0]['name'];
    $_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'],2);    
	$_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'] ; 
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment'],$decimalPrice);  
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal'],$decimalPrice); 
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance'],$decimalPrice) ; 

    
	if (!empty($rs[0]['refkey'])){
        $_POST['hidJobOrderKey'] = $rs[0]['refkey'] ;  
        $rsEmkl = $emklJobOrderExport->searchData($emklJobOrderExport->tableName.'.pkey',$rs[0]['refkey']); 
        $_POST['jobOrderCode'] = $rsEmkl[0]['code'] ;
        $_POST['selTypeOfJob'] = $rsEmkl[0]['jobtypekey'];
        $_POST['selAirSea'] = $rsEmkl[0]['transportationtypekey'];
        $_POST['selContainerType'] = $rsEmkl[0]['loadcontainertypekey'];
	    $_POST['containerName'] = $rsEmkl[0]['containername']; 
        $_POST['poNumber'] = $rsEmkl[0]['ponumber']; 
        $_POST['bookingNumber'] = $rsEmkl[0]['bookingnumber']; 
        $_POST['mblNumber'] = $rsEmkl[0]['mblnumber']; 
        $_POST['etdPol'] = $obj->formatDBDate($rsEmkl[0]['etdpol'],'d / m / Y ');
        $_POST['etaPod'] = $obj->formatDBDate($rsEmkl[0]['etapod'],'d / m / Y ');
        $_POST['containerNumber'] = $rsEmkl[0]['containernumber'];
        $_POST['shipperName'] = $rsEmkl[0]['customername'];
        $_POST['pol'] = $rsEmkl[0]['polname'];
        $_POST['pod'] = $rsEmkl[0]['podname'];
    } 
        
    if (!empty($rs[0]['supplierkey'])){
        $_POST['hidSupplierKey'] = $rs[0]['supplierkey']; 
		$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
		$_POST['supplierName'] = $rsSupplier[0]['name']; 
	}
    
	$_POST['trDesc'] = $rs[0]['trdesc'];   
	$_POST['selWarehouseKey'] = $rs[0]['warehousekey']; 
    
    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
    $editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
    $editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';

}

// sementar aambil yg piutang saja dulu
$rsTOP = $termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1 ' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');
$arrTOP = $class->convertForCombobox($rsTOP,'pkey','name');  
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')'),'pkey','name');
$rsCurrency = $currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1'.$editCurrencyInactiveCriteria.')');
$arrCurrencyName = array_column($rsCurrency,null,'pkey');  
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');      
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrCurrency = $class->convertForCombobox($currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1)'),'pkey','name'); 
$arrJob = $class->convertForCombobox($emklJobOrderExport->getJobType(),'pkey','name');  
$arrTransportType = $class->convertForCombobox($emklJobOrderExport->getTransportationType(),'pkey','name');  
$arrContainer = $class->convertForCombobox($emklJobOrderExport->getLoadContainer(),'pkey','name');  
//$arrVolume = $class->convertForCombobox($emklJobOrderImport->getVolumeUnit(),'pkey','name');  
 
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
                            EMKL : <?php echo json_encode(EMKL); ?>,  
                            };
        
               
         var cashTOP = Array();
   
         <?php 
            for ($i=0;$i<count($rsTOP);$i++){
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
            }
         ?> 
        
        
         var emklCommission = new EMKLCommission(tabID,cashTOP,varConstant); 
         prepareHandler(emklCommission);   
         
        
         var fieldValidation =  { code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    },  
                                    jobOrderCode: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.jobOrder[1]
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
                                            <?php echo $obj->inputDate('trDate'); ?> 
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
                                                                                'callbackFunction' => 'getTabObj().updateTOP()'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
 
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobOrderCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $emklJobOrderExport,
                                                                                'revalidateField' => false, 
                                                                                'element' => array('value' => 'jobOrderCode',
                                                                                                   'key' => 'hidJobOrderKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-emkl-job-order.php',
                                                                                                    'data' => array( 'action' =>'searchData', 'statuskey' => '(2,3)')
                                                                                                ) , 
                                                                                'allowedStatusForEdit' => array(1),
                                                                                'callbackFunction' => 'getTabObj().updateFromJobOrder()'
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                          <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
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
                                            <div class="lcl-only"><?php echo  $obj->inputText('containerName' , array('readonly' => true)); ?></div> 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipper']); ?></label> 
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
                                                <div class="consume"><?php echo $obj->inputDate('etdPol', array('etc'=>'style="text-align:center"','readonly' => true) ); ?></div>
                                                <div> / </div>
                                                <div class="consume"><?php echo $obj->inputDate('etaPod', array('etc'=>'style="text-align:center"','readonly' => true) ); ?></div>
                                            </div>   
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['containerNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('containerNumber', array('etc' => 'style="height:8em;"','readonly' => true)); ?>  
                                        </div> 
                                    </div>
                        </div>   
                    </div>
           </div>
      </div>  
      <div style="clear:both; height:1em;"></div> 
    
 
    <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">    
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['description']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['currencyShort']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['total']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:50px; text-align:right;"></div> 
                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?> <span class="mnv-active-currency text-muted"><?php echo $activeCurrency; ?></span></div> 
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                  </div>
        
                    <?php  
                        $totalRows = count($rsCommissionDetail); 

                        for ($i=0;$i<=$totalRows; $i++){  

                            $class =  'transaction-detail-row';
                            $overwrite = true;
                            $disable = '';   
                            $detailDecimalPrice = ($rsCommissionDetail[$i]['currencykey'] == CURRENCY['idr'] ) ? 0 : 2;    
                            $activeCurrencyKey =  CURRENCY['idr'] ;
                            
                            if ($i == $totalRows ){
                                $class = 'detail-row-template';
                                $overwrite = false; 
                                $disable = 'disabled="disabled"';
                                $numberClass = 'inputnumber';
                            } else {  
                                $decimal = 0;
                                $inputnumber = 'inputnumber';
                                
                                $_POST['hidDetailKey[]'] =  $rsCommissionDetail[$i]['pkey']; 
                                $_POST['qty[]'] = $obj->formatNumber($rsCommissionDetail[$i]['qty'], 2);
                                $_POST['priceInUnit[]'] = $obj->formatNumber($rsCommissionDetail[$i]['priceinunit'],$detailDecimalPrice);
                                $_POST['detailDescription[]'] =  $rsCommissionDetail[$i]['description']; 
                                $_POST['detailRowCurrencySubtotal[]'] =  $obj->formatNumber($rsCommissionDetail[$i]['subtotalcurrency'],$detailDecimalPrice);
                                $_POST['selCurrencyDetail[]'] =  $rsCommissionDetail[$i]['currencykey']; 
                                $activeCurrencyKey = $rsCommissionDetail[$i]['currencykey'];
                                $_POST['detailSubtotal[]'] = $obj->formatNumber($rsCommissionDetail[$i]['subtotal'],$decimalPrice);
                            } 

                    ?>


                    <div class="div-table-row <?php echo $class; ?>">  
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?> 
                                <?php echo $obj->inputDecimal('qty[]', array('overwritePost' => $overwrite,'value' => 0, 'etc' => 'style="text-align:right;"', 'disabled' =>  $disable)); ?>
                            </div> 
                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('detailDescription[]', array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?></div>
							<div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selCurrencyDetail[]',$arrCurrency, array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?></div>
                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite,'value' => 0, 'etc' => 'style="text-align:right;"' , 'disabled' =>  $disable)); ?></div>
							
                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailRowCurrencySubtotal[]', array('overwritePost' => $overwrite,'readonly' => true, 'etc' => 'style="text-align:right;" ' , 'disabled' =>  $disable )); ?></div>
                            <div class="div-table-col detail-col-detail mnv-active-currency-detail text-muted"><?php echo $arrCurrencyName[$activeCurrencyKey]['name'] ;?></div>
                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite,'readonly' => true, 'etc' => 'style="text-align:right;" ' , 'disabled' =>  $disable )); ?></div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                        </div>

                <?php } ?>    
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows']); ?></div>
         

          <div> 
                <div style="width:350px; float:right; ">
                    <div class="div-table" style="width:100%" > 
                        <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3" style="text-align:right;"> 
                                <?php echo ucwords($obj->lang['total']); ?> 
                            </div>  
                            <div class="div-table-col-3"> 
                                 <?php echo $obj->inputNumber('total', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>  
                            </div>
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div>  
                        
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
                                            <?php echo $obj->inputCollapsibleNumber('totalPayment', array('readonly' => true, 'etc' => 'style="text-align:right;" ')); ?> 
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
                                                $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount'],$decimalPrice); 
                                            }
                                ?> 

                                <div class="div-table-row form-group payment-detail-row <?php echo $class; ?>">
                                    <div class="div-table-col-3" style="text-align:right;">  
                                            <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px"> 
                                           <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'etc' => 'style="text-align:right;" ')); ?>
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
                                <?php echo $obj->inputNumber('balance', array ( 'readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>  
                            </div> 
                            <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                        </div> 
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

