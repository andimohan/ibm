<?php

require_once '../../../_config.php';
require_once '../../../_include-v2.php';

includeClass(array('TruckingServiceWorkOrder.class.php','ItemUnit.class.php'));
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());
$workProgress = createObjAndAddToCol(new WorkProgress());
$workProgressStep = createObjAndAddToCol(new WorkProgressStep());
$warehouse = createObjAndAddToCol(new Warehouse());
$supplier = createObjAndAddToCol(new Supplier());
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$terminal = createObjAndAddToCol(new Terminal());
$location = createObjAndAddToCol(new Location());
$depot = createObjAndAddToCol(new Depot());
$port = createObjAndAddToCol(new Port());
$truckingJob = createObjAndAddToCol(new TruckingJob());
$car = createObjAndAddToCol(new Car());
$chassis = createObjAndAddToCol(new Chassis()); 
$ap = createObjAndAddToCol(new AP());
$truckingCost =  createObjAndAddToCol(new Service(TRUCKING_SERVICE,1));
$truckingCostCashOut =  createObjAndAddToCol(new TruckingCostCashOut());
$consignee = createObjAndAddToCol(new Consignee());   
$itemUnit = new ItemUnit();
$carCategory = createObjAndAddToCol(new CarCategory());

$obj= $truckingServiceWorkOrder;

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$hasCostAccess = $security->isAdminLogin($obj->costSecurityObject,10);
    
$formAction = 'truckingServiceWorkOrderList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 
    
$_POST['trDate'] =  date('d / m / Y');
$_POST['trDateStuffing'] = date('d / m / Y 00:00');
 
$rsSODetail = array(); 
$arrCategory = array();
$rsCost = array();
$rsCargoDetail = array();
$rsProgressStep = $workProgressStep->searchData($workProgressStep->tableName.'.statuskey','1',true,' order by orderlist asc');

$rs = prepareOnLoadData($obj);  
$rsItemFile = array();

$rsTruckingCost = $truckingCost->searchData($truckingCost->tableName . '.statuskey', 1, true, ' and isdroppointdetailprice = 1', 'order by ismultipliedbyqty desc, name asc');

$showRealizationCost = $obj->useRealization();
$editWarehouseInactiveCriteria = '';
$cashOutDownpaymentKey = 0;
$cashOutDownpaymentCode = '';

$partyDecimal = $obj->loadSetting('jobOrderPartyDecimal'); 
$sellingPriceAllowed = $security->isAdminLogin($truckingServiceOrder->sellingPriceSecurityObject, 10);
if($partyDecimal == '')  $partyDecimal = 0; // buat JS

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	
    $rsServiceOrder = $truckingServiceOrder->searchData($truckingServiceOrder->tableName.'.pkey', $rs[0]['refkey'],true);
    $rsSODetail = $truckingServiceOrder->getDetailWithRelatedInformation($rsServiceOrder[0]['pkey']);
    $rsCost = $obj->getCostDetail($id,'','',' order by '.$obj->tableItem.'.fixedcost desc, '.$obj->tableItem.'.name asc');
    $rsProgress = $workProgress->getProgress($id,$rs[0]['driverkey']);
    $arrProgress = array_column($rsProgress,'progresskey');

    $rsCargoDetail = $obj->getCargoDetail($rs[0]['pkey']);
    
   // $rsTariff = $truckingSellingRate->searchData($truckingSellingRate->tableName.'.pkey',$rsServiceOrder[0]['contractkey']);
            
    if ($rs[0]['statuskey'] == 2)  
        $statusConfirmed = array('status' => true, 'readonly' => 'readonly="readonly"',  'disabled' =>  'disabled="disabled"');
   
    $cashOutDownpaymentKey  = $rs[0]['refcashoutdownpaymentkey'];
    if(!empty($cashOutDownpaymentKey)){
        $rsDownpaymentCashOut = $truckingCostCashOut->getDataRowById($cashOutDownpaymentKey);
        $cashOutDownpaymentCode = $rsDownpaymentCashOut[0]['code'];
    }
    
	//$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
    $_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y', array('returnOnEmpty'=>true, 'value' => '')); 
    
    $_POST['hidSOKey'] = $rsServiceOrder[0]['pkey']; 
    $_POST['soNumber'] = $rsServiceOrder[0]['code'];
    
    $_POST['categoryName'] = $rsServiceOrder[0]['categoryname'];
    $_POST['cargoTypeName'] = $rsServiceOrder[0]['cargotype'];
    $_POST['customerName'] = $rsServiceOrder[0]['customername'];
    // $_POST['consigneeName'] = $rsServiceOrder[0]['consigneename'];
    $_POST['warehouseName'] = $rsServiceOrder[0]['consigneewarehousename']; 
	$_POST['doNumber'] = $rsServiceOrder[0]['donumber'];
	$_POST['shipmentNumber'] = $rsServiceOrder[0]['shipmentnumber'];  
	$_POST['contactPerson'] = $rsServiceOrder[0]['consigneecontactperson']; 
        
    $_POST['driverCommission'] = $obj->formatNumber($rs[0]['drivercommission']);     
    $_POST['codriverCommission'] = $obj->formatNumber($rs[0]['codrivercommission']); 
	$_POST['stuffingAddress'] = $rs[0]['stuffingaddress']; 
     
    $_POST['hidPlannerKey'] = $rs[0]['plannerkey']; 
	if (!empty($rs[0]['plannerkey'])){
		$rsPlanner = $employee->getDataRowById($rs[0]['plannerkey']);
		$_POST['plannerName'] = $rsPlanner[0]['name'];
	}  

    $_POST['hidConsigneeKey'] = $rs[0]['consigneekey'];
    if(!empty($rs[0]['consigneekey'])) {
        $rsConsignee = $consignee->getDataRowById($rs[0]['consigneekey']);
        $_POST['consigneeName'] = $rsConsignee[0]['name'];
    }
    
    $_POST['hidTerminalKey'] = $rs[0]['terminalkey']; 
	if (!empty($rs[0]['terminalkey'])){
		$rsUTC = $terminal->getDataRowById($rs[0]['terminalkey']);
		$_POST['terminalName'] = $rsUTC[0]['name'];
	}  
    $_POST['hidLocationKey'] = $rs[0]['locationkey']; 
	if (!empty($rs[0]['locationkey'])){
		$rsLocation= $location->getDataRowById($rs[0]['locationkey']);
		$_POST['locationName'] = $rsLocation[0]['name'];
	}     
    $_POST['hidDepotKey'] = $rsServiceOrder[0]['depotkey']; 
	if (!empty($rsServiceOrder[0]['depotkey'])){
		$rsDepo = $depot->getDataRowById($rsServiceOrder[0]['depotkey']);
		$_POST['depotName'] = $rsDepo[0]['name'];
	}  
    
	$_POST['trDateStuffing'] = $obj->formatDBDate($rs[0]['stuffingdatetime'],'d / m / Y H:i');   
	$_POST['hidSODetailKey'] = $rs[0]['refdetailkey'];  
	$_POST['hidItemKey'] = $rs[0]['itemkey'];  
    
    $_POST['containerNumber'] =  $rs[0]['containernumber'];
    $_POST['container2Number'] =  $rs[0]['container2number'];
    $_POST['sealNumber'] =  $rs[0]['sealnumber'];
    $_POST['seal2Number'] =  $rs[0]['seal2number'];
    $_POST['routeFrom'] =  $rs[0]['routefrom'];
    $_POST['routeTo'] =  $rs[0]['routeto'];
    $_POST['productDescription'] =  $rs[0]['productdesc'];
    $_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
     
    $_POST['hidDriverKey'] = $rs[0]['driverkey']; 
    $_POST['hidBeforeDriverKey'] = $rs[0]['driverkey']; 
    
	if (!empty($rs[0]['driverkey'])){
		$rsEmployee = $employee->getDataRowById($rs[0]['driverkey']);
		$_POST['driverName'] = $rsEmployee[0]['name'];
	}
    
    $_POST['hidCoDriverKey'] = $rs[0]['codriverkey']; 
	if (!empty($rs[0]['codriverkey'])){
		$rsEmployee = $employee->getDataRowById($rs[0]['codriverkey']);
		$_POST['coDriverName'] = $rsEmployee[0]['name'];
	}
    
    $_POST['hidCarKey'] = $rs[0]['carkey']; 
	if (!empty($rs[0]['carkey'])){
		$rsCar = $car->getDataRowById($rs[0]['carkey']);
		$_POST['policeNumber'] = $rsCar[0]['code']. ' - ' . $rsCar[0]['policenumber'];
	}

    $_POST['hidReplacementCarKey'] = $rs[0]['replacementcarkey']; 
	if (!empty($rs[0]['replacementcarkey'])){
		$rsReplacementCar = $car->getDataRowById($rs[0]['replacementcarkey']);
		$_POST['replacementPoliceNumber'] = $rsCar[0]['code']. ' - ' . $rsCar[0]['policenumber'];
	}
    
    $_POST['hidChassisKey'] = $rs[0]['chassiskey']; 
	if (!empty($rs[0]['chassiskey'])){
		$rsChassis = $chassis->getDataRowById($rs[0]['chassiskey']);
		$_POST['chassisNumber'] = $rsChassis[0]['chassisnumber'];
	}
    
    $_POST['hidSupplierKey'] = $rs[0]['supplierkey']; 
    $_POST['hidBeforeSupplierKey'] = $rs[0]['supplierkey']; 
    
	if (!empty($rs[0]['supplierkey'])){
		$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
		$_POST['supplierName'] = $rsSupplier[0]['name'];
	}
 
    
    $_POST['outsourceCarRegistrationNumber'] = $rs[0]['outsourcecarregistrationnumber'];
    $_POST['outsourceCost'] = $obj->formatNumber($rs[0]['outsourcecost']);
    $_POST['outsourceDownpayment'] = $obj->formatNumber($rs[0]['outsourcedownpayment']);
    $_POST['outsourceAP'] = $obj->formatNumber($rs[0]['outsourceap']);
    $_POST['hidDownpaymentRecipientKey'] = $rs[0]['downpaymentemployeekey'];
    
    if (!empty($rs[0]['downpaymentemployeekey'])){
		$rsEmployee = $employee->getDataRowById($rs[0]['downpaymentemployeekey']);
		$_POST['downpaymentRecipientName'] = $rsEmployee[0]['name'];
	}
     
        
    $_POST['chkIsOutsource'] = $rs[0]['isoutsource'];  
	$_POST['trDesc'] = $rs[0]['trdesc'];   
	$_POST['selJobType'] = $rs[0]['jobtypekey'];  
    
    $_POST['total'] = $obj->formatNumber($rs[0]['total']);
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax'];  
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
     
     
	$_POST['cargoQty'] = $obj->formatNumber($rs[0]['cargoqty'],2); 
	$_POST['cargoWeight'] = $obj->formatNumber($rs[0]['cargoweight'],2); 
	$_POST['cargoQtyUnit'] = $rs[0]['cargoqtyunit'];  
	$_POST['cargoWeightUnit'] = $rs[0]['cargoweightunit'];  

	$rsItemFile = $truckingServiceOrder->getItemFile($rs[0]['refkey']);
 
    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
       if (!empty($rs[0]['outsourcecarcategorykey'])){
		$rsCarCategory = $carCategory->getDataRowById($rs[0]['outsourcecarcategorykey']);
		$_POST['outsourceCarCategoryName'] = $rsCarCategory[0]['name'];
		$_POST['hidOutsourceCarCategoryKey'] = $rs[0]['outsourcecarcategorykey'];
	}
}else{

    // ambil semua cost fixed dulu
    $rsCost = $truckingCost->searchData($truckingCost->tableName.'.statuskey',1,true,' and showintrucking = 1 and chargetype = 2 and fixedcost = 1','order by fixedcost desc, name asc');
    for($i=0;$i<count($rsCost);$i++){ 
         $rsCost[$i]['costkey']=$rsCost[$i]['pkey']; 
         $rsCost[$i]['pkey']=0; 
         $rsCost[$i]['realizationkey']=0; 
    }
}

