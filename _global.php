<?php
    
require_once  $_SERVER ['DOCUMENT_ROOT'].'/assets/vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader($class->templateDocPath); 
$twig = new \Twig\Environment($loader);

//$twig = new Twig_Environment($loader); 
//$twig->addExtension(new Twig_Extension_Array());   

require_once  $_SERVER ['DOCUMENT_ROOT'].'/_twig-function.php';

$arrTwigVar = array();

$arrTwigVar ['PAGE_ID'] = basename ($_SERVER['PHP_SELF'] ,".php");
$arrTwigVar ['DOMAIN_NAME'] = DOMAIN_NAME;
$arrTwigVar ['SELF_PAGE'] = $_SERVER['PHP_SELF'];  
$arrTwigVar ['HTTP_HOST'] = HTTP_HOST; 
$arrTwigVar ['REQUEST_URI'] = REQUEST_URI;

$selectedActivePage = str_replace('/personalized/'.DOMAIN_NAME,'',$arrTwigVar ['SELF_PAGE']); 
$selectedActivePage = str_replace('.php','',$selectedActivePage); // hilangkan ext php nya
$arrActive = array($selectedActivePage);
$arrTwigVar ['ACTIVE_MENU'] = $arrActive;  

$arrTwigVar ['TEMPLATE_CSS_PATH'] =  $class->templateCssPath;
$arrTwigVar ['TEMPLATE_JS_PATH'] =  $class->templateJsPath;
$arrTwigVar ['TEMPLATE_JS_PAGE_PATH'] =  $class->templateJsPath;
$arrTwigVar ['TEMPLATE_IMG_PATH'] =  $class->templateImgPath;
 
$arrTwigVar ['DEFAULT_URL_UPLOAD_PATH'] =  DEFAULT_URL_UPLOAD_PATH;
$arrTwigVar ['DEFAULT_DOC_UPLOAD_PATH'] =  DEFAULT_DOC_UPLOAD_PATH;
$arrTwigVar ['UPLOAD_TEMP_DOC_SHORT'] =  UPLOAD_TEMP_DOC_SHORT; 
$arrTwigVar ['UPLOAD_TEMP_URL'] =  UPLOAD_TEMP_URL;  
$arrTwigVar ['PHPTHUMB_URL_SRC'] =  PHPTHUMB_URL_PATH; 
 
$arrTwigVar ['META_URL'] = $class->loadSetting('sitesName') . $_SERVER['REQUEST_URI']; // dulu metaURL
$arrTwigVar ['META_TYPE'] = $class->loadSetting('metaType');
$arrTwigVar ['META_TITLE'] = $class->loadSetting('metaTitle');
$arrTwigVar ['META_DESCRIPTION'] = $class->loadSetting('metaDescription');
$arrTwigVar ['META_IMAGE'] = $class->defaultURLUploadPath . 'setting/metaImage/'.$class->loadSetting('metaImage'); 
$arrTwigVar ['META_KEYWORDS'] = $class->loadSetting('metaKeywords');

$arrTwigVar ['CANONICAL'] = HTTP_HOST.ltrim($_SERVER['REQUEST_URI'], '/') ;
//$arrTwigVar ['CANONICAL'] = rtrim($class->loadSetting('sitesName'),'/') . rtrim(REQUEST_URI,'/');
  
$arrTwigVar['MAP_API_KEY'] = $class->loadSetting('mapAPIKey'); 


$RS_LOGIN_USER = array(); 
$userkey = 0;

// klao get dari IP takutnya lemot, jd tembak aj dulu
$LOCAL = array('timezone' => array('userGMT'=>7,'systemGMT'=>7)); 

// refcode
if(!empty($_GET['referral'])) $_SESSION['referralCode'] = trim($_GET['referral']);
	
