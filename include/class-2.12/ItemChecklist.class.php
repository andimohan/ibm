<?php
class ItemChecklist extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'item_checklist';     
		$this->securityObject = 'ItemChecklist'; 
		$this->tableStatus = 'master_status'; 
	 
        $this->arrLockedTable = array();
        $defaultFieldName = 'itemkey'; 
        array_push($this->arrLockedTable, array('table'=>'item_checklist_group_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_content_of_package_detail','field'=>$defaultFieldName)); 
       
               
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
     
		$this->overwriteConfig();
	}
	
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.'
				where  		  
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
	
	
	function addData($arrParam){   
		
		 $arrayToJs =  array(); 
	
		try{		
 
			if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
			$code = $this->getNewCustomCode($arrParam);	 
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
			$arrayToJs = $this->validateForm($arrParam);
            if (!empty($arrayToJs)) 
                return $arrayToJs;
			 	
			$pkey = $this->getNextKey($this->tableName);  
             
			$sql = '
					INSERT INTO		
					 '.$this->tableName .' (
						pkey, 
						code,  
						name, 
						statuskey,
						createdby,
						createdon
					)
					VALUES	( 
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
						 statuskey ='.$this->oDbCon->paramString($arrParam['selStatus']).',   
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
			$this->addErrorList($arrayToJs,false,$e->getMessage());  
		}			
			
		return $arrayToJs; 
	}  
	 
	function validateForm($arr,$pkey = ''){ 
		     
		$arrayToJs = parent::validateForm($arr,$pkey);  
		   
	 	$name = $arr['name'];    
        
        $rsName = $this->isValueExisted($pkey,'name',$name);	
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		} else{ 
            if (count($rsName) <> 0) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]); 
        }
        
		return $arrayToJs;
	 } 
	 
}
		
?>