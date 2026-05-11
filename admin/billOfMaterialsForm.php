<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass(array('BillOfMaterials.class.php'));
$billOfMaterials = createObjAndAddToCol(new BillOfMaterials());
$item = createObjAndAddToCol(new Item());

$obj = $billOfMaterials;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'billOfMaterialsList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$rsDetail = array();

$rs = prepareOnLoadData($obj);   

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
  
	if (!empty($rs[0]['itemkey'])){
		$rsItem = $item->getDataRowById($rs[0]['itemkey']);
		$_POST['itemName'] = $rsItem[0]['name'];
	}  
    
} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label' => 'status'));

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
        var billOfMaterials  = new BillOfMaterials(tabID,varConstant);
    
        prepareHandler(billOfMaterials,varConstant);   
        
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
													message: phpErrorMsg.item[1]
												}, 
											}
										}, 
										name: { 
											validators: {
												notEmpty: {
													message: phpErrorMsg.name[1]
												}, 
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
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['item']; ?></label>  
                            <div class="col-xs-9"> 
                                    <?php    
                                    echo $obj->inputAutoComplete(array(
                                                                        'objRefer' => $item,
                                                                        'revalidateField' => true,
                                                                        'element' => array('value' => 'itemName',
                                                                                            'key' => 'hidItemKey'),
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
       
        <div class="div-table  mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['item']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:70px;"></div> 
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
                            $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
                            $_POST['hidItemKeyDetail[]'] = $rsItem[0]['pkey'];
                            $_POST['itemNameDetail[]'] =  $rsItem[0]['name']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']);
                        }
                    ?>
            
                 <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemNameDetail[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemKeyDetail[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]',array('overwritePost' => $overwrite,'value' => 1, 'etc' => 'style="text-align:right" '.$etc)); ?></div>  
                    <div class="div-table-col detail-col-detail"><div class="text-muted"><span class="baseitemunit"><?php echo $rsDetail[$i]['baseunitname'];?></span></div></div>
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1" ','class' => 'btn btn-link remove-button')); ?></div>
                </div>
                         
                <?php  } ?>   
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows']); ?></div>
      
        <div>   
            <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:45px; height: 1em"></div>  
        </div>          
      
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
