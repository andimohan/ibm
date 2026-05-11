<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('FAQ.class.php'));  
$faq = new FAQ();

if(empty($_GET)){
	header("location: /");
	die;
}
 
$id = $_GET['id'];

$rsFAQ = $faq->getDataRowById($id, ' and statuskey = 1'); 
if(empty($rsFAQ)){
	header("location: /faq");
	die;
}

$rsFAQ[0]['publishdate'] = $class->convertToLocalTimeZone($rsFAQ[0]['createdon'],LOCAL['timezone']['systemGMT'], LOCAL['timezone']['userGMT'] ); 
$rsFAQ[0]['publishDateISO8601'] =  date('c',strtotime($rsFAQ[0]['createdon']));
$rsFAQ[0]['modifiedDateISO8601'] =  date('c',strtotime($rsFAQ[0]['modifiedon']));
$rsFAQ[0]['linktitle'] =  str_replace($class->arrSearch,$class->arrReplace,$rsFAQ[0]['question']); 

$arrTwigVar ['rsFAQ'] =  $article->updateContentLang($rsFAQ);
  
//$arrTwigVar ['META_TITLE'] = $rsFAQ[0]['question'] ;
//$arrTwigVar ['META_DESCRIPTION'] =  $rsFAQ[0]['answer'] ;
//$arrTwigVar ['META_KEYWORDS'] = $rsFAQ[0]['answer'];  

array_push($arrActive,'/faq.php');
array_push($arrActive,'/faq.php?'.$id);

$arrTwigVar ['ACTIVE_MENU'] =  $arrActive;  
//
//$companyName = $class->loadSetting('companyName');
//$companyLogo = $class->loadSetting('companyLogo');
//$structureData =' 
//<script type="application/ld+json">
//{
//  "@context": "http://schema.org",
//  "@type": "NewsArticle",
//  "mainEntityOfPage": {
//    "@type": "WebPage",
//    "@id": "'.HTTP_HOST.'",
//    "URL" : "'.rtrim(HTTP_HOST,'/'). REQUEST_URI .$rsFAQ[0]['linktitle'].'"
//  },
//  "headline": "'.$rsFAQ[0]['title'].'",
//  "image": [
//    "'.HTTP_HOST.'phpthumb/phpThumb.php?src='. $class->phpThumbURLSrc.'article/'.$rsFAQ[0]['pkey'].'/'.$rsFAQ[0]['image'].'&hash='.$rsFAQ[0]['phpThumbHash'].'" 
//   ],
//  "datePublished": "'.$rsFAQ[0]['publishDateISO8601'].'",
//  "dateModified":  "'.$rsFAQ[0]['publishDateISO8601'].'",
//  "author": {
//    "@type": "Person",
//    "name": "'.$companyName.'"
//  },
//   "publisher": {
//    "@type": "Organization",
//    "name": "'.$companyName.'",
//    "logo": {
//      "@type": "ImageObject",
//      "url": "'.HTTP_HOST.'phpthumb/phpThumb.php?src='. $class->phpThumbURLSrc.'settings/companyLogo/'.$companyLogo.'&hash='.getPHPThumbHash($companyLogo).'"
//    }
//  },
//  "description": "'.$rsFAQ[0]['shortdesc'].'"
//}
//</script>
//';
//    
//$arrTwigVar ['STRUCTURE_DATA'] = $structureData;

echo $twig->render('faq-detail.html', $arrTwigVar);
?>