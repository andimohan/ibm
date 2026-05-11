<?php

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$sql = 'select * from credit_note_header';
$rs = $class->oDbCon->doQuery($sql);

foreach($rs as $row){

    $rsDetail = $creditNote->getDetailById($row['pkey']);
    
    
    foreach($rsDetail as $detailRow){ 
        if(empty($detailRow['invoicekey'])) continue;
     
    
        $class->oDbCon->startTrans(); 
        $sql = ' update credit_note_detail set arkey = ( 
                    select pkey from ar where refkey = '.$detailRow['invoicekey'].' and reftabletype = 315
                ) where pkey = '.$detailRow['pkey'].' 
        ';    
        
        print_r($detailRow);
        echo '<br>';
        echo $sql.'<br>';
    
        $class->oDbCon->execute($sql); 
        
        $class->oDbCon->endTrans();
    }
    
}

    
echo 'done';
 
?>