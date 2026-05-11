<?php
require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Commodity.class.php';

$OBJ = new Commodity();
$MODULE_NAME = 'commodity';
$TITLE = $OBJ->lang['commodity'];
$AJAX_FILE = 'ajax-api-commodity';
validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);

$RESET_TABLE = array(
    'commodity',
);

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => ''));
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'name'));
array_push($DATA_STRUCTURE, array('field' => 'status'));

require_once '_import.php';
?>