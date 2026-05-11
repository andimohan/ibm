<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomerCategory.class.php';   
 
function getNewObj(){ return  new CustomerCategory(); }

$OBJ = getNewObj();


// INPUT QUERY
// field yang diterima dari parameter API
// convert ke nama parameter kita di class 
 

$API_FIELDS = array_merge(array(
					'requestid'  =>  array('paramName' => 'request_id' ) ,
					'code' =>   array('paramName' => 'code'), 
					'name'  =>  array('paramName' => 'name', 'mandatory' => true),  
					'orderlist'  =>  array('paramName' => 'order_list'), 
	
					'parentname'  =>  array('paramName' => 'parent_category_name','updatable' => false, 'return' => array('paramName' => 'parentname')), 
					'parentkey'  =>  array('paramName' => 'parent_category_id', 'ref' => array('obj' => $OBJ, 'field' => 'code'), 'return' => array('paramName' => 'parentcode')), 
	
					'shortdescription'  =>  array('paramName' => 'short_description'), 
					'description'  =>  array('paramName' => 'description') ,     
                	'statuskey'  =>  array('paramName' => 'status_key'),  
            ),$API_FIELDS);
         
require_once '_process.php';
     
?>