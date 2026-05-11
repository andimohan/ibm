<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
   
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone'); 
$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 
$arrTwigVar ['inputSubject'] =  $class->inputText('subject'); 
$arrTwigVar ['inputMessage'] =   $class->inputTextArea('message', array('etc' => 'style="height:10em"')); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['send']); 
$arrTwigVar ['PAGE_NAME'] =  $class->lang['contactUs'];
    

$arrTwigVar['inputNamePlaceholder'] = $class->inputText('name', array( 'etc' => 'placeholder="'.$class->lang['name'].'"')); 
$arrTwigVar['inputPhonePlaceholder'] = $class->inputText('phone', array( 'etc' => 'placeholder="'.$class->lang['phone'].'"')); 
$arrTwigVar['inputEmailPlaceholder'] = $class->inputText('email', array( 'etc' => 'placeholder="'.$class->lang['email'].'"')); 
$arrTwigVar['inputSubjectPlaceholder'] = $class->inputText('subject', array( 'etc' => 'placeholder="'.$class->lang['subject'].'"')); 
$arrTwigVar['inputMessagePlaceholder'] = $class->inputTextArea('message', array( 'etc' => 'style="height:10em" placeholder="'.$class->lang['message'].'"')); 

echo $twig->render('contact-us.html', $arrTwigVar);

?>
