<?php 
require_once '../../_config.php'; 
require_once "../../_include-v2.php";  

includeClass(array('Item.class.php','Marketplace.class.php'));

$item=new Item();
$rsItem = $item->searchData('','',true,' and '.$item->tableName.'.statuskey in (1)', 'order by pkey desc');

//try{  
//	if(!$item->oDbCon->startTrans())
//		throw new Exception($item->errorMsg[100]);
//
//	// hapus yg double
//	foreach($rsItem as $row){
//		$sql = 'select pkey from item_marketplace_sync_detail where marketplacekey =4 and refkey = '.$row['pkey'] .' order by pkey desc';
//		$rsLink =  $item->oDbCon->doQuery($sql);
//
//		if (empty($rsLink) || count($rsLink) == 1) continue;
//
//		$sql  = 'delete from item_marketplace_sync_detail where  marketplacekey =4 and pkey <> ' .$rsLink[0]['pkey'].' and refkey = '.$row['pkey']  ;
//		$item->oDbCon->execute($sql);
//	}
//	
//	$item->oDbCon->endTrans(); 
//
//} catch(Exception $e){   
//	$item->oDbCon->rollback(); 
//}		 



$sql = 'select * from item_marketplace_sync_detail where issync = 1 ';
$rsLink = $item->oDbCon->doQuery($sql);
$rsLink = $item->reindexDetailCollections($rsLink,'refkey');
	
$sql = 'select * from marketplace';
$rsMarketplace = $item->oDbCon->doQuery($sql);


echo '<table cellpadding="2" cellspacing="0" style="border:1px solid #999; border-top:0; border-left:0">';
echo '<tr style="font-weight:bold">';
	echo '<td style="border:1px solid #999;  border-bottom:0; border-right:0;">Kode Item</td>'; 
	echo '<td style="border:1px solid #999;  border-bottom:0; border-right:0;">Nama Item</td>';
	
	foreach($rsMarketplace as $mpRow){ 
		echo '<td style="border:1px solid #999;  border-bottom:0; border-right:0;">'.$mpRow['name'].'</td>';
	}
	echo '<td style="border:1px solid #999;  border-bottom:0; border-right:0;"></td>';
	
	echo '</tr>';

foreach($rsItem as $row){
	echo '<tr>';
	echo '<td style="border:1px solid #999; border-bottom:0; border-right:0;">'.$row['code'].'</td>'; 
	echo '<td style="border:1px solid #999;  border-bottom:0; border-right:0;">'.$row['name'].'</td>';
	
	$linked = $rsLink[$row['pkey']];
	$linked = array_column($linked,'marketplacekey');
	
	
	foreach($rsMarketplace as $mpRow){ 
	 
		$checked = (in_array($mpRow['pkey'],$linked)) ? 'v': '';
		
		echo '<td style="border:1px solid #999;  border-bottom:0; border-right:0; text-align:center">'.$checked.'</td>';
	}
	 echo '<td style="border:1px solid #999;  border-bottom:0; border-right:0;">'.implode(',',$linked).'</td>';
	
	echo '</tr>';
}
echo '</table>';

?>