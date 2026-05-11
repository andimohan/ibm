<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$obj = $itemMovement;


if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'getSNInformation' :   
                     
                    if(!isset($_GET) || empty($_GET['sn']))
                        die; 
                      
                    $rs = $item->getSNInformation($_GET['sn']); 
                 
                    echo json_encode($rs); 
                    break;
                     
            }
}
die;
  
?>