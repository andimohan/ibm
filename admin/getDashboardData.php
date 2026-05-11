<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 


// harus disesuaikan dengan tipe client
includeClass(array(
                    'Marketplace.class.php', 
                    'Customer.class.php',  
                    'AP.class.php', 
                    'AR.class.php', 
                    'APEmployee.class.php', 
                    'AREmployee.class.php', 
                    'Car.class.php', 
                    'WidgetSetting.class.php', 
                    'ChartOfAccount.class.php', 
                    'PurchaseOrder.class.php', 
                    'CashBankRealization.class.php', 
                    'MedicalJobOrder.class.php', 
                    'PettyCash.class.php',
                    'SupplierCategory.class.php',
                    'Supplier.class.php'  
                  ));

$customer = createObjAndAddToCol(new Customer()); 

$isActiveModule = $class->isActiveModule(array('TruckingServiceOrder','PurchaseOrder','SalesOrder','Item','MedicalJobOrder','EMKLJobOrder','DisposalJobOrder','CarServiceMaintenance','PettyCash'));

if($isActiveModule['truckingserviceorder']){ 
    
    includeClass(array( 
                    'TruckingServiceOrder.class.php',
                    'TruckingServiceWorkOrder.class.php',
                    'TruckingServiceOrderInvoice.class.php' 
                  ));
    
    $truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());   
    $truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice()); 
    $truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder()); 
}

if($isActiveModule['emkljoborder']){ 
    
    includeClass(array( 
                    'EMKLJobOrder.class.php',
                    'EMKLOrderInvoice.class.class.php',  
                    'CashAdvance.class.php', 
                  ));
    
    $emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());
    $emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['export']));   
    $emklJobOrderImport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['import']));   
    $emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice());  
}

if($isActiveModule['purchaseorder']){  
    includeClass(array( 'PurchaseOrder.class.php',  ));
    $purchaseOrder = createObjAndAddToCol(new PurchaseOrder()); 
}

if($isActiveModule['salesorder']){  
    includeClass(array( 'SalesOrder.class.php',  ));
    $salesOrder = createObjAndAddToCol(new SalesOrder()); 
}

if($isActiveModule['medicaljoborder']){ 
        
    includeClass(array( 
        'MedicalJobOrder.class.php',  
        'MedicalSalesInvoice.class.php',
        'MedicalRequestClaim.class.php',
        'MedicalSalesOrderQuotation.class.php',
        'MedicalPurchaseOrder.class.php',
        'Reminder.class.php'
    ));
    $medicalJobOrder = createObjAndAddToCol(new MedicalJobOrder()); 
    $medicalSalesInvoice = createObjAndAddToCol(new MedicalSalesInvoice()); 
    $medicalRequestClaim = createObjAndAddToCol(new MedicalRequestClaim()); 
    $medicalSalesOrderQuotation = createObjAndAddToCol(new MedicalSalesOrderQuotation()); 
    $medicalPurchaseOrder = createObjAndAddToCol(new MedicalPurchaseOrder()); 
    $reminder = createObjAndAddToCol(new Reminder()); 
}


if($isActiveModule['disposaljoborder']){ 
    includeClass(array( 
        'DisposalJobOrder.class.php',  
        'DisposalContract.class.php',
        'DisposalWorkOrderDispatcher.class.php',
        'DisposalWorkOrder.class.php',
        'DisposalSalesInvoice.class.php',
        'DisposalPurchaseOrder.class.php'
    ));
    $disposalJobOrder = createObjAndAddToCol(new DisposalJobOrder()); 
    $disposalContract = createObjAndAddToCol(new DisposalContract()); 
    $disposalWorkOrderDispatcher = createObjAndAddToCol(new DisposalWorkOrderDispatcher()); 
    $disposalWorkOrder = createObjAndAddToCol(new DisposalWorkOrder()); 
    $disposalSalesInvoice = createObjAndAddToCol(new DisposalSalesInvoice()); 
    $disposalPurchaseOrder = createObjAndAddToCol(new DisposalPurchaseOrder()); 
}


if($isActiveModule['item']){ 
includeClass('Item.class.php');
$item = createObjAndAddToCol(new Item());  
}


if($isActiveModule['carservicemaintenance']){ 
            includeClass(array(
                'CarServiceMaintenance.class.php', 
                'CarCategory.class.php'
                ));
    $carCategory = createObjAndAddToCol(new CarCategory());  
    $carServiceMaintenance = createObjAndAddToCol(new CarServiceMaintenance());  
}
if($isActiveModule['pettycash']){ 
            includeClass(array(
                'PettyCash.class.php',
                'ChartOfAccount'
                ));

    $pettyCash = createObjAndAddToCol(new PettyCash()); 
    $chartOfAccount = createObjAndAddToCol(new ChartOfAccount()); 
}

$widgetSetting = createObjAndAddToCol(new WidgetSetting()); 
$rsWidgetProperties = $widgetSetting->getPropertiesValue();
$rsWidgetProperties = $class->reindexDetailCollections($rsWidgetProperties,'name');
    
$rowsLimit = 25;
$amountIn = $class->loadSetting('dashboardAmountIn');
if(empty($amountIn)) $amountIn = 1000;

// sementara dalam ribuan dulu labelnya
$saysInThousand = ($amountIn == 1000) ? $class->lang['inThousand'] : '';

$footerTemplate = '
<div class="footer">
    <div class="flex">
        <div class="consume">{{FOOTER}}</div>
        <div class="icon-panel">{{ICON_SETTING}} <i class="fal fa-times remove-widget"></i></div>
    </div>
</div>';

$templatePanel = '<div class="content">
<div class="flex  justify-content-space-between">
    <div class="title">{{TITLE}}</div>
    <div>{{HEADER_SUMMARY}}</div>
</div>
<div class="body">{{CONTENT}}</div>
'.$footerTemplate.'
</div>';

$templateGraphPanel = '<div class="content">{{CONTENT}}</div>'.$footerTemplate; 

$AIButton = '';

if(PLAN_TYPE['categorykey'] == 1){
    $AIButton = '<div class="div-button ai-assist" rel="salesOrderSummary" onClick="AIAnalyze(this)">
                  <div><i class="fa-regular fa-microchip-ai"></i></div>
                  <div>'.$class->lang['AIAnalysis'].'</div>
                </div>';

}
    
$templateLineGraphPanel = '<div class="main-opt-panel">   
                                '.$AIButton.'
                                <div><i class="btn-widget-setting fal fa-cog"></i></div> 
                        </div>   
                        <div class="content">{{CONTENT}}</div>'; 
	
if(!isset($_POST) || empty($_POST['data'])) die;
	  
$arrReturn = array();
foreach($_POST['data'] as $data){
    
    $start_time = microtime(TRUE);
    
    $arrayToJs = array();   
    switch ($data['action']){ 
        case 'salesGraph' :  
        case 'truckingSalesGraph' :  
        case 'medicalSalesGraph' :  
        case 'disposalSalesGraph' :  
        case 'emklSalesGraph' :  
            $arrayToJs = generatePanelSalesGraph($data['action'], $data['startPeriod'],$data['endPeriod'],$data['warehousekey']);  
            break; 
        case 'marketplace' :  
            $arrayToJs = generatePanelMarketplace($data['action']);  
            break;
        case 'profitByItemGraph' :  
            $arrayToJs = generatePanelProfitByItemGraph($data['action'],$data['startPeriod'],$data['endPeriod'],$data['warehousekey']); 
            break;
        case 'profitByCategoryGraph' :  
            $arrayToJs = generatePanelProfitByCategoryGraph($data['action'],$data['startPeriod'],$data['endPeriod'],$data['warehousekey']);   
            break;
        case 'profitByBrandGraph' :  
            $arrayToJs = generatePanelProfitByBrandGraph($data['action'],$data['startPeriod'],$data['endPeriod'],$data['warehousekey']);  
            break; 
        case 'bestSellingGraph' :  
            $arrayToJs = generatePanelBestSellingGraph($data['action'],$data['startPeriod'],$data['endPeriod'],$data['warehousekey']); 
            break; 

        case 'topCustomerGraph' : 
        case 'truckingTopCustomerGraph' :  
        case 'disposalTopCustomerGraph' :  
            $arrayToJs = generatePanelTopCustomerGraph($data['action'],$data['startPeriod'],$data['endPeriod'],$data['warehousekey']);  
            break;

        case 'pendingPurchaseOrder' :  
            $arrayToJs = generatePanelPendingPurchaseOrder($data['action'], 10,$data['warehousekey']);   
            break;
        case 'pendingGuaranteeLetter' :  
            $arrayToJs = generatePanelPendingGuaranteeLetter($data['action']);  
            break;

         case 'pendingSalesOrder' :  
            $arrayToJs = generatePanelPendingSalesOrder($data['action'], 10,$data['warehousekey']);   
            break;

        case 'pendingNewRequest' :  
            $arrayToJs = generatePanelPendingNewRequest($data['action']);  
            break;

        case 'pendingInvoice' :  
            $arrayToJs = generatePanelPendingInvoice($data['action']);  
            break;

        case 'pendingSalesOrderQuotation' :  
            $arrayToJs = generatePanelPendingSalesOrderQuotation($data['action'], 10,$data['warehousekey']);   
            break;

        case 'pendingReminder' :  
            $arrayToJs = generatePanelPendingReminder($data['action']);  
            break;

        case 'minStock' :  
            $arrayToJs = generatePanelMinStock($data['action']);  
            break;

         case 'maxStock' :  
            $arrayToJs = generatePanelMaxStock($data['action']);  
            break;

         case 'notMovingStock' :  
            $arrayToJs = generatePanelNotMovingStock($data['action']);  
            break;

         case 'emptyStock' :  
            $arrayToJs = generatePanelEmptyStock($data['action']);  
            break;

        case 'overdueAP' :  
            $arrayToJs = generatePanelOverdueAP($data['action'],10,$data['warehousekey']);   
            break;

        case 'overdueAR' :  
            $arrayToJs = generatePanelOverdueAR($data['action'],10,$data['warehousekey']); 
            break;

        case 'outstandingAR' :  
            $arrayToJs = generatePanelOutstandingAR($data['action'],10,$data['warehousekey']); 
            break;
        //case 'unCollectedAR' :  
        //    $arrayToJs = generatePanelUnCollectedAR($data['action'],$data['warehousekey']); 
        //    break;
        case 'underMarginSalesOrder' :  
            $arrayToJs = generatePanelUnderMarginSalesOrder($data['action'],10,$data['warehousekey']);  
            break;

        case 'truckingServiceOrderStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="far fa-edit"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['jobOrder'],$truckingServiceOrder, $data['startPeriod'],$data['endPeriod'],$bgDiv,$data['warehousekey']);  
            break;

        case 'medicalJobOrderStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="far fa-edit"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['jobOrder'],$medicalJobOrder, $data['startPeriod'],$data['endPeriod'],$bgDiv,$data['warehousekey']);  
            break;
            
        case 'emklJobOrderImportStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="far fa-edit"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['jobOrderImport'],$emklJobOrderImport, $data['startPeriod'],$data['endPeriod'],$bgDiv,$data['warehousekey']);  
            break;
            
        case 'emklJobOrderExportStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="far fa-edit"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['jobOrderExport'],$emklJobOrderExport, $data['startPeriod'],$data['endPeriod'],$bgDiv,$data['warehousekey']);   
            break;

 
        case 'emklOrderInvoiceStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="far fa-edit"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['invoice'],$emklOrderInvoice, $data['startPeriod'],$data['endPeriod'],$bgDiv,$data['warehousekey']);   
            break;


        case 'truckingServiceWorkOrderStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-tasks"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['workOrder'],$truckingServiceWorkOrder, $data['startPeriod'],$data['endPeriod'],$bgDiv,$data['warehousekey']);   
            break; 

        case 'medicalRequestClaimStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-tasks"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['newRequest'], $medicalRequestClaim, $data['startPeriod'],$data['endPeriod'],$bgDiv,$data['warehousekey']);  
            break; 

        case 'medicalSalesQuotationStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-tasks"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['priceQuotation'], $medicalSalesOrderQuotation, $data['startPeriod'],$data['endPeriod'],$bgDiv,$data['warehousekey']);   
            break;

        case 'guaranteeLetterStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-tasks"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['guaranteeLetter'], $medicalPurchaseOrder, $data['startPeriod'],$data['endPeriod'],$bgDiv,$data['warehousekey']);   
            break; 
        case 'reminderStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-tasks"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['reminder'], $reminder, $data['startPeriod'],$data['endPeriod'],$bgDiv,$data['warehousekey']);   
            break; 

        case 'truckingServiceOrderInvoiceStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-receipt"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['salesInvoice'],$truckingServiceOrderInvoice, $data['startPeriod'],$data['endPeriod'], $bgDiv,$data['warehousekey']);   
            break;

        case 'medicalSalesInvoiceStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-receipt"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['salesInvoice'],$medicalSalesInvoice, $data['startPeriod'],$data['endPeriod'], $bgDiv,$data['warehousekey']);  
            break;


        case 'disposalJobOrderStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-receipt"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['jobOrder'],$disposalJobOrder, $data['startPeriod'],$data['endPeriod'], $bgDiv,$data['warehousekey']);   
            break;

        case 'disposalContractStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-receipt"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['jobContract'],$disposalContract, $data['startPeriod'],$data['endPeriod'], $bgDiv,$data['warehousekey']);   
            break;

        case 'disposalWorkOrderDispatcherStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-receipt"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['workOrderDispatcher'],$disposalWorkOrderDispatcher, $data['startPeriod'],$data['endPeriod'], $bgDiv,$data['warehousekey']);    
            break;

        case 'disposalWorkOrderStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-receipt"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['workOrder'],$disposalWorkOrder, $data['startPeriod'],$data['endPeriod'], $bgDiv,$data['warehousekey']);   
            break;

        case 'revenueAndCostByCustomerGraph' :  
            $arrayToJs = generateRevenueAdCostByCustomerGraph($data['action'], $data['startPeriod'],$data['endPeriod'], 'month', $data['warehousekey']);  
            break;

        case 'carServiceMaintenanceByItemCategoryGraph' :  
            $arrayToJs = generatecarServiceMaintenanceByItemCategoryGraph($data['action'], $data['startPeriod'],$data['endPeriod'], 'month', $data['warehousekey']);  
            break;

        case 'disposalPurchaseOrderStatus' :   
            $bgDiv = '<div style="font-size:7em; position:absolute; right:0.3em; bottom: 0.1em; color: rgba(255,255,255, 0.1)"><i class="fas fa-receipt"></i></div>';
            $arrayToJs = generateTransactionStatus($class->lang['purchaseOrder'],$disposalPurchaseOrder, $data['startPeriod'],$data['endPeriod'], $bgDiv,$data['warehousekey']);   
            break;
        case 'truckingCostRevenueGraph' :    
            $arrayToJs = generatePanelTruckingCostRevenueGraph($data['action'],$data['startPeriod'],$data['endPeriod'],$data['warehousekey']);     
            break;
        case 'truckingCostMaintenanceRevenueGraph' :    
            $arrayToJs = generatePanelTruckingCostMaintenanceRevenueGraph($data['action'],$data['startPeriod'],$data['endPeriod'],$data['warehousekey']);     
            break;

        case 'truckingVehicleOverdue' :    
            $arrayToJs = generatePanelVehicleOverdue($data['action'],$data['warehousekey']);  
            break;

        case 'driverLicenseExpired' :    
            $arrayToJs = generatePanelDriverLicenseExpired($data['action'],$data['warehousekey']);  
            break;

        case 'dailyTransactionSummary' :
            $arrayToJs = generatePanelDailyTransactionSummary($data['action'],10,$data['warehousekey']);  
            break;
        case 'dailyMarketplaceTransactionSummary' :
            $arrayToJs = generatePanelDailyMarketplaceTransactionSummary($data['action'],10,$data['warehousekey']);   
            break;
        case 'cashBankOutstanding' :
            $arrayToJs = generatePanelCashBankOutstanding($data['action']);   
            break; 
        case 'cashAdvance' :
            $arrayToJs = generatePanelCashAdvance($data['action'],$data['warehousekey']);   
            break; 
        case 'topCustomerByJO' :
            $arrayToJs = generatePanelTopCustomerByJO($data['action'],$data['startPeriod'],$data['endPeriod'],$data['warehousekey']);   
            break;
            
        case 'cashBankRealizationSummary' :
            $arrayToJs = generatePanelCashBankRealizationSummary($data['action']);  
            break;
        case 'customerCreditLimitSummary' :
            $arrayToJs = generatePanelCustomerCreditLimitSummary($data['action']);  
            break;
            
        case 'disposalUninvoicedJobOrder' :
            $arrayToJs = generateUninvoicedJobOrder($data['action']);  
            break;
            
        case 'dailyOngoingWorkOrder' : 
            $arrayToJs = generateDailyOngoingWorkOrder($data['action'], $data['warehousekey']);  
            break;
        case 'carServiceMaintenanceGraph':
            $arrayToJs = generatePanelCarServiceMaintenanceGraph($data['action'], $data['startPeriod'], $data['endPeriod'], $data['warehousekey']);
            break;

        case 'pettyCash':
            $arrayToJs = generatePanelPettyCash($data['action'],$data['endPeriod']);
            break;
        case 'outstandingAP':
            $arrayToJs = generatePanelOutstandingAP($data['action']);
            break;
        case 'COAMonitoring1':
            $arrayToJs = generateCOAMonitoring($data['action'],$data['startPeriod'], $data['endPeriod'], $class->lang['pettyCash']);
            break;
        case 'COAMonitoring2':
            $arrayToJs = generateCOAMonitoring($data['action'],$data['startPeriod'], $data['endPeriod'], $class->lang['unCollectedAR']);
            break; 
		case 'incomeStatementGraph' :  
            $arrayToJs = generateIncomeStatementGraph($data['action'], $data['startPeriod'],$data['endPeriod'], 'month', $data['warehousekey']);  
            break; 
    } 
    
    $arrReturn[$data['action']] = $arrayToJs; 

    // $perfomanceLog = getPerformanceLog($start_time);

    // if( $perfomanceLog['memoryPeak']> 10000){
    //     $fileName = '../'.DOMAIN_NAME.'-dashboard-performance.txt';
    //     $class->setLog($data['action'],true,$fileName);
    //     $class->setLog($perfomanceLog['msg'].chr(13).$perfomanceLog['memoryPeak'],true,$fileName);
    // }
     

}

