<?php

die("die, comment open for reset transaction");

include_once '../_config.php';  
include_once '../_include-v2.php';

includeClass(array('Marketplace.class.php','Brand.class.php','ItemCategory.class.php')); 

$sql = 'select * from brand_marketplace_detail where marketplacekey in(2,4)';
$rsBrand = $class->oDbCon->doQuery($sql);

$arrMarketplaceBrandName = array_column($rsBrand,'marketplacebrandname');
	
$dbCon = $class->masterConn();

$sql = 'select marketplacebrandkey,lower(name) as name from marketplace_brand where name in ('.$dbCon->paramString($arrMarketplaceBrandName,',').')';
$rs = $dbCon->doQuery($sql);
$rs = array_column($rs,'marketplacebrandkey','name');


try{			  
		if (!$class->oDbCon->startTrans())
			throw new Exception($class->errorMsg[100]);


		foreach($rsBrand as $row){
			$brandName = strtolower($row['marketplacebrandname']);

			if (!isset($rs[$brandName])){
				echo $brandName .'<br>';
				continue;
			}

			$sql = 'update brand_marketplace_detail set marketplacebrandkey = '.$rs[$brandName].' where pkey = ' .$row['pkey']; 
			$class->oDbCon->execute($sql);;
		}

		$class->oDbCon->endTrans();
			 
				
}catch(Exception $e){
	$class->oDbCon->rollback(); 
}		



echo 'done';
die; 
?>