<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $vendorWarrantyClaim;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'vendorWarrantyClaimList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$editWarehouseInactiveCriteria = '';
$editVendorWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$internalHide = '';
$externalHide = 'style="display:none"';

$showVendorPartNumber = $item->loadSetting('showVendorPartNumber');
$_POST['chkIsFullDelivered'] = 1;
$activeCurrencyKey = CURRENCY['idr'];
 
$rsDetail = array();  
$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	  
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selFromWarehouseKey'] =$rs[0]['fromwarehousekey']; 
	$_POST['selToWarehouseKey'] =$rs[0]['towarehousekey']; 
	$_POST['trDesc'] = $rs[0]['trdesc'];     
    $_POST['refCode'] = $rs[0]['refcode'];
    $_POST['hidSupplierKey'] = $rs[0]['supplierkey'];
    $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
    $_POST['supplierName'] = $rsSupplier[0]['name'];
    $_POST['selCurrency'] = $rs[0]['currencykey'];
    $_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'],-2) ; 
    $_POST['grandtotal'] = $obj->formatNumber($rs[0]['grandtotal'],-2);
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['fromwarehousekey']);   
    $editVendorWarehouseInactiveCriteria =  ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['towarehousekey']); 
    $editCurrencyInactiveCriteria = ' or '.$currency->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);

}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and (isrma = 1 and '.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrVendorWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and (isvendor = 1 and '.$warehouse->tableName.'.statuskey = 1' .$editVendorWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrDefaultUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 
$arrCurrency = $obj->convertForCombobox($currency->searchData ('','',true,' and ('.$currency->tableName.'.statuskey = 1' . $editCurrencyInactiveCriteria.')'),'pkey','name');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style> 
    .total-sn-label {font-size: 0.9em; color:#999; font-style: italic}
    .tag-list li {height: 2em; text-align: center; }
    .transaction-detail>.div-table-row:nth-child(2n+3) .tag-list li {background-color: #dedede !important}
    .options-row .form-panel-result {max-height: 10em; overflow: auto}
</style>
<title></title> 
 
<script type="text/javascript">  
 jQuery(document).ready(function(){  
	 	 var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
         var vendorWarrantyClaim = new VendorWarrantyClaim(tabID);
        
         prepareHandler(vendorWarrantyClaim);   
        
         var fieldValidation =  {
                                    code: {
                                            validators: {
                                            notEmpty: {  message: phpErrorMsg.code[1] }, 
                                        }
                                     },
                                     supplierName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.supplier[1]
                                            }, 
                                        }
                                    },
                                    refCode: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.RMA[1]
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
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>"> 
            <?php prepareOnLoadDataForm($obj); ?>   
      
          <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col" > 
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
                                <?php echo $obj->inputDate('trDate',array('allowedStatusForEdit' => array(1))); ?>  
                            </div> 
                        </div>   
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sourceWarehouse']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputSelect('selFromWarehouseKey', $arrWarehouse,array('allowedStatusForEdit' => array(1))); ?>  
                            </div> 
                        </div> 
                          
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['destinationWarehouse']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputSelect('selToWarehouseKey', $arrVendorWarehouse,array('allowedStatusForEdit' => array(1) )); ?>  
                            </div> 
                        </div>    
                          
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['RMANumber']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputText('refCode',array('allowedStatusForEdit' => array(1))); ?>
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
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $supplier,
                                                                                    'revalidateField' => true, 
                                                                                    'element' => array('value' => 'supplierName',
                                                                                                       'key' => 'hidSupplierKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-supplier.php',
                                                                                                        'data' => array(  'action' =>'searchData')
                                                                                                    ),
                                                                                    'allowedStatusForEdit' => array(1)
                                                                              )
                                                                        );  
                                            ?> 
                            </div> 
                        </div>  

                        
                        </div>
         			</div>
                    <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group"> 
                                <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"','allowedStatusForEdit' => array(1,2))); ?>
                                </div> 
                            </div>   
                        </div>
                    </div>
                </div>
                    
         </div>  
                        
                        
        <div class="div-table transaction-detail" style="width:100%;"> 
             <div class="div-table-row"> 
                <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                 <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['outstanding']); ?></div>
                <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> <span class="text-muted">(<span class="mnv-active-currency"></span>)</span></div>
                <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?> <span class="text-muted">(<span class="mnv-active-currency"></span>)</span></div>
                <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>" style="width:35px;"></div>
                <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
            </div>  
        </div> 
        <div class="div-table mnv-transaction  transaction-detail" style="width:100%; border-bottom:1px solid #333; ">       
				<?php 
                    $totalRows = count($rsDetail);
            
                    for ($i=0;$i<=$totalRows; $i++){  
                        
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $readonly = false;
                        $readonlyprice = false;
                        $etc = '';
                        $txtSN = ''; 
                        $arrUnit = $arrDefaultUnit;
                        $showOptions = false;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"';
                        } else{ 
                            $rsSN = $obj->getSerialNumber($rsDetail[$i]['pkey']);
                            $arrSN = array_column($rsSN, 'serialnumber');
                               
                            if (!empty($rsSN)){ 
                                $txtSN = "<ul  class=\"tag-list\">"; 
                                for($j=0;$j<count($rsSN);$j++) 
                                    $txtSN .= '<li>'.$rsSN[$j]['serialnumber'].'</li>';
                           
                                $txtSN .= "</ul>";
                            }
                            
                             
                            //$_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey']; 
                            $_POST['selUnit[]'] =  $rsDetail[$i]['unitkey']; 
                            $_POST['itemName[]'] =  $rsDetail[$i]['itemname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']); 
                            $_POST['qtyOutstanding[]'] =   $obj->formatNumber($rsDetail[$i]['qty'] - $rsDetail[$i]['receivedqtyinbaseunit']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsDetail[$i]['priceinunit'],-2); 
                            $_POST['subtotal[]'] =   $obj->formatNumber($rsDetail[$i]['total'],-2); 
                            //$_POST['COGS[]'] =   $obj->formatNumber($rsDetail[$i]['costinbaseunit']); 
                            $_POST['snList[]'] =  implode(chr(13),$arrSN);
                            $_POST['hidNeedSN[]'] = $rsDetail[$i]['needsn'];
                            $_POST['hidVendorPartNumberKey[]'] = $rsDetail[$i]['vendorpartnumberkey']; 
                            $_POST['vendorPartNumber[]'] = $rsDetail[$i]['partnumber'];
                            $_POST['hidTempItemKey[]'] =  $rsDetail[$i]['itemkey'];
                                             
                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsDetail[$i]['itemkey']),'conversionunitkey','unitname'); 
        
				            $showOptions = (USE_SN && $rsDetail[$i]['needsn'] == 1 &&  (isset($rs) && $rs[0]['isfulldelivered'] == 1) ) ? true : false;
                            
