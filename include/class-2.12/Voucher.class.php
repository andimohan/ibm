<?php

class Voucher extends BaseClass{
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'voucher'; 
		$this->tableVoucherTransaction = 'voucher_transaction'; 
		$this->tableVoucherItem = 'voucher_detail_item'; 
		$this->tableVoucherItemCategory = 'voucher_detail_item_category'; 
		$this->tableVoucherCity = 'voucher_detail_city'; 
		$this->tableVoucherCityCategory = 'voucher_detail_city_category'; 
		$this->tableVoucherLocation = 'voucher_detail_location'; 
		$this->tableVoucherBrand = 'voucher_detail_brand'; 
		$this->tableStatus = 'transaction_status';
		$this->tableCity = 'city';
        $this->tableCityCategory = 'city_category';
		$this->tableLocation = 'location';
		$this->tableType = 'voucher_type'; 
        $this->tableCategory = 'voucher_category';
		$this->tableWarehouse = 'warehouse'; 
		$this->tableCustomer = 'customer'; 
		$this->tableSalesOrder= 'sales_order_header'; 
		$this->tableItemCategory = 'item_category'; 
		$this->tableItem= 'item'; 
		$this->tableBrand = 'brand'; 
		$this->securityObject = 'Voucher';
	    $this->isTransaction = true; 
		
        $arrCityDetail = array(); 
        $arrCityDetail['pkey'] = array('hidDetailKey');
        $arrCityDetail['refkey'] = array('pkey', 'ref');
        $arrCityDetail['citykey'] = array('hidCityKey',array('mandatory'=>true));
       
        $arrCityCategoryDetail = array(); 
        $arrCityCategoryDetail['pkey'] = array('hidDetailKey');
        $arrCityCategoryDetail['refkey'] = array('pkey', 'ref');
        $arrCityCategoryDetail['categorykey'] = array('hidCityCategoryKey',array('mandatory'=>true));
       
        $arrCategoryDetail = array(); 
        $arrCategoryDetail['pkey'] = array('hidDetailCategoryKey');
        $arrCategoryDetail['refkey'] = array('pkey', 'ref');
        $arrCategoryDetail['categorykey'] = array('hidCategoryKey',array('mandatory'=>true));
       
        $arrBrandDetail = array(); 
        $arrBrandDetail['pkey'] = array('hidDetailBrandKey');
        $arrBrandDetail['refkey'] = array('pkey', 'ref');
        $arrBrandDetail['brandkey'] = array('hidBrandKey',array('mandatory'=>true));
       
