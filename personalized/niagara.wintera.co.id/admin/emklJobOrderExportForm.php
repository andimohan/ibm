<?php 
require_once '../../../_config.php'; 
require_once '../../../_include-v2.php';

includeClass('EMKLJobOrder.class.php');
$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['export']));
$emklQuotationOrderExport = createObjAndAddToCol(new EMKLQuotationOrder(EMKL['jobType']['export']));
$container = createObjAndAddToCol(new Container());
$port = createObjAndAddToCol(new Port());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$vessel = createObjAndAddToCol(new Vessel());
$terminal = createObjAndAddToCol(new Terminal());
$supplier = createObjAndAddToCol(new Supplier());
$currency = createObjAndAddToCol(new Currency());
$service = createObjAndAddToCol(new Service(SERVICE)); 
$consignee = createObjAndAddToCol(new Consignee());
$city = createObjAndAddToCol(new City());

$isActiveMasterHBL = $class->isActiveModule('emklhousebl');

if($isActiveMasterHBL)
    $emklHouseBL = createObjAndAddToCol(new EMKLHouseBL());

$depot = createObjAndAddToCol(new Depot());

$obj = $emklJobOrderExport; 
 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
 
$formAction = 'emklJobOrderExportList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','textcolor');   
$rs = prepareOnLoadData($obj); 
$rsDetail = array();
$rsContactPerson = array(); 
$rsSalesDetail = array();
$rsItemDetail = array();
$rsCommisionDetail = array(); 
$rsInvoiced = array();  
$rsVolumeDetail = array();
$rsContainerDetail = array();

$useShippingInstruction = $obj->loadSetting('useShippingInstruction');
$containerQtyUnit = $obj->loadSetting('containerQtyUnit');

$_POST['trDate'] = date('d / m / Y'); 
$_POST['closingDate'] = date('d / m / Y 00:00'); 
/*
$emptyDate = '00 / 00 / 0000';
$_POST['etaPod'] = $emptyDate;
$_POST['etdPol'] = $emptyDate;
$_POST['stuffingIn'] = $emptyDate;
$_POST['dateDoc'] = $emptyDate;
$_POST['invoiceDate'] = $emptyDate;
$_POST['bookingDate'] = $emptyDate;
$_POST['packingDate'] = $emptyDate;
$_POST['insuranceDate'] = $emptyDate;
$_POST['admDODate'] = $emptyDate;
$_POST['thcDate'] = $emptyDate;
$_POST['liftOnDate'] = $emptyDate;
$_POST['pibDate'] = $emptyDate;
$_POST['siDate'] = $emptyDate;
$_POST['npeDate'] = $emptyDate;
$_POST['formDEADate'] = $emptyDate;
$_POST['others'] = $emptyDate;
$_POST['gateIn'] = $emptyDate;
$_POST['gateOut'] = $emptyDate;
$_POST['dateTakeEmpty'] = $emptyDate;
$_POST['dateExport'] = $emptyDate;
$_POST['agreementDate'] = $emptyDate;*/


$_POST['selTypeOfJob'] = EMKL['jobType']['export'];
$_POST['chkIsMaster'] = 1;
$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';
$editSupplierPICCriteria = '';
    
$arrInvoicedKey = array();
$arrType = array(); 
 
$isMaster = '';
$hblReadOnly = ($isActiveMasterHBL) ? true : false;
 
$useJobOrderHeader = $obj->loadSetting('useJobOrderHeader');
$attrHeader = ($useJobOrderHeader == 2) ? '' :  'attr-header="true"';
 
$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');    

$dateReturnOnEmpty = array('returnOnEmpty'=>true, 'value' => '00 / 00 / 0000');
$useShippingInstruction = $obj->loadSetting('useShippingInstruction');

if(!empty(PARTNER_ACCOUNT['TMS']))	$_POST['chkIsTrucking'] = 1;

