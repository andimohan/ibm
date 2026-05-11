<?php 
require_once '../../../_config.php'; 
require_once '../../../_include-v2.php'; 

includeClass('TruckingServiceWorkOrder.class.php');
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

$obj= $truckingServiceWorkOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));

$hasCostAccess = $security->isAdminLogin($obj->costSecurityObject,10);
    
$formAction = 'truckingServiceWorkOrderList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false; 
    
$_POST['trDate'] =  date('d / m / Y');
$_POST['trDateStuffing'] = date('d / m / Y 00:00');
$_POST['chkIsOutsource'] = 1;
 
 
$rsSODetail = array(); 
$arrCategory = array();
$rsCost = array();
$rsCarDetail = array();
$rsProgressStep = $workProgressStep->searchData($workProgressStep->tableName.'.statuskey','1',true,' order by orderlist asc');

$rs = prepareOnLoadData($obj);  
$rsItemFile = array();

$showRealizationCost = $obj->useRealization();
$editWarehouseInactiveCriteria = '';
$cashOutDownpaymentKey = 0;
$cashOutDownpaymentCode = '';

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	
	
    $rsServiceOrder = $truckingServiceOrder->searchData($truckingServiceOrder->tableName.'.pkey', $rs[0]['refkey'],true);
    $rsSODetail = $truckingServiceOrder->getDetailWithRelatedInformation($rsServiceOrder[0]['pkey']);
    $rsCarDetail = $obj->getCarDetail($rs[0]['pkey']);
    $rsCost = $obj->getCostDetail($id,'','',' order by '.$obj->tableItem.'.fixedcost desc, '.$obj->tableItem.'.name asc');
    $rsProgress = $workProgress->getProgress($id,$rs[0]['driverkey']);
    $arrProgress = array_column($rsProgress,'progresskey');
    
   // $rsTariff = $truckingSellingRate->searchData($truckingSellingRate->tableName.'.pkey',$rsServiceOrder[0]['contractkey']);
            
    if ($rs[0]['statuskey'] == 2)  
        $statusConfirmed = array('status' => true, 'readonly' => 'readonly="readonly"',  'disabled' =>  'disabled="disabled"');
   
    $cashOutDownpaymentKey  = $rs[0]['refcashoutdownpaymentkey'];
    if(!empty($cashOutDownpaymentKey)){
        $rsDownpaymentCashOut = $truckingCostCashOut->getDataRowById($cashOutDownpaymentKey);
        $cashOutDownpaymentCode = $rsDownpaymentCashOut[0]['code'];
    }
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
    
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
    
    /*$_POST['containerNumber'] =  $rs[0]['containernumber'];
    $_POST['container2Number'] =  $rs[0]['container2number'];
    $_POST['sealNumber'] =  $rs[0]['sealnumber'];
    $_POST['seal2Number'] =  $rs[0]['seal2number'];*/
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
    //$_POST['outsourceAP'] = $obj->formatNumber($rs[0]['outsourceap']);
    $_POST['hidDownpaymentRecipientKey'] = $rs[0]['downpaymentemployeekey'];
    
    if (!empty($rs[0]['downpaymentemployeekey'])){
		$rsEmployee = $employee->getDataRowById($rs[0]['downpaymentemployeekey']);
		$_POST['downpaymentRecipientName'] = $rsEmployee[0]['name'];
	}
     
        
    //$_POST['chkIsOutsource'] = $rs[0]['isoutsource'];  
	$_POST['trDesc'] = $rs[0]['trdesc'];   
	$_POST['selJobType'] = $rs[0]['jobtypekey'];  
    
    $_POST['total'] = $obj->formatNumber($rs[0]['total']);
    $_POST['chkIncludeTax'] = $rs[0]['ispriceincludetax'];  
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']);
    $_POST['chkIsOutsource'] = 1;
     
      
	$rsItemFile = $truckingServiceOrder->getItemFile($rs[0]['refkey']);
 
    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
    
}else{

    // ambil semua cost fixed dulu
    $rsCost = $truckingCost->searchData($truckingCost->tableName.'.statuskey',1,true,' and showintrucking = 1 and chargetype = 2 and fixedcost = 1','order by fixedcost desc, name asc');
    for($i=0;$i<count($rsCost);$i++){ 
         $rsCost[$i]['costkey']=$rsCost[$i]['pkey']; 
         $rsCost[$i]['pkey']=0; 
    }
}
 
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');   
$arrContainer = $obj->convertForCombobox($rsSODetail,'pkey','label');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 

