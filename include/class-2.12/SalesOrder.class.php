<?php
  
class SalesOrder extends BaseClass{ 
  

    function __construct(){

            parent::__construct();

            $this->tableName = 'sales_order_header';
            $this->tableNameDetail = 'sales_order_detail';
            $this->tableDetailSerial = 'sales_order_detail_sn';
            $this->tableCustomer = 'customer';
            $this->tableCity = 'city';
            $this->tableEmployee = 'employee';
            $this->tableWarehouse = 'warehouse'; 
            $this->tableStatus = 'transaction_status';
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
		    $this->tableShipment  = 'shipment'; 
		    $this->tableShipmentService = 'shipment_detail';
            $this->tableTermOfPayment = 'term_of_payment'; 
            $this->tableARStatus = 'ar_status'; 
            $this->tableFile = 'sales_order_file';
            $this->tableSalesOrderRecurringSubscription = 'sales_order_recurring_subscription_header';
            $this->tableAvailabilityStatus = 'availability_status';
            $this->isTransaction = true; 		

            $this->autoPrintURL = 'print/salesOrder'; 
			$this->tempInvoice = 'invoice-temp/'; 
            $this->importUrl = 'import/salesOrder';
		    $this->uploadFileFolder = 'sales-order/';
         
            $this->securityObject = 'SalesOrder';   
        
            $this->useStorage = $this->useStorage('S3');		

            $this->arrLinkedTable = array(); 
            $defaultFieldName = 'refkey';
            array_push($this->arrLinkedTable, array('table'=>'sales_delivery_header','field'=>$defaultFieldName));  
            array_push($this->arrLinkedTable, array('table'=>'ar','field'=>$defaultFieldName));  

            $this->arrDataDetailSN = array();  
            $this->arrDataDetailSN['pkey'] = array('hidDetailSNKey');
            $this->arrDataDetailSN['refkey'] = array('hidDetailKey','ref');  
            $this->arrDataDetailSN['refheaderkey'] = array('pkey','ref');  
            $this->arrDataDetailSN['serialnumber'] = array('serialNumber');
            $this->arrDataDetailSN['costinbaseunit'] = array('COGSSN', 'number');
        
            $this->arrDataDetail = array();  
            $this->arrDataDetail['pkey'] = array('hidDetailKey',array('dataDetail' => array('dataset' => $this->arrDataDetailSN, 'tableName' => $this->tableDetailSerial)));
            $this->arrDataDetail['refkey'] = array('pkey','ref');
            $this->arrDataDetail['refmarketplacekey'] = array('refMarketplaceKey');
            $this->arrDataDetail['itemkey'] = array('hidItemKey'); 
            $this->arrDataDetail['qty'] = array('qty','number'); 
            $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit','number');
            $this->arrDataDetail['unitkey'] = array('selUnit');
            $this->arrDataDetail['priceinunit'] = array('priceInUnit','number'); 
            $this->arrDataDetail['priceinbaseunit'] = array('priceInBaseUnit','number'); 
            $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier','number');
            $this->arrDataDetail['discounttype'] = array('selDiscountType');
            $this->arrDataDetail['discount'] = array('discountValueInUnit','number');
            $this->arrDataDetail['total'] = array('detailSubtotal','number');
            $this->arrDataDetail['costinbaseunit'] = array('cogs','number');
            $this->arrDataDetail['profit'] = array('detailProfit','number');
            $this->arrDataDetail['deliveredqtyinbaseunit'] = array('deliveredQtyInBaseUnit','number');
            $this->arrDataDetail['itemtype'] = array('itemType');
            $this->arrDataDetail['weight'] = array('itemWeight','number');
            $this->arrDataDetail['trdesc'] = array('trDetailDesc');
            $this->arrDataDetail['headerdiscvalueinunit'] = array('headerDiscValueInUnit','number');
            $this->arrDataDetail['qtyinpcs'] = array('qtyInPcs', 'number');
            $this->arrDataDetail['priceinpcs'] = array('priceInPcs', 'number');
            $this->arrDataDetail['ispriceinpcs'] = array('chkPriceInPcs');
            $this->arrDataDetail['packagingcodekey'] = array('selPackagingCode');
            $this->arrDataDetail['snlist'] = array('snList');
 
        
            $this->arrPaymentDetail = array(); 
            $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
            $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
            $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
            $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 

            $this->arrVoucherDetail = array(); 
            $this->arrVoucherDetail['pkey'] = array('hidDetailVoucherKey');
            $this->arrVoucherDetail['refkey'] = array('pkey', 'ref');
            $this->arrVoucherDetail['amount'] = array('voucherAmount',array('datatype' => 'number','mandatory'=>true));
            $this->arrVoucherDetail['voucherkey'] = array('hidVoucherKey',array('mandatory'=>true)); 
            $this->arrVoucherDetail['vouchertypekey'] = array('hidVoucherType'); 
    		$this->arrVoucherDetail['vouchercategorykey'] = array('hidVoucherCategoryKey'); 
  
            $arrDetails = array();
            array_push($arrDetails, array('dataset' => $this->arrDataDetail));
            array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
            array_push($arrDetails, array('dataset' => $this->arrVoucherDetail, 'tableName' => $this->tableVoucherDetail));


            if($this->useStorage){ 

                $this->arrDataFileDetail = array();  
                $this->arrDataFileDetail['pkey'] = array('hidDetailFileKey');
                $this->arrDataFileDetail['refkey'] = array('pkey','ref');
                $this->arrDataFileDetail['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));

                array_push($arrDetails, array('dataset' => $this->arrDataFileDetail, 'tableName' => $this->tableFile));
            }else{ 
                array_push($arrDetails, array('dataset' => $this->arrDataFile, 'tableName' => $this->tableFile, 
                                              'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
                                              'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader')); 
            }

            $this->arrData = array(); 
            $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
            $this->arrData['code'] = array('code');
            $this->arrData['customcodekey'] = array('selCustomCode'); 
            $this->arrData['marketplaceorderid'] = array('marketplaceOrderId'); 
            $this->arrData['marketplaceinvoiceurl'] = array('marketplaceInvoiceURL'); 
            $this->arrData['trdate'] = array('trDate','datetime');
            $this->arrData['recurringdate'] = array('recurringDate','datetime');
            $this->arrData['warehousekey'] = array('selWarehouseKey');
            $this->arrData['customerkey'] = array('hidCustomerKey');
            $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
            $this->arrData['trdesc'] = array('trDesc');
            $this->arrData['subtotal'] = array('subtotal','number');
            $this->arrData['finaldiscounttype'] = array('selFinalDiscountType','number');
            $this->arrData['finaldiscount'] = array('finalDiscount','number');
            $this->arrData['finaldiscounttype2'] = array('selFinalDiscountType2','number');
            $this->arrData['finaldiscount2'] = array('finalDiscount2','number');        
            $this->arrData['beforetaxtotal'] = array('beforeTaxTotal','number');
            $this->arrData['ispriceincludetax'] = array('isPriceIncludeTax');
            $this->arrData['taxpercentage'] = array('taxPercentage','number');
            $this->arrData['taxvalue'] = array('taxValue','number');
            $this->arrData['shipmentfee'] = array('shipmentFee','number'); 
            $this->arrData['commissionfee'] = array('commissionFee','number'); 
            $this->arrData['servicefee'] = array('serviceFee','number');
            $this->arrData['affiliatefee'] = array('affiliateFee','number'); //biaya afliasi BOS
            $this->arrData['diffshippingfee'] = array('diffShippingFee','number'); // selisih ongkir BOS
            $this->arrData['freeshippingfee'] = array('freeShippingFee','number'); // bebas ongkir BOS 
            $this->arrData['etccost'] = array('etcCost','number');
            $this->arrData['grandtotal'] = array('grandtotal','number');
            $this->arrData['totalpayment'] = array('totalPayment','number');
            $this->arrData['balance'] = array('balance','number'); 
            $this->arrData['isfulldeliver'] = array('chkIsFullDeliver');
            $this->arrData['profit'] = array('profit','number');
            $this->arrData['statuskey'] = array('selStatus');
            $this->arrData['saleskey'] = array('hidSalesKey');
            $this->arrData['recipientname'] = array('recipientName');
            $this->arrData['recipientphone'] = array('recipientPhone');
            $this->arrData['recipientemail'] = array('recipientEmail');
            $this->arrData['recipientaddress'] = array('recipientAddress');
            $this->arrData['recipienttrdesc'] = array('recipientTrDesc');
            $this->arrData['recipientzipcode'] = array('recipientZipcode');
            $this->arrData['recipientcitykey'] = array('hidRecipientCityKey');
            $this->arrData['recipientmapaddress'] = array('mapAddress');
            $this->arrData['recipientlatlng'] = array('hidLatLng');
            $this->arrData['pointvalue'] = array('pointValue','number');
            $this->arrData['useinsurance'] = array('useInsurance');
            $this->arrData['shipmentkey'] = array('selShipment');
            $this->arrData['shipmentservicekey'] = array('selShipmentService');
            $this->arrData['isdropship'] = array('chkIsDropship');
            $this->arrData['dropshipername'] = array('dropshiperName');
            $this->arrData['dropshiperphone'] = array('dropshiperPhone');
            $this->arrData['dropshiperaddress'] = array('dropshiperAddress'); 
            $this->arrData['marketplacekey'] = array('marketplaceKey'); 
            $this->arrData['refcode'] = array('refCode');  
            $this->arrData['totalweight'] = array('totalWeight','number');
            $this->arrData['voucherkey'] = array('hidTransVoucherKey');
			$this->arrData['totalCOGS'] = array('totalCOGS');
			$this->arrData['refsubscriptionkey'] = array('refSubscriptionKey');
			$this->arrData['vanumber'] = array('vaNumber');
        	$this->arrData['overwriteGL'] = array('overwriteGL'); 
        	$this->arrData['checksum'] = array('checksum');
        	$this->arrData['isfromfe'] = array('fromFE');
            $this->arrData['availabilitykey'] = array('selAvailability');
            
			$this->activeModule = $this->isActiveModule(array('salesorderrecurringsubscription','marketplace'));
		
		
            $this->arrDataListAvailableColumn = array(); 
            array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 120));
            
			if($this->activeModule['salesorderrecurringsubscription'])
				array_push($this->arrDataListAvailableColumn, array('code' => 'subscriptionCode','title' => 'subscriptionCode','dbfield' => 'subscriptioncode','default'=>true, 'width' => 120));
            
