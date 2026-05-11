<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Consignee.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Location.class.php';   

$OBJ = new Consignee();
$location = new Location();

$MODULE_NAME = 'consignee';
$TITLE = $OBJ->lang['consignee'];
$AJAX_FILE = 'ajax-api-consignee';
validateSecurity($OBJ, $MODULE_NAME, $spreadsheet); 

$RESET_TABLE = array( 
        'consignee',  
); 
 
$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => '')); 
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'name'));
array_push($DATA_STRUCTURE, array('field' => 'address'));
array_push($DATA_STRUCTURE, array('field' => 'location_id' , 'replace' => array('obj' => $location)));  
array_push($DATA_STRUCTURE, array('field' => 'status'));

require_once '_import.php';
?>
