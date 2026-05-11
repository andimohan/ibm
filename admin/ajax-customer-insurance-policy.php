<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('CustomerInsurancePolicy.class.php');
$customerInsurancePolicy = createObjAndAddToCol(new CustomerInsurancePolicy());

$obj = $customerInsurancePolicy;

//$fieldValue = $obj->tableName . '.policynumber';

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

//if (isset($_GET) && !empty($_GET['action'])) {
//    switch ($_GET['action']) {
////        case 'getCustomerInsurancePolicy':
////            if (empty($_GET['pkey'])) die;
////
////            $pkey = $_GET['pkey'];
////
////            $rsCustomerInsurancePolicy = $obj->searchData('','',true,' and '.$obj->tableName.'.pkey = '.$obj->oDbCon->paramString($pkey));
////            //$rsCustomerInsurancePolicy[0]['dateofbirth'] = $obj->formatDBDate($rsCustomerInsurancePolicy[0]['dateofbirth']);
////            //$rsCustomerInsurancePolicy[0]['age'] = $obj->getCustomerPolicysAge($pkey);
////
////            echo json_encode($rsCustomerInsurancePolicy);
////            break;
//    }
//}

include 'ajax-general.php';

die;
?>