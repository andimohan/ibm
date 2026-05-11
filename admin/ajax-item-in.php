<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$obj = $itemIn;    

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){ 
                    
                case 'getRelatedInformation' :
                    
                    if (!isset($_GET) || empty($_GET['pkey']))
                        die;
                    
                    $rs = $obj->searchData($obj->tableName.'.pkey',$_GET['pkey'],true);
                    echo json_encode($rs); 
                    break;
                    
               
                case 'getOutstandingDetail' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                    $pokey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, 'qtyinbaseunit > receivedqtyinbaseunit' );  
                    
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';
                     
                     $rs = $obj->getDetailWithRelatedInformation($pokey,$criteria); 
                     echo json_encode($rs);
                    
                    break;
            }
}
 
die;
  
?>