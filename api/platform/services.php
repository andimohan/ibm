<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Lang.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ServiceCategory.class.php';       
 
function getNewObj(){ return new Service(SERVICE); } 
$OBJ = getNewObj();

$serviceCategory = new ServiceCategory(); 

$API_FIELDS = array_merge(array( 
               'name'  =>  array('paramName' => 'name', 'mandatory' => true),      
               'categorykey'  =>  array('paramName' => 'category_name', 'mandatory' => true, 'ref' => array('obj' => new $serviceCategory()), 'return' => array('paramName' => 'categoryname')), 
               'shortdescription'  =>  array('paramName' => 'short_description'),
               'statuskey'  =>  array('paramName' => 'status', 'ref' => array('tableName' => $OBJ->tableStatus, 'field' => 'status') , 'return' => array('isReturn' => false)), 
            ),$API_FIELDS);
       
require_once '_process.php';
     
?>