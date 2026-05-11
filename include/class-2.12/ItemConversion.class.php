<?php
  
class ItemConversion extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct(); 
       
		$this->tableName = 'item_conversion';
		$this->tableWarehouse = 'warehouse';
        $this->tableItem = 'item';
        $this->tableBrand = 'brand';
        $this->tableItemCategory = 'item_category';
		$this->tableStatus = 'master_status'; 
         
		$this->securityObject = 'ItemConversion'; 

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        //$this->arrData['trdate'] = array('trDate','date');  
        $this->arrData['itemconvertkey'] = array('hidItemConvertKey');
        $this->arrData['categorykey'] = array('hidCategoryKey');
        $this->arrData['brandkey'] = array('hidBrandKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
		 
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 180 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'brand','title' => 'brand','dbfield' => 'brandname','default'=>true, 'width' => 130));
        array_push($this->arrDataListAvailableColumn, array('code' => 'item','title' => 'conversion','dbfield' => 'itemname','default'=>true,'width' => 200));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
//        $this->printMenu = array();  
//        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/warehouseTransfer'));
        
       $this->includeClassDependencies(array(  
            'Item.class.php', 
            'Category.class.php',  
            'ItemCategory.class.php',  
            'Brand.class.php',
            'Warehouse.class.php' 
        ));

        $this->overwriteConfig();

   }
    
    function getQuery(){
	   
	   $sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableItem.'.name as itemname,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname,
			   '.$this->tableItemCategory.'.name as categoryname,
			   '.$this->tableBrand.'.name as brandname
			FROM '.$this->tableStatus.', 
                '.$this->tableName.' , 
                '.$this->tableBrand.' , 
                '.$this->tableItemCategory.' , 
                '.$this->tableItem.' , 
                '.$this->tableWarehouse.'  
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  
                  '.$this->tableName.'.itemconvertkey = '.$this->tableItem.'.pkey and  
                  '.$this->tableName.'.categorykey = '.$this->tableItemCategory.'.pkey and  
                  '.$this->tableName.'.brandkey = '.$this->tableBrand.'.pkey and  
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
		' .$this->criteria ; 
		 
        $sql .=  $this->getCompanyCriteria() ;
        //$this->setLog($sql,true);
        return $sql;
    }  
    
    function afterStatusChanged($rsHeader){   
        // retrieve latest status

    }
    
    function validateForm($arr,$pkey = ''){
		$item = new Item();   
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$arrItemkey = $arr['hidItemKey']; 
		$arrSelUnit = $arr['selUnit']; 

       
         
		return $arrayToJs;
	 }
	   
   /* function getItemConvert($categorykey,$brandkey){
        
        $sql = 'select '.$this->tableName.'.* 
                from '.$this->tableName.'
                where 
                    '.$this->tableName.'. categorykey = '.$this->oDbCon->paramString($categorykey).' and
                    '.$this->tableName.'. brandkey = '.$this->oDbCon->paramString($brandkey).'
                
                ';
         
        return $this->oDbCon->doQuery($sql);  
        
    }*/


    function normalizeParameter($arrParam, $trim = false){
          
        $item = new Item();
         
        $arrParam = parent::normalizeParameter($arrParam); 
          

        return $arrParam;
    }
      
    
}
?>