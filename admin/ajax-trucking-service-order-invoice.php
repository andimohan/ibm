<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';   

includeClass(array('TruckingServiceOrderInvoice.class.php'));
$truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();

$obj = $truckingServiceOrderInvoice;

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php'; 
 if (isset($_GET) && !empty($_GET['action'])) {

     switch ($_GET['action']){ 
        case 'getTaxPercentageType' : 
                   
                if (empty($_GET['pkey'])) die;

				$pkey =  $_GET['pkey'];  
                $rsResult = $obj->getTaxPercentageType($pkey);
                                       
                echo json_encode($rsResult); 
                break;


		case 'searchDataForVatOut':

			$criteria = array();
			
			// kalo search manual gpp, karena utk revisi
			if (!isset($_GET['hastaxinvoice']) || empty($_GET['hastaxinvoice']))
				array_push($criteria, $obj->tableName . '.reftaxinvoicekey = 0');
			else
				array_push($criteria, $obj->tableName . '.reftaxinvoicekey != 0');

			 
			array_push($criteria, $obj->tableName.'.refvatoutkey = 0');
                    
			 
			if (isset($_GET['term']) && !empty($_GET['term']))
				array_push($criteria, $obj->tableName . '.code like ' . $obj->oDbCon->paramString('%' . $_GET['term'] . '%'));

			if (isset($_GET['taxType']) && $_GET['taxType'] >= 0)
				array_push($criteria, $obj->tableNameItemDetail . '.taxdetail =' . $obj->oDbCon->paramString($_GET['taxType']));

			if (!empty($_GET['warehouseKey']))
				array_push($criteria, $obj->tableName . '.warehousekey =' . $obj->oDbCon->paramString($_GET['warehouseKey']));

			// if(!empty($_GET['businessUnitKey'])) 
			//	 array_push($criteria, $obj->tableName.'.businessunitkey =' . $obj->oDbCon->paramString( $_GET['businessUnitKey']) );

			if (!empty($_GET['period'])) {
				array_push($criteria, 'month(' . $obj->tableName . '.trdate) = ' . $obj->oDbCon->paramString(date("m", strtotime($_GET['period']))));
				array_push($criteria, 'year(' . $obj->tableName . '.trdate) = ' . $obj->oDbCon->paramString(date("Y", strtotime($_GET['period']))));
			}
			 
			$criteria = implode(' and ', $criteria);
			
			if (!empty($criteria))
				$criteria = ' and ' . $criteria;
			
			$searchOptions = array();
			$searchOptions['criteria'] = $criteria;

			$rsData = $obj->generateDataForVatOut($searchOptions);
			
			echo json_encode($rsData);
		break;

     }
}

 if (isset($_POST) && !empty($_POST['action'])) {

     switch ($_POST['action']){ 
        case 'updateDocumentFiles' : 
                   
                if (empty($_POST['pkey'])) die;

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
                break;
			 
			 
        case 'updateDocumentTaxFiles' : 
                   
                if (empty($_POST['pkey'])) die;

				$pkey =  $_POST['pkey'];  
                $rsResult = array();
			 	
			 	$itemFileToken = $_POST['token-item-file-tax-uploader'];
			 	$arrItemFileUploader = explode(',',$_POST['item-file-tax-uploader']);
			 
			 	$arrQueue = array();
			 	foreach($arrItemFileUploader as $row){
					array_push($arrQueue, array('token' => $itemFileToken,
												'fileName' => $row,
												'uploadFolder' => $obj->uploadFileTaxFolder,
												));
				}
		 
			 	$rsResult  = $obj->updateDocumentFiles($pkey,'filetax',$arrQueue);
			   
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
die;
  
?>
