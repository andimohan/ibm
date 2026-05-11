<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

// test 
//includeClass("SalesOrder.class.php");
//$salesOrder = createObjAndAddToCol( new SalesOrder()); 

if(!isset($_GET) || empty($_GET['action'])) die;

switch ($_GET['action']){
    case   'getRowHistory' : if (empty($_GET['year']) || empty($_GET['id']) || empty($_GET['tablekey']) ) die;
                          $rs = array();
                          array_push($rs,array('pkey' => $_GET['id']));
                    
                          //$class->setLog($_GET['tablekey'],true);
                          //$obj = $class->getTableNameAndObjById($_GET['tablekey'])['obj'];  
                          //$obj = $class; // kayayna gk guna, class reference kalo gk salah
                          //$obj->tableName = $tableName; // harus object, perlu table statusnya jg
                          $return = $obj->showDataHistory($rs,$_GET['year'],false); 
                          echo $return;
                          break;
}

die;
  
?>