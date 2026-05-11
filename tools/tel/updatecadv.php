<?php

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashAdvanceRealization.class.php';

$cashAdvanceRealization = new CashAdvanceRealization(); 
$cashAdvance = new CashAdvance(); 
$class->oDbCon->startTrans(); 
$sql = 'select * from '.$cashAdvanceRealization->tableName.' where statuskey <> 4 and refkey IS NOT NULL AND refkey !=\'\' ' ;
$rs = $class->oDbCon->doQuery($sql);
 
foreach($rs as $row) { 
    $pkey = $row['pkey'];
    $cashkey = $row['refkey'];

    $rsCashAdvance = $cashAdvance->getDataRowById($cashkey);
    
	
    $sql = 'insert into '.$cashAdvanceRealization->tableNameDetailAdvance.' (refkey,cashadvancekey,amount) values (
                            '.$pkey.',
                            '.$cashkey.',
                            '.$rsCashAdvance[0]['amount'].'
                            )';

    $class->oDbCon->execute($sql); 
	echo $row['code'].'<br>';
	echo $sql.'<br>';
	$sql = 'update '.$cashAdvanceRealization->tableName.' set  
                cashadvancecache = '.$class->oDbCon->paramString($rsCashAdvance[0]['code']).' where pkey = ' .$pkey;
	$class->oDbCon->execute($sql); 
	echo $sql.'<br>';
   
     
}


$class->oDbCon->endTrans();
    
echo 'done';
 
?>