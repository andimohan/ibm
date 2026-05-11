<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

$arrParentPath = array();
$cat = 0; 
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

    
$criteria = '';

$arrChild  = $serviceCategory->getChildren($cat);
if (!empty($arrChild)){ 
	$catCriteria = ' and categorykey in ('.implode(",",$arrChild).')';
}else{
	$catCriteria =  ' and categorykey = ' . $service->oDbCon->paramString($cat);
}
  
$criteria .= $catCriteria; 
$criteria .= ' and ' .$service->tableName.'.statuskey = 1';
       
$rsServices = $service->searchData('','',true,$criteria,'order by '.$service->tableName.'.orderlist asc , '.$service->tableName.'.name asc');

for($i=0;$i<count($rsServices);$i++){
    $rsItemImage = $service->getItemImage($rsServices[$i]['pkey']); 

    if(!empty($rsItemImage)){ 
        $rsServices[$i]['mainimage'] = $rsItemImage[0]['file'];  
    }
 
}
 

for($i=0;$i<count($arrTwigVar['categoryPath']);$i++)  
    array_push($arrActive,$arrTwigVar ['SELF_PAGE'].'?'.$arrTwigVar['categoryPath'][$i]['pkey']);  

$arrTwigVar ['rsServices'] =  $service->updateContentLang($rsServices);
$arrTwigVar ['ACTIVE_MENU'] =  $arrActive;
$arrTwigVar ['PAGE_NAME'] =  $class->lang['services'];

//$arrTwigVar ['STRUCTURE_DATA'] = $service->generateStructurData($rsServices);   


echo $twig->render('services.html', $arrTwigVar);
?>