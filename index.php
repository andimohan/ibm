<?php   
require_once '_config.php'; 
require_once '_include-fe-v2.php';

// kalo ada landing page
// ini pernah masalah, redirect terus atau next time coba pake absolute path
// if (empty($_SESSION['FIRST_LOAD'])){
// 	$_SESSION['FIRST_LOAD'] = true;
	
//   $landingPage = $class->loadSetting('firstLandingURL');
//   if(!empty($landingPage)){
// 		header('Location: '. $landingPage);
// 		die;
//   } 
// }

require_once '_global.php';  

if(!PLAN_TYPE['usefrontend']){  
   header('Location: /admin');
   die;
} 

// khusus icommunity
//if (DOMAIN_NAME == 'icommunity.id'){
//	
//	includeClass(array('GiveOpportunity.class.php')); 
//	
//	$giveOpportunity = new GiveOpportunity(); 
//	$rsCounter = $giveOpportunity->countSummary();
//	$indexCounter = array(); 
//		
//	$indexCounter['businessRefer'] = $rsCounter['businessRefer'] + 10;
//	$indexCounter['transactionAmount'] = ($rsCounter['transactionAmount'] + 100000000) ;
//	$arrTwigVar['indexCounter'] = $indexCounter; 
//    
//    // total member registered
//    $customer = new Customer();
//    $rsMemberRegistered = $customer->searchDataRow(array($customer->tableName.'.pkey'), ' and '. $customer->tableName.'.statuskey = 2');
//    $arrTwigVar['totalMemberRegistered'] = count($rsMemberRegistered);
//    
//    
//    //search member
//    $arrTwigVar['inputSearchMemberIndex'] = $customer->inputText('searchMemberIndex');
//
//}

// sementara
//if (DOMAIN_NAME == 'hrta.wintera.co.id'){
//		$url = "https://backbone.hrtagold.id/api/v1/prices/one";
//
//		$headers = [
//			"Accept: application/json",
//			"User-Agent: PostmanRuntime/7.39.0",
//			"CLIENT-ID: ComProHRTA",
//			"CLIENT-SECRET: GPAwxqyRqmbuKBbcWRCyMIxN12rlAvLTGQ6h0+4ggtIwNGDs7YR7pQNnZ/YqbGE6oowoE/9XNzHlyhenceMqmHGaRx6whPGQOLQQBuNJVkJYIjOfaBQhgCaqdRFX9js="
//		];
//
//		$ch = curl_init();
//
//		curl_setopt_array($ch, [
//			CURLOPT_URL => $url,
//			CURLOPT_RETURNTRANSFER => true,
//			CURLOPT_HTTPHEADER => $headers,
//			CURLOPT_CUSTOMREQUEST => "GET"
//		]);
//
//		$response = curl_exec($ch); 
//		curl_close($ch);
//	
//		$rateHistory = json_decode($response,true);
//		$rateHistory = $rateHistory['data'];
//		
//		$arrTwigVar['rateHistory'] = array('latestPrice' => $rateHistory['latest_price'], 'priceGap' => $rateHistory['price_gap'], 'lastUpdate' => $rateHistory['created_at']);
//		curl_close($ch); 
//
//}
	
