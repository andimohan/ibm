<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('InvestorRelations.class.php'));

$investorRelations = new InvestorRelations();
 
echo $twig->render('investor-relations.html', $arrTwigVar);

?>