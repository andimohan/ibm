<?php

$filename = $_GET['filename']; 
if (empty($filename))
    die;

if(strpos($filename, '/passwd') !== false) die;
if(strpos($filename, '../') !== false) die;
		 

$patterns = array('www.',':');
$replacements = array('','-');
$DOMAIN_NAME = str_replace($patterns, $replacements, $_SERVER['HTTP_HOST']); 
$DOC_ROOT = $_SERVER ['DOCUMENT_ROOT'];
if(substr($DOC_ROOT,-1) <> "/") $DOC_ROOT .= '/'; 

if(file_exists($DOC_ROOT.'_development.php'))
    include '_development.php';  


// buat download dari export, aksesnya folder temp
$path = (isset($_GET) & !empty($_GET['temp'])) ?  $DOC_ROOT.'../_temp/'.$DOMAIN_NAME.'/' : $DOC_ROOT.'../_upload/'.$DOMAIN_NAME.'/' ;  
$path .= $filename; 

$forceDownload = (isset($_GET) & !empty($_GET['download'])) ? true : false; 
 
if(!file_exists($path) || !is_file($path)) die; 

$filename = basename($filename); 

$file_extension = strtolower(substr(strrchr($filename,"."),1));
 
// validasi kalo file ext nya php gk boleh
if(in_array($file_extension,array('php'))) die;

$cdisposition = '';

switch( $file_extension ) {
    case "gif": $ctype="image/gif"; break;
    case "png": $ctype="image/png"; break;
    case "jpeg":
    case "jpg": $ctype="image/jpeg"; break;
    case "svg": $ctype="image/svg+xml"; break;
    case "pdf": $ctype="application/pdf";
                $cdisposition = "inline;";
                break;
    case "mp4": $ctype="video/mp4";break; 
    default: $ctype="application/force-download";
               $cdisposition = "attachment;";
               break;
}

if($forceDownload) { 
    $ctype="application/force-download";
    $cdisposition = "attachment;";
}

header('Content-type: ' . $ctype);
if (!empty($cdisposition))
     header('Content-Disposition: '.$cdisposition.'; filename="'.$filename.'"');
  
// ini kayanya masalah kalo pake kompresi
header('Content-Length: ' . filesize($path)); 
readfile($path);
 
/*
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
ob_clean();
flush();
readfile($path);
exit;
*/
//  
//$filename = $_GET['filename'];
//$path =  $_SERVER ['DOCUMENT_ROOT'].'/../_upload/hrta.wintera.co.id/' ;  
//$path .= $filename; 
//
//returnFile( $path );
//
//function returnFile( $filename ) {
// 
//    
//    error_log ('$filename '.$filename.chr(13),3,'test.txt');
//    
//    // Check if file exists, if it is not here return false:
//    if ( !file_exists( $filename )) return false;
//    header('Content-Description: File Transfer');
//    header('Content-Type: application/octet-stream');
//    // Suggest better filename for browser to use when saving file:
//    header('Content-Disposition: attachment; filename='.basename($filename));
//    header('Content-Transfer-Encoding: binary');
//    // Caching headers:
//    header('Expires: 0');
//    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//    header('Pragma: public');
//    // This should be set:
//    header('Content-Length: ' . filesize($filename));
//    // Clean output buffer without sending it, alternatively you can do ob_end_clean(); to also turn off buffering.
//    ob_clean();
//    // And flush buffers, don't know actually why but php manual seems recommending it:
//    flush();
//    // Read file and output it's contents:
//    readfile( $filename );
//    // You need to exit after that or at least make sure that anything other is not echoed out:
//    exit;
//}

?>