<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

if(!isset($_GET['id']) || empty($_GET['id']))die;

includeClass(array('Customer.class.php','DownloadCategory.class.php', 'Download.class.php'));  
$download = new Download();
$customer = new Customer();

$id = $_GET['id'];

$rsCust = $customer->getDataRowById(USERKEY);
$rsDownload = $download->getDataRowById($id);
	
// cek akses dengan LOGIN_USER
if(empty($rsDownload) || empty($rsCust)) die;

if( $rsDownload[0]['hosttypekey']  > $rsCust[0]['hostlevelkey'] || $rsDownload[0]['membershiplevelkey']  > $rsCust[0]['membershiplevel'] ) die;

// file detail
// sementara ambil 1 file pertama aj
$rsFile = $download->getFileDetail($id);

// download
// pisahkan dr download.php
$filename = $rsFile[0]['file'];

// buat jaga2
if(strpos($filename, '/passwd') !== false) die;

$path = DEFAULT_DOC_UPLOAD_PATH.$download->uploadFileFolder.$rsDownload[0]['pkey'].'/'.$filename; 

if(!file_exists($path) || !is_file($path)) die; 
 
//error_log ('$path ok '.chr(13),3,$logfilename);

$filename = basename($filename); 
     
$file_extension = strtolower(substr(strrchr($filename,"."),1));
 
// validasi kalo file ext nya php gk boleh
if(strpos($filename, '/passwd') !== false) die;
if(strpos($filename, '../') !== false) die;

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