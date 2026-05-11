<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass('TruckingServiceOrder.class.php');
$truckingServiceOrder = createObjAndAddToCol(new TruckingServiceOrder());
$truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE,1));   
$costRate = createObjAndAddToCol(new CostRate());
$chartOfAccount = createObjAndAddToCol(new ChartOfAccount());
$coaLink = createObjAndAddToCol(new COALink());
$warehouse = createObjAndAddToCol(new Warehouse());
    
$obj = $truckingServiceOrder;    

$arrCriteria = array();    
//$fieldValue = $obj->tableName.'.code';

$fieldValue = array('code','donumber');

include 'ajax-general.php';

 
if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){    
                case 'searchDataForInvoice' : 

                    //$returnField = array('key' => $obj->tableName.'.pkey','value' => $fieldValue) ;
                    
                    $returnField = array(
                        'key'   => $obj->tableName . '.pkey',
                        'value' => "CONCAT(" . $obj->tableName . ".code, IF(" . $obj->tableName . ".donumber IS NULL OR " . $obj->tableName . ".donumber = '', '', CONCAT(' - ', " . $obj->tableName . ".donumber)))"
                    );

                    //overwrite field yg di search 
                    $searchFieldValue = (isset($_GET['searchField']) && !empty($_GET['searchField'])) ? explode(',',$_GET['searchField']) : $fieldValue;
                    $searchOptions = array('field' => $searchFieldValue,  'key' => $_GET['term']) ;
  
                    
                    // khusus invoice, utk bedain status DP atau sales
                    $statuskey = "5";
                     
                    if (isset($_GET['statustype']) && !empty($_GET['statustype'])){
                        switch ( $_GET['statustype'] ){     
                             case 'downpayment' :
                                    $statuskey = "2,3,4,5";
                                    break;      
                        }
                    } 
                    
                    $criteria =array();
                    array_push($criteria, $obj->tableName.'.statuskey in ('.$statuskey.')');
                    
                    if(isset($_GET) && !empty($_GET['startdate']) && !empty($_GET['enddate'])){ 
                        array_push($criteria,$obj->tableName.'.trdate between '.$obj->oDbCon->paramString($_GET['startdate']).' AND '.$obj->oDbCon->paramString( $_GET['enddate'].' 23:59:59'));
                    }

                    if(isset($_GET['locationfrom']) && !empty($_GET['locationfrom'])){ 
                        array_push($criteria, $obj->tableName.'.stuffinglocationfromkey = ' . $obj->oDbCon->paramString($_GET['locationfrom']) );
                    }

                    if(isset($_GET['locationto']) && !empty($_GET['locationto'])){ 
                        array_push($criteria, $obj->tableName.'.stuffinglocationkey = ' . $obj->oDbCon->paramString($_GET['locationto']) );
                    }

                    if (isset($_GET['customerkey']) && !empty($_GET['customerkey']))
                        array_push($criteria, $obj->tableName.'.customerkey = ' . $obj->oDbCon->paramString($_GET['customerkey']) );
                    else
                        array_push($criteria, 'false'); // sementara biar kalo gk ad customer, gk ketarik semua invoicenya
                        
                    
                    $criteria = implode (' and ', $criteria);
                       
                    $searchOptions['criteria'] = ' and '. $criteria; 
                   
                    $rsData = $obj->searchDataForAutoComplete($returnField,$searchOptions,$order);
                    echo json_encode($rsData);  
                    
                    break;
                    
                case 'getUnInvoicedItemDetail' :
                    if (empty($_GET['pkey'])) die;
                     
                    $rs = $obj->getUnInvoicedItemDetail($_GET['pkey']); 
                    $rs = $obj->reindexDetailCollections($rs,'refkey');
                    
                    echo json_encode($rs); 
                    break; 
                        
                case 'getCost'  :
                    // gk perlu onchange lokasi, karena lokasi diambil dr JO
                    
                    // $_GET['itemkey'] ==> detailkey sebenernya.
                    
                    if (empty($_GET['pkey'])) die;
                    if (empty($_GET['jobtypekey'])) die;
                    $_GET['itemkey'] =  (empty($_GET['itemkey'])) ? 0 : $_GET['itemkey']; 
                    
                    $rsHeader = $obj->getDataRowById($_GET['pkey']);
                    
                    // get all cost, baik doc atau per item    
                    $rsCost = $truckingCost->searchDataRow(array($truckingCost->tableName.'.pkey'),
                                                           ' and '.$truckingCost->tableName.'.statuskey = 1 
                                                             and showintrucking = 1 and chargetype = 2',
                                                           'order by fixedcost desc, name asc'
                                                          );
                    
                    $rsCostRateCol = $costRate->getCostDetail($rsHeader[0]['warehousekey'],$rsHeader[0]['stuffinglocationkey'], $rsHeader[0]['cargotypekey'], $_GET['jobtypekey'], $_GET['itemkey'] , array_column($rsCost,'pkey'),$rsHeader[0]['consigneekey']);
                    $rsCostRateCol = array_column($rsCostRateCol,null,'costkey');
                     
                    $returnValue = array();
                    for ($i=0;$i<count($rsCost);$i++){   
//                      $rsCostRate = $costRate->getCostDetail($rsHeader[0]['warehousekey'],$rsHeader[0]['stuffinglocationkey'], $rsHeader[0]['cargotypekey'], $_GET['jobtypekey'], $_GET['itemkey'] , $rsCost[$i]['pkey'],$rsHeader[0]['consigneekey']);
                        $rsCostRate = $rsCostRateCol[$rsCost[$i]['pkey']];
                        $returnValue[$rsCost[$i]['pkey']] = (isset($rsCostRate['price']) && !empty($rsCostRate['price'])) ? $rsCostRate['price'] : 0;
                   }
                     
                    echo json_encode($returnValue); 
                    break; 
                    
                    
                case 'getDriverCommission' :
                    // gk perlu onchange lokasi, karena lokasi diambil dr JO
                    
                    if (empty($_GET['pkey'])) die;
                    if (empty($_GET['jobtypekey'])) die;
                    $_GET['itemkey'] =  (empty($_GET['itemkey'])) ? 0 : $_GET['itemkey']; 
                     
                    // get all cost, baik doc atau per item    
                    
                    $rsHeader = $obj->getDataRowById($_GET['pkey']);
                    
                    $rsDriverCommission = $costRate->getDriverCommissionRate($rsHeader[0]['warehousekey'],$rsHeader[0]['stuffinglocationkey'], $rsHeader[0]['cargotypekey'], $_GET['jobtypekey'],  $_GET['itemkey'] , $rsHeader[0]['consigneekey']);  
                    $rsDriverCommission = array_column($rsDriverCommission,'price', 'costkey');
                    
                    echo json_encode($rsDriverCommission); 
                    break;  
                    
                case 'getTotalInvoicedAndOutstanding' :  
                    if (empty($_GET['pkey'])) die;
                    
                    $rs = $obj->getTotalInvoicedAndOutstanding($_GET['pkey'], $_GET['invoiceType']);
                    
                    echo json_encode($rs); 
                    break; 
                    
                      
                case 'getUnCashedCostDetail' :    
                       
                    if (!isset($_GET['pkey']) || empty($_GET['pkey']))  die;
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
                    $criteria .= ' and '.$obj->tableHeaderCost.'.refcashoutkey = 0'; 
                    $rsCost = $obj->getHeaderCost($pkey, $criteria );
                         
                    for($i=0;$i<count($rsCost);$i++){
                        $rsCost[$i]['coakey'] = $coakey;
                        $rsCost[$i]['coaname'] = $rsCOA[$coakey]['coaname'];
                        
                        $rsCost[$i]['name'] = $rsCost[$i]['itemname'];
                         
                        $rsCost[$i]['costvalue'] = $rsCost[$i]['requestamount'];
                        $rsCost[$i]['requestamount'] = $rsCost[$i]['subtotal'];
                    }
                     
                    
                    echo json_encode($rsCost); 
                    break;
                    
               case 'searchAvailableJobOrderForPurchase' : 
                             
                    if(empty($_GET['supplierkey'])){
                        $rsData = array();
                    } else {
                        $_GET['supplierkey'] =  (empty($_GET['supplierkey'])) ? 0 : $_GET['supplierkey'];  
                        
                        $arrCriteria = array();
                        array_push($arrCriteria, $obj->tableName.'.code like ' .  $obj->oDbCon->paramString('%'.$_GET['term'].'%') );
                        
                        $criteria = '';
                        if(!empty($arrCriteria))
                            $criteria = ' and '. implode (' and ', $arrCriteria);
                        
                        $rsData = $obj->searchAvailableJobOrderForPurchase($_GET['supplierkey'],$criteria);
                    } 
                 
                    echo json_encode($rsData);  
                    
                    break;
                    
                    
            }
    
}
die;
  
?>
