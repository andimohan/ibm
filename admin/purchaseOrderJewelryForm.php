<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('PurchaseOrderJewelry.class.php', 'PurchaseCategory.class.php');
$purchaseOrderJewelry = createObjAndAddToCol(new PurchaseOrderJewelry());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$paymentMethod = createObjAndAddToCol(new PaymentMethod());
$purchaseRequest = createObjAndAddToCol(new PurchaseRequest());
$supplier = createObjAndAddToCol(new Supplier()); 
$termOfPayment = createObjAndAddToCol(new TermOfPayment());
$warehouse = createObjAndAddToCol(new Warehouse()); 
$receivingPurchaseJewelry = createObjAndAddToCol(new ReceivingPurchaseJewelry()); 

$purchaseCategory = createObjAndAddToCol(new PurchaseCategory());

$obj= $purchaseOrderJewelry;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$formAction = 'purchaseOrderJewelryList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
 
$rsPurchaseDetail = array();
$rsPaymentMethodDetail = array();

$_POST['trDate'] = date('d / m / Y');
 
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber'; 


$finalDisc2Decimal = 0;
$finalDisc2DecimalType = 'inputnumber'; 

$rs = prepareOnLoadData($obj);  

$rsPurchaseRequestType = $obj->getTableKeyAndObj($purchaseRequest->tableName,array('key')); 

