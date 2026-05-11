<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  


/* ======== INPUT QUICK LOGIN ========== */ 
$arrTwigVar['inputPopupUsername'] = $class->inputText('loginID'); 
$arrTwigVar['inputPopupPassword'] = $class->inputPassword('loginPassword'); 

$arrTwigVar['inputPopupUsernamePlaceholder'] = $class->inputText('loginID', array( 'etc' => 'placeholder="'.$class->lang['username'].'"')); 
$arrTwigVar['inputPopupPasswordPlaceholder'] = $class->inputPassword('loginPassword', array( 'etc' => 'placeholder="'.$class->lang['password'].'"')); 

$arrTwigVar['btnSubmitPopupLogin'] =   $class->inputSubmit('btnSave',$class->lang['login']); 
$arrTwigVar ['inputHidActionPopupLogin'] =  $class->inputHidden('action',array('value' => 'login'));

$fbAppID = $class->loadSetting('FBAppID');
$fbSecretKey = $class->loadSetting('FBSecretKey');
$arrTwigVar['btnFFBLogin'] = '';

if(!empty($fbAppID)){ 
    require_once 'assets/vendor/autoload.php';

    $fb = new Facebook\Facebook([
        'app_id' => $fbAppID,
        'app_secret' => $fbSecretKey,
        'default_graph_version' => 'v11.0',
    ]);

    $helper = $fb->getRedirectLoginHelper();

    $permissions = ['email', 'public_profile']; // Optional permissions 

    $arrTwigVar['fbLoginLink'] = $helper->getLoginUrl(HTTP_HOST.'api/sso/fb-login', $permissions); 
}

echo $twig->render('popup-login.html', $arrTwigVar);
?>