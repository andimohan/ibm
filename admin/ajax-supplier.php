<?php 
require_once '../_config.php';  
require_once '../_include-v2.php';  

includeClass('Supplier.class.php');
$supplier = createObjAndAddToCol(new Supplier());


if(isset($_GET['suppliertype']) && !empty($_GET['suppliertype'])) {  
    $supplier = new Supplier(explode(',',$_GET['suppliertype'])); 
    unset($_GET['suppliertype']);
}

$obj = $supplier;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  
 
include 'ajax-general.php';
 
if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){ 
                    
                case 'getTaxInformation' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))   die;   
                     $rs = $obj->getTaxInformation($_GET['pkey']);  
                     echo json_encode($rs);
                    
                    break;
                    
		 case 'getSupplierPrice' :
                     
                    if (!isset($_GET) ||  empty($_GET['pkey']))   die;  
                    
					$pkey = $_GET['pkey']; 
					$rsSupplier = $obj->getDataRowById($pkey);
                     
                    $currencykey =  (empty($_GET['currencykey'])) ? CURRENCY['idr'] : $_GET['currencykey']; 
					
					$arrCriteria = array(); 
                    if (isset($_GET['servicekey']) && !empty($_GET['servicekey']))
                        array_push ($arrCriteria, $obj->tableMasterPrice.'.servicekey = ' . $obj->oDbCon->paramString($_GET['servicekey']) );  
                    
                    if (isset($_GET['containerkey']) && !empty($_GET['containerkey']))
						array_push ($arrCriteria, $obj->tableMasterPrice.'.itemkey = '. $obj->oDbCon->paramString($_GET['containerkey'])); 
              
                    if (isset($_GET['locationkey']) && !empty($_GET['locationkey']))
                        array_push ($arrCriteria, $obj->tableMasterPrice.'.locationkey = ' . $obj->oDbCon->paramString($_GET['locationkey']) );  
                    
                 
                    array_push ($arrCriteria, $obj->tableMasterPrice.'.currencykey = ' . $obj->oDbCon->paramString($currencykey) );  
                    
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : ''; 
					
                    $rs = $obj->getDetailWithRelatedInformation($pkey,$criteria);  
                    echo json_encode($rs);
                    
                    break;
            }
}


die;
  
?>
