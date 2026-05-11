<?php
  
class SalesOrderPropertyType extends BaseClass{ 
  

    function __construct(){

            parent::__construct();

            $this->tableName = 'sales_order_property_type';
            $this->tableStatus = 'master_status';
		
            $this->securityObject = 'SalesOrderPropertyType';   

            $this->arrData = array(); 
            $this->arrData['pkey'] = array('pkey'); 
            $this->arrData['code'] = array('code');
            $this->arrData['name'] = array('name');
            $this->arrData['percentagevalue'] = array('percentageValue');
            $this->arrData['trdesc'] = array('trDesc');
            $this->arrData['statuskey'] = array('selStatus');
       
              
            $this->arrDataListAvailableColumn = array(); 
            array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
			array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
			
		
			$this->arrSearchColumn = array(); 
			array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
			array_push($this->arrSearchColumn, array('Nama', $this->tableName. '.name'));
			array_push($this->arrSearchColumn, array('Catatan', $this->tableName. '.trdesc'));

       
            $this->overwriteConfig();
    }
 
            
    
    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
               '.$this->tableStatus.'.status as statusname
            FROM 
                '.$this->tableStatus.',
                '.$this->tableName.'  

            WHERE
				 '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
        ' .$this->criteria ; 

 
        return $sql;
    }  
  

    function validateForm($arr,$pkey = ''){

            $arrayToJs = parent::validateForm($arr,$pkey); 
          
        
        
            return $arrayToJs;
    }
  
        
}
?>
