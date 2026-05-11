<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('VatOut.class.php'));
$vatOut = new VatOut();

$obj = $vatOut;   
$fieldValue = $obj->tableName.'.code'; 

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                    
                case 'getTaxServiceCode' : 
                    
                    $criteria = array();
                      
                    array_push($criteria, $obj->tableTaxServiceCode.'.statuskey = 1');

                    if (isset($_GET['term']) && !empty($_GET['term']))  
                        array_push($criteria, $obj->tableTaxServiceCode.'.code like ' . $obj->oDbCon->paramString( '%'. $_GET['term'] . '%') );

                    $criteria = ' and ' . implode (' and ',$criteria);
                    
                    $rs = $obj->getTaxServiceCode($criteria);
                    echo json_encode($rs); 
                    break;
                          
                case 'getTaxServiceUnit' : 
                    
                    $criteria = array();
                      
                    array_push($criteria, $obj->tableTaxServiceUnit.'.statuskey = 1');

                    if (isset($_GET['term']) && !empty($_GET['term']))  
                        array_push($criteria, $obj->tableTaxServiceUnit.'.code like ' . $obj->oDbCon->paramString( '%'. $_GET['term'] . '%') );

                    $criteria = ' and ' . implode (' and ',$criteria);
                    
                    $rs = $obj->getTaxServiceUnit($criteria);
                    echo json_encode($rs); 
                    break;
                     
            }
    
}
 
die;
  
?>