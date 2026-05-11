<?php
require_once '../../_config.php';
require_once '_include.php';
require_once 'function-v2.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/City.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/CityCategory.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Country.class.php';

$OBJ = new City();
$MODULE_NAME = 'city';
$TITLE = $OBJ->lang['city'];
$AJAX_FILE = 'ajax-api-city';
validateSecurity($OBJ, $MODULE_NAME, $spreadsheet);

$RESET_TABLE = array(
    'city',
);

$country = new Country();
$category = new CityCategory();

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => ''));
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'name'));
array_push($DATA_STRUCTURE, array('field' => 'category_id', 'replace' => array('obj' => $category)));
array_push($DATA_STRUCTURE, array('field' => 'country_id', 'replace' => array('obj' => $country)));
array_push($DATA_STRUCTURE, array('field' => 'status'));

require_once '_import.php';
?>