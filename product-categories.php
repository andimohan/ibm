<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('ItemCategory.class.php')); 
$obj = new ItemCategory(); 


$id = (isset($_GET) && !empty($_GET['id'])) ? $_GET['id'] : 0;  
$rsItemCategory = $obj->searchData($obj->tableName.'.pkey',$id,true,' and '.$obj->tableName.'.statuskey = 1  and '.$obj->tableName.'.isshow = 1'); 
$rsPath = $obj->getPath($id);

// gk boleh redirect, agar bsa akses page root
//
//if(empty($rsItemCategory)){
//    header("location: /");
//    die;
//}

$rsChildCategory = $obj->searchData($obj->tableName.'.parentkey',$rsItemCategory[0]['pkey'],true,'  and '.$obj->tableName.'.statuskey = 1 and '.$obj->tableName.'.isshow = 1'); 

$arrTwigVar['itemCategory']  = $obj->updateContentLang($rsItemCategory);
$arrTwigVar['childCategory']  = $obj->updateContentLang($rsChildCategory);
$arrTwigVar['categoryPath']  = $rsPath; // ini gk bisa spake updateContentLang 
 
echo $twig->render('product-categories.html', $arrTwigVar);
?>