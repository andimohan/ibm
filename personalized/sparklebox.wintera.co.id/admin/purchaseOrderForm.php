<?php 
require_once '../../../_config.php'; 
require_once '../../../_include-v2.php'; 

includeClass('PurchaseOrder.class.php', 'PurchaseCategory.class.php');
$purchaseOrder = createObjAndAddToCol(new PurchaseOrder());
$item = createObjAndAddToCol(new Item());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$paymentMethod = createObjAndAddToCol(new PaymentMethod());
$purchaseRequest = createObjAndAddToCol(new PurchaseRequest());
$supplier = createObjAndAddToCol(new Supplier()); 
$termOfPayment = createObjAndAddToCol(new TermOfPayment());
$warehouse = createObjAndAddToCol(new Warehouse()); 

$purchaseCategory = createObjAndAddToCol(new PurchaseCategory());

$obj= $purchaseOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$updatePurchaseOrderInvoiceReference = $security->isAdminLogin($obj->updatePurchaseOrderInvoiceReferenceSecurityObject, 10);

$editRefInvoiceCode = array(1);
if ($updatePurchaseOrderInvoiceReference) {
    $editRefInvoiceCode = array(1,2,3);
}

$formAction = 'purchaseOrderList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
 
$rsPurchaseDetail = array();
$rsPaymentMethodDetail = array();

$_POST['trDate'] = date('d / m / Y');
 
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber'; 

$rs = prepareOnLoadData($obj);  

$rsPurchaseRequestType = $obj->getTableKeyAndObj($purchaseRequest->tableName,array('key')); 

