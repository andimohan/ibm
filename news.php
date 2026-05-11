<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

$newsCategory = new NewsCategory();

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
$orderby = 'order by '.$news->tableName.'.publishdate desc, '.$news->tableName.'.pkey desc';
$criteria =  ' and '.$news->tableName.'.statuskey = 1 and publishdate <= now()';


// featured 
//$rsFeaturedNews = $news->searchDataRow(array($news->tableName.'.pkey',
//                                               $news->tableName.'.title',
//                                               $news->tableName.'.image',
//                                               $news->tableName.'.publishdate',
//                                               $news->tableName.'.shortdesc'
//                                              ),
//                                ' and '.$news->tableName.'.featured = 1 and '.$news->tableName.'.publishdate <= now() ',
//                                ' order by '.$news->tableName.'.publishdate desc');

// perlu nama kategori
$rsFeaturedNews = $news->searchData('','',true,' and '.$news->tableName.'.featured = 1  and '.$news->tableName.'.statuskey = 1  and '.$news->tableName.'.publishdate <= now()',$orderby);

//$rsItemImages = $news->getItemImages(array_column($rsFeaturedNews,'pkey')); 
//$rsItemImages = $news->reindexDetailCollections($rsItemImages,'refkey');


if($excludeFeatured){ 
    if(!empty($rsFeaturedNews)) 
        $criteria .= ' and '.$news->tableName.'.pkey not in ('.$class->oDbCon->paramString(array_column($rsFeaturedNews,'pkey'),',').')';
}


// sementara hanya ambil yg level pertama 
$rsCategory = $newsCategory->searchDataRow(array($newsCategory->tableName.'.pkey',
                                                 $newsCategory->tableName.'.name',
                                                 $newsCategory->tableName.'.parentkey',
                                                 $newsCategory->tableName.'.statuskey'),
                                            ' and '.$newsCategory->tableName.'.parentkey=0 and '.$newsCategory->tableName.'.statuskey = 1',
                                           'order by orderlist asc'
                                          );

$arrTwigVar ['rsCategory'] = $rsCategory;

$rsCat = array_column($arrTwigVar ['rsCategory'],null,'pkey');

$arrParentPath = array();
$cat = (isset($_GET) && !empty($_GET['cat'])) ? $_GET['cat'] : 0 ; 

if(!empty($cat)){ 
    
    $criteria .= ' and ' . $news->tableName .'.categorykey = ' . $class->oDbCon->paramString($cat);

    $rsCat = $rsCat[$cat];
      
	$arrParentPath[0]['pkey'] = $rsCat['pkey'];
	$arrParentPath[0]['name'] = $rsCat['name']; 
	$parentkey = $rsCat['parentkey'];
	 
	while($parentkey <> 0){ 
		$rsParent = $newsCategory->getDataRowById($parentkey); 
		$parentkey = $rsParent[0]['parentkey'];
		
		$ctr = count($arrParentPath);
		$arrParentPath[$ctr]['pkey'] =  $rsParent[0]['pkey'];
		$arrParentPath[$ctr]['name'] = $rsParent[0]['name']; 
	} 

}


$arrTwigVar['selectedCategory'] = $cat; 
$arrTwigVar['categoryPath'] = $arrParentPath;   

for($i=0;$i<count($arrTwigVar['categoryPath']);$i++)  
    array_push($arrActive,$arrTwigVar ['SELF_PAGE'].'?'.$arrTwigVar['categoryPath'][$i]['pkey']);  

$rsNews = $news->searchData('','',true,$criteria,$orderby,$limit);

//$rsItemImages = $news->getItemImages(array_column($rsNews,'pkey')); 
//$rsItemImages = $news->reindexDetailCollections($rsItemImages,'refkey');

for($i=0;$i<count($rsNews);$i++){
    $rsNews[$i]['publishDateISO8601'] =  date('c',strtotime($rsNews[$i]['publishdate']));
    $rsNews[$i]['modifiedDateISO8601'] =  date('c',strtotime($rsNews[$i]['publishdate'])); 
    $rsNews[$i]['linktitle'] =  str_replace($class->arrSearch,$class->arrReplace,$rsNews[$i]['title']); 
    $rsNews[$i]['publishdate'] = $class->convertToLocalTimeZone($rsNews[$i]['publishdate'],LOCAL['timezone']['systemGMT'], LOCAL['timezone']['userGMT'] );
}


//$arrTwigVar ['categoryName'] =  $categoryName;
$arrTwigVar ['rsNews'] =  $news->updateContentLang($rsNews); 
$arrTwigVar ['rsFeaturedNews'] =  $news->updateContentLang($rsFeaturedNews); 
  
$totalPages = ceil( $news->getTotalRows($criteria) / $totalrowsperpage);
$arrTwigVar ['totalPages'] =  $totalPages; 
$arrTwigVar ['ACTIVE_MENU'] =  $arrActive; 

echo $twig->render('news.html', $arrTwigVar);

?>