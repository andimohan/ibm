<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Car.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CarCategory.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';    

$OBJ = new Car();
$MODULE_NAME = 'car';
$TITLE = $OBJ->lang['car'];
$AJAX_FILE = 'ajax-api-car';
 
$brand = new Brand();
$brandTableKey = $OBJ->getTableKeyAndObj( $brand->tableName ,array('key'))['key'];
  
// diurutkan sesuai dengan urutan kolom di excel
// jika tidak urut (seperti buying FF), harus dihandle manual

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => '')); // index 0 gk dipake, karena excel indexnya dari 1
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'registration_number')); 
array_push($DATA_STRUCTURE, array('field' => 'brand_name')); 
array_push($DATA_STRUCTURE, array('field' => 'category_name'));
array_push($DATA_STRUCTURE, array('field' => 'year'));
array_push($DATA_STRUCTURE, array('field' => 'ownership_name'));
array_push($DATA_STRUCTURE, array('field' => 'ownership_number'));
array_push($DATA_STRUCTURE, array('field' => 'license_number'));
array_push($DATA_STRUCTURE, array('field' => 'license_expired_date')); 
array_push($DATA_STRUCTURE, array('field' => 'tax_expired_date')); 
array_push($DATA_STRUCTURE, array('field' => 'kir_number')); 
array_push($DATA_STRUCTURE, array('field' => 'kir_expired_date')); 
array_push($DATA_STRUCTURE, array('field' => 'machine_number')); 
array_push($DATA_STRUCTURE, array('field' => 'chassis_number')); 
array_push($DATA_STRUCTURE, array('field' => 'status'));
 
// kalo ad beberapa baris utk detail, harus handling manual...
// jgn include _import.php
require_once '_import.php';
?>