<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CashBankIn.class.php');
$cashBankIn = createObjAndAddToCol( new CashBankIn()); 
$revenueCashIn = createObjAndAddToCol( new RevenueCashIn()); 
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$cashBank = createObjAndAddToCol( new CashBank()); 
$customer = createObjAndAddToCol( new Customer());
$currency = createObjAndAddToCol(new Currency());
$employee = createObjAndAddToCol(new Employee());
$supplier = createObjAndAddToCol(new Supplier());

$obj = $cashBankIn;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'cashBankInList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
$editCurrencyInactiveCriteria = '';

$activeCurrency = CURRENCY['idr'];
$rsCurrency = $currency->searchDataRow(array($currency->tableName.'.pkey',$currency->tableName.'.name'), ' and ' . $currency->tableName . '.statuskey = 1');
$rsCurrency = array_column($rsCurrency,null,'pkey');
  
$rsDetail = array();
    
$_POST['trDate'] = date('d / m / Y');
$_POST['hidCurrencyKey'] = $activeCurrency;
$_POST['currencyName'] = $rsCurrency[CURRENCY['idr']]['name'];

$rs = prepareOnLoadData($obj);  
$readOnlyCustomer = false;

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    
	$rsCashBank = $cashBank->getCashBankRef($id,$obj->tableName,$rs[0]['coakey']);
	$_POST['refCashBankCode'] = $rsCashBank['code'];

    if(!empty($rs[0]['customerkey'])){
    $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['hidCustomerKey'] = $rs[0]['customerkey'];
        $_POST['customerName'] = $rsCustomer[0]['name'];
    }
    
    if(!empty($rs[0]['employeekey'])){
        $rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
        $_POST['hidEmployeeKey'] = $rs[0]['employeekey'];
        $_POST['employeeName'] = $rsEmployee[0]['name'];
    }
        
    if(!empty($rs[0]['supplierkey'])){
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        $_POST['hidSupplierKey'] = $rs[0]['supplierkey'];
        $_POST['supplierName'] = $rsSupplier[0]['name'];
    }
 

    $_POST['chkUnknownSource'] = $rs[0]['isunknownresource'];
    $_POST['currencyName'] = $rsCurrency[$rs[0]['currencykey']]['name'];
    $_POST['selRecipientType'] = $rs[0]['recipienttypekey'];

    
	$rsCOAHeader = $chartOfAccount->getDataRowById($rs[0]['coakey']);
	$_POST['COAHeaderName'] = $rsCOAHeader[0]['code'].' - '.$rsCOAHeader[0]['name'] ;
	
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);   
    $editCurrencyInactiveCriteria = ' or  ' . $currency->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);
    
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrRevenueType= $obj->convertForCombobox($revenueCashIn->searchData('','',true,' and ('.$revenueCashIn->tableName.'.statuskey = 1)'),'pkey','name',$obj->lang['temporaryAccount']);  

$arrRecipientType = $obj->generateComboboxOpt(array('data' => $obj->getRecipientType())); 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  ; 

         var varConstant = {  
                tablekey  :  <?php echo $obj->getTableKeyAndObj($obj->tableName,array('key'))['key'];  ?>,
                CURRENCY : <?php echo json_encode(CURRENCY['idr']); ?>,
                RECIPIENTYPE : <?php echo json_encode(RECIPIENT_TYPE); ?>,
            };
        
        var opt = new Array();
		opt.arrCurrency =  <?php echo json_encode($rsCurrency); ?>; 
    
       
         var cashBankIn = new CashBankIn(tabID, varConstant,opt);
    
         prepareHandler(cashBankIn);

        
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
            
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
        
  	  
    }); 

