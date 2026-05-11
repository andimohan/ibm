<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  

$pageIndex = 0;
if ( isset($_GET) && !empty($_GET['page']) ){
	$pageIndex = $_GET['page'];
} 
    
$arrTwigVar ['pageIndex'] =  $pageIndex;
 
$totalrowsperpage = $class->loadSetting('productTotalItemPerPage');
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
   
$criteria = '';  
$criteria .= ' and ' .$preorderItem->tableName.'.statuskey = 2 ';
  
$rsItem = $preorderItem->searchData('','',true,$criteria,'order by '.$preorderItem->tableName.'.closingdate desc',$limit);
$totalPages = ceil( $preorderItem->getTotalRows($criteria) / $totalrowsperpage); 
  
for($i=0;$i<count($rsItem);$i++){
		$rsItemImage = $item->getItemImage($rsItem[$i]['itemkey']); 
		$rsItem[$i]['mainimage'] = $rsItemImage[0]['file']; 
}
 
$arrTwigVar ['rsItem'] =  $rsItem;
$arrTwigVar ['totalPages'] =  $totalPages;  
   
$arrTwigVar ['hidLayout'] =  $class->input('hidden','hidLayout');


if (isset($_POST) && !empty($_POST['hidLayout'])) 
	 $_SESSION['layout'] = $_POST['hidLayout']; 

if (!empty($_SESSION['layout']))
	$arrTwigVar ['layout'] = $_SESSION['layout'];

echo $twig->render('products-preorder.html', $arrTwigVar);
?>
