<?php 

class ReceivingPurchaseJewelry extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'receiving_purchase_jewelry_header';
        $this->tableNameDetail = 'receiving_purchase_jewelry_detail';
        $this->tablePurchaseOrderHeader = 'purchase_order_jewelry_header';
        $this->tablePurchaseOrder = 'purchase_order_jewelry_header';
        $this->tableSupplier = 'supplier';
        $this->tableWarehouse = 'warehouse';
        $this->tableItemUnit = 'item_unit';
        $this->tableItem = 'item';
        $this->tablePackaging = 'packaging';
        $this->tableStatus = 'transaction_status';

        $this->isTransaction = true; 

        $this->securityObject = 'ReceivingPurchaseJewelry';


        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['refpodetailkey'] = array('selItemPurchaseOrder');
        $this->arrDataDetail['itemkey'] = array('hidItemKey');
        $this->arrDataDetail['orderedqtyinbaseunit'] = array('orderedQtyInBaseUnit', 'number');
        $this->arrDataDetail['baseunitkey'] = array('hidBaseUnitKey');
        $this->arrDataDetail['qtyminusinbaseunit'] = array('qtyMinusInBaseUnit', 'number');
        $this->arrDataDetail['receivedqtyinbaseunit'] = array('receivedQtyInBaseUnit', 'number');
        $this->arrDataDetail['costinbaseunit'] = array('costinbaseunit', 'number');
        $this->arrDataDetail['orderedqtyinpcs'] = array('orderedQtyInPcs','number');
        $this->arrDataDetail['qtyminusinpcs'] = array('qtyMinusInPcs', 'number');
        $this->arrDataDetail['receivedqtyinpcs'] = array('receivedQtyInPcs', 'number');
        $this->arrDataDetail['packagingkey'] = array('hidPackagingKey');
        $this->arrDataDetail['grossweight'] = array('grossWeight', 'number');
        $this->arrDataDetail['rownumber'] = array('rowNumber', 'number');
        $this->arrDataDetail['trdesc'] = array('trDetailDesc');
        $this->arrDataDetail['beforegrossweight'] = array('beforeGrossWeight', 'number');
        $this->arrDataDetail['labelweight'] = array('labelWeight', 'number');

        //$this->arrPaymentDetail = array();
        //$this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        //$this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        //$this->arrPaymentDetail['amount'] = array('paymentMethodValue', array('datatype' => 'number', 'mandatory' => true));
        //$this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod', array('mandatory' => true));

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        //array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['refkey'] = array('hidPurchaseOrderKey');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['shipmentfee'] = array('shipmentFee', 'number');
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['balance'] = array('balance', 'number');
        $this->arrData['totalpayment'] = array('totalPayment', 'number');
        $this->arrData['statuskey'] = array('selStatus');

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Kode PO', $this->tablePurchaseOrderHeader. '.code'));
        array_push($this->arrSearchColumn, array('Supplier', $this->tableSupplier. '.name'));
        array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 100, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'poCode', 'title' => 'poCode', 'dbfield' => 'purchaseordercode', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier', 'title' => 'supplier', 'dbfield' => 'suppliername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc', 'title' => 'note', 'dbfield' => 'trdesc', 'width' => 200));
   
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printPackagingCode', 'name' => $this->lang['print'] . ' ' .$this->lang['packaging'],  'icon' => 'print', 'url' => 'print/packagingCode?module=receiving-jewelry'));
         
        
        $this->includeClassDependencies(array(
            'Warehouse.class.php',
            'ItemUnit.class.php',
            'Item.class.php',
            'PurchaseOrderJewelry.class.php',
            'PaymentMethod.class.php',
            'TermOfPayment.class.php',
            'ItemMovement.class.php',
            'PackagingCode.class.php'
        ));

        $this->overwriteConfig();

    }

    function getQuery()
    {
        $sql = '
			SELECT 
                ' . $this->tableName . '.*,
                '.$this->tablePurchaseOrderHeader.'.code as purchaseordercode,
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableSupplier.'.name as suppliername,
			    ' . $this->tableStatus . '.status as statusname
			FROM 
                ' . $this->tableName . ', 
                ' . $this->tableWarehouse . ', 
                '.$this->tablePurchaseOrderHeader.',
                '.$this->tableSupplier.',
                ' . $this->tableStatus . '  
			WHERE
                ' . $this->tableName . '.refkey = '.$this->tablePurchaseOrderHeader.'.pkey and
                ' . $this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and
                ' . $this->tablePurchaseOrderHeader.'.supplierkey = '.$this->tableSupplier.'.pkey and
                ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
		' . $this->criteria;

        $sql .= $this->getCompanyCriteria();
        $sql .= $this->getWarehouseCriteria();

        return $sql;
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {
        $sql = 'select
	   			' . $this->tableNameDetail . '.*,
                ' . $this->tableItem . '.name as itemname,
                ' . $this->tablePackaging .'.name as packagingname,
                '. $this->tableItemUnit .'.name as baseunitname
			  from
			  	' . $this->tableNameDetail . '
                left join '.$this->tableItemUnit.' on '.$this->tableNameDetail.'.baseunitkey = '.$this->tableItemUnit.'.pkey
                left join '.$this->tablePackaging.' on '.$this->tableNameDetail.'.packagingkey = '.$this->tablePackaging.'.pkey,
                ' . $this->tableItem . '
			  where
			  	' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey and
                ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }

    function validateForm($arr, $pkey = '')
    {

        $item = new Item();  
        $purchaseOrderJewelry = new PurchaseOrderJewelry();

        $arrayToJs = parent::validateForm($arr, $pkey);

        if (!empty($pkey)){
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] <> 1){
                $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
            }
        } 

        $purchaseOrderKey = $arr['hidPurchaseOrderKey'];
        
        $arrItemKey = $arr['hidItemKey'];
        $arrItemPurchaseOrder = $arr['selItemPurchaseOrder'];
        $arrPackagingKey = $arr['hidPackagingKey'];

        $arrReceivedQtyInBaseUnit = $arr['receivedQtyInBaseUnit'];
        $arrReceivedQtyInPcs = $arr['receivedQtyInPcs'];
        $arrGrossWeight = $arr['grossWeight'];
        $arrOrderedQtyInBaseUnit = $arr['orderedQtyInBaseUnit'];
        $arrOrderedQtyInPcs = $arr['orderedQtyInPcs'];
        $arrBaseUnitKey = $arr['hidBaseUnitKey'];

        if(empty($purchaseOrderKey)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['purchaseOrder'][1]);
        }else{
            
            $rsPurchaseOrderJewelry = $purchaseOrderJewelry->getDataRowById($purchaseOrderKey); 
            $receivedate = strtotime(str_replace('\'','',$this->oDbCon->paramDate($_POST['trDate'],' / ','Y-m-d')));
            $podate =  strtotime($rsPurchaseOrderJewelry[0]['trdate']);

            if ($receivedate < $podate)
                $this->addErrorList($arrayToJs,false, $this->errorMsg['receivingPurchaseJewelry'][1]);
        } 

        $arrCountQtyGroupPO = array();
        for($i=0;$i<count($arrItemKey);$i++) { 

            if (empty($arrItemKey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
			} else{
                $rsItem = $item->getDataRowById($arrItemKey[$i]);

                if(empty($arrPackagingKey[$i])) {
                    $this->addErrorList($arrayToJs,false, '<strong>'.$rsItem[0]['name'].'. </strong>'. $this->errorMsg['packaging'][1]);
                }
                

                $poDetailKey = $arrItemPurchaseOrder[$i];

                $qtyInBaseUnit = $this->unFormatNumber($arrReceivedQtyInBaseUnit[$i]);
                $qtyInPcs  = $this->unFormatNumber($arrReceivedQtyInPcs[$i]);
                $grossWeight = $this->unFormatNumber($arrGrossWeight[$i]);
                $orderedQtyInBaseUnit = $this->unFormatNumber($arrOrderedQtyInBaseUnit[$i]);
                $orderedQtyInPcs = $this->unFormatNumber($arrOrderedQtyInPcs[$i]);

                $baseUnitKey = $arrBaseUnitKey[$i];
                if($baseUnitKey <> $rsItem[0]['baseunitkey']) {
                     $this->addErrorList($arrayToJs, false, '<strong>' . $rsItem[0]['name'] . '. </strong>' . $this->errorMsg['receivingPurchaseJewelry'][7]); 
                }

                
                if($qtyInBaseUnit <= 0) {
                    $this->addErrorList($arrayToJs, false, '<strong>' . $rsItem[0]['name'] . '. </strong>' . $this->errorMsg['receivingPurchaseJewelry'][4]); 
                }
                
                if($qtyInPcs <= 0) {
                    $this->addErrorList($arrayToJs, false, '<strong>' . $rsItem[0]['name'] . '. </strong>' . $this->errorMsg['receivingPurchaseJewelry'][5]);    
                }
                
                if($grossWeight <= 0) {
                    $this->addErrorList($arrayToJs, false, '<strong>' . $rsItem[0]['name'] . '. </strong>' . $this->errorMsg['receivingPurchaseJewelry'][5] . ' (GRAM)');
                } else if($grossWeight < $qtyInPcs) {
                    $this->addErrorList($arrayToJs, false, '<strong>' . $rsItem[0]['name'] . '. </strong>' . $this->errorMsg['receivingPurchaseJewelry'][6]);
                }

                //jumlah qty per baris PO
                if (!isset($arrCountQtyGroupPO[$poDetailKey])) {
                    $arrCountQtyGroupPO[$poDetailKey] = [
                        'totalReceivedQtyInBaseUnit' => 0,
                        'totalReceivedQtyInPcs'      => 0,
                        'count'                 => 0
                    ];
                }

                $arrCountQtyGroupPO[$poDetailKey]['totalReceivedQtyInBaseUnit'] += $qtyInBaseUnit;
                $arrCountQtyGroupPO[$poDetailKey]['totalReceivedQtyInPcs']      += $qtyInPcs;
                $arrCountQtyGroupPO[$poDetailKey]['count']++;

            }

        }

        if(!empty($arrCountQtyGroupPO)) {
            foreach($arrCountQtyGroupPO as $key => $row) {
                $rsPODetail = $purchaseOrderJewelry->getDetailByColumn('pkey', $key);
                
                $outstanding = $rsPODetail[0]['qtyinbaseunit'] - $rsPODetail[0]['receivedqtyinbaseunit'];
                $outstandingInPcs = $rsPODetail[0]['qtyinpcs'] - $rsPODetail[0]['receivedqtyinpcs'];

                //cek jml qty  lebih dari di detail PO tidak
                // sementara boleh, karena kalo hitunganyna gramasi, perhiasan kadang bisa lebih byk terimanya
                //if($row['totalReceivedQtyInBaseUnit'] > $outstanding ) {
                //    $this->addErrorList($arrayToJs,false, '<strong>'.$rsPODetail[0]['itemname'].'. </strong>'. $this->errorMsg['receivingPurchaseJewelry'][2]. '<strong>Max. '.$this->formatNumber($outstanding).'</strong>');
                //}

                 //cek jml qty (pcs)  lebih dari di detail PO tidak
                 // sementara boleh, karena kalo hitunganyna gramasi, perhiasan kadang bisa lebih byk terimanya
                //if($row['totalReceivedQtyInPcs'] > $outstandingInPcs) {
                //    $this->addErrorList($arrayToJs,false, '<strong>'.$rsPODetail[0]['itemname'].'. </strong>'. $this->errorMsg['receivingPurchaseJewelry'][3]. '<strong>Max. '.$this->formatNumber($outstandingInPcs).'</strong>');
                //}

            }
        }

        return $arrayToJs;
    }

    function validateConfirm($rsHeader)
    {
        $purchaseOrderJewelry = new PurchaseOrderJewelry();
        $id = $rsHeader[0]['pkey'];

        //cek apakah PO sudah selesai
        $rsPO = $purchaseOrderJewelry->getDataRowById($rsHeader[0]['refkey']);

        if(!empty($rsPO)) {
            if($rsPO[0]['statuskey'] == TRANSACTION_STATUS['selesai']) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . '<br> <strong>'.$rsPO[0]['code'].'. </strong>' .  $this->errorMsg[204]);
            }
        } else {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . '<br> <strong>'.$this->lang['purchaseOrderJewelry'].'. </strong>' .  $this->errorMsg[213]);
        }
 
    }
    
    function confirmTrans($rsHeader)
    {
        $itemMovement = new ItemMovement();
        $purchaseOrderJewelry = new PurchaseOrderJewelry();
        $supplier = new Supplier();
        
        $id = $rsHeader[0]['pkey'];
        $this->updateCostAndPrice($id);

        $rsPO = $purchaseOrderJewelry->searchDataRow(array($purchaseOrderJewelry->tableName.'.pkey',
                                                        $purchaseOrderJewelry->tableName.'.code',
                                                        $purchaseOrderJewelry->tableName.'.warehousekey',
                                                        $purchaseOrderJewelry->tableName.'.supplierkey'
                                                        ),
                                                  ' and '.$purchaseOrderJewelry->tableName.'.pkey =  ' . $this->oDbCon->paramString($rsHeader[0]['refkey'])
                                                ); 
        $rsSupplier = $supplier->getDataRowById($rsPO[0]['supplierkey']);
         
        $note = $rsHeader[0]['code'] . '. ' . $this->ucFirst($this->lang['receivingPurchaseJewelry'] . ' ' . $this->lang['from']) . ' ' . $rsPO[0]['code'] . ', ' . $rsSupplier[0]['name'] . '.';
        $rsDetail = $this->getDetailById($id);

        for($i=0; $i<count($rsDetail); $i++) { 
            if($rsDetail[$i]['receivedqtyinbaseunit'] != 0){ 
                $itemMovement->updateItemMovement(array('refkey' => $id, 'refdetailkey' => $rsDetail[$i]['pkey']), 
                                                  $rsDetail[$i]['itemkey'], 
                                                  array('qtyinbaseunit' => $rsDetail[$i]['receivedqtyinbaseunit'],'qtyinpcs' => $rsDetail[$i]['receivedqtyinpcs']),
                                                  array('costinbaseunit'=>$rsDetail[$i]['costinbaseunit'], 'costinpcs'=>$rsDetail[$i]['costinpcs']), 
                                                  $this->tableName, 
                                                  $rsPO[0]['warehousekey'], 
                                                  $note, 
                                                  $rsHeader[0]['trdate']);
            }
        }

    }
    
    function updateCostAndPrice($id){
        
        $purchaseOrderJewelry = new PurchaseOrderJewelry();
        $sql = 'update  
                    '.$purchaseOrderJewelry->tableNameDetail.',
                    '.$this->tableNameDetail.' 
             set 
                '.$this->tableNameDetail.'.costinbaseunit = '.$purchaseOrderJewelry->tableNameDetail.'.priceinunit,
                '.$this->tableNameDetail.'.costinpcs = '.$purchaseOrderJewelry->tableNameDetail.'.priceinpcs
             where
                '.$this->tableNameDetail.'.refkey = ' . $this->oDbCon->paramString($id).' and  
                '.$this->tableNameDetail.'.refpodetailkey = '.$purchaseOrderJewelry->tableNameDetail.'.pkey 
             ';
        
        $this->oDbCon->execute($sql);
    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {

    }

    function cancelTrans($rsHeader, $copy)
    {
        $id = $rsHeader[0]['pkey'];

        $itemMovement = new ItemMovement();  
        $itemMovement->cancelMovement($id,$this->tableName);

        if ($copy)
            $this->copyDataOnCancel($id);

    }

    function validateClose($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];

    }

    function closeTrans($rsHeader)
    {

    }

    function afterStatusChanged($rsHeader)
    {
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);

        $purchaseOrderJewelry = new PurchaseOrderJewelry();
        $purchaseOrderJewelry->updateReceivingPurchaseOrderJewelryItem($rsHeader[0]['refkey']);

    }

    function afterAddDataOnCopy($pkey, $oldkey)
    {

    }

    function getDetailForPurchaseOrder($pkey)
    {
        $sql = 'select
	   			' . $this->tableNameDetail . '.*,
                '. $this->tableName .'.code,
                ' . $this->tableItem . '.name as itemname,
                ' . $this->tablePackaging .'.name as packagingname,
                  '. $this->tableItemUnit .'.name as baseunitname
			  from
			  	' . $this->tableNameDetail . '
                  left join ' . $this->tableItemUnit . ' on ' . $this->tableNameDetail . '.baseunitkey = ' . $this->tableItemUnit . '.pkey
                left join '.$this->tablePackaging.' on '.$this->tableNameDetail.'.packagingkey = '.$this->tablePackaging.'.pkey,
                ' . $this->tableItem . ',
                '. $this->tableName .'
			  where
			  	' . $this->tableNameDetail . '.refkey = ' . $this->tableName . '.pkey and
			  	' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey and
                ' . $this->tableNameDetail . '.refpodetailkey in (' . $this->oDbCon->paramString($pkey, ',') . ') and
                ' . $this->tableName . '.statuskey in (2,3)
            ';

        return $this->oDbCon->doQuery($sql);
    }

    function searchDataForAutoComplete($fieldname = '', $searchkey = '', $mustmatch = false, $searchCriteria = '', $orderCriteria = '', $limit = '')
    {
        $sql = 'select
					' . $this->tableName . '.pkey,  
                    '. $this->tableName .'.code as value
				from 
					' . $this->tableName . ',
                    ' . $this->tableStatus . '
				where  		
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
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

    function reCountSubtotal($arrParam)
    {
    
    }

    function getTotalQtyReceiving($pkey)
    {
        $sql = '
            select
                coalesce(sum('.$this->tableNameDetail.'.receivedqtyinbaseunit),0) as totalreceivedqtyinbaseunit, 
                coalesce(sum('.$this->tableNameDetail.'.receivedqtyinpcs),0) as totalreceivedqtyinpcs, 
                coalesce(sum('.$this->tableNameDetail.'.grossweight),0) as totalgrossweight
            from
                '.$this->tableNameDetail.',
                '.$this->tableName.'
            where
                '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and
                '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey).'
        ';

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }


    function normalizeParameter($arrParam, $trim = false)
    {

        $purchaseOrderJewelry = new PurchaseOrderJewelry();
        $item = new Item();

        $purchaseOrderKey = $arrParam['hidPurchaseOrderKey'];  
        $rsPO = $purchaseOrderJewelry->getDataRowById($purchaseOrderKey); 
        $arrParam['selWarehouse'] = $rsPO[0]['warehousekey'];
        
        $rsPODetail = $purchaseOrderJewelry->getDetailById($rsPO[0]['pkey']);
        $rsPODetail = $this->reindexDetailCollections($rsPODetail,'pkey');

        $arrSelItemPO = $arrParam['selItemPurchaseOrder'];
        $arrItemKey = $arrParam['hidItemKey'];
       
        $rsItem = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.baseunitkey'),
                                      ' and '. $item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemKey,',').')');
        $rsItem = array_column($rsItem,null,'pkey');
        
        $arrParam['hidSupplierKey'] = $rsPO[0]['supplierkey'];
            
        for($i=0;$i<count($arrSelItemPO);$i++) {

            if(!isset($rsPODetail[$arrSelItemPO[$i]])) continue;

            $arrItem = $rsItem[$arrItemKey[$i]];
            
            $arrParam['hidBaseUnitKey'][$i] = $arrItem['baseunitkey']; 
            $arrParam['orderedQtyInBaseUnit'][$i] = $rsPODetail[$arrSelItemPO[$i]][0]['qtyinbaseunit'];
            $arrParam['rowNumber'][$i] = $rsPODetail[$arrSelItemPO[$i]][0]['number'];
            $arrParam['orderedQtyInPcs'][$i] = $rsPODetail[$arrSelItemPO[$i]][0]['qtyinpcs'];
        }

        $labelWeight = $this->loadSetting('labelWeight');
        for($j=0; $j<count($arrItemKey); $j++) {
                $beforeGrossWeight = $this->unFormatNumber($arrParam['beforeGrossWeight'][$j]);
                $arrParam['grossWeight'][$j] = $beforeGrossWeight + $labelWeight;
                $arrParam['labelWeight'][$j] = $labelWeight;
        }

        $arrParam = parent::normalizeParameter($arrParam, true);
        
        return $arrParam; 

    }


}

?>
