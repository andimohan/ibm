<?php 

include_once '../../_config.php'; 
include_once '../../_include-v2.php';

includeClass(array('Item.class.php')); 
$item = new Item();

try {

$class->oDbCon->startTrans();

$rs = $item->searchData('','',true);

if(empty($rs)) die;

for($i=0; $i<count($rs); $i++) {

    $pkey = $rs[$i]['pkey'];

    $arrParam = array();

    $rsColor = $item->getItemColorDetail($pkey);
    $rsModel = $item->getItemModelDetail($pkey);
    $rsTexture = $item->getItemTextureDetail($pkey);
    $rsCharacter = $item->getItemCharacterDetail($pkey);
    
    $arrColorKey = array_column($rsColor,'colorkey');
    $arrModelKey = array_column($rsModel, 'modelkey');
    $arrTextureKey = array_column($rsTexture, 'texturekey');
    $arrCharacter = array_column($rsCharacter, 'characterkey');

    $arrParam['hidCategoryKey'] = $rs[$i]['categorykey'];
    $arrParam['hidColorKey'] = $arrColorKey;
    $arrParam['hidModelKey'] = $arrModelKey;
    $arrParam['hidTextureKey'] = $arrTextureKey;
    $arrParam['hidPlatingKey'] = $rs[$i]['platingkey'];
    $arrParam['hidMaterialKey'] = $rs[$i]['materialkey'];
    $arrParam['hidRingSizeKey'] = $rs[$i]['ringsizekey'];
    $arrParam['hidCharacterKey'] = $arrCharacter;

    $arrParm['carat'] = $class->formatNumber($rs[$i]['carat'],2);
    $arrParam['gramasi'] = $class->formatNumber($rs[$i]['gramasi'],2);
    $arrParam['size'] = $class->formatNumber($rs[$i]['size']);

    $code = $item->generateCodeFromCategory($arrParam);
    
    $sql = '
        UPDATE
            '.$item->tableName.'
        SET
            code = '.$class->oDbCon->paramString($code).'
        WHERE
            pkey = '.$class->oDbCon->paramString($pkey).'
    ';

    echo $sql.'<br>';
    $class->oDbCon->execute($sql);

}

$class->oDbCon->endTrans();

} catch(Exception $e){
	$this->oDbCon->rollback();
}

echo 'done';

?>