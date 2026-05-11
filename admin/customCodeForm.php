<?php 
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array('CustomCode.class.php','Warehouse.class.php'));

$customCode = new CustomCode();
$warehouse = new Warehouse();

$obj= $customCode;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'customCodeList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$editCodeCriteria = ''; 

$rs = prepareOnLoadData($obj); 
$rsShippingAddress = array();
 
$_POST['chkIsAutoCode'] = 1;   
$_POST['chkResetWarehouse'] = 0;   

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	  
	$_POST['name'] = $rs[0]['name'];
	$_POST['title'] = $rs[0]['title'];
	$_POST['codeFormat'] = $rs[0]['codeformat']; 
	$_POST['digit'] = $obj->formatNumber($rs[0]['digit']);  
	$_POST['selResetType'] =  $rs[0]['resettypekey'] ;   
	$_POST['chkIsAutoCode'] =  $rs[0]['useautocode'] ;   
	$_POST['chkResetWarehouse'] =  $rs[0]['resetwarehouse'] ; 
	$_POST['selParentModule'] =  $rs[0]['refparentkey'] ;  
	//$_POST['selWarehouseKey'] =  $rs[0]['warehousekey'] ;   
     
	$_POST['selModule'] = $rs[0]['reftabletype'];
      
	$editCodeCriteria = ' or '.$obj->tableTablekey.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['reftabletype']);
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
$arrResetType = $obj->convertForCombobox($obj->getCustomCodeResetType(),'pkey','name');   
$arrModule = $obj->convertForCombobox($obj->getModulCategory(' and ('.$obj->tableTablekey.'.statuskey = 1' .$editCodeCriteria.')'),'pkey','label');  
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true),'pkey','name');  
 
$arrParentModule = $obj->generateComboboxOpt(null,array('criteria' =>' and ('.$obj->tableName.'.statuskey = 1)'),'-----');

$_POST['selDailyPeriod'] = date('d / m / Y'); 
$_POST['selMonthlyPeriod'] = date('F Y');  
$_POST['selAnnuallyPeriod'] = date('F Y');  
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 

<script type="text/javascript"> 
 
    jQuery(document).ready(function(){  
	 	 var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
         var customCode = new CustomCode(tabID,<?php echo json_encode($rs); ?>);
    
         prepareHandler(customCode);   
        
         var fieldValidation =  {
                                  name: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.category[1]
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
       <div class="div-table main-tab-table-1">
			<div class="div-table-row">
				<div class="div-table-col">
                     <div class="div-tab-panel">   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                <div class="col-xs-9"> 
                                <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputAutoCode('code'); ?>
                                </div> 
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('name'); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['title']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('title'); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['moduleName']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputSelect('selModule', $arrModule); ?>
                                </div> 
                            </div>    
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['followCode']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputSelect('selParentModule', $arrParentModule); ?>
                                </div> 
                            </div>  
                            <div class="showNotParent form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['autoCode']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo $obj->inputCheckBox('chkIsAutoCode'); ?> 
                                </div> 
                            </div>  
                            <div class="showNotParent showAutoCode"> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['format']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputText('codeFormat'); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['digit']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputNumber('digit'); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['resetEvery']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputSelect('selResetType', $arrResetType); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['forEachWarehouse']); ?></label> 
                                <div class="col-xs-9">  
                                    <div class="flex">
                                        <div ><?php echo '<span>'.$obj->inputCheckBox('chkResetWarehouse'); ?> </div>
                                        <div class="consume"><?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?></div>
                                    </div>    
                                </div> 
                            </div>  
                         
                            <div class="form-group">
                                <label class="col-xs-3 control-label" style="margin-top:2.5em"><?php echo ucwords($obj->lang['runningNumber']); ?></label> 
                                <div class="col-xs-9">  
                                            <div class="increment-number 1">  
                                                <div class="col-xs-6 control-label" style="padding-left:0">
                                                    <label class="text-muted"><?php echo ucwords($obj->lang['runningNumber']); ?></label>
                                                    <div>  <?php echo  $obj->inputInteger('increment'); ?></div> 
                                                </div>  
                                            </div>  
                                            <div class="increment-number 2" style="display:none"> 
                                                <div class="col-xs-6 control-label" style="padding-left:0">
                                                    <label class="text-muted"><?php echo ucwords($obj->lang['period']); ?></label>
                                                    <div><?php echo $obj->inputDate('selDailyPeriod',array('etc'=>'style="text-align:center;"')); ?></div> 
                                                </div>    
                                                <div class="col-xs-6 control-label">
                                                    <label class="text-muted"><?php echo ucwords($obj->lang['runningNumber']); ?></label>
                                                    <div> <?php echo  $obj->inputInteger('dailyIncrement'); ?></div> 
                                                </div>  
                                            </div> 
                                            <div class="increment-number 3" style="display:none"> 
                                                <div class="col-xs-6 control-label" style="padding-left:0">
                                                    <label class="text-muted"><?php echo ucwords($obj->lang['period']); ?></label>
                                                    <div><?php  echo $obj->inputMonth('selMonthlyPeriod',array('etc'=>'style="text-align:center;"')); ?></div> 
                                                </div>   
                                                
                                                <div class="col-xs-6 control-label">
                                                    <label class="text-muted"><?php echo ucwords($obj->lang['runningNumber']); ?></label>
                                                    <div> <?php echo  $obj->inputInteger('monthlyIncrement'); ?></div> 
                                                </div>    
                                            </div> 
                                            <div class="increment-number 4" style="display:none"> 
                                                <div class="col-xs-6 control-label" style="padding-left:0">
                                                    <label class="text-muted"><?php echo ucwords($obj->lang['period']); ?></label>
                                                    <div> <?php  echo $obj->inputMonth('selAnnuallyPeriod',array('etc'=>'style="text-align:center;"')); ?></div> 
                                                </div>   
                                                
                                                <div class="col-xs-6 control-label">
                                                    <label class="text-muted"><?php echo ucwords($obj->lang['runningNumber']); ?></label>
                                                    <div>  <?php echo  $obj->inputInteger('annuallyIncrement'); ?></div> 
                                                </div>    
                                            </div>  
                                </div> 
                            </div>  
                      </div> 
                </div>
                 
           </div>
      </div>   
      </div>
      <div class="form-button-panel" > <?php echo $obj->generateSaveButton(); ?>  </div>   
    </form>
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
