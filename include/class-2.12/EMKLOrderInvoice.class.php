<?php

class EMKLOrderInvoice extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'emkl_order_invoice_header';
    $this->tableNameDetail = 'emkl_order_invoice_detail';
    $this->tableNameItemDetail = 'emkl_order_invoice_item_detail';
    $this->tableCustomer = 'customer';
    $this->tableItem = 'item';
    $this->tableStatus = 'transaction_status';
    $this->tablePaymentMethod = 'payment_method';
    $this->tableWarehouse = 'warehouse';   
    $this->tableEmployee = 'employee';   
    $this->tableCurrency = 'currency';   
    $this->tablePayment= 'emkl_order_invoice_payment';  
    $this->tableSalesOrderHeader = 'emkl_job_order_header';  
    $this->tableSalesOrderDetail = 'emkl_job_order_detail';  
    $this->tableConsignee= 'consignee'; 
    $this->tableDownpaymentDetail = 'emkl_order_invoice_downpayment';
    $this->tableDownpayment = 'customer_downpayment';
    $this->tablePartialInvoice = 'emkl_job_order_header_partial_invoice'; 
    $this->isTransaction = true;
    $this->securityObject = 'EMKLOrderInvoice';
    $this->tableCOA  = 'chart_of_account';
    $this->tableAR = 'ar';
    $this->tableARStatus = 'ar_status';
        
    //$this->testnewCode = true;
