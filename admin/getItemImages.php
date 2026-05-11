<?php 
require_once '../_config.php'; 
require_once '../_include.php'; 

$rsItemImage = array();
$obj = $item;

if (isset($_POST) && !empty($_POST['itemkey'])){
     $id = $_POST['itemkey'];
	 $rsItemImage = $obj->getItemImage($id); 
    
     for ($i=0;$i<count($rsItemImage); $i++){
         $rsItemImage[$i]['phpthumburl'] = '/phpthumb/phpThumb.php?src='.$obj->phpThumbURLSrc .$obj->uploadFolder.$id.'/'.$rsItemImage[$i]['file'].'&w=200&h=160&far=C&hash='.getPHPThumbHash($rsItemImage[$i]['file']);
     }
}
  
echo json_encode($rsItemImage); 
die;
?>