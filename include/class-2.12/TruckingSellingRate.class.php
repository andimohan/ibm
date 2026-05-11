<?php
  
class TruckingSellingRate extends BaseClass{ 
  
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'trucking_selling_rate_header';
		$this->tableNameDetail = 'trucking_selling_rate_detail';
		$this->tableCategory = 'trucking_service_order_category'; 
        $this->tableCargoType = 'cargo_type'; 
		$this->tableCustomer = 'customer';  
		$this->tableConsignee = 'consignee';   
		$this->tableWarehouse = 'warehouse'; 
		$this->tableStatus = 'master_status'; 
        $this->tableItem = 'item';
       
        $this->arrDataDetail = array();   
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref'); 
        $this->arrDataDetail['itemkey'] = array('hidItemKey'); 
        $this->arrDataDetail['price'] = array('price','number');

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['warehousekey'] = array('selWarehouseKey');    
        $this->arrData['categorykey'] = array('hidCategoryKey');    
        $this->arrData['customerkey'] = array('hidCustomerKey');  
        $this->arrData['consigneekey'] = array('hidConsigneeKey');
        $this->arrData['cargotypekey'] = array('selCargoType');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
       
		$this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
       
		$this->securityObject = 'TruckingSellingRate';  
        
        $this->arrLockedTable = array();
        $defaultFieldName = 'contractkey'; 
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_header','field'=>$defaultFieldName)); 
        
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=>$this->tableNameDetail,'field' => 'refkey')); 
       
        //array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
		 
       		       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'nameOfRate','title' => 'nameOfRate','dbfield' => 'name','default'=>true));
        array_push($this->arrDataListAvailableColumn, array('code' => 'cargoType','title' => 'cargoType','dbfield' => 'cargotypename','default'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'consignee','title' => 'consignee','dbfield' => 'consigneename','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'consigneeWarehouse','title' => 'consigneeWarehouse','dbfield' => 'consigneewarehousename',  'width' =>150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' =>100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'locationname',  'width' =>100));
          
        $this->includeClassDependencies(array(
            'Service.class.php',
            'Category.class.php',
            'TruckingServiceOrderCategory.class.php',
            'Consignee.class.php',
            'Customer.class.php',
            'Location.class.php',
            'Category.class.php',
            'TruckingServiceOrderCategory.class.php',
            'Warehouse.class.php',
            )
        );
            
        $this->overwriteConfig();
       
   }
   
   function getQuery(){
	   
	   $sql = '
			SELECT 
               '.$this->tableName.'.* ,  
               '.$this->tableWarehouse.'.name as warehousename ,  
               '.$this->tableCustomer.'.name as customername , 
               '.$this->tableConsignee.'.pkey as consigneekey , 
               '.$this->tableConsignee.'.name as consigneename , 
               '.$this->tableConsignee.'.warehousename as consigneewarehousename, 
               '.$this->tableConsignee.'.contactperson, 
               '.$this->tableConsignee.'.locationkey, 
               '.$this->tableConsignee.'.address, 
               '.$this->tableCategory.'.name as categoryname , 
			   '.$this->tableCargoType.'.name as cargotypename  ,  
			   '.$this->tableStatus.'.status as statusname ,  
			   '.$this->tableLocation.'.name as locationname 
			FROM 
                '.$this->tableName.'
                    left join '.$this->tableConsignee.' on '.$this->tableName.'.consigneekey = '.$this->tableConsignee.'.pkey
                    left join '.$this->tableLocation.' on '.$this->tableConsignee.'.locationkey = '.$this->tableLocation.'.pkey,
                '.$this->tableCustomer.', 
                '.$this->tableWarehouse.', 
                '.$this->tableCategory.',
                '.$this->tableStatus.',
                '.$this->tableCargoType .'
			WHERE  
                '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and 
                '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey and
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                '.$this->tableName.'.cargotypekey = '.$this->tableCargoType.'.pkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
       //$sql .=  $this->getWarehouseCriteria();
       return $sql;
       
    }  
 
    
     function validateForm($arr,$pkey = ''){ 
	    $service = new Service();
         
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$customerkey = $arr['hidCustomerKey'];   
		$name = $arr['name'];  
		$arrItemKey = $arr['hidItemKey'];  
		$arrPrice = $arr['price'];   
          
			
		if(empty($customerkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
		}  
         
         if(empty($name)){  
				$this->addErrorList($arrayToJs,false,$this->errorMsg['sellingRate'][1]);
         }else{  
			$rsRate = $this->isValueExisted($pkey,'name',$name);	
			if(count($rsRate) <> 0) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['sellingRate'][2]);
		}  
         
        $arrDetailKeys = array(); 
		for($i=0;$i<count($arrItemKey);$i++) { 
            
            $rsItem = $service->getDataRowById($arrItemKey[$i]);  
            
			if (empty($arrItemKey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['service'][1]); 	
			} 
			
			if (!empty($arrItemKey[$i]) && $this->unFormatNumber($arrPrice[$i]) <= 0 ){
				$this->addErrorList($arrayToJs,false,$rsItem[0]['name'] . '. ' . $this->errorMsg[500]); 
			} 

            // cek ada detail double gk  
            if (in_array($arrItemKey[$i],$arrDetailKeys)){   
                $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
            }else{  
                array_push($arrDetailKeys, $arrItemKey[$i]);
            } 
           
		}
       
		return $arrayToJs;
	 }
	   
   function getDetailWithRelatedInformation($pkey,$criteria=''){
   
       
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*,  
                '.$this->tableItem.'.name as itemname 
                
			  from
			  	'.$this->tableNameDetail .',  
                '.$this->tableItem.'  
			  where 
			  	'.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and 
			  	refkey = '.$this->oDbCon->paramString($pkey)  ;
       
         
       
        $sql .= $criteria;
         
		return $this->oDbCon->doQuery($sql);
	
   }
     
     function generateDefaultQueryForAutoComplete($returnField){ 
        
        $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value ,
                    '.$this->tableConsignee. '.name as consigneename 
				from 
					'.$this->tableName . ' 
                    left join '.$this->tableConsignee.' on
                        '.$this->tableName.'.consigneekey = '.$this->tableConsignee.'.pkey,
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
         
         //$sql .=  $this->getWarehouseCriteria();
         return $sql;
     }
    
	
    function delete($id,$forceDelete = false,$reason = ''){
		 
		$arrayToJs =  array(); 
		
		try{ 
		
	 		$arrayToJs = $this->validateDelete($id);
			if (!empty($arrayToJs)) 
				return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
			 
                $this->deleteReference($id);
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans(); 

				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
				 
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}
    
   function generateReportSellingRate($searchCriteria='',$orderCriteria=''){
        
        $sql = 'select 
                    '.$this->tableNameDetail.'.*,
                    '.$this->tableName.'.code,
                    '.$this->tableName.'.name,
                    '.$this->tableName.'.cargotypekey,
                    '.$this->tableName.'.customerkey,
                    '.$this->tableName.'.categorykey,
                    '.$this->tableName.'.consigneekey,
                    '.$this->tableName.'.statuskey,
                    '.$this->tableName.'.warehousekey,
                    '.$this->tableCargoType.'.name as cargotypename,
                    '.$this->tableCategory.'.name as categoryname,
                    '.$this->tableCustomer.'.name as customername,
                    '.$this->tableConsignee.'.name as consigneename,
                    '.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableStatus.'.status as statusname
                from
                    '.$this->tableName.',
                    '.$this->tableNameDetail.',
                    '.$this->tableCategory.',
                    '.$this->tableCargoType.',
                    '.$this->tableConsignee.',
                    '.$this->tableCustomer.',
                    '.$this->tableWarehouse.',
                    '.$this->tableStatus.'
                where
                    
                    '.$this->tableNameDetail.'.refkey =  '. $this->tableName.'.pkey and 
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                    '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                    '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                    '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey and
                    '.$this->tableName.'.consigneekey = '.$this->tableConsignee.'.pkey and
                    '.$this->tableName.'.cargotypekey = '.$this->tableCargoType.'.pkey
                    ';
        
           if($searchCriteria <> '')
                $sql .= ' ' .$searchCriteria;
        
           if($orderCriteria <> '')
                $sql .= ' ' .$orderCriteria;
        
                               
          return $this->oDbCon->doQuery($sql);

    }   
    
}
?>
