<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj= $workProgress;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'workProgressList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsDetail = array();

$_POST['trDate'] = date('d / m / Y');
$defaultDetailDate = date('d / m / Y 00:00');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
	$rsDetail = $obj->getDetailById($id);
   
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['hidEmployeeKey'] = $rs[0]['employeekey'] ;
	$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
	$_POST['employeeName'] = $rsEmployee[0]['name'] ;
	     
	$_POST['trDesc'] = $rs[0]['trdesc'];
	
    
	$_POST['hidWOKey'] = $rs[0]['workorderkey'] ;
    $rsWO = $truckingServiceWorkOrder->getDataRowById($rs[0]['workorderkey']);
    $_POST['WOCode'] = $rsWO[0]['code'] ;
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
			
			   employeeName: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.employee[1]
                        }, 
                    }
                },   
				
			 
            }
        })
        .on('success.form.bv', function(e) {
               <?php echo $obj->submitFormScript(); ?> 
        });
		 
 
	objAndValue = new Array;
	objAndValue.push({object:'hidProgressKey[]', value :'pkey'}); 
    objAndValue.push({object:'progressCode[]', value :'wocode'}); 	
    objAndValueForDetailAutoComplete[tabID] = objAndValue;	
	
	// DETAIL CLONE
	 $("#"+ tabID + " [name=btnAddRows]").on('click', function() {
		addNewTemplateRow("detail-row-template");
		bindAutoCompleteForTransactionDetail('progressCode[]',objAndValueForDetailAutoComplete[tabID],'ajax-progress.php?action=searchData');
	});
	 
	

    <?php if (empty($_GET['id'])){ ?> 
    addNewTemplateRow("detail-row-template"); 
    <?php } ?>
  	bindAutoCompleteForTransactionDetail('progressCode[]',objAndValueForDetailAutoComplete[tabID],'ajax-progress.php?action=searchData');

    
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?>  
                                        </div> 
                                    </div>     
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['driver']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $employee,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'employeeName',
                                                                                                   'key' => 'hidEmployeeKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-employee.php',
                                                                                                    'data' => array(  'action' =>'searchData',
                                                                                                                      'isdriver' => '(1)'
                                                                                                                   )
                                                                                                ) 
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['WOCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $truckingServiceWorkOrder,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'WOCode',
                                                                                                   'key' => 'hidWOKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-trucking-service-work-order.php',
                                                                                                    'data' => array(  'action' =>'searchData'
                                                                                                                   )
                                                                                                ) 
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div> 
                                       
                                </div>
                    </div>
                    <div class="div-table-col"> 
                           <div class="div-tab-panel"> 
                              <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['note']); ?></div> 
                               <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?> 
                            </div>   
                    </div>
                </div>    
        </div>    
                        
        <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['progressCode']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:720px; text-align:left;"><?php echo ucwords($obj->lang['description']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?>"  style="width:70px"></div>
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
						    $rsProgress = $workStepProgress->getDataRowById($rsDetail[$i]['progresskey']);  
                            $_POST['hidProgressKey[]'] = $rsDetail[$i]['progresskey']; 
                            $_POST['progressCode[]'] = $rsProgress[0]['name'] ;
							$_POST['description[]'] = $rsDetail[$i]['description']; 
                        }
                       
                 ?>        
                 
                  <div class="div-table-row <?php echo $class; ?>"> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('progressCode[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidProgressKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('description[]',array('overwritePost' => $overwrite, 'etc' => 'style="text-align:left"' .$etc)); ?></div> 
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , $obj->lang['delete'], array('class' => 'btn btn-link remove-button', 'etc' => $etc)); ?> </div>
                </div>
            
                <?php  } ?>   
                   
         </div>        
                   
          <div style="clear:both; height:1em;"></div> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows']); ?></div>
              
        
        <div style="clear:both"></div>
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
