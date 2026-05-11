<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $changeItemSN;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'changeItemSNList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);   

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
	$_POST['trDesc'] = $rs[0]['trdesc']; 
    $_POST['hidItemKey'] = $rs[0]['itemkey'] ;
	$_POST['serialNumber'] = $rs[0]['serialnumber'];  
	$_POST['newSerialNumber'] = $rs[0]['newserialnumber'];  
	$_POST['hidVendorPartNumberKey'] = $rs[0]['vendorpartnumberkey'] ; 
    
    $rsItem = $obj->searchSN($rs[0]['serialnumber']);
    if(!empty($rsItem)){  
       $_POST['vendorPartNumber'] = $rsItem[0]['partnumber'] ;
       $_POST['itemName'] = $rsItem[0]['itemname'] ;
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
	 	    
        var tabID = selectedTab.newPanel[0].id;
//        var varConstant = {  CLAIM_TYPE : <?php echo json_encode(CLAIM_TYPE); ?> };
        
         var changeItemSN = new ChangeItemSN(tabID); 
         prepareHandler(changeItemSN); 
		 

         var fieldValidation =  { 
            code: { 
                validators: {
                    notEmpty: {
                        message: phpErrorMsg.code[1]
                    }, 
                }
            },
            serialNumber: { 
                validators: {
                    notEmpty: {
                        message: phpErrorMsg.serialnumber[1]
                    }, 
                }
            },
            newSerialNumber: { 
                validators: {
                    notEmpty: {
                        message: phpErrorMsg.serialnumber[1]
                    }, 
                }
            },
        };
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
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
                                <label class="col-xs-3 control-label" ><?php echo ucwords($obj->lang['serialNumber']); ?></label> 
                                <div class="col-xs-9"> 
                                        <?php echo $obj->inputText('serialNumber'); ?> 
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['vendorPartNumber']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('vendorPartNumber', array('readonly' => true)); ?> 
                                    <?php echo $obj->inputHidden('hidVendorPartNumberKey', array('readonly' => true)); ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemName']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('itemName', array('readonly' => true)); ?> 
                                     <?php echo $obj->inputHidden('hidItemKey', array('readonly' => true)); ?> 
                                </div> 
                            </div>                            
                            <div class="form-group isSNReplace">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['newSerialNumber']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php echo $obj->inputText('newSerialNumber'); ?> 
                                </div> 
                            </div> 
                           

                           
                    </div>  
                </div> 
                <div class="div-table-col">
                    <div class="div-tab-panel"> 
                         <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['description']); ?></div>
                    <div class="form-group"> 
                            <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                            </div> 
                        </div> 
                    </div>
                </div> 
            </div>
      </div>       
        <div class="form-button-panel" > 
         <?php  echo $obj->generateSaveButton(array(),true); ?>  
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
