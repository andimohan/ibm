<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$class->oDbCon->startTrans(); 
 
$arrBrand = array(); // all brands
$arrCategory = array();

$order = ' order by brandname asc, categoryname asc';
$criteria = '';
$groupCriteria = '';// 'having qtyonhand > 5';

if(!empty($arrCategory))
    $criteria .= ' and '.$item->tableName.'.categorykey in ('.$class->oDbCon->paramString($arrCategory,',').')';
     
if(!empty($arrBrand))
    $criteria .= ' and '.$item->tableName.'.brandkey in ('.$class->oDbCon->paramString($arrBrand,',').')';
    
$rsItem =  $item->searchData('','',true,$criteria,$order,'',$groupCriteria ); 

$path = '/Users/mhk/public_html/export-image/';

foreach($rsItem as $itemRow){
    $itemkey = $itemRow['pkey'];
    $itemname = $itemRow['name'];
    
    echo $itemname.'<br>';
    
    $rsImage = $item->getItemImage($itemkey);
    $imageName = $rsImage[0]['file'];
     
    
    $source = $item->defaultDocUploadPath.'item/'.$itemkey.'/'.$imageName;
    
    $destFileName = explode('.',$imageName);
    $destFileName = $itemname.'.'.$destFileName[count($destFileName)-1];
     
    $folder = $itemRow['brandname'] .'/'. $itemRow['categoryname'].'/';
    
    if(!is_dir($path.$folder))
         mkdir($path.$folder, 0755, true);
    
    $destination = $path.$folder.$destFileName;
    
    echo $source.'<br>';
    echo $destination.'<br>';
    
    
    if (!copy($source, $destination)) 
        echo '<div style="color:#F00">failed to copy '.$source.'</div>';
    
    
}

$class->oDbCon->endTrans();
 
?>