if (!empty($_GET['id'])){  
	$id = $_GET['id'];	  
    
    $rsPurchaseDetail = $obj->getDetailWithRelatedInformation($id);
    $rsPaymentMethodDetail = $obj->getPaymentMethodDetail($id); 
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
	$_POST['supplierName'] = $rsSupplier[0]['name'] ;
	$_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ;  

	$rsPurchaseCategory = $purchaseCategory->getDataRowById($rs[0]['categorykey']);
	$_POST['purchaseCategoryName'] = $rsPurchaseCategory[0]['name'] ;
	$_POST['hidPurchaseCategoryKey'] = $rsPurchaseCategory[0]['pkey'] ;  
		
	$_POST['overwriteRate'] = $obj->formatNumber($rs[0]['rate']);
	$_POST['trDesc'] = $rs[0]['trdesc'];
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']); 
     
    if ($rs[0]['finaldiscounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 
    
    $_POST['selType'] =  $rs[0]['reftabletype'] ; 
    
    if($rs[0]['reftabletype']==$rsPurchaseRequestType['key']){
        // PURCHASE REQUEST
        $rsPurchaseRequest = $purchaseRequest->searchData($purchaseRequest->tableName.'.pkey',$rs[0]['refkey']); 
        $_POST['hidPurchaseRequestKey'] = $rs[0]['refkey'] ;  
        $_POST['purchaseRequestCode'] = $rsPurchaseRequest[0]['code'] ; 
    }
    
	$_POST['selFinalDiscountType'] = $rs[0]['finaldiscounttype'] ;
	$_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'],$finalDiscDecimal);
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']); 
  
    $_POST['hidCurrentPurchaseRequestCode'] = $rsPurchaseRequest[0]['code'] ; 
    $_POST['hidCurrentPurchaseRequestKey'] = $rsPurchaseRequest[0]['pkey'] ;
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax']; 
    $_POST['chkIsFullReceive'] = $rs[0]['isfullreceive']; 
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
	$_POST['shipmentFee'] = $obj->formatNumber($rs[0]['shipmentfee']);
	$_POST['etcCost'] = $obj->formatNumber($rs[0]['etccost']);
	$_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'] ;
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ;  
	$_POST['refInvoiceCode'] =  $rs[0]['refinvoicecode'] ;  
    $_POST['totalPayment'] = $obj->formatNumber($rs[0]['totalpayment']); 
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey in (select paymentkey from '.$obj->tablePayment.' where refkey = '. $obj->oDbCon->paramString($rs[0]['pkey']).')';
	  
} 

$rsTOP = $termOfPayment->searchData('','',true, ' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')', ' order by duedays asc');

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrTOP = $class->convertForCombobox($rsTOP,'pkey','name');  
$arrPaymentMethod = $obj->convertForCombobox($paymentMethod->getDataForCommboboxWithPrivileges($editPaymentMethodInactiveCriteria),'pkey','name');    
$arrDefaultUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 


$arrType = array();
$arrType[$rsPurchaseRequestType['key']] = 'Request';
 
$arrActiveModule = $class->isActiveModule(array('SalesOrderCarService'));
 
if($arrActiveModule['salesordercarservice']){  
    $salesOrderCarService = createObjAndAddToCol(new SalesOrderCarService());
    $rsServiceType = $obj->getTableKeyAndObj($salesOrderCarService->tableName,array('key'));
    $arrType[$rsServiceType['key']] = 'Services';
    
    if(!empty($rs[0]['pkey'])){
         // SERVICE
         $rsSOCarService = $salesOrderCarService->searchData($salesOrderCarService->tableName.'.pkey',$rs[0]['refservicekey']); 
         $_POST['hidJobHeaderKey'] = $rs[0]['refservicekey'] ;  
         $_POST['serviceCode'] = $rsSOCarService[0]['code'] ;
    }
    
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
    
<script type="text/javascript">

    
	jQuery(document).ready(function(){   
    
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?> ; 
	 	 var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
              
          
        var varConstant = {  
                            TRANSACTIONTYPE : <?php echo json_encode(array_flip($arrType)); ?>
                            };
            var cashTOP = Array();
   
         <?php 
            for ($i=0;$i<count($rsTOP);$i++){
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
            }
         ?> 
	  
        var purchaseOrder = new PurchaseOrder(tabID,cashTOP,tablekey,varConstant); 
        prepareHandler(purchaseOrder); 
        var fieldValidation =  {
                                code: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.code[1] }, 
                                    }
                                 },
            
                                supplierName: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.supplier[1] }, 
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
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
         <?php prepareOnLoadDataForm($obj); ?>     
         <?php echo $obj->inputHidden('hidCurrentPurchaseRequestKey'); ?>
         <?php echo $obj->inputHidden('hidCurrentPurchaseRequestCode'); ?>
    
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
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['purchaseRequest']); ?></label> 
                                      <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div><?php echo $obj->inputSelect('selType', $arrType ); ?> </div>
                                                <div class="consume">
                                                    <div class="ispurchase" style="margin-right:0">
                                                       <?php  echo $obj->inputAutoComplete(array( 
                                                                            
                                                                            'element' => array('value' => 'purchaseRequestCode',
                                                                                               'key' => 'hidPurchaseRequestKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-purchase-request.php',
                                                                                                'data' => array(  'action' =>'searchData', 'statuskey' => "(2,3)" )
                                                                                            ), 
                                                                                'callbackFunction' => 'getTabObj().onChangePurchaseRequest(event, ui)' 
                                                                          )
                                                                    );  
                                                        ?>
                                                    </div> 
                                                    <div class="isservice" >
                                                      <?php    
                                                            echo $obj->inputAutoComplete(array( 
                                                                                            'revalidateField' => false, 
                                                                                            'element' => array('value' => 'serviceCode',
                                                                                                               'key' => 'hidServiceKey'),
                                                                                            'source' =>array(
                                                                                                                'url' => 'ajax-sales-order-car-service.php',
                                                                                                                'data' => array( 'action' =>'searchData', 'statuskey' => '(2,3)')
                                                                                                            ) , 
                                                                                           /* 'callbackFunction' => 'getTabObj().onChangeServiceOrder()'*/
                                                                                          )
                                                                                    );  
                                                        ?> 
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                </div>   
                                <div class="form-group">
      <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['purchaseCategory']); ?></label> 
                                    <div class="col-xs-9"> 
                                       <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $purchaseCategory,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'purchaseCategoryName',
                                                                                                   'key' => 'hidPurchaseCategoryKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-purchase-category.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
