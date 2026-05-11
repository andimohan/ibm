<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
 
includeClass(array('Customer.class.php'));

if (isset($_GET) && !empty($_GET['pkey']) && !empty($_GET['activationhashkey'])){
    $LANG_USER_KEY = $_GET['pkey'];
    require_once '_reset-lang.php';  
     
    $customer = new Customer(); // set dibawah karena reset lang
	 $result =  $customer->activateMember($_GET['pkey'],$_GET['activationhashkey']); 
	 if ($result[0]['valid'])
	 	$content = $class->lang['accountActivationSuccessful'];
	 else
	 	$content = $result[0]['message'];
}
  
$arrTwigVar['title'] = $class->lang['accountActivation']; 
$arrTwigVar['content'] =  $content ; 

echo $twig->render('page.html', $arrTwigVar);

?>
