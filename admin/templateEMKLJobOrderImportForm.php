<?php 
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('TemplateEMKLJobOrder.class.php', 'EMKLJobOrder.class.php');
$templateEMKLJobOrderImport = createObjAndAddToCol(new TemplateEMKLJobOrder(EMKL['jobType']['import']));
$emklJobOrderImport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['import']));

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
$depot = createObjAndAddToCol(new Depot());
$city = createObjAndAddToCol(new City());
$country = createObjAndAddToCol(new Country());


$obj = $templateEMKLJobOrderImport; 
 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
 
$formAction = 'templateEMKLJobOrderImportList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','textcolor');   
$rs = prepareOnLoadData($obj); 

$useShippingInstruction = $obj->loadSetting('useShippingInstruction');
$containerQtyUnit = $obj->loadSetting('containerQtyUnit');

$rsDetail = array();
$rsContactPerson = array(); 
$rsSalesDetail = array();
$rsItemDetail = array();
$rsCommisionDetail = array(); 
$rsInvoiced = array();  
$rsVolumeDetail = array();
$rsContainerDetail = array();
$rsCommodityDetail = array();

$_POST['trDate'] = date('d / m / Y'); 

$_POST['selTypeOfJob'] = EMKL['jobType']['import'];

$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';
    
$arrInvoicedKey = array();
$arrType = array(); 
 
$isMaster = '';

$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');    

$dateReturnOnEmpty = array('returnOnEmpty'=>true, 'value' => '00 / 00 / 0000');

