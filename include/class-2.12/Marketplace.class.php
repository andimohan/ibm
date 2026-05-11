<?php
class Marketplace extends BaseClass{
    
    function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'marketplace';
        $this->tableCustomer = 'customer';
		$this->tableStatus = 'master_status'; 
		$this->tableMarketplaceBrand = 'marketplace_brand'; 
		$this->tableMarketplaceActionType = 'marketplace_action_type'; 
        $this->tableMarketplaceCondition = 'marketplace_item_condition';
		$this->tableMarketplaceCategory = 'marketplace_category'; 
		$this->tableMarketplaceLogistics = 'marketplace_logistics'; 
		$this->tableShipmentDetails = 'shipment_marketplace_detail'; 
        $this->tableMarketplaceStorefront = 'item_category_storefront';
        $this->tableMarketplaceCategoryAttributes =  'marketplace_category_attributes'; 
        $this->tableItemCategoryMarketplaceAttributes ='item_category_marketplace_attributes';
		$this->tableItemCategoryMarketplaceDetail = 'item_category_marketplace_detail';
        $this->tableItemMarketplaceLink = 'item_marketplace_link';
        $this->tableMarketplaceAttributesExclude  = 'marketplace_category_attributes_exclude';
        $this->tableSyncedItem  = 'item_marketplace_sync_detail'; 
		$this->tableSalesOrder = 'sales_order_header'; 
        $this->tableCampaignPrice  = 'campaign_price';
        $this->tableCampaign  = 'campaign';
        $this->tableMarketplaceProviderAccount = 'marketplace_provider_account';
		$this->securityObject = 'Marketplace'; 
        $this->tableMarketplaceLog = 'marketplace_log';
		$this->marketplaceBackgroundJob = 'marketplace_background_job';
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['shopid'] = array('shopId');
        $this->arrData['customerkey'] = array('hidCustomerKey'); 
        $this->arrData['margintype'] = array('selMarginType');
        $this->arrData['finalpricetype'] = array('selFinalPriceType');
        $this->arrData['margin'] = array('marginValue','number');
        $this->arrData['trdesc'] = array('trDesc');  
        $this->arrData['statuskey'] = array('selStatus');  
        $this->arrData['accesstoken'] = array('accessToken');
        $this->arrData['campaignstartdate'] = array('campaignStartDate','date');
        $this->arrData['campaignenddate'] = array('campaignEndDate','date');
        $this->arrData['discounttype'] = array('selDiscountType');
        $this->arrData['discount'] = array('discountValue','number');   
        $this->arrData['priceadjustmenttype'] = array('selPriceAdjustmentType');
        $this->arrData['priceadjustment'] = array('priceAdjustment','number');   
        $this->arrData['paymentmethodkey'] = array('selPaymentMethod');
       
       
        $this->url  = '';
        $this->appKey  = '';
        $this->secretKey  = '';
        $this->rsMarketplace = array();
        $this->marketplaceKey = '';
        $this->accessToken = ''; // lazada
        $this->shopId = ''; // shopee
        
        $this->backdateInterval =  "-7 days";
        
        $this->itemCondition = array('1' => 'NEW', '2' => 'USED');
        
        $this->actionType =  $this->getActionType(); 
        $this->actionType =  array_column( $this->actionType,'pkey','keyword' );
        
        //$this->excludeCategoryAttributes = array('name', 'short_description','brand','merek','package_weight','package_length','package_width','package_height', 'SellerSku', 'price');
        
        // sebisa mungkin includeClass disesuaikan saja di file PHP tergantung kebutuhan
         
        require_once DOC_ROOT. 'lazada/LazopSdk.php';
        require_once DOC_ROOT. 'connections/_mp-app-config.php';
        
		$this->includeClassDependencies(array( 
              'Brand.class.php' 
        ));
		
		$this->isExecuteOnBackground = $this->loadSetting('mpExecuteQuery');
		
        $this->debug = false;
	}
	
	function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
                    '.$this->tableCustomer.'.name as customername,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.',
                    '.$this->tableCustomer.',
                    '.$this->tableStatus.'
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                    '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
 		' .$this->criteria ; 
		 
    }
    
    function getActionType($pkey = ''){
        $sql = 'select * from '.$this->tableMarketplaceActionType. ' where 1=1 ';
        
        if(!empty($pkey)){  
            
            if(!is_array($pkey))
                $pkey = array($pkey);

            $sql .= ' and '.$this->tableMarketplaceActionType.'.pkey in (' . $this->oDbCon->paramString($pkey,',') .')';
        }
        
        return $this->oDbCon->doQuery($sql);
    }
    
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey);

		$name = $arr['name']; 
        $customer = $arr['hidCustomerKey'];
		
        $rs = $this->isValueExisted($pkey,'name',$name);
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['marketplace'][1]);
		}else if(count($rs) <> 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['marketplace'][2]);
		}
        
        if(empty($customer))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
        
		return $arrayToJs;
        
    }	 
	  
    function generateDefaultQueryForAutoComplete($returnField){ 
            $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value
				from 
				    '.$this->tableName . ',
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
        
        return $sql;
    }
    
    
    
	function executeOnBackground($url,$method='GET',$payload=array(), $arrLog = array(), $arrOpt = array()){
		 
// 		 return $this->execute($url,$method,$payload, $arrLog, $arrOpt);
		 
		if($this->isExecuteOnBackground == 2)
			return $this->addToBackgroundJob($url,$method, $payload,$arrLog);
		else
			return $this->execute($url,$method,$payload, $arrLog, $arrOpt);
	}
	
     
    function onConfirmTrans($pkey){  
        
        $salesOrder = new SalesOrder();  
        $rsSalesOrder = $salesOrder->getDataRowById($pkey); 
        if(empty($rsSalesOrder)) return;
        if(empty($rsSalesOrder[0]['marketplacekey'])) return;
        
        $rsSalesOrderDetail = $salesOrder->getDetailById($pkey);
        
        $rs = array();
        $rs['header'] = $rsSalesOrder[0];
        $rs['detail'] = $rsSalesOrderDetail;
        
        $marketplaceObj = $this->getMarketplaceObj($rs['header']['marketplacekey']);
        if(empty($marketplaceObj)) return;
        
        $marketplaceObj[0]['obj']->onConfirmTrans($rs);
         
    }
    
    function onRequestPickup($pkey){  
        
        $salesOrder = new SalesOrder();  
        $rsSalesOrder = $salesOrder->getDataRowById($pkey); 
        if(empty($rsSalesOrder)) return;
        if(empty($rsSalesOrder[0]['marketplacekey'])) return;
        
        $rsSalesOrderDetail = $salesOrder->getDetailById($pkey);
        
        $rs = array();
        $rs['header'] = $rsSalesOrder[0];
        $rs['detail'] = $rsSalesOrderDetail;
        
        $marketplaceObj = $this->getMarketplaceObj($rs['header']['marketplacekey']);
        if(empty($marketplaceObj)) return;
        
        $marketplaceObj[0]['obj']->requestPickup($rs);
         
    }
    
    
    function onUpdateAWB($pkey,$awb){  
        
        $salesOrder = new SalesOrder();  
        $rsSalesOrder = $salesOrder->getDataRowById($pkey); 
        if(empty($rsSalesOrder)) return;
        if(empty($rsSalesOrder[0]['marketplacekey'])) return;
         
        
        $marketplaceObj = $this->getMarketplaceObj($rsSalesOrder[0]['marketplacekey']);
        if(empty($marketplaceObj)) return;
        
        $marketplaceObj[0]['obj']->updateAWB($pkey,$awb);
         
    }
    
    function resyncItemIfNotExist($arrItemKey){ 
        
        // $this->setTimeLog('start resync ' . $this->oDbCon->paramString($arrItemKey,','),true);
        
        $rsItemLink = $this->searchLinkItem($arrItemKey);
        $existedItem = array_column($rsItemLink,'refkey');
          
        // $this->setTimeLog($existedItem,true);
        
        $arrNotExist = array_diff($arrItemKey,$existedItem);  
        
        if(!empty($arrNotExist)){ 
            
            //$this->setTimeLog('>>> resync',true, 'mpsync');
            //$this->setLog($arrNotExist,true, 'mpsync');
            
            $syncCriteria = array(); 
            $syncCriteria['attr'] = array('name','brand', 'qoh', 'price','measurement', 'shortDescription','status','image', 'others'); // karena kalo stok awal 0, pas brg masuk, harga harus update ulang
            $syncCriteria['type'] = 2;  
            $syncCriteria['itemkey'] =  $arrNotExist;
            
            $this->syncProductsInAllMarketplace($syncCriteria);  
            // $this->syncProducts($syncCriteria); // kenapa dipanggil lg ?
            
            //$this->setTimeLog('>>> end resync',true, 'mpsync');
        }
        
        
        // $this->setTimeLog('end resync ' . $this->oDbCon->paramString($arrItemKey,','),true);
    }
     
    function updateProductsQOHInAllMarketplace($arrItemKey){  
        $itemMovement = new ItemMovement();
        $item = new Item();
        
        if (!is_array($arrItemKey))  
            $arrItemKey = array($arrItemKey); 
     
        $rsQOH = $itemMovement->getItemsQOH($arrItemKey);   
        $rsQOR = $itemMovement->getItemsQOR($arrItemKey);
        
        // kalo stoknya gk pernah ad, balikinnya empty row....
        // jadi harus manual tambahin row1 yg gk ad, biar QOH nya 0   
        $returnedItemKey =  array_column($rsQOH, 'itemkey');  
        $arrDiff = array_diff($arrItemKey, $returnedItemKey);
        
        if(!empty($arrDiff)){
        
            $arrDiff = array_values($arrDiff);
            
            $rsEmptyItem = $item->searchDataRow( 
                	            array(  $item->tableName.'.pkey', $item->tableName.'.code' , $item->tableName.'.isvariant', $item->tableName.'.parentkey' ) , 
                                ' and '.$item->tableName.'.pkey in ('. $this->oDbCon->paramString($arrDiff,',').')'
                           );  
            
            // manual add agar tetep keupdate 0
            foreach($rsEmptyItem as $itemRow){
                array_push($rsQOH, array(
                                'itemkey' => $itemRow['pkey'],
                                'itemcode' => $itemRow['code'],
                                'isvariant' => $itemRow['isvariant'],
                                'parentkey' => $itemRow['parentkey'],
                                'qtyinbaseunit' => 0
                        ));
                 
            }
            
        }
     
   
        $rsQOH = array_column($rsQOH,null, 'itemkey');
        $rsQOR = array_column($rsQOR,null, 'itemkey');
        
        // potong dulu dengan QOR
        foreach($rsQOH as $itemkey => $row){ 
            if (!isset($rsQOR[$itemkey])) continue; 
            $rsQOH[$itemkey]['qtyinbaseunit'] -=  $rsQOR[$itemkey]['qtyonreserveinbaseunit'];       
            if($rsQOH[$itemkey]['qtyinbaseunit'] < 0 ) $rsQOH[$itemkey]['qtyinbaseunit'] = 0;  
        }
        
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj){   
            $obj['obj']->updateProductsQOH($rsQOH);
        }
        
    }
           
    function updateProductsPriceInAllMarketplace($itemkey){ 
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj){ 
             $obj['obj']->updateProductsPrice($itemkey);
        } 
    }
        
    function updateProductsDescriptionInAllMarketplace($itemkey){ 
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj){ 
             $obj['obj']->updateProductsDescription($itemkey);
        } 
    }

    function updateStorefrontInAllMarketplace($arrStorefrontKey){   
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj){   
            $obj['obj']->updateStorefront($arrStorefrontKey);
        } 
    }
    
    function deleteProductsInAllMarketplace($rs){
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj) 
             $obj['obj']->deleteProduct($rs); 
    } 
    
    function importOrdersInAllMarketplace(){  
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj)    
             $obj['obj']->importOrders(); 
         
    }
      
    function updateARPaymentInAllMarketplace(){  
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj){ 
               try{ 
                    $this->oDbCon->startTrans(true); 
                    $obj['obj']->updateARPayment();  
                    $this->oDbCon->endTrans();
         
                } catch(Exception $e){
                    $this->oDbCon->rollback(); 
                }	 
        }
            
    }
	
    function boostItemInAllMarketplace(){  
		
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj){ 
			$obj['obj']->boostItem();  
			
//               try{ 
//                    $this->oDbCon->startTrans(true); 
//                    $obj['obj']->boostItem();  
//                    $this->oDbCon->endTrans();
//         
//                } catch(Exception $e){
//                    $this->oDbCon->rollback(); 
//                }	 
        }
            
    }
	
    function closeCompletedOrdersInAllMarketplace(){  
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj)  
             $obj['obj']->closeCompletedOrders(); 
        
    }
        
    function updateDeliveredOrders($arrRefId ,$useOrderId = true){ 
      // dari webhook
        if(!is_array($arrRefId))
            $arrRefId = array($arrRefId);
         
        $salesOrder = new SalesOrder();
        $field = ($useOrderId) ? $salesOrder->tableName.'.marketplaceorderid' :  $salesOrder->tableName.'.refcode';
         
        $rsSalesOrder = $salesOrder->searchDataRow(array($salesOrder->tableName.'.pkey',$salesOrder->tableName.'.code',$salesOrder->tableName.'.refcode',$salesOrder->tableName.'.statuskey'),
                                                   ' and '.$field.' in ('.$this->oDbCon->paramString($arrRefId,',').') and '.$salesOrder->tableName.'.statuskey in (2) 
                                                     and '.$salesOrder->tableName.'.marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey)
                                                  );
        // kedepan mungkin perlu ditambahkan, kalo masih menunggu, proses jd konfirmasi dulu
        
        foreach($rsSalesOrder as $row){   
            if($row['statuskey'] == 1) {   
               // $salesOrder->changeStatus($row['pkey'], TRANSACTION_STATUS['konfirmasi'], '',false, true);
               //$this->setLog('auto confirm '.$row['code']. ' - ' .$row['refcode'],true,'auto-tp.txt');
            } 
            
            $salesOrder->changeStatus($row['pkey'], TRANSACTION_STATUS['selesai'], '',false, true); 
            //$this->setLog('auto close '.$row['code']. ' - ' .$row['refcode'],true,'auto-tp.txt');
        }  
            
    }
    
    function cancelCanceledOrdersInAllMarketplace(){  
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj)  
             $obj['obj']->cancelCanceledOrders(); 
   
    }
    
    
    function syncProductsInAllMarketplace($syncCriteria = array()){ 
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj) 
             $obj['obj']->syncProducts($syncCriteria); 
             
             
		// perlu update ulang QOH karena harus motong OOR 
// 		$this->setLog($syncCriteria,true);
// buat looping forever kah ?
// 		$this->updateProductsQOHInAllMarketplace($syncCriteria['itemkey']);
    }
    
    function syncAllMarketplaceBrand($syncType){
        $marketplaceObj = $this->getMarketplaceObj(); 
        foreach($marketplaceObj as $obj)   
             $obj['obj']->syncMarketplaceBrand($syncType); 
    }
    
