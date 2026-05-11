<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Termination.class.php');   
$termination = createObjAndAddToCol( new Termination()); 
$salesOrderSubscription = createObjAndAddToCol( new SalesOrderSubscription()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer()); 
$location = createObjAndAddToCol( new Location()); 
$employee = createObjAndAddToCol( new Employee());
$media = createObjAndAddToCol( new Media());
//$jobDetails = createObjAndAddToCol( new JobDetails());

$obj = $termination;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'terminationList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editWarehouseInactiveCriteria = ''; 
$editJobDetailsInactiveCriteria = ''; 
 
$_POST['trDate'] = date('d / m / Y');
$_POST['terminateDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
    	 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['terminateDate'] = $obj->formatDBDate($rs[0]['terminatedate'],'d / m / Y');
    $_POST['trDesc'] = $rs[0]['trdesc'];
    $_POST['department'] = $rs[0]['department'];

	$_POST['selWarehouseKey'] =$rs[0]['warehousekey'];
        
    $rsRepresented = $employee->getDataRowById($rs[0]['representedkey']);
    $_POST['representedName'] = $rsRepresented[0]['name'] ;
	$_POST['hidRepresentedKey'] = $rsRepresented[0]['pkey'] ; 
    
    $rsSO = $salesOrderSubscription->getDataRowById($rs[0]['salesorderkey']);
    $_POST['salesOrderCode'] = $rsSO[0]['code'] ;
	$_POST['hidSalesOrderKey'] = $rsSO[0]['pkey'] ; 

    
    if(!empty($rsSO[0]['pkey'])){
        $rsCustomer = $customer->getDataRowById($rsSO[0]['customerkey']);
        $_POST['selMedia'] = $rsCustomer[0]['mediakey'] ;
        $_POST['address'] = $rsCustomer[0]['address'] ;
        $_POST['phone'] = $rsCustomer[0]['phone'] ;
        $_POST['attention'] = $rsCustomer[0]['attention'] ;
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
        $_POST['sid'] = $rsCustomer[0]['sid'] ;
        $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ; 
        $rsLocation = $location->getDataRowById($rsCustomer[0]['locationkey']);
        $_POST['locationName'] = $rsLocation[0]['name'] ; 
        //$_POST['selJobDetails'] = $rsSO[0]['jobdetailskey'];

    }

	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	//$editJobDetailsInactiveCriteria = ' or  '.$jobDetails->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['jobdetailskey']);  

}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
//$arrJobDetails = $obj->convertForCombobox($jobDetails->searchData('','',true,' and ('.$jobDetails->tableName.'.statuskey = 1' .$editJobDetailsInactiveCriteria.')',' order by '.$jobDetails->tableName.'.name asc'),'pkey','name');  
$arrMedia = $class->convertForCombobox($media->searchData ('','',true,' and ('.$media->tableName.'.statuskey = 1 )'),'pkey','name');    
//$arrStageProcess = $class->convertForCombobox($stagesProcess->searchData ('','',true,' and ('.$stagesProcess->tableName.'.statuskey = 1 )',' order by '.$stagesProcess->tableName.'.orderlist asc'),'pkey','name');    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        
         var termination = new Termination(tabID);
    
         prepareHandler(termination);
        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    },
                                salesOrderCode: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.salesOrder[1]
                                            }, 
                                        }
                                    },
                                representedName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.represented[1]
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
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
                                                                                                    'data' => array(  'action' =>'searchData', 'statuskey' => '(3)')
                                                                                                ) ,
                                                                                'callbackFunction' => 'getTabObj().updateOrderInformation()'                                                                          
                                                                                )
                                                                        );  
                                            ?> 
                                        </div> 
                                 </div>
                                      <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['terminationDate']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('terminateDate'); ?> 
                                        </div> 
                                    </div>    
                                  <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['representedby']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $employee,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'representedName',
                                                                                                   'key' => 'hidRepresentedKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-employee.php',
                                                                                                    'data' => array(  'action' =>'searchData', 
                                                                                                                              'statuskey' => 2  )
                                                                                                )
                                                                                )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>  
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['department']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('department'); ?> 
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
                                <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['customerInformation']); ?></div>
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'readonly' => true,
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData', 
                                                                                                                              'statuskey' => 2  )
                                                                                                ),
                                                                                'callbackFunction' => 'getTabObj().updateCustomerInformation()'                                                                           
                                                                                )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sid']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('sid', array('readonly' => true)); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['media']); ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selMedia', $arrMedia, array('readonly' => true)); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['attention']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('attention', array('readonly' => true)); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('phone', array('readonly' => true)); ?> 
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
                                            <?php echo  $obj->inputTextArea('address', array('readonly' => true,'etc' => 'style="height:10em;"')); ?>
                                        </div> 
                                    </div>     
                            </div> 
                    </div>
           </div>
      </div>      
          
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(1,2),true);?>
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
