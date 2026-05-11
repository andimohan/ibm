<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Category.class.php','EventCategory.class.php','Event.class.php'));

$event = new Event();
$eventCategory = createObjAndAddToCol( new EventCategory()); 

$obj= $event;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    

$formAction = 'eventList';

$_POST['txtEventDateFrom'] = date('d / m / Y H:i'); 
$_POST['txtEventDateTo'] = date('d / m / Y H:i'); 

$rs = prepareOnLoadData($obj);

$rsItemImage = array(); 

if (!empty($_GET['id'])){ 
 	 
	$id = $_GET['id'];
    
	$_POST['txtEventDateFrom'] = $obj->formatDBDate($rs[0]['eventdatefrom'],'d / m / Y H:i');
	$_POST['txtEventDateTo'] = $obj->formatDBDate($rs[0]['eventdatefrom'],'d / m / Y H:i');
	
    $_POST['hidCategoryKey'] = $rs[0]['categorykey'];  
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $eventCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $eventCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
	}
     
    $rsItemImage = $obj->getItemImages($id); 
    
    if(count($rsItemImage) > 0){
        $sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
        $destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
        $obj->deleteAll($destinationPath); 

        if(!is_dir($destinationPath)) 
            mkdir ($destinationPath,  0755, true);

        $obj->fullCopy($sourcePath,$destinationPath);  
    }

    foreach($rsItemImage as $key=>$row) 
        $rsItemImage[$key]['phpthumbhash'] = getPHPThumbHash($row['file']);
    
    
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

            var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id'; ?>;
            var event = new Event(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?>);

            prepareHandler(event);

            var fieldValidation = {
                code: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        },
                    }
                },
                title: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.title[1]
                        },
                    }
                } 
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
                    <div class="div-table-col"  style="width:49%; text-align:center">
                        <div class="div-tab-panel">  
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                      
							<div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus);  ?>
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
                                    <?php echo $obj->inputText('title',array('multilang' => true )); ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
                                <div class="col-xs-9"> 
                                    <div class="flex"> 
                                        <div class="consume"><?php echo $obj->inputDateTime('txtEventDateFrom',array( 'etc' => 'style="text-align:center"')); ?></div>  
                                        <div class="consume"><?php echo $obj->inputDateTime('txtEventDateTo',array(  'etc' => 'style="text-align:center"')); ?></div>  
                                    </div> 
                                </div> 
                           </div>
                            
<!--
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputDateTime('txtEventDateFrom'); ?>
                                </div>
                            </div> 
-->
                            
                             <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array(  
                                                                                    'element' => array('value' => 'categoryName',
                                                                                                       'key' => 'hidCategoryKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-event-category.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    )  
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['host']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputText('hostName',array('multilang' => true )); ?>
                                </div>
                            </div>  
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['featured']); ?></label>
                                <div class="col-xs-9">
                                    <?php echo $obj->inputCheckBox('chkIsFeatured'); ?>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shortDescription']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputTextArea('txtShortDescription', array( 'etc' => 'style="height:8em;"', 'multilang' => true )); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['image']); ?></label> 
                                    <div class="col-xs-9"> 
                                         <!-- image uploader --> 
                                        <div class="item-image-uploader">
                                            <ul class="image-list " ></ul>
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
                    <div class="div-table-col"  style="width:49%; text-align:center">
                        <div class="div-tab-panel transaction-detail" style="margin-bottom:3em; "> 
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div>
                             <div class="form-group"> 
                                <div class="col-xs-12"  style="margin-top:1em">  
                                    <?php echo  $obj->inputEditor('txtDescription', array('overwritePost' => $overwrite, 'multilang' => true)); ?>  
                                </div>  
                            </div>   
                        </div>
                        <div class="div-tab-panel transaction-detail" style="margin-bottom:3em; "> 
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['mechanism']); ?></div>
                             <div class="form-group"> 
                                <div class="col-xs-12"  style="margin-top:1em">  
                                    <?php echo  $obj->inputEditor('txtMechanism', array('overwritePost' => $overwrite, 'multilang' => true)); ?>  
                                </div>  
                            </div>   
                        </div>
                         <div class="div-tab-panel transaction-detail" style="margin-bottom:3em; "> 
                        <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['termsAndConditions']); ?></div>
                             <div class="form-group"> 
                                <div class="col-xs-12"  style="margin-top:1em">  
                                    <?php echo  $obj->inputEditor('txtTermsAndConditions', array('overwritePost' => $overwrite, 'multilang' => true)); ?>  
                                </div>  
                            </div>   
                        </div>
                    </div>
                  </div>
         </div>  
 	                       
 	 <div class="form-button-panel">
                <?php echo $obj->generateSaveButton(); ?>
            </div>
	  
    </form>  
        <?php echo $obj->showDataHistory(); ?>
 
</div> 
</body>

</html>