//	function syncAllMarketplaceBrandV2($syncType,$categorykey = array()){
//		// sinkronisasi secukupnya tergantung kebutuhan
//		
//        $marketplaceObj = $this->getMarketplaceObj(); 
//        foreach($marketplaceObj as $obj)   
//             $obj['obj']->syncMarketplaceBrandV2($syncType,$categorykey);  
//	}
//	
//	function syncMarketplaceBrandV2($syncType,$categorykey = array()){
//		
//	}
		
    function syncAllMarketplaceCategory($syncType){
        $marketplaceObj = $this->getMarketplaceObj();  
        foreach($marketplaceObj as $obj)  
             $obj['obj']->syncMarketplaceCategory($syncType);
        
    }
    
    function syncAllMarketplaceCategoryVariant(){
        $marketplaceObj = $this->getMarketplaceObj(); 
        foreach($marketplaceObj as $obj)  
             $obj['obj']->syncMarketplaceCategoryVariant();
        
    }
    
    function syncAllMarketplaceStorefront($syncType){
        // satu arah dr marketplace
        $marketplaceObj = $this->getMarketplaceObj(); 
        foreach($marketplaceObj as $obj)   
             $obj['obj']->syncMarketplaceStorefront($syncType); 
    }
    
    function syncAllMarketplaceCategoryAttributes($syncType){
        $marketplaceObj = $this->getMarketplaceObj(); 
        foreach($marketplaceObj as $obj) 
             $obj['obj']->syncMarketplaceCategoryAttributes($syncType);
    }
    
    function syncAllMarketplaceLogistics(){ 
        $marketplaceObj = $this->getMarketplaceObj(); 
        foreach($marketplaceObj as $obj)  
             $obj['obj']->syncMarketplaceLogistics(); 
    }
    
    function searchLinkItem($arrItemKey = array(), $criteria = '',$limit =''){
        
        if(!isset($this->marketplaceKey) || empty($this->marketplaceKey))
            return;
            
        $sql = 'select * from '.$this->tableItemMarketplaceLink.' where marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceKey);
         
        if(!empty($arrItemKey)){
            if(!is_array($arrItemKey)) $arrItemKey = array($arrItemKey);
            $sql .= ' and refkey in ('.$this->oDbCon->paramString($arrItemKey,',').') ';
        }
            
            
        if(!empty($criteria))
            $sql .= ' ' .$criteria;
            
        if(!empty($limit))
            $sql .= ' ' .$limit;
            
        $rs = $this->oDbCon->doQuery($sql);	
        
        return $rs;
    }
    
    function addItemMarketplaceLink($itemkey, $marketplaceitemkey, $marketplaceskukey = ''){
          
        $rs = $this->searchLinkItem($itemkey);
        
          try{ 
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	 
        	 
                // tokopedia kadang harus update belakangan
                if (!empty($rs)){
                    $sql = 'update '.$this->tableItemMarketplaceLink.' 
                            set marketplaceitemkey = '.$this->oDbCon->paramString($marketplaceitemkey).', marketplaceskukey = '.$this->oDbCon->paramString($marketplaceskukey).'
                            where refkey = '.$this->oDbCon->paramString($itemkey).' and marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey);
                }else{
                    $sql = 'insert into '.$this->tableItemMarketplaceLink.' 
                        (refkey, marketplacekey,marketplaceitemkey,marketplaceskukey ) 
                    values 
                        ('.$this->oDbCon->paramString($itemkey).',  '.$this->oDbCon->paramString($this->marketplaceKey).',  '.$this->oDbCon->paramString($marketplaceitemkey).',  '.$this->oDbCon->paramString($marketplaceskukey).') ';
                   
                }
         
                $this->oDbCon->execute($sql);	
                 
    			$this->oDbCon->endTrans();
    			//$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    } catch(Exception $e){
			$this->oDbCon->rollback();
			//$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
        

    }
    
     function deleteItemMarketplaceLink($itemkey = '', $marketplaceitemkey = '', $marketplaceskukey = ''){
     
         try{  
             
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	 
			
            $sql = 'delete from 
                        '.$this->tableItemMarketplaceLink.' 
                    where 
                        marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceKey);
             
            if (!empty($itemkey)) 
             $sql .= ' and refkey = ' .$this->oDbCon->paramString($itemkey);
           
            if (!empty($marketplaceitemkey)) 
             $sql .= ' and marketplaceitemkey = ' .$this->oDbCon->paramString($marketplaceitemkey);
           
            if (!empty($marketplaceskukey)) 
             $sql .= ' and marketplaceskukey = ' .$this->oDbCon->paramString($marketplaceskukey);
            
             $this->oDbCon->execute($sql);	
                    
			 $this->oDbCon->endTrans();
		
			//$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    } catch(Exception $e){
			$this->oDbCon->rollback();
			//$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		 
         
     }

    function getMarketplaceObj($marketplaceKey = ''){
         
        $criteria = array();
        if (!empty($marketplaceKey))
            array_push($criteria,' and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($marketplaceKey) );
        
        $criteria = implode(' ' ,$criteria);
         
        $rsMarketplace = $this->searchDataRow(array ($this->tableName.'.pkey',$this->tableName.'.name',$this->tableName.'.refmarketplacekey'),
											  ' and '.$this->tableName.'.statuskey = 1 ' .$criteria);
        
        $objMarketPlace = array();
        foreach($rsMarketplace as $row){
           // $marketplaceName = strtolower($row['name']); 

            switch($row['refmarketplacekey']){
                case MARKETPLACE['lazada'] : $mpObj = new Lazada($row['pkey']); break;
                case MARKETPLACE['shopee'] : $mpObj = new Shopee($row['pkey']); break;
                case MARKETPLACE['tokopedia'] : $mpObj = new Tokopedia($row['pkey']); break;
                default : $mpObj = null; 
            }

            array_push($objMarketPlace,array('key' => $row['pkey'],'name' => $row['name'], 'refmarketplacekey' => $row['refmarketplacekey'],  'obj' => $mpObj ));
        }
 
        return $objMarketPlace;
    }
    
    function getProducts($criteria = array()){
     
    }
    
    function syncProducts($syncCriteria = array()){ 
          
        
        // $syncType = 1; // Patokan item dr marketplace, dicocokan ke database sistem utk update data
        // $syncType = 2; // Patokan dr sistem, loop setiap item dicek ke marketplace, kalo blm ad di ADD, kalo sudah ad di UPDATE
     
        $item = new Item();  
         
        $syncType = (isset($syncCriteria['type']) && !empty($syncCriteria['type'])) ? $syncCriteria['type'] : 1;
        
//        $this->setLog('>>>',true,'tp');
//        $this->setLog($syncType,true,'tp');
//        $this->setLog($syncCriteria['itemkey'],true,'tp');
        
        $arrExistingCode = array();
        
        // cari semua item yg sudah pernah link dengan database
        $rsLinkItem = $this->searchLinkItem();
        $arrItemLinkedWithMarketplace = array_column($rsLinkItem,'refkey');
         
        switch ($syncType) {
                
            // based on marketplace item
            case 1:    
                    // get products from marketplace
                    $arrProducts = $this->getProducts($syncCriteria);
                    $arrExistingCode = array_column($arrProducts,'SellerSku');

                    // get item from database that match item code
                    $rsItem = $item->searchData('', '',true, ' and item.code in  (' . $item->oDbCon->paramString($arrExistingCode,',').')');  
                    $syncCriteria['itemkey'] = array_column($rsItem,'pkey');
                    
                    break;

            // based on item database
            case 2 :       
                    if (isset($syncCriteria['itemkey']) && !empty($syncCriteria['itemkey'])){ 
                        // kalo sudah ditentukan itemnya, gk perlu cek di marketplace ada atau gk
                        
                    }else{   
                        $limit = (isset($syncCriteria['limit']) && !empty($syncCriteria['limit'])) ? ' limit ' . $syncCriteria['limit'] : '';
                        $rsItem = $item->searchData($item->tableName.'.statuskey', 1 ,true,'','', $limit);  
                        $syncCriteria['itemkey'] = array_column($rsItem,'pkey');
                        //$this->setLog($syncCriteria['itemkey']);
                    }
                    break;

            default: 
        }
 


        $arrItemKey = (isset($syncCriteria['itemkey']) && !empty($syncCriteria['itemkey'])) ? $syncCriteria['itemkey'] : array();
        
        if (!is_array($arrItemKey))  
            $arrItemKey = array($arrItemKey); 
 
        $rsItemColl = $item->searchData('', '',true, ' and item.pkey in  (' . $item->oDbCon->paramString($arrItemKey,',').')');  
        $rsItemColl = array_column($rsItemColl,null,'pkey');
 
        foreach($arrItemKey as $itemkey){ 
             
            // cek dulu itemnya perlu sync gk ke setiap marketplace
            if(!$item->isItemSyncToMarketplace($itemkey,$this->marketplaceKey)) continue;
               
            if(!in_array($itemkey,$arrItemLinkedWithMarketplace)) {  
                $this->createProduct($itemkey);  
            }else  {  
                $this->updateProduct($itemkey,$rsItemColl,$syncCriteria); 
            }
        } 
         
    }
	
    function initMarketplace ($marketplaceProviderKey, $marketplaceKey = ''){ 
		
		// kalo marketplacekeynya gk ad, mabil yg pertama aj
		
		$criteria =  ' and '. $this->tableName.'.statuskey = 1';
		
		if(!empty($marketplaceKey))
			$criteria .= ' and '. $this->tableName.'.pkey = '.$this->oDbCon->paramString($marketplaceKey);
			
        $rsMarketplace = $this->searchData($this->tableName.'.refmarketplacekey', $marketplaceProviderKey, true,$criteria);
		
		//$this->setLog($marketplaceName,true);
		//$this->setLog($rsMarketplace,true);
		
        if (!empty($rsMarketplace)){ 
            $this->rsMarketplace = $rsMarketplace[0]; 
            $this->marketplaceKey =  $rsMarketplace[0]['pkey']; 
            $this->marketplaceProviderKey =  $rsMarketplace[0]['refmarketplacekey']; 
            $this->marketplaceAutoPickup =  $rsMarketplace[0]['autopickup']; 
            $this->rowsLimit =  $rsMarketplace[0]['rowslimit']; 
            $this->marketplaceName =  $rsMarketplace[0]['name']; 
            $this->ARPaymentMethodKey =  $rsMarketplace[0]['paymentmethodkey']; 
            
            //sementara
            if($this->marketplaceProviderKey == MARKETPLACE['tokopedia']){
                $dbCon = $this->masterConn(); 
                $sql = 'select token from '.$this->tableMarketplaceProviderAccount.' where '.$this->tableMarketplaceProviderAccount.'.refkey = ' .MARKETPLACE['tokopedia'];  
                $rsToken = $dbCon->doQuery($sql);
                $dbCon = null; 
                $this->accessToken = $rsToken[0]['token'];
            }else{
                $this->accessToken = $rsMarketplace[0]['accesstoken'];
            }
            
			
			// lazada kayanya tokennya per user, nanti dicek saja lg
			/*elseif($this->marketplaceProviderKey == MARKETPLACE['lazada']){
                $dbCon = $this->masterConn(); 
                $sql = 'select token from '.$this->tableMarketplaceProviderAccount.' where  '.$this->tableMarketplaceProviderAccount.'.refkey = '.MARKETPLACE['lazada'];    
                $rsToken = $dbCon->doQuery($sql); 
                $dbCon = null; 
                $this->accessToken = $rsToken[0]['token'];
            }*/
			
            $this->shopId = intval($rsMarketplace[0]['shopid']);
			
			// harusnya gk masalah, karena ketika ad request, sselalu bentuk obj baru, dan ini selalu kepanggil
			$this->refCode = $rsMarketplace[0]['refcode'];
			$this->refreshToken =  $rsMarketplace[0]['refreshtoken'];
        }
    }
    
    function getMarketplaceBrandForAutoComplete($criteria = '', $limit = ''){
         
        $dbCon = $this->masterConn(); 
        $sql = 'select pkey,marketplacebrandkey, name as value from '.$this->tableMarketplaceBrand. ' where marketplacekey = ' . $this->marketplaceProviderKey;
        
        $sql .= ' ' .$criteria;
        
        if (!empty($limit))
            $sql .= ' ' .$limit;
         
        $rs = $dbCon->doQuery($sql);
        $dbCon = null;
        
         for($i=0;$i<count($rs);$i++) 
            $rs[$i]['value'] = htmlspecialchars_decode($rs[$i]['value']); 
        
        return $rs;
    }
    
    
    function getMarketplaceLogisticsForAutoComplete($criteria = '', $limit = ''){
         
        $dbCon = $this->masterConn(); 
        $sql = 'select logisticid as pkey, name as value from '.$this->tableMarketplaceLogistics. ' where marketplacekey = ' . $this->marketplaceProviderKey;
        
        $sql .= ' ' .$criteria;
        
        if (!empty($limit))
            $sql .= ' ' .$limit;
         
       
        $rs = $dbCon->doQuery($sql);
        $dbCon = null;
        
         for($i=0;$i<count($rs);$i++) 
            $rs[$i]['value'] = htmlspecialchars_decode($rs[$i]['value']); 
        
        return $rs;
    }
    
    function getMarketplaceBrand($criteria = '', $limit = ''){ 
        $dbCon = $this->masterConn(); 
        $sql = 'select * from '.$this->tableMarketplaceBrand. ' where marketplacekey = ' . $this->marketplaceProviderKey;
        
        $sql .= ' ' .$criteria;
        $sql .= ' ' .$limit;
         
        $rs = $dbCon->doQuery($sql);
        $dbCon = null;
        return $rs;
    } 
     
    function getMarketplaceCondition($criteria = '', $limit = ''){ 
        $dbCon =  $this->masterConn(); 
        $sql = 'select * from '.$this->tableMarketplaceCondition. ' where marketplacekey = ' . $this->marketplaceProviderKey;
        
        $sql .= ' ' .$criteria;
        $sql .= ' ' .$limit;
          
        //$this->setLog($sql,true);
        $rs = $dbCon->doQuery($sql);
        $dbCon = null;
        return $rs;
    } 
    
    function getMarketplaceCategory($criteria = '', $limit = ''){ 
        $dbCon =  $this->masterConn(); 
        $sql = 'select * from '.$this->tableMarketplaceCategory. ' where statuskey = 1 and marketplacekey = ' . $this->marketplaceProviderKey;
        
        $sql .= ' ' .$criteria;
        
        $sql .= ' order by '.$this->tableMarketplaceCategory. '.name asc';
        
        if (!empty($limit))
            $sql .= ' ' .$limit;
        
        $rs = $dbCon->doQuery($sql);
        $dbCon = null;
        return $rs;
    } 
        
    function getMarketplaceLogistics($criteria = '', $limit = ''){ 
        $dbCon = $this->masterConn(); 
        $sql = 'select * from '.$this->tableMarketplaceLogistics. ' where statuskey = 1 and marketplacekey = ' . $this->marketplaceProviderKey;
        
        $sql .= ' ' .$criteria;
        
        $sql .= ' order by '.$this->tableMarketplaceLogistics. '.name asc';
        
        if (!empty($limit))
            $sql .= ' ' .$limit;
		
        $rs = $dbCon->doQuery($sql);
        $dbCon = null;
        return $rs;
    } 
        
    function getMarketplaceCategoryAttributes($categorykey='', $criteria = '', $order = ' order by attributekey asc ',$limit = '', $excludePrimaryFields = true){ 
        // $categorykey yg digunakan adalah pkey marketplace_category di Minerva
        
        $dbCon = $this->masterConn(); 
        $sql = 'select * from '.$this->tableMarketplaceCategoryAttributes. ' where marketplacekey = ' . $this->marketplaceProviderKey;
        
        if (is_numeric($categorykey)) // utk bedain kosong dan 0
            $sql .= ' and marketplacecategorykey = ' . $this->oDbCon->paramString($categorykey);
        
        $sql .= ' ' .$criteria;
        
        if($excludePrimaryFields){ 
            $rsExclude = $this->getExcludeAttributes();
            $rsExclude = array_column($rsExclude,'attributekey'); 
             
            $sql .= ' AND attributekey not in (' .$this->oDbCon->paramString($rsExclude,',').')';
            $sql .= ' AND label not in (' .$this->oDbCon->paramString($rsExclude,',').')';
             
            // SHOPEE patokanny label karena kalo attributekey nya bisa byk, sama2 "merek" tp id nya beda2
        }
        
        if (!empty($limit))
            $sql .= ' ' .$limit;
           
        
        $sql .= $order;
         
		//$this->setLog($sql,true);
        $rs = $dbCon->doQuery($sql);
        $dbCon = null;

        return $rs;
    } 
    
    function getExcludeAttributes($forUpdateOnly = false){
         $dbCon = $this->masterConn(); 
        
         $sqlExclude = 'select attributekey,paramname,reftable from '.$this->tableMarketplaceAttributesExclude.' where marketplacekey in (0,'. $this->marketplaceProviderKey.')';
         
         if($forUpdateOnly)
             $sqlExclude .= ' and '.$this->tableMarketplaceAttributesExclude.'.update = 1';
        
         $rsExclude = $dbCon->doQuery($sqlExclude);
         $dbCon = null;
         return $rsExclude; 
    }
    
    function getBrandUsedForMarketplace($brandkey,$categoryInformation = array()){
        $brand = new Brand();
         
        $rsBrand = $brand->getMarketplaceBrand($brandkey,
                                                array('marketplaceKey' =>$this->marketplaceKey, 'marketplaceProviderKey'=>$this->marketplaceProviderKey),
                                                $categoryInformation
                                                );  
        
        if(!empty($rsBrand)){ 
            $rsBrand[0]['name'] = $rsBrand[0]['marketplacebrandname'];
        }else{  
            //$rsBrand = $brand->getDataRowById($brandkey); 
			$rsBrand = array();
			$rsBrand[0]['pkey'] = 0;
			$rsBrand[0]['name'] = ($this->marketplaceKey == 2) ? 'NoBrand' : 'No Brand'; 
        }
        
        return $rsBrand;
        
    }
    
    function getBrandOption($arrBrandKey){
        $brand = new Brand();
        
		$arrReturn = array();
		
		$dbCon = $this->masterConn(); 
		
		$sql = 'select marketplacebrandkey, name from '.$this->tableMarketplaceBrand.' 
				where marketplacekey = '.$this->oDbCon->paramString($this->marketplaceProviderKey).' and marketplacebrandkey in ('.$this->oDbCon->paramString( $arrBrandKey,',').')';
//		$this->setLog($sql,true);
		
		$rs = $dbCon->doQuery($sql);
		$arrReturn  = array_column($rs,'name','marketplacebrandkey');
		
		$dbCon = null;
		
        return $arrReturn;
    }
    
        
    function getItemConditionForMarketplace($conditionkey){
        
        // kalo gk ad link brand, pake brand sendiri
        $itemCondition = new ItemCondition();
        $marketplaceConditionKey = $itemCondition->getMarketplaceCondition($conditionkey, $this->marketplaceKey);
        
        return (isset($this->itemCondition[$marketplaceConditionKey])) ? $this->itemCondition[$marketplaceConditionKey] : '';
        
    }
        
    function getStorefrontUsedForMarketplace($categorykey,$brandkey=''){
        
		//$this->setLog('$this->marketplaceKey ' . $this->marketplaceKey,true); 
		// dari category
        $itemCategory = new ItemCategory();
        $rs = $itemCategory->getMarketplaceStorefront($categorykey, $this->marketplaceKey);
		
		// harusnya cuma balikin 1 row 
		$arrStorefrontKey = json_decode($rs[0]['marketplacestorefrontkey']);
		if(!is_array($arrStorefrontKey)) $arrStorefrontKey = array($arrStorefrontKey); 
		
		// dari merek
		if(!empty($brandkey)){
			$brand = new Brand();
			$rs = $brand->getMarketplaceStorefront($brandkey, $this->marketplaceKey);
			
			if(!empty($rs)){ 
				// harusnya cuma balikin 1 row 
				$arrBrandStorefrontKey = json_decode($rs[0]['marketplacestorefrontkey']);
				if(!is_array($arrBrandStorefrontKey)) $arrBrandStorefrontKey = array($arrBrandStorefrontKey);  
				$arrStorefrontKey = array_merge($arrStorefrontKey, $arrBrandStorefrontKey);	
			}
		}
		
		//$this->setLog($arrStorefrontKey,true);
        return $arrStorefrontKey; 
    }
    
    function getCategoryUsedForMarketplace($categorykey){
        $itemCategory = new ItemCategory();
        
        // kalo gk ad link kategoru, pake kategori sendiri
        $rsCategory = $itemCategory->getMarketplaceCategory($categorykey,$this->marketplaceKey);  
        if(!empty($rsCategory)){ 
            //$rsCategory[0]['pkey'] = $rsCategory[0]['marketplacecategoryid'];
        }else{  
            $rsCategory = $itemCategory->getDataRowById($categorykey); 
            $rsCategory[0]['marketplacecategorykey']  =  $rsCategory[0]['pkey'] ;
        }
        
        return $rsCategory;
        
    }
    
    function addMarketplaceBrand($brandRow){ 
        
        $dbCon = $this->masterConn(); 
        
        $brandName = $brandRow['name']; 
        $brandKey = $brandRow['pkey'];
        
        try{ 
			
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	 
            $sql = 'insert into  '.$this->tableMarketplaceBrand.'(
                        marketplacebrandkey, 
                        marketplacekey,
                        name 
                    ) 
                    values(
                        '.$this->oDbCon->paramString($brandKey).',
                        '.$this->oDbCon->paramString($this->marketplaceProviderKey).', 
                        '.$this->oDbCon->paramString($brandName).' 
                    ) ';
           
            $dbCon->execute($sql);	 
			$dbCon->endTrans(); 
            
            //$this->setLog("Add brand " . $brandRow['name'],true,$this->marketplaceName);
		
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}		
        
        $dbCon = null;

    }
    
    
    function updateMarketplaceBrand($brandRow){
         
        $dbCon = $this->masterConn(); 
        
        $brandName = $brandRow['name']; 
        $brandKey = $brandRow['pkey'];
        
        try{ 
			
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	 
            $sql = 'update 
                        '.$this->tableMarketplaceBrand.' 
                    set 
                        name = '.$this->oDbCon->paramString($brandName).'  
                    where 
                        marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey).' and
                        marketplacebrandkey = '.$this->oDbCon->paramString($brandKey).' ';
           
            $dbCon->execute($sql);	 
			$dbCon->endTrans(); 
		
            //$this->setLog("Update brand " . $brandRow['name'],true,$this->marketplaceName);
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}		
        
        $dbCon = null;

    }
    
    
     function addMarketplaceCategory($categoryRow){ 
        
        $dbCon = $this->masterConn(); 
        
        $categorykey = $categoryRow['marketplacecategorykey'];
        $categoryName = $categoryRow['name']; 
        $parentkey = $categoryRow['parentkey'];
        $leaf = ($categoryRow['leaf']) ? 1 : 0;
        
        try{ 
			
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	 
            $sql = 'insert into  '.$this->tableMarketplaceCategory.'(
                        marketplacecategorykey, 
                        marketplacekey,
                        name ,
                        parentkey,
                        isleaf,
                        statuskey 
                    ) 
                    values(
                        '.$this->oDbCon->paramString($categorykey).',
                        '.$this->oDbCon->paramString($this->marketplaceProviderKey).', 
                        '.$this->oDbCon->paramString($categoryName).',
                        '.$this->oDbCon->paramString($parentkey).',
                        '.$this->oDbCon->paramString($leaf).',
                        1 
                    ) ';
           
            $dbCon->execute($sql);	 
			$dbCon->endTrans(); 
            
            //$this->setLog("Add category " . $categoryRow['name'],true,$this->marketplaceName);
		
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}		
         
         $dbCon = null;
    }
    
    
    function updateMarketplaceCategory($categoryRow){
         
        $dbCon = $this->masterConn(); 
        
        $categoryName = $categoryRow['name']; 
        $categorykey = $categoryRow['pkey'];
        
        try{ 
			
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	 
            $sql = 'update 
                        '.$this->tableMarketplaceCategory.' 
                    set 
                        name = '.$this->oDbCon->paramString($categoryName).'  
                    where 
                        marketplacekey = '.$this->oDbCon->paramString($this->marketplaceProviderKey).' and
                        pkey = '.$this->oDbCon->paramString($categorykey).' ';
           
            //$this->setLog("Update category " . $categoryRow['name'],true,$this->marketplaceName);
            
            $dbCon->execute($sql);	 
			$dbCon->endTrans(); 
		
	    } catch(Exception $e){ 
			$dbCon->rollback(); 
		}		
         
         $dbCon = null;
    }
    
    function addMarketplaceCategoryAttributes($categoryRow,$response){ 
        
        $dbCon = $this->masterConn(); 
         
        try{ 
			
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	  
            $sql = 'insert into  '.$this->tableMarketplaceCategoryAttributes.'( 
                        marketplacecategorykey,
                        marketplacekey,
                        attributekey, 
                        label,
                        attributetype,
                        inputtype,
                        ismandatory,
                        value 
                    ) 
                    values(
                        '.$this->oDbCon->paramString($categoryRow['marketplacecategorykey']).',
                        '.$this->oDbCon->paramString($this->marketplaceProviderKey).', 
                        '.$this->oDbCon->paramString($response['attributekey']).' , 
                        '.$this->oDbCon->paramString($response['label']).'  , 
                        '.$this->oDbCon->paramString($response['attributeType']).', 
                        '.$this->oDbCon->paramString($response['inputType']).', 
                        '.$this->oDbCon->paramString($response['isMandatory']).' , 
                        \''.addslashes($response['value']).'\' 
                    ) ';
           
            $dbCon->execute($sql);	 
			$dbCon->endTrans(); 
             
		
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}		
        
         $dbCon = null;

    }
    
       
    function updateMarketplaceCategoryAttributes($arrKey,$response){
        $dbCon =  $this->masterConn(); 
         
        try{ 
			
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	  
            // updatenya berdasarkan pkey attribute saja (attributekey)
            
            $sql = 'update  '.$this->tableMarketplaceCategoryAttributes.'
                    set     
                        label = '.$this->oDbCon->paramString($response['label']).',
                        attributetype = '.$this->oDbCon->paramString($response['attributeType']).',
                        inputtype = '.$this->oDbCon->paramString($response['inputType']).',
                        ismandatory = '.$this->oDbCon->paramString($response['isMandatory']).',
                        value = \''.addslashes($response['value']).'\' 
                    where
                        marketplacekey = '.$this->oDbCon->paramString($this->marketplaceProviderKey);
            
            $arrCriteria = array();
            foreach($arrKey as $key=>$value)
                array_push( $arrCriteria, $key .' = '.$this->oDbCon->paramString($value));
            
            if(!empty($arrCriteria))
                $sql .= ' and '. implode(' and ',$arrCriteria);
            
            //$this->setLog($sql,true);
            
            $dbCon->execute($sql);	 
			$dbCon->endTrans(); 
             
		
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}		
        
        $dbCon = null;
    }
    
 
    function getMarketplaceCategoryByCategoryId($id){
        $dbCon =   $this->masterConn(); 
        $sql = 'select * from  '.$this->tableMarketplaceCategory.'  where marketplacecategorykey = ' . $this->oDbCon->paramString($id). ' and marketplacekey = ' . $this->marketplaceProviderKey ;
        $rs = $dbCon->doQuery($sql);
        $dbCon = null;
        return $rs;
    }
 
    function getMarketplaceStorefrontForAutoComplete($criteria = '', $limit = ''){
          
        $sql = 'select '.$this->tableMarketplaceStorefront. '.pkey, '.$this->tableMarketplaceStorefront. '.name as value from '.$this->tableMarketplaceStorefront. ' where isleaf = 1 and statuskey = 1 and marketplacekey = ' . $this->marketplaceKey;
        
        $sql .= ' ' .$criteria;
        
        if (!empty($limit))
            $sql .= ' ' .$limit;
        
        $rs = $this->oDbCon->doQuery($sql);
     
         
        return $rs; 
    }
     
    function getMarketplaceCategoryForAutoComplete($criteria = '', $limit = ''){
         
		// ambil kategori dr jenis providernya
        $dbCon =  $this->masterConn(); 
        $sql = 'select marketplacecategorykey as pkey, name as value
				from '.$this->tableMarketplaceCategory. ' 
				where isleaf = 1 and statuskey = 1 
						and marketplacekey = ' . $this->marketplaceProviderKey;
        
        $sql .= ' ' .$criteria;
        
        if (!empty($limit))
            $sql .= ' ' .$limit;
         
        $rs = $dbCon->doQuery($sql);
        $dbCon = null;
        
        for($i=0;$i<count($rs);$i++) { 
            $rsPath = $this->getPath($rs[$i]['pkey']); 
            $rs[$i]['name'] = $rs[$i]['value'] ; 
            $rs[$i]['value'] = htmlspecialchars_decode($rsPath[0]['path']); 
         }
        
        return $rs; 
    }
    
    function getPath($categorykey, $pathSeparator = ' / '){
        $arrPath = array();
        $arrTempPath = array();
        
        $rsCat = $this->getMarketplaceCategoryByCategoryId($categorykey);  
        array_unshift($arrTempPath, $rsCat[0]['name']);  
        
        $arrResult = array();
        $arrResult['name'] = $rsCat[0]['name'];
        $arrResult['pkey'] = $rsCat[0]['pkey'];
        $arrResult['marketplacecategorykey'] = $rsCat[0]['marketplacecategorykey'];
        $arrResult['path'] = implode($pathSeparator,$arrTempPath);
        array_unshift($arrPath, $arrResult);
        
        while($rsCat[0]['parentkey'] <> 0) { 
            $rsCat = $this->getMarketplaceCategoryByCategoryId($rsCat[0]['parentkey']);  
            
            array_unshift($arrTempPath, $rsCat[0]['name']);  
            
            $arrResult = array();
            $arrResult['name'] = $rsCat[0]['name'];
            $arrResult['pkey'] = $rsCat[0]['pkey'];
            $arrResult['marketplacecategorykey'] = $rsCat[0]['marketplacecategorykey'];
            $arrResult['path'] = implode($pathSeparator,$arrTempPath); 
            array_unshift($arrPath, $arrResult); 
        }
 
        return $arrPath ;
    }
    
    function getAirwayBill($arrOrderId = ''){
        
    }
    
    function getSelectOptions($value){ 
        // shopee
        //$temp = json_decode(htmlspecialchars_decode($value)); 
        $temp = json_decode($value); 
        $temp = array_column($temp,'display_value_name'); // v2
		
        $options = array();
        foreach($temp as $optionRow){
            $options[$optionRow] = $optionRow;
        } 
         
        return $options;
    }
    
    
    function getValueFromTable($table,$pkey){
            $sql = 'select name from '.$table.' where pkey = ' . $this->oDbCon->paramString($pkey);
            $rs =  $this->oDbCon->doQuery($sql);
            return $rs[0]['name'];
    }
    
    function dataMoatLoginInAllMarketplace($arrParam){
       /* $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj){ 
             $obj['obj']->dataMoatLogin($arrParam);
        } */
    }
        
    function dataMoatLogin($arrParam){ }
    
    
    function dataMoatComputeRiskInAllMarketplace($arrParam){
        $marketplaceObj = $this->getMarketplaceObj();
        foreach($marketplaceObj as $obj){ 
             $obj['obj']->dataMoatComputeRisk($arrParam);
        } 
    }
        
    function dataMoatComputeRisk($arrParam){}
    
    function syncMarketplaceStorefront($syncType = ''){}
     
    
    function getMarketplaceStorefront($pkey='',$marketplacekey ='', $criteria = ''){
        $sql = 'select * from '.$this->tableMarketplaceStorefront.' where 1=1';
        
        if(!empty($pkey))
            $sql .= ' and refkey = ' .$this->oDbCon->paramString($pkey);
        
        if(!empty($marketplacekey))
            $sql .= ' and marketplacekey = ' .$this->oDbCon->paramString($marketplacekey);
          
        if(!empty($criteria))
            $sql .= $criteria;
         
        return $this->oDbCon->doQuery($sql);
    }
    
    function cacheImageForMarketplace($itemkey,$filename){ 
        
            $tempSubPath = 'item/'.$itemkey.'/';

            $sourceFile = DEFAULT_DOC_UPLOAD_PATH.$tempSubPath.$filename; 
            if(!file_exists($sourceFile) || !is_file($sourceFile)) return ''; 

            $destinationPath = $this->fckDOCUploadPath .$tempSubPath; 
            if(!is_dir($destinationPath))  mkdir ($destinationPath,  0755, true);
            $destinationfile = $destinationPath.$filename;

            copy($sourceFile, $destinationfile);
            sleep(1);

            $path = $this->fckURLUploadPath.$tempSubPath.$filename; 
 
            return $path;  
        
    }
    
    function cancelCanceledOrders(){ 
        // marketplace
    }
    
    function getItemPrice($itemkey){
       
        $item = new Item();
        $rsItem = $item->getDataRowById($itemkey); 
        
        $returnArr = array(); 
       
        $normalPrice = $rsItem[0]['sellingprice'];
        $adjustedPrice = $rsItem[0]['sellingprice'];
        $discountedPrice = $rsItem[0]['sellingprice'];
        
        $inTestedItem = false;
        
        // khusus positano 
        
		
        if ($inTestedItem){
            // lakukan penyesuaian harga utk marketplace (bkn biaya admin !)
            
            if(!empty($this->rsMarketplace['margin'])){
                $margin = $this->rsMarketplace['margin'];
                $marginType = $this->rsMarketplace['margintype'];

                $margin = ($marginType == 2 ) ? ($normalPrice * $margin/100) : $margin; 
                $adjustedPrice += $margin;
            }
            
            
             // untuk adjust biaya admin marketplace
            if(!empty($this->rsMarketplace['priceadjustment'])){
                $priceAdjustment = $this->rsMarketplace['priceadjustment'];
                $priceAdjustmentType = $this->rsMarketplace['priceadjustmenttype'];

                $priceAdjustment = ($priceAdjustmentType == 2 ) ? ($adjustedPrice * $priceAdjustmentType/100) : $priceAdjustment;
            }

            
            // cek ad diskon gk....
            // cek tipe hasil akhir diskonnya ap. apakah potong harga atau ke harga normal
            // kalo harga normal, berarti yg dipake adalah harga normal plus adjustment (admin)
            
          /*  $sql = 'select 
                        '.$this->tableCampaignPrice.'.* 
                    from 
                        '.$this->tableCampaignPrice.',
                        '.$this->tableCampaign.'
                    where 
                        '.$this->tableCampaignPrice.'.refkey =  '.$this->tableCampaign.'.pkey and
                        '.$this->tableCampaign.'.startdate <= date(now()) and
                        '.$this->tableCampaign.'.enddate >= date(now()) and
                        '.$this->tableCampaign.'.statuskey in (2,3) and 
                        '.$this->tableCampaignPrice.'.marketplacekey = '.$this->marketplaceKey.' and
                        itemkey = ' . $this->oDbCon->paramString($itemkey).'
                    order by startdate desc, '.$this->tableCampaign.'.pkey desc limit 1    
                    '; 

            //$this->setLog($sql,true);
            $rs = $this->oDbCon->doQuery($sql);*/
            
           /* if(!empty($rs)){
                $normalPrice = $rs[0]['normalprice'];
                $adjustedPrice = $rs[0]['adjustedprice'];
                $discountedPrice = $rs[0]['discountedprice'];
            }*/

           

            $adjustedPrice += $priceAdjustment;
            $discountedPrice += $priceAdjustment;
         
            //$this->setLog('update price => ' . $rsItem[0]['name'], true);
        }
       
        
        $returnArray = array('normalprice' => $normalPrice, 'adjustedprice' => $adjustedPrice, 'discountedprice' => $discountedPrice );
        //$this->setLog($returnArray,true);  
        return $returnArray;
        
    }
    
    
    function setMarketplaceLog($param){
          
         //sementara kalo gk ad action, return
         if(!isset($param['actionkey']) || empty($param['actionkey']) ) return;
        
         try{		
		 	
			if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
           
            $actionkey = (isset($param['actionkey'])) ? $param['actionkey'] : 0; 
            $errorCode = (isset($param['errorCode'])) ? $param['errorCode'] : 0;
            $url = (isset($param['url'])) ? $param['url'] : '';
            $message = (isset($param['message'])) ? $param['message'] : '';
            $response = (isset($param['response'])) ? $param['response'] : '';
            $ref = (isset($param['ref'])) ? html_entity_decode($param['ref']) : ''; // biar gk muncul &amp; utk ref nama item
            $isSuccess =  (isset($param['issuccess'])) ? $param['issuccess'] : false;
              
            // PRINT AIRWAYBILL
            $logResult = array('issuccess' => $isSuccess, 'message' => $response, 'errorcode' => $errorCode);
             
            switch($actionkey){
                case $this->actionType['printAirwayBill'] : 
                        $logResult = $this->logPrintAirwayBill($response);
                        break;
                case $this->actionType['testConnection'] :
                        $logResult['message'] = '';
                        break; 
                case $this->actionType['updateProduct'] :
                        $logResult = $this->logUpdateProduct($response);
                        break; 
                case $this->actionType['updateProductQOH'] :
                        $logResult = $this->logUpdateProductQOH($response);
                        break;  
                case $this->actionType['importOrders'] :
                        $logResult = $this->logImportOrders($response);
                        break; 
            }
 
            $isSuccess = $logResult['issuccess'];
            $errorCode = (isset($logResult['errorcode'])) ? $logResult['errorcode'] : 0;  
            
            $message = (!empty($message)) ? array($message) : array();
             
            if (!empty($logResult['message']))
              array_push($message, $logResult['message']);
            
            $message = implode(chr(13),$message); 
              
             
            $sql = 'insert into ' . $this->tableMarketplaceLog.'
                    (createdon,marketplacekey,actionkey, url,issuccess,errorcode,ref,message )
                    values (
                        now(), 
                        '.$this->oDbCon->paramString($this->marketplaceKey).',
                        '.$this->oDbCon->paramString($actionkey).',
                        '.$this->oDbCon->paramString($url).',
                        '.$this->oDbCon->paramString($isSuccess).',
                        '.$this->oDbCon->paramString($errorCode).',
                        '.$this->oDbCon->paramString($ref).',
                        '.$this->oDbCon->paramString($message).'  
                    )
                    ';

            $this->oDbCon->execute($sql);  
            $this->oDbCon->endTrans();  

		}catch(Exception $e){
			$this->oDbCon->rollback(); 
		}			
        
    }
    
    function generateMarketplaceLog($criteria='',$order=''){
        
        $sql = 'select 
                    '.$this->tableMarketplaceLog.'.*,
                    '.$this->tableName.'.name as marketplacename,
                    '.$this->tableMarketplaceActionType.'.name as actionname,
                     IF(issuccess=1, "'.$this->lang['success'].'", "'.$this->lang['failed'].'") as resultstate 
                from 
                    '.$this->tableMarketplaceLog.',
                    '.$this->tableMarketplaceActionType.',
                    '.$this->tableName.'
                where
                    '.$this->tableMarketplaceLog.'.marketplacekey = '.$this->tableName.'.pkey and
                    '.$this->tableMarketplaceLog.'.actionkey = '.$this->tableMarketplaceActionType.'.pkey  ';
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 

        if (!empty($order))  
            $sql .=  ' ' .$order; 
        
        return $this->oDbCon->doQuery($sql);

    }
    
    function getShipmentDetailByName($name,$marketplacekey = ''){
        
        $rsLogistic = $this->getMarketplaceLogistics( ' and '.$this->tableMarketplaceLogistics.'.name = ' .  $this->oDbCon->paramString($name));
        
        if(empty($rsLogistic)) return 0;
        
        $marketplacekey = (empty($marketplacekey)) ? $this->marketplaceKey : $marketplacekey;
        
        $sql = 'select 
                    '.$this->tableShipmentDetails.'.refkey 
                from 
                    '.$this->tableShipmentDetails.' 
                where '.$this->tableShipmentDetails.'.marketplacekey = ' . $this->oDbCon->paramString($marketplacekey).'  and '.$this->tableShipmentDetails.'.marketplacelogisticid = ' . $this->oDbCon->paramString($rsLogistic[0]['logisticid']);
         
        //$this->setLog($sql,true);
        $rs = $this->oDbCon->doQuery($sql);
         
        return (empty($rs)) ? 0 : $rs[0]['refkey'];
    }
    
    // LOG
    function logPrintAirwayBill($response){
        return array('issuccess' => false, 'message' => '');
    }
    
    function logUpdateProduct($response){
        return array('issuccess' => false, 'message' => ''); 
    }
    
    function logUpdateProductQOH($response){
        return array('issuccess' => false, 'message' => ''); 
    }
    
    function logImportOrders($response){
        return array('issuccess' => false, 'message' => '');
    }
        
    function updateRequestPickupStatus($pkey){
        try{			  
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			 
				$sql  = 'update '.$this->tableSalesOrder.' set mprequestpickup = mprequestpickup  + 1 where pkey = ' .$this->oDbCon->paramString($pkey) ; 
            
                $this->oDbCon->execute($sql); 
            
				$this->oDbCon->endTrans(); 
            
			}catch(Exception $e){
				$this->oDbCon->rollback(); 
		}			
			
    }
    
    function requestPickUp($rsSalesOrder){ 
    // $this->setLog("-- pickup",true); 
    }
    
    function getItemInformationForMarketplace($itemkey){ 
		
		$sql = 'select * from '. $this->tableSyncedItem.' 
				where 
					refkey = ' . $this->oDbCon->paramString($itemkey) .' and 
					marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceKey); 
		
        $rs = $this->oDbCon->doQuery($sql); 
		return $rs; 
    }
    
    
    function getSalesAR($arrTransId){
        $salesOrder = new SalesOrder();
        $ar = new AR();
          
        $rsKey = $salesOrder->getTableKeyAndObj($salesOrder->tableName,array('key'));
        $sql = 'select 
                    '.$ar->tableName.'.pkey,
                    '.$ar->tableName.'.outstanding,
                    '.$salesOrder->tableName.'.refcode 
                from
                    '.$salesOrder->tableName.',
                    '.$ar->tableName.' 
                where 
                    '.$ar->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsKey['key']).' and
                    '.$ar->tableName.'.refkey = '.$salesOrder->tableName.'.pkey and
                    '.$ar->tableName.'.statuskey = 1 and
                    '.$salesOrder->tableName.'.refcode in ('.$this->oDbCon->paramString($arrTransId,',').') and
                    '.$salesOrder->tableName.'.marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey).' 
                ';
         
        $rsAR = $this->oDbCon->doQuery($sql);
        
        return $rsAR; 
    }
    
    function createARPayment($arrTransactionId){
        
        $warehouse = new Warehouse();
        $customCode = new CustomCode();
        //$paymentMethod = new PaymentMethod();
         
        $warehousekey = $warehouse->getDefaultData(); 
        
        // cari di AR, AR mana yg refcode sales ordernya sama
        
        $rsAR = $this->getSalesAR(array_column($arrTransactionId,'transId')); 
        $paymentAmountCol = $this->reindexDetailCollections($arrTransactionId,'transId');
        
        $overpaidNotes = array();
         
        // buat payment
        if(!empty($rsAR)){
            $arPayment = new ARPayment();
            
            $customerkey = $this->rsMarketplace['customerkey']; 
  
            // sementara,nanti bisa dipisah otomatis per tgl 
            $paymentDate = $paymentAmountCol[$rsAR[0]['refcode']][0]['trdate'];
                
            $arrParam = array();
            
            $arrParam['code'] = 'xxxxx';
            $arrParam['trDate'] = date('d / m / Y',strtotime($paymentDate)); 
            $arrParam['hidCustomerKey'] = $customerkey;
            $arrParam['selWarehouseKey'] = $warehousekey; 
            $arrParam['currencyRate'] = 1;  
            $arrParam['trDesc'] = '';
            
            // detail  
            $arrParam['hidDetailKey'] = array();
            $arrParam['hidARKey'] = array();
            $arrParam['outstanding'] = array();
            $arrParam['discount'] = array();
            $arrParam['amount'] = array();
            $arrParam['chkPick'] = array();
            
            $totalPayment = 0;
            
            foreach($rsAR as $arRow){
                //$this->setLog($arRow['refcode'],true);
                
                $arrAmount = $paymentAmountCol[$arRow['refcode']];
                //$this->setLog($arrAmount,true);
                //$this->setLog('>>>>>>>>>>>>>>>>>',true);
                
                $outstanding = $arRow['outstanding'];
                 
                
                $settlementAmount = 0;
                foreach($arrAmount as $amountRow){  
                    $settlementAmount += $amountRow['amount'];
                    //$this->setLog($amountRow['amount'] . ' => ' . $settlementAmount,true);
                }
                $totalPayment += $settlementAmount;
                
                //$this->setLog('=================',true);
                
                if($settlementAmount <= 0) continue;
                
                // kalo kelebihan bayar, harusnya dipisahkan jd pendapatan lain2 
                 if($settlementAmount > $outstanding){
                    array_push($overpaidNotes, $arRow['refcode'].'= '.$this->formatNumber($settlementAmount-$outstanding));
                    $settlementAmount = $outstanding;
                 } 
                
                $discount =  $outstanding - $settlementAmount;
                
                array_push($arrParam['hidDetailKey'],0);   
                array_push($arrParam['hidARKey'],$arRow['pkey']); 
                array_push($arrParam['outstanding'],$outstanding);
                array_push($arrParam['discount'],$discount);
                array_push($arrParam['amount'],$settlementAmount);
                array_push($arrParam['chkPick'],1);
                 
            }
            
            $arrParam['trDesc'] = (!empty($overpaidNotes)) ? $this->lang['overPaid'].chr(13).implode(chr(13),$overpaidNotes) : '';
             
            // payment  
            $arrParam['hidDetailPaymentKey'] = array();
            $arrParam['paymentMethodValue'] = array();
            $arrParam['selPaymentMethod'] = array();
            
            //get defaultPayment for marketplace
            /*$rsPaymentMethod = $paymentMethod->searchDataRow(array($paymentMethod->tableName.'.pkey'),
                                            ' and '.$paymentMethod->tableName.'.statuskey = 1 ',
                                            ' order by  '.$paymentMethod->tableName.'.marketplacedefaultpayment desc limit 1'
                                         );*/
             
            array_push($arrParam['hidDetailPaymentKey'],0);   
            array_push($arrParam['paymentMethodValue'],$totalPayment);   
            //array_push($arrParam['selPaymentMethod'],$rsPaymentMethod[0]['pkey']);   
            array_push($arrParam['selPaymentMethod'],$this->ARPaymentMethodKey);   
             
                 
            $result = $arPayment->addData($arrParam);  
            //$this->setLog($result,true,'AR'); 
            $res = $arPayment->changeStatus($result[0]['data']['pkey'],2, '',false,true);  
            //$this->setLog($res,true,'AR');
        
            return $result;
        }
    }
    
    function updateStorefront($arrStorefrontKey){ }
    function deleteStorefront($storefrontKey){ }
    
//    function updateTokenInAllMarketplace(){
//		 
//        $this->updateTokenTokopedia();
//        $this->updateTokenShopee();
//		
//        // harus ad code soalnya 
//        //$this->updateTokenLazada();
//    }
    
    function getAccessTokenTokopedia($appKey,$secretKey){
     
        $url = 'https://accounts.tokopedia.com/token?grant_type=client_credentials';
        $token = base64_encode($appKey.':'.$secretKey);
        
        $header = array(
            'Content-Type: application/json', 
            'Content-Length: 0',
            'Authorization: Basic '.$token
        );


        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($connection, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

        $response = curl_exec($connection);
          
        curl_close($connection);
        
        $response = json_decode($response,true);
        
        return  $response['access_token'];
    }
    
    function updateTokenTokopedia(){  
            
            // loop semua account tokped  
            $dbCon = $this->masterConn();  
        
            try{ 

                if(!$dbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);

                $sql = 'select pkey,appkey,secretkey from '.$this->tableMarketplaceProviderAccount.' where refkey = '.$this->oDbCon->paramString(MARKETPLACE['tokopedia']);    
                $rs = $dbCon->doQuery($sql);	  
                
                //$this->setLog($rs,true);
                
                foreach($rs as $accountRow){
//                    echo $accountRow['appkey'].':'.$accountRow['secretkey'].'<bR><br>';
                    
                    $newToken = $this->getAccessTokenTokopedia($accountRow['appkey'], $accountRow['secretkey'] );
                    
                    if(empty($newToken)) continue;
                
                    $sql = 'update  
                                '.$this->tableMarketplaceProviderAccount .' 
                            set 
                                token = '.$this->oDbCon->paramString($newToken).',
                                lasttokenupdated = now()
                            where 
                                '.$this->tableMarketplaceProviderAccount .'.pkey = ' . $this->oDbCon->paramString($accountRow['pkey']); 

                    //$this->setLog($sql,true);
                    $dbCon->execute($sql);
                    
                    // reupdate
                    $this->accessToken = $newToken;
                }
               
                $dbCon->endTrans();  

            } catch(Exception $e){
                $dbCon->rollback(); 
            }		
             
            $dbCon = null; 
          
    }
    
    function updateTokenLazada($code){
        $code = strval($code);
        
        // loop semua account tokped  
        $dbCon = $this->masterConn();  
  
        $sql = 'select pkey,appkey,secretkey from '.$this->tableMarketplaceProviderAccount.' where refkey = '.$this->oDbCon->paramString(MARKETPLACE['lazada']);    
        $rs = $dbCon->doQuery($sql);	  
		
		//$this->setLog($rs,true);
		
        if (empty($rs)) return;

        // get token
        $client = new LazopClient('https://auth.lazada.com/rest',$rs[0]['appkey'],$rs[0]['secretkey']);
        $request = new LazopRequest('/auth/token/create');
        $request->addApiParam('code',$code);  
        $response =  $client->execute($request); 
        $response = json_decode($response,true); 
		
		//$this->setLog($response,true);
		
        $newToken = $response['access_token'];
        
        $dbCon = null; 

        try{		
		 	
			if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
            if(!empty($newToken)){
                $sql = 'update '.$this->tableName.' set accesstoken = ' .$this->oDbCon->paramString($newToken).' where pkey = ' . $this->marketplaceKey;
                $this->oDbCon->execute($sql); 
            }
            
			$this->oDbCon->endTrans();  
	 
		}catch(Exception $e){
			$this->oDbCon->rollback(); 
		}	 
 
    }
    
	function updateTokenShopee(){
		$rs = $this->searchDataRow(array($this->tableName.'.pkey'), ' and statuskey = 1 and '.$this->tableName.'.refmarketplacekey = '.$this->oDbCon->paramString(MARKETPLACE['shopee']));
		foreach($rs as $row){ 
			$shopee = new Shopee($row['pkey']);
			$shopee->updateRefreshToken(true);	
		}
	}
	
    function getInvoice($arrInvoice){  }
    
    function addSalesOrderById($arrId){
        
    }
    
    function syncMarketplaceCategoryVariant(){
        
    }
     
    function updateAWB($pkey,$awb){}
    
    function getMarketplaceCategoryVariant($marketplacecategorykey, $parentkey = '', $criteria = ''){ }
    
       
    function searchSyncedItem($itemkey = ''){
        
        if(!isset($this->marketplaceKey) || empty($this->marketplaceKey))  return;
            
        $sql = 'select pkey,refkey from '.$this->tableSyncedItem.' 
				where issync = 1 and  marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceKey);
            
        if(!empty($itemkey))
            $sql .= ' and refkey in ('.$this->oDbCon->paramString($itemkey,',').')';
              
        $rs = $this->oDbCon->doQuery($sql);	
        
        return $rs;
    }
    
    function removeUnsyncItem($arrItemsInformation){
        //test
      
        // index harus itemkey
        /*$this->setLog('item info',true,'sync');
        $this->setLog($arrItemsInformation,true,'sync');*/
        
        $arrItemKeys = array_keys($arrItemsInformation);
         
        // remove not synced item
        $rsItemSynced = $this->searchSyncedItem($arrItemKeys);
        $arrItemSyncedKey = array_column($rsItemSynced,'refkey');
            
        $newArr = array();
        
        foreach($arrItemsInformation as $itemkey => $qohRow){
            // kalo gk sync, di hapus saja
            if (in_array($itemkey, $arrItemSyncedKey))
                $newArr[$itemkey] = $qohRow;
        }
        
        /*$this->setLog('new array',true,'sync');
        $this->setLog($newArr,true,'sync');*/
         
        return $newArr;
    }
    
	function boostItem($arrItemKey = array()){}
	
	function getAvailableMarketplaceLogisticsForItem(){ return array(); }
	
	function updateAuthShopee($shopId,$code){
		 // gk bisa dari $this->shopId karena baliknya ke wintera.co.id, gk tau toko yg mana
		
		  try{

				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
 
				$sql = 'update '.$this->tableName.' set  refcode = '.$this->oDbCon->paramString($code).'  where shopid = '.$this->oDbCon->paramString($shopId); 
				$this->oDbCon->execute($sql);
 
				$this->oDbCon->endTrans();
			}catch(Exception $e){
				$this->oDbCon->rollback(); 
			}	
		
		// update refresh token langsung
		// harus buat object
		
		$rs = $this->searchDataRow(array($this->tableName.'.pkey'), ' and '.$this->tableName.'.shopid = '.$this->oDbCon->paramString($shopId));
		$shopee = new Shopee($rs[0]['pkey']);
		$shopee->updateRefreshToken();

	}
	
	function addToBackgroundJob($url,$method, $payload = array(),$arrLog=array()){
	    
	     try{

                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);

             
        		$payload =  json_encode($payload);
        		$arrLog =  json_encode($arrLog);
        

        		$sql = 'insert into '.$this->marketplaceBackgroundJob.' (marketplaceproviderkey, url,method,payload,transactionlog,statuskey,createdon)
        				values (
        						'.$this->oDbCon->paramString($this->marketplaceProviderKey).',
        					    \''.addslashes($url).'\',
        						'.$this->oDbCon->paramString($method).',
        					    \''.addslashes($payload).'\',
        					    \''.addslashes($arrLog).'\',
        						1,
        						now()
        				)' ;
        		
        // 		$this->setLog($sql,true);
        		$this->oDbCon->execute($sql);


                $this->oDbCon->endTrans();
            }catch(Exception $e){
                $this->oDbCon->rollback(); 
            }	

 
	}
	
	function executeBackgroundJob(){
		/*
			status
				1: waiting
				2: processing
				3: done
		*/
		// cari dulu masih ad status yg 2 gk, 
		
		$sql = 'select pkey from '.$this->marketplaceBackgroundJob.' where statuskey = 2' ;
		$rs = $this->oDbCon->doQuery($sql); 
		if (!empty($rs)) return;
		
		
		// tarik semua yg status 1 utk diproses
		// tp hati2 kalo kebykan bisa timeout
		// coba set limit per 10 dulu
		
		$limit = 15;
		
		$sql = 'select * from '.$this->marketplaceBackgroundJob.' where statuskey = 1 order by createdon asc limit '. $limit ;
		$rs = $this->oDbCon->doQuery($sql); 
	 
		if (empty($rs)) return;
	
		// update status dulu
		try{

			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
 
				$sql = 'update '.$this->marketplaceBackgroundJob.' set statuskey = 2, startedon=now() where pkey in ('.$this->oDbCon->paramString( array_column($rs,'pkey') ,',').')';
				$this->oDbCon->execute($sql);  
			
				$this->oDbCon->endTrans();
			}catch(Exception $e){
				$this->oDbCon->rollback(); 
			}
		
		// group by marketplace dulu biar gk select terus
		$rs =  $this->reindexDetailCollections($rs,'marketplaceproviderkey');
	 
		// execute 
		foreach($rs as $key=>$marketplaceRow){
			$marketplaceObj = $this->getMarketplaceObj($key)[0]['obj'];

			foreach($marketplaceRow as $row){  
				
				$response = $marketplaceObj->execute($row['url'],
										 $row['method'],
										 json_decode($row['payload'],true), 
										 json_decode($row['arrLog'],true), 
										 array('backgroundkey' => $row['pkey'])
										);
				
				// update status, dikeluarkan dari function execute agar open trans nya gk kelamaan
				
				try{

					if(!$this->oDbCon->startTrans())
						throw new Exception($this->errorMsg[100]);
 
					// update hasil response 
					$sql = 'update '.$this->marketplaceBackgroundJob.' set statuskey = 3, finishedon=now(), responsemsg = \'' . addslashes($response) .'\' where pkey = ' . $this->oDbCon->paramString($row['pkey']);
					$this->oDbCon->execute($sql);


					$this->oDbCon->endTrans();
				}catch(Exception $e){
					$this->oDbCon->rollback(); 
				}


			}

		}
 
    	
    	// siapa tau nanti kepake kedepannya
    	return true;
	}
	
}

class Shopee extends Marketplace{
    
    function __construct($marketplaceKey = ''){
		
		parent::__construct();
		   
        // default value, overwrite if needed
  		
		$this->apiPathVersion = '/api/v2/'; // harus pake slash didepannya
	 	$this->url  = 'https://partner.shopeemobile.com'.$this->apiPathVersion;  
        $this->callbackURL = '/shopee-callback';
 
		// sementara pake app pertama dulu
	    $selectedApp = 0; 
        $this->appKey  = SHOPEE_CONFIG[$selectedApp]['appKey'];
        $this->secretKey  = SHOPEE_CONFIG[$selectedApp]['secretKey'];  
		
		
	   	$this->marketplaceProviderKey = MARKETPLACE['shopee'];
		
        $this->initMarketplace(	$this->marketplaceProviderKey,$marketplaceKey);
         
	}
 
    function importOrders(){ 
    	// shopee
		
		$url = 'order/get_order_list';
		
		$item = new Item();
        $customer = new Customer();
        $salesOrder = new SalesOrder();
        $warehouse = new Warehouse();
        $customCode = new CustomCode();
        $shipment = new Shipment();
        
        // cek custom code
        $rsKey = $salesOrder->getTableKeyAndObj($salesOrder->tableName,array('key'));
        $rsCustomCode = $customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'],true,' and '.$customCode->tableName.'.statuskey = 1');
        $customCodeKey = (empty($rsCustomCode)) ? 0 : $rsCustomCode[0]['pkey'];
        
        $yesterday =  date('U',strtotime($this->backdateInterval)); 
        $today =  date('U'); 
        $warehousekey = $warehouse->getDefaultData();
        
        $customerkey = $this->rsMarketplace['customerkey'];
        $rsCustomer = $customer->getDataRowById($customerkey);
        $topkey = $rsCustomer[0]['termofpaymentkey']; 
        $saleskey =  $rsCustomer[0]['saleskey'];
         
        $orderPerPage = 100;
        $offset = 0;
        $nextPage = true;
        $odersColl = array();
        
        do{
            
            $payload = $this->createJsonBodyV2($url,array(
				'time_range_field' => 'create_time',
                'time_from' =>  intval($yesterday),
                'time_to' => intval($today),
                'page_size' => $orderPerPage,
                'cursor' => $offset,
                'order_status' => 'READY_TO_SHIP'
            ));

 
            $response = $this->execute($url,'GET', $payload);
            $response = json_decode($response,true); 
            $response = $response['response'];
			
            if(isset($response['order_list']) && !empty($response['order_list']))
                $odersColl = array_merge($odersColl,$response['order_list']);
            
            $offset = $response['next_cursor'];
            
            if (!isset($response['more']) || !$response['more'])   $nextPage = false; 
            
        }while(	$nextPage);
        
        
        if(empty($odersColl)) return;
        
        //if(!isset($odersColl['orders']) || empty($odersColl['orders'])) return;
        //$this->setLog($odersColl);
         
        $arrOrderQueue = array();
        
        // collect all order sn, utk tau sales mana saja yg sudah masuk ke SO
        $ordersnList = array_column($odersColl,'order_sn');
        $rsSalesCol = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey',$salesOrder->tableName.'.refcode'),
                                              ' and '.$salesOrder->tableName.'.refcode in ('.$salesOrder->oDbCon->paramString($ordersnList,',').') 
                                                and '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey)
                                             );
        
        $rsSales = array_column($rsSalesCol,null,'refcode'); 
            
        // pecah per 50 baris 
        $ordersnList = array_chunk($ordersnList, 50);
        
        // get each order
		$url = 'order/get_order_detail';
		
        $responseOpt = array("buyer_user_id","recipient_address","item_list","shipping_carrier");
        
