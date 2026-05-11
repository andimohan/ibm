<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass('EMKLReminderJobOrder.class.php');
$emklReminderJobOrder = createObjAndAddToCol(new EMKLReminderJobOrder(EMKL['jobType']['export']));
$container = createObjAndAddToCol(new Container());
$port = createObjAndAddToCol(new Port());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());
$vessel = createObjAndAddToCol(new Vessel());
$terminal = createObjAndAddToCol(new Terminal());
$supplier = createObjAndAddToCol(new Supplier());
$currency = createObjAndAddToCol(new Currency());
$service = createObjAndAddToCol(new Service(SERVICE)); 
$consignee = createObjAndAddToCol(new Consignee());
$depot = createObjAndAddToCol(new Depot());
$location = createObjAndAddToCol(new Location());

$obj = $emklReminderJobOrder; 
 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
 
$formAction = 'emklReminderJobOrderList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','textcolor');   
$rs = prepareOnLoadData($obj); 
$rsVolumeDetail = array();
$rsContainerDetail = array();
$rsDetail = array();

$_POST['trDate'] = date('d / m / Y'); 

$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';

$arrInfo = array('isdocdate','istransferdate','isprofitlossdate','isvoucherdate','ispaymentcarrierdate','isamsdate','isisfdate',
				 'isemanifestdate','isehbldate','istrizdate','ismbltype','ishbltype'
				);  


$disabledDate = array();
$forceReadonly = array();
$forceReadonlyDate = array();

foreach($arrInfo as $row){
	$disabledDate[$row] = true;
	$forceReadonly[$row] = true;
	$forceReadonlyDate[$row] = 'force-readonly';
}
	
$disabledTelex = true;
$forceReadonlyTelex = true;
$forceReadonlyDateTelex = 'force-readonly';

$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');    

