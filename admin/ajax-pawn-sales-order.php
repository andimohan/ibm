<?php 
require_once '../_config.php'; 
require_once '../_include.php';   

$obj = $pawnSalesOrder;    

foreach ($_POST as $k => $v) {

    if (!is_array($v))
         $v = trim($v);  

    $arr[$k] = $v;     

}  

$arr['createdBy'] =  base64_decode($_SESSION[$obj->loginAdminSession]['id']);
$arr['modifiedBy'] =  base64_decode($_SESSION[$obj->loginAdminSession]['id']);   

if (isset($_POST) && !empty($_POST['action'])) {
			switch ($_POST['action']){  
                case 'closedTransaction' :   
                     
                    $status = 3; 
                    if(!$security->isAdminLogin($obj->securityObject,$status,false)) die; 
                    
                    //update status tebus
                    $result = $obj->redeemTransaction($arr);
                        
                    echo json_encode($result); 
                    break;
              
              
                case 'sellTransaction' :   
                    
                    $status = 3; 
                    if(!$security->isAdminLogin($obj->securityObject,$status,false)) die; 
                    
                    //update status tebus
                    $result = $obj->sellTransaction($arr);
                        
                    echo json_encode($result); 
                    break;
                    
                case 'extendTransaction' :   
                    
                    $status = 3; 
                    if(!$security->isAdminLogin($obj->securityObject,$status,false)) die; 
                    
                    //update status tebus
                    $result = $obj->extendTransaction($arr);
                        
                    echo json_encode($result); 
                    break;
             
            }
}
 
die;
  
?>