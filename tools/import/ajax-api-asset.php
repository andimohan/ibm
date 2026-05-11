<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Asset.class.php';

$asset = new Asset();   

$url = API_URL.'assets';
  
// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT
echo $asset->executeImportAPI($url,$_POST['data'], 'code');

?>