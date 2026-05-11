<?php
class AssetItem extends BaseClass
{

   function __construct()
   {

      parent::__construct();
      $this->tableName = 'asset_item';
      $this->tableStatus = 'master_status';
      $this->tableWarehouse = 'warehouse'; 
      $this->tableBrand = 'brand';
      $this->tableItemUnit = 'item_unit';
      $this->tableCategoryAssetItem = 'category_asset_item';
      $this->tableCarSeries = 'car_series';

      $this->newLoad = true;
      $this->securityObject = 'AssetItem';

      $this->arrData = array();
      $this->arrData['pkey'] = array('pkey');
      $this->arrData['code'] = array('code');
      $this->arrData['name'] = array('name');
      $this->arrData['refpurchasekey'] = array('hidPurchaseKey');
      $this->arrData['statuskey'] = array('selStatus');
      $this->arrData['categorykey'] = array('selCategory');
      $this->arrData['warehousekey'] = array('selWarehouse');
//      $this->arrData['unitkey'] = array('selUnit');
      $this->arrData['brandkey'] = array('hidBrandKey');
      $this->arrData['assetgroupkey'] = array('selAssetGroup');
      $this->arrData['acquisitionvalue'] = array('acquisitionValue', 'number');
      $this->arrData['initdepreciationvalue'] = array('initDepreciationValue', 'number');
      $this->arrData['bookvalue'] = array('bookValue', 'number');
      $this->arrData['islinked'] = array('islinked');
      $this->arrData['acquisitiondate'] = array('acquisitionDate', 'date');
      $this->arrData['explicensedate'] = array('expLicenseDate', 'date');
      $this->arrData['depreciationvalue'] = array('depreciationValue', 'number');
      $this->arrData['qoh'] = array('qoh', 'number');
      $this->arrData['qor'] = array('qor', 'number');
      $this->arrData['onrental'] = array('onrental', 'number');

      $this->arrData['capacitykey'] = array('selCapacity');
      $this->arrData['capacity'] = array('capacity', 'number');
      $this->arrData['mastkey'] = array('selMast');
      $this->arrData['mast'] = array('mast', 'number');
      $this->arrData['serialnumber'] = array('serialNumber');
      $this->arrData['typekey'] = array('hidTypeKey');
      $this->arrData['assetcategorykey'] = array('hidCategoryKey');
      $this->arrData['chassisnumber'] = array('chassisNumber');
      $this->arrData['year'] = array('year');
      $this->arrData['itemcondition'] = array('itemCondition');
      $this->arrData['note'] = array('note');

      //$this->arrData['bookvalue'] = array('bookValue', 'number');
      //$this->arrData['residue'] = array('residue', 'number');
      //$this->arrData['aging'] = array('aging', 'number');
      //$this->arrData['typekey'] = array('selType');
      //$this->arrData['acquisitionvalue'] = array('acquisitionValue', 'number');

      $this->arrLockedTable = array();
      $defaultFieldName = 'itemkey'; 
      array_push($this->arrLockedTable, array('table'=>'work_order_rental_item_asset_detail','field'=>$defaultFieldName)); 
      array_push($this->arrLockedTable, array('table'=>'sales_order_item_asset_detail','field'=>'assetitemkey')); 


      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
      array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 150));
