<?php

require_once '_config.php';

$pkey = $_GET['pkey']; 
$module = $_GET['module'];
if (empty($pkey) || empty($module)) die;

// coba biar load seminim mungkin
require_once DOC_ROOT. 'connections/_connection.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
$GLOBALS['ObjCol'] = array();
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);


$class = new Baseclass();

$DOMAIN_NAME = $_SESSION[$class->loginSession]['customerCompany']['domain'];

if(empty($DOMAIN_NAME)){
	header('location: /customer-portal'); 
	die;
}

//errror kalo pake session
$CUSTOMER_CONN = newConnection($DOMAIN_NAME); 
$security = new Security();
$security->oDbCon = $CUSTOMER_CONN;

// cek ualng user agar tdk di tempered 
if (!$security->isMemberLogin(false)) { 
	header('location:/logout'); // gk bisa pake KICKED_REDIRECT_URL, karena blm kedefined
	die;
}  


define ('USERKEY',  base64_decode($_SESSION[$class->loginSession]['id']));

$folderName = '';
$downloadName = '';

switch($module){
	case 'trucking-invoice' :  
							  require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderInvoice.class.php';  
							  $obj = new TruckingServiceOrderInvoice();
							  $obj->oDbCon = $CUSTOMER_CONN;
							  $rs = $obj->searchDataRow(array($obj->tableName.'.file',$obj->tableName.'.code'), 
														' and '.$obj->tableName.'.pkey = ' . $obj->oDbCon->paramString($pkey).'
														  and '.$obj->tableName.'.customerkey = '. $obj->oDbCon->paramString(USERKEY)
													   );
		
		
							  $filename = (!empty($rs[0]['file'])) ? $rs[0]['file'] : '';
							  $downloadName = $obj->lang['documentFiles'].'_'.$rs[0]['code'];
				
							  $folderName = $obj->uploadFileFolder;
							  break;
		
	case 'trucking-invoice-tax' :  
							  require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderInvoice.class.php';  
							  $obj = new TruckingServiceOrderInvoice();
							  $obj->oDbCon = $CUSTOMER_CONN;
							  $rs = $obj->searchDataRow(array($obj->tableName.'.filetax',$obj->tableName.'.code'), 
														' and '.$obj->tableName.'.pkey = ' . $obj->oDbCon->paramString($pkey).'
														  and '.$obj->tableName.'.customerkey = '. $obj->oDbCon->paramString(USERKEY)
													   );
		
		
							  $filename = (!empty($rs[0]['filetax'])) ? $rs[0]['filetax'] : '';
							  $downloadName = $obj->lang['taxInvoice'].'_'.$rs[0]['code'];
				
							  $folderName = $obj->uploadFileTaxFolder;
							  break;
		
	case 'tax23-payable-receipt' :  
							  require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';  
							  require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/APPayment.class.php';  
							  require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/APPayableTax23Payment.class.php';  
							  $obj = new APPayableTax23Payment();
							  $customer = new Customer();
							  $obj->oDbCon = $CUSTOMER_CONN;
							  $customer->oDbCon = $CUSTOMER_CONN;
		
		 					 // cari link supplierkey nya
		
							 $rsCust = $customer->getSupplierLink(USERKEY); 
							 $supplierkey = (isset($rsCust[0]['supplierkey']) && !empty($rsCust[0]['supplierkey'])) ? $rsCust[0]['supplierkey'] : 0;


							  $rs = $obj->searchDataRow(array($obj->tableName.'.pkey',$obj->tableName.'.code',$obj->tableName.'.refcode'), 
														' and '.$obj->tableName.'.pkey = ' . $obj->oDbCon->paramString($pkey).'
														  and '.$obj->tableName.'.supplierkey = '. $obj->oDbCon->paramString($supplierkey)
													   );
													   
                                // sementara convert ke satu file dulu, meskipun skrg sudah bisa multiple file
                                $rsItemFile = $obj->getItemFile($rs[0]['pkey']);
		
		                        $rs[0]['file'] = (!empty($rsItemFile)) ? $rsItemFile[0]['file'] : '';
		
							  $filename = (!empty($rs[0]['file'])) ? $rs[0]['file'] : '';
							  
		
							  $downloadName = $obj->lang['witholdingReceipt'];
							  $downloadName .= (!empty($rs[0]['refcode'])) ? $rs[0]['refcode'] : $rs[0]['code'];
				
							  $folderName = $obj->uploadFileFolder;

							  break;
	default : die;
}
  


$patterns = array('www.',':','/','\\'); // buat jaga2
$replacements = array('','-','','');
$DOMAIN_NAME = str_replace($patterns, $replacements, $DOMAIN_NAME); 
$DOC_ROOT = $_SERVER ['DOCUMENT_ROOT'];
if(substr($DOC_ROOT,-1) <> "/") $DOC_ROOT .= '/';


// buat download dari export, aksesnya folder temp
$path = (isset($_GET) & !empty($_GET['temp'])) ?  $DOC_ROOT.'../_temp/'.$DOMAIN_NAME.'/' : $DOC_ROOT.'../_upload/'.$DOMAIN_NAME.'/' ;  

// buat jaga2
if(strpos($filename, '/passwd') !== false) die;
if(strpos($filename, '../') !== false) die;

$path .= $folderName.$pkey.'/'. $filename;

$forceDownload = true; // (isset($_GET) & !empty($_GET['download'])) ? true : false; 

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

if(!empty($downloadName))
	$filename = $downloadName.'.'.$file_extension;

header('Content-type: ' . $ctype);
if (!empty($cdisposition))
     header('Content-Disposition: '.$cdisposition.'; filename="'.$obj->filenameSanitizer($filename).'"');
  
// ini kayanya masalah kalo pake kompresi
//header('Content-Length: ' . filesize($path)); 
readfile($path);
 
?>