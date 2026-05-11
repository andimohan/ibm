<?php
class ContractDuration extends BaseClass{
     
   function __construct(){
		
		parent::__construct();
 
		$this->tableName = 'contract_duration';  
		$this->securityObject = 'contractDuration'; 
        $this->tableInterestMaturity = 'interest_maturity';
		$this->tableStatus = 'master_status';  
       
        /*
        $this->arrLockedTable = array();
        $defaultFieldName = 'durationkey'; 
        array_push($this->arrLockedTable, array('table'=>'ap_payment_header','field'=>$defaultFieldName));  
        */   
	}
	 
	 
  function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
                    '.$this->tableInterestMaturity.'.name as interestmaturityname,
                    fine_maturity.name as finematurityname,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName . ',
                    '.$this->tableStatus.' ,
                    '.$this->tableInterestMaturity.',
                    '.$this->tableInterestMaturity.' fine_maturity
                where  		
					'.$this->tableName . '.interestmaturitykey = '.$this->tableInterestMaturity.'.pkey and
					'.$this->tableName . '.finematuritykey = fine_maturity.pkey and
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
						duedays, 
                        interest,
                        interestmaturitykey,
                        fine,
                        finematuritykey,
						statuskey,
						createdby,
						createdon
					)
					VALUES	( 
						'.$pkey.', 
						'.$this->oDbCon->paramString($arrParam['code']).',
						'.$this->oDbCon->paramString($arrParam['name']).',  
						'.$this->oDbCon->paramString($this->unFormatNumber($arrParam['duedays'])).',  
						'.$this->oDbCon->paramString($this->unFormatNumber($arrParam['interest'])).',  
						'.$this->oDbCon->paramString($arrParam['selInterestMaturity']).',
                        '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['fine'])).',  
				        '.$this->oDbCon->paramString($arrParam['selFineMaturity']).',
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
						 duedays = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['duedays'])).',
                         interest = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['interest'])).',  
						 interestmaturitykey = '.$this->oDbCon->paramString($arrParam['selInterestMaturity']).',
                         fine = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['fine'])).',  
						 finematuritykey = '.$this->oDbCon->paramString($arrParam['selFineMaturity']).',
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
		$duedays = $this->unFormatNumber($arr['duedays']);  
	 	 
				 
	 	$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['top'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['top'][2]);
		}
		 
		if (!is_numeric($duedays) || $duedays < 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['duedays'][2]);
		}
		  	
		return $arrayToJs;
	 } 
	
    function getInterestMaturity(){
        $sql = 'select * from '.$this->tableInterestMaturity;
        $rs = $this->oDbCon->doQuery($sql);	
        
        return $rs;
    }
	   
}
		
?>