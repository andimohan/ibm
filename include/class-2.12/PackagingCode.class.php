<?php 

class PackagingCode extends BaseClass
{
    
    function __construct(){
		
		 parent::__construct();

        $this->tableName = 'packaging_code';
        $this->tablePackaging = 'packaging';
        $this->tableItem = 'item';
        $this->tableItemUnit = 'item_unit';
        $this->tableSupplier = 'supplier';
        $this->tableReceivingPurchaseJewelryHeader = 'receiving_purchase_jewelry_header';
        $this->tableReceivingPurchaseJewelryDetail = 'receiving_purchase_jewelry_detail';
        $this->tableStatus = 'master_status';

        $this->securityObject = 'PackagingCode'; 

        $this->newLoad = true;

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['reftransactionkey'] = array('hidRefTransactionKey');
        $this->arrData['reftransactiondetailkey'] = array('hidRefTransactionDetailKey');
        $this->arrData['reftabletype'] = array('reftabletype');
        $this->arrData['itemkey'] = array('hidItemKey');
        $this->arrData['qtyinpcs'] = array('qtyInPcs','number');
        $this->arrData['qtyinbaseunit'] = array('qtyInBaseUnit','number');
        $this->arrData['costinbaseunit'] = array('costInBaseUnit','number');
        $this->arrData['costinpcs'] = array('costInPcs','number');
        $this->arrData['rownumber'] = array('rowNumber','number');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['packagingkey'] = array('packagingkey');
        $this->arrData['supplierkey'] = array('supplierkey');
 
        $this->includeClassDependencies(array(
            'Supplier.class.php'
      )); 
		
	}