$rsInvoiceCol = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
    $rsSalesDetail = $obj->getDetailWithRelatedInformation($id);  
    $rsInvoiced = $obj->getInvoiceInformation($id); 
    
    $rsVolumeDetail = $obj->getDetailVolume($id); 
    $rsContainerDetail = $obj->getDetailContainer($id);  
   
    if(!empty($rsInvoiced)){
        $arrInvoicedKey = array_column($rsInvoiced,'refdetailkey'); 
        $attrHeader = 'attr-header="true"'; 
    }
 

    // cek invoice setiap sales order, buat jadi array
    // utk header cek setiap array ad nilai atau tdk
    // utk setiap detail sales, jg sama
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y '); 
    
    $_POST['hidCargoType'] = $rs[0]['containertypekey'];

    $rsQuotation = $emklQuotationOrderExport->searchData($emklQuotationOrderExport->tableName.'.pkey',$rs[0]['quotationkey']); 
        
    if (!empty($rs[0]['quotationkey'])){
        $rsQuotation = $emklQuotationOrderExport->getDataRowById($rs[0]['quotationkey']);
        $_POST['hidQuotationKey'] = $rs[0]['quotationkey'];
        $_POST['quotationCode'] = $rsQuotation[0]['code'];
    } 
        
    if (!empty($rs[0]['saleskey'])){
        $_POST['hidSalesKey'] = $rs[0]['saleskey']; 
		$rsSales = $employee->getDataRowById($rs[0]['saleskey']);
		$_POST['salesName'] = $rsSales[0]['name'];   
	}

    
    if (!empty($rs[0]['itemkey'])){
        $rsItem = $container->getDataRowById($rs[0]['itemkey']);
        $_POST['hidContainerKey'] = $rs[0]['itemkey'];
        $_POST['containerName'] = $rsItem[0]['name'];
    }
    
    if (!empty($rs[0]['customerkey'])){
        $rsShipper = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['hidCustomerKey'] = $rs[0]['customerkey'];
        $_POST['shipperName'] = $rsShipper[0]['name'];
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
    
    if (!empty($rs[0]['depotkey'])){
        $rsDepot = $depot->getDataRowById($rs[0]['depotkey']);
        $_POST['hidDepotKey'] = $rs[0]['depotkey'];
        $_POST['depotName'] = $rsDepot[0]['name'];
    }

        
    if (!empty($rs[0]['terminalkey'])){
        $rsTerminal = $terminal->getDataRowById($rs[0]['terminalkey']);
        $_POST['hidTerminalKey'] = $rs[0]['terminalkey'];
        $_POST['terminalName'] = $rsTerminal[0]['name'];
    }
    
    if (!empty($rs[0]['refkey'])){
        $rsJob = $obj->getDataRowById($rs[0]['refkey']);
        $_POST['hidJobOrderKey'] = $rs[0]['refkey'];
        $_POST['jobOrderCode'] = $rsJob[0]['code'];
    }    
 
    $_POST['hidVesselKey'] = $rs[0]['vesselkey'];
	if(!empty($rs[0]['vesselkey'])){
        $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
        $_POST['vesselName'] = $rsVessel[0]['name'];
    }

    $_POST['hidFeederKey'] = $rs[0]['feederkey'];
	if(!empty($rs[0]['feederkey'])){
        $rsFeeder = $vessel->getDataRowById($rs[0]['feederkey']);
        $_POST['feederName'] = $rsFeeder[0]['name'];
    }

    $_POST['selWarehouseKey'] = $rs[0]['warehousekey']; 
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
  
    
	//$_POST['selTypeOfJob'] = $rs[0]['jobtypekey'];
	$_POST['selAirSea'] = $rs[0]['transportationtypekey'];
	$_POST['selContainerType'] = $rs[0]['loadcontainertypekey'];
    $_POST['volume'] = $obj->formatNumber($rs[0]['volume'],2); 
    $_POST['weight'] = $obj->formatNumber($rs[0]['weight'],2); 
    $_POST['selVolumeType'] = $rs[0]['volumetype'];
    $_POST['mblNumber'] = $rs[0]['mblnumber'];
    $_POST['etdPol'] = $obj->formatDBDate($rs[0]['etdpol'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));
    $_POST['etaPod'] = $obj->formatDBDate($rs[0]['etapod'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));
    $_POST['closingDate'] = $obj->formatDBDate($rs[0]['closingdate'],'d / m / Y H:i', array('returnOnEmpty'=>true, 'value' => ''));
	$_POST['vesselNumber'] = $rs[0]['vesselnumber'];
	$_POST['feederNumber'] = $rs[0]['feedernumber'];
	$_POST['poNumber'] = $rs[0]['ponumber'];
	$_POST['aju'] = $rs[0]['aju'];  
    $_POST['stuffingIn'] = $obj->formatDBDate($rs[0]['stuffingin'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));

    $_POST['selSupplier'] = $rs[0]['supplierkey'];

    $_POST['flag'] = $rs[0]['flag']; 
 
	$_POST['trDesc'] = $rs[0]['trdesc']; 
	$_POST['itemDescription'] = $rs[0]['itemdescription']; 
    $_POST['chkIsMaster'] = $rs[0]['ismaster']; 
    $_POST['chkIsTrucking'] = $rs[0]['istrucking'];  
    
    if($rs[0]['ismaster'] && in_array($rs[0]['loadcontainertypekey'], array(EMKL['emklType']['lcl'])) ) { 
        $isMaster = 'display-none'; 
	}
 
    $_POST['totalSelling'] = $obj->formatNumber($rs[0]['totalselling'],2); 
  
    //FORM OTHERS
    $_POST['consigneeName'] = $rs[0]['consigneename'];  
    $_POST['dateDoc'] = $obj->formatDBDate($rs[0]['datedoc'],'d / m / Y', $dateReturnOnEmpty);  
    $_POST['siDate'] = $obj->formatDBDate($rs[0]['sidate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['bookingDate'] = $obj->formatDBDate($rs[0]['bookingdate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['invoiceDate'] = $obj->formatDBDate($rs[0]['invoicedate'],'d / m / Y', $dateReturnOnEmpty);  
    $_POST['packingDate'] = $obj->formatDBDate($rs[0]['packingdate'],'d / m / Y', $dateReturnOnEmpty);  
    $_POST['insuranceDate'] = $obj->formatDBDate($rs[0]['insurancedate'],'d / m / Y', $dateReturnOnEmpty);  
    $_POST['formDEADate'] = $obj->formatDBDate($rs[0]['formdate'],'d / m / Y', $dateReturnOnEmpty);  
    //$_POST['others'] = $obj->formatDBDate($rs[0]['others'],'d / m / Y', $dateReturnOnEmpty);  
    
    
    $_POST['admDODate'] = $obj->formatDBDate($rs[0]['admdodate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['thcDate'] = $obj->formatDBDate($rs[0]['thcdate'],'d / m / Y', $dateReturnOnEmpty);   
    $_POST['deliveryNPEDate'] = $obj->formatDBDate($rs[0]['deliverynpedate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['exportCardDate'] = $obj->formatDBDate($rs[0]['exportdcarddate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['liftOnDate'] = $obj->formatDBDate($rs[0]['liftondate'],'d / m / Y', $dateReturnOnEmpty);
    $_POST['stuffingLocation'] = $rs[0]['stuffinglocation'];  
       
    $_POST['pabean'] = $obj->formatNumber($rs[0]['pabean'],2); 
    $_POST['incoterm'] = $rs[0]['incoterm'];  
    $_POST['lartas'] = $rs[0]['lartas'];  
    $_POST['qtyPack'] = $rs[0]['qtypack'];  
    $_POST['attachment'] = $rs[0]['attachment'];     
    $_POST['transferPIBDate'] = $obj->formatDBDate($rs[0]['transferpibdate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['response'] = $rs[0]['response'];   
    $_POST['npeDate'] = $obj->formatDBDate($rs[0]['npedate'],'d / m / Y', $dateReturnOnEmpty); 
      
      
    if (!empty($rs[0]['placeofdeliverykey'])){
        $rsPODelivery = $port->getDataRowById($rs[0]['placeofdeliverykey']);
        $_POST['hidPlaceOfDeliveryKey'] = $rs[0]['placeofdeliverykey'];
        $_POST['placeOfDeliveryName'] = $rsPODelivery[0]['name'];
    }
    
    if (!empty($rs[0]['placeofreceiptkey'])){
        $rsPOReceipt = $port->getDataRowById($rs[0]['placeofreceiptkey']);
        $_POST['hidPlaceOfReceiptKey'] = $rs[0]['placeofreceiptkey'];
        $_POST['placeOfReceiptName'] = $rsPOReceipt[0]['name'];
    } 

    if (!empty($rs[0]['agentkey'])){
        $rsAgent = $customer->getDataRowById($rs[0]['agentkey']);
        $_POST['hidAgentKey'] = $rs[0]['agentkey'];
        $_POST['agentName'] = $rsAgent[0]['name'];
    }}


    // shiping instruction

	$_POST['siConsigneeName'] = $rs[0]['siconsigneename']; 
	$_POST['siConsigneeAddress'] = $rs[0]['siconsigneeaddress']; 
	$_POST['selBillType'] = $rs[0]['billtypekey'];
	$_POST['hsCode'] = $rs[0]['hscode'];
	$_POST['kpbc'] = $rs[0]['kpbc'];

	if (!empty($rs[0]['sishipperkey'])){
		$rsShipper = $customer->getDataRowById($rs[0]['sishipperkey']);
		$_POST['hidSIShipperKey'] = $rs[0]['sishipperkey'];
		$_POST['shipperSIName'] = $rsShipper[0]['name'];
	}


	if (!empty($rs[0]['notifykey'])){
		$rsNotify = $customer->getDataRowById($rs[0]['notifykey']);
		$_POST['hidNotifyKey'] = $rs[0]['notifykey'];
		$_POST['notifyName'] = $rsNotify[0]['name'];
	}    

	if (!empty($rs[0]['notifykey2'])){
		$rsNotify2 = $customer->getDataRowById($rs[0]['notifykey2']);
		$_POST['hidNotifyKey2'] = $rs[0]['notifykey2'];
		$_POST['notifyName2'] = $rsNotify2[0]['name'];
	}    

	if (!empty($rs[0]['placeofissuekey'])){
		$rsCity = $city->searchData('city.pkey',$rs[0]['placeofissuekey'],true);
		$_POST['hidPlaceOfIssueKey'] = $rs[0]['placeofissuekey'];
		$_POST['placeOfIssueName'] = $rsCity[0]['citycategoryname'];
	} 

$rsCurrency = $currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1'.$editCurrencyInactiveCriteria.')');
$arrCurrencyName = array_column($rsCurrency,null,'pkey');

$rsContainer = $container->searchData();

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');      
$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrCurrency = $class->convertForCombobox($rsCurrency,'pkey','name'); 
$arrJob = $class->convertForCombobox($obj->getJobType(),'pkey','name');  
$arrTransportType = $class->convertForCombobox($obj->getTransportationType(),'pkey','name');  
$arrContainer = $class->convertForCombobox($obj->getLoadContainer(),'pkey','name');  
$arrVolume = $class->convertForCombobox($obj->getVolumeUnit(),'pkey','name');  
$arrFreight = $class->convertForCombobox($obj->getFreightTerm(),'pkey','name');  
$arrUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name');
$arrContainerVolume = $class->convertForCombobox($rsContainer,'pkey','name');
$arrBillType = $class->convertForCombobox($obj->getBillType(),'pkey','name');  

$rsContainer = array_column($rsContainer,'name','pkey');

$rsService = $service->searchData();
$rsService = array_column($rsService,'name','pkey');
 
$rsInvoiceCol = $obj->getAmountInvoiced($rs[0]['pkey']);
$rsInvoiceCol = $obj->reindexDetailCollections($rsInvoiceCol,'refdetailkey'); 	
$arrSupplierPIC = $obj->convertForCombobox($customer->getPersonInChargeByCustomer($rs[0]['customerkey']),'pkey','value');    

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title></title> 
<style>
    .customer-row-header .div-table-col {vertical-align: middle}
    .customer-row-header > .div-table-col {vertical-align: middle; vertical-align: top}
    .subpanel {background:rgba(222,222,222, 0.6); border-radius: 0.5em; padding: 0.3em}
    .btn-primary.btn-hbl {padding: 2px !important; min-width: 0;}
</style> 
<script type="text/javascript">  
	jQuery(document).ready(function(){  
        
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
             
         var varConstant = {  
                            CURRENCY : <?php echo json_encode(CURRENCY); ?>,
                            EMKL : <?php echo json_encode(EMKL); ?>,
                            LCLTYPE : <?php echo json_encode(LCL_CONTAINER_TYPE); ?>, 
                        };
        
        
         var emklJobOrder = new EMKLJobOrder(tabID,<?php echo json_encode(
                                                                array(
                                                                    'rs' => $rs,
                                                                    'detail' => $rsDetail,
                                                                    'volumeDetail' => $rsVolumeDetail,
                                                                    'containerNumberDetail' => $rsContainerDetail
                                                                ) 
                                                            ); ?> ,varConstant); 
         prepareHandler(emklJobOrder);   
         
        
         var fieldValidation =  { code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 
                                 
                                    shipperName: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.shipper[1]
                                            }, 
                                        }
                                    }, 
  
                                } ; 
        
        
        setFormValidation(getTabObj(), $('#defaultForm-' + tabID), fieldValidation, <?php echo json_encode($obj->validationFormSubmitParam()); ?>  );
 
         <?php
			 if(isset($_GET) && !empty($_GET['shortcut']) && !empty($_GET['refkey'])){
				   $refQuotationKey = $_GET['refkey'];

				   $rsQuotationSC = $emklQuotationOrderExport->searchDataRow(array($emklQuotationOrderExport->tableName.'.code'), 
																			  ' and ' .$emklQuotationOrderExport->tableName.'.pkey = ' . $obj->oDbCon->paramString($refQuotationKey). 
																			  ' and ' .$emklQuotationOrderExport->tableName.'.statuskey  = 3'
																			  );
				  if(!empty($rsQuotationSC)){ 
					   $_POST['hidQuotationKey'] = $refQuotationKey;
					   $_POST['quotationCode'] = $rsQuotationSC[0]['code'];

					   echo  'getTabObj().onChangeQuotationOrder();';
				  }
			 } 
        ?>   
}); 

</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div> 
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
        <?php prepareOnLoadDataForm($obj); ?>   
        <?php echo  $obj->inputHidden('selTypeOfJob'); ?>
       <div class="div-table main-tab-table-2 header-panel">
                <div class="div-table-row">
                    <div class="div-table-col"> 
      						 <div class="div-tab-panel"> 
                                   <div class="div-table-caption border-orange">
                                       <div style="float:left"><?php echo ucwords($obj->lang['generalInformation']); ?></div>
                                       <div class="status-label" style="float:right; font-size: 0.7em"> 
                                            <label class="bg-green-avocado label-type-of-job"><?php  echo strtoupper($arrJob[$_POST['selTypeOfJob']]['label']); ?></label>
                                            <label class="bg-purple-purpureus label-air-sea"><?php echo strtoupper($arrTransportType[$_POST['selAirSea']]['label']); ?></label>
                                            <label class="bg-orange label-container-type"><?php echo strtoupper($arrContainer[$_POST['selContainerType']]['label']); ?></label>
                                        </div> 
                                       <div style="clear:both"></div>
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <?php if($useJobOrderHeader == 2) { ?>
                                            <div class="col-xs-9"><?php echo $obj->inputAutoCode('code', array('allowedStatusForEdit' => array (1))); ?></div>
                                        <?php } else { ?>
                                            <div class="col-xs-9"><?php echo $obj->inputText('code'); ?></div>
                                        <?php } ?>
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('etc' => $attrHeader) ); ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div> 


                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['quotationCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                                      <?php    
                                                        echo $obj->inputAutoComplete(array( 
                                                                                        'revalidateField' => false, 
                                                                                        'element' => array('value' => 'quotationCode',
                                                                                                            'key' => 'hidQuotationKey'
                                                                                                    ),
                                                                                        'source' =>array(
                                                                                                        'url' => 'ajax-emkl-quotation-order.php',
                                                                                                        'data' => array( 'action' =>'searchData', 'statuskey' => '(3)', 'jobtypekey' => EMKL['jobType']['export'])
                                                                                                    ) , 
                                                                                        'allowedStatusForEdit' => array(1),
                                                                                        'callbackFunction' => 'getTabObj().onChangeQuotationOrder('.EMKL['jobType']['export'].')'
                                                                                    )
                                                                                );  
                                                        ?> 
                                        </div> 
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['exportir']); ?> / <?php echo ucwords($obj->lang['shipper']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array(  
                                                                                    'revalidateField' => true,
                                                                                    'element' => array('value' => 'shipperName',
                                                                                                       'key' => 'hidCustomerKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-customer.php',
                                                                                                        'data' => array('action' =>'searchData' )
                                                                                                    ),  
                                                                                    'allowedStatusForEdit' => array (1),
                                                                                    'callbackFunction' => 'getTabObj().updatePersonInCharge();  getTabObj().updateCustomerInformation();',
                                                                                    'etc' => $attrHeader
                                                                                ) 
                                                                            );  
                                            ?> 
                                        </div> 
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['PIC']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selSupplier', $arrSupplierPIC); ?>  
                                        </div> 
                                    </div> 
                          
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label">Purchase Invoice</label> 
                                        <div class="col-xs-9">
                                              <?php echo $obj->inputText('poNumber'); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consignee']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('consigneeName', array('etc' => $attrHeader)); ?>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['typeOfJob']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                            <div class="consume"><?php echo  $obj->inputSelect('selAirSea', $arrTransportType, array('etc' => $attrHeader)); ?></div>
                                            <div style="width:100px"><?php echo  $obj->inputSelect('selContainerType', $arrContainer, array( 'etc' => $attrHeader)); ?></div>
                                            <div class="lcl-only" style="width:150px"><?php    
                                                                                            echo $obj->inputAutoComplete(array(
                                                                                                'objRefer' => $container, 
                                                                                                'revalidateField' => false, 
                                                                                                'element' => array('value' => 'containerName',
                                                                                                                   'key' => 'hidContainerKey'),
                                                                                                'source' =>array(
                                                                                                                    'url' => 'ajax-container.php',
                                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                                ) ,          
                                                                                                 'etc' => $attrHeader
                                                                                                )
                                                                                              ); 
                                                    ?>  
                                              </div> 
                                              <div> <?php echo $obj->inputSelect('hidCargoType', $arrCargoType, array( 'etc' => $attrHeader )); ?> </div>
                                            </div>
                                        </div>  
                                    </div>    
                                  <div class="form-group lcl-only lclnc">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['volume']); ?> / <?php echo ucwords($obj->lang['container']); ?></label> 
                                    <div class="col-xs-9">   
                                        <div class="flex">
                                            <div class="consume"><?php echo  $obj->inputDecimal('weight'); ?></div>
                                            <div class="text-muted" style="margin-right:20px">KG</div> 
                                            <div class="consume"><?php echo  $obj->inputDecimal('volume'); ?></div>
                                            <div class="text-muted" style="margin-right:20px">CBM</div> 
                                            <div>/</div>
                                            <div style="width: 10em">
                                                <?php echo $obj->inputSelect('hidContainerKey', $arrContainerVolume); ?>
                                            </div>
                                        </div>
                                    </div> 
                                </div>    
  				                <div class="form-group truckingfcl">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['volume']); ?></label> 
                                    <div class="col-xs-9">  
                                         <div class="div-table mnv-transaction transaction-detail" style="width: 100%">
                                        <?php 
                                            $totalVolumeRows = count($rsVolumeDetail);
                                            for ($j=0;$j<=$totalVolumeRows; $j++){ 
                                                
                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $readonly = false;
                                                $disabled = false; 
                                                $style = '';

                                                if ($j == $totalVolumeRows ){
                                                    $class = 'volume-row-template ';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                    $style = 'style="display:none !important"';
                                                } else{ 
                                                    $_POST['hidDetailVolumeKey[]'] =  $rsVolumeDetail[$j]['pkey'];
                                                    $_POST['selContainerDetailVolumeKey[]'] =  $rsVolumeDetail[$j]['itemkey'];
                                                    $_POST['qtyVolume[]'] =  $obj->formatNumber($rsVolumeDetail[$j]['qty']);
                                                
                                                }
                                                $hideDeleteIcon = '';  
                                            ?>
												
                                            <div class="div-table-row <?php echo $class; ?> odd-style-adjustment" <?php echo $style; ?> > 
                                                <div class="div-table-col"> 
                                                    <div class="flex">     
                                                        <div style="width:100px;">
                                                            <?php echo $obj->inputHidden('hidDetailVolumeKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputNumber('qtyVolume[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>
                                                        </div>
                                                        <div class="consume">
                                                            <?php echo $obj->inputSelect('selContainerDetailVolumeKey[]', $arrContainerVolume, array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled )); ?>
                                                        </div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="volume-row-template"')); ?></div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; '.$hideDeleteIcon.'"')); ?></div>
 
                                                    </div> 
                                                </div> 
                                            </div>   
                                        <?php }	 ?>  
                                        
                                    </div>
                                    </div> 
                                </div>
				                    <div class="form-group lcl-only">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['master']); ?> / <?php echo ucwords($obj->lang['reference']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div><?php echo $obj->inputCheckBox('chkIsMaster'); ?></div> 
                                                <div  class="consume"> 
                                                        <?php    
                                                        echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $emklJobOrderExport,
                                                                                        'readonly' => true,
                                                                                        'revalidateField' => false, 
                                                                                        'element' => array('value' => 'jobOrderCode',
                                                                                                           'key' => 'hidJobOrderKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-emkl-job-order.php',
                                                                                                            'data' => array( 'action' =>'searchDataMaster', 'jobtype' => EMKL['jobType']['export'])
                                                                                                        ) ,  
                                                                                        'callbackFunction' => 'getTabObj().updateFromJobOrder('.EMKL['jobType']['export'].')'
                                                                                      )
                                                                                );  
                                                        ?> 
                                                </div> 
                                            </div>
                                        </div>  
                                    </div>  
                         
                                     <div class="form-group">
                                       <label class="col-xs-3 control-label">AJU PEB</label> 
                                        <div class="col-xs-9"> 
                                                <div class="consume"><?php echo $obj->inputText('aju'); ?></div>
                                        </div> 
                                     </div>  
                                    

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['carrier']); ?>
                                        </label>
                                        <div class="col-xs-9">
                                        <?php echo $obj->inputAutoComplete(
                                            array(
                                                'objRefer' => $supplier,
                                                'revalidateField' => true,
                                                'element' => array(
                                                    'value' => 'carrierName',
                                                    'key' => 'hidCarrierKey'
                                                ),
                                                'source' => array(
                                                    'url' => 'ajax-supplier.php',
                                                    'data' => array('action' => 'searchData')
                                                ),
                                                'popupForm' => array(
                                                    'url' => 'supplierForm.php',
                                                    'element' => array(
                                                        'value' => 'carrierName',
                                                        'key' => 'hidCarrierKey'
                                                    ),
                                                    'width' => '600px',
                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['carrier'])
                                                ),
                                                'allowedStatusForEdit' => array(1),
                                                'etc' => $attrHeader
                                            )
                                        );
                                        ?>                                     
                                        </div> 
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['agent']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputAutoComplete(
                                                array(
                                                    'objRefer' => $customer,
                                                    'revalidateField' => true,
                                                    'element' => array(
                                                        'value' => 'agentName',
                                                        'key' => 'hidAgentKey'
                                                    ),
                                                    'source' => array(
                                                        'url' => 'ajax-customer.php',
                                                        'data' => array('action' => 'searchData')
                                                    ),
                                                    'popupForm' => array(
                                                        'url' => 'customerForm.php',
                                                        'element' => array(
                                                            'value' => 'agentName',
                                                            'key' => 'hidAgentKey'
                                                        ),
                                                            'width' => '600px',
                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['agent'])
                                                    ),
                                                    'allowedStatusForEdit' => array(1),
                                                    'etc' => $attrHeader
                                                                                    )
                                                );
                                            ?>                                     
                                        </div> 
                                    </div>

								 	<?php if(!empty(PARTNER_ACCOUNT['TMS'])){ ?>
                                     <div class="form-group">
                                       <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['trucking']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div><?php echo $obj->inputCheckBox('chkIsTrucking'); ?></div> 
                                        </div> 
                                     </div> 
								 	<?php } ?>
								 
                             </div>
