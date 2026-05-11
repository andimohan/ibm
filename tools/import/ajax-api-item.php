<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Brand.class.php';      

$item = new Item();  
$itemUnit =  createObjAndAddToCol(new ItemUnit());
$brand =  createObjAndAddToCol(new Brand());

$url = API_URL.'items';

//$item->setLog($_POST,true);
// convert jenis token
foreach($_POST['data'] as $key=>$postRow){ 
    if($key == 'image_url'){  
        if(!empty($postRow['value'])){
            $tempArr = explode(',',$postRow['value']);

            $newImgSet = array();
            foreach($tempArr as $row)
                array_push($newImgSet, array('url' => trim($row)));

            $_POST['data'][$key]['value'] = $newImgSet;
        }
    }
}


//$item->setLog("========",true);
//$item->setLog($_POST[$key]['code'],true);
//$item->setLog($_POST['data'],true);
//$item->setLog("xxxxxxxx",true);

echo $item->executeImportAPI($url,$_POST['data'], 'code');

?>