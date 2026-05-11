<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
includeClass('SalesOrderRental.class.php');
$salesOrderRental = createObjAndAddToCol( new SalesOrderRental()); 
$salesRentalQuotation = createObjAndAddToCol( new SalesRentalQuotation()); 
$customer = createObjAndAddToCol( new Customer()); 
$employee = createObjAndAddToCol( new Employee()); 
$location = createObjAndAddToCol( new Location()); 
$city = createObjAndAddToCol( new City()); 
$warehouse = createObjAndAddToCol( new Warehouse()); 
$itemUnit = createObjAndAddToCol( new ItemUnit()); 
$timeUnit = createObjAndAddToCol( new TimeUnit()); 
$item = createObjAndAddToCol( new Item()); 


$obj = $salesOrderRental;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$overwriteContractAllowed = $security->isAdminLogin($salesOrderRental->overwriteContractSecurityObject,10);

$formAction = 'salesOrderRentalList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$editWarehouseInactiveCriteria = ''; 
$editSalesInactiveCriteria = ''; 
$editCityInactiveCriteria = ''; 
 
$rsSalesDetail = array();
$rsPaymentMethodDetail = array();

$_POST['trDate'] = date('d / m / Y');

$saleskey = base64_decode($_SESSION[$obj->loginAdminSession]['id']); 
$_POST['selSalesKey'] = $saleskey;
 
$rs = prepareOnLoadData($obj);  

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
	 
    $_POST['selCustomCode'] = $rs[0]['customcodekey']; 
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
	$_POST['customerName'] = $rsCustomer[0]['name'] ;
	$_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;  
	$_POST['trDesc'] = $rs[0]['trdesc'];
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']);  
    
	if(!$overwriteContractAllowed){
		$_POST['hidSalesQuotationKey'] = $rs[0]['refkey'] ; 
		if(!empty($_POST['hidSalesQuotationKey'])){
			$rsQuotation = $salesRentalQuotation->getDataRowById($rs[0]['refkey']); 
			$_POST['salesQuotationCode'] = $rsQuotation[0]['code'] ;
			$_POST['quotationName'] = $rsQuotation[0]['name'] ;
		}
	}else{
		$_POST['chkIsUnlimited'] = $rs[0]['isunlimited'];
		$_POST['termDetail'] = $rs[0]['termdetail'];
	}

	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	//$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']); 
	$_POST['hidSalesKey'] = $rs[0]['saleskey'];
	if(!empty($rs[0]['saleskey'])){ 
   	    $rsSales = $employee->getDataRowById($rs[0]['saleskey']);
	   $_POST['salesName'] = $rsSales[0]['name'] ; 
    }
    
    $_POST['hidLocationKey'] = $rs[0]['locationkey'];
	if(!empty($rs[0]['locationkey'])){ 
   	   $rsLocation = $location->getDataRowById($rs[0]['locationkey']);
	   $_POST['locationName'] = $rsLocation[0]['name'] ; 
    }
	$_POST['recipientName'] = $rs[0]['recipientname'];
	$_POST['recipientPhone'] = $rs[0]['recipientphone'];
	$_POST['recipientEmail'] = $rs[0]['recipientemail'];
	$_POST['recipientAddress'] = $rs[0]['recipientaddress'];
	$_POST['hidRecipientCityKey'] = $rs[0]['recipientcitykey'];
    if(!empty($rs[0]['recipientcitykey'])){ 
   	    $rsCity = $city->searchData($city->tableName.'.pkey',$rs[0]['recipientcitykey'],true);
	    $_POST['recipientCityName'] = $rsCity[0]['citycategoryname']; 
    }
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editSalesInactiveCriteria = 'or '.$employee->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['saleskey']);
 
}


if (!empty($_GET['id']) && ($_POST['selStatus']==2 || $_POST['selStatus']==3 )){ 
    $_POST['action'] = 'resendEmail';
}

