<?php

//die("die, comment open for reset transaction");

require_once '../../../_config.php';  

require_once DOC_ROOT. 'connections/_connection.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AutoCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Lang.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Mobile_Detect.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';  
//require_once DOC_ROOT. 'phpthumb/phpThumb.config.php'; 
 
$GLOBALS['ObjCol'] = array();
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);

$class = new Baseclass();
$GLOBALS['class'] = $class;

$setting = new Setting(); 	   
$security = new Security();
$lang = new Lang();

$DEST = DOC_ROOT.'../'.DOMAIN_NAME.'/';
if (!file_exists($DEST)) mkdir($DEST, 0755, true); 
    
// file2 pasti dicopy 
require_once DOMAIN_NAME.'.config.php';  

// utk overwrite nama domain _domain.php
$domainFile = fopen($DEST."_domain.php", "w") or die("Unable to open file!"); 
fwrite($domainFile, '<?php  $DOMAIN_NAME = \''.DOMAIN_NAME.'\'; ?>');
fclose($domainFile);

// start copy files
copyFiles($arrFiles);

function copyFiles($arrFiles){
 global $DEST;    
 global $class;    
     
 // cek dulu folder atau file_exists
 foreach($arrFiles as $sourceList){
     
     //cek dulu setiap path ready
      
     $sourcePath = DOC_ROOT.$sourceList;
     $destPath = $DEST.$sourceList;
      
    $path = pathinfo($destPath); 
    if (!file_exists($path['dirname']))  
        mkdir($path['dirname'], 0755, true);  
     
     if( is_dir($sourcePath) ){
         $class->fullCopy( $sourcePath, $destPath );
     }else{
        // $fileName = basename ($sourceList ,".php"); 
           
        if(file_exists($sourcePath))  
            copy(  $sourcePath,   $DEST.$sourceList);
     }
 }   
}
    
echo '<pre>';
print_r($arrFiles);
echo '</pre>';
?>