<?php 
require_once '../_config.php';  
require_once '../_include-v2.php';  

includeClass(array('Supplier.class.php','Tax.class.php'));
$supplier = createObjAndAddToCol( new Supplier());

$ap = createObjAndAddToCol( new AP());  
$city = createObjAndAddToCol( new City()); 
$currency = createObjAndAddToCol( new Currency());
$termOfPayment = createObjAndAddToCol( new TermOfPayment()); 
$supplierCategory = createObjAndAddToCol( new SupplierCategory()); 
$tax = createObjAndAddToCol( new Tax()); 

$isActiveCOA = $class->isActiveModule('chartOfAccount'); 
if($isActiveCOA && USE_GL)
	$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());

$obj= $supplier;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
$hasAPAccess = $security->isAdminLogin($ap->securityObject,10);  
$activeModule = $obj->isActiveModule(array('CustomerInsurancePolicy'));
	   
$formAction = 'supplierList';
 
$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$editTermOfPaymentInactiveCriteria = '';
$editSupplierCategoryInactiveCriteria = '';

$rs = prepareOnLoadData($obj);  
$rsContactPerson = array();
$rsSupplierDetail = array();
$rsSupplierBankDetail = array();

$inTELDomain = in_array(DOMAIN_NAME, array('eagle.wintera.co.id','trioeaglelogistic.wintera.co.id','marvel.wintera.co.id','airtel.wintera.co.id'));

if (!empty($_GET['id'])){ 
    
    $id = $_GET['id'];
    $rsContactPerson = $obj->getContactPerson($id);
    $rsSupplierBankDetail = $obj->getSupplierBankDetail($id);
 
    if ($inTELDomain)
		$rsSupplierDetail = $obj->getDetailWithRelatedInformation($id);
    
    $apoustanding = ($hasAPAccess) ? $rs[0]['apoutstanding'] : 0; 
	$_POST['apoutstanding'] = $obj->formatNumber($apoustanding);   
	
	$_POST['selCategory'] =  $rs[0]['categorykey'];   
	$_POST['selPPhType'] =  $rs[0]['pphtype'];
        
	if (!empty($_POST['hidCityKey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['citykey'],true);
		$_POST['cityName'] = $rsCity[0]['name'] .', ' . $rsCity[0]['categoryname'];
	}
    
	if($isActiveCOA && USE_GL){ 
		
		$arrCOAKey = array($rs[0]['apcoakey'],$rs[0]['downpaymentcoakey'],$rs[0]['commissioncoakey'],$rs[0]['icacoakey'] );
		
		$rsCOACol = $chartOfAccount->searchDataRow(array($chartOfAccount->tableName.'.pkey',$chartOfAccount->tableName.'.code',$chartOfAccount->tableName.'.name'),
												' and ' . $chartOfAccount->tableName.'.pkey in ('.$obj->oDbCon->paramString($arrCOAKey ,',').')'
											   );
		$rsCOACol = array_column($rsCOACol, null,'pkey');
		
		$_POST['hidAPCOAKey'] = $rs[0]['apcoakey'];  
		if (!empty($rs[0]['apcoakey'])){  
			$rsCOA = $rsCOACol[$rs[0]['apcoakey']]; 
			$_POST['APCOA'] = $rsCOA['code'] . ' - ' . $rsCOA['name']; 
		}

		$_POST['hidDownpaymentCOAKey'] = $rs[0]['downpaymentcoakey'];  
		if (!empty($rs[0]['downpaymentcoakey'])){ 
			$rsCOA = $rsCOACol[$rs[0]['downpaymentcoakey']]; 
			$_POST['downpaymentCOA'] = $rsCOA['code'] . ' - ' . $rsCOA['name']; 
		}

		$_POST['hidCommissionCOAKey'] = $rs[0]['commissioncoakey'];  
		if (!empty($rs[0]['commissioncoakey'])){ 
			$rsCOA = $rsCOACol[$rs[0]['commissioncoakey']]; 
			$_POST['commissionCOA'] = $rsCOA['code'] . ' - ' . $rsCOA['name']; 
		}
		$_POST['hidICACOAKey'] = $rs[0]['icacoakey'];  
		if (!empty($rs[0]['icacoakey'])){ 
			$rsCOA = $rsCOACol[$rs[0]['icacoakey']]; 
			$_POST['ICACOA'] = $rsCOA['code'] . ' - ' . $rsCOA['name']; 
		}
	 
	}
	
	$editTermOfPaymentInactiveCriteria = ' or '.$termOfPayment->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['termofpaymentkey']);
	$editSupplierCategoryInactiveCriteria =  ' or '.$supplierCategory->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['categorykey']);
}


