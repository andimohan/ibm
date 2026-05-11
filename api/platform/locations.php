<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Location.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CityCategory.class.php';  

function getNewObj(){ return  new Location(); } 
$OBJ = getNewObj();

// INPUT QUERY    
$API_FIELDS = array_merge(array(
               'name'  =>  array('paramName' => 'name'),  
               'citykey'  =>  array('paramName' => 'city_name', 'ref' => array('obj' => new City()), 'return' => array('paramName' => 'cityname')),  
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') , 'return' => array('isReturn' => false)), 
            ),$API_FIELDS);
       
require_once '_process.php';
     
?>