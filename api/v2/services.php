<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Lang.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ServiceCategory.class.php';       
 
$OBJ = new Service(SERVICE); 
$serviceCategory = new ServiceCategory(); 

$API_FIELDS = array_merge($API_FIELDS,array(
               'code' =>   array('paramName' => 'code'), 
               'name'  =>  array('paramName' => 'name', 'mandatory' => true),      
               'categorykey'  =>  array('paramName' => 'category_name', 'mandatory' => true, 'ref' => array('obj' => new $serviceCategory())),   
               'shortdescription'  =>  array('paramName' => 'short_description'),     
               'sellingprice'  =>  array('paramName' => 'sellingprice'), 
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') ) 
            ));
       
require_once '_process.php';
     
?>
