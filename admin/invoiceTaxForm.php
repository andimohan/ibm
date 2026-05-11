<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 


includeClass(array('InvoiceTax.class.php','Warehouse.class.php'));   
$invoiceTax = createObjAndAddToCol( new InvoiceTax()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$emklOrderInvoice = createObjAndAddToCol( new EMKLOrderInvoice()); 
$truckingServiceOrderInvoice = createObjAndAddToCol( new TruckingServiceOrderInvoice()); 
 
$obj = $invoiceTax;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'invoiceTaxList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$useStorage = $obj->useStorage;

$editWarehouseInactiveCriteria = ''; 
 
$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

//untuk memebedakan mana TMS dan FMS
$arrType = $obj->getCompanySubType();
$arrType = array_column($arrType,null,'invoicetabletypekey');

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
        
    if($rs[0]['reftabletype'] == $obj->arrTablekeys['truckingInvoiceKey']){
    
        $rsTruckingInvoice = $truckingServiceOrderInvoice->searchDataRow(array($truckingServiceOrderInvoice->tableName.'.pkey',
                                                                               $truckingServiceOrderInvoice->tableName.'.code'),
                                                                         ' and '.$truckingServiceOrderInvoice->tableName.'.pkey = '.$obj->oDbCon->paramString($rs[0]['refkey'])); 
		

		if(!empty($rsTruckingInvoice)){ 
			$_POST['refTruckingInvoiceCode'] = $rsTruckingInvoice[0]['code'];
			$_POST['hidRefTruckingInvoiceHeaderKey'] = $rsTruckingInvoice[0]['pkey'];   
		}                                                               
      
        $arrTaxPercentage = $truckingServiceOrderInvoice->getTaxPercentageType($rs[0]['refkey']);		
		
    }else{    
        $rsEMKLInvoice = $emklOrderInvoice->searchDataRow(array($emklOrderInvoice->tableName.'.pkey',
                                                                               $emklOrderInvoice->tableName.'.code'),
                                                                         ' and '.$emklOrderInvoice->tableName.'.pkey = '.$obj->oDbCon->paramString($rs[0]['refkey'])); 
 
		if(!empty($rsEMKLInvoice)){ 
			$_POST['refEMKLInvoiceCode'] = $rsEMKLInvoice[0]['code'];
			$_POST['hidRefEMKLInvoiceHeaderKey'] = $rsEMKLInvoice[0]['pkey'];   
		}    
		 
        $arrTaxPercentage = $emklOrderInvoice->getTaxPercentageType($rs[0]['refkey']);

    }
	
        //update file 
    if($useStorage){ 
        $rsFileDetail = $obj->getFileDetail($id);
    }else{  
        $rsItemFile = $obj->getFileDetail($id);
        $obj->prepareLoadedFile($id,array('file' => $rsItemFile ));
    }
    
	 
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$arrTax = $obj->generateComboboxOpt(array('data' => $arrTaxPercentage,'label' => 'taxpercentage')); 
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  

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
                            COMPANY_TYPE : <?php echo json_encode(COMPANY_TYPE); ?>,
                            TYPE : <?php echo json_encode($arrType); ?>,
                            USE_STORAGE : <?php echo ($useStorage) ? "true" : "false"; ?>
                            };        
        
            
            var opt = {};

            opt.fileFolder = "<?php echo $obj->uploadFileFolder; ?>";
            opt.fileUploaderTarget = "item-file-uploader";
            opt.arrFile = Array();
        
            <?php
            if (isset($id) && !empty($id)) {
                for ($i = 0; $i < count($rsItemFile); $i++) {
                    echo 'opt.arrFile.push("' . $rsItemFile[$i]['file'] . '"); ';
                }
            } ?>
        
         var invoiceTax = new InvoiceTax(tabID,varConstant,opt);
 
         prepareHandler(invoiceTax);
        
        var fieldValidation =  {
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceReference']); ?></label> 
                                        <div class="col-xs-9"> 
											<div class="flex">
                                                <div><?php echo $obj->inputSelect('selType', $arrType ); ?> </div>
                                                <div class="consume "> 
													  <div class="transtype transtype-<?php echo $obj->arrTablekeys['truckingInvoiceKey']; ?>">    
                                                        <?php                
                                                                echo $obj->inputAutoComplete(array( 
                                                                                                    'element' => array('value' => 'refTruckingInvoiceCode',
                                                                                                                       'key' => 'hidRefTruckingInvoiceHeaderKey'),
                                                                                                    'source' =>array(
                                                                                                       'url' => 'ajax-trucking-service-order-invoice.php',                                                                                                     
                                                                                                        'data' => array( 'action' =>'searchData', 'statuskey' => '(2,3)')
                                                                                                    ) ,
 																									'callbackFunction' => 'getTabObj().updateTruckingInvoice()'
                                                                                                  )
                                                                                            );  
                                                        ?>  
                                                    </div>
                                                    <div class="transtype transtype-<?php echo $obj->arrTablekeys['emklInvoiceKey']; ?>">
                                                    <?php                
                                                            echo $obj->inputAutoComplete(array( 
                                                                                                'element' => array('value' => 'refEMKLInvoiceCode',
                                                                                                                   'key' => 'hidRefEMKLInvoiceHeaderKey'),
                                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-emkl-order-invoice.php',
                                                                                                    'data' => array( 'action' =>'searchData', 'statuskey'=>2)
                                                                                                ),
                                                                                                'callbackFunction' => 'getTabObj().updateEMKLInvoice()'  
                                                                                              )
                                                                                        );  
                                                    ?>      
                                                    </div>  
												</div>
													<div><?php echo $obj->inputSelect('selTaxPercentage', $arrTax ); ?> </div>
                                                <div>%</div>
											</div> 
                                        </div> 
                                    </div> 

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse); ?>
                                        </div> 
                                    </div>  
                       
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceTaxNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('invoiceTaxNumber'); ?>
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
                                    <div class="div-table" style="width:100%">
                                        <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div>
                                        <div class="div-table-row">
                                            <div class="div-table-col-5">
                                                <!-- file uploader -->
                                                <div class="item-file-uploader">
                                                    <ul class="file-list"></ul>
                                                    <div style="clear:both; height:1em;"></div>
                                                    <div class="file-uploader">
                                                        <noscript>
                                                            <p>Please enable JavaScript to use file uploader.</p>
                                                        </noscript>
                                                    </div>
                                                </div>
                                                <!-- file uploader -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php } ?>
                        
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
          
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(1,2),true);?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>