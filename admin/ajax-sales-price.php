<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('SalesPrice.class.php');
$salesPrice = createObjAndAddToCol(new SalesPrice());

$obj = $salesPrice;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {

    switch ($_GET['action']) {
        case 'getSalesPrice':

            if (empty($_GET['customerkey']) || empty($_GET['itemkey']))
                die;

            $customerkey = $_GET['customerkey'];
            $itemkey = $_GET['itemkey'];


            $result = $obj->getSalesPrice($customerkey, $itemkey);

            echo json_encode($result);
            break;
    }

}

die;

?>