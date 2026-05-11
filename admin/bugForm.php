<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 
 
$obj= $bug;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
     
$formAction = 'bugList';
$_POST['trDate'] = date('d / m / Y');

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){
    $id = $_GET['id']; 
    
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');  
    $_POST['txtSubject'] = $rs[0]['subject'];
    $_POST['txtMessage'] = $rs[0]['message'];
    
    $rsItemImage = $obj->getItemImage($id);

    if(count($rsItemImage) > 0){ 
        $sourcePath = $obj->defaultDocUploadPath.'../program-stok.com/'.$obj->uploadFolder.$id; 
        $destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id;
        
        $obj->deleteAll($destinationPath); 

        if(!is_dir($destinationPath)) 
            mkdir ($destinationPath,  0755, true);

        $obj->fullCopy($sourcePath,$destinationPath);  
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
                
				echo 'createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder, "token":'.$id.', "arrImage":arrImage,"phpThumbHash":arrPHPThumbHash},true,true);';  
       			
			}else{
				echo 'createImageUploader({"tabID":tabID, "name":imageUploaderTarget},{"folder":folder},true,true);'; 
			}
		?>
        
        
		$( "." + imageUploaderTarget + " .image-list ").sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemImageArray({"tabID":tabID, "name":imageUploaderTarget}); }});
		$( "." + imageUploaderTarget + " .image-list"  ).disableSelection();
	 	  
		 $('#defaultForm-' + tabID)
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
                            message:  phpErrorMsg.code['1']
                        }, 
                    }
				} 
				
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
                        <div class="div-tab-panel" >
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
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['date']; ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputDate('trDate'); ?>   
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['subject']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo $obj->inputText('txtSubject'); ?>
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['description']); ?></label> 
                                <div class="col-xs-9">  
                                    <?php echo  $obj->inputTextArea('txtMessage', array( 'etc' => 'style ="height:10em"'));?>  
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['image']); ?></label> 
                                <div class="col-xs-9">  
                                    <div class="item-image-uploader">
                                        <ul class="image-list"></ul>
                                        <div style="clear:both; height:1em;"></div>
                                        <div class="file-uploader">	
                                            <noscript>			
                                            <p>Please enable JavaScript to use file uploader.</p> 
                                            </noscript> 
                                        </div>
                                      </div>  
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