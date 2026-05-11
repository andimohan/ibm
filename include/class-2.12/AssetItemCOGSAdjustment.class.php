<?php

class AssetItemCOGSAdjustment extends BaseClass
{

   function __construct()
   {

      parent::__construct();
      $this->tableName = 'asset_item_cogs_adjustment_header';
      $this->tableNameDetail = 'asset_item_cogs_adjustment_detail';
      $this->tableStatus = 'transaction_status';
      $this->tableWarehouse = 'warehouse';
      $this->tableAssetItem = 'asset_item';
      $this->tableCOA = 'chart_of_account';
      $this->tableBrand = 'brand';
      $this->tableCategoryAssetItem = 'category_asset_item';
      $this->tableCarSeries = 'car_series';
      $this->tableItem = 'item';
      $this->tableItemUnit = 'item_unit';

      $this->isTransaction = true;
      $this->newLoad = true;
      $this->securityObject = 'AssetItemCOGSAdjustment';

      $this->arrDataDetail = array();
      $this->arrDataDetail['pkey'] = array('hidDetailKey');
      $this->arrDataDetail['refkey'] = array('pkey', 'ref');
      $this->arrDataDetail['description'] = array('descriptionDetail');
      $this->arrDataDetail['adjustmentvalue'] = array('adjustmentValue', 'number');
      $this->arrDataDetail['coakey'] = array('hidCOAKey');
      $this->arrDataDetail['qtyinbaseunit'] = array('qty', 'number');
      $this->arrDataDetail['costinbaseunit'] = array('COGS', 'number');
      $this->arrDataDetail['itemkey'] = array('hidItemDetailKey');
      $this->arrDataDetail['unitkey'] = array('baseUnitKey');
      $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier', 'number');

      $arrDetails = array();
      array_push($arrDetails, array('dataset' => $this->arrDataDetail));

      $this->arrData = array();
		$this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
		$this->arrData['code'] = array('code');
		$this->arrData['trdate'] = array('trDate', 'date');
		$this->arrData['warehousekey'] = array('selWarehouse');
		$this->arrData['assetitemkey'] = array('hidAssetItemKey');
		$this->arrData['currentcogs'] = array('currentCOGS', 'number');
		$this->arrData['totaladjustment'] = array('totalAdjustment', 'number');
		$this->arrData['newcogsvalue'] = array('newCOGSValue', 'number');
		$this->arrData['trdesc'] = array('trDesc');
		$this->arrData['statuskey'] = array('selStatus');

      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'format' => 'date', 'align' => 'center', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'assetitemname', 'default' => true, 'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'currentOfCOGS', 'title' => 'currentOfCOGS', 'dbfield' => 'currentcogs', 'default' => true, 'format' => 'number', 'align' => 'right',  'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'totalAdjustment', 'title' => 'totalAdjustment', 'dbfield' => 'totaladjustment', 'default' => true, 'format' => 'number', 'align' => 'right',  'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'newCOGSValue', 'title' => 'newCOGSValue', 'dbfield' => 'newcogsvalue', 'default' => true, 'format' => 'number', 'align' => 'right',  'width' => 120));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 80));

      
      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Unit', $this->tableAssetItem . '.name'));
      array_push($this->arrSearchColumn, array('Unit', $this->tableAssetItem . '.serialnumber'));
      array_push($this->arrSearchColumn, array('Unit', $this->tableBrand . '.name'));
      array_push($this->arrSearchColumn, array('Unit', $this->tableCategoryAssetItem . '.name'));
      array_push($this->arrSearchColumn, array('Unit', $this->tableCarSeries . '.name'));
      array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
      array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));


      $this->includeClassDependencies(
         array(
            'AssetItem.class.php',
            'Warehouse.class.php',
            'AssetItemMovement.class.php',
            'Item.class.php',
            'AssetItemTurnover.class.php',
            'ItemIn.class.php'
         )
      );

      $this->overwriteConfig();
   
   }

   function getQuery() {
      $sql = '
               select
                  ' . $this->tableName . '.*, 
                  concat('. $this->tableAssetItem .'.serialnumber, \' - \', '. $this->tableAssetItem .'.name, \' - \', '. $this->tableBrand .'.name,\' - \', '. $this->tableCarSeries .'.name,\' - \', '. $this->tableCategoryAssetItem .'.name) as assetitemname,
                  ' . $this->tableWarehouse . '.name as warehousename, 
                  ' . $this->tableStatus . '.status as statusname 
               from 
                  ' . $this->tableName . '
                  left join '. $this->tableWarehouse .' on '. $this->tableName .'.warehousekey = '. $this->tableWarehouse .'.pkey,
                  ' . $this->tableAssetItem . '
					 		left join ' . $this->tableBrand . ' on ' . $this->tableAssetItem . '.brandkey = ' . $this->tableBrand . '.pkey
                     left join ' . $this->tableCarSeries . ' on '. $this->tableAssetItem .'.typekey = '. $this->tableCarSeries .'.pkey
					 		left join ' . $this->tableCategoryAssetItem . ' on ' . $this->tableAssetItem . '.assetcategorykey = ' . $this->tableCategoryAssetItem . '.pkey,
					   ' . $this->tableStatus . '
               where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                     ' . $this->tableName . '.assetitemkey = ' . $this->tableAssetItem . '.pkey
         ' . $this->criteria;
      return $sql;
   }

   function getDetailWithRelatedInformation($pkey,$criteria = ''){

      $sql = 'select
                  '.$this->tableNameDetail .'.*,
                  '.$this->tableCOA.'.code,
					   '.$this->tableCOA.'.name,
					   concat('.$this->tableCOA. '.code," - ",'.$this->tableCOA.'.name) as coa,
                  '. $this->tableItem .'.name as itemname,
                  '. $this->tableItem .'.code as itemcode,
                  '.$this->tableItemUnit.'.name as unitname,
                  baseunit.name as baseunitname
               from
                  '. $this->tableNameDetail .'
                  left join '. $this->tableItemUnit .' on '. $this->tableNameDetail .'.unitkey = '. $this->tableItemUnit .'.pkey
                  left join '. $this->tableItem .' on '. $this->tableNameDetail .'.itemkey = '. $this->tableItem .'.pkey
                  left join '. $this->tableItemUnit .' baseunit on ' . $this->tableItem . '.baseunitkey = baseunit.pkey

                  left join '. $this->tableCOA .' on '. $this->tableNameDetail .'.coakey = '. $this->tableCOA .'.pkey
               where
                  refkey in ('.$this->oDbCon->paramString($pkey,',').')
               ';
   
      return $this->oDbCon->doQuery($sql);

   }

   function validateForm($arr, $pkey = '')
	{
		$arrayToJs = parent::validateForm($arr, $pkey);

      $item = new Item();
      
      $assetItemKey = $arr['hidAssetItemKey'];

      $arrItemDetailKey = $arr['hidItemDetailKey'];

      $arrDescDetail = $arr['descriptionDetail'];
      $arrAdjustmentValue = $this->unFormatNumber($arr['adjustmentValue']);
      $arrCOGS = $this->unFormatNumber($arr['COGS']);
      $arrQty = $this->unFormatNumber($arr['qty']);

      if(empty($assetItemKey)) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['itemUnit'][1]);
      }

      if(empty($arrItemDetailKey[0])) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg[501]);
      } else {

         $rsItem = $item->searchData('','',true, ' and ' . $item->tableName.'.pkey in ('. $this->oDbCon->paramString($arrItemDetailKey,',') .') ');
         $rsItem = $this->reindexDetailCollections($rsItem, 'pkey');

         $arrItemKeys = array();
         for($i=0; $i < count($arrItemDetailKey); $i++) {
            
            if(empty($arrItemDetailKey[$i])) {
               $this->addErrorList($arrayToJs, false, $this->errorMsg['item'][1]);
            }
            
            $rsItemCol = $rsItem[$arrItemDetailKey[$i]];

            
            // if(empty($arrDescDetail[$i])) {
            //    $this->addErrorList($arrayToJs, false, $this->errorMsg[501]);
            // }

            if($arrQty[$i] <= 0) {
               $this->addErrorList($arrayToJs, false, '<strong>' . $rsItemCol[0]['name'] . '. </strong>' . $this->errorMsg['qty'][1]);
            }

            if($arrCOGS[$i] == 0) {
               $this->addErrorList($arrayToJs, false, '<strong>'. $rsItemCol[0]['name'] .'. '. $this->lang['cogs'] .' - </strong>' . $this->errorMsg[512]);
            }

            if($arrAdjustmentValue[$i] == 0) {
               $this->addErrorList($arrayToJs, false, '<strong>'. $rsItemCol[0]['name'] .'. </strong>' . $this->errorMsg[512]);
            }

            if (in_array($arrItemDetailKey[$i], $arrItemKeys)) {
               $this->addErrorList($arrayToJs, false, '<strong>' . $rsItemCol[0]['name'] . '. </strong> ' . $this->errorMsg[215]);
            } else {
               $arrItemKeys[] = $arrItemDetailKey[$i];
            }

         }
      }
      
		return $arrayToJs;
	}

   function validateConfirm($rsHeader)
   {
      $id = $rsHeader[0]['pkey'];

      $rsData = $this->getDataRowById($id);
      $rsDetail = $this->getDetailById($id);

      if(empty($rsData[0]['assetitemkey'])) {
         $this->addErrorLog(false, $this->errorMsg['itemUnit'][1]);
      }

      $assetItem = new AssetItem();
      $item = new Item();

      $rsAssetItem = $assetItem->getDataRowById($rsData[0]['assetitemkey']);

      if($rsAssetItem[0]['statuskey'] <> 1 ) {
         $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[201] . ' <br> <strong>' . $rsAssetItem[0]['code'] . ' - ' . $rsAssetItem[0]['name'] .'.</strong> ' . $this->errorMsg[206]);
      }

      $arrItemKey = array_column($rsDetail, 'itemkey');
   
      $rsItem = $item->searchData('','',true, ' and ' . $item->tableName.'.pkey in ('. $this->oDbCon->paramString($arrItemKey,',') .') ');
      $rsItem = $this->reindexDetailCollections($rsItem, 'pkey');

      $arrErrorMsg = array();

      if(empty($rsDetail[0]['itemkey'])) {
         $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'</strong>'. $this->errorMsg['item'][1]);
      }


      $arrItemKeys = [];
      
      for($i=0; $i < count($rsDetail); $i++) {

         $rsItemCol = $rsItem[$rsDetail[$i]['itemkey']];

         if($rsDetail[$i]['qtyinbaseunit'] <= 0) {
            array_push($arrErrorMsg, '<strong>' . $rsItemCol[0]['name'] . '. </strong>' . $this->errorMsg['qty'][1]);
         }
         
         if($rsDetail[$i]['costinbaseunit'] == 0) {
            array_push($arrErrorMsg, '<strong>' . $rsItemCol[0]['name'] . '. ' . $this->lang['cogs'] . ' - </strong>' . $this->errorMsg[512]);
         }

         if($rsDetail[$i]['adjustmentvalue'] == 0) {
            array_push($arrErrorMsg, '<strong>' . $rsItemCol[0]['name'] . '. </strong>' . $this->errorMsg[512]);
         }

         if (in_array($rsDetail[$i]['itemkey'], $arrItemKeys)) {
            array_push($arrErrorMsg, '<strong>' . $rsItemCol[0]['name'] . '. </strong> ' . $this->errorMsg[215]);
         } else {
            $arrItemKeys[] = $rsDetail[$i]['itemkey'];
         }

      }
   
      if (!empty($arrErrorMsg)) {
         $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'.</strong> '. $this->errorMsg[201] .' <br>' . implode('<br>', $arrErrorMsg));
      }

   }

   function confirmTrans($rsHeader)
   {
      $assetItemTurnover = new AssetItemTurnover();
      $id = $rsHeader[0]['pkey'];

      //update hpp value unit
      $this->updateCOGSAssetItem($rsHeader);
      $this->addItemIn($rsHeader);
      $this->updateGL($rsHeader);

      $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);

      $arrParam = array();
      $rsObjKey = $this->getTableKeyAndObj($this->tableName);   
      $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
      $arrParam['refCode'] = $rsHeader[0]['code'];
      $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
      $arrParam['joDate'] =   $arrParam['trDate'] ; // samakan 
      $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
      $arrParam['hidRefTable'] = $rsObjKey['key'];
      $arrParam['hidAssetItemKey'] = $rsHeader[0]['assetitemkey'];   
      $arrParam['amount'] = $rsHeader[0]['totaladjustment'];   
      $arrParam['selStatus'] = 1;

      $arrDesc = array();
      for ($i=0; $i<count($rsDetail); $i++) {
          $itemDesc =  $this->formatNumber($rsDetail[$i]['qtyinbaseunit']). "  ". $rsDetail[$i]['unitname'] ." ". $rsDetail[$i]['itemname'] ." @ Rp. ". $this->formatNumber($rsDetail[$i]['costinbaseunit']);
          array_push($arrDesc, $itemDesc);
      }
      
      $arrParam['trDesc'] = implode(chr(13),$arrDesc);
      // throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);   

      $arrayToJs = $assetItemTurnover->addData($arrParam); 
      if (!$arrayToJs[0]['valid'])
         throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    

   }

   function validateCancel($rsHeader, $autoChangeStatus = false)
   {
      $id = $rsHeader[0]['pkey'];

      $itemIn = new ItemIn();
      

      $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'));
      $tablekey = $tablekey['key'];

      $rsItemIn = $itemIn->searchDataRow(
         array($itemIn->tableName . '.pkey', $itemIn->tableName . '.code'),
         ' and ' . $itemIn->tableName . '.refkey = ' . $this->oDbCon->paramString($id) . ' 
         and ' . $itemIn->tableName . '.reftabletype = ' . $this->oDbCon->paramString($tablekey) . ' and ' . $itemIn->tableName . '.statuskey in (2,3) ');

      if(!empty($rsItemIn)) {
      
         for($j=0; $j<count($rsItemIn); $j++) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[210] .'<br> <strong>'. $rsItemIn[$j]['code'] .'. </strong>' .$this->errorMsg[203]);
         }

      }
      
   }

   function cancelTrans($rsHeader,$copy){  
      
      $rsObjKey = $this->getTableKeyAndObj($this->tableName);
      
      $id = $rsHeader[0]['pkey'];

      $this->cancelCOGSAssetItem($rsHeader);
      $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
      $this->cancelItemIn($rsHeader);

      $assetItemTurnover = new AssetItemTurnover();
      $assetItemTurnover->cancelMovement($id,$rsObjKey['key']);

      if ($copy) {
         $this->copyDataOnCancel($id);
      }

   }

   function updateCOGSAssetItem($rsHeader) 
   {
      $assetItem = new AssetItem();
      $assetItemMovement = new AssetItemMovement();

      $rsAssetItem = $assetItem->getDataRowById($rsHeader[0]['assetitemkey']);
      
      //update Unit Movement
      
      $movementNote = $rsHeader[0]['code'] . ', ' . $this->ucFirst($this->lang['cogsAdjustment'] . ' ' . $this->lang['assetItem']) . '  ' .$rsAssetItem[0]['code'] .' - '.   $rsAssetItem[0]['name'];
      $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'))['key'];
      
      $assetItemMovement->updateAssetItemMovement($rsHeader[0]['pkey'], $rsHeader[0]['trdate'], '', $rsHeader[0]['assetitemkey'], $rsHeader[0]['warehousekey'], 0, $tablekey, $movementNote, $rsHeader[0]['supplierkey'], $rsHeader[0]['totaladjustment']);
   
      $assetItem->updateAssetItem($rsHeader[0]['assetitemkey']);

   }

   function addItemIn($rsHeader)
   {
      $id = $rsHeader[0]['pkey'];
      $rsDetail = $this->getDetailById($id);

      $itemIn = new ItemIn();

      $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'));
      $tablekey = $tablekey['key'];

      if (!empty($rsDetail)) {

         $index = 0;

         $arrParam = array();
         $arrParam['hidDetailKey'] = array();
         $arrParam['hidItemKey'] = array();
         $arrParam['qty'] = array();
         $arrParam['selUnit'] = array();
         $arrParam['COGS'] = array();

         foreach ($rsDetail as $detail) {

            $arrParam['hidDetailKey'][$index] = 0;
            $arrParam['hidItemKey'][$index] = $detail['itemkey'];
            $arrParam['qty'][$index] = $detail['qtyinbaseunit'];
            $arrParam['COGS'][$index] = ($detail['costinbaseunit'] * -1);
            $arrParam['selUnit'][$index] = $detail['unitkey']; 

            $index++;
         
         }

         if (count($arrParam['hidDetailKey']) > 0) {

            $arrParam['code'] = 'xxxxxx';
            $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
            $arrParam['refCode'] = $rsHeader[0]['code'];
            $arrParam['refkey'] = $rsHeader[0]['pkey'];
            $arrParam['reftabletype'] = $tablekey;
            $arrParam['islinked'] = 1;
            $arrParam['trDesc'] = $rsHeader[0]['trdesc'];
            $arrParam['overwriteGL'] = 1;

            $arrayToJs = $itemIn->addData($arrParam);

            if (!$arrayToJs[0]['valid']) {
               $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message'], true);
            }

         }

      }
   }

   function cancelItemIn($rsHeader) 
   {
      $id = $rsHeader[0]['pkey'];
      $itemIn = new ItemIn();

      $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'));
      $tablekey = $tablekey['key'];

      $rsItemIn = $itemIn->searchDataRow(array($itemIn->tableName.'.pkey', $itemIn->tableName.'.code'), 
                                                ' and ' . $itemIn->tableName.'.refkey = '. $this->oDbCon->paramString($id) .' 
                                                and '. $itemIn->tableName .'.reftabletype = '. $this->oDbCon->paramString($tablekey) .' and '. $itemIn->tableName .'.statuskey = 1');
   
         $totalItem = count($rsItemIn);
         for($i=0;$i<$totalItem;$i++) { 
            $arrayToJs = $itemIn->changeStatus($rsItemIn[$i]['pkey'],4,'',false, true);  
            if(!$arrayToJs[0]['valid'])
               throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']); 
         }
   
   }

   function cancelCOGSAssetItem($rsHeader) 
   {
      $assetItemMovement = new AssetItemMovement();
      $assetItem = new AssetItem();

      
      $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'))['key'];
      $assetItemMovement->cancelAssetItemMovement($rsHeader[0]['pkey'], $tablekey);
      
      $assetItem->updateAssetItem($rsHeader[0]['assetitemkey']);
   }



   function getDetailCOGSAdjustmentByAssetItem($pkey) 
   {
      $sql = '
         select
            '. $this->tableNameDetail .'.*,
            '. $this->tableName .'.pkey as headerkey,
            '. $this->tableName .'.code,
            '. $this->tableName .'.trdate,
            '. $this->tableName .'.assetitemkey
         from 
            '. $this->tableNameDetail .'
            inner join '. $this->tableName .' on '. $this->tableNameDetail .'.refkey = '. $this->tableName .'.pkey
         where
            '. $this->tableName .'.assetitemkey in (' . $this->oDbCon->paramString($pkey, ',') . ') and
            '. $this->tableName .'.statuskey in (2,3)
      ';

      $rs = $this->oDbCon->doQuery($sql);
   
      return $rs;
   } 

   function reCountTotal($arrParam)
   {
      $assetItem = new AssetItem();

      $assetitemkey = $arrParam['hidAssetItemKey'];
      $rsAssetItem = $assetItem->getDataRowById($assetitemkey);

      $arrItemDetailKey = $arrParam['hidItemDetailKey'];
      $arrCOGS = $this->unFormatNumber($arrParam['COGS']);
      $arrQty = $this->unFormatNumber($arrParam['qty']);

      $arrAdjustmentValue = $this->unFormatNumber($arrParam['adjustmentValue']);
      $currentCOGS = $rsAssetItem[0]['bookvalue'];

      $totalAdjustment = 0;
      $newCOGSValue = 0;
      
      // for($i=0; $i < count($arrAdjustmentValue); $i++) {
      //    $totalAdjustment += $arrAdjustmentValue[$i];
      // }

      $arrItemDetail = array();

      for($i=0; $i < count($arrItemDetailKey); $i++) {
         
         if(empty($arrItemDetailKey[$i])) continue;

         //buat minus
         $COGS = $arrCOGS[$i];
         $COGS  = (($COGS > 0) ? $COGS * -1 : $COGS);

         //total adjustment value (qty * cogs) * -1
         $adjustmentValue = ($arrQty[$i] * $arrCOGS[$i]);
         $adjustmentValue = (($adjustmentValue > 0) ? $adjustmentValue * -1 : $adjustmentValue);

         $totalAdjustment += $adjustmentValue;
         $arrItemDetail[$i]['adjustmentValue'] = $adjustmentValue;
         
      }

      $newCOGSValue = $currentCOGS + $totalAdjustment;

      $reCountResult = array();
      $reCountResult['detail'] = $arrItemDetail;
      $reCountResult['totalAdjustment'] = $totalAdjustment;
      $reCountResult['newCOGSValue'] = $newCOGSValue;

      return $reCountResult;
   }

   function normalizeParameter($arrParam, $trim = false)
	{
      
      $assetItem = new AssetItem();
      $item = new Item();

      //overwrite currentHPP
      $assetItemKey = $arrParam['hidAssetItemKey'];
      $rsAssetItem = $assetItem->getDataRowById($assetItemKey);

      $arrItemDetailKey = $arrParam['hidItemDetailKey'];

      $arrParam['currentCOGS'] = $rsAssetItem[0]['bookvalue'];

      $reCountResult = $this->reCountTotal($arrParam);
      $arrParam['detail'] = $reCountResult['detail'];
      
      //overwrite total detail
      for($i=0; $i<count($arrItemDetailKey); $i++) {
         $arrParam['adjustmentValue'][$i] = $arrParam['detail'][$i]['adjustmentValue'];
         $rsItem = $item->getDataRowById($arrItemDetailKey[$i]); 
         $arrParam['baseUnitKey'][$i]  =  $rsItem[0]['baseunitkey']; 
         $arrParam['unitConvMultiplier'][$i] = 1; 
      }

      $arrParam['totalAdjustment'] = $reCountResult['totalAdjustment'];
      $arrParam['newCOGSValue'] = $reCountResult['newCOGSValue'];
      
	
      $arrParam = parent::normalizeParameter($arrParam, true);

		return $arrParam;
	}

   function updateGL($rs){
        
      if (!USE_GL) return;
      
      $warehouse = new Warehouse();
      $coaLink = new COALink();
      $generalJournal = new GeneralJournal();
      $assetItem = new AssetItem();
      $item = new Item();

      $warehousekey = $rs[0]['warehousekey'];
      
      $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
      $arr = array();
      $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
      $arr['code'] = 'xxxxx';
      $arr['refkey'] = $rs[0]['pkey'];
      $arr['refTableType'] = $rsKey['key'];
      $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
      $arr['createdBy'] = 0; 
      $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
      
      $rsAssetItem = $assetItem->searchData($assetItem->tableName.'.pkey', $rs[0]['assetitemkey'], true);
      $assetItemName = $rsAssetItem[0]['serialnumber'] . ' - ' .$rsAssetItem[0]['name'] . ' - ' .$rsAssetItem[0]['brandname'] . ' - ' .$rsAssetItem[0]['carseriesname'] . ' - ' .$rsAssetItem[0]['assetcategoryname'];
      $arr['trDesc'] = $this->ucFirst($this->lang['cogsAdjustment']. ' ' .  $this->lang['for']) . ' '.$assetItemName; 
      
      $temp = -1;
      
      $rsDetail = $this->getDetailById($rs[0]['pkey']);
            
      $totalCOGS = 0;
      
      for ($i=0;$i<count($rsDetail);$i++) { 

         $itemCOAKey = $item->getInventoryCOAKey($rsDetail[$i]['itemkey'], $rs[0]['warehousekey']); 

         $temp++;
         // $arr['hidCOAKey'][$temp] = $rsDetail[$i]['coakey'];
         $arr['hidCOAKey'][$temp] = $itemCOAKey;
         $arr['debit'][$temp] = 0;
         $arr['credit'][$temp] = $rsDetail[$i]['adjustmentvalue'];
         $arr['refCashBankKey'][$temp] = '';

         $totalCOGS += $rsDetail[$i]['adjustmentvalue'];
      }

      $rsCOA = $coaLink->getCOALink ('inventoryitemasset', $warehouse->tableName,$warehousekey);
      $temp++;
      $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
      $arr['debit'][$temp] = $totalCOGS; 
      $arr['credit'][$temp] = 0;      
      
      $arrayToJs = $generalJournal->addData($arr);
      if (!$arrayToJs[0]['valid'])
              throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
       
  }

}


?>
