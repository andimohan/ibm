<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass(array('DebitNote.class.php')); 
$obj = new DebitNote();
$ap = new AP();
$APCommission = new APCommission();
    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';
 
if (isset($_GET) && !empty($_GET['action'])) {
    
			switch ( $_GET['action']){  
                
                case 'searchAPForDebitNote' :     
                
                
                    $arrCriteria = array(); 
                    $arrCommissionCriteria = array(); 
                    array_push ($arrCriteria, '('.$ap->tableName.'.statuskey in(1,2,3) )' );  
                    array_push ($arrCommissionCriteria, '('.$APCommission->tableName.'.statuskey in(1,2,3) )' );  
                    
                    if (isset($_GET) && !empty($_GET['supplierkey'])){
                        array_push ($arrCriteria, $ap->tableName.'.supplierkey ='. $obj->oDbCon->paramString($_GET['supplierkey']) );  
                        array_push ($arrCommissionCriteria, $APCommission->tableName.'.supplierkey ='. $obj->oDbCon->paramString($_GET['supplierkey']) );  
                    }

                    if (isset($_GET) && !empty($_GET['warehousekey'])) {
                        array_push ($arrCriteria, $ap->tableName.'.warehousekey ='. $obj->oDbCon->paramString($_GET['warehousekey']) );  
                        array_push ($arrCommissionCriteria, $APCommission->tableName.'.warehousekey ='. $APCommission->oDbCon->paramString($_GET['warehousekey']) );  
                    }
                  
                    if (isset($_GET) && !empty($_GET['currencykey'])) {
                        array_push ($arrCriteria, $ap->tableName.'.currencykey ='. $obj->oDbCon->paramString($_GET['currencykey']) );  
                        array_push ($arrCommissionCriteria, $APCommission->tableName.'.currencykey ='. $APCommission->oDbCon->paramString($_GET['currencykey']) );  
                    }
                                        
                    if ( isset($_GET['term']) && !empty($_GET['term']) ) {
                        array_push ($arrCriteria, '('.$ap->tableName.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').' or '.$ap->tableName.'.refcode like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );
                        array_push ($arrCommissionCriteria, '('.$APCommission->tableName.'.code like '.$APCommission->oDbCon->paramString('%'.$_GET['term'].'%').' or '.$APCommission->tableName.'.refcode like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );
                    }
                    
                    
                    if (isset($_GET) && !empty($_GET['refkey2'])) {
                        array_push ($arrCriteria, $ap->tableName.'.refkey2 ='. $obj->oDbCon->paramString($_GET['refkey2']) );
                        array_push ($arrCommissionCriteria, $APCommission->tableName.'.refkey2 ='. $APCommission->oDbCon->paramString($_GET['refkey2']) );
                    }
                         
                    if(isset($_GET) && !empty($_GET['startdate']) && !empty($_GET['enddate'])){ 
                        $dateDiff = $obj->dateDiff($_GET['startdate'],$_GET['enddate']);
                        if ($dateDiff < 0)    $_GET['enddate'] = $_GET['startdate'];
                        array_push($arrCriteria,$ap->tableName.'.trdate between '.$obj->oDbCon->paramString($_GET['startdate']).' AND '.$obj->oDbCon->paramString( $_GET['enddate'].' 23:59:59'));
                    } 
                            
                        
                    $criteria = implode(' and ', $arrCriteria);  
                    $APCriteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
                    
                    $criteria = implode(' and ', $arrCommissionCriteria);  
                    $commissionCriteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
                    
                    $rs = $obj->searchAPForDebitNote($APCriteria, $commissionCriteria );
 
                    echo json_encode($rs); 
                    break;                    
            }
}


die;
  
?>
