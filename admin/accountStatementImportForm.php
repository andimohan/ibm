<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('AccountStatementImport.class.php'));
$accountStatementImportImport = createObjAndAddToCol( new AccountStatementImport()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount()); 
$paymentMethod = createObjAndAddToCol( new PaymentMethod()); 

$obj= $accountStatementImportImport;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'accountStatementImportList'; 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');  

$editWarehouseInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';

$rs = prepareOnLoadData($obj);  

$rsFile = array();

if (!empty($_GET['id'])){    
	  
	$id = $_GET['id'];
	
 
    	$rsCOACol = $chartOfAccount->searchDataRow(array($chartOfAccount->tableName.'.pkey',$chartOfAccount->tableName.'.code',$chartOfAccount->tableName.'.name'),
												' and ' . $chartOfAccount->tableName.'.pkey in ('.$obj->oDbCon->paramString($rs[0]['coakey'] ,',').')'
											   );
    
    
	$rsCOACol = array_column($rsCOACol, null,'pkey');
    $_POST['virtualAccount'] = $rs[0]['virtualaccount'];
    $_POST['selPaymentMethod'] = $rs[0]['paymentmethodkey'];
	$_POST['hidCOAKey'] = $rs[0]['coakey'];  
		if (!empty($rs[0]['coakey'])){ 
			$rsCOA = $rsCOACol[$rs[0]['coakey']]; 
			$_POST['coaName'] = $rsCOA['code'] . ' - ' . $rsCOA['name']; 
	}    
    
	$rsFile = array();     
    
	if( !empty($rs[0]['file'])){
		$rsFile[0]['file'] =  $rs[0]['file'];
	
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	}
	
	
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['paymentmethodkey']);
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 

$rsPaymentMethod =  $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')',' order by '.$paymentMethod->tableName.'.name asc');
$arrPaymentMethod = $obj->convertForCombobox($rsPaymentMethod,'pkey','name'); 

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
            tablekey : tablekey
         };

         var accountStatementImport = new AccountStatementImport(tabID,varConstant, "<?php echo $obj->uploadFolder; ?>", <?php echo json_encode($rsFile); ?>);
    
         prepareHandler(accountStatementImport);   
        
         var fieldValidation =  {
                                     code: { 
                                            validators: {
                                                notEmpty: {
                                                    message: phpErrorMsg.code[1]
                                                }, 
                                            }
                                        }  
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
                                            <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse); ?> 
                                        </div> 
                                    </div>  
								   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?>  
                                        </div> 
                                    </div>  
                                    
								   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['virtualAccount']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputText('virtualAccount'); ?>
                                        </div>
                                    </div>
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['paymentMethod']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                               /* echo $obj->inputAutoComplete(array(  
                                                                                    'element' => array('value' => 'paymentMethodName',
                                                                                                       'key' => 'hidPaymentMethod'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    )  
                                                                                  )
                                                                            );  */
                                                ?> 
                                            
                                                                                       
                                            
                                            <?php echo  $obj->inputSelect('selPaymentMethod', $arrPaymentMethod); ?> 

                                        </div> 
                                    </div> 
                                   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>  
								    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['file']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <!-- image uploader --> 
                                            <div class="item-file-uploader">
                                                <ul class="file-list" ></ul>
                                                <div style="clear:both; height:1em; "></div>
                                                <div class="file-uploader">	
                                                    <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                                </div>
                                              </div>  
                                            <!-- image uploader --> 
                                        </div> 
                                    </div> 
                                   
                                </div>   
                    </div>    
                </div>
        </div>    
                     
    <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
