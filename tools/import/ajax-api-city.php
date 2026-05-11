<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/City.class.php';

$city = new City();

$url = API_URL . 'cities';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap public_path('')
echo $city->executeImportAPI($url, $_POST['data'], 'code');

?>