<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('TruckingServiceWorkOrder.class.php'));
$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
$car = new Car();
$warehouse = new Warehouse();
$supplier = new Supplier();
$truckingServiceOrder = new TruckingServiceOrder();

include '_global.php';

$obj = $truckingServiceWorkOrder;
$securityObject = 'reportRitaseSummary'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
 
$arrFilterInformation = array();  
 

$_POST['selDriverStatus[]'] = array(2);
$_POST['selCarStatus[]'] = array(1);
$_POST['selSupplierStatus[]'] = array(1);
$sellingPriceAllowed = $security->isAdminLogin($truckingServiceOrder->sellingPriceSecurityObject, 10);
    
if(!isset($_POST['selReportType'])) $_POST['selReportType'] = 1;
if(!isset($_POST['hideNoSales'])) $_POST['hideNoSales'] = 1;

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$hideNoSales = (isset($_POST['hideNoSales']) && $_POST['hideNoSales'] == 1) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);  
if($_POST['selReportType']==1){
    $arrDataStructure['codecol'] = array('title'=>ucwords($obj->lang['driver']),'dbfield' => 'name', 'width'=>"280px" );
}else if($_POST['selReportType']==2){ 
    $arrDataStructure['codecol'] = array('title'=>ucwords($obj->lang['car']),'dbfield' => 'policenumber', 'width'=>"280px" );
}else{
    $arrDataStructure['codecol'] = array('title'=>ucwords($obj->lang['supplier']),'dbfield' => 'name', 'width'=>"280px" ); 
}     
 
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
    $arrDataStructure['totalTrip'.$keyPeriod] = array('title'=>ucwords($obj->lang['trip']),'group' => $period['label'], 'dbfield' => 'totaltrip'.$keyPeriod, 'width'=>"20px" ,"align" => 'center',  "sortable" => false, "calculateTotal" => true);
  	
	if($_POST['selReportType']==3)
		$arrDataStructure['cost'.$keyPeriod] = array('title'=>ucwords($obj->lang['cost']),'group' => $period['label'],'dbfield' => 'cost'.$keyPeriod, 'width'=>"60px","format" => 'number', "sortable" => false, "textColor" => 'c95b5f', "calculateTotal" => true);
	
    if ($sellingPriceAllowed) { 
        $arrDataStructure['revenue'.$keyPeriod] = array('title'=>ucwords($obj->lang['revenue']),'group' => $period['label'],'dbfield' => 'revenue'.$keyPeriod, 'width'=>"60px","format" => 'number', "sortable" => false, "textColor" => '568203', "calculateTotal" => true);
    }
}

$arrDataStructure['totalTrip'] = array('title'=>ucwords($obj->lang['trip']),'group' => $obj->lang['total'], 'dbfield' => 'totaltrip', 'width'=>"20px" ,"align" => 'center', "sortable" => false,"format" => 'number', "style" => 'font-weight:bold', "calculateTotal" => true);

if($_POST['selReportType']==3)
	$arrDataStructure['totalCost'] = array('title'=>ucwords($obj->lang['cost']),'group' => $obj->lang['total'], 'dbfield' => 'totalcost', 'width'=>"60px" ,"format" => 'number', "sortable" => false, "textColor" => 'c95b5f', "style" => 'font-weight:bold', "calculateTotal" => true);


