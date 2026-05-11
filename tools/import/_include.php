<?php

ini_set('max_execution_time', '7200'); //300 seconds = 5 minutes i

$FILE_NAME = basename (  $_SERVER['PHP_SELF'] ,".php"); 

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AutoCode.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomCode.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';   
require_once DOC_ROOT. 'connections/_connection.php';     
require_once '../../assets/vendor/autoload.php';  
 
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);
 
$GLOBALS['ObjCol'] = array();

$class = new Baseclass();
$setting = new Setting();
$security = new Security();  
$employee = new Employee();  

// define plan configuration  
$PLAN_TYPE = $security->getUserPlanType();
$PLAN_TYPE = $PLAN_TYPE[0];   
define('PLAN_TYPE', $PLAN_TYPE);

$MAX_ROWS_LIMIT = 500;

$TABLEKEY_SETTINGS = $class->loadTableKeySettings();
define('TABLENAME_SETTINGS', array_column($TABLEKEY_SETTINGS,null,'tablename'));
define('TABLEKEY_SETTINGS', array_column($TABLEKEY_SETTINGS,null,'pkey'));

function checkMaxRowsLimit($MAX_ROWS_LIMIT){ 
    if ($MAX_ROWS_LIMIT < 0)  
        die('Maximum number of rows exceeded. ('.$MAX_ROWS_LIMIT.')' ); 
}

function createObjAndAddToCol($obj){ 
    $GLOBALS['ObjCol'][$obj->tableName] = $obj;
    return $obj;
}

?>