        foreach($ordersnList as $orderIds){
            
            
           //$payload = $this->createJsonBodyV2($url, array('order_sn_list' => $orderIds)); 
            $payload = $this->createJsonBodyV2($url, array('order_sn_list' => implode(',',$orderIds),
                                                           'response_optional_fields' =>implode(',', $responseOpt)
            )); 
            
            
            $orders = $this->execute($url,'GET', $payload); 
            $orders = json_decode($orders,true); 
			$orders = $orders['response'];
			
            foreach($orders['order_list'] as $order){
                $orderId = $order['order_sn'];

                // transaksi sudah ada  
                if (isset($rsSales[$orderId])) { 

                    // update logistic yg blm keupdate
                    if(empty($rsSales[$orderId]['shipmentservicekey'])){
                         try{

                                if(!$this->oDbCon->startTrans())
                                    throw new Exception($this->errorMsg[100]);
 
                                $shipmentkey = $this->getShipmentDetailByName($order['shipping_carrier']);

                                $rsService = $shipment->getServices('',$shipmentkey);
                                $sql = 'update '.$this->tableSalesOrder.' 
                                        set 
                                            shipmentkey = '.$this->oDbCon->paramString($rsService[0]['refkey']).', 
                                            shipmentservicekey =  '.$this->oDbCon->paramString($shipmentkey).'
                                        where pkey = '.$this->oDbCon->paramString($rsSales[$orderId]['pkey']);
 
                                $this->oDbCon->execute($sql);


                                $this->oDbCon->endTrans();
                            }catch(Exception $e){
                                $this->oDbCon->rollback(); 
                            }	
 
                    }
                    
                    continue;
                }
                
                //$this->setLog($order,true,'shopee-logistics');
                //$this->setLog($order['shipping_carrier'],true);
                
                $shipmentkey = $this->getShipmentDetailByName($order['shipping_carrier']);

                $orderDate =  date("d / m / Y H:i", $order['create_time']);   
                $recipientName = $order['recipient_address']['name'];
                $recipientAddress = $order['recipient_address']['full_address'];
                $recipientPhone = $order['recipient_address']['phone'];
                $trDesc = $order['message_to_seller'];

                // details
                $orderDetails = $order['item_list'];

                // =============  compile item
                // gk bisa selalu ambil dari item_sku
                // yg perlu dicek, kalo gk ad model_sku, apakah nilai defaultnya tetep sama dengan item_sku
                
                // $arrItemCode = array_column($orderDetails,'item_sku');
        
                $arrItemCode = array();
                foreach($orderDetails as $detailOrderRow){
                    $soldSKU = (!empty($detailOrderRow['model_sku'])) ? $detailOrderRow['model_sku'] : $detailOrderRow['item_sku'];
                    array_push($arrItemCode, $soldSKU);
                }

                $rsItemColl = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.code',$item->tableName.'.baseunitkey'),
                                                   ' and ('.$item->tableName.'.code in ('.$item->oDbCon->paramString($arrItemCode,',').'))'
                                                  );
                $itemColl = array_column($rsItemColl,null,'code');
                // =============  compile item

                // PREPARE ARRAY  
                $arrParam = array();
                $arrParam['code'] = 'xxxxxx';
                $arrParam['trDate'] = $orderDate;
                $arrParam['selWarehouseKey'] = $warehousekey;
                $arrParam['hidCustomerKey'] = $customerkey;
                $arrParam['selStatus'] = 1;
                $arrParam['selTermOfPaymentKey'] = $topkey; 
                $arrParam['trDesc'] = $trDesc;
                $arrParam['chkIsFullDeliver'] = 1; 
                $arrParam['selFinalDiscountType'] = 1;
                $arrParam['finalDiscount'] = 0 ; // akan diupdate ulang dibawah
                $arrParam['marketplaceKey'] = $this->marketplaceKey;
                $arrParam['refCode'] = $orderId;
                $arrParam['selCustomCode'] = $customCodeKey;
                $arrParam['selShipmentService'] = $shipmentkey;
                $arrParam['hidSalesKey'] = $saleskey;

                $arrParam['recipientName'] = $recipientName;
                $arrParam['recipientPhone'] = $recipientPhone; 
                $arrParam['recipientAddress'] = $recipientAddress;
                

                $arrItemKey = array();
                $warningNotifications = array(); 

                $arrParam['hidDetailKey'] = array();
                $arrParam['refMarketplaceKey'] = array();
                $arrParam['hidItemKey'] = array();
                $arrParam['selUnit'] = array();
                $arrParam['priceInUnit'] = array();
                $arrParam['priceInBaseUnit'] = array();
                $arrParam['unitConvMultiplier'] = array();
                $arrParam['qty'] = array();
                $arrParam['qtyInBaseUnit'] = array();

                $totalOrderDetails = count($orderDetails);

                $subtotal = 0;
                for($i=0;$i<$totalOrderDetails;$i++){  

                    //$indexCode = $orderDetails[$i]['item_sku'];
                    
                    $soldSKU = (!empty($orderDetails[$i]['model_sku'])) ? $orderDetails[$i]['model_sku'] : $orderDetails[$i]['item_sku'];
                  
                    $indexCode = strval(trim($soldSKU));

                    if(!isset($itemColl[$indexCode]['pkey'])) {
                        array_push($warningNotifications, $indexCode. '. '. $salesOrder->errorMsg[213]);
                        continue;
                    }

                    $itemkey = $itemColl[$indexCode]['pkey'];
                    $baseunitkey = $itemColl[$indexCode]['baseunitkey'];

                    if (in_array($itemkey, $arrItemKey)){
                          for($j=0;$j<$i;$j++){ 
                              if ($arrParam['hidItemKey'][$j] == $itemkey){ 
                                  $arrParam['qty'][$j]++;
                                  break;
                              }
                          }

                        continue;
                    }

                    $priceInUnit = intval($orderDetails[$i]['model_discounted_price']);
                    $qty = intval($orderDetails[$i]['model_quantity_purchased']);
                    
                    array_push($arrItemKey,$itemkey); 
                    array_push($arrParam['hidDetailKey'], 0); 
                    array_push($arrParam['hidItemKey'], $itemkey);
                    array_push($arrParam['selUnit'], $baseunitkey);
                    array_push($arrParam['priceInUnit'], $priceInUnit);
                    array_push($arrParam['priceInBaseUnit'], $priceInUnit);
                    array_push($arrParam['unitConvMultiplier'], 1);
                    array_push($arrParam['qty'],$qty ); 
                    array_push($arrParam['qtyInBaseUnit'], $qty);

                    $subtotal += ($qty * $priceInUnit);
                } 
                
                // ASUMSI :
                // update total promo kalo jml yg dierima selisi dengan total penjualan
                // baik dr biaya admin, voucher , dsb
                
                if(!empty($warningNotifications)){ 
                    $arrParam['_hasWarning_'] = true;

                    if (!empty($arrParam['trDesc'])) $arrParam['trDesc'] .= chr(13);
                    $arrParam['trDesc'] .= implode(chr(13),$warningNotifications);
                } 
                array_push($arrOrderQueue,$arrParam);

            }
            
        } 
            
        // keluarin dulu yg sudah ada sales ordernya, agar mengurangi kerjaan validateForm
        // harusya sih gk mungkin ad karena sdh difilter diatas, utk MEMASTIKAN SAJA lg, ini model awal 
        // atau mungkin kalo ketarik 2x? karena delay
        $arrRefCode = array_column($arrOrderQueue,'refCode');
        $rsSales = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey',$salesOrder->tableName.'.refcode'),
                                              ' and '.$salesOrder->tableName.'.refcode in ('.$salesOrder->oDbCon->paramString($arrRefCode,',').') 
                                                and '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey)
                                             );

        //$rsSales = $salesOrder->searchData('','',true, ' and '.$salesOrder->tableName.'.refcode in ('.$salesOrder->oDbCon->paramString($arrRefCode,',').') and  '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey));
        $rsSales =  array_column($rsSales,'refcode');
            
        foreach($arrOrderQueue as $arrParam){
             
            if(in_array($arrParam['refCode'],$rsSales)) continue; 
            
            if (!isset($arrParam['hidItemKey']) || empty($arrParam['hidItemKey'])) continue;
      
            try{
	
                if(!$this->oDbCon->startTrans(true))
                    throw new Exception($this->errorMsg[100]);

                  $arrayToJs = $salesOrder->addData($arrParam); 

                  if(!$arrayToJs[0]['valid'])
                    throw new Exception( $arrayToJs[0]['message'] );
 
                  $this->oDbCon->endTrans();
            }catch(Exception $e){
                $this->oDbCon->rollback();
                $this->addErrorList($arrayToJs,false,$e->getMessage());
            }	
 
            
        }
        
        
        //$this->setLog("end import shopee " . date('d / m / Y H:i:s'),true,'mp');
    } 
     
    function closeCompletedOrders(){ 
        
        // shopee
        // sudah optimal, sekali ambil 50 transaksi dr shopee yg statusnya selesai
        
        $completedStatus = array('shipped','to_confirm_receive', 'completed');
         
        $salesOrder = new SalesOrder(); 
        
        $ordersnList = array();
        $rsSalesOrder = $salesOrder->searchDataRow(array($salesOrder->tableName.'.pkey', $salesOrder->tableName.'.refcode'),
                                                    ' and refcode <> "" and '.$salesOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].') 
                                                      and '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey) 
                                                  );
         
        if(empty($rsSalesOrder)) return;
         
        $rsSalesOrderCol = array_column($rsSalesOrder,'pkey', 'refcode'); 
        
        foreach($rsSalesOrder as $rs)
            array_push($ordersnList, $rs['refcode']);
         
        
        // pecah per 50 baris 
        $ordersnList = array_chunk($ordersnList, 50);
         
        // get each order
        // $this->setLog('close order',true,'sh.txt');
        
		$url = 'order/get_order_detail';
        foreach($ordersnList as $snRow){
            // $payload = $this->createJsonBodyV2($url,array( 'order_sn_list' =>  $snRow ));  
           $payload = $this->createJsonBodyV2($url,array( 'order_sn_list' =>  implode(',',$snRow) ));  
             
            $ordersHeader = $this->execute($url,'GET', $payload);
            $ordersHeader = json_decode($ordersHeader,true);   
			$ordersHeader = $ordersHeader['response'];
				
            $ordersHeader = (isset ($ordersHeader['order_list'])) ? $ordersHeader['order_list'] : array();

            foreach($ordersHeader as $row){  
                if(!in_array(strtolower($row['order_status']),$completedStatus)) continue;  
                $salesOrder->changeStatus($rsSalesOrderCol[$row['order_sn']], TRANSACTION_STATUS['selesai'], '',false, true); 
            }    
        }
      
          
    } 
     
    function cancelCanceledOrders(){ 
        
        // shopee
        // sudah optimal, sekali ambil 50 transaksi dr shopee yg statusnya selesai
        
        $completedStatus = array('cancelled');
         
        $salesOrder = new SalesOrder(); 
        
        $ordersnList = array();
        //$rsSalesOrder = $salesOrder->searchData($salesOrder->tableName.'.marketplacekey', $this->marketplaceKey,true, ' and refcode <> "" and '.$salesOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].') ' );
        $rsSalesOrder = $salesOrder->searchDataRow(array($salesOrder->tableName.'.pkey', $salesOrder->tableName.'.refcode'),
                                            ' and refcode <> "" and '.$salesOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].')  
                                              and '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey) 
                                          );

        if(empty($rsSalesOrder)) return;
         
        $rsSalesOrderCol = array_column($rsSalesOrder,'pkey', 'refcode');
        
        foreach($rsSalesOrder as $rs)
            array_push($ordersnList, $rs['refcode']);
         
        
        // pecah per 50 baris 
        $ordersnList = array_chunk($ordersnList, 50);
         
        // get each order
        // $this->setLog('cancel order',true,'sh.txt');
        
		$url = 'order/get_order_detail';
        foreach($ordersnList as $snRow){
            // $payload = $this->createJsonBodyV2($url,array( 'order_sn_list' =>  $snRow ));   
            $payload = $this->createJsonBodyV2($url,array( 'order_sn_list' =>  implode(',',$snRow) ));   
             
            // $this->setLog($payload,true,'sh.txt');
            
            $ordersHeader = $this->execute($url,'GET', $payload);
            $ordersHeader = json_decode($ordersHeader,true);   
			$ordersHeader = $ordersHeader['response'];
			
            $ordersHeader = (isset ($ordersHeader['order_list'])) ? $ordersHeader['order_list'] : array();

            foreach($ordersHeader as $row){  
                if(!in_array(strtolower($row['order_status']),$completedStatus)) continue; 
                
                $salesOrder->changeStatus($rsSalesOrderCol[$row['order_sn']], TRANSACTION_STATUS['batal'], '',false, true);
            }    
        }
       
    }
    
	
    function updateProductVariant($arrItemInformation,$syncCriteria = array()){ 
        
	    // shopee 
		$item = new Item();
		
		$itemkey = $arrItemInformation['itemkey'];
		$itemcode = $arrItemInformation['itemcode'];
		$marketplaceitemkey = $arrItemInformation['marketplaceitemkey'];
		$parentkey = $arrItemInformation['parentkey'];
		$warehousekey = $arrItemInformation['warehousekey'];
			
		if(empty($parentkey)){ 
			$rsItemParent = $item->getDataRowById($itemkey);
			$parentkey = $rsItemParent[0]['parentkey'];
		}
        
       // cari model list di online yg skrg, buat ambil id imagenya jg biar gk perlu upload ulang
        $onlineModelList = $this->getModelList($parentkey);
        $arrVariantList = $onlineModelList[0]['tier_variation']; 
        
        //convert ke format yg lebih rapi, key berdasarkan tier_index
        // klo edit, pake model_id
        $existingModelList = $this->convertModelList($onlineModelList);
        $existingModelListByModelId = array_column($existingModelList['model'],null,'model_id');
        
        $arrExistingVariantModelId = array_column($existingModelList['model'],'model_id'); 
          
		$rsItemParentLink = $this->searchLinkItem($parentkey); 
		$parentmarketplaceitemkey = $rsItemParentLink[0]['marketplaceitemkey'];

        $rsVariant = $item->getItemVariants($parentkey,$this->marketplaceProviderKey);
        $arrTierAndModel = $this->createTierAndModel($rsVariant,null,$arrVariantList,$itemkey);   // nanti perlu diupdte khusus item ini, imge harus update ulang 
       
         
        // sementara update dulu yg existing berdasarkan SKU 
        
        $arrTierOptList = $arrTierAndModel['tier_variation'][0]['option_list']; 
        $arrTierModelList = $arrTierAndModel['tier_item_index']; 
         
                   
        $rsItemLinkCol = $this->searchLinkItem(array_column($arrTierModelList,'pkey'));  
        $rsItemLinkCol = array_column($rsItemLinkCol,'marketplaceitemkey','refkey');
       
        $arrUpdateModel = array();    
        $arrNewVariant = array();
        foreach($arrTierOptList as $key=>$modelRow){
              
            $modelId = $rsItemLinkCol[$arrTierModelList[$key]['pkey']];
            
            if(in_array($modelId,$arrExistingVariantModelId)){ 
                $onlineTierIndex = $existingModelListByModelId[$modelId]['tier_index']; 
                $arrVariantList[0]['option_list'][$onlineTierIndex]['option'] = $modelRow['option'];
                
                array_push($arrUpdateModel, array('model_id' => intval($modelId), 'model_sku' => $arrTierModelList[$key]['code'] )); 
            }else{
                array_push($arrNewVariant,$arrTierModelList[$key]); 
            } 
        }
 
            $url = 'product/update_tier_variation';
            $payload = $this->createJsonBodyV2($url,array( 
                        'item_id' => intval($parentmarketplaceitemkey), 
                        'tier_variation' => $arrVariantList
                        )   
            );
            
            $this->execute($url, 'POST', $payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Varian', 'ref' => $name ));
 
            // update SKU, jaga2 kalo ketuker
            // harusnya update semua model sku
            $url = 'product/update_model';  
            $payload = $this->createJsonBodyV2($url,array( 
                    'item_id' => intval($parentmarketplaceitemkey), 
                    'model' => $arrUpdateModel
                    )   
            ); 
            $this->execute($url, 'POST', $payload);
     
    		// update price
    		if (in_array('price', $syncCriteria['attr'])){ 
    
    			$itemPrice = $this->getItemPrice($itemkey); 
    
    			// hati2 kena banned
    			$sellingPrice = floatval($itemPrice['adjustedprice']);
    
    			$arrPrice = array(
    								'item_id' => intval($parentmarketplaceitemkey),
    								'price_list' => array(array('model_id' => intval($marketplaceitemkey),'original_price' => $sellingPrice)), 
    							  );
    
    			$url = 'product/update_price';
    			$payload = $this->createJsonBodyV2($url,$arrPrice); 
    
    			$result = $this->execute($url, 'POST', $payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Price', 'ref' => $name ));
    
    		}
    
    		// update qoh 
    		if (in_array('qoh', $syncCriteria['attr'])){ 
    			$itemMovement = new ItemMovement();
    			$qoh = $itemMovement->getItemQOH($itemkey,$warehousekey);   
    
                $stockPayload = array();
                array_push($stockPayload,array('stock' => intval($qoh)));
                
    			$url = 'product/update_stock';
    			$payload = $this->createJsonBodyV2($url,array(
    				'item_id' => intval($parentmarketplaceitemkey), 
    				'stock_list' => array(array('model_id' => intval($marketplaceitemkey),'seller_stock' => $stockPayload)) , 
    			)); 
    
    			$this->execute($url, 'POST', $payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Qty', 'ref' => $name ));
    
    		}

       
//		// update status 
//		if (in_array('status', $syncCriteria['attr'])){ 
//                   
//				   $url = 'product/unlist_item';
//                   $arrItem = array(); 
//					$payload = $this->createJsonBodyV2($url,array(
//                       	'item_list' => array(array( 
//											'item_id' => intval($marketplaceitemkey), 
//											'unlist' => ($rsItem['statuskey'] == 2) ? true : false
//                                    		)) 
//										)
//                    ); 
//              
//                  $this->execute($url, 'POST', $payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Status', 'ref' => $name ));
//                
//            }

            // create new variant
            // bisanya dari item yg sebelumnya bkn variant, ke model detail variant
            // harus dipanggil karena di item_marketplace_link sudah ad, jg selalu dianggap update product
             
            foreach($arrNewVariant as $rsItem)  
                $this->createProductVariant($rsItem); 
            
	}
	
    function updateProduct($itemkey,$rsItemColl = array(),$syncCriteria = array()){
       
            // shopee
            
            // $rsItemColl digunakan agar tdk query data item berulang2 
            $item = new Item();
			$brand = new Brand();
            
			$warehousekey = ''; // ini diudpate jika punya gudang khusus utk marketplace
         
            
            $arrParam = array();
            
            if(!empty($rsItemColl)){ 
                $rsItem = $rsItemColl[$itemkey];
            }else{ 
                $rsItem = $item->getDataRowById($itemkey); 
                $rsItem = $rsItem[0];
            }
         
            $code = $rsItem['code']; 
		
        	$arrItemMPInformation = $this->getItemInformationForMarketplace($itemkey)[0];
		
            $mpName = $arrItemMPInformation['name'];
            $name = (!empty($mpName)) ? $mpName : $rsItem['name']; // cek ke settingan ad overwrite nama gk
        
            $rsCategory = $this->getCategoryUsedForMarketplace($rsItem['categorykey']);  
            $categorykey = intval($rsCategory[0]['marketplacecategorykey']);  //7440;
        
            // cari itemkey di shopee
            $rsItemLink = $this->searchLinkItem($itemkey);
        
            //parameter need to be sync
            $marketplaceitemkey = $rsItemLink[0]['marketplaceitemkey'];
        
            // kalo variant, HANYA JIKA sebelumnya variant
            // kalo sebelumny bukan vriant, harus panggil createProductvariant 
            
            if($rsItem['isvariant'] ==1){
			 return $this->updateProductVariant(array('itemkey' => $itemkey, 'marketplaceitemkey' => $marketplaceitemkey, 'parentkey' => $rsItem['parentkey'],'warehousekey' => $warehousekey ),$syncCriteria);
            }
          
          
            $arrPayload = array(
                        'item_id' => intval($marketplaceitemkey),
                        'item_sku' => $code, 
                        'condition' => $this->getItemConditionForMarketplace($rsItem['conditionkey']),  
                        'category_id' => $categorykey,
            ); 
        
        
            // img
            $arrImgId = array();
            $rsItemImage = $item->getItemImage($itemkey);   
            foreach($rsItemImage as $imgRow){     
                
                // cek checksum masih sama tdk, kalo beda baru upload ulang
                           
               // $checksum = (md5($imgRow['file'].'.'.$imgRow['marketplaceimageid']) == $imgRow['checksum'] ) ? true : false;
                
               // if(!$checksum){  
                    $sourceFile = DEFAULT_DOC_UPLOAD_PATH.'item/'.$itemkey.'/'.$imgRow['file'];  
                    $imageId = $this->uploadToMediaSpace($sourceFile); 
                    if(!empty($imageId))
                        array_push($arrImgId,$imageId);
                        
                    // update checksum
                    $item->updateMarketplaceImageId($imgRow['pkey'],$imageId);
              //  }
            }
            
            // if(!empty($arrImgId))
            //     $arrPayload['image_id_list'] = $arrImgId;
             
            if(!empty($arrImgId))
                $arrPayload['image'] = array('image_id_list' => $arrImgId);
            
            
            $additionalPayload = array();
        
            // NAME attr
            if (in_array('name', $syncCriteria['attr']))
                $additionalPayload = array_merge($additionalPayload, array('item_name' => htmlspecialchars_decode($name))); // <- test utk " &quote;
	 
            // SHORT DESCRIPTION attr
            if (in_array('shortDescription', $syncCriteria['attr'])){  
        		 
				$mpDesc = $arrItemMPInformation['shortdescription'];
				$mpDesc = (!empty($mpDesc)) ? $mpDesc : $rsItem['shortdescription'];
         
				$additionalPayload = array_merge($additionalPayload, array('description' => html_entity_decode($mpDesc)));
			}
		
            // MEASUREMENT attr
            if (in_array('measurement', $syncCriteria['attr'])){ 
                $weight = $rsItem['gramasi'];
                if ($rsItem['weightunitkey'] == UNIT['gram'])
                    $weight /= 1000;
                
                $length = (!isset($rsItem['length']) || $rsItem['length'] <= 0 ) ? 1 : $rsItem['length'];
                $width = (!isset($rsItem['width']) || $rsItem['width'] <= 0 ) ? 1 : $rsItem['width'];
                $height = (!isset($rsItem['height']) || $rsItem['height'] <= 0 ) ? 1 : $rsItem['height']; 
                   
				$arrDimension = array( 'package_length' => intval($length),
									   'package_width' => intval($width),
									   'package_height' => intval($height) 
									 );
				
                $additionalPayload = array_merge($additionalPayload, array( 'weight' => floatval($weight),
																'dimension' => $arrDimension 
                                                            )); 
            }
          	
			//brand
			$rsBrand = $this->getBrandUsedForMarketplace($rsItem['brandkey'], array('marketplacecategorykey' => $categorykey));
			$additionalPayload = array_merge($additionalPayload, array( 'brand' => array('brand_id' => intval($rsBrand[0]['marketplacebrandkey'])))); 
			
            // OTHERS ATTRIBUTE
            if (in_array('others', $syncCriteria['attr'])) { 
                $arrAttributes = array(); 
        
                $rsAttributes = $item->getMarketplaceCategoryAttributes($itemkey, $this->marketplaceKey); 
				
                foreach($rsAttributes as $attributeRow){  
					if ($attributeRow['attributekey'] == 0) continue; // kalo brand diupate terpish di V2
					
					$attrList = array(); 
				 	array_push($attrList , array( 'value_id' => intval($attributeRow['valueid']), 'original_value_name' =>  htmlspecialchars_decode($attributeRow['value'])));  
                    // array_push($attrList , array( 'value_id' => 0, 'original_value_name' =>  htmlspecialchars_decode($attributeRow['value'])));  
                    array_push($arrAttributes, array('attribute_id' => intval($attributeRow['attributekey']), 'attribute_value_list' => $attrList));
				}
				
                if (!empty($arrAttributes))
                    $additionalPayload = array_merge($additionalPayload, array( 'attribute_list' => $arrAttributes )); 
            
            }
            
            //$this->setLog($additionalPayload,true,'attr-sh');
        
            // LOGISTICS
            // tetep harus ngambil dr shopee, kecuali sudah terintegrasi di sistem.
            // tp kalo terintegrasi resiko jg, kalo di shopee diupdate, tp disistem blm ke sync, akan gagal update productnya
            
            // yg dicentang di sistem
            $rsLogisticsDetail = $item->getMarketplaceLogistics($itemkey, $this->marketplaceKey); 
			$arrLogisticsKey = array_column($rsLogisticsDetail,'logisticid');
			
			// v2 
			$url = 'logistics/get_channel_list'; 
			$payload = $this->createJsonBodyV2($url);   
			$logisticsResponse = $this->execute($url, 'GET', $payload);
            $logisticsResponse = json_decode($logisticsResponse,true);
			$logisticsResponse = $logisticsResponse['response']['logistics_channel_list'];
		     
            $arrLogistics = array();
            foreach($logisticsResponse as $logisticRow) {
                 if(!$logisticRow['enabled']) continue; // kalo yg disable diproses, akan error
                  
                 $logisticid = ($logisticRow['mask_channel_id'] != 0) ? $logisticRow['mask_channel_id'] : $logisticRow['logistics_channel_id']; 
                 $status = (in_array($logisticid,$arrLogisticsKey)) ? true : false;
                    array_push($arrLogistics, array('logistic_id' => $logisticid, 'enabled' => $status));
            }
              
            $additionalPayload = array_merge($additionalPayload, array( 'logistic_info' => $arrLogistics ));
         
         
            if (!empty($additionalPayload))
                $arrPayload = array_merge($arrPayload, $additionalPayload);

			$url = 'product/update_item';
			$payload = $this->createJsonBodyV2($url, $arrPayload);

			// update 
			$result = $this->executeOnBackground($url,'POST',$payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Attribute',  'ref' => $name ) );    
// 			$this->execute($url,  'POST', $payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Attribute', 'ref' => $name ));
               

			// update etalase
			// kalo storefrontny kosong (gk pernah update), gk perlu 
			// nanti perlu diupdate ketika update stok, baru bisa masuk ke etalase
		
			$arrStorefrontKey = $this->getStorefrontUsedForMarketplace($rsItem['categorykey'],$rsItem['brandkey']);  
			foreach($arrStorefrontKey as $storefrontkey){
				
				$url = 'shop_category/add_item_list';
				
				$payload = $this->createJsonBodyV2($url,array(
					'shop_category_id' => intval($storefrontkey), 
					'item_list' => array(intval($marketplaceitemkey)),
				));   
				
		    	$result = $this->executeOnBackground($url,'POST',$payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Shop Category',  'ref' => $name ) );  
				// $result = $this->execute($url, 'POST', $payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Shop Category', 'ref' => $name )); 
 
			}
			
		 
            // update price
            if (in_array('price', $syncCriteria['attr'])){ 

				$itemPrice = $this->getItemPrice($rsItem['pkey']); 

				// hati2 kena banned
				$sellingPrice = floatval($itemPrice['adjustedprice']);

				$arrPrice = array(
								'item_id' => intval($marketplaceitemkey),
								'price_list' => array(array('original_price' => $sellingPrice)), 
							  ); 

				$url = 'product/update_price';
				$payload = $this->createJsonBodyV2($url,$arrPrice); 

		    	$result = $this->executeOnBackground($url,'POST',$payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Price',  'ref' => $name ) );  
				// $result = $this->execute($url, 'POST', $payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Price', 'ref' => $name ));
         
            }
            
            // update qoh 
            if (in_array('qoh', $syncCriteria['attr'])){  
            	  $itemMovement = new ItemMovement();  
                  $qoh = $itemMovement->getItemQOH($itemkey,$warehousekey);   
               
                    $stockPayload = array();
                    array_push($stockPayload,array('stock' => intval($qoh)));
                    
					$url = 'product/update_stock';
					$payload = $this->createJsonBodyV2($url,array(
						'item_id' => intval($marketplaceitemkey), 
					    'stock_list' => array(array('seller_stock' =>  $stockPayload )), 
				    )); 
             
		    	$result = $this->executeOnBackground($url,'POST',$payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Qty',  'ref' => $name ) );  
                //   $this->execute($url, 'POST', $payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Qty', 'ref' => $name ));
                
            }
            
            // update status 
            if (in_array('status', $syncCriteria['attr'])){ 
                   
				   $url = 'product/unlist_item';
                   $arrItem = array(); 
					$payload = $this->createJsonBodyV2($url,array(
                       	'item_list' => array(array( 
											'item_id' => intval($marketplaceitemkey), 
											'unlist' => ($rsItem['statuskey'] == 2) ? true : false
                                    		)) 
										)
                    ); 
              
		    	$result = $this->executeOnBackground($url,'POST',$payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Status',  'ref' => $name ) );  
                //   $this->execute($url, 'POST', $payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Status', 'ref' => $name ));
                
            }
        
          
    }
    
    function createTierAndModel($rsVariant,$rsQOH = array(), $existingModel = array(),$editPkey=''){
        $item = new Item();
                 
        $arrVariantList = array();
        $arrOptList = array(); 
        $arrItemVariations = array(); 
        $tierItemIndex = array();  

        $tierIndex = 0;  
        $variantName = ''; 
        
        $existingModelOpt = $existingModel[0]['option_list'];
        $existingModelOptNameMap = array();
        $existingModelOptName = array();
        
       
        if (!empty($existingModelOpt)){
            $existingModelNameMap = array_column($existingModelOpt,null,'option'); 
             
            $existingModelName =  array_column($existingModelOpt,'option');
            $existingModelName = array_map('strtolower', $existingModelName);
        } 
        
      
        foreach($rsVariant as $key=>$variantItem){  
 
            
            $arrVariant = $variantItem['marketplace_variant'][0]; 
            
            if($variantName == '' && $arrVariant['variantkey'] != '' ) $variantName = $arrVariant['variantkey'];
            
            $optValue = $arrVariant['optionvalue']; 
		 
            $variantItemKey = $variantItem['pkey'];
            
            if(empty($optValue)) continue;
		
		    $optValue = htmlspecialchars_decode($optValue); 
            $arrOptItem = array();
            $arrOptItem['sku'] = $variantItem['code']; // gk kepake dishopee. tp buat indexing internal aj utk updateProductVariant  
            $arrOptItem['option'] = $optValue; 
            
		    // add image utk variant baru saja
		    // yg lama ambil dr modellist
		     
		    // kalo edit, selalu ambil image terbaru
		    if($variantItemKey <> $editPkey && in_array(strtolower($optValue),$existingModelName ?? [])){  
		         $arrOptItem['image'] = array('image_id' => $existingModelNameMap[$optValue]['image']['image_id']) ;
		    }else{   
                $arrImgId = array();
                $rsItemImage = $item->getItemImage($variantItemKey);   
                foreach($rsItemImage as $imgRow){     
                    $sourceFile = DEFAULT_DOC_UPLOAD_PATH.'item/'.$variantItemKey.'/'.$imgRow['file']; 
                    $imgResponse = $this->uploadToMediaSpace($sourceFile);
        
                    if(!empty($imgResponse))
                        array_push($arrImgId,$imgResponse);
                }
                
                if(!empty($arrImgId)) $arrOptItem['image'] =  array('image_id' => $arrImgId[0]);
		    }
		     
                
            array_push($arrOptList, $arrOptItem);

            $itemVariationPayload =  array(
                                    'tier_index' => array($tierIndex), 
                                    'original_price' =>  intval($variantItem['sellingprice']) ,
                                    'model_sku' => $variantItem['code'],
                                );
                                 
            // kaalo perlu submit informasi stock
            if(!empty($rsQOH)){
                $qoh = $rsQOH[$variantItemKey]['qtyinbaseunit'];
                $stockPayload = array();
                array_push($stockPayload,array('stock' => intval($qoh)));

                $itemVariationPayload['seller_stock'] = $stockPayload;
            }
            
            $tierItemIndex[$tierIndex] = $variantItem; 
            array_push($arrItemVariations, $itemVariationPayload ); 
    
            $tierIndex++;

        }
         
        array_push($arrVariantList,
            array(
                'name' => $variantName,
                'option_list' => $arrOptList, 
            )
        ); 
 
        return array( 'tier_variation' => $arrVariantList,
                      'model' => $arrItemVariations,
                      'tier_item_index' => $tierItemIndex
                      ); 
    }
     
    function convertModelList($arr){ 
        
        $optList = $arr[0]['tier_variation'][0]['option_list'];
        $tempModelList = $arr[0]['model']; // urutan gk sama, harus di sort ulagn berdasarkan tier_index
        
        $modelList = array();
        foreach($tempModelList as $row) 
            $modelList[$row['tier_index'][0]] =$row;  // urutan gk sama, harus di sort ulagn berdasarkan tier_index
       
        // $onlineModel = array();
        $standardModel = array();
        foreach($optList as $key=>$row){
            // $onlineModel[$key]['model']= $modelList[$key];
            // $onlineModel[$key]['option']= $optList[$key]; 
            
            $standardModel[$key]['model_id']=$modelList[$key]['model_id'];
            $standardModel[$key]['tier_index']=$key;
            $standardModel[$key]['sku']=strtolower($modelList[$key]['model_sku']); // agar menghidnari case sensitive
            $standardModel[$key]['option']=$optList[$key]['option'];
            $standardModel[$key]['image']=$optList[$key]['image'];
        }
 
        return array('model' => $standardModel);
    }
    
    function createProductVariant($rsItem){  
        
        // shopee
		 
        // kalo update variant tetep perlu urut semua variantnnya,
         
        $itemMovement = new ItemMovement();
        $item = new Item();
        
        $itemkey = $rsItem['pkey']; // ini sudah pkey utk item variant
        $code = $rsItem['code'];
        $sellingPrice =  $rsItem['sellingprice'];
        $parentkey = $rsItem['parentkey'];
         
        // cari itemkey di shopee
        $rsItemLink = $this->searchLinkItem($parentkey); 
        $marketplaceitemkey = $rsItemLink[0]['marketplaceitemkey'];
 
        
        // cari model list di online yg skrg, buat ambil id imagenya jg biar gk perlu upload ulang
        $onlineModelList = $this->getModelList($parentkey);
        
        //convert ke format yg lebih rapi, key berdasarkan tier_index
        $existingModelList = $this->convertModelList($onlineModelList);
        $existingModelListBySKU = array_column($existingModelList['model'],null,'sku');
        // $this->setLog($existingModelList,true,'sh.txt');
        
               
        // cek dulu sudah pernah initVariant blm
        // NANTI DITENTUIN DR GET ITEM MODEL, jgn dr DB lg, karena bisa saja user sudah ada variant di shopee, tp gk kecatat di sistem
        $rsVariant = $item->getItemVariants($parentkey, $this->marketplaceKey); 
        $arrItemVariantKey = array_column($rsVariant,'pkey');
              
        $arrExistedItemVariant = (!empty($rsVariant)) ? $this->searchLinkItem($arrItemVariantKey) : array(); 
        $arrVariantLinkMap =  array_column($arrExistedItemVariant,'marketplaceitemkey','refkey');
        $arrVariantCodeMap = array_column($rsVariant,'pkey','code');
        $arrVariantByCode = array_column($rsVariant,null,'code');
        
        // init atau add list, perlu QOH
        $rsQOH = $itemMovement->getItemsQOH($arrItemVariantKey);
        $rsQOH = array_column($rsQOH,null, 'itemkey');
         
        foreach($rsVariant as $variantItem){ 
            $variantItemKey = $variantItem['pkey'];
            if(!isset($rsQOH[$variantItemKey])){
              $rsQOH[$variantItemKey]['itemkey'] = 0;
              $rsQOH[$variantItemKey]['itemcode'] = '';
              $rsQOH[$variantItemKey]['qtyinbaseunit'] = 0;  
            } 
        }
           
          
        if(empty($existingModelList['model'])){  // patokanyna harus dr model list yg online
            // kalo kosong, init 
            // atau kalo di sistem sudah ad variant, di shopee blm terbentuk sama sekali, jd sekali init saja
            
            $arrTierAndModel = $this->createTierAndModel($rsVariant,$rsQOH);  
            $tierItemIndex = $arrTierAndModel['tier_item_index'];
             
            $url = 'product/init_tier_variation';  
            $initVariantPayload = $this->createJsonBodyV2($url,
                                                            array(
                                                                    'item_id' => intval($marketplaceitemkey),
                                                                    'tier_variation' => $arrTierAndModel['tier_variation'],
                                                                    'model' => $arrTierAndModel['model']
                                                                )
                                                            );
    
            $result = $this->execute($url, 'POST',$initVariantPayload);
    
            $result = json_decode($result,true);
    		$result = $result['response'];
    		
    		foreach($result['model'] as $row) {  
                $variantItemKey = $tierItemIndex[$row['tier_index'][0]]['pkey'];
                
                // buat kepake dibawah
                $arrVariantLinkMap[$variantItemKey] = $row['model_id']; 
                $this->addItemMarketplaceLink($variantItemKey,$row['model_id']); 
            }
            
        }else{
            // kalo sudah ad satu variant pertama 
            // kalo blm ad di item_marketplace_link, add_model
            // sebelum add model, update tier variation dulu
            
            // biar gk perlu upload ulang gbr2 variant yg existing
            // kecuali dari edit item variant
          
          
            // GET CURRENT MODEL LIST 
            // bentuk list yg skrg ada di online 
           
            $arrVariantList = $onlineModelList[0]['tier_variation']; 
             
            // rename option kalo sudah berbeda 
            // foreach($arrVariantList[0]['option_list'] as $key=>$optRow){
            //     $itemCode =  $existingModelList['model'][$key]['sku']; // ambil kode item berdasarkan key tier index dulu 
                
            //     $arrVariant = $arrVariantByCode[$itemCode]['marketplace_variant'][0];  
            //     $optValue = htmlspecialchars_decode($arrVariant['optionvalue']); 
		   
            //     $arrVariantList[0]['option_list'][$key]['option'] = $optValue;
            // } 
            
            
            $arrTierAndModel = $this->createTierAndModel($rsVariant,null,$arrVariantList);  
             
            // $arrExistingVariantName = array_column($arrVariantList[0]['option_list'], 'option');  
            // $arrExistingVariantName = array_map('strtolower', $arrExistingVariantName);
            // $totalExistingTierIndex = count($arrExistingVariantName);
             
            $arrExistingVariantSKU = array_column($existingModelList['model'],'sku');
            $arrExistingVariantSKU = array_map('strtolower', $arrExistingVariantSKU);
            $totalExistingTierIndex = count($arrExistingVariantSKU);
            
      
            // tetep ambil semua variant 
            // baru disubset dengan yg sudah ad dionline
             
            $arrTierAndModelOptList = $arrTierAndModel['tier_variation'][0]['option_list']; 
             
            $arrNewVariant = array();
                     
            foreach($arrTierAndModelOptList as $key=>$modelRow){
                //  kalo sudah terdaftar, update nama saja, utk jaga2 
                //  index dari createTierAndModel BISA BERBEDA dengan urutan tier_index dr shopee, jd harus di looping ulang
                $modelRow['sku'] = strtolower($modelRow['sku']);
                
                if(in_array($modelRow['sku'],$arrExistingVariantSKU)){   
                    $onlineTierIndex = $existingModelListBySKU[$modelRow['sku']]['tier_index']; 
                    $arrVariantList[0]['option_list'][$onlineTierIndex]['option'] = $modelRow['option'];
                }else{
                     
                    array_push($arrVariantList[0]['option_list'], $modelRow);
                    
                    // ambil berdasarkan tier_item_index saja
                    $arrItem = $arrTierAndModel['tier_item_index'][$key]; 
                    array_push($arrNewVariant, array(
                                                    'pkey' => $arrItem['pkey'],
                                                    'code' => $arrItem['code'],
                                                    'sellingprice' =>  $arrItem['sellingprice'] 
                                        ));
                } 
            }
            
            // $this->setLog('$arrVariantList',true,'sh.txt');
            // $this->setLog($arrVariantList,true,'sh.txt');
            // die;
            
            $url = 'product/update_tier_variation';
            $initVariantPayload = $this->createJsonBodyV2($url,
                                                            array(
                                                                    'item_id' => intval($marketplaceitemkey),
                                                                    'tier_variation' => $arrVariantList
                                                                )
                                                            );
             
            $result = $this->execute($url, 'POST',$initVariantPayload);
            $result = json_decode($result,true);
              
              
            // NEW MODEL 
            $modelList = array();   
             
            foreach($arrNewVariant as $newVariantRow){
                
                $variantkey = $newVariantRow['pkey']; 
                 
                $stockPayload = array(); 
                array_push($stockPayload,array('stock' => intval($rsQOH[$variantkey]['qtyinbaseunit'])));
                
                array_push($modelList,array( 
                            'tier_index' => array($totalExistingTierIndex++),
                            'original_price' => intval($newVariantRow['sellingprice']),
                            'model_sku' => $newVariantRow['code'],
                            'seller_stock' => $stockPayload
                        ));
                  
            }
            
            
            $url = 'product/add_model';
            $variantPayload = $this->createJsonBodyV2($url, array(
                                                                    'item_id' => intval($marketplaceitemkey),
                                                                    'model_list' => $modelList 
                                                            ) );
 
            
            $result = $this->execute($url, 'POST',$variantPayload); 
            $result = json_decode($result,true);
    		$result = $result['response'];
    		 
    		foreach($result['model'] as $row) {
                    // buat kepake dibawah
                    $variantkey = $arrVariantCodeMap[$row['model_sku']];
                    $arrVariantLinkMap[$variantkey] = $row['model_id']; 
            		$this->addItemMarketplaceLink($variantkey,$row['model_id']); 
    		}
    		 
             
            // update informasi image
            
            // update ulang semua stok dengan update_stock
            $url = 'product/update_stock'; 
            
            $arrStockList = array();
            foreach($arrVariantLinkMap as $itemkey=>$modelid){ 
                $stockPayload = array(); 
                array_push($stockPayload,array('stock' => intval($rsQOH[$itemkey]['qtyinbaseunit']))); 
                array_push($arrStockList,
                            array(
                                    'model_id' =>  intval($modelid),
                                    'seller_stock' => $stockPayload
                                ) 
                        );                                 
            }
            
            $variantPayload = $this->createJsonBodyV2($url,
                                                    array(
                                                            'item_id' => intval($marketplaceitemkey),
                                                            'stock_list' => $arrStockList
                                                        )
                                                    );
                                                    
            $result = $this->execute($url, 'POST',$variantPayload); 
            $result = json_decode($result,true);
            $result = $result['response'];
              
        }
           
    }
    
    function getModelList($arrItemKeys){
			 
        $item = new Item();    
        $rsItemLink = $this->searchLinkItem($arrItemKeys);
              
        $url = 'product/get_model_list';
        
        $arrReturn = array();
        
        
        foreach($rsItemLink as $itemRow){
        	$payload = $this->createJsonBodyV2($url,array(
				'item_id' => intval($itemRow['marketplaceitemkey']),  
			));
        
            $result = $this->execute($url, 'GET', $payload);  
            
            array_push($arrReturn, json_decode($result,true)['response']);
             
        }
        
        return $arrReturn;
    }
    
    function createProduct($itemkey){
         
		// shopee
        $item = new Item();
        $itemMovement = new ItemMovement();
        $itemCategory = new ItemCategory(); 
        $marketplace = new Marketplace();
        
        $warehousekey = ''; // ini diudpate jika punya gudang khusus utk marketplace
        
        $rsItem = $item->getDataRowById($itemkey);
        if (empty($rsItem)) return;
         
        // cek kalo tipenya variant  
        if($rsItem[0]['isvariant']){
            $this->createProductVariant($rsItem[0]);
            return;
        }
        
        $rsItem = $rsItem[0];

        $rsCategory = $this->getCategoryUsedForMarketplace($rsItem['categorykey']);  
        $categorykey = intval($rsCategory[0]['marketplacecategorykey']);
        
        //parameter need to be sync
        $code = $rsItem['code']; 
        
		$arrItemMPInformation = $this->getItemInformationForMarketplace($itemkey)[0];
		
        $mpName = $arrItemMPInformation['name'];
        $name = (!empty($mpName)) ? $mpName : $rsItem['name']; // cek ke settingan ad overwrite nama gk
        
        // QOH attr 
        $qoh = $itemMovement->getItemQOH($itemkey,$warehousekey);  
        $qoh = ($qoh < 0) ? 0 : $qoh; // utk precaution  

        $weight = $rsItem['gramasi'];
        if ($rsItem['weightunitkey'] == UNIT['gram'])
            $weight /= 1000;

        $length = (!isset($rsItem['length']) || $rsItem['length'] <= 0 ) ? 1 : $rsItem['length'];
        $width = (!isset($rsItem['width']) || $rsItem['width'] <= 0 ) ? 1 : $rsItem['width'];
        $height = (!isset($rsItem['height']) || $rsItem['height'] <= 0 ) ? 1 : $rsItem['height']; 
  
         // img
        $arrImgId = array();
        $rsItemImage = $item->getItemImage($itemkey);   
        foreach($rsItemImage as $imgRow){     
            $sourceFile = DEFAULT_DOC_UPLOAD_PATH.'item/'.$itemkey.'/'.$imgRow['file']; 
            $imgResponse = $this->uploadToMediaSpace($sourceFile);

            if(!empty($imgResponse))
                array_push($arrImgId,$imgResponse);
        }
        
        // ATTRIBUTES attr  
        $arrAttributes = array(); 

        $rsAttributes = $item->getMarketplaceCategoryAttributes($itemkey, $this->marketplaceKey);  
        foreach($rsAttributes as $attributeRow){ 
         
			if ($attributeRow['attributekey'] == 0) continue; // kalo brand diupate terpish di V2
			
			$attrList = array(); 
			array_push($attrList , array( 'value_id' => intval($attributeRow['valueid']), 'original_value_name' => htmlspecialchars_decode($attributeRow['value'])));   // "< 33 inchies", agar bisa diproses tanda < nya
		    //array_push($attrList , array( 'value_id' => 0, 'original_value_name' => htmlspecialchars_decode($attributeRow['value'])));   // "< 33 inchies", agar bisa diproses tanda < nya
			array_push($arrAttributes, array('attribute_id' => intval($attributeRow['attributekey']), 'attribute_value_list' => $attrList));
		}
        
        // tambah attribute lainnya 
        // LOGISTICS 
        // yg dicentang di sistem
        $rsLogisticsDetail = $item->getMarketplaceLogistics($itemkey, $this->marketplaceKey); 
		$arrLogisticsKey = array_column($rsLogisticsDetail,'logisticid');
		
		// v2 
		$url = 'logistics/get_channel_list'; 
		$payload = $this->createJsonBodyV2($url);   
		$logisticsResponse = $this->execute($url, 'GET', $payload);
        $logisticsResponse = json_decode($logisticsResponse,true);
        
		$logisticsResponse = $logisticsResponse['response']['logistics_channel_list'];
        
        $arrLogistics = array();
        foreach($logisticsResponse as $logisticRow) {
             if(!$logisticRow['enabled']) continue; // kalo yg disable diproses, akan error
              
             $logisticid = ($logisticRow['mask_channel_id'] != 0) ? $logisticRow['mask_channel_id'] : $logisticRow['logistics_channel_id']; 
             $status = (in_array($logisticid,$arrLogisticsKey)) ? true : false;
                array_push($arrLogistics, array('logistic_id' => $logisticid, 'enabled' => $status));
        }

        $itemPrice = $this->getItemPrice($rsItem['pkey']); 
        $sellingPrice = array('current_price' => floatval($itemPrice['adjustedprice']), 'original_price' => floatval($itemPrice['adjustedprice']));
 
		$mpDesc = $arrItemMPInformation['shortdescription'];
		$mpDesc = (!empty($mpDesc)) ? $mpDesc : $rsItem['shortdescription'];

		$rsBrand = $this->getBrandUsedForMarketplace($rsItem['brandkey'],array('marketplacecategorykey' =>$categorykey));
		
        $stockPayload = array();
        array_push($stockPayload,array('stock' => intval($qoh)));
        
        $arrPayload = array(
                    'category_id' => $categorykey, // khusus positano, Seprai & Sarung Bantal Guling
                    'item_name' =>  htmlspecialchars_decode($name), // <- test utk " &quote;,
                    'condition' => $this->getItemConditionForMarketplace($rsItem['conditionkey']),  
                    'description' => html_entity_decode($mpDesc),
                    'original_price' => floatval($itemPrice['adjustedprice']),
                    'price_info' => $sellingPrice,
                    'seller_stock' => $stockPayload, 
                    'item_sku' => $code,
                    'weight' => floatval($weight),
                    'package_length' => intval($length),
                    'package_width' => intval($width),
                    'package_height' => intval($height),
                    'image' => array('image_id_list' => $arrImgId),
                    'logistic_info' => $arrLogistics,
                    'attribute_list' => $arrAttributes ,
			 		'brand' => array('brand_id' => intval($rsBrand[0]['marketplacebrandkey'])),  
                    'status' => ($rsItem['statuskey'] == 1) ? 'NORMAL' : 'UNLIST'
        ); 
        
		$url = 'product/add_item';
        $payload = $this->createJsonBodyV2($url,$arrPayload);
        
        // update 
        $result = $this->execute($url,'POST', $payload, array('actionkey' => $this->actionType['updateProduct'], 'ref' => $name ) );
        $result = json_decode($result,true)['response'];
                  
        if(isset($result['item_id']) && !empty($result['item_id'])){ 
			 
			$marketplaceItemKey = $result['item_id']; 
			$this->addItemMarketplaceLink($itemkey,$marketplaceItemKey);
		
			// update etalase
			// kalo storefrontny kosong (gk pernah update), gk perlu 
			
			// TIDAK kepasnggil pada saat add prduct baru
			
			$url = 'shop_category/add_item_list';
			$arrStorefrontKey = $this->getStorefrontUsedForMarketplace($rsItem['categorykey'],$rsItem['brandkey']);
			foreach($arrStorefrontKey as $storefrontkey){  
				$payload = $this->createJsonBodyV2($url,array(
					'shop_category_id' => intval($storefrontkey), 
					'item_list' => array(intval($marketplaceItemKey)),
				));  
				 
				$result = $this->execute($url,'GET', $payload, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Shop Category', 'ref' => $name )); 
			}
		}

    } 
      
    function updateProductsDescription($arrItemKey = ''){  
           
        if (!is_array($arrItemKey))  
            $arrItemKey = array($arrItemKey); 
        
        foreach($arrItemKey as $itemkey){  
            $syncCriteria = array();
            $syncCriteria['attr'] = array('shortDescription');
            $syncCriteria['type'] = 2;  
            $syncCriteria['itemkey'] = $itemkey; 

            $this->syncProducts($syncCriteria);
        }
        
    }
    
     function updateProductsQOH($arrItemsQOH){  
        // shopee
 
        $arrItemsQOH = $this->removeUnsyncItem($arrItemsQOH);
        
        // ambil ulang karena $arrItemsQOH sudah ad perubahan
        $arrItemKeys = array_keys($arrItemsQOH);
        
         
        // harus cek, kalo item blm ad, di add dulu 
        $this->resyncItemIfNotExist($arrItemKeys);

		$rsItemLink = $this->searchLinkItem($arrItemKeys);
		$rsItemLink = array_column($rsItemLink,'marketplaceitemkey','refkey');
             
		 // variant blm
		 
		//cari $parentmarketplaceitemkey
		$arrItemParentKey = array();
		foreach($arrItemsQOH as $itemkey=>$row)
			if($row['isvariant'] == 1) array_push($arrItemParentKey,$row['parentkey']);
		 
		$rsItemParentLink = $this->searchLinkItem($arrItemParentKey);
		$rsItemParentLink = array_column($rsItemParentLink,'marketplaceitemkey','refkey');
        	    
   
		$url = 'product/update_stock';
		 
		foreach($arrItemsQOH as $itemkey=>$row){
			
			$arrStockList = array();

            $stockPayload = array();
            array_push($stockPayload,array('stock' => intval($row['qtyinbaseunit'])));
			
			// kalo variant
			if($row['isvariant'] == 1){
			     
				$marketplaceitemkey = intval($rsItemParentLink[$row['parentkey']]);
				array_push($arrStockList,   array(
											'model_id' => intval($rsItemLink[$itemkey]), 
											'seller_stock' => $stockPayload
									)
						); 
			}else{
			     
		    	// kalo bukan variant
				$marketplaceitemkey = intval($rsItemLink[$itemkey]);
				array_push($arrStockList,   array(
											'model_id' => 0, 
											'seller_stock' => $stockPayload
								)
				);

			}
			
			
			$arrPayload = array('item_id' => $marketplaceitemkey, 'stock_list' => $arrStockList);  
			$payload = $this->createJsonBodyV2($url,$arrPayload); 
			
		    $this->executeOnBackground($url,'POST',$payload, array('actionkey' => $this->actionType['updateProductQOH'])); 
            // $this->execute($url,'POST', $payload,array('actionkey' => $this->actionType['updateProductQOH']));
		} 
		
 
    }
        
    //special condition
    function updateProductsPrice($arrItemKey = ''){  
           
        if (!is_array($arrItemKey))  
            $arrItemKey = array($arrItemKey); 
        
        foreach($arrItemKey as $itemkey){  
            $syncCriteria = array();
            $syncCriteria['attr'] = array('price','name', 'description'); // sementara
            $syncCriteria['type'] = 2;  
            $syncCriteria['itemkey'] = $itemkey; 

            $this->syncProducts($syncCriteria);
        }
        
    }
    
    function deleteProduct($rs){ 
        $rs = $this->searchLinkItem($rs[0]['pkey']);
        if (empty($rs)) return;

		$url = 'product/delete_item';
        $itemId = $rs[0]['marketplaceitemkey'];

        $arrPayload = array( 'item_id' => intval($itemId)); 
        $payload = $this->createJsonBodyV2($url,$arrPayload);

        $response = $this->execute($url,'POST', $payload);
        $response = json_decode($response,true);  
    }
    
    
    function getProducts($criteria = array()){
       

    } 
    
    function requestPickUp($rsSalesOrder){
        
        $rsSalesOrder = $rsSalesOrder['header'];
        
        // init logistics 
        $url = 'logistics/get_shipping_parameter';
        $payload = $this->createJsonBodyV2($url,array('order_sn' => $rsSalesOrder['refcode'] ));
        
        $response = $this->execute($url,'GET', $payload);
        $response = json_decode($response,true)['response']; 
        
        //$this->setLog($response,true,'logistic');
        
        $pickup = $response['pickup']['address_list'][0];
        $infoNeeded = $response['info_needed'];
         
        $arrPayload = array( 'order_sn' => $rsSalesOrder['refcode'] ); 
        $additionalPayload = array();
         
        
       if(isset($infoNeeded['pickup'])){ 
            $addressId = $pickup['address_id'];
            $pickupTimeId = $pickup['time_slot_list'][0]['pickup_time_id']; // cek lg pake id 0 gk ?
                 
            $additionalPayload = array(  
                'pickup' => array('address_id' => intval($addressId), 'pickup_time_id' =>  $pickupTimeId ) 
            ); 
           
        }else if (isset($infoNeeded['dropoff'])){ 
            //$addressId = $pickup['address_id'];
            //$pickupTimeId = $pickup['time_slot_list'][0]['pickup_time_id'];
                 
            $additionalPayload = array(  
                'dropoff' => array( 'sender_real_name' =>  $this->loadSetting('companyName') ) 
            ); 
        }else { 
   
        }
        
        if(!empty($additionalPayload))
            $arrPayload = array_merge($arrPayload, $additionalPayload);
        
        $url = 'logistics/ship_order';
        $payload = $this->createJsonBodyV2($url,$arrPayload);
        
        // set logistics 
        $response = $this->execute($url,'POST', $payload);
        $response = json_decode($response,true)['response'];  
         
        $this->updateRequestPickupStatus($rsSalesOrder['pkey']);
    }
	
    function onConfirmTrans($rsSalesOrder){ 
        
        // shopee 
        if($this->marketplaceAutoPickup)
            $this->requestPickUp($rsSalesOrder);
         
    }
    
    function syncMarketplaceBrand($syncType){
            
           $brand = new Brand();
           try{  
                    $dbCon = $this->masterConn();

                    if(!$dbCon->startTrans())
                        throw new Exception($this->errorMsg[100]);

                    $criteria = '';

                    //echo $criteria;
                    $sql = 'select value from '.$this->tableMarketplaceCategoryAttributes.' where label = \'merek\'';
                    $rs = $dbCon->doQuery($sql);
                    $rs = array_column($rs,'value');
               
                    // get exisiting brand
                    $rsExistingBrand = $this->getMarketplaceBrand();
                    $rsExistingBrand = array_column($rsExistingBrand,'name'); 
               
                    //$array = array_unique (array_merge ($array1, $array2));
               
                    foreach($rs as $row){
                        $arrBrand =  json_decode(htmlspecialchars_decode($row)); 
                        if(empty($arrBrand)) continue;
                        
                        foreach($arrBrand as $brandRow){ 
                            
                            $brandName = ucwords(trim(strtolower($brandRow))); // normalize brand
                            
                            if (in_array($brandName, $rsExistingBrand)) continue;   
                             
                            $sql = 'insert into '.$this->tableMarketplaceBrand. ' (name,marketplacekey) values ('.$this->oDbCon->paramString($brandName).','.$this->oDbCon->paramString($this->marketplaceProviderKey).')';
                            $dbCon->execute($sql);
                             //$this->setLog($sql,true);
                           
                            array_push($rsExistingBrand,$brandName );
                        }
                    }
                

                    $dbCon->endTrans(); 
             
		
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}		
         
        $dbCon = null;
        
    }
    
//	function syncMarketplaceBrandV2($syncType, $categorykey = array()){
//            
//			// register semua brand ke master marketplace_brand
//		    // di attribute cuma isi indexnya saja
//			
//				$brand = new Brand();
//				$dbCon = $this->masterConn();
//
//				// loop utk setiap category
//				$sql = 'select ' . $this->tableMarketplaceCategoryAttributes. '.marketplacecategorykey from ' . $this->tableMarketplaceCategoryAttributes. ' 
//						where 
//							' . $this->tableMarketplaceCategoryAttributes. '.attributekey = 0 and 
//							' . $this->tableMarketplaceCategoryAttributes. '.value = \'\' and 
//							' . $this->tableMarketplaceCategoryAttributes. '.label = \'Brand\' and 
//							' . $this->tableMarketplaceCategoryAttributes. '.marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceProviderKey);
// 
//				if(!empty($categorykey)){ 
//					// cari dr item_category_marketplace_detail
//					$sqlCategory = 'select 
//										'.$this->tableItemCategoryMarketplaceDetail.'.marketplacecategorykey 
//									from '.$this->tableItemCategoryMarketplaceDetail.' 
//									where 
//										'.$this->tableItemCategoryMarketplaceDetail.'.refkey in ('.$this->oDbCon->paramString($categorykey,',').') and
//										'.$this->tableItemCategoryMarketplaceDetail.'.marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceProviderKey);
//					
//					$rsItemCategory = $this->oDbCon->doQuery($sqlCategory);
//					
//					
//					$sql .= ' and  ' . $this->tableMarketplaceCategoryAttributes. '.marketplacecategorykey in ('. $this->oDbCon->paramString(array_column($rsItemCategory,'marketplacecategorykey'),',').')';
//				}
//  
//			   		$rs = $dbCon->doQuery($sql);
//					
////					$this->setLog($sql,true);
////					$this->setLog($rs,true);
////					die;
////		
//			   		foreach($rs as $row){ 
//						try{  
//
//							if(!$dbCon->startTrans(true))
//								throw new Exception($this->errorMsg[100]);
// 
//							$offset = 0;
//							$pageSize = 100;
//							$nextPage = false;
//
//							$arrBrandCol = array();
//
//							do{ 
//								$url = 'product/get_brand_list'; 
//								$payload = $this->createJsonBodyV2($url, array('offset' => $offset,
//																			   'page_size' => $pageSize, 
//																			   'category_id' => $row['marketplacecategorykey'],
//																			   'status' => 1)
//																  ); 
//
//
//								$response = $this->execute($url, 'GET', $payload);
//								$response = json_decode($response,true);  
//
//								if(isset($response['response']) && !empty($response['response'])){
//									$response = $response['response']; 
//									$arrBrandCol = array_merge($arrBrandCol, $response['brand_list']); 
//									$offset = $response['next_offset']; 
//								}
//
//								$nextPage =  ($response['has_next_page'] == 1) ? true : false;
//
//							}while($nextPage);
//
//							if (empty($arrBrandCol)) continue;
//							
//							//$this->setLog($arrBrandCol,true);
//							
//							// semua brand utk kategori ini, akan dipake utk diupdate di table attribute
//							$arrAllBrandId = array_column($arrBrandCol, 'brand_id');
//
//							// SAVE MASTER KE TABLE
//							// intersect dulu
//
//							$sql = 'select marketplacebrandkey from ' . $this->tableMarketplaceBrand .' where marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceProviderKey);
//							$rsExistingBrand = $dbCon->doQuery($sql);
//							$rsExistingBrand = array_column($rsExistingBrand, 'marketplacebrandkey');
//
//
//							//	$arrBrandId didepan karena patokan yg mau di diff  
//							$arrNewBrandKey = array_diff($arrAllBrandId, $rsExistingBrand);	
//
//							// re-recolumn
//							$arrBrandId = array_column($arrBrandCol, 'original_brand_name', 'brand_id');
//
//							foreach($arrNewBrandKey as $brandkey){ 
//								$sql = 'insert into ' . $this->tableMarketplaceBrand .' (marketplacebrandkey,name,marketplacekey) 
//										values ('. $this->oDbCon->paramString($brandkey).','. $this->oDbCon->paramString($arrBrandId[$brandkey]).','. $this->oDbCon->paramString($this->marketplaceProviderKey).') ';
//								$dbCon->execute($sql);
//							}
//
//							// UPDATE BRANDKEY / BRAND_ID KE TABLE ATTRIBUTE
//							$sql = 'update ' . $this->tableMarketplaceCategoryAttributes. ' set value = '.$this->oDbCon->paramString(json_encode($arrAllBrandId)).' 
//									where  marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceProviderKey) .' and 
//										   marketplacecategorykey = '.$this->oDbCon->paramString($row['marketplacecategorykey']).' and
//										   attributekey = 0  and 
//										   label = \'Brand\'
//									';
//							
//							//echo $sql;
//							$dbCon->execute($sql);
//
//							$dbCon->endTrans(); 
//
//						 } catch(Exception $e){
//							$dbCon->rollback(); 
//						}		
//
//					} 
//         
//        $dbCon = null;
//        
//    }
//    

    function syncCategoryForBrand($marketplaceCategoryId=array()){
        // hanya digunakan sekali, khusus mencatat setiap brand berlaku utk kategori apa saja
        // utk function import brand coba pake yg diatas, tp gk jelas jg kenapa sql nya => where attributekey = 0, kayanya gk bisa dipake
        
    	// flag hany utk flaging jika proses timeout, biar bisa lanjut
    	
    	$dbCon = $this->masterConn();
    	  
    	if(empty($marketplaceCategoryId)){
    	    //  ' . $this->tableMarketplaceCategory. '.flag = 0 and 
        	$sql = 'select '.$this->tableMarketplaceCategory. '.marketplacecategorykey from ' . $this->tableMarketplaceCategory. ' 
    				where  
    					' . $this->tableMarketplaceCategory. '.isleaf = 1 and 
    					' . $this->tableMarketplaceCategory. '.marketplacekey = ' . $dbCon->paramString($this->marketplaceProviderKey).'
    				order by marketplacecategorykey asc	
    				';
		    
    	}else{
    	    	$sql = 'select '.$this->tableMarketplaceCategory. '.marketplacecategorykey from ' . $this->tableMarketplaceCategory. ' 
    				where   
    					' . $this->tableMarketplaceCategory. '.marketplacecategorykey in (' . $dbCon->paramString($marketplaceCategoryId,',').') and
    					' . $this->tableMarketplaceCategory. '.marketplacekey = ' . $dbCon->paramString($this->marketplaceProviderKey).'
    				order by marketplacecategorykey asc	
    				';
		    
    	}
    	 
		// sementara utk test
		//$sql .= ' limit 10 ';
		 
     	$rs = $dbCon->doQuery($sql);
  
         
        // loop per kategori
        foreach($rs as $row){ 
                $marketplaceCategoryId = $row['marketplacecategorykey'];
                
				try{  

					if(!$dbCon->startTrans(true))
						throw new Exception($this->errorMsg[100]);

					$offset = 0;
					$pageSize = 100;
					$nextPage = false;

					$arrBrandCol = array();

					do{ 
						$url = 'product/get_brand_list'; 
						$payload = $this->createJsonBodyV2($url, array('offset' => $offset,
																	   'page_size' => $pageSize, 
																	   'category_id' => $marketplaceCategoryId,
																	   'status' => 1)
														  ); 


						$response = $this->execute($url, 'GET', $payload);
						$response = json_decode($response,true);  

						if(isset($response['response']) && !empty($response['response'])){
							$response = $response['response']; 
							$arrBrandCol = array_merge($arrBrandCol, $response['brand_list']); 
							$offset = $response['next_offset']; 
						}

						$nextPage =  ($response['has_next_page'] == 1) ? true : false;
         
					}while($nextPage);
  
                    $arrBrandId = array_column($arrBrandCol,'brand_id');
                     
                    //echo $marketplaceCategoryId . ' => ' .count($arrBrandId).'<br>';
                    
                    // select ulang utk dapetin  existing category value
				    $sql = 'select '.$this->tableMarketplaceBrand.'.pkey,'.$this->tableMarketplaceBrand.'.marketplacecategoryid 
				    from '.$this->tableMarketplaceBrand.' where '.$this->tableMarketplaceBrand.'.marketplacebrandkey in ('.$dbCon->paramString($arrBrandId,',').') ';
				     
				    $rsExistingValue = $dbCon->doQuery($sql);
				     
				    $totalValue = count($rsExistingValue);
				    for($i=0;$i<$totalValue;$i++){
				         
				        if(!$this->isJSON($rsExistingValue[$i]['marketplacecategoryid'])){
				            $categoryVal = array($marketplaceCategoryId);
				        }else{
				            $categoryVal = json_decode($rsExistingValue[$i]['marketplacecategoryid']);   
				            if(!in_array( $marketplaceCategoryId,$categoryVal)){
				                array_push($categoryVal,$marketplaceCategoryId); 
				            }
				        }
				        
				        $sql = 'update '.$this->tableMarketplaceBrand.' set marketplacecategoryid = \''. addslashes(json_encode($categoryVal)).'\' 
				                where pkey = ' .$dbCon->paramString($rsExistingValue[$i]['pkey']) ;
				        $dbCon->execute($sql);
				        
				    }
				 
				    $sql = 'update 
			                    ' . $this->tableMarketplaceCategory. ' set flag = 1 
				            where marketplacecategorykey = '.$dbCon->paramString($marketplaceCategoryId).' and 
				        	' . $this->tableMarketplaceCategory. '.marketplacekey = ' . $dbCon->paramString($this->marketplaceProviderKey);
				    $dbCon->execute($sql);
				    
				    
                    $this->setLog($marketplaceCategoryId . ' import done',true,'import-sh.txt');
				    
					$dbCon->endTrans(); 

				 } catch(Exception $e){
					$dbCon->rollback(); 
				}		

			} 


        $dbCon = null;
    }
	
    function syncMarketplaceCategory($syncType){
        $url = 'product/get_category' ;
		
        // $syncType -> blm tentu kepake di category tree
        
        $dbCon =  $this->masterConn();
        $sql = 'delete from '.$this->tableMarketplaceCategory.' where marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceProviderKey); 
        $dbCon->execute($sql);	
         
        $sql = 'select pkey from '.$this->tableMarketplaceCategory.' order by pkey desc limit 1';
        $rs =  $dbCon->doQuery($sql);	
        
        $sql = 'ALTER TABLE '.$this->tableMarketplaceCategory.' AUTO_INCREMENT='. ($rs[0]['pkey']+1);
        $dbCon->execute($sql);	
        
        $rsExistingCategory = $this->getMarketplaceCategory();
        $rsExistingCategory = array_column($rsExistingCategory,null,'pkey'); 
         
        $arrPayload = array('language' => 'id');
        $payload = $this->createJsonBodyV2($url,$arrPayload);
         
        $response = $this->execute($url, 'GET', $payload);
        $response = json_decode($response,true);  
		
		$response = $response['response']['category_list'];
		
        foreach ($response as $categoryRow){  
            $categoryRow['marketplacecategorykey'] = $categoryRow['category_id']; 
            $categoryRow['name'] = $categoryRow['display_category_name']; 
            $categoryRow['parentkey'] = $categoryRow['parent_category_id']; 
            $categoryRow['leaf'] = ($categoryRow['has_children']) ? 0 : 1;
            
            $this->addMarketplaceCategory($categoryRow);
        }
     
        $dbCon = null;
    }
    
	
    function syncMarketplaceCategoryAttributes($syncType,$marketplacecategorykey=array()){  
		  // shopee 
        try{ 
			
            $limit = ''; // 'limit 0,500';
            
            $dbCon = $this->masterConn();
            
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	  
            $criteria = '';
            if ($syncType == 1){
               
            }

            //echo $criteria;
            $criteria = ' and isleaf <> 0 ';
            if(!empty($marketplacecategorykey))
                $criteria .= ' and marketplacecategorykey = ' . $dbCon->paramString($marketplacecategorykey,',');
                
            $rsCategory = $this->getMarketplaceCategory($criteria, $limit);  
            //$temp = false;
            
            foreach($rsCategory as $categoryRow){ 
                  
				$url = 'product/get_attributes'; 
				$arrPayload = array('category_id' => intval($categoryRow['marketplacecategorykey']),'language' => 'id','country' => 'ID');
				$payload = $this->createJsonBodyV2($url,$arrPayload);
				 
                // set attributes 
                $response = $this->execute($url,'GET', $payload);
                $response = json_decode($response,true);  
  
                if (!isset($response['response']['attribute_list'])) continue;

                $response = $response['response']['attribute_list'];

                $rsExistAttributes = $this->getMarketplaceCategoryAttributes($categoryRow['marketplacecategorykey'], '','','',false);
                
                $rsExistAttributes = array_column($rsExistAttributes,'attributekey');

                foreach($response as $row){

                    $attibuteId = $row['attribute_id'];
                    
                    if (!$row['is_mandatory'])  continue; 

                    $inputType = $this->getInputType($row['input_type']);

                    $newRow = array();
                    $newRow['marketplacecategorykey'] = $categoryRow['marketplacecategorykey'];
                    $newRow['attributekey']  =  $attibuteId ;
                    $newRow['name']   =  '';
                    $newRow['label']   =  $row['display_attribute_name'] ;
                    $newRow['attributeType']   =  $row['input_type'] ;
                    $newRow['inputType']   =  $inputType;
                    $newRow['isMandatory']   =  $row['is_mandatory'] ;
                    $newRow['value']   =  json_encode($row['attribute_value_list']);
     
                    if(in_array($attibuteId,$rsExistAttributes))
                        $this->updateMarketplaceCategoryAttributes(array('attributekey' => $attibuteId),$newRow);
                    else
                        $this->addMarketplaceCategoryAttributes($categoryRow,$newRow);

                }
 
            } 
          
			$dbCon->endTrans(); 
             
		
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}		
         
        $dbCon = null;
	}
	 
  
    function syncMarketplaceLogistics(){   
        try{  
            $dbCon = $this->masterConn();
            
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	   
			$url = 'logistics/get_channel_list';

			$payload = $this->createJsonBodyV2($url);

			// set attributes 
			$response = $this->execute($url,'GET', $payload);
			$response = json_decode($response,true);  
			$response = $response['response']['logistics_channel_list'];

			$arrLogisticId = array_column($response, 'logistics_channel_id');
             
            //echo $criteria;
            $rsExistedLogistics = $this->getMarketplaceLogistics(' and logisticid in ('.$dbCon->paramString($arrLogisticId,',').') ');  
            $rsExistedLogistics = array_column($rsExistedLogistics, 'logisticid');
 
            $arrMPActiveLogistics  = array(); // buat non aktifin yg dr marketplacenya memang sudah dihilangkan
            foreach($response as $row){ 
                array_push($arrMPActiveLogistics, $row['logistics_channel_id']);
                
                $statuskey = ($row['enabled']) ? 1 : 2;
                if (in_array($row['logistics_channel_id'], $rsExistedLogistics)) 
					$sql = 'update '.$this->tableMarketplaceLogistics. ' 
							set name = '.$dbCon->paramString($row['logistics_channel_name']).',
							    maskchannelid = '.$dbCon->paramString($row['mask_channel_id']).',
							    statuskey = '.$dbCon->paramString($statuskey).'
							where 
							    logisticid = '.$dbCon->paramString($row['logistics_channel_id']).' and
							    marketplacekey = '.$dbCon->paramString($this->marketplaceProviderKey);
                else
					$sql = 'insert into 
								'.$this->tableMarketplaceLogistics. ' (marketplacekey, name, logisticid,maskchannelid,statuskey) 
						   values ('.$dbCon->paramString($this->marketplaceKey).','.$dbCon->paramString($row['logistics_channel_name']).','.$dbCon->paramString($row['logistics_channel_id']).','.$dbCon->paramString($row['mask_channel_id']).','.$dbCon->paramString($statuskey).') ';


				$dbCon->execute($sql);
                
            } 

			// buat non aktifin yg dr marketplacenya memang sudah dihilangkan
            $sql = 'update '.$this->tableMarketplaceLogistics. ' set statuskey = 2 	where  logisticid not in ('.$dbCon->paramString($arrMPActiveLogistics,',').') and  marketplacekey = '.$dbCon->paramString($this->marketplaceKey);
            $dbCon->execute($sql);
                 
			$dbCon->endTrans(); 
             
		
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}		
         
        $dbCon =null;
    }
  
    
  function getAirwayBill($arrOrderId = ''){
         // shopee
         
        if(isset($arrOrderId) && !is_array($arrOrderId))
            $arrOrderId = array($arrOrderId); 
        
        $arrPayloadCreate = array(); 
        $arrPayloadDownload = array(); 
        
        // get tracking number 
        foreach($arrOrderId as $sn){ 
            $url = 'logistics/get_tracking_number';
            $payload = $this->createJsonBodyV2($url,array('order_sn' =>  $sn)); 
            $response = $this->execute($url,'GET', $payload);  
            $response = json_decode($response,true)['response'];
            $trackingNumber = $response['tracking_number']; 
            
            array_push($arrPayloadCreate, array('order_sn' => $sn, 'tracking_number' => $trackingNumber ));
            array_push($arrPayloadDownload, array('order_sn' => $sn));
         
        }
       
       // create document dulu
        $url  = 'logistics/create_shipping_document'; 
        $payload = $this->createJsonBodyV2($url,array('order_list' =>  $arrPayloadCreate));  
        $ordersHeader = $this->execute($url,'POST', $payload);
         
        
       // baru download
        $url  = 'logistics/download_shipping_document'; 
        $payload = $this->createJsonBodyV2($url,array('order_list' =>  $arrPayloadDownload));  
        $ordersHeader = $this->execute($url,'POST', $payload); 
        
        return $ordersHeader;
         
    }
     
    function testConnection(){
              
		$url = 'shop/get_shop_info';   
		$response = $this->execute($url,'GET', array());  
// 		$this->setLog($this->appKey,true);
		
        $response = json_decode($response,true);
        
        $arrReturn = array();
        if (isset($response['error']) && $response['error'] == 'invalid_acceess_token') {
            $arrReturn['status'] = false;
            $arrReturn['callbackURL'] = $this->callbackURL;
            $arrReturn['authURL'] = $this->createAuthLink();
        }else{
            $arrReturn['status'] = true;
        } 
        
        $this->setMarketplaceLog(array('actionkey' => $this->actionType['testConnection'], 'issuccess' => $arrReturn['status']));
        return $arrReturn;
    }
    
//    function executeRequest($action,$payload,$arrLog = array()){
//        // shopee 
//		
//		$v1Url =  'https://partner.shopeemobile.com/api/v1/';
//        $apiUrl = $v1Url.$action ; 
//        $auth = $this->signature($apiUrl,$payload);
//      
//        $header = array(
//            'Content-Type: application/json', 
//            'Content-Length: ' . strlen($payload),
//            'Authorization: '.$auth
//        );
//
//
//        $connection = curl_init(); 
//        curl_setopt($connection, CURLOPT_URL, $apiUrl);
//        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
//        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
//        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
//        curl_setopt($connection, CURLOPT_POST, 1); 
//        curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
//        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
//
//        $response = curl_exec($connection);  
//        
////		$this->setLog($action,true,'sh');
////		$this->setLog($response,true,'sh.txt');
//		
//        $arrLog['response'] = $response; 
//        $this->setMarketplaceLog($arrLog);
//        
//        return $response;
//    }
	
	function getCommonQuery($url){
		
		//setup common parameter
		//common query bisa beda2 setiap request, kalo semua di add, jadinya WRONG SIGN
        
		$arrOpt = array();
		$timestamp = time();
		$partnerId =  $this->appKey; 
		$partnerKey = $this->secretKey;
		$shopId = $this->shopId;
		$accessToken = $this->accessToken;
		$signUrl = $this->apiPathVersion.$url;
		
		switch($url){
			case 'shop/auth_partner': 
			case 'auth/token/get':
			case 'auth/access_token/get' : 
			case 'media_space/upload_image':
											$arrOpt = array('partner_id', 
													        array('timestamp' => $timestamp),
													        array('sign' => hash_hmac('sha256', $partnerId.$signUrl.$timestamp,$partnerKey))
													  );   
									   		break;
				
			default :						$arrOpt = array('partner_id', 
														array('timestamp' => $timestamp),
														array('access_token' => $accessToken),
														array('shop_id' => $shopId),
														array('sign' => hash_hmac('sha256', $partnerId.$signUrl.$timestamp.$accessToken.$shopId,$partnerKey))
												  );   
						break; 
				
				//Signature generated by partner_id, api path, timestamp, access_token, shop_id and partner_key via HMAC-SHA256 hashing algorithm. More details:
		}

		
		$commonQuery = $this->getDefaultParametersV2($url, $arrOpt ,true);   //,'access_token',shop_id
		
		return $commonQuery;
	}

	function execute($url,$method='GET',$payload=array(), $arrLog = array(), $arrOpt = array()){ 
        // shopee API v2  
       	 
		
		$rawReturn = (isset($arrOpt['rawReturn'])) ? $arrOpt['rawReturn'] : false;
		$recallUpdateToken = (isset($arrOpt['recallUpdateToken'])) ? $arrOpt['recallUpdateToken'] : true;
		
        $apiUrl = $this->url.$url.'?'.$this->getCommonQuery($url);
		 
		
		if ($method == "GET" && !empty($payload)) 
			$apiUrl .= '&'. http_build_query(json_decode($payload));
		
        $header = array(
            'Content-Type: application/json',  
        );
		 
        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $apiUrl);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

		if ($method != "GET") 
        	curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
 
        $response = curl_exec($connection);  
//         
// 		$this->setLog(chr(13).'>>>>>>>>>>>>>>>> ',true,'sh.txt');
// 		$this->setLog($apiUrl,true,'sh.txt');
// 		$this->setLog($payload,true,'sh.txt');
// 		$this->setLog($response,true,'sh.txt');
// 		$this->setLog('<<<<<<<<<<<<<<< '.chr(13),true,'sh.txt');
//		
		// kalo error token, request ulang 
		if($recallUpdateToken){
			$result = json_decode($response,true);
			if ($result['error'] == 'invalid_acceess_token') { 
				$this->updateTokenShopee();
				$this->execute($url,$method,$payload, $arrLog, array('rawReturn' => $rawReturn,'recallUpdateToken' => false) );  
			}
		}
			
        $arrLog['response'] = $response; 
        $this->setMarketplaceLog($arrLog);
        
        return $response;
		 
    }
	
    function signature($url,$body){   
		// cuma kepake di v1
		$data = $url . '|' . $body;   
		$hash = hash_hmac('sha256', $data, $this->secretKey); 
		return $hash;
    }

    function createJsonBody($data = array()) {
            $data = array_merge($data,$this->getDefaultParameters()); 
            return  json_encode($data);  
    } 
	
    function getDefaultParameters(){ 
			return array (
				'partner_id' => $this->appKey,
				'shopid' => $this->shopId,
				'timestamp' => time(),
			); 
    }
	
	function createJsonBodyV2($url, $data = array()) { 
            $data = array_merge($data,$this->getDefaultParametersV2($url)); 
            return  json_encode($data);  
    }
	
    function getDefaultParametersV2($url, $defaultParam = array(),$buildQuery = false){
   
			$partnerId =  $this->appKey; 
			$shopId = $this->shopId;
			$accessToken = $this->accessToken;

			// biar gampaang saja
			$requestCol =  array (
				'partner_id' => $partnerId, 
				'access_token' => $accessToken,
				'shop_id' => $shopId, 
			); 
			 
			$returnArr = array();
			foreach($defaultParam as $requestRow) { 
				if(is_array($requestRow)){
					// ada isiny
					$index = array_keys($requestRow)[0];
					$returnArr[$index] = $requestRow[$index];	
				} 
				else{ 
					$returnArr[$requestRow] = $requestCol[$requestRow]; 
				}
			}
		
			return ($buildQuery) ? http_build_query($returnArr) : $returnArr; 
		 
    }
    
    function getTokenByResendCode(){
        //get_token_by_resend_code
        
        $url = 'public/get_token_by_resend_code'; 
        $data = array('resend_code' => 'resend'.$this->refCode);
        $data = array_merge($data,$this->getDefaultParametersV2($url));
			
		$payload = $this->createJsonBodyV2($url, $data); 
		
// 		$this->setLog($payload,true,'sh.txt');
        $response = $this->execute($url, 'POST', $payload, array(), array('recallUpdateToken' => false) ); // harus false agar gk looping forever 
		
		return $response;

    }

	function getRefreshToken($renew = false){  
		
		if($renew){
			$url = 'auth/access_token/get'; 
			$data = array('refresh_token' => $this->refreshToken);   
		}else{
			$url = 'auth/token/get';
			$data = array('code' => $this->refCode);   
		}
		
		$data = array_merge($data,$this->getDefaultParametersV2($url, array('partner_id','shop_id')));
			
		$payload = $this->createJsonBodyV2($url, $data); 
		
// 		$this->setLog("payload",true);
// 		$this->setLog($payload,true);
		
        $response = $this->execute($url, 'POST', $payload, array(), array('rawReturn' => false,'recallUpdateToken' => false) ); // harus false agar gk looping forever 
		
		return $response;
	}
	
	function updateRefreshToken($renew = false){

		$response = $this->getRefreshToken($renew);
		
// 		$this->setLog($response,true);
		
		$response = json_decode($response,true);
		
		if(!isset($response['access_token'])) return;
		
		$refreshToken = $response['refresh_token'];
		$accessToken =  $response['access_token'];
			
		try{

			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
 
			$sql = 'update '.$this->tableName.' 
					set 
						refreshtoken = '.$this->oDbCon->paramString($refreshToken).', 
						accesstoken =  '.$this->oDbCon->paramString($accessToken).',
						modifiedon = now()
					where 
						shopid = '.$this->oDbCon->paramString($this->shopId);

			$this->oDbCon->execute($sql);

			$this->oDbCon->endTrans();
		}catch(Exception $e){
			$this->oDbCon->rollback(); 
		}	 
	}
	
    function getInputType($type){
        
        $type = strtoupper($type);
        
        switch ($type){
            case 'DROP_DOWN': 
            case 'MULTIPLE_SELECT_COMBO_BOX': // v2
            case 'COMBO_BOX': $returnType = INPUT_TYPE['select'];
                              break;
                
            case 'TEXT_FILED': $returnType = INPUT_TYPE['text'];
                              break;
           	
            default : $returnType = 0; // buat validasi mana yg blm terdaftar tipe inputannya
                      break;
                                  
        }
        
        return $returnType; 
    }
    
    function getAttributeKeyByLabel($label,$categorykey){
        
        $dbCon = $this->masterConn();
        
        $sql = 'select attributekey from '.$this->tableMarketplaceCategoryAttributes.' where label = '.$dbCon->paramString($label).' and marketplacecategorykey = '.$dbCon->paramString($categorykey).' and marketplacekey = '. $this->marketplaceProviderKey ;    
        $rs = $dbCon->doQuery($sql);
        $dbCon = null;
        
        return $rs[0]['attributekey'];
        
    }
    
    function createAuthLink(){
        // shopee
        // use this to register new store
        
        $redirectURL = 'https://wintera.co.id/api/shopee/wintera-callback-auth';
           
		$url = 'shop/auth_partner';
		$queryString = $this->getCommonQuery($url);
		$authUrl = $this->url.$url.'?'.$queryString.'&redirect='.$redirectURL;   
		return $authUrl; 
		 
    }
    
    
    function updateARPayment(){ 
           
        $warehouse = new Warehouse();
        $customCode = new CustomCode();
        $paymentMethod = new PaymentMethod();
         
        $warehousekey = $warehouse->getDefaultData(); 
        
        $arrDepositId = array();
        $arrTransactionId = array();
        $loop = true;
        
        $savePoint = 20;
        $orderPerPage = 40;
        $offset = 0;

        $dateFrom = date('Y-m-d',strtotime('-1 days'));
        $dateTo = date('Y-m-d 23:59:59',strtotime('-1 days'));

        $dateFromU = date("U", strtotime($dateFrom));  
        $dateToU = date("U", strtotime($dateTo));  

		$url = 'payment/get_wallet_transaction_list';
		
        while($loop){
            $payload = $this->createJsonBodyV2($url,array(
                'create_time_from' => intval($dateFromU),
                'create_time_to' => intval($dateToU),
                'page_size' => $orderPerPage,
                'page_no' => $offset
            ));

            $result = $this->execute($url,'GET', $payload);
            $result = json_decode($result,true);
            $result = $result['response'];
			
            $offset += $orderPerPage; // nanti dicek lg
            $savePoint--;
            
            $paymentResult = $result['transaction_list']; 
            
            foreach($paymentResult as $row){
                
                if(in_array($row['withdraw_id'],$arrDepositId)) continue;  // dulu transaction_id
                array_push($arrDepositId,$row['withdraw_id']); 
                
                $transId = $row['order_sn'];
                
                // add saja dulu semua, mau positif atau negativ
                if(!empty($transId))  array_push($arrTransactionId,array('transId'=>$transId, 'trdate' => $dateFrom, 'amount'=>$row['amount'])); 
            }
            
            $loop = $result['more'];
                
            if ($savePoint<0){
              $loop = false; // buat jaga2
              $this->setLog('save check point entered',true,'ck-sh');
            } 
            
        }
  
        // gk ad payment
        return (!empty($arrTransactionId)) ? $this->createARPayment($arrTransactionId) : 'no payment'; 
         
    } 
    
    
    // LOG 
    function logPrintAirwayBill($response){
        // shopee
        $isSuccess = true;
        $message = '';
        
        $response = json_decode($response,true);   
         
        if(isset($response['result']['errors']) && !empty($response['result']['errors'])){ 
            $isSuccess = false;
            $message = $response['result']['errors'][0]['error_description']; 
        } 
        
        return array('issuccess' => $isSuccess, 'message' => $message);
    }
    
    function logUpdateProduct($response){
        // shopee
        
        $response = json_decode($response,true);   
        
        $isSuccess = true;
        $message = ''; 
         
        if( isset($response['error']) ){
            $isSuccess = false; 
            $message = $response['error'].chr(13);
            $message .= $response['msg'];  
        }
        
        return array('issuccess' => $isSuccess, 'message' => $message ); 
    }
    
    function logUpdateProductQOH($response){
              
        $response = json_decode($response,true);   
        
        $isSuccess = true;
        $message = ''; 
           
        if( isset($response['batch_result']['failures']) && !empty($response['batch_result']['failures']) ){
            $isSuccess = false; 
            
            $arrErr = array();
            foreach($response['batch_result']['failures'] as $row)
                array_push($arrErr, $row['error_description']);
                
            $message = implode(chr(13),$arrErr);
        }
        
        return array('issuccess' => $isSuccess, 'message' => $message ); 
    }
    
    
    function updateDeliveredOrders($arrRefId ,$useOrderId = true){ 
      // dari webhook
        if(!is_array($arrRefId))
            $arrRefId = array($arrRefId);
         
        $salesOrder = new SalesOrder();
        $field = ($useOrderId) ? $salesOrder->tableName.'.marketplaceorderid' :  $salesOrder->tableName.'.refcode';
         
        $rsSalesOrder = $salesOrder->searchDataRow(array($salesOrder->tableName.'.pkey',$salesOrder->tableName.'.code',$salesOrder->tableName.'.refcode',$salesOrder->tableName.'.statuskey'),
                                                   ' and '.$field.' in ('.$this->oDbCon->paramString($arrRefId,',').') and '.$salesOrder->tableName.'.statuskey in (2)
                                                     and '.$salesOrder->tableName.'.marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey)
                                                  );
        // kedepan mungkin perlu ditambahkan, kalo masih menunggu, proses jd konfirmasi dulu
        
        foreach($rsSalesOrder as $row){   
            if($row['statuskey'] == 1) {   
               // $salesOrder->changeStatus($row['pkey'], TRANSACTION_STATUS['konfirmasi'], '',false, true);
               //$this->setLog('auto confirm '.$row['code']. ' - ' .$row['refcode'],true,'auto-sh.txt');
            } 
            
            //$this->setLog('auto closing '.$row['code']. ' - ' .$row['refcode'],true,'auto-sh.txt');
            $salesOrder->changeStatus($row['pkey'], TRANSACTION_STATUS['selesai'], '',false, true); 
        }  
            
    }
    
    function addSalesOrderById($ordersnList){  
        
        if(!is_array($ordersnList)) $ordersnList = array($ordersnList);
        
        //$this->setLog($ordersnList,true,'auto-post-sh');
        
        $customer = new Customer();
        $salesOrder = new SalesOrder();
        $warehouse = new Warehouse();
        $customCode = new CustomCode();
        $item = new Item(); 
               
        $warehousekey = $warehouse->getDefaultData();
        
        // cek custom code
        $rsKey = $salesOrder->getTableKeyAndObj($salesOrder->tableName,array('key'));
        // $this->setLog($rsKey,true,'rskey.txt');
        $rsCustomCode = $customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'],true,' and '.$customCode->tableName.'.statuskey = 1');
        $customCodeKey = (empty($rsCustomCode)) ? 0 : $rsCustomCode[0]['pkey'];
        
        $customerkey = $this->rsMarketplace['customerkey'];
        $rsCustomer = $customer->getDataRowById($customerkey);
        $topkey = $rsCustomer[0]['termofpaymentkey'];
        $saleskey = $rsCustomer[0]['saleskey'];
        
        // collect all order sn, utk tau sales mana saja yg sudah masuk ke SO 
        $rsSalesCol = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey',$salesOrder->tableName.'.refcode'),
                                              ' and '.$salesOrder->tableName.'.refcode in ('.$salesOrder->oDbCon->paramString($ordersnList,',').') 
                                                and '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey)
                                             );
        
        $rsSalesCol = array_column($rsSalesCol,null,'refcode');  
        
        // pecah per 50 baris 
        $ordersnList = array_chunk($ordersnList, 50);
        //$this->setLog($ordersnList,true,'auto-post-sh');
        
        // get each order 
		$url = 'order/get_order_detail';
		
        $responseOpt = array("buyer_user_id","recipient_address","item_list","shipping_carrier");
     
        // $this->setLog('add order by id',true,'sh.txt');
     
        foreach($ordersnList as $orderIds){
            
            //$payload = $this->createJsonBodyV2($url, array('order_sn_list' => $orderIds));  
            $payload = $this->createJsonBodyV2($url, array('order_sn_list' => implode(',',$orderIds),
                                                           'response_optional_fields' =>implode(',', $responseOpt)
            )); 
            
            
            $orders = $this->execute($url,'GET', $payload); 
            //$this->setLog($orders,true,'auto-post-sh');
            $orders = json_decode($orders,true); 
			$orders = $orders['response'];
 
            foreach($orders['order_list'] as $order){
                // kalo sudah ada, jgn add 
                if(isset($rsSalesCol[$order['order_sn']])){
                    //$this->setLog($order['ordersn'],true,'duplicate-sh');    
                    continue;
                }

                $orderId = $order['order_sn'];
  
                $shipmentkey = $this->getShipmentDetailByName($order['shipping_carrier']);

                $orderDate =  date("d / m / Y H:i", $order['create_time']);   
                $recipientName = $order['recipient_address']['name'];
                $recipientAddress = $order['recipient_address']['full_address'];
                $recipientPhone = $order['recipient_address']['phone'];
                $trDesc = $order['message_to_seller'];

                // details
                $orderDetails = $order['item_list'];
 
                // =============  compile item
                // gk bisa selalu ambil dari item_sku
                // yg perlu dicek, kalo gk ad model_sku, apakah nilai defaultnya tetep sama dengan item_sku
                
                // $arrItemCode = array_column($orderDetails,'item_sku');
        
                $arrItemCode = array();
                foreach($orderDetails as $detailOrderRow){
                    $soldSKU = (!empty($detailOrderRow['model_sku'])) ? $detailOrderRow['model_sku'] : $detailOrderRow['item_sku'];
                    array_push($arrItemCode, $soldSKU);
                }


                $rsItemColl = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.code',$item->tableName.'.baseunitkey'),
                                                   ' and ('.$item->tableName.'.code in ('.$item->oDbCon->paramString($arrItemCode,',').'))'
                                                  );
                
                $itemColl = array_column($rsItemColl,null,'code');
                // =============  compile item

               try{ 
                    $this->oDbCon->startTrans(true);   

                    // PREPARE ARRAY  
                    $arrParam = array();
                    $arrParam['code'] = 'xxxxxx';
                    $arrParam['trDate'] = $orderDate;
                    $arrParam['selWarehouseKey'] = $warehousekey;
                    $arrParam['hidCustomerKey'] = $customerkey;
                    $arrParam['selStatus'] = 1;
                    $arrParam['selTermOfPaymentKey'] = $topkey; 
                    $arrParam['trDesc'] = '- Auto Push -'.chr(13).$trDesc;
                    $arrParam['chkIsFullDeliver'] = 1; 
                    $arrParam['selFinalDiscountType'] = 1;
                    $arrParam['finalDiscount'] = 0 ; // akan diupdate ulang dibawah
                    $arrParam['marketplaceKey'] = $this->marketplaceKey;
                    $arrParam['refCode'] = $orderId;
                    $arrParam['selCustomCode'] = $customCodeKey;
                    $arrParam['selShipmentService'] = $shipmentkey;
                    $arrParam['hidSalesKey'] = $saleskey; 
                    $arrParam['recipientName'] = $recipientName;
                    $arrParam['recipientPhone'] = $recipientPhone; 
                    $arrParam['recipientAddress'] = $recipientAddress;


                    $arrItemKey = array();
                    $warningNotifications = array(); 

                    $arrParam['hidDetailKey'] = array();
                    $arrParam['refMarketplaceKey'] = array();
                    $arrParam['hidItemKey'] = array();
                    $arrParam['selUnit'] = array();
                    $arrParam['priceInUnit'] = array();
                    $arrParam['priceInBaseUnit'] = array();
                    $arrParam['unitConvMultiplier'] = array();
                    $arrParam['qty'] = array();
                    $arrParam['qtyInBaseUnit'] = array();

                    $totalOrderDetails = count($orderDetails);

                    $subtotal = 0;
                    for($i=0;$i<$totalOrderDetails;$i++){  

                        $soldSKU = (!empty($orderDetails[$i]['model_sku'])) ? $orderDetails[$i]['model_sku'] : $orderDetails[$i]['item_sku'];
                        $indexCode = strval(trim($soldSKU));
                        
                        if(!isset($itemColl[$indexCode]['pkey'])) { 
                            array_push($warningNotifications, $indexCode. '. '. $salesOrder->errorMsg[213]);
                            continue;
                        }

                        $itemkey = $itemColl[$indexCode]['pkey'];
                        $baseunitkey = $itemColl[$indexCode]['baseunitkey'];

                        if (in_array($itemkey, $arrItemKey)){
                              for($j=0;$j<$i;$j++){ 
                                  if ($arrParam['hidItemKey'][$j] == $itemkey){ 
                                      $arrParam['qty'][$j]++;
                                      break;
                                  }
                              }

                            continue;
                        }

                        $priceInUnit = intval($orderDetails[$i]['model_discounted_price']);
                        $qty = intval($orderDetails[$i]['model_quantity_purchased']);

                        array_push($arrItemKey,$itemkey); 
                        array_push($arrParam['hidDetailKey'], 0); 
                        array_push($arrParam['hidItemKey'], $itemkey);
                        array_push($arrParam['selUnit'], $baseunitkey);
                        array_push($arrParam['priceInUnit'], $priceInUnit);
                        array_push($arrParam['priceInBaseUnit'], $priceInUnit);
                        array_push($arrParam['unitConvMultiplier'], 1);
                        array_push($arrParam['qty'],$qty ); 
                        array_push($arrParam['qtyInBaseUnit'], $qty);

                        $subtotal += ($qty * $priceInUnit);
                    }  

                    if(!empty($warningNotifications)){ 
                        $arrParam['_hasWarning_'] = true;

                        if (!empty($arrParam['trDesc'])) $arrParam['trDesc'] .= chr(13);
                        $arrParam['trDesc'] .= implode(chr(13),$warningNotifications);
                    }
                   
                    $result = $salesOrder->addData($arrParam);
                   
                    $this->oDbCon->endTrans();
                   
                } catch(Exception $e){
                    $this->oDbCon->rollback(); 
                }	 
                 
            }
            
        } 
    }
    
     function cancelSalesOrderById($orderId){
        //$this->setLog('cancel '.$orderId,true,'CANCEL-SH');    
        
        $salesOrder = new SalesOrder();

        //cek dulu sudah terdaftar blm
        $rsSalesOrder = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey', $salesOrder->tableName.'.statuskey'),
                                                    ' and ' .$salesOrder->tableName.'.refcode = ' .$this->oDbCon->paramString($orderId) .' 
                                                      and ' .$salesOrder->tableName.'.statuskey in (1,2,3)  
                                                      and '.$salesOrder->tableName.'.marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey)
                                                    );
         
        //$this->setLog($rsSalesOrder,true,'CANCEL-SH');  
         
        // kalo pake array nanti, perlu set ulang start transactionnya
        if(!empty($rsSalesOrder)) { 
            $orderId = $rsSalesOrder[0]['pkey']; 
            
            if( $rsSalesOrder[0]['statuskey'] == 1){ 
               //$this->setLog('cancel ',true,'CANCEL-SH');  
               $salesOrder->changeStatus($orderId, TRANSACTION_STATUS['batal'], '',false, true);    
            }else{  
               //$this->setLog('change tag ',true,'CANCEL-SH');  
               $salesOrder->changeTag($orderId, 1);
            }
        }
        
    }
    
    function updateLogistic(){
        // cari semua transaksi yg masih menunggu dan shipment service keyny 0
        $salesOrder = new SalesOrder();
        $shipment = new Shipment();
        
        //cek dulu yg blm ad shipment servicenya
        $rsSalesCol = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey',$salesOrder->tableName.'.refcode',$salesOrder->tableName.'.shipmentservicekey'),
                                                    ' and ' .$salesOrder->tableName.'.shipmentservicekey = 0 
                                                      and ' .$salesOrder->tableName.'.statuskey = 1
                                                      and '.$salesOrder->tableName.'.marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey)
                                                    );
         
        if(empty($rsSalesCol)) return;
        
        //$this->setLog($rsSalesCol,true,'logistic');
        
        $rsSales = array_column($rsSalesCol,null,'refcode'); 
         
        // pecah per 50 baris 
        $ordersnList = array_column($rsSalesCol,'refcode'); 
        $ordersnList = array_chunk($ordersnList, 50);
        
        $url = 'order/get_order_detail';
        
        // get each order
        // $this->setLog('update logistic',true,'sh.txt');
        
        foreach($ordersnList as $orderIds){
            
            $payload = $this->createJsonBodyV2($url,array('order_sn_list' => $orderIds)); 
            $orders = $this->execute($url,'GET', $payload);  
            $orders = json_decode($orders,true)['response']; 
            
            
            foreach($orders['order_list'] as $order){
                $orderId = $order['order_sn']; 

                // update logistic yg blm keupdate
                if(empty($rsSales[$orderId]['shipmentservicekey'])){
                     try{

                            if(!$this->oDbCon->startTrans(true))
                                throw new Exception($this->errorMsg[100]);

                            $shipmentkey = $this->getShipmentDetailByName($order['shipping_carrier']);

                            $rsService = $shipment->getServices('',$shipmentkey);
                            $sql = 'update '.$this->tableSalesOrder.' 
                                    set 
                                        shipmentkey = '.$this->oDbCon->paramString($rsService[0]['refkey']).', 
                                        shipmentservicekey =  '.$this->oDbCon->paramString($shipmentkey).'
                                    where pkey = '.$this->oDbCon->paramString($rsSales[$orderId]['pkey']);

                            //$this->setLog($sql,true,'logistic');
                            $this->oDbCon->execute($sql);
 
                            $this->oDbCon->endTrans();
                        }catch(Exception $e){
                            $this->oDbCon->rollback(); 
                        }	 
                }

            }
        }
    }
	
	function updateStorefront($arrStorefrontKey){
         // kalo blm ad marketplacestorefrontkey
        // add baru
        $storefront = new Storefront();
        $rsStorefront = $storefront->searchDataRow(array($storefront->tableName.'.pkey', $storefront->tableName.'.name', $storefront->tableName.'.marketplacestorefrontkey'),
                                   ' and '.$storefront->tableName.'.marketplacekey = ' . $this->marketplaceKey .' and '.$storefront->tableName.'.pkey in ('.$this->oDbCon->paramString($arrStorefrontKey,',').')'
                                  );
             
        foreach($rsStorefront as $row){ 
            
            if(empty($row['marketplacestorefrontkey'])){
                // push
                // update key 
  				$url = 'shop_category/add_shop_category';
				
				$payload = $this->createJsonBodyV2($url,array('name' => $row['name'] )); 
				$result = $this->execute($url,'POST', $payload);  
				$result = json_decode($result,true);
 				$result = $result['response'];
				
                if (!empty($result['shop_category_id'])){
                    try{ 

                        if(!$this->oDbCon->startTrans())
                            throw new Exception($this->errorMsg[100]);

                          $sql = 'update '.$storefront->tableName.' 
                            set marketplacestorefrontkey = '.$this->oDbCon->paramString($result['shop_category_id']).' 
                            where '.$storefront->tableName.'.pkey = ' . $this->oDbCon->paramString($row['pkey']); 
                    
                        $this->oDbCon->execute($sql);
                        $this->oDbCon->endTrans();  

                    } catch(Exception $e){
                        $this->oDbCon->rollback(); 
                    }		
 
                } 

            }else{
                // update  
				$url = 'shop_category/update_shop_category';
				$payload = $this->createJsonBodyV2( $url, array("shop_category_id" => intval($row['marketplacestorefrontkey']), "name" => $row['name'] ) ); 
				$response = $this->execute($url,'POST', $payload);  
				//$response = json_decode($response,true);
            }
        } 
    }
	
	function deleteStorefront($storefrontkey){
		$url = 'shop_category/delete_shop_category';
		$payload = $this->createJsonBodyV2( $url, array("shop_category_id" => intval($storefrontkey)) ); 
		$response = $this->execute($url, 'POST', $payload);  
		$response = json_decode($response,true); 
	}
	
	function getStorefront(){
		
		$url = 'shop_category/get_shop_category_list';
		
		$orderPerPage = 100;
        $offset = 1;
        $nextPage = true;
        $shopCategories = array();
        
        do{ 
            $payload = $this->createJsonBodyV2($url,array( 
                'page_size' => $orderPerPage,
                'page_no' => $offset, 
            ));
 
            $response = $this->execute($url, 'GET', $payload);
            $response = json_decode($response,true); 
            $response = $response['response'];
             
            if(isset($response['shop_categorys']) && !empty($response['shop_categorys']))
                $shopCategories = array_merge($shopCategories,$response['shop_categorys']);
            
            $offset++; 
			
            if (!$response['more'])  $nextPage = false; 	 
			
        }while($nextPage);
		
        return $shopCategories;
    }
	
	function syncMarketplaceStorefront($syncType = ''){
        $itemCategory = new ItemCategory();
        
        $result = $this->getStorefront();
        $storeFrontKey = array_column($result,'shop_category_id');
         
        $existingStorefront = $this->getMarketplaceStorefront('',$this->marketplaceKey);  
        $existingStorefront = array_column($existingStorefront,'marketplacestorefrontkey');
        
        try{ 
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	 
             foreach($result as $row){
                 
                // kalo blm ad
                $storefrontkey = $row['shop_category_id'];
                $storefrontname = $row['name'];

                if(!in_array($storefrontkey, $existingStorefront)){ 
                    $sql = 'insert into  '.$this->tableMarketplaceStorefront.' (marketplacestorefrontkey,marketplacekey,name,statuskey,isleaf) 
                            values ('.$this->oDbCon->paramString($storefrontkey).', '.$this->oDbCon->paramString($this->marketplaceKey).', '.$this->oDbCon->paramString($storefrontname).',1 ,1) ';

                // kalo ud ad, update
                }else { 
                    $sql = 'update  '.$this->tableMarketplaceStorefront.' 
                            set name = '.$this->oDbCon->paramString($storefrontname).'
                            where 
								marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey).' and
								marketplacestorefrontkey = '.$this->oDbCon->paramString($storefrontkey);
                 
                }
                $this->oDbCon->execute($sql);
                  
                // kalo gk ad,delete
                $sql = 'delete from '.$this->tableMarketplaceStorefront.' where marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey).' and marketplacestorefrontkey not in ('.$this->oDbCon->paramString($storeFrontKey,',').')';
                $this->oDbCon->execute($sql);

            }

			$this->oDbCon->endTrans(); 
             
		
	    } catch(Exception $e){
			$this->oDbCon->rollback(); 
		}		
        
        
    }
    
	function boostItem($arrItemKey = array()){ 
		
		$url = 'product/boost_item';
		
		$item = new Item();
		
		// cari item dengan stok terbanyak  
		$rsItem = $item->getItemForBoost($this->marketplaceKey); 
		 
		$arrItem = array();
		foreach($rsItem as $row)
			array_push($arrItem,intval($row['marketplaceitemkey']));
		 
		$payload = $this->createJsonBodyV2($url,array( 
			'item_id_list' => $arrItem, 
		));

		$response = $this->execute($url,'POST', $payload);
		$response = json_decode($response,true);
		 
		//$this->setLog($response,true,'boost-item-sh');
		
	}
	
	function getAvailableMarketplaceLogisticsForItem(){ 
        $dbCon = $this->masterConn(); 
		
		$arrLogistics = array();
		 
		$sql = 'select 
					'.$this->tableMarketplaceLogistics. '.pkey,
					'.$this->tableMarketplaceLogistics. '.name,
					'.$this->tableMarketplaceLogistics. '.logisticid ,
					'.$this->tableMarketplaceLogistics. '.maskchannelid 
				from '.$this->tableMarketplaceLogistics. '
				where 	'.$this->tableMarketplaceLogistics. '.statuskey = 1 and
						'.$this->tableMarketplaceLogistics. '.marketplacekey = ' . $this->marketplaceProviderKey.' and
						'.$this->tableMarketplaceLogistics. '.logisticid in (
						select '.$this->tableMarketplaceLogistics.'.maskchannelid 
						from '.$this->tableMarketplaceLogistics. ' where '.$this->tableMarketplaceLogistics. '.marketplacekey = ' . $this->marketplaceProviderKey.')
						
				UNION ALL
				
				select 
					'.$this->tableMarketplaceLogistics. '.pkey,
					'.$this->tableMarketplaceLogistics. '.name,
					'.$this->tableMarketplaceLogistics. '.logisticid ,
					'.$this->tableMarketplaceLogistics. '.maskchannelid 
				from '.$this->tableMarketplaceLogistics. '
				where '.$this->tableMarketplaceLogistics. '.statuskey = 1 and
					  '.$this->tableMarketplaceLogistics. '.marketplacekey = ' . $this->marketplaceProviderKey.' and
					  '.$this->tableMarketplaceLogistics.'.maskchannelid = 0 and
					  '.$this->tableMarketplaceLogistics. '.logisticid not in (
							select '.$this->tableMarketplaceLogistics.'.maskchannelid 
							from '.$this->tableMarketplaceLogistics. ' where '.$this->tableMarketplaceLogistics. '.marketplacekey = ' . $this->marketplaceProviderKey.'
						)';
         
		$rs = $dbCon->doQuery($sql);
		 
        $dbCon = null;
        return $rs;
    } 
	  
    function getMarketplaceCategoryVariant($marketplacecategorykey, $parentkey = '', $criteria = ''){ 
		// ambil variant dari item parent
		// untuk shopee, harusny wajib ad parent
		
		$item = new Item();
		$rsVariantValue = $item->getVariantValueForMarketplace($parentkey,$this->marketplaceProviderKey);
		return $rsVariantValue;
	}
      
    function uploadToMediaSpace($imgPath){
        // $imgPath : doc path
        
//        $this->setLog($imgPath,true,'sh.txt');
        
        $curl = curl_init();
        
        $apiurl = 'media_space/upload_image'; 
        $apiurl =  $this->url.$apiurl.'?'.$this->getCommonQuery($apiurl); 
   
        curl_setopt_array($curl, array(
            CURLOPT_URL => $apiurl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST', 
            CURLOPT_POSTFIELDS => array('image'=> new CURLFILE($imgPath)),
        ));

        $imgResponse = curl_exec($curl);  
        curl_close($curl); 
        
        $imgResponse = json_decode($imgResponse,true);
        
        return (isset($imgResponse['response']['image_info']['image_id'])) ? $imgResponse['response']['image_info']['image_id'] : '';
    }
}
 
