<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass(array('APPayment.class.php'));
$apPayment = createObjAndAddToCol(new APPayment());

$obj = $apPayment;

$arrCriteria = array();

$fieldValue = $obj->tableName . '.code';
include 'ajax-general.php';
if (isset($_POST) && !empty($_POST['action'])) {
	switch ($_POST['action']){
        case 'updateDocumentFiles':
            if (empty($_POST['pkey']))
                die;

            $pkey = $_POST['pkey'];
            $rsResult = array();

            $itemFileToken = $_POST['token-item-file-uploader'];
            $arrItemFileUploader = explode(',', $_POST['item-file-uploader']);

            $arrQueue = array();
            foreach ($arrItemFileUploader as $row) {
                array_push($arrQueue, array(
                    'token' => $itemFileToken,
                    'fileName' => $row,
                    'uploadFolder' => $obj->uploadFileFolder,
                ));
            }

            $rsResult = $obj->updateDocumentFiles($pkey, 'file', $arrQueue);
            echo json_encode($rsResult);
        break;
            
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