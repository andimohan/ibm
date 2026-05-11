<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SupplierCategory.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';   

$OBJ = new Supplier();
$supplierCategory = new SupplierCategory();

$MODULE_NAME = 'supplier';
$TITLE = $OBJ->lang['supplier'];
$AJAX_FILE = 'ajax-api-supplier';
validateSecurity($OBJ, $MODULE_NAME, $spreadsheet); 

$RESET_TABLE = array( 
            'supplier',  
            'supplier_category',  
); 
 
$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => '')); 
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'name'));
array_push($DATA_STRUCTURE, array('field' => 'category_id' , 'replace' => array('obj' => $supplierCategory))); 
array_push($DATA_STRUCTURE, array('field' => 'tax_id'));
array_push($DATA_STRUCTURE, array('field' => 'address'));
array_push($DATA_STRUCTURE, array('field' => 'city_name'));
array_push($DATA_STRUCTURE, array('field' => 'zip_code'));
array_push($DATA_STRUCTURE, array('field' => 'phone'));
array_push($DATA_STRUCTURE, array('field' => 'mobile'));
array_push($DATA_STRUCTURE, array('field' => 'fax'));
array_push($DATA_STRUCTURE, array('field' => 'email'));
array_push($DATA_STRUCTURE, array('field' => 'status')); 

require_once '_import.php';
?>
