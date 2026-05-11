<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CashIn.class.php');
$cashIn= createObjAndAddToCol( new CashIn()); 
$chartOfAccount= createObjAndAddToCol( new ChartOfAccount()); 
$cashBank= createObjAndAddToCol( new CashBank()); 
$warehouse = createObjAndAddToCol( new Warehouse());
$currency = createObjAndAddToCol( new Currency());

$obj= $cashIn;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'cashInList';

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

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	 
	$rsCOAHeader = $chartOfAccount->getDataRowById($rs[0]['coakey']);
	$_POST['COAHeaderName'] = $rsCOAHeader[0]['code'].' - '.$rsCOAHeader[0]['name'] ;
 
    $_POST['currencyName'] = $rsCurrency[$rs[0]['currencykey']]['name'];
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
   
    if(ADV_FINANCE)
        $_POST['cashBankRefCode'] = $cashBank->getCashBankRef($id,$obj->tableName)['code'];
} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' => ' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 

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
            TABLEKEY : <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>,
            CURRENCY : <?php echo json_encode(CURRENCY['idr']); ?>,
            RECIPIENTYPE : <?php echo json_encode(RECIPIENT_TYPE); ?>,
        };
        
        var opt = new Array();
		opt.arrCurrency =  <?php echo json_encode($rsCurrency); ?>; 
        var useMasterRevenue = <?php echo ($obj->useMasterRevenue) ? 'true' : 'false'; ?>; 
       
        var cashIn = new CashIn(tabID,useMasterRevenue,varConstant,opt); 
         
    
        prepareHandler(cashIn);  
        
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
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['from']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputText('recipientName'); ?>
                            </div> 
                        </div>   
                       <!-- <div class="form-group iscustomer">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['from']); ?></label> 
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
                                                                    'popupForm' => array(
                                                                                        'url' => 'customerForm.php',
                                                                                        'element' => array('value' => 'customerName',
                                                                                               'key' => 'hidCustomerKey'),
                                                                                        'width' => '1000px',
                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
                                                                                    )
                                                                  )
                                                            );  
                                ?> 
                            </div> 
                        </div>   -->
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
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['revenue']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:300px;"><?php echo ucwords($obj->lang['note']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div> 
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>" style="width:45px"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsDetail);
                    for ($i=0;$i<=$totalRows; $i++){  
                        
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        } else {  
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                            $_POST['hidRevenueKey[]'] =  $rsDetail[$i]['revenuekey'];
                            $_POST['hidCOAKey[]'] =  $rsDetail[$i]['coakey'];
                            $_POST['revenueName[]'] =  $rsDetail[$i]['revenuename']; 
                            $_POST['COAName[]'] =  $rsDetail[$i]['coacodename']; 
                            $_POST['amount[]'] =   $obj->formatNumber($rsDetail[$i]['amount']);  
                            $_POST['trdesc[]'] =  $rsDetail[$i]['trdesc'];
                        }
                ?>
                    
                    <div class="div-table-row <?php echo $class; ?>"> 
                        <div class="div-table-col detail-col-detail">
                              <?php 
                                if($obj->useMasterRevenue) {
                                    echo $obj->inputText('revenueName[]',array('overwritePost' => $overwrite, 'etc' => $etc));  
                                    echo $obj->inputHidden('hidRevenueKey[]',array('overwritePost' => $overwrite, 'etc' => $etc));          
                                }else{
                                    echo $obj->inputText('COAName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); 
                                    echo $obj->inputHidden('hidCOAKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); 
                                } 
                                ?> 
                            <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                        </div> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('trdesc[]',array('overwritePost' => $overwrite, 'etc' =>$etc)); ?></div> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right" ' .$etc)); ?></div> 
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); ?></div>
                     </div>
                <?php } ?>  
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
      
        <div>   
            <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:45px; height: 1em"></div>  
            <div class="div-table" style="float:right;">
               <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="text-align:right;"> 
                        <?php echo ucwords($obj->lang['total']); ?> 
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
