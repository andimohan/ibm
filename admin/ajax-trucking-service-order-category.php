<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('TruckingServiceOrderCategory.class.php'));
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();
	
$obj = $truckingServiceOrderCategory;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){   
           
                case 'getProgress' : 
                    
                    if (empty($_GET['categorykey'])) die;
                    
                    $_GET['categorykey'] =  (empty($_GET['categorykey'])) ? 0 : $_GET['categorykey'];  
                   
                    $rs = $obj->getProgress($_GET['categorykey']); 
                     
                    echo json_encode($rs); 
                    break;  
 
            }
    
}
 
die;
  
?>