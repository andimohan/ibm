<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('JournalBalancing.class.php');
$journalBalancing = createObjAndAddToCol(new JournalBalancing());
$coa = createObjAndAddToCol(new ChartOfAccount());

$obj = $journalBalancing;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'journalBalancingForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Akun', 'coa_0.name'));
array_push($arrSearchColumn, array('Ayat Silang', 'coa_1.name'));
array_push($arrSearchColumn, array('Jumlah', $obj->tableName . '.amount'));
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>