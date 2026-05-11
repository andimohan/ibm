<?php 
include '_config.php';
    
$path = DOC_ROOT.'log/';  
$filename = 'download-shopee'; 
$filename = '['.date('d-m-Y') .'] - '.$filename.'.txt'; 
$filename = $path.$filename;  

//error_log ('download-shopee => '.$_SERVER['REMOTE_ADDR'].chr(13),3,$filename);


$filename = $_GET['filename'];
if (empty($filename))
    die;

if(strpos($filename, '/passwd') !== false) die;
if(strpos($filename, '../') !== false) die;

$path = (isset($_GET) & !empty($_GET['temp'])) ?  UPLOAD_TEMP_DOC : DEFAULT_DOC_UPLOAD_PATH ;  
$path .= $filename; 
   
if(!file_exists($path) || !is_file($path))  die; 
 
$filename = basename($filename); 
    
$file_extension = strtolower(substr(strrchr($filename,"."),1));
 
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
    default: $ctype="application/force-download";
               $cdisposition = "attachment;";
               break;
}

header('Content-type: ' . $ctype);
if (!empty($cdisposition))
     header('Content-Disposition: '.$cdisposition.'; filename="'.$filename.'"');
  
// ini kayanya masalah kalo pake kompresi
header('Content-Length: ' . filesize($path)); 
readfile($path);
?>