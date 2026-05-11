<?php 
require_once '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array("Quiz.class.php","Warehouse.class.php"));
$warehouse = new Warehouse();
$quiz = new Quiz();

$obj = $quiz;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'quizList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
  
$rsDetail = array();
$rsItemDetail = array();
$rsDescDetail = array();
    
$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
	$rsDescDetail = $obj->getDescDetail($id);
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['name'] = $rs[0]['name'];
    $_POST['description'] = $rs[0]['description'];
    $_POST['from'] = $rs[0]['fromvalue'];
    $_POST['to'] = $rs[0]['tovalue'];
    $_POST['level'] = $rs[0]['levelval'];
	$_POST['selWarehouseKey'] = $rs[0]['warehousekey'];
     
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
    
    $rsImage = array(); 
    if(!empty($rs[0]['uploadfile'])){
		$rsImage[0]['file'] =  $rs[0]['uploadfile'];
        $rsImage[0]['phpthumbhash'] = getPHPThumbHash($rsImage[0]['file']);
	
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath); 
	} 
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 <style>
    .question-row-header .div-table-col {vertical-align: middle}
    .question-row-header > .div-table-col {vertical-align: middle; vertical-align: top}
</style> 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        
         var quiz = new Quiz(tabID,"<?php echo $obj->uploadFolder; ?>",<?php echo json_encode($rsImage); ?>);
    
         prepareHandler(quiz);

        
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['description']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputTextArea('description',array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div> 
                             </div>
                             <div class="div-tab-panel">
                                    <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['image']); ?></div>
                                 <div class="form-group">