echo json_encode($arrReturn);   
die;
 
function generatePanelSalesGraph($panelName, $startPeriod, $endPeriod,$warehousekey){ 
   
    global $class;   
    global $amountIn;
    global $saysInThousand;
    global $security;
    global $customer;
    global $item;
    global $templateLineGraphPanel;
	 
    $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
    
    $arrTitle = array();
    array_push($arrTitle, $class->lang['period']);
    array_push($arrTitle, $class->lang['sales']);
      
    $arrData = array();
    
    $companyPlanType = PLAN_TYPE['categorykey'];
    
    //sementaraa
    $arrExclude = array('fms.wintera.co.id','marvel.wintera.co.id','eagle.wintera.co.id','trioeaglelogistic.wintera.co.id', 'airtel.wintera.co.id','thewhale.wintera.co.id','okl.wintera.co.id');

    if(in_array(DOMAIN_NAME,$arrExclude)) 
        $companyPlanType = COMPANY_TYPE['forwarding'];
   
    switch ($companyPlanType){
        case COMPANY_TYPE['retail'] :
        case COMPANY_TYPE['jewelry'] :    
					global $salesOrder;
                    $obj = $salesOrder;  
                     
                    // kalo ad marketplace
                    $rsMarketplaceCustomer = $customer->searchData($customer->tableName.'.ismarketplace',1,true); // gk peduli aktif atau gk
                    $rsMarketplaceCustomer = array_column($rsMarketplaceCustomer,'pkey'); 
            
                    if(!empty($rsMarketplaceCustomer))  array_push($arrTitle, $class->lang['marketplace']);
                    if($hasCOGSAccess)  array_push($arrTitle, $class->lang['profit']);
            
                    //all sales
                    array_push($arrData, $obj->getSalesByMonth( $startPeriod, $endPeriod,'',$warehousekey ));  
                     
                    //marketplace sales
                    if(!empty($rsMarketplaceCustomer))  
                    array_push($arrData, $obj->getSalesByMonth( $startPeriod, $endPeriod, ' and '.$obj->tableName.'.customerkey in ('.$obj->oDbCon->paramString($rsMarketplaceCustomer,',').')' , $warehousekey));  
                     
                    // COGS
                    if($hasCOGSAccess) 
                        array_push($arrData, $obj->getProfitByMonth( $startPeriod, $endPeriod, $warehousekey )); 
            
                    break;
			
        case COMPANY_TYPE['trucking'] :    
            
					global $rsWidgetProperties;  
                    global $truckingServiceOrder;
                    global $truckingServiceOrderInvoice;
                    $obj = $truckingServiceOrder;
               
					// cek properties 
					$rsSettings = $rsWidgetProperties[$panelName];
					$rsSettings = array_column($rsSettings,null,'properties');
					 
					$graphItem = getWidgetValue($rsSettings,'graphitem');  

                    $arrTitle = array();
                    array_push($arrTitle, $class->lang['period']);
			
			
					if(in_array('jobOrder',$graphItem)){ 
						array_push($arrTitle, $class->lang['jobOrder']);	
						array_push($arrData, $obj->getJobOrderByMonth( $startPeriod, $endPeriod,$warehousekey ));
					}
                    
					if(in_array('cost',$graphItem)){
						if($hasCOGSAccess)  {
							array_push($arrTitle, $class->lang['cost']);
							array_push($arrData, $obj->getTruckingCostByMonth( $startPeriod, $endPeriod,$warehousekey )); 
						}
					}
                      
					if(in_array('invoice',$graphItem)){   
						array_push($arrTitle, $class->lang['invoice']);
						array_push($arrData, $truckingServiceOrderInvoice->getInvoiceByMonth( $startPeriod, $endPeriod,$warehousekey ));  
					} 
            
                    break;
//       case COMPANY_TYPE['workshop'] :    
//                    global $salesOrderCarService;
//                    $obj = $salesOrderCarService;
//                    break;   
//       case COMPANY_TYPE['tpamedical'] :  
//                    global $medicalJobOrder;
//                    global $medicalSalesInvoice;
//                    $obj = $medicalJobOrder;
//            
//
//                    $arrTitle = array();
//                    array_push($arrTitle, $class->lang['period']);
//                    array_push($arrTitle, $class->lang['jobOrder']);
//
//                    if($hasCOGSAccess)  array_push($arrTitle, $class->lang['cost']);
//                    
//                    array_push($arrTitle, $class->lang['invoice']);
//            
//                    array_push($arrData, $obj->getJobOrderByMonth( $startPeriod, $endPeriod ));
//                
//                    if($hasCOGSAccess) array_push($arrData, $obj->getTruckingCostByMonth( $startPeriod, $endPeriod )); 
//                    
//                    array_push($arrData, $medicalSalesInvoice->getInvoiceByMonth( $startPeriod, $endPeriod ));   
//                    
//                    break;
			
       case COMPANY_TYPE['forwarding'] :  
					global $emklJobOrderImport;
					global $emklJobOrderExport;
                    global $emklOrderInvoice;
					global $rsWidgetProperties;
			 
                    $obj = $emklJobOrderImport; // but getMonthPeriode dibawah
            
                    // cek properties 
					$rsSettings = $rsWidgetProperties[$panelName]??[]; 
					$rsSettings = array_column($rsSettings,null,'properties');
					 
					$graphDateType = getWidgetValue($rsSettings,'graphdatetype');  
                    
                    switch($graphDateType){
                        case 2 : $dateColumn = 'etdpol'; break;
                        case 3 : $dateColumn = 'etapod'; break;
                        default : $dateColumn = 'trdate'; 
                    }
             
            
                    $arrTitle = array();
                    array_push($arrTitle, $class->lang['period']);
                    array_push($arrTitle, $class->lang['importOrderSheet']);
                    array_push($arrTitle, $class->lang['exportOrderSheet']);
 
                    array_push($arrData, $emklJobOrderImport->getJobOrderByMonth( $startPeriod, $endPeriod, $dateColumn, $warehousekey ));
                    array_push($arrData, $emklJobOrderExport->getJobOrderByMonth( $startPeriod, $endPeriod, $dateColumn, $warehousekey )); 
                    //array_push($arrData, $emklOrderInvoice->getInvoiceByMonth( $startPeriod, $endPeriod ));   
                    
                    break;
            
        case COMPANY_TYPE['logistics'] :  
					global $disposalJobOrder; 
                    global $disposalSalesInvoice;
					global $rsWidgetProperties;
			 
                    $obj = $disposalJobOrder;  
            
                    // cek properties 
					$rsSettings = $rsWidgetProperties[$panelName]; 
					$rsSettings = array_column($rsSettings,null,'properties');
					 
            
                    $arrTitle = array();
                    array_push($arrTitle, $class->lang['period']);
                    array_push($arrTitle, $class->lang['jobOrder']);
                    if($hasCOGSAccess)  array_push($arrTitle, $class->lang['cost']);
                    array_push($arrTitle, $class->lang['invoice']);
 
                    array_push($arrData, $obj->getJobOrderByMonth( $startPeriod, $endPeriod ,$warehousekey));
                    if($hasCOGSAccess) array_push($arrData, $obj->getTruckingCostByMonth( $startPeriod, $endPeriod,$warehousekey )); 
                    array_push($arrData, $disposalSalesInvoice->getInvoiceByMonth( $startPeriod, $endPeriod,$warehousekey ));   
                    
                    break;
    }
 
     
    $title = $obj->lang['salesGraph'].' '.$saysInThousand; 
  
    $arrayToJs = array();  
    array_push($arrayToJs,$arrTitle) ; 
    
    $rsDataPeriod = array();
    
    for ($ctr=0;$ctr<count($arrData);$ctr++){   
        $rs = $arrData[$ctr];
        for($i=0;$i<count($rs);$i++)  
            $rsDataPeriod[$rs[$i]['month'] .$rs[$i]['year']][$ctr] = $rs[$i]['total']; // (isset($rs[$i][$ctr])) ? $rs[$i]['total'] : 0;   
    }
    
    $period = $obj->getMonthPeriod($startPeriod, $endPeriod);
    
    foreach ($period as $dt) {
        
        $keyIndex = $dt->format('nY'); 
       
        $tempArray = array();
        array_push($tempArray, $dt->format('M Y'));
         
        for ($ctr=0;$ctr<count($arrData);$ctr++){   
            $value = (isset($rsDataPeriod[$keyIndex][$ctr])) ? $rsDataPeriod[$keyIndex][$ctr] / $amountIn  : 0 ;
            array_push($tempArray, $value); 
        }
        
        array_push($arrayToJs,$tempArray) ; 
    }
    
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawSalesChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
      
    $html =  generateLineChart($arrParam); 
	
//    $replacement = array(); 
//    $replacement['title'] = '';   
//    $replacement['content'] = '';    
//    return replaceContent($replacement, $templateLineGraphPanel).$html;  
	 
    return $templateLineGraphPanel.$html;  
        
}
 

