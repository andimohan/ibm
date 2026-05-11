<?php
class Asset extends BaseClass{

	function __construct()
	{

		parent::__construct();
		$this->tableName = 'asset';
		$this->tableStatus = 'master_status'; 
		$this->tableWarehouse = 'warehouse'; 
		$this->tableAssetCategory = 'asset_category';
		$this->tableAssetType = 'asset_type';

		$this->newLoad = true;
		$this->securityObject = 'Asset'; 
 
		$this->arrData = array();
		$this->arrData['pkey'] = array('pkey');
		$this->arrData['code'] = array('code');
		$this->arrData['name'] = array('name');
		$this->arrData['refpurchasekey'] = array('hidPurchaseKey');
		$this->arrData['statuskey'] = array('selStatus');
		$this->arrData['categorykey'] = array('selCategory');
		$this->arrData['warehousekey'] = array('selWarehouse');
		$this->arrData['assetgroupkey'] = array('selAssetGroup');
		$this->arrData['acquisitionvalue'] = array('acquisitionValue', 'number');
		$this->arrData['initdepreciationvalue'] = array('initDepreciationValue', 'number');
		$this->arrData['bookvalue'] = array('bookValue', 'number');			
		$this->arrData['islinked'] = array('islinked');
		$this->arrData['acquisitiondate'] = array('acquisitionDate','date');
		$this->arrData['explicensedate'] = array('expLicenseDate','date');
		$this->arrData['depreciationvalue'] = array('depreciationValue', 'number');			

		//$this->arrData['bookvalue'] = array('bookValue', 'number');
		//$this->arrData['residue'] = array('residue', 'number');
		//$this->arrData['aging'] = array('aging', 'number');
		//$this->arrData['typekey'] = array('selType');
		//$this->arrData['acquisitionvalue'] = array('acquisitionValue', 'number');


		$this->arrDataListAvailableColumn = array();
		array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
		array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 150));
		array_push($this->arrDataListAvailableColumn, array('code' => 'category', 'title' => 'category', 'dbfield' => 'categoryname', 'default' => true, 'width' => 130));
		array_push($this->arrDataListAvailableColumn, array('code' => 'type', 'title' => 'type', 'dbfield' => 'typename', 'default' => true, 'width' => 120));
		array_push($this->arrDataListAvailableColumn, array('code' => 'aging', 'title' => 'usefulLife', 'dbfield' => 'aging', 'default' => true, 'width' => 100, 'format' => 'number', 'align' => 'right'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'bookvalue', 'title' => 'bookValue', 'dbfield' => 'bookvalue', 'default' => true, 'align' => 'right', 'width' => 100, 'format' => 'number'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true,  'width' => 120));
		//array_push($this->arrDataListAvailableColumn, array('code' => 'residue', 'title' => 'residue', 'dbfield' => 'residue', 'default' => true, 'align' => 'right', 'width' => 100, 'format' => 'number'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'explicensedate', 'title' => 'expirationDate', 'dbfield' => 'explicensedate', 'align' => 'center', 'width' => 100, 'format' => 'date'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


		$this->arrSearchColumn = array();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name')); 
		array_push($this->arrSearchColumn, array('Nama', $this->tableAssetType . '.name')); 
		array_push($this->arrSearchColumn, array('Nama', $this->tableAssetCategory . '.name')); 

		$this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'print1DBrcode', 'name' => $this->lang['print']. ' ' .$this->lang['linearBarcode'],  'icon' => 'print', 'url' => 'print/assetBarcode'));
      
        $this->importUrl = 'import/asset';
		
		$this->includeClassDependencies(array(
			'AssetCategory.class.php',
			'AssetGroup.class.php',
			'Warehouse.class.php',
			'AssetDepreciation.class.php',
            'GeneralJournal.class.php'
		));

		$this->overwriteConfig();
	}

	function getQuery(){

		$sql = '
                 select
                     ' . $this->tableName . '.*, 
					 ' . $this->tableAssetCategory . '.name as categoryname,
					 ' . $this->tableAssetCategory . '.aging,
                     ' . $this->tableStatus . '.status as statusname,
                     ' . $this->tableAssetType . '.name as typename,
					 ' . $this->tableWarehouse . '.name as warehousename
                 from 
                     ' . $this->tableName . '
					 		left join ' . $this->tableAssetCategory . ' on ' . $this->tableName . '.categorykey = ' . $this->tableAssetCategory . '.pkey
					 		left join ' . $this->tableAssetType . ' on ' . $this->tableAssetCategory . '.typekey = ' . $this->tableAssetType . '.pkey,
					 ' . $this->tableStatus . ',
					 ' . $this->tableWarehouse . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                     ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey';

		$sql .= $this->criteria;

		return $sql;
	}
    
   
 
	function validateForm($arr, $pkey = '')
	{
		$arrayToJs = parent::validateForm($arr, $pkey);
		if (empty($arr['code']))
			$this->addErrorList($arrayToJs, false, $this->errorMsg['code'][1]);

	/*	if (empty($arr['name']))
			$this->addErrorList($arrayToJs, false, $this->errorMsg['name'][1]);*/
// 
//		if ($arr['bookValue'] <= 0)
//			$this->addErrorList($arrayToJs, false, $this->errorMsg['bookValue'][2]);
//
//		if ($arr['residue'] < 0)
//			$this->addErrorList($arrayToJs, false, $this->errorMsg['residue'][2]);
//
//		if ($arr['acquisitionValue'] < 0)
//			$this->addErrorList($arrayToJs, false, $this->errorMsg['residue'][2]);

		return $arrayToJs;
	}

	function generateDefaultQueryForAutoComplete($returnField){ 
		// code dipish, karena search asset bisa kombinasi
		
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value, 
				'.$this->tableName . '.code,
				'.$this->tableName . '.name,
				'.$this->tableName . '.explicensedate,
				'.$this->tableName . '.depreciationvalue
            from 
                '.$this->tableName . '  
            where  		
               1=1
        ';
        
        return $sql; 
    }
	
	function calculateDepreciationValue($acquisitionValue,$lifetime){
		
		if($lifetime == 0) return 0;
		
		$lifetime *= 12;
		
		// straightline
		return $acquisitionValue / $lifetime;
		
	}
     
	function getAssetCOAKey($ssetkey,$warehousekey){ 
        $coaLink = new COALink();
        $warehouse = new Warehouse();
         
		// ambil coa key dari jenis kategori
		$sql = 'select 
					'.$this->tableAssetCategory.'.coaassetkey, '.$this->tableAssetCategory.'.coadepreciationkey, '.$this->tableAssetCategory.'.coaaccumulatedkey,
					'.$this->tableName.'.categorykey, '.$this->tableName.'.pkey
				from  '.$this->tableName.', '.$this->tableAssetCategory.'  
				where '.$this->tableName.'.categorykey ='.$this->tableAssetCategory.'.pkey
				and '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($ssetkey ,',').')
				';
        
