<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass('ChartOfAccount.class.php');
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
$customCode = createObjAndAddToCol(new CustomCode());
$currency = createObjAndAddToCol(new Currency());

$obj= $chartOfAccount;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
    

$formAction = 'chartOfAccountList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$isHasCustomCode = $security->hasSecurityAccess( $obj->userkey ,$security->getSecurityKey($customCode->securityObject),10);

$editCategoryCriteria= '';
$editCurrencyInactiveCriteria = '';
$cashBankStyle = 'display:none;';
$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){  
	 
	$_POST['name'] = $rs[0]['name'];
	$_POST['selCategory'] = $rs[0]['parentkey'];   
	
	$arrChild  = $obj->getChildren($rs[0]['pkey']);
	array_push($arrChild, $rs[0]['pkey']);
	if (!empty($arrChild)) 
		  $editCategoryCriteria = ' and '.$obj->tableName.'.pkey not in ('.implode(",",$arrChild).')'; 
       
    $_POST['chkCashBank'] = $rs[0]['iscashbank'];
    $_POST['chkIsUseVoucher'] = $rs[0]['isusevoucher'];
    
    if ($rs[0]['iscashbank']){
        
        $cashBankStyle = '';
        $editCurrencyInactiveCriteria = ' or  ' . $currency->tableName . '.pkey = ' . $obj->oDbCon->paramString($rs[0]['currencykey']);

        $_POST['selCurrency'] = $rs[0]['currencykey'];
        
        $_POST['outCode'] = $rs[0]['outcode'];
        $_POST['outCounter'] = $obj->formatNumber($rs[0]['outcounter']);
        $_POST['inCode'] = $rs[0]['incode'];
        $_POST['inCounter'] = $obj->formatNumber($rs[0]['incounter']);
        $_POST['hidCounterCOAKey'] = $rs[0]['countercoakey']; 
        $_POST['digit'] = $obj->formatNumber($rs[0]['digit']);  
	    $_POST['selResetType'] =  $rs[0]['resettypekey'] ;  
        
        if (!empty($rs[0]['countercoakey'])){
            $rsCoa = $chartOfAccount->getDataRowById($rs[0]['countercoakey']);
            $_POST['counterCOACode'] = $rsCoa[0]['code'] . ' - ' . $rsCoa[0]['name'];
        }
    } 
  
} 
 
$arrCategory = $obj->searchData($obj->tableName.'.statuskey',1,true,$editCategoryCriteria ,' order by code asc');
$arrCategory = $obj->convertForCombobox($arrCategory,'pkey','coaname');  
$arrResetType = $obj->convertForCombobox($obj->getCustomCodeResetType(),'pkey','name'); 
$arrCurrency = $obj->convertForCombobox($currency->searchData('', '', true, ' and (' . $currency->tableName . '.statuskey = 1' . $editCurrencyInactiveCriteria . ')'), 'pkey', 'name');


$_POST['selDailyPeriod'] = date('d / m / Y'); 
$_POST['selMonthlyPeriod'] = date('d / m / Y');   
$_POST['selAnnuallyPeriod'] = date('d / m / Y'); 

