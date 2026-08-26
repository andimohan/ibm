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
            $obj->setLog($_GET, true);
            if(isset($_GET['pkey']) && !empty($_GET['pkey'])){
                $pkey = $_GET['pkey'];
            }

            // $rs = $obj->getDataForPutAway($pkey);
            $rs = $obj->getDataForZoneTransfer($pkey, null);

            echo json_encode($rs);

        break;

        case 'getDataForZoneTransfer':

            $pkey = 0;
            if(isset($_GET['pkey']) && !empty($_GET['pkey'])){
                $pkey = $_GET['pkey'];
            }

            $layoutoriginkey = 0;
            if(isset($_GET['warehouselayoutoriginkey']) && !empty($_GET['warehouselayoutoriginkey'])){
                $warehouselayoutoriginkey = $_GET['warehouselayoutoriginkey'];
            }

            $rs = $obj->getDataForZoneTransfer($pkey,$warehouselayoutoriginkey);
            echo json_encode($rs);

            break;
            
        case 'getDataForLabeling':

            $pkey = 0;
            if (isset($_GET['pkey']) && !empty($_GET['pkey'])) {
                $pkey = $_GET['pkey'];
            }


            $rs = $obj->getDataForLabeling($pkey);

            echo json_encode($rs);

            break;
            
        case 'getDataForGoodsOut':

            $pkey = 0;
            if (isset($_GET['pkey']) && !empty($_GET['pkey'])) {
                $pkey = $_GET['pkey'];
            }


            $rs = $obj->getDataForGoodsOut($pkey, null);

            echo json_encode($rs);

            break;

    }
}
 
die;
  
?>