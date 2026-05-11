<?php
  
class ProjectDumper extends BaseClass{ 
  

    function __construct(){

            parent::__construct();

            $this->tableName = 'project_dumper_header';
            $this->tableNameDetail = 'project_dumper_detail';
            $this->tableSales = 'sales_order_dumper';
            $this->tableCustomer = 'customer';
            $this->tableCity = 'city';
            $this->tableLocation = 'location';
            $this->tableWarehouse = 'warehouse'; 
            $this->tableStatus = 'transaction_status';
            $this->tableHistory = 'history';
            $this->isTransaction = true; 		
         
            $this->securityObject = 'ProjectDumper';   

            $this->arrDataDetail = array();  
            $this->arrDataDetail['pkey'] = array('hidDetailKey');
            $this->arrDataDetail['refkey'] = array('pkey','ref');
            $this->arrDataDetail['locationkey'] = array('hidLocationDetailKey');
            $this->arrDataDetail['qty'] = array('qty','number'); 
            $this->arrDataDetail['priceperdistance'] = array('pricePerDistance','number');

            $arrDetails = array();
            array_push($arrDetails, array('dataset' => $this->arrDataDetail));

            $this->arrData = array(); 
            $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
            $this->arrData['code'] = array('code');
            $this->arrData['trdate'] = array('trDate','date');
            $this->arrData['warehousekey'] = array('selWarehouseKey');
            $this->arrData['name'] = array('projectName');
            $this->arrData['customerkey'] = array('hidCustomerKey');
            $this->arrData['locationkey'] = array('hidLocationKey');
            $this->arrData['trdesc'] = array('trDesc');
            $this->arrData['statuskey'] = array('selStatus');
          
            $this->arrDataListAvailableColumn = array(); 
            array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align'=>'center', 'format' => 'date'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
            array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'locationname','default'=>true, 'width' => 150));
            array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200));
            array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
            array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
      
            
            array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
            $this->newLoad = true;
            $this->overwriteConfig();
    }
 
            
    
    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
               '.$this->tableCustomer.'.name as customername,
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableStatus.'.status as statusname ,
               '.$this->tableLocation.'.name as locationname 
            FROM 
                '.$this->tableName.' left join  '.$this->tableLocation.' on 
                    '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey,
                '.$this->tableStatus.', 
                '.$this->tableCustomer.' left join '.$this->tableCity.' on  
                     '.$this->tableCustomer.'.citykey = '.$this->tableCity.'.pkey,
                '.$this->tableWarehouse.'
            WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                     '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                     '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
        ' .$this->criteria ; 


//        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
//        $this->setLog($sql,true);
        return $sql;
    }  
  
    function validateForm($arr,$pkey = ''){
            $item = new Item();   

            $arrayToJs = parent::validateForm($arr,$pkey); 

            $customerkey = $arr['hidCustomerKey'];  
            $locationkey = $arr['hidLocationKey']; 
            $arrItemkey = $arr['hidLocationDetailKey']; 
            $arrQty = $arr['qty']; 
            $arrPriceinunit = $arr['pricePerDistance']; 


            //validasi kalo status gk menunggu gk bisa edit 
            if (!empty($pkey)){
                $rs = $this->getDataRowById($pkey);
                if ($rs[0]['statuskey'] <> 1){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
                }
            }  

            if(empty($customerkey)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
            
            if(empty($locationkey)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['location'][1]);
            

            if(empty($arrItemkey)) 
                 $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);  


            $arrDetailKeys = array(); 

            for($i=0;$i<count($arrItemkey);$i++) { 
                if (empty($arrItemkey[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['pricelist'][3]); 	
                } 

                if (!empty($arrItemkey[$i])){
                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
                    if ($this->unFormatNumber($arrQty[$i]) <= 0){ 
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[510]);  
                    }
                } 
            }



            return $arrayToJs;
    }
      
    function getDetailWithRelatedInformation($pkey,$criteria=''){


        $sql = 'select
                '.$this->tableNameDetail .'.*, 
                '.$this->tableLocation.'.name as locationname, 
                '.$this->tableLocation.'.code as locationcode
              from
                '.$this->tableNameDetail .',
                '.$this->tableLocation.'
              where
                '.$this->tableNameDetail .'.locationkey = '.$this->tableLocation.'.pkey and
                refkey = '.$this->oDbCon->paramString($pkey) . ' ';

        $sql .= $criteria;
        return $this->oDbCon->doQuery($sql);

    } 
    
    function getJobInvoice($pkey,$criteria=''){


        $sql = 'select
                '.$this->tableLocation.'.name as locationname, 
                '.$this->tableLocation.'.code as locationcode,
                '.$this->tableLocation.'.pkey as locationkey,
                GROUP_CONCAT('.$this->tableSales.'.pkey) as sokey,
                GROUP_CONCAT('.$this->tableSales.'.code) as socode,
                SUM('.$this->tableSales.'.weight) as totalweight,
                SUM('.$this->tableSales.'.distance) as totaldistance,
                SUM('.$this->tableSales.'.total) as grandtotal,
                COUNT( * ) AS totalritase
              from
                '.$this->tableName .',
                '.$this->tableNameDetail .',
                '.$this->tableSales .',
                '.$this->tableLocation.'
              where
                '.$this->tableNameDetail.'.refkey = '.$this->tableName .'.pkey and
                '.$this->tableName.'.pkey = '.$this->tableSales .'.refkey and
                '.$this->tableNameDetail .'.locationkey = '.$this->tableSales.'.locationkey and
                '.$this->tableSales .'.locationkey = '.$this->tableLocation.'.pkey and
                '.$this->tableSales .'.statuskey in (2,3) and
                '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey) . ' ';

        $sql .= $criteria;
        $sql .= ' group by '.$this->tableSales .'.locationkey,'.$this->tableSales .'.refkey';
        $this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql);

    } 

    /*function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){

         $sql = 'select
                    concat('.$this->tableName. '.code,\' - \','.$this->tableName. '.name) as value,
                    '.$this->tableName. '.pkey
                from 
                    '.$this->tableName . ','.$this->tableCustomer.','.$this->tableStatus.'
                where  		
                    '.$this->tableName . '.customerkey = '.$this->tableCustomer.'.pkey and
                    '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey

            ';

        if($searchCriteria <> '')
            $sql .= ' ' .$searchCriteria;

        if($orderCriteria <> ''){
            $sql .= ' ' .$orderCriteria;

        }

        if($limit <> '')
            $sql .= ' ' .$limit;
        
        $this->setLog($sql,true);

        return $this->oDbCon->doQuery($sql);	
    } */
    
    function generateDefaultQueryForAutoComplete($returnField){ 
        
        $sql = 'select
					'.$returnField['key'].',
                    concat('.$this->tableName. '.code,\' - \','.$this->tableName. '.name) as value,
                    trdate
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			';
        
        $this->setLog($sql,true);
         return $sql;
     }
    
    
    function normalizeParameter($arrParam, $trim = false){ 
        $arrParam = parent::normalizeParameter($arrParam,true);   
        return $arrParam;
    }
    
}
?>
