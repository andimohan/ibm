<?php

class MedicalRecord extends BaseClass{
	
    function __construct(){

        parent::__construct();

        $this->tableName = 'medical_record_header';
        $this->tableNameDetail = 'medical_record_detail';
        $this->tableCustomer = 'customer';   
        $this->tableEmployee = 'employee';   
        $this->tableWarehouse = 'warehouse';   
        $this->tableStatus = 'transaction_status';
        $this->securityObject = 'MedicalRecord';
        $this->isTransaction = true; 		

        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref'); 
        $this->arrDataDetail['date'] = array('trDate','date'); 
        $this->arrDataDetail['employeekey'] = array('hidEmployeeKey'); 
        $this->arrDataDetail['soapdescription'] = array('soapDesc'); 
        $this->arrDataDetail['therapydescription'] = array('theraphyDesc'); 

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
        $this->arrData['code'] = array('code');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['note'] = array('note');
        $this->arrData['statuskey'] = array('selStatus');


        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'dpjp','title' => 'DPJP','dbfield' => 'doctorname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'address','title' => 'address','dbfield' => 'address','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'medicineAllergy','dbfield' => 'description', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['print'],  'icon' => 'print', 'url' => 'print/medicalRecord'));
       
    }

    function getQuery(){
        
        
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableCustomer.'.name as customername,
                '.$this->tableCustomer.'.address,
                '.$this->tableCustomer.'.description,
                '.$this->tableEmployee.'.name as doctorname,
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableCustomer.'
                    left join '.$this->tableEmployee.' on '.$this->tableCustomer . '.saleskey = '.$this->tableEmployee.'.pkey, 
                 '.$this->tableWarehouse.',
                 '.$this->tableName.'
            WHERE   
                  '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;
                                         
        return $sql;
    }

    function validateForm($arr,$pkey = ''){ 

        $arrayToJs = parent::validateForm($arr,$pkey); 
        
        $customerkey = $arr['hidCustomerKey'];
        $arrEmployeekey = $arr['hidEmployeeKey']; 

        if(empty($customerkey))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]); 
        
        
        if(empty($arrEmployeekey)) 
                 $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);  
        
        for($i=0;$i<count($arrEmployeekey);$i++) { 
                if (empty($arrEmployeekey[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['employee'][1]); 	
                }

        }
        return $arrayToJs;
    }
    
    function validateConfirm($rsHeader){
     
 
    }
  

    function confirmTrans($rsHeader){  
        
    
    } 
    
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
            $id = $rsHeader[0]['pkey'];
    } 



    function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey'];
      
        if ($copy)
            $this->copyDataOnCancel($id);	  


    }  
 

    
    function normalizeParameter($arrParam, $trim=false){
        
   
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }


     function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*,
            '.$this->tableEmployee.'.name as employeename
          from
            '.$this->tableEmployee.',
            '.$this->tableNameDetail.'
          where  
            '. $this->tableNameDetail.'.employeekey = '.$this->tableEmployee.'.pkey and
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);

    }

}

?>