$useJobOrderHeader = $obj->loadSetting('useJobOrderHeader');

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  

    $rsVolumeDetail = $obj->getDetailVolume($id);  
    $rsCommodityDetail = $obj->getDetailCommodity($id);

	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y '); 
    
    $_POST['name'] = $rs[0]['name'];
    $_POST['hidCargoType'] = $rs[0]['containertypekey'];

        
    if (!empty($rs[0]['saleskey'])){
        $_POST['hidSalesKey'] = $rs[0]['saleskey']; 
		$rsSales = $employee->getDataRowById($rs[0]['saleskey']);
		$_POST['salesName'] = $rsSales[0]['name'];
    }
	
    if (!empty($rs[0]['carrierkey'])){
        $_POST['hidCarrierKey'] = $rs[0]['carrierkey']; 
		$rsCarrier = $supplier->getDataRowById($rs[0]['carrierkey']);
		$_POST['carrierName'] = $rsCarrier[0]['name'];
	} 
	
    if (!empty($rs[0]['agentkey'])){
        $rsAgent = $customer->getDataRowById($rs[0]['agentkey']);
        $_POST['hidAgentKey'] = $rs[0]['agentkey'];
        $_POST['agentName'] = $rsAgent[0]['name'];
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
	  
	if (!empty($rs[0]['placeofdeliverykey'])){
        $rsPODelivery = $port->getDataRowById($rs[0]['placeofdeliverykey']);
        $_POST['hidPlaceOfDeliveryKey'] = $rs[0]['placeofdeliverykey'];
        $_POST['placeOfDeliveryName'] = $rsPODelivery[0]['name'];
    }

	
	if (!empty($rs[0]['placeofreceiptkey'])){
        $rsPODelivery = $port->getDataRowById($rs[0]['placeofreceiptkey']);
        $_POST['hidPlaceOfReceiptKey'] = $rs[0]['placeofreceiptkey'];
        $_POST['placeOfReceiptName'] = $rsPODelivery[0]['name'];
    }


    if (!empty($rs[0]['terminalkey'])){
        $rsTerminal = $terminal->getDataRowById($rs[0]['terminalkey']);
        $_POST['hidTerminalKey'] = $rs[0]['terminalkey'];
        $_POST['terminalName'] = $rsTerminal[0]['name'];
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
    $_POST['etdPol'] = $obj->formatDBDate($rs[0]['etdpol'],'d / m / Y', $dateReturnOnEmpty);
    $_POST['etaPod'] = $obj->formatDBDate($rs[0]['etapod'],'d / m / Y', $dateReturnOnEmpty);
	$_POST['vesselNumber'] = $rs[0]['vesselnumber'];
	$_POST['feederNumber'] = $rs[0]['feedernumber'];
	$_POST['poNumber'] = $rs[0]['ponumber']; 
	$_POST['aju'] = $rs[0]['aju']; 
    
    $_POST['stuffingIn'] = $obj->formatDBDate($rs[0]['stuffingin'],'d / m / Y',$dateReturnOnEmpty);

    
    
    //FORM BARU
    $_POST['consigneeName'] = $rs[0]['consigneename'];  
    
    $_POST['selShipmentTerm'] = $rs[0]['shipmenttermkey'];
    $_POST['selShipmentTerm2'] = $rs[0]['shipmentterm2key'];

    $_POST['hidFinalDestinationKey'] = $rs[0]['finaldestinationkey'];
    if(!empty($rs[0]['finaldestinationkey'])) {
        $rsCity = $city->searchData('','',true, ' and ' . $city->tableName.'.pkey = ('. $obj->oDbCon->paramString($rs[0]['finaldestinationkey']) .') ');
        $_POST['finalDestinationName'] = $rsCity[0]['name'];
    }

    $_POST['hidConnectingVesselKey'] = $rs[0]['connectingvesselkey'];
    $_POST['hidConnectingVessel2Key'] = $rs[0]['connectingvessel2key'];
    $_POST['hidConnectingVessel3Key'] = $rs[0]['connectingvessel3key'];

    if(!empty($rs[0]['connectingvesselkey'])) {
        $rsVessel = $vessel->getDataRowById($rs[0]['connectingvesselkey']);
        $_POST['connectingVesselName'] = $rsVessel[0]['name'];
    }
    if(!empty($rs[0]['connectingvessel2key'])) {
        $rsVessel = $vessel->getDataRowById($rs[0]['connectingvessel2key']);
        $_POST['connectingVessel2Name'] = $rsVessel[0]['name'];
    }
    if(!empty($rs[0]['connectingvessel3key'])) {
        $rsVessel = $vessel->getDataRowById($rs[0]['connectingvessel3key']);
        $_POST['connectingVessel3Name'] = $rsVessel[0]['name'];
    }

    $_POST['connectingVesselNumber'] = $rs[0]['connectingvesselnumber'];
    $_POST['connectingVessel2Number'] = $rs[0]['connectingvessel2number'];
    $_POST['connectingVessel3Number'] = $rs[0]['connectingvessel3number'];
    
    $_POST['selFreightTerm'] = $rs[0]['freighttermkey'];
    $_POST['selFreightTerm2'] = $rs[0]['freightterm2key'];
    $_POST['selShipmentType'] = $rs[0]['shipmenttypekey'];
	
    $_POST['hidConnectingCountryKey'] = $rs[0]['connectingcountrykey'];
    $_POST['hidConnectingCountry2Key'] = $rs[0]['connectingcountry2key'];
    $_POST['hidConnectingCountry3Key'] = $rs[0]['connectingcountry3key'];
    

    $_POST['serviceContract'] = $rs[0]['servicecontract'];

    $_POST['chkIsOverwriteNotifyParty'] = $rs[0]['isoverwritenotifyparty'];
    $_POST['hidNotifyPartyKey'] = $rs[0]['notifypartykey'];
    if (!empty($rs[0]['notifypartykey'])){
        $rsNotifyParty = $consignee->getDataRowById($rs[0]['notifypartykey']); 
        $_POST['notifyPartyName'] = $rsNotifyParty[0]['name'];
        $_POST['notifyPartyAddress'] = $rsNotifyParty[0]['address'];
    } else {
        $_POST['notifyPartyName1'] = $rs[0]['notifypartyname'];
        $_POST['notifyPartyAddress1'] = $rs[0]['notifypartyaddress'];
    }

    $_POST['alsoNotifyParty'] = $rs[0]['alsonotifyparty'];
    
    
    $_POST['qtyHeader'] = $obj->formatNumber($rs[0]['qty']);
    $_POST['selUnitKey'] = $rs[0]['unitkey'];
    $_POST['weightQty'] = $obj->formatNumber($rs[0]['weightqty'],2);
    $_POST['measurement'] = $obj->formatNumber($rs[0]['measurement'],2);

}

$rsCurrency = $currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1'.$editCurrencyInactiveCriteria.')');
$arrCurrencyName = array_column($rsCurrency,null,'pkey');
    