			array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'datetime','title' => 'dateAndTime','dbfield' => 'trdate','default'=>true, 'width' => 130, 'align'=>'center', 'format' => 'datetime'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200));
            array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
            array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
            array_push($this->arrDataListAvailableColumn, array('code' => 'salesman','title' => 'salesman','dbfield' => 'salesname', 'width' => 150));
            array_push($this->arrDataListAvailableColumn, array('code' => 'recipientname','title' => 'recipient','dbfield' => 'recipientname', 'width' => 150));
            
			if($this->activeModule['marketplace'])
				array_push($this->arrDataListAvailableColumn, array('code' => 'pickup','title' => '[icon]requestPickup', 'dbfield' => 'ispickupicon', 'default'=>true, 'width' => 50, 'align' => 'center'));
            
			array_push($this->arrDataListAvailableColumn, array('code' => 'shipment','title' => 'shipment', 'dbfield' => 'shipmentname',   'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'paymentsuccessicon','title' => '[icon]paymentGateway', 'dbfield' => 'paymentsuccessicon',  'width' => 40, 'align' => 'center'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'paidstatus','title' => 'payment', 'dbfield' => 'paidstatusname',  'width' => 40));
            
            
            $this->arrSearchColumn = array ();
            array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
            array_push($this->arrSearchColumn, array('Kode Ref', $this->tableName . '.refcode'));
            
			if($this->activeModule['salesorderrecurringsubscription'])
				array_push($this->arrSearchColumn, array('Kode Berlangganan', $this->tableSalesOrderRecurringSubscription . '.code'));
            
			array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
            array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse. '.name'));
            array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer. '.name'));
            array_push($this->arrSearchColumn, array('Sales', $this->tableEmployee. '.name'));
            array_push($this->arrSearchColumn, array('Nama Penerima',$this->tableName.'.recipientname'));
            array_push($this->arrSearchColumn, array('No. Handphon Penerima',$this->tableName.'.recipientphone'));
            array_push($this->arrSearchColumn, array('Email Penerima',$this->tableName.'.recipientemail'));
            array_push($this->arrSearchColumn, array('Alamat Penerima',$this->tableName.'.recipientaddress'));
            array_push($this->arrSearchColumn, array('Total', $this->tableName. '.grandtotal'));
            array_push($this->arrSearchColumn, array('Catatan', $this->tableName. '.trdesc'));
            
            $this->printMenu = array();  
            array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice'],  'icon' => 'print', 'url' => 'print/salesOrder'));
            array_push($this->printMenu,array('code' => 'printDeliveryNotes', 'name' => $this->lang['print'] . ' ' .$this->lang['deliveryNotes'],  'icon' => 'print', 'url' => 'print/salesOrderDelivery'));
            array_push($this->printMenu,array('code' => 'printShippingLabel', 'name' => $this->lang['print'] . ' ' .$this->lang['shippingLabel'],  'icon' => 'print', 'url' => 'print/salesLabel'));
         
            if($this->activeModule['marketplace']) {
                array_push($this->printMenu,array('code' => 'printSeparator', 'name' => '-'));
                array_push($this->printMenu,array('code' => 'printAirwayBill', 'name' => $this->lang['print'] . ' ' .$this->lang['airwayBill'],  'icon' => 'print', 'url' => 'print/airwayBill'));
                array_push($this->printMenu,array('code' => 'printInvoiceMarketplace', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice'] .' (Marketplace)',  'icon' => 'print', 'url' => 'print/marketplaceInvoice'));
            }
        
			$this->multiLevelDiscount = $this->loadSetting('multiLevelDiscount');
             
            $this->includeClassDependencies(array(
                   'TermOfPayment.class.php', 
                   'Warehouse.class.php',  
                   'PaymentMethod.class.php', 
                   'Shipment.class.php', 
                   'City.class.php', 
                   'Customer.class.php', 
                   'Item.class.php', 
                   'Downpayment.class.php', 
                   'CustomerDownpayment.class.php',
                   'RewardsPoint.class.php', 
                   'COALink.class.php',
                   'VoucherTransaction.class.php', 
                   'ItemMovement.class.php',
                   'Marketplace.class.php',
                   'PaymentConfirmation.class.php',
                   'AR.class.php',
                   'ARPayment.class.php',
                   'SalesDelivery.class.php', 
                   'ChartOfAccount.class.php',
                   'GeneralJournal.class.php',
                   'ItemUnit.class.php',
                   'Brand.class.php',
                   'Category.class.php',
                   'ItemCategory.class.php',
                   'ItemCondition.class.php',
                   'Biteship.class.php',
                   'RecurringPeriod.class.php',
                   'SalesOrderRecurringSubscription.class.php',
				   'SalesOrderReturn.class.php',
                   'PackagingCode.class.php'
            ));  
                       
                       

            $this->actionMenu = array();  
            $function = '  
                    var phpDataListFile = tabParam[selectedTabId].phpDataListFile; 

                    if (selectedPkey.length == 0){
                        showMsgDialog ("Anda belum memilih data yang hendak di request pickup."); 
                        break ;
                    }

                    var msg =  "Anda yakin akan melakukan request pickup ?";

                    $( "#dialog-message" ).html(msg);
                    $( "#dialog-message" ).dialog({
                      width: 300,
                      modal: true,
                      title:"Konfirmasi Request Pickup", 
                      open: function() {
                          $(this).closest(\'.ui-dialog\').find(\'.ui-dialog-buttonpane button:last\').focus();
                      },
                      buttons : {
                          OK : function (){
                                     $.ajax({
                                        type: "POST",
                                        url:  phpDataListFile,
                                        data:{action:"requestpickup", 
                                            selectedPkey:selectedPkey
                                        },
                                    }).done(function( data ) { 
                                        updateData(false); 
                                    });  

                                    $( this ).dialog( "close" );
                          },
                          Cancel : function (){ 
                            $( this ).dialog( "close" );
                          }
                      },
                      });
            ';
        
        
            if($this->activeModule['marketplace'])
                array_push($this->actionMenu,array('code' => 'requestPickUp', 'name' => $this->lang['requestPickup'],  'icon' => 'requestPickup', 'function' => $function)); 
      
            array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
            array_push($this->filterCriteria, array('title' => $this->lang['payment'],'field' => $this->tableARStatus.'.pkey', 'alias' => 'arstatuskey', 'sql' => 'select pkey,status as name from '. $this->tableARStatus.' where 1=1' ));
        
       
            $this->overwriteConfig();
    }
 
            
    
    function getQuery(){
 

        $sql = '
            SELECT '.$this->tableName.'.* ,
               '.$this->tableCustomer.'.code as customercode,
               '.$this->tableCustomer.'.name as customername,
               '.$this->tableWarehouse.'.code as warehousecode,
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableStatus.'.status as statusname ,
               '.$this->tableEmployee.'.name as salesname , 
               '.$this->tableTermOfPayment.'.code as termofpaymentcode,
               '.$this->tableTermOfPayment.'.name as termofpaymentname,
               '.$this->tableTermOfPayment.'.duedays as termofpaymentduedays,
               '.$this->tableARStatus.'.status as paidstatusname,
               concat('.$this->tableShipment.'.name, \'-\','.$this->tableShipmentService.'.servicename) as shipmentname,
               IF('.$this->tableName.'.mprequestpickup > 0, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as ispickupicon';
		
			if($this->activeModule['salesorderrecurringsubscription'])
				$sql .=  ','.$this->tableSalesOrderRecurringSubscription .'.code as subscriptioncode';
				
				
          $sql .= ' FROM 
                '.$this->tableStatus.', 
                '.$this->tableCustomer.' left join '.$this->tableCity.' on  
                     '.$this->tableCustomer.'.citykey = '.$this->tableCity.'.pkey,
                '.$this->tableWarehouse.',
                '.$this->tableName.' 
                    left join '.$this->tableEmployee.' on '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey 
                    left join '.$this->tableShipmentService.' on '.$this->tableName.'.shipmentservicekey = '.$this->tableShipmentService.'.pkey 
                    left join '.$this->tableShipment.' on '.$this->tableShipmentService.'.refkey = '.$this->tableShipment.'.pkey 
                    left join '.$this->tableTermOfPayment.' on '.$this->tableName.'.termofpaymentkey = '.$this->tableTermOfPayment.'.pkey
                    left join '.$this->tableARStatus.' on  '.$this->tableName.'.paidstatuskey =  '.$this->tableARStatus.'.pkey';
					
			if($this->activeModule['salesorderrecurringsubscription'])
				  $sql .= ' left join '. $this->tableSalesOrderRecurringSubscription .' on '. $this->tableName .'.refsubscriptionkey = '. $this->tableSalesOrderRecurringSubscription .'.pkey';
       
				
           $sql .=  ' WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                     '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                     '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
        			 ' .$this->criteria ; 


        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
  
//		$this->setLog($sql,true);
		
        return $sql;
    }  
    
    function afterCommitUpdateData($pkey,$rs){  
        // cek pernah diedit gk, biar gk kekirim lg via backend 
	   if ($rs[0]['isfromfe'] == 1 && empty($rs[0]['modifiedby'])){ 
            $this->sendInvoice($pkey); // get invoice pertama gagal gk tau knp, returnnya kosong 
            //$this->sendNotificationInvoice($pkey);
        }
        
        parent::afterCommitUpdateData($pkey,$rs);    
    }
    
    function afterUpdateData($arrParam, $action){   
        
        $pkey = $arrParam['pkey'];
        
        // kalo add new / update data selama masih di status menunggu
        // harus hitung ulang QOR, kalo nambah terus jd double
        // input 5pcs, terus direvisi jd 7 pcs
        
        // gk bisa digabung dalam updateItemMovement karena kalo cuma add blm masuk ke item movement
      
        //if($arrParam['selStatus'] == TRANSACTION_STATUS['menunggu']){ 
        // gk perlu validasi status karena : selStatus Disabled, dan normalnya setelah konfirmasi sudah tdk bisa update data
        $itemMovement = new ItemMovement();
        $voucherTransaction = new VoucherTransaction();

        $arrItemkey = $arrParam['hidItemKey'];
        $arrVoucherKey = $arrParam['hidVoucherKey']; 
        $arrVoucherType = $arrParam['hidVoucherType']; 
         
        foreach($arrItemkey as $itemkey)
            $itemMovement->updateQOR($itemkey,$arrParam['selWarehouseKey']);
 
        if (isset($arrParam['hidSendEmail']) && !empty($arrParam['hidSendEmail'])){
            $this->sendInvoice($pkey);
        }
        
        if (isset($arrParam['fromFE']) && !empty($arrParam['fromFE'])){
            
            // nanti diaktifkan lg, utk testing dulu
            $_SESSION[$this->loginSession]['cart']  = array();
            $_SESSION[$this->loginSession]['pointValue'] = 0;
            
            // harus after commit
            //$this->sendInvoice($pkey); // get invoice pertama gagal gk tau knp, returnnya kosong
            //$this->sendNotificationInvoice($pkey);
        }
        
        // kalo ad marketplace
        if($this->isActiveModule('marketplace')) 
             $this->updateMarketplaceQOH($pkey);
		
		// update voucher, ketika save saja, harus sudah dilock
		// kalo ganti voucher? harus buka voucher lama JIKA reftransna masih nyambung
		// update status, ref transaksi dll
		
		$useVoucherPoint = $this->loadSetting('transactionVoucherPoint');
		
		if($useVoucherPoint == 1){ 
                
            $voucher = new Voucher();
			$refTableKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
            
            // kalo tipe collectible ketika add data, generate voucher transactionnya dulu 
            // akan divoverwrite kalo jenisnya collectible
            
            // harus pisahin mana yg collectible mana yg reguler,
            // untuk kedepanny kalo support beberapa voucher
            
            $regularVoucherKey = array();
            for($i=0;$i<count($arrVoucherKey);$i++){ 
                 if($arrVoucherType[$i] == VOUCHER_TYPE['regular'] || $arrVoucherType[$i] == VOUCHER_TYPE['claim'])
                     array_push($regularVoucherKey,$arrVoucherKey[$i]);
            }
           
            
            // khusus kalo dr front end & API
            
			$createVoucherTransaction = ( (isset($arrParam['fromFE']) && !empty($arrParam['fromFE'])) || 
										  (isset($arrParam['_mnv-api']) && !empty($arrParam['_mnv-api']))
										) ? true : false;
		 
            if($action == INSERT_DATA &&  $createVoucherTransaction ){
	 
                $collectibleVoucherKey = array();
                for($i=0;$i<count($arrVoucherKey);$i++){ 
					 
                    if($arrVoucherType[$i] == VOUCHER_TYPE['collectible']){
 
                        $voucherTransaction = new VoucherTransaction();

                        $rsVoucher = $voucher->searchDataRow(array($voucher->tableName.'.pkey',$voucher->tableName.'.enddate',
                                                                   $voucher->tableName.'.minamount',$voucher->tableName.'.maxdiscount',
                                                                   $voucher->tableName.'.discounttype',$voucher->tableName.'.value')
                                                             , 'and '.$voucher->tableName.'.pkey = ' .  $this->oDbCon->paramString($arrVoucherKey[$i])
                                                            );

                        // kalo jenisnya collectible
                        // kalo add aj, create voucher traansaksi dulu 

                        $arrData = array();

                        $arrData['trDate'] = $arrParam['trDate']; 
                        $arrData['expDate'] =  $this->formatDBDate($rsVoucher[0]['enddate'],'d / m / Y');   
                        $arrData['hidVoucherKey'] = $arrVoucherKey[$i]; 
                        $arrData['hidCustomerKey'] = $arrParam['hidCustomerKey']; 
                        $arrData['selWarehouse'] = $arrParam['selWarehouseKey']; 
                        $arrData['selDiscountType'] = $rsVoucher[0]['discounttype']; 
                        $arrData['minAmount'] = $rsVoucher[0]['minamount']; 
                        $arrData['maxDiscount'] = $rsVoucher[0]['maxdiscount']; 
                        $arrData['value'] = $rsVoucher[0]['value']; 
                        $arrData['refTableType'] = $refTableKey; 
                        $arrData['selStatus'] = 1;  
                        $arrData['code'] = 'xxxxxx'; 

                        // update ulang voucherkey, karena pertamanya collectible 
						// SEMENTARA TIDAK DIHANDLE KALO GAGAL ADD VOUCHER, jd kalo gagal add voucher, transaksi ke rollback sepertinya
						
                        $arrayToJs = $voucherTransaction->addData($arrData);  
						
						if ($arrayToJs[0]['valid']){
							if(isset($arrayToJs[0]['data']['pkey'])){ 
								$newVoucherKey = $arrayToJs[0]['data']['pkey'];
								array_push($collectibleVoucherKey,$newVoucherKey);

								$sql = 'update  '. $this->tableVoucherDetail.' 
										set voucherkey = '. $this->oDbCon->paramString($newVoucherKey).' 
										where refkey = '. $this->oDbCon->paramString($pkey).' and voucherkey = '. $this->oDbCon->paramString($arrVoucherKey[$i]) ;
								$this->oDbCon->execute($sql);
							}else{
								// harusnya gk kepanggil, karena ke throw ..
								$sql = 'delete from '. $this->tableVoucherDetail.' 
										where refkey = '. $this->oDbCon->paramString($pkey).' and voucherkey = '. $this->oDbCon->paramString($arrVoucherKey[$i]) ;

								$this->oDbCon->execute($sql);
							}

						}else{
							throw new Exception($arrayToJs[0]['message']);
						}
                        
                    } 
                }
                
                // khusus dr front end aja 
                // meskipun kosong, perlu tetep kirim, utk reset transaksi yg lama (jaga2), nanti di function baru di ifelse  
                // jgn 2x panggil, nanti salah satunya kereset, jd dimerge saja, terus panggil sekalian dibawah
                // $voucherTransaction->useVoucher($collectibleVoucherKey,$pkey,$refTableKey);
                $regularVoucherKey = array_merge($regularVoucherKey,$collectibleVoucherKey);
                
            }
              
            $voucherTransaction->useVoucher($regularVoucherKey,$pkey,$refTableKey);
		}
        
        
        if($action == INSERT_DATA) {
                // update payment gateway URL 
                $paymentGateway = $this->loadSetting('PaymentGateway');
                if(!empty($paymentGateway) && strtolower($paymentGateway) == 'xendit' ){
                    
                    $rsHeader = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.code',$this->tableName.'.grandtotal',$this->tableName.'.recipientemail'),
                                                        ' and '.$this->tableName.'.pkey = ' .  $this->oDbCon->paramString($pkey)
                                                  );
                    
                    $arrTransaction = array();
                    $arrTransaction['pkey'] = $rsHeader[0]['pkey'];
                    $arrTransaction['token'] = $this->getTransactionToken($rsHeader); 
                    $arrTransaction['code'] = $rsHeader[0]['code'];
                    $arrTransaction['currency'] ='IDR'; // saat ini hanya IDR (!empty($rsCurrency[0]['code'])) ? $rsCurrency[0]['code'] : 'IDR';
                    $arrTransaction['amount'] = $rsHeader[0]['grandtotal'];
                    $arrTransaction['customerEmail'] = $rsHeader[0]['recipientemail'];
                    $arrTransaction['description'] = $rsHeader[0]['code'];

                    $response = $this->getPaymentGatewayInvoice($arrTransaction);

                    if(!empty($response)){
                        $sql = 'update '.$this->tableName.' set paymentgatewayinvoiceurl = '. $this->oDbCon->paramString($response['invoice_url']) .' where pkey = ' . $this->oDbCon->paramString($pkey) ;
                        $this->oDbCon->execute($sql);
                    }
                }

        }
      
         
		
    }
      
    function editData($arrParam){ 
        // kalo edit, marketplacenya jgn diupdate, kalo gk jd 0
        
		unset($this->arrData['marketplacekey']);  
        return parent::editData($arrParam);
	}
    
    
    function afterAddDataOnCopy($pkey, $oldkey){
        $sql = 'update ' .$this->tableNameDetail.' set deliveredqtyinbaseunit = 0 where refkey = ' . $this->oDbCon->paramString($pkey);    
        $this->oDbCon->execute($sql); 
    }
    
    
    function afterStatusChanged($rsHeader){ 
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        
        // knp dulu dinonaktifkan ??
        if ($rsHeader[0]['isfulldeliver'] == 1 && $rsHeader[0]['statuskey'] == 2){  
            $sql = 'update '.$this->tableNameDetail.' set deliveredqtyinbaseunit = qtyinbaseunit where refkey  = '.$this->oDbCon->paramString($rsHeader[0]['pkey']);
            $this->oDbCon->execute($sql); 
             
            // gk boleh langsung change status, karena marketplace perlu membedakan status selesai
            //$this->changeStatus($rsHeader[0]['pkey'],3); 
        }
            
         
        // kalo cancel / konfirmasi 
        if ($rsHeader[0]['statuskey'] == TRANSACTION_STATUS['konfirmasi'] || $rsHeader[0]['statuskey'] == TRANSACTION_STATUS['batal'] ){  
            $itemMovement = new ItemMovement();
            $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
            foreach($rsDetail as $row)
                $itemMovement->updateQOR($row['itemkey'],$rsHeader[0]['warehousekey']); 
        } 
        
        if ($this->isActiveModule('marketplace') && ($rsHeader[0]['statuskey'] == 2 || $rsHeader[0]['statuskey'] == 4)){  
            // gk perl $rsHeader[0]['isfulldeliver'] == 1 karena ad QOR
            $this->updateMarketplaceQOH($rsHeader[0]['pkey']);
        }
      
        if($this->isActiveModule('salesorderrecurringsubscription') && in_array($rsHeader[0]['statuskey'], array(2,4))){
            //update Last Recurring jika sales order di konfirmasi / batalkan
			$salesOrderRecurringSubscription = new SalesOrderRecurringSubscription(); 
			$salesOrderRecurringSubscription->updateLastRecurringDate($rsHeader[0]['refsubscriptionkey']);	
        }
        
        if(PLAN_TYPE['usefrontend']){  
            $item = new Item();
            $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
            $item->updateTotalSold(array_column($rsDetail,'itemkey'));
        }
    }
    
    function updateMarketplaceQOH($pkey){
        $marketplace = new Marketplace();
        $rsDetail = $this->getDetailById($pkey);
        $arrItemKey = array_column($rsDetail,'itemkey'); 
        $marketplace->updateProductsQOHInAllMarketplace($arrItemKey); 
    }
	
	function sendAttachedInvoice($pkey){
		
		$customer = new Customer();
		
		$rs = $this->getDataRowById($pkey);
		if(empty($rs)) return;
		
		
		$rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
		if(empty($rsCustomer[0]['billingemail'])) die;
		
	    if (isset($_SESSION[$this->loginAdminSession]['id']) && !empty( $_SESSION[$this->loginAdminSession]['id']))
           $userkey =  base64_decode($_SESSION[$this->loginAdminSession]['id']);
        else
            die;
		
		$token = $this->getUserOTP($userkey);
		
		$url = 'https://'.DOMAIN_NAME.'/admin/pdf/salesOrder/'.$pkey.'/'.$userkey.'/'.$token;  

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); // optional if HTTPS cert issues
        curl_setopt($ch, CURLOPT_HEADER, false);
        
        $result = curl_exec($ch);
        curl_close($ch);
		 
		$destinationPath = UPLOAD_TEMP_DOC.'pdf/';
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);

		$filename = preg_replace('/[^A-Za-z0-9\-\_]/', '_', $rs[0]['code']);
		
		$storeLocation = $destinationPath.$filename.'.pdf' ;
		
		file_put_contents($storeLocation, $result);
		 
        
		// send mail attachment
		$arrAttachment = array();
	 	array_push($arrAttachment, $storeLocation);
		 
		$arrBillingEmail = explode(',',$rsCustomer[0]['billingemail']);
		
		//testing
//		$arrBillingEmail = array('martinhalimk@gmail.com');
		
		$toName = $rsCustomer[0]['name'];
		$toEmail = trim(array_shift($arrBillingEmail)); // ambil yg pertama saja. sisanya masukin CC
  
		$arrCC = (count($arrBillingEmail) > 0)  ? $arrBillingEmail : array();
		
		$title = 'Invoice Layanan';
		$content = 'Dear '.$rsCustomer[0]['name'].',<br><br> 
Sebelumnya kami sampaikan terima kasih atas kepercayaan Anda dalam menggunakan jasa dan layanan kami. <br>
Berikut kami lampirkan invoice untuk periode selanjutnya.<br><br>
Terima Kasih.
';
		
		$arrOpt = array('attachment' => $arrAttachment);
		if(!empty($arrCC))  $arrOpt['cc'] =  $arrCC;
		
		// utk archive
		$arrOpt['bcc'] = array('martinhalimk@gmail.com');
		
		$this->sendMail(array('name' => 'Wintera', 'email' => 'billing@wintera.co.id' ),
						$title,
						$content,
						array('email' => $toEmail,'name' => $toName),
						$arrOpt
					   );
 
	}

   
    function sendInvoice($pkey){
        
        try{  
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);

                    $arrayToJs = array(); 

                    $rs = $this->getDataRowById($pkey);
                    if(empty($rs)){ 
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['901']);  
                        return $arrayToJs;
                    } 

                    $invoice =  $this->generateInvoice($pkey); 

                    $this->sendMail(array(),$this->lang['invoice'] . ' '. $rs[0]['code'],$invoice,array('email'=>$rs[0]['recipientemail']));

                    /*$invoiceArchiveEmail = $this->loadSetting('invoiceArchiveEmail');
                    if (!empty($invoiceArchiveEmail))
                       $this->sendMail(array(),$this->lang['invoice'] . ' '.  $rs[0]['code'],$email,array('email'=>$invoiceArchiveEmail));*/

                    $sql = 'update ' .$this->tableName.' set invoicesent = invoicesent + 1 where pkey = ' . $this->oDbCon->paramString($pkey) ;
                    $this->oDbCon->execute($sql); 

                    $this->oDbCon->endTrans();

                    $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

        }catch (Exception $e){
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs,false,$e->getMessage());    
        }

        return $arrayToJs; 

    } 

    function sendNotificationInvoice($pkey){
    // untuk email ke admin saja, bukan ke user

    try{  
            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

                $arrayToJs = array(); 

                $rs = $this->getDataRowById($pkey);
                if(empty($rs)){ 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['901']);  
                    return $arrayToJs;
                } 

                $invoice =  $this->generateInvoice($pkey);

                $invoiceArchiveEmail = $this->loadSetting('invoiceArchiveEmail');
                $this->sendMail(array(),$this->lang['invoice'] . ' '.  $rs[0]['code'],$invoice,array('email'=>$invoiceArchiveEmail));

                $this->oDbCon->endTrans();

                $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

    }catch (Exception $e){
        $this->oDbCon->rollback();
        $this->addErrorList($arrayToJs,false,$e->getMessage());    
    }

    return $arrayToJs; 

    } 

    function reCountSubtotal($arrParam){

            $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0; 
            $arrParam['pointValue'] = (isset($arrParam['pointValue']) && !empty($arrParam['pointValue'])) ? $this->unFormatNumber($arrParam['pointValue']) : 0; 

            $subtotal = 0 ;
            $grandtotal = 0;

            $arrItemKey = $arrParam['hidItemKey']; 
			$arrVoucherKey = $arrParam['hidVoucherKey']; 
			$arrVoucherType = $arrParam['hidVoucherType']; 
            $taxValue = $this->unFormatNumber($arrParam['taxValue']);  
            $finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
            $finalDiscountType = $arrParam['selFinalDiscountType']; 
             
			$taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);  
            $shipmentFee = $this->unFormatNumber($arrParam['shipmentFee']); 

