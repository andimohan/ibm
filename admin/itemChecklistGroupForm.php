<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $itemChecklistGroup;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'itemChecklistGroupList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$rsDetail = array();

$rs = prepareOnLoadData($obj);   

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsDetail = $obj->getDetailById($id);
	$_POST['name'] = $rs[0]['name']; 
	$_POST['trDesc'] = $rs[0]['trdesc']; 
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
			   name: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.itemChecklistGroup[1]
                        }, 
                    }
				},
            }
        })
        .on('success.form.bv', function(e) { 
               <?php echo $obj->submitFormScript(); ?> 
        });
		
		   
		objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'}); 
        objAndValueForDetailAutoComplete[tabID] = objAndValue;
	  	 	
	 	// DETAIL CLONE
		 $("#"+tabID+"  [name=btnAddRows]").on('click', function() { 
          	addNewTemplateRow("detail-row-template");
			bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item-checklist.php?action=searchData'); 
        });
		 
        <?php if (empty($_GET['id'])){ ?> 
         	addNewTemplateRow("detail-row-template");
        <?php } ?>
        bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item-checklist.php?action=searchData');
 

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
                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                            <div class="col-xs-9"> 
                                 <?php echo $obj->inputText('name'); ?> 
                            </div> 
                        </div>  
                    </div>
                </div>
                
                <div class="div-table-col">   
                    <div class="div-tab-panel"> 
                        <div class="div-table-caption border-blue">Description</div>
                        <div class="form-group"> 
                            <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                            </div> 
                        </div>   
                    </div>
                </div>
                
            </div>
      </div>   
       
        <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['itemChecklist']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div> 
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>" style="width:45px"></div>   
                </div>
                
				<?php 
                            
                    $totalRows = count($rsDetail);
                    for ($i=0;$i<=$totalRows; $i++){  
                                
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $etc = '';
                        
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $etc = 'disabled="disabled"'; 
                        } else {  
                            $rsitemChk = $itemChecklist->getDataRowById($rsDetail[$i]['itemkey']);
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['hidItemKey[]'] =  $rsitemChk[0]['pkey']; 
                            $_POST['itemName[]'] =  $rsitemChk[0]['name']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']);
                        }
                    ?>
            
                 <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]',array('overwritePost' => $overwrite,'value' => 1, 'etc' => 'style="text-align:right" '.$etc)); ?></div> 
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?></div>
                </div>
                         
                <?php  } ?>   
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows']); ?></div>
      
        <div>   
            <div class="<?php echo $obj->hideOnDisabled(); ?>" style="float:right; width:45px; height: 1em"></div>  
             
        </div>          
      
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>