$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrContainer = $obj->convertForCombobox($rsSODetail,'pkey','label');

$rsSODetailInfo = array_column($rsSODetail,null,'pkey');
foreach($arrContainer as $key=>$row){
	$soRow = $rsSODetailInfo[$key];
	$arrContainer[$key]['label'] = '#' . $soRow['numberlabel'] . ' ' . $obj->formatNumber($soRow['qtyinbaseunit'],$partyDecimal) . 'x '. $soRow['itemname'];
}

$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 

$rsCategory = $truckingJob->searchData(); 
$arrJobType = $obj->convertForCombobox($rsCategory,'pkey','name');     

$arrWeight = $obj->generateComboboxOpt(array('data' => $obj->getSystemWeight()));  
$arrUnit = $itemUnit->generateComboboxOpt(null,array('criteria' =>' and unittype = 1 and ('.$itemUnit->tableName.'.statuskey = 1 ' . $editUnitInactiveCriteria. ')')); 


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title> 
 
<script type="text/javascript"> 
    var objAndValueForSupplierDetailAutoComplete = [];
    var objAndValueForEmployeeDetailAutoComplete = [];
    var objAndValueForDestinationDetailAutoComplete = [];
    function TruckingServiceWorkOrder(tabID, varConstant) { 

        this.costDetailField = function costDetailField() {

            var arrTruckingCost = varConstant.arrTruckingCost;
    
            //define field
            var arrCostDetailField = [];
            var fieldCostDetailName = 'costCargoDetail_';
            var fieldSellingCostDetailName = 'sellingCostCargoDetail_';
            var fieldMultipliedName = 'hidIsMultipliedQty_';
            for(var i = 0; i < arrTruckingCost.length; i++) {
               arrCostDetailField.push([
                   `${fieldCostDetailName}${arrTruckingCost[i].pkey}[]`,
                   `${fieldSellingCostDetailName}${arrTruckingCost[i].pkey}[]`,
                   `${fieldMultipliedName}${arrTruckingCost[i].pkey}[]`
                ]);
            }

            return arrCostDetailField;
        }
        
        
        var arrDetails = {};
         
         <?php if (!empty($_GET['id'])){ 
                  for($i=0;$i<count($rsSODetail);$i++){  
                        echo 'arrTemp = {};';
                        echo 'arrTemp[\'stuffingdatetime\'] = "' . $obj->formatDBDate($rsSODetail[$i]['trdate'],'d / m / Y H:i') .'";';
                        echo 'arrTemp[\'itemname\'] = "' . $rsSODetail[$i]['itemname'] . '";'; 
                        echo 'arrTemp[\'itemkey\'] = "' . $rsSODetail[$i]['itemkey'] . '";'; 
                        echo 'arrDetails['.$rsSODetail[$i]['pkey'].'] = arrTemp; ';    
                   } 
             } ?>
         
        
         this.updateSOInformation = function updateSOInformation(){
                
                // udpate detail container
               $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-service-order.php', 
                    async: false,
                    data: "action=getDetailById&pkey=" + $("#" + tabID + " [name=hidSOKey]").val() ,  
                }).done(function( data ) {
                   
                    data = JSON.parse(data) ;  
    
                    // udpate detail
                    for(i=0;i<data.length;i++){  
                        var pkey = data[i].pkey;
                         
                        var arrTemp = {}; 
                        arrTemp['stuffingdatetime'] = moment(data[i].trdate).format("DD / MM / YYYY HH:mm");
                        arrTemp['itemkey'] = data[i].itemkey;
                        arrTemp['itemname'] = data[i].itemname;
                         
                        arrDetails[pkey] = arrTemp;     
                    }
                    
                    // update combobox services
                    var newOptions = {};
                    for(i=0;i<data.length;i++){
						var qtyParty = data[i].qtyinbaseunit;
 
						//.formatCurrency({roundToDecimalPlace: 2 })
						var temp = $("<div>"+qtyParty+"</div>").formatCurrency({roundToDecimalPlace: <?php echo $partyDecimal; ?> }).text(); 
						newOptions[data[i].pkey] =  '#' + data[i].numberlabel + ' ' + temp + 'x ' + data[i].itemname;       
					}
                        
                    
                    var select = $("#" + tabID + " [name=hidSODetailKey]");
                    if(select.prop)  
                      var options = select.prop('options');
                    else  
                      var options = select.attr('options');
                    
                    $('option', select).remove();

                    $.each(newOptions, function(val, text) {
                        options[options.length] = new Option(text, val);
                    });

                    select.find('option:eq(0)').prop('selected', true).change();

                }); 
              
             

         } 
         
         this.updateSOInformationHeader = function updateSOInformationHeader(){
                // update informasi tentang SO 
                $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-service-order.php', 
                    async: false,
                    data: "action=getDataRowById&pkey=" + $("#" + tabID + " [name=hidSOKey]").val() ,  
                }).done(function( data ) { 

                    data = JSON.parse(data) ; 
                     
                    $("#" + tabID + " [name=categoryName]").val(""); 
                    $("#" + tabID + " [name=cargoTypeName]").val(""); 
                    $("#" + tabID + " [name=hidCategoryKey]").val(""); 
                    $("#" + tabID + " [name=jobTypeName]").val(""); 
                    $("#" + tabID + " [name=customerName]").val("");
                    $("#" + tabID + " [name=warehouseName]").val("");
                    $("#" + tabID + " [name=consigneeName]").val("");
                    $("#" + tabID + " [name=contactPerson]").val("");
                    $("#" + tabID + " [name=stuffingAddress]").val("");
                     
                    $("#" + tabID + " [name=doNumber]").val("");
                    $("#" + tabID + " [name=shipmentNumber]").val("");
                    $("#" + tabID + " [name=depotName]").val("");
                    $("#" + tabID + " [name=locationName]").val("");
                     
                    $("#" + tabID + " [name=routeFrom]").val("");
                    $("#" + tabID + " [name=routeTo]").val("");
                    
                    if (data.length > 0 ){
                         
                        data = data[0]; 

                        $("#" + tabID + " [name=categoryName]").val(data.categoryname); 
                        $("#" + tabID + " [name=cargoTypeName]").val(data.cargotype); 
                        $("#" + tabID + " [name=hidCategoryKey]").val(data.categorykey); 
                        $("#" + tabID + " [name=jobTypeName]").val(data.jobtypename); 
                        $("#" + tabID + " [name=customerName]").val(data.customername);
                        $("#" + tabID + " [name=warehouseName]").val(data.consigneewarehousename);  
                        $("#" + tabID + " [name=consigneeName]").val(data.consigneename);
                        $("#" + tabID + " [name=contactPerson]").val(data.consigneecontactperson);
                        $("#" + tabID + " [name=stuffingAddress]").val(decodeHTMLEntities(data.stuffingaddress)) ;  
                        
                        $("#" + tabID + " [name=doNumber]").val(data.donumber);
                        $("#" + tabID + " [name=shipmentNumber]").val(data.shipmentnumber);
                        $("#" + tabID + " [name=depotName]").val(data.depotname);
                        $("#" + tabID + " [name=locationName]").val(data.locationname);
                        $("#" + tabID + " [name=routeFrom]").val(data.routefrom);
                        $("#" + tabID + " [name=routeTo]").val(data.routeto);
                        truckingServiceWorkOrder.updateSOInformation();
                    }
                    
                });  

         } 
         
        
         this.updateSupplierInformation = function updateSupplierInformation(){
            
            $("#" + tabID + " [name=hidSupplierKey]").val("");
            $("#" + tabID + " [name=supplierName]").val(""); 
         
            var purchasekey = $( "#" + tabID + " [name=hidPurchaseOrderKey]" ).val();
         
            $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-purchase-order.php',
                    async: false,
                    data: "action=getDataRowById&pkey=" + purchasekey ,  
                }).done(function( data ) { 
                        data = JSON.parse(data) ;  
                        if (data.length > 0){   
                            data = data[0];  
                              
                            $("#" + tabID + " [name=hidSupplierKey]").val(data.supplierkey);
                            $("#" + tabID + " [name=supplierName]").val(data.suppliername);
                            
                            truckingServiceWorkOrder.updateCostSupplier();
                            //truckingServiceWorkOrder.updateSupplierDetail();
                        }  
                });   
         
        }
         
        this.removeSupplierOnChange = function removeSupplierOnChange(obj){ 
          var row =  $(obj).closest(".transaction-detail-row");
          row.find("[name='supplierDetailName[]']").val("");
          row.find("[name='hidSupplierDetailKey[]']").val("");
        }
          
        this.removeEmployeeOnChange = function removeEmployeeOnChange(obj){ 
          var row =  $(obj).closest(".transaction-detail-row");
          row.find("[name='employeeDetailName[]']").val("");
          row.find("[name='hidEmployeeDetailKey[]']").val("");
        }
         
        this.updateCostEmployeeKey = function updateCostEmployeeKey(){ 
          var driverKey = $("#" + tabID + " [name=hidDriverKey]").val();
          var driverName = $("#" + tabID + " [name=driverName]").val();
      
          var beforeDriverKey = $("#" + tabID + " [name=hidBeforeDriverKey]").val();
             
          $("#" + tabID + " [name=\"hidEmployeeDetailKey[]\"]").each(function() {   
             var supplierKey = $(this).closest(".transaction-detail-row").find("[name='hidSupplierDetailKey[]']").val();
             var refCashOutKey = $(this).closest(".transaction-detail-row").find("[name='hidRefCashOutKey[]']").val();  
             
             if ($(this).val() == beforeDriverKey && supplierKey == 0 && refCashOutKey == 0){ 
                 $(this).val(driverKey); 
                 $(this).closest("div").find("[name='employeeDetailName[]']").val(driverName);
             }
          })
            
          $("#" + tabID + " [name=hidBeforeDriverKey]").val(driverKey);
            
        }
        
        this.updateCostSupplierKey = function updateCostSupplierKey(){ 
          var supplierKey = $("#" + tabID + " [name=hidSupplierKey]").val();
          var supplierName = $("#" + tabID + " [name=supplierName]").val();
            
          var beforeSupplierKey = $("#" + tabID + " [name=hidBeforeSupplierKey]").val();
             
          $("#" + tabID + " [name=\"hidSupplierDetailKey[]\"]").each(function() {   
             var employeeKey = $(this).closest(".transaction-detail-row").find("[name='hidEmployeeDetailKey[]']").val();
             var refCashOutKey = $(this).closest(".transaction-detail-row").find("[name='hidRefCashOutKey[]']").val();  
              
             if ($(this).val() == beforeSupplierKey && employeeKey == 0 && refCashOutKey == 0){ 
                 $(this).val(supplierKey); 
                 $(this).closest("div").find("[name='supplierDetailName[]']").val(supplierName);
             }
          })
            
          $("#" + tabID + " [name=hidBeforeSupplierKey]").val(supplierKey);
            
        }
        
         
        /* this.updateSupplierDetail = function updateSupplierDetail(){
            var supplierkey = $( "#" + tabID + " [name=hidSupplierKey]" ).val();
             
             $.ajax({
                type: "GET",
                url:  "ajax-supplier.php",
                async: false,
                data: "action=getDataRowById&pkey=" + supplierkey,  
            }).done(function( data ) { 
                data = JSON.parse(data) ;  
                if (data.length > 0){   
                    data = data[0];  
                    $("#" + tabID + " [name=\"hidCostKey[]\"]").each(function() {   
                    $(this).closest(".transaction-detail-row").find("[name=\"hidSupplierDetailKey[]\"]").val(data.pkey);
                    $(this).closest(".transaction-detail-row").find("[name=\"supplierDetailName[]\"]").val(data.name);
                 });
                } 
                   
            }); 
             
        }*/
         
        this.updateCostSupplier = function updateCostSupplier(){
            $("#" + tabID + " [name=outsourceCost]").val(0);
         
            var purchasekey = $( "#" + tabID + " [name=hidPurchaseOrderKey]" ).val();
            var itemkey = $( "#" + tabID + " [name=hidItemKey]" ).val();
         
            $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-purchase-order.php',
                    async: false,
                    data: "action=getItemPrice&pkey=" + purchasekey + "&itemkey=" + itemkey ,  
                }).done(function( data ) {  
                        data = JSON.parse(data) ;   
                        $("#" + tabID + " [name=outsourceCost]").val(data).blur(); 
                });   
         
        }
        
         this.updateContainerChange = function updateContainerChange(){
             
             var detailkey = $("#" + tabID + " [name=hidSODetailKey]").val();
             
             $("#" + tabID + " [name=trDateStuffing]").val(arrDetails[detailkey].stuffingdatetime); 
             $("#" + tabID + " [name=hidItemKey]").val(arrDetails[detailkey].itemkey); 
          
             //truckingServiceWorkOrder.updateCostSupplier();
             truckingServiceWorkOrder.updateCost();
         }
         
         this.updateCost = function updateCost(){  
             
             var itemkey = $("#" + tabID + " [name=hidItemKey]").val();
             var pkey = $("#" + tabID + " [name=hidSOKey]").val();
             var jobtypekey = $("#" + tabID + " [name=selJobType]").val();
             
             //update cost 
             $.ajax({
                type: "GET",
                url:  "ajax-trucking-service-order.php",
                async: true,
                data: "action=getCost&pkey=" + pkey + "&jobtypekey="+jobtypekey+"&itemkey=" + itemkey ,  
            }).done(function( data ) {  
                 data = JSON.parse(data) ; 
                 $("#" + tabID + " [name=\"hidCostKey[]\"]").each(function() {   

                     // gk boleh return, kalo gk, gk kereset jd 0
                     //  if (data[$(this).val()] == 0)   return true; 
                     
                     $(this).closest(".transaction-detail-row").find("[name=\"requestAmount[]\"]").val(data[$(this).val()]).blur();
                 })  
            }); 
             
             // update komisi 
             $.ajax({
                type: "GET",
                url:  "ajax-trucking-service-order.php",
                async: true,
                data: "action=getDriverCommission&pkey=" + pkey + "&jobtypekey="+jobtypekey+"&itemkey=" + itemkey ,  
            }).done(function( data ) {  
                 data = JSON.parse(data) ; 
                 $("#" + tabID + " [name=\"driverCommission\"]").val(data[-1]).blur();
                 $("#" + tabID + " [name=\"codriverCommission\"]").val(data[-2]).blur(); 
            }); 
             
         }
         
         this.showOutsource = function showOutsource(obj, revalidate){  
             
             if (revalidate == undefined)
                 revalidate = true;
              
             if ($(obj).val() == 1){ 
                 $("#" + tabID + " .inhouse").hide();
                 $("#" + tabID + " .outsource").show();
             }else{ 
                 $("#" + tabID + " .inhouse").show();
                 $("#" + tabID + " .outsource").hide();
             }
             
             if (revalidate){
                 
                <?php if (!empty($rs) && $rs[0]['statuskey'] == 2) { ?>  
                  //   $(obj).closest('form').bootstrapValidator('revalidateField', 'policeNumber'); 
                   //  $(obj).closest('form').bootstrapValidator('revalidateField', 'driverName'); 
                <?php  } ?>  

                //  $(obj).closest('form').bootstrapValidator('revalidateField', 'supplierName');
             }
              
             // enabled field kalo ad akses
             
             // hanya jika masi dalam status boleh edit
             <?php if ((!isset( $rs[0]['statuskey']) || in_array( $rs[0]['statuskey'], $obj->allowedStatusForEdit)) && $hasCostAccess) { ?>   
                 $("#" + tabID + " .fixed-cost-list").each(function() {
                     $(this).prop("readonly", !$(obj).prop("checked"));
                 }) 
             <?php } ?>
             
         }


        this.calculateCargoCost = function calculateCargoCost($row) {
            var totalCost = 0;
            var totalSellingCost = 0;

            var arrCostDetailField = truckingServiceWorkOrder.costDetailField();

            for (var i = 0; i < arrCostDetailField.length; i++) {
                var qty = unformatCurrency($row.find("[name=\"qtyDetailCargo[]\"]").val()) || 0;
				 
                var cost = unformatCurrency($row.find("[name=\"" + arrCostDetailField[i][0] + "\"]").val()) || 0; 
                var sellingCost = unformatCurrency($row.find("[name=\"" + arrCostDetailField[i][1] + "\"]").val()) || 0; 
                var isMultipliedQty = parseInt($row.find("[name=\"" + arrCostDetailField[i][2] + "\"]").val());
                 
                qty = parseFloat(qty);
                cost = parseFloat(cost);
                sellingCost = parseFloat(sellingCost);
				if (isMultipliedQty == 1) {
					totalCost += qty * cost;
					totalSellingCost += qty * sellingCost;
				} else {
					totalCost += cost;
					totalSellingCost += sellingCost;
				}
            }

            $row.find("[name=\"amountCargo[]\"]").val(totalCost).blur();
            $row.find("[name=\"sellingAmountCargo[]\"]").val(totalSellingCost).blur();

        };
        
        this.updateOutsourceAP = function updateOutsourceAP(){
             var outsourceCostObj = $("#"+tabID+" [name=outsourceCost]");
             var outsourceDownpaymentObj = $("#"+tabID+" [name=outsourceDownpayment]"); 
             var outsourceCost = parseInt(unformatCurrency(outsourceCostObj.val()));
             var outsourceDownpayment = parseInt(unformatCurrency(outsourceDownpaymentObj.val()));
             var includeTax =   $("#" + tabID + " [name='chkIncludeTax']").val();
             var taxPercentage =  parseFloat(unformatCurrency($("#" + tabID + " [name='taxPercentage']").val())) || 0 ; 
              
             /*if (outsourceDownpayment > outsourceCost){ 
                 outsourceDownpayment = outsourceCost;
                 $("#"+tabID+" [name=outsourceDownpayment]").val(outsourceDownpayment).blur();
             }*/
             
             var subtotal = outsourceCost;
             var taxValue = 0;
             if (includeTax == 0) {
                taxValue = subtotal * taxPercentage / 100;
                subtotal += taxValue;
             }else{
                taxValue = (taxPercentage/(100 + taxPercentage)) * subtotal; 
                //subtotal -= taxValue; 
             }
             
             $("#" + tabID + " [name='taxValue']").val(taxValue).blur();
             $("#" + tabID + " [name='total']").val(subtotal).blur();
                 
             var outsourceAP = subtotal - outsourceDownpayment;
              $("#"+tabID+" [name=outsourceAP]").val(outsourceAP).blur();
             
         }


         
    }
    
    
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        
        var varConstant = { 
                            arrTruckingCost : <?php echo json_encode($rsTruckingCost); ?>
                            };
        
        truckingServiceWorkOrder = new TruckingServiceWorkOrder(tabID, varConstant);
        setOnDocumentReady(tabID);    
       
		 $('#defaultForm-' + tabID )
			.bootstrapValidator({ 
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                code: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.code[1]
                        }, 
                    }
				},
                 
                trDate: { 
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.date[1]
                        }, 
                    }
				},
                 
                
            <?php if (!empty($rs) && $rs[0]['statuskey'] == 2) { ?>  
			 /*  driverName: {   
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.driver[1]
                        }
                    } 
                },
              
			   policeNumber: {  
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.car[1]
                        }
                    } 
                },
              */ 
            <?php } ?> 
                
			   soNumber: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.jobOrder[1]
                        }
                    } 
                },
                /*
			   supplierName: {  
                    trigger: 'change',
                    validators: { 
                        callback: {
                            message: phpErrorMsg.supplier[1],
                            callback: function(value, validator, $field) { 
                                
                                if ($("#" + tabID + " [name=chkIsOutsource]").is(':checked')){ 
                                    
                                     var supplierkey = $("#" + tabID + " [name=hidSupplierKey]").val();
                                     console.log("value " + value);
                                     console.log("value length " + value.length);
                                     console.log("supplierkey " + supplierkey); 
                                    
                                    if (!supplierkey || supplierkey == 0 || !value || value.length == 0)
                                        return false;
                                  
                                    
                                    return true;
                                 
                                }  
                                 
                                return true;
                            }
                        } 
                    }
                   
                },*/
                 
            }
        })
        .on('success.form.bv', function(e) { 
              <?php echo $obj->submitFormScript(); ?> 
        }); 
          
        truckingServiceWorkOrder.showOutsource("#" + tabID + " [name=chkIsOutsource]",false); 

        var arrCostDetailField = truckingServiceWorkOrder.costDetailField();


        $("#"+tabID+" [name=btnUpdateCost]").on('click', function() {
            truckingServiceWorkOrder.updateCost();
        }); 
            


     
        $("#"+tabID+" [name=outsourceCost],  #"+tabID+" [name=outsourceDownpayment],  #"+tabID+" [name=chkIncludeTax],  #"+tabID+" [name=taxPercentage],  #"+tabID+" [name=taxValue],  #"+tabID+" [name=total]").on('change', function() {
            truckingServiceWorkOrder.updateOutsourceAP();
        }); 
        for (var i = 0; i < arrCostDetailField .length; i++) {
            $("#" + tabID).on('change', "[name=\"" + arrCostDetailField[i][0] + "\"],[name=\"" + arrCostDetailField[i][1] + "\"]", function() {
                var $row = $(this).closest(".transaction-detail-row");
                truckingServiceWorkOrder.calculateCargoCost($row);
            });
        }

       $("#" + tabID).on('change', "[name='qtyDetailCargo[]']", function() {
            var $row = $(this).closest(".transaction-detail-row");
            truckingServiceWorkOrder.calculateCargoCost($row);
        });
        
        objAndValue = new Array;
		objAndValue.push({object:'hidCostKey[]', value :'pkey'});  
        objAndValueForDetailAutoComplete[tabID] = objAndValue;  
        
        objAndValue = new Array;
		objAndValue.push({object:'hidSupplierDetailKey[]', value :'pkey'});    
        objAndValueForSupplierDetailAutoComplete[tabID] = objAndValue;
        
        objAndValue = new Array;
		objAndValue.push({object:'hidEmployeeDetailKey[]', value :'pkey'});    
        objAndValueForEmployeeDetailAutoComplete[tabID] = objAndValue;
        
        objAndValue = new Array;
		objAndValue.push({object:'hidDestinationDetailKey[]', value :'pkey'});    
        objAndValueForDestinationDetailAutoComplete[tabID] = objAndValue;
	     
		// DETAIL CLONE
		 $("#"+tabID+" [name=btnAddRows]").on('click', function() {
          	var newRow = addNewTemplateRow("cost-row-template"); 
            bindAutoCompleteForTransactionDetail('costName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1&moduleCost=trucking');   
            bindAutoCompleteForTransactionDetail('supplierDetailName[]',objAndValueForSupplierDetailAutoComplete[tabID],'ajax-supplier.php?action=searchData');   
            bindAutoCompleteForTransactionDetail('employeeDetailName[]',objAndValueForEmployeeDetailAutoComplete[tabID],'ajax-employee.php?action=searchData');   
              
        }); 

        $("#"+tabID+" [name=btnAddCargoRow]").on('click', function() { 
            var newRow = addNewTemplateRow("detail-row-template"); 
            bindAutoCompleteForTransactionDetail('destinationDetailName[]',objAndValueForDestinationDetailAutoComplete[tabID],'ajax-location.php?action=searchData'); 
        });
        
        <?php if (empty($rsCost)){ ?> 
            var newRow = addNewTemplateRow("cost-row-template");  
        <?php }  ?>

        <?php if (empty($rsCargoDetail)){ ?> 
            var newRow = addNewTemplateRow("detail-row-template");  
        <?php }  ?>
        

         bindAutoCompleteForTransactionDetail('costName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1&moduleCost=trucking');   
         bindAutoCompleteForTransactionDetail('supplierDetailName[]',objAndValueForSupplierDetailAutoComplete[tabID],'ajax-supplier.php?action=searchData');  
         bindAutoCompleteForTransactionDetail('employeeDetailName[]',objAndValueForEmployeeDetailAutoComplete[tabID],'ajax-employee.php?action=searchData');   
         bindAutoCompleteForTransactionDetail('destinationDetailName[]',objAndValueForDestinationDetailAutoComplete[tabID],'ajax-location.php?action=searchData');   
       
});
	 
     

