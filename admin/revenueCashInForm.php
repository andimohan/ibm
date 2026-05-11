<?php 

require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass(array('RevenueCashIn.class.php'));
$revenueCashIn = new RevenueCashIn();
$chartOfAccount = new ChartOfAccount();

$obj= $revenueCashIn;
$securityObject = $obj->securityObject;

if(!$security->isAdminLogin($securityObject,10,true));

$formAction = 'revenueCashInList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsItemDescription = array();  

$rs = prepareOnLoadData($obj); 

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
  
	$_POST['name'] = $rs[0]['name'];    
  
    $_POST['hidCOAHeaderKey'] = $rs[0]['coakey'] ;
    if (!empty($rs[0]['coakey'])){
        $rsCOAHeader = $chartOfAccount->getDataRowById($rs[0]['coakey']);          
		$_POST['COAHeaderName'] = $rsCOAHeader[0]['code'].' - '.$rsCOAHeader[0]['name'] ;
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
           
		// BOOTSTRAP VALIDATOR
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
                            message: phpErrorMsg.name[1]
                        },  
                    }
                }, 
				 
                COAHeaderName: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.coa[1]
                        }, 
                    }
				},
                 
            }
        })
        .on('success.form.bv', function(e) {   
                 <?php echo $obj->submitFormScript(); ?>
        }); 
         
        <?php if (!isset($rsItemDescription) || empty($rsItemDescription)) {  ?> 
            addNewTemplateRow("item-description-row-template");  
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['revenueName']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('name'); ?>
                                        </div> 
                                    </div>
        
                                 <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['account']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php 
                                                $popupOpt =  (!$isQuickAdd) ? array(
                                                    'url' => 'chartOfAccountForm.php',
                                                    'element' => array('value' => 'COAHeaderName',
                                                           'key' => 'hidCOAHeaderKey'),
                                                    'width' => '600px',
                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['chartOfAccount'])
                                                )  : ''; 
 
                                            echo  $obj->inputAutoComplete( array(
                                                                        'objRefer' => $chartOfAccount,
                                                                        'revalidateField' => true, 
                                                                        'element' => array('value' => 'COAHeaderName',
                                                                                           'key' => 'hidCOAHeaderKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-coa.php',
                                                                                            'data' => array(  'action' =>'searchData'  )
                                                                                        ) ,
                                                                        'popupForm' => $popupOpt
                                                            ));
                                            ?>
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