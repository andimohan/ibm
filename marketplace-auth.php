<?php 
// update code
require_once '_config.php'; 
require_once '_mp-client.php'; 


require_once DOC_ROOT. 'connections/_connection.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BaseClass.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AutoCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomCode.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Marketplace.class.php'; 
 
$GLOBALS['ObjCol'] = array();
$GLOBALS['oDbCon'] = new Database($rs[0]['dbusername'],$rs[0]['dbpass'],$rs[0]['dbname'],$host);

//preloadSystemSettings();
	
$class = new Baseclass();
$GLOBALS['class'] = $class;

$shopId = $_GET['shop_id'];
$refCode = $_GET['code'];


// hati2 hanya boleh redirect kalo dari awal 

$marketplace = new Marketplace();
$marketplace->updateAuthShopee($shopId,$refCode);

// redirect ke halaman sukses 
header('location: https://wintera.co.id/auth-success.html');
 
die;
?>