<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Gallery.class.php'));  
$gallery = new Gallery(2);
$galleryCategory = new GalleryCategory(2);

$pageIndex =( isset($_GET) && !empty($_GET['page']) ) ?  $_GET['page'] : 0;
$rsCategory= array();

$cat = ( isset($_GET) && !empty($_GET['catkey'])) ? $_GET['catkey'] : '';
if(!empty($cat)){
    $rsCategory = $galleryCategory->getDataRowById($cat);
}

$criteriaPublisher = '';
$publisherkey = 0;

/*$publisherName = '';
if ( isset($_GET) && !empty($_GET['publisherkey']) ){
	$publisherkey = $_GET['publisherkey'];
	$rsCustomer = $customer->getDataRowById($publisherkey);
	if (!empty($rsCustomer)){
		$criteriaPublisher = ' and customerkey = ' . $publisherkey;
		$publisherName = $rsCustomer[0]['name'];
	}
}  
	   */
    
$arrTwigVar ['rsCategory'] =  $rsCategory;
$arrTwigVar ['pageIndex'] =  $pageIndex;
 
$totalrowsperpage = $class->loadSetting('gallerytotalitemperpage');
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
     
$criteria = ' and ' .$gallery->tableName.'.statuskey = 1 ' . $criteriaPublisher;

if(!empty($cat)) $criteria .= ' and ' .$gallery->tableName.'.categorykey  = ' .$class->oDbCon->paramString($cat);

$rsGallery = $gallery->searchData('','',true,$criteria,'order by '.$gallery->tableName.'.pkey desc',$limit); 
$totalPages = ceil( $gallery->getTotalRows($criteria) / $totalrowsperpage); 
  
for($i=0;$i<count($rsGallery);$i++){
		$rsImage = $gallery->getGalleryImage($rsGallery[$i]['pkey']);  
        $rsGallery[$i]['file'] = $rsImage[0]['file'];
        $rsGallery[$i]['phpThumbHash'] = getPHPThumbHash($rsImage[0]['file']); 
        $rsGallery[$i]['rsGalleryImage'] = $rsImage;

        $arrTwigVar ['rsGalleryImage'][$rsGallery[$i]['pkey']] =  $rsImage; 
}

$arrTwigVar ['rsGallery'] =  $rsGallery;
$arrTwigVar ['totalPages'] =  $totalPages;  
$arrTwigVar ['activePubisherKey'] = $publisherkey; 
//$arrTwigVar ['activePubisherName'] = $publisherName; 

echo $twig->render('gallery-hr.html', $arrTwigVar);
?>