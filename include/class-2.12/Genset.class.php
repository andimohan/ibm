<?php
class Genset extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'genset';
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'Genset'; 
		
	}
	
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.','.$this->tableStatus.' where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
   
   
	function addData ($arrParam){
		$arrayToJs = array();
		
		try{
	
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
			
			$pkey = $this->getNextKey($this->tableName);
            
			$code = $this->getNewCustomCode($arrParam);	 
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
	 		$arrayToJs = $this->validateForm($arrParam);
				if (!empty($arrayToJs))
					return $arrayToJs;
		 
			$sql = '
					INSERT INTO
					'.$this->tableName.'(
						pkey,
						code,
						name,  
						statuskey,
						createdby,
						createdon 
					)
					VALUES (
						'.$pkey.',
						'.$this->oDbCon->paramString($arrParam['code']).',
						'.$this->oDbCon->paramString($arrParam['name']).',  
						'.$this->oDbCon->paramString($arrParam['selStatus']).',
						'.$this->oDbCon->paramString($arrParam['createdBy']).',
						now() 
					)
			';
			
			$this->oDbCon->execute($sql);
			
            $this->setTransactionLog(INSERT_DATA,$pkey);
            
			$this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);
            $arrayToJs[0]['pkey'] = $pkey;
            
		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());
		}	
		
		return $arrayToJs;
			
	}
	
	function editData($arrParam){    
		$arrayToJs =  array();
	
		try{		
	  	 	
			if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
		
			$code = $this->getNewCustomCode($arrParam);	 
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
			$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
			if (!empty($arrayToJs)) 
					return $arrayToJs;
					
            
				$sql = '
						UPDATE	
						 '.$this->tableName .'
						SET	 
                            code ='.$this->oDbCon->paramString($arrParam['code']).',
                            name ='.$this->oDbCon->paramString($arrParam['name']).',  
                            statuskey = '.$this->oDbCon->paramString($arrParam['selStatus']).',
                            modifiedby = '.$this->oDbCon->paramString($arrParam['modifiedBy']).',
                            modifiedon = now() 
						WHERE	
						  pkey = '.$this->oDbCon->paramString($arrParam['hidId']).'
						
				';    
 
             
                $this->oDbCon->execute($sql);                
				
                $this->setTransactionLog(UPDATE_DATA,$arrParam['hidId']);
            
				$this->oDbCon->endTrans(); 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
			
		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}			
		
		return $arrayToJs; 
	}
	
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey);

		$name = $arr['name'];   
        
	 	$rs = $this->isValueExisted($pkey,'name',$name);
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['item'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['item'][2]);
		} 
        
		return $arrayToJs;
	 }	 
	  
 
	function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
		$sql = 'select
					'.$this->tableName. '.pkey,  '.$this->tableName. '.name as value
				from 
					'.$this->tableName . ' ,'.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			';
	
		if(!empty($fieldname)){
			
			$sql .= ' and ' ;
			
			if($mustmatch)
				$sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$sql .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria;
	 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit;
		return $this->oDbCon->doQuery($sql);	
	}
}
?>