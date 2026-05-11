<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  
   
includeClass(array("Service.class.php","Category.class.php","ServiceCategory.class.php"));
$serviceCategory = new ServiceCategory();
$service = new Service(SERVICE);
  
if(empty($_GET)){
	header("location: /");
	die;
} 

$id = $_GET['id']; 
$rsService = $service->searchData($service->tableName.'.pkey',$id,true, ' and '.$service->tableName.'.statuskey = 1'); 
if(empty($rsService)){
	header("location: /");
	die;
}

$rsItemImage = $service->getItemImage($rsService[0]['pkey']);  
if(!empty($rsItemImage))
	$rsItemImage[0]['mainimage'] = $rsItemImage[0]['file'];


$rsCat = $serviceCategory->getDataRowById($rsService[0]['categorykey']);

$arrParentPath[0]['pkey'] = $rsCat[0]['pkey'];
$arrParentPath[0]['name'] = $rsCat[0]['name']; 
$parentkey = $rsCat[0]['parentkey'];
 

$title = (!empty($rsService[0]['metatitle'])) ? $rsService[0]['metatitle'] : $rsService[0]['name'];  
$descForMeta = (!empty($rsService[0]['metadescription'])) ? $rsService[0]['metadescription'] : $rsService[0]['shortdescription']; 

$arrTwigVar ['META_TITLE'] = $title;
$arrTwigVar ['META_DESCRIPTION'] = $descForMeta;

if (!empty($rsService[0]['tag']))
    $rsService[0]['tag'] .=  ', '. $rsService[0]['tag'];

$arrTwigVar ['META_KEYWORDS'] = $title . ', '. $rsService[0]['tag'];
//$arrTwigVar ['META_IMAGE'] = $class->defaultURLUploadPath . 'service/'.$rsService[0]['pkey'].'/'.$rsImage[0]['file']; 
 
$arrTwigVar['categoryPath'] = $arrParentPath;   

/*$structureData =' 
<script type="application/ld+json">
{
    "@context": "http://schema.org/",
    "@type": "Product",
    "name": "'.$rsService[0]['name'].'",
    "image" : '. $structureData .='"'.HTTP_HOST.'phpthumb/phpThumb.php?src='. $class->phpThumbURLSrc.'item/'.$rsService[0]['pkey'].'/'.$rsImage[0]['file'].'&hash='.$rsImage[0]['phpThumbHash'].'",'; 
 
 

$structureData .='
    "offers": {
        "@type": "Offer",
        "priceCurrency": "IDR",
        "price": "'.$rsService[0]['sellingprice'].'",  
        "availability": "http://schema.org/InStock"
    }  
}
</script>
';*/


/* ===================== OTHERS SERVICES ========================================== */  
$rsOtherServices = $service->searchData($service->tableName.'.statuskey',1,true, ' and '.$service->tableName.'.pkey <> '.  $service->oDbCon->paramString($rsService[0]['pkey']) ,'' , ' limit 10 ');
foreach($rsOtherServices as $key=>$row){  
    $rsItemImage = $service->getItemImage($row['pkey']); 

    if(!empty($rsItemImage)){ 
        $rsOtherServices[$key]['mainimage'] = $rsItemImage[0]['file']; 
    }
	
} 
$arrTwigVar['rsOtherServices'] =  $rsOtherServices;  

     
$_POST['hidCategoryKey'] = 2; // tembak mati, 2 utk inquiry product
$arrTwigVar ['inputHidCategoryKey'] =  $class->inputHidden('hidCategoryKey'); 
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone'); 
$arrTwigVar ['inputEmail'] =  $class->inputText('email'); 
$arrTwigVar ['inputSubject'] =  $class->inputText('subject'); 
$arrTwigVar ['inputMessage'] =   $class->inputTextArea('message', array('etc' => 'style="height:10em"')); 
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['send']); 
$arrTwigVar['rsService'] =   $service->updateContentLang($rsService);      
//$arrTwigVar ['STRUCTURE_DATA'] = $structureData;   
 
echo $twig->render('service-detail.html', $arrTwigVar);
?>