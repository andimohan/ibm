<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('LogisticSalesOrder.class.php');
$logisticSalesOrder = createObjAndAddToCol(new LogisticSalesOrder()); 

$obj = $logisticSalesOrder;

$fieldValue = $obj->tableName.'.code';

//include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {

    switch ($_GET['action']) {

    case 'searchData' :    
                    
                    $order = 'order by '.$obj->tableName.'.code asc';
                    
					$returnField = array('key' => $obj->tableName.'.pkey','value' => $fieldValue) ;
			
					// nonaktifin, karena kalo search pake periode, error.
                    //$searchFieldValue = (isset($_GET['searchField']) && !empty($_GET['searchField'])) ? explode(',',$_GET['searchField']) : $fieldValue;
                    //$searchOptions = array('field' => $searchFieldValue,  'key' => $_GET['term']) ;
 
			
                    $arrCriteria = array();
					// buat search detail
                    array_push ($arrCriteria, $obj->tableName.'.statuskey = 2');  
                  
                    if (isset($_GET) && !empty($_GET['recipientcitykey']))
                        array_push ($arrCriteria, $obj->tableName.'.recipientcitykey ='. $obj->oDbCon->paramString($_GET['recipientcitykey']) );  
                                        
                    if (isset($_GET) && !empty($_GET['transportationkey']))
                        array_push ($arrCriteria, $obj->tableName.'.transportationkey ='. $obj->oDbCon->paramString($_GET['transportationkey']) );  
              
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
                    
                    $searchOptions['criteria'] = ' and '.$criteria; 
                    
					//$obj->setLog($searchOptions,true);
				
                    $rs = $obj->searchDataForAutoComplete($returnField,$searchOptions,$order); 
  
                    echo json_encode($rs); 
                    break;
        
        case 'calculateTotalShippingPrice':
             
            if (!isset($_GET) || empty($_GET['senderCityKey']))  die;
            if (!isset($_GET) || empty($_GET['recipientCityKey']))  die;
            if (!isset($_GET) || empty($_GET['weightDetail']))  die;
            if (!isset($_GET) || empty($_GET['transportationkey']))  die;

            $rsPrice = $obj->calculateTotalShippingPrice($_GET['senderCityKey'], $_GET['recipientCityKey'], $_GET['transportationkey'], $_GET['weightDetail'],$_GET['totalWeight']);

            echo json_encode($rsPrice);
            break;
			
 
    }
}

die; 
