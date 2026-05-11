<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';    

$OBJ = new Brand();
$MODULE_NAME = 'brand';
$TITLE = $OBJ->lang['brand'];
$AJAX_FILE = 'ajax-api-brand';

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => '')); // index 0 gk dipake, karena excel indexnya dari 1
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'name'));
array_push($DATA_STRUCTURE, array('field' => 'is_publish'));
array_push($DATA_STRUCTURE, array('field' => 'status'));
 
require_once '_import.php';
?>