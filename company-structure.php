<?php   
// khusus icomunity
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
$arrTwigVar ['PAGE_NAME'] = $class->lang['companyStructure'];

echo $twig->render('company-structure.html', $arrTwigVar);

?>