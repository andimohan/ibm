<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('InstallationWorkOrder.class.php');   
$installationWorkOrder = createObjAndAddToCol( new InstallationWorkOrder()); 
$salesOrderSubscription = createObjAndAddToCol( new SalesOrderSubscription()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer()); 
$location = createObjAndAddToCol( new Location()); 
$employee = createObjAndAddToCol( new Employee()); 
$stagesProcess = createObjAndAddToCol( new StagesProcess()); 
$jobDetails = createObjAndAddToCol( new JobDetails()); 
$itemUnit = createObjAndAddToCol( new ItemUnit()); 
$media = createObjAndAddToCol( new Media()); 
$item = createObjAndAddToCol( new Item()); 
$supplier = createObjAndAddToCol( new Supplier()); 

$obj = $installationWorkOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'installationWorkOrderList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
$editJobDetailsInactiveCriteria = ''; 
  
$rsDetail = array();
$rsDetailTechnician = array();
$defaultHour = date('d / m / Y 00:00');
$_POST['trDate'] = date('d / m / Y');
$_POST['startTime'] = $defaultHour;
$_POST['endTime'] = $defaultHour;
//$_POST['chkIsDone'] = 1;
$isConfirm = false;
$rs = prepareOnLoadData($obj);  

$showOutsource  = false;

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    
    if($rs[0]['isoutsource']==1){ 
       $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
       $_POST['hidSupplierKey'] = $rsSupplier[0]['pkey'] ; 
       $_POST['supplierName'] = $rsSupplier[0]['name'] ; 
    }
    
    $rsDetailTechnician = $obj->getDetailTechnicianWithRelatedInformation($id);    
    
    $showOutsource = $rs[0]['isoutsource'];
	
    if($rs[0]['statuskey']==2) $isConfirm = true;
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
    $_POST['selStageKey'] = $rs[0]['stagekey'];
    $_POST['chkIsOutsource'] = $rs[0]['isoutsource'];
    $_POST['chkIsDone'] = $rs[0]['isdone'];
    $_POST['trDesc'] = $rs[0]['trdesc'];
	$_POST['startTime'] = $obj->formatDBDate($rs[0]['starttime'],'d / m / Y H:i');
	$_POST['endTime'] = $obj->formatDBDate($rs[0]['endtime'],'d / m / Y H:i');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey'];
    $rsSO = $salesOrderSubscription->getDataRowById($rs[0]['salesorderkey']);
    $_POST['salesOrderCode'] = $rsSO[0]['code'] ;
	$_POST['hidSalesOrderKey'] = $rsSO[0]['pkey'] ; 
    if(!empty($rsSO[0]['pkey'])){
        $rsCustomer = $customer->getDataRowById($rsSO[0]['customerkey']);
        $_POST['selMedia'] = $rsCustomer[0]['mediakey'] ;
        $_POST['address'] = $rsCustomer[0]['address'] ;
        $_POST['phone'] = $rsCustomer[0]['phone'] ;
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
        $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ; 
        $rsLocation = $location->getDataRowById($rsCustomer[0]['locationkey']);
        $_POST['locationName'] = $rsLocation[0]['name'] ; 
        $_POST['selJobDetails'] = $rsSO[0]['jobdetailskey'];
    }
    
    if(!empty($rs[0]['customerkey'])){
       $_POST['hidSalesKey'] = $rsCustomer[0]['saleskey'] ;
       $rsEmployee = $employee->getDataRowById($rsCustomer[0]['saleskey']);
	   $_POST['salesName'] = $rsEmployee[0]['name'] ;
        
    }



	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editJobDetailsInactiveCriteria = ' or  '.$jobDetails->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['jobdetailskey']);  

}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrJobDetails = $obj->convertForCombobox($jobDetails->searchData('','',true,' and ('.$jobDetails->tableName.'.statuskey = 1' .$editJobDetailsInactiveCriteria.')',' order by '.$jobDetails->tableName.'.name asc'),'pkey','name');  
$arrDefaultUnit = $obj->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 
$arrMedia = $class->convertForCombobox($media->searchData ('','',true,' and ('.$media->tableName.'.statuskey = 1 )'),'pkey','name');    
$arrStageProcess = $class->convertForCombobox($stagesProcess->searchData ('','',true,' and ('.$stagesProcess->tableName.'.statuskey = 1 )',' order by '.$stagesProcess->tableName.'.orderlist asc'),'pkey','name');    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        
         var installationWorkOrder = new InstallationWorkOrder(tabID,<?php echo json_encode($rs); ?>);
    
         prepareHandler(installationWorkOrder);
        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 

                                   customerName: { 
                                        validators: {
                                            notEmpty: {
                                                message:  phpErrorMsg.customer[1]
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate', array('allowedStatusForEdit' => array (1))); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('allowedStatusForEdit' => array (1))); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['stagesProcess']); ?></label>  
                                                <div class="col-xs-6 control-label"><?php echo  $obj->inputSelect('selStageKey', $arrStageProcess, array('allowedStatusForEdit' => array (1))); ?></div>
                                                <div class="col-xs-1"><?php echo  $obj->inputCheckBox('chkIsDone',array('allowedStatusForEdit' => array(1,2))); ?></div>
                                                <div class="col-xs-2 control-label" style="padding-left:0"><?php echo ucwords($obj->lang['done']); ?></div>

                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesOrder']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $salesOrderSubscription,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'salesOrderCode',
                                                                                                   'key' => 'hidSalesOrderKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-sales-order-subscription.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'statuskey' => '(2)' )
                                                                                                ), 
                                                                                'allowedStatusForEdit' => array (1) ,
                                                                                'callbackFunction' => 'getTabObj().updateOrderInformation()'                                                                          
                                                                                )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobDetails']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selJobDetails', $arrJobDetails, array('readonly' => true)); ?>
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesman']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php                
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'objRefer'=>$employee,
                                                                                        'revalidateField' => false, 
                                                                                        'element' => array('value' => 'salesName',
                                                                                                           'key' => 'hidSalesKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-employee.php',
                                                                                                            'data' => array(  'action' =>'searchData')
                                                                                                        ),
                                                                                        'readonly'=>true 
                                                                                      )
                                                                                );  
                                            ?>  
                                        </div> 
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['startTime']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDateTime('startTime', array('allowedStatusForEdit' => array (1))); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['endTime']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDateTime('endTime',array('allowedStatusForEdit' => array(1,2))); ?> 
                                        </div> 
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"','allowedStatusForEdit' => array(1,2))); ?>                                         </div> 
                                    </div>
                             </div>
                         
                    </div>
                     <div class="div-table-col">
                            <div class="div-tab-panel">
                                <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['customerInformation']); ?></div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'readonly'=>true
