<?php 

require_once '../../../_config.php'; 
require_once '../../../_include-v2.php';

includeClass('TruckingServiceOrder.class.php');
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$consignee = createObjAndAddToCol(new Consignee());   
$customer = createObjAndAddToCol(new Customer());   
$customerDownpayment = createObjAndAddToCol(new CustomerDownpayment());   
$depot = createObjAndAddToCol(new Depot());   
$location = createObjAndAddToCol(new Location());   
$supplier = createObjAndAddToCol(new Supplier());   
$truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE,1));   
$truckingSellingRate = createObjAndAddToCol(new TruckingSellingRate());   
$truckingServiceOrderCategory = createObjAndAddToCol(new TruckingServiceOrderCategory());   
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());   
$terminal = createObjAndAddToCol(new Terminal());   
$vessel = createObjAndAddToCol(new Vessel());   
$warehouse = createObjAndAddToCol(new Warehouse());   

$obj= $truckingServiceOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
$sellingPriceAllowed = $security->isAdminLogin($truckingServiceOrder->sellingPriceSecurityObject, 10);

$overwriteContractAllowed = $security->isAdminLogin($truckingServiceOrder->overwriteContractSecurityObject,10);

$formAction = 'truckingServiceOrderList';

$isQuickAdd = ( isset($_GET) && !empty($_GET['quickadd'])) ? true : false;
$partyDecimal = $obj->loadSetting('jobOrderPartyDecimal'); 
if (empty($partyDecimal)) $partyDecimal = 0;

if (!empty($partyDecimal)){
	$qtyWidth = '100px';
	$attrDecimal = 'mnv-attr-decimal='.$partyDecimal;
}else{
	$qtyWidth = '60px';
	$attrDecimal = '';
}
 
$rsSalesHeaderCost = array();
$rsSalesDetail = array();
$rsSalesDetailCost = array();
$arrShowInvoiced = array(5);
$showInvoicedQty = false;
$isCustomerInvoiced = false;
 
//$arrContract = array();

$defaultShipmentDate = date('d / m / Y 00:00');
$_POST['trDate'] = date('d / m / Y'); 
 
$finalDiscDecimal = 0;
$finalDiscDecimalType = 'inputnumber';

//$rsCost = $truckingCost->searchData($truckingCost->tableName.'.statuskey',1, true, '','order by fixedcost desc, name asc');  
  
// get status color
$rsStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','textcolor'); 
    
$rs = prepareOnLoadData($obj); 
$rsContactPerson = array(); 
$rsInvoice = array();
$showRealizationCost = $obj->useRealization();
        
$editWarehouseInactiveCriteria = '';
$totalIssuedClass = '';

if (!empty($_GET['id'])){ 
	$id = $_GET['id'];	  
    $rsSalesDetail = $obj->getDetailWithRelatedInformation($id);
    $rsSalesDetailCost = $obj->getSellingCostDetail($id);
    $rsSalesHeaderCost = $obj->getHeaderCost($id);
    $rsContactPerson = $obj->getContactPerson($id);
    $rsInvoice = $obj->getInvoiceInformation($id);
    
    if(!empty($rsInvoice))
        $isCustomerInvoiced = true;
    
    $showInvoicedQty = (in_array($rs[0]['statuskey'], $arrShowInvoiced)) ? true : false;
        
    $rsDeliveryAddress = $customer->getAvailableAddress($rs[0]['customerkey'],array(1));
	
    if (!empty($rsDeliveryAddress)){
        $_POST['hidCustomerLocationKey'] = $rsDeliveryAddress[0]['locationkey'] ;  
        $_POST['customerLocationName'] = $rsDeliveryAddress[0]['locationname'] ; 
        $_POST['customerAddress'] = $rsDeliveryAddress[0]['address'] ;   
    }   
    
	$_POST['trDate'] = $obj->formatDBDate($rs[0]['trdate'],'d / m / Y '); 
	//$_POST['selContract'] = $rs[0]['contractkey'];   
   
    $_POST['hidContractKey'] = $rs[0]['contractkey'] ;  
    $rsContract = $truckingSellingRate->getDataRowById($rs[0]['contractkey']); 
    if (!empty($rsContract))
        $_POST['contractName'] = $rsContract[0]['name'] ;
    
    $_POST['stuffingAddress'] =  $rs[0]['stuffingaddress']; 

	if (!empty($rs[0]['customerkey'])){
        $_POST['hidCustomerKey'] = $rs[0]['customerkey'] ;  
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']); 
        $_POST['customerName'] = $rsCustomer[0]['name'] ;
    } 
    
	if (!empty($rs[0]['saleskey'])){
        $_POST['hidSalesKey'] = $rs[0]['saleskey'] ;  
        $rsEmployee = $employee->getDataRowById($rs[0]['saleskey']); 
        $_POST['salesName'] = $rsEmployee[0]['name'] ;
    } 
    
    if (!empty($rs[0]['depotkey'])){
        $_POST['hidDepotKey'] = $rs[0]['depotkey']; 
		$rsDepo = $depot->getDataRowById($rs[0]['depotkey']);
		$_POST['depotName'] = $rsDepo[0]['name'];
	}
        
    if (!empty($rs[0]['terminalkey'])){
        $_POST['hidTerminalKey'] = $rs[0]['terminalkey']; 
		$rsUTC = $terminal->getDataRowById($rs[0]['terminalkey']);
		$_POST['terminalName'] = $rsUTC[0]['name'];
	}
        
	// data kategori, job type dan consignee hanya boleh keupdate infonya ketika open form yg blm di konfirmasi
    // kalo sudah konfirmasi gk boleh keupdate, karena data sudah tdk boleh berubah

    $categorykey = $rs[0]['categorykey'];
    $cargotypekey = $rs[0]['cargotypekey'];
    $consigneekey = $rs[0]['consigneekey'];

    $warehousename = '';
    $contactperson = '';
    
    if ($rs[0]['statuskey'] == 1){
        
        if (!$overwriteContractAllowed){  
            $rsTariff = $truckingSellingRate->getDataRowById($rs[0]['contractkey']);
            if(!empty($rsTariff)){ 
                $consigneekey = $rsTariff[0]['consigneekey'];
                $categorykey = $rsTariff[0]['categorykey'];
                $cargotypekey = $rsTariff[0]['cargotypekey'];
            }
        }  
        
        // update ulang
        $rsConsignee = $consignee->getDataRowById($consigneekey); 
        if (!empty($rsConsignee)){ 
            $warehousename = $rsConsignee[0]['warehousename'];
            $contactperson = $rsConsignee[0]['contactperson']; 
            $locationkey = $rsConsignee[0]['locationkey']; 
        } 
        
    }else{
        $rsConsignee = $consignee->getDataRowById($consigneekey); 
        $warehousename = $rs[0]['consigneewarehousename'];
        $contactperson = $rs[0]['consigneecontactperson'];
        $locationkey = $rs[0]['consigneelocationkey']; 
    }
     
    $address =  $rs[0]['consigneeaddress'];  
    
    $_POST['hidConsigneeKey'] = $consigneekey;   
    $_POST['consigneeName'] = (isset($rsConsignee) && !empty($rsConsignee)) ? $rsConsignee[0]['name'] : '';  
    $_POST['warehouseName'] = $warehousename;
	$_POST['contactPerson'] = $contactperson; 
	$_POST['address'] =  $address; 
      
    if (!empty($locationkey)){ 
        $_POST['hidLocationKey'] = $locationkey;    
        $rsLocation = $location->searchData($location->tableName.'.pkey',$locationkey,true);
        $_POST['locationName'] = $rsLocation[0]['name'] ;
    }
    
    
    $stuffingLocationKey = $rs[0]['stuffinglocationkey'];
    if (!empty($stuffingLocationKey)){ 
        $_POST['hidStuffingLocationKey'] = $stuffingLocationKey;    
        $rsLocation = $location->searchData($location->tableName.'.pkey',$stuffingLocationKey,true);
        $_POST['stuffingLocationName'] = $rsLocation[0]['name'] ;
    }


    $stuffingLocationFromKey = $rs[0]['stuffinglocationfromkey'];
    
    if(!empty($stuffingLocationFromKey)) {
        $_POST['hidStuffingLocationFromKey'] = $stuffingLocationFromKey;
        $rsLocation = $location->searchData($location->tableName.'.pkey',$stuffingLocationFromKey,true);
        $_POST['stuffingLocationFromName'] = $rsLocation[0]['name'];
    }

    $_POST['hidCategoryKey'] = $categorykey; 
    $rsCategory = $truckingServiceOrderCategory->getDataRowById($categorykey);
    $_POST['categoryName'] = $rsCategory[0]['name']; 
     
    $_POST['hidCargoType'] = $cargotypekey; 
    //$rsJob = $obj->getTruckingJobType($cargotypekey);
    //$_POST['jobTypeName'] = $rsJob[0]['name']; 

    
	$_POST['doNumber'] = $rs[0]['donumber'];
	$_POST['aju'] = $rs[0]['aju'];
	$_POST['mbl'] = $rs[0]['mbl'];
	
    $_POST['shipmentNumber'] = $rs[0]['shipmentnumber'];
	$_POST['poReference'] = $rs[0]['poreference'];
    
	$_POST['trDesc'] = $rs[0]['trdesc']; 
	$_POST['subtotal'] = $obj->formatNumber($rs[0]['subtotal']);  

    if ($rs[0]['finaldiscounttype']  == 2){ 
        $finalDiscDecimal = 2;
        $finalDiscDecimalType = 'inputdecimal';
    } 

	$_POST['selFinalDiscountType'] = $rs[0]['finaldiscounttype'] ;
	$_POST['finalDiscount'] = $obj->formatNumber($rs[0]['finaldiscount'],$finalDiscDecimal); 
	$_POST['total'] = $obj->formatNumber($rs[0]['grandtotal']); 
 
    $rsKey = $obj->getTableKeyAndObj($obj->tableName);
    $rsDP = $customerDownpayment->getDownpaymentList('',array('refkey' => $id, 'reftabletype' => $rsKey['key']));
    $totalDP = 0;
    foreach($rsDP as $dp)
        $totalDP += $dp['amount'];
        
    $_POST['downpayment'] = $obj->formatNumber($totalDP); 
      
    $totalInvoice = 0;
    foreach($rsInvoice as $invoice) 
        $totalInvoice += $invoice['amount']; 
    
    $_POST['totalInvoiced'] = $obj->formatNumber($totalInvoice) ; 
    
    $totalIssuedClass = ($totalInvoice < $rs[0]['grandtotal']) ? 'text-red-cardinal' : 'text-green-avocado';
    
	$_POST['beforeTaxTotal'] =  $obj->formatNumber($rs[0]['beforetaxtotal']); 
	$_POST['totalHeaderCost'] =  $obj->formatNumber($rs[0]['totalheadercost']);  
	$_POST['totalCost'] =  $obj->formatNumber($rs[0]['totalsellingcost']);  
	
	if(!empty($rs[0]['ispriceincludetax'])) 
	   $isPriceIncludeTax = 'checked="checked"';
   
	if(!empty($rs[0]['useinsurance'])) 
	   $useInsurance = 'checked="checked"';
	
	$_POST['taxPercentage'] = $obj->formatNumber($rs[0]['taxpercentage'],2);
	$_POST['taxValue'] = $obj->formatNumber($rs[0]['taxvalue']); 
	$_POST['selTermOfPaymentKey'] = $rs[0]['termofpaymentkey'] ;
	$_POST['balance'] =  $obj->formatNumber($rs[0]['balance']) ; 
    
    if (!empty($rs[0]['plannerkey'])){ 
        $_POST['hidPlannerKey'] =  $rs[0]['plannerkey'] ;
        $rsEmployee = $employee->getDataRowById($rs[0]['plannerkey']);
        $_POST['plannerName'] = $rsEmployee[0]['name'];
        
        
        $_POST['hidBeforePlannerKey'] = $rs[0]['plannerkey']; 
    }
    
    $_POST['hidVesselKey'] = $rs[0]['vesselkey'];
	if(!empty($rs[0]['vesselkey'])){
        $rsVessel = $vessel->getDataRowById($rs[0]['vesselkey']);
        $_POST['vesselName'] = $rsVessel[0]['name'];
    }
    
	$_POST['routeFrom'] =  $rs[0]['routefrom'];
	$_POST['routeTo'] =   $rs[0]['routeto'];  
    $_POST['selWarehouseKey'] =$rs[0]['warehousekey']; 
    $_POST['vesselNumber'] = $rs[0]['vesselnumber'];    
    $editWarehouseInactiveCriteria = ' or '.$warehouse->tableName.'.pkey = ' . $obj->oDbCon->paramString($rs[0]['warehousekey']); 
	     
	//update file 
	$rsItemFile = $obj->getItemFile($id);
		
	if(count($rsItemFile) > 0){
		$sourcePath = $obj->defaultDocUploadPath.$obj->uploadFileFolder.$id;
		$destinationPath = $obj->uploadTempDoc.$obj->uploadFileFolder.$id; 
		$obj->deleteAll($destinationPath); 
	
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
				
		$obj->fullCopy($sourcePath,$destinationPath);  
	} 
  
}
  
