<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('GalleryCategory.class.php');
$galleryCategory = createObjAndAddToCol(new GalleryCategory());  

$obj = $galleryCategory;    

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');   

include 'ajax-general.php';
 
die;
  
?>