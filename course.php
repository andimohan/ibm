<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  
   
$criteria =  ' and '.$course->tableName.'.statuskey = 1';
 
$arrParentPath = array();
$cat = 0; 
$rsCat = array();
if(isset($_GET) && !empty($_GET['cat'])){ 
    
    $cat = $_GET['cat'];
    
    $criteria .= ' and ' . $course->tableName .'.categorykey = ' . $class->oDbCon->paramString($cat);

    // select ulang karena rs news bisa empty
    $rsCat = $courseCategory->getDataRowById($cat);
      
	$arrParentPath[0]['pkey'] = $rsCat[0]['pkey'];
	$arrParentPath[0]['name'] = $rsCat[0]['name']; 
	$parentkey = $rsCat[0]['parentkey'];
	 
	while($parentkey <> 0){ 
		$rsParent = $courseCategory->getDataRowById($parentkey); 
		$parentkey = $rsParent[0]['parentkey'];
		
		$ctr = count($arrParentPath);
		$arrParentPath[$ctr]['pkey'] =  $rsParent[0]['pkey'];
		$arrParentPath[$ctr]['name'] = $rsParent[0]['name']; 
	} 

}

$arrTwigVar['categoryPath'] = $arrParentPath;   
for($i=0;$i<count($arrTwigVar['categoryPath']);$i++)  
    array_push($arrActive,$arrTwigVar ['SELF_PAGE'].'?'.$arrTwigVar['categoryPath'][$i]['pkey']);  

$orderby = ' order by '.$course->tableName.'.orderlist asc, name asc';
$rsCourse = $course->searchData('','',true,$criteria,$orderby);
 
$arrTwigVar ['rsCourseCategory'] =  $rsCat; 
$arrTwigVar ['rsCourse'] =  $rsCourse; 
$arrTwigVar ['ACTIVE_MENU'] =  $arrActive; 

echo $twig->render('course.html', $arrTwigVar);

?>