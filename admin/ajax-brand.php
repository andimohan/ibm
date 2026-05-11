<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Brand.class.php');
$brand = createObjAndAddToCol(new Brand());

$obj = $brand;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
//                case 'getBrandUsedForShopee' :  
//                    //$marketplacekey = MARKETPLACE['shopee'];
//                    
//                    // langsun saja, agar gk perlu buat object lg
//                    //$rsBrand = $shopee->getBrandUsedForMarketplace($_GET['brandkey'],$_GET['categorykey']);   
//                    $rsBrand = $obj->getMarketplaceBrand($_GET['brandkey'], MARKETPLACE['shopee'], $_GET['categorykey']);
//                    echo json_encode($rsBrand); 
//                    break; 
            }
}


    
die;
  
?>