<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('CarTurnover.class.php','Car.class.php','Item.class.php','CarCategory.class.php','TruckingServiceWorkOrder.class.php','TruckingServiceOrder.class.php','CarServiceMaintenance.class.php'));
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());
$carServiceMaintenance = createObjAndAddToCol(new CarServiceMaintenance());
$carCategory = createObjAndAddToCol(new CarCategory());
$car = createObjAndAddToCol(new Car());

include '_global.php';

$obj = $truckingServiceOrder;
$securityObject = 'reportCarTurnOver'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class

if(!$security->isAdminLogin($securityObject,10,true));
 
$arrFilterInformation = array();  
 
$_POST['selStatus[]'] = array(2,3,4,5,6);
if(!isset($_POST['hideNoSales'])) $_POST['hideNoSales'] = 1;

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$hideNoSales = (isset($_POST['hideNoSales']) && $_POST['hideNoSales'] == 1) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);  
$arrDataStructure['policeNumber'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),  'width'=>"280px", 'dbfield' => 'policenumber'); 
 
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
    $arrDataStructure['sales'.$keyPeriod] = array('title'=>ucwords($obj->lang['sales']),'group' => $period['label'], 'dbfield' => 'sales'.$keyPeriod, 'width'=>"90px" ,"format" => 'integer', "sortable" => false, 'calculateTotal' => true);
    $arrDataStructure['cost'.$keyPeriod] = array('title'=>ucwords($obj->lang['cost']),'group' => $period['label'], 'dbfield' => 'cost'.$keyPeriod, 'width'=>"90px" ,"format" => 'integer', "sortable" => false, 'calculateTotal' => true);
    $arrDataStructure['balance'.$keyPeriod] = array('title'=>ucwords($obj->lang['balance']),'group' => $period['label'], 'dbfield' => 'balance'.$keyPeriod, 'width'=>"90px" ,"format" => 'integer', "sortable" => false, 'calculateTotal' => true);
}

$arrDataStructure['totalSales'] = array('title'=>ucwords($obj->lang['sales']),'group' => $obj->lang['total'], 'dbfield' => 'totalsales', 'width'=>"90px" , "format" => 'number', "sortable" => false, "style" => 'font-weight:bold', 'calculateTotal' => true);
$arrDataStructure['totalCost'] = array('title'=>ucwords($obj->lang['cost']),'group' => $obj->lang['total'], 'dbfield' => 'totalcost', 'width'=>"90px" ,"format" => 'number', "sortable" => false, "style" => 'font-weight:bold', 'calculateTotal' => true);
$arrDataStructure['totalBalance'] = array('title'=>ucwords($obj->lang['balance']),'group' => $obj->lang['total'], 'dbfield' => 'totalbalance', 'width'=>"90px" ,"format" => 'number', "sortable" => false,  "style" => 'font-weight:bold', 'calculateTotal' => true);
     