if($IS_ACTIVE_MODULE['item']){
	
	includeClass(array('Item.class.php','Category.class.php','ItemCategory.class.php','Brand.class.php','DiscountScheme.class.php')); 

	$item = new Item();
	$brand = new Brand();
	$discountScheme = new DiscountScheme();
    $itemCategory = new ItemCategory();


	/* DISCOUNTED PRODUCTS */
	$rsDiscountedItem = $discountScheme->getAllDiscountedItem(); 

	for($i=0;$i<count($rsDiscountedItem);$i++){
		$rsItemImage = $item->getItemImage($rsDiscountedItem[$i]['itemkey']);
		$rsDiscountedItem[$i]['mainimage'] = $rsItemImage[0]['file'];	 
		$rsDiscountedItem[$i]['discpercentage'] = 100 - ($rsDiscountedItem[$i]['discountedprice']  / $rsDiscountedItem[$i]['sellingprice'] * 100);
	}

	$arrTwigVar['rsDiscountedItem'] = $rsDiscountedItem;

	$totalRandomProducts = $class->loadSetting('totalRandomProducts');
	$qohCriteria = '';
	$webQOHCriteria = '';

	if(!empty($totalRandomProducts)){

		if (!IGNORE_QOH){ 
			$qohCriteria = ' having qtyonhand > 0 ';
			$webQOHCriteria = ' and iswebqoh = 1';
		}

		$rsRandomItem = $item->searchData('','',true,' and '.$item->tableName.'.statuskey = 1 and '.$item->tableName.'.isvariant = 0','order by rand()',' limit 0,' .$totalRandomProducts, $qohCriteria, $webQOHCriteria );

		for($i=0;$i<count($rsRandomItem);$i++){
			$rsItemImage = $item->getItemImage($rsRandomItem[$i]['pkey']);
			$rsRandomItem[$i]['mainimage'] = $rsItemImage[0]['file'];	 
			$rsRandomItem[$i]['linkname'] =  str_replace($class->arrSearch,$class->arrReplace,$rsRandomItem[$i]['name']); 

			if (IGNORE_QOH)
				$rsRandomItem[$i]['qtyonhand'] = 99999;

		}

		$arrTwigVar['rsItem'] = $rsRandomItem;

		$arrTwigVar ['STRUCTURE_DATA'] = $item->generateStructurData($rsRandomItem);  
	}

	$rsBrand = $brand->searchData($brand->tableName.'.statuskey',1,true,' and  '.$brand->tableName.'.publish = 1','order by orderlist asc');
	$arrTwigVar['rsBrand'] = $rsBrand; 
	

	$rsBestSellerItem = $item->searchData($item->tableName.'.statuskey',1,true, ' and '.$item->tableName.'.isshow = 1', 'order by totalsold desc', 'limit ' . $totalRandomProducts, $qohCriteria, $webQOHCriteria );
	foreach($rsBestSellerItem as $key=>$itemRow){ 
		$rsItemImage = $item->getItemImage($itemRow['pkey']);
		$rsBestSellerItem[$key]['mainimage'] = $rsItemImage[0]['file'];	
		$rsBestSellerItem[$key]['description'] = $item->getItemDescription($itemRow['pkey']);	  
		//$rsBestSellerItem[$key]['promo'] = $voucher->checkHasPromo($itemRow); 

	} 
	$arrTwigVar['rsBestSellerItem'] = $rsBestSellerItem;

	$rsFeaturedItem = $item->searchData($item->tableName.'.statuskey',1,true, ' and '.$item->tableName.'.publish = 1 and '.$item->tableName.'.isvariant = 0');
	foreach($rsFeaturedItem as $key=>$itemRow){ 
		$rsItemImage = $item->getItemImage($itemRow['pkey']);
		$rsFeaturedItem[$key]['mainimage'] = $rsItemImage[0]['file'];	
		$rsFeaturedItem[$key]['description'] = $item->getItemDescription($itemRow['pkey']);	 
		//$rsFeaturedItem[$key]['promo'] = $voucher->checkHasPromo($itemRow); 

	} 

	$arrTwigVar['rsFeaturedItem'] = $rsFeaturedItem;
    
    
	$rsFeaturedItemCategory = $itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true, ' and '.$itemCategory->tableName.'.featured = 1', 'order by orderlist asc');
    $arrTwigVar['rsFeaturedItemCategory'] = $rsFeaturedItemCategory;

}


if($IS_ACTIVE_MODULE['event']){
	includeClass(array('Event.class.php')); 
	$event = new Event();
	$rsEventFeatured = $event->searchData($event->tableName.'.statuskey',1,true,' and  '.$event->tableName.'.isfeatured = 1');


    $rsItemImages = $event->getItemImages(array_column($rsEventFeatured,'pkey')); 
    $rsItemImages = $event->reindexDetailCollections($rsItemImages,'refkey');

    for($i=0;$i<count($rsEventFeatured);$i++){
        $arrItemImage = $rsItemImages[$rsEventFeatured[$i]['pkey']][0]; 
        $rsEventFeatured[$i]['image'] = $arrItemImage['file'];
    }

	$arrTwigVar['featuredEvents'] =  $event->updateContentLang($rsEventFeatured); 
	
	
	// next
	$rsNextEvents = $event->searchData($event->tableName.'.statuskey',1,true,' and  '.$event->tableName.'.eventdateto > now()');


    $rsItemImages = $event->getItemImages(array_column($rsNextEvents,'pkey')); 
    $rsItemImages = $event->reindexDetailCollections($rsItemImages,'refkey');

    for($i=0;$i<count($rsNextEvents);$i++){
        $arrItemImage = $rsItemImages[$rsNextEvents[$i]['pkey']][0]; 
        $rsNextEvents[$i]['image'] = $arrItemImage['file'];
    }

	$arrTwigVar['rsNextEvents'] =  $event->updateContentLang($rsNextEvents); 
}



