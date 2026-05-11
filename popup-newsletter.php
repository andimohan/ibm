<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

$_SESSION['newsletterLoaded'] = true;

echo $twig->render('popup-newsletter.html', $arrTwigVar);
?>