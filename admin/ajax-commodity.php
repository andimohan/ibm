<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('Commodity.class.php');
$commodity = createObjAndAddToCol(new Commodity());

$obj = $commodity;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

die;

?>