<?php
require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/City.class.php'; 
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Port.class.php';

$OBJ = new Port();
$MODULE_NAME = 'port';
$TITLE = $OBJ->lang['port'];
$AJAX_FILE = 'ajax-api-port';
validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);

$RESET_TABLE = array(
    'port',
);

$city = new City();
 

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => ''));
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'name'));
array_push($DATA_STRUCTURE, array('field' => 'city_id', 'replace' => array('obj' => $city)));
array_push($DATA_STRUCTURE, array('field' => 'status'));

require_once '_import.php';
?>