//$_POST['selMonthlyPeriod'] = date('F Y');  // nanti baru dibenerin, karena kalo beda format, pas ganti tgl, blm berubah
//$_POST['selAnnuallyPeriod'] = date('F Y'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript"> 

    jQuery(document).ready(function(){   
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
         var chartOfAccount = new ChartOfAccount(tabID,<?php echo json_encode($rs); ?>);
    
         prepareHandler(chartOfAccount);   
        
         var fieldValidation =  {
                                      name: { 
                                            validators: {
                                                notEmpty: {
                                                    message: phpErrorMsg.coa[1]
                                                }, 
                                            }
                                        },  

                                        code: { 
                                            validators: {
                                                notEmpty: {
                                                    message: phpErrorMsg.code[1]
                                                }, 
                                            }
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
       <div class="div-table main-tab-table-1">
              <div class="div-table-row">
                  <div class="div-table-col">  
                        <div class="div-tab-panel">   
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php echo $obj->inputAutoCode('code'); ?>
                                    </div> 
                                 </div>  
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['parent']); ?></label> 
                                    <div class="col-xs-9"> 
                                       <?php echo $obj->inputSelect('selCategory',$arrCategory); ?>
                                    </div> 
                                 </div>  
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['accountName']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php echo $obj->inputText('name'); ?>
                                    </div> 
                                 </div> 
                                   <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cashBank']); ?> </label> 
                                    <div class="col-xs-9" > 
										<div class="flex" style="padding-top:7px">
											<div><?php echo $obj->inputCheckBox('chkCashBank'); ?> </div>
											<div style="margin-left:2em"><?php echo ucwords($obj->lang['useVoucher']); ?> </div>
											<div><?php echo $obj->inputCheckBox('chkIsUseVoucher', array('value' => 1)); ?> </div>
										</div> 
                                    </div> 
                                 </div> 
								 <?php if ($isHasCustomCode) { ?> 
                                 <div class="form-group cashbank" style="<?php echo $cashBankStyle; ?>">                                    
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?></label> 
                                    <div class="col-xs-9"> 
                                           <?php echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency')); ?>
                                    </div> 
                                </div>
                                 <div class="form-group cashbank" style="<?php echo $cashBankStyle; ?>">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['digit']); ?></label> 
                                    <div class="col-xs-9"> 
                                           <?php echo  $obj->inputNumber('digit'); ?>
                                    </div> 
                                </div>
                                <div class="form-group cashbank" style="<?php echo $cashBankStyle; ?>"> 
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['resetEvery']); ?></label> 
                                    <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selResetType', $arrResetType); ?>
                                    </div> 
                                </div>
                            
                                <div class="increment-number 2" style="display:none"> 
                                    <div class="form-group cashbank" style="<?php echo $cashBankStyle; ?>"> 
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php echo $obj->inputDate('selDailyPeriod'); ?>
                                        </div> 
                                    </div>
                                </div>
                                <div class="increment-number 3" style="display:none"> 
                                    <div class="form-group cashbank" style="<?php echo $cashBankStyle; ?>"> 
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php echo $obj->inputDate('selMonthlyPeriod'); ?>
                                        </div> 
                                    </div>
                                </div>
                                <div class="increment-number 4" style="display:none"> 
                                    <div class="form-group cashbank" style="<?php echo $cashBankStyle; ?>"> 
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php echo $obj->inputDate('selAnnuallyPeriod'); ?>
                                        </div> 
                                    </div>
                                </div>

                                 <!--<div class="form-group cashbank" style="<?php echo $cashBankStyle; ?>">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['runningNumber']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <div class="increment-number 1">      
                                            <div>  <?php echo  $obj->inputInteger('increment'); ?></div>  
                                        </div> 
                                        <div class="increment-number 2" style="display:none">
                                            <div> <?php echo  $obj->inputInteger('dailyIncrement'); ?></div>
                                        </div>
                                        <div class="increment-number 3" style="display:none">
                                            <div> <?php echo  $obj->inputInteger('monthlyIncrement'); ?></div>
                                        </div> 
                                        <div class="increment-number 4" style="display:none">
                                            <div> <?php echo  $obj->inputInteger('annuallyIncrement'); ?></div>
                                        </div> 
                                    </div>
                                </div>-->
                            
                                <div class="form-group cashbank" style="<?php echo $cashBankStyle; ?>">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['voucherCashInCode']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <div class="flex">
                                            <div class="consume"> <?php echo $obj->inputText('inCode'); ?> </div> 
                                            <div  style="padding-left:1em" > <?php echo ucwords($obj->lang['runningNumber']); ?> </div>
                                            <div> 
                                                <div class="increment-number 1">      
                                                    <div>  <?php echo  $obj->inputInteger('inIncrement'); ?></div>  
                                                </div> 
                                                <div class="increment-number 2" style="display:none">
                                                    <div> <?php echo  $obj->inputInteger('inDailyIncrement'); ?></div>
                                                </div>
                                                <div class="increment-number 3" style="display:none">
                                                    <div> <?php echo  $obj->inputInteger('inMonthlyIncrement'); ?></div>
                                                </div> 
                                                <div class="increment-number 4" style="display:none">
                                                    <div> <?php echo  $obj->inputInteger('inAnnuallyIncrement'); ?></div>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                                <div class="form-group cashbank" style="<?php echo $cashBankStyle; ?>">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['voucherCashOutCode']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <div class="flex">
                                            <div class="consume"> <?php echo $obj->inputText('outCode'); ?> </div> 
                                            <div style="padding-left:1em" > <?php echo ucwords($obj->lang['runningNumber']); ?> </div>
                                            <div> 
                                                <div class="increment-number 1">      
                                                    <div>  <?php echo  $obj->inputInteger('outIncrement'); ?></div>  
                                                </div> 
                                                <div class="increment-number 2" style="display:none">
                                                    <div> <?php echo  $obj->inputInteger('outDailyIncrement'); ?></div>
                                                </div>
                                                <div class="increment-number 3" style="display:none">
                                                    <div> <?php echo  $obj->inputInteger('outMonthlyIncrement'); ?></div>
                                                </div> 
                                                <div class="increment-number 4" style="display:none">
                                                    <div> <?php echo  $obj->inputInteger('outAnnuallyIncrement'); ?></div>
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
                           		<?php } ?> 
                            
                                <div class="form-group cashbank" style="<?php echo $cashBankStyle; ?>">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['counterCashBank']); ?></label> 
                                    <div class="col-xs-9"> 
                                         <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $chartOfAccount, 
                                                                                    'element' => array('value' => 'counterCOACode',
                                                                                                       'key' => 'hidCounterCOAKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData')
                                                                                                    )
                                                                              )
                                                                        );  
                                            ?>
                                    </div> 
                                 </div>
                           
                        </div>
                  </div>
           </div>
      </div> 
      
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
