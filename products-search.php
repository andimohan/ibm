<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
    
includeClass(array('Item.class.php')); 
$item = new Item();

if($class->loadSetting('FEProductsPageNeedLogin') == 1){ 
if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
}

if(!IGNORE_QOH)  $warehouseCriteria = ' and iswebqoh = 1';

$pageUrlParam = array();
 
$searchKey = ( isset($_GET) && !empty($_GET['key']) ) ? $_GET['key'] : '';

$arrTwigVar ['searchKey'] =  urlencode(urlencode($searchKey));  
array_push($pageUrlParam,"key=" . $arrTwigVar ['searchKey']);

$searchKey = $class->oDbCon->paramString('%'.$searchKey .'%');   
$criteria = ' and '.$item->tableName.'.statuskey = 1 and isvariant = 0 and ('.$item->tableName.'.name like '.$searchKey.' or '.$item->tableName.'.code like '.$searchKey.' or '.$item->tableName.'.tag like '.$searchKey.')';
    
if (isset($_POST['hidHideNotAvailable'])){ 
 $_SESSION['hidHideNotAvailable'] = $_POST['hidHideNotAvailable']; 
} 
 
$categorykey = ( isset($_GET) && !empty($_GET['categorykey']) ) ? $_GET['categorykey'] : 0;
 
$arrTwigVar ['categorykey'] =  urlencode(urlencode($categorykey));  
array_push($pageUrlParam,"categorykey=" . $arrTwigVar ['categorykey']);

$arrChild  = $itemCategory->getChildren($categorykey);
if (!empty($arrChild)){ 
    $criteria .= ' and categorykey in ('.implode(",",$arrChild).')';
}else{
    $criteria .=  ' and categorykey = ' . $item->oDbCon->paramString($categorykey);
}
      
 
//$rsItemWithoutFilter = $item->searchDataVariantGroup('','',true,$criteria);
$rsItemWithoutFilter = $item->searchData('','',true,$criteria); 
$arrItemKeyWithoutFilter = array_column($rsItemWithoutFilter,'pkey');
 


/* ======================================================== ORDER CRITERIA ======================================================== */

// pastikan terdaftar

$arrOrderBy = array('name' => $class->lang['name'], 'price' => $class->lang['price']);
$arrOrderType = array('1' =>  $class->lang['asc'], '2' =>  $class->lang['desc']);

switch($_GET['orderby']){
    case 'name' : $orderField = $item->tableName.'.name';
                  break;
        
    case 'price' : $orderField = $item->tableName.'.sellingprice';
                    break;
        
    default : $orderField = $item->tableName.'.name';
}

$orderAsc = ($_GET['ordertype'] == 2) ? 'desc' : 'asc'; 

$orderBy = ' order by '.$orderField.' '.$orderAsc; 


// assign ulang 
if(isset($_GET['orderby'])) {
    $_POST['selOrderBy'] = $_GET['orderby'];
    array_push($pageUrlParam,"orderby=". $_GET['orderby']);  
}

if(isset($_GET['ordertype'])){
    $_POST['selOrderType'] = $_GET['ordertype'];
    array_push($pageUrlParam,"ordertype=". $_GET['ordertype']);  
} 

/* ======================================================== FILTER CRITERIA ======================================================== */
$groupCriteria = '';
$chkHideNoteAvailable = '';
if (isset($_GET) && !empty($_GET['hidHideNotAvailable']) && $_GET['hidHideNotAvailable'] == 1){ 
    
    if(!IGNORE_QOH)
	   $groupCriteria = ' having qtyonhand > 0 ';   
    
    $arrTwigVar ['hidHideNotAvailable'] =  1;
    
    array_push($pageUrlParam,"hidHideNotAvailable=1");
}   


if (isset($_GET) && !empty($_GET['pricerange'])){ 
	$arrPriceRange = explode(',',$_GET['pricerange']);
	 
	if (empty($arrPriceRange[0]) || !is_numeric($arrPriceRange[0]) || $arrPriceRange[0] < 0 )
		$arrPriceRange[0] = 0;
	
	if (empty($arrPriceRange[1]) || !is_numeric($arrPriceRange[1]) || $arrPriceRange[1] < 0 )
		$arrPriceRange[1] = 0;
		
	if ($arrPriceRange[1] < $arrPriceRange[0])
		$arrPriceRange[0] = 0;		 
	
    $_POST['priceFrom'] =  $class->formatNUmber($arrPriceRange[0]); 
    $_POST['priceTo'] =  $class->formatNUmber($arrPriceRange[1]); 
    
	if ($arrPriceRange[1] > 0){
		$criteria .= ' and sellingprice >= '.$class->oDbCon->paramString($arrPriceRange[0]).' and sellingprice <=  '.$class->oDbCon->paramString($arrPriceRange[1]);  
	    $arrTwigVar ['priceFrom'] = $arrPriceRange[0]; 
		$arrTwigVar ['priceTo'] =  $arrPriceRange[1]; 
        array_push($pageUrlParam,"pricerange=".$arrTwigVar ['priceFrom'].",".$arrTwigVar ['priceTo']); 
	}
	
}   