//        $this->setLog($sql,true);
		 
        $rs = $this->oDbCon->doQuery($sql);
//        $this->setLog($rs,true);
		
		//cek ulang setiap kategori sudah lengkap blm coanyaa
		
		$coaAAssetCache = array();
		$coaExpenseCache = array();
		$coaAccumulated = array();
		
		$totalRs = count($rs);
		for($i=0;$i<$totalRs;$i++){  
			
			$categorykey = $rs[$i]['categorykey'];
			
			if ( empty($rs[$i]['coaassetkey']) ) { 
				$rs[$i]['coaassetkey'] = (!isset($coaAAssetCache[$categorykey])) ? $coaLink->getCOALink ('asset', $warehouse->tableName,  $warehousekey)[0]['coakey'] : $coaAAssetCache[$categorykey] ;    
			 	$coaAAssetCache[$categorykey] = $rs[$i]['coaassetkey'];
			}
			
			if ( empty($rs[$i]['coadepreciationkey']) ) { 
				$rs[$i]['coadepreciationkey'] = (!isset($coaExpenseCache[$categorykey])) ? $coaLink->getCOALink ('depreciationExpense', $warehouse->tableName,  $warehousekey)[0]['coakey'] : $coaExpenseCache[$categorykey] ;    
			 	$coaExpenseCache[$categorykey] = $rs[$i]['coadepreciationkey'];
			}
			
			if ( empty($rs[$i]['coaaccumulatedkey']) ) { 
				$rs[$i]['coaaccumulatedkey'] = (!isset($coaAccumulated[$categorykey])) ? $coaLink->getCOALink ('accumulatedDepreciation', $warehouse->tableName,  $warehousekey)[0]['coakey'] : $coaAccumulated[$categorykey] ;    
			 	$coaAccumulated[$categorykey] = $rs[$i]['coaaccumulatedkey'];
			}
			
		}
		 
        return $rs;
    }
	
	function addDepreciation($trDate=''){
         
        // $trDate => db format
        
		// cari asset yang totaldepreciatedctr  masih dibawah lifetime
		// group by category dan lokasi
		if(empty($trDate)) $trDate = date('Y-m-d');
		
		$sql = 'select 
					'.$this->tableName.'.pkey, 
					'.$this->tableName.'.warehousekey, 
					'.$this->tableName.'.categorykey, 
					'.$this->tableName.'.bookvalue
				from 
				 	'.$this->tableName.', 
					'.$this->tableAssetCategory.'  
				where 
					'.$this->tableName.'.categorykey ='.$this->tableAssetCategory.'.pkey and
					'.$this->tableName.'.statuskey = 1 and
					'.$this->tableName.'.acquisitiondate <=  '.$this->oDbCon->paramString($trDate).' and  
					'.$this->tableName.'.totaldepreciatedctr < ('.$this->tableAssetCategory.'.aging * 12)  
			';

// 		$this->setLog($sql,true);
		
        $rs = $this->oDbCon->doQuery($sql);
		 

		$assetDepreciation = new AssetDepreciation();

		$sqlCheck = '
			select 
				pkey, 
				code,
				warehousekey,
				trdate 
			from 
				' . $assetDepreciation->tableName . ' 
			where 
				statuskey in (1,2,3) and
				trdate = ' . $this->oDbCon->paramString($this->formatDBDate($trDate,'Y-m-d')) 
			;

		$rsCheck = $this->oDbCon->doQuery($sqlCheck);
		
		$arrDepreciated = array();
		foreach($rs as $row){
			$warehousekey = $row['warehousekey'];
			$categorykey = $row['categorykey'];
			$assetkey = $row['pkey'];
			$bookValue = $row['bookvalue'];
            
            // kalo nilai buku sudah dibawah nol, jgn di add, gk bisa, pas cron pertama gk masuk 
            if(round($bookValue) <= 0) continue;
            
			if(!isset($arrDepreciated[$warehousekey])) $arrDepreciated[$warehousekey] = array();			 
			array_push($arrDepreciated[$warehousekey],$assetkey);  
		}
		
		//adad depreciation
		foreach($arrDepreciated as $warehousekey=>$assetRow){
 
			if (!empty(array_filter($rsCheck, fn($r) => $r['warehousekey'] == $warehousekey)))   continue; 
			
			$arrParam = array();

			$arrParam['code'] = 'xxxxxx';    
			$arrParam['selStatus'] = 1;
            $arrParam['selWarehouseKey'] = $warehousekey; 
            $arrParam['trDate'] = (!empty($trDate)) ? $this->formatDBDate($trDate,'d / m / Y')  : date(' d / m / Y'); 
			$arrParam['createdBy'] = 0; 
			
			$arrParam['hidDetailKey'] = array();
			$arrParam['hidAssetKey'] = array(); 
			 
			
			foreach($assetRow as $row){
					array_push($arrParam['hidDetailKey'],0);
					array_push($arrParam['hidAssetKey'],$row);
			}
 			
			$arrayToJs = $assetDepreciation->addData($arrParam);
// 			$this->setLog($arrayToJs,true);
			
			if (!$arrayToJs[0]['valid'])
                throw new Exception($this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
			 
			$assetDepreciation->changeStatus($arrayToJs[0]['data']['pkey'],2,'',false,true);   
  
			
		}
	 
	}
	
//	function getDepreciationValueByCategory($arrCategoryKey){
//		if(!is_array($arrCategoryKey))
//			$arrCategoryKey = array($arrCategoryKey);
//		
//			  
//		$assetCategory = new AssetCategory();
//		$rsAssetCategory = $assetCategory->searchDataRow(array($assetCategory->tableName.'.pkey',$assetCategory->tableName.'.aging * 12'), 
//														 ' and '.$assetCategory->tableName.'.pkey in ('.$this->oDbCon->paramString($arrCategoryKey ,',').')');
//		$rsAssetCategory = array_column($rsAssetCategory,'aging','pkey');
//		
//		// straight line
//		return ($aging == 0) ? 0 :  $acquisitionValue / ($rsAssetCategory[$detailRow['categorykey']]);
//	}
    
    
    
    function afterUpdateData($arrParam, $action){ 
        // kalo dr purchsae, return aj
        
        $generalJournal = new GeneralJournal(); 
        
        $rs = $this->getDataRowById($arrParam['pkey']);
        $oldRs = $arrParam['oldRs']; 
          
        if(!empty($rs[0]['refpurchasekey'])) return; // kalo dr purchase, return
        if($rs[0]['totaldepreciatedctr'] > 0) return; // klo sdh d depresisi, return
        if($rs[0]['initdepreciationvalue'] > 0) return; // klo sdh ad nila init, return
         
        $arr1 =array(); 
        array_push($arr1,$rs[0]['warehousekey']);  
        array_push($arr1,$rs[0]['acquisitionvalue']);  
        $arr1 = md5(json_encode($arr1));
         
        $arr2 = array();
        if(!empty($oldRs)){   
            array_push($arr2,$oldRs[0]['warehousekey']);
            array_push($arr2,$oldRs[0]['acquisitionvalue']);  
        }
        $arr2 = md5(json_encode($arr2));
        
        $same = ($arr1 == $arr2) ? true : false;
	           
        // kalo blm ad jurnal, add
        if (empty($oldRs)){ 
            $this->updateGL($rs);
        }else{
            if (!$same){ 
                //kalo ud ad cek perlu add ulang atau tidak
                $this->cancelGLByRefkey($arrParam['pkey'],$this->tableName);  
                $this->updateGL($rs);
            } 
        }
    }
    
    
    function  updateGL($rs){
            
        if (!USE_GL) return; 
        if ($rs[0]['overwriteGL'] == 1) return; 
         
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();  
        $generalJournal = new GeneralJournal(); 
		
        $warehousekey = $rs[0]['warehousekey']; 
            
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['acquisitiondate'],'d / m / Y');  
		$arr['refCode'] = $rs[0]['code'];
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		
		$temp = -1; 
        
        $temp++; 
        // debit 
		$rsCOA = $this->getAssetCOAKey($rs[0]['pkey'],$warehousekey);  
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coaassetkey'];
        $arr['debit'][$temp] = $rs[0]['acquisitionvalue']; 
        $arr['credit'][$temp] = 0;  

        $temp++; 
		// credit     
        $retainedearnings = $coaLink->getCOALink('retainedearnings'); 
        $arr['hidCOAKey'][$temp] =  $retainedearnings[0]['coakey'];
        $arr['debit'][$temp] = 0;  
        $arr['credit'][$temp] = $rs[0]['acquisitionvalue'];  
         
		$arrayToJs = $generalJournal->addData($arr); 
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    }
    
     
	
	function normalizeParameter($arrParam, $trim = false){  
        
                
        // kalo import pertama kali
        if($arrParam['_mnv-api'] == 1){
          $arrParam['bookValue'] = $arrParam['acquisitionValue'] - $arrParam['initDepreciationValue'];
        }
        
        
        // hitung ulang nilai depresiasi per bln 
        // kalo edit
        
        $keepAquisitionValue = false;
        
        if(!empty($arrParam['hidId'])){
             
            // kalo edit,nilai akuisi gk boleh berubah kalo sudah ada depresiasi
            $rs = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.warehousekey',$this->tableName.'.totaldepreciatedctr',$this->tableName.'.acquisitionvalue'), 
                                                             ' and '.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($arrParam['hidId'])); 
            
            if ($rs[0]['totaldepreciatedctr'] > 0)  $keepAquisitionValue = true; 
            
            // kalo edit, dan dr purchase gk boelh edit nilai akuisisi
            if(!empty($rs[0]['refpurchasekey']))  $keepAquisitionValue = true;  
            
            if($keepAquisitionValue == true) unset($arrParam['acquisitionValue']);  
            
            $arrParam['oldRs'] = $rs;
            
        }else{ 
            // kalo add
            /// kalo edit dan dari purchase
        } 
        
        // kalo nilai akusisi berubah, nilai depresiasi jug berubah
        if(!$keepAquisitionValue){  
            $assetCategory = new AssetCategory();
            $rsAssetCategory = $assetCategory->searchDataRow(array($assetCategory->tableName.'.pkey',$assetCategory->tableName.'.aging'), 
                                                             ' and '.$assetCategory->tableName.'.pkey = ' . $this->oDbCon->paramString($arrParam['selCategory'])); 
            $acquisitionValue = $this->unFormatNumber($arrParam['acquisitionValue']);
            $arrParam['depreciationValue'] = $this->calculateDepreciationValue($acquisitionValue,$rsAssetCategory[0]['aging']);
        }
         
        
		$arrParam = parent::normalizeParameter($arrParam, true);
		return $arrParam;
	}
}
