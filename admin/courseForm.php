<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $course;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'courseList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
  
$rsDetail = array();
    
$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    $obj->setLog($rsDetail,true);
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['name'] = $rs[0]['name'];
    $_POST['hidCategoryKey'] = $rs[0]['categorykey']; 
    if (!empty($rs[0]['categorykey'])){
		$rsCategory = $courseCategory->getDataRowById($rs[0]['categorykey']);
        $categoryName =  $courseCategory->getPath($rsCategory[0]['pkey']);
		$_POST['categoryName'] = $categoryName[0]['path'];
	}

    $_POST['description'] = $rs[0]['description'];
	$_POST['selWarehouseKey'] = $rs[0]['warehousekey'];
	$_POST['orderList'] = $rs[0]['orderlist'];
  
        
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
    
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
        
         var course = new Course(tabID);
    
         prepareHandler(course);

        
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
                                                message:  phpErrorMsg.name[1]
                                            }
                                        } 
                                    },
                                    categoryName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.category[1]
                                            }
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
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
 <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                             <div class="col-xs-9">  
                                               <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $courseCategory,
                                                                                    'revalidateField' => true, 
                                                                                    'element' => array('value' => 'categoryName',
                                                                                                       'key' => 'hidCategoryKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-course-category.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    ) 
                                                                                  )
                                                                            );  
                                                ?> 
                                            </div> 
                                        </div>
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['orderList']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('orderList'); ?>
                                        </div> 
                                    </div> 
<!--
                                  <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['description']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputTextArea('description',array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div> 
-->
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
      
      <div class="div-table transaction-detail" style="width:100%; ">
                <div class="div-table-row">  
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['quiz']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"></div>
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>"></div>
                </div>
        </div>    
        <div class="div-table mnv-transaction item-row  transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row" style="display:none" >  
                    <div class="div-table-col"></div> 
                    <div class="div-table-col"></div>
                    <div class="div-table-col"></div> 
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
    
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey']; 
                            $_POST['hidQuizKey[]'] =  $rsDetail[$i]['quizkey']; 
                            $_POST['quizName[]'] =  $rsDetail[$i]['quizname']; 
                             
                        }
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail">
                                    <?php echo $obj->inputText('quizName[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                    <?php echo $obj->inputHidden('hidQuizKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                    <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                </div> 
                            </div>
                        </div>   
                    </div>
                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); ?></div>
                </div> 
 
            <?php } ?> 
                   
         </div>        
        
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'],array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton();?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
