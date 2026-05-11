<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('EMKLQuotationOrder.class.php');
$emklQuotationOrderDomestic = createObjAndAddToCol(new EMKLQuotationOrder(EMKL['jobType']['domestic']));

$container = createObjAndAddToCol(new Container());
$port = createObjAndAddToCol(new Port());
$customer = createObjAndAddToCol(new Customer());
$currency = createObjAndAddToCol(new Currency());
$warehouse = createObjAndAddToCol(new Warehouse());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$supplier = createObjAndAddToCol(new Supplier());
$location = createObjAndAddToCol(new Location());
$city = createObjAndAddToCol(new City()); 
$termsAndConditions = createObjAndAddToCol(new TermsAndConditions());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$currencyRate = createObjAndAddToCol(new CurrencyRate());

$obj = $emklQuotationOrderDomestic;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'emklQuotationOrderDomesticList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$editWarehouseInactiveCriteria = ''; 
$editSalesInactiveCriteria = ''; 
$editCurrencyInactiveCriteria = ''; 
$editTransportTypeCriteria = ''; 
$editTermAndConditionCriteria = ''; 
$editContainerCriteria = ''; 
$editCurrencyInactiveCriteria = '';
$editBusinessUnitInactiveCriteria = '';
 
$rsFreightDetail = array();
$rsDestinationDetail = array();

$emptyDateString = '00 / 00 / 0000';
$emptyDate = date($emptyDateString);

$_POST['trDate'] = date('d / m / Y');
$_POST['validDate'] = $emptyDate;


$saleskey = base64_decode($_SESSION[$obj->loginAdminSession]['id']); 
$_POST['hidSalesKey'] = $saleskey;

$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';
$dateReturnOnEmpty = array('returnOnEmpty'=>true, 'value' => '00 / 00 / 0000');

$totalWeight = 0;
                                           
$rs = prepareOnLoadData($obj);  

$_POST['selTypeOfJob'] = EMKL['jobType']['domestic'];

$jobTypeName = $obj->getJobType(EMKL['jobType']['domestic']);
$_POST['typeOfJob'] = $jobTypeName[0]['name'];

$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');    
$rsItemFile = array();
$rsTermsAndCondition = array();
$rsOriginDetail = array();
$rsPIC = array();
$rsVolumeDetail = array();
$rsContainerSummary = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	
    $rsPIC = $customer->getContactPerson($rs[0]['customerkey']);
    
	$rsTermsAndCondition = $obj->getDetailTermAndCondition($id);

	$rsOriginDetail = $obj->getDetailOriginInformation($id); 
	$rsOriginDetail = $obj->reindexDetailCollections($rsOriginDetail,'polpodkey');
    $rsOriginDetail = array_values($rsOriginDetail);
	
	$rsVolumeDetail = $obj->getDetailVolume($id);

	$rsDestinationDetail = $obj->getDetailDestinationInformation($id);
    $rsDestinationDetail = $obj->reindexDetailCollections($rsDestinationDetail,'polpodkey');
    $rsDestinationDetail = array_values($rsDestinationDetail);   
    
	$rsFreightDetail = $obj->getDetailFreight($id);
	$rsFreightDetail = $obj->reindexDetailCollections($rsFreightDetail,'polpodkey');
    $rsFreightDetail = array_values($rsFreightDetail);

    $rsContainerSummary = $obj->getTotalContainerJobOrderSummary($id, $rs[0]['loadcontainertypekey']);



    $_POST['validDate'] = $obj->formatDBDate($rs[0]['expdate'],'d / m / Y', $dateReturnOnEmpty);  
    $_POST['revision'] = $rs[0]['revision'];
    $_POST['shipmentTermKey'] = $rs[0]['shipmenttermkey'];

    if (!empty($rs[0]['customerkey'])){
        $rsShipper = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['hidCustomerKey'] = $rs[0]['customerkey'];
        $_POST['shipperName'] = $rsShipper[0]['name'];
    }
    
     if (!empty($rs[0]['quotationkey'])){
        $rsQuotation = $obj->getDataRowById($rs[0]['quotationkey']);
        $_POST['hidQuotationKey'] = $rs[0]['quotationkey'];
        $_POST['quotationOrderCode'] = $rsQuotation[0]['code'];
    }
   
    if (!empty($rs[0]['polkey'])){
        $rsPOL = $port->getDataRowById($rs[0]['polkey']);
        $_POST['hidPOLKey'] = $rs[0]['polkey'];
        $_POST['polName'] = $rsPOL[0]['name'];
    }
    
    if (!empty($rs[0]['podkey'])){
        $rsPOD = $port->getDataRowById($rs[0]['podkey']);
        $_POST['hidPODKey'] = $rs[0]['podkey'];
        $_POST['podName'] = $rsPOD[0]['name'];
    } 
    
    if (!empty($rs[0]['finalpodkey'])){
        $rsFinalPOD = $port->getDataRowById($rs[0]['finalpodkey']);
        $_POST['hidFinalPODKey'] = $rs[0]['finalpodkey'];
        $_POST['finalPODName'] = $rsFinalPOD[0]['name'];
    } 
  

    if (!empty($rs[0]['customerkey'])){
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
        $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;
    }    
 
    $_POST['selPIC'] = $rs[0]['pickey'] ;
     
    $_POST['hidSalesKey'] = $rs[0]['saleskey'];
	if(!empty($rs[0]['saleskey'])){ 
   	    $rsSales = $employee->getDataRowById($rs[0]['saleskey']);
	   $_POST['salesName'] = $rsSales[0]['name'] ; 
    }


     if (!empty($rs[0]['itemkey'])){
        $rsItem = $container->getDataRowById($rs[0]['itemkey']);
        $_POST['hidContainerKey'] = $rs[0]['itemkey'];
        $_POST['containerName'] = $rsItem[0]['name'];
    }

    $_POST['volume'] = $obj->formatNumber($rs[0]['totalvolume'], 2);


    $_POST['chkIsShowCurrency'] = $rs[0]['isshowcurrency'];     
	$_POST['selCurrency'] = $rs[0]['currencykey']; 
	$_POST['currencyRate'] = $obj->formatNumber($rs[0]['rate'],2);
	$_POST['hidCurrentCurrencyKey'] = $rs[0]['currencykey'] ;      
	$_POST['selContainerType'] = $rs[0]['loadcontainertypekey'];

	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
	$editSalesInactiveCriteria = 'or '.$employee->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['saleskey']);
	$editTermAndConditionCriteria = ' and '.$termsAndConditions->tableName.'.categorykey = ' . $obj->oDbCon->paramString($rs[0]['jobtypekey']);
 
    

    $rsItemFile = $obj->getFileDetail($id);
 	$obj->prepareLoadedFile($id,array('file' => $rsItemFile ));

    $rsTotSum = $obj->getTotalContainerJobOrderSummary($id);

}else{
	$_POST['chkIsShowTOP'] = 1;
}

$arrOptPIC = array();
$arrOptPIC[0]['pkey'] = 0;
$arrOptPIC[0]['name'] = '----------';
$arrPIC = array_merge($arrOptPIC,$rsPIC);

$arrPIC = $obj->generateComboboxOpt(array('data' => $arrPIC ));	


$rsCurrency = $currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1'.$editCurrencyInactiveCriteria.')');
$arrCurrencyName = array_column($rsCurrency,null,'pkey');
$arrContainers = $container->searchDataRow(array($container->tableName.'.pkey',$container->tableName.'.name'), ' and ('.$container->tableName.'.statuskey = 1 ) ', 'order by '.$container->tableName.'.orderlist asc');

$arrCurrency = $obj->generateComboboxOpt(array('data' => $rsCurrency));	
$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(), 'label'=> 'status'));	

$arrContainer = $container->generateComboboxOpt(null,array('criteria' =>' and ('.$container->tableName.'.statuskey = 1 )'));
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' =>' and ('.$warehouse->tableName.'.statuskey = 1 '.$editWarehouseInactiveCriteria.')'));
$arrFreight = $obj->generateComboboxOpt(array('data' => $obj->getFreightTerm()));	
//$arrTOS = $obj->generateComboboxOpt(array('data' => $obj->getTermOfShipment()));	

$arrCargoType = $obj->generateComboboxOpt(array('data' => $obj->getCargoType()));	

$arrTransportType = $obj->generateComboboxOpt(array('data' => $obj->getTransportationType()));	
$arrType = $obj->generateComboboxOpt(array('data' => $obj->getEmklType('',$editTransportTypeCriteria)));	

