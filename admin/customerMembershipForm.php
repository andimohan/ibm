<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 
 
$obj= $customerMembership;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
$hasARAccess = $security->isAdminLogin($ar->securityObject,10);  
  
$formAction = 'customerMembershipList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$editCategoryInactiveCriteria = '';
$editMemberInactiveCriteria = '';
$_POST['trDate'] = date('d / m / Y');

$rs = prepareOnLoadData($obj); 
$rsContactPerson = array();
$rsShippingAddress = array();

if (!empty($_GET['id'])){ 
           
    $id = $_GET['id'];
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['activationDate'] = $obj->formatDBDate($rs[0]['activationdate'],'d / m / Y');
	//$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$_POST['selMembership'] =$rs[0]['membershipkey']; 
	$_POST['selVoucher'] =$rs[0]['voucherkey']; 
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['trDesc'] = $rs[0]['trdesc'];
	$rsMembership = $membership->getDataRowById($rs[0]['membershipkey']);
	$_POST['maxAttendance'] = $obj->formatNumber($rsMembership[0]['maxattendance']); 
	$_POST['attendance'] = $obj->formatNumber($rs[0]['attendance']); 
	$_POST['registrationCost'] = $obj->formatNumber($rs[0]['price']); 
	$_POST['discountValue'] = $obj->formatNumber($rs[0]['discountvalue']); 
	$_POST['balance'] = $obj->formatNumber($rs[0]['total']); 
	$_POST['timeLimit'] = $obj->formatNumber($rsMembership[0]['timelimit']); 
    
	 
	$editMemberInactiveCriteria = ' or '.$membership->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['membershipkey']);
$editVoucherInactiveCriteria = ' or '.$voucherTransaction->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['voucherkey']);
		//$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
  $arrVoucher = $class->convertForCombobox($voucherTransaction->searchData('','',true, ' and '.$voucherTransaction->tableName.'.customerkey = '.$obj->oDbCon->paramString($rs[0]['customerkey']).' and ('.$voucherTransaction->tableName.'.statuskey = 1'.$editVoucherInactiveCriteria.')'),'pkey','code'); 

} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
//$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrMembership = $class->convertForCombobox($membership->searchData('','',true, ' and ('.$membership->tableName.'.statuskey = 1' .$editMemberInactiveCriteria.')'),'pkey','name'); 
 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  
<script type="text/javascript">  
	
		jQuery(document).ready(function(){   
    
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
	  
        var customerMembership = new CustomerMembership(tabID); 
        prepareHandler(customerMembership); 
        var fieldValidation =  {
                                code: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.code[1] }, 
                                    }
                                 },
            
                                customerName: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.customer[1] }, 
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
                            
                            <!--<div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputSelect('selCategory', $arrCategory); ?> 
                                </div> 
                            </div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('allowedStatusForEdit' => array(1)) ); ?>  
								</div> 
							</div>-->
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputDate('trDate'); ?> 
								</div> 
							</div> 
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
																						),
  																		'callbackFunction' => 'getTabObj().updateCustomer()'
																	  )
																);  
									?> 
								</div> 
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['activationDate']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputDate('activationDate', array('readonly' => true)); ?> 
								</div> 
							</div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['membership']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $obj->inputSelect('selMembership', $arrMembership); ?>
                                </div> 
                            </div>
							<div class="form-group" >
								<label class="col-xs-3 control-label"><?php echo $obj->lang['attendance']; ?></label> 
								<div class="col-xs-9">
                                    <div class="flex">
                                        <div class="consume"><?php echo $obj->inputNumber('attendance', array('readonly' => true,'etc' => 'style="text-align:right','class' => 'form-control inputnumber ')); ?>  </div>
                                        <div>/</div>
                                        <div class="consume"><?php echo $obj->inputNumber('maxAttendance', array('readonly' => true,'etc' => 'style="text-align:right','class' => 'form-control inputnumber ')); ?>  </div>
                                    </div>
									 
								</div> 
							</div> 
							<div class="form-group" >
								<label class="col-xs-3 control-label"><?php echo $obj->lang['timeLimit'].' ('.$obj->lang['month'].')'; ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputNumber('timeLimit', array('readonly' => true,'class' => 'form-control inputnumber ')); ?>   
								</div> 
							</div> 
                  <div class="form-group" >
								<label class="col-xs-3 control-label"><?php echo $obj->lang['registrationCost']; ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputNumber('registrationCost', array('readonly' => true,'class' => 'form-control inputnumber ')); ?>   
								</div> 
							</div> 
                             <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['voucher']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $obj->inputSelect('selVoucher', $arrVoucher); ?>
                                </div> 
                            </div>
                      
                             <div class="form-group" >
								<label class="col-xs-3 control-label"><?php echo $obj->lang['discount']; ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputNumber('discountValue', array('readonly' => true,'class' => 'form-control inputnumber ')); ?>   
								</div> 
							</div> 
                            <div class="form-group" >
								<label class="col-xs-3 control-label"><?php echo $obj->lang['balance']; ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputNumber('balance', array('readonly' => true,'class' => 'form-control inputnumber ')); ?>   
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
   
        <div class="form-button-panel" > <?php echo $obj->generateSaveButton(); ?>  </div>  
    </form>  
   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
