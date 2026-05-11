<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';    
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CustomerCategory.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';   

$OBJ = new Customer();
$MODULE_NAME = 'customer';
$TITLE = $OBJ->lang['customer'];
$AJAX_FILE = 'ajax-api-customer';

$RESET_TABLE = array( 
            'customer', 
            'customer_category',  
); 
 
$customerCategory = new CustomerCategory(); 
//$customerCategoryTableKey = $OBJ->getTableKeyAndObj( $customerCategory->tableName ,array('key'))['key'];
 
/*$city = new city(); 
$cityTableKey = $OBJ->getTableKeyAndObj( $city->tableName ,array('key'))['key'];*/
 
// diurutkan sesuai dengan urutan kolom di excel
// jika tidak urut (seperti buying FF), harus dihandle manual

$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => '')); // index 0 gk dipake, karena excel indexnya dari 1
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'name'));
array_push($DATA_STRUCTURE, array('field' => 'category_id' , 'replace' => array('obj' => $customerCategory)));  
array_push($DATA_STRUCTURE, array('field' => 'address'));
array_push($DATA_STRUCTURE, array('field' => 'city_name'));
array_push($DATA_STRUCTURE, array('field' => 'zip_code'));
array_push($DATA_STRUCTURE, array('field' => 'phone'));
array_push($DATA_STRUCTURE, array('field' => 'mobile'));
array_push($DATA_STRUCTURE, array('field' => 'fax'));
array_push($DATA_STRUCTURE, array('field' => 'email'));
array_push($DATA_STRUCTURE, array('field' => 'tax_id'));
array_push($DATA_STRUCTURE, array('field' => 'status'));
 
// kalo ad beberapa baris utk detail, harus handling manual... 
require_once '_import.php';
?>