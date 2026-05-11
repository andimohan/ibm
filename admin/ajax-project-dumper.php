<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$obj = $projectDumper;
$arrCriteria = array(); 
include 'ajax-general.php';
  
if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'getLocationInformation' : 
                    if (!isset($_GET) ||  empty($_GET['pkey']) ||  empty($_GET['locationkey']))  
                        die;  
                       
                    $projectkey =  $_GET['pkey']; 
                    $locationkey = $_GET['locationkey']; 
                    $criteria = ' and locationkey = '.$obj->oDbCon->paramString($locationkey).' ';
                    //$rsData = $obj->getDetailByColumn('locationkey',$locationkey);
                    $rsData = $obj->getDetailWithRelatedInformation($projectkey,$criteria);
                    $obj->setLog($rsData,true);
                    echo json_encode($rsData);   
                    break;
                    
                    
                case 'searchDataForInvoice' :   
 
                     if (!isset($_GET) ||  empty($_GET['pkey']))  
                        die;  
                       
                    $projectkey =  $_GET['pkey'];  
                  
                    //if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                        //array_push ($arrCriteria, '('.$obj->tableName.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').' or '.$obj->tableName.'.refcode like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );  
                     
  			       if(isset($_GET) && !empty($_GET['startdate']) && !empty($_GET['enddate'])){  
                       
                        $dateDiff = $obj->dateDiff($_GET['startdate'],$_GET['enddate']);
                        if ($dateDiff < 0)    $_GET['enddate'] = $_GET['startdate'];
                        array_push($arrCriteria,$obj->tableSales.'.trdate between '.$obj->oDbCon->paramString($_GET['startdate']).' AND '.$obj->oDbCon->paramString( $_GET['enddate'].' 23:59:59'));
                    }

                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
              
                    $rs = $obj->getJobInvoice($projectkey,$criteria); 
 
                    echo json_encode($rs); 
                    break; 
                    
                    
                
                    
            }
    
}

die;
  
?>