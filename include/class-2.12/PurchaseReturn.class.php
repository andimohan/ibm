<?php
  
class PurchaseReturn extends BaseClass{  
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'purchase_return_header';
		$this->tableNameDetail = 'purchase_return_detail';
		$this->tableWarehouse = 'warehouse';
		$this->tableStatus = 'transaction_status';
		$this->tableSupplier = 'supplier';
		  
		$this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
       
		$this->securityObject = 'PurchaseReturn'; 
		 
   }
    
   function getQuery(){
	   
	   return '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname,
               '.$this->tableSupplier.'.name as suppliername
			FROM '.$this->tableStatus.', '.$this->tableWarehouse.'  , 
                '.$this->tableName.' left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.' .pkey
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 		' .$this->criteria ; 
		 
    }
	
   function addData($arrParam){
	   
		$arrayToJs = array();
		
		try{						
			
				if(!$this->oDbCon->startTrans())
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
							warehousekey,
							trdesc,
							trdate, 
                            supplierkey,
							statuskey,
							createdby,
							createdon
						)
						VALUES	( 
							'.$pkey.', 
							'.$this->oDbCon->paramString($arrParam['code']).',
							'.$this->oDbCon->paramString($arrParam['selWarehouseKey']).',
							'.$this->oDbCon->paramString($arrParam['trDesc']).',
							'.$this->oDbCon->paramDate($arrParam['trDate'],' / ').', 
                             '.$this->oDbCon->paramString($arrParam['hidSupplierKey']).',
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
		
		$arrayToJs = array();
		
		try{ 
				if(!$this->oDbCon->startTrans())
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
							code = 	'.$this->oDbCon->paramString($arrParam['code']).',
							trdate = '.$this->oDbCon->paramDate($arrParam['trDate'],' / ').', 
							warehousekey = '.$this->oDbCon->paramString($arrParam['selWarehouseKey']).', 
                            supplierkey = '.$this->oDbCon->paramString($arrParam['hidSupplierKey']).',
							trdesc = 	'.$this->oDbCon->paramString($arrParam['trDesc']).',
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
		 
		$arrItemkey = $arrParam['hidItemKey']; 
		$arrQty = $arrParam['qty'];  
		
        $item = new Item();
		        
     	for ($i=0;$i<count($arrItemkey);$i++){
			
			if (empty($arrItemkey[$i]))
				continue;
				
			$rsItem = $item->getDataRowById($arrItemkey[$i]); 
			$baseunitkey = $rsItem[0]['baseunitkey'];
			$unitconvmultiplier = 1;
			
			 
		 	$qty =  $this->unFormatNumber($arrQty[$i]);
		 	 
			$sql = 'insert into '.$this->tableNameDetail.' (
						refkey,
						itemkey,
						qty,  
						unitkey,
						unitconvmultiplier, 
						qtyinbaseunit 
					 ) values (
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrItemkey[$i]).',
						'.$this->oDbCon->paramString($qty).',
						'.$this->oDbCon->paramString($baseunitkey).',
						1, 
						'.$this->oDbCon->paramString($qty).' 
					)';	 
			$this->oDbCon->execute($sql);
                                        
		}
		 
					
	}
         
        
     function validateForm($arr,$pkey = ''){
		  
		$item = new Item();    
		
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$supplierkey = $arr['hidSupplierKey']; 
		$arrItemkey = $arr['hidItemKey']; 
		$arrQty = $arr['qty']; 
	 
	 	//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
		 
        if(empty($supplierkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
		} 
		
		for($i=0;$i<count($arrItemkey);$i++) {
			if (empty($arrItemkey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} 
			if (!empty($arrItemkey[$i]) && $this->unFormatNumber($arrQty[$i]) <= 0){
				$rsItem = $item->getDataRowById($arrItemkey[$i]);
				$this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[500]); 
			}
		}
		  
		
		return $arrayToJs;
	 }
	  
	   
	function changeStatus($id,$status,$reason='',$copy=false, $autoChangeStatus = false){
		$arrayToJs = array();
		try{
			 	 
			switch ($status){
				case 1 :  $arrayToJs = $this->validateInput($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs;
						
						  break;
				case 2 : $arrayToJs = $this->validateConfirm($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs;
						 break;
				case 3 : $arrayToJs = $this->validateClose($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs; 
						  break;
			
				case 4 : $arrayToJs = $this->validateCancel($id);
						 if (!empty($arrayToJs)) 
								return $arrayToJs;
						   break;  
			}
		 
		 
		 	 
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
					 
			switch ($status){
			 	case 2 : $this->confirmTrans($id); break; 
				case 4 : $this->cancelTrans($id,$copy);
                          $this->afterCancelTrans($id);
                          break;  
			}
		 
		 
			$sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id);
			$this->oDbCon->execute($sql);
			
            $rsStatus = $this->getStatusById ($status); 
            $this->setTransactionLog($rsStatus[0]['pkey'],$id);
            
			$this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
	    } catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				 
 		return $arrayToJs; 
 	}
	  

	function validateConfirm($id){
		
		$rs = $this->getDataRowById($id);
		  
		$arrayToJs = array();
		
		if($rs[0]['statuskey'] <> 1){  
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[203]);
		} 
		
	 	return $arrayToJs;
	 }		

	function confirmTrans($id){
		$rsHeader = $this->getDataRowById($id); 
		 
		$itemMovement = new ItemMovement();  
		$item = new Item();
		
		$note = $rsHeader[0]['code'] .'. Retur Pembelian';
		$warehouse = new Warehouse();
		$rsWarehouse = $warehouse->getDataRowById($rsHeader[0]['warehousekey']);
	 	$rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
		
		for($i=0;$i<count($rsDetail); $i++){	 
		   $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
		   $itemMovement->updateItemMovement($id,$rsDetail[$i]['itemkey'],-$rsDetail[$i]['qtyinbaseunit'], $rsItem[0]['cogs'] ,$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate']);
		}	 
		
	} 
	
	function cancelTrans($id,$copy){ 
		
		$rsHeader = $this->getDataRowById($id);
		  	
		if ($rsHeader[0]['statuskey'] == 1)
			return;
	
		$itemMovement = new ItemMovement();  
		$itemMovement->cancelMovement($id,$this->tableName);
		 
		if ($copy == 1)
			$this->copyDataOnCancel($id);	  
		
	} 
}
?>