$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status')); 
$arrTOP = $termOfPayment->generateComboboxOpt(null,array('criteria' =>' and ('.$termOfPayment->tableName.'.statuskey = 1' .$editTermOfPaymentInactiveCriteria.')','order' => ' order by duedays asc')); 
$arrCurrency = $currency->generateComboboxOpt(null,array('criteria' =>' and ('.$currency->tableName.'.statuskey = 1)'));
$arrSupplierCategory = $supplierCategory->generateComboboxOpt(null,array('criteria' =>' and ('.$supplierCategory->tableName.'.statuskey = 1)' .$editSupplierCategoryInactiveCriteria));  
$arrPPh = $tax->generateComboboxOpt(null, array('criteria' => ' and ( ' . $tax->tableName . '.typekey=' . $obj->oDbCon->paramString(TAX_TYPE['PPH']) . ' and ' . $tax->tableName . '.statuskey = 1)', 'order' => 'order by ' . $tax->tableName . '.orderlist asc, ' . $tax->tableName . '.name asc'));


if ($inTELDomain){   
	$container = createObjAndAddToCol( new Container()); 
	$rsContainer = $container->searchData();
	$rsContainer = array_column($rsContainer,'name','pkey');


	$service = createObjAndAddToCol( new Service());
	$rsService = $service->searchData();
	$rsService = array_column($rsService,'name','pkey'); 

	$location = createObjAndAddToCol( new Location());
	$rsLocation = $location->searchData();
	$rsLocation = array_column($rsLocation,'name','pkey');
}