</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>

  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
      
        <?php prepareOnLoadDataForm($obj); ?>   
        <?php echo $obj->inputHidden('hidItemKey'); ?>
       
       <div class="div-table main-tab-table-2">
                <div class="div-table-row">
                    <div class="div-table-col"> 
      						 <div class="div-tab-panel"> 
                                   <div class="div-table-caption border-orange"><?php echo $obj->lang['generalInformation']; ?></div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['status']; ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputSelect('selStatus', $arrStatus, array('disabled' => true)); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['code']; ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputAutoCode('code'); ?>
                                        </div> 
                                    </div>    
                                    <div class="form-group" >
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['date']; ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputDate('trDate', array('allowedStatusForEdit' => array (1))); ?>   
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                     			    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('allowedStatusForEdit' => array(1)) ); ?>  
                                        </div> 
                                    </div>
                                    <div class="form-group" >
                                        <label class="col-xs-3 control-label" ><?php echo $obj->lang['stuffingAndDestuffingDateTime']; ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputDateTime('trDateStuffing', array('allowedStatusForEdit' => array (1))); ?>  
                                        </div> 
                                    </div>

                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['planner']; ?></label>  
                                        <div class="col-xs-9"> 
                                         <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $employee,
                                                                                'element' => array('value' => 'plannerName',
                                                                                                   'key' => 'hidPlannerKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-employee.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) , 
                                                                                'popupForm' => array(
                                                                                                        'url' => 'employeeForm.php',
                                                                                                        'element' => array('value' => 'plannerName',
                                                                                                               'key' => 'hidPlannerKey'),
                                                                                                        'width' => '1000px',
                                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['employee'])
                                                                                                    )
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div> 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"></label> 
                                        <div class="col-xs-9"></div> 
                                    </div>  
                                 
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['jobOrder']; ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php     
                                                   echo $obj->inputAutoComplete(array(
                                                                                            'objRefer' => $truckingServiceOrder,
                                                                                            'revalidateField' => true, 
                                                                                            'element' => array('value' => 'soNumber',
                                                                                                               'key' => 'hidSOKey'),
                                                                                            'source' => array(
                                                                                                                'url' => 'ajax-trucking-service-order.php',
                                                                                                                'data' => array(  'action' =>'searchData', 'statuskey' => '(2)' )
                                                                                                            ) , 
                                                                                            'allowedStatusForEdit' => array (1),
                                                                                            'callbackFunction' => 'truckingServiceWorkOrder.updateSOInformationHeader()'
                                                                                          )
                                                                                    );  
                                                 
                                                       
                                                ?> 
                                        </div> 
                                    </div>    
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['typeOfJob']; ?></label> 
                                        <div class="col-xs-3" style="padding-right:0"> 
                                            <?php echo $obj->inputText('cargoTypeName',array('readonly' => true)); ?> 
                                        </div> 
                                         <div class="col-xs-6"> 
                                         <?php echo $obj->inputText('categoryName',array('readonly' => true)); ?> 
                                         <?php echo $obj->inputHidden('hidCategoryKey'); ?> 
                                        </div> 
                                    </div>
                                  
                                    <div class="form-group" >
                                        <label class="col-xs-3 control-label"></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selJobType',$arrJobType, array( 'allowedStatusForEdit' => array (1), 'etc' => ' onChange="truckingServiceWorkOrder.updateCost()"')); ?> 
                                        </div> 
                                    </div>   
                                 
                                    <div style="clear:both; height:2em"></div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['services']; ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php  echo  $obj->inputSelect('hidSODetailKey', $arrContainer,  array( 'allowedStatusForEdit' => array (1), 'etc' => ' onChange="truckingServiceWorkOrder.updateContainerChange()"')); ?> 
                                        </div> 
                                    </div>
  
                                    <!-- <div class="form-group" style="margin-bottom:5px"> 
                                        <div class="col-xs-3"></div> 
                                        <div class="col-xs-5"><?php echo $obj->lang['containerNumber']; ?></div>  
                                        <div class="col-xs-4" style="padding-left:5px;"><?php echo $obj->lang['sealNumber']; ?></div>  
                                    </div>  
                                    <div class="form-group"> 
                                        <div class="col-xs-3"></div> 
                                        <div class="col-xs-5" style="padding-right:5px;"> 
                                                <?php echo $obj->inputText('containerNumber');  ?>
                                        </div>  
                                        <div class="col-xs-4" style="padding-left:5px;"> 
                                                 <?php echo $obj->inputText('sealNumber');  ?>
                                        </div>  
                                    </div>  
                                    <div class="form-group"> 
                                        <div class="col-xs-3"></div> 
                                        <div class="col-xs-5" style="padding-right:5px;"> 
                                                <?php echo $obj->inputText('container2Number');  ?>
                                        </div>  
                                        <div class="col-xs-4" style="padding-left:5px;"> 
                                                <?php echo $obj->inputText('seal2Number');  ?>
                                        </div>  
                                    </div>        -->
                                    <!-- <div style="clear:both; height:2em"></div>  -->
                                    <div class="form-group" >
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['outsource']; ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputCheckBox('chkIsOutsource', array('allowedStatusForEdit' => array(1,2), 'etc' => 'onChange="truckingServiceWorkOrder.showOutsource(this)"')); ?>   
                                        </div> 
                                    </div> 
                                   
                                    <div class="form-group inhouse">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['driver']; ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php                
                                                    echo $obj->inputHidden('hidBeforeDriverKey');
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'objRefer'=>$employee,
                                                                                        'revalidateField' => false, 
                                                                                        'element' => array('value' => 'driverName',
                                                                                                           'key' => 'hidDriverKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-employee.php',
                                                                                                            'data' => array(  'action' =>'searchData' , 
                                                                                                                              'isdriver' => 1 )
                                                                                                        ) ,
                                                                                        'allowedStatusForEdit' => array(1,2),
                                                                                        'popupForm' => array(
                                                                                                'url' => 'employeeForm.php',
                                                                                                'element' => array('value' => 'driverName',
                                                                                                       'key' => 'hidDriverKey'),
                                                                                                'width' => '1000px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['employee'])
                                                                                            ) ,
                                                                                        'callbackFunction' => 'truckingServiceWorkOrder.updateCostEmployeeKey()'
                                                                                      )
                                                                                );  
                                            ?> 
                                        </div> 
                                    </div>  

                                    <!-- <div class="form-group inhouse">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['driverCommission']; ?></label> 
                                        <div class="col-xs-9">  
                                                <?php echo $obj->inputNumber('driverCommission' , array('readonly' => (!$hasCostAccess) ? true : false ) );?> 
                                        </div> 
                                    </div>  -->

                                    <!-- <div class="form-group inhouse">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['codriver']; ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php                                 
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'objRefer'=>$employee,
                                                                                        'revalidateField' => false, 
                                                                                        'element' => array('value' => 'coDriverName',
                                                                                                           'key' => 'hidCoDriverKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-employee.php',
                                                                                                            'data' => array(  'action' =>'searchData' , 
                                                                                                                              'isdriver' => 1 )
                                                                                                        ) ,
                                                                                        'allowedStatusForEdit' => array(1,2),
                                                                                        'popupForm' => array(
                                                                                                'url' => 'employeeForm.php',
                                                                                                'element' => array('value' => 'coDriverName',
                                                                                                       'key' => 'hidCoDriverKey'),
                                                                                                'width' => '1000px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['employee'])
                                                                                            ) 
                                                                                      )
                                                                                );  
                                            ?> 
                                        </div> 
                                    </div> -->

                                    <!-- <div class="form-group inhouse">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['codriverCommission']; ?></label> 
                                        <div class="col-xs-9">  
                                                  <?php echo $obj->inputNumber('codriverCommission', array('readonly' => (!$hasCostAccess) ? true : false ));?> 
                                        </div> 
                                    </div>  -->
                                <div class="form-group inhouse">                                    
				                <label class="col-xs-3 control-label"><?php echo $obj->lang['car']; ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $car,
                                                                                    'revalidateField' => false, 
                                                                                    'element' => array('value' => 'policeNumber',
                                                                                                       'key' => 'hidCarKey', 
                                                                                                      ),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-car.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'searchField' => 'code,policenumber')
                                                                                                    ) ,
                                                                                    'allowedStatusForEdit' => array(1,2),
                                                                                    'popupForm' => array(
                                                                                                        'url' => 'carForm.php',
                                                                                                        'element' => array( 'value' => 'policeNumber', 'valueDBField' => 'codepolicenumber',
                                                                                                                            'key' => 'hidCarKey'),
                                                                                                        'width' => '1000px',
                                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['car'])
                                                                                                    )  
                                                                                  )
                                                                            );  
                                        ?> 
                                    </div> 
                                </div>  
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['category']); ?></label> 
                                        <div class="col-xs-9"> 
                                             <?php  
                                                        $popupOpt = (!$isQuickAdd) ? array(
                                                                'url' => 'carCategoryForm.php',
                                                                'element' =>  array('value' => 'categoryName', 'key' => 'hidCategoryKey') ,
                                                                'width' => '600px',
                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['carCategory'])
                                                            )  : ''; 
                                            
                                                        echo $obj->inputAutoComplete(array(  
                                                                                'objRefer' => $carCategory,
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'categoryName',
                                                                                                   'key' => 'hidCategoryKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-car-category.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'popupForm' => $popupOpt
                                                                              )
                                                                        );  
                                            ?>
                                        </div> 
                                    </div> 

                                <div class="form-group inhouse">                                    
				                <label class="col-xs-3 control-label"><?php echo $obj->lang['replacementCar']; ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $car,
                                                                                    'revalidateField' => false, 
                                                                                    'element' => array('value' => 'replacementPoliceNumber',
                                                                                                        'key' => 'hidReplacementCarKey', 
                                                                                                    ),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-car.php',
                                                                                                        'data' => array(  'action' =>'searchData', 'searchField' => 'code,policenumber')
                                                                                                    ),
                                                                                    'allowedStatusForEdit' => array(1,2),
                                                                                    'popupForm' => array(
                                                                                                        'url' => 'carForm.php',
                                                                                                        'element' => array( 'value' => 'policeNumber', 'valueDBField' => 'codepolicenumber',
                                                                                                                            'key' => 'hidCarKey'),
                                                                                                        'width' => '1000px',
                                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['car'])
                                                                                                    )  
                                                                                  )
                                                                            );  
                                        ?> 
                                    </div> 
                                </div>   
                                <!-- <div class="form-group inhouse" >
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['chassis']; ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                    'objRefer' => $chassis,
                                                                                    'revalidateField' => false, 
                                                                                    'element' => array('value' => 'chassisNumber',
                                                                                                       'key' => 'hidChassisKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-chassis.php',
                                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                                    ) ,
                                                                                    'allowedStatusForEdit' => array(1,2),
                                                                                    'popupForm' => array(
                                                                                                        'url' => 'chassisForm.php',
                                                                                                        'element' => array('value' => 'chassisNumber', 'valueDBField' => 'chassisnumber',
                                                                                                               'key' => 'hidChassisKey'),
                                                                                                        'width' => '600px',
                                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['chassis'])
                                                                                                    )  
                                                                                  )
                                                                            );  
                                        ?> 
                                    </div> 
                                </div>    -->
                                <div class="form-group outsource"> 
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['supplier']; ?></label> 
                                    <div class="col-xs-9"> 
                                        <?php    
                                                echo $obj->inputHidden('hidBeforeSupplierKey');
                                                echo $obj->inputAutoComplete(array( 
                                                                                    'objRefer' => $supplier,
                                                                                    'revalidateField' => false, 
                                                                                    'element' => array('value' => 'supplierName',
                                                                                                       'key' => 'hidSupplierKey'),
                                                                                    'source' =>array(
                                                                                                        'url' => 'ajax-supplier.php',
                                                                                                        'data' => array(  'action' =>'searchData')
                                                                                                    ) ,
                                                                                    'allowedStatusForEdit' => array(1,2),
                                                                                    'callbackFunction' => 'truckingServiceWorkOrder.updateSupplierDetail()',
                                                                                    'popupForm' => array(
                                                                                                        'url' => 'supplierForm.php',
                                                                                                        'element' => array('value' => 'supplierName',
                                                                                                               'key' => 'hidSupplierKey'),
                                                                                                        'width' => '1000px',
                                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['supplier'])
                                                                                                    ) ,
                                                                                    'callbackFunction' => 'truckingServiceWorkOrder.updateCostSupplierKey()'
                                                                                  )
                                                                            );  
                                        ?> 
                                    </div> 
                                </div>   
                                
                                <div class="form-group outsource">
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['car']; ?></label> 
                                    <div class="col-xs-9"> 
                                         <?php echo $obj->inputText('outsourceCarRegistrationNumber',array('allowedStatusForEdit' => array(1,2))); ?> 
                                    </div> 
                                </div>   
                                <div class="form-group outsource">
                                    <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['carCategory']); ?></label> 
                                    <div class="col-xs-9"> 
                                            <?php   
                                            
                                                        echo $obj->inputAutoComplete(array(   
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'outsourceCarCategoryName',
                                                                                                   'key' => 'hidOutsourceCarCategoryKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-car-category.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) 
                                                                              )
                                                                        );  
                                            ?>
                                    </div> 
                                </div> 
                                <div class="form-group outsource">
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['truckingFee']; ?></label> 
                                    <div class="col-xs-9"> 
                                         <?php echo $obj->inputNumber('outsourceCost',array('allowedStatusForEdit' => array(1,2))); ?>  
                                    </div>  
                                </div> 
                                
                                <div class="form-group outsource">
                                    <label class="col-xs-3 control-label"><?php echo strtoupper($obj->lang['PPN']); ?> [Include]</label> 
                                    <div class="col-xs-9"> 
                                        <div class="flex">
                                            <div ><?php echo $obj->inputCheckBox('chkIncludeTax'); ?></div>
                                            <div style="width:80px"><?php echo $obj->inputDecimal('taxPercentage'); ?></div>
                                            <div style="text-align:center;width:20px">%</div>
                                            <div class="consume" style="width:270px"><?php echo $obj->inputNumber('taxValue', array('readonly' => true)); ?></div>
                                        </div>
                                    </div>  
                                </div>
                                 
                                <div class="form-group outsource">
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['total']; ?></label> 
                                    <div class="col-xs-9"> 
                                         <?php echo $obj->inputNumber('total', array( 'disabled' => true)); ?>  
                                    </div>  
                                </div> 
                                
                                <div class="form-group outsource">
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['downpayment']; ?></label> 
                                    <div class="col-xs-9"> 
                                        <div class="flex">
                                            <div class="consume"> 
                                               <?php echo $obj->inputNumber('outsourceDownpayment',array('readonly' =>  ($cashOutDownpaymentKey) ? true : false, 'allowedStatusForEdit' => array(1,2))); ?>  
                                            </div>
                                            <?php if ($cashOutDownpaymentKey) {?>
                                                <div><i class="cashed-out-icon fas fa-hand-holding-usd" title="<?php echo $cashOutDownpaymentCode; ?>"></i></div>
                                            <?php } ?>
                                        </div> 
                                    </div>  
                                </div>  
                                <div class="form-group outsource">
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['accountsPayableBalance']; ?></label> 
                                    <div class="col-xs-9"> 
                                         <?php echo $obj->inputNumber('outsourceAP',array('readonly' => 'true')); ?>  
                                    </div>  
                                </div> 
                                 <div class="form-group outsource"> 
                                    <label class="col-xs-3 control-label" style="padding-top:0"><?php echo $obj->lang['recipient'] . '<br>('.$obj->lang['cashOut'].')'; ?></label> 
                                    <div class="col-xs-9"> 
                                        <div class="flex">
                                            <div class="consume"> 
                                                <?php     
                                                        echo $obj->inputAutoComplete(array( 
                                                                                            'objRefer' => $employee,
                                                                                            'readonly' =>  ($cashOutDownpaymentKey) ? true : false,
                                                                                            'revalidateField' => false, 
                                                                                            'element' => array('value' => 'downpaymentRecipientName',
                                                                                                               'key' => 'hidDownpaymentRecipientKey'),
                                                                                            'source' =>array(
                                                                                                                'url' => 'ajax-employee.php',
                                                                                                                'data' => array(  'action' =>'searchData')
                                                                                                            ) ,
                                                                                            'allowedStatusForEdit' => array(1,2)
                                                                                          )
                                                                                    );  
                                                ?> 
                                            </div> 
                                        </div>
                                        <div class="asterix-label" style="font-size:0.9em; margin-top:0.5em">Uang muka hanya diisi jika diberikan melalui modul <b>Kas Keluar Biaya Trucking</b>. Jika tidak, silahkan menggunakan modul <b>Uang Muka Pemasok</b></div>
                                    </div> 
                                </div>
                                 
                             </div> 
                             
                    </div>
                    <div class="div-table-col">   
                         
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-green"><?php echo $obj->lang['customerInformation']; ?></div>
                            
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['customer']; ?></label> 
                               <div class="col-xs-9"> <?php echo $obj->inputText('customerName',array('readonly' => true)); ?></div>
                            </div>   
                            <!-- <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['consignee']; ?></label> 
                                <div class="col-xs-9"> <?php echo $obj->inputText('consigneeName',array( 'readonly' => true)); ?></div> 
                            </div> -->
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['consignee']); ?></label> 
                                <div class="col-xs-9">  
                                   <?php
                                                    echo $obj->inputAutoComplete(array(
                                                                            'objRefer' => $consignee,
                                                                            'revalidateField' => false, 
                                                                            'element' => array('value' => 'consigneeName',
                                                                                                'key' => 'hidConsigneeKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-consignee.php',
                                                                                                'data' => array(  'action' =>'searchData', 'limit' => 25 )
                                                                                            ) ,
                                                                            // 'readonly' => !$overwriteContractAllowed,
                                                                            'allowedStatusForEdit' => array (1),
                                                                        )
                                                                    );  
                                    ?> 
                                </div> 
                            </div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo strtoupper($obj->lang['si']); ?></label>
                               <div class="col-xs-9"> <?php echo $obj->inputText('doNumber',array('readonly' => true)); ?></div> 
                            </div>   
                            <!-- <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['bookingNumber']; ?></label> 
                                <div class="col-xs-9"> <?php echo $obj->inputText('shipmentNumber',array('readonly' => true)); ?></div> 
                            </div> -->
                            
                        </div> 
                         
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo $obj->lang['stuffingDestuffingInformation']; ?></div>
                            
                            <!-- <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['depot']; ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('depotName',array('readonly' => true)); ?> 
                                    <?php echo $obj->inputHidden('hidDepotKey'); ?>   
                                </div> 
                            </div>   -->

                            <!-- <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['terminal']; ?></label> 
                                <div class="col-xs-9"> 
                                   <?php      
                                        echo $obj->inputAutoComplete(array( 
                                                                            'objRefer' => $terminal,
                                                                            'readonly' => true,
                                                                            'element' => array('value' => 'terminalName',
                                                                                               'key' => 'hidTerminalKey'),
                                                                            'source' =>array(
                                                                                                'url' => 'ajax-terminal.php',
                                                                                                'data' => array(  'action' =>'searchData' )
                                                                                           ) ,
                                                                            'popupForm' => array(
                                                                                                'url' => 'portForm.php',
                                                                                                'element' => array('value' => 'terminalName',
                                                                                                       'key' => 'hidTerminalKey'),
                                                                                                'width' => '600px',
                                                                                                'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['terminal'])
                                                                                            )  
                                                                          )
                                                                    );  
                                ?> 
                                </div> 
                            </div> -->

                            <!-- <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['warehouse']; ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('warehouseName',array( 'readonly' => true)); ?>  
                                </div> 
                            </div>  -->
                            <!-- <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['location']; ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('locationName',array( 'readonly' => true)); ?>   
                                </div> 
                            </div>   -->
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['contactPerson']; ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('contactPerson',array( 'readonly' => false)); ?>    
                                </div> 
                            </div> 

                            <!-- <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['address']; ?></label> 
                                <div class="col-xs-9">     
                                   <?php echo  $obj->inputTextArea('stuffingAddress', array('readonly' => true, 'etc' => 'style="height:8em;"')); ?>
                                </div> 
                            </div>  -->
                             
                               <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['route']; ?></label> 
                                <div class="col-xs-9" > 
                                    <div class="flex">
                                     <div class="consume"><?php echo $obj->inputText('routeFrom',array( 'allowedStatusForEdit' => array (1))); ?></div>
                                     <div>-</div>
                                     <div class="consume"><?php echo $obj->inputText('routeTo',array( 'allowedStatusForEdit' => array (1))); ?></div>
                                    </div>
                                </div>   
                            </div>  
                        </div>
						<!-- <div class="div-tab-panel"> -->
                            <!-- <div class="div-table-caption border-red"><?php echo $obj->lang['cargoDetail']; ?></div>  -->
							
                            
                                <!-- <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['quantity'];?></label> 
                                	<div class="col-xs-9"> 
										<div class="flex">
											<div style="width: 7em"> <?php echo $obj->inputNumber('cargoQty',array('etc' => 'style="text-align:right"')); ?>  </div>
											<div  class="consume"> <?php echo $obj->inputSelect('cargoQtyUnit',$arrUnit ); ?>  </div>
											<div style="margin-left: 2em; margin-right:0.5em"> <?php echo $obj->lang['weight']; ?>  </div>
											<div  style="width: 7em"> <?php echo $obj->inputNumber('cargoWeight',array('etc' => 'mnv-attr-decimal="2" style="text-align:right"')); ?>  </div>
											<div  class="consume"> <?php echo $obj->inputSelect('cargoWeightUnit',$arrWeight); ?>  </div>
										</div> 
                                    </div> 
                                </div> 
                                <div class="form-group">
                                    <label class="col-xs-3 control-label"><?php echo $obj->lang['description']; ?></label> 
                                        <div class="col-xs-9"> 
                                        <?php echo $obj->inputTextArea('productDescription', array( 'etc' => 'style="height:8em;"')); ?>
                                    </div> 
                                </div>  -->
                        <!-- </div> -->
                         <div class="div-tab-panel">
                             <div class="div-table-caption border-purple"><?php echo $obj->lang['note']; ?></div> 
                             <div class="form-group">
                                <div class="col-xs-12"> 
                                       <?php echo  $obj->inputTextArea('trDesc', array( 'etc' => 'style="height:8em;"')); ?>
                                </div> 
                            </div> 
                         </div>
                         <?php if (!empty($rsProgressStep)) { ?> 
                            <div class="div-tab-panel"> 
                                <div class="div-table-caption border-purple"><?php echo $obj->lang['progressInformation']; ?></div> 
                                    <div class="div-table">
                                       <?php for($i=0;$i<count($rsProgressStep);$i++) {
                                            $icon = '';
                                            if (in_array($rsProgressStep[$i]['pkey'],$arrProgress))
                                                $icon = '<i class="fas fa-check green-check-icon"></i>';

                                            echo '<div class="div-table-row"><div class="div-table-col" style="width: 3em; text-align:center">'.$icon.'</div><div class="div-table-col">' .$rsProgressStep[$i]['name'].'</div></div>';

                                        }?>
                                    </div>
                              </div>
                         <?php } ?> 
                         
