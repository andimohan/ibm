<?php 

require_once '_config.php'; 

// biar gk berat, load minim saja

require_once DOC_ROOT. 'connections/_connection.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AutoCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Lang.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/GCloud.class.php';
require_once DOC_ROOT. 'phpthumb/phpThumb.config.php'; 
 
$GLOBALS['ObjCol'] = array();
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);
 
$class = new Baseclass(); 
$GCloud = new GCloud(); 

if(!IS_DEVELOPMENT && DOMAIN_NAME <> $_SERVER['HTTP_HOST']) die;

if (isset($_POST) && !empty($_POST['action'])) {
			switch ($_POST['action']){  
                 
                case 'translation' :   
                    // kalo empty lang, ambil dr session
                    $targetLang = (empty($_POST['targetLang'])) ?  $_SESSION['lang'] : $_POST['targetLang'];
                     
                    //jaga2 kalo gk ad session
                    if(empty($targetLang)) {
                        $lang = new LANG(); 
                        $rsLang = $lang->searchDataRow(array('code'),' and systemVariable = 1');
                        $targetLang = $rsLang[0]['code'];
                    }
                      
                    $result = $GCloud->translate([$_POST['content']],$targetLang); 
                    echo json_encode($result);
                    break; 
            }
}

die;
  
?>