class Lazada extends Marketplace{
    
   function __construct($marketplaceKey = ''){
		
		parent::__construct();
		   
        // default value, overwrite if needed
  
        $this->url  = 'https://api.lazada.co.id/rest';
        $this->callbackURL = 'https://www.wintera.co.id/lazada-callback/';
        $this->authURL = 'https://auth.lazada.com/oauth/authorize?response_type=code&force_auth=true&redirect_uri='.$this->callbackURL.urlencode(DOMAIN_NAME).'/&client_id='.$this->appKey; 
        
	    // sementara pake app pertama dulu
	    $selectedApp = 0; 
        $this->appKey  = LAZADA_CONFIG[$selectedApp]['appKey'];
        $this->secretKey  = LAZADA_CONFIG[$selectedApp]['secretKey'];  
	   
	   	$this->marketplaceProviderKey = MARKETPLACE['lazada'];
        $this->initMarketplace($this->marketplaceProviderKey,$marketplaceKey);
 
	}
	  
    
    function importOrders(){
        // lazada
        
        $item = new Item();
        $customer = new Customer();
        $salesOrder = new SalesOrder();
        $warehouse = new Warehouse();
        $customCode = new CustomCode();
        
        // cek custom code
        $rsKey = $salesOrder->getTableKeyAndObj($salesOrder->tableName,array('key'));
        $rsCustomCode = $customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'],true,' and '.$customCode->tableName.'.statuskey = 1');
        $customCodeKey = (empty($rsCustomCode)) ? 0 : $rsCustomCode[0]['pkey'];
        
        $today = (!empty($this->backdateInterval)) ? date('Y-m-d 00:00:00', strtotime($this->backdateInterval) ) : date('Y-m-d 00:00:00');
         
        $warehousekey = $warehouse->getDefaultData(); 
        
        $customerkey = $this->rsMarketplace['customerkey'];
        $rsCustomer = $customer->getDataRowById($customerkey);
        $topkey = $rsCustomer[0]['termofpaymentkey'];
        $saleskey =  $rsCustomer[0]['saleskey'];
        
        $timestamp = new DateTime($today);  
        $createdDateAfter = $timestamp->format(DateTime::ISO8601);  

        // setting criteria ..
        $request = new LazopRequest('/orders/get','GET'); 
        $request->addApiParam('created_after',$createdDateAfter); 
        $request->addApiParam('status','pending'); 
        $request->addApiParam('sort_direction','ASC');
        $request->addApiParam('offset','0');
        $request->addApiParam('limit','100');
        $request->addApiParam('sort_by','updated_at');


        // execute
        $response = $this->execute($request);
        //$response = $this->client->execute($request, $this->accessToken);
         
        $result = json_decode($response,true);

        $result = $result['data'];

        $orders  =  $result['orders']; 

        //$this->setLog('Today\'s Order');
 
        $arrOrderQueue = array(); 
        foreach ($orders as $order){ 
            $orderId = $order['order_id']; 
            
            $arrRecipientName = array();
            if(!empty($order['customer_first_name'])) array_push($arrRecipientName, $order['customer_first_name']);
            if(!empty($order['customer_last_name'])) array_push($arrRecipientName, $order['customer_last_name']); 
            $recipientName = implode (' ',$arrRecipientName);
            $recipientAddress = $order['address_billing']['address1'];
            $recipientPhone = $order['address_billing']['phone'];
            $recipientEmail = '';
            
            //$rsSales = $salesOrder->searchData('','',true, ' and '.$salesOrder->tableName.'.refcode = '.$salesOrder->oDbCon->paramString($orderId).' and  '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey));
            $rsSales = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey'),
                                                   ' and '.$salesOrder->tableName.'.refcode = '.$salesOrder->oDbCon->paramString($orderId).' 
                                                     and  '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey) 
            );
            
            if(!empty($rsSales)) continue;
            
            $orderDate =  date("d / m / Y H:i", strtotime($order['created_at']));

            // setting criteria ..
            $request = new LazopRequest('/order/items/get','GET'); 
            $request->addApiParam('order_id',$orderId);

            //$this->setLog('Starting Process : '.$orderId);

            // execute
            $response = $this->execute($request);
            //$response = $this->client->execute($request, $this->accessToken);
            $result = json_decode($response,true);
            $orderDetails = $result['data'];

            // =============  compile item
            $arrItemCode = array();
            for($i=0;$i<count($orderDetails);$i++)
                array_push($arrItemCode, $orderDetails[$i]['sku']);

            $rsItemColl = $item->searchData('','',true, ' and ('.$item->tableName.'.code in ('.$item->oDbCon->paramString($arrItemCode,',').'))');
            $itemColl = array_column($rsItemColl,null,'code');
            // =============  compile item

 
            // PREPARE ARRAY   
            $arrParam = array();
            $arrParam['code'] = 'xxxxxx';
            $arrParam['trDate'] = $orderDate;
            $arrParam['selWarehouseKey'] = $warehousekey;
            $arrParam['hidCustomerKey'] = $customerkey;
            $arrParam['selStatus'] = 1;
            $arrParam['selTermOfPaymentKey'] = $topkey;
            $arrParam['trDesc'] = $order['remarks'];
            $arrParam['chkIsFullDeliver'] = 1; 
            $arrParam['selFinalDiscountType'] = 1;
            $arrParam['finalDiscount'] = 0 ;
            $arrParam['marketplaceKey'] = $this->marketplaceKey;
            $arrParam['refCode'] = $orderId;
            $arrParam['selCustomCode'] = $customCodeKey;
            $arrParam['hidSalesKey'] = $saleskey; 
             
            $arrParam['recipientName'] = $recipientName;
            $arrParam['recipientPhone'] = $recipientPhone;
            $arrParam['recipientEmail'] = $recipientEmail;
            $arrParam['recipientAddress'] = $recipientAddress;
             
            $arrItemKey = array();

            $warningNotifications = array();
            
            $arrParam['hidDetailKey'] = array();
            $arrParam['refMarketplaceKey'] = array();
            $arrParam['hidItemKey'] = array();
            $arrParam['selUnit'] = array();
            $arrParam['priceInUnit'] = array();
            $arrParam['priceInBaseUnit'] = array();
            $arrParam['unitConvMultiplier'] = array();
            $arrParam['qty'] = array();
            $arrParam['qtyInBaseUnit'] = array();
            
            for($i=0;$i<count($orderDetails);$i++){  
                $detailrefcode = $orderDetails[$i]['order_item_id'];
                    
                if(!isset($itemColl[$orderDetails[$i]['sku']]['pkey'])) {
                    array_push($warningNotifications, $orderDetails[$i]['sku']. '. '. $salesOrder->errorMsg[213]);
                    continue;
                }

                $itemkey = $itemColl[$orderDetails[$i]['sku']]['pkey'];
                $baseunitkey = $itemColl[$orderDetails[$i]['sku']]['baseunitkey'];

               /*
                // gk boleh digabung karena masing2 ad shipment key nya
                
                if (in_array($itemkey, $arrItemKey)){
                      for($j=0;$j<$i;$j++){ 
                          if ($arrParam['hidItemKey'][$j] == $itemkey){ 
                              $arrParam['qty'][$j]++;
                              break;
                          }
                      }

                    continue;
                }*/

                array_push($arrItemKey,$itemkey ); 
                array_push($arrParam['hidDetailKey'], 0);
                array_push($arrParam['refMarketplaceKey'], $detailrefcode);
                array_push($arrParam['hidItemKey'], $itemkey);
                array_push($arrParam['selUnit'], $baseunitkey);
                array_push($arrParam['priceInUnit'], intval($orderDetails[$i]['item_price']));
                array_push($arrParam['priceInBaseUnit'], intval($orderDetails[$i]['item_price']));
                array_push($arrParam['unitConvMultiplier'], 1);
                array_push($arrParam['qty'], 1);
                array_push($arrParam['qtyInBaseUnit'], 1);
                
                /*
                $arrParam['hidDetailKey'][$i] = 0;
                $arrParam['refMarketplaceKey'][$i] = $detailrefcode; 
                $arrParam['hidItemKey'][$i] = $itemkey; 
                $arrParam['selUnit'][$i] = $baseunitkey;
                $arrParam['priceInUnit'][$i] = intval($orderDetails[$i]['item_price']);
                $arrParam['priceInBaseUnit'][$i] = intval($orderDetails[$i]['item_price']);
                $arrParam['unitConvMultiplier'][$i] = 1; 
                $arrParam['qty'][$i] = 1;
                $arrParam['qtyInBaseUnit'][$i] = 1;
                */

            }

            if(!empty($warningNotifications)){ 
                $arrParam['_hasWarning_'] = true;
                
                if (!empty($arrParam['trDesc'])) $arrParam['trDesc'] .= chr(13);
                $arrParam['trDesc'] .= implode(chr(13),$warningNotifications);
            }

            array_push($arrOrderQueue,$arrParam);
            
        }
        
