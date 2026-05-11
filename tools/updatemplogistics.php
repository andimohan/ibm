<?php

die("die, comment open for reset transaction");

include_once '../_config.php'; 
include_once '../_include-v2.php';

includeClass(array('Item.class.php','Marketplace.class.php'));
$item = new Item();

$marketplacekey = 2;

//ambil semua logistic yg available
//$dbCon = $class->masterConn(); 
//$sql = 'select * from marketplace_logistics where statuskey = 1 and marketplacekey = '.$marketplacekey;
//$rsMasterShipment = $dbCon->doQuery($sql);
//$rsMasterShipment = array_column($rsMasterShipment,null,'pkey');
//$dbCon = null;

$shopee = new Shopee();
$rsMasterShipment = $shopee->getAvailableMarketplaceLogisticsForItem();
$rsMasterShipment = array_column($rsMasterShipment,null,'pkey');
//	
//echo '<pre>';
//print_r($rsMasterShipment);
//echo '</pre>';
//
//die;

// narik dr shopee, yg nyala yg mana aj
// nanti dulu, test bisa gk kalo gk aktif di shopee nya

$class->oDbCon->startTrans();

$sql = 'truncate item_marketplace_logistics';
$class->oDbCon->execute($sql);

$rsItem = $item->searchDataRow(array($item->tableName.'.pkey'));

foreach($rsItem as $itemRow){
	foreach($rsMasterShipment as $row){
		$sql = 'insert into item_marketplace_logistics (refkey,marketplacekey,reflogistickey) values ('.$itemRow['pkey'].','.$marketplacekey.','.$row['pkey'].')';
		$class->oDbCon->execute($sql);
	} 
}

$class->oDbCon->endTrans();
echo 'done';

?>