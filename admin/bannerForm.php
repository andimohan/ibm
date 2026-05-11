<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Banner.class.php'));

$banner = createObjAndAddToCol(new Banner());

$obj= $banner;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'bannerList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
  
$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
     

	$_POST['orderList'] =  $obj->formatNumber($rs[0]['orderlist']);

 	$rsItemImage = array(); 
		
	if( !empty($rs[0]['file'])){
		$rsItemImage[0]['file'] =  $rs[0]['file'];
//	    $rsItemImage[0]['phpthumbhash'] = getPHPThumbHash($rsItemImage[0]['file']);
        
        $_POST['txtDetail'] = $obj->HTMLSpecialCharacterForEditor($_POST['txtDetail']);

		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	} 
    
} 



$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status'); 
$arrPosition = $obj->convertForCombobox($obj->getAllPosition(),'pkey','namewithsize'); 
 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 

	        
    jQuery(document).ready(function() { 

            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id'; ?>;
            
                
            var opt = {}; 
            opt.fileFolder = "<?php echo $obj->uploadFolder; ?>";
            opt.fileUploaderTarget = "item-file-uploader";
            opt.arrFile = Array();

            <?php
            if (isset($id) && !empty($id)) {
                for ($i = 0; $i < count($rsItemImage); $i++) {
                    echo 'opt.arrFile.push("' . $rsItemImage[$i]['file'] . '"); ';
                }
            } ?>
 
            var banner = new Banner(tabID,opt);

            prepareHandler(banner);

            var fieldValidation = {
                name: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.banner[1]
                        }, 
                    }
                },  
				
				code: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        }, 
                    }
				},
				
				url: {
					validators: {
						uri: {
							message: phpErrorMsg.url[3]
						}
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
            };

            setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>);


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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['title']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('name'); ?> 
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['position']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selPosition', $arrPosition); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['orderList']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputNumber('orderList'); ?>
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['url']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('url', array('etc' => 'placeholder="http://"')); ?>  
                                        </div> 
                                     </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['image']); ?></label> 
                                        <div class="col-xs-9"> 
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
                                
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['loop']); ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php echo $obj->inputCheckBox('chkIsLoop'); ?>
                                        </div> 
                                     </div>
                            </div>   
                  </div>
                  <div class="div-table-col">  
                  		   	<div class="div-tab-panel">    
                                   <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div> 
                                     <div class="form-group">
                                         <div class="col-xs-12">  
                                         <?php echo  $obj->inputEditor('txtDetail',array('multilang' => true )); ?> 
                                         </div>     
                                     </div> 
                            </div>   
                  </div> 
             </div>
        </div>      
       
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>