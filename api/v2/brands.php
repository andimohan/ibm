<?php
require_once '../../_config.php';  
require_once '_include.php';
    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Marketplace.class.php'; 
  
$OBJ = new Brand();

$API_FIELDS = array_merge($API_FIELDS,array(
               'code' =>   array('paramName' => 'code'), 
               'name'  =>  array('paramName' => 'name', 'mandatory' => true),     
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') ), 
            ));
       
require_once '_process.php';
     
?>