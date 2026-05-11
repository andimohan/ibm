<?php 
if (empty($_SESSION[$class->loginAdminSession])) 
	header("location: /driver");

require_once  $_SERVER ['DOCUMENT_ROOT'].'/assets/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader($class->templateDocPath); 
$twig = new \Twig\Environment($loader);

//$loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'].'/driver/template');
//$twig = new Twig_Environment($loader);
 
$arrTwigVar ['DEFAULT_CSS_PATH'] =  $class->adminCssPath;
$arrTwigVar ['DEFAULT_JS_PATH'] =  $class->defaultJsPath; 
$arrTwigVar ['SELF_PAGE'] = $_SERVER['PHP_SELF'];
 
/* LANGUAGE */
$arrTwigVar ['LANG'] = $class->lang;
$arrTwigVar ['ERRORMSG'] = $class->errorMsg;

$arrTwigVar['loginSession'] = $_SESSION[$class->loginAdminSession];

/* settings */
$rsSetting =  $setting->getSettingData();
for ($i=0;$i<count($rsSetting);$i++){
	$code = $rsSetting[$i]['code'];
	 
	if ($rsSetting[$i]['multivalue'] == 0){ 
			if ($rsSetting[$i]['type'] == 3 )
				$arrTwigVar ['settings'][$code] =str_replace(chr(13),'<br>',$rsSetting[$i]['value']);
			else
				$arrTwigVar ['settings'][$code] = $rsSetting[$i]['value'] ;
	}else{ 
		$arrDetail = $setting->getDetailByCode($code);
		$arrTwigVar ['settings'][$code] = $arrDetail;
	} 
		 
}   


?>