if (isset($_SESSION) && !empty($_SESSION[$class->loginSession]['id'])){ 
	
	// kalo ad connection dari perusahaan user (TMS), ganti koneksi
	
	if (isset( $_SESSION[$class->loginSession]['customerCompany']['domain']) && 
		!empty( $_SESSION[$class->loginSession]['customerCompany']['domain'])){
		
		$domainName = $_SESSION[$class->loginSession]['customerCompany']['domain']; 
		$CUSTOMER_CONN = newConnection($domainName); 
		$security->oDbCon =  $CUSTOMER_CONN;
	}	
	
    $userkey = base64_decode($_SESSION[$class->loginSession]['id']);
    $arrTwigVar['loginName'] =  $_SESSION[$class->loginSession]['name'];
	$arrTwigVar['loginType'] =  $_SESSION[$class->loginSession]['logintype'];
	  
	// test, memastikan USERKEY tdk di tempered
	if (!$security->isMemberLogin(false)) { 
		header('location:/logout'); // gk bisa pake KICKED_REDIRECT_URL, karena blm kedefined
		die;
	}  
	
    $LOCAL['timezone']['userGMT'] = (isset($_SESSION[$class->loginSession]['gmt'])) ? $_SESSION[$class->loginSession]['gmt'] : 7;
}
 
if(!isset($_SESSION['itemsToCompare']))  $_SESSION['itemsToCompare'] = array();


define ('USERKEY', $userkey);
define ('LOCAL', $LOCAL);

// utk set local timezone, dsb
$arrTwigVar['LOCAL'] = LOCAL;

/* LANGUAGE */   
$rsLang = $lang->searchData($lang->tableName.'.statuskey',1,true,'','order by '.$lang->tableName.'.orderlist asc');
$arrTwigVar['rslang'] = $rsLang;


// load informasi login customer
// utk TMS harusnya di switch jg conn nya
 
