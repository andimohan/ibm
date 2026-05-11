<?php

class Campaign extends BaseClass{
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'campaign';  
		$this->tableCampaignItem = 'campaign_detail_item'; 
		$this->tableCampaignItemCategory = 'campaign_detail_item_category';    
		$this->tableCampaignBrand = 'campaign_detail_brand'; 
		$this->tableCampaignMarketplace = 'campaign_detail_marketplace'; 
		$this->tableCampaignPrice = 'campaign_price'; 
		$this->tableStatus = 'transaction_status';  
		$this->tableWarehouse = 'warehouse'; 
		$this->tableCustomer = 'customer'; 
		$this->tableSalesOrder= 'sales_order_header'; 
		$this->tableItemCategory = 'item_category'; 
		$this->tableItem= 'item'; 
		$this->tableBrand = 'brand'; 
		$this->tableMarketplace = 'marketplace'; 
		$this->securityObject = 'Campaign';
	    $this->isTransaction = true;  
       
        $arrCategoryDetail = array(); 
        $arrCategoryDetail['pkey'] = array('hidDetailCategoryKey');
        $arrCategoryDetail['refkey'] = array('pkey', 'ref');
        $arrCategoryDetail['categorykey'] = array('hidCategoryKey',array('mandatory'=>true));
       
        $arrMarketplaceDetail = array(); 
        $arrMarketplaceDetail['pkey'] = array('hidDetailMarketplaceKey');
        $arrMarketplaceDetail['refkey'] = array('pkey', 'ref');
        $arrMarketplaceDetail['marketplacekey'] = array('hidMarketplaceKey',array('mandatory'=>true));
       
        $arrBrandDetail = array(); 
        $arrBrandDetail['pkey'] = array('hidDetailBrandKey');
        $arrBrandDetail['refkey'] = array('pkey', 'ref');
        $arrBrandDetail['brandkey'] = array('hidBrandKey',array('mandatory'=>true));
       