<!--                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['image']); ?></label> -->
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
                      
                    <div class="div-tab-panel">  
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['Tiering']); ?></div>
                        
                        <div class="div-table mnv-transaction desc-row transaction-detail" style="width:100%; ">
                                <?php  
                                    $totalDesc = count($rsDescDetail); 

                                    for ($k=0;$k<=$totalDesc; $k++){  

                                        $class =  'transaction-detail-row';
                                        $overwrite = true;
                                        $disabled = false; 

                                        if ($k == $totalDesc ){
                                            $class = 'desc-row-template row-template';
                                            $disabled = true; 
                                            $overwrite = false;
                                            $editor = $obj->inputTextArea('descDetail[]', array('overwritePost' => $overwrite, 'class' => 'ckeditor', 'disabled' => $disabled));;
                                        } else {   
                                            $_POST['hidDetailItemDescKey[]'] =  $rsDescDetail[$k]['pkey']; 
                                            $_POST['descDetail[]'] =  $rsDescDetail[$k]['description']; 
                                            $_POST['from[]'] =  $rsDescDetail[$k]['fromvalue']; 
                                            $_POST['to[]'] =  $rsDescDetail[$k]['tovalue']; 
                                            $_POST['level[]'] =  $rsDescDetail[$k]['level']; 
                                            $editor = $obj->inputEditor('descDetail[]', array('overwritePost' => $overwrite));
                                        }

                                ?>
                             
                                
                                  
                                <div class="div-table-row odd-style-adjustment <?php echo $class; ?>">
                                    <div class="div-table-col detail-col-detail" style="padding:0.5em">
                                        <div class="flex" style="padding-bottom:1em">
                                            <div><?php echo ucwords($obj->lang['correctAnswer']); ?></div>
                                            <div style="width:80px;"><?php echo $obj->inputNumber('from[]'); ?></div>
                                            <div>-</div>
                                            <div style="width:80px;"><?php echo $obj->inputNumber('to[]'); ?></div>
                                            <div style="padding-left:1em"><?php echo ucwords($obj->lang['level']); ?></div>
                                            <div><?php echo $obj->inputText('level[]'); ?></div> 
                                        </div>   
                                        <div >
                                            <?php echo $editor; ?>
                                            <?php echo $obj->inputHidden('hidDetailItemDescKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                        </div>  
                                    </div>
                                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" ')); ?></div>
                                </div> 
                                
                            <?php } ?> 

                         </div>        

                          <div style="clear:both; height:1em;"></div> 
                          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddDesc', $obj->lang['addRows'],array('class' => 'btn btn-primary btn-second-tone')); ?></div>
                 </div>

                </div>
            </div>
      </div>
      
      <div class="div-tab-panel">

      <div class="div-table-caption border-red">Q &amp; A</div>
      <div class="div-table mnv-transaction transaction-detail no-odd-even-style" style="width:100%;" attr-level="0">
                <?php 
                    $totalRows = count($rsDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  
							
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        $rsItemDetail = array();
                        $totalItemRows = 0;
                        $deleteIcon = $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"'));
                        $readonly = false; 

                        if ($i == $totalRows ){
                            $class = 'detail-row-template row-template';
                            $overwrite = false;
                            $disabled = true; 
                            $editor = $obj->inputTextArea('question[]', array('overwritePost' => $overwrite, 'class' => 'ckeditor', 'disabled' => $disabled));
                        } else {  
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                            $_POST['question[]'] =  $rsDetail[$i]['question']; 
                            $editor = $obj->inputEditor('question[]', array('overwritePost' => $overwrite));
                        } 
				 
                ?>            
                
                <div class="div-table-row question-row <?php echo $class; ?>">
                    <div class="div-table-col" style="padding:0; padding-top:1.5em">
                        <div class="div-table row-panel" style="width:100%">
                        <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail row-number" style="padding:1.8em 0.5em 0 0.5em; vertical-align:top; font-size:1.2em"></div>
                                    <div class="div-table-col detail-col-detail" style="padding:1em 0;">
                                        <div class="div-table" style="width:100%;"> 
                                            <div class="div-table-row question-row-header">
                                                <div class="div-table-col" style="width:900px;">
                                                    <div class="div-table" >
                                                        <div class="div-table-row"> 
                                                             <div class="div-table-col">
                                                                    <?php //echo $obj->inputText('question[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                                    <?php echo $editor; ?>
                                                                    <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                            </div>
                                                            
                                                        </div>
                                                    </div>    
                                                </div>
                                                <div class="div-table-col" style="width:20px;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="div-table transaction-detail detail-item" style="padding-left:0.1em; width: 100%; margin: auto; margin-top:10px"  attr-level="1" attr-group="hidDetailItemKey">
                                             <div class="div-table-row">   
                                                    <div class="div-table-col "  style="font-weight:bold"><?php echo ucwords($obj->lang['answer']); ?></div> 
                                                    <div class="div-table-col  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                                    <div class="div-table-col   <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                                </div> 
                                                <?php 
                                                    $itemDetailKey = (isset($rsDetail[$i])) ? $rsDetail[$i]['pkey'] : 0;
                                                    $rsItemDetail = $obj->getItemDetail($itemDetailKey); 
                                                    $totalItemRows = count($rsItemDetail); 

                                                    for ($j=0;$j<=$totalItemRows; $j++){  

                                                        $class =  'transaction-detail-row';
                                                        $overwrite = true;
                                                        $disabled = false; 
                                                        $readonly = false; 

                                                        if ($j == $totalItemRows ){
                                                             $class = 'item-row-template row-template';
                                                             $overwrite = false; 
                                                             $disabled = true; 
                                                        } else {  

                                                            $_POST['hidDetailItemKey[]'] =  $rsItemDetail[$j]['pkey'];
                                                            $_POST['answers[]'] =  $rsItemDetail[$j]['answers'];
                                                            $_POST['chkIsAnswer[]'] =  $rsItemDetail[$j]['isanswer'];

                                                        } 

                                                ?>
                                            
                                                <div class="div-table-row <?php echo $class; ?>"> 
                                                    <div class="div-table-col detail-col-detail">
                                                        <?php echo $obj->inputHidden('hidDetailItemKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disabled)); ?>
                                                        <?php echo $obj->inputText('answers[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' =>  $disabled)); ?>
                                                    </div>
                                                    <div class="div-table-col detail-col-detail">
                                                        <?php echo $obj->inputCheckBox('chkIsAnswer[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' =>  $disabled)); ?>
                                                    </div>
                                                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="item-row-template"')) : ''; ?></div>
                                                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnDeleteQuetionRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : ''; ?></div>
                       
                                                </div>
                                             <?php } ?> 

                                        </div>
                                     </div> 
                                    <div class="div-table-col detail-col-detail icon-col<?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top; padding-top:1em !important"><?php echo $deleteIcon; ?></div>
                        </div>
                        </div>
                    </div>
                </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div>
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
         
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
