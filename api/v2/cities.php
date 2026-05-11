<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CityCategory.class.php';  

$OBJ = new City();

// INPUT QUERY    
$API_FIELDS = array_merge($API_FIELDS,array(
               'name'  =>  array('paramName' => 'name'),  
               'categorykey'  =>  array('paramName' => 'category_name', 'mandatory' => true, 'ref' => array('obj' => new cityCategory())),  
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') ), 
            ));
       
require_once '_process.php';
     
?>