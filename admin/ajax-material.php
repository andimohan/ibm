<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('Material.class.php');
$material = createObjAndAddToCol(new Material());

$obj = $material;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {

    }
}

die;

?>