//    $this->autoPrintURL = 'print/truckingServiceOrderInvoice';
        
    $this->arrItem = array();  
    $this->arrItem['pkey'] = array('hidDetailItemKey');
    $this->arrItem['refkey'] = array('hidDetailKey','ref');  
    $this->arrItem['refheaderkey'] = array('pkey','ref');  
    $this->arrItem['itemkey'] = array('hidItemDetailKey', array('mandatory'=>true)); 
    $this->arrItem['refsodetailkey'] = array('hidRefSODetailKey'); 
    $this->arrItem['refsoheaderkey'] = array('hidRefSOHeaderKey'); 
    $this->arrItem['containerkey'] = array('hidContainerDetailKey'); 
    $this->arrItem['istax23'] = array('chkIsTax23'); 
    $this->arrItem['aliasname'] = array('itemNameAliasDetail'); 
    $this->arrItem['qtyinbaseunit'] = array('qtyDetail','number', array('mandatory'=>true));
    $this->arrItem['priceinunit'] = array('priceInUnitDetail','number', array('mandatory'=>true)); 
    $this->arrItem['total'] = array('subtotalDetail','number');  
    $this->arrItem['rate'] = array('detailRate','number'); 
    $this->arrItem['currencykey'] = array('hidCurrencyKey'); 
    $this->arrItem['taxdetail'] = array('taxDetail','number'); 
    $this->arrItem['taxdetailvalue'] = array('taxValueDetail','number'); 
    $this->arrItem['beforetaxdetailvalue'] = array('beforeTaxDetail','number'); 
    $this->arrItem['aftertaxdetailvalue'] = array('afterTaxDetail','number'); 
    $this->arrItem['ispriceincludetax'] = array('chkIncludeTaxDetail'); 
    $this->arrItem['description'] = array('descriptionDetail'); 
    $this->arrItem['isreimburse'] = array('chkIsReimburse', 'number');
    $this->arrItem['beforetaxdetailvaluefortax'] = array('beforeTaxDetailValueForTax');
    $this->arrItem['taxdetailvaluefortax'] = array('taxDetailValueForTax');
    $this->arrItem['unitkey'] = array('selDetailItemUnit'); 


    $this->arrDataDetail = array(); 
    $this->arrDataDetail['pkey'] = array('hidDetailKey', array('dataDetail' => array('dataset' => $this->arrItem, 'tableName' => $this->tableNameItemDetail)));
    $this->arrDataDetail['refkey'] = array('pkey','ref'); 
    $this->arrDataDetail['salesorderkey'] = array('hidSalesOrderKey');
    $this->arrDataDetail['refsalesorderheaderkey'] = array('hidSalesOrderHeaderKey');
    $this->arrDataDetail['invoicetype'] = array('selInvoiceType', array('mandatory'=>true)); 
    //$this->arrDataDetail['salesordergrandtotal'] = array('salesOrderSubtotal','number');
    //$this->arrDataDetail['salesordertotalinvoiced'] = array('salesOrderDownpayment','number');
    $this->arrDataDetail['itemkey'] = array('hidItemKey');
    $this->arrDataDetail['invoicekey'] = array('hidInvoiceKey');
    $this->arrDataDetail['description'] = array('detailNote');
    $this->arrDataDetail['amount'] = array('amount','number');
        
    $this->arrPaymentDetail = array(); 
    $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
    $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
    $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
    $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 
    
    $arrDownpaymentDetail = array(); 
    $arrDownpaymentDetail['pkey'] = array('hidDetailDownpaymentKey');
    $arrDownpaymentDetail['refkey'] = array('pkey', 'ref');
    $arrDownpaymentDetail['amount'] = array('downpaymentAmount',array('datatype' => 'number','mandatory'=>true));
    $arrDownpaymentDetail['downpaymentkey'] = array('hidDownpaymentKey',array('mandatory'=>true));

    $arrDetails = array(); 
    array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));    
    array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
    array_push($arrDetails, array('dataset' => $arrDownpaymentDetail, 'tableName' => $this->tableDownpaymentDetail));    
        
    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
    $this->arrData['code'] = array('code');
    $this->arrData['codectr'] = array('codectr');
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['customerkey'] = array('hidCustomerKey');
    $this->arrData['refinvoicekey'] = array('hidRefInvoiceKey');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['companybankkey'] = array('selBank');
    $this->arrData['trdesc'] = array('trDesc');
    $this->arrData['statuskey'] = array('selStatus');
    $this->arrData['grandtotal'] = array('grandTotal','number');
    //$this->arrData['customertaxdate'] = array('trDateCustomerTax','date');
    //$this->arrData['customertaxid'] = array('customerTaxId');
    $this->arrData['termofpaymentkey'] = array('selTermOfPayment'); 
    $this->arrData['customcodekey'] = array('selCustomCode'); 
    $this->arrData['subtotal'] = array('subtotal','number'); 
    $this->arrData['beforetaxtotal'] = array('beforeTaxTotal','number'); 
    $this->arrData['ispriceincludetax'] = array('isPriceIncludeTax'); 
    $this->arrData['totalpayment'] = array('totalPayment','number'); 
    $this->arrData['balance'] = array('balance','number'); 
    $this->arrData['finaldiscounttype'] = array('selFinalDiscountType'); 
    $this->arrData['finaldiscount'] = array('finalDiscount','number'); 
    $this->arrData['finaldiscounttype'] = array('selFinalDiscountType'); 
    $this->arrData['ispriceincludetax'] = array('isPriceIncludeTax'); 
    $this->arrData['taxpercentage'] = array('taxPercentage','number'); 
    $this->arrData['taxvalue'] = array('taxValue','number'); 
    $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
    $this->arrData['tax23percentage'] = array('tax23Percentage','number');    
    $this->arrData['tax23value'] = array('tax23Value','number');    
    $this->arrData['usetax23'] = array('chkTax23'); 
    $this->arrData['donumber'] = array('doNumber');
    $this->arrData['shipmentnumber'] = array('shipmentNumber'); 
    $this->arrData['totaldownpayment'] = array('totalDownpayment','number');
    $this->arrData['outstanding'] = array('outstanding','number');
    $this->arrData['salesordercodecache'] = array('salesordercodecache');
    $this->arrData['salesorderkeycache'] = array('salesorderkeycache');
    $this->arrData['salesorderajucache'] = array('salesorderajucache');
    //$this->arrData['isdownpayment'] = array('chkDownpayment');
    $this->arrData['currencykey'] = array('selCurrency');
    $this->arrData['rate'] = array('currencyRate','number');
    $this->arrData['othercost'] = array('otherCost','number'); 
    $this->arrData['approvedbykey'] = array('hidApprovedKey');
    $this->arrData['invoiceaddress'] = array('invoiceAddress');
    $this->arrData['invoicename'] = array('invoiceName');
    $this->arrData['invoiceaddressname'] = array('invoiceAddressName');
    $this->arrData['refinvoiceaddresskey'] = array('selInvoiceAddress');
    $this->arrData['undername'] = array('undername');
    $this->arrData['jobtypekey'] = array('selTypeOfJob');
    $this->arrData['transportationtypekey'] = array('selAirSea');
    $this->arrData['saleskey'] = array('hidSalesKey');

    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code', 'default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 90, 'align' =>'center', 'format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true,'width' => 200));
    array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceAmount','title' => 'invoiceAmount','dbfield' => 'grandtotal','default'=>true,'width' => 100, 'align'=>'right','format'=>'number'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'downpayment','title' => 'downpayment','dbfield' => 'totaldownpayment','default'=>true,'width' => 100, 'align'=>'right','format'=>'number'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceOutstanding','title' => 'invoiceOutstanding','dbfield' => 'outstanding','default'=>true,'width' => 100, 'align'=>'right','format'=>'number'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceType','title' => 'invoiceType','dbfield' => 'invoicetype','default'=>true, 'width' => 140));
    array_push($this->arrDataListAvailableColumn, array('code' => 'bank','title' => 'paymentTo','dbfield' => 'companybank','default'=>true, 'width' => 200));
    array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 80));
    array_push($this->arrDataListAvailableColumn, array('code' => 'salesordercode','title' => 'jobOrderCode','dbfield' => 'salesordercodecache','width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'salesorderaju','title' => 'aju','dbfield' => 'salesorderajucache','width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'receiptDate','title' => 'receiptDate','dbfield' => 'receiptdt', 'width' => 120, 'align' => 'center','format'=>'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'receivedDate','title' => 'dateReceived','dbfield' => 'receiveddate', 'width' => 100, 'align' => 'center','format'=>'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'invoicetaxnumber','title' => 'invoiceTaxNumber','dbfield' => 'invoicetaxnumber','default'=>false, 'width' => 130));
    array_push($this->arrDataListAvailableColumn, array('code' => 'undername','title' => 'undername','dbfield' => 'undername','default'=>true,'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'createdBy', 'title' => 'createdBy', 'dbfield' => 'createdbyname', 'width' => 150));
 
        
    $this->arrSearchColumn = array ();
    array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
    array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
    array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
    array_push($this->arrSearchColumn, array('Total', $this->tableName . '.grandtotal'));
    array_push($this->arrSearchColumn, array('Total', $this->tableName . '.totaldownpayment'));
    array_push($this->arrSearchColumn, array('Total', $this->tableName . '.outstanding'));
    array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.trdesc'));
    array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer . '.name'));
    array_push($this->arrSearchColumn, array('Kode JO', $this->tableName . '.salesordercodecache'));  
    array_push($this->arrSearchColumn, array('Kode JO', $this->tableName . '.salesorderajucache'));  
    array_push($this->arrSearchColumn, array('Undername', $this->tableName . '.undername'));  
    array_push($this->arrSearchColumn, array('Undername', $this->tableEmployee . '.name'));  


    array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
                 
    $this->printMenu = array();
    array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/emklOrderInvoice'));
 
	$this->includeClassDependencies(array(
        'PrepaidExpense.class.php',
        'Service.class.php',
        'CostReconsile.class.php',
        'EMKLJobOrder.class.php',  
        'Currency.class.php', 
        'Customer.class.php', 
        'Downpayment.class.php', 
        'CustomerDownpayment.class.php', 
        'InvoiceTax.class.php',
		'TermOfPayment.class.php',
		'AP.class.php',
		'APEmployeeCommission.class.php',
        'EMKLPurchaseOrder.class.php',
        'VatOut.class.php'
    ));  
    
    $this->newLoad = true;
    $this->overwriteConfig();
        
    }

    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
                '.$this->tableWarehouse.'.name as warehousename, 
                '.$this->tableCustomer.'.name as customername, 
                '.$this->tableEmployee.'.name as createdbyname,
                '.$this->tableStatus.'.status as statusname,
                '.$this->tableCustomCode.'.name as invoicetype,
                '.$this->tableCustomCode.'.isreimburse,
                '.$this->tablePaymentMethod.'.name as companybank
            FROM '.$this->tableStatus.', 
                 '.$this->tableName.'
                 left join '.$this->tableEmployee.' on  '.$this->tableName.'.createdby = '.$this->tableEmployee.'.pkey
                 left join '.$this->tableCustomCode.' on    '.$this->tableName.'.customcodekey =  '.$this->tableCustomCode.'.pkey
  				 left join '.$this->tablePaymentMethod.' on '.$this->tableName.'.companybankkey =  '.$this->tablePaymentMethod.'.pkey
                , '.$this->tableCustomer.', '.$this->tableWarehouse.' 
            WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
            ' .$this->criteria ;
         
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCustomerCriteria() ;
        
        return $sql;
    }
 

    function reCountGrandTotal($arrParam){
        $emklJobOrder = new EMKLJobOrder();
        $currency = new Currency();
        
        // pembulatan pajak ada 2 jenis, pembulatan pajak biasa (keatas, bawah atau round) dan pembulatan pajak dengan aturan coretax
        // pembulatan pajak biasa menggunakan settingan invoiceTaxRoundType
        // sedangkan utk coretax menggunaka vatOutRoundType
        // PRIORITAS PAJAK CORETAX (vatOutRoundType) lebih tinggi, dan akan mengoverwrite invoiceTaxRoundType
        
        
        // gk bisa pake vatout jg, karena cust ad yg mau tetep keriting decimalnya
        //$useVatOut = $this->isActiveModule('vatout');
        $vatOutRoundType = $this->loadSetting('vatOutRoundType');

        $grandtotal = 0;
        $subtotal = 0;

        $usePPNDetail = $this->loadSetting('usePPNDetail');
        $isPriceIncludeTax =  $arrParam['chkIncludeTax']; 
        //$finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
        $finalDiscount = 0; 
        $finalDiscountType = $arrParam['selFinalDiscountType']; 
        $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']); 
        $taxValue = $this->unFormatNumber($arrParam['taxValue']);   
        $arrSalesOrderKey = $arrParam['hidSalesOrderKey'];
        $arrInvoiceKey = $arrParam['hidInvoiceKey'];
        $arrInvoiceType = $arrParam['selInvoiceType'];
        $arrSalesOrderDescription  = $arrParam['detailNote'];
        $arrItemKey = $arrParam['hidItemKey']; 
        $arrSalesOrderDownpayment = $arrParam['salesOrderDownpayment'];
        $arrAmount = $arrParam['amount'];
        $arrPick = $arrParam['chkPick']; 
        $headerCurrencyKey = $arrParam['selCurrency'];
        $otherCost = $this->unFormatNumber($arrParam['otherCost']);
	
        $arrDataChkService = $arrParam['chkService']; 
        $arrDataChkTax = $arrParam['chkIsTax23']; 
    
        $arrDataQtyService = $arrParam['qtyDetail']; 
        $arrDataItemDetailKey = $arrParam['hidItemDetailKey'];  
        $arrDataSODetailKey = $arrParam['hidRefSODetailKey'];  
        $arrDataDetailCurrency = $arrParam['hidCurrencyKey'];  
        $arrDataDetailRate = $arrParam['detailRate'];  

        $defaultCurrencyKey = $currency->getDefaultData();
	    $detailSubtotalTaxed = 0;
        $detailTaxTotal = 0;
        $arrDetailInvoice = array();
		 
        
        for ($i=0;$i<count($arrPick);$i++){ 
            $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
            $arrSalesOrderDownpayment[$i] = $this->unFormatNumber($arrSalesOrderDownpayment[$i]);
            
            if ( empty($arrAmount[$i]) || empty($arrPick[$i]) )   continue;

			if($arrInvoiceType[$i] == 1 && empty($arrSalesOrderKey[$i])) continue;
			if($arrInvoiceType[$i] == 2 && empty($arrItemKey[$i])) continue;
			if($arrInvoiceType[$i] == 3 && empty($arrInvoiceKey[$i])) continue;
   	  
			// kalo biaya
            if ($arrInvoiceType[$i] == 2){  
                $subtotal += $arrAmount[$i];
            }else{  
				
				$rsInvoiceReferenceDetail = array();
				if($arrInvoiceType[$i] == 1){ 
					// kalo dr job order
					$rsPrice = $emklJobOrder->getUnInvoicedItemDetail($arrSalesOrderKey[$i]); 
					$joinfield = 'joinkey';
				}else{ 
					// kalo dr invoice lain
					$rsInvoiceReferenceDetail =  $this->getItemDetail($arrInvoiceKey[$i],'refheaderkey');
					 
					// copy ke rsPrice
                    $rsPrice = $rsInvoiceReferenceDetail;
                    // harus di combine sama pkey kwitansi, utk jaga2 kalo ad partial kwitansi
					// selama cuma narik harga satuan masih aman
					$joinfield = 'refsodetailkey'; 
					
					
                	$rsInvoiceReferenceDetail = array_column($rsInvoiceReferenceDetail,null, $joinfield);
				}
				
				
                $arrDefaultPrice = array_column($rsPrice,'priceinunit', $joinfield);

                $arrChkService = $arrDataChkService[$i]; 
                $arrChkTax = $arrDataChkTax[$i]; 
                $arrQtyService = $arrDataQtyService[$i]; 
                $arrItemDetailKey = $arrDataItemDetailKey[$i] ?? [];  
                $arrSODetailKey = $arrDataSODetailKey[$i];  
                $arrDetailCurrency = $arrDataDetailCurrency[$i];  
                $arrDetailRate = $arrDataDetailRate[$i];  

                $detailSubtotal = 0;
                $arrDetailInvoiceItem = array();
			
                $arrTaxDetail = $arrParam['taxDetail'][$i]; 
				$arrIsPriceIncludeTaxDetail = $arrParam['chkIncludeTaxDetail'][$i]; 	
//                $this->setLog($arrSODetailKey,true);
                $rsJODetailItem = $emklJobOrder->getItemDetail('', $arrSODetailKey);
                $rsJODetailItem = array_column($rsJODetailItem,null,'pkey');
                
                for ($j=0;$j<count($arrItemDetailKey);$j++){  
                    $joinkey = ($arrInvoiceType[$i] == 1) ? $arrSODetailKey[$j] . '-' . $arrItemDetailKey[$j] : $arrSODetailKey[$j];    
                    $priceInUnit = (isset($arrDefaultPrice[$joinkey])) ? $arrDefaultPrice[$joinkey] : 0 ;
                    $rateDetail = $this->unFormatNumber($arrDetailRate[$j]);
                    $qty = $this->unFormatNumber($arrQtyService[$j]);
                    
                    if ($arrDetailCurrency[$j] == $headerCurrencyKey)
                        $rateDetail = 1;
                    
                    $total = $qty * $priceInUnit;  
					
                    if($arrDetailCurrency[$j] == $defaultCurrencyKey){  
                       $total /= $rateDetail;   
                    }else{ 
                       $total *= $rateDetail; 
                    }
					
                    // gk boleh pake chkService karena gk kepecah
                    // gk kepecah karena kita gk assign chkService di arrItem
                    //  empty($arrChkService[$j]) || 
                     
                    
                    if ( empty($arrSODetailKey[$j]) || empty($arrItemDetailKey[$j]) || empty($total) )   continue;    

					if($arrInvoiceType[$i] == 1){ 
						// kalo dr tipe job order / normal, hitung ulang seperti biasa

                        if($usePPNDetail){

							$taxDetail =  $this->unFormatNumber($arrTaxDetail[$j]);
							$isPriceIncludeTaxDetail = $arrIsPriceIncludeTaxDetail[$j];
							  
                            $detailBeforeTax = 0;  
							$arrDetailInvoiceItem[$j]['priceInUnitDetail'] = $priceInUnit;
							$arrDetailInvoiceItem[$j]['subtotalDetail'] = $total;  
                            
                            // di database nilai tetep dicatat tanpa pembulatan, agar lebih real
                            // nanti jika perlu dibulatkan di XML saja
                            $roundedTaxValueDetail = 1; // karena tidak di rounded nilainya 1 
                                
                            $this->recalculateTaxAndValue($detailBeforeTax,$total,$taxDetailValue, $taxDetail, $isPriceIncludeTaxDetail,$roundedTaxValueDetail); 
 
							//$beforeTaxDetailForTax = $detailBeforeTax * $headerRate;
							$beforeTaxDetailForTax = $detailBeforeTax;
							
							$taxDetailValueForTax = floor($beforeTaxDetailForTax * $taxDetail / 100);
							 
							$arrDetailInvoiceItem[$j]['beforeTaxDetailValueForTax'] = $beforeTaxDetailForTax;
							$arrDetailInvoiceItem[$j]['taxDetailValueForTax'] = $taxDetailValueForTax;
							
							$arrDetailInvoiceItem[$j]['beforeTaxDetail'] = $detailBeforeTax;
							$arrDetailInvoiceItem[$j]['afterTaxDetail'] = $total;
                            $arrDetailInvoiceItem[$j]['chkIncludeTaxDetail'] = $isPriceIncludeTaxDetail;
							$arrDetailInvoiceItem[$j]['taxValueDetail'] = $taxDetailValue; 
                            $arrDetailInvoiceItem[$j]['taxDetail'] = $taxDetail;

                            $detailTaxTotal += $taxDetailValue;
                            $detailBeforeTaxTotal += $detailBeforeTax; 

                        }else{
 
                            // di database nilai tetep dicatat tanpa pembulatan, agar lebih real
                            // nanti jika perlu dibulatkan di XML saja
                            $taxDetailValue  =  ($isPriceIncludeTax == 1) ? floor( ($taxPercentage/(100 + $taxPercentage)) * $total ) :  $total * ($taxPercentage / 100);

                            $beforeTaxTotalDetail = ($isPriceIncludeTax == 1) ?   $total - $taxDetailValue : $total ;
                            $afterTaxTotal = ($isPriceIncludeTax == 1) ? $total : $total + $taxDetailValue;

                            // untuk informasi tambahhan kwitansi, tidak pengaruh ke perhitungan total utk jenis kwitansi
                            $arrDetailInvoiceItem[$j]['hidContainerDetailKey'] = $rsJODetailItem[$arrSODetailKey[$j]]['itemkey']; //$containerkey;
                            $arrDetailInvoiceItem[$j]['taxDetail'] = $taxPercentage;
                            $arrDetailInvoiceItem[$j]['taxValueDetail'] = $taxDetailValue;
                            $arrDetailInvoiceItem[$j]['beforeTaxDetail'] = $beforeTaxTotalDetail ;
                            $arrDetailInvoiceItem[$j]['afterTaxDetail'] = $afterTaxTotal;
                            $arrDetailInvoiceItem[$j]['chkIncludeTaxDetail'] = $isPriceIncludeTax;
                            $arrDetailInvoiceItem[$j]['subtotalDetail'] = $total; // kalo normal, subtotalDetail ikutin $total. qty * priceinunit,
                             
                            // khusus CORETAX
                            if($vatOutRoundType == 1){  
                                $detailTaxTotal += round($taxDetailValue);
                            }
						}
					} else if($arrInvoiceType[$i] == 3){
						// kalo dr tipe kwitansi detailnya, ambil ulang detailnya dr JO, copy ulang semua row detailnya
						// nilai total yg dihitung dr aftertaxdetail 
						
						$arrDetailRow = $rsInvoiceReferenceDetail[$joinkey];
						$arrDetailInvoiceItem[$j]['hidContainerDetailKey'] = $arrDetailRow['containerkey'];
						$arrDetailInvoiceItem[$j]['taxDetail'] = $arrDetailRow['taxdetail'];
						$arrDetailInvoiceItem[$j]['taxValueDetail'] = $arrDetailRow['taxdetailvalue'];
						$arrDetailInvoiceItem[$j]['beforeTaxDetail'] = $arrDetailRow['beforetaxdetailvalue']; // ini perlu recount kah ?
						$arrDetailInvoiceItem[$j]['afterTaxDetail'] = $arrDetailRow['aftertaxdetailvalue'];
						$arrDetailInvoiceItem[$j]['chkIncludeTaxDetail'] = $arrDetailRow['ispriceincludetax'];
						$arrDetailInvoiceItem[$j]['subtotalDetail'] = $arrDetailRow['beforetaxdetailvalue']; // kalo dr KW, totalnya sebelum tax
						
						// overwrite total,
						$total = $arrDetailInvoiceItem[$j]['afterTaxDetail']; 
					}
					
					$detailSubtotal += $total;  
 					

                    // ini utk PPH 23 
                    if(!empty($arrChkTax[$j]))
                        $detailSubtotalTaxed += ($usePPNDetail)  ? $detailBeforeTax : $total;
                    
                }
                  
				// untuk informasi tambahhan kwitansi, tidak pengaruh ke perhitungan total
				$arrDetailInvoice[$i] =  $arrDetailInvoiceItem  ;
				
                $subtotal += $detailSubtotal;    
                
            }
        }
		
        $grandtotal = $subtotal;
      
        if($usePPNDetail){
            $beforeTaxTotal = $detailBeforeTaxTotal;
			 
			// pembulatan di detail 
			// tidak perlu, karena sudah terpisah kolom utk FP yg dibulatkan
            $taxTotal = $detailTaxTotal; //($headerCurrencyKey == CURRENCY['idr']) ? floor($detailTaxTotal) : $detailTaxTotal;
            $grandtotal = $beforeTaxTotal + $taxTotal; 

        }else{
          
            if ($finalDiscount != 0){
                if ($finalDiscountType == 2)
                    $finalDiscount = $finalDiscount/100 * $grandtotal;
            } 

            $beforeTaxTotal = $subtotal - $finalDiscount; 
            $grandtotal = $beforeTaxTotal;
            
            $rondedType =  ($arrParam['selCurrency'] != CURRENCY['idr'])  ? 1 : $this->loadSetting('invoiceTaxRoundType');

            // utk CORERTAX
            if($vatOutRoundType == 1){  
                // sementara baru yg exclude aj dulu  
                 if ($isPriceIncludeTax == false) {
                     // tax value jumlahin dari detail
           
                    //$taxValue = 0; 
                    //foreach($arrDetailInvoice as $detailInvoiceRow) 
                    //    foreach($detailInvoiceRow as $itemDetailRow) 
                    //        $taxValue += round($itemDetailRow['taxValueDetail']);
                    //
                    ////$this->setLog($taxValue,true);
                    //$grandtotal += $taxValue;
                     
                     $taxValue = $detailTaxTotal; // overwrite total tax header
                     $grandtotal += $detailTaxTotal;
                }else{

                    // sementara masih sama seperti biasa
                    // pembulatan untuk coretax, akan masalah utk tipe include 
                    // nanti dicek kembali
                     
                    $taxValue =  ($taxPercentage/(100 + $taxPercentage)) * $grandtotal; 
                    $taxValue = $this->getInvoiceRoundedTax($taxValue,$rondedType);  
                    $beforeTaxTotal = $grandtotal - $taxValue ;
                }
                
                
            }else{
                 
                if ($isPriceIncludeTax == false) {
                    $taxValue = $beforeTaxTotal * $taxPercentage / 100;
                    $taxValue = $this->getInvoiceRoundedTax($taxValue,$rondedType);
                    $grandtotal += $taxValue;
                }else{
                    $taxValue =  ($taxPercentage/(100 + $taxPercentage)) * $grandtotal; 
                    $taxValue = $this->getInvoiceRoundedTax($taxValue,$rondedType);  
                    $beforeTaxTotal = $grandtotal - $taxValue ;
                }
            }
            
        }
  
        $grandtotal += $otherCost;

        $balance = 0;
        $totalPayment = 0; 

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPayment']);  
        if ($rsTOP[0]['duedays'] == 0){ 
            $payment = $arrParam['paymentMethodValue'];
            for($i=0;$i<count($payment);$i++){
                $totalPayment += $this->unFormatNumber($payment[$i]);
            } 
        } 

        $totalDowpayment = 0; 
        $downpayment = $arrParam['downpaymentAmount'];
        $downpaymentKey = $arrParam['hidDownpaymentKey'];
        for($i=0;$i<count($downpayment);$i++){
            if(empty($downpaymentKey[$i]))
                continue;
            $totalDowpayment += $this->unFormatNumber($downpayment[$i]);
        }  
	
        $tax23Percentage = $arrParam['tax23Percentage'];
        $outstanding = $grandtotal - $totalDowpayment;
        $balance = $totalPayment - $outstanding; 
        
         // cek dulu ppn nya include atau exclude
         if ($isPriceIncludeTax) 
            $detailSubtotalTaxed = $detailSubtotalTaxed - (round(($taxPercentage/(100 + $taxPercentage)) * $detailSubtotalTaxed)) ;
          
	    $tax23 = $tax23Percentage * $detailSubtotalTaxed / 100; 

        $reCountResult = array();
        $reCountResult['subtotal'] = $subtotal;
        $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
        $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
        $reCountResult['grandTotal'] = $grandtotal;
        $reCountResult['totalPayment'] = $totalPayment;
        $reCountResult['totalDownpayment'] = $totalDowpayment;
        $reCountResult['outstanding'] = $outstanding;
        $reCountResult['balance'] = $balance;  
	    $reCountResult['tax23Value'] = $tax23; 
	   // $reCountResult['taxValue'] = $taxValue; 
        $reCountResult['taxValue'] = ($usePPNDetail) ? $detailTaxTotal : $taxValue;
        $reCountResult['recountDetail'] =  $arrDetailInvoice ;  

        return $reCountResult;

    } 

    function validateForm($arr,$pkey = ''){ 
        $emklJobOrder = new EMKLJobOrder();
        $arrayToJs = parent::validateForm($arr,$pkey); 
        $downpayment = new CustomerDownpayment();

        $customerkey = $arr['hidCustomerKey']; 
        $arrAmount = $arr['amount'];
        $arrSalesOrderKey = $arr['hidSalesOrderKey']; 
        $arrInvoiceKey = $arr['hidInvoiceKey']; 
        $refHeaderKey = $arr['hidSalesOrderHeaderKey']; 
        $arrSalesOrderDescription  = $arr['detailNote'];
        $arrPick = $arr['chkPick']; 
        $arrInvoiceType = $arr['selInvoiceType']; 
        $arrDownpaymentKey = $arr['hidDownpaymentKey'];
		$arrDownpaymentAmount = $arr['downpaymentAmount'];
		$arrDownpaymentCode = $arr['downpaymentCode'];
		$subtotal = $arr['subtotal'];
        $salesOrderSubtotal = $arr['salesOrderSubtotal'];
        //$salesOrderDownpayment = $arr['salesOrderDownpayment'];
        $warehousekey = $arr['selWarehouseKey'];
        $selBank = $arr['selBank'];
        
        $arrDetailKey = array();
          
        if(empty($customerkey)) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
        } else {
            if($this->loadSetting('paymentAccountMustMatchDefault') == 1){ 
                
                $customer = new Customer();
                //cek bank payment harus sama dengan customer
                $rsCustomer = $customer->getDataRowById($customerkey); 
                if($selBank != $rsCustomer[0]['companybankkey']) 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['emklOrderInvoice'][8]); 
            }
        }
        
        //validasi kalo status gk menunggu gk bisa edit 
        if (!empty($pkey)){
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] <> 1){
                $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
            }
        }  
 
        
