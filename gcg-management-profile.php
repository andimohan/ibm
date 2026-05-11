<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('ManagementTeam.class.php','GoodCorporateGovernment.class.php','Category.class.php','GoodCorporateGovernmentCategory.class.php'));
$obj = new ManagementTeam();
$gcg = new GoodCorporateGovernment();
$gcgCategory = new GoodCorporateGovernmentCategory();

if(empty($_GET)){
	header("location: /");
	die;
}
 
$id = $_GET['id']; 

$rsManagement = $obj->getDataRowById($id, ' and statuskey = 1');
if(empty($rsManagement)){
	header("location: /");
	die;
}


$rsCategory = $gcgCategory->searchDataRow(array( $gcgCategory->tableName.'.pkey', $gcgCategory->tableName.'.name' , $gcgCategory->tableName.'.description' ),
                                   ' and '. $gcgCategory->tableName.'.statuskey = 1');

$rsCategory  = $gcgCategory->updateContentLang($rsCategory);
$arrTwigVar ['rsCategory'] =  $rsCategory;

$rsCategory = array_column($rsCategory,null,'pkey');
$rsSelectedCategory = $rsCategory[$_GET['catid']];

$arrTwigVar ['rsManagement'] = $obj->updateContentLang($rsManagement); 
$arrTwigVar ['rsSelectedCategory'] = $rsSelectedCategory; 

echo $twig->render('gcg-management-profile.html', $arrTwigVar);
?>