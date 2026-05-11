<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';
 
includeClass(array('ManagementTeam.class.php','ManagementStructure.class.php'));
$obj = new ManagementTeam();
$managementStructure = new ManagementStructure();


$rsStructure = $managementStructure->searchData($managementStructure->tableName.'.statuskey',1,true,'',' order by orderlist asc');

$orderby = 'order by '.$obj->tableName.'.orderlist asc';
$criteria =  ' and '.$obj->tableName.'.statuskey = 1';

foreach($rsStructure as $key=>$row){
    $rsData = $obj->searchData($obj->tableName.'.structurekey',$row['pkey'],true,$criteria,$orderby);
    $rsStructure[$key]['team'] = $obj->updateContentLang($rsData);
}    

$arrTwigVar['rsManagementTeam'] = $rsStructure; 
echo $twig->render('management-team.html', $arrTwigVar);

?>