//                                                                                'popupForm' => array(
//                                                                                                    'url' => 'purchaseCategoryForm.php',
//                                                                                                    'element' => array('value' => 'statusname',
//                                                                                                           'key' => 'hidSupplierKey'),
//                                                                                                    'width' => '1000px',
//                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['supplier'])
//                                                                                                ),
//                                                                                'callbackFunction' => 'getTabObj().updateTOP()'
                                                                              )
                                                                        );  
                                            ?>
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
                                                                                'popupForm' => array(
                                                                                                    'url' => 'supplierForm.php',
                                                                                                    'element' => array('value' => 'supplierName',
                                                                                                           'key' => 'hidSupplierKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['supplier'])
                                                                                                ),
                                                                                'callbackFunction' => 'getTabObj().updateTOP()'
                                                                              )
                                                                        );  
                                            ?>
                                    </div> 
                                </div>    
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceReference']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <div class="flex">
                                            <div class="consume"><?php echo $obj->inputText('refInvoiceCode',  array('allowedStatusForEdit' => $editRefInvoiceCode)); ?></div>
                                            <?php if($updatePurchaseOrderInvoiceReference && !empty($rs) && in_array($rs[0]['statuskey'], array(2,3)) ) { ?>
                                            <div><?php echo $obj->inputButton('btnUpdate', $obj->lang['update'], array('allowedStatusForEdit'=> $editRefInvoiceCode,'class' => 'btn btn-primary btn-second-tone')); ?></div>
                                            <?php } ?>
                                        </div>
                                    </div> 
                                </div> 
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['partialShipment']); ?></label> 
                                    <div class="col-xs-1"> 
                                       <?php 
                                        $etc = (PARTIAL_SHIPMENT) ?  '' :  'onclick="return false"'; 
                                        echo $obj->inputCheckBox('chkIsFullReceive', array('value' => 1, 'etc' =>  $etc  )); ?> 
                                    </div> 
                                    <div class="col-xs-8 control-label" style="padding-left:0"><?php echo ucwords($obj->lang['fullReceived']); ?></div> 
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
         
        <div class="div-table mnv-transaction  purchase-detail transaction-detail" style="width:100%; border-bottom:1px solid #333; " attr-level="0">
                
                <div class="div-table-row"> 
                    <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
        
                            <div class="div-table-row"> 
                                <div class="div-table-col detail-col-header" style="width:480px"><?php echo ucwords($obj->lang['itemOrService']); ?></div>
                                <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                                <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                                <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']) . ' (PCS)'; ?></div>
                                <div class="div-table-col detail-col-header" style="width:60px; text-align:center;"><?php echo ucwords('/PCS'); ?></div>
                                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> @</div>
                                <!-- <div class="div-table-col detail-col-header" style="width:100px; text-align:right;  padding-right:0;"></div>
                                <div class="div-table-col detail-col-header" style="width:80px; text-align:right;   padding-left:0.2em;"><?php echo ucwords($obj->lang['discount']); ?> @</div> -->
                                <div class="div-table-col detail-col-header" style="width:180px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                                <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            
                <?php 
                    $totalRows = count($rsPurchaseDetail);
            
                    for ($i=0;$i<=$totalRows; $i++){  
					
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        $arrUnit = $arrDefaultUnit;
        
                        $priceInPcs = 'display:none';
                        $priceInUnit = '';
                        $optionRows = '';

                        $rsItemDetail = array();
                        $totalDetailRows = 0 ;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        } else {

                            $decimal = 0;
                            $inputnumber = 'inputnumber';

                            if ($rsPurchaseDetail[$i]['discounttype']  == 2){ 
                                $decimal = 2;
                                $inputnumber = 'inputdecimal';
                            }

                            $itemkey = $rsPurchaseDetail[$i]['itemkey'];

                            $_POST['hidDetailKey[]'] =  $rsPurchaseDetail[$i]['pkey'];
                            $_POST['hidItemKey[]'] =  $rsPurchaseDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsPurchaseDetail[$i]['itemname'];
                            $_POST['qty[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['qty']); 
                            $_POST['qtyInPcs[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['qtyinpcs']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['priceinunit']); 
                            $_POST['priceInPcs[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['priceinpcs']); 
                            $_POST['chkPriceInPcs[]'] =   $rsPurchaseDetail[$i]['ispriceinpcs']; 
                            // $_POST['selDiscountType[]'] =  $rsPurchaseDetail[$i]['discounttype'] ; 
                            // $_POST['discountValueInUnit[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['discount'],$decimal); 
                            $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['total']);  
                            $_POST['selUnit[]'] =  $rsPurchaseDetail[$i]['unitkey']; 

                            if($rsPurchaseDetail[$i]['ispriceinpcs'] == 1) {
                                $priceInPcs = '';
                                $priceInUnit = 'display:none';
                            } else {
                                $priceInUnit = '';
                                $priceInPcs = 'display:none';
                            } 
                            
                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsPurchaseDetail[$i]['itemkey']),'conversionunitkey','unitname'); 
                        
                            if (!empty($itemkey)){
                                $rsItemDetail = $obj->getItemDetail($rsPurchaseDetail[$i]['pkey']); 
                                $totalDetailRows = count($rsItemDetail); 
                            }

                        } 
                        
                ?>
        
                    <div class="div-table-row <?php echo $class; ?> " > 
                        <div class="div-table-col detail-col-detail" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                
                                    <div class="div-table-col detail-col-detail" style="width:470px;"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' =>  $etc,'add-class'=>'mnv-barcode-input')); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                                    <div class="div-table-col detail-col-detail" style="width:90px;"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                                    <div class="div-table-col detail-col-detail" style="width:80px;"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?></div>
                                    <div class="div-table-col detail-col-detail" style="width:80px;"><?php echo $obj->inputNumber('qtyInPcs[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                                    <div class="div-table-col detail-col-detail" style="text-align:center;width:60px;"><?php echo $obj->inputCheckbox('chkPriceInPcs[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:center;"' .$etc)) ?></div>
                                    <div class="div-table-col detail-col-detail price-in-unit-wrapper" style="width:100px;<?php echo $priceInUnit ?>"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                                    <div class="div-table-col detail-col-detail price-in-pcs-wrapper" style="width:100px;<?php echo $priceInPcs ?>"><?php echo $obj->inputNumber('priceInPcs[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                                    <!-- <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('discountValueInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                                    <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputSelect('selDiscountType[]',$obj->arrDiscountType, array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?></div> -->
                                    <div class="div-table-col detail-col-detail" style="width:180px;"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                        <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?>
                                    </div>
                                </div>
                            </div>


                            <div class="options-row" style="<?php echo $optionRows ?>" > 
                                <div style="clear:both; height:1em" ></div>

                                <div class="div-table mnv-transaction transaction-detail" style="width:100%" attr-level="1" attr-group="hidDetailItemKey" > 
                                    <div class="div-table-row"> 
                                        <div class="div-table-col">
                                            <div class="div-table" style="width:100%">

                                                <div class="div-table-col detail-col-header fcl-only col-header no-border"  style="text-align:left"><?php echo ucwords($obj->lang['size']); ?></div>
                                                <div class="div-table-col detail-col-header fcl-only col-header no-border"  style="width:120px;text-align:right"><?php echo ucwords($obj->lang['qty']); ?></div>
                                                <div class="div-table-col detail-col-header fcl-only col-header no-border"  style="width:120px;text-align:right"><?php echo ucwords($obj->lang['qty'] . ' (PCS)'); ?></div>
                                                <div class="div-table-col detail-col-detail col-header no-border" style="width:300px;"><?php echo ucwords($obj->lang['packaging']); ?></div>
                                                <div class="div-table-col detail-col-header fcl-only col-header no-border"  style="width:120px;text-align:right"><?php echo ucwords($obj->lang['qty'] . ' /PCKG'); ?></div>
                                                <div class="div-table-col detail-col-header fcl-only col-header no-border"  style="width:120px;text-align:right"><?php echo ucwords($obj->lang['qty'].' Pckg'); ?></div>
                                                
                                                <div class="div-table-col  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                                                <div class="div-table-col  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>

                                            </div>
                                        </div>
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


                                                $_POST['hidDetailItemKey[]'] =  $rsItemDetail[$j]['pkey'];
                                                $_POST['itemSize[]'] =  $rsItemDetail[$j]['itemsize'];
                                                $_POST['qtyDetail[]'] =  $obj->formatNumber($rsItemDetail[$j]['qty']);
                                                $_POST['qtyInPcsDetail[]'] =  $obj->formatNumber($rsItemDetail[$j]['qtyinpcs']);
                                                $_POST['qtyInPackageDetail[]'] =  $obj->formatNumber($rsItemDetail[$j]['qtyinpackage']);
                                                $_POST['hidPackagingKey[]'] =  $rsItemDetail[$j]['packagingkey'];
                                                $_POST['packagingName[]'] =  $rsItemDetail[$j]['packagingname'];
                                                $_POST['qtyPackageDetail[]'] =  $obj->formatNumber($rsItemDetail[$j]['qtypackage']);

                                            }
                                    
                                    ?>

                                        <div class="div-table-row service-detail-row <?php echo $classDetail; ?>" >
                                            <div class="div-table-col" >
                                                <div class="div-table" style="width:100%">  
                                                    <div class="div-table-row">

                                                        <div class="div-table-col" style="vertical-align:top;">
                                                            <?php echo $obj->inputHidden('hidDetailItemKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?> 
                                                            <?php echo $obj->inputText('itemSize[]',array('overwritePost' => $overwriteDetail,'class' => 'form-control label-style', 'disabled' => $disabledDetail)); ?>
                                                        </div>
                                                        <div class="div-table-col" style="vertical-align:top;width:120px;text-align:right;">
                                                            <?php echo $obj->inputNumber('qtyDetail[]', array('overwritePost' => $overwriteDetail,'class' => 'form-control inputautodecimal label-style',   'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ' )); ?>
                                                        </div>
                                                        <div class="div-table-col" style="vertical-align:top;width:120px;text-align:right;">
                                                            <?php echo $obj->inputNumber('qtyInPcsDetail[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control inputautodecimal label-style', 'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
                                                        </div>
                                                        <div class="div-table-col" style="vertical-align:top;width:300px;">
                                                            <?php echo $obj->inputHidden('hidPackagingKey[]', array('overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail)); ?>
                                                            <?php echo $obj->inputText('packagingName[]',array('overwritePost' => $overwriteDetail,'class' => 'form-control label-style', 'disabled' => $disabledDetail)); ?>
                                                        </div>
                                                        <div class="div-table-col" style="vertical-align:top;width:120px;text-align:right;">
                                                            <?php echo $obj->inputNumber('qtyInPackageDetail[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control inputautodecimal label-style', 'disabled' => $disabledDetail, 'etc' => ' style="text-align:right;"  ')); ?>
                                                        </div>
                                                        <div class="div-table-col" style="vertical-align:top;width:120px;text-align:right;">
                                                            <?php echo $obj->inputNumber('qtyPackageDetail[]', array('overwritePost' => $overwriteDetail, 'class' => 'form-control inputautodecimal label-style', 'readonly' => true, 'etc' => ' style="text-align:right;"  ')); ?>
                                                        </div>
                                                        <div class="div-table-col icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top;"><?php echo $obj->inputLinkButton('btnAddDetailRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="service-row-template"')); ?></div>
                                                        <div class="div-table-col icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top;"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; "')); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <?php } ?>

                                </div>
                            </div>
                            

                            
                        </div>

                    </div> 

                <?php  }   ?>  
                   
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
                                                $_POST['paymentMethodValue[]'] = $obj->formatNumber($rsPaymentMethodDetail[$i]['amount']); 
                                            }
                                ?> 

                                <div class="div-table-row form-group payment-detail-row <?php echo $class; ?>">
                                    <div class="div-table-col-3" style="text-align:right;">  
                                            <?php echo $obj->inputHidden('hidDetailPaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
                                            <?php echo  $obj->inputSelect('selPaymentMethod[]', $arrPaymentMethod, array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                    </div>  
                                    <div class="div-table-col-3" style="width:180px"> 
                                           <?php echo $obj->inputNumber('paymentMethodValue[]', array('overwritePost' => $overwrite, 'disabled' => $disabled,'class'=>'form-control inputnumber mnv-detail-field', 'etc' => 'style="text-align:right;" ')); ?>
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
              
              <div class="div-table" style="float:right; margin-right:4em">
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;">
                            <?php echo ucwords($obj->lang['subtotal']); ?> 
                        </div>  
                        <div class="div-table-col-5" style="width:200px;"> 
                             <?php echo $obj->inputNumber('subtotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                        </div>

                    </div>
                        <!-- <div class="div-table-row  form-group"> 
                            <div class="div-table-col-5"  style="text-align:right;">
                                 <?php echo ucwords($obj->lang['discount']); ?>
                            </div>  
                            <div class="div-table-col-5"> 
                                <div class="flex">          
                                    <div><?php echo $obj->inputSelect('selFinalDiscountType',$obj->arrDiscountType); ?> </div>
                                    <div class="consume"> <?php echo $obj->inputNumber('finalDiscount', array ('class'=> 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;" ')) ;?> </div>
                                 </div> 
                            </div> 
                        </div> -->

                     <div class="div-table-row  form-group   form-detail-field"> 
                        <div class="div-table-col-5" style="text-align:right; padding-top:2em;">
                            <?php echo ucwords($obj->lang['beforeTax']); ?>
                        </div>  
                        <div class="div-table-col-5" style="padding-top:2em;"> 
                             <?php echo $obj->inputNumber('beforeTaxTotal',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?> 
                        </div>

                    </div>

                   <div class="div-table-row  form-group"> 
                      <div class="div-table-col-5"  style="text-align:right;">
                        <?php echo strtoupper($obj->lang['PPN']); ?> [Include]
                     </div>   
                     <div class="div-table-col-5"> 
                         <div class="flex">    
                            <div><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>  
                            <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"')); ?></div> 
                            <div>%</div>
                            <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                          </div> 
                    </div> 
                 </div>  

                     <div class="div-table-row  form-group   form-detail-field"> 
                        <div class="div-table-col-5"  style="text-align:right; padding-top:2em;">
                             <?php echo ucwords($obj->lang['shippingFee']); ?>
                        </div>  
                        <div class="div-table-col-5" style=" padding-top:2em;" > 
                                <?php echo $obj->inputNumber('shipmentFee', array('etc' => 'style="text-align:right;" ')); ?>
                        </div>
                        <div class="div-table-col" > </div>
                    </div>

                     <div class="div-table-row  form-group   form-detail-field"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                            <?php echo ucwords($obj->lang['others']); ?>
                        </div>      
                        <div class="div-table-col-5"> 
                            <?php echo $obj->inputNumber('etcCost', array('etc' => 'style="text-align:right;"')); ?> 
                          </div>
                        <div class="div-table-col" > </div>
                    </div>
                   <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;"> 
                            <?php echo ucwords($obj->lang['total']); ?> 
                        </div>  
                        <div class="div-table-col-5"> 
                             <?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                        </div>
                        <div class="div-table-col"> </div>
                    </div> 
                     <div class="div-table-row  form-group"> 
                        <div class="div-table-col-5" style="text-align:right;"> </div>  
                        <div class="div-table-col-5"> 
                               <div class="form-detail-button" style="float:right; text-align:right; padding-right:0; padding-top:0; " relalt="<?php echo ucwords($obj->lang['hideDetail']); ?>"> <?php echo ucwords($obj->lang['showDetail']); ?> </div>
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
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
