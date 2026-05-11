<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Vessel.class.php';

$port = new Vessel();

$url = API_URL . 'vessel';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap public_path('')
echo $port->executeImportAPI($url, $_POST['data'], 'code');

?>