if ($sellingPriceAllowed) { 
    $arrDataStructure['totalRevenue'] = array('title'=>ucwords($obj->lang['revenue']),'group' => $obj->lang['total'], 'dbfield' => 'totalrevenue', 'width'=>"60px" ,"format" => 'number', "sortable" => false, "textColor" => '568203', "style" => 'font-weight:bold', "calculateTotal" => true);
}
// ==================================== ADD PERIOD COLUMN
    
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['ritaseSummaryReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
   
 
//if ($isShowDetail){ 
    // detail ...
    $arrDataDetailStructure = array(); 
 
        $arrDataDetailStructure['indent'] = array('title'=> '',  'dbfield' => '','width' => '270px');  
            
        foreach($arrKeyPeriod as $keyPeriod => $period){ 
            $arrDataDetailStructure['invoicecode'.$keyPeriod] = array('title'=>ucwords($obj->lang['invoice']), 'width' => '108px','dbfield' => 'invoicecode'.$keyPeriod);  
            $arrDataDetailStructure['detailrevenue'.$keyPeriod] = array('title'=>ucwords($obj->lang['revenue']), 'width' => '88px','format' => 'number','align' => 'right', 'dbfield' => 'detailrevenue'.$keyPeriod);
       }



    $arrDetailTemplate = array(); 
    $arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
    $arrDetailTemplate['total'] = array();

    array_push($arrTemplate, $arrDetailTemplate); 
//}

if(isset($_POST) && !empty($_POST['trStartDate'])){
    array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
}


$criteria = ''; 
$movementCriteria = ''; 

//$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $obj->generateComboboxOpt(array('data' => $employee->getAllStatus(),'label' => 'status')); 
$arrCarStatus = $obj->generateComboboxOpt(array('data' => $car->getAllStatus(),'label' => 'status')); 
$arrSupplierStatus = $obj->generateComboboxOpt(array('data' => $supplier->getAllStatus(),'label' => 'status')); 
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' =>' and '.$warehouse->tableName.'.statuskey = 1','order by name asc')); 


$arrType = array();
$arrType[1] = $obj->lang['driver'];
$arrType[2] = $obj->lang['car'];
$arrType[3] = $obj->lang['outsource'];

$arrTwigVar['inputDriverName'] =  $class->inputText('driverName'); 
$arrTwigVar['inputSupplierName'] =  $class->inputText('supplierName');
$arrTwigVar['inputCarCode'] =  $class->inputText('carCode'); 
$arrTwigVar['inputStartDate'] = $class->inputMonth('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputMonth('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputMinTrip'] = $class->inputNumber('minTrip');
$arrTwigVar['inputDriverStatus'] =  $class->inputSelect('selDriverStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputCarStatus'] =  $class->inputSelect('selCarStatus[]', $arrCarStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSupplierStatus'] =  $class->inputSelect('selSupplierStatus[]', $arrSupplierStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelReportType'] =  $class->inputSelect('selReportType', $arrType);  
$arrTwigVar['inputHideNoSales'] =  $class->inputCheckBox('hideNoSales'); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
$arrTwigVar['exportExcel'] = false;


if (isset($_POST) && !empty($_POST['hidAction'])){   
     
     if($_POST['selReportType']==1){ 
            $oderField = $obj->tableEmployee.'.name'; 
            $driverObj = $employee;
         
            $driverNameLabel = $obj->lang['driver'];
            $objDriverName = $employee->tableName.'.name'; 
            $driverNameSearchKey = 'driverName';
          
            $objDriverStatus = $employee->tableName.'.statuskey'; 
            $driverStatusSearchKey = 'selDriverStatus';
         
     }else if($_POST['selReportType']==2){   
           $oderField = $obj->tableCar.'.code'; 
           $driverObj = $car;
         
           $driverNameLabel = $obj->lang['car'];
           $objDriverName = $car->tableName.'.policenumber'; 
           $driverNameSearchKey = 'carCode';
          
           $objDriverStatus = $car->tableName.'.statuskey'; 
           $driverStatusSearchKey = 'selCarStatus';
     }else{
           $oderField = $obj->tableSupplier.'.code'; 
           $driverObj = $supplier;
         
           $driverNameLabel = $obj->lang['supplier'];
           $objDriverName = $supplier->tableName.'.name'; 
           $driverNameSearchKey = 'supplierName';
          
           $objDriverStatus = $supplier->tableName.'.statuskey'; 
           $driverStatusSearchKey = 'selSupplierStatus';
		 
	 }
       
    

    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 

        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   

        $criteria .= ' AND warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');

        $arrTempStatus = array();
        for ($k=0;$k<count($rsCriteria);$k++)
            array_push($arrTempStatus,$rsCriteria[$k]['name']);

        $warehouseName = implode(", ",$arrTempStatus); 
        array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $warehouseName ));

    }
    
    if(isset($_POST) && !empty($_POST[$driverNameSearchKey])) { 
        $criteria .= ' AND '.$objDriverName.' LIKE ('.$class->oDbCon->paramString('%'.$_POST[$driverNameSearchKey].'%').')';
        array_push($arrFilterInformation,array("label" => $driverNameLabel, 'filter' => $_POST[$driverNameSearchKey]));
    } 
     
    if(isset($_POST) && !empty($_POST[$driverStatusSearchKey])) { 

            $key = implode(",", $class->oDbCon->paramString($_POST[$driverStatusSearchKey]));   

            $criteria .= ' AND '.$objDriverStatus.' in('.$key.')';  

            $rsCriteria =  $driverObj->getStatusById($key);

            $arrTempStatus = array();
            for ($k=0;$k<count($rsCriteria);$k++)
                array_push($arrTempStatus,$rsCriteria[$k]['status']);

            $statusName = implode(", ",$arrTempStatus); 
            array_push($arrFilterInformation,array("label" => $obj->lang['status'], 'filter' => $statusName));

    }
    


    $orderBy = (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : $oderField;  
    $orderType = (isset ($_POST) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc'; 
   
	$order = 'order by '.$orderBy.' ' .$orderType; 
 
    $reportType = $_POST['selReportType'];
    if($_POST['selReportType'] == 1) 
        $rsObj = $employee->searchDataRow(array($employee->tableName.'.pkey',$employee->tableName.'.code',$employee->tableName.'.name'),
										  ' and '.$employee->tableName.'.isdriver = 1 '.$criteria,
										  $order);  
    else  if($_POST['selReportType'] == 2) 
        $rsObj = $car->searchDataRow(array($car->tableName.'.pkey',$car->tableName.'.code',$car->tableName.'.policenumber'),
										  $criteria,
										  $order);  
    else
	    $rsObj = $supplier->searchDataRow(array($supplier->tableName.'.pkey',$supplier->tableName.'.code',$supplier->tableName.'.name'),
										  $criteria,
										  $order);  
		 
    // get summary
    $hasDetail = false;
	if( $reportType == 1 || $reportType == 2){ 
        $startPeriod = date('d / m / Y',strtotime($_POST['trStartDate']));
        $endPeriod = date('d / m / Y',strtotime($_POST['trEndDate']));
    	$rs = $obj->getMonthlySummary( $startPeriod, $endPeriod,$movementCriteria,'',$reportType);
        $rsDetailCol = $obj->getMonthlySummaryDetail($startPeriod,$endPeriod,'',$reportType); 
        $rsDetailCol = $obj->reindexDetailCollections($rsDetailCol,'periodindex');
        $hasDetail =true;
    }else{ 
    	$rs = $obj->getOutsourceMonthlySummary( date('d / m / Y',strtotime($_POST['trStartDate'])), date('d / m / Y',strtotime($_POST['trEndDate'])),$movementCriteria,'',$reportType);
    }
    $rsTripSummary = array_column($rs,null,'periodindex');

	$tempreport = '';

    if (empty($rsObj))
         $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
  
    foreach ($rsObj as $row) {  
		 
        $driverkey = $row['pkey'];
        $hasSales = false;
        $totalTrip = 0;
        $totalCost = 0;
        $totalRevenue = 0;
        
        if($_POST['selReportType']==1 || $_POST['selReportType']==3)
            $row['name'] .= ' <span class="text-muted">- ' .  $row['code'] .'</span>';
        else if($_POST['selReportType']==2)
            $row['policenumber'] .= ' <span class="text-muted">- ' .  $row['code'] .'</span>'; 
			
        $tempDetailPeriodStack = array();
        foreach($arrKeyPeriod as $keyPeriod => $period){ 

            $periodIndex= $driverkey.'-'.$keyPeriod; 
             

            $trip = (isset($rsTripSummary[$periodIndex]['totaltrip'])) ? $rsTripSummary[$periodIndex]['totaltrip']: 0;
            if(!empty($trip))
                $hasSales = true; 
            
            $totalTrip += $trip;   
            $trip = (isset($_POST) && !empty($_POST['minTrip']) && $trip <=  $_POST['minTrip']) ? '<div class="bg-green-avocado text-white border-radius-02">'.$obj->formatNumber($trip).'</div>' : $obj->formatNumber($trip);
                       
            $revenue = (isset($rsTripSummary[$periodIndex]['sellingprice'])) ? $rsTripSummary[$periodIndex]['sellingprice']: 0;
            $totalRevenue += $revenue;  
       
            $cost = (isset($rsTripSummary[$periodIndex]['outsourcecost'])) ? $rsTripSummary[$periodIndex]['outsourcecost']: 0;
            $totalCost += $cost;  
       
            $row['totaltrip'.$keyPeriod] = $trip; 
            $row['cost'.$keyPeriod] = $cost; 
            $row['revenue'.$keyPeriod] = $revenue; 
             
            if($hasDetail){
                $tempDetailPeriodStack[$keyPeriod] = array(); 
                $rsDetailSelling = $rsDetailCol[$periodIndex];
                foreach($rsDetailSelling as $sellingRow){  
                    array_push($tempDetailPeriodStack[$keyPeriod],array('sellingprice' =>  $sellingRow['sellingprice'], 'invoicecode' => $sellingRow['invoicecode']));
                }    
            }
            
        }
        
          
        if($hideNoSales && !$hasSales) continue;
        
        $row['totaltrip'] = $totalTrip; 
        $row['totalcost'] = $totalCost; 
        $row['totalrevenue'] = $totalRevenue; 
        
        // has detail
        
         if($hasDetail){
            // rearrange detail
            //cari index yg paling byk utk patokan berapa baris
            $totalIndexRow = 0;
            foreach($tempDetailPeriodStack as $stackRow)
                if ( count($stackRow) > $totalIndexRow) $totalIndexRow = count($stackRow);


            $rsDetail = array();
            for($stackCtr=0;$stackCtr<$totalIndexRow;$stackCtr++){

                foreach($arrKeyPeriod as $keyPeriod => $period){ 
                    if(!isset($tempDetailPeriodStack[$keyPeriod][$stackCtr])) continue;

                    $rsDetail[$stackCtr]['invoicecode'.$keyPeriod] = $tempDetailPeriodStack[$keyPeriod][$stackCtr]['invoicecode'];
                    $rsDetail[$stackCtr]['detailrevenue'.$keyPeriod] = $tempDetailPeriodStack[$keyPeriod][$stackCtr]['sellingprice'];
                }

            }

            $row['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail);
         }
        
        $return = $obj->formatReportRows(array('data' => $row),$arrTemplate); 
            
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html']; 
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
             
    }  
    
    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
} 


echo $twig->render('reportRitaseSummary.html', $arrTwigVar);   
?>