/*
// nanti dulu
            $commissionFee = $this->unFormatNumber($arrParam['commissionFee']); 
            $serviceFee = $this->unFormatNumber($arrParam['serviceFee']); 
            $affiliateFee = $this->unFormatNumber($arrParam['affiliateFee']); 
            $diffShippingFee = $this->unFormatNumber($arrParam['diffShippingFee']); 
           $freeShippingFee = $this->unFormatNumber($arrParam['freeShippingFee']); 
*/
            $etcCost = $this->unFormatNumber($arrParam['etcCost']); 
            $pointValue =$this->unFormatNumber($arrParam['pointValue']);   

            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit']; 
            $arrDiscountValueInUnit = $arrParam['discountValueInUnit']; 
            $arrDiscountType = $arrParam['selDiscountType']; 
            $arrTransUnitKey = $arrParam['selUnit']; 


            $arrIsPriceInPcs = (isset($arrParam['chkPriceInPcs'])) ? $arrParam['chkPriceInPcs'] : array();
            $arrQtyInPcs = (isset($arrParam['qtyInPcs'])) ? $arrParam['qtyInPcs'] : array(); // gk bisa pake validasi isset karena APi selalu ada
            $arrPriceInPcs = (isset($arrParam['priceInPcs'])) ? $arrParam['priceInPcs'] :  array(); //  gk bisa pake validasi isset karena APi selalu ada
            $arrItemDetail = array();
            $arrVoucherDetail = array();
            $item = new Item();
            $totalProfit = 0;
            $totalGramasi = 0;
			$totalCOGS = 0;
        
            $rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',
                                                 $item->tableName.'.gramasi', 
                                                 $item->tableName.'.itemtype', 
                                                 $item->tableName.'.cogs', 
                                                 $item->tableName.'.weightunitkey', 
                                                 $item->tableName.'.isweightfixed', 
                                                 $item->tableName.'.baseunitkey'),
                                            ' and '. $item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemKey,',').')');
         
            $rsItemCol = array_column($rsItemCol,null,'pkey');

            for ($i=0;$i<count($arrItemKey);$i++){

                if (empty($arrItemKey[$i]))  
                    continue; 

                    //$rsItem = $item->getDataRowById($arrItemKey[$i]);
 
                    $itemkey = $arrItemKey[$i];
				    $rsItem = $rsItemCol[$itemkey];
                
                    $transactionUnitKey = $arrTransUnitKey[$i];
                    $baseunitkey = $rsItem['baseunitkey']; 
                    $qty =  $this->unFormatNumber($arrQty[$i]);
                    $conversionMultiplier = $item->getConvMultiplier($itemkey,$transactionUnitKey,$baseunitkey); 

					// buat jasa , sementara
					if($conversionMultiplier == 0) $conversionMultiplier = 1; 
		 
				
                    $qtyinbaseunit = $qty * $conversionMultiplier;
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
                    $discount = $this->unFormatNumber($arrDiscountValueInUnit[$i]);
                    $discountType =  $this->unFormatNumber($arrDiscountType[$i]);
                 
					$discount = $this->getDiscountValue($priceInUnit, $discount,$discountType);
 
                    $gramasi = $rsItem['gramasi'];
                    if ($rsItem['weightunitkey'] == UNIT['kg'])
                        $gramasi *= 1000;
                
                
                    // utk jewelry, harusnya sama dengan gramsi diatas, tp dipisah saja utk jaga2
                    // qtyinpcs adalah qty dalam gramasi, cuma variablenya sudah terlanjur pake "inpcs"
                    // kalo user gramasi gk disi, maka dianggap sama dengan qtyinbaseunit, harusnya masih aman karena sampe skrg baru Hans yg pake gramasi
                    if($rsItem['isweightfixed'])
                        $arrItemDetail[$i]['qtyInPcs'] = ($rsItem['gramasi'] > 0) ? ($rsItem['gramasi']*$qtyinbaseunit) : $qtyinbaseunit;
                

                    // hanya jika ada field QtyInPcs (utk Jewelry biasanya)
                    //if(isset($arrParam['qtyInPcs'])){
                    //if(isset($arrParam['chkPriceInPcs'])){ // gk bisa karena gk semua ad chk harga per pcs
                    if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['jewelry']))){   
                
                        $isPriceInPcs = $arrIsPriceInPcs[$i];
                        $qtyInPcs = $this->unFormatNumber($arrQtyInPcs[$i] ?? 0);
                        $priceInPcs = $this->unFormatNumber($arrPriceInPcs[$i] ?? 0);

                         //$this->setLog('priceInUnitUpdate',true);
                         if($isPriceInPcs == 1) {
                            if ($qty != 0){
                                $priceInBaseUnit = ($qtyInPcs * $priceInPcs) / $qty;
                                $priceInUnit = $priceInBaseUnit;
                            }
                        } else {
                            if ($qtyInPcs != 0) {
                                $priceInPcsValue = ($qty * $priceInUnit) / $qtyInPcs;
                                $priceInPcs = $priceInPcsValue;
                            }
                        }
  
                        $arrItemDetail[$i]['priceInPcs'] = $priceInPcs;
                        $arrItemDetail[$i]['qtyInPcs'] = $qtyInPcs;
                    }


                    $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
                    $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
                    $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit ; 
                    $arrItemDetail[$i]['priceInBaseUnit'] = $priceInUnit / $conversionMultiplier ;
                    $arrItemDetail[$i]['priceInUnit'] = $priceInUnit ; // perlu ad, kalo gk nilainya kereset jd 0 karena di normalize akan diisi ulang
                    $arrItemDetail[$i]['weight'] = $gramasi ; 
   
                    $detailSubtotal = $qty * ($priceInUnit - $discount);
                    $arrItemDetail[$i]['unitDiscountValue'] = $discount;
                    $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal; 
				    $arrItemDetail[$i]['itemType'] = $rsItem['itemtype']; 

                    $subtotal += $detailSubtotal ;  
                    $totalGramasi += ($qty * $gramasi);
            } 

            $grandtotal = $subtotal;

			$finalDiscount = $this->getDiscountValue($grandtotal, $finalDiscount,$finalDiscountType);
			
			if(isset($arrParam['finalDiscount2'])){
				$finalDiscount2 = $this->unFormatNumber($arrParam['finalDiscount2']); 
				$finalDiscountType2 = $arrParam['selFinalDiscountType2'];      
				
				$finalDiscount += $this->getDiscountValue( ($grandtotal-$finalDiscount), $finalDiscount2,$finalDiscountType2);  
			}
            
			// hitung ulang nilai voucher
			$voucherSalesValue = 0;
			$voucherShipmentValue = 0;
        
			$useVoucherPoint = $this->loadSetting('transactionVoucherPoint');
			
			if($useVoucherPoint == 1){  
				$voucherTransaction = new VoucherTransaction();
				for ($i=0;$i<count($arrVoucherKey);$i++){ 
                     
					 $voucherInformation = $voucherTransaction->calculateVoucherValue( array('voucherkey' => $arrVoucherKey[$i], 
                                                                                                                  'vouchertype' => $arrVoucherType[$i], 
                                                                                                                 ),
                                                                                                            array('totalsales' =>  $subtotal - $finalDiscount,
                                                                                                                  'totalshipment' => $shipmentFee
                                                                                                                 )
                                                                                                            ) ; 
         
                    $arrVoucherDetail[$i]['voucherAmount'] = $voucherInformation['amount'];
				 
                    if ($voucherInformation['categorykey'] == VOUCHER_CATEGORY['sales'])
                        $voucherSalesValue += $arrVoucherDetail[$i]['voucherAmount']; 
                    else if ($voucherInformation['categorykey'] == VOUCHER_CATEGORY['shipment'])
                        $voucherShipmentValue += $arrVoucherDetail[$i]['voucherAmount']; 
                 
				}
			}
			
            $totalFinalDiscountAndPointValue = $finalDiscount + $voucherSalesValue + $pointValue ;

            for ($i=0;$i<count($arrItemKey);$i++){

                if (empty($arrItemKey[$i]))  
                    continue;
 
                    $qtyinbaseunit = $arrItemDetail[$i]['qtyInBaseUnit'];
                    $conversionMultiplier = $arrItemDetail[$i]['unitConvMultiplier'];
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]); 

                    $unitDiscountedValue = $priceInUnit - $arrItemDetail[$i]['unitDiscountValue'] ;
                    
                    $discPerSub = (!empty($subtotal)) ? $unitDiscountedValue/$subtotal : 0; 
                    $priceInUnitBeforeTax = $unitDiscountedValue - ($discPerSub * $totalFinalDiscountAndPointValue); 

                    if ($isPriceIncludeTax == true) { 
                            $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $priceInUnitBeforeTax;   
                            $priceInUnitBeforeTax = $priceInUnitBeforeTax - $taxValue ;
                    }

                    //$rsItem = $item->getDataRowById($arrItemKey[$i]);
                
				    $rsItem = $rsItemCol[$arrItemKey[$i]]; 
                    $arrItemDetail[$i]['cogs'] = $rsItem['cogs'];	
                 
                    $arrItemDetail[$i]['profit'] = ($priceInUnitBeforeTax / $conversionMultiplier) - $rsItem['cogs'];
                    $arrItemDetail[$i]['headerDiscValueInUnit'] = $discPerSub * $totalFinalDiscountAndPointValue; // per item
                 //
                 //   $this->setLog($priceInUnitBeforeTax,true);
                 //   $this->setLog($totalFinalDiscountAndPointValue,true);
                 //   $this->setLog($arrItemDetail[$i]['profit'],true);
                 //   $this->setLog('>>>>>>>>>>',true);
                        
                    $totalProfit += ($qtyinbaseunit * $arrItemDetail[$i]['profit']); 
					$totalCOGS += $qtyinbaseunit * $arrItemDetail[$i]['cogs'];
            }
        
        
            $totalProfit -= $totalFinalDiscountAndPointValue;
            $beforeTaxTotal = $subtotal - $totalFinalDiscountAndPointValue;
            $grandtotal = $beforeTaxTotal;

            if ($isPriceIncludeTax == false) {
                    $taxValue = $beforeTaxTotal * $taxPercentage / 100;
                    $taxValue = round($taxValue); // kalo ad koma, nilainya gantung di AR nanti
                    $grandtotal += $taxValue;
            }else{
                    $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $grandtotal;   
                    $beforeTaxTotal = $grandtotal - $taxValue ;
            }

	   // $grandtotal +=  $shipmentFee - $voucherShipmentValue  - $commissionFee - $serviceFee - $affiliateFee - $diffShippingFee - $freeShippingFee  + $etcCost;

            $grandtotal +=  $shipmentFee - $voucherShipmentValue + $etcCost;


            $balance = 0;
            $totalPayment = 0; 

            $termOfPayment = new TermOfPayment();
            $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
            if ($rsTOP[0]['duedays'] == 0){ 
                $payment = $arrParam['paymentMethodValue'] ?? [];
                for($i=0;$i<count($payment);$i++){
                    $totalPayment += $this->unFormatNumber($payment[$i]);
                } 
            }


            $balance = $totalPayment - $grandtotal; 

            $reCountResult = array();
            $reCountResult['subtotal'] = $subtotal;
            $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
            $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
            $reCountResult['taxValue'] = $taxValue;
            $reCountResult['grandtotal'] = $grandtotal;
            $reCountResult['totalPayment'] = $totalPayment;
            $reCountResult['balance'] = $balance;
            $reCountResult['totalCOGS'] = $totalCOGS;
            $reCountResult['profit'] = $totalProfit;
            $reCountResult['detailCOGS'] = $arrItemDetail;
            $reCountResult['detailVoucher'] = $arrVoucherDetail;
            $reCountResult['totalWeight'] = ceil($totalGramasi);

            return $reCountResult;

    } 
   

    function validateForm($arr,$pkey = ''){
            $item = new Item();    
            $packagingCode = new PackagingCode();

		
            $arrayToJs = parent::validateForm($arr,$pkey); 
          
			$useVoucherPoint = $this->loadSetting('transactionVoucherPoint');
		
            $customerkey = $arr['hidCustomerKey'];  
            $arrItemkey = $arr['hidItemKey']; 
            $arrVoucherKey = $arr['hidVoucherKey']; 
            $arrVoucherType = $arr['hidVoucherType']; 
            $arrQty = $arr['qty']; 
            $arrPriceinunit = $arr['priceInUnit'];
            $email = $arr['recipientEmail'];
            $arrSelUnit = $arr['selUnit']; 


            $arrQtyInPcs = $arr['qtyInPcs'];
            $arrPriceInPcs = $arr['priceInPcs'];
            
            $arrSerialNumber = $arr['serialNumber'];
            $isFullDeliver = $arr['chkIsFullDeliver'];
            
            // kalo dr marketplace dan kalo add
            if (!empty($arr['marketplaceKey'])){
                if (empty($pkey)){ 
                    $rs = $this->searchData($this->tableName.'.refcode',$arr['refCode'], true, ' and '.$this->tableName.'.marketplacekey = ' .  $this->oDbCon->paramString($arr['marketplaceKey']). ' and '. $this->tableName.'.statuskey <> 4');
                    if(!empty($rs))
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['marketplace'][3]); 
                }
            }    

            if (PLAN_TYPE['maxsalesorder'] >= 0){ 
                $month = str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','m'));
                $year = str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','Y'));

                $sql = 'select
                            count(pkey) as total 
                        from 
                            ' .$this->tableName.'
                        where 
                            month(trdate) = '.$this->oDbCon->paramString($month).' and year(trdate) = '. $this->oDbCon->paramString($year);

                if (!empty($pkey))
                    $sql .= ' and pkey <> ' . $pkey;

                $rs = $this->oDbCon->doQuery($sql);

                if($rs[0]['total'] >= PLAN_TYPE['maxsalesorder'])   
                  $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][1]);   
            }


            //validasi kalo status gk menunggu gk bisa edit 
            if (!empty($pkey)){
                $rs = $this->getDataRowById($pkey);
                if ($rs[0]['statuskey'] <> 1){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
                }
            }  

            if(empty($customerkey)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
            }


            if(!empty($email)){
                if(!filter_var($email, FILTER_VALIDATE_EMAIL)) 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]); 
            }  

            if(empty($arrItemkey)) 
                 $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);  


            $arrDetailKeys = array(); 
            $arrGroupPackaging = array();

            for($i=0;$i<count($arrItemkey);$i++) { 
                if (empty($arrItemkey[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['item'][1]); 	
                } 

				$rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name',$item->tableName.'.itemtype',$item->tableName.'.needsn'),
												 ' and ' . $item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemkey,',').')');
				$rsItemCol = array_column($rsItemCol,null,'pkey');
				
                if (!empty($arrItemkey[$i])){
//                    $rsItem = $item->getDataRowById($arrItemkey[$i]);
					$rsItem = $rsItemCol[$arrItemkey[$i]];
                    if ($this->unFormatNumber($arrQty[$i]) <= 0){ 
                        $this->addErrorList($arrayToJs,false,$rsItem['name']. '. ' . $this->errorMsg[510]);  
                    }

                    $priceMandatory = $this->loadSetting('priceMandatory');
                    if ($priceMandatory == 1 && $this->unFormatNumber($arrPriceinunit[$i]) <= 0){  
                        $this->addErrorList($arrayToJs,false,$rsItem['name']. '. ' . $this->errorMsg[511]);  
                    }  

                    // cek punya konversi unit utk satuan yg dipilih gk  
					// khusus jenis item
					if($rsItem['itemtype'] != SERVICE){
						$conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
						if (empty($conv)){
	//                        $rsItem = $item->getDataRowById($arrItemkey[$i]);
							$this->addErrorList($arrayToJs,false,$rsItem['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); 
						} 
					}
                     
                    // validasi jewelry biasanya
                    // utk ini nanti dicek lg, kenapa kalo edit dari API harus di kirim gk bisa hitung otomatis saja
                    // kalo dari API selalu ke set seperti nya
                    // sementara set khusus jewelry saja dulu
                    //if(isset($arr['qtyInPcs'])) {
                    if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['jewelry']))){  
                        //$this->setLog("validasi jewelry",true);
                        if($this->unFormatNumber($arrQtyInPcs[$i]) <= 0 ) {
                            $rsItem = $item->getDataRowById($arrItemkey[$i]);
                            $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[510] . ' (PCS)'); 
                        }

                        if($this->unFormatNumber($arrPriceInPcs[$i]) <= 0) {
                            $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[511] . ' (PCS)'); 
                        }
                    }
                    
                    // kalo ad package
                    // kalo dari API skip dulu
                    if(!isset($arr['_mnv-api'])){ 
                        if(isset($arr['selPackagingCode'])) {
    
                            $selPackagingCode = $arr['selPackagingCode'][$i];
    
                            $qtyInBaseUnit = $this->unFormatNumber($arrQty[$i]);
                            $qtyInPcs = $this->unFormatNumber($arrQtyInPcs[$i]);
    
                            if(empty($selPackagingCode)) {
                                $this->addErrorList($arrayToJs,false,'<strong>'.$rsItem[0]['name'].'. </strong>' . $this->errorMsg['salesOrder'][6]);
                            } else {
                              
                                $rsPackagingCode = $packagingCode->getAvailablePackagingCodeByItem($arrItemkey[$i]);
                                if(empty($rsPackagingCode)) {
                                    $this->addErrorList($arrayToJs,false,'<strong>'.$rsItem[0]['name'].'. </strong>' . $this->errorMsg['salesOrder'][7]); 
                                } 
                                
                                //cek apakah qty melebihi total di packaging atau tidak
                                
                                if(!isset($arrGroupPackaging[$selPackagingCode])) {
                                    $arrGroupPackaging[$selPackagingCode] = [
                                        'totalQtyInBaseUnit' => 0,
                                        'totalQtyInPcs'      => 0
                                    ];
                                }
    
                                $arrGroupPackaging[$selPackagingCode]['totalQtyInBaseUnit'] += $qtyInBaseUnit;
                                $arrGroupPackaging[$selPackagingCode]['totalQtyInPcs']      += $qtyInPcs;
                            }
    
                        }
                    }

                    if($rsItem['needsn'] == 1 && $isFullDeliver == 1) {
 
                            if(empty($arrSerialNumber[$i])) {
                                $this->addErrorList($arrayToJs,false,'<strong>'.$rsItem['name']. '.</strong> '. $this->errorMsg['serialnumber'][1]); 
                            }
                            // validasi jumlah serial number dan jumlah unit
                            // harus cek satu2 ad yg kosong gk, karena ad bug, kalo cuma 1 pcs, gk diisi SNnya, dianggap ada, karena indexnya ada, tp valuenya kosong
                            //$qtySN = count($arr['serialNumber'][$i]); 
                            $qtySN = count(array_filter($arr['serialNumber'][$i]));
                          
                            if ($this->unFormatNumber($arrQty[$i]) <> $qtySN){
                                $this->addErrorList($arrayToJs,false,$rsItem['name']. '. ' . $this->errorMsg['serialnumber'][2]); 
                            } 
                     

                    }                
                }

                if(!empty($arrGroupPackaging)) {
                    foreach($arrGroupPackaging as $key => $row) {

                        $rsPakcagingCode = $packagingCode->getDataRowById($key);
                
                        $outstanding = $rsPakcagingCode[0]['qtyinbaseunit'];
                        $outstandingInPcs = $rsPakcagingCode[0]['qtyinpcs'];
                      
                        if($row['totalQtyInBaseUnit'] > $outstanding ) {
                           $this->addErrorList($arrayToJs,false, '<strong>'.$rsPakcagingCode[0]['code'].'. </strong>'. $this->errorMsg['salesOrder'][8]. '<strong> '.$this->errorMsg[402].', '.$this->formatNumber($outstanding).'</strong>');
                        }
                        
                        //$packagingCode->setLog($row['totalQtyInPcs'] .'>'. $outstandingInPcs,true);
                        
                        if($row['totalQtyInPcs'] > $outstandingInPcs) {
                           $this->addErrorList($arrayToJs,false, '<strong>'.$rsPakcagingCode[0]['code'].'. </strong>'. $this->errorMsg['salesOrder'][9]. '<strong> '.$this->errorMsg[402].', '.$this->formatNumber($outstandingInPcs).'</strong>');
                        }
                    }
                }   


                // cek ada detail double gk  

                // dinonaktifkan, karena beberapa user 1 hari dijadikan 1 bon
                
                // sementar akalo dr lazada gk perlu cek, karena lazada pisah setiap item 1 pcs
                /*if (!isset($arr['marketplaceKey']) ||  empty($arr['marketplaceKey'])){
                    if (in_array($arrItemkey[$i],$arrDetailKeys)){   
                        $this->addErrorList($arrayToJs,false, $rsItem['name'].'. '.$this->errorMsg[215]); 	 
                    }else{ 
                        array_push($arrDetailKeys, $arrItemkey[$i]);
                    } 
                }*/

            }

            /* utk handle edit bagian UI frontend */ 
            if (isset($arr['fromFE']) && !empty($arr['fromFE'])){

                if (!IS_DEVELOPMENT){ 
                    // semetnara
                    //$captchaResponse = $arr['g-recaptcha-response'];  
                    //$request = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".$this->loadSetting('reCaptchaSecretKey')."&response=".$captchaResponse);
                    //$captchaResult = json_decode($request);
//
                    //$errorCaptcha= $captchaResult->{'error-codes'};  
                    //
                    //if (empty($captchaResponse)){
                    //    $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                    //} else if(!$captchaResult->{'success'}){
                    //    $this->addErrorList($arrayToJs,false,$this->errorMsg['captcha'][1]);
                    //} 
                }

                 $recipientName =  $arr['recipientName'];
                 $recipientPhone=  $arr['recipientPhone'];
                 $recipientAddress=  $arr['recipientAddress'];
                 $shipmentFee =  $arr['shipmentFee']; 

                if(empty($recipientName)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
                } 

                if(empty($recipientPhone)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['phone'][1]);
                }

                if(empty($email)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][1]);
                }

                if(empty($recipientAddress)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['address'][1]);
                }
               
                // disesuaikan sesuai kebutuhan customer
                //if(empty($shipmentFee)){
                //    $this->addErrorList($arrayToJs,false,$this->errorMsg['shipment'][4]);
                //}

            }
		
			// validasi voucher
		
			// harus bedain, kalo mengecualikan voucher sendiri, tetep harus cek voucher sudah digunakan atau blm..
			$hasVoucher = false;
			foreach($arrVoucherKey as $voucherkey)
				if(!empty($voucherkey)) $hasVoucher = true;
        
			if($useVoucherPoint == 1 && $hasVoucher){ 
				
                // kalo dari admin, yg dikirim semua voucher transaction, sudah punya pkey voucher transaction ( tinggal beda nya voucher transacion itu dr regular atau collectible)
                // kalo dari FE, raw, antara master voucher collectible ata voucher transaction dr regular
                 
            
                // kalo dr FE bisa beda...
                // sementara skrg baru 1
                     
                $voucher = new Voucher();
                $voucherTransaction = new VoucherTransaction();
                				
                $fromFEAndApps = (!empty($arr['fromFE']) || !empty($arr['_mnv-api']) ) ? true : false; 
				
                 if ( $fromFEAndApps && $arrVoucherType[0] == VOUCHER_TYPE['collectible']  ){
                     
                     // group manual berdasarkan collectible atau regulaar
                      $rsVoucher = $voucher->searchData('','',true,' and '.$voucher->tableName.'.pkey in ('.$this->oDbCon->paramString($arrVoucherKey,',').')');

                      $uniqueRules = $voucher->eligibleForOneTimeUse($customerkey,$arrVoucherKey);
 
                        // buat jaga2 saja struktuer data yg diperlukan tetep sama, kalo siapa tau nanti ad perubahaan di query utama
                      $rsVoucherTransaction = array();
                      foreach($rsVoucher as $voucherRow){ 
                          array_push($rsVoucherTransaction,
                                        array(
                                            'pkey' => $voucherRow['pkey'],
                                            'statuskey' => $voucherRow['statuskey'],
                                            'enddate' =>  $voucherRow['enddate'],
                                            'expdate' =>  $voucherRow['enddate'],
                                            'qtyused' =>  $voucherRow['qtyused'],
                                            'qty' =>  $voucherRow['qty'],
                                            'categorykey' => $voucherRow['categorykey'],
                                            'minamount' => $voucherRow['minamount'],
                                            'customerkey' => 0
                                        ) 
                                    );
                      }
                      
                 }else{
                     // kalo dari admin  tipe voucherny semua pasti voucher transaction, tinggal beda nya voucher transacion itu dr regular atau collectible 
                     
                     $rsVoucherTransaction = $voucherTransaction->searchData('','',true,' and '.$voucherTransaction->tableName.'.pkey in ('.$this->oDbCon->paramString($arrVoucherKey,',').')  
                                                                        and '.$voucherTransaction->tableName.'.reftranskey in (0, '.$this->oDbCon->paramString($pkey).')
                                                                     ');
  
                      $uniqueRules = $voucherTransaction->eligibleForOneTimeUse($customerkey,$arrVoucherKey);

                 }
                 
                
                  $totalVoucher = count($arrVoucherKey); 
                  for($i=0;$i<$totalVoucher;$i++) { 
                        $voucherkey = $arrVoucherKey[$i];
                        if(!isset($uniqueRules[$voucherkey]))
                            $this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][4]); 	 
                  }

                // baru mulai cek dari collection
                $voucherByTypeCol = $this->reindexDetailCollections($rsVoucherTransaction,'typekey'); 
                
                	
				$minLevel = $this->loadSetting('minMembershipLevelToUseVoucher');
				$customer = new Customer();
				$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.membershiplevel'),
													   ' and '.$customer->tableName.'.pkey = ' . $this->oDbCon->paramString($customerkey));
 
                foreach($voucherByTypeCol as $typekey => $typeRow){
                    
                    $voucherCol = array_column($typeRow ,null,'pkey'); 
                    
                    for($i=0;$i<$totalVoucher;$i++) { 
                        $voucherkey = $arrVoucherKey[$i];
                        $vouchertype = $arrVoucherType[$i];
 
                        // ad kemungkinan pkey sama, tp beda tipe... collectible dan reguler, karena 2 table yg berbeda
                        
                        // pastikan $vouchertype cuma antara 1 dan 2
                        if(!in_array($vouchertype,array(1,2))) $vouchertype = 1;
                         
                        if($vouchertype <> $typekey) continue; // nanti dicek di loop tipenya
                        
                        if (!isset($voucherCol[$voucherkey])){ 
                            $this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][6]); 	
                        }else{ 
                            $rsVoucher = $voucherCol[$voucherkey];

                            if( $voucherkey <> $rsVoucher['pkey']  && $rsVoucher['statuskey'] <> 2)
                                $this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][3]); 	 

                            // validsai quota
                            if($typekey == VOUCHER_TYPE['collectible']) {
                                // buat dibawah
                                $rsVoucher['expdate'] = $rsVoucher['enddate'];
                                
                                // cek quota
                                if($rsVoucher['qtyused'] > $rsVoucher['qty'])
                                    $this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][4]);  
                            }
                            
                             // validasi min amount sales
                             $amount = ( $rsVoucher['categorykey'] == VOUCHER_CATEGORY['shipment']) ?  $arr['shipmentFee'] : $arr['beforeTaxTotal'] ;
                             if($rsVoucher['minamount'] > 0 &&  $amount < $rsVoucher['minamount'])
                                $this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][9]); 	
                             
                            
                            // khusus jenis regular saja
                            if($typekey == VOUCHER_TYPE['regular'] && $rsVoucher['customerkey'] <> $customerkey)
                                $this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][5]); 	 
                            
                            // voucher blm epxired
                            $expDate = $this->formatDBDate($rsVoucher['expdate'],'d / m / Y'); 
                            $transDate = $arr['trDate']; 
                            $dateDiff = $this->dateDiff($expDate,$transDate);
                            if($dateDiff > 0)
                                $this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][8]); 	 
 

                            // keanggotaan sudah boleh menggunakan voucher
                            // untu kenari hanya utk tipe voucher yg beli, baru ad vaalidasi level
                            if($typekey != VOUCHER_TYPE['collectible'] && $rsCustomer[0]['membershiplevel'] < $minLevel)
                                $this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][7]);

                        }   
                    } 
                }
                  
			
				
			}
			
            //$this->addErrorList($arrayToJs,false, 'sementara '); 	 
            return $arrayToJs;
    }
 

    function validateConfirm($rsHeader){
		
        $id = $rsHeader[0]['pkey'];
        $rewardsPoint = new RewardsPoint();
        $warehouse = new Warehouse();  
        $coaLink = new COALink();
        $item = new Item();
 
		$useVoucherPoint = $this->loadSetting('transactionVoucherPoint');
		$salesBelowCOGS = $this->loadSetting('salesBelowCOGS');
		$salesBelowCOGS = ($salesBelowCOGS == 2) ? false : true; 
			
        $rsDetail = $this->getDetailById($id);
        $rsPayment = $this->getPaymentMethodDetail($id); 
		$rsItem = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name',$item->tableName.'.cogs'), 
									   ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString( array_column($rsDetail,'itemkey') ,',').')');
		$rsItem = array_column($rsItem,null,'pkey');
		
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;  
 
		$customerkey = $rsHeader[0]['customerkey'];
		
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $balance = $totalPayment - $rsHeader[0]['grandtotal'];   
 
        if ($isCash){ 
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }else{
              
            //validasi creditlimit
            // masi harus ditambahkan dengan JO yg masih menggantung
            $customer = new Customer(); 
            $customerkey = $rsHeader[0]['customerkey'];
            $rsCustomer = $customer->getDataRowById($customerkey);
            
            if ($rsCustomer[0]['creditlimit'] > 0){  
                $total = $this->unFormatNumber($rsHeader[0]['grandtotal']);
                if ($customer->willExceedCreditLimit($customerkey,$total)){
                     $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['creditlimit'][1]);
                }
            } 

            
        }
 
        // validasi point
        $point = $rsHeader[0]['pointvalue'];
        $currentPoint = $rewardsPoint->getSumTotalRewards($rsHeader[0]['customerkey']) * $this->loadSetting('rewardsPointUnitValue');
        if ($point > $currentPoint)
            $this->addErrorLog(false,$this->errorMsg['point'][3]);

        if (USE_GL){
                $arrCOA = array();
                array_push($arrCOA, 'salesretail' , 'taxout', 'otherrevenue', 'hpp' , 'inventory' , 'inventorytemp', 'salesretaildiscount'); 
                for ($i=0;$i<count($arrCOA);$i++){
                    $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                    if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
                }   

                if ($isCash){
                    for($i=0;$i<count($rsPayment); $i++){ 
                        if ($rsPayment[$i]['amount'] > 0 ){ 
                            $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                            if (empty($rsCOA))	
                                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                        }
                    } 
                }else{ 
                        // validasi COA piutang  
                        $rsCOA = $coaLink->getCOALink ('ar', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                        if (empty($rsCOA))	
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                }
 
         } 
             
        //validasi stock, khusus penjualan tetep harus validasi
		// gk perlu, tp resikonya jurnalny akan salah
		
        $negativeQOH = $this->loadSetting('negativeQOH');
		// validasi hanya jika stok tdk boleh negative
		// 0/2 : tdk boleh negative
		// 1 : boleh negative
        if($negativeQOH != 1){
			$itemMovement = new itemMovement();
			for($i=0;$i<count($rsDetail);$i++){

				if(empty($rsDetail[$i]['itemkey']) || $rsDetail[$i]['itemtype'] == SERVICE) continue;

				 $saldoakhir = $itemMovement->getItemQOH($rsDetail[$i]['itemkey'], $rsHeader[0]['warehousekey']);  
				 $totalqty = $saldoakhir - $rsDetail[$i]['qtyinbaseunit'];  

				if($totalqty<0){
					//$item = new Item();
					//$rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']); 
					$arrItem = $rsItem[$rsDetail[$i]['itemkey']];
					$this->addErrorLog(false,'<strong>'.$arrItem['name'].'</strong>. '.$this->errorMsg[402]);
				}


			} 
        }
        
        // validasi di marketplace, ordernya masih aktif gk
        // nanti saja ditambahkan, harusnya kejadian hanya jika transaksinya lama diproses
        if(!empty($rsHeader[0]['marketplacekey'])){
            
        }
		 
		
		//validasi harga jual 
		if(!$salesBelowCOGS){
			for($i=0;$i<count($rsDetail);$i++){
				$arrItem = $rsItem[$rsDetail[$i]['itemkey']];
				$itemUnitPrice = $rsDetail[$i]['total'] /  $rsDetail[$i]['qtyinbaseunit']; // biar kehitung jg discuntnya
				if ($itemUnitPrice < $arrItem['cogs']) 
					$this->addErrorLog(false,'<strong>'.$arrItem['name'].'</strong>. '.$this->errorMsg['salesOrder'][4]);
			}
		}
		
		// validasi voucher
		if($useVoucherPoint == 1){
             
            $voucherTransaction = new VoucherTransaction();
            $rsVoucher =  $this->getVoucherDetail($id); 
            $arrVoucherKey = array_column($rsVoucher,'voucherkey'); 
            $totalVoucher = count($arrVoucherKey);
		
            if($totalVoucher > 0){
			 
                    $voucherTransaction = new VoucherTransaction();
                    $rsVoucherTransaction = $voucherTransaction->searchDataRow(array($voucherTransaction->tableName.'.pkey', $voucherTransaction->tableName.'.customerkey', $voucherTransaction->tableName.'.expdate'),
                                                                              ' and '.$voucherTransaction->tableName.'.pkey in ('.$this->oDbCon->paramString($arrVoucherKey,',').')  
                                                                                and '.$voucherTransaction->tableName.'.reftranskey in (0, '.$this->oDbCon->paramString($id).')
                                                                            ');

                    $rsVoucherTransaction = array_column($rsVoucherTransaction,null,'pkey');

                    // voucher harus punya customer yg sama 
                    for($i=0;$i<$totalVoucher;$i++) { 

                        if (!isset($rsVoucherTransaction[$arrVoucherKey[$i]])){ 
                            $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][6]); 	
                        }else{ 
                            $rsVoucher = $rsVoucherTransaction[$arrVoucherKey[$i]];

                            //$this->setLog('validasi per penjualan atau pengiriman',true);
                            //gk perlu sepertinya validasi penjualan / pengirman

                            if( $arrVoucherKey[$i] <> $rsVoucher['pkey']  && $rsVoucher['statuskey'] <> 2)
                                $this->addErrorLog(false, $this->errorMsg['voucher'][3]); 	 

                            if($rsVoucher['customerkey'] <> $customerkey)
                                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][5]); 	 

                            // voucher blm epxired
                            $expDate = $this->formatDBDate($rsVoucher['expdate'],'d / m / Y'); 
                            $transDate = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                            $dateDiff = $this->dateDiff($expDate,$transDate);
                            if($dateDiff > 0)
                                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][8]); 	 

                        }  

                    }


                    $minLevel = $this->loadSetting('minMembershipLevelToUseVoucher');
                    $customer = new Customer();
                    $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.membershiplevel'),
                                                           ' and '.$customer->tableName.'.pkey = ' . $this->oDbCon->paramString($customerkey));

                    // keanggotaan sudah boleh menggunakan voucher
                    // nanti harus cek utk jenis tipe voucherny jg