$rsKey = $obj->getTableKeyAndObj($obj->tableName);

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
$arrSales = $obj->convertForCombobox($employee->searchData('','',true, ' and ('.$employee->tableName.'.statuskey = 2 ' .$editSalesInactiveCriteria.')'),'pkey','name'); 
$arrDefaultUnit = $obj->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name'); 
$arrTimeUnit = $timeUnit->searchData('','',true, ' and ('.$timeUnit->tableName.'.statuskey = 1 )');
$arrDefaultTimeUnit = $obj->convertForCombobox($arrTimeUnit,'pkey','name');  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = selectedTab.newPanel[0].id;
        
        salesOrderRental = new SalesOrderRental(tabID,<?php echo json_encode($rs); ?>,<?php echo $overwriteContractAllowed; ?>);
        prepareHandler(salesOrderRental); 
        
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
    <?php echo $obj->inputHidden('hidSendEmail'); ?>
    <?php echo $obj->inputHidden('hidCreditLimit'); ?>
    <?php echo $obj->inputHidden('marketplaceKey'); ?>
    <?php echo $obj->inputHidden('hidTransVoucherKey'); ?>
     
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
								 	<?php if (!$overwriteContractAllowed) { ?> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['quotationCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $salesRentalQuotation,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'salesQuotationCode',
                                                                                                   'key' => 'hidSalesQuotationKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-sales-rental-quotation.php',
                                                                                                    'data' => array(  'action' =>'searchData', 'statuskey' =>'(2,3)')
                                                                                                ) ,
                                                                                'callbackFunction' => 'getTabObj().updateQuotationDetail(); getTabObj().updateQuotationInformation();'                                                                          
                                                                                )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['quotationName']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('quotationName', array('readonly' => true)); ?> 
                                        </div> 
                                    </div> 
								 	<?php } ?> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true,
                                                                                'readonly' => !$overwriteContractAllowed,
                                                                                'element' => array('value' => 'customerName',
                                                                                                   'key' => 'hidCustomerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                    'url' => 'customerForm.php',
                                                                                                    'element' => array('value' => 'customerName',
                                                                                                           'key' => 'hidCustomerKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
                                                                                                ),
                                                                                'callbackFunction' => 'getTabObj().updateSalesman(); getTabObj().updateRecipients()'
                                                                              )
                                                                        );  
                                            ?> 
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
                                                                                            'data' => array(  'action' =>'searchData' , 
                                                                                                              'issales' => 1 )
                                                                                        )  
                                                                                      )
                                                                                );  
                                            ?>  
                                        </div> 
                                    </div>
								 	<?php if ($overwriteContractAllowed) { ?> 
								 	<div class="form-group">
										<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['lumpSum']); ?></label> 
										<div class="col-xs-9"> 
											<?php echo $obj->inputCheckBox('chkIsUnlimited'); ?> 
										</div> 
									</div>
								 	<?php } ?> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php                
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'objRefer'=>$location,
                                                                                        'revalidateField' => false, 
                                                                                        'element' => array('value' => 'locationName',
                                                                                                           'key' => 'hidLocationKey'),
                                                                                        'source' =>array(
                                                                                            'url' => 'ajax-location.php',
                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        )  
                                                                                      )
                                                                                );  
                                            ?>  
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
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['shippingInformation']); ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientName'); ?>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['phone']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientPhone'); ?> 
                                </div> 
                            </div> 
							
							 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['email']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('recipientEmail'); ?>  
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['city']); ?></label> 
                                <div class="col-xs-9"> 
                                     <?php  echo $obj->inputAutoComplete(array(
                                                                'objRefer' => $city,
                                                                'revalidateField' => false, 
                                                                'element' => array('value' => 'recipientCityName',
                                                                                   'key' => 'hidRecipientCityKey'),
                                                                'source' =>array(
                                                                                    'url' => 'ajax-city.php',
                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                )                                                               
                                                                )
                                                        );  
                                    ?> 
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('recipientAddress', array('etc' => 'style="height:10em;"')); ?> 
                                </div> 
                            </div>
						 
                        </div> 
                    </div>
           </div>
      </div>  

        <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['service']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['delivered']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; padding-left: 13px"><?php echo ucwords($obj->lang['timeUnit']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
					<div class="div-table-col detail-col-header" style="width:60px; "></div>
					<div class="div-table-col detail-col-header" style="width:60px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                    
                    <div class="div-table-col detail-col-header" style="width:130px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                </div>
                
				<?php 
                    $totalRows = count($rsSalesDetail); 
            
                    for ($i=0;$i<=$totalRows; $i++){  
							
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        $readonly = false; 
						if(!$overwriteContractAllowed)
							$readonly = true;
						
                        $arrUnit = $arrDefaultUnit;
						$delButton = ($overwriteContractAllowed) ? $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : $obj->inputHidden('btnDeleteRows' ,  array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) ;
                        
                        $timeunitname = $arrTimeUnit[0]['name'];
                        $arrTimeUnit = $arrDefaultTimeUnit;
                         
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true;   
                        } else {  
                            $decimal = 0;
                            $inputnumber = 'inputnumber';
 
                            
                            $_POST['hidDetailKey[]'] =  $rsSalesDetail[$i]['pkey'];
                            $_POST['hidItemKey[]'] =  $rsSalesDetail[$i]['itemkey']; 
                            $_POST['itemName[]'] =  $rsSalesDetail[$i]['itemname']; 
                            $_POST['qty[]'] =   $obj->formatNumber($rsSalesDetail[$i]['qty']); 
                            $_POST['deliveredQtyInBaseUnit[]'] =   $obj->formatNumber(($rsSalesDetail[$i]['deliveredqtyinbaseunit'])); 
                            $_POST['totalDays[]'] =   $obj->formatNumber($rsSalesDetail[$i]['totaldays']); 
                            //$_POST['hidGramasi[]'] =   $obj->formatNumber($rsSalesDetail[$i]['gramasi']); 
                            //$_POST['hidGramasiSubtotal[]'] =   $obj->formatNumber($rsSalesDetail[$i]['gramasi'] * $rsSalesDetail[$i]['qtyinbaseunit']); 
                            $_POST['priceInUnit[]'] =   $obj->formatNumber($rsSalesDetail[$i]['priceinunit']); 
                            $_POST['totalDays[]'] =   $obj->formatNumber($rsSalesDetail[$i]['totaldays']);  
                            $_POST['detailSubtotal[]'] =   $obj->formatNumber($rsSalesDetail[$i]['total']); 
						
								$_POST['selUnit[]'] =  $rsSalesDetail[$i]['unitkey'];
								
							
                             
                            
							$_POST['selTimeUnit[]'] =  $rsSalesDetail[$i]['timeunitkey'];  
                                      
                            $arrUnit = $obj->convertForCombobox($item->getAvailableUnit($rsSalesDetail[$i]['itemkey']),'conversionunitkey','unitname','',array('relconversionmultiplier' => 'conversionmultiplier')); 
                            $timeunitname = $rsSalesDetail[$i]['timename'];
                        } 
				 
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite,'readonly' => $readonly , 'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidItemKey[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidGramasi[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidGramasiSubtotal[]',array('overwritePost' => $overwrite,'disabled' => $disabled)); ?>
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled)); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"','readonly' => $readonly , 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('deliveredQtyInBaseUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"','readonly' => true , 'disabled' => $disabled)); ?></div>
                    
					<div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selUnit[]',$arrUnit, array('overwritePost' => $overwrite,'readonly' => $readonly ,  'disabled' => $disabled)); ?></div>
                 
					<div class="div-table-col detail-col-detail">
                        <div class="flex">
                          
                   <?php echo $obj->inputSelect('selTimeUnit[]',$arrTimeUnit, array('overwritePost' => $overwrite,'readonly' => $readonly , 'disabled' => $disabled)); ?>
                        </div> 
                     </div>
					<div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' ,'readonly' => $readonly , 'disabled' => $disabled)); ?></div>
                    <div class="div-table-col detail-col-detail">
                        <div class="flex">
                            <div>/</div>
                            <div class="consume text-muted time-unit"><?php echo $timeunitname; ?></div> 
                        </div> 
                     </div>
										<div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('totalDays[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' ,'readonly' => $readonly , 'disabled' => $disabled)); ?></div>
                    
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('detailSubtotal[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" ', 'readonly' => true , 'disabled' => $disabled)); ?></div>
                    <!--<div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"> <?php echo $obj->inputHidden('btnDeleteRows' ,  array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?> </div>-->
					<div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?>"><?php echo $delButton;  ?></div>

                </div>
                 
            <?php } ?> 
                   
         </div>        
          
          <div style="clear:both; height:1em;"></div> 
	  	  <?php if ($overwriteContractAllowed) { ?> 
          <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
       	  <?php } ?> 
          <div style="float:right;">   
                   <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['total']); ?> 
                        </div>  
                        <div class="div-table-col-3" style="width:130px;"> 
                            <?php echo $obj->inputNumber('total', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                        </div>
                        <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                    </div>
                 <div style="clear:both"></div> 
          </div>
       <?php if ($overwriteContractAllowed) { ?>   
      <div class="div-tab-panel" style="margin-top:8em">  
         <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['termsandagreements']); ?></div>
        <div class="form-group">
             <div class="col-xs-12">  
                 <?php echo  $obj->inputEditor('termDetail',array('multilang' => true )); ?> 
             </div>
        </div>
    </div>
	<?php } ?> 
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?> 
         </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
