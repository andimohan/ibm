<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';
 
includeClass(array('SalesOrder.class.php'));
$salesOrder = createObjAndAddToCol(new SalesOrder());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());
$brand = createObjAndAddToCol(new Brand());
$employee = createObjAndAddToCol(new Employee());

$_POST['hidTotalFreezeCol'] = 3;

include '_global.php';

$obj = $salesOrder;
$securityObject = 'reportSalesOrder'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$arrFilterInformation = array();  
 

$arrGroupBy = array(
    array('key' => 'item', 'groupby' => 'itemkey','label' => $obj->lang['item'],'arrColumn' => array( 'itemCode' =>  array('title'=>ucwords($obj->lang['code']),'dbfield' => 'itemcode', 'width'=>"100px"),
                                                                                  'itemName' => array('title'=>ucwords($obj->lang['item']),'dbfield' => 'itemname', 'width'=>"300px")
                                                                                )),
    array('key' => 'customer', 'groupby' => 'customerkey','label' => $obj->lang['customer'],'arrColumn' => array( 'customerCode' =>  array('title'=>ucwords($obj->lang['code']),'dbfield' => 'customercode', 'width'=>"100px"),
                                                                                          'customerName' => array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"300px"),
                                                                                )),
    array('key' => 'brand', 'groupby' => 'brandkey','label' => $obj->lang['brand'],'arrColumn' => array( 'brandCode' =>  array('title'=>ucwords($obj->lang['code']),'dbfield' => 'brandcode', 'width'=>"100px"),
                                                                                                         'brandName' => array('title'=>ucwords($obj->lang['brand']),'dbfield' => 'brandname', 'width'=>"300px"),
                                                                                                    )),
    array('key' => 'salesman', 'groupby' => 'salesordersaleskey','label' => $obj->lang['salesman'],'arrColumn' => array( 'salesCode' =>  array('title'=>ucwords($obj->lang['code']),'dbfield' => 'salesmancode', 'width'=>"100px"),
                                                                                                         'salesNaame' => array('title'=>ucwords($obj->lang['salesman']),'dbfield' => 'salesmanname', 'width'=>"300px"),
                                                                                                    ))
          
);


// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$groupOpt = array(); 
$selGroupBy = (isset($_POST['selGroupBy']) && !empty($_POST['selGroupBy'])) ? $_POST['selGroupBy'] : '';
if(!empty($selGroupBy)){
    $arrTempGroup = array_column($arrGroupBy,null,'key');
    if(isset($arrTempGroup[$selGroupBy])) 
        $groupOpt = $arrTempGroup[$selGroupBy]; 
}

$hasGroup = (!empty($selGroupBy)) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);  

foreach($groupOpt['arrColumn'] as $key=>$row) 
         $arrDataStructure[$key] = $row;
// $arrDataStructure['customerName'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'name', 'width'=>"280px" );
 
// ==================================== ADD PERIOD COLUMN
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
    $_POST['trStartDate'] = date('F Y',mktime(0, 0, 0, 1, 1, date('Y')));
    $_POST['trEndDate'] = date('F Y');  
} 

$monthPeriod = $obj->getMonthPeriod($_POST['trStartDate'], $_POST['trEndDate']);

$arrKeyPeriod = array(); 
foreach ($monthPeriod as $dt) { 
    $keyIndex = $dt->format('nY');  
    $arrKeyPeriod[$keyIndex] = array('label' => $dt->format('M Y')); 
}


foreach($arrKeyPeriod as $keyPeriod => $period){ 
    $arrDataStructure['sales'.$keyPeriod] = array('title'=>ucwords($obj->lang['sales']),'group' => $period['label'], 'dbfield' => 'grandtotal'.$keyPeriod, 'width'=>"90px" ,"format" => 'number', "sortable" => false, 'calculateTotal' => true);
    // $arrDataStructure['grossprofit'.$keyPeriod] = array('title'=>ucwords($obj->lang['grossProfit']),'group' => $period['label'],'dbfield' => 'grossprofit'.$keyPeriod, 'width'=>"90px","format" => 'number', "sortable" => false, "textColor" => '568203', 'calculateTotal' => true);
}

