<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Partners.class.php'));

$partners = new Partners();
$partnersCategory = new PartnersCategory();

// category
$rsPartnersCategory = $partnersCategory->searchData($partnersCategory->tableName.'.statuskey',1,true);
	
$orderby = 'order by '.$partners->tableName.'.name asc';
$criteria =  ' and '.$partners->tableName.'.statuskey = 1';
  
$rsPartners = $partners->searchData('','',true,$criteria,$orderby);

$rsPartners = $partners->updateContentLang($rsPartners);
	
$rsPartnersByCategory = $class->reindexDetailCollections($rsPartners,'categorykey');
	
// jika menggunakan lokasi google maps
/*$rsPartnersLocation = array();
foreach($rsPartners as $row){
    if (empty($row['location'])) continue;
    $ltdlng = explode(',',$row['location']);
    if(count($ltdlng) <> 2) continue;
    
    array_push($rsPartnersLocation, array('label' => $row['name'], 'ltd' => $ltdlng[0], 'lng' => $ltdlng[1]));
}*/

// jika menggunakan map sendiri  
$rsPartnersMapLocation = array();
if($IS_ACTIVE_MODULE['portfolio']){
	$rsPortfolio = $portfolio->searchData('','',true, ' and ' .$portfolio->tableName.'.statuskey <> 3 ');
	$rsPortfolio = $portfolio->updateContentLang($rsPortfolio); 
	foreach($rsPortfolio as $row){
		if (empty($row['maplocation'])) continue;
		$ltdlng = explode(',',$row['maplocation']);
		if(count($ltdlng) <> 2) continue;

		$posX = trim($ltdlng[0]);
		$posY = trim($ltdlng[1]);

		$adj = 5;

		// 1000 adalah lebar peta  - fixed - 
		$posXRelative = (($posX - $adj) / 1000) * 100 ; 


		// 370 adalah lebar peta  - fixed - 
		$posYRelative = (($posY - $adj)  / 370) * 100 ; 

		// convert RGB Color
		$color = str_replace("#","",$row['statuscolor']);
		$rgbcolor = array();
		$rgbcolor['r'] =  hexdec(substr($color,0,2));
		$rgbcolor['g'] =  hexdec(substr($color,2,2));
		$rgbcolor['b'] =  hexdec(substr($color,4,2));


		array_push($rsPartnersMapLocation, array('name' => $row['name'],'statusname' =>  $row['statusname'], 'companyname' => $row['companyname'], 'shortdescription' => str_replace(chr(13) ,'<br>', $row['shortdesc']), 'color' => $row['statuscolor'], 'rgbcolor' => $rgbcolor, 'x' => $posX, 'xRelative' => $posXRelative, 'y' => $posY, 'yRelative' => $posYRelative ));
	}
}

$arrTwigVar ['rsPartnersCategory'] = $rsPartnersCategory;
$arrTwigVar ['rsPartners'] = $partners->updateContentLang($rsPartners);  
$arrTwigVar ['rsPartnersByCategory'] = $rsPartnersByCategory;
	
//$arrTwigVar ['rsPartnersLocation'] = $rsPartnersLocation;
$arrTwigVar ['rsPartnersMapLocation'] = $rsPartnersMapLocation;
 
echo $twig->render('partners.html', $arrTwigVar);

?>