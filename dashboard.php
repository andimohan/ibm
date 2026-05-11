<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_include-portal.php';


includeClass(array("CustomerNews.class.php"));
$customerNews = new CustomerNews();

$customerNews->oDbCon = $CUSTOMER_CONN;


// customer news

$rsNews = $customerNews->searchData($customerNews->tableName.'.statuskey',1,true,' and '.$customerNews->tableName.'.publishdate <= now()',' order by '.$customerNews->tableName.'.publishdate desc limit 5');
$arrTwigVar['rsCustomerNews'] = $rsNews;
	
echo $twig->render('dashboard.html', $arrTwigVar);

?>