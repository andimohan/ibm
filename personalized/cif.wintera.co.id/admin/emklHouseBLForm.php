<?php
require_once '../../../_config.php';
require_once '../../../_include-v2.php';


includeClass(array('EMKLHouseBL.class.php'));
$emklHBL = createObjAndAddToCol( new EMKLHouseBL()); 
$port = createObjAndAddToCol(new Port());
$customer = createObjAndAddToCol(new Customer());
$consignee = createObjAndAddToCol(new Consignee());
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$vessel = createObjAndAddToCol(new Vessel());
$city = createObjAndAddToCol(new City());
$country = createObjAndAddToCol(new Country());
$supplier = createObjAndAddToCol(new Supplier());


$obj = $emklHBL;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
    
$formAction = 'emklHouseBLList';


$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$_POST['trDate'] = date('d / m / Y');
$_POST['telexDate'] = date('d / m / Y');

$arrUnitOfMeas = array();

$rs = prepareOnLoadData($obj);

$rsContainerDetail = array();
$arrContainerNumber = array();
$arrDataContainerNumber = array();

$arrTemp = array(); 
$arrTemp['pkey'] = 0;
$arrTemp['containerno'] = '-----';
array_push($arrDataContainerNumber,$arrTemp);

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
    
    $rsJO = $emklJobOrder->searchDataForInvoice($emklJobOrder->tableNameDetail.'.pkey',$rs[0]['refkey'],true);

    $rsContainerDetail = $obj->getDetailHBLContainer($rs[0]['pkey']);

    $_POST['hidJobOrderKey'] = $rsJO[0]['pkey'];
    $_POST['jobOrderCode'] = $rsJO[0]['value'];

    if (!empty($rs[0]['shipperkey'])){
        $rsShipper = $customer->getDataRowById($rs[0]['shipperkey']); 
        $_POST['shipperName'] = $rsShipper[0]['name'];
        $_POST['shipperAddress'] = $rsShipper[0]['address'];
    }
    
    if (!empty($rs[0]['consigneekey'])){
        $rsConsignee = $consignee->getDataRowById($rs[0]['consigneekey']); 
        $_POST['consigneeName'] = $rsConsignee[0]['name'];
        $_POST['consigneeAddress'] = $rsConsignee[0]['address'];
    }
    
    if (!empty($rs[0]['carrierkey'])){
        $rsCarrier = $consignee->getDataRowById($rs[0]['carrierkey']); 
        $_POST['carrierName'] = $rsCarrier[0]['name'];
        $_POST['carrierAddress'] = $rsCarrier[0]['address'];
    } else {
        $_POST['carrierAddress1'] = $rs[0]['carrieraddress'];
    }

    if (!empty($rs[0]['podkey'])){
        $rsPOD = $port->getDataRowById($rs[0]['podkey']); 
        $_POST['podName'] = $rsPOD[0]['name'];
    }
    
       if (!empty($rs[0]['podeliverykey'])){
        $rsPODelivery = $port->getDataRowById($rs[0]['podeliverykey']); 
        $_POST['placeOfDeliveryName'] = $rsPODelivery[0]['name'];
    }
    
    if (!empty($rs[0]['polkey'])){
        $rsPOL = $port->getDataRowById($rs[0]['polkey']); 
        $_POST['polName'] = $rsPOL[0]['name'];
    }
    if (!empty($rs[0]['agentkey'])){
        $rsAgent = $customer->getDataRowById($rs[0]['agentkey']); 
        $_POST['agentName'] = $rsAgent[0]['name'];
    }

    if (!empty($rs[0]['poreceiptkey'])){
        $rsPODelivery = $port->getDataRowById($rs[0]['poreceiptkey']); 
        $_POST['placeOfReceiptName'] = $rsPODelivery[0]['name'];
    }
    
    $_POST['chkIsOverwriteShipper']  =  $rs[0]['isoverwriteshipper'];
    $_POST['chkIsOverwriteConsignee']  =  $rs[0]['isoverwriteconsignee'];
    $_POST['chkIsOverwriteCarrier']  =  $rs[0]['isoverwritecarrier'];
    $_POST['chkIsOverwritePOD']  =  $rs[0]['isoverwritepod'];
    $_POST['chkIsOverwritePOL']  =  $rs[0]['isoverwritepol'];
    $_POST['chkIsOverwriteFinalDestination']  =  $rs[0]['isoverwritefinaldestination'];
    $_POST['placeOfReceipt']  =  $rs[0]['placeofreceipt'];
    $_POST['placeOfDelivery']  =  $rs[0]['placeofdelivery'];
    $_POST['portOfDischarge']  =  $rs[0]['portofdischarge'];
    $_POST['portOfLoading']  =  $rs[0]['portofloading'];
    $_POST['finalDestination'] = $rs[0]['finaldestination'];

    
	$_POST['note'] = $rs[0]['note'];
	$_POST['weight'] = $obj->formatNumber($rs[0]['weight'],2);
	$_POST['volume'] = $obj->formatNumber($rs[0]['volume'],3);
	$_POST['qty'] = $obj->formatNumber($rs[0]['qty']);
	$_POST['selUnit'] = $rs[0]['unitkey'];

    $_POST['sumGrossWeight'] = $obj->formatNumber($rs[0]['sumgrossweight'], 4);
    $_POST['sumNetWeight'] = $obj->formatNumber($rs[0]['sumnetweight'],4);
    $_POST['sumMeas'] = $obj->formatNumber($rs[0]['summeas'],4);
    $_POST['sumQty'] = $obj->formatNumber($rs[0]['sumqty']);
    $_POST['sumChargeWeight'] = $obj->formatNumber($rs[0]['sumchargeweight'], 4);
	$_POST['selSumUnit'] = $rs[0]['sumunitkey'];

    $_POST['vesselNumber'] = $rs[0]['vesselnumber'];
    $_POST['feederNumber'] = $rs[0]['feedernumber'];

    $_POST['hidFeederKey'] = $rs[0]['feederkey'];
    if (!empty($rs[0]['feederkey'])) {
        $rsFeeder = $vessel->getDataRowById($rs[0]['feederkey']);
        $_POST['feederName'] = $rsFeeder[0]['name'];
    }

    $_POST['hidVesselKey'] = $rs[0]['vesselkey'];
	if(!empty($rs[0]['vesselkey'])){
        $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
        $_POST['vesselName'] = $rsVessel[0]['name'];
    }

    $_POST['selShipmentTermKey'] = $rs[0]['shipmenttermkey'];
    $_POST['selShipmentTerm2Key'] = $rs[0]['shipmentterm2key'];
    $_POST['selFreightTermKey'] = $rs[0]['freighttermkey'];

    $_POST['hidFinalDestinationKey'] = $rs[0]['finaldestinationkey'];
    if(!empty($rs[0]['finaldestinationkey'])) {
        $rsCity = $city->searchData('','',true, ' and ' . $city->tableName.'.pkey = ('. $obj->oDbCon->paramString($rs[0]['finaldestinationkey']) .') ');
        $_POST['finalDestinationName'] = $rsCity[0]['name'].', '.$rsCity[0]['countryname'];
    }

    $_POST['prepaidAt'] = $rs[0]['prepaidat'];
    $_POST['payableAt'] = $rs[0]['payableat'];
    $_POST['byInformation'] = $rs[0]['byinformation'];
    $_POST['by2Information'] = $rs[0]['by2information'];
    $_POST['numberOfOriginal'] = $rs[0]['numberoforiginal'];

    $_POST['hidConnectingVesselKey'] = $rs[0]['connectingvesselkey'];
    $_POST['hidConnectingVessel2Key'] = $rs[0]['connectingvessel2key'];

    
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
    
    $_POST['chkIsShowContainerNumber'] = $rs[0]['isshowcontainernumber'];

    $_POST['etdPol'] = $obj->formatDBDate($rs[0]['etdpol'], 'd / m / Y', array('returnOnEmpty' => true, 'value' => ''));
    $_POST['etaPod'] = $obj->formatDBDate($rs[0]['etapod'], 'd / m / Y', array('returnOnEmpty' => true, 'value' => ''));


    $_POST['hidConnectingCountryKey'] = $rs[0]['connectingcountrykey'];
    $_POST['hidConnectingCountry2Key'] = $rs[0]['connectingcountry2key'];
    $_POST['hidConnectingCountry3Key'] = $rs[0]['connectingcountry3key'];
    
    if(!empty($rs[0]['connectingcountrykey'])) {
        $rsPort = $port->getDataRowById($rs[0]['connectingcountrykey']);
        $_POST['connectingCountryName'] = $rsPort[0]['name'];
    }
    
    if(!empty($rs[0]['connectingcountry2key'])) {
        $rsPort = $port->getDataRowById($rs[0]['connectingcountry2key']);
        $_POST['connectingCountry2Name'] = $rsPort[0]['name'];
    }

    if(!empty($rs[0]['connectingcountry3key'])) {
        $rsPort = $port->getDataRowById($rs[0]['connectingcountry3key']);
        $_POST['connectingCountry3Name'] = $rsPort[0]['name'];
    }

    $_POST['shipTo'] = $rs[0]['shipto'];
    $_POST['serviceContract'] = $rs[0]['servicecontract'];
    $_POST['selUnitOfMeas'] = $rs[0]['unitofmeaskey'];

    $_POST['transit1Date'] = $obj->formatDBDate($rs[0]['transit1date'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));
    $_POST['transit2Date'] = $obj->formatDBDate($rs[0]['transit2date'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));

    $_POST['alsoNotifyParty'] = $rs[0]['alsonotifyparty']; 

    if (!empty($rs[0]['shippinglinekey'])){
        $_POST['hidShippingLineKey'] = $rs[0]['shippinglinekey']; 
		$rsShippingLine = $supplier->getDataRowById($rs[0]['shippinglinekey']);
		$_POST['supplierName'] = $rsShippingLine[0]['name'];
	} 
    $rsContainer = $emklJobOrder->getDetailContainer($rs[0]['refheaderkey']);

    $arrTemp = array();
    for($i=0; $i<count($rsContainer); $i++) {
        $arrTemp['pkey'] = $rsContainer[$i]['pkey'];
        $arrTemp['containerno'] = $rsContainer[$i]['containerno'];

        array_push($arrDataContainerNumber, $arrTemp);
    }


    $_POST['chkIsOverwriteAgent']  =  $rs[0]['isoverwriteagent'];

    if (!empty($rs[0]['agentkey'])){
        $rsAgent = $customer->getDataRowById($rs[0]['agentkey']); 
        $_POST['agentName'] = $rsAgent[0]['name'];
        $_POST['agentAddress'] = $rsAgent[0]['address'];
    }

} 

