<?php   
//die("die");

include_once '../_config.php';  
include_once '../_include-v2.php';
  
if(DOMAIN_NAME != 'mti.wintera.co.id') die;

includeClass(array('TruckingServiceWorkOrder.class.php', 'TruckingCostCashOut.class.php', 'TruckingServiceOrder.class.php'));
$spk = new TruckingServiceWorkOrder();
$cashOut = new TruckingCostCashOut();
$jo = new TruckingServiceOrder();

$sql = 'select * from trucking_service_work_order where statuskey = 1';
$rs = $class->oDbCon->doQuery($sql);

$pkeyTemp = 0;
 try{ 

    $class->oDbCon->startTrans(true);
    foreach($rs as $row){ 
        $pkeyTemp = $row['pkey'];  
        $spk->changeStatus($row['pkey'] ,2);
    }

    $class->oDbCon->endTrans(); 

} catch(Exception $e){
    $class->oDbCon->rollback();
    echo 'trucking_service_work_order => 2<br>';
    echo $pkeyTemp.'<br>'; 
    var_dump($e->getMessage());
    echo '<br>';
}	


$sql = 'select * from trucking_cost_cash_out_header where statuskey = 1';
$rs = $class->oDbCon->doQuery($sql);
 
$pkeyTemp = 0;
 try{ 

    $class->oDbCon->startTrans(true);
    foreach($rs as $row){
        $pkeyTemp = $row['pkey'];  
        $cashOut->changeStatus($row['pkey'] ,3);
    }

    $class->oDbCon->endTrans(); 

} catch(Exception $e){
    $class->oDbCon->rollback();
    echo 'trucking_cost_cash_out_header => 1<br>'; 
    echo $pkeyTemp.'<br>'; 
    var_dump($e->getMessage());
    echo '<br>';
}	

$sql = 'select * from trucking_service_work_order where statuskey = 2';
$rs = $class->oDbCon->doQuery($sql);
 
$pkeyTemp = 0;
 try{ 

    $class->oDbCon->startTrans(true);
    foreach($rs as $row){
        $pkeyTemp = $row['pkey'];  
        $spk->changeStatus($row['pkey'] ,3);
    }

    $class->oDbCon->endTrans(); 

} catch(Exception $e){
  $class->oDbCon->rollback();
  echo 'trucking_service_work_order => 3<br>';
echo $pkeyTemp.'<br>'; 
var_dump($e->getMessage());
echo '<br>';
}	


$sql = 'select * from trucking_service_order_header where statuskey = 3';
$rs = $class->oDbCon->doQuery($sql);
 
$pkeyTemp = 0;
 try{ 

    $class->oDbCon->startTrans(true);
    foreach($rs as $row){
        $pkeyTemp = $row['pkey'];  
        $jo->changeStatus($row['pkey'] ,5);
    }

    $class->oDbCon->endTrans(); 

} catch(Exception $e){
    $class->oDbCon->rollback();
     
    echo 'trucking_service_order_header => 3<br>';
echo $pkeyTemp.'<br>'; 
var_dump($e->getMessage());
echo '<br>';
}	

echo 'done';

?>