        // keluarin dulu yg sudah ada sales ordernya, agar mengurangi kerjaan validateForm
        $arrRefCode = array_column($arrOrderQueue,'refCode');
        //$rsSales = $salesOrder->searchData('','',true, ' and '.$salesOrder->tableName.'.refcode in ('.$salesOrder->oDbCon->paramString($arrRefCode,',').') and  '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey));
        $rsSales =  $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey', $salesOrder->tableName.'.refcode'),
                                                   ' and '.$salesOrder->tableName.'.refcode in ('.$salesOrder->oDbCon->paramString($arrRefCode,',').') 
                                                     and  '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey)
        );
        $rsSales =  array_column($rsSales,'refcode');
            
        foreach($arrOrderQueue as $arrParam){
            
            if(in_array($arrParam['refCode'],$rsSales)) continue;
            if (!isset($arrParam['hidItemKey']) || empty($arrParam['hidItemKey'])) continue;
  
            //$this->setLog("--- Saving ". $arrParam['refCode'] ." " . date('d / m / Y H:i:s'),true,'mp'); 
            
            try{
	
                if(!$this->oDbCon->startTrans(true))
                    throw new Exception($this->errorMsg[100]);

                  $arrayToJs = $salesOrder->addData($arrParam); 

                  if(!$arrayToJs[0]['valid'])
                    throw new Exception( $arrayToJs[0]['message'] );

                   /*if (isset($arrParam['_hasWarning_']) && !empty($arrParam['_hasWarning_'])){
                        $sql = 'update ' . $salesOrder->tableName.' set tagkey = 1 where pkey = ' . $item->oDbCon->paramString($arrayToJs[0]['data']['pkey']);
                    }*/ 

                  $this->oDbCon->endTrans();
            }catch(Exception $e){
                $this->oDbCon->rollback();
                $this->addErrorList($arrayToJs,false,$e->getMessage());
            }	
 
            
        
            /*if (isset($arrParam['_hasWarning_']) && !empty($arrParam['_hasWarning_'])){
                $sql = 'update ' . $salesOrder->tableName.' set tagkey = 1 where pkey = ' . $item->oDbCon->paramString($arrayToJs[0]['data']['pkey']);
            }*/
            
        }

        //$this->setLog ('done');
    } 

    function closeCompletedOrders(){ 
         
        
        $completedStatus = array("delivered", "shipped");
        
        $salesOrder = new SalesOrder(); 
        
        $ordersnList = array();
        //$rsSalesOrder = $salesOrder->searchData(', $this->marketplaceKey,true, ' and refcode <> "" and '.$salesOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].') ' );
        $rsSalesOrder = $salesOrder->searchDataRow(array($salesOrder->tableName.'.pkey', $salesOrder->tableName.'.refcode'),
                                                  ' and '.$salesOrder->tableName.'.refcode <> \'\'
                                                    and '.$salesOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].') 
                                                    and '.$salesOrder->tableName.'.marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceKey)
                                                   );
        
        $refcode = array_column($rsSalesOrder,'refcode'); 
        $refcode = json_encode($refcode); 
        
        $rsByRefCode = array_column($rsSalesOrder,'pkey','refcode'); 
         
          
         // setting criteria ..
        $request = new LazopRequest('/orders/items/get','GET');   
        $request->addApiParam('order_ids',$refcode);
        $response = $this->execute($request);

        $result = json_decode($response,true);
        $result = $result['data'];
        
        foreach($result as $row){
            
            $orderItems = $row['order_items'];
            //$this->setLog($orderItems,true,'lz');
            $completed = false;
            
            foreach($orderItems as $itemRow){ 
                if (in_array(strtolower($itemRow['status']),$completedStatus) ) { 
                    $completed = true;
                    break;
                } 
            }
         
            if (!$completed) continue;

            $sokey = $rsByRefCode[$row['order_id']]; 
            
            //$this->setLog($sokey, true, 'lz-closed');
            $salesOrder->changeStatus($sokey, TRANSACTION_STATUS['selesai'], '',false, true);
        }
         
         
    }
  
     function cancelCanceledOrders(){ 
         
        // lazada
         
        $canceledStatus = array("canceled");
        
        $salesOrder = new SalesOrder(); 
        
        $ordersnList = array();
        //$rsSalesOrder = $salesOrder->searchData($salesOrder->tableName.'.marketplacekey', $this->marketplaceKey,true, ' and refcode <> "" and '.$salesOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].') ' );
        
        $rsSalesOrder = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey', $salesOrder->tableName.'.refcode'),
                                                  ' and '.$salesOrder->tableName.'.refcode <> \'\'
                                                    and '.$salesOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].') 
                                                    and '.$salesOrder->tableName.'.marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceKey)
                                                  
                        );
            
        $refcode = array_column($rsSalesOrder,'refcode'); 
        $refcode = json_encode($refcode); 
        
        $rsByRefCode = array_column($rsSalesOrder,'pkey','refcode'); 
         
          
         // setting criteria ..
        $request = new LazopRequest('/orders/items/get','GET');   
        $request->addApiParam('order_ids',$refcode);
        $response = $this->execute($request);

        $result = json_decode($response,true);
        $result = $result['data'];
        
        foreach($result as $row){
            
            $orderItems = $row['order_items'];
            $canceled = false;
            
            foreach($orderItems as $itemRow){ 
                if (in_array(strtolower($itemRow['status']),$canceledStatus) ) { 
                    $canceled = true;
                    break;
                } 
            }
         
            if (!$canceled) continue;

            $sokey = $rsByRefCode[$row['order_id']];
            $salesOrder->changeStatus($sokey, TRANSACTION_STATUS['batal'], '',false, true);
        }
       

    }
  
    
    function updateProduct($itemkey,$rsItemColl = array(),$syncCriteria = array()){
            // lazada
        
            // $rsItemColl digunakan agar tdk query data item berulang2
        
            $item = new Item();
            $itemMovement = new ItemMovement();
        
            $warehousekey = ''; // ini diudpate jika punya gudang khusus utk marketplace
         
            if(!empty($rsItemColl)){ 
                $rsItem = $rsItemColl[$itemkey];
            }else{ 
                $rsItem = $item->getDataRowById($itemkey); 
                $rsItem = $rsItem[0];
            }
        
            //parameter need to be sync
            $code = $rsItem['code']; 
		
        	$arrItemMPInformation = $this->getItemInformationForMarketplace($itemkey)[0];
		
            $mpName = $arrItemMPInformation['name'];
            $name = (!empty($mpName)) ? $mpName : $rsItem['name']; // cek ke settingan ad overwrite nama gk
            
            // ============================================================ MAIN ATTRIBUTES
            $arrMainAttributes = array(); 
        
            // NAME attr
            if (in_array('name', $syncCriteria['attr']))
                $arrMainAttributes['name']  = $name;
             
            if (in_array('shortDescription', $syncCriteria['attr'])){ 
             	$mpDesc = $arrItemMPInformation['shortdescription'];
				$mpDesc = (!empty($mpDesc)) ? $mpDesc : $rsItem['shortdescription'];
				
				$arrMainAttributes['short_description']  = $mpDesc; 
			}
            
            if (in_array('others', $syncCriteria['attr']))
                $arrMainAttributes['model'] = $name; // $rsItem['shortdescription']; 
        
            // BRAND attr
            if (in_array('brand', $syncCriteria['attr'])){
                $rsBrand = $this->getBrandUsedForMarketplace($rsItem['brandkey']); 
                $arrMainAttributes['brand'] = ucwords($rsBrand[0]['name']); 
            }  
            
            // ============================================================ SKU 
        
            $arrSKU = array();

            $arrSKUDetails = array();
            $arrSKUDetails['SellerSku'] = $code;
 
            // QOH attr
        
            // temp defaults...
            $arrSKUDetails['Status'] = 'active'; 
        
            if (in_array('qoh', $syncCriteria['attr'])){ 
                $qoh = intval($itemMovement->getItemQOH($itemkey,$warehousekey));  
                
                if ($qoh <= 0){
                     $arrSKUDetails['quantity'] = 0;
                     $arrSKUDetails['Status'] = 'inactive'; 
                }else{
                     $arrSKUDetails['quantity'] = $qoh;
                     $arrSKUDetails['Status'] = 'active'; 
                } 
            }
		
			// overwrite status, kalo dikirim
		 	if (in_array('status', $syncCriteria['attr'])){ 
                $arrSKUDetails['Status'] = ($rsItem['statuskey'] == 1) ? 'active' : 'inactive';  
            }
              
            // MEASUREMENT
            if (in_array('measurement', $syncCriteria['attr'])){ 
                $arrSKUDetails['package_weight'] = $rsItem['gramasi'];
                if ($rsItem['weightunitkey'] == UNIT['gram'])
                    $arrSKUDetails['package_weight'] /= 1000;

                $arrSKUDetails['package_weight'] = ($arrSKUDetails['package_weight'] <= 0) ? 1 : $arrSKUDetails['package_weight']; // default 1 kg
                $arrSKUDetails['package_width'] = (!isset($rsItem['width']) || $rsItem['width'] <= 0 ) ? 1 : $rsItem['width'];
                $arrSKUDetails['package_length'] = (!isset($rsItem['length']) || $rsItem['length'] <= 0 ) ? 1 : $rsItem['length'];
                $arrSKUDetails['package_height'] = (!isset($rsItem['height']) || $rsItem['height'] <= 0 ) ? 1 : $rsItem['height'];
            }
           
            // PRICE attr
            if (in_array('price', $syncCriteria['attr'])){ 
                 
                if(!empty($this->rsMarketplace['priceadjustment'])){
                    $priceAdjustment = $this->rsMarketplace['priceadjustment'];
                    $priceAdjustmentType = $this->rsMarketplace['priceadjustmenttype'];

                    $priceAdjustment = ($priceAdjustmentType == 2 ) ? ($rsItem['sellingprice'] * $priceAdjustmentType/100) : $priceAdjustment;
                    $rsItem['sellingprice'] += $priceAdjustment;
                    $rsItem['sellingprice']  = ceil($rsItem['sellingprice']/1000) * 1000;
                }

                $arrSKUDetails['price'] = intval($rsItem['sellingprice']);


                if(!empty($this->rsMarketplace['margin'])){
                    $margin = $this->rsMarketplace['margin'];
                    $marginType = $this->rsMarketplace['margintype'];

                    $margin = ($marginType == 2 ) ? ($arrSKUDetails['price'] * $margin/100) : $margin;
                    $arrSKUDetails['price'] += $margin;
                    $arrSKUDetails['price'] = intval($arrSKUDetails['price']); 

                }

                // CAMPAIGN
                $now = date('Y-m-d');
                $campaignDateBegin = date('Y-m-d', strtotime($this->rsMarketplace['campaignstartdate']));
                $campaignDateEnd = date('Y-m-d', strtotime($this->rsMarketplace['campaignenddate']));

                if (($now >= $campaignDateBegin) && ($now <= $campaignDateEnd)){  
                    // kalo tipenya harga final hitung balik ulang
                    $priceType = $this->rsMarketplace['finalpricetype'];

                    if($priceType == 1){
                        $arrSKUDetails['special_price'] = intval($rsItem['sellingprice']);  
                        $arrSKUDetails['special_from_date'] = $campaignDateBegin;
                        $arrSKUDetails['special_to_date'] = $campaignDateEnd;
                    }else if($priceType == 2 && !empty($this->rsMarketplace['discount'])){
                        $discount = $this->rsMarketplace['discount'];
                        $discountType = $this->rsMarketplace['discounttype'];

                        $discount = ($discountType == 2 ) ? ($arrSKUDetails['price'] * $discount/100) : $discount;
                        $arrSKUDetails['special_price'] = intval($arrSKUDetails['price']  - $discount); 
                        $arrSKUDetails['special_from_date'] = $campaignDateBegin;
                        $arrSKUDetails['special_to_date'] = $campaignDateEnd;
                    }   
                } 

            }
         
            // IMAGE attr
            if (in_array('image', $syncCriteria['attr'])){ 
                $arrImg = array();
                $rsItemImage = $item->getItemImage($itemkey);
                foreach($rsItemImage as $img){   
                    $uploadedImg = $this->getCacheImageForMarketplace($itemkey,$img['file']); 
                    if(!empty($uploadedImg))
                        array_push($arrImg, $uploadedImg); 
                }

                if(!empty($arrImg)){ 
                    $arrSKUDetails['Images'] = array();
                    foreach($arrImg as $imgRow)
                        array_push($arrSKUDetails['Images'],array('Image' => $imgRow));
                }  
            } 
                
            // OTHERS ATTRIBUTE  
        
            if (in_array('others', $syncCriteria['attr'])){ 
                $rsAttributes = $item->getMarketplaceCategoryAttributes($itemkey, $this->marketplaceKey); 
                foreach($rsAttributes as $attributeRow)
                    $arrSKUDetails[$attributeRow['attributekey']] = $attributeRow['value'];
            }
 
            $arrSKU['sku'] = $arrSKUDetails;

            // ============================================================ SKU 
              
            $arrParam = array(); 
        
            // CATEGORY attr
            $rsCategory = $this->getCategoryUsedForMarketplace($rsItem['categorykey']);
            $arrParam['Product']['PrimaryCategory']  = $rsCategory[0]['marketplacecategorykey'];   
        
            $arrParam['Product']['Attributes'] = $arrMainAttributes;
            $arrParam['Product']['Skus'] = $arrSKU; 

            $payloadXML = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><Request></Request>");
            $this->arrayToXML($arrParam,$payloadXML);
        
            $payloadXML = $payloadXML->asXML();
        
        
            // update
            $request = new LazopRequest('/product/update');
            $request->addApiParam('payload',$payloadXML);
            $result = $this->execute($request, array('actionkey' => $this->actionType['updateProduct'], 'ref' => $name ));  
        
    }
    
    function createProduct($itemkey){
         // lazada
		
        $item = new Item();
        $itemMovement = new ItemMovement();
        
        $warehousekey = ''; // ini diudpate jika punya gudang khusus utk marketplace 
        
        //$this->setLog('add new products');
        
        $rsItem = $item->getDataRowById($itemkey);
        if (empty($rsItem)) return;
        
        $rsItem = $rsItem[0];
        
        //parameter need to be sync
        $code = $rsItem['code']; 
		
		$arrItemMPInformation = $this->getItemInformationForMarketplace($itemkey)[0]; 
		$mpName = $arrItemMPInformation['name'];
		
        $name = (!empty($mpName)) ? $mpName : $rsItem['name']; // cek ke settingan ad overwrite nama gk
           
         
        // ============================================================ MAIN ATTRIBUTES
        $arrMainAttributes = array(); 
        $arrMainAttributes['name']  = $name;           

		$mpDesc = $arrItemMPInformation['shortdescription'];
		$mpDesc = (!empty($mpDesc)) ? $mpDesc : $rsItem['shortdescription'];
        $arrMainAttributes['short_description']  = $mpDesc;  
        $arrMainAttributes['model'] = $name; // $rsItem['shortdescription']; 
         
        // kalo gk ad link brand,  pake Tidak Ada Merk
        $rsBrand = $this->getBrandUsedForMarketplace($rsItem['brandkey']); 
        $arrMainAttributes['brand'] = ucwords($rsBrand[0]['name']); 
        
        // ============================================================ MAIN ATTRIBUTES
        
        // ============================================================ SKU 
          
        $arrSKU = array();
        
        $arrSKUDetails = array();
        $arrSKUDetails['SellerSku'] = $code;
 
        // QOH attr 
        $qoh = intval($itemMovement->getItemQOH($itemkey,$warehousekey));  
        $arrSKUDetails['quantity'] = ($qoh < 0) ? 0 : $qoh; // utk precaution  
         
        // MEASUREMENT
        $arrSKUDetails['package_weight'] = $rsItem['gramasi'];
        if ($rsItem['weightunitkey'] == UNIT['gram'])
            $arrSKUDetails['package_weight'] /= 1000;
  
        $arrSKUDetails['package_weight'] = ($arrSKUDetails['package_weight'] <= 0) ? 1 : $arrSKUDetails['package_weight']; // default 1 kg
        $arrSKUDetails['package_width'] = (!isset($rsItem['width']) || $rsItem['width'] <= 0 ) ? 1 : $rsItem['width'];
        $arrSKUDetails['package_length'] = (!isset($rsItem['length']) || $rsItem['length'] <= 0 ) ? 1 : $rsItem['length'];
        $arrSKUDetails['package_height'] = (!isset($rsItem['height']) || $rsItem['height'] <= 0 ) ? 1 : $rsItem['height'];
 
        // PRICE
        if(!empty($this->rsMarketplace['priceadjustment'])){
            $priceAdjustment = $this->rsMarketplace['priceadjustment'];
            $priceAdjustmentType = $this->rsMarketplace['priceadjustmenttype'];

            $priceAdjustment = ($priceAdjustmentType == 2 ) ? ($rsItem['sellingprice'] * $priceAdjustmentType/100) : $priceAdjustment;
            $rsItem['sellingprice'] += $priceAdjustment;
            $rsItem['sellingprice']  = ceil($rsItem['sellingprice']/1000) * 1000;
        }
        
        $arrSKUDetails['price'] = intval($rsItem['sellingprice']);
         

        if(!empty($this->rsMarketplace['margin'])){
            $margin = $this->rsMarketplace['margin'];
            $marginType = $this->rsMarketplace['margintype'];
            
            $margin = ($marginType == 2 ) ? ($arrSKUDetails['price'] * $margin/100) : $margin;
            $arrSKUDetails['price'] += $margin;
            $arrSKUDetails['price'] = intval($arrSKUDetails['price']); 
            
        }
        
        // CAMPAIGN
        $now = date('Y-m-d');
        $campaignDateBegin = date('Y-m-d', strtotime($this->rsMarketplace['campaignstartdate']));
        $campaignDateEnd = date('Y-m-d', strtotime($this->rsMarketplace['campaignenddate']));
        
        if (($now >= $campaignDateBegin) && ($now <= $campaignDateEnd)){  
            // kalo tipenya harga final hitung balik ulang
            $priceType = $this->rsMarketplace['finalpricetype'];

            if($priceType == 1){
                $arrSKUDetails['special_price'] = intval($rsItem['sellingprice']);  
                $arrSKUDetails['special_from_date'] = $campaignDateBegin;
                $arrSKUDetails['special_to_date'] = $campaignDateEnd;
            }else if($priceType == 2 && !empty($this->rsMarketplace['discount'])){
                $discount = $this->rsMarketplace['discount'];
                $discountType = $this->rsMarketplace['discounttype'];

                $discount = ($discountType == 2 ) ? ($arrSKUDetails['price'] * $discount/100) : $discount;
                $arrSKUDetails['special_price'] = intval($arrSKUDetails['price']  - $discount); 
                $arrSKUDetails['special_from_date'] = $campaignDateBegin;
                $arrSKUDetails['special_to_date'] = $campaignDateEnd;
            }   
        } 
        
             
        // IMAGE attr 
        $arrImg = array();
        $rsItemImage = $item->getItemImage($itemkey);
        foreach($rsItemImage as $img){   
            $uploadedImg = $this->getCacheImageForMarketplace($itemkey,$img['file']); 
            if(!empty($uploadedImg))
                array_push($arrImg, $uploadedImg); 
        }
        
        if(!empty($arrImg)){ 
            $arrSKUDetails['Images'] = array();
            foreach($arrImg as $imgRow)
                array_push($arrSKUDetails['Images'],array('Image' => $imgRow));
        }
         
        // OTHERS ATTRIBUTE  
        $rsAttributes = $item->getMarketplaceCategoryAttributes($itemkey, $this->marketplaceKey); 
        foreach($rsAttributes as $attributeRow)
            $arrMainAttributes[$attributeRow['attributekey']] = $attributeRow['value'];
        
        
        $arrSKU['sku'] = $arrSKUDetails;
         
        // ============================================================ SKU 
 
        $arrParam = array(); 
        
        // CATEGORY attr
        $rsCategory = $this->getCategoryUsedForMarketplace($rsItem['categorykey']);
        $arrParam['Product']['PrimaryCategory']  = $rsCategory[0]['marketplacecategorykey'];   
        
        $arrParam['Product']['Attributes'] = $arrMainAttributes;
        $arrParam['Product']['Skus'] = $arrSKU; 
        
        $payloadXML = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><Request></Request>");
        $this->arrayToXML($arrParam,$payloadXML);
        $payloadXML = $payloadXML->asXML();
         
        //$this->setLog($payloadXML,true,'lazada.txt'); 
       /* echo $payloadXML;
        die;*/
        
        $request = new LazopRequest('/product/create');
        $request->addApiParam('payload',$payloadXML);
         
        $result = $this->execute($request,  array('actionkey' => $this->actionType['updateProduct'], 'ref' => $name )); 
        $result = json_decode($result,true); 
        //$this->setLog($result,true,'lazada.txt');
        
        $result = $result['data'];
        if(isset($result['item_id']) && !empty($result['item_id']))
            $this->addItemMarketplaceLink($itemkey,$result['item_id'], $result['sku_list'][0]['shop_sku']);
    }
    
    function getCacheImageForMarketplace($itemkey,$img){
           
        $imgPath = $this->cacheImageForMarketplace($itemkey,$img); 

        $payload = '<?xml version="1.0" encoding="UTF-8" ?><Request><Image><Url>'.$imgPath.'</Url></Image> </Request>';

        $imgRequest = new LazopRequest('/image/migrate');
        $imgRequest->addApiParam('payload',$payload);
        $imgResult = $this->execute($imgRequest); 
        $imgResult = json_decode($imgResult,true);


        return (isset($imgResult['data']['image']['url']) && !empty($imgResult['data']['image']['url'])) ? $imgResult['data']['image']['url'] : '';

    }
 
    function createXMLProducts($arrParam){
                    
            $payloadXML = '';
            $payloadXML .= '<?xml version="1.0" encoding="UTF-8" ?>';
            $payloadXML .= '<Request>';
            $payloadXML .= '<Product>';
  
        
            $payloadXML .= '<Attributes>';  
            foreach($arrMainAttributes as $key=>$row) 
               $payloadXML .= '<'.$key.'>'.$row.'</'.$key.'>';  
            $payloadXML .= '</Attributes>';
        
        
             
        
          /*  if (isset($arrParam['name']) && !empty($arrParam['name']))   
                $payloadXML .= '<name>'.$arrParam['name'].'</name>'; 
        
            if (isset($arrParam['shortDescription']) && !empty($arrParam['shortDescription']))   
                $payloadXML .= '<short_description>'.$arrParam['shortDescription'].'</short_description>'; 
            
            if (isset($arrParam['brand']) && !empty($arrParam['brand']))   
                $payloadXML .= '<brand>'.$arrParam['brand'].'</brand>'; 
        
             if(isset($arrParam['model']) && !empty($arrParam['model']))
                $payloadXML .= '<model>'.$arrParam['model'].'</model>'; */
        
        
            $payloadXML .= '<Skus>';
        
            foreach($arrParam['sku'] as $sku){ 
                //$this->setLog($sku);
                
                $payloadXML .= '<Sku>';
                
                 foreach($sku as $skuKey=>$skuAttribute) 
                     $payloadXML .= '<'.$skuKey.'>'.$skuAttribute.'</'.$skuKey.'>';
                   
                $payloadXML .= '</Sku>';
            }
        
        
            $payloadXML .= '</Skus>';
            $payloadXML .= '</Product>';
            $payloadXML .= '</Request>';
        
            return $payloadXML;
    }
     
    
    function updateProductsDescription($arrItemKey = ''){  
           
        if (!is_array($arrItemKey))  
            $arrItemKey = array($arrItemKey); 
        
        foreach($arrItemKey as $itemkey){  
            $syncCriteria = array();
            $syncCriteria['attr'] = array('shortDescription');
            $syncCriteria['type'] = 2;  
            $syncCriteria['itemkey'] = $itemkey; 

            $this->syncProducts($syncCriteria);
        }
        
    }
    
    
    function updateProductsQOH($arrItemsQOH){   
         
        // ad masalah kayanya kalo 0, harus di nonaktifkan...
 
         // lazada
            
            /* 
            if (in_array('qoh', $syncCriteria['attr'])){ 
                $qoh = intval($itemMovement->getItemQOH($itemkey,$warehousekey));  
                
                if ($qoh <= 0){
                     $arrSKUDetails['quantity'] = 0;
                     $arrSKUDetails['Status'] = 'inactive'; 
                }else{
                     $arrSKUDetails['quantity'] = $qoh;
                     $arrSKUDetails['Status'] = 'active'; 
                } 
            }*/
           
              
        
        // harus cek, kalo item blm ad, di add dulu  
    
        
        $arrItemsQOH = $this->removeUnsyncItem($arrItemsQOH);
        
        // ambil ulang karena $arrItemsQOH sudah ad perubahan
        $arrItemKeys = array_keys($arrItemsQOH);
         
        $this->resyncItemIfNotExist($arrItemKeys);
        
        $rowsLimit = $this->rowsLimit;
         
        $arr = array_chunk($arrItemsQOH, $rowsLimit,true);
        foreach($arr as $qohChunk){
            
            $arrItemKey = array_keys($qohChunk); 
            $rsItemLink = $this->searchLinkItem($arrItemKey);
            $rsItemLink = array_column($rsItemLink,null,'refkey');
                
            $arrSKU = array();
            foreach($qohChunk as $itemkey=>$row)
                array_push($arrSKU, array('Sku' => array(
                                                     'ItemId' =>  $rsItemLink[$itemkey]['marketplaceitemkey'], 
                                                    'SkuId' =>  $rsItemLink[$itemkey]['marketplaceskukey'],  
                                                    'SellerSku' => $row['itemcode'],  
                                                    'Quantity' => intval($row['qtyinbaseunit'])
                                                    )
                                        )
                           ); 

            $arrParam = array();  
            $arrParam['Product']['Skus'] = $arrSKU; 

            $payloadXML = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><Request></Request>");  
            $this->arrayToXML($arrParam,$payloadXML); 
            $payloadXML = $payloadXML->asXML(); 
 
            // update
            $request = new LazopRequest('/product/price_quantity/update'); 
            $request->addApiParam('payload',$payloadXML);
            $result = $this->execute($request,array('actionkey' => $this->actionType['updateProductQOH']));   
        } 
 
    }
    
    //special condition
    function updateProductsPrice($arrItemKey = ''){  
           
        if (!is_array($arrItemKey))  
            $arrItemKey = array($arrItemKey); 
        
        foreach($arrItemKey as $itemkey){  
            $syncCriteria = array();
            $syncCriteria['attr'] = array('price','name','qoh','description','others'); // sementara
            $syncCriteria['type'] = 2;  
            $syncCriteria['itemkey'] = $itemkey; 

            $this->syncProducts($syncCriteria);
        }
        
    }
    
    function deleteProduct($rs){  
         
        $itemList = array();
        array_push($itemList, $rs[0]['code']);
        
        $itemList = json_encode($itemList);
        
        $request = new LazopRequest('/product/remove');
        $request->addApiParam('seller_sku_list',$itemList);   
        $response = $this->execute($request); 
         
        $this->deleteItemMarketplaceLink($rs[0]['pkey']);
    }
    
    function getProducts($criteria = array()){
        
        $status = (isset($criteria['filter']) && !empty($criteria['filter']) ) ?  $criteria['filter'] : 'all' ;
        $offset = (isset($criteria['offset']) && !empty($criteria['offset']) ) ?  $criteria['offset'] : 0;
        $limit = (isset($criteria['limit']) && !empty($criteria['limit']) ) ?  $criteria['limit'] : 0;  
          
        $arrProducts = array();
        $nextPage = true; 
        $itemPerPage = 100;
        
        do { 
            if ( !empty($limit) && ( ($offset + $itemPerPage) > $limit ) ){ 
                $limit -= $offset;
                $itemPerPage = $limit;
                
                if($itemPerPage <= 0 ) break;
                
                $nextPage = false;
            }
            
            $request = new LazopRequest('/products/get','GET');
            $request->addApiParam('filter',$status); 
            $request->addApiParam('offset',$offset); 
            $request->addApiParam('limit',$itemPerPage); 
            
            $response = $this->execute($request);
            //$response = $this->client->execute($request, $this->accessToken);
            
            $response = json_decode($response,true);
            $response = $response['data'];
            
            if(empty($response)) break;
            
            $products = $response['products'];
           
            foreach($products as $product)
                array_push($arrProducts, array('item_id' => $product['item_id'],'ShopSku' => $product['skus'][0]['ShopSku'],  'SellerSku' => $product['skus'][0]['SellerSku'] ) ); // asumsi hanya 1 sku utk 1 item
             
            $offset += $itemPerPage; 
                 
            // buat jaga2 limt item 5000 product
            if($offset > 5000 ) {
                $nextPage = false;
                break;
            }
            
        } while($nextPage);
           
        
        return $arrProducts;

    } 
    
    function requestPickUp($rsSalesOrder){
         
        $rsSalesOrderDetail = $rsSalesOrder['detail']; 
         
        $orderItemID = array_column($rsSalesOrderDetail,'refmarketplacekey');
        $orderItemID = implode(',',$orderItemID);
           
        $trackingNumber = substr($orderItemID,-4);
        $shipmentProvider = $this->rsMarketplace['shipmentkey']; // 'J&T Cashless'
   
        $request = new LazopRequest('/order/rts');
        $request->addApiParam('delivery_type','dropship');
        $request->addApiParam('order_item_ids','['.$orderItemID.']');
        $request->addApiParam('shipment_provider',$shipmentProvider);
        $request->addApiParam('tracking_number',$trackingNumber);
        
        $response = $this->execute($request);
        //$response = $this->client->execute($request, $this->accessToken);
        //$this->setLog($response);    
        
        $this->updateRequestPickupStatus($rsSalesOrder['header']['pkey']);
    }
    
    function onConfirmTrans($rsSalesOrder){ 
        //$this->setLog('on confirm',true,'lz');
        // lazada 
        if($this->marketplaceAutoPickup)
            $this->requestPickUp($rsSalesOrder); 
       
    }
    
    function syncMarketplaceBrand($syncType){
        
        
        // $syncType => 1 : Lanjut, 2: dr awal
         
        $nextPage = true; 
        $itemPerPage = 100;
        $offset = (isset($criteria['offset']) && !empty($criteria['offset']) ) ?  $criteria['offset'] : 0;
         
        $rsExistingBrand = $this->getMarketplaceBrand();
        $rsExistingBrand = array_column($rsExistingBrand,null,'pkey'); 
        
        $totalExistingBrand = count($rsExistingBrand);
         
        if ($syncType == 1) 
            $offset = $totalExistingBrand;
       
        do {   
            $request = new LazopRequest('/category/brands/query','GET');  
            //$request->addApiParam('sort_direction','ASC');
            $request->addApiParam('startRow',$offset);
            $request->addApiParam('pageSize',$itemPerPage);
            //$request->addApiParam('sort_by','brand_id'); 
            
            $response = $this->execute($request);  
			//print_r($response);
			
            $response = json_decode($response,true);
            $response = $response['data']['module'];
               
            if(empty($response)) {
                $nextPage = false;
                break;
            }
            
            foreach ($response as $brandRow){
                $brandRow['pkey'] = $brandRow['brand_id']; 
                $brandkey = $brandRow['pkey'];
				
                if(isset($rsExistingBrand[$brandkey])){ 
                    //biar lebih cepet
                    if ($rsExistingBrand[$brandkey]['name'] <> $brandRow['name'])
                        $this->updateMarketplaceBrand($brandRow); 
                }else { 
                    $this->addMarketplaceBrand($brandRow);
                }
            }
             
            $offset += $itemPerPage;  
        } while($nextPage); 
          
        //delete semua brand yg sudah tdk terdaftar
        
    }
     
    function syncMarketplaceCategory($syncType){
        
        // $syncType -> blm tentu kepake di category tree
       
        // utk lazada delete semua saja, karena kalo children agak susah utk di loop update
        $dbCon = $this->masterConn();
        $sql = 'delete from '.$this->tableMarketplaceCategory.' where marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceProviderKey);
		
        $dbCon->execute($sql);	
         
        $sql = 'select pkey from '.$this->tableMarketplaceCategory.' order by pkey desc limit 1';
        $rs =  $dbCon->doQuery($sql);	
        
        $sql = 'ALTER TABLE '.$this->tableMarketplaceCategory.' AUTO_INCREMENT='. ($rs[0]['pkey']+1);
        $dbCon->execute($sql);	
      
        $dbCon = null;
        
        $rsExistingCategory = $this->getMarketplaceCategory();
        $rsExistingCategory = array_column($rsExistingCategory,null,'pkey'); 
          
        $request = new LazopRequest('/category/tree/get','GET');  
		$request->addApiParam('language_code','id_ID'); 

        $response = $this->execute($request);  
        $response = json_decode($response,true);
        $response = $response['data'];
 
        foreach ($response as $categoryRow){  
             
            $categoryRow['marketplacecategorykey'] = $categoryRow['category_id'];  
            $categoryRow['parentkey'] = 0; 
  
            /*if(isset($rsExistingCategory[$categorykey])){ 
                //biar lebih cepet 
                if ( ($rsExistingCategory[$categorykey]['name'] <> $categoryRow['name']) || ($rsExistingCategory[$categorykey]['isleaf'] <> $categoryRow['leaf']) )
                    $this->updateMarketplaceCategory($categoryRow); 
            }else { 
                $this->addMarketplaceCategory($categoryRow);
            }*/
            
            $this->addMarketplaceCategory($categoryRow);
             
            if(!$categoryRow['leaf'])
               $this->addMarketplaceCategoryChildren($categoryRow);
            
        }
        
        
    }
    
    function syncMarketplaceCategoryAttributes($syncType){ 
        // lazada    
        try{ 
                $limit = ''; //'limit 0,2';
            
                $dbCon = $this->masterConn();
                if(!$dbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);

                $criteria = '';
                if ($syncType == 1){
                    // ambil pkey category terakhir 
                    // utk table marketplace_category_attributes, marketplacecategorykey -> pkey di table minerva
                    
                    
                    // gk ad urusan, karena bisa saja penambahan attribute baru utk kategori lama
                   /* $rs = $this->getMarketplaceCategoryAttributes('',' order by marketplacecategorykey desc','limit 1');	  
                    $criteria .= (empty($rs)) ? '' : ' and pkey > ' . $rs[0]['marketplacecategorykey']; */
                }

                //echo $criteria;
                $rsCategory = $this->getMarketplaceCategory(' and isleaf <> 0',$limit);
 

                foreach($rsCategory as $categoryRow){

                    //echo $categoryRow['name'].'<br>';

                    $request = new LazopRequest('/category/attributes/get','GET'); 
                    $request->addApiParam('primary_category_id',$categoryRow['marketplacecategorykey']); 
                    //$request->addApiParam('language_code','id_ID');  // yg diterima di lazada cuma yg bhs inggris

                    $response = $this->execute($request);  
                    $response = json_decode($response,true);
                     
                    
                    if (!isset($response['data'])) continue;
                    
                    $response = $response['data'];

                    $rsExistAttributes = $this->getMarketplaceCategoryAttributes($categoryRow['marketplacecategorykey'], '','','',false);
                        
                    $rsExistAttributes = array_column($rsExistAttributes,'attributekey');

                    //$this->setLog($rsExistAttributes,true);
                    
                    foreach($response as $row){
                         
                        $attibuteId =  $row['name'];
                        
                        if (!$row['is_mandatory']) continue;
                        
                        $inputType = $this->getInputType($row['input_type']);
                        
                        $newRow = array();
                        $newRow['marketplacecategorykey'] = $categoryRow['marketplacecategorykey'];
                        $newRow['attributekey']  = $row['name'];
                        $newRow['name']   =  '';
                        $newRow['label']   =  $row['label'] ;
                        $newRow['attributeType']   =  $row['attribute_type'] ; 
                        $newRow['inputType']   =  $inputType;
                        $newRow['isMandatory']   =  $row['is_mandatory'] ;
                        $newRow['value']   =  ($inputType == 1) ? '' : json_encode($row['options']);

                        if(in_array($attibuteId,$rsExistAttributes))
                            $this->updateMarketplaceCategoryAttributes(array('attributekey' => $attibuteId, 'marketplacecategorykey' => $categoryRow['marketplacecategorykey']),$newRow);
                        else
                            $this->addMarketplaceCategoryAttributes($categoryRow,$newRow);

                    }
 

                } 


            $dbCon->endTrans(); 
             
		
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}	  
        
        $dbCon = null;
        
        /*$request = new LazopRequest('/category/attributes/get','GET'); 
        //$request->addApiParam('primary_category_id',$categoryRow['marketplacecategorykey']); 

        $response = $this->execute($request);  
        $response = json_decode($response,true);
        $response = $response['data'];

        die*/;
        
        
    }
  
    function syncMarketplaceLogistics(){   
        // gk tau kenapa dikomentarin dulu
        
        /*try{  
            $dbCon = $this->masterConn();
            
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	   
            $request = new LazopRequest('/shipment/providers/get','GET');

            $response = $this->execute($request);  
            $response = json_decode($response,true);
                 */
            
           /* $arrLogisticId = array_column($response, 'logistic_id');

             
            //echo $criteria;
            $rsExistedLogistics = $this->getMarketplaceLogistics(' and logisticid in ('.$dbCon->paramString($arrLogisticId,',').') ');  
            $rsExistedLogistics = array_column($rsExistedLogistics, 'logisticid');
            
            foreach($response as $row){ 
                if (in_array($row['logistic_id'], $rsExistedLogistics)) continue;
                
                $sql = 'insert into 
                            '.$this->tableMarketplaceLogistics. ' (marketplacekey, name, logisticid,statuskey) 
                       values ('.$dbCon->paramString($this->marketplaceKey).','.$dbCon->paramString($row['logistic_name']).','.$dbCon->paramString($row['logistic_id']).',1) ';
                $dbCon->execute($sql);
                
            } */
                 
		/*	$dbCon->endTrans(); 
             
		
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}		
         
        $dbCon = null;*/
    }
        
    function addMarketplaceCategoryChildren($categoryRow){

            $children = $categoryRow['children']; 
            foreach ($children as $childrenRow) {    
                $childrenRow['parentkey'] = $categoryRow['marketplacecategorykey'];
                $childrenRow['marketplacecategorykey'] = $childrenRow['category_id'];  
                $this->addMarketplaceCategory($childrenRow);
                
                if(!$childrenRow['leaf'])
                   $this->addMarketplaceCategoryChildren($childrenRow); 
            } 
    }
    
    function updateARPayment(){ 
     
    }
    
    /*function updateToken($code){  
        $code = strval($code);
        
        try{		
		 	
			if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
            $client = new LazopClient('https://auth.lazada.com/rest',$this->appKey,$this->secretKey);
            $request = new LazopRequest('/auth/token/create');
            $request->addApiParam('code',$code);  
            $response =  $client->execute($request);
            
            $response = json_decode($response,true); 
            
            if(isset($response['access_token']) && !empty($response['access_token'])){
                $sql = 'update '.$this->tableName.' set accesstoken = ' .$this->oDbCon->paramString($response['access_token']).' where pkey = ' . $this->marketplaceKey;
                $this->oDbCon->execute($sql); 
            }
            
			$this->oDbCon->endTrans();  
	 
		}catch(Exception $e){
			$this->oDbCon->rollback(); 
		}	 
			
    }*/

    function testConnection(){

            // setting criteria ..
            $request = new LazopRequest('/seller/get','GET');   
            $response = $this->execute($request);
            $response = json_decode($response,true);

            //$this->setLog($response,true);

            $arrReturn = array();
            if (isset($response['code']) && $response['code'] == 'IllegalAccessToken') {
                $arrReturn['status'] = false;
                $arrReturn['authURL'] = $this->authURL;
            }else{
                $arrReturn['status'] = true;
            } 

            
            $this->setMarketplaceLog(array('actionkey' => $this->actionType['testConnection'], 'issuccess' => $arrReturn['status']));
            return $arrReturn;
      }
    
    function getAirwayBill($arrOrderId = ''){ 
        $salesOrder = new SalesOrder();
 
        
        if(isset($arrOrderId) && !is_array($arrOrderId))
            $arrOrderId = array($arrOrderId); 
        
        $arrMarketplaceOrderId = array();
        
        // loop utk setiap transaksi
        foreach($arrOrderId as $refcode){
            //$rsSO = $salesOrder->searchData($salesOrder->tableName.'.refcode',$refcode);
            $rsSO = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey'),
                                                ' and '. $salesOrder->tableName.'.refcode = ' .$this->oDbCon->paramString($refcode)
                                              );
            
            $rsDetailSO = $salesOrder->getDetailById($rsSO[0]['pkey']);
 
            foreach($rsDetailSO as $detailRow){
                $marketplaceorderkey = $detailRow['refmarketplacekey'];
                 if(!in_array($marketplaceorderkey, $arrMarketplaceOrderId))
                     array_push($arrMarketplaceOrderId,$marketplaceorderkey);
            }
            
        }
         
        $request = new LazopRequest('/order/document/get','GET'); 
        $request->addApiParam('doc_type','shippingLabel');  
 
        $request->addApiParam('order_item_ids',json_encode($arrMarketplaceOrderId));  
         
        $response = $this->execute($request,  array('actionkey' => $this->actionType['printAirwayBill'], 'ref' => implode(', ',$arrMarketplaceOrderId) ));  
         
            
        $response = json_decode($response,true); 
        
        //$this->setLog($response,true);
        return $response['data']['document'];
 
    }
    
    function execute($request,$arrLog = array()){  
            
        $url = $this->url; 
        
        $client = new LazopClient($url,$this->appKey,$this->secretKey); 
        $response =  $client->execute($request, $this->accessToken);     
        //$this->setLog($response,true);
        
        $arrLog['response'] = $response;
        $this->setMarketplaceLog($arrLog);
        
        return $response; 
    }
    
    
    function getInputType($type){
          
        switch ($type){ 
            case 'multiEnumInput' : 
            case 'multiSelect':  
            case 'singleSelect': $returnType = INPUT_TYPE['select'];
                              break;
            case 'numeric' : $returnType = INPUT_TYPE['number'];
                              break;
            case 'richText' :   $returnType = INPUT_TYPE['textarea'];
                              break;
            case 'text': $returnType = INPUT_TYPE['text'];
                              break;
                 
                    
            default : $returnType = 0; // buat validasi mana yg blm terdaftar tipe inputannya
                      break;
                                  
        }
        
        return $returnType; 
    }
        
    function getSelectOptions($value){ 
        
        // lazada
        
        $temp = json_decode(htmlspecialchars_decode($value));  
        
        $options = array();
        foreach($temp as $optionRow){
            $optionRow = ( array ) $optionRow;
            $options[$optionRow['name']] = $optionRow['name'];
        } 
        
        
        return $options;
    }
    
    function dataMoatLogin($arrParam){
        return '';
        
        $client = new LazopClient('https://api.lazada.com/rest',$this->appKey,$this->secretKey);
        $request = new LazopRequest('/datamoat/login');
        $request->addApiParam('time',time());
        $request->addApiParam('appName','Minerva');
        $request->addApiParam('userId',$arrParam['name']);
        $request->addApiParam('tid',$arrParam['email']);
        $request->addApiParam('userIp',$arrParam['ipAddress']); 
        $request->addApiParam('ati',$arrParam['deviceFingerprint']);
        $request->addApiParam('loginResult',$arrParam['loginResult']);
        $request->addApiParam('loginMessage',$arrParam['loginMessage']);
        $response = $client->execute($request);
        $response = json_decode($response,true); 
        //$this->setLog($response,true);
  
        return $response;
         
    }
    
    function dataMoatComputeRisk($arrParam){
        return '';
                
        $client = new LazopClient('https://api.lazada.com/rest',$this->appKey,$this->secretKey);
        $request = new LazopRequest('/datamoat/compute_risk');
        $request->addApiParam('time',time());
        $request->addApiParam('appName','Minerva');
        $request->addApiParam('userId',$arrParam['name']);
        $request->addApiParam('userIp',$arrParam['ipAddress']); 
        $request->addApiParam('ati',$arrParam['deviceFingerprint']); 
        $response = $client->execute($request);
        $response = json_decode($response,true); 
        //$this->setLog($response,true);
      
        return $response;
    }
    
    // LOG
    function logPrintAirwayBill($response){
        $isSuccess = false;
        $message = '';
        
        $response = json_decode($response,true); 
        
        if(isset($response['data'])){
            $isSuccess = true;
        }
        
        return array('issuccess' => $isSuccess, 'message' => $message);
    }
    
    function logUpdateProduct($response){
        
        $response = json_decode($response,true);   
        
        $isSuccess = true;
        $message = '';
        $errCode = '';
         
        if( $response['code'] != '0' ){
            $isSuccess = false; 
            $message = $response['message'].chr(13);
            $message .= $response['detail'][0]['message']; 
            $errCode = $response['code'];
        }
        
        return array('issuccess' => $isSuccess, 'message' => $message, 'errorcode' => $errCode); 
    }
    
     function logUpdateProductQOH($response){
              
        $response = json_decode($response,true);   
          
        $isSuccess = true;
        $message = ''; 
              
        if( isset($response['code']) && !empty($response['code']) ){
            $isSuccess = false; 
            
            $arrErr = array();
            foreach($response['detail'] as $row)
                array_push($arrErr, $row['seller_sku']. ', '.$row['message']);
                
            $message = implode(chr(13),$arrErr);
        } 
        
        return array('issuccess' => $isSuccess, 'message' => $message ); 
    }
    
}
 
