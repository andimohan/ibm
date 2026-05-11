<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('EMKLHouseBL.class.php');
$emklHouseBl = createObjAndAddToCol(new EMKLHouseBL()); 

$obj = $emklHouseBl;   


$fieldValue = $obj->tableName.'.code';

if(isset($_POST['action'])){
    switch ( $_POST['action']){ 
        case 'addHBL' : 
                    
                    $arrParam['code'] = 'xxxxxx';
                    $arrParam['trDate'] = date('d / m / Y');
                    $arrParam['_mnv'] = true;

			// coba diudpate sekalian sama shipper di classs
			
//                    $isOverWriteConsignee = 0;
//                    $isOverWriteShipper = 0;
//                    $consigneeName = '';
//                    $shipperName = '';
//            
//                    if(isset($_POST['consigneename']) && !empty($_POST['consigneename'])){
//                        
//                           if ($_POST['jobtype'] == EMKL['jobType']['export']){
//                               $isOverWriteConsignee = 1;
//                                $consigneeName = $_POST['consigneename'];
//                           }else{
//                               $isOverWriteShipper = 1;
//                               $shipperName = $_POST['consigneename'];
//                           }
//                    }
//                    
//                    $arrParam['consigneeName1'] = $consigneeName;
//                    $arrParam['shipperName1'] = $shipperName;
//                    $arrParam['chkIsOverwriteShipper'] = $isOverWriteShipper;
//                    $arrParam['chkIsOverwriteConsignee'] = $isOverWriteConsignee;
//                    
					
                    $arrayToJs = $obj->addData($arrParam);  
                        
                    break;

    }

    echo json_encode($arrayToJs); 
    die;
}

include 'ajax-general.php';


die
  
?>
