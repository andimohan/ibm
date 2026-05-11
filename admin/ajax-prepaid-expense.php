<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('PrepaidExpense.class.php');
$prepaidExpense = createObjAndAddToCol(new PrepaidExpense());
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice());
 
$obj = $prepaidExpense;

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'searchData' :    

                    $order = 'order by '.$obj->tableName.'.code asc';
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, '('.$obj->tableName.'.statuskey = 1 || '.$obj->tableName.'.statuskey = 2  )' );  
           
                    if (isset($_GET) && !empty($_GET['currencykey']))
                        array_push ($arrCriteria, $obj->tableName.'.currencykey ='. $obj->oDbCon->paramString($_GET['currencykey']) );  
                    
                    if (isset($_GET) && !empty($_GET['invoicekey'])){
                        $rsDetail = $emklOrderInvoice->getDetailById($_GET['invoicekey']);
                        
                        $arrJoKey = array();
                        $rsDetailCol = $obj->reindexDetailCollections($rsDetail,'refsalesorderheaderkey');    
                        foreach($rsDetailCol as $key=>$row) 
                            $arrJoKey[$key] = implode('',array_column($row,'refsalesorderheaderkey'));  
    
                    
                        array_push ($arrCriteria, $obj->tableName.'.salesorderkey in ('. $obj->oDbCon->paramString($arrJoKey,',').')' );  

                    }
                  
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

                    $rs = $obj->searchDataForAutoComplete('','',false,$criteria,$order ); 
                    echo json_encode($rs); 
                    break;
                    
            }
}

die;
  
?>
