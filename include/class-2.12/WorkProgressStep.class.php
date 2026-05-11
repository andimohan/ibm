<?php

class WorkProgressStep extends BaseClass{ 
    
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'work_progress_step'; 
		$this->tableStatus = 'master_status';  
		$this->securityObject = 'WorkProgressStep';	  
       
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['orderlist'] = array('orderList','number');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['trdesc'] = array('trDesc'); 
		 	
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
			  
		 	if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
            
            $pkey = $this->getNextKey($this->tableName); 
			$code = $this->getNewCustomCode($arrParam);	 
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
			$arrayToJs = $this->validateForm($arrParam);
			if (!empty($arrayToJs)) 
					return $arrayToJs; 
					
			$pkey = $this->getNextKey($this->tableName);  
			
            $arrParam['pkey'] = $pkey;  
            $this->updateData($arrParam,INSERT_DATA); 
            
			$this->oDbCon->endTrans();
					 
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
            $arrayToJs[0]['pkey'] = $pkey;
            $arrayToJs[0]['value'] = $arrParam['name'];
					 
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());   
		}
		
		return $arrayToJs; 	 	
		 
	}


	function editData($arrParam){  
		
		$arrayToJs =  array();
			
		try{	  
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
            
			$code = $this->getNewCustomCode($arrParam);	 
            $arrParam['code'] = (is_array($code)) ? $code[0] : $code;
            
			$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
			if (!empty($arrayToJs)) 
					return $arrayToJs;   
            
			$arrParam['pkey'] = $arrParam['hidId']; 
            $this->updateData($arrParam, UPDATE_DATA); 
			$this->oDbCon->endTrans();  
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
					
				
		} catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}		
				 
 		return $arrayToJs; 
	}
	 
	 
	function validateForm($arr,$pkey = ''){
		  
        $arrayToJs = parent::validateForm($arr,$pkey); 
          
	  	$name = $arr['name'];  
		 
        $rsName = $this->isValueExisted($pkey,'name',$name);	
	 	if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else{ 
            if (count($rsName) <> 0) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]); 
        }
	     
		return $arrayToJs;
	 }   
    
    
    function getNextStep($currentstep = 0){
        
        $criteria = '';
        if(!empty($currentstep)){
            $rs = $this->getDataRowById($currentstep);
            $criteria = ' and orderlist > ' . $this->oDbCon->paramString($rs[0]['orderlist']) ;
        }
         
        $rs = $this->searchData($this->tableName.'.statuskey',1,true, $criteria ,'order by orderlist asc limit 1');
        return $rs;
    }
    
    
  }

?>