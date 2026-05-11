<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('EmployeeCommission.class.php');
$employeeCommission = createObjAndAddToCol(new EmployeeCommission());
$warehouse = createObjAndAddToCol(new Warehouse()); 
$employee = createObjAndAddToCol(new Employee()); 

$obj= $employeeCommission;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    
$formAction = 'employeeCommissionList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
 
$rsPurchaseDetail = array();

$_POST['trDate'] = date('d / m / Y');
$_POST['periodDate'] = date('F Y');
$_POST['endPeriodDate'] = date('F Y');

$overrideCommission = $security->isAdminLogin($obj->overrideEmployeeCommissionObject,10);

$rs = prepareOnLoadData($obj);
$rsDetail = array();
if (!empty($_GET['id'])){  
	$id = $_GET['id'];	  
    
    $rsDetail = $obj->getDetailWithRelatedInformation($id);
 
    $_POST['periodDate'] = $obj->formatDBDate($rs[0]['perioddate'], 'F Y');
    $_POST['hidCurrentPeriod'] = $obj->formatDBDate($rs[0]['perioddate'], 'F Y');
    $_POST['endPeriodDate'] = $obj->formatDBDate($rs[0]['endperioddate'], 'F Y');
    if (!empty($rs[0]['employeekey'])) {
        $rsEmployee = $employee->getDataRowById($rs[0]['employeekey']);
        $_POST['employeeName'] = $rsEmployee[0]['name'];
        $_POST['hidCurrenEmployeeKey'] = $rsEmployee[0]['pkey'];
        $_POST['hidCurrenEmployeeName'] = $rsEmployee[0]['name'];

        $_POST['commissionPercentage'] = $obj->formatNumber($rsEmployee[0]['commissionpercentage'], 2);
        $_POST['targetProfit'] = $obj->formatNumber($rsEmployee[0]['targetprofit'], 2);
        $_POST['targetMonthPeriod'] = $obj->formatNumber($rsEmployee[0]['targetmonthperiod']);

    }

	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
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
	
         var varConstant = {  
            overrideCommission : <?php echo ($overrideCommission) ? 'true' : 'false'; ?> 
         };
             
        var employeeCommission = new EmployeeCommission(tabID,varConstant ); 
        prepareHandler(employeeCommission); 
        var fieldValidation =  {
                                code: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.code[1] }, 
                                    }
                                }, 
                                employeeName: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.employee[1] }, 
                                    }
                                } 
                                } ; 
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
		 
	 
});
    
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
         <?php prepareOnLoadDataForm($obj); ?>
        <?php echo $obj->inputHidden('hidCurrentEmployeeKey'); ?>
        <?php echo $obj->inputHidden('hidCurrentEmployeeName'); ?>     
        <?php echo $obj->inputHidden('hidCurrentPeriod'); ?>          
    
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
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php echo $obj->inputSelect('selWarehouse', $arrWarehouse ); ?>  
                                    </div> 
                                </div>  
                                      <!-- <div class="form-group type-filter type-1">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label>
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputMonth('periodDate', array( 'allowedStatusForEdit' => array (1))); ?>
                                    </div>
                                </div>  -->


                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['period']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <div class="flex">
                                            <div class="consume"><?php echo $obj->inputMonth('periodDate',array('allowedStatusForEdit' => array (1), 'etc' => 'style="text-align:center"')); ?></div>
                                            <div>-</div>
                                            <div class="consume"><?php echo $obj->inputMonth('endPeriodDate',array('allowedStatusForEdit' => array (1), 'etc' => 'style="text-align:center"')); ?></div>  
                                        </div> 
                                    </div> 
                                </div>
<!--
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['refCode']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php echo $obj->inputText('refCode', array('readonly' => true) ); ?>  
                                    </div> 
                                </div>  
