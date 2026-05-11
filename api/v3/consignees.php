<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Consignee.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Location.class.php';   
 
function getNewObj(){ return  new Consignee(); }

$OBJ = getNewObj();


// INPUT QUERY
// field yang diterima dari parameter API
// convert ke nama parameter kita di class 
 

$API_FIELDS = array_merge(array(
                'requestid'  =>  array('paramName' => 'request_id' ) ,
                'code' =>   array('paramName' => 'code'), 
                'name'  =>  array('paramName' => 'name', 'mandatory' => true),    
                'locationame'  => array('paramName' => 'location_name','updatable' => false, 'return' => array('paramName' => 'locationname')), 
                'locationkey'  =>  array('paramName' => 'location_id', 'ref' => array('obj' => new Location(), 'field' => 'code'), 'return' => array('paramName' => 'locationcode')), 
                'address'  =>  array('paramName' => 'address'), 
                'warehousename'  =>  array('paramName' => 'warehouse_name'), 
                'statuskey'  =>  array('paramName' => 'status_key'),  
            ),$API_FIELDS);
         
require_once '_process.php';
     
?>