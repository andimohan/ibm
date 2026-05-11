<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  

echo $twig->render('popup-banner.html', $arrTwigVar);
?>