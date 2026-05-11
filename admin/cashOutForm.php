<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CashOut.class.php');
$cashOut= createObjAndAddToCol( new CashOut()); 
$chartOfAccount= createObjAndAddToCol( new ChartOfAccount()); 
$cashBank= createObjAndAddToCol( new CashBank()); 
$warehouse = createObjAndAddToCol( new Warehouse());
$currency = createObjAndAddToCol( new Currency());
$tax = createObjAndAddToCol(new Tax());
$supplier = createObjAndAddToCol( new Supplier());

$obj= $cashOut;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'cashOutList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$rsDetail = array();

$activeCurrency = CURRENCY['idr'];
$rsCurrency = $currency->searchDataRow(array($currency->tableName.'.pkey',$currency->tableName.'.name'), ' and ' . $currency->tableName . '.statuskey = 1');
$rsCurrency = array_column($rsCurrency,null,'pkey');


$_POST['trDate'] = date('d / m / Y');
$_POST['hidCurrencyKey'] = $activeCurrency;
$_POST['currencyName'] = $rsCurrency[CURRENCY['idr']]['name'];

$rs = prepareOnLoadData($obj);

$editWarehouseInactiveCriteria = '';

$useTax = ($class->loadSetting('taxOnCashBank') == 1) ? true : false;
$useStorage = $obj->useStorage;

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	 
	$_POST['refcode'] = $obj->getRefCode($id, $rs[0]['reftable']); 
	
	$rsCOAHeader = $chartOfAccount->getDataRowById($rs[0]['coakey']);
	$_POST['COAHeaderName'] = $rsCOAHeader[0]['code'].' - '.$rsCOAHeader[0]['name'] ;
	$_POST['hidCOAHeaderKey'] = $rsCOAHeader[0]['pkey'] ;
       
    if(!empty($rs[0]['supplierkey'])) {
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        $_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'];
        $_POST['supplierName'] = $rsSupplier[0]['name'];
    }

	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
    
    
    $_POST['currencyName'] = $rsCurrency[$rs[0]['currencykey']]['name'];
    
    if(ADV_FINANCE)
        $_POST['cashBankRefCode'] = $cashBank->getCashBankRef($id,$obj->tableName)['code'];

	
    //update file 
    if($useStorage){ 
        $rsFileDetail = $obj->getFileDetail($id);
    }else{ 
        $rsFile = array();  
        if( !empty($rs[0]['file'])){
            $rsFile[0]['file'] =  $rs[0]['file'];

            $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
            $destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath); 
        }

    }
    
	
} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 

