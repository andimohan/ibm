<?php
class CurrencyRate extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'currency_rate_header';   
		$this->tableNameDetail = 'currency_rate_detail';
		$this->tableCurrency = 'currency';
		$this->securityObject = 'currencyRate'; 
		$this->tableStatus = 'transaction_status';
        $this->isTransaction = true; 
       
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['currencykey'] = array('hidCurrencyKey'); 
        $this->arrDataDetail['ratebefore'] = array('rateBefore','number');
        $this->arrDataDetail['rate'] = array('rate','number');
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        //$this->arrData['trdesc'] = array('trDesc');    
        $this->arrData['statuskey'] = array('selStatus');
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 200));
        

        $this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code')); 

       
        $this->includeClassDependencies(array(
              'Currency.class.php',
              'GeneralJournal.class.php'
        ));


		$this->overwriteConfig();
	 
	}
	
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName . ','.$this->tableStatus.' where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
 		' .$this->criteria ; 
		 
    }
     
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 
		 
	  	return $arrayToJs;
	 } 
	 
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableCurrency.'.name as currencyname
			  from
			  	'. $this->tableNameDetail .',
                '.$this->tableCurrency.'
			  where
			  	' . $this->tableNameDetail .'.currencykey = '.$this->tableCurrency.'.pkey and
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }
    
    function getCurrencyLastRate($currencykey = '', $trdate = '', $fiscalRate=false){

		 $rateField = (!$fiscalRate) ? 'rate' : 'ratebi';
         $currency = new Currency();  
        
         if ($currencykey == $currency->getDefaultData()){
             $arrReturn = array();
             array_push($arrReturn,array('currencykey' => $currencykey, 'rate' => 1));
             return $arrReturn;
         }
             

        $criteriaDate = '';
        if (!empty($trdate))
            $criteriaDate = ' and '.$this->tableName.'.trdate <= '.$this->oDbCon->paramDate($trdate,' / '); 
             
         $sql = 'select * from '.$this->tableName.' where statuskey in (2,3)  '.$criteriaDate.'  order by trdate desc, '.$this->tableName.'.pkey desc limit 1';
        
        
        $rs = $this->oDbCon->doQuery($sql);	 
        
         // kalo blm ad rate,
         if (empty($rs)){
             $arrReturn = array();
             array_push($arrReturn,array('currencykey' => $currencykey, 'rate' => 1));
             return $arrReturn;
         }
          
         $sql = array();
        
         if (empty($currencykey))
          array_push($sql,  'select currencykey, 1 as rate from (select pkey as currencykey from '.$this->tableCurrency.' where systemVariable = 1 limit 1) defaultRate ');
        
        
         if (!empty($rs)){ 
              $tempSql = ' 
                     select  
                        currencykey, coalesce('.$this->tableNameDetail.'.'.$rateField.',0) as rate
                     from  
                        '.$this->tableNameDetail.' 
                     where   
                        '.$this->tableNameDetail.'.refkey =  '.$this->oDbCon->paramString($rs[0]['pkey']);

                     if (!empty($currencykey))
                         $tempSql .= ' and  '.$this->tableNameDetail.'.currencykey = ' .$this->oDbCon->paramString($currencykey);
             
            array_push($sql,$tempSql);
         }
              
         $sql = implode (' UNION ALL ', $sql);
         
        
         $rs = (!empty($sql)) ?  $this->oDbCon->doQuery($sql) : array();	
         
		return  $rs;
	}
}
		
?>
