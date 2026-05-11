<?php
class WorkProgress extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'work_progress_detail'; 
        $this->tableEmployee = 'employee';
        $this->tableWorkStep = 'work_progress_step';
        $this->tableWorkOrder = 'trucking_service_work_order'; 
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['wokey'] = array('hidWOKey');
        $this->arrData['driverkey'] = array('hidEmployeeKey');
        $this->arrData['description'] = array('trDesc');
        $this->arrData['progresskey'] = array('hidProgressKey');
        $this->arrData['statuskey'] = array('selStatus');  
           
	}
	
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
                    '.$this->tableEmployee.'.name as drivername,
                    '.$this->tableWorkOrder.'.code as wocode,
                    '.$this->tableWorkStep.'.name as progressname 
				from
					'.$this->tableName.',
                    '.$this->tableWorkOrder.', 
                    '.$this->tableWorkStep.',  
                    '.$this->tableEmployee.' 
                        
                where 
                    '.$this->tableName.'.reftablekey = '.$this->tableWorkOrder.'.pkey and
                    '.$this->tableName.'.progresskey = '.$this->tableWorkStep.'.pkey and
                    '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey
                    
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
		 
            $arrParam['pkey'] = $pkey;  
            $this->updateData($arrParam,INSERT_DATA);   
            //$this->updateDetail($pkey,$arrParam);
            
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
					
            
            $arrParam['pkey'] = $arrParam['hidId']; 
            $this->updateData($arrParam, UPDATE_DATA); 
            //$this->updateDetail($arrParam['hidId'],$arrParam);
            
            $this->oDbCon->endTrans(); 
            $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
			
		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false, $e->getMessage());  
		}			
		
		return $arrayToJs; 
	}
    
    function updateDetail($pkey,$arrParam){
		
	 	$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
        $arrProgress = $arrParam['hidProgressKey'];
        //$arrDate = $arrParam['detailDate'];
        $arrDescription = $arrParam['description'];

            for ($i=0;$i<count($arrProgress);$i++){


                $sql = 'insert into '.$this->tableNameDetail.' (
                            refkey,
                            progresskey,
                            description
                         ) values (
                            '.$this->oDbCon->paramString($pkey).',
                            '.$this->oDbCon->paramString($arrProgress[$i]).',
                            '.$this->oDbCon->paramString($arrDescription[$i]).' 
                        )';	 
                $this->oDbCon->execute($sql); 
            } 
        

     	
	}
	
	function validateForm($arr,$pkey = ''){
        
		 
		$arrayToJs = parent::validateForm($arr,$pkey);
        /*$wokey = $arr['hidWOKey']; 
        
        
        if(empty($wokey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceWorkOrder'][1]);
		}*/
 
		return $arrayToJs;
	 }	 
         
    function getProgress($wokey,$userkey = ''){
            
           $userkey = (empty($userkey)) ? $this->userkey : $userkey; 
           $rsProgress = $this->searchData('','',true, ' and reftable = '.$this->oDbCon->paramString($this->tableName).' and reftablekey = ' . $this->oDbCon->paramString($wokey). ' and '.$this->tableName.'.driverkey = ' . $this->oDbCon->paramString($userkey),' order by '.$this->tableWorkStep.'.orderlist desc');
           return $rsProgress;
    }
    
    function updateWorkProgress($arrParam){
        
        $workProgressStep = new WorkProgressStep();
        
        $arrayToJs = array();    
         
        try{ 
            
			if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);
                 
            //update nextc progress 
            $wokey =  $arrParam['hidWOKey']; 
        
            // validasi...
            if(empty($wokey))
                return;
 
            $rsProgress = $this->getProgress($wokey);
            $currentStep = count($rsProgress);
             
            $rsWorkStep = $workProgressStep->searchData($workProgressStep->tableName.'.statuskey',1,true,' order by orderlist asc, pkey asc');
             
            while(!empty($rsWorkStep) && !empty($rsProgress) && $rsWorkStep[0]['pkey'] <> $rsProgress[0]['progresskey'] ){ 
                array_shift($rsWorkStep);
            } 
            
            if (!empty($rsProgress)) 
                array_shift($rsWorkStep);
            
            if (empty($rsWorkStep))
                return;
                 
            $progresskey =  $rsWorkStep[0]['pkey'];
            $progressname =  $rsWorkStep[0]['name'];
            
  
            $sql = 'insert into '.$this->tableName.' ( 
                            reftable,
                            reftablekey,
                            progresskey,
                            driverkey ,
                            createdon
                         ) values ( 
                            '.$this->oDbCon->paramString($this->tableName).',
                            '.$this->oDbCon->paramString($wokey).',
                            '.$this->oDbCon->paramString($progresskey).',
                            '.$this->oDbCon->paramString($this->userkey).',
                            now()
                        )';	 
             
            $this->oDbCon->execute($sql);
                	
            $this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);
          
            $rsProgressStep = $workProgressStep->getNextStep($progresskey);
            $arrayToJs[0]['pkey'] = $rsProgressStep[0]['pkey'];
            $arrayToJs[0]['name'] = $rsProgressStep[0]['name'];
            $arrayToJs[0]['laststep'] = (count($rsWorkStep) <= 1) ? true : false ;
         
            }
                catch(Exception $e){
                $this->oDbCon->rollback();
                $this->addErrorList($arrayToJs,false,$e->getMessage());
		    }

        return $arrayToJs;
    }
    
}
?>