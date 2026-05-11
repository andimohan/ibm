<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 
 
$obj= $membershipAttendance;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
$hasARAccess = $security->isAdminLogin($ar->securityObject,10);  
  
$formAction = 'membershipAttendanceList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$editCategoryInactiveCriteria = '';
$editMemberInactiveCriteria = '';
$editCustomerMemberInactiveCriteria = '';
$_POST['trDate'] = date('d / m / Y 00:00');

$rs = prepareOnLoadData($obj); 
$rsContactPerson = array();
$rsShippingAddress = array();
$arrCustomerMembership = array();  

if (!empty($_GET['id'])){ 
           
    $id = $_GET['id'];
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y H:i');
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['code'].' - '.$rsCustomer[0]['name'] ;
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['trDesc'] = $rs[0]['trdesc'];
	
	$_POST['selCustomerMembership'] = $rs[0]['customermembershipkey']; 
	$rsMembership = $customerMembership->getDataRowById($rs[0]['customermembershipkey']);
	if(!empty($rs[0]['customermembershipkey'])){ 
		$_POST['maxAttendance'] = $obj->formatNumber($rsMembership[0]['maxattendance']); 
		$_POST['timeLimit'] = $obj->formatNumber($rsMembership[0]['timelimit']); 
		//$_POST['expDate'] = $obj->formatDBDate($rsMembership[0]['expdate'],'d / m / Y'); 
		$_POST['attendance'] = $obj->formatNumber($rsMembership[0]['attendance']);
        $_POST['selMembership'] = $rsMembership[0]['membershipkey']; 
	}
	
	$rsService = $item->getDataRowById($rs[0]['servicekey']);
	$_POST['serviceName'] = $rsService[0]['name'] ;
	$_POST['hidServiceKey'] = $rsService[0]['pkey'] ;
    
	$editCustomerMemberInactiveCriteria = ' or '.$customerMembership->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['customermembershipkey']); 
    	$arrCustomerMembership = $class->convertForCombobox($customerMembership->searchData('','',true, ' and '.$customerMembership->tableName.'.customerkey = '.$obj->oDbCon->paramString($rs[0]['customerkey']).' and ('.$customerMembership->tableName.'.statuskey = 2'.$editCustomerMemberInactiveCriteria.')'),'pkey','code'); 

} 

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');
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
	  
        var membershipAttendance = new MembershipAttendance(tabID); 
        prepareHandler(membershipAttendance); 
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
			
								/*selCustomerMembership: {
                                        validators: {
                                        notEmpty: {  message: phpErrorMsg.customerMembership[1] }, 
                                    }
                                 }*/
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
                                            <?php echo $obj->inputAutoCode('code', array('allowedStatusForEdit' => array(1)) ); ?>
                                </div> 
                            </div>    
							<!--<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('allowedStatusForEdit' => array(1)) ); ?>  
								</div> 
							</div>-->
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputDateTime('trDate',array('allowedStatusForEdit' => array(1))); ?> 
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
																							'data' => array(  'action' =>'searchData','searchField' => 'code,name' )
                                                                        ),
                                                                        'allowedStatusForEdit' => array (1),
                                                                        'callbackFunction' => 'getTabObj().updateCustomer()' 
																	  )
																);  
									?> 
								</div> 
							</div>
							
                            <div class="form-group">
                                <label class="col-xs-3 control-label" style="padding-top:0"><?php echo ucwords($obj->lang['customerMembership']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $obj->inputSelect('selCustomerMembership', $arrCustomerMembership,array('allowedStatusForEdit' => array(1))); ?>
                                </div> 
                            </div>
							<div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['membership']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $obj->inputSelect('selMembership', $arrMembership, array('readonly' => true)); ?>
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
							
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['class']); ?></label> 
								<div class="col-xs-9"> 
									  <?php  echo $obj->inputAutoComplete(array( 
																		'objRefer' => $item,
																		'revalidateField' => true,
																		'element' => array('value' => 'serviceName',
																						   'key' => 'hidServiceKey'),
																		'source' =>array(
																							'url' => 'ajax-item.php',
																							'data' => array(  'action' =>'searchData' , 'itemtype' => 3)
																						),
                                                                        'allowedStatusForEdit' => array (1,2)
																	  )
																);  
									?> 
								</div> 
							</div>
							

                        </div>
                        
                        
                    </div>
                  
                    <div class="div-table-col">  
                     
                      <div class="div-tab-panel"> 
                        <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['note']); ?></div>
                        <div class="form-group"> 
                            <div class="col-xs-12"> 
                                    <?php echo  $obj->inputTextArea('trDesc', array('allowedStatusForEdit' => array (1,2),'etc' => 'style="height:10em;"')); ?>
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
