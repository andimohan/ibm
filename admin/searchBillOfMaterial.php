<?php 
require_once '../_config.php'; 
require_once '../_include.php';  

$order = 'order by bill_of_materials_header.name asc';

$action = '';
if(!empty($_GET['action']))
	$action = $_GET['action'];

switch ($action){ 
    case 'importDetail';
             $rs = $billOfMaterials->getDetailWithRelatedInformation($_GET['pkey']);
             break;
    default :
             $criteria = ' and '.$billOfMaterials->tableName.'.statuskey = 1';
             $rs = $billOfMaterials->searchDataForAutoComplete($billOfMaterials->tableName.'.name',$_GET['term'],false,$criteria,$order );
            
            for($i=0;$i<count($rs);$i++){
				$rs[$i]['value'] = htmlspecialchars_decode($rs[$i]['value']); 
				$rsItem = $item->getDataRowById($rs[$i]['itemkey']);
				$rs[$i]['itemkey'] = $rsItem[0]['pkey'];
				$rs[$i]['itemname'] = htmlspecialchars_decode($rsItem[0]['name']);  
                    
            }

}
 
echo json_encode($rs); 
die;
  
?>
