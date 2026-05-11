<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Banner.class.php'));
$banner = createObjAndAddToCol(new Banner());


$obj = $banner;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'bannerForm';


 
function generateQuickView($obj,$id){ 
 
	$rs = $obj->getDataRowById($id);   
    $detail = '<div class="data-card" style="border:0; margin:auto; padding-top:1em"><div class="image-panel" style="width:100%;"><div class="image" style="background-image:url(\'../phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc .$obj->uploadFolder.$id.'/'.$rs[0]['file'].'&w=600&h=160&far=C&hash='.getPHPThumbHash($rs[0]['file']).'\'); "></div></div></div>';
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
