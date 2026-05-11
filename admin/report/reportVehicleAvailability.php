<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $car;
$securityObject = 'reportCar';
//$securityObject = 'reportVehicleAvailability'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 

$arrFilterInformation = array();    

// ===== FOR EXPORT SECTION
$dataToExport = array();

/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
//$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['code']),'dbfield' => 'code' , 'width'=>"100px");
$arrDataStructure['noPolisi'] = array('title'=>ucwords($obj->lang['carRegistrationNumber']),'dbfield' => 'policenumber','width'=>"100px");
$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");
$arrDataStructure['category'] = array('title'=>ucwords($obj->lang['category']),'dbfield' => 'categoryname', 'width'=>"130px");
$arrDataStructure['businessPartner'] = array('title'=>ucwords($obj->lang['businessPartner']),'dbfield' => 'suppliername', 'width'=>"190px");
$arrDataStructure['JOCode'] = array('title'=>ucwords($obj->lang['JOCode']),'dbfield' => 'jocode', 'width'=>"150px");
$arrDataStructure['WOCode'] = array('title'=>ucwords($obj->lang['WOCode']),'dbfield' => 'wocode', 'width'=>"150px");
$arrDataStructure['WODate'] = array('title'=>ucwords($obj->lang['serviceWorkOrderDate']),'dbfield' => 'wodate', 'width'=>"100px", 'format' => 'date','returnOnEmpty' => array('returnOnEmpty' => true, 'value' => ''));
$arrDataStructure['driver'] = array('title'=>ucwords($obj->lang['driver']),'dbfield' => 'drivername', 'width'=>"150px" );
$arrDataStructure['customer'] = array('title'=>ucwords($obj->lang['customer']),'dbfield' => 'customername', 'width'=>"200px" );
$arrDataStructure['consignee'] = array('title'=>ucwords($obj->lang['consignee']),'dbfield' => 'consigneename', 'width'=>"200px" );
$arrDataStructure['route'] = array('title'=>ucwords($obj->lang['route']),'dbfield' => 'route', 'width'=>"200px" );
$arrDataStructure['status'] = array('title'=>ucwords($obj->lang['status']),'dbfield' => 'statusname', 'width'=>"100px");

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['vehicleAvailabilityReport']; 
$arrHeaderTemplate['dataStructure'] = $arrDataStructure;
$arrHeaderTemplate['total'] = array();

array_push($arrTemplate, $arrHeaderTemplate);
	
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
    
    if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" =>  $class->lang['warehouse'], 'filter' => $statusName ));
        
	}
    
    // untuk pencarian berdasarkan kode
	/*if(isset($_POST) && !empty($_POST['carCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['carCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['carCode']));
	}*/

    // untuk pencarian berdasarkan nama
    if(isset($_POST) && !empty($_POST['carNumber'])) {
		$criteria .= ' AND '.$obj->tableName.'.policenumber LIKE  ('.$class->oDbCon->paramString('%'.$_POST['carNumber'].'%').')'; 
		array_push($arrFilterInformation,array("label" =>  $class->lang['carRegistrationNumber'], 'filter' =>  $_POST['carNumber']));
	}
    if(isset($_POST) && !empty($_POST['WOCode'])) {
		$criteria .= ' AND '.$obj->tableTruckingServiceWorkOrder.'.code LIKE  ('.$class->oDbCon->paramString('%'.$_POST['WOCode'].'%').')'; 
		array_push($arrFilterInformation,array("label" =>  $class->lang['WOCode'], 'filter' =>  $_POST['WOCode']));
	}
    
    if(isset($_POST) && $_POST['isAvailable']) {
		$criteria .= ' AND '.$obj->tableName.'.pkey not in (select carkey from '.$obj->tableTruckingServiceWorkOrder.' where statuskey in (1,2))'; 
        $availability = ($_POST['isAvailable'] == 1) ? $obj->lang['available'] : '-';
		array_push($arrFilterInformation,array("label" => $class->lang['availability'], 'filter' => $availability));
	}
    
    if(isset($_POST) && !empty($_POST['selCategory'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));   
        
        $criteria .= ' AND '.$obj->tableName.'.categorykey in('.$key.')'; 
         
        $rsCriteria =  $carCategory->searchData('','',true,' AND '.$carCategory->tableName.'.pkey in('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $class->lang['category'], 'filter' => $statusName));
        
	}  

    if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in ('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $class->lang['status'], 'filter' => $statusName));
        
	} 
    
    if(isset($_POST) && !empty($_POST['selSupplier'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selSupplier']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.supplierkey in('.$key.')';  

        $rsCriteria = $supplier->searchData('','',true, ' and '.$supplier->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => $class->lang['supplier'], 'filter' => $statusName ));
        
	}
	 
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
    
	$order = 'order by '.$orderBy.' ' .$orderType; 
      
    // select data
    //$rs = $obj->searchData('','',true,$criteria,$order);
    $rs = $obj->getVehicleAvailabilityReport($criteria,$order);
    $tempreport = ''; 

    for( $i=0;$i<count($rs);$i++) {     
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate);
        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION
        $tempreport .= $return['html'];  
    }	  
    $obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);
}

$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');    
$arrCategory = $class->convertForCombobox($carCategory->searchData($carCategory->tableName.'.statuskey',1,true, ' and '.$carCategory->tableName.'.isleaf = 1', ' order by name asc'),'pkey','name');   
$arrSupplier = $class->convertForCombobox($supplier->searchData($supplier->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');


//$arrTwigVar['inputCarCode'] =  $class->inputText('carCode');  
$arrTwigVar['inputCarNumber'] =  $class->inputText('carNumber');   
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));  
$arrTwigVar['inputSelSupplier'] =  $class->inputSelect('selSupplier[]', $arrSupplier, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox')); 
$arrTwigVar['inputWOCode'] =  $class->inputText('WOCode'); 
$arrTwigVar['inputIsAvailable'] =  $class->inputCheckBox('isAvailable'); 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;   

echo $twig->render('reportVehicleAvailability.html', $arrTwigVar);  
    
?>