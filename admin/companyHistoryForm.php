<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('CompanyHistory.class.php'));
$obj = createObjAndAddToCol( new CompanyHistory());  

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'companyHistoryList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$arrPHPThumbHash = array();
$rs = prepareOnLoadData($obj);  
 
$arrImages = array();
array_push($arrImages, array('key' => 'image', 'uploadFolder' => $obj->uploadFolder,'uploaderTarget' => 'item-image-uploader'));
 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	   
    
    foreach($arrImages as $index=>$imgRow){
        $key = $imgRow['key'];
        $uploadFolder = $imgRow['uploadFolder'];
        
        $rsImage = array();
        
        if( !empty($rs[0][$key])){
             
            $rsImage[0]['file'] =  $rs[0][$key];
            $rsImage[0]['phpthumbhash'] = getPHPThumbHash($rsImage[0]['file']); 

            $sourcePath = $obj->defaultDocUploadPath.$uploadFolder.$id; 

            $destinationPath = $obj->uploadTempDoc.$uploadFolder.$id; 
            $obj->deleteAll($destinationPath); 

            if(!is_dir($destinationPath)) 
                mkdir ($destinationPath,  0755, true);

            $obj->fullCopy($sourcePath,$destinationPath); 
        }
        
        $arrImages[$index]['rsImage'] = $rsImage;

    }
	
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
        
    jQuery(document).ready(function(){  
        
       var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
             
        var companyHistory = new CompanyHistory(tabID, <?php echo json_encode($arrImages); ?>);
         prepareHandler(companyHistory);   
		 
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
                                                message: phpErrorMsg.name[1]
                                            },  
                                        }
                                    },
            
                                   orderList: {
                                        validators: { 
                                            greaterThan: {
                                                value: -1,
                                                inclusive: false,
                                                separator: ',', 
                                                message: phpErrorMsg.orderList[2]
                                            }
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
    <?php echo $obj->generateLangOptions(); ?>
      
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('name'); ?> 
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shortDescription']); ?></label> 
                                        <div class="col-xs-9"> 
                                                <?php echo  $obj->inputTextArea('shortDescription',array('etc' => 'style="height:10em;"','multilang' => true )); ?>
                                        </div> 
                                    </div>  
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['orderList']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputNumber('orderList'); ?>
                                        </div> 
                                     </div>
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['image']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <!-- image uploader --> 
                                            <div class="item-image-uploader">
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
                    
                    <div class="div-table-col"> 
      						 <div class="div-tab-panel"> 
                                 <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div> 
                                   
                                    <div class="form-group">
                                        <div class="col-xs-12"> 
                                              <div><?php echo $obj->inputEditor('txtDescription',array('multilang' => true)); ?></div> 
                                        </div> 
                                    </div> 
                                 
                             </div>
                         
                    </div>
           </div>
      </div>   
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton();?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
