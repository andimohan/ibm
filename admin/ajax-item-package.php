<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$obj = $itemPackage;    

$arrCriteria = array();

include 'ajax-general.php';
 
if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                    
                case 'getAvailableConversion' : 
                         
                        if (!isset($_GET) ||  empty($_GET['itemkey']))  
                            die; 
                            
                        $rs = $obj->getItemUnitConversion($_GET['itemkey']);
                        
                        echo json_encode($rs); 
                        break;
           
            }
     
}
die;
  
?>