function generatePanelCarServiceMaintenanceGraph($panelName, $startPeriod, $endPeriod, $warehousekey)
{
    global $class;
    global $amountIn;
    global $security;
    global $templateLineGraphPanel;
	global $rsWidgetProperties;

    $arrTitle = array();
    array_push($arrTitle, $class->lang['period']);

    $arrData = array();
    
    $companyPlanType = PLAN_TYPE['categorykey'];

    // cek properties 
    $rsSettings = $rsWidgetProperties[$panelName];
    if(!empty($rsSettings)){ 
        $rsSettings = array_column($rsSettings,null,'properties');
        $vehicleGroup = getWidgetValue($rsSettings,'vehiclegroup'); 
        if(empty($vehicleGroup)) $vehicleGroup = 1;
    }else{
        $vehicleGroup = 1;
    }
     

    //sementara
    $arrExclude = array('tms.wintera.co.id');

    if(in_array(DOMAIN_NAME,$arrExclude)) 
        $companyPlanType = COMPANY_TYPE['trucking'];

    switch ($companyPlanType){

        case COMPANY_TYPE['trucking'] :    
            global $rsWidgetProperties;                   
            global $carCategory;
            global $carServiceMaintenance;
            $obj = $carServiceMaintenance;

            $rsCarCategory = $carCategory->searchDataRow( array($carCategory->tableName.'.pkey',$carCategory->tableName.'.name'),
                                                        ' and ' . $carCategory->tableName.'.statuskey = 1 order by orderlist asc');
      
            
            if ($vehicleGroup==1){
                 $arrCategoryKey =   array_column($rsCarCategory,'pkey');
                 $groupBy = '';
            }else{
                $arrCategoryKey = array();
                $groupBy = 'group by year(trdate),month(trdate)';
            }
      
            $rsMaintenance =  $obj->getServiceMaintenanceByMonth($startPeriod, $endPeriod,$arrCategoryKey, $warehousekey,$groupBy);
            
            if ($vehicleGroup==1){
                $rsMaintenance = $class->reindexDetailCollections($rsMaintenance,'categorykey');
            
                foreach($rsCarCategory as $category){
                    if(empty($rsMaintenance[$category['pkey']])) continue;

                    array_push($arrTitle, $category['name']);	
                    array_push($arrData, $rsMaintenance[$category['pkey']]);
                }
            }else{
                
                    array_push($arrTitle, $obj->lang['allVehicle']);	
                    array_push($arrData, $rsMaintenance);
            }

            break;
        }
 
        $title = $obj->lang['carServiceMaintenanceGraph'].' '.$obj->lang['inThousand']; 

        $arrayToJs = array();  
        array_push($arrayToJs,$arrTitle) ; 
        
        $rsDataPeriod = array();
        
        for ($ctr=0;$ctr<count($arrData);$ctr++){   
            $rs = $arrData[$ctr];
            for($i=0;$i<count($rs);$i++)  
                $rsDataPeriod[$rs[$i]['month'] .$rs[$i]['year']][$ctr] = $rs[$i]['total']; // (isset($rs[$i][$ctr])) ? $rs[$i]['total'] : 0;   
        }
        
        $period = $obj->getMonthPeriod($startPeriod, $endPeriod);
        
        foreach ($period as $dt) {
            
            $keyIndex = $dt->format('nY'); 
        
            $tempArray = array();
            array_push($tempArray, $dt->format('M Y'));
            
            for ($ctr=0;$ctr<count($arrData);$ctr++){   
                $value = (isset($rsDataPeriod[$keyIndex][$ctr])) ? $rsDataPeriod[$keyIndex][$ctr] / $amountIn  : 0 ;
                array_push($tempArray, $value); 
            }
            
            array_push($arrayToJs,$tempArray) ; 
        }
        
        $arrParam = array();
        $arrParam['data'] = $arrayToJs;
        $arrParam['callbackName'] = 'drawCarServiceMaintenanceChart';
        $arrParam['panelName'] = $panelName;
        $arrParam['title'] = $title ;
        
        $html =  generateLineChart($arrParam);
        // $obj->setLog($html, true);
        return $templateLineGraphPanel.$html;   

}


function generatecarServiceMaintenanceByItemCategoryGraph($panelName, $startPeriod, $endPeriod, $groupBy, $warehousekey){
	
	global $class;   
    global $amountIn;
    global $security;  
    global $templateLineGraphPanel;
    global $rsWidgetProperties;
    $carServiceMaintenance = new CarServiceMaintenance(); 
    $item = new Item(); 
    $itemCategory = new ItemCategory(); 
    $obj = $carServiceMaintenance;

    $rsSettings = $rsWidgetProperties[$panelName];
    $arrCategoryKey = array();
    // filter berdasarkan kategori
    if(!empty($rsSettings)){ 

    }else{

    }

    $criteria = '';
    if (!empty($arrCategoryKey))
        $criteria =' and '. $itemCategory->tableName.'.pkey in ('. $obj->oDbCon->paramString($arrCategoryKey,',').' )';

    $rsCategory = $itemCategory->searchDataRow(array($itemCategory->tableName.'.pkey',$itemCategory->tableName.'.name'),
                                                                ' and '. $itemCategory->tableName.'.statuskey = 1'.$criteria);

    $arrTitle = array();
    array_push($arrTitle, $class->lang['period']); 

    foreach($rsCategory as $categoryRow) 					   
		array_push($arrTitle, html_entity_decode($categoryRow['name']));	
     
    
    $arrayToJs = array();  
    array_push($arrayToJs,$arrTitle) ; 
    
    $rsData = $obj->getCostByCategoryKey($startPeriod, $endPeriod, $warehousekey, $arrCategoryKey, 'categorykey'); 
    
    $rsData = $obj->reindexDetailCollections($rsData,'period');
    
    $title = $obj->lang['carServiceMaintenanceByItemCategory'] ; 

    $rsDataPeriod = array(); 
  
    
    $period = $obj->getMonthPeriod($startPeriod, $endPeriod);
    
    foreach ($period as $dt) {
        
        $keyIndex = $dt->format('Y-m'); 
        $arrData = $rsData[$keyIndex];
        $arrData = $obj->reindexDetailCollections($arrData,'categorykey');
        
       
        $tempArray = array();
        array_push($tempArray, $dt->format('M Y'));

        // $costAmount = (!empty($testing)) ? (float)$testing[0]['costamount'] : 0 ;
        // $revenueAmount = (!empty($testing)) ? (float)$testing[0]['revenueamount'] : 0 ;

        for ($ctr=0;$ctr<count($rsCategory);$ctr++) {
            $categoryKey = $rsCategory[$ctr]['pkey'];
            $data = $arrData[$categoryKey];
            
            $costAmount = (!empty($data)) ? (float)$data[0]['costamount'] : 0 ;
            array_push($tempArray, $costAmount ); 

        }   
        
        array_push($arrayToJs,$tempArray) ; 
    }
    
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawTransactionCarServiceMaintenanceByItemCategory';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
      
    $html =  generateColumnChart($arrParam); 
	 
    return $templateLineGraphPanel.$html;  
}


function generateRevenueAdCostByCustomerGraph($panelName, $startPeriod, $endPeriod, $groupBy, $warehousekey){
	
	global $class;     
    global $templateLineGraphPanel;
    global $truckingServiceOrder; 
    global $rsWidgetProperties;
    global $customer;
    
    $truckingServiceOrderCategory = new TruckingServiceOrderCategory(); 
    $obj = $truckingServiceOrder;

    $rsSettings = $rsWidgetProperties[$panelName];
    $rsSettings = array_column($rsSettings,null,'properties');
    
    if(!empty($rsSettings)){ 
        // filter berdasarkan customerkey  
        $customerkey = getWidgetValue($rsSettings,'revenuecostcustomername');  
    }else{ 
        $rsCust = $customer->searchDataRow( array($customer->tableName.'.pkey'), ' and '.$customer->tableName.'.statuskey = 1','limit 1');
        $customerkey = $rsCust[0]['pkey'] ?? 0; 
    }

    $arrTitle = array();
    array_push($arrTitle, $class->lang['period']); 
    array_push($arrTitle, $class->lang['cost']); 
    array_push($arrTitle, $class->lang['revenue']); 
     
    
    $arrayToJs = array();  
    array_push($arrayToJs,$arrTitle) ; 
    
    $rsData = $obj->getTruckingRevenueCostAmountByCustomer($startPeriod, $endPeriod, $customerkey, $warehousekey); 
    //$customerkey = (!empty($customerkey)) ? $customerkey : $rsData[0]['customerkey']; // pasti ada
    $rsData = $obj->reindexDetailCollections($rsData,'customerkey');
    
    $arrData = $rsData[$customerkey]; 
    $title = $obj->lang['revenueAndCost'].' - '.$arrData[0]['customername'].'\n*diluar perawatan mobil'; 
    $rsDataPeriod = array(); 
  
    
    $period = $obj->getMonthPeriod($startPeriod, $endPeriod);
    
    $data = $obj->reindexDetailCollections($arrData,'period');
    foreach ($period as $dt) {
        
        $keyIndex = $dt->format('Y-m'); 
        $dataRow = $data[$keyIndex];
       
        $tempArray = array();
        array_push($tempArray, $dt->format('M Y'));

        $costAmount = (!empty($dataRow)) ? (float)$dataRow[0]['costamount'] : 0 ;
        $revenueAmount = (!empty($dataRow)) ? (float)$dataRow[0]['revenueamount'] : 0 ;

        array_push($tempArray, $costAmount );  
        array_push($tempArray, $revenueAmount );  
        
        array_push($arrayToJs,$tempArray) ; 
    }
    
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawTransactionCostAndRevenueByCustomer';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
      
    $html =  generateColumnChart($arrParam); 
	 
    return $templateLineGraphPanel.$html;  
}