<?php /*dipindah ke header*/ if(false){ ?>
                         <div class="div-tab-panel"> 
                             <div class="div-table-caption border-green"><?php echo ucwords($obj->lang['shippingIntruction']); ?></div>
                                   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['billType']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selBillType', $arrBillType); ?>
                                        </div> 
                                    </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipper']); ?></label> 
                                 
                                    <div class="col-xs-9">  
                                           <?php  echo $obj->inputAutoComplete(array(  
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'shipperSIName',
                                                                                                   'key' => 'hidSIShipperKey'),
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
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consignee']); ?> <?php echo ucwords($obj->lang['name']); ?></label> 
                                    <div class="col-xs-9">     
                                           <?php echo $obj->inputText('siConsigneeName'); ?>
                                    </div>  
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consignee']); ?> <?php echo ucwords($obj->lang['address']); ?></label> 
                                    <div class="col-xs-9">     
                                           <?php echo $obj->inputTextArea('siConsigneeAddress',array('etc' => 'style="height:10em;"')); ?>
                                    </div>  
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['notifyParty']); ?> </label> 
                                    <div class="col-xs-9">     
                                            <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'notifyName',
                                                                                                   'key' => 'hidNotifyKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array( 'action' =>'searchData' )
                                                                                                ) , 
                                                                                'popupForm' => array(
                                                                                                'url' => 'customerForm.php',
                                                                                                'element' => array('value' => 'notifyName',
                                                                                                'key' => 'hidNotifyKey'),
                                                                                                'width' => '600px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['notifyParty'])
                                                                                            )
                                                                                )
                                                                        );  
                                            ?>                                    
                                    </div>  
                                </div>  
                                   <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['notifyParty']); ?> 2</label> 
                                    <div class="col-xs-9">     
                                            <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'notifyName2',
                                                                                                   'key' => 'hidNotifyKey2'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array( 'action' =>'searchData' )
                                                                                                ) , 
                                                                                'popupForm' => array(
                                                                                                'url' => 'customerForm.php',
                                                                                                'element' => array('value' => 'notifyName2',
                                                                                                       'key' => 'hidNotifyKey2'),
                                                                                                'width' => '600px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
                                                                                            )
                                                                                )
                                                                        );  
                                            ?>                                     
                                       </div>  
                                </div>  
                               
							    <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['placeOfIssue']); ?></label> 
                                    <div class="col-xs-9">     
                                             <?php  echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $city,
                                                                                'revalidateField' => false, 
                                                                                'element' => array('value' => 'placeOfIssueName',
                                                                                                   'key' => 'hidPlaceOfIssueKey'),
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
                                    <label class="col-xs-3 control-label">HS CODE</label> 
                                    <div class="col-xs-9">     
                                           <?php echo $obj->inputText('hsCode'); ?>
                                    </div>  
                                </div> 
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">KPBC</label> 
                                    <div class="col-xs-9">     
                                           <?php echo $obj->inputText('kpbc'); ?>
                                    </div>  
                                </div> 
