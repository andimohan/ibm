<?php
	 
includeClass(array('CarTurnover.class.php','Car.class.php','Item.class.php','CarCategory.class.php','TruckingServiceWorkOrder.class.php','TruckingServiceOrder.class.php','CarServiceMaintenance.class.php'));

$carTurnover = createObjAndAddToCol(new CarTurnover()); 
$carCategory = createObjAndAddToCol(new CarCategory());
$car = createObjAndAddToCol(new Car());
$warehouse = createObjAndAddToCol(new Warehouse());
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$carServiceMaintenance = createObjAndAddToCol(new CarServiceMaintenance());

$obj= $carTurnover;

$securityObject = 'reportTruckingServiceWorkOrder'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
 
$arrFilterInformation = array();  
 
// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code');  
$arrDataStructure['policeNumber'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),  'width'=>"150px", 'dbfield' => 'policenumber'); 
$arrDataStructure['carCategory'] = array('title'=>ucwords($obj->lang['carCategory']),  'width'=>"150px", 'dbfield' => 'categoryname'); 
/*$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");*/
$arrDataStructure['totalRevenue'] = array('title'=>ucwords($obj->lang['revenue']), 'dbfield' => 'totalRevenue', 'width'=>"110px" ,'format'=>'number', "sortable" => false, 'textColor' => '568203', 'calculateTotal' => true);
$arrDataStructure['totalCost'] = array('title'=>ucwords($obj->lang['cost']), 'dbfield' => 'totalCost', 'width'=>"110px" ,'format'=>'number', "sortable" => false,  'textColor' => 'C41E3A', 'calculateTotal' => true);
$arrDataStructure['totalBalance'] = array('title'=>ucwords($obj->lang['balance']), 'dbfield' => 'totalBalance', 'width'=>"110px" ,'format'=>'number', "sortable" => false , 'calculateTotal' => true);
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['carTurnoverReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

// detail ...
$arrDataDetailStructure = array();  
$arrDataDetailStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');  
$arrDataDetailStructure['jodate'] = array('title'=>ucwords($obj->lang['jobOrderDate']),'dbfield' => 'jodate', 'width'=>"90px",'format'=>'date');  
$arrDataDetailStructure['revenue'] = array('title'=>ucwords($obj->lang['revenue']),'dbfield' => 'revenueamount', 'width'=>"90px",'format'=>'number', 'textColor' => '568203'); 
$arrDataDetailStructure['cost'] = array('title'=>ucwords($obj->lang['cost']),'dbfield' => 'costamount', 'width'=>"90px",'format'=>'number', 'textColor' => 'C41E3A'); 
$arrDataDetailStructure['balance'] = array('title'=>ucwords($obj->lang['balance']),'dbfield' => 'balanceamount', 'width'=>"90px",'format'=>'number', 'textColor' => '568203');
$arrDataDetailStructure['reference1'] = array('title'=>ucwords($obj->lang['reference']),'dbfield' => 'refcode', 'width'=>"120px" ); 
$arrDataDetailStructure['description'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'trdesc', 'width'=>"250px" ); 

$arrDetailTemplate = array(); 
$arrDetailTemplate['dataStructure'] = $arrDataDetailStructure;
$arrDetailTemplate['total'] = array();

array_push($arrTemplate, $arrDetailTemplate); 
	
$arrDateType= array(
    '1' => $obj->lang['transactionDate'],
    '2' => $obj->lang['jobOrderDate'], 
);

if (isset($_POST) && !empty($_POST['hidAction'])){   
	
	$criteria =' and '. $obj->tableName.'.statuskey = 1';
	$SPKcriteria =' and '. $truckingServiceWorkOrder->tableName.'.statuskey in (2,3)';
	$serviceCriteria =' and '. $carServiceMaintenance->tableName.'.statuskey in (2,3)';
	$carCriteria = ''; 
	$orderByDetail = $obj->tableName.'.trdate';
	$orderByDetailSPK = $truckingServiceWorkOrder->tableName.'.trdate';
	$orderByDetailService = $carServiceMaintenance->tableName.'.trdate';
    
    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$carCriteria .= ' AND '.$car->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $statusName ));
        
	}
	