//                    if($rsCustomer[0]['membershiplevel'] < $minLevel)
//                        $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][7]);

            }

        }
 
        if($rsHeader[0]['isfulldeliver'] == 1) {

            $arrErrMsg = array();

            $arrItemKey = array_column($rsDetail,'itemkey');
            $rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name',$item->tableName.'.needsn'),
												 ' and ' . $item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemKey,',').')');
			$rsItemCol = array_column($rsItemCol,null,'pkey');
           
            for($j=0; $j<count($rsDetail); $j++) {
                $rsItem = $rsItemCol[$rsDetail[$j]['itemkey']]; 
                if($rsItem['needsn'] != 1)   continue; 

                // harusnya gk mungkin terjadi
                $rsSNDetail = $this->getSerialNumber($rsDetail[$j]['pkey']);
                if(empty($rsSNDetail))  continue; 
                
                foreach($rsSNDetail as $snRow) {
                    $rsSNItem = $item->searchSerialNumber($rsDetail[$j]['itemkey'],'', $snRow['serialnumber'], $rsHeader[0]['warehousekey']);
                    if(empty($rsSNItem)) {
                        $errorMessage = '<b>'.$rsItem['name'] . ' - ' . $snRow['serialnumber'] . '</b>. ' . $this->errorMsg['serialnumber'][4];
                        array_push($arrErrMsg, $errorMessage);
                    }

                }

            }
            
            if(!empty($arrErrMsg)) {
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'. </strong>'. $this->errorMsg[201] . '<br>' . implode('<br>', $arrErrMsg));
            }

        }

    }
  
    function updatePaidStatus($pkey,$paidStatus){
 
        $sql = 'update '.$this->tableName.' set paidstatuskey = ' . $this->oDbCon->paramString($paidStatus) .', paidon = now() where pkey = ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);
        
    }

    function confirmTrans($rsHeader){  
        
        $id = $rsHeader[0]['pkey']; 
        $sql = 'update '.$this->tableName.' set paidstatuskey = ' . $this->oDbCon->paramString($paidStatus) .' where pkey = ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);
        $this->updateCostAndProfit($id);

        $item = new Item();
        $customer = new Customer();
        $coaLink = new COALink();
        
        $rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);
        
        $noteCode = $rsHeader[0]['code'].'.';
        if (!empty($rsHeader[0]['refcode']))
            $noteCode .= ' ' .$rsHeader[0]['refcode'].'.';
        
        $note  = $noteCode . ' Jual ke '.$rsCustomer[0]['name'].'.';
            
        $warehouse = new Warehouse();
        $rsWarehouse = $warehouse->getDataRowById($rsHeader[0]['warehousekey']); 
        $notecash =  $noteCode . ' Kas Masuk dari '.$rsWarehouse[0]['name'].' untuk penjualan barang dari '.$rsCustomer[0]['name'].'.';
        
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);

        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
 
		$refTableKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
			
        // MENGHITUNG PAYMENT
        if ($isCash){
            $rsPayment = $this->getPaymentMethodDetail($id);   
            //$cashMovement = new CashMovement();  

            for($i=0;$i<count($rsPayment); $i++){  
                  if (USE_GL) {
                       $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
				       $coakey = $rsPaymentCOA[0]['coakey']; 
				   }else{
				       $coakey = $rsPayment[$i]['paymentkey'];
				   } 
 
                 //$cashMovement->updateCashMovement($id, $coakey,$rsPayment[$i]['amount'],$this->tableName, $rsHeader[0]['warehousekey'], $notecash,$rsHeader[0]['trdate']);
            } 
            
            $this->updatePaidStatus($id,3);
            
        }else{ 
			 
			if($this->isActiveModule('AR')){
				//update AR
            	$ar = new AR();

				$arrParam = array();	

				$arrParam['code'] = 'xxxxxx';
				$arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
				$arrParam['hidSalesKey'] = $rsHeader[0]['saleskey'];
				$arrParam['hidRefKey'] = $id;
				$arrParam['hidRefHeaderKey'] = $id;
				$arrParam['hidRefCode'] =  $rsHeader[0]['code']; 
				$arrParam['hidRefCode2'] =  $rsHeader[0]['refcode'];  
				$arrParam['hidRefTable'] = $refTableKey;
				$arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
				$arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
				$arrParam['vaNumber'] = $rsHeader[0]['vanumber']; 
				$arrParam['selARType'] = 1; 
				$arrParam['amount'] = abs($rsHeader[0]['grandtotal']);
				$arrParam['trDesc'] = '';
				$arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
				$date = new DateTime($rsHeader[0]['trdate']);
				$date->add(new DateInterval('P'.$rsTOP[0]['duedays'].'D'));
				$arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
				$arrParam['createdBy'] = 0;
				$arrParam['islinked'] = 1; 
				$arrParam['overwriteGL'] = 1;

				$arrayToJs = $ar->addData($arrParam); 
				
				if (!$arrayToJs[0]['valid'])
					throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    
			}
            
        }
        // END            


        if ($rsHeader[0]['isfulldeliver']){
            $itemMovement = new ItemMovement();   
             
            $arrItemkey = array_column($rsDetail,'itemkey');
            $arrItem = $item->searchDataRow( array($item->tableName.'.pkey',$item->tableName.'.code',$item->tableName.'.name',$item->tableName.'.needsn'),
                                                ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemkey,',').')'
                                          );
            $arrItem = array_column($arrItem,null,'pkey');

            $rsSNCol = $this->getSerialNumber(array_column($rsDetail,'pkey')); 
            $rsSNCol = $this->reindexDetailCollections($rsSNCol,'refkey'); 
            for($i=0;$i<count($rsDetail); $i++){	 

                $rsItem = $arrItem[$rsDetail[$i]['itemkey']];	
               if(empty($rsDetail[$i]['itemkey']) || $rsDetail[$i]['itemtype']==SERVICE)  continue;
                  
                //SN             
                if($rsItem['needsn'] == 1){

                    $rsSN = $rsSNCol[$rsDetail[$i]['pkey']];
                    $rsTableKey = $this->getTableKeyAndObj($this->tableName,array('key'));
                      
                    for($j=0;$j<count($rsSN); $j++) {
                       
                        // cogs per SN  
                        $snCOGS = $rsSN[$j]['costinbaseunit'];
                        //$this->setLog($snCOGS,true);
                             
                        $itemMovement->updateItemMovement(array('refkey' => $id, 'refdetailkey' => $rsDetail[$i]['pkey']),$rsDetail[$i]['itemkey'],array('qtyinbaseunit' => -1, 'qtyinpcs' => -1), $snCOGS ,$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate']);
                        $itemMovement->updateItemSNMovement( 
                            array(
                                    'refkey' => $rsDetail[$i]['pkey'],
                                    'refheaderkey' => $id,
                                    'itemkey' => $rsDetail[$i]['itemkey'],
                                    'sn' => $rsSN[$j]['serialnumber'],
                                    'qtyinbaseunit' => -1,
                                    'costinbaseunit' => $snCOGS,
                                    'warehousekey' => $rsHeader[0]['warehousekey'],
                                    'note' => $note,
                                    'trdate' => $rsHeader[0]['trdate'] ,
                                    'reftabletype' => $rsTableKey['key'] 
                            )); 
                        }

                } else {
                    $itemMovement->updateItemMovement(array('refkey' => $id, 'refdetailkey' => $rsDetail[$i]['pkey']),$rsDetail[$i]['itemkey'],array('qtyinbaseunit' => -$rsDetail[$i]['qtyinbaseunit'], 'qtyinpcs' => -$rsDetail[$i]['qtyinpcs']), $rsDetail[$i]['costinbaseunit'] ,$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate']);
                }
            
            }
        }
 
        
        // potong point jika digunakan
        if ($rsHeader[0]['pointvalue'] > 0){ 
//            $totalPoint = $rsHeader[0]['pointvalue'] / $this->loadSetting('rewardsPointUnitValue'); 
//
//            $rewardsPoint = new RewardsPoint();
//            $arr = array();
//            $arr['pkey']  = $rewardsPoint->getNextKey($rewardsPoint->tableName);
//            $arr['code'] = 'xxxxx';
//            $arr['trDate'] =  date('d / m / Y');
//            $arr['hidCustomerKey'] = $rsHeader[0]['customerkey'];
//            $arr['hidSalesOrderKey'] = $rsHeader[0]['pkey'];
//            $arr['point'] = $totalPoint*-1;
//            $arr['createdBy'] = 0;
//            $arr['notes'] = 'Redeem'; 
//
//            $rewardsPoint->addData($arr);  
//            $rewardsPoint->changeStatus($arr['pkey'] ,2);
        }

        //update point jika ada
        $pointMultiple = $this->loadSetting('rewardsPointMultiple');
        if ($pointMultiple > 0){ 
            $beforeTaxTotal = $rsHeader[0]['beforetaxtotal']; 
			// harus dipotong nanti dengan point yg digunakan
            $totalPoint = floor($beforeTaxTotal/$pointMultiple); 

            if ($totalPoint > 0){
                $rewardsPoint = new RewardsPoint();
				$rewardsExpiredIn = $this->loadSetting('rewardsExpiredIn');
				
                $arr = array(); 
                $arr['code'] = 'xxxxx';
                $arr['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate']);
                $arr['hidCustomerKey'] = $rsHeader[0]['customerkey'];
                $arr['hidSalesOrderKey'] = $rsHeader[0]['pkey'];
                $arr['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
                $arr['hidRefKey'] = $rsHeader[0]['pkey'] ;
                $arr['hidRefTableType'] = $refTableKey ;
                $arr['point'] = $totalPoint ; 
                $arr['outstanding'] = $totalPoint ;
                $arr['trDesc'] = $rsHeader[0]['code'] ;
                $arr['expDate'] = date('d / m / Y', strtotime($rsHeader[0]['trdate']. ' + '.$rewardsExpiredIn.' months'));
					
                $arr['selStatus'] = 1 ;

                $response = $rewardsPoint->addData($arr);   
                $rewardsPoint->changeStatus($response[0]['data']['pkey'] ,2,'',false,true); 
            }

        }

      	// dipindahkan kedetail dan after update data
//        if(!empty($rsHeader[0]['voucherkey'])){
//            $voucherTransaction = new VoucherTransaction();
//            $rsTableKey = $this->getTableKeyAndObj($rs[0]['tablename'],array('key')); 
//            $voucherTransaction->useVoucher($rsHeader[0]['voucherkey'], $rsHeader[0]['pkey'],$rsTableKey['key']); 
//        }
		 
        //update jurnal umum 
        $this->updateGL($rsHeader);
        
        if($this->isActiveModule('marketplace')){ 
        	$marketplace = new Marketplace();
			$marketplace->onConfirmTrans($id); 
		}
            
        
        // kalo pake Biteship
        // sementara cara cek nya lihat ad isi gk secretkey kurir 
        // nanti perlu dibuat, pilihan pake gateway ap
        $biteShip = new Biteship();
        if(!empty($biteShip->secretkey)){
            $response = $biteShip->placeOrder($id);
            $trackingid = $response['courier']['tracking_id'];
            if(!empty($trackingid))
                $this->updateShipmentTrackingId($id, $trackingid);
        }
    } 

    function updateShipmentTrackingId($id, $trackingid, $isCancel=false){ 
        if ($isCancel)
            $trackingid = '';

        $sql = 'update '. $this->tableName .' set trackingid = '.$this->oDbCon->paramString($trackingid).' where pkey = '.$this->oDbCon->paramString($id);
        
        $this->oDbCon->execute($sql);
    }    function requestPickup($id){ 
        $marketplace = new MarketPlace();
        $marketplace->onRequestPickup($id);
    }

    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey'];
  
		$isActive = $this->isActiveModule(array('AR','SalesDelivery','SalesOrderReturn'));
        //cek apakah sudah ad penerimaan PO
		if($isActive['salesdelivery']){
			if (!$rsHeader[0]['isfulldeliver']) {
				$salesDelivery = new SalesDelivery();
				$rsSalesDelivery = $salesDelivery->searchData('','',true,' and '.$salesDelivery->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$salesDelivery->tableName.'.statuskey in  (2,3) )');

				if (!empty($rsSalesDelivery))
					 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['salesOrder'][2]);
			}
		}

		if($isActive['ar']){
			//cek ad AR terbayar 
			$ar = new AR();
			$rsARKey = $ar->getTableKeyAndObj($this->tableName); 
			$rsAR = $ar->searchData('','',true,' and  '.$ar->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and '.$ar->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$ar->tableName.'.statuskey  in (2,3))');
			if(!empty($rsAR)) 
				$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ar'][2]);
		}
		
        if($isActive['salesorderreturn']) {
            //check jika ada sales order return maka tidak bisa di cancel
            $salesOrderReturn = new SalesOrderReturn();
            $rsSalesOrderReturn = $salesOrderReturn->searchData('','',true,' and '.$salesOrderReturn->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and ('.$salesOrderReturn->tableName.'.statuskey  in (2,3))');

            if(!empty($rsSalesOrderReturn))
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].', '.$rsSalesOrderReturn[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['salesOrder'][5]);
        }
    } 

 
    function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey'];
        
		$isActive = $this->isActiveModule(array('AR','SalesDelivery', 'SalesOrderReturn'));
		
        $paymentConfirmation = new PaymentConfirmation(); 
        $rewardsPoint = new RewardsPoint();

        if ($rsHeader[0]['isfulldeliver']){ 
            $itemMovement = new ItemMovement();  
            $itemMovement->cancelMovement($id,$this->tableName);  
            $itemMovement->cancelSNMovement($id,$this->tableName);
        }

        $rsRewards = $rewardsPoint->searchDataRow(array('pkey'),
												   ' and '.$rewardsPoint->tableName.'.refkey = '.$this->oDbCon->paramString($id)
												 );
        for($i=0;$i<count($rsRewards); $i++)
            $rewardsPoint->changeStatus($rsRewards[$i]['pkey'] ,TRANSACTION_STATUS['batal'],'',false,true);

		// adjust point yg sdh dipake dan kecancel
		
		if($isActive['ar']){
			$ar = new AR();
			$rsARKey = $ar->getTableKeyAndObj($this->tableName); 
			$rsAR = $ar->searchData('','',true,' and  '.$ar->tableName.'.reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and  '.$ar->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$ar->tableName.'.statuskey = 1');
			for($i=0;$i<count($rsAR);$i++) {
				$arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
				if (!$arrayToJs[0]['valid'])
					throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
			}
		}

		if($isActive['salesdelivery']){	
			$salesDelivery = new SalesDelivery();
			$rsSalesDelivery = $salesDelivery->searchData('','',true,' and refkey = '.$this->oDbCon->paramString($id).' and '.$salesDelivery->tableName.'.statuskey = 1');
			for($i=0;$i<count($rsSalesDelivery);$i++) {
				$arrayToJs = $salesDelivery->changeStatus($rsSalesDelivery[$i]['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
				if (!$arrayToJs[0]['valid'])
					throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
			}
		}
        
		if($isActive['salesorderreturn']){
            //cancel sales return jika so di cancel jika sales return status menunggu
			$salesOrderReturn = new SalesOrderReturn();
			$rsSalesOrderReturn = $salesOrderReturn->searchData('','',true,' and refkey = '.$this->oDbCon->paramString($id).' and '.$salesOrderReturn->tableName.'.statuskey = 1');
			for($i=0;$i<count($rsSalesOrderReturn);$i++) {
				$arrayToJs = $salesOrderReturn->changeStatus($rsSalesOrderReturn[$i]['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
				if (!$arrayToJs[0]['valid'])
					throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
			}
		}
		
        $rsPayment = $paymentConfirmation->searchData($paymentConfirmation->tableName.'.refkey',$id,true);
        for($i=0;$i<count($rsPayment); $i++)
            $paymentConfirmation->changeStatus($rsPayment[$i]['pkey'] ,TRANSACTION_STATUS['batal'],'',false, true,true);

        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);

        // jika di cancel maka tracking id dihapus
        $this->updateShipmentTrackingId($id, $trackingid, true);

    }  
 

    function updateGL($rs){
            if (!USE_GL) return;
        	if ($rs[0]['overwriteGL'] == 1) return;
         

            $warehouse = new Warehouse();
            $coaLink = new COALink();
            $generalJournal = new GeneralJournal();
            $customer = new Customer();
            $item = new Item();
        
            $warehousekey = $rs[0]['warehousekey'];

            $omitSalesDiscountGL = $this->loadSetting('omitSalesDiscountGL');
            
            $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
            $arr = array();
            $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
            $arr['code'] = 'xxxxx';
            $arr['refkey'] = $rs[0]['pkey'];
		    $arr['refTableType'] = $rsKey['key'];
            $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
            $arr['createdBy'] = 0;
		    $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
        

            $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
            $arr['trDesc'] = $this->ucFirst($this->lang['sales']. ' ' .  $this->lang['for']) . ' '. $rsCustomer[0]['name'].'.'; 

            $temp = -1;

            //HPP 
            $totalDisc = 0 ;

            $rsDetail = $this->getDetailById($rs[0]['pkey']);  
            $finalDiscount = $this->getDiscountValue($rs[0]['subtotal'], $rs[0]['finaldiscount'],$rs[0]['finaldiscounttype']);
			
			if($rs[0]['finaldiscount2'] > 0){
			  	$finalDiscount += $this->getDiscountValue( ($rs[0]['subtotal'] - $finalDiscount), $rs[0]['finaldiscount'],$rs[0]['finaldiscounttype']); 
			}
		
            $arrItemSellingCOA = array(); 
            $arrItemCOGSCOA = array(); 
            $totalDetailDiscount = 0;
            $totalHPP = 0;
        
            foreach($rsDetail as $detail){
                
                // COGS
                $itemCOGS = $detail['costinbaseunit'] * $detail['qtyinbaseunit'];
                $totalHPP += $itemCOGS;
                 
                if($rs[0]['isfulldeliver'])
                    $itemCOAKey = $item->getInventoryCOAKey($detail['itemkey'],$warehousekey);
                else
                    $itemCOAKey = $item->getInventoryTempCOAKey($detail['itemkey'],$warehousekey); 
                
                $arrItemCOGSCOA[$itemCOAKey] = (!isset($arrItemCOGSCOA[$itemCOAKey])) ? $itemCOGS : $arrItemCOGSCOA[$itemCOAKey] + $itemCOGS; 
                
                
                // SELLING
                $totalItemValue = $detail['qtyinbaseunit'] * $detail['priceinbaseunit'];
                $discountValue = ($detail['discount'] != 0 && $detail['discounttype'] == 2) ? $detail['discount']/100 * $detail['priceinunit'] : $detail['discount'];  
                $detailDiscount =  $discountValue * $detail['qty'] ;
                $totalDetailDiscount += $detailDiscount;
                
                if ($rs[0]['ispriceincludetax'] == 1) { 
                    $totalItemValue -= $detailDiscount; 
                    
                    $finalDiscountProportion = ($totalItemValue / $rs[0]['subtotal']) * $finalDiscount; 
                    $totalItemValue -= $finalDiscountProportion;
                    
                    $taxValue  = ($rs[0]['taxpercentage']/(100 + $rs[0]['taxpercentage'])) * $totalItemValue;
                    $totalItemValue -= $taxValue;   
                    
                    $totalItemValue += ($finalDiscountProportion + $detailDiscount ); 
                }
                
                
                $itemCOAKey = $item->getRevenueCOAKey($detail['itemkey'],$warehousekey); 
                $arrItemSellingCOA[$itemCOAKey] = (!isset($arrItemSellingCOA[$itemCOAKey])) ? $totalItemValue : $arrItemSellingCOA[$itemCOAKey] + $totalItemValue; 
                 
            }
        
        
            foreach ($arrItemCOGSCOA as $coakey => $coaValue){  
                $temp++; 
                $arr['hidCOAKey'][$temp] = $coakey;
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] = $coaValue;
            } 
        
            foreach ($arrItemSellingCOA as $coakey => $coaValue){  
                $temp++; 
                $arr['hidCOAKey'][$temp] = $coakey;
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] = $coaValue; 
            }
         
            $rsCOA = $coaLink->getCOALink ('hpp', $warehouse->tableName,$warehousekey, 0);
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = $totalHPP; 
            $arr['credit'][$temp] = 0;   

            // kalo include tax, discount diabaikan, karena sudah include dalam perhitungan...
            //$totalDisc = ($rs[0]['ispriceincludetax'] <> 1) ? $finalDiscount + $rs[0]['pointvalue'] + $totalDetailDiscount : 0;

            $totalDisc =   $finalDiscount + $rs[0]['pointvalue'] + $totalDetailDiscount;
            
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
                    //akun piutang  
                    $temp++;
                    $arr['hidCOAKey'][$temp] = $customer->getARCOAKey($rs[0]['customerkey'],$warehousekey);
                    $arr['debit'][$temp] = $rs[0]['grandtotal']; 
                    $arr['credit'][$temp] = 0;  
            }

            if(!$omitSalesDiscountGL){ 
                $rsCOA = $coaLink->getCOALink ('salesretaildiscount', $warehouse->tableName,$warehousekey, 0);
                $temp++;
                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                $arr['debit'][$temp] = $totalDisc; 
                $arr['credit'][$temp] = 0;  
            }else{
                // potong balik nilai penjualan
                $rsCOA = $coaLink->getCOALink ('salesretail', $warehouse->tableName,$warehousekey, 0);
                $temp++;
                $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                $arr['debit'][$temp] = $totalDisc; 
                $arr['credit'][$temp] = 0;   
            }

