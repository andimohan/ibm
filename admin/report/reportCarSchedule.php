<?php
include '../../_config.php';
include '../../_include.php';
include '_global.php';

$obj = $car;
$securityObject = 'reportCarSchedule'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true));  

$arrFilterInformation = array();
if(!isset($_POST['hideNoActivity'])) $_POST['hideNoActivity'] = 1;


$orderCriteria = array(); 
$orderCriteria['orderBy'] = 'policenumber'; 
$orderCriteria['orderType'] = (isset ($_POST) && !empty($_POST['hidOrderType'])) ? $_POST['hidOrderType'] : -1;
  
// ====================== must be set before TWIG


// ===== FOR EXPORT SECTION
$dataToExport = array();


/* data structure */
$arrTemplate = array();
$hideNoActivity = (isset($_POST['hideNoActivity']) && $_POST['hideNoActivity'] == 1) ? true : false;

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);  
$arrDataStructure['policeNumber'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),  'width'=>"100px", 'dbfield'=>'policenumber');


// ====================== must be set before TWIG
if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y',strtotime("+1 week"));
} 

$startDate = $class->oDbCon->paramDate($_POST['trStartDate'],' / ','Y-m-d');
$startDate = str_replace('\'','',$startDate);

$endDate = $class->oDbCon->paramDate($_POST['trEndDate'],' / ','Y-m-d');
$endDate = str_replace('\'','',$endDate);

$currentDate = strtotime($startDate);

$arrWODate = array();
while ($currentDate <= strtotime($endDate)){ 

    $dmy = date('Y-m-d', $currentDate);
    
    $arrWODate[$dmy] = array('label' => $currentDate); 
    $arrDataStructure['date'.$dmy] = array('title'=> date('d / m / Y', $currentDate) ,'align'=>'center', 'dbfield' => $dmy, 'sortable' => false, 'width'=>"200px");
 
    $currentDate =  strtotime('+1 day', $currentDate);
} 
 
 
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['carScheduleReport'];
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrCar = $class->convertForCombobox($car->searchData($car->tableName.'.statuskey',1,true,'','order by policenumber asc'),'pkey','policenumber');
    
$arrTwigVar['inputWorkOrderCode'] =  $class->inputText('workOrderCode');
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['inputSalesCode'] =  $class->inputText('salesCode');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
//$arrTwigVar['inputHidCarKey'] = $class->inputHidden('hidCarKey');
//$arrTwigVar['inputPoliceNumber'] =  $class->inputText('policeNumber'); 
$arrTwigVar['inputSelCar'] =  $class->inputSelect('selCar[]', $arrCar, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputHideNoActivity'] =  $class->inputCheckBox('hideNoActivity'); 
$arrTwigVar['order'] =  $orderCriteria;
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';
    $carCriteria = ' and '.$obj->tableName.'.statuskey = 1' ;
        
    if(isset($_POST) && !empty($_POST['selCar'])) { 
        
        $key = $class->oDbCon->paramString($_POST['selCar'],',');   
        
       	$carCriteria .= ' AND '.$car->tableName.'.pkey in ('.$key.')';  

        $rsCriteria = $car->searchData('','',true, ' and '.$car->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['policenumber']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $obj->lang['carRegistrationNumber'], 'filter' => $statusName ));
        
	} 
    
    if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$truckingServiceWorkOrder->tableName.'.stuffingdatetime between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate']. ' 23:59:59',' / '); 
		array_push($arrFilterInformation,array("label" => 'Tanggal', 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
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
    
            
  	if(isset($_POST) && !empty($_POST['salesCode'])) {
		$criteria .= ' AND '.$truckingServiceWorkOrder->tableServiceOrderHeader.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode SO', 'filter' => $_POST['salesCode']));
	}
    
    if(isset($_POST) && !empty($_POST['workOrderCode'])) {
		$criteria .= ' AND '.$truckingServiceWorkOrder->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['workOrderCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['workOrderCode']));
	}
   
   
    $order = 'order by '.$orderCriteria['orderBy'].' ' . (($orderCriteria['orderType'] == 1) ? 'desc' : 'asc');  
    $rsCar = $obj->searchData($obj->tableName.'.statuskey',1,true,$carCriteria,$order);
    
    $tempreport = ''; 

    // ============================= GENERATE DATA =============================  
	for($i=0;$i<count($rsCar);$i++) {
        
            $rsWorkOrder = $truckingServiceWorkOrder->generateCarSchedule($rsCar[$i]['pkey'],$criteria);   
            
            if($hideNoActivity && empty($rsWorkOrder))  continue;
        
            $arrWO = $obj->arrayColumnWithMultiDimension($rsWorkOrder, 'stuffingdatetimeshort');
        
            foreach($arrWODate as $dateIndex => $dateRow){  
                  
                 if (!isset($arrWO[$dateIndex])) continue;
                
                 $woEachDate = $arrWO[$dateIndex];
                 
                 $arrField = array(); 
                 
                 foreach($woEachDate as $workOrder){    
                        array_push($arrField,'<li><strong>'.$workOrder['code'].'</strong> - '.$workOrder['drivername'].'<br>'. $workOrder['serviceordercode'].' - '.$workOrder['customername'].'</li>');
                 }
                
                $cellContent = implode('',$arrField);
                if (!empty($cellContent))
                    $cellContent = '<ul class="cell-list">'.$cellContent.'</ul>';
                $rsCar[$i][$dateIndex] = $cellContent; 
            }
        
            
        
            $return = $obj->formatReportRows(array('data' => $rsCar[$i]),$arrTemplate); 

            // ===== FOR EXPORT SECTION 
            array_push($dataToExport, $return['data']);  
            // ===== END FOR EXPORT SECTION

            $tempreport .= $return['html']; 


            $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);
		}
    
        $tableHeader = $twig->render('template-header.html', $arrTwigVar);
        $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,$tableHeader);


}

echo $twig->render('reportCarSchedule.html', $arrTwigVar);   

?>