class Tokopedia extends Marketplace{
    
   function __construct($marketplaceKey = ''){
		
		parent::__construct();
		   
        // default value, overwrite if needed
  
        $this->url  = 'https://fs.tokopedia.net/';
        $this->callbackURL = '';
        $this->authURL = ''; 
        $this->backdateInterval =  "-2 days";
       
	    // sementara pake app pertama dulu
	    $selectedApp = 0;
        $this->fsid  = TOKOPEDIA_CONFIG[$selectedApp]['fsid'];
        $this->appKey  = TOKOPEDIA_CONFIG[$selectedApp]['appKey'];
        $this->secretKey  = TOKOPEDIA_CONFIG[$selectedApp]['secretKey']; 
        $this->webhookSecret = TOKOPEDIA_CONFIG[$selectedApp]['webhookSecret'];
	   
	   	$this->marketplaceProviderKey = MARKETPLACE['tokopedia'];
        
        $this->initMarketplace($this->marketplaceProviderKey,$marketplaceKey);
 
	}
	  
    
    function importOrders(){
        
        //$this->setLog("start import tokopedia " . date('d / m / Y H:i:s'),true,'tokopedia');
        
        $item = new Item(); 
        $customer = new Customer();
        $salesOrder = new SalesOrder();
        $warehouse = new Warehouse();
        $customCode = new CustomCode();
        
        // cek custom code
        $rsKey = $salesOrder->getTableKeyAndObj($salesOrder->tableName,array('key'));
        $rsCustomCode = $customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'],true,' and '.$customCode->tableName.'.statuskey = 1');
        $customCodeKey = (empty($rsCustomCode)) ? 0 : $rsCustomCode[0]['pkey'];
        
        $warehousekey = $warehouse->getDefaultData(); 
        
        $customerkey = $this->rsMarketplace['customerkey'];
        $rsCustomer = $customer->getDataRowById($customerkey);
        $topkey = $rsCustomer[0]['termofpaymentkey'];
        $saleskey =  $rsCustomer[0]['saleskey'];
  
        $yesterday =  date('U',strtotime($this->backdateInterval)); 
        $today =  date('U',strtotime("+1 days")); // kayanya timezone tokopedia beda
         
        // execute 
 
        $pageCtr = 1;
        $loop = true;
        $savePoint = 3;
        $perPage = 100;
        
        while($loop){
            
            $url = $this->url . 'v2/order/list?fs_id='.$this->fsid.'&shop_id='.$this->shopId.'&from_date='.$yesterday.'&to_date='.$today.'&status=220&page='.$pageCtr.'&per_page='.$perPage;  
            $result = $this->execute($url) ;   
            //$this->setLog($url,true,'tokopedia');
            //$this->setLog($result,true,'tokopedia');

            //$this->setLog($orders,true);  
            $orders = (isset($result['data']) && !empty($result['data'])) ? $result['data'] : array();
  
            // klao udah gk sama
            // ad kemungkinan bisa miss kalo pas narik transaksi halaman kedua ad masuk penjualan lg, indexnya jd berubah
            if(count($orders) < $perPage)
                $loop = false;
            
            $arrOrderQueue = array();  
            foreach ($orders as $order){ 

                if(empty($order['order_id'])) continue;

                $orderId = $order['order_id']; 
                $invoiceNumber = $order['invoice_ref_num'];
                $arrProducts = $order['products'];
                $arrBuyer = $order['buyer'];
                $arrRecipient = $order['recipient'];

                $logisticName = $order['logistics']['shipping_agency'] . ' - '.$order['logistics']['service_type'] ;  
                $shipmentkey = $this->getShipmentDetailByName($logisticName);

                //$rsSales = $salesOrder->searchData('','',true, ' and '.$salesOrder->tableName.'.marketplaceorderid = '.$salesOrder->oDbCon->paramString($orderId).' and  '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey));
                $rsSales = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey'),  
                                                      ' and '.$salesOrder->tableName.'.marketplaceorderid = '.$salesOrder->oDbCon->paramString($orderId).' 
                                                        and '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey)
                                                     );
                
                if(!empty($rsSales)) continue;

                $order['create_time'] -= (7*60*60); // tokopedia pakenya GMT +0
                $orderDate =  date("d / m / Y H:i",  $order['create_time']);

                 // =============  compile item
                $arrItemCode = array();
                $notes = array();
                foreach($arrProducts as $product){ 
                    if (!empty($product['notes'])) array_push($notes,$product['notes']);
                    array_push($arrItemCode, $product['sku']);
                }

                //$rsItemColl = $item->searchData('','',true, ' and ('.$item->tableName.'.code in ('.$item->oDbCon->paramString($arrItemCode,',').'))');

                $rsItemColl = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.code',$item->tableName.'.baseunitkey'),
                                                       ' and ('.$item->tableName.'.code in ('.$item->oDbCon->paramString($arrItemCode,',').'))'
                                                      );

                $itemColl = array_column($rsItemColl,null,'code');
                // =============  compile item


                // PREPARE ARRAY   
                $arrParam = array();
                $arrParam['code'] = 'xxxxxx';
                $arrParam['trDate'] = $orderDate;
                $arrParam['selWarehouseKey'] = $warehousekey;
                $arrParam['hidCustomerKey'] = $customerkey;
                $arrParam['selStatus'] = 1;
                $arrParam['selTermOfPaymentKey'] = $topkey;
                $arrParam['trDesc'] = implode(chr(13), $notes);
                $arrParam['chkIsFullDeliver'] = 1; 
                $arrParam['selFinalDiscountType'] = 1;
                $arrParam['finalDiscount'] = 0 ;
                $arrParam['marketplaceKey'] = $this->marketplaceKey;
                $arrParam['refCode'] = $invoiceNumber;
                $arrParam['selCustomCode'] = $customCodeKey;
                $arrParam['marketplaceOrderId'] =  $orderId;
                $arrParam['selShipmentService'] = $shipmentkey;
                $arrParam['hidSalesKey'] = $saleskey; 

                //$this->setLog('order id '. $orderId,true);    

                $arrParam['recipientName'] = $arrBuyer['name'];
                $arrParam['recipientPhone'] = $arrBuyer['phone'];
                $arrParam['recipientEmail'] = $arrBuyer['email'];
                $arrParam['recipientAddress'] = $arrRecipient['address']['address_full'];

                $arrItemKey = array();

                $warningNotifications = array();

                $arrParam['hidDetailKey'] = array();
                $arrParam['refMarketplaceKey'] = array();
                $arrParam['hidItemKey'] = array();
                $arrParam['selUnit'] = array();
                $arrParam['priceInUnit'] = array();
                $arrParam['priceInBaseUnit'] = array();
                $arrParam['unitConvMultiplier'] = array();
                $arrParam['qty'] = array();
                $arrParam['qtyInBaseUnit'] = array();

                foreach($arrProducts as $product){ 

                    $product['sku'] = trim($product['sku']);
                    
                    if(!isset($itemColl[$product['sku']]['pkey'])) { 
                        array_push($warningNotifications, $product['sku']. '. '. $salesOrder->errorMsg[213]);
                        continue;
                    }

                    $itemkey = $itemColl[$product['sku']]['pkey'];
                    $baseunitkey = $itemColl[$product['sku']]['baseunitkey'];

                    array_push($arrItemKey,$itemkey ); 
                    array_push($arrParam['hidDetailKey'], 0);
                    array_push($arrParam['refMarketplaceKey'], '');
                    array_push($arrParam['hidItemKey'], $itemkey);
                    array_push($arrParam['selUnit'], $baseunitkey);
                    array_push($arrParam['priceInUnit'], intval($product['price']));
                    array_push($arrParam['priceInBaseUnit'], intval($product['price']));
                    array_push($arrParam['unitConvMultiplier'], 1);
                    array_push($arrParam['qty'], intval($product['quantity']));
                    array_push($arrParam['qtyInBaseUnit'], intval($product['quantity']));

                }

                if(!empty($warningNotifications)){ 
                    $arrParam['_hasWarning_'] = true;

                    if (!empty($arrParam['trDesc'])) $arrParam['trDesc'] .= chr(13);
                    $arrParam['trDesc'] .= implode(chr(13),$warningNotifications);
                }

                array_push($arrOrderQueue,$arrParam);

            }


