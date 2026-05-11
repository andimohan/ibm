<?php
	 
include '../../_config.php';  
include '../../_include.php';
include '_global.php';

$obj= $item;
$securityObject = 'reportItem'; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
$hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);  


$arrWarehouse = $class->convertForCombobox($warehouse->searchData($warehouse->tableName.'.statuskey',1,true,'order by name asc'),'pkey','name');
$arrStatus = $class->convertForCombobox($obj->getAllStatus(),'pkey','status');  
$arrCategory = $class->convertForCombobox($itemCategory->searchData($itemCategory->tableName.'.statuskey',1,true, ' and '.$itemCategory->tableName.'.isleaf = 1', ' order by name asc'),'pkey','name');   
$arrBrand = $class->convertForCombobox($brand->searchData('','',true, ' and '.$brand->tableName.'.statuskey = 1','order by name asc'),'pkey','name'); 
$arrOLShop = array('tk'=>'Tokopedia','bl' => 'BukaLapak','sh' => 'Shopee');

$arrFilter = array();
$rsItemFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey','1',true);
 
for($i=0;$i<count($rsItemFilterCategory);$i++){
     $rsFilter = $itemFilter->searchData('categorykey',$rsItemFilterCategory[$i]['pkey'],true, ' and '.$itemFilter->tableName.'.statuskey = 1'); 
     $arrFilter[$rsItemFilterCategory[$i]['name']] = $class->convertForCombobox($rsFilter,'pkey','name') ;  
}  
 
$arrFilterInformation = array();    
	
