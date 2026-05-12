<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('ItemReceivingPlan.class.php'));
$itemReceivingPlan= new ItemReceivingPlan();
$obj = $itemReceivingPlan;    

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
	switch ( $_GET['action']){ 
        case 'getDataForItemReceiving' :

            $rs = [];

            $pkey = 0;
            if(isset($_GET['pkey']) && !empty($_GET['pkey'])){
                $pkey = $_GET['pkey'];
            }

            $rs = $obj->searchData('','',true, ' and ' . $obj->tableName.'.pkey = '.$obj->oDbCon->paramString($pkey));

            if(!empty($rs)) {
                $rsDetail = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
                $rs[0]['details'] = $rsDetail ?? [];
            }

            echo json_encode($rs);

        break;

    }
}
 
die;
  
?>