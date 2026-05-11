<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Country.class.php';

$country = new Country();

$url = API_URL . 'countries';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap public_path('')
echo $country->executeImportAPI($url, $_POST['data'], 'code');

?>