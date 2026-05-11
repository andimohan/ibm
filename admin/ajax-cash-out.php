<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass(array('CashOut.class.php'));
$cashOut = createObjAndAddToCol(new CashOut());

$obj = $cashOut;

$arrCriteria = array();

$fieldValue = $obj->tableName . '.code';
include 'ajax-general.php';
if (isset($_POST) && !empty($_POST['action'])) {
	switch ($_POST['action']){
       
        case 'updateFileAjax' : 
                   
                if (empty($_POST['pkey'])) die;
                
                // harus panggil update sql jg
                // kedepan dicoba bisa tdk bebrapa element sekaligus
                $rsResult = $obj->updateFileAjax(array(array('elName' => 'fileDetail', 
                                                             'detailPkey' => 'hidDetailFileKey',
                                                             'uploadFolder' => $obj->uploadFileFolder, 
                                                             'tableFile' => $obj->tableFile))); 

                echo json_encode($rsResult); 
                break;
    }
}

?>