//                            $readonly = (isset($rs) && $rs[0]['statuskey'] == 1) ? true : false;
                            if(isset($rs) && $rs[0]['statuskey'] != 1)
                               $readonly = true;
                            
                            if(isset($rs) && $rs[0]['statuskey'] == 3)
                               $readonlyprice = true;
       
                        }

                         
                 ?>
            
                <div class="div-table-row odd-style-adjustment <?php echo $class; ?> "> 
                        <div class="div-table-col" style="padding:0">
                            <div class="div-table" style="width:100%">
                                <div class="div-table-row">
                                    <div class="div-table-col" style="padding:0">
                                        <div class="div-table" style="width: 100%">
                                         <div class="div-table-row"> 
                                            <div class="div-table-col detail-col-detail" style="vertical-align:top;"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidNeedSN[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                                            <div class="div-table-col detail-col-detail" style="vertical-align:top; width:80px; "><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc, 'readonly' => $readonly)); ?></div>
                                             <div class="div-table-col detail-col-detail" style="vertical-align:top; width:80px; "><?php echo $obj->inputNumber('qtyOutstanding[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ' .$etc, 'readonly' => true)); ?></div>
                                            <div class="div-table-col detail-col-detail" style="vertical-align:top; width:80px;"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite, 'etc' => $etc, 'readonly' => $readonly)); ?></div>
                                            <div class="div-table-col detail-col-detail" style="vertical-align:top; width:100px; "><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'class'=>'form-control inputnumber mnv-input-number', 'etc' => 'style="text-align:right;" ' .$etc, 'readonly' => $readonlyprice)); ?></div>
                                            <div class="div-table-col detail-col-detail" style="vertical-align:top; width:120px; "><?php echo $obj->inputNumber('subtotal[]', array('overwritePost' => $overwrite, 'class'=>'form-control inputnumber mnv-input-number', 'etc' => 'style="text-align:right;" ' .$etc , 'readonly' => true)); ?></div>
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"  style="width:35px;"><?php echo $obj->inputLinkButton('btnMoreOptions' , '<i class="fas fa-ellipsis-h"></i>', array('class' => 'btn btn-link btn-more-options','disabled' => true)); ?></div>
                                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"','class' => 'btn btn-link remove-button')); ?></div>
                                </div> 
                            </div> 
                             <div class="options-row" style="<?php if (!$showOptions) { echo 'display:none;'; } ?>">
                                <div style="<?php if (!$showOptions) { echo 'display:none;'; } ?>" class="total-sn-label need-sn">Selisih SN : <span class="total-sn-remaining">0</span></div>
                                <div class="panel form-panel-result" <?php if(!empty($txtSN)) echo 'style="display:block"'; ?> >
                                    <?php echo $txtSN; ?>
                                    <div style="clear:both"></div>
                                </div>
                                <div class="panel form-panel" style="display:none; width: 100%">
                                    <div style="font-weight:bold"><?php echo $obj->lang['serialNumber']; ?></div>
                                    <?php echo  $obj->inputTextArea('snList[]', array('overwritePost' => $overwrite, 'etc' => 'attr-label="mnv-opt-sn"  style="height:10em;"')); ?>  
                                </div>
                               <div class="panel summary-panel" style="width:200px; float:left"></div>
                               <div style="clear:both"></div>  
                             </div>   
                        </div> 
                    </div> 
                         
                <?php  } ?> 
                   
         </div>              
          
          <div style="clear:both; height:1em;"></div>  
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
      
        <div>   
           <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:65px; height: 1em"></div>
            <div class="div-table" style="float:right;">
               <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['total']); ?>
                    </div>  
                    <div class="div-table-col-3" style="width:120px"> 
                         <?php echo $obj->inputNumber('grandtotal', array ('readonly' => true, 'class'=>'form-control inputnumber mnv-input-number', 'etc' => 'style="text-align:right;"')) ;?>    
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
