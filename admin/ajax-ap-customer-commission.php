<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('AP.class.php','APCustomerCommission.class.php'));
$apCustomerCommission = createObjAndAddToCol(new APCustomerCommission());
 
$obj = $apCustomerCommission;

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'searchData' :    
                    
                    $order = 'order by '.$obj->tableName.'.code asc';
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, '('.$obj->tableName.'.statuskey = 1 || '.$obj->tableName.'.statuskey = 2  )' );  
                    
                    if (isset($_GET) && !empty($_GET['customerkey']))
                        array_push ($arrCriteria, $obj->tableName.'.customerkey ='. $obj->oDbCon->paramString($_GET['customerkey']) );  
                    
                    if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                        array_push ($arrCriteria, '('.$obj->tableName.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').' or '.$obj->tableName.'.refcode like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );
                    
                    if(isset($_GET) && $_GET['datetype'] == 'apdate'  && !empty($_GET['startdate']) && !empty($_GET['enddate'])){   
                        
                        $dateDiff = $obj->dateDiff($_GET['startdate'],$_GET['enddate']);
                        if ($dateDiff < 0)    $_GET['enddate'] = $_GET['startdate'];
                        
                        array_push($arrCriteria,$obj->tableName.'.trdate between '.$obj->oDbCon->paramString($_GET['startdate']).' AND '.$obj->oDbCon->paramString( $_GET['enddate'].' 23:59:59'));
                    }
                    
                    // BERDASARKAN TANGGAL PEKERJAAN
                    if(isset($_GET) && $_GET['datetype'] == 'jobsdate' && !empty($_GET['startdate']) && !empty($_GET['enddate'])){ 
                        
                        $dateDiff = $obj->dateDiff($_GET['startdate'],$_GET['enddate']);
                        if ($dateDiff < 0)    $_GET['enddate'] = $_GET['startdate'];
                        array_push($arrCriteria,$obj->tableName.'.refdate between '.$obj->oDbCon->paramString($_GET['startdate']).' AND '.$obj->oDbCon->paramString( $_GET['enddate'].' 23:59:59'));
                    }
                    
                    if (isset($_GET) && !empty($_GET['refkey2']))
                        array_push ($arrCriteria, $obj->tableName.'.refkey2 ='. $obj->oDbCon->paramString($_GET['refkey2']) );
                        
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
