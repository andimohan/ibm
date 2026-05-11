<?php 
require_once '../../_config.php'; 
require_once "../../_include.php";  

set_time_limit(1800); // 30 mins
  
$rsItemCategory = $itemCategory->searchData('','',true, ' and ('.$itemCategory->tableName.'.statuskey = 1)', ' order by name asc');

$arrBrand = $class->convertForCombobox($brand->searchData('','',true, ' and ('.$brand->tableName.'.statuskey = 1)', ' order by name asc'),'pkey','name'); 
$arrCategory = $class->convertForCombobox($rsItemCategory,'pkey','name'); 

?>

<form action="#" method="post">
    <?php  
        echo $class->inputSelect('selBrand', $arrBrand) . ' '.  $class->inputSelect('selCategory', $arrCategory) ;
        echo '<br><br>';
        echo $class->inputSubmit('btnSubmit', 'Submit');
    ?>
</form>


<?php
 

if(isset($_POST) && !empty($_POST['btnSubmit'])){
    
    $brandkey = $_POST['selBrand'];
    $categorykey = $_POST['selCategory'];
    //$price = $class->unformatNumber($_POST['textPrice']);
    
    $rsBrand = $brand->getDataRowById($brandkey);
    $rsCategory = $itemCategory->getDataRowById($categorykey);
    
    echo 'Update Merk <b>'.$rsBrand[0]['name'] .'</b>,<br>Kategori <b>'.$rsCategory[0]['name'].'</b>';
    
    $criteria = ' brandkey = '.$class->oDbCon->paramString($brandkey).' and categorykey =  '.$class->oDbCon->paramString($categorykey);
    $criteria .= ' and name like \'%Corak%\' '; // agar beda dengan barnag yg random
    $sql = 'select * from item where '.$criteria.' order by pkey asc ';
    $rs = $class->oDbCon->doQuery($sql);

	// update dulu nama produknya
	$class->oDbCon->startTrans();
	
	foreach($rs as $row){
		
		$arrName = explode('Corak',$row['name']);
		$corak = trim($arrName[1]);
		// Sprei Bonita King 180x200 Corak 
 		
		if(in_array($rsCategory[0]['pkey'],array(1,2,3,4,15,25)))
			$type = 'Sprei';
		else if(in_array($rsCategory[0]['pkey'],array(5)))
			$type = 'Sprei Bantal 4';
		else if(in_array($rsCategory[0]['pkey'],array(6,29)))
			$type = 'Sprei Rumbai';
		else if(in_array($rsCategory[0]['pkey'],array(8,9,10)))
			$type = 'Bedcover & Sprei Set';
		else if(in_array($rsCategory[0]['pkey'],array(32,35)))
			$type = 'Bedcover & Sprei Rumbai Set';
		else if(in_array($rsCategory[0]['pkey'],array(14)))
			$type = 'Sprei Rumbai Bantal 4';
		else if(in_array($rsCategory[0]['pkey'],array(26)))
			$type = 'Sprei Sorong/Duo';
		else if(in_array($rsCategory[0]['pkey'],array(11,12)))
			$type = 'Selimut';
		else if(in_array($rsCategory[0]['pkey'],array(13)))
			$type = 'Sarung Bantal';
		else if(in_array($rsCategory[0]['pkey'],array(33)))
			$type = 'Sprei Rumbai Bantal Busa'; 
		else
			$type = '';
		
		$categoryName = $rsCategory[0]['name'];
		$categoryName = str_replace('Bedcover','',$categoryName);
		$categoryName = str_replace('Rumbai','',$categoryName);
		$categoryName = str_replace('B4','',$categoryName);
		$categoryName = str_replace('(Bantal 4)','',$categoryName);
		$categoryName = str_replace('Bantal 4','',$categoryName);
		$categoryName = str_replace('Sorong/Duo','',$categoryName);
		$categoryName = str_replace('Selimut','',$categoryName);
		$categoryName = str_replace('Bantal Busa','',$categoryName);
		$categoryName = trim($categoryName);
		
		if(!empty($type)){
			$newName = $type.' ' .$rsBrand[0]['name'] . ' ' .$categoryName. ' Corak '.$corak;
			$sqlUpdate = 'update item set name = ' .$class->oDbCon->paramString($newName) . ' where pkey = ' . $row['pkey'];
			$class->oDbCon->execute($sqlUpdate); 
			echo '<br>'.$newName;
		}
		
	}
	$class->oDbCon->endTrans();
	
	// ambil ulang rs nya, utk nama agar lebih rapi, cari yg statusnya aktif saja coba. soalnya yg nonaktif gk bisa diedit jg
	$criteria = ' brandkey = '.$class->oDbCon->paramString($brandkey).' and categorykey =  '.$class->oDbCon->paramString($categorykey);
    $criteria .= ' and name like \'%Corak%\' and statuskey = 1'; // agar beda dengan barnag yg random
    $sql = 'select * from item where '.$criteria.' order by pkey asc ';
	$rs = $class->oDbCon->doQuery($sql);
	
	//print_r($rs);
    echo '<br><br>';
    updateproducts($rs);
    echo '<br><br>done';
}



function updateProducts($rs){
    
    global $class; 
    global $item;
    global $itemMovement;
     
    global $GRAMASI;
    
    echo '<ol class="item-list" >';
    foreach($rs as $itemRow){   
        echo '<li relkey="'.$itemRow['pkey'].'"><b>'.$itemRow['name'].'</b> <span class="status" style="margin-left : 0.5em">updating...</span></li>';  
    }
    echo '</ol>';
}
?>

<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />        
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>   

<script type="text/javascript">
    jQuery(document).ready(function(){
         $(".item-list li").each(function(){   
             var itemline = $(this);
             var itemkey = $(this).attr("relkey"); 
             
               $.ajax({ 
                      url: 'ajax-marketplace.php',  
                      method : 'POST',
                      data: 'action=resyncitem&itemkey='+itemkey,  
                      success: function(data){   
                           itemline.find(".status").html("<span style=\"color:#568203\">done</style>");
                       } 
                    });
          })     
    });
</script>

</head>
</html>

<?php
 
/*
$brandkey = 4; // bonita
$arrCategory = array(1,2,3,4,5,6,14,15,26,29,33, 36); // sprei
$arrCategoryBedcover = array(8,9,10,32,35); // bedcover 
$arrCategory = array_merge($arrCategory,$arrCategoryBedcover);

// test
$arrCategory = array(1);

$sql = 'select * from item where brandkey = '.$brandkey.' and categorykey in ('.implode(',',$arrCategory).') and name not like "Sprei %"';
$rsItem =  $class->oDbCon->doQuery($sql); 

foreach($rsItem as $row){
    $class->oDbCon->startTrans();
    $sql = 'update item set name = concat("Sprei ", name) where pkey = '.$row['pkey']; 
    echo $sql.'<br>';
    $class->oDbCon->execute($sql);
    $class->oDbCon->endTrans();
}



$arrPkey = array_column($rsItem,'pkey'); 

$syncCriteria = array(); 
$syncCriteria['attr'] = array('name','qoh'); // karena kalo stok awal 0, pas brg masuk, harga harus update ulang
$syncCriteria['type'] = 2;  
$syncCriteria['itemkey'] = $arrPkey; 

$marketplace->syncProductsInAllMarketplace($syncCriteria);  
*/
  
?>