$arrTerm = $termsAndConditions->generateComboboxOpt(null,array('criteria' =>' and ('.$termsAndConditions->tableName.'.statuskey = 1 and '.$termsAndConditions->tableName.'.categorykey = 1 )'.$editTermAndConditionCriteria.''));
$arrUnit = $itemUnit->generateComboboxOpt(null,array('criteria' =>' and ('.$itemUnit->tableName.'.statuskey = 1) and '.$itemUnit->tableName.'.unittype = 3'));
$arrJobType = $obj->generateComboboxOpt(array('data' => $obj->getJobType()));	
//$arrShipmentTerm = $obj->generateComboboxOpt(array('data' => $obj->getShipmentTerm()));	
$totalCols = count($arrContainers)  ;

$rsContainer = $container->searchData();
$arrContainerVolume = $class->convertForCombobox($rsContainer, 'pkey', 'name');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 <style>
    /*
    .destination-row-header .div-table-col {vertical-align: middle}
    .destination-row-detail .div-table-col {vertical-align: top}
    .freight-row-detail .div-table-col {vertical-align: top}
    .origin-row-detail .div-table-col {vertical-align: top}
    */

    /*.destination-row-header > .div-table-col {vertical-align: middle; vertical-align: top}*/
    .subpanel {background:rgba(222,222,222, 0.6); border-radius: 0.5em; padding: 0.3em}
    .btn-primary.btn-hbl {padding: 2px !important; min-width: 0;}
    .row-panel {border-radius: 0.5em; padding:0.5em; border:1px solid #dedede; }
</style> 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = <?php echo (isset($isQuickAdd) && $isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  ;
        var varConstant = { 
                            EMKL : <?php echo json_encode(EMKL); ?>,
                            CURRENCY : <?php echo json_encode(CURRENCY); ?>,                        
                            LOCTYPE : <?php echo json_encode(LOC_TYPE); ?>,

                            }
		var tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;  
		var opt = {};

		opt.uploadFileFolder = "<?php echo $obj->uploadFileFolder; ?>"; 
		opt.fileUploaderTarget = "item-file-uploader"; 
		opt.arrFile =  <?php echo json_encode(array_column($rsItemFile,'file')); ?>;  
		opt.noLocation =  "<?php echo $obj->lang['noLocation'] ?>";  

        var emklQuotationOrder = new EMKLQuotationOrder(tabID, <?php echo json_encode(
                                                                array(
                                                                    'rs' => $rs,
                                                                    'detailFreight' => $rsFreightDetail,
 																	'detailOrigin' => $rsOriginDetail,
                                                                    'detailDestination' => $rsDestinationDetail,
 																	'termsDetail' => $rsTermsAndCondition,
                                                                    'volumeDetail' => $rsVolumeDetail
                                                                ) 
                                                            ); ?> ,varConstant,tablekey,opt); 
            
        prepareHandler(emklQuotationOrder); 
        
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
        <?php echo  $obj->inputHidden('selTypeOfJob'); ?>
    <?php echo $obj->inputHidden('hidCurrentCurrencyKey'); ?>

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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?> / <?php echo ucwords($obj->lang['revision']); ?> </label> 
                                        <div class="col-xs-9">
											<div class="flex">
												<div class="consume"><?php echo $obj->inputAutoCode('code'); ?></div>
												<div style="width:5em"><?php echo $obj->inputNumber('revision', array('readonly' => true, 'add-class' => 'inputautodecimal')); ?></div>
											</div> 
                                        </div>
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['validUntil']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('validDate',array('allowEmpty' => true)); ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
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
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                    'url' => 'customerForm.php',
                                                                                                    'element' => array('value' => 'customerName',
                                                                                                           'key' => 'hidCustomerKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
                                                                                                ),
                                                                                'callbackFunction' => 'getTabObj().updateContactInformation()'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
								 
                                  <div class="form-group ">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['PIC']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selPIC', $arrPIC); ?>
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
                                   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['jobType']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                            <div  class="consume"><?php echo  $obj->inputText('typeOfJob', array( 'readonly' => true)); ?></div>
                                            <div ><?php echo  $obj->inputSelect('selAirSea', $arrTransportType )?></div>
                                            <div style="width:150px;"><?php echo  $obj->inputSelect('selContainerType', $arrType); ?></div>
<!--                                            <div> <?php echo $obj->inputSelect('hidCargoType', $arrCargoType); ?> </div>-->
                
                                            </div>
                                        </div> 
                                    </div>    

                                 	<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['currency']); ?> / <?php echo ucwords($obj->lang['currencyRate']); ?></label> 
                                        <div class="col-xs-9  mnv-currency"> 
                                           <div class="flex">
                                                <div><?php  echo $obj->inputCheckBox('chkIsShowCurrency'); ?></div>
                                                <div><?php  echo $obj->inputSelect('selCurrency', $arrCurrency, array('class' => 'form-control input-currency')); ?></div>
                                               <div class="consume"><?php echo $obj->inputDecimal('currencyRate', array('class'=>'form-control inputnumber input-currency-rate')); ?></div>
                                           </div>
                                        </div> 
                                    </div> 
                                   
                                 	<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>  
                                        </div> 
                                    </div> 
								
								 
                                   <div class="form-group" style="padding-top:2em">
                                        <div class="col-xs-3"></div>
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputButton('btnImport', $obj->lang['showAll'],array('class' =>'btn btn-primary btn-second-tone')); ?>
                                        </div> 
                                    </div>   
                           
                        </div>
                        <div class="div-tab-panel">
                                        <div class="div-table" style="width:100%">
                                            <div class="div-table-caption border-purple"><?php echo ucwords($obj->lang['file']); ?></div>
                                            <div class="div-table-row">
                                                <div class="div-table-col-5">
                                                    <!-- file uploader -->
                                                    <div class="item-file-uploader">
                                                        <ul class="file-list"></ul>
                                                        <div style="clear:both; height:1em;"></div>
                                                        <div class="file-uploader">
                                                            <noscript>
                                                                <p>Please enable JavaScript to use file uploader.</p>
                                                            </noscript>
                                                        </div>
                                                    </div>
                                                    <!-- file uploader -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                         
                    </div>
                     <div class="div-table-col">  
                        
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['quotaOrTarget']); ?></div>
                        
                                <div class="form-group lcl-only lclnc">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['volume']); ?></label> 
                                    <div class="col-xs-9">   
                                        <div class="flex">
                                            <div class="consume"><?php echo  $obj->inputDecimal('volume'); ?></div>
                                            <div class="text-muted" style="margin-right:20px">CBM</div> 
                                            <!-- <div>/</div>
                                            <div style="width: 12em">
                                                <?php //echo $obj->inputSelect('hidContainerKey', $arrContainerVolume); ?>
                                            </div> -->
                                        </div>
                                    </div> 
                                </div>   

                                <div class="form-group truckingfcl">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['volume']); ?></label>

                                        <div class="col-xs-9">
                                            <div class="div-table mnv-transaction transaction-detail" style="width: 100%">
                                                    <?php
                                                    $totalVolumeRows = count($rsVolumeDetail);
                                                    for ($j = 0; $j <= $totalVolumeRows; $j++) {

                                                        $class = 'transaction-detail-row';
                                                        $overwrite = true;
                                                        $readonly = false;
                                                        $disabled = false;
                                                        $style = '';

                                                    if ($j == $totalVolumeRows) {
                                                        $class = 'volume-row-template ';
                                                        $overwrite = false;
                                                        $disabled = true;
                                                        $style = 'style="display:none !important"';
                                                    } else {
                                                        $_POST['hidDetailVolumeKey[]'] = $rsVolumeDetail[$j]['pkey'];
                                                        $_POST['selContainerDetailVolumeKey[]'] = $rsVolumeDetail[$j]['itemkey'];
                                                        $_POST['qtyVolume[]'] = $obj->formatNumber($rsVolumeDetail[$j]['qty']);
                                                    }
                                                    $hideDeleteIcon = '';
                                                    ?>
                                    
                                                <div class="div-table-row <?php echo $class; ?> odd-style-adjustment" <?php echo $style; ?> >
                                                    <div class="div-table-col">
                                                        <div class="flex">
                                                            <div style="width:100px;">
                                                                <?php echo $obj->inputHidden('hidDetailVolumeKey[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                                <?php echo $obj->inputNumber('qtyVolume[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            </div>
                                                            <div class="consume">
                                                                <?php echo $obj->inputSelect('selContainerDetailVolumeKey[]', $arrContainerVolume, array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            </div>
                                                            <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>">
                                                                <?php echo $obj->inputLinkButton('btnAddDetailRow', '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="volume-row-template"')); ?>
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

                                <?php if(!empty($rsContainerSummary)) { ?>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['realization']); ?></label>
                                    
                                        <div class="col-xs-9">
                                            <div class="div-table" style="width:100%">
                                                    <?php
                                                        for($i=0; $i < count($rsContainerSummary); $i++) {
                                                            if($rs[0]['loadcontainertypekey'] == EMKL['emklType']['lclnc']) {
                                                                echo '
                                                                    <div class="div-table-row"> 
                                                                        <div class="div-table-col-5 quota-target-container-summary" style="font-weight:bold;border-bottom:1px solid #dedede;display:none;width:15em" > 
                                                                            '.$rsContainerSummary[$i]['code'].'
                                                                        </div> 
                                                                        <div class="div-table-col-5" style="border-bottom:1px solid #dedede;width:20em" > 
                                                                            '.$obj->formatNumber($rsContainerSummary[$i]['qty'],2).' <span class="text-muted" style="margin-right:15px">CBM</span>
                                                                        </div> 
                                                                    </div> 
                                                                '; 
                                                            } else if(($rs[0]['loadcontainertypekey'] == EMKL['emklType']['fcl']) || ($rs[0]['loadcontainertypekey'] == EMKL['emklType']['trucking'])) {
                                                                echo '
                                                                    <div class="div-table-row"> 
                                                                        <div class="div-table-col-5 quota-target-container-summary" style="font-weight:bold;border-bottom:1px solid #dedede;display:none;width:15em" > 
                                                                            '.$rsContainerSummary[$i]['code'].'
                                                                        </div> 
                                                                        <div class="div-table-col-5" style="border-bottom:1px solid #dedede;width:10em" > 
                                                                            ' . $obj->formatNumber($rsContainerSummary[$i]['qty']) . '
                                                                        </div> 
                                                                        <div class="div-table-col-5" style="border-bottom:1px solid #dedede;" > 
                                                                            <div class="" style="margin-right:0px">'. $rsContainerSummary[$i]['containername'] .'</div> 
                                                                        </div> 
                                                                    </div> 
                                                                ';
                                                            }

                                                        } 
                                                    ?>
                                            </div>
                                            
                                            <div style="float:left;margin-top:5px">
                                                <div class="quota-target-detail-button text-primary" relobj="quota-target-container-summary" relalt="<?php echo ucwords($obj->lang['hideDetail']); ?> "><?php echo ucwords($obj->lang['showDetail']); ?> </div>
                                            </div>


                                        </div>
                                    </div>
                                    
                                <?php } ?>

						</div>                                                        
						       
						<div class="div-tab-panel"> 
                         	<div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['headerText']); ?></div>
                                  
							<div class="form-group">
                                    <div class="col-xs-12">
										<?php echo  $obj->inputEditor('txtHeader'); ?> 
                                    </div>
                                </div>
						 </div>
						 
                        <div class="div-tab-panel"> 
                         	<div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['termsAndConditions']); ?></div>
                                  
							<div class="form-group">
                                    <div class="col-xs-12" id="testTerms">
										<?php echo  $obj->inputEditor('txtTermsAndConditions'); ?>
                                    </div>
                                </div>
						 </div> 
                    </div>
           </div>
      </div>
       <div class="table-container">
                <div class="div-table ">
                    
                     <div class="div-table-row"> 
                         <div class="div-table-col ">
                              <h4 style="width:7.2em"><?php echo ucwords($obj->lang['originCharge']); ?></h4>     
                         </div>
                        <div class="div-table-col" style="padding-top:1.2em; padding-left: 2em" >
                             <div class="div-table">
                                <div class="div-table-row list-container-origin" style="">
                                     <?php 
                                        $zonaRow = count($rsOriginDetail);
                                        $arrShowContainer = array();
                                        $arrPriceContainer = array();

                                        for($k =0;$k<$zonaRow;$k++){ 
                                            
                                            for($l=0;$l<count($rsOriginDetail[$k]);$l++){

												$detailPkey = $rsOriginDetail[$k][$l]['pkey'];
                                                $rsOriginPrice = $obj->getContainerPrice($detailPkey, LOC_TYPE['origin']);
												 
                                            	$_POST['chkContainerOrigin-1'] = ($rsOriginDetail[$k][$l]['rateprice'] > 0) ? 1 : 0 ; 
												
                                                for($c=0;$c<$totalCols;$c++){ 
 
													$arrPriceContainer[$detailPkey] = array();
														
                                                    for($ctr=0;$ctr<count($rsOriginPrice);$ctr++){ 
														$containerkey = $rsOriginPrice[$ctr]['containerkey'];
                                                        $arrShowContainer[$containerkey]['containerkey'] = $rsOriginPrice[$ctr]['containerkey']; 
														 
														$arrPriceContainer[$detailPkey][$containerkey]['price'] = $rsOriginPrice[$ctr]['price'];
                                                    }

                                                }
                                            }
                                        } 
                                    ?>
									