if (!empty($_GET['id'])){  
	$id = $_GET['id'];	  
    
    $rsPurchaseDetail = $obj->getDetailWithRelatedInformation($id,'',' order by '.$obj->tableNameDetail.'.number asc');
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
    
    if ($rs[0]['finaldiscount2type']  == 2){ 
        $finalDisc2Decimal = 2;
        $finalDisc2DecimalType = 'inputdecimal';
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
	$_POST['afterFinalDiscount'] = $obj->formatNumber($rs[0]['afterfinaldiscount']);
	
    $_POST['selFinalDiscount2Type'] = $rs[0]['finaldiscount2type'] ;
	$_POST['finalDiscount2'] = $obj->formatNumber($rs[0]['finaldiscount2'],$finalDisc2Decimal);
	
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']); 
  
    $_POST['hidCurrentPurchaseRequestCode'] = $rsPurchaseRequest[0]['code'] ; 
    $_POST['hidCurrentPurchaseRequestKey'] = $rsPurchaseRequest[0]['pkey'] ;
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax']; 
    // $_POST['chkIsFullReceive'] = $rs[0]['isfullreceive']; 
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
                            TRANSACTIONTYPE : <?php echo json_encode(array_flip($arrType)); ?>,
                            TABLEKEY : tablekey
                            };
            var cashTOP = Array();
   
         <?php 
            for ($i=0;$i<count($rsTOP);$i++){
                if ($rsTOP[$i]['duedays'] <> 0)
                    echo 'cashTOP.push('.$rsTOP[$i]['pkey'].');'.chr(13);
            }
         ?> 
	  
        var purchaseOrderJewelry = new PurchaseOrderJewelry(tabID,cashTOP,varConstant); 
        prepareHandler(purchaseOrderJewelry); 
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
                                 <!-- <div class="form-group">
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
                                </div>    -->
                                
                                <!-- <div class="form-group">
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
                                </div>   -->
					 
								 
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
                                        <div class="consume"><?php echo $obj->inputText('refInvoiceCode'); ?></div>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php //echo ucwords($obj->lang['partialShipment']); ?></label> 
                                    <div class="col-xs-1"> 
                                        <?php 
                                        // $etc = (PARTIAL_SHIPMENT) ?  '' :  'onclick="return false"'; 
                                        // echo $obj->inputCheckBox('chkIsFullReceive', array('value' => 1, 'etc' =>  $etc  )); 
                                        ?> 
                                    </div> 
                                    <div class="col-xs-8 control-label" style="padding-left:0"><?php //echo ucwords($obj->lang['fullReceived']); ?></div> 
                                </div>     -->
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
        
        
        <div class="div-table mnv-transaction transaction-detail row-panel" style="width:100%; border-bottom:1px solid #333; ">
        
            <div class="div-table-row">  
            <div class="div-table-col" style="padding:0">
            <div class="div-table" style="width:100%">

                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="width:60px; text-align:right;"><?php echo ucwords($obj->lang['number']); ?></div>
                    <div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['item']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']) . ' Gr'; ?></div>
                    <div class="div-table-col detail-col-header" style="width:50px; text-align:center;"><?php echo ucwords('/Gr'); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> Unit</div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> Gr</div>
                    <!-- <div class="div-table-col detail-col-header" style="width:100px; text-align:right;  padding-right:0;"></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;   padding-left:0.2em;"><?php echo ucwords($obj->lang['discount']); ?> @</div> -->
                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                                                                        
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"  style="width: 45px"></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
                
            </div>
            </div>
            </div>
                
              
                <?php 
                    $totalRows = count($rsPurchaseDetail);
            
                    $rsReceivingPurchase = array();
                    for ($i=0;$i<=$totalRows; $i++){  
					
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        $arrUnit = $arrDefaultUnit;

                        $readOnlyPriceInPcs = true;
                        $readOnlyPriceInUnit = false;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"';
                        $_POST['chkPriceInPcs[]'] = 0;
                        } else {
                            $decimal = 0;
                            $inputnumber = 'inputnumber';

                            if ($rsPurchaseDetail[$i]['discounttype']  == 2){ 
                                $decimal = 2;
                                $inputnumber = 'inputdecimal';
                            }


                            $_POST['hidDetailKey[]'] =  $rsPurchaseDetail[$i]['pkey'];
                            //$_POST['detailNumber[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['number']); 
                            $_POST['itemName[]'] =  $rsPurchaseDetail[$i]['itemname'];
                            $_POST['qty[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['qty']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['priceinunit']); 
                            $_POST['qtyInPcs[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['qtyinpcs']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['priceinunit']); 
                            $_POST['priceInPcs[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['priceinpcs']); 
                            $_POST['chkPriceInPcs[]'] =   $rsPurchaseDetail[$i]['ispriceinpcs']; 
                            // $_POST['selDiscountType[]'] =  $rsPurchaseDetail[$i]['discounttype'] ; 
                            // $_POST['discountValueInUnit[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['discount'],$decimal); 
                            $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['total']);  
                            $_POST['selUnit[]'] =  $rsPurchaseDetail[$i]['unitkey']; 
                            $_POST['detailNotes[]'] =  $rsPurchaseDetail[$i]['trdesc']; 
                            
                            if($rsPurchaseDetail[$i]['ispriceinpcs'] == 1) {
                                $readOnlyPriceInPcs = false;
                                $readOnlyPriceInUnit = true;
                            } else {
                                $readOnlyPriceInPcs = true;
                                $readOnlyPriceInUnit = false;
                            } 

                        } 
                        $rsReceivingPurchase = $receivingPurchaseJewelry->getDetailForPurchaseOrder($rsPurchaseDetail[$i]['pkey']);
                ?>
                <div class="div-table-row <?php echo $class; ?>"> 
                    
                <div class="div-table-col"  style="padding:0">
                
                <div class="div-table" style="width:100%">
                    <div class="div-table-row">

                        <div class="div-table-col detail-col-detail" style="width:60px; text-align:right;">
                            <?php echo $obj->inputInteger('numberDetail[]', array('overwritePost' => $overwrite, 'readonly'=>true, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' =>  $etc,'add-class'=>'mnv-barcode-input')); ?>
                            <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                        <div class="div-table-col detail-col-detail" style="width:80px; text-align:right;"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                        <div class="div-table-col detail-col-detail" style="width:80px;"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?></div>
                        <div class="div-table-col detail-col-detail" style="width:80px; text-align:right;"><?php echo $obj->inputNumber('qtyInPcs[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' . $etc)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail" style="text-align:center;width:50px;"><?php echo $obj->inputCheckbox('chkPriceInPcs[]', array('overwritePost' => $overwrite, 'value' => 0, 'etc' => 'style="text-align:center;"' .$etc)) ?></div>
                        <div class="div-table-col detail-col-detail price-in-unit-wrapper" style="width:120px;text-align:right;"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'readonly' =>  $readOnlyPriceInUnit, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                        <div class="div-table-col detail-col-detail price-in-pcs-wrapper" style="width:120px;text-align:right;"><?php echo $obj->inputNumber('priceInPcs[]', array('overwritePost' => $overwrite, 'readonly' =>  $readOnlyPriceInPcs, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                        <!-- <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('discountValueInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selDiscountType[]',$obj->arrDiscountType, array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?></div> -->
                        <div class="div-table-col detail-col-detail" style="width:180px; text-align:right;"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                        <div class="div-table-col-3 icon-col <?php echo $obj->hideOnDisabled(); ?>"  style="width: 45px">
                            <div class="flex">
                                <?php echo $obj->inputHidden('hidOrderList[]', array('readonly' => true,'overwritePost' => $overwriteDetail, 'disabled' => $disabledDetail )); ?>
                                <i class="fas arrow-nav fa-arrow-circle-up" rel="-1" rel-ctr="numberDetail[]"></i>
                                <i class="fas arrow-nav fa-arrow-circle-down " rel="1" rel-ctr="numberDetail[]"></i>
                            </div> 
                        </div> 
                        <div class="div-table-col-3 icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="detail-row-template"')); ?></div>
                                 
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col">
                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?>
                        </div>
                    </div> 
                </div>

                <div class="div-table" style="width:100%">
                    <div class="div-table-row">
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('detailNotes[]',array('overwritePost' => $overwrite,'etc' => 'placeholder="'.$obj->lang['description'].'"')); ?></div> 
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                    </div>
                </div>

                <?php   if(!empty($rsReceivingPurchase) && (in_array($rs[0]['statuskey'], [2,3]))) {   ?>
                <div class="" style="height:0.5em;"></div>
                <div class="div-table" style="width:100%;"> 
                        <div class="div-table table-subdetail" style="margin:1em 2em; width:80%; ">
                            <div class="div-table-caption" style="font-weight:bold"><?php echo $obj->lang['purchaseReceive'] ?></div> 
                            <div class="div-table-row row-header">
                                <div class="div-table-col " style="width:9em; text-align:left;"><?php echo ucwords($obj->lang['transactionCode']); ?></div>
                                <div class="div-table-col " style="width:15em; text-align:left;"><?php echo ucwords($obj->lang['item']); ?></div>
                                <div class="div-table-col " style="width:8em; text-align:right;"><?php echo ucwords($obj->lang['received']); ?></div>
                                <div class="div-table-col " style="width:6em;">Unit</div>
                                <div class="div-table-col " style="width:7em; text-align:right;"><?php echo ucwords($obj->lang['received'] . ' Gr'); ?></div>
                                <div class="div-table-col " style="width:7em; text-align:right;">GW (Gr)</div>
                                <div class="div-table-col " style="width:8em; text-align:left;"><?php echo ucwords($obj->lang['packaging']); ?></div>
                                <div class="div-table-col " style=""></div>

                            </div>  
                                <?php
                                    $totalReceived = 0;
                                    $totalReceivedGram = 0;
                                    $totalGW = 0;
                                ?>
                                <?php  for($j=0; $j<count($rsReceivingPurchase); $j++) { 
                                
                                        $totalReceived += $rsReceivingPurchase[$j]['receivedqtyinbaseunit'];
                                        $totalReceivedGram += $rsReceivingPurchase[$j]['receivedqtyinpcs']; 
                                        $totalGW += $rsReceivingPurchase[$j]['grossweight']; 
 
                                    ?>
                                    <div class="div-table-row">
                                        <div class="div-table-col" style="font-style:italic;"><?php echo $rsReceivingPurchase[$j]['code']; ?></div>
                                        <div class="div-table-col" style="font-style:italic;"><?php echo $rsReceivingPurchase[$j]['itemname']; ?></div>
                                        <div class="div-table-col" style="text-align:right;font-style:italic;"><?php echo $obj->formatNumber($rsReceivingPurchase[$j]['receivedqtyinbaseunit']); ?></div>
                                        <div class="div-table-col" style="font-style:italic;"><?php echo $rsReceivingPurchase[$j]['baseunitname']; ?></div>
                                        <div class="div-table-col" style="text-align:right;font-style:italic;"><?php echo $obj->formatNumber($rsReceivingPurchase[$j]['receivedqtyinpcs']); ?></div>
                                        <div class="div-table-col" style="text-align:right;font-style:italic;"><?php echo $obj->formatNumber($rsReceivingPurchase[$j]['grossweight'],2); ?></div>
                                        <div class="div-table-col" style="font-style:italic;"><?php echo $rsReceivingPurchase[$j]['packagingname']; ?></div>
                                       <div class="div-table-col"></div>
                                    </div>
                                <?php } ?> 
                                
                                 <div class="div-table-row  row-footer" style="font-weight:bold">
                                        <div class="div-table-col"></div>
                                        <div class="div-table-col" style="text-align:right"><?php echo $obj->lang['total'] ?></div>
                                        <div class="div-table-col" style="text-align:right;font-style:italic;"><?php echo $obj->formatNumber($totalReceived); ?></div>
                                        <div class="div-table-col"></div>
                                        <div class="div-table-col" style="text-align:right;font-style:italic;"><?php echo $obj->formatNumber($totalReceivedGram); ?></div>
                                        <div class="div-table-col" style="text-align:right;font-style:italic;"><?php echo $obj->formatNumber($totalGW,2); ?></div>
                                        <div class="div-table-col"></div>
                                        <div class="div-table-col"></div>
                                    </div> 
                        </div>
                </div>
                    <?php 
                        } 
                    ?>

                <div class="" style="height:0.5em;"></div>
                
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
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['total']); ?>
                        </div>
                        <div class="div-table-col-3" >
                            <div class="flex">
                                <div><?php echo $obj->inputNumber('totalQty', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                <div>/</div>
                                <div><?php echo $obj->inputNumber('totalQtyInPcs', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                                <div>Gr</div>
                            </div>
                            
                        </div>
                    
                    </div>
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['subtotal']); ?> 
                        </div>  
                        <div class="div-table-col-3" style="width:200px;"> 
                             <?php echo $obj->inputNumber('subtotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                        </div>

                    </div>

                  <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3"  style="text-align:right;">
                                <?php echo ucwords($obj->lang['discount'] . ' #1'); ?>
                            </div>  
                            <div class="div-table-col-3"> 
                                <div class="flex">          
                                    <div><?php echo $obj->inputSelect('selFinalDiscountType',$obj->arrDiscountType); ?> </div>
                                    <div class="consume"> <?php echo $obj->inputNumber('finalDiscount', array ('class'=> 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;" ')) ;?> </div>
                                 </div> 
                            </div> 
                    </div>

                    <div class="div-table-row  form-group" style="display:none"> 
                        <div class="div-table-col-3" style="text-align:right;">
                           
                        </div>
                        <div class="div-table-col-3" style="padding-bottom:2em;">
                            <?php echo $obj->inputNumber('afterFirstDiscount', array('readonly' => true, 'etc' => 'style="text-align:right;')); ?>
                        </div>
                    
                    </div>

                    <div class="div-table-row  form-group"> 
                            <div class="div-table-col-3"  style="text-align:right;">
                                <?php echo ucwords($obj->lang['discount'] . ' #2'); ?>
                            </div>  
                            <div class="div-table-col-3"> 
                                <div class="flex">          
                                    <div><?php echo $obj->inputSelect('selFinalDiscount2Type',$obj->arrDiscountType); ?> </div>
                                    <div class="consume"> <?php echo $obj->inputNumber('finalDiscount2', array ('class'=> 'form-control ' . $finalDiscDecimalType, 'etc' => 'style="text-align:right;" ')) ;?> </div>
                                 </div> 
                            </div> 
                    </div>

                     <div class="div-table-row  form-group   form-detail-field"> 
                        <div class="div-table-col-3" style="text-align:right; padding-top:2em;">
                            <?php echo ucwords($obj->lang['beforeTax']); ?>
                        </div>  
                        <div class="div-table-col-3" style="padding-top:2em;"> 
                             <?php echo $obj->inputNumber('beforeTaxTotal',array('readonly' => true,  'etc' => 'style="text-align:right;')); ?> 
                        </div>

                    </div>

                   <div class="div-table-row  form-group"> 
                      <div class="div-table-col-3"  style="text-align:right;">
                        <?php echo strtoupper($obj->lang['PPN']); ?> [Include]
                     </div>   
                     <div class="div-table-col-3"> 
                         <div class="flex">    
                            <div><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>  
                            <div class="percentage-col"><?php echo $obj->inputDecimal('taxPercentage', array('etc' => 'style="text-align:right;"')); ?></div> 
                            <div>%</div>
                            <div class="consume"><?php echo $obj->inputNumber('taxValue', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?></div>
                          </div> 
                    </div> 
                 </div>  

                     <div class="div-table-row  form-group   form-detail-field"> 
                        <div class="div-table-col-3"  style="text-align:right; padding-top:2em;">
                             <?php echo ucwords($obj->lang['shippingFee']); ?>
                        </div>  
                        <div class="div-table-col-3" style=" padding-top:2em;" > 
                                <?php echo $obj->inputNumber('shipmentFee', array('etc' => 'style="text-align:right;" ')); ?>
                        </div>
                        <div class="div-table-col" > </div>
                    </div>

                     <div class="div-table-row  form-group   form-detail-field"> 
                        <div class="div-table-col-3" style="text-align:right;"> 
                            <?php echo ucwords($obj->lang['others']); ?>
                        </div>      
                        <div class="div-table-col-3"> 
                            <?php echo $obj->inputNumber('etcCost', array('etc' => 'style="text-align:right;"')); ?> 
                          </div>
                        <div class="div-table-col" > </div>
                    </div>
                   <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;"> 
                            <?php echo ucwords($obj->lang['total']); ?> 
                        </div>  
                        <div class="div-table-col-3"> 
                             <?php echo $obj->inputNumber('total', array('readonly' =>true, 'etc' => 'style="text-align:right;"')); ?>  
                        </div>
                        <div class="div-table-col"> </div>
                    </div> 
                     <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;"> </div>  
                        <div class="div-table-col-3"> 
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
