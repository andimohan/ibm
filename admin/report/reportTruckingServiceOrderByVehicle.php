<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('TruckingServiceOrder.class.php', 'Car.class.php', 'Service.class.php'));
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$car = createObjAndAddToCol(new Car());
$truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE, 1));
$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder());

include '_global.php';

$obj = $truckingServiceOrder;
$securityObject = 'reportTruckingServiceOrderByVehicle'; // the value of security object is manually inserted to handle 
// some modules that have different security object from that of their class


if (!$security->isAdminLogin($securityObject, 10, true));

$arrFilterInformation = array();
$detailCriteria = '';
$_POST['selStatus[]'] = array(2,3,4,5,6);

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])) {
    $_POST['trStartDate'] = date('d / m / Y');
    $_POST['trEndDate'] = date('d / m / Y');
}

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();
$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title' => '#', 'align' => 'right', 'width' => "40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['soCode'] = array('title' => ucwords($obj->lang['soCode']), 'width' => "120px", 'dbfield' => 'code');
$arrDataStructure['date'] = array('title' => ucwords($obj->lang['date']), 'dbfield' => 'trdate', 'width' => "90px", 'format' => 'date');
$arrDataStructure['woCode'] = array('title' => ucwords($obj->lang['WOCode']), 'width' => "120px", 'dbfield' => 'workordercode');
$arrDataStructure['order'] = array('title' => ucwords('Order'), 'width' => "250px", 'dbfield' => 'customername');
$arrDataStructure['feet'] = array('title' => ucwords($obj->lang['feet']), 'width' => "150px", 'dbfield' => 'itemname');
$arrDataStructure['from'] = array('title' => ucwords($obj->lang['from']), 'width' => "150px", 'dbfield' => 'routefrom');
$arrDataStructure['destination'] = array('title' => ucwords($obj->lang['destination']), 'width' => "150px", 'dbfield' => 'routeto');
$arrDataStructure['fee'] = array('title' => ucwords($obj->lang['fee']), 'width' => "150px", 'dbfield' => 'priceinunit', 'format' => 'number', 'calculateTotal'=> true);

//$arrDataStructure['apDriver'] = array('title' => ucwords('Pinjaman Supir'), 'width' => "150px", 'dbfield' => 'appayable', 'format' => 'number', 'calculateTotal' => true);
$arrDataStructure['netResult'] = array('title' => ucwords($obj->lang['netResult']), 'width' => "150px", 'dbfield' => 'netresult', 'format' => 'number', 'calculateTotal' => true);
$arrDataStructure['description'] = array('title' => ucwords($obj->lang['description']), 'width' => "150px", 'dbfield' => 'trdesc');

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['truckingServiceOrderByVehicleReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrStatus = $class->convertForCombobox($obj->getAllStatus(), 'pkey', 'status');
$arrCar = $class->convertForCombobox($car->searchData($car->tableName.'.statuskey = 1', true, ' ', 'order by policenumber asc'), 'pkey', 'policenumber');

$rsCost = $truckingCost->searchData($truckingCost->tableName . '.statuskey = 1', true, ' ', 'order by name asc');
$rsCost = array_merge(array(array('pkey'=> -1, 'name' => $obj->lang['truckingFee']),array('pkey'=> -2, 'name' => $obj->lang['driverCommission']),array('pkey'=> -3, 'name' => $obj->lang['codriverCommission'])), $rsCost);
$arrTrucingCost = $class->convertForCombobox($rsCost, 'pkey', 'name');

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputSelCar'] = $class->inputSelect('selCar[]', $arrCar, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCost'] = $class->inputSelect('selCost[]', $arrTrucingCost, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] = $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));

$arrTwigVar['arrTemplate'] = $arrHeaderTemplate;

