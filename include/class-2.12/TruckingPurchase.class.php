<?php

class TruckingPurchase extends BaseClass
{

    function __construct()
    {

        parent::__construct();
        $this->tableName = 'trucking_purchase_header';
        $this->tableNameDetail = 'trucking_purchase_detail';
        $this->tableNameItemDetail = 'trucking_purchase_item_detail';
        $this->tableNameJobOrderDetail = 'trucking_purchase_job_detail';
        $this->tableJobOrder = 'trucking_service_order_header';
        $this->tableWorkOrder = 'trucking_service_work_order';
        $this->tableDownpayment = 'trucking_purchase_downpayment';
        $this->tableCashBank = 'cash_bank';

        $this->tableStatus = 'transaction_status';
        $this->tableItem = 'item';
        $this->tableSupplier = 'supplier';
        $this->tableContact = 'contact_person';
        $this->tablePayment = 'trucking_purchase_payment';
        $this->securityObject = 'TruckingPurchase';
        $this->isTransaction = true;
        $this->newLoad = true;

        $this->arrItem = array();
        $this->arrItem['pkey'] = array('hidDetailItemKey');
        $this->arrItem['refkey'] = array('hidDetailKey', 'ref');
        $this->arrItem['refheaderkey'] = array('pkey', 'ref');
        $this->arrItem['itemkey'] = array('hidItemDetailKey', array('mandatory' => true));
        $this->arrItem['wodetailkey'] = array('hidWODetailKey');
        $this->arrItem['detailtype'] = array('detailType');
        $this->arrItem['istax23'] = array('chkIsTax23');
        $this->arrItem['remark'] = array('remarkDetail');
        $this->arrItem['aliasname'] = array('itemNameAliasDetail');
        $this->arrItem['qty'] = array('qtyDetail', 'number', array('mandatory' => true));
        $this->arrItem['priceinunit'] = array('priceInUnitDetail', 'number', array('mandatory' => true));
        $this->arrItem['total'] = array('subtotalDetail', 'number');
        $this->arrItem['taxdetail'] = array('taxDetail', 'number');
        $this->arrItem['taxdetailvalue'] = array('taxValueDetail', 'number');
        $this->arrItem['beforetaxdetailvalue'] = array('beforeTaxDetail', 'number');
        $this->arrItem['aftertaxdetailvalue'] = array('afterTaxDetail', 'number');
        $this->arrItem['tax23percentagedetail'] = array('tax23PercentageDetail', 'number');
        $this->arrItem['ispriceincludetax'] = array('chkIncludeTaxDetail');
        $this->arrItem['isreimburse'] = array('chkIsReimburse');

		
        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey', array('dataDetail' => array('dataset' => $this->arrItem, 'tableName' => $this->tableNameItemDetail)));
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['wokey'] = array('hidWOKey');
        $this->arrDataDetail['sokey'] = array('hidSODetailKey');
        $this->arrDataDetail['car'] = array('car');
        $this->arrDataDetail['qty'] = array('qty', 'number');
        $this->arrDataDetail['wodate'] = array('workOrderDate', 'date');
        $this->arrDataDetail['priceinunit'] = array('priceInUnit', 'number');
        $this->arrDataDetail['subtotal'] = array('detailSubtotal', 'number');
        $this->arrDataDetail['total'] = array('detailTotal', 'number');

        $this->arrPaymentDetail = array();
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue', array('datatype' => 'number', 'mandatory' => true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod'); 
        $this->arrPaymentDetail['cashbankvoucherkey'] = array('selVoucher');

        $this->arrJobOrderDetail = array();
        $this->arrJobOrderDetail['pkey'] = array('hidDetailJobOrderKey');
        $this->arrJobOrderDetail['refkey'] = array('pkey', 'ref');
        $this->arrJobOrderDetail['sokey'] = array('hidSOKey');

        // $this->allowedStatusForEdit = array(1, 2);
        $arrDetails = array();
        // array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
        array_push($arrDetails, array('dataset' => $this->arrJobOrderDetail, 'tableName' => $this->tableNameJobOrderDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['subtotal'] = array('total', 'number');
        $this->arrData['balance'] = array('balance', 'number');
        $this->arrData['termofpaymentkey'] = array('detailNotes');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['refinvoicecode'] = array('refInvoiceCode');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['taxvalue'] = array('taxValue', 'number');
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal', 'number');
        $this->arrData['termofpaymentkey'] = array('selTermOfPayment');
        $this->arrData['totalpayment'] = array('totalPayment', 'number');
        $this->arrData['balance'] = array('balance', 'number');
        $this->arrData['grandtotal'] = array('total', 'number');
        $this->arrData['finaldiscounttype'] = array('selFinalDiscountType', 'number');
        $this->arrData['finaldiscount'] = array('finalDiscount', 'number');
        $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        $this->arrData['taxpercentage'] = array('taxPercentage', 'number');
        $this->arrData['tax23value'] = array('tax23Value', 'number');
        $this->arrData['salesordercodecache'] = array('salesOrderCodeCache');
        $this->arrData['stampfee'] = array('stampFee', 'number');


        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 100, 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier', 'title' => 'supplier', 'dbfield' => 'suppliername', 'default' => true, 'width' => 150,));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refinvoice', 'title' => 'invoiceReference', 'dbfield' => 'refinvoicecode', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'subtotal', 'title' => 'subtotal', 'dbfield' => 'subtotal', 'default' => true, 'width' => 100, 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


         $this->arrSearchColumn = array();
         array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code')); 
         array_push($this->arrSearchColumn, array('supplier', $this->tableSupplier . '.name'));  
         array_push($this->arrSearchColumn, array('Invoice', $this->tableName . '.refinvoicecode'));  
         array_push($this->arrSearchColumn, array('Sales', $this->tableName . '.salesordercodecache'));  

        $this->includeClassDependencies(array(
            'Supplier.class.php',
            'TruckingServiceOrder.class.php',
            'TruckingServiceWorkOrder.class.php',
            'Warehouse.class.php',
            'AP.class.php',
            'Item.class.php',
            'CashBank.class.php'
        ));

        $this->overwriteConfig();
    }

    function getQuery()
    {

        $sql = '
            SELECT ' . $this->tableName . '.* ,
                    ' . $this->tableSupplier . '.name as suppliername,
                    ' . $this->tableWarehouse . '.name as warehousename, 
			        ' . $this->tableStatus . '.status as statusname 
			FROM ' . $this->tableStatus . ',
                 ' . $this->tableName . ',
                 ' . $this->tableWarehouse . ',
                 ' . $this->tableSupplier . '
			WHERE 
                ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and
                ' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey
 		' . $this->criteria;
        return $sql;
    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {
        $id = $rsHeader[0]['pkey'];
        //cek ad AP terbayar
        $ap = new AP();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName, array('key'));
        $rsAP = $ap->searchData('', '', true, ' and ' . $ap->tableName . '.refkey = ' . $this->oDbCon->paramString($id) . ' and ' . $ap->tableName . '.reftabletype = ' . $rsAPKey['key'] . ' and (' . $ap->tableName . '.statuskey in (2,3))');

        if (!empty($rsAP))
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['ap'][2]);
    }

