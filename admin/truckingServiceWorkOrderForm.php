<?php

require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('TruckingServiceWorkOrder.class.php','ItemUnit.class.php'));
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());

// deprecated
//$workProgress = createObjAndAddToCol(new WorkProgress());
//$workProgressStep = createObjAndAddToCol(new WorkProgressStep());
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
$itemUnit = new ItemUnit();
$jobProgress = createObjAndAddToCol(new JobProgress());

$obj= $truckingServiceWorkOrder;

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$hasCostAccess = $security->isAdminLogin($obj->costSecurityObject,10);
    
$formAction = 'truckingServiceWorkOrderList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 
    
$dateReturnOnEmpty = array('returnOnEmpty'=>true, 'value' => '');
$_POST['trDate'] =  date('d / m / Y');
$_POST['trDateStuffing'] = date('d / m / Y 00:00');
//$defaultCompletedDate = date('d / m / Y 00:00');
 
$rsSODetail = array(); 
$arrCategory = array();
$rsCost = array();
$rsJobProgressDetail = array();
//$rsProgressStep = $workProgressStep->searchData($workProgressStep->tableName.'.statuskey','1',true,' order by orderlist asc');

$rs = prepareOnLoadData($obj);  
$rsItemFile = array();

$showRealizationCost = $obj->useRealization();
$editWarehouseInactiveCriteria = '';
$cashOutDownpaymentKey = 0;
$cashOutDownpaymentCode = '';
$editUnitInactiveCriteria = '';

$partyDecimal = $obj->loadSetting('jobOrderPartyDecimal'); 
if($partyDecimal == '')  $partyDecimal = 0; // buat JS

