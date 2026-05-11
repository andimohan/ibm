<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('BillOfMaterials.class.php'));
$billOfMaterials = new BillOfMaterials();

$obj = $billOfMaterials;   

$arrCriteria = array();

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){ 
                    
                case 'getBOMDetail' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                    
                    $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
                    
                    $arrCriteria = array(); 
                     
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';
                     
                     $rs = $obj->getDetailWithRelatedInformation($pkey,$criteria); 
                     echo json_encode($rs);
                    
                    break;
					
//				case 'getItemBOM' :
//                     
//                    if (!isset($_GET) ||  empty($_GET['pkey']))  
//                        die;  
//                    
//                    $pkey = (empty($_GET['pkey'])) ? 0 : $_GET['pkey'];
//
//                    $arrCriteria = array(); 
//					array_push($arrCriteria, ' and '.$obj->tableName.'.statuskey= 1');
//					array_push($arrCriteria, $obj->tableName.'.itemkey ='.$obj->oDbCon->paramString($pkey));
//                     
//                    $criteria = (!empty($arrCriteria)) ? implode(' and ',$arrCriteria) : '';
//
//                     
//                     $rs = $obj->searchData('','',true,$criteria); 
//						
//					echo json_encode($rs);
//                    
//                    break;
            }
}
 
die;
  
?>