-->

                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['employee']); ?></label>
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputAutoComplete(
                                            array( 
                                                'element' => array(
                                                    'value' => 'employeeName',
                                                    'key' => 'hidEmployeeKey'
                                                ),
                                                'source' => array(
                                                    'url' => 'ajax-employee.php',
                                                    'data' => array('action' => 'searchData', 'searchField' => 'code,name', 'issales' => 1)
                                                ),
                                                    'callbackFunction' => 'getTabObj().onChangeEmployee(this,event, ui)'
                                            )
                                        );
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['profit']); ?></label>
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputDecimal('totalProfit', array('readonly' => true)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesCommission']); ?> (%)</label>
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputDecimal('commissionPercentage', array('readonly' => true)); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['targetProfit']); ?></label>
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume">
                                                <?php echo $obj->inputDecimal('targetProfit', array('readonly' => true, 'etc' => ' style="text-align:right"')); ?></div>
                                            <div>/</div>
                                            <div>
                                                <?php echo $obj->inputNumber('targetMonthPeriod', array('readonly' => true, 'etc' => ' style="text-align:right; width: 5em"')); ?>
                                            </div>
                                            <div><?php echo $obj->lang['month']; ?></div>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['totalCommission']); ?></label>
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputDecimal('totalCommission', array('readonly' => !$overrideCommission)); ?>
                                    </div>
                                </div>


                                <div class="form-group">
                                        <div class="col-xs-3"></div>
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputButton('btnImport', $obj->lang['showAll'], array('class' => 'btn btn-primary btn-second-tone')); ?>
                                    </div>
                                </div>

                                
                            </div>

                            
         		    </div> 
                    
                    <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                            <div class="form-group"> 
                                <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
                                </div> 
                            </div>   
                        </div>
                    </div>
                </div>
         </div>   
         
        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['jobOrder']); ?></div>
                    <div class="div-table-col detail-col-header" style="text-align:right;width:130px"><?php echo ucwords($obj->lang['totalselling']); ?></div>
                    <div class="div-table-col detail-col-header" style="text-align:right;width:130px"><?php echo ucwords($obj->lang['totalBuying']); ?></div>
                    <div class="div-table-col detail-col-header" style="text-align:right;width:100px"><?php echo ucwords($obj->lang['tax']); ?></div>
                    <div class="div-table-col detail-col-header" style="text-align:right;width:100px"><?php echo ucwords($obj->lang['refund']); ?></div>
                    <div class="div-table-col detail-col-header" style="text-align:right;width:100px"><?php echo ucwords($obj->lang['creditNote']); ?></div>
                    <div class="div-table-col detail-col-header" style="text-align:right;width:100px"><?php echo ucwords($obj->lang['debitNote']); ?></div>
                    <div class="div-table-col detail-col-header" style="text-align:right;width:130px"><?php echo ucwords($obj->lang['profit']); ?></div>
                
                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
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
                            $decimal = 0;
                            $inputnumber = 'inputnumber';


                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                            $_POST['hidJobOrderKey[]'] =  $rsDetail[$i]['jokey'];
                            $_POST['jobOrderCode[]'] =  $rsDetail[$i]['jocode'];
                            $_POST['totalBuying[]'] =  $obj->formatNumber($rsDetail[$i]['totalbuying'], 2);
                            $_POST['totalSelling[]'] =  $obj->formatNumber($rsDetail[$i]['totalselling'], 2);
                            $_POST['taxValue[]'] =  $obj->formatNumber($rsDetail[$i]['taxvalue'], 2);
                            $_POST['purchaseRefund[]'] =  $obj->formatNumber($rsDetail[$i]['purchaserefund'], 2);
                            $_POST['creditNote[]'] =  $obj->formatNumber($rsDetail[$i]['creditnote'], 2);
                            $_POST['debitNote[]'] =  $obj->formatNumber($rsDetail[$i]['debitnote'], 2);
                            $_POST['profit[]'] =  $obj->formatNumber($rsDetail[$i]['profit'], 2);
                            
                        } 
                        
                ?>
                <div class="div-table-row <?php echo $class; ?>"> 
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('jobOrderCode[]',array('overwritePost' => $overwrite, 'etc' =>  $etc)); ?>
                        <?php echo $obj->inputHidden('hidJobOrderKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail" style="text-align:right;width:100px;">
                        <?php echo $obj->inputDecimal('totalSelling[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' =>'style="text-align:right"' .  $etc)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail"  style="text-align:right;width:100px;">
                        <?php echo $obj->inputDecimal('totalBuying[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right"' .  $etc)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail" style="text-align:right;width:100px;">
                        <?php echo $obj->inputDecimal('taxValue[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' =>'style="text-align:right"' .  $etc)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail" style="text-align:right;width:100px;">
                        <?php echo $obj->inputDecimal('purchaseRefund[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' =>'style="text-align:right"' .  $etc)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail" style="text-align:right;width:100px;">
                        <?php echo $obj->inputDecimal('creditNote[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' =>'style="text-align:right"' .  $etc)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail" style="text-align:right;width:100px;">
                        <?php echo $obj->inputDecimal('debitNote[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' =>'style="text-align:right"' .  $etc)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail" style="text-align:right;width:100px;">
                        <?php echo $obj->inputDecimal('profit[]',array('overwritePost' => $overwrite, 'readonly' => true, 'etc' =>'style="text-align:right"' .  $etc)); ?>
                    </div> 
                            
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                </div> 
                <?php  }   ?>  
                   
         </div>        
       
          <div style="clear:both; height:1em;"></div> 
         <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
      
         
        <div class="form-button-margin"></div>
        <div class="form-button-panel" > 
        <?php  echo $obj->generateSaveButton(array(),true);   ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
