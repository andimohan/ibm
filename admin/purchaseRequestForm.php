<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('PurchaseRequest.class.php');
$purchaseRequest = createObjAndAddToCol(new PurchaseRequest());
$item = createObjAndAddToCol(new Item());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$supplier = createObjAndAddToCol(new Supplier()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 

$obj= $purchaseRequest;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    

$formAction = 'purchaseRequestList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
 
$rsPurchaseDetail = array();

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){  
	$id = $_GET['id'];	  
    
    $rsPurchaseDetail = $obj->getDetailWithRelatedInformation($id);
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$_POST['trDesc'] = $rs[0]['trdesc'];
    
    if(!empty($rs[0]['supplierkey'])){
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        $_POST['supplierName'] = $rsSupplier[0]['name'] ;
        $_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ;  
    }
    
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
} 


$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');
$arrDefaultUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">

    
	jQuery(document).ready(function(){   
    
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
	  
        var purchaseOrder = new PurchaseRequest(tabID); 
        prepareHandler(purchaseOrder); 
        var fieldValidation =  {
                                code: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.code[1] }, 
                                    }
                                 } 
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
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php echo $obj->inputDate('trDate'); ?> 
                                    </div> 
                                </div>    
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                    <div class="col-xs-9"> 
                                         <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>  
                                    </div> 
                                </div>  
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['supplier']); ?></label> 
                                    <div class="col-xs-9"> 
                                       <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $supplier,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'supplierName',
                                                                                                   'key' => 'hidSupplierKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-supplier.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                    'url' => 'supplierForm.php',
                                                                                                    'element' => array('value' => 'supplierName',
                                                                                                           'key' => 'hidSupplierKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['supplier'])
                                                                                                )
                                                                              )
                                                                        );  
                                            ?>
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
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> @</div>                    
                    <div class="div-table-col detail-col-header" style="width:180px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>                    
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
              
                <?php 
                    $totalRows = count($rsPurchaseDetail);
            
                    for ($i=0;$i<=$totalRows; $i++){  
					
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        $arrUnit = $arrDefaultUnit;
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        } else {
                            $decimal = 0;
                            $inputnumber = 'inputnumber';


                            $_POST['hidDetailKey[]'] =  $rsPurchaseDetail[$i]['pkey'];
                            $_POST['hidItemKey[]'] =  $rsPurchaseDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsPurchaseDetail[$i]['itemname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['qty']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['priceinunit']); 
                            $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsPurchaseDetail[$i]['subtotal']); 
                            $_POST['selUnit[]'] =  $rsPurchaseDetail[$i]['unitkey']; 
                            
                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsPurchaseDetail[$i]['itemkey']),'conversionunitkey','unitname'); 
                        } 
                          
                ?>
                <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc)); ?></div>                    
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>                    
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div> 
                <?php  }   ?>  
                   
         </div>        
       
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
      
         
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