$arrStatus = $obj->convertForCombobox($obj->getAllStatus(),'pkey','status');      
$arrCargoType = $obj->convertForCombobox($obj->getCargoType(),'pkey','name');    
$arrWarehouse = $class->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1' .$editWarehouseInactiveCriteria.')'),'pkey','name'); 

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<title></title> 
 
<script type="text/javascript">  
   
	
    function TruckingServiceOrder(tabID) {  
        
         this.updateDetail = function updateDetail(target,objAndValue,ui){
                var detailRow = $(target).closest(".transaction-detail-row"); 
				  
                for(i=0;i<objAndValue.length;i++){   
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 

                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemName[]\"]").first().val(ui.item['value']);   
                truckingServiceOrder.updateDetailInformation (detailRow); 

         }
         

         this.updateDetailHeader = function updateDetailHeader(target,objAndValue,ui){
                var detailRow = $(target).closest(".transaction-detail-row"); 
				  
                for(i=0;i<objAndValue.length;i++){   
                    detailRow.find("[name='" + objAndValue[i].object +"']").first().val(ui.item[objAndValue[i].value]).blur();  
                } 

                // harus handle manual utk obj autosearch
                detailRow.find("[name=\"itemNameHeaderCost[]\"]").first().val(ui.item['value']);   
                truckingServiceOrder.updateCostInformation (detailRow); 

         }
         this.updateAllDetailInformation = function updateAllDetailInformation(){
             $(".trucking-service .transaction-detail-row").each(function(){   
                  truckingServiceOrder.updateDetailInformation($(this));
             })    
         }
         
         this.updateDetailInformation = function updateDetailInformation(detailRow){ 
             
                //var contractkey = $("#" + tabID + " [name=selContract]").val();
                var contractkey = $("#" + tabID + " [name=hidContractKey]").val();
                var itemkey = detailRow.find("[name=\"hidItemKey[]\"]").val();
                
                if (!itemkey) return;
              
                var obj = detailRow.find(".status-label");
                $(obj).addClass("text-green-avocado");
                
                //update price
                 $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-selling-rate.php',
                    async: false,
                    data: "action=getDetail&contractkey=" + contractkey + "&itemkey=" + itemkey ,  
                }).done(function( data ) { 
                    
                    if(data.length == 0)
                        return;
                     
                    data = JSON.parse(data) ; 
                     
                    if (data.length > 0){  
                        data = data[0];   
                        price = data.price;  
                    }else{
                        price = 0;
                    }
                     
                    detailRow.find("[name=\"price[]\"]").val(price).blur().change(); 
                    detailRow.find(".status-label").html("Open");  
                });  
                
         } 
            
        this.updateCostInformation = function updateCostInformation(detailRow){ 
                var terminalkey = $("#" + tabID + " [name=hidTerminalKey]").val();
                var depotkey = $("#" + tabID + " [name=hidDepotKey]").val();
                var jobcategorykey = $("#" + tabID + " [name=hidCategoryKey]").val();
                var itemkey = detailRow.find("[name=\"hidItemKeyHeaderCost[]\"]").val(); 
                var serviceRow = $("#" + tabID + " .trucking-service .transaction-detail-row");
                
                var arrService = []; 
                serviceRow.each(function() {   
                  arrService.push({"qty":$(this).find("[name=\"qty[]\"]").val(), "servicekey":$(this).find("[name=\"hidItemKey[]\"]").val()});
                });
            
                if (!itemkey) return;

                //update price
                 $.ajax({
                    type: "GET",
                    url:  'ajax-item.php',
                    async: false,
                    data: "action=getTruckingCostDefaultPrice&terminalkey=" + terminalkey + "&depotkey=" + depotkey + "&jobcategorykey="+jobcategorykey+"&itemkey=" + itemkey + "&servicedetail="+JSON.stringify(arrService),  
                }).done(function( data ) { 
                     
                    data = JSON.parse(data) ;  
                    price = data.amount;
                      
                    detailRow.find("[name=\"requestPriceHeaderCost[]\"]").val(price).blur().change(); 
                });   
         }         
        
        this.updateConsigneeInformation = function updateConsigneeInformation(){
            var consigneekey = $("#" + tabID + " [name=hidConsigneeKey]").val();
             
            $("#" + tabID + " [name=consigneeName]").val('');
            $("#" + tabID + " [name=warehouseName]").val('');
            $("#" + tabID + " [name=contactPerson]").val('');
            $("#" + tabID + " [name=hidLocationKey]").val('');   
            $("#" + tabID + " [name=locationName]").val('');
            $("#" + tabID + " [name=address]").val('');
            $("#" + tabID + " [name=hidConsigneeKey]").val('');

            $.ajax({
                    type: "GET",
                    url:  'ajax-consignee.php',
                    async: false,
                    data: "action=getDataRowById&pkey=" + consigneekey ,  
            }).done(function( data ) { 
                    data = JSON.parse(data) ;  
                    //var parser = new DOMParser;
                 
                    if (data.length > 0){

                        data = data[0];

                        $("#" + tabID + " [name=hidConsigneeKey]").val(data.pkey);
                        $("#" + tabID + " [name=consigneeName]").val(data.name);
                        $("#" + tabID + " [name=warehouseName]").val(data.warehousename);
                        $("#" + tabID + " [name=contactPerson]").val(data.contactperson);
                        $("#" + tabID + " [name=hidLocationKey]").val(data.locationkey);   
                        $("#" + tabID + " [name=locationName]").val(data.locationname);   
                        // $("#" + tabID + " [name=hidStuffingLocationKey]").val(data.locationkey);   
                        // $("#" + tabID + " [name=stuffingLocationName]").val(data.locationname);
                        $("#" + tabID + " [name=address]").val(decodeHTMLEntities(data.address));  
                    }
            }) 
            
             truckingServiceOrder.updateStuffingLocation();
                    
        }
         
        this.updateContractRelatedInformation = function updateContractRelatedInformation(){ 

                loadOverlayScreen({content: _LOADING_TEMPLATE_});
                TruckingServiceOrder.activeAjaxConnections = 0;

                $.ajax({
                    type: "GET",
                    url:  'ajax-trucking-selling-rate.php',
                    beforeSend:function (xhr){ 
                        TruckingServiceOrder.activeAjaxConnections++; 
                    }, 
                    async: false,
                    data: "action=getDataRowById&pkey=" + $("#" + tabID + " [name=hidContractKey]").val() ,  
                }).done(function( data ) { 
                      
                    $("#" + tabID + " [name=hidConsigneeKey]").val(0);
                    $("#" + tabID + " [name=hidCargoType]").val(0);  
                    $("#" + tabID + " [name=hidCategoryKey").val(0);
                    $("#" + tabID + " [name=categoryName]").val("");
                    $("#" + tabID + " [name=hidLocationKey]").val(0);

                    data = JSON.parse(data) ;  
                       
                    if (data.length > 0){ 
                        data = data[0];  

                        $("#" + tabID + " [name=hidConsigneeKey]").val(data.consigneekey);
                        $("#" + tabID + " [name=hidCargoType]").val(data.cargotypekey);  
                        $("#" + tabID + " [name=hidCategoryKey]").val(data.categorykey);
                        $("#" + tabID + " [name=categoryName]").val(data.categoryname);
                        $("#" + tabID + " [name=hidLocationKey]").val(data.locationkey);
                    }
                      
                    $("#" + tabID + " [name=hidCargoType]").find("option:selected").attr('disabled', false);
                    $("#" + tabID + " [name=hidCargoType]").find("option:not(:selected)").attr('disabled', true);
                  
                     truckingServiceOrder.updateConsigneeInformation();
                     truckingServiceOrder.updateAllDetailInformation();
                    
                    //revalidate field kategori
                    $obj = $("#" + tabID + " [name=categoryName]");
                    $obj.closest('form').bootstrapValidator('revalidateField', $obj.attr("name")); 
                    
                    decreaseActiveAjaxConnections(TruckingServiceOrder); 
                }); 
        }       
 
    
	this.calculateDetail = function calculateDetail(obj){     
        var parentObj =  $(obj).closest(".transaction-detail-row");

        var itemkey =  parentObj.find("[name='hidItemKey[]']").val(); 
        if (itemkey == undefined)
            return;

        var qty =  unformatCurrency(parentObj.find("[name='qty[]']").val());
        var priceInUnit =  unformatCurrency(parentObj.find("[name='price[]']").val()); 
  
        var subtotal = qty * priceInUnit; 
        parentObj.find("[name='subtotal[]']").val(subtotal).blur(); 
 
        truckingServiceOrder.calculateTotalSales();
    }
	
	this.calculateTotalSales = function calculateTotalSales(){  
            var subtotal = 0; 
            $("#" + tabID + " [name='subtotal[]']").each(function() {    
                    subtotal +=  parseInt(unformatCurrency($(this).val())) || 0; 
            }) 
            $("#" + tabID + " [name='subtotal']").val(subtotal).blur();
         
         
            truckingServiceOrder.calculateTotal(); 
	 }
    
     
	this.calculateHeaderCost = function calculateHeaderCost(obj){     
        var parentObj =  $(obj).closest(".transaction-detail-row");

        var itemkey =  parentObj.find("[name='hidItemKeyHeaderCost[]']").val(); 
        if (itemkey == undefined)
            return;

        var costAmountObj = (parentObj.find("[name='priceHeaderCost[]']")) ? parentObj.find("[name='requestPriceHeaderCost[]']") : parentObj.find("[name='priceHeaderCost[]']") ;
        
        var qty =  unformatCurrency(parentObj.find("[name='qtyHeaderCost[]']").val());
        var priceInUnit =  unformatCurrency(costAmountObj.val()); 
        //parentObj.find("[name='priceHeaderCost[]']").val(priceInUnit).blur(); 
  
        var subtotal = qty * priceInUnit; 
        parentObj.find("[name='subtotalHeaderCost[]']").val(subtotal).blur();  
        
        truckingServiceOrder.calculateTotalHeaderCost();
    }
    
    
	this.calculateTotalHeaderCost = function calculateTotalHeaderCost(){  
            var subtotal = 0; 
            $("#" + tabID + " [name='subtotalHeaderCost[]']").each(function() {    
                    subtotal +=  parseInt(unformatCurrency($(this).val())) || 0; 
            })

            $("#" + tabID + " [name='totalHeaderCost']").val(subtotal).blur();
        
            truckingServiceOrder.calculateCostSummary(); 
	 }
    
    this.calculateCostSummary = function calculateCostSummary(){   
             
            var totalHeaderCost = parseInt(unformatCurrency($("#" + tabID + " [name='totalHeaderCost']").val())); 
            var totalInHouseCost = parseInt(unformatCurrency($("#" + tabID + " .inhouse-cost").html()));
            var totalOutsourceCost = parseInt(unformatCurrency($("#" + tabID + " .outsource-cost").html()));  
        
            var totalBilledCost = totalHeaderCost + totalInHouseCost + totalOutsourceCost;
        
            var totalSellingCost = parseInt(unformatCurrency($("#" + tabID + " [name='total']").val())); 
            var balance = totalSellingCost - totalBilledCost;
        
            $("#" + tabID + " [name='totalBilledCost']").val(totalBilledCost).blur(); 
        
            var balanceCostObj =  $("#" + tabID + " [name='balanceCost']");
            balanceCostObj.val(balance).blur();  
        
            var percentageCost = (totalSellingCost-totalBilledCost)  / totalSellingCost * 100; 
            //$("#" + tabID + " .percentage-cost").html(percentageCost).formatCurrency({roundToDecimalPlace: 2 });
         
            balanceCostObj.removeClass("text-red-cardinal").removeClass("text-green-avocado");
        
            if (balance < 0)
                balanceCostObj.addClass("text-red-cardinal");
            else if (balance > 0)
                balanceCostObj.addClass("text-green-avocado");
        
    }
    
	this.calculateDetailCost = function calculateDetailCost(obj){     
        var parentObj =  $(obj).closest(".transaction-detail-row");

        var itemkey =  parentObj.find("[name='hidItemKeyCost[]']").val(); 
        if (itemkey == undefined)
            return;

        var qty =  unformatCurrency(parentObj.find("[name='qtyCost[]']").val());
        var priceInUnit =  unformatCurrency(parentObj.find("[name='priceCost[]']").val()); 
  
        var subtotal = qty * priceInUnit; 
        parentObj.find("[name='subtotalCost[]']").val(subtotal).blur();   
 
        truckingServiceOrder.calculateTotalCost();
    }
     
	
	this.calculateTotalCost = function calculateTotalCost(){  
            var subtotal = 0; 
            $("#" + tabID + " [name='subtotalCost[]']").each(function() {    
                    subtotal +=  parseInt(unformatCurrency($(this).val())) || 0; 
            })

            $("#" + tabID + " [name='totalCost']").val(subtotal).blur();
         
            truckingServiceOrder.calculateTotal();  
	 }
    
    
	this.calculateTotal = function calculateTotal(){  
        var totalSales =   parseInt(unformatCurrency($("#" + tabID + " [name='subtotal']").val()));
        var totalCost =   parseInt(unformatCurrency($("#" + tabID + " [name='totalCost']").val()));
        
        var total = totalSales + totalCost; 
        $("#" + tabID + " [name='total']").val(total).blur(); 
        truckingServiceOrder.calculateCostSummary();
    }
    
    this.onChangeCustomer = function onChangeCustomer(){
        
        var customerkey = $("#" + tabID + " [name=hidCustomerKey]").val();
 
        $("#" + tabID + " [name=hidSalesKey]").val("");  
        $("#" + tabID + " [name=salesName]").val("");   

        // update stuffing location
        $.ajax({
                type: "GET",
                url:  'ajax-customer.php',
                async: false,
                data: "action=getDeliveryAddress&addresstype=1&pkey=" +customerkey ,  
            }).done(function( data ) { 
 
                data = JSON.parse(data) ;  
                if (data.length > 0 ){ 
                    data = data[0];   

                    $("#" + tabID + " [name=hidCustomerLocationKey]").val(data.locationkey);  
                    $("#" + tabID + " [name=customerLocationName]").val(data.locationname);   
                    $("#" + tabID + " [name=customerAddress]").val(data.address);   
                }
                truckingServiceOrder.updateStuffingLocation();

        }); 
        
        // update salesman
         $.ajax({
                type: "GET",
                url:  'ajax-customer.php',
                async: false,
                data: "action=getSalesman&pkey=" + customerkey ,  
            }).done(function( data ) {  
                if (!data ) return;
              
                data = JSON.parse(data) ;  
                if ( data.length  == 0  ) return;
             
                $("#" + tabID + " [name=hidSalesKey]").val(data.pkey);  
                $("#" + tabID + " [name=salesName]").val(data.name);    
                

        }); 

    }
    
    this.onChangePlanner = function onChangePlanner(){ 
        var plannerkey = $("#" + tabID + " [name=hidPlannerKey]").val();
        var plannername = $("#" + tabID + " [name=plannerName]").val();
        
        var beforePlannerKey = $("#" + tabID + " [name=hidBeforePlannerKey]").val();
        
        $("#" + tabID + " [name='hidDetailEmployeeKey[]']").each(function() {  
            if ($(this).val() == 0 || $(this).val() == beforePlannerKey) { 
                $(this).val(plannerkey);
                $(this).closest("div").find("[name='detailEmployeeName[]']").val(plannername);
            }
        })
        
        $("#" + tabID + " [name=hidBeforePlannerKey]").val(plannerkey);
    }
    
    this.updateStuffingLocation = function updateStuffingLocation(){
        $customerLocationKey = $("#" + tabID + " [name=hidCustomerLocationKey]").val();
        $customerLocationName = $("#" + tabID + " [name=customerLocationName]").val();
        $customerAddress = $("#" + tabID + " [name=customerAddress]").val();
        $consigneeLocationKey = $("#" + tabID + " [name=hidLocationKey]").val();
        $consigneeLocationName = $("#" + tabID + " [name=locationName]").val();
        $consigneeAddress = $("#" + tabID + " [name=address]").val();
        
       if ($consigneeLocationKey != ""){  
            // $("#" + tabID + " [name=hidStuffingLocationKey]").val($consigneeLocationKey);
            // $("#" + tabID + " [name=stuffingLocationName]").val($consigneeLocationName);
            $("#" + tabID + " [name=stuffingAddress]").val($consigneeAddress);
        } else {  
            // $("#" + tabID + " [name=hidStuffingLocationKey]").val($customerLocationKey);
            // $("#" + tabID + " [name=stuffingLocationName]").val($customerLocationName);
            $("#" + tabID + " [name=stuffingAddress]").val($customerAddress);
        }
    }  
        
    }
    
	jQuery(document).ready(function(){  
        var tabID = <?php echo ($isQuickAdd) ?  $_GET['tabID'] :  'selectedTab.newPanel[0].id';  ?>  
        truckingServiceOrder = new TruckingServiceOrder(tabID);
        setOnDocumentReady(tabID);   
		  
         
		/// FILE UPLOADER 
		var fileFolder = "<?php echo $obj->uploadFileFolder; ?>"; 
		var fileUploaderTarget = "item-file-uploader"; 
		var arrFile = Array();
		   
        <?php   
			if (isset($id) && !empty($id)){   
         		for($i=0;$i<count($rsItemFile);$i++) 
					echo 'arrFile.push("'.$rsItemFile[$i]['file'].'"); '; 
					
				echo 'createFileUploader(fileUploaderTarget,fileFolder,'.$id.',arrFile,true);';  
				
			}else{ 
				echo 'createFileUploader(fileUploaderTarget,fileFolder,"","",true);'; 
			}
		?>
          
		$( "." + fileUploaderTarget + " .file-list" ).sortable({  placeholder: "sortable-placeholder" ,stop: function( event, ui ) { updateItemFileArray(fileUploaderTarget); }});
		$( "." + fileUploaderTarget + " .file-list"  ).disableSelection();
		 
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
			
			   categoryName : { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.category[1]
                        }
                    } 
                }, 
             
                /*
                plannerName : { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.employee[3]
                        }
                    } 
                },*/ 
                
			   customerName: { 
                    validators: {
                        notEmpty: {
                            message:  phpErrorMsg.customer[1]
                        }
                    } 
                }, 
			 
            }
        })
        .on('success.form.bv', function(e) { 
              <?php echo $obj->submitFormScript(); ?> 
        });
        
        $( "#" + tabID + " .section-panel .title" ).click(function() {  
            $(this).closest(".section-panel").find(".section-panel-content").first().toggle();
			$(this).find(".icon-expand").toggle();
        });
        
        $("#"+tabID+" [name=btnShowDetail]").on('click', function() {
            var $obj = $("#" + tabID +" .div-detail-information"); 
             
            if ($obj.is(":visible")){ 
                $obj.css('display','none');
                $(this).html("<?php echo $obj->lang['showDetail']; ?>");
            }else{ 
                $obj.css('display','table');
                $(this).html("<?php echo $obj->lang['hideDetail']; ?>"); 
            }
             
        });
        
        $( "#" + tabID + " [name=btnAddReimburseCost]" ).click(function() {  
            var reimbursementCost = new Array;
            	  
            // get all reimburesement
            $("#" + tabID + " [name='hidWorkOrderIsReimburse[]']").each(function() {  
                if ($(this).val() == 0) return;
                 
                $row = $(this).closest("div");
                
                reimburseCostKey = $row.find("[name='hidWorkOrderCostKey[]']").val(); 
		        reimbursementCost.push({costkey :reimburseCostKey, 
                                        costname : $row.find("[name='hidWorkOrderCostName[]']").val(),
                                        qty : $row.find("[name='hidWorkOrderQty[]']").val(),
                                        amount : $row.find("[name='hidWorkOrderCost[]']").val(),
                                        subtotal : $row.find("[name='hidWorkOrderSubtotal[]']").val() 
                                       });  
	    
                // delete first 
                $("#" + tabID + " [name='hidItemKeyCost[]']").each(function() {  
                    if ($(this).val() != reimburseCostKey) return; 
                    $(this).closest(".div-table-row").find("[name=btnDeleteRows]").click(); 
                })
            })
            
            
            // header cost reimbusement
             $("#" + tabID + " [name='hidHeaderCostIsReimburse[]']").each(function() {  
                if ($(this).val() == 0) return;
                 
                $row = $(this).closest(".div-table-row");
                
                reimburseCostKey = $row.find("[name='hidItemKeyHeaderCost[]']").val(); 
                 
                var costAmountObj = ($row.find("[name='priceHeaderCost[]']").val() != undefined) ? $row.find("[name='priceHeaderCost[]']") : $row.find("[name='requestPriceHeaderCost[]']") ;
                var amount = unformatCurrency(costAmountObj.val());
                var qty =  $row.find("[name='qtyHeaderCost[]']").val();
                var subtotal = amount * qty;
                    
                    
		        reimbursementCost.push({costkey :reimburseCostKey, 
                                        costname : $row.find("[name='itemNameHeaderCost[]']").val(),
                                        qty : qty,
                                        amount : amount,
                                        subtotal : subtotal 
                                       });  
	    
                // delete first 
                $("#" + tabID + " [name='hidItemKeyCost[]']").each(function() {  
                    if ($(this).val() != reimburseCostKey) return; 
                    $(this).closest(".div-table-row").find("[name=btnDeleteRows]").click(); 
                })
            })

            
            // add reimburement 
            for(i=0;i<reimbursementCost.length;i++){  
                 $firstCostKey = $( "#" + tabID).find("[name=\"hidItemKeyCost[]\"]").first().val();
                 if ( $firstCostKey == "" || $firstCostKey == 0){   
                       $( "#" + tabID).find("[name=\"qtyCost[]\"]").first().val(reimbursementCost[i].qty).blur();
                       $( "#" + tabID).find("[name=\"hidItemKeyCost[]\"]").first().val(reimbursementCost[i].costkey);
                       $( "#" + tabID).find("[name=\"itemNameCost[]\"]").first().val(reimbursementCost[i].costname);
                       $( "#" + tabID).find("[name=\"priceCost[]\"]").first().val(reimbursementCost[i].amount).blur();
                       $( "#" + tabID).find("[name=\"subtotalCost[]\"]").first().val(reimbursementCost[i].subtotal).blur(); 
                  }else{
                        var arrPostValue = []; 
                        arrPostValue.push({"selector":"qtyCost", "value":reimbursementCost[i].qty});
                        arrPostValue.push({"selector":"hidItemKeyCost", "value":reimbursementCost[i].costkey}); 
                        arrPostValue.push({"selector":"itemNameCost", "value":reimbursementCost[i].costname}); 
                        arrPostValue.push({"selector":"priceCost", "value":reimbursementCost[i].amount}); 
                        arrPostValue.push({"selector":"subtotalCost", "value":reimbursementCost[i].subtotal}); 

                        $newRow = addNewTemplateRow("detail-cost-row-template",JSON.stringify(arrPostValue)); 
                        $newRow.find(".inputnumber").blur();
                  } 

            }
            
            truckingServiceOrder.calculateTotalCost();
        });
        
	   
        $("#" + tabID + " .form-detail-button").click(function() {  
            var objName = $(this).attr("relobj");
            
            $("#" + tabID + " ." + objName).toggle();
            var temp = $(this).attr("relalt");   
            $(this).attr("relalt",$(this).text());
            $(this).text(temp);

        }); 
		  
		objAndValue = new Array;
		objAndValue.push({object:'hidItemKey[]', value :'pkey'});  
        objAndValueForDetailAutoComplete[tabID] = objAndValue;  
	     
		// DETAIL CLONE
		 $("#"+tabID+" [name=btnAddRows]").on('click', function() {
          	var newRow = addNewTemplateRow("detail-row-template");
             
            newRow.find(".input-datetime").removeClass("hasDatepicker");
            newRow.find(".input-datetime").removeAttr("id"); 
            newRow.find(".input-datetime").datetimepicker({  currentText: 'Now', dateFormat:'dd / mm / yy',  changeMonth: true, changeYear: true }); 
             
			bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=0','truckingServiceOrder.updateDetail');  
              
        });
		 
        
        var objAndValueForDetailCostAutoComplete = {};    
		objAndValue = new Array;
		objAndValue.push({object:'hidItemKeyCost[]', value :'pkey'});  
        objAndValueForDetailCostAutoComplete[tabID] = objAndValue;  
        
        var objAndValueForEmployeeHeaderCostAutoComplete = {};    
		objAndValue = new Array;
		objAndValue.push({object:'hidDetailEmployeeKey[]', value :'pkey'});  
        objAndValueForEmployeeHeaderCostAutoComplete[tabID] = objAndValue;  
        $("#"+tabID+" [name=btnAddCostRows]").on('click', function() {
          	var newRow = addNewTemplateRow("detail-cost-row-template"); 
			bindAutoCompleteForTransactionDetail('itemNameCost[]',objAndValueForDetailCostAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1');   
        });
        
        
        var objAndValueForHeaderCostAutoComplete = {};    
		objAndValue = new Array;
		objAndValue.push({object:'hidItemKeyHeaderCost[]', value :'pkey'});  
		objAndValue.push({object:'hidHeaderCostIsReimburse[]', value :'reimburse'});  
        objAndValueForHeaderCostAutoComplete[tabID] = objAndValue;  
         
        $("#"+tabID+" [name=btnAddHeaderCostRows]").on('click', function() {
          	var newRow = addNewTemplateRow("cost-row-template"); 
			bindAutoCompleteForTransactionDetail('itemNameHeaderCost[]',objAndValueForHeaderCostAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1','truckingServiceOrder.updateDetailHeader');    
      	bindAutoCompleteForTransactionDetail('detailEmployeeName[]',objAndValueForEmployeeHeaderCostAutoComplete[tabID],'ajax-employee.php?action=searchData');        
});
		
		   
    <?php if (empty($_GET['id']) || empty($rsSalesDetail) ){ ?> 
        var newRow = addNewTemplateRow("detail-row-template");
        newRow.find(".input-datetime").removeClass("hasDatepicker");
        newRow.find(".input-datetime").removeAttr("id");
        newRow.find(".input-datetime").datetimepicker({  currentText: 'Now', dateFormat:'dd / mm / yy',  changeMonth: true, changeYear: true }); 
    <?php }  ?>
        
    
    <?php if (isset($_POST['selStatus']) && ($_POST['selStatus'] >= 2)){ ?>     
        $( "#" + tabID + " .section-panel .title" ).click();
    <?php } ?>    
          
    <?php if (empty($rsSalesDetailCost) ){ ?>     
        var newRow = addNewTemplateRow("detail-cost-row-template"); 
    <?php } ?>    
    <?php if (empty($rsSalesHeaderCost) ){ ?>     
        var newRow = addNewTemplateRow("cost-row-template"); 
    <?php } ?>    
            
    bindAutoCompleteForTransactionDetail('itemName[]',objAndValueForDetailAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=0','truckingServiceOrder.updateDetail');   
    bindAutoCompleteForTransactionDetail('itemNameCost[]',objAndValueForDetailCostAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1');   
    bindAutoCompleteForTransactionDetail('itemNameHeaderCost[]',objAndValueForHeaderCostAutoComplete[tabID],'ajax-item.php?action=searchData&itemtype=2&serviceCost=1', 'truckingServiceOrder.updateDetailHeader'); 
    bindAutoCompleteForTransactionDetail('detailEmployeeName[]',objAndValueForEmployeeHeaderCostAutoComplete[tabID],'ajax-employee.php?action=searchData');
   
    truckingServiceOrder.calculateCostSummary(); 
        
    //$("#" + tabID + " .transaction-detail-row").each(function() { truckingServiceOrder.updateCostInformation($(this)); })
 
}); 

</script>

<style>
    .cost-detail .service-name{font-weight: bold}
    .cost-detail .recipient{color:#666; font-style: italic}
    .cost-detail .div-table-col-03 {vertical-align: middle}
    .summary {background-color: #cbddce; border-radius: 0.5em; padding: 1em} 
</style>
</head> 

<body>                    
<div style="width:100%; margin:auto; " class="tab-panel-form">   
  <div class="notification-msg"></div> 
  <form id="defaultForm" method="post" class="form-horizontal" action="<?php echo $formAction; ?>" >
        <?php prepareOnLoadDataForm($obj); ?>   
        <?php echo $obj->inputHidden('hidSendEmail'); ?>
        <?php echo $obj->inputHidden('hidCreditLimit'); ?>
     
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
                                            <?php echo $obj->inputAutoCode('code', array('allowedStatusForEdit' => array(1))); ?>
                                        </div> 
                                    </div>  
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['date']); ?></label> 
                                        <div class="col-xs-9">  
                                            <?php echo $obj->inputDate('trDate', array('allowedStatusForEdit' => array(1))); ?> 
                                        </div> 
                                    </div>   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['warehouse']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputSelect('selWarehouseKey', $arrWarehouse, array('allowedStatusForEdit' => array(1)) ); ?>  
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['poReference']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo $obj->inputText('poReference', array('readonly' => $isCustomerInvoiced)); ?>
                                        </div> 
                                    </div>     
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo strtoupper($obj->lang['si']); ?></label> 
                                        <div class="col-xs-9"> 
											<?php echo $obj->inputText('doNumber', array('readonly' => $isCustomerInvoiced)); ?>
                                        </div> 
                                    </div> 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['planner']); ?></label>  
                                        <div class="col-xs-9"> 
                                         <?php    
                                            
                                                echo $obj->inputHidden('hidBeforePlannerKey');
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $employee,
                                                                                'revalidateField' => false, 
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
                                                                                                    ),
                                                                                'allowedStatusForEdit' => array(1), 
                                                                                'callbackFunction' =>  'truckingServiceOrder.onChangePlanner()'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>   
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['customer']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php     
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $customer,
                                                                                        'readonly' => $isCustomerInvoiced, 
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
                                                                                        'callbackFunction' =>  'truckingServiceOrder.onChangeCustomer()'
                                                                                      )
                                                                                ); 
                                                    echo $obj->inputHidden('customerLocationName');
                                                    echo $obj->inputHidden('hidCustomerLocationKey');
                                                    echo $obj->inputHidden('customerAddress');
                                            ?>  
                                        </div> 
                                    </div> 
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['salesman']); ?></label> 
                                        <div class="col-xs-9"> 
                                              <?php     
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'objRefer' => $employee,
                                                                                        'revalidateField' => true, 
                                                                                        'element' => array('value' => 'salesName',
                                                                                                           'key' => 'hidSalesKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-employee.php',
                                                                                                            'data' => array(  'action' =>'searchData', 'issales' => 1 )
                                                                                                        ) , 
                                                                                        'allowedStatusForEdit' => array(1)
                                                                                      )
                                                                                );  
                                            ?>  
                                        </div> 
                                    </div> 
                                 
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['masterRates']); ?></label> 
                                        <div class="col-xs-9">
                                            <?php //echo  $obj->inputSelect('selContract', $arrContract, array ( 'etc' => 'onChange="truckingServiceOrder.updateContractRelatedInformation()"') ); ?>
                                             <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $truckingSellingRate,
                                                                                'revalidateField' => false, 
                                                                                'element' => array('value' => 'contractName',
                                                                                                   'key' => 'hidContractKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-trucking-selling-rate.php',
                                                                                                    'data' => array('action' =>'searchData')
                                                                                                ) ,
                                                    
                                                                                'allowedStatusForEdit' => array(1),
                                                                                'callbackFunction' => 'truckingServiceOrder.updateContractRelatedInformation()'
                                                                              )
                                                                        );  
                                            ?> 
                                        </div> 
                                    </div>    
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['typeOfJob']); ?></label> 
                                        <div class="col-xs-3">  
                                            <?php echo $obj->inputSelect('hidCargoType', $arrCargoType, array('etc' => 'style="padding-right:0"', 'readonly' => !$overwriteContractAllowed, 'allowedStatusForEdit' => array (1) )); ?> 
                                        </div> 
                                        <div class="col-xs-6" style="padding-left:0"> 
                                         <?php    
                                                echo $obj->inputAutoComplete(array(
                                                                                'objRefer' => $truckingServiceOrderCategory,
                                                                                'revalidateField' => true, 
                                                                                'element' => array('value' => 'categoryName',
                                                                                                   'key' => 'hidCategoryKey'),
                                                                                'source' =>array(
                                                                                                    'url' => 'ajax-trucking-service-order-category.php',
                                                                                                    'data' => array(  'action' =>'searchData' )
                                                                                                ) ,
                                                                                'allowedStatusForEdit' => array (1),
                                                                                'readonly' => !$overwriteContractAllowed
                                                                              )
                                                                        );  
                                            
                                                            /*                    'popupForm' => array(
                                                                                                        'url' => 'serviceOrderCategoryForm.php',
                                                                                                        'element' => array('value' => 'categoryName',
                                                                                                               'key' => 'hidCategoryKey'),
                                                                                                        'width' => '600px',
                                                                                                        'title' => ucwords($obj->lang['add'] . ' - ' . $obj->lang['serviceOrderCategory'])
                                                                                                    )*/
                                            ?> 
                                        </div> 
                                    </div>   
                                   
                                    <div class="form-group">
                                        <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['note']); ?></label> 
                                        <div class="col-xs-9"> 
                                            <?php echo  $obj->inputTextArea('trDesc', array('etc' => 'style="height:10em;"')); ?>
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
                            <div class="div-table-caption border-blue"><?php echo ucwords($obj->lang['stuffingDestuffingInformation']); ?></div>
                         
                            
                            <!-- <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['route']); ?></label> 
                                <div class="col-xs-9" > 
                                    <div class="flex">
                                        <div class="consume"><?php //echo $obj->inputText('routeFrom', array('readonly' => $isCustomerInvoiced)); ?></div>
                                        <div> - </div> 
                                        <div class="consume"><?php //echo $obj->inputText('routeTo', array('readonly' => $isCustomerInvoiced)); ?></div>
                                    </div>
                                </div> 
                            </div>  -->
                             
              
              
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['location']); ?></label>
                                <div class="col-xs-9">
                                    <div class="flex"> 
                                        <div class="consume">
                                            <?php    
                                                    echo $obj->inputAutoComplete(array(
                                                                        'obbjRefer' => $location, 
                                                                        'revalidateField' => false, 
                                                                        'element' => array('value' => 'stuffingLocationFromName',
                                                                                            'key' => 'hidStuffingLocationFromKey'),
                                                                                'source' =>array(
                                                                                        'url' => 'ajax-location.php',
                                                                                        'data' => array(  'action' =>'searchData' )
                                                                                    ) , 
                                                                            'allowedStatusForEdit' => array(1) 
                                                                        ));  
                                            ?> 
                                        </div>
                                        <div> - </div>
                                        <div class="consume"> 
                                            <?php    
                                                    echo $obj->inputAutoComplete(array(
                                                                                        'obbjRefer' => $location, 
                                                                                        'revalidateField' => false, 
                                                                                        'element' => array('value' => 'stuffingLocationName',
                                                                                                           'key' => 'hidStuffingLocationKey'),
                                                                                        'source' =>array(
                                                                                                            'url' => 'ajax-location.php',
                                                                                                            'data' => array(  'action' =>'searchData' )
                                                                                        ) , 
                                                                                        'allowedStatusForEdit' => array(1) 
                                                                                      )
                                                                                );  
                                            ?>
                                        </div> 
                                    </div>
                                </div> 
                             </div>
                             
                            <div class="form-group">
                                <label class="col-xs-3 control-label"><?php echo ucwords($obj->lang['address']); ?></label> 
                                <div class="col-xs-9"> 
                                    <?php echo  $obj->inputTextArea('stuffingAddress', array('etc' => 'style="height:10em;"',  'allowedStatusForEdit' => array(1))); ?>
                                </div> 
                            </div> 
                        </div>   
                         
                         <?php if (!empty($rsInvoice)) { ?> 
                              <div class="div-tab-panel">  
                                 <div class="div-table-caption border-pink"><?php echo ucwords($obj->lang['invoice']); ?></div> 
                                 <div class="div-table" style="width:100%"> 
                                      <div class="div-table-row"> 
                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; width:150px;" > 
                                            <strong><?php echo ucwords($obj->lang['invoiceNumber']); ?></strong>
                                         </div> 
                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666;  width:120px; text-align:center" > 
                                            <strong><?php echo ucwords($obj->lang['date']); ?></strong>
                                         </div> 
                                         <div class="div-table-col-5" style="border-top:1px solid #666;border-bottom:1px solid #666; text-align:right" > 
                                            <strong><?php echo ucwords($obj->lang['amount']); ?></strong>
                                         </div> 
                                     </div> 
                                     <?php for ($i=0;$i<count($rsInvoice);$i++){  
                                            $code = $rsInvoice[$i]['code'] ;
                                            $code .= ($rsInvoice[$i]['isdownpayment']) ? ' *' : '';
                                     ?>
                                             <div class="div-table-row">   
                                                    <div class="div-table-col-5" style="border-bottom:1px solid #dedede;"><?php echo $code; ?></div> 
                                                    <div class="div-table-col-5" style="text-align:center; border-bottom:1px solid #dedede;"><?php echo $obj->formatDBDate($rsInvoice[$i]['trdate']) ?></div>  
                                                    <div class="div-table-col-5" style="text-align:right; border-bottom:1px solid #dedede;"><?php echo $obj->formatNumber($rsInvoice[$i]['amount']) ?></div>  
                                                </div> 
                                     <?php } ?> 
                                  </div>     
                                  <div class="tag-list text-muted" style="font-style:italic">*) <?php echo ucwords($obj->lang['partialInvoice']); ?></div>
                             </div>  

                       <?php } ?>
                    </div>
           </div>
      </div> 
      
        <div style="float:right; display:inline-block; margin-bottom:1em"><?php echo $obj->inputButton('btnShowDetail',ucwords($obj->lang['showDetail']),array('allowedStatusForEdit' => array(), 'class' =>'btn btn-primary btn-second-tone')); ?></div>
        <div class="div-table trucking-service transaction-detail" style="width:100%; border-bottom:1px solid #333; ">
                <div class="div-table-row">  
                    <div class="div-table-col" style="padding:0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row"> 
                                    <div class="div-table-col detail-col-header"  style="width:30px; text-align:right;">#</div>
                                    <div class="div-table-col detail-col-header"  style="width:<?php echo $qtyWidth; ?>; text-align:right;"><?php echo ucwords($obj->lang['party']); ?></div>
                                    <?php if ($showInvoicedQty) {?><div class="div-table-col detail-col-header"  style="width:40px;"></div><?php } ?>
                                    <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['services']); ?></div>  
                                    <!--<div class="div-table-col detail-col-header" style="width:170px;"><?php echo ucwords($obj->lang['note']); ?></div> -->
                                    <div class="div-table-col detail-col-header" style="width:140px; text-align:center"><?php echo ucwords($obj->lang['serviceWorkOrderDate']); ?></div>   
  									<?php
                                    if ($sellingPriceAllowed) {
                                    ?>   
                                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right; "><?php echo ucwords($obj->lang['price']); ?></div>
                                        <div class="div-table-col detail-col-header" style="width:100px; text-align:right; "><?php echo ucwords($obj->lang['subtotal']); ?></div>
                                    <?php } ?>
                                 <!--   <div class="div-table-col detail-col-header" style="width:90px; text-align:center"><?php echo ucwords($obj->lang['status']); ?></div> -->
                                    <div class="div-table-col detail-col-header" style="width:40px; text-align:center"><?php echo ucwords($obj->lang['group']); ?></div>
                                    <div class="div-table-col detail-col-header <?php echo $obj->hideOnDisabled(array(1,2)); ?> icon-col"  ></div> 
                            </div>
                        </div>    
                    </div>  
                </div>
                
				<?php 
            
                    $totalRows = count($rsSalesDetail); 
                  
                    for ($i=0;$i<=$totalRows; $i++){  
                        
                        $class =  'transaction-detail-row';
                        $overwrite = true;
                        $disabled = false; 
                        $readonly = false; 
                        
                        $statusStyle = '';
                        $detail = 'Tidak ada data.';
                        
                        $rowNumber = 1;
                            
                        $readonly = !$overwriteContractAllowed;
                        $readonlyServices = !$obj->inAllowedStateToUpdateServices($rs[0]['statuskey'],$rsSalesDetail[$i]['pkey']) ?  array(1) : array(1,2);  
                        //$readonlyServices = array(1,2); // untuk testing
                        
						
                        if ($i == $totalRows ){
                            $class = 'detail-row-template';
                            $overwrite = false;
                            $disabled = true; 
                            $unitname = 'Pcs';
                            $statusName = '-';
                            $qtyInvoiced = 0;
                            $qtyInvoicedClass = 'text-muted';
                            $readonlyServices = array(1,2);
                        } else {   
                            
                            //$readonly = !$overwriteContractAllowed;
                            
                            // gk bisa pake readonyl == false soalnya buat trdate
                             
                            // kalo sudah ad qty invoiced, readonly = true;
                            if ($rsSalesDetail[$i]['qtyinvoiced'] > 0) $readonly = true; 
                            
                            $rowNumber = $rsSalesDetail[$i]['numberkey'];
                            
                            $qty = $rsSalesDetail[$i]['qtyinbaseunit'];
                            $qtyInvoiced = $rsSalesDetail[$i]['qtyinvoiced'];  
                            
                            $_POST['hidDetailKey[]'] = $rsSalesDetail[$i]['pkey'];
                            $_POST['hidItemKey[]'] = $rsSalesDetail[$i]['itemkey'];
                            $_POST['itemName[]'] = $rsSalesDetail[$i]['itemname']; 
                            $_POST['trShipmentDate[]'] = $obj->formatDBDate($rsSalesDetail[$i]['trdate'],'d / m / Y H:i'); 
                            $_POST['price[]'] = $obj->formatNumber($rsSalesDetail[$i]['priceinunit']);
                            $_POST['qty[]'] = $obj->formatNumber($qty,$partyDecimal);
                            $_POST['subtotal[]'] = $obj->formatNumber($rsSalesDetail[$i]['total']);
                            $_POST['detailNotes[]'] =  $rsSalesDetail[$i]['trdesc']; 
                            $_POST['chkIsGroup[]'] =  $rsSalesDetail[$i]['isgroup'];
                            
                            $qtyInvoicedClass = ($qtyInvoiced < $qty) ? 'text-red-cardinal' : 'text-muted';
                            $statusName =  $rsSalesDetail[$i]['statusname'];  
                            $statusStyle =  $rsSalesDetail[$i]['class']; 

                            $rsDetailWorkOrder =$truckingServiceWorkOrder->getWorkOrderInformationForJobOrder($rsSalesDetail[$i]['pkey'],'','order by stuffingdatetime asc'); // $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refdetailkey',$rsSalesDetail[$i]['pkey'],true,' and ' .$truckingServiceWorkOrder->tableName.'.statuskey in (1,2,3) ', 'order by stuffingdatetime asc');
 
							
                            if(!empty($rsDetailWorkOrder)){ 

                                $detail  = '<div class="div-table gray-scheme" style="width:100%">';
                                $detail .= '<div class="div-table-row" style="font-weight:bold">';
                                $detail .= '<div class="div-table-col col-header" style="width: 30px; text-align:right;">#</div>';
                                $detail .= '<div class="div-table-col col-header" style="width: 80px; ">'.$obj->lang['WOCode'].'</div>';
                                $detail .= '<div class="div-table-col col-header" style="width: 120px; text-align:center; ">'.$obj->lang['date'].'</div>';
                                $detail .= '<div class="div-table-col col-header" style="width: 160px; ">'.$obj->lang['driver'].' / <span class="text-blue-munsell">'.$obj->lang['supplier'].'</span></div>';  
                                $detail .= '<div class="div-table-col col-header" style="width: 120px; ">'.$obj->lang['car'].'</div>'; 
                                $detail .= '<div class="div-table-col col-header" style="width: 100px; ">'.$obj->lang['container'].'</div>'; 
                                $detail .= '<div class="div-table-col col-header" style="width: 80px; text-align:center ">'.$obj->lang['status'].'</div>'; 
                                $detail .= '<div class="div-table-col col-header">'.$obj->lang['note'].'</div>'; 
                                $detail .= '</div>';

                                for($j=0;$j<count($rsDetailWorkOrder);$j++){

                                    $arrTemp = array();
                                    if (!empty($rsDetailWorkOrder[$j]['containernumber'])) array_push($arrTemp,$rsDetailWorkOrder[$j]['containernumber']);
                                    if (!empty($rsDetailWorkOrder[$j]['container2number'])) array_push($arrTemp,$rsDetailWorkOrder[$j]['container2number']);
                                    $containerNumber = implode('<br>',$arrTemp );

//                                    $arrTemp = array();
//                                    if (!empty($rsDetailWorkOrder[$j]['sealnumber'])) array_push($arrTemp,$rsDetailWorkOrder[$j]['sealnumber']);
//                                    if (!empty($rsDetailWorkOrder[$j]['seal2number'])) array_push($arrTemp,$rsDetailWorkOrder[$j]['seal2number']);
//                                    $sealNumber = implode('<br>',$arrTemp );

                                    $detail .= '<div class="div-table-row">';
                                    $detail .= '<div class="div-table-col" style="text-align:right">'.($j+1).'.</div>';
                                    $detail .= '<div class="div-table-col">'.$rsDetailWorkOrder[$j]['code'].'</div>';
                                    $detail .= '<div class="div-table-col" style="text-align:center;">'.$obj->formatDBDate($rsDetailWorkOrder[$j]['stuffingdatetime'],'d / m / Y H:i').'</div>';

                                    $drivername = '';
                                    $policenumber = '';
                                    $chassisnumber = ''; 

                                    if ($rsDetailWorkOrder[$j]['isoutsource'] == 0){ 
                                        $drivername = (!empty($rsDetailWorkOrder[$j]['drivername'])) ? $rsDetailWorkOrder[$j]['drivername'] : '';
                                        $policenumber = (!empty($rsDetailWorkOrder[$j]['policenumber'])) ? $rsDetailWorkOrder[$j]['policecode']  . ' - ' . $rsDetailWorkOrder[$j]['policenumber'] : '';
//                                        $chassisnumber = (!empty($rsDetailWorkOrder[$j]['chassisnumber'])) ? $rsDetailWorkOrder[$j]['chassisnumber'] : '';  
                                    }else{ 
                                        $drivername = '<span class="text-blue-munsell">'.$rsDetailWorkOrder[$j]['suppliername'].'</span>';
                                        $policenumber = (!empty($rsDetailWorkOrder[$j]['outsourcecarregistrationnumber'])) ? $rsDetailWorkOrder[$j]['outsourcecarregistrationnumber'] : '';
                                     }
 
                                    $detail .= '<div class="div-table-col">'.$drivername.'</div>';  
                                    $detail .= '<div class="div-table-col">'.$policenumber.'</div>';  
                                    $detail .= '<div class="div-table-col">'.$containerNumber .'</div>'; 
                                    $detail .= '<div class="div-table-col" style="text-align:center; color: '.$rsStatus[$rsDetailWorkOrder[$j]['statuskey']]['label'].'">'.$rsDetailWorkOrder[$j]['statusname'].'</div>'; 
                                    $detail .= '<div class="div-table-col">'.$rsDetailWorkOrder[$j]['trdesc'] .'</div>'; 

                                    $detail .= '</div>';
                                }

                                $detail .= '</div>';
                            }  
                            
                            
                        }  
                         
                         
                ?>
            
                
                <div class="div-table-row <?php echo $class; ?>">
                    <div class="div-table-col"  style="padding: 0.3em 0">
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                <div class="div-table-col detail-col-detail" style="width:30px; text-align:right"><div class="row-number"></div></div>
                                <div class="div-table-col detail-col-detail" style="width:<?php echo $qtyWidth; ?>" > <?php echo $obj->inputNumber('qty[]', array('overwritePost' => $overwrite, 'value'=> 1,'disabled' => $disabled, 'etc' =>  'style="text-align:right;" onChange="truckingServiceOrder.calculateDetail(this)" ' .$attrDecimal )); ?></div>
                                <?php if ($showInvoicedQty) {?><div class="div-table-col detail-col-detail text-muted"  style="width:40px;">/ <span class="<?php echo $qtyInvoicedClass; ?>" style="text-align:right; width: 35px"><?php echo $obj->formatNumber($qtyInvoiced); ?></span></div><?php } ?>
                                <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemName[]',array('overwritePost' => $overwrite,'disabled' => $disabled,  'allowedStatusForEdit' => $readonlyServices,  'etc' =>  '  onChange="truckingServiceOrder.calculateDetail(this)"')); ?><?php echo $obj->inputHidden('hidItemKey[]',array('disabled' => $disabled, 'overwritePost' => $overwrite )); ?><?php echo $obj->inputHidden('hidDetailKey[]',array('disabled' => $disabled, 'overwritePost' => $overwrite )); ?></div> 
                                <div class="div-table-col detail-col-detail" style="width:140px;"><?php echo $obj->inputDateTime('trShipmentDate[]', array('value' => $defaultShipmentDate,'overwritePost' => $overwrite, 'disabled' => $disabled, 'allowedStatusForEdit' => $readonlyServices,  'etc' => 'style="text-align:center;" ' )); ?></div>
    <?php
                                if ($sellingPriceAllowed) {
                                ?>
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('price[]', array('overwritePost' => $overwrite,'readonly' => $readonly,  'disabled' => $disabled, 'etc' => 'style="text-align:right;"  onChange="truckingServiceOrder.calculateDetail(this)" ' )); ?></div>
                                <div class="div-table-col detail-col-detail" style="width:100px;"><?php echo $obj->inputNumber('subtotal[]', array('overwritePost' => $overwrite,'readonly' => true, 'disabled' => $disabled,  'etc' =>  'style="text-align:right;"'  )); ?></div>
                                <?php } ?>
                              <!--  <div class="div-table-col detail-col-detail status-label <?php echo $statusStyle; ?>" style="width:90px; text-align: center"><?php echo $statusName; ?></div> -->
                                <div class="div-table-col detail-col-detail" style="width:40px;  text-align:center"><?php echo $obj->inputCheckBox('chkIsGroup[]',array('overwritePost' => $overwrite, 'disabled' => $disabled, 'allowedStatusForEdit' => array (1))); ?></div>
                                <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(array(1,2)); ?> icon-col"  ><?php  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button','allowedStatusForEdit' => $readonlyServices, 'etc' => 'tabIndex="-1" attrhandler="truckingServiceOrder.calculateTotalSales()"')); ?></div>
                            </div> 
                        </div> 
                        <div class="div-table" style="width:100%">
                            <div class="div-table-row">
                                  <div class="div-table-col detail-col-detail" style="width:30px;"></div>
                                  <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('detailNotes[]',array('overwritePost' => $overwrite,'disabled' => $disabled , 'etc' => 'placeholder="'.$obj->lang['note'].'"')); ?></div> 
                                  <div class="div-table-col detail-col-detail status-label " style="width:130px; text-align: center"><label class=" <?php echo $statusStyle; ?>"><?php echo $statusName; ?></label></div> 
                                  <div class="div-table-col detail-col-detail  <?php echo $obj->hideOnDisabled(array(1,2)); ?> icon-col" ></div>
                            </div>
                        </div>
                        <div class="div-table div-detail-information" style="margin: 0.2em 0">
                            <div class="div-table-row"> 
                                <div class="div-table-col">  
                                        <div class="work-order-detail"><?php echo $detail; ?></div> 
                                </div>
                            </div>
                        </div>
                            
                    </div>
                </div>
            
            <?php } ?>
             
                   
         </div>         
      
      <div style="clear:both; height:1em;"></div> 
      <div style="float:left; display:inline-block;"><?php echo $obj->inputButton('btnAddRows',ucwords($obj->lang['addRows']), array('class' =>'btn btn-primary btn-second-tone', 'allowedStatusForEdit' => array(1,2))); ?></div>
	  <?php
        if ($sellingPriceAllowed) {
        ?>
        <div>  
            <div style="float:right; ">
                <div class="div-table icon-col  <?php echo $obj->hideOnDisabled(array(1)); ?>" style="float:right;">&nbsp;</div>   
                <div class="div-table" style="width:250px;float:right">
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['subtotal']); ?> 
                        </div>  
                        <div class="div-table-col-3" style="width:150px;"> 
                            <?php echo $obj->inputNumber('subtotal', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                        </div>
                    </div>
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['additionalCost']); ?> 
                        </div>  
                        <div class="div-table-col-3"> 
                            <?php echo $obj->inputNumber('totalCost', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                        </div>
                    </div> 
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['total']); ?> 
                        </div>  
                        <div class="div-table-col-3"> 
                            <?php echo $obj->inputNumber('total', array ('readonly' => true, 'etc' => 'style="text-align:right;"')) ;?>   
                        </div>
                    </div>
                     <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3"></div>  
                        <div class="div-table-col-3"> </div>
                    </div>
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['invoiceIssued']); ?> 
                        </div>  
                        <div class="div-table-col-3"> 
                            <?php echo $obj->inputNumber('totalInvoiced',array ('readonly' => true,'class' => 'form-control inputnumber '.$totalIssuedClass, 'etc' => 'style="text-align:right;"')); ?>  
                        </div>
                    </div>
                    <div class="div-table-row  form-group"> 
                        <div class="div-table-col-3" style="text-align:right;">
                            <?php echo ucwords($obj->lang['downpayment']); ?> 
                        </div>  
                        <div class="div-table-col-3"> 
                            <?php echo $obj->inputNumber('downpayment',array ('readonly' => true, 'etc' => 'style="text-align:right;"')); ?>  
                        </div>
                    </div>
                    
                </div>    
            </div>   
       </div>
       <?php } ?>      
     
        <div style="clear:both; height:1em;"></div>    
        
       <div class="section-panel green-scheme" >  
       <div class="title">
		   <div class="flex">
			   <div><?php echo ucwords($obj->lang['costList']); ?></div>
			   <div><i class="icon-expand icon-expand-down fas fa-sort-down"></i><i class="icon-expand icon-expand-up fas fa-sort-up" style="display:none"></i></div>
		   </div>
	   </div>
          <div class="section-panel-content div-table-tab-form" style="float:left;  width:100%; ">     
              <div class="div-table" style=" width:100%; ">          
                  <div class="div-table-row">
                        <div class="div-table-col-5" style="vertical-align:top; width: 50%"> 
                            <div style="margin-bottom: 0.5em;">
                                <div style="font-size:1.5em;  float:left"><?php echo ucwords($obj->lang['inhouseCost']); ?></div>
                                <div style="float:right"><div class="form-detail-button" relobj="inhouse-summary" relalt="<?php echo ucwords($obj->lang['hideDetail']); ?> "><?php echo ucwords($obj->lang['showDetail']); ?> </div></div>
                            </div> 

                            <div class="inhouse-summary" >
                             <div class="div-table green-scheme cost-detail" style="width:100%;">
                                <div class="div-table-row">  
                                        <div class="div-table-col-03 col-header"><?php echo ucwords($obj->lang['description']); ?></div>  
                                        <div class="div-table-col-03 col-header" style="text-align:right; width:70px; text-align:right"><?php echo ucwords($obj->lang['qty']); ?></div> 
                                        <div class="div-table-col-03 col-header" style="text-align:right; width:70px; text-align:right"><?php echo ucwords($obj->lang['cost']); ?></div> 
                                        <div class="div-table-col-03 col-header" style="text-align:right; width:100px; text-align:right"><?php echo ucwords($obj->lang['total']); ?></div>  
                                </div> 
                                
                                <?php
                                    // INHOUSE
                                    $total = 0;  

                                    $rsCost = $obj->getWorkOrderCostDetail($rs[0]['pkey'],false,false,'',' order by  costname asc, workordercode asc ');
                                 
 				                    $rsGroupCost = $obj->groupCostAmount($rsCost);
                                    for($i=0;$i<count($rsGroupCost);$i++){
                                        $qty = $rsGroupCost[$i]['qty']; 
                                        $isRealize = (!$showRealizationCost) ? true : $rsGroupCost[$i]['isrealization']; 
                                        
                                        $amount = ($isRealize) ? $rsGroupCost[$i]['amount'] : $rsGroupCost[$i]['requestamount']; 
                                        $subtotal = $qty * $amount;
                                        
                                        $costname = ((!$isRealize) ? '* ' : '') . $rsGroupCost[$i]['costname'];
                                        if ($subtotal == 0)
                                            continue;

                                        $_POST['hidWorkOrderCostKey[]'] = $rsGroupCost[$i]['costkey'];
                                        $_POST['hidWorkOrderCostName[]'] = $rsGroupCost[$i]['costname'];
                                        $_POST['hidWorkOrderCost[]'] = $amount;
                                        $_POST['hidWorkOrderQty[]'] = $qty;
                                        $_POST['hidWorkOrderSubtotal[]'] = $subtotal;
                                        $_POST['hidWorkOrderIsReimburse[]'] = $rsGroupCost[$i]['reimburse'];

                                         $total += $subtotal;  

                                        echo '
                                        <div class="div-table-row">  
                                        <div class="div-table-col-03">'.
                                            $costname.
                                            $obj->inputHidden('hidWorkOrderCostKey[]').
                                            $obj->inputHidden('hidWorkOrderCostName[]').
                                            $obj->inputHidden('hidWorkOrderQty[]').
                                            $obj->inputHidden('hidWorkOrderCost[]').
                                            $obj->inputHidden('hidWorkOrderSubtotal[]').
                                            $obj->inputHidden('hidWorkOrderIsReimburse[]'). 
                                        '</div>   
                                        <div class="div-table-col-03" style="text-align:right">'.$obj->formatNumber($qty).'</div>  
                                        <div class="div-table-col-03" style="text-align:right">'.$obj->formatNumber($amount).'</div>  
                                        <div class="div-table-col-03" style="text-align:right">'.$obj->formatNumber($subtotal).'</div>   
                                        </div>
                                        ';
 
                                    } 
                           

                                    echo '
                                        <div class="div-table-row">    
                                        <div class="div-table-col-03  no-background-color" style="border-top:1px solid #333"></div> 
                                        <div class="div-table-col-03  no-background-color" style="border-top:1px solid #333"></div> 
                                        <div class="div-table-col-03  no-background-color" style="font-weight:bold; text-align:right; border-top:1px solid #333">'.$obj->lang['total'].'</div> 
                                        <div class="div-table-col-03  no-background-color inhouse-cost" style="font-weight:bold; text-align:right; border-top:1px solid #333">'.$obj->formatNumber($total).'</div> 
                                        </div>
                                        '; 
                                ?>
                                </div>
                                </div>
                                
                                <div class="inhouse-summary" style="display:none">
                                <div class="div-table green-scheme cost-detail" style="width:100%;">
                                <div class="div-table-row">  
                                        <div class="div-table-col-03 col-header"><?php echo ucwords($obj->lang['description']); ?></div>  
                                        <div class="div-table-col-03 col-header" style=" width:90px;"><?php echo ucwords($obj->lang['WOCode']); ?></div>     
                                        <div class="div-table-col-03 col-header" style=" width:100px;"><?php echo ucwords($obj->lang['cashOutCode']); ?></div>     
                                        <div class="div-table-col-03 col-header" style="text-align:right; width:70px; text-align:right"><?php echo ucwords($obj->lang['cost']); ?></div> 
                                </div> 
                                
                                <?php
                                     // INHOUSE
                                     $total = 0;  
                                    
                                     // pake yg atas saja sudah sama, karena sudah gk di group
                                     // reset order
                                    $rsCost = $obj->getWorkOrderCostDetail($rs[0]['pkey'],false,false);
 
                                    for($i=0;$i<count($rsCost);$i++){

                                        $isRealize = (!$showRealizationCost) ? true : $rsCost[$i]['isrealization']; 
                                        $amount = ($isRealize) ? $rsCost[$i]['amount'] : $rsCost[$i]['requestamount'];
                                        $subtotal = $amount; 
                                        $costname = ((!$isRealize) ? '* ' : '') . $rsCost[$i]['costname'];

                                        if ($subtotal == 0)
                                            continue;
 
                                         $total += $subtotal;  

                                        echo '
                                        <div class="div-table-row">  
                                        <div class="div-table-col-03"> 
                                            <span class="service-name">'.$costname.'</span>'. 
                                        '<div class="recipient">'.$rsCost[$i]['recipientname'].'</div>
                                        </div>   
                                        <div class="div-table-col-03">'.$rsCost[$i]['workordercode'].'</div>  
                                        <div class="div-table-col-03">'.$rsCost[$i]['cashoutcode'].'</div>  
                                        <div class="div-table-col-03" style="text-align:right">'.$obj->formatNumber($subtotal).'</div>  
                                        </div>
                                        ';
 
                                    } 
                           

                                    echo '
                                        <div class="div-table-row">     
                                        <div class="div-table-col-03  no-background-color" style="border-top:1px solid #333"></div> 
                                        <div class="div-table-col-03  no-background-color" style="border-top:1px solid #333"></div> 
                                        <div class="div-table-col-03  no-background-color" style="font-weight:bold; text-align:right; border-top:1px solid #333">'.$obj->lang['total'].'</div> 
                                        <div class="div-table-col-03  no-background-color inhouse-cost" style="font-weight:bold; text-align:right; border-top:1px solid #333">'.$obj->formatNumber($total).'</div> 
                                        </div>
                                        '; 
                                ?>
                                </div>
                                </div>
                                
                                <?php
                                    if($showRealizationCost)    
                                        echo '<div style="clear:both"></div><div style="font-style:italic">*) Belum direalisasi</div>';    
                                ?>
                            
                                <div style="clear:both;"></div>
                            
                                <div style="margin-top:1em; margin-bottom: 0.5em;">
                                    <div style="font-size:1.5em;  float:left"><?php echo ucwords($obj->lang['outsourceCost']); ?></div>
                                    <div style="float:right"><div class="form-detail-button" relobj="outsource-summary" relalt="<?php echo ucwords($obj->lang['hideDetail']); ?> "><?php echo ucwords($obj->lang['showDetail']); ?> </div></div>
                                </div> 
                                <div class="outsource-summary">
                                <div class="div-table green-scheme cost-detail outsource-detail" style="width:100%;">
                                <div class="div-table-row">  
                                    <div class="div-table-col-03 col-header"><?php echo ucwords($obj->lang['description']); ?></div>        
                                    <div class="div-table-col-03 col-header" style="text-align:right; width:70px; text-align:right"><?php echo ucwords($obj->lang['qty']); ?></div> 
                                    <div class="div-table-col-03 col-header" style="text-align:right; width:70px; text-align:right"><?php echo ucwords($obj->lang['cost']); ?></div> 
                                    <div class="div-table-col-03 col-header" style="text-align:right; width:100px; text-align:right"><?php echo ucwords($obj->lang['total']); ?></div> 
                                </div> 
                                     
                                <?php 
    
                                         // OUTSOURCE
                                         $total = 0;  
                                    
                                        // BIAYA
                                        $rsCost =  $obj->getWorkOrderCostDetail($rs[0]['pkey'], true, false);  
                                        $rsCost = $obj->groupCostAmount($rsCost);
                                    
                                        for($i=0;$i<count($rsCost);$i++){
                                            $qty = $rsCost[$i]['qty'];
                                            $amount = $rsCost[$i]['amount'];
                                            $subtotal = $qty * $amount;
                                            $costname = $rsCost[$i]['costname'];
                                            
                                            if ($subtotal == 0)
                                                continue; 
                                              
                                            $_POST['hidWorkOrderCostKey[]'] = $rsCost[$i]['costkey'];
                                            $_POST['hidWorkOrderCostName[]'] = $rsCost[$i]['costname'];
                                            $_POST['hidWorkOrderCost[]'] = $amount;
                                            $_POST['hidWorkOrderQty[]'] = $qty;
                                            $_POST['hidWorkOrderSubtotal[]'] = $subtotal;
                                            $_POST['hidWorkOrderIsReimburse[]'] = $rsCost[$i]['reimburse'];
                                             
                                            $total += $subtotal;  
                                            
                                            echo '
                                            <div class="div-table-row">  
                                            <div class="div-table-col-03">'.
                                                $costname.
                                                $obj->inputHidden('hidWorkOrderCostKey[]').
                                                $obj->inputHidden('hidWorkOrderCostName[]').
                                                $obj->inputHidden('hidWorkOrderQty[]').
                                                $obj->inputHidden('hidWorkOrderCost[]').
                                                $obj->inputHidden('hidWorkOrderSubtotal[]').
                                                $obj->inputHidden('hidWorkOrderIsReimburse[]').'
                                            </div>                         
                                            <div class="div-table-col-03" style="text-align:right">'.$obj->formatNumber($qty).'</div>  
                                            <div class="div-table-col-03" style="text-align:right">'.$obj->formatNumber($amount).'</div>  
                                            <div class="div-table-col-03" style="text-align:right">'.$obj->formatNumber($subtotal).'</div>   
                                            </div>
                                            ';
 
                                        }    
     
                                        echo '
                                            <div class="div-table-row">  
                                            <div class="div-table-col-03 no-background-color" style="border-top:1px solid #333"></div> 
                                            <div class="div-table-col-03 no-background-color" style="border-top:1px solid #333"></div> 
                                            <div class="div-table-col-03 no-background-color" style="font-weight:bold; text-align:right; border-top:1px solid #333">'.ucwords($obj->lang['total']).'</div> 
                                            <div class="div-table-col-03 no-background-color  outsource-cost" style="font-weight:bold; text-align:right; border-top:1px solid #333">'.$obj->formatNumber($total).'</div> 
                                            </div>
                                        ';
                                ?>
                                 
                                 </div> 
                                </div>
                            
                            
                               <div class="outsource-summary" style="display:none">
                               <div class="div-table green-scheme cost-detail outsource-detail" style="width:100%;">
                                <div class="div-table-row">  
                                    <div class="div-table-col-03 col-header"><?php echo ucwords($obj->lang['description']); ?></div>       
                                    <div class="div-table-col-03 col-header" style=" width:90px;"><?php echo ucwords($obj->lang['WOCode']); ?></div>     
                                    <div class="div-table-col-03 col-header" style=" width:100px;"><?php echo ucwords($obj->lang['apCode']); ?></div>     
                                    <div class="div-table-col-03 col-header" style="text-align:right; width:70px; text-align:right"><?php echo ucwords($obj->lang['cost']); ?></div> 
                                </div> 
                                     
                                <?php 
    
                                         // OUTSOURCE
                                         $total = 0;  
                                    
                                        // BIAYA
                                        $rsCost =  $obj->getWorkOrderCostDetail($rs[0]['pkey'], true, false);  

                                        for($i=0;$i<count($rsCost);$i++){
                                            $subtotal = $rsCost[$i]['amount'];
                                            $costname = $rsCost[$i]['costname'];
                                            
                                            if ($subtotal == 0)
                                                continue; 
                                              
                                            $_POST['hidWorkOrderCostKey[]'] = $rsCost[$i]['costkey'];
                                            $_POST['hidWorkOrderCostName[]'] = $rsCost[$i]['costname'];
                                            $_POST['hidWorkOrderCost[]'] = $rsCost[$i]['amount'];
                                            $_POST['hidWorkOrderQty[]'] = 1;
                                            $_POST['hidWorkOrderSubtotal[]'] = $subtotal;
                                            $_POST['hidWorkOrderIsReimburse[]'] = $rsCost[$i]['reimburse'];
                                             
                                            $total += $subtotal;  
                                            
                                            echo '
                                            <div class="div-table-row">  
                                            <div class="div-table-col-03"> 
                                            <span class="service-name">'.$costname.'</span>'. 
                                            '<div class="recipient">'.$rsCost[$i]['recipientname'].'</div>
                                            </div>                           
                                            <div class="div-table-col-03">'.$rsCost[$i]['workordercode'].'</div>  
                                            <div class="div-table-col-03">'.$rsCost[$i]['cashoutcode'].'</div>  
                                            <div class="div-table-col-03" style="text-align:right">'.$obj->formatNumber($rsCost[$i]['amount']).'</div>
                                            </div>
                                            ';
 
                                        }    
     
                                        echo '
                                            <div class="div-table-row">  
                                            <div class="div-table-col-03 no-background-color" style="border-top:1px solid #333"></div> 
                                            <div class="div-table-col-03 no-background-color" style="border-top:1px solid #333"></div> 
                                            <div class="div-table-col-03 no-background-color" style="font-weight:bold; text-align:right; border-top:1px solid #333">'.ucwords($obj->lang['total']).'</div> 
                                            <div class="div-table-col-03 no-background-color  outsource-cost" style="font-weight:bold; text-align:right; border-top:1px solid #333">'.$obj->formatNumber($total).'</div> 
                                            </div>
                                        ';
                                ?>
                                 
                                 </div> 
                                </div>
                            
                                <div style="clear:both"></div>
 
                                <div style="margin-top:1em; margin-bottom: 0.5em;">
                                    <div style="font-size:1.5em;  float:left"><?php echo ucwords($obj->lang['additionalCost']); ?></div>
                                    <div style="float:right"><div class="form-detail-button" relobj="additional-cost-summary" relalt="<?php echo ucwords($obj->lang['hideDetail']); ?> "><?php echo ucwords($obj->lang['showDetail']); ?> </div></div>
                                </div> 
                            
                                <div class="div-table transaction-detail green-scheme" style="width:100%; border-bottom:1px solid #333">
                                <div class="div-table-row">  

                                    <div class="div-table-col" style="padding:0">
                                        <div class="div-table" style="width:100%">
                                            <div class="div-table-row">  
                                                <div class="div-table-col col-header" style="width:60px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                                                <div class="div-table-col col-header"><?php echo ucwords($obj->lang['costName']); ?></div>    
                                                <div class="div-table-col col-header" style="width:80px; text-align:right"><?php echo ucwords($obj->lang['cost']); ?></div>
                                                <?php if ($showRealizationCost) { ?> 
                                                    <div class="div-table-col col-header" style="width:80px; text-align:right;"><?php echo ucwords($obj->lang['realization']); ?></div> 
                                                <?php } ?>    
                                                <div class="div-table-col col-header" style="width:90px; text-align:right"><?php echo ucwords($obj->lang['subtotal']); ?></div>    
                                            </div>
                                        </div>
                                    </div>
                                    <div class="div-table-col col-header icon-col" ></div>    
                                </div> 

                                <?php  
                                      $totalRows = count($rsSalesHeaderCost); 

                                      for ($i=0;$i<=$totalRows; $i++){  

                                            $class =  'transaction-detail-row';
                                            $style = '';
                                            $overwrite = true;
                                            $etc = '';  
                                            $statusStyle = '';
                                            $detail = '';
                                            $readonlyCashedOut = false;
                                            $cashedOutIcon = '';
                                            $deleteHeaderCostIcon = $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1"  style="position:relative; top: 4px"  attrhandler="truckingServiceOrder.calculateTotalHeaderCost()"'));

                                            if ($i == $totalRows ){
                                                $class = 'cost-row-template';
                                                $style = 'style="display:none"';
                                                $overwrite = false;
                                                $etc = 'disabled="disabled"';  
                                            } else {    
                                                 
                                                $_POST['hidAdditionalKey[]'] = $rsSalesHeaderCost[$i]['pkey'];
                                                $_POST['hidItemKeyHeaderCost[]'] = $rsSalesHeaderCost[$i]['costkey'];
                                                $_POST['itemNameHeaderCost[]'] = $rsSalesHeaderCost[$i]['itemname']; 
						                        $_POST['hidHeaderCostIsReimburse[]'] = $rsSalesHeaderCost[$i]['reimburse'];  
                                                $_POST['requestPriceHeaderCost[]'] = $obj->formatNumber($rsSalesHeaderCost[$i]['requestamount']);
                                                $_POST['priceHeaderCost[]'] = $obj->formatNumber($rsSalesHeaderCost[$i]['amount']);
                                                $_POST['qtyHeaderCost[]'] = $obj->formatNumber($rsSalesHeaderCost[$i]['qty']);
                                                $_POST['subtotalHeaderCost[]'] = $obj->formatNumber($rsSalesHeaderCost[$i]['subtotal']); 
                                                 
                                                $_POST['detailDesc[]'] = $rsSalesHeaderCost[$i]['description'];
                                                $_POST['hidDetailEmployeeKey[]'] = $rsSalesHeaderCost[$i]['employeekey'];
                                                $_POST['detailEmployeeName[]'] = $rsSalesHeaderCost[$i]['recipientname'];
                                                 
                                                                                         
                                                $_POST['hidRefCashOutKey[]'] ='';
                                                $_POST['refCashOutCode[]'] = ''; 
                                                 
                                                if($rsSalesHeaderCost[$i]['isrealization'] == 1 || !empty($rsSalesHeaderCost[$i]['refcashoutkey'])  || !empty($rsSalesHeaderCost[$i]['refrequestkey']) ){
                                                    $readonlyCashedOut = true;
                                                    $deleteHeaderCostIcon = '';
                                                    
                                                    
                                                    if(!empty($rsSalesHeaderCost[$i]['refrequestkey'])){ 
//                                                        $_POST['refCashOutCode[]'] = $rsSalesHeaderCost[$i]['refcashoutcode']; 
                                                        $cashedOutIcon = '<i class="cashed-out-icon  far fa-file-alt" style="margin-top:10px" title="'.$_POST['refCashOutCode[]'].'"></i>'; 
                                                    }
                                                    
                                                    
                                                    if(!empty($rsSalesHeaderCost[$i]['refcashoutkey'])){
                                                        $_POST['hidRefCashOutKey[]'] = $rsSalesHeaderCost[$i]['refcashoutkey'];
                                                        $_POST['refCashOutCode[]'] = $rsSalesHeaderCost[$i]['refcashoutcode']; 
                                                        $cashedOutIcon = '<i class="cashed-out-icon fas fa-hand-holding-usd" style="margin-top:10px" title="'.$_POST['refCashOutCode[]'].'"></i>'; 
                                                    }
                                                    
                                                }
                                            }
                                          
                                           /* $readonlyCashedOut = false;
                                            $cashedOutIcon = '';
                                            $deleteHeaderCostIcon = $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'attrhandler="truckingServiceOrder.calculateTotalHeaderCost()" style="padding:6px 0"'));
*/
                                ?>

                                <div class="div-table-row  <?php echo $class; ?>" <?php echo $style; ?> >
                                        <div class="div-table-col"  style="padding:0">
                                        <div class="additional-cost-summary" style="height:0.5em; display:none"></div>
                                        <div class="div-table" style="width:100%">
                                            <div class="div-table-row">
                                                 <div class="div-table-col detail-col-detail" style="width:60px; text-align:right;">
                                                    <?php echo $obj->inputHidden('hidAdditionalKey[]',array('overwritePost' => $overwrite , 'etc' => $etc)); ?>
                                                    <?php echo $obj->inputNumber('qtyHeaderCost[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyCashedOut, 'value'=> 1,'class' => 'form-control inputnumber s-input', 'etc' =>  'style="text-align:right;" onChange="truckingServiceOrder.calculateHeaderCost(this)" ' .$etc)); ?>
                                                    <?php echo $obj->inputHidden('hidHeaderCostIsReimburse[]', array('overwritePost' => $overwrite)); ?>
                                                </div>
                                                <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('itemNameHeaderCost[]',array('overwritePost' => $overwrite, 'readonly' => $readonlyCashedOut, 'class' => 'form-control s-input', 'etc' =>  '  onChange="truckingServiceOrder.calculateHeaderCost(this)" ' . $etc )); ?><?php echo $obj->inputHidden('hidItemKeyHeaderCost[]',array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                                                <div class="div-table-col detail-col-detail" style="width:80px; text-align:right"><?php echo $obj->inputNumber('requestPriceHeaderCost[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyCashedOut, 'class' => 'form-control inputnumber s-input','etc' => 'style="text-align:right;"  onChange="truckingServiceOrder.calculateHeaderCost(this)" ' .$etc )); ?></div>
                                                <?php if ($showRealizationCost) { ?>
                                                    <div class="div-table-col detail-col-detail" style="width:80px; text-align:right"><?php echo $obj->inputNumber('priceHeaderCost[]', array('overwritePost' => $overwrite,'readonly' => true,'class' => 'form-control inputnumber s-input', 'etc' => 'style="text-align:right;" ' .$etc)); ?></div>
                                                <?php } ?>                                    
                                                <div class="div-table-col detail-col-detail" style="width:90px; text-align:right"><?php echo $obj->inputNumber('subtotalHeaderCost[]', array('overwritePost' => $overwrite,'readonly' => true,'class' => 'form-control inputnumber s-input', 'etc' => 'style="text-align:right;"  onChange="truckingServiceOrder.calculateHeaderCost(this)" ' .$etc)); ?></div> 
                                            </div>

                                        </div>
                                        <div class="div-table additional-cost-summary" style="width:100%; display:none">
                                            <div class="div-table-row">
                                                  <div class="div-table-col detail-col-detail"><?php echo $obj->inputText('detailEmployeeName[]',array('overwritePost' => $overwrite,'readonly' => $readonlyCashedOut,'class' => 'form-control s-input', 'etc' => 'placeholder="'.$obj->lang['employee'].'"'. $etc)); ?><?php echo $obj->inputHidden('hidDetailEmployeeKey[]', array('overwritePost' => $overwrite, 'etc' => $etc)); ?></div> 
                                            </div>
                                            <div class="div-table-row">
                                                   <div class="div-table-col detail-col-detail">
                                                    <?php echo $obj->inputText('detailDesc[]',array('overwritePost' => $overwrite,'readonly' => $readonlyCashedOut,'class' => 'form-control s-input', 'etc' => 'placeholder="'.$obj->lang['note'].'"')); ?>
                                                  </div>

                                            </div>
                                        </div> 
                                        <div class="additional-cost-summary" style="height:0.5em; display:none"></div>
                                    </div>
                                    <div class="div-table-col detail-col-detail icon-col" style="vertical-align:top" ><?php echo $cashedOutIcon.$deleteHeaderCostIcon; ?></div>
                                     
                                </div>
                                <?php } ?>

                                </div> 

                                <div style="clear:both; height:0.5em;"></div>  
                                <div class="div-table transaction-detail" style="width:100%;">
                                    <div class="div-table-row">
                                        <div class="div-table-col detail-col-detail"><?php echo $obj->inputButton('btnAddHeaderCostRows',ucwords($obj->lang['addRows']), array('class' =>'btn btn-primary btn-second-tone')); ?></div> 
                                        <div class="div-table-col detail-col-detail" colspan="2"></div>
                                        <div class="div-table-col detail-col-detail"  style="width:90px;"><?php echo $obj->inputNumber('totalHeaderCost',array('readonly' => true, 'class' => 'form-control inputnumber s-input','etc' => 'style="text-align:right;')); ?></div>
                                       <div class="div-table-col detail-col-detail icon-col"></div>
                                    </div>     
                                </div>
                            
                            
                            </div>  
                        <div class="div-table-col-5" style="vertical-align:top; padding-left: 2em"> 
                        <?php    if ($sellingPriceAllowed) {   ?>
                            <div style="font-size:1.5em; margin-bottom: 0.5em"><?php echo ucwords($obj->lang['sellingPrice']); ?></div>
                            <div class="div-table transaction-detail green-scheme" style="width:100%; border-bottom:1px solid #333">
                                <div class="div-table-row"> 
                                    <div class="div-table-col" style="padding:0">
                                        <div class="div-table" style="width:100%">
                                            <div class="div-table-row"> 
                                                <div class="div-table-col detail-col-header" style="width:50px; text-align:right;"><?php echo ucwords($obj->lang['qty']); ?></div>
                                                <?php if ($showInvoicedQty) {?><div class="div-table-col detail-col-header"  style="width:40px;"></div><?php } ?>
                                                <div class="div-table-col detail-col-header"><?php echo ucwords($obj->lang['services']); ?></div>    
                                                <div class="div-table-col detail-col-header" style="width:120px;"><?php echo ucwords('Store'); ?></div>    
                                                <div class="div-table-col detail-col-header" style="width:90px; text-align:right"><?php echo ucwords($obj->lang['price']); ?></div>
                                                <div class="div-table-col detail-col-header" style="width:90px; text-align:right"><?php echo ucwords($obj->lang['subtotal']); ?></div> 
                                            </div>
                                        </div>
                                    </div>
                                </div> 

                                <?php  
                                      $totalRows = count($rsSalesDetailCost); 

                                      for ($i=0;$i<=$totalRows; $i++){  

                                            $class =  'transaction-detail-row';
                                            $style = '';
                                            $overwrite = true;
                                            $disabledSellingCost = ''; 

                                            $statusStyle = '';
                                            $detail = '';
                                            $readonlyOnInvoiced = false;

                                            if ($i == $totalRows ){
                                                $class = 'detail-cost-row-template';
                                                $style = 'style="display:none"';
                                                $overwrite = false;
                                                $disabledSellingCost = 'disabled="disabled"';   
                                                $qtyInvoiced = 0;
                                                $qtyInvoicedClass = 'text-muted';
                                            } else {       

                                                // kalo sudah ad qty invoiced, readonly = true;
                                                if ($rsSalesDetailCost[$i]['qtyinvoiced'] > 0) $readonlyOnInvoiced = true;

                                                $qty = $rsSalesDetailCost[$i]['qty'];
                                                $qtyInvoiced = $rsSalesDetailCost[$i]['qtyinvoiced'];
                                                    
                                                $_POST['hidDetailCostKey[]'] = $rsSalesDetailCost[$i]['pkey'];
                                                $_POST['hidItemKeyCost[]'] = $rsSalesDetailCost[$i]['costkey'];
                                                $_POST['itemNameCost[]'] = $rsSalesDetailCost[$i]['itemname']; 
                                                $_POST['store[]'] = $rsSalesDetailCost[$i]['store'];
                                                $_POST['sellingDesc[]'] = $rsSalesDetailCost[$i]['notes'];
                                                $_POST['priceCost[]'] = $obj->formatNumber($rsSalesDetailCost[$i]['price']);
                                                $_POST['qtyCost[]'] = $obj->formatNumber($qty);
                                                $_POST['subtotalCost[]'] = $obj->formatNumber($rsSalesDetailCost[$i]['subtotal']);  
                                                $qtyInvoicedClass = ($qtyInvoiced < $qty) ? 'text-red-cardinal' : 'text-muted';
                                            }
                                ?>

                                <div class="div-table-row  <?php echo $class; ?>" <?php echo $style; ?> >
                                    
                                    <div class="div-table-col"  style="padding:0">
                                        <div class="additional-cost-summary" style="height:0.5em; display:none"></div>
                                        <div class="div-table" style="width:100%">
                                            <div class="div-table-row">
                                                
                                                <div class="div-table-col detail-col-detail" style="width:50px;"><?php echo $obj->inputNumber('qtyCost[]', array('overwritePost' => $overwrite, 'value'=> 1,'etc' =>  'style="text-align:right;" onChange="truckingServiceOrder.calculateDetailCost(this)" ','class' => 'form-control s-input',  'disabled' => $disabledSellingCost )); ?></div>
                                                <?php if ($showInvoicedQty) {?><div class="div-table-col detail-col-detail text-muted"  style="width:40px;">/ <span class="<?php echo $qtyInvoicedClass; ?>" style="text-align:right; width: 35px"><?php echo $obj->formatNumber($qtyInvoiced); ?></span></div><?php } ?>
                                                <div class="div-table-col detail-col-detail">
                                                    <?php echo $obj->inputText('itemNameCost[]',array('overwritePost' => $overwrite,'readonly' => $readonlyOnInvoiced,  'etc' => '  onChange="truckingServiceOrder.calculateDetailCost(this)" ','class' => 'form-control s-input',  'disabled' => $disabledSellingCost )); ?>
                                                    <?php echo $obj->inputHidden('hidItemKeyCost[]',array('overwritePost' => $overwrite, 'disabled' => $disabledSellingCost)); ?><?php echo $obj->inputHidden('hidDetailCostKey[]',array('overwritePost' => $overwrite , 'disabled' => $disabledSellingCost)); ?>
                                                </div> 
                                                <div class="div-table-col detail-col-detail" style="width:120px;">
                                                    <?php echo $obj->inputText('store[]',array('overwritePost' => $overwrite,'readonly' => $readonlyOnInvoiced,  'etc' => '  onChange="truckingServiceOrder.calculateDetailCost(this)" ','class' => 'form-control s-input',  'disabled' => $disabledSellingCost )); ?>
                                                </div> 
                                                <div class="div-table-col detail-col-detail" style="width:90px;"><?php echo $obj->inputNumber('priceCost[]', array('overwritePost' => $overwrite, 'readonly' => $readonlyOnInvoiced,   'etc' => 'style="text-align:right;"  onChange="truckingServiceOrder.calculateDetailCost(this)" ' ,'class' => 'form-control inputnumber s-input',  'disabled' => $disabledSellingCost)); ?></div>
                                                <div class="div-table-col detail-col-detail" style="width:90px;"><?php echo $obj->inputNumber('subtotalCost[]', array('overwritePost' => $overwrite,'readonly' => true, 'etc' => 'style="text-align:right;"  onChange="truckingServiceOrder.calculateDetailCost(this)" ' , 'class' => 'form-control inputnumber s-input', 'disabled' => $disabledSellingCost)); ?></div>
                                                
                                            </div>

                                        </div>
                                        <div class="div-table" style="width:100%;">
                                            <div class="div-table-row">
                                                <div class="div-table-col detail-col-detail">
                                                    <?php echo $obj->inputText('sellingDesc[]',array('overwritePost' => $overwrite,'readonly' => $readonlyCashedOut,'class' => 'form-control s-input', 'etc' => 'placeholder="'.$obj->lang['note'].'"')); ?>
                                                </div>
                                            </div>
                                        </div> 
                                        
                                    </div>
                                    
                                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col" style="width:25px;vertical-align:top">
                                        <?php if(!$readonlyOnInvoiced)  echo $obj->inputLinkButton('btnDeleteRows' , '<i class="fas fa-times"></i>', array('class' => 'btn btn-link remove-button', 'etc' => 'tabIndex="-1" attrhandler="truckingServiceOrder.calculateTotalCost()" style="padding:6px 0"' )); ?>
                                    </div>

                                </div> 

                                <?php } ?> 
                            </div>  

                            <div style="clear:both; height:0.5em;"></div>  
                            <div class="div-table transaction-detail" style="width:100%;">
                                <div class="div-table-row">
                                    <div class="div-table-col detail-col-detail" >
                                        <?php echo $obj->inputButton('btnAddCostRows',$obj->lang['addRows'], array('class' =>'btn btn-primary btn-second-tone', 'etc' => 'style="margin-bottom:0.5em"')); ?>
                                        <?php echo $obj->inputButton('btnAddReimburseCost',$obj->lang['addReimburse'], array('class' =>'btn btn-primary btn-princeton-orange btn-second-tone', 'etc' => 'style="margin-bottom:0.5em"')); ?>
                                    </div> 
                                    <div class="div-table-col detail-col-detail"  style="width:90px; vertical-align:top"><?php echo $obj->inputNumber('totalCost',array('readonly' => true,'class' => 'form-control inputnumber s-input',  'etc' => 'style="text-align:right;')); ?></div>
                                    <div class="div-table-col detail-col-detail <?php echo $obj->hideOnDisabled(); ?> icon-col"></div>
                                </div>
                            </div>  
                            <?php } ?>
                             <div style="clear:both; height: 3em"></div>
                             <div class="div-table" style="width: 100%">
                                 <div class="div-table-row">
                                      <div class="div-table-col" style="width: 250px">
                                        <div class="div-table summary" style="width:100%">
                                          <div class="div-table-row">
                                              <div class="div-table-col-5" style="font-weight:bold; width: 90px"><?php echo strtoupper($obj->lang['totalCost']); ?></div>
                                              <div class="div-table-col-5" style="text-align:right"><?php echo $obj->inputNumber('totalBilledCost',array('readonly' => true, 'etc' => 'style="text-align:right;')); ?></div>
                                             <!-- <div class="div-table-col-5" style="width: 50px;"></div>-->
                                          </div>
                                        <?php  if ($sellingPriceAllowed) {    ?>
                                           <div class="div-table-row">
                                              <div class="div-table-col-5" style="font-weight:bold; padding-top:0.3em"><?php echo strtoupper($obj->lang['sellingPrice']); ?></div>
                                              <div class="div-table-col-5" style="text-align:right; padding-top:0.3em"><?php echo $obj->inputNumber('total', array( 'readonly'=>true, 'etc' => 'style="text-align:right;"')); ?></div>
                                             <!-- <div class="div-table-col-5"></div>-->
                                          </div>
                                           <div class="div-table-row">
                                              <div class="div-table-col-5" style="font-weight:bold; padding-top:0.3em"><?php echo strtoupper($obj->lang['balance']); ?></div>
                                              <div class="div-table-col-5" style="text-align:right; padding-top:0.3em; border-top:1px solid #666"><?php echo $obj->inputNumber('balanceCost',array('readonly' => true, 'etc' => 'style="text-align:right;')); ?></div>
                                             <!-- <div class="div-table-col-5" style="width:80px"><span  class="percentage-cost" style="float:left; margin-right:0.2em"></span> %</div>-->
                                          </div>
                                        <?php } ?>
                                      </div>
                                     </div>  
                                     <div class="div-table-col" style="padding-left:0.3em">
                                       <!-- <div class="summary" style="width: 100%"><strong>Gross Profit Margin : </strong><span class="gross-profit-margin">0</span> %</div>-->
                                     </div>
                                 </div>
                                
                             </div>
                        </div>
                    </div>
              </div> 
           </div> 
          <div style="clear:both"></div>
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
