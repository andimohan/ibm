<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';   
 
if(!$security->isMemberLogin(false)) 
	header('location:/logout'); 
   
$arrTwigVar['movementkey'] = $_GET['id'];
echo $twig->render('report-item-movement-depot-detail.html', $arrTwigVar);

?>