$currencyPreference = array();
$currencyPreference[1] = 'Auto';
$currencyPreference[2] = 'As Invoiced';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>  
<script type="text/javascript">  
	jQuery(document).ready(function(){  
        
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
            var supplier = new Supplier(tabID, <?php echo json_encode(array(
                                                    'rsSupplierBankDetail' => $rsSupplierBankDetail
                                                )); ?>);         
         prepareHandler(supplier);   
         
        
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
                                                message: phpErrorMsg.supplier[1]
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('name');   ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['alias']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('alias');   ?>
                                        </div> 
                                    </div>    
							
                                    <div class="form-group">
    									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selCategory', $arrSupplierCategory); ?> 
                                        </div> 
                                    </div>    
							
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('address1');   ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"></label> 
                                        <div class="col-xs-9"> 
                                             <?php echo $obj->inputText('address2');   ?>
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                        <div class="col-xs-9"> 
                                                <?php   
                                                        $popupOpt = (!$isQuickAdd) ? array(
                                                                'url' => 'cityForm.php',
                                                                'element' => array('value' => 'cityName', 'valueDBField' => 'citycategoryname',
                                                                                    'key' => 'hidCityKey'),
                                                                'width' => '600px',
                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['city'])
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
									<?php if($activeModule['customerinsurancepolicy']){ ?>
									 <div class="form-group"  >
											<label class="col-xs-3 control-label"><?php echo $obj->lang['insuranceCompany']; ?></label> 
											<div class="col-xs-9"> 
											<?php echo $obj->inputCheckBox('chkIsInsurance');   ?>    
											</div> 
									 </div>  
									<?php } ?>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputTextArea('description',array('etc' => 'style="height:8em;"') );   ?> 
                                        </div> 
                                    </div>    
                                 
                                    
                           </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-tab-panel"> 
                              <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['financialInformation']); ?></div>
                              <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currencyPreference']); ?></label> 
                                    <div class="col-xs-9"> 
                                           <?php echo  $obj->inputSelect('selCurrencyPreference', $currencyPreference); ?> 
                                    </div> 
                                </div> 
                              <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['top']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputSelect('selTermOfPayment', $arrTOP); ?> 
                                </div> 
                              </div>   
 
                              <div class="form-group">
                                
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['outstanding']); ?></label> 
                                    <div class="col-xs-9"> 
                                    <?php echo $obj->inputNumber('apoutstanding', array('etc' => 'readonly="readonly"') );   ?>    
                                    </div> 
                            </div>  
                              
                            <div class="form-group">
                                <label class="col-xs-3 control-label"></label> 
                                <div class="col-xs-9"></div> 
                            </div>  
                            
                             <div class="form-group"  >
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['withholdingTaxType']; ?></label> 
                                    <div class="col-xs-9"><?php echo $obj->inputSelect('selPPhType', $arrPPh); ?></div> 
                             </div>  
                            
                             <div class="form-group"  >
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['withholdingTax']; ?></label> 
                                    <div class="col-xs-9"> 
                                    <?php echo $obj->inputCheckBox('chkAutoTax');   ?>    
                                    </div> 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['apAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array(  
                                                                                    'element' => array('value' => 'APCOA',
                                                                                                       'key' => 'hidAPCOAKey'),
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['commissionAPAccount']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array(  
                                                                                    'element' => array('value' => 'commissionCOA',
                                                                                                       'key' => 'hidCommissionCOAKey'),
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
											<div class="flex"  style="min-height:35px">
												  <div> <?php echo  $obj->inputCheckBox('chkICA'); ?> </div>
												  <div class="consume  ica-group"> 
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
								<?php } ?>
<?php } ?>
                        </div>
                        
                        <?php if (!$isQuickAdd) { ?> 
                       <div class="div-tab-panel"> 
                            <div class="div-table-caption border-red"><?php echo ucwords($obj->lang['contactPerson']); ?></div>
                            <?php echo $obj->contactPersonPlugin($rsContactPerson); ?>     
                       </div>
                        <?php } ?>
                    </div>
            </div>
      </div>     
      
      
      
    <div class="div-tab-panel"> 
            <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['bankInformation']); ?></div>
            <div class="div-table mnv-transaction mnv-job transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">
                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['bankName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:300px;"><?php echo ucwords($obj->lang['bankAccountName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:300px;"><?php echo ucwords($obj->lang['bankAccountNumber']); ?></div>
<!--                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['address']); ?></div>-->
                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['branch']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['swift']); ?></div>
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>" style="width:35px;"></div>
                    <div class="div-table-col detail-col-header icon-col <?php echo $obj->hideOnDisabled(); ?>" style="width:35px;"></div>
                </div>

                <?php
                $supplierBankDetail = count($rsSupplierBankDetail);

                for ($i = 0; $i <= $supplierBankDetail; $i++) {

                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $disabled = false;

                    if ($i == $supplierBankDetail) {
                        $class = 'supplier-detail-bank-row-template row-template';
                        $overwrite = false;
                        $disabled = true;
                    } else {
                        $_POST['hidSupplierBankDetailKey[]'] = $rsSupplierBankDetail[$i]['pkey'];
                        $_POST['bankName[]'] =  $rsSupplierBankDetail[$i]['bankname'];
                        $_POST['accountName[]'] =  $rsSupplierBankDetail[$i]['accountname'];
                        $_POST['accountNumber[]'] =  $rsSupplierBankDetail[$i]['accountnumber'];
                        $_POST['address[]'] =  $rsSupplierBankDetail[$i]['address'];
                        $_POST['branch[]'] =  $rsSupplierBankDetail[$i]['branch'];
                        $_POST['swift[]'] =  $rsSupplierBankDetail[$i]['swift'];
                    }
                ?>

                    <div class="div-table-row <?php echo $class; ?>" style="">
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputHidden('hidSupplierBankDetailKey[]', array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                            <?php echo $obj->inputText('bankName[]', array('overwritePost' => $overwrite, 'allowedStatusForEdit' => array(1))); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputText('accountName[]', array('overwritePost' => $overwrite)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputText('accountNumber[]', array('overwritePost' => $overwrite)); ?>
                        </div>
<!--
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputText('address[]', array('overwritePost' => $overwrite)); ?>
                        </div>
-->
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputText('branch[]', array('overwritePost' => $overwrite)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail">
                            <?php echo $obj->inputText('swift[]', array('overwritePost' => $overwrite)); ?>
                        </div>
                        <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnAddDetailRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="supplier-detail-bank-row-template"')); ?></div>
                        <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"')); ?></div>
                    </div>

                <?php } ?>
            </div>
      </div>
            
      
      <?php if ( $inTELDomain ) { ?> 
      <div style="clear:both; height:1em;"></div> 
            
        <div class="div-tab-panel"> 
            <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['servicesPrice']); ?></div>
      <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">  
					<div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['service']); ?></div> 
                    <div class="div-table-col detail-col-header"  style="width:100px;"><?php echo ucwords($obj->lang['container']); ?></div> 
                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['location']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px;"><?php echo ucwords($obj->lang['currency']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                  </div>
        
                    <?php  
                        $totalRows = count($rsSupplierDetail); 

                        for ($i=0;$i<=$totalRows; $i++){  

                            $class =  'transaction-detail-row';
                            $overwrite = true;
                            $disable = '';  
                            $numberClass = 'inputnumber';
                            if ($i == $totalRows ){
                                $class = 'row-template detail-row-template';
                                $overwrite = false; 
                                $disable = 'disabled="disabled"';
                                
                            } else { 
								$_POST['hidDetailKey[]'] =  $rsSupplierDetail[$i]['pkey'];
                                $_POST['hidLocationDetailKey[]'] =  $rsSupplierDetail[$i]['locationkey'];  
                                $_POST['locationDetailName[]'] =  $rsLocation[$rsSupplierDetail[$i]['locationkey']];
								$_POST['hidContainerDetailKey[]'] =  $rsSupplierDetail[$i]['itemkey'];  
                                $_POST['containerDetailName[]'] =  $rsContainer[$rsSupplierDetail[$i]['itemkey']]; 
                                $_POST['hidServiceKey[]'] =  $rsSupplierDetail[$i]['servicekey']; 
                                $_POST['serviceName[]'] =  $rsService[$rsSupplierDetail[$i]['servicekey']];
                                $_POST['price[]'] = $obj->formatNumber($rsSupplierDetail[$i]['price']);
								$_POST['selDetailCurrency[]'] =  $rsSupplierDetail[$i]['currencykey']; 

                            } 

                    ?>


                    <div class="div-table-row <?php echo $class; ?>"> 
						<div class="div-table-col detail-col-detail">
							<?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
							<?php echo $obj->inputHidden('hidServiceKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
							<?php echo $obj->inputText('serviceName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
						</div> 
						<div class="div-table-col detail-col-detail">
							<?php echo $obj->inputHidden('hidContainerDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
							<?php echo $obj->inputText('containerDetailName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
						</div> 
						<div class="div-table-col detail-col-detail">
							<?php echo $obj->inputHidden('hidLocationDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
							<?php echo $obj->inputText('locationDetailName[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
						</div>
						<div class="div-table-col detail-col-detail">
							<?php echo $obj->inputSelect('selDetailCurrency[]',$arrCurrency, array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>
						</div>
						<div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('price[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' , 'disabled' =>  $disable, 'class' =>'form-control ' . $numberClass)); ?></div>
						<div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
					</div>

                <?php } ?>    
                   
       </div>        
      </div>
	  <div style="clear:both; height:1em;"></div> 
      <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       <?php } ?>
      
      <div class="form-button-margin"></div>
      <div class="form-button-panel" > <?php echo $obj->generateSaveButton(); ?>  </div>  
    </form>
     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
