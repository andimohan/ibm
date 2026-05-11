<?php
//$start_time = microtime(TRUE);

include_once '../_config.php';  
include_once '../_include-v2.php';

// perlu diduluin
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/APPayment.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ARPayment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Downpayment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderInvoiceReceipt.class.php';
    
$path    = '../include/class-2.12/';
$files = scandir($path);
$files = array_diff(scandir($path), array('.', '..'));

//include semua file dulu

$filesExclude= array('.DS_Store','minerva.lc','php-mailjet-events.class-mailjet-0.1.php','php-mailjet.class-mailjet-0.1.php','xmlapi.class.php',
                     'MarketplaceV1.class.php','TruckingServiceOrderInvoice_NEW.class.php','Excel.class.php','GPSConnection.class.php','GPSETI.class.php',
                     'Mobile_Detect.php','SMTP.class.php');

foreach($files as $file) { 
    if(substr($file, 0, 1) == '_') continue;
    if (in_array($file,$filesExclude )) continue;  
    require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/'.$file;   
}

$fileContent = '';

foreach($files as $file){
  if(substr($file, 0, 1) == '_') continue;
  if (in_array($file,$filesExclude )) continue;
    
  $className = str_replace('.class.php','',$file);
  eval('$obj = new '.$className.'();');
  if(!isset($obj->tableName) || empty($obj->tableName)) continue;
     
  $fileContent .= 'case \''.$obj->tableName.'\': includeClass(\''.$file.'\');
										 return  createObjAndAddToCol(new '.$className.'());
										 break;';
    	
	
}

//echo getPerformanceLog($start_time);
echo $fileContent;

die;

 // update berdasarkan tablename

?>