//      array_push($this->arrDataListAvailableColumn, array('code' => 'category', 'title' => 'category', 'dbfield' => 'categoryname', 'default' => true, 'width' => 130));
//      array_push($this->arrDataListAvailableColumn, array('code' => 'type', 'title' => 'type', 'dbfield' => 'typename', 'default' => true, 'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'brand', 'title' => 'brand', 'dbfield' => 'brandname', 'default' => true, 'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'assetGroup', 'title' => 'assetGroup', 'dbfield' => 'assetgroupname', 'default' => true, 'width' => 120));
//      array_push($this->arrDataListAvailableColumn, array('code' => 'aging', 'title' => 'usefulLife', 'dbfield' => 'aging', 'default' => true, 'width' => 100, 'format' => 'number', 'align' => 'right'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'bookvalue', 'title' => 'bookValue', 'dbfield' => 'bookvalue', 'default' => true, 'align' => 'right', 'width' => 100, 'format' => 'number'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 80));
      //array_push($this->arrDataListAvailableColumn, array('code' => 'residue', 'title' => 'residue', 'dbfield' => 'residue', 'default' => true, 'align' => 'right', 'width' => 100, 'format' => 'number'));
      //      array_push($this->arrDataListAvailableColumn, array('code' => 'explicensedate', 'title' => 'expirationDate', 'dbfield' => 'explicensedate', 'align' => 'center', 'width' => 100, 'format' => 'date'));
      
      array_push($this->arrDataListAvailableColumn, array('code' => 'category', 'title' => 'category', 'dbfield' => 'assetcategoryname',  'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'type', 'title' => 'type', 'dbfield' => 'carseriesname', 'default' => true, 'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'serialNumber', 'title' => 'serialNumber', 'dbfield' => 'serialnumber', 'default' => true, 'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'chassisNumber', 'title' => 'chassisNumber', 'dbfield' => 'chassisnumber',  'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'year', 'title' => 'year', 'dbfield' => 'year', 'align' => 'center', 'width' => 80));

      array_push($this->arrDataListAvailableColumn, array('code' => 'capacity', 'title' => 'capacity', 'dbfield' => 'capacity', 'align' => 'right', 'format' => 'number', 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'qoh', 'title' => 'qoh', 'dbfield' => 'qoh', 'align' => 'right', 'format' => 'number', 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'qor', 'title' => 'qor', 'dbfield' => 'qor', 'align' => 'right', 'format' => 'number', 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'onrental', 'title' => 'onRental', 'dbfield' => 'onrental', 'align' => 'right', 'format' => 'number', 'width' => 70));
      array_push($this->arrDataListAvailableColumn, array('code' => 'mast', 'title' => 'mast', 'dbfield' => 'mast', 'align' => 'right', 'format' => 'number', 'width' => 100));
      
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
      array_push($this->arrSearchColumn, array('Serial Number', $this->tableName . '.serialnumber'));
      array_push($this->arrSearchColumn, array('Nomor Rangka', $this->tableName . '.chassisnumber'));
      array_push($this->arrSearchColumn, array('Tipe', $this->tableCarSeries . '.name'));
      array_push($this->arrSearchColumn, array('Merk', $this->tableBrand . '.name'));
      array_push($this->arrSearchColumn, array('Kategori', $this->tableCategoryAssetItem . '.name'));
      array_push($this->arrSearchColumn, array('Tahun', $this->tableName . '.year'));
      array_push($this->arrSearchColumn, array('Kapasitas', $this->tableName . '.capacity'));
      array_push($this->arrSearchColumn, array('Mast', $this->tableName . '.mast'));

      $this->printMenu = array();
      array_push($this->printMenu, array('code' => 'print1DBrcode', 'name' => $this->lang['print'] . ' ' . $this->lang['linearBarcode'], 'icon' => 'print', 'url' => 'print/assetBarcode'));

      $this->importUrl = 'import/assetItem';

      $this->includeClassDependencies(array(
         'AssetItemCategory.class.php',
         'AssetGroup.class.php',
         'SalesOrderItemAsset.class.php',
         'ItemUnit.class.php',
         'PurchaseOrder.class.php',
         'CarSeries.class.php',
         'Brand.class.php',
         'Warehouse.class.php',
         'AssetDepreciation.class.php',
         'GeneralJournal.class.php',
         'CategoryAssetItem.class.php',
         'SalesOrderRentalItemAsset.class.php',
         'SalesOrder.class.php',
         'AssetItemCOGSAdjustment.class.php',
         'ReplacementRentalItemAsset.class.php'
      ));

      $this->overwriteConfig();
   }

   function getQuery()
   {

      $sql = '
                 select
                     ' . $this->tableName . '.*, 
                     ' . $this->tableStatus . '.status as statusname,
                     ' . $this->tableBrand . '.name as brandname,
					 ' . $this->tableWarehouse . '.name as warehousename,
                '. $this->tableItemUnit .'.name as capacityname,
                '. $this->tableCarSeries .'.name as carseriesname,
                '. $this->tableCategoryAssetItem .'.name as assetcategoryname,
                concat('. $this->tableName .'.code, \' - \', '. $this->tableName .'.name) as assetitemcodename
                 from
                     ' . $this->tableName . '
					 		left join ' . $this->tableBrand . ' on ' . $this->tableName . '.brandkey = ' . $this->tableBrand . '.pkey
                     left join ' . $this->tableItemUnit . ' on '. $this->tableName .'.capacitykey = '. $this->tableItemUnit .'.pkey
                     left join ' . $this->tableCategoryAssetItem . ' on '. $this->tableName .'.assetcategorykey = '. $this->tableCategoryAssetItem .'.pkey
                     left join ' . $this->tableCarSeries . ' on '. $this->tableName .'.typekey = '. $this->tableCarSeries .'.pkey,
					 ' . $this->tableStatus . ',
					 ' . $this->tableWarehouse . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                     ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey';

      $sql .= $this->criteria;

      return $sql;
   }



   function validateForm($arr, $pkey = '')
   {
      $arrayToJs = parent::validateForm($arr, $pkey);
      if (empty($arr['code']))
         $this->addErrorList($arrayToJs, false, $this->errorMsg['code'][1]);

      // $rs = $this->isValueExisted($pkey, 'name', $arr['name']);
      // if (empty($arr['name'])) {
      //    $this->addErrorList($arrayToJs, false, $this->errorMsg['name'][1]);
      // } else if(count($rs) <> 0) {
      //    $this->addErrorList($arrayToJs, false, $this->errorMsg['name'][2]);
      // }

      // if (empty($arr['hidTypeKey']))
      //    $this->addErrorList($arrayToJs, false, $this->errorMsg['type'][1]);

      // if (empty($arr['serialNumber']))
      //    $this->addErrorList($arrayToJs, false, $this->errorMsg['serialNumber'][1]);

      if (empty($arr['hidBrandKey']))
         $this->addErrorList($arrayToJs, false, $this->errorMsg['brand'][1]);

      $vadidateDuplicate = $this->validateDuplicate($arr['name'], $arr['hidBrandKey'], $arr['hidTypeKey'], $arr['serialNumber'], $arr['hidCategoryKey']);
      // $this->addErrorList($arrayToJs, false, $this->errorMsg['bookValue'][2]);
      
      // kalau add baru
      if (empty($arr['hidId'])) { 
         if (!empty($vadidateDuplicate))
            $this->addErrorList($arrayToJs, false, $this->errorMsg['assetItem'][2]);
      } else {
         $newValue = $arr['name'].'-'.$arr['serialNumber'].'-'.$arr['hidTypeKey'].'-'.$arr['hidBrandKey'].'-'.$arr['hidCategoryKey'];
         if ($newValue != $arr['hidCurrrentValue'] && !empty($vadidateDuplicate)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['assetItem'][2]);
         } 
      }

      // 
//		if ($arr['bookValue'] <= 0)
//			$this->addErrorList($arrayToJs, false, $this->errorMsg['bookValue'][2]);
//
//		if ($arr['residue'] < 0)
//			$this->addErrorList($arrayToJs, false, $this->errorMsg['residue'][2]);
//
//		if ($arr['acquisitionValue'] < 0)
//			$this->addErrorList($arrayToJs, false, $this->errorMsg['residue'][2]);

      return $arrayToJs;
   }

   function validateDuplicate($name, $brandkey, $type, $serialnumber, $category)
   {
      $sql = 'select 
      '.$this->tableName.'.code
     from 
        '.$this->tableName.'
     where   
         '.$this->tableName.'.name = ' . $this->oDbCon->paramString($name) .' and
         '.$this->tableName.'.brandkey = ' . $this->oDbCon->paramString($brandkey) .' and
         '.$this->tableName.'.typekey = ' . $this->oDbCon->paramString($type) .' and
         '.$this->tableName.'.assetcategorykey = ' . $this->oDbCon->paramString($category) .' and
         '.$this->tableName.'.serialnumber = ' . $this->oDbCon->paramString($serialnumber) ;

         $rs = $this->oDbCon->doQuery($sql);


      return $rs;
   }

   function validateDelete($id, $forceDelete = false)
   {

      $arrayToJs = array();

      $rsData = $this->getDataRowById($id);

      if (!empty($rsData[0]['refpurchasekey'] && !$forceDelete)) {
         $this->addErrorList($arrayToJs,false, '<strong>' . $rsData[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg[210]);
      }

      return $arrayToJs;
   }

   function generateDefaultQueryForAutoComplete($returnField)
   {
      // code dipish, karena search asset bisa kombinasi

       $sql = 'select
                ' . $returnField['key'] . ',
                ' . $returnField['value'] . ' as value, 
				' . $this->tableName . '.code,
				' . $this->tableName . '.name,
				' . $this->tableName . '.explicensedate,
				' . $this->tableName . '.serialnumber,
				' . $this->tableName . '.mast,
				' . $this->tableName . '.qoh,
				' . $this->tableName . '.chassisnumber,
				' . $this->tableName . '.year,
				' . $this->tableName . '.depreciationvalue,
				' . $this->tableBrand . '.name as brandname,
				' . $this->tableCarSeries . '.name as carseriesname
            from 
               ' . $this->tableName . '  
                  left join ' . $this->tableBrand . ' on ' . $this->tableName . '.brandkey = ' . $this->tableBrand . '.pkey
                  left join ' . $this->tableCarSeries . ' on '. $this->tableName .'.typekey = '. $this->tableCarSeries .'.pkey
            where  		
               1=1
        ';

      return $sql;
   }

   function calculateDepreciationValue($acquisitionValue, $lifetime)
   {

      if ($lifetime == 0)
         return 0;

      $lifetime *= 12;

      // straightline
      return $acquisitionValue / $lifetime;

   }

function updateQty($pkey){  
         
        $arrayToJs = array();
        $salesOrderItemAsset = new SalesOrderItemAsset();
        $rsHeader = $this->getDataRowById($pkey);
        
         
         $sql = ' select 
                                ' . $salesOrderItemAsset->tableName . '.code,
                                ' . $salesOrderItemAsset->tableName . '.trdate,
                                ' . $salesOrderItemAsset->tableNameDetail . '.assetitemkey
                            from 
                                ' . $salesOrderItemAsset->tableName . ',
                                ' . $salesOrderItemAsset->tableNameDetail . '
                            where 
                                ' . $salesOrderItemAsset->tableName . '.statuskey in (2,3) and
                                ' . $salesOrderItemAsset->tableName . '.pkey = ' . $salesOrderItemAsset->tableNameDetail . '.refkey and
                                assetitemkey = ' . $this->oDbCon->paramString($pkey);
    
          $rsSO = $this->oDbCon->doQuery($sql);

      
        if(!empty($rsSO)){
            $totalQty = 0;
        }else{
            $totalQty = 1;
        }
                
        $sql = 'update ' . $this->tableName.' set  qoh = '.$this->oDbCon->paramString($totalQty).'  where  pkey = '.$this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);

       
        
        return $arrayToJs;
           
    }

    function updateQORItem($pkey, $moduleType) {
      $arrayToJs = array();

      $rsHeader = $this->getDataRowById($pkey);
      $salesOrderItemAsset = new SalesOrderItemAsset();
      $salesOrderRentalItemAsset = new SalesOrderRentalItemAsset();
      $salesOrder = new SalesOrder();

      switch ($moduleType) {
         case 1:
            //module type 1
            //untuk module SalesOrderItemAsset

            $sql = ' select 
                     ' . $salesOrderItemAsset->tableName . '.code,
                     ' . $salesOrderItemAsset->tableName . '.trdate,
                     ' . $salesOrderItemAsset->tableNameDetail . '.assetitemkey,
                     ' . $salesOrderItemAsset->tableNameDetail . '.qty
                  from 
                     ' . $salesOrderItemAsset->tableName . ',
                     ' . $salesOrderItemAsset->tableNameDetail . '
                  where 
                     ' . $salesOrderItemAsset->tableName . '.statuskey = ('. TRANSACTION_STATUS['konfirmasi'] .') and
                     ' . $salesOrderItemAsset->tableName . '.pkey = ' . $salesOrderItemAsset->tableNameDetail . '.refkey and
                     assetitemkey = ' . $this->oDbCon->paramString($pkey);

            $rsSO = $this->oDbCon->doQuery($sql);

            if (!empty($rsSO)) {
               $totalQty = $rsSO[0]['qty'];
            }

            $sql = 'update ' . $this->tableName . ' set  qor = ' . $this->oDbCon->paramString($totalQty) . '  where  pkey = ' . $this->oDbCon->paramString($pkey);
            $this->oDbCon->execute($sql);

            break;
         case 2:
            //module type 2
            //untuk module SalesOderRentalItemAsset
            //cek qty - delivered  > 0, karena kemungkinan ada SO yang masih kkonfirmasi (belum di spk semua) tetapi unit ini sudah di kirim, dan selesai
               $sql = ' select 
                                 ' . $salesOrderRentalItemAsset->tableName . '.code,
                                 ' . $salesOrderRentalItemAsset->tableName . '.trdate,
                                 ' . $salesOrderRentalItemAsset->tableNameDetail . '.assetitemkey,
                                 ' . $salesOrderRentalItemAsset->tableNameDetail . '.deliveredqty,
                                 ' . $salesOrderRentalItemAsset->tableNameDetail . '.qty
                              from 
                                 ' . $salesOrderRentalItemAsset->tableName . ',
                                 ' . $salesOrderRentalItemAsset->tableNameDetail . '
                              where 
                                 ' . $salesOrderRentalItemAsset->tableName . '.statuskey = (' . TRANSACTION_STATUS['konfirmasi'] . ') and
                                 ' . $salesOrderRentalItemAsset->tableName . '.pkey = ' . $salesOrderRentalItemAsset->tableNameDetail . '.refkey and
                                 (qty - deliveredqty) > 0 and
                                 assetitemkey = ' . $this->oDbCon->paramString($pkey);

               $rsSO = $this->oDbCon->doQuery($sql);


               if (!empty($rsSO)) {
                  $totalQty = $rsSO[0]['qty'];
               }

               $sql = 'update ' . $this->tableName . ' set  qor = ' . $this->oDbCon->paramString($totalQty) . '  where  pkey = ' . $this->oDbCon->paramString($pkey);
               $this->oDbCon->execute($sql);

            break;
         case 3:
            //module type 3
            //untuk module Sales Order, yang salesordetype 2
               $sql = ' select 
                     ' . $salesOrder->tableName . '.code,
                     ' . $salesOrder->tableName . '.trdate,
                     ' . $salesOrder->tableNameDetail . '.itemkey,
                     ' . $salesOrder->tableNameDetail . '.qty
                  from 
                     ' . $salesOrder->tableName . ',
                     ' . $salesOrder->tableNameDetail . '
                  where 
                     ' . $salesOrder->tableName . '.statuskey = ('. TRANSACTION_STATUS['konfirmasi'] .') and
                     ' . $salesOrder->tableName . '.pkey = ' . $salesOrder->tableNameDetail . '.refkey and
                     itemkey = ' . $this->oDbCon->paramString($pkey);

            $rsSO = $this->oDbCon->doQuery($sql);

            if (!empty($rsSO)) {
               $totalQty = $rsSO[0]['qty'];
            }

            $sql = 'update ' . $this->tableName . ' set  qor = ' . $this->oDbCon->paramString($totalQty) . '  where  pkey = ' . $this->oDbCon->paramString($pkey);
            $this->oDbCon->execute($sql);
            break;

         default:
            return false;
      }

      return $arrayToJs;
   }

   function updateQOHItem($pkey, $refkey = '', $moduleType) {

      $salesOrderItemAsset = new SalesOrderItemAsset();
      $salesOrderRentalItemAsset = new SalesOrderRentalItemAsset();
      $workOrderRentalItemAsset = new WorkOrderRentalItemAsset();
      $salesOrder = new SalesOrder();      

      switch($moduleType) {
         case 1: 
         //module type 1
         //untuk module SalesOrderItemAsset
            $sql = 'select 
                     coalesce(sum(qty-deliveredqty),0) as newqty
                  from 
                     ' . $salesOrderItemAsset->tableNameDetail . ',
                     ' . $salesOrderItemAsset->tableName . ' 
                  where 
                     ' . $salesOrderItemAsset->tableNameDetail . '.refkey = ' . $salesOrderItemAsset->tableName . '.pkey and 
                     ' . $salesOrderItemAsset->tableNameDetail . '.assetitemkey = ' . $salesOrderItemAsset->oDbCon->paramString($pkey) . ' and 
                     ' . $salesOrderItemAsset->tableName . '.statuskey in ('. TRANSACTION_STATUS['konfirmasi'] .')';

            $result = $this->oDbCon->doQuery($sql);
            $qty = $result[0]['newqty'];

            // sementara update QOH berdasarkan QOR
            $sql = '
               update
                  ' . $this->tableName . '
               set
                  qoh = ' . $this->oDbCon->paramString($qty) . ',
                  qor = ' . $this->oDbCon->paramString($qty) . '
               where
                  pkey = ' . $this->oDbCon->paramString($pkey) . '
               ';

            $this->oDbCon->execute($sql);

            break;
         case 2:
         //module type 2
         //untuk module SalesOderRentalItemAsset

            $sql = 'select 
                  qty,
                  deliveredqty,
                  statuskey,
                  coalesce(sum(qty-deliveredqty),0) as newqty,
                  '. $salesOrderRentalItemAsset->tableName .'.pkey as soheaderkey
               from 
                  ' . $salesOrderRentalItemAsset->tableNameDetail . ',
                  ' . $salesOrderRentalItemAsset->tableName . ' 
               where 
                  ' . $salesOrderRentalItemAsset->tableNameDetail . '.refkey = ' . $salesOrderRentalItemAsset->tableName . '.pkey and 
                  ' . $salesOrderRentalItemAsset->tableNameDetail . '.assetitemkey = ' . $salesOrderRentalItemAsset->oDbCon->paramString($pkey) . ' and 
                  ' . $salesOrderRentalItemAsset->tableName . '.statuskey in ('. TRANSACTION_STATUS['konfirmasi'] .','. TRANSACTION_STATUS['selesai'] .')';

            $result = $this->oDbCon->doQuery($sql);
            $qty = $result[0]['newqty'];
            $delivered = $result[0]['deliveredqty'];
            $status = $result[0]['statuskey'];

            //ambil data wo  
            $rsWO = $workOrderRentalItemAsset->searchDataRow(array(
                           $workOrderRentalItemAsset->tableName.'.pkey',
                           $workOrderRentalItemAsset->tableName.'.salesorderkey',
                           $workOrderRentalItemAsset->tableName.'.statuskey'
                        ), ' and ' . $workOrderRentalItemAsset->tableName.'.salesorderkey = ' . $this->oDbCon->paramString($result[0]['soheaderkey']) . ' 
                           and ' . $workOrderRentalItemAsset->tableName.'.pkey = ' . $this->oDbCon->paramString($refkey) .' ');

            $rsWODetail = $workOrderRentalItemAsset->getDetailWithRelatedInformation($refkey, ' and ' . $workOrderRentalItemAsset->tableNameDetail . '.itemkey = ' . $this->oDbCon->paramString($pkey));

            $onrental = 0;
            //cek jika status wo tidak selesai set onrental 1
            //kalau selesai set 0 onrental
            if (($status == TRANSACTION_STATUS['konfirmasi'] || $status == TRANSACTION_STATUS['selesai']) && $rsWODetail[0]['statuskey'] == 1) {
               $onrental = $delivered;
            } 
   
            $sql = '
                  update
                     ' . $this->tableName . '
                  set
                     qor = ' . $this->oDbCon->paramString($qty) . ',
                     onrental = ' . $this->oDbCon->paramString($onrental) . '
                  where
                     pkey = ' . $this->oDbCon->paramString($pkey) . '
                  ';

            $this->oDbCon->execute($sql);

            break;
         case 3:
         //module type 3
         //untuk module SalesDeliver unit

            $sql = 'select 
                     coalesce(sum(qty-deliveredqtyinbaseunit),0) as newqty
                  from 
                     ' . $salesOrder->tableNameDetail . ',
                     ' . $salesOrder->tableName . ' 
                  where 
                     ' . $salesOrder->tableNameDetail . '.refkey = ' . $salesOrder->tableName . '.pkey and 
                     ' . $salesOrder->tableNameDetail . '.itemkey = ' . $salesOrder->oDbCon->paramString($pkey) . ' and 
                     ' . $salesOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['konfirmasi'] . ')';

            $result = $this->oDbCon->doQuery($sql);
            $qty = $result[0]['newqty'];

            // sementara update QOH berdasarkan QOR
            $sql = '
               update
                  ' . $this->tableName . '
               set
                  qoh = ' . $this->oDbCon->paramString($qty) . ',
                  qor = ' . $this->oDbCon->paramString($qty) . '
               where
                  pkey = ' . $this->oDbCon->paramString($pkey) . '
               ';

            $this->oDbCon->execute($sql);

            break;

         default:
         return false;
      }

   }


   function updateOnRentalUnit($pkey, $onrental) {
      $sql = '
            update
               ' . $this->tableName . '
            set
               onrental = ' . $this->oDbCon->paramString($onrental) . '
            where
               pkey = ' . $this->oDbCon->paramString($pkey) . '
            ';

      $this->oDbCon->execute($sql);
   }


   // function updateQOR($pkey) {
   //    $arrayToJs = array();
   //    $salesOrderItemAsset = new SalesOrderItemAsset();
   //    $rsHeader = $this->getDataRowById($pkey);

   //    $sql = ' select 
   //                              ' . $salesOrderItemAsset->tableName . '.code,
   //                              ' . $salesOrderItemAsset->tableName . '.trdate,
   //                              ' . $salesOrderItemAsset->tableNameDetail . '.assetitemkey,
   //                              ' . $salesOrderItemAsset->tableNameDetail . '.qty
   //                          from 
   //                              ' . $salesOrderItemAsset->tableName . ',
   //                              ' . $salesOrderItemAsset->tableNameDetail . '
   //                          where 
   //                              ' . $salesOrderItemAsset->tableName . '.statuskey in (2,3) and
   //                              ' . $salesOrderItemAsset->tableName . '.pkey = ' . $salesOrderItemAsset->tableNameDetail . '.refkey and
   //                              assetitemkey = ' . $this->oDbCon->paramString($pkey);

   //    $rsSO = $this->oDbCon->doQuery($sql);

   
   //    if (!empty($rsSO)) {
   //       $totalQty = $rsSO[0]['qty'];
   //    } 

   //    $sql = 'update ' . $this->tableName . ' set  qor = ' . $this->oDbCon->paramString($totalQty) . '  where  pkey = ' . $this->oDbCon->paramString($pkey);
   //    $this->oDbCon->execute($sql);



   //    return $arrayToJs;
   // }

   // function updateQOH($itemkey) {
   //    $salesOrderItemAsset = new SalesOrderItemAsset();

   //    $sql = 'select 
   //                  coalesce(sum(qty-deliveredqty),0) as newqty
   //              from 
   //                  ' . $salesOrderItemAsset->tableNameDetail . ',
   //                  ' . $salesOrderItemAsset->tableName . ' 
   //              where 
   //                  ' . $salesOrderItemAsset->tableNameDetail . '.refkey = ' . $salesOrderItemAsset->tableName . '.pkey and 
   //                  ' . $salesOrderItemAsset->tableNameDetail . '.assetitemkey = ' . $salesOrderItemAsset->oDbCon->paramString($itemkey) . ' and 
   //                  ' . $salesOrderItemAsset->tableName . '.statuskey in (2,3)';

   //       $result = $this->oDbCon->doQuery($sql);
   //       $qty = $result[0]['newqty'];

   //       // sementara update QOH berdasarkan QOR
   //       $sql = '
   //          update
   //             ' . $this->tableName . '
   //          set
   //             qoh = ' . $this->oDbCon->paramString($qty) . ',
   //             qor = ' . $this->oDbCon->paramString($qty) . '
   //          where
   //             pkey = ' . $this->oDbCon->paramString($itemkey) . '
   //          ';

   //       $r = $this->oDbCon->execute($sql);
   // }

   // function updateQORRental($pkey)
   // {
   //    $arrayToJs = array();
   //    $salesOrderRentalItemAsset = new SalesOrderRentalItemAsset();
   //    $rsHeader = $this->getDataRowById($pkey);

   //    $sql = ' select 
   //                              ' . $salesOrderRentalItemAsset->tableName . '.code,
   //                              ' . $salesOrderRentalItemAsset->tableName . '.trdate,
   //                              ' . $salesOrderRentalItemAsset->tableNameDetail . '.assetitemkey,
   //                              ' . $salesOrderRentalItemAsset->tableNameDetail . '.qty
   //                          from 
   //                              ' . $salesOrderRentalItemAsset->tableName . ',
   //                              ' . $salesOrderRentalItemAsset->tableNameDetail . '
   //                          where 
   //                              ' . $salesOrderRentalItemAsset->tableName . '.statuskey in (2,3) and
   //                              ' . $salesOrderRentalItemAsset->tableName . '.pkey = ' . $salesOrderRentalItemAsset->tableNameDetail . '.refkey and
   //                              assetitemkey = ' . $this->oDbCon->paramString($pkey);

   //    $rsSO = $this->oDbCon->doQuery($sql);


   //    if (!empty($rsSO)) {
   //       $totalQty = $rsSO[0]['qty'];
   //    }

   //    $sql = 'update ' . $this->tableName . ' set  qor = ' . $this->oDbCon->paramString($totalQty) . '  where  pkey = ' . $this->oDbCon->paramString($pkey);
   //    $this->oDbCon->execute($sql);



   //    return $arrayToJs;
   // }

   // function updateQOHRental($itemkey)
   // {
   //    $salesOrderRentalItemAsset = new SalesOrderRentalItemAsset();

   //    $sql = 'select 
   //                  coalesce(sum(qty-deliveredqty),0) as newqty
   //              from 
   //                  ' . $salesOrderRentalItemAsset->tableNameDetail . ',
   //                  ' . $salesOrderRentalItemAsset->tableName . ' 
   //              where 
   //                  ' . $salesOrderRentalItemAsset->tableNameDetail . '.refkey = ' . $salesOrderRentalItemAsset->tableName . '.pkey and 
   //                  ' . $salesOrderRentalItemAsset->tableNameDetail . '.assetitemkey = ' . $salesOrderRentalItemAsset->oDbCon->paramString($itemkey) . ' and 
   //                  ' . $salesOrderRentalItemAsset->tableName . '.statuskey in (2,3)';

   //    $result = $this->oDbCon->doQuery($sql);
   //    $qty = $result[0]['newqty'];

   //    // sementara update QOH berdasarkan QOR
   //    $sql = '
   //          update
   //             ' . $this->tableName . '
   //          set
   //             qoh = ' . $this->oDbCon->paramString($qty) . ',
   //             qor = ' . $this->oDbCon->paramString($qty) . '
   //          where
   //             pkey = ' . $this->oDbCon->paramString($itemkey) . '
   //          ';

   //    $r = $this->oDbCon->execute($sql);
   // }
   
   function afterUpdateData($arrParam, $action)
   {
      // kalo dr purchsae, return aj

      $generalJournal = new GeneralJournal();

      $rs = $this->getDataRowById($arrParam['pkey']);
      $oldRs = $arrParam['oldRs'];

      if (!empty($rs[0]['refpurchasekey']))
         return; // kalo dr purchase, return
      if ($rs[0]['totaldepreciatedctr'] > 0)
         return; // klo sdh d depresisi, return
      if ($rs[0]['initdepreciationvalue'] > 0)
         return; // klo sdh ad nila init, return

      $arr1 = array();
      array_push($arr1, $rs[0]['warehousekey']);
      array_push($arr1, $rs[0]['acquisitionvalue']);
      $arr1 = md5(json_encode($arr1));

      $arr2 = array();
      if (!empty($oldRs)) {
         array_push($arr2, $oldRs[0]['warehousekey']);
         array_push($arr2, $oldRs[0]['acquisitionvalue']);
      }
      $arr2 = md5(json_encode($arr2));

      $same = ($arr1 == $arr2) ? true : false;

      // kalo blm ad jurnal, add
      if (empty($oldRs)) {
        //$this->updateGL($rs);
      } else {
         if (!$same) {
            //kalo ud ad cek perlu add ulang atau tidak
            $this->cancelGLByRefkey($arrParam['pkey'], $this->tableName);
            $this->updateGL($rs);
         }
      }
   }


   function updateGL($rs)
   {

      if (!USE_GL)
         return;
      if ($rs[0]['overwriteGL'] == 1)
         return;

      $coaLink = new COALink();
      $warehouse = new Warehouse();
      $generalJournal = new GeneralJournal();

      $warehousekey = $rs[0]['warehousekey'];

      $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
      $arr = array();
      $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
      $arr['code'] = 'xxxxx';
      $arr['refkey'] = $rs[0]['pkey'];
      $arr['refTableType'] = $rsKey['key'];
      $arr['trDate'] = $this->formatDBDate($rs[0]['acquisitiondate'], 'd / m / Y');
      $arr['refCode'] = $rs[0]['code'];
      $arr['selWarehouseKey'] = $rs[0]['warehousekey'];

      $temp = -1;

      $temp++;
      // debit 
      $rsCOA = $this->getAssetCOAKey($rs[0]['pkey'], $warehousekey);
      $arr['hidCOAKey'][$temp] = $rsCOA[0]['coaassetkey'];
      $arr['debit'][$temp] = $rs[0]['acquisitionvalue'];
      $arr['credit'][$temp] = 0;

      $temp++;
      // credit     
      $retainedearnings = $coaLink->getCOALink('retainedearnings');
      $arr['hidCOAKey'][$temp] = $retainedearnings[0]['coakey'];
      $arr['debit'][$temp] = 0;
      $arr['credit'][$temp] = $rs[0]['acquisitionvalue'];

      $arrayToJs = $generalJournal->addData($arr);

      if (!$arrayToJs[0]['valid'])
         throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);

   }

   function normalizeParameter($arrParam, $trim = false)
   {

      $arrParam = parent::normalizeParameter($arrParam, true);
      return $arrParam;
   }

   function getAvailableUnit($searchCriteria = '', $orderCriteria = '', $limit = '') {
      $sql = '
         select
            '. $this->tableName .'.*,
            '. $this->tableBrand .'.name as brandname,
            '. $this->tableCarSeries .'.name as typename,
            '. $this->tableCategoryAssetItem .'.name as categoryname,
            concat('. $this->tableName .'.serialnumber, \' - \', '. $this->tableName .'.name, \' - \', '. $this->tableBrand .'.name,\' - \', '. $this->tableCarSeries .'.name,\' - \', '. $this->tableCategoryAssetItem .'.name) as value
            from 
               '. $this->tableName .'
               left join ' . $this->tableCarSeries . ' on ' . $this->tableName . '.typekey = ' . $this->tableCarSeries . '.pkey
               left join ' . $this->tableCategoryAssetItem . ' on ' . $this->tableName . '.assetcategorykey = ' . $this->tableCategoryAssetItem . '.pkey,
               '. $this->tableBrand .'
            where
            '.$this->tableName.'.brandkey = '.$this->tableBrand.'.pkey  

      ';


        if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria;
	 
	 	}    
			
		if($limit <> '')
			$sql .= ' ' .$limit;

         $this->setLog($sql, true);

        $result = $this->oDbCon->doQuery($sql);
		return $result;

   }

   function availableItemUnit($pkey = '') {
      
      // sum(qoh - qor - onrental) as outstanding
      // outstanding diganti jadi qoh dlu
      $sql = '
         select
            '. $this->tableName .'.*,
            '. $this->tableName .'.qoh as outstanding
         from
            '. $this->tableName .'
         where 
            '. $this->tableName .'.statuskey = 1
      ';

      if(!empty($pkey)) {
         $sql .= ' and ' . $this->tableName .'.pkey =  '. $this->oDbCon->paramString($pkey);
      }

      $rs = $this->oDbCon->doQuery($sql);

      return $rs;
   }


   function updateWarehouseAssetItem($pkey, $warehousekey) {
      $sql = '
         update
            '. $this->tableName .'
         set 
            warehousekey = '. $this->oDbCon->paramString($warehousekey) .'
         where
            pkey = '. $this->oDbCon->paramString($pkey) .'
      ';

      $this->oDbCon->execute($sql);
   }

   function updateAssetItem($pkey) 
   {
      $assetItemMovement = new AssetItemMovement();

      $cogsValue =  $assetItemMovement->getCOGSAssetItem($pkey);

      $sql = '
         UPDATE
            ' . $this->tableName . '
         SET
            bookvalue = ' . $cogsValue . '
         WHERE
            pkey = ' . $this->oDbCon->paramString($pkey) . '
      ';

      $this->oDbCon->execute($sql);
   }

}
