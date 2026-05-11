<?php 

include '_config.php';

$path = DOC_ROOT.'log/';  
$fileLog = 'download-lazada'; 
$fileLog = '['.date('d-m-Y') .'] - '.$fileLog.'.txt'; 
$fileLog = $path.$fileLog;  
  
$filename = base64_decode($_GET['filename']);   
$arrTemp = json_decode($filename,true);

$filename = $arrTemp['filename']; //$_GET['filename'];

if(strpos($filename, '/passwd') !== false) die;
if(strpos($filename, '../') !== false) die;

$ts =  $arrTemp['ts']; 
$leadTime = time() - $ts;

if (empty($filename)) die; 
if ($leadTime > 60) die; // 30 second

$path = (isset($_GET) & !empty($_GET['temp'])) ?  UPLOAD_TEMP_DOC : DEFAULT_DOC_UPLOAD_PATH ;  
$path .= $filename; 
   
if(!file_exists($path) || !is_file($path)) die; 

error_log (date('d-m-Y H:m:s').','.$leadTime.',lazada,'.$_SERVER['REMOTE_ADDR'].','.$path.chr(13),3,$fileLog);
 
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