// ==================================== ADD PERIOD COLUMN
    
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['carSummaryTurnoverReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
   
if(isset($_POST) && !empty($_POST['trStartDate'])){
    array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
}

$carCriteria = ''; 
$movementCriteria = '';
 
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrPoliceNumber = $class->convertForCombobox($car->searchData($car->tableName.'.statuskey',1,true, ' and '.$car->tableName.'.statuskey = 1', ' order by policenumber asc'),'pkey','policenumber'); 
$arrCarCategory = $class->convertForCombobox($carCategory->searchData($carCategory->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputStartDate'] = $class->inputMonth('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputMonth('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputPoliceNumber'] =  $class->inputSelect('selPoliceNumber[]', $arrPoliceNumber, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCarCategory'] =  $class->inputSelect('selCarCategory[]', $arrCarCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputHideNoSales'] =  $class->inputCheckBox('hideNoSales'); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
$arrTwigVar['exportExcel'] = false; 


if (isset($_POST) && !empty($_POST['hidAction'])){   
    $SPKcriteria = ' and '.$truckingServiceWorkOrder->tableName.'.trdate'.' between '.$class->oDbCon->paramString(date('Y-m-d',strtotime($_POST['trStartDate']))).' AND '.$class->oDbCon->paramString(date('Y-m-t 23:59',strtotime($_POST['trEndDate'])));
    $serviceCriteria = ' and '.$carServiceMaintenance->tableName.'.trdate'.' between '.$class->oDbCon->paramString(date('Y-m-d',strtotime($_POST['trStartDate']))).' AND '.$class->oDbCon->paramString(date('Y-m-t 23:59',strtotime($_POST['trEndDate'])));
    // $serviceCriteria =' and '. $carServiceMaintenance->tableName.'.statuskey in (2,3)';
     
    if(isset($_POST) && !empty($_POST['selPoliceNumber'])) { 

        $key = implode(",", $class->oDbCon->paramString($_POST['selPoliceNumber']));   

        $carCriteria .= ' AND '.$car->tableName.'.pkey in('.$key.')';  
        $rsCriteria = $car->searchData('','',true, ' and '.$car->tableName.'.pkey in ('.$key.')');
        $arrTempNumber = array();
        for ($k=0;$k<count($rsCriteria);$k++)
            array_push($arrTempNumber,$rsCriteria[$k]['policenumber']);

        $policeNumber = implode(", ",$arrTempNumber); 
        array_push($arrFilterInformation,array("label" => 'No. Polisi', 'filter' => $policeNumber));
	} 

    if(isset($_POST) && !empty($_POST['selCarCategory'])) { 

        $key = implode(",", $class->oDbCon->paramString($_POST['selCarCategory']));   

        $carCriteria .= ' AND '.$car->tableName.'.categorykey in ('.$key.')';  
        $rsCriteria = $carCategory->searchData('','',true, ' and '.$carCategory->tableName.'.pkey in ('.$key.')');
        $arrTempNumber = array();
        for ($k=0;$k<count($rsCriteria);$k++)
            array_push($arrTempNumber,$rsCriteria[$k]['name']);

        $category = implode(", ",$arrTempNumber); 
        array_push($arrFilterInformation,array("label" => 'Kategori', 'filter' => $category));
	} 
    
    $orderBy = (isset ($_POST) && !empty($_POST['hidOrderBy']) ) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'name';  
    $orderType = (isset ($_POST) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc'; 
 
	$order = 'order by '.$orderBy.' ' .$orderType; 

    $rsCar = $car->searchData($car->tableName.'.statuskey','1',true,$carCriteria);
	$arrCarKeys = array_column($rsCar,'pkey');

    $rsSPKCol = $truckingServiceWorkOrder->searchData('','',true,$SPKcriteria.' and '. $truckingServiceWorkOrder->tableName.'.statuskey in (2,3) and carkey in (' .$class->oDbCon->paramString($arrCarKeys,',').') order by '.$truckingServiceWorkOrder->tableName.'.trdate'.'  asc');
    $arrDetailSPKCol = $obj->reindexDetailCollections($rsSPKCol,'carkey');   
    
    $arrSPKKeys = array_column($rsSPKCol,'pkey');
    $arrJOKeys = array_column($rsSPKCol,'refkey');
    
    $rsSPKCost = $truckingServiceWorkOrder->getCostDetail($arrSPKKeys);   
    $rsSPKCost = $obj->reindexDetailCollections($rsSPKCost,'refkey');   
    
    $rsJOCol = $truckingServiceOrder->searchData('','',true,' and '.$truckingServiceOrder->tableName.'.pkey'.' in (' .$class->oDbCon->paramString($arrJOKeys,',').') order by '.$truckingServiceOrder->tableName.'.trdate  asc');
    $arrJOCol = $obj->reindexDetailCollections($rsJOCol,'pkey');   
    
    $rsJOTruckingCost = $truckingServiceOrder->getDetailWithRelatedInformation($arrJOKeys);   
    $arrJOTruckingCostCol = $obj->reindexDetailCollections($rsJOTruckingCost,'refkey');   
    
    $rsJOSellingCostDetail = $truckingServiceOrder->getSellingCostDetail($arrJOKeys);   
    $rsJOSellingCostDetailCol = $obj->reindexDetailCollections($rsJOSellingCostDetail,'refkey');   

    $rsJOCostDetail = $truckingServiceOrder->getHeaderCost($arrJOKeys);   
    $rsJOCostDetailCol = $obj->reindexDetailCollections($rsJOCostDetail,'refkey');   
    
    $rsServiceCol = $carServiceMaintenance->searchData('','',true,$serviceCriteria.' and '. $carServiceMaintenance->tableName.'.statuskey in (2,3) and '.$carServiceMaintenance->tableName.'.carkey in (' .$class->oDbCon->paramString($arrCarKeys,',').') order by '.$carServiceMaintenance->tableName.'.trdate  asc');

    $arrServiceKeys = array_column($rsServiceCol,'pkey');
    $rsServiceCol = $obj->reindexDetailCollections($rsServiceCol,'carkey');   
    
    $rsServiceDetailCol = $carServiceMaintenance->getDetailWithRelatedInformation($arrServiceKeys);   
    $rsServiceDetailCol = $obj->reindexDetailCollections($rsServiceDetailCol,'refkey'); 
    
    $arrDetailCol = array();
    for($j=0;$j<count($rsCar);$j++){ 

        $carKey = $rsCar[$j]['pkey'];
        $rsService = $rsServiceCol[$carKey] ?? [];

        $rsSPKDetail = $arrDetailSPKCol[$rsCar[$j]['pkey']] ?? [];
        $arrDetailCol[$carKey] = array();
        for($i=0;$i<count($rsSPKDetail);$i++) { 

            $SPKKey = $rsSPKDetail[$i]['pkey'];
            $JOKey = $rsSPKDetail[$i]['refkey'];
            $arrJO = $arrJOCol[$JOKey];
            
            if($rsSPKDetail[$i]['drivercommission'] > 0) {
                array_push($arrDetailCol[$carKey], array(
                    'trdate' => $rsSPKDetail[$i]['trdate'],
                    'jodate' => $rsSPKDetail[$i]['serviceorderdate'],
                    'index' => date("nY", strtotime($rsSPKDetail[$i]['trdate'])),
                    'revenueamount' => 0,
                    'costamount' => $rsSPKDetail[$i]['drivercommission'],
                    'balanceamount' => 0,
                    'refcode' => $rsSPKDetail[$i]['code'],
                    'trdesc' => $obj->lang['driverCommission']
                ));
            }

            if($rsSPKDetail[$i]['codrivercommission'] > 0) {
                array_push($arrDetailCol[$carKey], array(
                    'trdate' => $rsSPKDetail[$i]['trdate'],
                    'jodate' => $rsSPKDetail[$i]['serviceorderdate'],
                    'index' => date("nY", strtotime($rsSPKDetail[$i]['trdate'])),
                    'revenueamount' => 0,
                    'costamount' => $rsSPKDetail[$i]['codrivercommission'],
                    'balanceamount' => 0,
                    'refcode' => $rsSPKDetail[$i]['code'],
                    'trdesc' => $obj->lang['codriverCommission']
                ));
            }
            
            // data biaya dari cost SPK
            $arrSPKCost = $rsSPKCost[$SPKKey] ?? [];
            for($k=0;$k<count($arrSPKCost);$k++) { 
                $SPKCost = array();
                $SPKCost['trdate'] = $rsSPKDetail[$i]['trdate'];
                $SPKCost['jodate'] = $rsSPKDetail[$i]['serviceorderdate'];
                $SPKCost['index'] = date("nY", strtotime($rsSPKDetail[$i]['trdate']));
                $SPKCost['revenueamount'] = 0;
                $SPKCost['costamount'] = $arrSPKCost[$k]['total'];
                $SPKCost['balanceamount'] = 0;
                $SPKCost['refcode'] = $rsSPKDetail[$i]['code'];
                $SPKCost['trdesc'] = $arrSPKCost[$k]['name'];
                array_push($arrDetailCol[$carKey], $SPKCost);
            }

            
            // selling data dari JO trucking
            $arrJOTruckingCost = $arrJOTruckingCostCol[$JOKey] ?? [];
            for($k=0;$k<count($arrJOTruckingCost);$k++) { 
                $SPKCost = array();
                $SPKCost['trdate'] = $rsSPKDetail[$i]['trdate'];
                $SPKCost['jodate'] = $arrJO[0]['trdate'];
                $SPKCost['index'] = date("nY", strtotime($rsSPKDetail[$i]['trdate']));
                $SPKCost['revenueamount'] = $arrJOTruckingCost[$k]['total'];
                $SPKCost['costamount'] = 0;
                $SPKCost['balanceamount'] = 0;
                $SPKCost['refcode'] = $arrJO[0]['code'];
                $SPKCost['trdesc'] = $arrJOTruckingCost[$k]['itemname'];
                array_push($arrDetailCol[$carKey], $SPKCost);
            }

            // selling data dari JO service
            $rsJOSellingCostDetail = $rsJOSellingCostDetailCol[$JOKey] ?? [];
            for($k=0;$k<count($rsJOSellingCostDetail);$k++) { 
                $JOSellingCostDetail = array();
                $JOSellingCostDetail['trdate'] = $rsSPKDetail[$i]['trdate'];
                $JOSellingCostDetail['jodate'] = $arrJO[0]['trdate'];
                $JOSellingCostDetail['index'] = date("nY", strtotime($rsSPKDetail[$i]['trdate']));
                $JOSellingCostDetail['revenueamount'] = $rsJOSellingCostDetail[$k]['subtotal'];
                $JOSellingCostDetail['costamount'] = 0;
                $JOSellingCostDetail['balanceamount'] = 0;
                $JOSellingCostDetail['refcode'] = $arrJO[0]['code'];
                $JOSellingCostDetail['trdesc'] = $rsJOSellingCostDetail[$k]['itemname'];
                array_push($arrDetailCol[$carKey], $JOSellingCostDetail);
            }

            // data biaya dari JO 
            $rsJOCostDetail = $rsJOCostDetailCol[$JOKey] ?? [];
            for($k=0;$k<count($rsJOCostDetail);$k++) { 
                $JOCostDetail = array();
                $JOCostDetail['trdate'] = $rsSPKDetail[$i]['trdate'];
                $JOCostDetail['jodate'] = $arrJO[0]['trdate'];
                $JOCostDetail['index'] = date("nY", strtotime($rsSPKDetail[$i]['trdate']));
                $JOCostDetail['revenueamount'] = $rsJOCostDetail[$k]['subtotal'];
                $JOCostDetail['costamount'] = 0;
                $JOCostDetail['balanceamount'] = 0;
                $JOCostDetail['refcode'] = $arrJO[0]['code'];
                $JOCostDetail['trdesc'] = $rsJOCostDetail[$k]['itemname'];
                array_push($arrDetailCol[$carKey], $JOCostDetail);
            }
        }


        // data biaya dari Maintenance
        for($i=0;$i<count($rsService);$i++) { 
            $serviceKey = $rsService[$i]['pkey'];
            $rsServiceDetail = $rsServiceDetailCol[$serviceKey];
            for($k=0;$k<count($rsServiceDetail);$k++) { 
                $SPKCost = array();
                $SPKCost['trdate'] = $rsService[$i]['trdate'];
                $SPKCost['jodate'] = $rsService[$i]['trdate'];
                $SPKCost['index'] = date("nY", strtotime($rsService[$i]['trdate']));
                $SPKCost['revenueamount'] = 0;
                $SPKCost['costamount'] = $rsServiceDetail[$k]['total'];
                $SPKCost['balanceamount'] = 0;
                $SPKCost['refcode'] = $rsService[$i]['code'];
                $SPKCost['trdesc'] = $rsServiceDetail[$k]['itemname'];
                array_push($arrDetailCol[$carKey], $SPKCost);
            }
        }

    }
    
     
    // get summary
    // $rs = $obj->getMonthlySalesSummary(date('d / m / Y',strtotime($_POST['trStartDate'])), date('d / m / Y',strtotime($_POST['trEndDate'])),$movementCriteria);
    // $rsSalesSummary = array_column($rs,null,'periodindex');

  
	$tempreport = '';

    if (empty($rsCar))
         $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
  
    
    $j=0;
    
    foreach ($rsCar as $carRow) {  
	   $arrHeaderStyle = array();	 
        
        $carKey = $carRow['pkey'];
        $rsDetail = $arrDetailCol[$carKey];
        $rsDetail = $obj->reindexDetailCollections($rsDetail,'index'); 
        
        $hasSales = false;
        $totalSales = 0;  
        $totalCost = 0;  
        $totalBalance = 0;  
        $totalGrossProfit = 0;  
        $arrItemSum = array(); 
        $rsItem = array();
       
        foreach($arrKeyPeriod as $keyPeriod => $period){ 

            $periodIndex= $carKey.'-'.$keyPeriod; 
            $detail = $rsDetail[$keyPeriod];
            $sales = 0;
            $cost = 0;
            $balance = 0;
             
            // $amount = (isset($rsSalesSummary[$periodIndex]['grandtotal'])) ? $rsSalesSummary[$periodIndex]['grandtotal']: 0;
            // $totalSales += $amount;  
            foreach($detail as $data){ 
                if($data['revenueamount'] > 0){
                    $sales += $data['revenueamount'];
                    $totalSales += $data['revenueamount'];
                }
                if($data['costamount'] > 0){
                    $cost += $data['costamount'];
                    $totalCost += $data['costamount'];
                }   
            }
            
            // $grossProfit = (isset($rsSalesSummary[$periodIndex]['grossprofit'])) ? $rsSalesSummary[$periodIndex]['grossprofit']: 0;
            // $totalGrossProfit += $grossProfit;  
            
            
            $balance = $sales - $cost;
            $totalBalance += $balance;
            // $carRow['grandtotal'.$keyPeriod] = $amount; 
            // $carRow['grossprofit'.$keyPeriod] = $grossProfit; 
            $carRow['sales'.$keyPeriod] = $sales; 
            $carRow['cost'.$keyPeriod] = $cost; 
            $carRow['balance'.$keyPeriod] = $balance; 
                 
            $arrHeaderStyle['balance'.$keyPeriod]['textColor'] =  ($balance == 0) ? "333333" : (($balance < 0) ? "C41E3A" : "568203");;
        }
        
        if(!empty($totalBalance) || !empty($totalBalance)) $hasSales = true;
        if($hideNoSales && !$hasSales) continue;
        
        $carRow['totalsales'] = $totalSales; 
        $carRow['totalcost'] = $totalCost; 
        $carRow['totalbalance'] = $totalSales - $totalCost; 
         
        $arrHeaderStyle['totalbalance']['textColor'] = ($carRow['totalbalance'] == 0) ? "333333" : (($carRow['totalbalance'] < 0) ? "C41E3A" : "568203");
        
        //$carRow['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsCar,'style'=> $arrDetailStyle);
        
        $return = $obj->formatReportRows(array('data' => $carRow, 'style' => $arrHeaderStyle),$arrTemplate); 
            
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html']; 
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
             
        $j++;
    }  
    
    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);
} 


echo $twig->render('reportCarSummaryTurnover.html', $arrTwigVar);   
?>