$rsCategory = $truckingJob->searchData(); 
$arrJobType = $obj->convertForCombobox($rsCategory,'pkey','name');     

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
         this.tablekey = <?php echo $obj->getTableKeyAndObj($obj->tableName ,array('key'))['key']; ?>;   
         
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
                    for(i=0;i<data.length;i++)  
                        newOptions[data[i].pkey] =  data[i].label;       
                    
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
                            
                            //truckingServiceWorkOrder.updateCostSupplier();
                            //truckingServiceWorkOrder.updateSupplierDetail();
                        }  
                });   
         
        }
         
        this.removeSupplierOnChange = function removeSupplierOnChange(obj){ 
          var row =  $(obj).closest(".transaction-detail-row");
          row.find("[name='supplierDetailName[]']").val("");
          row.find("[name='hidSupplierDetailKey[]']").val("");
          row.find("[name='tax23PercentageCostDetail[]']").attr("readonly",true).val(0);
          row.find("[name='taxPercentageCostDetail[]']").attr("readonly",true).val(0).change();
            
        }
          
        this.removeEmployeeOnChange = function removeEmployeeOnChange(obj){ 
          var row =  $(obj).closest(".transaction-detail-row");
          row.find("[name='employeeDetailName[]']").val("");
          row.find("[name='hidEmployeeDetailKey[]']").val("");
          row.find("[name='tax23PercentageCostDetail[]']").attr("readonly",false);
          row.find("[name='taxPercentageCostDetail[]']").attr("readonly",false);
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
         
       /* this.updateCostSupplier = function updateCostSupplier(){
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
         
        }*/
        
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
                     if (data[$(this).val()] == 0)
                         return true;
                     
                     $(this).closest(".transaction-detail-row").find("[name=\"requestAmount[]\"]").val(data[$(this).val()]).blur();
                 })  
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
              
             // enabled field kalo ad akses
             
             // hanya jika masi dalam status boleh edit
             <?php if ((!isset( $rs[0]['statuskey']) || in_array( $rs[0]['statuskey'], $obj->allowedStatusForEdit)) && $hasCostAccess) { ?>   
                 $("#" + tabID + " .fixed-cost-list").each(function() {
                     $(this).prop("readonly", !$(obj).prop("checked"));
                 }) 
             <?php } ?>
             
         }
         
         /*this.updateOutsourceAP = function updateOutsourceAP(){
             var outsourceCostObj = $("#"+tabID+" [name=outsourceCost]"); 
             var outsourceCost = parseInt(unformatCurrency(outsourceCostObj.val()));
             var outsourceDownpayment = 0;  
             var includeTax =   $("#" + tabID + " [name='chkIncludeTax']").val();
             var taxPercentage =  parseFloat(unformatCurrency($("#" + tabID + " [name='taxPercentage']").val())) || 0 ; 
               
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
         */
         
         this.calculateCostDetail = function calculateCostDetail(obj){
            var parentObj =  $(obj).closest(".transaction-detail-row");

            var itemkey =  parentObj.find("[name='hidCostKey[]']").val(); 
            if (itemkey == undefined) return;

            var qty =  unformatCurrency(parentObj.find("[name='qtyCostDetail[]']").val());
            var priceInUnit =  (parentObj.find("[name='hidIsRealization[]']").val() == 1) ? unformatCurrency(parentObj.find("[name='amount[]']").val()) : unformatCurrency(parentObj.find("[name='requestAmount[]']").val()); 
             
            var taxPercentage =  parseFloat(unformatCurrency(parentObj.find("[name='taxPercentageCostDetail[]']").val())) || 0; 
             
            var subtotal = qty * priceInUnit; 
            var taxValue = subtotal * (taxPercentage/100); 
        
            subtotal +=  taxValue;   
            parentObj.find("[name='subtotalCostDetail[]']").val(subtotal).blur(); 
 
         }
         
         this.calculateDetail = function calculateDetail(obj){     
            var parentObj =  $(obj).closest(".transaction-detail-row");

            var itemkey =  parentObj.find("[name='hidServiceDetailKey[]']").val(); 
            if (itemkey == undefined)
                return;

            var qty =  unformatCurrency(parentObj.find("[name='qtyDetail[]']").val());
            var priceInUnit =  unformatCurrency(parentObj.find("[name='priceDetail[]']").val()); 
            var taxPercentage =  parseFloat(unformatCurrency(parentObj.find("[name='taxPercentageDetail[]']").val())) || 0; 
             
            var subtotal = qty * priceInUnit; 
            var taxValue = subtotal * (taxPercentage/100); 
        
            subtotal +=  taxValue;   
            parentObj.find("[name='subtotalDetail[]']").val(subtotal).blur(); 

            truckingServiceWorkOrder.calculateTotal();
        }
	
        this.calculateTotal = function calculateTotal(){  
                var subtotal = 0; 
                $("#" + tabID + " [name='subtotalDetail[]']").each(function() {    
                        subtotal +=  parseInt(unformatCurrency($(this).val())) || 0; 
                }) 
                $("#" + tabID + " [name='total']").val(subtotal).blur(); 
         }
         
    }
    
    
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        truckingServiceWorkOrder = new TruckingServiceWorkOrder(tabID);
        setOnDocumentReady(tabID,truckingServiceWorkOrder);    
       
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
          
         
        $("#"+tabID+" [name=btnUpdateCost]").on('click', function() {
            truckingServiceWorkOrder.updateCost();
        });
     
        /*$("#"+tabID+" [name=outsourceCost], #"+tabID+" [name=total]").on('change', function() {
            truckingServiceWorkOrder.updateOutsourceAP();
        }); */
         
        
        objAndValue = new Array;
		objAndValue.push({object:'hidCostKey[]', value :'pkey'});  
        objAndValueForDetailAutoComplete[tabID] = objAndValue;  
        
        var objAndValueForDetailItemAutoComplete = {};    
		objAndValue = new Array;
		objAndValue.push({object:'hidServiceDetailKey[]', value :'pkey'});  
        objAndValueForDetailItemAutoComplete[tabID] = objAndValue;  
        
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
        
        $("#"+tabID+" [name=btnAddCarRows]").on('click', function() {
          	var newRow = addNewTemplateRow("car-row-template"); 
            bindAutoCompleteForTransactionDetail('serviceDetailName[]',objAndValueForDetailItemAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=0');   
        }); 
        
        <?php if (empty($rsCost)){ ?> 
            var newRow = addNewTemplateRow("cost-row-template");  
        <?php }  ?>
        <?php if (empty($rsCarDetail)){ ?> 
            var newRow = addNewTemplateRow("car-row-template");  
        <?php }  ?>

         bindAutoCompleteForTransactionDetail('costName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1&moduleCost=trucking');   
         bindAutoCompleteForTransactionDetail('supplierDetailName[]',objAndValueForSupplierDetailAutoComplete[tabID],'ajax-supplier.php?action=searchData');  
         bindAutoCompleteForTransactionDetail('employeeDetailName[]',objAndValueForEmployeeDetailAutoComplete[tabID],'ajax-employee.php?action=searchData');   
         bindAutoCompleteForTransactionDetail('serviceDetailName[]',objAndValueForDetailItemAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=0');   

});
	 
     