<!-- ini file bawaan dari JO, bukan upload manual
                         <div class="div-tab-panel"> 
                               <div class="div-table-caption border-black"><?php echo $obj->lang['files']; ?></div> 
                                <div class="item-file-uploader user-select-none">
                                    <ul class="file-list">
                                        <?php for($i=0;$i<count($rsItemFile);$i++) {
                                            echo '<li><div class="panel"><div class="file-uploader-description"><a href="/download.php?filename=trucking-service-order/'.$rs[0]['refkey'].'/'.$rsItemFile[$i]['file'].'" target="_blank" title="'.$rsItemFile[$i]['file'].'">'.$rsItemFile[$i]['file'].'</a></div></div></li>';
                                        } ?>    
                                    </ul>
                                </div>
                        </div>   
-->
 	               </div>    
                </div>
      </div> 
    
       <div class="div-tab-panel overflow-scroll-panel" > 
      <div class="div-table mnv-transaction transaction-detail" style="width:1500px; border-bottom:1px solid #333; ">
        <div class="div-table-row"> 
   <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['destination']); ?></div>
            <div class="div-table-col detail-col-header" style="width:150px;"><?php echo ucwords($obj->lang['deliveryNotes']); ?></div>
            <div class="div-table-col detail-col-header" style="width:90px;text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
            <div class="div-table-col detail-col-header" style="width:140px;"><?php echo ucwords($obj->lang['unit']); ?></div>
            <?php for ($i = 0; $i < count($rsTruckingCost); $i++) { ?>
            <div class="div-table-col detail-col-header" relheaderkey="<?php echo $rsTruckingCost[$i]['pkey'] ?>" style="width: 130px; text-align:right"><?php echo ucwords($rsTruckingCost[$i]['name']); ?></div>
            <?php } ?>
            <div class="div-table-col detail-col-header" style="width:150px;text-align:right;"><?php echo ucwords($obj->lang['total']); ?></div>
            <div class="div-table-col detail-col-header icon-col"></div>
        </div>

        <?php 
		
        $totalCargoDetail = count($rsCargoDetail);
        
        $arrCargoKey = array_column($rsCargoDetail, 'pkey');
        $arrCostKey = array_column($rsTruckingCost, 'pkey');
        $rsCostCargo = $obj->getCargoCostDetail($arrCargoKey, '', $arrCostKey);

        for ($i = 0; $i <= $totalCargoDetail; $i++) {

            $class = 'transaction-detail-row';
            $overwrite = true;
            $disabled = false;
            $etc = '';

            if ($i == $totalCargoDetail ){
				$class = 'detail-row-template';
				$style = 'style="display:none"';
                $overwrite = false;
                $etc = 'disabled="disabled"';
            } else {

//                $_POST['hidCostDetailCargoKey[]'] = $rsCargoDetail[$i]['pkey'];
                $_POST['hidCargoDetailKey[]'] = $rsCargoDetail[$i]['pkey'];
                $_POST['destinationCargo[]'] = $rsCargoDetail[$i]['destination'];
                $_POST['workOrderCargo[]'] = $rsCargoDetail[$i]['workorder'];
                $_POST['qtyDetailCargo[]'] = $obj->formatNumber($rsCargoDetail[$i]['qty']);
                $_POST['selUnitCargo[]'] = $rsCargoDetail[$i]['unitkey'];
                $_POST['hidDestinationDetailKey[]'] = $rsCargoDetail[$i]['destinationkey'];
                $_POST['destinationDetailName[]'] = $rsCargoDetail[$i]['destinationname'];
                $_POST['amountCargo[]'] = $obj->formatNumber($rsCargoDetail[$i]['amount']);
                $_POST['sellingAmountCargo[]'] = $obj->formatNumber($rsCargoDetail[$i]['sellingamount']);

            }

                                    

        ?>

        <div class="div-table-row <?php echo $class; ?>" <?php echo $style ?> > 

            <div class="div-table-col detail-col-detail"> 
<!--                <?php echo $obj->inputHidden('hidCostDetailCargoKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => $etc)); ?> -->
                <?php echo $obj->inputHidden('hidCargoDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => $etc)); ?> 
                <?php echo $obj->inputHidden('hidDestinationDetailKey[]',array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => $etc)); ?> 
                <?php echo $obj->inputText('destinationDetailName[]',array('overwritePost' => $overwrite, 'readonly' => false, 'etc' => 'style="margin-bottom:0.3em;" ' . $etc)); ?>
                <?php echo $obj->inputText('destinationCargo[]',array('overwritePost' => $overwrite, 'readonly' => false, 'etc' => 'style="" ' . $etc)); ?>
		    </div> 
            <div class="div-table-col detail-col-detail">
			    <?php echo $obj->inputText('workOrderCargo[]', array('overwritePost' => $overwrite, 'readonly' => false, 'etc' => 'style="margin-bottom:0.3em;" ')); ?>
                <label style="margin-top:2.2em;"></label>
            </div>

            <div class="div-table-col detail-col-detail">
			    <?php echo $obj->inputNumber('qtyDetailCargo[]',array('overwritePost' => $overwrite,'readonly'=> false, 'etc' => 'style="text-align:right;margin-bottom:0.3em;" '. $etc.'')); ?>
                <label style="margin-top:2.2em;"></label>
            </div>

            <div class="div-table-col detail-col-detail">
			    <?php echo $obj->inputSelect('selUnitCargo[]', $arrUnit, array('overwritePost' => $overwrite, 'readonly' => false, 'etc' => 'style="margin-bottom:0.3em;" ' . $etc)); ?>
                <?php   if ($sellingPriceAllowed) {   ?>
                    <label style="margin-top:1em;"><?php echo ucwords($obj->lang['sellingPrice']); ?></label>
                <?php   } else {  ?>
                    <label style="margin-top:2.2em;"></label>
                <?php  }
                ?>
            </div>
            <?php for ($j = 0; $j < count($rsTruckingCost); $j++) {
                $arrCargoCost = array();
                for($ctr=0;$ctr<count($rsCostCargo);$ctr++){
                    
                    $indexkey = $rsCostCargo[$ctr]['costkey'] . '_' . $rsCostCargo[$ctr]['refkey'];
                    
                    $arrCargoCost[$indexkey]['pkey'] = $rsCostCargo[$ctr]['pkey'];
                    $arrCargoCost[$indexkey]['price'] = $rsCostCargo[$ctr]['price']; 
                    $arrCargoCost[$indexkey]['sellingprice'] = $rsCostCargo[$ctr]['sellingprice'];
                    $arrCargoCost[$indexkey]['ismultipliedqty'] = $rsCostCargo[$ctr]['ismultipliedqty'];
                }

                $index = $rsTruckingCost[$j]['pkey'] . '_' . $rsCargoDetail[$i]['pkey'];
                
                $_POST['hidCostDetailCargoKey_' .$rsTruckingCost[$j]["pkey"].'[]'] = $arrCargoCost[$index]['pkey'];
                $_POST['costCargoDetail_' .$rsTruckingCost[$j]["pkey"].'[]'] = (empty($arrCargoCost[$index]['price'])) ? 0 : $obj->formatNumber($arrCargoCost[$index]['price']);
                $_POST['sellingCostCargoDetail_' .$rsTruckingCost[$j]["pkey"].'[]'] = (empty($arrCargoCost[$index]['sellingprice'])) ? 0 : $obj->formatNumber($arrCargoCost[$index]['sellingprice']);
                $_POST['hidIsMultipliedQty_' .$rsTruckingCost[$j]["pkey"].'[]'] = (empty($arrCargoCost[$index]['ismultipliedqty'])) ? 0 : $obj->formatNumber($arrCargoCost[$index]['ismultipliedqty']);
                                
            ?>
                <div class="div-table-col detail-col-detail"> 
                    <?php echo $obj->inputHidden('hidCostDetailCargoKey_'.$rsTruckingCost[$j]['pkey'].'[]',array('overwritePost' => $overwrite, 'disabled' => $disabled, 'etc' => $etc)); ?> 
                    <?php echo $obj->inputHidden('hidIsMultipliedQty_'.$rsTruckingCost[$j]['pkey'].'[]',array('overwritePost' => $overwrite, 'value'  => $rsTruckingCost[$j]['ismultipliedbyqty'], 'disabled' => $disabled, 'etc' => $etc)); ?> 
                    <?php echo $obj->inputNumber('costCargoDetail_'.$rsTruckingCost[$j]['pkey'].'[]', array('overwritePost' => $overwrite, 'readonly' => false, 'etc' => 'style="text-align:right;margin-bottom:0.3em;" '. $etc .'')); ?>
                    <?php  if ($sellingPriceAllowed)  
                            echo $obj->inputNumber('sellingCostCargoDetail_'.$rsTruckingCost[$j]['pkey'].'[]', array('overwritePost' => $overwrite, 'readonly' => false, 'etc' => 'style="text-align:right" '. $etc .''));
                          else 
                            echo '<label style="margin-top:2.2em;"></label>';
                    ?>
                    
                </div>
            <?php } ?>

            <div class="div-table-col detail-col-detail">
			    <?php echo $obj->inputNumber('amountCargo[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right;margin-bottom:0.3em;"'. $etc .'')); ?>
                <?php  if ($sellingPriceAllowed)   
                        echo $obj->inputNumber('sellingAmountCargo[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => 'style="text-align:right"'. $etc .''));
                     else 
                         echo '<label style="margin-top:2.2em;"></label>';
                ?>
			    
            </div>
        <div class="div-table-col detail-col-detail icon-col <?php echo $obj->hideOnDisabled(); ?>">
                <div class="flex">
                <div><?php echo $obj->inputLinkButton('btnAddCargoRow' , '<i class="fas fa-plus-circle"></i>', array('class' => 'btn btn-link add-row-button', 'etc' => 'attr-template="detail-row-template"')); ?></div>
                <div><?php echo $obj->inputLinkButton('btnDeleteRowsCargo' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1"')); ?></div>
                </div>    
            </div>
        </div>

        <?php } ?>

    </div>
    </div>

    <div style="clear:both; height:1em;"></div> 

      <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['costName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['cost']); ?> </div>
                    <?php if ($showRealizationCost) { ?>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right; padding-left:0;"><?php echo ucwords($obj->lang['realization']); ?> </div>
                    <?php } ?>
                    <div class="div-table-col detail-col-header" style="width:225px; text-align:left;"><?php echo ucwords($obj->lang['employee']); ?> <span class="text-muted">(<?php echo ucwords($obj->lang['recipient']); ?>)</span></div>
                    <div class="div-table-col detail-col-header" style="width:225px; text-align:left;"><?php echo ucwords($obj->lang['supplier']); ?> <span class="text-muted">(<?php echo ucwords($obj->lang['recipient']); ?>)</span></div>
                    <div class="div-table-col detail-col-header" style="width:25px; text-align:center;"><i class="fas fa-file-alt" style="font-size:1.2em; line-height:0; position:relative;top:0.1em"></i></div>
                    <div class="div-table-col detail-col-header icon-col" style="width:45px "></div>
                </div>
                
				<?php  
                      $totalRows = count($rsCost); 
                      for ($i=0;$i<=$totalRows; $i++){  

                            $class =  'transaction-detail-row';
                            $style = '';
                            $overwrite = true;
                            $etc = ''; 

                            $statusStyle = '';
                            $detail = '';

                            $readonlyOnFixedCost = false;
                            $fixedCostClass = '';
                            $cashedOutIcon = '';
                            $deleteIcon = $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' =>  'tabIndex="-1" style="padding:6px 0"'));

                            if ($i == $totalRows ){
                                $class = 'cost-row-template';
                                $style = 'style="display:none"';
                                $overwrite = false;
                                $etc = 'disabled="disabled"';  
                            } else {    

                                  /*if ($rsCost[$i]['fixedcost'] == 1 || !empty($rsCost[$i]['refcashoutkey'])){
                                      $readonlyOnFixedCost = true;
                                      $fixedCostClass = 'fixed-cost-list';
                                  }*/
                                
                                 if ($rsCost[$i]['realizationkey'] <> 0 || !empty($rsCost[$i]['refcashoutkey']) || !empty($rsCost[$i]['refrequestkey'])){
                                      $readonlyOnFixedCost = true;
                                      $fixedCostClass = 'fixed-cost-list';
                                  }

                                $_POST['hidDetailKey[]'] = $rsCost[$i]['pkey'];
                                $_POST['hidCostKey[]'] = $rsCost[$i]['costkey'];
                                $_POST['costName[]'] = $rsCost[$i]['name'];  
                                $_POST['amount[]'] = (isset($rsCost[$i]['amount'])) ?  $obj->formatNumber($rsCost[$i]['amount']) : 0;
                                $_POST['requestAmount[]'] =  (isset($rsCost[$i]['requestamount'])) ? $obj->formatNumber($rsCost[$i]['requestamount']) : 0;
                                $_POST['hidRefCashOutKey[]'] = '';  
                                $_POST['refCashOutCode[]'] = '';
                                $_POST['hidSupplierDetailKey[]'] = (isset($rsCost[$i]['supplierkey'])) ? $rsCost[$i]['supplierkey'] : '';
                                $_POST['supplierDetailName[]'] =  (isset($rsCost[$i]['suppliername'])) ? $rsCost[$i]['suppliername'] : '';
                                $_POST['hidEmployeeDetailKey[]'] = (isset($rsCost[$i]['employeekey'])) ?$rsCost[$i]['employeekey'] : '';
                                $_POST['employeeDetailName[]'] = (isset($rsCost[$i]['employeename'])) ?$rsCost[$i]['employeename'] : '';
                                $_POST['chkReceivedDoc[]'] = $rsCost[$i]['isreceiveddoc'];

                                // kalo outsource nimpa dr AP
                                // PERLU REVISI karena gk selalu dr AP skrg
                                /*if (!empty($rs) && $rs[0]['isoutsource'] == 1){
                                    $ap = new AP();
                                    $rsAP = $ap->getDataRowById($rsCost[$i]['refcashoutkey']);
                                    $rsCost[$i]['refcashoutcode'] = $rsAP[0]['code'];
                                } */

                                if (isset($rsCost[$i]['refcashoutkey']) && !empty($rsCost[$i]['refcashoutkey'])){ 
                                    $_POST['hidRefCashOutKey[]'] =  $rsCost[$i]['refcashoutkey'];  
                                    
                                    // kalo supplier, ambil dr hutang
                                    if (!empty($rsCost[$i]['supplierkey'])){ 
                                        $rsAP = $ap->getDataRowById($rsCost[$i]['refcashoutkey']);
                                        $rsCost[$i]['refcashoutcode'] = $rsAP[0]['code'];
                                    }
                                    
                                    $_POST['refCashOutCode[]'] =  $rsCost[$i]['refcashoutcode'];
                                    $cashedOutIcon = '<i class="cashed-out-icon fas fa-hand-holding-usd" title="'.$_POST['refCashOutCode[]'].'"></i>'; 
                                    $deleteIcon = '';
                                }
                                
                                
                                if (isset($rsCost[$i]['refrequestkey']) && !empty($rsCost[$i]['refrequestkey'])){  
//                                    $_POST['refCashOutCode[]'] =  $rsCost[$i]['refcashoutcode'];
                                    $cashedOutIcon = '<i class="cashed-out-icon far fa-file-alt" title="'.$_POST['refCashOutCode[]'].'"></i>'; 
                                    $deleteIcon = '';
                                }
                                
                                
                                if ($rsCost[$i]['realizationkey'] <> 0 )  $deleteIcon = '';
                            }

                          //overwrite kalo gk punya akses
                          if (!$hasCostAccess)
                               $readonlyOnFixedCost = true;
 

                    ?>
            
                
                <div class="div-table-row  <?php echo $class; ?>" <?php echo $style; ?> >
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                        <?php echo $obj->inputText('costName[]',array('overwritePost' => $overwrite,'readonly' => $readonlyOnFixedCost, 'class' => 'form-control ' . $fixedCostClass, 'etc' => $etc )); ?>
                        <?php echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputNumber('requestAmount[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyOnFixedCost, 'class' => 'form-control inputnumber ' . $fixedCostClass, 'etc' => 'style="text-align:right;"' .$etc )); ?>
                    </div>
                    <?php if ($showRealizationCost) { ?> 
                    <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('amount[]', array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control inputnumber ' . $fixedCostClass, 'etc' => 'style="text-align:right;"' .$etc )); ?></div>
                    <?php } ?> 
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('employeeDetailName[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyOnFixedCost, 'class' => 'form-control  ' . $fixedCostClass, 'etc' => $etc )); ?>
                        <?php echo $obj->inputHidden('hidEmployeeDetailKey[]',array('overwritePost' => $overwrite, 'etc' => ' onChange="truckingServiceWorkOrder.removeSupplierOnChange(this)" '.$etc)); ?>
                    </div>
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('supplierDetailName[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyOnFixedCost, 'class' => 'form-control  ' . $fixedCostClass, 'etc' => $etc )); ?>
                        <?php echo $obj->inputHidden('hidSupplierDetailKey[]',array('overwritePost' => $overwrite, 'etc' => ' onChange="truckingServiceWorkOrder.removeEmployeeOnChange(this)" '. $etc)); ?>
                    </div>
                    <div class="div-table-col detail-col-detail" style="text-align:center">
                        <?php echo $obj->inputCheckBox('chkReceivedDoc[]', array('overwritePost' => $overwrite, 'etc' => $etc )); ?>
                   </div>
                    <div class="div-table-col detail-col-detail icon-col"> 
                        <?php echo $obj->inputHidden('hidRefCashOutKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                        <?php //echo $obj->inputText('refcashoutcode[]', array('overwritePost' => $overwrite, 'readonly' => true, 'etc' => $etc )); ?> 
                        <?php echo  $cashedOutIcon.$deleteIcon; ?>
                    </div>
                </div>
                 
            <?php } ?> 
                   
        </div> 
        <div style="clear:both; height:1em;"></div> 
        <?php if ($hasCostAccess){ ?>
        <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
        <?php } ?>
      
      <div class="form-button-margin"></div>
        <div class="form-button-panel" >  
         <?php  echo $obj->generateSaveButton(array(),true);   ?>  
        </div> 
        
    </form>  
   
     <?php echo $obj->showDataHistory(); ?>
    
</div> 
</body>

</html>
