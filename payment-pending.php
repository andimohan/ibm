<?php 
include '_config.php'; 
include '_include-fe-v2.php'; 
include '_global.php'; 
     
$arrTwigVar ['title'] =  $class->lang['paymentInformation'];
$arrTwigVar ['content'] =  $class->lang['paymentPendingContent'];

echo $twig->render('page.html', $arrTwigVar);  
?>