$arrAdditionalCost = array();
if (isset($_POST) && !empty($_POST['hidAction'])) {

    $criteria = '';
    $criteriaArr = array();
    $criteriaSellingDetail = '';
    $criteriaCostDetail = '';

    if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / ');
        array_push($criteriaArr, array(
            'postVariable' => 'trStartDate',
            'criteria' => $criteria,
            'label' => $obj->lang['date'],
            'filter' => $_POST['trStartDate'] . ' - ' . $_POST['trEndDate'],
            'type' => 'criteria'
        ));
	} 

    if(isset($_POST) && !empty($_POST['selCar'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCar']));   
        
       	$criteria .= ' AND '.$obj->tableWorkOrder.'.carkey in('.$key.')';  
        
        $criteriaSellingDetail .= ' and '. $obj->tableSellingCost .'.carkey in ('. $key .')  ';
        $criteriaCostDetail .= ' and '. $truckingServiceWorkOrder->tableName .'.carkey in ('. $key .')  '; 

        $rsCriteria = $car->searchData('','',true, ' and '.$car->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['policenumber']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['car'], 'filter' => $statusName ));
        
	}

    if(isset($_POST) && !empty($_POST['selCost'])) { 

        array_push($arrAdditionalCost,0);
        
        foreach($_POST['selCost'] as $categoryRow){
            if($categoryRow < 0)
             array_push($arrAdditionalCost,$categoryRow);
        }
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCost']));   
        
        $criteriaSellingDetail .= ' and '. $obj->tableSellingCost .'.costkey in ('. $key .')  ';
        
        //$criteriaCostDetail .= ' and '. $truckingServiceWorkOrder->tableCost .'.costkey in ('. $key .')  '; 
        $criteriaCostDetail .= ' and '. $truckingServiceWorkOrder->tableItem .'.pkey in ('. $key .')  '; 
        
        
        $rsCriteria = $truckingCost->searchData('','',true, ' and '.$truckingCost->tableName.'.pkey in ('.$key.')');
	
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['policenumber']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['car'], 'filter' => $statusName ));
        
	}


    array_push($criteriaArr, array(
        'postVariable' => 'selStatus',
        'type' => 'status'
    ));

    $obj->createReportCriteria($criteria, $arrFilterInformation, $criteriaArr);

    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

    $order = 'order by ' . $orderBy . ' ' . $orderType;

    $rs = $obj->generateDataForSalesOrderVehicleReport($criteria, $order);
    $arrPkey = array_column($rs, 'sokey');
    $arrWOKey = array_column($rs, 'workorderkey');

    $rsJOSellingDetail = $obj->getSellingCostDetail($arrPkey,$criteriaSellingDetail);
    $rsJOSellingDetailCol = $obj->reindexDetailCollections($rsJOSellingDetail,'refkey');

    //$rsJOSellingDetail = array_unique(array_column($rsJOSellingDetail, 'itemname', 'costkey'));

    $validCostkeys = [];
    foreach ($rs as $row) {

        $index = ($row['carkey'] ?? '') . '-' . ($row['workorderkey'] ?? '');
        foreach ($rsJOSellingDetailCol[$row['sokey']] ?? [] as $sellingRow) {
            if (
                $index === (($sellingRow['carkey'] ?? '') . '-' . ($sellingRow['workorderkey'] ?? '')) &&
                (floatval($sellingRow['subtotal']) > 0) 
                && ($sellingRow['reimburse'] == 0)
            ) {
                $validCostkeys[$sellingRow['costkey']] = $sellingRow['itemname'];
            }
        }


    }
    $rsJOSellingDetail = $validCostkeys;

    
    //$rsWOCostDetail = $truckingServiceWorkOrder->getCostDetail($arrWOKey,'',$criteriaCostDetail);
    
    $rsWOCostDetail = $truckingServiceWorkOrder->generateCostForReport($arrWOKey,$criteriaCostDetail,'',$arrAdditionalCost);
    $rsWOCostDetailCol = $obj->reindexDetailCollections($rsWOCostDetail, 'wokey');
    $rsWOCostDetail = array_unique(array_column($rsWOCostDetail, 'name', 'costkey'));

    // $validWOCostkeys = [];
    // foreach ($rsWOCostDetail as $row) {
    //     if ((floatval($row['amount']) > 0) && ($row['reimburse'] == 0)) {
    //         $validWOCostkeys[$row['costkey']] = $row['name'];
    //     }
    // }
    // $rsWOCostDetail = $validWOCostkeys;
    
    $arrTempStructure = array();
  
    foreach ($rsJOSellingDetail as $key => $value) {
        $arrStructureIndex = 'mnv-selling-' . $key;
        $arrTempStructure[$arrStructureIndex] = array('title' => ucwords($value), 'dbfield' => $arrStructureIndex, 'width' => "150px", 'sortable' => false, 'format' => 'number', 'calculateTotal' => true, 'textColor' => '568203');
    }

    foreach ($rsWOCostDetail as $key => $value) {
        $arrStructureIndex = 'mnv-cost-' . $key;
        if (!isset($arrTempStructure[$arrStructureIndex])) {
            $arrTempStructure[$arrStructureIndex] = array('title' => ucwords($value), 'dbfield' => $arrStructureIndex, 'width' => "150px", 'sortable' => false, 'format' => 'number', 'calculateTotal' => true, 'textColor' => '0093AF');
        }
    }

    $arrReturn = $obj->insertReportColumns(9, $arrDataStructure, $arrTempStructure, $twig, $arrTwigVar, $arrHeaderTemplate);
    $arrTemplate = $arrReturn['tableTemplate'];

    $tempreport = '';
    if (empty($rs))
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="' . count($arrHeaderTemplate['dataStructure']) . '"></td></tr>';
   
    $totalRs = count($rs);
    for ($i = 0; $i < $totalRs; $i++) {
        $index = $rs[$i]['carkey'] . '-' . $rs[$i]['workorderkey'];

        $netResult = 0;

        $fee = $rs[$i]['priceinunit'];
        $totalSelling = 0;
        $totalCost = 0;
        
        $rsSelling = isset($rsJOSellingDetailCol[$rs[$i]['sokey']]) ? $rsJOSellingDetailCol[$rs[$i]['sokey']] : array();
        foreach ($rsSelling as $sellingRow) {

            if($sellingRow['reimburse'] == 1) continue;

            $arrStructureIndex = 'mnv-selling-' . $sellingRow['costkey'];
            if (!isset($rs[$i][$arrStructureIndex])) {
                $rs[$i][$arrStructureIndex] = 0;
            }
            $indexSelling = $sellingRow['carkey'] . '-' . $sellingRow['workorderkey'];
            // $rs[$i][$arrStructureIndex] = $sellingRow['subtotal'];
            // $totalSelling += $sellingRow['subtotal'];
            if($index == $indexSelling){
                $rs[$i][$arrStructureIndex] = $sellingRow['subtotal'];
                $totalSelling += $sellingRow['subtotal'];
            }
        }

        $rsCost = isset($rsWOCostDetailCol[$rs[$i]['workorderkey']]) ? $rsWOCostDetailCol[$rs[$i]['workorderkey']] : array();
        foreach ($rsCost as $costRow) {

            if($costRow['reimburse'] == 1) continue;

            $arrStructureIndex = 'mnv-cost-' . $costRow['costkey'];
            if (!isset($rs[$i][$arrStructureIndex])) {
                $rs[$i][$arrStructureIndex] = 0;
            }
            $rs[$i][$arrStructureIndex] = $costRow['amount'];
            $totalCost += $costRow['amount'];
        }

        //hasil bersih => fee + totalSelling - totalCost
        $netResult = $fee + $totalSelling - $totalCost;

        $rs[$i]['netresult'] = $netResult;

        $return = $obj->formatReportRows(array('data' => $rs[$i]), $arrTemplate);

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }

    $tableHeader = $twig->render('template-header.html', $arrTwigVar);
    $obj->generateReport($_POST, $tempreport, $arrTemplate, $dataToExport, $arrFilterInformation, $tableHeader);

}

echo $twig->render('reportTruckingServiceOrderByVehicle.html', $arrTwigVar);

?>