<?php   
// khusus icomunity
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Category.class.php','Brand.class.php','Subsidiaries.class.php','ItemCategory.class.php'));
$obj = new Brand();
$subsidiaries = new Subsidiaries();
$itemCategory = new ItemCategory();

// BRAND ================

$rsBrands = $obj->searchDataRow(array($obj->tableName.'.pkey',$obj->tableName.'.name',$obj->tableName.'.image',$obj->tableName.'.shortdesc'),
                                ' and '.$obj->tableName.'.statuskey = 1',
                                'order by '.$obj->tableName.'.orderlist asc');

$arrTwigVar ['rsBrands'] = $obj->updateContentLang($rsBrands); 

// SUBSIDIARIES ================

$rsSubsidiaries = $subsidiaries->searchDataRow(array($subsidiaries->tableName.'.pkey',$subsidiaries->tableName.'.name',$subsidiaries->tableName.'.image',$subsidiaries->tableName.'.shortdesc'),
                                ' and '.$subsidiaries->tableName.'.statuskey = 1',
                                'order by '.$subsidiaries->tableName.'.orderlist asc');

$arrTwigVar ['rsSubsidiaries'] =   $subsidiaries->updateContentLang($rsSubsidiaries); 


// ITEM CATEGORY (HRTA) ================ 
//$rsItemCategory = $itemCategory->compileChildArray(true); 
//$arrTwigVar ['rsItemCategory'] =   $subsidiaries->updateContentLang($rsItemCategory); 

 
$rsItemCategory = $itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true, ' and '.$itemCategory->tableName.'.featured = 1', ' order by '.$itemCategory->tableName.'.orderlist asc, '. $itemCategory->tableName.'.name asc ' );
$arrTwigVar['rsItemCategory'] = $rsItemCategory;

 
echo $twig->render('brands.html', $arrTwigVar);

?>