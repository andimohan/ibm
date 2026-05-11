<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$obj = $customerMembership;   

$arrCriteria = array();  
//array_push ($arrCriteria, $obj->tableName.'.statuskey = 2');  
$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
        switch ( $_GET['action']){            
                case 'getCustomer' :
                    
                    if (!isset($_GET) || empty($_GET['customerkey']))
                        die;
                    
                    $rs = $obj->searchData($obj->tableName.'.customerkey',$_GET['customerkey'],true, 'and '.$obj->tableName.'.statuskey = 2');
                    echo json_encode($rs); 
                    break;
		}
 
}
die;
  
?>