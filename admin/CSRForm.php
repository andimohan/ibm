<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('CSR.class.php','CSRCategory.class.php'));
$CSR = createObjAndAddToCol( new CSR()); 
$CSRCategory = createObjAndAddToCol( new CSRCategory()); 

$obj= $CSR;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'CSRList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
  
$_POST['publishDate'] = date('d / m / Y');
 
$rs = prepareOnLoadData($obj);

$rsFile = array();
$rsItemImage = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
  
	$_POST['title'] = $rs[0]['title']; 
    	
    $_POST['hidCategoryKey'] = $rs[0]['categorykey'];  
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $CSRCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $CSRCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
	}
    
	$_POST['publishDate'] = $obj->formatDBDate($rs[0]['publishdate']);
	$_POST['txtShortDescription'] = $rs[0]['shortdesc'];
	$_POST['txtDetail'] = $rs[0]['detail']; 

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
//	  
//    $rsFile = array();  
//	if( !empty($rs[0]['file'])){
//		$rsFile[0]['file'] =  $rs[0]['file'];
//	
//		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
//		$destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
//		$obj->deleteAll($destinationPath); 
//	
//		if(!is_dir($destinationPath)) 
//			mkdir ($destinationPath,  0755, true);
//				
//		$obj->fullCopy($sourcePath,$destinationPath); 
//	}
} 
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 


<script type="text/javascript"> 
 jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
              
        var CSRObj = new CSR(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsItemImage); ?> );
    
        prepareHandler(CSRObj);   
        
         var fieldValidation =  {
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['title']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('title',array('multilang' => true )); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array(  
                                                                                    'element' => array('value' => 'categoryName',
                                                                                                       'key' => 'hidCategoryKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-csr-category.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    ) , 
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['publishDate']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputDate('publishDate'); ?>  
                                        </div> 
                                    </div> 
<!--
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['featuredArticle']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputCheckBox('isFeatured'); ?>   
                                        </div> 
                                    </div>
-->
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
                                
                                
<!--
                                    <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['file']); ?></label> 
                                    <div class="col-xs-9">  
                                        <div class="item-file-uploader">
                                            <ul class="file-list " ></ul>
                                            <div style="clear:both; height:1em; "></div>
                                            <div class="file-uploader">	
                                                <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                            </div>
                                          </div>   
                                    </div> 
                                    </div> 
-->
                            </div> 
                    		<div class="div-tab-panel">   
                                  <div class="div-table-caption border-red">Meta</div>
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['title']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('metaTitle'); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['tag']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('metaTag'); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['description']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('metaDescription'); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['keyword']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('metaKeyword'); ?>
                                        </div> 
                                    </div>  
                             </div>
                    </div>
                     
                    <div class="div-table-col">  
                                        <div class="div-tab-panel"> 
                                <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['shortDescription']); ?></div>
                                            <div class="form-group">
                                             <div class="col-xs-12"> 
                                                <?php echo  $obj->inputTextArea('txtShortDescription', array('multilang' => true ,'etc' => 'style="height:10em;"')); ?> 
                                             </div>
                                            </div>
                                        </div>
                    </div>    
               </div>
         </div>  
    <div class="div-table main-tab-table-1" style="width:100%;">
              <div class="div-table-row">
                    <div class="div-table-col">  
                         <div class="div-tab-panel">  
                             <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['newsContent']); ?></div>
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