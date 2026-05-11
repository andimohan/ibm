<?php 
die;

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 


$sql = 'select pkey,code,headerorderkey from emkl_job_order_header where year(trdate) = 2021' ;
$rs = $class->oDbCon->doQuery($sql);
 
foreach($rs as $row) { 
     try{  
         $class->oDbCon->startTrans(true);  
         echo $row['code'].'<br>';
         
         if (empty($row['headerorderkey'])) continue;
             
         $sql = 'select * from emkl_order_detail where refkey = ' .$row['headerorderkey']; // headerkey => pkey job_header
         $rsCont = $class->oDbCon->doQuery($sql);
        
         $sql = 'delete from emkl_job_order_detail_volume where refkey = '.$row['pkey'];
         $class->oDbCon->execute($sql);
         
         foreach($rsCont as $contDetailRow){
            $sql = 'insert into emkl_job_order_detail_volume (refkey,itemkey,qty) values ('.$row['pkey'].','.$contDetailRow['itemkey'].','.$contDetailRow['qty'].')'; 
            $class->oDbCon->execute($sql);
         }
         
       $class->oDbCon->endTrans(); 
         
       } catch(Exception $e){
           echo $e->getMessage().'<br>';
           $class->oDbCon->rollback();
    }	
}
  
echo 'done';
 
?>