/*

// perlu pisah coa gk ?? BOS
            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0);
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = $rs[0]['commissionfee'] + $rs[0]['servicefee'] + $rs[0]['affiliatefee'] + $rs[0]['diffshippingfee'] + $rs[0]['freeshippingfee']; 
             $arr['credit'][$temp] = 0;   
*/
            $rsCOA = $coaLink->getCOALink ('taxout', $warehouse->tableName,$warehousekey, 0);
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] = $rs[0]['taxvalue'];  

            $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0);
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] = $rs[0]['shipmentfee'] + $rs[0]['etccost'];  
  
            $arrayToJs = $generalJournal->addData($arr);

            if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }
    
    
//    function updateUserCartSession(){
//        $userkey = '';
//        if (isset($_SESSION[$this->loginSession]['id']) && !empty( $_SESSION[$this->loginSession]['id']))
//           $userkey =  base64_decode($_SESSION[$this->loginSession]['id']);
//        else
//            return;
//
//
//        $sql = 'select * from '.$this->tableCartTemp.' where refkey = '.$this->oDbCon->paramString($userkey);
//        $rs =  $this->oDbCon->doQuery($sql);
//
//        $_SESSION[$this->loginSession]['cart'] = array(); 
//
//        for($i=0;$i<count($rs);$i++){ 
//            $_SESSION[$this->loginSession]['cart'][$i]['itemkey'] =  $rs[$i]['itemkey'];
//            $_SESSION[$this->loginSession]['cart'][$i]['qty'] = number_format($rs[$i]['qty'],0,'','');
//        }
//        
//    }

    function addToCartSession($arr,$updateTemporaryCart = true){   	 

        for($j=0;$j<count($arr['hiditemkey']);$j++){

                 $qty = $this->unFormatNumber($arr['orderQty'][$j]);
            
                 // kalo ad variant, item variantnya yg diambil
                 $itemkey = (isset($arr['hidItemVariantKey'][$j]) && !empty($arr['hidItemVariantKey'][$j]))  ?  $arr['hidItemVariantKey'][$j] :  $arr['hiditemkey'][$j];
 
                if ($qty <= 0 || !is_numeric($qty))  continue;

                $ctr = isset($_SESSION[$this->loginSession]['cart']) ? count($_SESSION[$this->loginSession]['cart']) : 0;

                //cari apakah ad item yg sama
                $haveSameItem = false;
                for($i=0;$i<$ctr;$i++){ 
                    if ($_SESSION[$this->loginSession]['cart'][$i]['itemkey'] == $itemkey){
                         $_SESSION[$this->loginSession]['cart'][$i]['qty'] += $qty;
                         $haveSameItem = true; 
                     } 
                }

                if(!$haveSameItem){
                    $_SESSION[$this->loginSession]['cart'][$ctr]['itemkey'] = $itemkey;
                    $_SESSION[$this->loginSession]['cart'][$ctr]['qty'] = $qty; 
                }

        }

        if($updateTemporaryCart)   $this->updateTemporaryCart();
        
        return true;

    } 
    
    function getAbandonedCart($userkey){
        // utk API
        $sql = 'select 
                    '.$this->tableCartTemp.'.*, 
                    '.$this->tableItem.'.code as itemcode , 
                    '.$this->tableItem.'.name as itemname, 
                    '.$this->tableItem.'.name as sellingprice, 
                    '.$this->tableItem.'.gramasi as itemweight, 
                    weightunit.name as itemweightunit, 
                    weightunit.code as itemweightunitcode,
                    '.$this->tableItemUnit.'.name as baseunit,
                    '.$this->tableItemUnit.'.code as baseunitcode
               from
                    '.$this->tableCartTemp.' , 
                    '.$this->tableItem.',
                    '.$this->tableItemUnit.' weightunit,
                    '.$this->tableItemUnit.'
               where 
                '.$this->tableCartTemp.'.itemkey =  '.$this->tableItem.'.pkey and
                '.$this->tableItem.'.baseunitkey =  '.$this->tableItemUnit.'.pkey and
                '.$this->tableItem.'.weightunitkey =  weightunit.pkey and
                '.$this->tableCartTemp.'.refkey = '.$this->oDbCon->paramString($userkey); 
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
    
    function retrieveAbandonedCart(){
        $userkey = '';
        if (isset($_SESSION[$this->loginSession]['id']) && !empty( $_SESSION[$this->loginSession]['id']))
           $userkey =  base64_decode($_SESSION[$this->loginSession]['id']);

        if (empty($userkey)) return;
        
        $_SESSION[$this->loginSession]['cart'] = array();
        
        $sql = 'select * from '.$this->tableCartTemp.' where refkey = '.$this->oDbCon->paramString($userkey); 
        $rs = $this->oDbCon->doQuery($sql);
 
        $arrItemKey = array();
        $arrQty = array();
        foreach($rs as $row) {
            array_push($arrItemKey,  $row['itemkey']);
            array_push($arrQty,  $this->formatNumber($row['qty']));
        }
              
        $this->addToCartSession(array('hiditemkey' => $arrItemKey ,'orderQty' => $arrQty),false); 
         
    }
    
    function updateTemporaryCart(){
        $userkey = '';
        if (isset($_SESSION[$this->loginSession]['id']) && !empty( $_SESSION[$this->loginSession]['id']))
           $userkey =  base64_decode($_SESSION[$this->loginSession]['id']);

        if (empty($userkey)) return;
        
        $cartList = $_SESSION[$this->loginSession]['cart'];
         
         try{

            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);
         
            $sql  = 'select * from '.$this->tableCartTemp.' where refkey = '.$this->oDbCon->paramString($userkey); 
            $rs =   $this->oDbCon->doQuery($sql);
            $existingItemKey = array_column($rs,'itemkey');

            if (empty($rs)){
                foreach($cartList as $row){
                    $itemkey = $row['itemkey'];
                    $qty = $this->unformatNumber($row['qty']); 
                      
                     $sql  = 'insert '.$this->tableCartTemp.' (`refkey`,`itemkey`,`qty`) 
                     values ('.$this->oDbCon->paramString($userkey).', ' .$this->oDbCon->paramString($itemkey).','.$this->oDbCon->paramString($qty).')';
                    $this->oDbCon->execute($sql); 
                }
            }else{
                
                // update dan delete
                foreach($cartList as $row){
                    $itemkey = $row['itemkey'];
                    $qty = $this->unformatNumber($row['qty']); 
                    
                    if($qty <= 0)
                        $sql = 'delete from '.$this->tableCartTemp.' where refkey = '.$this->oDbCon->paramString($userkey).' and itemkey = '.$this->oDbCon->paramString($itemkey);
                    else{
                        if(in_array($itemkey ,$existingItemKey))
                            $sql = 'update  '.$this->tableCartTemp.' set qty = '.$this->oDbCon->paramString($qty).' where refkey = '.$this->oDbCon->paramString($userkey).' and itemkey = '.$this->oDbCon->paramString($itemkey);
                        else
                            $sql  = 'insert '.$this->tableCartTemp.' (`refkey`,`itemkey`,`qty`) 
                                 values ('.$this->oDbCon->paramString($userkey).', ' .$this->oDbCon->paramString($itemkey).','.$this->oDbCon->paramString($qty).')'; 
                    }
                    
                    //$this->setLog($sql,true);
                    $this->oDbCon->execute($sql);   
                }
                
                
                // hapus yg sudah gk ad di cart 
                $sql  = 'delete from  '.$this->tableCartTemp.'  
                         where refkey = '.$this->oDbCon->paramString($userkey).' 
                         and itemkey not in ('.$this->oDbCon->paramString(array_column($cartList,'itemkey'),',').')';
                
                //$this->setLog($sql,true);
                $this->oDbCon->execute($sql); 
            }

            $this->oDbCon->endTrans();
       
        } catch(Exception $e){
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs,false,$e->getMessage());
        }	
        
    }

