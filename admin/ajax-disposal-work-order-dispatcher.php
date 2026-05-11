<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('DisposalWorkOrderDispatcher.class.php');
$disposalWorkOrderDispatcher = createObjAndAddToCol(new DisposalWorkOrderDispatcher());

$obj = $disposalWorkOrderDispatcher;

$fieldValue = array('code');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {
        case 'getDetailWithRelatedInformation':
            $pkey = $_GET['pkey'];
            $criteria = array() ;
            if (!empty($_GET['customerKey'])) {
                $customerKey = $_GET['customerKey'];
                array_push($criteria, $obj->tableNameDetail . '.customerkey = ' . $obj->oDbCon->paramString($customerKey));
            }
            $criteria = implode(' and ', $criteria);
            $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';
            $rs = $obj->getDetailWithRelatedInformation($pkey, $criteria);
            echo json_encode($rs);
            break;
    }
}

die;
