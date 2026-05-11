<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('AR.class.php','ARPrepaidTax23.class.php'));
$arPrepaidTax23 = createObjAndAddToCol(new ARPrepaidTax23());
 
$obj = $arPrepaidTax23;

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'searchData' :    
                    
                    $order = 'order by '.$obj->tableName.'.code asc';
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, '('.$obj->tableName.'.statuskey = 1 || '.$obj->tableName.'.statuskey = 2  )' );  
                    
                    if (isset($_GET) && !empty($_GET['customerkey']))
                        array_push ($arrCriteria, $obj->tableName.'.customerkey ='. $obj->oDbCon->paramString($_GET['customerkey']) );  
                    

					if (isset($_GET) && !empty($_GET['warehousekey']))
                        array_push ($arrCriteria, $obj->tableName.'.warehousekey ='. $obj->oDbCon->paramString($_GET['warehousekey']) ); 
                    if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                        array_push ($arrCriteria, '('.$obj->tableName.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').' or '.$obj->tableName.'.refcode like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );  
                     
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
              
                    $rs = $obj->searchDataForAutoComplete('','',false,$criteria,$order );
 
                    echo json_encode($rs); 
                    break;
                    
            }
}

die;
  
?>
