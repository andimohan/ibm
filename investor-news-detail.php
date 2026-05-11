<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';
 
if(empty($_GET)){
	header("location: /");
	die;
}
 
$id = $_GET['id']; 

$rsNews = $investorNews->getDataRowById($id, ' and statuskey = 1');
if(empty($rsNews)){
	header("location: /");
	die;
} 

$rsNews[0]['publishdate'] = $class->convertToLocalTimeZone($rsNews[0]['publishdate'],LOCAL['timezone']['systemGMT'], LOCAL['timezone']['userGMT'] );
$arrTwigVar ['rsNews'] = $investorNews->updateContentLang($rsNews); 
 
// related news
$totallatestrowsperpage = $class->loadSetting('latestNews'); 
$now = 0; //$pageIndex * $totallatestrowsperpage;
$limit = ' limit ' . $now . ', ' . $totallatestrowsperpage; 
$orderby = 'order by rand() ';
$criteria =  ' and '.$investorNews->tableName.'.statuskey = 1 and publishdate <= now()
               and '.$investorNews->tableName.'.categorykey = ' . $class->oDbCon->paramString($rsNews[0]['categorykey']);

$rsRelatedNews = $investorNews->searchData('','',true,$criteria,$orderby,$limit);

$arrTwigVar ['rsRelatedNews'] = $rsRelatedNews;


$arrTwigVar ['META_TITLE'] = (!empty($rsNews[0]['metatitle'])) ? $rsNews[0]['metatitle'] : $rsNews[0]['title'];
$arrTwigVar ['META_DESCRIPTION'] = (!empty($rsNews[0]['metadescription'])) ? $rsNews[0]['metadescription'] : $rsNews[0]['shortdesc'];  
$arrTwigVar ['META_KEYWORDS'] = $rsNews[0]['metakeyword'];  

echo $twig->render('investor-news-detail.html', $arrTwigVar);
?>