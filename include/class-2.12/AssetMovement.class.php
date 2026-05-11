<?php 
class AssetMovement extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'asset_movement' ;   
		$this->tableItemInWarehouse = 'asset_in_warehouse' ; 
		$this->tableItem = 'asset' ; 
		$this->tableWarehouse = 'warehouse' ;  
		$this->tableCustomer = 'customer' ; 

   }
   
   function getQuery(){
	     
	   return '
		    select 
				 '.$this->tableName.'.*,
				 '. $this->tableWarehouse .'.name as warehousename
			from  
                '.$this->tableName.' 
                    left join ' . $this->tableWarehouse .' on  '.$this->tableName.'.warehousekey = ' . $this->tableWarehouse .'.pkey,
                
                '.$this->tableItem.'    
			where
				 '.$this->tableName.'.itemkey =  '.$this->tableItem.'.pkey
		   ' .$this->criteria ; 
   }
    
   function sumItemsMovement($arrItemKey, $warehousekey = '',$endDate=''){
      	$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
            
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
            
			$criteria .= ' and warehousekey in ('.$warehousekey.')';  
		}
       
		if (!empty($endDate)){
            $dateMethod = $this->loadSetting('movementDateMethod');  
            $datefield = ($dateMethod == 2)  ? 'trdate' : 'createdon';  
            $criteria .= ' and '.$datefield.' <= '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
		}
       
         $sql = 'select 
                        '.$this->tableName.'.itemkey, 
                        coalesce(sum('.$this->tableName.'.qtyinbaseunit),0) as "qtyinbaseunit" 
                    from '.$this->tableName.'  where '.$this->tableName.'.statuskey = 1 and 
                    '.$this->tableName.'.itemkey in ('. $this->oDbCon->paramString($arrItemKey, ',' ) . ') 
                    '. $criteria.
                    ' group by '.$this->tableName.'.itemkey,'.$this->tableName.'.categorykey';

        //$this->setLog($sql,true);
        
        $rs =  $this->oDbCon->doQuery($sql);	 
        return $rs;     

   }
   
     function sumItemMovement($itemkey, $warehousekey = '',$endDate=''){
		       
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
            
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
            
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
            //$criteria .= $this->getCompanyCriteria($this->tableWarehouse);
            
		}
         
         
		if (!empty($endDate)){
            $dateMethod = $this->loadSetting('movementDateMethod');  
            $datefield = ($dateMethod == 2)  ? 'trdate' : 'createdon';  
            $criteria .= ' and '.$datefield.' <= '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
		}
       
		$sql = 'select coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" from '.$this->tableName.'  where statuskey = 1 and itemkey in ('. $itemkey . ')'. $criteria;	
       
        $rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['qtyinbaseunit'];
	}

	 
   function getItemQOH($itemkey, $warehousekey = ''){ 
	
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
             
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
       
        // filter by company warehouse
        $warehouse = new Warehouse();
        $arrWarehouse = implode(',',$warehouse->getCompanyWarehouse());
        if (!empty($arrWarehouse)) 
            $criteria .= ' and warehousekey in ('.$arrWarehouse.')';
        
        
		$sql = 'select coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" from '.$this->tableItemInWarehouse.'  where itemkey = '.$this->oDbCon->paramString($itemkey).''. $criteria;		 
      
		$rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['qtyinbaseunit'];
	}
	  	 
   function getItemsQOH($arrItemKey, $warehousekey = ''){
       
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
             
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
       
        // filter by company warehouse
        $warehouse = new Warehouse();
        $arrWarehouse = implode(',',$warehouse->getCompanyWarehouse());
        if (!empty($arrWarehouse)) 
            $criteria .= ' and warehousekey in ('.$arrWarehouse.')';
        
        
		$sql = 'select 
                    itemkey,
                    isvariant,
                    parentkey,
                    '.$this->tableItem.'.code as itemcode,
                    coalesce(sum(qtyinbaseunit),0) as "qtyinbaseunit" 
                from 
                    '.$this->tableItemInWarehouse.', '.$this->tableItem.' 
                where 
                    itemkey in('.$this->oDbCon->paramString($arrItemKey,',').')
                    ' . $criteria .' and
                    '.$this->tableItemInWarehouse.'.itemkey = '.$this->tableItem.'.pkey 
                group by itemkey';		 
                 
	 	return  $this->oDbCon->doQuery($sql);
	}
	  
    function getItemQOR($itemkey, $warehousekey = ''){
       
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
             
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
       
        // filter by company warehouse
        $warehouse = new Warehouse();
        $arrWarehouse = implode(',',$warehouse->getCompanyWarehouse());
        if (!empty($arrWarehouse)) 
            $criteria .= ' and warehousekey in ('.$arrWarehouse.')';
        
       
		$sql = 'select coalesce(sum(qtyonreserveinbaseunit),0) as "qtyonreserveinbaseunit" from '.$this->tableItemInWarehouse.'  where itemkey = '.$this->oDbCon->paramString($itemkey) . $criteria;		 
        
		$rs =  $this->oDbCon->doQuery($sql);		 
	 	return $rs[0]['qtyonreserveinbaseunit'];
	}
    
    function getItemsQOR($arrItemKey, $warehousekey = ''){
       
		$criteria = '';
		if (!empty($warehousekey)){
            
            if(!is_array($warehousekey))
                $warehousekey = explode(',',$warehousekey);
             
            $warehousekey = implode(',',$this->oDbCon->paramString($warehousekey));
			$criteria .= ' and warehousekey in ('.$warehousekey.')';
		}
       
        // filter by company warehouse
        $warehouse = new Warehouse();
        $arrWarehouse = implode(',',$warehouse->getCompanyWarehouse());
        if (!empty($arrWarehouse)) 
            $criteria .= ' and warehousekey in ('.$arrWarehouse.')';
        
        
		$sql = 'select 
                    itemkey,
                    isvariant,
                    parentkey,
                    '.$this->tableItem.'.code as itemcode,
                    coalesce(sum(qtyonreserveinbaseunit),0) as "qtyonreserveinbaseunit" 
                from 
                    '.$this->tableItemInWarehouse.', '.$this->tableItem.' 
                where 
                    itemkey in('.$this->oDbCon->paramString($arrItemKey,',').') ' . $criteria .' and
                    '.$this->tableItemInWarehouse.'.itemkey = '.$this->tableItem.'.pkey 
                group by itemkey ';		 
                 
	 	return  $this->oDbCon->doQuery($sql);
	}
	  
    

    function updateItemMovement($refkey, $itemkey, $qtyinbaseunit, $costinbaseunit, $reftable, $warehousekey,$note,$trdate, $vendorpartnumberkey = 0){
	
		$warehouse = new Warehouse();
		$asset = new Asset();
        
		$rsItem = $asset->getDataRowById($itemkey);
        
		$createdby =  base64_decode($_SESSION[$this->loginAdminSession]['id']);
		/*
        $negativeQOH = $this->loadSetting('negativeQOH');
        if($negativeQOH != 1){
            
            $totalqty = 0; 
            $saldoakhir = $this->getItemQOH($itemkey, $warehousekey);  
            $totalqty = $saldoakhir + $qtyinbaseunit; 

            // stok gk boleh minus, hanya jika dr transaksi barang keluar
            if($qtyinbaseunit < 0 && $totalqty < 0)
                throw new Exception('<strong>'.$rsItem[0]['name']. '</strong>. ' .$this->errorMsg[402]);

            $rsWarehouse = $warehouse->getDataRowById($warehousekey);

            //klo barang di keluarin di cabang/warehouse tidak cukup
            // ini gk tau buat ap .. lupa...
            if($totalqty < 0)
                throw new Exception('<strong>'.$rsItem[0]['name']. '</strong>. ' .$this->errorMsg[402]); 

        }
        */
        
		$sql = '
			INSERT INTO		
			  '.$this->tableName.' (
			  	refkey,
                trdate,
				itemkey,
				vendorpartnumberkey,
				warehousekey, 
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
				'.$this->oDbCon->paramString($trdate).',
				'.$this->oDbCon->paramString($itemkey).',  
				'.$this->oDbCon->paramString($vendorpartnumberkey).', 
				'.$this->oDbCon->paramString($warehousekey).',
				'.$this->oDbCon->paramString($qtyinbaseunit).',
				'.$this->oDbCon->paramString($costinbaseunit).',
				'.$this->oDbCon->paramString($reftable).',
				'.$this->oDbCon->paramString($note).',
				1  ,
				now(),
				'.$this->oDbCon->paramString($createdby).'
			)';			 
         
		$this->oDbCon->execute($sql); 
		$arrReturn = $this->updateItemInWarehouse($itemkey,$warehousekey);

		return $arrReturn;
	}

	function updateItemInWarehouse($itemkey,$warehousekey){
		
		
		$asset = new Asset();
		$warehouse = new Warehouse();
        //$marketplace = new Marketplace();
        
		$rsItem = $asset->getDataRowById($itemkey);
		$rsWarehouse = $warehouse->getDataRowById($warehousekey);
		 
        $saldoakhir = $this->sumItemMovement($itemkey, $warehousekey); 
        
      /*  //klo barang di keluarin di cabang/warehouse tidak cukup
        
        $negativeQOH = $this->loadSetting('negativeQOH');
        if($negativeQOH != 1){ 
            if($saldoakhir < 0)
                throw new Exception('<strong>'.$rsItem[0]['name']. '</strong>. ' .$this->errorMsg[402]);
            
            // others script
        }
		*/
        $sql = 'select itemkey from '.$this->tableItemInWarehouse.' where itemkey='.$this->oDbCon->paramString($itemkey).' and  warehousekey = '.$this->oDbCon->paramString($warehousekey).' limit 0,1';
        $result = $this->oDbCon->doQuery($sql);

        if(empty($result)){ 
            $sql = '
                INSERT INTO	'.$this->tableItemInWarehouse.' (
                    itemkey,
                    warehousekey,
                    qtyinbaseunit 
                    )
                VALUES (
                    '.$this->oDbCon->paramString($itemkey).',
                    '.$this->oDbCon->paramString($warehousekey).',
                    '.$this->oDbCon->paramString($saldoakhir).' 
                    )';			
            
            $this->oDbCon->execute($sql);

        }else{
            $sql = '
                UPDATE '.$this->tableItemInWarehouse.'
                    SET	 
                        qtyinbaseunit = '.$saldoakhir.' 
                    WHERE	
                        itemkey='.$this->oDbCon->paramString($itemkey).' and 
                        warehousekey = '.$this->oDbCon->paramString($warehousekey).'
                ';			
            $this->oDbCon->execute($sql);
        }
		 
	 					 
/*		$sql = 'update 
					item 
				set 
					cogs = '.$item->getCOGS($itemkey).'
				where 
					pkey = '.$this->oDbCon->paramString($itemkey);
		 
		$this->oDbCon->execute($sql);*/
		
//		$item->updateItemPriceByMargin($itemkey,$rsItem[0]['sellingprice'],$rsItem[0]['marginpercentage']);	
		  
        return  array('itemkey' => $itemkey,  'warehousekey' => $warehousekey, 'qtyinbaseunit' => $saldoakhir);
	}
    
    
	function cancelMovement($refkey,$tableName){
		$sql = 'update '.$this->tableName.' set statuskey = 2 where refkey = ' . $this->oDbCon->paramString($refkey) .' and reftable  = ' . $this->oDbCon->paramString($tableName);
		$this->oDbCon->execute($sql);
		
		$sql = 'select * from '.$this->tableName.'  where refkey = ' . $this->oDbCon->paramString($refkey) .' and reftable  = ' . $this->oDbCon->paramString($tableName);
		$rs = $this->oDbCon->doQuery($sql);
		
        $arrItemMovement = array();
		for($i=0;$i<count($rs);$i++){ 
			$arrResult = $this->updateItemInWarehouse($rs[$i]['itemkey'],$rs[$i]['warehousekey']);
            array_push($arrItemMovement, $arrResult);
        }
        
        return $arrItemMovement;
	}
	  
    
    
}  

?>
