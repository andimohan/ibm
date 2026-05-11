<?php

class ItemPromo extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item_promo_header';
		$this->tableNameDetail = 'item_promo_detail';
		$this->securityObject = 'ItemPromo';
		$this-> tableStatus = 'master_status';
		
		 
		
	}
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName.'.*,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.','.$this->tableStatus.'
				where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
 		' .$this->criteria ; 
		 
    }
	
	function addData($arrParam){   
		
		 $arrayToJs =  array(); 
	
		try{		
				 
					 
			if (!$this->oDbCon->startTrans())
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
						startdate,
						enddate,
						statuskey,
						trnotes,
						createdby,
						createdon
					)
					VALUES	( 
						'.$pkey.', 
						'.$this->oDbCon->paramString($arrParam['code']).',  
						'.$this->oDbCon->paramDate($arrParam['txtStartDate'],' / ').',  
						'.$this->oDbCon->paramDate($arrParam['txtEndDate'],' / ').',  
						'.$this->oDbCon->paramString($arrParam['selStatus']).',
						'.$this->oDbCon->paramString($arrParam['trNotes']).',
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
	
		$arrayToJs =  array();
			
		try{		
	  	
			$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
			if (!empty($arrayToJs)) 
					return $arrayToJs;
					
			if (!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
	
			$sql = '
						UPDATE	
						 '.$this->tableName .'
						SET	  
							   
						 code ='.$this->oDbCon->paramString($arrParam['code']).', 
						 startdate ='.$this->oDbCon->paramDate($arrParam['txtStartDate'],' / ').',  
						 enddate ='.$this->oDbCon->paramDate($arrParam['txtEndDate'],' / ').',
						 statuskey ='.$this->oDbCon->paramString($arrParam['selStatus']).',   
						 trnotes = 	'.$this->oDbCon->paramString($arrParam['trNotes']).',
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
		$arrPriceinunit = $arrParam['priceInUnit'];;
		$arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
		$arrDiscountType = $arrParam['selDiscountType'];
		
     	for ($i=0;$i<count($arrItemkey);$i++){	
		
			$priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
		 	$discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
			$discountType =  $this->unFormatNumber($arrDiscountType[$i]);
			
			$discountValue = $discount;
		 
			if ($discount != 0){
				if ($discountType == 2)
					$discountValue = $discount/100 * $priceInUnit;
			}
			
			$promoPrice = ($priceInUnit - $discountValue); 
			 
			$sql = 'insert into '.$this->tableNameDetail.' (
						refkey,
						itemkey,
						priceinunit,
						discounttype,
						discount,
						promoprice
					 ) values (
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrItemkey[$i]).',
						'.$this->oDbCon->paramString($priceInUnit).',
						'.$this->oDbCon->paramString($discountType).',
						'.$this->oDbCon->paramString($discount).',
						'.$this->oDbCon->paramString($promoPrice).'
					)';	 
			
			$this->oDbCon->execute($sql);
                                        
		}
		 
					
	}
	
	function validateForm($arr,$pkey = ''){
		$item = new Item();   
		 
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$hasDetail = false;
		   
	 	$datestart = $arr['txtStartDate'];
	 	$dateend = $arr['txtEndDate'];
		$arrItemkey = $arr['hidItemKey']; 
		$discountValueInUnit = $arr['discountValueInUnit'];
		 
	 	if(  $this->dateDiff($datestart,$dateend)  <= 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['date'][3]);
		} 
		
		
		for($i=0;$i<count($arrItemkey);$i++) {
			if (!empty($arrItemkey[$i])) $hasDetail = true;
			
			if (!empty($arrItemkey[$i]) && $this->unFormatNumber($discountValueInUnit[$i]) <= 0){
				$rsItem = $item->getDataRowById($arrItemkey[$i]);
				$this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '<br>' . $this->errorMsg[500]); 
			}
		}
		 
		if(!$hasDetail){
			$this->addErrorList($arrayToJs,false, $this->errorMsg[501]);
		} 
		
		return $arrayToJs;
	 }
	
	function delete($id, $forceDelete = false,$reason = ''){ 
		 
		try{			
				  
				$arrayToJs =  array();
			 	
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			
		 		 
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
	
	function getPromoList($criteria = ''){
			$sql = ' select * from ( 	
								select
									item_promo_detail.promoprice ,item.pkey, item.name, item.sellingprice,  item.isvariant
								from 
									item_promo_header,item_promo_detail  inner join item on  item_promo_detail.itemkey = item.pkey 
								where
									item_promo_header.pkey = item_promo_detail.refkey and
														date(startdate) <= date(now()) and date(enddate) >= date(now()) and
									item_promo_header.statuskey = 1	 order by item_promo_header.enddate desc 
									) promotable group by promotable.pkey
			   ';
			   
		if (!empty($criteria))
			$sql .= $criteria;
				   
		return $this->oDbCon->doQuery($sql);			
	}
	 
	
}
?>