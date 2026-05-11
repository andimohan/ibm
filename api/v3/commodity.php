<?php
require_once '../../_config.php';  
require_once '_include.php';
     
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Commodity.class.php';
 
function getNewObj(){ return  new Commodity(); }

$OBJ = getNewObj();


// INPUT QUERY
// field yang diterima dari parameter API
// convert ke nama parameter kita di class 
 

$API_FIELDS = array_merge(array(
					'requestid'  =>  array('paramName' => 'request_id' ) ,
					'code' =>   array('paramName' => 'code'), 
					'name'  =>  array('paramName' => 'name', 'mandatory' => true), 
                	'statuskey'  =>  array('paramName' => 'status_key'),  
            ),$API_FIELDS);
         
require_once '_process.php';
     
?>
