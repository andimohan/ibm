<?php 
require_once '../../_config.php'; 
require_once "../../_include-v2.php";  

includeClass(array('Item.class.php', 'Category.class.php', 'ItemCategory.class.php','Marketplace.class.php'));
$item = new Item();
$itemCategory = new ItemCategory();
$marketplace = new Marketplace();

if(!isset($_POST) && empty($_POST['action']))
    die;

$ARR_MARKETPLACE = array(1,2,3);  

switch($_POST['action']){ 
    case 'updateprice' : if(!isset($_POST) || empty($_POST['itemkey'])) die;
        
                        $ARR_ATTR = array(); 
                        $ARR_ATTR[1] = array(); // LAZADA
                        $ARR_ATTR[2] = array(); // SHOPEE
                        $ARR_ATTR[3] = array(); // TOKOPEDIA

                        $rsItemCategory = $itemCategory->searchData('','',true, ' and ('.$itemCategory->tableName.'.statuskey = 1)');
                        foreach($rsItemCategory as $itemCategoryRow){ 
                            foreach($ARR_MARKETPLACE as $marketplacekey)
                                $ARR_ATTR[$marketplacekey][$itemCategoryRow['pkey']] = array(); 
                        }

                        // bedcover RUMBAI king 
                        $categorykey = 32 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','King')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Bedcover, sprei rumbai, 2 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','King')); 

                        $GRAMASI[$categorykey] = '4';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Bedcover, sprei rumbai ukuran No. 1 / King 180 cm x 200 cm, 2 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';



                        // bedcover FITTED king 
                        $categorykey = 8 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','King')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Bedcover, sprei fitted, 2 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','King')); 

                        $GRAMASI[$categorykey] = '4';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Bedcover, sprei fitted/karet ukuran No. 1 / King 180 cm x 200 cm, 2 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';



                        // FITTED extra king  
                        $categorykey = 15 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','Super King')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei fitted, 2 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Super King')); 

                        $GRAMASI[$categorykey] = '2';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Sprei fitted/karet ukuran Extra King 200 cm x 200 cm, 2 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';

                        // king 
                        $categorykey = 3 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','King')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei fitted, 2 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','King')); 

                        $GRAMASI[$categorykey] = '1.2';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Sprei fitted/karet ukuran No. 1 / King 180 cm x 200 cm, 2 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';

                        // king B4
                        $categorykey = 5 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','King')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei fitted, 4 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','King')); 

                        $GRAMASI[$categorykey] = '1.2';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Sprei fitted/karet ukuran No. 1 / King 180 cm x 200 cm, 4 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';

                        // RUMBAI king
                        $categorykey = 6 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','King')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei rumbai, 2 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','King')); 

                        $GRAMASI[$categorykey] = '2';

                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Sprei rumbai ukuran No. 1 / King 180 cm x 200 cm, 2 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';

                        // RUMBAI king B4
                        $categorykey = 14 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','King')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei rumbai, 4 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','King')); 

                        $GRAMASI[$categorykey] = '2';

                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Sprei rumbai ukuran No. 1 / King 180 cm x 200 cm, 4 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';


                        // RUMBAI BANTAL BUSA king
                        $categorykey = 33 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','King')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei rumbai, 1 bantal busa, 2 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','King')); 

                        $GRAMASI[$categorykey] = '2';

                        // bedcover FITTED queen 
                        $categorykey = 9 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','QUEEN')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Bedcover, Sprei fitted, 2 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Queen')); 

                        $GRAMASI[$categorykey] = '4'; 
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Bedcover, sprei fitted/karet ukuran No. 2 / Queen 160 cm x 200 cm, 2 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';


                        // bedcover RUMBAI queen 
                        $categorykey = 35 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','QUEEN')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Bedcover, Sprei rumbai, 2 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Queen')); 

                        $GRAMASI[$categorykey] = '4';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Bedcover, sprei rumbai ukuran No. 2 / Queen 160 cm x 200 cm, 2 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.'; 

                        // queen 
                        $categorykey = 2 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','QUEEN')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei fitted, 2 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Queen')); 

                        $GRAMASI[$categorykey] = '1.2';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Sprei fitted/karet ukuran No. 2 / Queen 160 cm x 200 cm, 2 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';

                        // Queen B4
                        $categorykey = 36 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','QUEEN')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei fitted, 4 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Queen')); 

                        $GRAMASI[$categorykey] = '2';

                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Sprei fitted/karet ukuran No. 2 / Queen 160 cm x 200 cm, 4 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';



                        // RUMBAI queen 
                        $categorykey = 29 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','QUEEN')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei rumbai, 2 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Queen')); 

                        $GRAMASI[$categorykey] = '2';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Sprei rumbai ukuran No. 2 / Queen 160 cm x 200 cm, 2 sarung bantal, 2 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';


                        // bedcover FITTED single 
                        $categorykey = 10 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','Single')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Bedcover, Sprei fitted, 1 sarung bantal dan 1 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Single')); 

                        $GRAMASI[$categorykey] = '2';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Bedcover, sprei fitted/karet ukuran No. 3 / Single 120 cm x 200 cm, 1 sarung bantal, 1 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';



                        // single 120
                        $categorykey = 1 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','Single')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei fitted, 1 sarung bantal dan 1 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Single')); 

                        $GRAMASI[$categorykey] = '1';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Sprei fitted/karet ukuran No. 3 / Single 120 cm x 200 cm, 1 sarung bantal, 1 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';


                        // single 100
                        $categorykey = 4 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','Single')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei fitted, 1 sarung bantal dan 1 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Single')); 

                        $GRAMASI[$categorykey] = '1';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Sprei fitted/karet ukuran No. 4 / Single 100 cm x 200 cm, 1 sarung bantal, 1 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';


                        // single sorong
                        $categorykey = 26 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','Twin')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei fitted, 2 sarung bantal dan 2 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Twin'));

                        $GRAMASI[$categorykey] = '1';



                        // single 90
                        $categorykey = 25 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','Single')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Sprei fitted, 1 sarung bantal dan 1 sarung guling')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Single')); 

                        $GRAMASI[$categorykey] = '1';
                        $DESC[$categorykey] = 'Barang dijamin ORIGINAL / ASLI 100% !!. Sprei berkualitas tinggi dengan bahan yang halus, lembut dan di jamin tidak luntur. Teknologi disperse printing membuat sprei memiliki motif printing yang sangat lembut dan warna yang bagus.
                        Kelengkapan : Sprei fitted/karet ukuran No. 4 / Single 90 cm x 200 cm, 1 sarung bantal, 1 sarung guling. * Gambar hanya menunjukan ilustrasi corak / motif. Bukan menunjukan jenis / ukuran sprei.';


                        // SELIMUT queen 
                        $categorykey = 12 ; 
                        array_push($ARR_ATTR[1][$categorykey], array('bedding_size_2','QUEEN')); 
                        array_push($ARR_ATTR[1][$categorykey], array('package_content','Selimut')); 
                        array_push($ARR_ATTR[1][$categorykey], array('color_family','Multicolor')); 

                        array_push($ARR_ATTR[2][$categorykey], array('16041','Katun')); 
                        array_push($ARR_ATTR[2][$categorykey], array('16042','Queen')); 

                        $GRAMASI[$categorykey] = '1';

 
                        $itemkey = $_POST['itemkey']; 
                        $price = $class->unformatNumber($_POST['price']); 
                        updateproducts($itemkey,$price);
                       
                        break;
    
    case 'resynstock' : if(!isset($_POST) || empty($_POST['itemkey'])) die;
                        $marketplace->updateProductsQOHInAllMarketplace($_POST['itemkey']);
                        break;
                        

        
   case 'resyncitem' :  
                        if(!isset($_POST) || empty($_POST['itemkey'])) die;
    
                        $arrPkey = $_POST['itemkey'];  

                        $syncCriteria = array(); 
        
                        // karena kalo stok awal 0, pas brg masuk, harga harus update ulang
                        // harus semua, karena kalo produk nya baru, harus update semau attribute
                        $syncCriteria['attr'] = array('name','brand', 'qoh', 'price','measurement', 'shortDescription','image', 'others'); 
                        
                        $syncCriteria['type'] = 2;  
                        $syncCriteria['itemkey'] = $arrPkey; 

                        $marketplace->syncProductsInAllMarketplace($syncCriteria);   
                        break;
        
}


