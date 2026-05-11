<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CashBank.class.php');
$cashBank = createObjAndAddToCol( new CashBank());  

$chartOfAccount = createObjAndAddToCol( new ChartOfAccount()); 
$warehouse = createObjAndAddToCol( new Warehouse());
$currency = createObjAndAddToCol( new Currency());
    
$obj= $cashBank;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'cashBankList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$rsDetail = array();
$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$_POST['trDate'] = date('d / m / Y');
$_POST['reconsileDate'] = date('00 / 00 / 0000');
$dateReturnOnEmpty = array('returnOnEmpty'=>true, 'value' => '00 / 00 / 0000');

$rs = prepareOnLoadData($obj);

$recipientLabel = $obj->lang['recipient'];
$rsCashBankTransaction = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
	
    $rsCashBankTransaction = $obj->getCashBankTransaction($rs[0]['pkey']);
    $recipientLabel = (($rs[0]['amount'] > 0) ) ? $obj->lang['sender'] :  $obj->lang['recipient'];
     
    $recipientName = '';
    if (!empty($rs[0]['customerkey'])){ 
        $customer = new Customer(); 
        $rsRecipient = $customer->getDataRowById($rs[0]['customerkey']);
        $recipientName = $rsRecipient[0]['name'];
    }else if (!empty($rs[0]['supplierkey'])){ 
        $supplier =  new Supplier(); 
        $rsRecipient = $supplier->getDataRowById($rs[0]['supplierkey']);
        $recipientName = $rsRecipient[0]['name'];
    }else if (!empty($rs[0]['employeekey'])){ 
        $employee = new Employee(); 
        $rsRecipient = $employee->getDataRowById($rs[0]['employeekey']);
        $recipientName = $rsRecipient[0]['name'];
    }
    
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
$_POST['reconsileDate'] = $obj->formatDBDate($rs[0]['reconsiledate'],'d / m / Y',$dateReturnOnEmpty); 
	 
	$_POST['trDesc'] = $rs[0]['trdesc'];
	$rsCOAHeader = $chartOfAccount->getDataRowById($rs[0]['coakey']);
	$_POST['COAHeaderName'] = $rsCOAHeader[0]['code'].' - '.$rsCOAHeader[0]['name'] ;
	$_POST['hidCOAHeaderKey'] = $rs[0]['coakey'] ;
	$_POST['recipientName'] =  $recipientName ;
	$_POST['selTransactionTypeKey'] = $rs[0]['transactiontypekey'] ;
	$_POST['hidRefKey'] = $rs[0]['refkey'] ;
	$_POST['detailKey'] = $rs[0]['detailkey'] ;
	$_POST['refCode'] = $rs[0]['refcode'] ;
	$_POST['selCurrency'] = $rs[0]['currencykey'] ;
	$_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate']);
    
	$_POST['amount'] = $obj->formatNumber($rs[0]['amount'] * $rs[0]['credittype'],-2); 
	$_POST['outstanding'] = $obj->formatNumber($rs[0]['outstanding']* $rs[0]['credittype'],-2); 
    
    $editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
   $editCurrencyInactiveCriteria = ' or  ' . $currency->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);

} 
//$arrTransaction = CASH_TRANSACTION;
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrTransaction = $obj->convertForCombobox($obj->getTransactionType(),'pkey','name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrCurrency = $obj->convertForCombobox($currency->searchData('', '', true, ' and (' . $currency->tableName . '.statuskey = 1' . $editCurrencyInactiveCriteria . ')'), 'pkey', 'name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">
    
    jQuery(document).ready(function(){   
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
         var cashBank = new CashBank(tabID);
    
         prepareHandler(cashBank);   
        
         var fieldValidation =  {code: {
                                        validators: {
                                            notEmpty: {  message: phpErrorMsg.code[1] }, 
                                            },
                                        },
                                }; 
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
	});
	
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
         <?php prepareOnLoadDataForm($obj); ?>
         <?php echo $obj->inputHidden('hidRefKey'); ?>
         <?php echo $obj->inputHidden('detailKey'); ?>
    
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
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?> / <?php echo ucwords($obj->lang['reconsiliationDate']); ?></label> 
                            <div class="col-xs-9"> 
                                   <div class="flex">
                                        <div class="consume">
                                            <?php echo $obj->inputDate('trDate', array ('etc' => 'style="text-align:center;"')); ?> 
                                        </div>
                                        <div class="consume">
                                            <?php echo $obj->inputDate('reconsileDate', array ('readonly'=> true, 'etc' => 'style="text-align:center;"')); ?> 
                                        </div>
                                   </div>

                            </div> 
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>  
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
                                                                    'popupForm' => $popupOpt
                                                        ));
                                    ?>
                            </div> 
                        </div> 
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['transactionType']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputSelect('selTransactionTypeKey', $arrTransaction ); ?>  
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo  $recipientLabel; ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputText('recipientName', array('readonly' => true)); ?>
                            </div> 
                        </div> 
                        
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['refCode']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputText('refCode', array('readonly' => true)); ?>
                            </div> 
                        </div> 

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?></label> 
                            <div class="col-xs-9"> 
                                <div class="flex">
                                    <div>
                                        <?php echo $obj->inputSelect('selCurrency', $arrCurrency, array ('readonly' => true)); ?>
                                     </div>
                                    <div class="consume">
                                        <?php echo $obj->inputAutoDecimal('currencyRate', array('readonly' => true)); ?>
                                    </div>
                                </div>
                            </div> 
                        </div> 

                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputNumber('amount', array ('readonly' => true)) ;?> 
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['outstanding']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputNumber('outstanding', array ('readonly' => true)) ;?> 
                            </div> 
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
					
					
                    <div class="div-tab-panel"> 
                    <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['transactionInformation']); ?></div>
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row "> 
                                <div class="div-table-col-5 col-header" style="width:150px;" > 
                                    <?php echo ucwords($obj->lang['code']); ?>
                                </div>
                                <div class="div-table-col-5 col-header" style="text-align:center; width: 100px" > 
                                    <?php echo ucwords($obj->lang['date']); ?>
                                </div>
                                <div class="div-table-col-5 col-header" style="text-align:right;" > 
                                    <?php echo ucwords($obj->lang['amount']); ?> 
                                </div>
                            </div> 
                            <?php 
                                if (!empty($_GET['id'])){
                                    
                                    for ($i=0;$i<count($rsCashBankTransaction);$i++){
                                        echo '
                                        <div class="div-table-row"> 
                                            <div class="div-table-col-5 row-bb" >'. $rsCashBankTransaction[$i]['refcode'].'</div> 
                                            <div class="div-table-col-5 row-bb" style="text-align:center" >'.$obj->formatDBDate($rsCashBankTransaction[$i]['refdate']).'</div> 
                                            <div class="div-table-col-5 row-bb" style="text-align:right;" >'.$obj->formatNumber($rsCashBankTransaction[$i]['amount']).'</div>
                                        </div> 
                                        '; 
                                    }
                                }
                            ?>
                        </div> 
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
