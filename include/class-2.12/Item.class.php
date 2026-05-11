<?php 
class Item extends BaseClass{
 
   function __construct($itemType = 1){
		
		parent::__construct();
       
		$this->tableName = 'item';
		$this->tableCategory = 'item_category';
	    $this->tableLangValue = 'item_lang';
		$this->tableItemUnit = 'item_unit'; 
        $this->tableItemCondition = 'item_condition';
		$this->tableDescription = 'item_description';
        $this->tableFilter = 'filter_header';
		$this->tableFilterDetail = 'filter_detail';
		$this->tableBrand= 'brand';
		$this->tableItemMovement = 'item_movement';
		$this->tableStatus = 'master_status';
		$this->tableImage = 'item_image';
		$this->tableWarehouse= 'warehouse';
		$this->tableItemInWarehouse= 'item_in_warehouse';
		$this->tableImageVariant = 'item_image_variant';
		$this->tableFile = 'item_file';
        $this->tableItemChecklist = 'item_checklist';
        $this->tableItemContentOfPackage = 'item_content_of_package_detail';
        $this->tableItemSpecificationDetail = 'item_specification_detail';
        $this->tableItemSpecification= 'item_specification';
		$this->tableUnitConversion = 'item_unit_conversion'; 
        $this->tableItemGroup = 'item_category_group';
        $this->tableVendorPartNumber = 'item_vendor_part_number';
        $this->tableSerialNumber = 'item_sn'; 
        $this->tableSerialNumberMovement = 'item_sn_movement';
        $this->tableItemDepotMovement = 'item_depot_movement';
        $this->tableItemSyncMarketplaceDetail = 'item_marketplace_sync_detail';
        $this->tableItemMarketplaceVariant = 'item_marketplace_variant';
	    $this->tableItemMarketplaceLink = 'item_marketplace_link';
        //$this->tableDivision = 'division'; 
        $this->tableWarrantyPeriod = 'warranty_period';
        $this->tableMarketplaceCategoryAttributes = 'item_category_marketplace_attributes';
        $this->tableMarketplaceLogisticsDetail = 'item_marketplace_logistics';
		$this->tableMarketplaceLogistics = 'marketplace_logistics'; 
        $this->tableCustomer = 'customer';
        $this->tableSupplier = 'supplier';
        $this->tableTimeUnit = 'time_unit'; 
	    $this->tableDetailTime = 'item_detail_time';
	    $this->tableDetailVideo = 'item_detail_video';
        $this->tableFavoritProduct = 'item_favorit';
        $this->tableReview = 'item_review';
        $this->tableSalesOrder = 'sales_order_header';
        $this->tableSalesOrderDetail = 'sales_order_detail';
        $this->tableItemUnitGroup = 'item_group';

        $this->tableItemModelDetail = 'item_model_detail';
        $this->tableItemTextureDetail = 'item_texture_detail';
        $this->tableModel = 'model';
        $this->tableColor = 'color';
        $this->tableCharacter = 'item_character';
        $this->tableMaterial = 'material';
        $this->tableTexture = 'texture';
        $this->tableRingSize = 'ring_size';
        $this->tablePlating = 'plating';
        $this->tableItemSKUAlias = 'item_sku_alias';
        $this->tableItemColorDetail = 'item_color_detail';
        $this->tableItemCharacterDetail = 'item_character_detail';
        $this->tableSparePartType = 'sparepart_type';
        
        $this->tableItemVariationDetail = 'item_variation_detail';
        $this->tableItemVariation = 'item_variation';
        $this->tableAge = 'age';
            
            
		$this->uploadFolder = 'item/';
		$this->uploadFileFolder = 'item-file/';
		$this->uploadVariantFolder = 'item-variant/';
	 
        $this->importUrl = 'import/item';
       
        $this->itemType = (is_array($itemType)) ? implode(",",$itemType) : $itemType; 
        $this->securityObject = 'Item';    
       
		$this->cogsSecurityObject = 'COGS';
		 
		$this->warehouseCriteria = ''; 
		//$this->mj = new Mailjet(); 
		//$this->list_id = '771591';
		//$this->list_id = '172434'; // development
		$this->fromemail = 'admin@domain.com';
		$this->fromname = '';
		$this->unsubscribetext = 'Klik link ini jika Anda ingin berhenti berlangganan newsletter ini.';
         
        $this->arrUnitConversion = array(); 
        $this->arrUnitConversion['pkey'] = array('hidConversionDetailKey');
        $this->arrUnitConversion['refkey'] = array('pkey', 'ref');
        $this->arrUnitConversion['baseunitkey'] = array('conversionBaseUnitKey',array('mandatory'=>true));
        $this->arrUnitConversion['conversionunitkey'] = array('selConversionUnitKey',array('datatype'=>'number','mandatory'=>true));
        $this->arrUnitConversion['conversionmultiplier'] = array('txtConversionMultiplier',array('datatype'=>'number','mandatory'=>true));
        $this->arrUnitConversion['sellingprice'] = array('unitSellingPrice','number');
          
        $this->arrMarketplaceAttributes = array(); 
        $this->arrMarketplaceAttributes['pkey'] = array('hidAttributeDetailKey');
        $this->arrMarketplaceAttributes['refkey'] = array('pkey', 'ref');
        $this->arrMarketplaceAttributes['marketplacekey'] = array('hidMarketplaceKey');
        $this->arrMarketplaceAttributes['attributekey'] = array('hidCategoryAttributeKey'); 
        $this->arrMarketplaceAttributes['valueid'] = array('attributeValueId');
        $this->arrMarketplaceAttributes['value'] = array('attributeValue');
        
        $this->arrMarketplaceLogistics = array(); 
        $this->arrMarketplaceLogistics['pkey'] = array('hidLogisticsMarketplaceDetailKey');
        $this->arrMarketplaceLogistics['refkey'] = array('pkey', 'ref');
        $this->arrMarketplaceLogistics['marketplacekey'] = array('hidLogisticsMarketplaceKey');
        $this->arrMarketplaceLogistics['reflogistickey'] = array('hidLogisticsRefKey',array('mandatory'=>true));  
	   
        $this->arrMarketplaceVariant = array(); 
        $this->arrMarketplaceVariant['pkey'] = array('hidVariantDetailKey');
        $this->arrMarketplaceVariant['refkey'] = array('pkey', 'ref');
        $this->arrMarketplaceVariant['marketplacekey'] = array('hidVariantMarketplaceKey');
        $this->arrMarketplaceVariant['variantkey'] = array('selVariant'); 
        $this->arrMarketplaceVariant['optionvalue'] = array('selOption');
	   
	    $this->arrTimeConversion = array(); 
        $this->arrTimeConversion['pkey'] = array('hidDetailTimeKey');
        $this->arrTimeConversion['refkey'] = array('pkey', 'ref');
        $this->arrTimeConversion['timeunitkey'] = array('selTimeUnitKey',array('mandatory'=>true));
        $this->arrTimeConversion['sellingprice'] = array('unitSellingPrice','number');
       
        $this->arrItemVideo = array(); 
        $this->arrItemVideo['pkey'] = array('hidDetailVideoKey');
        $this->arrItemVideo['refkey'] = array('pkey', 'ref');
        $this->arrItemVideo['url'] = array('url',array('mandatory'=>true));
       
	    $this->arrMarketplaceSync = array(); 
        $this->arrMarketplaceSync['pkey'] = array('hidMarketplaceSyncDetailKey');
        $this->arrMarketplaceSync['refkey'] = array('pkey', 'ref');
        $this->arrMarketplaceSync['marketplacekey'] = array('hidSyncMarketplaceKey'); 
        $this->arrMarketplaceSync['issync'] = array('chkSyncToMarketplace');
        $this->arrMarketplaceSync['name'] = array('marketplaceItemName');
        $this->arrMarketplaceSync['shortdescription'] = array('marketplaceItemShortDescription');
	    

        $this->arrItemModelDetail = array();
        $this->arrItemModelDetail['pkey'] = array('hidModelDetailKey');
        $this->arrItemModelDetail['refkey'] = array('pkey', 'ref');
        $this->arrItemModelDetail['modelkey'] = array('hidModelKey');

        $this->arrItemTextureDetail = array();
        $this->arrItemTextureDetail['pkey'] = array('hidTextureDetailKey');
        $this->arrItemTextureDetail['refkey'] = array('pkey', 'ref');
        $this->arrItemTextureDetail['texturekey'] = array('hidTextureKey');
        
        $this->arrItemSKUALiasDetail = array();
        $this->arrItemSKUALiasDetail['pkey'] = array('hidSKUAliasDetailKey');
        $this->arrItemSKUALiasDetail['refkey'] = array('pkey', 'ref');
        $this->arrItemSKUALiasDetail['skualias'] = array('skuAlias');

        $this->arrItemColorDetail = array();
        $this->arrItemColorDetail['pkey'] = array('hidColorDetailKey');
        $this->arrItemColorDetail['refkey'] = array('pkey', 'ref');
        $this->arrItemColorDetail['colorkey'] = array('hidColorKey');

        $this->arrItemCharacterDetail = array();
        $this->arrItemCharacterDetail['pkey'] = array('hidCharacterDetailKey');
        $this->arrItemCharacterDetail['refkey'] = array('pkey', 'ref');
        $this->arrItemCharacterDetail['characterkey'] = array('hidCharacterKey');
       
        $this->arrItemVariationDetail = array();
        $this->arrItemVariationDetail['pkey'] = array('hidVariationDetailKey');
        $this->arrItemVariationDetail['refkey'] = array('pkey', 'ref');
        $this->arrItemVariationDetail['variationkey'] = array('hidVariationKey');
       
	    $this->activeModule = $this->isActiveModule(array('ItemChecklist','DiscountScheme'));
        $this->allowedStatusForEdit = array(1,2);
       
        $this->useStorage = $this->useStorage('S3');
       
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
        array_push($arrDetails, array('dataset' => $this->arrUnitConversion, 'tableName' => $this->tableUnitConversion));
        array_push($arrDetails, array('dataset' => $this->arrMarketplaceAttributes, 'tableName' => $this->tableMarketplaceCategoryAttributes));
        array_push($arrDetails, array('dataset' => $this->arrMarketplaceLogistics, 'tableName' => $this->tableMarketplaceLogisticsDetail));
  	    array_push($arrDetails, array('dataset' => $this->arrMarketplaceVariant, 'tableName' => $this->tableItemMarketplaceVariant));
        array_push($arrDetails, array('dataset' => $this->arrTimeConversion, 'tableName' => $this->tableDetailTime));
        array_push($arrDetails, array('dataset' => $this->arrItemVideo, 'tableName' => $this->tableDetailVideo));      
	    array_push($arrDetails, array('dataset' => $this->arrMarketplaceSync, 'tableName' => $this->tableItemSyncMarketplaceDetail));   

        if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['jewelry']))){  
            array_push($arrDetails, array('dataset' => $this->arrItemModelDetail, 'tableName' => $this->tableItemModelDetail));      
            array_push($arrDetails, array('dataset' => $this->arrItemTextureDetail, 'tableName' => $this->tableItemTextureDetail));      
            array_push($arrDetails, array('dataset' => $this->arrItemSKUALiasDetail, 'tableName' => $this->tableItemSKUAlias));      
            array_push($arrDetails, array('dataset' => $this->arrItemColorDetail, 'tableName' => $this->tableItemColorDetail));         
	        array_push($arrDetails, array('dataset' => $this->arrItemCharacterDetail, 'tableName' => $this->tableItemCharacterDetail));    
	        array_push($arrDetails, array('dataset' => $this->arrItemVariationDetail, 'tableName' => $this->tableItemVariationDetail));        
        }
       
              
        if($this->useStorage){ 
            
            $this->arrDataImageDetail = array();  
            $this->arrDataImageDetail['pkey'] = array('hidDetailImageKey');
            $this->arrDataImageDetail['refkey'] = array('pkey','ref');
            $this->arrDataImageDetail['file'] = array('imageDetail',array('datatype' => 'image','uploadFolder' => $this->uploadFolder));
            
            array_push($arrDetails, array('dataset' => $this->arrDataImageDetail, 'tableName' => $this->tableImage));
        
            
            $this->arrDataFileDetail = array();  
            $this->arrDataFileDetail['pkey'] = array('hidDetailFileKey');
            $this->arrDataFileDetail['refkey'] = array('pkey','ref');
            $this->arrDataFileDetail['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));
            
            array_push($arrDetails, array('dataset' => $this->arrDataFileDetail, 'tableName' => $this->tableFile));
        
        }else{ 
            array_push($arrDetails, array('dataset' => $this->arrDataImage, 'tableName' => $this->tableImage, 
                                          'datatype' => 'image', 'uploadFolder' => $this->uploadFolder,
                                          'token' => 'token-item-image-uploader', 'fileName' => 'item-image-uploader')); 
            
            array_push($arrDetails, array('dataset' => $this->arrDataFile, 'tableName' => $this->tableImage, 
                                          'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
                                          'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader')); 
        }
       
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code');
        $this->arrData['barcode'] = array('barcode');
        $this->arrData['name'] = array('name');
        $this->arrData['categorykey'] = array('hidCategoryKey'); 
        $this->arrData['statuskey'] = array('selStatus');  
        $this->arrData['conditionkey'] = array('selCondition');  
        $this->arrData['sellingprice'] = array('sellingPrice',array('datatype' => 'number', 'emptyErrMsg' => $this->errorMsg['sellingPrice'][2]));
        $this->arrData['rentprice'] = array('rentPrice','number');
        $this->arrData['lostprice'] = array('lostPrice','number');
        $this->arrData['baseunitkey'] = array('selBaseUnitKey');
        $this->arrData['deftransunitkey'] = array('selDefaultTransUnitKey');
        $this->arrData['timeunitkey'] = array('selTimeUnit');
        $this->arrData['minstockqty'] = array('minStockQty','number');
        $this->arrData['maxstockqty'] = array('maxStockQty','number');
        $this->arrData['gramasi'] = array('gramasi',array('datatype' => 'number', 'emptyErrMsg' => $this->errorMsg['gramasi'][3]));
        $this->arrData['weightunitkey'] = array('selWeightUnit'); 
        $this->arrData['shortdescription'] = array('shortdescription',array('emptyErrMsg' => $this->errorMsg['item'][5]));
        $this->arrData['brandkey'] = array('hidBrandKey',array('emptyErrMsg' => $this->errorMsg['brand'][1])); 
        $this->arrData['tag'] = array('tag');
        //$this->arrData['groupkey'] = array('groupkey');
        $this->arrData['publish'] = array('chkIsPublish');
        $this->arrData['needsn'] = array('chkNeedSN');
        $this->arrData['vendorpartnumbercache'] = array('vendorpartnumbercache');
        $this->arrData['itemtype'] = array('itemType'); 
        $this->arrData['mileage'] = array('mileage', 'number');
        $this->arrData['lifespan'] = array('lifespan', 'number');
        //$this->arrData['contentofpackagekey'] = array('hidContentOfPackageKey');
        //$this->arrData['specificationkey'] = array('hidSpecificationKey');
        $this->arrData['oiltypekey'] = array('selOilType'); 
        $this->arrData['commissiontype'] = array('selCommissionType');
        $this->arrData['commission'] = array('commissionValue','number');
        $this->arrData['inventorycoakey'] = array('hidInventoryCOAKey');
        $this->arrData['inventorytempcoakey'] = array('hidInventoryTempCOAKey');
        $this->arrData['revenuecoakey'] = array('hidRevenueCOAKey');
        $this->arrData['costcoakey'] = array('hidCostCOAKey');
        $this->arrData['cashbacktype'] = array('selCashBackType');
        $this->arrData['cashback'] = array('cashBackValue','number');
        $this->arrData['pointvalue'] = array('pointValue','number');
        $this->arrData['warrantyperiodkey'] = array('selWarranty');
        $this->arrData['warrantyvendorperiodkey'] = array('selWarrantyVendor');
        $this->arrData['syncallmarketplace'] = array('chkSyncAllMarketplace');
       
        $this->arrData['width'] = array('width',array('datatype' => 'number', 'emptyErrMsg' => $this->errorMsg['width'][2]));
        $this->arrData['length'] = array('length',array('datatype' => 'number', 'emptyErrMsg' => $this->errorMsg['length'][2]));
        $this->arrData['height'] = array('height',array('datatype' => 'number', 'emptyErrMsg' => $this->errorMsg['height'][2]));
        $this->arrData['divisionkey'] = array('selDivisionKey');
        $this->arrData['isrental'] = array('chkIsRental');
        $this->arrData['isvariant'] = array('chkIsVariant');
       	$this->arrData['parentkey'] = array('hidParentItemKey'); 
       	$this->arrData['isprimary'] = array('chkIsPrimary'); 
       	$this->arrData['metatitle'] = array('metaTitle'); 
       	$this->arrData['metadescription'] = array('metaDescription'); 
       	$this->arrData['isperiodically'] = array('chkIsPeriodically'); 
       	$this->arrData['marginpercentage'] = array('marginPercentage','number'); 
       	$this->arrData['orderlist'] = array('orderList','number'); 
        $this->arrData['carat'] = array('carat','number'); 
       	$this->arrData['pricingcategorykey'] = array('selPricingCategory'); 
        $this->arrData['additionalprice'] = array('additionalPrice','number');
            
        $this->arrData['platingkey'] = array('hidPlatingKey'); 
        $this->arrData['materialkey'] = array('hidMaterialKey');
        $this->arrData['ringsizekey'] = array('hidRingSizeKey'); 
        $this->arrData['size'] = array('size', 'number');
        $this->arrData['sizeunitkey'] = array('selSizeUnitKey');
        $this->arrData['aliasname'] = array('aliasName');
        $this->arrData['taxalias'] = array('taxAlias');
        $this->arrData['isweightfixed'] = array('chkWeightFixed');
        $this->arrData['agekey'] = array('hidAgeKey');
        $this->arrData['sexkey'] = array('selSexKey');
        $this->arrData['purity'] = array('purity', 'number');
        $this->arrData['mililiter'] = array('mililiter', 'number');
        $this->arrData['alcoholcontent'] = array('alcoholContent', 'number');
        // $this->arrData['sexkey'] = array('hidBrand');
        
        /* 
        $this->arrData['parentkey'] = array('hidParentKey'); 
        $this->arrData['codevariant'] = array('codeVariant'); 
        $this->arrData['variantorder'] = array('variantOrder'); 
        */
       
        $this->arrLockedTable = array();
        $defaultFieldName = 'itemkey'; 
        array_push($this->arrLockedTable, array('table'=>'item','field'=>'parentkey')); 
        array_push($this->arrLockedTable, array('table'=>'item_movement','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'service_work_order','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'trucking_selling_rate_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'warehouse_transfer_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'email_blast','field'=>$defaultFieldName)); 
        //array_push($this->arrLockedTable, array('table'=>'filter_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_adjustment_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_in_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_out_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_in_warehouse','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_movement_po','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_promo_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'preorder_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'preorder_item','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'purchase_order_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'purchase_receive_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'purchase_return_detail','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'sales_order_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_delivery_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'sales_order_car_service_detail','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'sales_return_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'service_order_detail','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'cart_temp','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'item_package_detail','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'item_sn','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'emkl_order_invoice_item_detail','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'emkl_purchase_order_detail_item','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'emkl_job_order_detail_item','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_detail','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_cost','field'=>$defaultFieldName));   
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_header_cost','field'=>'costkey'));   
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_invoice_item_detail','field'=>$defaultFieldName));   
   
// khusus TPA
//		array_push($this->arrLockedTable, array('table'=>'medical_request_claim_detail','field'=>$defaultFieldName));   
//      array_push($this->arrLockedTable, array('table'=>'medical_job_order_detail','field'=>$defaultFieldName));   
             
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'barcode','title' => 'barcode','dbfield' => 'barcode', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'brand','title' => 'brand','dbfield' => 'brandname','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'weight','title' => 'weight','dbfield' => 'gramasi', 'width' => 100,'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'weightUnit','title' => 'unit','dbfield' => 'weightunitname', 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'price','title' => 'price','dbfield' => 'sellingprice','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'integer'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'qty','title' => 'qty','dbfield' => 'qtyonhand','default'=>true, 'width' => 70, 'align' => 'right', 'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'qor','title' => 'qor','dbfield' => 'qtyonreserve', 'width' => 70, 'align' => 'right', 'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'qtyInUnit','title' => 'qtyInUnit','dbfield' => 'qtyonhand', 'width' => 120, 'align' => 'right', 'format' => function ($row,$obj){ return $obj->splitQtyBaseOnUnit($row['pkey'], $row['qtyonhand']); } ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'qtyUnit','title' => 'unit','dbfield' => 'baseunitname','default'=>true, 'width' => 70 ));  
        array_push($this->arrDataListAvailableColumn, array('code' => 'shortDescription','title' => 'shortDescription','dbfield' => 'shortdescription', 'width' => 250 ));  
	    array_push($this->arrDataListAvailableColumn, array('code' => 'tag','title' => 'tag','dbfield' => 'tag',  'width' => 1150 ));   
        array_push($this->arrDataListAvailableColumn, array('code' => 'purity','title' => 'purity','dbfield' => 'carat',  'width' => 100, 'align' => 'right','format' => 'number' ));   
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
          

		$this->arrSearchColumn = array ();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Barcode', $this->tableName . '.barcode'));
		array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
		array_push($this->arrSearchColumn, array('Tag', $this->tableName . '.tag'));
		array_push($this->arrSearchColumn, array('Deskripsi Singkat', $this->tableName . '.shortdescription'));
		array_push($this->arrSearchColumn, array('Merk', $this->tableBrand. '.name') );
		array_push($this->arrSearchColumn, array('Kategori', $this->tableCategory. '.name') );
		array_push($this->arrSearchColumn, array('VendorPart Number', $this->tableName. '.vendorpartnumbercache') );


        $this->includeClassDependencies(array(
              'ItemUnit.class.php', 
              'Warehouse.class.php', 
              'ItemMovement.class.php', 
              'Marketplace.class.php',  
              'ItemCondition.class.php',  
              'Category.class.php',  
              'ItemCategory.class.php',  
              'ChartOfAccount.class.php',  
              'Brand.class.php', 
              'Terminal.class.php',
              'Depot.class.php',
              'TimeUnit.class.php',
			  'ItemSpecification.class.php',
              'PriceUpdate.class.php',
              'SalesOrder.class.php',
              'Color.class.php',
              'Plating.class.php',
              'Material.class.php',
              'Texture.class.php',
              'Model.class.php',
              'RingSize.class.php',
              'Character.class.php',
              'ItemVariation.class.php',
              'Age.class.php',
              'Car.class.php'
        ));
	   
	   	if($this->activeModule['itemchecklist']){
			$this->includeClassDependencies(array( 
              'ItemChecklist.class.php', 
			  'ItemChecklistGroup.class.php', 
			));
		}
	   
	   	if($this->activeModule['discountscheme']){
			$this->includeClassDependencies(array(  
			  'DiscountScheme.class.php', 
			));
		}
	   
        $this->isActiveMarketplace = $this->isActiveModule('marketplace');
	  
        $this->actionMenu = array();   
        if($this->isActiveMarketplace){ 
            $function = '  
                    var phpDataListFile = tabParam[selectedTabId].phpDataListFile; 

                    if (selectedPkey.length == 0){
                        showMsgDialog ("Anda belum memilih data yang hendak di sinkronisasikan."); 
                        break ;
                    }

                    var msg =  "Anda yakin akan melakukan sinkronisasi marketplace ?";

                    $( "#dialog-message" ).html(msg);
                    $( "#dialog-message" ).dialog({
                      width: 300,
                      modal: true,
                      title:"Konfirmasi Sinkronisasi", 
                      open: function() {
                          $(this).closest(\'.ui-dialog\').find(\'.ui-dialog-buttonpane button:last\').focus();
                      },
                      buttons : {
                          OK : function (){
                                    
                                     setRowToLoadingState(selectedTabId, selectedPkey);
                                     
                                     $.ajax({
                                        type: "POST",
                                        url:  phpDataListFile,
                                        data:{action:"resyncmarketplace", 
                                            selectedPkey:selectedPkey
                                        },
                                    }).done(function( data ) {  
                                          generateDataRow(selectedTabId, selectedPkey);  
                                    });  

                                    $( this ).dialog( "close" );
                          },
                          Cancel : function (){ 
                            $( this ).dialog( "close" );
                          }
                      },
                      });
            ';

            array_push($this->actionMenu,array('code' => 'resyncMarketplace', 'name' => $this->lang['syncMarketplace'],  'icon' => 'resync', 'function' => $function)); 

        }
        if($this->loadSetting('duplicateDataItem') == 1){
            $form = 'itemForm';
            $label = $this->lang['itemList'];

            $form = $this->getPersonalizedFiles($form);

            $functionDuplicate ='openTabForShortCutAdd("'.$form.'",{"title" : "'.$label.'"});';
            array_push($this->actionMenu,array('code' => 'itemListDuplicate', 'name' => $this->lang['duplicateData'],  'icon' => 'duplicate', 'function' => $functionDuplicate)); 
        }
        $this->newLoad = true;
       
	    $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'print1DBrcode', 'name' => $this->lang['print']. ' ' .$this->lang['linearBarcode'],  'icon' => 'print', 'url' => 'print/itemBarcode'));
      
		$this->overwriteConfig();
   }
   
   
    
   function getQuery($onlyQOHCount = true){ 
       
       $companyCriteria = $this->getCompanyCriteria($this->tableWarehouse);
       $warehouseCriteria = $this->getWarehouseCriteria($this->tableWarehouse);
       
       $qohCountCriteria = ($onlyQOHCount) ?  'and ' . $this->tableWarehouse.'.isqohcount = 1 ' : '';
       
	   $sql = '
		    select * from (SELECT '.$this->tableName.'.* ,
               '.$this->tableCategory.'.name as categoryname,
               '.$this->tableCategory.'.code as categorycode,
               '.$this->tableItemCondition.'.name as conditionname,
			   '.$this->tableStatus.'.status as statusname,
			   '.$this->tableItemUnit.'.name as baseunitname,
			   '.$this->tableItemUnit.'.code as baseunitcode,
			   '.$this->tableBrand.'.code as brandcode, 
			   '.$this->tableBrand.'.name as brandname, 
			   '.$this->tableWarrantyPeriod.'.name as warrantyperiod,
			   warrantyVendorPeriod.name as warrantyvendorperiod,
               item_weight_unit.name as weightunitname,
               item_weight_unit.code as weightunitcode,
			   coalesce(sum('.$this->tableItemInWarehouse.'.qtyinbaseunit),0) as qtyonhand,
			   coalesce(sum('.$this->tableItemInWarehouse.'.qtyonreserveinbaseunit),0) as qtyonreserve
			FROM '.$this->tableStatus.',  '.$this->tableCategory.' ,'.$this->tableItemUnit.','.$this->tableItemUnit.' item_weight_unit, '.$this->tableName.'
				left join '.$this->tableBrand.' on  '.$this->tableName.'.brandkey = '.$this->tableBrand.'.pkey 
				left join '.$this->tableWarrantyPeriod.' on  '.$this->tableName.'.warrantyperiodkey = '.$this->tableWarrantyPeriod.'.pkey 
				left join '.$this->tableWarrantyPeriod.' warrantyVendorPeriod on  '.$this->tableName.'.warrantyvendorperiodkey = warrantyVendorPeriod.pkey 
				left join '.$this->tableItemCondition.' on '.$this->tableName.'.conditionkey = '.$this->tableItemCondition.'.pkey
				left join
				    (select itemkey, 
                            qtyinbaseunit,
                            qtyonreserveinbaseunit,
                            warehousekey,
                            iswebqoh 
                    from  '.$this->tableItemInWarehouse.',
                          '.$this->tableWarehouse.' 
                    where  
                       '.$this->tableWarehouse.'.pkey = '.$this->tableItemInWarehouse.'.warehousekey 
                       '.$qohCountCriteria.'
                       '.$this->warehouseCriteria.'  '.$companyCriteria.$warehouseCriteria.' ) '.$this->tableItemInWarehouse.' 
                on item.pkey =  '.$this->tableItemInWarehouse.'.itemkey 
				 
		    WHERE 
                itemtype in ('.$this->itemType.') and
                ispackage = 0 and
                '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey and
                '.$this->tableItemUnit.'.pkey = '.$this->tableName.'.baseunitkey and
                item_weight_unit.pkey =  '.$this->tableName.'.weightunitkey and
			    '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
			'.$this->criteria.'	 
			 group by '.$this->tableName.'.pkey) as ' . $this->tableName;
		   
	   
       return $sql;
   }
    
 function getItemModelDetail($pkey, $criteria = '')
   {
        $sql = '
            select
                '.$this->tableItemModelDetail.'.*,
                '.$this->tableModel.'.name as modelname
            from
                '.$this->tableItemModelDetail.',
                '.$this->tableModel.'
            where
                '.$this->tableItemModelDetail.'.modelkey = '.$this->tableModel.'.pkey and
                '.$this->tableItemModelDetail.'.refkey in ('.$this->oDbCon->paramString($pkey.',').')
                order by '.$this->tableItemModelDetail .'.pkey asc
        ';

         $sql .= $criteria;
           
		return $this->oDbCon->doQuery($sql);
   }

    function getItemTextureDetail($pkey, $criteria = '')
    {
        $sql = '
            select
                '.$this->tableItemTextureDetail.'.*,
                '.$this->tableTexture.'.name as texturename
            from
                '.$this->tableItemTextureDetail.',
                '.$this->tableTexture.'
            where
                '.$this->tableItemTextureDetail.'.texturekey = '.$this->tableTexture.'.pkey and
                '.$this->tableItemTextureDetail.'.refkey in ('.$this->oDbCon->paramString($pkey.',').')
                order by '.$this->tableItemTextureDetail .'.pkey asc
        ';

         $sql .= $criteria;
           
		return $this->oDbCon->doQuery($sql);
    }

    function getItemColorDetail($pkey, $criteria = '')
    {
        $sql = '
            select
                '.$this->tableItemColorDetail.'.*,
                '.$this->tableColor.'.name as colorname
            from
                '.$this->tableItemColorDetail.',
                '.$this->tableColor.'
            where
                '.$this->tableItemColorDetail.'.colorkey = '.$this->tableColor.'.pkey and
                '.$this->tableItemColorDetail.'.refkey in ('.$this->oDbCon->paramString($pkey.',').')
                order by '.$this->tableItemColorDetail .'.pkey asc
        ';

         $sql .= $criteria;
           
		return $this->oDbCon->doQuery($sql);
    }
      

    function getItemCharacterDetail($pkey, $criteria = '')
    {
        $sql = '
            select
                '.$this->tableItemCharacterDetail.'.*,
                '.$this->tableCharacter.'.name as charactername
            from
                '.$this->tableItemCharacterDetail.',
                '.$this->tableCharacter.'
            where
                '.$this->tableItemCharacterDetail.'.characterkey = '.$this->tableCharacter.'.pkey and
                '.$this->tableItemCharacterDetail.'.refkey in ('.$this->oDbCon->paramString($pkey.',').')
                order by '.$this->tableItemCharacterDetail .'.pkey asc
        ';

         $sql .= $criteria;
           
		return $this->oDbCon->doQuery($sql);
    }

    function getItemVariationDetail($pkey, $criteria = '')
    {
        // ini sudah depricated
        $sql = '
            select
                '.$this->tableItemVariationDetail.'.*,
                '.$this->tableItemVariation.'.name as variationname
            from
                '.$this->tableItemVariationDetail.',
                '.$this->tableItemVariation.'
            where
                '.$this->tableItemVariationDetail.'.variationkey = '.$this->tableItemVariation.'.pkey and
                '.$this->tableItemVariationDetail.'.refkey in ('.$this->oDbCon->paramString($pkey.',').')
                order by '.$this->tableItemVariationDetail .'.pkey asc
        ';

         $sql .= $criteria;
           
		return $this->oDbCon->doQuery($sql);
    }

   	function searchData($fieldname='',$searchkey='',$mustmatch=true,$searchCriteria='',$orderCriteria='', $limit='',$groupCriteria='',$warehouseCriteria ='',$onlyQOHCount = true){
		
		$criteria = '';
		 
		if(!empty($fieldname)){
			
			$criteria .= ' and ' ;
			
			if($mustmatch)
				$criteria .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$criteria .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$criteria .= ' ' .$searchCriteria;
	
		$this->setCriteria($criteria); 
         
        if($warehouseCriteria <> '')
       		 $this->warehouseCriteria = ' ' .$warehouseCriteria;
        
		$sql = $this->getQuery($onlyQOHCount);
	
        if($groupCriteria <> '')
       			$sql .= ' ' .$groupCriteria;

		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria; 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit; 
    
		return $this->oDbCon->doQuery($sql);	
	}  
    
    function searchDataAgingReport($fieldname='',$searchkey='',$mustmatch=true,$searchCriteria='', $orderCriteria='', $limit=''){
        
        // get latest warehouse position too....
        $sql = 'select 
                    '.$this->tableName.'.*, 
                    '.$this->tableStatus.'.status as statusname
                from 
                    '.$this->tableName.' left join   '.$this->tableBrand.' 
						on '.$this->tableName.'.brandkey = '.$this->tableBrand.'.pkey ,
                    '.$this->tableStatus.' 
                where
                    '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
                    
                ';
        
        if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	   
		if($orderCriteria <> '') 
			$sql .= ' ' .$orderCriteria;  
			
		if($limit <> '')
			$sql .= ' ' .$limit; 
     
		return $this->oDbCon->doQuery($sql);
        
    }
    
   /* function searchSNInAgingCriteria($fieldname='',$searchkey='',$mustmatch=true,$searchCriteria='', $orderCriteria='', $limit=''){
        
        // cek qty terakhir per tgl ada apa gk...
        
        
        $sql = 'select 
                 distinct('.$this->tableSerialNumberMovement.'.serialnumber),
                 '.$this->tableName.'.name as itemname,
                 '.$this->tableName.'.code as itemcode
               from  
                '.$this->tableSerialNumberMovement.',
                '.$this->tableName.'                    
            where 
                '.$this->tableSerialNumberMovement.'.itemkey = '.$this->tableName.'.pkey and
                '.$this->tableSerialNumberMovement.'.statuskey = 1 and
                '.$this->tableSerialNumberMovement.'.qtyinbaseunit > 0 '; 
        
        $sql = 'select 
                 '.$this->tableSerialNumberMovement.'.serialnumber,
                 '.$this->tableName.'.name as itemname,
                 '.$this->tableName.'.code as itemcode
               from  
                '.$this->tableSerialNumberMovement.',
                '.$this->tableName.'                    
            where 
                '.$this->tableSerialNumberMovement.'.statuskey = 1 and
                '.$this->tableSerialNumberMovement.'.itemkey = '.$this->tableName.'.pkey
                ';
         
        if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	   
        $sql .= '  group by '.$this->tableSerialNumberMovement.'.serialnumber
                   having sum('.$this->tableSerialNumberMovement.'.qtyinbaseunit) > 0  ';
        
		if($orderCriteria <> '') 
			$sql .= ' ' .$orderCriteria;  
			
		if($limit <> '')
			$sql .= ' ' .$limit; 
     
        //$this->setLog($sql,true);
		return $this->oDbCon->doQuery($sql);
        
    }*/
    
    function afterUpdateData($arrParam, $action){  
    
        //$pkey = $arrParam['pkey'];
		
        if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['jewelry']))){  
             $this->calculateAutoPrice($arrParam['pkey']);
        }
        
        
	    if($this->loadSetting('useVariant') == 1){  
            $this->updateParentVariant($arrParam); 
            $this->updateVariantInheritInformation($arrParam['pkey']); 
        }
        
        if(isset($arrParam['txtDescriptionLabel']))
            $this->updateDescription($arrParam['pkey'],$arrParam);
        
        if(isset($arrParam['hidItemPackageKey']))
            $this->updateItemContentOfPackage($arrParam['pkey'], $arrParam); 
        
		if(isset($arrParam['marginPercentage']))
			$this->updateItemPriceByMargin($arrParam['pkey'],$arrParam['sellingPrice'],$arrParam['marginPercentage']);

        if(isset($arrParam['hidItemSpecificationKey']))
            $this->updateItemSpecification($arrParam['pkey'], $arrParam); 
        
        //if(isset($arrParam['item-image-uploader']))
        //    $this->updateImage($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);  
        //
        //if(isset($arrParam['item-file-uploader'])) 
        //    $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);  
        
        //$this->updateUnitConversion($arrParam['pkey'],$arrParam);  
        $this->updateItemFilter($arrParam['pkey'],$arrParam);  
        $this->updatePartNumber($arrParam['pkey'],$arrParam);   
        //$this->updateItemSyncMarketplace($arrParam['pkey'],$arrParam);  
        
    }
    
    function afterCommitUpdateData($pkey,$rs){
        
        parent::afterCommitUpdateData($pkey,$rs);
        
        // matiin dulu
		//if($this->isActiveMarketplace){
		//	$marketplace = new Marketplace();
//
		//	// update marketplace
		//	$syncCriteria = array();
		//	$syncCriteria['attr'] = array('name','brand', 'qoh', 'price','measurement', 'status','shortDescription','image', 'others'); // array(ALL) <- harusnya ksoong, artiyna updatte semua
		//	$syncCriteria['type'] = 2;  
		//	$syncCriteria['itemkey'] = $pkey; 
//
		//	$marketplace->syncProductsInAllMarketplace($syncCriteria);
		//}
        
    }
    
    function afterCommitChangeStatus($pkey){
		if($this->isActiveMarketplace){
			$marketplace = new Marketplace();

			// update marketplace
			$syncCriteria = array();
			$syncCriteria['attr'] = array('status'); 
			$syncCriteria['type'] = 2;  
			$syncCriteria['itemkey'] = $pkey; 

			$marketplace->syncProductsInAllMarketplace($syncCriteria);
		}
    }
        
    function afterCommitDelete($rs){
		if($this->isActiveMarketplace){
			 $marketplace = new Marketplace();
			 $marketplace->deleteProductsInAllMarketplace($rs);
		}
    }
 
        
    function editData($arrParam){
		$pkey = $arrParam['hidId']; 
        
 
        // validasi agar unit dasar tidak berubah jika sudah ad transaksi
        $itemMovement = new ItemMovement();
        $rsMovement = $itemMovement->searchData('itemkey', $pkey, true,' and '. $itemMovement->tableName.'.statuskey = 1', 'limit 1');
 
        $rsItemUnitConversion = $this->getAvailableUnit($pkey, ' and conversionunitkey <> baseunitkey');
        $lockedByConversion = false;
        for ($i=0;$i<count($rsItemUnitConversion); $i++){   
                if ($rsItemUnitConversion[$i]['islocked'] == 1){
                    $lockedByConversion = true;
                    break;
                } 
        }

        if (!empty($rsMovement) || $lockedByConversion){
            $rsItem = $this->getDataRowById($pkey);
            $arrParam['selBaseUnitKey'] = $rsItem[0]['baseunitkey'];
        } 
               
		return parent::editData($arrParam); 
		 
	}
 
 
    function updatePartNumber($pkey,$arrParam){  
        // kalo ad permintaan baru diaktifkan lg
		return;
		
        $sql = 'delete from '.$this->tableVendorPartNumber.' where refkey = '. $this->oDbCon->paramString($pkey) .' and islock = 0';
		$this->oDbCon->execute($sql);
         
        // select part number yg sudah ke lock
        $rsPartNumber = $this->getVendorPartNumber($pkey);
        $existingLockedPartNumber = array_column($rsPartNumber,'partnumber');
        
        $arrPartNumber = $arrParam['partNumber'];
        
        for ($i=0;$i<count($arrPartNumber);$i++){
            
            if(empty($arrPartNumber[$i]))
                continue;
            
            //kalo part number sama dengan yg ke lock, abaikan
            if (in_array($arrPartNumber[$i], $existingLockedPartNumber))
                continue;
            
            $sql = 'insert into '.$this->tableVendorPartNumber.' (
                        refkey,
						partnumber
					 ) values (
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrPartNumber[$i]).'
					)';	 
            
			$this->oDbCon->execute($sql);
            
        }
    }     
	
	function updateImage($pkey,$token,$arrImage){		
        return;
       /* if (!isset($arrParam['_ignore_']) && $arrParam['_ignore_']['itemImage'])
            return;*/
          
		$sourcePath = $this->uploadTempDoc.$this->uploadFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFolder; 
        
		if(!is_dir($sourcePath))  return;
        
		if(!is_dir($destinationPath))  mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
		
		//delete variant image 
		/*$rsItemImage = $this->getItemImage($pkey);
		for($i=0;$i<count($rsItemImage);$i++){
			$sql = 'delete from '.$this->tableImageVariant.' where refkey = '. $this->oDbCon->paramString($rsItemImage[$i]['pkey']);
			$this->oDbCon->execute($sql);
			$this->deleteAll($this->defaultDocUploadPath.$this->uploadVariantFolder.$rsItemImage[$i]['pkey']);
		}*/
		
		//delete previous images	    
		$this->deleteAll($destinationPath);  
		$sql = 'delete from '.$this->tableImage.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
        
		  
		if (!empty($arrImage))	{
			$arrImage = explode(",",$arrImage);
			for ($i=0;$i<count($arrImage);$i++){   
                 
                if ($i >= PLAN_TYPE['maxproductimage']) continue; 
				
				$newFileName = $this->hashFileName($sourcePath.$arrImage[$i]);
				$imagekey = $this->getNextKey($this->tableImage);  
                
				$this->uploadImage($sourcePath, $destinationPath,$arrImage[$i],$newFileName);
				
                // get file size
                $filefullpath = $destinationPath.'/'.$newFileName;
                $arrSizeInformation = getimagesize($filefullpath);
                $width = $arrSizeInformation[0];
                $height = $arrSizeInformation[1]; 
	            $size = filesize($filefullpath);
    
                $sql = 'insert into '.$this->tableImage.' (pkey,refkey,file,width,height,size) 
                        values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($pkey).', '.$this->oDbCon->paramString($newFileName).','.$this->oDbCon->paramString($width).','.$this->oDbCon->paramString($height).','.$this->oDbCon->paramString($size).')';	
				$this->oDbCon->execute($sql);	 
				
				//$this->updateImageVariant( $imagekey, $arrImage[$i], $arrImageVariant);
			}		
		}  
					
	} 
	
	
	function updateFile($pkey,$token,$arrFile){		
	 /*   if (isset($arrParam['_ignore_']) && $arrParam['_ignore_']['itemFile'])
            return;*/
        
        if(!empty($arrFile)) 
            $this->validateDiskUsage(); 
        
		$sourcePath = $this->uploadTempDoc.$this->uploadFileFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFileFolder;
		
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
		 
		
		//delete previous files	    
		$this->deleteAll($destinationPath);  
		$sql = 'delete from '.$this->tableFile.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		 
		
		 
		if(!is_dir($sourcePath)) 
			return;
	
		if (!empty($arrFile))	{
			$arrFile = explode(",",$arrFile);
			for ($i=0;$i<count($arrFile);$i++){   
				$this->uploadImage($sourcePath, $destinationPath,$arrFile[$i]);
				
				$imagekey = $this->getNextKey($this->tableFile);  
				
				$sql = 'insert into '.$this->tableFile.' (pkey,refkey,file) values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($arrFile[$i]).')';	
				$this->oDbCon->execute($sql);	 
				 
			}		
		} 
					
	} 
	
	/*function updateImageVariant($imagekey, $token, $arrImage){		
		 
		$sourcePath = $this->uploadTempDoc.$this->uploadVariantFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadVariantFolder;
		
			
		if(!is_dir($sourcePath))
			return;
        
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $imagekey;  
		
		//default value   
		if (empty($arrImage) ){
			$color = '#000000'; 
		}else{
			$arrImage = json_decode($arrImage,true); 
			
			if(empty( $arrImage[$token])){
				$color = '#000000'; 
			}else{
				$arrImage = $arrImage[$token]; 
				$color = $arrImage[0]['fileColor'];
			}
			
		}
		
		
		$sql = 'update  '.$this->tableImage.' set color =  '.$this->oDbCon->paramString($color).' where pkey = ' . $this->oDbCon->paramString($imagekey);
		$this->oDbCon->execute($sql);	 
		
		
	
		if (!empty($arrImage))	{
			//$arrImage = json_decode($arrImage,true); 
			//$arrImage = $arrImage[$token]; 
			 
			for ($i=1;$i<count($arrImage);$i++){   
				$this->uploadImage($sourcePath, $destinationPath,$arrImage[$i]['fileName']);
				
				$sql = 'insert into '.$this->tableImageVariant.' (refkey,file,color) values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($arrImage[$i]['fileName']).','.$this->oDbCon->paramString($arrImage[$i]['fileColor']).')';	
				$this->oDbCon->execute($sql);	  
			}		
		}
					
	} */
	 
	
	function validateForm($arr,$pkey = ''){
		
        // NOTE 
        // utk variant marketplace, sementara parent boleh ganti agar tidak bottleneck, 
        // karena kalo variant item di nonaktifkan dulu variantny, akan dianggap item baru ketika disave
        // tidak perlu ada security object utk harga jual dibawah COGS, karena sudah ada securityObject utk update harga jual
		
        $widthLimit = 300;
        $heightLimit = 300;
        
		$arrayToJs = parent::validateForm($arr,$pkey);  
		 
        $itemUnit = new ItemUnit();
        $itemCategory = new ItemCategory();
		$showTimeUnit = $this->loadSetting('showMultiTimeUnit'); 
        
		$name = $arr['name'];  
		$gramasi = $this->unFormatNumber($arr['gramasi']);
        
        $arr['sellingPrice'] = $this->unFormatNumber($arr['sellingPrice']); // sekalian untuk marketplace
		$sellingPrice = floatval($arr['sellingPrice']);
        
		$maxStockQty = $this->unFormatNumber($arr['maxStockQty']);
		$minStockQty = $this->unFormatNumber($arr['minStockQty']);
		$defTransUnitKey = $arr['selDefaultTransUnitKey'];
		$selBaseUnitKey = $arr['selBaseUnitKey']; 
		$categorykey = $arr['hidCategoryKey'];
		$arrItemPackageKey = $arr['hidItemPackageKey']; 
        $arrItemSpecificationKey = $arr['hidItemSpecificationKey']; 
        $arrSpecificationValue = $arr['specificationValue']; 
		$arrQty = $arr['qty'];
        $arrPartNumber = $arr['partNumber'];
		$arrTimeKey = isset($arr['selTimeUnitKey']) ? $arr['selTimeUnitKey'] : array(); 
        $arrUrl = $arr['url'] ?? array();
        
        $arr['gramasi'] = floatval($this->unFormatNumber($arr['gramasi'])); // sekalian untuk marketplace
        $arr['length'] = floatval($this->unFormatNumber($arr['length'])); // sekalian untuk marketplace
        $arr['width'] = floatval($this->unFormatNumber($arr['width'])); // sekalian untuk marketplace
        $arr['height'] = floatval($this->unFormatNumber($arr['height'])); // sekalian untuk marketplace
         
		$isVariant = (isset($arr['chkIsVariant'])) ? $arr['chkIsVariant'] : 0;
        
        /*$codeVariant = array();
        if (isset($arr['codeVariant']))
		  $codeVariant = $arr['codeVariant'];
        
        $variantOrder = array();
        if (isset($arr['variantOrder']))
		  $variantOrder = $arr['variantOrder'];*/
		 
		$itemParent  = $arr['hidParentItemKey'];
				   
        if($this->checkTotalItemLimitation($this->tableName,PLAN_TYPE['maxproduct'],$pkey))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][1] . ' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxproduct']). ' '. strtolower($this->lang['items']).')');  

        // sementara dinonaktifkan dulu
        //if (isset($arr['item-image-uploader']) && !empty($arr['item-image-uploader'])){
        //    $arrImage = explode(",",$arr['item-image-uploader']);
        //    if(count($arrImage) > PLAN_TYPE['maxproductimage'])
        //        $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][2] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxproductimage']). ' '. strtolower($this->lang['images']).')' );