//         validasi gudang harus sama
        if($this->loadSetting('invoiceStrictWarehouse') == 1){
            $rsJobOrder = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.code',$emklJobOrder->tableName.'.warehousekey'),
                                                        ' and '.$emklJobOrder->tableName.'.pkey in ('.$this->oDbCon->paramString($refHeaderKey,',').')');
              
            foreach($rsJobOrder as $joRow){
             if($joRow['warehousekey'] <> $warehousekey)
                 $this->addErrorList($arrayToJs,false, $joRow['code'].'. '.$this->errorMsg[905]); 
            } 
        }
        
        for($i=0;$i<count($arrAmount);$i++) { 
            $arrAmount[$i] = $this->unformatNumber($arrAmount[$i]);
            $salesOrderSubtotal[$i] = $this->unformatNumber($salesOrderSubtotal[$i]);
            //$salesOrderDownpayment[$i] = $this->unformatNumber($salesOrderDownpayment[$i]);
            
            if ($arrAmount[$i] <= 0)
                $this->addErrorList($arrayToJs,false,$this->errorMsg[503]);
            
            
            /*if ($arrAmount[$i] > ($salesOrderSubtotal[$i] - $salesOrderDownpayment[$i]))
                $this->addErrorList($arrayToJs,false,$this->errorMsg[508]);*/
        }
        
        
        $hasSO = false; 
        // cek ad duplikasi gk, dan cek customernya sesuai gk
        for($i=0;$i<count($arrSalesOrderKey);$i++) {   

	 	// tipe job order
		if ( $arrInvoiceType[$i] == 1 && !empty($arrSalesOrderKey[$i]) && !empty($arrPick[$i]) )  {
			$hasSO = true;   

			$rsSO = $emklJobOrder->getDetailByColumn('pkey',$arrSalesOrderKey[$i]);

			if (in_array($arrSalesOrderKey[$i],$arrDetailKey)){  
				$this->addErrorList($arrayToJs,false, $rsSO[0]['code'].'. '.$this->errorMsg[215]); 	 
			}else{ 
				if (!empty($arrSalesOrderKey[$i])) {  
					array_push($arrDetailKey, $arrSalesOrderKey[$i]);
				}
			}

			if ($rsSO[0]['customerkey'] <> $customerkey)
				$this->addErrorList($arrayToJs,false, $rsSO[0]['code'].'. '.$this->errorMsg['emklOrderInvoice'][3]);	
		}
  
             
        } 

        /*
        if (!$hasSO)
        $this->addErrorList($arrayToJs,false, $this->errorMsg['salesOrder'][1]); 	
        */
		
		// tipe invoice
  		$hasInvoice = false; 

		// cek ad duplikasi gk, dan cek customernya sesuai gk
		for($i=0;$i<count($arrInvoiceKey);$i++) {   

			if (  $arrInvoiceType[$i] == 3 && !empty($arrInvoiceKey[$i])  && !empty($arrPick[$i]) )  {
				$hasInvoice = true;   

				$rsInvoice = $this->getDataRowById($arrInvoiceKey[$i]);

				if (in_array($arrInvoiceKey[$i],$arrDetailKey)){  
					$this->addErrorList($arrayToJs,false, $rsInvoice[0]['code'].'. '.$this->errorMsg[215]); 	 
				}else{ 
					if (!empty($arrInvoiceKey[$i])) {  
						array_push($arrDetailKey, $arrInvoiceKey[$i]);
					}
				}

				if ($rsInvoice[0]['customerkey'] <> $customerkey)
					$this->addErrorList($arrayToJs,false, $rsInvoice[0]['code'].'. '.$this->errorMsg['emklOrderInvoice'][4]);	
			}


		} 
            
//        if (!$hasInvoice)
//            $this->addErrorList($arrayToJs,false, $this->errorMsg['salesOrder'][1]); 
        
 
             
            
        $arrDownpaymentExistKey = array();
        for($i=0;$i<count($arrDownpaymentKey);$i++) {  
            if(empty($arrDownpaymentKey[$i]))
                continue;
            
            // validasi DP masi available gk
            $rsDP = $downpayment->searchData($downpayment->tableName.'.pkey',$arrDownpaymentKey[$i],true, ' and '.$downpayment->tableName.'.statuskey in (2) ');
               
            if(empty($rsDP)){ 
                $this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][9]);
            }else{
              
                if ($customerkey <> $rsDP[0]['customerkey'])
                    $this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][6]); 
                
                // cek double gk
                 if (in_array($arrDownpaymentKey[$i],$arrDownpaymentExistKey)){  
                    $this->addErrorList($arrayToJs,false, $rsDP[0]['code'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    if (!empty($arrDownpaymentKey[$i])) {  
                        array_push($arrDownpaymentExistKey, $arrDownpaymentKey[$i]);
                    }
                }
                
                // validasi nilai DP masi mencukupi gk
                $amount = $this->unformatNumber($arrDownpaymentAmount[$i]);
                if ($amount > $rsDP[0]['outstanding'] )
                    $this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][8].' ('.$this->lang['outstanding']. ': ' .$this->formatNumber($rsDP[0]['outstanding']).')');  
            }
                
        }
 


    return $arrayToJs;
    }

    
    function recalculatePriceBeforeTaxAndFinalDiscount($rs,$amount){
        // potong diskon proposional dan hitung harga sebelum pajak
        
        $finalDiscount = ($rs[0]['finaldiscount'] != 0 && $rs[0]['finaldiscounttype'] == 2) ? $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'] : $rs[0]['finaldiscount']; 
        $total =  $rs[0]['subtotal'];
        $taxPercentage = $rs[0]['taxpercentage'];
        
        $discount = (!empty($finalDiscount)) ? $amount / $total * $finalDiscount : 0;
         
        $priceBeforeTax = $amount - $discount; 
        
        if(!empty($rs[0]['taxvalue'])){   
            if ($rs[0]['ispriceincludetax']) { 
                $taxValue = round(($taxPercentage/(100 + $taxPercentage)) * $priceBeforeTax);   
                $priceBeforeTax = $priceBeforeTax - $taxValue ;
            } 
        }

        
        return $priceBeforeTax;
        
    }
  
//	
//	function updateICAGL($rs,$arrCommission,$rsCustomer){
//		// AR ICA pada revenue ICA
//		
//        $generalJournal = new GeneralJournal();
//        $customer = new Customer(); 
//      
//		   
//        $temp = -1; 
//		
//        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
//        $warehousekey = $rs[0]['warehousekey'];
//		
//		$arr = array();
//        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
//        $arr['code'] = 'xxxxx';
//        $arr['refkey'] = $rs[0]['pkey'];
//        $arr['refTableType'] = $rsKey['key'];
//        $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
//        $arr['createdBy'] = 0;
//        $arr['selWarehouse'] = $warehousekey;
//        $arr['trDesc'] = $rsCustomer[0]['name'];
//		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
//		
//		
//        $temp++;
//        $arr['hidCOAKey'][$temp] =  $customer->getARCOAKey($rs[0]['customerkey'],$warehousekey);
//        $arr['debit'][$temp] = $rs[0]['grandtotal'];
//        $arr['credit'][$temp] = 0;
//		$arr['trdescDetail'][$temp] = '';
//		
//        $temp++;
//        $arr['hidCOAKey'][$temp] = $rsCustomer[0]['icacoakey'];
//        $arr['debit'][$temp] = 0;
//        $arr['credit'][$temp] = $rs[0]['grandtotal'];
//		$arr['trdescDetail'][$temp] = '';
//     	
//        $arrayToJs = $generalJournal->addData($arr); 
// 
//        if (!$arrayToJs[0]['valid'])
//            throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);
//
//	}
	
    function updateGL($rs){ 
        if (!USE_GL) return;
		

        $generalJournal = new GeneralJournal();
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();
        $customer = new Customer();
        $service = new Service(SERVICE);
        $customCode = new CustomCode();
		$employee = new Employee();
		
		
		$headerCurrencyKey = $rs[0]['currencykey'];
        $warehousekey = $rs[0]['warehousekey'];
		
		// biar gk bingung, kalo utk ICA, lempar ke fungsi baru saja  
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
		
		// klao dia split COA, perlu cari detail kwitansinya lg
		$costByJobCategory = $this->loadSetting('splitCOAByJobCategory');
		$usePrepaidExpense = $this->loadSetting('usePrepaidExpense');
		$usePrepaidExpense = ($usePrepaidExpense == 1) ? true : false;
		        
        $usePPNDetail = $this->loadSetting('usePPNDetail');
		$usePPNDetail = ($usePPNDetail == 1) ? true : false;
        
        $id = $rs[0]['pkey']; 
        $rsDetail = $this->getDetailById($id);  
        
        $rsCustomCode = $customCode->getDataRowById($rs[0]['customcodekey']); 
        $isReimburse = $rsCustomCode[0]['isreimburse']; 
        
          
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
        $arr['createdBy'] = 0;
        $arr['selWarehouse'] = $warehousekey;
        $arr['trDesc'] = $rsCustomer[0]['name'];
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
        
        $rate = ($rs[0]['currencykey']==CURRENCY['idr']) ? 1 : $rs[0]['rate']; //versi lama pakai rate jo
        
//        $rate = ($rs[0]['currencykey']==CURRENCY['idr']) ? 1 : $this->getDetailRate($id); 
		
		
		$totalTaxFromOtherInvoices = 0;
		
        $temp = -1; 
        $totalDisc = 0 ; 
  
        $finalDiscount = ($rs[0]['finaldiscount'] != 0 && $rs[0]['finaldiscounttype'] == 2) ? $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'] : $rs[0]['finaldiscount']; 
        $totalDisc = $finalDiscount;

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 

        if ($isCash) {
            $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
            for($i=0;$i<count($rsPayment); $i++){ 
                    $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey, $rsPayment[$i]['paymentkey']);
                    $temp++;
                    $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                    $arr['debit'][$temp] = $rsPayment[$i]['amount']*$rate ; // harus kali rate kah ?  
                    $arr['credit'][$temp] = 0; 
                    $arr['debitSource'][$temp] = $rsPayment[$i]['amount']; 
                    $arr['creditSource'][$temp] = 0 ; 
                    $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                    $arr['rate'][$temp] = $rate ; 
                    $arr['trdescDetail'][$temp] = '';
            }
 
            //selisih pembayaran   
            $temp++; 
            if ($rs[0]['balance'] < 0){ 
                $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                $arr['debit'][$temp] = abs($rs[0]['balance']); 
                $arr['credit'][$temp] = 0; 
                $arr['debitSource'][$temp] = abs($rs[0]['balance']); 
                $arr['creditSource'][$temp] = 0 ; 
				$arr['trdescDetail'][$temp] = '';
            }else{ 
                $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] = abs($rs[0]['balance']); 
                $arr['debitSource'][$temp] = 0;
                $arr['creditSource'][$temp] = abs($rs[0]['balance']);  
				$arr['trdescDetail'][$temp] = '';
            }

            $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
            $arr['rate'][$temp] = $rate ; 

            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];

        }else {  
                $arAmount = $rs[0]['grandtotal'] - $rs[0]['totaldownpayment'];
                $temp++;
                $arr['hidCOAKey'][$temp] =  $customer->getARCOAKey($rs[0]['customerkey'],$warehousekey);
                $arr['debit'][$temp] = round($arAmount*$rate,2) ; // biar sama dengan outstanding AR nya 
                $arr['credit'][$temp] = 0;  
                $arr['debitSource'][$temp] = $arAmount; 
                $arr['creditSource'][$temp] = 0 ; 
                $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                $arr['rate'][$temp] = $rate ; 
				$arr['trdescDetail'][$temp] = '';
        } 
 
        //downpayment 
		$rsDownpayment = $this->getDownpaymentDetail($rs[0]['pkey']);  
        for($i=0;$i<count($rsDownpayment); $i++){  
             $temp++;
             $arr['hidCOAKey'][$temp] = $customer->getDownpaymentCOAKey($rs[0]['customerkey'],$warehousekey);   
             $arr['debit'][$temp] = $rsDownpayment[$i]['amount'] * $rate; 
             $arr['credit'][$temp] = 0; 
             $arr['debitSource'][$temp] = $rsDownpayment[$i]['amount']; 
             $arr['creditSource'][$temp] = 0 ; 
             $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
             $arr['rate'][$temp] = $rate ; 
			 $arr['trdescDetail'][$temp] = ''; 
        }
		
		if($rsCustomer[0]['isica'] == 1){ 
			$temp++;
			$arr['hidCOAKey'][$temp] = $rsCustomer[0]['icacoakey'];
			$arr['debit'][$temp] = 0;
			$arr['credit'][$temp] = $rs[0]['grandtotal'] * $rate;
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] = $rs[0]['grandtotal'] ; 
            $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
            $arr['rate'][$temp] = $rate ; 

			$arr['trdescDetail'][$temp] = '';
		}else{
			 if (!$rs[0]['isdownpayment']){ 

					if($isReimburse){

						$arrCOAAmout = array();

						for($i=0;$i<count($rsDetail);$i++){
							if ($rsDetail[$i]['invoicetype'] == 2){
								$rsItem = $service->getDataRowById($rsDetail[$i]['itemkey']); 
								$coakey = $rsItem[0]['revenuecoakey'];
								if (!isset($arrCOAAmout[$coakey]))
									$arrCOAAmout[$coakey] = 0;

								$arrCOAAmout[$coakey] += $this->recalculatePriceBeforeTaxAndFinalDiscount($rs,$rsDetail[$i]['amount']);  

							}else{ 

								// ambil detail item
								$rsServiceDetail = $this->getItemDetail($rsDetail[$i]['pkey']);

								// jika COA dipisah berdasarkan jenis pekerjaan 
								if($costByJobCategory == 1){

									$arrJOkey = array_column($rsServiceDetail,'refsoheaderkey');

									$emklJobOrder = new EMKLJobOrder();
									$rsJOCol = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.jobtypekey',$emklJobOrder->tableName.'.loadcontainertypekey'),
													  ' and '.$emklJobOrder->tableName.'.pkey in(' . $this->oDbCon->paramString($arrJOkey,',').')'
													  );
									$rsJOCol = array_column($rsJOCol,null,'pkey');

								}


								for($j=0;$j<count($rsServiceDetail);$j++){ 

									// biar cepet
									if($costByJobCategory == 1){  
										$rsJO = $rsJOCol[ $rsServiceDetail[$j]['refsoheaderkey'] ]; // tp kalo beberapa JO gmana ??
										$coakey = $service->getCostCOAKey($rsServiceDetail[$j]['itemkey'],$warehousekey,'costcoakey',$usePrepaidExpense,$rsJO['jobtypekey'],$rsJO['loadcontainertypekey'], $isReimburse);
                                        
									} else { 
										$coakey = $rsServiceDetail[$j]['costcoakey'];
									}


									if (empty($coakey)){  
										 $coatype = 'operationalcost';
										 $rsCOA = $coaLink->getCOALink ($coatype, $warehouse->tableName,$warehousekey, 0);
										 $coakey = $rsCOA[0]['coakey'];
									}

									if (!isset($arrCOAAmout[$coakey]))
										$arrCOAAmout[$coakey] = 0;

									$arrCOAAmout[$coakey] += $this->recalculatePriceBeforeTaxAndFinalDiscount($rs,$rsServiceDetail[$j]['total']);  
								}
							}
						}


						foreach ($arrCOAAmout as $key => $amount) { 
								$temp++;
								$arr['hidCOAKey'][$temp] = $key;
								$arr['debit'][$temp] = 0;
								$arr['credit'][$temp] = $amount * $rate; 
                                $arr['debitSource'][$temp] = 0; 
                                $arr['creditSource'][$temp] = $amount ; 
                                $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                                $arr['rate'][$temp] = $rate ; 
								$arr['trdescDetail'][$temp] = '';
						}

					}else{
							$arrCOAAmout = array();

							for($i=0;$i<count($rsDetail);$i++){ 

								if ($rsDetail[$i]['invoicetype'] == 2){

									throw new Exception('<strong>'.$rs[0]['code'] . '</strong>.  Jenis transaksi tidak didukung.');

		//							$rsItem = $service->getDataRowById($rsDetail[$i]['itemkey']); 
		//                            $coakey = $rsItem[0]['revenuecoakey'];
		//                            if (!isset($arrCOAAmout[$coakey]))
		//                                $arrCOAAmout[$coakey] = 0;
		//
		//                            $arrCOAAmout[$coakey] += $this->recalculatePriceBeforeTaxAndFinalDiscount($rs,$rsDetail[$i]['amount']);   

								}else {

									// ambil detail item
									$rsServiceDetail = $this->getItemDetail($rsDetail[$i]['pkey']);  

									// jika COA dipisah berdasarkan jenis pekerjaan 
									if($costByJobCategory == 1){

										//$rsInvoice = $this->getDetailById($rsDetail[$i]['invoicekey']);
										$arrJOkey = array_column($rsServiceDetail,'refsoheaderkey');

										$emklJobOrder = new EMKLJobOrder();
										$rsJOCol = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.jobtypekey',$emklJobOrder->tableName.'.loadcontainertypekey'),
														  ' and '.$emklJobOrder->tableName.'.pkey in(' . $this->oDbCon->paramString($arrJOkey,',').')'
														  );
										$rsJOCol = array_column($rsJOCol,null,'pkey');

									}

									for($j=0;$j<count($rsServiceDetail);$j++){ 

										// biar cepet
										if($costByJobCategory == 1){  
											$rsJO = $rsJOCol[ $rsServiceDetail[$j]['refsoheaderkey'] ]; // tp kalo beberapa JO gmana ??
											$coakey = $service->getRevenueCOAKey($rsServiceDetail[$j]['itemkey'],$warehousekey,$rsJO['jobtypekey'],$rsJO['loadcontainertypekey']);
										} else { 
											$coakey = $rsServiceDetail[$j]['revenuecoakey'];	
										}


										if (empty($coakey)){ 
											 $coatype = (empty($rsServiceDetail[$j]['refsodetailkey'])) ? 'otherrevenue' : 'salesservice';  
											 $rsCOA = $coaLink->getCOALink ($coatype, $warehouse->tableName,$warehousekey, 0);
											 $coakey = $rsCOA[0]['coakey'];
										}

										if (!isset($arrCOAAmout[$coakey]))
											$arrCOAAmout[$coakey] = 0;

										if($rsDetail[$i]['invoicetype'] == 1){  
											// normal 
                                            
//                                            $this->setLog("test",true);
                                            
											$arrCOAAmout[$coakey] += ($usePPNDetail == 1 ) ? $rsServiceDetail[$j]['beforetaxdetailvalue'] : $this->recalculatePriceBeforeTaxAndFinalDiscount($rs,$rsServiceDetail[$j]['total']); 
										}else {  
											// kalo dr kwitansi kecil ada nilai pajak    
											$arrCOAAmout[$coakey] += $rsServiceDetail[$j]['beforetaxdetailvalue'];	
											$totalTaxFromOtherInvoices += $rsServiceDetail[$j]['taxdetailvalue'];   
										}
									}
								} 

							}

							foreach ($arrCOAAmout as $key => $amount) { 
									$temp++;
									$arr['hidCOAKey'][$temp] = $key;
									$arr['debit'][$temp] = 0;
									$arr['credit'][$temp] = $amount* $rate; 
                                    $arr['debitSource'][$temp] = 0; 
                                    $arr['creditSource'][$temp] = $amount ; 
                                    $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                                    $arr['rate'][$temp] = $rate ; 
									$arr['trdescDetail'][$temp] = '';
							}
					}

				} else{
						$totalDP = 0;
						$coaKey  = $coaLink->getCOALink ('salesservice', $warehouse->tableName,$warehousekey, 0);
						for($i=0;$i<count($rsDetail);$i++) 
							$totalDP += $this->recalculatePriceBeforeTaxAndFinalDiscount($rs,$rsDetail[$i]['amount']);   


						$temp++;
						$arr['hidCOAKey'][$temp] = $coaKey[0]['coakey'];
						$arr['debit'][$temp] = 0;
                        $arr['credit'][$temp] = $totalDP * $rate;   
                        $arr['debitSource'][$temp] = 0; 
                        $arr['creditSource'][$temp] = $totalDP ; 
                        $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                        $arr['rate'][$temp] = $rate ; 
						$arr['trdescDetail'][$temp] = '';
				}

		/*        $rsCOA = $coaLink->getCOALink ('salesservicediscount', $warehouse->tableName,$warehousekey, 0);
				$temp++;
				$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
				$arr['debit'][$temp] = $totalDisc; 
				$arr['credit'][$temp] = 0; 
				$arr['trdescDetail'][$temp] = '';*/

				$rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0);
				$temp++;
				$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
				$arr['debit'][$temp] = 0;
				$arr['credit'][$temp] = $rs[0]['othercost'] * $rate; 
                $arr['debitSource'][$temp] = 0; 
                $arr['creditSource'][$temp] = $rs[0]['othercost'] ; 
                $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                $arr['rate'][$temp] = $rate ; 
				$arr['trdescDetail'][$temp] = '';

				$rsCOA = $coaLink->getCOALink ('taxout', $warehouse->tableName,$warehousekey, 0);
				$taxOutCoaKey = $rsCOA[0]['coakey'];
				$temp++;
				$arr['hidCOAKey'][$temp] = $taxOutCoaKey;
				$arr['debit'][$temp] = 0;
				$arr['credit'][$temp] = $rs[0]['taxvalue'] * $rate;  
                $arr['debitSource'][$temp] = 0; 
                $arr['creditSource'][$temp] = $rs[0]['taxvalue'] ; 
                $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                $arr['rate'][$temp] = $rate ; 
				$arr['trdescDetail'][$temp] = '';

				// khusus utk invoice dr kwitansi kecil
				if($totalTaxFromOtherInvoices > 0){ 
					$temp++;
					$arr['hidCOAKey'][$temp] = $taxOutCoaKey;
					$arr['debit'][$temp] = 0;
					$arr['credit'][$temp] = $totalTaxFromOtherInvoices; 
                    $arr['debitSource'][$temp] = 0; 
                    $arr['creditSource'][$temp] = $totalTaxFromOtherInvoices; // ini akan bermasalah nanti kalo beda currency, tp biarin aj karena cuma OKL yg pake 
                    $arr['selCurrencyKey'][$temp] = CURRENCY['idr'] ; 
                    $arr['rate'][$temp] = 1 ; 
					$arr['trdescDetail'][$temp] = ''; 
				}


				// KOMISI KANTOR / MANAGEMENT 
