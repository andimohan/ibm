<?php

class WarehouseLocation extends BaseClass{
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'warehouse_location';  
		$this->tableNameWarehouseDetail = 'warehouse_location_detail';  
		$this->tableWarehouseLayout = 'warehouse_layout';  
		$this->tableWarehouse = 'warehouse';  
		$this->tableStatus = 'master_Status';  
		$this->securityObject = 'warehouseLocation'; 

        $this->arrWarehouseLayout = array();
        $this->arrWarehouseLayout['pkey'] = array('hidDetailWarehouseLayoutKey');
        $this->arrWarehouseLayout['refkey'] = array('pkey', 'ref');
        $this->arrWarehouseLayout['reflayoutkey'] = array('hidWarehouseLayoutKey', array('mandatory'=>true));

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrWarehouseLayout, 'tableName' => $this->tableNameWarehouseDetail));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['shortdescription'] = array('trShortDesc');       
        $this->arrData['warehousekey'] = array('selWarehouseKey'); 
        
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'shortdescription', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'orderlist','title' => 'orderList','dbfield' => 'orderlist', 'align' => 'right', 'format' => 'integer', 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->newLoad = true;
       
        $this->includeClassDependencies(array( 
              'Warehouse.class.php',
        ));
   
		$this->overwriteConfig();
    } 

    function getQuery(){ 
	   
	    $sql = '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName . ',
                    '.$this->tableStatus. '
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
                    
 		' .$this->criteria ; 
		  
        return $sql;
    }

    function getWarehouseLayoutDetail($pkey, $criteria = '') 
    {
        $sql = '
            select 
                '. $this->tableNameWarehouseDetail .'.*,
                '. $this->tableWarehouseLayout .'.name as warehouselayoutname
            from 
                '. $this->tableNameWarehouseDetail .'
                    left join ' . $this->tableWarehouseLayout . ' on ' . $this->tableNameWarehouseDetail . '.reflayoutkey = ' . $this->tableWarehouseLayout . '.pkey
            where
                '. $this->tableNameWarehouseDetail .'.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ')
        ';

        $sql .= $criteria;

        $rs =  $this->oDbCon->doQuery($sql);
        $this->setLog($rs, true);
        $this->setLog($sql, true);
        return $rs;
    }
    
    function validateForm($arr,$pkey = ''){
		       
		$arrayToJs = parent::validateForm($arr,$pkey);  
        
		return $arrayToJs;
	} 
 
    function normalizeParameter($arrParam, $trim = false){
		 
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam; 
    }   
	    
    
}

?>
