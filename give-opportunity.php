<?php 
require_once '_config.php';  
require_once '_include-fe-v2.php';
require_once '_global.php';  

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

require_once '_include-customer-information.php';

includeClass(array('GiveOpportunity.class.php'));

$giveOpportunity = new GiveOpportunity(); 

$pageUrlParam = array();

//$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
//$arrTwigVar['pageIndex'] =  $pageIndex;

$totalrowsperpage = $class->loadSetting('productTotalItemPerPage'); //sementara pakai ini dulu

//$orderBy = ' order by '.$giveOpportunity->tableName.'.pkey asc ';
//
//$now = $pageIndex * $totalrowsperpage;
//$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

//$rsGiveOpportunity = $giveOpportunity->searchData('','',true,$criteria,$orderBy,$limit );
//$totalPages = ceil($giveOpportunity->getTotalRows($criteria) / $totalrowsperpage);

$rsCategory =  $giveOpportunity->getCategoryType();
$arrCategory = $class->generateComboboxOpt(array('data' => $rsCategory, 'label' => 'name'));
 
$rsAllCategory = array(array('pkey' => 0, 'name' => $class->lang['allCategories'])); // harus 2 kali array, karena 2 dimensi
$rsCategory = array_merge($rsAllCategory,$rsCategory);
$rsCategory = $giveOpportunity->updateContentLang($rsCategory,$_SESSION['lang'],array('column' => array('name')));   
$rsCategoryByPkey = array_column($rsCategory,null,'pkey');

$arrSearchCategory = $class->generateComboboxOpt(array('data' => $rsCategory, 'label' => 'name'));

$arrType = array();
$arrType[0]= 'Semua Member';
$arrType[3]= 'Pro Member';

$_POST['trDate'] = date('d / m / Y H:i'); 
$_POST['action'] ='add';

