<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

//$investorNewsCategory = new NewsCategory();

$pageIndex = 0;
if ( isset($_GET) && !empty($_GET['page']) ){
	$pageIndex = $_GET['page'];
}
$arrTwigVar ['pageIndex'] =  $pageIndex;

$excludeFeatured = ( $class->loadSetting('excludeFeaturedData') > 0 ) ? true : false;

//news 
$totalrowsperpage = $class->loadSetting('newsTotalRowsPerPage'); 
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$orderby = 'order by '.$investorNews->tableName.'.publishdate desc, '.$investorNews->tableName.'.pkey desc';
$criteria =  ' and '.$investorNews->tableName.'.statuskey = 1 and publishdate <= now()';


// featured 
//$rsFeaturedNews = $investorNews->searchDataRow(array($investorNews->tableName.'.pkey',
//                                               $investorNews->tableName.'.title',
//                                               $investorNews->tableName.'.image',
//                                               $investorNews->tableName.'.publishdate',
//                                               $investorNews->tableName.'.shortdesc'
//                                              ),
//                                ' and '.$investorNews->tableName.'.featured = 1 and '.$investorNews->tableName.'.publishdate <= now() ',
//                                ' order by '.$investorNews->tableName.'.publishdate desc');

// perlu nama kategori
$rsFeaturedNews = $investorNews->searchData('','',true,' and '.$investorNews->tableName.'.featured = 1  and '.$investorNews->tableName.'.statuskey = 1  and '.$investorNews->tableName.'.publishdate <= now()',$orderby);

//$rsItemImages = $investorNews->getItemImages(array_column($rsFeaturedNews,'pkey')); 
//$rsItemImages = $investorNews->reindexDetailCollections($rsItemImages,'refkey');


if($excludeFeatured){ 
    if(!empty($rsFeaturedNews)) 
        $criteria .= ' and '.$investorNews->tableName.'.pkey not in ('.$class->oDbCon->paramString(array_column($rsFeaturedNews,'pkey'),',').')';
}


// sementara hanya ambil yg level pertama 
//$rsCategory = $investorNewsCategory->searchDataRow(array($investorNewsCategory->tableName.'.pkey',
//                                                 $investorNewsCategory->tableName.'.name',
//                                                 $investorNewsCategory->tableName.'.parentkey',
//                                                 $investorNewsCategory->tableName.'.statuskey'),
//                                            ' and '.$investorNewsCategory->tableName.'.parentkey=0 and '.$investorNewsCategory->tableName.'.statuskey = 1',
//                                           'order by orderlist asc'
//                                          );

//$arrTwigVar ['rsCategory'] = $rsCategory;
//
//$rsCat = array_column($arrTwigVar ['rsCategory'],null,'pkey');

//$arrParentPath = array();
//$cat = (isset($_GET) && !empty($_GET['cat'])) ? $_GET['cat'] : 0 ; 

//if(!empty($cat)){ 
//    
//    $criteria .= ' and ' . $investorNews->tableName .'.categorykey = ' . $class->oDbCon->paramString($cat);
//
//    $rsCat = $rsCat[$cat];
//      
//	$arrParentPath[0]['pkey'] = $rsCat['pkey'];
//	$arrParentPath[0]['name'] = $rsCat['name']; 
//	$parentkey = $rsCat['parentkey'];
//	 
//	while($parentkey <> 0){ 
//		$rsParent = $investorNewsCategory->getDataRowById($parentkey); 
//		$parentkey = $rsParent[0]['parentkey'];
//		
//		$ctr = count($arrParentPath);
//		$arrParentPath[$ctr]['pkey'] =  $rsParent[0]['pkey'];
//		$arrParentPath[$ctr]['name'] = $rsParent[0]['name']; 
//	} 
//
//}


//$arrTwigVar['selectedCategory'] = $cat; 
//$arrTwigVar['categoryPath'] = $arrParentPath;   
//
//for($i=0;$i<count($arrTwigVar['categoryPath']);$i++)  
//    array_push($arrActive,$arrTwigVar ['SELF_PAGE'].'?'.$arrTwigVar['categoryPath'][$i]['pkey']);  

$rsNews = $investorNews->searchData('','',true,$criteria,$orderby,$limit);

//$rsItemImages = $investorNews->getItemImages(array_column($rsNews,'pkey')); 
//$rsItemImages = $investorNews->reindexDetailCollections($rsItemImages,'refkey');

for($i=0;$i<count($rsNews);$i++){
    $rsNews[$i]['publishDateISO8601'] =  date('c',strtotime($rsNews[$i]['publishdate']));
    $rsNews[$i]['modifiedDateISO8601'] =  date('c',strtotime($rsNews[$i]['publishdate'])); 
    $rsNews[$i]['linktitle'] =  str_replace($class->arrSearch,$class->arrReplace,$rsNews[$i]['title']); 
    $rsNews[$i]['publishdate'] = $class->convertToLocalTimeZone($rsNews[$i]['publishdate'],LOCAL['timezone']['systemGMT'], LOCAL['timezone']['userGMT'] );
}


//$arrTwigVar ['categoryName'] =  $categoryName;
$arrTwigVar ['rsNews'] =  $investorNews->updateContentLang($rsNews); 
$arrTwigVar ['rsFeaturedNews'] =  $investorNews->updateContentLang($rsFeaturedNews); 
  
$totalPages = ceil( $investorNews->getTotalRows($criteria) / $totalrowsperpage);
$arrTwigVar ['totalPages'] =  $totalPages; 
$arrTwigVar ['ACTIVE_MENU'] =  $arrActive; 

echo $twig->render('investor-news.html', $arrTwigVar);

?>