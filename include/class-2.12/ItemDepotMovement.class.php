<?php 
class ItemDepotMovement extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'item_depot_movement' ;   
        $this->tableItemInDepot = 'item_in_depot' ;  
        $this->tableCustomer = 'customer'; 
		$this->tableItem = 'item' ;  
		$this->tableItemUnit = 'item_unit' ; 
		$this->tableDepot = 'depot ' ;   

        $this->arrConcatField = array();
        $this->arrConcatField['refcode'] = array('field' => 'code', 'value' => array());
        $this->arrConcatField['refdate'] = array('field' => 'trdate', 'value' => array());
        $this->arrConcatField['policenumber'] = array('field' => 'policenumber', 'value' => array());
        $this->arrConcatField['docode'] = array('field' => 'docode', 'value' => array());
  		 
   }
   
   function getQuery(){  
         
       $arrRefTable = $this->getRefTableQuery($this->arrConcatField);
       
	   $sql = '
            select * from ( 
		    select 
				 '.$this->tableName.'.*,
				 '.$this->tableDepot .'.name as depotname,
				 '.$this->tableItem .'.name as itemname,
                 '.$this->tableItem.'.width *'.$this->tableItem.'.length * '.$this->tableItem.'.height * '.$this->tableName .'.qtyinbaseunit as totalvolume, 
                 '.$this->tableItem.'.gramasi * '.$this->tableName .'.qtyinbaseunit as totalweight, 
				 '.$this->tableCustomer .'.name as customername,
				 '.$this->tableItemUnit.'.name as baseunitname,
				 weightunit.name as weightunitname 
                 '.$arrRefTable['concatString'].'
			from  
                '.$this->tableName.' 
                    left join ' . $this->tableDepot .' on  '.$this->tableName.'.depotkey = ' . $this->tableDepot .'.pkey
                    left join ' . $this->tableCustomer .' on  '.$this->tableName.'.customerkey = ' . $this->tableCustomer .'.pkey
                    '.$arrRefTable['joinString'].', 
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' as weightunit,
                '.$this->tableItem.'    
			where
				 '.$this->tableName.'.itemkey =  '.$this->tableItem.'.pkey and
                 '.$this->tableItem.'.baseunitkey =  '.$this->tableItemUnit.'.pkey  and
                 '.$this->tableItem.'.weightunitkey = weightunit.pkey 
            ) '.$this->tableName.' where 1=1  
		   ' .$this->criteria ;  
         
       //$this->setLog($sql);
       return $sql;
   }
    
   
   function sumItemMovement($itemkey, $depotkey = '',$endDate='', $customerkey=''){
		       
		$criteria = '';
		if (!empty($depotkey)){
            
            if(!is_array($depotkey))
                $depotkey = explode(',',$depotkey);
            
            $depotkey = implode(',',$this->oDbCon->paramString($depotkey));
            
			$criteria .= ' and depotkey in ('.$depotkey.')'; 
            
		}
		if (!empty($endDate)){
            $dateMethod = $this->loadSetting('movementDateMethod'); 
            
            $datefield = 'createdon';
            if ($dateMethod == 2) 
                $datefield = 'trdate'; 
            
            $criteria .= ' and '.$datefield.' < '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
		}
       
		if (!empty($customerkey)){
            
            if(!is_array($customerkey))
                $customerkey = explode(',',$customerkey);
            
            $customerkey = implode(',',$this->oDbCon->paramString($customerkey));
            
			$criteria .= ' and customerkey in ('.$customerkey.')'; 
            
		}
       
		$sql = 'select coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" from '.$this->tableName.'  where statuskey = 1 and itemkey in ('. $itemkey . ') '. $criteria;	
           
        $rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['qtyinbaseunit'];
	}
	
	  
   function getItemQOH($itemkey, $depotkey = '', $customerkey = ''){
       
		$criteria = '';
		if (!empty($depotkey)){
            
            if(!is_array($depotkey))
                $depotkey = explode(',',$depotkey);
             
            $depotkey = implode(',',$this->oDbCon->paramString($depotkey));
			$criteria .= ' and depotkey in ('.$depotkey.')';
		}
       
      if (!empty($customerkey)){
            
            if(!is_array($customerkey))
                $customerkey = explode(',',$customerkey);
             
            $customerkey = implode(',',$this->oDbCon->paramString($customerkey));
			$criteria .= ' and customerkey in ('.$customerkey.')';
		}
       
        // filter by company depot
        /*$depot = new Depot();
        $arrDepot = implode(',',$depot->getCompanyDepot());
        if (!empty($arrDepot)) 
            $criteria .= ' and depotkey in ('.$arrDepot.')';*/
        
       
		$sql = 'select coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" from '.$this->tableItemInDepot.'  where itemkey = '.$this->oDbCon->paramString($itemkey) . $criteria;		 
        //$this->setLog($sql);
       
		$rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['qtyinbaseunit'];
	}
	  
        
	function updateItemMovement($arrParam){
		
        //$refkey, $itemkey, $qtyinbaseunit, $costinbaseunit, $reftable, $depotkey,$note,$trdate, $vendorpartnumberkey = ''
            
		$createdby =  base64_decode($_SESSION[$this->loginAdminSession]['id']);
		
		$totalqty = 0; 
		$saldoakhir = $this->getItemQOH($arrParam['itemkey'], $arrParam['depotkey'], $arrParam['customerkey']);  
		$totalqty = $saldoakhir + $arrParam['qtyinbaseunit'];
		 
        
		if($totalqty < 0){
		 	$item = new Item();
			$rsItem = $item->getDataRowById($arrParam['itemkey']);

			throw new Exception('<strong>'.$rsItem[0]['name']. '</strong>. ' .$this->errorMsg[402]);
		}
			   
		$sql = '
			INSERT INTO		
			  '.$this->tableName.' (
			  	refkey,
                trdate,
				itemkey, 
				depotkey, 
                customerkey,
				qtyinbaseunit, 
				reftabletype, 
				note,
				statuskey,
				createdon,
				createdby
			)
			VALUES (
				'.$this->oDbCon->paramString($arrParam['refkey']).',
				'.$this->oDbCon->paramString($arrParam['trdate']).',
				'.$this->oDbCon->paramString($arrParam['itemkey']).',  
				'.$this->oDbCon->paramString($arrParam['depotkey']).',
				'.$this->oDbCon->paramString($arrParam['customerkey']).',
				'.$this->oDbCon->paramString($arrParam['qtyinbaseunit']).', 
				'.$this->oDbCon->paramString($arrParam['tableType']).',
				'.$this->oDbCon->paramString($arrParam['note']).',
				1  ,
				now(),
				'.$this->oDbCon->paramString($createdby).'
			)';			 
         
		$this->oDbCon->execute($sql); 
		$this->updateItemInDepot($arrParam['itemkey'],$arrParam['depotkey'], $arrParam['customerkey']);
		 
        
		return true;
	}

	function updateItemInDepot($itemkey,$depotkey,$customerkey){
		
		$item = new Item();
		$rsItem = $item->getDataRowById($itemkey);
		 
        $saldoakhir = $this->sumItemMovement($itemkey, $depotkey,'',$customerkey);   
		
        $sql = 'select 
                    itemkey
                from 
                    '.$this->tableItemInDepot.' 
                where 
                    itemkey='.$this->oDbCon->paramString($itemkey).' and  
                    depotkey = '.$this->oDbCon->paramString($depotkey) .' and 
                    customerkey = ' . $this->oDbCon->paramString($customerkey) .'
                limit 0,1';
        
        $result = $this->oDbCon->doQuery($sql);
        
        

        if(empty($result)){ 
            $sql = '
                INSERT INTO	'.$this->tableItemInDepot.' (
                    itemkey,
                    depotkey,
                    customerkey,
                    qtyinbaseunit 
                    )
                VALUES (
                    '.$this->oDbCon->paramString($itemkey).',
                    '.$this->oDbCon->paramString($depotkey).',
                    '.$this->oDbCon->paramString($customerkey).',
                    '.$this->oDbCon->paramString($saldoakhir).' 
                    )';			
            
            $this->oDbCon->execute($sql);

        }else{
            $sql = '
                UPDATE '.$this->tableItemInDepot.'
                    SET	 
                        qtyinbaseunit = '.$saldoakhir.' 
                    WHERE	
                        itemkey='.$this->oDbCon->paramString($itemkey).' and 
                        depotkey = '.$this->oDbCon->paramString($depotkey).' and 
                        customerkey = '.$this->oDbCon->paramString($customerkey).'
                ';			
            $this->oDbCon->execute($sql);
        }
		  
		 
	}
	
	
	function cancelMovement($refkey,$tableType){
		$sql = 'update '.$this->tableName.' set statuskey = 2 where refkey = ' . $this->oDbCon->paramString($refkey) .' and reftabletype  = ' . $this->oDbCon->paramString($tableType);
		$this->oDbCon->execute($sql);
		
		$sql = 'select * from '.$this->tableName.'  where refkey = ' . $this->oDbCon->paramString($refkey) .' and reftabletype  = ' . $this->oDbCon->paramString($tableType);
		$rs = $this->oDbCon->doQuery($sql);
		
		for($i=0;$i<count($rs);$i++)
			$this->updateItemInDepot($rs[$i]['itemkey'],$rs[$i]['depotkey'],$rs[$i]['customerkey']);
	}
    
    	 
    function getItemMovementMonthlySummary($startPeriod = '',$endPeriod ='',  $criteria='',$groupby = ''){
        
        // DATE FORMAT => d / m / Y

        if (empty($startPeriod)) $startPeriod = DEFAULT_EMPTY_DATE; 
        if (empty($endPeriod)) $endPeriod = date('d / m / Y');
         
        
        // be aware, perubahan group harus update ke concat index jg
        if (empty($groupby))
            $groupby = 'itemkey, year(trdate), month(trdate)';
        
        $sql  = '
                select 
                    item.name,
                    itemkey,
                    concat(itemkey,\'-\',DATE_FORMAT(trdate, \'%c%Y\'))  as periodindex,
                    month(trdate) as month,   
                    year(trdate) as year, 
                    sum(qtyinbaseunit) as total,
                    sum( if(qtyinbaseunit > 0, qtyinbaseunit,0)) as totalin,
                    sum( if(qtyinbaseunit < 0, qtyinbaseunit,0)) as totalout
                from 
                    '.$this->tableName.', 
                    '.$this->tableItem.' 
                where  
                    '.$this->tableName.'.statuskey = 1 and
                    '.$this->tableName.'.itemkey = '.$this->tableItem.'.pkey';
          
        $sql .= ' and  trdate between '. $this->oDbCon->paramDate($startPeriod.' 00:00:00',' / ') .' and LAST_DAY('. $this->oDbCon->paramDate($endPeriod.' 23:59:59',' / ') .')';
        
       // $sql .= ' and  trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\') ';
          
        if (!empty($criteria))
            $sql .= ' ' .$criteria;
        
        $sql .=' group by ' .$groupby;
            
        //$this->setLog($sql);
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
    
}  

?>
