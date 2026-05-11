<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('Country.class.php');
$country = createObjAndAddToCol(new Country());

$obj = $country;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

die;

?>