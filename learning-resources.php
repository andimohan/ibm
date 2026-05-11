<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

if(!$security->isMemberLogin(false))  {
	header('location:'.KICKED_REDIRECT_URL); 
	die;
}

require_once '_include-customer-information.php';

includeClass(array('DownloadCategory.class.php', 'Download.class.php','MembershipLevel.class.php'));  
$download = new Download();
$membershipLevel = new MembershipLevel();

$rsMembershipLevel = $membershipLevel->searchDataRow(array('pkey','name'),
													 ' and '.$membershipLevel->tableName.'.statuskey = 1'
													);  
 
$rsMembershipLevel[0]['pkey'] = 0;
$rsMembershipLevel[0]['name'] = '-----';
$arrMembershipLevel = $membershipLevel->generateComboboxOpt(array('data' => $rsMembershipLevel));
 
$rsMembershipLevel = array_column($rsMembershipLevel,null,'pkey');

$arrLabelFilter = array();

$arrHostType = array();
$arrHostType[0] = '-----';
$arrHostType[1] = 'Host';
$arrHostType[2] = 'Master Host';

$pageIndex = ( isset($_GET) && !empty($_GET['page']) ) ? $_GET['page'] : 0; 
$totalrowsperpage = $class->loadSetting('productTotalItemPerPage'); 

$pageUrlParam = array();

$arrCriteria = array();
array_push($arrCriteria, $download->tableName.'.statuskey = 1');
array_push($arrCriteria, $download->tableName.'.hosttypekey <= ' . $class->oDbCon->paramString($LOGIN_USER['hostlevelkey']));
array_push($arrCriteria, $download->tableName.'.membershiplevelkey <= ' . $class->oDbCon->paramString($LOGIN_USER['membershiplevel']));

if(isset($_GET['search'])) $_POST['searchkey'] = $_GET['search'];
if(isset($_GET['selHost'])) $_POST['selHost'] = $_GET['selHost'];
if(isset($_GET['membershiplevel'])) $_POST['selMembershipKey'] = $_GET['membershiplevel'];

if (isset($_POST)){ 
	if (!empty($_POST['searchkey'])){ 
		array_push ($arrCriteria, '  (' . $download->tableName . '.name like ' . $class->oDbCon->paramString('%'.$_POST['searchkey'].'%') .' OR
									  ' . $download->tableName . '.shortdesc like ' . $class->oDbCon->paramString('%'.$_POST['searchkey'].'%') .' OR
									  ' . $download->tableName . '.tag like ' . $class->oDbCon->paramString('%'.$_POST['searchkey'].'%') .' 
							 	   )' );
		
		array_push($pageUrlParam,"search=" . $_POST['searchkey']);
	}
	
	if (!empty($_POST['selHost'])){ 
		array_push ($arrCriteria, ' (' . $download->tableName . '.hosttypekey  = ' . $class->oDbCon->paramString($_POST['selHost']) .'
							 	   )' );
		
		array_push($pageUrlParam,"hosttype=" . $_POST['selHost']);
	}
	
	if (!empty($_POST['selMembershipKey'])){ 
		array_push ($arrCriteria, ' (' . $download->tableName . '.membershiplevelkey  = ' . $class->oDbCon->paramString($_POST['selMembershipKey']) .'
							 	   )' );
		
		array_push($pageUrlParam,"membershiplevel=" . $_POST['selMembershipKey']);
	}
}


$arrOpt = array(
			  array(
			  		'name' => $class->lang['membership'],	
			  		'param' => 'selMembershipKey',	
			  		'dbfield' =>  $download->tableName . '.membershiplevelkey',	
			  ),
			  array(
			  		'name' => $class->lang['host'],	
			  		'param' => 'selHost',	
			  		'dbfield' =>  $download->tableName . '.hosttypekey',	
			  ),
		);

foreach($arrOpt as $row){
	
	if(isset($_GET[$row['param']])) $_POST[$row['param']] = explode(',',$_GET[$row['param']]);
	if(isset( $_POST[$row['param']] ) && !empty(  $_POST[$row['param']] )){
		$value = $_POST[$row['param']];
		if(!is_array($value)) $value = array($value);

		$_POST[$row['param'].'[]'] = $value;
		array_push($arrSearchCriteria, $row['dbfield']. ' in (' . $class->oDbCon->paramString($value,',') . ')'); 
		array_push($pageUrlParam,$row['param']."=" . implode(',',$value)); 
		
		
	 	array_push($arrLabelFilter, $row['name']);
	}
	 
} 


$criteria = ' AND '. implode(' AND ',$arrCriteria); 
$orderBy = 'order by '.$download->tableName.'.createdon desc';

$totalPages = ceil( $download->getTotalRows($criteria) / $totalrowsperpage);

$now = $pageIndex * $totalrowsperpage;
if ($pageIndex < 0 || $pageIndex > ($totalPages -1) ){
	$now = 0; 
	$pageIndex = 0; 
}

$limit = ' limit ' . $now . ', ' . $totalrowsperpage;

$rsDownload = $download->searchData('','',true,$criteria,$orderBy,$limit);

for($i=0;$i<count($rsDownload);$i++) { 
    $rsDownload[$i]['phpThumbHash'] =  getPHPThumbHash($rsDownload[$i]['file']);
	$rsDownload[$i]['shortdesc'] = str_replace(chr(13),'<br>',$rsDownload[$i]['shortdesc']);    
    $rsDownload[$i]['hostlevel'] =  $arrHostType[$rsDownload[$i]['hosttypekey']];
    $rsDownload[$i]['membershiplevel'] =  $rsMembershipLevel[$rsDownload[$i]['membershiplevelkey']]['name'];
    $rsDownload[$i]['createdon'] = $class->convertToLocalTimeZone($rsDownload[$i]['createdon'],LOCAL['timezone']['systemGMT'], LOCAL['timezone']['userGMT'] );
}

$arrTwigVar ['pageIndex'] =  $pageIndex;   
$arrTwigVar ['rsDownload'] =  $download->updateContentLang($rsDownload); 
$arrTwigVar ['totalPages'] =  $totalPages; 
$arrTwigVar ['selHost'] =  $class->inputSelect('selHost', $arrHostType);
$arrTwigVar ['selMembershipLevel'] =  $class->inputSelect('selMembershipKey', $arrMembershipLevel);
$arrTwigVar ['inputSearchKey'] =  $class->inputText('searchkey');  
$arrTwigVar ['btnSearch'] =  $class->inputSubmit('btnSearch',$class->lang['search'], array('overwritePost' => false));   
$arrTwigVar ['searchFilter'] =  implode(', ',$arrLabelFilter);
$arrTwigVar ['pageUrlParam'] = (!empty($pageUrlParam)) ? '&'. implode('&',$pageUrlParam) : '';
$arrTwigVar ['STRUCTURE_DATA'] = '';   

array_push($arrTwigVar ['ACTIVE_MENU'], '/member-area.php'); 
echo $twig->render('learning-resources.html', $arrTwigVar);
?>