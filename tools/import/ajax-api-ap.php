<?php

require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/AP.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Supplier.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Currency.class.php';

$ap = new AP();

$url = API_URL . 'ap';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT

echo $ap->executeImportAPI($url, $_POST['data'], 'code');

?>