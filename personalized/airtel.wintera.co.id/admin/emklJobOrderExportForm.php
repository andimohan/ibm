<?php 
require_once '../../../_config.php'; 
require_once '../../../_include-v2.php'; 

includeClass('EMKLJobOrder.class.php');
$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['export']));
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

$obj = $emklJobOrderExport; 
 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
 
$formAction = 'emklJobOrderExportList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;

$rsStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','textcolor');   
$rs = prepareOnLoadData($obj); 
$rsContactPerson = array(); 
$rsSalesDetail = array();
$rsItemDetail = array();
$rsVolumeDetail = array();
$rsCommisionDetail = array(); 
$rsInvoiced = array();  

$_POST['trDate'] = date('d / m / Y'); 
$_POST['closingDate'] = date('d / m / Y 00:00'); 
$_POST['etdPol'] = date('d / m / Y'); 
$_POST['etaPod'] = date('d / m / Y');  
$_POST['selTypeOfJob'] = EMKL['jobType']['export'];
$_POST['chkIsMaster'] = 1;
$editWarehouseInactiveCriteria = '';
$editCurrencyInactiveCriteria = '';
    
$arrInvoicedKey = array();
$arrType = array(); 
 
$isMaster = '';
$readInvoiced = false;

$useJOHeader =  true;
$attrHeader = ($useJOHeader) ? 'attr-header="true"' : '';

$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');    

$rsInvoiceCol = array();

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
    $rsSalesDetail = $obj->getDetailWithRelatedInformation($id);  
    $rsInvoiced = $obj->getInvoiceInformation($id);  

    $rsVolumeDetail = $obj->getDetailVolume($id);  
    if(!empty($rsInvoiced)){
        $arrInvoicedKey = array_column($rsInvoiced,'refdetailkey');
        $readInvoiced = true;
    }
    
    
    // cek invoice setiap sales order, buat jadi array
    // utk header cek setiap array ad nilai atau tdk
    // utk setiap detail sales, jg sama
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y '); 
    
    $_POST['hidCargoType'] = $rs[0]['containertypekey'];
 
    if (!empty($rs[0]['agentkey'])){
        $_POST['hidAgentKey'] = $rs[0]['agentkey']; 
		$rsAgent = $supplier->getDataRowById($rs[0]['agentkey']);
		$_POST['agentName'] = $rsAgent[0]['name'];
	}
    
    if (!empty($rs[0]['consigneekey'])){
        $_POST['hidConsigneeKey'] = $rs[0]['consigneekey']; 
		$rsConsignee = $consignee->getDataRowById($rs[0]['consigneekey']);
		$_POST['consigneeName'] = $rsConsignee[0]['name'];
	}
        
    if (!empty($rs[0]['saleskey'])){
        $_POST['hidSalesKey'] = $rs[0]['saleskey']; 
		$rsSales = $employee->getDataRowById($rs[0]['saleskey']);
		$_POST['salesName'] = $rsSales[0]['name'];
        
	}if (!empty($rs[0]['carrierkey'])){
        $_POST['hidCarrierKey'] = $rs[0]['carrierkey']; 
		$rsCarrier = $supplier->getDataRowById($rs[0]['carrierkey']);
		$_POST['carrierName'] = $rsCarrier[0]['name'];
	} 
    
    if (!empty($rs[0]['itemkey'])){
        $rsItem = $container->getDataRowById($rs[0]['itemkey']);
        $_POST['hidContainerKey'] = $rs[0]['itemkey'];
        $_POST['containerName'] = $rsItem[0]['name'];
    }
    $_POST['hidContainerVolumeKey'] = $rs[0]['itemkey'];
    
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
       
