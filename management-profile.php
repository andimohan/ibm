<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('ManagementTeam.class.php'));
$obj = new ManagementTeam();

if(empty($_GET)){
	header("location: /");
	die;
}
 
$id = $_GET['id']; 

$rsManagement = $obj->getDataRowById($id, ' and statuskey = 1');
if(empty($rsManagement)){
	header("location: /");
	die;
}

$arrTwigVar ['rsManagement'] = $obj->updateContentLang($rsManagement);

echo $twig->render('management-profile.html', $arrTwigVar);
?>