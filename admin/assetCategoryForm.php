<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Category.class.php','AssetCategory.class.php','ChartOfAccount.class.php'));
$assetCategory = createObjAndAddToCol(new AssetCategory());
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());

$obj= $assetCategory;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    
$formAction = 'assetCategoryList';

$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
	 
	$_POST['name'] = $rs[0]['name'];
	$_POST['selCategory'] = $rs[0]['parentkey']; 
	$_POST['selStatus'] = $rs[0]['statuskey']; 
		
    if (!empty($rs[0]['coaassetkey'])){
        $rsCOA = $chartOfAccount->getDataRowById($rs[0]['coaassetkey']);
        $_POST['hidCOAAssetKey'] = $rs[0]['coaassetkey'];
        $_POST['coaAsset'] = $rsCOA[0]['code'].' - '.$rsCOA[0]['name'] ;
    }
		
    if (!empty($rs[0]['coadepreciationkey'])){
        $rsCOA = $chartOfAccount->getDataRowById($rs[0]['coadepreciationkey']);
        $_POST['hidCOADepreciationKey'] = $rs[0]['coadepreciationkey'];
        $_POST['coaDepreciation'] = $rsCOA[0]['code'].' - '.$rsCOA[0]['name'] ;
    }
     
    if (!empty($rs[0]['coaaccumulatedkey'])){
        $rsCOA = $chartOfAccount->getDataRowById($rs[0]['coaaccumulatedkey']);
        $_POST['hidCOAAccumulatedKey'] = $rs[0]['coaaccumulatedkey'];
        $_POST['coaAccumulated'] = $rsCOA[0]['code'].' - '.$rsCOA[0]['name'] ;
    }
 
}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));
$arrAssetType = $obj->generateComboboxOpt(array('data' => $obj->getAssetType()));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript">
  
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var assetCategory = new AssetCategory(tabID);
    
        prepareHandler(assetCategory);   
        
        var fieldValidation =  {
                                    code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 

                                    name: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.category[1]
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
                                        <label class="col-xs-3 control-label"><?php echo $class->lang['status'] ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                        </div> 
                                    </div>    
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $class->lang['code'] ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                     </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $class->lang['type'] ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputSelect('selAssetType', $arrAssetType); ?>
                                        </div> 
                                    </div>    
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $class->lang['category'] ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('name'); ?> 
                                        </div> 
                                     </div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $class->lang['usefulLife']. ' ('.$class->lang['year'].')' ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputNumber('aging'); ?>
                                        </div> 
                                     </div> 
                            </div>   
                  </div>
				   <div class="div-table-col">  
                  		   	<div class="div-tab-panel">    
								   <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['financialInformation']); ?></div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $class->lang['asset'] ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php    
                                                echo $obj->inputAutoComplete(array(  
                                                                                    'element' => array('value' => 'coaAsset',
                                                                                                       'key' => 'hidCOAAssetKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    )  
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div>    
                                      
									<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $class->lang['depreciationExpense'] ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php    
                                                echo $obj->inputAutoComplete(array(  
                                                                                    'element' => array('value' => 'coaDepreciation',
                                                                                                       'key' => 'hidCOADepreciationKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    )  
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div>    
                                   
									<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $class->lang['accumulatedDepreciation'] ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php    
                                                echo $obj->inputAutoComplete(array(  
                                                                                    'element' => array('value' => 'coaAccumulated',
                                                                                                       'key' => 'hidCOAAccumulatedKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    )  
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div>    
                                   
                            </div>   
                  </div>
             </div>
        </div>      
          
 	   <div style="clear:both"></div>
        <div class="form-button-panel" >  
       	 <?php echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>