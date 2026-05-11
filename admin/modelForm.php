<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Model.class.php'));
$model = createObjAndAddToCol( new Model()); 

$obj= $model;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'modelList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editModelCriteria= '';

$rs = prepareOnLoadData($obj);

$rsItemImage = array();

if (!empty($_GET['id'])){ 
    $id = $_GET['id']; 

	$_POST['orderList'] = $obj->formatNumber($rs[0]['orderlist']);

    $arrChild  = $obj->getChildren($rs[0]['pkey']);
	array_push($arrChild, $rs[0]['pkey']);
	if (!empty($arrChild)) 
		$editModelCriteria = ' and '.$obj->tableName.'.pkey not in ('.implode(",",$arrChild).')'; 
	
	$rsItemImage = array(); 
		
	if( !empty($rs[0]['file'])){
		$rsItemImage[0]['file'] =  $rs[0]['file'];
        $rsItemImage[0]['phpthumbhash'] = getPHPThumbHash($rsItemImage[0]['file']);
	
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	}
	
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrModel = $obj->searchData($obj->tableName.'.statuskey',1,true,$editModelCriteria );
$temp = count($arrModel);
$arrModel[$temp]['name'] = 'ROOT';
$arrModel[$temp]['pkey'] = 0;

$arrModel = $obj->convertForCombobox($arrModel,'pkey','name');  

    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  

<script type="text/javascript"> 

	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
    
        var imageUpload = new Array;
		imageUpload.push({folder:"<?php echo $obj->uploadFolder; ?>",
                            rsImage : <?php echo json_encode($rsItemImage); ?>, 
                            imageUploaderTarget : "model-image-uploader"
                        });    

        
        var model = new Model(tabID,imageUpload);
    
        prepareHandler(model);  
        
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
                                                message: phpErrorMsg.model[1]
                                            }, 
                                        }
                                    },  

                                    orderList: { 
                                        validators: { 
                                             regexp: {
                                                regexp: /^[0-9]+$/,
                                                message:  phpErrorMsg.orderList[2]
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
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['parent']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputSelect('selModel',$arrModel); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $obj->inputText('name'); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['order']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputNumber('orderList'); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shortDescription']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('trShortDesc',array('etc' => 'style="height:10em;"' )); ?>
                                </div> 
                            </div>  
                        
                            <div class="form-group">
                                <label class="col-xs-3 control-label" style="padding-top:0"> <?php echo ucwords($obj->lang['image']); ?></label> 
                                <div class="col-xs-9"> 
                                   <!-- image uploader --> 
                                <div class="item-image-uploader model-image-uploader">
                                    <ul class="image-list" ></ul>
                                    <div style="clear:both; height:1em; "></div>
                                    <div class="file-uploader">	
                                        <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                    </div>
                                  </div>  
                                <!-- image uploader --> 
                                </div>   
                            </div>
                            
                    </div>
                
                </div>
                
                <?php if (!$isQuickAdd){ ?> 
                    <div class="div-table-col">   
                        
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div>
                            <div class="form-group"> 
                                <div class="col-xs-12"> 
                                    <?php echo  $obj->inputEditor('txtDescription', array('overwritePost' => $overwrite)); ?>  
                                </div> 
                            </div>   
                        </div>

                    </div> 
                <?php } ?>  
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
