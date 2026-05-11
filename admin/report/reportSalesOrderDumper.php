<?php
include '../../_config.php';
include '../../_include.php';
include '_global.php';

$obj = $salesOrderDumper; 
$securityObject = 'reportSalesOrderDumper'; // the value of security object is manually inserted to handle 
								  // some modules that have different security object from that of their class
								  
if(!$security->isAdminLogin($securityObject,10,true)); 


$_POST['selStatus[]'] = array(2,3);

$arrFilterInformation = array(); 
$detailCriteria = '';

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),  'width'=>"150px", 'dbfield' => 'code'); 
$arrDataStructure['project'] = array('title'=>ucwords($obj->lang['project']),  'width'=>"150px", 'dbfield' => 'projectname'); 
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),  'width'=>"150px", 'dbfield' => 'warehousename'); 
$arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['driver']),'dbfield' => 'drivername', 'width'=>"150px");
$arrDataStructure['car'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),  'dbfield' => 'policenumber', 'width'=>"150px" );
$arrDataStructure['weight'] = array('title'=>ucwords($obj->lang['weight']),  'dbfield' => 'weight', 'width'=>"100px",'format'=>'number' );
$arrDataStructure['cubication'] = array('title'=>ucwords($obj->lang['cubication']),  'dbfield' => 'phone', 'width'=>"150px" );
$arrDataStructure['height'] = array('title'=>ucwords($obj->lang['height']),  'dbfield' => 'height', 'width'=>"150px" );
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'trdate', 'width'=>"90px",'format'=>'date');
$arrDataStructure['startDate'] = array('title'=>ucwords($obj->lang['startDate']),  'dbfield' => 'address', 'width'=>"200px",'format'=>'date' );
$arrDataStructure['endDate'] = array('title'=>ucwords($obj->lang['endDate']),  'dbfield' => 'address', 'width'=>"200px",'format'=>'date' );
$arrDataStructure['duration'] = array('title'=>ucwords($obj->lang['duration']),  'dbfield' => 'duration', 'width'=>"100px",'format'=>'number' );
$arrDataStructure['distance'] = array('title'=>ucwords($obj->lang['distance']),  'dbfield' => 'distance', 'width'=>"100px",'format'=>'number' );
$arrDataStructure['compartment'] = array('title'=>ucwords($obj->lang['compartmentNumber']),  'dbfield' => 'attention', 'width'=>"200px");
$arrDataStructure['location'] = array('title'=>ucwords($obj->lang['location']),  'dbfield' => 'locationname', 'width'=>"100px");
$arrDataStructure['mileageStart'] = array('title'=>ucwords($obj->lang['mileageStart']),  'dbfield' => 'mileagestart', 'width'=>"100px",'format'=>'number');
$arrDataStructure['mileageNextDue'] = array('title'=>ucwords($obj->lang['mileageNextDue']),  'dbfield' => 'mileagenextdue', 'width'=>"100px",'format'=>'number');
$arrDataStructure['mileage'] = array('title'=>ucwords($obj->lang['mileage']),  'dbfield' => 'locationname', 'width'=>"100px",'format'=>'number');
$arrDataStructure['fuelConsumption'] = array('title'=>ucwords($obj->lang['fuelConsumption']),  'dbfield' => 'locationname', 'width'=>"200px",'format'=>'number');
$arrDataStructure['fuelEficiency'] = array('title'=>ucwords($obj->lang['fuelEficiency']),  'dbfield' => 'locationname', 'width'=>"100px",'format'=>'number');
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'total','align'=>'right', 'width'=>"110px",'format'=>'number','calculateTotal' => true); 
$arrDataStructure['note'] = array('title'=>ucwords($obj->lang['note']), 'width'=>"300px",'dbfield' => 'trdesc');
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");
		   
