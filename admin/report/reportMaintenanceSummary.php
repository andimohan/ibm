<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('CarServiceMaintenance.class.php','ServiceCategory.class.php'));
$carServiceMaintenance = new CarServiceMaintenance();
$car = new Car();
$chassis = new Chassis();
$warehouse = new Warehouse();
$item = new Item();
$itemCategory = new ItemCategory();
$serviceCategory = new ServiceCategory();
$chassis = new Chassis();

include '_global.php';

$obj = $carServiceMaintenance;
$securityObject = 'reportCarServiceMaintenance'; // the value of security object is manually inserted to handle 
									 // some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));  
 
$arrFilterInformation = array();  
  
$_POST['selCarStatus[]'] = array(1);

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);  
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['car']),'dbfield' => 'policenumber', 'width'=>"100px" , "sortable" => false);
$arrDataStructure['carcategory'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"150px",  "sortable" => false);

// ==================================== ADD PERIOD COLUMN
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
    $_POST['trStartDate'] = date('01 / 01 / Y');
	$_POST['trEndDate'] = date('d / m / Y');  
}

$arrDataStructure['totalCost'] = array('title'=>ucwords($obj->lang['total']), 'dbfield' => 'totalcost', 'width'=>"100px" , "format" => 'integer', "style" => 'font-weight:bold', "calculateTotal" => true, "sortable" => false);


// ==================================== ADD PERIOD COLUMN
    
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['maintenanceSummaryReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
   
 
$criteria = ''; 

//$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $obj->generateComboboxOpt(array('data' => $employee->getAllStatus(),'label' => 'status')); 
$arrCarStatus = $obj->generateComboboxOpt(array('data' => $car->getAllStatus(),'label' => 'status')); 
$arrWarehouse = $warehouse->generateComboboxOpt(null,array('criteria' =>' and '.$warehouse->tableName.'.statuskey = 1','order by name asc')); 

$rsCar = $car->searchDataRow(array(
    $car->tableName.'.pkey',
    $car->tableName.'.policenumber',
    "CONCAT(".$car->tableName.".pkey, '-1') AS indexkey",
    $car->tableName.'.statuskey'
), ' and ' . $car->tableName.'.statuskey = 1');

$rsChassis = $chassis->searchDataRow(array(
    $chassis->tableName.'.pkey',
    $chassis->tableName.'.chassisnumber',
    "CONCAT(".$chassis->tableName.".pkey, '-2') AS indexkey",
    $chassis->tableName.'.statuskey'
), ' and ' . $chassis->tableName.'.statuskey = 1');

$arrCar = $obj->generateComboboxOpt(array('data' => $rsCar,'label' => 'policenumber','value' => 'indexkey')); 
$arrChassis = $obj->generateComboboxOpt(array('data' => $rsChassis,'label' => 'chassisnumber','value' => 'indexkey')); 
$arrCarAndChassis = $arrCar + $arrChassis;


$arrTwigVar['inputCarCode'] =  $class->inputText('carCode'); 
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputSelCar'] =  $class->inputSelect('selCar[]', $arrCarAndChassis, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelChassis'] =  $class->inputSelect('selChassis[]', $arrChassis, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;       

$carCriteria = '';
$chassisCriteria = '';


$arrCarKey = [];
$arrChassisKey = [];
$rsCar = [];
$rsChassis = [];
if (isset($_POST) && !empty($_POST['hidAction'])){   


    $criteria = '';
    $arrFilterInformation = array();
    $criteriaArr = array();

    array_push($criteriaArr, array('postVariable' => 'selWarehouse',
    'fieldName' => $obj->tableName . '.warehousekey',
    'label' => $obj->lang['warehouse'],
    'useArrayKey' => array('obj' => $warehouse)));

    // gk bisa karena gk punya field 'name'
//    array_push($criteriaArr, array('postVariable' => 'selCar',
//    'fieldName' => $obj->tableName . '.carkey',
//    'label' => $obj->lang['car'],
//    'useArrayKey' => array('obj' => $car)));
    
        
    
    if(isset($_POST) && !empty($_POST['selCar'])) { 

        $selectedKeys = is_array($_POST['selCar']) ? $_POST['selCar'] : explode(',', $_POST['selCar']);
        foreach ($selectedKeys as $keys) {
            $parts = explode('-', $keys); // pisahkan pkey dan tipe
            if (count($parts) == 2) {
                $pkey = trim($parts[0]);
                $type = trim($parts[1]);

                if ($type == '1') {
                    $arrCarKey[] = $pkey;
                } elseif ($type == '2') {
                    $arrChassisKey[] = $pkey;
                }
            }
        }

        $carCriteria = '';
        $chassisCriteria = '';

        $arrTempStatus = array();
        if (!empty($arrCarKey)) {
            $carCriteria .= ' AND '.$car->tableName.'.pkey IN ('.$class->oDbCon->paramString($arrCarKey, ',').') ';
            $rsCar = $car->searchData('', '', true, $carCriteria);
            foreach ($rsCar as $row) {
                $arrTempStatus[] = $row['policenumber'];
            }
        }

        if (!empty($arrChassisKey)) {
            $chassisCriteria .= ' AND '.$chassis->tableName.'.pkey IN ('.$class->oDbCon->paramString($arrChassisKey, ',').') ';
            $rsChassis = $chassis->searchData('', '', true, $chassisCriteria);
            foreach ($rsChassis as $row) {
                $arrTempStatus[] = $row['chassisnumber'];
            }
        }
				$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['car'] . ' / ' . $obj->lang['chassis'], 'filter' => $statusName ));
	} else {
        $rsCar = $car->searchData('','',true);
        $rsChassis = $chassis->searchData('','',true);
    } 
    
    //filter periode
    array_push($criteriaArr, array('postVariable' => array('trStartDate', 'trEndDate'),
    'fieldName' => $obj->tableName . '.trdate',
    'label' => $obj->lang['period'],
    'type' => 'daterange'));
 

    $obj->createReportCriteria($criteria, $arrFilterInformation, $criteriaArr);
 

    //$orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'policenumber'; // order by harus dr kolom yg terdaftar saja
    //$orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    //
	//$order = 'order by '.$orderBy.' ' .$orderType; 

    // get summary
    
    // semua mobil dan chasis harus muncul, meskipun sudah nonaktif 
    foreach($rsCar as $key=>$row) $rsCar[$key]['typekey'] = 1;
    foreach($rsChassis as $chassisRow){
        array_push($rsCar, array('typekey' => 2, 'pkey' => $chassisRow['pkey'], 'policenumber' => $chassisRow['chassisnumber'],'categoryname' => ''));
    }
    
    
    $rs = $obj->getMaintenanceSummaryByItem($criteria, $carCriteria, $chassisCriteria);

    // get master category dulu karena miantenance ambil dari 2 jenis, item dan services
    $rsItemCategory = $itemCategory->searchDataRow(array($itemCategory->tableName.'.pkey',$itemCategory->tableName.'.name'),
                                                  ' and '.$itemCategory->tableName.'.statuskey = 1'
                                                  );
    $rsItemCategory = array_column($rsItemCategory,null,'pkey');
    
    $rsServiceCategory = $serviceCategory->searchDataRow(array($serviceCategory->tableName.'.pkey',$serviceCategory->tableName.'.name'),
                                                  ' and '.$serviceCategory->tableName.'.statuskey = 1'
                                                  );
    $rsServiceCategory = array_column($rsServiceCategory,null,'pkey');
     
    $arrCostByCategory= array();
    $arrCategory= array(); // category barang dan jasa harus dipisah
    
    // catat ulagn cost berdasarkan nopol, kategori barang dan kategori jasa
    foreach($rs as $row){
        
        $indexkey = $row['typekey'].'-' .$row['carkey'].'-'.$row['itemcategorykey'].'-'.$row['itemtype'];
        
        if(!isset($arrCostByCategory[$indexkey])){
            
            $categoryName = ($row['itemtype'] == SERVICE) ? $rsServiceCategory[$row['itemcategorykey']]['name'] : $rsItemCategory[$row['itemcategorykey']]['name'];
            
            $arrCostByCategory[$indexkey] = $row;
            $arrCostByCategory[$indexkey]['itemcategoryname'] = $categoryName; 
            
            $cateogryIndexkey = $row['itemcategorykey'].'-'.$row['itemtype'];
            $arrCategory[$cateogryIndexkey] = array( 'indexkey' => $cateogryIndexkey, 'pkey' => $row['itemcategorykey'],'itemtype' => $row['itemtype'], 'itemcategoryname' => $categoryName);
            continue;
        }
        
        $arrCostByCategory[$indexkey]['total'] += $row['total'];
    
    }
    
        
    // sort dulu
    $obj->mknatsort($arrCategory,'itemcategoryname');
    $arrCategory = array_column($arrCategory,null,'indexkey');
    
	$tempreport = '';

    if (empty($rs))
         $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';
  
    
    // reindex by nopol dan kategori
    
