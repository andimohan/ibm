<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('TruckingServiceWorkOrder.class.php','ChartOfAccount.class.php','COALink.class.php','Service.class.php','Warehouse.class.php'));

$truckingServiceWorkOrder = createObjAndAddToCol(new TruckingServiceWorkOrder()); 
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
$coaLink = createObjAndAddToCol(new COALink()); 
$obj = $truckingServiceWorkOrder;   
$truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE,1));   
$warehouse =  createObjAndAddToCol(new Warehouse()); 
    
$arrCriteria = array();   

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'getUnCashedCostDetail' :    
                    
                    if (!isset($_GET['pkey']) || empty($_GET['pkey']))  die;
                    if (!isset($_GET['employeekey']) || empty($_GET['employeekey']))  die;
                    if (!isset($_GET['warehousekey']) || empty($_GET['warehousekey']))  die;
                    
                    $pkey = $_GET['pkey'];
                    $employeekey = $_GET['employeekey'];
                    $warehousekey = $_GET['warehousekey'];
                         
                    $rsCOA = $chartOfAccount->searchData($chartOfAccount->tableName.'.statuskey','1');
                    $rsCOA = array_column($rsCOA,null,'pkey');
                    
                    $coakey = 0; 
                    $rsEmployee = $employee->getDataRowById($employeekey);
                    if(!empty($rsEmployee[0]['cashbankcoakey']))
                        $coakey = $rsEmployee[0]['cashbankcoakey'];
 
                    if(empty($coakey)){
                        $rsCOALink = $coaLink->getCOALink ('cashbankops', $warehouse->tableName, $warehousekey,0); 
                        $coakey = $rsCOALink[0]['coakey'];
                    }
                    
                    $criteria = '';
                    $criteria .= ' and '.$obj->tableCost.'.refcashoutkey = 0'; 
                    $criteria .= ' and '.$obj->tableCost.'.refrequestkey = 0'; 
                    $criteria .= ' and '.$obj->tableCost.'.employeekey = ' . $obj->oDbCon->paramString($employeekey); 
                    $rsCost = $obj->getCostDetail($pkey, '', $criteria );
                      
                    for($i=0;$i<count($rsCost);$i++){
                        $rsCost[$i]['coakey'] = $coakey;
                        $rsCost[$i]['costvalue'] = $rsCost[$i]['requestamount'];
                        //$rsCost[$i]['coaname'] = $rsCOA[$coakey]['name'];
                        $rsCost[$i]['coacodename'] = $rsCOA[$coakey]['coaname']; 
                    }
                    
                    // add DP Outsource
                    $rsHeader = $truckingServiceWorkOrder->getDataRowById($pkey);
                    if($rsHeader[0]['outsourcedownpayment'] > 0 && empty($rsHeader[0]['refcashoutdownpaymentkey'])){
                        $arrDPCost = array();
                        $rsCostDP = $truckingCost->getDataRowById(DEFAULT_COST['outsourceDownpayment']);
                        
                        $arrDPCost['pkey'] = 0;
                        $arrDPCost['costkey'] =  DEFAULT_COST['outsourceDownpayment'];
                        $arrDPCost['name'] =  $rsCostDP[0]['name'];
                        $arrDPCost['coakey'] =  $coakey;
                        $arrDPCost['coacodename'] =  $rsCOA[$coakey]['coaname'];
                        $rsCost[$i]['costvalue'] =  $rsHeader[0]['outsourcedownpayment'] ;
                        $arrDPCost['requestamount'] =  $rsHeader[0]['outsourcedownpayment'] ;
                        $arrDPCost['description'] = '' ; 
                        
                        array_push($rsCost, $arrDPCost); 
                    }
                     
                    echo json_encode($rsCost); 
                    break;
                    
                case 'getTruckingCost' : 
                      
                    if (!isset($_GET['pkey']) || empty($_GET['pkey']))
                        die;
                    
                    $pkey = $_GET['pkey'];
                    
                    $criteria = ''; 
                    
                    $rsCost = $obj->getTruckingCost($pkey,$criteria );
                    echo json_encode($rsCost); 
                    break;
        
                case 'searchAvailableItemForPurchase' : 
                              
                    if(empty($_GET['supplierkey']) || empty($_GET['SOKey'])){
                        $rsData = array();
                    } else {
                        $_GET['supplierkey'] =  (empty($_GET['supplierkey'])) ? 0 : $_GET['supplierkey'];  
                        $_GET['SOKey'] =  (empty($_GET['SOKey'])) ? array() : $_GET['SOKey']; 
                        
                        // query langsung job mana saja yg ada tagihannya  
                        $arrSOKey = explode(',',$_GET['SOKey']);
                        $rsData = $obj->searchAvailableItemForPurchase($arrSOKey, $_GET['supplierkey']);
                    } 
                 
                    echo json_encode($rsData);  
                    
                    break;

                case 'getDataForTruckingAdditionalCost' :
                    
                    if ( (!isset($_GET['pkey']) || empty($_GET['pkey'])) &&
                         (!isset($_GET['containernumber']) || empty($_GET['containernumber'])) 
                       ) die;
                    
                    
                    $arrCriteria = array();
                     
                    array_push($arrCriteria, $obj->tableName.'.statuskey  in (2) ' );
                    
                    if(!empty($_GET['pkey']))
                        array_push($arrCriteria, $obj->tableName.'.pkey = '. $obj->oDbCon->paramString($_GET['pkey']) );
                    else if(!empty($_GET['containernumber'])) 
                        array_push($arrCriteria, '  (' . $obj->tableName.'.containernumber = '. $obj->oDbCon->paramString($_GET['containernumber']) . ' or 
                                                        ' . $obj->tableName.'.container2number = '. $obj->oDbCon->paramString($_GET['containernumber']) . '
                                                        )');

                    $criteria = (!empty($arrCriteria)) ?  ' and ' . implode(' and ', $arrCriteria) : '';
                    $rs = $obj->searchData('','',true, $criteria);
   
                    echo json_encode($rs);  

                break;


                case 'getWorkOrderByRefKey' : 
                      
                    if (!isset($_GET['refkey']) || empty($_GET['refkey']))
                        die;
                    
                    $refkey = $_GET['refkey'];
                    
                    $rsWorkOrder = $obj->searchDataRow(array($obj->tableName.'.pkey',$obj->tableName.'.code',$obj->tableName.'.refkey'), ' and ' . $obj->tableName.'.refkey = '. $obj->oDbCon->paramString($refkey) .' and ' . $obj->tableName.'.statuskey = 3');
                    echo json_encode($rsWorkOrder); 
                    break;

                case  'searchDataCarForSalesOrder' :

                    $arrCriteria = array(); 

                    if (isset($_GET['refkey']) && !empty($_GET['refkey'])){  
                        array_push ($arrCriteria, $obj->tableName.'.refkey = '. $obj->oDbCon->paramString($_GET['refkey']));  
                    }

                    if ( isset($_GET['term']) && !empty($_GET['term']) ) {
                        array_push ($arrCriteria, '('.$obj->tableCar.'.policenumber like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );  
                    }


                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 

                    $rs = $obj->searchDataCarForSalesOrder('','',false,$criteria,$order );  

                    echo json_encode($rs); 

                    break;

        //case 'getJobProgressDetail':
//
//
        //            if (
        //                (!isset($_GET['pkey']) || empty($_GET['pkey'])) &&
        //                (!isset($_GET['refkey']) || empty($_GET['refkey']))
        //            )
        //            die;
//
        //            if (!empty($_GET['pkey'])) {
        //                $column = 'pkey';
        //                $value = $_GET['pkey'];
        //            } else {
        //                $column = 'refkey';
        //                $value = $_GET['refkey'];
        //            }
//
        //            $rsJobProgressDetail = $obj->getJobProgressDetail($value, $column);
        //            $obj->setLog($rsJobProgressDetail,true);
        //            $obj->setLog(json_encode($rsJobProgressDetail),true);
        //            echo json_encode($rsJobProgressDetail); 
        //                
        //            break;
    }
            
} 
   

//if (isset($_POST) && !empty($_POST['action'])) { 
//	   switch ($_POST['action']) {
//		    case 'updateJobProgressWorkOrder':	
//
//			$arrData = array();
//			if (isset($_POST) && !empty($_POST['data'])) $arrData = $_POST['data']; 
//
//			$result = $obj->updateJobProgressWorkOrder($arrData);
//			echo json_encode($result);
//
//			break;
//	   }
//	}

die;
  
?>