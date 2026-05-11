<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
  
includeClass(array('Gallery.class.php'));  
$gallery = new Gallery();

if(empty($_GET)){
	header("location: /");
	die;
} 
$id = $_GET['id'];

$rsGallery = $gallery->searchData($gallery->tableName.'.pkey',$id,true, ' and '.$gallery->tableName.'.statuskey = 1'); 
if(empty($rsGallery)){
	header("location: /");
	die;
}
 
$rsImage = $gallery->getGalleryImage($id);
$arrTwigVar['rsGallery'] =  $rsGallery;     
$arrTwigVar['rsGalleryImage'] =  $rsImage;    

$arrTwigVar ['META_TITLE'] = $rsGallery[0]['name'];
$arrTwigVar ['META_DESCRIPTION'] ='';
$arrTwigVar ['META_IMAGE'] = $class->defaultURLUploadPath . 'gallery/'.$rsGallery[0]['pkey'].'/'.$rsImage[0]['file']; 

echo $twig->render('gallery-detail.html', $arrTwigVar);
?>
