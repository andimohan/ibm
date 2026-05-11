<?php 
require_once '../../../_config.php'; 
require_once '../../../_include-v2.php'; 

includeClass('EMKLJobOrderHeader.class.php');
$emklJobOrderHeaderImport = createObjAndAddToCol(new EMKLJobOrderHeader(EMKL['jobType']['import']));

$container = createObjAndAddToCol(new Container());
$port = createObjAndAddToCol(new Port());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());
$itemUnit = createObjAndAddToCol(new ItemUnit());
$emklJobOrderImport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['import']));
$vessel = createObjAndAddToCol(new Vessel());
$terminal = createObjAndAddToCol(new Terminal());
$supplier = createObjAndAddToCol(new Supplier());

$obj = $emklJobOrderHeaderImport;

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true)); 

$formAction = 'emklJobOrderImportHeaderList';

$parentFileName = $_GET['fileName'];
$parentPanelId = $_GET['selectedPanelId'];
$parentTitle = $_GET['title'];

$editWarehouseInactiveCriteria = ''; 
$editSalesInactiveCriteria = ''; 
$editCityInactiveCriteria = ''; 
 
$rsDetail = array();

$_POST['trDate'] = date('d / m / Y');
/*$_POST['closingDate'] = date('d / m / Y 00:00');
$_POST['etaPod'] = date('d / m / Y');
$_POST['etdPol'] = date('d / m / Y'); 
$_POST['stuffingIn'] = date('d / m / Y');
$_POST['stuffingOut'] = date('d / m / Y');*/

$_POST['selTypeOfJob'] = EMKL['jobType']['import'];

$saleskey = base64_decode($_SESSION[$obj->loginAdminSession]['id']); 
$_POST['selSalesKey'] = $saleskey;
 
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

$totalWeight = 0;
 
$rs = prepareOnLoadData($obj);  

