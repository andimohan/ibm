<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashOut.class.php';
$cashOut = new CashOut(); 

$url = API_URL.'cash-out';
echo $cashOut->executeImportAPI($url,$_POST['data'], 'code');

?>