if(!empty($userkey)){ 
    includeClass(array('Customer.class.php'));
    $customer = new Customer();
	
	// kalo ad koneksi customer company (TMS)
	if(isset($CUSTOMER_CONN) && !empty($CUSTOMER_CONN) ) 
		$customer->oDbCon =  $CUSTOMER_CONN;
	
    $rsCust = $customer->searchDataRow(array('langkey','ssotypekey'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString(USERKEY));  
    $rsLangTemp = array_column($rsLang,'code','pkey');
    
    $RS_LOGIN_USER['pkey'] = $userkey;
    $RS_LOGIN_USER['name'] = $_SESSION[$class->loginSession]['name'];
    $RS_LOGIN_USER['ssotypekey'] = $rsCust[0]['ssotypekey'];
    if(isset($rsLangTemp[$rsCust[0]['langkey']]))
        $RS_LOGIN_USER['lang'] = $rsLangTemp[$rsCust[0]['langkey']]; 
    
    // set lang
    if(!isset($_SESSION['langIsset']) && !empty($RS_LOGIN_USER['lang'])){
        $_SESSION['lang'] = $rsLangTemp[$rsCust[0]['langkey']]; 
        $class->setActiveLang();
        
        $_SESSION['langIsset'] = true;
    }
}

// kalo gk kosong dan gk pernah set session
if(!empty($rsLang) && empty($_SESSION['lang'])) {
    $_SESSION['lang'] = $rsLang[0]['code'];
    $class->setActiveLang();
}

$arrTwigVar ['LOGIN_USER'] = $RS_LOGIN_USER;
$arrTwigVar ['LANG'] = $class->lang;
$arrTwigVar ['ERRORMSG'] = $class->errorMsg;
$arrTwigVar ['activeLangIndex'] = $class->langCode;
 
/* settings */
$rsSetting =  $setting->getSettingData();

for ($i=0;$i<count($rsSetting);$i++){
	$code = $rsSetting[$i]['code'];
	 
	if ($rsSetting[$i]['multivalue'] == 0){ 
			if ($rsSetting[$i]['type'] == 3 )
				$arrTwigVar ['settings'][$code] =str_replace(chr(13),'<br>',$rsSetting[$i]['value']);
			else
				$arrTwigVar ['settings'][$code] = $rsSetting[$i]['value'] ;
             
	}else{ 
		$arrDetail = $setting->getDetailByCode($code);
		$arrTwigVar ['settings'][$code] = $arrDetail;
	} 
		 
}

$IGNOREQOH = ($class->loadSetting('ignoreQOH') == 1) ? true : false;
define ('IGNORE_QOH', $IGNOREQOH) ;
$arrTwigVar['ignoreQOH'] = IGNORE_QOH; 

/* KHUSUS WA */
$arrTwigVar['whatsapp'] = array();
foreach($arrTwigVar['settings']['companyMessenger'] as $row){
    if(in_array($row['label'], array('wa','whatsapp'))) 
        array_push($arrTwigVar['whatsapp'],$row);
}

/* ICON */
$favicon = (!empty($arrTwigVar ['settings']['webIcon'])) ? $class->defaultURLUploadPath.'setting/webIcon/'.$arrTwigVar ['settings']['webIcon'] : HTTP_HOST.'include/img/programstok.ico' ;
$arrTwigVar ['favicon'] = $favicon;

/* kalo under maintenance redirect ke under-maintenance */  

if ($arrTwigVar ['settings']['underMaintenance'] == 1)
   header('Location: /under-maintenance');
    
//$IS_ACTIVE_MODULE = $class->isActiveModule(array('banner', 'testimonial', 'item','service','course','article','youtube','page'));


/* ===================== BANNER ===================== */
if($IS_ACTIVE_MODULE['banner']){ 
	$rsBannerPosition = $banner->getAllPosition();
	for($i=0;$i<count($rsBannerPosition);$i++){
		$rsBanner = $banner->searchData($banner->tableNamePosition.'.name', $rsBannerPosition[$i]['name'],true,' and statuskey = 1',' order by orderlist asc'); 
 
        $rsBanner = $banner->updateContentLang($rsBanner);  
		$arrTwigVar['banner'][strtolower($rsBannerPosition[$i]['name'])] = $rsBanner; 
	}	
}

/* ===================== ITEM CATEGORY ========================================== */
if($IS_ACTIVE_MODULE['item']){
    
    $item = new Item();
    
	$arrTwigVar['compiledItemCategory'] = $itemCategory->compileChildArray(true);
  
	//$class->setLog($arrTwigVar['compiledItemCategory'],true);

	$rsItemCategory= $itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true,' and '.$itemCategory->tableName.'.isshow = 1 and '.$itemCategory->tableName.'.featured = 1',' order by '.$itemCategory->tableName.'.orderlist asc, '.$itemCategory->tableName.'.name asc'); 
 
	$arrTwigVar['itemCategory']  = $rsItemCategory;
	
	if ( isset($_GET) && !empty($_GET['categorykey']) ) $_POST['selQuickSearchCategory'] = $_GET['categorykey'];
	$arrTemp = array("0" => $class->lang['allCategories']);
	$arrCategory = $class->convertForCombobox($arrTwigVar['compiledItemCategory'][0]['childnode'], 'pkey','name');  
	$arrCategory = $arrTemp + $arrCategory;
	$arrTwigVar['selCategoryQuickSearch'] =  $class->inputSelect('selQuickSearchCategory',  $arrCategory  );

    
    $rsFeaturedItem = $item->searchData($item->tableName.'.statuskey',1,true, ' and '.$item->tableName.'.publish = 1');
	foreach($rsFeaturedItem as $key=>$itemRow){ 
		$rsItemImage = $item->getItemImage($itemRow['pkey']);
		$rsFeaturedItem[$key]['mainimage'] = $rsItemImage[0]['file'];	
		$rsFeaturedItem[$key]['description'] = $item->getItemDescription($itemRow['pkey']);	 
		//$rsFeaturedItem[$key]['promo'] = $voucher->checkHasPromo($itemRow); 

	} 

	$arrTwigVar['rsFeaturedItem'] = $rsFeaturedItem;
    
}

if($IS_ACTIVE_MODULE['service']){

	/* ===================== SERVICES ========================================== */
	$rsService = $service->searchData($service->tableName.'.statuskey',1,true, '','order by '.$service->tableName.'.orderlist asc , '.$service->tableName.'.name asc');
	for($j=0;$j<count($rsService);$j++){  

		$rsImage = $service->getItemImage($rsService[$j]['pkey']);

		if(!empty($rsImage)){ 
			$rsService[$j]['mainimage'] = $rsImage[0]['file'];	 
		}
 

	}

	$arrTwigVar['services'] = $rsService;

	/* ===================== SERVICE CATEGORY ========================================== */
	$arrTwigVar['compiledServiceCategory'] = $serviceCategory->compileChildArray(true);  
 
}


/* ===================== ARTICLE ===================== */ 

