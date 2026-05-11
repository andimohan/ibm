<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('RingSize.class.php');
$ringSized7f      = createObjAndAddToCol(new RingSize());

$obj = $ringSized7f     ;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {

    }
}

die;

?>