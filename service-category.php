<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  
   
$arrParentPath = array();
$cat = 0; 
$rsCat = array();
if ( isset($_GET) && !empty($_GET['cat']) ){
	$cat = $_GET['cat'];
        
	$rsCat = $serviceCategory->getDataRowById($cat);
	
	$arrParentPath[0]['pkey'] = $rsCat[0]['pkey'];
	$arrParentPath[0]['name'] = $rsCat[0]['name']; 
	$parentkey = $rsCat[0]['parentkey'];
	 
	while($parentkey <> 0){ 
		$rsParent = $serviceCategory->getDataRowById($parentkey); 
		$parentkey = $rsParent[0]['parentkey'];
		
		$ctr = count($arrParentPath);
		$arrParentPath[$ctr]['pkey'] =  $rsParent[0]['pkey'];
		$arrParentPath[$ctr]['name'] = $rsParent[0]['name']; 
	} 
} 
$arrTwigVar['categoryPath'] = $arrParentPath;  

     
/* ======================================================== PREPARE DATA ======================================================== */
 
  
for($i=0;$i<count($arrTwigVar['categoryPath']);$i++)  
    array_push($arrActive,$arrTwigVar ['SELF_PAGE'].'?'.$arrTwigVar['categoryPath'][$i]['pkey']);  
 
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action',array( 'value' => 'addToCart')); 
$arrTwigVar ['ACTIVE_MENU'] =  $arrActive; 
$arrTwigVar ['rsCat'] = $rsCat;
  

echo $twig->render('service-category.html', $arrTwigVar);
?>