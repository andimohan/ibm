<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass("QuizResult.class.php");
$quizResult = new QuizResult();

$obj = $quizResult;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
//$addDataFile = 'quizResultForm';
$quickView = false;
		
$arrSearchColumn = array ();
//array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableName . '.code')); 
array_push($arrSearchColumn, array($obj->lang['name'], $obj->tableName . '.name')); 
array_push($arrSearchColumn, array($obj->lang['phone'], $obj->tableName . '.phone')); 
array_push($arrSearchColumn, array($obj->lang['quiz'], $obj->tableQuiz. '.name')); 
//array_push($arrSearchColumn, array($obj->lang['warehouse'], $obj->tableWarehouse . '.name')); 

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';  

function generateQuickView($obj,$id){ 
	$detail = '';
    
    return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