$arrPPh = $tax->generateComboboxOpt(null, array('criteria' => ' and ( ' . $tax->tableName . '.typekey=' . $obj->oDbCon->paramString(TAX_TYPE['PPH']) . ' and ' . $tax->tableName . '.statuskey = 1)', 'order' => 'order by ' . $tax->tableName . '.orderlist asc, ' . $tax->tableName . '.name asc'));
$arrPPn = $tax->generateComboboxOpt(array('value' => 'name'),array( 'criteria' => ' and ( '.$tax->tableName.'.typekey='.$obj->oDbCon->paramString(TAX_TYPE['PPN']).' and '.$tax->tableName.'.statuskey = 1)', 'order' => 'order by ' . $tax->tableName . '.orderlist asc, ' . $tax->tableName . '.name asc')); 

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
            CURRENCY : <?php echo json_encode(CURRENCY['idr']); ?>,
			USE_STORAGE : <?php echo ($useStorage) ? "true" : "false"; ?>,
			uploadFileFolder :  "<?php echo $obj->uploadFileFolder; ?>",
            rsFile : <?php echo json_encode($rsFile); ?>
         };
		
        var opt = new Array();
		opt.arrCurrency =  <?php echo json_encode($rsCurrency); ?>; 
    
        var useMasterCost = <?php echo ($obj->useMasterCost) ? 'true' : 'false'; ?>;
        var cashOut = new CashOut(tabID,useMasterCost,varConstant,opt);
		  
        prepareHandler(cashOut);  
        
         var fieldValidation =  {
                                    code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 		
                                   COAHeaderName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.coa[1]
                                            }, 
                                        }
                                    },
                                } ; 
 
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
   

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
                                <div class="flex">  
                                    <div class="consume"><?php echo $obj->inputAutoCode('code'); ?></div>
                                    <?php  if(ADV_FINANCE) { ?> <div class="consume"><?php echo $obj->inputText('cashBankRefCode', array('readonly' => true)); ?></div> <?php } ?>
                                </div>
                            </div> 
                        </div>   
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputText('refcode', array('readonly' => true)); ?> 
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankRef']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputText('bankRefCode', array('readonly' => true)); ?> 
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('etc' => $attrHeader) ); ?>  
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
                                                ) 
                                            )
                                        );  
                                    ?>
                            </div> 
                        </div>  
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['paidTo']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputText('recipientName'); ?>
                            </div> 
                        </div>   
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['account']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php 
                                            $popupOpt =  (!$isQuickAdd) ? array(
                                                'url' => 'chartOfAccountForm.php',
                                                'element' => array('value' => 'COAHeaderName',
                                                       'key' => 'hidCOAHeaderKey'),
                                                'width' => '600px',
                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['chartOfAccount'])
                                            )  : ''; 
 
                                            echo  $obj->inputAutoComplete( array(
                                                                    'objRefer' => $chartOfAccount,
                                                                    'revalidateField' => true, 
                                                                    'element' => array('value' => 'COAHeaderName',
                                                                                       'key' => 'hidCOAHeaderKey'),
                                                                    'source' =>array(
                                                                                        'url' => 'ajax-coa.php',
                                                                                        'data' => array(  'action' =>'searchData', 'iscashbank' => '1' )
                                                                                    ) ,
                                                                    'popupForm' => $popupOpt,
                                                                    'callbackFunction' => 'getTabObj().updateCurrency()'
                                                        ));
                                    ?>
                            </div> 
                        </div>
                        
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?> /
                                <?php echo ucwords($obj->lang['currencyRate']); ?>
                            </label>
                            <div class="col-xs-9  mnv-currency">
                                <div class="flex">
                                    <div style="width:70px">
                                        <?php echo $obj->inputText('currencyName', array('readonly' => true)); ?>
                                        <?php echo $obj->inputHidden('hidCurrencyKey'); ?>
                                    </div>
                                    <div class="consume">
                                        <?php echo $obj->inputAutoDecimal('currencyRate', array('allowedStatusForEdit' => array (1))); ?>
                                    </div>
                                </div>
                            </div>
                        </div>   
					     
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
                                    </div>
                                </div>
                            </div>
                        <?php }?> 
                    
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
       
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['cost']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:300px;"><?php echo ucwords($obj->lang['note']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div>
                    <?php if ($useTax) { ?>
                    <div class="div-table-col detail-col-header" style="width:70px;text-align:right;">PPN %</div> 
<!--                    <div class="div-table-col detail-col-header" style="width:40px;text-align:center;">Inc</div>  -->
                    <div class="div-table-col detail-col-header" style="width:100px;"><?php echo ucwords($obj->lang['PPhType']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:80px;text-align:right;"><?php echo ucwords($obj->lang['PPhValue']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:120px;text-align:right;"><?php echo ucwords($obj->lang['total']); ?></div>   
                    <?php } ?>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>" style="width:45px"></div>   
                </div>
                
				<?php 
                            
                    $totalRows = count($rsDetail);
                    for ($i=0;$i<=$totalRows; $i++){  
                                
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        $readOnly = false;
                        $readOnlyPPh = false;
                        $readOnlyDetail = false;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        } else {   
                            
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                            $_POST['hidCostKey[]'] =  $rsDetail[$i]['costkey'];
                            $_POST['CCOName[]'] =  $rsDetail[$i]['costname']; 
                            $_POST['hidCOAKey[]'] =  $rsDetail[$i]['coakey']; 
                            $_POST['COAName[]'] =  $rsDetail[$i]['coaname']; 
                            $_POST['amount[]'] =   $obj->formatNumber($rsDetail[$i]['amount']);  
                            $_POST['trdesc[]'] =  $rsDetail[$i]['trdesc'];  
                            
                            $_POST['detailTaxPercentage[]'] = $rsDetail[$i]['taxpercentage'];
                            $_POST['chkDetailIncludeTax[]'] = $rsDetail[$i]['ispriceincludetax'];
                            $_POST['amount[]'] = $obj->formatNumber($rsDetail[$i]['amount'], -2);
                            $_POST['PPhValue[]'] = $obj->formatNumber($rsDetail[$i]['pphvalue'], -2);
                            $_POST['detailTotal[]'] = $obj->formatNumber($rsDetail[$i]['total'], -2); 
                            $_POST['selPPhType[]'] = $rsDetail[$i]['pphtype'];
                            
                        }
                    ?>
    
                 <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail">
                        <?php 
                        if($obj->useMasterCost) {
                            echo $obj->inputText('CCOName[]',array('overwritePost' => $overwrite, 'etc' => $etc));  
                            echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite, 'etc' => $etc));          
                        }else{
                            echo $obj->inputText('COAName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); 
                            echo $obj->inputHidden('hidCOAKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); 
                        } 
                        ?> 
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('trdesc[]',array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right" ' .$etc)); ?></div> 

                    <?php if ($useTax) { ?>
                    <div class="div-table-col detail-col-detail"  style="width:70px;">
                        <?php echo $obj->inputSelect('detailTaxPercentage[]', $arrPPn, array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right"', 'readonly' => $readOnly)); ?>
                    </div>
<!--                    <div class="div-table-col detail-col-detail"  style="width:40px; text-align:center"><?php echo $obj->inputCheckBox('chkDetailIncludeTax[]', array('overwritePost' => $overwrite, 'readonly' => $readOnly || $readOnlyPPh, 'etc' => 'style="text-align:right;" ' . $etc)); ?> </div>  -->
                    <div class="div-table-col detail-col-detail"  style="width:100px;"><?php echo $obj->inputSelect('selPPhType[]', $arrPPh, array('overwritePost' => $overwrite, 'readonly' => $readOnly)); ?></div>
                    <div class="div-table-col detail-col-detail" style="width:80px;">
                        <?php echo $obj->inputNumber('PPhValue[]', array('overwritePost' => $overwrite, 'readonly' => $readOnly || $readOnlyPPh, 'class' => 'form-control inputautodecimal ', 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                    </div>
                               
                    <div class="div-table-col detail-col-detail" style="width:120px;">
                        <?php echo $obj->inputNumber('detailTotal[]', array('overwritePost' => $overwrite, 'class' => 'form-control inputautodecimal ', 'readonly' => true, 'etc' => 'style="text-align:right;" ' . $etc)); ?>
                    </div>
                     <?php } ?>
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); ?></div>
                </div>
                         
                <?php  } ?>   
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
      
   
        <div>   
            <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:45px; height: 1em"></div>  
         
            <div class="div-table" style="float:right;">
                   <?php if ($useTax) { ?>
                 <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['dpp']); ?>
                    </div>  
                    <div class="div-table-col-3" style="width:120px"> 
                        <?php echo $obj->inputNumber('totalCost', array ('readonly' => true, 'class' => 'form-control inputautodecimal ', 'etc' => 'style="text-align:right;"')) ;?>    
                    </div>  
                </div> 
                
                <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['PPN']); ?>
                    </div>  
                    <div class="div-table-col-3" style="width:120px"> 
                        <?php echo $obj->inputNumber('totalPPN', array ('readonly' => true, 'class' => 'form-control inputautodecimal ', 'etc' => 'style="text-align:right;"')) ;?>    
                    </div>  
                </div> 
                <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['PPhValue']); ?>
                    </div>  
                    <div class="div-table-col-3" style="width:120px"> 
                        <?php echo $obj->inputNumber('totalPPh', array ('readonly' => true,'class' => 'form-control inputautodecimal ', 'etc' => 'style="text-align:right;"')) ;?>    
                    </div>  
                </div>  
             <?php } ?>
                <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['total'] . ' ' . $obj->lang['cashOut']); ?>
                    </div>  
                    <div class="div-table-col-3" style="width:120px"> 
                        <?php echo $obj->inputNumber('total', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>    
                    </div>  
                </div>
               
            </div>   
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
