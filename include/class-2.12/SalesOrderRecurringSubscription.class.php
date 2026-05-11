<?php

class SalesOrderRecurringSubscription extends BaseClass 
{

   function __construct()
   {

      parent::__construct();

      $this->tableName = 'sales_order_recurring_subscription_header';
      $this->tableNameDetail = 'sales_order_recurring_subscription_detail';
      $this->tableCustomer = 'customer';
      $this->tableCity = 'city';
      $this->tableEmployee = 'employee';
      $this->tableWarehouse = 'warehouse';
      $this->tableStatus = 'sales_order_subscription_status';
      $this->tableMovement = 'item_movement';
      $this->tableHistory = 'history';
      $this->tablePayment = 'sales_order_payment';
      $this->tableVoucherDetail = 'sales_order_voucher';
      $this->tableVoucher = 'voucher';
      $this->tableVoucherTransaction = 'voucher_transaction';
      $this->tableItem = 'item';
      $this->tableItemCategory = 'item_category';
      $this->tableItemUnit = 'item_unit';
      $this->tableItemImage = 'item_image';
      $this->tableBrand = 'brand';
      $this->tableItemCategory = 'item_category';
      $this->tableCartTemp = 'cart_temp';
      $this->tablePaymentConfirmation = 'payment_confirmation';
      $this->tableShipment = 'shipment';
      $this->tableShipmentService = 'shipment_detail';
      $this->tableTermOfPayment = 'term_of_payment';
      $this->tableARStatus = 'ar_status';
      $this->tableRecurringPeriod = 'recurring_period';
      $this->isTransaction = true;
      $this->newLoad = true;

      $this->securityObject = 'SalesOrderRecurringSubscription';

      $this->arrDataDetail = array();
      $this->arrDataDetail['pkey'] = array('hidDetailKey');
      $this->arrDataDetail['refkey'] = array('pkey', 'ref');
      $this->arrDataDetail['refmarketplacekey'] = array('refMarketplaceKey');
      $this->arrDataDetail['itemkey'] = array('hidItemKey');
      $this->arrDataDetail['qty'] = array('qty', 'number');
      $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit', 'number');
      $this->arrDataDetail['unitkey'] = array('selUnit');
      $this->arrDataDetail['priceinunit'] = array('priceInUnit', 'number');
      $this->arrDataDetail['priceinbaseunit'] = array('priceInBaseUnit', 'number');
      $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier', 'number');
      $this->arrDataDetail['discounttype'] = array('selDiscountType');
      $this->arrDataDetail['discount'] = array('discountValueInUnit', 'number');
      $this->arrDataDetail['total'] = array('detailSubtotal', 'number');
      $this->arrDataDetail['costinbaseunit'] = array('cogs', 'number');
      $this->arrDataDetail['profit'] = array('detailProfit', 'number');
      $this->arrDataDetail['deliveredqtyinbaseunit'] = array('deliveredQtyInBaseUnit', 'number');
      $this->arrDataDetail['itemtype'] = array('itemType');
      $this->arrDataDetail['weight'] = array('itemWeight', 'number');
      $this->arrDataDetail['trdesc'] = array('trDetailDesc');

      $arrDetails = array();
      array_push($arrDetails, array('dataset' => $this->arrDataDetail));


      $this->arrData = array();
      $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
      $this->arrData['code'] = array('code');
      $this->arrData['customcodekey'] = array('selCustomCode');
      $this->arrData['marketplaceorderid'] = array('marketplaceOrderId');
      $this->arrData['marketplaceinvoiceurl'] = array('marketplaceInvoiceURL');
      $this->arrData['trdate'] = array('trDate', 'date');
      $this->arrData['warehousekey'] = array('selWarehouseKey');
      $this->arrData['customerkey'] = array('hidCustomerKey');
      $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
      $this->arrData['trdesc'] = array('trDesc');
      $this->arrData['subtotal'] = array('subtotal', 'number');
      $this->arrData['finaldiscounttype'] = array('selFinalDiscountType', 'number');
      $this->arrData['finaldiscount'] = array('finalDiscount', 'number');
      $this->arrData['finaldiscounttype2'] = array('selFinalDiscountType2', 'number');
      $this->arrData['finaldiscount2'] = array('finalDiscount2', 'number');
      $this->arrData['beforetaxtotal'] = array('beforeTaxTotal', 'number');
      $this->arrData['ispriceincludetax'] = array('isPriceIncludeTax');
      $this->arrData['taxpercentage'] = array('taxPercentage', 'number');
      $this->arrData['taxvalue'] = array('taxValue', 'number');
      $this->arrData['shipmentfee'] = array('shipmentFee', 'number');
      $this->arrData['etccost'] = array('etcCost', 'number');
      $this->arrData['grandtotal'] = array('grandTotal', 'number');
      $this->arrData['totalpayment'] = array('totalPayment', 'number');
      $this->arrData['balance'] = array('balance', 'number');
      $this->arrData['isfulldeliver'] = array('chkIsFullDeliver');
      $this->arrData['profit'] = array('profit', 'number');
      $this->arrData['statuskey'] = array('selStatus');
      $this->arrData['saleskey'] = array('hidSalesKey');
      $this->arrData['recipientname'] = array('recipientName');
      $this->arrData['recipientphone'] = array('recipientPhone');
      $this->arrData['recipientemail'] = array('recipientEmail');
      $this->arrData['recipientaddress'] = array('recipientAddress');
      $this->arrData['recipientzipcode'] = array('recipientZipcode');
      $this->arrData['recipientcitykey'] = array('hidRecipientCityKey');
      $this->arrData['recipientmapaddress'] = array('mapAddress');
      $this->arrData['recipientlatlng'] = array('hidLatLng');
      $this->arrData['pointvalue'] = array('pointValue', 'number');
      $this->arrData['useinsurance'] = array('useInsurance');
      $this->arrData['shipmentkey'] = array('selShipment');
      $this->arrData['shipmentservicekey'] = array('selShipmentService');
      $this->arrData['isdropship'] = array('chkIsDropship');
      $this->arrData['dropshipername'] = array('dropshiperName');
      $this->arrData['dropshiperphone'] = array('dropshiperPhone');
      $this->arrData['dropshiperaddress'] = array('dropshiperAddress');
      $this->arrData['marketplacekey'] = array('marketplaceKey');
      $this->arrData['refcode'] = array('refCode');
      $this->arrData['totalweight'] = array('totalWeight', 'number');
      $this->arrData['voucherkey'] = array('hidTransVoucherKey');
      $this->arrData['totalCOGS'] = array('totalCOGS'); 

      $this->arrData['recurringperiodkey'] = array('selRecurringPeriod');
      $this->arrData['lastrecurringdate'] = array('lastRecurringDate', 'date');
      $this->arrData['nextrecurringdate'] = array('nextRecurringDate', 'date');

      $this->arrDataListAvailableColumn = array();
      array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'refCode', 'title' => 'reference', 'dbfield' => 'refcode', 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 100, 'align' => 'center', 'format' => 'date'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 100));
      array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 200));
      array_push($this->arrDataListAvailableColumn, array('code' => 'total', 'title' => 'total', 'dbfield' => 'grandtotal', 'default' => true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
      array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));
      array_push($this->arrDataListAvailableColumn, array('code' => 'desc', 'title' => 'note', 'dbfield' => 'trdesc', 'width' => 200));
      array_push($this->arrDataListAvailableColumn, array('code' => 'salesman', 'title' => 'salesman', 'dbfield' => 'salesname', 'width' => 150));
      array_push($this->arrDataListAvailableColumn, array('code' => 'recurringPeriod', 'title' => 'recurringPeriod', 'dbfield' => 'recurringperiodname', 'default' => true, 'width' => 110));
      array_push($this->arrDataListAvailableColumn, array('code' => 'lastRecurrinDate', 'title' => 'lastRecurringDate', 'dbfield' => 'lastrecurringdate', 'default' => true, 'width' => 130, 'align' => 'center', 'format' => 'date'));

      $this->arrSearchColumn = array();
      array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
      array_push($this->arrSearchColumn, array('Kode Ref', $this->tableName . '.refcode'));
      array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
      array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
      array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer . '.name'));
      array_push($this->arrSearchColumn, array('Sales', $this->tableEmployee . '.name'));
      array_push($this->arrSearchColumn, array('Total', $this->tableName . '.grandtotal'));
      array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.trdesc'));
      array_push($this->arrSearchColumn, array('Terakhir Recurring', $this->tableName . '.lastrecurringdate'));
      array_push($this->arrSearchColumn, array('Terakhir Recurring', $this->tableRecurringPeriod . '.name'));

	  $this->invoiceInterval = 10;
	   
      $this->includeClassDependencies(
         array(
            'SalesOrder.class.php',
            'TermOfPayment.class.php',
            'Warehouse.class.php',
            'PaymentMethod.class.php',
            'Shipment.class.php',
            'City.class.php',
            'Customer.class.php',
            'Item.class.php',
            'ItemMovement.class.php',
            'ItemUnit.class.php',
            'Brand.class.php',
            'Category.class.php',
            'ItemCategory.class.php',
            'ItemCondition.class.php',
            'RecurringPeriod.class.php',
            'SalesOrderRecurringSubscriptionTerminate.class.php'
         )
      );

      $this->overwriteConfig();

   }

   function getQuery()
   {

      $sql = '
            SELECT ' . $this->tableName . '.* ,
               ' . $this->tableCustomer . '.code as customercode,
               ' . $this->tableCustomer . '.name as customername,
               ' . $this->tableWarehouse . '.code as warehousecode,
               ' . $this->tableWarehouse . '.name as warehousename,
               ' . $this->tableStatus . '.status as statusname ,
               ' . $this->tableEmployee . '.name as salesname , 
               ' . $this->tableRecurringPeriod . '.pkey as recurringperiodkey,
               ' . $this->tableRecurringPeriod . '.name as recurringperiodname,
               ' . $this->tableRecurringPeriod . '.numberofperiod as recurringnumberofperiod,
               ' . $this->tableRecurringPeriod . '.timeperiodkey as recurringtimeperiodkey 
            FROM 
                ' . $this->tableStatus . ', 
                ' . $this->tableCustomer . ' left join ' . $this->tableCity . ' on  
                     ' . $this->tableCustomer . '.citykey = ' . $this->tableCity . '.pkey,
                ' . $this->tableWarehouse . ',
                ' . $this->tableName . ' 
                    left join ' . $this->tableEmployee . ' on ' . $this->tableName . '.saleskey = ' . $this->tableEmployee . '.pkey 
                    left join ' . $this->tableRecurringPeriod . ' on ' . $this->tableName . '.recurringperiodkey = ' . $this->tableRecurringPeriod . '.pkey

            WHERE ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey and
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                     ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey 
        ' . $this->criteria;


      $sql .= $this->getWarehouseCriteria();
//      $sql .= $this->getCompanyCriteria(); 
	   
      return $sql;

   }

   function getDetailWithRelatedInformation($pkey, $criteria = '', $orderby = '')
   {
      $sql = 'select
                ' . $this->tableNameDetail . '.*, 
                ' . $this->tableItem . '.name as itemname, 
                ' . $this->tableItem . '.code as itemcode, 
                ' . $this->tableItem . '.brandkey, 
                ' . $this->tableItem . '.gramasi, 
                ' . $this->tableBrand . '.name as brandname ,
                ' . $this->tableItem . '.deftransunitkey,
                ' . $this->tableItemCategory . '.pkey as itemcategorykey,
                ' . $this->tableItemCategory . '.name as itemcategoryname,
                ' . $this->tableItemUnit . '.code as unitcode,
                ' . $this->tableItemUnit . '.name as unitname,
                 baseunit.name as baseunitname
              from
                ' . $this->tableNameDetail . ',
                ' . $this->tableItemUnit . ',
                ' . $this->tableItemCategory . ',
                ' . $this->tableItemUnit . ' baseunit,
                ' . $this->tableItem . '
                    left join ' . $this->tableBrand . ' on 	' . $this->tableItem . '.brandkey = ' . $this->tableBrand . '.pkey 
              where
                ' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey and
                ' . $this->tableNameDetail . '.unitkey = ' . $this->tableItemUnit . '.pkey and
                ' . $this->tableItem . '.baseunitkey = baseunit.pkey and
                ' . $this->tableItem . '.categorykey = ' . $this->tableItemCategory . '.pkey and
		' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

      $sql .= $criteria;

      $sql .= ' ' . $orderby;

      return $this->oDbCon->doQuery($sql);
   }

   function validateForm($arr, $pkey = '')
   {

      $item = new Item();

      $arrayToJs = parent::validateForm($arr, $pkey);

      $customerkey = $arr['hidCustomerKey'];
      $arrItemkey = $arr['hidItemKey'];
      $pkey = $arr['hidId'];
      $selRecurringPeriod = $arr['selRecurringPeriod'];   


      if (empty($customerkey)) {
         $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
      }


      if (empty($arrItemkey))
         $this->addErrorList($arrayToJs, false, $this->errorMsg[501]);

      //update periode
	   // harusnya di normalize
	   
//      if(!empty($pkey)){
//
//         $recurringPeriod = new RecurringPeriod();
//
//         $rsData = $this->getDataRowById($pkey);
//      
//         if($rsData[0]['statuskey'] == TRANSACTION_STATUS['konfirmasi'] ){
//
//			$lastRecurringData = ($this->isEmptyDate($rsData[0]['lastrecurringdate'])) ? $rsData[0]['trdate'] : $rsData[0]['lastrecurringdate'];
//
//			 
//			$this->setLog($rsData[0]['lastrecurringdate'],true);
//			$this->setLog($lastRecurringData,true);
//             
//            //cek apakah sama recurring period nya sama yang di database ?
//            if ($rsData['recurringperiodkey'] <> $selRecurringPeriod) {
//
//               $rsRecurringPeriod = $recurringPeriod->getDataRowById($selRecurringPeriod);
//
//               $newRecurringDate = $this->calculateDatePeriod($lastRecurringData, $rsRecurringPeriod[0]['numberofperiod'], $rsRecurringPeriod[0]['timeperiodkey']);
//
//               $arrData = array();
//               $arrData['nextrecurringdate'] = $newRecurringDate;
//               $this->updateRecurringPeriod($rsData[0]['pkey'], $arrData);
//
//            }
//
//         }
//      
//      }

      return $arrayToJs;
   }


   function validateConfirm($rsHeader)
   {

      $item = new Item();

      $id = $rsHeader[0]['pkey'];

      $warehouse = $rsHeader[0]['warehousekey'];
      $customer = $rsHeader[0]['customerkey']; 

      $rsDetail = $this->getDetailWithRelatedInformation($id);
 

      if(empty($customer))   
         $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'.</strong> ' . $this->errorMsg['customer'][1]);
   

      if(empty($warehouse)) 
         $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'.</strong> ' . $this->errorMsg['warehouse'][1]); 

      if(empty($rsDetail)){
         $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'.</strong> ' . $this->errorMsg[501]);
      } else {

         $errMsg = array();

		  
		$rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name',$item->tableName.'.itemtype'),
										 ' and ' . $item->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'itemkey'),',').')');
		$rsItemCol = array_column($rsItemCol,null,'pkey');

         for($i=0; $i<count($rsDetail); $i++){

            if(!empty($rsDetail[$i]['itemkey'])){
//               $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);

				$rsItem = $rsItemCol[$rsDetail[$i]['itemkey']];
				
               if ($this->unFormatNumber($rsDetail[$i]['qty']) <= 0) {
                  $msg = '<strong>' . $rsItem['name'] . '</strong>' . '. ' . $this->errorMsg[510];
                  array_push($errMsg, $msg);
               }

               if($this->unFormatNumber($rsDetail[$i]['priceinunit']) <= 0)
               {
                  $msg = '<strong>' .  $rsItem['name'] . '</strong>' . '. ' . $this->errorMsg[511];
                  array_push($errMsg, $msg);

               }

				if($rsItem['itemtype'] != SERVICE){
				   $conv = $item->getConvMultiplier($rsDetail[$i]['itemkey'], $rsDetail[$i]['unitkey']);
				   if (empty($conv)) { 
					  $msg = '<strong>' . $rsItem['name'] . '</strong>' . '. ' . $this->errorMsg['itemUnitConversion'][3];
					  array_push($errMsg, $msg); 
				   }
				}

            }
         
         }

         if(!empty($errMsg))
         {
            $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'</strong> <br>' . implode('<br>', $errMsg));
         }

      }

	  // kalo sudah terminated, gk bisa diapa2in lg
	  // harusnya masuk validateBackConfirm
