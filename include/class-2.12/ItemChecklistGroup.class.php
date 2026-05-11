<?php

class ItemChecklistGroup extends BaseClass{ 
    
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item_checklist_group_header'; 
        $this->tableNameDetail ='item_checklist_group_detail';
        $this->tableItem ='item_checklist';
		$this->tableStatus = 'master_status';  
		$this->securityObject = 'ItemChecklistGroup';	  
		 	 
       
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['itemkey'] = array('hidItemKey',array('mandatory'=>true));
        $this->arrDataDetail['qty'] = array('qty',array('datatype'=>'number','mandatory'=>true)); 
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
         
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=>$this->tableNameDetail,'field' => 'refkey'));  
       
              
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'parent','title' => 'parent','dbfield' => 'parentname','default'=>true, 'width' => 150));
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
	
    //updateDetail

 
    
  /*  function updateDetail($pkey,$arrParam){
		
	 	$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		 
		$arrChkItemKey = $arrParam['hidItemKey']; 
		$arrQty = $arrParam['qty'];  
	 	         
     	for ($i=0;$i<count($arrChkItemKey);$i++){
			
			if (empty($arrChkItemKey[$i]))
				continue;
				  
		 	$qty =  $this->unFormatNumber($arrQty[$i]); 
			 
			$sql = 'insert into '.$this->tableNameDetail.' (
						refkey,
						itemkey,
						qty
					 ) values (
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrChkItemKey[$i]).',
						'.$this->oDbCon->paramString($qty).' 
					)';	 
            $this->oDbCon->execute($sql);
                                        
		} 
	}*/
	 
    function getDetailWithRelatedInformation($pkey,$criteria=''){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*, 
                '.$this->tableItem.'.name as itemname 
              from
			  	'.$this->tableNameDetail .', 
                '.$this->tableItem.' 
			  where
			  	'.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and  
			  	refkey = '.$this->oDbCon->paramString($pkey) . ' ';
       
        $sql .= $criteria;
         
        $sql .= ' order by itemname asc';
          
		return $this->oDbCon->doQuery($sql);
	
   }	 
	function validateForm($arr,$pkey = ''){
		  
        $itemChecklist = new ItemChecklist();
        
        $arrayToJs = parent::validateForm($arr,$pkey); 
          
	  	$name = $arr['name'];  
        $arrItemkey = $arr['hidItemKey'];
        $arrQty = $arr['qty'];
        
        $rsName = $this->isValueExisted($pkey,'name',$name);	
	 	if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else{ 
            if (count($rsName) <> 0) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]); 
        }
		   
   
        $arrDetailKeys = array(); 
         
		for($i=0;$i<count($arrItemkey);$i++) {
		 	if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} else{
                
                if ($this->unFormatNumber($arrQty[$i]) <= 0){
                    $rsItem = $itemChecklist->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[503]); 
                } 
 
                // cek ada detail double gk 
                if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                    $rsItem = $itemChecklist->getDataRowById($arrItemkey[$i]);
                    $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    array_push($arrDetailKeys, $arrItemkey[$i]);
                }   
            } 
		}
        
        
		return $arrayToJs;
	 }   
    
    /* function delete($id,$forceDelete = false,$reason = ''){
		 
		$arrayToJs =  array();
	 
		try{ 
		
	 		$arrayToJs = $this->validateDelete($id);
			if (!empty($arrayToJs)) 
				return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
			 
                $this->deleteReference($id);
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans(); 

				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
				 
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}*/
    
  }

?>