<?php   
// khusus icomunity
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Brand.class.php'));
$obj = new Brand();

// BRAND ================

if(empty($_GET)){
   $rsBrand = $obj->searchData($obj->tableName.'.statuskey',1, '', ' order by '.$obj->tableName.'.orderlist limit 1');
}else{
    $id = $_GET['id'];  
    $rsBrand = $obj->getDataRowById($id, ' and statuskey = 1');
}
 

if(empty($rsBrand)){
    header("location: /");
    die;
}


$arrTwigVar ['rsBrand'] = $obj->updateContentLang($rsBrand); 


$rsAllBrands = $obj->searchDataRow(array($obj->tableName.'.pkey',$obj->tableName.'.name',$obj->tableName.'.image',$obj->tableName.'.shortdesc'),
                                ' and '.$obj->tableName.'.statuskey = 1',
                                'order by '.$obj->tableName.'.orderlist asc');

$arrTwigVar ['rsAllBrands'] = $obj->updateContentLang($rsAllBrands); 

echo $twig->render('brand-detail.html', $arrTwigVar);

?>