<!--
									<div class="div-table-col  ">
										   <div class="flex">
										   	<div relkey="-1"><?php echo $obj->inputCheckBox('chkContainerOrigin-1',  array('add-class'=>'chklist-container','etc'=>'relcontainerkey="-1"')); ?> </div>
											<div>Rate</div>   
										   </div> 
                                    </div>
-->
                                     
                                       <?php      
									 
                                        for($c=0;$c<$totalCols;$c++){  
                                            $chkIsContainer = (empty($arrShowContainer[$arrContainers[$c]["pkey"]]['containerkey'])) ? 0 : 1;
                                            $_POST['chkContainerOrigin'.$arrContainers[$c]["pkey"].'[]'] = $chkIsContainer;

                                    ?>
                                    <div class="div-table-col ct-item">
										<div class="flex" style="padding-right:1em">
											<div relkey="<?php echo $arrContainers[$c]["pkey"]; ?>"><?php echo $obj->inputCheckBox('chkContainerOrigin'.$arrContainers[$c]["pkey"].'[]', array('add-class'=>'chklist-container','etc'=>'relcontainerkey="'.$arrContainers[$c]["pkey"].'"')); ?></div>
											<div><?php echo $arrContainers[$c]["name"]; ?></div>
										</div> 
                                    </div>              
                                    <?php } ?>
                                            
    
                                </div>
                             </div>            
                         </div>
                 
                    </div>
                </div>
             <div class="div-table "> 
				<div class="div-table-row"> 
					<div class="div-table-col " style="vertical-align:middle;width:150px;font-weight:bold">Pickup (Zona) / Zona</div>
					<div class="div-table-col " style="vertical-align:middle;width:250px;"><?php echo  $obj->inputSelect('selOriginZone', array(), array('add-class'=>'select-location', 'allowedStatusForEdit' => array(1,2,3) )); ?></div>
				</div>
			</div>
            <div class="div-table mnv-transaction transaction-detail no-odd-even-style" style="width:100%;" attr-level="0">

                        <?php  
                            $totalRows = count($rsOriginDetail);
                          
                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row ';
                                $overwrite = true;
                                $disable = '';  
                 
                                $readonlyDetail = false;
                                    
                                    
                                if ($i == $totalRows ){
                                    $class = 'location-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';  
                                } else {   
                                    $_POST['hidDetailOriginKey[]'] =  $rsOriginDetail[$i][0]['pkey']; 
                                    $_POST['hidPickupDetailKey[]'] =  $rsOriginDetail[$i][0]['locationzonekey']; 
                                    $_POST['pickupDetailName[]'] =  $rsOriginDetail[$i][0]['polname']; 
                                    $_POST['hidPickupZoneDetailKey[]'] =  $rsOriginDetail[$i][0]['locationpickupkey']; 
                                    $_POST['pickupZoneDetailName[]'] =  $rsOriginDetail[$i][0]['podname'];  
                                } 

                        ?>

                        <div class="div-table-row destination-row  <?php echo $class; ?> " reldetailkey="<?php echo $rsOriginDetail[$i][0]['pkey']?>"> 
                      <div class="div-table-col" style="padding:0; padding-top:1.5em">   
                            <div class="div-table row-panel" style="width:100%">
                                        <div class="div-table-col detail-col-detail">

                                            <div class="div-table" style="width:100%;"> 
                                                    <div class="div-table-row destination-row-header">   
                                                         <div class="div-table-col" style="width:450px;">
                                                             <div class="div-table"  style="width:96%;">
                                                                <div class="div-table-row location-row">
                                                                    <div class="div-table-col" style="width:120px;font-weight:bold">Pickup (Zona) / Zona</div>

                                                                    <div class="div-table-col" style="">
                                                                    <div class="flex">
                                                                        <div style="width:150px">
                                                                            <?php echo $obj->inputHidden('hidDetailOriginKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>  
                                                                            <?php echo $obj->inputHidden('hidPickupDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?> 
                                                                            <?php echo  $obj->inputText('pickupDetailName[]', array('overwritePost' => $overwrite, 'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>

                                                                        </div>
                                                                        <div >/</div>
                                                         
                                                                        <div style="width:150px">
                                                                           <?php echo $obj->inputHidden('hidPickupZoneDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?> 
                                                                            <?php echo  $obj->inputText('pickupZoneDetailName[]', array('overwritePost' => $overwrite, 'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>
                                                                        </div>

                                                                    </div>

                                                                    </div>

                                                                </div>  
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="div-table transaction-detail detail-item" style="width:100%; border-bottom:1px solid #333;" attr-level="1" attr-group="hidDetailItemOriginKey">

                                           <div class="div-table-row">  
                                                <div class="div-table-col detail-col-header" style="width:50px;text-align:center">Reim</div> 
                                                <div class="div-table-col detail-col-header"  style="width:150px;">Origin <?php echo ucwords($obj->lang['services']); ?></div> 
                                                <div class="div-table-col detail-col-header"  style="width:120px;"><?php echo ucwords($obj->lang['alias']); ?></div> 
                                                <div class="div-table-col detail-col-header"  style="width:100px;"><?php echo ucwords($obj->lang['unit']); ?></div> 
                                                <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['currencyShort']); ?></div>
                                                <div class="div-table-col detail-col-header container-detail type-rate" relheaderkey="-1"  style="width:110px;text-align:right;display:none;">Rate</div> 

                                                  <?php foreach($arrContainers as $row){ ?> 
											   		<div class="div-table-col detail-col-header container-detail  "relHeaderKey="<?php echo $row['pkey']; ?>" style="width:110px;text-align:right;display:none;"><?php echo ucwords($row['name']); ?></div>
											   	  <?php } ?>
											   
                                                <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['note']); ?></div>
                                                <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                                <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                                <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                              </div>
                                            <?php  
                                         
                                             $totalItemRows = count($rsOriginDetail[$i] ?? []);
                    
                                                for ($j=0;$j<=$totalItemRows; $j++){  

                                                    $class =  'transaction-detail-row';
                                                    $overwrite = true;
                                                    $disable = '';  
                                                    $activeCurrencyKey =  CURRENCY['idr'] ;
                                                    $numberClass = 'inputdecimal';
                                                    
                                                    $_POST['chkIsReimburseOrigin[]'] = 0;
                                                    $_POST['chkIncludeTaxOriginDetail[]'] = 0;
                                                    $_POST['chkIncludeTaxOriginCostDetail[]'] = 0;

                                                    $readonly = false;
                                                    if ($j == $totalItemRows ){
                                                        $class = 'origin-row-template row-template';
                                                        $overwrite = false; 
                                                        $disable = 'disabled="disabled"';  

                                                    } else {  

                                                        $_POST['hidDetailItemOriginKey[]'] =  $rsOriginDetail[$i][$j]['pkey'];
                                                        $_POST['hidServiceOriginKey[]'] =  $rsOriginDetail[$i][$j]['servicekey']; 
                                                        $_POST['serviceOriginName[]'] =  $rsOriginDetail[$i][$j]['servicename']; 
                                                        $_POST['aliasOrigin[]'] =  $rsOriginDetail[$i][$j]['alias']; 
                                                        $_POST['chkIsReimburseOrigin[]'] =  $rsOriginDetail[$i][$j]['isperreciept'];  
                                                        $_POST['selCurrencyOriginDetail[]'] =  $rsOriginDetail[$i][$j]['currencykey']; 
                                                        $_POST['hidDetailZoneKey[]'] =  $rsOriginDetail[$i][$j]['locationzonekey'];
                                                        $_POST['hidDetailPickupZoneKey[]'] =  $rsOriginDetail[$i][$j]['locationpickupkey']; 
                                                        $_POST['hidUnitOriginDetailKey[]'] =  $rsOriginDetail[$i][$j]['unitkey']; 
                                                        $_POST['unitOriginDetailName[]'] =  $rsOriginDetail[$i][$j]['unitname'];  
                                                        $_POST['taxPercentageOrigin[]'] =  $obj->formatNumber($rsOriginDetail[$i][$j]['taxpercentage']); 
                                                        $_POST['chkIncludeTaxOriginDetail[]'] =  $rsOriginDetail[$i][$j]['ispriceincludetax'];  
                                                        $_POST['serviceOriginRemarks[]'] =  $rsOriginDetail[$i][$j]['remarks'];  
                                                        $_POST['ratePriceOrigin[]'] =  $obj->formatNumber($rsOriginDetail[$i][$j]['rateprice']); 
                                                        $_POST['hidOrderListOrigin[]'] =  $rsOriginDetail[$i][$j]['orderlist']; 
                                                        $activeCurrencyKey = $rsOriginDetail[$i][$j]['currencykey'];

                                                        $numberClass = 'inputdecimal'; // ($_POST['selCurrencyDetail[]'] == CURRENCY['idr'] ) ? 'inputnumber' : 'inputdecimal';

                                                    } 

                                            ?>

                                            <div class="div-table-row freight-row-detail <?php echo $class; ?>"> 
                                                         <div class="div-table-col detail-col-detail" style="text-align:center"><?php  echo $obj->inputCheckBox('chkIsReimburseOrigin[]',array('overwritePost' => $overwrite,'add-class'=> 'chk-list-reciept','disabled' =>  $disable )); ?></div>

                                                            <div class="div-table-col   detail-col-detail"  style="text-align:center">
                                                                <?php echo $obj->inputHidden('hidDetailItemOriginKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                                <?php echo $obj->inputHidden('hidDetailZoneKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                                <?php echo $obj->inputHidden('hidDetailPickupZoneKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                                <?php echo $obj->inputHidden('hidServiceOriginKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                                <?php echo $obj->inputText('serviceOriginName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' =>  $disable)); ?>
                                                            </div>   
                                                            <div class="div-table-col detail-col-detail">
                                                                <?php echo $obj->inputText('aliasOrigin[]', array('overwritePost' => $overwrite,'readonly' => $readonly, 'disabled' =>  $disable)); ?>

                                                            </div>
                                                            <div class="div-table-col detail-col-detail">
                                                                <?php echo $obj->inputHidden('hidUnitOriginDetailKey[]',array('overwritePost' => $overwrite,'readonly' => $readonly, 'disabled' =>  $disable)); ?>
                                                                <?php echo $obj->inputText('unitOriginDetailName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                            </div>
                                                            <div class="div-table-col detail-col-detail ">
                                                                <?php echo $obj->inputSelect('selCurrencyOriginDetail[]',$arrCurrency, array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                            </div> 
                                                             <div class="div-table-col detail-col-detail container-detail type-rate" style="display:none;" relheaderkey="-1">
                                                                <?php echo $obj->inputNumber('ratePriceOrigin[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'etc'=>'style="text-align:right;margin-bottom:.3em;" ', 'disabled' =>  $disable)); ?>
                                                            </div> 
                                                            <?php 

                                                                $classType = '';       
                                                                for($c=0;$c<$totalCols;$c++){ 
																	 
																	$detailPkey = $rsOriginDetail[$i][$j]['pkey'];
																	$containerkey = $arrContainers[$c]["pkey"];
																	
																	$_POST['containerOrigin_'.$arrContainers[$c]["pkey"].'[]'] =  (empty($arrPriceContainer[$detailPkey][$containerkey]['price'])) ? 0 : $obj->formatNumber($arrPriceContainer[$detailPkey][$containerkey]['price']);
                                                                  
                                                            ?>

                                                                <div class="div-table-col detail-col-detail container-detail "  relheaderkey="<?php echo $arrContainers[$c]["pkey"]; ?>"  style="display:none;">
 																	<?php echo $obj->inputNumber('containerOrigin_'.$arrContainers[$c]["pkey"].'[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'etc'=>'style="text-align:right;" ', 'disabled' =>  $disable)); ?>
                                                                </div> 

                                                            <?php } ?>   
                                                            <div class="div-table-col detail-col-detail"> 
																<?php echo $obj->inputText('serviceOriginRemarks[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' =>  $disable )); ?>
                                                            </div>
															<div class="div-table-col detail-col-detail  icon-col <?php echo $obj->hideOnDisabled(); ?>">
																<div class="flex">
                                                                	<?php echo $obj->inputHidden('hidOrderListOrigin[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable, 'add-class'=>'hid-order-list')); ?>
																	<i class="fas arrow-nav fa-arrow-circle-up" rel="-1"></i>
																	<i class="fas arrow-nav fa-arrow-circle-down " rel="1"></i>
																</div> 
															</div> 
                                                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="origin-row-template"')) ?></div>
                                                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo  $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                                                       </div>   
                                                    <?php } ?> 

                                            </div>


                                    </div>  
                                <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top; padding-top:1em !important"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"','class' => 'btn btn-link remove-button')); ?></div>
                            </div>
                        </div>
                    </div>

                    <?php } ?> 
           
            </div>  
		  <div style="clear:both; height:1em;"></div> 
		  <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddOriginRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone btn-add-detail')); ?></div>   
              
      </div>
      <div style="clear:both; height:5em;"></div> 
        
      <!--FREIGHT-->
      
      <div class="table-container">
          <div class="div-table">
                    
                     <div class="div-table-row"> 
                         <div class="div-table-col "> 
                              <h4  style="width:7.2em"><?php echo ucwords($obj->lang['freight']); ?></h4>     
                         </div>
                        <div class="div-table-col" style="padding-top:1.2em; padding-left: 2em" >
                             <div class="div-table">
                                <div class="div-table-row list-container-origin" style=""> 
                                    <?php
									
									 	$zonaRow = count($rsFreightDetail);
                                        $arrShowContainer = array();
                                        $arrPriceContainer = array();

                                        for($k =0;$k<$zonaRow;$k++){ 
                                            
                                            for($l=0;$l<count($rsFreightDetail[$k]);$l++){

												$detailPkey = $rsFreightDetail[$k][$l]['pkey']; 
												 
                                                $rsFreightPrice = $obj->getContainerPrice($detailPkey, LOC_TYPE['freight']);
												 
                                            	$_POST['chkContainerFreight-1'] = ($rsFreightDetail[$k][$l]['rateprice'] > 0) ? 1 : 0 ; 
												
                                                for($c=0;$c<$totalCols;$c++){ 
 
													$arrPriceContainer[$detailPkey] = array();
														
                                                    for($ctr=0;$ctr<count($rsFreightPrice);$ctr++){ 
														$containerkey = $rsFreightPrice[$ctr]['containerkey'];
                                                        $arrShowContainer[$containerkey]['containerkey'] = $rsFreightPrice[$ctr]['containerkey']; 
														 
														$arrPriceContainer[$detailPkey][$containerkey]['price'] = $rsFreightPrice[$ctr]['price'];
                                                    }

                                                }
                                            }
                                        }  
 
                                    ?>

<!--
									<div class="div-table-col">
                                        <div class="div-table">
                                            <div class="div-table-row">
												
												<div class="div-table-col">
													   <div class="flex">
														<div relkey="-1"><?php echo $obj->inputCheckBox('chkContainerFreight-1',  array('add-class'=>'chklist-container','etc'=>'relcontainerkey="-1"')); ?> </div>
														<div>Rate</div>   
													   </div> 
												</div> 

                                            </div>
                                        </div>
                                    </div>
-->
                            
                                       <?php      
                                        for($c=0;$c<$totalCols;$c++){ 
                                            $chkIsContainer = (empty($arrShowContainer[$arrContainers[$c]["pkey"]]['containerkey'])) ? 0 : 1;
                                            $_POST['chkContainerFreight'.$arrContainers[$c]["pkey"].'[]'] = $chkIsContainer; 
                                    
                                    	?>
									
										<div class="div-table-col ct-item">
											  <div class="div-table">
												<div class="div-table-row">
													<div class="div-table-col">
														   <div class="flex"  style="padding-right:1em">
															<div relkey="<?php echo $arrContainers[$c]["pkey"]; ?>"><?php echo $obj->inputCheckBox('chkContainerFreight'.$arrContainers[$c]["pkey"].'[]', array('add-class'=>'chklist-container','etc'=>'relcontainerkey="'.$arrContainers[$c]["pkey"].'"')); ?></div>
															<div><?php echo $arrContainers[$c]["name"]; ?></div>   
														   </div> 
													</div>   
												 </div>
											</div>
										</div>              
                                    <?php } ?> 
    
                                 </div>

                             </div>
                            
                         </div>
                 
                    </div>
                </div>  
                <div class="div-table ">
               
                    <div class="div-table-row"> 
                        <div class="div-table-col " style="vertical-align:middle;width:150px;font-weight:bold">POL / POD</div>
                        <div class="div-table-col " style="vertical-align:middle;width:500px;"><?php echo  $obj->inputSelect('selFreightZone', array(), array('add-class'=>'select-location', 'allowedStatusForEdit' => array(1,2,3) )); ?></div>
                    </div>
                </div>
          
		  		<div class="div-table mnv-transaction transaction-detail no-odd-even-style" style="width:100%;" attr-level="0">

                        <?php  
                            $totalRows = count($rsFreightDetail); 
					
                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                 
                                $readonlyDetail = false;
 
                                if ($i == $totalRows ){
                                    $class = 'location-freight-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';  
                                } else {  
                                    
                                    $_POST['hidDetailFreightKey[]'] =  $rsFreightDetail[$i][0]['pkey'];
                                    $_POST['hidDetailPOLKey[]'] =  $rsFreightDetail[$i][0]['polkey']; 
                                    $_POST['detailPOLName[]'] =  (!empty($rsFreightDetail[$i][0]['polname'])) ? $rsFreightDetail[$i][0]['polname'] : ''; 
                                    $_POST['hidDetailPODKey[]'] =  $rsFreightDetail[$i][0]['podkey']; 
                                    $_POST['detailPODName[]'] = (!empty($rsFreightDetail[$i][0]['podname'])) ? $rsFreightDetail[$i][0]['podname'] : ''; 
                             
                                    
                                } 
 
                        ?>


                        <div class="div-table-row destination-row <?php echo $class; ?>" reltes="<?php echo $i; ?>" reldetailkey="<?php echo $rsFreightDetail[$i][0]['pkey'];?>"> 
							<div class="div-table-col" style="padding:0; padding-top:1.5em">   
                            <div class="div-table row-panel" style="width:100%"> 
                                        <div class="div-table-col detail-col-detail">

                                            <div class="div-table" style="width:100%;"> 
                                                    <div class="div-table-row destination-row-header">   
                                                         <div class="div-table-col" style="width:450px;">
                                                             <div class="div-table"  style="width:96%;">
                                                                <div class="div-table-row location-row">
                                                                    <div class="div-table-col" style="width:80px;font-weight:bold">POL / POD</div>

                                                                    <div class="div-table-col" style="">
                                                                    <div class="flex">
                                                                        <div style="width:200px">
                                                                            <?php echo $obj->inputHidden('hidDetailFreightKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>  
                                                                            <?php echo $obj->inputHidden('hidDetailPOLKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?> 
                                                                            <?php echo  $obj->inputText('detailPOLName[]', array('overwritePost' => $overwrite, 'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>
                                                                        </div>
                                                                        <div >/</div> 
                                                                        <div style="width:200px">
                                                                           <?php echo $obj->inputHidden('hidDetailPODKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?> 
                                                                            <?php echo  $obj->inputText('detailPODName[]', array('overwritePost' => $overwrite, 'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>
                                                                        </div>

                                                                    </div>

                                                                    </div>

                                                                </div>  
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="div-table transaction-detail detail-item" style="width:100%; border-bottom:1px solid #333;" attr-level="1" attr-group="hidDetailItemFreightKey">

                                                <div class="div-table-row">   
                                                    <div class="div-table-col detail-col-header" style="width:50px;text-align:center">Reim</div> 
                                                    <div class="div-table-col detail-col-header" style="width:150px;"><?php echo ucwords($obj->lang['service']); ?></div>    
                                                    <div class="div-table-col detail-col-header"  style="width:120px;"><?php echo ucwords($obj->lang['alias']); ?></div>                                                     
                                                    <div class="div-table-col detail-col-header" style="width:200px;"><?php echo ucwords($obj->lang['carrier']); ?></div>    
                                                    <div class="div-table-col detail-col-header" style="width:100px;"><?php echo ucwords($obj->lang['unit']); ?></div>
                                                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['currencyShort']); ?></div>
                                                    <div class="div-table-col detail-col-header container-detail type-rate " relheaderKey="-1" style="width:110px;text-align:right;display:none;">Rate</div> 

                                                     <?php  for($a=0;$a<$totalCols;$a++){  ?> 
                                                    <div class="div-table-col detail-col-header container-detail" relheaderKey="<?php echo $arrContainers[$a]['pkey']; ?>" style="width:110px;text-align:right;display:none;"><?php echo ucwords($arrContainers[$a]['name']); ?></div> 
                                                      <?php echo $obj->inputHidden('hidItemFreightKey[]',array('value' => $arrContainers[$a]['pkey'])); ?>
                                                      <?php } ?> 
                                                    <div class="div-table-col detail-col-header" ><?php echo ucwords($obj->lang['note']); ?></div>  
                                                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>     
                                                  </div>
                                            <?php  
                                         
                                                $totalItemsRows = count($rsFreightDetail[$i] ?? []) ;

                                
                                                for ($k=0;$k<=$totalItemsRows; $k++){  
                                                    
                                                    $class =  'transaction-detail-row';
                                                    $overwrite = true;
                                                    $disable = '';  
                                                    $activeCurrencyKey =  CURRENCY['idr'] ;
                                                    $numberClass = 'inputdecimal';

                                                    $_POST['chkIncludeTaxCarrierDetail[]'] = 0;
                                                    $_POST['chkIncludeTaxCarrierCostDetail[]'] = 0;
                                                    $_POST['chkIsReimburseFreight[]'] = 0;
                                                     $readonly = false;

                                                    if ($k == $totalItemsRows ){
                                                        $class = 'item-row-template row-template';
                                                        $overwrite = false; 
                                                        $disable = 'disabled="disabled"'; 
                                                    } else { 

                                                        $_POST['hidDetailItemFreightKey[]'] =  $rsFreightDetail[$i][$k]['pkey']; 
                                                        $_POST['hidServiceFreightKey[]'] =  $rsFreightDetail[$i][$k]['servicekey'];
                                                        $_POST['hidCarrierDetailKey[]'] =  $rsFreightDetail[$i][$k]['carrierkey']; 
                                                        $_POST['carrierDetailName[]'] =  $rsFreightDetail[$i][$k]['carriername']; 
                                                        $_POST['carrierRemarks[]'] =  $rsFreightDetail[$i][$k]['remarks']; 
                                                        $_POST['chkIsReimburseFreight[]'] = $rsFreightDetail[$i][$k]['isperreciept']; 
                                                        $_POST['serviceFreightName[]'] =  $rsFreightDetail[$i][$k]['servicename']; 
                                                        $_POST['aliasCarrier[]'] =  $rsFreightDetail[$i][$k]['alias']; 
                                                        $_POST['taxPercentageCarrier[]'] =  $obj->formatNumber($rsFreightDetail[$i][$k]['taxpercentage']);
                                                        $_POST['chkIncludeTaxCarrierDetail[]'] =  $rsFreightDetail[$i][$k]['ispriceincludetax'];
                                                        $_POST['hidDetailFreightPOLKey[]'] =  $rsFreightDetail[$i][$k]['polkey'];
                                                        $_POST['hidDetailFreightPODKey[]'] =  $rsFreightDetail[$i][$k]['podkey']; 
                                                        $_POST['selCurrencyDetail[]'] =  $rsFreightDetail[$i][$k]['currencykey'];
                                                        $_POST['rateFreight[]'] =  $obj->formatNumber($rsFreightDetail[$i][$k]['rateprice']);  
                                                        $_POST['hidUnitFreightDetailKey[]'] =  $rsFreightDetail[$i][$k]['unitkey']; 
                                                        $_POST['unitFreightDetailName[]'] =  $rsFreightDetail[$i][$k]['unitname'];   
                                                        $_POST['hidOrderListFreight[]'] =  $rsFreightDetail[$i][$k]['orderlist']; 
                                                        $activeCurrencyKey = $rsFreightDetail[$i][$k]['currencykey'];
                                                        $numberClass = 'inputdecimal';

                                                    } 

                                            ?>


                                            <div class="div-table-row freight-row-detail <?php echo $class; ?>"> 
                                                        <div class="div-table-col detail-col-detail" style="text-align:center"><?php echo $obj->inputCheckBox('chkIsReimburseFreight[]', array('overwritePost' => $overwrite, 'add-class' => 'chk-list-reciept', 'disabled' => $disable )); ?></div>
                                                        <div class="div-table-col detail-col-detail"   style="text-align:center">
															<?php echo $obj->inputHidden('hidDetailItemFreightKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
															<?php echo $obj->inputHidden('hidServiceFreightKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
															<?php echo $obj->inputHidden('hidDetailFreightPOLKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
															<?php echo $obj->inputHidden('hidDetailFreightPODKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?> 
															<?php echo $obj->inputText('serviceFreightName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' =>  $disable)); ?>  
                                                        </div>
                                                        <div class="div-table-col detail-col-detail">
                                                            <?php echo $obj->inputText('aliasCarrier[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>

                                                        </div>
                                                        <div class="div-table-col detail-col-detail">
                                                            <?php echo $obj->inputHidden('hidCarrierDetailKey[]',array('overwritePost' => $overwrite,'readonly' => $readonly, 'disabled' =>  $disable)); ?>
                                                            <?php echo $obj->inputText('carrierDetailName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>

                                                        </div>
                                                        <div class="div-table-col detail-col-detail">
                                                            <?php echo $obj->inputHidden('hidUnitFreightDetailKey[]',array('overwritePost' => $overwrite,'readonly' => $readonly, 'disabled' =>  $disable)); ?>
                                                            <?php echo $obj->inputText('unitFreightDetailName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>

                                                        </div>
                                                            <div class="div-table-col detail-col-detail ">
                                                                <?php echo $obj->inputSelect('selCurrencyDetail[]',$arrCurrency, array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                        </div>
                                                      <div class="div-table-col detail-col-detail container-detail type-normal" style="display:none;" relheaderkey="-3">
                                                            <?php echo $obj->inputNumber('normalPriceFreight[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'etc'=>'style="text-align:right;margin-bottom:.3em;" ', 'disabled' =>  $disable)); ?>
                                                        </div> 
                                                        <div class="div-table-col detail-col-detail container-detail type-minimum" style="display:none;" relheaderkey="-2">
                                                            <?php echo $obj->inputNumber('minimumPriceFreight[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'etc'=>'style="text-align:right;margin-bottom:.3em;" ', 'disabled' =>  $disable)); ?>
                                                        </div> 
                                                        <div class="div-table-col detail-col-detail container-detail type-rate" style="display:none;" relheaderkey="-1">
                                                            <?php echo $obj->inputNumber('rateFreight[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'etc'=>'style="text-align:right;margin-bottom:.3em;" ', 'disabled' =>  $disable)); ?>
                                                        </div> 
                                                        <?php 
                                                                
															$classType = '';
                                                            for($c=0;$c<$totalCols;$c++){ 
                                                                 $arrCost = array();
                                                                   
																// sementara tembak sea-break
																 $classType = 'sea-break'; 
                                           
																$detailPkey = $rsFreightDetail[$i][$k]['pkey'];
																$containerkey = $arrContainers[$c]["pkey"];
																	  
																$_POST['containerFreight_'.$arrContainers[$c]["pkey"].'[]'] =  (empty($arrPriceContainer[$detailPkey][$containerkey]['price'])) ? 0 : $obj->formatNumber($arrPriceContainer[$detailPkey][$containerkey]['price']);
															
//																$_POST['containerFreight_'.$arrContainers[$c]["pkey"].'[]'] =  (empty($arrCost[$arrContainers[$c]["pkey"]]['price'])) ? 0 : $obj->formatNumber($arrCost[$arrContainers[$c]["pkey"]]['price']);
                                                
                                                        ?>
                                                                
                                                            <div class="div-table-col detail-col-detail container-detail <?php echo $classType; ?>" relheaderkey="<?php echo $arrContainers[$c]["pkey"]; ?>"  style="display:none;" > 
                                                                   <?php echo $obj->inputNumber('containerFreight_'.$arrContainers[$c]["pkey"].'[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'etc'=>'style="text-align:right; " ', 'disabled' =>  $disable)); ?>
                                                            </div> 
                                                            
                                                        <?php } ?> 
                                                             <div class="div-table-col detail-col-detail">
                                                                 <?php echo $obj->inputText('carrierRemarks[]', array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable )); ?>
                                                            </div>

															<div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>">
																<div class="flex">
                                                            		<?php echo $obj->inputHidden('hidOrderListFreight[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable, 'add-class'=>'hid-order-list')); ?>
																	<i class="fas arrow-nav fa-arrow-circle-up" rel="-1"></i>
																	<i class="fas arrow-nav fa-arrow-circle-down " rel="1"></i>
																</div> 
															</div> 
															<div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button add-item-container', 'etc' => 'attr-template="item-row-template"')) ?></div>
															<div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo  $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                                                        </div>
                                                    <?php } ?> 

                                            </div>


                                    </div>  
                                <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top; padding-top:1em !important"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"','class' => 'btn btn-link remove-button')); ?></div>
                            </div>
                        </div>
                    </div>
						
                    <?php } ?> 
           
            </div>  
                 <div style="clear:both; height:1em;"></div> 
                  <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddFreightRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>   

         </div>
      <div style="clear:both; height:5em;"></div> 
      
        <!--DESTINATION-->
      <div class="table-container">
                    <div class="div-table">
                    
                     <div class="div-table-row"> 
                         <div class="div-table-col ">
                             <div class="div-table">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail" style="width:150px">
                                            <h4 style="width:7.2em"><?php echo ucwords($obj->lang['destinationCharge']); ?></h4>                                    
                                    </div>
                                 </div>
                             </div>
                            
                         </div>
                        <div class="div-table-col" style="padding-top:1.2em; padding-left: 0" >
                             <div class="div-table">
                                <div class="div-table-row list-container-origin" style="">
                                    
								<?php 
                                        $zonaRow = count($rsDestinationDetail);
                                        $arrShowContainer = array();
                                        $arrPriceContainer = array();

                                        for($k =0;$k<$zonaRow;$k++){ 
                                            
                                            for($l=0;$l<count($rsDestinationDetail[$k]);$l++){

												$detailPkey = $rsDestinationDetail[$k][$l]['pkey'];
                                                $rsDestinationPrice = $obj->getContainerPrice($detailPkey, LOC_TYPE['destination']);
												 
                                            	$_POST['chkContainerDestination-1'] = ($rsDestinationDetail[$k][$l]['rateprice'] > 0) ? 1 : 0 ; 
												
                                                for($c=0;$c<$totalCols;$c++){ 
 
													$arrPriceContainer[$detailPkey] = array();
														
                                                    for($ctr=0;$ctr<count($rsDestinationPrice);$ctr++){ 
														$containerkey = $rsDestinationPrice[$ctr]['containerkey'];
                                                        $arrShowContainer[$containerkey]['containerkey'] = $rsDestinationPrice[$ctr]['containerkey']; 
														 
														$arrPriceContainer[$detailPkey][$containerkey]['price'] = $rsDestinationPrice[$ctr]['price'];
                                                    }

                                                }
                                            }
                                        }
									 
                                    ?>
<!--
									
									<div class="div-table-col  ">
										   <div class="flex">
										   	<div relkey="-1"><?php echo $obj->inputCheckBox('chkContainerDestination-1',  array('add-class'=>'chklist-container','etc'=>'relcontainerkey="-1"')); ?> </div>
											<div>Rate</div>   
										   </div> 
                                    </div>
-->
                                     
                                    
									<?php      
                                     
                                        for($c=0;$c<$totalCols;$c++){  
                                            $chkIsContainer = (empty($arrShowContainer[$arrContainers[$c]["pkey"]]['containerkey'])) ? 0 : 1;
                                            $_POST['chkContainerDestination'.$arrContainers[$c]["pkey"].'[]'] = $chkIsContainer;
                                    
                                    ?>
                                    <div class="div-table-col ct-item <?php echo $classType ?>">
                                          <div class="div-table">
                                            <div class="div-table-row">
												 <div class="div-table-col ct-item">
													<div class="flex" style="padding-right:1em">
														<div relkey="<?php echo $arrContainers[$c]["pkey"]; ?>"><?php echo $obj->inputCheckBox('chkContainerDestination'.$arrContainers[$c]["pkey"].'[]', array('add-class'=>'chklist-container','etc'=>'relcontainerkey="'.$arrContainers[$c]["pkey"].'"')); ?></div>
														<div><?php echo $arrContainers[$c]["name"]; ?></div>
													</div> 
												</div>     
                                             </div>
                                        </div>
                                    </div>              
                                    <?php } ?>
                                            
    
                                 </div>

                             </div>
                            
                         </div>
                 
                    </div>
                </div>      
                <div class="div-table ">
               
                    <div class="div-table-row"> 
                        <div class="div-table-col " style="vertical-align:middle;width:150px;font-weight:bold">Zona / Delivery (Zona)</div>
                        <div class="div-table-col " style="vertical-align:middle;width:250px;"><?php echo  $obj->inputSelect('selDestinationZone', $arrDestinationtZone, array('add-class'=>'select-location', 'allowedStatusForEdit' => array(1,2,3))); ?></div>
                </div>
                </div>
         
		  		<div class="div-table mnv-transaction transaction-detail no-odd-even-style" style="width:100%;" attr-level="0">

                        <?php  
                            $totalRows = count($rsDestinationDetail);
                          
                            for ($i=0;$i<=$totalRows; $i++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                 
                                $readonlyDetail = false;
                         
                                    
                                if ($i == $totalRows ){
                                    $class = 'location-destination-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';  
                                } else {  
                                    
                                    $_POST['hidDetailDestinationKey[]'] =  $rsDestinationDetail[$i][0]['pkey']; 
                                    $_POST['hidLocationPickupDetailKey[]'] =  $rsDestinationDetail[$i][0]['locationpickupkey']; 
                                    $_POST['pickupLocationDetailName[]'] =  $rsDestinationDetail[$i][0]['polname']; 
                                    $_POST['hidLocationZoneDetailKey[]'] =  $rsDestinationDetail[$i][0]['locationzonekey']; 
                                    $_POST['zoneLocationDetailName[]'] =  $rsDestinationDetail[$i][0]['podname'];
                                    
                                } 

                        ?>

                        <div class="div-table-row destination-row <?php echo $class; ?>" reldetailkey = "<?php echo $rsDestinationDetail[$i][0]['pkey'];?>"> 
                      <div class="div-table-col" style="padding:0; padding-top:1.5em">   
                            <div class="div-table row-panel" style="width:100%">
                                        <div class="div-table-col detail-col-detail">

                                            <div class="div-table" style="width:100%;"> 
                                                    <div class="div-table-row destination-row-header">   
                                                         <div class="div-table-col" style="width:450px;">
                                                             <div class="div-table"  style="width:96%;">
                                                                <div class="div-table-row location-row">
                                                                    <div class="div-table-col" style="width:150px;font-weight:bold">Zona / Delivery (Zona)</div>

                                                                    <div class="div-table-col" style="">
                                                                    <div class="flex">
                                                                        <div style="width:150px">
                                                                            <?php echo $obj->inputHidden('hidDetailDestinationKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?>  
                                                                            <?php echo $obj->inputHidden('hidLocationPickupDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?> 
                                                                            <?php echo  $obj->inputText('pickupLocationDetailName[]', array('overwritePost' => $overwrite, 'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>

                                                                        </div>
                                                                        <div >/</div>
                                                         
                                                                        <div style="width:150px">
                                                                           <?php echo $obj->inputHidden('hidLocationZoneDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?> 
                                                                            <?php echo  $obj->inputText('zoneLocationDetailName[]', array('overwritePost' => $overwrite, 'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>
                                                                        </div>

                                                                    </div>

                                                                    </div>

                                                                </div>  
                                                            </div>
                                                        </div>
                                                    </div>
                                            </div>
                                            <div class="div-table transaction-detail detail-item" style="width:100%; border-bottom:1px solid #333;" attr-level="1" attr-group="hidDetailItemDestinationKey">

                                                <div class="div-table-row">  
                                                    <div class="div-table-col detail-col-header" style="width:50px;text-align:center">Reim</div> 
                                                    <div class="div-table-col detail-col-header"  style="width:150px;">Destination <?php echo ucwords($obj->lang['services']); ?></div> 
                                                    <div class="div-table-col detail-col-header"  style="width:120px;"><?php echo ucwords($obj->lang['alias']); ?></div>                                                                             
                                                    <div class="div-table-col detail-col-header"  style="width:100px;"><?php echo ucwords($obj->lang['unit']); ?></div> 
                                                    <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['currencyShort']); ?></div>
<!--
                                                   <div class="div-table-col detail-col-header container-detail" relheaderKey="-3" style="width:110px;text-align:right;display:none;">Normal Price</div> 
                                                   <div class="div-table-col detail-col-header container-detail" relheaderKey="-2" style="width:110px;text-align:right;display:none;">Minimum Price</div> 
-->
                                                   <div class="div-table-col detail-col-header container-detail" relheaderKey="-1" style="width:110px;text-align:right;display:none;">Rate</div> 

                                                      <?php 

                                                      $classType = '';

                                                      foreach($arrContainers as $row){ ?>

                                                    <div class="div-table-col detail-col-header container-detail  " relheaderKey="<?php echo $row['pkey']; ?>" style="width:110px;text-align:right;display:none;"><?php echo ucwords($row['name']); ?></div> 

                                                      <?php } ?>
 
                                                     <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['note']); ?></div> 

                                                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                                                  </div>
                                            <?php  
                                         
                                                $totalItemRows = count($rsDestinationDetail[$i] ?? []);
                 
                    
                                                for ($j=0;$j<=$totalItemRows; $j++){  

                                                    $class =  'transaction-detail-row';
                                                    $overwrite = true;
                                                    $disable = '';  
                                                    $activeCurrencyKey =  CURRENCY['idr'] ;
                                                    $numberClass = 'inputdecimal';
                                                    
                                                    $_POST['chkIsReimburse[]'] = 0;
                                                    $_POST['chkIncludeTaxServiceDetail[]'] = 0;
                                                    $_POST['chkIncludeTaxServiceCostDetail[]'] = 0;
                
                                                    $readonly = false;

                                                    if ($j == $totalItemRows ){
                                                        $class = 'service-row-template row-template';
                                                        $overwrite = false; 
                                                        $disable = 'disabled="disabled"';  
                                                    } else {  

                                                        $_POST['hidDetailItemDestinationKey[]'] =  $rsDestinationDetail[$i][$j]['pkey'];
                                                        $_POST['hidServiceDestinationKey[]'] =  $rsDestinationDetail[$i][$j]['servicekey']; 
                                                        $_POST['serviceDestinationName[]'] =  $rsDestinationDetail[$i][$j]['servicename']; 
                                                        $_POST['serviceDescription[]'] =  $rsDestinationDetail[$i][$j]['description']; 
                                                        $_POST['chkIsReimburse[]'] =  $rsDestinationDetail[$i][$j]['isperreciept']; 
                                                        $_POST['aliasService[]'] =  $rsDestinationDetail[$i][$j]['alias']; 
                    
                                                      
                                                        $_POST['taxPercentageService[]'] =  $obj->formatNumber($rsDestinationDetail[$i][$j]['taxpercentage']); 
                                                        $_POST['chkIncludeTaxServiceDetail[]'] =  $rsDestinationDetail[$i][$j]['ispriceincludetax']; 
                                                        $_POST['hidDetailLocationZoneKey[]'] =  $rsDestinationDetail[$i][$j]['locationzonekey'];
                                                        $_POST['hidDetailLocationPickupKey[]'] =  $rsDestinationDetail[$i][$j]['locationpickupkey'];
                                                        
                                                        $_POST['selCurrencyItemDetail[]'] =  $rsDestinationDetail[$i][$j]['currencykey']; 
                                                        $_POST['hidUnitItemDetailKey[]'] =  $rsDestinationDetail[$i][$j]['unitkey']; 
                                                        $_POST['unitItemDetailName[]'] =  $rsDestinationDetail[$i][$j]['unitname'];                                                        
                                                        
                                                        $_POST['serviceDestinationRemarks[]'] =  $rsDestinationDetail[$i][$j]['remarks'];  
                                                        $_POST['ratePriceDestination[]'] =  $obj->formatNumber($rsDestinationDetail[$i][$j]['rateprice']);   
 														$_POST['hidOrderListDestination[]'] =  $rsDestinationDetail[$i][$j]['orderlist']; 

                                                        $activeCurrencyKey = $rsDestinationDetail[$i][$j]['currencykey'];

                                                        $numberClass = 'inputdecimal'; // ($_POST['selCurrencyDetail[]'] == CURRENCY['idr'] ) ? 'inputnumber' : 'inputdecimal';

                                                    } 

                                            ?>

                                            <div class="div-table-row freight-row-detail <?php echo $class; ?>"> 
                                                       <div class="div-table-col detail-col-detail" style="text-align:center"><?php  echo $obj->inputCheckBox('chkIsReimburse[]',array('overwritePost' => $overwrite, 'add-class'=> 'chk-list-reciept','disabled' =>  $disable )); ?></div>

                                                            <div class="div-table-col   detail-col-detail" style="text-align:center">
                                                                <?php echo $obj->inputHidden('hidDetailItemDestinationKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                                <?php echo $obj->inputHidden('hidDetailLocationZoneKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                                <?php echo $obj->inputHidden('hidDetailLocationPickupKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                                <?php echo $obj->inputHidden('hidServiceDestinationKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>  
                                                                <?php echo $obj->inputText('serviceDestinationName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' =>  $disable)); ?>
                                                            </div>   
                                                                 <div class="div-table-col detail-col-detail">
                                                                <?php echo $obj->inputText('aliasService[]', array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable )); ?>

                                                            </div>
                                                           <div class="div-table-col detail-col-detail" >
                                                                <?php echo $obj->inputHidden('hidUnitItemDetailKey[]',array('overwritePost' => $overwrite,'readonly' => $readonly, 'disabled' =>  $disable)); ?>
                                                                <?php echo $obj->inputText('unitItemDetailName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                            </div>

                                                            <div class="div-table-col detail-col-detail">
                                                                <?php echo $obj->inputSelect('selCurrencyItemDetail[]',$arrCurrency, array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable )); ?>
                                                            </div>
                                                            
                                                            <div class="div-table-col detail-col-detail container-detail" style="display:none;" relheaderkey="-1">
                                                                <?php echo $obj->inputNumber('ratePriceDestination[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'etc'=>'style="text-align:right;margin-bottom:.3em;" ', 'disabled' =>  $disable)); ?>
                                                            </div> 
                                                            <?php 
                                                                $classType = '';                           
                                                                for($c=0;$c<$totalCols;$c++){ 
                                                                 
																	$detailPkey = $rsDestinationDetail[$i][$j]['pkey'];
																	$containerkey = $arrContainers[$c]["pkey"];
																	
																	$_POST['containerDestination_'.$arrContainers[$c]["pkey"].'[]'] =  (empty($arrPriceContainer[$detailPkey][$containerkey]['price'])) ? 0 : $obj->formatNumber($arrPriceContainer[$detailPkey][$containerkey]['price']);
                                                                 
                                                            ?>

                                                                <div class="div-table-col detail-col-detail container-detail " relheaderkey="<?php echo $arrContainers[$c]["pkey"]; ?>"  style="display:none;" >

                                                                    <?php    
                                                                        echo $obj->inputNumber('containerDestination_'.$arrContainers[$c]["pkey"].'[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'etc'=>'style="text-align:right;" ', 'disabled' =>  $disable)); 
                                                                    ?>
                                                                </div> 

                                                            <?php } ?>  
                                                            
                                                            <div class="div-table-col detail-col-detail">
                                                                <?php echo $obj->inputText('serviceDestinationRemarks[]', array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                                            </div>
															<div class="div-table-col detail-col-detail  icon-col <?php echo $obj->hideOnDisabled(); ?>">
																<div class="flex">
																	<?php echo $obj->inputHidden('hidOrderListDestination[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable, 'add-class'=>'hid-order-list')); ?>
																	<i class="fas arrow-nav fa-arrow-circle-up" rel="-1"></i>
																	<i class="fas arrow-nav fa-arrow-circle-down " rel="1"></i>
																</div> 
															</div> 
                                                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="service-row-template"')) ?></div>
                                                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo  $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>
                                                       </div>   
                                                    <?php } ?> 

                                            </div>


                                    </div>  
                                <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top; padding-top:1em !important"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"','class' => 'btn btn-link remove-button')); ?></div>
                            </div>
                        </div>
                    </div>

                    <?php } ?> 
           
            </div>  
                  <div style="clear:both; height:1em;"></div> 
                  <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddDestinationRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone btn-add-detail')); ?></div>   
             

 
      </div>
      
         <div class="form-button-margin"></div>
         <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);?> 
        </div> 
        
    </form>  

     <?php echo $obj->showDataHistory(); ?>
</div> 
	<script>
		 
//		for(var instanceName in CKEDITOR.instances) { 
//			console.log(instanceName)
//			console.log(CKEDITOR.instances[instanceName]);  
////			instanceName.getData();
//			
//			// get data bisa pake ini, set blm bisa
////			 CKEDITOR.instances[instanceName].getData();
////			 CKEDITOR.instances[instanceName].setData("test 123");
//		}
//		
//		jQuery(document).ready(function(){   
////			CKEDITOR.instances['txtTermsAndConditions'].getData();
////			CKEDITOR.instances['txtTermsAndConditions'].setData("tsets");
//			
////			console.log("test")
////			console.log(ckeditorList)
////			console.log(selectedTab.newPanel[0].id);
////		
////		  var objEditor = ckeditorList[selectedTab.newPanel[0].id]; 
////      
////			for (i=0;i<objEditor.length;i++){  
////				var elementId = objEditor[i].elementId; 
////				var editor = objEditor[i].editor; 
////				editor.setData( '<p>This is the editor data.</p>' ); 
////			}  
//		})
		
	
	</script>
</body>

</html>