        $arrItemDetail = array(); 
        $arrItemDetail['pkey'] = array('hidDetailItemKey');
        $arrItemDetail['refkey'] = array('pkey', 'ref');
        $arrItemDetail['itemkey'] = array('hidItemKey',array('mandatory'=>true));
       
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $arrCityDetail, 'tableName' => $this->tableVoucherCity));
        array_push($arrDetails, array('dataset' => $arrCityCategoryDetail, 'tableName' => $this->tableVoucherCityCategory));
        array_push($arrDetails, array('dataset' => $arrCategoryDetail, 'tableName' => $this->tableVoucherItemCategory));
        array_push($arrDetails, array('dataset' => $arrBrandDetail, 'tableName' => $this->tableVoucherBrand));
        array_push($arrDetails, array('dataset' => $arrItemDetail, 'tableName' => $this->tableVoucherItem));
		
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['alias'] = array('alias');
        $this->arrData['typekey'] = array('selType');
        //$this->arrData['warehousekey'] = array('selWarehouse'); 
        $this->arrData['startdate'] = array('startDate','date');
        $this->arrData['enddate'] = array('endDate','date'); 
        $this->arrData['minamount'] = array('minAmount','number');
        $this->arrData['maxdiscount'] = array('maxDiscount','number');
        $this->arrData['qty'] = array('qty','number');
        //$this->arrData['used'] = array('qtyused','number'); // biar gk keupdate
        $this->arrData['discounttype'] = array('selDiscountType');
        $this->arrData['value'] = array('value','number');
        $this->arrData['business'] = array('businessType');
        $this->arrData['combine'] = array('combineType');
        $this->arrData['trdesc'] = array('trDesc','raw');
        $this->arrData['shortdesc'] = array('shortDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['customertypekey'] = array('customerType'); 
        $this->arrData['customerkey'] = array('hidCustomerKey'); 
        $this->arrData['categorykey'] = array('selCategory'); 
        $this->arrData['pointneeded'] = array('pointNeeded'); 
        $this->arrData['canuseindiscounted'] = array('chkCanUseInDiscounted'); 
        $this->arrData['onetimeuse'] = array('chkOneTimeUse'); 
		
		$this->newLoad = true;
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));        
        array_push($this->arrDataListAvailableColumn, array('code' => 'startdate','title' => 'startDate','dbfield' => 'startdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'enddate','title' => 'endDate','dbfield' => 'enddate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'vouchertype','title' => 'type','dbfield' => 'vouchertype','default'=>true,  'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'vouchercategory','title' => 'category','dbfield' => 'vouchercategory','default'=>true,  'width' => 100));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100)); 
        //array_push($this->arrDataListAvailableColumn, array('code' => 'value','title' => 'value','dbfield' => 'valuewithunit','default'=>true, 'width' => 80, 'align' => 'right', 'format'=>'number'));   
		array_push($this->arrDataListAvailableColumn, array('code' => 'qty','title' => 'issued','dbfield' => 'qty','default'=>true, 'width' =>90, 'align' => 'right', 'format'=>'number'));   
		array_push($this->arrDataListAvailableColumn, array('code' => 'qtyUsed','title' => 'used','dbfield' => 'qtyused','default'=>true, 'width' => 70, 'align' => 'right', 'format'=>'number'));   
		array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
		array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        
        $this->includeClassDependencies(array(
            'Warehouse.class.php',  
            'City.class.php', 
            'Customer.class.php', 
            'VoucherTransaction.class.php'
        ));   

        $this->overwriteConfig(); 

       
   }
    
	function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*, 
					'.$this->tableType. '.name as vouchertype,
					'.$this->tableCategory. '.name as vouchercategory, 
                    '.$this->tableCustomer.'.code as customercode,
                    '.$this->tableCustomer.'.name as customername,
                    '.$this->tableCustomer.'.email as customeremail,
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . '
                        left join '.$this->tableCustomer.' on '.$this->tableCustomer.'.pkey = '.$this->tableName. '.customerkey
						left join '.$this->tableType.' on '.$this->tableName . '.typekey = '.$this->tableType.'.pkey 
						left join '.$this->tableCategory.' on '.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey ,  
					'.$this->tableStatus.'
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
					 
        
        ' .$this->criteria ;
        
        return $sql;
    }
    
    function updateQtyUsed($pkey){
        $arrayToJs =  array();
		
		try{ 
            
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
			 
            $rs = $this->getDataRowById($pkey);   

			$sql = 'update '.$this->tableName.' set qtyused = ( 
                            select count(pkey) as qtyused 
                            from '.$this->tableVoucherTransaction.' 
                            where refvoucherkey = '.$this->oDbCon->paramString($pkey).' and statuskey in (2,3) 
                        ) 
                    where   pkey = '.$this->oDbCon->paramString($pkey); 
            
//            $this->setLog($sql,true);
            $this->oDbCon->execute($sql);
            
            $maxqty = $this->unFormatNumber($rs[0]['qty']);
            $qtyused = $this->unFormatNumber($rs[0]['qtyused']);
            
            if($maxqty > 0){  
                $statuskey = ($qtyused < $maxqty) ? 2 : 3 ; 
                if ($rs[0]['statuskey'] <> $statuskey)
                    $this->changeStatus($pkey,$statuskey,'',true);
            }
                        
		    $this->oDbCon->endTrans();
					  
					 
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
		
		return $arrayToJs; 	 	
    }
     
    function afterStatusChanged($rsHeader){ 
    
            
    }
    
    
	function validateForm($arr,$pkey = ''){ 
        
        $arrayToJs = parent::validateForm($arr,$pkey);  
                 
        $value = $this->unFormatNumber($arr['value']); 
           
        if($value <= 0)  
            $this->addErrorList($arrayToJs,false,$this->errorMsg['voucher'][2]);
      
		return $arrayToJs;
	 } 
    
    function validateConfirm($rsHeader){
        
    }
    
    function validateCancel($rsHeader, $autoChangeStatus = false){
        $id = $rsHeader[0]['pkey'];
        $voucherTransaction = new VoucherTransaction();
        $rsVoucher = $voucherTransaction->searchData('','',true,' and refvoucherkey = '.$this->oDbCon->paramString($id).' and '.$voucherTransaction->tableName.'.statuskey in (2,3)');
        if(!empty($rsVoucher)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['voucher'][2],true);

     } 
     
    function cancelTrans($rsHeader,$copy){  
		$id = $rsHeader[0]['pkey'];
        $voucherTransaction = new VoucherTransaction();
        $rsVoucher = $voucherTransaction->searchData('','',true,' and refvoucherkey = '.$this->oDbCon->paramString($id).' and '.$voucherTransaction->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsVoucher);$i++) { 
            $arrayToJs = $voucherTransaction->changeStatus($rsVoucher[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }

		if ($copy)
			$this->copyDataOnCancel($id);	  
         
	} 
    
    function normalizeParameter($arrParam, $trim = false){  
             
        $arrParam = parent::normalizeParameter($arrParam,true);  
         
        return $arrParam;
        
    }
    
    function getVoucherType(){
        $sql = 'select * from '.$this->tableType.''; 
        return $this->oDbCon->doQuery($sql);
    }
    
    
    function getVoucherCategory(){
        $sql = 'select * from '.$this->tableCategory.' where statuskey = 1'; 
        return $this->oDbCon->doQuery($sql);
    }
    
    /*
    function updateDetail($pkey,$arrParam){
        $this->updateItem($pkey, $arrParam);
        $this->updateItemCategory($pkey, $arrParam);
        $this->updateBrand($pkey, $arrParam);
        $this->updateLocation($pkey, $arrParam);

    }
    */
 
    function getItem($voucherkey = ''){ 
            
            $sql = 'select 
                    ' . $this->tableVoucherItem .'.* ,
                    ' . $this->tableItem .'.name as itemname
                from 
                    ' . $this->tableVoucherItem .',
                    ' . $this->tableItem .'
                where 
                    ' . $this->tableVoucherItem .'.itemkey = ' . $this->tableItem .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($voucherkey) ; 

        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs;
         
    }
    
        
    function getItemCategory($voucherkey = '',$categorykey = ''){ 
            
            $sql = 'select 
                    ' . $this->tableVoucherItemCategory .'.* ,
                    ' . $this->tableItemCategory .'.name as categoryname
                from 
                    ' . $this->tableVoucherItemCategory .',
                    ' . $this->tableItemCategory .'
                where 
                    ' . $this->tableVoucherItemCategory .'.categorykey = ' . $this->tableItemCategory .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($voucherkey) ; 

        
            if (!empty($categorykey))
                $sql .= ' and ' . $this->tableVoucherItemCategory.'.categorykey = '.$this->oDbCon->paramString($categorykey) ; 
        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs;
         
    }
    
    function getCity($voucherkey = ''){ 
            
            $sql = 'select 
                    ' . $this->tableVoucherCity.'.* ,
                    ' . $this->tableCity .'.name as cityname
                from 
                    ' . $this->tableVoucherCity .',
                    ' . $this->tableCity .'
                where 
                    ' . $this->tableVoucherCity .'.citykey = ' . $this->tableCity .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($voucherkey) ; 

        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs;
         
    }
    
    function getCityCategory($voucherkey = ''){ 
            
            $sql = 'select 
                    ' . $this->tableVoucherCityCategory.'.* ,
                    ' . $this->tableCityCategory .'.name as categoryname
                from 
                    ' . $this->tableVoucherCityCategory .',
                    ' . $this->tableCityCategory .'
                where 
                    ' . $this->tableVoucherCityCategory .'.categorykey = ' . $this->tableCityCategory .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($voucherkey) ; 

        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs;
         
    }
    
    function getLocation($voucherkey = ''){ 
            
            $sql = 'select 
                    ' . $this->tableVoucherLocation.'.* ,
                    ' . $this->tableLocation .'.name as locationname
                from 
                    ' . $this->tableVoucherLocation .',
                    ' . $this->tableLocation .'
                where 
                    ' . $this->tableVoucherLocation .'.locationkey = ' . $this->tableLocation .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($voucherkey) ; 

        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs;
         
    }
      
    function getBrand($voucherkey = '',$brandkey = ''){ 
            
            $sql = 'select 
                    ' . $this->tableVoucherBrand.'.* ,
                    ' . $this->tableBrand .'.name as brandname
                from 
                    ' . $this->tableVoucherBrand .',
                    ' . $this->tableBrand .'
                where 
                    ' . $this->tableVoucherBrand .'.brandkey = ' . $this->tableBrand .'.pkey and
                    refkey = ' . $this->oDbCon->paramString($voucherkey) ; 
         
            if (!empty($brandkey))
                $sql .= ' and ' . $this->tableVoucherBrand.'.brandkey = '.$this->oDbCon->paramString($brandkey) ; 
         
        
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs;
         
    }
    
    function getAvailableVoucher($arrOption){
        // voucher berlaku utk semua jika tidak ad kriteria 
        $voucherTransaction = new VoucherTransaction();
        
		$category = isset($arrOption['category']) ? $arrOption['category'] : '';
		$voucherType = isset($arrOption['voucherType']) ? $arrOption['voucherType'] : '';
		$userkey = isset($arrOption['userkey']) ? $arrOption['userkey'] : ''; 
		$removeUnavailable = isset($arrOption['removeUnavailable']) ? $arrOption['removeUnavailable'] : false;
		
		if($voucherType != '' && !is_array($voucherType)) $voucherType = array($voucherType);
		
		$customerType = isset($arrOption['customerType']) ? $arrOption['customerType'] : '';
		 
		$rs = array();
		if(in_array(VOUCHER_TYPE['collectible'],$voucherType)){
			$sql = 'select 
						'.$this->tableName.'.pkey,
						'.$this->tableName.'.code,
						'.$this->tableName.'.minamount,
						'.$this->tableName.'.name,
						'.$this->tableName.'.shortdesc,
						'.$this->tableName.'.trdesc,
						'.$this->tableName.'.startdate,
						'.$this->tableName.'.enddate, 
						'.$this->tableName.'.typekey,
						'.$this->tableName.'.categorykey,
                        '.$this->tableName.'.qtyused,
				        '.$this->tableName.'.qty,
						'.$this->tableVoucherItem .'.itemkey,
						'.$this->tableVoucherItemCategory.'.categorykey as itemcategorykey,
						'.$this->tableVoucherCity.'.citykey,
						'.$this->tableVoucherCityCategory.'.categorykey as citycategorykey,
						'.$this->tableVoucherBrand.'.brandkey 
					from 
						'.$this->tableName.' 
							left join '.$this->tableVoucherItem .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherItem .'.refkey
							left join '.$this->tableVoucherItemCategory .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherItemCategory .'.refkey
							left join '.$this->tableVoucherCity .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherCity .'.refkey
							left join '.$this->tableVoucherCityCategory .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherCityCategory .'.refkey
							left join '.$this->tableVoucherBrand .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherBrand .'.refkey 
					where
						'.$this->tableName.'.statuskey = 2 and 
						curdate() between '.$this->tableName.'.startdate and '.$this->tableName.'.enddate and 
						('.$this->tableName.'.qtyused < '.$this->tableName.'.qty  or '.$this->tableName.'.qty = 0) 
					';

			$sql .= ' and '.$this->tableName.'.typekey = ' .$this->oDbCon->paramString(VOUCHER_TYPE['collectible']) ; 
			
			if(!empty($category)){ 
				if(!is_array($category))  $category = array($category); 
				$sql .= ' and '.$this->tableName.'.categorykey in ('. implode(',',$category).')' ; 
			}
 

			if(!empty($customerType))
				$sql .= ' and '.$this->tableName.'.customertypekey = '.$this->oDbCon->paramString($customerType) ; 

			$sql .= ' order by '.$this->tableName.'.startdate asc, '.$this->tableName.'.pkey asc';
            
			$rsCollectible = $this->oDbCon->doQuery($sql);
            
            // USERKKEY => user dari frontend 
            if (!empty(USERKEY)) 
                $rsCollectible = $this->removeOneTimeUse($rsCollectible); 
             
			$rs = array_merge($rs,$rsCollectible);
		}
		
		// kalo termasuk voucher reguler
        // voucher REGULAR  adalah voucher yagn didapat user dengan melakukan penukaran point yagn telah dikumpulkan
        // dan berlaku hanya utk user tersebut
		if(in_array(VOUCHER_TYPE['regular'],$voucherType)){
			 $sql = 'select 
                    '.$this->tableVoucherTransaction.'.pkey,
                    '.$this->tableVoucherTransaction.'.code,
                    '.$this->tableVoucherTransaction.'.minamount,
                    '.$this->tableName.'.name,
                    '.$this->tableName.'.shortdesc,
					'.$this->tableName.'.trdesc,
                    '.$this->tableName.'.startdate,
                    '.$this->tableVoucherTransaction.'.expdate as enddate, 
                    '.$this->tableName.'.typekey,
				    '.$this->tableName.'.categorykey,
				    '.$this->tableName.'.qtyused,
				    '.$this->tableName.'.qty,
                    '.$this->tableVoucherItem .'.itemkey,
                    '.$this->tableVoucherItemCategory.'.categorykey as itemcategorykey,
                    '.$this->tableVoucherCity.'.citykey,
                    '.$this->tableVoucherCityCategory.'.categorykey as citycategorykey,
                    '.$this->tableVoucherBrand.'.brandkey 
                from 
					'.$this->tableVoucherTransaction.',
                    '.$this->tableName.' 
                        left join '.$this->tableVoucherItem .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherItem .'.refkey
                        left join '.$this->tableVoucherItemCategory .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherItemCategory .'.refkey
                        left join '.$this->tableVoucherCity .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherCity .'.refkey
                        left join '.$this->tableVoucherCityCategory .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherCityCategory .'.refkey
                        left join '.$this->tableVoucherBrand .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherBrand .'.refkey 
                where
					'.$this->tableVoucherTransaction.'.refvoucherkey =  '.$this->tableName.'.pkey and
                    '.$this->tableVoucherTransaction.'.statuskey = 2 and 
                    '.$this->tableVoucherTransaction.'.customerkey = '.$this->oDbCon->paramString($userkey).'  and  
                    curdate() <= '.$this->tableVoucherTransaction.'.expdate 
                ';
        
           $sql .= ' and '.$this->tableName.'.typekey = ' .$this->oDbCon->paramString(VOUCHER_TYPE['regular']) ; 
			
			if(!empty($category)){ 
				if(!is_array($category))  $category = array($category); 
				$sql .= ' and '.$this->tableName.'.categorykey in ('. implode(',',$category).')' ; 
			}
 

			if(!empty($customerType))
				$sql .= ' and '.$this->tableName.'.customertypekey = '.$this->oDbCon->paramString($customerType) ; 

			$sql .= ' order by '.$this->tableVoucherTransaction.'.expdate asc, '.$this->tableVoucherTransaction.'.pkey asc'; 

			$rsReguler = $this->oDbCon->doQuery($sql);
            
             // USERKKEY => user dari frontend 
            // regular harus ambil dr obj voucher transaction
            
            if (!empty(USERKEY)) 
                $rsReguler = $voucherTransaction->removeOneTimeUse($rsReguler);
            
			$rs = array_merge($rs,$rsReguler);
		}
         
		// kalo termasuk voucher reguler
        // voucher CLAIM  adalah voucher yagn didapat user dengan melakukan penginputan kode voucher
        // dan berlaku hanya utk user tersebut
		if(in_array(VOUCHER_TYPE['claim'],$voucherType)){
			 $sql = 'select 
                    '.$this->tableVoucherTransaction.'.pkey,
                    '.$this->tableVoucherTransaction.'.code,
                    '.$this->tableVoucherTransaction.'.minamount,
                    '.$this->tableName.'.name,
                    '.$this->tableName.'.shortdesc,
                    '.$this->tableName.'.trdesc,
                    '.$this->tableName.'.startdate,
                    '.$this->tableVoucherTransaction.'.expdate as enddate, 
                    '.$this->tableName.'.typekey,
                    '.$this->tableVoucherTransaction.'.refvoucherkey,
				    '.$this->tableName.'.categorykey,
                    1 as qtyused,
                    1 as qty,
                    '.$this->tableVoucherItem .'.itemkey,
                    '.$this->tableVoucherItemCategory.'.categorykey as itemcategorykey,
                    '.$this->tableVoucherCity.'.citykey,
                    '.$this->tableVoucherCityCategory.'.categorykey as citycategorykey,
                    '.$this->tableVoucherBrand.'.brandkey 
                from 
					'.$this->tableVoucherTransaction.',
                    '.$this->tableName.' 
                        left join '.$this->tableVoucherItem .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherItem .'.refkey
                        left join '.$this->tableVoucherItemCategory .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherItemCategory .'.refkey
                        left join '.$this->tableVoucherCity .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherCity .'.refkey
                        left join '.$this->tableVoucherCityCategory .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherCityCategory .'.refkey
                        left join '.$this->tableVoucherBrand .' on  '.$this->tableName.'.pkey = '.$this->tableVoucherBrand .'.refkey 
                where
					'.$this->tableVoucherTransaction.'.refvoucherkey =  '.$this->tableName.'.pkey and
                    '.$this->tableVoucherTransaction.'.statuskey = 2 and 
                    '.$this->tableVoucherTransaction.'.customerkey = '.$this->oDbCon->paramString($userkey).'  and  
                    curdate() <= '.$this->tableVoucherTransaction.'.expdate 
                ';
        
           $sql .= ' and '.$this->tableName.'.typekey = ' .$this->oDbCon->paramString(VOUCHER_TYPE['claim']) ; 
			
			if(!empty($category)){ 
				if(!is_array($category))  $category = array($category); 
				$sql .= ' and '.$this->tableName.'.categorykey in ('. implode(',',$category).')' ; 
			}
 

			if(!empty($customerType))
				$sql .= ' and '.$this->tableName.'.customertypekey = '.$this->oDbCon->paramString($customerType) ; 

			$sql .= ' order by '.$this->tableVoucherTransaction.'.expdate asc, '.$this->tableVoucherTransaction.'.pkey asc'; 

			$rsReguler = $this->oDbCon->doQuery($sql);
            
             // USERKKEY => user dari frontend 
            // regular harus ambil dr obj voucher transaction
            
            if (!empty(USERKEY)) 
                $rsReguler = $voucherTransaction->removeOneTimeUse($rsReguler);
            
			$rs = array_merge($rs,$rsReguler);
		}
		
        $arrKeys = array();
        $arrReturn = array();
         
        foreach($rs as $row){
            
			// harus pake index , karen join, ad kemungkinan pkeynya sama, cuma beda di typekey
            if(in_array($row['pkey'].'-'.$row['typekey'],$arrKeys)) continue;
            
            // kalo gk ad criteria apa2.. push
            
            if (!empty($row['itemkey'])  && isset($arrOption['itemkey'])){
                $voucherItemKey = $row['itemkey'];
                $items = (!is_array($arrOption['itemkey'])) ? array($arrOption['itemkey']): $arrOption['itemkey'];
                if(!in_array($voucherItemKey, $items))  continue;  
            }  
            
            if (!empty($row['itemcategorykey'])  && isset($arrOption['itemcategorykey'])){ 
                
                $voucherItemCategoryKey = $row['itemcategorykey'];
                $itemsCategory = (!is_array($arrOption['itemcategorykey'])) ? array($arrOption['itemcategorykey']): $arrOption['itemcategorykey'];
                
                // ambil category item ke parent2nya...
                $itemCategory = new ItemCategory();
                $arrCategoryPath = array();
                
                foreach($itemsCategory as $categorykey){
                    $rsPath = $itemCategory->getPath($categorykey); 
                    $arrCategoryPath += array_column($rsPath,'pkey'); // test merge unique array
                    
                    if(!in_array($categorykey,$arrCategoryPath ))
                        array_push($arrCategoryPath,$categorykey);  
                }
                 
                if(!in_array($voucherItemCategoryKey, $arrCategoryPath)) continue; 
                  
            }   
             
                       
            if (!empty($row['brandkey']) && isset($arrOption['brandkey'])){ 
                $voucherBrandKey = $row['brandkey'];
                $brands = (!is_array($arrOption['brandkey'])) ? array($arrOption['brandkey']): $arrOption['brandkey'];
                 
                if(!in_array($voucherBrandKey, $brands))  continue;  
            }  
    
            // kalo dikirim criteria min amount baru dicek 
            if (!empty($row['minamount']) && isset($arrOption['totalsales'])){ 
                $totalSales = (isset($arrOption['totalsales']) && !empty($arrOption['totalsales'])) ? $this->unFormatNumber($arrOption['totalsales']) : 0; 
                if( $totalSales < $row['minamount'])  { 
                    if($removeUnavailable) continue;
                    else  $row['isAvailable'] = false; 
                }
            }  
            $row['index'] = $row['pkey'].'-'.$row['typekey'];
            $row['percentageused'] = ($row['qty'] == 0) ? 0 : $row['qtyused'] / $row['qty'] * 100; // gk boleh 100%, karena kalo gk ad batas, user akan isi 0 qty nya
            
            array_push($arrKeys, $row['pkey'].'-'.$row['typekey']); 
            
            if (!isset($row['isAvailable']))  $row['isAvailable'] = true;
            
            array_push($arrReturn, $row);     
        }
        
        return $arrReturn;
    }
    
    function checkHasPromo($itemRow){ 
        $arrHasPromo = array('sales' => false, 'shipping' => false);
        $rsVoucher = $this->getAvailableVoucher(array('category' => array(VOUCHER_CATEGORY['sales']),
													   'voucherType' => VOUCHER_TYPE['regular'],
													   'customerType' => CUSTOMER_TYPE['enduser'], 
													   'brandkey' => $itemRow['brandkey'], 
													   'itemkey' => $itemRow['pkey'], 
													   'itemcategorykey' => $itemRow['categorykey']
													 ) 
											   );
        if(!empty($rsVoucher)) 
            $arrHasPromo['sales'] = true;

        $rsVoucher = $this->getAvailableVoucher( array('category' => array(VOUCHER_CATEGORY['shipment']),
													   'voucherType' => VOUCHER_TYPE['regular'],
													   'customerType' => CUSTOMER_TYPE['enduser'],
													   'brandkey' => $itemRow['brandkey'], 
													   'itemkey' => $itemRow['pkey'], 
													   'itemcategorykey' => $itemRow['categorykey']
												) );
        if(!empty($rsVoucher)) 
            $arrHasPromo['shipping'] = true; 
        
        return $arrHasPromo;
    }
    
    function getVoucherValue($voucherkey,$arrDetailTransaction,$arrShipment){
         
        $voucherValue = array('value' => 0);
        if (!is_numeric($voucherkey)) return $voucherValue;
          
        $item = new Item();
        $rsVoucher = $this->getDataRowById($voucherkey);
        
        $eligibleAmount = 0;
        
        // kalo tipe voucher penjualan 
        
        if ($rsVoucher[0]['categorykey'] == VOUCHER_CATEGORY['sales']){
            
            foreach($arrDetailTransaction as $transactionRow){
                $itemkey = $transactionRow['itemkey'];
                $qty = $transactionRow['qty'];

                $rsItem = $item->getDataRowById($itemkey); 
                $sellingPrice = $rsItem[0]['sellingprice'];
                $amount = $qty * $sellingPrice;
                  
                $availableVoucher = $this->getAvailableVoucher(array('category' => array(VOUCHER_CATEGORY['sales']),
																   'voucherType' => VOUCHER_TYPE['regular'],
																   'customerType' => CUSTOMER_TYPE['enduser'],
																   'brandkey' => $rsItem[0]['brandkey'], 
																   'itemkey' => $rsItem[0]['pkey'], 
																   'itemcategorykey' => $rsItem[0]['categorykey']
																	) 
															  );
                $availableVoucher = array_column($availableVoucher,'pkey');
                
                // jika voucher tdk ada
                /*$this->setLog($voucherkey,true);
                $this->setLog($availableVoucher,true);*/
                if (!in_array($voucherkey, $availableVoucher)) continue;
                
                $eligibleAmount += $amount;
            }
             
            if ($eligibleAmount > 0){
                $minAmountOfTransaction = $rsVoucher[0]['minamount'];
                $maxDiscount = $rsVoucher[0]['maxdiscount'];
                $discType = $rsVoucher[0]['discounttype'];
                
                if ($minAmountOfTransaction != 0 && $eligibleAmount < $minAmountOfTransaction){
                    //nilai transaksi kurang dr min amount
                    
                }else{
                    if ($discType == 1){ 
                        $voucherValue['value'] = $rsVoucher[0]['value'];
                    }else{ 
                        $voucherValue['value'] = $rsVoucher[0]['value'] * $eligibleAmount / 100;
                        
                        if($maxDiscount > 0 && $voucherValue['value'] > $maxDiscount)
                            $voucherValue['value'] = $maxDiscount ;
                    } 
                }
                
                    
            }

        }
        
        return $voucherValue;
        
    }
    
    
    function eligibleForOneTimeUse($userkey,$voucherkey){
        $voucherTransaction = new VoucherTransaction();
        
        // gk perlu cek status
        $rsVoucher = $this->searchData('','',true,' and '.$this->tableName.'.pkey in (' .$this->oDbCon->paramString($voucherkey,',').')' ); 
        
        $arrReturn = array();
        foreach($rsVoucher as $row){
            $voucherkey = $row['pkey'];
            
            // kalo gk ad rules ny, boleh
             if ($row['onetimeuse'] == 0){
                 $arrReturn[$voucherkey] = true;
                 continue;
             }
             
             // count total yg kepake utk voucher ini
             $sql  = 'select coalesce(count(pkey),0) as total from '.$voucherTransaction->tableName.' 
                      where 
                        '.$voucherTransaction->tableName.'.customerkey = '.$this->oDbCon->paramString($userkey).' and 
                        '.$voucherTransaction->tableName.'.statuskey <> 4 and 
                        '.$voucherTransaction->tableName.'.refvoucherkey = '.$this->oDbCon->paramString($voucherkey);
              
             $rs = $this->oDbCon->doQuery($sql);
             $arrReturn[$voucherkey] = ($rs[0]['total'] == 0) ? true : false;
        }
     
        return $arrReturn;
    }
    
    function removeOneTimeUse($rs){
        if (empty(USERKEY)) return $rs;
        
        $uniqueRules = $this->eligibleForOneTimeUse(USERKEY,array_column($rs,'pkey'));

        foreach($rs as $key=>$row){
            if(!$uniqueRules[$row['pkey']])
                unset($rs[$key]);
        }    

        return array_values($rs);
    }
    
  }

?>
