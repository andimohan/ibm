<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  
    
$rsCourseCategory = $courseCategory->searchData($courseCategory->tableName.'.statuskey', 1,true,' and ' . $courseCategory->tableName.'.isleaf = 1',' order by '.$courseCategory->tableName.'.orderlist asc');
foreach($rsCourseCategory as $key=>$row){
    $rsCourse = $course->searchData($course->tableName.'.categorykey', $row['pkey'],true,' and '.$course->tableName.'.statuskey = 1',' order by '.$course->tableName.'.orderlist asc');
    if (empty($rsCourse)){ 
        unset($rsCourseCategory[$key]);
        continue;
    }
     
    $rsQuizCol = $course->getDetailCollections($rsCourse,'refkey'); 
         
    foreach($rsCourse as $coursekey=>$courseRow)  
        $rsCourse[$coursekey]['quiz'] = $rsQuizCol[$courseRow['pkey']];  
    
    $rsCourseCategory[$key]['course'] = $rsCourse; 
}  
 
//$course->setLog($rsCourseCategory,true);
$arrTwigVar ['rsCourseCategory'] =  $rsCourseCategory;  

echo $twig->render('member-course.html', $arrTwigVar);

?>