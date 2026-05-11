<?php

    require_once '../../_config.php';
    require_once '_include.php';

    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php'; 
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php'; 
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/WidgetSetting.class.php';
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PurchaseOrder.class.php';
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AP.class.php';
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AR.class.php';


    $warehouse = new Warehouse();
    $salesOrder = new SalesOrder();
    $item = new Item();
    $customer = new Customer();
    $widgetSetting = new WidgetSetting();
    $purchaseOrder = new PurchaseOrder();
    $ap = new AP();
    $ar = new AR();

    function getNewObj(){ 
        return new WidgetSetting(); 
    }

    $obj = getNewObj();
    

    require_once '_global.php';

    $rsWidgetProperties = $widgetSetting->getPropertiesValue();
    $rsWidgetProperties = $class->reindexDetailCollections($rsWidgetProperties,'name');
    

    $rowsLimit = 25;
    $amountIn = $class->loadSetting('dashboardAmountIn');
    if(empty($amountIn)) $amountIn = 1000;

    $saysInThousand = ($amountIn == 1000) ? $class->lang['inThousand'] : '';


    function widgetResult($title, $widgetName,$data,$startPeriod = null, $endPeriod = null)
    {
        return [
            'title' => $title,
            'widget_name' => $widgetName,
            'period' => [
                'start' => $startPeriod,
                'end'   => $endPeriod,
            ],
            'data' => $data
        ];
    }

    function generatePanelSalesGraph($widgetName, $startPeriod, $endPeriod,$warehousekey){ 

        global $class;   
        global $amountIn;
        global $saysInThousand;
        global $security;
        global $customer;
        global $item;


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
					$rsSettings = $rsWidgetProperties[$widgetName];
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
            case COMPANY_TYPE['forwarding'] :  
                
                break;

            case COMPANY_TYPE['logistics'] :  

                break;
        
        }


        if(!$security->isAdminLogin($obj->securityObject,10)) {
            return [];
        }


        $title = $class->lang['salesGraph'].' '.$saysInThousand; 
  
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
        


        return widgetResult($title, $widgetName, $arrayToJs, $startPeriod, $endPeriod);

    }


    function generatePanelPendingSalesOrder($widgetName,$rowsLimit = 10, $warehousekey = ''){ 
    
        global $security;
    
        switch (PLAN_TYPE['categorykey']){
            
            case COMPANY_TYPE['retail'] :    
            
            case COMPANY_TYPE['jewelry'] :    
                        global $salesOrder;
                        $obj = $salesOrder; 
                        break;
            case COMPANY_TYPE['trucking'] :     
                        global $truckingServiceOrder;
                        $obj = $truckingServiceOrder;
                        break;  
            case COMPANY_TYPE['workshop'] :     global $salesOrderCarService;
                        $obj = $salesOrderCarService;
                        break;  
            case COMPANY_TYPE['tpamedical'] :    global $medicalJobOrder;
                        $obj = $medicalJobOrder;
                        break;  
        }
        
        if(!$security->isAdminLogin($obj->securityObject,10)) {
            return [];
        }
        
        $criteria = '';
        if (!empty($warehousekey))
                $criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
        
        $rs =  $obj->searchData($obj->tableName.'.statuskey',1,true,$criteria,'order by '.$obj->tableName.'.trdate desc','limit 0,'.  $rowsLimit );
        
        return widgetResult($obj->lang['unproccesedSalesOrder'], $widgetName, $rs);
    }

    function generatePanelPendingPurchaseOrder($widgetName,$rowsLimit = 10, $warehousekey = ''){ 
            global $security;
            global $purchaseOrder;
            
            $obj = $purchaseOrder;
            

            if(!$security->isAdminLogin($obj->securityObject,10)) {
                return [];
            }
            
            $criteria = '';
            if (!empty($warehousekey))
                    $criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
            
            $rs =  $obj->searchData($obj->tableName.'.statuskey',1,true,$criteria,'order by '.$obj->tableName.'.trdate desc','limit 0,'.  $rowsLimit );
            
            return widgetResult($obj->lang['unproccesedPurchaseOrder'], $widgetName, $rs);
    }

    function generatePanelSalesOrderTotalTransaction($widgetName, $startPeriod, $endPeriod, $warehousekey = ''){
        global $security;
        global $salesOrder;

        $obj = $salesOrder;

        if(!$security->isAdminLogin($obj->securityObject,10)) {
            return [];
        }

        $title = $obj->lang['salesOrder'] . ' ('.$obj->lang['total'].' '.$obj->lang['transaction'].')';

        $rs = $obj->getSalesOrderTotalTransaction($startPeriod,$endPeriod,$warehousekey);

        $totalTransaction = 0;
        $totalAmount = 0;
        $detail = [];

        foreach ($rs as $row) {
            // total keseluruhan
            $totalTransaction += $row['totaltransaction'];
            $totalAmount += $row['totalamount'];

            // detail per tanggal
            $detail[$row['date']] = [
                'total_transaction' => $row['totaltransaction'],
                'total_amount' => $row['totalamount']
            ];
        }

        $result = [
            'total_transaction' => $totalTransaction,
            'total_amount' => $totalAmount,
            'detail' => $detail
        ];

        return widgetResult($title, $widgetName, $result, $startPeriod, $endPeriod);
    }


    function generatePanelMinStock($widgetName, $limit=10)
    {
        global $item;
        global $security;
        
        $obj = $item;
        
        if(!$security->isAdminLogin($obj->securityObject,10)) {
            return [];
        }
    
        $result =  $obj->searchData($obj->tableName.'.statuskey','1',true,' and minstockqty > 0 ','order by name asc','limit 0,'.  $limit  ,' having qtyonhand < minstockqty'); // .$obj->oDbCon->paramString($minStock) );

        $title = $obj->lang['lowStock'];  

        return widgetResult($title, $widgetName, $result);    
    }

    function generatePanelMaxStock($widgetName, $limit = 10){ 
        global $item;
        global $security;
        
        $obj = $item;

        if(!$security->isAdminLogin($obj->securityObject,10)) {
            return [];
        }
        
        $result =  $obj->searchData($obj->tableName.'.statuskey','1',true,' and maxstockqty > 0 ','order by qtyonhand desc','limit 0,'.  $limit  ,' having qtyonhand > maxstockqty '); // .$obj->oDbCon->paramString($maxStock) );

        $title = $obj->lang['overStock']; 

        return widgetResult($title, $widgetName, $result); 
        
    }  

    function generatePanelTopCustomerGraph($widgetName,$startPeriod, $endPeriod,$warehousekey){ 
    
        global $class;   
        global $security;
        global $customer;
        global $amountIn;
        
        $arrTitle = array();
        array_push($arrTitle, $class->lang['customer']);
        array_push($arrTitle, $class->lang['amount']); 
        
        switch (PLAN_TYPE['categorykey']){
            case COMPANY_TYPE['retail'] :     
            case COMPANY_TYPE['jewelry'] :    
                    global $salesOrder;
                    $obj = $salesOrder; 
                break;
            case COMPANY_TYPE['trucking'] :     
                    global $truckingServiceOrderInvoice;
                    $obj = $truckingServiceOrderInvoice; 
                    break;
            case COMPANY_TYPE['workshop'] :     
                    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                    break;  
            case COMPANY_TYPE['logistics'] :     global $disposalSalesInvoice;
                    $obj = $disposalSalesInvoice;
                    break;  
        }

        if(!$security->isAdminLogin($obj->securityObject,10)) {
            return [];
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
            
        return widgetResult($title, $widgetName, $arrayToJs, $startPeriod, $endPeriod);
    
    }


    function generatePanelBestSellingGraph($widgetName,$startPeriod, $endPeriod, $warehousekey){ 
    
        global $class;    
        global $item; 
        global $security;
            
        $arrTitle = array();
        array_push($arrTitle, $class->lang['item']);
        array_push($arrTitle, $class->lang['qty']); 
        
        switch (PLAN_TYPE['categorykey']){
        case COMPANY_TYPE['retail'] :     
            case COMPANY_TYPE['jewelry'] :    
                    global $salesOrder;
                    $obj = $salesOrder; 
                break;
        case COMPANY_TYPE['trucking'] :    
                    global $truckingServiceOrderInvoice;
                    $obj = $truckingServiceOrderInvoice;
                break;  
        case COMPANY_TYPE['workshop'] :     
                    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                break;  
        }

        if(!$security->isAdminLogin($obj->securityObject,10)) {
            return [];
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
        
        return widgetResult($title, $widgetName, $arrayToJs, $startPeriod, $endPeriod);
    }

    function generatePanelProfitByItemGraph($widgetName, $startPeriod, $endPeriod, $warehousekey){ 
   
        global $class;    
        global $item; 
        global $amountIn;
        global $security;
        
        $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
        if(!$hasCOGSAccess) return;
            
        $arrTitle = array();
        array_push($arrTitle, $class->lang['item']);
        array_push($arrTitle, $class->lang['amount']); 
        
        switch (PLAN_TYPE['categorykey']){
            case COMPANY_TYPE['retail'] :    
            case COMPANY_TYPE['jewelry'] :    
                    global $salesOrder;
                    $obj = $salesOrder; 
                break;
            case COMPANY_TYPE['workshop'] :     
                    global $salesOrderCarService;
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
        
        return widgetResult($title, $widgetName, $arrayToJs, $startPeriod, $endPeriod);
        
    }


    function generatePanelProfitByCategoryGraph($widgetName,$startPeriod, $endPeriod, $warehousekey){ 
   
        global $class;    
        global $item;
        global $amountIn;
        global $security;
        
        $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
        if(!$hasCOGSAccess) return;
        
        $arrTitle = array();
        array_push($arrTitle, $class->lang['category']);
        array_push($arrTitle, $class->lang['amount']); 
        
        switch (PLAN_TYPE['categorykey']){
            case COMPANY_TYPE['retail']   :
            case COMPANY_TYPE['jewelry'] :    
                    global $salesOrder;
                    $obj = $salesOrder; 
                break;
            case COMPANY_TYPE['trucking'] :     
                    global $truckingServiceOrder;
                    $obj = $truckingServiceOrder; 
                break;
            case COMPANY_TYPE['workshop'] :     
                    global $salesOrderCarService;
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
        
        
        return widgetResult($title, $widgetName, $arrayToJs, $startPeriod, $endPeriod);
    }

    function generatePanelProfitByBrandGraph($widgetName, $startPeriod, $endPeriod, $warehousekey){ 

        global $class;   
        global $item;
        global $amountIn;
        global $security; 
        
        $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
        if(!$hasCOGSAccess) return;
            
        $arrTitle = array();
        array_push($arrTitle, $class->lang['brand']);
        array_push($arrTitle, $class->lang['amount']); 
        
        
        switch (PLAN_TYPE['categorykey']){
            case COMPANY_TYPE['retail'] :
            case COMPANY_TYPE['jewelry'] :    
                    global $salesOrder;
                    $obj = $salesOrder; 
                break;
            case COMPANY_TYPE['workshop'] :     
                    global $salesOrderCarService;
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
        
        return widgetResult($title, $widgetName, $arrayToJs, $startPeriod, $endPeriod);
    }

    function generatePanelUnderMarginSalesOrder($widgetName, $rowsLimit = 10, $warehousekey=''){  
        global $security; 
        global $item;
        global $salesOrder;

        
        switch (PLAN_TYPE['categorykey']){
            case COMPANY_TYPE['retail'] :     
        
            case COMPANY_TYPE['jewelry'] :    
                    global $salesOrder;
                    $obj = $salesOrder; 
                break;
            case COMPANY_TYPE['workshop'] :    
                    global $salesOrderCarService;
                    $obj = $salesOrderCarService;
                break;  
        } 
        
        if(!$security->isAdminLogin($obj->securityObject,10)) {
            return [];
        }

        $criteria = '';
        if (!empty($warehousekey))
                $criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';

        $result =  $obj->searchData('','',true,' and '.$obj->tableName.'.statuskey in (2,3) and profit < 0 '.$criteria,' order by pkey desc','limit 0,'.  $rowsLimit );

        $title = $obj->lang['underMarginSalesOrder'];  

        return widgetResult($title, $widgetName, $result);
        
    }

    function generatePanelNotMovingStock($widgetName,$rowsLimit = 10){
        global $security;
        global $item;


        if(!$security->isAdminLogin($item->securityObject,10)) {
            return [];
        }
        
        $monthInterval = 3;
        
        
        $result = $item->getNotMovingStock($monthInterval,$rowsLimit);

        $title = $item->lang['notMovingStock'];
    
        return widgetResult($title, $widgetName, $result);
    }

    function generatePanelEmptyStock($widgetName,$rowsLimit = 10){ 
        global $item;
        global $security;
        $obj = $item;

        if(!$security->isAdminLogin($obj->securityObject,10)) {
            return [];
        }
        
        $result = $obj->getLatestEmptyStock($rowsLimit);
        
        $title = $obj->lang['emptyStock'];
        
        return widgetResult($title, $widgetName, $result);
    }

    function generatePanelOverdueAP($widgetName,$rowsLimit = 10, $warehousekey = ''){  
        global $ap; 
        global $security; 
        
        $obj = $ap;

        if(!$security->isAdminLogin($obj->securityObject,10)) {
            return [];
        }
                
        $criteria = '';
        if (!empty($warehousekey))
                $criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
        
        $result =  $obj->searchData('','',true,' and (' . $obj->tableName.'.statuskey in (1,2) ) and duedate <  date(now()) '.$criteria,'order by duedate asc' , 'limit 0,'.  $rowsLimit);

        $title = $obj->lang['overdueAccountsPayable']; 

        return widgetResult($title, $widgetName, $result);
    }


    function generatePanelOverdueAR($widgetName,$rowsLimit = 10, $warehousekey = ''){ 
        global $ar;
        global $security; 
        
        $obj = $ar;

        if(!$security->isAdminLogin($obj->securityObject,10)) {
            return [];
        }
        
        $criteria = '';
        if (!empty($warehousekey))
                $criteria .= ' and '.$obj->tableName.'.warehousekey in ('. $obj->oDbCon->paramString($warehousekey,',').' )';
        
        $result =  $obj->searchData('','',true,' and (' . $obj->tableName.'.statuskey = 1 or ' . $obj->tableName.'.statuskey = 2) and duedate <  date(now()) ' . $criteria,'order by duedate asc', 'limit 0,' . $rowsLimit );

        $title = $obj->lang['overdueAccountsReceivable'];  
        
        return widgetResult($title, $widgetName, $result);

    }  

    //END FUNCTION

    if (!in_array($ACTION, array('GET')))
        endForRequestMethodError();

    $RETURN_VALUE = array();

    switch ($ACTION)    {

        case 'GET':


            $hasSuccessValue = false;
            $arrFailed = array();
            $ARR_RETURN_VALUE = array();

            $result = [];

            $obj = getNewObj();
            
            if (!empty($errorMessage)) {
                    addFailedRows($arrFailed, array(
                        'code' => '',
                        'message' => $errorMessage, // error disini,
                ));
            } else {

                try {
                    if (!$obj->oDbCon->startTrans(true))
                        throw new Exception($obj->errorMsg[100]);


                        $widgetsRaw = $_GET['widget'] ?? '';
                        $widgets = array_filter(
                            array_map(fn($v) => strtolower(trim($v)), explode(',', $widgetsRaw))
                        );
                    
                        $startPeriod = $_GET['start_period'] ?? (new DateTime('first day of january this year'))->format('Y-m-d');
                        $endPeriod = $_GET['end_period'] ?? date('Y-m-t');


                        $warehousekey = null;
                        if(isset($_GET['warehouse_id']) && !empty($_GET['warehouse_id'])) {
                            $rsWarehouse = $warehouse->searchDataRow(array($warehouse->tableName.'.pkey',$warehouse->tableName.'.code'), ' and ' . $warehouse->tableName.'.code = '.$obj->oDbCon->paramString($_GET['warehouse_id']).' ');
                            $warehousekey = $rsWarehouse[0]['pkey'];
                        }

                        $rowLimit = 10;
                        if(isset($_GET['limit']) && !empty($_GET['limit'])) {
                            $rowLimit = $_GET['limit'];
                        }

                    foreach($widgets as $widget) {

                        switch ($widget) {
                            case 'salesgraph':

                                $result[$widget] = generatePanelSalesGraph($widget, $startPeriod, $endPeriod,$warehousekey);
                                
                                break;

                            case 'pendingsalesorder':   

                                $result[$widget] = generatePanelPendingSalesOrder($widget, $rowLimit,$warehousekey);
                                
                                break;

                            case 'pendingpurchaseorder':

                                $result[$widget] = generatePanelPendingPurchaseOrder($widget, $rowLimit,$warehousekey);
                            
                                break;

                            case 'salesordertotaltransaction':

                                $result[$widget] = generatePanelSalesOrderTotalTransaction($widget, $startPeriod, $endPeriod,$warehousekey);
                                
                                break;

                            case 'minstock':

                                $result[$widget] = generatePanelMinStock($widget,$rowLimit);
                                
                                break;
                            
                            case 'maxstock' :  
                                
                                $result[$widget] = generatePanelMaxStock($widget,$rowLimit);  
                            
                                break;

                            case 'topcustomergraph' :  
                                
                                $result[$widget] = generatePanelTopCustomerGraph($widget,$startPeriod,$endPeriod,$warehousekey);  
                            
                                break;

                            case 'bestsellinggraph' :  

                                $result[$widget] = generatePanelBestSellingGraph($widget,$startPeriod,$endPeriod,$warehousekey); 
                                
                                break; 

                            case 'profitbyitemgraph' :  
                               
                                $result[$widget] = generatePanelProfitByItemGraph($widget,$startPeriod,$endPeriod,$warehousekey); 
                               
                                break;

                            case 'profitbycategorygraph' :  
                                
                                $result[$widget] = generatePanelProfitByCategoryGraph($widget,$startPeriod,$endPeriod,$warehousekey); 
                                
                                break;

                            case 'profitbybrandgraph' :  
                                
                                $result[$widget] = generatePanelProfitByBrandGraph($widget,$startPeriod,$endPeriod,$warehousekey);  
                                
                                break; 

                            case 'undermarginsalesorder' :  
                                
                                $result[$widget] = generatePanelUnderMarginSalesOrder($widget,$rowLimit,$warehousekey);  
                                
                                break;

                            case 'notmovingstock' :  
                                
                                $result[$widget] = generatePanelNotMovingStock($widget,$rowLimit);  
                                
                                break;
                            
                            case 'emptystock' :  

                                $result[$widget] = generatePanelEmptyStock($widget,$rowLimit);  

                                break;

                            case 'overdueap' :  

                                $result[$widget] = generatePanelOverdueAP($widget,$rowLimit,$warehousekey);   

                                break;
                            
                            case 'overduear' :  

                                $result[$widget] = generatePanelOverdueAR($widget,$rowLimit,$warehousekey);   

                                break;

                            default:
                                $result[$widget] = null;
                                break;
                        }

                    }


                    if (empty($result)) {
                        throw new Exception('Dashboard data is empty.');
                    }
              

                $obj->oDbCon->endTrans();

                } catch (Exception $e) {
                    $obj->oDbCon->rollback();
                    addFailedRows($arrFailed, [
                        'code' => '',
                        'message' => $e->getMessage()
                    ]);
                }

            }


            if($result) {

                array_push(
                    $ARR_RETURN_VALUE,
                    array(
                        'code' => '',
                        'message' => 'success',
                        'widget' => $result
                    )
                );
                $hasSuccessValue = true;
            }   else {
                addFailedRows($arrFailed, [
                    'code' => '',
                    'message' => 'Failed to generate dashboard data'
                ]);
            }



            $RETURN_VALUE['response_code'] = ($hasSuccessValue) ? 200 : 409;
            $RETURN_VALUE['success_data'] = $ARR_RETURN_VALUE;
            $RETURN_VALUE['failed_data'] = $arrFailed;


            break;
    }


    http_response_code($RETURN_VALUE['response_code']);
    echo json_encode($RETURN_VALUE);

    die;

?>