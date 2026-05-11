<?php

if (!isset($class))
	$class = new BaseClass();

require_once  $_SERVER ['DOCUMENT_ROOT'].'/assets/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader($class->templateDocPath); 
$twig = new \Twig\Environment($loader);

require_once  $_SERVER ['DOCUMENT_ROOT'].'/_twig-function.php';

$arrTwigVar = array();

$arrTwigVar['PAGE_ID'] = basename ($_SERVER['PHP_SELF'] ,".php");
$arrTwigVar ['DOMAIN_NAME'] = DOMAIN_NAME;
$arrTwigVar ['SELF_PAGE'] = $_SERVER['PHP_SELF'];  
$arrTwigVar ['HTTP_HOST'] = HTTP_HOST; 
//$arrTwigVar ['REQUEST_URI'] = REQUEST_URI;

$arrTwigVar ['TEMPLATE_CSS_PATH'] =  $class->templateCssPath;
$arrTwigVar ['TEMPLATE_JS_PATH'] =  $class->templateJsPath;
$arrTwigVar ['TEMPLATE_JS_PAGE_PATH'] =  $class->templateJsPath;
$arrTwigVar ['TEMPLATE_IMG_PATH'] =  $class->templateImgPath;
 
$arrTwigVar ['DEFAULT_URL_UPLOAD_PATH'] =  DEFAULT_URL_UPLOAD_PATH;
$arrTwigVar ['DEFAULT_DOC_UPLOAD_PATH'] =  DEFAULT_DOC_UPLOAD_PATH;
$arrTwigVar ['UPLOAD_TEMP_DOC_SHORT'] =  UPLOAD_TEMP_DOC_SHORT; 
$arrTwigVar ['UPLOAD_TEMP_URL'] =  UPLOAD_TEMP_URL;  
$arrTwigVar ['PHPTHUMB_URL_SRC'] =  PHPTHUMB_URL_PATH; 
?>