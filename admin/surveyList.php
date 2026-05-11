<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 


$obj = $survey;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'surveyForm';
 
$arrSearchColumn = array(
	'0' => array('Kode', $obj->tableName . '.code'), 
	'1' => array('Pertanyaan', $obj->tableName . '.question') 
); 		 
		
$arrColumn = array (
  '0' => array('Kode','code',70,'true','left'),
  '1' => array('Pertanyaan','question',250,'true','left'), 
  '2' =>  array('Status','statusname','','true','left'),
);   
   
function generateQuickView($obj,$id){ 
	    
	$detail = '';
	   
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>