// public give =======================================
$pageIndex =  (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : 0;
$arrTwigVar['pageIndex'] =  $pageIndex;

$orderby = '';
$now = $pageIndex * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

$arrCriteria = array();
array_push ($arrCriteria, $giveOpportunity->tableName . '.createdon > now() - interval 1 month');
array_push ($arrCriteria, $giveOpportunity->tableName . '.typekey = 0');
array_push ($arrCriteria, $giveOpportunity->tableName . '.statuskey <> 5'); 


if(isset($_GET['search'])) $_POST['searchkey'] = $_GET['search'];
if(isset($_GET['searchCat'])) $_POST['selSearchCategory'] = $_GET['searchCat'];

if (isset($_POST)){
	
	if (!empty($_POST['selSearchCategory'])){ 
		array_push ($arrCriteria,   $giveOpportunity->tableName . '.categorykey = ' . $class->oDbCon->paramString($_POST['selSearchCategory']) );
	
		array_push($pageUrlParam,"searchCat=" . $_POST['selSearchCategory']);
	}
	
	if (!empty($_POST['searchkey'])){ 
		array_push ($arrCriteria, '  (' . $giveOpportunity->tableName . '.name like ' . $class->oDbCon->paramString('%'.$_POST['searchkey'].'%') .' OR
										  ' . $giveOpportunity->tableName . '.description like ' . $class->oDbCon->paramString('%'.$_POST['searchkey'].'%') .' OR
										  ' . $giveOpportunity->tableName . '.phone like ' . $class->oDbCon->paramString('%'.$_POST['searchkey'].'%') .' 
										)' );
	
		array_push($pageUrlParam,"search=" . $_POST['searchkey']);
	}
	
}

$criteria =' AND '. implode(' AND ',$arrCriteria); 
$orderBy = ' order by ' . $giveOpportunity->tableName . '.createdon desc'; 
$rsPublicGive = $giveOpportunity->searchData('','',true ,$criteria,$orderBy,$limit);

foreach($rsPublicGive as $key=>$row ){
    $rsPublicGive[$key]['categoryname'] = $rsCategoryByPkey[$row['categorykey']]['name'];
	$rsPublicGive[$key]['description'] = str_replace(chr(13),'<br>',$rsPublicGive[$key]['description']);
}

$totalPages = ceil( $giveOpportunity->getTotalRows($criteria) / $totalrowsperpage);

// received give =======================================
$pageIndexRcv =  (isset($_GET) && !empty($_GET['pagercv'])) ? $_GET['pagercv'] : 0;
$arrTwigVar['pageIndexRcv'] =  $pageIndexRcv;

$orderby = '';
$now = $pageIndexRcv * $totalrowsperpage;
$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

$arrCriteria = array();
array_push ($arrCriteria, $giveOpportunity->tableName . '.typekey <> 0');
array_push ($arrCriteria, $giveOpportunity->tableName . '.statuskey <> 5'); 
array_push ($arrCriteria, $giveOpportunity->tableName . '.torecipientkey = ' . $class->oDbCon->paramString(USERKEY)); 


if(isset($_GET['searchRcv'])) $_POST['searchkeyRcv'] = $_GET['searchRcv'];
if(isset($_GET['searchCatRcv'])) $_POST['selSearchCategoryRcv'] = $_GET['searchCatRcv'];


if (isset($_POST)){
	if (!empty($_POST['selSearchCategoryRcv'])){ 
		array_push ($arrCriteria, $giveOpportunity->tableName . '.categorykey = ' . $class->oDbCon->paramString($_POST['selSearchCategoryRcv']) );
	
	 	array_push($pageUrlParam,"searchCatRcv=" . $_POST['selSearchCategoryRcv']);
	}
	
	if (!empty($_POST['searchkeyRcv'])){ 
		array_push ($arrCriteria, '  (' . $giveOpportunity->tableName . '.name like ' . $class->oDbCon->paramString('%'.$_POST['searchkeyRcv'].'%') .' OR
										  ' . $giveOpportunity->tableName . '.description like ' . $class->oDbCon->paramString('%'.$_POST['searchkeyRcv'].'%') .' OR
										  ' . $giveOpportunity->tableName . '.phone like ' . $class->oDbCon->paramString('%'.$_POST['searchkeyRcv'].'%') .' 
										)' );
		
	 	array_push($pageUrlParam,"searchRcv=" . $_POST['searchkeyRcv']);
	}
}

$criteria =' AND '. implode(' AND ',$arrCriteria); 
$orderBy = ' order by ' . $giveOpportunity->tableName . '.createdon desc';  
$rsReceivedGive = $giveOpportunity->searchData('','',true ,$criteria,$orderBy,$limit);
 
foreach($rsReceivedGive as $key=>$row ){
    // ganti lang category
    $rsReceivedGive[$key]['categoryname'] = $rsCategoryByPkey[$row['categorykey']]['name'];
	$rsReceivedGive[$key]['description'] = str_replace(chr(13),'<br>',$rsReceivedGive[$key]['description']);
}

$totalPagesRcv = ceil( $giveOpportunity->getTotalRows($criteria) / $totalrowsperpage);
 
$arrTwigVar ['rsPublicGive'] =  $rsPublicGive; 
$arrTwigVar ['rsReceivedGive'] =  $rsReceivedGive; 
$arrTwigVar ['inputName'] =  $class->inputText('name'); 
$arrTwigVar ['inputPhone'] =  $class->inputText('phone'); 
$arrTwigVar ['inputDescription'] =  $class->inputTextArea('description', array('etc' => 'style="height:10em"'));  
$arrTwigVar ['btnSubmit'] =   $class->inputSubmit('btnSave',$class->lang['save']);
$arrTwigVar ['totalPages'] =  $totalPages; 
$arrTwigVar ['totalPagesRcv'] =  $totalPagesRcv; 
$arrTwigVar ['inputSelCategory'] =  $class->inputSelect('selCategoryKey',$arrCategory); 
$arrTwigVar ['inputSelSearchCategory'] =  $class->inputSelect('selSearchCategory',$arrSearchCategory);  
$arrTwigVar ['inputSearchKey'] =  $class->inputText('searchkey');  
$arrTwigVar ['inputSelSearchCategoryRcv'] =  $class->inputSelect('selSearchCategoryRcv',$arrSearchCategory);  
$arrTwigVar ['inputSearchKeyRcv'] =  $class->inputText('searchkeyRcv');  
$arrTwigVar ['inputSelType'] =  $class->inputSelect('selType',$arrType); 
$arrTwigVar ['btnSearch'] =  $class->inputSubmit('btnSearch',$class->lang['search'], array('overwritePost' => false));   
 
$arrTwigVar ['inputAmount'] =  $class->inputNumber('amount',array('etc' => 'style="text-align:center"')); 
$arrTwigVar ['btnSubmitAmount'] =  $class->inputButton('btnSubmitAmount',$class->lang['submit'], array('overwritePost' => false)); 
$arrTwigVar ['btnFollowUp'] =  $class->inputButton('btnFollowUp',$class->lang['followUp'], array('overwritePost' => false));   
$arrTwigVar ['btnDeal'] =  $class->inputButton('btnDeal',$class->lang['deal'], array('overwritePost' => false));   
$arrTwigVar ['btnNoDeal'] =  $class->inputButton('btnNoDeal',$class->lang['noDeal'], array('overwritePost' => false));   

	
$arrTwigVar ['inputHidAction'] =  $class->inputHidden('action'); 
$arrTwigVar ['inputMember']  = $class->inputAutoComplete(
                                        array( 
                                            'element' => array(
                                                'value' => 'recipientName',
                                                'key' => 'hidRecipientKey'
                                            ),
                                            'source' => array(
                                                'url' => 'ajax-customer.php',
                                                'data' => array('action' => 'searchData', 'membershiplevel' => 3, 'searchField' => 'code,name')
                                            )
                                        )
                                    );


array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 

$arrTwigVar ['pageUrlParam'] = (!empty($pageUrlParam)) ? '&'. implode('&',$pageUrlParam) : '';
echo $twig->render('give-opportunity.html', $arrTwigVar);

?>