function updateProducts($itemkey, $price){
    
    global $class;
    global $marketplace;
    global $item;
    
    global $ARR_MARKETPLACE;
    global $ARR_ATTR;
    global $GRAMASI;
    global $DESC;
     
    $class->oDbCon->startTrans();  
    //$class->setLog($itemkey.' '.$price,true); 
    
    $rsItem = $item->getDataRowById($itemkey);
    $itemRow = $rsItem[0];
    
    $itemkey = $itemRow['pkey']; 
    $categorykey = $itemRow['categorykey']; 

    
    foreach($ARR_MARKETPLACE as $marketplacekey){

        
         if (!isset($ARR_ATTR[$marketplacekey][$categorykey]) || empty($ARR_ATTR[$marketplacekey][$categorykey]) ) continue;
        
         foreach($ARR_ATTR[$marketplacekey][$categorykey] as $attrRow){

            $attributekey =  $attrRow[0];
            $attributevalue =  $attrRow[1];

            $sql = 'select * from item_category_marketplace_attributes where refkey = '. $class->oDbCon->paramString($itemkey).' and marketplacekey = '.$marketplacekey.' and attributekey = \''.$attributekey.'\'';
            $rs = $class->oDbCon->doQuery($sql);

            // if empty
            if(empty($rs)){
                $sql = 'insert into  item_category_marketplace_attributes (refkey,marketplacekey, attributekey, value)  
                        values  ('.$class->oDbCon->paramString($itemkey).','.$marketplacekey.', \''.$attributekey.'\', '.$class->oDbCon->paramString($attributevalue).')';
            }else{
                $sql = 'update item_category_marketplace_attributes 
                        set value =  '.$class->oDbCon->paramString($attributevalue).' 
                        where  refkey = '. $class->oDbCon->paramString($itemkey).' and marketplacekey = '.$marketplacekey.' and attributekey = \''.$attributekey.'\'';
            }

            //$class->setLog($sql,true);
            $class->oDbCon->execute($sql);
        }
        
    }

    $updateDescription = (isset($DESC[$categorykey])) ? ', shortdescription =' .$class->oDbCon->paramString($DESC[$categorykey]) : '';
    
    $sql = 'update 
                item 
            set 
                sellingprice = '.$class->oDbCon->paramString($class->unFormatNumber($price)).',
                length = 30, width = 20, height = 10,
                weightunitkey = 2,
                gramasi = '.$class->oDbCon->paramString($GRAMASI[$categorykey]).' 
                '.$updateDescription.'                     
            where pkey = ' . $class->oDbCon->paramString($itemkey);

    
     //$class->setLog($itemkey.', '.$sql,true);
     $class->oDbCon->execute($sql); 
     $class->oDbCon->endTrans();

     $marketplace->updateProductsPriceInAllMarketplace($itemkey);  
  
}
?>