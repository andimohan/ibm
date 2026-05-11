<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';  

function getNewObj(){ return  new ItemUnit(); } 
$OBJ = getNewObj();

// INPUT QUERY    
$API_FIELDS = array_merge($API_FIELDS,array(
               'code' =>   array('paramName' => 'code'), 
               'name'  =>  array('paramName' => 'name'),  
               'statuskey'  =>  array('paramName' => 'status_key'), 
            ));
       
require_once '_process.php';
     
?>