<!--
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['payment']); ?></label> 
                                    <div class="col-xs-9">     
                                           <?php /*echo $obj->inputSelect('selSellingFreightTerm',$arrFreight);*/ ?>
                                    </div>  
                                </div> 
-->
                         </div>
                        <?php } ?>
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['shippingInformation']); ?></div>
                             		<div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mblawb']); ?></label> 
                                    <div class="col-xs-9">     
                                           <?php echo $obj->inputText('mblNumber'); ?>
                                    </div>  
                                </div>  
                               <!-- <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bookingNumber']); ?></label> 
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputText('bookingNumber'); ?>
                                        </div> 
                                    </div> -->
                            
                                <div class="form-group">

                                   <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['feederVessel']); ?> / <?php echo ucwords($obj->lang['voyage']); ?></label>
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume">
                                                 <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $vessel,  
                                                                                'element' => array('value' => 'feederName',
                                                                                                   'key' => 'hidFeederKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-vessel.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ), 
                                                                                'popupForm' => array(
                                                                                                'url' => 'vesselForm.php',
                                                                                                'element' => array('value' => 'vesselName',
                                                                                                       'key' => 'hidVesselKey'),
                                                                                                'width' => '600px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['vessel'])
                                                                                            )
                                                                                )
                                                                        );  
                                            ?> 
                                            </div>
                                            <div style="width: 10em">
                                                  <?php echo $obj->inputText('feederNumber'); ?> 
                                            </div>
                                        </div>
                                    </div>   
                                </div>
							
                                <div class="form-group"> 
                                   <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['masterVessel']); ?> / <?php echo ucwords($obj->lang['voyage']); ?></label>
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume">
                                                 <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $vessel,  
                                                                                'element' => array('value' => 'vesselName',
                                                                                                   'key' => 'hidVesselKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-vessel.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ), 
                                                                                'callbackFunction' => 'getTabObj().updateFlag()',
                                                                                'popupForm' => array(
                                                                                                'url' => 'vesselForm.php',
                                                                                                'element' => array('value' => 'vesselName',
                                                                                                       'key' => 'hidVesselKey'),
                                                                                                'width' => '600px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['vessel'])
                                                                                            )
                                                                                )
                                                                        );  
                                            ?> 
                                            </div>
                                            <div style="width: 10em">
                                                  <?php echo $obj->inputText('vesselNumber'); ?> 
                                            </div>
                                        </div>
                                    </div>   
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['flag']) ?></label> 
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputText('flag',  array('etc' => 'attr-header="true"')); ?>
                                    </div> 
                                </div> 

                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['etd']); ?> / <?php echo ucwords($obj->lang['eta']); ?></label> 
                                    <div class="col-xs-9">  
                                           <div class="flex">
                                                <div class="consume"><?php echo $obj->inputDate('etdPol',array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?></div>
                                                <div>/</div>
                                                <div class="consume"><?php echo $obj->inputDate('etaPod',array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?></div>
                                            </div> 
                                       
                                    </div> 
                                </div>
   
                                <div class="form-group">
                                    <label class="col-xs-3 control-label">POL / POD</label> 
                                    <div class="col-xs-9">
                                       <div class="flex">
                                            <div class="consume"> 
                                                    <?php  echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $port,
                                                                                'revalidateField' => false, 
                                                                                'etc' =>  $attrHeader,
                                                                                'element' => array('value' => 'polName',
                                                                                                   'key' => 'hidPOLKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-port.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                )  
                                                                              )
                                                                        );  
                                                ?>
                                            </div> 
                                            <div>/</div>
                                             <div class="consume"> 
                                                   <?php  echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $port,
                                                                                        'revalidateField' => false,  
                                                           'etc' => $attrHeader,
                                                                                        'element' => array('value' => 'podName',
                                                                                                           'key' => 'hidPODKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-port.php',
                                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                                        ) 
                                                                                      )
                                                                                );  
                                                    ?>
                                            </div> 
                                            </div> 
                                    </div> 
                                </div>

                                
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['placeOfDelivery']); ?> / <?php echo ucwords($obj->lang['placeOfReceipt']); ?></label> 
                                    <div class="col-xs-9">
                                       <div class="flex">
                                            <div class="consume"> 
                                                  <?php  echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $port,
                                                                                'revalidateField' => false,  
                                                                                'element' => array('value' => 'placeOfDeliveryName',
                                                                                                   'key' => 'hidPlaceOfDeliveryKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-port.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                ),
                                                                                'allowedStatusForEdit' => array(1),
                                                                                'etc' => $attrHeader  
                                                                            )
                                                                        );  
                                                ?>     
                                            </div> 
                                            <div>/</div>
                                            <div class="consume"> 
                                                <?php  echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $port,
                                                                                'revalidateField' => false,  
                                                                                'element' => array('value' => 'placeOfReceiptName',
                                                                                                   'key' => 'hidPlaceOfReceiptKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-port.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                ),
                                                                                'allowedStatusForEdit' => array(1),
                                                                                'etc' => $attrHeader   
                                                                            )
                                                                        );  
                                                ?>    
                                            </div> 
                                        </div> 
                                    </div> 
                                </div>
                              
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['closingDate']); ?></label> 
                                    <div class="col-xs-9">
                                           <?php echo $obj->inputDateTime('closingDate', array('etc'=>'style="text-align:center"','allowEmpty' => true )); ?>                                      
                                    </div> 
                                </div>  
                               
								<div class="form-group">
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['goodsDescription']); ?></label> 
									<div class="col-xs-9"><?php echo $obj->inputTextArea('itemDescription', array('etc' => 'style="height:10em;"')); ?></div> 
								</div> 
								<?php if ($useShippingInstruction == 1) { ?>
								<div class="form-group">
									<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['attachment']); ?></label> 
									<div class="col-xs-9"><?php echo $obj->inputTextArea('attachment',  array('etc' => 'style="height:10em;"')); ?></div>  
								</div> 
								<?php } ?>
								<div class="form-group">
								 <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?>  </label> 
								 <div class="col-xs-9">   
									   <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>

								</div>
                                </div>
 								<div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['container']); ?> / <?php echo ucwords($obj->lang['seal']); ?></label> 
                                    <div class="col-xs-9">
                                        <div class="div-table mnv-transaction transaction-detail" style="width:100%">
                                        <?php 
                                            $totalRows = count($rsContainerDetail);
                                            for ($j=0;$j<=$totalRows; $j++){ 
                                                
                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $readonly = false;
                                                $disabled = false;  

                                                if ($j == $totalRows ){
                                                    $class = 'container-row-template row-template';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                } else{ 
                                                    $_POST['hidDetailContainerKey[]'] =  $rsContainerDetail[$j]['pkey'];
                                                    $_POST['containerNo[]'] =  $rsContainerDetail[$j]['containerno'];
                                                    $_POST['sealNo[]'] = $rsContainerDetail[$j]['sealno'];
                                                    $_POST['qtyContainer[]'] = $obj->formatNumber($rsContainerDetail[$j]['qty']);
                                                    $_POST['selUnit[]'] =  $rsContainerDetail[$j]['unitkey'];                                                   
													$_POST['weightContainer[]'] = $obj->formatNumber($rsContainerDetail[$j]['weight'],2);
                                                    $_POST['volumeContainer[]'] = $obj->formatNumber($rsContainerDetail[$j]['volume'],3);
                                                
                                                }
                                                $hideDeleteIcon = '';  
                                             ?>
                                            <div class="div-table-row <?php echo $class; ?>  odd-style-adjustment" > 
                                                <div class="div-table-col"> 
													 <div class="flex" style="width:100%">     
														<div style="width:50%;">
														   <?php echo $obj->inputHidden('hidDetailContainerKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
															<?php echo $obj->inputText('containerNo[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>
														</div>
														<div style="width:50%;">
															<?php echo $obj->inputText('sealNo[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled )); ?>
														</div>

														<div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="container-row-template"')); ?></div>
														<div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; '.$hideDeleteIcon.'"')); ?></div>

													</div> 
													
													<?php if ($containerQtyUnit == 1) { ?>
													 <div style="clear:both; height:0.5em"></div>
													 <div class="flex" style="width:100%">
														<div style="width:50%;">
															<?php echo $obj->inputNumber('qtyContainer[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled, 'add-class' => 'label-style', 'etc' => 'style="text-align:right"' )); ?>
														</div>
                                                        <div style="width:40%"><?php echo $obj->inputSelect('selUnit[]', $arrUnit, array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disabled,'add-class' => 'label-style')); ?></div>     
														<div style="width:50%;">
															<?php echo $obj->inputDecimal('weightContainer[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled, 'add-class' => 'label-style' , 'etc' => 'style="text-align:right"')); ?>
														</div>
														<div class="text-muted" style="margin-right:5%">KG</div> 

														<div style="width:50%;">
															<?php echo $obj->inputDecimal('volumeContainer[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled, 'add-class' => 'label-style', 'etc' => 'style="text-align:right"  mnv-attr-decimal="3"' )); ?>
														</div>
														<div class="text-muted" style="margin-right:5%">CBM</div>                                                             
													 </div>
													<?php }	 ?>    
                                                      
                                                </div> 
                                            </div>   
                                        <?php }	 ?>  
                                        
                                    </div>
                                    </div> 
                                </div>
                           

                        </div>   
                          
                    </div>
           </div>
      </div>  
      
      <!-- <div class="div-table-caption border-orange"><?php echo ucwords($obj->lang['others']); ?></div> -->
      <!-- <div class="div-tab-panel"> 
         <div class="div-table main-tab-table-2">
            <div class="div-table-row">
               <div class="div-table-col">
                     <div class="div-tab-panel"  style="margin-bottom:10px">
                     <div class="col-xs-12 section-title">DOKUMEN</div>  
                                       <div class="form-group"> 
                                          <div class="col-xs-12">
                                             <div class="flex">
                                                   <div class="consume">
                                                      Tgl. Terima Dokumen<br>
                                                      <?php echo $obj->inputDate('dateDoc', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div> 
                                                   <div class="consume">
                                                      Shipping Instruction<br>
                                                      <?php echo $obj->inputDate('siDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div>
                                                   <div class="consume">
                                                      Booking Confirmation<br>
                                                      <?php echo $obj->inputDate('bookingDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div>
                                             </div> 
                                          </div> 
                                       </div> 
                                       <div class="form-group"> 
                                          <div class="col-xs-12">
                                             <div class="flex"> 
                                                   <div class="consume">
                                                      <?php echo ucwords($obj->lang['invoice']); ?><br>
                                                      <?php echo $obj->inputDate('invoiceDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div> 
                                                   <div class="consume">
                                                      Packing List<br>
                                                      <?php echo $obj->inputDate('packingDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div> 
                                                   <div class="consume">
                                                      <?php echo ucwords($obj->lang['insurance']); ?><br>
                                                      <?php echo $obj->inputDate('insuranceDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div>
                                             </div> 
                                          </div> 
                                       </div>   
                                       <div class="form-group"> 
                                          <div class="col-xs-12">
                                             <div class="flex"> 
                                                   <div class="consume">
                                                      Form: D, E, AK, Jiepa, dll<br>
                                                      <?php echo $obj->inputDate('formDEADate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div>  
                                                   <div class="consume"></div>
                                                   <div class="consume"></div>
                                             </div> 
                                          </div> 
                                       </div>   
                              </div>
                              <div class="div-tab-panel">
                              <div class="col-xs-12 section-title">CLEAREANCE</div>  
                                          <div class="form-group"> 
                                             <div class="col-xs-12">
                                                   <div class="flex"> 
                                                      <div class="consume">
                                                         Nilai pabean<br>
                                                         <?php echo $obj->inputDecimal('pabean'); ?>
                                                      </div> 
                                                      <div class="consume">
                                                         Incoterm<br>
                                                            <?php echo $obj->inputText('incoterm'); ?>
                                                      </div> 
                                                      <div class="consume">
                                                         Lartas<br>
                                                         <?php echo $obj->inputText('lartas'); ?>
                                                      </div>
                                                   </div> 
                                             </div> 
                                          </div>   
                                          <div class="form-group"> 
                                             <div class="col-xs-12">
                                                   <div class="flex"> 
                                                      <div class="consume">
                                                         Jumlah / jenis kemasan<br>
                                                         <?php echo $obj->inputText('qtyPack'); ?>
                                                      </div> 
                                                      <div class="consume">
                                                         Transfer PIB<br>
                                                         <?php echo $obj->inputDate('transferPIBDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                      </div> 
                                                      <div class="consume">
                                                         Respon / P. Jaluran<br>
                                                         <?php echo $obj->inputText('response'); ?>
                                                      </div>
                                                   </div> 
                                             </div> 
                                          </div>  
                        
                                          <div class="form-group"> 
                                             <div class="col-xs-12">
                                                   <div class="flex"> 
                                                      <div class="consume">
                                                         NPE<br>
                                                         <?php echo $obj->inputDate('npeDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                      </div>  
                                                      <div class="consume"></div>
                                                      <div class="consume"></div>
                                                   </div> 
                                             </div> 
                                          </div>  
                           
                                       
                              </div>
               </div> 
               <div class="div-table-col">
               
                     <div class="div-tab-panel">
                              <div class="col-xs-12 section-title">OPERASIONAL</div>  

                                       <div class="form-group"> 
                                          <div class="col-xs-12">
                                             <div class="flex"> 
                                                   <div class="consume">
                                                      Adm D / O<br>
                                                      <?php echo $obj->inputDate('admDODate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div> 
                                                   <div class="consume">
                                                      THC / CFS / LAINNYA<br>
                                                      <?php echo $obj->inputDate('thcDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div> 
                                             </div> 
                                          </div> 
                                       </div> 
                                       <div class="form-group"> 
                                          <div class="col-xs-12">
                                             <div class="flex">  
                                                   <div class="consume">
                                                      Penyerahan NPE<br>
                                                      <?php echo $obj->inputDate('deliveryNPEDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div> 
                                                   <div class="consume">
                                                      Tgl. Terbit Kartu Expor<br>
                                                      <?php echo $obj->inputDate('exportCardDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div>
                                             </div> 
                                          </div> 
                                       </div>  
                                       <div class="form-group"> 
                                                         <div class="col-xs-12">     
                                                               <div class="flex"> 
                                                                     <div class="consume">
                                                                           <?php echo ucwords($obj->lang['terminal']); ?><br>
                                                                           <?php  echo $obj->inputAutoComplete(array(  
                                                                                                               'element' => array('value' => 'terminalName',
                                                                                                                                 'key' => 'hidTerminalKey'),
                                                                                                               'source' =>array(
                                                                                                                                 'url' => 'ajax-terminal.php',
                                                                                                                                 'data' => array(  'action' =>'searchData' )
                                                                                                                              ) 
                                                                                                               )
                                                                                                      );  
                                                                           ?> 
                                                                     </div>  
                                                                        <div class="consume">
                                                                              <?php echo ucwords($obj->lang['depot']); ?><br>
                                                                              <?php  echo $obj->inputAutoComplete(array(  
                                                                                                               'element' => array('value' => 'depotName',
                                                                                                                                 'key' => 'hidDepotKey'),
                                                                                                               'source' =>array(
                                                                                                                                 'url' => 'ajax-depot.php',
                                                                                                                                 'data' => array(  'action' =>'searchData' )
                                                                                                                              ) 
                                                                                                               )
                                                                                                      );  
                                                                              ?> 
                                                                           </div>
                                                               </div> 
                                                         </div>   
                                             </div> 
                                       <div class="form-group"> 
                                          <div class="col-xs-12">
                                             <div class="flex"> 
                                                   <div class="consume">
                                                      Lift On<br>
                                                      <?php echo $obj->inputDate('liftOnDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div>  
                                                   <div class="consume">
                                                      <?php echo $obj->lang['stuffing']; ?> <br>
                                                      <?php echo $obj->inputDate('stuffingIn', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                   </div>
                                             </div> 
                                          </div> 
                                       </div>  
                                 <div class="form-group"> 
                                       <div class="col-xs-12">     
                                          <?php echo ucwords($obj->lang['stuffingLocation']); ?><br>
                                          <?php echo $obj->inputText('stuffingLocation'); ?> 
                                       </div>  
                                 </div>  
                           
                              </div>
               </div>
               
            </div>
         </div>      
      </div> -->
        
    <div style="clear:both; height:1em;"></div> 
        
    <div class="div-tab-panel doc-detail-only">
        
    <div class="div-table mnv-transaction transaction-detail no-odd-even-style" style="width:100%;" attr-level="0">
        
         <?php  
                        
                $totalRows = count($rsSalesDetail);  
                for($i=0;$i<=$totalRows;$i++){ 
					
                    $class =  'transaction-detail-row';
                    $overwrite = true;
                    $disable = '';  
                    $rsItemDetail = array();
                    $rsCommissionDetail = array();
                    $totalIssueRows = 0;
                    $totalItemRows = 0;
                    $showOtherCurrency = false;
                    $readonlyDetail = false;
					
					$refhblcode = '';
					$refhblkey = '';
					
                    if ($i == $totalRows ){
                        $class = 'detail-row-template row-template ';
                        $overwrite = false; 
                        $disable = 'disabled="disabled"';  
                        $readonlyRate = true;
                        $_POST['detailSalesCode[]'] = '[auto code]'; 
						$_POST['chkIsManual[]'] = 1;
						$_POST['hblLock[]'] = 0;
						$_POST['invoiceList[]'] = '';
                        $activeCurrency = 'IDR';
                    } else {    
                        
                        $_POST['hidDetailKey[]'] =  $rsSalesDetail[$i]['pkey'];
                        $_POST['detailSalesCode[]'] = $rsSalesDetail[$i]['code']; 
						
						if($isActiveMasterHBL){ 
							
						}else{
							$_POST['detailHBL[]'] = $rsSalesDetail[$i]['hbl'];
						}
						
						
						$_POST['detailHBL[]'] = $rsSalesDetail[$i]['hbl']; 
                        $_POST['hidDetailHBLKey[]'] =  $rsSalesDetail[$i]['hblkey'];
                        $_POST['hidSalesDetailKey[]'] =  $rsSalesDetail[$i]['saleskey'];
                        $_POST['salesDetailName[]'] =  $rsSalesDetail[$i]['salesname'];
                        $_POST['customerDetailName[]'] =  $rsSalesDetail[$i]['customername'];
                        $_POST['hidCustomerDetailKey[]'] =  $rsSalesDetail[$i]['customerkey'];
                        $_POST['selSellingFreightTerm[]'] =  $rsSalesDetail[$i]['freighttermkey'];
                        $_POST['selSellingCurrency[]'] =  $rsSalesDetail[$i]['currencykey'];
                        $_POST['sellingCurrencyRate[]'] =  $obj->formatNumber($rsSalesDetail[$i]['rate'],2);
                        $_POST['detailTotal[]'] =  $obj->formatNumber($rsSalesDetail[$i]['subtotal'],2); 
                        $_POST['detailCurrencyTotal[]'] =  $obj->formatNumber($rsSalesDetail[$i]['subtotalcurrency'],2);
                        $_POST['detailOtherCurrencyTotal[]'] =  $obj->formatNumber($rsSalesDetail[$i]['subtotalothercurrency'],2);
                        $_POST['detailQty[]'] =  $obj->formatNumber($rsSalesDetail[$i]['qty']);
                        $_POST['detailSelUnit[]'] =  $rsSalesDetail[$i]['unitkey'];
                        $_POST['detailWeight[]'] =  $obj->formatNumber($rsSalesDetail[$i]['weight'],2);
                        $_POST['detailMeasurement[]'] =  $obj->formatNumber($rsSalesDetail[$i]['measurement'],2);
                        $_POST['hidDestinationDetailKey[]'] =  $rsSalesDetail[$i]['destinationkey'];
                        $_POST['destinationDetailName[]'] =  $rsSalesDetail[$i]['destinationname']; 
                        $_POST['detailDescription[]'] =  $rsSalesDetail[$i]['description'];
                        $_POST['chkIsManual[]'] =  $rsSalesDetail[$i]['ismanual'];
                        $_POST['hblLock[]'] = ($rsSalesDetail[$i]['ismanual']) ? 0 : 1;
							
                        $showOtherCurrency = ($_POST['detailOtherCurrencyTotal[]'] <> 0) ? true : false; 
                        $activeCurrency =  $rsSalesDetail[$i]['currencyname'];
                        $readonlyDetail = (in_array($rsSalesDetail[$i]['pkey'],$arrInvoicedKey)) ? true : false;
                        
						// perlu tambahin rel karena, ketika hapus detail row, semua checkbox ke trigger lg (karena ini 3 level)
						$refhblcode = $rsSalesDetail[$i]['hbl'];
						$refhblkey = $rsSalesDetail[$i]['hblkey'];

                        if($readonlyDetail)
                            $readonlyRate = true;
                        else
                            $readonlyRate = ($_POST['selSellingCurrency[]'] == CURRENCY['idr'] ) ? true : false;
 
						$rsInvoice = (isset( $rsInvoiceCol[$rsSalesDetail[$i]['pkey']] )) ? $rsInvoiceCol[$rsSalesDetail[$i]['pkey']] : array();
						$_POST['invoiceList[]'] = (!empty($rsInvoice)) ?  implode(', ',array_column($rsInvoice,'code')) : '';
				
                        //$obj->setLog($readonlyDetail,true); 
                    }
					
					
					if($isActiveMasterHBL){
					 
						// kalo settingannya manual, 
						if($_POST['chkIsManual[]'] == 1){
							// kalo manual, hbl bisa diketik bebas
							$hblReadOnly = false;	
							$showButtonHBL = 'display:none';
						} else{
							$hblReadOnly = true;	
							
							// tombol muncul jika bukan manual dan blm keisi
							// manual sudah pasti gk muncul 
							// karena ada row terakhir utk template
							$showButtonHBL =  (empty($rsSalesDetail[$i]['hblkey'])) ? '' : 'display:none';
						}

					}else{
						$showButtonHBL =  'display:none';
					}
					
					   
					

            ?>


        <div class="div-table-row customer-row <?php echo $class; ?>"  refhblcode="<?php echo $refhblcode; ?>" refhblkey="<?php echo $refhblkey; ?>">
            <div class="div-table-col" style="padding:0; padding-top:1.5em">   
                <div class="div-table row-panel" style="width:100%">
                    <!--<div style="position:absolute; top: 0.5em; right:0.5em"><?php echo $obj->inputLinkButton('btnDeleteSupplierRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?></div>-->
                <div class="div-table-row">
                    <div class="div-table-col detail-col-detail" style="padding:1em 0;">

                    <div class="div-table" style="width:100%;"> 
                     <div class="div-table-row customer-row-header">   
                         <div class="div-table-col" style="width:450px;">
                             <div class="div-table"  style="width:96%;">
                                <div class="div-table-row">
                                    <div class="div-table-col" style="width:100px; font-weight:bold"><?php echo $obj->lang['code']; ?></div>
                                    <div class="div-table-col">
                                    <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable)); ?> 
                                    <?php echo  $obj->inputText('detailSalesCode[]', array('readonly' => true , 'value' => '[auto code]','disabled' =>  $disable, 'class' =>'form-control label-style')); ?>
                                    </div>
                                </div>
                                 <div class="div-table-row">
                                    <div class="div-table-col" style="font-weight:bold"><?php echo $obj->lang['invoiceTo']; ?></div>
                                    <div class="div-table-col" style="width:170px;">
                                    <?php echo $obj->inputText('customerDetailName[]',array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>
                                    <?php echo $obj->inputHidden('hidCustomerDetailKey[]',array('overwritePost' => $overwrite,'disabled' =>  $disable)); ?>
								   </div>
                                </div> 
								<div class="div-table-row">
                                    <div class="div-table-col" style="font-weight:bold"><?php echo $obj->lang['invoiceNumber']; ?></div>
                                    <div class="div-table-col" style="width:170px;">
                                    <?php echo $obj->inputText('invoiceList[]',array('readonly' => true, 'class' =>'form-control label-style')); ?>
                                    </div>
                                </div>   
								 
                                <div class="div-table-row">
                                    <div class="div-table-col" style="font-weight:bold"><?php echo $obj->lang['hbl']; ?></div>
                                    <div class="div-table-col">
									<div class="flex">
											<?php if($isActiveMasterHBL) { ?>
												<div style="margin-right:0.5em"><?php echo $obj->inputCheckBox('chkIsManual[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail ,'disabled' =>  $disable, 'class' =>'form-control ')); ?></div> 
											<?php } ?>
                                            <div class="consume"><?php
																		// kalo readonly dr invoice, sudah mutlak, kalo blm ke lock, baru dicek dengang auto HBL nya
																		echo $obj->inputHidden('hblLock[]',array('overwritePost' => $overwrite, 'disabled' =>  $disable));
																		echo $obj->inputText('detailHBL[]', array('overwritePost' => $overwrite, 'readonly' => ($readonlyDetail) ? $readonlyDetail : $hblReadOnly  ,'disabled' =>  $disable, 'class' =>'form-control label-style')); 
																		echo $obj->inputHidden('hidDetailHBLKey[]', array('overwritePost' => $overwrite, 'class' =>'form-control label-style'));
						 										?>
											</div>  
										  	<div class="icon-button add-hbl" style="<?php echo $showButtonHBL; ?>"><i class="fas fa-file-medical"></i></div>
                                    </div> 
									</div>
                                </div>

<!--
                                <div class="div-table-row">
                                    <div class="div-table-col" style="font-weight:bold"><?php echo $obj->lang['pod']; ?></div>
                                    <div class="div-table-col">
                                         <?php echo $obj->inputText('detailPOD[]',array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>
										 <?php echo $obj->inputHidden('hidDetailPODKey[]',array('overwritePost' => $overwrite,'disabled' =>  $disable)); ?>
                                	</div>
                                </div>
-->

                                 <div class="div-table-row">
                                    <div class="div-table-col" style="font-weight:bold"><?php echo $obj->lang['qty']; ?></div>
                                    <div class="div-table-col"> 
                                        <div class="flex">
                                            <div class="consume"><?php echo  $obj->inputNumber('detailQty[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail ,'disabled' =>  $disable, 'add-class' => 'label-style', 'etc' => 'style="text-align:right"')); ?></div>
                                            <div  style="font-weight:bold"><?php echo $obj->inputSelect('detailSelUnit[]', $arrUnit, array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable,'add-class' => 'label-style')); ?></div>
                                            <div style="width:60px"><?php echo $obj->inputDecimal('detailWeight[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable,'add-class' => 'label-style', 'etc' => 'style="text-align:right"')); ?></div>
                                            <div style="font-weight:bold">KG</div>  
                                            <div style="width:60px"><?php echo $obj->inputDecimal('detailMeasurement[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable,'add-class' => 'label-style', 'etc' => 'style="text-align:right"')); ?></div>
                                            <div style="font-weight:bold">CBM</div>   
                                        </div> 
                                    </div>
                                </div>  
                             </div>   
                         </div> 
                         <div class="div-table-col" style="width:20px;"> </div>
                         <div class="div-table-col"  style="width:400px;"> 
                             <div class="div-table" style="width: 100%"> 
                                <div class="div-table-row">
                                    <div class="div-table-col" style="width: 100px; font-weight:bold"><?php echo $obj->lang['payment']; ?></div>
                                    <div class="div-table-col"> 
                                        <div class="flex">
                                            <div style="width:50px"><?php echo $obj->inputSelect('selSellingFreightTerm[]', $arrFreight, array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail ,'disabled' =>  $disable, 'class' =>'form-control label-style')); ?></div>
                                            <div style="width:50px"><?php echo $obj->inputSelect('selSellingCurrency[]', $arrCurrency, array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable,'class' =>'form-control label-style')); ?></div>
                                            <div class="consume"><?php echo $obj->inputDecimal('sellingCurrencyRate[]', array('overwritePost' => $overwrite, 'value'=>1, 'readonly' => $readonlyRate, 'disabled' =>  $disable, 'class' =>'form-control inputdecimal label-style')); ?></div>
                                        </div> 
                                    </div>
                                </div> 
                                <div class="div-table-row">
                                    <div class="div-table-col" style="font-weight:bold"><?php echo $obj->lang['subtotal']; ?></div>
                                    <div class="div-table-col">
                                        <div class="flex"> 
                                            <div class="currency-only" style="width:40px; font-weight:bold"><span class="subheader-active-currency"><?php echo $activeCurrency; ?></span></div>
                                            <div class="currency-only consume" style="width:100px"><?php echo $obj->inputDecimal('detailCurrencyTotal[]', array('overwritePost' => $overwrite, 'disabled' =>  $disable, 'readonly' =>true, 'class' =>'form-control inputdecimal label-style')); ?></div>
                                           <!-- <div style="width:30px; font-weight:bold"><span>IDR</span></div>
                                            <div class="consume"><?php echo $obj->inputDecimal('detailTotal[]', array('overwritePost' => $overwrite, 'disabled' =>  $disable, 'readonly' =>true, 'class' =>'form-control inputdecimal label-style')); ?></div>-->
                                        </div> 
                                    </div>
                                </div> 
                                  <div class="div-table-row idr-only" style="<?php if(!$showOtherCurrency) echo 'display:none'; ?>">
                                    <div class="div-table-col" style="font-weight:bold"></div>
                                    <div class="div-table-col">
                                        <div class="flex"> 
                                            <div class="currency-only" style="width:40px; font-weight:bold">IDR</div>
                                            <div class="currency-only consume" style="width:100px"><?php echo $obj->inputDecimal('detailOtherCurrencyTotal[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail, 'disabled' =>  $disable, 'readonly' =>true, 'class' =>'form-control inputdecimal label-style')); ?></div>  
                                        </div>
                                    </div>
                                </div> 

                                   <div class="div-table-row">
                                    <div class="div-table-col" style="font-weight:bold"><?php echo $obj->lang['total']; ?></div>
                                    <div class="div-table-col">
                                        <div class="flex">
                                            <div  style="width:40px; font-weight:bold"><span>IDR</span></div>
                                            <div class="consume" style="width:250px"> <?php echo  $obj->inputDecimal('detailTotal[]', array('overwritePost' => $overwrite, 'readonly' => true ,'disabled' =>  $disable, 'class' =>'form-control inputdecimal label-style')); ?></div>
                                            
                                        </div>
                                    </div>
                                </div>

                                <div class="div-table-row">
                                    <div class="div-table-col" style="width:80px; font-weight:bold; vertical-align:top"><?php echo $obj->lang['description']; ?></div>
                                    <div class="div-table-col">
                                        <?php echo  $obj->inputTextArea('detailDescription[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail ,'disabled' =>  $disable, 'class' =>'form-control label-style','etc' => 'style="height:6em;"')); ?>
                                    </div>
                                </div>
                             </div>     
                         </div> 
                         <div class="div-table-col"> </div>
                     </div>
                    </div>
				 
                    <div><?php echo $obj->inputButton('btnImport[]', $obj->lang['import'].' Quotation',array('class' =>'btn btn-primary btn-princeton-orange btn-second-tone', 'etc' => 'style="margin-top:1em;" ')); ?></div>
		
							
                    <div class="div-table transaction-detail detail-item" style="margin-top:1em; width:100%; border-bottom:1px solid #333;"  attr-level="1" attr-group="hidDetailItemKey">
                      <div class="div-table-row">   
                        <div class="div-table-col detail-col-header"  style="width:40px;">Reim</div> 
                        <div class="div-table-col detail-col-header fcl-only sea-only"  style="width:100px;"><?php echo ucwords($obj->lang['containerType']); ?></div> 
                        <div class="div-table-col detail-col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                        <div class="div-table-col detail-col-header"  ><?php echo ucwords($obj->lang['service']); ?></div> 
                        <div class="div-table-col detail-col-header" style="width:80px;"><?php echo ucwords($obj->lang['currencyShort']); ?></div>
                        <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?></div>
                        <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?></div> 
                        <div class="div-table-col detail-col-header" style="width:50px; text-align:right;"></div> 
                        <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['subtotal']); ?> <span class="text-muted">IDR</span></div> 
                        <!--<div class="div-table-col detail-col-header" style="width:40px; text-align:center"><?php echo ucwords($obj->lang['vat']); ?></div> -->
                        <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                        <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col"></div> 
                      </div>
                        <?php  
                            $itemDetailKey = (isset($rsSalesDetail[$i])) ? $rsSalesDetail[$i]['pkey'] : 0;
                            $rsItemDetail = $obj->getItemDetail($itemDetailKey); 
                            $totalItemRows = count($rsItemDetail);
                    
                            for ($j=0;$j<=$totalItemRows; $j++){  

                                $class =  'transaction-detail-row';
                                $overwrite = true;
                                $disable = '';  
                                $activeCurrencyKey =  CURRENCY['idr'] ;
                                $numberClass = 'inputdecimal';
                                
                                $readonly = false;
                                    
                                if ($j == $totalItemRows ){
                                    $class = 'item-row-template row-template';
                                    $overwrite = false; 
                                    $disable = 'disabled="disabled"';  
                                } else {  
                                    
                                    $_POST['chkIsReimburse[]'] =  $rsItemDetail[$j]['isreimburse'];
                                    $_POST['hidDetailItemKey[]'] =  $rsItemDetail[$j]['pkey'];
                                    $_POST['hidContainerDetailKey[]'] =  $rsItemDetail[$j]['itemkey']; 
                                    $_POST['containerDetailName[]'] =  $rsContainer[$rsItemDetail[$j]['itemkey']]; 
                                    $_POST['hidServiceKey[]'] =  $rsItemDetail[$j]['servicekey']; 
                                    $_POST['serviceName[]'] =  $rsService[$rsItemDetail[$j]['servicekey']]; 
                                    $_POST['qty[]'] = $obj->formatNumber($rsItemDetail[$j]['qty'], 3);
                                    $_POST['priceInUnit[]'] = $obj->formatNumber($rsItemDetail[$j]['priceinunit'], 2);
                                    $_POST['detailRowSubtotal[]'] = $obj->formatNumber($rsItemDetail[$j]['subtotal'],2);
                                    $_POST['detailRowCurrencySubtotal[]'] =  $obj->formatNumber($rsItemDetail[$j]['subtotalcurrency'],2);
                                    $_POST['isVat[]'] =  $rsItemDetail[$j]['isvat'];
                                    $_POST['selCurrencyDetail[]'] =  $rsItemDetail[$j]['currencykey'];
                                        
                                    $activeCurrencyKey = $rsItemDetail[$j]['currencykey'];
                                    
                                    $numberClass = 'inputdecimal'; // ($_POST['selCurrencyDetail[]'] == CURRENCY['idr'] ) ? 'inputnumber' : 'inputdecimal';
                                    
                                    $readonly = ($rsItemDetail[$j]['qtyinvoiced'] > 0) ? true : false;
                                } 

                        ?>


                        <div class="div-table-row <?php echo $class; ?>"> 
                            <div class="div-table-col detail-col-detail" style="width:40px; text-align:center;"><?php echo $obj->inputCheckBox('chkIsReimburse[]', array('overwritePost' => $overwrite, 'readonly' => false, 'disabled' => $disable)); ?>
                            </div>
                            <div class="div-table-col  fcl-only sea-only detail-col-detail">
                                <?php echo $obj->inputHidden('hidDetailItemKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputHidden('hidContainerDetailKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputText('containerDetailName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' =>  $disable)); ?>
                            </div>   
                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('qty[]', array('overwritePost' => $overwrite,'readonly' => $readonly, 'value' => 0, 'etc' => 'style="text-align:right;" mnv-attr-decimal="3";', 'disabled' =>  $disable)); ?></div>
                            <div class="div-table-col detail-col-detail">
                                <?php echo $obj->inputHidden('hidServiceKey[]',array('overwritePost' => $overwrite,'readonly' => $readonly, 'disabled' =>  $disable)); ?>
                                <?php echo $obj->inputText('serviceName[]',array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?>
                            </div> 
                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputSelect('selCurrencyDetail[]',$arrCurrency, array('overwritePost' => $overwrite, 'readonly' => $readonly,'disabled' =>  $disable)); ?></div>
                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('priceInUnit[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"','readonly' => $readonly, 'disabled' =>  $disable, 'class' =>'form-control ' . $numberClass)); ?></div>
                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('detailRowCurrencySubtotal[]', array('overwritePost' => $overwrite,'readonly' => true, 'etc' => 'style="text-align:right;" ', 'disabled' =>  $disable)); ?></div>
                            <div class="div-table-col detail-col-detail active-currency text-muted"><?php echo $arrCurrencyName[$activeCurrencyKey]['name'] ;?></div>
                            <div class="div-table-col detail-col-detail"><?php echo $obj->inputDecimal('detailRowSubtotal[]', array('overwritePost' => $overwrite,'readonly' => true, 'etc' => 'style="text-align:right;" ' ,  'disabled' =>  $disable)); ?></div>
                            <!--<div class="div-table-col detail-col-detail" style="text-align:center"><?php echo $obj->inputCheckBox('isVat[]', array('overwritePost' => $overwrite,'readonly' => $readonly,'disabled' =>  $disable)); ?></div>-->
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="item-row-template"')) ?></div>
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnDeleteSalesRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')) : ''; ?></div>
                        </div>

                    <?php } ?> 

                    </div>  
                   
                    </div> 
                    <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>" style="vertical-align:top; padding-top:1em !important"><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('etc'=>'tabIndex="-1"','class' => 'btn btn-link remove-button')); ?></div>
                </div>
                    
                </div>
            </div>
        </div> 
         <?php } ?> 
    </div>    
         
      <div style="clear:both; height:1em;"></div> 
      <div class="isMaster <?php echo $isMaster; ?>" style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>    
     
    </div>  
       
        <div class="form-button-margin"></div>
        <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>  
   
     <?php echo $obj->showDataHistory(); ?>
    
</div> 
</body>

</html>
