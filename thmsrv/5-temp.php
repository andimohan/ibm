<?php   
 
include_once '../_config.php';  
include_once '../_include-v2.php';

if( ! in_array(DOMAIN_NAME, array('thmsrv.local', 'mandy.wintera.co.id') ) ) die;

   
$sql = array();


//GJ84672
array_push($sql, "delete from chart_of_account where code = '6.1.1.01' ");
//array_push($sql, ""); 

//array_push($sql, ""); 



try{ 
    $class->oDbCon->startTrans();

    foreach($sql as $row){
        $class->setLog($row,true);
        $class->oDbCon->execute($row);
    }

    
        
    $class->oDbCon->endTrans(); 
} catch(Exception $e){
    $class->oDbCon->rollback();
    var_dump($e->getMessage()); 
}		


echo 'done';


?>