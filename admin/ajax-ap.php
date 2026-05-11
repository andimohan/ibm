<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('AP.class.php');
$ap = createObjAndAddToCol(new AP());
$APCommission = createObjAndAddToCol(new APCommission());
 
$obj = $ap;

$paymentStrictToWarehouse = $obj->loadSetting('APPaymentStrictWarehouse');
if($paymentStrictToWarehouse != 1) $_GET['warehousekey'] = '';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'searchData' :     
                    
                    $order = 'order by '.$obj->tableName.'.code asc';
                    
                    $arrCriteria = array(); 
                    array_push ($arrCriteria, '('.$obj->tableName.'.statuskey in(1,2) )' );  
                    
                    if (isset($_GET) && !empty($_GET['supplierkey']))
                        array_push ($arrCriteria, $obj->tableName.'.supplierkey ='. $obj->oDbCon->paramString($_GET['supplierkey']) );  

                    if (isset($_GET) && !empty($_GET['warehousekey']))
                        array_push ($arrCriteria, $obj->tableName.'.warehousekey ='. $obj->oDbCon->paramString($_GET['warehousekey']) );  
                  
                    if (isset($_GET) && !empty($_GET['currencykey']))
                        array_push ($arrCriteria, $obj->tableName.'.currencykey ='. $obj->oDbCon->paramString($_GET['currencykey']) );  
                                        
                    if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                        array_push ($arrCriteria, '('.$obj->tableName.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').' or '.$obj->tableName.'.refcode like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );
                    
                    // kalo TMS, kalo pake trucking purchase, harus cari dulu
                    // kalo active trucking purchase, cari trucking purchae dulu refkey nya ap aj
                     
                    if (isset($_GET) && !empty($_GET['refkey2'])){
                        if( $obj->isActiveModule('TruckingPurchase') ){
                            
                            includeClass('TruckingPurchase.class.php');
                            $truckingPurchase = createObjAndAddToCol(new TruckingPurchase());
                            
                            $rsTruckingPurchaseDetail = $truckingPurchase->getDetailByColumn('sokey', $_GET['refkey2']);
                             
                            // refkey dari detail adalah pkey dari Trucking Purchase
                            // harusnya gk perlu cari yg status bkn cancel, karena kalo cancel, otomatis AP nya udah kecancel jg  
                            $arrAPRefKey = array_column($rsTruckingPurchaseDetail, 'refkey');
                            array_push ($arrCriteria, $obj->tableName.'.refkey in ('. $obj->oDbCon->paramString( $arrAPRefKey, ',').')'); 
                        }else{
                            array_push ($arrCriteria, $obj->tableName.'.refkey2 ='. $obj->oDbCon->paramString($_GET['refkey2']) );
                        }
                    }
                        
                         
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