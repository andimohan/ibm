<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('WarehouseLocation.class.php','Warehouse.class.php'));
$warehouseLocation = createObjAndAddToCol( new WarehouseLocation()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 

$obj= $warehouseLocation;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'warehouseLocationList';
$rsWarehouseLayout = array();

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editCategoryCriteria= '';
$editWarehouseInactiveCriteria= ''; 

$rs = prepareOnLoadData($obj);  
$rsCategoryDetail = array();

if (!empty($_GET['id'])){ 
    $id = $_GET['id'];
    
    $rsWarehouseLayout = $obj->getWarehouseLayoutDetail($id);  
   
	$_POST['orderList'] = $obj->formatNumber($rs[0]['orderlist']);
	$_POST['secondPercentage'] = $obj->formatNumber($rs[0]['secondpercentage'],2);
	$_POST['sellPercentage'] = $obj->formatNumber($rs[0]['sellpercentage'],2);
 
    $_POST['txtDescription'] = $obj->HTMLSpecialCharacterForEditor($_POST['txtDescription']);
    // $_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
    

    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
	 
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   

$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  

<script type="text/javascript"> 

	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
 
        
        var warehouseLocation = new WarehouseLocation(tabID,<?php echo json_encode(array('rsDetailLayout' => $rsWarehouseLayout)); ?>);
    
        prepareHandler(warehouseLocation);  
        
         var fieldValidation =  {
                                    code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }
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
        <?php echo $obj->generateLangOptions(); ?>  
       <div class="div-table main-tab-table-2">
            <div class="div-table-row">
                <div class="div-table-col"> 
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
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
                     			<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('allowedStatusForEdit' => array(1)) ); ?>  
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shortDescription']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo  $obj->inputTextArea('trShortDesc',array('etc' => 'style="height:10em;"', 'multilang' => true )); ?>
                                </div> 
                            </div>  
                             
                    </div>
                
                </div>
                
                <div class="div-table-col">   
                    
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-green">
                        <?php echo ucwords($obj->lang['warehouseLayout']); ?>
                     </div>

                        <div class="div-table mnv-transaction transaction-detail" style="width:100%;">
                           <div class="div-table-row"> 
                              <div class="div-table-col detail-col-header" style="border:0"><?php echo ucwords($obj->lang['name']); ?></div>
                              <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border:0"></div>
                              <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border:0"></div> 
                           </div>

                           <?php 
                              $totalDetail = count($rsWarehouseLayout); 

                              for ($i=0;$i<=$totalDetail; $i++){  

                                 $class =  'transaction-detail-row';
                                 $overwrite = true;
                                 $disabled = false; 
                                 
                                 if ($i == $totalDetail ){
                                    $class = 'detail-warehouse-row-template row-template';
                                    $overwrite = false;
                                    $disabled = true; 
                                 } else {    

                                    $_POST['hidDetailWarehouseLayoutKey[]'] =  $rsWarehouseLayout[$i]['pkey'];
                                    $_POST['hidWarehouseLayoutKey[]'] =  $rsWarehouseLayout[$i]['reflayoutkey']; 
                                    $_POST['warehouseLayoutName[]'] =  $rsWarehouseLayout[$i]['warehouselayoutname']; 
                                 }  

                           ?>
   
                           <div class="div-table-row <?php echo $class; ?>" style=""> 
                              <div class="div-table-col detail-col-detail">
                                 <?php echo $obj->inputHidden('hidDetailWarehouseLayoutKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?> 
                                 <?php echo $obj->inputText('warehouseLayoutName[]',array('overwritePost' => $overwrite)); ?>
                                 <?php echo $obj->inputHidden('hidWarehouseLayoutKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                              </div> 
                              <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnAddDPRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="detail-downpayment-row-template"')); ?></div>
                              <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"')); ?></div>
                           </div>

                        <?php } ?> 

                        </div>
                    </div>
                </div> 
           </div>
      </div>  
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>  
    <?php echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
