<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('GoodCorporateGovernment.class.php','Category.class.php','GoodCorporateGovernmentCategory.class.php','ManagementTeam.class.php'));
$obj = new GoodCorporateGovernment();
$gcgCategory = new GoodCorporateGovernmentCategory();
$managementTeam = new ManagementTeam();

$rsCategoryCol = $gcgCategory->searchDataRow(array( $gcgCategory->tableName.'.pkey', $gcgCategory->tableName.'.name' , $gcgCategory->tableName.'.description' ),
                                   ' and '. $gcgCategory->tableName.'.statuskey = 1');

$rsCategory  = $gcgCategory->updateContentLang($rsCategoryCol);

$arrTwigVar ['rsCategory'] =  $rsCategory;

$arrYear = $obj->generateYearSelectBox('',5,true,3);

$reportCriteria = '';

if(!isset($_GET) || empty($_GET['id']))
    $_GET['id'] =  $rsCategory[0]['pkey'];

if(isset($_GET)){
    if(empty($_GET['period'])){
        $_GET['period'] = array_keys($arrYear)[0];   
    } 
    
    $arrPeriod = explode('-',$_GET['period']);
    $startYear = ($arrPeriod[0]) ? $arrPeriod[0] : 0;
    $endYear = ($arrPeriod[1] && $arrPeriod[1] >=$arrPeriod[0] ) ? $arrPeriod[1] : 0;
    $reportCriteria .= ' and '.$obj->tableGoodCorporateGovernmentReport.'.yearperiod between '.$obj->oDbCon->paramString($startYear).' and '.$obj->oDbCon->paramString($endYear);
        
    $_POST['selPeriod'] = $_GET['period'];
}



$rs = $obj->searchDataRow(array( $obj->tableName.'.pkey', $obj->tableName.'.title',  $obj->tableName.'.file', $obj->tableName.'.image', $obj->tableName.'.shortdesc', $obj->tableName.'.description' ),
                                   ' and '. $obj->tableName.'.statuskey = 1
                                     and '. $obj->tableName.'.categorykey = ' . $obj->oDbCon->paramString($_GET['id'])
                             );

$rsTeam = $obj->getGoodCorporateGovernmentTeam($rs[0]['pkey']);
    
$rsCategory = array_column($rsCategory,null,'pkey');
$rsSelectedCategory = $rsCategory[$_GET['id']];

$arrTwigVar ['rs'] = $obj->updateContentLang($rs); 
$arrTwigVar ['rsSelectedCategory'] = $rsSelectedCategory; 
$arrTwigVar ['rsTeam'] = $managementTeam->updateContentLang($rsTeam); 



$rsReport = $obj->getGoodCorporateGovernmentReportDetail($_GET['id'],$reportCriteria);

$arrTwigVar['inputSelThreePeriod'] = $class->inputSelect('selPeriod',$arrYear); 
$arrTwigVar['rsReport'] = $rsReport;

// agar tetep ambil nama category dari lang default
$rsCategoryCol = array_column($rsCategoryCol,null,'pkey'); 
$pageIndex = $obj->URLFilter($rsCategoryCol[$_GET['id']]['name']);


$arrTwigVar['inputHidPage'] = $class->inputHidden('hidPage',array('value'=> $pageIndex)); 
$arrTwigVar['inputHidId'] = $class->inputHidden('hidId',array('value'=> $_GET['id'])); 

echo $twig->render($pageIndex.'.html', $arrTwigVar);
?>