<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('Continent.class.php');
$continent = createObjAndAddToCol(new Continent());

$obj = $continent;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

die;

?>