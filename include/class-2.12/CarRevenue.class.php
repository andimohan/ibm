<?php 
class CarRevenue extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'car_revenue'; 
		$this->tableWarehouse = 'warehouse' ;   
		$this->tableCustomer = 'customer' ; 
		$this->tableEmployee = 'employee' ; 
		$this->tableCar = 'car' ; 
        $this->tableStatus = 'transaction_status';
        $this->securityObject = 'CarRevenue';
        $this->isTransaction = true;
	 
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code'); 
        $this->arrData['trdate'] = array('trDate','date'); 
        $this->arrData['refcode'] = array('refCode'); 
        $this->arrData['warehousekey'] = array('selWarehouse');  
        $this->arrData['carkey'] = array('hidCarKey');     
        $this->arrData['customerkey'] = array('hidCustomerKey');  
        $this->arrData['driverkey'] = array('hidDriverKey');   
        $this->arrData['trdesc'] = array('trDesc');  
        $this->arrData['amount'] = array('amount','number'); 
        $this->arrData['statuskey'] = array('selStatus');   
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'reference','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'policenumber','title' => 'carRegistrationNumber','dbfield' => 'policenumber','default'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 80, 'align' =>'right','format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'driver','title' => 'driver','dbfield' => 'drivername',  'width' => 100));
       
        $this->overwriteConfig();
       
   }
    
   function getQuery(){
	     
	   $sql =  '
		    select 
				  '.$this->tableName.'.*,
                  '.$this->tableStatus.'.status as statusname,
				  '.$this->tableWarehouse .'.name as warehousename,
                  '.$this->tableCustomer.'.name as customername,
                  '.$this->tableEmployee.'.name as drivername,
				  '.$this->tableCar.'.policenumber 
			from  
                  '.$this->tableStatus.',
                  '.$this->tableName.' 
                    left join '.$this->tableCustomer.' on  '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tableEmployee.' on  '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey ,
                  '.$this->tableCar.', 
                  '.$this->tableWarehouse .'  
			where
				'.$this->tableName.'.carkey = '.$this->tableCar.'.pkey and  
                '.$this->tableName.'.warehousekey = ' . $this->tableWarehouse .'.pkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
		   ' .$this->criteria ;
        
       return $sql;
   }
    
    function validateForm($arr,$pkey = ''){
        $arrayToJs = parent::validateForm($arr,$pkey);
        // amount <> 0 
        $amount = $arr['amount']; 
        if(empty($amount))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][1]); 
        
        return $arrayToJs;
    }

    
/*    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey']; 

    }*/
    
    function cancelTrans($rsHeader,$copy){  

        $id = $rsHeader[0]['pkey'];
        $rsObjKey = $this->getTableKeyAndObj($this->tableName);
 
        $carTurnover = new CarTurnover();
        $carTurnover->cancelMovement($id,$rsObjKey['key']);


        if ($copy)
            $this->copyDataOnCancel($id);	  

    }
    
    function normalizeParameter($arrParam, $trim=false){  
         
            $arrParam = parent::normalizeParameter($arrParam); 
 
            $arrParam['trDesc'] = (empty($arrParam['trDesc'])) ? '' : $arrParam['trDesc']; 
 
 
        return $arrParam;
    }
    
    function afterStatusChanged($rsHeader){ 
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
    
    function validateConfirm($rsHeader){

        $amount = $rsHeader[0]['amount']; 
        if(empty($amount))
            $this->addErrorList(false,$this->errorMsg['amount'][1]);  

	 }
    
    function confirmTrans($rsHeader){
        $carTurnover = new CarTurnover();
        $rsKey = $this->getTableKeyAndObj($this->tableName);
        
        $arrParam = array();	 
        $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
        $arrParam['refCode'] = $rsHeader[0]['code'];
        $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
        $arrParam['joDate'] =  $arrParam['trDate']; 
        $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
        $arrParam['hidRefTable'] = $rsKey['key'];
        $arrParam['hidCarKey'] = $rsHeader[0]['carkey'];   
        $arrParam['amount'] =  $rsHeader[0]['amount'];
        $arrParam['selStatus'] = 1;

        $arrayToJs =  $carTurnover->addData($arrParam); 
        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);  

    }
  

}  

?>
