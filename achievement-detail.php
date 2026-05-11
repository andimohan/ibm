<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('Achievement.class.php'));
$obj = new Achievement();

if(empty($_GET)){
	header("location: /");
	die;
}
 
$id = $_GET['id']; 

$rsAchievement = $obj->getDataRowById($id, ' and statuskey = 1');
if(empty($rsAchievement)){
	header("location: /");
	die;
}

$arrTwigVar ['rsAchievement'] = $obj->updateContentLang($rsAchievement); 

$arrTwigVar ['ACTIVE_MENU'] = array('/achievements.php'); 

echo $twig->render('achievement-detail.html', $arrTwigVar);
?>