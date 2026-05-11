<?php  

require_once '_config.php'; 
require_once DOC_ROOT. 'connections/_connection.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
require_once  $_SERVER ['DOCUMENT_ROOT'].'/assets/vendor/autoload.php';

$GLOBALS['ObjCol'] = array();
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);

$class = new BaseClass();

$loader = new \Twig\Loader\FilesystemLoader($class->templateDocPath); 
$twig = new \Twig\Environment($loader);


if(!isset($_GET['page']) || empty($_GET['page'])) die;
   
$url = (isset($_GET['url']) && !empty($_GET['url'])) ? $_GET['url'] : '';

$arrTwigVar ['LANG'] = $class->lang;
$arrTwigVar['url'] = $url;

switch($_GET['page']){
    case 'video' :  $page = 'vid-popup.html'; break;
    case 'job-application-success' : $page = 'dialog-job-application-success.html'; break;
    default : $page = 'dialog-msg.html';
}
echo $twig->render($page, $arrTwigVar);

?>