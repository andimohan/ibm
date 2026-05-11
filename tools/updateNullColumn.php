<?php  
die("die");

require_once '../_config.php';
require_once '../_include-v2.php';


ini_set('max_execution_time', '3000'); //300 seconds = 5 minutes i
$dbname = 'wintera_st';

$sql = 'SELECT * FROM information_schema.columns WHERE table_schema = \''.$dbname.'\'  AND column_default IS NULL';
$rs = $class->oDbCon->doQuery($sql);

$class->oDbCon->startTrans();

foreach($rs as $row){
    $tableName = $row['TABLE_NAME'];
    $columnName = $row['COLUMN_NAME'];
    $dataType = $row['DATA_TYPE'];
    
    $defaultValue = '';
    $alterColumn = '';
    
    switch($dataType){
            
        case 'int' : $defaultValue = 0; $alterColumn = 'INT(11)'; break;
        case 'varchar' : $defaultValue = '\'\'';  $alterColumn = 'VARCHAR(255)'; break;
        case 'decimal' : $defaultValue = 0;  $alterColumn = 'DECIMAL(20,7)'; break;
        case 'text' : $defaultValue = '\'\'';  $alterColumn = 'TEXT'; break;
        case 'date' : $defaultValue = '0000-00-00';  $alterColumn = 'DATE'; break;
        case 'datetime' : $defaultValue = '0000-00-00 00:00'; $alterColumn = 'DATETIME';  break;
        case 'tinyint' : $defaultValue = 0;  $alterColumn = 'TINYINT(1)'; break;
        case 'mediumtext' : $defaultValue = '\'\'';  $alterColumn = 'MEDIUMTEXT'; break; 

    
    }
    
    // buat jaga2
    if(empty($alterColumn)) continue;
    
    $sql = 'ALTER TABLE `'.$tableName.'` CHANGE `'.$columnName.'` `'.$columnName.'` '.$alterColumn.'  NULL  DEFAULT '.$defaultValue;
    echo $sql.'<br>';
    $class->oDbCon->execute($sql);
}


$class->oDbCon->endTrans();

die;
 
?>