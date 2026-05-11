<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('TicketSupportWorkOrder.class.php');   
$ticketSupportWorkOrder = createObjAndAddToCol( new TicketSupportWorkOrder());


$obj = $ticketSupportWorkOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'ticketSupportWorkOrderForm';
$IMPORT_URL = 'import/location';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableName . '.code')); 
array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableTicket . '.code')); 
array_push($arrSearchColumn, array($obj->lang['subject'], $obj->tableTicket . '.subject')); 
array_push($arrSearchColumn, array($obj->lang['customer'], $obj->tableCustomer . '.name')); 

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}

 
include ('dataList.php');

?>
