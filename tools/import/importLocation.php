<?php
require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/City.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Location.class.php'; 

$OBJ = new Location();
$MODULE_NAME = 'location';
$TITLE = $OBJ->lang['location'];
$AJAX_FILE = 'ajax-api-location';
validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);

$RESET_TABLE = array(
    'location',
);

$city = new City();
$location = new Location();

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => ''));
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'name'));
array_push($DATA_STRUCTURE, array('field' => 'city_id', 'replace' => array('obj' => $city))); 
array_push($DATA_STRUCTURE, array('field' => 'status'));

require_once '_import.php';
?>