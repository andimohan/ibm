<?php
include_once '../../_config.php'; 
include_once '../../_include.php';  

$sql = 'select distinct(tablekey.tablename) as tablename,reftabletype from general_journal_header,tablekey where general_journal_header.reftabletype = tablekey.pkey';
$rs = $class->oDbCon->doQuery($sql); 

foreach($rs as $row){
    
    $class->oDbCon->startTrans(); 
    $sql = 'update general_journal_header,'. $row['tablename'].' 
            set general_journal_header.warehousekey = '. $row['tablename'].'.warehousekey
            where general_journal_header.reftabletype = ' . $row['reftabletype'].' and
            general_journal_header.refkey = '. $row['tablename'].'.pkey
            ';
    $class->oDbCon->execute($sql); 
    $class->oDbCon->endTrans(); 

}

echo 'done';
?> 
 