<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Continent.class.php';

$continent = new Continent();

$url = API_URL . 'continents';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap public_path('')
echo $continent->executeImportAPI($url, $_POST['data'], 'code');

?>