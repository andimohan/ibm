<?php

//die("die, comment open for reset transaction");


include_once '../_config.php';  
include_once '../_include-v2.php';

//if(DOMAIN_NAMME != 'trioeaglelogistic.wintera.co.id' ) die ("wrong domain");

ini_set('max_execution_time', 30000000);
ini_set('memory_limit', '2024M');


includeClass(array('EMKLOrderInvoice.class.php')); 

$emklOrderInvoice = new EMKLOrderInvoice(); 

$sql = 'select * from emkl_order_invoice_header
        
        where 
            emkl_order_invoice_header.statuskey in (2,3) and 
            year(emkl_order_invoice_header.trdate) = 2025 and 
            month(emkl_order_invoice_header.trdate) = 1 and 
            taxpercentage = 1.2
        ';
  
$rsInvoice = $class->oDbCon->doQuery($sql);

echo 'total : '.count($rsInvoice).'<br>';

foreach($rsInvoice as $row){
    
    echo $row['code'].'<br>';
    
    $emklOrderInvoice->startNewErrorLogSession();    
    try{   
            $emklOrderInvoice->oDbCon->startTrans(true);
            $result =  $emklOrderInvoice->changeStatus($row['pkey'], 4, 'cancel otomatis oleh sistem, revisi pajak 1.2%',true); 
            $emklOrderInvoice->oDbCon->endTrans();   

    } catch(Exception $e){ 
        foreach($result as $resultRow){
            echo $resultRow['message'].'<br>';
        }
 
        $emklOrderInvoice->setLog($result,true);
    }
}
 

echo 'done';
?>