$dateReturnOnEmpty = array('returnOnEmpty'=>true, 'value' => '');

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
    $rsDetail = $obj->getDetailWithRelatedInformation($id);  
    
    $rsVolumeDetail = $obj->getDetailVolume($id); 
    $rsContainerDetail = $obj->getDetailContainer($id);
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y ');  
    $_POST['hidCargoType'] = $rs[0]['containertypekey'];

    
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
    
    if (!empty($rs[0]['agentkey'])){
        $_POST['hidAgentKey'] = $rs[0]['agentkey']; 
		$rsAgent = $supplier->getDataRowById($rs[0]['agentkey']);
		$_POST['agentName'] = $rsAgent[0]['name'];
	}
    if (!empty($rs[0]['carrierkey'])){
        $_POST['hidCarrierKey'] = $rs[0]['carrierkey']; 
		$rsCarrier = $supplier->getDataRowById($rs[0]['carrierkey']);
		$_POST['carrierName'] = $rsCarrier[0]['name'];
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

     if (!empty($rs[0]['porkey'])){
        $rsLocation = $location->getDataRowById($rs[0]['porkey']);
        $_POST['hidPORKey'] = $rs[0]['porkey'];
        $_POST['porName'] = $rsLocation[0]['name'];
    }
    
    if (!empty($rs[0]['poikey'])){
        $rsLocation = $location->getDataRowById($rs[0]['poikey']);
        $_POST['hidPOIKey'] = $rs[0]['poikey'];
        $_POST['poiName'] = $rsLocation[0]['name'];
    }
        
    $_POST['hidVesselKey'] = $rs[0]['vesselkey'];
    
	if(!empty($rs[0]['vesselkey'])){
        $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
        $_POST['vesselName'] = $rsVessel[0]['name'];
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
    $_POST['hblNumber'] = $rs[0]['hblnumber'];
    $_POST['etdPol'] = $obj->formatDBDate($rs[0]['etdpol'],'d / m / Y', $dateReturnOnEmpty);
    $_POST['etaPod'] = $obj->formatDBDate($rs[0]['etapod'],'d / m / Y', $dateReturnOnEmpty);
	$_POST['vesselNumber'] = $rs[0]['vesselnumber'];

    
	$_POST['trDesc'] = $rs[0]['trdesc']; 
	$_POST['itemDescription'] = $rs[0]['itemdescription']; 

    //FORM OTHERS
    $_POST['consigneeName'] = $rs[0]['consigneename'];  
    $_POST['transferDate'] = $obj->formatDBDate($rs[0]['transferdate'],'d / m / Y', $dateReturnOnEmpty);  
    $_POST['profitLossDate'] = $obj->formatDBDate($rs[0]['profitlossdate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['voucherDate'] = $obj->formatDBDate($rs[0]['voucherdate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['paymentCarrierDate'] = $obj->formatDBDate($rs[0]['paymentcarrierdate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['docDate'] = $obj->formatDBDate($rs[0]['docdate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['amsDate'] = $obj->formatDBDate($rs[0]['amsdate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['isfDate'] = $obj->formatDBDate($rs[0]['isfdate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['emanifestDate'] = $obj->formatDBDate($rs[0]['emanifestdate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['trizDate'] = $obj->formatDBDate($rs[0]['trizdate'],'d / m / Y', $dateReturnOnEmpty); 
    $_POST['telexDate'] = $obj->formatDBDate($rs[0]['telexdate'],'d  / m / Y', $dateReturnOnEmpty); 
    $_POST['ehblDate'] = $obj->formatDBDate($rs[0]['ehbldate'],'d  / m / Y', $dateReturnOnEmpty); 
    $_POST['mblDate'] = $obj->formatDBDate($rs[0]['mbldate'],'d  / m / Y', $dateReturnOnEmpty); 
    $_POST['hblDate'] = $obj->formatDBDate($rs[0]['hbldate'],'d  / m / Y', $dateReturnOnEmpty); 

    
    $_POST['chkIsDocDate'] = $rs[0]['isdocdate'];
    $_POST['chkIsTransferDate'] = $rs[0]['istransferdate'];
    $_POST['chkIsPLDate'] = $rs[0]['isprofitlossdate'];
    $_POST['chkIsVoucherDate'] = $rs[0]['isvoucherdate'];
    $_POST['chkIsPaymentCarrierDate'] = $rs[0]['ispaymentcarrierdate'];
    $_POST['chkIsAMSDate'] = $rs[0]['isamsdate'];
    $_POST['chkIsISFDate'] = $rs[0]['isisfdate'];
    $_POST['chkIsEmanifestDate'] = $rs[0]['isemanifestdate'];
    $_POST['chkIsEHBLDate'] = $rs[0]['isehbldate'];
    $_POST['chkIsTrizDate'] = $rs[0]['istrizdate'];
    
    $_POST['selTelexType'] = $rs[0]['telextype'];
	$_POST['chkIsMBLType'] = $rs[0]['ismbltype'];
    
    $_POST['chkIsHBLType'] = $rs[0]['ishbltype'];
    $_POST['selMBLType'] = $rs[0]['mbltypekey'];
    $_POST['selHBLType'] = $rs[0]['hbltypekey'];
    $_POST['selServiceType'] = $rs[0]['servicetypekey'];

	foreach($arrInfo as $row){
		
		// jgn dibalik, karena diatas init defaultnya false
		if($rs[0][$row] == 1){
			$disabledDate[$row] = false;
			$forceReadonly[$row] = false;
			$forceReadonlyDate[$row] = '';
		}
	}
	
	if($rs[0]['telextype'] == 2){
		$disabledTelex = false;
		$forceReadonlyTelex = false;
		$forceReadonlyDateTelex = '';
	}
	


}

$rsCurrency = $currency->searchData('','',true,' and ('.$currency->tableName.'.statuskey = 1'.$editCurrencyInactiveCriteria.')');
$arrCurrencyName = array_column($rsCurrency,null,'pkey');

$arrMBLType = array();
$arrMBLType[0]='-------';
$arrMBLType[1]='ORI';
$arrMBLType[2]='WB';
$arrMBLType[3]='EXP ';

$arrHBLType = array();
$arrHBLType[0]='-------';
$arrHBLType[1]='ORI';
$arrHBLType[2]='TLX';

$arrServiceType = array();
$arrServiceType[0]='-------';
$arrServiceType[1]='CY-CY';
$arrServiceType[2]='CY-DOOR';
$arrServiceType[3]='CFS-CFS';
$arrServiceType[4]='CFS-CY';

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
$arrContainerVolume = $class->convertForCombobox($rsContainer,'pkey','name');

$rsContainer = array_column($rsContainer,'name','pkey');

$rsService = $service->searchData();
$rsService = array_column($rsService,'name','pkey');
 
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
        
        
         var emklReminderJobOrder = new EMKLReminderJobOrder(tabID,<?php echo json_encode(
                                                                array(
                                                                    'rs' => $rs,
                                                                    'detail' => $rsDetail,
                                                                    'volumeDetail' => $rsVolumeDetail,
                                                                    'containerNumberDetail' => $rsContainerDetail
                                                                ) 
                                                            ); ?> ,varConstant); 
         prepareHandler(emklReminderJobOrder);   
         
        
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
 
       
}); 

</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div> 
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
        <?php prepareOnLoadDataForm($obj); ?>   
       <div class="div-table main-tab-table-2 header-panel">
                <div class="div-table-row">
                    <div class="div-table-col"> 
      						 <div class="div-tab-panel"> 
                                   <div class="div-table-caption border-orange">
                                       <div><?php echo ucwords($obj->lang['generalInformation']); ?></div>

                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['status']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['code']); ?></label> 
                                        <div class="col-xs-9"><?php echo $obj->inputAutoCode('code', array('allowedStatusForEdit' => array (1))); ?></div>
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse ); ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputDate('trDate'); ?> 
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
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ),  
                                                                                    'allowedStatusForEdit' => array (1),
                                                                                  ) 
                                                                                );  
                                            ?> 
                                        </div> 
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consignee']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('consigneeName'); ?>
                                        </div> 
                                    </div> 

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['typeOfJob']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                            <div class="consume"><?php echo  $obj->inputSelect('selAirSea', $arrTransportType); ?></div>
                                            <div style="width:100px"><?php echo  $obj->inputSelect('selContainerType', $arrContainer, array('allowedStatusForEdit' => array (1))); ?></div>
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
                                                                                                )
                                                                                              ); 
                                                    ?>  
                                              </div> 
                                              <div> <?php echo $obj->inputSelect('hidCargoType', $arrCargoType); ?> </div>
                                            </div>
                                        </div>  
                                    </div>    
								 

                                 <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['service']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo  $obj->inputSelect('selServiceType', $arrServiceType); ?>
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
                                                    $isLocked = false;
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
                                    <div class="form-group" style="margin-top:3em">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['carrier']); ?> / <?php echo ucwords($obj->lang['agent']); ?></label> 
                                    <div class="col-xs-9 flex">  
										<div class="consume">
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
										<div>/</div>
										<div class="consume">
										 <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $supplier,
                                                                                'revalidateField' => false, 
                                                                                'element' => array('value' => 'agentName',
                                                                                                   'key' => 'hidAgentKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-supplier.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) , 
                                                    
                                                                                'popupForm' => array(
                                                                                                    'url' => 'supplierForm.php',
                                                                                                    'element' => array('value' => 'agentName',
                                                                                                           'key' => 'hidAgentKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['agent'])
                                                                                                ) 
                                                                              )
                                                                        );  
                                            ?>  
										</div>
                                       
                                    </div> 
                            </div> 
                            
                                <div class="form-group">
                                   
                                   <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['vessel']); ?> / <?php echo ucwords($obj->lang['voyage']); ?></label>
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
                                    <label class="col-xs-3 control-label">Place Of Reciept / Issued</label> 
                                        <div class="col-xs-9" >  
                                            <div class="flex">
                                                <div class="consume">
                                                    <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $location,  
                                                                                'element' => array('value' => 'porName',
                                                                                                   'key' => 'hidPORKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-location.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ), 
                                                                                'popupForm' => array(
                                                                                                'url' => 'locationForm.php',
                                                                                                'element' => array('value' => 'porName',
                                                                                                       'key' => 'hidPORKey'),
                                                                                                'width' => '600px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['location'])
                                                                                            )
                                                                                )
                                                                        );  
                                                    ?>                     
                                                </div>
											<div>/</div>
											<div class="consume">
												<?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $location,  
                                                                                'element' => array('value' => 'poiName',
                                                                                                   'key' => 'hidPOIKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-location.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ), 
                                                                                'popupForm' => array(
                                                                                                'url' => 'locationForm.php',
                                                                                                'element' => array('value' => 'poiName',
                                                                                                       'key' => 'hidPOIKey'),
                                                                                                'width' => '600px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['location'])
                                                                                            )
                                                                                )
                                                                        );  
                                                    ?>             
											</div>
                                            </div> 
                                         </div> 
                                    </div>
                             
                               
								 	<div class="form-group" style="margin-top:3em">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['goodsDescription']); ?></label> 
                                        <div class="col-xs-9"><?php echo $obj->inputText('itemDescription'); ?></div>  
                                    </div>
                                   
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
                                                    $isLocked = false; 
                                                } else{ 
                                                    $_POST['hidDetailContainerKey[]'] =  $rsContainerDetail[$j]['pkey'];
                                                    $_POST['containerNo[]'] =  $rsContainerDetail[$j]['containerno'];
                                                    $_POST['sealNo[]'] = $rsContainerDetail[$j]['sealno'];
                                                
                                                }
                                                $hideDeleteIcon = '';  
                                            ?>
                                            <div class="div-table-row <?php echo $class; ?>  odd-style-adjustment" > 
                                                <div class="div-table-col"> 
                                                    <div class="flex">     
                                                        <div class="consume">
                                                            <?php echo $obj->inputHidden('hidDetailContainerKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputText('containerNo[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>
                                                        </div>
                                                        <div>/</div>
                                                        <div class="consume">
                                                            <?php echo $obj->inputText('sealNo[]', array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled )); ?>
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
                            <div class="div-tab-panel">
                                <div class="div-table-caption border-green"> <div><?php echo ucwords($obj->lang['description']); ?></div></div> 
                                <div class="div-table transaction-detail" style="width:100%">
                                 <div class="div-table-row" > 
                                        <div class="div-table-col" style="width:40px;"></div> 
                                        <div class="div-table-col detail-col-header" style="width:130px; border:0;"><?php echo ucwords($obj->lang['services']); ?> </div> 
                                        <div class="div-table-col detail-col-header" style=" border:0;"><?php echo ucwords($obj->lang['date']); ?> </div> 
                                        <div class="div-table-col detail-col-header" style="width:130px; border:0;"><?php echo ucwords($obj->lang['description']); ?> </div> 
                                    </div>  
                           
                                        <?php 
                                            $totalRows = count($rsDetail);
                                            for ($j=0;$j<=$totalRows; $j++){ 
                                                
                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $readonly = false;
                                                $disabled = false;  

                                                if ($j == $totalRows ){
                                                    $class = 'detail-row-template ';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                    $isLocked = false; 
                                                } else{ 
                                                    $_POST['hidDetailKey[]'] =  $rsDetail[$j]['pkey'];
                                                    $_POST['hidServiceDetailKey[]'] =  $rsDetail[$j]['itemkey'];
                                                    $_POST['serviceDetailName[]'] =  $rsDetail[$j]['servicename'];
                                                    $_POST['dateDetail[]'] =  $obj->formatDBDate($rsDetail[$j]['trdate'], 'd / m / Y');
                                                    $_POST['descDetail[]'] = $rsDetail[$j]['trdesc'];
    
                                                }
                                                $hideDeleteIcon = '';  
                                            ?>
                                            <div class="div-table-row <?php echo $class; ?>  odd-style-adjustment" > 
                                                <div class="div-table-col detail-col-detail" style="width:40px; border:0;text-align:center;"> 
                                                        <?php echo $obj->inputCheckBox('chkIsService[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>

                                                </div> 
                                                <div class="div-table-col detail-col-detail" style="width:130px; border:0;"> 
                                                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                        <?php echo $obj->inputHidden('hidServiceDetailKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                        <?php echo $obj->inputText('serviceDetailName[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>

                                                </div> 
                                                <div class="div-table-col detail-col-detail" style=" border:0;"> 
                                                        <?php echo $obj->inputDate('dateDetail[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>
                                                </div> 
                                                <div class="div-table-col detail-col-detail" style="width:160px; border:0;"> 
                                                        <?php echo $obj->inputText('descDetail[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>
                                                </div> 
                                                <div class="div-table-col <?php echo $obj->hideOnDisabled(); ?> icon-col">
                                                    <?php echo $obj->inputLinkButton('btnDeleteRows', '<i class="fas fa-times"></i>',array('etc' => 'tabIndex="-1"', 'class' =>'btn btn-link remove-button' )); ?>
                                                </div>
                                            </div> 
                                        <?php }	 ?>  
                                                                        </div>   

                                    <div style="clear:both;height:1em;"></div>
                                    <div style=" display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>


                            </div>

       
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['documentInformation']); ?></div>
							
                             <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['mbl']); ?></label> 
                                    <div class="col-xs-9">  
                                            <div class="flex">
                                                <div style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsMBLType', array('add-class' => 'chkDisabled')); ?></div>
                                                <div><?php echo  $obj->inputSelect('selMBLType', $arrMBLType,array('disabled'=> $disabledDate['ismbltype'],'add-class' => 'select-object')); ?></div>
                                                <div  style="width:120px;"><?php echo  $obj->inputDate('mblDate',array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?></div>
												<div class="consume"> <?php echo $obj->inputText('mblNumber'); ?></div>
                                            </div>
                                    </div>  
                                </div>   
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['hbl']); ?></label> 
                                    <div class="col-xs-9">  
                                            <div class="flex">
                                                <div  style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsHBLType', array('add-class' => 'chkDisabled')); ?></div>
                                                <div><?php echo  $obj->inputSelect('selHBLType', $arrHBLType,array('disabled'=> $disabledDate['ishbltype'],'add-class' => 'select-object')); ?></div>
                                                <div  style="width:120px;"><?php echo $obj->inputDate('hblDate',array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?></div>  
												<div class="consume"><?php echo $obj->inputText('hblNumber'); ?></div>
                                            </div>
                                    </div>  
                                </div>  
                                
                                <div class="form-group">
                                        <label class="col-xs-3 control-label">Option</label> 
                                        <div class="col-xs-9">  
                                        <div class="flex">
                                            <div style="width:100px"><?php echo  $obj->inputSelect('selTelexType', $arrHBLType, array('add-class' => 'selectOption')); ?></div>
                                            <div  class="consume"><?php echo $obj->inputDate('telexDate', array('etc'=>'style="text-align:center"', 'allowEmpty' => true, 'disabled'=> $disabledTelex,'add-class' => 'dateDisabled '.$forceReadonlyDateTelex)); ?></div>   
                                        </div>
                                        </div> 
                                </div> 
                                  <div class="form-group">
                                        <label class="col-xs-3 control-label">Propose Date</label> 
                                        <div class="col-xs-9">  
                                           <div class="flex">
                                                <div  style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsPaymentCarrierDate', array('add-class' => 'chkDisabled')); ?></div>
                                                <div ><?php echo $obj->inputDate('paymentCarrierDate', array('etc'=>'style="text-align:center"', 'disabled'=> $disabledDate['ispaymentcarrierdate'],'add-class' => 'dateDisabled '.$forceReadonlyDate['ispaymentcarrierdate'])); ?></div>   
                                            </div>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Voucher Date</label> 
                                        <div class="col-xs-9">  
                                            <div class="flex">
                                                <div  style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsVoucherDate', array('add-class' => 'chkDisabled')); ?></div>
                                                <div><?php echo $obj->inputDate('voucherDate',array('etc'=>'style="text-align:center"', 'disabled'=> $disabledDate['isvoucherdate'],'add-class' => 'dateDisabled '.$forceReadonlyDate['isvoucherdate'])); ?></div>   
                                            </div>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">Transfer FTP</label> 
                                        <div class="col-xs-9">  
                                            <div class="flex">
                                                <div  style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsTransferDate', array('add-class' => 'chkDisabled')); ?></div>
                                                <div><?php echo $obj->inputDate('transferDate', array('etc'=>'style="text-align:center"', 'disabled'=> $disabledDate['istransferdate'],'add-class' => 'dateDisabled '.$forceReadonlyDate['istransferdate'])); ?></div>   
                                            </div>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">PA Date</label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div  style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsDocDate', array('add-class' => 'chkDisabled')); ?></div>
                                                <div><?php echo $obj->inputDate('docDate', array('etc'=>'style="text-align:center"',  'disabled'=> $disabledDate['isdocdate'],'add-class' => 'dateDisabled '.$forceReadonlyDate['isdocdate'])); ?></div>   
                                            </div>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">PL Date</label> 
                                        <div class="col-xs-9">  
                                            <div class="flex">
                                                <div  style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsPLDate', array('add-class' => 'chkDisabled')); ?></div>
                                                <div><?php echo $obj->inputDate('profitLossDate', array('etc'=>'style="text-align:center"',  'disabled'=> $disabledDate['isprofitlossdate'],'add-class' => 'dateDisabled '.$forceReadonlyDate['isprofitlossdate'])); ?></div>   
                                            </div>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">AMS</label> 
                                        <div class="col-xs-9">
                                            <div class="flex">
                                                <div  style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsAMSDate', array('add-class' => 'chkDisabled')); ?></div>
                                                <div><?php echo $obj->inputDate('amsDate',array('etc'=>'style="text-align:center"','disabled'=> $disabledDate['isamsdate'],'add-class' => 'dateDisabled '.$forceReadonlyDate['isamsdate'])); ?></div>   
                                            </div>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">ISF</label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div  style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsISFDate', array('add-class' => 'chkDisabled')); ?></div>
                                                <div><?php echo $obj->inputDate('isfDate',array('etc'=>'style="text-align:center"',  'disabled'=> $disabledDate['isisfdate'],'add-class' => 'dateDisabled '.$forceReadonlyDate['isisfdate'])); ?></div>   
                                            </div>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">eManifest</label> 
                                        <div class="col-xs-9">  
                                            <div class="flex">
                                                <div  style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsEmanifestDate', array('add-class' => 'chkDisabled')); ?></div>
                                                <div><?php echo $obj->inputDate('emanifestDate',array('etc'=>'style="text-align:center"',  'disabled'=> $disabledDate['isemanifestdate'],'add-class' => 'dateDisabled '. $forceReadonlyDate['isemanifestdate'])); ?></div>   
                                            </div>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">3Z</label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div  style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsTrizDate', array('add-class' => 'chkDisabled')); ?></div>
                                                <div><?php echo $obj->inputDate('trizDate',array('etc'=>'style="text-align:center"',  'disabled'=> $disabledDate['istrizdate'],'add-class' => 'dateDisabled '. $forceReadonlyDate['istrizdate'])); ?></div>   
                                            </div>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label">eHBL</label> 
                                        <div class="col-xs-9">  
                                            <div class="flex">
                                                <div  style="width:40px;text-align:center"><?php echo  $obj->inputCheckBox('chkIsEHBLDate', array('add-class' => 'chkDisabled')); ?></div>
                                                <div><?php echo $obj->inputDate('ehblDate',array('etc'=>'style="text-align:center"', 'disabled'=> $disabledDate['isehbldate'],'add-class' => 'dateDisabled '. $forceReadonlyDate['isehbldate'])); ?></div>   
                                            </div>
                                        </div> 
                                    </div> 
                        
                        </div>   
                         
                    </div>
           </div>
      </div>  
    

        
    <div style="clear:both; height:1em;"></div> 
        
     
       
        <div class="form-button-margin"></div>
        <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>  
   
     <?php echo $obj->showDataHistory(); ?>
    
</div> 
</body>

</html>
