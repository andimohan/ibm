<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('DisposalContract.class.php');
$disposalContract = createObjAndAddToCol(new DisposalContract());
$customer = createObjAndAddToCol(new Customer());

$obj = $disposalContract;
 
$fieldValue = $obj->tableName.'.code';
 
$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 2');  

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {

        case 'getWasteDetail':

            if (!isset($_GET['pkey'])) die;
            $pkey = $_GET['pkey'];

            $rsData = $obj->getWasteDetail($pkey);

            echo json_encode($rsData);
            break;
    }
}

die;

?>
