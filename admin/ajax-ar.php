<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('AR.class.php');
$ar = createObjAndAddToCol(new AR());
 
$obj = $ar;

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'searchData' :    
                    
                    $order = 'order by '.$obj->tableName.'.code asc';
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, '('.$obj->tableName.'.statuskey = 1 || '.$obj->tableName.'.statuskey = 2  )' );  
                    
                    if (isset($_GET) && !empty($_GET['customerkey']))
                        array_push ($arrCriteria, $obj->tableName.'.customerkey ='. $obj->oDbCon->paramString($_GET['customerkey']) );  
                                        
                    if (isset($_GET) && !empty($_GET['currencykey']))
                        array_push ($arrCriteria, $obj->tableName.'.currencykey ='. $obj->oDbCon->paramString($_GET['currencykey']) );  
              
                    if (isset($_GET) && !empty($_GET['warehousekey']))
                        array_push ($arrCriteria, $obj->tableName.'.warehousekey ='. $obj->oDbCon->paramString($_GET['warehousekey']) );  
                  
                    if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                        array_push ($arrCriteria, '('.$obj->tableName.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').' or '.$obj->tableName.'.refcode like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );  
                     
  			       if(isset($_GET) && !empty($_GET['startdate']) && !empty($_GET['enddate'])){ 
                       
                        $dateDiff = $obj->dateDiff($_GET['startdate'],$_GET['enddate']);
                        if ($dateDiff < 0)    $_GET['enddate'] = $_GET['startdate'];
                        array_push($arrCriteria,$obj->tableName.'.trdate between '.$obj->oDbCon->paramString($_GET['startdate']).' AND '.$obj->oDbCon->paramString( $_GET['enddate'].' 23:59:59'));
                    }

                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
              
                    //$obj->setLog($criteria,true);
                    $rs = $obj->searchDataForAutoComplete('','',false,$criteria,$order ); 
 
                    echo json_encode($rs); 
                    break;
                    
            }
}

die;
  
?>
