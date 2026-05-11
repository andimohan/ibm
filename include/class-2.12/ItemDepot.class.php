<?php 
class ItemDepot extends Item{
 
   function __construct($itemType = 4){
		
		parent::__construct();
         
        $this->itemType = $itemType; 
        $this->securityObject = 'ItemDepot';         
		 
		$this->tableDepot = 'depot';
		$this->tableItemInDepot = 'item_in_depot';
		$this->tableItemDepotMovement = 'item_depot_movement';
       
        $this->depotCriteria = '';
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['categorykey'] = array('hidCategoryKey'); 
        $this->arrData['statuskey'] = array('selStatus');   
        $this->arrData['baseunitkey'] = array('selBaseUnitKey');
        $this->arrData['deftransunitkey'] = array('selDefaultTransUnitKey'); 
        $this->arrData['gramasi'] = array('gramasi', 'number');
        $this->arrData['shortdescription'] = array('shortdescription'); 
        $this->arrData['itemtype'] = array('itemType');         
        $this->arrData['weightunitkey'] = array('selWeightUnit');
        $this->arrData['width'] = array('width');
        $this->arrData['length'] = array('length');
        $this->arrData['height'] = array('height');
       
        $this->arrLockedTable = array();
        $defaultFieldName = 'itemkey'; 
        array_push($this->arrLockedTable, array('table'=>'item_depot_movement','field'=>$defaultFieldName));  
        array_push($this->arrLockedTable, array('table'=>'item_in_depot_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_out_depot_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_in_depot','field'=>$defaultFieldName));
       
   }
   
   function getQuery($onlyQOHCount = true){ 
       
       $companyCriteria = $this->getCompanyCriteria($this->tableDepot);
       
	   $sql = '
		    select * from (
                SELECT '.$this->tableName.'.* ,
               '.$this->tableCategory.'.name as categoryname,
			   '.$this->tableStatus.'.status as statusname,
			   '.$this->tableItemUnit.'.name as baseunitname, 
			   weightunit.name as weightunitname, 
               coalesce(sum('.$this->tableItemInDepot.'.qtyinbaseunit),0) as qtyonhand ,
               coalesce(sum('.$this->tableItemInDepot.'.qtyinbaseunit),0) * gramasi as totalweight 
			FROM '.$this->tableStatus.',  '.$this->tableCategory.' ,'.$this->tableItemUnit.','.$this->tableItemUnit.' as weightunit, '.$this->tableName.'  
				left join
				    (select itemkey, 
                            qtyinbaseunit,
                            depotkey
                    from  '.$this->tableItemInDepot.',
                          '.$this->tableDepot.' 
                    where 
                       '.$this->tableDepot.'.pkey =  '.$this->tableItemInDepot.'.depotkey '.$this->depotCriteria.'  '.$companyCriteria.' ) '.$this->tableItemInDepot.' 
                on item.pkey =  '.$this->tableItemInDepot.'.itemkey 
				 
		    WHERE 
                itemtype in ('.$this->itemType.') and
                '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey and
                '.$this->tableItemUnit.'.pkey = '.$this->tableName.'.baseunitkey and
                weightunit.pkey = '.$this->tableName.'.weightunitkey and
			    '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
			'.$this->criteria.'	 
			 group by '.$this->tableName.'.pkey) as ' . $this->tableName;
		    
       return $sql;
   }
    
    
    function normalizeParameter($arrParam, $trim = false){
        
        $arrParam['itemType'] = $this->itemType;
        $arrParam['selDefaultTransUnitKey'] = $arrParam['selBaseUnitKey'];
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
          
        
        return $arrParam;
    }
    
    
  } 

?>