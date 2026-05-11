<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CashBankTransfer.class.php');
$cashBankTransfer= createObjAndAddToCol( new CashBankTransfer()); 
$cashBank= createObjAndAddToCol( new CashBank()); 
$warehouse = createObjAndAddToCol( new Warehouse());

$obj= $cashBankTransfer;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'cashBankTransferList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsDetail = array();

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

$useStorage = $obj->useStorage;

$editWarehouseInactiveCriteria = '';

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['trDesc'] = $rs[0]['trdesc']; 
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']); 
      
    $_POST['selWarehouseKey'] = $rs[0]['warehousekey']; 
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
  
    //update file 
    $rsFileDetail = $obj->getFileDetail($id);
    if($useStorage){ 
        
    }else{  
        $obj->prepareLoadedFile($id,array('file' => $rsFileDetail )); 
    }
    
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
		var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
            
        var varConstant = {   
			USE_STORAGE : <?php echo ($useStorage) ? "true" : "false"; ?>,
			uploadFileFolder :  "<?php echo $obj->uploadFileFolder; ?>",
            rsFile : <?php echo json_encode($rsFileDetail); ?>
         };
          
        var opt = new Array();  
        var cashBankTransfer = new CashBankTransfer(tabID,varConstant,opt);
		  
        prepareHandler(cashBankTransfer);  
          
        var fieldValidation =  {
				code: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        }, 
                    }
				}
				
            };
        
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
                                    <?php echo $obj->inputAutoCode('code'); ?>
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
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['fromAccount']); ?></div>
					<div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['toAccount']); ?></div>
                    <?php if(ADV_FINANCE){ ?>
                    <div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['cashBankNumber']); ?></div>
                    <?php } ?> 
                    <div class="div-table-col detail-col-header" style="width:300px;"><?php echo ucwords($obj->lang['note']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['amount']); ?></div> 
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>" style="width:45px"></div>
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
                            $_POST['hidCOAFromKey[]'] =  $rsDetail[$i]['coafromkey']; 
                            $_POST['hidCOAToKey[]'] =  $rsDetail[$i]['coatokey']; 
                            $_POST['COAFromName[]'] =  $rsDetail[$i]['codenamefrom']; 
                            $_POST['COAToName[]'] = $rsDetail[$i]['codenameto']; 						
                            $_POST['amount[]'] =   $obj->formatNumber($rsDetail[$i]['amount']);  
                            $_POST['trdesc[]'] =  $rsDetail[$i]['trdesc']; 
                            
                            
                            if(ADV_FINANCE){ 
                                $arrCashBankRef = array();
                                array_push($arrCashBankRef, $cashBank->getCashBankRef($id,$obj->tableName,$rsDetail[$i]['coafromkey'])['code']);
                                array_push($arrCashBankRef, $cashBank->getCashBankRef($id,$obj->tableName,$rsDetail[$i]['coatokey'])['code']);  
                                $_POST['cashBankRefCode[]'] = implode(', ', $arrCashBankRef); 
                            }
                        } 
                ?>
                
                 <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('COAFromName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidCOAFromKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('COAToName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidCOAToKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <?php if(ADV_FINANCE){ ?>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('cashBankRefCode[]',array('overwritePost' => $overwrite, 'readonly' => true )); ?></div> 
                    <?php } ?>    
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('trdesc[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('amount[]',array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right" ' .$etc)); ?></div> 
                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" attrhandler="cashBankTransfer.calculateTotal()"')); ?></div>
                </div>
            
                <?php  } ?>  
                    
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
