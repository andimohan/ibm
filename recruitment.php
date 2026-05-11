<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Recruitment.class.php','JobOpportunities.class.php'));
$recruitment = new Recruitment();
$jobOpportunities = new JobOpportunities();

if(empty($_GET)){
	header("location: /");
	die;
} 

$jobkey = $_GET['jobkey']; 
$rsJob = $jobOpportunities->getDataRowById($jobkey);
$_POST['inputHidJobKey'] = $jobkey;
$arrTwigVar['inputHidJobKey'] = $class->inputHidden('inputHidJobKey'); 

$_POST['action'] ='apply';  
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 

$arrTwigVar['rsJob'] = $rsJob;
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone'); 
$arrTwigVar ['inputMobile'] =  $class->inputText('mobile');
$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 
$arrTwigVar ['inputAddress'] =  $class->inputTextArea('address', array( 'etc' => 'style="height:10em"'));  
$arrTwigVar ['uploadFileFolder'] = $recruitment->uploadFileFolder;

$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['apply']); 
 
echo $twig->render('recruitment.html', $arrTwigVar);

?>