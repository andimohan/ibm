<?php 
// deprecated
class CashMovement extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'cash_movement'; 
		$this->tableWarehouse = 'warehouse' ;   
	 
   }
    
   function getQuery(){
	     
	   return '
		    select 
				 '.$this->tableName.'.*,
				  ' . $this->tableWarehouse .'.name as warehousename
			from  '.$this->tableName.' left join ' . $this->tableWarehouse .' on  '.$this->tableName.'.warehousekey = ' . $this->tableWarehouse .'.pkey 
			where
				1=1 
		   ' .$this->criteria ; 
   }
   
   function sumCashMovement($coakey, $warehousekey = '',$endDate=''){
		  
		$criteria = '';
		if (!empty($warehousekey)){
            $warehousekey = explode(',',$warehousekey);
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		} 
       
       	if (!empty($endDate)){
            $dateMethod = $this->loadSetting('movementDateMethod'); 
            
            $datefield = 'createdon';
            if ($dateMethod == 2) 
                $datefield = 'trdate';
 
           $criteria .= ' and '.$datefield.' < '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
		}
       
       
		$sql = 'select coalesce(sum(amount),0) as "amount" from '.$this->tableName.'  where statuskey = 1 and coakey = '.$this->oDbCon->paramString($coakey) . $criteria;		 
		   
		$rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['amount'];
	}
    
    function updateCashMovement($refkey,$coakey, $amount, $reftable, $warehousekey,$note,$trdate){
	    // deprecated
        return;
        
		if ($amount == 0)
			return true;
			
		$createdby =  base64_decode($_SESSION[$this->loginAdminSession]['id']);		
		
		//$totalamount = 0;
		
		//$rs = $this->sumCashMovement($warehouse);
	 	//$totalamount = $rs + $amount;
		
		// harus dirapiin validasi nya
		
		//if($totalamount<0){
		//	$err =  '<li>Perubahan status gagal ('.$rsHeader[0]['code'].').<br>Saldo Kas tidak mencukupi pada Cabang '.$rsWarehouse[0]['name'].'.</li>';
		//		throw new Exception($err);}
			
		$sql = '
			INSERT INTO		
			  '.$this->tableName.' (
			    refkey,
				reftable,
                trdate,
				warehousekey, 
				amount, 
				createdon,
				createdby,
				statuskey,
				note,
				coakey
			)
			VALUES (
				'.$this->oDbCon->paramString($refkey).',
				'.$this->oDbCon->paramString($reftable).',
				'.$this->oDbCon->paramString($trdate).', 
				'.$this->oDbCon->paramString($warehousekey).', 
                '.$this->oDbCon->paramString($amount).',
				now(), 
				'.$this->oDbCon->paramString($createdby).',
				1,
				'.$this->oDbCon->paramString($note).',
				'.$this->oDbCon->paramString($coakey).'   
			)';		
                
				
		$this->oDbCon->execute($sql);
		
		return true;
	}
	
	function cancelMovement($refkey,$tableName){
		$sql = 'update '.$this->tableName.' set statuskey = 2 where refkey = ' . $this->oDbCon->paramString($refkey).' and reftable  = ' . $this->oDbCon->paramString($tableName);
		$this->oDbCon->execute($sql);
	}

}  

?>