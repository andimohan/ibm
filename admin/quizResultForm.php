<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass("QuizResult.class.php");
$quizResult = new QuizResult();

$obj = $quizResult;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'quizResultist';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
  
$rsDetail = array();
    
$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
//	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
//    $_POST['quizName'] = $rs[0]['quizname'];
    if(!empty($rs[0]['refkey'])){
        
        $rsQuiz = $quiz->getDataRowById($rs[0]['refkey']);
        $_POST['quizName'] = $rsQuiz[0]['name'];

    }
    
    $_POST['name'] = $rs[0]['name'];
    $_POST['email'] = $rs[0]['email'];
    $_POST['phone'] = $rs[0]['phone'];
    $_POST['rightAnswer'] = $rs[0]['rightanswer'];
    $_POST['wrongAnswer'] = $rs[0]['wronganswer'];
//    $_POST['description'] = $rs[0]['description'];
//	$_POST['selWarehouseKey'] = $rs[0]['warehousekey'];

//	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
 
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        
         var quizResult = new QuizResult(tabID);
    
         prepareHandler(quizResult);

        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 

//                                   customerName: { 
//                                        validators: {
//                                            notEmpty: {
//                                                message:  phpErrorMsg.customer[1]
//                                            }
//                                        } 
//                                    },
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
      
<!--
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                    </div>   
-->
<!--
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>
-->
<!--
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div>    
-->
                            
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['quiz']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('quizName', array('readonly' => true)); ?>
                                        </div> 
                                    </div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('name', array('readonly' => true)); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('email', array('readonly' => true)); ?>
                                        </div> 
                                    </div> 
                                      <div class="form-group">
                                            <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                            <div class="col-xs-9"> 
                                                <?php echo $obj->inputText('phone', array('readonly' => true)); ?>
                                            </div> 
                                        </div> 
<!--
                                     <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php /*echo ucwords($obj->lang['quiz']);*/ ?></label> 
                                             <div class="col-xs-9">  
                                               <?php    
                                          /*      echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $quiz,
                                                                                    'revalidateField' => true, 
                                                                                    'element' => array('value' => 'quizName',
                                                                                                       'key' => 'hidQuizKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-customer.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    ) 
                                                                                  )
                                                                            ); */ 
                                                ?> 
                                            </div> 
                                        </div>
-->
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['rightAnswer']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('rightAnswer', array('readonly' => true)); ?>
                                        </div> 
                                    </div> 
                                  <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['wrongAnswer']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('wrongAnswer', array('readonly' => true)); ?>
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
      
    
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  /*echo $obj->generateSaveButton();*/?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