//                                                                                'popupForm' => array(
//                                                                                                    'url' => 'customerForm.php',
//                                                                                                    'element' => array('value' => 'customerName',
//                                                                                                           'key' => 'hidCustomerKey'),
//                                                                                                    'width' => '1000px',
//                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
//                                                                                                )                                                                              
                                                                                )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['media']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selMedia', $arrMedia, array('readonly' => true)); ?> 
                                        </div> 
                                    </div>  
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('phone', array('readonly'=>true, )); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?></label> 
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputText('locationName', array('readonly' => true)); ?> 
                                            
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('address', array('readonly'=>true, 'etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>    
                            </div> 
                            <div class="div-tab-panel"> 
    				    
                                <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['technician']); ?></div>
                                    <div class="form-group" >
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['outsource']; ?></label> 
                                        <div class="col-xs-9"> 
                                           <div class="flex">
                                               <div><?php echo $obj->inputCheckBox('chkIsOutsource', array('etc' => 'onChange="getTabObj().updateOutsource(this)"')); ?></div>
                                               <div class="consume">
                                                     <?php  echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $customer,
                                                                                    'revalidateField' => true,
                                                                                    'element' => array('value' => 'supplierName',
                                                                                                       'key' => 'hidSupplierKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-supplier.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    )
                                                                                )
                                                                            );  
                                                       ?> 
                                               </div>
                                           </div> 
                                        </div> 
                                    </div> 
                                    
                                    <div class="technician-detail" style="margin-top:1em">  
                                    <div class="div-table mnv-transaction transaction-detail" style="width:100%;">
                                            <div class="div-table-row"> 
                                                <div class="div-table-col detail-col-header" style="border:0"><?php echo ucwords($obj->lang['technician']); ?></div>
                                                <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col" style="border:0;"></div>
                                            </div>

                                            <?php 
                                                $totalDetail = count($rsDetailTechnician); 

                                                for ($i=0;$i<=$totalDetail; $i++){  

                                                    $class =  'transaction-detail-row';
                                                    $overwrite = true;
                                                    $disabled = false; 
                                                    $style = '';
                                                    $deleteIcon = $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"'));
                                                    $readonly = false; 
                                                    if ($i == $totalDetail ){
                                                        $class = 'technician-row-template';
                                                        $overwrite = false;
                                                        $disabled = true; 
                                                        $style = 'display: none !important';
                                                    } else {  

                                                        $_POST['hidDetailTechnicianKey[]'] =  $rsDetailTechnician[$i]['pkey'];
                                                        $_POST['hidTechnicianKey[]'] =  $rsDetailTechnician[$i]['techniciankey']; 
                                                        $_POST['technicianName[]'] =  $rsDetailTechnician[$i]['technicianname']; 
                                                        if($isConfirm){
                                                            $readonly = true;
                                                            $deleteIcon = '';
                                                        } 
                                                    }
                                            ?>
 
                                            <div class="div-table-row <?php echo $class; ?>" style="<?php echo $style ; ?>">
                                                <div class="div-table-col detail-col-detail">
                                                    <?php echo $obj->inputText('technicianName[]',array('overwritePost' => $overwrite,'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                    <?php echo $obj->inputHidden('hidTechnicianKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                                                    <?php echo $obj->inputHidden('hidDetailTechnicianKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                                                </div> 
                                                <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $deleteIcon; ?></div>
                                            </div>

                                        <?php } ?> 

                                     </div>        
                                      <?php if (!$isConfirm){ ?>
                                      <div style="clear:both; height:1em;"></div> 
                                      <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddTechnician', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
                                     <?php } ?> 
                                </div>  

                             </div>
                            
                    </div>
           </div>
      </div>
      <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="width:30px; text-align:right;">#</div>
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['description']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                    <?php if ($isConfirm || $rs[0]['statuskey']==3){ ?>
                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['usedQty']); ?> </div>
                        <div class="div-table-col detail-col-header" style="width:100px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                    <?php } ?>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  
							
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        $deleteIcon = $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"'));
                        $readonlyUsed = true; 
                        $readonly = false; 
                        $arrUnit = $arrDefaultUnit;

                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                            $unitname = 'Pcs';
                        } else {  
                            $decimal = 0;
                            $inputnumber = 'inputnumber';
                            
                            $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                            $_POST['hidItemKey[]'] =  $rsDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsDetail[$i]['itemname']; 
                            $_POST['hidGramasi[]'] =   $obj->formatNumber($rsDetail[$i]['gramasi']); 
                            $_POST['qty[]'] =   $obj->formatNumber($rsDetail[$i]['qty']); 
                            $_POST['usedQty[]'] =   $obj->formatNumber($rsDetail[$i]['usedqty']); 
                            $_POST['selUnit[]'] =  $rsDetail[$i]['unitkey'];
                            $_POST['selUsedUnit[]'] =  $rsDetail[$i]['usedunitkey'];
                            if($isConfirm){
                                $readonly = true;
                                $readonlyUsed = false;
                                $deleteIcon ='';
                            }
                                 

                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsDetail[$i]['itemkey']),'conversionunitkey','unitname','',array('relconversionmultiplier' => 'conversionmultiplier')); 
                 
                        } 
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail" style="text-align:right;"><div class="row-number"></div></div>
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite,'readonly' => $readonly, 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidGramasi[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidGramasiSubtotal[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite,'readonly' => $readonly, 'etc' => 'style="text-align:right;"', 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite,'readonly' => $readonly,  'disabled' => $disabled)); ?></div>                
                    <?php if ($isConfirm || $rs[0]['statuskey']==3){ ?>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('usedQty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' , 'disabled' => $disabled,'readonly' => $readonlyUsed)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selUsedUnit[]',$arrUnit, array('overwritePost' => $overwrite,'readonly' => $readonlyUsed,  'disabled' => $disabled)); ?></div>
                    <?php } ?>
                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $deleteIcon; ?></div>
                </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div>
          <?php if (!$isConfirm){ ?>
            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
          <?php } ?>
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(1,2),true);?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
