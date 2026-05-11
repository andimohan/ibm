<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Asset.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AssetCategory.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AssetGroup.class.php';  

$OBJ = new Asset();
$MODULE_NAME = 'asset';
$TITLE = $OBJ->lang['asset'];
$AJAX_FILE = 'ajax-api-asset';

$RESET_TABLE = array( 
            'asset'
); 
 
$warehouse = new Warehouse(); 
$assetCategory = new AssetCategory(); 
$assetGroup = new AssetGroup();  

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => '')); // index 0 gk dipake, karena excel indexnya dari 1
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'name'));
array_push($DATA_STRUCTURE, array('field' => 'warehouse_id' , 'replace' => array('obj' => $warehouse)));  
array_push($DATA_STRUCTURE, array('field' => 'category_id' , 'replace' => array('obj' => $assetCategory)));  
//array_push($DATA_STRUCTURE, array('field' => 'useful_life'));
//array_push($DATA_STRUCTURE, array('field' => 'asset_group_id' , 'replace' => array('obj' => $assetGroup)));  
//array_push($DATA_STRUCTURE, array('field' => 'explicensedate'));
array_push($DATA_STRUCTURE, array('field' => 'acquisition_date','format' => 'date'));
array_push($DATA_STRUCTURE, array('field' => 'acquisition_value'));
array_push($DATA_STRUCTURE, array('field' => 'init_depreciation_value'));

array_push($DATA_STRUCTURE, array('field' => 'status'));
 
// kalo ad beberapa baris utk detail, harus handling manual... 
require_once '_import.php';

?>