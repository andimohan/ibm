<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';


$newsCategory = new NewsCategory();

if(empty($_GET)){
	header("location: /");
	die;
}
 
$id = $_GET['id']; 

$rsNews = $news->getDataRowById($id, ' and statuskey = 1');
if(empty($rsNews)){
	header("location: /");
	die;
}

$rsCategory = $newsCategory->getDataRowByid($rsNews[0]['categorykey']);

$rsNews[0]['publishdate'] = $class->convertToLocalTimeZone($rsNews[0]['publishdate'],LOCAL['timezone']['systemGMT'], LOCAL['timezone']['userGMT'] );
$arrTwigVar ['rsNews'] = $news->updateContentLang($rsNews); 
$arrTwigVar ['rsNewsCategory'] = $rsCategory; 


// related news
$totallatestrowsperpage = $class->loadSetting('latestNews'); 
$now = 0; //$pageIndex * $totallatestrowsperpage;
$limit = ' limit ' . $now . ', ' . $totallatestrowsperpage; 
$orderby = 'order by rand() ';
$criteria =  ' and '.$news->tableName.'.statuskey = 1 and publishdate <= now()
               and '.$news->tableName.'.categorykey = ' . $class->oDbCon->paramString($rsNews[0]['categorykey']);

$rsRelatedNews = $news->searchData('','',true,$criteria,$orderby,$limit);

$arrTwigVar ['rsRelatedNews'] = $rsRelatedNews;


$arrTwigVar ['META_TITLE'] = (!empty($rsNews[0]['metatitle'])) ? $rsNews[0]['metatitle'] : $rsNews[0]['title'];
$arrTwigVar ['META_DESCRIPTION'] = (!empty($rsNews[0]['metadescription'])) ? $rsNews[0]['metadescription'] : $rsNews[0]['shortdesc'];  
$arrTwigVar ['META_KEYWORDS'] = $rsNews[0]['metakeyword'];  

echo $twig->render('news-detail.html', $arrTwigVar);
?>