<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('DisposalWorkOrder.class.php');
$disposalWorkOrder = createObjAndAddToCol(new DisposalWorkOrder());

$obj = $disposalWorkOrder;

$fieldValue = array('code');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
        case 'getInformationForPurchase':
            $dispatchKey = $_GET['dispatchkey'];
            $rs = $obj->getInformationForPurchase($dispatchKey);
            echo json_encode($rs);
            break;
    }
}

die;
