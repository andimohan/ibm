<?php 

class AssetItemMovement extends BaseClass{

   function __construct(){
      parent::__construct();

      $this->tableName = 'asset_item_movement';
      $this->tableAssetItem = 'asset_item';
      $this->tableItemUnit = 'item_unit';
      $this->tableCustomer = 'customer'; 
		$this->tableSupplier = 'supplier';
      $this->tableSalesOrderRentalItemAssetHeader = 'sales_order_rental_item_asset_header';
      $this->tableSalesOrderRentalItemAssetDetail = 'sales_order_rental_item_asset_detail';  

   }

   function getQuery()
   {

      $sql = '
		    select 
				 ' . $this->tableName . '.*,
				 ' . $this->tableWarehouse . '.name as warehousename,
				 ' . $this->tableCustomer . '.name as customername,
				 ' . $this->tableSupplier . '.name as suppliername
			from  
               ' . $this->tableName . ' 
                     left join ' . $this->tableWarehouse . ' on  ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey
                     left join ' . $this->tableCustomer . ' on  ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey
                     left join ' . $this->tableSupplier . ' on  ' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey,
               ' . $this->tableAssetItem . '    
			where
				' . $this->tableName . '.itemkey =  ' . $this->tableAssetItem . '.pkey
		' . $this->criteria;

      return $sql;
   }

   function updateAssetItemMovement($refkey, $trdate, $customerkey, $itemkey, $warehousekey, $qtyinbaseunit,  $reftablekey, $note, $supplierkey = 0, $costinbaseunit = 0, $vendorpartnumberkey = 0) {
   
      $createdby =  base64_decode($_SESSION[$this->loginAdminSession]['id']);
      
      $sql = '
         INSERT INTO 
            '. $this->tableName .' (
               refkey,
               trdate,
               customerkey,
               supplierkey,
               itemkey,
               vendorpartnumberkey,
               warehousekey,
               qtyinbaseunit,
               costinbaseunit,
               reftablekey,
               note,
               statuskey,
               createdon,
               createdby
            ) VALUES (
               '. $this->oDbCon->paramString($refkey) .',
               '. $this->oDbCon->paramString($trdate) .',
               '. $this->oDbCon->paramString($customerkey) .',
               '. $this->oDbCon->paramString($supplierkey) .',
               '. $this->oDbCon->paramString($itemkey) .',
               '. $this->oDbCon->paramString($vendorpartnumberkey) .',
               '. $this->oDbCon->paramString($warehousekey) .',
               '. $this->oDbCon->paramString($qtyinbaseunit) .',
               '. $this->oDbCon->paramString($costinbaseunit) .',
               '. $this->oDbCon->paramString($reftablekey) .',
               '. $this->oDbCon->paramString($note) .',
               1,
               now(),
               '.$this->oDbCon->paramString($createdby).'
            )';
       
         $this->oDbCon->execute($sql);
   }

   function cancelAssetItemMovement($refkey, $reftablekey) {
      
      $sql = '
         UPDATE
            '. $this->tableName .'
         SET
            statuskey = 2
         WHERE 
            refkey = '. $this->oDbCon->paramString($refkey) .' and
            reftablekey = '. $this->oDbCon->paramString($reftablekey) .'
      ';

      $this->oDbCon->execute($sql);

   }

   function cancelAssetItemMovementByItem($refkey, $itemKey, $reftablekey) {
      $sql = '
         UPDATE
            '. $this->tableName .'
         SET
            statuskey = 2
         WHERE 
            refkey = '. $this->oDbCon->paramString($refkey) .' and
            itemkey = '. $this->oDbCon->paramString($itemKey) .' and
            reftablekey = '. $this->oDbCon->paramString($reftablekey) .'
      ';

      $this->oDbCon->execute($sql);

   }

   function getCOGSAssetItem($pkey) {
      $sql = '
         select 
            '. $this->tableName .'.*,
            coalesce(sum('. $this->tableName .'.costinbaseunit),0) as totalcogs 
         from 
            '. $this->tableName .'
         where
            '. $this->tableName .'.itemkey = '. $this->oDbCon->paramString($pkey) .' and
            '. $this->tableName .'.statuskey = 1 
      ';
      
      $rs = $this->oDbCon->doQuery($sql);

      return $rs[0]['totalcogs'];
   }  

   function getLastDataByItem($itemkey) {
      
      $sql = '
            select
               '. $this->tableName .'.*
            from
               '. $this->tableName .'
            where
               '. $this->tableName .'.itemkey = '. $this->oDbCon->paramString($itemkey) .' and
               '. $this->tableName .'.statuskey = 1 
            order by pkey desc limit 1;
      ';

      $rs = $this->oDbCon->doQuery($sql);

      return $rs;
      //return last data in asset item movement by itemkey
   }

   //function untuk mengecek,
   //apakah unit ada di customer
   function getAvailableCustomerUnit($unitkey, $customerkey) {
      $sql = '
         select
            '. $this->tableName .'.*,
            '. $this->tableAssetItem .'.name as itemunitname
         from
            '. $this->tableName .'
             left join '. $this->tableAssetItem .' on '. $this->tableName .'.itemkey = '. $this->tableAssetItem .'.pkey
         where
            '. $this->tableName .'.itemkey = '. $this->oDbCon->paramString($unitkey) .' and
            '. $this->tableName .'.customerkey <> 0 and
            '. $this->tableName .'.statuskey = 1
            order by trdate desc limit 1; 
      ';

      $rs = $this->oDbCon->doQuery($sql);

      $result = true;
      if(!empty($rs)) {
         $customerUnit = $rs[0]['customerkey'];
         if($customerUnit <> $customerkey) {
            $result = false;
         }
      } else {
         $result = false;
      }
      return $result;
   
   }


}

?>