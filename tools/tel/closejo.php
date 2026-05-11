<?php
die;

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$sql = 'select * from emkl_job_order_header where  statuskey = 2 and year(trdate) = 2021' ;
$rs = $class->oDbCon->doQuery($sql);
 
foreach($rs as $row) { 
     try{  
        //$class->oDbCon->startTrans();  
             
        $class->startNewErrorLogSession();  
        echo $row['code'].'<br>';
        $errMsg = $emklJobOrder->changeStatus($row['pkey'],3);
        
        print_r($errMsg);
        echo '<br>';
         
        //$class->oDbCon->endTrans(); 
         
       } catch(Exception $e){
           echo $e->getMessage().'<br>';
           //$class->oDbCon->rollback();
    }	
}
  
echo 'done';
 
?>