            // keluarin dulu yg sudah ada sales ordernya, agar mengurangi kerjaan validateForm
            $arrOrderId = array_column($arrOrderQueue,'marketplaceOrderId');
            //$rsSales = $salesOrder->searchData('','',true, ' and '.$salesOrder->tableName.'.marketplaceorderid in ('.$salesOrder->oDbCon->paramString($arrOrderId,',').') and  '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey));
            $rsSales = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey', $salesOrder->tableName.'.marketplaceorderid'),
                                       ' and '.$salesOrder->tableName.'.marketplaceorderid in ('.$salesOrder->oDbCon->paramString($arrOrderId,',').') 
                                         and  '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey)
                                       );
                
            $rsSales =  array_column($rsSales,'marketplaceorderid');

            foreach($arrOrderQueue as $arrParam){ 

                if(in_array($arrParam['marketplaceOrderId'],$rsSales)) continue;
                if (!isset($arrParam['hidItemKey']) || empty($arrParam['hidItemKey'])) continue;

                //$this->setLog("--- Saving ". $arrParam['refCode'] ." " . date('d / m / Y H:i:s'),true,'mp'); 

                try{

                    if(!$this->oDbCon->startTrans(true))
                        throw new Exception($this->errorMsg[100]);

                      $arrayToJs = $salesOrder->addData($arrParam); 

                      if(!$arrayToJs[0]['valid'])
                        throw new Exception( $arrayToJs[0]['message'] );

                       /*if (isset($arrParam['_hasWarning_']) && !empty($arrParam['_hasWarning_'])){
                            $sql = 'update ' . $salesOrder->tableName.' set tagkey = 1 where pkey = ' . $item->oDbCon->paramString($arrayToJs[0]['data']['pkey']);
                        }*/

                      $this->oDbCon->endTrans();
                }catch(Exception $e){
                    $this->oDbCon->rollback();
                    $this->addErrorList($arrayToJs,false,$e->getMessage());
                }	

            }

            
            $pageCtr++;
            
            // save point    
            if($pageCtr > $savePoint) $loop = false;
        }
        
        
        //$this->setLog("end import tokopedia " . date('d / m / Y H:i:s'),true,'mp');
    } 
    
    function cancelCanceledOrders(){
        // tokopedia
        
        // coba pake webhooks aja
        // tetep perlu utk triger manual
        
        $salesOrder = new SalesOrder();  
        $canceledStatus = array(0,10);
        
        $yesterday =  date('U',strtotime($this->backdateInterval)); 
        $today =  date('U',strtotime("+1 days")); // kayanya timezone tokopedia beda

        foreach($canceledStatus as $statusRow){
            $url = $this->url . 'v2/order/list?fs_id='.$this->fsid.'&status='.$statusRow.'&from_date='.$yesterday.'&to_date='.$today.'&page=1&per_page=100'; 
            $result = $this->execute($url); 
            $result = $result['data'];

            if(empty($result)) continue;
            
            $arrOrderId = array_column($result,'order_id'); 
            
            $rsSalesOrder = $salesOrder->searchDataRow(array($salesOrder->tableName.'.pkey'), 
                                                       ' and '.$salesOrder->tableName.'.statuskey = 1 
                                                         and '.$salesOrder->tableName.'.marketplaceorderid in ('.$salesOrder->oDbCon->paramString($arrOrderId,',').') 
                                                         and  '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey)
                                                      );

            foreach($rsSalesOrder as $row)
                $salesOrder->changeStatus($row['pkey'], TRANSACTION_STATUS['batal'], '',false, true);
             
        }
        
        
    }

    
    function closeCompletedOrders(){ 
       // dari webhook harusnya sudah keupdate
       // tp ini utk jaga2 aja kalo webhook gagal
        
        // harus pisah tombol, karena ini berat narik satu2 query ke tokped
        return;
        
        $salesOrder = new SalesOrder();
        
        $ordersnList = array();
        //$rsSalesOrder = $salesOrder->searchData($salesOrder->tableName.'.marketplacekey', $this->marketplaceKey,true, ' and refcode <> "" and '.$salesOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].') ' );
        $rsSalesOrder = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey', $salesOrder->tableName.'.refcode'),
                                                    ' and '.$salesOrder->tableName.'.marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey).' 
                                                      and '.$salesOrder->tableName.'refcode <> \'\' 
                                                      and '.$salesOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].') ' 
                                                  );
          
        foreach($rsSalesOrder as $rs){
             
            $refcode = $rs['refcode'];
            
            $url = $this->url . 'v2/fs/'.$this->fsid.'/order?invoice_num='.$refcode; 
            $result = $this->execute($url); 
            $result = $result['data'];
            
            if($result['order_status'] >= 500)  
                $salesOrder->changeStatus($rs['pkey'], TRANSACTION_STATUS['selesai'], '',false, true);
             
        } 
            
    }
  
    function registerWebhook(){
        
        $return = array();
        
        $host = 'https://www.wintera.co.id/';
        
        $url = $this->url.'v1/fs/'.$this->fsid.'/register';
        array_push($return, $url);
        
        $arr = array();
        $arr['fs_id'] = $this->fsid;
        $arr['order_notification_url'] = $host.'api/tokopedia/wintera-callback-order-notification'; 
        $arr['order_cancellation_url'] = $host.'api/tokopedia/wintera-callback-order-cancellation'; 
        $arr['order_status_url'] = $host.'api/tokopedia/wintera-callback-order-status'; 
        $arr['order_request_cancellation_url'] = $host.'api/tokopedia/wintera-callback-order-request-cancellation'; 
        $arr['chat_notification_url'] = $host.'api/tokopedia/wintera-callback-chat-notification'; 
        $arr['product_creation_url'] = $host.'api/tokopedia/wintera-callback-product-creation'; 
        $arr['webhook_secret'] = $this->webhookSecret; 
         
        array_push($return, $arr);
        $result = $this->execute($url,'POST',$arr);  
        array_push($return, $result);
        
        return $return;
         
    }
    
    
    function getRegisteredWebhook(){
        
        $return = array();
        
        $url = $this->url.'v1/fs/'.$this->fsid;
        array_push($return, $url);
         
        $result = $this->execute($url);
        array_push($return, $result);
        
        return $return;
    }
    
    
    function updateProduct($itemkey,$rsItemColl = array(),$syncCriteria = array()){ 
       return $this->createProduct($itemkey, 'edit',$syncCriteria);  
    }
    
    function createProduct($itemkey,$action='create',$syncCriteria = array()){
      
        // tokopedia
        
        $item = new Item();
        $itemMovement = new ItemMovement();
         
        $useVariant = $this->loadSetting('useVariant');
        
        $returnArr = array();
        
        $warehousekey = ''; // ini diudpate jika punya gudang khusus utk marketplace 
          
        $rsItem = $item->getDataRowById($itemkey);
        if (empty($rsItem)) return;
        
        
        // kalo tipenya variant, yg diupdate parentnya
        if($useVariant && $rsItem[0]['isvariant'] == 1){
            $itemkey = $rsItem[0]['parentkey'];
            $rsItem = $item->getDataRowById($itemkey);
            if (empty($rsItem)) return;
            
            $action = 'edit'; // harusnya utk parent nya pasti EDIT
        }
        
        
        $rsItem = $rsItem[0]; 
        
        // cek kalo blm ad di tokped, add ulang  
        $existingItem = $this->getProductBySKU($rsItem['code']);  
  
		$rsItemLink = array();
		
        if(empty($existingItem)){
			$action = 'create';
		}else{
			 $rsItemLink = $this->searchLinkItem($itemkey); 
            
            if(empty($rsItemLink[0]['marketplaceitemkey'])){ 
                //update ulang marketplacekey karena kadang gk keupdate begitu add 
                $productId = $existingItem['basic']['productID']; 
                $this->addItemMarketplaceLink($itemkey,$productId); 
                 
                $rsItemLink[0]['marketplaceitemkey'] = $productId;
            }
		} 
		
		$marketplaceProductId = (!empty($existingItem)) ? $existingItem['basic']['productID'] : 0; 
         
        //parameter need to be sync
        $sku = $rsItem['code'];  
		$arrItemMPInformation = $this->getItemInformationForMarketplace($itemkey)[0];

		$mpName = $arrItemMPInformation['name'];
		$name = (!empty($mpName)) ? $mpName : $rsItem['name']; // cek ke settingan ad overwrite nama gk
		
        // CATEGORY attr
        $rsCategory = $this->getCategoryUsedForMarketplace($rsItem['categorykey']);
        $categoryId  = $rsCategory[0]['marketplacecategorykey'];   
        
        
        if(!empty($this->rsMarketplace['priceadjustment'])){
            $priceAdjustment = $this->rsMarketplace['priceadjustment'];
            $priceAdjustmentType = $this->rsMarketplace['priceadjustmenttype']; 
            $priceAdjustment = ($priceAdjustmentType == 2 ) ? ($rsItem['sellingprice'] * $priceAdjustmentType/100) : $priceAdjustment;
          
            
            $rsItem['sellingprice'] += $priceAdjustment;
            $rsItem['sellingprice']  = ceil($rsItem['sellingprice']/1000) * 1000;
        }
        
        if(!empty($this->rsMarketplace['margin'])){
            $margin = $this->rsMarketplace['margin'];
            $marginType = $this->rsMarketplace['margintype'];
            
            $margin = ($marginType == 2 ) ? ($rsItem['sellingprice'] * $margin/100) : $margin;
            $rsItem['sellingprice'] += $margin;
            $rsItem['sellingprice'] = intval($rsItem['sellingprice']);
        }
          
        $price = floatval($rsItem['sellingprice']);  
          
        $minOrder = 1;
        
        $weight = $rsItem['gramasi'];
        if ($rsItem['weightunitkey'] == UNIT['kg'])
            $weight *= 1000;
         
        $weightUnit = 'GR';
        $condition = $this->getItemConditionForMarketplace($rsItem['conditionkey']);
	    $arrEtalaseKey = $this->getStorefrontUsedForMarketplace($rsItem['categorykey'],$rsItem['brandkey']);

		$mpDesc = $arrItemMPInformation['shortdescription'];
		$mpDesc = (!empty($mpDesc)) ? $mpDesc : $rsItem['shortdescription'];
        $description = $mpDesc;
		
        $qoh = $itemMovement->getItemQOH($itemkey,$warehousekey);  
        
        $qoh = ($qoh < 0) ? 0 : $qoh; 
        $qoh = ($qoh > 1000) ? 1000 : $qoh;
        
        $status = ($qoh <= 0) ? 'EMPTY' : 'LIMITED';
        
        // khusus kalo produk baru
        /*$pushWithEmpty = false;
        if($action == 'create' && $qoh == 0) {
            $pushWithEmpty = true;
            $qoh = 1;
        }*/
        
        $arrImage = array();
        
        // update iamge 
        $rsItemImage = $item->getItemImage($itemkey);
        $arrImg = array();
         
        foreach($rsItemImage as $img){   
            //temp
            //$imgPath = 'https://pstn.program-stok.com/uploadeditor/pstn.program-stok.com/item/3619/84828f2f524b31cf0d34b65b6435cab3.jpg';
            $imgPath = $this->cacheImageForMarketplace($itemkey,$img['file']);
            
            array_push($arrImage,array('file_path' => $imgPath));
        }  
  
        $arr = array();
        $arr['products'] = array();
        
        $temp = array();
        
        $temp['name'] = html_entity_decode($name); // agar gk keluar tag html
        
        if($action == 'edit'){ 
            // kalo edit, cek pernah transaksi gk, kalo pernah, nama gk bisa diubah
 
			if (isset($existingItem['GMStats']['countSold']) && !empty($existingItem['GMStats']['countSold']))
                $temp['name'] = $existingItem['basic']['name'];
            
//            $rsItemLink = $this->searchLinkItem($itemkey); 
            
//            if(empty($rsItemLink[0]['marketplaceitemkey'])){ 
//                //update ulang marketplacekey karena kadang gk keupdate begitu add 
//                $productId = $existingItem['basic']['productID']; 
//                $this->addItemMarketplaceLink($itemkey,$productId); 
//                 
//                $rsItemLink[0]['marketplaceitemkey'] = $productId;
//            }
            
            $temp['id'] = intval($rsItemLink[0]['marketplaceitemkey']); 
        }
             
        //$this->setLog($description,true,'tp-status');    
        
        $temp['category_id'] = intval($categoryId); 
        $temp['price_currency'] = 'IDR';
        $temp['price'] = intval($price);
        $temp['status'] = $status;
        $temp['min_order'] = intval(1);
        $temp['weight'] = intval($weight);
        $temp['weight_unit'] = $weightUnit; 
        $temp['condition'] = $condition;
        $temp['etalase'] = array('id' => intval($arrEtalaseKey[0])); 
        $temp['description'] = $description;
        $temp['sku'] = $sku;
        $temp['stock'] = intval($qoh);
        $temp['price_currency'] = 'IDR';
        $temp['pictures'] = $arrImage;
        //$temp['variant'] = ""; // gk bisa, harus remove manual
        
        // kondisi variant 
        // sudah ad variant, lalu 
            // nonaktifkan variant
            // hapus variant
        
        // kalo ad variant
        if($useVariant){
                $rsVariant = $item->getItemVariants($itemkey, $this->marketplaceKey);
         
                if(!empty($rsVariant)){

                    $arrItemVariantKey = array_column($rsVariant,'pkey');
                    $rsQOH = $itemMovement->getItemsQOH($arrItemVariantKey);
                    $rsQOH = array_column($rsQOH,null, 'itemkey');

                    $temp['stock'] = 1;
                    $temp['status'] = $status;

                    $arrVariant = array();
                    $arrVariant['products'] = array();
                    $arrVariant['selection'] = array();

                    $variantProduct = array();
                    $arrSelection = array();
                    $variantOpt = array();

                    // ini perlu dicek nanti kalo 2 level 
                    $combinationCtr = 0;
                    $isPrimary = true; // sementara
                    
                    // set jenis option nya dulu, ambil variatn pertama saja, idealnya harus samas semua utk setiap variant
                     foreach($rsVariant as $key=>$variantItem){  

                        $arrVariantValue = $variantItem['marketplace_variant'];

                        // ini perlu dicek nanti kalo 2 level 
                        foreach($arrVariantValue as $variantValueRow){
                            $valueId = explode(',',$variantValueRow['variantkey']);
                            $arrOptionValue = $variantValueRow['optionvalue'];
                            $arrOptionValue = json_decode(html_entity_decode($arrOptionValue),true);

							array_push($variantOpt, array(
								"hex_code" =>  $arrOptionValue['hexcode'], 
								//"unit_value_id" => 432,
								"value" => substr($arrOptionValue['value'],0,15),
							));  
								  
//                            foreach($arrOptionValue as $key=>$optRow){ 
//                                array_push($variantOpt, array(
//                                    "hex_code" =>  $optRow['hexcode'],
//                                    //"unit_value_id" => $key,
//                                    "value" => $optRow['value'],
//                                ));  
//                            }
                        }
                     }
  
                    
                    // set selection 
                    array_push($arrSelection, array(
                        "id" => intval(trim($valueId[0])), // ambil index[0] saja gpp, idelanya memang harus sama semua
                        "unit_id" => intval(trim($valueId[1])), 
                        "options" => $variantOpt
                    )); 
 
                    
                    // set products
                    $combinationCtr = 0;
                    foreach($rsVariant as $key=>$variantItem){   
  
                        $qoh =  (isset($rsQOH[$variantItem['pkey']])) ? $rsQOH[$variantItem['pkey']]['qtyinbaseunit'] : 0; 
                        
                        //test dulu
                        $statusVariant = ($qoh <= 0) ? 'EMPTY' : 'LIMITED';
                        
                        $rsItemImage = $item->getItemImage($variantItem['pkey']);
                         
                        $arrImg = array();
                         
                        foreach($rsItemImage as $img){    
                            $imgPath = $this->cacheImageForMarketplace($variantItem['pkey'],$img['file']); 
                            array_push($arrImg,array('file_path' => $imgPath));
                        }  
        
                        array_push($variantProduct, array(
                            "is_primary" => $isPrimary,
                            "status" => $statusVariant,
                            "price" => intval($variantItem['sellingprice']),
                            "stock" =>  intval($qoh),
                            "sku" => $variantItem['code'],
                            "combination" => array(intval($combinationCtr++)), // 0, 1 tergantung index selection
                            "pictures" => $arrImg,
                        )); 
                         
                        $isPrimary = false; // sementara
                    }
                     
                    $arrVariant['products'] = $variantProduct;  
                    $arrVariant['selection'] = $arrSelection;

                    $temp['variant'] = $arrVariant;
                }
        }
      
       
        
        $arr['products'][0] = $temp;
        
        $url = $this->url . 'v3/products/fs/'.$this->fsid.'/'.$action.'?shop_id='.$this->shopId ; 
        $method = ($action == 'create') ? 'POST' : 'PATCH';
        
        $result = $this->executeOnBackground($url,$method,$arr, array('actionkey' => $this->actionType['updateProduct'], 'ref' => $name ) );    
		        
// 		$this->setLog('$arr =====',true,'tp');
//         $this->setLog($url,true,'tp');
//         $this->setLog(json_encode($arr),true,'tp');
// 		$this->setLog('result =====',true,'tp');
//         $this->setLog(json_encode($result),true,'tp');
        
        array_push($returnArr,$result); 
		
		// kalo post / add baru, ambil product id nya
		if($action == 'create')
			$marketplaceProductId = $result['data']['success_rows_data'][0]['product_id'] ?? '';
		
		
		 //$this->setLog('$productId',true,'status-tp');
		// $this->setLog($marketplaceProductId,true,'status-tp');
		
		// update status
		// kalo product id gk kosong saja  
		if(!empty($marketplaceProductId)){ 
			$activeInactiveUrl = ($rsItem['statuskey'] == 1) ? 'active' : 'inactive';
			$url = $this->url . 'v1/products/fs/'.$this->fsid.'/'.$activeInactiveUrl.'?shop_id='.$this->shopId ; 
			//$this->setLog($url,true,'status-tp');

			$arrStatusProducts = array();
			$arrStatusProducts['product_id'] = array(intval($marketplaceProductId));

			//$this->setLog($arrStatusProducts,true,'status-tp');
			$result = $this->execute($url,'POST',$arrStatusProducts, array('actionkey' => $this->actionType['updateProduct'],'message' => 'Update Status', 'ref' => $name ) );   
			//$this->setLog($result,true,'status-tp');
			array_push($returnArr,$result);  
		} 
		
        // selanjutnya dihandle oleh webhook
        // gk bisa kayanya karena beda domain
        
        /* 
        $uploadId  = $result['data']['upload_id'];
        
        if (!empty($uploadId)){
            
            //get upload status
            $result = $this->getUpdateStatus($uploadId);
            array_push($returnArr,$result);

            // nanti perlu diupdate di loop per baris / rows
            
            $failedRows = $result['data']['failed_rows'];
            $unprocessedRows =  $result['data']['unprocessed_rows'];

            // get marketplace product id
            // harusnya pake foreach product_id nya
            //&& $unprocessedRows == 0 <-- ini kadang data berhasil diupload, tp nilainya 1
              
            
            if ($failedRows == 0 ){ 
               $result = $this->getProductBySKU($sku); 
               array_push($returnArr,$result);

               $this->setLog($returnArr,true,'addtp');
            
               $this->setLog('result basic',true,'addtp');
               $this->setLog($result,true,'addtp');
               
               $productId = $result[0]['basic']['productID']; 
               $this->addItemMarketplaceLink($itemkey,$productId); 
            } 
        }
        */
         
        return $returnArr;
           
    }
    
    function getUpdateStatus($id){
        $url = $this->url . 'v2/products/fs/'.$this->fsid.'/status/'.$id.'?shop_id='.$this->shopId; 
        return $this->execute($url);  
    }
  
    function updateProductsDescription($arrItemKey = ''){  
           
        if (!is_array($arrItemKey))  
            $arrItemKey = array($arrItemKey); 
        
        foreach($arrItemKey as $itemkey){  
            return $this->createProduct($itemkey, 'edit');  
        }
        
    }
  
    function updateProductsQOH($arrItemsQOH){   
        
        // tokopedia
         
        $arrItemsQOH = $this->removeUnsyncItem($arrItemsQOH);
        
        // ambil ulang karena $arrItemsQOH sudah ad perubahan
        $arrItemKeys = array_keys($arrItemsQOH);
        
        // harus cek, kalo item blm ad, di add dulu    
        $this->resyncItemIfNotExist($arrItemKeys);
        
        
        $url = $this->url . 'inventory/v1/fs/'.$this->fsid.'/stock/update?shop_id='.$this->shopId;
  
        $rowsLimit = $this->rowsLimit;
         
        //$arr = array_chunk('item QOH : ', $rowsLimit,true);
        $arr = array_chunk($arrItemsQOH, $rowsLimit,true);
           
        foreach($arr as $qohChunk){ 
            $arrUpdate = array(); 
            foreach($qohChunk as $itemkey=>$row){ 
                $qoh = $row['qtyinbaseunit'];   
                $qoh = ($qoh < 0) ? 0 : $qoh; 
                $qoh = ($qoh > 1000) ? 1000 : $qoh; 
                array_push($arrUpdate, array('sku' => $row['itemcode'], 'new_stock' => intval($qoh) ));
            }
             
		    $result = $this->executeOnBackground($url,'POST',$arrUpdate, array('actionkey' => $this->actionType['updateProductQOH'])); 
            // $result = $this->execute($url,'POST', $arrUpdate,array('actionkey' => $this->actionType['updateProductQOH'])); 
            
        }
          
        
    }
    
    //special condition
    function updateProductsPrice($arrItemKey = ''){  
        
       // update price only
        
       if (!is_array($arrItemKey))  
            $arrItemKey = array($arrItemKey); 
        
        foreach($arrItemKey as $itemkey){  
            return $this->createProduct($itemkey, 'edit');  
        }
    }
    
    function deleteProduct($rs){  
      /*   
        $itemList = array();
        array_push($itemList, $rs[0]['code']);
        
        $itemList = json_encode($itemList);
        
        $request = new LazopRequest('/product/remove');
        $request->addApiParam('seller_sku_list',$itemList);   
        $response = $this->execute($request); 
         
        $this->deleteItemMarketplaceLink($rs[0]['pkey']);*/
    }
    
    /*function tempUpdateMarketplaceLink(){
        $nextPage = false;
        $itemPerPage = 50; 
        $page = 1; 
        
        $item = new Item();
        
        $rsItem = $item->searchData();
        $rsItem = array_column($rsItem,'pkey','code');
        
        do{
            $url = $this->url . 'inventory/v1/fs/'.$this->fsid.'/product/info?shop_id='.$this->shopId.'&page='.$page.'&per_page='.$itemPerPage;
            $result = $this->execute($url);    
              
            $result = $result['data']; 
             
            foreach($result as $row) 
                 $this->addItemMarketplaceLink($rsItem[$row['other']['sku']],$row['basic']['productID']); 
            
             $nextPage = (count($result) < $itemPerPage) ? false : true;
             
             $page++; 
             
        } while($nextPage);
             
    }*/
    
    function getProducts($criteria = array()){  
        $nextPage = false;
        $itemPerPage = 50;
        $arrProducts = array();
        $page = 1; 
        
        do{
            // $this->setLog('start >>>> ',true,'tp');  
            $url = $this->url . 'inventory/v1/fs/'.$this->fsid.'/product/info?shop_id='.$this->shopId.'&page='.$page.'&per_page='.$itemPerPage;
            $result = $this->execute($url);    
            
            // $this->setLog('returned ',true,'tp');  
            // $this->setLog($result,true,'tp');  
            
            $result = $result['data']; 
            
            foreach($result as $row){ 
                array_push($arrProducts, $row);
                //$this->addItemMarketplaceLink($itemkey,$row['basic']['productId']); 
            }
            
             $nextPage = (count($result) < $itemPerPage) ? false : true; 
             $page++; 
             
        } while($nextPage);
            
           
        return $arrProducts;
    } 
    
    function onConfirmTrans($rsSalesOrder){ 
    
        // terima order
        $url = $this->url . 'v1/order/'.$rsSalesOrder['header']['marketplaceorderid'].'/fs/'.$this->fsid.'/ack';
        //$this->setLog($url,true);
        $result = $this->execute($url,'POST');      
        //$this->setLog($result,true);
        
        if($this->marketplaceAutoPickup)
            $this->requestPickUp($rsSalesOrder);
    }
    
    function requestPickUp($rsSalesOrder){
         
        $rsSalesOrder = $rsSalesOrder['header'];
        
        // request pickup
        
        $url = $this->url . 'inventory/v1/fs/'.$this->fsid.'/pick-up';
        
        $arr = array();
        $arr['order_id'] = intval($rsSalesOrder['marketplaceorderid']);
        $arr['shop_id'] =  $this->shopId ;  
        
        $result = $this->execute($url,'POST',$arr);
         
        if (isset($result['data']) && !empty($result['data'])) 
            $this->updateRequestPickupStatus($rsSalesOrder['pkey']);  
         
    }
    
    function syncMarketplaceBrand($syncType){ }
     
    function syncMarketplaceCategory($syncType=''){
        
        // $syncType -> blm tentu kepake di category tree
       
        // utk lazada delete semua saja, karena kalo children agak susah utk di loop update
        $dbCon = $this->masterConn();
        
        $sql = 'delete from '.$this->tableMarketplaceCategory.' where marketplacekey = ' . $this->oDbCon->paramString($this->marketplaceProviderKey);
        $dbCon->execute($sql);	
         
        $sql = 'select pkey from '.$this->tableMarketplaceCategory.' order by pkey desc limit 1';
        $rs =  $dbCon->doQuery($sql);	
        
        $sql = 'ALTER TABLE '.$this->tableMarketplaceCategory.' AUTO_INCREMENT='. ($rs[0]['pkey']+1);
        $dbCon->execute($sql);	
        
		
        $dbCon = null;
        
/*        $rsExistingCategory = $this->getMarketplaceCategory();
        $rsExistingCategory = array_column($rsExistingCategory,null,'pkey'); */
          
        $response = $this->getCategories();
        $response = $response['categories'];
           
        foreach ($response as $categoryRow)    
            $this->addMarketplaceCategory($categoryRow);  
    }
    
    function addMarketplaceCategory($categoryRow,$parentkey = 0){  
         
        $dbCon = $this->masterConn();
        
        $categorykey = $categoryRow['id'];
        $categoryName = $categoryRow['name'];  
        $leaf = (!isset($categoryRow['child']) || empty($categoryRow['child']) ) ? 1 : 0;
        
        try{ 
			
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	 
            $sql = 'insert into  '.$this->tableMarketplaceCategory.'(
                        marketplacecategorykey, 
                        marketplacekey,
                        name ,
                        parentkey,
                        isleaf,
                        statuskey 
                    ) 
                    values(
                        '.$this->oDbCon->paramString($categorykey).',
                        '.$this->oDbCon->paramString($this->marketplaceProviderKey).', 
                        '.$this->oDbCon->paramString($categoryName).',
                        '.$this->oDbCon->paramString($parentkey).',
                        '.$this->oDbCon->paramString($leaf).',
                        1 
                    ) ';
           
            $dbCon->execute($sql);	 
            
            if(!$leaf){
                foreach($categoryRow['child'] as $childRow) 
                    $this->addMarketplaceCategory($childRow,$categorykey);  
            }
                
                
			$dbCon->endTrans(); 
             
		
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}		
         
        $dbCon = null;
    }
    
    function syncMarketplaceCategoryAttributes($syncType){ }
       
    function syncMarketplaceLogistics(){  
         
        try{  
            $dbCon =  $this->masterConn();
            
			if(!$dbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	   
            
            $url = $this->url . 'v2/logistic/fs/'.$this->fsid.'/info?shop_id='. $this->shopId;
 
            $response = $this->execute($url);  
            $response = $response['data'];
             
            $arrLogisticId = array(); 
            foreach($response as $row)
                foreach($row['services'] as $serviceRow)
                    array_push($arrLogisticId,$serviceRow['service_id']); 
          
 
            //echo $criteria;
            $rsExistedLogistics = $this->getMarketplaceLogistics(' and logisticid in ('.$dbCon->paramString($arrLogisticId,',').') ');  
            $rsExistedLogistics = array_column($rsExistedLogistics, 'logisticid');
            
            foreach($response as $row){ 
                
                $logisticName = $row['shipper_name'];
                
                foreach($row['services'] as $serviceRow){
                    
                    if (in_array($serviceRow['service_id'], $rsExistedLogistics)) continue;

                    $sql = 'insert into 
                                '.$this->tableMarketplaceLogistics. ' (marketplacekey, name, logisticid,statuskey) 
                           values ('.$dbCon->paramString($this->marketplaceProviderKey).','.$dbCon->paramString($logisticName . ' - '.$serviceRow['service_name']).','.$dbCon->paramString($serviceRow['service_id']).',1) ';
                    $dbCon->execute($sql);
                    
                }
                
            } 
                 
			$dbCon->endTrans(); 
             
		
	    } catch(Exception $e){
			$dbCon->rollback(); 
		}		
         
        $dbCon = null;
         
    }
        
    function updateARPayment(){
        //$this->setLog("start import tokopedia " . date('d / m / Y H:i:s'),true,'mp'); 
        
        $savePoint = 20;
        $page = 1;
        $maxRequest = 100; // sementara abaikan dulu saja have_next_page
        
        $baseDate = strtotime("-1 days");
        $dateFrom = date('Y-m-d',$baseDate);
        $dateTo =  $dateFrom; // biar nariknya per hari
          
        $arrDepositId = array();
        $arrTransactionId = array();
        $loop = true;
        
        while($loop){ 
            $url = $this->url . 'v1/fs/'.$this->fsid.'/shop/'.$this->shopId.'/saldo-history?page='.$page.'&per_page='.$maxRequest.'&from_date='.$dateFrom.'&to_date='.$dateTo; 
            $result = $this->execute($url);
             
            $paymentResult = $result['data']['saldo_history']; 
            
            foreach($paymentResult as $row){
                
                // tampung deposit id jg, karena ad temuan, utk page selanjutnya, data pertama berulang dr page sebelumnya
                if(in_array($row['deposit_id'],$arrDepositId)) continue; 
                array_push($arrDepositId,$row['deposit_id']);
                
                $transId = $this->getTransactionId($row['note']);
                
                // add saja dulu semua, mau positif atau negativ
                if(!empty($transId))  array_push($arrTransactionId,array('transId'=>$transId, 'trdate' => $dateFrom, 'amount'=>$row['amount'])); 
            }
            
            $loop = $result['data']['have_next_page'];
            $page++;
            if ($page > $savePoint){
              $loop = false; // buat jaga2
              $this->setLog('save check point entered',true,'ck-tp');
            } 
        }    
         
        // gk ad payment 
        return (!empty($arrTransactionId)) ? $this->createARPayment($arrTransactionId) : 'no payment'; 
    } 
    
    function updateStorefront($arrStorefrontKey){
        // kalo blm ad marketplacestorefrontkey
        // add baru
        $storefront = new Storefront();
        $rsStorefront = $storefront->searchDataRow(array($storefront->tableName.'.pkey', $storefront->tableName.'.name', $storefront->tableName.'.marketplacestorefrontkey'),
                                   ' and '.$storefront->tableName.'.marketplacekey = ' . $this->marketplaceKey .' and '.$storefront->tableName.'.pkey in ('.$this->oDbCon->paramString($arrStorefrontKey,',').')'
                                  );
             
        foreach($rsStorefront as $row){ 
            
            if(empty($row['marketplacestorefrontkey'])){
                // push
                // update key 
                $arr = array( "name" => $row['name'] );
                $url = $this->url . 'v1/showcase/fs/'.$this->fsid.'/create?shop_id='.$this->shopId ;   
                $result = $this->execute($url,'POST',$arr);   
                //$this->setLog($result,true);
                
                if (!empty($result['data']['created_id'])){
                    try{ 

                        if(!$this->oDbCon->startTrans())
                            throw new Exception($this->errorMsg[100]);

                          $sql = 'update '.$storefront->tableName.' 
                            set marketplacestorefrontkey = '.$this->oDbCon->paramString($result['data']['created_id']).' 
                            where '.$storefront->tableName.'.pkey = ' . $this->oDbCon->paramString($row['pkey']); 
                    
                        $this->oDbCon->execute($sql);
                        $this->oDbCon->endTrans();  

                    } catch(Exception $e){
                        $this->oDbCon->rollback(); 
                    }		
 
                } 

            }else{
                // update 
                $arr = array("id" => intval($row['marketplacestorefrontkey']), "name" => $row['name'] );
                $url = $this->url . 'v1/showcase/fs/'.$this->fsid.'/update?shop_id='.$this->shopId ;   
                $result = $this->execute($url,'PATCH',$arr);
            }
        } 
    }
    
    function deleteStorefront($storefrontKey){
		$arr = array( "id" => intval($storefrontKey) );
		$url = $this->url . 'v1/showcase/fs/'.$this->fsid.'/delete?shop_id='.$this->shopId ;   
		$result = $this->execute($url,'POST',$arr); 
	}
	
     function getTransactionId($string,$keyword='INV/'){
        $pos = strpos($string,$keyword);

        $string = substr($string,$pos); 
        $string = explode(' ',$string);
        $string = $string[0];
        return $string;
    }
    
    /*function updateToken(){ 
    
            $newToken = $this->getAccessToken();
        
            try{ 

                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);

                $sql = 'update  '.$this->tableName .' set accesstoken = '.$this->oDbCon->paramString($newToken).' where pkey = ' . $this->oDbCon->paramString($this->marketplaceKey); 
                 
                $this->oDbCon->execute($sql);	 
 
                $this->oDbCon->endTrans(); 


            } catch(Exception $e){
                $this->oDbCon->rollback(); 
            }		

    }*/

    function testConnection(){
        
        // sementara taro disini dulu 
        //$this->updateToken(); 
            
        $url = $this->url . 'v1/shop/fs/'.$this->fsid.'/shop-info';
        $result = $this->execute($url);                
         
        if (isset($tempResult['message']) && ($tempResult['message'] == 'invalid_token' || $tempResult['message'] == 'token_not_found')){
            $arrReturn['status'] = false;
            $arrReturn['authURL'] = '#'; 
        }else{ 
            $arrReturn['status'] = true; 
            $arrReturn['authURL'] = '#'; 
        } 
         
        $this->setMarketplaceLog(array('actionkey' => $this->actionType['testConnection'], 'issuccess' => $arrReturn['status']));
            
        return $arrReturn;
         
    }
    
    function getAirwayBill($arrOrderId = ''){
    
        // tokopedia   
        $salesOrder = new SalesOrder();
        
        if(isset($arrOrderId) && !is_array($arrOrderId)) $arrOrderId = array($arrOrderId); 
         
        $result = array();
        
        foreach($arrOrderId as $orderId){ 
            
            //$rsSalesOrder = $salesOrder->searchData($salesOrder->tableName.'.refcode', $orderId);
            $rsSalesOrder = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey', $salesOrder->tableName.'.marketplaceorderid'),
                                                        ' and '.$salesOrder->tableName.'.refcode = '. $this->oDbCon->paramString($orderId)
                                                       );
            
            $oderId = $rsSalesOrder[0]['marketplaceorderid']; 

            $url = $this->url . 'v1/order/'.$oderId.'/fs/'.$this->fsid.'/shipping-label';
            
            // perlu raw nya tanpa dide code json 
            $response = $this->execute($url,'GET', array(),array(),array('rawReturn' => true));
            
            array_push($result,$response);  
        }
         
        return $result;
        
    }
    
    function execute($url,$method='GET',$payload=array(), $arrLog = array(),$arrOpt = array()){ 
	 
        // tokopedia 
		
		$rawReturn = (isset($arrOpt['rawReturn'])) ? $arrOpt['rawReturn'] : false;
		$recallUpdateToken = (isset($arrOpt['recallUpdateToken'])) ? $arrOpt['recallUpdateToken'] : true;
		
        $header = array(
            'Content-Type: application/json',  
            'Authorization: Bearer '.$this->accessToken
        );
        
        if (empty($payload)) 
            array_push($header, 'Content-Length: 0'); 
        
        
        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0); 
        curl_setopt($connection, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
     
            
        if(!empty($payload)) {
            $payload = json_encode($payload);
            curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
        }
         
        $response = curl_exec($connection); 
        
        if ($response === false) $this->setLog(curl_error($connection),true); 

        curl_close($connection); 
        
        // kalo error karena token, update token
        if($recallUpdateToken){
			 $tempResult = json_decode($response,true);
             
             if (isset($tempResult['message']) && ($tempResult['message'] == 'invalid_token' || $tempResult['message'] == 'token_not_found')){
                $this->updateTokenTokopedia();
                $this->execute($url,$method,$payload, $arrLog, array('rawReturn' => $rawReturn,'recallUpdateToken' => false) );  
            }
		}
		
        $arrLog['response'] = $response;
        $this->setMarketplaceLog($arrLog);
        
		
//        $this->setLog('will return',true,'tp'); 
//        $this->setLog($response,true,'tp'); 
//        $this->setLog(($rawReturn) ? 'raw' : 'no',true,'tp'); 
        
        return ($rawReturn) ? $response : json_decode($response,true); 
    }
     
   /* function getAccessToken(){
        $url = 'https://accounts.tokopedia.com/token?grant_type=client_credentials';
        $token = base64_encode($this->appKey.':'.$this->secretKey);
        
        $header = array(
            'Content-Type: application/json', 
            'Content-Length: 0',
            'Authorization: Basic '.$token
        );


        $connection = curl_init(); 
        curl_setopt($connection, CURLOPT_URL, $url);
        curl_setopt($connection, CURLOPT_HTTPHEADER, $header);
        curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($connection, CURLOPT_POST, 1);  
        curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($connection); 
          
        curl_close($connection);
        
        $response = json_decode($response,true);
        return  $response['access_token'];
    }*/
       
    function getStorefront(){
        $url = $this->url . 'inventory/v1/fs/'.$this->fsid.'/product/etalase?shop_id='.$this->shopId;
        $result = $this->execute($url);                
        
        $result = $result['data']['etalase'];
        
        return $result;
    }
    
    function getCategories($keywords = ''){  
        
        $keyword = (!empty($keywords)) ? '?keyword='.$keywords : '';
        
        $url = $this->url . 'inventory/v1/fs/'.$this->fsid.'/product/category'.$keyword;
        $result = $this->execute($url);                
        
        $result = $result['data'];
   
        return $result;
    }
     
    function getProductBySKU($sku){
		$sku = trim($sku);
		
        $url = $this->url . 'inventory/v1/fs/'.$this->fsid.'/product/info?sku='.$sku; 
        $result = $this->execute($url);   
        $result = $result['data'] ?? [];
		   
		// tokopedia kalo balikin item SKU, tidak bisa filter per toko
		foreach($result as $row){  
			if($row['basic']['shopID'] == $this->shopId){ return $row; break;}
		}
        
        return array();
    }
     
    function getProductByProductId($productId){
        $url = $this->url . 'inventory/v1/fs/'.$this->fsid.'/product/info?product_id='.intval($productId);   
        $result = $this->execute($url);    
        $result = $result['data'];
        
        return $result;
    }
    
    function syncMarketplaceStorefront($syncType = ''){
        $itemCategory = new ItemCategory();
        
        $result = $this->getStorefront();
        $storeFrontKey = array_column($result,'etalase_id');
         
        $existingStorefront = $this->getMarketplaceStorefront('',$this->marketplaceKey);  
        $existingStorefront = array_column($existingStorefront,'marketplacestorefrontkey');
        
        try{ 
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
	 
             foreach($result as $row){
                 
                // kalo blm ad
                $storefrontkey = $row['etalase_id'];
                $storefrontname = $row['etalase_name'];

                if(!in_array($storefrontkey, $existingStorefront)){ 
                    $sql = 'insert into  '.$this->tableMarketplaceStorefront.' (marketplacestorefrontkey,marketplacekey,name,statuskey,isleaf) 
                            values ('.$this->oDbCon->paramString($storefrontkey).', '.$this->oDbCon->paramString($this->marketplaceKey).', '.$this->oDbCon->paramString($storefrontname).',1 ,1) ';

                // kalo ud ad, update
                }else { 
                    $sql = 'update  '.$this->tableMarketplaceStorefront.' 
                            set name = '.$this->oDbCon->paramString($storefrontname).'
                            where
							marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey).' and
							marketplacestorefrontkey = '.$this->oDbCon->paramString($storefrontkey);
					
                 
                }
                $this->oDbCon->execute($sql);
                  
                // kalo gk ad,delete
                $sql = 'delete from '.$this->tableMarketplaceStorefront.' where marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey).' and marketplacestorefrontkey not in ('.$this->oDbCon->paramString($storeFrontKey,',').')';
                $this->oDbCon->execute($sql);

            }

			$this->oDbCon->endTrans(); 
             
		
	    } catch(Exception $e){
			$this->oDbCon->rollback(); 
		}		
        
        
    }
     
	
    // LOG
    function logPrintAirwayBill($response){
        
        $isSuccess = false;
        $message = '';
         
        if( strpos($response, 'Halaman Tidak Ditemukan') === false  ){
            $isSuccess = true;
        }
        
        return array('issuccess' => $isSuccess, 'message' => $message);
    }
        
    function logUpdateProduct($response){
        // tokopedia
         
        $response = json_decode($response,true);   
        
        $isSuccess = true;
        $message = '';
        $errCode = '';
         
        if( isset($response['header']['error_code']) ){
            $isSuccess = false; 
            
            $message = array();
            
            if(!empty($response['header']['messages']))
                 array_push( $message, $response['header']['messages']);
                 
            if(!empty($response['header']['reason']))
                 array_push( $message, $response['header']['reason']);
             
            $message =  implode(chr(13), $message);
            
            $errCode = $response['header']['error_code'];
        }
        
        return array('issuccess' => $isSuccess, 'message' => $message, 'errorcode' => $errCode); 
    }
    
     function logUpdateProductQOH($response){
        // tokopedia      
         $response = json_decode($response,true);   
        
        $isSuccess = true;
        $message = '';
        $errCode = '';
         
        if( isset($response['header']['error_code']) ){
            $isSuccess = false; 
            
            $message = array();
            
            if(!empty($response['header']['messages']))
                 array_push( $message, $response['header']['messages']);
                 
            if(!empty($response['header']['reason']))
                 array_push( $message, $response['header']['reason']);
             
            $message =  implode(chr(13), $message);
            
            $errCode = $response['header']['error_code'];
        }
        
        return array('issuccess' => $isSuccess, 'message' => $message, 'errorcode' => $errCode); 
    }
    
    
    function logImportOrders($response){
        // tokopedia
         
        //$this->setLog("test",true);
        
        //$response = json_decode($response,true);   
        //$this->setLog($response,true);
        
        $isSuccess = false;
        $message = '';
        $errCode = '';
         
        /*if( isset($response['header']['data']) && !empty($response['header']['data'])){
            $isSuccess = true;    
            $message = count($response['header']['data']) . ' '. $this->lang['totalData'];
        }*/
        
        return array('issuccess' => $isSuccess, 'message' => $message); 
    }
     
    function getInvoice($arrInvoice){
        // kedepan perlu disave url pdf nya agar hemat API call
        
          // tokopedia   
         
        $result = array();
        
        foreach($arrInvoice as $invoiceId){ 
             
            $url = $this->url . 'v2/fs/'.$this->fsid.'/order?invoice_num='.$invoiceId; 
            $response = $this->execute($url);
            
            array_push($result,array('url' => $response['data']['invoice_url'] ));  
        }
         
        return $result;
        
    }
    
    function addSalesOrderById($arrId){  
          
        /*$this->setLog(date('d-m-Y H:i').' start auto push for ...',true,'auto-tp.txt');
        $this->setLog($arrId,true,'auto-tp.txt');*/
        
        $customer = new Customer();
        $salesOrder = new SalesOrder();
        $warehouse = new Warehouse();
        $customCode = new CustomCode();
        $item = new Item(); 
        
        $customerkey = $this->rsMarketplace['customerkey'];
        $rsCustomer = $customer->getDataRowById($customerkey);
        $topkey = $rsCustomer[0]['termofpaymentkey'];
        $saleskey =  $rsCustomer[0]['saleskey'];
        
        // cek custom code
        $rsKey = $salesOrder->getTableKeyAndObj($salesOrder->tableName,array('key'));
        $rsCustomCode = $customCode->searchData($customCode->tableName.'.reftabletype',$rsKey['key'],true,' and '.$customCode->tableName.'.statuskey = 1');
        $customCodeKey = (empty($rsCustomCode)) ? 0 : $rsCustomCode[0]['pkey'];
       
        $warehousekey = $warehouse->getDefaultData(); 
        
        if(!is_array($arrId))
            $arrId = array($arrId);
        
        
        // collect all order sn, utk tau sales mana saja yg sudah masuk ke SO 
        $rsSalesCol = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey',$salesOrder->tableName.'.marketplaceorderid'),
                                              ' and '.$salesOrder->tableName.'.marketplaceorderid in ('.$salesOrder->oDbCon->paramString($arrId,',').') 
                                                and '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey)
                                             );
        
        $rsSalesCol = array_column($rsSalesCol,null,'marketplaceorderid'); 
        
        /*$this->setLog( ' and '.$salesOrder->tableName.'.marketplaceorderid in ('.$salesOrder->oDbCon->paramString($arrId,',').') 
                                                and '.$salesOrder->tableName.'.marketplacekey = '.$salesOrder->oDbCon->paramString($this->marketplaceKey),true,'duplicate-sh');    
        $this->setLog($rsSalesCol,true,'duplicate-sh');    */
             
        
        foreach($arrId as $orderId){ 
            // kalo sudah ada, jgn add 
            if(isset($rsSalesCol[$orderId])){
                //$this->setLog($orderId,true,'duplicate-tp');    
                continue;
            }

            //cek dulu sudah terdaftar blm
            /*$rsSalesOrder = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey'),
                                                        ' and '.$salesOrder->tableName.'.marketplaceorderid = ' .$this->oDbCon->paramString($orderId) .' 
                                                          and '.$salesOrder->tableName.'.marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey)
                                                        );
            
            if(!empty($rsSalesOrder)) continue;*/
            
            
            // cari ulang ke tokped informasi berdasarkan order ids
            $url = $this->url . 'v2/fs/'.$this->fsid.'/order?order_id='.$orderId; 
            //$this->setLog($url,true);
            
            $response = $this->execute($url);
            //$this->setLog($response,true,'auto-tp.txt');
            
            if(empty($response['data'])) continue;

              try{ 
                    $this->oDbCon->startTrans(true); 
                   
                    $order = $response['data'];
                  
                    $orderId = $order['order_id']; 
                    $invoiceNumber = $order['invoice_number'];
                    $invoiceUrl = $order['invoice_url'];
                  
                    $arrProducts = $order['order_info']['order_detail'];
                    $arrBuyer = $order['buyer_info'];
                    $arrRecipient = $order['order_info']['destination'];

                    $logisticName = $order['order_info']['shipping_info']['logistic_name'] . ' - '.$order['order_info']['shipping_info']['logistic_service'] ;  // ini perlu cek lg
                    //$this->setLog($logisticName,true);
                    $shipmentkey = $this->getShipmentDetailByName($logisticName);
   
                    // tokopedia pakenya GMT +0 ?? knp beda disini
                    $orderDate =  date("d / m / Y H:i",  strtotime($order['create_time'].' -7 hours')); 

                  
                    $notes = array();
                  
                    // =============  compile item
                    /*$arrItemCode = array();
                    $notes = array();
                    foreach($arrProducts as $product){ 
                        if (!empty($product['notes'])) array_push($notes,$product['notes']);
                        array_push($arrItemCode, $product['sku']);
                    }*/


                    // PREPARE ARRAY   
                    $arrParam = array();
                    $arrParam['code'] = 'xxxxxx';
                    $arrParam['trDate'] = $orderDate;
                    $arrParam['selWarehouseKey'] = $warehousekey;
                    $arrParam['hidCustomerKey'] = $customerkey;
                    $arrParam['selStatus'] = 1;
                    $arrParam['selTermOfPaymentKey'] = $topkey;
                    $arrParam['trDesc'] = '- Auto Push -'.chr(13).implode(chr(13), $notes);
                    $arrParam['chkIsFullDeliver'] = 1; 
                    $arrParam['selFinalDiscountType'] = 1;
                    $arrParam['finalDiscount'] = 0 ;
                    $arrParam['marketplaceKey'] = $this->marketplaceKey;
                    $arrParam['refCode'] = $invoiceNumber;
                    $arrParam['selCustomCode'] = $customCodeKey;
                    $arrParam['marketplaceOrderId'] =  $orderId;
                    $arrParam['marketplaceInvoiceURL'] = $invoiceUrl;
                    $arrParam['selShipmentService'] = $shipmentkey;
                    $arrParam['hidSalesKey'] = $saleskey; 
 
                    //$this->setLog('order id '. $orderId,true);    
                    
                    $recipientAddress = array();
                    if(!empty($arrRecipient['address_street'])) array_push($recipientAddress,$arrRecipient['address_street']);
                    if(!empty($arrRecipient['address_city']))  array_push($recipientAddress,$arrRecipient['address_city']);
                    if(!empty($arrRecipient['address_province'])) array_push($recipientAddress,$arrRecipient['address_province']);
                    if(!empty($arrRecipient['address_postal'])) array_push($recipientAddress,$arrRecipient['address_postal']);
                        
                    $arrParam['recipientName'] = $arrRecipient['receiver_name'];
                    $arrParam['recipientPhone'] = $arrRecipient['receiver_phone'];
                    $arrParam['recipientEmail'] = '';
                    $arrParam['recipientAddress'] = implode(chr(13),$recipientAddress);

                    $arrItemKey = array();

                    $warningNotifications = array();

                    $arrParam['hidDetailKey'] = array();
                    $arrParam['refMarketplaceKey'] = array();
                    $arrParam['hidItemKey'] = array();
                    $arrParam['selUnit'] = array();
                    $arrParam['priceInUnit'] = array();
                    $arrParam['priceInBaseUnit'] = array();
                    $arrParam['unitConvMultiplier'] = array();
                    $arrParam['qty'] = array();
                    $arrParam['qtyInBaseUnit'] = array();

                    $arrItemCode = array_column($arrProducts,'sku'); 
                    $rsItemColl = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.code',$item->tableName.'.baseunitkey'),
                                                   ' and ('.$item->tableName.'.code in ('.$item->oDbCon->paramString($arrItemCode,',').'))'
                                                  );

                    $itemColl = array_column($rsItemColl,null,'code');


                    foreach($arrProducts as $product){ 
                        
                        $product['sku'] = trim($product['sku']);
                        
                        if(!isset($itemColl[$product['sku']]['pkey'])) { 
                            array_push($warningNotifications, $product['sku']. '. '. $salesOrder->errorMsg[213]);
                            continue;
                        }

                        $itemkey = $itemColl[$product['sku']]['pkey'];
                        $baseunitkey = $itemColl[$product['sku']]['baseunitkey'];

                        array_push($arrItemKey,$itemkey ); 
                        array_push($arrParam['hidDetailKey'], 0);
                        array_push($arrParam['refMarketplaceKey'], '');
                        array_push($arrParam['hidItemKey'], $itemkey);
                        array_push($arrParam['selUnit'], $baseunitkey);
                        array_push($arrParam['priceInUnit'], intval($product['product_price']));
                        array_push($arrParam['priceInBaseUnit'], intval($product['product_price']));
                        array_push($arrParam['unitConvMultiplier'], 1);
                        array_push($arrParam['qty'], intval($product['quantity']));
                        array_push($arrParam['qtyInBaseUnit'], intval($product['quantity']));

                    }

                    if(!empty($warningNotifications)){ 
                        $arrParam['_hasWarning_'] = true;

                        if (!empty($arrParam['trDesc'])) $arrParam['trDesc'] .= chr(13);
                        $arrParam['trDesc'] .= implode(chr(13),$warningNotifications);
                    }
                  
                    $result = $salesOrder->addData($arrParam);
                    //$this->setLog($result,true,'auto-tp.txt');
                  
                    $this->oDbCon->endTrans();
         
                } catch(Exception $e){
                    $this->oDbCon->rollback(); 
                }	 
            
        }
         
    }
    
    function cancelSalesOrderById($orderId){
        //$this->setLog('cancel '.$orderId,true,'CANCEL-TP');    
        
        $salesOrder = new SalesOrder();

        //cek dulu sudah terdaftar blm
        $rsSalesOrder = $salesOrder->searchDataRow( array($salesOrder->tableName.'.pkey', $salesOrder->tableName.'.statuskey'),
                                                    ' and ' .$salesOrder->tableName.'.marketplaceorderid = ' .$this->oDbCon->paramString($orderId) .' 
                                                      and ' .$salesOrder->tableName.'.statuskey in (1,2,3)  
                                                      and '.$salesOrder->tableName.'.marketplacekey = '.$this->oDbCon->paramString($this->marketplaceKey)
                                                    );
   
        // kalo pake array nanti, perlu set ulang start transactionnya
        if(!empty($rsSalesOrder)) { 
            $orderId = $rsSalesOrder[0]['pkey']; 
            
            if( $rsSalesOrder[0]['statuskey'] == 1){ 
               //$this->setLog('cancel ',true,'CANCEL-TP');  
               $salesOrder->changeStatus($orderId, TRANSACTION_STATUS['batal'], '',false, true);    
            }else{ 
                
               //$this->setLog('change tag ',true,'CANCEL-TP');  
               $salesOrder->changeTag($orderId, 1);
            }
        }
        
        
    }
    
    function syncMarketplaceCategoryVariant(){
        // ambil semua kategori yg terdaftar
        
        $dbCon = $this->masterConn(); 
        $sql = 'select * from '.$this->tableMarketplaceCategory.'
                where
                    '.$this->tableMarketplaceCategory.'.isleaf = 1 and
                    '.$this->tableMarketplaceCategory.'.variantjson is null and 
                    '.$this->tableMarketplaceCategory.'.marketplacekey = ' .MARKETPLACE['tokopedia'].' 
                order by pkey asc ';  
            
//		$this->setLog($sql,true,'variant'); 
//		die;
        
        $rsCategory = $dbCon->doQuery($sql);
         
        foreach($rsCategory as $row){
            $url = $this->url . 'inventory/v1/fs/'.$this->fsid.'/category/get_variant?cat_id='.$row['marketplacecategorykey'];
           
			$response = $this->execute($url);
            if(empty($response['data'])) continue;
            
               try{ 
                    $dbCon->startTrans(true); 

                    $sql = 'update '.$this->tableMarketplaceCategory.' set variantjson = \''. addslashes(json_encode($response['data'])).'\' 
                            where '.$this->tableMarketplaceCategory.'.pkey = '.$this->oDbCon->paramString($row['pkey']);
                    $dbCon->execute($sql);
                   
                    $dbCon->endTrans();
         
                } catch(Exception $e){
                   $dbCon->rollback(); 
                }	
            
//            $this->setLog($row['name'],true,'variant');
//            $this->setLog($response,true,'variant');
        } 
        $dbCon = null; 
        
    }
    
    function updateAWB($pkey,$awb){ 
        $salesOrder = new SalesOrder(); 
        $rsSalesOrder = $salesOrder->getDataRowById($pkey);
        
        if(empty($rsSalesOrder[0]['marketplaceorderid'])) return;
        
        $url = $this->url . 'v1/order/'.$rsSalesOrder[0]['marketplaceorderid'].'/fs/'.$this->fsid.'/status';   
        
        $arr = array("order_status" => 500, "shipping_ref_num" => $awb);
        $result = $this->execute($url,'POST',$arr); 
        
    }


    function getMarketplaceCategoryVariant($marketplacecategorykey,$parentkey='', $criteria = ''){ 
        
        // $categorykey yg digunakan adalah pkey marketplace_category di Minerva
        
        $rsVariant = array();
        
        // $parentkey : kalo ad nilai, maka jenis variant harus mengikuti parentkey
        $parentVariantKey = 0;
        if (!empty($parentkey)){ 
            $item = new Item();
            $rsVariantDetail = $item->getVariantValueForMarketplace($parentkey, $this->marketplaceKey);  
            $parentVariantKey = $rsVariantDetail[0]['variantkey']; 
        }
        
        $dbCon = $this->masterConn(); 
        $sql = 'select * from '.$this->tableMarketplaceCategory. ' where marketplacekey = ' . $this->marketplaceProviderKey .' 
                and marketplacecategorykey = ' . $this->oDbCon->paramString($marketplacecategorykey);
        
        $sql .= ' ' .$criteria;
         
        $rs = $dbCon->doQuery($sql);
        $dbCon = null;
        
        
		if(!empty($rs[0]['variantjson'])){
			$arrVariant = json_decode($rs[0]['variantjson'],true);
            
			foreach($arrVariant as $row){
                
                
				$hasUnit = $row['has_unit']; // semua pasti ad unit ? coba dicek //ada
				$arrUnit = $row['units'];
                
				foreach($arrUnit as $unitRow){
                    $key = $row['variant_id'].','.$unitRow['unit_id'];
                    
                    // kalo ad variant parent, cuma return kategori parentnya, biar sama dengan parent
                    if(!empty($parentVariantKey) && $key != $parentVariantKey) continue; 

					$labelName =  (empty($unitRow['name'])) ? $row['name'] :  $row['name'].' ('.$unitRow['name'].')';
					$arrValues = $unitRow['values'];
                    
					$arrVariantValues = ($arrValues) ? array_column($arrValues, 'value', 'value_id') : array();
				    
					array_push($rsVariant,
						array(
							'variantId' => $row['variant_id'],
						    'unitId' => $unitRow['unit_id'],
						    'key' => $key,
							'label' => $labelName,
							'rsVariant' => $arrValues,
							'arrVariant' => $arrVariantValues 
						)
					);
			     }  
			}
			 
		}
        
        
        return $rsVariant;
    } 
}
?>
