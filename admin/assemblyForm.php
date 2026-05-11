<?php
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass(array('Assembly.class.php'));
$assembly = createObjAndAddToCol(new Assembly());
$billOfMaterials = createObjAndAddToCol(new BillOfMaterials());
$item = createObjAndAddToCol(new Item());
$warehouse = createObjAndAddToCol(new Warehouse());
$itemUnit = new ItemUnit();

$obj= $assembly;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$formAction = 'assemblyList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
$editBillOfMaterialsInactiveCriteria = ''; 
$finishedUnitName = '';

$_POST['trDate'] = date('d / m / Y');
$_POST['qty'] = 1;

$rsDetail = array();
$arrBOM = array();
$rs = prepareOnLoadData($obj);
if (!empty($_GET['id'])){
    $id = $_GET['id'];  
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
 
	$rsBOM = $billOfMaterials->getDataRowById($rs[0]['bomkey']);
	
	$rsItem = $item->getDataRowById($rs[0]['itemkey']);
	$_POST['itemName'] = $rsItem[0]['name'];

	$rsItemUnit = $itemUnit->getDataRowById($rsItem[0]['baseunitkey']);
	$finishedUnitName  = $rsItemUnit[0]['name'];
	
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousefromkey']); 
	$editWarehouseInactiveCriteria .= ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousetokey']); 
	$editBillOfMaterialsInactiveCriteria .= ' or  '.$billOfMaterials->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['pkey']).' or '.$billOfMaterials->tableName.'.itemkey = ' . $obj->oDbCon->paramString($rs[0]['itemkey']); 
	$arrBOM = $billOfMaterials->generateComboboxOpt(null,array('criteria' =>' and ( '.$billOfMaterials->tableName.'.itemkey = '.$rs[0]['itemkey'].' and '.$billOfMaterials->tableName.'.statuskey = 1 ' . $editBillOfMaterialsInactiveCriteria. ')')); 

}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

<script type="text/javascript"> 
   jQuery(document).ready(function() {
       
            var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
   
          var varConstant = {  
            TABLEKEY : tablekey,
         };
 
		var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var assembly  = new Assembly(tabID,varConstant);
    
        prepareHandler(assembly);   
        
        var fieldValidation =  {
                                    code: {
										validators: {
											notEmpty: {
												message: phpErrorMsg.code[1]
											},
										}
									},

									itemName: {
									   validators: {
										   notEmpty: {
											   message:  phpErrorMsg.item[1]
										   },
									   }
								   },
									BOMName: {
									   validators: {
										   notEmpty: {
											   message:  phpErrorMsg.billofmaterials[1]
										   },
									   }
								   },
									qty: {
									   validators: {
										 greaterThan: {
												value: 0,
												inclusive: false,
												separator: ',', 
												message: phpErrorMsg.amount[2]
											}
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
                                    <?php echo $obj->inputAutoCode('code'); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputDate('trDate'); ?> 
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['rawItemWarehouse']); ?></label>
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selWarehouseFromKey', $arrWarehouse); ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['resultItemWarehouse']; ?></label>
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selWarehouseToKey', $arrWarehouse); ?>
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['item']; ?></label>  
                                <div class="col-xs-9">  
                                    <?php    
                                        echo $obj->inputAutoComplete(array(
                                                                            'revalidateField' => true, 
                                                                            'objRefer' => $item,
                                                                            'element' => array('value' => 'itemName',
                                                                                               'key' => 'hidItemKey'),
                                                                                               'source' =>array(
                                                                                                                'url' => 'ajax-item.php',
                                                                                                                'data' => array(  'action' =>'searchData')
                                                                                                                ) , 
                                                                                                'callbackFunction' =>  'getTabObj().updateItemInformation()'
                                                                        )
                                                                );  
                                    ?> 
                                </div> 
                            </div> 
                        
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['qty']); ?></label> 
                                <div class="col-xs-7"> 
                                    <?php echo $obj->inputNumber('qty'); ?>
                                </div> 
                                <div class="col-xs-2 control-label baseunitname text-muted" style="vertical-align:middle; padding-left:0" ><?php echo $finishedUnitName; ?></div>
                            </div>
                           

                           <!--
 <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['additionalCost']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputNumber('cost'); ?>
                                </div> 
                            </div>
-->
                         <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['billOfMaterials']); ?></label>
                              		  <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selBOM', $arrBOM); ?>
                                </div> 
                            </div>

                </div>
            </div> 
                <div class="div-table-col">   
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div>
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
                <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['item']); ?></div>
                <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['qtyBom']); ?></div>
                <div class="div-table-col detail-col-header" style="width:80px;"></div>
                <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['qtyUsed']); ?></div>
                <div class="div-table-col detail-col-header" style="width:80px;"></div>
                <div class="div-table-col detail-col-header" style="width:45px;display:none"></div>
            </div>

                <?php  
                    $totalRows = count($rsDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        if ($i == $totalRows){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"';
                        } else {  
                            $_POST['hidItemDetailKey[]'] =  $rsDetail[$i]['itemkey'];
                            $_POST['itemNameDetail[]'] =  $rsDetail[$i]['itemname'];
                            $_POST['qtyDetail[]'] =   $obj->formatNumber($rsDetail[$i]['qtybom']);
                            $_POST['qtyUsed[]'] =   $obj->formatNumber($rsDetail[$i]['qtyused']);
                        }
                ?>
                <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemNameDetail[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qtyDetail[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right" '.$etc)); ?></div>  
                    <div class="div-table-col detail-col-detail baseitemBOMunit text-muted"><?php  echo (!empty($rsDetail[$i]['baseunitname'])) ? $rsDetail[$i]['baseunitname'] : '';  ?></div>  
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qtyUsed[]',array('overwritePost' => $overwrite,'readonly' => true, 'etc' => 'style="text-align:right" '.$etc)); ?></div>
                    <div class="div-table-col detail-col-detail baseitemusedunit text-muted"><?php  echo (!empty($rsDetail[$i]['baseunitname'])) ? $rsDetail[$i]['baseunitname'] : '';  ?></div>  
                    <div class="div-table-col detail-col-detail " style="display:none"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1" ','class' => 'btn btn-link remove-button')); ?></div>
                </div>
            
                <?php }   ?>
         </div>

        
        <!--<div style="clear:both; height:1em;"></div> 
        <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
        
      -->
        <div>   
            <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:45px; height: 1em"></div>  
        </div>          
      
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	    <?php  echo $obj->generateSaveButton(array(),true);?>
        </div>

    </form>
     <?php  echo $obj->showDataHistory(); ?>
</div>
</body>

</html>
