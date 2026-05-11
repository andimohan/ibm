<?php
include_once '../../_config.php'; 
include_once '../../_include-v2.php'; 

if(!isset($_GET['module']) || empty($_GET['module'])) die;
    
$moduleIndex = $_GET['module'];

$importModule = array();
$importModule['item'] = array('title' => $class->lang['item'] ,'file' => 'importItem', 'templateFile'=> '../report/reportItem', 'securityObj' => 'Item');
$importModule['itemIn'] = array('title' => $class->lang['itemIn'] ,'file' => 'importItemIn', 'templateFile'=> '../report/reportItemIn', 'securityObj' => 'ItemIn');
$importModule['itemOut'] = array('title' => $class->lang['itemOut'] ,'file' => 'importItemOut', 'templateFile'=> '../report/reportItemOut', 'securityObj' => 'ItemOut');
$importModule['services'] = array('title' => $class->lang['service'] ,'file' => 'importServices', 'templateFile'=> '../report/reportService', 'securityObj' => 'Service');
$importModule['customer'] = array('title' => $class->lang['customer'] ,'file' => 'importCustomer', 'templateFile'=> '../report/reportCustomer', 'securityObj' => 'Customer');
$importModule['supplier'] = array('title' => $class->lang['supplier'] ,'file' => 'importSupplier', 'templateFile'=> '../report/reportSupplier', 'securityObj' => 'Supplier');
$importModule['consignee'] = array('title' => $class->lang['consignee'] ,'file' => 'importConsignee', 'templateFile'=> '../report/reportConsignee', 'securityObj' => 'Consignee');
$importModule['ap'] = array('title' => $class->lang['accountsPayable'] ,'file' => 'importAP', 'templateFile'=> '../report/reportAP', 'securityObj' => 'AP');
$importModule['ar'] = array('title' => $class->lang['accountsReceivable'] ,'file' => 'importAR', 'templateFile'=> '../report/reportAR', 'securityObj' => 'AR');
$importModule['location'] = array('title' => $class->lang['location'] ,'file' => 'importLocation', 'templateFile'=> '../report/reportLocation', 'securityObj' => 'location');
$importModule['employee'] = array('title' => $class->lang['employee'] ,'file' => 'importEmployee', 'templateFile'=> '../report/reportEmployee', 'securityObj' => 'employee');
$importModule['serialnumber'] = array('title' => $class->lang['serialNumber'] ,'file' => 'importSerialNumber', 'templateFile'=> '../../tools/template-excel/templateSN.xlsx', 'securityObj' => 'item');
$importModule['car'] = array('title' => $class->lang['car'] ,'file' => 'importCar', 'templateFile'=> '../report/reportCar', 'securityObj' => 'car');
$importModule['FFPurchaseOrderExport'] = array('title' => $class->lang['purchaseOrderExport'] ,'file' => 'importFFPurchaseOrderExport', 'templateFile'=> '#', 'securityObj' => 'EMKLPurchaseOrder');
$importModule['FFPurchaseOrderImport'] = array('title' => $class->lang['purchaseOrderImport'] ,'file' => 'importFFPurchaseOrderImport', 'templateFile'=> '#', 'securityObj' => 'EMKLPurchaseOrder');
$importModule['FFJobOrderExport'] = array('title' => $class->lang['exportOrderSheet'] ,'file' => 'importFFJobOrderExport', 'templateFile'=> '#', 'securityObj' => 'EMKLJobOrder');
$importModule['salesOrderSubscription'] = array('title' => $class->lang['salesOrder'] ,'file' => 'importSalesOrderSubscription', 'templateFile'=> '../report/reportSalesOrderSubscription', 'securityObj' => 'SalesOrder');
$importModule['cashBankIn'] = array('title' => $class->lang['cashBankIn'] ,'file' => 'importCashBankIn', 'templateFile'=> '../report/reportCashIn', 'securityObj' => 'CashBankIn');
$importModule['cashOut'] = array('title' => $class->lang['cashOut'] ,'file' => 'importCashOut', 'templateFile'=> '../report/reportCashOut', 'securityObj' => 'CashOut');
$importModule['brand'] = array('title' => $class->lang['brand'] ,'file' => 'importBrand', 'templateFile'=> '../report/reportBrand', 'securityObj' => 'Brand');
$importModule['itemCategory'] = array('title' => $class->lang['itemCategory'] ,'file' => 'importItemCategory', 'templateFile'=> '../report/reportItemCategory', 'securityObj' => 'ItemCategory');
$importModule['asset'] = array('title' => $class->lang['asset'] ,'file' => 'importAsset', 'templateFile'=> '../report/reportAsset', 'securityObj' => 'Asset');

