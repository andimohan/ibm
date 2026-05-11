<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$obj = $changeItemSN;    

$fieldValue = $obj->tableSN.'.serialnumber';

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){ 
                    
                case 'searchSN' :
                    
                    if (!isset($_GET) || empty($_GET['sn']))
                        die;
                    
                    $rs = $obj->searchSN($_GET['sn']);
                    echo json_encode($rs); 
                    break;
                
            }
}
 
die;
  
?>