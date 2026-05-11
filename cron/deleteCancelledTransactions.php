<?php
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

$class->oDbCon->startTrans();

$lastYear = date('Y');

$year = (isset($_GET) && !empty($_GET['year'])) ? $_GET['year'] : $lastYear; 


$arrSQL = array();

array_push($arrSQL, 'delete from general_journal_header where statuskey = 4 and trdate < \'' . $year . '-01-01\'');
array_push($arrSQL, 'delete from general_journal_detail where refkey not in (select pkey from general_journal_header)');

array_push($arrSQL, 'delete from emkl_purchase_order_header where statuskey = 4 and trdate < \'' . $year . '-01-01\'');
array_push($arrSQL, 'delete from emkl_purchase_order_detail where refkey not in (select pkey from emkl_purchase_order_header)');
array_push($arrSQL, 'delete from emkl_purchase_order_payment where refkey not in (select pkey from emkl_purchase_order_header)');

try { 

		if(!$class->oDbCon->startTrans(true))
			throw new Exception($class->errorMsg[100]);

		foreach($arrSQL as $row){ 
			echo $row.'<br>';
			$class->oDbCon->execute($row);
		}

		$class->oDbCon->endTrans();   

	} catch(Exception $e){  
		$class->oDbCon->rollback();  
	}		
				  


die("done");
?>