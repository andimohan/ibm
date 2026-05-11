<?php
require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Country.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Continent.class.php';

$OBJ = new Country();
$MODULE_NAME = 'country';
$TITLE = $OBJ->lang['country'];
$AJAX_FILE = 'ajax-api-country';
validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);

$RESET_TABLE = array(
    'country',
);

$continent = new Continent();

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => ''));
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'name'));
array_push($DATA_STRUCTURE, array('field' => 'continent_id', 'replace' => array('obj' => $continent)));
array_push($DATA_STRUCTURE, array('field' => 'status'));

require_once '_import.php';
?>