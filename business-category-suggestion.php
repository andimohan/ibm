<?php
require_once '_config.php';
require_once '_include-fe-v2.php';
require_once '_global.php';


if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}


require_once '_include-customer-information.php';

includeClass(array('BusinessCategorySuggestion.class.php'));
$businessCategorySuggestion = new BusinessCategorySuggestion();

$arrTwigVar['inputName'] =  $class->inputText('category'); 
$arrTwigVar['btnSubmit'] =   $class->inputSubmit('btnSave', $class->lang['save']);


$_POST['action'] = 'add';
$arrTwigVar['inputHidAction'] =  $class->inputHidden('action'); 

array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 
echo $twig->render('business-category-suggestion.html', $arrTwigVar);
