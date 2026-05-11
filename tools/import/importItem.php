<?php
require_once '../../_config.php'; 
require_once '_include.php';    
require_once 'function-v2.php';
  
ini_set ('max_execution_time', '3000'); // 50 menit ??

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';

$OBJ = new Item();
$MODULE_NAME = 'item';
$TITLE = $OBJ->lang['item'];
$AJAX_FILE = 'ajax-api-item';

$itemUnit = new ItemUnit(); 
//$itemUnitTableKey = $OBJ->getTableKeyAndObj( $itemUnit->tableName ,array('key'))['key'];

$brand = new Brand();
//$brandTableKey = $OBJ->getTableKeyAndObj( $brand->tableName ,array('key'))['key'];
  
// diurutkan sesuai dengan urutan kolom di excel
// jika tidak urut (seperti buying FF), harus dihandle manual


$DATA_STRUCTURE = array();
array_push($DATA_STRUCTURE, array('field' => '')); // index 0 gk dipake, karena excel indexnya dari 1
array_push($DATA_STRUCTURE, array('field' => 'code'));
array_push($DATA_STRUCTURE, array('field' => 'barcode'));
array_push($DATA_STRUCTURE, array('field' => 'parent_id', 'replace' => array('obj' =>$OBJ, 'field' => 'code'))); 
array_push($DATA_STRUCTURE, array('field' => 'name'));
//array_push($DATA_STRUCTURE, array('field' => 'category_name'));
array_push($DATA_STRUCTURE, array('field' => 'category_id' , 'replace' => array('obj' => new ItemCategory()))); 
//array_push($DATA_STRUCTURE, array('field' => 'brand_name' , 'ref' => array('reftabletype' => $brandTableKey , 'apiURL' => 'brands'))); 
array_push($DATA_STRUCTURE, array('field' => 'brand_id' , 'replace' => array('obj' => $brand))); 
array_push($DATA_STRUCTURE, array('field' => 'condition')); // ini nanti perlu dicek
array_push($DATA_STRUCTURE, array('field' => 'weight')); 
//array_push($DATA_STRUCTURE, array('field' => 'weight_unit')); 
array_push($DATA_STRUCTURE, array('field' => 'weight_unit_id' , 'replace' => array('obj' => $itemUnit))); 
//array_push($DATA_STRUCTURE, array('field' => 'base_unit' , 'ref' => array('reftabletype' => $itemUnitTableKey , 'apiURL' => 'item-units')));
array_push($DATA_STRUCTURE, array('field' => 'base_unit_id', 'replace' => array('obj' => $itemUnit))); 
array_push($DATA_STRUCTURE, array('field' => 'min_stock')); 
array_push($DATA_STRUCTURE, array('field' => 'max_stock')); 
array_push($DATA_STRUCTURE, array('field' => 'selling_price')); 
array_push($DATA_STRUCTURE, array('field' => 'short_description')); 
array_push($DATA_STRUCTURE, array('field' => 'image_url')); // ini ganti token saja nanti
array_push($DATA_STRUCTURE, array('field' => 'status'));
 
require_once '_import.php';
?>