//
        //    for($i=0;$i<count($arrImage);$i++){ 
        //        $filefullpath = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader'].'/'.$arrImage[$i];
        //        if (filesize($filefullpath) >  (pow(1024,2) * PLAN_TYPE['maximagesize'])  )
        //            $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
        //    
        //        
        //        // validasi size min 300 x 300 
        //        $arrSizeInformation = getimagesize($filefullpath);
        //        $width = $arrSizeInformation[0];
        //        $height = $arrSizeInformation[1]; 
        //        
        //        if($width < $widthLimit || $height < $heightLimit)
        //            $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][7] .' ('.$widthLimit.'px X '. $heightLimit.'px)' );
        //             
        //    }
        //    
        //}
        
        //if(isset($arr['item-file-uploader']) && !empty($arr['item-file-uploader'])){ 
        //    $arrFile = explode(",",$arr['item-file-uploader']);
        //    if(count($arrFile) > PLAN_TYPE['maxproductfile'])
        //        $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][3] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxproductfile']). ' '. strtolower($this->lang['files']).')' );
//
        //    for($i=0;$i<count($arrFile);$i++){
        //        if (empty($arrFile[$i])) continue;
//
        //        $path = $this->uploadTempDoc.$this->uploadFileFolder.$arr['token-item-file-uploader']; 
        //        if (filesize($path.'/'.$arrFile[$i]) > (pow(1024,2) * PLAN_TYPE['maxfilesize']) )
        //            $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][5] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maxfilesize']). ' MB)' );
        //        
        //    }
        //}
         
		// $rsItem = $this->isValueExisted($pkey,'name',$name);	 
		// if(empty($name)){
		// 	$this->addErrorList($arrayToJs,false,$this->errorMsg['item'][1]);
		// }else if(count($rsItem) <> 0){
		// 	$this->addErrorList($arrayToJs,false,$this->errorMsg['item'][2]);
		// }
		
        // cek kategori isleaf bukan
        if(!empty($categorykey)){
            $rsCategory = $itemCategory->searchDataRow(array($itemCategory->tableName.'.name', $itemCategory->tableName.'.isleaf'),
                                                     ' and '.$itemCategory->tableName.'.pkey = ' .  $this->oDbCon->paramString($categorykey)
                                                      );
            
            if ($rsCategory[0]['isleaf'] <>1 )
                $this->addErrorList($arrayToJs,false,$this->errorMsg['category'][3]); 
                
        }
		
		if (empty($isVariant) && empty($categorykey)){ 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]); 
		}
        
		if (!is_numeric($gramasi) || $gramasi < 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['gramasi'][2]);
		}
		
        if($this->loadSetting('itemWeightCannotBeEmpty') == 1 && $gramasi <= 0){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['gramasi'][3]);
        }
        
		if (!is_numeric($sellingPrice) || $sellingPrice < 0){ 
			$this->addErrorList($arrayToJs,false, $this->errorMsg['sellingPrice'][2]);
		}
		
		if ($maxStockQty < 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['maxStockQty'][2]);
		}
		
		if ($minStockQty < 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['minStockQty'][2]);
		}
		 
		if ($isVariant){
			/*if (empty($codeVariant)){
				$this->addErrorList($arrayToJs,false,$this->errorMsg['codeVariant'][1]);
			} 
			if (!is_numeric($variantOrder)){
				$this->addErrorList($arrayToJs,false,$this->errorMsg['orderList'][2]);
			}*/
			if (empty($itemParent)){
				$this->addErrorList($arrayToJs,false,$this->errorMsg['itemParent'][1]);
			}else{ 
                // gk boleh pilih diri sendiri sebagai item parent
				if(!empty($pkey) && $itemParent==$pkey)
					$this->addErrorList($arrayToJs,false,$this->errorMsg['item'][6]);
			 }
            
            // item parent tdk boleh punya stok
            // kalo stok nya 0, di marketplace bagaimana ?
            
           /* $itemMovement = new ItemMovement();
            $parentQOH = $itemMovement->getItemQOH($itemParent);  
            if($parentQOH > 0) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['itemParent'][2]);*/
            
            
		}
		 
        // validasi satuan yg dipilih harus ad konversinya 
        // kalo dr import, gk perlu cek karena gk diupdate
        if(isset($arr['selConversionUnitKey'])){ 
             $conversionMultiplier = $arr['txtConversionMultiplier'];
             $unitConversion = $arr['selConversionUnitKey'];

             $conversionUnitKey = array();  
             for($i=0;$i<count($unitConversion); $i++){ 

                    if (empty($unitConversion[$i])) continue;
 
                    if (in_array($unitConversion[$i],$conversionUnitKey)){   
                        $rsItemUnit = $itemUnit->getDataRowById($unitConversion[$i]);
                        $this->addErrorList($arrayToJs,false, $rsItemUnit[0]['name'].'. '.$this->errorMsg[215]); 	 
                    }else{ 
                        if ($conversionMultiplier[$i] > 0)
                            array_push($conversionUnitKey,$unitConversion[$i]); 
                    }
                }

                // problem, $conversionUnitKey harus ditambah yg kelock / disabled jg
                $rsUnitConv = $this->getAvailableUnit($pkey);  
                foreach($rsUnitConv as $unitrow){
                    if (!in_array($unitrow['conversionunitkey'],$conversionUnitKey))
                        array_push($conversionUnitKey,$unitrow['conversionunitkey']); 
                }
 
                // validasi gk boelh dr $conversionUnitKey, karena kalo delete row, database blm keupdate
                if ($defTransUnitKey <> $selBaseUnitKey && !in_array ($defTransUnitKey, $unitConversion) ){
                        $rsItemUnit = $itemUnit->getDataRowById($defTransUnitKey);
                        $this->addErrorList($arrayToJs,false, $rsItemUnit[0]['name'].'. '.$this->errorMsg['itemUnitConversion'][4]);
                }

            }

        
        $arrDetailPartNumber = array(); 
		for($i=0;$i<count($arrPartNumber); $i++){
						 
		    // cek ada detail double gk 
            if(empty($arrPartNumber[$i]))
                continue;
            
            if (in_array($arrPartNumber[$i],$arrDetailPartNumber)){   
                $this->addErrorList($arrayToJs,false, $arrPartNumber[$i].'. '.$this->errorMsg[215]); 	 
            }else{ 
                
                // cek ad yg sama gk di item lain 
                // kalo edit, tambah pengecualian
                $criteria = ' and partnumber = ' . $this->oDbCon->paramString($arrPartNumber[$i]);
                $criteria .= (isset($pkey) && !empty($pkey)) ?  ' and refkey <> '. $pkey : ''; 
                $rsPartsNumber = $this->getVendorPartNumber('',$criteria);
                
                if (!empty($rsPartsNumber))
                    $this->addErrorList($arrayToJs,false, $arrPartNumber[$i].'. '.$this->errorMsg['vendorPartNumber'][2].'<br>'.$rsPartsNumber[0]['itemname'].'.'); 	  
                else
                    array_push($arrDetailPartNumber, $arrPartNumber[$i]);
                
            }
		}  
        
		if($showTimeUnit){
			$arrDetailTime = array(); 
			$timeUnit = new TimeUnit();
			for($i=0;$i<count($arrTimeKey); $i++){
				if(empty($arrTimeKey[$i])) continue;

			if (in_array($arrTimeKey[$i],$arrDetailTime)){
				$rsTimeUnit = $timeUnit->getDataRowById($arrTimeKey[$i]);
				$this->addErrorList($arrayToJs,false, $rsTimeUnit[0]['name'].'. '.$this->errorMsg[215]); 
			}else
				array_push($arrDetailTime, $arrTimeKey[$i]);
			}  

		  }      
       //cek url
        	for($i=0;$i<count($arrUrl); $i++){
				if(empty($arrUrl[$i])) continue;
                
                if (!empty($arrUrl[$i]) && filter_var($arrUrl[$i], FILTER_VALIDATE_URL) === false) {
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['url'][3]);
                } 
            }
        
		if($this->activeModule['itemchecklist']){
				$arrDetailKeys = array();  
				$itemChecklist = new ItemChecklist();
				for($i=0;$i<count($arrItemPackageKey);$i++) {
					if( empty($arrItemPackageKey[$i]) && empty($arrQty[$i]) )
						continue;

					if (empty($arrItemPackageKey[$i]) ){ 
						$this->addErrorList($arrayToJs,false, $this->errorMsg['itemCheckList'][2]); 	
					}else{
						if ($this->unFormatNumber($arrQty[$i]) <= 0){
							$rsItem = $itemChecklist->getDataRowById($arrItemPackageKey[$i]);
							$this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['itemCheckList'][2]); 
						} 

						// cek ada detail double gk 
						if (in_array($arrItemPackageKey[$i],$arrDetailKeys)){  
							$rsItem = $itemChecklist->getDataRowById($arrItemPackageKey[$i]);
							$this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
						}else{ 
							array_push($arrDetailKeys, $arrItemPackageKey[$i]);
						}
					}   
				}
		}
      
        
        $arrSpecificationDetail = array(); 
		for($i=0;$i<count($arrItemSpecificationKey);$i++) {
            
            if( empty($arrItemSpecificationKey[$i]) && empty($arrSpecificationValue[$i]) )  continue;
                
		 	if (!empty($arrItemSpecificationKey[$i]) ){
                
                if( empty($arrSpecificationValue[$i]))
				    $this->addErrorList($arrayToJs,false, $this->errorMsg['itemSpecification'][1]); 	
			  
                // cek ada detail double gk 
                if (in_array($arrItemSpecificationKey[$i],$arrSpecificationDetail)){  
                    $rsItemSpecification = $itemSpecification->getDataRowById($arrItemSpecificationKey[$i]);
                    $this->addErrorList($arrayToJs,false, $rsItemSpecification[0]['name'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    array_push($arrSpecificationDetail, $arrItemSpecificationKey[$i]);
                }
            }   
		}

        
        // VALIDASI KHUSUS KALO AD MARKETPLACE 
        
        if($this->isActiveMarketplace){
			
            $marketplace = new Marketplace();
            $marketplaceObjs = $marketplace->getMarketplaceObj();
            if(!empty($marketplaceObjs)){

                // semua field di kolom marketplace wajib diisi  
                $arrSyncToMarketplace = $arr['chkSyncToMarketplace'];
                $arrSyncToMarketplaceKey = $arr['hidSyncMarketplaceKey'];

                // tampung account mana saja yg sync
                $arrAccNeedToSync = array();
                for($i=0;$i<count($arrSyncToMarketplace);$i++){ 
                    if($arrSyncToMarketplace[$i] == 1)
                        array_push($arrAccNeedToSync,$arrSyncToMarketplaceKey[$i]);
                }


                // validasi field2 primary yg wajib diisi
                // kalo gk ada yg sync, gk usah validasi

                $arrValidateField = array();
                if(!empty($arrAccNeedToSync)){ 

                    // CEK CATEGORY ==========
                    if(strlen(trim($arr['shortdescription'])) < 50)
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['shortDescription'][2]);  


                    // CEK MAPPING cATEGORY ==========
                     foreach($marketplaceObjs as $marketplaceRow){
                         $marketplaceObj = $marketplaceRow['obj'];

                        // cek kategori sudah lengkap blm  
                        $rsMPCategory = $itemCategory->getMarketplaceCategory($categorykey,$marketplaceObj->marketplaceKey);


                        if(empty($rsMPCategory[0]['marketplacecategorykey']))
                            $this->addErrorList($arrayToJs,false,$this->errorMsg['marketplace'][5]);  

                         if (!in_array($marketplaceObj->marketplaceKey, $arrAccNeedToSync)) continue; 

                         $rsExclude = $marketplaceObj->getExcludeAttributes(); 
                         $rsExclude = array_column($rsExclude,'paramname');

                         foreach($rsExclude as $row){ 
                             if(!in_array($row,$arrValidateField))
                                 array_push($arrValidateField, $row); 
                         } 
                    }

                    foreach($arrValidateField as $field){    
                        if (empty($arr[$field])){

                            $errMsg = '';
                            foreach($this->arrData as $key=>$row){  
                                if($row[0] == $field){
                                    $errMsg = $row[1]['emptyErrMsg']; 
                                    break;
                                } 
                            }

                           $this->addErrorList($arrayToJs,false, $errMsg); 	
                        }
                    }


                    // CEK ATTRIBUTE YANG WAJIB DIISI ==========
                    $arrAttributeValue = array();
                    $arrAttributeLabel = array();
                    foreach($arr['attributeValue'] as $key => $row){  
                        if(in_array($arr['hidMarketplaceKey'][$key],$arrAccNeedToSync)){ 
                            array_push($arrAttributeValue,$row); 
                            array_push($arrAttributeLabel,$arr['hidAttributeLabel'][$key]);  
                        }
                    }

                    foreach($arrAttributeValue as $key => $attributeValue){  
                        // utk field nama, boleh kosong
                        // brand jg 0, bisa masalah 

                       /* if ($arr['hidCategoryAttributeKey'][$key] == '0')   // kalo pake angka 0, malah error 
                            continue;*/

                        if (empty($attributeValue)) 
                            $this->addErrorList($arrayToJs,false,'<b>'.$arrAttributeLabel[$key].'</b>, '.$this->errorMsg['marketplace'][4]);  
                    } 
                }


            }

		}
		return $arrayToJs;
	 } 

    function cekDuplicateData($name, $brandkey, $mililiter, $category, $alcoholContent)
    {
        $sql = 'select 
        '.$this->tableName.'.pkey,
        '.$this->tableName.'.code
        from 
            '.$this->tableName.'
        where   
            '.$this->tableName.'.name = ' . $this->oDbCon->paramString($name) .' and
            '.$this->tableName.'.brandkey = ' . $this->oDbCon->paramString($brandkey) .' and
            '.$this->tableName.'.mililiter = ' . $this->oDbCon->paramString($mililiter) .' and
            '.$this->tableName.'.categorykey = ' . $this->oDbCon->paramString($category) .' and
            '.$this->tableName.'.alcoholcontent = ' . $this->oDbCon->paramString($alcoholContent) ;

            $rs = $this->oDbCon->doQuery($sql);
            $this->setLog($sql, true);


        return $rs;
    }
	  
	function getItemDescription($pkey){
		$sql = 'select * from  '.$this->tableDescription.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql); 
	} 
	function getItemImage($pkey ){  
		$sql = 'select * from '.$this->tableImage.' where refkey = '.$this->oDbCon->paramString($pkey).' order by  pkey asc';	
	 	return $this->oDbCon->doQuery($sql);
    } 
	
	function getItemFile($pkey){
		$sql = 'select * from '.$this->tableFile.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql);
    } 
    
    function getItemSyncMarketplace($pkey,$marketplacekey=''){
		$sql = 'select * from '.$this->tableItemSyncMarketplaceDetail.' where refkey = '.$this->oDbCon->paramString($pkey);
        
        if(!empty($marketplacekey))
            $sql .= 'and marketplacekey = ' . $this->oDbCon->paramString($marketplacekey);
        
        $sql .= ' order by pkey asc';	
         
		return $this->oDbCon->doQuery($sql);
    } 	
    
    function isItemSyncToMarketplace($pkey,$marketplacekey){
        $rs = $this->getItemSyncMarketplace($pkey,$marketplacekey); 
        return (empty($rs) || $rs[0]['issync'] == 0) ? false : true; 
    }   
    
	function updateItemPriceByMargin($pkey,$sellingPrice,$marginPercentage){
  
		if($this->loadSetting('autoSellingPrice') < 1) return; // kedepannya, 1, 2 dan 3 menentukan cara penentuan harga jual
		
		if($marginPercentage <= 0) return;
		
		$sellingPrice = $this->unFormatNumber($sellingPrice);
		$marginPercentage = $this->unFormatNumber($marginPercentage);
		$cogsValue = $this->unFormatNumber($this->getCOGS($pkey));
		
		$priceMargin = ($cogsValue * ($marginPercentage / 100)) + $cogsValue; 

		if($sellingPrice < $priceMargin)
			$sellingPrice = $priceMargin;
								
        $sql = 'update '. $this->tableName .' set sellingprice = '.$this->oDbCon->paramString($sellingPrice).' where pkey = ' .$this->oDbCon->paramString($pkey);
		return  $this->oDbCon->execute($sql);
		 
	}	
	
	function getItemImageVariant($pkey){
		$sql = 'select file,color from  '.$this->tableImage.' where pkey = '.$this->oDbCon->paramString($pkey).'  union select file,color from  '.$this->tableImageVariant.' where refkey = '.$this->oDbCon->paramString($pkey);	
		return $this->oDbCon->doQuery($sql);
    } 
	 
	function getVariant($pkey,$warehouseCriteria = '', $orderBy = ''){
		
		$this->setCriteria(' and '.$this->tableName.'.parentkey = ' .$this->oDbCon->paramString($pkey) );
        
        if (!empty($warehouseCriteria))
            $this->warehouseCriteria = ' ' .$warehouseCriteria;
        
		$orderBy = (!empty($orderBy)) ? $orderBy : ' order by '.$this->tableName.'.variantorder asc';
        $sql = $this->getQuery().' ' .$orderBy;
         
		//$sql = 'select * from ' .$this->tableName .' where parentkey = ' .$this->oDbCon->paramString($pkey) .' order by variantorder asc' ;
		return $this->oDbCon->doQuery($sql);
	}

    
    function getAvailableUnit($itemkey,$criteria='', $orderby=''){
            $itemUnit = new ItemUnit();
        
            $rsItem = $this->getDataRowById($itemkey); 
		

			$baseunitkey = (!empty($rsItem[0]['baseunitkey'])) ? $rsItem[0]['baseunitkey'] : 0;
			$deftransunitkey =  (!empty($rsItem[0]['deftransunitkey'])) ? $rsItem[0]['deftransunitkey']: $baseunitkey; 
			$sellingprice = (!empty($rsItem[0]['sellingprice'])) ? $rsItem[0]['sellingprice'] : 0;

			// sementara, utk jenis service, gk ad opsi harga 
			if($rsItem[0]['itemtype'] == SERVICE){
				
				$sql = '
							select 
								'.$this->tableUnit.'.pkey as pkey,
								'.$this->tableUnit.'.pkey as conversionunitkey,
								'.$sellingprice.' as sellingprice,
								'.$baseunitkey.' as baseunitkey,
								'.$baseunitkey.' as deftransunitkey,
								1 as conversionmultiplier, 
								1 as islocked, 
								'.$this->tableUnit.'.code as unitcode,
								'.$this->tableUnit.'.name as unitname
								from   
									'.$this->tableUnit.' 
								where
								 1=1
							';  
				
			}else{
				// untuk jenis item
				
				   // harus pake conversionunitkey agar sama dengan struktur database item unit conversion
					$sql = '
							select 
								'.$this->tableUnitConversion.'.pkey,
								'.$this->tableUnitConversion.'.conversionunitkey,
								'.$this->tableUnitConversion.'.sellingprice,
								'.$this->tableUnitConversion.'.baseunitkey,
								'.$deftransunitkey.' as deftransunitkey,
								'.$this->tableUnitConversion.'.conversionmultiplier, 
								'.$this->tableUnitConversion.'.islocked, 
								'.$this->tableUnit.'.code as unitcode,
								'.$this->tableUnit.'.name as unitname

								from (
										select 
											0 as pkey,
											'.$baseunitkey.' as baseunitkey,
											'.$baseunitkey.' as conversionunitkey,
											1 as conversionmultiplier,
											1 as islocked,
											'.$sellingprice.' as sellingprice

										UNION 

										select 
											'.$this->tableUnitConversion.'.pkey,
											'.$this->tableUnitConversion.'.baseunitkey,
											'.$this->tableUnitConversion.'.conversionunitkey,
											'.$this->tableUnitConversion.'.conversionmultiplier,
											'.$this->tableUnitConversion.'.islocked,
											'.$this->tableUnitConversion.'.sellingprice
										from 
											'.$this->tableUnitConversion.' 
										where  
											refkey = '.$this->oDbCon->paramString($itemkey).'  
								) 

							'.$this->tableUnitConversion.',
							'.$this->tableUnit.' 
							where
							   '.$this->tableUnitConversion.'.conversionunitkey = '.$this->tableUnit.'.pkey  
							';  

			}
		
			$sql .= (!empty($criteria)) ? ' ' . $criteria : '';
			$sql .= (!empty($orderby)) ? ' ' . $orderby : ''; 
 
			$rs  = $this->oDbCon->doQuery($sql);  
 
        return   $rs;
    }
    
	function getItemUnitConversion($pkey,$toUnitKey = '', $criteria = '', $orderby = ''){
        $rsItem = $this->getDataRowById($pkey);
        $sellingprice = (!empty($rsItem[0]['sellingprice'])) ? $rsItem[0]['sellingprice'] : 0;
        
		$sql = '
                select
                    0  as pkey,
                    '.$this->oDbCon->paramString($pkey).'  as refkey,
                    '.$this->oDbCon->paramString($rsItem[0]['baseunitkey']).' as baseunitkey,
                    '.$this->oDbCon->paramString($rsItem[0]['baseunitkey']).' as conversionunitkey,
                    1 as conversionmultiplier ,
                    1 as isautoinsert,
                    1 as islocked ,
                    '.$sellingprice.' as sellingprice
                
                UNION 
                
                select 
                    '.$this->tableUnitConversion.'.pkey,
                    '.$this->tableUnitConversion.'.refkey,
                    '.$this->tableUnitConversion.'.baseunitkey,
                    '.$this->tableUnitConversion.'.conversionunitkey,
                    '.$this->tableUnitConversion.'.conversionmultiplier ,
                    '.$this->tableUnitConversion.'.isautoinsert,
                    '.$this->tableUnitConversion.'.islocked ,
                    '.$this->tableUnitConversion.'.sellingprice
                from 
                    '.$this->tableUnitConversion.' 
                where  
                    baseunitkey <> conversionunitkey  and
                    refkey = '.$this->oDbCon->paramString($pkey);
        
        if (!empty($toUnitKey))
            $sql .= ' and conversionunitkey = ' .$this->oDbCon->paramString($toUnitKey); 
        
        $sql .= (!empty($criteria)) ? ' ' . $criteria : '';
        $sql .= (!empty($orderby)) ? ' ' . $orderby : ''; 
         
        
        $rs  = $this->oDbCon->doQuery($sql);  

        return $rs;
	}
    function getTimeDetail($pkey,$criteria=''){
        $sql = 'select
	   			'.$this->tableDetailTime .'.*,
                '.$this->tableTimeUnit.'.name as timename
			  from
			  	'. $this->tableDetailTime .',
                '.$this->tableTimeUnit.'
			  where
			  	' . $this->tableDetailTime .'.timeunitkey = '.$this->tableTimeUnit.'.pkey and
			  	'.$this->tableDetailTime .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
		$rs  = $this->oDbCon->doQuery($sql); 
		return $rs;
    }   
    
    function getConvMultiplier($pkey,$fromUnitKey,$toUnitKey = ''){
        
        if (empty($toUnitKey)){
            $rs = $this->getDataRowById($pkey);    
            if (empty($rs))
                return 0;
            
            $toUnitKey = $rs[0]['baseunitkey'];
        }
        
        if ($fromUnitKey == $toUnitKey)
            return 1;
       
        
        $sql = 'select 
                    coalesce(conversionmultiplier,0) as conversionmultiplier
                from 
                    '.$this->tableUnitConversion.'
                where 
                    refkey = '.$this->oDbCon->paramString($pkey) . ' and 
                    baseunitkey = '.$this->oDbCon->paramString($toUnitKey) . ' and 
                    conversionunitkey = '.$this->oDbCon->paramString($fromUnitKey) ;
         
        
        $rs = $this->oDbCon->doQuery($sql);
        
        $conMultiplier = (empty($rs)) ? 0 : $rs[0]['conversionmultiplier'];
        
        return $conMultiplier; 
    }
	
   function getDetailVideo($pkey,$criteria=''){
        
            $sql = 'select
	   			'.$this->tableDetailVideo .'.*
			  from
			  	'. $this->tableDetailVideo .' 
			  where  
			  	'.$this->tableDetailVideo .'.refkey = '.$this->oDbCon->paramString($pkey);
         
                
        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }
   function getDetailItemSKUAlias($pkey,$criteria=''){
        
            $sql = 'select
	   			        '.$this->tableItemSKUAlias .'.*
			        from
			  	        '. $this->tableItemSKUAlias .' 
			        where  
			  	        '.$this->tableItemSKUAlias .'.refkey in ('.$this->oDbCon->paramString($pkey,',').') 
                ';
         
                
        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }
	function updateDescription($pkey,$arrParam){
		
       /* if (isset($arrParam['_ignore_']) && $arrParam['_ignore_']['itemDescription'])
            return;*/
        
        $sql = 'delete from '.$this->tableDescription.' where refkey = '. $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);

        if (!isset($arrParam['txtDescriptionLabel']))
            return;
        
	 	$itemDescriptionLabel = $arrParam['txtDescriptionLabel'];
		$itemDescription = $arrParam['txtDescription'];
		 
			for ($i=0;$i<count($itemDescription);$i++){
				
                if(!isset($itemDescriptionLabel[$i]))
                    continue;
                
				$label = trim($itemDescriptionLabel[$i]);
				$value = trim($itemDescription[$i]);
				 
			 	if (empty($label) && empty($value))  
					continue; 
			   
				$sql = 'insert into  '.$this->tableDescription.' (refkey,label,value) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($label).',  \''.addslashes($value).'\' )';	
		 
				$this->oDbCon->execute($sql);
				 
			}
					 
	}
	 
 function updateItemContentOfPackage($pkey,$arrParam){
		
        /*if (isset($arrParam['_ignore_']) && $arrParam['_ignore_']['itemContentOfPackage'])
            return;*/
     
        $sql = 'delete from '.$this->tableItemContentOfPackage.' where refkey = '. $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);

        if (!isset($arrParam['hidItemPackageKey']))
            return;
        
	 	$itemPackageKey = $arrParam['hidItemPackageKey'];
		$qty = $arrParam['qty'];
		 
			for ($i=0;$i<count($itemPackageKey);$i++){
			   
                if (empty($qty[$i]) || empty($itemPackageKey))
                    continue;
                    
				$sql = 
                        'insert into  '.$this->tableItemContentOfPackage.'(
                            refkey,
                            itemkey,
                            qty
                        )  values (
                            '.$this->oDbCon->paramString($pkey).',
                            '.$this->oDbCon->paramString($itemPackageKey[$i]).',
                            '.$this->oDbCon->paramString($this->unFormatNumber($qty[$i])).'
                        )';	
		 
				$this->oDbCon->execute($sql);
				 
			}
					 
	}
    
    function getItemPackageOfContent($pkey){
        
        if (empty($pkey)) return array();
     
		$sql = 'select 
                    '.$this->tableItemContentOfPackage.'.*,
                    '.$this->tableItemChecklist.'.name as itemchecklistname
                    
                from 
                    '.$this->tableItemContentOfPackage.'
                    left join  
                    '.$this->tableItemChecklist.' on  '.$this->tableItemContentOfPackage.'.itemkey = '.$this->tableItemChecklist.'.pkey 
                where  
                    refkey = '.$this->oDbCon->paramString($pkey);
         
        $rs  = $this->oDbCon->doQuery($sql);  
          
        return $rs;
	}
	  
    function updateItemSpecification($pkey,$arrParam){
		
     
        $sql = 'delete from '.$this->tableItemSpecificationDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);

        if (!isset($arrParam['hidItemSpecificationKey']))  return;
        
	 	$itemSpecificationKey = $arrParam['hidItemSpecificationKey'];
		$value = $arrParam['specificationValue'];

        for ($i=0;$i<count($itemSpecificationKey);$i++){

            if (empty($value[$i]) || empty($itemSpecificationKey))
                continue;

            $sql = 
                    'insert into  '.$this->tableItemSpecificationDetail.'(
                        refkey,
                        specificationkey,
                        value
                    )  values (
                        '.$this->oDbCon->paramString($pkey).',
                        '.$this->oDbCon->paramString($itemSpecificationKey[$i]).',
                        '.$this->oDbCon->paramString($value[$i]).'
                    )';	
 
            $this->oDbCon->execute($sql);

        }
					 
	}
    
    function getItemSpecification($pkey){
        
        if (empty($pkey)) return array();
     
		$sql = 'select  
                    '.$this->tableItemSpecification.'.name ,
                    '.$this->tableItemSpecificationDetail.'.*                    
                from 
                    '.$this->tableItemSpecification.',
                    '.$this->tableItemSpecificationDetail.'
                where  
                    '.$this->tableItemSpecification.'.pkey = '.$this->tableItemSpecificationDetail.'.specificationkey and
                    refkey = '.$this->oDbCon->paramString($pkey);
         
        $rs  = $this->oDbCon->doQuery($sql);  
          
        return $rs;
	}
	 

	function updateItemFilter($pkey,$arrParam){
        // nanti kalo ad request baru diaaktifklan lg
		
		return;
		
	   /* if (isset($arrParam['_ignore_']) && $arrParam['_ignore_']['itemItemFilter'])
             return; */
        
		$sql = 'delete from '.$this->tableFilterDetail.' where itemkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
        
        
        if (empty($arrParam['selFilter']))
            return;
        
		$itemFilterkey = $arrParam['selFilter']; 


			for ($i=0;$i<count($itemFilterkey);$i++){ 
				$filterkey = $itemFilterkey[$i];
				
			 	if (empty($filterkey))  
					continue;
				   
				$sql = 'insert into  '.$this->tableFilterDetail.' (refkey,itemkey) values ('.$this->oDbCon->paramString($filterkey).','.$this->oDbCon->paramString($pkey).')';	
				$this->oDbCon->execute($sql);
				 
			}
					 
	}
    
	// pindah ke mapping detail
