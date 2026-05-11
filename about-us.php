<?php   
// khusus icomunity
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

includeClass(array('Customer.class.php'));
$customer = new Customer();
 
$criteria = ' and '.$customer->tableName.'.statuskey = 2';
$criteria .= ' and '.$customer->tableName.'.hostlevelkey > 0 and '.$customer->tableName.'.membershiplevel > 1';
$criteria .=  ' and  '.$customer->tableName.'.pkey not in (8014 ,8167)';

$rsHost = $customer->searchData('','',true,$criteria,' order by '.$customer->tableName.'.modifiedon desc, '.$customer->tableName.'.createdon desc',$limit );

$arrHostLevel = array('1' => 'Host', '2' =>'Master Host');

// group by host level key 
$rsHost = $customer->reindexDetailCollections($rsHost,'hostlevelkey');


if($IS_ACTIVE_MODULE['corporatevalues']){
    
	includeClass(array('CorporateValues.class.php')); 
    $corporateValues = new CorporateValues();
    
    $rsCorporateValues = $corporateValues->searchData($corporateValues->tableName.'.statuskey',1,true, '', 'order by orderlist asc'); 
  
    $arrTwigVar['rsCorporateValues'] = $corporateValues->updateContentLang($rsCorporateValues) ;   
        
}

$arrTwigVar ['arrHostLevel'] =  $arrHostLevel;
$arrTwigVar ['rsHost'] =  $rsHost;
$arrTwigVar ['PAGE_NAME'] =  $customer->lang['aboutUs'];

echo $twig->render('about-us.html', $arrTwigVar);

?>