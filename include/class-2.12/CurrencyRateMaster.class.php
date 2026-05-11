<?php
class CurrencyRateMaster extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'currency_rate_master_header';   
		$this->tableNameDetail = 'currency_rate_master_detail';
		$this->tableCurrency = 'currency';
		$this->securityObject = 'currencyRateMaster'; 
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
        $this->arrData['bankname'] = array('bankName');
        $this->arrData['statuskey'] = array('selStatus');
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bank','title' => 'bank','dbfield' => 'bankname','default'=>true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 200));

       $this->arrSearchColumn = array ();
       array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
       array_push($this->arrSearchColumn, array('Bank', $this->tableName . '.bankname'));
       
        $this->includeClassDependencies(array(
              'Currency.class.php',
              'GeneralJournal.class.php',
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

}
		
?>