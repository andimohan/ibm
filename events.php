<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';
 
includeClass(array('Event.class.php','EventCategory.class.php'));
$event = new Event();
$eventCategory = new EventCategory();

$pageIndex = 0;
if ( isset($_GET) && !empty($_GET['page']) ){
	$pageIndex = $_GET['page'];
}
$arrTwigVar ['pageIndex'] =  $pageIndex;

$excludeFeatured = ( $class->loadSetting('excludeFeaturedData') > 0 ) ? true : false;

//event 
$totalrowsperpage = $class->loadSetting('newsTotalRowsPerPage'); 
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$orderby = 'order by '.$event->tableName.'.eventdatefrom desc, '.$event->tableName.'.pkey desc';
$criteria =  ' and '.$event->tableName.'.statuskey = 1';



// featured 
$rsEventFeatured = $event->searchDataRow(array($event->tableName.'.pkey',
                                               $event->tableName.'.title',
                                               $event->tableName.'.image',
                                               $event->tableName.'.shortdesc'
                                              ),
                                ' and '.$event->tableName.'.isfeatured = 1 
                                  and '.$event->tableName.'.statuskey = 1 ',
                                ' order by '.$event->tableName.'.eventdatefrom desc, '.$event->tableName.'.pkey desc');
$rsItemImages = $event->getItemImages(array_column($rsEventFeatured,'pkey')); 
$rsItemImages = $event->reindexDetailCollections($rsItemImages,'refkey');

for($i=0;$i<count($rsEventFeatured);$i++){
    $arrItemImage = $rsItemImages[$rsEventFeatured[$i]['pkey']][0]; 
    $rsEventFeatured[$i]['image'] = $arrItemImage['file'];
}

if($excludeFeatured){
    
    if(!empty($rsEventFeatured)) 
        $criteria .= ' and '.$event->tableName.'.pkey not in ('.$class->oDbCon->paramString(array_column($rsEventFeatured,'pkey'),',').')';

}


$rsCategory = $eventCategory->searchDataRow(array($eventCategory->tableName.'.pkey',
                                                 $eventCategory->tableName.'.name',
                                                 $eventCategory->tableName.'.parentkey',
                                                 $eventCategory->tableName.'.statuskey'),
                                           ' and '.$eventCategory->tableName.'.parentkey=0 and '.$eventCategory->tableName.'.statuskey = 1'
                                          );


$cat = (isset($_GET) && !empty($_GET['cat'])) ? $_GET['cat'] : 0 ; 

// khusus event, kalo gk ad kiriman cat, pake cat pertama
if(empty($cat)) $cat = $rsCategory[0]['pkey'];

$arrTwigVar ['rsCategory'] = $rsCategory;
$rsCategory = array_column($arrTwigVar['rsCategory'],null,'pkey');

// gk boleh diisi karena ad web yg gk pake kategori
// nanti dari url html nya saja ditembak categorykey

$arrParentPath = array();

if(!empty($cat)){ 
    
    $criteria .= ' and ' . $event->tableName .'.categorykey = ' . $class->oDbCon->paramString($cat);

    $rsCategory = $rsCategory[$cat];
      
	$arrParentPath[0]['pkey'] = $rsCategory['pkey'];
	$arrParentPath[0]['name'] = $rsCategory['name']; 
	$parentkey = $rsCategory['parentkey'];
	 
	while($parentkey <> 0){ 
		$rsParent = $eventCategory->getDataRowById($parentkey); 
		$parentkey = $rsParent[0]['parentkey'];
		
		$ctr = count($arrParentPath);
		$arrParentPath[$ctr]['pkey'] =  $rsParent[0]['pkey'];
		$arrParentPath[$ctr]['name'] = $rsParent[0]['name']; 
	} 

}

$arrTwigVar['selectedCategory'] = $cat;
$arrTwigVar['categoryPath'] = $arrParentPath;   

$rsEvent = $event->searchData('','',true,$criteria,$orderby,$limit);

$rsItemImages = $event->getItemImages(array_column($rsEvent,'pkey')); 
$rsItemImages = $event->reindexDetailCollections($rsItemImages,'refkey');
 
for($i=0;$i<count($rsEvent);$i++){
    $arrItemImage = $rsItemImages[$rsEvent[$i]['pkey']][0]; 
    $rsEvent[$i]['image'] = $arrItemImage['file'];
}


$arrTwigVar ['rsEvent'] =  $event->updateContentLang($rsEvent); 
$arrTwigVar ['rsEventFeatured'] =  $event->updateContentLang($rsEventFeatured); 
$arrTwigVar ['rsEventCategory'] =  $rsCategory;
  
$totalPages = ceil( $event->getTotalRows($criteria) / $totalrowsperpage);
$arrTwigVar ['totalPages'] =  $totalPages;

echo $twig->render('events.html', $arrTwigVar);

?>