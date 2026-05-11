<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';
 
includeClass(array('TruckingServiceOrder.class.php'));
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol(new Warehouse());

include '_global.php';

$obj = $truckingServiceOrder;
$securityObject = 'reportTruckingServiceOrder'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$arrFilterInformation = array();  
 
$_POST['selStatus[]'] = array(2,3,4,5,6);
$_POST['selCustomerStatus[]'] = array(2);
if(!isset($_POST['showServices'])) $_POST['showServices'] = 0;
if(!isset($_POST['hideNoSales'])) $_POST['hideNoSales'] = 1;

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$showServices = (isset($_POST['showServices']) && $_POST['showServices'] == 1) ? true : false;
$hideNoSales = (isset($_POST['hideNoSales']) && $_POST['hideNoSales'] == 1) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);  
$arrDataStructure['customerName'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'name', 'width'=>"280px" );
 
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
    $arrDataStructure['grossprofit'.$keyPeriod] = array('title'=>ucwords($obj->lang['grossProfit']),'group' => $period['label'],'dbfield' => 'grossprofit'.$keyPeriod, 'width'=>"90px","format" => 'number', "sortable" => false, "textColor" => '568203', 'calculateTotal' => true);
   
    if($showServices)
        $arrDataStructure['service'.$keyPeriod] = array('title'=>ucwords($obj->lang['service']),'group' => $period['label'],'dbfield' => 'service'.$keyPeriod, 'width'=>"90px",  "sortable" => false, "textColor" => '0093AF');
}

$arrDataStructure['totalSales'] = array('title'=>ucwords($obj->lang['sales']),'group' => $obj->lang['total'], 'dbfield' => 'totalsales', 'width'=>"90px" , "format" => 'number', "sortable" => false, "style" => 'font-weight:bold', 'calculateTotal' => true);
$arrDataStructure['totalGrossProfit'] = array('title'=>ucwords($obj->lang['grossProfit']),'group' => $obj->lang['total'], 'dbfield' => 'totalgrossprofit', 'width'=>"90px" ,"format" => 'number', "sortable" => false, "textColor" => '568203', "style" => 'font-weight:bold', 'calculateTotal' => true);
     
if($showServices)
    $arrDataStructure['totalService'] = array('title'=>ucwords($obj->lang['service']),'group' => $obj->lang['total'], 'dbfield' => 'totalservice', 'width'=>"90px" , "sortable" => false, "textColor" => '0093AF', "style" => 'font-weight:bold');

// ==================================== ADD PERIOD COLUMN
    
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['monthlySalesReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
   
if(isset($_POST) && !empty($_POST['trStartDate'])){
    array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
}

$customerCriteria = '';
$movementCriteria = '';
 
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrCustomerStatus = $class->convertForCombobox($customer->getAllStatus(),'pkey','status');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName . '.statuskey', 1, true, '', 'order by name asc'), 'pkey', 'name');
$arrTwigVar['inputCustomerName'] =  $class->inputText('customerName'); 
$arrTwigVar['inputStartDate'] = $class->inputMonth('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputMonth('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputCustomerStatus'] =  $class->inputSelect('selCustomerStatus[]', $arrCustomerStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputShowServices'] =  $class->inputCheckBox('showServices'); 
$arrTwigVar['inputHideNoSales'] =  $class->inputCheckBox('hideNoSales'); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
$arrTwigVar['exportExcel'] = false; 


