<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PaymentMethod.class.php';

function getNewObj(){
    return new PaymentMethod();
}

$OBJ = getNewObj();  
 
$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'),
    'name' => array('paramName' => 'name'),
));

require_once '_process.php';

?>