<?php 
set_time_limit(36000); // 60 mins

require_once '../../_config.php'; 
require_once "../../_include-v2.php";  
 
includeClass(array('Marketplace.class.php','Brand.class.php','Item.class.php','ItemMovement.class.php'));
 
$brand = createObjAndAddToCol(new Brand());  
$item = createObjAndAddToCol(new Item());  
$itemMovement = new ItemMovement();
	
// ambil kategori dan merk tertentu dulu
$marketplacekey = 4;
//$brandkey = 3; //ladyrose
//$brandkey = 4; //bonita
//$brandkey = 2; //california
// $brandkey = 10; //california polos
$brandkey = 5; //kintakun dluxe

$arrLogisticKey = array(16,46,48,49,50,51,58,62);
 
$arrItemInformation = array();

// $categorykey = 32; // BC Rumbai king 180
// $arrItemInformation[$categorykey]['size'] = '180x200. No 1.';
// $arrItemInformation[$categorykey]['desc'] = 'Barang Original / Asli 100%
// Uk. 180 cm x 200 cm
// Sprei Rumbai
// 1x bedcover
// 2x sarung bantal
// 2x sarung guling

// * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';

// $categorykey = 35; // BC Rumbai queen 160
// $arrItemInformation[$categorykey]['size'] = '160x200. No 2.';
// $arrItemInformation[$categorykey]['desc'] = 'Barang Original / Asli 100%
// Uk. 160 cm x 200 cm
// Sprei Rumbai
// 1x bedcover
// 2x sarung bantal
// 2x sarung guling

// * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';

// $categorykey = 8; // BC king 180
// $arrItemInformation[$categorykey]['size'] = '180x200. No 1.';
// $arrItemInformation[$categorykey]['desc'] = 'Barang Original / Asli 100%
// Uk. 180 cm x 200 cm
// Sprei fitted/karet
// 1x bedcover
// 2x sarung bantal
// 2x sarung guling

// * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';

// $categorykey = 9; // BC queen 160
// $arrItemInformation[$categorykey]['size'] = '160x200. No 1.';
// $arrItemInformation[$categorykey]['desc'] = 'Barang Original / Asli 100%
// Uk. 160 cm x 200 cm
// Sprei fitted/karet
// 1x bedcover
// 2x sarung bantal
// 2x sarung guling

// * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';

//
//$categorykey = 5; // B4 180
//
//$arrItemInformation[$categorykey]['size'] = '180x200. No 1.';
//$arrItemInformation[$categorykey]['desc'] = 'Barang Original / Asli 100%
//Uk. 180 cm x 200 cm
//Sprei fitted/karet
//4x sarung bantal
//2x sarung guling
//
//* Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';


//
//
// $categorykey = 3; // king 180
// $arrItemInformation[$categorykey]['size'] = '180x200. No 1.';
// $arrItemInformation[$categorykey]['desc'] = 'Barang Original / Asli 100%
// Uk. 180 cm x 200 cm
// Sprei fitted/karet
// 2x sarung bantal
// 2x sarung guling

// * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';


// $categorykey = 2; // queen 180
// $arrItemInformation[$categorykey]['size'] = '160x200. No 2.';
// $arrItemInformation[$categorykey]['desc'] = 'Barang Original / Asli 100%
// Uk. 160 cm x 200 cm
// Sprei fitted/karet
// 2x sarung bantal
// 2x sarung guling

// * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';


$categorykey = 1; // sprei 120
$arrItemInformation[$categorykey]['size'] = '120x200. No 3.';
$arrItemInformation[$categorykey]['desc'] = 'Barang Original / Asli 100%
Uk. 120 cm x 200 cm
Sprei fitted/karet
1x sarung bantal
1x sarung guling

* Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';


//$categorykey = 25; // sprei 90
//$arrItemInformation[$categorykey]['size'] = '90x200. No 5.';
//$arrItemInformation[$categorykey]['desc'] = 'Barang Original / Asli 100%
//Uk. 90 cm x 200 cm
//Sprei fitted/karet
//1x sarung bantal
//1x sarung guling
//
//* Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';



$rsBrand = $brand->getDataRowById($brandkey);
$rsItem = $item->searchDataRow(array('pkey','name'),
							  ' and categorykey = '.$class->oDbCon->paramString($categorykey).' and brandkey = '.$class->oDbCon->paramString($brandkey) );

if ($rsBrand[0]['name'] == 'Lady Rose')
	$rsBrand[0]['name'] = 'Ladyrose';
else if ($rsBrand[0]['name'] == 'California - Polos')
	$rsBrand[0]['name'] = 'California';
	 

$ctr= 0 ; 
     