if (isset($_POST) && !empty($_POST['hidAction'])){   
     
    if(isset($_POST) && !empty($_POST['customerName'])) { 
        $customerCriteria .= ' AND '.$customer->tableName.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['customerName'].'%').')';
	    array_push($arrFilterInformation,array("label" => $obj->lang['customer'], 'filter' => $_POST['customerName']));
	}
    
 	 
    if(isset($_POST) && !empty($_POST['selCustomerStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCustomerStatus']));   
        
       	$customerCriteria .= ' AND '.$customer->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $customer->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status Pelanggan', 'filter' => $statusName));
        
	}

    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));

        $movementCriteria .= ' AND '.$obj->tableName.'.warehousekey in ('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		    array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $warehouseName ));
        
	}
 	 
    
    if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$movementCriteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status Transaksi', 'filter' => $statusName));
        
	}
    
    $orderBy = (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'name';  
    $orderType = (isset ($_POST) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc'; 
 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	  
    $rsCustomer = $customer->searchData('','',true,$customerCriteria,$order); 
     
    // get summary
    $rs = $obj->getMonthlySalesSummary(date('d / m / Y',strtotime($_POST['trStartDate'])), date('d / m / Y',strtotime($_POST['trEndDate'])),$movementCriteria);
    $rsSalesSummary = array_column($rs,null,'periodindex');
    
    if($showServices)
        $rsQty = $obj->getMonthlyQtySummary(date('d / m / Y',strtotime($_POST['trStartDate'])), date('d / m / Y',strtotime($_POST['trEndDate'])),$movementCriteria);

  
	$tempreport = '';

    if (empty($rsCustomer))
         $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
  
    
    foreach ($rsCustomer as $customerRow) {  
		 
        $customerkey = $customerRow['pkey'];
        $hasSales = false;
        $totalSales = 0;
        $totalGrossProfit = 0;  
        $arrItemSum = array(); 
        $rsItem = array();
        
        foreach($arrKeyPeriod as $keyPeriod => $period){ 

            $periodIndex= $customerkey.'-'.$keyPeriod; 
             
            $amount = (isset($rsSalesSummary[$periodIndex]['grandtotal'])) ? $rsSalesSummary[$periodIndex]['grandtotal']: 0;
            $totalSales += $amount;  
                  
            $grossProfit = (isset($rsSalesSummary[$periodIndex]['grossprofit'])) ? $rsSalesSummary[$periodIndex]['grossprofit']: 0;
            $totalGrossProfit += $grossProfit;  
             
            if(!empty($amount) || !empty($grossProfit))
                $hasSales = true;
            
            //$obj->setLog($amount);
            $customerRow['grandtotal'.$keyPeriod] = $amount; 
            $customerRow['grossprofit'.$keyPeriod] = $grossProfit; 
            
            $filterBy['customerkey'] = $customerkey;
            $filterBy['periodIndex'] = $periodIndex;
            
            
            if($showServices){ 
                $rsItem  = array_filter($rsQty, function ($var) use ($filterBy) { 
                    return ($var['customerkey'] == $filterBy['customerkey'] && $var['periodindex'] == $filterBy['periodIndex']);
                });
            }

            
            if($showServices){
                    $itemDesc = '';
                    foreach ($rsItem as $item){ 
                        $itemkey = $item['itemkey'];

                        if(!isset($arrItemSum[$itemkey])){
                            $arrItemSum[$itemkey]['qty'] = 0; 
                            $arrItemSum[$itemkey]['name'] = $item['itemname']; 
                        }

                        $arrItemSum[$itemkey]['qty'] += $item['total'];

                        $itemDesc .= '<div class="item-desc"><div class="flex"><div class="name consume">'.$item['itemname'].'</div> <div class="colon">:</div> <div class="value">'.$obj->formatNumber($item['total']).'</div></div></div>';  
                    }  

                    $customerRow['service'.$keyPeriod] = $itemDesc;
            }
            
 
        }
        
        if($hideNoSales && !$hasSales)
            continue;
        
        $customerRow['totalsales'] = $totalSales; 
        $customerRow['totalgrossprofit'] = $totalGrossProfit; 
         
        
        if($showServices){
            $itemDesc = '';
            foreach ($arrItemSum as $item){    
                $itemDesc .= '<div class="item-desc"><div class="flex"><div class="name consume">'.$item['name'].'</div> <div class="colon">:</div> <div class="value">'.$obj->formatNumber($item['qty']).'</div></div></div>';  
            }  
            $customerRow['totalservice'] = $itemDesc; 
        }
        
        $return = $obj->formatReportRows(array('data' => $customerRow),$arrTemplate); 
            
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html']; 
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
             
    }  
    
    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
} 


echo $twig->render('reportMonthlySales.html', $arrTwigVar);   
?>
