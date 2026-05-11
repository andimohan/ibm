<?php
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  

if (isset($_GET) && !empty($_GET['days'])){
	$item->sendNewRestockList($_GET['days']);
} 
	
?>