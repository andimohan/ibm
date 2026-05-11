<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';
 
includeClass(array("Achievement.class.php"));
$achievement = new Achievement();

$pageIndex = 0;
if ( isset($_GET) && !empty($_GET['page']) ){
	$pageIndex = $_GET['page'];
}
$arrTwigVar ['pageIndex'] =  $pageIndex;

//news 
$totalrowsperpage = $class->loadSetting('newsTotalRowsPerPage'); 
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$orderby = 'order by publishdate desc';
$criteria =  ' and '.$achievement->tableName.'.statuskey = 1 and publishdate <= now()';
 
$rsAchievement = $achievement->searchData('','',true,$criteria,$orderby,$limit);
$arrTwigVar ['rsAchievement'] =  $achievement->updateContentLang($rsAchievement); 
  
$totalPages = ceil( $achievement->getTotalRows($criteria) / $totalrowsperpage);
$arrTwigVar ['totalPages'] =  $totalPages;

$rsAchievementFeatured = $achievement->searchData($achievement->tableName.'.statuskey',1,true,' and  '.$achievement->tableName.'.featured = 1',' order by ' .$achievement->tableName.'.publishdate desc, ' .$achievement->tableName.'.pkey desc  limit ' . $class->loadSetting('latestNews') );
$arrTwigVar['rsFeaturedAchievement'] = $achievement->updateContentLang($rsAchievementFeatured);   


echo $twig->render('achievements.html', $arrTwigVar);

?>