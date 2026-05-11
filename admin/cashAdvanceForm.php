<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('CashAdvance.class.php');
$cashAdvance = createObjAndAddToCol( new CashAdvance()); 
$employee = createObjAndAddToCol( new Employee()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$chartOfAccount = createObjAndAddToCol( new ChartOfAccount());

$obj = $cashAdvance;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'cashAdvanceList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
  
    
$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	 
    
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['realizationDate'] = $obj->formatDBDate($rs[0]['realizationdate'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => '')); 
    $_POST['note'] = $rs[0]['trdesc'];
	$_POST['selWarehouseKey'] = $rs[0]['warehousekey'];
   	$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
	$_POST['employeeName'] = $rsEmployee[0]['name'] ;
	$_POST['hidEmployeeKey'] = $rs[0]['employeekey'] ;
    $_POST['amount'] = $obj->formatNumber($rs[0]['amount']); 
    
    $rsCOA = $chartOfAccount->getDataRowById($rs[0]['coakey']);
	$_POST['COAName'] = $rsCOA[0]['code'].' - '.$rsCOA[0]['name'] ;
	$_POST['hidCOAKey'] = $rs[0]['coakey'] ;
    
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
        
         var cashAdvance = new CashAdvance(tabID); 
         prepareHandler(cashAdvance);
 
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
                                    },
                                 COAName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.coa[1]
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['realizationDate']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('realizationDate',array('readonly' => true)); ?>  
                                        </div> 
                                    </div>
					                                    
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cashBankAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php 
                                                   echo  $obj->inputAutoComplete( array(
                                                                            'objRefer' => $chartOfAccount,
                                                                            'revalidateField' => true, 
                                                                            'element' => array('value' => 'COAName',
                                                                                               'key' => 'hidCOAKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-coa.php',
                                                                                                'data' => array(  'action' =>'searchData', 'iscashbank' => '1' )
                                                                                            )  
                                                                ));
                                                ?>
                                        </div> 
                                    </div>  
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['recipient']); ?></label> 
                                    <div class="col-xs-9"> 
                                         <?php  echo  $obj->inputAutoComplete( array(
                                                                            'objRefer' => $employee,
                                                                            'revalidateField' => true, 
                                                                            'element' => array('value' => 'employeeName',
                                                                                               'key' => 'hidEmployeeKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-employee.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            ) 
                                                                ));
                                            ?>
                                    </div> 
                                </div> 
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('amount'); ?>  
                                        </div> 
                                    </div>
                             </div>
                         
                    </div>     
                <div class="div-table-col">
                     <div class="div-tab-panel">
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group">
                                        <div class="col-xs-12"> 
                                            <?php echo  $obj->inputTextArea('note', array('etc' => 'style="height:10em;"','allowedStatusForEdit' => array (1))); ?>                                         
                                        </div> 
                            </div>
                     
                     </div>
                </div>
            </div>
      </div> 
     
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
