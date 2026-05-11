<?php
	 
include '../../_config.php';  
include '../../_include-v2.php';

includeClass(array('CarServiceMaintenance.class.php','Car.class.php','Chassis.class.php','Item.class.php','Warehouee.class.php'));
$carServiceMaintenance = new CarServiceMaintenance();
$item = new Item();
$warehouse = new Warehouse();
$car = new Car();
$chassis = new Chassis();

include '_global.php';

$obj= $carServiceMaintenance;
$securityObject = 'reportCarMaintenanceHistory'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true)); 
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  
 
$arrFilterInformation = array(); 
$detailCriteria = '';
    
$dataToExport = array();

if (!isset($_POST['trStartDate']) || empty($_POST['trStartDate'])){ 
	$_POST['trStartDate'] = date('01 / 01 / Y');
	$_POST['trEndDate'] = date('d / m / Y');
}   


/* data structure */
$arrTemplate = array();

$arrDataStructure = array();
$arrDataStructure['rowNumber'] = array('title'=>'#', 'align'=>'right', 'width'=>"40px", 'autoNumber' => true, "sortable" => false);
$arrDataStructure['date'] = array('title'=>ucwords($obj->lang['date']),'dbfield' => 'salesdate', 'align'=>'center','width'=>"120px",'format'=>'date', "sortable" => false); 
$arrDataStructure['executedate'] = array('title'=>ucwords($obj->lang['executeDate']),'dbfield' => 'executedate', 'align'=>'center','width'=>"120px",'format'=>'date', "sortable" => false); 
/*$arrDataStructure['warehouse'] = array('title'=>ucwords($obj->lang['warehouse']),'dbfield' => 'warehousename', 'width'=>"100px");*/
$arrDataStructure['mileage'] = array('title'=>ucwords($obj->lang['mileage']),  'width'=>"80px", 'dbfield' => 'mileage',"format" => 'number', "sortable" => false);  
$arrDataStructure['itemName'] = array('title'=>ucwords($obj->lang['itemName']),  'width'=>"240px", 'dbfield' => 'itemname', "sortable" => false); 
$arrDataStructure['description'] = array('title'=>ucwords($obj->lang['description']),'dbfield' => 'trdesc', 'width'=>"150px", "sortable" => false); 
$arrDataStructure['qty'] = array('title'=>ucwords($obj->lang['qty']),'dbfield' => 'qty','align'=>'right', 'width'=>"60px",'format'=>'number', "sortable" => false); 
$arrDataStructure['unitname'] = array('title'=>ucwords($obj->lang['unit']),'dbfield' => 'unitname', 'width'=>"60px", "sortable" => false); 

// kalo format number, kalo row nya ad decimal, totalnya bulet jd aneh.
// dan maintenance sepertinya lebih sering dipake org lapangan, jd gk perlu koma
$arrDataStructure['pricePerUnit'] = array('title'=>ucwords($obj->lang['pricePerUnit']),'dbfield' => 'priceinunitafterdiscount','align'=>'right', 'width'=>"90px",'format'=>'integer', "sortable" => false); 
$arrDataStructure['total'] = array('title'=>ucwords($obj->lang['total']),'dbfield' => 'totaldetailafterdiscount','align'=>'right', 'width'=>"150px",'format'=>'integer','calculateTotal' => true, "sortable" => false); 
$arrDataStructure['technicianName'] = array('title'=>ucwords($obj->lang['technician']),'dbfield' => 'technicianname', 'width'=>"200px", "sortable" => false); 
$arrDataStructure['driverName'] = array('title'=>ucwords($obj->lang['driver']),'dbfield' => 'drivername', 'width'=>"200px", "sortable" => false); 
$arrDataStructure['code'] = array('title'=>ucwords($obj->lang['reference']),  'width'=>"150px", 'dbfield' => 'salescode', "sortable" => false); 

