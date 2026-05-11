<?php 
class AssetItemTurnover extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'asset_item_turnover'; 
		$this->tableWarehouse = 'warehouse' ;   
		$this->tableAssetItem = 'asset_item' ;  
	 
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');   
        $this->arrData['refkey'] = array('hidRefKey'); 
        $this->arrData['refcode'] = array('refCode'); 
        $this->arrData['trdate'] = array('trDate','date'); 
        $this->arrData['jodate'] = array('joDate','date'); 
        $this->arrData['warehousekey'] = array('selWarehouse'); 
        $this->arrData['reftabletype'] = array('hidRefTable'); 
        $this->arrData['assetitemkey'] = array('hidAssetItemKey'); 
        $this->arrData['refkey1'] = array('hidRefKey1'); 
        $this->arrData['refcode1'] = array('refCode1'); 
        $this->arrData['refkey2'] = array('hidRefKey2'); 
        $this->arrData['refcode2'] = array('refCode2');  
        $this->arrData['amount'] = array('amount','number'); 
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');    
        
       
   }
    
   function getQuery(){
	     
	   $sql =  '
		    select 
				  '.$this->tableName.'.*,
				  '.$this->tableWarehouse .'.name as warehousename,
				  '.$this->tableAssetItem .'.name as assetitemname
			from  
                  '.$this->tableName.',
                  '.$this->tableAssetItem.',
                  '.$this->tableWarehouse .'  
			where
				'.$this->tableName.'.assetitemkey = '.$this->tableAssetItem.'.pkey and  
                '.$this->tableName.'.warehousekey = ' . $this->tableWarehouse .'.pkey and
                '.$this->tableName.'.statuskey = 1
		   ' .$this->criteria ;
       return $sql;
   }
    
    function validateForm($arr,$pkey = ''){
        $arrayToJs = parent::validateForm($arr,$pkey);
        // amount <> 0 
        $amount = $arr['amount']; 
        if($amount==0)
            $this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][1]); 
        return $arrayToJs;
    }

    
    function cancelMovement($refkey, $tableType){
        $sql = 'update '.$this->tableName.' set statuskey = 2 where refkey = ' . $this->oDbCon->paramString($refkey) .' and reftabletype = ' . $this->oDbCon->paramString($tableType);
        $this->oDbCon->execute($sql);
    }
    
    function normalizeParameter($arrParam, $trim = false){  
         
            $arrParam = parent::normalizeParameter($arrParam); 

            $arrParam['hidRefKey1'] = (empty($arrParam['hidRefKey1'])) ? '' : $arrParam['hidRefKey1']; 
            $arrParam['refCode1'] = (empty($arrParam['refCode1'])) ? '' : $arrParam['refCode1']; 
            $arrParam['hidRefKey2'] = (empty($arrParam['hidRefKey2'])) ? '' : $arrParam['hidRefKey2']; 
            $arrParam['refCode2'] = (empty($arrParam['refCode2'])) ? '' : $arrParam['refCode2']; 
            $arrParam['trDesc'] = (empty($arrParam['trDesc'])) ? '' : $arrParam['trDesc']; 
 
 
        return $arrParam;
    }
   
  

}  

?>
