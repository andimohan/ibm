<?php
class PriceUpdate extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'price_update_header';   
		$this->tableNameDetail = 'price_update_detail';
		$this->tablePricingCategory = 'pricing_category';
		$this->securityObject = 'PriceUpdate'; 
		$this->tableStatus = 'transaction_status';
        $this->isTransaction = true; 
       
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['categorykey'] = array('hidCategoryKey'); 
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
              'PricingCategory.class.php',
               'Item.class.php',
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
                '.$this->tablePricingCategory.'.name as pricingcategoryname,
                '.$this->tablePricingCategory.'.iscaratbase
			  from
			  	'. $this->tableNameDetail .',
                '.$this->tablePricingCategory.'
			  where
			  	' . $this->tableNameDetail .'.categorykey = '.$this->tablePricingCategory.'.pkey and
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }
    
    function getPricingCategoryLastRate($pricingCategoryKey = '', $trdate = '', $fiscalRate=false){

		 $rateField = (!$fiscalRate) ? 'rate' : 'ratebi';
         $pricingCategory = new PricingCategory();  
        
//         if ($pricingCategoryKey == $pricingCategory->getDefaultData()){
//             $arrReturn = array();
//             array_push($arrReturn,array('categorykey' => $pricingCategoryKey, 'rate' => 1));
//             return $arrReturn;
//         }
             

        $criteriaDate = '';
        if (!empty($trdate))
            $criteriaDate = ' and '.$this->tableName.'.trdate <= '.$this->oDbCon->paramDate($trdate,' / '); 
             
         $sql = 'select * from '.$this->tableName.' where statuskey in (2,3)  '.$criteriaDate.'  order by trdate desc, '.$this->tableName.'.pkey desc limit 1'; 
         $rs = $this->oDbCon->doQuery($sql);	 
        
         // kalo blm ad rate,
         if (empty($rs)){
             $arrReturn = array();
             array_push($arrReturn,array('categorykey' => $pricingCategoryKey, 'iscaratbase' =>0, 'rate' => 1));
             return $arrReturn;
         }
          
         $sql = array();
        
         if (empty($pricingCategoryKey))
          array_push($sql,  'select categorykey, 0 as iscaratbase, 1 as rate from (select pkey as categorykey from '.$this->tablePricingCategory.' where systemVariable = 1 limit 1) defaultRate ');
        
        
         if (!empty($rs)){ 
              $tempSql = ' 
                     select  
                        categorykey, iscaratbase, coalesce('.$this->tableNameDetail.'.'.$rateField.',0) as rate
                     from  
                        '.$this->tableNameDetail.' , 
                        '.$this->tablePricingCategory.' 
                     where   
                        '.$this->tableNameDetail.'.categorykey =  '.$this->tablePricingCategory.'.pkey and
                        '.$this->tableNameDetail.'.refkey =  '.$this->oDbCon->paramString($rs[0]['pkey']);

                     if (!empty($pricingCategoryKey))
                         $tempSql .= ' and  '.$this->tableNameDetail.'.categorykey = ' .$this->oDbCon->paramString($pricingCategoryKey);
             
            array_push($sql,$tempSql);
         }
              
         $sql = implode (' UNION ALL ', $sql);
          
         $rs = (!empty($sql)) ?  $this->oDbCon->doQuery($sql) : array();	
         
		return  $rs;
	}
    
    function afterStatusChanged($rsHeader){ 
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2){
             $item = new Item();

                    // update price jgn ketika afterchangestatus. untuk menghindari kesalahan harga
                    // jd better user harus selalu submit harga baru

                    // loop per kategori pricing
                  $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);

                  foreach($rsDetail as $row){
                      $categorykey = $row['categorykey']; 

                      $rsItem = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.gramasi', $item->tableName.'.carat', $item->tableName.'.additionalprice', $item->tableName.'.pricingcategorykey'), 
                                                    ' and '.$item->tableName.'.statuskey = 1 and '.$item->tableName.'.pricingcategorykey = ' . $this->oDbCon->paramString($categorykey)
                                                    );

                      // hitung ulang di item saja, karena ad beberapa perhitungan seperti kg ke gramasi
                      // harus kirim data rate, karema kalo query ualng, posisinya blm kekonfirmasi transaksi 
                      $item->calculateAutoPrice(array_column($rsItem,'pkey')); 

                  }
 
        }
    }
    
    
    
      function confirmTrans($rsHeader){ 
        
         
          
      }
    
}
		
?>