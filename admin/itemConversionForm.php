<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
includeClass('ItemConversion.class.php');
$itemConversion = createObjAndAddToCol( new ItemConversion());  
$item = createObjAndAddToCol( new Item());  
$itemCategory = createObjAndAddToCol(new ItemCategory());
$warehouse = createObjAndAddToCol( new Warehouse());  
$brand = createObjAndAddToCol(new Brand());

$obj= $itemConversion;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'itemConversionList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 

$editWarehouseInactiveCriteria = '';

$rsDetail = array();  
$rsDetailDestination = array();
//$_POST['trDate'] = date('d / m / Y');
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	 
	//$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
    
    $rsItemConvert = $item->getDataRowById($rs[0]['itemconvertkey']);
	$_POST['itemConvertName'] = $rsItemConvert[0]['name'] ;
	$_POST['hidItemConvertKey'] = $rsItemConvert[0]['pkey'] ; 
    
	$_POST['hidCategoryKey'] = $rs[0]['categorykey']; 
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $itemCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $itemCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
	}
    
	$_POST['hidBrandKey'] = $rs[0]['brandkey']; 
    if (!empty($rs[0]['brandkey'])){
		$rsBrand = $brand->getDataRowById($rs[0]['brandkey']);
		$_POST['brandName'] = $rsBrand[0]['name'];
	}
    
	$_POST['trDesc'] = $rs[0]['trdesc'];
	  
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style> 
    .total-sn-label {font-size: 0.9em; color:#999; font-style: italic}
    .tag-list li {height: 2em; text-align: center; }
    .transaction-detail>.div-table-row:nth-child(2n+3) .tag-list li {background-color: #dedede !important}
    .options-row .form-panel-result {max-height: 10em; overflow: auto}
</style>
<title></title> 
 
<script type="text/javascript">  
 
	  
	jQuery(document).ready(function(){  
        
	 	 var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
         var itemConversion = new  ItemConversion(tabID); 
         prepareHandler(itemConversion);   
        
         var fieldValidation =  {code: {
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
                                        <label class="col-xs-3 control-label">Status</label> 
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
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>   
                                        </div> 
                                    </div> 
                                              
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                         <div class="col-xs-9">  
                                           <?php    
                                            echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $itemCategory,
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'categoryName',
                                                                                                   'key' => 'hidCategoryKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-item-category.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                )
                                           
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['brand']); ?></label> 
                                        <div class="col-xs-9">  
                                           <?php    
                                            echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $brand,  
                                                                                'element' => array('value' => 'brandName',
                                                                                                   'key' => 'hidBrandKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-brand.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                )
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['item']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $item,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'itemConvertName',
                                                                                                   'key' => 'hidItemConvertKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-item.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
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
                                    <?php echo  $obj->inputTextArea('trDesc',array('etc' => 'style="height:10em;"' )); ?>
                                </div> 
                            </div>   
                        </div>
                    </div>
                </div> 
         </div>  
                        
    
        <div class="form-button-panel" > 
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
     <?php  echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