//				foreach($arrCommission as $row){
//					// setiap gudang bisa beda alokasi coanya 
//					$commissionWarehouseKey  = $row['warehousekey'];
//					$coaCommissionCost =  $coaLink->getCOALink ('adminfeecost', $warehouse->tableName,$commissionWarehouseKey, 0);
//
//					$temp++;
//					$arr['hidCOAKey'][$temp] =  $coaCommissionCost[0]['coakey'];
//					$arr['debit'][$temp] = $row['amount'];
//					$arr['credit'][$temp] = 0;  
//					$arr['trdescDetail'][$temp] =  $rs[0]['code'].', '. $row['code'].'.';
//
//					$temp++;
//					$arr['hidCOAKey'][$temp] =  $employee->getAPCommissionCOAKey($row['employeekey'],$commissionWarehouseKey);
//					$arr['debit'][$temp] =  0;
//					$arr['credit'][$temp] = $row['amount'];
//					$arr['trdescDetail'][$temp] =  $rs[0]['code'].', '. $row['code'].'.'; 
//				} 
		}
		 
		
        $arrayToJs = $generalJournal->addData($arr);
 
        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);

    } 

    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*,  
            '.$this->tableSalesOrderDetail.'.code as socode,  
            '.$this->tableSalesOrderDetail.'.hbl,  
            '.$this->tableSalesOrderDetail.'.refdetailhbl,  
            '.$this->tableSalesOrderDetail.'.trdate as sodate, 
            '.$this->tableItem.'.name as itemname
          from
            '.$this->tableNameDetail.'
                left join 
                    (select 
                        '.$this->tableSalesOrderDetail.'.pkey, 
                        '.$this->tableSalesOrderDetail.'.code, 
                        '.$this->tableSalesOrderDetail.'.hbl, 
                        '.$this->tableSalesOrderDetail.'.refdetailhbl, 
                        '.$this->tableSalesOrderHeader.'.trdate
                    from 
                        '.$this->tableSalesOrderHeader.', 
                        '.$this->tableSalesOrderDetail.' 
                    where
                    '.$this->tableSalesOrderDetail.'.refkey = '.$this->tableSalesOrderHeader.'.pkey
                    ) as '.$this->tableSalesOrderDetail.' on  '.$this->tableNameDetail.'.salesorderkey = '.$this->tableSalesOrderDetail.'.pkey
                left join '.$this->tableItem.' on  '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey 
          where  
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

        $sql .= $criteria;
		 
        return $this->oDbCon->doQuery($sql);

    }
 
	function generateInvoiceReport($criteria='',$order='',$pkey='',$itemkey = ''){
	   
	   $sql =  '
			SELECT '.$this->tableName.'.code,
                   '.$this->tableCustomer.'.name as customername, 
                   '.$this->tableItem.'.name as itemname, 
                   '.$this->tableName.'.salesordercodecache, 
                   '.$this->tableName.'.customcodekey, 
                   '.$this->tableName.'.trdate, 
                   '.$this->tableNameDetail.'.description, 
                   '.$this->tableNameItemDetail.'.qtyinbaseunit, 
                   '.$this->tableNameItemDetail.'.priceinunit, 
                   '.$this->tableNameItemDetail.'.total, 
                   '.$this->tableSalesOrderDetail.'.code as socode, 
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableWarehouse.'.name as warehousename , 
                   concat('.$this->tableCOA. '.code,\' - \','.$this->tableCOA. '.name) as coaname
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableItem.'  
                    left join '.$this->tableCOA.' on  '.$this->tableItem.'.revenuecoakey = '.$this->tableCOA.'.pkey,
                '.$this->tableNameDetail.'
                       left join '.$this->tableSalesOrderDetail.' on  '.$this->tableNameDetail.'.salesorderkey = '.$this->tableSalesOrderDetail.'.pkey,
                '.$this->tableName.',
                '.$this->tableCustomer.',
                '.$this->tableNameItemDetail.',
                '.$this->tableWarehouse.'
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and 
                '.$this->tableNameItemDetail.'.refkey = '.$this->tableNameDetail.'.pkey and 
                '.$this->tableNameItemDetail.'.refheaderkey = '.$this->tableName.'.pkey and 
                '.$this->tableNameItemDetail.'.itemkey = '.$this->tableItem.'.pkey and 
                '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($pkey))  
            $sql .=  '  and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey);
        
        if (!empty($itemkey))  
            $sql .=  '  and '.$this->tableNameItemDetail.'.costkey = ' .$this->oDbCon->paramString($itemkey); 
         
        if (!empty($order))  
            $sql .=  ' ' .$order; 
        
       return $this->oDbCon->doQuery($sql);
		 
    }
   
    function manipulateDataBeforeUpdateData($arrParam){
         
        // sementara tembak dulu utk CIF
        if(!in_array(DOMAIN_NAME, array('cif.wintera.co.id') )) return $arrParam;
        if (!isset($arrParam['hidSalesOrderKey'][0]) || empty($arrParam['hidSalesOrderKey'][0]) ) return $arrParam;
            
        $emklJobOrder = new EMKLJobOrder();

        $rsJO = $emklJobOrder->getDetailByColumn('pkey',$arrParam['hidSalesOrderKey'][0]); 
        $jokey = $rsJO[0]['refkey']; 
    
        $rsJO = $emklJobOrder->searchDataRow( array($emklJobOrder->tableName.'.jobtypekey',$emklJobOrder->tableName.'.transportationtypekey'), ' and '. $emklJobOrder->tableName.'.pkey = '. $this->oDbCon->paramString($jokey) );  
      
        if (!empty($rsJO)){ 
            $arrParam['selTypeOfJob'] = $rsJO[0]['jobtypekey']; 
            $arrParam['selAirSea'] = $rsJO[0]['transportationtypekey']; 
        }
    
       return $arrParam;
    }
    
    function normalizeParameter($arrParam, $trim=false){
        $termOfPayment = new TermOfPayment();
        $customCode = new CustomCode();
        $customer = new Customer();
		  
		// tarik ulang nama address
		$rsInvoiceAddress = $customer->getMultipleAddress($arrParam['hidCustomerKey']);
		$rsInvoiceAddress = array_column($rsInvoiceAddress,'name','pkey');
		$arrParam['invoiceAddressName'] = $rsInvoiceAddress[$arrParam['selInvoiceAddress']];
			
        if($arrParam['selCurrency'] == CURRENCY['idr']){
             $arrParam['currencyRate'] = 1;  
        }else{
            // overwrite rate kalo tipe lama
            $rateHeaderType = $this->loadSetting('useInvoiceRateForGL');
            if($rateHeaderType <> 1){
                // loop ambil yg paling besar
                $arrParam['currencyRate'] = 1;  
                foreach($arrParam['detailRate'] as $rateRow){
                    $detailRate = $this->unformatNumber($rateRow);
                    if ($detailRate > $arrParam['currencyRate'])  $arrParam['currencyRate'] = $detailRate;
                }  
            }
        }
			
        // buat jaga2, rate ini utk pencatatan ke GL, bukan konversi total tagihan
        if( $arrParam['currencyRate'] <=0 )  $arrParam['currencyRate'] = 1;
        
		// kalo invoice void 
        $rsCustomCode = $customCode->getDataRowById($arrParam['selCustomCode']);  
        $nogl =  ($rsCustomCode[0]['nogl'] == 1) ? true : false; 
        
        if($nogl){
            $arrParam['selTermOfPayment'] = 1; //tembak system variable
            $arrParam['hidDetailDownpaymentKey'] = array();
            $arrParam['downpaymentAmount'] = array();
            $arrParam['hidDownpaymentKey'] = array();  
            $arrParam['hidDetailPaymentKey'] = array(); 
            $arrParam['paymentMethodValue'] = array();  
            $arrParam['selPaymentMethod'] = array();
        }
        
		
        $itemkey = count($arrParam['hidItemDetailKey']);
        for($i=0;$i<$itemkey;$i++){
            /*if ($isPartial) {
                $arrParam['hidItemDetailKey'][$i] = '';
                continue;
            }*/
            
            if($arrParam['chkService'][$i]) continue; 
            $arrParam['hidItemDetailKey'][$i] = ''; 
        }
        
        
        $detail = $arrParam['hidDetailKey'];
        //$isPartial = $arrParam['chkDownpayment'];
        $arrDONumber = array();
        $arrJOKey = array();
        $arrJOCode = array();
        $arrJOAju = array();
        $emklJobOrder = new EMKLJobOrder();
        
        $invoiceDetailKey = array();
        
        for($i=0;$i<count($detail);$i++){
            //$arrParam['selInvoiceType'][$i] = ($isPartial) ? 1 : $arrParam['selInvoiceType'][$i];
            if($arrParam['chkPick'][$i] == 0 ||
			   ($arrParam['selInvoiceType'][$i] == 1 && empty($arrParam['hidSalesOrderKey'][$i]) ) ||
			   ($arrParam['selInvoiceType'][$i] == 3 && empty($arrParam['hidInvoiceKey'][$i]))
			  ){ 
                $arrParam['selInvoiceType'][$i] = '';
                continue;
			}
			 

			if($arrParam['selInvoiceType'][$i] == 1){
				// pindahin keatas kalo bisa
				$rsJO = $emklJobOrder->getDetailByColumn('pkey',$arrParam['hidSalesOrderKey'][$i]); 
				$arrParam['hidSalesOrderHeaderKey'][$i] = $rsJO[0]['refkey'];
 
				if(!empty($rsJO) && !empty($rsJO[0]['hbl'])) array_push($arrDONumber,$rsJO[0]['hbl']); 
				
                array_push($arrJOKey,$rsJO[0]['refkey']);  
                array_push($arrJOCode,$rsJO[0]['code']); 
                
			}else if($arrParam['selInvoiceType'][$i] == 3){
				// harus ad cache jg utk HBL dan no JOIN
                 
                // utk update cache JO
                array_push($invoiceDetailKey,$arrParam['hidInvoiceKey'][$i]); 
                
			}


            // update cache tax percentage
            $taxDetail = $this->unformatNumber($arrParam['taxDetail'][$i]);
			if ($taxDetail > 0)
			$arrParam['detailTaxPercentage'] = $taxDetail;

        } 
      
        //cari semua cache jo di invoice kecil, 
        //explode terus unique
        
        // khusus kalo selInvoiceTypenya 3
        if(!empty($invoiceDetailKey)){
            $rsInvoiceCol = $this->searchDataRow(array($this->tableName.'.salesordercodecache',$this->tableName.'.salesorderkeycache'),
                                                 ' and '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($invoiceDetailKey,',').') ');
            
            $salesOrderCache = array();
            $salesOrderKeyCache = array();
            foreach($rsInvoiceCol as $invoiceColRow){
                $salesOrderCache = array_merge($salesOrderCache, explode(', ', $invoiceColRow['salesordercodecache']));
                $salesOrderKeyCache = array_merge($salesOrderKeyCache, explode(', ', $invoiceColRow['salesorderkeycache']));
            }
                 
            $arrJOCode = array_merge($arrJOCode,$salesOrderCache);  
            $arrJOKey = array_merge($arrJOKey,$salesOrderKeyCache);  
        }
        
        
        $arrJOCode = array_unique($arrJOCode); 
        $arrJOKey = array_unique($arrJOKey);
            
        $arrParam['chkIncludeTax'] = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;  
        $arrParam['chkTax23'] = (!empty($arrParam['chkTax23'])) ? 1 : 0;  

        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPayment']);  
        if ($rsTOP[0]['duedays'] != 0){   
            for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
                $arrParam['paymentMethodValue'][$i] = 0; 
                $arrParam['hidDetailPaymentKey'][$i] = 0;
            }
        }

        if(!empty($arrJOKey)){
            $rsJO = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.aju',$emklJobOrder->tableName.'.peb',$emklJobOrder->tableName.'.saleskey'),
                                                ' and '.$emklJobOrder->tableName.'.pkey in ('.$this->oDbCon->paramString($arrJOKey,',').')');
            
            $arrJOAju = array_column($rsJO,'aju'); 
            
            if(!empty($rsJO))
                $arrParam['hidSalesKey'] = $rsJO[0]['saleskey'];
        }
        
        $arrParam['doNumber'] = (!empty($arrDONumber)) ? implode(', ',$arrDONumber) : '';
        $arrParam['shipmentNumber'] = (!empty($arrShipmentNumber)) ? implode(', ',$arrShipmentNumber) : '';
        $arrParam['salesordercodecache'] = (!empty($arrJOCode)) ? implode(', ',$arrJOCode) : '';
        $arrParam['salesorderkeycache'] = (!empty($arrJOKey)) ? implode(', ',$arrJOKey) : '';
        $arrParam['salesorderajucache'] = (!empty($arrJOAju)) ? implode(', ',$arrJOAju) : '';
 
        /*if ($arrParam['chkDownpayment']){
            $arrParam['finalDiscount'] = 0;
            $arrParam['selFinalDiscountType'] = 1; 
        }*/
		
		// ===== update refSOHeaderKey di detail Item (level 3), utk keperluan split coa agar lebih mudah
		$arrSODetailKey = $arrParam['hidRefSODetailKey'];
		$sql = 'select '.$emklJobOrder->tableNameDetailItem.'.pkey,'.$emklJobOrder->tableNameDetailItem.'.refheaderkey 
				from '.$emklJobOrder->tableNameDetailItem.' 
				where '.$emklJobOrder->tableNameDetailItem.'.pkey in ('.$this->oDbCon->paramString($arrSODetailKey,',').')';
		$rsSO = $this->oDbCon->doQuery($sql);
        $rsSO = array_column($rsSO,null,'pkey');
		
		for($i=0;$i<count($arrSODetailKey);$i++)
			$arrParam['hidRefSOHeaderKey'][$i] = $rsSO[$arrSODetailKey[$i]]['refheaderkey']; 
		// ===== update refSOHeaderKey di detail Item (level 3)
			
		
        $details = array();
        array_push($details,$this->arrItem);
        $arrParam = $this->prepareMultiLevelDetail($arrParam,$details);
         
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
	
		// taxValue ambil dari recount, karena ad settingan coretax atau bkn
        
        //$rondedType =  $this->loadSetting('invoiceTaxRoundType'); 
        // khusus yg non IDR, jgn dibulatkan
        //if ($arrParam['selCurrency'] != CURRENCY['idr'])  $rondedType = 1; 
        //$arrParam['taxValue'] = $this->getInvoiceRoundedTax($this->unFormatNumber($arrParam['taxValue']),$rondedType);
        $arrParam['taxValue'] = $reCountResult['taxValue'];

		$recountDetail = $reCountResult['recountDetail']; 
		// hitung ulang, kecuali kalo nanti detailny diisi oleh user seperti TMS
 		$arrParam['taxDetail'] = array();
		$arrParam['beforeTaxDetail'] = array();
		$arrParam['taxValueDetail'] = array();
		$arrParam['afterTaxDetail'] = array();
		$arrParam['chkIncludeTaxDetail'] = array();
 
		for($i=0;$i<count($recountDetail);$i++){
			$arrDetailValue = $recountDetail[$i] ;

			$countItemDetail = count($arrDetailValue); // ini yg ad kemungkinan tidak sama total index nya  lompat
              
            foreach($arrDetailValue as $key=>$itemRow){ 
				$arrParam['hidContainerDetailKey'][$i][$key] = $itemRow['hidContainerDetailKey'];
				$arrParam['taxDetail'][$i][$key] = $itemRow['taxDetail'];
				$arrParam['beforeTaxDetail'][$i][$key] = $itemRow['beforeTaxDetail'];
				$arrParam['taxValueDetail'][$i][$key] = $itemRow['taxValueDetail'];
				$arrParam['afterTaxDetail'][$i][$key] = $itemRow['afterTaxDetail'];
				$arrParam['chkIncludeTaxDetail'][$i][$key] = $itemRow['chkIncludeTaxDetail'];
				$arrParam['subtotalDetail'][$i][$key] = $itemRow['subtotalDetail'];

                $arrParam['beforeTaxDetailValueForTax'][$i][$key] = $itemRow['beforeTaxDetailValueForTax'];
				$arrParam['taxDetailValueForTax'][$i][$key] = $itemRow['taxDetailValueForTax'];            }
			 
		}
         
        
        $arrParam = parent::normalizeParameter($arrParam,true);
        
        return $arrParam;
    }

    function getItemDetail($refkey,$reffield = 'refkey',$criteria = ''){
        // gk boleh di join dengan detail kaarena sonehow (blm dicek) di form invoice jd gk muncul detailnya)
        
        $sql = 'select 
                '. $this->tableNameItemDetail. '.*, 
                '.$this->tableItem.'.name as itemname ,
                '.$this->tableItem.'.servicecost,
                '.$this->tableItem.'.iscontainer,
                '.$this->tableItem.'.costcoakey,
                '.$this->tableItem.'.revenuecoakey,
                '.$this->tableItem.'.iscommission,
                '.$this->tableName.'.currencykey as headercurrencykey,
                '.$this->tableCurrency.'.name as headercurrencyname
            from 
                '.$this->tableName.',
                '.$this->tableNameItemDetail.',
                '.$this->tableItem.',
				'.$this->tableCurrency.'
            where 
                '.$reffield.' in  ('.$this->oDbCon->paramString($refkey,',') . ') and 
                ' . $this->tableNameItemDetail. '.itemkey = ' . $this->tableItem. '.pkey and 
                ' . $this->tableNameItemDetail. '.refheaderkey = ' . $this->tableName. '.pkey and 
                ' . $this->tableName. '.currencykey = ' . $this->tableCurrency. '.pkey'; 
         
		$sql .= ' ' .$criteria;

        return  $this->oDbCon->doQuery($sql);
    } 

    function updateDetailTablesOnCopy($id,$newPkey, $arrTableDetail){ 

        for($k=0;$k<count($arrTableDetail);$k++){
            $rsDetail = $this->getDetailById($id,'','',$arrTableDetail[$k]);

            $sql = 'show columns from ' . $arrTableDetail[$k] ;   
            $rsColumnsName = $this->oDbCon->doQuery ($sql); 

            for ($j=0;$j<count($rsDetail);$j++){
                $fields = '';
                $data = ''; 
                $oldDetailKey = $rsDetail[$j]['pkey'];

                if ($arrTableDetail[$k] == $this->tableNameDetail)  
                     $rsDetail[$j]['pkey'] = $this->getNextKey($this->tableNameDetail);  

                $rsDetail[$j]['refkey'] = $newPkey; 

                for ($i=1;$i<count($rsColumnsName);$i++){

                    $fields .= $rsColumnsName[$i]['Field'];  
                    $data .=   $this->oDbCon->paramString($rsDetail[$j][$rsColumnsName[$i]['Field']]);

                    if ($i <> count($rsColumnsName) - 1){
                      $data .= ',';   
                      $fields.= ',';    
                    }

                }

                $sql = 'insert into ' .$arrTableDetail[$k].'  ('.$fields.') values ('.$data.')'; 
                $this->oDbCon->execute ($sql);	

                if ($arrTableDetail[$k] <> $this->tableNameDetail || empty($rsDetail[$j]['salesorderkey'] ))
                    continue;

                // ============= update detail SO

                $rsItemDetail = $this->getItemDetail($oldDetailKey);
                $sql = 'show columns from ' . $this->tableNameItemDetail;   
                $rsDetailsColumnsName = $this->oDbCon->doQuery($sql); 

               for ($z=0;$z<count($rsItemDetail);$z++){
                    $fields = '';
                    $data = ''; 

                    for ($i=1;$i<count($rsDetailsColumnsName);$i++){

                        $fields .= $rsDetailsColumnsName[$i]['Field'];

                        $rsItemDetail[$z]['refheaderkey'] = $newPkey;
                        $rsItemDetail[$z]['refkey'] = $rsDetail[$j]['pkey']; 

                        $data .= $this->oDbCon->paramString($rsItemDetail[$z][$rsDetailsColumnsName[$i]['Field']]);

                        if ($i <> count($rsDetailsColumnsName) - 1){
                          $data .= ',';   
                          $fields.= ',';    
                        }

                    }

                    $sql = 'insert into ' .$this->tableNameItemDetail.'  ('.$fields.') values ('.$data.')';  
                    $this->oDbCon->execute ($sql);	 
               }


                // ============= end update detail SO

            }  
        }  

    }

    function  afterStatusChanged($rsHeader){
		
		// ambil ulang agar dpt status baru
		$rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
			
        //update qtyinvoice 
        $emklJobOrder = new EMKLJobOrder();
        $customCode = new CustomCode();

        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        $rsCustomCode = $customCode->getDataRowById($rsHeader[0]['customcodekey']);  
        $updateqty =  ($rsCustomCode[0]['noupdateqty'] == 1) ? false : true; 
		
        // gk bisa pake noupdateqty, karena ketika cancel kwitansi besar, patokanyna tetep kwitansi kecil yg dihitung ulang qty nya
		// jadi mau gk mau, kwitansi besar patokanyan dr kwitansi kecil saja kal omau lihat sudah diinvoice semua atau blm
		// kwitansi besar sementara jg update ulang qty, siapa tau kwitansi besar ada yg diisi JO jg
		
		 if($updateqty){
			foreach($rsDetail as $invoiceDetail){  
			   //if ($invoiceDetail['invoicetype'] == 1 && empty($invoiceDetail['salesorderkey']))    continue; 

				$arrSalesOrderKey = array();

				if ($invoiceDetail['invoicetype'] == 1) {
					array_push($arrSalesOrderKey,$invoiceDetail['salesorderkey']);
				}else if ($invoiceDetail['invoicetype'] == 3){
					 
					$invoiceKey = $invoiceDetail['invoicekey'];
					$rsInvoice = $this->getDetailById($invoiceKey);
					$arrSalesOrderKey = array_column($rsInvoice,'salesorderkey');
				}

			   foreach($arrSalesOrderKey as $salesorderkey)	
				$emklJobOrder->updateQtyInvoiced($salesorderkey);
			}  
		 }
		 
		 

	   // update status kwitansi kecil 
		
		$arrInvoiceKey = array();
		foreach($rsDetail as $invoiceDetail)
			if ($invoiceDetail['invoicetype'] == 3) array_push($arrInvoiceKey,$invoiceDetail['invoicekey']);
		
		if(!empty($arrInvoiceKey)){
			$rsInvoice = $this->searchDataRow(array($this->tableName.'.pkey', $this->tableName.'.statuskey'),
										 ' and '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrInvoiceKey,',').') ');

			foreach($rsInvoice as $row){  
				// error kalo kwitansi kecilnya sudah dibatalin.. jd harus diskip
				if($row['statuskey'] == 4) continue;


				if($row['statuskey'] == 2 && $rsHeader[0]['statuskey'] == 2)
					$this->changeStatus($row['pkey'],3,'',false, true);
				else if($row['statuskey'] == 3 && $rsHeader[0]['statuskey'] == 4)
					$this->changeStatus($row['pkey'],2,'',false, true);  
			}
		}
		
			
      
        $customerDownpayment = new CustomerDownpayment();
        $rsDownpayment = $this->getDownpaymentDetail($rsHeader[0]['pkey']);
        for($i=0;$i<count($rsDownpayment); $i++){  
           $customerDownpayment->updateOutstanding($rsDownpayment[$i]['downpaymentkey'],true); 
        }
       
        
        $nogl =  ($rsCustomCode[0]['nogl'] == 1) ? true : false; 
        
        // selain utk jenis kwitansi kecil OKL
        if(!$nogl) {  
            // update tgl Aging AR 
            // kalo tgl nya 0000-00-00 pake tgl ar  
            
            $agingType = $this->loadSetting('arapAgingDate');
            
            $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'))['key'];  
            $receivedDate = $rsHeader[0]['receiveddate'];

//            $agingdate = ($receivedDate == '0000-00-00') ? 'trdate' : $this->oDbCon->paramString($rsHeader[0]['receiveddate']);
            
            if ($receivedDate == '0000-00-00'){
                $agingdate = 'trdate';
                $dateField = 'trdate';
            }else{
                $agingdate = $this->oDbCon->paramString($rsHeader[0]['receiveddate']); 
                $dateField = ($agingType == 2) ? $this->tableAR.'.agingdate' : $this->tableAR.'.trdate'; 
            }


            $sql = 'update 
                        '.$this->tableAR.'
                    set  
                        '.$this->tableAR.'.agingdate = '.$agingdate.' ,
                        '.$this->tableAR.'.duedate = '.$dateField.' + interval  ' .$this->tableAR.'.duedays day  
                    where
                    '.$this->tableAR.'.statuskey <>  4 and
                    '.$this->tableAR.'.reftabletype = '.$this->oDbCon->paramString($tablekey).' and 
                    '.$this->tableAR.'.refheaderkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']); 

            $this->oDbCon->execute($sql);
        }  
    }
    
    
    /*function afterPrintTransaction($rsHeader){  
         parent::afterPrintTransaction($rsHeader);    
        
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }*/
    
        
    function validateConfirm($rsHeader){ 
         
        $id = $rsHeader[0]['pkey'];
        $customerkey = $rsHeader[0]['customerkey'];
        $rate = $rsHeader[0]['rate'];
        
        $emklJobOrder = new EMKLJobOrder();
        $termOfPayment = new TermOfPayment();
        $customCode = new CustomCode();
    
 
		 // kalo invoice void, langsung return aj
        $rsCustomCode = $customCode->getDataRowById($rsHeader[0]['customcodekey']);  
        $nogl =  ($rsCustomCode[0]['nogl'] == 1) ? true : false; 
		
        $rsDetail = $this->getDetailById($id); 
        $rsPayment = $this->getPaymentMethodDetail($id); 
        $rsDownpayment = $this->getDownpaymentDetail($id);
  
        
        // cek detail masih sama tidak dengan sales detail
        // cek qty, jenis services, currency, rate
        
        
        // cek rate header sudah berubah atau blm, agar kompatible dengan transisi data dengan model rate di invoice header
        $rateHeaderType = $this->loadSetting('useInvoiceRateForGL');
        if($rateHeaderType <> 1 && $rsHeader[0]['currencykey'] <> CURRENCY['idr']){ 
            if($this->getDetailRate($id) <> $rsHeader[0]['rate']) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[906]);   
        }
        
        
        
        $totalDetails = count($rsDetail);
        $detailsChanged = false; 
        for($i=0;$i<$totalDetails;$i++){
            if($rsDetail[$i]['invoicetype'] == 1){
				// ambil rate setiap job order, masih sama atau gk
				$rsJODetail = $emklJobOrder->getDetailByColumn('pkey',$rsDetail[$i]['salesorderkey']);
				$joRate = $rsJODetail[0]['rate'];

				// cek nilai qty dan harga utk setiap detail masih sama atau gk
				// sementara berdasarkan itemkey saja dulu. masalah kalo ad 2 itemkey yg sama.
				$rsItemDetails = $this->getItemDetail($rsDetail[$i]['pkey']);
				//$sodetailitemkey = array_column($rsItemDetails,'refsodetailkey');
				//$rsItemDetails = array_column($rsItemDetails,null,'itemkey');
				$rsItemDetails = array_column($rsItemDetails,null,'refsodetailkey');

				$rsSODetailItem = $emklJobOrder->getItemDetail($rsDetail[$i]['salesorderkey']);
				//$rsSODetailItem = array_column($rsSODetailItem,null,'servicekey');
				$rsSODetailItem = array_column($rsSODetailItem,null,'pkey');

				foreach($rsItemDetails as $sodetailkey => $itemDetail){

					// kalo itemnya sudah tidak ada
					if (!isset($rsSODetailItem[$sodetailkey])){ 
					   $detailsChanged = true; 
					   break; 
					}

					// kalo rate berubah
					if($joRate <> $itemDetail['rate']){ 
					   $detailsChanged = true; 
					   break;
					} 

					if ($itemDetail['qtyinbaseunit'] <> $rsSODetailItem[$sodetailkey]['qty'] || 
						$itemDetail['priceinunit'] <> $rsSODetailItem[$sodetailkey]['priceinunit'] ||
						$itemDetail['currencykey'] <> $rsSODetailItem[$sodetailkey]['currencykey']){  

						$detailsChanged = true;
						break; 
					}
				} 
			}else if ($rsDetail[$i]['invoicetype'] == 3){
				
				$rsInvoice = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.code',$this->tableName.'.statuskey',$this->tableName.'.grandtotal'),
												 ' and '.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($rsDetail[$i]['invoicekey']).'
												   and '.$this->tableName.'.statuskey in (2) ');
				
				// cek status kwitansi kecil masih konfirmasi gk
				// cek ad perubahan nilai tdk 
				if(empty($rsInvoice) || $rsInvoice[0]['grandtotal'] <> $rsDetail[$i]['amount']){  
					$detailsChanged = true;
					break;
				}
				 
			}
            
        }
         
        if($detailsChanged)
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[906]); 
          
        $balance = 0;
        $totalPayment = 0;
        $totalDownpayment = 0;

        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];
        
        
        for($i=0;$i<count($rsDownpayment); $i++)
            $totalDownpayment += $rsDownpayment[$i]['amount'];
 

        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;  
  
        
        $balance = $totalPayment + $totalDownpayment - $rsHeader[0]['grandtotal'];   
        
        // balance harus dikali rate dulu kalo bkn IDR
        if ($rsHeader[0]['currencykey'] <> CURRENCY['idr']){
            // cek rate gk boleh 1
            if($rsHeader[0]['rate'] == 1)
                  $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['rate'][5]); 
        }
		
        // utk rate nanti harus direvisi dulu, kalo IDR reate header harus 1 
        
        if ( !$nogl && $isCash ){  
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if(($balance * $rate) < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if (($balance * $rate) > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }

        
         for($i=0;$i<count($rsDownpayment);$i++) {   
            
            // validasi DP masi available gk 
            if($rsDownpayment[$i]['downpaymentstatuskey'] <> 2){ 
                $this->addErrorLog(false,$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][9]);
            }else{
                if ($customerkey <> $rsDownpayment[$i]['downpaymentcustomerkey'])
                    $this->addErrorLog(false,$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][6]); 

                // validasi nilai DP masi mencukupi gk 
                if ($rsDownpayment[$i]['amount'] > $rsDownpayment[$i]['downpaymentoutstanding'] )
                    $this->addErrorLog(false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][8].' ('.$this->lang['outstanding']. ': ' .$this->formatNumber($rsDownpayment[$i]['downpaymentoutstanding']['outstanding']).')');  
            }
                
        }
         
        // validasi gudang harus sama
        if($this->loadSetting('invoiceStrictWarehouse') == 1){
            $rsJobOrder = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.code',$emklJobOrder->tableName.'.warehousekey'),
                                                        ' and '.$emklJobOrder->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'refsalesorderheaderkey'),',').')');
              
            foreach($rsJobOrder as $joRow){
             if($joRow['warehousekey'] <> $rsHeader[0]['warehousekey'])
                 $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$joRow['code'].', '.$this->errorMsg[905]); 
            } 
        }
        

        
         for($i=0;$i<$totalDetails;$i++){
            if (empty($rsDetail[$i]['salesorderkey']))
                continue;
            
            //cek jenis tax
            $sql = 'select 
                        '.$this->tableName.'.code,
                        '.$this->tableName.'.taxpercentage,
                        '.$this->tableName.'.ispriceincludetax 
                    from 
                        '.$this->tablePartialInvoice.',
                        '.$this->tableName.'
                    where 
                        '.$this->tablePartialInvoice.'.refkey = '.$this->oDbCon->paramString($rsDetail[$i]['salesorderkey']).' and
                        '.$this->tablePartialInvoice.'.customcodekey = '.$this->oDbCon->paramString($rsHeader[0]['customcodekey']).' and
                        '.$this->tablePartialInvoice.'.refinvoicekey = '.$this->tableName.'.pkey';
              
            $rsTax =  $this->oDbCon->doQuery($sql);
             
            $hash = md5($rsHeader[0]['taxpercentage'] . $rsHeader[0]['ispriceincludetax']);
             
            foreach($rsTax as $row){   
                if ( md5($row['taxpercentage'].$row['ispriceincludetax']) <> $hash){
                     $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg['truckingServiceOrderInvoice'][5]);
                } 
            }

        } 
          
        for($i=0;$i<$totalDetails;$i++){
//            if ($rsDetail[$i]['invoicetype'] == 1 && empty($rsDetail[$i]['salesorderkey']))
//                continue;

			if($rsDetail[$i]['invoicetype'] == 1){
				$rsSO  = $emklJobOrder->getDataRowById($rsDetail[$i]['refsalesorderheaderkey']);
				if ($rsSO[0]['statuskey'] > 2){ 
					 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsSO[0]['code'].'</strong>. ' . $this->errorMsg['emklOrderInvoice'][2]);
				}else if ($rsSO[0]['statuskey'] == 4){ 
					 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsSO[0]['code'].'</strong>. ' . $this->errorMsg[507]); 
				}else{ 
					// cek qty diinvoice berlebihan ap gk
					//ambil semua yg blm diinvoiced. kalo ad detail yg tdk dlm array itu, cancel

					$rsUnInvoiced = $emklJobOrder->getUnInvoicedItemDetail($rsDetail[$i]['salesorderkey']); 
					$arrQtyUnInvoiced = array_column($rsUnInvoiced,'outstandingqty','joinkey');  

					$rsItemDetail = $this->getItemDetail($rsDetail[$i]['pkey']);
					for($j=0;$j<count($rsItemDetail);$j++){ 
						$joinkey = $rsItemDetail[$j]['refsodetailkey'].'-'.$rsItemDetail[$j]['itemkey'];
						 if( $arrQtyUnInvoiced[$joinkey] - $rsItemDetail[$j]['qtyinbaseunit']  < 0)  
							$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsSO[0]['code'].'</strong>. <strong>'.$rsItemDetail[$j]['itemname'].'</strong>, ' . $this->errorMsg[508]);
					}

				} 

				// buat validasi kalo ada 2 form, dan DP sudah pernah dipakai disalah satu form 
				$totalInvoiced = $emklJobOrder->getTotalInvoicedAndOutstanding($rsDetail[$i]['salesorderkey'], $rsHeader[0]['customcodekey']); 
				if($rsDetail[$i]['salesordertotalinvoiced']<>$totalInvoiced['outstanding'])
					$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsSO[0]['code'].'</strong>. ' . $this->errorMsg['truckingServiceOrderInvoice'][6]);        

			 }elseif($rsDetail[$i]['invoicetype'] == 3){
				// jensi kwitansi
				$rsInvoice = $this->getDataRowById($rsDetail[$i]['invoicekey']);
				if ($rsInvoice[0]['statuskey'] != 2){ 
					 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsInvoice[0]['code'].'</strong>. ' . $this->errorMsg['emklOrderInvoice'][5]);
				}

			}
            
        } 

       
    } 

    function confirmTrans($rsHeader){

        $termOfPayment = new TermOfPayment();
        $warehouse = new Warehouse();
        $emklJobOrder = new EMKLJobOrder();
        $customCode = new CustomCode();
        $currency = new Currency();
		
        $defaultCurrencyKey = $currency->getDefaultData();
        
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        $baseRate = 0; // udah gk kepake
//        $baseRate = $this->getDetailRate($rsHeader[0]['pkey']);
        $usePPNDetail = $this->loadSetting('usePPNDetail');
        
        // kalo invoice void, langsung return aj
        $rsCustomCode = $customCode->getDataRowById($rsHeader[0]['customcodekey']);  
        $nogl =  ($rsCustomCode[0]['nogl'] == 1) ? true : false; 
        if($nogl) return; // utk jenis kwitansi kecil OKL

        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;  
         
        // SUDAH GK PERLU
        // karena di detail bisa diedit ratenya
        // diheader hanya alatt bantu utk ratenya
        //$rate = ($rsHeader[0]['currencykey'] == $defaultCurrencyKey) ? 1 : $rsHeader[0]['rate']; 
        
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'));  
		
        //update ar service
        if (!$isCash){ 
            $ar = new AR(); 
            $customer = new Customer();

            $topkey = $rsHeader[0]['termofpaymentkey']; 
            $rsTOP = $termOfPayment->getDataRowById($topkey);   
            $rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);     
            $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];

            $arrParam = array();	 
            $desc = array();
                
			$arrParam['code'] = 'xxxxxx';
			$arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey']; 
			$arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
			$arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
			$arrParam['hidRefCode'] =  $rsHeader[0]['code'];
			$arrParam['hidRefCode2'] =  $rsHeader[0]['donumber'];
			$arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
			$arrParam['hidRefTable'] = $tablekey['key'];
			// * $rate; // gk perlu, karena di detailnya sudah dihitung ratenya.
			// di round biar gk ad decimal 3, masalah di payment
			$arrParam['amount'] = round( ($rsHeader[0]['grandtotal'] - $rsHeader[0]['totaldownpayment']) , 2) ;

			if($rsHeader[0]['currencykey']==CURRENCY['idr']){
				$arrParam['currencyRate'] = 1; 
				$arrParam['selCurrency'] = CURRENCY['idr'];
			}else{
				$arrParam['currencyRate'] = $rsHeader[0]['rate'];
//				$arrParam['currencyRate'] = $baseRate;
				$arrParam['selCurrency'] = $rsHeader[0]['currencykey']; 
			}

			$arrParam['amountIDR'] = $arrParam['amount'] * $arrParam['currencyRate']; 
			$arrParam['trDesc'] = implode(chr(13),$desc) ;
			$arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
            $arrParam['trAgingDate'] = $arrParam['trDate'];
            
            $arrParam['dueDays'] = $top;
            
			$date = new DateTime($rsHeader[0]['trdate']);
			$date->add(new DateInterval('P'.$top.'D'));
			$arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
			$arrParam['createdBy'] = 0;
			$arrParam['overwriteGL'] = 1;
			$arrParam['islinked'] = 1;
			$arrParam['selARType'] = AR_TYPE['serviceOrder'];
			$arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];//$warehouse->getDefaultData();
			$arrParam['salesordercodecache'] = $rsHeader[0]['salesordercodecache'];

			$returnVal = $ar->addData($arrParam,false); 
			
			// add error log atau throw ??
			if (!$returnVal[0]['valid']) 
			   $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$returnVal[0]['message'], true);  
				
			$rsHeader[0]['arKey'] = $returnVal[0]['data']['pkey'];
			$rsHeader[0]['arCode'] = $returnVal[0]['data']['code'];
            
        } 
        
		// kalo pake prepaid expense
		$usePrepaidExpense = $this->loadSetting('usePrepaidExpense');
 
        if($usePrepaidExpense == 1){
          
            $prepaidExpense = new PrepaidExpense();
            $costReconsile = new CostReconsile();

            $arrCostReconsile = array();	
            $arrCostReconsile['code'] = 'xxxxx';
            $arrCostReconsile['hidInvoiceKey'] = $rsHeader[0]['pkey'];
            $arrCostReconsile['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
            $arrCostReconsile['reftabletype'] = $tablekey['key'];
            $arrCostReconsile['refHeaderCode'] = $rsHeader[0]['code'];

/*            
           if($rsHeader[0]['currencykey']==CURRENCY['idr']){
                $arrCostReconsile['currencyRate'] = 1; 
                $arrCostReconsile['selCurrency'] = CURRENCY['idr']; 
            }else{
                $arrCostReconsile['currencyRate'] = $baseRate;
                $arrCostReconsile['selCurrency'] = $rsHeader[0]['currencykey'];
           }*/
             
            $arrCostReconsile['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
            $arrCostReconsile['selStatus'] = 1;
            //$arrCostReconsile['overwriteGL'] = 1;
            //$arrCostReconsile['hidDetailKey'] = array();
 
			// jenis OKA
			$arrInvoiceKey =  array_column($rsDetail,'invoicekey'); 
            $rsDetailInvoice = $this->getDetailWithRelatedInformation($arrInvoiceKey); 
            $arrJoKey =  array_column($rsDetailInvoice,'refsalesorderheaderkey'); 
			
			// kalo tipe normal
			// sofar kita tidak misahin dulu, karena by default kalo OKA akan direturn diatas karena sifatnya nogl, dan
			// utk jenis normal, harusnya gk ad yg pilih nya jenis invoice diatas
            $arrJoKey2 =  array_column($rsDetail,'refsalesorderheaderkey'); 
			$arrJoKey = array_merge($arrJoKey,$arrJoKey2);
			
			$rsOutstanding = $prepaidExpense->searchDataRow( array($prepaidExpense->tableName.'.pkey', $prepaidExpense->tableName.'.amount',
																   $prepaidExpense->tableName.'.outstanding', $prepaidExpense->tableName.'.currencykey'), 
						  'and  '. $prepaidExpense->tableName.'.salesorderkey in ('. $this->oDbCon->paramString($arrJoKey ,',').') and 
						  '. $prepaidExpense->tableName.'.outstanding > 0 and '. $prepaidExpense->tableName.'.statuskey in (1,2)'    
			);

			$countDetail = count($rsOutstanding);
			
			if($countDetail > 0){
                $arrDetail = array();
				
				$arrCostReconsile['hidTotalRows'] = array(array($countDetail)); 

				for($i=0;$i<count($rsOutstanding);$i++){  
   						$currencykey = $rsOutstanding[$i]['currencykey'];

                        if(!isset($arrDetail[$currencykey]))  $arrDetail[$currencykey] = array();
                        
                        $arrCostReconsileDetail = array();
                        $arrCostReconsileDetail['hidDetailKey'] = 0;
                        $arrCostReconsileDetail['hidReconsileKey'] = $rsOutstanding[$i]['pkey'];
                        $arrCostReconsileDetail['amount'] = $rsOutstanding[$i]['outstanding']; // kalo pake amount, kalo sudah ad realisasi sebagian, jadinya overpay
                        $arrCostReconsileDetail['outstanding'] = $rsOutstanding[$i]['outstanding'];
                        
                        array_push($arrDetail[$currencykey], $arrCostReconsileDetail);
				}
                                    
                $arrCostReconsile['hidTotalRows'] = array(array(count($arrDetail))); 
 
                foreach($arrDetail as $key => $row){

					
                    //add header
                    $arrCostReconsile['currencyRate'] = ($key == CURRENCY['idr']) ? 1 : $baseRate; 
                    $arrCostReconsile['selCurrency'] = $key; 
                        
                    $arrCostReconsile['hidDetailKey']= array();
                    $arrCostReconsile['hidReconsileKey']= array();
                    $arrCostReconsile['amount']= array();
                    $arrCostReconsile['outstanding']= array();
                    $arrCostReconsile['chkPick']= array();

                    for($k=0;$k<count($row);$k++){ 
						$arrCostReconsile['hidDetailKey'][$k] = $row[$k]['hidDetailKey'];
						$arrCostReconsile['hidReconsileKey'][$k] = $row[$k]['hidReconsileKey'];
						$arrCostReconsile['amount'][$k] = $row[$k]['amount'];
						$arrCostReconsile['outstanding'][$k] = $row[$k]['outstanding'];
						$arrCostReconsile['chkPick'][$k] = 1;
					}
 
					$arrayToJs  = $costReconsile->addData($arrCostReconsile);  
                     
					// add error log atau throw ??
					if (!$arrayToJs[0]['valid']) 
					   $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true);  

                }
				 
				
			}
           		
        }

        // update invoicekey di SPK
        //cari berapa step utk jenis JO nya   
        // kalo pake ppn detail gk bisa pake ini, karena tax percentange nya bisa lebih dari 1
        // kedepan jg pakenya generate csv saja

         if($rsHeader[0]['taxvalue'] <> 0 && !$usePPNDetail){
            $invoiceTax = new InvoiceTax(); 
			
            $arrInvoiceTax = array();	
            $arrInvoiceTax['code'] = 'xxxxxx';
            $arrInvoiceTax['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrInvoiceTax['selType'] = $tablekey['key'];
            $arrInvoiceTax['trDate'] = date('d / m / Y'); 
            $arrInvoiceTax['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrInvoiceTax['selTaxPercentage'] = $rsHeader[0]['taxpercentage'] ;

            $result = $invoiceTax->addData($arrInvoiceTax); 
        }
     
        $this->addPartialInvoice($rsHeader,$rsDetail); 
        
        //update jurnal umum
        $this->updateGL($rsHeader); 
            
    } 
    
  function getCostReconsileByInvoice($pkey,$currencykey='',$warehousekey=''){
        $prepaidExpense = new PrepaidExpense();
        $service = new Service(SERVICE);
        $emklPurchaseOrder = new EMKLPurchaseOrder();
   
	    // cari jokey utk detail invoice 
	  	$invoiceKey = $pkey; 
        $rsDetail = $this->getDetailById($invoiceKey);
		$arrJoKey = array_column($rsDetail,'refsalesorderheaderkey');  
		$arrInvoiceKey = array_column($rsDetail,'invoicekey');  
	   
	  	// cek kalo ada yg jenisnya kwitansi, harus ambil ulang jo ref key nya
	  	// terus merge detail jo nya
	  	$arrDetailFromInvoiceType = array();
	  	$rsDetail = $this->getDetailByColumn($this->tableNameDetail.'.refkey',$arrInvoiceKey);
		$rsDetail = array_column($rsDetail,'refsalesorderheaderkey');  
	  	$arrJoKey = array_merge($arrJoKey,$rsDetail);
	  
        $tablekey = $this->getTableKeyAndObj($emklPurchaseOrder->tableName,array('key'))['key']; 
      
        $currencyCriteria = (!empty($currencykey)) ? ' and '. $prepaidExpense->tableName.'.currencykey= '. $this->oDbCon->paramString($currencykey) : '';
      
	  	// cari dr prepaidExpense 
		$rsOutstanding = $prepaidExpense->searchDataRow( array($prepaidExpense->tableName.'.pkey',$prepaidExpense->tableName.'.code',$prepaidExpense->tableName.'.costkey',$prepaidExpense->tableName.'.refcode',$prepaidExpense->tableName.'.amount', $prepaidExpense->tableName.'.outstanding'), 
														'and  '. $prepaidExpense->tableName.'.salesorderkey in ('. $this->oDbCon->paramString($arrJoKey,',').')
                                                        and  '. $prepaidExpense->tableName.'.reftabletype = '. $this->oDbCon->paramString($tablekey).' 
														and  '. $prepaidExpense->tableName.'.outstanding > 0 
														and  '. $prepaidExpense->tableName.'.statuskey in (1,2)'.
                                                        $currencyCriteria
		);
      
	    $rsService = $service->searchDataRow(array($service->tableName.'.pkey', $service->tableName.'.name'),
										    ' and '.$service->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsOutstanding,'costkey'),',').')');
	  	$rsServiceCol = array_column($rsService,null,'pkey');
	   
		for($j=0;$j<count($rsOutstanding);$j++){
			$rsService = $rsServiceCol[$rsOutstanding[$j]['costkey']];
			$rsOutstanding[$j]['servicename'] = $rsService['name']; 
		}
 
        return $rsOutstanding;
        
    }

    function addPartialInvoice($rsHeader,$rsDetail){ 
          
        $emklJobOrder = new EMKLJobOrder();
        
        foreach ($rsDetail as $invoiceDetail) { 
            if (empty($invoiceDetail['salesorderkey']))  continue;
            
            if ($rsHeader[0]['isdownpayment']){
                $amount = $invoiceDetail['amount'];
            }else{
                $rsDP = $emklJobOrder->getTotalInvoicedAndOutstanding($invoiceDetail['salesorderkey'],$rsHeader[0]['customcodekey']);
                    
                $amount = ($invoiceDetail['salesordergrandtotal']  >= $rsDP['outstanding'] ) ? $rsDP['outstanding'] : $invoiceDetail['salesordergrandtotal'];
                $amount *= -1;
                
                //update lock status
                foreach($rsDP['rsTotalnvoiced'] as $row){
                    $sql = 'update 
                                    '.$this->tablePartialInvoice.'  
                            set 
                                    reflinkinvoiceheaderkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).', 
                                    reflinkinvoicedetailkey = '.$this->oDbCon->paramString($invoiceDetail['pkey']).' 
                            where pkey = ' .$row['pkey'].' and reflinkinvoiceheaderkey = 0' ;
                    $this->oDbCon->execute($sql);
                }
                    
            }

            if ($amount == 0) continue;
            
            $sql = 'insert into '.$this->tablePartialInvoice.' 
                       (refkey,refinvoicekey,customcodekey,amount) 
                    values(
                            '.$this->oDbCon->paramString($invoiceDetail['salesorderkey']).',
                            '.$this->oDbCon->paramString($rsHeader[0]['pkey']).',
                            '.$this->oDbCon->paramString($rsHeader[0]['customcodekey']).',
                            '.$this->oDbCon->paramString($amount).' 
                        )
                    '; 
           $this->oDbCon->execute($sql);
        }
    }
       
    function deletePartialInvoice($id){  
        
            //update lock status 
            $sql = 'update 
                            '.$this->tablePartialInvoice.'  
                    set 
                            reflinkinvoiceheaderkey = 0, 
                            reflinkinvoicedetailkey = 0
                    where reflinkinvoiceheaderkey = ' .$this->oDbCon->paramString($id) ;
            $this->oDbCon->execute($sql);
        
           $sql = 'delete from '.$this->tablePartialInvoice.' where refinvoicekey = '.$this->oDbCon->paramString($id); 
           $this->oDbCon->execute($sql);
    }
       
    function validateCancel($rsHeader,$autoChangeStatus=false){
        $id = $rsHeader[0]['pkey'];
        
        $rsARKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
		
        // cek invoice partial
        // cari di partial invoice
        $sql = 'select pkey from '.$this->tablePartialInvoice.' where refinvoicekey = '.$rsHeader[0]['pkey'].' and reflinkinvoiceheaderkey <> 0 ';
        $rsPartial = $this->oDbCon->doQuery($sql); 
        if(!empty($rsPartial)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['emklOrderInvoice'][6],true);
        
        // cari di kwitansi besar
        $sql = 'select '.$this->tableName.'.pkey,'.$this->tableName.'.code 
				from '.$this->tableName.','.$this->tableNameDetail.'
				where '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
					  '.$this->tableNameDetail.'.invoicekey = '.$rsHeader[0]['pkey']. ' 
					  and '.$this->tableName.'.statuskey in (2,3)';
        $rsPartial = $this->oDbCon->doQuery($sql); 
        if(!empty($rsPartial)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['emklOrderInvoice'][6]. ' (<b>'.$rsPartial[0]['code'].'</b>)',true);
        
        //cek DP
        $customerDownpayment = new CustomerDownpayment(); 
        $rsDP = $customerDownpayment->searchData($customerDownpayment->tableName.'.refheaderkey', $id, true, ' and '.$customerDownpayment->tableName.'.statuskey in (2,3) ');
        if(!empty($rsDP)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['downpayment'][6],true);
        
		//cek ad AR Service terbayar
        $ar = new AR();
		$rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and '.$ar->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$ar->tableName.'.statuskey = 2 or '.$ar->tableName.'.statuskey = 3)');
		if(!empty($rsAR)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ar'][2],true);
	  
        //cek ad AP Employee Commission terbayar
        $apEmployeeCommission = new APEmployeeCommission(); 
		$rsAP = $apEmployeeCommission->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and '.$apEmployeeCommission->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$apEmployeeCommission->tableName.'.statuskey in (2,3) )');
		if(!empty($rsAP)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.implode(', ',array_column($rsAP,'code')).'</strong>. ' .$this->errorMsg['apEmployeeCommission'][2],true);
	  
	  
        /*
        // dipindah ke validasi AR
        $creditNote = new CreditNote(); 
        $rsCN = $creditNote->getCreditNoteByInvoice($rsHeader[0]['pkey'],'  and '.$creditNote->tableName.'.statuskey in (2,3) ');
        if(!empty($rsCN)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg[201].'<br><strong>'.$rsCN[0]['code'].'</strong> ' .$this->errorMsg['creditNote'][3],true);
       */
        
        // versi lama
        $invoiceTax = new InvoiceTax(); 
        $rsInvoiceTax = $invoiceTax->searchDataRow( array($invoiceTax->tableName.'.code'),
															 ' and '.$invoiceTax->tableName.'.refkey = '.$this->oDbCon->paramString($id).'
															   and '.$invoiceTax->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' 
															   and '.$invoiceTax->tableName.'.statuskey in (2,3) ');
	 
		if(!empty($rsInvoiceTax))  
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsInvoiceTax[0]['code'].'</strong>, ' .$this->errorMsg['emklOrderInvoice'][7] );
       
        // versi baru coretax  
		if(!empty($rsHeader[0]['refvatoutkey'])) {  
           $vatOut = new VatOut();
           $rsVatOut = $vatOut->searchDataRow( array($vatOut->tableName.'.code'),
                                                         ' and '.$vatOut->tableName.'.pkey = '.$this->oDbCon->paramString($rsHeader[0]['refvatoutkey']));
 
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong>, <strong>'.$rsVatOut[0]['code'].'</strong>. ' .$this->errorMsg[201].' ' .$this->errorMsg['emklOrderInvoice'][7]);
        }
		
		$usePrepaidExpense = $this->loadSetting('usePrepaidExpense');
        if($usePrepaidExpense == 1){
            $costReconsile = new CostReconsile();
            $rsCostReconsile = $costReconsile->searchDataRow( array($costReconsile->tableName.'.code'),
															 ' and '.$costReconsile->tableName.'.refkey = '.$this->oDbCon->paramString($id).'
															   and '.$costReconsile->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).'
															   and '.$costReconsile->tableName.'.statuskey in (2,3) ');
            if(!empty($rsCostReconsile)) 
               $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsCostReconsile[0]['code'].'</strong>, ' .$this->errorMsg[225] );
        }
	 } 
     
	function cancelTrans($rsHeader,$copy){ 
		 
        $emklJobOrder = new EMKLJobOrder();
        
		$id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
		  
		if ($rsHeader[0]['statuskey'] == 1) 
        	return; 
         
        //cancel partialinvoice
        $this->deletePartialInvoice($id);
         
        $customerDownpayment = new CustomerDownpayment();  
        $rsDP = $customerDownpayment->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and '.$customerDownpayment->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsDP);$i++) { 
			$arrayToJs = $customerDownpayment->changeStatus($rsDP[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
        
        
		$ar = new AR();
        $rsObjKey = $this->getTableKeyAndObj($this->tableName, array('key')); 
        
        $rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsObjKey['key']).' and '.$ar->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ar->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAR);$i++) { 
			$arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
         
      	/*$creditNote = new CreditNote(); 
        $rsCN = $creditNote->getCreditNoteByInvoice($rsHeader[0]['pkey'],'  and '.$creditNote->tableName.'.statuskey = 1 ');
        for($i=0;$i<count($rsCN);$i++) { 
			$arrayToJs = $creditNote->changeStatus($rsCN[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }*/
         
     
        $invoiceTax = new InvoiceTax();

        $rsInvoiceTax = $invoiceTax->searchDataRow( array($invoiceTax->tableName.'.pkey',$invoiceTax->tableName.'.code'),
												 ' and '.$invoiceTax->tableName.'.refkey = '.$this->oDbCon->paramString($id).'
												   and '.$invoiceTax->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsObjKey['key']).'
												   and '.$invoiceTax->tableName.'.statuskey in (1) ');

		for($i=0;$i<count($rsInvoiceTax);$i++) { 
			$arrayToJs = $invoiceTax->changeStatus($rsInvoiceTax[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
         

		$usePrepaidExpense = $this->loadSetting('usePrepaidExpense'); 
        if($usePrepaidExpense == 1){
            $costReconsile = new CostReconsile();
			$rsCostReconsile = $costReconsile->searchDataRow( array($costReconsile->tableName.'.pkey',$costReconsile->tableName.'.code'),
												 ' and '.$costReconsile->tableName.'.refkey = '.$this->oDbCon->paramString($id).'
												   and '.$costReconsile->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsObjKey['key']).'
												   and '.$costReconsile->tableName.'.statuskey in (1) ');
 
            for($i=0;$i<count($rsCostReconsile);$i++) { 
                $arrayToJs = $costReconsile->changeStatus($rsCostReconsile[$i]['pkey'],4,'',false, true);
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
            }
        }
         
        $apEmployeeCommission = new APEmployeeCommission();  
		$rsAP = $apEmployeeCommission->searchData('','',true,' and  '.$apEmployeeCommission->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$apEmployeeCommission->tableName.'.reftabletype = '.$rsObjKey['key'].' and '.$apEmployeeCommission->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsAP);$i++)  
			$apEmployeeCommission->changeStatus($rsAP[$i]['pkey'],4,'',false, true);   
    		
		if ($copy)
			$this->copyDataOnCancel($id);	  
		   
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
      
	}
    
  
    function getInvoiceByMonth($startPeriod, $endPeriod){
         $sql = 'select 
                    month(trdate) as month,  
                    DATE_FORMAT(trdate, \'%b\')  as monthname, 
                    year(trdate) as year, 
                    sum(grandtotal) as total
                from 
                    '.$this->tableName.'
                where (statuskey = 2 or statuskey = 3) and trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\') 
                    group by year(trdate),month(trdate)';
      
         return $this->oDbCon->doQuery($sql); 
    } 
    
    function getBestSalesAmountByGroup($groupBy, $startPeriod, $endPeriod, $limit = 5){
        // Sales Amount
        
        $sql = 'select 
                  sum('.$this->tableName.'.beforetaxtotal) as amount, 
                  '.$this->tableCustomer.'.name  as customername
                from 
                    '.$this->tableName.', 
                    '.$this->tableCustomer.' 
                where 
                    ('.$this->tableName.'.statuskey = 2 or '.$this->tableName.'.statuskey = 3) and 
                     '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                     trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')  
                 group by 
                    '.$groupBy.'
                 order by amount desc limit ' . $limit;
      
        return $this->oDbCon->doQuery($sql); 
    }   
    
      function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value,
                '.$this->tableName . '.code,
                '.$this->tableName . '.trdate,
                '.$this->tableName . '.grandtotal,
                '.$this->tableCustomer . '.name as customername
            from 
                '.$this->tableName . ',
                '.$this->tableCustomer . ',
                '.$this->tableStatus.'  
            where  		

                '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
        ';
        
        $sql .=  $this->getCompanyCriteria() ;
		
        return $sql; 
    }
      
    function getSalesOrderObj() {
        return new EMKLJobOrder();
    }

    // sementara masih kepake buat validate confirm, selama transisi data
    function getDetailRate($pkey,$detailkey=''){
        $sql = 'select rate from '.$this->tableNameItemDetail.' where refheaderkey = ' .$this->oDbCon->paramString($pkey).' order by rate desc limit 1 ' ;
        $rs = $this->oDbCon->doQuery($sql); 
        
        return (empty($rs)) ? 1 : $rs[0]['rate'];
    }
    
        
    function afterUpdateData($arrParam,$action){
       /* $sql = 'insert into temp (`refkey`, `code`,`sql`,`createdon`) values ('.$this->oDbCon->paramString($arrParam['pkey']).','.$this->oDbCon->paramString($arrParam['code']).',\''.$action.'\',now())';
        $this->setLog($sql,true);
        $this->oDbCon->execute($sql); */
    }
	
	function getTaxPercentageType($pkey,$ppnDetailType = ''){
		
		// sementara masih satu jenis
		// nanti bisa saja, di TMS pp ndi detail, di FMS pp ndi header
		
		// kalo gk dikirim jenis detail PPN nya, di search ulang dulu
//		if ($ppnDetailType == '') 
//			 $usePPNDetail = $this->loadSetting('usePPNDetail');
//	 
//		if (($ppnDetailType == 1){
//			$rsDetail = $this->getItemDetail($pkey,'refheaderkey');
//			$arrDetail = array_column($rsDetail,'taxdetail')
//		}else{
//			$rsDetail = $this->searchDataRow(array($this->tableName.'.taxpercentage'), ' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey));
//			$arrDetail = array_column($rsDetail,'taxpercentage');
//		}
// 
		$rsDetail = $this->searchDataRow(array($this->tableName.'.taxpercentage'), ' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey));
		$arrDetail = array_column($rsDetail,'taxpercentage');
			
         $arrTaxPercentage = array();
         foreach($arrDetail as  $val){
                            
             if($val == 0 ) continue;
                        
             $arrValue = array();
             $arrValue['pkey'] =  $this->formatNumber($val,2);
             $arrValue['taxpercentage'] =  $this->formatNumber($val,2);
                        
             array_push($arrTaxPercentage,$arrValue);
         }
                            
        return $arrTaxPercentage;
    } 
    
    function generateDataForVatOut($searchOptions){
		  
		
		    $sql = 'select  
                ' . $this->tableName . '.code as value,
                ' . $this->tableName . '.pkey,
                ' . $this->tableName . '.code,
                ' . $this->tableName . '.currencykey,
                ' . $this->tableName . '.trdate, 
                sum(' . $this->tableNameItemDetail . '.aftertaxdetailvalue) * ' . $this->tableName . '.rate as aftertaxdetailvalue,
                sum(' . $this->tableNameItemDetail . '.beforetaxdetailvalue) * ' . $this->tableName . '.rate as beforetaxtotal,
                sum(' . $this->tableNameItemDetail . '.taxdetailvalue) * ' . $this->tableName . '.rate as taxvalue,
                ' . $this->tableStatus . '.status,
                ' . $this->tableWarehouse . '.name as warehousename, 
                ' . $this->tableCustomer . '.taxregistrationname as customername, 
                ' . $this->tableCustomer . '.taxregistrationaddress as customeraddress, 
                ' . $this->tableCustomer . '.taxid as npwp, 
                ' . $this->tableCustomer . '.nik, 
                ' . $this->tableCustomer . '.passport, 
                ' . $this->tableCustomer . '.taxid as npwp, 
                '.$this->tableCustomCode.'.name as invoicetype,  
                '.$this->tableCustomCode.'.pkey as invoicetypekey 
            from 
				'.$this->tableName.'
					left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey
					left join  ' . $this->tableWarehouse . ' on ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey
					left join ' . $this->tableCustomCode . ' on ' . $this->tableName . '.customcodekey =  ' . $this->tableCustomCode . '.pkey,
				'.$this->tableNameDetail.',
				'.$this->tableNameItemDetail.', 
                ' . $this->tableStatus . '      
            where 
                ' . $this->tableNameDetail . '.refkey = ' . $this->tableName . '.pkey and
                ' . $this->tableNameItemDetail . '.refkey = ' . $this->tableNameDetail . '.pkey  and
                ' . $this->tableNameItemDetail . '.isreimburse = 0 and
                ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                ' . $this->tableName . '.statuskey in (2,3) and
                ' . $this->tableName . '.grandtotal > 0  
        ';
		
        if ($searchOptions['criteria'] <> '')   $sql .= ' ' . $searchOptions['criteria'];
   
        $sql .= $this->getCompanyCriteria();
 
		$sql .= ' group by ' . $this->tableName . '.pkey';
		$sql .= ' order by ' . $this->tableName . '.trdate asc';
        
        //$this->setLog($sql,true);
        return  $this->oDbCon->doQuery($sql);

    }
    
    	function getItemDetailByHeaderKey($arrHeaderKey, $criteria = ''){
		  $sql = 'select 
                '. $this->tableNameItemDetail. '.*, 
                '.$this->tableItem.'.name as itemname ,
                '.$this->tableItem.'.servicecost,
                '.$this->tableItem.'.iscontainer,
                '.$this->tableItem.'.costcoakey,
                '.$this->tableItem.'.revenuecoakey,
                '.$this->tableItem.'.iscommission,
                '.$this->tableName.'.currencykey as headercurrencykey,
                '.$this->tableCurrency.'.name as headercurrencyname
            from 
                '.$this->tableName.',
                '.$this->tableNameDetail.',
                '.$this->tableNameItemDetail.',
                '.$this->tableItem.',
				'.$this->tableCurrency.'
            where 
                '.$this->tableName.'.pkey in  ('.$this->oDbCon->paramString($arrHeaderKey,',') . ') and 
                ' . $this->tableNameItemDetail. '.itemkey = ' . $this->tableItem. '.pkey and 
                ' . $this->tableName. '.pkey = ' . $this->tableNameDetail. '.refkey and 
                ' . $this->tableNameDetail. '.pkey = ' . $this->tableNameItemDetail. '.refkey and 
                ' . $this->tableName. '.currencykey = ' . $this->tableCurrency. '.pkey'; 
         
		$sql .= ' ' .$criteria;
		
		//$this->setLog($sql,true);
        return  $this->oDbCon->doQuery($sql);
	}
     
   function getTaxPercentageTypeForVatOut($arrInvoiceKey){
		$sql = 'select '.$this->tableNameItemDetail.'.taxdetail
				from  '.$this->tableName.','.$this->tableNameDetail.', '.$this->tableNameItemDetail.' 
				where  	
					'.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and 
					'.$this->tableNameDetail.'.pkey = '.$this->tableNameItemDetail.'.refkey and 
					'.$this->tableName.'.statuskey in (2,3) and
					'.$this->tableNameItemDetail.'.isreimburse = 0 and
					'.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrInvoiceKey, ',').')
				';
		 
		 $rs =  $this->oDbCon->doQuery($sql);
		 return array_column($rs,'taxdetail');
	}
    
     
    function getJODetail($invoicekey){
         
        $objServiceOrder = $this->getSalesOrderObj();
        
        $sql = 'select  
                     '.$this->tableNameDetail.'.pkey , 
                     '.$this->tableNameDetail.'.refkey , 
                     '.$objServiceOrder->tableName.'.pkey as jokey, 
                     '.$objServiceOrder->tableName.'.code as jocode, 
                     '.$objServiceOrder->tableName.'.trdate as jodate
                from 
                    '.$this->tableName.',
                    '.$this->tableNameDetail.',
                    '.$objServiceOrder->tableName.' 
                where   
                    '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($invoicekey,',').') and 
                    '.$this->tableNameDetail.'.invoicetype = 1 and 
                    '.$this->tableNameDetail.'.refsalesorderheaderkey = '.$objServiceOrder->tableName.'.pkey and
                    '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey   
                ';
		 
        return $this->oDbCon->doQuery($sql);
    }
    
    function getRevenueByJO($pkey)
    {


        $sql = '
            select
                coalesce(sum('. $this->tableNameItemDetail .'.aftertaxdetailvalue * '.$this->tableName.'.rate),0) as amount,
                '.$this->tableNameDetail.'.refsalesorderheaderkey as salesorderkey
            from
                '.$this->tableNameItemDetail .',
                '.$this->tableNameDetail.',
                '.$this->tableName.'
            where
                '. $this->tableNameItemDetail .'.refkey = '. $this->tableNameDetail .'.pkey and
                '. $this->tableNameDetail .'.refkey = '. $this->tableName .'.pkey and
                '. $this->tableNameDetail .'.refsalesorderheaderkey in ('. $this->oDbCon->paramString($pkey,',') .') and
                '. $this->tableName .'.statuskey in (2,3)  group by refsalesorderheaderkey
        ';

        $result = $this->oDbCon->doQuery($sql);

        return $result;

    }
    

    function getDataForGrossPNLFFInvoiceReport($criteria = '',$order = '')
    {
        
        $sql = '
            SELECT
                '.$this->tableName.'.* ,
                '.$this->tableCustomer.'.name as customername,
                '.$this->tableStatus.'.status as statusname,
                created.name as createdname

            FROM 
                '.$this->tableStatus.',
                '.$this->tableName.'
                    left join '.$this->tableEmployee.' created on  '.$this->tableName.'.createdby = created.pkey   
                    left join ' . $this->tableCustomer . '  on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey

            WHERE 
                ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
        ';

        $sql .= ' ' . $criteria;

        $sql .= $this->getWarehouseCriteria();
        $sql .= $this->getCustomerCriteria();
        $sql .= $this->getSalesCriteria();

        $sql .= ' ' . $order;

        $result = $this->oDbCon->doQuery($sql);

        return $result;

    }

    function getTotalCostCIPByInvoice($pkey, $inIDR = true, $criteria = '')
    {
        $costReconsile = new CostReconsile();
        $prepaidExpense = new PrepaidExpense();

        $tabletype = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];

        if($inIDR){ 
            $query = 'coalesce(sum('.$costReconsile->tableNameDetail.'.amount * '.$prepaidExpense->tableName.'.rate),0) as amount,
					    1 as rate'; 
        }else{
            $query = 'coalesce(sum(' . $costReconsile->tableNameDetail . '.amount),0) as amount';
        }

        $sql = 'select 
                    ' . $query . ',
                    '. $costReconsile->tableName.'.pkey,
                    '. $costReconsile->tableName.'.code,
                    '. $costReconsile->tableName.'.refkey,
                    '. $costReconsile->tableName.'.currencykey
                from 
                    '.$costReconsile->tableName.', 
                    '.$costReconsile->tableNameDetail.',
                    '.$prepaidExpense->tableName.'
                where 
                    '.$costReconsile->tableName.'.statuskey in (2,3) and
                    '.$costReconsile->tableName.'.pkey =  '.$costReconsile->tableNameDetail.'.refkey and
                    '.$costReconsile->tableName.'.reftabletype = '. $tabletype.' and
                    '.$costReconsile->tableNameDetail.'.refreconsilekey = '. $prepaidExpense->tableName.'.pkey and
                    '.$costReconsile->tableName.'.refkey in ('.$this->oDbCon->paramString($pkey,',') .')
            ';

            if(!empty($criteria)) {
                $sql .= $criteria;
            }

            if($inIDR) {
                $sql .= ' group by '.$costReconsile->tableName.'.refkey';
            }else{
                $sql .= ' group by '.$costReconsile->tableName.'.refkey, '. $costReconsile->tableName.'.currencykey';
            }

            $rs = $this->oDbCon->doQuery($sql);
        
            return $rs;

    }
    
    function afterAddDataOnCopy($pkey, $oldkey){
        $sql = 'update '.$this->tableName.' set receiptdt = \''.DEFAULT_EMPTY_DATE.'\', receiveddate = \''.DEFAULT_EMPTY_DATE.'\', refvatoutkey = 0 where pkey  = ' . $this->oDbCon->paramString($pkey); 
        $this->oDbCon->execute($sql); 
    }
    
     function getJobInformation($arrPkey){
        // untuk laporan buku besar
     
        $sql = 'select distinct
                 '.$this->tableSalesOrderHeader.'.pkey as jokey,
                 '.$this->tableSalesOrderHeader.'.code as jocode,
                 '.$this->tableName.'.pkey as reftablekey 
                from  
                 '.$this->tableSalesOrderHeader.', 
                 '.$this->tableName.',
                 '.$this->tableNameDetail.'  
                where  
                    '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').') and
                    '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableNameDetail.'.refsalesorderheaderkey = '.$this->tableSalesOrderHeader.'.pkey  
              ';
          
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
}

?>