$arrHeaderTemplate = array();
$arrHeaderTemplate['reportTitle'] = $obj->lang['carMaintenanceHistoryReport']; 
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
	    array_push($arrFilterInformation,array("label" => $obj->lang['warehouse'], 'filter' => $statusName ));
        
	}
    
    if(isset($_POST) && !empty($_POST['trStartDate'])){
         
		$criteria .= ' and '.$obj->tableName.'.trdate between '.$class->oDbCon->paramDate( $_POST['trStartDate'],' / ').' AND '.$class->oDbCon->paramDate( $_POST['trEndDate'],' / '); 
		array_push($arrFilterInformation,array("label" => $obj->lang['period'], 'filter' => $_POST['trStartDate'] . ' - ' .$_POST['trEndDate'] ));
	}
    
	switch ($_POST['selType']) {


		case 1:
			if (isset($_POST) && !empty($_POST['selCarKey'])) {

				$key = implode(",", $class->oDbCon->paramString($_POST['selCarKey']));

				$criteria .= ' AND ' . $obj->tableName . '.carkey in(' . $key . ')';

				$rsCriteria = $car->searchData('', '', true, ' and ' . $car->tableName . '.statuskey = 1  and ' . $car->tableName . '.pkey in (' . $key . ')');

				$arrTempStatus = array();
				for ($k = 0; $k < count($rsCriteria); $k++)
					array_push($arrTempStatus, $rsCriteria[$k]['policenumber']);

				$statusName = implode(", ", $arrTempStatus);
				array_push($arrFilterInformation, array("label" => 'No Polisi', 'filter' => $statusName));

			}
			break;
		case 2:
			if (isset($_POST) && !empty($_POST['selChassisKey'])) {

				$key = implode(",", $class->oDbCon->paramString($_POST['selChassisKey']));

				$criteria .= ' AND ' . $obj->tableName . '.chassiskey in(' . $key . ')';

				$rsCriteria = $chassis->searchData('', '', true, ' and ' . $chassis->tableName . '.statuskey = 1  and ' . $chassis->tableName . '.pkey in (' . $key . ')');

				$arrTempStatus = array();
				for ($k = 0; $k < count($rsCriteria); $k++)
					array_push($arrTempStatus, $rsCriteria[$k]['chassisnumber']);

				$statusName = implode(", ", $arrTempStatus);
				array_push($arrFilterInformation, array("label" => 'No Chassis', 'filter' => $statusName));

			}
			break;
	}


     	
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'trdate'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';

		   
	$order = 'order by '.$orderBy.' ' .$orderType;  
	
	$rs = array();
	if(isset($_POST)){
		switch ($_POST['selType']) {
			case 1 : $rs = $obj->getCarMaintenanceHistory('',$criteria,$order);
					 break; 
			case 2 : $rs = $obj->getChassisMaintenanceHistory('',$criteria,$order);
					 break; 
		}
	}

	

    $tempreport = '';
		
    if (empty($rs)) 
        $tempreport .= '<tr class="report-row rewrite-row"><td colspan="'.count($arrHeaderTemplate['dataStructure']).'"></td></tr>';

	  for( $i=0;$i<count($rs);$i++) {  
        $return = $obj->formatReportRows(array('data' => $rs[$i]),$arrTemplate); 

        // ===== FOR EXPORT SECTION 
        array_push($dataToExport, $return['data']);  
        // ===== END FOR EXPORT SECTION

        $tempreport .= $return['html'];
        $arrTemplate[0]['total'] = $obj->arraySum($arrTemplate[0]['total'], $return['subtotal'][0]);

    }
    
	$obj->generateReport($_POST, $tempreport, $arrTemplate,$dataToExport,$arrFilterInformation);

}
else{
   	//$_POST['trStartDate'] = date('d / m / Y');
	//$_POST['trEndDate'] = date('d / m / Y'); 
} 

$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'','order by name asc'),'pkey','name');
$arrType = $obj->convertForCombobox($obj->getMaintenanceType(),'pkey','name');
$arrCar = $class->convertForCombobox($car->searchData($car->tableName . '.statuskey', 1, true, '', 'order by policenumber asc'), 'pkey', 'policenumber');
$arrChassis = $class->convertForCombobox($chassis->searchData($chassis->tableName . '.statuskey', 1, true, '', 'order by chassisnumber asc'), 'pkey', 'chassisnumber');
    
$arrTwigVar['inputSelType'] =  $class->inputSelect('selType', $arrType);
$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"'));
$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"'));   
// $arrTwigVar['inputCarNumber'] =  $class->inputText('carNumber');
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse, array('etc' => 'multiple="multiple"', 'class' => 'multi-selectbox'));
$arrTwigVar['inputSelCar'] = $class->inputSelect('selCarKey[]', $arrCar);
$arrTwigVar['inputSelChassis'] = $class->inputSelect('selChassisKey[]', $arrChassis);
//$arrTwigVar['inputHidCarKey'] = $class->inputHidden('hidCarKey');
//$arrTwigVar['inputStartDate'] = $class->inputDate('trStartDate', array('etc' => 'style="text-align:center"')); 
//$arrTwigVar['inputEndDate'] = $class->inputDate('trEndDate', array('etc' => 'style="text-align:center"')); 
$arrTwigVar['autoLoad'] =  0; 
$arrTwigVar['arrTemplate'] =  $arrHeaderTemplate;

echo $twig->render('reportCarMaintenanceHistory.html', $arrTwigVar);  
 
?>