$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['serviceOrderInvoiceReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);


if (isset($_POST) && !empty($_POST['hidAction'])){
	
	$criteria = '';

	if(isset($_POST) && !empty($_POST['salesOrderDumperCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['salesOrderDumperCode'].'%').')';
		array_push($arrFilterInformation,array("label" => ucwords($obj->lang['code']), 'filter' => $_POST['salesOrderDumperCode']));
	}
	  	 
    if(isset($_POST) && !empty($_POST['projectCode'])) { 
        $criteria .= ' AND '.$obj->tableSalesOrder.'.code  LIKE ('.$class->oDbCon->paramString('%'.$_POST['projectCode'].'%').')';
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['soCode']), 'filter' => $_POST['projectCode']));
	}
    
	if(isset($_POST) && !empty($_POST['trStartDate'])){
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => ucwords($obj->lang['period']), 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
    if(isset($_POST) && !empty($_POST['projectName'])) { 
        $criteria .= ' AND '.$obj->tableSalesOrder.'.code  LIKE ('.$class->oDbCon->paramString('%'.$_POST['projectName'].'%').')';
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['projectName']), 'filter' => $_POST['projectName']));
	}
	
    if(isset($_POST) && !empty($_POST['selDriver'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selDriver']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.driverkey in('.$key.')';  

        $rsCriteria = $employee->searchData('','',true, ' and '.$employee->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$driverName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['driver']), 'filter' => $driverName ));
        
	}


    if(isset($_POST) && !empty($_POST['selCar'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCar']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.carkey in('.$key.')';  

        $rsCriteria = $car->searchData('','',true, ' and '.$car->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['policenumber']);
			
		$carName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['car']), 'filter' => $carName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selLocation'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selLocation']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.locationkey in('.$key.')';  

        $rsCriteria = $location->searchData('','',true, ' and '.$location->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$locationName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['location']), 'filter' => $locationName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$warehouseName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['warehouse']), 'filter' => $warehouseName ));
        
	}

	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => ucwords($obj->lang['status']), 'filter' => $statusName));
        
	}
    
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
		   
	$order = 'order by '.$orderBy.' ' .$orderType; 
	$rs = $obj->searchData('','',true,$criteria,$order);
		 
    $tempreport = ''; 

    
    for( $i=0;$i<count($rs);$i++) {   

         
        // has detail
//        $rs[$i]['_detail_'] = array('arrTemplate'=>$arrDetailTemplate,'data' => $rsDetail); 
        
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    } 
    $footnote = '';

	$obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation,'', $footnote);
		
} 
else{
   	$_POST['trStartDate'] = date('d / m / Y');
	$_POST['trEndDate'] = date('d / m / Y'); 
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');
$arrCar = $class->convertForCombobox($car->searchData ('','',true,' and ('.$car->tableName.'.statuskey = 1  )'),'pkey','policenumber');    
$arrDriver = $class->convertForCombobox($employee->searchData ('','',true,' and ('.$employee->tableName.'.statuskey = 2 )'),'pkey','name');    
$arrLocation = $class->convertForCombobox($location->searchData ('','',true,' and ('.$location->tableName.'.statuskey = 1 )'),'pkey','name');    
$arrWarehouse = $obj->convertForCombobox($warehouse->searchData('','',true,' and ('.$warehouse->tableName.'.statuskey = 1 )'),'pkey','name');  
$arrCustomCode =  $class->convertForCombobox($customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'],true,' and ('.$customCode->tableName.'.statuskey = 1 ) '),'pkey','name');  

$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));   
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelDriver'] =  $class->inputSelect('selDriver[]', $arrDriver, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSalesOrderDumperCode'] =  $class->inputText('salesOrderDumperCode'); 
$arrTwigVar['inputSelCar'] =  $class->inputSelect('selCar[]', $arrCar, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputProjectCode'] =  $class->inputText('projectCode'); 
$arrTwigVar['inputProjectName'] =  $class->inputText('projectName'); 
$arrTwigVar['inputSelLocation'] =  $class->inputSelect('selLocation[]', $arrLocation, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;  

echo $twig->render('reportSalesOrderDumper.html', $arrTwigVar);   

?>
