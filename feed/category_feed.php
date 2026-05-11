<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 
require_once '../_global.php'; 
 
 
$compiledItemCategory= $itemCategory->compileChildArray($class->loadSetting('productCategoryOrder')); 
$arrCategory = $class->convertForCombobox($compiledItemCategory[0]['childnode'], 'pkey','name');

$arrReturn = Array();
 
array_push($arrReturn, array("-1" =>'- All Categories -'));
foreach ($arrCategory as $key => $value){ 
	array_push($arrReturn, array($key=>$value) );
}
  
echo json_encode($arrReturn);
 

?>
