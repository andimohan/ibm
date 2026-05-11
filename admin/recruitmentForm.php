<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Recruitment.class.php','JobOpportunities.class.php'));

$recruitment = createObjAndAddToCol( new Recruitment()); 
$jobOpportunities = createObjAndAddToCol( new JobOpportunities()); 

$obj = $recruitment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'recruitmentList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
  
$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

    $rsItemFile = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['name'] = $rs[0]['name'];
    $_POST['email'] = $rs[0]['email'];
    $_POST['phone'] = $rs[0]['phone'];
    $_POST['address'] = $rs[0]['address'];
    $_POST['hidJobKey'] = $rs[0]['jobkey']; 
    
    if (!empty($rs[0]['jobkey'])){
		$rsJob = $jobOpportunities->getDataRowById($rs[0]['jobkey']);
		$_POST['jobName'] = $rsJob[0]['title'];
	}

    $_POST['description'] = $rs[0]['description'];
      //update file 
	$rsItemFile = $obj->getItemFile($id);
		
	if(count($rsItemFile) > 0){
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
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
        
        var recruitment = new Recruitment(tabID,"<?php echo $obj->uploadFileFolder; ?>",<?php echo json_encode($rsItemFile); ?>);

         prepareHandler(recruitment);
 
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
                                    email: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.email[1]
                                            }, 
                                            emailAddress: {
                                                message: phpErrorMsg.email[3]
                                            },
                                        }
                                    }, 
                                    phone: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.phone[1]
                                            },
                                        }
                                    }, 
                                    address: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.address[1]
                                            }, 
                                            
                                        }
                                    }, 
            
                                    jobName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.jobOpportunities[1]
                                            }, 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('email'); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('phone'); ?>
                                        </div> 
                                    </div> 
                                     <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobOpportunities']); ?></label> 
                                             <div class="col-xs-9">  
                                               <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $jobOpportunities,
                                                                                    'revalidateField' => true, 
                                                                                    'element' => array('value' => 'jobName',
                                                                                                       'key' => 'hidJobKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-job-opportunities.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'statuskey' => '(1)' )
                                                                                                    ) 
                                                                                  )
                                                                            );  
                                                ?> 
                                            </div> 
                                        </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputTextArea('address', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div> 
                             </div>
                       <div class="div-tab-panel"> 
                                        <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['file']); ?></div>
                                        <div class="form-group">
                                        <div class="col-xs-12">  
                                            <div class="item-file-uploader">
                                                <ul class="file-list" ></ul>
                                                <div style="clear:both; height:1em; "></div>
                                                <div class="file-uploader">	
                                                    <noscript><p>Please enable JavaScript to use file uploader.</p></noscript> 
                                                </div>
                                              </div>   
                                        </div> 
                                    </div>  
                                </div>
                         
                    </div>     
                 <div class="div-table-col">
                     <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div>
                            <div class="form-group">
                                        <div class="col-xs-12"> 
                                        <?php echo  $obj->inputTextArea('description', array('etc' => 'style="height:10em;"')); ?>                                         </div> 
                            </div>
                     </div>
                </div>
            </div>
      </div> 
      <!--
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton();?>
        </div> 
        -->
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
