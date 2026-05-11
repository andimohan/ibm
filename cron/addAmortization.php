<?php    

require_once '../_config.php'; 
require_once "../_include-v2.php"; 

includeClass(array('PrepaidExpense.class.php')); 
$prepaidExpense = new PrepaidExpense();

$date = (isset($_GET['date']) && !empty($_GET['date'])) ? $_GET['date'] : ''; 

$prepaidExpense->addAmortization($date);

echo 'done';
?>