//    function updateItemSyncMarketplace($pkey,$arrParam){
//         
//        if (!isset($arrParam['hidSyncMarketplaceKey'])) return;
//        
//		$sql = 'delete from '.$this->tableItemSyncMarketplaceDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
//		$this->oDbCon->execute($sql);
//  
//        // jgn return karena kalo gk sync perlu di save sebagai 0
//        //if (empty($arrParam['chkSyncToMarketplace'])) return;
//        
//        $arrSyncToMarketplace = $arrParam['chkSyncToMarketplace']; 
//        $arrMarketplaceKey = $arrParam['hidSyncMarketplaceKey']; 
//           
//        for ($i=0;$i<count($arrMarketplaceKey);$i++){ 
//            $marketplacekey = $arrMarketplaceKey[$i];
//            $isSync = $arrSyncToMarketplace[$i];
//
//            //if (empty($marketplacekey) || !$isSync)  continue;
//            if (empty($marketplacekey))  continue;
//
//            $sql = 'insert into  '.$this->tableItemSyncMarketplaceDetail.' (refkey,marketplacekey,issync) values ('.$this->oDbCon->paramString($pkey).','.$this->oDbCon->paramString($marketplacekey).','.$this->oDbCon->paramString($isSync).')';	
//            $this->oDbCon->execute($sql);
//
//        }
//					 
//	}

	function delete($id, $forceDelete = false,$reason = ''){ 
		$arrayToJs =  array();
		 
		try{			 
				 
				$arrayToJs = $this->validateDelete($id);
				if (!empty($arrayToJs)) 
					return $arrayToJs;
		 		
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			 
				
                $rs = $this->getDataRowById($id);
            
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
				
				$sql = 'delete from  '.$this->tableDescription.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql); 
				
			// cuma di EAI dulu
//				$sql = 'delete from  '.$this->tableVendorPartNumber.' where refkey = '. $this->oDbCon->paramString($id);
//				$this->oDbCon->execute($sql);
				
				
				$rsItemImage = $this->getItemImage($id);
				for($i=0;$i<count($rsItemImage);$i++){
					$sql = 'delete from '.$this->tableImageVariant.' where refkey = '. $this->oDbCon->paramString($rsItemImage[$i]['pkey']);
					$this->oDbCon->execute($sql);
					$this->deleteAll($this->defaultDocUploadPath.$this->uploadVariantFolder.$rsItemImage[$i]['pkey']);
				}
	
				//$sql = 'delete from '.$this->tableImage.' where refkey = '. $this->oDbCon->paramString($id);
				//$this->oDbCon->execute($sql);
				//$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id);
				
			    $sql = 'delete from '.$this->tableFile.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFileFolder.$id);
				
                $sql = 'delete from '.$this->tableUnitConversion.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);	 
				
				$sql = 'delete from  '.$this->tableItemContentOfPackage.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
            
				$sql = 'delete from  '.$this->tableItemSpecificationDetail.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
            
                $sql = 'delete from '.$this->tableItemSyncMarketplaceDetail.' where refkey = '. $this->oDbCon->paramString($id);
                $this->oDbCon->execute($sql);

                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				$this->afterCommitDelete($rs);
            
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
	 
	  
	 
	 function updateUnitConversion($itemkey,$arrParam){ 
         	
	        /*if (isset($arrParam['_ignore_']) && $arrParam['_ignore_']['itemUnitConversion'])
             return;*/
         
			$baseUnitKey = $arrParam['selBaseUnitKey'];
			$conversionUnitKey = (isset($arrParam['selConversionUnitKey']) && !empty($arrParam['selConversionUnitKey'])) ? $arrParam['selConversionUnitKey'] :  array();
			$conversionMultiplier = (isset($arrParam['txtConversionMultiplier']) && !empty($arrParam['txtConversionMultiplier'])) ? $arrParam['txtConversionMultiplier'] : array();
		   
            $arrOptions = array();
            $arrOptions['tableName'] = $this->tableHeaderCost;
 
            $this->updateDetailRows($itemkey, $arrParam, $this->arrUnitConversion, $arrOptions);
          
			 
	 }
	  
	 function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
		$sql = 'select
					'.$this->tableName. '.pkey,
                    '.$this->tableName. '.name as value,  
                    '.$this->tableName. '.code as code,
					'.$this->tableName. '.gramasi,
					'.$this->tableName. '.weightunitkey,
					'.$this->tableName. '.timeunitkey,
                    '.$this->tableBrand. '.name as brandname,
                    '.$this->tableName. '.sellingprice, 
                    '.$this->tableName. '.cogs, 
                    '.$this->tableName. '.deftransunitkey, 
                    '.$this->tableName. '.ispackage, 
                    '.$this->tableName. '.itemtype, 
                    '.$this->tableName. '.needsn, 
                    '.$this->tableName. '.reimburse, 
                    '.$this->tableName. '.baseunitkey,
                    '.$this->tableName. '.parentkey,
                    '.$this->tableName. '.isparent,
                    '.$this->tableName. '.shortdescription, 
                    '.$this->tableName. '.isweightfixed, 
                    '.$this->tableItemUnit. '.name as baseunitname
				from 
					'.$this->tableName . ' 
                        left join '.$this->tableBrand.' on 
					       '.$this->tableName . '.brandkey = '.$this->tableBrand.'.pkey
					    left join '.$this->tableItemUnit . ' on
                           '.$this->tableName . '.baseunitkey  = '.$this->tableItemUnit.'.pkey
					    left join '.$this->tableCategory . ' on
                           '.$this->tableName . '.categorykey  = '.$this->tableCategory.'.pkey, 
                    '.$this->tableStatus.'
				where  		  
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
	
		if(!empty($fieldname)){
			
			$sql .= ' and ' ;
			
			if($mustmatch)
				$sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$sql .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria;
	 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit;
        
		return $this->oDbCon->doQuery($sql);	
	} 
	 
	function getSNCOGS($pkey,$sn = array()){
         
       $rs = $this->searchSerialNumber($pkey,'',$sn); 
       $cogs=0;
        
        foreach($rs as $row)
         $cogs += $row['costinbaseunit'];
        
        return $cogs/count($rs); // bagi rata2, utk per baris soalnya 
    }
    
	function getCOGS($pkey, $criteria = ''){
		// jika warehouse isqohcount diupdate, data cogs blm otomatis update sebelum ad pergerakan barang
		  
        $cogsMethod = $this->loadSetting('COGSType');
        
        if ($cogsMethod == 2) {
            $sql = 'select '.$this->tableItemMovement.'.costinbaseunit as cogs from '.$this->tableItemMovement.','.$this->tableWarehouse.' where
                    qtyinbaseunit > 0 and 
					'.$this->tableItemMovement.'.statuskey = 1 and 
					'.$this->tableWarehouse.'.isqohcount = 1 and 
                    '.$this->tableItemMovement.'.itemkey = ' .$this->oDbCon->paramString($pkey) .' order by '.$this->tableItemMovement.'.pkey desc limit 1'; 
           } else{ 
                $sql = 'select 
                ' . $this->tableItemMovement . '.itemkey,
					coalesce(sum(costinbaseunit*qtyinbaseunit) / sum(qtyinbaseunit),0)  as cogs
				from 
					'.$this->tableItemMovement.','.$this->tableWarehouse.'
			 	where 
					'.$this->tableItemMovement.'.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableItemMovement.'.statuskey = 1 and 
					'.$this->tableWarehouse.'.isqohcount = 1 and 
					'.$this->tableItemMovement.'.itemkey in ('.$this->oDbCon->paramString($pkey,',').')';

                    if (!empty($criteria))  $sql .= $criteria; 
                    if (is_array($pkey)) { 
                        $sql .= ' group by itemkey';
                    }
        } 
            
        $rs = $this->oDbCon->doQuery($sql);	

        if (is_array($pkey)) {
        
            return $rs;
        } else {
            if (empty($rs[0]['cogs']))
                $rs[0]['cogs'] = 0;
     
            return $rs[0]['cogs'] ;
        }
    } 
  
  
      
     
    
    function lockUnitConversion($itemkey,$conversionUnitKey){
        
		$arrayToJs = array();
        
		try{ 
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	 
			$sql = 'update '.$this->tableUnitConversion.' set islocked = 1  
                    where refkey = '.$this->oDbCon->paramString($itemkey).' and conversionunitkey = ' . $this->oDbCon->paramString($conversionUnitKey);
			$this->oDbCon->execute($sql);
			 
			$this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    } catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				 
    }
    
    function lockUsedUnit($itemkey){
            // locked DULU yg sudah pernah ad transaksi 
			// lock sesuai modul
            $arrTables = array('item_in_detail','item_out_detail','item_adjustment_detail', 'warehouse_transfer_detail', 
                               'purchase_order_detail','purchase_receive_detail','purchase_return_detail', 'sales_order_detail', 
                               'sales_delivery_detail' , 'sales_car_service_return_detail');
			$rsActiveModule = $this->isActiveModule($arrTables); 
         
            $arrayToJs = array();
        
         	try{ 
			
                    if(!$this->oDbCon->startTrans())
                        throw new Exception($this->errorMsg[100]);
                
                    $conversionUnitKey = $this->getItemUnitConversion($itemkey,'', ' and  isautoinsert = 0 ');
                
                    for ($i=0;$i<count($conversionUnitKey);$i++){
                        for ($j=0;$j<count($arrTables);$j++){
							
							// kalo gk active modulenya
							if(!$rsActiveModule[$arrTables[$j]]) continue;
							
                            $sql = 'select unitkey from '.$arrTables[$j].' where itemkey = '.$itemkey. ' and unitkey = '.$conversionUnitKey[$i]['conversionunitkey'].' limit 1';
                            $rs = $this->oDbCon->doQuery($sql);

                            if (!empty($rs)){
                                $this->lockUnitConversion($itemkey,$conversionUnitKey[$i]['conversionunitkey']);
                                break;
                            }
                        }
                    }
                
                $this->oDbCon->endTrans();
                $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

            } catch(Exception $e){
                $this->oDbCon->rollback();
                $this->addErrorList($arrayToJs,false,$e->getMessage()); 
            }		 
                
                
    }
    
    function generateStructurData($rsItem, $structureType = 1){
        // $structureType -> 1 : list , 2 : detail

        $structureData = '';
        
          switch ($structureType){
                case 1 :    $arrItemList = array();  
                            for($i=0;$i<count($rsItem);$i++){ 
                                $itemList = '
                                 {
                                  "@type":"ListItem",
                                  "position":'.($i+1).',
                                  "url":"'.HTTP_HOST.'products-detail/'.$rsItem[$i]['pkey'].'/'. str_replace($this->arrSearch,$this->arrReplace,$rsItem[$i]['name']).'"
                                 }
                                '; 
                                array_push($arrItemList,$itemList);
                            }

                            $structureData =' 
                                <script type="application/ld+json">
                                {
                                    "@context": "http://schema.org/",
                                    "@type": "ItemList",
                                    "itemListElement": ['.implode(',',$arrItemList).']  
                                }
                                </script>
                            ';

                          break; 

          }

       return $structureData;
        
      
    }
    
    function getLatestEmptyStock($rowsLimit){
        
        $sql = $this->getQuery();
        
        $sql .= '  having qtyonhand = 0 ';
        
        $sql = 'select 
                    '.$this->tableName.'.pkey,
                    '.$this->tableName.'.name,
                    '.$this->tableItemMovement.'.createdon
                from  
                    ('.$sql.') as '.$this->tableName.',
                    '.$this->tableItemMovement.'
                            
                where 
                    '.$this->tableName.'.statuskey = 1 and
                    '.$this->tableItemMovement.'.itemkey =  '.$this->tableName.'.pkey  
                order by 
                    '.$this->tableItemMovement.'.createdon desc
                limit 0,'.  $rowsLimit  
                ;
         
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
         
    }
    
    function searchCategoryGroup($refname = '',$criteria = ''){
        
        $sql = 'select 
                    '.$this->tableItemGroup.'.* 
                from
                    '.$this->tableItemGroup.' 
                where
                    1=1'
            ;
        
        if (!empty($refname))
            $sql .= ' and ref = ' . $this->oDbCon->paramString($refname);
         
        
        if (!empty($criteria))
            $sql .= $criteria;
           
        return $this->oDbCon->doQuery($sql);
    }
    
    
    function getBrandByCategory($categorykey = ''){
        $brand = new Brand();
        
        if (is_array($categorykey))
            $categorykey = implode(',',$categorykey);
        
        $sql = 'select distinct(brandkey) as brandkey from ' . $this->tableName.' where 1=1';
        
        if (!empty($categorykey))
          $sql .= ' and categorykey in ('.$categorykey.')';
        
        $rsCategory = $this->oDbCon->doQuery($sql);
        
        $rsBrand = array();
        if (!empty($rsCategory)){
            $arrCat = array_column($rsCategory,'brandkey');
            $arrCat = implode(',',$arrCat);
            $rsBrand = $brand->searchData($brand->tableName.'.statuskey',1,true,' and '.$brand->tableName.'.pkey in ('.$arrCat.')'); 
        }
          
        return $rsBrand;
    }
    
    function searchItemByGroupCategory($ref){
        $rs = $this->searchCategoryGroup($ref);
        $arrCat = array_column($rs,'categorykey');
        
        $rs = array();
        
        if (!empty($arrCat)){ 
            $arrCat = implode(',',$arrCat);    
            $rs = $this->searchData( $this->tableName.'.statuskey',1,true,' and '.$this->tableName.'.categorykey in ('.$arrCat.')');
        }
            
        return  $rs;
    }
    
    function getTruckingCostDefaultPrice($pkey, $arrCriteria = array()){ 
        // harus bisa depo & terminal & port
        
        $terminal = new Terminal();
        $depot = new Depot();
        
        $terminaltablekey = $this->getTableKeyAndObj($terminal->tableName, array('key')); 
        $depottablekey = $this->getTableKeyAndObj($depot->tableName, array('key'));
        
        $sql = 'select 
                 '.$this->tableName.'.pkey as itemkey,
                 '.$this->tableName.'.name as itemname,
                 '.$terminal->tableCost.'.servicekey,
                 '.$terminal->tableCost.'.price
                from
                 '.$this->tableName.'
                    left join '.$terminal->tableCost.' on '.$terminal->tableCost.'.costkey = '.$this->tableName.'.pkey
                where 
                 '.$terminal->tableCost.'.costkey in ('.$this->oDbCon->paramString($pkey,',') . ') '; 
         
        $locationCriteria = array();
        
        $jobCategoryCiteria= '';
        if(isset($arrCriteria['jobcategorykey']) && !empty($arrCriteria['jobcategorykey']))
           $jobCategoryCiteria = ' AND  ( '.$terminal->tableCost.'.jobcategorykey = '.$this->oDbCon->paramString($arrCriteria['jobcategorykey']).' or  '.$terminal->tableCost.'.jobcategorykey = 0) ';
            
        $serviceCriteria= '';
        $arrServiceDetail = array();
		if(isset($arrCriteria['servicedetail']) && !empty($arrCriteria['servicedetail'])){
              
            $arrServiceDetail = $arrCriteria['servicedetail'];
            
            // hapus yg kosong
            foreach($arrServiceDetail as $key=>$serviceRow){
                if (empty($serviceRow['qty']) || empty($serviceRow['servicekey'])) unset($arrServiceDetail[$key]);
            }  
              
            $serviceCriteria = ' AND  ( '.$terminal->tableCost.'.servicekey in ('.$this->oDbCon->paramString(array_column($arrServiceDetail,'servicekey'),',').') or  '.$terminal->tableCost.'.servicekey = 0) ';
        }
			 
        if(isset($arrCriteria['terminalkey']) && !empty($arrCriteria['terminalkey']))
             array_push ( $locationCriteria , '('.$terminal->tableCost.'.reftabletype = '.$this->oDbCon->paramString($terminaltablekey['key']).'  AND 
                                                    ( '.$terminal->tableCost.'.refkey = '.$this->oDbCon->paramString($arrCriteria['terminalkey']) .' '. $jobCategoryCiteria.$serviceCriteria.' ) '.
                                                ')');
            
        if(isset($arrCriteria['depotkey']) && !empty($arrCriteria['depotkey']))
             array_push ( $locationCriteria , '('.$terminal->tableCost.'.reftabletype = '.$this->oDbCon->paramString($depottablekey['key']).'  AND 
                                                    ( '.$terminal->tableCost.'.refkey = '.$this->oDbCon->paramString($arrCriteria['depotkey']) .' '. $jobCategoryCiteria.$serviceCriteria.' ) '.
                                                ')');
            
    
        if(!empty($locationCriteria)){
            $locationCriteria = implode( ' OR ', $locationCriteria);
            $sql .= ' AND ('.$locationCriteria.')';
        }
        
        $sql .= ' order by '.$terminal->tableCost.'.servicekey asc, '.$terminal->tableCost.'.price asc'; 
        $rs = $this->oDbCon->doQuery($sql);
        
        $amount = 0;
        $totalparty = 0;
        // kalo ad servicekey yg double, otomatis ambil salah satu saja, 
        // karena asc ordernya, otomatis keambil yg paling besar terakhir)
        $rsCost = array_column($rs,'price','servicekey');  
         foreach($arrServiceDetail as $key=>$serviceRow){
            $totalparty += $serviceRow['qty'];
            $cost = $rsCost[$serviceRow['servicekey']];
            $amount +=  ($cost * $serviceRow['qty']); 
        }
        
        // avg
        if($totalparty > 0)
            $amount /= $totalparty; 
        
        return array(
                    'raw' => $rs,
                    'amount' => ceil($amount),
                );
        
    }

    function getVendorPartNumber($pkey='',$criteria=''){
		$sql = 'select 
                    '.$this->tableVendorPartNumber.'.*,
                    '.$this->tableName.'.name as itemname
                from  
                    '.$this->tableVendorPartNumber.'
                        left join '.$this->tableName.' on '.$this->tableVendorPartNumber.'.refkey = '.$this->tableName.'.pkey
                where 1=1';
        
        if(!empty($pkey))
            $sql .= ' and refkey = '.$this->oDbCon->paramString($pkey);	 
        
        if(!empty($criteria))
            $sql .= $criteria;	
         
		return $this->oDbCon->doQuery($sql);
    } 
    
    function normalizeParameter($arrParam, $trim = false){
        
		// kalo gk ad akses update harga dn tipeny edit 
		if(isset($arrParam['hidId']) && !empty($arrParam['hidId'])){ 
			 
			$security = new Security();
			$hasUpdatePriceAccess = $security->isAdminLogin('updateItemSellingPrice',10);  
			
			if(!$hasUpdatePriceAccess) 
				unset($this->arrData['sellingprice']);
			
		} 
 

        if($this->loadSetting('customCodeItem') == 1) {

            $autoCode = new AutoCode();
            $rsAutoCode = $autoCode->searchData('','', true, ' and ' . $autoCode->tableName.'.code = '. $this->oDbCon->paramString($this->tableName) .' ');
            if(!empty($rsAutoCode) || $rsAutoCode[0]['useautocode'] <> 1) {
                $rsCode =  $this->generateCodeFromCategory($arrParam);
                //$this->setLog($rsCode, true);
                if(!empty($rsCode)) 
                    $arrParam['code'] = $rsCode;
            }
        }

		 
		// wajib ada
        $arrParam['selBaseUnitKey'] = (isset( $arrParam['selBaseUnitKey'])) ?  $arrParam['selBaseUnitKey'] : 1; 
        $arrParam['selDefaultTransUnitKey'] = (isset( $arrParam['selDefaultTransUnitKey'])) ?  $arrParam['selDefaultTransUnitKey'] : $arrParam['selBaseUnitKey'];
        
        // khusus dari API
        if(isset($arrParam['_mnv-api'])){  
             if (!empty($arrParam['hidParentItemKey']))
                 $arrParam['chkIsVariant'] = 1; 
        }
        
        $isVariant = (isset($arrParam['chkIsVariant'])) ? $arrParam['chkIsVariant'] : 0;
        
        if(!$isVariant){
			$arrParam['hidParentItemKey']=""; 
            $arrParam['chkIsPrimary']=0; 
		}else{
			$rsParent = $this->getDataRowById($arrParam['hidParentItemKey']);
			$arrParam['hidCategoryKey'] = $rsParent[0]['categorykey'];
			$arrParam['hidBrandKey'] = $rsParent[0]['brandkey'];
		}
		 
        // kalo dr import gk udpate detail
        if (isset($arrParam['_isImport_']) && $arrParam['_isImport_']){ 
            $this->arrData['pkey'] = array('pkey'); 
        }else{
            $arrParam['selConversionUnitKey'] = (isset( $arrParam['selConversionUnitKey'])) ?  $arrParam['selConversionUnitKey'] : array(); 
            $arrParam['txtConversionMultiplier'] = (isset( $arrParam['txtConversionMultiplier'])) ?  $arrParam['txtConversionMultiplier'] : array(); 
            for ($i=0;$i<count($arrParam['selConversionUnitKey']);$i++){
                $arrParam['conversionBaseUnitKey'][$i] = $arrParam['selBaseUnitKey'];

                // utk remove kalo konversi ke baseunit sendiri 
                if( $arrParam['selConversionUnitKey'][$i] == $arrParam['selBaseUnitKey']){ 
                    $arrParam['txtConversionMultiplier'][$i] = 0;
                }
            }

            $rsItemUnitConversion = $this->getAvailableUnit($arrParam['pkey'],' and '.$this->tableUnitConversion.'.islocked = 1 and '.$this->tableUnitConversion.'.conversionunitkey <> '.$this->tableUnitConversion.'.baseunitkey');	 
            $this->retrieveReadonlyDataRow($arrParam, $rsItemUnitConversion, $this->arrUnitConversion,'conversionunitkey','hidConversionDetailKey' ); 
 	    }
       	
        $arrParam['vendorpartnumbercache']  = '';  
        if(!isset($arrParam['partNumber'])) 
            $arrParam['partNumber']  = array();
             
        $partNumber = $arrParam['partNumber'];
        $arrPart = array();
        for ($i=0;$i<count($partNumber);$i++){
            if(empty($partNumber[$i])) continue; 
            if(in_array($partNumber[$i],$arrPart)) continue; 
            array_push($arrPart,$partNumber[$i]); 
        }
          
        $arrPartNumber = implode(" ",$arrPart);
        if(!empty($arrPartNumber))
            $arrParam['vendorpartnumbercache'] = $arrPartNumber; 
             
        
        $arrParam['sellingPrice'] = (isset( $arrParam['sellingPrice'])) ?  $arrParam['sellingPrice'] : 0;
        $arrParam['secondPrice'] = (isset( $arrParam['secondPrice'])) ?  $arrParam['secondPrice'] : 0;
        $arrParam['maxStockQty'] = (isset( $arrParam['maxStockQty'])) ?  $arrParam['maxStockQty'] : 0;
        $arrParam['minStockQty'] = (isset( $arrParam['minStockQty'])) ?  $arrParam['minStockQty'] : 0;
        
        $minStock = $this->unformatNumber($arrParam['minStockQty']);
        $maxStock = $this->unformatNumber($arrParam['maxStockQty']);
         
        if($maxStock <> 0 && $minStock > $maxStock)
            $arrParam['minStockQty'] = 0;
                  
        $arrParam['hidParentKey'] = (isset( $arrParam['hidParentKey'])) ?  $arrParam['hidParentKey'] : 0; 
        $arrParam['gramasi'] = (isset( $arrParam['gramasi'])) ?  $arrParam['gramasi'] : 0;
        $arrParam['selCommissionType'] = (isset( $arrParam['selCommissionType'])) ?  $arrParam['selCommissionType'] : 1; 
        $arrParam['hidBrandKey'] = (empty($arrParam['hidBrandKey'])) ? 0 : $arrParam['hidBrandKey']; 
        $arrParam['chkIsPublish'] = (isset($arrParam['chkIsPublish'])) ?  $arrParam['chkIsPublish'] : 0 ;  
        $arrParam['chkNeedSN'] = (!empty($arrParam['chkNeedSN'])) ?  $arrParam['chkNeedSN'] : 0 ;  
  
       /* $arrParam['item-image-variant-uploader'] = (isset( $arrParam['item-image-variant-uploader'])) ?  $arrParam['item-image-variant-uploader'] : ''; 
        $arrParam['item-file-uploader'] = (isset( $arrParam['item-file-uploader'])) ?  $arrParam['item-file-uploader'] : '';  
        $arrParam['token-item-file-uploader'] = (isset( $arrParam['token-item-file-uploader'])) ?  $arrParam['token-item-file-uploader'] : ''; 
        $arrParam['item-image-uploader'] = (isset( $arrParam['item-image-uploader'])) ?  $arrParam['item-image-uploader'] : ''; 
        $arrParam['token-item-image-uploader'] = (isset( $arrParam['token-item-image-uploader'])) ?  $arrParam['token-item-image-uploader'] : ''; */ 
      
        $arrParam['mileage'] = (isset( $arrParam['mileage'])) ?  $arrParam['mileage'] : 0;
        $arrParam['lifespan'] = (isset( $arrParam['lifespan'])) ?  $arrParam['lifespan'] : 0;  
        $arrParam['commission'] = (isset( $arrParam['commission'])) ?  $arrParam['commission'] : 0;  
        $arrParam['hidContentOfPackageKey'] = (empty($arrParam['hidContentOfPackageKey'])) ? 0 : $arrParam['hidContentOfPackageKey']; 
        $arrParam['selOilType'] = (isset( $arrParam['selOilType'])) ?  $arrParam['selOilType'] : 0;
        $arrParam['hidItemPackageKey'] = (isset( $arrParam['hidItemPackageKey'])) ?  $arrParam['hidItemPackageKey'] : array();
        $arrParam['qty'] = (isset( $arrParam['qty'])) ?  $arrParam['qty'] : array(); 
        $arrParam['hidDetailKey'] = (isset( $arrParam['hidDetailKey'])) ?  $arrParam['hidDetailKey'] : array(); 
        $arrParam['partNumber'] = (isset( $arrParam['partNumber'])) ?  $arrParam['partNumber'] : array(); 
        $arrParam['commissionValue'] = (isset( $arrParam['commissionValue'])) ?  $arrParam['commissionValue'] : 0;
        $arrParam['cashBackValue'] = (isset( $arrParam['cashBackValue'])) ?  $arrParam['cashBackValue'] : 0;
        $arrParam['hidInventoryCOAKey'] = (isset( $arrParam['hidInventoryCOAKey'])) ?  $arrParam['hidInventoryCOAKey'] : 0;
        $arrParam['hidInventoryTempCOAKey'] = (isset( $arrParam['hidInventoryTempCOAKey'])) ?  $arrParam['hidInventoryTempCOAKey'] : 0;
        $arrParam['hidRevenueCOAKey'] = (isset( $arrParam['hidRevenueCOAKey'])) ?  $arrParam['hidRevenueCOAKey'] : 0;
        $arrParam['hidCostCOAKey'] = (isset( $arrParam['hidCostCOAKey'])) ?  $arrParam['hidCostCOAKey'] : 0;
        $arrParam['selCashBackType'] = (isset( $arrParam['selCashBackType'])) ?  $arrParam['selCashBackType'] : 1; 
	    $arrParam['selDivisionKey'] = (isset( $arrParam['selDivisionKey'])) ?  $arrParam['selDivisionKey'] : 0; 
        $arrParam['cashback'] = (isset( $arrParam['cashback'])) ?  $arrParam['cashback'] : 0;
        $arrParam['tag'] = (isset( $arrParam['tag'])) ?  $arrParam['tag'] : '';  
        $arrParam['hidItemSpecificationKey'] =  (isset( $arrParam['hidItemSpecificationKey'])) ?  $arrParam['hidItemSpecificationKey'] : array();
        $arrParam['specificationValue'] =  (isset( $arrParam['specificationValue'])) ?  $arrParam['specificationValue'] : array();
      
        
        $rsDefaultWeightUnit = $this->getSystemWeight();
        $arrParam['selWeightUnit'] = (isset( $arrParam['selWeightUnit'])) ?  $arrParam['selWeightUnit'] : $rsDefaultWeightUnit[0]['pkey']; 
             
        $arrParam['width'] = (isset( $arrParam['width'])) ?  $arrParam['width'] : 0;
        $arrParam['length'] = (isset( $arrParam['length'])) ?  $arrParam['length'] : 0;
        $arrParam['height'] = (isset( $arrParam['height'])) ?  $arrParam['height'] : 0; 
        
        $arrParam['selWarranty'] = (isset( $arrParam['selWarranty']) && !empty($arrParam['selWarranty'])) ?  $arrParam['selWarranty'] : 0;  
        $arrParam['selWarrantyVendor'] = (isset( $arrParam['selWarrantyVendor']) && !empty($arrParam['selWarrantyVendor'])) ?  $arrParam['selWarrantyVendor'] : 0;  
        $arrParam['pointValue'] = (isset( $arrParam['pointValue'])) ?  $arrParam['pointValue'] : 0;  
           
        $arrParam['width'] = (isset( $arrParam['width'])) ?  $arrParam['width'] : 0; 
        $arrParam['length'] = (isset( $arrParam['length'])) ?  $arrParam['length'] : 0; 
        $arrParam['height'] = (isset( $arrParam['height'])) ?  $arrParam['height'] : 0; 
        
 
 
        $arrParam['itemType'] = $this->itemType; 
        
        // variable marketplace 
         
        $arrParam['sellingPrice'] = floatval($this->unFormatNumber($arrParam['sellingPrice']));  
        $arrParam['gramasi'] = floatval($this->unFormatNumber($arrParam['gramasi'])); 
        $arrParam['length'] = floatval($this->unFormatNumber($arrParam['length'])); 
        $arrParam['width'] = floatval($this->unFormatNumber($arrParam['width'])); 
        $arrParam['height'] = floatval($this->unFormatNumber($arrParam['height'])); 
        
        $arrParam['length'] = ($arrParam['length'] < 0) ? 0 : $arrParam['length'];
        $arrParam['width'] = ($arrParam['width'] < 0) ? 0 : $arrParam['width'];
        $arrParam['height'] = ($arrParam['height'] < 0) ? 0 : $arrParam['height'];
        
        $arrParam['hidVariantDetailKey'] = array();
        $arrParam['hidVariantMarketplaceKey'] = array();
        $arrParam['selVariant'] = array();
        $arrParam['selOption'] = array();
        
		if($this->isActiveMarketplace){
			$marketplace = new Marketplace();
			
			$marketplaceObjs = $marketplace->getMarketplaceObj();
			if(!empty($marketplaceObjs)){
				
				// hilangin logistics yg gk dicentang
				for($i=0;$i<count($arrParam['hidLogisticsRefKey']);$i++){ 
					if(empty($arrParam['chkLogisticsMarketplace'][$i]))
						$arrParam['hidLogisticsRefKey'][$i]='';
				}
				
                // isi ulang attribute value id, kepake utk shopee saja saat ini harusnyaa
                $arrParam['attributeValueId'] = array();
				for($i=0;$i<count($arrParam['attributeValue']);$i++){ 
                    $valueId = 0;
                    if(isset($arrParam['hidRawOpt']) && !empty($arrParam['hidRawOpt'][$i])){
                        $rawOpt = json_decode(base64_decode($arrParam['hidRawOpt'][$i]),true);    
                         
                        $rawOpt = array_column($rawOpt,null,'display_value_name');
                        
                        if(isset($rawOpt[ $arrParam['attributeValue'][$i] ]))
                            $valueId = $rawOpt[ $arrParam['attributeValue'][$i] ]['value_id'];
                    }
                    
                    $arrParam['attributeValueId'][$i] = $valueId;
                }
                
				// validasi field2 primary yg wajib diisi
				$arrValidateField = array();
				foreach($marketplaceObjs as $marketplaceRow){
					 $marketplaceKey = $marketplaceRow['key'];
					 $marketplaceProviderKey = $marketplaceRow['refmarketplacekey'];  
					 $marketplaceObj = $marketplaceRow['obj'];

					 $rsMPCategory = $marketplaceObj->getCategoryUsedForMarketplace($arrParam['hidCategoryKey']); 

					
					 if ($marketplaceProviderKey == MARKETPLACE['tokopedia']){  
						$arrVariantKey = $arrParam['selVariant'.$marketplaceKey]; // gk bisa jadi satu diatas, karena beda marketplacekey nya
						$arrOptionVariantKey = $arrParam['selOption'.$marketplaceKey]; 
 

						for($i=0;$i<count($arrVariantKey);$i++){
							$variantkey = $arrVariantKey[$i];

							if(!array($arrOptionVariantKey[$i])) $arrOptionVariantKey[$i] = array($arrOptionVariantKey[$i]); // buat jaga2, kedepannya biar bisa multi select

							// convert ke format json [key : {hexcode,value}] // gk lg  

							$arrSaveVariant = array();
							foreach($arrOptionVariantKey as $variantId){ 
								$arrSaveVariant = array('hexcode' => '', 'value' => $arrOptionVariantKey[$i]);   
							}

							// kalo parent, gk perlu save option
							$arrSaveVariant = ($isVariant == 1) ? json_encode($arrSaveVariant) : '';

							// kalo variant, variant harus sama dengan parent 
							array_push($arrParam['hidVariantDetailKey'],''); // ini nanti test bisa gk pake yg ada
							array_push($arrParam['hidVariantMarketplaceKey'],$marketplaceKey); 
							array_push($arrParam['selVariant'],$variantkey); 
							array_push($arrParam['selOption'],$arrSaveVariant);
						} 

					 }  else if ($marketplaceProviderKey == MARKETPLACE['shopee']){
							//$arrHideVariantDetailKey = $arrParam['selVariant'.$marketplaceKey]; // gk bisa, kecuali ad marketplacekeyny jg
							$arrVariantKey = $arrParam['selVariant'.$marketplaceKey];
							$arrOptionVariantKey = $arrParam['selOption'.$marketplaceKey]; 

							for($i=0;$i<count($arrVariantKey);$i++){
								array_push($arrParam['hidVariantDetailKey'],'');
								array_push($arrParam['hidVariantMarketplaceKey'],$marketplaceKey); 
								array_push($arrParam['selVariant'],$arrVariantKey[$i]); 
								array_push($arrParam['selOption'],$arrOptionVariantKey[$i]);   
							}
					 }

					 $rsExclude = $marketplaceObj->getExcludeAttributes(true);  
					 foreach($rsExclude as $row){     

							$attributekey = $row['attributekey'];

							// kalo tipenya refkey, harus ambil namanya...
							$value = $arrParam[$row['paramname']];
 
							// kalo shoppe attributekey harus ditarik ulang, karena yg skrg exclude nariknya label...
							if ($marketplaceProviderKey == MARKETPLACE['shopee']){  

								 // untuk merk, shopee beda karena attributeidnya beda2 
								 // HATI2, jgn dipindahin kebawah karena attributekey dibawah berubah
								if(strtolower($attributekey) == 'merek'){
									$rsBrand = $marketplaceObj->getBrandUsedForMarketplace($arrParam['hidBrandKey']);  
									$value = ucwords($rsBrand[0]['name']); 
								}

								$attributekey = $marketplaceObj->getAttributeKeyByLabel($attributekey,intval($rsMPCategory[0]['marketplacecategorykey']));
								if (empty($attributekey))  continue; // bisa gk ketemu kalo merek

							}

							array_push($arrParam['hidAttributeDetailKey'],'');
							array_push($arrParam['hidMarketplaceKey'],$marketplaceKey);
							array_push($arrParam['hidCategoryAttributeKey'],$attributekey);
							array_push($arrParam['attributeValue'],$value);
					 } 

				}

			} 

		}
		
		// hati2, kalo diatas, variable2 yg awalnya gk ad isi ketrim
        
        
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
 
        return $arrParam; 
    }
    
    function reupdateMarketplaceAttribute($arrPkey){
        // utk dipanggil dari klik kanan sync
        
        if(!$this->isActiveMarketplace) return;
		
        $marketplace = new Marketplace(); 
        
        $arrNewAttr = array();
        
        $rsItem = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.brandkey',$this->tableName.'.categorykey'),
                                       ' and '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').')'
                                      );
        $rsItem = array_column($rsItem,null,'pkey');
        
        $marketplaceObjs = $marketplace->getMarketplaceObj();
        if(!empty($marketplaceObjs)){
            
            foreach($arrPkey as $itemkey){
                
                foreach($marketplaceObjs as $marketplaceRow){
                     $marketplaceKey = $marketplaceRow['key'];
					 $marketplaceProviderKey = $marketplaceRow['refmarketplacekey'];  
                     $marketplaceObj = $marketplaceRow['obj'];

                     $rsExclude = $marketplaceObj->getExcludeAttributes(true);  

                     foreach($rsExclude as $row){     
                            // sementara cuma BRAND

                            $attributekey = $row['attributekey'];

                            // kalo tipenya refkey, harus ambil namanya...
                            //$value = $arrParam[$row['paramname']]; 
                            $value = '';
                         

                            // kalo shoppe attributekey harus ditarik ulang, karena yg skrg exclude nariknya label...
                            if ($marketplaceProviderKey == MARKETPLACE['shopee']){  

                                 // untuk merk, shopee beda karena attributeidnya beda2 
                                 // HATI2, jgn dipindahin kebawah karena attributekey dibawah berubah
                                if(strtolower($attributekey) == 'merek'){
                                    $rsBrand = $marketplaceObj->getBrandUsedForMarketplace($rsItem[$itemkey]['brandkey']);  
                                    $value = ucwords($rsBrand[0]['name']); 
                                }

                                $rsMPCategory = $marketplaceObj->getCategoryUsedForMarketplace($rsItem[$itemkey]['categorykey']); 

                                $attributekey = $marketplaceObj->getAttributeKeyByLabel($attributekey,intval($rsMPCategory[0]['marketplacecategorykey']));
                                if (empty($attributekey))  continue; // bisa gk ketemu kalo merek

                            }

                            if($value == '') continue;

                            array_push($arrNewAttr, array( 
                                'itemkey' => $itemkey,
                                'marketplacekey' => $marketplaceKey,
                                'attributekey' => $attributekey, 
                                'attributeValue' => $value,
                            ));
                          
                     } 

                }

                // insert kalo attributenya blm ada...
                foreach($arrNewAttr as $newAttr){
                    $sql = 'select pkey from '.$this->tableMarketplaceCategoryAttributes.' 
                            where 
                            refkey = '.$this->oDbCon->paramString($newAttr['itemkey']).'  and 
                            marketplacekey = '.$this->oDbCon->paramString($newAttr['marketplacekey']).' and 
                            attributekey =  '.$this->oDbCon->paramString($newAttr['attributekey']);
                    $rsAttr = $this->oDbCon->doQuery($sql);
                  
                        try{ 

                            if(!$this->oDbCon->startTrans())
                                throw new Exception($this->errorMsg[100]);

                            // kedepan perlu diupdate utk UPDATE, misalnya nama merk diganti dr master, harus bisa resync ulang
                              
                            if(empty($rsAttr)){ 
                                $sql = 'insert into  '.$this->tableMarketplaceCategoryAttributes.'(refkey,marketplacekey,attributekey,value)  
                                values ( 
                                '.$this->oDbCon->paramString($newAttr['itemkey']).',
                                '.$this->oDbCon->paramString($newAttr['marketplacekey']).',
                                '.$this->oDbCon->paramString($newAttr['attributekey']).', 
                                '.$this->oDbCon->paramString($newAttr['attributeValue']).'
                                )';
                            }else{
                                $sql = 'update  '.$this->tableMarketplaceCategoryAttributes.' 
                                        set  
                                            value =  '.$this->oDbCon->paramString($newAttr['attributeValue']).'
                                        where pkey = '.$this->oDbCon->paramString($rsAttr[0]['pkey']).' 
                                ';
                            }

                            $this->oDbCon->execute($sql);

                            $this->oDbCon->endTrans(); 

                        } catch(Exception $e){
                            $this->oDbCon->rollback(); 
                        }	
                    
                }
                
                
            } 
        } 
        
    }
    
    /*function getPartNumberInformation ($vendorPartNumberKey='' ,$searchCriteria = ''){
        	$sql = 'select
					'.$this->tableVendorPartNumber. '.*,
                     '.$this->tableName.'.name as itemname,
                     '.$this->tableName.'.name as itemkey
				from 
					'.$this->tableVendorPartNumber . ',
                     '.$this->tableName.'
				where 
                    '.$this->tableVendorPartNumber . '.refkey = '. $this->tableName.'.pkey and 
                    '.$this->tableVendorPartNumber . '.pkey = '. $this->oDbCon->paramString($vendorPartNumberKey) ;
	   
            if($searchCriteria <> '')
                $sql .= ' ' .$searchCriteria;
        
		return $this->oDbCon->doQuery($sql);	
        
    }*/
    
    function searchVendorPartNumberForAutoComplete ($itemkey='', $searchCriteria = ''){
        	$sql = 'select
					'.$this->tableVendorPartNumber. '.pkey,
                    '.$this->tableVendorPartNumber. '.partnumber as value, 
                    '.$this->tableName.'.pkey as itemkey,
                    '.$this->tableName.'.name as itemname,
                    '.$this->tableName.'.needsn
				from 
					'.$this->tableVendorPartNumber . ',
                     '.$this->tableName.'
				where 
                    '.$this->tableVendorPartNumber . '.refkey = '.$this->tableName.'.pkey
			';
	  
         if($searchCriteria <> '')
            $sql .= ' ' .$searchCriteria;
	
        
		return $this->oDbCon->doQuery($sql);	
        
    }

     function searchSerialNumber($itemkey='',$vendorpartnumberkey = '', $sn = '', $warehousekey = '', $criteria = ''){
         $sql  = 'SELECT 
                    '.$this->tableSerialNumber.'.*,  
                    '.$this->tableName.'.name as itemname,
                    '.$this->tableName.'.code as itemcode, 
                    '.$this->tableBrand.'.name as brandname, 
                    '.$this->tableWarehouse.'.name as warehousename
                 FROM 
                    '.$this->tableName.' 
                        left join  '.$this->tableBrand.' on  '.$this->tableName.'.brandkey = '.$this->tableBrand.'.pkey,
                    '.$this->tableSerialNumber.'
                        left join  '.$this->tableWarehouse.' on  '.$this->tableSerialNumber.'.warehousekey = '.$this->tableWarehouse.'.pkey
                 WHERE 
                    '.$this->tableSerialNumber.'.itemkey = '.$this->tableName.'.pkey ';
                    // '.$this->tableSerialNumber.'.vendorpartnumberkey = '.$this->tableVendorPartNumber.'.pkey ';
         
         
         if (!empty($itemkey))
             $sql .= 'and '.$this->tableSerialNumber.'.itemkey in ('.$this->oDbCon->paramString($itemkey,',').')';
           
         if (!empty($vendorpartnumberkey))
             $sql .= 'and '.$this->tableSerialNumber.'.vendorpartnumberkey = '.$this->oDbCon->paramString($vendorpartnumberkey);
          
         if (!empty($sn)){ 
             if(!is_array($sn)) $sn = array($sn);
             $sql .= 'and '.$this->tableSerialNumber.'.serialnumber in ('.$this->oDbCon->paramString($sn,',').')';
         }
         
         if (!empty($warehousekey))
             $sql .= 'and '.$this->tableSerialNumber.'.warehousekey = '.$this->oDbCon->paramString($warehousekey);
         
         if (!empty($criteria))
             $sql .= $criteria;
         
         // default
         $sql .= ' order by '.$this->tableSerialNumber.'.pkey desc';
         
         //$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql); 
    }

