<?php 
require_once '../../../_config.php'; 
require_once '../../../_include-v2.php';

includeClass(array('Customer.class.php'));
$customer = createObjAndAddToCol(new Customer());
$ar = createObjAndAddToCol(new AR()); 
$paymentMethod =   createObjAndAddToCol(new PaymentMethod());
$termOfPayment = createObjAndAddToCol(new TermOfPayment());
$customerCategory = createObjAndAddToCol(new CustomerCategory());
$city = createObjAndAddToCol(new City());
$currency = createObjAndAddToCol(new Currency());
$supplier =  createObjAndAddToCol(new Supplier());
$personInCharge = createObjAndAddToCol(new PersonInChargeGroup());
	
$isActiveCOA = $class->isActiveModule('chartOfAccount'); 
if($isActiveCOA && USE_GL)
	$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());

$obj= $customer;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
$hasARAccess = $security->isAdminLogin($ar->securityObject,10);   
$showItemImage = $obj->loadSetting('showItemImage');
$formAction = 'customerList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
 
$editCategoryInactiveCriteria = '';
$editTermOfPaymentInactiveCriteria = '';
$editPaymentMethodInactiveCriteria = '';
$editPersonInChargeCriteria = '';

$rs = prepareOnLoadData($obj); 
$_POST['dob'] = '01 / 01 / 2000';
$rsContactPerson = array();
$rsShippingAddress = array();
$arrCurrency = array();
$rsDetailItemAlias = array();
$rsDetailAccount = array();
$rsPersonInChargeDetail = array();

