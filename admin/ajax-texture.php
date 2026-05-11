<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('Texture.class.php');
$texture = createObjAndAddToCol(new Texture());

$obj = $texture;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {

    }
}



die;

?>