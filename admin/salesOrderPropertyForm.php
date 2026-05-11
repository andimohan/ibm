<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
includeClass(array('SalesOrderProperty.class.php'));
$salesOrderProperty = createObjAndAddToCol( new SalesOrderProperty()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$customer = createObjAndAddToCol( new Customer()); 
$supplier = createObjAndAddToCol( new Supplier()); 
$employee = createObjAndAddToCol( new Employee()); 
$customerDownpayment = createObjAndAddToCol( new CustomerDownpayment()); 
$termOfPayment = createObjAndAddToCol( new TermOfPayment()); 
$chartOfAccount = createObjAndAddToCol( new ChartofAccount()); 
$salesOrderPropertyType = createObjAndAddToCol( new SalesOrderPropertyType()); 
	
$obj = $salesOrderProperty;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'salesOrderPropertyList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$editTermOfPaymentInactiveCriteria = '';
$editWarehouseInactiveCriteria = ''; 

$rsSalesDetail = array();
$rsPaymentMethodDetail = array();
$rsVoucher = array();

$_POST['trDate'] = date('d / m / Y H:i');

$rs = prepareOnLoadData($obj);  

// nnati diganti ke settingan
//$_POST['officeFeePercentage'] = 30;  
//$_POST['agentFeePercentage'] = 70;  
//$_POST['officeFeeBankPercentage'] = 30;  
//$_POST['agentFeeBankPercentage'] = 70;    
$_POST['adminFeePercentage'] = 2;  
$_POST['orLeadPercentage'] = 1;    
$_POST['bankProvisionPercentage'] =  1;  
$_POST['taxFeePercentage'] =  2.5;  

$rsKey = $obj->getTableKeyAndObj($obj->tableName,array('key'));
$rsDetail = array();

$hideCommissionPercentage ='';
$hideProvisionPercentage = '';


// default persen
$_POST['selCommissionType'] = 2;
$_POST['selProvisionType'] = 2;

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	$agentFeeDetail = $salesOrderProperty->getDetailWithRelatedInformation($id);
    $rsSalesOrderDP = $obj->getDownpaymentDetail($id); 
    
    $rsCustomer = $customer->getDataRowById($rs[0]['buyerkey']);
    $_POST['selCustomCode'] = $rs[0]['customcodekey']; 
	$_POST['buyerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidBuyerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['hidCurrentBuyerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['hidCurrentBuyerName'] = $rsCustomer[0]['name'] ;  
    
    $rsCustomer = $customer->getDataRowById($rs[0]['sellerkey']);
	$_POST['sellerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidSellerKey'] = $rsCustomer[0]['pkey'] ; 
    
    $rsBank = $customer->getDataRowById($rs[0]['bankkey']);
	$_POST['bankName'] = $rsBank[0]['name'] ;
	$_POST['hidBankKey'] = $rsBank[0]['pkey'] ; 
    
    $rsEmployee = $employee->getDataRowById($rs[0]['agentkey']);
	$_POST['employeeName'] = $rsEmployee[0]['name'] ;
	$_POST['hidEmployeeKey'] = $rsEmployee[0]['pkey'] ; 
	
	if(!empty($rs[0]['refundcoakey'])){
		$rsCOA = $chartOfAccount->getDataRowById($rs[0]['refundcoakey']);
		$_POST['refundCOAName'] = $rsCOA[0]['code'].' - '.$rsCOA[0]['name'] ;
		$_POST['hidRefundCOAKey'] = $rs[0]['refundcoakey'] ; 
	}
	
	  
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y H:i'); 
	$_POST['selCommissionType'] = $rs[0]['commissiontype'];
	$hideCommissionPercentage = ($rs[0]['commissiontype'] != 2) ? 'display:none;': '' ;
	
	$_POST['selProvisionType'] = $rs[0]['provisiontype'];
	$hideProvisionPercentage = ($rs[0]['provisiontype'] != 2) ? 'display:none;': '' ;
	
	$_POST['agencyPercentage'] = $obj->formatNumber($rs[0]['agencypercentage'],2);
	$_POST['officeFeePercentage'] = $obj->formatNumber($rs[0]['officepercentage'],2);
	$_POST['agentFeePercentage'] = $obj->formatNumber($rs[0]['agentpercentage'],2);
	$_POST['adminFeePercentage'] = $obj->formatNumber($rs[0]['adminpercentage'],2);
	$_POST['taxFeePercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['orLeadPercentage'] = $obj->formatNumber($rs[0]['orleadpercentage'],2);
	$_POST['bankProvisionPercentage'] = $obj->formatNumber($rs[0]['bankprovisionpercentage'],2);
     
	$_POST['officeFeeBankPercentage'] = $obj->formatNumber($rs[0]['officebankpercentage'],2);
	$_POST['agentFeeBankPercentage'] = $obj->formatNumber($rs[0]['agentbankpercentage'],2); 
  
	$_POST['totalCommissionCompany'] =  $obj->formatNumber($rs[0]['officefee']);
	$_POST['totalCommissionAgent'] =  $obj->formatNumber($rs[0]['agentfee']);
	$_POST['totalBankProvisionCompany'] =  $obj->formatNumber($rs[0]['officefeebank']);
	$_POST['totalBankProvisionAgent'] =  $obj->formatNumber($rs[0]['agentfeebank']);
		
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
    $editCustomCodeInactiveCriteria = ' or  '.$customCode->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['customcodekey']); 
	
}

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));  
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' =>' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')')); 
$arrType = $salesOrderPropertyType->generateComboboxOpt(null,array('criteria' =>' and ('.$salesOrderPropertyType->tableName.'.statuskey = 1 )'));  
// sementara ambil yg TOP lebih dr 0 saja dulu 
$arrTOP = $termOfPayment->generateComboboxOpt(null,array('criteria' => ' and ('.$termOfPayment->tableName.'.statuskey = 1 and '.$termOfPayment->tableName.'.duedays > 0 ' .$editTermOfPaymentInactiveCriteria.')')); 
$arrCustomCode = $customCode->generateComboboxOpt(null,array('criteria' =>' and ('.$customCode->tableName.'.reftabletype = '.$rsKey['key'].' and '.$customCode->tableName.'.statuskey = 1 ' . $editCustomCodeInactiveCriteria.')')); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = selectedTab.newPanel[0].id; 
        salesOrderProperty = new SalesOrderProperty(tabID, <?php echo json_encode($rs); ?>);
        prepareHandler(salesOrderProperty); 
        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    },  
                                sellerName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.salesOrderProperty[2]
                                            }, 
                                        }
                                    },  
                                buyerName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.salesOrderProperty[3]
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
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" > 
    <?php prepareOnLoadDataForm($obj); ?>   
    <?php echo $obj->inputHidden('hidCurrentBuyerKey'); ?>
    <?php echo $obj->inputHidden('hidCurrentBuyerName'); ?> 
     
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
                                            <?php echo $obj->inputDateTime('trDate'); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
                                        </div> 
                                    </div>    
								 
                                    <?php if (!empty($arrCustomCode)) {  ?>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesType']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selCustomCode', $arrCustomCode); ?>
                                        </div> 
                                    </div>  
                                    <?php }  ?>
								    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9">   <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>   </div> 
                                    </div> 
                             </div>  
                    </div>
                     <div class="div-table-col">  
						 	<div class="div-tab-panel"> 
                                   <div class="div-table-caption" style="border:0; height:2.3em"></div> 
								   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['type']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selType', $arrType); ?>
                                        </div> 
                                    </div> 
								    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['propertyInformation']); ?></label> 
                                        <div class="col-xs-9">   <?php echo  $obj->inputTextArea('propertyInformation', array('etc' => 'style="height:10em;"')); ?>   </div> 
                                    </div>  
                                  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['seller']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array(  
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'sellerName',
                                                                                                   'key' => 'hidSellerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ), 
                                                                                    
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
								   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['buyer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array(  
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'buyerName',
                                                                                                   'key' => 'hidBuyerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
																				'callbackFunction' => 'getTabObj().onChangeBuyer()'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 

									<div class="form-group" style="margin-top:2em">
										<label class="col-xs-3 control-label">  <?php echo ucwords($obj->lang['payment']); ?> </label> 
										<div class="col-xs-9"> <?php echo  $obj->inputSelect('selTermOfPaymentKey', $arrTOP); ?> </div> 
									</div>          
 
                    </div>
					</div>  
           </div>
		   
            <div class="div-table-row">
                 <div class="div-table-col"> 
					 <div class="div-tab-panel">   
                            <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['transactionValue']); ?></div>    
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['transactionValue']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo  $obj->inputNumber('transactionTotal',array ('etc' => 'style="text-align:right;"')); ?>
								</div> 
							</div>  
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['downpayment']); ?></label> 
								<div class="col-xs-9"> 

									<?php echo $obj->inputHidden('totalDownpayment'); ?> 

									<div class="div-table transaction-detail  no-odd-even-style" style="width: 100%">
										<?php  
											$totalRows = count($rsSalesOrderDP);
											for($i=0;$i<=$totalRows;$i++) {
													$class =  'downpayment-row transaction-detail-row';
													$overwrite = true; 
													$disabled = false; 

													if ($i == $totalRows ){
														$class = 'downpayment-row-template row-template'; 
														$overwrite = false; 
														$disabled = true; 
													} else {   
														$_POST['hidDetailDownpaymentKey[]'] = $rsSalesOrderDP[$i]['pkey'];
														$_POST['hidDownpaymentKey[]'] = $rsSalesOrderDP[$i]['downpaymentkey'];
														$_POST['downpaymentCode[]'] = $rsSalesOrderDP[$i]['refcode'];
														$_POST['downpaymentAmount[]'] = $obj->formatNumber($rsSalesOrderDP[$i]['amount']); 
													}
										?> 

										<div class="div-table-row <?php echo $class; ?> ">
											<div class="div-table-col-3" style="text-align:right; padding-left:0">  
													<?php echo $obj->inputHidden('hidDetailDownpaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?>
													<?php echo $obj->inputHidden('hidDownpaymentKey[]',array('overwritePost' => $overwrite,  'disabled' => $disabled)); ?> 
													<?php echo  $obj->inputText('downpaymentCode[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
											</div>  
											<div class="div-table-col-3" style="width:180px"> 
												   <?php echo $obj->inputNumber('downpaymentAmount[]', array('overwritePost' => $overwrite, 'class'=>'form-control inputnumber mnv-detail-field', 'disabled' => $disabled, 'etc' => 'style="text-align:right;"')); ?>
											</div>  
											<div class="div-table-col-3 icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="downpayment-row-template"')); ?></div>
                                            <div class="div-table-col-3 icon-col <?php echo $obj->hideOnDisabled(); ?>" style="padding-right:0">
												<?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"', 'class' =>'btn btn-link remove-button' )); ?>
											</div>
										</div> 

										<?php } ?>  

								   </div>   
								</div> 
							</div>
						 
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['balance']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo  $obj->inputNumber('balance',array ('readonly'=>true, 'etc' => 'style="text-align:right;"')); ?>
								</div> 
							</div>
						 
						    <div class="form-group" style="margin-top:2em">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customerDownpaymentSettlement']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo  $obj->inputNumber('downpaymentSettlement',array ('readonly'=>true, 'etc' => 'style="text-align:right;"')); ?>
								</div> 
							</div>
							<div class="form-group">
								<label class="col-xs-3 control-label">  <?php echo ucwords($obj->lang['settlementAccount']); ?> </label> 
								<div class="col-xs-9">
										<?php 
										   echo  $obj->inputAutoComplete( array(
																	'objRefer' => $chartOfAccount,
																	'revalidateField' => true, 
																	'element' => array('value' => 'refundCOAName',
																					   'key' => 'hidRefundCOAKey'),
																	'source' =>array(
																						'url' => 'ajax-coa.php',
																						'data' => array(  'action' =>'searchData', 'iscashbank' => '1' )
																					)  
														));
										?>

								</div> 
							</div>    
						 
                                
                            <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['commissionTransaction']); ?></div>      
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['commission']); ?></label>
								<div class="col-xs-9">
									<div class="flex">
										<div style="width:6em"><?php echo $obj->inputSelect('selCommissionType', $obj->arrDiscountType); ?> </div>
										<div style="width:6em <?php echo $hideCommissionPercentage; ?>" class="field-percentage"><?php echo  $obj->inputDecimal('agencyPercentage', array('etc' => 'style="text-align:right;"')); ?></div>
										<div style="width:1em <?php echo $hideCommissionPercentage; ?>" class="field-percentage">%</div>
										<div class="consume">
											<?php echo  $obj->inputNumber('agencyFee', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
										</div>
									</div>
								</div>

							</div>
                            <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['company']); ?></label> 
								<div class="col-xs-9">      
									<div class="flex">
									<div class="consume">
										  <?php echo  $obj->inputNumber('officeFee',array ('readonly' => true,'etc' => 'style="text-align:right;"')); ?>
									</div>
									</div>
								</div> 
							</div>   
						<div class="form-group">
							<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['agent']); ?></label> 
							<div class="col-xs-9">      
								<div class="flex"> 
								<div class="consume">
									  <?php echo  $obj->inputNumber('agentFee',array ('readonly' => true,'etc' => 'style="text-align:right;"')); ?>
								</div>
								</div>
							</div> 
						</div>    
                                
							<div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['bankProvision']); ?></div>       
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bank']); ?></label> 
								<div class="col-xs-9"> 
									  <?php  echo $obj->inputAutoComplete(array( 
																		'objRefer' => $supplier,
																		'revalidateField' => true,
																		'element' => array('value' => 'bankName',
																						   'key' => 'hidBankKey'),
																		'source' =>array(
																							'url' => 'ajax-customer.php',
																							'data' => array(  'action' =>'searchData' )
																						)
																	  )
																);  
									?> 
								</div> 
							</div> 
								
                            <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['amount']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo  $obj->inputNumber('bankTotal',array ('etc' => 'style="text-align:right;"')); ?>
								</div> 
							</div>  
								<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankProvision']); ?></label>
								<div class="col-xs-9">
									<div class="flex">
										<div style="width:6em"><?php echo $obj->inputSelect('selProvisionType', $obj->arrDiscountType); ?> </div>
										<div style="width:6em  <?php echo $hideProvisionPercentage; ?>" class="field-percentage-provision"><?php echo  $obj->inputDecimal('bankProvisionPercentage', array('etc' => 'style="text-align:right;"')); ?></div>
										<div style="width:1em  <?php echo $hideProvisionPercentage; ?>" class="field-percentage-provision">%</div>
										<div class="consume">
											<?php echo  $obj->inputNumber('bankProvision', array('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>
										</div>
									</div>
								</div>
							</div>   
				          <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['company']); ?></label> 
								<div class="col-xs-9">      
									<div class="flex">
									<div class="consume">
										  <?php echo  $obj->inputNumber('officeFeeBank',array ('readonly' => true,'etc' => 'style="text-align:right;"')); ?>
									</div>
									</div>
								</div> 
							</div>   
						<div class="form-group">
							<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['agent']); ?></label> 
							<div class="col-xs-9">      
								<div class="flex"> 
								<div class="consume">
									  <?php echo  $obj->inputNumber('agentFeeBank',array ('readonly' => true,'etc' => 'style="text-align:right;"')); ?>
								</div>
								</div>
							</div> 
						</div>   
						 
					 </div>
				 </div>
				 <div class="div-table-col"> 
					 <div class="div-tab-panel">
					 	 <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['companyRevenue']); ?></div>      
             
                        <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['commission']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo  $obj->inputNumber('totalCommissionCompany',array ('readonly' => true,'etc' => 'style="text-align:right;"')); ?>
								</div> 
				        </div> 
                        <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bankProvision']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo  $obj->inputNumber('totalBankProvisionCompany',array ('readonly' => true,'etc' => 'style="text-align:right;"')); ?>
								</div> 
				        </div> 
						<div class="form-group" >
                              
							<label class="col-xs-3 control-label">OR Lead</label> 
							<div class="col-xs-9">      
								<div class="flex">
								<div style="width:6em" ><?php echo  $obj->inputDecimal('orLeadPercentage',array ('etc' => 'style="text-align:right;"')); ?></div>
									<div style="width:1em">%</div>
								<div class="consume">
									  <?php echo  $obj->inputNumber('orLead',array ('readonly' => true,'etc' => 'style="text-align:right;"')); ?>
								</div>
								</div>
							</div> 
						</div>    
				       <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['total']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo  $obj->inputNumber('totalCompanyRevenue',array ('readonly' => true,'etc' => 'style="text-align:right;"')); ?>
								</div> 
				        </div> 	   
						<div style="clear:both; height: 1em"></div>	 
                        <div class="col-xs-12 section-title"><?php echo strtoupper($obj->lang['agentRevenue']); ?></div>      
 							<div class="form-group">
								<div class="div-table-col col-xs-12" style="width:100%;">
									<div class="div-tab-panel">
										<div class="div-table  mnv-transaction transaction-detail  no-odd-even-style" style="width:100%;">
											<?php
											$totalRows = count($agentFeeDetail);
											for ($i = 0; $i <= $totalRows; $i++) {
												$class =  'transaction-detail-row';
												$overwrite = true;
												$etc = '';
												$showOptions = false;

												if ($i == $totalRows) {
													$class = 'detail-row-template';
													$overwrite = false;
													$etc = 'disabled="disabled"';
												} else {
													
													$_POST['hidDetailKey[]'] =  $agentFeeDetail[$i]['pkey'];
													$_POST['agentName[]'] =  $agentFeeDetail[$i]['employeename'];
													$_POST['hidAgentKey[]'] =  $agentFeeDetail[$i]['agentkey'];
													$_POST['cobrokePercentage[]'] = $obj->formatNumber($agentFeeDetail[$i]['cobrokepercentage'],2);
													$_POST['commissionPercentage[]'] = $obj->formatNumber($agentFeeDetail[$i]['commissionpercentage'],2);
													$_POST['agentBankProvision[]'] = $obj->formatNumber($agentFeeDetail[$i]['bankprovision']);
													$_POST['agentClosingFee[]'] = $obj->formatNumber($agentFeeDetail[$i]['closingfee']);
													$_POST['commissionFee[]'] = $obj->formatNumber($agentFeeDetail[$i]['commissionfee']);
													$_POST['cobrokeFee[]'] = $obj->formatNumber($agentFeeDetail[$i]['cobrokefee']);
													$_POST['provisionPercentage[]'] = $obj->formatNumber($agentFeeDetail[$i]['bankprovisionpercentage'],2);
												}

											?>
												<div class="div-table-row  <?php echo $class; ?> ">
													<div class="div-table-col detail-col-detail">
														<div class="form-group">
															<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label>
															<div class="col-xs-9">
																<?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
																<?php echo $obj->inputHidden('hidAgentKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
																<?php echo $obj->inputText('agentName[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
															</div>
														</div>
														<div class="form-group">
															<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['cobroke']); ?></label>
															<div class="col-xs-9">
																<div class="flex">
																	<div style="width:6em"><?php echo  $obj->inputDecimal('cobrokePercentage[]', array('overwritePost' => $overwrite, 'etc' => $etc . 'style="text-align:right;"')); ?></div>
																	<div style="width:1em">%</div>
																	<div class="consume">
																		<?php echo  $obj->inputNumber('cobrokeFee[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc . 'style="text-align:right;"')); ?>
																	</div>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label class="col-xs-3 control-label" style="padding-top:0px !important"><?php echo ucwords($obj->lang['commission']); ?><div class="text-muted"><span class="company-commission-percentage"></span>% <?php echo strtolower($obj->lang['company']); ?></div></label>
															<div class="col-xs-9">
																<div class="flex">
																	<div style="width:6em"><?php echo  $obj->inputDecimal('commissionPercentage[]', array('overwritePost' => $overwrite, 'etc' => $etc . 'style="text-align:right;"')); ?></div>
																	<div style="width:1em">%</div>
																	<div class="consume">
																		<?php echo  $obj->inputNumber('commissionFee[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc . 'style="text-align:right;"')); ?>
																	</div>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label class="col-xs-3 control-label" style="padding-top:0px !important"><?php echo ucwords($obj->lang['bankProvision']); ?><div class="text-muted"><span class="company-provision-percentage"></span>% <?php echo strtolower($obj->lang['company']); ?></div></label>
															<div class="col-xs-9">
																<div class="flex">
																	<div style="width:6em"><?php echo  $obj->inputDecimal('provisionPercentage[]', array('overwritePost' => $overwrite, 'etc' => $etc . 'style="text-align:right;"')); ?></div>
																	<div style="width:1em">%</div>
																	<div class="consume">
																		<?php echo  $obj->inputNumber('agentBankProvision[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc . 'style="text-align:right;"')); ?>
																	</div>
																</div>
															</div>
														</div>
														<div class="form-group">
															<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['closingFee']); ?></label>
															<div class="col-xs-9">
																<?php echo  $obj->inputNumber('agentClosingFee[]', array('overwritePost' => $overwrite, 'readonly' => false, 'etc' => $etc . 'style="text-align:right;"')); ?>
															</div>
														</div>
														<div class="form-group">
															<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['total']); ?></label>
															<div class="col-xs-9">
																<?php echo  $obj->inputNumber('agentTotal[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc . 'style="text-align:right;"')); ?>
															</div>
														</div>

														<div style="text-align:right" class="<?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', $obj->lang['delete'], array('etc' => 'tabIndex="-1"', 'class' => 'btn btn-link remove-button')); ?></div>
														<div style="clear:both; height:1em;"></div>
													</div>
												</div>
											<?php  } ?>
										</div>

										<div style="clear:both; height:1em;"></div>
										<div style="text-align:center"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>

									</div>
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