    function getItemDetail($refkey, $reffield = 'refkey', $orderby = '', $criteria='')
    {
        $sql = 'select 
                ' . $this->tableNameItemDetail . '.*, 
                ' . $this->tableItem . '.name as itemname ,
                ' . $this->tableItem . '.servicecost,
                ' . $this->tableItem . '.costcoakey,
                ' . $this->tableItem . '.revenuecoakey,
                ' . $this->tableItem . '.volume
            from 
                ' . $this->tableNameItemDetail . ',
                ' . $this->tableItem . '
            where 
                ' . $reffield . ' in  (' . $this->oDbCon->paramString($refkey, ',') . ') and 
                ' . $this->tableNameItemDetail . '.itemkey = ' . $this->tableItem . '.pkey';

        if (!empty($criteria))
            $sql .= ' ' . $criteria;
		
        if (!empty($orderby))
            $sql .= ' ' . $orderby;
		
        return  $this->oDbCon->doQuery($sql);
    }

    function validateForm($arr, $pkey = '')
    {

        // satu case boleh lebih dari satu job (utk job susulan)

        $arrayToJs = parent::validateForm($arr, $pkey);
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();

        $arrSOKey = $arr['hidSOKey'];
        $arrSOCode = $arr['SOCode'];
        $supplierKey = $arr['hidSupplierKey'];
        $refInvoiceCode = $arr['refInvoiceCode'];
        $arrWOKey = $arr['hidWOKey'];
        $arrWODetailKey = $arr['hidWODetailKey'];
        $arrAmount = $arr['amount'];
        $arrPick = $arr['chkPick'];
        $arrItemKey = $arr['hidItemDetailKey'];
        $arrDetailType = $arr['detailType']; 


//        $arrDetailJOKeys = array();
//        for ($i = 0; $i < count($arrSOKey); $i++) {
//            if (empty($arrSOKey[$i])) {
//                $this->addErrorList($arrayToJs, false, $this->errorMsg['jobOrder'][1]);
//            } else {
//                //  cek ada jo detail double gk 
//                if (in_array($arrSOKey[$i], $arrDetailJOKeys)) {
//                    $this->addErrorList($arrayToJs, false, $arrSOCode[$i] . '. ' . $this->errorMsg[215]);
//                } else {
//                    array_push($arrDetailJOKeys, $arrSOKey[$i]);
//                }
//            }
//        }

        if (empty($supplierKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['supplier'][1]);
        } 

        if (empty($refInvoiceCode)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['reference'][1]);
        }

        // cek detail transaksi tidak boleh kosong (case) saat import
        if (empty($arrAmount)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg[503]);
        }

        for ($i = 0; $i < count($arrAmount); $i++) {
            $arrAmount[$i] = $this->unformatNumber($arrAmount[$i]);
            $salesOrderSubtotal[$i] = $this->unformatNumber($salesOrderSubtotal[$i]);
            $salesOrderDownpayment[$i] = $this->unformatNumber($salesOrderDownpayment[$i]);

            if ($arrAmount[$i] <= 0)
                $this->addErrorList($arrayToJs, false, $this->errorMsg[503]);


            if (!empty($rsDetail[$i]['salesorderkey']) && ($arrAmount[$i] > ($salesOrderSubtotal[$i] - $salesOrderDownpayment[$i])))
                $this->addErrorList($arrayToJs, false, $this->errorMsg[508]);
        }

        $arrDetailKeys = array();


        for($i=0;$i<count($arrWOKey);$i++) { 

            $rsTruckingServiceWorkOrder = $truckingServiceWorkOrder->getDataRowById($arrWOKey[$i]);

            // cek detail double 
            if (in_array($arrWOKey[$i],$arrDetailKeys)){  
                $this->addErrorList($arrayToJs,false, $rsTruckingServiceWorkOrder[0]['code'].'. '.$this->errorMsg[215]); 	 
            }else{ 
                array_push($arrDetailKeys, $arrWOKey[$i]);
            } 

            if($rsTruckingServiceWorkOrder[0]['statuskey'] <> 3)  {
                $this->addErrorList($arrayToJs,false, $rsTruckingServiceWorkOrder[0]['code'].'. '.$this->errorMsg[205]); 
            }
            
            // CEK SUPPLIER SAMA ATAU TIDAK 
            // validasi supplierkey harus sama, ambil dr wo detail, karena di spk bisa beda vendor
            
            // kalo detailtype 1 (Trucking) cek ke header 
            $arrSupplierWO = array();
            if($arrDetailType[$i] == 1){ 
                array_push($arrSupplierWO, $rsTruckingServiceWorkOrder[0]['supplierkey']);
            }else{ 
                // kalo detailtype == 2 (cost) cek ke detail  
 
                $rsWODetail =  $truckingServiceWorkOrder->getCostDetail($arrWOKey[$i],'',' and '. $truckingServiceWorkOrder->tableCost .'.pkey in ('.$this->oDbCon->paramString($arrWODetailKey[$i], ',') .') ');  
                $arrSupplierWO = array_merge($arrSupplierWO,array_column($rsWODetail,'supplierkey'));
            }
            
            $arrSupplierWO = array_unique($arrSupplierWO);
            
            foreach($arrSupplierWO as $woSupplierKey){
                if( $supplierKey != $woSupplierKey)
                      $this->addErrorList($arrayToJs, false, $this->errorMsg['truckingPurchase'][2]);
            }

            
        } 

        // gk jadi, karena kena 2x validasi, gk efisien