</script>

</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div>
  
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
      
        <?php prepareOnLoadDataForm($obj); ?>   
        <?php echo $obj->inputHidden('hidItemKey'); ?>
        <?php echo $obj->inputHidden('chkIsOutsource'); ?>
       
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
                                  
                                    <div class="form-group" style="display:none" >
                                        <label class="col-xs-3 control-label"></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selJobType',$arrJobType, array( 'allowedStatusForEdit' => array (1), 'etc' => ' onChange="truckingServiceWorkOrder.updateCost()"')); ?> 
                                        </div> 
                                    </div>   
                                  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['services']; ?></label> 
                                        <div class="col-xs-9"> 
                                           <?php  echo  $obj->inputSelect('hidSODetailKey', $arrContainer,  array( 'allowedStatusForEdit' => array (1), 'etc' => ' onChange="truckingServiceWorkOrder.updateContainerChange()"')); ?> 
                                        </div> 
                                    </div>  
                                    <div class="form-group"> 
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
                                   <!--  <div class="form-group ">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['truckingFee']; ?></label> 
                                        <div class="col-xs-9">
                                            <div class="flex">
                                                <div class="consume"> <?php echo $obj->inputNumber('outsourceCost',array('readonly' => true)); ?></div>
                                                <div style="padding-left:1em"> <?php echo $obj->lang['PPN']; ?></div>
                                                <div> <?php echo $obj->inputNumber('taxValue',array('readonly' => true)); ?> </div>
                                            </div>
                                           
                                        </div>  
                                    </div>  -->
                                     <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo $obj->lang['note']; ?></label> 
                                        <div class="col-xs-9"> 
                                               <?php echo  $obj->inputTextArea('trDesc', array( 'etc' => 'style="height:8em;"')); ?>
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
                            <div class="div-table-caption border-blue"><?php echo $obj->lang['stuffingInformation']; ?></div>
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
                                <div class="col-xs-9"> 
                                    <div class="flex">
                                     <div class="consume"><?php echo $obj->inputText('routeFrom',array( 'allowedStatusForEdit' => array (1))); ?></div>
                                     <div>-</div>
                                     <div class="consume"><?php echo $obj->inputText('routeTo',array( 'allowedStatusForEdit' => array (1))); ?></div>
                                    </div>
                                </div>   
                            </div> 
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo $obj->lang['productDescription']; ?></label> 
                                <div class="col-xs-9">     
                                   <?php echo  $obj->inputTextArea('productDescription', array('etc' => 'style="height:8em;"')); ?>
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
 	               </div>   
                         
           </div>
      </div> 
      
      
      <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="width:60px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?> </div>
                    <div class="div-table-col detail-col-header" style="text-align:left;"><?php echo ucwords($obj->lang['service']); ?> </div>
                    <div class="div-table-col detail-col-header" style="width:120px;"><?php echo ucwords($obj->lang['carRegistrationNumber']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:left;"><?php echo ucwords($obj->lang['containerNumber']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:150px; text-align:left;"><?php echo ucwords($obj->lang['sealNumber']); ?> </div>
                    <div class="div-table-col detail-col-header" style="width:100px; text-align:right;"><?php echo ucwords($obj->lang['price']); ?> </div>
                    <div class="div-table-col detail-col-header" style="width:70px; text-align:right;"><?php echo ucwords($obj->lang['PPN']); ?> %</div>
                    <div class="div-table-col detail-col-header" style="width:70px; text-align:right;"><?php echo ucwords($obj->lang['tax23']); ?> %</div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['total']); ?> </div>

                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:45px;border:1;"></div>
                </div>
                
				<?php  
                      $totalRows = count($rsCarDetail); 
                      for ($i=0;$i<=$totalRows; $i++){  

                            $class =  'transaction-detail-row';
                            $style = '';
                            $overwrite = true;
                            $etc = ''; 

                            $statusStyle = '';
                            $detail = '';
 
                            $cashedOutIcon = '';
                            $readonlyPurchase = false;
                            
                            if ($i == $totalRows ){
                                $class = 'car-row-template';
                                $style = 'style="display:none"';
                                $overwrite = false;
                                $etc = 'disabled="disabled"';  
                            } else { 
                                $_POST['hidOutsourceVehicleDetailKey[]'] = $rsCarDetail[$i]['pkey'];
                                $_POST['carRegistration[]'] = $rsCarDetail[$i]['carregistrationnumber'];
                                $_POST['hidServiceDetailKey[]'] = $rsCarDetail[$i]['itemkey'];
                                $_POST['serviceDetailName[]'] = $rsCarDetail[$i]['itemname'];  
                                $_POST['containerDetail[]'] = $rsCarDetail[$i]['container'];  
                                $_POST['sealDetail[]'] = $rsCarDetail[$i]['seal'];  
                                $_POST['qtyDetail[]'] = $obj->formatNumber($rsCarDetail[$i]['qty']);
                                $_POST['priceDetail[]'] = $obj->formatNumber($rsCarDetail[$i]['price']);
                                $_POST['taxPercentageDetail[]'] = $obj->formatNumber($rsCarDetail[$i]['taxpercentage']);
                                $_POST['tax23PercentageDetail[]'] = $obj->formatNumber($rsCarDetail[$i]['tax23percentage']);
                                $_POST['subtotalDetail[]'] = $obj->formatNumber($rsCarDetail[$i]['total']);
                                //$_POST['chkIsTax23Detail[]'] =  $rsCarDetail[$i]['istax23'] ; 
                                
                                $readonlyPurchase = ($rsCarDetail[$i]['purchaseorderkey'] <> 0) ? true : false;
                            }
                    ?>
            
                
                <div class="div-table-row  <?php echo $class; ?>" <?php echo $style; ?> >
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputHidden('hidOutsourceVehicleDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc, 'readonly' => $readonlyPurchase)); ?>
                        <?php echo $obj->inputNumber('qtyDetail[]', array('overwritePost' => $overwrite,   'etc' => 'style="text-align:right;" onChange="truckingServiceWorkOrder.calculateDetail(this)" ' .$etc , 'readonly' => $readonlyPurchase)); ?>
                    </div>
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('serviceDetailName[]', array('overwritePost' => $overwrite,    'etc' => $etc, 'readonly' => $readonlyPurchase )); ?>
                        <?php echo $obj->inputHidden('hidServiceDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc, 'readonly' => $readonlyPurchase)); ?>
                    </div>
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('carRegistration[]',array('overwritePost' => $overwrite, 'etc' => $etc , 'readonly' => $readonlyPurchase)); ?>
                     </div>
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('containerDetail[]', array('overwritePost' => $overwrite,  'etc' => $etc, 'readonly' => $readonlyPurchase )); ?>
                   </div>
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('sealDetail[]', array('overwritePost' => $overwrite,  'etc' => $etc, 'readonly' => $readonlyPurchase )); ?>
                   </div> 
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputNumber('priceDetail[]', array('overwritePost' => $overwrite,  'etc' => 'style="text-align:right;" onChange="truckingServiceWorkOrder.calculateDetail(this)" ' .$etc, 'readonly' => $readonlyPurchase )); ?>
                    </div>
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputNumber('taxPercentageDetail[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;" onChange="truckingServiceWorkOrder.calculateDetail(this)" ' .$etc, 'readonly' => $readonlyPurchase )); ?>
                    </div>
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputNumber('tax23PercentageDetail[]', array('overwritePost' => $overwrite, 'etc' => 'style="text-align:right;"' .$etc, 'readonly' => $readonlyPurchase )); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputNumber('subtotalDetail[]', array('overwritePost' => $overwrite, 'readonly' => true,   'etc' => 'style="text-align:right;" ' .$etc )); ?>
                    </div>  
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"> 
                        <?php  if (!$readonlyPurchase)
                                    echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"')); ?>
                   </div>
                    
                </div>
                 
            <?php } ?> 
                   
        </div> 
        <div style="clear:both; height:1em;"></div> 
        <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddCarRows', $obj->lang['addRows'], array('class' => 'btn btn-primary btn-second-tone')); ?></div>
      
        <div style="float:right;" > 
            <div class="div-table">
               <div class="div-table-row  form-group"> 
                    <div class="div-table-col-3" style="width:120px;"> 
                        <?php echo $obj->inputNumber('total', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                    </div>
                    <div class="div-table-col-3 <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:45px;"></div>
                </div>
            </div>
            <div style="clear: both;"></div>
      </div> 
      
      
      
      <div style="clear:both; height:4em;"></div>
      
      <div class="div-table transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row"> 
                    <div class="div-table-col detail-col-header" style="width:60px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?> </div> 
                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['costName']); ?></div>
                    <div class="div-table-col detail-col-header" style="width:160px; text-align:left;"><?php echo ucwords($obj->lang['employee']); ?> <span class="text-muted">(<?php echo ucwords($obj->lang['recipient']); ?>)</span></div>
                    <div class="div-table-col detail-col-header" style="width:160px; text-align:left;"><?php echo ucwords($obj->lang['supplier']); ?> <span class="text-muted">(<?php echo ucwords($obj->lang['recipient']); ?>)</span></div>
                    <div class="div-table-col detail-col-header" style="width:90px; text-align:right;"><?php echo ucwords($obj->lang['cost']); ?> </div>
                    <?php if ($showRealizationCost) { ?>
                    <div class="div-table-col detail-col-header" style="width:90px; text-align:right; padding-left:0;"><?php echo ucwords($obj->lang['realization']); ?> </div>
                    <?php } ?>
                    <div class="div-table-col detail-col-header" style="width:70px; text-align:right;"><?php echo ucwords($obj->lang['PPN']); ?> %</div>
                    <div class="div-table-col detail-col-header" style="width:70px; text-align:right;"><?php echo ucwords($obj->lang['tax23']); ?> %</div>
					<div class="div-table-col detail-col-header" style="width:20px; text-align:center">R</div>
                    <div class="div-table-col detail-col-header" style="width:120px; text-align:right;"><?php echo ucwords($obj->lang['total']); ?> </div>
                    <div class="div-table-col detail-col-header  <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:45px;border:1;"></div>
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
                            
                            $tax23Readonly = false;
                            $readonlyPurchase = false;
                          
                            // khusus checkbox
                            //$_POST['chkIsTax23CostDetail[]']  = 0;
                          
                            if ($i == $totalRows ){
                                $class = 'cost-row-template';
                                $style = 'style="display:none"';
                                $overwrite = false;
                                $etc = 'disabled="disabled"';  
                            } else {     
                                
                                 if ($rsCost[$i]['realizationkey'] <> 0 || !empty($rsCost[$i]['refcashoutkey'])){
                                      $readonlyOnFixedCost = true;
                                      $fixedCostClass = 'fixed-cost-list';
                                  }

                                $_POST['hidDetailKey[]'] = $rsCost[$i]['pkey'];
                                $_POST['qtyCostDetail[]'] =  $obj->formatNumber($rsCost[$i]['qty']);
                                $_POST['taxPercentageCostDetail[]'] =  $obj->formatNumber($rsCost[$i]['taxpercentage']);
                                $_POST['tax23PercentageCostDetail[]'] =  $obj->formatNumber($rsCost[$i]['tax23percentage']);
                                //$_POST['chkIsTax23CostDetail[]'] =  $rsCost[$i]['istax23'];
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
                                $_POST['subtotalCostDetail[]'] =  $obj->formatNumber($rsCost[$i]['total']);
                                $_POST['hidIsRealization[]'] = $rsCost[$i]['isrealization'];
                                $_POST['isReimburse[]'] = $rsCost[$i]['isreimburse'];

                                if(empty($_POST['hidSupplierDetailKey[]']))
                                    $tax23Readonly = true;
                                    
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
                                
                                if ($rsCost[$i]['realizationkey'] <> 0 || ($rsCost[$i]['purchaseorderkey'] <> 0))  $deleteIcon = '';
                                 
                                $readonlyPurchase = ($rsCost[$i]['purchaseorderkey'] <> 0) ? true : false;
                            }

                          //overwrite kalo gk punya akses
                          if (!$hasCostAccess)
                               $readonlyOnFixedCost = true;
 

                    ?>
            
                
                <div class="div-table-row  <?php echo $class; ?>" <?php echo $style; ?> >
                       
                   <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputHidden('hidDetailKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                        <?php echo $obj->inputHidden('hidIsRealization[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?> 
                       <?php echo $obj->inputNumber('qtyCostDetail[]', array('overwritePost' => $overwrite,'readonly' => ($readonlyOnFixedCost || $readonlyPurchase),   'etc' => 'style="text-align:right;" onChange="truckingServiceWorkOrder.calculateCostDetail(this)" ' .$etc )); ?>
                   </div>  
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('costName[]',array('overwritePost' => $overwrite,'readonly' =>  ($readonlyOnFixedCost || $readonlyPurchase), 'class' => 'form-control ' . $fixedCostClass, 'etc' => $etc )); ?>
                        <?php echo $obj->inputHidden('hidCostKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?>
                    </div> 
                     <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('employeeDetailName[]', array('overwritePost' => $overwrite, 'readonly' =>  ($readonlyOnFixedCost || $readonlyPurchase), 'class' => 'form-control  ' . $fixedCostClass, 'etc' => $etc )); ?>
                        <?php echo $obj->inputHidden('hidEmployeeDetailKey[]',array('overwritePost' => $overwrite, 'etc' => ' onChange="truckingServiceWorkOrder.removeSupplierOnChange(this)" '.$etc)); ?>
                    </div>
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputText('supplierDetailName[]', array('overwritePost' => $overwrite, 'readonly' =>  ($readonlyOnFixedCost || $readonlyPurchase), 'class' => 'form-control  ' . $fixedCostClass, 'etc' => $etc )); ?>
                        <?php echo $obj->inputHidden('hidSupplierDetailKey[]',array('overwritePost' => $overwrite, 'etc' => ' onChange="truckingServiceWorkOrder.removeEmployeeOnChange(this)" '. $etc)); ?>
                    </div>
                   
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputNumber('requestAmount[]', array('overwritePost' => $overwrite, 'readonly' =>  ($readonlyOnFixedCost || $readonlyPurchase), 'class' => 'form-control inputnumber ' . $fixedCostClass, 'etc' => 'style="text-align:right;" onChange="truckingServiceWorkOrder.calculateCostDetail(this)" ' .$etc )); ?>
                    </div>
                    <?php if ($showRealizationCost) { ?> 
                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputNumber('amount[]', array('overwritePost' => $overwrite, 'readonly' => true, 'class' => 'form-control inputnumber ' . $fixedCostClass, 'etc' => 'style="text-align:right;" ' .$etc )); ?></div>
                    <?php } ?>
                    
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputNumber('taxPercentageCostDetail[]', array('overwritePost' => $overwrite, 'readonly' => ($readonlyOnFixedCost || $readonlyPurchase || $tax23Readonly), 'etc' => 'style="text-align:right;" onChange="truckingServiceWorkOrder.calculateCostDetail(this)" ' .$etc )); ?>
                    </div>
                    <div class="div-table-col detail-col-detail" style="text-align:center">
                       <?php echo $obj->inputNumber('tax23PercentageCostDetail[]', array('overwritePost' => $overwrite, 'readonly' => ($readonlyOnFixedCost || $readonlyPurchase || $tax23Readonly), 'etc' => 'style="text-align:right;" onChange="truckingServiceWorkOrder.calculateCostDetail(this)" ' .$etc )); ?>
                     </div>
					<div class="div-table-col detail-col-detail" style="text-align:center">
                        <?php echo $obj->inputCheckBox('isReimburse[]', array('overwritePost' => $overwrite, 'readonly' =>  ($readonlyOnFixedCost || $readonlyPurchase), 'etc' => $etc )); ?>
                    </div> 
                    <div class="div-table-col detail-col-detail">
                        <?php echo $obj->inputNumber('subtotalCostDetail[]', array('overwritePost' => $overwrite, 'readonly' => true,   'etc' => 'style="text-align:right;" ' .$etc )); ?>
                    </div>  
                    <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(); ?> icon-col"> 
                        <?php echo $obj->inputHidden('hidRefCashOutKey[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?> 
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
