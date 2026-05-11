<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('ItemVariation.class.php');
$itemVariation = createObjAndAddToCol(new ItemVariation());

$obj = $itemVariation;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {

    }
}



die;

?>