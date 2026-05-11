<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('TruckingCostCashOut.class.php'));
$truckingCostCashOut = createObjAndAddToCol(new TruckingCostCashOut()); 
    
$obj = $truckingCostCashOut;   

$arrCriteria = array();   

$fieldValue = $obj->tableName.'.code';
    
include 'ajax-general.php';  
if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){    
         
                case 'searchAvailableReference' :  
                    $arrCriteria = array(); 
                      
                    if ( isset($_GET['code']) && !empty($_GET['code']) ) { 
                         array_push ($arrCriteria, 'and ('.$obj->tableAlias.'.code like '.$obj->oDbCon->paramString('%'.$_GET['code'].'%').')' );
                    }else{
                        if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                            array_push ($arrCriteria, 'and ('.$obj->tableAlias.'.code like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );
                    }
                        
                    $arrCriteria = implode(' ',$arrCriteria);
                    $rs = $obj->searchAvailableReference($arrCriteria);
                    
                    echo json_encode($rs); 
                    break; 
    
                case 'searchDataForRequest':
                    $returnField = array('key' => $obj->tableName . '.pkey', 'value' => $fieldValue);
                    //overwrite field yg di search 
                    $searchFieldValue = (isset($_GET['searchField']) && !empty($_GET['searchField'])) ? explode(',', $_GET['searchField']) : $fieldValue;
                    $searchOptions = array('field' => $searchFieldValue,  'key' => $_GET['term']);

                    $criteria = array();
                    array_push($criteria,  $obj->tableName . '.statuskey =  1 ');

                    if (isset($_GET['recipientkey']) && !empty($_GET['recipientkey'])) {
                        array_push($criteria, $obj->tableName . '.employeekey = ' . $obj->oDbCon->paramString($_GET['recipientkey']));
                    }                    
                    
                    if (isset($_GET['warehousekey']) && !empty($_GET['warehousekey'])) {
                        array_push($criteria, $obj->tableName . '.warehousekey = ' . $obj->oDbCon->paramString($_GET['warehousekey']));
                    } 
 
                    if (isset($_GET) && !empty($_GET['startdate']) && !empty($_GET['enddate'])) {  
                         array_push($criteria, $obj->tableName . '.trdate between ' . $obj->oDbCon->paramDate($_GET['startdate']) . ' AND ' . $obj->oDbCon->paramDate($_GET['enddate']));
                    }

                    
                    $criteria = implode(' and ', $criteria); 
                    $searchOptions['criteria'] = ' and ' . $criteria;
   
                    
                    $rsData = $obj->searchDataForAutoComplete($returnField, $searchOptions, $order);

                    echo json_encode($rsData);
                    break;

                } 
    
}

if (isset($_POST) && !empty($_POST['action'])) {
	switch ($_POST['action']){    
			
			case 'updateDocumentFiles' : 

					if (empty($_POST['pkey'])) die;

                    try{
                        $obj->validateDiskUsage(); 
                        
                        $pkey =  $_POST['pkey'];  
                        $rsResult = array();

                        $itemFileToken = $_POST['token-item-file-uploader'];
                        $arrItemFileUploader = explode(',',$_POST['item-file-uploader']);

                        $arrQueue = array();
                        foreach($arrItemFileUploader as $row){
                            array_push($arrQueue, array('token' => $itemFileToken,
                                                        'fileName' => $row,
                                                        'uploadFolder' => $obj->uploadFileFolder,
                                                        ));
                        }

                        $rsResult  = $obj->updateDocumentFiles($pkey,'file',$arrQueue);

                        echo json_encode($rsResult); 
                    }catch(Exception $e){ 
                        $arrMsg = array();
                        array_push($arrMsg, array('valid'=>false, 'message' =>  $e->getMessage()));
                        echo json_encode($arrMsg);   
                    }
                        
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

die;
  
?>