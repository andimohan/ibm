<?php

require_once '../_config.php'; 
require_once "../_include-v2.php"; 

includeClass(array('Asset.class.php')); 
$asset = new Asset();

$date = (isset($_GET['date']) && !empty($_GET['date'])) ? $_GET['date'] : ''; 
$asset->addDepreciation($date);

echo 'done';
?>