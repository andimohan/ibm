<?php
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass("Quiz.class.php");
$quiz = new Quiz();


foreach ($_POST as $k => $v) { 
    if (!is_array($v))
         $v = trim($v);  

    $arr[$k] = $v;     
}  

$arrReturn = $quiz->checkAnswers($arr);

echo json_encode($arrReturn);  
die; 
	
?>