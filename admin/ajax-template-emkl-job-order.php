<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('TemplateEMKLJobOrder.class.php');
$templateEMKLJobOrder = createObjAndAddToCol(new TemplateEMKLJobOrder());


$obj = $templateEMKLJobOrder;

$arrCriteria = array();
array_push($arrCriteria, $obj->tableName . '.statuskey = 1');

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
    switch ($_GET['action']) {

        case 'getTemplateInformation':

            if (empty($_GET['pkey']))
                die;

            $pkey = $_GET['pkey'];
            $rs = $obj->searchData('','', true, ' and ' . $obj->tableName.'.statuskey = 1  and ' . $obj->tableName.'.pkey = '. $obj->oDbCon->paramString($pkey) .' ');

            echo json_encode($rs);

            break;
        case 'getContainerVolume':

            if (empty($_GET['pkey']))
                die;

            $pkey = $_GET['pkey'];
            $rs = $obj->getDetailVolume($pkey);

            echo json_encode($rs);

            break;

        case 'getCommodityDetail':

            if (empty($_GET['pkey']))
                die;

            $pkey = $_GET['pkey'];
            $rs = $obj->getDetailCommodity($pkey);

            echo json_encode($rs);

            break;
    
    }
}


die;

?>