    function getQuery(){
	  
	      $sql = '
          select
              ' . $this->tableName . '.*,
              CONCAT(' . $this->tableName . '.code, \' - \', ' . $this->tablePackaging . '.name) as value,
              ' . $this->tableItem . '.code as itemcode,
              ' . $this->tableItem . '.name as itemname,
              ' . $this->tableItem . '.sellingprice,
              ' . $this->tableItem . '.aliasname as itemalias, 
              ' . $this->tableItemUnit . '.name as baseunitname, 
              ' . $this->tableReceivingPurchaseJewelryHeader . '.code as receivingcode, 
              ' . $this->tableReceivingPurchaseJewelryDetail . '.receivedqtyinpcs as netweight,
              ' . $this->tableReceivingPurchaseJewelryDetail . '.grossweight,
              ' . $this->tablePackaging . '.pkey as packagingkey,
              ' . $this->tablePackaging . '.code as packagingcode,
              ' . $this->tablePackaging . '.name as packagingname,
              ' . $this->tableSupplier . '.name as suppliername,
              ' . $this->tableSupplier . '.code as suppliercode, 
              ' . $this->tableStatus . '.status as statusname
            from
              '.$this->tableName.' 
                  left join  '.$this->tableSupplier.'  on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey,
              '.$this->tableItem.'
                  left join  '.$this->tableItemUnit.'  on '.$this->tableItem.'.baseunitkey = '.$this->tableItemUnit.'.pkey ,

              '.$this->tableStatus.',
              '.$this->tableReceivingPurchaseJewelryDetail.',
              '.$this->tablePackaging.',
              '.$this->tableReceivingPurchaseJewelryHeader.'
            where 
              '.$this->tableName.'.itemkey = '.$this->tableItem.'.pkey and
              ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
              '.$this->tableName.'.reftransactionkey = '.$this->tableReceivingPurchaseJewelryHeader.'.pkey and
              '.$this->tableName.'.reftransactiondetailkey = '.$this->tableReceivingPurchaseJewelryDetail.'.pkey and
              '.$this->tableReceivingPurchaseJewelryDetail.'.packagingkey = '.$this->tablePackaging.'.pkey
        ' .$this->criteria ; 
		 
        return $sql;
    }

    function addPackagingCode($rs,$arrParam=array()){

        $tablekey = $arrParam['tablekey'];
        $pkey = $rs[0]['pkey'];
        $code = $rs[0]['code'];
        $supplierkey = $rs[0]['supplierkey'];
        $reftransactionkey = $rs[0]['pkey']; 
        $trDate = $rs[0]['trdate']; 
      
        $reftransactiondetailkey = (!isset($arrParam['reftransactiondetailkey']) ? 0 : $arrParam['reftransactiondetailkey']);
        $itemkey = (!isset($arrParam['itemkey']) ? 0 : $arrParam['itemkey']);
        $qtyinpcs = (!isset($arrParam['qtyinpcs']) ? 0 : $arrParam['qtyinpcs']);
        $qtyinbaseunit = (!isset($arrParam['qtyinbaseunit']) ? 0 : $arrParam['qtyinbaseunit']);
        $costinbaseunit = (!isset($arrParam['costinbaseunit']) ? 0 : $arrParam['costinbaseunit']);
        $costinpcs = (!isset($arrParam['costinpcs']) ? 0 : $arrParam['costinpcs']);
        $rowNumber = (!isset($arrParam['rownumber']) ? 0 : $arrParam['rownumber']);
        $trdesc = (!isset($arrParam['trdesc']) ? '' : $arrParam['trdesc']);

        $arr = array();
        $arr['code'] = 'xxxxxx';
        $arr['trDate'] = $this->formatDBDate($trDate);
        $arr['hidRefTransactionKey'] = $reftransactionkey;
        $arr['hidRefTransactionDetailKey'] = $reftransactiondetailkey;
        $arr['reftabletype'] = $tablekey;
        $arr['hidItemKey'] = $itemkey;
        $arr['qtyInPcs'] = $qtyinpcs;
        $arr['qtyInBaseUnit'] = $qtyinbaseunit;
        $arr['costInBaseUnit'] = $costinbaseunit;
        $arr['costInPcs'] = $costinpcs;
        $arr['trDesc'] = $trdesc;
        $arr['packagingkey'] = $arrParam['packagingkey'];
        $arr['rowNumber'] = $rowNumber;
        $arr['supplierkey'] = $supplierkey;
      
        $arrayToJs = $this->addData($arr);

        if (!$arrayToJs[0]['valid']) {
          $this->addErrorLog(false, '<strong>' . $code . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message'], true);
        }

        return $arrayToJs[0]['data'];

  }

  //function deletePackagingCode($reftransactionkey, $tableName)
  //{
//
  //  $tablekey = $this->getTableKeyAndObj($tableName, array(('key')))['key'];
//
  //  $sql = '
  //      DELETE FROM
  //        '. $this->tableName .' 
  //      WHERE 
  //        '.$this->tableName.'.reftransactionkey in ('.$this->oDbCon->paramString($reftransactionkey,',').') and 
  //        '.$this->tableName.'.reftabletype = '. $this->oDbCon->paramString($tablekey) .'
  //  ';
//
  //  $this->oDbCon->execute($sql);
  //  
//
  //}

  function getAvailablePackagingCodeByItem($itemkey)
  {
    $sql = '
      select 
        '.$this->tableName.'.*,
        '.$this->tableName.'.code as value,
        '.$this->tableItem.'.code as itemcode,
        '.$this->tableItem.'.name as itemname,
        '.$this->tableReceivingPurchaseJewelryDetail.'.receivedqtyinpcs as netweight,
        '.$this->tableReceivingPurchaseJewelryDetail.'.grossweight,
        '.$this->tablePackaging.'.name as packagingname
      from
        '.$this->tableName.',
        '.$this->tableReceivingPurchaseJewelryDetail.',
        '.$this->tablePackaging.',
        '.$this->tableItem.'
      where
        '.$this->tableName.'.statuskey = 1 and
        '.$this->tableName.'.itemkey in ('.$this->oDbCon->paramString($itemkey,',').') and
        '.$this->tableName.'.itemkey = '.$this->tableItem.'.pkey and
        '.$this->tableName.'.reftransactiondetailkey = '.$this->tableReceivingPurchaseJewelryDetail.'.pkey and
        '.$this->tableReceivingPurchaseJewelryDetail.'.packagingkey = '.$this->tablePackaging.'.pkey and
        '.$this->tableName.'.qtyinpcs > 0 and '.$this->tableName.'.qtyinbaseunit > 0
        order by '.$this->tableName.'.trdate asc, '.$this->tableName.'.qtyinbaseunit desc
    ';
     
    $rs =  $this->oDbCon->doQuery($sql);
    
    return $rs;
  }

  function updateQty($pkey, $qtyinbaseunit, $qtyinpcs)
  {
    $sql = '
      update
        '.$this->tableName.'
      set
        '.$this->tableName.'.qtyinbaseunit = '.$this->oDbCon->paramString($qtyinbaseunit).',
        '.$this->tableName.'.qtyinpcs = '.$this->oDbCon->paramString($qtyinpcs).'
      where
        '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey).'
    ';

    $this->oDbCon->execute($sql);

  }



  function searchDataForReceivingPurchase($fieldname = '', $searchkey = '', $mustmatch = false, $searchCriteria = '', $orderCriteria = '', $limit = '')
  { 

    $tablekey = $this->getTableKeyAndObj($this->tableReceivingPurchaseJewelryHeader, array(('key')))['key'];

    $sql = '
      select
        '.$this->tableName.'.pkey,
        '.$this->tableName.'.code as value,
        '.$this->tableName.'.qtyinbaseunit,
        '.$this->tableName.'.qtyinpcs,
        '.$this->tableName.'.reftransactionkey,
        '.$this->tableName.'.reftransactiondetailkey,
        '.$this->tableName.'.packagingkey,
        '.$this->tableName.'.trdesc,
        '.$this->tableItem.'.name as itemname,
        '.$this->tableItem.'.code as itemcode,
        '.$this->tableReceivingPurchaseJewelryHeader.'.code as receivingpurchasecode,
        '.$this->tableReceivingPurchaseJewelryDetail.'.receivedqtyinpcs as netweight,
        '.$this->tableReceivingPurchaseJewelryDetail.'.grossweight,
        '.$this->tablePackaging.'.code as packagingcode,
        '.$this->tablePackaging.'.name as packagingname
      from
        '.$this->tableName.',
        '.$this->tableStatus.',
        '.$this->tableReceivingPurchaseJewelryHeader.',
        '.$this->tableReceivingPurchaseJewelryDetail.',
        '.$this->tableItem.',
        '.$this->tablePackaging.'
      where
        '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
        '.$this->tableName.'.reftabletype = '.$this->oDbCon->paramString($tablekey).' and
        '.$this->tableName.'.reftransactionkey = '.$this->tableReceivingPurchaseJewelryHeader.'.pkey and
        '.$this->tableName.'.reftransactiondetailkey = '.$this->tableReceivingPurchaseJewelryDetail.'.pkey and
        '.$this->tableName.'.itemkey = '.$this->tableItem.'.pkey and
        '. $this->tableName.'.packagingkey = '.$this->tablePackaging.'.pkey 
    ';

    if (!empty($fieldname)) {

            $sql .= ' and ';

            if ($mustmatch)
                $sql .= $fieldname . ' = ' . $this->oDbCon->paramString($searchkey);
            else
                $sql .= $fieldname . ' like ' . $this->oDbCon->paramString('%' . $searchkey . '%');
        }

        if ($searchCriteria <> '')
            $sql .= ' ' . $searchCriteria;

        if ($orderCriteria <> '') {
            $sql .= ' ' . $orderCriteria;

        }

        if ($limit <> '')
            $sql .= ' ' . $limit;
          
        return $this->oDbCon->doQuery($sql);

  }

  function getDataByBarcodeForReceivingPurchase($barcode, $criteria = '')
  {
    $tablekey = $this->getTableKeyAndObj($this->tableReceivingPurchaseJewelryHeader, array(('key')))['key'];
    $sql = '
      select
        '.$this->tableName.'.pkey,
        '.$this->tableName.'.code,
        '.$this->tableName.'.qtyinbaseunit,
        '.$this->tableName.'.qtyinpcs,
        '.$this->tableName.'.reftransactionkey,
        '.$this->tableName.'.reftransactiondetailkey,
        '.$this->tableName.'.packagingkey,
        '.$this->tableName.'.trdesc,
        '.$this->tableItem.'.pkey as itemkey,
        '.$this->tableItem.'.name as itemname,
        '.$this->tableItem.'.code as itemcode,
        '.$this->tableItem.'.aliasname as itemaliasname,
        '.$this->tablePackaging.'.pkey as packaginkey,
        '.$this->tablePackaging.'.name as packagingname,
        '.$this->tablePackaging.'.code as packagingcode,
        '.$this->tableReceivingPurchaseJewelryHeader.'.code as receivingcode,
        '.$this->tableReceivingPurchaseJewelryDetail . '.receivedqtyinpcs as netweight,
        '.$this->tableReceivingPurchaseJewelryDetail . '.grossweight
      from
        '.$this->tableName.',
        '.$this->tableItem.',
        '.$this->tableReceivingPurchaseJewelryHeader.',
        '.$this->tableReceivingPurchaseJewelryDetail.',
        '.$this->tablePackaging.'
      where
        '.$this->tableName.'.itemkey = '.$this->tableItem.'.pkey and
        '.$this->tableName.'.code = '.$this->oDbCon->paramString($barcode).' and
        '.$this->tableName.'.reftransactionkey = '.$this->tableReceivingPurchaseJewelryHeader.'.pkey and
        '.$this->tableName.'.reftransactiondetailkey = '.$this->tableReceivingPurchaseJewelryDetail.'.pkey and
        '.$this->tableName.'.packagingkey = '.$this->tablePackaging.'.pkey and
        '.$this->tableName.'.reftabletype = '.$this->oDbCon->paramString($tablekey).' and
        '.$this->tableName.'.statuskey = 1
    ';


    if(!empty($criteria)) {
      $sql .= ' ' . $criteria;
    }

    $rs = $this->oDbCon->doQuery($sql);

    return $rs;

  }


  function normalizeParameter($arrParam, $trim = false)
  {
        $arrParam = parent::normalizeParameter($arrParam, true);
        
        return $arrParam; 

  }

}


?>