if (!empty($_GET['id'])){ 
           
    $id = $_GET['id'];
    $rsContactPerson = $obj->getContactPerson($id);
    $rsDetailAccount = $obj->getAccountDetail($id);
    $rsShippingAddress = $obj->getMultipleAddress($id,1);
    $rsPersonInChargeDetail = $obj->getPICGroupDetail($id);
    
	if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding']))) 
		$rsDetailItemAlias = $obj->getItemAliasDetail($id);
    
	$_POST['name'] = $rs[0]['name']; 
	$_POST['alias'] = $rs[0]['alias']; 
	$_POST['address'] = $rs[0]['address'];  
	$_POST['zipCode'] = $rs[0]['zipcode']; 
	$_POST['phone'] = $rs[0]['phone']; 
	$POST['mobile'] = $rs[0]['mobile']; 
	$_POST['email'] = $rs[0]['email']; 
	$_POST['fax'] = $rs[0]['fax']; 
	$_POST['taxid'] = $rs[0]['taxid']; 
	$_POST['taxRegistrationName'] = $rs[0]['taxregistrationname']; 
	$_POST['taxRegistrationAddress'] = $rs[0]['taxregistrationaddress']; 
	$_POST['description'] = $rs[0]['description']; 
	$_POST['selCategory'] = $rs[0]['categorykey']; 
	//s$_POST['selPersonInCharge'] = $rs[0]['personinchargekey']; 
	$_POST['hidCityKey'] = $rs[0]['citykey']; 
	$_POST['creditlimit'] = $obj->formatNumber($rs[0]['creditlimit']); 
	$_POST['selTermOfPayment'] = $rs[0]['termofpaymentkey'];  
	$_POST['selBank'] = $rs[0]['companybankkey'];      
	$_POST['userName'] = $rs[0]['username'];  
	$_POST['selCurrencyPreference'] = $rs[0]['currencypreference'];  
    $_POST['hidLatLng'] = $rs[0]['latlng'];
    $_POST['sex'] = $rs[0]['sexkey']; 
	$_POST['IDNumber'] = $rs[0]['idnumber']; 
    $_POST['dob'] = $obj->formatDBDate($rs[0]['dateofbirth'],'d / m / Y'); 
    $aroustanding = ($hasARAccess) ? $rs[0]['aroutstanding'] : 0; 
	$_POST['aroutstanding'] = $obj->formatNumber($aroustanding);   
	$_POST['point'] = $obj->formatNumber($rs[0]['point']);   
	
	$_POST['billingEmail'] = $rs[0]['billingemail']; 
	$_POST['billingMobile'] = $rs[0]['billingmobile']; 
	$_POST['chkICA'] = $rs[0]['isica']; 
	$_POST['chkShowGPSLocation'] = $rs[0]['showgpslocation'];
	$_POST['virtualAccount'] = $rs[0]['virtualaccount'];
	
	if (!empty($rs[0]['citykey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['citykey'],true);
		$_POST['cityName'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
	}
    
    $_POST['hidSalesKey'] = $rs[0]['saleskey'];
	if (!empty($rs[0]['saleskey'])){
		$rsSales = $employee->getDataRowById($rs[0]['saleskey']);
		$_POST['salesName'] = $rsSales[0]['name'];
	}
    
	$_POST['hidSupplierLinkKey'] = $rs[0]['supplierlinkkey'];
	if (!empty($rs[0]['supplierlinkkey'])){
		$rsSupplier = $supplier->getDataRowById($rs[0]['supplierlinkkey']);
		$_POST['supplierLinkName'] = $rsSupplier[0]['name'];
	}
    

    $_POST['hidPlaceOfBirthKey'] = $rs[0]['placeofbirth'];

    if (!empty($_POST['hidPlaceOfBirthKey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['placeofbirth'],true);
		$_POST['placeOfBirth'] = $rsCity[0]['name'] ;
	}   
    
	if($isActiveCOA && USE_GL){ 
		
		$arrCOAKey = array($rs[0]['arcoakey'],$rs[0]['downpaymentcoakey'],$rs[0]['icacoakey'],$rs[0]['apicacoakey'] );
		
		$rsCOACol = $chartOfAccount->searchDataRow(array($chartOfAccount->tableName.'.pkey',$chartOfAccount->tableName.'.code',$chartOfAccount->tableName.'.name'),
												' and ' . $chartOfAccount->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrCOAKey ,',').')'
											   );
		$rsCOACol = array_column($rsCOACol, null,'pkey');
		
		$_POST['hidARCOAKey'] = $rs[0]['arcoakey'];  
		if (!empty($rs[0]['arcoakey'])){
			$rsCOA = $rsCOACol[$rs[0]['arcoakey']]; 
			$_POST['ARCOA'] = $rsCOA['code'] . ' - ' . $rsCOA['name'];
		}

		$_POST['hidDownpaymentCOAKey'] = $rs[0]['downpaymentcoakey'];  
		if (!empty($rs[0]['downpaymentcoakey'])){ 
			$rsCOA = $rsCOACol[$rs[0]['downpaymentcoakey']]; 
			$_POST['downpaymentCOA'] = $rsCOA['code'] . ' - ' . $rsCOA['name']; 
		}
		
		$_POST['hidICACOAKey'] = $rs[0]['icacoakey'];  
		if (!empty($rs[0]['icacoakey'])){ 
			$rsCOA = $rsCOACol[$rs[0]['icacoakey']]; 
			$_POST['ICACOA'] = $rsCOA['code'] . ' - ' . $rsCOA['name']; 
		}
		
		
		$_POST['hidAPICACOAKey'] = $rs[0]['apicacoakey'];  
		if (!empty($rs[0]['apicacoakey'])){ 
			$rsCOA = $rsCOACol[$rs[0]['apicacoakey']]; 
			$_POST['APICACOA'] = $rsCOA['code'] . ' - ' . $rsCOA['name']; 
		}
	}
		
	$editPaymentMethodInactiveCriteria = ' or '.$paymentMethod->tableName.'.pkey =' .$obj->oDbCon->paramString($rs[0]['companybankkey']);	 
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
	$editCategoryInactiveCriteria = ' or '.$customerCategory->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['categorykey']);
    $editPersonInChargeCriteria = ' or '.$personInCharge->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['personinchargekey']);

} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status')); 
$arrCategory = $customerCategory->generateComboboxOpt(null,array('criteria' =>' and ('.$customerCategory->tableName.'.statuskey = 1 '. $editCategoryInactiveCriteria.')')); 
$arrTOP = $termOfPayment->generateComboboxOpt(null,array('criteria' =>' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')','order' => ' order by duedays asc')); 
$arrPaymentMethod = $paymentMethod->generateComboboxOpt(null,array('criteria' =>' and ('.$paymentMethod->tableName.'.statuskey = 1' . $editPaymentMethodInactiveCriteria.')')); 
$arrCurrency = $currency->generateComboboxOpt(null,array('criteria' =>' and ('.$currency->tableName.'.statuskey = 1)')); 
$arrSex = $obj->generateComboboxOpt(array('data' => $obj->getSex()));

//PIC select box data
$arrPersonInCharge = $personInCharge->searchData($personInCharge->tableName . '.statuskey', 1, true, $editPersonInChargeCriteria);
$temp = count($arrPersonInCharge);
$arrPersonInCharge[$temp]['name'] = '----';
$arrPersonInCharge[$temp]['pkey'] = 0;
$arrPersonInCharge = $obj->convertForCombobox($arrPersonInCharge,'pkey','name');  

$arrLoginType = array();
foreach(LOGIN_TYPE as $key=>$row)
	array_push($arrLoginType, array('name' => ucfirst($key), 'pkey' => $row));

array_shift($arrLoginType);

$arrCustomerAccountRoleType = $obj->generateComboboxOpt(array('data' => $arrLoginType));	

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>   
<script type="text/javascript">  
	  
	jQuery(document).ready(function(){  
        
		
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>;
		 
		 var opt = {};
		 
		// by default ini dimatikan, kecuali mau di personalized
		 opt.showItemImage =  false; 

	      opt.data = <?php echo json_encode(
                            array(
                                    'detailAccount' => $rsDetailAccount,
                                    'customerPersonInCharge' => $rsPersonInChargeDetail
                                 )
                            ); ?>;
		  
         var customer = new Customer(tabID,opt); 
         prepareHandler(customer);   
         
         var fieldValidation =  { code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    },  
                                    name: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.customer[1]
                                            }, 
                                        }
                                    },
                                    email: { 
                                        validators: { 
                                            emailAddress: {
                                                message:  phpErrorMsg.email[3]
                                            }
                                        }
                                    },

                                } ; 
        
        
        setFormValidation(getTabObj(tabID), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
   	
	});
			
			