//    if (!empty($rs[0]['locationkey'])){
//        $rsLocation = $location->getDataRowById($rs[0]['locationkey']);
//        $_POST['hidLocationKey'] = $rs[0]['locationkey'];
//        $_POST['locationName'] = $rsLocation[0]['name'];
//    }
        
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
    $_POST['selWarehouseKey'] = $rs[0]['warehousekey']; 
	$editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);
  
    
	$_POST['poNumber'] = $rs[0]['ponumber'];
	//$_POST['selTypeOfJob'] = $rs[0]['jobtypekey'];
	$_POST['selAirSea'] = $rs[0]['transportationtypekey'];
	$_POST['selContainerType'] = $rs[0]['loadcontainertypekey'];
    
    $_POST['volume'] = $obj->formatNumber($rs[0]['volume'],2); 
    $_POST['weight'] = $obj->formatNumber($rs[0]['weight'],2); 
    $_POST['selVolumeType'] = $rs[0]['volumetype'];
    $_POST['mblNumber'] = $rs[0]['mblnumber'];
    $_POST['hblNumber'] = $rs[0]['hblnumber']; 
    $_POST['etdPol'] = $obj->formatDBDate($rs[0]['etdpol'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));
    $_POST['etaPod'] = $obj->formatDBDate($rs[0]['etapod'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));
    $_POST['closingDate'] = $obj->formatDBDate($rs[0]['closingdate'],'d / m / Y H:i', array('returnOnEmpty'=>true, 'value' => ''));
	$_POST['vesselNumber'] = $rs[0]['vesselnumber'];
	$_POST['containerNumber'] = $rs[0]['containernumber'];
	$_POST['aju'] = $rs[0]['aju']; 
	$_POST['peb'] = $rs[0]['peb']; 
    $_POST['stuffingLocation'] = $rs[0]['stuffinglocation']; 
    $_POST['stuffingIn'] = $obj->formatDBDate($rs[0]['stuffingin'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));
    $_POST['stuffingOut'] = $obj->formatDBDate($rs[0]['stuffingout'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));

    if (!empty($rs[0]['truckingkey'])){
        $rsTrucking = $supplier->getDataRowById($rs[0]['truckingkey']);
        $_POST['hidTruckingSupplierKey'] = $rs[0]['truckingkey'];
        $_POST['truckingSupplierName'] = $rsTrucking[0]['name'];
    }
    
	$_POST['trDesc'] = $rs[0]['trdesc']; 
	$_POST['bookingNumber'] = $rs[0]['bookingnumber']; 
    $_POST['chkIsMaster'] = $rs[0]['ismaster'];  
    
    if($rs[0]['ismaster'] && $rs[0]['loadcontainertypekey'] == EMKL['emklType']['lcl']) 
        $isMaster = 'display-none';
 
    $_POST['totalSelling'] = $obj->formatNumber($rs[0]['totalselling'],2); 

	$rsInvoiceCol = $obj->getAmountInvoiced($rs[0]['pkey']);
	$rsInvoiceCol = $obj->reindexDetailCollections($rsInvoiceCol,'refdetailkey'); 	
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
$arrContainerVolume = $class->convertForCombobox($rsContainer,'pkey','name');  
$arrVolume = $class->convertForCombobox($obj->getVolumeUnit(),'pkey','name');  
$arrFreight = $class->convertForCombobox($obj->getFreightTerm(),'pkey','name');  
$arrUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name');

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
        
        
        
         var emklJobOrder = new EMKLJobOrder(tabID,<?php echo json_encode(
                                                                array(
                                                                    'rs' => $rs,
                                                                    'detail' => $rsDetail,
                                                                    'volumeDetail' => $rsVolumeDetail,
                                                                    'containerNumberDetail' => $rsContainerDetail
                                                                )
                                                            );
                                                    ?>,varConstant);  
        
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
                                        <div class="col-xs-9"><?php echo $obj->inputText('code',array("readonly"=>true)); ?></div>
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipper']); ?></label> 
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
                                                                                    'etc' => $attrHeader,
                                                                                    'callbackFunction' => 'getTabObj().updateSalesman();'
                                                                                  ) 
                                                                                );  
                                            ?> 
                                        </div> 
                                    </div>
                                     
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('poNumber', array('readonly' => $readInvoiced)); ?>
                                        </div> 
                                    </div> 
  				                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bookingNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('bookingNumber', array( 'etc' => $attrHeader )); ?>
                                        </div> 
                                    </div> 
  				                    <div class="form-group">
                                       <label class="col-xs-3 control-label">AJU / PEB</label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume"><?php echo $obj->inputText('aju', array('readonly' => $readInvoiced)); ?></div>
                                                <div >/</div>
                                                <div class="consume"><?php echo $obj->inputText('peb', array('readonly' => $readInvoiced)); ?></div>
                                            </div> 
                                        </div> 
                                     </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['typeOfJob']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                            <div class="consume"><?php echo  $obj->inputSelect('selAirSea', $arrTransportType, array('etc' => $attrHeader)); ?></div>
                                            <div style="width:100px"><?php echo  $obj->inputSelect('selContainerType', $arrContainer, array('allowedStatusForEdit' => array (1),'disabled' => $readInvoiced)); ?></div>
                                            <div class="lcl-only" style="width:150px"><?php    
                                                                                            echo $obj->inputAutoComplete(array(
                                                                                                'objRefer' => $container,
                                                                                                'readonly' => $readInvoiced,
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
                                <div class="form-group lcl-only">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['volume']); ?> / <?php echo ucwords($obj->lang['container']); ?></label> 
                                    <div class="col-xs-9">   
                                        <div class="flex">
                                            <div class="consume"><?php echo  $obj->inputDecimal('weight'); ?></div>
                                            <div class="text-muted" style="margin-right:20px">KG</div> 
                                            <div class="consume"><?php echo  $obj->inputDecimal('volume'); ?></div>
                                            <div class="text-muted" style="margin-right:20px">CBM</div> 
                                            <div>/</div>
                                            <div style="width: 10em">
                                                <?php echo $obj->inputSelect('hidContainerVolumeKey', $arrContainerVolume); ?>
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
                                                    $style = 'style="display:none !important"';
                                                } else{ 
                                                    $_POST['hidDetailVolumeKey[]'] =  $rsVolumeDetail[$i]['pkey'];
                                                    $_POST['selContainerDetailVolumeKey[]'] =  $rsVolumeDetail[$i]['itemkey'];
                                                    $_POST['qtyVolume[]'] =  $obj->formatNumber($rsVolumeDetail[$i]['qty']);
                                                
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
                                                <div><?php echo $obj->inputCheckBox('chkIsMaster', array('readonly' => $readInvoiced)); ?></div> 
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
                                     <div class="form-group doc-detail-only">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesman']); ?></label>  
                                        <div class="col-xs-9"> 
                                         <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $employee,
                                                                                'revalidateField' => false, 
                                                                                'readonly' => $readInvoiced,
                                                                                'element' => array('value' => 'salesName',
                                                                                                   'key' => 'hidSalesKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-employee.php',
                                                                                                    'data' => array(  'action' =>'searchData&issales=1' )
                                                                                                ) , 
                                                                                'popupForm' => array(
                                                                                                    'url' => 'employeeForm.php',
                                                                                                    'element' => array('value' => 'salesName',
                                                                                                           'key' => 'hidSalesKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['employee'])
                                                                                                ),  
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>    
<!--
                                    <div class="form-group fcl-only">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consignee']); ?></label> 
                                        <div class="col-xs-9">  
                                         <?php
                                                    echo $obj->inputAutoComplete(array(
                                                                            'objRefer' => $consignee, 
                                                                            'readonly' => $readInvoiced,
                                                                            'element' => array('value' => 'consigneeName',
                                                                                               'key' => 'hidConsigneeKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-consignee.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                            ) , 
                                                                            'popupForm' => array(
                                                                                                'url' => 'consigneeForm.php',
                                                                                                'element' => array('value' => 'consigneeName',
                                                                                                       'key' => 'hidConsigneeKey'),
                                                                                                'width' => '600px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['consignee'])
                                                                                            ))
                                                                    );  
                                        ?> 
                                        </div> 
                                    </div>
-->
                                 
                                    <div class="form-group fcl-only">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['closingDate']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputDateTime('closingDate', array('allowEmpty' => true )); ?> 
                                        </div> 
                                    </div>                          
				                    <!--<div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['volume']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                            <div class="consume"><?php echo  $obj->inputDecimal('volume', array('disabled' => true)); ?></div> 
                                            <div style="width: 100px"><?php echo  $obj->inputSelect('selVolumeType', $arrVolume, array('disabled' => true)); ?></div> 
                                            </div>
                                        </div>  
                                    </div>-->  
                                        
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['totalSales']); ?></label> 
                                        <div class="col-xs-9"><?php echo $obj->inputDecimal('totalSelling', array('disabled' => true)); ?></div>  
                                    </div> 
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                         <div class="col-xs-9">    
                                               <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?> 
                                        </div>
                                    </div>
                             </div>
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['stuffingDestuffingInformation']); ?></div>
                            
                               <div class="form-group">
                                    <div class="col-xs-12">     
                                        <?php echo ucwords($obj->lang['mbl']); ?>       
                                       <br>
                                           <?php echo $obj->inputText('mblNumber', array( 'readonly' => $readInvoiced )); ?>
                                    
                                    </div>  
                                </div>  
          			           <div class="form-group">
                                    <div class="col-xs-6" style="padding-right:5px;">     
                                        POL    
                                       <br>
                                           <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $port,
                                                                                'revalidateField' => false, 
                                                                                'readonly' => $readInvoiced,
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
                                    <div class="col-xs-6"  style="padding-left:10px">   
                                        POD     
                                       <br>            
                                           <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $port,
                                                                                'revalidateField' => false, 
                                                                                'readonly' => $readInvoiced,
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
                                <div class="form-group">
                                    <div class="col-xs-6" style="padding-right:5px;">     
                                       <?php echo strtoupper($obj->lang['etd']); ?>        
                                       <br>
                                           <?php echo $obj->inputDate('etdPol',array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                          
                                    </div> 
                                    <div class="col-xs-6"  style="padding-left:10px">   
                                       <?php echo strtoupper($obj->lang['eta']); ?>
                                       <br>            
                                           <?php echo $obj->inputDate('etaPod',array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?>
                                        
                                    </div> 
                                     
                                </div>
                              
                                 <div class="form-group">
                                    <div class="col-xs-6" style="padding-right:5px;">     
                                       <?php echo ucwords($obj->lang['vessel']); ?>          
                                       <br>
                                        <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $vessel, 
                                                                                'readonly' => $readInvoiced,
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
                                    <div class="col-xs-6"  style="padding-left:10px">   
                                          <?php echo ucwords($obj->lang['voyage']); ?>        
                                       <br>            
                                          <?php echo $obj->inputText('vesselNumber', array('readonly' => $readInvoiced)); ?> 
                                    </div> 

                                </div>
                            
                              <div class="form-group">
                                    <div class="col-xs-6"  style="padding-right:5px;">   
                                        <?php echo ucwords($obj->lang['carrier']); ?>      
                                            <br>   
                                           <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $supplier,
                                                                                'revalidateField' => true,
                                                                                'readonly' => $readInvoiced,
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
                                  
                                    <div class="col-xs-6" style="padding-left: 10px">     
                                       <?php echo ucwords($obj->lang['agent']); ?>          
                                       <br>
                                        <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $supplier,
                                                                                'revalidateField' => false,
                                                                                'readonly' => $readInvoiced,
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
                                <div class="form-group">
 				               <div class="col-xs-6" style="padding-right:5px;">     
                                       <?php echo ucwords($obj->lang['terminal']); ?>          
                                       <br>
                                        <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $terminal, 
                                                                                'readonly' => $readInvoiced,
                                                                                'element' => array('value' => 'terminalName',
                                                                                                   'key' => 'hidTerminalKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-terminal.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ), 
                                                                                'popupForm' => array(
                                                                                                'url' => 'terminalForm.php',
                                                                                                'element' => array('value' => 'terminalName',
                                                                                                       'key' => 'hidTerminalKey'),
                                                                                                'width' => '600px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['terminal'])
                                                                                            )
                                                                                )
                                                                        );  
                                            ?> 
                                    </div> 
                                    <div class="col-xs-6"  style="padding-left:10px">   
                                          <?php echo ucwords($obj->lang['depot']); ?>        
                                       <br>            
                                            <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $depot, 
                                                                                'readonly' => $readInvoiced,
                                                                                'element' => array('value' => 'depotName',
                                                                                                   'key' => 'hidDepotKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-depot.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ), 
                                                                                'popupForm' => array(
                                                                                                'url' => 'depotForm.php',
                                                                                                'element' => array('value' => 'depotName',
                                                                                                       'key' => 'hidDepotKey'),
                                                                                                'width' => '600px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['depot'])
                                                                                            )
                                                                                )
                                                                        );  
                                            ?> 
                                    </div> 

                                </div> 
                                <div class="form-group">
                                    <div class="col-xs-12">     
                                       <?php echo ucwords($obj->lang['stuffingDestuffingLocation']); ?>          
                                       <br>
                                            <?php echo $obj->inputText('stuffingLocation', array('readonly' => $readInvoiced)); ?> 
                                    </div>  
                                </div> 
                            <div class="form-group"> 
                                 <div class="col-xs-6" style="padding-right:5px;">     
                                   <?php echo ucwords($obj->lang['stuffingIn']); ?>          
                                   <br>
                                        <?php echo $obj->inputDate('stuffingIn', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?> 
                                </div>  
                                 <div class="col-xs-6"  style="padding-left:10px">     
                                   <?php echo ucwords($obj->lang['stuffingOut']); ?>          
                                   <br>
                                        <?php echo $obj->inputDate('stuffingOut', array('etc'=>'style="text-align:center"', 'allowEmpty' => true)); ?> 
                                </div>  
                            </div> 
                            <div class="form-group">
                                <div class="col-xs-12" >  
                                   <?php echo ucwords($obj->lang['trucking']); ?> 
                                    <br>
                                        <?php  echo $obj->inputAutoComplete(array(
                                                                        'objRefer' => $supplier, 
                                                                        'element' => array('value' => 'truckingSupplierName',
                                                                                           'key' => 'hidTruckingSupplierKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-supplier.php',
                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        ) , 
                                                                        'popupForm' => array(
                                                                                            'url' => 'supplierForm.php',
                                                                                            'element' => array('value' => 'truckingSupplierName',
                                                                                                   'key' => 'hidTruckingSupplierKey'),
                                                                                            'width' => '600px',
                                                                                            'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['supplier'])
                                                                                        ))
                                                                );  
                                    ?>
                                        </div> 
                            </div>
                                <div class="form-group">
                                     <div class="col-xs-12">   
                                       <?php echo ucwords($obj->lang['container']); ?> / <?php echo ucwords($obj->lang['seal']); ?> 
                                       <br>            
                                           <?php echo $obj->inputTextArea('containerNumber', array('etc' => 'style="height:10em;"','readonly' => $readInvoiced)); ?>
                                         
                                    </div>
                                </div>

                        </div>   
                          
                    </div>
           </div>
      </div>  
      <div style="clear:both; height:1em;"></div> 
        
    <div class="div-tab-panel doc-detail-only">
        
    <div class="div-table mnv-transaction transaction-detail no-odd-even-style" style="width:100%;" attr-level="0">
        
         <?php  
                        
                $totalRows = count($rsSalesDetail);  
		
				$arrPortKey = array_column($rsSalesDetail,'podkey');
		
				if(!empty($arrPortKey)){ 
					$arrPortKey = $port->searchDataRow(array('pkey','name'), ' and pkey in ('. $obj->oDbCon->paramString($arrPortKey,',').') ');
					$arrPortKey = array_column($arrPortKey,'name','pkey');
				}
		
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

                    if ($i == $totalRows ){
                        $class = 'detail-row-template row-template ';
                        $overwrite = false; 
                        $disable = 'disabled="disabled"';  
                        $readonlyRate = true;
                        $_POST['detailSalesCode[]'] = '[auto code]'; 
						$_POST['invoiceList[]'] = '';
                        $activeCurrency = 'IDR';
                    } else {    
                        
                        $_POST['hidDetailKey[]'] =  $rsSalesDetail[$i]['pkey'];
                        $_POST['detailSalesCode[]'] = $rsSalesDetail[$i]['code'];
                        $_POST['detailHBL[]'] =  $rsSalesDetail[$i]['hbl'];
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
                        
						$_POST['detailPODName[]'] = (isset($arrPortKey[$rsSalesDetail[$i]['podkey']])) ? $arrPortKey[$rsSalesDetail[$i]['podkey']] : '';
						$_POST['hidDetailPODKey[]'] =  $rsSalesDetail[$i]['podkey'];
							
                        
                        $showOtherCurrency = ($_POST['detailOtherCurrencyTotal[]'] <> 0) ? true : false; 
                        $activeCurrency =  $rsSalesDetail[$i]['currencyname'];
                        $readonlyDetail = (in_array($rsSalesDetail[$i]['pkey'],$arrInvoicedKey)) ? true : false;
                        
                        if($readonlyDetail)
                            $readonlyRate = true;
                        else
                            $readonlyRate = ($_POST['selSellingCurrency[]'] == CURRENCY['idr'] ) ? true : false;
                        
						$rsInvoice = (isset( $rsInvoiceCol[$rsSalesDetail[$i]['pkey']] )) ? $rsInvoiceCol[$rsSalesDetail[$i]['pkey']] : array();
						$_POST['invoiceList[]'] = (!empty($rsInvoice)) ?  implode(', ',array_column($rsInvoice,'code')) : '';
						
                        //$obj->setLog($readonlyDetail,true);
                        
                        
                    } 

            ?>


        <div class="div-table-row customer-row <?php echo $class; ?>"> 
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
                                    <div class="div-table-col" style="font-weight:bold"><?php echo $obj->lang['pod']; ?></div>
                                    <div class="div-table-col">
                                         <?php echo $obj->inputText('detailPODName[]',array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>
										 <?php echo $obj->inputHidden('hidDetailPODKey[]',array('overwritePost' => $overwrite,'disabled' =>  $disable)); ?>
                                	</div>
                                </div>
                                <div class="div-table-row">
                                    <div class="div-table-col" style="font-weight:bold"><?php echo $obj->lang['hbl']; ?></div>
                                    <div class="div-table-col">
                                        <?php echo  $obj->inputText('detailHBL[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail ,'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>
                                    </div>
                                </div>

                                 <div class="div-table-row">
                                    <div class="div-table-col" style="font-weight:bold"><?php echo $obj->lang['qty']; ?></div>
                                    <div class="div-table-col"> 
                                        <div class="flex">
                                            <div class="consume"><?php echo  $obj->inputNumber('detailQty[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail ,'disabled' =>  $disable, 'class' =>'form-control label-style')); ?></div>
                                            <div  style="font-weight:bold"><?php echo $obj->inputSelect('detailSelUnit[]', $arrUnit, array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable,'class' =>'form-control label-style')); ?></div>
                                            <div style="width:60px"><?php echo $obj->inputDecimal('detailWeight[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable,'class' =>'form-control inputdecimal label-style')); ?></div>
                                            <div style="font-weight:bold">KG</div>  
                                            <div style="width:60px"><?php echo $obj->inputDecimal('detailMeasurement[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable,'class' =>'form-control inputdecimal label-style')); ?></div>
                                            <div style="font-weight:bold">CBM</div>   
                                        </div> 
                                    </div>
                                </div>  
                             </div>   
                         </div> 
                         <div class="div-table-col" style="width:20px;"> </div>
           <!--              <div class="div-table-col"  style="padding-right: 1em; padding-left: 1em" > 
                             <div class="div-table"> 

                                <div class="div-table-row lcl-only">
                                    <div class="div-table-col"  style="font-weight:bold"><?php echo $obj->lang['salesman']; ?></div>
                                    <div class="div-table-col">
                                    <?php echo $obj->inputText('salesDetailName[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail ,'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>
                                    <?php echo $obj->inputHidden('hidSalesDetailKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable)); ?>
                                    </div>
                                </div>
                                 <div class="div-table-row">
                                    <div class="div-table-col" style="font-weight:bold"><?php echo $obj->lang['destination']; ?></div>
                                    <div class="div-table-col">
                                    <?php echo $obj->inputText('destinationDetailName[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail ,'disabled' =>  $disable, 'class' =>'form-control label-style')); ?>
                                    <?php echo $obj->inputHidden('hidDestinationDetailKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonlyDetail,'disabled' =>  $disable)); ?>
                                    </div>
                                </div> 
                             </div>   
                         </div>  -->
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

                    <div class="div-table transaction-detail detail-item" style="width:100%; border-bottom:1px solid #333;"  attr-level="1" attr-group="hidDetailItemKey">
                      <div class="div-table-row">   
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
                            <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?>  icon-col"><?php echo (!$readonly) ? $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="item-row-template"')) : ''; ?></div>
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
