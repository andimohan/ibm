<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('BankReconsiliation.class.php'));
$bankReconsiliation = new BankReconsiliation();

$obj = $bankReconsiliation;   
$fieldValue = $obj->tableName.'.code'; 
include 'ajax-general.php';

    
if (isset($_GET) && !empty($_GET['action'])) {
        switch ( $_GET['action']){  

         
            case 'getLastedReconsile' : 
                    if(empty($_GET['coakey']))  die;
                    $rsData = $obj->getLastedReconsile($_GET['coakey']);


                    echo json_encode($rsData); 
                    break; 
         

        }
}

die;
  
?>