<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CityCategory.class.php';    
 
$OBJ = new CityCategory();

// INPUT QUERY    
$API_FIELDS = array_merge($API_FIELDS,array(
               'name'  =>  array('paramName' => 'name'), 
               'orderlist'  =>  array('paramName' => 'order_list'), 
               'parentkey'  =>  array('paramName' => 'parent_category', 'ref' => array('obj' => $OBJ)), 
               'shortdescription'  =>  array('paramName' => 'short_description'), 
               'description'  =>  array('paramName' => 'description') ,     
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') ), 
            ));
       
require_once '_process.php';
     
?>