        $arrItemDetail = array(); 
        $arrItemDetail['pkey'] = array('hidDetailItemKey');
        $arrItemDetail['refkey'] = array('pkey', 'ref');
        $arrItemDetail['itemkey'] = array('hidItemKey',array('mandatory'=>true));
       
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $arrCategoryDetail, 'tableName' => $this->tableCampaignItemCategory));
        array_push($arrDetails, array('dataset' => $arrBrandDetail, 'tableName' => $this->tableCampaignBrand));
        array_push($arrDetails, array('dataset' => $arrItemDetail, 'tableName' => $this->tableCampaignItem));
        array_push($arrDetails, array('dataset' => $arrMarketplaceDetail, 'tableName' => $this->tableCampaignMarketplace));
        
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');  
        $this->arrData['startdate'] = array('startDate','date');
        $this->arrData['enddate'] = array('endDate','date');   
        $this->arrData['margintype'] = array('selMarginType');
        $this->arrData['finalpricetype'] = array('selFinalPriceType');
        $this->arrData['margin'] = array('marginValue','number'); 
        $this->arrData['discounttype'] = array('selDiscountType');
        $this->arrData['discount'] = array('discountValue','number');   
        //$this->arrData['priceadjustmenttype'] = array('selPriceAdjustmentType');
        //$this->arrData['priceadjustment'] = array('priceAdjustment','number');    
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['allitem'] = array('chkAllItem'); 
        $this->arrData['allitemcategory'] = array('chkAllItemCategory'); 
        $this->arrData['allmarketplace'] = array('chkAllMarketplace'); 
        $this->arrData['allbrand'] = array('chkAllBrand'); 
       
           
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));        
        array_push($this->arrDataListAvailableColumn, array('code' => 'startdate','title' => 'startDate','dbfield' => 'startdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'enddate','title' => 'endDate','dbfield' => 'enddate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->newLoad = true;
        
        $this->overwriteConfig();
 
   }
    
	function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,  
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ',  
					'.$this->tableStatus.'
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
        
        ' .$this->criteria ;
         
        
        return $sql;
    }
    
    
	function validateForm($arr,$pkey = ''){ 
        
        $arrayToJs = parent::validateForm($arr,$pkey);  
                 	
        $name = $arr['name'];  
        $arrMarketplaceKey = $arr['hidMarketplaceKey'];
        $arrItemKey = $arr['hidItemKey'];
        $arrBrandKey = $arr['hidBrandKey'];
        $arrCategoryKey = $arr['hidCategoryKey'];
	 	 
	  	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['campaign'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['campaign'][2]);
		} 
         
        // kalo detail gk dicentang, harus diisi   );   
        if (empty($arr['chkAllMarketplace'])){ 
            for($i=0;$i<count($arrMarketplaceKey);$i++) 
                if (empty($arrMarketplaceKey[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['campaign'][3]); 	
                    break;
                }  
        }
        
        if (empty($arr['chkAllItem'])){
            for($i=0;$i<count($arrItemKey);$i++)  
                if (empty($arrItemKey[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['campaign'][5]); 	
                    break;
                } 
           
        }
        
        if (empty($arr['chkAllBrand'])){
            for($i=0;$i<count($arrBrandKey);$i++)  
                if (empty($arrBrandKey[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['campaign'][4]); 	
                    break;
                }  
        }
        
        if (empty($arr['chkAllItemCategory'])){
            for($i=0;$i<count($arrCategoryKey);$i++)  
                if (empty($arrCategoryKey[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['campaign'][6]); 	
                    break;
                }  
            
            
        }
        
		return $arrayToJs;
	 } 
    
    function validateConfirm($rsHeader){
        
    }
    
    function confirmTrans($rsHeader){ 
        
        $item = new Item();
        //$marketplace = new Marketplace();
        
		$id = $rsHeader[0]['pkey'];
 
        // loop utk setiap item 
        
        $criteria = '';
        
        $rsItem = $this->getItem($id); 
        $itemkey = ( !empty($rsItem) ) ? implode(',',array_column($rsItem,'itemkey')) : '\'\''; 
        
        // kalo suda define barang, abaikan merk dan kategori 
        if (!empty($rsItem)){  
                $criteria .= ' and '.$item->tableName.'.pkey in ('.$itemkey.')';  
        }else{
            

            if ($rsHeader[0]['allbrand'] == 0){
                $rsBrand  = $this->getBrand($id); 
                $brandkey = ( !empty($rsBrand) ) ?implode(',', array_column($rsBrand,'brandkey')) : '\'\''; 
                $criteria .= ' and '.$item->tableName.'.brandkey in ('.$brandkey.')'; 
            }

            if ($rsHeader[0]['allitemcategory'] == 0){
                $rsCategory = $this->getItemCategory($id); 
                $categorykey = ( !empty($rsCategory) ) ? implode(',',array_column($rsCategory,'categorykey')) : '\'\''; 
                $criteria .= ' and '.$item->tableName.'.categorykey in ('.$categorykey.')'; 
            }
  
            if ($rsHeader[0]['allitem'] == 0){    
                $criteria .= ' and '.$item->tableName.'.pkey in ('.$itemkey.')'; 
            }
            
        }
         
            
        $rsItem = $item->searchData($item->tableName.'.statuskey',1,true,$criteria);
        //$this->setLog($criteria,true);
        //$this->setLog($rsItem,true);
 
        // loop utk setiap marketplace
        $rsMarketplaceDetail = $this->getMarketplace($id);
        
        foreach($rsMarketplaceDetail as $marketplaceRow){
             
            //$rsMarketplace = $marketplace->getDataRowById($marketplaceRow['marketplacekey']);
            
            /*$priceAdjustmentType = $rsMarketplace[0]['priceadjustmenttype'];
            $priceAdjustment = $rsMarketplace[0]['priceadjustment'];*/

            foreach($rsItem as $itemRow){

                // reset data
                // adjustment tetep ambilnya dr marketplace
                
                
                $marginType = $rsHeader[0]['margintype'];
                $margin =  $rsHeader[0]['margin'];
                $finalPriceType = $rsHeader[0]['finalpricetype'];
                $discountType = $rsHeader[0]['discounttype'];
                $discount  = $rsHeader[0]['discount'];


                $campaignmarketplaceid = $marketplaceRow['marketplacecampaignid']; 
                
                $itemkey = $itemRow['pkey'];
                $marketplacekey = $marketplaceRow['marketplacekey'];
                
                $normalPrice = $itemRow['sellingprice'];
                $adjustedPrice = $itemRow['sellingprice'];
                $discountedPrice = $itemRow['sellingprice'];
                 
                 
                // MARGIN
                // utk fake discount
                if(!empty($margin)) 
                    $adjustedPrice = ($marginType == 2 ) ? ($adjustedPrice / (1-($margin/100))) : $adjustedPrice + $margin;  
               
                // PRICE ADJUSTMENT
                // utk biaya admin dsb adanya di MARKETPLACE
                /*if(!empty($priceAdjustment)) 
                    $priceAdjustment = ($priceAdjustmentType == 2 ) ? ($normalPrice * $priceAdjustment/100) : $priceAdjustment;  
               */

                // SELLING PRICE 
                if($finalPriceType == 1){
                    $discountedPrice = $normalPrice; 
                }else if($finalPriceType == 2 && !empty($discount)){ 
                    $discount = ($discountType == 2 ) ? ($adjustedPrice * $discount/100) : $discount;
                    $discountedPrice = $adjustedPrice - $discount; 
                }   
                
                
                // utk biaya admin dsb adanya di MARKETPLACE
                //$discountedPrice += $priceAdjustment;
 
                $sql = 'insert into '.$this->tableCampaignPrice.' 
                        (refkey,itemkey,marketplacekey, campaignmarketplaceid,normalprice, adjustedprice, discountedprice)
                        values 
                        (
                            '.$this->oDbCon->paramString($id).',
                            '.$this->oDbCon->paramString($itemkey).',
                            '.$this->oDbCon->paramString($marketplacekey).',
                            '.$this->oDbCon->paramString($campaignmarketplaceid).',
                            '.$this->oDbCon->paramString($normalPrice).',
                            '.$this->oDbCon->paramString($adjustedPrice).',
                            '.$this->oDbCon->paramString($discountedPrice).'
                        ) 
                        ';

                $this->oDbCon->execute($sql); 
            }
             
        }
         
        
    } 
    
    function cancelTrans($rsHeader,$copy){  
           
		$id = $rsHeader[0]['pkey'];
         
        $sql = 'delete from '.$this->tableCampaignPrice.' where refkey = ' .  $this->oDbCon->paramString($id) ;
        $this->oDbCon->execute($sql); 
         
		if ($copy)
			$this->copyDataOnCancel($id);	  
         
	} 
    
     
    
    function normalizeParameter($arrParam, $trim = false){  
         
        $arrParam = parent::normalizeParameter($arrParam,true);   
         
        // kalo pilih barang, semua brand sama kategori dihapus
        if (isset($arrParam['chkAllItem']) && empty($arrParam['chkAllItem'])){
            $arrParam['chkAllBrand']  = 1;
            $arrParam['chkAllItemCategory']  = 1; 
        }
            
        
        
        if (isset($arrParam['chkAllMarketplace']) && !empty($arrParam['chkAllMarketplace']))
            $arrParam['hidDetailMarketplaceKey'] = array();
        
        if (isset($arrParam['chkAllBrand']) && !empty($arrParam['chkAllBrand']))
            $arrParam['hidDetailBrandKey'] = array();
        
        if (isset($arrParam['chkAllItem']) && !empty($arrParam['chkAllItem']))
            $arrParam['hidDetailItemKey'] = array();
        
        if (isset($arrParam['chkAllItemCategory']) && !empty($arrParam['chkAllItemCategory']))
            $arrParam['hidDetailCategoryKey'] = array();
        
            
        
        return $arrParam;
        
    }
     
 
    function getItem($campaignkey = ''){ 
            
            $sql = 'select 
                    ' . $this->tableCampaignItem .'.* ,
                    ' . $this->tableItem .'.name as itemname
                from 
                    ' . $this->tableCampaignItem .',
                    ' . $this->tableItem .'
                where 
                    ' . $this->tableCampaignItem .'.itemkey = ' . $this->tableItem .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($campaignkey) ; 

        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs;
         
    }
    
        
    function getItemCategory($campaignkey = '',$categorykey = ''){ 
            
            $sql = 'select 
                    ' . $this->tableCampaignItemCategory .'.* ,
                    ' . $this->tableItemCategory .'.name as categoryname
                from 
                    ' . $this->tableCampaignItemCategory .',
                    ' . $this->tableItemCategory .'
                where 
                    ' . $this->tableCampaignItemCategory .'.categorykey = ' . $this->tableItemCategory .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($campaignkey) ; 

        
            if (!empty($categorykey))
                $sql .= ' and ' . $this->tableCampaignItemCategory.'.categorykey = '.$this->oDbCon->paramString($categorykey) ; 
        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs;
         
    }
     
      
    function getBrand($campaignkey = '',$brandkey = ''){ 
            
            $sql = 'select 
                    ' . $this->tableCampaignBrand.'.* ,
                    ' . $this->tableBrand .'.name as brandname
                from 
                    ' . $this->tableCampaignBrand .',
                    ' . $this->tableBrand .'
                where 
                    ' . $this->tableCampaignBrand .'.brandkey = ' . $this->tableBrand .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($campaignkey) ; 
         
            if (!empty($brandkey))
                $sql .= ' and ' . $this->tableCampaignBrand.'.brandkey = '.$this->oDbCon->paramString($brandkey) ; 
         
        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs;
         
    } 
    
      
    function getMarketplace($campaignkey = '',$marketplacekey = ''){ 
            
            $sql = 'select 
                    ' . $this->tableCampaignMarketplace.'.* ,
                    ' . $this->tableMarketplace .'.name as marketplacename
                from 
                    ' . $this->tableCampaignMarketplace .',
                    ' . $this->tableMarketplace .'
                where 
                    ' . $this->tableCampaignMarketplace .'.marketplacekey = ' . $this->tableMarketplace .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($campaignkey) ; 
         
            if (!empty($marketplacekey))
                $sql .= ' and ' . $this->tableCampaignMarketplace.'.marketplacekey = '.$this->oDbCon->paramString($marketplacekey) ; 
         
        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs;
         
    } 
    
    
    function getDiscountedValue($campaignkey,$arrDetailTransaction,$arrShipment){
         
        $campaignValue = array('value' => 0);
        if (!is_numeric($campaignkey)) return $campaignValue;
          
        $item = new Item();
        $rsCampaign = $this->getDataRowById($campaignkey);
        
        $eligibleAmount = 0;
        
        // kalo tipe campaign penjualan 
        
        if ($rsCampaign[0]['categorykey'] == VOUCHER_CATEGORY['sales']){
            
            foreach($arrDetailTransaction as $transactionRow){
                $itemkey = $transactionRow['itemkey'];
                $qty = $transactionRow['qty'];

                $rsItem = $item->getDataRowById($itemkey); 
                $sellingPrice = $rsItem[0]['sellingprice'];
                $amount = $qty * $sellingPrice;
                
                $campaignCriteria = array();
                $campaignCriteria['brandkey']= $rsItem[0]['brandkey'];
                $campaignCriteria['itemkey'] = $rsItem[0]['pkey'];
                $campaignCriteria['itemcategorykey']= $rsItem[0]['categorykey'];
                  
                $availableCampaign = $this->getAvailableCampaign(array(VOUCHER_CATEGORY['sales']),VOUCHER_TYPE['regular'],CUSTOMER_TYPE['enduser'], $campaignCriteria );
                $availableCampaign = array_column($availableCampaign,'pkey');
                
                // jika campaign tdk ada
                /*$this->setLog($campaignkey,true);
                $this->setLog($availableCampaign,true);*/
                if (!in_array($campaignkey, $availableCampaign)) continue;
                
                $eligibleAmount += $amount;
            }
             
            if ($eligibleAmount > 0){
                $minAmountOfTransaction = $rsCampaign[0]['minamount'];
                $maxDiscount = $rsCampaign[0]['maxdiscount'];
                $discType = $rsCampaign[0]['discounttype'];
                
                if ($minAmountOfTransaction != 0 && $eligibleAmount < $minAmountOfTransaction){
                    //nilai transaksi kurang dr min amount
                    
                }else{
                    if ($discType == 1){ 
                        $campaignValue['value'] = $rsCampaign[0]['value'];
                    }else{ 
                        $campaignValue['value'] = $rsCampaign[0]['value'] * $eligibleAmount / 100;
                        
                        if($maxDiscount > 0 && $campaignValue['value'] > $maxDiscount)
                            $campaignValue['value'] = $maxDiscount ;
                    } 
                }
                
                    
            }

        }
        
        return $campaignValue;
        
    }
    
     
     
   /* function getItemCampaignPrice($itemkey, $opt = array()){
        $item = new Item();
        
        // hanya berlaku utk harga jual satuan dasar
        $rsItem  = $item->getDataRowById($itemkey);
        $arrReturn = array('normalprice' => $rsItem[0]['sellingprice']);
        
        // cek di daftar campaign, order by startdate desc limit 1
         
        $sql = 'select '.$this->tableName.'.pkey  
                from 
                    '.$this->tableName.'
                    
                left join '.$this->tableCampaignMarketplace.' on '.$this->tableName.'.pkey = '.$this->tableCampaignMarketplace.'.refkey
                left join '.$this->tableMarketplace.' on '.$this->tableCampaignMarketplace.'.marketplacekey = '.$this->tableMarketplace.'.pkey 
                
                left join '.$this->tableCampaignItemCategory.' on '.$this->tableName.'.pkey =  '.$this->tableCampaignItemCategory.'.refkey
                left join '.$this->tableItemCategory.' on '.$this->tableCampaignItemCategory.'.marketplacekey = '.$this->tableItemCategory.'.pkey 
                 
                left join '.$this->tableCampaignItem.' on '.$this->tableName.'.pkey = '.$this->tableCampaignItem.'.refkey
                left join '.$this->tableItem.' on '.$this->tableCampaignItem.'.marketplacekey = '.$this->tableItem.'.pkey 
               
                left join '.$this->tableCampaignBrand.' on '.$this->tableName.'.pkey = '.$this->tableCampaignBrand.'.refkey
                left join '.$this->tableBrand.' on '.$this->tableCampaignBrand.'.marketplacekey = '.$this->tableBrand.'.pkey ';
        
        $sql .= '  and startdate >= now()  and enddate <= now() ';
      
        
        return $arrReturn;
    }*/
       
  }

?>