if (isset($_POST) && !empty($_POST['hidAction'])){  
		
	$criteria = '';
	if(isset($_POST) && !empty($_POST['itemCode'])) {
		$criteria .= ' AND '.$obj->tableName.'.code LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemCode'].'%').')';
		array_push($arrFilterInformation,array("label" => 'Kode', 'filter' => $_POST['itemCode']));
	}
	if(isset($_POST) && !empty($_POST['itemName'])) {
		$criteria .= ' AND '.$obj->tableName.'.name LIKE ('.$class->oDbCon->paramString('%'.$_POST['itemName'].'%').')'; 
		array_push($arrFilterInformation,array("label" => 'Nama', 'filter' => $_POST['itemName']));
	}
	if(isset($_POST) && !empty($_POST['selCategory'])) { 
         
        $key = implode(",", $class->oDbCon->paramString($_POST['selCategory']));   
        
        $criteria .= ' AND categorykey in('.$key.')';  

        $rsCriteria = $itemCategory->searchData('','',true, ' and '.$itemCategory->tableName.'.pkey in ('.$key.')');
	 
        $arrTempCategory = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempCategory,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempCategory); 
	    array_push($arrFilterInformation,array("label" => 'Kategori', 'filter' => $statusName));
	}
    
    if(isset($_POST) && !empty($_POST['selBrand'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selBrand']));   
        
       	$criteria .= ' AND '.$obj->tableBrand.'.pkey in('.$key.')';  

        $rsCriteria = $brand->searchData('','',true, ' and '.$brand->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Merk', 'filter' => $statusName));
        
	}
    
	if(isset($_POST) && !empty($_POST['selWarehouse'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selWarehouse']));   
        
       	$criteria .= ' AND warehousekey in('.$key.')';  

        $rsCriteria = $warehouse->searchData('','',true, ' and '.$warehouse->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Gudang', 'filter' => $statusName ));
        
	}	
	if(isset($_POST) && !empty($_POST['selFilter'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selFilter']));     
        
        $rsFilterCategory = $filterCategory->searchData($filterCategory->tableName.'.statuskey',1);
 
        $arrIntersect = array();
        for ($i=0;$i<count($rsFilterCategory);$i++){
            $rsFilter = $itemFilter->searchData($itemFilter->tableName.'.statuskey',1,true, ' and categorykey = ' . $obj->oDbCon->paramString($rsFilterCategory[$i]['pkey']));
            $rsFilterKey = array_column($rsFilter, 'pkey');
            $rsIntersectFilterKey = array_intersect($rsFilterKey,$_POST['selFilter']);

            if(empty($rsIntersectFilterKey))
                continue;

            $rsFilteredItem = $itemFilter->getItemInFilter($rsIntersectFilterKey); 
            $rsFilteredItemKey = array_column($rsFilteredItem, 'itemkey'); 


            if (empty($arrIntersect)) 
                $arrIntersect = $rsFilteredItemKey;
             else 
                $arrIntersect = array_intersect($arrIntersect,$rsFilteredItemKey);

        }
   
        $criteria .=  ' and '.$obj->tableName.'.pkey in ('.implode(',',$arrIntersect).')'; 
        
        $rsCriteria = $itemFilter->searchData('','',true, ' and '.$itemFilter->tableName.'.pkey in ('.$key.')');
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['name']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Filter', 'filter' => $statusName ));
        
	}	
 
	if(isset($_POST) && !empty($_POST['selStatus'])) { 
        
        $key = implode(",", $class->oDbCon->paramString($_POST['selStatus']));   
        
       	$criteria .= ' AND '.$obj->tableName.'.statuskey in('.$key.')';  

        $rsCriteria =  $obj->getStatusById ($key);
	 
        $arrTempStatus = array();
		for ($k=0;$k<count($rsCriteria);$k++)
		 	array_push($arrTempStatus,$rsCriteria[$k]['status']);
			
		$statusName = implode(", ",$arrTempStatus); 
	    array_push($arrFilterInformation,array("label" => 'Status', 'filter' => $statusName));
        
	}
    
    $OLShopKey = 0;
    if(isset($_POST) && !empty($_POST['selOLShop'])) {  
        $OLShopKey = $_POST['selOLShop'];
	    array_push($arrFilterInformation,array("label" => 'Online Shop', 'filter' => $arrOLShop[$OLShopKey] )); 
	}	
    
    $OLCategorykey = '';
    if(isset($_POST) && !empty($_POST['onlineCategoryKey'])) {  
        $OLCategorykey = $_POST['onlineCategoryKey'];
	    array_push($arrFilterInformation,array("label" => 'Kategori Online Shop', 'filter' =>  $OLCategorykey )); 
	}	
    
    $OLCatalogName = '';
    if(isset($_POST) && !empty($_POST['onlineCatalogName'])) {  
        $OLCatalogName = $_POST['onlineCatalogName'];
	    array_push($arrFilterInformation,array("label" => 'Katalog Online Shop', 'filter' =>  $OLCatalogName )); 
	}	
    
    $OLStockDefault = 0; 
    if(isset($_POST) && !empty($_POST['stockDefault'])) {  
        $OLStockDefault = $_POST['stockDefault'];
	    array_push($arrFilterInformation,array("label" => 'Stock Default', 'filter' =>  $OLStockDefault )); 
	}	 
        
    $OLGramasiDefault = 0; 
    if(isset($_POST) && !empty($_POST['gramasiDefault'])) {  
        $OLGramasiDefault = $_POST['gramasiDefault'];
	    array_push($arrFilterInformation,array("label" => 'Gramasi Default', 'filter' =>  $OLGramasiDefault )); 
	}	
    
        
    $groupCriteria = ''; 
	if(isset($_POST) && !empty($_POST['chkAvailable'])){ 
        $groupCriteria = 'having qtyonhand > 0';
    }
      
    $orderBy = (!empty($_POST['hidOrderBy'])) ? $obj->oDbCon->paramOrder($_POST['hidOrderBy']) : 'pkey'; // order by harus dr kolom yg terdaftar saja
    $orderType = (isset($_POST['hidOrderType']) && !empty($_POST['hidOrderType']) && $_POST['hidOrderType'] == 1) ? 'desc' : 'asc';
     
	$order = 'order by '.$orderBy.' ' .$orderType;
     
    $tempreport = '';  
    
 	$rs = $obj->searchData('','',true,$criteria,$order,'',$groupCriteria); 
    
    $totalimage = 0;
    $totaldesc = 0;
    for( $i=0;$i<count($rs);$i++) {  
            // DESC
            $rsDescription = $obj->getItemDescription($rs[$i]['pkey']); 
	        $desc = array(); 
            if (!empty($rs[$i]['shortdescription']))
                array_push($desc,$rs[$i]['shortdescription']); 
            else if (!empty($rsDescription)) { 
                for($k=0;$k<count($rsDescription);$k++)
                     array_push($desc, strip_tags($rsDescription[$k]['value'] ));  
            }
        
            $rs[$i]['desc'] = $desc;
         
            $totaldesc = (count($desc) > $totaldesc)  ? count($desc)  : $totaldesc ;
                
            // IMAGE
            $imglink = array();  
            $rsItemImage = $obj->getItemImage($rs[$i]['pkey']);  
            for($k=0;$k<count($rsItemImage);$k++)  
                array_push($imglink,HTTP_HOST. 'phpthumb/phpThumb.php?far=C&hash=' . getPHPThumbHash($rsItemImage[$k]['file']).'&src='.$obj->phpThumbURLSrc .$obj->uploadFolder.$rs[$i]['pkey'].'/'.$rsItemImage[$k]['file']); 
             
            $rs[$i]['imagelink'] = $imglink;
        
            $totalimage = (count($rsItemImage) > $totalimage)  ? count($rsItemImage)  : $totalimage ;
        
    }

    
    $OLParam = array (
                      'totalImage' => $totalimage, 
                      'totalDesc' => $totaldesc, 
                      'OLCategoryKey' => $OLCategorykey, 
                      'OLCatalogName' => $OLCatalogName, 
                      'OLStockDefault' => $OLStockDefault, 
                      'OLGramasiDefault' => $OLGramasiDefault
                     );
    
    $dataset = generateReport($OLShopKey,$rs,$OLParam); 
    
    $rs = $dataset['detail'];
     
    for( $i=0;$i<count($rs);$i++) {   

        $temptablerow  = '<tr class="rewrite-row"> ';
        
        for ($k=0;$k<count($rs[$i]);$k++) 
                 $temptablerow  .= '<td>'.$rs[$i][$k].'</td>'; 
       
        $temptablerow  .= '</tr>';
 
        $temptablerow .= '<tr class="detail-row rewrite-row"><td colspan="'.count($rs[$i]).'"></td></tr>';
        $tempreport .= $temptablerow; 
 
    } 

    $report =  '<table class="rewrite-row" style="max-width:none !important; width:auto;"  >';
    $report .= ' <tr class="table-header-gray"> ';

    for ($k=0;$k<count($dataset['col']);$k++){
        $report .= '<td style="width:40px;">'.$dataset['col'][$k].'</td>';
    } 

    $report .= ' </tr>'; 
    $report .= $tempreport;
    $report .= '</table>';

	$reportResult = array(); 
	 
	$reportResult['filterInformation'] = $arrFilterInformation;  
 	$reportResult['content'] = $report;  
 	echo json_encode($reportResult);
	die;
}


$arrTwigVar['inputItemCode'] =  $class->input('text','itemCode');  
$arrTwigVar['inputHidItemKey'] = $class->input('hidden','hidItemKey');
$arrTwigVar['inputItemName'] =  $class->input('text','itemName');
$arrTwigVar['inputSelCategory'] =  $class->inputSelect('selCategory[]', $arrCategory,true,0,"multiple=\"multiple\"","multi-selectbox");
$arrTwigVar['inputSelStatus'] =  $class->inputSelect('selStatus[]', $arrStatus,true,0,"multiple=\"multiple\"","multi-selectbox");
$arrTwigVar['inputSelBrand'] =  $class->inputSelect('selBrand[]', $arrBrand,true,0,"multiple=\"multiple\"","multi-selectbox");
$arrTwigVar['inputSelWarehouse'] =  $class->inputSelect('selWarehouse[]', $arrWarehouse,true,0,"multiple=\"multiple\"","multi-selectbox");
$arrTwigVar['inputSelFilter'] =  $class->inputSelect('selFilter[]', $arrFilter,true,0,"multiple=\"multiple\"","multi-selectbox");
$arrTwigVar['inputChkAvailable'] =  $class->input('checkbox','chkAvailable',false,'1','','no-class');

$arrTwigVar['inputOLCategoryKey'] =  $class->input('text','onlineCategoryKey');
$arrTwigVar['inputOLCatalogName'] =  $class->input('text','onlineCatalogName');
$arrTwigVar['inputOLShop'] =  $class->inputSelect('selOLShop', $arrOLShop,true);
$arrTwigVar['inputOLStockDefault'] = $class->input('text','stockDefault', true, '5');
$arrTwigVar['inputOLGramasiDefault'] = $class->input('text','gramasiDefault', true, '1000');
      
$arrTwigVar['autoLoad'] =  0;

echo $twig->render('reportItemForMassUpload.html', $arrTwigVar);  
 

function generateReport($olShopKey,$rs,$OLParam){
    
    $col = array();
    $detail = array();
    $data = array();
    
    	switch ($olShopKey) {
				case 'sh':  array_push($col,'ps_category_list_id', 
                                             'ps_product_name', 
                                             'ps_product_description',
                                             'ps_price', 
                                             'ps_stock',
                                             'ps_product_weight',
                                             'ps_days_to_ship');
                 
                
                            for($i=0;$i<count($OLParam['totalImage']);$i++)
                                    array_push($col,'ps_img_' .  ($i+1));
                
                            for($i=0;$i<count($rs);$i++){
                                $row = array();
                                $desc = (count($rs[$i]['desc']) != 0 ) ? $rs[$i]['desc'][0] : '';
                                
                                $gramasi = ($rs[$i]['gramasi'] > 0 ) ? $rs[$i]['gramasi'] : $OLParam['OLGramasiDefault'];
                                $stock = ($rs[$i]['qtyonhand'] > 0 ) ? $rs[$i]['qtyonhand'] : $OLParam['OLStockDefault'];
                                
                                array_push($row,$OLParam['OLCategoryKey'], 
                                                    $rs[$i]['name'],  
                                                    $desc,  
                                                    $rs[$i]['sellingprice'],  
                                                    $stock,  
                                                    $gramasi,
                                                    '2');
                                
                                for($k=0;$k<count($OLParam['totalImage']);$k++){ 
                                    $imagelink = (isset($rs[$i]['imagelink'][$k])) ? $rs[$i]['imagelink'][$k] : '';
                                    array_push($row,$imagelink);
                                }
                
                                array_push($detail,$row);
                            }
      
    
                            break;
                
				case 'tk':  array_push($col,'Nama Produk', 
                                             'Kategori', 
                                             'Deskripsi Produk',
                                             'Harga (Dalam Rupiah)', 
                                             'Berat (Dalam Gram)',
                                             'Pemesanan Minimum',
                                             'Status',
                                             'Jumlah Stok',
                                             'Etalase',
                                             'Kondisi' 
                                      );
                 
                
                            for($i=0;$i<count($OLParam['totalImage']);$i++)
                                    array_push($col,'Gambar ' .  ($i+1));
                
                            for($i=0;$i<count($rs);$i++){
                                $row = array();
                                $desc = (count($rs[$i]['desc']) != 0 ) ? $rs[$i]['desc'][0] : '';
                                
                                $gramasi = ($rs[$i]['gramasi'] > 0 ) ? $rs[$i]['gramasi'] : $OLParam['OLGramasiDefault'];
                                $stock = ($rs[$i]['qtyonhand'] > 0 ) ? $rs[$i]['qtyonhand'] : $OLParam['OLStockDefault'];
                                
                                array_push($row,    $rs[$i]['name'],  
                                                    $OLParam['OLCategoryKey'],
                                                    $desc,  
                                                    $rs[$i]['sellingprice'],  
                                                    $gramasi,
                                                    '1',
                                                    'Stok Tersedia',
                                                    $stock,  
                                                    $OLParam['OLCatalogName'], 
                                                    'Baru');
                                
                                for($k=0;$k<count($OLParam['totalImage']);$k++){ 
                                    $imagelink = (isset($rs[$i]['imagelink'][$k])) ? $rs[$i]['imagelink'][$k] : '';
                                    array_push($row,$imagelink);
                                }
                
                                array_push($detail,$row);
                            }
      
    
                            break;
        }
    
    $data['col'] = $col;
    $data['detail'] = $detail;
    
    return $data;
}
?>