<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false)){
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

includeClass(array("MedicalSalesInvoice.class.php"));
$medicalSalesInvoice = new MedicalSalesInvoice();


$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
$arrTwigVar['pageIndex'] =  $pageIndex;

$totalrowsperpage = $class->loadSetting('productTotalItemPerPage'); //sementara pakai ini dulu

$orderBy = ' order by '.$medicalSalesInvoice->tableName.'.trdate desc, '.$medicalSalesInvoice->tableName.'.pkey desc'; 
$now = $pageIndex * $totalrowsperpage;

$arrStatus = array(1);
if (isset($_GET['status']) && is_numeric($_GET['status']))
	$arrStatus = array($_GET['status']);
	
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;
$criteria =   ' and '.$medicalSalesInvoice->tableName.'.customerkey = '.$class->oDbCon->paramString(USERKEY).'              
				and '.$medicalSalesInvoice->tableARStatus.'.pkey = '.$class->oDbCon->paramString($arrStatus,',').'
                and '.$medicalSalesInvoice->tableName.'.statuskey in (2,3)';

$rs = $medicalSalesInvoice->searchData('','',true,$criteria,$orderBy,$limit);

$arrStatusMenu = array( 1 => array( 'pkey' => AP_STATUS['open'], 'name' => $class->lang['open']),
                    2 => array ( 'pkey' => AP_STATUS['lunas'], 'name' =>  $class->lang['paid'])
				  );

$rsDetailCol = $medicalSalesInvoice->getDetailCollections($rs,'refkey');    

for($i=0;$i<count($rs);$i++){ 
    $rsDetail = $rsDetailCol[$rs[$i]['pkey']];
    $rs[$i]['insuredname']  = implode(', ',array_unique(array_column($rsDetail, 'insuredname'))); 
}

$totalPages = ceil( $medicalSalesInvoice->getTotalRows($criteria) / $totalrowsperpage);  
$arrTwigVar ['totalPages'] =  $totalPages;

$arrTwigVar['rsInvoice'] =   $rs;  
$arrTwigVar['rsStatus'] =   $arrStatusMenu; 
$arrTwigVar['selectedStatus'] = $arrStatus[0]; 
$arrTwigVar['ACTIVE_MENU'] =  $arrActive;  
$arrTwigVar['PAGE_NAME'] =  $class->lang['invoice'];
   

echo $twig->render('invoice-list.html', $arrTwigVar);

?>