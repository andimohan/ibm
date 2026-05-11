<?php 

include '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Chassis.class.php');
$chassis = createObjAndAddToCol(new Chassis()); 
$chassisCategory = createObjAndAddToCol(new ChassisCategory()); 
$warehouse = createObjAndAddToCol(new Warehouse()); 


$obj= $chassis;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'chassisList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
  
$editWarehouseInactiveCriteria = '';

$_POST['kirExpiryDate'] = date('d / m / Y'); 

$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
    $id = $_GET['id'];	  
    
    $_POST['kir'] = $rs[0]['kir']; 
    $_POST['kirExpiryDate'] = $obj->formatDBDate($rs[0]['kirexpirydate'],'d / m / Y'); 
    $_POST['chassisNumber'] = $rs[0]['chassisnumber']; 
    $_POST['sumbu'] = $rs[0]['sumbu']; 
    $_POST['color'] = $rs[0]['color']; 
    $_POST['selWarehouse'] = $rs[0]['warehousekey']; 
    	
    $_POST['hidCategoryKey'] = $rs[0]['categorykey']; 
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $chassisCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $chassisCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
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
<title></title>  

<script type="text/javascript"> 
	
	jQuery(document).ready(function(){  
		
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        setOnDocumentReady(tabID);
        
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                 code: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        }, 
                    }
                }, 
				
				categoryName: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.category[1]
                        },  
                    }
                }, 
                
				chassisNumber: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.chassis[1]
                        }, 
                    }
                },  				
            }
        })
        .on('success.form.bv', function(e) {
                 <?php echo $obj->submitFormScript(); ?>
        });
   
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
                                              <?php echo  $obj->inputSelect('selWarehouse',$arrWarehouse); ?> 
                                        </div> 
                                    </div>   
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['chassisNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('chassisNumber'); ?>
                                        </div> 
                                     </div>   
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['kirNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('kir'); ?>
                                        </div> 
                                     </div>  
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['kirExpiredDate']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputDate('kirExpiryDate'); ?>
                                        </div> 
                                     </div>   
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['axis']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('sumbu'); ?>
                                        </div> 
                                     </div>   
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['color']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('color'); ?>
                                        </div> 
                                     </div>  
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
                                                   <?php    
                                                            $popupOpt = (!$isQuickAdd) ? array(
                                                                            'url' => 'chassisCategoryForm.php',
                                                                            'element' => array('value' => 'categoryName',
                                                                                   'key' => 'hidCategoryKey'),
                                                                            'width' => '600px',
                                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['chassisCategory'])
                                                                        )  : '';
                                            
                                                            echo $obj->inputAutoComplete(array(
                                                                                            'objRefer' => $chassisCategory,
                                                                                            'revalidateField' => true, 
                                                                                            'element' => array('value' => 'categoryName',
                                                                                                               'key' => 'hidCategoryKey'),
                                                                                            'source' =>array(
                                                                                                                'url' => 'ajax-chassis-category.php',
                                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                                            ) ,
                                                                                            'popupForm' => $popupOpt
                                                                                          )
                                                                                    );  
                                                        ?> 
                                        </div> 
                                     </div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
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
   <?php echo $obj->showDataHistory(); ?> 
</div> 
</body>

</html>
