<?php 
class ItemMovementPO extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item_movement_po' ; 
 		 
   }
   
   function getQuery(){
	     
	   return '
		    select 
				 '.$this->tableName.'.* 
			from  '.$this->tableName.' 
			where
				1=1 
		   ' .$this->criteria ; 
   } 
   
   function sumItemMovement($itemkey, $endDate=''){
		  
		$criteria = '';
 
		if (!empty($endDate)){
			$criteria .= ' and createdon < '.$this->oDbCon->paramDate($endDate,' / ');
		}
		$sql = 'select coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" from '.$this->tableName.'  where statuskey = 1 and itemkey = '.$this->oDbCon->paramString($itemkey) . $criteria;		 
		   
		$rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['qtyinbaseunit'];
	}
	
	function updateItemMovement($refkey, $itemkey, $qtyinbaseunit, $costinbaseunit, $reftable,  $note){
		
		$createdby =  base64_decode($_SESSION[$this->loginAdminSession]['id']);
		
		$totalqty = 0;
		
		$saldoakhir = $this->sumItemMovement($itemkey);
		  
		$totalqty = $saldoakhir + $qtyinbaseunit;
		
		if($totalqty<0){
			 
			$item = new Item();
			$rsItem = $item->getDataRowById($itemkey);

			throw new Exception('<strong>'.$rsItem[0]['name']. '</strong>. ' .$this->errorMsg[402]);
		}
			
		$sql = '
			INSERT INTO		
			  '.$this->tableName.' (
			  	refkey,
				itemkey, 
				qtyinbaseunit,
				costinbaseunit,
				reftable, 
				note,
				statuskey,
				createdon,
				createdby
			)
			VALUES (
				'.$this->oDbCon->paramString($refkey).',
				'.$this->oDbCon->paramString($itemkey).',  
				'.$this->oDbCon->paramString($qtyinbaseunit).',
				'.$this->oDbCon->paramString($costinbaseunit).',
				'.$this->oDbCon->paramString($reftable).',
				'.$this->oDbCon->paramString($note).',
				1  ,
				now(),
				'.$this->oDbCon->paramString($createdby).'
			)';			
			
		$this->oDbCon->execute($sql); 
		 
		return true;
	}
 
	
	
	function cancelMovement($refkey,$tableName){
		$sql = 'update '.$this->tableName.' set statuskey = 2 where refkey = ' . $this->oDbCon->paramString($refkey) .' and reftable  = ' . $this->oDbCon->paramString($tableName);
		$this->oDbCon->execute($sql);
		
		$sql = 'select * from '.$this->tableName.'  where refkey = ' . $this->oDbCon->paramString($refkey) .' and reftable  = ' . $this->oDbCon->paramString($tableName);
		$rs = $this->oDbCon->doQuery($sql);
		
	}
	
}  

?>