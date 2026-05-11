<?php     

ini_set('display_errors', 1);
ini_set ('max_execution_time', '3000'); // 50 menit ??
error_reporting(E_ALL);

require_once dirname(__FILE__).'/_include-cron.php';

includeClass(array('APIScheduler.class.php'));

$apiScheduler = new APIScheduler();

$rs = $apiScheduler->searchData('','',true, ' and ' .$apiScheduler->tableName.'.statuskey in (1)');
$arrKeys  = array_column($rs,'pkey');

if(empty($rs)) die;

if ($apiScheduler->oDbCon->startTrans(true)){ 
	$sql = 'update '.$apiScheduler->tableName.' set statuskey = 2 where '.$apiScheduler->tableName.'.pkey in (' . $apiScheduler->oDbCon->paramString($arrKeys,',').')';  
	$apiScheduler->oDbCon->execute($sql); 		 

	$apiScheduler->oDbCon->endTrans(); 
}else {
	$apiScheduler->oDbCon->rollback(); 
}


foreach($rs as $row){
	 
	switch ($row['jobtype']){
		
		case 'woowa':	includeClass(array('WooWA.class.php'));
						 $wooWA = new WooWA();
						 $wooWA->execute($row['url'],$row['action'],json_decode($row['payload'],true));
			
						// update status jadi 2, sementara anggap saja selalu berhasil
						//$result = $apiScheduler->changeStatus($row['pkey'],2); // gk bisa pake changeStatus karena perlu akses
					 	
						if ($apiScheduler->oDbCon->startTrans(true)){ 
							$sql = 'update '.$apiScheduler->tableName.' set statuskey = 3 where '.$apiScheduler->tableName.'.pkey = ' . $apiScheduler->oDbCon->paramString($row['pkey']); 
							$apiScheduler->oDbCon->execute($sql); 
							
							$apiScheduler->oDbCon->endTrans(); 
						}else {
							$apiScheduler->oDbCon->rollback(); 
						}
 
						break;
			
		default : break;
			
	}
}

echo 'WA Sent ! ';
	
?>