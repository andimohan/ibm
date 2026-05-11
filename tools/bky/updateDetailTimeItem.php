<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php';

$class->oDbCon->startTrans();
 
$sql = 'select * from item where pkey not in (select refkey from item_detail_time)';
$rsItem = $class->oDbCon->doQuery($sql);

$timeUnitKey = $timeUnit->getDefaultData();

 
$totalAR = 0;

echo '<table>';
foreach($rsItem as $row){
	echo $row['code'].'<bR>';
    $sql = 'insert into item_detail_time(refkey,timeunitkey,sellingprice)values('.$class->oDbCon->paramString($row['pkey']).','.$class->oDbCon->paramString($timeUnitKey).',1)
            ';
    
$class->oDbCon->execute($sql);
	echo $row['code'].'Data berhasil Masuk <bR>';
}
$class->oDbCon->endTrans();
echo '<bR><br>done';
?>