//	if(isset($_POST) && !empty($_POST['trStartDate'])){
//		//$criteria .= ' and trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
//		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
//	} 
	
	if(isset($_POST) && !empty($_POST['trStartDate'])){
        $fieldName = $obj->tableName.'.trdate'; 
        $SPKfieldName = $truckingServiceWorkOrder->tableName.'.trdate'; 
        $orderByDetailSPK = $truckingServiceWorkOrder->tableName.'.trdate'; 
        $orderByDetail = $obj->tableName.'.trdate';
        $orderByDetailService = $carServiceMaintenance->tableName.'.trdate';
        
		$criteria .= ' and '.$fieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59');
		$SPKcriteria .= ' and '.$SPKfieldName.' between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ','Y-m-d 23:59');
		array_push($arrFilterInformation,array("label" => $arrDateType[$_POST['selDateType']], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
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
    
		 
	$orderBy = 'policenumber'; 
	$orderType = 'asc'; 
	 
	$order = 'order by '.$orderBy.' ' .$orderType; 
	 
	$rsCar = $car->searchData($car->tableName.'.statuskey','1',true,$carCriteria,$order);
	$arrCarKeys = array_column($rsCar,'pkey');
	
    $rsSPKCol = $truckingServiceWorkOrder->searchData('','',true,$SPKcriteria . ' and carkey in (' .$class->oDbCon->paramString($arrCarKeys,',').') order by '.$orderByDetailSPK.'  asc');
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
    
    
    $rsServiceCol = $carServiceMaintenance->searchData('','',true,$serviceCriteria . ' and '.$carServiceMaintenance->tableName.'.carkey in (' .$class->oDbCon->paramString($arrCarKeys,',').') order by '.$carServiceMaintenance->tableName.'.trdate  asc');

    $arrServiceKeys = array_column($rsServiceCol,'pkey');
    $rsServiceCol = $obj->reindexDetailCollections($rsServiceCol,'carkey');   
    
    $rsServiceDetailCol = $carServiceMaintenance->getDetailWithRelatedInformation($arrServiceKeys);   
    $rsServiceDetailCol = $obj->reindexDetailCollections($rsServiceDetailCol,'refkey');   

    $arrDetailCol = array();
    for($j=0;$j<count($rsCar);$j++){ 

        $carKey = $rsCar[$j]['pkey'];
        $rsService = $rsServiceCol[$carKey];

        $rsSPKDetail = $arrDetailSPKCol[$rsCar[$j]['pkey']];
        $arrDetailCol[$carKey] = array();
        for($i=0;$i<count($rsSPKDetail);$i++) { 

            $SPKKey = $rsSPKDetail[$i]['pkey'];
            $JOKey = $rsSPKDetail[$i]['refkey'];
            $arrJO = $arrJOCol[$JOKey];
            
            if($rsSPKDetail[$i]['drivercommission'] > 0) {
                array_push($arrDetailCol[$carKey], array(
                    'trdate' => $rsSPKDetail[$i]['trdate'],
                    'jodate' => $rsSPKDetail[$i]['serviceorderdate'],
                    'revenueamount' => 0,
                    'costamount' => -$rsSPKDetail[$i]['drivercommission'],
                    'balanceamount' => 0,
                    'refcode' => $rsSPKDetail[$i]['code'],
                    'trdesc' => $obj->lang['driverCommission']
                ));
            }

            if($rsSPKDetail[$i]['codrivercommission'] > 0) {
                array_push($arrDetailCol[$carKey], array(
                    'trdate' => $rsSPKDetail[$i]['trdate'],
                    'jodate' => $rsSPKDetail[$i]['serviceorderdate'],
                    'revenueamount' => 0,
                    'costamount' => -$rsSPKDetail[$i]['codrivercommission'],
                    'balanceamount' => 0,
                    'refcode' => $rsSPKDetail[$i]['code'],
                    'trdesc' => $obj->lang['codriverCommission']
                ));
            }
            
            // data biaya dari cost SPK
            $arrSPKCost = $rsSPKCost[$SPKKey];
            for($k=0;$k<count($arrSPKCost);$k++) { 
                $SPKCost = array();
                $SPKCost['trdate'] = $rsSPKDetail[$i]['trdate'];
                $SPKCost['jodate'] = $rsSPKDetail[$i]['serviceorderdate'];
                $SPKCost['revenueamount'] = 0;
                $SPKCost['costamount'] = -$arrSPKCost[$k]['total'];
                $SPKCost['balanceamount'] = 0;
                $SPKCost['refcode'] = $rsSPKDetail[$i]['code'];
                $SPKCost['trdesc'] = $arrSPKCost[$k]['name'];
                array_push($arrDetailCol[$carKey], $SPKCost);
            }

            
            // selling data dari JO trucking
            $arrJOTruckingCost = $arrJOTruckingCostCol[$JOKey];
            for($k=0;$k<count($arrJOTruckingCost);$k++) { 
                $SPKCost = array();
                $SPKCost['trdate'] = $arrJO[0]['trdate'];
                $SPKCost['jodate'] = $arrJO[0]['trdate'];
                $SPKCost['revenueamount'] = $arrJOTruckingCost[$k]['total'];
                $SPKCost['costamount'] = 0;
                $SPKCost['balanceamount'] = 0;
                $SPKCost['refcode'] = $arrJO[0]['code'];
                $SPKCost['trdesc'] = $arrJOTruckingCost[$k]['itemname'];
                array_push($arrDetailCol[$carKey], $SPKCost);
            }

            // selling data dari JO service
            $rsJOSellingCostDetail = $rsJOSellingCostDetailCol[$JOKey];
            for($k=0;$k<count($rsJOSellingCostDetail);$k++) { 
                $JOSellingCostDetail = array();
                $JOSellingCostDetail['trdate'] = $arrJO[0]['trdate'];
                $JOSellingCostDetail['jodate'] = $arrJO[0]['trdate'];
                $JOSellingCostDetail['revenueamount'] = $rsJOSellingCostDetail[$k]['subtotal'];
                $JOSellingCostDetail['costamount'] = 0;
                $JOSellingCostDetail['balanceamount'] = 0;
                $JOSellingCostDetail['refcode'] = $arrJO[0]['code'];
                $JOSellingCostDetail['trdesc'] = $rsJOSellingCostDetail[$k]['itemname'];
                array_push($arrDetailCol[$carKey], $JOSellingCostDetail);
            }

            // data biaya dari JO 
            $rsJOCostDetail = $rsJOCostDetailCol[$JOKey];
            for($k=0;$k<count($rsJOCostDetail);$k++) { 
                $JOCostDetail = array();
                $JOCostDetail['trdate'] = $arrJO[0]['trdate'];
                $JOCostDetail['jodate'] = $arrJO[0]['trdate'];
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
                $SPKCost['revenueamount'] = 0;
                $SPKCost['costamount'] = -$rsServiceDetail[$k]['total'];
                $SPKCost['balanceamount'] = 0;
                $SPKCost['refcode'] = $rsService[$i]['code'];
                $SPKCost['trdesc'] = $rsServiceDetail[$k]['itemname'];
                array_push($arrDetailCol[$carKey], $SPKCost);
            }
        }

    }

		
	$tempreport = '';

    if (empty($rsCar))
         $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
 
    for($j=0;$j<count($rsCar);$j++){ 
        $arrHeaderStyle = array();
		   
        $totalRevenue = 0;
        $totalCost = 0;

		$rsDetail = $arrDetailCol[ $rsCar[$j]['pkey'] ];

        // asc trdate detail
        usort($rsDetail, function($a, $b) {
            return strtotime($a['trdate']) <=> strtotime($b['trdate']);
        });
			
        $arrDetailStyle = array();
        for( $i=0;$i<count($rsDetail);$i++) {
            
            $amount = ($rsDetail[$i]['revenueamount']>0) ? $rsDetail[$i]['revenueamount'] : $rsDetail[$i]['costamount'];
            
            if($rsDetail[$i]['revenueamount']>0){
                $totalRevenue = $totalRevenue + $amount; 
            }
            if($rsDetail[$i]['costamount']<0){
                // $rsDetail[$i]['costamount'] = abs($amount);
                $totalCost = $totalCost + $amount; 
            }   
            
            $rsDetail[$i]['balanceamount'] = (isset($rsDetail[$i-1]['balanceamount'])) ? $rsDetail[$i-1]['balanceamount'] + $amount : $amount;  
            
            if ($rsDetail[$i]['balanceamount'] < 0)  
                $arrDetailStyle[$i]['balanceamount']['textColor'] = 'C41E3A'; 
        }
        
        $rsCar[$j]['totalRevenue'] = $totalRevenue; 
        $rsCar[$j]['totalCost'] = $totalCost; 
        $rsCar[$j]['totalBalance'] = (isset($rsDetail[$i-1]['balanceamount'])) ? $rsDetail[$i-1]['balanceamount'] : 0; 
               
        // has detail
        $rsCar[$j]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail, 'style' => $arrDetailStyle);
                  
        
         $color = '000000';  
        if ($rsCar[$j]['totalBalance'] < 0)  
           $color = 'C41E3A';
        else if ($rsCar[$j]['totalBalance'] > 0)  
           $color = '568203';  
            
        $arrHeaderStyle['totalBalance']['textColor'] = $color;
        
        
        $return = $obj->formatReportRows(array('data' => $rsCar[$j], 'style' => $arrHeaderStyle),$arrTemplate); 
            
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];

        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
            
    }  
    
        $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}
else{ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y');
} 

 
$arrPoliceNumber = $class->convertForCombobox($car->searchData($car->tableName.'.statuskey',1,true, ' and '.$car->tableName.'.statuskey = 1', ' order by policenumber asc'),'pkey','policenumber'); 
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrCarCategory = $class->convertForCombobox($carCategory->searchData($carCategory->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');

$arrTwigVar['inputSelDateType'] =  $class->inputSelect('selDateType', $arrDateType);  
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));  
$arrTwigVar['inputPoliceNumber'] =  $class->inputSelect('selPoliceNumber[]', $arrPoliceNumber, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelCarCategory'] =  $class->inputSelect('selCarCategory[]', $arrCarCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['autoLoad'] =  0; 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       
echo $twig->render('@custom/reportCarTurnover.html', $arrTwigVar);   
?>