if($IS_ACTIVE_MODULE['achievement']){
	includeClass(array('Achievement.class.php')); 
	$achievement = new Achievement();
	$rsAchievementFeatured = $achievement->searchData($achievement->tableName.'.statuskey',1,true,' and  '.$achievement->tableName.'.featured = 1',' order by ' .$achievement->tableName.'.publishdate desc, ' .$achievement->tableName.'.pkey desc  limit ' . $class->loadSetting('latestNews') );
  
	$arrTwigVar['featuredAchievement'] = $achievement->updateContentLang($rsAchievementFeatured);   
}
 
if($IS_ACTIVE_MODULE['portfolio']){
	includeClass(array('Portfolio.class.php')); 
	$portfolio = new Portfolio();
	$rsPortfolio = $portfolio->searchData($portfolio->tableName.'.statuskey',1,true,' order by '.$portfolio->tableName.'.orderlist asc, '.$portfolio->tableName.'.companyname asc');
	for($i=0;$i<count($rsPortfolio);$i++){  
		$rsItemImage = $portfolio->getItemImages($rsPortfolio[$i]['pkey']);
		if(empty($rsItemImage)) continue;

		$rsPortfolio[$i]['mainimage'] = $rsItemImage[0]['file'];	
		$rsPortfolio[$i]['mainimagewidth'] = $rsItemImage[0]['width'];	
		$rsPortfolio[$i]['mainimageheight'] = $rsItemImage[0]['height'];	
	}
	$arrTwigVar['rsPortfolio'] = $rsPortfolio;
}


if($IS_ACTIVE_MODULE['service']){
	includeClass(array('Service.class.php')); 
    $service = new Service();
	$rsServices = $service->searchData($service->tableName.'.statuskey',1,true); 
	for($i=0;$i<count($rsServices);$i++){  
		$rsItemImage = $service->getItemImage($rsServices[$i]['pkey']);
		$rsServices[$i]['mainimage'] = $rsItemImage[0]['file'];	
		$rsServices[$i]['description'] = $service->getItemDescription($rsServices[$i]['pkey']);	
	} 
	$arrTwigVar['rsServices'] = $rsServices;  
}

if($IS_ACTIVE_MODULE['investorrelations']){
    
	includeClass(array('InvestorRelations.class.php')); 
    $investorRelations = new InvestorRelations();
    
    $rsCYInvestorRelations = $investorRelations->searchData($investorRelations->tableName.'.statuskey',1,true, ' and ' . $investorRelations->tableName.'.year = YEAR(now())', 'limit 1'); 
    
    $arrInvetorImages = array();
    array_push($arrInvetorImages, array('key' => 'chartimage', 'uploadFolder' => $investorRelations->chartUploadFolder));
    array_push($arrInvetorImages, array('key' => 'coverimage', 'uploadFolder' => $investorRelations->coverUploadFolder));
     
    $arrTwigVar['rsCYInvestorRelations'] = $rsCYInvestorRelations;  
        
    
    // all reports
    $rsInvestorRelations = $investorRelations->searchData($investorRelations->tableName.'.statuskey',1,true, '', 'order by year desc'); 
    $arrTwigVar['rsInvestorRelations'] = $rsInvestorRelations;  
    
}

if($IS_ACTIVE_MODULE['subsidiaries']){
    
	includeClass(array('Subsidiaries.class.php')); 
    $subsidiaries = new Subsidiaries();
    
    $rsSubsidiaries = $subsidiaries->searchData($subsidiaries->tableName.'.statuskey',1,true, '','order by orderlist asc'); 
 
    $arrTwigVar['rsSubsidiaries'] = $rsSubsidiaries;  
    
}



if($IS_ACTIVE_MODULE['features']){
    
	includeClass(array('Features.class.php')); 
    $features = new Features();
    
    $rsFeatures = $features->searchData($features->tableName.'.statuskey',1,true, '','order by orderlist asc'); 
 
    $arrTwigVar['rsFeatures'] = $rsFeatures;  
    
}


if($IS_ACTIVE_MODULE['corporatevalues']){
    
	includeClass(array('CorporateValues.class.php')); 
    $corporateValues = new CorporateValues();
    
    $rsCorporateValues = $corporateValues->searchData($corporateValues->tableName.'.statuskey',1,true, '', 'order by orderlist asc'); 
   
    $arrTwigVar['rsCorporateValues'] = $corporateValues->updateContentLang($rsCorporateValues);  
        
}


/* ===================== NEWS ========================================== */  

