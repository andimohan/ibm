<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Location.class.php';

$location = new Location();

$url = API_URL . 'locations';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap public_path('')
echo $location->executeImportAPI($url, $_POST['data'], 'code');

?>