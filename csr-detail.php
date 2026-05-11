<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('CSR.class.php','Category.class.php','CSRCategory.class.php'));
$obj = new CSR();
$csrCategory = new CSRCategory();

if(empty($_GET)){
	header("location: /");
	die;
}
 
$id = $_GET['id']; 

$rsCSR= $obj->getDataRowById($id, ' and statuskey = 1');

if(empty($rsCSR)){
	header("location: /");
	die;
}

$rsItemImage = $obj->getItemImages($id);

$rsCSRCategory = $csrCategory->searchDataRow(array($csrCategory->tableName.'.pkey',$csrCategory->tableName.'.name'),
                                          ' and '.$csrCategory->tableName.'.pkey = ' . $class->oDbCon->paramString($rsCSR[0]['categorykey']) );
    
// other event ==============

//$totalrowsperpage = $class->loadSetting('newsTotalRowsPerPage'); 
//if($totalrowsperpage == '') $totalrowsperpage = 5;
//
//$rsOtherEvents = $obj->searchDataRow(array($obj->tableName.'.pkey',$obj->tableName.'.title'),
//                                  ' and '.$obj->tableName.'.statuskey = 1 
//                                    and '.$obj->tableName.'.categorykey = '. $obj->oDbCon->paramString($rsEvent[0]['categorykey']) .' 
//                                    and  '.$obj->tableName.'.pkey <> '. $obj->oDbCon->paramString($id) .'
//                                 ','order by pkey desc limit ' . $totalrowsperpage);
//   
//$rsItemImages = $obj->getItemImages(array_column($rsOtherEvents,'pkey')); 
//$rsItemImages = $obj->reindexDetailCollections($rsItemImages,'refkey');
//
//for($i=0;$i<count($rsOtherEvents);$i++){
//    $arrItemImage = $rsItemImages[$rsOtherEvents[$i]['pkey']][0];
//        
//    $rsOtherEvents[$i]['image'] = $arrItemImage['file'];  
//}

$arrTwigVar ['rsCSR'] = $obj->updateContentLang($rsCSR); 
$arrTwigVar ['rsCSRCategory'] = $obj->updateContentLang($rsCSRCategory); 
$arrTwigVar ['rsItemImage'] = $rsItemImage; 
//$arrTwigVar ['rsOtherEvents'] = $obj->updateContentLang($rsOtherEvents); 
$arrTwigVar ['ACTIVE_MENU'] = array('/csr'); 

echo $twig->render('csr-detail.html', $arrTwigVar);
?>