<?php  
class SalesOrderDumper extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
        $this->tableName = 'sales_order_dumper';
        $this->tableProject = 'project_dumper_header';
        $this->tableLocation = 'location';
        $this->tableEmployee = 'employee';
        $this->tableWarehouse = 'warehouse';
		$this->tableStatus = 'transaction_status'; 
	   
		$this->securityObject = 'SalesOrderDumper'; 
        
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['refkey'] = array('hidProjectKey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['locationkey'] = array('selDestination');
        $this->arrData['weight'] = array('weight','number'); 
        $this->arrData['distance'] = array('distance','number');
        $this->arrData['price'] = array('pricePerDistance','number');
        $this->arrData['total'] = array('totalPrice','number');
        $this->arrData['carkey'] = array('hidCarKey');
        $this->arrData['driverkey'] = array('hidDriverKey');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['trdesc'] = array('trDesc');
        
        $this->isTransaction = true;
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'projectname','title' => 'project','dbfield' => 'projectname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'locationname','title' => 'location','dbfield' => 'locationname','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'weight','title' => 'weight','dbfield' => 'weight','default'=>true,  'align' =>'right',  'format' => 'number', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'distance','title' => 'distance','dbfield' => 'distance','default'=>true,  'align' =>'right',  'format' => 'number', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'price','title' => 'price','dbfield' => 'price','default'=>true,  'align' =>'right',  'format' => 'number', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'total','default'=>true,  'align' =>'right',  'format' => 'number', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
        $this->newLoad = true;
       
		$this->overwriteConfig();
   }
	 
	 
	 
    function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
                    '.$this->tableEmployee.'.name as drivername,
                    '.$this->tableLocation.'.name as locationname,
                    '.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableProject.'.name as projectname,
                    '.$this->tableProject.'.code as projectcode,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName. ' 
                        left join  '.$this->tableEmployee. ' on  '.$this->tableName . '.driverkey = '.$this->tableEmployee.'.pkey,
                    '.$this->tableProject.',
                    '.$this->tableStatus.',
                    '.$this->tableWarehouse.',
                    '.$this->tableLocation.'
                where  		
                    '.$this->tableName . '.refkey = '.$this->tableProject.'.pkey AND
                    '.$this->tableName . '.locationkey = '.$this->tableLocation.'.pkey AND
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey AND
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		         ' .$this->criteria ; 
		 
    } 
	
	  function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
	   
         $locationCode = $arr['selDestination']; 
         
		if(empty($locationCode)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['location'][1]);
		}
           
		 return $arrayToJs;
	 }
    
	 
     
    function normalizeParameter($arrParam, $trim=false){ 
        
        $arrParam = parent::normalizeParameter($arrParam,true);   
        return $arrParam;
    }
        
    
  }

?>