$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');    

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	 
	$rsDetail = $obj->getDetailWithRelatedInformation($id);
    $_POST['bookingNumber'] = $rs[0]['bookingnumber']; 
    $_POST['refNumber'] = $rs[0]['refnumber']; 
    $_POST['volume'] = $obj->formatNumber($rs[0]['volume'],2);
    $_POST['weight'] = $obj->formatNumber($rs[0]['weight'],2);
    $_POST['temperature'] = $obj->formatNumber($rs[0]['temperature'],-2);
    $_POST['hidCargoType'] = $rs[0]['containertypekey'];
    
    $_POST['hidVesselKey'] = $rs[0]['vesselkey'];
	if(!empty($rs[0]['vesselkey'])){
        $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
        $_POST['vesselName'] = $rsVessel[0]['name'];
    }
    
	$_POST['vesselNumber'] = $rs[0]['vesselnumber'];
    
    if (!empty($rs[0]['terminalkey'])){
        $rsTerminal = $terminal->getDataRowById($rs[0]['terminalkey']);
        $_POST['hidTerminalKey'] = $rs[0]['terminalkey'];
        $_POST['terminalName'] = $rsTerminal[0]['name'];
    }
    
    
    $_POST['etdPol'] = $obj->formatDBDate($rs[0]['etdpol'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));
    $_POST['etaPod'] = $obj->formatDBDate($rs[0]['etapod'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));
    $_POST['closingDate'] = $obj->formatDBDate($rs[0]['closingdate'],'d / m / Y H:i', array('returnOnEmpty'=>true, 'value' => '')); 
    $_POST['aju'] = $rs[0]['aju']; 
	$_POST['peb'] = $rs[0]['peb'];
    $_POST['stuffing'] = $rs[0]['stuffing'];
    $_POST['stuffingIn'] = $obj->formatDBDate($rs[0]['stuffingin'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));
    $_POST['stuffingOut'] = $obj->formatDBDate($rs[0]['stuffingout'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => ''));
    $_POST['containerNumber'] = $rs[0]['containernumber'];
    $_POST['invoiceNumber'] = $rs[0]['invoicenumber'];
    $_POST['selContainerType'] = $rs[0]['loadcontainertypekey'];
	$_POST['selAirSea'] = $rs[0]['transportationtypekey'];
    
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
    
    if (!empty($rs[0]['truckingkey'])){
        $rsVendor = $supplier->getDataRowById($rs[0]['truckingkey']);
        $_POST['hidVendorKey'] = $rs[0]['truckingkey'];
        $_POST['vendorName'] = $rsVendor[0]['name'];
    }
    
 
    if(!empty($rs[0]['saleskey'])){ 
   	    $rsSales = $employee->getDataRowById($rs[0]['saleskey']);
	   $_POST['salesName'] = $rsSales[0]['name'] ; 
    }
    
    if (!empty($rs[0]['itemkey'])){
        $rsItem = $container->getDataRowById($rs[0]['itemkey']);
        $_POST['hidContainerKey'] = $rs[0]['itemkey'];
        $_POST['containerName'] = $rsItem[0]['name'];
    }

    
    if (!empty($rs[0]['customerkey'])){
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
        $_POST['hidCustomerKey'] = $rsCustomer[0]['pkey'] ;
    }    
    
    if (!empty($rs[0]['customerpebkey'])){
        $rsCustomer = $customer->getDataRowById($rs[0]['customerpebkey']);
        $_POST['customerPEBName'] = $rsCustomer[0]['name'] ;
        $_POST['hidCustomerPEBKey'] = $rsCustomer[0]['pkey'] ;
    }
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y');
	$_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
	  
	$_POST['trDesc'] = $rs[0]['trdesc'];
	//$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']);  

	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']);
	$_POST['hidSalesKey'] = $rs[0]['saleskey'];
	if(!empty($rs[0]['saleskey'])){ 
   	    $rsSales = $employee->getDataRowById($rs[0]['saleskey']);
	   $_POST['salesName'] = $rsSales[0]['name'] ; 
    }
    
    
	$editWarehouseInactiveCriteria = ' or  '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']);  
    $editSalesInactiveCriteria = 'or '.$employee->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['saleskey']);
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name');  
//$arrSales = $obj->convertForCombobox($employee->searchData('','',true, ' and ('.$employee->tableName.'.statuskey = 2 ' .$editSalesInactiveCriteria.')'),'pkey','name'); 
//$arrShipment = $obj->convertForCombobox($shipment->getAllShipment(),'servicekey','joinservicename');
$arrUnit = $class->convertForCombobox($itemUnit->searchData('','',true, ' and ('.$itemUnit->tableName.'.statuskey = 1 )'),'pkey','name');
$arrContainer = $class->convertForCombobox($container->searchData('','',true, ' and ('.$container->tableName.'.statuskey = 1 )'),'pkey','name');
$arrType = $class->convertForCombobox($obj->getEmklType(),'pkey','name'); 
$arrTransportType = $class->convertForCombobox($emklJobOrderImport->getTransportationType(),'pkey','name');  
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript">  
   
	jQuery(document).ready(function(){  
        var tabID = <?php echo (isset($isQuickAdd) && $isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        var varConstant = { 
                            EMKL : <?php echo json_encode(EMKL); ?> 
                            };
        
        var emklJobOrderHeader = new EmklJobOrderHeader(tabID,null,varConstant); 
        prepareHandler(emklJobOrderHeader); 
        
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
    <?php echo $obj->inputHidden('selTypeOfJob'); ?>
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
                                            <?php echo $obj->inputDate('trDate', array('readonly' => (empty($rs)) ? false : true )); ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selWarehouseKey', $arrWarehouse); ?>
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
                                            <div class="consume"><?php echo  $obj->inputSelect('selAirSea', $arrTransportType, array('allowedStatusForEdit' => array (1))); ?></div>
                                            <div><?php echo  $obj->inputSelect('selContainerType', $arrType); ?></div>
                                            <div> <?php echo $obj->inputSelect('hidCargoType', $arrCargoType); ?> </div>
                                            </div>
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['bookingNumber']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php echo $obj->inputText('bookingNumber'); ?>  
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipper']); ?></label> 
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
                                                                               'callbackFunction' => 'getTabObj().updateSalesman();'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['shipperPIB']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $customer,
                                                                                'revalidateField' => true,
                                                                                'element' => array('value' => 'customerPEBName',
                                                                                                   'key' => 'hidCustomerPEBKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-customer.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => array(
                                                                                                    'url' => 'customerForm.php',
                                                                                                    'element' => array('value' => 'customerPEBName',
                                                                                                           'key' => 'hidCustomerPEBKey'),
                                                                                                    'width' => '1000px',
                                                                                                    'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['customer'])
                                                                                                ),
                                                                                //'callbackFunction' => 'getTabObj().updateSalesman(); getTabObj().updateRecipients()'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>
                                    
                                    <div class="form-group">
                                       <label class="col-xs-3 control-label">POL / POD</label> 
                                        <div class="col-xs-9"> 
                                            <div class="flex">
                                                <div class="consume">
                                                    <?php    
                                                        echo $obj->inputAutoComplete(array(
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
                                                <div >/</div>
                                                <div class="consume">
                                                    <?php    
                                                        echo $obj->inputAutoComplete(array(
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
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['carrier']); ?></label> 
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
                                <div class="form-group lcl">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['volume']); ?> / <?php echo ucwords($obj->lang['container']); ?></label> 
                                    <div class="col-xs-9">   
                                        <div class="flex">
                                            <div class="consume"><?php echo  $obj->inputDecimal('weight'); ?></div>
                                            <div class="text-muted" style="margin-right:20px">KG</div> 
                                            <div class="consume"><?php echo  $obj->inputDecimal('volume'); ?></div>
                                            <div class="text-muted" style="margin-right:20px">CBM</div> 
                                            <div>/</div>
                                            <div style="width: 10em">
                                                <?php echo $obj->inputSelect('hidContainerKey', $arrContainer); ?>
                                            </div>
                                        </div>
                                    </div> 
                                </div>    
  				                <div class="form-group truckingfcl">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['volume']); ?></label> 
                                    <div class="col-xs-9">  
                                            <div class="div-table mnv-transaction transaction-detail">
                                        <?php 
                                            $totalRows = count($rsDetail);
                                            for ($i=0;$i<=$totalRows; $i++){ 
                                                
                                                $class =  'transaction-detail-row';
                                                $overwrite = true;
                                                $readonly = false;
                                                $disabled = false; 
                                                //$style = '';

                                                if ($i == $totalRows ){
                                                    $class = 'detail-row-template row-template';
                                                    $overwrite = false;
                                                    $disabled = true; 
                                                    $isLocked = false;
                                                    //$style = 'style="display:none"';
                                                } else{ 
                                                    $_POST['hidDetailKey[]'] =  $rsDetail[$i]['pkey'];
                                                    $_POST['selContainerDetailKey[]'] =  $rsDetail[$i]['itemkey'];
                                                    $_POST['qty[]'] =  $obj->formatNumber($rsDetail[$i]['qty']);
                                                
                                                }
                                                $hideDeleteIcon = '';  
                                            ?>
                                            <div class="div-table-row <?php echo $class; ?> odd-style-adjustment"> 
                                                <div class="div-table-col"> 
                                                    <div class="flex">     
                                                        <div class="consume">
                                                            <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled)); ?>
                                                            <?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite ,'readonly' => $readonly, 'disabled' => $disabled )); ?>
                                                        </div>
                                                        <div style="width:150px;">
                                                            <?php echo $obj->inputSelect('selContainerDetailKey[]', $arrContainer, array('overwritePost' => $overwrite, 'readonly' => $readonly, 'disabled' => $disabled )); ?>
                                                        </div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnAddDetailRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="detail-row-template"')); ?></div>
                                                        <div class="icon-col <?php echo $obj->hideOnDisabled(); ?>"><?php echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0; '.$hideDeleteIcon.'"')); ?></div>
 
                                                    </div> 
                                                </div> 
                                            </div>   
                                        <?php }	 ?>  
                                        
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
                                            <div>/</div>
                                            <div style="width: 10em">
                                                  <?php echo $obj->inputText('vesselNumber'); ?> 
                                            </div>
                                        </div>
                                    </div>   
                                </div>
                                <div class="form-group"> 
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['stackArea']); ?></label> 
                                    <div class="col-xs-9"> 
                                             <?php  echo $obj->inputAutoComplete(array( 
                                                                                'objRefer' => $terminal,  
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
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['closingDate']); ?></label> 
                                    <div class="col-xs-9">  
                                        <?php echo $obj->inputDateTime('closingDate', array('allowEmpty' => true )); ?> 
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['invoiceNumber']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php echo $obj->inputText('invoiceNumber'); ?>
                                    </div> 
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['temperature']); ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php echo $obj->inputDecimal('temperature'); ?>
                                    </div> 
                                </div>   
                             </div>
                         
                    </div>
                     <div class="div-table-col">   
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['stuffingDestuffingInformation']); ?></div>
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
                                   AJU        
                                   <br>
                                       <?php echo $obj->inputText('aju'); ?>

                                </div> 
                                <div class="col-xs-6"  style="padding-left:10px">   
                                   PEB
                                   <br>            
                                       <?php echo $obj->inputText('peb'); ?>
                                </div> 
                            </div> 
                            
                             <div class="form-group">
                                <div class="col-xs-12" >  
                                   <?php echo ucwords($obj->lang['stuffing']); ?> 
                                    <br> 
                                    <?php echo $obj->inputText('stuffing'); ?>
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
                                 <div class="col-xs-12">   
                                    <?php echo ucwords($obj->lang['note']); ?>      
                                   <br>            
                                       <?php echo $obj->inputTextArea('trDesc', array('etc' => 'style="height:15em;"')); ?>

                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-xs-12" >  
                                   <?php echo ucwords($obj->lang['trucking']); ?> 
                                    <br>
                                        <?php  echo $obj->inputAutoComplete(array(
                                                                        'objRefer' => $supplier, 
                                                                        'element' => array('value' => 'vendorName',
                                                                                           'key' => 'hidVendorKey'),
                                                                        'source' =>array(
                                                                                            'url' => 'ajax-supplier.php',
                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        ) , 
                                                                        'popupForm' => array(
                                                                                            'url' => 'supplierForm.php',
                                                                                            'element' => array('value' => 'vendorName',
                                                                                                   'key' => 'hidVendorKey'),
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
                                       <?php echo $obj->inputTextArea('containerNumber', array('etc' => 'style="height:10em;"')); ?>

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
