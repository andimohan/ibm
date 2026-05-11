<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('TruckingSellingRate.class.php');
$truckingSellingRate = createObjAndAddToCol(new TruckingSellingRate());

$obj = $truckingSellingRate;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){   
          
/*                case 'getRatesInformation' :
                    
                    if (empty($_GET['pkey'])) die;
                    
                    $contractKey = $_GET['pkey'];
                    $rs = $obj->searchData($obj->tableName.'.pkey',$contractKey,true);
                    $obj->setLog($rs,true);
                    
                    echo json_encode($rs); 
                    
                    break;*/
                    
                case 'getDetail' : 
                    
                    if (empty($_GET['itemkey'])) die;
                    
                    $_GET['contractkey'] =  (empty($_GET['contractkey'])) ? 0 : $_GET['contractkey']; 
                    $_GET['itemkey'] =  (empty($_GET['itemkey'])) ? 0 : $_GET['itemkey']; 
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, $obj->tableNameDetail.'.refkey = ' . $obj->oDbCon->paramString($_GET['contractkey']) );  
                    array_push ($arrCriteria, $obj->tableNameDetail.'.itemkey = '. $obj->oDbCon->paramString($_GET['itemkey'])); 
           
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
                     
                    $rs = $obj->getDetailByColumn('','',true,$criteria); 
                     
                    echo json_encode($rs); 
                    break; 
                    
 
            }
    
}
 
die;
  
?>