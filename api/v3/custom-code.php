<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomCode.class.php';    
 
function getNewObj(){ return  new CustomCode(); }

$OBJ = getNewObj();


// INPUT QUERY
// field yang diterima dari parameter API
// convert ke nama parameter kita di class 
 

$API_FIELDS = array_merge(array( 
					'code' =>   array('paramName' => 'code'), 
					'name'  =>  array('paramName' => 'name','updatable' => false),    
					'tablename'  =>  array('paramName' => 'table_name','updatable' => false),    
					'categoryname'  =>  array('paramName' => 'category_name','updatable' => false, 'return' => array('paramName' => 'categoryname')), 
					'statuskey'  =>  array('paramName' => 'status_key'),  
            ),$API_FIELDS);
         
require_once '_process.php';
     
?>