//     function searchSerialNumber($itemkey='',$vendorpartnumberkey = '', $sn = '', $warehousekey = '', $criteria = ''){
//         $sql  = 'SELECT 
//                    '.$this->tableSerialNumber.'.*,  
//                    '.$this->tableName.'.name as itemname,
//                    '.$this->tableName.'.code as itemcode, 
//                    '.$this->tableBrand.'.name as brandname,
//                    '.$this->tableVendorPartNumber.'.partnumber,
//                    datediff(warrantyvendorperiodexpireddate,now())  as warrantyvendorperiodexpireddatediff,
//                    datediff(warrantyperiodexpireddate,now())  as warrantyperiodexpireddatediff 
//                 FROM 
//                    '.$this->tableName.' left join 
//                       '.$this->tableBrand.' on  '.$this->tableName.'.brandkey = '.$this->tableBrand.'.pkey,
//                    '.$this->tableSerialNumber.',
//                    '.$this->tableVendorPartNumber .' 
//                 WHERE 
//                    '.$this->tableSerialNumber.'.itemkey = '.$this->tableName.'.pkey and
//                    '.$this->tableSerialNumber.'.vendorpartnumberkey = '.$this->tableVendorPartNumber.'.pkey ';
        
        
//         if (!empty($itemkey))
//             $sql .= 'and '.$this->tableSerialNumber.'.itemkey = '.$this->oDbCon->paramString($itemkey);
          
