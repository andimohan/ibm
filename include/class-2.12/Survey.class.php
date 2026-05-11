<?php
  
class Survey extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'survey_question';
		$this->tableNameDetail = 'survey_options';
		$this->tableStatus = 'master_status';
		  
		$this->securityObject = 'Survey'; 
		 
   }
    	
   function getQuery(){
	   
	   return '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableStatus.'.status as statusname
			FROM '.$this->tableStatus.', '.$this->tableName.'  
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
 		' .$this->criteria ; 
		 
    }  
	
   
   function addData($arrParam){
	   
		try{						
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
		 	         
		  		$arrayToJs = $this->validateForm($arrParam);
				if (!empty($arrayToJs)) 
						return $arrayToJs;
						
				$pkey = $this->getNextKey($this->tableName);
				$usecode = $this->useAutoCode($this->tableName); 
	 
              if($usecode == 1)  
				$arrParam['code'] =  $this->getNewCode($this->tableName); 
		
			
				$sql = '
						INSERT INTO		
						 '.$this->tableName .' (
                            pkey, 
							code,
							question,
							statuskey,
							createdby,
							createdon
						)
						VALUES	( 
							'.$pkey.', 
							'.$this->oDbCon->paramString($arrParam['code']).',
							'.$this->oDbCon->paramString($arrParam['question']).', 
							1,
							'.$this->oDbCon->paramString($arrParam['createdBy']).', 
							now()
						)
				';
			 
				$this->oDbCon->execute($sql);
				                                    
				$this->updateDetail($pkey, $arrParam);	
                         
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
		
		try{ 
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
				
				$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
				if (!empty($arrayToJs)) 
						return $arrayToJs;
				 
				$sql = '
						UPDATE	
						 '.$this->tableName .'
						SET	  
							question = '.$this->oDbCon->paramString($arrParam['question']).', 
							modifiedby = '.$this->oDbCon->paramString($arrParam['modifiedBy']).',
							modifiedon = now() 
						WHERE	
						 pkey = '.$this->oDbCon->paramString($arrParam['hidId']).'
				';
														   
				$this->oDbCon->execute($sql);
				$this->updateDetail($arrParam['hidId'], $arrParam);  
                $this->setTransactionLog(UPDATE_DATA,$arrParam['hidId']);
							
				$this->oDbCon->endTrans();
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());    
		}		
		
		return $arrayToJs; 
			 

	}	
	
    function updateDetail($pkey,$arrParam){
		
	 	$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		 
		$arrAnswer = $arrParam['answer']; 
     	for ($i=0;$i<count($arrAnswer);$i++){	
			$arrAnswer[$i] = trim($arrAnswer[$i]);
			
			if (empty($arrAnswer[$i]))
				continue;
				
		 	$answer = $arrAnswer[$i];
			 
			$sql = 'insert into '.$this->tableNameDetail.' (
						refkey,
						answer
					 ) values (
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($answer).'
					)';	 
			$this->oDbCon->execute($sql);
                                        
		}
		 
					
	}
        
	function delete($id, $forceDelete = false,$reason = ''){ 
		$arrayToJs =  array();
			 	
		  try{ 
				$arrayToJs = $this->validateDelete($id);
				if (!empty($arrayToJs)) 
						return $arrayToJs;
			 
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]); 
				 
				  
				$rs=$this->getDataRowById($id);
				  
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);

				$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);

                $this->setTransactionLog(DELETE_DATA,$id);
              
				$this->oDbCon->endTrans(); 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
				 
				
			}catch(Exception $e){
					$this->oDbCon->rollback();
					$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			}			
				
			return $arrayToJs;	
	 }
 
        
     function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = array();
		  
		return $arrayToJs;
	 }
 
	 
}
?>