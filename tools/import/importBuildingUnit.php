<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/BuildingUnit.class.php';   

$OBJ = new BuildingUnit();
$customer = new Customer();
$buildingUnitCategory = new BuildingUnitCategory();

$MODULE_NAME = 'buildingUnit';
$TITLE = $OBJ->lang['buildingUnit'];
$AJAX_FILE = 'ajax-api-building-unit';

$RESET_TABLE = array( 
        'BuildingUnit'
); 
 
$warehouse = new Warehouse(); 

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => '')); // index 0 gk dipake, karena excel indexnya dari 1
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'block'));
array_push($DATA_STRUCTURE, array('field' => 'unit'));
//array_push($DATA_STRUCTURE, array('field' => 'category_id' , 'replace' => array('obj' => $customer)));  
array_push($DATA_STRUCTURE, array('field' => 'category_id' , 'replace' => array('obj' => new BuildingUnitCategory()))); 
array_push($DATA_STRUCTURE, array('field' => 'owner_id' , 'replace' => array('obj' => $customer)));  
array_push($DATA_STRUCTURE, array('field' => 'tenant_id' , 'replace' => array('obj' => $customer)));   
array_push($DATA_STRUCTURE, array('field' => 'virtual_account'));
array_push($DATA_STRUCTURE, array('field' => 'unit_size'));
array_push($DATA_STRUCTURE, array('field' => 'price_per_square'));
array_push($DATA_STRUCTURE, array('field' => 'status'));
 
// kalo ad beberapa baris utk detail, harus handling manual... 
require_once '_import.php';

?>