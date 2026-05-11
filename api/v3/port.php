<?php
require_once '../../_config.php';  
require_once '_include.php';
    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';   
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Port.class.php';
 
function getNewObj(){ return  new Port(); }

$OBJ = getNewObj();


// INPUT QUERY
// field yang diterima dari parameter API
// convert ke nama parameter kita di class 
 

$API_FIELDS = array_merge(array(
					'requestid'  =>  array('paramName' => 'request_id' ) ,
					'code' =>   array('paramName' => 'code'), 
					'name'  =>  array('paramName' => 'name', 'mandatory' => true),   
					'cityname' => array('paramName' => 'city_name', 'updatable' => false, 'return' => array('paramName' => 'cityname')),
					'citykey' => array('paramName' => 'city_id', 'ref' => array('obj' => new City(), 'field' => 'code'), 'return' => array('paramName' => 'citycode')), 
                	'statuskey'  =>  array('paramName' => 'status_key'),  
            ),$API_FIELDS);
         
require_once '_process.php';
     
?>
