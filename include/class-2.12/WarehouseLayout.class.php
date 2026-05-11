<?php

class WarehouseLayout extends BaseClass{
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'warehouse_layout';  
		$this->tableWarehouse = 'warehouse';  
		$this->securityObject = 'WarehouseLayout'; 
        $this->tableStatus = 'master_Status';  
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['capacity'] = array('capacity', 'number');
        $this->arrData['parentkey'] = array('selCategory');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['shortdescription'] = array('trShortDesc'); 
        $this->arrData['isshow'] = array('chkIsShow'); 
        $this->arrData['featured'] = array('chkIsFeatured'); 
        $this->arrData['description'] = array('txtDescription','raw'); 
        $this->arrData['itemcodepattern'] = array('itemCodePattern');
        $this->arrData['spareparttypekey'] = array('selSparePartType');          
        $this->arrData['warehousekey'] = array('selWarehouseKey'); 
        $this->arrData['rootkey'] = array('rootkey'); 
        $this->arrData['istransit'] = array('chkIsTransit'); 
        
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'parent','title' => 'parent','dbfield' => 'parentname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'shortdescription', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'orderlist','title' => 'orderList','dbfield' => 'orderlist', 'align' => 'right', 'format' => 'integer', 'width' => 70));
       
        $this->newLoad = true;
       
        $this->includeClassDependencies(array( 
              'Warehouse.class.php',
        ));
   
		$this->overwriteConfig();
    } 

    function getQuery(){
	   
	   $sql= '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename,
					parentcat.name as parentname
				from 
					'.$this->tableName .' 
                    left join '.$this->tableName . ' parentcat on 	parentcat.pkey = '.$this->tableName . '.parentkey 
                    left join '.$this->tableWarehouse.' on ' . $this->tableName .'.warehousekey = '.$this->tableWarehouse.'.pkey, 
                    '.$this->tableStatus.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
       return $sql; 
    }
    
    function validateForm($arr,$pkey = ''){
		       
		$arrayToJs = parent::validateForm($arr,$pkey);  

		if($this->isActiveModule('marketplace')){
			$marketplace = new Marketplace();
                
        // VALIDASI KHUSUS KALO AD MARKETPLACE
        $marketplaceObjs = $marketplace->getMarketplaceObj();
        if(!empty($marketplaceObjs)){
            
            // semua field di kolom marketplace wajib diisi
            $hasEmptyField = false;
             
            foreach($arr['marketplaceCategoryName'] as $marketplaceCategoryName){
                if (empty($marketplaceCategoryName)){
                    $hasEmptyField = true;
                    break;
                }  
            }
            
            if($hasEmptyField)
                $this->addErrorList($arrayToJs,false,$this->errorMsg['marketplace'][5]); 
            
        }
         
		}
        
		return $arrayToJs;
	} 

    function getRootKey($parentKey)
    {
        $pkey = $parentKey;
        while (true) {
            
            $row = $this->searchDataRow(array($this->tableName.'.parentkey',$this->tableName.'.pkey'), ' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey));

            if (empty($row)) {
                return null; // data tidak ditemukan
            }

            if ($row[0]['parentkey'] == 0) {
                return $row[0]['pkey']; // INI ROOT
            }


            $pkey = $row[0]['parentkey'];
        }
    }

    function getChildren($parentkey=0, &$arrChild=array()){
		// utk mencari semua node dibawah node $parentkey
		$rs = $this->searchData($this->tableName.'.statuskey',1,true,' and '.$this->tableName . '.parentkey = ' . $this->oDbCon->paramString($parentkey));
		 
		for ($i=0;$i<count($rs);$i++){ 
			 array_push($arrChild,$rs[$i]['pkey']);
			 if ($rs[$i]['isleaf'] == 0)
			 	$this->getChildren($rs[$i]['pkey'],$arrChild);
		}
		
		return $arrChild;
		 
	}

    function afterUpdateData($arrParam, $action){   
        
        $pkey = $arrParam['pkey'];

        $arrItemkey = $arrParam['hidItemKey'];
        $parentKey = $arrParam['selCategory'];
    
    }

    function getDataByWarehouse($warehousekey, $criteria = '')
    {
       	$sql= 'select
					'.$this->tableName. '.*,
					'.$this->tableWarehouse.'.name as warehousename,
                    parentcat.name as parentname,
                    CONCAT(parentcat.name, " / ", '.$this->tableName.'.name) as warehouselayoutname
				from 
					'.$this->tableName .' 
                    left join '.$this->tableName . ' parentcat on parentcat.pkey = '.$this->tableName . '.parentkey 
                    left join '.$this->tableWarehouse.' on ' . $this->tableName .'.warehousekey = '.$this->tableWarehouse.'.pkey
				where  		
                    '.$this->tableName.'.parentkey != 0 and
					parentcat.warehousekey = '.$this->oDbCon->paramString($warehousekey).'
 		';

        if(!empty($criteria)) {
            $sql .= ' ' . $criteria;
        }
        $this->setLog($sql, true);

        //  $sql= '
		// 	select
		// 			'.$this->tableName. '.*,
		// 			'.$this->tableWarehouse.'.name as warehousename,
        //             parentcat.name as parentname,
        //             CONCAT(parentcat.name, " / ", '.$this->tableName.'.name) as warehouselayoutname
		// 		from 
		// 			'.$this->tableName .' 
        //             left join '.$this->tableName . ' parentcat on parentcat.pkey = '.$this->tableName . '.parentkey 
        //             left join '.$this->tableWarehouse.' on ' . $this->tableName .'.warehousekey = '.$this->tableWarehouse.'.pkey
		// 		where  		
        //             '.$this->tableName.'.parentkey != 0 and
		// 			parentcat.warehousekey = '.$this->oDbCon->paramString($warehousekey).'
 		// '; 
    
        $result = $this->oDbCon->doQuery($sql);

        return $result;

    }

    function updateDataWarehouse($parentkey, $criteria = '')
    { 
        
    }
 
    function normalizeParameter($arrParam, $trim = false){

        $parentKey = $arrParam['selCategory'];
        $rootKey = $this->getRootKey($parentKey);
        $arrParam['rootkey'] = $rootKey;

        if ($parentKey <> 0 ){
            $rsParent = $this->getDataRowById($parentKey);
            $arrParam['selWarehouseKey'] = $rsParent[0]['warehousekey'];
        }


        $arrParam = parent::normalizeParameter($arrParam,true); 
		 
        
        return $arrParam; 
    }   
	    
    
}

?>
