<?php
class Brand extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'brand';
		$this->tableNameDetail = 'brand_marketplace_detail';
        $this->tableMarketplaceStorefrontDetail = 'brand_storefront_detail';
        $this->tableMarketplaceStorefront = 'item_category_storefront';
		$this->tableMarketplace = 'marketplace';
		$this->tableStatus = 'master_status';
	    $this->tableLangValue = 'brand_lang';
		$this->securityObject = 'Brand'; 
		$this->newLoad = true; 
		$this->uploadFolder = 'brand/';
		$this->uploadCoverFolder = 'brand-cover/';
	   
        $this->importUrl = 'import/brand';
	   
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['marketplacekey'] = array('hidMarketplaceKey',array('mandatory'=>true));
        $this->arrDataDetail['marketplacebrandkey'] = array('hidMarketplaceBrandKey');
        $this->arrDataDetail['marketplacebrandname'] = array('marketplaceBrandName',array('mandatory'=>true));	
       
        $this->arrStorefrontDetail = array(); 
        $this->arrStorefrontDetail['pkey'] = array('hidDetailStorefrontKey');
        $this->arrStorefrontDetail['refkey'] = array('pkey','ref'); 
        $this->arrStorefrontDetail['refstorefrontkey'] = array('hidStorefrontKey',array('datatype'=>'raw','mandatory'=>true));
       

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
        array_push($arrDetails, array('dataset' => $this->arrStorefrontDetail, 'tableName' => $this->tableMarketplaceStorefrontDetail));
	   
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['shortdesc'] = array('txtShortDesc');
        $this->arrData['trdesc'] = array('txtDescription','raw');
        $this->arrData['publish'] = array('chkIsPublish'); 
        $this->arrData['orderlist'] = array('orderList','number');
        $this->arrData['image'] =  array('brand-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-brand-image-uploader', 'fileName' => 'brand-image-uploader')); 
        $this->arrData['imagecover'] = array('brand-image-cover-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadCoverFolder,  'token' => 'token-brand-image-cover-uploader', 'fileName' => 'brand-image-cover-uploader'));
        
            
       	$this->arrLockedTable = array();
        $defaultFieldName = 'brandkey';
        array_push($this->arrLockedTable, array('table'=>'item','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'car','field'=>$defaultFieldName));
              
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
  	   $this->includeClassDependencies(array(
              'Marketplace.class.php',  
              'ItemCategory.class.php',  
              'Storefront.class.php'
        ));

        $this->overwriteConfig();
	}
	
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.','.$this->tableStatus.' where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
 		' .$this->criteria ; 
		 
    }
    
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
        
		$name = $arr['name'];  
	 	 
	  	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['brand'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['brand'][2]);
		} 
        
        // VALIDASI KHUSUS KALO AD MARKETPLACE
		// $marketplace = new Marketplace();
        
        /*$marketplaceObjs = $marketplace->getMarketplaceObj();
        if(!empty($marketplaceObjs)){ 
            
            $hasEmptyField = false;
            
            foreach($arr['marketplaceBrandName'] as $marketplaceBrandName){
                if (empty($marketplaceBrandName)){
                    $hasEmptyField = true;
                    break;
                } 
            } 
            if($hasEmptyField)
                $this->addErrorList($arrayToJs,false,$this->errorMsg['marketplace'][4]); 
            
        }*/
		 
		return $arrayToJs;
	 }	 
 
    	 
	function delete($id, $forceDelete = false,$reason = ''){ 
		 
		try{			
				  
				$arrayToJs =  array();
			 	
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			
		 		 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);  
			
				$this->deleteFiles($id);
			
                $this->setTransactionLog(DELETE_DATA,$id);
        
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
    
    
    function getMarketplaceBrand($id, $marketplaceInformation = array(), $categoryInformation = array(), $criteria = '') {
         
        $marketplacekey = $marketplaceInformation['marketplaceKey'];
        $marketplaceProviderKey = $marketplaceInformation['marketplaceProviderKey'];
        
        $sql =  
            'select  
                '.$this->tableName.'.name as name ,
                '.$this->tableNameDetail.'.* 
                
            FROM 
                '.$this->tableName.',
                '.$this->tableNameDetail.' 
            WHERE
                '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and 
                '.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($id).'
                ';
        
        if (!empty($marketplacekey))
            $sql .= ' and ' .$this->tableNameDetail .'.marketplacekey = '.$this->oDbCon->paramString($marketplacekey);
            
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria;
          
        $rsBrand =  $this->oDbCon->doQuery($sql);
        
        // kalo shopee, cocokin ulang ke kategori
        if( !empty($rsBrand) &&  !empty($categoryInformation) && $marketplaceProviderKey == MARKETPLACE['shopee']){ 
            // cari marketplacecategory dalam format json
            
            if(!isset($categoryInformation['marketplacecategorykey']) || empty($categoryInformation['marketplacecategorykey'])){
                $itemCategory = new ItemCategory();
                $rsMarketplaceCategory = $itemCategory->getMarketplaceCategory($categorykey, $marketplacekey);
                $marketplacecategorykey = (!empty($rsMarketplaceCategory)) ? $rsMarketplaceCategory[0]['marketplacecategorykey'] : 0; 
            }else{
                $marketplacecategorykey = $categoryInformation['marketplacecategorykey'];
            }
                
            $dbCon = $this->masterConn(); 
            $sql = 'select
                        marketplacebrandkey, name 
                    from
                        marketplace_brand
                    where 
                        name = '.$this->oDbCon->paramString($rsBrand[0]['marketplacebrandname']).' and 
                        marketplacecategoryid like \'%"'.intval($marketplacecategorykey).'"%\' and 
                        marketplacekey = '.MARKETPLACE['shopee'] ;
                         
            $rsBrand = $dbCon->doQuery($sql); 
              
            $dbCon = null; 
               
        }
        
        return $rsBrand;
    }

	
	 function getMarketplaceStorefront($pkey='',$marketplacekey =''){
        $sql = 'select 
                    '.$this->tableMarketplaceStorefrontDetail.'.*
                from 
                    '.$this->tableMarketplaceStorefrontDetail.' 
                where 
					1=1';
        
        if(!empty($pkey))
            $sql .= ' and '.$this->tableMarketplaceStorefrontDetail.'.refkey = ' .$this->oDbCon->paramString($pkey);
        
		$rs = $this->oDbCon->doQuery($sql);
		 
		$arrStorefrontKey = array();
		foreach($rs as $row){ 
			
			// utk compatibility 
			$arrKeys = json_decode($row['refstorefrontkey']);
			if(empty($arrKeys)) continue;
			
			if(!is_array($arrKeys)) $arrKeys = array($arrKeys);
			 
			$arrStorefrontKey = array_merge($arrStorefrontKey, $arrKeys);
		}
		 
		 
		// harus pisahin manual karena format storefrontkey ny JSON  
		 
			$sql = 'select pkey,marketplacekey,marketplacestorefrontkey from '.$this->tableMarketplaceStorefront.' where '.$this->tableMarketplaceStorefront.'.pkey in ('.$this->oDbCon->paramString($arrStorefrontKey,',').') ';
		   	
		 	if(!empty($marketplacekey))
			   $sql .= ' and '.$this->tableMarketplaceStorefront.'.marketplacekey = ' .$this->oDbCon->paramString($marketplacekey);
			
		 	$rsStorefront = $this->oDbCon->doQuery($sql); 
			$rsStorefrontKey = array_column($rsStorefront,'pkey'); 
			$rsStorefrontCol = array_column($rsStorefront,null,'pkey'); 
			 
			foreach($rs as $key=>$row){ 
				 
				// utk compatibility 
				$arrKeys = json_decode($row['refstorefrontkey']);
				if(!is_array($arrKeys)) $arrKeys = array($arrKeys);

				if(!in_array( $arrKeys[0] ,$rsStorefrontKey)) { 
					unset($rs[$key]); 
				}else{ 
					$rs[$key]['marketplacekey'] = $rsStorefrontCol[$arrKeys[0]]['marketplacekey'];
					
					$marketplacestorefrontkey = array();
					foreach($arrKeys as $refstorefrontkey)
						array_push($marketplacestorefrontkey,$rsStorefrontCol[$refstorefrontkey]['marketplacestorefrontkey']);
					
					$rs[$key]['marketplacestorefrontkey'] = (!empty($marketplacestorefrontkey)) ? json_encode($marketplacestorefrontkey) : '';
				}
			}

			$rs = array_values($rs);
	   
        return $rs;
    }
		
    function normalizeParameter($arrParam, $trim = false){ 
        
//         $arrParam['fileName'] = (isset( $arrParam['token-item-image-uploader'])) ? $this->updateImage($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']) : '' ;

		// handling manual, karena nama variabelnya sama, jd harus dipisah manual
		if($this->isActiveModule('marketplace')){
            
            // HARUS ISI ULANG KALO AD MARKETPLACE YG DINONAKTIFKAN SEMENTARA
            // AGAR KETIKA DIAKTIFKAN LG, MAPPINGNYA TDK HILANG
            
            // select detail mapping marketplace
            // kalo gk isset untuk hidMarketplacekey ny, isi ulang
 
            $rsStorefrontDetail = $this->getMarketplaceStorefront($arrParam['hidId']);   
            $rsStorefrontDetail = array_column($rsStorefrontDetail, null, 'marketplacekey'); 
 
            
            // kalo marketplacenya di nonaktifkan, isi ulang biar gk hilang
            foreach($rsStorefrontDetail as $marketplacekey=>$categoryRow){
                if (!in_array( $marketplacekey, $arrParam['hidStoreFrontMarketplaceKey'])){
                    array_push($arrParam['hidStoreFrontMarketplaceKey'], $marketplacekey);
                    array_push($arrParam['hidDetailStorefrontKey'], $categoryRow['pkey']);
                    
                    // khusus storefront agak beda perlakukannya
                    $tempStorefront = json_decode($categoryRow['refstorefrontkey'],true);
                    foreach($tempStorefront as $storefrontkey)
                        array_push($arrParam['hidStorefrontKey'], $storefrontkey);
                    
                }   
            }
            
            
			$arrStorefrontKey = $arrParam['hidStorefrontKey']; 
			$arrStorefrontMarketplaceKey = $arrParam['hidStoreFrontMarketplaceKey'];
			
			// cari relasi stoefrontkey dengan marketplacenya
			$storefront = new Storefront();
			$rsStorefront = $storefront->searchDataRow(array($storefront->tableName.'.pkey',$storefront->tableName.'.marketplacekey'),
													   ' and '.$storefront->tableName.'.pkey in ('.$this->oDbCon->paramString($arrStorefrontKey,',').')'
													  ); 
			
			$rsStorefront = $this->reindexDetailCollections($rsStorefront,'marketplacekey');   
			
			// reset 
			for($i=0;$i<count($arrStorefrontMarketplaceKey);$i++)  { 
				$arrStorefrontKey = array_column($rsStorefront[$arrStorefrontMarketplaceKey[$i]],'pkey'); 
				$arrParam['hidStorefrontKey'][$i] = !(empty($arrStorefrontKey)) ? json_encode($arrStorefrontKey) : '' ; 
			}

		} 
		 
         $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
         $arrParam = parent::normalizeParameter($arrParam,true); 
          
         return $arrParam; 
    }
	
	
}
?>