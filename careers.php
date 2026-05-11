<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('JobOpportunities.class.php'));  
$jobOpportunities = new JobOpportunities(); 
$city = new City();

$pageUrlParam = array();
$sortCriteria = (isset($_GET) && !empty($_GET['sort']) && in_array($_GET['sort'], array('featured','newest'))) ? $_GET['sort'] : 'featured';  

$orderby = ($sortCriteria == 'featured') ? 'order by '.$jobOpportunities->tableName.'.isfeatured asc, '.$jobOpportunities->tableName.'.createdon desc' :  'order by '.$jobOpportunities->tableName.'.createdon desc';
 
$criteria = '';
$criteria .=  ' and '.$jobOpportunities->tableName.'.statuskey = 1 ';

if(isset($_POST) && !empty($_POST['searchCareer'])){
    $keyword = $_POST['searchCareer'];
    $criteria .=  ' and ('.$jobOpportunities->tableName.'.title like '.$class->oDbCon->paramString('%'.$keyword.'%').' or
                     '.$jobOpportunities->tableDepartment.'.name like '.$class->oDbCon->paramString('%'.$keyword.'%').' or
                     '.$jobOpportunities->tableExperience.'.name like '.$class->oDbCon->paramString('%'.$keyword.'%').'  or
                     '.$jobOpportunities->tableCity.'.name like '.$class->oDbCon->paramString('%'.$keyword.'%').' or
                     '.$jobOpportunities->tableCityCategory.'.name like '.$class->oDbCon->paramString('%'.$keyword.'%').' 
                   )'; 
}

/* ===================== CITY ========================================== */  
//$arrSelectedCity = explode(',',$_GET['citykey']);
//$arrSelectedCity = array_unique($arrSelectedCity);
//$rsCity = $city->searchData($city->tableName.'.statuskey',1);
//foreach($rsCity as $key=>$row)  { 
//    $_POST['chkCity[]'] = (in_array($row['pkey'],$arrSelectedCity)) ? 1 : '';
//    $rsCity[$key]['input'] = $class->inputCheckBox('chkCity[]',array("etc" => 'attr-rel = '.$row['pkey']));
//}
//
//if(!empty($_GET['citykey'])){
//    $criteria .= ' and '.$jobOpportunities->tableName.'.citykey in ('.$class->oDbCon->paramString($arrSelectedCity,',').')';   
////    array_push($pageUrlParam,'citykey=' . implode(',',$arrSelectedCity) );
//} 
//
//$arrTwigVar ['rsCity'] = $rsCity;

$totalrowsperpage = $class->loadSetting('careersTotalRowsPerPage');   
$pageIndex = ( isset($_GET) && !empty($_GET['page']) ) ? $_GET['page'] : 0; 
$now = $pageIndex * $totalrowsperpage; 
$arrTwigVar ['pageIndex'] =  $pageIndex;   
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

$rsJobs = $jobOpportunities->searchData('','',true,$criteria,$orderby,$limit);

$arrTwigVar ['rsJobs'] = $jobOpportunities->updateContentLang($rsJobs); 
$arrTwigVar ['totalPages'] =   ceil( $jobOpportunities->getTotalRows($criteria) / $totalrowsperpage);

$arrTwigVar ['lastPage'] =  ($pageIndex >= $arrTwigVar['totalPages'] - 1) ? 1 : 0 ; 

$arrTwigVar ['sortCriteria'] =  $sortCriteria;  
$arrTwigVar ['inputHidPages'] =  $class->inputHidden('hidPages', array('value' => 1)); 
$arrTwigVar ['inputSearchCareer'] =  $class->inputText('searchCareer', array('etc' => 'placeholder="'.$jobOpportunities->lang['careerSearchPlaceholder'].'"'));  

echo $twig->render('careers.html', $arrTwigVar);

?>