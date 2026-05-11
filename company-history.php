<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';
 
includeClass(array('CompanyHistory.class.php'));
$obj = new CompanyHistory();
 
$rsHistory = $obj->searchData($obj->tableName.'.statuskey',1,true,'',' order by name asc, orderlist asc');
$rsHistory = $obj->updateContentLang($rsHistory);

$rsHistory = array_column($rsHistory,null,'pkey');
$arrYear = $obj->generateComboboxOpt(array('data' => $rsHistory));

$arrTwigVar['selYear'] = $class->inputSelect('selYear', $arrYear);  
$arrTwigVar['rsHistory'] = $rsHistory;

echo $twig->render('company-history.html', $arrTwigVar);

?>
