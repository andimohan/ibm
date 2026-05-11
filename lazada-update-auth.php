<?php 

require_once '_config.php'; 
require_once '_include-v2.php'; 

includeClass(array('Marketplace.class.php'));
$lazada = new Lazada();

if(!isset($_GET) || empty($_GET['code'])) die;
  
$code = $_GET['code'];
$lazada->updateTokenLazada($code);    

echo 'Token telah diupdate.';
?>
