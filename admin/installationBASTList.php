<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('InstallationBAST.class.php');   
$installationBAST = createObjAndAddToCol( new InstallationBAST()); 


$obj = $installationBAST;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'installationBASTForm';
$IMPORT_URL = 'import/location';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableName . '.code')); 
array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableSalesOrderSubs . '.code')); 
array_push($arrSearchColumn, array($obj->lang['customer'], $obj->tableCustomer . '.name')); 
array_push($arrSearchColumn, array($obj->lang['warehouse'], $obj->tableWarehouse . '.name')); 

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}

 
include ('dataList.php');

?>
