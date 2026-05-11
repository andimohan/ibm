<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('WarehouseLayout.class.php'));
$warehouseLayout= new WarehouseLayout();
$obj = $warehouseLayout;    

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
	switch ( $_GET['action']){ 
        case 'getDataLayout' :    
                    
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, '('.$obj->tableName.'.statuskey = 1  )' );  
                    
                    if (isset($_GET) && !empty($_GET['warehousekey']))
                        array_push ($arrCriteria, $obj->tableName.'.warehousekey ='. $obj->oDbCon->paramString($_GET['warehousekey']) ); 

                    if (isset($_GET) && !empty($_GET['istransit']))
                        array_push ($arrCriteria, $obj->tableName.'.istransit ='. $obj->oDbCon->paramString($_GET['istransit']) );  
                    
                    if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                        array_push ($arrCriteria, '('.$obj->tableName.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').' or '.$obj->tableName.'.refcode like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );
                    
                        
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
                
                    $obj->setLog($criteria,true);
                    $rs = $obj->searchData('','',true,$criteria); 
                    $obj->setLog($rs,true);
        
                    echo json_encode($rs); 
        break;
        case 'getDataByWarehouse' :
            $criteria = '';
            $warehousekey = 0;

            if(isset($_GET['warehousekey']) || !empty($_GET['warehousekey'])){
                $warehousekey = $_GET['warehousekey'];
                $criteria = ' and '.$obj->tableName.'.statuskey = 1';
            }

            // if(isset($_GET['istransit']) || !empty($_GET['istransit'])){
            //     $criteria = ' and '.$obj->tableName.'.istransit = 1';
            // }
            // $obj->setLog($_GET, true);
            
            // $rs = $obj->getDataByWarehouse($warehousekey, $criteria);
            // $obj->setLog($rs, true);
        
            echo json_encode($rs); 
        break;

    }
}

 
die;
  
?>