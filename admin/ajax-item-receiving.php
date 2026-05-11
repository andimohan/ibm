<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('ItemReceiving.class.php'));
$itemReceiving= new ItemReceiving();
$obj = $itemReceiving;    

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
	switch ( $_GET['action']){ 
        case 'getDataForPutAway' :


            $pkey = 0;
            if(isset($_GET['pkey']) && !empty($_GET['pkey'])){
                $pkey = $_GET['pkey'];
            }

            $rs = $obj->getDataForPutAway($pkey);
            $obj->setLog($rs, true);

            echo json_encode($rs);

        break;

    }
}
 
die;
  
?>