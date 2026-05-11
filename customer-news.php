<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_include-portal.php';

includeClass(array("CustomerNews.class.php"));
$customerNews = new CustomerNews();

$customerNews->oDbCon = $CUSTOMER_CONN;

$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
$arrTwigVar['pageIndex'] =  $pageIndex;

$totalrowsperpage = 25; //$class->loadSetting('productTotalItemPerPage'); //sementara pakai ini dulu

$orderBy = ' order by publishdate desc,pkey desc'; 
$now = $pageIndex * $totalrowsperpage;

$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$criteria =   ' and '.$customerNews->tableName.'.statuskey in (1) and '.$customerNews->tableName.'.publishdate <= now()'; // ini tetep ahrus ad karena status invoice


$rs = $customerNews->searchData('','',true,$criteria,$orderBy,$limit);

$totalPages = ceil( $customerNews->getTotalRows($criteria) / $totalrowsperpage);

$arrTwigVar ['rs'] =  $rs;
$arrTwigVar ['totalPages'] =  $totalPages;
$arrTwigVar['getParameters'] = updateGetParameters();

echo $twig->render('customer-news.html', $arrTwigVar);

?>