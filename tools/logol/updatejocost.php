<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$class->oDbCon->startTrans(); 
 
// cari semua JO yg headercost nya realisasinya 0
$sql = 'select pkey,code from trucking_service_order_header where pkey in (
			select refkey from trucking_service_order_header_cost where 
			isrealization = 1 and trucking_service_order_header_cost.amount = 0
		)
		';
$rs = $class->oDbCon->doQuery($sql);

foreach($rs as $row){
	echo $row['code'].'<br>';
	
	$sql = 'select coalesce(sum(trucking_service_order_header_cost.qty * trucking_service_order_header_cost.amount),0) as totalcost
			from trucking_service_order_header_cost
			where trucking_service_order_header_cost.refkey = ' .  $row['pkey'];
	$rscost = $class->oDbCon->doQuery($sql);
	
	
	$sql = 'update trucking_service_order_header,trucking_service_order_header_cost
			set trucking_service_order_header.totalheadercost =  '.$rscost[0]['totalcost'].' where trucking_service_order_header.pkey = ' . $row['pkey'];
	 $class->oDbCon->execute($sql);

}

//$rsSalesOrder =  $truckingServiceOrder->searchData();
//foreach($rsSalesOrder as $row)
//    $truckingServiceOrder->updateSalesWorkOrderCost($row['pkey']);
//    
$class->oDbCon->endTrans();
    
echo 'done';
 
?>