$arrStatus = $obj->generateComboboxOpt(array('data' => $obj->getAllStatus(),'label' => 'status'));
$arrUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name');
$arrShipmentTerm = $obj->generateComboboxOpt(array('data' => $emklJobOrder->getShipmentTerm()));
$arrFreight = $class->convertForCombobox($emklJobOrder->getFreightTerm(), 'pkey', 'name');

$arrUnitOptMeas[0]['pkey'] = 0;
$arrUnitOptMeas[0]['name'] = 'CBM';
$arrUnitOptMeas[1]['pkey'] = 1;
$arrUnitOptMeas[1]['name'] = 'M3';

$arrUnitOfMeas = $obj->generateComboboxOpt(array('data' => $arrUnitOptMeas));

$arrContainerNumber = $obj->convertForCombobox($arrDataContainerNumber, 'pkey', 'containerno');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
<script type="text/javascript"> 

	
	
	jQuery(document).ready(function(){  
        var tabID = selectedTab.newPanel[0].id;
        
        var emklHouseBL = new EMKLHouseBL(tabID, <?php echo json_encode(
                                                                array(
                                                                    'containerDetail' => $rsContainerDetail,
                                                                ) 
                                                            ); ?>);
        prepareHandler(emklHouseBL); 
        
        var fieldValidation =  {
                                 code: { 
                                        validators: {
                                            notEmpty: {
                                                message: phpErrorMsg.code[1]
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputDate('trDate'); ?>  
                                        </div> 
                                    </div>
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['refCode']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $emklJobOrder,
                                                                                    'element' => array('value' => 'jobOrderCode',
                                                                                                       'key' => 'hidJobOrderKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-emkl-job-order.php',
                                                                                                        'data' => array(  'action' =>'searchDataForInvoice' )
                                                                                                    ),
                                                                                    'callbackFunction' => 'getTabObj().updateFromJobOrder()'

                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipper']); ?></label> 
                                        <div class="col-xs-9"> 
											<div class="flex">
											<div class="consume">
												<div  class="non-overwrite-shipper">
												 <?php    
													echo $obj->inputAutoComplete(array( 
																						'objRefer' => $customer,
																						'element' => array('value' => 'shipperName',
																										   'key' => 'hidShipperKey'),
																						'source' =>array(
																											'url' => 'ajax-customer.php',
																											'data' => array(  'action' =>'searchData' )
																										) ,
																						 'callbackFunction' => 'getTabObj().updateShipper()'

																					  )
																				);  
													?> 
												</div> 
												<div class="overwrite-shipper"><?php echo $obj->inputText('shipperName1'); ?></div>
											</div>
											
											<div style="padding-left: 0.5em">
												<div style="float:left; margin-top:0.1em"  rel="shipper"><?php echo $obj->inputCheckBox('chkIsOverwriteShipper'); ?></div>
												<div style="float:left; margin-left:0.5em"><?php echo ucwords($obj->lang['overwrite']); ?></div>
											</div>
											</div>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                       <label class="col-xs-3 control-label"></label> 
                                        <div class="col-xs-9"> 
                                            <div class="non-overwrite-shipper"><?php echo $obj->inputTextArea('shipperAddress', array('readonly' => true, 'etc' => 'style="height:10em;"')); ?></div> 
											<div class="overwrite-shipper"><?php echo $obj->inputTextArea('shipperAddress1', array('etc' => 'style="height:10em;"')); ?></div> 
                                        </div> 
                                     </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consignee']); ?></label> 
                                        <div class="col-xs-9"> 
										<div class="flex">
                                         <div class="consume"> 
											 <div class="non-overwrite-consignee">
											 <?php 
											 echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $consignee,
                                                                                    'element' => array('value' => 'consigneeName',
                                                                                                       'key' => 'hidConsigneeKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-consignee.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ),
                                                                                    'callbackFunction' => 'getTabObj().updateConsignee()'

                                                                                  )
                                                                            );  
                                                ?> 
											 </div>
											 <div class="overwrite-consignee"><?php echo $obj->inputText('consigneeName1'); ?></div>
										</div>
										<div style="padding-left: 0.5em">
											<div style="float:left; margin-top:0.1em"  rel="consignee"><?php echo $obj->inputCheckBox('chkIsOverwriteConsignee'); ?></div>
											<div style="float:left; margin-left:0.5em"><?php echo ucwords($obj->lang['overwrite']); ?></div>
										</div>
										</div>	
                                        </div> 
                                    </div> 
                                 	<div class="form-group ">
                                       <label class="col-xs-3 control-label"></label> 
                                        <div class="col-xs-9"> 
                                            <div>
												<div class="non-overwrite-consignee"><?php echo $obj->inputTextArea('consigneeAddress', array('readonly' => true, 'etc' => 'style="height:10em;"')); ?></div>
												<div class="overwrite-consignee"><?php echo $obj->inputTextArea('consigneeAddress1', array('etc' => 'style="height:10em;"')); ?></div>
											</div> 
                                        </div> 
                                    </div>    
                                    <!-- <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['notifyParty']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $consignee,
                                                                                    'element' => array('value' => 'carrierName',
                                                                                                       'key' => 'hidCarrierKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-consignee.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ) 
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div>  -->

                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['notifyParty']); ?></label>
                                        <div class="col-xs-9">
                                            <div class="flex">
                                                <div class="consume">
                                                    <div class="non-overwrite-carrier">
                                                        <?php
                                                        echo $obj->inputAutoComplete(
                                                            array(
                                                                'objRefer' => $consignee,
                                                                'element' => array(
                                                                    'value' => 'carrierName',
                                                                    'key' => 'hidCarrierKey'
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
                                                    <div class="overwrite-carrier"><?php echo $obj->inputText('carrierName1'); ?></div>
                                                </div>
                                                <div style="padding-left: 0.5em">
                                                    <div style="float:left; margin-top:0.1em" rel="carrier">
                                                        <?php echo $obj->inputCheckBox('chkIsOverwriteCarrier'); ?>
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
												<div class="non-overwrite-carrier"><?php echo $obj->inputTextArea('carrierAddress', array('readonly' => true, 'etc' => 'style="height:10em;"')); ?>
                                                </div>
                                                <div class="overwrite-carrier">
                                                    <?php echo $obj->inputTextArea('carrierAddress1', array('etc' => 'style="height:10em;"')); ?></div>
                                            </div>
                                        </div>
                                    </div>

                                
                                    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords('Also Notify Party'); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('shipTo', array('etc' => 'style="height:10em;"')); ?> 
                                        </div> 
                                    </div>
                                
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['feederVessel']); ?> / <?php echo ucwords($obj->lang['voyage']); ?></label>
                                            <div class="col-xs-9">
                                                <div class="flex">
                                                    <div class="consume">
                                                        <?php echo $obj->inputAutoComplete(
                                                            array(
                                                                'objRefer' => $vessel,
                                                                'element' => array(
                                                                    'value' => 'feederName',
                                                                    'key' => 'hidFeederKey'
                                                                ),
                                                                'source' => array(
                                                                    'url' => 'ajax-vessel.php',
                                                                    'data' => array('action' => 'searchData')
                                                                ),
                                                                'popupForm' => array(
                                                                    'url' => 'vesselForm.php',
                                                                    'element' => array(
                                                                        'value' => 'vesselName',
                                                                        'key' => 'hidVesselKey'
                                                                    ),
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['motherVessel']); ?> /
                                            <?php echo ucwords($obj->lang['voyage']); ?></label>
                                        <div class="col-xs-9">
                                            <div class="flex">
                                                <div class="consume">
                                                    <?php echo $obj->inputAutoComplete(
                                                        array(
                                                            'objRefer' => $vessel,
                                                            'element' => array(
                                                                'value' => 'vesselName',
                                                                'key' => 'hidVesselKey'
                                                            ),
                                                            'source' => array(
                                                                'url' => 'ajax-vessel.php',
                                                                'data' => array('action' => 'searchData')
                                                            ),
                                                            'popupForm' => array(
                                                                'url' => 'vesselForm.php',
                                                                'element' => array(
                                                                    'value' => 'vesselName',
                                                                    'key' => 'hidVesselKey'
                                                                ),
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
                                                    echo $obj->inputAutoComplete(
                                                        array(
                                                            'objRefer' => $vessel,
                                                            'revalidateField' => false,
                                                            'element' => array(
                                                                'value' => 'connectingVesselName',
                                                                'key' => 'hidConnectingVesselKey'
                                                            ),
                                                            'source' => array(
                                                                'url' => 'ajax-vessel.php',
                                                                'data' => array('action' => 'searchData')
                                                            ),
                                                            'popupForm' => array(
                                                                'url' => 'vesselForm.php',
                                                                'element' => array(
                                                                    'value' => 'vesselName',
                                                                    'key' => 'hidVesselKey'
                                                                ),
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
                                                    echo $obj->inputAutoComplete(
                                                        array(
                                                            'objRefer' => $vessel,
                                                            'revalidateField' => false,
                                                            'element' => array(
                                                                'value' => 'connectingVessel2Name',
                                                                'key' => 'hidConnectingVessel2Key'
                                                            ),
                                                            'source' => array(
                                                                'url' => 'ajax-vessel.php',
                                                                'data' => array('action' => 'searchData')
                                                            ),
                                                            'popupForm' => array(
                                                                'url' => 'vesselForm.php',
                                                                'element' => array(
                                                                    'value' => 'vesselName',
                                                                    'key' => 'hidVesselKey'
                                                                ),
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
                                                    echo $obj->inputAutoComplete(
                                                        array(
                                                            'objRefer' => $vessel,
                                                            'revalidateField' => false,
                                                            'element' => array(
                                                                'value' => 'connectingVessel3Name',
                                                                'key' => 'hidConnectingVessel3Key'
                                                            ),
                                                            'source' => array(
                                                                'url' => 'ajax-vessel.php',
                                                                'data' => array('action' => 'searchData')
                                                            ),
                                                            'popupForm' => array(
                                                                'url' => 'vesselForm.php',
                                                                'element' => array(
                                                                    'value' => 'vesselName',
                                                                    'key' => 'hidVesselKey'
                                                                ),
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
                                    </div> -->

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['firstConnectingAirport']); ?></label>
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'objRefer' => $port,
                                                        'revalidateField' => false,
                                                        'element' => array(
                                                            'value' => 'connectingCountryName',
                                                            'key' => 'hidConnectingCountryKey'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-port.php',
                                                            'data' => array('action' => 'searchData')
                                                        ),
                                                        'popupForm' => array(
                                                            'url' => 'portForm.php',
                                                            'element' => array(
                                                                'value' => 'portName',
                                                                'key' => 'hidPortKey'
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
                                </div>


                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['secondConnectingAirport']); ?></label>
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'objRefer' => $port,
                                                        'revalidateField' => false,
                                                        'element' => array(
                                                            'value' => 'connectingCountry2Name',
                                                            'key' => 'hidConnectingCountry2Key'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-port.php',
                                                            'data' => array('action' => 'searchData')
                                                        ),
                                                        'popupForm' => array(
                                                            'url' => 'countryForm.php',
                                                            'element' => array(
                                                                'value' => 'portName',
                                                                'key' => 'hidPortKey'
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
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['firstTransitTime']); ?></label>
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputDate('transit1Date', array('etc' => 'style="text-align:center"', 'allowEmpty' => true)); ?>
                                    </div>
                                </div>


                                <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['thirdConnectingAirport']); ?></label>
                                    <div class="col-xs-9">
                                        <div class="flex">
                                            <div class="consume">
                                                <?php
                                                echo $obj->inputAutoComplete(
                                                    array(
                                                        'objRefer' => $port,
                                                        'revalidateField' => false,
                                                        'element' => array(
                                                            'value' => 'connectingCountry3Name',
                                                            'key' => 'hidConnectingCountry3Key'
                                                        ),
                                                        'source' => array(
                                                            'url' => 'ajax-port.php',
                                                            'data' => array('action' => 'searchData')
                                                        ),
                                                        'popupForm' => array(
                                                            'url' => 'portForm.php',
                                                            'element' => array(
                                                                'value' => 'portName',
                                                                'key' => 'hidPortKey'
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
                                </div>

                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['secondTransitTime']); ?></label>
                                    <div class="col-xs-9">
                                        <?php echo $obj->inputDate('transit2Date', array('etc' => 'style="text-align:center"', 'allowEmpty' => true)); ?>
                                    </div>
                                </div>

                                    <!-- <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['finalDestination'] ?></label> 
                                        <div class="col-xs-9">  
                                                <?php  echo $obj->inputAutoComplete(
                                                        array( 
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
                                    </div> -->

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['finalDestination']); ?></label> 
                                        <div class="col-xs-9"> 
											<div class="flex">
											<div class="consume">
												<div  class="non-overwrite-finalDestination">
												<?php  echo $obj->inputAutoComplete(
                                                        array( 
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
												<div class="overwrite-finalDestination"><?php echo $obj->inputText('finalDestination'); ?></div>
											</div>
											
											<div style="padding-left: 0.5em">
												<div style="float:left; margin-top:0.1em"  rel="finalDestination"><?php echo $obj->inputCheckBox('chkIsOverwriteFinalDestination'); ?></div>
												<div style="float:left; margin-left:0.5em"><?php echo ucwords($obj->lang['overwrite']); ?></div>
											</div>
											</div>
                                        </div> 
                                    </div> 

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['etd']); ?> /<?php echo ucwords($obj->lang['eta']); ?></label>
                                        <div class="col-xs-9">
                                            <div class="flex">
                                                <div class="consume">
                                                    <?php echo $obj->inputDate('etdPol', array('etc' => 'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                </div>
                                                <div>/</div>
                                                <div class="consume">
                                                    <?php echo $obj->inputDate('etaPod', array('etc' => 'style="text-align:center"', 'allowEmpty' => true)); ?>
                                                </div>
                                            </div>
                                    
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">POL / <?php echo ucwords($obj->lang['placeOfReceipt']); ?></label> 
                                        <div class="col-xs-9">
                                        <div class="flex">
                                                <div class="consume"> 
                                                    <div class="overwrite-POL"><?php echo $obj->inputText('portOfLoading'); ?></div>
                                                    <div class="non-overwrite-POL">
                                                            <?php  echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $port, 
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
                                                </div> 
                                                <div>/</div>
                                            <div class="consume"> 
                                                        <div class="overwrite-POD"><?php echo $obj->inputText('placeOfReceipt'); ?></div>
                                                        <div class="non-overwrite-POD">
                                                            <?php  echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $port,
                                                                                        'revalidateField' => false,  
                                                                                        'element' => array('value' => 'placeOfReceiptName',
                                                                                                        'key' => 'hidPOReceiptKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-port.php',
                                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        ),
                                                                                        'allowedStatusForEdit' => array (1),
                                                                                        'etc' => $attrHeader   
                                                                                    )
                                                                                );  
                                                            ?>   
                                                        </div> 
                                                    </div>  
                                                <div style="padding-left: 0.5em">
                                                    <div style="float:left; margin-top:0.1em" rel="POL">
                                                        <?php echo $obj->inputCheckBox('chkIsOverwritePOL'); ?>
                                                    </div>
                                                    <div style="float:left; margin-left:0.5em"><?php echo ucwords($obj->lang['overwrite']); ?></div>
                                                </div>
                                            </div> 
                                        </div> 
                                    </div>
 

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">POD / <?php echo ucwords($obj->lang['placeOfDelivery']); ?></label> 
                                        <div class="col-xs-9">
                                        <div class="flex">
                                            
                                                <div class="consume"> 
                                                    <div class="overwrite-POL"><?php echo $obj->inputText('portOfDischarge'); ?></div>
                                                    <div class="non-overwrite-POL">
                                                    <?php  echo $obj->inputAutoComplete(array(
                                                                                            'objRefer' => $port, 
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
                                                <div>/</div>
                                                <div class="consume"> 
                                                    <div class="overwrite-POD"><?php echo $obj->inputText('placeOfDelivery'); ?></div>
                                                    <div class="non-overwrite-POD">
                                                        <?php  echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $port,
                                                                                        'revalidateField' => false,  
                                                                                        'element' => array('value' => 'placeOfDeliveryName',
                                                                                                        'key' => 'hidPODeliveryKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-port.php',
                                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        ),
                                                                                        'allowedStatusForEdit' => array (1),
                                                                                        'etc' => $attrHeader   
                                                                                    )
                                                                                );  
                                                        ?>  
                                                    </div>   
                                                </div> 
                                                    
                                                <div style="padding-left: 0.5em">
                                                    <div style="float:left; margin-top:0.1em" rel="POD">
                                                        <?php echo $obj->inputCheckBox('chkIsOverwritePOD'); ?>
                                                    </div>
                                                    <div style="float:left; margin-left:0.5em"><?php echo ucwords($obj->lang['overwrite']); ?></div>
                                                </div>
                                                </div> 
                                        </div> 
                                    </div>


                                                                            

                         
                                    <!-- <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['agent']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php    
                                                echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $customer,
                                                                                    'element' => array('value' => 'agentName',
                                                                                                       'key' => 'hidAgentKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-customer.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ) 
                                                                                  )
                                                                            );  
                                                ?> 
                                        </div> 
                                    </div>  -->
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['agent']); ?></label> 
                                        <div class="col-xs-9"> 
											<div class="flex">
											<div class="consume">
												<div  class="non-overwrite-agent">
												 <?php    
													echo $obj->inputAutoComplete(array( 
																						'objRefer' => $customer,
																						'element' => array('value' => 'agentName',
																										   'key' => 'hidAgentKey'),
																						'source' =>array(
																											'url' => 'ajax-customer.php',
																											'data' => array(  'action' =>'searchData' )
																										) ,
																						 'callbackFunction' => 'getTabObj().updateAgent()'

																					  )
																				);  
													?> 
												</div> 
												<div class="overwrite-agent"><?php echo $obj->inputText('agentName1'); ?></div>
											</div>
											
											<div style="padding-left: 0.5em">
												<div style="float:left; margin-top:0.1em"  rel="agent"><?php echo $obj->inputCheckBox('chkIsOverwriteAgent'); ?></div>
												<div style="float:left; margin-left:0.5em"><?php echo ucwords($obj->lang['overwrite']); ?></div>
											</div>
											</div>
                                        </div> 
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"></label> 
                                        <div class="col-xs-9"> 
                                            <div class="non-overwrite-agent"><?php echo $obj->inputTextArea('agentAddress', array('readonly' => true, 'etc' => 'style="height:10em;"')); ?>
                                            </div>
                                            <div class="overwrite-agent"><?php echo $obj->inputTextArea('agentAddress1', array('etc' => 'style="height:10em;"')); ?></div> 
                                        </div> 
                                    </div> 
                                   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Shipping Line</label> 
                                        <div class="col-xs-9">  
                                               <?php  echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $supplier,
                                                                                    'revalidateField' => true, 
                                                                                    'element' => array('value' => 'shippingLineName',
                                                                                                       'key' => 'hidShippingLineKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-supplier.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ) , 
                                                                                    'popupForm' => array(
                                                                                                    'url' => 'supplierForm.php',
                                                                                                    'element' => array('value' => 'supplierName',
                                                                                                           'key' => 'hidSupplierKey'),
                                                                                                    'width' => '600px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['carrier'])
                                                                                                )
                                                                                    )
                                                                            );  
                                                ?>                                     
                                        </div> 
                                    </div>
                                   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['exportReference']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('exportReference'); ?>
                                        </div> 
                                     </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['merchant']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('merchant'); ?>
                                        </div> 
                                     </div> 

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipmentTerm']); ?></label>
                                        <div class="col-xs-9">
                                            <div class="flex">
                                                <div class="consume">
                                                    <?php echo $obj->inputSelect('selShipmentTermKey', $arrShipmentTerm); ?>
                                                </div>
                                                <div>-</div>
                                                <div class="consume">
                                                    <?php echo $obj->inputSelect('selShipmentTerm2Key', $arrShipmentTerm); ?>
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
                                        <label class="col-xs-3 control-label">Payment</label>
                                        <div class="col-xs-9">
                                                <?php echo $obj->inputSelect('selFreightTermKey', $arrFreight); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords('Prepaid at'); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('prepaidAt'); ?>
                                        </div> 
                                    </div>
                                    
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords('Payable at'); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('payableAt'); ?>
                                        </div> 
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords('By #1'); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('byInformation'); ?>
                                        </div> 
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords('By #2'); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('by2Information'); ?>
                                        </div> 
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['freightCharges']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('freightCharges'); ?>
                                        </div> 
                                     </div> 

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Number Of Original B/L</label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputNumber('numberOfOriginal'); ?>
                                        </div> 
                                     </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Unit of Meas</label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputSelect('selUnitOfMeas', $arrUnitOfMeas); ?>
                                        </div>
                                    </div>

                                    <!-- <div class="form-group">
                                        <label class="col-xs-3 control-label">Also Notify Party</label>
                                        <div class="col-xs-9">
                                            <?php echo $obj->inputText('alsoNotifyParty'); ?>
                                        </div>
                                    </div> -->
                                
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['telex']); ?></label> 
                                        <div class="col-xs-9  control-label"> 
											<div class="flex">
                                            	<div><?php echo $obj->inputCheckBox('chkIsRelease'); ?></div>
                                            	<div><?php echo $obj->lang['release']; ?></div>
                                                <div style="margin-left:2em"><?php echo $obj->inputDate('telexDate', array('etc'=>'style="text-align:center"')); ?></div>
                                                
											</div>
                                        </div> 
                                    </div>
                                
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['showContainerInformation']); ?></label> 
                                        <div class="col-xs-9  control-label"> 
											<div class="flex">
                                            	<div><?php echo $obj->inputCheckBox('chkIsShowContainerNumber'); ?></div>
											</div>
                                        </div> 
                                    </div>



                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('note', array('etc' => 'style="height:10em;"')); ?> 
                                        </div> 
                                    </div>
                           
                             </div>
                    </div>
                     
                    <div class="div-table-col">  
						<div class="div-tab-panel"> 
							<div class="div-table-caption border-green"><?php echo ucwords($obj->lang['itemPackage']. ' & '. $obj->lang['goodsDescription']); ?></div>
					
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mblawb']); ?></label> 
                                <div class="col-xs-9">     
                                    <?php echo $obj->inputText('mblNumber'); ?>
                                </div>  
                            </div> 
							<div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['itemPackage']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputText('package'); ?>
								</div> 
							 </div>
							
                                <!--
                            <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['weight']); ?> / <?php echo ucwords($obj->lang['volume']); ?></label> 
								<div class="col-xs-9">   
									<div class="flex">
                                        <div ><?php echo $obj->inputNumber('qty'); ?></div>
                                        <div style="width:30%" style="margin-right:10px"><?php echo $obj->inputSelect('selUnit', $arrUnit, array('add-class' => 'label-style')); ?></div>
										<div ><?php echo  $obj->inputDecimal('weight'); ?></div>
										<div class="text-muted" style="margin-right:10px">KG</div> 
										<div ><?php echo  $obj->inputDecimal('volume', array('etc'=>'mnv-attr-decimal="3"')); ?></div>
										<div class="text-muted" style="margin-right:10px">CBM</div> 
									</div>
								</div> 
							</div> 
                                -->

                            <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords('QTY'); ?> / <?php echo ucwords('CBM'); ?></label> 
								<div class="col-xs-9">   
									<div class="flex">
                                        <div ><?php echo $obj->inputNumber('sumQty'); ?></div>
                                        <div style="width:22%" style="margin-right:3x"><?php echo $obj->inputSelect('selSumUnit', $arrUnit, array('add-class' => 'label-style')); ?></div>
										<div ><?php echo  $obj->inputDecimal('sumMeas', array('etc'=>'mnv-attr-decimal="4"')); ?></div>
										<div class="text-muted" style="margin-right:3px">CBM</div> 
									</div>
								</div> 
							</div> 
                            <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords('GW'); ?> / <?php echo ucwords('NW'); ?> / <?php echo ucwords('CW'); ?></label> 
								<div class="col-xs-9">   
									<div class="flex">
                                       	<div ><?php echo $obj->inputDecimal('sumGrossWeight', array('etc'=>'mnv-attr-decimal="4"')); ?></div>
                                        <div class="text-muted" style="margin-right:3px">KG</div>
                                        <div><?php echo $obj->inputDecimal('sumNetWeight', array('etc'=>'mnv-attr-decimal="4"')); ?></div>
                                        <div class="text-muted" style="margin-right:3px">KG</div>
                                        <div><?php echo $obj->inputDecimal('sumChargeWeight', array('etc'=>'mnv-attr-decimal="4"')); ?></div>
                                        <div class="text-muted" style="margin-right:3px">KG</div>
									</div>
								</div> 
							</div> 

                            <!-- <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords('GW'); ?> / <?php echo ucwords('NW'); ?> / <?php echo ucwords('MEAS'); ?></label> 
								<div class="col-xs-9">   
									<div class="flex">
                                        <div ><?php echo $obj->inputNumber('sumQty'); ?></div>
                                        <div style="width:22%" style="margin-right:3x"><?php echo $obj->inputSelect('selSumUnit', $arrUnit, array('add-class' => 'label-style')); ?></div>
										<div ><?php echo  $obj->inputDecimal('sumGrossWeight'); ?></div>
										<div class="text-muted" style="margin-right:3px">KG</div> 
										<div ><?php echo  $obj->inputDecimal('sumNetWeight'); ?></div>
										<div class="text-muted" style="margin-right:3px">KG</div> 
										<div ><?php echo  $obj->inputDecimal('sumMeas', array('etc'=>'mnv-attr-decimal="4"')); ?></div>
										<div class="text-muted" style="margin-right:3px">CBM</div> 
									</div>
								</div> 
							</div>  -->

							 <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['marksAndNumber']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputTextArea('marksNumber',array('etc' => 'style="height:10em;"')); ?>
								</div> 
							 </div>
							 <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['goodsDescription']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputTextArea('shortDesc',array('etc' => 'style="height:10em;"')); ?>
								</div> 
							 </div>
							 <div class="form-group">
								<label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['attachment']); ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputTextArea('trDesc',array('etc' => 'style="height:10em;"')); ?>
								</div> 
							 </div>
                             <div class="form-group ">
								<label class="col-xs-3 control-label"><?php echo  $obj->lang['containerInformationInWords']; ?></label> 
								<div class="col-xs-9"> 
									<?php echo $obj->inputTextArea('sayTotalContainer',array('etc' => 'style="height:10em;"')); ?>
								</div> 
							</div>


                            <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['container'] . ' / '. $obj->lang['seal']); ?></label> 
                                    <div class="col-xs-9">
                                        <div class="div-table mnv-commodity transaction-detail" style="width:100%">
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
                                                    $isLocked = false; 
                                                } else{ 
                                                    $_POST['hidDetailContainerKey[]'] =  $rsContainerDetail[$j]['pkey'];
                                                    $_POST['selContainerNo[]'] =  $rsContainerDetail[$j]['refcontainerkey'];
                                                    $_POST['hidJobOrderDetailKey[]'] =  $rsContainerDetail[$j]['refjoborderkey'];
                                                    $_POST['hidContainerNo[]'] =  $rsContainerDetail[$j]['containerno'];
                                                    $_POST['sealNo[]'] =  $rsContainerDetail[$j]['sealno'];
                                                }
                                                $hideDeleteIcon = '';  
                                            ?>
                                            <div class="div-table-row <?php echo $class; ?>  odd-style-adjustment" > 
                                                <div class="div-table-col"  style="padding-left:0"> 
													<div class="flex" style="width:100%">     
														<div style="width:100%;">
														    <?php echo $obj->inputHidden('hidDetailContainerKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputHidden('hidJobOrderDetailKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputHidden('hidContainerNo[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
															<?php echo $obj->inputSelect('selContainerNo[]', $arrContainerNumber, array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>
														</div>
													    <div style="width:100%;">
													    	<?php echo $obj->inputText('sealNo[]', array('overwritePost' => $overwrite, 'readonly' => true, 'disabled' => $disabled )); ?>
													    </div>
													    <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="container-row-template"')); ?></div>
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
                </div>
            </div>  
      
        <div class="form-button-panel" > 
       	 <?php  echo $obj->generateSaveButton(array(),true); ?> 
        </div> 
        
    </form>   
   
     <?php  echo $obj->showDataHistory(); ?>
</div> 
</body>

</html>