// khusus COA sementara dimatikan link templatenya
$importModule['coa'] = array('title' => $class->lang['chartOfAccount'] ,'file' => 'importCOA', 'templateFile'=> '', 'securityObj' => 'ChartOfAccount');

$importModule['buildingUnit'] = array('title' => $class->lang['buildingUnit'] ,'file' => 'importBuildingUnit', 'templateFile'=> '../report/reportBuildingUnit', 'securityObj' => 'BuildingUnit');
$importModule['salesOrder'] = array('title' => $class->lang['salesOrder'] ,'file' => 'importSalesOrder', 'templateFile'=> '../report/reportSalesOrder', 'securityObj' => 'SalesOrder');
$importModule['arPayment'] = array('title' => $class->lang['arPayment'] ,'file' => 'importARPayment', 'templateFile'=> '../report/reportARPayment', 'securityObj' => 'ARPayment');
$importModule['purchaseOrder'] = array('title' => $class->lang['purchaseOrder'], 'file' => 'importPurchaseOrder', 'templateFile' => '../report/reportPurchaseOrder', 'securityObj' => 'PurchaseOrder');
$importModule['itemAdjustment'] = array('title' => $class->lang['itemAdjustment'], 'file' => 'importItemAdjustment', 'templateFile' => '../report/reportItemAdjustment', 'securityObj' => 'ItemAdjustment');
$importModule['continent'] = array('title' => $class->lang['continent'], 'file' => 'importContinent', 'templateFile' => '../report/reportContinent', 'securityObj' => 'Continent');
$importModule['country'] = array('title' => $class->lang['country'], 'file' => 'importCountry', 'templateFile' => '../report/reportCountry', 'securityObj' => 'Country');
$importModule['city'] = array('title' => $class->lang['city'], 'file' => 'importCity', 'templateFile' => '../report/reportCity', 'securityObj' => 'City');
$importModule['location'] = array('title' => $class->lang['location'], 'file' => 'importLocation', 'templateFile' => '../report/reportLocation', 'securityObj' => 'Location');
$importModule['cityCategory'] = array('title' => $class->lang['cityCategory'], 'file' => 'importCityCategory', 'templateFile' => '../report/reportCity', 'securityObj' => 'CityCategory');
$importModule['port'] = array('title' => $class->lang['port'], 'file' => 'importPort', 'templateFile' => '../report/reportPort', 'securityObj' => 'Port');
$importModule['vessel'] = array('title' => $class->lang['vessel'], 'file' => 'importVessel', 'templateFile' => '../report/reportVessel', 'securityObj' => 'Vessel');
$importModule['commodity'] = array('title' => $class->lang['commodity'], 'file' => 'importCommodity', 'templateFile' => '../report/reportCommodity', 'securityObj' => 'Commodity');
$importModule['itemUnits'] = array('title' => $class->lang['itemUnit'], 'file' => 'importItemUnits', 'templateFile' => '../report/reportItemUnit', 'securityObj' => 'ItemUnit');
$importModule['generalJournal'] = array('title' => $class->lang['generalJournal'], 'file' => 'importGeneralJournal', 'templateFile' => '../report/reportGeneralJournal', 'securityObj' => 'GeneralJournal');
$importModule['carServiceMaintenance'] = array('title' => $class->lang['carMaintenance'], 'file' => 'importCarServiceMaintenance', 'templateFile' => '../report/reportCarServiceMaintenance', 'securityObj' => 'CarServiceMaintenance');
$importModule['costRate'] = array('title' => $class->lang['costRate'], 'file' => 'importCostRate', 'templateFile' => '../report/reportCostRate', 'securityObj' => 'costRate');