</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?> / <?php echo ucwords($obj->lang['voucherNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
												<div class="consume"><?php echo $obj->inputAutoCode('code'); ?></div>
												<div>/</div>
												<div class="consume"><?php echo $obj->inputText('refCashBankCode',array('readonly' => 'true')); ?></div>
											</div>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('allowedStatusForEdit' => array (1))); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate', array('allowedStatusForEdit' => array (1))); ?>  
                                        </div> 
                                    </div>    
								 
									  <div class="form-group">
											<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['unknownSource']); ?></label> 
											<div class="col-xs-9"> 
											  <?php echo $obj->inputCheckBox('chkUnknownSource'); ?> 
											</div> 
									   </div>  
								  <div class="known-source">
                                    <div class="form-group  ">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recipientType']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo  $obj->inputSelect('selRecipientType', $arrRecipientType, array('allowedStatusForEdit' => array (1))); ?>
                                        </div>
                                    </div> 
                                    <div class="form-group type-1">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' , 'statuskey' => '2' )
                                                                                                ) ,


                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div> 
         			    
                                    <div class="form-group type-2">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $supplier,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'supplierName',
                                                                                                   'key' => 'hidSupplierKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-supplier.php',
                                                                                                    'data' => array(  'action' =>'searchData')
                                                                                                ) ,


                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div> 
								 
                                 <div class="form-group type-3">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['employee']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $employee,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'employeeName',
                                                                                                   'key' => 'hidEmployeeKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-employee.php',
                                                                                                    'data' => array(  'action' =>'searchData' , 'statuskey' => '2' )
                                                                                                ) ,


                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                 </div> 
								</div>
								   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['attention']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('attnName'); ?>  
                                        </div> 
                                    </div>  
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cash/bank']); ?></label> 
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
                                                                            'allowedStatusForEdit' => array (1),
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

                    </div>     
              <div class="div-table-col">
                     <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                        <div class="col-xs-12"> 
                                            <?php echo  $obj->inputTextArea('note', array('etc' => 'style="height:10em;"','allowedStatusForEdit' => array (1))); ?>                                         
                                        </div> 
                            </div>
                     
                     </div>
                </div>
            </div>
      </div> 
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
  <div class="div-table-row">  
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['description']); ?></div>  
                    <div class="div-table-col detail-col-header" style="width:120px;text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:150px;"><?php echo ucwords($obj->lang['transactionType']); ?></div> 
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>" style="width:45px;"></div>
                </div>
      
            
				<?php  
                    $totalRows = count($rsDetail);      
		            $deleteBtn = $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); 
              
            
                    for ($i=0;$i<=$totalRows; $i++){  
					 
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $readOnly = false;
                        $readOnlyDetail = false;
                        $etc = '';  
                        
						if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        } else {  
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['hidCustomerKey[]'] =  $rsDetail[$i]['customerkey']; 
//                            $_POST['customerName[]'] =  $rsDetail[$i]['customername']; 
                            $_POST['trdescDetail[]'] =  $rsDetail[$i]['trdesc'];
                            $_POST['hidRevenueKey[]'] =  $rsDetail[$i]['revenuekey'];
                            $_POST['amount[]'] =  $obj->formatNumber($rsDetail[$i]['amount']); 
                            $rsCashBank = $cashBank->getCashBankRef($id,$obj->tableName,$rs[0]['coakey']);
//                            $_POST['cashBankRefCode[]'] = $rsCashBank['code'];
//                        $obj->setLog($rsCashBank,true);
                   
                        }
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>"> 
            
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('trdescDetail[]',array('overwritePost' => $overwrite, 'readonly'=>$readOnlyDetail,'etc' =>$etc)); ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                    
                    </div>  
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite, 'readonly' => $readOnly,'class' => 'form-control inputautodecimal ',  'etc' => 'style="text-align:right;" ' .$etc)); ?>
                    </div>  
                      <div class="div-table-col detail-col-detail" >
                          <?php echo $obj->inputSelect('hidRevenueKey[]',$arrRevenueType,array('overwritePost' => $overwrite,'readonly' => $readOnly)); ?>
                    </div>
                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style=" width:45px;"><?php echo $deleteBtn ?></div>
                </div> 
 
            <?php } ?> 
                   
         </div>        
        
          <div style="clear:both; height:1em;"></div> 

          <?php if ($rs[0]['statuskey']==1 || empty($rs)) { ?> 
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' => 'btn btn-primary btn-second-tone')); ?></div>
          <?php } ?>   
 
        <div>   
            <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:45px; height: 1em"></div>  
            <div class="div-table" style="float:right;">
               <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['total']); ?>
                    </div>  
                    <div class="div-table-col-3" style="width:120px"> 
                         <?php echo $obj->inputNumber('total', array ('readonly' => true,'class' => 'form-control inputautodecimal ', 'etc' => 'style="text-align:right;"')) ;?>    
                    </div> 
                   <div class="div-table-col-3" style="width:150px"> 
                   </div>
                </div> 
            </div>   
        </div>       
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
