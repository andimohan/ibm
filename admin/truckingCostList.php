<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Service.class.php'));
$truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE,1));   

$obj = $truckingCost;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'truckingCostForm';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['alias']), $obj->tableName . '.aliasname')); 
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory . '.name')); 

      
$obj->arrDataListAvailableColumn = array(); 
array_push($obj->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
array_push($obj->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
array_push($obj->arrDataListAvailableColumn, array('code' => 'alias','title' => 'alias','dbfield' => 'aliasname','default'=>true, 'width' => 200));
array_push($obj->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 200));
array_push($obj->arrDataListAvailableColumn, array('code' => 'shortDescription','title' => 'shortDescription','dbfield' => 'shortdescription', 'width' => 250 ));  
array_push($obj->arrDataListAvailableColumn, array('code' => 'reimburse','title' => 'reimburse','dbfield' => 'reimburseicon','default'=>true, 'align'=>'center', 'width' => 80));
array_push($obj->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';

function generateQuickView($obj,$id){  
	$detail = ''; 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
