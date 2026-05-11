<?php 
require_once '../../_config.php'; 
require_once "../../_include.php";  

set_time_limit(1800); // 30 mins
  
$sql = 'select * from item_image';
$rsImage = $class->oDbCon->doQuery($sql);

$destinationPath = $class->defaultDocUploadPath.$class->uploadFolder; 
$class->oDbCon->startTrans();

foreach($rsImage as $row){
    
    $filefullpath = $destinationPath.'item/'.$row['refkey'].'/'.$row['file']; 
    if(!is_file($filefullpath)) continue;
    
    $arrSizeInformation = getimagesize($filefullpath);
    $width = $arrSizeInformation[0];
    $height = $arrSizeInformation[1]; 
    $size = filesize($filefullpath);

    $sql = 'update item_image set width= '.$width.' ,height= '.$height.', size= '.$size.' where pkey = '.$row['pkey'];
    $class->oDbCon->execute($sql);
}
$class->oDbCon->endTrans();
 echo 'done';
?>