</script>

</head> 

<body> 
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>">
        <?php prepareOnLoadDataForm($obj); ?> 
     	<?php echo $obj->inputHidden('hidLatLng'); ?>
       <div class="div-table main-tab-table-2">
              <div class="div-table-row">
                    <div class="div-table-col"> 
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['generalInformation']); ?></div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                <div class="col-xs-9"> 
                                        <?php echo  $obj->inputSelect('selStatus', $arrStatus,array('value' => 2)); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                </div> 
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('name'); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['alias']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputText('alias'); ?>
                                </div> 
                            </div>    
							<div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['asSupplier']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php  echo $obj->inputAutoComplete(array(  
                                                                            'element' => array('value' => 'supplierLinkName',
                                                                                               'key' => 'hidSupplierLinkKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-supplier.php',
                                                                                                'data' => array('action' =>'searchData')
                                                                                            ) 
                                                                          )
                                                                    );   ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                <div class="col-xs-9"> 
                                       <?php echo  $obj->inputSelect('selCategory', $arrCategory); ?> 
                                </div> 
                            </div>      
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['PICGroup']); ?></label>
                                <div class="col-xs-9">  
                                    <div class="div-table mnv-transaction transaction-detail" style="width:100%">
                                        <?php 
                                            $totalPersonInChargeRows = count($rsPersonInChargeDetail);
                                            
                                            for ($i = 0; $i <= $totalPersonInChargeRows; $i++) {

                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $readonly = false;
                                                $disabled = false; 
                                                $style = '';

                                                if ($i == $totalPersonInChargeRows ){
                                                    $class = 'pic-row-template';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                    $isLocked = false;
                                                    $style = 'style="display:none !important"';
                                                } else{ 
                                                    $_POST['hidCustomerPersonInChargeKey[]'] = $rsPersonInChargeDetail[$i]['pkey'];
                                                    $_POST['selPersonInCharge[]'] = $rsPersonInChargeDetail[$i]['personinchargekey'];
                                                
                                                }
                                                $hideDeleteIcon = '';
                                        ?>  
                                            
                                            <div class="div-table-row <?php echo $class; ?> odd-style-adjustment" <?php echo $style; ?>>
                                                <div class="div-table-col" style="padding:0"> 
                                                    <div class="flex">     
                                                        <div  class="consume">
                                                            <?php echo $obj->inputHidden('hidCustomerPersonInChargeKey[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputSelect('selPersonInCharge[]', $arrPersonInCharge, array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                        </div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>">
                                                            <?php echo $obj->inputLinkButton('btnAddDetailRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="pic-row-template"')); ?>
                                                        </div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>">
                                                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" style="padding:6px 0; ' . $hideDeleteIcon . '"')); ?>
                                                        </div>
                                                
                                                    </div>
                                                </div>
                                            </div>

                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesman']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php    
                                        echo $obj->inputAutoComplete(array( 
                                                                            'objRefer' => $employee,
                                                                            'element' => array('value' => 'salesName',
                                                                                               'key' => 'hidSalesKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-employee.php',
                                                                                                'data' => array('action' =>'searchData', 'issales' => 1)
                                                                                            ) 
                                                                          )
                                                                    );  
                                        ?> 
                                </div> 
                            </div> 

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                <div class="col-xs-9"> 
                                      <?php echo $obj->inputTextArea('address',array('etc' => 'style="height:8em;"')); ?>
                                </div> 
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php  
                                            $popupOpt = (!$isQuickAdd) ? array(
                                                                'url' => 'cityForm.php',
                                                                'element' => array('value' => 'cityName',
                                                                       'key' => 'hidCityKey'),
                                                                'width' => '600px',
                                                                'title' => ucwords($obj->lang['add'] . ' - ' .  $obj->lang['city'])
                                                            )  : '';
                                    
                                            echo $obj->inputAutoComplete(array(
                                                                'objRefer' => $city,
                                                                'revalidateField' => false, 
                                                                'element' => array('value' => 'cityName',
                                                                                   'key' => 'hidCityKey'),
                                                                'source' =>array(
                                                                                    'url' => 'ajax-city.php',
                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                ) ,
                                                                'popupForm' => $popupOpt
                                                              )
                                                        );  
                                    ?> 
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['zipcode']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $obj->inputText('zipCode'); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?> / <?php echo ucwords($obj->lang['mobilePhone']); ?></label> 
                                <div class="col-xs-4" style="padding-right:0"> 
                                    <?php echo $obj->inputText('phone'); ?>
                                </div>  
                                <div class="col-xs-5" style="padding-left:5px"> 
                                    <?php echo $obj->inputText('mobile'); ?>
                                </div> 
                            </div>    
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['fax']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo $obj->inputText('fax'); ?>
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('email'); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                <div class="col-xs-9"> 
                                   <?php echo  $obj->inputTextArea('description',array('etc' => 'style="height:8em;"')); ?> 
                                </div> 
                            </div>   

                        </div>
                        
                        <div class="div-tab-panel"> 
                              <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['shippingAddress']); ?></div>
                              <?php echo $obj->multipleAddressPlugin($rsShippingAddress); ?>  
                       </div>
                        
                    </div>
                  
                    <div class="div-table-col">  
                     
                      <div class="div-tab-panel"> 
                                  <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['websiteAccount']); ?></div>
                                  <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['username']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputText('userName'); ?>  
                                        </div> 
                                   </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['password']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputPassword('password'); ?>   
                                        </div> 
                                   </div> 
						  
                                    <div class="form-group">
                                        <label class="col-xs-3"></label> 
                                        <div class="col-xs-9">  
													<div class="div-table mnv-transaction transaction-detail" style="width:100%;  ">

														<?php
														$totalRows = count($rsDetailAccount);
														for ($i = 0; $i <= $totalRows; $i++) {

															$class =  'transaction-detail-row';
															$overwrite = true;
															$etc = '';
															$style = '';

															if ($i == $totalRows) {
																$class = 'account-row-template row-template';
																$overwrite = false;
																$etc = 'disabled="disabled"';
																$style  = 'style="display:none;"';

															} else {
																$decimal = 0;
																$inputnumber = 'inputnumber';

																$_POST['hidDetailKey[]'] =  $rsDetailAccount[$i]['pkey'];
																$_POST['selRoleTypeKey[]'] =  $rsDetailAccount[$i]['rolekey'];
																$_POST['userNameDetail[]'] =  $rsDetailAccount[$i]['username'];
																$_POST['passwordDetail[]'] =  $rsDetailAccount[$i]['password'];
															}
														?>

															<div class="div-table-row <?php echo $class; ?> odd-style-adjustment "  >
																 <div class="div-table-col detail-col-detail" style="width:10em">
																	<?php echo $obj->inputSelect('selRoleTypeKey[]',$arrCustomerAccountRoleType, array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
																	 <?php echo $obj->inputHidden('hidAccountDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
																</div>
																<div class="div-table-col detail-col-detail">
																	<?php echo $obj->inputText('userNameDetail[]', array('overwritePost' => $overwrite, 'etc' => $etc. ' placeholder="'.$obj->lang['username'].'" ')); ?>
																</div>
																<div class="div-table-col detail-col-detail">
																	<?php echo $obj->inputText('passwordDetail[]', array('overwritePost' => $overwrite, 'etc' =>  $etc. ' placeholder="'.$obj->lang['password'].'" ')); ?>
																</div>
																<div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="account-row-template"')); ?></div>
																<div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>">
																	<?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?>
																</div>

															</div>

														<?php }      
														?>

													</div>  
                                        </div> 
                                   </div> 
						   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['GPSTracker']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputCheckBox('chkShowGPSLocation'); ?>  
                                        </div> 
                                   </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['IDNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputText('IDNumber'); ?>  
                                        </div> 
                                   </div>  
                                  <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['placeAndDateOfBirth']); ?></label> 
                                    <div class="col-xs-6" style="padding-right:0"> 
                                            <?php  echo $obj->inputAutoComplete(array(
                                                            'objRefer' => $city,
                                                            'revalidateField' => false, 
                                                            'element' => array('value' => 'placeOfBirth',
                                                                               'key' => 'hidPlaceOfBirthKey'),
                                                            'source' =>array(
                                                                                'url' => 'ajax-city.php',
                                                                                'data' => array(  'action' =>'searchData' )
                                                                            ) ,
                                                          )
                                                    );  
                                        ?>
                                    </div> 
                                     <div class="col-xs-3" style="padding-left:5px"> 
                                          <?php echo $obj->inputDate('dob', array('etc' => 'style="text-align:center"')); ?>
                                    </div> 
                                </div>   
                                
                               <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['sex']); ?></label> 
                                    <div class="col-xs-9"> 
                                          <?php echo $obj->inputSelect('sex',$arrSex); ?>
                                    </div> 
                                </div>  
						  		
						       <?php if(PLAN_TYPE['categorykey'] == COMPANY_TYPE['retail']) {  ?>
                               <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['point']); ?></label> 
                                    <div class="col-xs-9"> 
                                         <?php echo  $obj->inputNumber('point', array('readonly' => true)); ?>  
                                    </div> 
                                </div>  
						       <?php  }  ?>
                                        
                       </div>
                       
                   		<div class="div-tab-panel"> 
                                <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['financialInformation']); ?></div>
                                 <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currencyPreference']); ?></label> 
                                    <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selCurrencyPreference', $arrCurrency); ?> 
                                    </div> 
                                </div>          
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['top']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selTermOfPayment',$arrTOP); ?>   
                                        </div> 
                                </div>  
                            
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['creditLimit']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputNumber('creditlimit'); ?>   
                                        </div> 
                                </div>  
                                  
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['outstanding']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputNumber('aroutstanding',array('etc' => 'readonly="readonly"')); ?>    
                                        </div> 
                                </div>  
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['paymentTo']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selBank',$arrPaymentMethod); ?>   
                                        </div> 
                                </div> 
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['virtualAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo  $obj->inputText('virtualAccount'); ?>    
                                        </div> 
                                </div> 
                             
                                 <div class="form-group">
                                            <label class="col-xs-3 control-label"></label> 
                                            <div class="col-xs-9"></div> 
                                        </div>  
                                 <div class="form-group"  >
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxIdentificationNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                        <?php echo $obj->inputText('taxid');   ?>    
                                        </div> 
                                </div>   
                                 <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxRegistrationName']); ?></label> 
                                        <div class="col-xs-9"> 
                                        <?php echo $obj->inputText('taxRegistrationName');   ?>    
                                        </div> 
                                </div>  
                                 <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['taxRegistrationAddress']); ?></label> 
                                        <div class="col-xs-9"> 
                                        <?php echo $obj->inputText('taxRegistrationAddress');   ?>    
                                        </div> 
                                </div> 
                               <?php if($isActiveCOA && USE_GL){ ?>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['arAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array(  
                                                                                    'element' => array('value' => 'ARCOA',
                                                                                                       'key' => 'hidARCOAKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    )  
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['downpaymentAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array(  
                                                                                    'element' => array('value' => 'downpaymentCOA',
                                                                                                       'key' => 'hidDownpaymentCOAKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-coa.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
                                                                                                    )  
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
								
								 <?php if ($obj->loadSetting('ICA') == 1) {  ?>
									<div class="form-group">
										<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['ICA']); ?></label> 
										<div class="col-xs-9">  
											<div class="flex" style="min-height:35px">
												<div> <?php echo  $obj->inputCheckBox('chkICA'); ?> </div> 
												<div class="consume ica-group">
													<?php    
															echo $obj->inputAutoComplete(array(   
																				'element' => array('value' => 'ICACOA',
																								   'key' => 'hidICACOAKey'),
																				'source' =>array(
																									'url' => 'ajax-coa.php',
																									'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
																								) 
																			  )
																		);  
													?>
												</div>
											</div> 
										</div> 
									</div>   
<!--
									<div class="form-group ica-group">
										<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['ICACOA']); ?></label> 
										<div class="col-xs-9">  
											<?php    
															echo $obj->inputAutoComplete(array(   
																				'element' => array('value' => 'ICACOA',
																								   'key' => 'hidICACOAKey'),
																				'source' =>array(
																									'url' => 'ajax-coa.php',
																									'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
																								) 
																			  )
																		);  
											?>
										</div> 
									</div>   
-->
<!--
									<div class="form-group ica-group">
										<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['accountsPayable']); ?> <?php echo ucwords($obj->lang['ICA']); ?></label> 
										<div class="col-xs-9"> 
											<div class="flex"> 
												  <div class="consume"> 
													 <?php    
														echo $obj->inputAutoComplete(array(   
																			'element' => array('value' => 'APICACOA',
																							   'key' => 'hidAPICACOAKey'),
																			'source' =>array(
																								'url' => 'ajax-coa.php',
																								'data' => array(  'action' =>'searchData', 'isleaf' => 1 )
																							) 
																		  )
																	);  
                                                ?> 
											 </div>
											</div> 
										</div> 
									</div>    
-->
                            	<?php } ?> 
                            <?php } ?>
							
                             
                                 <div class="form-group">
                                            <label class="col-xs-3 control-label"></label> 
                                            <div class="col-xs-9"></div> 
                                 </div>  
                                 <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['billingEmail']); ?></label> 
                                        <div class="col-xs-9"> 
                                        <?php echo $obj->inputText('billingEmail');   ?>    
                                        </div> 
                                </div> 
                                 <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['billingMobile']); ?></label> 
                                        <div class="col-xs-9"> 
                                        <?php echo $obj->inputText('billingMobile');   ?>    
                                        </div> 
                                </div> 
                              
                       </div>
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['contactPerson']); ?></div>
                            <?php echo $obj->contactPersonPlugin($rsContactPerson); ?>     
                       </div>
 	
						<!-- Form Detail -->
					   <?php 	if((in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])))) {  ?>
                       <div class="div-tab-panel"> 
                            <div class="div-table-caption border-cadet-grey"><?php echo ucwords($obj->lang['alias']); ?></div>
                            <div class="div-table mnv-transaction transaction-detail" style="width:100%;  ">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-header" style="border:0">
                                        <?php echo ucwords($obj->lang['item']) .' / '.ucwords($obj->lang['services']); ?>
                                    </div>
                                    <div class="div-table-col detail-col-header" style="text-align:left;border:0">
                                        <?php echo ucwords($obj->lang['alias']); ?>
                                    </div>
                                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>" style="width:45px; border:0"></div>
                                </div>

                                <?php
                                $totalRows = count($rsDetailItemAlias);
                                for ($i = 0; $i <= $totalRows; $i++) {

                                    $class =  'transaction-detail-row';
                                    $overwrite = true;
                                    $etc = '';

                                    if ($i == $totalRows) {
                                        $class = 'detail-row-template';
                                        $overwrite = false;
                                        $etc = 'disabled="disabled"';
                                    } else {
                                        $decimal = 0;
                                        $inputnumber = 'inputnumber';

                                        $_POST['hidDetailKey[]'] =  $rsDetailItemAlias[$i]['pkey'];
                                        $_POST['hidItemKey[]'] =  $rsDetailItemAlias[$i]['itemkey'];
                                        $_POST['itemName[]'] =  $rsDetailItemAlias[$i]['itemname'];
                                        $_POST['aliasItem[]'] =   $rsDetailItemAlias[$i]['alias'];
                                    }
                                ?>

                                    <div class="div-table-row <?php echo $class; ?>">
                                        <div class="div-table-col detail-col-detail">
                                            <?php echo $obj->inputText('itemName[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidItemKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?><?php echo $obj->inputHidden('hidDetailKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                                        </div>
                                        <div class="div-table-col detail-col-detail">
                                            <?php echo $obj->inputText('aliasItem[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:left;" ' . $etc)); ?>
                                        </div>
                                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>">
                                            <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?>
                                        </div>
                                        <!--onClick="itemAdj.calculateTotal()"-->
                                    </div>

                                <?php }      ?>

                            </div>

                            <div style="clear:both; height:1em;"></div>
                            <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
                        </div>
						<?php } ?>
  	 
                    </div>
             </div>
      </div>  
        <div class="form-button-panel" > <?php echo $obj->generateSaveButton(); ?>  </div>
    </form>  
   
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