$arrFilter = array();
if (isset($_GET) && !empty($_GET['filterkey'])){  
	//$arrTwigVar ['filterkey'] =  $_GET['filterkey']; 
	 
	$arrFilter = explode(',',$_GET['filterkey']);
	for ($i=0;$i<count($arrFilter);$i++){
		if (!is_numeric($arrFilter[$i])) 
				unset($arrFilter[$i]);  
	}
	
	$arrTwigVar ['filterkey'] =  $arrFilter; 
    array_push($pageUrlParam,'filterkey=' . implode(',',$arrTwigVar['filterkey']) );
	  
}
/*
nanti dulu
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
  
 
$rsFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey',1,' order by name asc');
for($i=0;$i<count($rsFilterCategory);$i++){ 
	$rsFilter = $itemFilter->getRelatedFilterByItemKey($rsFilterCategory[$i]['pkey'],$arrItemKeyWithoutFilter);
 	$rsFilterCategory[$i]['filter']	 = $rsFilter;
}
$arrTwigVar ['rsFilterCategory'] =  $rsFilterCategory;*/



if (isset($_GET) && !empty($_GET['brandkey'])){   
    $arrTwigVar ['brandkey'] =  $_GET['brandkey']; 
    array_push($pageUrlParam,"brandkey=" . $_GET['brandkey']);
    $criteria .= ' and brandkey=' . $item->oDbCon->paramString($_GET['brandkey']);
    
    $rsBrand = $brand->getDataRowById($_GET['brandkey']); 
    $arrTwigVar ['searchKey'] = $rsBrand[0]['name'];
}   


/* ======================================================== FILTER CRITERIA ======================================================== */

/*
//if has promo criteria 
$promoType = (isset($_GET['promoType']) && !empty($_GET['promoType'])) ? strtolower($_GET['promoType']) : '';

if(!empty($promoType)){
    
    $arrTwigVar ['ACTIVE_MENU'] = array('/item-promo.php'); // temporary
    
    array_push($pageUrlParam,"promoType=".$promoType);
    $rsVoucher = $voucher->getAvailableVoucher(array(VOUCHER_CATEGORY['sales'],VOUCHER_CATEGORY['shipment']),VOUCHER_TYPE['regular'],CUSTOMER_TYPE['enduser']);
 
    $voucherCriteria = array();
    switch ($promoType){

        case 'all' :    
                        $arrBrand = array_values(array_filter(array_column($rsVoucher,'brandkey')));
                        $arrItem =  array_values(array_filter(array_column($rsVoucher,'itemkey')));
                        $arrCategory =  array_values(array_filter(array_column($rsVoucher,'itemcategorykey')));
             

                        // jika ada salah satu voucher yang tdk ad kriteria, berarti semua item termasuk 
                        // lokasi gk termasuk
                        if (empty($arrBrand) && empty($arrItem) )
                            break; 
                        
             
                        if (!empty($arrBrand))
                            array_push($voucherCriteria, $item->tableName.'.brandkey in ('.$item->oDbCon->paramString($arrBrand,',').')');
                
                         
                        if (!empty($arrItem))
                            array_push($voucherCriteria, $item->tableName.'.pkey in ('.$item->oDbCon->paramString($arrItem,',').')');
                
                        if (!empty($arrCategory)){  
                            $arrCategoryPath = array();
                            
                            foreach($arrCategory as $itemCategoryKey){
                                $rsPath = $itemCategory->getPath($itemCategoryKey); 
                                $arrCategoryPath += array_column($rsPath,'pkey');  

                                if(!in_array($itemCategoryKey,$arrCategoryPath ))
                                    array_push($arrCategoryPath,$itemCategoryKey);  
                            }
                         

                            array_push($voucherCriteria, $item->tableName.'.categorykey in ('.$item->oDbCon->paramString($arrCategoryPath,',').')');
                        }
                         
                        // $arrItemCategory = array_column($row,'itemcategorykey');
                   
                     break;

    }
     
}

if (!empty($voucherCriteria))
    $criteria .= ' and ('.implode(' or ', $voucherCriteria).') ';
*/
    
$rsItem = $item->searchData('','',true,$criteria,$orderBy,'',$groupCriteria,$warehouseCriteria);


/* ======================================================== PAGING ======================================================== */

if (isset($_POST) && !empty($_POST['hidTotalItemPerPage']))  
    $_SESSION['totalItemPerPage'] = $_POST['hidTotalItemPerPage']; 

if (empty($_SESSION['totalItemPerPage']))
	$_SESSION['totalItemPerPage'] = $class->loadSetting('productTotalItemPerPage');
     

$totalrowsperpage = $_SESSION['totalItemPerPage'];
$arrTwigVar ['totalItemPerPage'] = $totalrowsperpage;
  