if($IS_ACTIVE_MODULE['news']){ 
	includeClass(array('News.class.php'));
	$news = new News();
	$newsCategory = new NewsCategory();
    
	$rsLatestNews = $news->searchData($news->tableName.'.statuskey',1,true, '  and publishdate <= now()',' order by '.$news->tableName.'.publishdate desc', ' limit ' . $class->loadSetting('latestNews'));
	foreach($rsLatestNews as $key=>$newsRow){  
      $rsLatestNews[$key]['publishdate'] = $class->convertToLocalTimeZone($rsLatestNews[$key]['publishdate'],LOCAL['timezone']['systemGMT'], LOCAL['timezone']['userGMT'] );
	} 
     
    $arrTwigVar ['rsLatestNews'] =  $news->updateContentLang($rsLatestNews);  
    
    // sementara hanya ambil yg level pertama 
    $rsNewsCategory = $newsCategory->searchDataRow(array($newsCategory->tableName.'.pkey',
                                             $newsCategory->tableName.'.name',
                                             $newsCategory->tableName.'.parentkey',
                                             $newsCategory->tableName.'.statuskey'),
                                        ' and '.$newsCategory->tableName.'.parentkey=0 and '.$newsCategory->tableName.'.statuskey = 1',
                                       'order by orderlist asc'
                                      );

    $arrTwigVar ['rsNewsCategory'] = $rsNewsCategory;

}

if($IS_ACTIVE_MODULE['investornews']){ 
	includeClass(array('InvestorNews.class.php'));
	$investorNews = new InvestorNews(); 
    
	$rsLatestNews = $investorNews->searchData($investorNews->tableName.'.statuskey',1,true, '  and publishdate <= now()',' order by '.$investorNews->tableName.'.publishdate desc', ' limit ' . $class->loadSetting('latestNews'));
	foreach($rsLatestNews as $key=>$newsRow){  
      $rsLatestNews[$key]['publishdate'] = $class->convertToLocalTimeZone($rsLatestNews[$key]['publishdate'],LOCAL['timezone']['systemGMT'], LOCAL['timezone']['userGMT'] );
	} 
     
    $arrTwigVar ['rsLatestInvestorNews'] =  $news->updateContentLang($rsLatestNews);  
     

}


if($IS_ACTIVE_MODULE['csrcategory']){ 
	includeClass(array('CSRCategory.class.php'));
	$CSRCategory = new CSRCategory(); 
    
	$rsCSRCategory =  $CSRCategory->searchDataRow(array($CSRCategory->tableName.'.pkey',
                                             $CSRCategory->tableName.'.name',
                                             $CSRCategory->tableName.'.parentkey',
                                             $CSRCategory->tableName.'.statuskey'),
                                        ' and '.$CSRCategory->tableName.'.parentkey=0 and '.$CSRCategory->tableName.'.statuskey = 1',
                                       'order by orderlist asc'
                                      );
    
    $arrTwigVar ['rsCSRCategory'] =  $CSRCategory->updateContentLang($rsCSRCategory);  

}


if($IS_ACTIVE_MODULE['goodcorporategovernmentcategory']){ 
	includeClass(array('GoodCorporateGovernmentCategory.class.php'));
	$goodCorporateGovernmentCategory = new GoodCorporateGovernmentCategory(); 
    
	$rsGCGCategory =  $goodCorporateGovernmentCategory->searchDataRow(array($goodCorporateGovernmentCategory->tableName.'.pkey',
                                             $goodCorporateGovernmentCategory->tableName.'.name',
                                             $goodCorporateGovernmentCategory->tableName.'.parentkey',
                                             $goodCorporateGovernmentCategory->tableName.'.statuskey'),
                                        ' and '.$goodCorporateGovernmentCategory->tableName.'.parentkey=0 and '.$goodCorporateGovernmentCategory->tableName.'.statuskey = 1',
                                       'order by orderlist asc'
                                      );
    
    $arrTwigVar ['rsGCGCategory'] =  $goodCorporateGovernmentCategory->updateContentLang($rsGCGCategory);  

}