//    function addToTemporaryCart($itemkey,$qty){
//        
//
//        $userkey = '';
//        if (isset($_SESSION[$this->loginSession]['id']) && !empty( $_SESSION[$this->loginSession]['id']))
//           $userkey =  base64_decode($_SESSION[$this->loginSession]['id']);
//
//        if (empty($userkey)) return;
//
//         try{
//
//            if(!$this->oDbCon->startTrans())
//                throw new Exception($this->errorMsg[100]);
//         
//            $sql  = 'select * from '.$this->tableCartTemp.'
//                     where refkey = '.$this->oDbCon->paramString($userkey).' 
//                     and itemkey = '.$this->oDbCon->paramString($itemkey);
//
//            $rs =   $this->oDbCon->doQuery($sql);
//
//            if (empty($rs)){
//                 $sql  = 'insert '.$this->tableCartTemp.' 
//                  (`refkey`,`itemkey`,`qty`) values 
//                  ('.$this->oDbCon->paramString($userkey).', '
//                    .$this->oDbCon->paramString($itemkey).','
//                    .$this->oDbCon->paramString($qty).')';
//            }else{
//                $sql  = 'update '.$this->tableCartTemp.' 
//                         set qty = '.$this->oDbCon->paramString($qty).'
//                         where refkey = '.$this->oDbCon->paramString($userkey).' 
//                         and itemkey = '.$this->oDbCon->paramString($itemkey);
//
//            }
//
//            $this->oDbCon->execute($sql); 
//            $this->oDbCon->endTrans();
//       
//        } catch(Exception $e){
//            $this->oDbCon->rollback();
//            $this->addErrorList($arrayToJs,false,$e->getMessage());
//        }		
//    
//    }

    function clearTemporaryCart(){
        $userkey = '';
        if (isset($_SESSION[$this->loginSession]['id']) && !empty( $_SESSION[$this->loginSession]['id']))
           $userkey =  base64_decode($_SESSION[$this->loginSession]['id']);

        if (empty($userkey))   return;

          try{

                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);

                $sql  = 'delete from '.$this->tableCartTemp.'
                         where refkey = '.$this->oDbCon->paramString($userkey);


                $this->oDbCon->execute($sql); 
                $this->oDbCon->endTrans();
          
         } catch(Exception $e){
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs,false,$e->getMessage());
          }	
         

    }

    function getlatestSellingPrice($itemkey,$unitkey,$customerkey){

        // kalo customer gk ad, harus balikin nilai 0 
        // karena history harga jual better per customer
        
        if($customerkey == 0) return 0;
        
        $sql = 'select 
                        coalesce(priceInUnit,0) as price
                    from 
                        '.$this->tableName.',' . $this->tableNameDetail .'
                    where 
                        '.$this->tableName.'.pkey ='.$this->tableNameDetail.'.refkey and
                        '.$this->tableName.'.statuskey in (2,3) and
                        '.$this->tableNameDetail.'.unitkey = '.$this->oDbCon->paramString($unitkey).' and
                        '.$this->tableNameDetail.'.itemkey = '.$this->oDbCon->paramString($itemkey);
 
        if(!empty($customerkey))
            $sql .=  ' and '.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey);
        
        $sql .= ' order by trdate desc limit 1';
        
        $rs =  $this->oDbCon->doQuery($sql); 
 
        return $rs[0]['price']; 
    }

    function getDetailWithRelatedInformation($pkey,$criteria='',$orderby =''){

		// kategori harus dibenerin, kalo tipenya jasa, harus narik dari kategori jasa

        $sql = 'select
                '.$this->tableNameDetail .'.*, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode, 
                '.$this->tableItem.'.brandkey, 
                '.$this->tableItem.'.gramasi, 
                '.$this->tableItem.'.isvariant, 
                '.$this->tableItem.'.needsn, 
                '.$this->tableItem.'.parentkey as itemparentkey, 
                '.$this->tableBrand.'.name as brandname ,
                '.$this->tableItem.'.deftransunitkey,
                '.$this->tableItemCategory.'.pkey as itemcategorykey,
                '.$this->tableItemCategory.'.name as itemcategoryname,
                '.$this->tableItemUnit.'.code as unitcode,
                '.$this->tableItemUnit.'.name as unitname,
                 baseunit.name as baseunitname
              from
                '.$this->tableNameDetail .',
                '.$this->tableItemUnit.', 
                '.$this->tableItemUnit.' baseunit,
                '.$this->tableItem.'
                    left join '.$this->tableBrand.' on 	' . $this->tableItem .'.brandkey = '.$this->tableBrand.'.pkey 
                    left join '.$this->tableItemCategory.' on 	' . $this->tableItem .'.categorykey = '.$this->tableItemCategory.'.pkey 
              where
                '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
                '.$this->tableItem.'.baseunitkey = baseunit.pkey and 
		'.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' .$orderby;
        
        return $this->oDbCon->doQuery($sql);

    } 

    function getSerialNumber($refkey){
        if(!is_array($refkey))
            $refkey = array($refkey);
        
        $sql = 'select * from '.$this->tableDetailSerial.' where refkey in ('.$this->oDbCon->paramString($refkey,',').')'; 
        return $this->oDbCon->doQuery($sql);
    }


    function getDetailForAPI($arrKey, $arrIndex = array()){
		$rsDetailsCol = array();
		
        if(in_array('detail', $arrIndex)){   
			//$rsDetailsCol = array();  // ini kalo ad didalam jadinya error kalo ad 2 detail atau lebih, karena kereset lg yg sebelumnya
            $rsDetails = $this->getDetailWithRelatedInformation($arrKey); 
            $arrDetailKey = array_column($rsDetails, 'pkey');
            $rsDetailSN = $this->getSerialNumber($arrDetailKey); 
            $rsDetailSN = $this->reindexDetailCollections($rsDetailSN,'refkey'); 
            for($i=0;$i<count($rsDetails);$i++) {
                $detailkey = $rsDetails[$i]['pkey'];
                $rsDetails[$i]['detail_sn'] = array();
                if (!isset($rsDetailSN[$detailkey]))  continue;
                $arrDetailSN = $rsDetailSN[$detailkey];
                for($j=0;$j<count($arrDetailSN);$j++) { 
                    $arrTemp = array();
                    $arrTemp['serial_number'] = $arrDetailSN[$j]['serialnumber'];
                    $arrTemp['cogs'] = $arrDetailSN[$j]['costinbaseunit'];
                    array_push($rsDetails[$i]['detail_sn'], $arrTemp);
                }
            }
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['detail'] = $rsDetails;
        }
        
	 	if(in_array('payment_method_detail', $arrIndex)){  
            $rsDetails = $this->getPaymentMethodDetail($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey');
            $rsDetailsCol['payment_method_detail'] = $rsDetails;
        }
         
	 	if(in_array('voucher_detail', $arrIndex)){  
            $rsDetails = $this->getVoucherDetail($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['voucher_detail'] = $rsDetails;
        }
         
		 if(in_array('image_url', $arrIndex)){  
			$rsDetails = $this->getItemImagesForAPI($arrKey);
			$rsDetails = $this->reindexDetailCollections($rsDetails,'detailkey');   
			$rsDetailsCol['image_url'] = $rsDetails;
         }
		
        return $rsDetailsCol;
    }
	
	function getItemImagesForAPI($arrPkey){
        // yg dibalikin harus pkey detailnya 
		
        $sql = 'select 
				'.$this->tableNameDetail.'.pkey as detailkey,
	   			'.$this->tableItemImage .'.pkey,
	   			'.$this->tableItemImage .'.refkey,
	   			'.$this->tableItemImage .'.file 
			  from 
			  	'.$this->tableNameDetail.',
				'.$this->tableItemImage.'  
			  where   
			  	'.$this->tableNameDetail.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').') and
				'.$this->tableNameDetail.'.itemkey = '.$this->tableItemImage.'.refkey';
    
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
  
		$rs = $this->oDbCon->doQuery($sql);
            
        $total = count($rs);    
        for($i=0;$i<count($rs);$i++)
            $rs[$i]['url'] = HTTP_HOST.'download/item/'.$rs[$i]['refkey'].'/'.$rs[$i]['file'];
        
        return $rs;
     
    }
  

    function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){

     $sql = 'select
                '.$this->tableName. '.pkey,  concat('.$this->tableName. '.code,\' - \', '.$this->tableCustomer.'.name) as value,  grandtotal
            from 
                '.$this->tableName . ','.$this->tableCustomer.','.$this->tableStatus.'
            where  		
                '.$this->tableName . '.customerkey = '.$this->tableCustomer.'.pkey and
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
        ';


    if(!empty($fieldname)){

        $sql .= ' and ' ;

        if($mustmatch)
            $sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
        else
            $sql .=  $fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%');
    }

    if($searchCriteria <> '')
        $sql .= ' ' .$searchCriteria;

    if($orderCriteria <> ''){
        $sql .= ' ' .$orderCriteria;

    }

    if($limit <> '')
        $sql .= ' ' .$limit;

    return $this->oDbCon->doQuery($sql);	
    } 
 
    function generateShipmentTracking($pkey){ 
    $rsHeader = $this->getDataRowById($pkey);  

    $recipientName = $rsHeader[0]['recipientname'];
    $recipientAddress = $rsHeader[0]['recipientaddress'];
    $recipientPhone = $rsHeader[0]['recipientphone'];
    $recipientEmail = $rsHeader[0]['recipientemail'];

    $shipment = new Shipment();	
    $rsShipment = $shipment->getDataRowById($rsHeader[0]['shipmentkey']);	

    $insurance = '';
    if ($rsHeader[0]['useinsurance'])
        $insurance = ', '.$this->lang['insurance'];

    $content = ' 
        <div style="width:700px;" >

            <table style="width:100%;">   
                <tr>
                    <td style="padding: 5px; vertical-align:top; width:100px"><strong>'.$this->lang['shipmentReceipt'].'</strong></td>  
                    <td style="padding: 5px; vertical-align:top;">'.$rsHeader[0]['shipmenttracking'].'</td> 
                </tr>     
                <tr>
                    <td style="padding: 5px; vertical-align:top;"><strong>'.$this->lang['shipment'].'</strong></td>  
                    <td style="padding: 5px; vertical-align:top;">'.$rsShipment[0]['name'].$insurance.'</td> 
                </tr>   
                <tr>
                    <td style="padding: 5px; vertical-align:top;"><strong>'.$this->lang['shippingFee'].'</strong></td>  
                    <td style="padding: 5px; vertical-align:top;">'.$this->formatNumber($rsHeader[0]['shipmentfee']).'</td> 
                </tr> 
                 <tr>
                    <td style="padding: 5px; vertical-align:top;"></td>  
                    <td style="padding: 5px; vertical-align:top;"></td> 
                </tr>     
                <tr>
                    <td style="padding: 5px; vertical-align:top;"><strong>'.$this->lang['name'].'</strong></td>  
                    <td style="padding: 5px; vertical-align:top;">'.$recipientName.'</td> 
                </tr>  
                <tr>
                    <td style="padding: 5px; vertical-align:top;"><strong>'.$this->lang['phone'].'</strong></td>  
                    <td style="padding: 5px; vertical-align:top;">'.$recipientPhone.'</td>  
                <tr> 
                <tr>
                    <td style="padding: 5px; vertical-align:top;"><strong>'.$this->lang['address'].'</strong></td>  
                    <td style="padding: 5px; vertical-align:top;"> '.str_replace(chr(13),'<br>',$recipientAddress).'</td>  
                <tr>
            </table>

            <div style="clear:both; height:10px;"></div>
        </div>
    ';	 


         return $content;
    }
 
    function generateInvoice($pkey){   
        $rsHeader = $this->getDataRowById($pkey);

        $file=  HTTP_HOST . 'invoice/'.$pkey.'/'.$this->getTransactionToken($rsHeader).'/1';     
        // $invoice =  file_get_contents($file);
        
        $ch = curl_init($file);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        
        if ($response === false) {
            $this->setLog ('Curl error: ' . curl_error($ch),true);
        } else {
            // $this->setLog ('Success!',true);
            // $this->setLog ($response,true);
        }
        
        curl_close($ch);
        
        return $response;
    }

    function updateCostAndProfit($id){
        
        //gk bis dipake utk SN, karema bisa beda baris utk item yg sama, tp modal berbeda
        
        
        $rs = $this->getDataRowById($id);
        $rsDetail = $this->getDetailById($id);
        $item = new Item();

        $subtotal = 0;
        $totalProfit = 0;


        $arrItemKey = array_column($rsDetail, 'itemkey');
        
        // sementara biarin dulu, nanti overwrite dibawah kalo pake SN, karena agka susah pishin diawal.
        $rsItemCOGS = $item->getCOGS($arrItemKey, ' and trdate <= '.$this->oDbCon->paramString($rs[0]['trdate'])); 
        $rsItemCOGS = array_column($rsItemCOGS, null,'itemkey'); 
        
        $rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.needsn'),
                                       ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemKey,',').')');
        $rsItemCol = array_column($rsItemCol,null,'pkey');
        
        for($i=0;$i<count($rsDetail);$i++){

            $itemkey = $rsDetail[$i]['itemkey'];
            $rsItem = $rsItemCol[$itemkey];
            
        
            if($rsItem['needsn'] == 1){
                // ambil detail SN
                $rsSNDetail = $this->getSerialNumber($rsDetail[$i]['pkey']);
        
                $arrSN = array_column($rsSNDetail,'serialnumber' );
                $cogs = $item->getSNCOGS($itemkey, $arrSN);
            }else{ 
                $itemCOGS = $rsItemCOGS[$itemkey];
                $cogs = (!empty($itemCOGS)) ? $itemCOGS['cogs'] : 0; 
            }
            
            $discount = $rsDetail[$i]['discount'];  
            if ($discount != 0 && $rsDetail[$i]['discounttype'] == 2){
                    $discount = $discount/100 * $rsDetail[$i]['priceinunit'];
            }

            $rsDetail[$i]['cogs'] = $cogs;
            $rsDetail[$i]['unitDiscountValue'] = $discount;
            $detailSubtotal = $rsDetail[$i]['qty'] * ( $rsDetail[$i]['priceinunit'] - $discount);

            $subtotal += $detailSubtotal ; 
        }
 
		$finalDiscount = $this->getDiscountValue($rs[0]['subtotal'], $rs[0]['finaldiscount'],$rs[0]['finaldiscounttype']);
		if($rs[0]['finaldiscount2'] > 0){ 
			$finalDiscount += $this->getDiscountValue( ($rs[0]['subtotal'] -  $finalDiscount), $rs[0]['finaldiscount2'],$rs[0]['finaldiscounttype2']);
		}

        // nanti dibuat opsi saja