$totalRows = count($rsItem);
$totalPages = ceil( $totalRows / $totalrowsperpage); 

$pageIndex = ( isset($_GET) && !empty($_GET['page']) ) ? $_GET['page'] : 0; 

$now = $pageIndex * $totalrowsperpage; 
	
if ($now > $totalRows){
	$now = 0; 
	$pageIndex = 0; 
}


$arrTwigVar ['pageIndex'] =  $pageIndex; 


/* ======================================================== PAGING ======================================================== */


/* ======================================================== PREPARE DATA ======================================================== */

$limit = (is_numeric($_SESSION['totalItemPerPage'])) ? ' limit ' . $now . ', ' . $totalrowsperpage : '';
   
$rsItem = $item->searchData('','',true,$criteria,$orderBy,$limit,$groupCriteria,$warehouseCriteria);
  
for($i=0;$i<count($rsItem);$i++){
		$rsItemImage = $item->getItemImage($rsItem[$i]['pkey']); 
        $rsItem[$i]['mainimage'] = $rsItemImage[0]['file'];	
        $rsItem[$i]['linkname'] =  str_replace($class->arrSearch,$class->arrReplace,$rsItem[$i]['name']); 
       
        $rsVariant = $item->getVariant($rsItem[$i]['pkey'],$warehouseCriteria);
        if (!empty($rsVariant)){ 
            $rsItem[$i]['variant'] = $rsVariant; 
            $rsItem[$i]['sellingprice'] = $rsVariant[0]['sellingprice']; 
        }
    

    if (IGNORE_QOH)
        $rsItem[$i]['qtyonhand'] = 99999;

}
 
/* ======================================================== PREPARE DATA ======================================================== */

/* ======================================================== APPLIED DISCOUNT ======================================================== */
 
/*
$rsItem = $discountScheme->appliedDiscountScheme(USERKEY,$rsItem);

foreach($rsItem as $key=>$itemRow) 
    $rsItem[$key]['promo'] = $voucher->checkHasPromo($itemRow);  
*/


/* ======================================================== APPLIED DISCOUNT ======================================================== */
 
$layout = (isset($_SESSION) && !empty($_SESSION['layout'])) ? $_SESSION['layout'] : $class->loadSetting('defaultLayout');  
$layout = (isset($_POST) && !empty($_POST['hidLayout'])) ? $_POST['hidLayout'] : $layout; 

// maslaah utk BKY, layoutnya normal, tp dibuat listing
$defaultQty = 0; // ($layout == 2) ? 0 : 1; 
 
$arrTwigVar ['rsItem'] =  $rsItem;
$arrTwigVar ['totalPages'] =  $totalPages;   
$arrTwigVar ['hidLayout'] =  $class->inputHidden('hidLayout');  
$arrTwigVar ['layout'] = $layout; 
$arrTwigVar ['inputPriceFrom'] = $class->inputNumber('priceFrom', array('etc' => 'style="text-align:right"')); 
$arrTwigVar ['inputPriceTo'] = $class->inputNumber('priceTo', array('etc' => 'style="text-align:right"')); 
$arrTwigVar ['hidTotalItemPerPage'] =  $class->inputHidden('hidTotalItemPerPage');  
$arrTwigVar ['btnUpdateFilter'] =   $class->inputSubmit('btnUpdateFilter',$class->lang['updateSearchFilter'],array('add-class' => 'btnUpdate'));   
$arrTwigVar ['inputHidItemkey'] =  $class->inputHidden('hiditemkey[]');
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action',array( 'value' => 'addToCart'));
$arrTwigVar ['inputQty'] = $class->inputNumber('orderQty[]',array( 'value' => $defaultQty)); 
$arrTwigVar ['inputDisabledQty'] = $class->inputNumber('orderQty[]',array( 'value' => 0, 'disabled' => true ));   
$arrTwigVar ['btnAddToCart'] =   $class->inputSubmit('btnSave', $class->lang['addToCart'], array( 'etc' => ' style="width:100%;"')); 
$arrTwigVar ['btnListSubmit'] =  $class->inputSubmit('btnListSubmit',$class->lang['addToCart']); 
$arrTwigVar ['pageUrlParam'] = (!empty($pageUrlParam)) ? '&'. implode('&',$pageUrlParam) : ''; // you can change it later in html / js, this is just a default variable

$arrTwigVar ['selOrderBy']  = $class->inputSelect('selOrderBy',$arrOrderBy);
$arrTwigVar ['selOrderType']  = $class->inputSelect('selOrderType',$arrOrderType);

$arrTwigVar ['META_DESCRIPTION'] = $class->lang['searchResult'] . ' ' . $_GET['key'];   
$arrTwigVar ['STRUCTURE_DATA'] = $item->generateStructurData($rsItem);  

$_SESSION['layout'] = $layout;

echo $twig->render('products-search.html', $arrTwigVar);
?>