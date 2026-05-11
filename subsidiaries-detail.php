<?php   
// khusus icomunity
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Subsidiaries.class.php'));
$obj = new Subsidiaries();

// BRAND ================

if(empty($_GET)){
   $rsSubsidiaries = $obj->searchData($obj->tableName.'.statuskey',1, '', ' order by '.$obj->tableName.'.orderlist limit 1');
}else{
    $id = $_GET['id'];  
    $rsSubsidiaries = $obj->getDataRowById($id, ' and statuskey = 1');
}
 
  
if(empty($rsSubsidiaries)){
	header("location: /");
	die;
}


$arrTwigVar ['rsSubsidiaries'] = $obj->updateContentLang($rsSubsidiaries); 


$rsAllSubsidiaries = $obj->searchDataRow(array($obj->tableName.'.pkey',$obj->tableName.'.name',$obj->tableName.'.image',$obj->tableName.'.shortdesc'),
                                ' and '.$obj->tableName.'.statuskey = 1',
                                'order by '.$obj->tableName.'.orderlist asc');

$arrTwigVar ['rsAllSubsidiaries'] = $obj->updateContentLang($rsAllSubsidiaries); 

echo $twig->render('subsidiaries-detail.html', $arrTwigVar);

?>