$rsContainer = $container->searchData();

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');      
$arrCargoType = $obj->convertForCombobox($emklJobOrderImport->getCargoType(),'pkey','name');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 
$arrCurrency = $class->convertForCombobox($rsCurrency,'pkey','name'); 
$arrJob = $class->convertForCombobox($emklJobOrderImport->getJobType(),'pkey','name');  
$arrTransportType = $class->convertForCombobox($emklJobOrderImport->getTransportationType(),'pkey','name');  
$arrContainer = $class->convertForCombobox($emklJobOrderImport->getLoadContainer(),'pkey','name');  
$arrVolume = $class->convertForCombobox($emklJobOrderImport->getVolumeUnit(),'pkey','name');  
$arrFreight = $class->convertForCombobox($emklJobOrderImport->getFreightTerm(),'pkey','name');  
$arrUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name');
$arrContainerVolume = $class->convertForCombobox($rsContainer,'pkey','name');  

$rsContainer = array_column($rsContainer,'name','pkey');

$rsService = $service->searchData();
$rsService = array_column($rsService,'name','pkey');
$arrShipmentTerm = $obj->generateComboboxOpt(array('data' => $emklJobOrderImport->getShipmentTerm())); 
$arrShipmentType = $obj->generateComboboxOpt(array('data' => $emklJobOrderImport->getShipmentType()));

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
</style> 
<script type="text/javascript">  
	jQuery(document).ready(function(){  
        
         var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
             
         var varConstant = {  
                            CURRENCY : <?php echo json_encode(CURRENCY); ?>,
                            EMKL : <?php echo json_encode(EMKL); ?> 
                            };
        
        
        var templatEMKLJobOrder = new TemplateEMKLJobOrder(tabID,<?php echo json_encode(
                                                                array(
                                                                    'rs' => $rs,
                                                                    'volumeDetail' => $rsVolumeDetail,
                                                                    'commodityDetail' => $rsCommodityDetail,
                                                                ) 
                                                            ); ?>,varConstant); 
         prepareHandler(templatEMKLJobOrder);   
         
        
         var fieldValidation =  { 
                                    code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
                                            }, 
                                        }
                                    }, 
                                 
                                    name: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.name[1]
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
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => false)); ?>
                                        </div> 
                                    </div>  
								 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"><?php echo $obj->inputAutoCode('code'); ?></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputDate('trDate'); ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['name']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputText('name'); ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['importir']); ?> / <?php echo ucwords($obj->lang['consignee']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array(  
                                                                                    'revalidateField' => true,
                                                                                    'element' => array('value' => 'shipperName',
                                                                                                       'key' => 'hidCustomerKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-customer.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ),  
                                                                                    'allowedStatusForEdit' => array (1),
                                                                                    'callbackFunction' => 'getTabObj().updateCustomerInformation()'
                                                                                  ) 
                                                                                );  
                                            ?> 
                                        </div> 
                                    </div>
                                      <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipmentTerm']); ?></label>
                                        <div class="col-xs-9">
                                            <div class="flex"> 
                                                <div class="consume"> 
                                                    <?php echo  $obj->inputSelect('selShipmentTerm', $arrShipmentTerm); ?>
                                                </div> 
                                                    <div>-</div>
                                                <div class="consume"> 
                                                    <?php echo  $obj->inputSelect('selShipmentTerm2', $arrShipmentTerm); ?>
                                                </div> 
                                            </div>                 
                                        </div>                 
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['freightTerm']); ?></label>
                                        <div class="col-xs-9">
                                            <div class="flex"> 
                                                <div class="consume"> 
                                                    <?php echo  $obj->inputSelect('selFreightTerm', $arrFreight); ?>
                                                </div> 
                                                    <div>-</div>
                                                <div class="consume"> 
                                                    <?php echo  $obj->inputSelect('selFreightTerm2', $arrFreight); ?>
                                                </div> 
                                            </div>                 
                                        </div>                 
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Service Contract</label> 
                                        <div class="col-xs-9">
                                              <?php echo $obj->inputText('serviceContract'); ?>
                                        </div> 
                                    </div> 

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipmentType']); ?></label>
                                        <div class="col-xs-9">
                                            <?php echo  $obj->inputSelect('selShipmentType', $arrShipmentType); ?>                
                                        </div>                 
                                    </div> 
                                   <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipper']); ?></label> 
                                    <div class="col-xs-9">  
                                        <?php echo $obj->inputText('consigneeName'); ?>

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
                                            <div class="consume"><?php echo  $obj->inputSelect('selAirSea', $arrTransportType); ?></div>
                                            <div style="width:100px"><?php echo  $obj->inputSelect('selContainerType', $arrContainer); ?></div>
                                            <div class="lcl-only" style="width:150px"><?php    
                                                                                            echo $obj->inputAutoComplete(array(
                                                                                                'objRefer' => $container, 
                                                                                                'revalidateField' => false, 
                                                                                                'element' => array('value' => 'containerName',
                                                                                                                   'key' => 'hidContainerKey'),
                                                                                                'source' =>array(
                                                                                                                    'url' => 'ajax-container.php',
                                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                                )
                                                                                                )
                                                                                              ); 
                                                    ?>  
                                              </div> 
                                              <div> <?php echo $obj->inputSelect('hidCargoType', $arrCargoType); ?> </div>
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
                                            <div class="div-table mnv-transaction transaction-detail" style="width:100%">
                                        <?php 
                                            $totalVolumeRows = count($rsVolumeDetail);
                                            for ($i=0;$i<=$totalVolumeRows; $i++){ 
                                                
                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $readonly = false;
                                                $disabled = false; 
                                                $style = '';

                                                if ($i == $totalVolumeRows ){
                                                    $class = 'volume-row-template';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                    $isLocked = false;
                                                    $style = 'style="display:none !important"';
                                                } else{ 
                                                    $_POST['hidDetailVolumeKey[]'] =  $rsVolumeDetail[$i]['pkey'];
                                                    $_POST['selContainerDetailVolumeKey[]'] =  $rsVolumeDetail[$i]['itemkey'];
                                                    $_POST['qtyVolume[]'] =  $obj->formatNumber($rsVolumeDetail[$i]['qty']);
                                                
                                                }
                                                $hideDeleteIcon = '';  
                                            ?>
                                            <div class="div-table-row <?php echo $class; ?> odd-style-adjustment" <?php echo $style; ?>> 
                                                <div class="div-table-col"  style="padding-left:0"> 
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

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords('QTY'); ?></label> 
                                        <div class="col-xs-9">   
                                            <div class="flex">
                                                <div ><?php echo $obj->inputNumber('qtyHeader'); ?></div>
                                                <div style="width:22%" style="margin-right:3x"><?php echo $obj->inputSelect('selUnitKey', $arrUnit, array('add-class' => 'label-style')); ?></div>
                                                <div ><?php echo  $obj->inputDecimal('weightQty'); ?></div>
                                                <div class="text-muted" style="margin-right:3px">KG</div> 
                                                <div ><?php echo  $obj->inputDecimal('measurement'); ?></div>
                                                <div class="text-muted" style="margin-right:3px">CBM</div> 
                                            </div>
                                        </div> 
                                    </div>

                                     <div class="form-group">
                                       <label class="col-xs-3 control-label">AJU PIB</label> 
                                        <div class="col-xs-9"> 
                                                <div class="consume"><?php echo $obj->inputText('aju'); ?></div>
                                        </div> 
                                    </div>  
								 <?php if ($useJobOrderHeader == 2) { ?>
								   <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['agent']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php  echo $obj->inputAutoComplete(array( 
                                                                'objRefer' => $customer,
                                                                'revalidateField' => true, 
                                                                'element' => array('value' => 'agentName',
                                                                'key' => 'hidAgentKey'),
                                                                'source' => array(
                                                                            'url' => 'ajax-customer.php',
                                                                            'data' => array(  'action' =>'searchData' )
                                                                        ) , 
                                                                'popupForm' => array(
                                                                'url' => 'customerForm.php',
                                                                'element' => array(
                                                                                'value' => 'agentName',
                                                                                'key' => 'hidAgentKey'
                                                                            ),
                                                                'width' => '600px',
                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['agent'])
                                                                ),
                                                            'allowedStatusForEdit' => array (1,2) 
                                                        )
                                                    );  
                                            ?>                                     
                                        </div> 
                                    </div>
								 <?php } ?>
							
                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['notifyParty']); ?></label>
                                        <div class="col-xs-9">
                                            <div class="flex">
                                                <div class="consume">
                                                    <div class="non-overwrite-notifyparty">
                                                        <?php
                                                        echo $obj->inputAutoComplete(
                                                            array(
                                                                'objRefer' => $consignee,
                                                                'element' => array(
                                                                    'value' => 'notifyPartyName',
                                                                    'key' => 'hidNotifyPartyKey'
                                                                ),
                                                                'source' => array(
                                                                    'url' => 'ajax-consignee.php',
                                                                    'data' => array('action' => 'searchData')
                                                                ),
                                                                'callbackFunction' => 'getTabObj().updateNotifyParty()'
                                                            )
                                                        );
                                                        ?>
                                                    </div>
                                                    <div class="overwrite-notifyparty"><?php echo $obj->inputText('notifyPartyName1'); ?></div>
                                                </div>
                                                <div style="padding-left: 0.5em">
                                                    <div style="float:left; margin-top:0.1em" rel="notifyparty">
                                                        <?php echo $obj->inputCheckBox('chkIsOverwriteNotifyParty'); ?>
                                                    </div>
                                                    <div style="float:left; margin-left:0.5em"><?php echo ucwords($obj->lang['overwrite']); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group ">
                                        <label class="col-xs-3 control-label"></label> 
                                        <div class="col-xs-9"> 
                                            <div>
												<div class="non-overwrite-notifyparty"><?php echo $obj->inputTextArea('notifyPartyAddress', array('readonly' => true, 'etc' => 'style="height:10em;"')); ?>
                                                </div>
                                                <div class="overwrite-notifyparty">
                                                    <?php echo $obj->inputTextArea('notifyPartyAddress1', array('etc' => 'style="height:10em;"')); ?></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords('Also Notify Party'); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('alsoNotifyParty', array('etc' => 'style="height:10em;"')); ?> 
                                        </div> 
                                    </div>
							
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['commodity']); ?> *</label> 
                                   <div class="col-xs-9">
                                        <div class="div-table mnv-commodity transaction-detail" style="width:100%">
                                        <?php 
                                            $totalRows = count($rsCommodityDetail);
                                            for ($j=0;$j<=$totalRows; $j++){ 
                                                
                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $readonly = false;
                                                $disabled = false;  

                                                if ($j == $totalRows ){
                                                    $class = 'commodity-row-template row-template';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                    $isLocked = false; 
                                                } else{ 
                                                    $_POST['hidDetailCommodityKey[]'] =  $rsCommodityDetail[$j]['pkey'];
                                                    $_POST['hidCommodityKey[]'] =  $rsCommodityDetail[$j]['commoditykey'];
                                                    $_POST['commodityName[]'] =  $rsCommodityDetail[$j]['commodityname'];
                                                
                                                }
                                                $hideDeleteIcon = '';  
                                            ?>
                                            <div class="div-table-row <?php echo $class; ?>  odd-style-adjustment" > 
                                                <div class="div-table-col"  style="padding-left:0"> 
													  <div class="flex" style="width:100%">     
															<div style="width:100%;">
															   <?php echo $obj->inputHidden('hidDetailCommodityKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                                <?php echo $obj->inputHidden('hidCommodityKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
																<?php echo $obj->inputText('commodityName[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>
															</div>                                                            
															<div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="commodity-row-template"')); ?></div>
															<div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; '.$hideDeleteIcon.'"')); ?></div>

														</div>  
												
                                                </div> 
                                            </div>   
                                        <?php }	 ?>  
                                        
                                    </div>
                                    </div> 
                                </div> 
                           
                             </div>
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['stuffingDestuffingInformation']); ?></div>
                             <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mblawb']); ?></label> 
                                    <div class="col-xs-9">     
                                           <?php echo $obj->inputText('mblNumber'); ?>
                                    </div>  
                                </div> 
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
                                   <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['motherVessel']); ?> / <?php echo ucwords($obj->lang['voyage']); ?></label> 
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['firstConnectingVessel']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume">
                                                    <?php                
                                                            echo $obj->inputAutoComplete(array(
                                                                                                'objRefer'=> $vessel,
                                                                                                'revalidateField' => false,
                                                                                                'element' => array('value' => 'connectingVesselName',
                                                                                                                'key' => 'hidConnectingVesselKey'),
                                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-vessel.php',
                                                                                                    'data' => array('action' =>'searchData')
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
                                                    <?php echo $obj->inputText('connectingVesselNumber'); ?> 
                                                </div>
                                            </div>  
                                        </div> 
                                    </div>  
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['secondConnectingVessel']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume">
                                                    <?php                
                                                            echo $obj->inputAutoComplete(array(
                                                                                                'objRefer'=> $vessel,
                                                                                                'revalidateField' => false,
                                                                                                'element' => array('value' => 'connectingVessel2Name',
                                                                                                                'key' => 'hidConnectingVessel2Key'),
                                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-vessel.php',
                                                                                                    'data' => array('action' =>'searchData')
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
                                                    <?php echo $obj->inputText('connectingVessel2Number'); ?> 
                                                </div>
                                            </div> 
                                        </div> 
                                    </div>  

                                    <!-- <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['thirdConnectingVessel']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume">
                                                    <?php                
                                                            echo $obj->inputAutoComplete(array(
                                                                                                'objRefer'=> $vessel,
                                                                                                'revalidateField' => false,
                                                                                                'element' => array('value' => 'connectingVessel3Name',
                                                                                                                'key' => 'hidConnectingVessel3Key'),
                                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-vessel.php',
                                                                                                    'data' => array('action' =>'searchData')
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
                                                    <?php echo $obj->inputText('connectingVessel3Number'); ?> 
                                                </div>
                                            </div>  
                                        </div> 
                                    </div>  -->

                                <!-- <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['firstConnectingAirport']); ?></label>
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'objRefer' => $country,
                                                        'revalidateField' => false,
                                                        'element' => array(
                                                            'value' => 'connectingCountryName',
                                                            'key' => 'hidConnectingCountryKey'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-country.php',
                                                            'data' => array('action' => 'searchData')
                                                        ),
                                                        'popupForm' => array(
                                                            'url' => 'countryForm.php',
                                                            'element' => array(
                                                                'value' => 'countryName',
                                                                'key' => 'hidCountryKey'
                                                            ),
                                                            'width' => '600px',
                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['country'])
                                                        )
                                                    )
                                                );
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->


                                <!-- <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['secondConnectingAirport']); ?></label>
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'objRefer' => $country,
                                                        'revalidateField' => false,
                                                        'element' => array(
                                                            'value' => 'connectingCountry2Name',
                                                            'key' => 'hidConnectingCountry2Key'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-country.php',
                                                            'data' => array('action' => 'searchData')
                                                        ),
                                                        'popupForm' => array(
                                                            'url' => 'countryForm.php',
                                                            'element' => array(
                                                                'value' => 'countryName',
                                                                'key' => 'hidCountryKey'
                                                            ),
                                                            'width' => '600px',
                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['country'])
                                                        )
                                                    )
                                                );
                                                ?>
                                            </div>
                        
                                        </div>
                                    </div>
                                </div> -->


                                <!-- <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['thirdConnectingAirport']); ?></label>
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'objRefer' => $country,
                                                        'revalidateField' => false,
                                                        'element' => array(
                                                            'value' => 'connectingCountry3Name',
                                                            'key' => 'hidConnectingCountry3Key'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-country.php',
                                                            'data' => array('action' => 'searchData')
                                                        ),
                                                        'popupForm' => array(
                                                            'url' => 'countryForm.php',
                                                            'element' => array(
                                                                'value' => 'countryName',
                                                                'key' => 'hidCountryKey'
                                                            ),
                                                            'width' => '600px',
                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['country'])
                                                        )
                                                    )
                                                );
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> -->

   
                              <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['finalDestination'] ?></label> 
                                        <div class="col-xs-9">  
                                               <?php  echo $obj->inputAutoComplete(array( 
                                                                    'objRefer' => $city,
                                                                    'revalidateField' => true, 
                                                                    'element' => array('value' => 'finalDestinationName',
                                                                                'key' => 'hidFinalDestinationKey'),
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
                                                                                'revalidateField' => false,  
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
                                                                                'allowedStatusForEdit' => array (1)
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
                                                                                'allowedStatusForEdit' => array (1)
                                                                            )
                                                                        );  
                                                    ?>    
                                                </div> 
                                            </div> 
                                    </div> 
                                </div>
							
							
                                <div class="form-group">
                                        <label class="col-xs-3 control-label">Shipping Line</label> 
                                        <div class="col-xs-9">  
                                               <?php  echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $supplier,
                                                                                    'revalidateField' => true, 
                                                                                    'element' => array('value' => 'carrierName',
                                                                                                       'key' => 'hidCarrierKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-supplier.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ) , 
                                                                                    'popupForm' => array(
                                                                                                    'url' => 'supplierForm.php',
                                                                                                    'element' => array('value' => 'carrierName',
                                                                                                           'key' => 'hidCarrierKey'),
                                                                                                    'width' => '600px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['carrier'])
                                                                                                )
                                                                                    )
                                                                            );  
                                                ?>                                     
                                        </div> 
                                    </div>
                                    
                        </div>   
                          
                    </div>
           </div>
      </div>  
      
       
        

       
        <div class="form-button-margin"></div>
        <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton();   ?>  
        </div> 
        
    </form>  
   
     <?php echo $obj->showDataHistory(); ?>
    
</div> 
</body>

</html>
