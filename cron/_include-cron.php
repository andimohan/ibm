<?php 
date_default_timezone_set('Asia/Jakarta');

// gk tau kenapa gk bentrok sama define DOC_ROOT yg di _config, malah gk kebaca DOC_ROOT nya

if(file_exists('../_development.php')) {
    // khusus development
    $DOC_ROOT = $_SERVER ['DOCUMENT_ROOT'] ;
    if(substr($DOC_ROOT,-1) <> "/")   $DOC_ROOT .= '/';   
    define('DOC_ROOT',$DOC_ROOT);
}else{
    define('DOC_ROOT','/home/icommuni/public_html/');
}
 
//gk jadi pake
define('DOC_ROOT','/home/programstok/minerva/');
  
require_once DOC_ROOT. '_config.php';
require_once DOC_ROOT. 'connections/_connection.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AutoCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Lang.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Mobile_Detect.php';     

// sementara saja biar gk error kalo kirim email dr cron
require_once DOC_ROOT. 'phpthumb/phpThumb.config.php'; 

$GLOBALS['ObjCol'] = array();
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);

$class = new Baseclass();
$GLOBALS['class'] = $class;

$setting = new Setting(); 	   
$security = new Security();
$lang = new Lang(); 


function includeClass($classFile, $createObject = true){
    if (!is_array($classFile)) $classFile = array($classFile);
    
    foreach($classFile as $file){ 
         $filePath = DOC_ROOT. 'include/'.CLASS_VERSION.'/'.$file;
            if(is_file($filePath))
                require_once $filePath; 
    }
}
 


require_once DOC_ROOT.'/assets/vendor/autoload.php'; 
$loader = new \Twig\Loader\FilesystemLoader($class->templateDocPath); 
$twig = new \Twig\Environment($loader);
 
require_once DOC_ROOT.'_twig-function.php';

?>