<?php
include_once '../../_config.php'; 
include_once '../../_include-v2.php';

// untuk sementara khusus triby saja dulu
if(DOMAIN_NAME != 'triby.wintera.co.id') die("error domain");
    
includeClass(array('Marketplace.class.php','Item.class.php'));
$marketplace = new Marketplace();
$item = new Item();

$itemSKU = (isset($_POST) && !empty($_POST['txtItemCode'])) ? explode(chr(13),$_POST['txtItemCode']) : array();
// ntah kenapa hasil explode ad spasi 
foreach($itemSKU as $key=>$sku)
	$itemSKU[$key] = trim($itemSKU[$key]);
	
// gk perlu, dibawwah sudah ad
//if(isset($_POST) && empty($itemSKU)) die('done');

if(!empty($itemSKU)){
	
	$marketplacekey = 3 ; // nanti buat bisa pilih, karena kalo narik dr marketplace. berarti cuma 1 marketplace yg mau ditarik
	$tokopedia = new Tokopedia($marketplacekey);

	//$rsMarketplace=$marketplace->searchDataRow(array(), ' and ');
	//$url = $tokopedia->url.'inventory/v1/fs/'.$tokopedia->fsid.'/product/info?sku=';
	
	$rsItem = $item->searchDataRow(array('code','pkey'),' and code in ('.$class->oDbCon->paramString($itemSKU,',').')');
	
	$rsItem = array_column($rsItem,null,'code');
	 
		foreach($itemSKU as $sku){   
			
			echo $sku.'<Br>';
			
			$result = $tokopedia->getProductBySKU($sku);
			 
            $arrMPPictures = $result['pictures']; 
            
			$result = $result['basic'];
			
			$name = $result['name'];
			$desc = $result['shortDesc'];
			
 
			 try{ 

				if(!$class->oDbCon->startTrans(true))
					throw new Exception($this->errorMsg[100]);

				// update detail
				$sql = 'select  item_marketplace_sync_detail.pkey
						from
							item_marketplace_sync_detail,item
						where
							item.pkey = item_marketplace_sync_detail.refkey and
							item.code = '.$class->oDbCon->paramString($sku).' 
						';
				$rs = $class->oDbCon->doQuery($sql);
				 
				if (empty($rs)){
					$sql = 'insert into item_marketplace_sync_detail (refkey, marketplacekey, issync,name,shortdescription)
							values ('.$class->oDbCon->paramString($rsItem[$sku]['pkey']).','.$class->oDbCon->paramString($marketplacekey).',0
									,'.$class->oDbCon->paramString($name).','.$class->oDbCon->paramString($desc).')'; 
					$class->oDbCon->execute($sql);
				}else{
					$sql = 'update item,item_marketplace_sync_detail
							set 
								item_marketplace_sync_detail.name = '.$class->oDbCon->paramString($name).',
								item_marketplace_sync_detail.shortdescription =  '.$class->oDbCon->paramString($desc).'
							where
								item.pkey = item_marketplace_sync_detail.refkey and
								item.code = '.$class->oDbCon->paramString($sku).' 
							';
					$class->oDbCon->execute($sql);
				}
				 
		

				// update header
				$sql = 'update item
						set  
							item.shortdescription =  '.$class->oDbCon->paramString($desc).'
						where
							(item.shortdescription = \'\' or  item.shortdescription is null ) and
							item.code = '.$class->oDbCon->paramString($sku).' 
						';
				$class->oDbCon->execute($sql);
				
				// update image, kalo blm ad image aj. biar gk double, utk saat ini 
				$sql = 'select * from item_image where refkey = ' . $class->oDbCon->paramString($rsItem[$sku]['pkey']);
				$rsImage = 	$class->oDbCon->doQuery($sql);
				  
				if(empty($rsImage)){
				    
				    $arrImages = array();
				    foreach($arrMPPictures as $picRow){
                        
                        $imgUrl = $picRow['OriginalURL'];
                        $fileName = $picRow['fileName'];
                 
                        // temp
                        // $saveToTempPath = $item->uploadTempDoc.'item/'.$rsItem[$sku]['pkey'].'/'; 
                        // mkdir($saveToTempPath, 0755, true);  
                        // $saveToTemp = $saveToTempPath.$fileName;
                        
                        // doc
                        $saveToPath = $item->defaultDocUploadPath.'item/'.$rsItem[$sku]['pkey'].'/'; 
                        mkdir($saveToPath, 0755, true); 
                        $saveTo = $saveToPath.$fileName;
                        
                        grab_image($imgUrl,$saveTo);
                        $imgSize = getimagesize($saveTo);  
                        $size = filesize($saveTo);
                             
                        $sql = 'insert into item_image (`refkey`,`file`,`size`,`width`,`height`) 
                                values (
                                        '.$class->oDbCon->paramString($rsItem[$sku]['pkey']).',
                                        '.$class->oDbCon->paramString($fileName).',
                                        '.$class->oDbCon->paramString($size).',
                                        '.$class->oDbCon->paramString($imgSize[0]).',
                                        '.$class->oDbCon->paramString($imgSize[1]).' 
                                        )';
                                         
                        $class->oDbCon->execute($sql);
				    }
				    
				}


				$class->oDbCon->endTrans(); 


			} catch(Exception $e){
				$class->oDbCon->rollback(); 
			}		
 
		}
 
	echo '<br><br>done';
	die; 
}

function grab_image($url,$saveto){
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($saveto)){
        unlink($saveto);
    }
    $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);
}

?>

<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
</head>
	
	<form action="#" method="post">
    <?php  
        echo $class->inputTextArea('txtItemCode', array('etc' => 'style="width: 30em; height: 20em"'));
        echo '<br><br>';
        echo $class->inputSubmit('btnSubmit', 'Submit');
    ?>
</form>

	
</html>