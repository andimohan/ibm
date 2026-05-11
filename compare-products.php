<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  
    
$criteria = '';

$rsItem = array();
$rsItemComparison = array();

if(!empty($_SESSION['itemsToCompare']))
   $rsItem = $item->searchData($item->tableName.'.statuskey',1,true, ' and '. $item->tableName.'.pkey in ('.$class->oDbCon->paramString($_SESSION['itemsToCompare'],',').')');

 foreach($rsItem as $row){
    $itemkey = $row['pkey'];
    $rsSpec = $item->getItemSpecification($itemkey);
     
 
    foreach($rsSpec as $spec){ 
    
        $specName = $spec['name'];
        
        if(!isset($rsItemComparison[$specName]))
            $rsItemComparison[$specName] = array();

        $rsItemComparison[$specName][$itemkey] = $spec['value']; 
        
    }
     
 }

$arrTwigVar ['rsItem'] = $rsItem;
$arrTwigVar ['rsItemComparison'] = $rsItemComparison;

echo $twig->render('compare-products.html', $arrTwigVar);
?>
