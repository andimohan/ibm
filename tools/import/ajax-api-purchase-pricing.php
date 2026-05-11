<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/PurchasePricing.class.php';
$purchasePrice = new PurchasePricing();

$url = API_URL . 'purchase-pricing';


echo $purchasePrice->executeImportAPI($url, $_POST['data'], 'code');

?>