//        $errMsg = $this->checkDetailWO(1, $arr);
//        foreach($errMsg as $row) 
//            $this->addErrorList($arrayToJs, false, $row);
 

        return $arrayToJs;
    }

   

    function checkDetailWO ($status, $arr) {
        
        $errMsg = array(); 
        $purchaseData = array(); 
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        $item = new Item();

        if ($status == 1) { 
            // bagian ini harusny sudah tdk terpakai
            $arrWOKey = $arr['hidWOKey'];
            $arrItemKey = $arr['hidItemDetailKey'];
            $arrPrice = $arr['priceInUnitDetail'];
            $arrDetailType = $arr['detailType']; 
            $arrTax = $arr['taxDetail']; 
            $arrTax23 = $arr['tax23PercentageDetail']; 
            $arrIsReimburse = $arr['chkIsReimburse']; 
            $arrWODetailKey = $arr['hidWODetailKey'];
            $arrWoCode = $arr['WOCode'];
            $arrItemNameDetail = $arr['itemNameDetail'];
            $supplierKey = $arr['hidSupplierKey'];
  
            for($i=0;$i<count($arrWOKey);$i++) { 
                for ($j=0; $j<count($arrItemKey[$i]); $j++) { 
                    
                    if (empty($arrItemKey[$i][$j])) continue; // gk bisa pake chkService karena gk diubah jd 2 dimensi
                    
                    $indexkey =  $arrDetailType[$i][$j].'-'.$arrWODetailKey[$i][$j];
                    
                    $purchaseData[$indexkey] = array();
                    $purchaseData[$indexkey]['wodetailkey'] =  $arrWODetailKey[$i][$j];
                    $purchaseData[$indexkey]['detailtype'] =   intval($arrDetailType[$i][$j]);
                    $purchaseData[$indexkey]['supplierkey'] =  $supplierKey;
                    $purchaseData[$indexkey]['itemkey'] = $arrItemKey[$i][$j]; 
                    $purchaseData[$indexkey]['priceinunit'] =  $this->formatNumber($this->unformatNumber($arrPrice[$i][$j]),2); 
                    $purchaseData[$indexkey]['taxdetail'] =   $this->formatNumber($this->unformatNumber($arrTax[$i][$j]),2); 
                    $purchaseData[$indexkey]['tax23percentagedetail'] =   $this->formatNumber($this->unformatNumber($arrTax23[$i][$j]),2); 
                    $purchaseData[$indexkey]['isreimburse'] = intval($arrIsReimburse[$i][$j]); 
                            
                }
            }
            
        } else {
            $id = $arr[0]['pkey'];
            $rsDetail = $this->getDetailById($id);
            $arrWOKey = array_column($rsDetail,'wokey');
            $arrDetailKey = array_column($rsDetail,'pkey');
            $rsItemDetail = $this->getItemDetail($arrDetailKey);  
                
             for($i=0;$i<count($rsItemDetail);$i++) {      

                $indexkey =  $rsItemDetail[$i]['detailtype'].'-'. $rsItemDetail[$i]['wodetailkey'];

                $purchaseData[$indexkey] = array();
                $purchaseData[$indexkey]['wodetailkey'] = $rsItemDetail[$i]['wodetailkey'];
                $purchaseData[$indexkey]['detailtype'] =  intval($rsItemDetail[$i]['detailtype']);
                $purchaseData[$indexkey]['supplierkey'] = $arr[0]['supplierkey']; 
                $purchaseData[$indexkey]['itemkey'] =  $rsItemDetail[$i]['itemkey']; 
                $purchaseData[$indexkey]['priceinunit'] = $this->formatNumber($rsItemDetail[$i]['priceinunit'],2);
                $purchaseData[$indexkey]['taxdetail'] = $this->formatNumber($rsItemDetail[$i]['taxdetail'],2);
                $purchaseData[$indexkey]['tax23percentagedetail'] = $this->formatNumber($rsItemDetail[$i]['tax23percentagedetail'],2);
                $purchaseData[$indexkey]['isreimburse'] = intval($rsItemDetail[$i]['isreimburse']); 
            }  

        }

        
        $rsItem = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name'), 
                                                          ' and '.$item->tableName.'.pkey in ('. $this->oDbCon->paramString(array_column($purchaseData,'itemkey'), ',') .')' );
        $rsItem = array_column($rsItem,null,'pkey');
        
        $rsSPK = $truckingServiceWorkOrder->searchDataRow(array($truckingServiceWorkOrder->tableName.'.pkey',$truckingServiceWorkOrder->tableName.'.supplierkey'), 
                                                          ' and '.$truckingServiceWorkOrder->tableName.'.pkey in ('. $this->oDbCon->paramString($arrWOKey, ',') .')' );
        $rsSPK = array_column($rsSPK,null,'pkey');
        
        $totalVehicleInvoiced = $truckingServiceWorkOrder->getCarDetail($arrWOKey); 
        $totalCostInvoiced = $truckingServiceWorkOrder->getCostDetail($arrWOKey); 

        // gabungin dulu jadi satu array data dr SPK
        $arrSPKCost = array();
        foreach($totalVehicleInvoiced as $row){
            $detailType = 1;
            
            $indexkey =  $detailType.'-'. $row['pkey'];
            $arrSPKCost[$indexkey] = array();
             
            $arrSPKCost[$indexkey]['wodetailkey'] = $row['pkey'];
            $arrSPKCost[$indexkey]['detailtype'] =  intval($detailType);
            $arrSPKCost[$indexkey]['supplierkey'] = $rsSPK[$row['refkey']]['supplierkey']; 
            $arrSPKCost[$indexkey]['itemkey'] =  $row['itemkey']; 
            $arrSPKCost[$indexkey]['priceinunit'] = $this->formatNumber($row['price'],2); 
            $arrSPKCost[$indexkey]['taxdetail'] = $this->formatNumber($row['taxpercentage'],2);
            $arrSPKCost[$indexkey]['tax23percentagedetail'] = $this->formatNumber($row['tax23percentage'],2);
            $arrSPKCost[$indexkey]['isreimburse'] = 0;
            
        }
        
        foreach($totalCostInvoiced as $row){
            
            $detailType = 2;
            $indexkey =  $detailType.'-'. $row['pkey'];
            $arrSPKCost[$indexkey] = array();
             
            $arrSPKCost[$indexkey]['wodetailkey'] = $row['pkey'];
            $arrSPKCost[$indexkey]['detailtype'] =  intval($detailType);
            //$arrSPKCost[$indexkey]['supplierkey'] = $rsSPK[$row['refkey']]['supplierkey']; 
            $arrSPKCost[$indexkey]['supplierkey'] = $row['supplierkey'];  
            $arrSPKCost[$indexkey]['itemkey'] =  $row['costkey']; 
            $arrSPKCost[$indexkey]['priceinunit'] = $this->formatNumber($row['amount'],2);
            $arrSPKCost[$indexkey]['taxdetail'] = $this->formatNumber($row['taxpercentage'],2);
            $arrSPKCost[$indexkey]['tax23percentagedetail'] = $this->formatNumber($row['tax23percentage'],2);
            $arrSPKCost[$indexkey]['isreimburse'] = intval($row['isreimburse']);
            
        }
        
        
        // baru compare per item
         
        foreach($purchaseData as $key=>$row){
            $spkCost = $arrSPKCost[$key];
             
            $purchaseItem = md5(json_encode($row)); 
            $spkItem = md5(json_encode($spkCost));
            
            if($purchaseItem != $spkItem) 
                array_push($errMsg, '<strong>' . $rsItem[$row['itemkey']]['name'] . '</strong>, ' . $this->errorMsg['truckingPurchase'][3]);
        
        }
          
        return $errMsg;
    }

 

    function getDetailJobOrder($pkey, $criteria = '') {

        $sql = 'select
               ' . $this->tableNameJobOrderDetail . '.*,
               ' . $this->tableJobOrder . '.code as socode,
               ' . $this->tableJobOrder . '.trdate,
               ' . $this->tableJobOrder . '.categorykey as socategorykey,
               ' . $this->tableJobOrder . '.donumber,
               ' . $this->tableJobOrder . '.shipmentnumber,
               ' . $this->tableJobOrder . '.poreference,
               ' . $this->tableJobOrder . '.routefrom,
               ' . $this->tableJobOrder . '.routeto
          from
              ' . $this->tableNameJobOrderDetail . ' 
              left join ' . $this->tableJobOrder . ' on ' . $this->tableNameJobOrderDetail . '.sokey = ' . $this->tableJobOrder . '.pkey 
          where  
              ' . $this->tableNameJobOrderDetail . '.refkey in ( ' . $this->oDbCon->paramString($pkey, ',') . ')';


        $sql .= $criteria;
        
        return $this->oDbCon->doQuery($sql);
    }

    function validateConfirm($rsHeader){
        
        // sementara khusus logol
        
        $id = $rsHeader[0]['pkey']; 
        $supplierkey =  $rsHeader[0]['supplierkey'];
		
		$rsPayment = (ADV_FINANCE && TEST_VOUCHER) ?  $this->getPaymentVoucherDetail($id,'',2) : $this->getPaymentMethodDetail($id);  
		
        $termOfPayment = new TermOfPayment();  
        $supplier = new Supplier();
        $item = new Item();
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        $truckingServiceOrder = new TruckingServiceOrder();
		$coaLink = new COALink();
		$warehouse = new Warehouse();
 
        // cek data dengan supplier dan code invoice yg sama
        $rsData = $this->searchDataRow( array($this->tableName . '.code'),
                                            ' and ' . $this->tableName . '.supplierkey = ' . $this->oDbCon->paramString($rsHeader[0]['supplierkey']) . '
															   and ' . $this->tableName . '.refinvoicecode = ' . $this->oDbCon->paramString($rsHeader[0]['refinvoicecode']) . '
															   and ' . $this->tableName . '.statuskey in (2,3) '
        );
 
        if (!empty($rsData)) {
            $rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $rsHeader[0]['refinvoicecode'] . ', ' . $this->errorMsg['invoice'][4]);
        }

        
        // validasi qty invoiced
        $rsDetail = $this->getDetailById($id);
        $arrWOKey =array_column($rsDetail,'wokey');
        
        // cari detail wo
        $totalVehicleInvoiced = $truckingServiceWorkOrder->getCarDetail($arrWOKey);
        $totalVehicleInvoiced = $this->reindexDetailCollections($totalVehicleInvoiced,'pkey'); // reindex dari detail, sudah pasti unik, gk perlu pkey spk lg
        
        $totalCostInvoiced = $truckingServiceWorkOrder->getCostDetail($arrWOKey);
        $totalCostInvoiced = $this->reindexDetailCollections($totalCostInvoiced,'pkey'); // reindex dari detail, sudah pasti unik, gk perlu pkey spk lg
         
        
        foreach($rsDetail as $detailRow){
            
            $rsItemDetail = $this->getItemDetail($detailRow['pkey']); 
            

            //cek Work Order statusnya harus closed
            $rsWO = $truckingServiceWorkOrder->getDataRowById($detailRow['wokey']);
            
            if($rsWO[0]['statuskey'] <> 3)  
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsWO[0]['code'].'</strong>. ' . $this->errorMsg[205]);
        
            $arrItemKey = array_column($rsItemDetail,'itemkey');
            $rsItem = $item->searchDataRow( array($item->tableName . '.pkey',$item->tableName . '.code',$item->tableName . '.name'),
                                            ' and ' . $item->tableName . '.pkey in (' . $this->oDbCon->paramString($arrItemKey,',').')'
                                          );
            $rsItem = array_column($rsItem,null,'pkey');
            
                
             foreach($rsItemDetail as $itemRow){
                 $WODetailKey = $itemRow['wodetailkey']; 
            
                 
                 if($itemRow['detailtype'] == 1) 
                     $rsWODetail = $totalVehicleInvoiced[$WODetailKey][0];
                 else 
                     $rsWODetail = $totalCostInvoiced[$WODetailKey][0];
                   
                 $totalQty = $rsWODetail['qty'];
                 $qtyInvoiced = $rsWODetail['qtyinvoiced'];
                 
                 if(  ($itemRow['qty'] + $qtyInvoiced) > $totalQty ){
                     $arrNotes = array();
                     
                     array_push($arrNotes,$rsItem[$itemRow['itemkey']]['name']);
                     if (!empty($itemRow['remark'])) array_push($arrNotes,$itemRow['remark']);
                     
                     $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. '.implode(', ',$arrNotes).', '.$this->errorMsg[508]);
                 }
                 
                 
             }
        }
         

        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;

        $totalPayment = 0;
        for ($i = 0; $i < count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $balance = $totalPayment - $rsHeader[0]['grandtotal'];

        if ($isCash) {
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if ($balance < ($thresholdDiscount * -1)) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[502]);
            } else if ($balance > $thresholdDiscount) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[509]);
            }
        }
		
		 if (USE_GL){  
            $arrCOA = array();
            array_push($arrCOA, 'outsourcecost'); 
            // kalo ad lebih dr 1 currency
            $currency = new Currency();
            $rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1);
            if (!count($rsCurrency) > 1)
                array_push($arrCOA, 'lossprofitrate');

            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }    
            
        
            if (ADV_FINANCE && TEST_VOUCHER){
                for($i=0;$i<count($rsPayment); $i++){ 
                    // cek kalo supplierkey sudah beda
                    if ($rsPayment[$i]['vouchersupplierkey'] <> $supplierkey)
                         $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][3]); 
                    else if ($rsPayment[$i]['voucheroutstanding'] < $rsPayment[$i]['amount'])
                        // cek kalo outstanding masih cukup
                         $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][4]); 
                    
                    else if ($rsPayment[$i]['voucherstatuskey'] <> TRANSACTION_STATUS['konfirmasi'])
                         $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][5]); 
                 
                }  
            }else{ 
                for($i=0;$i<count($rsPayment); $i++){ 
                    if ($rsPayment[$i]['amount'] > 0 ){ 
                        $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                        if (empty($rsCOA))	
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]); 
                    }
                }  
            }
        }

        
        $errMsg = $this->checkDetailWO(2, $rsHeader);
        foreach($errMsg as $row) 
            $this->addErrorLog(false, $row);
		
        $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);
        $arrSODetailKey = array_column($rsDetail, 'sokey');
        
		// diubah, cukup validasi yg JO yg gk boelh dibuat purchase kalo masih menunggu dan batal
        $rsServiceOrder = $truckingServiceOrder->searchDataRow(array($truckingServiceOrder->tableName . '.pkey', 
                                                                $truckingServiceOrder->tableName . '.code', 
                                                                $truckingServiceOrder->tableName . '.statuskey'), 
                                                                ' and ' . $truckingServiceOrder->tableName . '.statuskey in (1,7)  
																  and  ' . $truckingServiceOrder->tableName . '.pkey in (' . $this->oDbCon->paramString($arrSODetailKey, ',') . ') ');
        

        //Check JO dapat di peroses jika status < 6, atau tidak telah di faktur
        if (!empty($rsServiceOrder)) {
            $errMsg = array();
            foreach ($rsServiceOrder as $serviceOrder) {
                array_push($errMsg, '<b>' . $serviceOrder['code'] . '. </b>' . $this->errorMsg['truckingServiceOrder'][6]);
            }

            $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'. </strong>' . $this->errorMsg[201] . '<br>' . implode('<br>', $errMsg));

        }
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   			' . $this->tableNameDetail . '.*, 
                ' . $this->tableWorkOrder . '.code as wocode, 
                ' . $this->tableJobOrder . '.code as socode
			  from
			  	' . $this->tableNameDetail . ',
                ' . $this->tableJobOrder . ',
                ' . $this->tableWorkOrder . '
			  where
			  	' . $this->tableNameDetail . '.wokey = ' . $this->tableWorkOrder . '.pkey and
                ' . $this->tableNameDetail . '.sokey = ' . $this->tableJobOrder . '.pkey and
                ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }

    function confirmTrans($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];

        $supplier = new Supplier();
		$cashBank = new CashBank();

        $rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;

        $rsPayment = array();

        if ($isCash) {
			 if (ADV_FINANCE && TEST_VOUCHER){ 
					$rsPayment = $this->getPaymentVoucherDetail($id,'',2);
					$rsAPKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
					// update outstanding voucher  
					foreach($rsPayment as $voucherlist){ 
						$cashBank->insertTransaction(
							array('refkey' => $voucherlist['cashbankvoucherkey'],
								  'reftablekey' => $rsAPKey['key'],
								  'reftranskey' => $rsHeader[0]['pkey'],
								  'refcode' => $rsHeader[0]['code'],
								  'refdate' => $rsHeader[0]['trdate'],
								  'amount' => $voucherlist['amount'],
						),true
						); 
					}
				}else{  
                         $rsPayment = $this->getPaymentMethodDetail($id);
                         if (ADV_FINANCE) {
                             //$cashMovement = new CashMovement();  

                             $cashBank = new CashBank();
                             for ($i = 0; $i < count($rsPayment); $i++) {
                                 if ($rsPayment[$i]['amount'] == 0) continue;

                                 if (USE_GL) {
                                     $rsPaymentCOA = $coaLink->getCOALink('payment', $warehouse->tableName, $rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']);
                                     $coakey = $rsPaymentCOA[0]['coakey'];
                                 } else {
                                     $coakey = $rsPayment[$i]['paymentkey'];
                                 }

                                 /*if(!empty($rsPaymentCOA))
                                      $cashMovement->updateCashMovement($id, $rsPaymentCOA[0]['coakey'],$rsPayment[$i]['amount'],$this->tableName, $rsHeader[0]['warehousekey'], $notecash,$rsHeader[0]['trdate']);
                                     */

                                 $arrItemName =  array_column($rsDetail, 'itemname');

                                 $rsCashBank = $cashBank->addCashBank($rsHeader, $this->tableName, array('supplierkey' => $rsHeader[0]['supplierkey'], 'coakey' => $coakey, 'desc' => $note, 'amount' => -$rsPayment[$i]['amount']));
                                 $rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey'];
                             }
                         }
					 }
        } else {
            //update AP
            
            $ap = new AP();
            $rsSODetail = $this->getDetailJobOrder($id);
            $arrSOCode = array_column($rsSODetail, 'socode');
            $arrSOCode = implode(", ",$arrSOCode);
            $arrParam = array();

            $rsAPKey = $ap->getTableKeyAndObj($this->tableName, array('key'));
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
            $arrParam['hidRefKey'] = $id;
            $arrParam['hidRefHeaderKey'] = $id;
            $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
            $arrParam['hidRefCode2'] =  $arrSOCode;
            $arrParam['hidRefTable'] = $rsAPKey['key'];
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['amount'] = abs($rsHeader[0]['grandtotal']) - $rsHeader[0]['tax23value'];
            $arrParam['trDesc'] = '';
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P' . $rsTOP[0]['duedays'] . 'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');
            $arrParam['createdBy'] = 0;
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['islinked'] = 1;
            $arrParam['overwriteGL'] = 1;
            $arrParam['selAPType'] = 1;
            $arrParam['hidRefInvoiceCode'] = $rsHeader[0]['refinvoicecode'];

            $arrayToJs = $ap->addData($arrParam);
             
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
        }
        
        
        $this->updateGL($rsHeader,$rsPayment);
        
    }

    function cancelTrans($rsHeader, $copy)
    {

        $id = $rsHeader[0]['pkey'];

        $ap = new AP();
		
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName, array('key'));
        $rsAP = $ap->searchData('', '', true, ' and ' . $ap->tableName . '.reftabletype = ' . $this->oDbCon->paramString($rsAPKey['key']) . ' and ' . $ap->tableName . '.refkey = ' . $this->oDbCon->paramString($id) . ' and ' . $ap->tableName . '.statuskey = 1');
        for ($i = 0; $i < count($rsAP); $i++) {
            $arrayToJs = $ap->changeStatus($rsAP[$i]['pkey'], 4, '', false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' .  $arrayToJs[0]['message']);
        }
		
		if( $this->isActiveModule('CashBank') ){
			$cashBank = new CashBank();
			if (ADV_FINANCE && TEST_VOUCHER){ 
				$cashBank->removeTransaction($id,$rsAPKey['key']);
			}else{ 
				$cashBank->cancelCashBank($rsHeader,$this->tableName);
			}
		}

        if ($copy)
            $this->copyDataOnCancel($rsHeader[0]['pkey']);
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    }

    function afterStatusChanged($rsHeader){
        //update qtyinvoice 
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);

        // if (!$rsHeader[0]['isdownpayment']){   
        foreach ($rsDetail as $invoiceDetail) {
            if (empty($invoiceDetail['wokey']))    continue; 
            $truckingServiceWorkOrder->updateQtyInvoiced($rsHeader[0]['pkey'] ,$invoiceDetail['wokey'], $rsHeader[0]['supplierkey']); // update jml yg sudah diinvoiced 
        }
    }

    function reCountGrandTotal($arrParam) {
  
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();

        $usePPNDetail = $this->loadSetting('usePurchasePPNDetail');
		$usePPNDetail = ($usePPNDetail == 1) ? true : false;
		
        $grandtotal = 0;
        $subtotal = 0;
        $totalTax23 = 0;

        $isPriceIncludeTax =  $arrParam['chkIncludeTax'];
        //$taxValue = $this->unFormatNumber($arrParam['taxValue']);  
        $finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']);
        $finalDiscountType = $arrParam['selFinalDiscountType'];
        $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);
        $arrWorkOrderKey = $arrParam['hidWOKey'];
        $supplierkey = $arrParam['hidSupplierKey'];
        $arrItemKey = $arrParam['hidItemKey'];
        $arrSalesOrderDownpayment = $arrParam['salesOrderDownpayment'];
        $arrAmount = $arrParam['amount'];
        $stampFee =  $this->unFormatNumber($arrParam['stampFee']);
        $arrPick = $arrParam['chkPick'];
        $arrSOKey = $arrParam['hidSOKey'];

        $detailSubtotalTaxed = 0;
        $detailBeforeTaxTotal = 0;
        $detailTaxTotal = 0;
        $arrDetailInvoice = array();
        
        // jgn ambil semua, karena blm tentu semua nya di pilih oleh user, 
        // pilih berdasarkan WO Key saja
        // $rsPriceCol = $truckingServiceWorkOrder->searchAvailableItemForPurchase($arrSOKey,$supplierkey);
 
        $rsPriceCol = $truckingServiceWorkOrder->searchAvailableItemForPurchase($arrSOKey,$supplierkey, ' and '.$truckingServiceWorkOrder->tableName.'.pkey in ('.$this->oDbCon->paramString($arrWorkOrderKey,',').') ');
        $rsPriceCol = $this->reindexDetailCollections($rsPriceCol,'pkey'); // reindex per SPK
       
        for ($i = 0; $i < count($arrPick); $i++) {
            $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
            $arrSalesOrderDownpayment[$i] = $this->unFormatNumber($arrSalesOrderDownpayment[$i]);
            
            if ((empty($arrWorkOrderKey[$i]) && empty($arrItemKey[$i])) ||  empty($arrAmount[$i]) || empty($arrPick[$i]))
                continue;
 
            if (empty($arrWorkOrderKey[$i]) || $arrParam['chkDownpayment']) { 
                $subtotal += $arrAmount[$i];
            } else {
                $wokey = $arrWorkOrderKey[$i];
           
                $rsPrice = $rsPriceCol[$wokey][0]['detail'];  //pasti 1 spk per detail
                $rsPrice = array_column($rsPrice,null,'joinkey'); 
                
                
                $arrChkService = $arrParam['chkService'][$i];
                $arrChkTax = $arrParam['chkIsTax23'][$i];
                $arrQtyService = $arrParam['qtyDetail'][$i];
                $arrItemDetailKey = $arrParam['hidItemDetailKey'][$i];
                $arrWODetailKey = $arrParam['hidWODetailKey'][$i];
                $tax23PercentageDetail = $arrParam['tax23PercentageDetail'][$i];
                $arrTaxDetail = $arrParam['taxDetail'][$i];
                $arrDetailType = $arrParam['detailType'][$i]; 
                $arrIsPriceIncludeTaxDetail = $arrParam['chkIncludeTaxDetail'][$i];

                $workOrderSubtotal = 0; // nilai total murni dr selling JO
                $detailSubtotal = 0;
                
                $arrDetailInvoiceItem = array();

                for ($j = 0; $j < count($arrItemDetailKey); $j++) {
                     
                    $joinkey = $arrDetailType[$j] .'-'. $arrWODetailKey[$j] .'-'. $arrItemDetailKey[$j];
                    
                    $arrDefaultPrice = array_column($rsPrice,'priceinunit','joinkey');
                     
                    $priceInUnit = (isset($arrDefaultPrice[$joinkey])) ? $arrDefaultPrice[$joinkey] : 0;
                
                    $total =  $this->unFormatNumber($arrQtyService[$j]) * $priceInUnit;

                    $workOrderSubtotal += $total;

                    // gk boleh pake chkService karena gk kepecah
                    // gk kepecah karena kita gk assign chkService di arrItem
                    //  empty($arrChkService[$j]) || 
                    
                    
                    // gk boleh continue, nanti pas recountnya error kalo ad yg sudah full invoiced (jml iterasinya tdk sama), jd kita koosngin saja itemkeynya
//                    if (empty($arrWODetailKey[$j]) || empty($arrItemDetailKey[$j]) || empty($total))
//                        continue;

                    if (empty($arrWODetailKey[$j]) || empty($arrItemDetailKey[$j]) || empty($total)){
                             $arrDetailInvoiceItem[$j]['hidItemDetailKey'] = '';
                    }else{
                             $arrDetailInvoiceItem[$j]['hidItemDetailKey'] = $arrItemDetailKey[$j];
                    }
                        
                    
                    // jika informasi PPN di detail, sekalian hitung ulang nilai per detailnya
                    if ($usePPNDetail) {
                        
                        $taxDetail =  $this->unFormatNumber($arrTaxDetail[$j]);
                        $isPriceIncludeTaxDetail = $arrIsPriceIncludeTaxDetail[$j];

                        $detailBeforeTax = 0;
                        $arrDetailInvoiceItem[$j]['joinkey'] = $joinkey;
                        $arrDetailInvoiceItem[$j]['priceInUnitDetail'] = $priceInUnit;
                        $arrDetailInvoiceItem[$j]['subtotalDetail'] = $total;
                        $this->recalculateTaxAndValue($detailBeforeTax, $total, $taxDetailValue, $taxDetail, $isPriceIncludeTaxDetail);
                        $arrDetailInvoiceItem[$j]['beforeTaxDetail'] = $detailBeforeTax;
                        $arrDetailInvoiceItem[$j]['afterTaxDetail'] = $total;
                        $arrDetailInvoiceItem[$j]['taxValueDetail'] = $taxDetailValue;
                        $detailTaxTotal += $taxDetailValue;
                        $detailBeforeTaxTotal += $detailBeforeTax;
                    }

                    //$this->setLog('total '.$total,true);
                    $detailSubtotal += $total;

                    // ini utk PPH 23 
                    // if (!empty($arrChkTax[$j]))
                    //     $detailSubtotalTaxed += ($usePPNDetail)  ? $detailBeforeTax : $total;
                    $tax23 = $tax23PercentageDetail[$j] * $detailBeforeTax / 100;
                    $totalTax23 += $tax23 ;
                }

                if ($usePPNDetail)
                    $arrDetailInvoice[$i]['detailValue'] = $arrDetailInvoiceItem;


                $detailSubtotal -= $arrSalesOrderDownpayment[$i];
                if ($detailSubtotal < 0) $detailSubtotal = 0;

                $subtotal += $detailSubtotal;

                // khusus menghitung subtotal level JO
                $arrDetailInvoice[$i]['detailSubtotal'] = $workOrderSubtotal;
                $arrDetailInvoice[$i]['amount'] = $workOrderSubtotal - $arrSalesOrderDownpayment[$i];
                if ($arrDetailInvoice[$i]['amount'] < 0) $arrDetailInvoice[$i]['amount'] = 0;
            }
        }

        if ($usePPNDetail) {
            $beforeTaxTotal = $detailBeforeTaxTotal;
            $grandtotal = $beforeTaxTotal + $detailTaxTotal;
        } else {

            if ($finalDiscount != 0) {
                if ($finalDiscountType == 2)
                    $finalDiscount = $finalDiscount / 100 * $subtotal;
            }

            $beforeTaxTotal = $subtotal - $finalDiscount;
            $grandtotal = $beforeTaxTotal;

            $this->recalculateTaxAndValue($beforeTaxTotal, $grandtotal, $taxValue, $taxPercentage, $isPriceIncludeTax);
        }

        $grandtotal += $stampFee;

        $balance = 0;
        $totalPayment = 0;

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPayment']);
        if ($rsTOP[0]['duedays'] == 0) {
            $payment = $arrParam['paymentMethodValue'];
            for ($i = 0; $i < count($payment); $i++) {
                $totalPayment += $this->unFormatNumber($payment[$i]);
            }
        }

        $totalDowpayment = 0;
        $downpayment = $arrParam['downpaymentAmount'];
        $downpaymentKey = $arrParam['hidDownpaymentKey'];
        for ($i = 0; $i < count($downpayment); $i++) {
            if (empty($downpaymentKey[$i]))
                continue;
            $totalDowpayment += $this->unFormatNumber($downpayment[$i]);
        }
        $tax23Percentage = $arrParam['tax23Percentage'];

        $outstanding = $grandtotal - $totalDowpayment;
        $balance = $totalPayment - $outstanding;

        $ppnType = $this->loadSetting('companyPPNType');
        if ($ppnType == 1)  $balance +=  ($usePPNDetail) ? $detailTaxTotal : $taxValue; // plus karena nilai minus

        // hitung PPH 23  
        // if ($isPriceIncludeTax)
        //     $detailSubtotalTaxed = $detailSubtotalTaxed - (round(($taxPercentage / (100 + $taxPercentage)) * $detailSubtotalTaxed));
        // $tax23 = $tax23Percentage * $detailSubtotalTaxed / 100;


        $reCountResult = array();
        $reCountResult['subtotal'] = $subtotal;
        $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
        $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
        $reCountResult['grandTotal'] = $grandtotal;
        $reCountResult['totalPayment'] = $totalPayment;
        $reCountResult['totalDownpayment'] = $totalDowpayment;
        $reCountResult['outstanding'] = $outstanding;
        $reCountResult['balance'] = $balance;
        $reCountResult['tax23Value'] = $totalTax23;
        $reCountResult['taxValue'] = ($usePPNDetail) ? $detailTaxTotal : $taxValue;
        $reCountResult['recountDetail'] = ($usePPNDetail) ? $arrDetailInvoice : array();

