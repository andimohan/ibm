<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  
  
includeClass('CurrencyRate.class.php');
$currencyRate = createObjAndAddToCol(new CurrencyRate());

$obj = $currencyRate;   

$arrCriteria = array();

include 'ajax-general.php';
if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){ 
                    
                case 'getLastRate' :
                    
                    $currencykey = (isset($_GET['currencykey']) && !empty($_GET['currencykey'])) ? $_GET['currencykey'] : ''; 
                    $trdate = (isset($_GET['trdate']) && !empty($_GET['trdate'])) ? $_GET['trdate'] : ''; 
                    $rs = $obj->getCurrencyLastRate($currencykey,$trdate);  
                    
                    echo json_encode($rs); 
                    break;
            }
                
} 
 
die;
  
?>