//      $statusKey = $rsHeader[0]['statuskey'];
//      if(($statusKey == 3)) 
//         $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'</strong>. ' . $this->errorMsg['salesOrderRecurringSubscription'][4]);
   

   }

   function confirmTrans($rsHeader)
   {  
      $id = $rsHeader[0]['pkey'];

      //add sales order
      $this->addSalesOrder($id,true);
      
   }

   function validateBackConfirm($rsHeader)
   {
      $statusKey = $rsHeader[0]['statuskey'];

      //validasi kalau udah di terminate tidak bisa ubah ke aktif melalui modul recurring
      if(($statusKey == SUBSCRIPTION_STATUS['terminated'])){
         $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'</strong>. ' . $this->errorMsg[201] . '<br>' . $this->errorMsg['salesOrderRecurringSubscription'][3]);
      }

   }

   function validateClose($rsHeader, $autoChangeStatus = true)
   {
	   
		if($autoChangeStatus)  return;
	   
	   // harus dr modul terminated

      $id = $rsHeader[0]['pkey']; 
      $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['salesOrderRecurringSubscription'][4]);
      
   }


   function validateCancel($rsHeader, $autoChangeStatus=false)
   {

	  if($autoChangeStatus)  return;
	   
      $pkey = $rsHeader[0]['pkey'];

      $salesOrder = new SalesOrder();

      $rsSalesOrder = $salesOrder->searchData('', '', true, ' and ' . $salesOrder->tableName.'.refsubscriptionkey = '. $this->oDbCon->paramString($pkey) .' and '. $salesOrder->tableName.'.statuskey in (2,3) ');

      if(!empty($rsSalesOrder)) 
         $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['salesOrderRecurringSubscription'][4]);
   
	  // kalo sdh terminated, gk boleh dicancel
      if ($rsHeader[0]['statuskey'] == SUBSCRIPTION_STATUS['terminated'])
         $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['salesOrderRecurringSubscription'][2]);
      

   }

   function closeTrans($rsHeader) 
   {
      $pkey = $rsHeader[0]['pkey'];

      $salesOrder = new SalesOrder();

      $rsSalesOrder = $salesOrder->searchData('', '', true, ' and ' . $salesOrder->tableName.'.refsubscriptionkey = '. $this->oDbCon->paramString($pkey) .' and '. $salesOrder->tableName.'.statuskey = 1 ');
	  for($i=0; $i <count($rsSalesOrder); $i++) 
		$salesOrder->changeStatus($rsSalesOrder[$i]['pkey'], 4, '', false, true);

   }

   function cancelTrans($rsHeader, $copy)
   {
      $id = $rsHeader[0]['pkey'];

      $salesOrder = new SalesOrder();

      //ambil data SO yang statusnya masih menunggu
      $rsSalesOrder = $salesOrder->searchData('', '', true, ' and ' . $salesOrder->tableName.'.refsubscriptionkey = '. $this->oDbCon->paramString($id) . 
                           ' and ' . $salesOrder->tableName.'.statuskey = 1 ' );

      //ubah status menjadi batal, kalau ada yang menunggu
      for($i = 0; $i < count($rsSalesOrder); $i++) 
            $salesOrder->changeStatus($rsSalesOrder[$i]['pkey'], 4, '', false, true); 
	   
      if ($copy)
         $this->copyDataOnCancel($id);

   }
 
 
   function reCountSubtotal($arrParam)
   {

      $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;
    
      $subtotal = 0;
      $grandtotal = 0;

      $arrItemKey = $arrParam['hidItemKey']; 
      $taxValue = $this->unFormatNumber($arrParam['taxValue']);
      $finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']);
      $finalDiscountType = $arrParam['selFinalDiscountType'];

      $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);
      $shipmentFee = $this->unFormatNumber($arrParam['shipmentFee']);
      $etcCost = $this->unFormatNumber($arrParam['etcCost']); 

      $arrQty = $arrParam['qty'];
      $arrPriceinunit = $arrParam['priceInUnit'];
      $arrDiscountValueInUnit = $arrParam['discountValueInUnit'];
      $arrDiscountType = $arrParam['selDiscountType'];
      $arrTransUnitKey = $arrParam['selUnit'];

      $arrItemDetail = array(); 
      $item = new Item(); 
      $totalGramasi = 0;
      $totalCOGS = 0;

      for ($i = 0; $i < count($arrItemKey); $i++) {

         if (empty($arrItemKey[$i]))
            continue;

         $rsItem = $item->getDataRowById($arrItemKey[$i]);

         $itemkey = $arrItemKey[$i];
         $transactionUnitKey = $arrTransUnitKey[$i];
         $baseunitkey = $rsItem[0]['baseunitkey'];
         $qty = $this->unFormatNumber($arrQty[$i]);
         $conversionMultiplier = $item->getConvMultiplier($itemkey, $transactionUnitKey, $baseunitkey);
		  
		 // buat jasa 
		 if($conversionMultiplier == 0) $conversionMultiplier = 1; 
		  
		  
         $qtyinbaseunit = $qty * $conversionMultiplier;
         $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
         $discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
         $discountType = $this->unFormatNumber($arrDiscountType[$i]);

         $discount = $this->getDiscountValue($priceInUnit, $discount, $discountType);

         $gramasi = $rsItem[0]['gramasi'];
         if ($rsItem[0]['weightunitkey'] == UNIT['kg'])
            $gramasi *= 1000;

         $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
         $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
         $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit;
         $arrItemDetail[$i]['priceInBaseUnit'] = $priceInUnit / $conversionMultiplier;
         $arrItemDetail[$i]['weight'] = $gramasi;

         $detailSubtotal = $qty * ($priceInUnit - $discount);
         $arrItemDetail[$i]['unitDiscountValue'] = $discount;
         $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;
         $arrItemDetail[$i]['itemType'] = $rsItem[0]['itemtype'];

         $subtotal += $detailSubtotal;
         $totalGramasi += ($qty * $gramasi);
      }

      $grandtotal = $subtotal;

      $finalDiscount = $this->getDiscountValue($grandtotal, $finalDiscount, $finalDiscountType);

      if (isset($arrParam['finalDiscount2'])) {
         $finalDiscount2 = $this->unFormatNumber($arrParam['finalDiscount2']);
         $finalDiscountType2 = $arrParam['selFinalDiscountType2'];

         $finalDiscount += $this->getDiscountValue(($grandtotal - $finalDiscount), $finalDiscount2, $finalDiscountType2);
      }
 
 
      $totalFinalDiscountAndPointValue = $finalDiscount;
 
      $beforeTaxTotal = $subtotal - $totalFinalDiscountAndPointValue;
      $grandtotal = $beforeTaxTotal;

      if ($isPriceIncludeTax == false) {
         $taxValue = $beforeTaxTotal * $taxPercentage / 100;
         $taxValue = round($taxValue); // kalo ad koma, nilainya gantung di AR nanti
         $grandtotal += $taxValue;
      } else {
         $taxValue = ($taxPercentage / (100 + $taxPercentage)) * $grandtotal;
         $beforeTaxTotal = $grandtotal - $taxValue;
      }

      $grandtotal += $shipmentFee - $voucherShipmentValue + $etcCost;
 
      $balance =  $grandtotal;

      $balance = $totalPayment - $grandtotal;
      $reCountResult = array();
      $reCountResult['subtotal'] = $subtotal;
      $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
      $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
      $reCountResult['taxValue'] = $taxValue;
      $reCountResult['grandtotal'] = $grandtotal; 
      $reCountResult['balance'] = $balance;  
      $reCountResult['detailCOGS'] = $arrItemDetail; 
      $reCountResult['totalWeight'] = ceil($totalGramasi);

      return $reCountResult;

   }

   function normalizeParameter($arrParam, $trim = false)
   {
 
      $arrItemkey = $arrParam['hidItemKey']; 
      $arrQty = $arrParam['qty'];
      $arrPriceinunit = $arrParam['priceInUnit'];
      $arrUnitKey = $arrParam['selUnit'];

 
      for ($i = 0; $i < count($arrItemkey); $i++) {
         $arrParam['discountValueInUnit'][$i] = (!isset($arrParam['discountValueInUnit'][$i])) ? 0 : $arrParam['discountValueInUnit'][$i];
         $arrParam['selDiscountType'][$i] = (!isset($arrParam['selDiscountType'][$i])) ? 1 : $arrParam['selDiscountType'][$i];
         $arrParam['refMarketplaceKey'][$i] = (!isset($arrParam['refMarketplaceKey'][$i])) ? '' : $arrParam['refMarketplaceKey'][$i];
      }



      $reCountResult = $this->reCountSubtotal($arrParam); 
      $arrParam['detailCOGS'] = $reCountResult['detailCOGS'];
 
      $arrParam['subtotal'] = $reCountResult['subtotal']; 
      $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
      $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
      $arrParam['taxValue'] = $reCountResult['taxValue'];
      $arrParam['grandtotal'] = $reCountResult['grandtotal'];
      $arrParam['totalPayment'] = $reCountResult['totalPayment'];
      $arrParam['balance'] = $reCountResult['balance'];
      $arrParam['totalWeight'] = $reCountResult['totalWeight'];
	   
      for ($i = 0; $i < count($arrItemkey); $i++) {

         $qtyinbaseunit = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];
         $arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit;
         $arrParam['unitConvMultiplier'][$i] = $arrParam['detailCOGS'][$i]['unitConvMultiplier']; 
         $arrParam['priceInBaseUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInBaseUnit'];
         $arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal'];
         $arrParam['itemType'][$i] = $arrParam['detailCOGS'][$i]['itemType'];
         $arrParam['itemWeight'][$i] = $arrParam['detailCOGS'][$i]['weight'];

         if (!isset($arrParam['trDetailDesc'][$i]))
            $arrParam['trDetailDesc'][$i] = '';
 
      }
   
      $arrParam = parent::normalizeParameter($arrParam, true);

      return $arrParam;
   }

   function updateRecurringPeriod($pkey, $arrParam)
   {
      $sql = '
            update
               ' . $this->tableName . '
            set 
               nextrecurringdate = ' . $this->oDbCon->paramString($arrParam['nextrecurringdate']) . '
            where
               pkey = ' . $this->oDbCon->paramString($pkey)
      ;

      $this->oDbCon->execute($sql);
   }

	function afterAddDataOnCopy($pkey, $oldkey){    
		
		$rsHeader = $this->getDataRowById($pkey);
        $arrParam = array();
        $arrParam['pkey'] = $rsHeader[0]['pkey'];
        $arrParam['oldRs'] = '';  
 
			
        //reset ulang nextrecurring sama lastrecurring
		$sql = 'update '.$this->tableName.' set lastrecurringdate = \'0000-00-00\', nextrecurringdate =  \'0000-00-00\' 
				where '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($arrParam['pkey']);
									 
		$this->oDbCon->execute($sql);
			
        $this->afterUpdateData($arrParam,INSERT_DATA);  
		
    }
	 
   function calculateDatePeriod($currentDate, $numberOfPeriod, $timePeriodKey, $adj = ''){
      //params
      //tanggal awal
      //jumlah periode
      //period waktu => day, week, month, annual
	   
	  // ini bukan bugs, jika 
	  // sales order subscription d konfirmasi, SO terbentuk (baru satu SO).
	  // SO dicancel, sbuscription akan mencatat nextInvoiceDatenya sdh nambah ke periode ke 2, 
	  // karena idealnya, SO langsung terbentuk pada saat pertama kali subscriptino di konfirmasi
	  // dan kalo nextInvoiceDatenya masih balik ke tgl awal, maka pada saat cron, tdk akan terbaca selamanya (karena sudah lewat tgl nya)
 
      $nextDate = new DateTime($currentDate);

	  $timePeriod = $this->getRepeatPeriod($timePeriodKey)[0]['mysqlinterval'];
	  $nextDate->modify('+' . $numberOfPeriod . ' ' . $timePeriod);
	     
	   // untuk update descripsi periode penagihan
	   if(!empty($adj)) $nextDate->modify($adj); 
	   
      return $nextDate->format('Y-m-d');

   }


   function addSalesOrder($pkey, $firstTransaction = false, $invoiceDate = '' )
   {
      $salesOrder = new SalesOrder();
      $recurringPeriod = new RecurringPeriod();
      $customer = new Customer();

      $rsData = $this->getDataRowById($pkey);
      $rsDetail = $this->getDetailWithRelatedInformation($pkey);

	  // buat detail desc nanti
//      $rsRecurringPeriod = $recurringPeriod->getDataRowById($rsData[0]['recurringperiodkey']);
//	  $nextRecurringDate = $this->calculateDatePeriod($rsData[0]['trdate'], $rsRecurringPeriod[0]['numberofperiod'], $rsRecurringPeriod[0]['timeperiodkey']);

      $rsCustomer = $customer->getDataRowById($rsData[0]['customerkey']);

	  $trdate = ($firstTransaction) ? $rsData[0]['trdate'] : $rsData[0]['nextrecurringdate'];
	 
	  if(empty($invoiceDate))
		  $invoiceDate = $trdate;
	   
	    
	  // cari dulu utk inv di tanggal yg sama sdh ad blm
	  // sementara per tgl dulu, nanti baru dipikirin perperiode validasinya
	  $rsExistingSalesOrder = $salesOrder->searchDataRow(array('pkey'), ' and '.$salesOrder->tableName.'.statuskey in (1,2,3)
	  																	 and ' .$salesOrder->tableName.'.trdate = '.$this->oDbCon->paramString($invoiceDate).'
																		 and '.$salesOrder->tableName.'.refsubscriptionkey = ' .$this->oDbCon->paramString($pkey));
	 
	   if (!empty($rsExistingSalesOrder)) return;
	   
	   
      $arr = array();
      $arr['code'] = 'xxxx';
      $arr['trDate'] =  $this->formatDBDate($invoiceDate, 'd / m / Y');
      $arr['recurringDate'] =  $this->formatDBDate($trdate, 'd / m / Y');
      $arr['selCustomCode'] = $rsData[0]['customecodekey'];
      $arr['marketplaceOrderId'] = $rsData[0]['marketpalceorderid'];
      $arr['marketplaceInvoiceURL'] = $rsData[0]['marketpalceinvoiceurl'];
      $arr['selWarehouseKey'] = $rsData[0]['warehousekey'];
      $arr['hidCustomerKey'] = $rsData[0]['customerkey'];
      $arr['selTermOfPaymentKey'] = $rsCustomer[0]['termofpaymentkey'];
      $arr['trDesc'] = $rsData[0]['trDesc'];
      $arr['subtotal'] = $rsData[0]['subtotal'];
      $arr['selFinalDiscountType'] = $rsData[0]['finaldiscounttype'];
      $arr['finalDiscount'] = $rsData[0]['finalDiscount'];
      $arr['selFinalDiscountType2'] = $rsData[0]['finaldiscounttype2'];
      $arr['finalDiscount2'] = $rsData[0]['finaldiscount2'];
      $arr['beforeTaxTotal'] = $rsData[0]['beforetaxtotal'];
      $arr['isPriceIncludeTax'] = $rsData[0]['ispriceincludetax'];
      $arr['taxPercentage'] = $rsData[0]['taxpercentage'];
      $arr['taxValue'] = $rsData[0]['taxvalue'];
      $arr['shipmentFee'] = $rsData[0]['shipmentfee'];
      $arr['etcCost'] = $rsData[0]['etccost'];
      $arr['grandtotal'] = $rsData[0]['grandtotal'];
      $arr['totalPayment'] = $rsData[0]['totalpayment'];
      $arr['balance'] = $rsData[0]['balance'];
      $arr['chkIsFullDeliver'] = $rsData[0]['isfulldeliver'];
      $arr['profit'] = $rsData[0]['profit'];
      $arr['selStatus'] = $rsData[0]['statuskey'];
      $arr['hidSalesKey'] = $rsData[0]['saleskey'];
      $arr['recipientName'] = $rsData[0]['recipientname'];
      $arr['recipientPhone'] = $rsData[0]['recipientphone'];
      $arr['recipientEmail'] = $rsData[0]['recipientemail'];
      $arr['recipientAddress'] = $rsData[0]['recipientaddress'];
      $arr['recipientZipcode'] = $rsData[0]['recipientzipcode'];
      $arr['hidRecipientCityKey'] = $rsData[0]['recipientcitykey'];
      $arr['mapAddress'] = $rsData[0]['recipientmapaddress'];
      $arr['hidLatLng'] = $rsData[0]['recipientlatlng'];
      $arr['pointValue'] = $rsData[0]['pointvalue'];
      $arr['useInsurance'] = $rsData[0]['useinsurance'];
      $arr['selShipment'] = $rsData[0]['shipmentkey'];
      $arr['selShipmentService'] = $rsData[0]['shipmentservicekey'];
      $arr['chkIsDropship'] = $rsData[0]['isdropship'];
      $arr['dropshiperName'] = $rsData[0]['dropshipername'];
      $arr['dropshiperPhone'] = $rsData[0]['dropshiperphone'];
      $arr['dropshiperAddress'] = $rsData[0]['dropshiperaddress'];
      $arr['marketplaceKey'] = $rsData[0]['marketplacekey'];
      $arr['refCode'] = $rsData[0]['refcode'];
      $arr['totalWeight'] = $rsData[0]['totalweight'];
      $arr['hidTransVoucherKey'] = $rsData[0]['voucherke'];
      $arr['totalCOGS'] = $rsData[0]['totalcogs'];
      $arr['refSubscriptionKey'] = $rsData[0]['pkey'];
      $arr['vaNumber'] = $rsCustomer[0]['virtualaccount'];

      $arr['hidDetailKey'] = array();
      $arrRefKey = array();
      $arrRefMarketPlaceKey = array();
      $arrItemKey = array();
      $arrQty = array();
      $arrQtyInBaseUnit = array();
      $arrSelUnit = array();
      $arrPriceInUnit = array();
      $arrUnitConvMultipler = array();
      $arrSelDiscountType = array();
      $arrDisCountValueInUnit = array();
      $arrDetailSubTotal = array(); 
      $arrCOGS = array(); 
      $arrDetailProfit = array();  
      $arrItemType = array(); 
      $arrItemWeight = array(); 
      $arrDetailDesc = array();
      $arrItemName = array();

      for($i=0; $i < count($rsDetail); $i++) {
         
         array_push($arr['hidDetailKey'], 0);

         array_push($arrRefkey, $rsDetail[$i]['pkey']);
         array_push($arrRefMarketPlaceKet, $rsDetail[$i]['refmarketplacekey']);
         array_push($arrItemKey, $rsDetail[$i]['itemkey']);
         array_push($arrItemName, $rsDetail[$i]['itemname']);
         array_push($arrQty, $rsDetail[$i]['qty']);
         array_push($arrQtyInBaseUnit, $rsDetail[$i]['qtyinbaseunit']);
         array_push($arrSelUnit, $rsDetail[$i]['unitkey']);
         array_push($arrPriceInUnit, $rsDetail[$i]['priceinunit']);
         array_push($arrUnitConvMultipler, $rsDetail[$i]['unitconvmultipler']);
         array_push($arrSelDiscountType, $rsDetail[$i]['discounttype']);
         array_push($arrDisCountValueInUnit, $rsDetail[$i]['discount']);
         array_push($arrDetailSubTotal, $rsDetail[$i]['total']);
         array_push($arrCOGS, $rsDetail[$i]['costnbaseunit']);
         array_push($arrDetailProfit, $rsDetail[$i]['profit']); 
         array_push($arrItemType, $rsDetail[$i]['itemtype']);
         array_push($arrItemWeight, $rsDetail[$i]['weight']);
         array_push($arrItemWeight, $rsDetail[$i]['weight']);
		  
		 // update keterangan periode
		 $desc = $this->updateVariable($rsDetail[$i]['trdesc'], array('rsHeader' => $rsData)); 
         array_push($arrDetailDesc,$desc);

      }

      $arr['refkey'] = $arrRefkey;
      $arr['refMarketplaceKey'] = $arrRefMarketPlaceKey;
      $arr['hidItemKey'] = $arrItemKey;
      $arr['itemName'] = $arrItemName;
      $arr['qty'] = $arrQty;
      $arr['qtyInBaseUnit'] = $arrQtyInBaseUnit;
      $arr['selUnit'] = $arrSelUnit;
      $arr['selUnit'] = $arrSelUnit;
      $arr['priceInUnit'] = $arrPriceInUnit;
      $arr['unitConvMultipler'] = $arrUnitConvMultipler;
      $arr['selDiscountType'] = $arrSelDiscountType;
      $arr['discountValueInUnit'] = $arrDisCountValueInUnit;
      $arr['detailSubtotal'] = $arrDetailSubTotal;
      $arr['cogs'] = $arrCOGS; 
      $arr['itemType'] = $arrItemType;
      $arr['itemWeight'] = $arrItemWeight;
      $arr['trDetailDesc'] = $arrDetailDesc;

      $arrayToJs = $salesOrder->addData($arr);

      if (!$arrayToJs[0]['valid']){
         throw new Exception('<strong>' . $rsData[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
      } 
   }

	function updateVariable($label,$arrData){
		
		$recurringPeriod = new RecurringPeriod();
		
		$rsHeader = $arrData['rsHeader'];
		$periodeDate = (!$this->isEmptyDate($rsHeader[0]['nextrecurringdate'])) ? $rsHeader[0]['nextrecurringdate'] : $rsHeader[0]['trdate']; 
		      
		$rsRecurringPeriod = $recurringPeriod->getDataRowById($rsHeader[0]['recurringperiodkey']);
		
		// tgl mulai per tgl invoice
		
		$startPeriod = $this->toLocalDate($this->formatDBDate($periodeDate, 'd F Y')); 
		$endPeriod = $this->toLocalDate($this->formatDBDate($this->calculateDatePeriod($periodeDate, $rsRecurringPeriod[0]['numberofperiod'], $rsRecurringPeriod[0]['timeperiodkey'], '-1 day'), 'd F Y')); 
							 
		$label = str_replace('{{NEXT_MONTH_FROM}}', $startPeriod ,$label);
		$label = str_replace('{{TO_MONTH_FROM}}', $endPeriod ,$label);
		
		return $label;
	}
	
   function updateLastRecurringDate($pkey){

      $recurringPeriod = new RecurringPeriod();
      $currentDateTime = new DateTime('now');

	   
      //get data recurring
      $rsData = $this->getDataRowById($pkey);
      
	   
	  // ambil SO terakhir
	  $salesOrder = new SalesOrder();
	  $rsSalesOrder = $salesOrder->searchDataRow(array($salesOrder->tableName.'.pkey',$salesOrder->tableName.'.trdate',$salesOrder->tableName.'.recurringdate'),
												 ' and '.$salesOrder->tableName.'.refsubscriptionkey = ' . $this->oDbCon->paramString($pkey).
												 ' and '.$salesOrder->tableName.'.statuskey in (2,3) ',
												 ' order by '.$salesOrder->tableName.'.trdate desc limit 1'
												);
	     
	  if(!empty($rsSalesOrder)){
//		  $lastInvoiceDate = $rsSalesOrder[0]['trdate']; // jgn ambil dr tgl invoice, karena tgl inv bisa lebih kecil dr tgl jatuh tempo

		  $lastInvoiceDate = $rsSalesOrder[0]['recurringdate']; 
		  $lastInvoiceDateForCalculate =  $rsSalesOrder[0]['recurringdate'];
		  
		  //get data recurring period
		  $rsRecurringPeriod = $recurringPeriod->getDataRowById($rsData[0]['recurringperiodkey']);

		  //get next recurring period
		  $nextRecurringDate = $this->calculateDatePeriod($lastInvoiceDateForCalculate, $rsRecurringPeriod[0]['numberofperiod'], $rsRecurringPeriod[0]['timeperiodkey']);

	  }else{ 
		  $lastInvoiceDate = '0000-00-00';
		  $nextRecurringDate = $rsData[0]['trdate'];
	  }
	      
      $sql = '
            update
               '. $this->tableName .'
            set 
               lastrecurringdate = '. $this->oDbCon->paramString($lastInvoiceDate)  .',
               nextrecurringdate = '. $this->oDbCon->paramString($nextRecurringDate) .'
            where
               pkey = '. $this->oDbCon->paramString($pkey)
            ;
	    

      $this->oDbCon->execute($sql);

   }
 
}

?>
