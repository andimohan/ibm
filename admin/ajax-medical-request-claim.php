<?php
require_once '../_config.php';
require_once '../_include-v2.php';

includeClass('MedicalRequestClaim.class.php');
$medicalRequestClaim = createObjAndAddToCol(new MedicalRequestClaim());

$obj = $medicalRequestClaim;

// gk boleh diset statusnya, karena kalo dr quotation, perlu ambil yg statusnya menunggu

$fieldValue = $obj->tableName . '.code';

include 'ajax-general.php';


if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                case 'getDetailDiagnose' : 
                    if (!isset($_GET) || empty($_GET['pkey'])) die; 
                    $rs = $obj->getDetailDiagnose($_GET['pkey']);
                    echo json_encode($rs); 
                    break; 
					 
				case 'getUnAprrovedDetail' : 
					
                    if (!isset($_GET) || empty($_GET['pkey'])) die; 
					
					$pkey = $_GET['pkey'];
                    
					$arrCriteria = array();
					array_push($arrCriteria, $obj->tableNameDetail.'.statuskey = 1');
					array_push($arrCriteria, $obj->tableItem.'.isquotation = 1');
					
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';   
 
                    $rsData = $obj->getDetailWithRelatedInformation($pkey,$criteria);
                    
                    echo json_encode($rsData); 
                    break; 
					 
            }
}
	
die;