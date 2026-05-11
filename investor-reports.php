<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('InvestorReport.class.php','Category.class.php','InvestorReportCategory.class.php'));
$investorReport = new InvestorReport();
$investorReportCategory = new InvestorReportCategory();

$pageIndex = 'financial-information';

// sementara
$arrCategoryMap = array();
$arrCategoryMap['financial-information'] = 8000;
$arrCategoryMap['company-update'] = 8001;
$arrCategoryMap['annual-reports'] = 8002;
$arrCategoryMap['information-disclosures'] = 8003;
$arrCategoryMap['general-meeting-of-shareholders'] = 8004;
$arrCategoryMap['bond-and-debt-information'] = 8005;
$arrCategoryMap['stock-information'] = 8007;

if(isset($_GET) && in_array($_GET['pageIndex'], array_keys($arrCategoryMap))) 
    $pageIndex = $_GET['pageIndex'];
    
  
$categorykey = $arrCategoryMap[$pageIndex];
    
$arrYear = $class->generateYearSelectBox('',10,true);

if(isset($_GET) && !empty($_GET['period'])) {
    $selectedYear =  $_GET['period'];
    $_POST['selPeriod'] =  $_GET['period'];
}else{
    $selectedYear = $arrYear[array_keys($arrYear)[0]];
}
     

if(isset($_GET) && !empty($_GET['period']) && strtolower($_GET['period']) == 'all'){
    $yearCriteria = '';
}else{
    $yearCriteria = ' and ('.$investorReport->tableName.'.yearperiod = ' . $class->oDbCon->paramString($selectedYear).' or '.$investorReport->tableName.'.alwaysshow = 1)';
}

$rsCategory = $investorReportCategory->searchDataRow(array($investorReportCategory->tableName.'.pkey',$investorReportCategory->tableName.'.name'),
                                                    ' and '.$investorReportCategory->tableName.'.statuskey = 1 
                                                      and '.$investorReportCategory->tableName.'.parentkey = ' . $class->oDbCon->paramString($categorykey)
                                                    );
$arrCategoryKey = array_column($rsCategory,'pkey');
    
$arrField = array($investorReport->tableName.'.pkey', $investorReport->tableName.'.title',$investorReport->tableName.'.image',
                 $investorReport->tableName.'.categorykey',$investorReport->tableName.'.file',$investorReport->tableName.'.widget',
                 $investorReport->tableName.'.description',$investorReport->tableName.'.tableformat');

$rsReport = $investorReport->searchDataRow($arrField,
                                          ' and '.$investorReport->tableName.'.statuskey = 1
                                            and '.$investorReport->tableName.'.categorykey = ' . $class->oDbCon->paramString($categorykey) .' 
                                            '.$yearCriteria,
                                           ' order by '.$investorReport->tableName.'.trdate desc, '.$investorReport->tableName.'.pkey desc '
                                          ); 

$rsReportByCategory = $investorReport->searchDataRow($arrField,
                                          ' and '.$investorReport->tableName.'.statuskey = 1
                                            and '.$investorReport->tableName.'.categorykey in ( ' . $class->oDbCon->paramString($arrCategoryKey,',') .') 
                                            '.$yearCriteria,
                                           ' order by '.$investorReport->tableName.'.trdate desc, '.$investorReport->tableName.'.pkey desc '
                                          ); 
$rsReportByCategory = $investorReport->updateContentLang($rsReportByCategory);

for($i=0;$i<count($rsReportByCategory);$i++){
    if($rsReportByCategory[$i]['tableformat'] == 0){
        $rsReportByCategory[$i]['tableReport'] = '';
        continue;
    }
    $rsReportByCategory[$i]['table']= $investorReport->generateTable($rsReportByCategory[$i]['pkey']); 
}

$rsReportByCategory = $investorReport->reindexDetailCollections($rsReportByCategory,'categorykey');
 
$arrTwigVar['inputHidPage'] = $class->inputHidden('hidPage',array('value'=> $pageIndex)); 
$arrTwigVar['inputSelPeriod'] = $class->inputSelect('selPeriod',$arrYear); 
$arrTwigVar['rsReport'] = $investorReport->updateContentLang($rsReport);
$arrTwigVar['rsReportByCategory'] = $rsReportByCategory;
$arrTwigVar['rsCategory'] = $rsCategory;
    
echo $twig->render($pageIndex.'.html', $arrTwigVar);

?>