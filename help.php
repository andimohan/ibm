<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';

includeClass(array('Page.class.php','PageCategory.class.php'));
require_once '_global.php';

$page = new Page();
$pageCategory = new PageCategory();


// isi pertama kalo saja 
//try{			
//
//			$arrayToJs =  array();
//
//			if (!$page->oDbCon->startTrans())
//				throw new Exception($page->errorMsg[100]);
// 
//			$sql = 'truncate page_category_detail';
//			$page->oDbCon->execute($sql);
//	 	
//			$sql = 'select * from page where pkey > 8001';
//			$rs = $page->oDbCon->doQuery($sql);
//
//			foreach($rs as $row){
//				$sql = 'insert into page_category_detail (refkey,categorykey) values ('.$page->oDbCon->paramString($row['pkey']).',2) '; 
//				$page->oDbCon->execute($sql);
//			}
//	
//			$page->oDbCon->endTrans();
// 
//
//		}catch(Exception $e){
//			$page->oDbCon->rollback(); 
//	}			


//try{			
//
//			$arrayToJs =  array();
//	
//			$arr = array('login','bussiness-partner','customer','supplier','employee','workarea','customer category','division','product and services',
//						 'brand','sales','finance','ap','ap payment','ar','ar payment','top','payment method','others','city','city category','location',
//						'ar employee', 'ar employee payment','cash in','cash out','cash bank transfer');
//	
//			if (!$page->oDbCon->startTrans())
//				throw new Exception($page->errorMsg[100]);
// 
//			$sql = 'delete from page_category_detail where categorykey <> 2';
//			$page->oDbCon->execute($sql);
//	 	
//			$sql = 'select * from page where pagename in ('.$page->oDbCon->paramString($arr,',').')';
//			$rs = $page->oDbCon->doQuery($sql);
//
//			for($i=1;$i<=4;$i++){
//				if ($i == 2) continue;
//
//				foreach($rs as $row){
//					$sql = 'insert into page_category_detail (refkey,categorykey) values ('.$page->oDbCon->paramString($row['pkey']).','.$i.') '; 
//					$page->oDbCon->execute($sql);
//				}
//			}
//	
//			$page->oDbCon->endTrans();
// 
//
//		}catch(Exception $e){
//			$page->oDbCon->rollback(); 
//	}			

	
/* ===================== HELP CATEGORY ========================================== */
$arrPageCategory = $pageCategory->generateComboboxOpt(null,array('criteria' => ' and ('.$pageCategory->tableName.'.statuskey = 1)')); 

if(!isset($_SESSION['category']))	$_SESSION['category'] = array_keys($arrPageCategory)[0];
else $_POST['selCategory'] = $_SESSION['category'];
	
if(isset($_GET) && !empty($_GET['categorykey'])){ 
	$_POST['selCategory'] = $_GET['categorykey'];
	$_SESSION['category'] = $_GET['categorykey'];
}
	
$rsFirst = $page->searchData($page->tableName.'.pagename','help',true);  
$arrFilter = $page->getCategoryDetail($_SESSION['category']);
$arrFilter = array_column($arrFilter,'refkey');

// filter berdasarkan kategori
// kalo gk ad page_category_detail, berarti muncul disemua kategori


$helpRootKey = $rsFirst[0]['pkey'];
$rsCategoryTree = $page->getCategoryTree($helpRootKey,$arrFilter);  

$pagekey = (isset($_GET) && !empty($_GET['page'])) ? $_GET['page'] : $rsCategoryTree[0]['pkey']; 
 
$rsPage = $page->searchData($page->tableName.'.pkey',$pagekey,true,' and '.$page->tableName.'.pkey in ('.$page->oDbCon->paramString($arrFilter,',').')');
$rsPage = $page->updateContentLang($rsPage);  

$arrTwigVar['compiledCategory'] = $page->compileChildArray($helpRootKey,$arrFilter);
$arrTwigVar['categoryTree'] = $rsCategoryTree;
$arrTwigVar['helpRootKey'] = $helpRootKey;
$arrTwigVar['title'] =  $rsPage[0]['title']; 
$arrTwigVar['content'] =  $rsPage[0]['detail']; 
$arrTwigVar['pageKey'] =  $rsPage[0]['pkey']; 
$arrTwigVar['pageImage'] =  $rsPage[0]['file'];   
$arrTwigVar['ACTIVE_MENU'] = array($rsPage[0]['pagename']);  
$arrTwigVar['PAGE_ID'] =  $rsPage[0]['pagename'];
$arrTwigVar['selCategory'] = $page->inputSelect('selCategory',  $arrPageCategory); 

echo $twig->render('help.html', $arrTwigVar);

?>