$tax23DeductedAtPurchase = $obj->loadSetting('tax23DeductedAtPurchase'); 
$tax23DeductedAtPurchase = ($tax23DeductedAtPurchase == 1) ? true : false;

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	
    $rsServiceOrder = $truckingServiceOrder->searchData($truckingServiceOrder->tableName.'.pkey', $rs[0]['refkey'],true);
    $rsSODetail = $truckingServiceOrder->getDetailWithRelatedInformation($rsServiceOrder[0]['pkey']);
    $rsCost = $obj->getCostDetail($id,'','',' order by '.$obj->tableItem.'.fixedcost desc, '.$obj->tableItem.'.name asc');
    
    // deprecated
    //$rsProgress = $workProgress->getProgress($id,$rs[0]['driverkey']);
    //$arrProgress = array_column($rsProgress,'progresskey');
    
    if($obj->activeModule['jobprogress']){
            $rsJobProgressDetail = $obj->getJobProgressDetail($id); 
            //if(empty($rsJobProgressDetail)) {
            //    $rsJobProgress = $jobProgress->getJobProgressByCategory($rs[0]['categorykey']); 
            //    for ($i = 0; $i < count($rsJobProgress); $i++) {
            //        $rsJobProgressDetail[$i] = array(
            //            'jobprogresskey'       => $rsJobProgress[$i]['pkey'],
            //            'jobprogressheaderkey' => $rsJobProgress[$i]['refkey'],
            //            'jobprogressname'      => $rsJobProgress[$i]['name'],
            //            'number'               => $rsJobProgress[$i]['number'],
            //            'completeddate'        => $rs[0]['stuffingdatetime'],
            //            'iscompleted'          => 0
            //        );
            //    }  
            //}
    }

    
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
    $_POST['consigneeName'] = $rsServiceOrder[0]['consigneename'];
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
     
    if ($obj->activeModule['truckingpurchase'] && $tax23DeductedAtPurchase) {
        $rsCarDetail = $obj->getCarDetail($rs[0]['pkey']);
        $_POST['tax23Percentage'] = $obj->formatNumber($rsCarDetail[0]['tax23percentage'],2);
    }
    
	$_POST['cargoQty'] = $obj->formatNumber($rs[0]['cargoqty'],2); 
	$_POST['cargoWeight'] = $obj->formatNumber($rs[0]['cargoweight'],2); 
	$_POST['cargoQtyUnit'] = $rs[0]['cargoqtyunit'];  
	$_POST['cargoWeightUnit'] = $rs[0]['cargoweightunit'];  

	$rsItemFile = $truckingServiceOrder->getItemFile($rs[0]['refkey']);
 
    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
    
}else{

    // ambil semua cost fixed dulu
    $rsCost = $truckingCost->searchData($truckingCost->tableName.'.statuskey',1,true,' and showintrucking = 1 and chargetype = 2 and fixedcost = 1 and isdroppointdetailprice = 0 ','order by fixedcost desc, name asc');
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
    function TruckingServiceWorkOrder(tabID) {  
        
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

                        truckingServiceWorkOrder.getJobProgressList(data.categorykey);
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
         
             // hanya jika masi dalam status boleh edit
             <?php if ((!isset( $rs[0]['statuskey']) || in_array( $rs[0]['statuskey'], $obj->allowedStatusForEdit)) && $hasCostAccess) { ?>   
                 $("#" + tabID + " .fixed-cost-list").each(function() {
                     $(this).prop("readonly", !$(obj).prop("checked"));
                 }) 
             <?php } ?>
             
         }
         
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

        this.toggleDateDisplay = function toggleDateDisplay(row, isChecked) {
            if (isChecked) {
                row.find('.non-completed-date').hide();
                row.find('.completed-date').show();
            } else {
                row.find('.completed-date').hide();
                row.find('.non-completed-date').show();
            }
        }


        this.getJobProgressList = function getJobProgressList(categorykey)
        {
            var ajaxData = "action=getJobProgressForWorkOrder&categorykey=" + categorykey;  
          
            $.ajax({
                type: "GET",
                url:  'ajax-job-progress.php',
                beforeSend:function (xhr){ 
                    $("#" + tabID + " .job-progress-transaction-detail-row").remove();
                },
                data: ajaxData,
                success: function(data){ 
        
                        data = JSON.parse(data) ;    

                        if(data.length <= 0) {
                            addNewTemplateRow("detail-job-progress-row-template","","");
                            return;
                        }

                        var i;
                        for(i=0;i<data.length;i++){  

                            var arrPostValue = []; 
                            arrPostValue.push({"selector":"hidJobProgressKey", "value":data[i].pkey});
                            arrPostValue.push({"selector":"jobProgressNumber", "value":data[i].number});
                            arrPostValue.push({"selector":"hidJobProgressHeaderKey", "value":data[i].refkey});
                            arrPostValue.push({"selector":"jobProgressName", "value":data[i].name});
                            arrPostValue.push({"selector":"trDateCompleted", "value":moment().format("DD / MM / YYYY 00:00")});
                            //arrPostValue.push({"selector":"hidIsCompleted", "value":0});
                            
                            var newRow = addNewTemplateRow("detail-job-progress-row-template",JSON.stringify(arrPostValue)); 
                            newRow.find(".input-datetime").removeClass("hasDatepicker");
                            newRow.find(".input-datetime").removeAttr("id"); 
                            newRow.find(".input-datetime").datetimepicker({  currentText: 'Now', dateFormat:'dd / mm / yy',  changeMonth: true, changeYear: true }); 
                            
                            truckingServiceWorkOrder.toggleDateDisplay(newRow, false);  
                        }
 
                 
                } 
            }); 
        }
 
 
    }
    
    
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        truckingServiceWorkOrder = new TruckingServiceWorkOrder(tabID);
        setOnDocumentReady(tabID);    
       
        $(document).on('change', "#"+tabID+" [name='dummychkIsCompleted[]']", function () {
            const $row = $(this).closest('.div-table-row');
            const isChecked = $(this).is(':checked');
            truckingServiceWorkOrder.toggleDateDisplay($row, isChecked);
        });


        $(document).on('change', "#"+tabID+" [name='trDateCompleted[]']", function () {
            const val = $(this).val();
            const $tab = $("#" + tabID);
            const index = $tab.find("[name='trDateCompleted[]']").index(this);
            $tab.find("[name='dummytrDateCompleted[]']").eq(index).val(val);
        });
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
			   soNumber: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.jobOrder[1]
                        }
                    } 
                }, 
            }
        })
        .on('success.form.bv', function(e) { 
              <?php echo $obj->submitFormScript(); ?> 
        }); 
          
        truckingServiceWorkOrder.showOutsource("#" + tabID + " [name=chkIsOutsource]",false); 
                 
        $("#"+tabID+" [name=btnUpdateCost]").on('click', function() {
            truckingServiceWorkOrder.updateCost();
        }); 
        
        $("#"+tabID+" [name='dummychkIsCompleted[]']").on('click', function() {
     
            var checkboxes = $("#" + tabID + " [name='dummychkIsCompleted[]']");
            var index = checkboxes.index(this);
            if ($(this).is(':checked')) {
                checkboxes.slice(0, index).each(function () {
                    if (!$(this).is(':checked')) {
                        $(this).click(); // or .click() if needed
                        truckingServiceWorkOrder.toggleDateDisplay($(this).closest('.div-table-row'), true);
                    }
                });
            }
            truckingServiceWorkOrder.toggleDateDisplay($(this).closest('.div-table-row'), $(this).is(':checked'));
        }); 
    
     
        $("#"+tabID+" [name=outsourceCost],  #"+tabID+" [name=outsourceDownpayment],  #"+tabID+" [name=chkIncludeTax],  #"+tabID+" [name=taxPercentage],  #"+tabID+" [name=taxValue],  #"+tabID+" [name=total]").on('change', function() {
            truckingServiceWorkOrder.updateOutsourceAP();
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
	     
		// DETAIL CLONE
		 $("#"+tabID+" [name=btnAddRows]").on('click', function() {
          	var newRow = addNewTemplateRow("cost-row-template"); 
            bindAutoCompleteForTransactionDetail('costName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1&moduleCost=trucking');   
            bindAutoCompleteForTransactionDetail('supplierDetailName[]',objAndValueForSupplierDetailAutoComplete[tabID],'ajax-supplier.php?action=searchData');   
            bindAutoCompleteForTransactionDetail('employeeDetailName[]',objAndValueForEmployeeDetailAutoComplete[tabID],'ajax-employee.php?action=searchData');   
        }); 
        
        <?php if (empty($rsCost)){ ?> 
            var newRow = addNewTemplateRow("cost-row-template");  
        <?php }  ?>

         bindAutoCompleteForTransactionDetail('costName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1&moduleCost=trucking');   
         bindAutoCompleteForTransactionDetail('supplierDetailName[]',objAndValueForSupplierDetailAutoComplete[tabID],'ajax-supplier.php?action=searchData');  
         bindAutoCompleteForTransactionDetail('employeeDetailName[]',objAndValueForEmployeeDetailAutoComplete[tabID],'ajax-employee.php?action=searchData');   
       
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
                                                                                'readonly' => true, 
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
  
                                    <div class="form-group" style="margin-bottom:5px"> 
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
                                    </div>       
                                    <div style="clear:both; height:2em"></div> 
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
                                    <div class="form-group inhouse">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['driverCommission']; ?></label> 
                                        <div class="col-xs-9">  
                                                <?php echo $obj->inputNumber('driverCommission' , array('readonly' => (!$hasCostAccess) ? true : false ) );?> 
                                        </div> 
                                    </div> 
                                <div class="form-group inhouse">
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
                                    </div>
                                 <div class="form-group inhouse">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['codriverCommission']; ?></label> 
                                        <div class="col-xs-9">  
                                                  <?php echo $obj->inputNumber('codriverCommission', array('readonly' => (!$hasCostAccess) ? true : false ));?> 
                                        </div> 
                                    </div> 
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
                                <div class="form-group inhouse" >
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
                                </div>   
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
                                            <?php if ($obj->activeModule['truckingpurchase'] && $tax23DeductedAtPurchase) { ?>
                                                <div style="padding-left:2em"><?php echo strtoupper($obj->lang['tax23']); ?></div>
                                                <div  style="width: 5em"><?php echo $obj->inputDecimal('tax23Percentage',array('value' => 2)); ?></div>
                                                <div style="text-align:center;width:20px">%</div>
                                            <?php } ?>
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
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['consignee']; ?></label> 
                               <div class="col-xs-9"> <?php echo $obj->inputText('consigneeName',array( 'readonly' => true)); ?></div> 
                            </div>     
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo strtoupper($obj->lang['si']); ?></label>
                               <div class="col-xs-9"> <?php echo $obj->inputText('doNumber',array('readonly' => true)); ?></div> 
                            </div>   
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['bookingNumber']; ?></label> 
                               <div class="col-xs-9"> <?php echo $obj->inputText('shipmentNumber',array('readonly' => true)); ?></div> 
                            </div>   
                            
                        </div> 
                         
                        <div class="div-tab-panel"> 
                            <div class="div-table-caption border-blue"><?php echo $obj->lang['stuffingDestuffingInformation']; ?></div>
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['depot']; ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('depotName',array('readonly' => true)); ?> 
                                    <?php echo $obj->inputHidden('hidDepotKey'); ?>   
                                </div> 
                            </div>  

                            <div class="form-group">
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
                            </div>

                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['warehouse']; ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('warehouseName',array( 'readonly' => true)); ?>  
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['location']; ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('locationName',array( 'readonly' => true)); ?>   
                                </div> 
                            </div>  
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['contactPerson']; ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo $obj->inputText('contactPerson',array( 'readonly' => true)); ?>    
                                </div> 
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['address']; ?></label> 
                                <div class="col-xs-9">     
                                   <?php echo  $obj->inputTextArea('stuffingAddress', array('readonly' => true, 'etc' => 'style="height:8em;"')); ?>
                                </div> 
                            </div> 
                             
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
                        <?php  if($obj->activeModule['jobprogress']){ ?>
                        <div class="div-tab-panel">
                        <div class="div-table-caption border-green-avocado"><?php echo $obj->lang['jobProgress']; ?></div> 
							
                            <div class="div-table mnv-transaction transaction-detail" style="width:100%; border-bottom:1px solid #333">
                                <div class="div-table-row">  
                                        <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['description']); ?></div>    
                                        <div class="div-table-col detail-col-header" style="width:150px; text-align:center"><?php echo ucwords($obj->lang['updateTime']); ?></div>
                                        <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?>" style="width:50px;"></div> 
                                </div> 

                                <?php  
                                    $totalRows = count($rsJobProgressDetail); 
                                    for ($i=0;$i<=$totalRows; $i++){  

                                    $class =  'job-progress-transaction-detail-row';
                                            $style = '';
                                            $overwrite = true;
                                            $disabled = false;
                                            $readonly = false; 
                                            $disabledButton = true;
                                            $showReadonlyDate = 'display:none';
                                            $showNotReadonlyDate = '';
                                            $location = $obj->lang['viewLocation'];
                                            $podFile = ''; // sementara hanya bisa dari S3
                                        
                                            if ($i == $totalRows ){
                                                $class = 'detail-job-progress-row-template row-template';
                                                $overwrite = false;
                                                $disabled = true; 
                                                $readonly = true; 
                                                $disabledButton = true; 
                                                $showReadonlyDate = '';
                                                $showNotReadonlyDate = 'display:none';
                                            } else {       

                                                $disabledButton = false;
                                                    
                                                $_POST['hidJobProgressDetailKey[]'] = $rsJobProgressDetail[$i]['pkey'];
                                                $_POST['hidJobProgressKey[]'] = $rsJobProgressDetail[$i]['jobprogresskey'];
                                                $_POST['hidJobProgressHeaderKey[]'] = $rsJobProgressDetail[$i]['jobprogressheaderkey'];
                                                $_POST['jobProgressName[]'] = $rsJobProgressDetail[$i]['jobprogressname']; 
                                                $_POST['trDateCompleted[]'] = $obj->formatDBDate($rsJobProgressDetail[$i]['completeddate'], 'd / m / Y H:i', $dateReturnOnEmpty);
                                                $_POST['dummytrDateCompleted[]'] = $obj->formatDBDate($rsJobProgressDetail[$i]['completeddate'], 'd / m / Y H:i', $dateReturnOnEmpty);
                                                $_POST['chkIsCompleted[]'] = $rsJobProgressDetail[$i]['iscompleted'];
                                                $_POST['jobProgressNumber[]'] = $rsJobProgressDetail[$i]['number'];

                                                if($rsJobProgressDetail[$i]['iscompleted'] == 1)  {
                                                    $readonly = true; 
                                                    $showReadonlyDate = '';
                                                    $showNotReadonlyDate = 'display:none;';
                                                } else {
                                                    $showReadonlyDate = 'display:none;';
                                                    $showNotReadonlyDate = '';
                                                }
                                                
                                                if(!empty($rs[0]['pkey'])){
                                                      if(!empty(floatval($rsJobProgressDetail[$i]['latitude'])) && !empty(floatval($rsJobProgressDetail[$i]['longitude'])))
                                                            $location = '<a href="workOrderMap?ltdlng='.$rsJobProgressDetail[$i]['latitude'].','.$rsJobProgressDetail[$i]['longitude'].'" target="_blank">'. $location.'</a>';
                                                      if(!empty($rsJobProgressDetail[$i]['filename']))
                                                            $podFile = '<a href="'.$obj->createPresignedURL(DOMAIN_NAME.'/'.$obj->uploadProgressFileFolder.$rs[0]['pkey'].'/'.$rsJobProgressDetail[$i]['jobprogresskey'].'/'.$rsJobProgressDetail[$i]['filename']).'" target="_blank" >'. $obj->lang['viewPOD'].'</a>';
                                                }
                                              
                                            }
                                           
                                ?>

                                <div class="div-table-row  <?php echo $class; ?>">
                                  
                                    <div class="div-table-col detail-col-detail">
                                        <?php echo $obj->inputText('jobProgressName[]',array('overwritePost' => $overwrite,'readonly' => true,  'disabled' => $disabled,  'etc' => '' )); ?>
                                        <?php echo $obj->inputHidden('hidJobProgressDetailKey[]',array('overwritePost' => $overwrite,'readonly' => $readonly,  'disabled' => $disabled)); ?>
                                        <?php echo $obj->inputHidden('hidJobProgressKey[]',array('overwritePost' => $overwrite,'readonly' => $readonly,  'disabled' => $disabled )); ?>
                                        <?php echo $obj->inputHidden('hidJobProgressHeaderKey[]',array('overwritePost' => $overwrite,'readonly' => $readonly,  'disabled' => $disabled )); ?>
                                        <?php echo $obj->inputHidden('jobProgressNumber[]',array('overwritePost' => $overwrite,'readonly' => $readonly,  'disabled' => $disabled )); ?>
                                        <?php echo '<div class="flex justify-content-space-between" style="padding-left:0.3em"><div>'.$location.'</div><div>'.$podFile.'</div></div>'; ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail" style="vertical-align:top;">
                                        <div class="non-completed-date" style="<?php echo $showNotReadonlyDate; ?> ">
                                            <?php echo $obj->inputDateTime('trDateCompleted[]', array('overwritePost' => $overwrite, 'allowEmpty' => true,   'etc' => 'style="text-align:center;" ')); ?>
                                        </div>
                                        <div class="completed-date" style="<?php echo $showReadonlyDate; ?>;">
                                            <?php echo $obj->inputText('dummytrDateCompleted[]',array('overwritePost' => $overwrite,'readonly' => true,  'disabled' => $disabled,  'etc' => 'style="text-align:center;" ' )); ?>
                                        </div>    
                                    </div>
                                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"  style="vertical-align:top; padding-top:7px !important">
                                        <?php echo $obj->inputCheckBox('chkIsCompleted[]',array('disabled' => $disabled )); ?>
                                    </div>
                                </div> 

                                <?php } ?> 
                            </div>  

                            <div style="clear:both; height:0.5em;"></div>

                         </div> 
                        <?php  } ?>
						<div class="div-tab-panel">
                             <div class="div-table-caption border-red"><?php echo $obj->lang['cargoInformation']; ?></div> 
							<div class="form-group">
                                 <label class="col-xs-3 control-label"><?php echo $obj->lang['quantity'];?></label> 
                                	<div class="col-xs-9"> 
										<div class="flex">
											<div style="width: 6em"> <?php echo $obj->inputNumber('cargoQty',array('etc' => 'style="text-align:right"')); ?>  </div>
											<div  class="consume"> <?php echo $obj->inputSelect('cargoQtyUnit',$arrUnit ); ?>  </div>
											<div style="margin-left: 2em; margin-right:0.5em"> <?php echo $obj->lang['weight']; ?>  </div>
											<div  style="width: 6em"> <?php echo $obj->inputNumber('cargoWeight',array('etc' => 'mnv-attr-decimal="2" style="text-align:right"')); ?>  </div>
											<div  class="consume"> <?php echo $obj->inputSelect('cargoWeightUnit',$arrWeight); ?>  </div>
										</div> 
                                </div> 
                            </div> 
                             <div class="form-group">
                                 <label class="col-xs-3 control-label"><?php echo $obj->lang['description']; ?></label> 
                                	<div class="col-xs-9"> 
                                    <?php echo $obj->inputTextArea('productDescription', array( 'etc' => 'style="height:8em;"')); ?>
                                </div> 
                            </div> 
                         </div>
                         <div class="div-tab-panel">
                             <div class="div-table-caption border-purple"><?php echo $obj->lang['note']; ?></div> 
                             <div class="form-group">
                                <div class="col-xs-12"> 
                                       <?php echo  $obj->inputTextArea('trDesc', array( 'etc' => 'style="height:8em;"')); ?>
                                </div> 
                            </div> 
                         </div>
                         <!--
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
                         -->
                         
                         <div class="div-tab-panel"> 
                               <div class="div-table-caption border-black"><?php echo $obj->lang['files']; ?></div> 
                                <div class="item-file-uploader user-select-none">
                                    <ul class="file-list">
                                        <?php for($i=0;$i<count($rsItemFile);$i++) {
                                            $fileURL = ($obj->useStorage) ? $obj->createPresignedURL(DOMAIN_NAME.'/trucking-service-order/'.$rs[0]['refkey'].'/'.$rsItemFile[$i]['file']) : '/download.php?filename=trucking-service-order/'.$rs[0]['refkey'].'/'.$rsItemFile[$i]['file'];
                                            echo '<li><div class="panel"><div class="file-uploader-description"><a href="'.$fileURL.'" target="_blank" title="'.$rsItemFile[$i]['file'].'">'.$rsItemFile[$i]['file'].'</a></div></div></li>';
                                        } ?>    
                                    </ul>
                                </div>
                        </div>   
 	               </div>    
                </div>
      </div> 
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
                    <div class="div-table-col detail-col-header icon-col" style="width:45px;border:1;"></div>
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
                                 
                                 if ($rsCost[$i]['realizationkey'] <> 0 || !empty($rsCost[$i]['refcashoutkey']) || !empty($rsCost[$i]['refrequestkey']) || !empty($rsCost[$i]['refadditionalcostkey'])){
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
                                $_POST['hidRefAdditionalCostkey[]'] = $rsCost[$i]['refadditionalcostkey'];

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
                                 
                                if ($rsCost[$i]['realizationkey'] <> 0 || $rsCost[$i]['refadditionalcostkey'] <> 0 )  $deleteIcon = '';
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
                        <?php echo $obj->inputHidden('hidRefAdditionalCostkey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
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
