<?php  

require_once '../_config.php';
require_once '../_include-v2.php';

if( ! in_array(DOMAIN_NAME, array('thmsrv.local', 'mandy.wintera.co.id') ) ) die;

ini_set('max_execution_time', '3000'); //300 seconds = 5 minutes i

$dbname = 'thomas_prod';


$arrColumn = array('createdon','createdby','modifiedon','modifiedby','confirmedon', 'confirmedby');

$class->oDbCon->startTrans();

foreach($arrColumn as $col){ 

    $sql = 'SELECT * FROM information_schema.columns WHERE table_schema = \''.$dbname.'\'  AND column_name  = \''.$col.'\'' ;
    $rs = $class->oDbCon->doQuery($sql);

    foreach($rs as $row){
        $tableName = $row['TABLE_NAME'];
        $columnName = $row['COLUMN_NAME'];

        if ($tableName == 'cost_rate_header' && $columnName == 'createdon') continue;
        
        //$sql = 'update ' . $tableName.' set '.$col.' = null ';
        $sql = 'alter table ' . $tableName.' drop column '.$col ;
        $class->oDbCon->execute($sql);
    }

}
 
$class->oDbCon->endTrans();

echo 'done';
die;
 
?>