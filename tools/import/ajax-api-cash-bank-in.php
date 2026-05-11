<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashBankIn.class.php';
$cashBankIn = new CashBankIn(); 

$url = API_URL.'cash-bank-in';
echo $cashBankIn->executeImportAPI($url,$_POST['data'], 'code');

?>