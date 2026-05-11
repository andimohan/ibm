<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  ; 
 
if(empty($_GET)){
	header("location: /");
	die;
} 
$id = $_GET['id'];

$rsPOItem = $preorderItem->searchData($preorderItem->tableName.'.pkey',$id,true, ' and '.$item->tableName.'.statuskey = 1'); 
if(empty($rsPOItem)){
	header("location: /");
	die;
}
 
$rsImage = $item->getItemImage($rsPOItem[0]['itemkey']);  
$rsDescription = $item->getItemDescription($rsPOItem[0]['itemkey']);   


$_POST['POHiditemkey'] = $rsPOItem[0]['itemkey'];
$_POST['POHidkey'] = $id;

$_POST['action'] ='POaddtocart';
$_POST['POOrderQty'] = '1';
$arrTwigVar ['inputHidPOkey'] =  $class->input('hidden','POHiditemkey');
$arrTwigVar ['inputHidkey'] =  $class->input('hidden','POHidkey');
$arrTwigVar ['inputHidAction'] =  $class->input('hidden','action');
$arrTwigVar ['inputQty'] = $class->input('number','POOrderQty',true,'','','form-control'); 
$arrTwigVar ['btnSubmit'] =   $class->input('submit','btnSave',false,$class->lang['addToCart'], ' style="width:100%;"');  
  
   
$arrTwigVar['rsItem'] =  $rsPOItem;     
$arrTwigVar['rsItemImage'] =  $rsImage;  
$arrTwigVar['rsItemDescription'] =  $rsDescription;   
$arrTwigVar['ACTIVE_MENU'] = '/products-preorder.php';  

if (empty($rsPOItem[0]['name']))
	$title = $rsPOItem[0]['code'];
else
	$title = $rsPOItem[0]['name'];
 
$arrTwigVar ['META_TITLE'] = $title;
$arrTwigVar ['META_DESCRIPTION'] ='';
$arrTwigVar ['META_IMAGE'] = $class->defaultURLUploadPath . 'item/'.$rsPOItem[0]['pkey'].'/'.$rsImage[0]['file'];
 
echo $twig->render('products-preorder-detail.html', $arrTwigVar);
?>