//PRAJA
//$importModule['jobOrder'] = array('title' => $class->lang['jobOrder']. ' (Versi Lama)', 'file' => 'importTruckingServiceOrder', 'templateFile' => '../report/reportTruckingServiceOrder', 'securityObj' => 'TruckingServiceOrder');
$importModule['jobOrderAndCost'] = array('title' => $class->lang['jobOrder'], 'file' => 'importTruckingServiceOrderAndCost', 'templateFile' => '../report/reportTruckingServiceOrder', 'securityObj' => 'TruckingServiceOrder');
$importModule['workOrderSellingPrice'] = array('title' => $class->lang['importSellingProce'], 'file' => 'importTruckingServiceWorkOrder', 'templateFile' => '../report/reportTruckingServiceOrder', 'securityObj' => 'TruckingServiceOrder');
$importModule['costRatePraja'] = array('title' => $class->lang['costRate'], 'file' => 'importCostRatePraja', 'templateFile' => '../report/reportCostRate', 'securityObj' => 'costRate');

// MTI
$importModule['jobOrderAndSpkMTI'] = array('title' => $class->lang['jobOrder'], 'file' => 'importJobOrderAndSPKMTI', 'templateFile' => '../report/reportTruckingServiceOrder', 'securityObj' => 'TruckingServiceOrder');
$importModule['purchasePricing'] = array('title' => $class->lang['purchasePricing'], 'file' => 'importPurchasePricing', 'templateFile' => '../report/reportPurchasePricing', 'securityObj' => 'PurchasePricing');
$importModule['itemReceiving'] = array('title' => $class->lang['itemReceiving'], 'file' => 'importItemReceiving', 'templateFile' => '../report/reportItemReceiving', 'securityObj' => 'ItemReceiving');

$module = $importModule[$moduleIndex]; 
if(!$security->isAdminLogin($module['securityObj'],11,true)); 
 
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fontawesome6.min.css">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />    
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath.ADMIN_CSS_VERSION; ?>">  
     
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>  

<script> 
function updateChkBoxOnClick(obj){   
    var chkValue = $(obj).prop("checked") ? 1 : 0;
    $(obj).val(chkValue); 
    $(obj).next().val(chkValue); 
} 

function updateChkBoxOnChange(obj){   
    
    var checked = "",chkValue = 0;
    
    if($(obj).val() == 1){
        checked = "checked";
        chkValue = 1;
    } 
    
    $(obj).prev().prop("checked",checked).change(); // dont use click !
    $(obj).prev().val(chkValue);
}

function updateChkPick(obj,onChangeFunc){ 
    var obj = $(obj);  
    var container = obj.closest(".mnv-checkbox-group");
    
    if (obj.attr("relignore"))
        return; 

    var chkPick = container.find("[name='chkPick[]']:enabled"); 

    chkPick.prev().attr("relignore", true); 
    chkPick.val(obj.next().val()).change();
    chkPick.prev().removeAttr("relignore"); 
 
    // cukup sekali, gk perlu setiap klik detail dihitung ulang 
    if(onChangeFunc) onChangeFunc();
}
  
</script>    
    
<title>Import - <?php echo ucwords($module['title']); ?></title>  
</head> 
<body>    
    
<div style="padding: 1em">  
    <h1>Import - <?php echo ucwords($module['title']); ?></h1>
    <?php if (!empty($module['templateFile'])){  ?>
    <div style="clear:both; height: 2em"></div>
    <div>Silahkan mengunduh file template <a href="<?php echo $module['templateFile']; ?>" target="_blank">disini</a>.</div>
    <?php } ?>
    <div style="clear:both; height: 2em"></div>
    <form action="<?php echo $module['file'] ; ?>" method="post" enctype="multipart/form-data" target="_blank" id="form-import"> 
        <div class="div-table"> 
            <div class="div-table-row">
                <div class="div-table-col-5" style="font-weight:bold">File</div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><input type="file" name="fileToUpload"></div>
            </div>
            <div class="div-table-row">
                <div class="div-table-col-5" style="font-weight:bold; vertical-align:top">Reset Data</div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5">
                    <?php echo $class->inputCheckBox('chkReset'); ?>
                    <div class="text-red-cardinal" style="font-size:0.8em; font-style:italic">Jika opsi ini dipilih, semua data dan transaksi yang berhubungan akan dihapus.<br>Data yang telah terhapus tidak dapat dikembalikan.</div> 
                </div>
            </div>
            <div class="div-table-row">
                <div class="div-table-col-5" style="font-weight:bold">Token</div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><?php echo $class->inputText('token'); ?></div>
            </div>
            <div class="div-table-row">
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"></div>
                <div class="div-table-col-5"><?php echo $class->inputSubmit('btnSubmit','Import'); ?></div>
            </div>
        </div> 
    </form>
</div>     
</body> 
</html> 
