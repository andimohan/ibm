<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php'; 
require_once '_global.php';  

includeClass(array('Category.class.php','CareerCategory.class.php'));  
$careerCategory = new CareerCategory();

 
//$pageIndex = 0;
//if ( isset($_GET) && !empty($_GET['page']) ){
//	$pageIndex = $_GET['page'];
//}
//$arrTwigVar ['pageIndex'] =  $pageIndex;
//
//
//$totalrowsperpage = 999;

//$now = $pageIndex * $totalrowsperpage;
//$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
//      
//$criteria = ' and customerkey = ' . $careerCategory->oDbCon->paramString(USERKEY);

//$rsCategoryCareer = $careerCategory->searchData('','',true,$criteria,'order by '.$careerCategory->tableName.'.pkey desc',$limit);
$rsCategoryCareer = $careerCategory->searchData($careerCategory->tableName.'.statuskey',1);

for($i=0;$i<count($rsCategoryCareer);$i++){
        $rsCategoryCareer[$i]['shortdescription'] = str_replace(chr(13),'<br>',$rsCategoryCareer[$i]['shortdescription']);
}



//$totalPages = ceil( $salesOrder->getTotalRows($criteria) / $totalrowsperpage); 

$arrTwigVar ['rsCategoryCareer'] =  $rsCategoryCareer;
$arrTwigVar ['totalPages'] =  $totalPages;   
  
echo $twig->render('career-category.html', $arrTwigVar);

?>