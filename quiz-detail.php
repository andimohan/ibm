<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php'; 
   
includeClass('Quiz.class.php');
$quiz = new Quiz();

if(empty($_GET)){
	header("location: /");
	die;
} 

$id = $_GET['id']; 
$rsQuiz = $quiz->searchData($quiz->tableName.'.pkey',$id,true, ' and '.$quiz->tableName.'.statuskey = 1'); 
if(empty($rsQuiz)){
	header("location: /");
	die;
}

$totalQuestion = 10;

$rsQuizDetail = $quiz->getRandomQuestion($id,$totalQuestion);
$totalQuiz = count($rsQuizDetail);
$rsQuiz[0]['totalquiz'] = $totalQuiz;
foreach($rsQuizDetail as $key=>$row){
    $rsItemDetail = $quiz->getItemDetail($row['pkey']); 
    $rsQuizDetail[$key]['multiplechoice'] = $rsItemDetail;
    $rsQuizDetail[$key]['inputHidQuestionKey'] = $class->inputHidden('hidQuestionKey[]',array('value' => $row['pkey'])); 
}
  
$_POST['action'] ='add';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 

$arrTwigVar['rsQuiz'] = $rsQuiz;
$arrTwigVar['rsQuizDetail'] = $rsQuizDetail;
$arrTwigVar['totalQuiz'] = $totalQuiz;
$arrTwigVar['ACTIVE_MENU'] =  $arrActive; 
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone'); 
$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['send']); 
$arrTwigVar ['btnSubmitSubscribe'] =   $class->inputSubmit('btnSaveSubscribe',$class->lang['send']);
    
echo $twig->render('quiz-detail.html', $arrTwigVar);
?>