<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 
 
$obj =  $projects;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'projectsList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$rsParticipant = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['dueDate'] = date('d / m / Y');
$editEmployeeInactiveCriteria ='';
//$arrParty = array();

$rs = prepareOnLoadData($obj);   

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	//$rsTruckingCost = $obj->getDetailWithRelatedInformation($id); 
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['trDesc'] = $rs[0]['trdesc'];
     
    $_POST['hidCustomerKey'] = $rs[0]['customerkey']; 
	if (!empty($rs[0]['customerkey'])){
		$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
		$_POST['customerName'] = $rsCustomer[0]['name'];
	}  
    
    $editEmployeeInactiveCriteria = ' or  '.$employee->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	
  
} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrParty = $class->convertForCombobox($employee->searchData('','',true,' and ('.$employee->tableName.'.statuskey = 2' .$editEmployeeInactiveCriteria.')'),'pkey','name');  



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
	jQuery(document).ready(function(){  
	 	    
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        //projects = new Projects(tabID);
 
        setOnDocumentReady(tabID);  
        
        /// FILE UPLOADER 
		var fileFolder = "<?php echo $obj->uploadFileFolder; ?>"; 
		var fileUploaderTarget = "item-file-uploader"; 
		var arrFile = Array();
		   
        <?php   
			if (isset($id) && !empty($id)){   
         		for($i=0;$i<count($rsItemFile);$i++) 
					echo 'arrFile.push("'.$rsItemFile[$i]['file'].'"); '; 
					
				echo 'createFileUploader(fileUploaderTarget,fileFolder,'.$id.',arrFile,true);';  
				
			}else{ 
				echo 'createFileUploader(fileUploaderTarget,fileFolder,"","",true);'; 
			}
		?>
          
		$( "." + fileUploaderTarget + " .file-list" ).sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemFileArray(fileUploaderTarget); }});
		$( "." + fileUploaderTarget + " .file-list"  ).disableSelection();
		 
        
		 
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
            }
        })
        .on('success.form.bv', function(e) { 
               <?php echo $obj->submitFormScript(); ?> 
        });
		$("#" + tabID + " .multi-selectbox").searchableOptionList({  maxHeight: '250px',  showSelectAll: true, showSelectionBelowList: true  });
	  	 /*	
	
	 	// DETAIL CLONE 
		 $("#"+tabID+"  [name=btnAddRows]").on('click', function() { 
          	addNewTemplateRow("detail-row-template");
			bindAutoCompleteForTransactionDetail('costName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1'); 
        });
        
        <?php if (empty($_GET['id'])){ ?> 
         	addNewTemplateRow("detail-row-template");
        <?php } ?>
        bindAutoCompleteForTransactionDetail('costName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1');
        */
        <?php if (empty($_GET['id'])){ ?> 
         	//addNewTemplateRow("detail-row-template");
        <?php } ?>
       
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
                        <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                            </div> 
                        </div>  
                        <!--<div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputAutoCode('code'); ?>
                            </div> 
                        </div>-->   
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('name'); ?>
                            </div> 
                        </div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputDate('trDate'); ?> 
                            </div> 
                        </div>   
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['customer']; ?></label> 
                            <div class="col-xs-9"> 
                                          <?php     
                                               echo $obj->inputAutoComplete(array( 
                
                                                                                        'revalidateField' => true, 
                                                                                        'element' => array('value' => 'customerName',
                                                                                                           'key' => 'hidCustomerKey'),
                                                                                        'source' => array(
                                                                                                            'url' => 'ajax-customer.php',
                                                                                                            'data' => array(  'action' =>'searchData')
                                                                                                        )  
                                                                                      )
                                                                                );  
                                            ?> 
                                  
                                
                            </div>  
                        </div> 
                        <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['projectsInformation']); ?></div>
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['duedate']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputDate('dueDate[]'); ?> 
                            </div> 
                        </div> 
                        <div class="form-group">
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['urgent']); ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo $obj->inputCheckBox('isStick[]'); ?> 
                            </div> 
                        </div>  
                        <div class="form-group"> 
                            <label class="col-xs-3 control-label"><?php echo $obj->lang['description']; ?></label> 
                            <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('detailDesc[]', array('etc' => 'style="height:6em;"')); ?>
                            </div> 
                        </div> 
                        <div class="div-tab-panel">  
                             <div class="div-table" style="width:100%"> 
                                <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div> 
                                 <div class="div-table-row"> 
                                    <div class="div-table-col-5">
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
                              </div>     
                         </div> 
                             
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['participants']); ?></div>
                            <div class="col-xs-12"> <?php echo  $obj->inputSelect('selParticipants[]', $arrParty, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox') ); ?></div>
                         </div>
                                  
                    </div>
                </div>
                
                <!--<div class="div-table-col">   
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                        <div class="form-group"> 
                            <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                            </div> 
                        </div>   
                    </div>
                </div>-->
                
            </div>
      </div>             
      
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
