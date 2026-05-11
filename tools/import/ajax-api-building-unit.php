<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BuildingUnit.class.php';

$buildingUnit = new BuildingUnit();   

$url = API_URL.'building-unit';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT
echo $buildingUnit->executeImportAPI($url,$_POST['data'], 'code');

?>