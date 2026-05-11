<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('SalesOrderRecurringSubscription.class.php');
$salesOrderRecurringSubscription = createObjAndAddToCol(new SalesOrderRecurringSubscription());

$obj = $salesOrderRecurringSubscription;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey in (2,3)');

include 'ajax-general.php';

//    if (isset($_GET) && !empty($_GET['action'])) {
// 			switch ( $_GET['action']){  

//                case 'getDataRowById' :

//                     if (!isset($_GET['pkey'])) die;

//                     $pkey = $_GET['pkey'];

//                     $arrCriteria = array(); 
//                     array_push ($arrCriteria, $obj->tableName.'.pkey = ' .  $obj->oDbCon->paramString($pkey));   
//                     $criteria = implode(' and ', $arrCriteria);  

//                     $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   

//                     $rsData = $obj->searchData('','',true, $criteria);

//                     echo json_encode($rsData);
//                   break; 

//             }
// }

die;

?>