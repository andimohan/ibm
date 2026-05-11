<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
  
includeClass(array('JobOpportunities.class.php','CareerReference.class.php','JoiningConsideration.class.php'));  
$jobOpportunities = new JobOpportunities();
$careerReference = new CareerReference();
$joiningConsideration = new JoiningConsideration();

if(empty($_GET)){
	header("location: /");
	die;
} 

$id = $_GET['id']; 
$rsItem = $jobOpportunities->searchData($jobOpportunities->tableName.'.pkey',$id,true, ' and '.$jobOpportunities->tableName.'.statuskey = 1'); 
if(empty($rsItem)){
	header("location: /");
	die;
}  
 
$arrTwigVar['rsCareer'] =  $jobOpportunities->updateContentLang($rsItem);       
$title = (empty($rsItem[0]['title'])) ? $rsItem[0]['code'] : $rsItem[0]['title']; 
 
$descForMeta = $title;
 
$rsReference = $careerReference->searchDataRow(array($careerReference->tableName.'.pkey',
                                                    $careerReference->tableName.'.name',),
                                               ' and '.$careerReference->tableName.'.statuskey = 1',
                                              'order by '.$careerReference->tableName.'.orderlist asc ');
$rsReference = $careerReference->updateContentLang($rsReference);       

$rsReference = $careerReference->generateComboboxOpt(array('data' => $rsReference));


$rsConsideration = $joiningConsideration->searchDataRow(array($joiningConsideration->tableName.'.pkey',
                                                    $joiningConsideration->tableName.'.name',),
                                               ' and '.$joiningConsideration->tableName.'.statuskey = 1',
                                              'order by '.$joiningConsideration->tableName.'.orderlist asc ');
$rsConsideration = $joiningConsideration->updateContentLang($rsConsideration);       
  
$rsConsideration = $joiningConsideration->generateComboboxOpt(array('data' => $rsConsideration));
 
$arrYear = $class->generateYearSelectBox('',10,true);
$arrMonth = $class->generateMonthSelectBox(true);

$arrTwigVar ['META_TITLE'] = $title;
$arrTwigVar ['META_DESCRIPTION'] = $descForMeta; 
$arrTwigVar ['META_KEYWORDS'] = $title ;
 
$arrTwigVar ['inputHidJobId'] =  $class->inputHidden('hidRefJobOpportunity', array('value'=>$rsItem[0]['pkey'])); 
$arrTwigVar ['inputName'] =  $class->inputText('name', array('etc' => 'placeholder="'.$class->getLang('namePlaceholder').'"')); 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone', array('etc' => 'placeholder="'.$class->getLang('phonePlaceholder').'"')); 
$arrTwigVar ['inputEmail'] =  $class->inputText('email', array('etc' => 'placeholder="'.$class->getLang('emailPlaceholder').'"'));  
$arrTwigVar ['inputResume'] =  $class->inputText('resume'); 
$arrTwigVar ['inputPortfolio'] =  $class->inputText('portfolioURL', array('etc' => 'placeholder="'.$class->getLang('portfolioPlaceholder').'"')); 
$arrTwigVar ['inputAddress'] =   $class->inputTextArea('address', array('etc' => 'style="height:10em" placeholder="'.$class->getLang('addressPlaceholder').'"')); 

$arrTwigVar ['inputLatestRole'] =  $class->inputText('latestRole', array('etc' => 'placeholder="'.$class->getLang('latestRolePlaceholder').'"')); 
$arrTwigVar ['inputLatestCompany'] =  $class->inputText('latestCompany', array('etc' => 'placeholder="'.$class->getLang('latestCompanyPlaceholder').'"')); 
$arrTwigVar ['inputStillWork'] =  $class->inputCheckBox('chkStillWork'); 
$arrTwigVar ['inputStartDateMonth'] =  $class->inputSelect('selStartMonth',$arrMonth); 
$arrTwigVar ['inputStartDateYear'] =  $class->inputSelect('selStartYear',$arrYear); 
$arrTwigVar ['inputEndDateMonth'] =  $class->inputSelect('selEndMonth',$arrMonth); 
$arrTwigVar ['inputEndDateYear'] =  $class->inputSelect('selEndYear',$arrYear); 

$arrTwigVar ['inputSelSourceInformation'] =  $class->inputSelect('selReference',$rsReference); 
$arrTwigVar ['inputSelConsideration'] =  $class->inputSelect('selConsideration',$rsConsideration); 

$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->getLang('careerSubmitpplication')); 
$arrTwigVar ['PAGE_NAME'] =  $class->lang['contactUs'];

 
$arrTwigVar ['ACTIVE_MENU'] =  array('/careers');  
echo $twig->render('career-detail.html', $arrTwigVar);
?>