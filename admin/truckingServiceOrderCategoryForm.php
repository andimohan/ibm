<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('TruckingServiceOrderCategory.class.php'));
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();

$obj= $truckingServiceOrderCategory;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    

$formAction = 'truckingServiceOrderCategoryList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$editCategoryCriteria= '';

$rs = prepareOnLoadData($obj); 
$rsDetail = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	 
	$_POST['name'] = $rs[0]['name']; 
	$_POST['orderList'] = $obj->formatNumber($rs[0]['orderlist']); 
    
 	$arrChild  = $obj->getChildren($rs[0]['pkey']);
	array_push($arrChild, $rs[0]['pkey']);
	if (!empty($arrChild)) 
		$editCategoryCriteria = ' and '.$obj->tableName.'.pkey not in ('.implode(",",$arrChild).')';   
    
	$rsItemImage = array(); 
		
	if( !empty($rs[0]['file'])){
		$rsItemImage[0]['file'] =  $rs[0]['file'];
	
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	}
	 
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrCategory = $obj->searchData($obj->tableName.'.statuskey',1,true,$editCategoryCriteria );
$temp = count($arrCategory);
$arrCategory[$temp]['name'] = 'ROOT';
$arrCategory[$temp]['pkey'] = 0;

$arrCategory = $obj->convertForCombobox($arrCategory,'pkey','name');  
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
       
        /// FILE UPLOADER
        var folder = "<?php echo $obj->uploadFolder; ?>";
        var imageUploaderTarget = "item-image-uploader";
        var arrImage = Array();  
		var arrPHPThumbHash = Array(); 
        
		<?php   
			if (isset($id) && !empty($id)){ 
			
				for($i=0;$i<count($rsItemImage);$i++) {
                 	echo 'arrImage.push("'.$rsItemImage[$i]['file'].'"); '; 
                	echo 'arrPHPThumbHash.push("'.getPHPThumbHash($rsItemImage[$i]['file']).'"); '; 
                }
				echo 'createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder, "token":'.$id.', "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},false,false);';  
       
            }else{
				echo 'createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder}, false, false);';
			}
		?> 
		 
        // DESC FIELD CLONE 
		
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                name: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.category[1]
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
					
				orderList: { 
                    validators: { 
						 regexp: {
                            regexp: /^[0-9]+$/,
                            message:  phpErrorMsg.orderList[2]
                        }
                    }
				},
				
            }
        })
        .on('success.form.bv', function(e) {
              <?php echo $obj->submitFormScript(); ?>
        });
        
        
		objAndValueContainer = new Array;
		objAndValueContainer.push({object:'hidJobTypeKey[]', value :'pkey'});  
        objAndValueForDetailAutoComplete[tabID] = objAndValueContainer; 
        
         // DETAIL CLONE
		 $("#defaultForm-"+tabID+" [name=btnAddRows]").on('click', function() {
          	addNewTemplateRow("detail-row-template"); 
            bindAutoCompleteForTransactionDetail('jobTypeName[]',objAndValueForDetailAutoComplete[tabID],'ajax-trucking-job.php?action=searchData'); 
        });
           
        <?php if (empty($_GET['id'])){ ?> 
            addNewTemplateRow("detail-row-template");	
        <?php } ?>
 
        bindAutoCompleteForTransactionDetail('jobTypeName[]',objAndValueForDetailAutoComplete[tabID],'ajax-trucking-job.php?action=searchData'); 
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
                            <div class="div-table-caption border-orange"><?php echo $obj->lang['generalInformation']; ?></div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['status']; ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputSelect('selStatus', $arrStatus); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['code']; ?></label> 
                                <div class="col-xs-9"> 
                                        <?php echo $obj->inputAutoCode('code'); ?>
                                </div> 
                            </div>     
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['category']; ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $obj->inputText('name'); ?>
                                </div> 
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['image']; ?></label> 
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
                    <div class="div-tab-panel transaction-detail"> 
                    <div class="div-table-caption border-green"><?php echo $obj->lang['jobType']; ?></div>  
                    <div class="div-table" style="width:100%">
                        	<?php 
                    
                                $totalRows = count($rsDetail);
                                for ($i=0;$i<=count($rsDetail); $i++){   

                                    $class =  'transaction-detail-row';
                                    $overwrite = true;
                                    $etc = '';

                                    if ($i == $totalRows ){
                                        $class = 'detail-row-template';
                                        $overwrite = false;
                                        $etc = 'disabled="disabled"'; 
                                    } else {
                                        $_POST['hidJobTypeKey[]'] =  $rsDetail[$i]['jobtypekey']; 
                                        $_POST['jobTypeName[]'] =  $rsDetail[$i]['jobtypename'];     
                                    }
                            ?>


                            <div class="div-table-row  <?php echo $class; ?>">
                                <div class="div-table-col">
                                    <?php  
                                        echo $obj->inputText('jobTypeName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); 
                                        echo $obj->inputHidden('hidJobTypeKey[]',array('overwritePost' => $overwrite, 'etc' => $etc));   
                                    ?>
                                </div>  
                                <div class="div-table-col" style="width:45px">
                                    <?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1" ', 'class' => 'btn btn-link remove-button')); ?>
                                </div>
                           </div> 

                        <?php } ?> 
  
                    </div>
                        
                  <div style="margin-top:1em"> <?php echo $obj->inputButton('btnAddRows', ucwords($obj->lang['addRows']), array('class' => 'btn btn-primary btn-second-tone')); ?> </div> 

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