if($IS_ACTIVE_MODULE['article']){ 
	
	includeClass(array('Article.class.php')); 
	$article = new Article();
	
	/* ===================== ARTICLES ========================================== */  
	$rsLatestArticles = $article->searchData($article->tableName.'.statuskey',1,true, ' and publishdate <= now()',' order by '.$article->tableName.'.publishdate desc', ' limit ' . $class->loadSetting('latestNews'));
	$arrTwigVar['rsLatestArticles'] = $article->updateContentLang($rsLatestArticles);  


/* ===================== ARTICLES ========================================== */  
	$rsLatestArticles = $article->searchData($article->tableName.'.statuskey',1,true, '  and featured = 1 and publishdate <= now()',' order by '.$article->tableName.'.publishdate desc', ' limit ' . $class->loadSetting('latestNews'));
	$arrArticleKey = array_column($rsLatestArticles,'pkey');
	$rsArticleCategory = $article->getDetailWithRelatedInformation($arrArticleKey);
	$rsArticleCategoryCol = array_column($rsArticleCategory,null,'refkey');

	foreach($rsLatestArticles as $key=>$articlesRow){  
	  $rsLatestArticles[$key]['categoryname'] =  $rsArticleCategoryCol[$articlesRow['pkey']]['categoryname'];
	} 
	$arrTwigVar['rsLatestFeaturedArticles'] = $article->updateContentLang($rsLatestArticles);    
}
 

/* ===================== TESTIMONIAL ===================== */

if($IS_ACTIVE_MODULE['testimonial']){
    $testimonial = new Testimonial();
	$rsRandTestimonial = $testimonial->getRandomData(10);
	
	$arrTwigVar['rsRandTestimonial'] = $testimonial->updateContentLang($rsRandTestimonial);  
}


/* ===================== Gallery ========================================== */  

if($IS_ACTIVE_MODULE['gallery']){ 
	
	includeClass(array('Gallery.class.php')); 
	$gallery = new Gallery();

	$limitGallery =  $class->loadSetting('latestGallery');
	if(empty($limitGallery)) $limitGallery = 25;

	$rsLatestFeaturedGallery = $gallery->searchData($gallery->tableName.'.statuskey',1,true,' and featured = 1 ', ' limit ' .$limitGallery);
	foreach($rsLatestFeaturedGallery as $key=>$galleryRow){ 
		$rsImage = $gallery->getGalleryImage($galleryRow['pkey']); 
		$rsLatestFeaturedGallery[$key]['mainimage'] = $rsImage[0]['file'];	
	} 
	$arrTwigVar['rsLatestFeaturedGallery'] = $rsLatestFeaturedGallery;  

	/* ===================== All Galleries / Portfolio ========================================== */  
	$rsGallery = $gallery->searchData($gallery->tableName.'.statuskey',1);
	foreach($rsGallery as $key=>$galleryRow){ 
		$rsImage = $gallery->getGalleryImage($galleryRow['pkey']); 
		$rsGallery[$key]['mainimage'] = $rsImage[0]['file'];	
	} 
	
	$arrTwigVar['rsGallery'] = $rsGallery;  
}



/* ===================== NEW RELEASE ========================================== */  

if($IS_ACTIVE_MODULE['newrelease']){ 
	
	includeClass(array('NewRelease.class.php')); 
	$newRelease = new NewRelease();
	 
	$rsLatestNewRelease = $newRelease->searchData($newRelease->tableName.'.statuskey',1,true, ' and publishdate <= now()',' order by '.$newRelease->tableName.'.publishdate desc');
	$rsLatestNewRelease = $newRelease->updateContentLang($rsLatestNewRelease);
    
    foreach($rsLatestNewRelease as $key=>$data){
        $rsItemImage = $newRelease->getItemImages($data['pkey']);     
        $rsLatestNewRelease[$key]['imagedetail'] = $rsItemImage;
    }
    
   $arrTwigVar['rsLatestNewRelease'] = $rsLatestNewRelease;
    
}


/* ===================== CITY ========================================== */  
 
if($IS_ACTIVE_MODULE['city']){ 
	
	includeClass(array('City.class.php')); 
	$city = new City();
	 
	$rsCity = $city->searchData($city->tableName.'.statuskey',1,true);
    
   $arrTwigVar['rsCity'] = $rsCity;
    
}
 

$arrTwigVar ['inputHidItemkey'] =  $class->inputHidden('hiditemkey[]');
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action',array('value' => 'addToCart'));
$arrTwigVar ['inputQty'] = $class->inputNumber('orderQty[]',array('value' => '1')); 
$arrTwigVar ['btnAddToCart'] =  $class->inputSubmit('btnSave', $class->lang['addToCart']);
    
echo $twig->render('index.html', $arrTwigVar);

?>