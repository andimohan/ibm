<?php

class Storefront extends BaseClass{
	
    function __construct(){

        parent::__construct();

        $this->tableName = 'item_category_storefront';
        $this->tableMarketplace = 'marketplace';
        $this->tableStatus = 'master_status';
        $this->securityObject = 'StoreFront';

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');  
        $this->arrData['marketplacestorefrontkey'] = array('storefrontId');
        $this->arrData['marketplacekey'] = array('hidMarketplaceKey');
        $this->arrData['name'] = array('name');

        $this->newLoad = true;
            
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'marketplace','dbfield' => 'marketplacename','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'marketplace','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->includeClassDependencies(array( 
                'Marketplace.class.php', 
        ));  
    }
 
       
    
    function getQuery(){
        
        
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableStatus.'.status as statusname,
                '.$this->tableMarketplace.'.name as marketplacename
            FROM '.$this->tableStatus.',
                 '.$this->tableName.',
                 '.$this->tableMarketplace.'
            WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.marketplacekey = '.$this->tableMarketplace.'.pkey
            ' .$this->criteria ;
        
                                         
        return $sql;
    }

    function validateForm($arr,$pkey = ''){ 

        $arrayToJs = parent::validateForm($arr,$pkey); 
        
        $name = $arr['name'];
        $marketplacename = $arr['hidMarketplaceKey'];
                
		if(empty($name)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		 
        if(empty($marketplacename)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['marketplace'][1]);
		 
        return $arrayToJs;
    }

    
    function normalizeParameter($arrParam, $trim=false){
        unset( $this->arrData['marketplacestorefrontkey']);             
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }

    function afterUpdateData($arrParam, $action){
        $pkey = $arrParam['pkey']; 
        
        if($this->hasActiveMarketplace()){
            $marketplace = new Marketplace();
            $marketplace->updateStorefrontInAllMarketplace($pkey); 	      
        } 
    }
    
	function delete($id, $forceDelete = false,$reason = ''){ 
		 
		$rsStoreFront = $this->getDataRowById($id);
		
		$arrayToJs = parent::delete($id,$forceDelete,$reason); 
		
        if($this->hasActiveMarketplace()){
            $marketplace = new Marketplace();
			$marketplaceObj = $marketplace->getMarketplaceObj($rsStoreFront[0]['marketplacekey']); 
            $marketplaceObj[0]['obj']->deleteStorefront($rsStoreFront[0]['marketplacestorefrontkey']); 	      
        } 
		
		return $arrayToJs;
	}
    

}

?>