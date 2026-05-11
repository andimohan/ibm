<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Port.class.php';

$port = new Port();

$url = API_URL . 'port';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap public_path('')
echo $port->executeImportAPI($url, $_POST['data'], 'code');

?>