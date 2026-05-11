<?php
require_once '_config.php';
require_once '_include-fe-v2.php';
require_once '_global.php';

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

require_once '_include-customer-information.php';

includeClass(array('Country.class.php','MeetingPointSuggestion.class.php'));
$meetingpointSuggestion = new MeetingPointSuggestion();
$city = new City();
$country = new Country();

$arrCountry =  $country->generateComboboxOpt(null,array('criteria' =>' and ('.$country->tableName.'.statuskey = 1)')); 
$arrTwigVar ['inputSelCountry']  = $class->inputSelect('selCountry', $arrCountry);

$rsDefaultCountry = $country->searchDataRow(array($country->tableName.'.pkey'),' and '.$country->tableName.'.systemVariable = 1');
$arrTwigVar ['defaultLangKey'] = (!empty($rsDefaultCountry)) ? $rsDefaultCountry[0]['pkey'] : 0;

$arrTwigVar['inputName'] =  $class->inputText('name');
$arrTwigVar['inputAddress'] =  $class->inputTextArea('address', array('etc' => 'style="height:8em"'));
$arrTwigVar['inputDescriptionPoint'] =  $class->inputTextArea('descriptionPoint', array('etc' => 'style="height:8em"'));
$arrTwigVar['inputPhone'] =  $class->inputText('phone');
$arrTwigVar['inputCity'] =  $class->inputAutoComplete(
	array( 
		'element' => array(
			'value' => 'cityName',
			'key' => 'hidCityKey'
		),
		'source' => array(
			'url' => 'ajax-city.php',
			'data' => array('action' => 'searchData')
		)
	)
);

$arrTwigVar['btnSubmit'] =   $class->inputSubmit('btnSave', $class->lang['save']);
$_POST['action'] = 'add';
$arrTwigVar['inputHidAction'] =  $class->inputHidden('action');

array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 

echo $twig->render('meeting-point-suggestion.html', $arrTwigVar);