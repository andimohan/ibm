<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass('GeneralJournal.class.php');
$generalJournal = createObjAndAddToCol(new GeneralJournal());
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
$warehouse =  createObjAndAddToCol(new Warehouse());
$currency =  createObjAndAddToCol(new Currency());

$obj= $generalJournal;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    
$useStorage = $obj->useStorage;

$formAction = 'generalJournalList'; 

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$refkey = '';
$editWarehouseInactiveCriteria = '';

$rsDetail = array();
$rsItemFile = array();

$_POST['trDate'] = date('d / m / Y');

$useCurrencyRevaluation = $obj->loadSetting('currencyRevaluation');
$useCurrencyRevaluation = ($useCurrencyRevaluation == 1) ? true: false;

$rs = prepareOnLoadData($obj);  
$rsRefFiles = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsDetail = $obj->getDetailById($id);
	$rsRefFiles = $obj->getDocumentFiles($rs);
	  
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
	$_POST['trDesc'] = $rs[0]['trdesc'];
	$_POST['totalDebit'] =  $obj->formatNumber($rs[0]['totaldebit'],2) ;
	$_POST['totalCredit'] =  $obj->formatNumber($rs[0]['totalcredit'],2) ;
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
    $_POST['refCode'] = $rs[0]['refcode'];  
    $refkey = $rs[0]['refkey'];
     
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
    
      
    //update file 
    if($useStorage){ 
        $rsFileDetail = $obj->getFileDetail($id);
    }else{ 

        $rsItemFile = $obj->getItemFile($id);

        if(count($rsItemFile) > 0){
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
 
$arrCurrency = $currency->generateComboboxOpt(null,array('criteria' => ' and ('.$currency->tableName.'.statuskey = 1)'));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
 
    
	jQuery(document).ready(function(){  
	 	
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;
                   
            
         var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
        
         var varConstant = {  
            tablekey : tablekey,
            currency : <?php echo json_encode(CURRENCY); ?>,
            useStorage : <?php echo ($useStorage) ? "true" : "false"; ?>
         };
             
        var generalJournal = new GeneralJournal(tabID,varConstant, "<?php echo $obj->uploadFileFolder; ?>", <?php echo json_encode($rsItemFile); ?>); 
         prepareHandler(generalJournal);   
         
        
         var fieldValidation =  { code: { 
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
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['reference']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('refCode',array('readonly' => true));  ?>
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputDate('trDate');  ?>
                            </div> 
                        </div>   
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                            <div class="col-xs-9"> 
                                <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>  
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
                                            <ul class="file-list" ></ul>
                                            <div style="clear:both; height:1em; "></div>
                                            <div class="file-uploader">	
                                                <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                            </div>
                                          </div>  
                                        <!-- file uploader -->
                                        <?php if (!empty($rs) && in_array($rs[0]['statuskey'], array(2,3)) ) {
                                             echo $obj->inputButton('btnUpdateFile', $obj->lang['update'], array('allowedStatusForEdit' => array(1,2,3),'class' =>'btn btn-primary btn-second-tone'));
                                        } ?>
                                </div>  
                              </div>  
                              </div>  
                            <?php } ?>



                        <?php if(!empty($rsRefFiles)) {  ?>
                                <div class="div-tab-panel">
                                    <div class="div-table" style="width:100%"> 
                                        <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['fileReference']); ?></div>  
                                        <div class="div-table-row">
                                                <div class="div-table-col-3">
                                                        <ul class="file-list">
                                                            <?php foreach($rsRefFiles as $rowFileRef) { ?> 
                                                               <li><a href="<?php echo $rowFileRef['url']; ?>" target="_blank"><div class="panel"><?php echo $rowFileRef['file']; ?></div></a></li>
                                                            <?php } ?>
                                                        </ul>
                                           </div>   </div>  
                                        
                                    </div>
                                </div>     
                        <?php } ?>
                </div>
           </div>
       </div>     
        
        <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['account']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:280px;"><?php echo ucwords($obj->lang['note']); ?></div> 
                    
                    <?php if($useCurrencyRevaluation){ ?>
                        <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['debitSource']); ?> </div>
                        <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['creditSource']); ?> </div>
                        <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['currency']); ?></div>
                        <div class="div-table-col detail-col-header" style="width:90px; text-align:right;"><?php echo ucwords($obj->lang['rate']); ?></div>
					<?php } ?>
                    
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['debit']); ?></div>
					<div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['credit']); ?></div>
                    <div class="div-table-col detail-col-header  icon-col <?php echo $obj->hideOnDisabled(); ?>" style=" width:45px;"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsDetail);
            
                    $readonlyDebitCredit = ($useCurrencyRevaluation) ? true : false;
                        
                    for ($i=0;$i<=$totalRows; $i++){  
					
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                        } else {  
                            $rsCOA = $chartOfAccount->getDataRowById($rsDetail[$i]['coakey']);

                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['hidCOAKey[]'] =  $rsDetail[$i]['coakey']; 
                            $_POST['COAName[]'] =  $rsCOA[0]['code']. ' - ' . $rsCOA[0]['name']; 
                            $_POST['debit[]'] =   $obj->formatNumber($rsDetail[$i]['debit'],2);
                            $_POST['credit[]'] =   $obj->formatNumber($rsDetail[$i]['credit'],2);;
                            $_POST['debitSource[]'] =   $obj->formatNumber($rsDetail[$i]['debitsource'],2);
                            $_POST['creditSource[]'] =   $obj->formatNumber($rsDetail[$i]['creditsource'],2);
                            $_POST['selCurrencyKey[]'] =  $rsDetail[$i]['currencykey']; 
                            $_POST['rate[]'] =   $obj->formatNumber($rsDetail[$i]['rate'],2);
                            $_POST['trdescDetail[]'] =  $rsDetail[$i]['trdesc'];
                        }  
                        
                        
                ?>
                <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputHidden('hidDetailKey[]',array('disabled' => $disabled, 'overwritePost' => $overwrite )); ?><?php echo $obj->inputText('COAName[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?><?php echo $obj->inputHidden('hidCOAKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('trdescDetail[]'); ?></div> 
                    
                    
                    <?php if($useCurrencyRevaluation){ ?>
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('debitSource[]',array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right" ','disabled' => $disabled )); ?></div> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('creditSource[]',array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right"  ' ,'disabled' => $disabled )); ?></div> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selCurrencyKey[]',$arrCurrency,array('overwritePost' => $overwrite,'readonly' => $readOnly)); ?></div>                      
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('rate[]',array('value' => 1,'overwritePost' => $overwrite, 'etc' => 'style="text-align:right" ' ,'disabled' => $disabled )); ?></div> 
                    <?php } ?>
                    
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('debit[]',array('overwritePost' => $overwrite,'readonly' => $readonlyDebitCredit, 'etc' => 'style="text-align:right"  ','disabled' => $disabled )); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('credit[]',array('overwritePost' => $overwrite,'readonly' => $readonlyDebitCredit, 'etc' => 'style="text-align:right"  ' ,'disabled' => $disabled )); ?></div> 
                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top; width:45px;"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?></div>
                </div>  
                <?php  } ?>  
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
		  
		  <div > 
            <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:45px; height: 1em"></div>  
			 <div class="div-table" style="float:right; ">
				<div class="div-table-row  form-group"> 
					<div class="div-table-col-5" style="text-align:right;">
						Total 
					</div>  
					<div class="div-table-col-3" style="width:120px;"> 
                         <?php echo $obj->inputDecimal('totalDebit', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>     
					</div> 
					<div class="div-table-col-3" style="width:120px;"> 
                         <?php echo $obj->inputDecimal('totalCredit', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>      
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