function generateColumnChart($arrParam,$opt=array()){  
    global $class;
    
    $arrData = $arrParam['data']; 
    $callbackName = $arrParam['callbackName'];  
    $panelName = $arrParam['panelName'] .' .content'; 
    $title = $arrParam['title'];
    $subtitle = (isset($arrParam['subtitle'])) ? '\n'.$arrParam['subtitle'] : '\n'.$arrData[1][0].' - '. $arrData[count($arrData)-1][0];
	  
	$colorSet = (isset($opt['colorSet']) && !empty($opt['colorSet'])) ? $opt['colorSet'] : $class->graphLineColorSet;
	 
    $content = '
        <script>  
            setTimeout(function(){google.load(\'visualization\', \'1\', {\'callback\':\''.$callbackName.'\', \'packages\':[\'corechart\']})} );  
     
            // tetep harus ad, agar keresize ketika di tab Dashboard
            $(window).resize(function(){   if (getSelectedTabIndex() == 0) '.$callbackName.'(); });   
            dashboardRedrawFunc.push('.$callbackName.');

            function '.$arrParam['callbackName'].'() {   
 
            var data = google.visualization.arrayToDataTable($.parseJSON(\''.json_encode($arrData).'\'));';

     
    $content .= '    
                  // Set chart options
                   
                  var options = {
                                    title: \''.$title.$subtitle.'\',
                                    chartArea: {\'width\': \'90%\', \'left\': \'80\' },
                                    pointSize: 5,
                                    vAxis: { gridlines: { count: 8 }, textStyle: {   color:\'#666\', fontSize: 12 } }, 
                                    tooltip : {  textStyle: {   color:\'#666\', fontSize: 12 } },
                                    hAxis: { textStyle: {   color:\'#666\', fontSize: 12 } }, 
                                    legend: {\'position\': \'bottom\',  scrollArrows: { inactiveColor: \'#666\', activeColor:\'#666\' }, pagingTextStyle: {  color:\'#666\' }  },
                                    titleTextStyle : { color: \'#333\', fontName: \'Palanquin\' } ,
                                    series : '.$colorSet.',
                                    animation: {
                                        duration: 1500,
                                        startup: true  
                                    }
                                };

                  // Instantiate and draw our chart, passing in some options.
                  var chart = new google.visualization.ColumnChart('.getPanelDOM($panelName).'); 
                  
                  chart.draw(data, options);
            }

        </script>                            
    ';  
    
    return $class->minimizeJavascriptSimple($content);
}

function generatePanelProfitByCategoryGraph($panelName,$startPeriod, $endPeriod, $warehousekey){ 
   
    global $class;    
    global $item;
    global $amountIn;
    global $templateGraphPanel;
    global $security;
    
    $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
    if(!$hasCOGSAccess) return;
    
    $arrTitle = array();
    array_push($arrTitle, $class->lang['category']);
    array_push($arrTitle, $class->lang['amount']); 
     
    switch (PLAN_TYPE['categorykey']){
        case COMPANY_TYPE['retail']   :
        case COMPANY_TYPE['jewelry'] :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case COMPANY_TYPE['trucking'] :     global $truckingServiceOrder;
                    $obj = $truckingServiceOrder; 
                    break;
        case COMPANY_TYPE['workshop'] :     global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
    
    
    $title = $class->lang['profitByCategory']; 
  
    $rs = $obj->getMostProfitableSalesByGroup($item->tableName.'.categorykey', $startPeriod, $endPeriod ,10,$warehousekey); 
      
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
    
    for ($i=0;$i<count($rs);$i++){   
        $rs[$i]['profit'] = ($rs[$i]['profit'] > 0) ? $rs[$i]['profit']  / $amountIn : 0 ;
        
        $tempArray = array();
        array_push($tempArray, htmlspecialchars_decode($rs[$i]['categoryname']));
        array_push($tempArray, round($rs[$i]['profit']));
        array_push($arrayToJs,$tempArray) ; 
    }
    
     
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawProfitByCategoryChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
    
    $html = generateBarChart($arrParam);
    
    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html;  
}
 
function generatePanelProfitByBrandGraph($panelName, $startPeriod, $endPeriod, $warehousekey){ 

    global $class;   
    global $item;
    global $amountIn;
    global $templateGraphPanel;
    global $security; 
    
    $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
    if(!$hasCOGSAccess) return;
        
    $arrTitle = array();
    array_push($arrTitle, $class->lang['brand']);
    array_push($arrTitle, $class->lang['amount']); 
    
    
    switch (PLAN_TYPE['categorykey']){
        case COMPANY_TYPE['retail'] :
        case COMPANY_TYPE['jewelry'] :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case COMPANY_TYPE['workshop'] :     global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
     
    $title = $class->lang['profitByBrand'];
  
    $rs = $obj->getMostProfitableSalesByGroup($item->tableName.'.brandkey', $startPeriod, $endPeriod,10,$warehousekey); 
     
    if (empty($rs)) 
        return '<div class="data-chart-not-available">'.$title.'<div class="text-silver">'. strtolower($obj->lang['chartNotAvailable']).'</div></div>';
      
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
    
    for ($i=0;$i<count($rs);$i++){ 
        $rs[$i]['profit'] = ($rs[$i]['profit'] > 0) ? $rs[$i]['profit']  / $amountIn : 0 ;
        
        $tempArray = array();
        array_push($tempArray, htmlspecialchars_decode($rs[$i]['brandname'] ?? ''));
        array_push($tempArray, round($rs[$i]['profit']));
        array_push($arrayToJs,$tempArray) ; 
    }
    
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawProfitByBrandChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
    
    $html = generateBarChart($arrParam);

    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html;  
}

function generatePanelProfitByItemGraph($panelName, $startPeriod, $endPeriod, $warehousekey){ 
   
    global $class;    
    global $item; 
    global $amountIn;
    global $templateGraphPanel;
    global $security;
	 
    $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
    if(!$hasCOGSAccess) return;
        
    $arrTitle = array();
    array_push($arrTitle, $class->lang['item']);
    array_push($arrTitle, $class->lang['amount']); 
    
    switch (PLAN_TYPE['categorykey']){
        case COMPANY_TYPE['retail'] :    
        case COMPANY_TYPE['jewelry'] :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case COMPANY_TYPE['workshop'] :     global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
     
    $title = $class->lang['profitByItem'];
      
    $rs = $obj->getMostProfitableSalesByGroup($item->tableName.'.pkey', $startPeriod, $endPeriod,10,$warehousekey); 
 
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
    
    for ($i=0;$i<count($rs);$i++){ 
        $rs[$i]['profit'] = ($rs[$i]['profit'] > 0) ? $rs[$i]['profit']  / $amountIn : 0 ;
        
        $tempArray = array();
        array_push($tempArray, htmlspecialchars_decode($rs[$i]['itemname']));
        array_push($tempArray, round($rs[$i]['profit']));
        array_push($arrayToJs,$tempArray) ; 
    }
      
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawProfitByItemChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ; 
     
    $html = generateBarChart($arrParam);
    
    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html;  
      
}

function generatePanelBestSellingGraph($panelName,$startPeriod, $endPeriod, $warehousekey){ 
    
    global $class;    
    global $item; 
    global $templateGraphPanel;
        
    $arrTitle = array();
    array_push($arrTitle, $class->lang['item']);
    array_push($arrTitle, $class->lang['qty']); 
    
    switch (PLAN_TYPE['categorykey']){
       case COMPANY_TYPE['retail'] :     
        case COMPANY_TYPE['jewelry'] :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
      case COMPANY_TYPE['trucking'] :    global $truckingServiceOrderInvoice;
                    $obj = $truckingServiceOrderInvoice;
                    break;  
     case COMPANY_TYPE['workshop'] :     global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
     
    $title = $class->lang['bestSellingItems'];
      
    $rs = $obj->getBestSellingByGroup($item->tableName.'.pkey', $startPeriod, $endPeriod, 10, $warehousekey); 
 
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
     
    for ($i=0;$i<count($rs);$i++){  
        $tempArray = array();
        array_push($tempArray, htmlspecialchars_decode($rs[$i]['itemname']));
        array_push($tempArray, round($rs[$i]['qty']));
        array_push($arrayToJs,$tempArray) ; 
    }
    
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawBestSellingItemChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
           
    $html = generateBarChart($arrParam);
    
    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html;  
}

function generatePanelTopCustomerGraph($panelName,$startPeriod, $endPeriod,$warehousekey){ 
    
    global $class;   
    global $customer;
    global $amountIn;
    global $templateGraphPanel;
	
    $arrTitle = array();
    array_push($arrTitle, $class->lang['customer']);
    array_push($arrTitle, $class->lang['amount']); 
    
    switch (PLAN_TYPE['categorykey']){
        case COMPANY_TYPE['retail'] :     
        case COMPANY_TYPE['jewelry'] :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
      case COMPANY_TYPE['trucking'] :     global $truckingServiceOrderInvoice;
                    $obj = $truckingServiceOrderInvoice; 
                    break;
       case COMPANY_TYPE['workshop'] :     global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
       case COMPANY_TYPE['logistics'] :     global $disposalSalesInvoice;
                    $obj = $disposalSalesInvoice;
                    break;  
    }
      
    $title = $obj->lang['topCustomers'];
      
    $rs = $obj->getBestSalesAmountByGroup($customer->tableName.'.pkey', $startPeriod, $endPeriod,5, $warehousekey);  
    
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
     
    for ($i=0;$i<count($rs);$i++){  
        $rs[$i]['amount'] = ($rs[$i]['amount'] > 0) ? $rs[$i]['amount']  / $amountIn : 0 ;
        
        $tempArray = array();
        array_push($tempArray, htmlspecialchars_decode($rs[$i]['customername']));
        array_push($tempArray, round($rs[$i]['amount']));
        array_push($arrayToJs,$tempArray) ; 
    }
        
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawTopCustomerChart';
    $arrParam['panelName'] = $panelName;
    $arrParam['title'] = $title ;
     
    $html = generateBarChart($arrParam);
      
    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html; 
     
    
}

function generatePanelTruckingCostRevenueGraph($panelName,$startPeriod, $endPeriod, $warehousekey = ''){

    global $class;   
    global $truckingServiceOrder; 
    global $amountIn;
    global $templateGraphPanel;
    
    $arrTitle = array();
    array_push($arrTitle, $class->lang['cost']);
    array_push($arrTitle, $class->lang['revenue']); 
    
    switch (PLAN_TYPE['categorykey']){
        case COMPANY_TYPE['retail'] :    
        case COMPANY_TYPE['jewelry'] :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case COMPANY_TYPE['trucking'] :     global $truckingServiceOrder;
                    $obj = $truckingServiceOrder; 
                    break;
        case COMPANY_TYPE['workshop'] :     global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
      
    $title =  ''; //$obj->lang['cost'];
      
    $rs = $obj->getTruckingCostRevenueAmount($startPeriod, $endPeriod, $warehousekey);  
    
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
     
    //for ($i=0;$i<count($rs);$i++){  
        //$rs[$i]['amount'] = ($rs[$i]['amount'] > 0) ? $rs[$i]['amount']  / $amountIn : 0 ;
        
        $rs[0]['revenueamount'] = ($rs[0]['revenueamount']  > 0) ? $rs[0]['revenueamount']   / $amountIn : 0 ;
        $tempArray = array();
        array_push($tempArray,$class->lang['revenue']);
        array_push($tempArray,$rs[0]['revenueamount']);
        array_push($arrayToJs,$tempArray) ; 
    
        $rs[0]['costamount'] = ($rs[0]['costamount']  > 0) ? $rs[0]['costamount']   / $amountIn : 0 ;
        $tempArray = array();
        array_push($tempArray,$class->lang['cost']);
        array_push($tempArray,$rs[0]['costamount']);
        array_push($arrayToJs,$tempArray) ; 
        
    //} 
    
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawTruckingCostRevenueChart';
    $arrParam['panelName'] = $panelName;
	$arrParam['title'] = $obj->lang['revenue'] .' vs ' . $obj->lang['cost'];
    $arrParam['subtitle'] =   $obj->lang['basedOn'] . ' '. $obj->lang['jobOrder'] ;
     
    $html = generatePieChart($arrParam,array('pieHole'=>'0.4', 'legendPos' => 'bottom'));
  
    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html; 
    
}


function generatePanelTruckingCostMaintenanceRevenueGraph($panelName,$startPeriod, $endPeriod, $warehousekey = ''){

    global $class;   
    global $truckingServiceOrder; 
    global $amountIn;
    global $templateGraphPanel;
    global $carServiceMaintenance;
    
    $arrTitle = array();
    array_push($arrTitle, $class->lang['cost']);
    array_push($arrTitle, $class->lang['revenue']); 
    
    
    switch (PLAN_TYPE['categorykey']){
        case COMPANY_TYPE['retail'] :    
        case COMPANY_TYPE['jewelry'] :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case COMPANY_TYPE['trucking'] :     global $truckingServiceOrder;
                    $obj = $truckingServiceOrder; 
                    break;
        case COMPANY_TYPE['workshop'] :     global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    }
      
    $title =  ''; //$obj->lang['cost'];
      
    $rs = $obj->getTruckingCostRevenueAmount($startPeriod, $endPeriod, $warehousekey);  
    $rsMaintenance = $carServiceMaintenance->getServiceCostMaintenance($startPeriod, $endPeriod, $warehousekey);  
    
    $arrayToJs = array(); 
    array_push($arrayToJs,$arrTitle) ; 
     
    //for ($i=0;$i<count($rs);$i++){  
        //$rs[$i]['amount'] = ($rs[$i]['amount'] > 0) ? $rs[$i]['amount']  / $amountIn : 0 ;
        
        $rs[0]['revenueamount'] = ($rs[0]['revenueamount']  > 0) ? $rs[0]['revenueamount']   / $amountIn : 0 ;
        $tempArray = array();
        array_push($tempArray,$class->lang['revenue']);
        array_push($tempArray,$rs[0]['revenueamount']);
        array_push($arrayToJs,$tempArray) ; 
    
        $rsMaintenance[0]['total'] = ($rsMaintenance[0]['total']  > 0) ? $rsMaintenance[0]['total']   / $amountIn : 0 ;
        $tempArray = array();
        array_push($tempArray,$class->lang['carMaintenance']);
        array_push($tempArray,$rsMaintenance[0]['total']);
        array_push($arrayToJs,$tempArray) ; 
        
    //} 
    
    $arrParam = array();
    $arrParam['data'] = $arrayToJs;
    $arrParam['callbackName'] = 'drawTruckingCostMaintenanceRevenueChart';
    $arrParam['panelName'] = $panelName;
	$arrParam['title'] = $obj->lang['revenue'] .' vs ' . $obj->lang['carMaintenance'];
    $arrParam['subtitle'] =   $obj->lang['basedOn'] . ' '. $obj->lang['jobOrder'];
     
    $html = generatePieChart($arrParam,array('pieHole'=>'0.4', 'legendPos' => 'bottom'));
  
    $replacement = array(); 
    $replacement['title'] = '';   
    $replacement['content'] = '';   
    $replacement['footer'] = $startPeriod.' - '.$endPeriod;    
    return replaceContent($replacement, $templateGraphPanel).$html; 
    
}


function generatePanelPendingPurchaseOrder($panelName,$rowsLimit = 10, $warehousekey = ''){ 
$purchaseOrder = createObjAndAddToCol(new PurchaseOrder()); 
    
    global $security;
    global $templatePanel;
    
    $obj = $purchaseOrder;
    

    if(!$security->isAdminLogin($obj->securityObject,10)) return;
     
	$criteria = '';
	if (!empty($warehousekey))
			$criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
	 
    $rs =  $obj->searchData($obj->tableName.'.statuskey',1,true,$criteria,'order by '.$obj->tableName.'.trdate desc','limit 0,'.  $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" style="width:8em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" style="width:7.5em; text-align:center">'.$obj->formatDbDate($rs[$i]['trdate']).'</div>
        <div class="div-table-col-3">'.$rs[$i]['suppliername'].'</div>
        </div>
        ';  
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['unproccesedPurchaseOrder'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}

function generatePanelPendingGuaranteeLetter($panelName,$rowsLimit = 10){ 
    $medicalPurchaseOrder = createObjAndAddToCol(new MedicalPurchaseOrder()); 
    
    global $security;
    global $templatePanel;
    
    $obj = $medicalPurchaseOrder;
    
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
     
    $rs =  $obj->searchData($obj->tableName.'.statuskey',1,true,'','order by '.$obj->tableName.'.trdate desc','limit 0,'.  $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" style="width:8em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" style="width:7.5em; text-align:center">'.$obj->formatDbDate($rs[$i]['trdate']).'</div>
        <div class="div-table-col-3">'.$rs[$i]['suppliername'].'</div>
        </div>
        ';  
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['unproccesedGuaranteeLetter'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}
function generatePanelPendingNewRequest($panelName,$rowsLimit = 10){ 
    
    $medicalRequestClaim = createObjAndAddToCol(new MedicalRequestClaim()); 
    
    global $security;
    global $templatePanel;
    
    $obj = $medicalRequestClaim;
    
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
     
    $rs =  $obj->searchData($obj->tableName.'.statuskey',1,true,'','order by '.$obj->tableName.'.trdate desc','limit 0,'.  $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" style="width:8em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" style="width:7.5em; text-align:center">'.$obj->formatDbDate($rs[$i]['trdate']).'</div>
        <div class="div-table-col-3">'.$rs[$i]['suppliername'].'</div>
        </div>
        ';  
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['unproccesedNewRequest'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}
function generatePanelPendingInvoice($panelName,$rowsLimit = 10){ 
    
    $medicalSalesInvoice = createObjAndAddToCol(new MedicalSalesInvoice()); 
    
    global $security;
    global $templatePanel;
    
    $obj = $medicalSalesInvoice;
    
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
     
    $rs =  $obj->searchData($obj->tableName.'.statuskey',1,true,'','order by '.$obj->tableName.'.trdate desc','limit 0,'.  $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" style="width:8em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" style="width:7.5em; text-align:center">'.$obj->formatDbDate($rs[$i]['trdate']).'</div>
        <div class="div-table-col-3">'.$rs[$i]['suppliername'].'</div>
        </div>
        ';  
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['unproccesedInvoice'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}
function generatePanelPendingSalesOrderQuotation($panelName,$rowsLimit = 10, $warehousekey = ''){ 
    
    $medicalSalesOrderQuotation = createObjAndAddToCol(new MedicalSalesOrderQuotation()); 
    
    global $security;
    global $templatePanel;
    
    $obj = $medicalSalesOrderQuotation;
    
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
     
	
	$criteria = '';
	if (!empty($warehousekey))
			$criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
	 
    $rs =  $obj->searchData($obj->tableName.'.statuskey',1,true,$criteria,'order by '.$obj->tableName.'.trdate desc','limit 0,'.  $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" style="width:8em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" style="width:7.5em; text-align:center">'.$obj->formatDbDate($rs[$i]['trdate']).'</div>
        <div class="div-table-col-3">'.$rs[$i]['suppliername'].'</div>
        </div>
        ';  
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['unproccesedSalesOrderQuotation'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}
function generatePanelPendingReminder($panelName,$rowsLimit = 10){ 
    
    $reminder = createObjAndAddToCol(new Reminder()); 
    
    global $security;
    global $templatePanel;
    
    $obj = $reminder;
    
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
     
    $rs =  $obj->searchData($obj->tableName.'.statuskey',1,true,'','order by '.$obj->tableName.'.trdate desc','limit 0,'.  $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" style="width:8em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" style="width:7.5em; text-align:center">'.$obj->formatDbDate($rs[$i]['trdate']).'</div>
        <div class="div-table-col-3">'.$rs[$i]['suppliername'].'</div>
        </div>
        ';  
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['unproccesedReminder'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}

function generatePanelPendingSalesOrder($panelName,$rowsLimit = 10, $warehousekey = ''){ 
    
    global $security;
    global $templatePanel; 
   
    switch (PLAN_TYPE['categorykey']){
        case COMPANY_TYPE['retail'] :    
        case COMPANY_TYPE['jewelry'] :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case COMPANY_TYPE['trucking'] :     global $truckingServiceOrder;
                    $obj = $truckingServiceOrder;
                    break;  
        case COMPANY_TYPE['workshop'] :     global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
        case COMPANY_TYPE['tpamedical'] :    global $medicalJobOrder;
                    $obj = $medicalJobOrder;
                    break;  
    }
    
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
    
	$criteria = '';
	if (!empty($warehousekey))
			$criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
	
    $rs =  $obj->searchData($obj->tableName.'.statuskey',1,true,$criteria,'order by '.$obj->tableName.'.trdate desc','limit 0,'.  $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" style="width:8em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" style="width:7.5em; text-align:center">'.$obj->formatDbDate($rs[$i]['trdate']).'</div>
        <div class="div-table-col-3"  >'.$rs[$i]['customername'].'</div>
        <div class="div-table-col-3" style="width:7.5em; text-align:right" >'.$obj->formatNumber($rs[$i]['beforetaxtotal']).'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['unproccesedSalesOrder'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);  
}

function generatePanelMinStock($panelName,$rowsLimit = 10){ 
    global $item;
    global $security;
    global $templatePanel;
    
    $obj = $item;
    
    if(!$security->isAdminLogin($obj->securityObject,10))   return;
   
    $rs =  $obj->searchData($obj->tableName.'.statuskey','1',true,' and minstockqty > 0 ','order by name asc','limit 0,'.  $rowsLimit  ,' having qtyonhand < minstockqty'); // .$obj->oDbCon->paramString($minStock) );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){  
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" >'.$rs[$i]['name'].'</div>
        <div class="div-table-col-3" style="width: 4em; text-align:right">'.$obj->formatNumber($rs[$i]['qtyonhand']).'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
      
    $replacement = array();
    $replacement['title'] = $obj->lang['lowStock'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);
     
} 

function generatePanelMaxStock($panelName,$rowsLimit = 10){ 
    global $item;
    global $security;
    global $templatePanel; 
    
    $obj = $item;
      
    $rs =  $obj->searchData($obj->tableName.'.statuskey','1',true,' and maxstockqty > 0 ','order by qtyonhand desc','limit 0,'.  $rowsLimit  ,' having qtyonhand > maxstockqty '); // .$obj->oDbCon->paramString($maxStock) );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
          
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" >'.$rs[$i]['name'].'</div>
        <div class="div-table-col-3" style="width: 4em; text-align:right">'.$obj->formatNumber($rs[$i]['qtyonhand']).'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
      
    $replacement = array();
    $replacement['title'] = $obj->lang['overStock'];  
    $replacement['content'] = $content;   

    return replaceContent($replacement, $templatePanel);
     
}  

function generatePanelEmptyStock($panelName,$rowsLimit = 10){ 
    global $item;
    global $security;
    global $templatePanel;  
    
    $obj = $item;
     
    $rs = $obj->getLatestEmptyStock($rowsLimit);
    
    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){  
        $content .='     
        <div class="div-table-row">
            <div class="div-table-col-3">'.$rs[$i]['name'].'</div> 
            <div class="div-table-col-3" style="width: 10em; text-align:center">'.$obj->formatDBDate($rs[$i]['createdon'],'d / m / Y H:i').'</div> 
        </div>
        '; 
    }
        
    $content .= '</div>';
      
    $replacement = array();
    $replacement['title'] = $obj->lang['emptyStock'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);
     
} 
 
function generatePanelNotMovingStock($panelName,$rowsLimit = 10){
    global $item;
    global $templatePanel;
    
    $monthInterval = 3;
    
    $rs = $item->getNotMovingStock($monthInterval,$rowsLimit);
    
    $content = '<div class="div-table table-with-border" style="width:100%">';
    for ($i=0;$i<count($rs);$i++){  
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3">'.$rs[$i]['name'].'</div>
        <div class="div-table-col-3" style="width:2em; text-align:right;">'.$item->formatNumber($rs[$i]['qtyonhand']).'</div> 
        <div class="div-table-col-3" style="width:4em">'. $rs[$i]['baseunitname'] .'</div> 
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] = $item->lang['notMovingStock'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);
}

function generatePanelOverdueAP($panelName,$rowsLimit = 10, $warehousekey = ''){  
    $ap = createObjAndAddToCol(new AP());  
    global $security; 
    global $templatePanel; 
    
    $obj = $ap;
            
	$criteria = '';
	if (!empty($warehousekey))
			$criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
	
    $rs =  $obj->searchData('','',true,' and (' . $obj->tableName.'.statuskey in (1,2) ) and duedate <  date(now()) '.$criteria,'order by duedate asc' , 'limit 0,'.  $rowsLimit);

    $content = '<div class="div-table table-with-border" style="width:100%">';
    
    for ($i=0;$i<count($rs);$i++){ 
         
        $overrideClass = ($rs[$i]['statuskey'] == 2) ? 'text-princeton-orange' : '';
        
        //<div class="div-table-col-3" style="width:8em; text-align:center">'.$obj->formatDbDate($rs[$i]['duedate']).'</div>
        $content .='     
        <div class="div-table-row '.$overrideClass.'">
        <div class="div-table-col-3" style="width:7em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" >'.$rs[$i]['suppliername'].'</div>
        <div class="div-table-col-3" style="width:5em;text-align:right">'.$obj->formatNumber($rs[$i]['outstanding']).'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['overdueAccountsPayable'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}

function generatePanelOverdueAR($panelName,$rowsLimit = 10, $warehousekey = ''){ 
    
    $ar = createObjAndAddToCol(new AR()); 
    
    global $security; 
    global $templatePanel;  
    
    $obj = $ar;
	
	$criteria = '';
	if (!empty($warehousekey))
			$criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
	
    $rs =  $obj->searchData('','',true,' and (' . $obj->tableName.'.statuskey = 1 or ' . $obj->tableName.'.statuskey = 2) and duedate <  date(now()) ' . $criteria,'order by duedate asc', 'limit 0,' . $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        
        $overrideClass = ($rs[$i]['statuskey'] == 2) ? 'text-princeton-orange' : '';
        
        //<div class="div-table-col-3" style="width:8em; text-align:center">'.$obj->formatDbDate($rs[$i]['duedate']).'</div>
        $content .='     
        <div class="div-table-row '.$overrideClass.'">
        <div class="div-table-col-3" style="width:7em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3" >'.$rs[$i]['customername'].'</div>
        <div class="div-table-col-3" style="width:5em; text-align:right">'.$obj->formatNumber($rs[$i]['outstanding']).'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['overdueAccountsReceivable'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}  


function generatePanelOutstandingAR($panelName,$rowsLimit = 10, $warehousekey = ''){ 
    
    $ar = createObjAndAddToCol(new AR()); 
    
    global $security; 
    global $templatePanel;  
    
    $obj = $ar;
	$arrMTI = array('mti.wintera.co.id','tcl.wintera.co.id');

	$criteria = ' and (' . $obj->tableName.'.statuskey = 1 or ' . $obj->tableName.'.statuskey = 2) ';
	if (!empty($warehousekey))
			$criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
	
    $rs = $obj->generateARReport($criteria, 'order by totaloutstanding desc', '');

    $content = '<div class="div-table table-with-border" style="width:100%">';

    //$tax23value = 0;
    //if(in_array(DOMAIN_NAME,$arrMTI)) 
    //    $tax23value = 2;
    
    $total = 0;
    for ($i=0;$i<count($rs);$i++){ 
        
        if(in_array(DOMAIN_NAME,$arrMTI)) { 
            $rs[$i]['totaloutstanding'] -= $rs[$i]['totaltax23value']; 
        }
        
        //$totalTax23 = $rs[$i]['totaloutstanding'] * $tax23value / 100 ;
        $outstanding = $rs[$i]['totaloutstanding'];
        
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" >'.$rs[$i]['customername'].'</div>
        <div class="div-table-col-3" style="width:5em; text-align:right">'.$obj->formatNumber($outstanding).'</div>
        </div>
        '; 
        
        $total += $outstanding;
    }
    
    $content .='     
        <div class="div-table-row total-row">
            <div class="div-table-col-3">'.strtoupper($obj->lang['total']).'</div> 
            <div class="div-table-col-3" style="width:5em; text-align:right">'.$obj->formatNumber($total).'</div>
        </div>
    '; 
        
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['AROutstanding'];  
    $replacement['headerSummary'] = '<div class="header-summary">'.$obj->formatNumber($total).'</div>';
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}
//
//function generatePanelUnCollectedAR($panelName, $warehousekey = ''){ 
//    
//    $ar = createObjAndAddToCol(new AR()); 
//    
//    global $security; 
//    global $templatePanel;  
//    
//    $obj = $ar;
//	
//	$criteria = '';
//	if (!empty($warehousekey))
//			$criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
//	
//    $rs =  $obj->searchData('','',true,' and '. $obj->tableName.'.statuskey in (1,2) and '.$obj->tableName.'.categorykey = 1 '. $criteria,'order by trdate asc' );
//
//    $content = '<div class="div-table table-with-border" style="width:100%">';
//    $total = 0;
//    for ($i=0;$i<count($rs);$i++){ 
//        
//        $overrideClass = ($rs[$i]['statuskey'] == 2) ? 'text-princeton-orange' : '';
//        
//        //<div class="div-table-col-3" style="width:8em; text-align:center">'.$obj->formatDbDate($rs[$i]['duedate']).'</div>
//        $content .='     
//        <div class="div-table-row '.$overrideClass.'">
//        <div class="div-table-col-3" style="width:7em">'.$rs[$i]['code'].'</div>
//        <div class="div-table-col-3" >'.$rs[$i]['customername'].'</div>
//        <div class="div-table-col-3" style="width:5em; text-align:right">'.$obj->formatNumber($rs[$i]['outstanding']).'</div>
//        </div>
//        '; 
//        $total += $rs[$i]['outstanding'];
//    }
//           
//    $content .='     
//        <div class="div-table-row total-row">
//            <div class="div-table-col-3"></div> 
//            <div class="div-table-col-3">'.strtoupper($obj->lang['total']).'</div> 
//            <div class="div-table-col-3" style="width:5em; text-align:right">'.$obj->formatNumber($total).'</div>
//        </div>
//    '; 
//         
//    $content .= '</div>';
//    
//    $replacement = array();
//    $replacement['title'] = ucwords($obj->lang['unCollectedAR']);  
//    $replacement['headerSummary'] = '<div class="header-summary">'.$obj->formatNumber($total).'</div>';
//    $replacement['content'] = $content;  
//
//     return replaceContent($replacement, $templatePanel);
//}  


function generatePanelMarketplace($panelName){
    
    $marketplace = createObjAndAddToCol(new Marketplace()); 
    
    global $templatePanel; 
    
    $obj = $marketplace;
    $marketplaceObjs = $obj->getMarketplaceObj();
 
    $content = '<div class="div-table table-with-border marketplace-status" style="width: 100%">';
    
    foreach($marketplaceObjs as $marketplaceRow){  
        $content .='     
        <div class="div-table-row marketplace-'.$marketplaceRow['key'].'">
            <div class="div-table-col-3" style="vertical-align:middle">'.$marketplaceRow['name'].'</div>
            <div class="div-table-col-3" style="width:3em; text-align:center">
                 <div class="status-icon disconnect-icon" style="float:left"><i class="fas fa-times-circle"></i></div>
                 <div class="status-icon connect-icon" style="float:left; display:none"><i class="fas fa-check-circle"></i></div>
            </div> 
        </div>
        '; 
    }
        
    $content .= '</div>'; 
            
    // test marketplace connection
    $script = '<script> 
                function testMarketplaceConnection(marketplacekey){
                  var marketplaceRow = $(".marketplace-"+marketplacekey);

                   $.ajax({
                    type: "GET",
                    url: "ajax-marketplace.php",
                    data: "action=testConnection&marketplacekey=" + marketplacekey, 
                    beforeSend : function (){
                            marketplaceRow.removeClass("active").addClass("inactive");
                            marketplaceRow.find(".disconnect-icon").show();
                            marketplaceRow.find(".connect-icon").hide();
                    },
                    success: function(data){      
                        if (!data) return; 
                        
                        var data = JSON.parse(data);  
                        if (data.status == true){ 
                            marketplaceRow.removeClass("inactive").addClass("active"); 
                            marketplaceRow.find(".disconnect-icon").hide();
                            marketplaceRow.find(".connect-icon").show();
                        }else{
                            marketplaceRow.attr("reladdr",data.authURL);
                        } 
                    }
                  }); 
                }    
    ';
    
    foreach($marketplaceObjs as $marketplaceRow) 
        $script .= 'testMarketplaceConnection('.$marketplaceRow['key'].');'; 
    
    $script .= 'var domObj = '.getPanelDOM($panelName,true).'; ';
    $script .= 'domObj.find(".marketplace-status .inactive").click(function(){   
                    var reladdr = $(this).attr("reladdr");
                    if(!reladdr) return; 
                    var win=window.open(reladdr, "_blank");
                    win.focus();  
                });';
    
    $script .= '</script>';
    $script = $obj->minimizeJavascriptSimple($script);
        
    $content .= $script;
     
    $replacement = array();
    $replacement['title'] =$obj->lang['marketplace'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);
}

function generatePanelUnderMarginSalesOrder($panelName, $rowsLimit = 10, $warehousekey=''){  
    global $security; 
    global $templatePanel;  
    global $item;
    
    switch (PLAN_TYPE['categorykey']){
        case COMPANY_TYPE['retail'] :     
        case COMPANY_TYPE['jewelry'] :    global $salesOrder;
                    $obj = $salesOrder; 
                    break;
        case COMPANY_TYPE['workshop'] :    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
    } 
      
	$criteria = '';
	if (!empty($warehousekey))
			$criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
	
    $rs =  $obj->searchData('','',true,' and '.$obj->tableName.'.statuskey in (2,3) and profit < 0 '.$criteria,' order by pkey desc','limit 0,'.  $rowsLimit );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        //       <div class="div-table-col-5" style="width:30%; text-align:center">'.$obj->formatDbDate($rs[$i]['trdate']).'</div>
        $content .='     
        <div class="div-table-row">
        <div class="div-table-col-3" style="width:10em">'.$rs[$i]['code'].'</div>
        <div class="div-table-col-3">'.$rs[$i]['customername'].'</div>
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['underMarginSalesOrder'];  
    $replacement['content'] = $content;  

     return replaceContent($replacement, $templatePanel);
}
 
function replaceContent($replacement, $templatePanel){
    $patterns = array();
    $patterns['title'] = '/({{TITLE}})/'; 
    $patterns['headerSummary'] = '/({{HEADER_SUMMARY}})/'; 
    $patterns['content'] = '/({{CONTENT}})/';
    $patterns['footer'] = '/({{FOOTER}})/';
    $patterns['iconSetting'] = '/({{ICON_SETTING}})/';
    
	// perlu reorder ulang, utk memastikan gk ketuker
	$arrReplacement = array();
	
	foreach($patterns as $key=>$row) 
		$arrReplacement[$key] = (isset($replacement[$key])) ? $replacement[$key] :  '';
    
    return preg_replace($patterns, $arrReplacement, $templatePanel);
}
 
function generateBarChart ($arrParam){ 
    global $class;
     
    $arrData = $arrParam['data']; 
    $callbackName = $arrParam['callbackName'];  
    $panelName = $arrParam['panelName'].' .content'; 
    $title = $arrParam['title'];
    
    // insert color style
    $arrData[0][2] = array('role' => 'style');
    for($i=1;$i<count($arrData);$i++){
        $arrData[$i][2] = isset($class->graphColorSet[$i]) ? $class->graphColorSet[$i] : '';
    } 
     
    $content ='
     <script>  
            setTimeout(function(){google.load(\'visualization\', \'1\', {\'callback\':\''.$callbackName.'\', \'packages\':[\'corechart\']})} );   
            
            // tetep harus ad, agar keresize ketika di tab Dashboard
            $(window).resize(function(){   if (getSelectedTabIndex() == 0) '.$callbackName.'(); });   
            dashboardRedrawFunc.push('.$callbackName.');

            function '.$callbackName.'() { 
            
                var data = google.visualization.arrayToDataTable('.json_encode($arrData).');
   
                var options = {
                  title: \''.$title.'\',  
                  legend: {position: \'none\'}, 
                  chartArea: {\'width\': \'100%\', \'left\' : \'110\', \'right\' : \'30\'},
                  titleTextStyle : { color: \'#333\', fontSize: \'16\', fontName: \'Palanquin\' } ,
                  animation: {
                        duration: 1500,
                        startup: true 
                    }
                };

                var chart = new google.visualization.BarChart('.getPanelDOM($panelName).');
                  
                chart.draw(data,options);
                 
                setTimeout(function(){
                     var domObj = '.getPanelDOM($panelName,true).';    
                     domObj.find(\'text[text-anchor=end]\').each(function () {   
                       $(this).attr("text-anchor","front"); 
                       $(this).attr("x",5); 
                     }); 
                }, 2000);
 
              }
        </script>';
    
    return $class->minimizeJavascriptSimple($content);
}


function generatePieChart ($arrParam,$arrOptions){ 
    global $class;
     
    $arrData = $arrParam['data']; 
    $callbackName = $arrParam['callbackName'];  
    $panelName = $arrParam['panelName'] .' .content'; 
    $title = $arrParam['title'];
    $subtitle = (isset($arrParam['subtitle'])) ?   '\n'.$arrParam['subtitle'] :   '\n'.$arrData[1][0].' - '. $arrData[count($arrData)-1][0];
	
    $pieHole = isset($arrOptions['pieHole']) ? $arrOptions['pieHole'] : 0;
    $legendPos = isset($arrOptions['legendPos']) ? $arrOptions['legendPos'] : 'right';
    
    $content ='
     <script>  
            setTimeout(function(){google.load(\'visualization\', \'1\', {\'callback\':\''.$callbackName.'\', \'packages\':[\'corechart\']})} );  

            // tetep harus ad, agar keresize ketika di tab Dashboard
            $(window).resize(function(){   if (getSelectedTabIndex() == 0) '.$callbackName.'(); });   
            dashboardRedrawFunc.push('.$callbackName.');

            function '.$callbackName.'() { 
			 
                var data = google.visualization.arrayToDataTable('.json_encode($arrData,JSON_NUMERIC_CHECK).');

                var options = {
                  title: \''.$title.$subtitle.'\',
                  chartArea: {\'width\': \'100%\'},
                  titleTextStyle : { color: \'#333\', fontSize: \'16\', fontName: \'Palanquin\' } ,
                  slices : '.$class->graphPieColorSet.',
                  pieHole: '.$pieHole.',
                  legend: \''.$legendPos.'\',
                  animation: {
                        duration: 1500,
                        startup: true 
                  }
                };

                var chart = new google.visualization.PieChart('.getPanelDOM($panelName).');

                chart.draw(data,options);
              }
        </script>';
    
    return $class->minimizeJavascriptSimple($content);
}


function generateLineChart($arrParam,$opt=array()){  
    global $class;
    
    $arrData = $arrParam['data']; 
    $callbackName = $arrParam['callbackName'];  
    $panelName = $arrParam['panelName'] .' .content'; 
    $title = $arrParam['title'];
    $subtitle = (isset($arrParam['subtitle'])) ?   '\n'.$arrParam['subtitle'] :   '\n'.$arrData[1][0].' - '. $arrData[count($arrData)-1][0];
	
	$colorSet = (isset($opt['colorSet']) && !empty($opt['colorSet'])) ? $opt['colorSet'] : $class->graphLineColorSet;
	
    $content = '
        <script>  
            setTimeout(function(){google.load(\'visualization\', \'1\', {\'callback\':\''.$callbackName.'\', \'packages\':[\'corechart\']})} );  
     
            // tetep harus ad, agar keresize ketika di tab Dashboard
            $(window).resize(function(){   if (getSelectedTabIndex() == 0) '.$callbackName.'(); });   
            dashboardRedrawFunc.push('.$callbackName.');

            function '.$arrParam['callbackName'].'() {   
 
            var data = google.visualization.arrayToDataTable($.parseJSON(\''.json_encode($arrData).'\'));';

 
    $content .= chr(13). '  period = "'.$arrData[1][0].' - '. $arrData[count($arrData)-1][0] .'";' . chr(13); 
             
    $content .= '    
                  // Set chart options
                   
                  var options = {
                                    title: \''.$title.$subtitle.'\',
                                    chartArea: {\'width\': \'90%\', \'left\': \'80\' },
                                    curveType: \'none\',
                                    pointSize: 5,
                                    vAxis: { gridlines: { count: 8 }, textStyle: {   color:\'#666\', fontSize: 12 } }, 
                                    tooltip : {  textStyle: {   color:\'#666\', fontSize: 12 } },
                                    hAxis: { textStyle: {   color:\'#666\', fontSize: 12 } }, 
                                    legend: {\'position\': \'bottom\',  scrollArrows: { inactiveColor: \'#666\', activeColor:\'#666\' }, pagingTextStyle: {  color:\'#666\' }  },
                                    titleTextStyle : { color: \'#333\', fontName: \'Palanquin\' } ,
                                    series : '.$colorSet.',
                                    animation: {
                                        duration: 1500,
                                        startup: true  
                                    }
                                };

                  // Instantiate and draw our chart, passing in some options.
                  var chart = new google.visualization.AreaChart('.getPanelDOM($panelName).'); 
                  
                  chart.draw(data, options);
            }

        </script>                            
    ';  
    
    return $class->minimizeJavascriptSimple($content);
}


function generateTransactionStatus($title,$obj,$startPeriod, $endPeriod, $bgDiv='',$warehousekey = ''){  
    
    global $truckingServiceOrder;
    global $truckingServiceWorkOrder;
    
    $startPeriod = date("Y-m-01", strtotime($startPeriod));
    $endPeriod = date("Y-m-t 23:59", strtotime($endPeriod));
        
    $rsStatus = $obj->getAllStatus(); 
    $statusCriteria = '';
     
    $statusCriteria = ' and '. $obj->tableName.'.trdate between '.$obj->oDbCon->paramString($startPeriod).' and ' . $obj->oDbCon->paramString($endPeriod);
    
	if (!empty($warehousekey))
		$statusCriteria .=  ' and '. $obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
		
	$statusCriteria .=  $obj->getWarehouseCriteria();
    
    $arrGroup = array();
    array_push($arrGroup, array('fieldName' => $obj->tableName.'.statuskey', 'groupkey' => 'statuskey' ));
    $rsCountedTotalRows = $obj->countTotalRows($statusCriteria,$arrGroup); 
    
    $total = $obj->getCountedTotalRows($rsCountedTotalRows);  //array_sum($arrTotalRows);
    
    // ==== hitung total waktu 
    // buat JO aj dulu. SPK gk terlalu guna karena tergantung tujuan 
    if(isset($truckingServiceOrder)){ 
    
        //$totalPerStatus = ($obj->tableName == $truckingServiceOrder->tableName) ? $obj->calculateDateDiffPerStatus($statusCriteria)['totalPerStatus'] : '';
        
        // nanti dibuat bisa pilih mau avg day atau nilai transaksi
        $totalPerStatus = ($obj->tableName == $truckingServiceOrder->tableName) ? $obj->calculateTotalSalesPerStatus($statusCriteria . ' and '.$obj->tableName.'.statuskey in (4,5)') : [];
      
    }
    
    $content = '<div class="auto-height transaction-status-panel">';
    $content .= $bgDiv;
                  
    $content .= '<div class="title">';
    $content .= '<div class="flex">';
    $content .= '<div class="consume">'.strtoupper($title).'</div>'; 
    $content .= '<div style="text-align:right; font-weight:normal">'.$obj->formatNumber($total,0).'</div>';
    $content .= '</div>'; 
    $content .= '</div>'; 
    
    $content .= '<div class="div-table">';

    for($i=0;$i<count($rsStatus);$i++){
        
        $statuskey = $rsStatus[$i]['pkey']; 
        $avgDaysLabel = (isset($totalPerStatus[$statuskey]['label'])) ? 'IDR '.$obj->formatNumber($totalPerStatus[$statuskey]['label']) : '';
         
        $totalData = $obj->getCountedTotalRows($rsCountedTotalRows,'statuskey', $rsStatus[$i]['pkey']);  //(isset($arrTotalRows[$rsStatus[$i]['pkey']])) ? $arrTotalRows[$rsStatus[$i]['pkey']] : 0;
        $totalDataLabel = $obj->formatNumber($totalData,0);
            
        // khusus SPK 
        if(isset($truckingServiceOrder)){ 
           if($obj->tableName == $truckingServiceWorkOrder->tableName){
                if($rsStatus[$i]['pkey'] == 2){ 
                    $qtyOutsource = $obj->getTotalOutsource($rsStatus[$i]['pkey']); 
                    $totalDataLabel = $obj->formatNumber (($totalData - $qtyOutsource),0) .' / ' . $obj->formatNumber($qtyOutsource,0); 
                    //$rsStatus[$i]['status'] .=  ' ('.$obj->lang['inhouse'].'/'.$obj->lang['outsource'].')';
                }
            }
        }
        
        $content .= '<div class="div-table-row">';
        $content .= '<div class="div-table-col-3">'.$rsStatus[$i]['status'].' <span class="tag">'.$avgDaysLabel.'</span></div>'; 
        $content .= '<div class="div-table-col-3" style="text-align:right">'.$totalDataLabel.'</div>';
        $content .= '</div>';
    } 
 
    $content .= '</div>
     <div style="clear:both; height: 3em"></div>
     <div class="footnote">
        <div>'.$obj->lang['cancellationRate'].': '. $obj->formatNumber($total != 0 ? ($totalData / $total) * 100 : 0, 2).' %</div>
        <div>'.$obj->formatDBDate($startPeriod,'M Y').' - '.$obj->formatDBDate($endPeriod,'M Y').'</div>
     </div> 
    </div>'; 
      
    return $content;
}

function generatePanelVehicleOverdue($panelName, $warehousekey =''){ 
    
    $car = createObjAndAddToCol(new Car()); 
    
    global $security;
    global $templatePanel;    
	global $rsWidgetProperties;
    
    $obj = $car;
    
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    // cek properties 
	$rsSettings = $rsWidgetProperties[$panelName];
    $rsSettings = array_column($rsSettings,null,'properties');
      
   // $timelimit = (isset($rsSettings['overduedays']['value']) && !empty($rsSettings['overduedays']['value'])) ? $rsSettings['overduedays']['value'] : $rsSettings['overduedays']['defaultvalue']  ;
       
    $expiredType = array();
    
    $licenseExpired = getWidgetValue($rsSettings,'licenseexpired'); 
    if($licenseExpired) array_push($expiredType,array('duedays' => $licenseExpired,  'label' => 'STNK','dbfield' => 'licenseexpirydate'  ));
    
    $taxExpired = getWidgetValue($rsSettings,'taxexpired'); 
    if($licenseExpired) array_push($expiredType,array('duedays' => $taxExpired,   'label' => 'BPKB' ,'dbfield' => 'licensetaxexpirydate'  )); 
    
    $kirExpired = getWidgetValue($rsSettings,'kirexpired'); 
    if($licenseExpired) array_push($expiredType,array('duedays' => $kirExpired, 'label' => 'KIR','dbfield' => 'kirexpirydate'  ));  
      
    $rs = $obj->getExpiryLicense($expiredType,$warehousekey);

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        
        $class = ($rs[$i]['duedate'] < 0) ? 'text-red-cardinal' : ''; 
        
        $content .='     
        <div class="div-table-row '.$class.'"> 
        <div class="div-table-col-3" style="width:12em">'.$rs[$i]['policenumber'].'</div>
        <div class="div-table-col-3" style="width:5em">'.$rs[$i]['typename'].'</div>
        <div class="div-table-col-3" style="text-align:center">'.$obj->formatDbDate($rs[$i]['expireddate']).'</div> 
        </div>
        '; 
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['vehicleLicenseOverdue'];  
    $replacement['content'] = $content;  
    $replacement['iconSetting'] = '<i class="btn-widget-setting fal fa-cog"></i>';// (!empty($rsSettings)) ? '<i class="fal fa-cog"></i>' : '';
	  
    return replaceContent($replacement, $templatePanel);  
}


function generatePanelDriverLicenseExpired($panelName, $warehousekey =''){ 
    
    $employee = createObjAndAddToCol(new Employee()); 
    
    global $security;
    global $employee;
    global $templatePanel;    
	global $rsWidgetProperties;
    
    $obj = $employee;
    
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    // cek properties 
	$rsSettings = $rsWidgetProperties[$panelName];
    $rsSettings = array_column($rsSettings,null,'properties');
       
    $expiredType = array();
    
    $licenseExpired = getWidgetValue($rsSettings,'driverlicenseexpired'); 
    if($licenseExpired) array_push($expiredType,array('duedays' => $licenseExpired,  'label' => 'SIM', 'dbfield' => 'drivinglicenseexpdate' ));
     
    $rs = $obj->getExpiryLicense($expiredType, $warehousekey);

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
        
        $class = ($rs[$i]['duedate'] < 0) ? 'text-red-cardinal' : ''; 
        
        $content .='     
        <div class="div-table-row '.$class.'"> 
        <div class="div-table-col-3" style="width:12em">'.$rs[$i]['name'].'</div> 
        <div class="div-table-col-3" style="text-align:center">'.$obj->formatDbDate($rs[$i]['expireddate']).'</div> 
        </div>
        ';
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['driverLicenseExpired'];  
    $replacement['content'] = $content;  
    $replacement['iconSetting'] = '<i class="btn-widget-setting fal fa-cog"></i>';
	  
    return replaceContent($replacement, $templatePanel);  
}
    
function generatePanelDailyTransactionSummary($panelName, $limitdays = 10, $warehousekey){
     
    global $security;
    global $templatePanel; 
    global $salesOrder;
    
    $obj = $salesOrder;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs =  $obj->getDailyTransactionSummary($limitdays,'',$warehousekey);

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
          
        $icon = '';

        $currSales = $rs[$i]['totalsales'];
        $prevSales =  $rs[$i+1]['totalsales'] ?? 0;

        if($i <> count($rs) -1) { 
            if($currSales != $prevSales ){
                $icon = ($currSales < $prevSales) ? '<i class="fas fa-caret-down text-red-cardinal"></i>' : '<i class="fas fa-caret-up  text-green-avocado"></i>';
            }
        }
        
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" style="width:8.5em; text-align:center">'.$obj->formatDBDate($rs[$i]['trdate']).'</div> 
        <div class="div-table-col-3" style="width:8em;text-align:right; ">'.$rs[$i]['totalsoldinunit'].'</div>  
        <div class="div-table-col-3" style="text-align:right">'.$obj->formatNumber($currSales).'</div> 
        <div class="div-table-col-3" style="width:1em; text-align:center">'.$icon.'</div> 
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['dailyTransactionSummary'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);  
}


function generatePanelDailyMarketplaceTransactionSummary($panelName, $limitdays = 10,$warehousekey){
     
    global $security;
    global $templatePanel; 
    global $salesOrder;
    global $customer;
    
    $obj = $salesOrder;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   

    // kalo ad marketplace
    $rsMarketplaceCustomer = $customer->searchData($customer->tableName.'.ismarketplace',1,true); // gk peduli aktif atau gk
    $rsMarketplaceCustomer = array_column($rsMarketplaceCustomer,'pkey');

    $rs =  $obj->getDailyTransactionSummary($limitdays, ' and '.$obj->tableName.'.customerkey in ('.$obj->oDbCon->paramString($rsMarketplaceCustomer,',').')', $warehousekey );

    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
          
        $icon = '';

        $currSales = $rs[$i]['totalsales'];
        $prevSales =  $rs[$i+1]['totalsales'];

        if($i <> count($rs) -1) { 
            if($currSales != $prevSales ){
                $icon = ($currSales < $prevSales) ? '<i class="fas fa-caret-down text-red-cardinal"></i>' : '<i class="fas fa-caret-up  text-green-avocado"></i>';
            }
        }
        
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" style="width:8.5em; text-align:center">'.$obj->formatDBDate($rs[$i]['trdate']).'</div> 
        <div class="div-table-col-3" style="width:8em;text-align:right; ">'.$rs[$i]['totalsoldinunit'].'</div>  
        <div class="div-table-col-3" style="text-align:right">'.$obj->formatNumber($currSales).'</div> 
        <div class="div-table-col-3" style="width:1em; text-align:center">'.$icon.'</div> 
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['dailyMarketplaceTransactionSummary'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel);  
}


function generatePanelCashAdvance ($panelName,$warehousekey){
    $cashAdvance = createObjAndAddToCol(new CashAdvance());
    
    global $security;
    global $templatePanel; 
    
    $obj = $cashAdvance;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs = $obj->getOutstandingSummary($warehousekey);
  
    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
           
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" >'.$rs[$i]['employeename'].'</div>   
        <div class="div-table-col-3" style="text-align:right;width:9em;">'.$obj->formatNumber($rs[$i]['total']).'</div>  
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['cashAdvance'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel); 
}

function generatePanelCashBankOutstanding ($panelName){
    $chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
    
    global $security;
    global $templatePanel; 
    
    $obj = $chartOfAccount;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs = $obj->searchData($obj->tableName.'.iscashbank',1,true,' and '.$obj->tableName.'.isleaf = 1','order by orderlist asc');
  
    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
           
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" >'.$rs[$i]['name'].'</div>  
        <div class="div-table-col-3" style="text-align:right;width:9em;">'.$obj->formatNumber($rs[$i]['amount']).'</div>  
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['cashBank'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel); 
}


function generatePanelTopCustomerByJO ($panelName,$startPeriod, $endPeriod,$warehousekey){ 
    global $security;
    global $templatePanel; 
    global $truckingServiceOrder; 
    global $customer; 
    
    $obj = $truckingServiceOrder;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs = $obj->getBestSalesAmountByGroup($customer->tableName.'.pkey', $startPeriod, $endPeriod,5,$warehousekey);  
  
    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
           
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" style="width:22em;">'.$rs[$i]['customername'].'</div>  
        <div class="div-table-col-3" style="text-align:right">'.$obj->formatNumber($rs[$i]['amount']).'</div>  
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['topCustomers'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel); 
}

function generatePanelCustomerCreditLimitSummary($panelName){
    global $security;
    global $templatePanel;  
    global $customer; 
    
    $obj = $customer;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs = $obj->getCustomerCreditLimitSummary($customer->tableName.'.pkey', true, ' order by aroutstanding desc');  
  
    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
           
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" >'.$rs[$i]['name'].'</div>  
        <div class="div-table-col-3" style="text-align:right;width:8em">'.$obj->formatNumber($rs[$i]['creditlimit']).'</div>  
        <div class="div-table-col-3 text-red-cardinal" style="text-align:right;width:8em">'.$obj->formatNumber($rs[$i]['creditlimit'] - $rs[$i]['aroutstanding']).'</div>  
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['creditLimit'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel); 
}

function generatePanelCashBankRealizationSummary ($panelName){  
    $apEmployee = createObjAndAddToCol(new APEmployee());   
    $arEmployee = createObjAndAddToCol(new AREmployee());   
    
    $obj = createObjAndAddToCol(new CashBankRealization()); 
         
    global $security;
    global $templatePanel;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs = $obj->getRealizationDashboardSummary();  
  
    $content = '<div class="div-table table-with-border" style="width:100%; font-size:1.2em">';

    $total = 0;
    
    for ($i=0;$i<count($rs);$i++){ 
        $class = ($rs[$i]['amount'] < 0) ? 'text-red-cardinal' : '';
        
        $content .='     
        <div class="div-table-row '.$class.'"> 
        <div class="div-table-col-3" style="width:22em;">'.$rs[$i]['label'].'</div>  
        <div class="div-table-col-3" style="text-align:right">'.$obj->formatNumber($rs[$i]['amount']).'</div>  
        </div>
        '; 
         
        $total += $rs[$i]['amount'];
    }
        
     $class = ($total< 0) ? 'text-red-cardinal' : '';
     $content .='     
        <div class="div-table-row '.$class.'" style="font-weight:bold"> 
        <div class="div-table-col-3" style="border-bottom:0">'.$obj->lang['total'].'</div>  
        <div class="div-table-col-3" style="border-bottom:0; text-align:right">'.$obj->formatNumber($total).'</div>  
        </div>
        '; 
    
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['cashBankRealization'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel); 
}

function generateUninvoicedJobOrder($panelName){
    global $security;
    global $templatePanel;  
    global $disposalSalesInvoice; 
    global $disposalJobOrder; 
    
    $obj = $disposalSalesInvoice;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
   
    $rs = $disposalJobOrder->getUninvoicedJobOrder();  
  
    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0;$i<count($rs);$i++){ 
           
        $content .='     
        <div class="div-table-row"> 
        <div class="div-table-col-3" style="width:12em">'.$rs[$i]['code'].'</div>   
        <div class="div-table-col-3" >'.$rs[$i]['customername'].'</div>  
        <div class="div-table-col-3 text-red-cardinal" style="text-align:right;width:7em">'.$obj->formatNumber($rs[$i]['totaluninvoiced']).'</div>  
        </div>
        '; 
         
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['uninvoicedSOReport'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel); 
}


function generateDailyOngoingWorkOrder($panelName, $warehousekey = ''){
    global $security; 
    global $templatePanel;  
    // global $truckingServiceWorkOrder; 
    global $truckingServiceOrder; 
    
    // $truckingServiceOrder = new TruckingServiceOrder();
    $truckingServiceOrderCategory = new TruckingServiceOrderCategory();
    // getDataForScheduleReport
     
    $obj = $truckingServiceOrder;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;
    
    $arrDate  = array();
    $interval = 2;
    $totalLoop = ($interval * 2) + 1;
    
    $currDate  = date('Y-m-d');
    $startDate = new DateTime($currDate);
    
    // mundurin dulu biar urut
    $startDate = $startDate->sub(new DateInterval('P'.($interval+1).'D')); 
     
    for($i=0;$i<$totalLoop;$i++){ 
        $startDate->add(new DateInterval('P1D')); 
        array_push($arrDate,$startDate->format('Y-m-d'));
    }
    // $dateCriteria = 'date('.$truckingServiceOrder->tableNameDetail.'.date) between '. $truckingServiceOrder->oDbCon->paramString($arrDate[0]) .  ' and '.$truckingServiceOrder->oDbCon->paramString($arrDate[]) ;
    
	// ambil tipe SPK Utama saja 
    // $rs = $obj->getWorkOrderVolume(array('type'=>'daily','date' => $arrDate),array('warehousekey' => $warehousekey,
	// 																			   'criteria' => ' and '.$obj->tableName.'.workordercategorykey =1'
	// 																			  ));
    
    $rs = $obj->getWorkOrderVolume(array('type'=>'daily','date' => $arrDate),array('warehousekey' => $warehousekey));
	
    // $testing =  array_column($testing,null,'indexkey'); 
    $rs =  array_column($rs,null,'indexkey'); 
    $rsCategory = $truckingServiceOrderCategory->searchDataRow(array($truckingServiceOrderCategory->tableName.'.pkey',$truckingServiceOrderCategory->tableName.'.name'),
                                                               ' and '. $truckingServiceOrderCategory->tableName.'.statuskey = 1');
    
    $content = '<div class="flex daily-ongoing-work-order-panel">';   
    
    foreach($arrDate as $key=>$dateRow){
        $content .= '<div>';
        $content .= '<div class="daily-panel">';
        $content .= '<div class="title">'.$obj->formatDBDate($dateRow).'</div>';    
        $content .= '<div class="div-table" style="width:100%">';
         
        foreach($rsCategory as $categoryRow){ 
            $categoryIndex =  $categoryRow['pkey'];
            $dateIndex = $obj->formatDBDate($dateRow,'Y-m-d');
            $indexkey = $categoryIndex.'-'.$dateIndex;
            
            $total = (isset($rs[$indexkey])) ? $rs[$indexkey]['total']: 0; 
            $classEmpty = ($total == 0) ? 'text-muted' : '';
            
            $categoryNameLabel = $categoryRow['name'];
            $totalLabel = $obj->formatNumber($total);
            
            if($total > 0){
                $categoryNameLabel = $categoryNameLabel;
                $totalLabel = $totalLabel;
            }
            
            $content .= '<div class="div-table-row data-row '.$classEmpty.'">';
            $content .= '<div class="div-table-col-3">'.$categoryNameLabel.'</div>'; 
            $content .= '<div class="div-table-col-3" style="text-align:right">'.$totalLabel.'</div>'; 
            $content .= '</div>'; 
        } 
        
        $content .= '</div>';  
        $content .= '</div>';  
        $content .= '</div>';
    }
    
    $content .= '</div>'; 
      
    return $content;
}

//
function generatePanelPettyCash($panelName,$endPeriod){
    $pettyCash = createObjAndAddToCol(new PettyCash());
    $chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
    
    global $security;
    global $templatePanel; 
    global $rsWidgetProperties;
    
    $obj = $pettyCash;
     
    if(!$security->isAdminLogin($obj->securityObject,10)) return;

    $rsSettings = $rsWidgetProperties[$panelName] ?? [];
    $rsSettings = array_column($rsSettings,null,'properties');
    
    $coakeys = getWidgetValue($rsSettings,'coakey'); 
    
    // $pettyCashParentKey = 8029;
    $rsCOA = $chartOfAccount->searchDataRow(array($chartOfAccount->tableName.'.pkey',$chartOfAccount->tableName.'.code',$chartOfAccount->tableName.'.name',$chartOfAccount->tableName.'.parentkey'), ' and ' .$chartOfAccount->tableName.'.pkey in ('.$chartOfAccount->oDbCon->paramString($coakeys,',').') ');
    // $arrCOAKey = array_column($rsCOA, 'pkey');   

    $rs = $obj->getDataDashboardSummary($coakeys, $endPeriod);
  
    $content = '<div class="div-table table-with-border" style="width:100%">';

    for ($i=0; $i<count($rsCOA); $i++){ 
        $coaKey = $rsCOA[$i]['pkey'];
        
        if (isset($rs[$coaKey])) {
            $rsCol = $rs[$coaKey];
            $lastDate = $obj->formatDBDate($rsCol[0]['lasttrdate'], 'd / m / Y');
            $balance  = $obj->formatNumber($rsCol[0]['balance']);
        } else {
            $lastDate = '00 / 00 / 0000';
            $balance  = $obj->formatNumber(0);
        }

        $content .= '     
        <div class="div-table-row"> 
            <div class="div-table-col-3">'.$rsCOA[$i]['name'].'</div>  
            <div class="div-table-col-3" style="text-align:center; width: 8em">'.$lastDate.'</div>  
            <div class="div-table-col-3" style="text-align:right; width: 7em">'.$balance.'</div>  
        </div>';
    }
        
    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] =$obj->lang['pettyCash'];  
    $replacement['content'] = $content;  

    return replaceContent($replacement, $templatePanel); 
}


function generatePanelOutstandingAP($panelName,$rowsLimit = 10, $warehousekey = ''){ 
    
    $ap = createObjAndAddToCol(new AP()); 
    $supplierCategory = createObjAndAddToCol(new SupplierCategory());

    global $security; 
    global $templatePanel;  

    $resultPanel = '';
    
    $obj = $ap;

    $rsCategory = $supplierCategory->searchDataRow(array($supplierCategory->tableName.'.pkey',$supplierCategory->tableName.'.name'),
												' and '. $supplierCategory->tableName.'.statuskey = 1');

    $arrTitle = array();
    foreach($rsCategory as $categoryRow) {
        array_push($arrTitle, $categoryRow['name']);
    }

    $arrOpt = array('groupBy' => $obj->tableName.'.supplierkey', 
					'orderBy' => ' total desc'
				  );

    $arrGroupData = array(
        'supplierkey' => $obj->lang['APOutstanding'] . ' ('.$obj->lang['supplier'].')',
        'categorykey' => $obj->lang['APOutstanding'] . ' ('.$obj->lang['supplierCategory'].')'
    );
    
    $criteria = ' and (' . $obj->tableName.'.statuskey = 1 or ' . $obj->tableName.'.statuskey = 2) ';
    if (!empty($warehousekey))
            $criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
    //$criteria .= ' and '.$obj->tableSupplier.'.categorykey = '.$obj->oDbCon->paramString($value['pkey']).' ';
    $groupBy = 'group by supplierkey';
    $rs = $obj->generateOutstandingAPDashboardSummary($criteria, 'order by totaloutstanding desc', $groupBy);    

    $allPanels = '<div style="display:flex; flex-wrap:wrap; gap:10px;">';

    foreach($rsCategory as $key => $value) {

        if(!isset($rs[$value['pkey']])) continue;

        $panelTitle = $value['name'];

        $rsCol =  $rs[$value['pkey']];

        $content = '<div class="div-table table-with-border" style="width:100%">';
        $total = 0;
        for ($i=0;$i<count($rsCol);$i++){ 
            $content .='     
                <div class="div-table-row">
                    <div class="div-table-col-3">'.$rsCol[$i]['suppliername'].'</div> 
                    <div class="div-table-col-3" style="width:5em; text-align:right">'.$obj->formatNumber($rsCol[$i]['totaloutstanding']).'</div>
                </div>
            '; 
            $total+= $rsCol[$i]['totaloutstanding'];
        }
        
        $content .='     
            <div class="div-table-row total-row">
                <div class="div-table-col-3">'.strtoupper($obj->lang['total']).'</div> 
                <div class="div-table-col-3" style="width:5em; text-align:right">'.$obj->formatNumber($total).'</div>
            </div>
        '; 
        
        $content .= '</div>';

        $replacement = array();
        $replacement['title']   = $panelTitle;  
        $replacement['headerSummary'] = '<div class="header-summary">'.$obj->formatNumber($total).'</div>';
        $replacement['content'] = $content;  

        // lebar panel otomatis max 3 per baris
        $allPanels .= '<div style="width:33%;">'. 
                        replaceContent($replacement, $templatePanel) .
                      '</div>';
    }

$allPanels .= '</div>';

return $allPanels;
}

function generateCOAMonitoring($panelName, $startPeriod, $endPeriod, $title) {
    
    $chartOfAccount = createObjAndAddToCol(new ChartOfAccount());

    global $security;
    global $templatePanel;
    global $rsWidgetProperties;

    $obj = $chartOfAccount;

    if(!$security->isAdminLogin($obj->securityObject,10)) return;

    // cek properties 
	$rsSettings = $rsWidgetProperties[$panelName] ?? [];
    $rsSettings = array_column($rsSettings,null,'properties');
    
    $coakeys = getWidgetValue($rsSettings,'coakey'); 
    
    //$dtStart = DateTime::createFromFormat('F Y', $startPeriod);
    //$startDtFormatted = $dtStart->format('m / Y');
//
    //$dtEnd = DateTime::createFromFormat('F Y', $endPeriod);
    //$endDtFormatted = $dtEnd->format('m / Y');
    
    //$rsCOA = $obj->generateCOAMonitoring($coakeys);

    $rsCOA = $obj->searchData('','',true,' and '.$obj->tableName.'.pkey in ('.$obj->oDbCon->paramString($coakeys,',').') ') ;
  
    $content = '<div class="div-table table-with-border" style=" width:100%">';
    $total = 0;
    for ($i=0;$i<count($rsCOA);$i++){ 
        
        $content .='     
        <div class="div-table-row "> 
        <div class="div-table-col-3" >'.$rsCOA[$i]['name'].'</div>
        <div class="div-table-col-3" style="width:5em;;text-align:right">'.$obj->formatNumber($rsCOA[$i]['amount'],2).'</div> 
        </div>
        '; 
        $total+= $rsCOA[$i]['amount'];
    }

    $content .='     
            <div class="div-table-row total-row">
                <div class="div-table-col-3">'.strtoupper($obj->lang['total']).'</div> 
                <div class="div-table-col-3" style="width:5em; text-align:right">'.$obj->formatNumber($total,2).'</div>
            </div>
        ';

    $content .= '</div>';
    
    $replacement = array();
    $replacement['title'] = $title;  
    $replacement['headerSummary'] = '<div class="header-summary">'.$obj->formatNumber($total,2).'</div>';
    $replacement['content'] = $content;  
    //$replacement['iconSetting'] = '<i class="btn-widget-setting fal fa-cog"></i>';
	
    return replaceContent($replacement, $templatePanel);  

}



function getPanelDOM($panelName, $asJqueryObj = false){ 
    return '$("#dashboard").find(".'.$panelName.'")' . (($asJqueryObj) ? '' : '[0]');
}

function getWidgetValue($rsSettings,$key){ 
     global $class;
     
	 $value = (isset($rsSettings[$key]['value']) && !empty($rsSettings[$key]['value'])) ? $rsSettings[$key]['value'] : $rsSettings[$key]['defaultvalue'];   
     $typekey = (isset($rsSettings[$key]['opt']) && !empty($rsSettings[$key]['opt'])) ? array_keys(json_decode($rsSettings[$key]['opt'],true))[0] : '';
     
     if($class->isJson($value))
         $value = json_decode($value,true); 
 
     switch($typekey){
         //case 'database' :
         case 'select-opt' : $value = $value['value'];  break;
     }
     
    return $value;
}



function generateIncomeStatementGraph($panelName, $startPeriod, $endPeriod, $groupBy, $warehousekey){
	  
			global $rsWidgetProperties;  
    		global $templateLineGraphPanel;
	
			$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
		 	$obj = $chartOfAccount;
	
			$colorSet = array('#008eb3','#e9656b', '#515898');
	
			// cek properties 
			$rsSettings = $rsWidgetProperties[$panelName] ?? [];
			$rsSettings = array_column($rsSettings,null,'properties');

			$graphItem = getWidgetValue($rsSettings,'graphitem') ?? [];  
  	
    		$title = $obj->lang['profitLoss'] .' '.$obj->lang['inThousand']; 
	
    		$arrayToJs = array();   
			$arrTitle = array();
			$arrColorSet = array();
	
    		array_push($arrTitle,$obj->lang['period']);
					   
			if(in_array('revenue',$graphItem)) { 
				array_push($arrTitle,$obj->lang['revenue']);
				array_push($arrColorSet, array('color' => $colorSet[0]));
			}
			
			if(in_array('cost',$graphItem)){ 
				array_push($arrTitle,$obj->lang['cost']);
				array_push($arrColorSet, array('color' => $colorSet[1]));
			}
						   
			if(in_array('profitLoss',$graphItem)){
				array_push($arrTitle,$obj->lang['profitLoss']); 
				array_push($arrColorSet, array('color' => $colorSet[2])); 
			}
	
			array_push($arrayToJs,$arrTitle);
    
            $dateFrom = date("01 / m / Y", strtotime($startPeriod));
			$dateTo = date("t / m / Y", strtotime($endPeriod)); 
    
			$arrIncome = $obj->getCOAAmount(array('income'),$dateFrom,$dateTo,1,$warehousekey);
			$arrExpense = $obj->getCOAAmount(array('expense'),$dateFrom,$dateTo,1,$warehousekey);
	   
			// hitugn total cost dan revenue per periode
			$newArrIncome = array();
			foreach($arrIncome as $key=>$row){
				if (!isset($newArrIncome[$key])) $newArrIncome[$key] = 0;
				
				$rs = $row['rs'];
				foreach($rs as $amountRow)
					if($amountRow['parentkey'] == 0) // ambil rootny aj biar cepet
						$newArrIncome[$key] += $amountRow['amount'];
			}
	
	
			// hitugn total cost dan revenue per periode
			$newArrExpense = array();
			foreach($arrExpense as $key=>$row){
				if (!isset($newArrExpense[$key])) $newArrExpense[$key] = 0;
				
				$rs = $row['rs'];
				foreach($rs as $amountRow)
					if($amountRow['parentkey'] == 0) // ambil rootny aj biar cepet
						$newArrExpense[$key] += $amountRow['amount'];
			}
	
            $period = $obj->getMonthPeriod($startPeriod, $endPeriod);
                
			foreach ($period as $dt) {
                
				$keyIndex = $dt->format('d / m / Y');   

				$tempArray = array();
				array_push($tempArray, $dt->format('M Y'));
 
				// income
				$incomeAmount = (isset($newArrIncome[$keyIndex])) ? $newArrIncome[$keyIndex]  / 1000 : 0;
							
				if(in_array('revenue',$graphItem)) 
						array_push($tempArray, $incomeAmount); 
				 
				// expense
				$expenseAmount = (isset($newArrExpense[$keyIndex])) ? $newArrExpense[$keyIndex] / 1000  : 0;
				$expenseAmount *= -1;
				
				if(in_array('cost',$graphItem)) 
					array_push($tempArray, $expenseAmount); 
				
				// profit/loss
				if(in_array('profitLoss',$graphItem)) 
					array_push($tempArray, $incomeAmount - $expenseAmount); 
				 
				array_push($arrayToJs,$tempArray) ; 
			}
  
	
	
			$arrParam = array();
			$arrParam['data'] = $arrayToJs;
			$arrParam['callbackName'] = 'drawIncomeStatementChart';
			$arrParam['panelName'] = $panelName;
			$arrParam['title'] = $title ;

	
			$html =  generateLineChart($arrParam, array('colorSet' => json_encode($arrColorSet)) ); 

			return $templateLineGraphPanel.$html;  


	
}


?>