foreach($rsItem as $row){
	 $ctr++;
	 $patternName = explode('Corak',$row['name']);
	 $patternName =trim($patternName[1]);
 
    if (empty($patternName) || $patternName == 'Random' ) continue;
 
    
     switch($categorykey){
         case 32:
         case 35: $bedcover = ' + Bedcover Rumbai 1 Set ';
                  break;
                  
         case 8:
         case 9: $bedcover = ' + Bedcover 1 Set ';
                  break;
                  
         default : $bedcover= '';
                  break;
                
         
     }
	  
	 $itemName = 'Sprei/Seprei '.$bedcover.$rsBrand[0]['name'].'. '.$arrItemInformation[$categorykey]['size'].' '.  $patternName ;
	 $desc = $arrItemInformation[$categorykey]['desc'];


	try{			 
		if (!$class->oDbCon->startTrans(true))
			throw new Exception($class->errorMsg[100]);


		 // cari udah ad blm aliasnya, kalo udah ad update, kalo blm insert
		 $sql = 'select * from item_marketplace_sync_detail where refkey = ' . $row['pkey'] . ' and marketplacekey = ' . $marketplacekey;
		 $rsSync = 	$class->oDbCon->doQuery($sql);

		 if (empty($rsSync)){
			$sql = 'insert into item_marketplace_sync_detail (refkey,marketplacekey,issync,name,shortdescription) 
					values ('.$class->oDbCon->paramString($row['pkey']).','.$marketplacekey.',1,'.$class->oDbCon->paramString($itemName).','.$class->oDbCon->paramString($desc).')';
		 }else{
			 
			 // hanya jika marketplace = 4
			 if ($marketplacekey == 4){ 
				 $sql = 'update item_marketplace_sync_detail 
						set  
							name = '.$class->oDbCon->paramString($itemName).' ,
							shortdescription ='.$class->oDbCon->paramString($desc).' 
						where 
							marketplacekey = '.$marketplacekey.' and
							refkey  = ' . $class->oDbCon->paramString($row['pkey']); 
			 }
		 }

		$class->oDbCon->execute($sql);

		// add semua logistik, kalo blm ada
		$sql = 'select * from item_marketplace_logistics where refkey = ' .  $row['pkey'] . ' and marketplacekey = ' . $marketplacekey;
		$rsLog = $class->oDbCon->doQuery($sql);

		if(empty($rsLog)){
			foreach($arrLogisticKey as $logistickey){ 
				$sql = 'insert into item_marketplace_logistics (refkey,marketplacekey,reflogistickey) values ('.$row['pkey'].','.$marketplacekey.','.$logistickey.') ';
				$class->oDbCon->execute($sql);
			}
		}

		// add ulang attribute kalo blm ada 
		$sql = 'select * from item_category_marketplace_attributes where refkey = ' .  $row['pkey'] . ' and marketplacekey = ' . $marketplacekey;
		$rsAttr= $class->oDbCon->doQuery($sql);
		if(empty($rsAttr)){
			$sql = 'insert into item_category_marketplace_attributes (refkey,marketplacekey,attributekey,value) 
					values ('.$row['pkey'].','.$marketplacekey.',100134,\'Combed Cotton\') ';
			$class->oDbCon->execute($sql); 

			$sql = 'insert into item_category_marketplace_attributes (refkey,marketplacekey,attributekey,value) 
					values ('.$row['pkey'].','.$marketplacekey.',0,'.$class->oDbCon->paramString($rsBrand[0]['name']).') ';
			$class->oDbCon->execute($sql);  
		}else{ 
			$sql = 'update item_category_marketplace_attributes set value = '.$class->oDbCon->paramString($rsBrand[0]['name']).' 
			        where pkey = '.$class->oDbCon->paramString($rsAttr[0]['pkey']) ;
			         
			$class->oDbCon->execute($sql);  
		}


		$class->oDbCon->endTrans(); 
	}catch(Exception $e){
		$class->oDbCon->rollback(); 
		echo $e->getMessage().'<br>';
		die;
	}			

}


echo 'total proses ' . ($ctr-1).'<br>'; 

//  >>>>>>>>>>>>>>>>>>>>>> SYNC

$shopeeObj = new Shopee($marketplacekey); 

$arrItemKey = array_column($rsItem,'pkey');
$rsQOH = $itemMovement->getItemsQOH($arrItemKey);
$rsQOH = array_column($rsQOH,null,'itemkey');

$arrSyncItemKey = array();
foreach($arrItemKey as $itemkey){ 
	if($rsQOH[$itemkey]['qtyinbaseunit'] > 0) 
		array_push($arrSyncItemKey,$itemkey); 
}

// kalo gk, nanti dia sync ulang semua
if(!empty($arrSyncItemKey)){
   try{			 
    	if (!$class->oDbCon->startTrans())
    		throw new Exception($class->errorMsg[100]);
    
    		// update marketplace
    		$syncCriteria = array();
    		
			if ($marketplacekey == 4)
	   			$syncCriteria['attr'] = array('name','brand', 'qoh', 'price','measurement', 'status','shortDescription','image', 'others'); // array(ALL) <- harusnya ksoong, artiyna updatte semua
    		else
				$syncCriteria['attr'] = array('qoh'); // array(ALL) <- harusnya ksoong, artiyna updatte semua
	   
	   		$syncCriteria['type'] = 2;  
    		$syncCriteria['itemkey'] = $arrSyncItemKey; 
    
    		//$class->setLog($arrSyncItemKey,true);
    		$shopeeObj->syncProducts($syncCriteria);
    
    	$class->oDbCon->endTrans(); 
    }catch(Exception $e){
    	$class->oDbCon->rollback(); 
    	echo $e->getMessage().'<br>';
    	die;
    }	 
}
		
 

echo 'total sync ' . (count($arrSyncItemKey)).'<br>';
echo 'done';
?> 