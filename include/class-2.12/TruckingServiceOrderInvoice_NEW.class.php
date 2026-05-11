<?php

class TruckingServiceOrderInvoice extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'trucking_service_order_invoice_header';
    $this->tableNameDetail = 'trucking_service_order_invoice_detail';
    $this->tableNameItemDetail = 'trucking_service_order_invoice_item_detail';
    $this->tableCustomer = 'customer';
    $this->tableItem = 'item';
    $this->tableStatus = 'transaction_status';
    $this->tableWarehouse = 'warehouse';   
    $this->tablePayment= 'trucking_service_order_invoice_payment'; 
    $this->tableSalesOrder= 'trucking_service_order_header';
    $this->tableSalesOrderCategory= 'trucking_service_order_category';
    $this->tableWorkOrder= 'trucking_service_work_order';    
    $this->tableConsignee= 'consignee'; 
    $this->tablePaymentMethod = 'payment_method';
    $this->tableDownpaymentDetail = 'trucking_service_order_invoice_downpayment';
    $this->tableDownpayment = 'customer_downpayment';
    $this->tablePartialInvoice = 'trucking_service_order_header_partial_invoice';  
    $this->tableAR = 'ar';
    $this->tableARPaymentHeader = 'ar_payment_header';
    $this->tableARPaymentDetail = 'ar_payment_detail';
    $this->tableCustomCode = 'custom_code'; 
    $this->isTransaction = true;
    $this->securityObject = 'TruckingServiceOrderInvoice';
    $this->tableCOA  = 'chart_of_account';
    $this->autoPrintURL = 'print/truckingServiceOrderInvoice';

        
    $this->arrItem = array();  
    $this->arrItem['pkey'] = array('hidDetailItemKey');
    $this->arrItem['refkey'] = array('hidDetailKey','ref');  
    $this->arrItem['refheaderkey'] = array('pkey','ref');  
    $this->arrItem['itemkey'] = array('hidItemDetailKey', array('mandatory'=>true)); 
    $this->arrItem['refsodetailkey'] = array('hidRefSODetailKey'); 
    $this->arrItem['istax23'] = array('chkIsTax23'); 
    $this->arrItem['aliasname'] = array('itemNameAliasDetail'); 
    $this->arrItem['qtyinbaseunit'] = array('qtyDetail','number', array('mandatory'=>true));
    $this->arrItem['priceinunit'] = array('priceInUnitDetail','number', array('mandatory'=>true)); 
    $this->arrItem['total'] = array('subtotalDetail','number'); 
    $this->arrItem['taxdetail'] = array('taxDetail','number'); 
    $this->arrItem['taxdetailvalue'] = array('taxValueDetail','number'); 
    $this->arrItem['beforetaxdetailvalue'] = array('beforeTaxDetail','number'); 
    $this->arrItem['aftertaxdetailvalue'] = array('afterTaxDetail','number'); 
    $this->arrItem['ispriceincludetax'] = array('chkIncludeTaxDetail'); 

    $this->arrDataDetail = array(); 
    $this->arrDataDetail['pkey'] = array('hidDetailKey', array('dataDetail' => array('dataset' => $this->arrItem, 'tableName' => $this->tableNameItemDetail)));
    $this->arrDataDetail['refkey'] = array('pkey','ref'); 
    $this->arrDataDetail['salesorderkey'] = array('hidSalesOrderKey');
    $this->arrDataDetail['invoicetype'] = array('selInvoiceType', array('mandatory'=>true)); 
    $this->arrDataDetail['salesordergrandtotal'] = array('salesOrderSubtotal','number');
    $this->arrDataDetail['salesordertotalinvoiced'] = array('salesOrderDownpayment','number');
    $this->arrDataDetail['itemkey'] = array('hidItemKey');
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
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['refinvoicekey'] = array('hidRefInvoiceKey');
    $this->arrData['customerkey'] = array('hidCustomerKey');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['trdesc'] = array('trDesc');
    $this->arrData['statuskey'] = array('selStatus');
    $this->arrData['companybankkey'] = array('selBank');
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
    $this->arrData['isdownpayment'] = array('chkDownpayment');
    $this->arrData['invoiceto'] = array('selInvoiceTo'); 
	$this->arrData['currencykey'] = array('selCurrency');
    $this->arrData['rate'] = array('currencyRate','number');
   
    $this->refAutoCode = array( 'param' => 'hidRefInvoiceKey', 'refField' => 'pkey');
        
    // perlu tambahin $this->tableNameItemDetail, tp harus manual, karena refkeynya beda
    //$this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail, $this->tablePayment, $this->tableDownpaymentDetail);
  
    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 90,  'align' => 'center', 'format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'duedate','title' => 'duedate','dbfield' => 'duedate',  'width' => 90,  'align' => 'center', 'format' => 'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'consignee','title' => 'consignee','dbfield' => 'consigneename','default'=>true, 'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceAmount','title' => 'invoiceAmount','dbfield' => 'grandtotal','default'=>true,'align'=>'right','format'=>'integer', 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'downpayment','title' => 'downpayment','dbfield' => 'totaldownpayment','default'=>true, 'align'=>'right','format'=>'integer', 'width' => 90));
    array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceOutstanding','title' => 'invoiceOutstanding','dbfield' => 'outstanding','default'=>true, 'align'=>'right','format'=>'integer', 'width' => 90));
    array_push($this->arrDataListAvailableColumn, array('code' => 'arOutstanding','title' => 'outstanding','dbfield' => 'aroutstanding','default'=>true,'align'=>'right','format'=>'integer', 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc', 'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceType','title' => 'invoiceType','dbfield' => 'invoicetype', 'width' => 100));
    array_push($this->arrDataListAvailableColumn, array('code' => 'receiptDate','title' => 'receiptDate','dbfield' => 'receiptdt', 'width' => 120, 'align' => 'center','format'=>'date'));
    array_push($this->arrDataListAvailableColumn, array('code' => 'salesordercodecache','title' => 'JOCode','dbfield' => 'salesordercodecache', 'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'taxvalue','title' => 'tax','dbfield' => 'taxvalue', 'width' => 100,'align'=>'right','format'=>'integer',));
    array_push($this->arrDataListAvailableColumn, array('code' => 'tax23value','title' => 'tax23','dbfield' => 'tax23value', 'width' => 100,'align'=>'right','format'=>'integer'));
         
    $this->printMenu = array();
    array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['printInvoice'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderInvoice'));
     
    array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
    $this->includeClassDependencies(array( 
          'AP.class.php',
          'AR.class.php',
          'Car.class.php',
          'CarTurnover.class.php',
          'COALink.class.php',
          'Customer.class.php',
          'Downpayment.class.php',
          'CustomerDownpayment.class.php',
          'GeneralJournal.class.php',
          'Item.class.php',
          'SalesOrderInvoiceReceipt.class.php', 
          'Supplier.class.php',
          'TermOfPayment.class.php',
          'Category.class.php',
          'TruckingServiceOrderCategory.class.php',
          'TruckingServiceOrder.class.php',
          'TruckingServiceWorkOrder.class.php',
          'TermOfPayment.class.php',
          'Warehouse.class.php' ,
          'Item.class.php' 
    ));      
          
    }

    function getQuery(){
        
        $rsKey = $this->getTableKeyAndObj($this->tableName,array('key'));
        
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                group_concat('.$this->tableConsignee.'.name separator \', \')  as consigneename, 
                '.$this->tableWarehouse.'.name as warehousename, 
                '.$this->tableCustomer.'.code as customercode, 
                '.$this->tableCustomer.'.name as customername, 
                '.$this->tableStatus.'.status as statusname,
                '.$this->tablePaymentMethod.'.name as companybank,
                '.$this->tableCustomCode.'.name as invoicetype, 
                '.$this->tableSalesOrderCategory.'.name as jobcategoryname,
                '.$this->tableAR.'.duedate,
                coalesce('.$this->tableAR.'.outstanding,0) as aroutstanding
            FROM '.$this->tableStatus.',
                 '.$this->tableName.'
                    left join '.$this->tableNameDetail.' on '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey
                    left join '.$this->tableItem.' on '.$this->tableNameDetail.'.itemkey  =  '.$this->tableItem.'.pkey
                    left join '.$this->tableSalesOrder.' on '.$this->tableNameDetail.'.salesorderkey  =  '.$this->tableSalesOrder.'.pkey
                    left join '.$this->tableSalesOrderCategory.' on '.$this->tableSalesOrderCategory.'.pkey  =  '.$this->tableSalesOrder.'.categorykey
                    left join '.$this->tablePaymentMethod.' on '.$this->tableName.'.companybankkey =  '.$this->tablePaymentMethod.'.pkey
                    left join '.$this->tableConsignee.' on '.$this->tableSalesOrder.'.consigneekey =  '.$this->tableConsignee.'.pkey  
                    left join 
                            '.$this->tableAR.' on '.$this->tableAR.'.refheaderkey = '.$this->tableName.'.pkey and
                            '.$this->tableAR.'.reftabletype = '. $this->oDbCon->paramString($rsKey['key']).' 
                    left join '.$this->tableCustomCode.' on 
                            '.$this->tableName.'.customcodekey =  '.$this->tableCustomCode.'.pkey and
                            '.$this->tableCustomCode.'.reftabletype = '. $this->oDbCon->paramString($rsKey['key']).', 
                 '.$this->tableCustomer.', 
                 '.$this->tableWarehouse.' 
            WHERE   
                  '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
            ' .$this->criteria ;
            
        $sql .=  $this->getWarehouseCriteria() ;
         
        $sql .= ' group by '.$this->tableName.'.pkey ';
     
        return $sql;
    }

 
    function reCountGrandTotal($arrParam){
          
        $truckingServiceOrder = new TruckingServiceOrder();

        $usePPNDetail = $this->loadSetting('usePPNDetail');
        
        $grandtotal = 0;
        $subtotal = 0;

        $isPriceIncludeTax =  $arrParam['chkIncludeTax'];
        //$taxValue = $this->unFormatNumber($arrParam['taxValue']);  
        $finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
        $finalDiscountType = $arrParam['selFinalDiscountType']; 
        $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);  
        $arrSalesOrderKey = $arrParam['hidSalesOrderKey'];
        $arrSalesOrderDescription  = $arrParam['detailNote'];
        $arrItemKey = $arrParam['hidItemKey']; 
        $arrSalesOrderDownpayment = $arrParam['salesOrderDownpayment'];
        $arrAmount = $arrParam['amount'];
        $arrPick = $arrParam['chkPick'];  


        $detailSubtotalTaxed = 0;
        $detailBeforeTaxTotal = 0;
        $detailTaxTotal = 0;
        for ($i=0;$i<count($arrPick);$i++){ 
                $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
                $arrSalesOrderDownpayment[$i] = $this->unFormatNumber($arrSalesOrderDownpayment[$i]);

                if ( (empty($arrSalesOrderKey[$i]) && empty($arrItemKey[$i]) ) ||  empty($arrAmount[$i]) || empty($arrPick[$i]) )  
                    continue;

                if (empty($arrSalesOrderKey[$i]) || $arrParam['chkDownpayment'] ){  
                    //$this->setLog($arrAmount[$i]);
                    $subtotal += $arrAmount[$i];
                }else{ 
                    $sokey = $arrSalesOrderKey[$i];
                    $rsPrice = $truckingServiceOrder->getUnInvoicedItemDetail($sokey);
                    $arrDefaultPrice = array_column($rsPrice,'priceinunit', 'joinkey');

                    $arrChkService = $arrParam['chkService'][$i];  
                    $arrChkTax = $arrParam['chkIsTax23'][$i]; 
                    $arrQtyService = $arrParam['qtyDetail'][$i]; 
                    $arrItemDetailKey = $arrParam['hidItemDetailKey'][$i];  
                    $arrSODetailKey = $arrParam['hidRefSODetailKey'][$i]; 
                    $arrTaxDetail = $arrParam['taxDetail'][$i]; 
                    $arrIsPriceIncludeTaxDetail = $arrParam['chkIncludeTaxDetail'][$i]; 

                    $detailSubtotal = 0;
                    //$this->setLog($arrItemDetailKey,true);

                    for ($j=0;$j<count($arrItemDetailKey);$j++){  
                        $joinkey = $arrSODetailKey[$j] . '-' . $arrItemDetailKey[$j]; 
                        $priceInUnit = (isset($arrDefaultPrice[$joinkey])) ? $arrDefaultPrice[$joinkey] : 0 ;
                        $total =  $this->unFormatNumber($arrQtyService[$j]) * $priceInUnit;
  
                        // gk boleh pake chkService karena gk kepecah
                        // gk kepecah karena kita gk assign chkService di arrItem
                        //  empty($arrChkService[$j]) || 
                        if (empty($arrSODetailKey[$j]) || empty($arrItemDetailKey[$j]) || empty($total) )  
                            continue;    
 
                         // jika informasi PPN di detail, sekalian hitung ulang nilai per detailnya
						 if($usePPNDetail){
							$taxDetail =  $this->unFormatNumber($arrTaxDetail[$j]);
							$isPriceIncludeTaxDetail = $arrIsPriceIncludeTaxDetail[$j];
							  
                            $detailBeforeTax = 0; 
                            $this->recalculateTaxAndValue($detailBeforeTax,$total,$taxDetailValue, $taxDetail, $isPriceIncludeTaxDetail);
                             
                            $detailTaxTotal += $taxDetailValue;
                            $detailBeforeTaxTotal += $detailBeforeTax; 
						 }
						  
                         //$this->setLog('total '.$total,true);
                         $detailSubtotal += $total;  

                        // ini utk PPH 23 
                        if(!empty($arrChkTax[$j]))
                            $detailSubtotalTaxed += ($usePPNDetail)  ? $detailBeforeTax : $total;
                    }

                    $detailSubtotal -= $arrSalesOrderDownpayment[$i];
                    if ($detailSubtotal < 0 ) $detailSubtotal = 0;

                    $subtotal += $detailSubtotal;   
                }
        } 

	    if($usePPNDetail){
            $beforeTaxTotal = $detailBeforeTaxTotal;
            $grandtotal = $beforeTaxTotal + $detailTaxTotal; 
        }else{ 
 
            if ($finalDiscount != 0){
                if ($finalDiscountType == 2)
                    $finalDiscount = $finalDiscount/100 * $subtotal;
            } 
 
            $beforeTaxTotal = $subtotal - $finalDiscount;
            $grandtotal = $beforeTaxTotal;
 
            $this->recalculateTaxAndValue($beforeTaxTotal,$grandtotal,$taxValue, $taxPercentage, $isPriceIncludeTax); 
        }  
        
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
        
        // hitung PPH 23  
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
        $reCountResult['taxValue'] = ($usePPNDetail) ? $detailTaxTotal : $taxValue;     
 
        return $reCountResult;

    } 

    function validateForm($arr,$pkey = ''){ 

        $truckingServiceOrder = new TruckingServiceOrder();
        $arrayToJs = parent::validateForm($arr,$pkey); 
        $downpayment = new CustomerDownpayment();

        $customerkey = $arr['hidCustomerKey']; 
        $arrAmount = $arr['amount'];
        $arrSalesOrderKey = $arr['hidSalesOrderKey']; 
        $arrSalesOrderDescription  = $arr['detailNote'];
        $arrPick = $arr['chkPick']; 
        $arrDownpaymentKey = $arr['hidDownpaymentKey'];
		$arrDownpaymentAmount = $arr['downpaymentAmount'];
		$arrDownpaymentCode = $arr['downpaymentCode'];
		$subtotal = $arr['subtotal'];
        $salesOrderSubtotal = $arr['salesOrderSubtotal'];
        $salesOrderDownpayment = $arr['salesOrderDownpayment'];
        $refInvoiceKey =  $arr['hidRefInvoiceKey'];
		$currencykey = $arr['selCurrency']; 
        
        $arrDetailKey = array();
          
        if(empty($customerkey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
        
        //validasi kalo status gk menunggu gk bisa edit 
        if (!empty($pkey)){
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] <> 1){
                $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
            }
        }  
 
         if (!empty($refInvoiceKey)){
            $rsRef = $this->getDataRowById($refInvoiceKey);
            if ($rsRef[0]['customerkey'] <> $customerkey){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceOrderInvoice'][7]);
            }
        }  
 
        
        for($i=0;$i<count($arrAmount);$i++) { 
            $arrAmount[$i] = $this->unformatNumber($arrAmount[$i]);
            $salesOrderSubtotal[$i] = $this->unformatNumber($salesOrderSubtotal[$i]);
            $salesOrderDownpayment[$i] = $this->unformatNumber($salesOrderDownpayment[$i]);
            
            if ($arrAmount[$i] <= 0)
                $this->addErrorList($arrayToJs,false,$this->errorMsg[503]);
            
            
            if (!empty($rsDetail[$i]['salesorderkey']) && ($arrAmount[$i] > ($salesOrderSubtotal[$i] - $salesOrderDownpayment[$i])))
                $this->addErrorList($arrayToJs,false,$this->errorMsg[508]);
        }
        
        
        $hasSO = false; 
        // cek ad duplikasi gk, dan cek customernya sesuai gk
        for($i=0;$i<count($arrSalesOrderKey);$i++) {   

            if ( (!empty($arrSalesOrderKey[$i]) ) && !empty($arrPick[$i]) )  {
                $hasSO = true;   
                
                $rsSO = $truckingServiceOrder->getDataRowById($arrSalesOrderKey[$i]);

                if (in_array($arrSalesOrderKey[$i],$arrDetailKey)){  
                    $this->addErrorList($arrayToJs,false, $rsSO[0]['code'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    if (!empty($arrSalesOrderKey[$i])) {  
                        array_push($arrDetailKey, $arrSalesOrderKey[$i]);
                    }
                }

                if ($rsSO[0]['customerkey'] <> $customerkey)
                    $this->addErrorList($arrayToJs,false, $rsSO[0]['code'].'. '.$this->errorMsg['truckingServiceOrderInvoice'][3]); 	
            }
  
             
        } 

        /*
        if (!$hasSO)
        $this->addErrorList($arrayToJs,false, $this->errorMsg['salesOrder'][1]); 	
        */
 
             
            
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
                
				if ($currencykey <> $rsDP[0]['currencykey'])
                    $this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][10]);                 // cek double gk
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
  
    function updateGL($rs,$arrShareProfit){ 
        if (!USE_GL) return;

        $generalJournal = new GeneralJournal();
        $truckingServiceOrder = new TruckingServiceOrder();  
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();
        $customer = new Customer();
        $item = new Item();
         
        $warehousekey = $rs[0]['warehousekey'];

        $id = $rs[0]['pkey']; 
        $rsDetail = $this->getDetailById($id);  
        $invoiceType = $rs[0]['customcodekey'];
        
          
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
        $arr['createdBy'] = 0;
        
        // desc
        $desc = array(); 
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        array_push($desc,$rsCustomer[0]['name']);  
        if(!empty($rs[0]['trdesc'])) array_push($desc,$rs[0]['trdesc']); 
		$arr['trDesc'] = implode(chr(13),$desc);
        

        $temp = -1; 
        $totalDisc = 0 ; 
  
/*        $finalDiscount = ($rs[0]['finaldiscount'] != 0 && $rs[0]['finaldiscounttype'] == 2) ? $rs[0]['finaldiscount']/100 * $rs[0]['subtotal'] : $rs[0]['finaldiscount']; 
        $totalDisc = $finalDiscount;*/

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 

        if ($isCash) {
            $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
            for($i=0;$i<count($rsPayment); $i++){ 
                 $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey, $rsPayment[$i]['paymentkey']);
                 $temp++;
                 $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                 $arr['debit'][$temp] = $rsPayment[$i]['amount']; 
                 $arr['credit'][$temp] = 0;  
            }
 
            //selisih pembayaran   
            $temp++; 
            if ($rs[0]['balance'] < 0){ 
                $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                $arr['debit'][$temp] = abs($rs[0]['balance']); 
                $arr['credit'][$temp] = 0; 
            }else{ 
                $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] = abs($rs[0]['balance']); 
            }

            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];

        }else {  
                $temp++;
                $arr['hidCOAKey'][$temp] =  $customer->getARCOAKey($rs[0]['customerkey'],$warehousekey);
                $arr['debit'][$temp] = $rs[0]['grandtotal'] - $rs[0]['totaldownpayment'] ; 
                $arr['credit'][$temp] = 0;  
        } 
 
        //downpayment 
		$rsDownpayment = $this->getDownpaymentDetail($rs[0]['pkey']);  
        for($i=0;$i<count($rsDownpayment); $i++){  
             $temp++;
             $arr['hidCOAKey'][$temp] = $customer->getDownpaymentCOAKey($rs[0]['customerkey'],$warehousekey);   
             $arr['debit'][$temp] = $rsDownpayment[$i]['amount']; 
             $arr['credit'][$temp] = 0;  
        }
        
        if (!$rs[0]['isdownpayment']){ 
            switch ($invoiceType){
                 
            case INVOICE_TYPE['reimbursement']:  
                                                $arrCOAAmout = array();

                                                for($i=0;$i<count($rsDetail);$i++){
                                                    if ($rsDetail[$i]['invoicetype'] == 2){
                                                        $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
                                                        $coakey = $rsItem[0]['revenuecoakey'];
                                                        if (!isset($arrCOAAmout[$coakey]))
                                                            $arrCOAAmout[$coakey] = 0;
                                                         
                                                        $arrCOAAmout[$coakey] += $this->recalculatePriceBeforeTaxAndFinalDiscount($rs,$rsDetail[$i]['amount']);  
                                                         
                                                    }else{ 
                                                        $rsServiceDetail = $this->getItemDetail($rsDetail[$i]['pkey']);
                                                        for($j=0;$j<count($rsServiceDetail);$j++){ 
                                                            $coakey = $rsServiceDetail[$j]['costcoakey'];
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
                                                        $arr['credit'][$temp] = $amount;   
                                                }


                                                break; 
                 
            default : // sales
                 
                        $arrCOAAmout = array();
                
                        for($i=0;$i<count($rsDetail);$i++){ 
                            
                            if ($rsDetail[$i]['invoicetype'] == 2){
                                $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
                                $coakey = $rsItem[0]['revenuecoakey'];
                                if (!isset($arrCOAAmout[$coakey]))
                                    $arrCOAAmout[$coakey] = 0;

                                $arrCOAAmout[$coakey] += $this->recalculatePriceBeforeTaxAndFinalDiscount($rs,$rsDetail[$i]['amount']);   
                            }else{ 
                                // kalo sales order
                                $rsServiceDetail = $this->getItemDetail($rsDetail[$i]['pkey']);
                                for($j=0;$j<count($rsServiceDetail);$j++){ 
                                    $coakey = $rsServiceDetail[$j]['revenuecoakey'];
                                    if (empty($coakey)){ 
                                         $coatype = (empty($rsServiceDetail[$j]['refsodetailkey'])) ? 'otherrevenue' : 'salesservice';  
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
                                $arr['credit'][$temp] = $amount;   
                        }
                
                        break;
                
            }
        } else{
                $totalDP = 0;
                $coaKey  = $coaLink->getCOALink ('salesservice', $warehouse->tableName,$warehousekey, 0);
                for($i=0;$i<count($rsDetail);$i++) 
                    $totalDP += $this->recalculatePriceBeforeTaxAndFinalDiscount($rs,$rsDetail[$i]['amount']);   


                $temp++;
                $arr['hidCOAKey'][$temp] = $coaKey[0]['coakey'];
                $arr['debit'][$temp] = 0;
                $arr['credit'][$temp] = $totalDP;   
        }
         
/*        $rsCOA = $coaLink->getCOALink ('salesservicediscount', $warehouse->tableName,$warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $totalDisc; 
        $arr['credit'][$temp] = 0; */

        $rsCOA = $coaLink->getCOALink ('taxout', $warehouse->tableName,$warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0;
        $arr['credit'][$temp] = $rs[0]['taxvalue'];   
 
        
        // kalo ad share profit
        $rsCOA = $coaLink->getCOALink ('outsourcecost', $warehouse->tableName, $warehousekey);   
        foreach($arrShareProfit as $shareProfitRow){ 
            
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = $shareProfitRow['amount'];
            $arr['credit'][$temp] = 0;   
            
            $temp++;
            $arr['hidCOAKey'][$temp] = $shareProfitRow['coakey'];
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] = $shareProfitRow['amount'];   
        }
        
        $arrayToJs = $generalJournal->addData($arr);


        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);

    } 
  
    function updateGLWithPPNDetail($rs){ 
        if (!USE_GL) return;

        $generalJournal = new GeneralJournal();
        $truckingServiceOrder = new TruckingServiceOrder();  
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();
        $customer = new Customer();
        $item = new Item();
         
        $warehousekey = $rs[0]['warehousekey'];

        $id = $rs[0]['pkey']; 
        $rsDetail = $this->getDetailById($id);  
        $invoiceType = $rs[0]['customcodekey'];
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
        $arr['createdBy'] = 0;
        
        // desc
        $desc = array(); 
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        array_push($desc,$rsCustomer[0]['name']);  
        if(!empty($rs[0]['trdesc'])) array_push($desc,$rs[0]['trdesc']); 
		$arr['trDesc'] = implode(chr(13),$desc);
        

        $temp = -1; 
        $totalDisc = 0 ;  
        
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 

        if ($isCash) {
            $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
            for($i=0;$i<count($rsPayment); $i++){ 
                 $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey, $rsPayment[$i]['paymentkey']);
                 $temp++;
                 $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                 $arr['debit'][$temp] = $rsPayment[$i]['amount']; 
                 $arr['credit'][$temp] = 0;  
            }
 
            //selisih pembayaran   
            $temp++; 
            if ($rs[0]['balance'] < 0){ 
                $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                $arr['debit'][$temp] = abs($rs[0]['balance']); 
                $arr['credit'][$temp] = 0; 
            }else{ 
                $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] = abs($rs[0]['balance']); 
            }

            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];

        }else {  
                $temp++;
                $arr['hidCOAKey'][$temp] =  $customer->getARCOAKey($rs[0]['customerkey'],$warehousekey);
                $arr['debit'][$temp] = $rs[0]['grandtotal'] - $rs[0]['totaldownpayment'] ; 
                $arr['credit'][$temp] = 0;  
        } 
 
        //downpayment 
		$rsDownpayment = $this->getDownpaymentDetail($rs[0]['pkey']);  
        for($i=0;$i<count($rsDownpayment); $i++){  
             $temp++;
             $arr['hidCOAKey'][$temp] = $customer->getDownpaymentCOAKey($rs[0]['customerkey'],$warehousekey);   
             $arr['debit'][$temp] = $rsDownpayment[$i]['amount']; 
             $arr['credit'][$temp] = 0;  
        }
        
        if (!$rs[0]['isdownpayment']){ 
            $arrCOAAmout = array();

            for($i=0;$i<count($rsDetail);$i++){ 

                if ($rsDetail[$i]['invoicetype'] == 2){
                 // sementara dinonaktifkan
             
                }else{ 
                    // kalo sales order
                    $rsServiceDetail = $this->getItemDetail($rsDetail[$i]['pkey']);
                    for($j=0;$j<count($rsServiceDetail);$j++){ 
                        $coakey = $rsServiceDetail[$j]['revenuecoakey'];
                        if (empty($coakey)){ 
                             $coatype = (empty($rsServiceDetail[$j]['refsodetailkey'])) ? 'otherrevenue' : 'salesservice';  
                             $rsCOA = $coaLink->getCOALink ($coatype, $warehouse->tableName,$warehousekey, 0);
                             $coakey = $rsCOA[0]['coakey'];
                        }

                        if (!isset($arrCOAAmout[$coakey]))
                            $arrCOAAmout[$coakey] = 0;

                        $arrCOAAmout[$coakey] +=  $rsServiceDetail[$j]['beforetaxdetailvalue']; 

                    }
                }

            }

            foreach ($arrCOAAmout as $key => $amount) { 
                    $temp++;
                    $arr['hidCOAKey'][$temp] = $key;
                    $arr['debit'][$temp] = 0;
                    $arr['credit'][$temp] = $amount;   
            } 

        } else{
            // sementara dinonaktifkan utk jenis DP
        }
        
        $rsCOA = $coaLink->getCOALink ('taxout', $warehouse->tableName,$warehousekey, 0);
        $temp++;
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0;
        $arr['credit'][$temp] = $rs[0]['taxvalue'];   
 
        
        // kalo ad share profit
        // sementara dinonaktifkan  
         
        $arrayToJs = $generalJournal->addData($arr);
 
        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);

    } 
  


    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*,  
            '.$this->tableSalesOrder.'.code as socode,  
            '.$this->tableSalesOrder.'.donumber,  
            '.$this->tableSalesOrder.'.trdate as sodate, 
            '.$this->tableSalesOrder.'.consigneename ,
            '.$this->tableItem.'.name as itemname
          from
            '.$this->tableNameDetail.'
                left join 
                    (select 
                        '.$this->tableSalesOrder.'.pkey, 
                        '.$this->tableSalesOrder.'.code, 
                        '.$this->tableSalesOrder.'.donumber, 
                        '.$this->tableSalesOrder.'.trdate,  
                        '.$this->tableConsignee.'.name as consigneename 
                    from 
                        '.$this->tableSalesOrder.' 
                            left join '.$this->tableConsignee.' on '.$this->tableSalesOrder.'.consigneekey = '.$this->tableConsignee.'.pkey
                    ) as '.$this->tableSalesOrder.' on  '.$this->tableNameDetail.'.salesorderkey = '.$this->tableSalesOrder.'.pkey
                left join '.$this->tableItem.' on  '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey 
          where  
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);

    }
 
	function generateInvoiceReport($criteria='',$order='',$pkey='',$itemkey = ''){ 
     
	   $sql =  '
			SELECT '.$this->tableName.'.pkey,
                   '.$this->tableName.'.code, 
                   '.$this->tableName.'.grandtotal,
                   '.$this->tableName.'.tax23value,
                   '.$this->tableName.'.taxvalue,
                   '.$this->tableCustomer.'.name as customername,  
                    group_concat('.$this->tableConsignee.'.name separator \', \')  as consigneename, 
                   '.$this->tableName.'.salesordercodecache, 
                   '.$this->tableName.'.customcodekey, 
                   '.$this->tableName.'.trdate, 
                   '.$this->tableNameDetail.'.description, 
                   '.$this->tableSalesOrder.'.code as socode, 
                   '.$this->tableSalesOrder.'.trdate as sodate, 
                   '.$this->tableSalesOrder.'.lastwodate, 
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableWarehouse.'.name as warehousename 
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableName.' 
                       left join '.$this->tableNameDetail.' on  '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey
                       left join '.$this->tableSalesOrder.' on  '.$this->tableNameDetail.'.salesorderkey = '.$this->tableSalesOrder.'.pkey
                       left join '.$this->tableConsignee.' on '.$this->tableSalesOrder.'.consigneekey =  '.$this->tableConsignee.'.pkey   ,
                '.$this->tableCustomer.', 
                '.$this->tableWarehouse.'
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and  
                '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($pkey))  
            $sql .=  '  and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey);
         
        $sql .=  $this->getWarehouseCriteria() ;
        
        $sql .= ' group by '.$this->tableName.'.pkey ';
        
        if (!empty($order))  
            $sql .=  ' ' .$order;  
           
        return $this->oDbCon->doQuery($sql);
		 
    }
  
    /*function updateConsignee($invoicekey = '', $consigneekey = ''){
        
        $truckingServiceOrder = new TruckingServiceOrder();
        $consignee = new Consignee();
       
        if (!empty($invoicekey)){ 
            // update berdasarkan invoicekey
            $arrConsignee = array();

            $rsDetail = $this->getDetailWithRelatedInformation($invoicekey);
            for($i=0;$i<count($rsDetail);$i++){
                if(empty($rsDetail[$i]['salesorderkey']))  continue;

                $rsJO = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);
                if(!empty($rsJO) && !empty($rsJO[0]['consigneekey'])){ 
                    $rsConsignee = $consignee->getDataRowById($rsJO[0]['consigneekey']);
                    if(!empty($rsConsignee) && !in_array($rsConsignee[0]['name'], $arrConsignee))
                        array_push($arrConsignee,$rsConsignee[0]['name']); 
                }

            }
            
            if(!empty($arrConsignee)){
                $arrConsignee = implode(', ',$arrConsignee);
                $sql = 'update '.$this->tableName.' set  consigneenamecache = '.$this->oDbCon->paramString($arrConsignee).' where pkey = ' . $this->oDbCon->paramString($invoicekey); 
                $this->oDbCon->execute($sql);  
            }
            
        }else if (!empty($consigneekey)){ 
  		    $rsJO = $truckingServiceOrder->searchData( '','', true,' and '.$truckingServiceOrder->tableName.'.consigneekey = '.$this->oDbCon->paramString($consigneekey).' and '.$truckingServiceOrder->tableName.'.statuskey in (5,6)');
            if(!empty($rsJO)){
                $arrJO = array_column($rsJO, 'pkey');
                $arrJO = implode(',', $arrJO);
                $sql = 'select distinct refkey from '.$this->tableNameDetail.' where salesorderkey in ('.$arrJO.')';
                $rsInvoice = $this->oDbCon->doQuery($sql);
                
                foreach($rsInvoice as $row) 
                    $this->updateConsignee($row['refkey']); 
                 
            }            
          
        }
              
    }*/
    
    function normalizeParameter($arrParam, $trim=false){
           
        $termOfPayment = new TermOfPayment();
        
        $usePPNDetail = $this->loadSetting('usePPNDetail');
         
        $arrParam['chkIncludeTax'] = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;  
        $arrParam['chkTax23'] = (!empty($arrParam['chkTax23'])) ? 1 : 0;  
        $arrParam['currencyRate'] = (!isset($arrParam['currencyRate']) || $this->unFormatNumber($arrParam['currencyRate']) <=0 ) ? 1 : $arrParam['currencyRate'];  
          
        $itemkey = count($arrParam['hidItemDetailKey']);
        
        // kalo detail ppn di setiap baris
        if($usePPNDetail){
            // header di nol kan
            $arrParam['chkIncludeTax'] = 0;
            $arrParam['taxPercentage'] = 0;
            
            // salah hitung
            /*$taxDetail = $this->unFormatNumber($arrParam['taxDetail']);
            $subTotalDetail = $this->unFormatNumber($arrParam['subtotalDetail']);
            for($i=0;$i<$itemkey;$i++){
                $taxTotal = ($subTotalDetail[$i]*$taxDetail[$i])/100;
                $arrParam['taxValueDetail'][$i] =  $taxTotal;
            }*/
        }
        
        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPayment']);  
        if ($rsTOP[0]['duedays'] != 0){   
            for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
                $arrParam['paymentMethodValue'][$i] = 0; 
                $arrParam['hidDetailPaymentKey'][$i] = 0;
            }
        }

 	    $detail = $arrParam['hidDetailKey'];
        $isPartial = $arrParam['chkDownpayment'];
        
        $arrDONumber = array();
        $arrShipmentNumber = array();
        $arrJO = array();
        $truckingServiceOrder = new TruckingServiceOrder();
          

        for($i=0;$i<count($detail);$i++){
            $arrParam['selInvoiceType'][$i] = ($isPartial) ? 1 : $arrParam['selInvoiceType'][$i]; 

            if($arrParam['chkPick'][$i] == 0 || (empty($arrParam['hidItemKey'][$i]) && empty($arrParam['hidSalesOrderKey'][$i]))){ 
                $arrParam['selInvoiceType'][$i] = '';
                continue;
            }

            if($arrParam['selInvoiceType'][$i] == 1){
                
                $rsJO = $truckingServiceOrder->getDataRowById($arrParam['hidSalesOrderKey'][$i]);
                if(!empty($rsJO)){

                    if(!empty($rsJO[0]['donumber']))
                        array_push($arrDONumber,$rsJO[0]['donumber']);

                    if(!empty($rsJO[0]['shipmentnumber']))
                        array_push($arrShipmentNumber,$rsJO[0]['shipmentnumber']); 

                    array_push($arrJO,$rsJO[0]['code']);
                }

            }
        } 

            
        for($i=0;$i<$itemkey;$i++){
            
            if ($isPartial) {
                $arrParam['hidItemDetailKey'][$i] = '';
                continue;
            }
            
            if($arrParam['chkService'][$i]) continue; 
            $arrParam['hidItemDetailKey'][$i] = '';
            
        }
        
        $arrParam['doNumber'] = (!empty($arrDONumber)) ? implode(', ',$arrDONumber) : '';
        $arrParam['shipmentNumber'] = (!empty($arrShipmentNumber)) ? implode(', ',$arrShipmentNumber) : '';
        $arrParam['salesordercodecache'] = (!empty($arrJO)) ? implode(', ',$arrJO) : '';
 
        if ($arrParam['chkDownpayment']){
            $arrParam['finalDiscount'] = 0;
            $arrParam['selFinalDiscountType'] = 1; 
        }
        
       
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
        $arrParam['taxValue'] = $reCountResult['taxValue'];
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
             
        return $arrParam;
    }

    function getItemDetail($refkey,$reffield = 'refkey'){
        $sql = 'select 
                '. $this->tableNameItemDetail. '.*, 
                '.$this->tableItem.'.name as itemname ,
                '.$this->tableItem.'.servicecost,
                '.$this->tableItem.'.costcoakey,
                '.$this->tableItem.'.revenuecoakey
            from 
                ' . $this->tableNameItemDetail. ',
                ' . $this->tableItem. '
            where 
                '.$reffield.' in  ('.$this->oDbCon->paramString($refkey,',') . ') and 
                ' . $this->tableNameItemDetail. '.itemkey = ' . $this->tableItem. '.pkey'; 
         
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
        //update qtyinvoice 
        $truckingServiceOrder = new TruckingServiceOrder();
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        
       // if (!$rsHeader[0]['isdownpayment']){   
            foreach($rsDetail as $invoiceDetail){  
               if (empty($invoiceDetail['salesorderkey']))    continue;
             
                // $this->setLog("in ===>", true);
               $truckingServiceOrder->updateQtyInvoiced($invoiceDetail['salesorderkey']); 
                // $this->setLog("out ===>", true);
            } 
       // }
/*        
        foreach($rsDetail as $invoiceDetail){  
            if (empty($invoiceDetail['salesorderkey']))    continue;
            $truckingServiceOrder->updateTotalInvoicedAndOutstandingAmount($invoiceDetail['salesorderkey']); 
        } */
        
        $customerDownpayment = new CustomerDownpayment();
        $rsDownpayment = $this->getDownpaymentDetail($rsHeader[0]['pkey']);
        for($i=0;$i<count($rsDownpayment); $i++){  
           $customerDownpayment->updateOutstanding($rsDownpayment[$i]['downpaymentkey'],true); 
        }
         
    }
    
    
    function afterPrintTransaction($rsHeader){  
        // dinonaktifkan karena sudah ad modul tanda terima invoice
        
/*         parent::afterPrintTransaction($rsHeader);    
        
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); */
    }
    
        
    function validateConfirm($rsHeader){ 
        
   
        $id = $rsHeader[0]['pkey'];
        $customerkey = $rsHeader[0]['customerkey'];
		$currencykey =  $rsHeader[0]['currencykey'];
        
        $truckingServiceOrder = new TruckingServiceOrder();
        $termOfPayment = new TermOfPayment();
    
        $rsDetail = $this->getDetailById($id); 
        $rsPayment = $this->getPaymentMethodDetail($id); 
        $rsDownpayment = $this->getDownpaymentDetail($id);
  
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
 
        if ($isCash){   
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }

        
         for($i=0;$i<count($rsDownpayment);$i++) {   
            
            // validasi DP masi available gk 
            if($rsDownpayment[$i]['downpaymentstatuskey'] <> 2){ 
                $this->addErrorLog(false,$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][9]);
            }else{
                if ($customerkey <> $rsDownpayment[$i]['downpaymentcustomerkey'])
                    $this->addErrorLog(false,$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][6]); 

		if ($currencykey <> $rsDownpayment[$i]['downpaymentcurrencykey'])
                    $this->addErrorLog(false,$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][10]);
                // validasi nilai DP masi mencukupi gk 
                if ($rsDownpayment[$i]['amount'] > $rsDownpayment[$i]['downpaymentoutstanding'] )
                    $this->addErrorLog(false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][8].' ('.$this->lang['outstanding']. ': ' .$this->formatNumber($rsDownpayment[$i]['downpaymentoutstanding']['outstanding']).')');  
            }
                
        }

        
         for($i=0;$i<count($rsDetail);$i++){
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
        
        
        // jika jenisnya DP, tidak perlu validasi lebih lanjut
        if ( $rsHeader[0]['isdownpayment'])
            return;
        
        
        
        for($i=0;$i<count($rsDetail);$i++){
            if (empty($rsDetail[$i]['salesorderkey']))
                continue;

            $rsSO  = $truckingServiceOrder->getDataRowById($rsDetail[$i]['salesorderkey']);
            if ($rsSO[0]['statuskey'] < 5 || $rsSO[0]['statuskey'] > 6){ 
                 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsSO[0]['code'].'</strong>. ' . $this->errorMsg['truckingServiceOrderInvoice'][2]);
            }else if ($rsSO[0]['statuskey'] == 6){ 
                 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsSO[0]['code'].'</strong>. ' . $this->errorMsg[507]); 
            }else{ 
                // cek qty diinvoice berlebihan ap gk
                //ambil semua yg blm diinvoiced. kalo ad detail yg tdk dlm array itu, cancel

                $rsUnInvoiced = $truckingServiceOrder->getUnInvoicedItemDetail($rsDetail[$i]['salesorderkey']); 
                
				$arrUninvoiced = array_column($rsUnInvoiced,null,'joinkey');
                //$arrQtyUnInvoiced = array_column($rsUnInvoiced,'outstandingqty','joinkey');  

                $rsItemDetail = $this->getItemDetail($rsDetail[$i]['pkey']);
                for($j=0;$j<count($rsItemDetail);$j++){ 
                    $joinkey = $rsItemDetail[$j]['refsodetailkey'].'-'.$rsItemDetail[$j]['itemkey'];
                    
					$rsSODetail = isset($arrUninvoiced[$joinkey]) ? $arrUninvoiced[$joinkey] : array();
                     
                    if( $rsSODetail['outstandingqty'] - $rsItemDetail[$j]['qtyinbaseunit']  < 0) 
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsSO[0]['code'].'</strong>. <strong>'.$rsItemDetail[$j]['itemname'].'</strong>, ' . $this->errorMsg[508]);
                    
                    // validasi item, detail dan harga harus sama
					if(empty($rsSODetail) || $rsItemDetail[$j]['itemkey'] <> $rsSODetail['itemkey'] || $rsItemDetail[$j]['priceinunit'] <> $rsSODetail['priceinunit'] || $rsItemDetail[$j]['refsodetailkey'] <> $rsSODetail['pkey']){ 
							$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsSO[0]['code'].'</strong>. <strong>'.$rsItemDetail[$j]['itemname'].'</strong>, ' . $this->errorMsg[906]);
					}
                }

            } 
 
            // buat validasi kalo ada 2 form, dan DP sudah pernah dipakai disalah satu form 
            $totalInvoiced = $truckingServiceOrder->getTotalInvoicedAndOutstanding($rsDetail[$i]['salesorderkey'], $rsHeader[0]['customcodekey']); 
            //$this->setLog($totalInvoiced['outstanding']);
            if($rsDetail[$i]['salesordertotalinvoiced']<>$totalInvoiced['outstanding'])
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsSO[0]['code'].'</strong>. ' . $this->errorMsg['truckingServiceOrderInvoice'][6]);        
        
        } 

       
    } 

    function confirmTrans($rsHeader){

        $termOfPayment = new TermOfPayment();
        $warehouse = new Warehouse();
        $truckingServiceOrder = new TruckingServiceOrder();

        $usePPNDetail = $this->loadSetting('usePPNDetail');
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;  

         
        //update ar service
        if (!$isCash){ 
            $ar = new AR(); 
            $customer = new Customer();

            $topkey = $rsHeader[0]['termofpaymentkey']; 
            $rsTOP = $termOfPayment->getDataRowById($topkey);    
            $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];

            $rsARKey = $ar->getTableKeyAndObj($this->tableName);  
            $arrParam = array();	

            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey']; 
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
            $arrParam['hidRefCode2'] =  $rsHeader[0]['donumber'];
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
            $arrParam['hidRefTable'] = $rsARKey['key'];
            $arrParam['amount'] = $rsHeader[0]['grandtotal'] - $rsHeader[0]['totaldownpayment'];
            $arrParam['trDesc'] = $rsHeader[0]['code'];
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P'.$top.'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
            $arrParam['createdBy'] = 0;
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            $arrParam['selARType'] = AR_TYPE['serviceOrder'];
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];//$warehouse->getDefaultData();
            $arrParam['tax23value'] = $rsHeader[0]['tax23value'];
            $arrParam['tax23outstanding'] = $rsHeader[0]['tax23value'];

            $returnVal = $ar->addData($arrParam,false); 
            
            $rsHeader[0]['arKey'] = $returnVal[0]['data']['pkey'];
            $rsHeader[0]['arCode'] = $returnVal[0]['data']['code'];
        } 
        
        // update invoicekey di SPK
        //cari berapa step utk jenis JO nya  
        
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        
        if(!$rsHeader[0]['isdownpayment']){  
            $this->updateCarTurnOver($rsHeader,$rsDetail);
	        $arrShareProfit = $this->updateVendorCommission($rsHeader,$rsDetail); 
	    }        
        $this->addPartialInvoice($rsHeader,$rsDetail); 
        
        //update jurnal umum 
        if(!$usePPNDetail)
            $this->updateGL($rsHeader,$arrShareProfit); 
        else
            $this->updateGLWithPPNDetail($rsHeader); 
            
    } 
    
    function addPartialInvoice($rsHeader,$rsDetail){ 
          
        $truckingServiceOrder = new TruckingServiceOrder();
        
        foreach ($rsDetail as $invoiceDetail) { 
            if (empty($invoiceDetail['salesorderkey']))  continue;
            
            if ($rsHeader[0]['isdownpayment']){
                $amount = $invoiceDetail['amount'];
            }else{
                $rsDP = $truckingServiceOrder->getTotalInvoicedAndOutstanding($invoiceDetail['salesorderkey'],$rsHeader[0]['customcodekey']);
                    
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
     
	/*
    //cuma dipake di tools
    function updateSPKANDCarTurnOver(){ 
		$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        $carTurnover = new CarTurnover(); 
		$tableWo = $truckingServiceWorkOrder->tableName;
		$tableCarTurnOver = $carTurnover->tableName;
		$rsObjKey = $this->getTableKeyAndObj($this->tableName);
		
		$sql = 'update 
                            ' . $tableWo.'
                        set 
                            invoicekey = 0 
                        where  
                            ' . $tableWo.'.statuskey = 3 and
                            ' . $tableWo.'.isoutsource = 0
                        ';

                $this->oDbCon->execute($sql);
		
        $sql = 'delete from '.$tableCarTurnOver.' where reftabletype = '.$rsObjKey['key'];
		$this->oDbCon->execute($sql); 
    
        
        $rsInvoice = $this->searchData('','',true, ' and '.$this->tableName.'.statuskey in (2,3) and ' .$this->tableName.'.isdownpayment <> 1 ');
	     
        foreach ($rsInvoice as $invoiceHeader){
            $rsHeader[0] = $invoiceHeader; 
			$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
			$this->updateCarTurnOver($rsHeader,$rsDetail);


            $sql = 'delete from ap where refkey = '.$rsHeader[0]['pkey'];
            $this->oDbCon->execute($sql); 
	        $this->updateVendorCommission($rsHeader,$rsDetail); 

        }
         
    }*/

    function updateCarTurnOver($rsHeader,$rsDetail){
        
        $item = new Item();
        $truckingServiceOrder = new TruckingServiceOrder();
        $truckingServiceOrderCategory = new TruckingServiceOrderCategory();
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        $carTurnover = new CarTurnover(); 
        
        
        foreach ($rsDetail as $invoiceDetail) {
            if (empty($invoiceDetail['salesorderkey']))
                continue;
            
            $jobOrderKey = $invoiceDetail['salesorderkey'];
                
            $rsJO = $truckingServiceOrder->getDataRowById($jobOrderKey);
            $rsJobType = $truckingServiceOrderCategory->getDetailById($rsJO[0]['categorykey']);
            //$this->setLog($rsJobType,true);
            
            // ambil detailnya job order di invoice
            $rsItemDetail = $this->getItemDetail($invoiceDetail['pkey']);
            foreach ($rsItemDetail as $itemDetail) {
                $itemkey =  $itemDetail['itemkey']; // bisa item layanan, bisa item cost
                $qty =  intval($itemDetail['qtyinbaseunit']); 
                
                // kalo bkn jenis item, continue ...
                $rsItem = $item->getDataRowById($itemkey);  
                if ($rsItem[0]['itemtype'] != 2 || $rsItem[0]['servicecost'] != 0)
                    continue;
                
                $totalJobType = count($rsJobType);
                foreach ($rsJobType as $jobType) {
                
                    // '.$truckingServiceWorkOrder->tableName.'.isoutsource = 0   // mobil outsource jg ikut dihitung, gpp 
                    // PROBLEM kalo jml SO berbeda dengan jml SPK
                    
                    
                    // kurang detail key
                    $sql = 'update 
                                '.$truckingServiceWorkOrder->tableName.'
                            set 
                                invoicekey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).',
                                sellingprice = '.$this->oDbCon->paramString(($itemDetail['priceinunit']/$totalJobType)).'
                            where
                                '.$truckingServiceWorkOrder->tableName.'.refdetailkey = '.$this->oDbCon->paramString($itemDetail['refsodetailkey']).' and
                                '.$truckingServiceWorkOrder->tableName.'.jobtypekey = '.$this->oDbCon->paramString($jobType['jobtypekey']).' and 
                                '.$truckingServiceWorkOrder->tableName.'.invoicekey = 0 and
                                '.$truckingServiceWorkOrder->tableName.'.statuskey = 3
                            order by pkey desc limit '. $qty; 
                    $this->oDbCon->execute($sql);
                    
                    //  '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($jobOrderKey).' and
                }
                 
                
            } 
        }
         
        // add to car turnover
        $rsWorkOrder = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.invoicekey' , $rsHeader[0]['pkey'], true); 
        
        $rsObjKey = $this->getTableKeyAndObj($this->tableName);   
        foreach($rsWorkOrder as $workOrder){
            $rsJO = $truckingServiceOrder->getDataRowById($workOrder['refkey']);
            
            //update CarProfitLoss 
            $arrParam = array();	 
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['refCode'] = $rsHeader[0]['code'];
            $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
            $arrParam['selWarehouse'] = $workOrder['warehousekey'];
            $arrParam['hidRefTable'] = $rsObjKey['key'];
            $arrParam['hidCarKey'] = $workOrder['carkey'];  
            $arrParam['hidRefKey1'] =  $workOrder['pkey']; 
            $arrParam['refCode1'] =  $workOrder['code'];  
            $arrParam['hidRefKey2'] =  $rsJO[0]['pkey']; 
            $arrParam['refCode2'] =  $rsJO[0]['code'];   
            $arrParam['amount'] =  $workOrder['sellingprice'];
            $arrParam['selStatus'] = 1;

            $arrayToJs =  $carTurnover->addData($arrParam); 
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);   

        }
    }
        
    function updateVendorCommission($rsHeader,$rsDetail){
        
        $item = new Item();
        $truckingServiceOrder = new TruckingServiceOrder();
        $truckingServiceOrderCategory = new TruckingServiceOrderCategory();
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        $ap = new AP(); 
        $termOfPayment = new TermOfPayment();
        $car = new Car();
        $supplier = new Supplier();
         
        $rsWorkOrder = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.invoicekey' , $rsHeader[0]['pkey']); 
        
        $rsObjKey = $this->getTableKeyAndObj($this->tableName);
 
        $arrReturnForGL = array();
        
        foreach($rsWorkOrder as $workOrder){
            //if($workOrder['isoutsource'] || empty($workOrder['vendorkey']))
            if($workOrder['isoutsource'])   continue;
             
            $rsCar = $car->getDataRowById($workOrder['carkey']);
            if(empty($rsCar) || empty($rsCar[0]['supplierkey']) || $rsCar[0]['vendorpartnershiptype'] == VEHICLE_PARTNERSHIP_TYPE['contract']) continue; // hanya proses yg mobilnya mobil titipan
              
            //$this->setLog($rsCar[0]['policenumber'],true);
            
            $adminFee = $rsCar[0]['adminfee'];
            $vendorkey = $rsCar[0]['supplierkey'];
            $rsSupplier = $supplier->searchData($supplier->tableName.'.pkey', $vendorkey); 
            $duedays = $rsSupplier[0]['duedays'];  
             
            // blm potong reimburse kah ?
            $totalcost = 0;
            $rsCost = $truckingServiceWorkOrder->getCostDetail($workOrder['pkey'],'',' and '.$truckingServiceWorkOrder->tableItem.'.shareprofit = 1');
             for($i=0;$i<count($rsCost);$i++){
                if($rsCost[$i]['amount']<=0) continue; 
                $totalcost += $rsCost[$i]['amount'];
            }
            
            if($workOrder['drivercommission']>0)
                $totalcost += $workOrder['drivercommission'];

            if($workOrder['codrivercommission']>0)
                $totalcost += $workOrder['codrivercommission'];

            // jangan lupa kalkulasi diskon di invoice
            // harusnya suda dikalkukasi di loop di atas (car turn over)
             
            //$this->setLog($workOrder['sellingprice'] .'-'. $totalcost,true);
            $amount = $workOrder['sellingprice'] - $totalcost - $adminFee;
             
            if ($amount == 0) continue;
             
            $note = $rsCar[0]['policenumber'];
                
            $arrParam = array();	 
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefCode'] = $rsHeader[0]['code'];
            $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
            $arrParam['selWarehouse'] = $workOrder['warehousekey'];
            $arrParam['hidRefTable'] = $rsObjKey['key'];
            $arrParam['hidCarKey'] = $workOrder['carkey'];  
            $arrParam['hidSupplierKey'] = $vendorkey;  
            $arrParam['hidRefKey2'] =  $workOrder['pkey']; 
            $arrParam['hidRefCode2'] =  $workOrder['code'];  
            $arrParam['hidRefDate'] =   $this->formatDBDate($workOrder['trdate'],'d / m / Y');  
            $arrParam['amount'] =  $amount;
            $arrParam['selStatus'] = 1;
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            $arrParam['autoTax'] = 1;
            $arrParam['trDesc'] = $note; 
            $arrParam['selAPType'] = AP_TYPE['serviceOutsource'];
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P'.$duedays.'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
  
                
            $arrayToJs =  $ap->addData($arrParam); 
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);   

            // add utk informasi jurnal
            array_push(
                $arrReturnForGL,
                array(
                    'coakey' => $supplier->getAPCOAKey($vendorkey,$workOrder['warehousekey']),
                    'amount' => $amount,
                )
            );
            
        }
        
        return $arrReturnForGL;
    }   

    function validateCancel($rsHeader,$autoChangeStatus=false){
        $id = $rsHeader[0]['pkey'];
         
        if(!$this->validateAutoReverseGL($id))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['generalJournal'][6],true);
            
        $ar = new AR();
        $rsARKey = $ar->getTableKeyAndObj($this->tableName); 
        
        // cek invoice partial
        // cari di partial invoice
        $sql = 'select * from '.$this->tablePartialInvoice.' where refinvoicekey = '.$rsHeader[0]['pkey'].' and reflinkinvoiceheaderkey <> 0 ';
        $rsPartial = $this->oDbCon->doQuery($sql); 
        if(!empty($rsPartial)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['truckingServiceOrderInvoice'][4],true);
        
        //cek DP
        $customerDownpayment = new CustomerDownpayment(); 
        $rsDP = $customerDownpayment->searchData($customerDownpayment->tableName.'.refheaderkey', $id, true, ' and '.$customerDownpayment->tableName.'.statuskey in (2,3) ');
        if(!empty($rsDP)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['downpayment'][6],true);
        
		//cek ad AR Service terbayar
		$rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and refkey = '.$this->oDbCon->paramString($id).' and ('.$ar->tableName.'.statuskey = 2 or '.$ar->tableName.'.statuskey = 3)');
		if(!empty($rsAR)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ar'][2],true);


		//cek ad AP Bagi hasil sudah terbayar blm
        // ini masalah kalo gk pake CN / DN
	    $ap = new AP();  
		$rsAP = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$ap->tableName.'.statuskey in (2,3))');
		if(!empty($rsAP)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2],true);
 
	   /*
        // gk perlu validasi, kadang perlu kirim invoice revisian saja
        
 		$salesOrderInvoiceReceipt = new SalesOrderInvoiceReceipt();
		$rsReceipt = $salesOrderInvoiceReceipt->getInvoiceReceipt($id,' and '.$salesOrderInvoiceReceipt->tableName.'.statuskey in (2,3) ');
		if(!empty($rsReceipt)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.$rsReceipt[0]['code'].'</strong>, ' .$this->errorMsg[203],true);
        */
	 } 
     
	function cancelTrans($rsHeader,$copy){ 
		 
        $truckingServiceOrder = new TruckingServiceOrder();
		$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        $carTurnover = new CarTurnover();
	    $ap = new AP();       
        
		$id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
		 
		if ($rsHeader[0]['statuskey'] == 1) 
        	return; 
        
		 for($i=0;$i<count($rsDetail);$i++){
            $soKey = $rsDetail[$i]['salesorderkey']; 
            $rsSO =  $truckingServiceOrder->getDataRowById($soKey);
            if ($rsSO[0]['statuskey'] == 6)
                $truckingServiceOrder->changeStatus($soKey,5,'',false,true); 
        } 
        
        //cancel partialinvoice
        $this->deletePartialInvoice($id);
            
        
        $customerDownpayment = new CustomerDownpayment();  
        $rsDP = $customerDownpayment->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and '.$customerDownpayment->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsDP);$i++) { 
			$arrayToJs = $customerDownpayment->changeStatus($rsDP[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
        
        $salesOrderInvoiceReceipt = new SalesOrderInvoiceReceipt();
		$rsReceipt = $salesOrderInvoiceReceipt->getInvoiceReceipt($id,' and '.$salesOrderInvoiceReceipt->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsReceipt);$i++) { 
			$arrayToJs = $salesOrderInvoiceReceipt->changeStatus($rsReceipt[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }        
        
		$ar = new AR();
        $rsObjKey = $this->getTableKeyAndObj($this->tableName); 
        
        $rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsObjKey['key']).' and refkey = '.$this->oDbCon->paramString($id).' and '.$ar->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAR);$i++) { 
			$arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
         
       $rsAPVendor = $ap->searchData('','',true,' and '.$ap->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsObjKey['key']).' and '.$ap->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ap->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAPVendor);$i++) { 
			$arrayToJs = $ap->changeStatus($rsAPVendor[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }

        // cancel car turnover
        $sql = 'update '.$truckingServiceWorkOrder->tableName.' set  invoicekey = 0, sellingprice = 0 where  invoicekey = ' . $this->oDbCon->paramString($id); 
        $this->oDbCon->execute($sql); 
        $carTurnover->cancelMovement($id, $rsObjKey['key']);


		if ($copy)
			$this->copyDataOnCancel($id);	  
		   
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
      
	}
    
    function validateBackConfirm($rsHeader){ 
        $pkey = $rsHeader[0]['pkey'];
        
        $salesOrderInvoiceReceipt = new SalesOrderInvoiceReceipt();
		$rsReceipt = $salesOrderInvoiceReceipt->getInvoiceReceipt($pkey,' and '.$salesOrderInvoiceReceipt->tableName.'.statuskey in (2,3) ');
		if(!empty($rsReceipt)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.$rsReceipt[0]['code'].'</strong> ' .$this->errorMsg[203],true);
	  
    
    } 
    
    function getInvoiceByMonth($startPeriod, $endPeriod){
         $sql = 'select 
                    month(trdate) as month,  
                    DATE_FORMAT(trdate, \'%b\')  as monthname, 
                    year(trdate) as year, 
                    sum(grandtotal) as total
                from 
                    '.$this->tableName.'
                where (statuskey = 2 or statuskey = 3) and trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')';
      
          $sql .=  $this->getWarehouseCriteria() ;             
          $sql .=  ' group by year(trdate),month(trdate)';
       
        
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
     
    
    function getReferenceCode($id){
        
        $truckingServiceOrder = new TruckingServiceOrder();
        
        $arrDONumber = array();
        $arrShipmentNumber = array();
        
        $rsDetail = $this->getDetailById($id);
        for($i=0;$i<count($rsDetail);$i++){
            $rsServiceDetail = $this->getItemDetail($rsDetail[$i]['pkey']);
            for($j=0;$j<count($rsServiceDetail);$j++){ 
                if (!empty($rsServiceDetail[$j]['refsodetailkey'])){
                    $rsSO = $truckingServiceOrder->getDataRowById();
                    if (!empty($rsSO[0]['donumber'])){ 
                        array_push($arrDONumber, $rsSO[0]['donumber']);
                        array_push($arrShipmentNumber, $rsSO[0]['shipmentnumber']);
                    }
                }
            }
        }
        
        
        return array('donumber' => $arrDONumber, 'shipmentnumber' => $arrShipmentNumber);
    }
    
    function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value,
                '.$this->tableName . '.code,
                '.$this->tableName . '.trdate,
                '.$this->tableName . '.grandtotal
            from 
                '.$this->tableName . ',
                '.$this->tableStatus.'  
            where  		
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
        ';
        
        $sql .=  $this->getCompanyCriteria() ;
        return $sql;
        
    }
    
    function getSalesOrderObj() {
            return new TruckingServiceOrder();
    }
    
    function getPaymentDetail($invoicekey){
        
        $objServiceOrder = $this->getSalesOrderObj();
        
        // diskon gk perlu dimasukan, jika memang perlu dibuat field terpisah saja
        $sql = 'select 
                     '.$this->tableAR.'.refheaderkey as invoicekey, 
                     '.$this->tableARPaymentHeader.'.code as paymentcode, 
                     '.$this->tableARPaymentHeader.'.trdate as paymentdate, 
                     '.$this->tableARPaymentDetail.'.amount, 
                     '.$this->tableARPaymentDetail.'.taxamount,  
                     '.$this->tableARPaymentDetail.'.discount 
                from 
                    '.$this->tableARPaymentHeader.',
                    '.$this->tableARPaymentDetail.',
                    '.$this->tableAR.'
                where   
                    '.$this->tableARPaymentDetail.'.refkey = '.$this->tableARPaymentHeader.'.pkey  and
                    '.$this->tableARPaymentDetail.'.arkey = '.$this->tableAR.'.pkey  and
                    '.$this->tableAR.'.refheaderkey in ('.$this->oDbCon->paramString($invoicekey,',').') and
                    '.$this->tableARPaymentHeader.'.statuskey in (2,3) 
                ';
        
        return $this->oDbCon->doQuery($sql);
    }
    
    
    function getJODetail($invoicekey){
         
        $objServiceOrder = $this->getSalesOrderObj();
        
        $sql = 'select 
                     '.$this->tableName.'.pkey, 
                     '.$objServiceOrder->tableName.'.code as jocode, 
                     '.$objServiceOrder->tableName.'.trdate as jodate
                from 
                    '.$this->tableName.',
                    '.$this->tableNameDetail.',
                    '.$objServiceOrder->tableName.' 
                where   
                    '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($invoicekey,',').') and 
                    '.$this->tableNameDetail.'.invoicetype = 1 and 
                    '.$this->tableNameDetail.'.salesorderkey = '.$objServiceOrder->tableName.'.pkey and
                    '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey   
                ';
        return $this->oDbCon->doQuery($sql);
    }
    
    function getDetailForAPI($arrKey){ 
        $rsDetailsCol = array();
     /*   $rsDetails = $this->getDetailWithRelatedInformation($arrKey); 
        $arrRefKey = array_column($rsDetails,'refkey');*/
  
        $rsDetails = $this->getJODetail($arrKey);
        $rsDetails = $this->reindexDetailCollections($rsDetails,'pkey');  
        $rsDetailsCol['job_order_detail'] = $rsDetails;
              
        $rsDetails = $this->getPaymentDetail($arrKey);
        $rsDetails = $this->reindexDetailCollections($rsDetails,'invoicekey');  
        $rsDetailsCol['payment_detail'] = $rsDetails;
             
             
        return $rsDetailsCol;
    }
        

    
    }

?>