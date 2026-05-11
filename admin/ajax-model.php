<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('Model.class.php');
$model = createObjAndAddToCol(new Model());

$obj = $model;

$fieldValue = array('code', 'name');

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {


    }
}

die;

?>