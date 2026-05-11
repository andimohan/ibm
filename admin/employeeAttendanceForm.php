<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Employee.class.php','EmployeeAttendance.class.php'));
$employeeAttendance = createObjAndAddToCol( new EmployeeAttendance()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 

$obj= $employeeAttendance;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'employeeAttendanceList'; 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');  

$editWarehouseInactiveCriteria = '';

$rs = prepareOnLoadData($obj);  

$rsLateDetail = array();

if (!empty($_GET['id'])){    
	
	$rsLateDetail = $obj->getDetailById($_GET['id']);
	$rsHalfDayDetail = $obj->getHalfDayDetail($_GET['id']);
	
	$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
	$_POST['employeeName'] = $rsEmployee[0]['code'] .' - '.$rsEmployee[0]['name'] ;
	 
	
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>

<script type="text/javascript">  
	jQuery(document).ready(function(){  
        
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
			
		 var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
        
         var varConstant = {  
            tablekey : tablekey
         };
       
         var employeeAttendance = new EmployeeAttendance(tabID,varConstant);
    
         prepareHandler(employeeAttendance);   
        
         var fieldValidation =  {
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
                                                    message: phpErrorMsg.employee[1]
                                                }, 
                                            }
                                        }    
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouse', $arrWarehouse); ?> 
                                        </div> 
                                    </div>  
								   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate', array('readonly' => true)); ?>  
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['employee']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php  echo $obj->inputAutoComplete(array(   
                                                                                'element' => array('value' => 'employeeName',
                                                                                                   'key' => 'hidEmployeeKey'),
																				'readonly'=> true,
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-employee.php',
                                                                                                    'data' => array(  'action' =>'searchData','searchField' => 'code,name')
                                                                                                )  
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div>  
								   
								   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['workDays']); ?></label> 
                                        <div class="col-xs-9"> 
											<div class="flex">
												<div class="consume"><?php echo $obj->inputNumber('totalWorkDays', array('readonly' => true)); ?></div>
												<div style="padding:0 1em"><?php echo $obj->lang['days']; ?></div>
											</div> 
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['absenceDays']); ?></label> 
                                        <div class="col-xs-9">
											<div class="flex">
												<div class="consume"><?php echo $obj->inputNumber('totalAbsenceDays',array('readonly' => true)); ?></div>
												<div style="padding:0 1em"><?php echo $obj->lang['days']; ?></div>
											</div> 
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['lateDays']); ?> / <?php echo $obj->lang['cut']; ?></label> 
                                        <div class="col-xs-9"> 
											<div class="flex">
												<div ><?php echo $obj->inputNumber('totalLateDays', array('readonly' => true,'etc' => 'style="width: 8em"')); ?></div>
												<div style="padding:0 .5em"><?php echo $obj->lang['days']; ?>,</div> 
												<div style="padding:0 .5em">IDR</div> 
												<div class="consume"><?php echo $obj->inputNumber('totalLateFine', array('readonly' => true)); ?></div>
											</div>  
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['halfDay']); ?></label> 
                                        <div class="col-xs-9"> 
											<div class="flex">
												<div class="consume"><?php echo $obj->inputNumber('totalHalfDay', array('readonly' => true)); ?></div>
												<div style="padding:0 1em"><?php echo $obj->lang['days']; ?></div>
											</div>  
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['unpaidLeave']); ?></label> 
                                        <div class="col-xs-9"> 
											<div class="flex">
												<div class="consume"><?php echo $obj->inputNumber('totalUnpaidLeave', array('readonly' => true)); ?></div>
												<div style="padding:0 1em"><?php echo $obj->lang['days']; ?></div>
											</div>  
                                        </div> 
                                    </div>  
                                     
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>  
                                   
                                </div>   
                    </div>   
                    
                    <div class="div-table-col"> 
      						   <div class="div-tab-panel"> 
                                  <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['late']); ?></div>
                                  	
								   <?php 
								   	$totalLateFine = 0;
								   	if (!empty($rsLateDetail)) { ?>
								   
										<div class="div-table" style="margin:auto;  width:95%; "> 
											 <div class="div-table-row"> 
												 <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:center; width: 8em" ><strong><?php echo ucwords($obj->lang['date']); ?></strong> </div> 
												 <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:center" ><strong><?php echo ucwords($obj->lang['late']); ?></strong>  </div> 
												 <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;" > <strong><?php echo ucwords($obj->lang['cut']); ?></strong></div> 
											 </div> 
											 <?php
											 for($i=0;$i<count($rsLateDetail);$i++){
 												 $totalLateFine += $rsLateDetail[$i]['latefine'];
												 echo '
												 <div class="div-table-row"> 
													 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:center" > '.$obj->formatDBDate($rsLateDetail[$i]['trdate']).'</div> 
													 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:center" > '.$rsLateDetail[$i]['late'].'</div>
													 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:right;" > '.$obj->formatNumber($rsLateDetail[$i]['latefine']).'</div> 
												 </div> 
												 ';
											 }
											?>
											 
											 <div class="div-table-row"> 
												 <div class="div-table-col-5" style="border-top:1px solid #666;"></div> 
												 <div class="div-table-col-5" style="border-top:1px solid #666;"></div> 
												 <div class="div-table-col-5" style="border-top:1px solid #666; text-align:right;" ><strong><?php echo $obj->formatNumber($totalLateFine) ?></strong></div> 
											 </div> 
										</div>
								   
								   <?php } ?>
								    
                                </div>
						
						 		<div class="div-tab-panel"> 
                                  <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['halfDay']); ?></div>
                                  	
								   <?php 
								   	 
								   	if (!empty($rsHalfDayDetail)) { ?>
								   
										<div class="div-table" style="margin:auto;  width:95%; "> 
											 <div class="div-table-row"> 
												 <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:center; width: 8em" ><strong><?php echo ucwords($obj->lang['date']); ?></strong> </div> 
												 <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:center" ><strong><?php echo ucwords($obj->lang['late']); ?></strong>  </div> 
												 <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right;" > <strong><?php echo ucwords($obj->lang['cut']); ?></strong></div> 
											 </div> 
											 <?php
											 for($i=0;$i<count($rsHalfDayDetail);$i++){ 
												 echo '
												 <div class="div-table-row"> 
													 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:center" > '.$obj->formatDBDate($rsHalfDayDetail[$i]['trdate']).'</div> 
													 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:center" > '.$rsHalfDayDetail[$i]['late'].'</div>
													 <div class="div-table-col-5" style="border-bottom:1px solid #dedede; text-align:right;" >0</div> 
												 </div> 
												 ';
											 }
											?>
<!--
											 
											 <div class="div-table-row"> 
												 <div class="div-table-col-5" style="border-top:1px solid #666;"></div> 
												 <div class="div-table-col-5" style="border-top:1px solid #666;"></div> 
												 <div class="div-table-col-5" style="border-top:1px solid #666; text-align:right;" ><strong><?php echo $obj->formatNumber($totalLateFine) ?></strong></div> 
											 </div> 
-->
										</div>
								   
								   <?php } ?>
								    
                                </div>
                    </div>    
                </div>
        </div>    
                     
    <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>