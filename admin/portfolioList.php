<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('Portfolio.class.php'));
$portfolio = new Portfolio();

$obj = $portfolio;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'portfolioForm';
 
$arrSearchColumn = array(); 	
array_push($arrSearchColumn, array('Kode',$obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama',$obj->tableName . '.name')); 
array_push($arrSearchColumn, array('Kategori',$obj->tableCategory . '.name')); 
array_push($arrSearchColumn, array('Deskripsi Singkat',$obj->tableName . '.shortdesc')); 
		
$arrColumn = array ();  
array_push($arrColumn, array(ucwords($obj->lang['code']),'code',100));
array_push($arrColumn, array(ucwords($obj->lang['title']),'name'));
array_push($arrColumn, array(ucwords($obj->lang['company']),'companyname',300));
array_push($arrColumn, array(ucwords($obj->lang['category']),'categoryname',150)); 
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',70));
   
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';

function generateQuickView($obj,$id){
	
    $detail = '';  
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
