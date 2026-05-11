<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass("FAQ.class.php");
$faq = new FAQ();

$obj = $faq;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'faqList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
  
$rsDetail = array();
    
$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
    $_POST['answer'] = $obj->HTMLSpecialCharacterForEditor($_POST['answer']);
 
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
        
         var faq = new FAQ(tabID);
    
         prepareHandler(faq);

        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 
                                 question: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.question[1]
                                            }, 
                                        }
                                    }, 
                                answer: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.answer[1]
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
      
       <div class="div-table main-tab-table-1" style="width:100%">
            <div class="div-table-row">
                <div class="div-table-col"> 
      						 <div class="div-tab-panel"> 

                                    <div class="form-group">
                                        <label class="col-xs-2 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                        <div class="col-xs-10"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                        </div> 
                                    </div>  
      
                                    <div class="form-group">
                                        <label class="col-xs-2 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-10"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                    </div>   
                                     <div class="form-group">
                                        <label class="col-xs-2 control-label"><?php echo ucwords($obj->lang['question']); ?></label> 
                                        <div class="col-xs-10"> 
                                            <?php echo $obj->inputText('question'); ?>
                                        </div> 
                                    </div>
                                     <div class="form-group">
                                        <label class="col-xs-2 control-label"><?php echo ucwords($obj->lang['answer']); ?></label> 
                                        <div class="col-xs-10"> 
                                              <?php echo  $obj->inputEditor('answer'); ?> 
                                        </div> 
                                    </div> 
                                  <div class="form-group">
                                        <label class="col-xs-2 control-label"><?php echo ucwords($obj->lang['orderList']); ?></label> 
                                        <div class="col-xs-10"> 
                                            <?php echo $obj->inputNumber('orderList'); ?>
                                        </div> 
                                    </div> 
                             </div>
                         
                    </div>     
            </div>
      </div> 
      
    
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton();?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
