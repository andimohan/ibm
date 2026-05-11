<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 
require_once '../_global.php';  
  
    
$criteria = '';

$criteria .= ' and ' .$item->tableName.'.statuskey = 1 and isvariant = 0 ';
    
 
if ( isset($_GET) && !empty($_GET['key']) ){ 
    $searchKey = $item->oDbCon->paramString('%'.$_GET['key'].'%');
    $criteria .= ' and ('.$item->tableName.'.name like '.$searchKey.' or '.$item->tableName.'.code like '.$searchKey.' or '.$item->tableName.'.tag like '.$searchKey.')';
}
 

$categorykey = 0;
if ( isset($_GET) && !empty($_GET['categorykey']) ){
    $categorykey = $_GET['categorykey'];
}  

if ($categorykey < 0)
$categorykey = 0;

 
$arrChild  = $itemCategory->getChildren($categorykey);
if (!empty($arrChild)){ 
    $criteria .= ' and categorykey in ('.implode(",",$arrChild).')';
}else{
    $criteria .=  ' and categorykey = ' . $item->oDbCon->paramString($categorykey);
}
 
/* FILTER CRITERIA */
$groupCriteria = '';
$chkHideNoteAvailable = '';
if (isset($_GET) && !empty($_GET['hidHideNotAvailable']) && $_GET['hidHideNotAvailable'] == 1){ 
	$groupCriteria = ' having qtyonhand > 0 ';   
}   


if (isset($_GET) && !empty($_GET['pricerange'])){ 
	$arrPriceRange = explode(',',$_GET['pricerange']);
	 
	if (empty($arrPriceRange[0]) || !is_numeric($arrPriceRange[0]) || $arrPriceRange[0] < 0 )
		$arrPriceRange[0] = 0;
	
	if (empty($arrPriceRange[1]) || !is_numeric($arrPriceRange[1]) || $arrPriceRange[1] < 0 )
		$arrPriceRange[1] = 0;
		
	if ($arrPriceRange[1] < $arrPriceRange[0])
		$arrPriceRange[0] = 0;		 
	
	if ($arrPriceRange[1] > 0){
		$criteria .= ' and sellingprice >= '.$class->oDbCon->paramString($arrPriceRange[0]).' and sellingprice <=  '.$class->oDbCon->paramString($arrPriceRange[1]);  
	}
	
}   


$arrFilter = array();
if (isset($_GET) && !empty($_GET['filterkey'])){   
	 
	$arrFilter = explode(',',$_GET['filterkey']);
	for ($i=0;$i<count($arrFilter);$i++){
		if (!is_numeric($arrFilter[$i])){
				unset($arrFilter[$i]); 
		} 	
	}
	   
}

if ( !empty($arrFilter)){
	
	//pisahkan per kategori
	$rsFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey',1);
	 
	//$arrFilter = array(); 
	$arrIntersect = array();
	for ($i=0;$i<count($rsFilterCategory);$i++){
		$rsItemFilter = $itemFilter->searchData($itemFilter->tableName.'.statuskey',1,true, ' and categorykey = ' . $item->oDbCon->paramString($rsFilterCategory[$i]['pkey']));
	  	$rsItemFilter = array_column($rsItemFilter, 'pkey');
		$rsItemFilter = array_intersect($rsItemFilter,$arrFilter);
		
		if(empty($rsItemFilter))
			continue;
			
		$rsItem = $itemFilter->getItemInFilter($rsItemFilter); 
		$rsItem = array_column($rsItem, 'itemkey'); 
		 
		
		if (empty($arrIntersect)) 
			$arrIntersect = $rsItem;
		 else 
			$arrIntersect = array_intersect($arrIntersect,$rsItem);
		  
	}
	 
	 
	if (!empty($arrIntersect))
		$criteria .= ' and '.$item->tableName.'.pkey in ('.implode(',',$arrIntersect).')';
	else
		$criteria .= ' and false';
}
 

/* END OF FILTER CRITERIA */
 

//$rsItem = $item->searchDataVariantGroup('','',true,$criteria,'order by '.$item->tableName.'.name asc','', $groupCriteria);
$rsItem = $item->searchData('','',true,$criteria,'order by '.$item->tableName.'.name asc','',$groupCriteria);

/* PAGE */
  
//$totalrowsperpage = 7; //$class->loadSetting('productTotalItemPerPage'); 
   
$totalrowsperpage  = 25;

$totalRows = count($rsItem);
$totalPages = ceil( $totalRows / $totalrowsperpage); 

$pageIndex = 0; 
if ( isset($_GET) && !empty($_GET['page']) ){
	$pageIndex = $_GET['page'];
}

$now = $pageIndex * $totalrowsperpage; 
	

/// remove this code for apps
/*
if ($now > $totalRows){
	$now = 0; 
	$pageIndex = 0; 
}*/

 
$limit = '';	
/* END OF PAGE */

$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
   
$rsItem = $item->searchData('','',true,$criteria,'order by '.$item->tableName.'.name asc',$limit,$groupCriteria);
  
for($i=0;$i<count($rsItem);$i++){
		$rsItemImage = $item->getItemImage($rsItem[$i]['pkey']); 
        $rsItem[$i]['mainimage'] = $rsItemImage[0]['file'];	
        $rsItem[$i]['phpThumbHash'] = getPHPThumbHash($rsItemImage[0]['file']);	
	 

        $rsVariant = $item->getVariant($rsItem[$i]['pkey']);
        if (!empty($rsVariant)){ 
            $rsItem[$i]['variant'] = $rsVariant; 
            $rsItem[$i]['sellingprice'] = $rsVariant[0]['sellingprice']; 
        }

    
}


$tempArray = Array();
$arrItem = Array();

for($i=0;$i<count($rsItem);$i++){
    $tempArray['name'] = $rsItem[$i]['name'];
     
    $rsCategory = $itemCategory->getDataRowById($rsItem[$i]['categorykey']);
    $tempArray['categoryname'] = $rsCategory[0]['name'];

    $tempArray['sellingprice'] = floatval($rsItem[$i]['sellingprice']);
    $tempArray['mainimageurl'] = HTTP_HOST.'/phpthumb/phpThumb.php?src='. $class->phpThumbURLSrc.'item/'. $rsItem[$i]['pkey'].'/'. $rsItem[$i]['mainimage'].'&w=500&h=500&hash='. $rsItem[$i]['phpThumbHash'];
    $tempArray['qtyonhand'] = floatval($rsItem[$i]['qtyonhand']);
    
    array_push($arrItem, $tempArray);
}
      


$tempArray = Array();
$tempArray['totalPages'] = floatval($totalPages); 

$rsReturn = Array();
$rsReturn['itemList'] = $arrItem;
$rsReturn['dataInformation'] = $tempArray;

echo json_encode($rsReturn);
 

?>