$arrDataStructure['totalSales'] = array('title'=>ucwords($obj->lang['sales']),'group' => $obj->lang['total'], 'dbfield' => 'totalsales', 'width'=>"90px" , "format" => 'number', "sortable" => false, "style" => 'font-weight:bold', 'calculateTotal' => true);
// $arrDataStructure['totalGrossProfit'] = array('title'=>ucwords($obj->lang['grossProfit']),'group' => $obj->lang['total'], 'dbfield' => 'totalgrossprofit', 'width'=>"90px" ,"format" => 'number', "sortable" => false, "textColor" => '568203', "style" => 'font-weight:bold', 'calculateTotal' => true);
     

// ==================================== ADD PERIOD COLUMN
    
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['monthlySalesReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$customerCriteria = '';
$movementCriteria = '';
$criteria = '';  
 
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrCustomerStatus = $class->convertForCombobox($customer->getAllStatus(),'pkey','status');
$arrCustomer = $class->convertForCombobox($customer->searchDataRow(array($customer->tableName . '.pkey', $customer->tableName . '.name',), ' and ' . $customer->tableName . '.statuskey=2' . ' ORDER BY ' . $customer->tableName . '.name ASC'), 'pkey', 'name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrEmployee = $class->convertForCombobox($employee->searchData($employee->tableName.'.statuskey',2,true),'pkey','name');   
$arrBrand = $class->convertForCombobox($brand->searchData($brand->tableName.'.statuskey',1,true),'pkey','name'); 
$comboGroupBy = $class->convertForCombobox($arrGroupBy,'key','label');
$arrTwigVar['inputGroupBy'] =  $class->inputSelect('selGroupBy', $comboGroupBy);   
$arrTwigVar['inputStartDate'] = $class->inputMonth('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputMonth('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputCustomerName'] =  $class->inputSelect('selCustomer[]', $arrCustomer, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputChkAvailable'] =  $class->inputCheckBox('chkAvailable',array('overwritePost' => false, 'value' => 1, 'class' => 'no-class'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputItemName'] =  $class->inputText('itemName'); 
$arrTwigVar['inputBrandName'] =  $class->inputSelect('selBrand[]', $arrBrand, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSalesName'] =  $class->inputSelect('selSales[]', $arrEmployee, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
//$arrTwigVar['exportExcel'] = false; 
 
if (isset($_POST) && !empty($_POST['hidAction'])){   
     
    if(isset($_POST) && !empty($_POST['trStartDate'])){
        $criteria .= ' AND trdate BETWEEN '.$class->oDbCon->paramString( date('Y-m-d 00:00:00', strtotime($_POST['trStartDate'])) ).' AND '.$class->oDbCon->paramString( date('Y-m-t 23:59:59', strtotime($_POST['trEndDate'])) );
		array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	} 

    if(isset($_POST) && !empty($_POST['selSales'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSales']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.saleskey in('.$key.')';  

        $rsCriteria =  $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$salesName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Sales', 'filter' => $salesName));
        
	}

    if(isset($_POST) && !empty($_POST['selBrand'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selBrand']));   
        
        $criteria .=  ' AND '.$obj->tableItem.'.brandkey in('.$key.')';  

        $rsCriteria =  $brand->searchData('','',true, ' and '.$brand->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$brandName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Brand', 'filter' => $brandName));
        
	}

    if(isset($_POST) && !empty($_POST['itemName'])) { 
        $criteria .= ' AND '.$obj->tableItem.'.name  LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')';
	    array_push($arrFilterInformation,array("label" => 'Item', 'filter' => $_POST['itemName']));
	}

    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));
        
	}

    if (isset($_POST) && !empty($_POST['selCustomer'])) {

        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomer']));
        $customerCriteria .= ' AND ' . $customer->tableName . '.pkey in(' . $key . ')';
        $criteria .= ' AND ' . $obj->tableName . '.customerkey in(' . $key . ')';

        $rsCriteria = $customer->searchDataRow(array($customer->tableName . '.name'), ' and ' . $customer->tableName . '.pkey in (' . $key . ')');;

        $arrTempStatus = array();
        for ($k = 0; $k < count($rsCriteria); $k++)
            array_push($arrTempStatus, $rsCriteria[$k]['name']);

        $customerName = implode(", ", $arrTempStatus);
        array_push($arrFilterInformation, array("label" => $obj->lang['customer'], 'filter' => $customerName));
    }
    
    // if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
    //     $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
    //    	$movementCriteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

    //     $rsCriteria =  $obj->getStatusById ($key);
	 
    //     $arrTempStatus = array();
	// 	for ($k=0;$k<count($rsCriteria);$k++)
	// 	 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
	// 	$statusName = implode(", ",$arrTempStatus); 
	//     array_push($arrFilterInformation,array("label" => 'Status Transaksi', 'filter' => $statusName));
        
	// }

    $groupBy = 'group by year(trdate),month(trdate)';
    $groupBy .= (isset($_POST['selGroupBy']) && !empty($_POST['selGroupBy'])) ? ','.$groupOpt['groupby'] : '';
    
    $orderBy = (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'itemname';  
    $orderType = (isset ($_POST) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc'; 
 
	$order = 'order by '.$orderBy.' ' .$orderType; 
    //$rsCustomer = $customer->searchData('','',true,$customerCriteria,$order); 
    
    $criteria .= ' AND '.$obj->tableName.'.statuskey in(2,3)';  
    $rs = $obj->generateSalesOrderItem($criteria,$order,$groupBy);
    for( $i=0;$i<count($rs);$i++) { 
        $periodIndex = $rs[$i][$groupOpt['groupby']].'-'.$rs[$i]['month'].$rs[$i]['year'];
        $rs[$i]['periodindex'] = $periodIndex;
    }
    $rsSalesSummary = $obj->reindexDetailCollections($rs,$groupOpt['groupby']);
    

  
	$tempreport = '';

  
    
    foreach ($rsSalesSummary as $rsSalesSummary) {  
		 
        $indexKey = $rsSalesSummary[0][$groupOpt['groupby']];
        $hasSales = false;
        $totalSales = 0;
        $totalGrossProfit = 0;  
        $data = $rsSalesSummary[0]; 
        
        $rsSalesSummaryIndex = array_column($rsSalesSummary,null,'periodindex');
        
        foreach($arrKeyPeriod as $keyPeriod => $period){ 

            $periodIndex= $indexKey.'-'.$keyPeriod; 
             
            $amount = (isset($rsSalesSummaryIndex[$periodIndex]['totalsales'])) ? $rsSalesSummaryIndex[$periodIndex]['totalsales']: 0;
            $totalSales += $amount;  
                  
            $grossProfit = (isset($rsSalesSummaryIndex[$periodIndex]['grossprofit'])) ? $rsSalesSummaryIndex[$periodIndex]['grossprofit']: 0;
            $totalGrossProfit += $grossProfit;  
             
            if(!empty($amount) || !empty($grossProfit))
                $hasSales = true;
            
            $data['grandtotal'.$keyPeriod] = $amount; 
            $data['grossprofit'.$keyPeriod] = $grossProfit; 
            
            //$filterBy['customerkey'] = $customerkey;
            $filterBy['periodIndex'] = $periodIndex; 
 
        }
        
        $data['totalsales'] = $totalSales; 
        $data['totalgrossprofit'] = $totalGrossProfit; 
        if ($totalSales == 0 && $_POST['chkAvailable'] == 1) continue;
         
        
        
        $return = $obj->formatReportRows(array('data' => $data, 'totalFreezeCol' => $_POST['hidTotalFreezeCol']),$arrTemplate); 
            
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html']; 
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
             
    }  
    
    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
} 


echo $twig->render('reportMonthlySalesSummary.html', $arrTwigVar);   
?>