//        $totalFinalDiscountAndPointValue = 0;
//        if (!USE_GL){ 
//            $totalFinalDiscountAndPointValue = $finalDiscount + $rs[0]['pointvalue'] ; 
//        }
        
          $totalFinalDiscountAndPointValue = $finalDiscount + $rs[0]['pointvalue'] ; 

		$totalCOGS = 0 ;
        for($i=0;$i<count($rsDetail);$i++){

            $unitDiscountedValue = $rsDetail[$i]['priceinunit'] -  $rsDetail[$i]['unitDiscountValue']  ;
            $proportionDisc = ($subtotal <> 0) ? $unitDiscountedValue/$subtotal : 0;
            
            $priceInUnitBeforeTax = $unitDiscountedValue - ($proportionDisc * $totalFinalDiscountAndPointValue);

            if ($rs[0]['ispriceincludetax'] == 1) { 
                    $taxValue = ($rs[0]['taxpercentage']/(100 + $rs[0]['taxpercentage'])) * $priceInUnitBeforeTax;   
                    $priceInUnitBeforeTax = $priceInUnitBeforeTax - $taxValue ; 
            }  

            $profit = ($priceInUnitBeforeTax / $rsDetail[$i]['unitconvmultiplier']) - $rsDetail[$i]['cogs'];
 			
            $sql = 'update '. $this->tableNameDetail .' set costinbaseunit = '.$rsDetail[$i]['cogs'].', profit = '.$profit.' where pkey = ' . $rsDetail[$i]['pkey'];
            $this->oDbCon->execute($sql);

			
            $totalProfit  += $rsDetail[$i]['qtyinbaseunit'] * $profit;
			
			$totalCOGS += $rsDetail[$i]['cogs'] *  $rsDetail[$i]['qtyinbaseunit'];
        }
         
        //update header 
		
        $sql = 'update '. $this->tableName  .' set totalcogs = '.$totalCOGS.', profit = '.$totalProfit.' where pkey = ' . $rs[0]['pkey'];
		 
        $this->oDbCon->execute($sql);

    }

    function updateShipmentTracking($id,$value){

    
        $arrayToJs = array();

        try{

            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            $value = trim($value);
            if (empty($value))
                throw new Exception($this->errorMsg['shipmentTracking'][1]);

            $sql = 'update '.$this->tableName.' set shipmenttracking = '.$this->oDbCon->paramString($value).' where pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);

            /*$invoice =  $this->generateShipmentTracking($id);
            $emailTemplate = $this->getEmailTemplate(); 

            $patterns = array();
            $patterns[count($patterns)] = '/({{CONTENT}})/'; 

            $replacement = array();
            $replacement[count($replacement)] = $invoice;  

            $email = preg_replace($patterns, $replacement, $emailTemplate);  

            $rs = $this->getDataRowById($id);
            $this->sendMail(array(),$this->lang['shippingInformation'] . ' '. $rs[0]['code'],$email,array('email'=>$rs[0]['recipientemail']));	

            $sql = 'update '.$this->tableName.' set shipmenttrackingSent = shipmenttrackingSent + 1 where pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);*/

            if ($this->isActiveModule('marketplace')){ 
                $marketplace = new MarketPlace();   
                $marketplace->onUpdateAWB($id,$value); // yg dilempar pkey, karena setiap marketplace kemungkinan berbeda ambil dr marketplaceorderid / refcode 
            }
            
            $this->oDbCon->endTrans();

            $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 


        } catch(Exception $e){
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs,false,$e->getMessage());
        }		

        return $arrayToJs; 
    

    }
 
    function getSalesByMonth($startPeriod, $endPeriod, $criteria ='', $warehousekey = ''){ 
        $sql = 'select 
                    month('.$this->tableName.'.trdate) as month,  
                    DATE_FORMAT('.$this->tableName.'.trdate, \'%b\')  as monthname, 
                    year('.$this->tableName.'.trdate) as year, 
                    sum('.$this->tableName.'.beforetaxtotal) as total  
                from 
                    '.$this->tableName.'
                where  
                    ('.$this->tableName.'.statuskey in (2,3)) and 
                    '.$this->tableName.'.trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')';

        if(!empty($criteria)) $sql .= $criteria;    
        
		if (!empty($warehousekey))
				$sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
		
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;

        $sql .= ' group by year(trdate),month(trdate)';

//		$this->setLog($sql,true);

        return $this->oDbCon->doQuery($sql);  
    }

    function getSalesByMonthAndCustomer($startPeriod, $endPeriod, $criteria ='', $warehousekey = '',$groupby = ''){
        
        $sql = 'select 
                    month('.$this->tableName.'.trdate) as month,  
                    DATE_FORMAT('.$this->tableName.'.trdate, \'%b\') as monthname, 
                    year('.$this->tableName.'.trdate) as year, 
                    concat(customerkey,\'-\',DATE_FORMAT(trdate, \'%c%Y\'))  as periodindex,
                    '.$this->tableCustomer.'.name, 
                    sum('.$this->tableName.'.subtotal) as total  
                from 
                    '.$this->tableName.',
                    '.$this->tableCustomer.'
                where  
                    '.$this->tableCustomer.'.pkey = '.$this->tableName.'.customerkey and
                    ('.$this->tableName.'.statuskey in (2,3)) and 
                    '.$this->tableName.'.trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')';

        if(!empty($criteria)) $sql .= $criteria;    
        
		if (!empty($warehousekey))
				$sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
		
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;

        if (!empty($groupby))  $groupby = ', ' .$groupby;

        $sql .= ' group by year(trdate),month(trdate)'.$groupby;

        return $this->oDbCon->doQuery($sql);  
    }

    function getProfitByMonth($startPeriod, $endPeriod,$warehousekey = ''){
        

        $security = new Security();
        $item = new Item();

/*        $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
        if (!$hasCOGSAccess)  return ; */

        $sql = 'select 
                    month(trdate) as month,  
                    DATE_FORMAT(trdate, \'%b\')  as monthname, 
                    year(trdate) as year, 
                    sum(profit) as total
                from 
                    '.$this->tableName.'
                where (statuskey = 2 or statuskey = 3) and trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\') ';
        
		if (!empty($warehousekey))
				$sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
        
        $sql .=  ' group by year(trdate),month(trdate)';

        return $this->oDbCon->doQuery($sql); 

    } 

    function getMostProfitableSalesByGroup($groupBy, $startPeriod, $endPeriod, $limit = 10, $warehousekey = ''){
    // margin sales
 
        $security = new Security();
        $item = new Item();
/*
        $hasCOGSAccess = $security->isAdminLogin($item->cogsSecurityObject,10);
        if (!$hasCOGSAccess)  return ; 
*/

        $sql = 'select 
                  sum('.$this->tableNameDetail.'.profit * '.$this->tableNameDetail.'.qtyinbaseunit) as profit, 
                  '.$this->tableItemCategory.'.name  as categoryname,
                  '.$this->tableBrand.'.name  as brandname,
                  '.$this->tableItem.'.name as itemname
                from 
                    '.$this->tableName.', 
                    '.$this->tableNameDetail.',
                    '.$this->tableItem.'
						left join '.$this->tableBrand.' on '.$this->tableItem.'.brandkey =  '.$this->tableBrand.'.pkey, 
                    '.$this->tableItemCategory.' 
                where 
                    ('.$this->tableName.'.statuskey = 2 or '.$this->tableName.'.statuskey = 3) and 
                     '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                     '.$this->tableName.'.trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\') and 
                     '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and 
                     '.$this->tableItem.'.categorykey = '.$this->tableItemCategory.'.pkey';
        
		if (!empty($warehousekey))
				$sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
        
        $sql .= ' group by 
                    '.$groupBy.'
                 order by profit desc limit ' . $limit;
  
        return $this->oDbCon->doQuery($sql); 
    }   

    function getBestSellingByGroup($groupBy, $startPeriod, $endPeriod, $limit = 10, $warehousekey = ''){
    // QTY BASED

    $sql = 'select 
              sum('.$this->tableNameDetail.'.qtyinbaseunit) as qty, 
              '.$this->tableItemCategory.'.name  as categoryname,
              '.$this->tableBrand.'.name  as brandname,
              '.$this->tableItem.'.name as itemname,
              '.$this->tableItem.'.sellingprice as sellingprice,
              '.$this->tableItemUnit.'.name as unitname
            from 
                '.$this->tableName.', 
                '.$this->tableNameDetail.',
                '.$this->tableItem.', 
                '.$this->tableItemCategory.', 
                '.$this->tableBrand.',
                '.$this->tableItemUnit.'
            where 
                ('.$this->tableName.'.statuskey = 2 or '.$this->tableName.'.statuskey = 3) and 
                 '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                 '.$this->tableName.'.trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\') and 
                 '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey and 
                 '.$this->tableItem.'.categorykey = '.$this->tableItemCategory.'.pkey  and 
                 '.$this->tableItem.'.brandkey = '.$this->tableBrand.'.pkey   and 
                 '.$this->tableItem.'.baseunitkey = '.$this->tableItemUnit.'.pkey ';

			if (!empty($warehousekey))
				$sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
		
            $sql .=  $this->getWarehouseCriteria() ;
            $sql .=  $this->getCompanyCriteria() ;


        $sql .= 'group by 
                '.$groupBy.'
             order by qty desc limit ' . $limit;
 
    return $this->oDbCon->doQuery($sql); 
    }  

    function getBestSalesAmountByGroup($groupBy, $startPeriod, $endPeriod, $limit = 5, $warehousekey = ''){
         // VALUE BASED

        $sql = 'select 
                  sum('.$this->tableName.'.beforetaxtotal) as amount, 
                  '.$this->tableCustomer.'.name  as customername
                from 
                    '.$this->tableName.', 
                    '.$this->tableCustomer.' 
                where 
                    ('.$this->tableName.'.statuskey = 2 or '.$this->tableName.'.statuskey = 3) and 
                     '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                     '.$this->tableName.'.trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\') ';
        
		if (!empty($warehousekey))
			$sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
         $sql .=  $this->getWarehouseCriteria() ;
         $sql .=  $this->getCompanyCriteria() ;
        
         $sql .= 'group by 
                    '.$groupBy.'
                 order by amount desc limit ' . $limit;

        return $this->oDbCon->doQuery($sql); 
    }  

    function updateAvailableQty($arrParam){
    //use this function to re-adjust available stock 

    $itemMovement = new ItemMovement();
    $item = new Item();

    $warehousekey = $arrParam['selWarehouseKey'];

    $arrItemKey = $arrParam['hidItemKey'];
    $arrUnit =  $arrParam['selUnit'];
    $arrQty =  $arrParam['qty'];

    for($i=0;$i<count($arrItemKey);$i++){
        $itemkey = $arrItemKey[$i];
        $transactionUnitKey = $arrUnit[$i];

        $orderedQty =  $this->unFormatNumber($arrQty[$i]);

        // qoh in base unit 
        $qoh = $itemMovement->getItemQOH($itemkey,$warehousekey);

        // hitung qty dalam transaksi unit
        // cari nilai konversi terlebih dahulu 
        $rsItem = $item->getDataRowById($itemkey); 
        $baseunitkey = $rsItem[0]['baseunitkey']; 
        $convMultiplier = $item->getConvMultiplier($itemkey,$transactionUnitKey,$baseunitkey);  

        $qty = $qoh / $convMultiplier; 

        if ( $orderedQty > $qty) 
            $arrParam['qty'][$i] = $qty;

    } 

    return $arrParam;
    } 
    
     function normalizeParameter($arrParam, $trim = false){ 
            $termOfPayment = new TermOfPayment();
            $shipment = new Shipment();
            $item = new Item();
          
            $arrParam['checksum'] = md5(time().$this->generateStrongPassword()); 
         
            $arrParam['selCustomCode'] = (empty($arrParam['selCustomCode'])) ? 0 : $arrParam['selCustomCode'];
            $arrParam['taxValue'] = (isset($arrParam['taxValue'])) ? $arrParam['taxValue'] : 0;
            $arrParam['taxPercentage'] = (isset($arrParam['taxPercentage'])) ? $arrParam['taxPercentage'] : 0;
            $arrParam['shipmentFee'] = (isset($arrParam['shipmentFee'])) ? $arrParam['shipmentFee'] : 0;
            $arrParam['etcCost'] = (isset($arrParam['etcCost'])) ? $arrParam['etcCost'] : 0;
            $arrParam['paymentMethodValue'] = (isset($arrParam['paymentMethodValue'])) ? $arrParam['paymentMethodValue'] : array();
            $arrParam['chkIsDropship'] = (isset($arrParam['chkIsDropship'])) ? $arrParam['chkIsDropship'] : 0;
            $arrParam['selShipmentService'] = (isset($arrParam['selShipmentService'])) ? $arrParam['selShipmentService'] : 0;
            $arrParam['useInsurance'] = (isset($arrParam['useInsurance'])) ? $arrParam['useInsurance'] : 0;
            $arrParam['pointValue'] = (isset($arrParam['pointValue'])) ? $arrParam['pointValue'] : 0;
            $arrParam['selSalesKey'] = (isset($arrParam['selSalesKey'])) ? $arrParam['selSalesKey'] : 0;
            $arrParam['marketplaceKey'] = (isset($arrParam['marketplaceKey'])) ? $arrParam['marketplaceKey'] : 0;
      	    $arrParam['hidSalesKey'] = (isset($arrParam['hidSalesKey'])) ? $arrParam['hidSalesKey'] : 0;
          
             
            //$isFullReceive = 1; 

            $arrItemkey = $arrParam['hidItemKey'];
            $arrVoucherkey = $arrParam['hidVoucherKey'] ?? array();
            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit'];   
            $arrUnitKey = $arrParam['selUnit']; 
            
            $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
            if ($rsTOP[0]['duedays'] != 0){   
                for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
                    $arrParam['paymentMethodValue'][$i] = 0; 
                    $arrParam['hidDetailPaymentKey'][$i] = 0;
                }
            }
 
             for ($i=0;$i<count($arrItemkey);$i++){ 
                $arrParam['discountValueInUnit'][$i] = (!isset($arrParam['discountValueInUnit'][$i])) ? 0 : $arrParam['discountValueInUnit'][$i];
                $arrParam['selDiscountType'][$i] = (!isset($arrParam['selDiscountType'][$i])) ? 1 : $arrParam['selDiscountType'][$i];
                $arrParam['refMarketplaceKey'][$i] = (!isset($arrParam['refMarketplaceKey'][$i])) ? '' : $arrParam['refMarketplaceKey'][$i];  
             }


            // init kalo qtyinpcs atau priceinpc kosong, diisi seusai qty dan priceinunit
            // karena akan digunakan dalam perhitungan
            if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['jewelry']))){    
                 for ($i=0;$i<count($arrItemkey);$i++){ 
                        if(!isset($arrParam['qtyInPcs']) || empty($arrParam['qtyInPcs'][$i])) $arrParam['qtyInPcs'][$i] = $arrParam['qty'][$i];
                        if(!isset($arrParam['priceInPcs']) || empty($arrParam['priceInPcs'][$i])) $arrParam['priceInPcs'][$i] = $arrParam['priceInUnit'][$i];
                 }
            }
         

            $reCountResult = $this->reCountSubtotal($arrParam); 
            $arrParam['detailCOGS'] = $reCountResult['detailCOGS'];
            $arrParam['detailVoucher'] = $reCountResult['detailVoucher'];
		 
		 
		 	//$this->setLog('recounnt',true);
		 	//$this->setLog($reCountResult['detailVoucher'],true);
		 
            $arrParam['profit'] =$reCountResult['profit'];
            $arrParam['subtotal'] = $reCountResult['subtotal'];
		  
            $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
            $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
            $arrParam['taxValue'] = $reCountResult['taxValue'];
            $arrParam['grandtotal'] = $reCountResult['grandtotal'];
            $arrParam['totalPayment'] = $reCountResult['totalPayment'];
            $arrParam['balance'] = $reCountResult['balance']; 
            $arrParam['totalWeight'] = $reCountResult['totalWeight'];
            $arrParam['totalCOGS'] = $reCountResult['totalCOGS'];
           
             for ($i=0;$i<count($arrItemkey);$i++){ 
  
                $qtyinbaseunit = $arrParam['detailCOGS'][$i]['qtyInBaseUnit'];  
                $arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit;
                $arrParam['unitConvMultiplier'][$i] = $arrParam['detailCOGS'][$i]['unitConvMultiplier'];
                $arrParam['cogs'][$i] = $arrParam['detailCOGS'][$i]['cogs']; 
                $arrParam['detailProfit'][$i] = $arrParam['detailCOGS'][$i]['profit'];
                $arrParam['priceInBaseUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInBaseUnit']; 
                $arrParam['detailSubtotal'][$i] = $arrParam['detailCOGS'][$i]['detailSubtotal'];
                $arrParam['itemType'][$i] = $arrParam['detailCOGS'][$i]['itemType'];
                $arrParam['itemWeight'][$i] = $arrParam['detailCOGS'][$i]['weight'];  
                $arrParam['headerDiscValueInUnit'][$i] = $arrParam['detailCOGS'][$i]['headerDiscValueInUnit'];  
                
                /// normalize jewelry biasanya
                // by default harusnya sesuai dengan inputan form
                //if(!isset($arrParam['priceInPcs'][$i])){ 
                    $arrParam['qtyInPcs'][$i] = $arrParam['detailCOGS'][$i]['qtyInPcs'];
                    $arrParam['priceInPcs'][$i] = $arrParam['detailCOGS'][$i]['priceInPcs'];
                    $arrParam['priceInUnit'][$i] = $arrParam['detailCOGS'][$i]['priceInUnit'];			
                //}	
                 
                if(!isset($arrParam['trDetailDesc'][$i]))
				   $arrParam['trDetailDesc'][$i] = '';
				 
                // set default jadi 0 lg, utk handle copy on cancel
                $arrParam['deliveredQtyInBaseUnit'][$i] = 0;
            }
		 
		 	// voucher  
             for ($i=0;$i<count($arrVoucherkey);$i++){ 
				  // kalo vouchernya 0, gk bisa dipake, dihapus // kedepan pake validasi saja
				  $arrParam['voucherAmount'][$i] = $arrParam['detailVoucher'][$i]['voucherAmount']; 
				 
				  if ($arrParam['detailVoucher'][$i]['voucherAmount'] <= 0)
					   $arrParam['hidVoucherKey'][$i] = 0;
			 }
		 
		 
			 
 
         $rsService = $shipment->getServices('',$arrParam['selShipmentService']);
         $arrParam['selShipment'] = $rsService[0]['refkey'];
  
         
            //SN
            if( $arrParam['chkIsFullDeliver'] == 0){
                for($i=0;$i<count($arrParam['snList']);$i++) {
                    $arrParam['snList'][$i] = ''; 
                    $arrParam['hidDetailSNKey'][$i] = 0;
                    $arrParam['serialNumberDetail'][$i] = '';
                    $arrParam['COGSSN'][$i] = 0;
                }
            } else {

                // ========= update SN
                $arrParam['hidDetailSNKey'] = array();
                $arrParam['serialNumberDetail'] = array();
                $arrParam['hidDetailSNKeyTotalRows'] = array('1' => array()); 
 
                $snCtr = 0;
                
                
                for($i=0;$i<count($arrParam['hidItemKey']);$i++){
 
                    
                    $snList = (isset($arrParam['snList'][$i]) && !empty($arrParam['snList'][$i])) ?  trim($arrParam['snList'][$i]): '';
                    $arrSerialNumber = (!empty($snList)) ?  preg_split($this->SN_SPLIT_REGEX, $snList) : [];
                    
                    $arrParam['hidDetailSNKeyTotalRows'][1][$i] = count($arrSerialNumber);
                     
                    $rsSN = $item->searchSerialNumber($arrParam['hidItemKey'][$i],'',$arrSerialNumber);
                    $rsSN = array_column($rsSN,'costinbaseunit', 'serialnumber');
                        
                    foreach($arrSerialNumber as $snRow){  
                        
                        // cogs per SN
                        $cogsDetail = $rsSN[$snRow];
 
                        $arrParam['hidDetailSNKey'][$snCtr] = 0;
                        $arrParam['serialNumber'][$snCtr] = $snRow;
                        $arrParam['COGSSN'][$snCtr] = $cogsDetail;
                        $snCtr++;
                    } 
                }  
                // ========= update SN

            }

            $details = array();
            array_push($details,$this->arrDataDetailSN);
            $arrParam = $this->prepareMultiLevelDetail($arrParam,$details);
                 
         
         
         
         $arrParam = parent::normalizeParameter($arrParam,true);
		 
        return $arrParam;
    }
    
    function getSalesOrderReturn($pkey)
    {
        $sql = '
            select 
                ' . $this->tableNameDetail . '.*,
                (' . $this->tableNameDetail . '.deliveredqtyinbaseunit - ' . $this->tableNameDetail . '.returnqtyinbaseunit) as outstandingqty,
                concat(' . $this->tableNameDetail . '.pkey,\'-\',' . $this->tableNameDetail . '.itemkey) as joinkey,
                ' . $this->tableItem . '.name as itemname
            from
                ' . $this->tableNameDetail . '
                    left join ' . $this->tableItem . ' on ' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey
            where
                ' . $this->tableNameDetail . '.pkey = ' . $this->oDbCon->paramString($pkey) . '
        ';

        $result = $this->oDbCon->doQuery($sql);

        return $result;
    }

    function updateSalesOrderReturnItem($pkey) {
        $salesOrderReturn = new SalesOrderReturn();
        $rsHeader = $this->getDataRowById($pkey);
        $rsDetail = $this->getDetailById($pkey);

		$sql = '
			select
				 coalesce(sum('. $salesOrderReturn->tableNameDetail .'.returnqtyinbaseunit),0) as totalreturnqtyinbaseunit,
				 '. $salesOrderReturn->tableNameDetail .'.refsodetailkey
			from
				'. $salesOrderReturn->tableName .', '. $salesOrderReturn->tableNameDetail .'
			where
				'. $salesOrderReturn->tableName .'.pkey = '. $salesOrderReturn->tableNameDetail .'.refkey and
				'. $salesOrderReturn->tableName .'.refkey = '. $this->oDbCon->paramString($pkey) .' and 
				'. $salesOrderReturn->tableNameDetail .'.refsodetailkey in  ('. $this->oDbCon->paramString(array_column($rsDetail,'pkey') ,',') .') and
				'. $salesOrderReturn->tableName .'.statuskey in (2,3) 
		';

		$rsTotal = $this->oDbCon->doQuery($sql);
		$rsTotal = array_column($rsTotal,null,'refsodetailkey');

        for($i=0; $i < count($rsDetail); $i++) {

        	$totalRow = $rsTotal[$rsDetail[$i]['pkey']];

            $sql = 'update ' . $this->tableNameDetail . ' set  returnqtyinbaseunit = ' . $totalRow['totalreturnqtyinbaseunit'] . ' where pkey = ' . $rsDetail[$i]['pkey']; 
            $this->oDbCon->execute($sql);
  
        } 

    }
	
     function updateSalesOrderDeliveredItem($pkey){ 
            $salesDelivery = new SalesDelivery(); 
            $rsHeader = $this->getDataRowById($pkey);  
            $rsDetail = $this->getDetailById($pkey); 

            for($i=0;$i<count($rsDetail); $i++){	
                $sql = 'select 
                        coalesce(sum(deliveredqtyinbaseunit),0) as totaldeliveredqtyinbaseunit
                    from 
                        '. $salesDelivery->tableName . ', '. $salesDelivery->tableNameDetail . '
                    where 
                         '. $salesDelivery->tableName . '.pkey = '. $salesDelivery->tableNameDetail . '.refkey and
                         '. $salesDelivery->tableName . '.refkey = '. $this->oDbCon->paramString($pkey) .' and
                         '. $salesDelivery->tableNameDetail . '.itemkey = ' . $rsDetail[$i]['itemkey'] .' and 
                         '. $salesDelivery->tableNameDetail . '.refsodetailkey = ' . $rsDetail[$i]['pkey'] .' and 
                         (statuskey = 2 or statuskey = 3)';
 
                $rsTotal = $this->oDbCon->doQuery($sql);

                // INI AKAN PROBLEM KALO DETAIL PUNYA 2 ITEM YG SAMA
                $sql = 'update 
                            ' . $this->tableNameDetail.' 
                        set  
                            deliveredqtyinbaseunit = '. $rsTotal[0]['totaldeliveredqtyinbaseunit'] .'
                        where 
                            refkey = '.$pkey.' and 
                            pkey = '.$rsDetail[$i]['pkey'].' and 
                            itemkey = ' . $rsDetail[$i]['itemkey'];
                 
                $this->oDbCon->execute($sql); 
            }

            //check if all item received, change PO status to finish
            $sql = 'select * from ' . $this->tableNameDetail.' where refkey = '.$this->oDbCon->paramString($pkey).' and  deliveredqtyinbaseunit < qtyinbaseunit';
            $rs = $this->oDbCon->doQuery($sql);

            $statuskey = (empty($rs)) ? 3 : 2; 
              
            if ($rsHeader[0]['statuskey'] <> $statuskey)
                $this->changeStatus($pkey,$statuskey); 
      
    }
    
    
    function generateSalesOrderItem($criteria='',$order='',$groupBy='',$dateDiff=''){
     
        $sumQty = (!empty($groupBy)) ? 'sum('.$this->tableNameDetail.'.qtyinbaseunit) as totalqty,sum('.$this->tableNameDetail.'.total) - sum('.$this->tableNameDetail.'.headerdiscvalueinunit * '.$this->tableNameDetail.'.qty) as totalsales,' : '';
        
        $diffDate = (!empty($dateDiff)) ? $dateDiff: '';
     
        $sql = 'select
                '.$sumQty.'
                '.$diffDate.'
                '.$this->tableName.'.code,
                '.$this->tableName.'.refcode,
                month('.$this->tableName.'.trdate) as month,  
                year('.$this->tableName.'.trdate) as year, 
                '.$this->tableName.'.pkey,
                '.$this->tableName.'.trdate,
                '.$this->tableName.'.customerkey,
                '.$this->tableName.'.saleskey as salesordersaleskey,
                '.$this->tableNameDetail.'.priceinunit,
                '.$this->tableNameDetail.'.discounttype,
                '.$this->tableNameDetail.'.discount,
                '.$this->tableNameDetail.'.total,
                ('.$this->tableNameDetail.'.total - ( '.$this->tableNameDetail.'.headerdiscvalueinunit * '.$this->tableNameDetail.'.qty )) as totalafterheaderdisc,
                '.$this->tableNameDetail.'.qty,
                '.$this->tableNameDetail.'.itemkey,
                '.$this->tableNameDetail.'.headerdiscvalueinunit,
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableCustomer.'.code as customercode,
                '.$this->tableCustomer.'.name as customername,
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItem.'.name as itemname,
                '.$this->tableItem.'.brandkey,
                '.$this->tableItemUnit.'.name as unitname,
                '.$this->tableStatus.'.status as statusname, 
                '.$this->tableBrand.'.code as brandcode,
                '.$this->tableBrand.'.name as brandname,
                '.$this->tableEmployee.'.code as salesmancode,
                '.$this->tableEmployee.'.name as salesmanname   
            from
                '.$this->tableName.'
                    left join '.$this->tableEmployee.' on '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey,
                '.$this->tableWarehouse.',
                '.$this->tableCustomer.',
                '.$this->tableStatus.',
                '.$this->tableNameDetail.'
                    inner join '.$this->tableItem.' on '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey
                    inner join '.$this->tableItemUnit.' on '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey
                    left join '.$this->tableBrand.' on '.$this->tableItem.'.brandkey = '.$this->tableBrand.'.pkey
            where
                '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
        ';
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
                   
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
           
                
        if(!empty($groupBy))
            $sql .= ' '.$groupBy;

        if (!empty($order))  
            $sql .=  ' ' .$order; 
        
        //$this->setLog($sql,true);
       return $this->oDbCon->doQuery($sql);
 
    }
    
    function getDailyTransactionSummary($daylimits = 10, $criteria = '', $warehousekey = ''){
        
        if(!is_numeric($daylimits))
            $daylimits = 10;
        
        $sql = 'select 
                    group_concat('.$this->tableName.'.pkey) as pkey, 
                    date('.$this->tableName.'.trdate) as trdate, 
                    sum('.$this->tableName.'.beforetaxtotal) as totalsales 
                from 
                    '.$this->tableName.' 
                where 
                    '.$this->tableName.'.statuskey in (2,3) and
                    date('.$this->tableName.'.trdate) + interval '.$daylimits.' day >= now()';
        
        if(!empty($criteria)) $sql .= $criteria; // utk marketplace dulu
        
		        
		if (!empty($warehousekey))
				$sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
        
        $sql .= ' group by 
                    date('.$this->tableName.'.trdate) order by '.$this->tableName.'.trdate desc '; 
         	
        $rs = $this->oDbCon->doQuery($sql);
        
        foreach($rs as $key=>$row){ 
            $sql = 'select 
                        sum('.$this->tableNameDetail.'.qty) as totalqty,
                        '.$this->tableItemUnit.'.name as unitname
                    from 
                        '.$this->tableNameDetail.','.$this->tableItemUnit.'
                    where 
                        '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
                        '.$this->tableNameDetail.'.refkey in ('.$row['pkey'].') 
                    group by
                        '.$this->tableNameDetail.'.unitkey ';
            $rsDetail = $this->oDbCon->doQuery($sql);
            
            $qtySold = array();
            foreach($rsDetail as $detailRow) 
                array_push($qtySold, $this->formatNumber($detailRow['totalqty']) . ' '. $detailRow['unitname']);
            
            $rs[$key]['totalsoldinunit'] = implode('<br>',$qtySold);
        }
        
        return $rs;
            
    }

    function getLatestTransaction($random = true,$opt = array()){
        $item = new Item();
        $itemUnit = new ItemUnit();
        $qty = 1;
        
        $rs = array();
        
        if($random){
            
            $show = rand(1, 2);
            
            if ($show <> 2) return $rs; 
                 
            $rsItem = $item->searchData($item->tableName.'.statuskey',1,true,'',' order by RAND() limit 1');     
             
            $rsUnit = $itemUnit->getDataRowById($rsItem[0]['deftransunitkey']);
            $rsItemImage = $item->getItemImage($rsItem[0]['pkey']);
                
            $rs['itemkey'] = $rsItem[0]['pkey'];
            $rs['itemcode'] = $rsItem[0]['code'];
            $rs['itemname'] = $rsItem[0]['name'];
            
            $rs['itemimage']  = $rsItemImage[0]['file'];	
            $rs['phpthumbhash'] = getPHPThumbHash($rsItemImage[0]['file']);
 
            $rs['unitname'] = $rsUnit[0]['name'];
            $rs['qty'] = rand(1, 3);
             
        }
        
        return $rs;
        
    }
    
    function updateStatusFromPaymentConfirmation($sokey){
        
	   $sql = 'select
	   			sum('.$this->tablePaymentConfirmation.'.paymentamount) as totalpayment,
                '.$this->tableName.'.grandtotal,
                '.$this->tableName.'.statuskey as salesorderstatus
			  from
			  	'.$this->tableName.',
                '.$this->tablePaymentConfirmation.' 
			  where
			  	'.$this->tablePaymentConfirmation.'.refkey = '.$this->tableName.'.pkey and 
			  	'.$this->tablePaymentConfirmation.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].') and
			  	'.$this->tableName.'.pkey = '.$this->oDbCon->paramString($sokey);
         
        $sql .= ' group by '.$this->tableName.'.pkey'; 
              
		$rsPayment = $this->oDbCon->doQuery($sql);
	
        // sementara kalo paymentnya kurang (karena ada payment yg dicancel), kita blm handle
        if($rsPayment[0]['totalpayment'] >= $rsPayment[0]['grandtotal'] && $rsPayment[0]['salesorderstatus'] == TRANSACTION_STATUS['menunggu'])
            $this->changeStatus($sokey,TRANSACTION_STATUS['konfirmasi']); 
   }
    
    function updateMidtransResponse($response){
        
        if($this->isJSON($response))
            $postVars = json_decode($response,true);
        else
            parse_str($response,$postVars);  
        
        $transCode = explode('-',$postVars['order_id']);  
        array_pop($transCode); 
        $transCode = implode('',$transCode); 
        
        $transactionStatus = $postVars['transaction_status'];
        $amount =  $postVars['gross_amount'];
        
         try{  
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);
 
                $sql = 'update ' .$this->tableName.' set paymentgatewayresponse =  \''.addslashes(json_encode($postVars)).'\'
                        where code = ' . $this->oDbCon->paramString($transCode) ; 
                $this->oDbCon->execute($sql); 

                $this->oDbCon->endTrans();

        }catch (Exception $e){
            $this->oDbCon->rollback();
             
        } 
        
    }
    
    function updatePaymentGatewwayResponse($pkey,$arr){
          try{  
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);
 
                $sql = 'update '.$this->tableName.' 
                        set 
                            paymentgatewayinvoiceurl = '.$this->oDbCon->paramString($arr['invoiceURL']) .' 
                        where 
                            pkey = ' . $this->oDbCon->paramString($pkey) ;
             
                $this->oDbCon->execute($sql); 

                $this->oDbCon->endTrans();

        }catch (Exception $e){
            $this->oDbCon->rollback();
             
        } 
    }
    
    function sendPaymentInstruction($pkey){
        global $twig;
        
        $sql = 'select
                    '.$this->tableName.'.code,
                    '.$this->tableName.'.paymentgatewayinvoiceurl,
                    '.$this->tableName.'.recipientemail,
                    '.$this->tableName.'.recipientname,
                    '.$this->tableCustomer.'.name,
                    '.$this->tableCustomer.'.email
                from 
                    '.$this->tableName.'
                    left join '.$this->tableCustomer.' on  '.$this->tableName.'.customerkey =  '.$this->tableCustomer.'.pkey
                where
                    '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey);
        
        
        $rs = $this->oDbCon->doQuery($sql);

        $transactionCode = $rs[0]['code'];
        $email = $rs[0]['recipientemail']; // (!empty($rs[0]['email'])) ? $rs[0]['email'] : $rs[0]['recipientemail']; // kalo pake email satu lg, nanti gk konsisten dengna email di shopping cart
        $name =  $rs[0]['recipientname']; //(!empty($rs[0]['name'])) ? $rs[0]['name'] : $rs[0]['recipientname'];
        
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $name;
        $arrTwigVar['INVOICE_URL'] = $rs[0]['paymentgatewayinvoiceurl'];
        
        $twig->render('email-template.html');  
        $content = $twig->render('email-payment-instruction.html', $arrTwigVar);

        /*$this->setLog($this->lang['paymentInstruction'] . ' ' .$transactionCode.' - ' . DOMAIN_NAME);
        $this->setLog($email);*/
        
        $this->sendMail(array(), $this->lang['paymentInstruction'] . ' ' .$transactionCode.' - ' . DOMAIN_NAME,$content,array('email'=>$email)); 
 
    }
    
    function paymentGatewaySuccess($pkey){
        // deprecated, ganti ke paidstatuskey
        //try{  
        //        if(!$this->oDbCon->startTrans())
        //            throw new Exception($this->errorMsg[100]);
//
        //            $arrayToJs = array(); 
//
//
        //            $termOfPayment = new TermOfPayment();
        //            $rsTOP = $termOfPayment->searchDataRow(array($termOfPayment->tableName.'.pkey'),
        //                                                    ' and '.$termOfPayment->tableName.'.duedays > 0 ',
        //                                                    ' order by duedays asc limit 1'
        //                                                  );
        //    
        //            $topkey = (!empty($rsTOP)) ? $rsTOP[0]['pkey'] : 1;
        //    
        //            $sql = 'update ' .$this->tableName.' 
        //                    set paymentgatewaysuccess = 1, termofpaymentkey = '.$this->oDbCon->paramString($topkey).' 
        //                    where  
        //                    pkey = ' .  $this->oDbCon->paramString($pkey) ;
        //    
        //            $this->oDbCon->execute($sql); 
//
        //            $this->oDbCon->endTrans();
//
        //            $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
//
        //}catch (Exception $e){
        //    $this->oDbCon->rollback();
        //    $this->addErrorList($arrayToJs,false,$e->getMessage());    
        //}

    }
	
	
	  
    function getVoucherDetail($pkey,$voucherKey=''){
        $voucherKeyCriteria = '';
        if (!empty($voucherKey))
            $voucherKeyCriteria = ' and  '.$this->tableVoucherDetail.'.voucherkey = ' . $this->oDbCon->paramString($voucherKey);

        // gk boleh ambil typekey dr sini, karena bisa berbeda kalo sudah masuk ke admin, sudah jd voucher transaction / regular
        $sql = 'select 
                    '.$this->tableVoucherDetail.'.*,
                    '.$this->tableVoucher.'.name, 
                    '.$this->tableVoucher.'.categorykey, 
                    '.$this->tableVoucher.'.typekey, 
					'.$this->tableVoucherTransaction.'.code, 
					'.$this->tableVoucherTransaction.'.refvoucherkey
                from  
                   '.$this->tableVoucherDetail.',
                   '.$this->tableVoucher.', 
				   '.$this->tableVoucherTransaction.'
                where  
					'.$this->tableVoucherDetail.'.voucherkey =  '.$this->tableVoucherTransaction.'.pkey and
					'.$this->tableVoucherTransaction.'.refvoucherkey =  '.$this->tableVoucher.'.pkey and
                    '.$this->tableVoucherDetail.'.refkey in ('.$this->oDbCon->paramString($pkey,',').')
					'.$voucherKeyCriteria.'
				order by 
					 '.$this->tableVoucherDetail.'.voucherkey asc';	
		
        return $this->oDbCon->doQuery($sql); 
    } 
       
    function getReportPerItem($criteria = '',$order = ''){
        
        $rs = array();
        
        $sql = 'select
                    '.$this->tableName.'.pkey,
                    '.$this->tableName.'.code, 
                    '.$this->tableName.'.refcode, 
                    '.$this->tableWarehouse.'.name as warehousename, 
                    '.$this->tableEmployee.'.name as salesname, 
                    '.$this->tableCustomer.'.name as customername, 
                    '.$this->tableName.'.trdate, 
                    '.$this->tableItem.'.name as itemname, 
                    '.$this->tableItem.'.code as itemcode, 
                    '.$this->tableBrand.'.name as brandname, 
                    '.$this->tableNameDetail.'.qty, 
                    '.$this->tableNameDetail.'.priceinunit, 
                    '.$this->tableUnit.'.name as unitname,
                    '.$this->tableNameDetail.'.costinbaseunit, 
                    '.$this->tableNameDetail.'.profit as profitinbaseunit,
                    '.$this->tableNameDetail.'.total  as totalselling,
                    '.$this->tableNameDetail.'.costinbaseunit * '.$this->tableNameDetail.'.qtyinbaseunit as totalcogs,
                    '.$this->tableNameDetail.'.profit * '.$this->tableNameDetail.'.qty as profit,
                    '.$this->tableStatus.'.status as statusname
                from
                    '.$this->tableName.'
                        left join   '.$this->tableEmployee.' on  '.$this->tableName.'.saleskey =  '.$this->tableEmployee.'.pkey
                        left join   '.$this->tableCustomer.' on  '.$this->tableName.'.customerkey =  '.$this->tableCustomer.'.pkey,
                    '.$this->tableNameDetail.',
                    '.$this->tableItemUnit.',
                    '.$this->tableStatus.',
                    '.$this->tableWarehouse.',
                    '.$this->tableItem.'
                        left join   '.$this->tableBrand.' on  '.$this->tableItem.'.brandkey =  '.$this->tableBrand.'.pkey
                where
                    '.$this->tableName.'.pkey =  '.$this->tableNameDetail.'.refkey and
                    '.$this->tableNameDetail.'.itemkey =  '.$this->tableItem.'.pkey and
                    '.$this->tableNameDetail.'.unitkey =  '.$this->tableItemUnit.'.pkey and
                    '.$this->tableName.'.warehousekey =  '.$this->tableWarehouse.'.pkey and
                    '.$this->tableName.'.statuskey =  '.$this->tableStatus.'.pkey
                ';
        
        if(!empty($criteria))  $sql .= ' ' . $criteria;
        if(!empty($order))  $sql .= ' ' . $order;
         
        $rs = $this->oDbCon->doQuery($sql); 
        return $rs;
    }
    
    function getTransactionToken($rs){
        return md5($rs[0]['pkey'] . $rs[0]['grandtotal'] . $this->secretKey);
    }
    
    /*function getTotalQtyAndSelling($itemkey,$startDate = '', $endDate =''){
        
        $criteria = '';
        if(!empty($startDate)) $criteria .= ' and '.$this->tableName.'.trdate >= '. $this->oDbCon->paramDate($startDate);
        if(!empty($endDate)) $criteria .= ' and '.$this->tableName.'.trdate <= '. $this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59');
            
        $sql = 'select 
                    '.$this->tableNameDetail.'.refkey,
                    '.$this->tableNameDetail.'.itemkey,
                    SUM('.$this->tableNameDetail.'.qtyinbaseunit) as totalqty,
                    SUM('.$this->tableNameDetail.'.total) as totalselling 
                from
                    '.$this->tableNameDetail.', 
                    '.$this->tableName.' 
                where
                    '. $this->tableNameDetail .'.itemkey in ('.$this->oDbCon->paramString($itemkey,',').') and 
    			  	'.$this->tableName .'.pkey = '.$this->tableNameDetail.'.refkey and
                    '. $this->tableName.'.statuskey in (2,3) 
                    '.$criteria.'
                group by '.$this->tableNameDetail.'.itemkey
        ';
        
        $this->setLog($sql,true);
        $rs = $this->oDbCon->doQuery($sql);
            
        return $rs;
    }*/
    
    function convertNumAlpha($input) {
        // mapping arrays
        $numToAlpha = [
            '0' => 'a', '1' => 'b', '2' => 'c', '3' => 'd', '4' => 'e',
            '5' => 'f'
        ];
        //, '6' => 'g', '7' => 'h', '8' => 'i', '9' => 'j'
        $alphaToNum = array_flip($numToAlpha);

        $output = '';
        $chars = str_split(strtolower($input));

        foreach ($chars as $ch) {
            if (isset($numToAlpha[$ch])) {
                $output .= $numToAlpha[$ch];   // number → letter
            } elseif (isset($alphaToNum[$ch])) {
                $output .= $alphaToNum[$ch];   // letter → number
            } else {
                $output .= $ch; // keep other characters as-is
            }
        }

        return $output;
    }


    function getAvailabilityStatus($pkey = '')
    {
        $sql = '
            select
                '.$this->tableAvailabilityStatus.'.*
            from
                '.$this->tableAvailabilityStatus.'
            where
                1=1
        ';

        if(!empty($pkey)) {
            $sql .= ' and '.$this->tableAvailabilityStatus.'.pkey = '.$this->oDbCon->paramString($pkey).' '; 
        }

        $rs = $this->oDbCon->doQuery($sql); 

        return $rs;

    }

    function getSalesOrderTotalTransaction($startPeriod, $endPeriod, $criteria = '', $warehousekey = '')
    {
        $sql = '
            select
                date('.$this->tableName.'.trdate) as date,
                count(*) as totaltransaction,
                sum('.$this->tableName.'.grandtotal) as totalamount
            from
                '.$this->tableName.'
            where
                '.$this->tableName.'.statuskey in (2,3) and 
                trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and \''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\'
            ';

            if(!empty($criteria)) $sql .= $criteria;    
        
		    if (!empty($warehousekey))
				$sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';

        $sql .=  $this->getWarehouseCriteria() ;

        $sql .=  $this->getCompanyCriteria() ;

        $sql .= ' group by date('.$this->tableName.'.trdate) order by '.$this->tableName.'.trdate asc ';

        $result = $this->oDbCon->doQuery($sql);  
    
        return $result;
        
    }
    
    function getReportForAI($opt=array(), $writeToFile = true){
        
        $limitTopTier = 5;
        
        // summary semua laporan utk AI, bisa dipisah per index jenis analisa
        
        $availableReport = array('sales-by-warehouse','sales-by-customer','sales-by-salesperson','sales-by-item');
        
        // keluarin dulu yg tdk dicari
        if (isset($opt['reportType']) && !empty($opt['reportType']))
            $availableReport = array_intersect($availableReport, $opt['reportType']);
        
       
        define('CONS_START_PERIODE', (isset($opt['startPeriod']) && !empty($opt['startPeriod'])) ? $opt['startPeriod'] : 'January ' . date('Y')); 
        define('CONS_END_PERIODE', (isset($opt['endPeriod']) && !empty($opt['endPeriod'])) ? $opt['endPeriod'] : 'December ' . date('Y')); 
      
        $warehousekey = array();
        if  (isset($opt['selWarehouse']) && !empty($opt['selWarehouse'])){
            $warehousekey = $opt['selWarehouse'];
            if (!is_array($warehousekey)) $warehousekey = array($warehousekey);
        }
        
        define('CONS_WAREHOUSE',$warehousekey); 
  
        
        $basicSelect = 'select 
                        sum('.$this->tableNameDetail.'.total - ( '.$this->tableNameDetail.'.headerdiscvalueinunit * '.$this->tableNameDetail.'.qty )) as totalselling, 
                        '.$this->tableWarehouse.'.name as warehousename,
                        DATE_FORMAT('.$this->tableName.'.trdate, \'%Y-%m\') AS periodname';
        $basicJoin = ' from 
                        '.$this->tableName.', 
                        '.$this->tableNameDetail.',
                        '.$this->tableWarehouse.'
                    ';
        $basicWhere = ' where ' .$this->tableName.'.statuskey in (2,3)  and 
                            ' . $this->tableName.'.warehousekey =  '.$this->tableWarehouse.'.pkey and
                    '. $this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey';
        
        if (!empty(CONS_WAREHOUSE))
            $basicWhere.= ' and '. $this->tableName.'.warehousekey in (' .$this->oDbCon->paramString(CONS_WAREHOUSE,',').')' ;
        
        $basicWhere.=  $this->getWarehouseCriteria() ; // warehouse harus selalu ada
        
        $rsResult = array('data' => array());
        
        // laporan group per cabang per periode
        if (in_array('sales-by-warehouse',$availableReport)){
            // nanti bisa ditambahkan opsi criteria warehouse
            //$sql = $basicSQL . ' group by '.$this->tableName.'.warehousekey, YEAR('.$this->tableName.'.trdate),  MONTH('.$this->tableName.'.trdate)'; 
 
            $sql = $basicSelect . ' '.
                   $basicJoin . ' '.
                   $basicWhere . ' '.
                    ' and '  . $this->tableName.'.trdate between \''. date("Y-m-d", strtotime(CONS_START_PERIODE)) .'\' and \''. date("Y-m-t 23:59", strtotime(CONS_END_PERIODE)) .'\' 
                   group by '.$this->tableName.'.warehousekey, YEAR('.$this->tableName.'.trdate),  MONTH('.$this->tableName.'.trdate)'; 
           
            $this->setLog($sql,true);
            
            $rs = $this->oDbCon->doQuery($sql);
            
            $rsData = array();
            foreach($rs as $row){
                array_push($rsData, array('Cabang' => $row['warehousename'],
                                        'Periode' =>  date("M Y", strtotime($row['periodname'])),
                                        'Total Penjualan' => $row['totalselling']
                                         ));
            }
            
            $rsResult['data']['salesByWarehouse']  = $rsData;
        }
        
        
        
        // laporan group per customer terbesar per bln,  per periode
        // setiap bulan bisa beda customernya, jd harus diloop
        if (in_array('sales-by-customer',$availableReport)){
             
            
            $start = new DateTime(CONS_START_PERIODE);
            $end   = new DateTime(CONS_END_PERIODE);
             
            $limit =  (isset($opt['limitTop']) && !empty($opt['limitTop'])) ? $opt['limitTop'] : $limitTopTier; 
            if(!is_numeric($limit)) $limit = $limitTopTier;
            
            $rsData = array();
            
            while ($start <= $end) {
               
                $startPeriod =  $start->format('Y-m-1');
                $endPeriod =  $start->format('Y-m-t 23:59');

                $sql = $basicSelect . ', '. $this->tableCustomer.'.name as customername'.
                       $basicJoin . ', '.$this->tableCustomer.
                       $basicWhere . ' and '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
                                        and '  . $this->tableName.'.trdate between '. $this->oDbCon->paramString($startPeriod) .' and   '. $this->oDbCon->paramString($endPeriod) .' 
                       group by '.$this->tableName.'.customerkey 
                       order by totalselling desc
                       limit ' . $limit;  
 
                $rs = $this->oDbCon->doQuery($sql);


                foreach($rs as $row){
                    array_push($rsData, array('Periode' =>  $start->format('M Y'),
                                            'Nama Pelanggan' => $row['customername'],
                                            'Total Penjualan' => $row['totalselling']
                                             ));
                }
                
                $rsResult['data']['salesByCustomer']  = $rsData;
                
                $start->modify('+1 month');
            }
            
           
        }
        
        
          
        // laporan group per sales terbesar per bln,  per periode
        // setiap bulan bisa beda sales nya, jd harus diloop
        if (in_array('sales-by-salesperson',$availableReport)){
             
            $start = new DateTime(CONS_START_PERIODE);
            $end   = new DateTime(CONS_END_PERIODE);
             
            
            if(!is_numeric($opt['limitTop'])) 
                $limit = $limitTopTier;
            else
                $limit =  (isset($opt['limitTop']) && !empty($opt['limitTop'])) ? $opt['limitTop'] : $limitTopTier; 
                
            
            $rsData = array();
            while ($start <= $end) {
               
                $startPeriod =  $start->format('Y-m-1');
                $endPeriod =  $start->format('Y-m-t 23:59');

                $sql = $basicSelect . ', '. $this->tableEmployee.'.name as salespersonname'.
                       $basicJoin . ', '.$this->tableEmployee.
                       $basicWhere . ' and '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey
                                        and '  . $this->tableName.'.trdate between '. $this->oDbCon->paramString($startPeriod) .' and   '. $this->oDbCon->paramString($endPeriod) .' 
                       group by '.$this->tableName.'.saleskey 
                       order by totalselling desc
                       limit ' . $limit;  
 
                //$this->setLog($sql,true);
                $rs = $this->oDbCon->doQuery($sql);

                foreach($rs as $row){
                    array_push($rsData, array('Periode' =>  $start->format('M Y'),
                                            'Nama Sales' => $row['salespersonname'],
                                            'Total Penjualan' => $row['totalselling']
                                             ));
                }
                
                $rsResult['data']['salesBySalesperson']  = $rsData;
                
                $start->modify('+1 month');
            }
            
           
        }
        
                
          
        // laporan group per item terbesar per bln,  per periode
        // setiap bulan bisa beda sales nya, jd harus diloop
        if (in_array('sales-by-item',$availableReport)){
             
            $start = new DateTime(CONS_START_PERIODE);
            $end   = new DateTime(CONS_END_PERIODE);
             
            
            if(!is_numeric($opt['limitTop'])) 
                $limit = $limitTopTier;
            else
                $limit =  (isset($opt['limitTop']) && !empty($opt['limitTop'])) ? $opt['limitTop'] : $limitTopTier; 
                
            
            $rsData = array();
            while ($start <= $end) {
               
                $startPeriod =  $start->format('Y-m-1');
                $endPeriod =  $start->format('Y-m-t 23:59');

                $sql = $basicSelect . ', '. $this->tableItem.'.name as itemname'.
                       $basicJoin . ', '.$this->tableItem.
                       $basicWhere . ' and '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey
                                        and '  . $this->tableName.'.trdate between '. $this->oDbCon->paramString($startPeriod) .' and   '. $this->oDbCon->paramString($endPeriod) .' 
                       group by '.$this->tableNameDetail.'.itemkey 
                       order by totalselling desc
                       limit ' . $limit;  
  
                $rs = $this->oDbCon->doQuery($sql);

                foreach($rs as $row){
                    array_push($rsData, array('Periode' =>  $start->format('M Y'),
                                            'Nama Item' => $row['itemname'],
                                            'Total Penjualan' => $row['totalselling']
                                             ));
                }
                
                $rsResult['data']['salesByItem']  = $rsData;
                
                $start->modify('+1 month');
            }
            
           
        }
        
        
        if($writeToFile){
            $path = $this->uploadTempDoc.'data-ai/';  
            if (!is_dir($path)) mkdir ($path,  0755, true);
            
            $fileDataName = time().$this->userkey.rand();
            $rsResult['fileData'] = $fileDataName;
            $rsResult['action'] = 'salesOrderSummary';
            $rsResult['companyType'] = PLAN_TYPE['categorykey'];
            $rsResult['companyName'] = $this->loadSetting('companyName'); 
            $rsResult['period'] = $startPeriod .' s/d '. $endPeriod;
            
            $fileDataPath = fopen($path.$fileDataName, "w");  
            fwrite($fileDataPath, json_encode($rsResult)); 
            fclose($fileDataPath);
        }
        

        return $rsResult;
    }
        
}
?>