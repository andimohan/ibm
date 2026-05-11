<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/CarServiceMaintenance.class.php';

$carServiceMaintenance = new CarServiceMaintenance();

$url = API_URL . 'car-service-maintenance';


echo $carServiceMaintenance->executeImportAPI($url, $_POST['data'], 'code');


// $carServiceMaintenance->setLog($_POST['data'], true);
// $carServiceMaintenance->setLog($test, true);
// $carServiceMaintenance->setLog($_POST['data'], true);

?>