//        $this->setLog($reCountResult,true);
        return $reCountResult;
    }

    function normalizeParameter($arrParam, $trim = false){
        
        $truckingServiceOrder = new TruckingServiceOrder(); 
        
        $usePPNDetail = $this->loadSetting('usePPNDetail');

        $itemkey = count($arrParam['hidItemDetailKey']);

        for ($i = 0; $i < $itemkey; $i++) {
            if ($arrParam['chkService'][$i]) continue;
            $arrParam['hidItemDetailKey'][$i] = '';
        }

        $details = array();
        array_push($details, $this->arrItem);

        $arrParam = $this->prepareMultiLevelDetail($arrParam, $details);
        
        $reCountResult = $this->reCountGrandTotal($arrParam);

        $arrParam['subtotal'] = $reCountResult['subtotal'];
        $arrParam['grandTotal'] = $reCountResult['grandTotal'];
        $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
        $arrParam['ispriceincludetax'] = $reCountResult['isPriceIncludeTax'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
        $arrParam['totalDownpayment'] = $reCountResult['totalDownpayment'];
        $arrParam['outstanding'] = $reCountResult['outstanding'];
        $arrParam['balance'] = $reCountResult['balance'];
        $arrParam['tax23Value'] = $reCountResult['tax23Value'];
        $arrParam['taxValue'] = $reCountResult['taxValue'];

        $recountDetail = $reCountResult['recountDetail'];
        for ($i = 0; $i < count($recountDetail); $i++) {
            $arrParam['amount'][$i] = $recountDetail[$i]['amount'];
            $arrParam['detailSubtotal'][$i] = $recountDetail[$i]['detailSubtotal'];
            $arrParam['detailTotal'][$i] = $recountDetail[$i]['detailSubtotal'];
        }

        
        // cache JO 
        $rsJO = $truckingServiceOrder->searchDataRow(array($truckingServiceOrder->tableName.'.code'),
                                                     ' and '.$truckingServiceOrder->tableName.'.pkey in ('.$this->oDbCon->paramString($arrParam['hidDetailJobOrderKey'],',').')');
        
        $arrParam ['salesOrderCodeCache'] = implode(', ',array_column($rsJO,'code'));
            
        // model logol
        if ($usePPNDetail) {
            $arrParam['chkIncludeTax'] = 0;

            for ($i = 0; $i < count($recountDetail); $i++) {
                $arrDetailValue = $recountDetail[$i]['detailValue'];
                $countItemDetail = count($arrDetailValue);
                 
                // kalo gk ad di recount, set qty jd 0 agar kehapus
                
                for ($j = 0; $j < $countItemDetail; $j++) { 
                    $arrParam['hidItemDetailKey'][$i][$j] = $arrDetailValue[$j]['hidItemDetailKey'];
                    $arrParam['priceInUnitDetail'][$i][$j] = $arrDetailValue[$j]['priceInUnitDetail'];
                    $arrParam['beforeTaxDetail'][$i][$j] = $arrDetailValue[$j]['beforeTaxDetail'];
                    $arrParam['subtotalDetail'][$i][$j] = $arrDetailValue[$j]['subtotalDetail'];
                    $arrParam['taxValueDetail'][$i][$j] = $arrDetailValue[$j]['taxValueDetail'];
                    $arrParam['afterTaxDetail'][$i][$j] = $arrDetailValue[$j]['afterTaxDetail'];
                }
                  
            }
        }

                 
        // $this->setLog($arrParam, true);

        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }
    
    function updateGL($rs,$rsPayment){ 
        
        if (!USE_GL) return;
        
        $truckingServiceOrder = new TruckingServiceOrder(); 
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();  
        $generalJournal = new GeneralJournal();
        $supplier = new Supplier();
        $item = new Item();
        $cost = new Service(TRUCKING_SERVICE,1); 
		$cashBank = new CashBank();
		$chartOfAccount = new ChartOfAccount();
		
        $warehousekey = $rs[0]['warehousekey'];  
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName,array('key'));
         
		$rate = 1; // nanti harus based on form kalo ada
		
		$temp = -1;  
        	
		if(ADV_FINANCE && TEST_VOUCHER) 
			$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2);
		 

		$termOfPayment = new TermOfPayment();
		$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 

		
		
        $rsItemDetail = array();
        $rsDetail = $this->getDetailById($rs[0]['pkey']);  
        foreach($rsDetail as $detailRow){ 
             $rsItemDetailTemp = $this->getItemDetail($detailRow['pkey']); 
             
             foreach($rsItemDetailTemp as $itemDetailkey=>$itemDetailRow)
                 $rsItemDetailTemp[$itemDetailkey]['sokey'] = $detailRow['sokey']; //utk ambil jenis kategori JO nanti
                 
             $rsItemDetail = array_merge($rsItemDetail, $rsItemDetailTemp);
        }
          
        
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
		$arr['refCode'] = $rs[0]['code'];
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
         
        //desc  
        $rsJO = $truckingServiceOrder->searchDataRow(array($truckingServiceOrder->tableName.'.pkey',$truckingServiceOrder->tableName.'.code',$truckingServiceOrder->tableName.'.categorykey'),
                                                     ' and '.$truckingServiceOrder->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'sokey'),',').')');
        $arrJOCode = array_column($rsJO,'code');
        $arrJOCol =  array_column($rsJO,null,'pkey');
            
        $desc = array();
        array_push($desc,implode(', ',$arrJOCode));
        array_push($desc,$rsSupplier[0]['name']);
		$arr['trDesc'] = implode(chr(13),$desc);   
        
        // cost outsource 
	    
	    $rsCOA = $coaLink->getCOALink ('outsourcecost', $warehouse->tableName, $warehousekey);  
        $outsourceCOAkey = $rsCOA[0]['coakey'];
        
        $rsCOA = $coaLink->getCOALink ('taxin', $warehouse->tableName, $warehousekey);  
        $taxcoakey = $rsCOA[0]['coakey']; 
        
        $tax23Amount = $rs[0]['tax23value'];
         
		$totalAP = 0;
        for($i=0;$i<count($rsItemDetail);$i++){
            
            $costkey = $rsItemDetail[$i]['itemkey'];
            
            if($rsItemDetail[$i]['detailtype'] == 1){ 
               $coakey = $outsourceCOAkey;
            }else{ 
                $JOCategoryKey = $arrJOCol[$rsItemDetail[$i]['sokey']]['categorykey'];
                $coakey = $cost->getCostCOAKeyByJobCategory($costkey,$JOCategoryKey,$warehousekey); //(!empty($arrItemCostCOAKey[$costkey])) ? $arrItemCostCOAKey[$costkey] : $coakey;
            }
              
            
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = $rsItemDetail[$i]['beforetaxdetailvalue']; 
            $arr['credit'][$temp] = 0; 
            
            $temp++;
            $arr['hidCOAKey'][$temp] = $taxcoakey;
            $arr['debit'][$temp] =  $rsItemDetail[$i]['taxdetailvalue']; 
            $arr['credit'][$temp] = 0;  
            
			$totalAP +=  $rsItemDetail[$i]['beforetaxdetailvalue'] + $rsItemDetail[$i]['taxdetailvalue'];
        }  
		
		if ($isCash) {
             	 for($i=0;$i<count($rsPayment); $i++){ 

					if(ADV_FINANCE && TEST_VOUCHER){ 
		//				$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2); // harusnya udah gk perlu
						$rsCashBank = $cashBank->getDataRowById($rsPayment[$i]['cashbankvoucherkey']);
						$rsCOA = $chartOfAccount->getDataRowById($rsCashBank[0]['coakey']);

						$paymentcoakey = $rsCOA[0]['countercoakey'];
					}else{
						$rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
						$paymentcoakey = $rsCOA[0]['coakey'];
					}

					 $paymentAmount = ($rsPayment[$i]['amount'] - $tax23Amount) * $rate; 

					 $temp++;
					 $arr['hidCOAKey'][$temp] = $paymentcoakey;
					 $arr['debit'][$temp] = 0; 
					 $arr['credit'][$temp] = $paymentAmount;  
					 $totalPaymentAmount += $paymentAmount;
				}
		 }else{  
			   $apCOAKey =  $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
			    
			    //akun hutang vendor 
				$temp++; 
				$arr['hidCOAKey'][$temp] = $apCOAKey;
				$arr['debit'][$temp] = 0; 
				$arr['credit'][$temp] = $totalAP - $tax23Amount; 
		 }
         
        
         //PPH
        if($tax23Amount != 0){
            $tax = new Tax();
            $rsCOA = $tax->getPPhCOA(array(), $rs[0]['warehousekey'],false); // semetnara kosongin tipe tax, harusnya by default ambil pph23
            $rsCOACols = array_column($rsCOA,null,'pkey');
            
            // sementara baru support pph 23
            $temp++; 
            $arr['hidCOAKey'][$temp] = $rsCOACols[0]['coakey'];
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] =$tax23Amount; 
        }
        
        
		$arrayToJs = $generalJournal->addData($arr); 

		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
     
    }
	
	function searchDataItemDetail($pkey,$criteria= ''){
		
		$sql = 'select 
					'.$this->tableName.'.pkey,
					'.$this->tableName.'.code,
					'.$this->tableName.'.supplierkey,
					'.$this->tableNameItemDetail.'.pkey as itemdetailkey,
					'.$this->tableNameItemDetail.'.wodetailkey,
					'.$this->tableNameItemDetail.'.detailtype,
					'.$this->tableNameItemDetail.'.itemkey,
					'.$this->tableNameItemDetail.'.priceinunit,
					'.$this->tableNameItemDetail.'.aftertaxdetailvalue
				from 
					'.$this->tableName.',
					'.$this->tableNameDetail.',
					'.$this->tableNameItemDetail.'
				where 
					'.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
					'.$this->tableNameDetail.'.pkey = '.$this->tableNameItemDetail.'.refkey and
					'.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($pkey,',').')
				';
		 
		
		if(!empty($criteria))
			$sql .= ' '.$criteria;
		
		$rs = $this->oDbCon->doQuery($sql);
		return $rs; 
	}
	
	
}

?>