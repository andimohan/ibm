<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';
 
includeClass(array('CSRCategory.class.php','CSR.class.php'));
$csr = new CSR(); 
$CSRCategory = new CSRCategory(); 
 
$pageIndex = ( isset($_GET) && !empty($_GET['page']) ) ? $_GET['page'] : 0; 
$arrTwigVar ['pageIndex'] =  $pageIndex;

//category 

$rsCSRCategory = $CSRCategory->searchData('','',true, ' and '.$CSRCategory->tableName.'.statuskey = 1','order by orderlist asc'); 
$rsItemImages = $CSRCategory->getItemImages(array_column($rsCSRCategory,'pkey'));  
$rsItemImages = $CSRCategory->reindexDetailCollections($rsItemImages,'refkey');
 
for($i=0;$i<count($rsCSRCategory);$i++) 
    $rsCSRCategory[$i]['image'] = $rsItemImages[$rsCSRCategory[$i]['pkey']];
 
//  kalo gk ad kiriman cat, pake cat pertama
$id = (isset($_GET) && !empty($_GET['id'])) ? $_GET['id'] : 0 ; 
if(empty($id)) $id = $rsCSRCategory[0]['pkey'];
$arrTwigVar['selectedCategoryKey'] = $id;   

$rsAllCSRCategory = $CSRCategory->updateContentLang($rsCSRCategory);  


$orderby = 'order by orderlist asc';
$criteria = ' and '.$csr->tableName.'.statuskey = 1 and '.$csr->tableName.'.categorykey = ' . $csr->oDbCon->paramString($id);
$totalrowsperpage = $class->loadSetting('newsTotalRowsPerPage'); 
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

$rsCSR = $csr->searchData('','',true,$criteria,$orderby,$limit); 
$rsItemImages = $csr->getItemImages(array_column($rsCSR,'pkey'));  
$rsItemImages = $csr->reindexDetailCollections($rsItemImages,'refkey');
for($i=0;$i<count($rsCSR);$i++){
    $rsCSR[$i]['mainimage'] =  $rsItemImages[$rsCSR[$i]['pkey']][0]['file'];
}

$arrTwigVar ['rsCSRCategory'] = $rsAllCSRCategory; 
$arrTwigVar ['rsCSR'] = $csr->updateContentLang($rsCSR);
$arrTwigVar ['rsSelectedCSRCategory'] =  array_column($rsAllCSRCategory,null,'pkey')[$id];   

$totalPages = ceil( $csr->getTotalRows($criteria) / $totalrowsperpage);
$arrTwigVar ['totalPages'] =  $totalPages;

echo $twig->render('csr.html', $arrTwigVar);

?>