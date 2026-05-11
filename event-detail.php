<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

includeClass(array('Event.class.php'));
$obj = new Event();

if(empty($_GET)){
	header("location: /");
	die;
}
 
$id = $_GET['id']; 

$rsEvent = $obj->getDataRowById($id, ' and statuskey = 1');

if(empty($rsEvent)){
	header("location: /");
	die;
}

$rsItemImage = $obj->getItemImages($id);

// other event ==============

$totalrowsperpage = $class->loadSetting('newsTotalRowsPerPage'); 
if($totalrowsperpage == '') $totalrowsperpage = 5;

$rsOtherEvents = $obj->searchDataRow(array($obj->tableName.'.pkey',$obj->tableName.'.title'),
                                  ' and '.$obj->tableName.'.statuskey = 1 
                                    and '.$obj->tableName.'.categorykey = '. $obj->oDbCon->paramString($rsEvent[0]['categorykey']) .' 
                                    and  '.$obj->tableName.'.pkey <> '. $obj->oDbCon->paramString($id) .'
                                 ','order by pkey desc limit ' . $totalrowsperpage);
   
$rsItemImages = $obj->getItemImages(array_column($rsOtherEvents,'pkey')); 
$rsItemImages = $obj->reindexDetailCollections($rsItemImages,'refkey');

for($i=0;$i<count($rsOtherEvents);$i++){
    $arrItemImage = $rsItemImages[$rsOtherEvents[$i]['pkey']][0];
        
    $rsOtherEvents[$i]['image'] = $arrItemImage['file']; 
}

$arrTwigVar ['rsEvent'] = $obj->updateContentLang($rsEvent); 
$arrTwigVar ['rsItemImage'] = $rsItemImage; 
$arrTwigVar ['rsOtherEvents'] = $obj->updateContentLang($rsOtherEvents); 
$arrTwigVar ['ACTIVE_MENU'] = array('/events'); 

echo $twig->render('event-detail.html', $arrTwigVar);
?>