//         if (!empty($vendorpartnumberkey))
//             $sql .= 'and '.$this->tableSerialNumber.'.vendorpartnumberkey = '.$this->oDbCon->paramString($vendorpartnumberkey);
         
//         if (!empty($sn))
//             $sql .= 'and '.$this->tableSerialNumber.'.serialnumber = '.$this->oDbCon->paramString($sn);
         
//         if (!empty($warehousekey))
//             $sql .= 'and '.$this->tableSerialNumber.'.warehousekey = '.$this->oDbCon->paramString($warehousekey);
        
//         if (!empty($criteria))
//             $sql .= $criteria;
           
//        return $this->oDbCon->doQuery($sql); 
//    }
     function getInventoryCOAKey($itemkey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsItem = $this->searchDataRow(array($this->tableName.'.pkey',
											 $this->tableName.'.itemtype',
											 $this->tableName.'.costcoakey',
											 $this->tableName.'.inventorycoakey',
											 $this->tableName.'.prepaidexpensecoakey', 
											 $this->tableName.'.isamortized',
                                             
                                            ),
									   ' and '. $this->tableName.'.pkey = '. $this->oDbCon->paramString($itemkey)
									   );
         
		$coakey = 0;
		 
		// cek dulu jasa atau barang
		$isService = ($rsItem[0]['itemtype'] == SERVICE || $rsItem[0]['itemtype'] == TRUCKING_SERVICE) ? true : false;
		 
		if($isService){
            
            // kalo  amortisassi 
			 if ( $rsItem[0]['isamortized'] == 1){
                 if (!empty($rsItem[0]['prepaidexpensecoakey'])) 
                    $coakey = $rsItem[0]['prepaidexpensecoakey'];
             } else{ 
                 if (!empty($rsItem[0]['costcoakey'])) 
                    $coakey = $rsItem[0]['costcoakey'];
             }
            
		}else{
			 if (!empty($rsItem[0]['inventorycoakey']))  
				$coakey = $rsItem[0]['inventorycoakey']; 
			
//			else{  
//				$rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName,  $warehousekey);   
//				$coakey = $rsCOA[0]['coakey'];
//			} 
		}
		 
		if(empty($coakey)){
			$rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName,  $warehousekey);   
			$coakey = $rsCOA[0]['coakey'];
		}
		
        return $coakey;
    }
    
     function getInventoryTempCOAKey($itemkey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsItem = $this->getDataRowById($itemkey);
         
        if (!empty($rsItem[0]['inventorytempcoakey'])){  
            $coakey = $rsItem[0]['inventorytempcoakey'];
        }else{  
            $rsCOA = $coaLink->getCOALink ('inventorytemp', $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
    
    
     function getRevenueCOAKey($itemkey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsItem = $this->getDataRowById($itemkey);
         
        if (!empty($rsItem[0]['revenuecoakey'])){  
            $coakey = $rsItem[0]['revenuecoakey'];
        }else{   
            $coa = ($rsItem[0]['itemtype'] == SERVICE) ? 'salesservice' : 'salesretail';
            $rsCOA = $coaLink->getCOALink ($coa, $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
        
        return $coakey;
    }
	
      
	function getCostCOAKey($itemkey,$warehousekey,$warehouseDefaultCOAKey ,$prepaidExpense = false){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        $rsItem = $this->getDataRowById($itemkey);
          
		$costCoaField = ($prepaidExpense) ? 'prepaidexpensecoakey' : 'costcoakey';
		 
        if (!empty($rsItem[0][$costCoaField])){  
            $coakey = $rsItem[0][$costCoaField];
        }else{    
			
			// kalo prepaidexpense, overwrite
			if($prepaidExpense)
				$warehouseDefaultCOAKey = 'prepaidexpense';
				
            $rsCOA = $coaLink->getCOALink ($warehouseDefaultCOAKey, $warehouse->tableName,  $warehousekey);   
            $coakey = $rsCOA[0]['coakey'];
        }
         
        return $coakey;
    }
 
    function splitQtyBaseOnUnit($itemkey, $qtyInBaseUnit){ 
       $rsUnit = $this->getAvailableUnit($itemkey,'',' order by conversionmultiplier desc');
       if (count($rsUnit) == 1) return $this->formatNumber($qtyInBaseUnit) . ' ' .$rsUnit[0]['unitname'];
           
        $returnName = array();
        foreach($rsUnit as $unit){ 
            $value = floor($qtyInBaseUnit / $unit['conversionmultiplier']);  
            if($value == 0) continue;
            
            array_push($returnName, $this->formatNumber($value) . ' ' .$unit['unitname']);
            $qtyInBaseUnit = $qtyInBaseUnit %  $unit['conversionmultiplier'];
        }
    
        return (!empty($returnName)) ?  implode(' ', $returnName) : '0 '. $rsUnit[count($rsUnit)-1]['unitname'];
    }
/*    
    
    function getSNInInformation($serialNumber){
        // digunakan untuk mencari tgl yg diakui sebagai tgl garansi vendor
        // 1. barang masuk - Item In - cari yang pertama kali masuk
        // kecuali kalo masuk barang dr vendor, SN sama ( ini hampir gk mungkin )
        // 2. barang kembalian dr service principal  ( replacement / refurbish ) - cari yang terakhir kali masuk, tp masuk bisa service / replacement
        
        $itemMovement = new ItemMovement();
        $rs = $itemMovement->searchSNMovement('','', $serialNumber, '', $criteria = '', $order = '');
        
        
    }
    */
    
    function getSNInformation($serialNumber){ 
        if(empty($serialNumber)) return;
             
        $itemOut = new ItemOut();
        $itemOutDelivery = new ItemOutDelivery();
        $itemMovement = new ItemMovement();

        $refTableItemOut = $this->getTableKeyAndObj($itemOut->tableName);
        $refTableItemOut =  $refTableItemOut['key'];
        
        $refTableItemOutDelivery = $this->getTableKeyAndObj($itemOutDelivery->tableName);
        $refTableItemOutDelivery =  $refTableItemOutDelivery['key'];
        
        $rsItemSN = $this->searchSerialNumber('','',$serialNumber);  
        
        if(empty($rsItemSN))  return; 
         
        // barang masuk, cek dr table ap
        // buat handle kalo dr import
        if(!empty($rsItemSN[0]['reftabletype'])){  
            $itemInObj = $this->getTableNameAndObjById($rsItemSN[0]['reftabletype']); 
            if(!empty($itemInObj['obj'])){ 
                $itemInObj = $itemInObj['obj']; 
                $rsItemIn = $itemInObj->searchData($itemInObj->tableName.'.pkey', $rsItemSN[0]['refheaderkey'],true,'','order by trdate asc', 'limit 1');

                $rs[0]['itemincode'] = $rsItemIn[0]['code'];
                $rs[0]['itemindate'] = $rsItemIn[0]['trdate'];
                $rs[0]['invoicedate'] = $rsItemIn[0]['trinvoicedate'];
                $rs[0]['suppliername'] = $rsItemIn[0]['suppliername'];
            }

        }
        
        $rs = array();
	// belum tau ini kepake gk
        $rs[0]['pkey'] = $rsItemSN[0]['pkey'];
        $rs[0]['refkey'] = $rsItemSN[0]['refkey'];

        $rs[0]['itemcode'] = $rsItemSN[0]['itemcode'];
        $rs[0]['itemname'] = $rsItemSN[0]['itemname'];
        $rs[0]['vendorpartnumber'] = $rsItemSN[0]['partnumber'];
        $rs[0]['vendorpartnumberkey'] = $rsItemSN[0]['vendorpartnumberkey'];
        $rs[0]['warrantyvendorperiodkey'] = $rsItemSN[0]['warrantyvendorperiodkey'];
        $rs[0]['warrantyvendorperiod'] = $rsItemSN[0]['warrantyvendorperiod'];
        $rs[0]['warrantyvendorperiodexpireddate'] = $rsItemSN[0]['warrantyvendorperiodexpireddate'];
        $rs[0]['warrantyperiodexpireddate'] = $rsItemSN[0]['warrantyperiodexpireddate'];
        $rs[0]['warrantyperiodexpireddatediff'] = $rsItemSN[0]['warrantyperiodexpireddatediff'];
        $rs[0]['warrantyvendorperiodexpireddatediff'] = $rsItemSN[0]['warrantyvendorperiodexpireddatediff'];
     
         
        // barang keluar, cek dr table ap
        // ini harus diupdate, karena barang bisa masuk lg dr pembelian.
        // nanti di informasi sn munculin pergerakannya saja 
        
        
        // buat handle kalo dr import
        if(!empty($rsItemSN[0]['itemoutreftabletype'])){   
            if(!empty($rsItemSN[0]['itemoutreftabletype'])){
                $itemOutObj = $this->getTableNameAndObjById($rsItemSN[0]['itemoutreftabletype']);
                $itemOutObj = $itemOutObj['obj'];
                $rsItemOut = $itemOutObj->searchData($itemOutObj->tableName.'.pkey', $rsItemSN[0]['itemoutrefheaderkey'],true,'','order by trdate asc', 'limit 1');

                $rs[0]['itemoutcode'] = $rsItemOut[0]['code'];
                $rs[0]['itemoutdate'] = $rsItemOut[0]['trdate'];
                $rs[0]['recipientkey'] = $rsItemOut[0]['recipientkey'];
                $rs[0]['recipientname'] = $rsItemOut[0]['recipientname'];
            }
        }
        
        
        $rs[0]['warrantyperiodkey'] = $rsItemSN[0]['warrantyperiodkey'];
        $rs[0]['warrantyperiod'] = $rsItemSN[0]['warrantyperiod'];
        $rs[0]['warrantyperiodexpireddate'] = $rsItemSN[0]['warrantyperiodexpireddate'];
        $rs[0]['warrantyperiodexpireddatediff'] = $rsItemSN[0]['warrantyperiodexpireddatediff'];
               
        return $rs;
    }
        
    function getBrandFilter($rsItem){
       $brandkey = array_unique(array_column($rsItem, 'brandkey'));
        
       $brandkeyCriteria = '';    
       if (!empty($brandkey)){
        $brandkey = implode(',',$brandkey);
        $brandkeyCriteria = ' where pkey in ('.$brandkey.') ';   
       }
        
       $sql = 'select pkey,name from ' . $this->tableBrand . $brandkeyCriteria;
        
       return  $this->oDbCon->doQuery($sql);
            
    }
    
    function getMaxAging($itemkey, $arrWarehouse = array()){
        
        $criteria = array();
        
        //warehouse pastikan yg masi di gudang
        $warehouseCriteria  = (!empty($arrWarehouse)) ?  $this->tableSerialNumber.'.warehousekey in ('. implode(",", $this->oDbCon->paramString($arrWarehouse)).') ' :  $this->tableSerialNumber.'.warehousekey <> 0 ';
        array_push($criteria, $warehouseCriteria);
            
        // tgl masuk  
        array_push($criteria,   $this->tableSerialNumber.'.itemindate <> \'0000-00-00\'' );
       
        $sql = 'select 
                    coalesce(datediff(now(),min('.$this->tableSerialNumber.'.itemindate)),0) as maxaging,
                    '.$this->tableSerialNumber.'.itemindate
                from 
                    '.$this->tableSerialNumber.'
                where
                    '.$this->tableSerialNumber.'.itemkey = '.$this->oDbCon->paramString($itemkey).' 
                '; 
        
        if (!empty($criteria))
            $sql .= ' and ' . implode(' and ', $criteria);
         
        $rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }
    
    function getCustomerItem($customerkey){
        $sql = 'select distinct(itemkey) as itemkey from '.$this->tableItemDepotMovement.' where customerkey = ' .$this->oDbCon->paramString($customerkey) ;
        $rs = $this->oDbCon->doQuery($sql);
        
        return array_column($rs, 'itemkey');
    }
    
    function getMarketplaceCategoryAttributes($itemkey, $marketplacekey = ''){
        
        $sql = 'select 
                    * 
                from 
                    '.$this->tableMarketplaceCategoryAttributes.' 
                where
                    refkey = '.$this->oDbCon->paramString($itemkey) ;
        
        if(!empty($marketplacekey))
                $sql .= ' and marketplacekey = '.$this->oDbCon->paramString($marketplacekey);
             
        $sql .= ' order by attributekey asc ';
                   
        return $this->oDbCon->doQuery($sql);
    }
    
	function getMarketplaceLogistics($itemkey, $marketplacekey = ''){
		
        $sql = 'select  *  from  '.$this->tableMarketplaceLogisticsDetail.'  where refkey = '.$this->oDbCon->paramString($itemkey) ;
        
        if(!empty($marketplacekey))
            $sql .= ' and marketplacekey = '.$this->oDbCon->paramString($marketplacekey);
              
        $rs =  $this->oDbCon->doQuery($sql);
		
		$arrItemKey = array_column($rs,'reflogistickey'); 
        
		$dbCon = $this->masterConn(); 
        $sql = 'select * from '.$this->tableMarketplaceLogistics. ' where statuskey = 1 and '.$this->tableMarketplaceLogistics. '.pkey in (' .$this->oDbCon->paramString($arrItemKey,',').')';
        $rsMasterShipment = $dbCon->doQuery($sql);
		$rsMasterShipment = array_column($rsMasterShipment,null,'pkey');
		$dbCon = null;
		
		foreach($rs as $key=>$row){
			$indexkey = $row['reflogistickey'];
			if(!isset($rsMasterShipment[$indexkey])) continue;
			
			$rs[$key]['logisticid'] = $rsMasterShipment[$indexkey]['logisticid'];
			$rs[$key]['maskchannelid'] = $rsMasterShipment[$indexkey]['maskchannelid'];
				
		}
		
        return $rs;
	}
	
    
    function normalizeSNList($snString,$uppercase = true){
        
       $snString =  preg_split('/[\n, ]+/', $snString);
        
       $arrTemp = array();
       foreach($snString as $sn){
           $sn = preg_replace("/[^A-Za-z0-9]/", '', $sn);  
           $sn = trim($sn);
           if(empty($sn)) continue;
           
           if($uppercase) $sn = strtoupper($sn);
           
           array_push($arrTemp,$sn);
       }
            
        return $arrTemp;
    }
    
    function getNotMovingStock($monthInterval = 1, $limit = ''){
        
        if(!is_numeric($monthInterval)) return array();
        
   /*     $rs = $this->searchData('','',true,'','','', 'having qtyonhand > 0' , '', '');  
        $arrItem = array_column($rs,'pkey');
        $arrItemQOH = array_column($rs,null,'pkey');
*/
        /*$sql = 'select * from item where pkey in ('.$this->oDbCon->paramString($arrItem,',').') and pkey not in ( 
                select itemkey from '.$this->tableItemMovement.' where
                qtyinbaseunit < 0 and trdate > DATE_ADD(now(),interval -'.$monthInterval.' month)
                order by trdate desc
         ) order by name asc';*/
        
        
        $companyCriteria = $this->getCompanyCriteria($this->tableWarehouse);
        $warehouseCriteria = $this->getWarehouseCriteria($this->tableWarehouse) ;
        
        $sql = '
         select * from (SELECT '.$this->tableName.'.* ,  
                           '.$this->tableItemUnit.'.name as baseunitname, 
                           coalesce(sum('.$this->tableItemInWarehouse.'.qtyinbaseunit),0) as qtyonhand
                        FROM   '.$this->tableItemUnit.',  '.$this->tableName.'
                        left join
                                (select itemkey, 
                                        qtyinbaseunit,
                                        warehousekey,
                                        iswebqoh 
                                from  '.$this->tableItemInWarehouse.',
                                      '.$this->tableWarehouse.' 
                                where  
                                   '.$this->tableWarehouse.'.pkey = '.$this->tableItemInWarehouse.'.warehousekey and
                                   '.$this->tableWarehouse.'.isqohcount = 1
                                   '.$this->warehouseCriteria.'  '.$companyCriteria.$warehouseCriteria.' ) '.$this->tableItemInWarehouse.' 
                            on item.pkey =  '.$this->tableItemInWarehouse.'.itemkey  
                        WHERE 
                            '.$this->tableName.'.statuskey = 1 and
                            itemtype in ('.$this->itemType.') and
                            ispackage = 0 and 
                            '.$this->tableItemUnit.'.pkey = '.$this->tableName.'.baseunitkey and 
                            '.$this->tableName.'.pkey not in ( 
                                    select itemkey from '.$this->tableItemMovement.' where
                                    (reftable = \'sales_order_header\' or reftable = \'sales_order_delivery_header\') and qtyinbaseunit < 0 and trdate > DATE_ADD(now(),interval -'.$monthInterval.' month)
                                    order by trdate desc
                             )
			             group by '.$this->tableName.'.pkey
                        ) as ' . $this->tableName .' order by qtyonhand desc, name asc';
        
        if(!empty($limit))  $sql .= ' limit 0,'. $limit;
  
        $rs = $this->oDbCon->doQuery($sql);
        
        $returnArr = array();
        foreach($rs as $row)
        array_push($returnArr, array(   
                                        'pkey' => $row['pkey'], 
                                        'name' => $row['name'], 
                                        'qtyonhand' => $row['qtyonhand'], 
                                        'baseunitname' => $row['baseunitname'], 
                                    )
                  );
        
        return  $returnArr;
    }
    
    function addToCompareSession($itemkey){
        
        if (empty($itemkey)) return; 
        
        if(!isset($_SESSION['itemsToCompare'])) 
            $_SESSION['itemsToCompare'] = array();
        
        if(in_array($itemkey, $_SESSION['itemsToCompare'])) return;
          
        array_push($_SESSION['itemsToCompare'], $itemkey);
        
    }
    
    function removeFromCompareSession($itemkey){
        
        if (empty($itemkey)) return;  
        if(!in_array($itemkey, $_SESSION['itemsToCompare'])) return; 
        
        foreach ($_SESSION['itemsToCompare'] as $key => $value) 
            if ($value == $itemkey) 
                unset($_SESSION['itemsToCompare'][$key]);  
 
        $_SESSION['itemsToCompare'] = array_values($_SESSION['itemsToCompare']);
    }
 
    
    /*function getSNInformation($serialNumber){
        if(empty($serialNumber)) return;
             
        $itemOut = new ItemOut();
        $itemOutDelivery = new ItemOutDelivery();
        $itemMovement = new ItemMovement();

        $refTableItemOut = $this->getTableKeyAndObj($itemOut->tableName);
        $refTableItemOut =  $refTableItemOut['key'];
        
        $refTableItemOutDelivery = $this->getTableKeyAndObj($itemOutDelivery->tableName);
        $refTableItemOutDelivery =  $refTableItemOutDelivery['key'];
        
        $rsItemSN = $this->searchSerialNumber('','',$serialNumber); 
        if(empty($rsItemSN)) 
           return;
        
        $arrCriteria = array();  
        array_push($arrCriteria, $this->tableSerialNumberMovement.'.serialnumber = '.$this->oDbCon->paramString($serialNumber));
        //array_push($arrCriteria, $this->tableSerialNumberMovement.'.reftabletype = '.$class->oDbCon->paramString($refTableItemOut));
        
        $criteria = ' and ' .implode(' and ', $arrCriteria);
        $orderBy = 'order by trdate asc';
        $rsSNMovement = $itemMovement->searchSNMovement('','','','',$criteria,$orderBy); 
        
        $firstInDate = '';
        $lastSoldDate = ''; 
        $warrantyPeriodExpiredDate = '';
        $warrantyVendorPeriodExpiredDate = '';
        $lastCustomerName = '';
        $lastCustomerKey = '';
        $lastRefCode = '';
        $lastRefKey = 0;

        for($i=0;$i<count($rsSNMovement);$i++){
            if (empty($firstInDate) && $rsSNMovement[$i]['qtyinbaseunit'] > 0 )
                $firstInDate = $rsSNMovement[$i]['trdate'];
            
            // asumsi barang terjual itu dari Item Out
            // harus cek Item Out atau Item Delivery ??, ini blm dicek ulang
            // overwrite kalo Item Delivery jadi Item Out // temporary
            if($rsSNMovement[$i]['reftabletype'] ==  $refTableItemOutDelivery){
                $rsItemOut = $itemOut->getDataRowById($rsSNMovement[$i]['refkey']);
                $rsSNMovement[$i]['refkey'] = $rsItemOut[0]['pkey'];
                $rsSNMovement[$i]['reftabletype'] = $refTableItemOut;
            }
            
            if ( $rsSNMovement[$i]['reftabletype'] ==  $refTableItemOut && $rsSNMovement[$i]['qtyinbaseunit'] < 0 ){ 
                // kalo dr Item Delivery harus cek ke Item Out nya
                $lastSoldDate = $rsSNMovement[$i]['trdate'];
                $lastRefKey = $rsSNMovement[$i]['refheaderkey'];
                $rsItemOut = $itemOut->searchData($itemOut->tableName.'.pkey',$lastRefKey);
                $lastRefCode = $rsItemOut[0]['code'];
                $lastCustomerName = $rsItemOut[0]['customername'];
                $lastCustomerKey = $rsItemOut[0]['customerkey'];
            }
            
            $warrantyPeriodExpiredDate = $rsSNMovement[$i]['warrantyperiodexpireddate'];
            $warrantyVendorPeriodExpiredDate = $rsSNMovement[$i]['warrantyvendorperiodexpireddate'];
            
        }
     


        $rs[0]['itemcode'] = $rsItemSN[0]['itemcode'];
        $rs[0]['itemname'] = $rsItemSN[0]['itemname'];
        $rs[0]['vendorpartnumber'] = $rsItemSN[0]['partnumber'];
        $rs[0]['firstindate'] = $firstInDate;
        $rs[0]['lastsolddate'] = $lastSoldDate;
        $rs[0]['warrantyperiodexpireddate'] = $warrantyPeriodExpiredDate;
        $rs[0]['warrantyvendorperiodexpireddate'] = $warrantyVendorPeriodExpiredDate;
          
        $rs[0]['lastcustomername'] = $lastCustomerName; 
        $rs[0]['lastcustomerkey'] = $lastCustomerKey; 
        $rs[0]['reference2'] = $lastRefCode; 
        
        //$rs[0]['itemindate'] = $rsSN[0]['trdate'];
        //$rs[0]['recipientname'] = $recipientName; 
        //$rs[0]['reference2'] = $refCode2; 
                    
        return $rs;
    }*/
 
    function updateVariantInheritInformation($itemkey){ 
        
        // cek parent bukan
        $rsItem = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.isparent',$this->tableName.'.categorykey',$this->tableName.'.brandkey'),
                                                ' and '. $this->tableName.'.pkey = '.$this->oDbCon->paramString($itemkey)); // gk boleh pake status, kalo mau pake status, harus ad handling jg ketika update status
        
        if($rsItem[0]['isparent'] <> 1) return;
        
        $sql = 'update '.$this->tableName.'  
                set 
                    categorykey = '.$this->oDbCon->paramString($rsItem[0]['categorykey']).',
                    brandkey =  '.$this->oDbCon->paramString($rsItem[0]['brandkey']).'
                where parentkey = '.$this->oDbCon->paramString($itemkey);
        $this->oDbCon->execute($sql);

    }
        
    function updateParentVariant($arrParam){
        
        // cek parentkey lama ada ap gk 
	 	$itemParentKey = $arrParam['hidParentItemKey'];
	 	$itemParentKeyBefore = $arrParam['hidBeforeParentItemKey'];
        
        // kalo sebelumny ad parentkey
        if(!empty($itemParentKeyBefore)){ 
            // cek item parent masih ad variant tdk 
			$rsCurrItem = $this->searchDataRow(array($this->tableName.'.pkey'),
                                                ' and '. $this->tableName.'.parentkey = '.$this->oDbCon->paramString($itemParentKeyBefore)); // gk boleh pake status, kalo mau pake status, harus ad handling jg ketika update status
          
			$isParent = (empty($rsCurrItem)) ? 0 : 1;
            
            $sql = 'update '.$this->tableName.' set isparent = '.$isParent.' where pkey = ' . $this->oDbCon->paramString($itemParentKeyBefore) ;
            $this->oDbCon->execute($sql);
         
        }else{  
            // sebelumnya kosong gk ad parent, jd gk perlu gapa2in
            
        }
        
        // update parentkey skrg, 
        if(!empty($itemParentKey)){ 
            $sql = 'update '.$this->tableName.' set isparent = 1 where pkey = ' . $this->oDbCon->paramString($itemParentKey) ;
			$this->oDbCon->execute($sql);
        }
					 
	}
    
    function getItemVariants($pkey,$marketplacekey = ''){
        
        // gk bisa pake join karena satu item bisa punya beberapa variant kombinasi (tokopedia)  
        // harus sort by pkey karena shopee perlu urutan yg sama utk update 
        $rs = $this->searchDataRow( array($this->tableName.'.*'),
                                    ' and '.$this->tableName.'.parentkey = ' . $this->oDbCon->paramString($pkey) .' and '.$this->tableName.'.statuskey = 1 ',
                                   ' order by '.$this->tableName.'.pkey asc'
                                    );
        
        
        // tambahkan informasi variant marketplace
        if($this->hasActiveMarketplace()){
            foreach($rs as $index => $itemRow) 
                $rs[$index]['marketplace_variant'] = $this->getVariantValueForMarketplace($itemRow['pkey'],$marketplacekey); 
        }
        
        return $rs;
    }
    
    function getVariantValueForMarketplace($itemkey,$marketplacekey=''){
        $sql = 'select * from '.$this->tableItemMarketplaceVariant.' where refkey = ' . $this->oDbCon->paramString($itemkey) ;
        
        if(!empty($marketplacekey))
            $sql .= ' and marketplacekey = ' .$this->oDbCon->paramString($marketplacekey) ;
    
        return $this->oDbCon->doQuery($sql);
    }
    
    function getDetailForAPI($arrKey, $arrIndex=array()){
        $rsDetailsCol = array();

        if(in_array('available_unit', $arrIndex)){ 
            $keys = is_array($arrKey) ? $arrKey : [$arrKey];
            $rsDetails = [];
            foreach ($keys as $key) {
                $rows = $this->getAvailableUnit($key);
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $row['refkey'] = $key;
                        $rsDetails[] = $row;
                    }
                }
            }
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey');  
            $rsDetailsCol['available_unit'] = $rsDetails;
                
        }
         
        if(in_array('serial_number', $arrIndex)){ 
            $rsDetails = $this->searchSerialNumber($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'itemkey');  
            $rsDetailsCol['serial_number'] = $rsDetails;
                
        }

         if(in_array('image_url', $arrIndex)){ 
            $rsDetails = $this->getItemImagesForAPI($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey');  
            $rsDetailsCol['image_url'] = $rsDetails;
         }
        
        return $rsDetailsCol;
    }

    function getItemImagesForAPI($arrPkey){
        $sql = 'select 
	   			'.$this->tableImage .'.pkey,
	   			'.$this->tableImage .'.refkey,
	   			'.$this->tableImage .'.file 
			  from 
			  	'.$this->tableImage.'  
			  where   
                '.$this->tableImage.'.refkey in ('.$this->oDbCon->paramString($arrPkey,',').')';
    
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
  
		$rs = $this->oDbCon->doQuery($sql);
            
        $total = count($rs);    
        for($i=0;$i<count($rs);$i++)
            $rs[$i]['url'] = HTTP_HOST.'download/item/'.$rs[$i]['refkey'].'/'.$rs[$i]['file'];
        
        return $rs;
     
    }
  
    function manipulateDataGetAPI($rs){
		if($this->activeModule['discountscheme']){
			$discountScheme = new DiscountScheme();
			$discountScheme->applyDiscountScheme($rs);	
		}
        return $rs;
    }
    
	function getItemForBoost($marketplacekey, $limit = 25, $random = 5){
		if(!is_numeric($limit)) die;
		
		$sql = 'select 
					itemkey, 
					marketplaceitemkey,
					('.$this->tableItemInWarehouse.'.qtyinbaseunit - '.$this->tableItemInWarehouse.'.qtyonreserveinbaseunit) as qty
				from 
					'.$this->tableName.', 
					'.$this->tableItemInWarehouse.', 
					'.$this->tableItemMarketplaceLink.' 
				where 
					'.$this->tableItemMarketplaceLink.'.refkey = '.$this->tableName.'.pkey and
					'.$this->tableItemInWarehouse.'.itemkey = '.$this->tableName.'.pkey and
					'.$this->tableName.'.statuskey = 1 and
					'.$this->tableItemMarketplaceLink.'.marketplacekey = '.$this->oDbCon->paramString($marketplacekey).' 
				order by qty desc limit '.$limit;
		
		
		$rs = $this->oDbCon->doQuery($sql);
		shuffle($rs);
		
		$returnArr = array();
		for($i=0;$i<$random;$i++)
			array_push($returnArr, $rs[$i]);
		
		return $returnArr;
	}
    
    
    // nanti akan dihapus di v2
    function getMarketplaceVariantDetail($itemkey, $marketplacekey = ''){
        
        $sql = 'select * 
                from 
                    '.$this->tableItemMarketplaceVariant.' 
                where
                    refkey = '.$this->oDbCon->paramString($itemkey) ;
        
        if(!empty($marketplacekey))
                $sql .= ' and marketplacekey = '.$this->oDbCon->paramString($marketplacekey);
              
           
        return $this->oDbCon->doQuery($sql);
    }

    function updateMarketplaceImageId($imgkey,$marketplaceImageId){
//    	try{ 
//			
//			if(!$this->oDbCon->startTrans())
//				throw new Exception($this->errorMsg[100]);
//	 
//			$sql = 'update '.$this->tableImage.' 
//			        set 
//			            marketplaceimageid = '.$this->oDbCon->paramString($marketplaceImageId).',
//			            checksum = md5(concat(file,\'.\',marketplaceimageid))
//                    where pkey = '.$this->oDbCon->paramString($imgkey);
//                    
//			$this->oDbCon->execute($sql);
//			 
//			$this->oDbCon->endTrans(); 
//		
//	    } catch(Exception $e){
//			$this->oDbCon->rollback(); 
//		}
    }	
    
      function calculateAutoPrice($arrPkey){
        // utk jewelry 
        // $this->setLog("kalo satuan KG ahrus dikali ke gram dulu",true);
        
        $priceUpdate = new PriceUpdate();
        
        if(!is_array($arrPkey)) $arrPkey = array($arrPkey);
        
        $rsItem = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.gramasi', $this->tableName.'.carat', $this->tableName.'.additionalprice', $this->tableName.'.pricingcategorykey'), 
                                            ' and '.$this->tableName.'.pkey in  ('.$this->oDbCon->paramString($arrPkey,',').')'
                                            );
        
        // ambil nilai update terahkir
        $rsLastPrice = $priceUpdate->getPricingCategoryLastRate();
        $rsPricingCol = array_column($rsLastPrice,null,'categorykey');
         
              
//        $this->setLog($rsPricingCol,true);
         
          
//        $arrPriceUpdateKey = array_unique(array_column($rsItem,'pricingcategorykey'));
//        
//        $rsPricingCol = array();
//        foreach($arrPriceUpdateKey as $priceKey)
//            $rsPricingCol[$priceKey] = $priceUpdate->getDetailWithRelatedInformation($priceKey);
             
        
        try{			  
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			 
                    foreach($rsItem as $itemRow){
                       $gramasi = $itemRow['gramasi'];

                       if ($itemRow['weightunitkey'] == UNIT['kg'])
                           $gramasi *= 1000;

                        if (empty($itemRow['pricingcategorykey'])) continue;

                        $arrPricing = $rsPricingCol[$itemRow['pricingcategorykey']];
                        $rate = $arrPricing['rate'];
                        $isCaratBase = $arrPricing['iscaratbase'];
 
                        $caratMultiplier = ($isCaratBase) ? ($itemRow['carat']/24) : 1;
                        $price = ($gramasi * $rate * $caratMultiplier) + $itemRow['additionalprice'] ; 
                         
//                        $this->setLog($gramasi .'*'. $rate .'*'. $caratMultiplier .'+'. $itemRow['additionalprice'],true);
                        
                        $sql = 'update '.$this->tableName.' set sellingprice =  '.$this->oDbCon->paramString($price).' where pkey = '.$this->oDbCon->paramString($itemRow['pkey']);
                        $this->oDbCon->execute($sql);

                    }

				$this->oDbCon->endTrans(); 
            
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
        
        return $arrPrice;
    }

    function getItemFavorite($userkey,$itemkey =''){
          
            $sql = 'select  '.$this->tableFavoritProduct.'.* 
                   from  '.$this->tableFavoritProduct.' 
                   where '.$this->tableFavoritProduct.'.customerkey = ' . $this->oDbCon->paramString($userkey) ; 
        
            if(!empty($itemkey))
                $sql   .= ' and ' .$this->tableFavoritProduct.'.itemkey in (' . $this->oDbCon->paramString($itemkey,',').' )'; 
            
            $rs = $this->oDbCon->doQuery($sql);
            return $rs;
    }
    
  function updateFavoritProduct($arr){

		$arrayToJs =  array();
			
		try{
			
            if (empty($arr['hidUserKey']))  return;
            
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
            
            
            $sql = 'select 
                    '.$this->tableFavoritProduct.'.* 
                from
                    '.$this->tableFavoritProduct.' 
                where 
						 '.$this->tableFavoritProduct.'.itemkey = ' . $this->oDbCon->paramString($arr['itemKey'])
						 .' and '.$this->tableFavoritProduct.'.customerkey = ' . $this->oDbCon->paramString($arr['hidUserKey']) ; 
            ;

            $rs = $this->oDbCon->doQuery($sql);

            if (!empty($rs)) {
                $sql = 'delete from '.$this->tableFavoritProduct.' where pkey = '. $this->oDbCon->paramString($rs[0]['pkey']);
            } else {
                $sql = 'insert into  '.$this->tableFavoritProduct.' (customerkey,itemkey) values ('.$this->oDbCon->paramString($arr['hidUserKey']).', '.$this->oDbCon->paramString($arr['itemKey']).')';
            }
                
            $this->oDbCon->execute($sql);
             
			$this->oDbCon->endTrans();
			
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
	    } catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				 
 		return $arrayToJs;  
	}
    
 function generateCodeFromCategory($arrParam)
    {
        $code = '';

        $itemCategory = new ItemCategory();

        $categorykey = $arrParam['hidCategoryKey'];
        //$colorkey = $arrParam['hidColorKey'];
        $arrColorKey = $arrParam['hidColorKey'];
        $arrModelKey = $arrParam['hidModelKey'];
        $arrTextureKey = $arrParam['hidTextureKey'];
        $platingkey = $arrParam['hidPlatingKey'];
        $materialkey = $arrParam['hidMaterialKey'];
        $ringsizekey = $arrParam['hidRingSizeKey'];
        $characterkey = $arrParam['hidCharacterKey'];
        $itemname = $arrParam['name'];

        $arrItemVariationKey = $arrParam['hidVariationKey'];
        $itemagekey = $arrParam['hidAgeKey'];
        $sexkey = $arrParam['selSexKey'];


        $carat = $arrParam['carat'];
        $gramasi = $arrParam['gramasi'];
        $size = $arrParam['size'];
        $rsCategory = $itemCategory->getDataRowById($categorykey);

        if(empty($rsCategory) || empty($rsCategory[0]['itemcodepattern'])) return $code;

        $formatCode = $rsCategory[0]['itemcodepattern'];
        preg_match_all('/{{(.*?)}}/', $formatCode, $matches);

        if (empty($matches[1])) return $code;

        $replacements = [];

        $rsCode = '';
        $multiSeparator = ''; // buat penghubung yg punya 2 atribute, misalnya 2 warna
        foreach ($matches[1] as $placeholder) {
            
            switch (strtoupper($placeholder)) {
                case 'COLOR':
                    $color = new Color();
                    // $rs = $color->getDataRowById($colorkey);
                    // $rsCode = $rs[0]['code'] ?? ' ';
                    
                    $rs = $color->searchData('', '', true, ' and ' . $color->tableName . '.pkey in (' . $this->oDbCon->paramString($arrColorKey,',') . ') ');
                    $rsCode = implode($multiSeparator, array_column($rs, 'code'));
                    break;

                case 'MODEL':
                    $model = new Model();
                    $rs = $model->searchData('', '', true, ' and ' . $model->tableName . '.pkey in (' . $this->oDbCon->paramString($arrModelKey,',') . ') ');
                    $rsCode = implode($multiSeparator, array_column($rs, 'code'));
                    break;

                case 'PLATING':
                    $plating = new Plating();
                    $rs = $plating->getDataRowById($platingkey);
                    $rsCode = $rs[0]['code'] ?? '0';
                    break;

                case 'TEXTURE':
                    $texture = new Texture();
                    $rs = $texture->searchData('', '', true, ' and ' . $texture->tableName . '.pkey in (' . $this->oDbCon->paramString($arrTextureKey,',') . ') ');
                    $rsCode = implode($multiSeparator, array_column($rs, 'code'));
                    break;

                case 'MATERIAL':
                    $material = new Material();
                    $rs = $material->getDataRowById($materialkey);
                    $rsCode = $rs[0]['code'] ?? '0';
                    break;

                case 'RINGSIZE':
                    $ringSize = new RingSize();
                    $rs = $ringSize->getDataRowById($ringsizekey);
                    $rsCode = $rs[0]['code'] ?? '0';
                    break;

                case 'CHARACTER':
                    $character = new Character();
                    $rs = $character->searchData('', '', true, ' and ' . $character->tableName . '.pkey in (' . $this->oDbCon->paramString($characterkey,',') . ') ');
                    $rsCode = implode($multiSeparator, array_column($rs, 'code'));
                    break;
                    
                case 'CARAT':
                        $rsCode = $carat ?? '0';
                    break;

                case 'GRAMASI':
                        $rsCode = $gramasi ?? '0';
                    break;

                case 'SIZE':
                        $rsCode = $size ?? '0';
                    break;

                case 'VARIATION':
                    $itemVariation = new ItemVariation();
                    $rs = $itemVariation->searchData('', '', true, ' and ' . $itemVariation->tableName . '.pkey in (' . $this->oDbCon->paramString($arrItemVariationKey,',') . ') ');
                    $rsCode = implode($multiSeparator, array_column($rs, 'code'));
                    break;

                case 'AGE':
                    $age = new Age();
                    $rs = $age->getDataRowById($itemagekey);
                    $rsCode = $rs[0]['code'] ?? '0';
                    break;

                case 'GENDER':
                    $rsSex =  $this->getSex();
                    $rsSexCol = $this->reindexDetailCollections($rsSex, 'pkey');
                    $rs = $rsSexCol[$sexkey];
                    $rsCode = $rs[0]['code'] ?? '0';
                    break;


                default:
                    $rsCode = '0';
                    break;
            }

            $replacements['{{' . $placeholder . '}}'] = $rsCode;

        }

        $code =  str_replace(array_keys($replacements), array_values($replacements), $formatCode);
        
        return $code;

    }
    function updateTotalSold($pkey = array(), $asParent = false){
         
        $fieldName = ($asParent) ? 'parentkey' : 'pkey'; 
        
        $sql = 'select 
                sum(qtyinbaseunit) as totalsold,
                ' . $this->tableName.'.'.$fieldName.' as itemkey,
                '.$this->tableName.'.parentkey 
            from  ' . $this->tableName.',' . $this->tableSalesOrder.', '. $this->tableSalesOrderDetail.' 
            where 
                ' . $this->tableSalesOrder.'.pkey = ' . $this->tableSalesOrderDetail.'.refkey and
                ' . $this->tableSalesOrder.'.statuskey in (2,3) and
                ' . $this->tableSalesOrderDetail.'.itemkey = '.$this->tableName.'.pkey and
                ' . $this->tableName.'.'.$fieldName.' in ('.$this->oDbCon->paramString($pkey,',').')
            group by ' . $this->tableName.'.'.$fieldName;
 
        
        $rs = $this->oDbCon->doQuery($sql);
        $rs = array_column($rs,null,'itemkey');
        
        $arrParentKey = array_column($rs,'parentkey'); 
        
        // hitung utk parent
        // gk bisa, dari parent harus hitugn ulang semua variasinya
        //$arrParent = array();
        //foreach($rs as $itemkey=>$row){
        //    if(empty($row['parentkey'])) continue; 
        //    $parentkey = $row['parentkey']; 
        //    if(!isset($arrParent[$parentkey])) $arrParent[$parentkey]['totalsold'] = 0;  
        //    $arrParent[$parentkey]['totalsold'] += $row['totalsold'];
        //}
        // 
        
        try{
			
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
             
             
            foreach($rs as $itemkey=>$row){
                $totalSold = $row['totalsold'];
                
                // cek item nya variant atau bkn
                $sql = 'update '.$this->tableName.' set totalsold = '.$this->oDbCon->paramString($totalSold).' where pkey = ' .$this->oDbCon->paramString($itemkey) ;
                $this->oDbCon->execute($sql);
            }

            //// harusnay gk mungkin looping forever
            if(!$asParent){

                foreach($arrParentKey as $indexkey => $row)
                    if($row == 0) unset($arrParentKey[$indexkey]);

                if(!empty($arrParentKey))
                    $this->updateTotalSold($arrParentKey,true);

            }
            
			$this->oDbCon->endTrans();
            
	    } catch(Exception $e){
			$this->oDbCon->rollback();  
		}	 
        
        
    } 
    
    
    function updateTotalReviews($pkey = array(), $asParent = false){
        
        $fieldName = ($asParent) ? 'parentkey' : 'pkey'; 
        
        $sql = 'select   
                    AVG('.$this->tableReview.'.rating) as rating, 
                    ' . $this->tableName.'.'.$fieldName.' as itemkey,
                    ' . $this->tableName.'.parentkey
                from  
                    ' . $this->tableReview.',' . $this->tableName.'
                where   
                    ' . $this->tableReview.'.refkey = ' . $this->tableName.'.pkey and
                    ' . $this->tableName.'.'.$fieldName.' in ('.$this->oDbCon->paramString($pkey,',').')
                group by 
                    ' . $this->tableName.'.'.$fieldName.'
              ';
        
        $rs = $this->oDbCon->doQuery($sql);
        $rs = array_column($rs,null,'itemkey');
        
        $arrParentKey = array_column($rs,'parentkey'); 
        
        try{
			
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
             
             
            foreach($rs as $itemkey=>$row){ 
                // cek item nya variant atau bkn
                $sql = 'update '.$this->tableName.' set rating = '.$this->oDbCon->paramString($row['rating']).' where pkey = ' .$this->oDbCon->paramString($itemkey) ;
                $this->oDbCon->execute($sql);
            }
            
             //// harusnay gk mungkin looping forever
            if(!$asParent){ 
                foreach($arrParentKey as $indexkey => $row)
                    if($row == 0) unset($arrParentKey[$indexkey]);

                if(!empty($arrParentKey))
                    $this->updateTotalReviews($arrParentKey,true);

            }

			$this->oDbCon->endTrans();
            
	    } catch(Exception $e){
			$this->oDbCon->rollback();  
		}	 
        
    } 
    
    function updateTotalReview($pkey = array(), $asParent = false){
        
        $fieldName = ($asParent) ? 'parentkey' : 'pkey'; 
        
        $sql = 'select   
                    count('.$this->tableReview.'.pkey) as totalreview, 
                    ' . $this->tableName.'.'.$fieldName.' as itemkey,
                    ' . $this->tableName.'.parentkey
                from  
                    ' . $this->tableReview.',' . $this->tableName.'
                where   
                    ' . $this->tableReview.'.refkey = ' . $this->tableName.'.pkey and
                    ' . $this->tableName.'.'.$fieldName.' in ('.$this->oDbCon->paramString($pkey,',').')
                group by 
                    ' . $this->tableName.'.'.$fieldName.'
              ';
        
        $rs = $this->oDbCon->doQuery($sql);
        $rs = array_column($rs,null,'itemkey');
        
        $arrParentKey = array_column($rs,'parentkey'); 
        
        try{
			
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
             
             
            foreach($rs as $itemkey=>$row){ 
                // cek item nya variant atau bkn
                $sql = 'update '.$this->tableName.' set totalreview = '.$this->oDbCon->paramString($row['totalreview']).' where pkey = ' .$this->oDbCon->paramString($itemkey) ;
                $this->oDbCon->execute($sql);
            }
            
             //// harusnay gk mungkin looping forever
            if(!$asParent){ 
                foreach($arrParentKey as $indexkey => $row)
                    if($row == 0) unset($arrParentKey[$indexkey]);

                if(!empty($arrParentKey))
                    $this->updateTotalReview($arrParentKey,true);

            }

			$this->oDbCon->endTrans();
            
	    } catch(Exception $e){
			$this->oDbCon->rollback();  
		}	 
        
    } 
     
    function getReview($criteria = ''){

        // bisa search antara sales order atau item
        // jd gk bisa hanya based on so
        
        $sql = 'select
                    '.$this->tableReview.'.*
                from
                    '.$this->tableReview.'
                where
                   1=1';
    
		if (!empty($criteria)) {
			$sql .= $criteria;
		}

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function addReview($arr){

		$arrayToJs =  array();

        if (empty($arr['review'])){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['review'][1]); 
            return $arrayToJs;  
		}

        if (empty($arr['rating'])){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['rating'][1]); 
            return $arrayToJs;  
		}
			
		try{
			
            if (empty($arr['hidUserKey']))  return;
            
            $salesOrder = new SalesOrder();
            // cek sales order punya usernya bukan
            $rsSalesOrder = $salesOrder->searchDataRow(array($salesOrder->tableName.'.pkey'),
                                                     ' and '. $salesOrder->tableName.'.pkey = '.$this->oDbCon->paramString($arr['salesorderkey']).' 
                                                       and '. $salesOrder->tableName.'.customerkey = '.$this->oDbCon->paramString($arr['hidUserKey'])
                                                      );
            
            if(empty($rsSalesOrder)) return;
            
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
            
            $sql = 'insert into  '.$this->tableReview.' (refkey,review,rating,salesorderkey,customerkey) values ('.$this->oDbCon->paramString($arr['itemkey']).', '.$this->oDbCon->paramString($arr['review']).', '.$this->oDbCon->paramString($arr['rating']).', '.$this->oDbCon->paramString($arr['salesorderkey']).', '.$this->oDbCon->paramString($arr['hidUserKey']).' )';
			$this->oDbCon->execute($sql);
         
            $this->updateTotalReviews($arr['itemkey']);
            $this->updateTotalReview($arr['itemkey']);
            
			$this->oDbCon->endTrans();
			
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
	    } catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				 
 		return $arrayToJs;  
	}
    function searchItemGroup($refname = '',$criteria = ''){
        
        $sql = 'select 
                    '.$this->tableItemUnitGroup.'.* 
                from
                    '.$this->tableItemUnitGroup.' 
                where
                    1=1'
            ;
        
        if (!empty($refname))
            $sql .= ' and ref = ' . $this->oDbCon->paramString($refname);
         
        
        if (!empty($criteria))
            $sql .= $criteria;
           
        return $this->oDbCon->doQuery($sql);
    }



    //untuk jewlery
    function getItemDataForDuplicate($pkey)
    {
        $sql = '
            select
                '. $this->tableName .'.*,
                '.$this->tableCategory.'.name as categoryname,
                '.$this->tableCategory.'.code as categorycode,
                '. $this->tableBrand .'.code as brandcode,
                '. $this->tableBrand .'.name as brandname,
                '.$this->tableMaterial.'.code as materialcode,
                '.$this->tableMaterial.'.name as materialname,
                '.$this->tableRingSize.'.code as ringsizecode,
                '.$this->tableRingSize.'.name as ringsizename,
                '.$this->tablePlating.'.code as platingcode,
                '.$this->tablePlating.'.name as platingname,
                '.$this->tableAge.'.code as agecode,
                '.$this->tableAge.'.name as agename
            from
                '. $this->tableName .'
                    left join '.$this->tableMaterial.' on '.$this->tableName.'.materialkey = '.$this->tableMaterial.'.pkey
                    left join '.$this->tableRingSize.' on '.$this->tableName.'.ringsizekey = '.$this->tableRingSize.'.pkey
                    left join '.$this->tablePlating.' on '.$this->tableName.'.platingkey = '.$this->tablePlating.'.pkey
                    left join '.$this->tableBrand.' on  '.$this->tableName.'.brandkey = '.$this->tableBrand.'.pkey
                    left join '.$this->tableAge.' on  '.$this->tableName.'.agekey = '.$this->tableAge.'.pkey,
                    '.$this->tableCategory.'
            where
                '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey and
                '.$this->tableName.'.itemtype in ('.$this->itemType.') and
                '. $this->tableName .'.pkey in ('.$this->oDbCon->paramString($pkey,',').')
        ';

        $rsModel = $this->getItemModelDetail($pkey);
        $rsModel = $this->reindexDetailCollections($rsModel,'refkey');

        $rsTexture = $this->getItemTextureDetail($pkey);
        $rsTexture = $this->reindexDetailCollections($rsTexture, 'refkey');
        
        $rsColor = $this->getItemColorDetail($pkey);
        $rsColor = $this->reindexDetailCollections($rsColor,'refkey');
        
        $rsCharacter = $this->getItemCharacterDetail($pkey);
        $rsCharacter = $this->reindexDetailCollections($rsCharacter, 'refkey');

        $rsSKUAlias = $this->getDetailItemSKUAlias($pkey);
        $rsSKUAlias = $this->reindexDetailCollections($rsSKUAlias, 'refkey');

        $rsDetailVidio = $this->getDetailVideo($pkey);
        $rsDetailVidio = $this->reindexDetailCollections($rsDetailVidio, 'refkey');

        $rsItemVariation = $this->getItemItemVariationDetail($pkey);
        $rsItemVariation = $this->reindexDetailCollections($rsItemVariation, 'refkey');
        $rsData = $this->oDbCon->doQuery($sql);

        foreach($rsData as &$dataRow) {
            $pkey = $dataRow['pkey'];

            $dataRow['model_detail'] = isset($rsModel[$pkey]) ? $rsModel[$pkey] : [];
            $dataRow['texture_detail'] = isset($rsTexture[$pkey]) ? $rsTexture[$pkey] : [];
            $dataRow['color_detail'] = isset($rsColor[$pkey]) ? $rsColor[$pkey] : [];
            $dataRow['character_detail'] = isset($rsCharacter[$pkey]) ? $rsCharacter[$pkey] : [];
            $dataRow['sku_alias_detail'] = isset($rsSKUAlias[$pkey]) ? $rsSKUAlias[$pkey] : [];
            $dataRow['video_detail'] = isset($rsDetailVidio[$pkey]) ? $rsDetailVidio[$pkey] : [];
            $dataRow['item_variation_detail'] = isset($rsItemVariation[$pkey]) ? $rsItemVariation[$pkey] : [];
            
        }

        return $rsData;
    }
    
    
      function getSparePartType($pkey = '') {
            $sql = '
                  select
                        '.$this->tableSparePartType.'.*
                  from
                        '.$this->tableSparePartType.'
                  where
                        1=1
            ';

            if (!empty($pkey)) {
                  $sql .= ' and '.$this->tableSparePartType.'.pkey in ('.$this->oDbCon->paramString($pkey,',').') ';
            }

            return $this->oDbCon->doQuery($sql);
      }



    function getItemPositionForMaintenance($pkey, $carkey)
    {
        $itemCategory = new ItemCategory();
        $car = new Car();
        $carCategory = new CarCategory();

        if(empty($pkey) || empty($carkey)) return [];

        $rsHeader = $this->getDataRowById($pkey);
        $rsCar = $car->getDataRowById($carkey);

        if(empty($rsHeader) || empty($rsCar)) return [];

        $categorykey = $rsHeader[0]['categorykey'];
        $carCategoryKey = $rsCar[0]['categorykey'];

        $rsItemCategory = $itemCategory->getDataRowById($categorykey);
        $sparePartTypeKey = $rsItemCategory[0]['spareparttypekey'];
        
        if(empty($sparePartTypeKey)) return [];

        $rs = $carCategory->getItemPosition($carCategoryKey, $sparePartTypeKey);
        
        return $rs;

    }
    function getMainImage($itemkey){
        // biasa utk website atau shoppingcart
        $rsItem = $this->searchDataRow(array($this->tableName.'.pkey',
                                          $this->tableName.'.isvariant',
                                          $this->tableName.'.parentkey'),
                                    ' and '. $this->tableName.'.pkey = ' . $this->oDbCon->paramString($itemkey)
                                    );
        
        //// kalo variant, gk ad image, ambil image parentnya 
        $itemImage = $this->getItemImage($itemkey);

        if($rsItem[0]['isvariant'] == 1 && empty($itemImage))
            $itemImage = $this->getItemImage($rsItem[0]['parentkey']);
        
        $returnImg = $itemImage[0]; 
        $returnImg['url'] = ($this->useStorage) ? 
                            $this->createPresignedURL(DOMAIN_NAME.'/'.$this->uploadFolder.$itemkey.'/'.$itemImage[0]['file']) :
                            $this->uploadFolder.$itemkey.'/'.$itemImage[0]['file']; // kalo yg local storage, kembalikan path saja, karena perlu utk phpthumb    
        
        return $returnImg;
    }
    
    function getItemSparepartTypeKey($itemkey){
        
        if (!is_array($itemkey)) $itemkey = array($itemkey);
        
        $sql = 'select ' . $this->tableName.'.pkey, '. $this->tableSparePartType .'.pkey as spareparttypekey
                from ' . $this->tableName.', ' . $this->tableCategory.', '. $this->tableSparePartType .'
                where   
                    ' . $this->tableName.'.categorykey = ' . $this->tableCategory.'.pkey and
                    ' . $this->tableCategory.'.spareparttypekey = '. $this->tableSparePartType .'.pkey and
                    ' . $this->tableName.'.pkey in ('.$this->oDbCon->paramString($itemkey,',').')
                ';
         //$this->setLog($sql,true);
         return $this->oDbCon->doQuery($sql);       
                
    }

  }
// 
?>
