<?php

require_once '../_config.php'; 

//gk usah pake include, karena resiko, kadang include class bawah ad spasi jd kebawa ke file

$DOC_ROOT = $_SERVER ['DOCUMENT_ROOT'] ;   
if(substr($DOC_ROOT,-1) <> "/") {
	$DOC_ROOT .= '/';	
} 
  
define('DOC_ROOT',$DOC_ROOT); 
//define('DOMAIN_FOLDER','gpi.mennconnect.com/'); 
define('DEFAULT_DOC_UPLOAD_PATH', DOC_ROOT. '_upload/' .DOMAIN_NAME.'/');
 
$path = DEFAULT_DOC_UPLOAD_PATH.'FP/FP-'.$_GET['id'].'.xml'; 

if(!file_exists($path) || !is_file($path)) die; 
  
$ctype="application/force-download";
$cdisposition = "attachment;";

$filename =  basename($path); 

//flush();
header('Content-type: ' . $ctype);
header('Content-Disposition: '.$cdisposition.'; filename="'.$filename.'"'); 
readfile($path);
?>