//if($IS_ACTIVE_MODULE['investorreport']){  
//	includeClass(array('Category.class.php','InvestorReportCategory.class.php')); 
//	$investorReportCategory = new InvestorReportCategory();
//     
//    // sementara hanya ambil yg level pertama 
//    $rsInvestorReportCategory = $investorReportCategory->searchDataRow(array($investorReportCategory->tableName.'.pkey',
//                                             $investorReportCategory->tableName.'.name',
//                                             $investorReportCategory->tableName.'.parentkey',
//                                             $investorReportCategory->tableName.'.statuskey'),
//                                        ' and '.$investorReportCategory->tableName.'.parentkey=0 and '.$investorReportCategory->tableName.'.statuskey = 1',
//                                       'order by orderlist asc'
//                                      );
//
//    $arrTwigVar ['rsInvestorReportCategory'] = $rsInvestorReportCategory;
//
//}



if($IS_ACTIVE_MODULE['article']){ 
	$rsRandArticle = $article->getRandomData(5);
	$arrTwigVar['rsRandArticle'] =  $article->updateContentLang($rsRandArticle);  
}
 
/* ===================== YOUTUBE ===================== */ 
if($IS_ACTIVE_MODULE['youtube']){
$rsRandYoutube = $youtube->getRandomData(5);
$arrTwigVar['rsRandYoutube'] = $rsRandYoutube; 
}
 
 
/* ===================== PAGE CONTENT ===================== */ 

if($IS_ACTIVE_MODULE['page']){ 
	$arrFreePage = array();
	$rsFreePage = $page->searchData($page->tableName.'.statuskey',1); 
	for($i=0;$i<count($rsFreePage);$i++){ 
		$nameindex = $rsFreePage[$i]['pagename'];
		$arrFreePage[$nameindex]['pkey'] = $rsFreePage[$i]['pkey'];
		$arrFreePage[$nameindex]['name'] = $nameindex;
		$arrFreePage[$nameindex]['title'] = $rsFreePage[$i]['title'];
		$arrFreePage[$nameindex]['shortdesc'] = $rsFreePage[$i]['shortdesc'];
		$arrFreePage[$nameindex]['detail'] = $rsFreePage[$i]['detail'];
		$arrFreePage[$nameindex]['file'] = $rsFreePage[$i]['file'];  
	}
	$arrTwigVar['page'] = $page->updateContentLang($arrFreePage); 
	
}

if($IS_ACTIVE_MODULE['partners']){
	includeClass(array('Partners.class.php')); 
	$partners = new Partners();
	$rsPartners = $partners->searchData($partners->tableName.'.statuskey',1,true,' and  '.$partners->tableName.'.isfeatured = 1', ' order by ' .$partners->tableName.'.orderlist asc');

//    foreach($rsPartners as $key=>$row){  
//        // get file size
//        $filefullpath =  $partners->defaultDocUploadPath.$partners->uploadFolder.'/'.$row['pkey'].'/'.$row['file'];
//        $arrSize = getimagesize($filefullpath);
//        $arrResize = $partners->resizeRatio($arrSize[0],$arrSize[1],array('type'=>1, 'size' => array('w' => 150,'h' => 40)));
//        
//        $rsPartners[$key]['width'] = $arrResize['w'].'px';
//        $rsPartners[$key]['height'] = $arrResize['h'].'px';
//    }
    
	$arrTwigVar['featuredPartners'] = $rsPartners;  
}

$arrTwigVar ['btnAddToCrt'] =   $class->inputSubmit('btnAddToCart', $class->lang['addToCart'], array('etc' => ' style="width:100%;"')); 
 
// nanti perlu dipindahan agar lebih fleksible
$arrTwigVar['btnGoogle'] = '
<div class="google">
<div class="button_container" width="15%">
    <div id="g_id_onload"
        data-client_id="'.$class->loadSetting('googleOAuthId').'"
        data-auto_prompt="false"
        data-nonce="'.$class->generateStrongPassword().'"
        data-login_uri="'.HTTP_HOST.'api/sso/google-login"
        data-ux_mode="redirect"
        data-auto_prompt="false">
    </div>
    <div class="g_id_signin"
        data-type="standard"
        data-size="medium"
        data-width="274"
        data-theme="filled_blue"
        data-text="continue_with"
        data-shape="rectangular"
        data-logo_alignment="left"
        data-locale="id-ID">
    </div>
</div>
</div>';


