<?php 
include '../_config.php'; 
include '../_include.php'; 
include '../_global.php'; 
 
     
$rsFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey',1);

$rsReturn = Array();
 
for ($i=0;$i<count($rsFilterCategory);$i++){
 $rsFilter = $itemFilter->searchData($itemFilter->tableName.'.categorykey',$rsFilterCategory[$i]['pkey']);
 
 $arrFilter = Array();
 for($j=0;$j<count($rsFilter);$j++){
 	array_push($arrFilter,array($rsFilter[$j]['pkey'], $rsFilter[$j]['name']));
 }
 
 $rsReturn[$rsFilterCategory[$i]['name']] = $arrFilter;
}
 

echo json_encode($rsReturn);
 

?>
