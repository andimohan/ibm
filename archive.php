<?php   
// khusus icomunity
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  


echo $twig->render('archive.html', $arrTwigVar);

?>