//    $rsByItemKey = array_column($rs,null,'itemkey');  
//    $rsByVehicle =  $obj->reindexDetailCollections($rs,'carkey');
 
    // update header kolom report
     $arrTempStructure = array();
     foreach($arrCategory as $indexkey => $row){   
        $arrTempStructure[$indexkey] = array('title'=>$row['itemcategoryname'],'dbfield' => $indexkey, 'width'=>"100px",'format'=>'integer','sortable' => false,'calculateTotal' => true);   
     }
     
    $arrReturn = $obj->insertReportColumns(3, $arrDataStructure, $arrTempStructure,$twig,$arrTwigVar,  $arrHeaderTemplate);
    $arrTemplate = $arrReturn['tableTemplate']; 
    
    foreach($rsCar as $row){
        
            $row['totalcost'] = 0;
        
             foreach ($arrCategory as $indexkey=>$categoryRow) {    
                $indexkey = $row['typekey'].'-'.$row['pkey'].'-'.$categoryRow['pkey'].'-'.$categoryRow['itemtype'];  
                $categoryIndexkey = $categoryRow['pkey'].'-'.$categoryRow['itemtype'];
                $row[$categoryIndexkey] =  (isset($arrCostByCategory[$indexkey])) ? $arrCostByCategory[$indexkey]['total'] : 0;
                $row['totalcost'] +=  $row[$categoryIndexkey] ;
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


echo $twig->render('reportMaintenanceSummary.html', $arrTwigVar);   
?>