$kickedRedirectURL = $class->loadSetting('kickedRedirectURL');
if(empty($kickedRedirectURL)) $kickedRedirectURL = '/logout';
define('KICKED_REDIRECT_URL',$kickedRedirectURL);   
 
/* QUICK SEARCH */
if ( isset($_GET) && !empty($_GET['key']) )
$_POST['quickSearch'] = $_GET['key'];

$arrTwigVar['inputQuickSearch'] = $class->inputText('quickSearch', array( 'etc' => 'placeholder="'.$class->lang['searchProduct'].'..."')); 
  

/* ======== INPUT QUICK LOGIN ========== */ 
/*
// dipindah ke file popup login
$arrTwigVar['inputPopupUsername'] = $class->inputText('username'); 
$arrTwigVar['inputPopupPassword'] = $class->inputText('password'); 

$arrTwigVar['inputPopupUsernamePlaceholder'] = $class->inputText('username', array( 'etc' => 'placeholder="'.$class->lang['username'].'"')); 
$arrTwigVar['inputPopupPasswordPlaceholder'] = $class->inputText('password', array( 'etc' => 'placeholder="'.$class->lang['password'].'"')); 

$arrTwigVar['btnSubmitPopupLogin'] =   $class->inputSubmit('btnSave',$class->lang['login']); 
*/

/* ======== FOOTER CONTACT ========== */ 
$arrTwigVar['inputHidQuickContact'] =   $class->inputHidden('hidQuickContact', array('value' => 1));
$arrTwigVar['inputQuickContactFrom'] = $class->inputText('quickContactFrom', array( 'etc' => 'placeholder="'.$class->lang['name'].'"')); 
$arrTwigVar['inputQuickContactPhone'] = $class->inputText('quickContactPhone', array( 'etc' => 'placeholder="'.$class->lang['phone'].'"')); 
$arrTwigVar['inputQuickContactEmail'] = $class->inputText('quickContactEmail', array( 'etc' => 'placeholder="'.$class->lang['email'].'"')); 
$arrTwigVar['inputQuickContactMessage'] = $class->inputTextArea('quickContactMessage', array( 'etc' => 'placeholder="'.$class->lang['message'].'" class="btn btn-primary" style="height:8em"')); 
$arrTwigVar['inputQuickContactSubmit'] = $class->inputSubmit('btnQuickContactSubmit', $class->lang['send']); 

// buat form contact
$arrTwigVar ['inputContactNamePlaceholder'] =  $class->inputText('name',array('etc' => 'placeholder="'.$class->lang['name'].'"')); 
$arrTwigVar ['inputContactPhonePlaceholder'] =  $class->inputText('phone',array('etc' => 'placeholder="'.$class->lang['phone'].'"')); 
$arrTwigVar ['inputContactEmailPlaceholder'] =  $class->inputText('email',array('etc' => 'placeholder="'.$class->lang['email'].'"')); 
$arrTwigVar ['inputContactSubjectPlaceholder'] =  $class->inputText('subject',array('etc' => 'placeholder="'.$class->lang['subject'].'"')); 
$arrTwigVar ['inputContactMessagePlaceholder'] =   $class->inputTextArea('message', array('etc' => 'placeholder="'.$class->lang['message'].'" style="height:10em"'));

// khusus utk TMS 
$arrTwigVar['portalMenu'] = (isset($_SESSION[$class->loginSession]['logintype'])) ? $customer->getPortalMenu( $_SESSION[$class->loginSession]['logintype'] ) : array();

// newsletter
$arrTwigVar['newsletterLoaded'] = (isset($_SESSION['newsletterLoaded']) && !empty($_SESSION['newsletterLoaded'])) ? $_SESSION['newsletterLoaded'] : false;
$arrTwigVar['inputSubscribeNewsletter'] =   $class->inputText('email');
$arrTwigVar['inputSubscribeNewsletterPlaceholder'] =   $class->inputText('email', array( 'etc' => 'placeholder="'.$class->lang['enterYourEmail'].'"')); 
$arrTwigVar['btnSubmitSubscribeNewsletter'] =   $class->inputButton('btnSave', $class->lang['submit']);
$arrTwigVar['inputHidActionNewsletter'] =  $class->inputHidden('action',array('value' => 'add'));


?>