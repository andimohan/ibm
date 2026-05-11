<?php
  
class EMKLJobOrder extends BaseClass{ 
 
    function __construct($jobType = ''){
		
		parent::__construct();
       
		$this->tableName = 'emkl_job_order_header';
		$this->tableNameDetail = 'emkl_job_order_detail';
		$this->tableNameDetailItem = 'emkl_job_order_detail_item';
		$this->tableVolumeDetail = 'emkl_job_order_detail_volume';
		$this->tableContainerDetail = 'emkl_job_order_detail_container';
        $this->tableCommodityDetail = 'emkl_job_order_detail_commodity';
		$this->tableHBL = 'emkl_hbl';
		//$this->tableNameDetailCommission = 'emkl_job_order_detail_commission';
		$this->tableJOHeader = 'emkl_order_header';
		$this->tablePurchase = 'emkl_purchase_order_header';
		$this->tableCommission = 'emkl_commission_header';
		$this->tableEmployeeCommission = 'ap_employee_commission';
		$this->tableStatus = 'transaction_status';
		$this->tableSupplier = 'supplier';
		$this->tablePort = 'port';
        $this->tableBillType = 'emkl_bill_type';
        $this->tableLocation = 'location'; 
		$this->tableItem = 'item';
		$this->tableItemUnit = 'item_unit';
        $this->tableConsignee = 'consignee';
        $this->tableCustomer = 'customer';
        $this->tableContainerType = 'container_type';
        $this->tableContact = 'contact_person';
        $this->tableJobType = 'emkl_import_export';
        $this->tableTransportationType = 'emkl_air_sea';
        $this->tableLoadContainer = 'emkl_fcl_lcl';
        $this->tableVolumeUnit = 'emkl_volume_unit';
        $this->tableFreightTerm = 'emkl_freight_term';
        $this->tableWarehouse = 'warehouse';
        $this->tableDepot = 'depot';
	    $this->tableTerminal = 'terminal'; 
        $this->tableCommodity = 'commodity';  
        $this->tableContainer = 'container';
        $this->tableCurrency = 'currency';
        $this->tableCity = 'city';
        $this->tableEmployee = 'employee'; 
	    $this->tableVessel = 'vessel';
        $this->tablePartialInvoice = 'emkl_job_order_header_partial_invoice';
        $this->tableInvoiceHeader = 'emkl_order_invoice_header';
        $this->tableInvoiceDetail = 'emkl_order_invoice_detail'; 
        $this->tableARPaymentHeader = 'ar_payment_header';
        $this->tableARPaymentDetail = 'ar_payment_detail';
        $this->tableIncoterms = 'incoterms';
        $this->tableAR = 'ar';
        $this->tableShipmentTerm = 'shipment_term';
        $this->tableShipmentType = 'shipment_type'; 
        $this->tableCountry = 'country';
        $this->tableServiceCategory = 'service_category';
        $this->tableContinent = 'continent';
		$this->tableTermOfPayment  = 'term_of_payment';
		
		// tableEmployeeCommission sudah digunakan diatas
		$this->tableEmployeeCommissionRequestHeader = 'employee_commission_header';
		$this->tableEmployeeCommissionRequestDetail = 'employee_commission_detail';
		
		$this->securityObject = 'EMKLJobOrder';
        $this->tableFile = 'emkl_job_order_file'; 
        $this->uploadFileFolder = 'emkl-job-order/';
        $this->isTransaction = true;
        $this->jobType = $jobType;
        $this->isActiveMasterHBL = $this->isActiveModule('emklhousebl'); // biarins aja dulu dipisah
		$this->activeModule = $this->isActiveModule(array('activityProgress','employeeCommission'));
        $this->allowedStatusForEdit = array(1,2);
        
//        $this->tableFile = 'emkl_job_order_header_file';
//		$this->uploadFileFolder = 'emkl-job-order-file/';
		
        $this->importUrl = 'import/FFJobOrderExport';
        
        $this->useStorage = $this->useStorage('S3');
        
        $this->arrItem = array();  
        $this->arrItem['pkey'] = array('hidDetailItemKey');
        $this->arrItem['refkey'] = array('hidDetailKey','ref');  
        $this->arrItem['refheaderkey'] = array('pkey','ref');  
        $this->arrItem['isreimburse'] = array('chkIsReimburse');
        $this->arrItem['itemkey'] = array('hidContainerDetailKey'); 
        $this->arrItem['unitkey'] = array('selDetailItemUnit'); 
        $this->arrItem['servicekey'] = array('hidServiceKey'); 
        $this->arrItem['qty'] = array('qty','number');
        $this->arrItem['priceinunit'] = array('priceInUnit','number'); 
        $this->arrItem['taxdetail'] = array('taxDetail', 'number');
        $this->arrItem['taxdetailvalue'] = array('taxDetailValue', 'number');
        $this->arrItem['beforetaxdetailvalue'] = array('beforeTaxDetail', 'number');
        $this->arrItem['aftertaxdetailvalue'] = array('afterTaxDetail', 'number');
        $this->arrItem['ispriceincludetax'] = array('chkIncludeTaxDetail');
        $this->arrItem['subtotal'] = array('detailRowSubtotal','number'); 
        $this->arrItem['subtotalcurrency'] = array('detailRowCurrencySubtotal','number'); 
 	    $this->arrItem['currencykey'] = array('selCurrencyDetail'); 
        $this->arrItem['isvat'] = array('isVat');
        $this->arrItem['trdesc'] = array('descriptionDetail');
        $this->arrItem['alias'] = array('itemNameAliasDetail');
     
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrItem, 'tableName' => $this->tableNameDetailItem)); 
         
        $this->arrDataDetail = array();   
        $this->arrDataDetail['pkey'] = array('hidDetailKey', array('dataDetail' =>  $arrDetails));
        $this->arrDataDetail['refkey'] = array('pkey','ref'); 
        $this->arrDataDetail['code'] = array('salesOrderCode'); 
        $this->arrDataDetail['hbl'] = array('detailHBL');
        $this->arrDataDetail['saleskey'] = array('hidSalesDetailKey');
        $this->arrDataDetail['customerkey'] = array('hidCustomerDetailKey', array('mandatory'=>true)); 
        $this->arrDataDetail['freighttermkey'] = array('selSellingFreightTerm'); 
        $this->arrDataDetail['currencykey'] = array('selSellingCurrency');
        $this->arrDataDetail['qty'] = array('detailQty','number');
        $this->arrDataDetail['unitkey'] = array('detailSelUnit');
        $this->arrDataDetail['weight'] = array('detailWeight','number');
        $this->arrDataDetail['measurement'] = array('detailMeasurement','number');
        //$this->arrDataDetail['destinationkey'] = array('hidDestinationDetailKey');
        $this->arrDataDetail['description'] = array('detailDescription');
        $this->arrDataDetail['rate'] = array('sellingCurrencyRate','number');
        $this->arrDataDetail['subtotal'] = array('detailTotal','number'); 
        $this->arrDataDetail['subtotalcurrency'] = array('detailCurrencyTotal','number'); 
        $this->arrDataDetail['subtotalothercurrency'] = array('detailOtherCurrencyTotal','number');
        $this->arrDataDetail['podkey'] = array('hidDetailPODKey');
        $this->arrDataDetail['hblkey'] = array('hidDetailHBLKey');
        $this->arrDataDetail['ismanual'] = array('chkIsManual');
        $this->arrDataDetail['refdetailhbl'] = array('refDetailHBL');
					  

        $this->arrDataDetail['grossweight'] = array('detailGrossWeight', 'number');
        $this->arrDataDetail['chargeweight'] = array('detailChargeWeight', 'number');
        $this->arrDataDetail['length'] = array('detailLength', 'number');
        $this->arrDataDetail['width'] = array('detailWidth', 'number');
        $this->arrDataDetail['height'] = array('detailHeight', 'number');
        $this->arrDataDetail['meas'] = array('detailMeas', 'number');
					
        $this->arrVolumeDetail = array(); 
        $this->arrVolumeDetail['pkey'] = array('hidDetailVolumeKey');
        $this->arrVolumeDetail['refkey'] = array('pkey','ref');
        $this->arrVolumeDetail['itemkey'] = array('selContainerDetailVolumeKey');
        $this->arrVolumeDetail['qty'] = array('qtyVolume','number');
        

        $this->arrContainerDetail = array(); 
        $this->arrContainerDetail['pkey'] = array('hidDetailContainerKey');
        $this->arrContainerDetail['refkey'] = array('pkey','ref');
        $this->arrContainerDetail['containerno'] = array('containerNo');
        $this->arrContainerDetail['sealno'] = array('sealNo');
        $this->arrContainerDetail['unitkey'] = array('selUnit');
        $this->arrContainerDetail['qty'] = array('qtyContainer',array('datatype' => 'number'));
        $this->arrContainerDetail['weight'] = array('weightContainer',array('datatype' => 'number'));
        $this->arrContainerDetail['volume'] = array('volumeContainer',array('datatype' => 'number'));
        $this->arrContainerDetail['deliveryaddress'] = array('deliveryAddress');
        
        $this->arrContainerDetail['grossweight'] = array('detailGrossWeight', 'number');
        $this->arrContainerDetail['chargeweight'] = array('detailChargeWeight', 'number');
        $this->arrContainerDetail['meas'] = array('detailMeas', 'number');
        $this->arrContainerDetail['netweight'] = array('detailNetWeight', 'number');
        $this->arrContainerDetail['typekey'] = array('selContainerTypeDetail');

        $this->arrCommodityDetail = array(); 
        $this->arrCommodityDetail['pkey'] = array('hidDetailCommodityKey');
        $this->arrCommodityDetail['refkey'] = array('pkey', 'ref');
        $this->arrCommodityDetail['commoditykey'] = array('hidCommodityKey'); 
        
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail)); 
        array_push($arrDetails, array('dataset' => $this->arrVolumeDetail, 'tableName' => $this->tableVolumeDetail));     
        array_push($arrDetails, array('dataset' => $this->arrContainerDetail, 'tableName' => $this->tableContainerDetail)); 
        array_push($arrDetails, array('dataset' => $this->arrCommodityDetail, 'tableName' => $this->tableCommodityDetail));    
        
        
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
        //$this->arrData['pkey'] = array('pkey');
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['codectr'] = array('codectr');
        $this->arrData['trdate'] = array('trDate','date');  
        $this->arrData['headerorderkey'] = array('hidHeaderOrderKey');
        $this->arrData['refkey'] = array('hidJobOrderKey'); // utk LCL yg punya master
        $this->arrData['consigneekey'] = array('hidConsigneeKey');
        $this->arrData['supplierkey'] = array('selSupplier');
        $this->arrData['carrierkey'] = array('hidCarrierKey');
        $this->arrData['terminalkey'] = array('hidTerminalKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['quotationkey'] = array('hidQuotationKey');
        $this->arrData['quotationnumber'] = array('quotationNumber');
        $this->arrData['ponumber'] = array('poNumber');
        $this->arrData['jobtypekey'] = array('selTypeOfJob');
        $this->arrData['transportationtypekey'] = array('selAirSea');
        $this->arrData['loadcontainertypekey'] = array('selContainerType');
        $this->arrData['itemkey'] = array('hidContainerKey');
        $this->arrData['volume'] = array('volume', 'number');
        $this->arrData['weight'] = array('weight','number');     
        $this->arrData['volumetype'] = array('selVolumeType');
        $this->arrData['saleskey'] = array('hidSalesKey');
        $this->arrData['agentkey'] = array('hidAgentKey');
        $this->arrData['mblnumber'] = array('mblNumber'); 
        $this->arrData['polkey'] = array('hidPOLKey');
        $this->arrData['podkey'] = array('hidPODKey');
        $this->arrData['etdpol'] = array('etdPol','date');
        $this->arrData['etapod'] = array('etaPod','date');
        $this->arrData['closingdate'] = array('closingDate','date'); 
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['bookingnumber'] = array('bookingNumber');
        $this->arrData['containernumber'] = array('containerNumber');
        $this->arrData['depotkey'] = array('hidDepotKey');
        $this->arrData['vesselkey'] = array('hidVesselKey');
        $this->arrData['vesselnumber'] = array('vesselNumber');
        $this->arrData['feederkey'] = array('hidFeederKey');
        $this->arrData['feedernumber'] = array('feederNumber');
        $this->arrData['statuskey'] = array('selStatus');
	    $this->arrData['ismaster'] = array('chkIsMaster');    
        $this->arrData['totalselling'] = array('totalSelling','number');
        $this->arrData['taxvalue'] = array('taxValue','number');
        $this->arrData['totalbuying'] = array('totalBuying','number');
        $this->arrData['totalcommission'] = array('totalCommission','number');
        // ad bug, gk ke save karena case sensitive dulu, tp kalo mau save grand total, currencynya yg bingung mau pake yg mana
        // kayanya skrg pake totalselling
        //$this->arrData['grandtotal'] = array('grandtotal','number'); 
	    //$this->arrData['locationkey'] = array('hidLocationKey');    
	    $this->arrData['aju'] = array('aju');    
        $this->arrData['peb'] = array('peb');    
        $this->arrData['stuffinglocation'] = array('stuffingLocation');
        $this->arrData['stuffingin'] = array('stuffingIn','date');
        $this->arrData['stuffingout'] = array('stuffingOut','date');
        $this->arrData['truckingkey'] = array('hidTruckingSupplierKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['customercache'] = array('customercache'); 
        $this->arrData['containertypekey'] = array('hidCargoType');
        $this->arrData['invoicetokey'] = array('hidInvoiceToKey');
        $this->arrData['itemdescription'] = array('itemDescription');
        
        //FROM BARU UPDATE variable ini yang ada di form semua mungkin ada beberapa saya komen karena takut ambil dari TMS.
        $this->arrData['consigneename'] = array('consigneeName');
        
        $this->arrData['datedoc'] = array('dateDoc','date'); 
        $this->arrData['originaldate'] = array('originalDate','date');
        $this->arrData['invoicedate'] = array('invoiceDate','date');
        $this->arrData['packingdate'] = array('packingDate','date');
        $this->arrData['insurancedate'] = array('insuranceDate','date');
        $this->arrData['formdate'] = array('formDEADate','date');
        $this->arrData['procurationdate'] = array('procurationDate','date');
        $this->arrData['procurationpabeandate'] = array('procurationPabeanDate','date');
        $this->arrData['procurationdodate'] = array('procurationDoDate','date');
        $this->arrData['lsdate'] = array('lsDate','date');
        //$this->arrData['others'] = array('others','date');
        
        $this->arrData['pabean'] = array('pabean','number');
        $this->arrData['incoterm'] = array('incoterm');
        $this->arrData['lartas'] = array('lartas');
        $this->arrData['qtypack'] = array('qtyPack');
        $this->arrData['transferpibdate'] = array('transferPIBDate','date');
        $this->arrData['response'] = array('response');
        $this->arrData['sppbdate'] = array('sppbDate','date');
        
        $this->arrData['admdodate'] = array('admDODate','date');
        $this->arrData['thcdate'] = array('thcDate','date');
        $this->arrData['liftoffdate'] = array('liftOffDate','date');
        $this->arrData['liftondate'] = array('liftOnDate','date');
        $this->arrData['agencydate'] = array('agencyDate','date');
        $this->arrData['mechanicdate'] = array('mechanicDate','date');
        $this->arrData['demurragedate'] = array('demurrageDate','date');
        $this->arrData['dodate'] = array('doDate','date');
        $this->arrData['deliverypibdate'] = array('deliveryPIBdate','date');
        $this->arrData['depotkey'] = array('hidDepotKey');
        $this->arrData['terminalkey'] = array('hidTerminalKey');
        
        
        $this->arrData['sidate'] = array('siDate','date');
        $this->arrData['bookingdate'] = array('bookingDate','date');
        $this->arrData['npedate'] = array('npeDate','date');
        $this->arrData['deliverynpedate'] = array('deliveryNPEDate','date');
        $this->arrData['exportcarddate'] = array('exportCardDate','date');
        $this->arrData['istrucking'] = array('chkIsTrucking');
        
        $this->arrData['placeofdeliverykey'] = array('hidPlaceOfDeliveryKey');
        $this->arrData['placeofreceiptkey'] = array('hidPlaceOfReceiptKey');
        
        //shipping instruction
        $this->arrData['placeofissuekey'] = array('hidPlaceOfIssueKey');
        $this->arrData['sishipperkey'] = array('hidSIShipperKey');
        $this->arrData['siconsigneename'] = array('siConsigneeName');
        $this->arrData['siconsigneeaddress'] = array('siConsigneeAddress');
	    $this->arrData['notifykey'] = array('hidNotifyKey');
	    $this->arrData['notifykey2'] = array('hidNotifyKey2');
	    $this->arrData['billtypekey'] = array('selBillType');
	    $this->arrData['freighttermkey'] = array('selFreightTerm');
	    $this->arrData['hscode'] = array('hsCode');
	    $this->arrData['kpbc'] = array('kpbc');       
	    $this->arrData['attachment'] = array('attachment');     
	    $this->arrData['flag'] = array('flag');       
		$this->arrData['totalemployeecommission'] = array('totalEmployeeCommission');
		

		$this->arrData['pibregistrationnumber'] = array('pibRegistrationNumber');
		$this->arrData['pibregistrationdate'] = array('pibRegistrationDate', 'date');
				
        $this->arrData['shipmenttermkey'] = array('selShipmentTerm');    
        $this->arrData['shipmentterm2key'] = array('selShipmentTerm2');  
        $this->arrData['finaldestinationkey'] = array('hidFinalDestinationKey');  
		$this->arrData['connectingvesselkey'] = array('hidConnectingVesselKey');
		$this->arrData['connectingvessel2key'] = array('hidConnectingVessel2Key');
        $this->arrData['connectingvessel3key'] = array('hidConnectingVessel3Key');
        $this->arrData['connectingvesselnumber'] = array('connectingVesselNumber');
        $this->arrData['connectingvessel2number'] = array('connectingVessel2Number');
        $this->arrData['connectingvessel3number'] = array('connectingVessel3Number');
        $this->arrData['connectingvessel3number'] = array('connectingVessel3Number');
        $this->arrData['freighttermkey'] = array('selFreightTerm');
        $this->arrData['freightterm2key'] = array('selFreightTerm2');
        $this->arrData['shipmenttypekey'] = array('selShipmentType');
        $this->arrData['connectingcountrykey'] = array('hidConnectingCountryKey');
		$this->arrData['connectingcountry2key'] = array('hidConnectingCountry2Key');
        $this->arrData['connectingcountry3key'] = array('hidConnectingCountry3Key');
        $this->arrData['servicecontract'] = array('serviceContract');
		
        $this->arrData['isoverwritenotifyparty'] = array('chkIsOverwriteNotifyParty');
        $this->arrData['notifypartykey'] = array('hidNotifyPartyKey');
        $this->arrData['notifypartyname'] = array('notifyPartyName1');
        $this->arrData['notifypartyaddress'] = array('notifyPartyAddress1');
        $this->arrData['alsonotifyparty'] = array('alsoNotifyParty');
	
        $this->arrData['qty'] = array('qtyHeader', 'number');
        $this->arrData['unitkey'] = array('selUnitKey');
        $this->arrData['weightqty'] = array('weightQty', 'number');
		$this->arrData['measurement'] = array('measurement', 'number');
        $this->arrData['reftemplatekey'] = array('hidTemplateKey');
        $this->arrData['incotermskey'] = array('selIncoterms');  
        $this->arrData['iscommissionpaid'] = array('isCommissionPaid');
       
        
        
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'containertype','title' => 'type','dbfield' => 'containertype', 'default'=>true,'width' => 60));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etdpol','title' => 'etd','dbfield' => 'etdpol','default'=>true, 'width' => 80,'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etapod','title' => 'eta','dbfield' => 'etapod','default'=>true, 'width' => 80,'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'invoiceTo','dbfield' => 'customerinvoicename','width' => 250));
        
 
        // kalo export / import beda
        if(EMKL['jobType']['import'] == $this->jobType)
            array_push($this->arrDataListAvailableColumn, array('code' => 'shipper','title' => 'consignee','dbfield' => 'customername','default'=>true,'width' => 250)); 
        else
            array_push($this->arrDataListAvailableColumn, array('code' => 'shipper','title' => 'shipper','dbfield' => 'customername','default'=>true,'width' => 250)); 

        array_push($this->arrDataListAvailableColumn, array('code' => 'carrier','title' => 'carrier','dbfield' => 'carriername', 'width' => 200)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'pod','title' => 'pod','dbfield' => 'podname','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'pol','title' => 'pol','dbfield' => 'polname','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'finaldestinationname','title' => 'finalDestination','dbfield' => 'finaldestinationname', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobType','title' => 'jobType','dbfield' => 'jobtypeunion','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'salesman','title' => 'salesman','dbfield' => 'salesname','width' => 150));
    	array_push($this->arrDataListAvailableColumn, array('code' => 'mbl','title' => 'mbl','dbfield' => 'mblnumber','width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'createdBy','title' => 'createdBy','dbfield' => 'createdbyname','width' => 150));
        
		
		 
		$this->arrSearchColumn = array ();
		array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
		array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));  
		array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer.'.name')); 
		array_push($this->arrSearchColumn, array('Pelanggan', 'customer_invoice.name')); 
		array_push($this->arrSearchColumn, array('Pelanggan', $this->tableName. '.customercache')); 
		array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableJobType. '.name')); 
		array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableTransportationType. '.name')); 
		array_push($this->arrSearchColumn, array(ucwords($this->lang['jobType']), $this->tableLoadContainer. '.name')); 
		array_push($this->arrSearchColumn, array(ucwords($this->lang['volume']), $this->tableName. '.volume')); 
		array_push($this->arrSearchColumn, array(ucwords($this->lang['unit']), $this->tableVolumeUnit. '.name')); 
		array_push($this->arrSearchColumn, array('Sales', $this->tableEmployee. '.name'));
		array_push($this->arrSearchColumn, array('Catatan', $this->tableName. '.trdesc'));
		array_push($this->arrSearchColumn, array('POL', 'pol.name'));
		array_push($this->arrSearchColumn, array('POD', 'pod.name'));
		array_push($this->arrSearchColumn, array('MBL', $this->tableName.'.mblnumber'));
		array_push($this->arrSearchColumn, array('AJU',  $this->tableName. '.aju'));
		array_push($this->arrSearchColumn, array('PEB',  $this->tableName. '.peb'));
		array_push($this->arrSearchColumn, array('Container',  $this->tableName. '.containernumber'));
		array_push($this->arrSearchColumn, array('Container Type',  $this->tableContainerType. '.name'));
		array_push($this->arrSearchColumn, array('Carrier', 'carrier.name')); 
		array_push($this->arrSearchColumn, array(ucwords($this->lang['createdBy']), 'created.name')); 
		array_push($this->arrSearchColumn, array($this->lang['quotation'],  $this->tableName.'.quotationnumber')); 



        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        array_push($this->filterCriteria, array('title' => $this->lang['containerType'], 'field' => 'containertypekey', 'table' => $this->tableContainerType));
        
        $this->printMenu = array();  
 
		switch($this->jobType){
			case EMKL['jobType']['import'] : $printUrl = 'print/emklJobOrderImport'; break;
			case EMKL['jobType']['export'] : $printUrl = 'print/emklJobOrderExport'; break;
			case EMKL['jobType']['domestic'] : $printUrl = 'print/emklJobOrderDomestic'; break;
			case EMKL['jobType']['warehouse'] : $printUrl = 'print/emklJobOrderWarehouse'; break;
			case EMKL['jobType']['trucking'] : $printUrl = 'print/emklJobOrderTrucking'; break;
			default : $printUrl =  'print/emklJobOrderImport';
		}
		
		
		array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => $printUrl)); 
		
        $this->includeClassDependencies(array(
              'Port.class.php',
              'Container.class.php',
              'Customer.class.php',
              'Warehouse.class.php',
              'ItemUnit.class.php',
              'Item.class.php', 
              'City.class.php',
              'EMKLPurchaseOrder.class.php',
              'Consignee.class.php',
              'Vessel.class.php',
              'Terminal.class.php',
              'Supplier.class.php',
              'EMKLOrderInvoice.class.php',
              'EMKLHouseBL.class.php',
              'EMKLJobOrderHeader.class.php',
              'Currency.class.php',
              'Service.class.php',
              'Depot.class.php',
              'Consignee.class.php',
              'City.class.php',
              'GeneralJournal.class.php',
              'Downpayment.class.php', // utk cancel JO
              'CustomerDownpayment.class.php',
              'CreditNote.class.php',
              'DebitNote.class.php',
              'InvoiceTax.class.php',
			  'EMKLCommission.class.php',
			  'EMKLQuotationOrder.class.php',
              'EMKLOrderInvoice.class.php',
			  'APEmployeeCommission.class.php',
              'ActivityProgress.class.php',
              'TemplateActivity.class.php',
              'ServiceCategory.class.php',
             'Country.class.php',
              'TemplateEMKLJobOrder.class.php',
              'CostReconsile.class.php',
              'PrepaidExpense.class.php',
              'AR.class.php',
              'ARPayment.class.php',
              'ARPrepaidTax23.class.php'  
        ));

		if(defined('PARTNER_ACCOUNT') && !empty(PARTNER_ACCOUNT['TMS'])){ 
			$this->includeClassDependencies(array( 
				  'Consginee.class.php',
				  'Supplier.class.php',
				  'Service.class.php',
				  'Container.class.php',
				  'TruckingServiceOrder.class.php'
			));
		}

        $this->overwriteConfig();
            
   }
   
    function getQuery(){
	   
        $sql = '
			SELECT
              '.$this->tableName.'.* ,
              ('.$this->tableName.'.totalselling - '.$this->tableName.'.totalbuying - '.$this->tableName.'.totalemployeecommission - '.$this->tableName.'.totalcommission - '.$this->tableName.'.totalcreditnote + '.$this->tableName.'.totaldebitnote) as grossprofit ,
              '.$this->tableCustomer.'.name as customername,
              '.$this->tableCustomer.'.address as customeraddress,
              '.$this->tableName.'.customercache as customercachename, 
              '.$this->tableWarehouse.'.name as warehousename,
              '.$this->tableEmployee.'.name as salesname,
			  '.$this->tableStatus.'.status as statusname ,
			  '.$this->tableJobType.'.name as jobtype ,
			  '.$this->tableTransportationType.'.name as transportationtype,
              '.$this->tableLoadContainer.'.name as loadcontainertype,
			  '.$this->tableContainer.'.name as containername ,
			  '.$this->tableVessel.'.name as vesselname ,
			  feeder_vessel.name as feedervesselname ,
			  '.$this->tableLocation.'.name as locationname ,
              customer_invoice.name as customerinvoicename,
              pol.name as polname,
              pod.name as podname,  
              podelivery.name as placeofdeliveryname,
              poreceipt.name as placeofreceiptname,
              carrier.name as carriername, 
              carrier.alias as carrieraliasname, 
              agent.name as agentname,
              agent.address as agentaddress,
              '.$this->tableDepot.'.name as depotname,
              '.$this->tableTerminal.'.name as terminalname,
              concat_ws(", ",'.$this->tableJobType.'.name,'.$this->tableTransportationType.'.name,'.$this->tableLoadContainer.'.name) as jobtypeunion,
              '.$this->tableVolumeUnit.'.name as volumeunit,
             '.$this->tableContainerType.'.name as containertype,
             '.$this->tableCity.'.name as finaldestinationname,
             created.name as createdbyname,
             connecting_vessel.name as connectingvesselname,
             connecting_vessel2.name as connectingvessel2name ,
			'.$this->tableTermOfPayment.'.name as topname
			FROM '.$this->tableStatus.',
                 '.$this->tableName.'
                    left join '.$this->tableEmployee.' on  '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey 
                    left join '.$this->tableEmployee.' created on  '.$this->tableName.'.createdby = created.pkey   
                    left join '.$this->tableContainer.' on  '.$this->tableName.'.itemkey = '.$this->tableContainer.'.pkey 
                    left join '.$this->tablePort.' pol on  '.$this->tableName.'.polkey = pol.pkey 
                    left join '.$this->tablePort.' pod on  '.$this->tableName.'.podkey = pod.pkey
                    left join '.$this->tablePort.' podelivery on  '.$this->tableName.'.placeofdeliverykey = podelivery.pkey
                    left join '.$this->tablePort.' poreceipt on  '.$this->tableName.'.placeofreceiptkey = poreceipt.pkey
                    left join '.$this->tableSupplier.' carrier on  '.$this->tableName.'.carrierkey = carrier.pkey
                    left join '.$this->tableVessel.' on  '.$this->tableName.'.vesselkey = '.$this->tableVessel.'.pkey 
                    left join '.$this->tableVessel.' feeder_vessel on  '.$this->tableName.'.feederkey = feeder_vessel.pkey 
                    left join '.$this->tableVessel.' connecting_vessel on  '.$this->tableName.'.connectingvesselkey = connecting_vessel.pkey 
                    left join '.$this->tableVessel.' connecting_vessel2 on  '.$this->tableName.'.connectingvessel2key = connecting_vessel2.pkey 
                    left join '.$this->tableCustomer.' agent on  '.$this->tableName.'.agentkey = agent.pkey 
                    left join '.$this->tableDepot.' on  '.$this->tableName.'.depotkey = '.$this->tableDepot.'.pkey 
                    left join '.$this->tableTerminal.' on  '.$this->tableName.'.terminalkey = '.$this->tableTerminal.'.pkey 
                    left join '.$this->tableLocation.' on  '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey 
                    left join '.$this->tableCustomer.'  on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tableTermOfPayment.'  on '.$this->tableCustomer.'.termofpaymentkey = '.$this->tableTermOfPayment.'.pkey
                    left join '.$this->tableCustomer.' customer_invoice on '.$this->tableName.'.invoicetokey = customer_invoice.pkey 
                    left join '.$this->tableContainerType.' on  '.$this->tableName.'.containertypekey = '.$this->tableContainerType.'.pkey  
                    left join '.$this->tableCity.' on '.$this->tableName.'.finaldestinationkey = '.$this->tableCity.'.pkey , 
                 '.$this->tableWarehouse.',  
                 '.$this->tableJobType.',
                 '.$this->tableTransportationType.',
                 '.$this->tableLoadContainer.',
                 '.$this->tableVolumeUnit.'
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and  
                '.$this->tableName.'.jobtypekey = '.$this->tableJobType.'.pkey and 
                '.$this->tableName.'.transportationtypekey = '.$this->tableTransportationType.'.pkey and
                '.$this->tableName.'.loadcontainertypekey = '.$this->tableLoadContainer.'.pkey and
                '.$this->tableName.'.volumetype = '.$this->tableVolumeUnit.'.pkey';
        
        
        if (!empty($this->jobType))
            $sql .= ' and jobtypekey in ('.$this->jobType.')  ';
            
 		$sql .= $this->criteria ;
        $sql .= $this->getWarehouseCriteria() ;
        $sql .= $this->getCustomerCriteria() ;
        $sql .= $this->getSalesCriteria() ;
         
        return $sql;
    }
        


 	function afterCommitChangeStatus($pkey){
        // after commit agar tidak mengganggu transaction
		
		// kalo auto add ke TMS
		$this->addTMSJobOrder($pkey);
		
    }
	
	
	
	function cancelTMSJobOrder($pkey){ 
		
		if(empty(PARTNER_ACCOUNT['TMS']))  return;
		
		$rsHeader = $this->searchDataRow(array($this->tableName.'.partnerid'),
										' and '.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($pkey)
										);
		
		if(empty($rsHeader[0]['partnerid'])) return;
		
		// add to API
		$url = PARTNER_ACCOUNT['TMS']['partnerurl'].'/api/v3/trucking-service-order/cancel';
		$payload = array($rsHeader[0]['partnerid']);
		
		$this->executeAPIPartner(PARTNER_ACCOUNT['TMS'],$url,'POST', $payload);
		
	}
	
	
	function addTMSJobOrder($pkey){
		// lempar pkey agar bisa diproses dr tmp lain kedepannya jika dibutuhkan
		 
		if(empty(PARTNER_ACCOUNT['TMS']))  return;
			 
        $rsHeader = $this->getDataRowById($pkey); 
		
        $isTrucking = ($rsHeader[0]['istrucking'] == 1) ? true : false;
		if(!$isTrucking || $rsHeader[0]['statuskey'] <> 2) return; // hanya proses jika status Job Order = konfirmasi
     
		// nanti perlu ditambahkan kalo cancel 
		
		$container = new Container();
		$truckingServiceOrder = new TruckingServiceOrder();
	
		// select container
		$arrContainer = $container->searchDataRow(array($container->tableName.'.pkey',
												$container->tableName.'.volume',
												$container->tableName.'.containertypekey' ,
												$container->tableName.'.partnerid' 
											   ), 
										  ' and '.$container->tableName.'.statuskey = 1');

		$rsContainerCol = array_column($arrContainer,null,'pkey');  


		// ambil jumlah container di header
		$sql = 'select '.$this->tableVolumeDetail.'.itemkey, sum('.$this->tableVolumeDetail.'.qty) as qty
							from '.$this->tableVolumeDetail.'
							where '.$this->tableVolumeDetail.'.refkey = '.$this->oDbCon->paramString($pkey).' 
						group by '.$this->tableVolumeDetail.'.itemkey ';

		$rsDetailVolume = $this->oDbCon->doQuery($sql);
  
		
		// kalo customer di partner, kode nya berubah, kita blm handle. harus ubah di TMS nya kayanya, pake left join ?
		$partnerCustomerId =  $this->getPartnerId(new Customer(),$rsHeader[0]['customerkey']);
		if (empty($partnerCustomerId)) 
			$partnerCustomerId = PARTNER_ACCOUNT['TMS']['defaultcustomerid'];
		
		$vessel = new Vessel();
		$rsVessel = $vessel->searchDataRow(array($vessel->tableName.'.pkey',
												$vessel->tableName.'.name' 
											   ), 
										  ' and '.$vessel->tableName.'.pkey = '.$this->oDbCon->paramString($rsHeader[0]['vesselkey']));
			
		$vesselName = (!empty($rsVessel)) ? $rsVessel[0]['name'] : '';
			
		// add to API
		$url = PARTNER_ACCOUNT['TMS']['partnerurl'].'/api/v3/trucking-service-order';
		
		$date = time();
		$arrCargoType = $this->getCargoType($rsHeader[0]['containertypekey']); 
		
		$arrDesc = array();
		$rsContainer = $this->getDetailContainer($pkey);
		
		foreach($rsContainer as $containerRow) 
			array_push($arrDesc, $containerRow['containerno'].'/'.$containerRow['sealno']);
		
		
		$payload = array();
		$payload['code'] = $rsHeader[0]['code'];
		$payload['date'] = $date;
		$payload['request_id'] = $pkey;
		$payload['customer_id'] = $partnerCustomerId; // perlu ad failover, kalo cr customer gk ketemu, pake customer default
		//$payload['cnsignee_id'] = $partnerConsigneeId;  
		$payload['category_id'] = ($this->jobType == 1) ? PARTNER_ACCOUNT['TMS']['importpartnerid'] : PARTNER_ACCOUNT['TMS']['exportpartnerid'] ; // export / import
		$payload['cargo_type'] = $arrCargoType[0]['name']; // dry / reefer
		$payload['depot_id'] = $this->getPartnerId(new Depot(),$rsHeader[0]['depotkey']);
		$payload['terminal_id'] = $this->getPartnerId(new Terminal(),$rsHeader[0]['terminalkey']);
		$payload['description'] = implode(chr(13),$arrDesc);
		$payload['mbl'] = $rsHeader[0]['mblnumber'];
		$payload['aju'] = $rsHeader[0]['aju'];
		$payload['vessel'] = trim($vesselName .' '.$rsHeader[0]['vesselnumber']);
		$payload['service_detail'] = array();
		
		foreach($rsDetailVolume as $detailRow){ 
			
			if(!isset($rsContainerCol[$detailRow['itemkey']]) || $detailRow['qty'] <= 0) continue;
			
			array_push($payload['service_detail'],
			 array( 
					'service_id' => $rsContainerCol[$detailRow['itemkey']]['partnerid'],
					'qty' => $detailRow['qty'],
					'shipment_date' => $date,
					'price' => 1
				)
			);
		}
		
		// kalo ad detail baru add
		if(!empty($payload['service_detail'])){ 
			 $this->executeAPIPartner(PARTNER_ACCOUNT['TMS'], $url,'POST', $payload); 
		}
    	
	}
	
 
    function validateForm($arr,$pkey = ''){     
        
	    $customer = new Customer();
        $consignee = new Consignee();
        $item = new Item();
        $carrier= new Supplier();
        $emklQuotationOrder = new EMKLQuotationOrder();
        $container = new Container();
          
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$consigneekey = $arr['hidConsigneeKey'];  
		$carrierkey = $arr['hidCarrierKey'];  
        $containerkey = $arr['hidContainerKey'];
	    $refkey = $arr['hidJobOrderKey'];     
	    $customerkey = $arr['hidCustomerKey'];   
        $cargoType = $arr['hidCargoType'];      
        
        $detailCustomerKey = $arr['hidCustomerDetailKey'];  
        $containerDetailKey = $arr['hidContainerDetailKey'];
        $serviceDetailKey = $arr['hidServiceKey'];
        $selContainerType = $arr['selContainerType'];
        $isMaster = $arr['chkIsMaster'];   
        $shipmentType = $arr['selAirSea']; 
		$warehousekey = $arr['selWarehouseKey'];
		$isFromJobHeader = $arr['isFromJobHeader']; 
        $arrDetailKey = $arr['hidDetailKey'];
        $arrDetailItemKey = $arr['hidDetailItemKey'];
        $arrContainerDetailVolumeKey = $arr['selContainerDetailVolumeKey']; 
        $arrQty = $arr['qty']; 
        
        $arrCurrency = $arr['selSellingCurrency'];
        $arrRate = $arr['sellingCurrencyRate'];
        $detailCode = $arr['detailSalesCode'];
        $quotationkey = $arr['hidQuotationKey'];
        
        //$autoSellingReimburse = (in_array($this->loadSetting('autoSellingReimburse'),array(1,2)))  ?  true : false;
        
		//$customersellingkey = $arr['hidCustomerDetailKey'];   
        $rs = (!empty($pkey)) ? $this->getDataRowById($pkey) : array() ;
         
        //validasi kalo status gk menunggu / konfirmasi gk bisa edit 
		if (!empty($rs)){ 
			if ($rs[0]['statuskey'] > 5){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}

			$joCodeRestriction = $this->loadSetting('transactionCodeRestriction');
			if($joCodeRestriction == 2){
				
				if ($rs[0]['code'] != $arr['code']) { 
					$rsInvoiced = $this->getInvoiceInformation($pkey);
					$emklPurchaseOrder = new EMKLPurchaseOrder(); 
					$rsPurchase = $emklPurchaseOrder->searchDataRow( 
															array($emklPurchaseOrder->tableName.'.pkey',$emklPurchaseOrder->tableName.'.code'),
															' and '.$emklPurchaseOrder->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' 
															  and '. $emklPurchaseOrder->tableName.'.statuskey in (2,3)'
															); 
					if (!empty($rsInvoiced) || !empty($rsPurchase)) {
						$this->addErrorList($arrayToJs,false,$this->errorMsg['code'][4]);
					}
				}

			}
			
			
		} 
         
        if (empty($warehousekey))  
            $this->addErrorList($arrayToJs,false,$this->errorMsg['warehouse'][1]); 
       
        if(empty($customerkey))
            $this->addErrorList($arrayToJs,false, $this->errorMsg['shipper'][1]); 

        if($shipmentType <= 0 ) {
            $this->addErrorList($arrayToJs,false, $this->errorMsg['emklJobOrder'][7]); 
        }

       
       if (isset($arr['selShipmentType'])){
			$shipmentFreehandType = $arr['selShipmentType'];  
			if (empty($shipmentFreehandType))  
				$this->addErrorList($arrayToJs,false,$this->errorMsg['emklJobOrder'][8]);  
	   }
		
 
        // validasi nilai rate gk boleh satu kalo bukan IDR 
        $rsInvoiced = (!empty($pkey)) ?  $this->getInvoiceInformation($pkey) : array();  // sementara sampe rapi semua USDnya 
        $arrInvoicedKey = (!empty($rsInvoiced)) ? array_column($rsInvoiced,'refdetailkey') : array();
     
        $totalDetail = count($arr['hidDetailKey']);  
        for($i=0;$i<$totalDetail;$i++){ 
            $detailkey = $arr['hidDetailKey'][$i];
            
            // kalo sudah ad invoiced, lewatin saja
            if(in_array($detailkey,$arrInvoicedKey )) continue; 
            
			$arrRate[$i] = $this->unformatNumber($arrRate[$i]);	
            if($arrRate[$i] <= 0) {
                $this->addErrorList($arrayToJs,false,'<b>'.$detailCode[$i].'</b>. '. $this->errorMsg['rate'][6]);  
            }

			
            if ($arrCurrency[$i] <> CURRENCY['idr'] && ($arrRate[$i] == 1 || $arrRate[$i] <= 0) )
                $this->addErrorList($arrayToJs,false,'<b>'.$detailCode[$i].'</b>. '. $this->errorMsg['rate'][5]);  
        }
        
/*      $rsCarrier = $carrier->getDataRowById($carrierkey);
		if(empty($rsCarrier)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['carrier'][1]);
		}*/


       if(!$isMaster){
            foreach($detailCustomerKey as $customerdetailkey){  
                if(empty($customerdetailkey))
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['customer'][1]); 
            }
        }
        
        
        // hanay kalo FCL dan Transaksi LCL 
        if(!$isFromJobHeader && ($selContainerType == EMKL['container']['fcl'] || ($selContainerType == EMKL['container']['lcl'] && !$isMaster)) ){ 
            foreach($serviceDetailKey as  $serviceRowKey=>$row){    
                foreach($row as   $serviceDetailRowKey=>$value) {   
                   $qty = $this->unformatNumber($arrQty[$serviceRowKey][$serviceDetailRowKey]);
                   if($qty > 0 && empty($value))
                       $this->addErrorList($arrayToJs,false,$this->errorMsg['service'][1]);  
                }
            }  
        }

        $containerDetail = array();
        
        if($selContainerType == EMKL['container']['lcl']){
            $containerDetail = array($containerkey);
            
            // KALO LCL 
            if(empty($containerkey)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['container'][1]); 
            
            if(!$isMaster){
                $rsRef = $this->getDataRowById($refkey);
                if(empty($rsRef))
                        $this->addErrorList($arrayToJs,false, $this->errorMsg['reference'][1]);
            }      
 
        }else if(in_array($selContainerType, array(EMKL['container']['document'], EMKL['container']['warehouse']))){ 
            // document gk perlu validasi
            
        }else{  
            $containerDetail = $arr['selContainerDetailVolumeKey'];  
             if ( $shipmentType == EMKL['shipping']['sea'] && !$isFromJobHeader){  
                // validasi, hanya jika qty > 0, karena akan masalah kalo divalidasi utk PO yg auto add ke selling
                // KALO FCL dan tipenya SEA 
                 
                foreach($containerDetailKey as $containerRowKey=>$row){    
                    foreach($row as $containerDetailRowKey=>$value){  
                       $qty = $this->unformatNumber($arrQty[$containerRowKey][$containerDetailRowKey]);
                       if($qty > 0 && empty($value))
                           $this->addErrorList($arrayToJs,false,$this->errorMsg['container'][1]);
                    }

                }  
            } 
        }
//        
//        if(!empty($containerDetail)){
//                // cek jenis cargo 
////                $rsContainer = $container->searchDataRow( array($container->tableName.'.pkey', $container->tableName.'.containertypekey'),
////                                                            ' and '.$container->tableName.'.pkey in ('.$this->oDbCon->paramString($containerDetail,',').')' 
////                                                        );
//
//                //$this->setLog(' and '.$container->tableName.'.pkey in ('.$this->oDbCon->paramString($containerDetail,',').')',true);
//
//                // dimatikan dulu, masalah di JO kalo tipenya JO
//                /*foreach($rsContainer as $row){
//                    if ($row['containertypekey'] <> $cargoType){ 
//                        $this->addErrorList($arrayToJs,false,$this->errorMsg['container'][3]);
//                        break;
//                    }
//                } */
//        }
        

        // document dan warehosue gk perlu validasi
        if(!in_array($selContainerType, array(EMKL['container']['document'], EMKL['container']['warehouse']))){
            	$arrErrContainer = array();
                foreach ($containerDetailKey as $containerKeys) {
                    foreach ($containerKeys as $containerkey) {
                        //jika containerDetailKey tidak kosong, dan containerDetailKey tidak sama dengan containerDetailVolumeKey 
                        if (!empty($containerkey) && !in_array($containerkey, $arrContainerDetailVolumeKey)) {
                            $rsContainer = $container->getDataRowById($containerkey);
                            $arrErrContainer[] = "<strong>". $rsContainer[0]['name'] .". </strong>" . $this->errorMsg['container'][3];
                        }
                    }
                }
        }


		if (!empty($arrErrContainer)) {
			$errorMessage = implode("<br>", $arrErrContainer);
			$this->addErrorList($arrayToJs, false, $errorMessage);
		}

        // cek ad detail yg sdh diinvoice tp kehapus tdk
        if(!empty($pkey)){ 
            $arrDetailKey = $arr['hidDetailKey'];

            $sql = 'select '.$this->tableInvoiceHeader.'.code
                   from  '.$this->tableInvoiceHeader.','.$this->tableInvoiceDetail.' 
                   where 
                        '.$this->tableInvoiceHeader.'.pkey = '.$this->tableInvoiceDetail.'.refkey and
                        '.$this->tableInvoiceHeader.'.statuskey in (2,3) and 
                        '.$this->tableInvoiceDetail.'.invoicetype = 1 and
                        '.$this->tableInvoiceDetail.'.refsalesorderheaderkey = '.$this->oDbCon->paramString($pkey).' and
                        '.$this->tableInvoiceDetail.'.salesorderkey not in ('.$this->oDbCon->paramString($arrDetailKey,',').' ) ';
            
            $rsInvoice = $this->oDbCon->doQuery($sql);

            if(!empty($rsInvoice))
                 $this->addErrorList($arrayToJs,false,'<b>'.$rsInvoice[0]['code'].'</b>. '.$this->errorMsg['emklJobOrder'][2]);


            
            // validasi, utk tipe yg autoreimburse, ketika user hapus detail JO, pastikan Jo tersebut tidak ad selling yang dari PO reimburse
            //if($autoSellingReimburse){
                
//                 '. $emklPurchaseOrder->tableNameDetail .'.refjoborderdetailkey <> 0
                
//                    $emklPurchaseOrder = new EMKLPurchaseOrder(); 
//                    $arrDetailKey = $arr['hidDetailKey'];
//
//                    $sql = '
//                        select
//                            '. $emklPurchaseOrder->tableName .'.code
//                        from
//                            '. $emklPurchaseOrder->tableName .',
//                            '. $emklPurchaseOrder->tableNameDetail .'
//                        where
//                            '. $emklPurchaseOrder->tableName .'.pkey = '. $emklPurchaseOrder->tableNameDetail .'.refkey and
//                            '. $emklPurchaseOrder->tableNameDetail .'.refjoborderdetailkey not in ('. $this->oDbCon->paramString($arrDetailKey,',') .') and
//                            '. $emklPurchaseOrder->tableName .'.refkey = '. $this->oDbCon->paramString($pkey) .' and
//                            '. $emklPurchaseOrder->tableName .'.isreimburse = 1 and
//                            '. $emklPurchaseOrder->tableNameDetail .'.refjoborderdetailkey <> 0 and
//                            '. $emklPurchaseOrder->tableName .'.statuskey in (2,3) 
//                    ';
// 
//                    $rsPurchaseOrder = $this->oDbCon->doQuery($sql);
//
//                    //validasi detail ada yang dihapus nggak ketika di edit, yang ada item dari PO
//                    if(!empty($rsPurchaseOrder)) {
//                        $this->addErrorList($arrayToJs, false, '<strong>'. $rsPurchaseOrder[0]['code'] .'. </strong>' . $this->errorMsg['emklJobOrder'][6]);
//                    } 



                    //validasi, data detail reimburse ada yang berusah atau tidak
//
//                    $criteriaDetailItem = ' and ' . $this->tableNameDetailItem.'.isreimburse = 1 and '. $this->tableNameDetailItem .'.refpurchaseorderdetailkey is not null';
//                    $rsDetailItem = $this->getItemDetail('','',$pkey, $criteriaDetailItem);
//
//
//                        $arrRsDetailItem = array();
//                        foreach($rsDetailItem as $detailItemRow) 
//                        {
//                            array_push($arrRsDetailItem, array(
//                                $detailItemRow['pkey'],
//                                $this->formatNumber($detailItemRow['qty'],3),
//                                $detailItemRow['servicekey'],
//                                $detailItemRow['currencykey'],
//                                $this->formatNumber($detailItemRow['priceinunit'],2),
//                                $this->formatNumber($detailItemRow['taxdetail'],2),
//                                $detailItemRow['ispriceincludetax'],
//                                $this->formatNumber($detailItemRow['subtotalcurrency'],2),
//                                $this->formatNumber($detailItemRow['subtotal'],2),
//                                $detailItemRow['isreimburse']
//                            ));
//                            if(($shipmentType == EMKL['shipping']['sea']) && in_array($selContainerType, array(EMKL['container']['fcl'],EMKL['container']['freightcustomfcl'],EMKL['container']['customfcl']))) {
//                                    array_push($arrRsDetailItem, array(
//                                        $detailItemRow['itemkey'],
//                                    ));
//                            }
//
//                        }
//
//
//
//
//                        $arrChkIsReimburse = $arr['chkIsReimburse'];
//                        $arrContainerDetailKey = $arr['hidContainerDetailKey'];
//                        $arrServiceKey = $arr['hidServiceKey'];
//                        $arrQty = $arr['qty'];
//                        $arrSelCurrencyDetail = $arr['selCurrencyDetail'];
//                        $arrPriceInUnit = $arr['priceInUnit'];
//                        $arrTaxDetail = $arr['taxDetail'];
//                        $arrChkIncludeTaxDetail = $arr['chkIncludeTaxDetail'];
//                        $arrDetailRowCurrencySubtotal = $arr['detailRowCurrencySubtotal'];
//                        $arrDetailRowSubtotal = $arr['detailRowSubtotal'];
//
//                        $arrDetailItem = array();
//                        for($i=0; $i<count($arrDetailKey); $i++) {
//
//                            $hidDetailItemKey = $arrDetailItemKey[$i];
//                            $chkIsReimburse = $arrChkIsReimburse[$i];
//                            $containerDetailKey = $arrContainerDetailKey[$i];
//                            $serviceKey = $arrServiceKey[$i];
//                            $qtyDetail = $arrQty[$i];
//                            $selCurrencyDetail = $arrSelCurrencyDetail[$i];
//                            $priceInUnitDetail = $arrPriceInUnit[$i];
//                            $taxDetail = $arrTaxDetail[$i];
//                            $chkIncludeTaxDetail = $arrChkIncludeTaxDetail[$i];
//                            $detailRowCurrencySubtotal = $arrDetailRowCurrencySubtotal[$i];
//                            $detailRowSubtotal = $arrDetailRowSubtotal[$i];
//
//
//                            for($j=0; $j<count($hidDetailItemKey); $j++) {
//
//                                if($chkIsReimburse[$j] == 0) continue;
//
//                                $containerDetail = $containerDetailKey[$j];
//                                $pkey = $hidDetailItemKey[$j];
//                                $qty = $qtyDetail[$j];
//                                $servicekey = $serviceKey[$j];
//                                $currencykey = $selCurrencyDetail[$j];
//                                $priceInUnit = $priceInUnitDetail[$j];
//                                $taxDetail = $this->formatNumber($taxDetail[$j],2);
//                                $isIncludeTax  = $chkIncludeTaxDetail[$j];
//                                $rowCurrencySubtotal = $this->formatNumber($detailRowCurrencySubtotal[$j],2);
//                                $rowSubtotal  = $this->formatNumber($detailRowSubtotal[$j],2);
//                                $isReimburse = $chkIsReimburse[$j];
//
//                                array_push($arrDetailItem, array(
//                                    $pkey,
//                                    $qty,
//                                    $servicekey,
//                                    $currencykey,
//                                    $priceInUnit,
//                                    $taxDetail,
//                                    $isIncludeTax,
//                                    $rowCurrencySubtotal,
//                                    $rowSubtotal,
//                                    $isReimburse
//                                ));
//
//                                if(($shipmentType == EMKL['shipping']['sea']) && in_array($selContainerType, array(EMKL['container']['fcl'],EMKL['container']['freightcustomfcl'],EMKL['container']['customfcl']))) {
//                                    array_push($arrDetailItem, array($containerDetail));
//                                }
//                            }
//
//                        }
//
//
//                        //compare data
//                        $arrRsDetailItem = md5(json_encode($arrRsDetailItem));
//                        $arrDetailItem = md5(json_encode($arrDetailItem));
//
//                        $compareResult = ($arrDetailItem == $arrRsDetailItem) ? true : false;
//
//                        if(!$compareResult) {
//                            $this->addErrorList($arrayToJs, false, '<strong>' . $this->errorMsg[201] . '</strong>. ' . $this->errorMsg[906]);
//                        }
            //} 
            
        }

        $selContainerType = $arr['selContainerType'];
        $shipmentType = $arr['selAirSea'];
      
		//validasi jika pakai quotation
        if (!empty($quotationkey)) {
            $rsQuotation = $emklQuotationOrder->searchDataRow(array($emklQuotationOrder->tableName . '.transportationtypekey', $emklQuotationOrder->tableName . '.loadcontainertypekey', $emklQuotationOrder->tableName . '.expdate', $emklQuotationOrder->tableName . '.trdate'), ' and ' . $emklQuotationOrder->tableName . '.pkey =' . $this->oDbCon->paramString($quotationkey));

            $trDate = str_replace('\'', '', $this->oDbCon->paramDate($arr['trDate'], ' / ', 'Y-m-d'));

            $rsQuotation = $emklQuotationOrder->getDataRowById($quotationkey);
            $arrStatusQuotationKey = array(TRANSACTION_STATUS['menunggu'], TRANSACTION_STATUS['konfirmasi'], TRANSACTION_STATUS['batal']);

            // validasi hanya quotation yang statusnya customer approved
            if (in_array($rsQuotation[0]['statuskey'], $arrStatusQuotationKey)) {
                $this->addErrorList($arrayToJs, false, '<strong>' . $rsQuotation[0]['code'] . '</strong>. ' . $this->errorMsg['emklQuotation'][5]);
            }
            //warehouse harus sama
            if ($arr['selWarehouseKey'] !== $rsQuotation[0]['warehousekey']) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['emklJobOrder'][8]);
            }

            $hidId = $arr['hidId'];
            if(!empty($hidId)) {
                $rsData = $this->getDataRowById($hidId);
                $shipperkey = $rsData[0]['customerkey'];

                if($shipperkey !== $rsQuotation[0]['customerkey']) {
                    $this->addErrorList($arrayToJs, false, $this->errorMsg['emklJobOrder'][11]);
                }
            }

            $joDate = strtotime($trDate);
            $expDate = strtotime($rsQuotation[0]['expdate']);
            if ($joDate >= $expDate) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['emklQuotation'][8]);
            }

            //jenis pekerjaan harus sesuai
            if ($selContainerType != $rsQuotation[0]['loadcontainertypekey']) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['jobType'][3]);
            }

            //jenis transportasi harus sesuai
            if ($shipmentType != $rsQuotation[0]['transportationtypekey']) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['transportation'][3]);
            }
        }

        // ketika edit, validasi containernya ada yg kehapus gk yg sudah jadi HBL
 
        if(!empty($pkey)) {
            $emklHouseBL = new EMKLHouseBL();

            $sql = '
                SELECT 
                    DISTINCT('. $emklHouseBL->tableNameDetailContainer .'.refcontainerkey) as refcontainerkey,
                    '. $emklHouseBL->tableNameDetailContainer .'.containerno,
                    '. $emklHouseBL->tableNameDetailContainer .'.sealno
                FROM
                    '. $emklHouseBL->tableName .',
                    '. $emklHouseBL->tableNameDetailContainer .'
                WHERE
                    '. $emklHouseBL->tableName .'.statuskey in (2,3) and
                    '. $emklHouseBL->tableName .'.pkey = '. $emklHouseBL->tableNameDetailContainer .'.refkey and
                    '. $emklHouseBL->tableName .'.refheaderkey = '. $this->oDbCon->paramString($pkey) .'
            ';
            
            
            $rs = $this->oDbCon->doQuery($sql);
 
            
            if(!empty($rs)) { 
                
                // pake container key aj, takutnya dihapus terus reenter ulang dengan no yg sama
                
//                $arrContainerNumber = $arr['containerNo'];
                //cek apakah container number yang terdaftar di house BL di job order telah dirubah atau di delete,
                //jika di job order telah dirubah atau di delete dan terdaftar di house BL maka tampilkan error
                
                $arrErrMsg = array();
                for($i=0; $i<count($rs); $i++) {
                    
                    // hati2 ad kemiripan nama variable hidDetailContainerKey[] dan hidContainerDetailKey[] 
                    
                    if (!in_array($rs[$i]['refcontainerkey'], $arr['hidDetailContainerKey']))  
                        array_push($arrErrMsg,  '<strong>'. $rs[$i]['containerno'] .'. </strong>' . $this->errorMsg['emklJobOrder'][3]);
                    
                }
                if(!empty($arrErrMsg)) 
                    $this->addErrorList($arrayToJs, false, $this->errorMsg[212] . '<br>' . implode('<br>', $arrErrMsg));
              
            }
        }

        if(isset($arr['refDetailHBL'])) {

            $arrDetailHBL = $arr['detailHBL'];
            $arrRefDetailHBL = $arr['refDetailHBL'];

            for ($i=0; $i<count($arrRefDetailHBL); $i++) {
            
                if(empty($arrRefDetailHBL[$i])) continue;

                if (!empty($arrDetailHBL[$i]) && !empty($arrRefDetailHBL[$i])) {
                    $this->addErrorList($arrayToJs, false, $this->errorMsg['hbl'][5]);
                }

                $arrDetailHBLExplode = explode(',',$arrRefDetailHBL[$i]);
                foreach($arrDetailHBLExplode as $hbl)  { 
                    if (!in_array(trim($hbl),$arrDetailHBL)) {
                        $this->addErrorList($arrayToJs,false, $this->lang['refHBL'].' '.'<strong>' . $hbl . '</strong>. ' .$this->errorMsg['hbl'][6]);
                    }
                }

            }

        }



		
		return $arrayToJs;
	 }

  
   function reCountSubtotal($arrParam){
     
        $grandtotal = 0; 
        $totalSelling = 0; 
        $taxTotal = 0;
        //$amount = 0;
        
        $updateTaxAtJobOrder = $this->loadSetting('updateTaxAtJobOrder');
        $arrCustomerKey = $arrParam['hidCustomerDetailKey'];  
        $arrRate = $arrParam['sellingCurrencyRate'];
        $arrSellingCurrency = $arrParam['sellingCurrencyRate'];
        
        $arrServicekeyDetail = $arrParam['hidServiceKey'];
        $arrPriceinunitDetail = $arrParam['priceInUnit'];
        $qtyInBaseUnitDetail =  $arrParam['qty'] ; 
        $arrCurrencyDetailDetail =  $arrParam['selCurrencyDetail'];
        $arrTaxDetail = $arrParam['taxDetail'];
        $arrChkIncludeTax = $arrParam['chkIncludeTaxDetail'];
        $arrSellingCurrency = $arrParam['selSellingCurrency'];
        $arrChkIsRemburse = $arrParam['chkIsReimburse'];
        
        $arrDetail = array();
        $arrDetail['detailCurrencyTotal'] = array();
        $arrDetail['detailOtherCurrencyTotal'] = array();
        $arrDetail['detailTotal'] = array();
       
        $arrDetailItem = array();
        $arrDetailItem['taxDetail'] = array();
        $arrDetailItem['taxDetailValue'] = array();
        $arrDetailItem['beforeTaxDetail'] = array();
        $arrDetailItem['afterTaxDetail'] = array();
        $arrDetailItem['detailRowCurrencySubtotal'] = array();
        $arrDetailItem['detailRowSubtotal'] = array();
        
        for ($i=0;$i<count($arrCustomerKey);$i++){
					
            if (empty($arrCustomerKey[$i]) || $arrRate[$i] <=0)   continue; 
          
            
            $arrServicekey = $arrServicekeyDetail[$i];
            $arrPriceinunit = $arrPriceinunitDetail[$i];
            $qtyInBaseUnit =  $qtyInBaseUnitDetail[$i] ; 
            $arrCurrencyDetail =  $arrCurrencyDetailDetail[$i];
            $arrTaxDetailPercentage = $arrTaxDetail[$i];
            $arrIncludeTax = $arrChkIncludeTax[$i];
			$sellingCurrencyKey = $arrSellingCurrency[$i];
            $arrIsReimburse = $arrChkIsRemburse[$i];
            
            $rate = $this->unFormatNumber($arrRate[$i],2);
            
            $totalCurrency = 0;
            $totalIDR = 0;
            
            for($j=0;$j<count($arrServicekey);$j++){
                $priceInUnit = $this->unFormatNumber($arrPriceinunit[$j]);   
				$qty = $this->unFormatNumber($qtyInBaseUnit[$j]);
                $servicekey = $arrServicekey[$j];
                $currencykey = $arrCurrencyDetail[$j];
                
                if(empty($servicekey) || $qty <= 0 || $priceInUnit <= 0 ) continue; 
                
                // gk bisa kalo gk kali rate karena currencykeynya diheader 
              
                $itemSubtotalCurrency = $qty * $priceInUnit;
                $beforeTaxCurrencyDetail = $itemSubtotalCurrency;
                $afterTaxCurrencyDetail = $itemSubtotalCurrency;
                
                $amount = ($currencykey == CURRENCY['idr']) ? $itemSubtotalCurrency : ($itemSubtotalCurrency * $rate);  
                
//                $itemSubtotal = ($currencykey == CURRENCY['idr']) ? $itemSubtotalCurrency : ($itemSubtotalCurrency * $rate);  

                if($updateTaxAtJobOrder == 1) {
                    
                    $taxDetailPercentage = $this->unFormatNumber($arrTaxDetailPercentage[$j]); 
                    $isReimburse = $arrIsReimburse[$j];
                    
                    if ($isReimburse == 1) $taxDetailPercentage = 0;
                    
                     $taxDetailValue = 0;
                    
                     if($taxDetailPercentage > 0){  
                        if($arrIncludeTax[$j] == 0){
                            $taxDetailValue = $itemSubtotalCurrency * $taxDetailPercentage / 100; 
                            $afterTaxCurrencyDetail += $taxDetailValue; 
                            $amount +=  $amount * $taxDetailPercentage / 100;
                        } else{
                            $taxDetailValue = ($taxDetailPercentage / (100 + $taxDetailPercentage) * $itemSubtotalCurrency);
                            $beforeTaxCurrencyDetail -= $taxDetailValue;
                        }
                            
                     }

                    $taxTotal += ($currencykey == CURRENCY['idr']) ? $taxDetailValue : ($taxDetailValue * $rate) ;

                    
                    $arrDetailItem['taxDetail'][$i][$j] = $taxDetailPercentage;
                    $arrDetailItem['taxDetailValue'][$i][$j] = $taxDetailValue;
                    $arrDetailItem['beforeTaxDetail'][$i][$j] = $beforeTaxCurrencyDetail;
                    $arrDetailItem['afterTaxDetail'][$i][$j] = $afterTaxCurrencyDetail;
                }
                
                


                if($currencykey != $sellingCurrencyKey)
                    $totalIDR += $amount;
                else
                    $totalCurrency += $afterTaxCurrencyDetail;

                
                $arrDetailItem['detailRowCurrencySubtotal'][$i][$j] = $afterTaxCurrencyDetail;
                $arrDetailItem['detailRowSubtotal'][$i][$j] = $amount; 
 
                $totalSelling += $amount;  
            }
            
            
            $arrDetail['detailCurrencyTotal'][$i] = $totalCurrency;
            $arrDetail['detailOtherCurrencyTotal'][$i] = $totalIDR;
            $arrDetail['detailTotal'][$i] = $totalIDR + ($totalCurrency * $rate);
             
        } 
        
//        $totalSelling = $subtotal;
       
        $totalBuying = $this->countTotalBuying($arrParam['pkey']); 
        $totalCommission = $this->countTotalCommission($arrParam['pkey']);
 
        $grandtotal = $totalSelling - $totalBuying - $totalCommission;
        
        $reCountResult['totalSelling'] = $totalSelling; 
        $reCountResult['grandTotal'] = $grandtotal;  
        $reCountResult['taxValue'] = $taxTotal;
        $reCountResult['detail'] = $arrDetail;
        $reCountResult['detailItem'] = $arrDetailItem;
       
        return $reCountResult;
    }
    
    function generateDefaultQueryForAutoComplete($returnField){ 
        
        $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			';
           
         return $sql;
     }

    
    //bikin autocomplete emkl job order belum kelar
     function searchDataForInvoice($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
		$sql = 'select
					'.$this->tableNameDetail. '.pkey,
                    '.$this->tableNameDetail. '.code as value,
                    '.$this->tableNameDetail. '.customerkey,
                    '.$this->tableNameDetail. '.hbl,
                    '.$this->tableNameDetail. '.weight,
                    '.$this->tableNameDetail. '.measurement,
                    '.$this->tableNameDetail. '.rate,
                    '.$this->tableCustomer. '.name as customername,
                    '.$this->tableName. '.code as refheadercode,
                    '.$this->tableName. '.pkey as refheaderkey,
                    '.$this->tableName. '.trdate as refdate,
                    '.$this->tableName. '.warehousekey
				from 
					'.$this->tableNameDetail . ','.$this->tableName . ','.$this->tableCustomer.','.$this->tableStatus.'
				where  		
					'.$this->tableName . '.pkey = '.$this->tableNameDetail.'.refkey and
					'.$this->tableNameDetail . '.customerkey = '.$this->tableCustomer.'.pkey and
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
    
  
    
    function getUnInvoicedItemDetail($pkey,$currencykey='',$typekey=0){
        
        // utk ek jensi reimburse atau bukan pake field servicetype saja, 
        // agar lebih fleksible kedepannya dalam pengelompokan data
        // typekey 1 : selling
        //         2 : reimburse
        //         0 : all
        
        
        $currencyCriteria = (!empty($currencykey)) ? ' and trans.currencykey = '.$this->oDbCon->paramString($currencykey)  : '';
        $invoiceTypeCriteria = '';
        
        if ($typekey == 1)
         $invoiceTypeCriteria .= ' and trans.isreimburse = 0 ';
        else if ($typekey == 2)
         $invoiceTypeCriteria .= ' and trans.isreimburse = 1 ';
            
        // asumsi itemkey dan costkey, pasti pkeynya unique, dan masing2 hanya bisa di detail atau di cost
        $sql = '  SELECT trans.*, item.name as itemname,item.istax23, 
				if(trans.alias = "" or trans.alias is null, item.aliasname, trans.alias) as aliasname, 
				currency.name as currencyname from ( 
                    select 
                        concat('.$this->tableNameDetailItem.'.pkey,\'-\','.$this->tableNameDetailItem.'.servicekey) as joinkey,
                        '.$this->tableNameDetail.'.rate, 
                        '.$this->tableNameDetailItem.'.pkey, 
                        '.$this->tableNameDetailItem.'.itemkey as containerkey, 
                        '.$this->tableNameDetailItem.'.refkey, 
                        '.$this->tableNameDetailItem.'.unitkey, 
                        '.$this->tableNameDetailItem.'.isreimburse, 
                        '.$this->tableNameDetailItem.'.alias, 
                        ' . $this->tableNameDetailItem . '.ispriceincludetax,  
                        ' . $this->tableNameDetailItem . '.taxdetail, 
                        servicekey as itemkey, '.$this->tableNameDetailItem.'.currencykey, 
                        '.$this->tableNameDetailItem.'.qty,  ('.$this->tableNameDetailItem.'.qty - qtyinvoiced) as outstandingqty, priceinunit, ('.$this->tableNameDetailItem.'.qty - qtyinvoiced) * priceinunit as total, \'1\' as orderlist  
                    from  
                        '.$this->tableNameDetailItem.'
                        left join '.$this->tableNameDetail.' on  '.$this->tableNameDetailItem.'.refkey = '.$this->tableNameDetail.'.pkey 
                        where '.$this->tableNameDetailItem.'.refkey in('.$this->oDbCon->paramString($pkey,',').')
                 ) trans, item, currency 
                 where  
                    trans.refkey in('.$this->oDbCon->paramString($pkey,',').') and  
                    trans.itemkey = item.pkey and outstandingqty > 0 and 
                    trans.currencykey = currency.pkey  
                    '.$currencyCriteria.' 
                    '.$invoiceTypeCriteria.' 
                 order by orderlist asc, pkey asc
                ';
        
		$rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }
     
     
 	function validateCancel($rsHeader,$autoChangeStatus=false){  
		
       // gk perlu bedain dulu dr header atau JO
       // karena lebih baik tetep diblock agar user tau sudah ad biaya yg keluar utk JO Tersebut
        
		$pkey = $rsHeader[0]['pkey'];
        $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'))['key']; 
           
        // cek Purchasing sudah ad yg konfirmasi / closed blm
        // kecuali purchaase yg dr JO Header
        $emklPurchaseOrder = new EMKLPurchaseOrder(); 
        $rsPurchase = $emklPurchaseOrder->searchDataRow( 
                                                        array($emklPurchaseOrder->tableName.'.pkey',$emklPurchaseOrder->tableName.'.code'),
                                                        ' and '.$emklPurchaseOrder->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' 
                                                          and '. $emklPurchaseOrder->tableName.'.statuskey in (2,3)'
                                                        ); 
        
        if (!empty($rsPurchase)) 
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsPurchase[0]['code'].'</strong>, ' .$this->errorMsg[225] );
 
        
		// validasi refund
		$emklCommission = new EMKLCommission();
		$rsCommission = $emklCommission->searchDataRow( 
													array($emklCommission->tableName.'.pkey',$emklCommission->tableName.'.code'),
													' and '.$emklCommission->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' 
													  and '.$emklCommission->tableName.'.statuskey in (2,3)'
													); 

        if (!empty($rsCommission)) 
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsCommission[0]['code'].'</strong>, ' .$this->errorMsg[225] );
 
		
		//cek ad AP Employee Commission terbayar
        $apEmployeeComission = new APEmployeeCommission();
		$rsAP = $apEmployeeComission->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($tablekey).' and '.$apEmployeeComission->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and '.$apEmployeeComission->tableName.'.statuskey in (2,3)');
		if(!empty($rsAP)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2],true);
	  
     
		
		// validasi invoice 
        $rsInvoiced = $this->getInvoiceInformation($pkey);
        if (!empty($rsInvoiced)) 
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsInvoiced[0]['code'].'</strong>, ' .$this->errorMsg[225] );
 
      	//cek apakah progress activity telah di konfirmasi / selesai 
        if($this->activeModule['activityprogress']){ 
          $activityProgress = new ActivityProgress();
		  $rsActivity = $activityProgress->searchDataRow(array(
                $activityProgress->tableName.'.pkey',
                $activityProgress->tableName.'.code',
                $activityProgress->tableName.'.joborderkey',
                $activityProgress->tableName.'.statuskey'   
            ), ' and ' . $activityProgress->tableName.'.joborderkey = ('. $this->oDbCon->paramString($pkey) .') and '. $activityProgress->tableName .'.statuskey in (2,3) ');

			if(!empty($rsActivity)) {
				$this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'</strong>. ' . $this->errorMsg[201] . '<br> <b>'. $rsActivity[0]['code'] .'. </b>' . $this->errorMsg['activityProgress'][2]);
			}
		}
		
		//validasi kalau komisi sudah di bayarkan tidak boleh ubah status
        $isCommissionPaid = $this->isCommissionRequested($rsHeader[0]['pkey']);
        if(!empty($isCommissionPaid[$rsHeader[0]['pkey']])) 
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '. '.$this->errorMsg[201].'</strong> ' .$this->errorMsg['emklJobOrder'][9]); 
   
	 }
    
     function cancelTrans($rsHeader,$copy){  

         
        $pkey = $rsHeader[0]['pkey'];
		$emklPurchaseOrder = new EMKLPurchaseOrder();
        $rsPurchase = $emklPurchaseOrder->searchDataRow( 
                                                        array($emklPurchaseOrder->tableName.'.pkey',$emklPurchaseOrder->tableName.'.code'),
                                                        ' and '.$emklPurchaseOrder->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' 
                                                          and '. $emklPurchaseOrder->tableName.'.statuskey in (1)'
                                                        );  
         
        for($i=0;$i<count($rsPurchase);$i++){  
         // cancel purchase yg terbentuk dr JO
          $emklPurchaseOrder->changeStatus($rsPurchase[$i]['pkey'],4,'',false,true); 
        }
		 
		// commission
		$emklCommission = new EMKLCommission();
        $rsCommission = $emklCommission->searchDataRow( 
                                                        array($emklCommission->tableName.'.pkey',$emklCommission->tableName.'.code'),
                                                        ' and '.$emklCommission->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' 
                                                          and '. $emklCommission->tableName.'.statuskey in (1)'
                                                        );  
         
        for($i=0;$i<count($rsCommission);$i++){  
         // cancel refund yg terbentuk dr JO
          $emklCommission->changeStatus($rsCommission[$i]['pkey'],4,'',false,true); 
        }
          
        $emklOrderInvoice = new EMKLOrderInvoice();
        $sql = 'select 
                    '.$emklOrderInvoice->tableNameDetail.'.refkey  
                from 
                    '.$emklOrderInvoice->tableNameDetail.', 
                    '.$emklOrderInvoice->tableName.' 
                where 
                    '.$emklOrderInvoice->tableNameDetail.'.refkey = '.$emklOrderInvoice->tableName.'.pkey  and 
                    '.$emklOrderInvoice->tableNameDetail.'.refsalesorderheaderkey = '.$pkey.' and
                    '.$emklOrderInvoice->tableName.'.statuskey = 1 ';
         
        $rsInvoice = $this->oDbCon->doQuery($sql); 
         
        for($i=0;$i<count($rsInvoice);$i++)  
            $emklOrderInvoice->changeStatus($rsInvoice[$i]['refkey'],4,'',false,true);

        
	  	if($this->isActiveMasterHBL) 
			$this->unlinkHBL($pkey,'refheaderkey'); 
		 
		if ($copy){ 			
			$this->copyDataOnCancel($pkey);
		}else{ 
            // pake JO Haader jd menunggu lg, kalo ad header
            // sementara langsung saja
            if(!empty($rsHeader[0]['headerorderkey'])){ 
                $sql = 'update ' .$this->tableJOHeader.' set statuskey = 1 where pkey = ' . $rsHeader[0]['headerorderkey'];
                $this->oDbCon->execute($sql);
            }
        }	  
		 
		// canel AP Employee Comission
//		$apEmployeeComission = new APEmployeeCommission();
//        $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'))['key']; 
//        
//        $rsAP = $apEmployeeComission->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($tablekey).' and '.$apEmployeeComission->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and '.$apEmployeeComission->tableName.'.statuskey = 1');
//        for($i=0;$i<count($rsAP);$i++) { 
//			$arrayToJs = $apEmployeeComission->changeStatus($rsAP[$i]['pkey'],4,'',false, true);
//            if (!$arrayToJs[0]['valid'])
//                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
//        }
		 
		if($this->activeModule['activityprogress']){ 
        	$activityProgress = new ActivityProgress();
			$rsActivityProgress = $activityProgress->searchDataRow(array(
						$activityProgress->tableName.'.pkey',
						$activityProgress->tableName.'.code',
						$activityProgress->tableName.'.joborderkey',
						$activityProgress->tableName.'.statuskey'
						), ' and ' . $activityProgress->tableName.'.joborderkey in ('. $this->oDbCon->paramString($pkey) .') 
								and '. $activityProgress->tableName .'.statuskey in (1) ');

			if(!empty($rsActivityProgress)) {
				for($i=0; $i<count($rsActivityProgress); $i++) {
					$activityProgress->changeStatus($rsActivityProgress[$i]['pkey'],4,'',false,true);
				}
			}        
		}
  		
		$this->cancelTMSJobOrder($pkey); 
		 
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	}    
    
    function getTotalInvoicedAndOutstanding($id,$customCodeKey = ''){
        
            $customCodeCriteria = (!empty($customCodeKey)) ? ' and customcodekey = ' . $this->oDbCon->paramString($customCodeKey) : '';
        
            $sql = 'select pkey, amount  from '.$this->tablePartialInvoice.' where refkey = '.$this->oDbCon->paramString($id).' and amount > 0 ' . $customCodeCriteria; 
            //$this->setLog($sql);
            $rs = $this->oDbCon->doQuery($sql);
   
            $totalInvoiced = 0;
            foreach($rs as $row)
                $totalInvoiced += $row['amount'];
                
            $sql = 'select coalesce(sum(amount),0) as outstanding  from '.$this->tablePartialInvoice.' where refkey = '.$this->oDbCon->paramString($id) . $customCodeCriteria; 
            $rsOutstanding = $this->oDbCon->doQuery($sql);
   
            $arr = array();
            $arr['rsTotalnvoiced'] = $rs;
            $arr['totalInvoiced'] = $totalInvoiced;
            $arr['outstanding'] = $rsOutstanding[0]['outstanding'];
        
            //$this->setLog($arr);
            return $arr;
		 	 
    }
	
	function confirmTrans($rsHeader){
 		       
        $id = $rsHeader[0]['pkey'];

		if ($this->activeModule['activityprogress']){
			$activityProgress = new ActivityProgress();

			$rsActivityProgress = $activityProgress->searchDataRow(array(
				$activityProgress->tableName.'.pkey',
				$activityProgress->tableName.'.code',
				$activityProgress->tableName.'.joborderkey',
				$activityProgress->tableName.'.statuskey'
			), ' and ' . $activityProgress->tableName.'.joborderkey in ('. $this->oDbCon->paramString($id) .') and '. $activityProgress->tableName .'.statuskey in (1,2,3) ');

			if(empty($rsActivityProgress)){
				$this->autoAddActivityProgress($id);
			} 
		}
		
		if($rsHeader[0]['totalemployeecommission'] > 0){ 
			//update jurnal umum
			$this->updateGL($rsHeader); 
		}
		
	}
	   
	function autoAddActivityProgress($id)
    {

        $templateActivity = new TemplateActivity();
        $activityProgress = new ActivityProgress();

        $rsData = $this->getDataRowById($id);

        $rsTemplateActivity = $templateActivity->searchData('', '', true, ' and ' . $templateActivity->tableName . '.statuskey = 1');

            $arrParam = array();

            $arrParam['code'] = 'xxxxx';
            $arrParam['selStatus'] = TRANSACTION_STATUS['menunggu'];
            $arrParam['trDate'] = date('d / m / Y');
            $arrParam['hidJobOrderKey'] = $id;
            $arrParam['trDesc'] = '';

            $arrParam['hidDetailKey'] = array();
            $arrParam['detailDate'] = array();
            $arrParam['hidActivityKey'] = array();
            $arrParam['response'] = array();
            $arrParam['detailNotes'] = array();

            for ($i = 0; $i < count($rsTemplateActivity); $i++) {

                array_push($arrParam['hidDetailKey'], 0);
                array_push($arrParam['detailDate'], $this->formatDBdate($rsData[0]['trdate'], 'd / m / Y'));
                array_push($arrParam['hidActivityKey'], $rsTemplateActivity[$i]['pkey']);
                array_push($arrParam['response'], '');
                array_push($arrParam['detailNotes'], '');

            }
        
            $result = $activityProgress->addData($arrParam);

            if (!$result[0]['valid'])
                throw new Exception('<strong>' . $rsData[0]['code'] . '</strong>. ' . $result[0]['message']);

    }
	
	
	  function updateGL($rs){
        if (!USE_GL) return;
		

        $generalJournal = new GeneralJournal();
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();  
		$employee = new Employee();
		 
        $warehousekey = $rs[0]['warehousekey'];
		$rsWarehouse = $warehouse->searchDataRow(array($warehouse->tableName.'.saleskey'),
												 ' and '. $warehouse->tableName.'.pkey = ' . $this->oDbCon->paramString($warehousekey));
		  
		$id = $rs[0]['pkey']; 
          
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
        $arr['createdBy'] = 0;
        $arr['selWarehouse'] = $warehousekey;
        $arr['trDesc'] = $this->lang['adminFee'];
		$arr['selWarehouseKey'] = $rs[0]['warehousekey']; 
		 
        $temp = -1; 
   
		// KOMISI KANTOR / MANAGEMENT  
		$coaCommissionCost =  $coaLink->getCOALink ('adminfeecost', $warehouse->tableName,$warehousekey, 0);

		$temp++;
		$arr['hidCOAKey'][$temp] =  $coaCommissionCost[0]['coakey'];
		$arr['debit'][$temp] = $rs[0]['totalemployeecommission'];
		$arr['credit'][$temp] = 0;  
		$arr['trdescDetail'][$temp] =  $rs[0]['code'];

		$temp++;
		$arr['hidCOAKey'][$temp] =  $employee->getAPCommissionCOAKey($rsWarehouse[0]['saleskey'],$warehousekey);
		$arr['debit'][$temp] =  0;
		$arr['credit'][$temp] =  $rs[0]['totalemployeecommission'];
		$arr['trdescDetail'][$temp] =  $rs[0]['code']; 
		 

        $arrayToJs = $generalJournal->addData($arr);
 
        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);

    } 

    	 
	function validateConfirm($rsHeader){
        
        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
         
        $emklQuotationOrder = new EMKLQuotationOrder();

        $quotationKey = $rsHeader[0]['quotationkey'];  
         // validasi nilai rate gk boleh satu kalo bukan IDR
        $totalDetail = count($rsDetail);
        for($i=0;$i<$totalDetail;$i++){ 
            if ($rsDetail[$i]['currencykey']<> CURRENCY['idr'] && ($rsDetail[$i]['rate'] == 1 || $rsDetail[$i]['rate'] == 0))
                $this->addErrorLog(false, '<b>'.$rsDetail[$i]['code'].'</b>. '.$this->errorMsg['rate'][5]);      
            
            
            if($rsHeader[0]['ismaster'] == 0){
                $rsDetailItem = $this->getItemDetail($rsDetail[$i]['pkey']);
                if(empty($rsDetailItem))
                     $this->addErrorLog(false, '<b>'.$rsDetail[$i]['code'].'</b>. '.$this->errorMsg[501]);  
            }
             
        
        }  
        
        if(!empty($quotationKey)) {
            $rsEMKLQuotationOrder = $emklQuotationOrder->getDataRowById($quotationKey);
            $arrStatusQuotationKey = array(TRANSACTION_STATUS['menunggu'],TRANSACTION_STATUS['konfirmasi'],TRANSACTION_STATUS['batal']);

            if (!empty($rsEMKLQuotationOrder)) {
                if (in_array($rsEMKLQuotationOrder[0]['statuskey'], $arrStatusQuotationKey)) {
                    $this->addErrorLog(false, '<strong>' . $rsEMKLQuotationOrder[0]['code'] . '</strong>. ' . $this->errorMsg['emklQuotation'][5]);
                }
            } else {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' .$this->errorMsg['emklQuotation'][1]); 
            }
        }

        // validasi nilai rate gk boleh satu kalo bukan IDR
        $totalDetail = count($rsDetail);
        for ($i = 0; $i < $totalDetail; $i++) {
            if ($rsDetail[$i]['currencykey'] <> CURRENCY['idr'] && ($rsDetail[$i]['rate'] == 1 || $rsDetail[$i]['rate'] == 0)) {
                $this->addErrorLog(false, '<b>' . $rsDetail[$i]['code'] . '</b>. ' . $this->errorMsg['rate'][5]);
            }
        }

//
//        $selContainerType = $rsHeader[0]['loadcontainertypekey'];
//        $isMaster = $rsHeader[0]['ismaster'];

//        if(!$isFromJobHeader && ($selContainerType == EMKL['container']['fcl'] || ($selContainerType == EMKL['container']['lcl'] && !$isMaster))){ 
//            // $rsDetailVolume = $this->getDetailVolume($id);
//            // $arrDetailKey = array_column('pkey', $rsDetail);
//            // $rsItemDetail = $this->getItemDetail($arrDetailKey);
//
//            for($i=0; $i < count($rsDetail); $i++) {
//
//                $rsDetailItem = $this->getItemDetail($rsDetail[$i]['pkey']);
//
//                $arrDetailItem = array();
//                $totalItem = count($rsDetailItem);
//                for($j=0;$j<$totalItem;$j++){
//                    if(!in_array($rsDetailItem[$j]['itemkey'],$arrKeyVolume) && !empty($rsDetailItem[$j]['itemkey'])){
//                        $this->addErrorLog(false, '<b>'.$rsDetail[$i]['code'].' - '.$rsDetailItem[$j]['containername'].'</b>. '.$this->errorMsg['container'][3]); 
//                    }else if(empty($rsDetailItem[$j]['itemkey'])){
//                        $this->addErrorLog(false, '<b>'.$rsDetail[$i]['code'].'</b>. '.$this->errorMsg['container'][1]); 
//                    }
//                }
//            } 
//        }
    }		
	     	 
	function validateClose($rsHeader){
        
        parent::validateClose($rsHeader);
        
        $id = $rsHeader[0]['pkey'];
        

              $sql = 'select  
                    ' . $this->tableNameDetailItem.'.pkey 
                from  
                    ' . $this->tableNameDetailItem.',
                    ' . $this->tableNameDetail.',
                    ' . $this->tableName.'
                where  
                    ' . $this->tableNameDetailItem.'.refheaderkey = '.$this->oDbCon->paramString($id).' and  
                    ' . $this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and  
                    ' . $this->tableNameDetailItem.'.refkey = '.$this->tableNameDetail.'.pkey and  
                    (' . $this->tableNameDetailItem.'.qty > ' . $this->tableNameDetailItem.'.qtyinvoiced or ' . $this->tableNameDetailItem.'.qtyinvoiced = 0)
        ';
        
    
  /*      $sql = 'select  pkey from  ' . $this->tableNameDetailItem.'  where  
            refheaderkey = '.$this->oDbCon->paramString($id).' and  
            (qty > qtyinvoiced or qtyinvoiced = 0)
        ';*/

 
        // kalo yg job gk ad detail sama sekali ?
        
        $rs =  $this->oDbCon->doQuery($sql);
        if (!empty($rs)) 
             $this->addErrorLog(false, '<b>'.$rsHeader[0]['code'].'</b>. '.$this->errorMsg[506]);      
     
    }		
    
     function getJobType($pkey=''){ 
        
       $pkey = (!is_array($pkey) && !empty($pkey)) ? array($pkey) : $pkey;
         
	   $sql = 'select
	   			'.$this->tableJobType .'.pkey, 
	   			'.$this->tableJobType .'.name 
              from
			  	'.$this->tableJobType .' 
			  where
			  	'.$this->tableJobType .'.statuskey = 1';
                
        if(!empty($pkey))
            $sql .= ' and pkey in ('.$this->oDbCon->paramString($pkey,',').')';
        
        
        $sql .=' order by name asc';
         
		return $this->oDbCon->doQuery($sql);
	
   }
    
    function getTransportationType($pkey=''){ 
       $pkey = (!is_array($pkey) && !empty($pkey)) ? array($pkey) : $pkey;
       
	   $sql = 'select
	   			'.$this->tableTransportationType .'.pkey, 
	   			'.$this->tableTransportationType .'.name 
              from
			  	'.$this->tableTransportationType .' 
			  where
			  	'.$this->tableTransportationType .'.statuskey = 1';
       if(!empty($pkey))
            $sql .= ' and pkey in ('.$this->oDbCon->paramString($pkey,',').')';
        
       $sql .=' order by name asc';
         
       return $this->oDbCon->doQuery($sql);
	
   }
    
    function getLoadContainer($pkey=''){ 
       $pkey = (!is_array($pkey) && !empty($pkey)) ? array($pkey) : $pkey;
       
	   $sql = 'select
	   			'.$this->tableLoadContainer .'.pkey, 
	   			'.$this->tableLoadContainer .'.name 
              from
			  	'.$this->tableLoadContainer .' 
			  where
			  	'.$this->tableLoadContainer .'.statuskey = 1';
        
       if(!empty($pkey))
            $sql .= ' and pkey in ('.$this->oDbCon->paramString($pkey,',').') ';
        
       $sql .=' order by orderlist asc, pkey asc';
         
       return $this->oDbCon->doQuery($sql);
	
   }
    function getVolumeUnit($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableVolumeUnit .'.pkey, 
	   			'.$this->tableVolumeUnit .'.name 
              from
			  	'.$this->tableVolumeUnit .' 
			  where
			  	'.$this->tableVolumeUnit .'.statuskey = 1';
       
       if(!empty($pkey))
            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
        
       $sql .=' order by name asc';
         
		return $this->oDbCon->doQuery($sql);
	
   }
        function getDetailCommodity($pkey,$criteria = ''){ 
        
        $sql = 'select
                    '.$this->tableCommodityDetail .'.*, 
                    '.$this->tableCommodity.'.name as commodityname                
                from
                    '.$this->tableCommodityDetail .' 
                    left join ' .$this->tableCommodity.' on   ' .$this->tableCommodityDetail.'.commoditykey = '.$this->tableCommodity.'.pkey
                where 
                    '.$this->tableCommodityDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ')  '; 
        
            $sql .= $criteria;
            
            return $this->oDbCon->doQuery($sql);
        
    }
    
    function getFreightTerm($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableFreightTerm .'.pkey, 
	   			'.$this->tableFreightTerm .'.name 
              from
			  	'.$this->tableFreightTerm .' 
			  where
			  	'.$this->tableFreightTerm .'.statuskey = 1';
        
        if(!empty($pkey))
            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .=' order by pkey asc';
         
		return $this->oDbCon->doQuery($sql);
	
   }
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*,  
                '.$this->tableEmployee.'.name as salesname,     
                '.$this->tablePort.'.name as destinationname,   
                '.$this->tableItemUnit.'.name as unitname,
                '.$this->tableCustomer.'.name as customername,
                '.$this->tableCurrency.'.name as currencyname 
              from
			  	'.$this->tableNameDetail .'
                    left join '.$this->tableEmployee.' on 
                    '.$this->tableNameDetail .'.saleskey =  '.$this->tableEmployee.'.pkey 
                    left join '.$this->tablePort.' on 
                    '.$this->tableNameDetail .'.destinationkey =  '.$this->tablePort.'.pkey 
                    left join '.$this->tableItemUnit.' on 
                    '.$this->tableNameDetail .'.unitkey =  '.$this->tableItemUnit.'.pkey, 
                '.$this->tableCustomer.' , 
                '.$this->tableCurrency.' 
			  where 
			  	'.$this->tableNameDetail .'.customerkey = '.$this->tableCustomer.'.pkey and 
			  	'.$this->tableNameDetail .'.currencykey = '.$this->tableCurrency.'.pkey and 
			  	refkey in ('.$this->oDbCon->paramString($pkey,',') . ') 
              order by '.$this->tableNameDetail .'.pkey asc 
             ';
       
         $sql .= $criteria;
           
		return $this->oDbCon->doQuery($sql);
	
   }
    
    
    // khusus CIF
    function manipulateDataBeforeUpdateData($arrParam){
          
      if(isset($this->domainConfig)){
              
            if(!empty($this->domainConfig['cif'])){ 
				// CIF sementara manual semua
                //if(EMKL['jobType']['export'] == $this->jobType){
                //       // kalo tipe export
                //      
                //      // kalo tipe freight
                //    if(in_array($arrParam['selContainerType'], array(1,5,6,7))){ 
                //                  
                //          $sql = 'select '.$this->tableCity.'.code 
                //                from  
                //                    '.$this->tableCity.', 
                //                    '.$this->tablePort.'
                //                where
                //                    '.$this->tablePort.'.pkey = '.$this->oDbCon->paramString($arrParam['hidPOLKey']).' and
                //                    '.$this->tablePort.'.citykey = '.$this->tableCity.'.pkey
                //                ';
    //
                //            $rsCity = $this->oDbCon->doQuery($sql); 
                //            $cityCode = (!empty($rsCity)) ? $rsCity[0]['code'] : '';
    //
    //
                //          // kalo tipe air
                //          if ($arrParam['selAirSea'] == EMKL['shipping']['air']){ 
                //               $arrParam['paramPrefixCode'] = 'CIFA';
                //          }else{
                //               
                //                    $sql = 'select '.$this->tableContinent.'.code 
                //                        from 
                //                            '.$this->tableContinent.',
                //                            '.$this->tableCity.',
                //                            '.$this->tableCountry.' 
                //                        where
                //                            '.$this->tableCity.'.pkey = '.$this->oDbCon->paramString($arrParam['finaldestinationkey']).' and 
                //                            '.$this->tableCity.'.countrykey = '.$this->tableCountry.'.pkey and
                //                            '.$this->tableCountry.'.continentkey = '.$this->tableContinent.'.pkey
                //                        ';
    //
                //               $rsContinent = $this->oDbCon->doQuery($sql); 
                //               $continentCode = (!empty($rsContinent)) ? $rsContinent[0]['code'] : '';
    //
                //               $arrParam['paramPrefixCode'] = 'C'.$cityCode.$continentCode;
                //          }
                //         
                //      }else{
                //                $arrParam['paramPrefixCode'] = 'CIFLOG';
                //      }
                //      
                //}else if(EMKL['jobType']['import'] == $this->jobType){ 
                //        $arrParam['overwriteCode'] = true;
                //}
            } else if(!empty($this->domainConfig['emkofi'])){ 
                 
                if ($arrParam['selAirSea'] == EMKL['shipping']['air']){ 
                    $airSeaCode = 'A';
                } else if ($arrParam['selAirSea'] == EMKL['shipping']['sea']) {
                    $airSeaCode = 'S';
                }

                if(EMKL['jobType']['export'] == $this->jobType){
                    $arrParam['paramPrefixCode'] = 'FE'.$airSeaCode;
                } else if(EMKL['jobType']['import'] == $this->jobType){ 
                    $arrParam['paramPrefixCode'] = 'FI'.$airSeaCode;
                } else if(EMKL['jobType']['domestic'] == $this->jobType){ 
                    $arrParam['paramPrefixCode'] = 'DOM';
                } else if(EMKL['jobType']['warehouse'] == $this->jobType){ 
                    $arrParam['paramPrefixCode'] = 'WHS';
                } else if(EMKL['jobType']['trucking'] == $this->jobType){ 
                    $arrParam['paramPrefixCode'] = 'TRK';
                } else {
                    $arrParam['paramPrefixCode'] = 'OTH';
                }

            } 
        }  
		
        return $arrParam;
    }
    

	  
    function normalizeParameter($arrParam, $trim=false){
 
        $details = array();
        array_push($details,$this->arrItem); 
        $arrParam = $this->prepareMultiLevelDetail($arrParam,$details);

        $useJobOrderHeader = $this->loadSetting('useJobOrderHeader'); 
        $updateTaxAtJobOrder = $this->loadSetting('updateTaxAtJobOrder');

		// hanya jika edit
	  	if(!empty($arrParam['hidId'])){  
			$rsInvoiced = $this->getInvoiceInformation($arrParam['hidId']); 
				
			//jika tidak pakai header dan sudah ada invoice status konfirmasi / selesai
			if($useJobOrderHeader == 2 && !empty($rsInvoiced)) {
				$rsHeader = $this->getDataRowById($arrParam['hidId']); 
				$arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
				$arrParam['consigneeName'] = $rsHeader[0]['consigneename']; 
			}
	  	}
		 
      
        //Overwride Flag
        $vessel = new Vessel();
        $vesselkey = $arrParam['hidVesselKey'];
        $rsVessel = $vessel->getDataRowById($vesselkey);

        $arrParam['flag'] = $rsVessel[0]['flag'];


        if(empty($arrParam['chkIsOverwriteNotifyParty'])) {
            $consignee = new Consignee();
            $rsNotifyParty = $consignee->getDataRowById($arrParam['hidNotifyPartyKey']);

            if(!empty($rsNotifyParty)){

                        $arrParam['notifyPartyName1'] = $rsNotifyParty[0]['name'];
                        $arrParam['notifyPartyAddress1'] = $rsNotifyParty[0]['address'];
            }else{

                        $arrParam['notifyPartyName1'] ='';
                        $arrParam['notifyPartyAddress1'] ='';
            }

        }

  
        // ambil ulang data dr header 
         
		// jgn divalidasi, karena udara ad kemungkinan bisa document only
        //if ($arrParam['selAirSea'] == EMKL['shipping']['air']) $arrParam['selContainerType'] =  EMKL['container']['lcl'];
            
		$arrParam['selVolumeType'] = ($arrParam['selAirSea'] == EMKL['shipping']['air']) ?  EMKL['volume']['kg'] : EMKL['volume']['cbm'];
        $arrParam['selTypeOfJob'] = $this->jobType; 
        $arrParam['isFromJobHeader'] = (isset($arrParam['isFromJobHeader']) && !empty($arrParam['isFromJobHeader'])) ?  $arrParam['isFromJobHeader'] : 0;
        
     
        if ( in_array($arrParam['selContainerType'], 
                      array(EMKL['container']['lcl'],
                            EMKL['container']['lclnc'],
                            EMKL['container']['freightcustomlcl'],
                            EMKL['container']['customlcl'] 
                           ))){
            // kalo LCL 
            $arrParam['hidConsigneeKey'] = 0; 
            $isMaster = $arrParam['chkIsMaster'];
            
            for($i=0;$i<count($arrParam['hidContainerDetailKey']);$i++){ 
                $arrParam['hidContainerDetailKey'][$i] =  $arrParam['hidContainerKey'];
            }
            
            // kalo detail 
            if (!$isMaster){ 
				
//				$this->setLog("not master",true);
				
                $jobOrderKey = $arrParam['hidJobOrderKey']; 
                $rsHeader = $this->getDataRowById($jobOrderKey); 
                if(!empty($rsHeader)){
                    
                    // ini baru diaktifin kalo pake kode otomatis
                    //$totalLCL = $this->getTotalLCL( $jobOrderKey ); 
                    //$arrParam['code'] = $rsHeader[0]['code'].'/'.strval($totalLCL+1); 
                      
                    $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey']; 
                    $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y '); 
                    $arrParam['poNumber'] = $rsHeader[0]['ponumber'];
                    $arrParam['bookingNumber'] = $rsHeader[0]['bookingnumber'];
                    $arrParam['selTypeOfJob'] = $rsHeader[0]['jobtypekey'];
                    $arrParam['selAirSea'] = $rsHeader[0]['transportationtypekey'];
                    $arrParam['selContainerType'] = $rsHeader[0]['loadcontainertypekey'];
                    $arrParam['hidContainerKey'] = $rsHeader[0]['itemkey'];
                    $arrParam['selVolumeType'] = $rsHeader[0]['volumetype'];
                    $arrParam['mblNumber'] = $rsHeader[0]['mblnumber'];
                    $arrParam['hidPOLKey'] = $rsHeader[0]['polkey'];
                    //$arrParam['hidPODKey'] = $rsHeader[0]['podkey']; // POD boleh diubah
                    $arrParam['etdPol'] =  $this->formatDBDate($rsHeader[0]['etdpol'],'d / m / Y '); 
                    $arrParam['etaPod'] =  $this->formatDBDate($rsHeader[0]['etapod'],'d / m / Y '); 
                    $arrParam['hidCarrierKey'] = $rsHeader[0]['carrierkey']; 
                    $arrParam['hidVesselKey'] = $rsHeader[0]['vesselkey'];
                    $arrParam['vesselNumber'] = $rsHeader[0]['vesselnumber'];
                    $arrParam['hidAgentKey'] = $rsHeader[0]['agentkey'];
                    $arrParam['hidDepotKey'] = $rsHeader[0]['depotkey'];
                    $arrParam['containerNumber'] = $rsHeader[0]['containernumber'];
                    $arrParam['hidCargoType'] = $rsHeader[0]['containertypekey']; 
					
					// sementara 
					//if ($arrParam['code'] != 'E-2106371-00')
                    $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey']; 
                     
                    //$this->arrData['closingdate'] = array('closingDate','date');    
                    
                }                 
            }
            
        }else{
            // kalo FCL 
            $arrParam['hidContainerKey'] = 0; // karena 1 form bisa lebih dr 1 jenis container
            $arrParam['volume'] = 0; 
            $arrParam['weight'] = 0; 
            
            for($i=0;$i<count($arrParam['hidContainerDetailKey']);$i++){ 
                $arrParam['hidSalesDetailKey'][$i] =  0;
                
                if ($arrParam['selAirSea'] == EMKL['shipping']['air'])
                    $arrParam['hidContainerDetailKey'][$i] = 0;
            } 
       
        }
         
        // kalo bkn fcl & trucking
        if($arrParam['selContainerType'] != EMKL['container']['fcl'] && 
           $arrParam['selContainerType'] != EMKL['container']['trucking'] && 
           $arrParam['selContainerType'] != EMKL['container']['freightcustomfcl'] && 
           $arrParam['selContainerType'] != EMKL['container']['customfcl']
          
          ){
            $arrParam['selContainerDetailVolumeKey'] = array();
            $arrParam['qtyVolume'] = array(); 
        }       
        
        
        for($i=0;$i<count($arrParam['hidDetailKey']);$i++) 
            $arrParam['salesOrderCode'][$i] = $arrParam['code'] . '-'. ($i+1); 
        
		
		
		// update admin fee
		// GL nanti ketika konfirmasi
		// hanya jika add
		if(empty($arrParam['hidId'])){  
			
			$warehouse = new Warehouse();

			$rsWarehouse = $warehouse->searchDataRow(array($warehouse->tableName.'.pkey',$warehouse->tableName.'.saleskey',$warehouse->tableName.'.defaultcommission'),
													' and '. $warehouse->tableName.'.pkey = '.$this->oDbCon->paramString($arrParam['selWarehouseKey'])
													 );

			if($rsWarehouse[0]['defaultcommission'] > 0 ) {  
				// utk LCL, yg diupdate detailnya. headernya di set 0 dulu
				// LCL NC, ismasterny selalu 0, dianggap sama dengan FCL   
				if( $rsJO[0]['loadcontainertypekey'] == EMKL['emklType']['lcl'] && $row['ismaster'] == 1 ){ 
						// tipe LCL, dan master, tdk ad admin fee dulu
				}else{
					$arrParam['totalEmployeeCommission'] = $rsWarehouse[0]['defaultcommission'];
				} 
			}

		}
		
		
        $arrCustomerKey = $arrParam['hidCustomerDetailKey'];  
        $arrServicekeyDetail = $arrParam['hidServiceKey'];
        $arrChkIsManual = $arrParam['chkIsManual'];
        $arrRate = $arrParam['sellingCurrencyRate'];
        
        $customer = new Customer();
        $rsCustomer = $customer->searchDataRow(   array($customer->tableName.'.pkey', $customer->tableName.'.name'),  ' and '.$customer->tableName.'.pkey in ('.$this->oDbCon->paramString($arrCustomerKey,',').')' );
        $rsCustomer = array_column($rsCustomer, 'name');

        $arrParam['customercache'] = implode(', ',$rsCustomer);   

         
        $reCountResult = $this->reCountSubtotal($arrParam);    
        $arrParam['totalSelling'] = $reCountResult['totalSelling'];
        $arrParam['grandtotal'] = $reCountResult['grandTotal'];
        $arrParam['taxValue'] = $reCountResult['taxValue'];
        $recountDetail = $reCountResult['detail'];
        $recountDetailItem = $reCountResult['detailItem'];
 
        
        for ($i=0;$i<count($arrCustomerKey);$i++){
            
            if (empty($arrCustomerKey[$i]) || $arrRate[$i] <= 0) continue;
                        
            if (!isset($arrParam['hidInvoiceToKey']) || empty($arrParam['hidInvoiceToKey']) || $arrCustomerKey[$i] == $arrParam['hidCustomerKey'])
                $arrParam['hidInvoiceToKey'] = $arrCustomerKey[$i];
             
            if($arrChkIsManual[$i] == 1)  
				$arrParam['hidDetailHBLKey'][$i] = 0;
            
            
            $arrParam['detailCurrencyTotal'][$i] = $recountDetail['detailCurrencyTotal'][$i];
            $arrParam['detailOtherCurrencyTotal'][$i] =  $recountDetail['detailOtherCurrencyTotal'][$i];
            $arrParam['detailTotal'][$i] =  $recountDetail['detailTotal'][$i];

            
            $arrServicekey = $arrServicekeyDetail[$i];
            
            for($j=0;$j<count($arrServicekey);$j++){ 
                $arrParam['taxDetail'][$i][$j]  =  $recountDetailItem['taxDetail'][$i][$j];
                $arrParam['taxDetailValue'][$i][$j]  =  $recountDetailItem['taxDetailValue'][$i][$j];
                $arrParam['beforeTaxDetail'][$i][$j]  =  $recountDetailItem['beforeTaxDetail'][$i][$j];
                $arrParam['afterTaxDetail'][$i][$j]  =  $recountDetailItem['afterTaxDetail'][$i][$j];
                $arrParam['detailRowCurrencySubtotal'][$i][$j]  =  $recountDetailItem['detailRowCurrencySubtotal'][$i][$j];
                $arrParam['detailRowSubtotal'][$i][$j]  =  $recountDetailItem['detailRowSubtotal'][$i][$j]; 
            }
             
            
        }
        
  
        $arrParam = parent::normalizeParameter($arrParam,true);
        
        return $arrParam;
    }
    
   
    function getItemDetail($refkey='', $refdetailkey = '', $refheaderkey = '', $criteria = ''){
        
        if(empty($refkey) && empty($refdetailkey) && empty($refheaderkey)) return array(); // utk jaga2
            
        $sql = 'select ' .$this->tableNameDetailItem.'.*,
               '.$this->tableItem.'.name as servicename,
               '.$this->tableServiceCategory.'.name as categoryname,
               '.$this->tableContainer.'.name as containername,
               '.$this->tableCurrency.'.name as currencyname
               from 
               ' .$this->tableNameDetailItem.'
				   left join ' .$this->tableItem.' on   ' .$this->tableNameDetailItem.'.servicekey = '.$this->tableItem.'.pkey
                   left join ' . $this->tableServiceCategory . ' on  ' . $this->tableItem . '.categorykey = ' . $this->tableServiceCategory . '.pkey
				   left join '.$this->tableContainer.' on  '.$this->tableNameDetailItem.'.itemkey = '.$this->tableContainer.'.pkey, 
               ' .$this->tableCurrency.'
                where  
				' .$this->tableNameDetailItem.'.currencykey = '.$this->tableCurrency.'.pkey' ;
        
        if (!empty($refkey))
            $sql .= ' and ' .$this->tableNameDetailItem.'.refkey = ' . $this->oDbCon->paramString($refkey);
        
        if (!empty($refdetailkey))
            $sql .= ' and ' .$this->tableNameDetailItem.'.pkey in (' . $this->oDbCon->paramString($refdetailkey,',').')';

        if (!empty($refheaderkey))
            $sql .= ' and ' .$this->tableNameDetailItem.'.refheaderkey in (' . $this->oDbCon->paramString($refheaderkey,',').')';

        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        return $this->oDbCon->doQuery($sql);
    }
        
 
        
    function getTotalQtyInvoiced($pkey,$detailkey){ 
          
        $emklOrderInvoice = new EMKLOrderInvoice(); 
        
        // update setiap SO, sudah brp qty yg ditagih, item dan cost
        $sql = 'select 
                    coalesce(sum(qtyinbaseunit),0) as totalinvoiced
                from  
                    '.$emklOrderInvoice->tableName.',  
                    '.$emklOrderInvoice->tableNameDetail.',
                    '.$emklOrderInvoice->tableNameItemDetail.' 
                where 
                    '.$emklOrderInvoice->tableName.'.pkey = '.$emklOrderInvoice->tableNameDetail.'.refkey and
                    '.$emklOrderInvoice->tableNameDetail.'.pkey = '.$emklOrderInvoice->tableNameItemDetail.'.refkey and
                    '.$emklOrderInvoice->tableName.'.statuskey in (2,3) and
                    '.$emklOrderInvoice->tableNameDetail.'.salesorderkey = '.$this->oDbCon->paramString($pkey).' and
                    '.$emklOrderInvoice->tableNameItemDetail.'.refsodetailkey = '.$this->oDbCon->paramString($detailkey).' 
                ';
 
        //$this->setLog($sql,true);
        $rsTotal = $this->oDbCon->doQuery($sql);

        return $rsTotal[0]['totalinvoiced'];
    }
    
    
     function updateQtyInvoiced($salesOrderKey,$isValidated = false){  
                 //$salesOrderKey bukan pkey, tp detailkey dalam job order
         
        $arrayToJs = array();
        
        $rsSalesDetail = $this->getDetailByColumn('pkey',$salesOrderKey);
        $pkey = $rsSalesDetail[0]['refkey'];
        $rsHeader = $this->getDataRowById($pkey);
        
        // kemungkinan detailnya sudah dihapus dulu sebelum invoice
        if(empty($rsHeader)) return;
         
        /* $this->setLog('$rsSalesDetail = ', true);
         $this->setLog($rsSalesDetail, true);*/
              
        // update setiap SO, sudah brp qty yg ditagih 
        
        foreach($rsSalesDetail as $salesRow){
                  
            //$this->setLog('salesrow pkey : ' . $salesRow['pkey'], true);
            $rsItemDetail = $this->getSalesDetail($salesRow['pkey']);
                
            foreach($rsItemDetail as $itemRow){
                 
                if(!$isValidated)
                    $totalInvoiced = $this->getTotalQtyInvoiced($salesOrderKey,$itemRow['pkey']);
                else
                    $totalInvoiced = $itemRow['qty'];
                
                $sql = 'update ' . $this->tableNameDetailItem.' set  qtyinvoiced = '.$this->oDbCon->paramString($totalInvoiced).'  where  pkey = '.$this->oDbCon->paramString($itemRow['pkey']);
                $this->oDbCon->execute($sql);

            }
             
        } 
         
         // harus join sama detail, karena ad bug level kedua kehapus, level ketiga tetep ad
        $sql = 'select 
                    ' . $this->tableNameDetailItem.'.pkey 
                from
                    ' . $this->tableName.' ,
                    ' . $this->tableNameDetail.' ,
                    ' . $this->tableNameDetailItem.'  
                where  
                    ' . $this->tableName.'.pkey = ' . $this->tableNameDetail.'.refkey and
                    ' . $this->tableNameDetail.'.pkey = ' . $this->tableNameDetailItem.'.refkey and
                    ' . $this->tableNameDetailItem.'.refheaderkey = '.$this->oDbCon->paramString($pkey).' and 
                    ' . $this->tableNameDetailItem.'.qty > ' . $this->tableNameDetailItem.'.qtyinvoiced 
                ';
          
         
        $rs =  $this->oDbCon->doQuery($sql);
        if (empty($rs)){ 
            if($rsHeader[0]['statuskey'] <> 3)
                $arrayToJs = $this->changeStatus($pkey,3,'',false,true);
        }else{ 
            if ($rsHeader[0]['statuskey'] == 3) 
                $arrayToJs = $this->changeStatus($pkey,2,'',false,true);
        }
        
        // cek utk SO, semua sudah tertagih atau blm. lalu ubah status 
   /*     $sql = 'SELECT * from ( 
                    select  pkey from   ' . $this->tableNameDetail.'  where  refkey = '.$this->oDbCon->paramString($pkey).' and  qtyinbaseunit > qtyinvoiced UNION 
                    select  pkey from   ' . $this->tableSellingCost.'  where  refkey = '.$this->oDbCon->paramString($pkey).' and  qty > qtyinvoiced 
                ) trans ';
         
        $rs =  $this->oDbCon->doQuery($sql);
        
        if (empty($rs)) { 
            if($rsHeader[0]['statuskey'] <> 6)
                $arrayToJs = $this->changeStatus($pkey,6,'',false,true);
        }else{ 
            if ($rsHeader[0]['statuskey'] == 6) 
                $arrayToJs = $this->changeStatus($pkey,5,'',false,true);
        }*/
        
        return $arrayToJs;
           
    }
     
    function getSalesDetail ($refkey){
        $sql = 'select  
                    '.$this->tableNameDetailItem.'.*,
                    '.$this->tableItem.'.name as servicename,
                    '.$this->tableItem.'.iscontainer,
                    '.$this->tableContainer.'.name as containername 
                from 
                    '.$this->tableNameDetailItem.'
                        left join '.$this->tableItem.' on '.$this->tableNameDetailItem.'.servicekey = '.$this->tableItem.'.pkey 
                        left join '.$this->tableContainer.' on '.$this->tableNameDetailItem.'.itemkey = '.$this->tableContainer.'.pkey 
                where  
                    '.$this->tableNameDetailItem.'.refkey in (' .$this->oDbCon->paramString($refkey,',').')';
        
		
        return $this->oDbCon->doQuery($sql);
    }
    
   function getInvoiceInformation($pkey){
        $emklOrderInvoice = new EMKLOrderInvoice();
      
        $sql = 'select
            '.$emklOrderInvoice->tableName.'.code,    
            '.$emklOrderInvoice->tableName.'.trdate,
            '.$emklOrderInvoice->tableName.'.isdownpayment,
            '.$emklOrderInvoice->tableName.'.customerkey,
            '.$emklOrderInvoice->tableName.'.pkey,
            '.$emklOrderInvoice->tableNameDetail.'.refsalesorderheaderkey,
            '.$emklOrderInvoice->tableNameDetail.'.amount,
            '.$emklOrderInvoice->tableNameDetail.'.salesorderkey as refdetailkey
          from 
            '.$emklOrderInvoice->tableName.',
            '.$emklOrderInvoice->tableNameDetail.'
          where  
            '. $emklOrderInvoice->tableNameDetail.'.refsalesorderheaderkey in ('.$this->oDbCon->paramString($pkey,',') .')  and   
            '. $emklOrderInvoice->tableName.'.pkey = '. $emklOrderInvoice->tableNameDetail.'.refkey and
            '. $emklOrderInvoice->tableName.'.statuskey in (2,3) ';
 
        return $this->oDbCon->doQuery($sql);

    }
    
  function getInvoiceDetailItem($refkey) 
    {
	   // THEWHALE, invocie pending jg boleh muncul di NOA
        $emklOrderInvoice = new EMKLOrderInvoice();

        $sql = '
            select 
                '. $emklOrderInvoice->tableNameItemDetail  .'.*,
                '. $emklOrderInvoice->tableNameDetail  .'.pkey as detailkey,
                '. $emklOrderInvoice->tableName .'.pkey as headerkey,
                '. $emklOrderInvoice->tableName .'.statuskey,
                '. $emklOrderInvoice->tableItem .'.name as servicename,
                '. $emklOrderInvoice->tableCurrency .'.name as currencyname,
                '. $this->tableContainer .'.name as containername
            from 
                '. $emklOrderInvoice->tableNameItemDetail .'
                    left join '. $emklOrderInvoice->tableItem .' on '. $emklOrderInvoice->tableNameItemDetail .'.itemkey = '. $emklOrderInvoice->tableItem .'.pkey
                    left join '. $this->tableContainer .' on '. $emklOrderInvoice->tableNameItemDetail .'.containerkey = '. $this->tableContainer .'.pkey
                    left join '. $emklOrderInvoice->tableCurrency.' on '. $emklOrderInvoice->tableNameItemDetail .'.currencykey = '. $emklOrderInvoice->tableCurrency .'.pkey,
                '.$emklOrderInvoice->tableNameDetail.',
                '.$emklOrderInvoice->tableName.'
            where
                '. $emklOrderInvoice->tableNameItemDetail .'.refsoheaderkey in ('. $this->oDbCon->paramString($refkey,',') .') and
                '. $emklOrderInvoice->tableNameItemDetail .'.refkey = '. $emklOrderInvoice->tableNameDetail .'.pkey and
                '. $emklOrderInvoice->tableNameItemDetail .'.refheaderkey = '. $emklOrderInvoice->tableName .'.pkey and
                '. $emklOrderInvoice->tableName.'.statuskey <> 4
        ';

        return $this->oDbCon->doQuery($sql);
    }

   function getAmountInvoiced($pkey){
       // pisahkan dr yg atas agar tidak mengganggu performance yg lain
       // tidak termasuk PPN
        $emklOrderInvoice = new EMKLOrderInvoice();
        
        $rsKey = $this->getTableKeyAndObj($emklOrderInvoice->tableName,array('key'));  
      
        $sql = 'select
            '.$emklOrderInvoice->tableName.'.code,    
            '.$emklOrderInvoice->tableName.'.trdate,
            '.$emklOrderInvoice->tableName.'.confirmedon,
            '.$emklOrderInvoice->tableName.'.receiptdt,
            '.$emklOrderInvoice->tableName.'.isdownpayment,
            '.$emklOrderInvoice->tableName.'.customerkey,
            '.$emklOrderInvoice->tableName.'.tax23value,
            '.$emklOrderInvoice->tableName.'.ispriceincludetax,
            '.$emklOrderInvoice->tableName.'.taxpercentage,
            '.$emklOrderInvoice->tableName.'.pkey,
            '.$emklOrderInvoice->tableNameDetail.'.refsalesorderheaderkey,
            '.$emklOrderInvoice->tableARStatus.'.status as arstatusname,
            '.$emklOrderInvoice->tableARStatus.'.pkey as arstatuskey,
            coalesce(sum('.$emklOrderInvoice->tableNameItemDetail.'.qtyinbaseunit * 
                         '.$emklOrderInvoice->tableNameItemDetail.'.priceinunit * 
                         IF ('.$emklOrderInvoice->tableNameItemDetail.'.currencykey = '.$this->oDbCon->paramString(CURRENCY['idr']).', 1, '.$emklOrderInvoice->tableNameItemDetail.'.rate) 
                         ),0) as amount,
            '.$emklOrderInvoice->tableNameDetail.'.salesorderkey as refdetailkey
            from 
            '.$emklOrderInvoice->tableName.'
                left join '.$emklOrderInvoice->tableAR.' on '.$emklOrderInvoice->tableAR.'.reftabletype = '.$this->oDbCon->paramString($rsKey['key']).' and '.$emklOrderInvoice->tableAR.'.refkey = '.$emklOrderInvoice->tableName.'.pkey 
                left join '.$emklOrderInvoice->tableARStatus.' on '.$emklOrderInvoice->tableAR.'.statuskey = '.$emklOrderInvoice->tableARStatus.'.pkey and
                          '.$emklOrderInvoice->tableAR.'.statuskey <> 4,
            '.$emklOrderInvoice->tableNameDetail.',
            '.$emklOrderInvoice->tableNameItemDetail.'
          where  
            '. $emklOrderInvoice->tableNameDetail.'.refsalesorderheaderkey in ('.$this->oDbCon->paramString($pkey,',') .')  and   
            '. $emklOrderInvoice->tableName.'.pkey = '. $emklOrderInvoice->tableNameDetail.'.refkey and
            '. $emklOrderInvoice->tableNameDetail.'.pkey = '. $emklOrderInvoice->tableNameItemDetail.'.refkey and
            '. $emklOrderInvoice->tableName.'.statuskey in (2,3) 
         group by ('. $emklOrderInvoice->tableNameDetail.'.pkey)    
        ';
        
        return $this->oDbCon->doQuery($sql);

    }
    

    function getAmountCost($pkey, $inIDR = true, $includeTax = true) {

        // $includeTax : kedepan buat bisa pilih before tax 
        // kalo model purchasenya JO nya di header
        
		// tax value sementara hanya sum utk total IDR 
		
        $fieldTotal = ($includeTax) ? 'grandtotal' : 'beforetaxtotal';
        
        $sql = 'select   
                '. $this->tablePurchase .'.refkey as jokey,
                '. $this->tableName .'.code as jocode,
                '. $this->tableCustomer .'.name as customername,
            ';

        if($inIDR) {
            $sql .='
                coalesce(sum('.$this->tablePurchase .'.'.$fieldTotal.' * IF('.$this->tablePurchase .'.currencykey = '.CURRENCY['idr'].',1 ,'.$this->tablePurchase .'.rate)),0) as amount,
				coalesce(sum('.$this->tablePurchase .'.taxvalue  * IF('.$this->tablePurchase .'.currencykey = '.CURRENCY['idr'].',1 ,'.$this->tablePurchase .'.rate)),0) as taxvalue,
				1 as rate
            ';
        } else {
            $sql .='
                    '. $this->tablePurchase .'.currencykey, 
                    '. $this->tablePurchase .'.rate, 
                    coalesce(sum('.$this->tablePurchase .'.'.$fieldTotal.'),0) as amount, 
                    coalesce(sum('.$this->tablePurchase .'.taxvalue),0) as taxvalue
            ';
        }

        $sql .= ' from 
                    '. $this->tablePurchase . ',
                    '. $this->tableName .',
                    '. $this->tableCustomer .'
                where 
                    '. $this->tablePurchase .'.refkey = '. $this->tableName .'.pkey and
                    '. $this->tableName .'.customerkey = '. $this->tableCustomer .'.pkey and
                    '. $this->tablePurchase . '.refkey in ('. $this->oDbCon->paramString($pkey,',') .') and
                ('.$this->tablePurchase .'.statuskey = 2 or '.$this->tablePurchase .'.statuskey = 3)';

        if($inIDR) {
            $sql .=' group by '.$this->tablePurchase .'.refkey';
        } else {
            $sql .=' group by '.$this->tablePurchase .'.refkey,  '.$this->tablePurchase .'.currencykey';
        }
		
        $rs = $this->oDbCon->doQuery($sql); 

        return $rs;
    }
    


    
  function countTotalBuying($pkey){  
//        $rsHeader = $this->getDataRowById($pkey);  
 	
        //  * rate
        $sql = 'select   
                  coalesce(sum('.$this->tablePurchase .'.beforetaxtotal * IF('.$this->tablePurchase .'.currencykey = '.CURRENCY['idr'].',1 ,'.$this->tablePurchase .'.rate)),0) as totalbuying 
            from 
                '. $this->tablePurchase . '
            where 
                 '. $this->tablePurchase . '.refkey = '. $this->oDbCon->paramString($pkey) .' and
                 (statuskey = 2 or statuskey = 3)';
             
        $rs = $this->oDbCon->doQuery($sql); 
        return $rs[0]['totalbuying'];
    }
    

    
    function updateTotalBuying($pkey){   
        $total = $this->countTotalBuying($pkey);

        $sql = 'update 
                    ' . $this->tableName.' 
                set  
                    totalbuying = '. $this->oDbCon->paramString($total).' 
                where 
                    pkey = '.$this->oDbCon->paramString($pkey) ;
              
        $this->oDbCon->execute($sql);  
    }
 
 
    function updateSubtotalDetail($arrPkey) {

          $sql = 'select
            '.$this->tableNameDetailItem .'.refkey,   
            coalesce(sum('.$this->tableNameDetailItem .'.aftertaxdetailvalue * IF('.$this->tableNameDetailItem .'.currencykey = '.CURRENCY['idr'].',1 ,'.$this->tableNameDetail .'.rate)),0) as subtotal, 
            coalesce(sum(
                IF('.$this->tableNameDetail.'.currencykey = '.$this->tableNameDetailItem.'.currencykey, '.$this->tableNameDetailItem.'.aftertaxdetailvalue, 0)
            ), 0) AS subtotalcurrency,
            coalesce(sum(
                IF('.$this->tableNameDetail.'.currencykey <> '.$this->tableNameDetailItem.'.currencykey, '.$this->tableNameDetailItem.'.aftertaxdetailvalue, 0)
            ), 0) AS subtotalothercurrency
            from 
                '. $this->tableNameDetailItem . '
                left join '. $this->tableNameDetail .' on '. $this->tableNameDetailItem .'.refkey = '. $this->tableNameDetail .'.pkey
            where 
                '. $this->tableNameDetailItem . '.refkey in ('. $this->oDbCon->paramString($arrPkey,',') .') group by refkey
        ';
            
        $rsTotal = $this->oDbCon->doQuery($sql);
        
        

        for($i=0; $i<count($rsTotal); $i++) {
            $sql = '
                UPDATE
                    '. $this->tableNameDetail .'
                SET
                    subtotal = '. $this->oDbCon->paramString($rsTotal[$i]['subtotal']) .',
                    subtotalcurrency = '. $this->oDbCon->paramString($rsTotal[$i]['subtotalcurrency']) .',
                    subtotalothercurrency = '. $this->oDbCon->paramString($rsTotal[$i]['subtotalothercurrency']) .'   
                WHERE
                    '. $this->tableNameDetail .'.pkey = '. $this->oDbCon->paramString($rsTotal[$i]['refkey']) .' 
                ';

            $this->oDbCon->execute($sql);

        }  
    }
 

    function updateTotalSelling($pkey) {

        $rsDetail = $this->getDetailById($pkey);
        $arrJODetailKey = array_column($rsDetail,'pkey');
        
        $this->updateSubtotalDetail($arrJODetailKey);
        
        
        $sql = 'select  coalesce(sum('. $this->tableNameDetail .'.subtotal),0) as totalselling  from  '. $this->tableNameDetail .' 
                where '. $this->tableNameDetail .'.refkey = '. $this->oDbCon->paramString($pkey);

        $rs = $this->oDbCon->doQuery($sql);

        $total = $rs[0]['totalselling'];
 

        $sql = '
            update  '. $this->tableName .'
            set  totalselling = '. $this->oDbCon->paramString($total) .'
            where '. $this->tableName .'.pkey = '.$this->oDbCon->paramString($pkey).'
        ';

        $this->oDbCon->execute($sql);

    }
    
  	function countTotalEmployeeCommission($pkey){   
//      
//        $rsHeader = $this->getDataRowById($pkey);  
 	
        $sql = 'select 
                coalesce(sum('.$this->tableEmployeeCommission .'.amount),0) as totalcommission
            from 
                '. $this->tableEmployeeCommission . '
            where 
                 '. $this->tableEmployeeCommission . '.refkey = '. $this->oDbCon->paramString($pkey) .' and
                 '. $this->tableEmployeeCommission . '.statuskey in (1,2,3)';
            
//		$this->setLog($sql,true);
		
        $rs = $this->oDbCon->doQuery($sql); 
        return $rs[0]['totalcommission'];
    }
	
	
    function updateTotalEmployeeCommission($pkey){
		 
        $total = $this->countTotalEmployeeCommission($pkey);

        $sql = 'update 
                    ' . $this->tableName.' 
                set  
                    totalemployeecommission = '. $this->oDbCon->paramString($total).' 
                where 
                    pkey = '.$this->oDbCon->paramString($pkey) ;
            
        $this->oDbCon->execute($sql);  
	}
	
	
  	function countTotalCommission($pkey){   
//      
//        $rsHeader = $this->getDataRowById($pkey);  
 	
        $sql = 'select 
                coalesce(sum('.$this->tableCommission .'.grandtotal * '.$this->tableCommission .'.rate),0) as totalcommission
            from 
                '. $this->tableCommission . '
            where 
                 '. $this->tableCommission . '.refkey = '. $this->oDbCon->paramString($pkey) .' and
                 (statuskey = 2 or statuskey = 3)';
            
        $rs = $this->oDbCon->doQuery($sql); 
        return $rs[0]['totalcommission'];
    }
	
    function updateTotalCommission($pkey){
        
        $total = $this->countTotalCommission($pkey);

        $sql = 'update 
                    ' . $this->tableName.' 
                set  
                    totalcommission = '. $this->oDbCon->paramString($total).' 
                where 
                    pkey = '.$this->oDbCon->paramString($pkey) ;
            
        $this->oDbCon->execute($sql);  
    }
	
	function updateTotalCreditNote($pkey){
		$creditNote = new CreditNote();
		
		// cari JO ad di invoice mana saja yg connect dengan CN
		$arrJO = array();
		$rsCNCol =  $creditNote->getCreditNoteByEMKLJO($pkey);
		foreach($rsCNCol as $row)  
			$arrJO = array_merge($arrJO, array_column($row,'jokey'));  
	 	
		$arrJO = array_unique($arrJO);
		
		
		// cari CN yg masih aktif
		$rsCNCol = $creditNote->getCreditNoteByEMKLJO($pkey,' and '.$creditNote->tableName.'.statuskey in (2,3)');
		
		$arrJOCN = array();
		foreach($rsCNCol as $row){ 
			foreach($row as $detailRow){
				$jokey = $detailRow['jokey'];
				if(!isset($arrJOCN[$jokey])) $arrJOCN[$jokey] = 0; 
				$arrJOCN[$jokey] += $detailRow['totalcredit'] *  $detailRow['rate']; 
			} 
		}

			
		foreach($arrJO as $jokey){ 
			
			$totalCreditNote = ( isset($arrJOCN[$jokey]) ) ? $arrJOCN[$jokey] : 0 ;
				
			$sql = 'update '.$this->tableName.' set totalcreditnote = ' . $this->oDbCon->paramString($totalCreditNote) .' 
					where pkey = ' .  $this->oDbCon->paramString($jokey);
			
			//$this->setLog($sql,true);
			$this->oDbCon->execute($sql);

		}
		 
	}
       
    function afterAddDataOnCopy($pkey, $oldkey){
        // reset invoiced qty
        $sql = 'update '.$this->tableNameDetailItem.' set qtyinvoiced = 0 where refheaderkey =  ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql); 
		
		
	  	if($this->isActiveMasterHBL) {  
			
			// gk perlu di reset, di HBL nya yg di linnk ulang
			//link ulang di HBL
			
			$rsHeader = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.code'),
										  ' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey)
										 );
			
			$rsDetail = $this->getDetailById($pkey);
			 
			foreach($rsDetail as $detailRow){ 
				if(empty($detailRow['hblkey'])) continue;
				
				$sql = 'update '.$this->tableHBL.' 
						set 
							refkey = '.$this->oDbCon->paramString($detailRow['pkey']).' ,
							refcode = '.$this->oDbCon->paramString($detailRow['code']).' ,
							refheaderkey = '.$this->oDbCon->paramString($pkey).' ,
							refheadercode = '.$this->oDbCon->paramString($rsHeader[0]['code']).' 
						where 
							pkey = ' .$this->oDbCon->paramString($detailRow['hblkey']);
					
				$this->oDbCon->execute($sql); 
			}
			
		}
		
        
    }
    
    function getDetailParty($detailkey){
        $arrParty = array();
        
        $rsDetailItem = $this->getSalesDetail($detailkey);
          
        /*if($detailkey == 4221)
            $this->setLog($rsDetailItem,true);*/
            
        foreach($rsDetailItem as $row){
            if($row['iscontainer'] == 0) continue;
            
            $containerkey = $row['itemkey'];
            
            if(!isset($arrParty[$containerkey])) $arrParty[$containerkey] = array('container' => $row['containername'], 'qty' => $row['qty']);
            
            if ($row['qty'] > $arrParty[$containerkey]['qty'])
                $arrParty[$containerkey]['qty'] = $row['qty'];
              
        }
        
        return $arrParty;
    }
    
    function generateGrossProfitReport($criteria = '',$order = ''){
        $sql = '
            select 
                '.$this->tableName.'.code,
                '.$this->tableName.'.etdpol,
                '.$this->tableName.'.containernumber,
                '.$this->tableName.'.mblnumber,
                '.$this->tableName.'.loadcontainertypekey,
                '.$this->tableName.'.totalselling,
                '.$this->tableName.'.totalbuying,
                '.$this->tableName.'.totalcommission,
                ('.$this->tableName.'.totalselling - '.$this->tableName.'.totalbuying  - '.$this->tableName.'.totalemployeecommission - '.$this->tableName.'.totalcommission) as grossprofit,
                '.$this->tableNameDetail.'.pkey as detailkey,
                '.$this->tableNameDetail.'.code as detailcode,
                '.$this->tableNameDetail.'.rate,
                '.$this->tableCustomer.'.name as customername,
                '.$this->tableInvoiceHeader.'.trdate as invoicedate,
                '.$this->tableInvoiceHeader.'.code as invoicecode,
                '.$this->tablePort.'.name as podname,
                carrier.name as carriername,
                lclcontainer.name as lclcontainername
            from 
                '.$this->tableName.' 
                    left join '.$this->tablePort.' on '.$this->tableName.'.podkey = '.$this->tablePort.'.pkey
                    left join '.$this->tableSupplier.' carrier on '.$this->tableName.'.carrierkey = carrier.pkey
                    left join '.$this->tableContainer.' lclcontainer on '.$this->tableName.'.itemkey = lclcontainer.pkey,
                '.$this->tableNameDetail.' 
                    left join '.$this->tableInvoiceDetail.' on '.$this->tableInvoiceDetail.'.salesorderkey = '.$this->tableNameDetail.'.pkey
                    left join  '.$this->tableInvoiceHeader.' on '.$this->tableInvoiceDetail.'.refkey =  '.$this->tableInvoiceHeader.'.pkey and 		
                    '.$this->tableInvoiceHeader.'.statuskey in (2,3),
                '.$this->tableCustomer.'
                
            where 
                '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                '.$this->tableNameDetail.'.customerkey = '.$this->tableCustomer.'.pkey and
                '.$this->tableName.'.statuskey in (2,3) and
                 '.$this->tableInvoiceHeader.'.code  is not null
            ';
        
         $sql .= ' ' .$criteria; 
         //$sql .= ' ' .$order;
        
          //$this->setLog($sql,true);
          return $this->oDbCon->doQuery($sql); 
    }
    
    
    function generateUninvoicedReport($criteria = '',$order = ''){
        $sql = '
            select 
                '.$this->tableName.'.pkey,
                '.$this->tableName.'.code,
                '.$this->tableName.'.trdate,
                '.$this->tableName.'.etdpol,
                '.$this->tableName.'.containernumber, 
                '.$this->tableName.'.loadcontainertypekey, 
                '.$this->tableName.'.jobtypekey,
                '.$this->tableNameDetail.'.pkey as detailkey,
                '.$this->tableNameDetail.'.code as detailcode, 
                '.$this->tableNameDetail.'.rate, 
                '.$this->tableCurrency.'.name as currency, 
                '.$this->tableNameDetailItem.'.currencykey as detailcurrency, 
                '.$this->tableNameDetailItem.'.subtotalcurrency, 
                '.$this->tableCustomer.'.name as customername, 
                '.$this->tableItem.'.name as itemname,
                '.$this->tableNameDetail.'.subtotal, 
                '.$this->tableStatus.'.status as statusname,
                '.$this->tableEmployee.'.name as salesname, 
                '.$this->tableContainerType.'.name as containertype , 
                '.$this->tableLoadContainer.'.name as loadcontainertype, 
                concat_ws(", ",'.$this->tableJobType.'.name,'.$this->tableTransportationType.'.name,'.$this->tableLoadContainer.'.name) as jobtypeunion
            from 
                '.$this->tableName.'   
                    left join '.$this->tableNameDetail.' on '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey  
                    left join '.$this->tableNameDetailItem.'  on '.$this->tableNameDetailItem.'.refkey = '.$this->tableNameDetail.'.pkey
                    left join '.$this->tableItem.' on '.$this->tableNameDetailItem.'.servicekey = '.$this->tableItem.'.pkey
                    left join '.$this->tableCurrency.'  on '.$this->tableNameDetailItem.'.currencykey = '.$this->tableCurrency.'.pkey 
                    left join '.$this->tableEmployee.' on '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey 
                    left join '.$this->tableContainerType.' on  '.$this->tableName.'.containertypekey = '.$this->tableContainerType.'.pkey 
                    left join '.$this->tableLoadContainer.' on '.$this->tableName.'.loadcontainertypekey = '.$this->tableLoadContainer.'.pkey 
                    left join '.$this->tableJobType.' on '.$this->tableName.'.jobtypekey = '.$this->tableJobType.'.pkey
                    left join '.$this->tableTransportationType.' on '.$this->tableName.'.transportationtypekey = '.$this->tableTransportationType.'.pkey ,   
                '.$this->tableStatus.',
                '.$this->tableCustomer.'
                
            where  
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                '.$this->tableNameDetail.'.customerkey = '.$this->tableCustomer.'.pkey and 
                ('.$this->tableNameDetailItem.'.qty = 0 or '.$this->tableNameDetailItem.'.qty > '.$this->tableNameDetailItem.'.qtyinvoiced ) 
            ';
         
                     
         $sql .= ' ' .$criteria; 
         
        $sql .= $this->getWarehouseCriteria() ;
        $sql .= $this->getCustomerCriteria() ;
        $sql .= $this->getSalesCriteria() ;
        
         $sql .= ' ' .$order;
        
        //$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql); 
    }
    
    function getDetailVolume($pkey,$criteria=''){
        
            $sql = 'select
	   			'.$this->tableVolumeDetail .'.*,
                '.$this->tableContainer.'.name as itemname,
                '.$this->tableContainer.'.volume,
                '.$this->tableContainer.'.groupvolume
			  from
			  	'. $this->tableContainer .', 
			  	'. $this->tableVolumeDetail .' 
			  where  
                '.$this->tableVolumeDetail .'.itemkey = '.$this->tableContainer .'.pkey and
			  	'.$this->tableVolumeDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',').')';
         

        $sql .= $criteria;
        
        //$this->setLog($sql,true);
		return $this->oDbCon->doQuery($sql);
    }
    
    
     function getDetailContainer($pkey,$criteria=''){
        
         
        $pkey = (!is_array($pkey)) ? array($pkey) : $pkey;
         
        $sql = 'select
	   			'.$this->tableContainerDetail .'.*,
                '.$this->tableItemUnit.'.name as unitname,
                '.$this->tableContainer.'.name as containername
			  from
			  	'. $this->tableContainerDetail .' 
                    left join '. $this->tableContainer .' on '. $this->tableContainerDetail .'.typekey = '. $this->tableContainer .'.pkey
                    left join '.$this->tableItemUnit.' on  '.$this->tableContainerDetail .'.unitkey =  '.$this->tableItemUnit.'.pkey
			  where  
			  	'.$this->tableContainerDetail .'.refkey in ('.$this->oDbCon->paramString($pkey, ',') .') ';
            
        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    }

    function getTotalLCL($jobOrderKey){
        $sql = 'select coalesce(count(pkey),0) as total from '. $this->tableName.' where refkey = ' . $this->oDbCon->paramString($jobOrderKey);
        $rs = $this->oDbCon->doQuery($sql); 
        
        return $rs[0]['total'];
    }
    
  /*  function getContainerVolume($sokey){
        
        if(!is_array($sokey))
            $sokey = array($sokey);
        
            $sql = 'select 
                        '.$this->tableName.'.pkey, 
                        '.$this->tableContainer.'.volume,
                        max('.$this->tableNameDetailItem.'.qty) as qty 
                    from 
                        '.$this->tableName.', '.$this->tableNameDetailItem.', '.$this->tableContainer.'
                    where
                        '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($sokey,',').') and
                        '.$this->tableName.'.pkey = '.$this->tableNameDetailItem.'.refheaderkey and
                        '.$this->tableNameDetailItem.'.itemkey = '.$this->tableContainer.'.pkey
                    group by '.$this->tableName.'.pkey, '.$this->tableContainer.'.volume
                    ';
            $rs = $this->oDbCon->doQuery($sql); 
        return $rs;  
        
    }*/
    
   function getBillType($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableBillType .'.pkey, 
	   			'.$this->tableBillType .'.name 
              from
			  	'.$this->tableBillType .' 
			  where
			  	'.$this->tableBillType .'.statuskey = 1';
       if(!empty($pkey))
            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
        
       $sql .=' order by name asc';
         
       return $this->oDbCon->doQuery($sql);
	
   }
     function getCubicVolume($sokey){
        
        if(!is_array($sokey))
            $sokey = array($sokey);
        
            $sql = 'select 
                        '.$this->tableName.'.pkey, 
                        max('.$this->tableNameDetail.'.weight) as weight,
                        max('.$this->tableNameDetail.'.measurement) as  measurement
                    from 
                        '.$this->tableName.', '.$this->tableNameDetail.'
                    where
                        '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($sokey,',').') and
                        '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey 
                    group by '.$this->tableName.'.pkey
                    ';
            $rs = $this->oDbCon->doQuery($sql); 
        return $rs;  
        
    }
    
 	function updateHBL($pkey){
        if(!$this->isActiveMasterHBL) return;
	 	
		$emklHouseBl = new EMKLHouseBL();
		$itemUnit = new ItemUnit();
		$customer = new Customer();
		
        $rsHeader = $this->getDataRowById($pkey);
        $rsDetail = $this->getDetailById($pkey);
		
		
		$headerWeight = $rsHeader[0]['weight'];
		$headerVolume = $rsHeader[0]['volume'];
		
		$arrDetailUnitKey = array_column($rsDetail,'unitkey');
		$rsUnit = $itemUnit->searchDataRow(array($itemUnit->tableName.'.pkey',$itemUnit->tableName.'.name'),' and '.$itemUnit->tableName.'.pkey in ('.$this->oDbCon->paramString($arrDetailUnitKey,',').')' );
		$rsUnit = array_column($rsUnit,null,'pkey');
		
		$rsShipper = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name',$customer->tableName.'.address'),' and '. $customer->tableName.'.pkey = '.$this->oDbCon->paramString($rsHeader[0]['customerkey']));
		
        $rsContainer = $this->getDetailContainer($pkey);

        $sumQty = 0;
        $sumUnitKey = 0;
        $sumGW = 0;
        $sumNW = 0;
        $sumCW = 0;
        $sumMeas = 0;

        $sumUnitKey = $rsContainer[0]['unitkey'];
        for($j=0; $j<count($rsContainer); $j++) {
            $sumQty += $rsContainer[$j]['qty'];
            $sumGW += $rsContainer[$j]['grossweight'];
            $sumNW += $rsContainer[$j]['netweight'];
            $sumCW += $rsContainer[$j]['chargeweight'];
            $sumMeas += $rsContainer[$j]['meas'];
        }

		for($i=0;$i<count($rsDetail);$i++){

			if(empty($rsDetail[$i]['hblkey']) || $rsDetail[$i]['ismanual'] == 1) continue;	 
 
			// hanya dijalankan jika pertama kali update saja..
			$rsHBL = $emklHouseBl->searchDataRow(array($emklHouseBl->tableName.'.refkey'), ' and '.$emklHouseBl->tableName.'.pkey = ' .$this->oDbCon->paramString($rsDetail[$i]['hblkey']));
			if(empty($rsHBL) || !empty($rsHBL[0]['refkey'])) continue;									 
			
			$weight = ($rsDetail[$i]['weight'] > 0) ? $rsDetail[$i]['weight'] : $headerWeight;
			$volume = ($rsDetail[$i]['measurement'] > 0) ? $rsDetail[$i]['measurement'] : $headerVolume; // gk bisa pake is empty ke 0.000
			
			$goodDescription = (!empty($rsDetail[$i]['description'])) ? $rsDetail[$i]['description'] : $rsHeader[0]['itemdescription'];
			$qtyPackage =  ($rsDetail[$i]['qty'] > 0) ? $this->formatNumber($rsDetail[$i]['qty']) . ' '. $rsUnit[$rsDetail[$i]['unitkey']]['name'] : '';
			
			// isoverwriteconsignee selalu satu, karena dr JO gk pake master
			// mau export / import harusny consigneenameny sama 
			
			$sql = 'update ' .$emklHouseBl->tableName.' set 
							refkey = '.$this->oDbCon->paramString($rsDetail[$i]['pkey']).', 
							refcode = '.$this->oDbCon->paramString($rsDetail[$i]['code']).',
							refheaderkey = '.$this->oDbCon->paramString($rsDetail[$i]['refkey']).',
							refheadercode = '.$this->oDbCon->paramString($rsHeader[0]['code']).',
							shipperkey = '.$this->oDbCon->paramString($rsHeader[0]['customerkey']).',
							shippername = '.$this->oDbCon->paramString(html_entity_decode($rsShipper[0]['name'])).',
							shipperaddress = '.$this->oDbCon->paramString($rsShipper[0]['address']).',
							polkey = '.$this->oDbCon->paramString($rsHeader[0]['polkey']).',
							podkey = '.$this->oDbCon->paramString($rsHeader[0]['podkey']).',
							agentkey = '.$this->oDbCon->paramString($rsHeader[0]['agentkey']).',
							podeliverykey = '.$this->oDbCon->paramString($rsHeader[0]['placeofdeliverykey']).',
							shortdescription = '.$this->oDbCon->paramString($goodDescription).',
							package = '.$this->oDbCon->paramString($qtyPackage).',
							weight = '.$this->oDbCon->paramString($weight).',
							volume = '.$this->oDbCon->paramString($volume).',
							isoverwriteconsignee = 0,
							consigneename = '.$this->oDbCon->paramString(html_entity_decode($rsHeader[0]['consigneename'])).',  
							isoverwritecarrier = 0,
                            carriername = '.$this->oDbCon->paramString(html_entity_decode($rsHeader[0]['carriername'])).',
                            feederkey = '.$this->oDbCon->paramString($rsHeader[0]['feederkey']).',
                            feedernumber = '.$this->oDbCon->paramString($rsHeader[0]['feedernumber']).',
                            vesselkey = '.$this->oDbCon->paramString($rsHeader[0]['vesselkey']).',
                            vesselnumber = '.$this->oDbCon->paramString($rsHeader[0]['vesselnumber']).',
                            finaldestinationkey = '.$this->oDbCon->paramString($rsHeader[0]['finaldestinationkey']).',
                            shipmenttermkey = '.$this->oDbCon->paramString($rsHeader[0]['shipmenttermkey']).',
                            shipmentterm2key = '.$this->oDbCon->paramString($rsHeader[0]['shipmentterm2key']).',
                            poreceiptkey = '.$this->oDbCon->paramString($rsHeader[0]['poreceiptkey']).',
                            freighttermkey = '.$this->oDbCon->paramString($rsHeader[0]['shippingterms1key']).',

                            sumqty = '.$this->oDbCon->paramString($sumQty).',
                            sumunitkey = '.$this->oDbCon->paramString($sumUnitKey).',
                            sumnetweight = '.$this->oDbCon->paramString($sumGW).',
                            sumgrossweight = '.$this->oDbCon->paramString($sumNW).',
                            summeas = '.$this->oDbCon->paramString($sumMeas).',
                            sumchargeweight = '.$this->oDbCon->paramString($sumChargeWeight).',

                            connectingvesselkey = '.$this->oDbCon->paramString($rsHeader[0]['connectingvesselkey']).',
                            connectingvesselnumber = '.$this->oDbCon->paramString($rsHeader[0]['connectingvesselnumber']).',
                            connectingvessel2key = '.$this->oDbCon->paramString($rsHeader[0]['connectingvessel2key']).',
                            connectingvessel2number = '.$this->oDbCon->paramString($rsHeader[0]['connectingvessel2number']).',

                            etdpol = '.$this->oDbCon->paramString($rsHeader[0]['etdpol']).',
                            etapod = '.$this->oDbCon->paramString($rsHeader[0]['etapod']).',

                            connectingcountrykey = '.$this->oDbCon->paramString($rsHeader[0]['connectingcountrykey']).',
                            connectingcountry2key = '.$this->oDbCon->paramString($rsHeader[0]['connectingcountry2key']).',
                            connectingcountry3key = '.$this->oDbCon->paramString($rsHeader[0]['connectingcountry3key']).',

                            servicecontract = '.$this->oDbCon->paramString($rsHeader[0]['servicecontract']).',

                            shippinglinekey = '.$this->oDbCon->paramString($rsHeader[0]['carrierkey']).'
					where 
							pkey = ' .$this->oDbCon->paramString($rsDetail[$i]['hblkey']);
 
			

			$this->oDbCon->execute($sql);
		}

    }
   
    function unlinkHBL($arrKeys,$byColumn = ''){
		
		if(empty($byColumn))
			 $byColumn = 'pkey';
			
         $sql = 'update ' .$this->tableHBL.' set 
                                    refkey = 0, 
                                    refcode = \'\',
                                    refheaderkey = 0,
                                    refheaderkey = 0,
                                    refheadercode = \'\',
                                    shipperkey = 0,
                                    polkey = 0,
                                    podkey = 0,
									podeliverykey = 0,
                                    shortdescription = \'\',
                                    package = \'\',
                                    weight = 0,
                                    volume = 0 
                            where 
                                    '.$byColumn.' in ('.$this->oDbCon->paramString($arrKeys,',').')
				';


			$this->oDbCon->execute($sql);
    }

 
        
	function afterUpdateData($arrParam,$action){
         $pkey = $arrParam['pkey'];
        
        if($this->isActiveMasterHBL) { 
            $emklHouseBL = new EMKLHouseBL();
			
            $this->updateHBL($pkey);
    
			// update HBL utk yg detailnya udah kehapus
			// update HBL utk yg detailnya jadi manual
			
			$sql = 'select '.$this->tableHBL.'.pkey
					from  '.$this->tableHBL.'
					where
						refheaderkey = '.$this->oDbCon->paramString($pkey).' and 
						refkey not in (
							select pkey from '.$this->tableNameDetail.' 
							where refkey = '.$this->oDbCon->paramString($pkey).' and ismanual = 0
						) 
					' ; 

			$rs = $this->oDbCon->doQuery($sql);
			$arrKeys = array_column($rs,'pkey');
			
			
            $this->unlinkHBL($arrKeys); 
			$this->syncHBLAndJobOrder($pkey);
             
		}
		
                // sementara utk upate bugs
//        $sql = 'delete from '.$this->tableNameDetailItem.' 
//                where 
//                    '.$this->tableNameDetailItem.'.refheaderkey = '.$this->oDbCon->paramString($pkey).' and 
//                    '.$this->tableNameDetailItem.'.refkey not in (
//                        select '.$this->tableNameDetail.'.pkey from '.$this->tableNameDetail.' where '.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($pkey).'
//                    )';
//        
//        $this->oDbCon->execute($sql);
        
    }
    
    function sumTotalARPayment($opt){
        $startDate = $opt['startDate'];
        $endDate = $opt['endDate'];
        $warehousekey = !empty($opt['warehousekey']) ? $opt['warehousekey'] : array();
        $topkey = !empty($opt['termOfPaymentKey']) ? $opt['termOfPaymentKey'] : array();
        $containerTypeKey = !empty($opt['containerTypeKey']) ? $opt['containerTypeKey'] : array();
    
        $emklOrderInvoice = new EMKLOrderInvoice(); 
        $tabletype = $this->getTableKeyAndObj($emklOrderInvoice->tableName,array('key'))['key'];
            
        // AR Payment -> AR -> INV -> JO
        
        $returnRs = array();
        $sql = 'select 
                    coalesce(sum('.$this->tableARPaymentDetail.'.amount * '.$this->tableARPaymentHeader.'.rate),0) as amount,
                    '.$this->tableName.'.warehousekey,
                    '.$this->tableName.'.containertypekey
                from 
                    '.$this->tableARPaymentHeader.', 
                    '.$this->tableARPaymentDetail.', 
                    '.$this->tableAR.',   
                    '.$this->tableName.',   
                    '.$emklOrderInvoice->tableName.' ,   
                    '.$emklOrderInvoice->tableNameDetail.' 
                where 
                    '.$this->tableARPaymentHeader.'.statuskey in (2,3) and
                    '.$this->tableARPaymentHeader.'.pkey =  '.$this->tableARPaymentDetail.'.refkey and
                    '.$this->tableARPaymentDetail.'.arkey =  '.$this->tableAR.'.pkey and 
                    '.$this->tableAR.'.reftabletype = '. $tabletype.' and
                    '.$this->tableAR.'.refheaderkey = '.$emklOrderInvoice->tableName.'.pkey and
                    '.$emklOrderInvoice->tableName.'.pkey = '.$emklOrderInvoice->tableNameDetail.'.refkey and
                    '.$emklOrderInvoice->tableNameDetail.'.invoicetype = 1 and
                    '.$emklOrderInvoice->tableNameDetail.'.refsalesorderheaderkey = '.$this->tableName.'.pkey  and
                    '.$this->tableARPaymentHeader.'.trdate between '.$this->oDbCon->paramDate($startDate,' / ').' and '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 
  
       /* if(!empty($warehousekey))
            $sql .= ' and '.$this->tableName.'.warehousekey in ('.$this->oDbCon->paramString($warehousekey,',').') ';

        if(!empty($containerTypeKey))
            $sql .= ' and '.$this->tableName.'.containertypekey in ('.$this->oDbCon->paramString($containerTypeKey,',').') ';
*/
        
        $sql .= ' group by '.$this->tableName.'.warehousekey, '.$this->tableName.'.containertypekey';

        //$this->setLog($sql,true);
        
        $rs = $this->oDbCon->doQuery($sql);
        $returnRs = array_merge($returnRs,$rs);
        
        return $returnRs;
    }
    
    /*function getRateForInvoice($soDetailKey){ 
       
	   $sql = 'select 
	   			'.$this->tableNameDetail .'.rate 
              from
			  	'.$this->tableNameDetail .' 
			  where
			  	pkey = '.$this->oDbCon->paramString($soDetailKey);
        
       $sql .=' order by rate desc';
         
       $rs = $this->oDbCon->doQuery($sql);
       return $rs[0]['rate'];
	
   }*/
	
	 function getSellingSummary($criteria='',$groupby = '',$periodIndex='', $orderby = 'warehousename'){
        
        // be aware, perubahan group harus update ke concat index jg
        if (empty($groupby))  $groupby = $this->tableName.'.warehousekey, year('.$this->tableName.'.trdate),month('.$this->tableName.'.trdate)';
        if (empty($periodIndex))  $periodIndex = 'concat(warehousekey,\'-\',DATE_FORMAT('.$this->tableName.'.trdate, \'%c-%Y\'))';
        
		// gk bisa per pelanggan, karena pelanggannya ad di detail, jd harus join detail lg
		 
        $sql  = '
                select  
					sum('.$this->tableName.'.totalselling) as totalselling,
					sum('.$this->tableName.'.totalbuying) as totalbuying,
					sum('.$this->tableName.'.totalcommission) as totalcommission,
					sum('.$this->tableName.'.totalemployeecommission) as totalemployeecommission,
					sum('.$this->tableName.'.totaldebitnote) as totaldebitnote,
                    '.$periodIndex.' as periodindex,
                    '.$this->tableWarehouse.'.name as warehousename, 
                    '.$this->tableName.'.warehousekey,
                    month('.$this->tableName.'.trdate) as month,   
                    year('.$this->tableName.'.trdate) as year  
                from 
                    '.$this->tableName.', 
                    '.$this->tableWarehouse.' 
                where 
                    '.$this->tableName.'.warehousekey  = '.$this->tableWarehouse.'.pkey and
					'.$this->tableName.'.statuskey in (2,3) ';
           
        if (!empty($criteria))
            $sql .= ' ' .$criteria;
        
        $sql .=  $this->getWarehouseCriteria() ;
        
        $sql .=' group by ' .$groupby;
        
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
	
	//  GROUP_CONCAT(concat('.$this->tablePurchase.'.code,\', \','.$this->tableSupplier.'.name ) SEPARATOR \'<br>\' ) as purchasedetail,
	
	function generateJobOrderCommissionReport($criteria,$order){
		
		$sql = 'select 
					'.$this->tableName.'.pkey,
					'.$this->tableName.'.code,
					'.$this->tableName.'.etdpol,
					'.$this->tableName.'.etapod,
					'.$this->tableName.'.totalselling,  
					'.$this->tableName.'.totalbuying,  
					'.$this->tableName.'.totalcommission,   
					'.$this->tableName.'.bookingnumber,   
              		carrier.name as carriername, 
					'.$this->tablePurchase.'.code as purchasecode, 
					GROUP_CONCAT('.$this->tableSupplier.'.name SEPARATOR \'<br>\' ) as suppliername,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename,
					('.$this->tableName.'.totalselling + '.$this->tableName.'.totaldebitnote - '.$this->tableName.'.totalbuying  - '.$this->tableName.'.totalemployeecommission - '.$this->tableName.'.totalcommission) as grossprofit ,
              		'.$this->tableCustomer.'.name as customername, 
              		'.$this->tableEmployee.'.name as salesname
				from 
					'.$this->tableName.' 
						left join '.$this->tablePurchase.' on '.$this->tableName.'.pkey = '.$this->tablePurchase.'.refkey
						left join '.$this->tableSupplier.' on '.$this->tablePurchase.'.supplierkey = '.$this->tableSupplier.'.pkey
						left join '.$this->tableSupplier.' carrier on '.$this->tableName  .'.carrierkey = carrier.pkey
						left join '.$this->tableEmployee.' on '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey,
					'.$this->tableStatus.',
					'.$this->tableWarehouse.',
					'.$this->tableCustomer.'
				where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
					'.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
					'.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
					'.$this->tablePurchase.'.statuskey in (2,3)
				';
		
		if (!empty($criteria)) $sql .= ' ' .$criteria;
		
        $sql .=  $this->getWarehouseCriteria() ;
		
		$sql .= ' group by '.$this->tableName.'.pkey';
		
		$sql .= ' ' .$order;
		
        $rs = $this->oDbCon->doQuery($sql);
		
        return $rs;
	}
	
	function getLCLChild($pkey){
		$sql = 'select '.$this->tableName.'.pkey from '.$this->tableName.' where  '.$this->tableName.'.refkey = '.$this->oDbCon->paramString($pkey);
		return $this->oDbCon->doQuery($sql);
	}
	
	function getLCLDetailWithRelatedInformation($pkey){
		// ambil detail LCL (anak) dari LCL master
		
		$arrReturn = array();;
		
		$arrReturn = $this->getDetailWithRelatedInformation($pkey);
		
		$rsLCL = $this->getLCLChild($pkey);
		$arrDetailPkey = array_column($rsLCL,'pkey'); 
		$rsDetail = $this->getDetailWithRelatedInformation($arrDetailPkey);
		
	 	$arrReturn = array_merge($arrReturn,$rsDetail);
		
		return $arrReturn;
	}
     
    function getJobOrderByMonth($startPeriod, $endPeriod, $dateColumn = 'trdate', $warehousekey = ''){
         $sql = 'select 
                    month('.$dateColumn.') as month,  
                    DATE_FORMAT('.$dateColumn.', \'%b\')  as monthname, 
                    year('.$dateColumn.') as year, 
                    sum(totalselling) as total
                from 
                    '.$this->tableName.'
                where (statuskey >= 2 and statuskey <= 3) and '.$dateColumn.' between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')';
                
        if (!empty($this->jobType))
            $sql .= ' and jobtypekey in ('.$this->jobType.')  ';
        
		 // khusus kalo user pilih warehouse
		 if (!empty($warehousekey))
				$sql .= ' and warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
			 
          $sql .=  $this->getWarehouseCriteria() ;
          $sql .= ' group by year('.$dateColumn.'),month('.$dateColumn.')';
        
         return $this->oDbCon->doQuery($sql); 
    }
    
     function updateTotalDebitNote($arrKey){
         
         // $arrKey => jokey
         
        if(!is_array($arrKey)) $arrKey = array($arrKey);
        
        $arrKey = array_unique($arrKey);
        
		$debitNote = new DebitNote();
		 
		$arrJO = array();
         
		// cari DN yg masih aktif
		$rsDNCol = $debitNote->getSourceTransaction($arrKey,array(2,3)); 
		
		$arrJODN = array();
		foreach($rsDNCol as $row){  
            $jokey = $row['sokey'];
            if(!isset($arrJODN[$jokey])) $arrJODN[$jokey] = 0; 
            $arrJODN[$jokey] += ($row['totaldebit'] *  $row['rate']);  
		}
			
		foreach($arrKey as $jokey){ 
			
			$totalDebitNote = ( isset($arrJODN[$jokey]) ) ? $arrJODN[$jokey] : 0 ;
				
			$sql = 'update '.$this->tableName.' set totaldebitnote = ' . $this->oDbCon->paramString($totalDebitNote) .' 
					where pkey = ' .  $this->oDbCon->paramString($jokey);

//            $this->setLog($sql,true);
			$this->oDbCon->execute($sql);

		}
		 
	}
	    function getShipmentTerm($pkey = '')
    {

        $sql = 'select
                    ' . $this->tableShipmentTerm . '.*
               from
                   ' . $this->tableShipmentTerm;

        if (!empty($pkey))
            $sql .= ' where  		
             ' . $this->tableShipmentTerm . '.pkey = ' . $this->oDbCon->paramString($pkey);


        $sql .= ' order by name asc';

        return $this->oDbCon->doQuery($sql);

    }

    function getFreightgTerm($pkey = '') {
        $sql = 'select
                    ' . $this->tableFreightgTerm . '.*
                from
                    ' . $this->tableFreightgTerm;

        if (!empty($pkey))
            $sql .= ' where  		
             ' . $this->tableFreightgTerm . '.pkey = ' . $this->oDbCon->paramString($pkey);


        $sql .= ' order by orderlist asc';

        return $this->oDbCon->doQuery($sql);
    }

    function getShipmentType($pkey = array()) {
        
        if(!is_array($pkey))
            $pkey = array($pkey);
            
        $sql = 'select
                    ' . $this->tableShipmentType . '.*
                from
                    ' . $this->tableShipmentType;

        if (!empty($pkey))
            $sql .= ' where  		
            ' . $this->tableShipmentType . '.pkey in('.$this->oDbCon->paramString($pkey,',').') ';


        $sql .= ' order by orderlist asc';

        return $this->oDbCon->doQuery($sql);
    }
	
	function updateAPEmployeeCommission($jokey){ 
		return;
		
		// sementara based on warehouse dulu
		
//		$apEmployeeCommission = new APEmployeeCommission();
//		$warehouse = new Warehouse();
//		
//		
//        $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'))['key'];  
//		
//		// cari semua data warehouse, termasuk yg gk aktif, karena ad kemungkinan sales order atas wwarehouse yg gk aktif
//		// alokasi komisi ditentukan oleh wwarehouse di JO, bukan di invoice
//		
//		
//		// cari sudah ad komisi utk JO ini blm
//		$rsAPCommission = $apEmployeeCommission->searchDataRow(array($apEmployeeCommission->tableName.'.pkey',$apEmployeeCommission->tableName.'.refkey'),
//															   'and '.$apEmployeeCommission->tableName.'.statuskey in (1,2,3) 
//																and '.$apEmployeeCommission->tableName.'.refkey = '.$this->oDbCon->paramString($jokey).'
//																and '.$apEmployeeCommission->tableName.'.reftabletype = '.$tablekey
//															  );   
//		
//		if(!empty($rsAPCommission)) return;
//		
//		// cari warehousekey dulu dan cari nilainya 
//		$rsJO = $this->searchDataRow(array($this->tableName.'.pkey',
//										   $this->tableName.'.refkey',
//										   $this->tableName.'.warehousekey',
//										   $this->tableName.'.code',
//										   $this->tableName.'.trdate',
//										   $this->tableName.'.loadcontainertypekey',
//										   $this->tableName.'.ismaster'
//										), 
//										' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($jokey) 
//									);
//		
//		$warehousekey = $rsJO[0]['warehousekey'];  
//		
//		$rsWarehouse = $warehouse->searchDataRow(array($warehouse->tableName.'.pkey',$warehouse->tableName.'.saleskey',$warehouse->tableName.'.defaultcommission'),
//												' and '. $warehouse->tableName.'.pkey = '.$this->oDbCon->paramString($warehousekey)
//												 );
//		
//		if($rsWarehouse[0]['defaultcommission'] <= 0 ) return;
//  
//		// kalo tipenya LCL dan ismaster == 0. ambil header LCL nya
//		// LCL NC, ismasterny selalu 0, dianggap sama dengan FCL  
// 
//		if( $rsJO[0]['loadcontainertypekey'] == EMKL['emklType']['lcl'] && $row['ismaster'] == 0 ){ 
//			 
//			// cari ulang kalo JO master nya LCL
//			$this->updateWarehouseCommission($rsJO[0]['refkey']);
//			return;
//
//		}
//	 
// 		 
//		
//		$currencykey = 1; // sementara
//		$rate = 1; //sementara
//		$jokey = $rsJO[0]['pkey'];
//		$commissionWarehouseKey = $rsJO[0]['warehousekey'];
//  
//		$note = array();
//		array_push($note,$rsJO[0]['code']);
//		
//		//add komisi 
//		$arrParam = array();	 
//
//		$arrParam['code'] = 'xxxxxx';
//		$arrParam['hidEmployeeKey'] = $rsWarehouse[0]['saleskey'];
//		$arrParam['hidRefKey'] = $jokey; 
//		$arrParam['hidRefKey2'] = $jokey;
//		$arrParam['hidRefHeaderKey'] = $jokey;
//		$arrParam['trDate'] =  date('d / m / Y');  
//		$date = new DateTime(date('Y-m-d'));
//		$date->add(new DateInterval('P30D'));
//		$arrParam['dueDate'] = $date->format('d / m / Y'); 
//		$arrParam['hidRefCode'] = $rsJO[0]['code'];
//		$arrParam['hidRefCode2'] = $rsJO[0]['code'];
//		$arrParam['hidRefDate'] =  $this->formatDBDate($rsJO[0]['trdate'],'d / m / Y'); 
//		$arrParam['hidRefTable'] = $tablekey;
//		$arrParam['amount'] =  $rsWarehouse[0]['defaultcommission']; 
//		$arrParam['amountIDR'] = $rsWarehouse[0]['defaultcommission'] * $rate;
//		$arrParam['currencyRate'] = $rate;
//		$arrParam['trDesc'] = implode(chr(13),$note); 
//		$arrParam['islinked'] = 1;
//		$arrParam['overwriteGL'] = 1;
//		$arrParam['selAPType'] = AP_TYPE['salesCommission'];
//		$arrParam['selWarehouse'] = $commissionWarehouseKey;
//		$arrParam['selCurrency'] = $currencykey;
//		$arrParam['currencyRate'] = $rate;
//
//		$arrayToJs = $apEmployeeCommission->addData($arrParam);  
//
//		if (!$arrayToJs[0]['valid'])
//			throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);
//
//		 $this->updateTotalEmployeeCommission($jokey);
//		 
// 
	}
    
    
    function getDataForJobOrderSummaryReport($criteria = '',$order = '')
    {
        $sql = '
            SELECT
                '.$this->tableName.'.pkey,
                '.$this->tableName.'.code,
                '.$this->tableName.'.trdate,
                '.$this->tableName.'.etdpol,
                '.$this->tableName.'.etapod,
                '.$this->tableName.'.mblnumber,
                '.$this->tableName.'.loadcontainertypekey,  
                '.$this->tableName.'.shipmenttypekey,  
                '.$this->tableName.'.jobtypekey,  
                '.$this->tableName.'.totalselling,  
                '.$this->tableName.'.totalbuying,  
                '.$this->tableName.'.totaldebitnote,  
                '.$this->tableName.'.volume,   
                '.$this->tableName.'.weight,     
                '.$this->tableName.'.customerkey,    
                '.$this->tableName.'.agentkey,    
                '.$this->tableName.'.carrierkey,
                '.$this->tableName.'.transportationtypekey,    
                '.$this->tableName.'.weightqty,    
                '.$this->tableName.'.placeofdeliverykey,
				if('.$this->tableName. '.jobtypekey = 1, '.$this->tableName.'.etapod ,'.$this->tableName.'.etdpol) as saildate,
                ('.$this->tableName.'.totalselling - '.$this->tableName.'.totalbuying - '.$this->tableName.'.totalemployeecommission - '.$this->tableName.'.totalcommission - '.$this->tableName.'.totalcreditnote + '.$this->tableName.'.totaldebitnote) as grossprofit ,
                '.$this->tableCustomer.'.name as customername, 
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableEmployee.'.name as salesname,
			    '.$this->tableStatus.'.status as statusname , 
			    '.$this->tableTransportationType.'.name as transportationtype,
                '.$this->tableLoadContainer.'.name as loadcontainertype, 
                pol.name as polname,
                pod.name as podname, 
                carrier.name as carriername,  
                agent.name as agentname,  
                '.$this->tableVolumeUnit.'.name as volumeunit,
                '.$this->tableContainerType.'.name as containertype,
                created.name as createdname, 
                pol_continent.pkey as polcontinentkey,
                pol_continent.name as polcontinentname,
                pod_continent.pkey as podcontinentkey,
                pod_continent.name as podcontinentname,
                podelivery.name as placeofdeliveryname
			FROM 
                '.$this->tableStatus.',
                '.$this->tableEmployee.' created,
                '.$this->tableContainerType.',
                '.$this->tableName.'
                    left join '.$this->tableEmployee.' on  '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey   
                    left join '.$this->tablePort.' pol on  '.$this->tableName.'.polkey = pol.pkey 
                    left join '.$this->tablePort.' pod on  '.$this->tableName.'.podkey = pod.pkey

                    left join '. $this->tableCity .' pol_city on pol.citykey = pol_city.pkey
                    left join '. $this->tableCountry .' pol_country on  pol_city.countrykey = pol_country.pkey
                    left join '. $this->tableContinent .' pol_continent on pol_country.continentkey = pol_continent.pkey
                    
                    left join '. $this->tableCity .' pod_city on pod.citykey = pod_city.pkey
                    left join '. $this->tableCountry .' pod_country on  pod_city.countrykey = pod_country.pkey
                    left join '. $this->tableContinent .' pod_continent on pod_country.continentkey = pod_continent.pkey
 
                    left join '.$this->tableSupplier.' carrier on  '.$this->tableName.'.carrierkey = carrier.pkey

                    left join '.$this->tableCustomer.' agent on  '.$this->tableName.'.agentkey = agent.pkey  
                    left join '.$this->tableCustomer.'  on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tablePort.' podelivery on  '.$this->tableName.'.placeofdeliverykey = podelivery.pkey,
                    
                 '.$this->tableWarehouse.',  
                 '.$this->tableJobType.',
                 '.$this->tableTransportationType.',
                 '.$this->tableLoadContainer.',
                 '.$this->tableVolumeUnit.'
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and  
                '.$this->tableName.'.jobtypekey = '.$this->tableJobType.'.pkey and 
                '.$this->tableName.'.transportationtypekey = '.$this->tableTransportationType.'.pkey and
                '.$this->tableName.'.loadcontainertypekey = '.$this->tableLoadContainer.'.pkey and
                '.$this->tableName.'.volumetype = '.$this->tableVolumeUnit.'.pkey and
                '.$this->tableName.'.createdby = created.pkey and
                '.$this->tableName.'.containertypekey = '.$this->tableContainerType.'.pkey 
        ';

        $sql .= ' ' .$criteria; 
         
        $sql .= $this->getWarehouseCriteria() ;
        $sql .= $this->getCustomerCriteria() ;
        $sql .= $this->getSalesCriteria() ;
        
        $sql .= ' ' .$order;
  
        $result = $this->oDbCon->doQuery($sql);

        return $result;
    }
    
     function getTotalEMKLJobOrder($criteria = '', $groupBy = '')
    {

        $indexkey = (empty($groupBy) ? $this->tableName.'.pkey' : $groupBy);

        $sql = '
            select
                COUNT('. $this->tableName .'.pkey) as total,
                '. $indexkey .' as indexkey
            from
                '. $this->tableName .'

                left join '.$this->tablePort.' pol on  '.$this->tableName.'.polkey = pol.pkey 
                left join '.$this->tablePort.' pod on  '.$this->tableName.'.podkey = pod.pkey

                left join '. $this->tableCity .' pol_city on pol.citykey = pol_city.pkey
                left join '. $this->tableCountry .' pol_country on  pol_city.countrykey = pol_country.pkey
                left join '. $this->tableContinent .' pol_continent on pol_country.continentkey = pol_continent.pkey
                    
                left join '. $this->tableCity .' pod_city on pod.citykey = pod_city.pkey
                left join '. $this->tableCountry .' pod_country on  pod_city.countrykey = pod_country.pkey
                left join '. $this->tableContinent .' pod_continent on pod_country.continentkey = pod_continent.pkey

            where
                1 = 1
        ';

        if(!empty($criteria)) {
            $sql .= $criteria;
        }

        if(!empty($groupBy)) {
            $sql .= ' group by '. $groupBy;
        }

        $result = $this->oDbCon->doQuery($sql);
        
        return $result;
    }
    
    function getTotalSelling($arrPkey = array(), $inIDR = true){
        
        // kalo IDR nanti dikali rate dulu
        // sum dpp, tax dan after tax agar sekalian
        
        if($inIDR){ 
            $query = 'coalesce(sum('.$this->tableNameDetailItem .'.beforetaxdetailvalue * IF('.$this->tableNameDetailItem .'.currencykey = '.CURRENCY['idr'].',1 ,'.$this->tableNameDetail .'.rate)),0) as beforetaxtotal,
                      coalesce(sum('.$this->tableNameDetailItem .'.taxdetailvalue * IF('.$this->tableNameDetailItem .'.currencykey = '.CURRENCY['idr'].',1 ,'.$this->tableNameDetail .'.rate)),0) as taxvalue,
                      coalesce(sum('.$this->tableNameDetailItem .'.aftertaxdetailvalue * IF('.$this->tableNameDetailItem .'.currencykey = '.CURRENCY['idr'].',1 ,'.$this->tableNameDetail .'.rate)),0) as total,
					  1 as rate'; 
        }else{
            $query = 'coalesce(sum('.$this->tableNameDetailItem.'.beforetaxdetailvalue),0) as beforetaxtotal,
                    coalesce(sum('.$this->tableNameDetailItem.'.taxdetailvalue),0)  as taxvalue,
                    coalesce(sum('.$this->tableNameDetailItem.'.aftertaxdetailvalue ),0)  as total';
        }
        
        $sql = 'select
                    '.$query.',
                    '.$this->tableName.'.pkey as jokey,
                    '.$this->tableNameDetailItem.'.currencykey
                from
                    '.$this->tableName.',
                    '.$this->tableNameDetail.',
                    '.$this->tableNameDetailItem.'
                where
                    '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableNameDetailItem.'.refkey = '.$this->tableNameDetail.'.pkey and
                    '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').')';
        
           if($inIDR)
             $sql .= ' group by '.$this->tableName.'.pkey';
            else
             $sql .= ' group by '.$this->tableName.'.pkey, '.$this->tableNameDetailItem.'.currencykey';
        
        $rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }

    function getDataForGrossProfit(){
	   
        $sql = '
			SELECT
              '.$this->tableName.'.* ,
              ('.$this->tableName.'.totalselling - '.$this->tableName.'.totalbuying - '.$this->tableName.'.totalemployeecommission - '.$this->tableName.'.totalcommission - '.$this->tableName.'.totalcreditnote + '.$this->tableName.'.totaldebitnote) as grossprofit ,
              '.$this->tableCustomer.'.name as customername,
              '.$this->tableCustomer.'.address as customeraddress,
              '.$this->tableName.'.customercache as customercachename, 
              '.$this->tableWarehouse.'.name as warehousename,
              '.$this->tableEmployee.'.name as salesname,
			  '.$this->tableStatus.'.status as statusname ,
			  '.$this->tableJobType.'.name as jobtype ,
			  '.$this->tableTransportationType.'.name as transportationtype,
              '.$this->tableLoadContainer.'.name as loadcontainertype,
			  '.$this->tableContainer.'.name as containername ,
			  '.$this->tableVessel.'.name as vesselname ,
			  feeder_vessel.name as feedervesselname ,
			  '.$this->tableLocation.'.name as locationname ,
              customer_invoice.name as customerinvoicename,
              pol.name as polname,
              pod.name as podname,  
              podelivery.name as placeofdeliveryname,
              poreceipt.name as placeofreceiptname,
              carrier.name as carriername, 
              carrier.alias as carrieraliasname, 
              agent.name as agentname,
              '.$this->tableDepot.'.name as depotname,
              '.$this->tableTerminal.'.name as terminalname,
              concat_ws(", ",'.$this->tableJobType.'.name,'.$this->tableTransportationType.'.name,'.$this->tableLoadContainer.'.name) as jobtypeunion,
              '.$this->tableVolumeUnit.'.name as volumeunit,
             '.$this->tableContainerType.'.name as containertype,
             created.name as createdbyname 
			FROM '.$this->tableStatus.',
                 '.$this->tableName.'
                    left join '.$this->tableEmployee.' on  '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey 
                    left join '.$this->tableEmployee.' created on  '.$this->tableName.'.createdby = created.pkey   
                    left join '.$this->tableContainer.' on  '.$this->tableName.'.itemkey = '.$this->tableContainer.'.pkey 
                    left join '.$this->tablePort.' pol on  '.$this->tableName.'.polkey = pol.pkey 
                    left join '.$this->tablePort.' pod on  '.$this->tableName.'.podkey = pod.pkey
                    left join '.$this->tablePort.' podelivery on  '.$this->tableName.'.placeofdeliverykey = podelivery.pkey
                    left join '.$this->tablePort.' poreceipt on  '.$this->tableName.'.placeofreceiptkey = poreceipt.pkey
                    left join '.$this->tableSupplier.' carrier on  '.$this->tableName.'.carrierkey = carrier.pkey
                    left join '.$this->tableVessel.' on  '.$this->tableName.'.vesselkey = '.$this->tableVessel.'.pkey 
                    left join '.$this->tableVessel.' feeder_vessel on  '.$this->tableName.'.feederkey = feeder_vessel.pkey 
                    left join '.$this->tableCustomer.' agent on  '.$this->tableName.'.agentkey = agent.pkey 
                    left join '.$this->tableDepot.' on  '.$this->tableName.'.depotkey = '.$this->tableDepot.'.pkey 
                    left join '.$this->tableTerminal.' on  '.$this->tableName.'.terminalkey = '.$this->tableTerminal.'.pkey 
                    left join '.$this->tableLocation.' on  '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey 
                    left join '.$this->tableCustomer.'  on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tableCustomer.' customer_invoice on '.$this->tableName.'.invoicetokey = customer_invoice.pkey 
                    left join '.$this->tableContainerType.' on  '.$this->tableName.'.containertypekey = '.$this->tableContainerType.'.pkey  ,
                 '.$this->tableWarehouse.',  
                 '.$this->tableJobType.',
                 '.$this->tableTransportationType.',
                 '.$this->tableLoadContainer.',
                 '.$this->tableVolumeUnit.'
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and  
                '.$this->tableName.'.jobtypekey = '.$this->tableJobType.'.pkey and 
                '.$this->tableName.'.transportationtypekey = '.$this->tableTransportationType.'.pkey and
                '.$this->tableName.'.loadcontainertypekey = '.$this->tableLoadContainer.'.pkey and
                '.$this->tableName.'.volumetype = '.$this->tableVolumeUnit.'.pkey';
        
        
        if (!empty($this->jobType))
            $sql .= ' and jobtypekey in ('.$this->jobType.')  ';
            
 		$sql .= $this->criteria ;
        $sql .= $this->getWarehouseCriteria() ;
        $sql .= $this->getCustomerCriteria() ;
        $sql .= $this->getSalesCriteria() ;
         
        return $sql;
    }

     function getTotalBuyingByEMKLJO($pkey, $multipliedRate = false) {

        $sql = 'select   
                '. $this->tablePurchase .'.refkey as jokey,
                '. $this->tableName .'.code as jocode,
                '. $this->tableCustomer .'.name as customername,
            ';

        if($multipliedRate) {
            $sql .='
                coalesce(sum('.$this->tablePurchase .'.beforetaxtotal * IF('.$this->tablePurchase .'.currencykey = '.CURRENCY['idr'].',1 ,'.$this->tablePurchase .'.rate)),0) as totalbuying 
            ';
        } else {
            $sql .='
                    '. $this->tablePurchase .'.currencykey, 
                    '. $this->tablePurchase .'.rate, 
                    coalesce(sum('.$this->tablePurchase .'.beforetaxtotal),0) as totalbuying
            ';
        }

        $sql .='
                from 
                    '. $this->tablePurchase . ',
                    '. $this->tableName .',
                    '. $this->tableCustomer .'
                where 
                    '. $this->tablePurchase .'.refkey = '. $this->tableName .'.pkey and
                    '. $this->tableName .'.customerkey = '. $this->tableCustomer .'.pkey and
                    '. $this->tablePurchase . '.refkey in ('. $this->oDbCon->paramString($pkey,',') .') and
                ('.$this->tablePurchase .'.statuskey = 2 or '.$this->tablePurchase .'.statuskey = 3)';

        if($multipliedRate) {
            $sql .=' group by '.$this->tablePurchase .'.refkey';
        } else {
            $sql .=' group by '.$this->tablePurchase .'.refkey,  '.$this->tablePurchase .'.currencykey';
        }
            
        $rs = $this->oDbCon->doQuery($sql); 

        return $rs;
    }
        
   function getIncoterms($pkey=''){ 
        
	   $sql = 'select
	   			'.$this->tableIncoterms .'.pkey, 
	   			'.$this->tableIncoterms .'.name 
              from
			  	'.$this->tableIncoterms .' 
			  where
			  	'.$this->tableIncoterms .'.statuskey = 1';
       if(!empty($pkey))
            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
        
       $sql .=' order by name asc';
         
       return $this->oDbCon->doQuery($sql);
	
   }
	function syncHBLAndJobOrder($pkey,$isJobOrder = true){
 			
		   $syncHBLAndJobOrder = ($this->loadSetting('syncHBLAndJobOrder') == 1)  ?  true : false;
		   if(!$syncHBLAndJobOrder) return; 
		
			//dipisah karena kedepannya mungkin perlu dipanggil manual
			$this->updateJobOrderAndHouseBL($pkey,$isJobOrder);   
	}
	
    function updateJobOrderAndHouseBL($pkey,$isJobOrder = true)
    { 
        $emklHouseBL = new EMKLHouseBL();
        
        $arrFields = array();
        $criteria = '';

        if($isJobOrder) {
            //update HBL dari JO
            $tableName= $emklHouseBL->tableName;

            $rsData = $this->getDataRowById($pkey);

            if(empty($rsData)) return;

            $arrFields = array(
                'mblnumber = '. $this->oDbCon->paramString($rsData[0]['mblnumber']),
                'etdpol = '. $this->oDbCon->paramString($rsData[0]['etdpol']), 
                'etapod = '. $this->oDbCon->paramString($rsData[0]['etapod']), 
                'finaldestinationkey = '. $this->oDbCon->paramString($rsData[0]['finaldestinationkey']), 
                'sumqty = '. $this->oDbCon->paramString($rsData[0]['qty']), 
                'sumunitkey = '. $this->oDbCon->paramString($rsData[0]['unitkey']), 
                'sumgrossweight = '. $this->oDbCon->paramString($rsData[0]['weightqty']), 
                'summeas = '. $this->oDbCon->paramString($rsData[0]['measurement']),
                'feederkey = ' .  $this->oDbCon->paramString($rsData[0]['feederkey']),
                'feedernumber = ' .  $this->oDbCon->paramString($rsData[0]['feedernumber']),
                'vesselkey = ' .  $this->oDbCon->paramString($rsData[0]['vesselkey']),
                'vesselnumber = ' .  $this->oDbCon->paramString($rsData[0]['vesselnumber']),
                'connectingvesselkey = ' .  $this->oDbCon->paramString($rsData[0]['connectingvesselkey']),
                'connectingvesselnumber = ' .  $this->oDbCon->paramString($rsData[0]['connectingvesselnumber']),
                'connectingvessel2key = ' .  $this->oDbCon->paramString($rsData[0]['connectingvessel2key']),
                'connectingvessel2number = ' .  $this->oDbCon->paramString($rsData[0]['connectingvessel2number']),
            );

            $criteria = $emklHouseBL->tableName.'.refheaderkey = '. $this->oDbCon->paramString($rsData[0]['pkey']) .' and '.$emklHouseBL->tableName.'.statuskey in (1,2,3) ';

        } else {
            //update JO dari HBL
            $tableName = $this->tableName;

            $rsData = $emklHouseBL->getDataRowById($pkey);

            if(empty($rsData)) return;

            $arrFields = array(
                'mblnumber = ' . $this->oDbCon->paramString($rsData[0]['mblnumber']),
                'etdpol = ' . $this->oDbCon->paramString($rsData[0]['etdpol']),
                'etapod = ' . $this->oDbCon->paramString($rsData[0]['etapod']),
                'finaldestinationkey = ' . $this->oDbCon->paramString($rsData[0]['finaldestinationkey']),
                'qty = ' . $this->oDbCon->paramString($rsData[0]['sumqty']),
                'unitkey = ' . $this->oDbCon->paramString($rsData[0]['sumunitkey']),
                'weightqty = ' . $this->oDbCon->paramString($rsData[0]['sumgrossweight']),
                'measurement = ' . $this->oDbCon->paramString($rsData[0]['summeas']),
                'feederkey = ' .  $this->oDbCon->paramString($rsData[0]['feederkey']),
                'feedernumber = ' .  $this->oDbCon->paramString($rsData[0]['feedernumber']),
                'vesselkey = ' .  $this->oDbCon->paramString($rsData[0]['vesselkey']),
                'vesselnumber ='  .$this->oDbCon->paramString($rsData[0]['vesselnumber']),
                'connectingvesselkey = ' .  $this->oDbCon->paramString($rsData[0]['connectingvesselkey']),
                'connectingvesselnumber = ' .  $this->oDbCon->paramString($rsData[0]['connectingvesselnumber']),
                'connectingvessel2key = ' .  $this->oDbCon->paramString($rsData[0]['connectingvessel2key']),
                'connectingvessel2number = ' .  $this->oDbCon->paramString($rsData[0]['connectingvessel2number']),
            );

            $criteria = $this->tableName . '.pkey = ' . $this->oDbCon->paramString($rsData[0]['refheaderkey']) .'  and '.$this->tableName.'.statuskey in (1,2,3)';

			// update detail JO
			$sqlDetail = 'update '.$this->tableNameDetail.'
						set   qty = ' . $this->oDbCon->paramString($rsData[0]['sumqty']).', 
							 unitkey = ' . $this->oDbCon->paramString($rsData[0]['sumunitkey']).', 
							 weight = ' . $this->oDbCon->paramString($rsData[0]['sumgrossweight']).',
							 measurement = ' . $this->oDbCon->paramString($rsData[0]['summeas']).' 
						where refkey = '. $this->oDbCon->paramString($rsData[0]['refheaderkey']);
			$this->oDbCon->execute($sqlDetail);
        }
 
        $sql = ' UPDATE '. $tableName .'  SET '.implode(", ", $arrFields).' WHERE ' . $criteria;  
        $this->oDbCon->execute($sql);

		// khusus yg dari HBL, panggil dan update ulang seolah2 dari JO
		// karena kalo dari Job, sudah keupdate semua diatas 
        if(!$isJobOrder) { 
			$this->updateJobOrderAndHouseBL($rsData[0]['refheaderkey']); 
        }
        
    }

    function getDataForGrossPNLFFReport($criteria = '',$order = '')
    {
        $sql = '
        SELECT
            '.$this->tableName.'.* ,
            '.$this->tableCustomer.'.name as customername,
            '.$this->tableEmployee.'.name as salesname,
            '.$this->tableStatus.'.status as statusname,
            
            created.name as createdname, 
            pol_continent.pkey as polcontinentkey,
            pol_continent.name as polcontinentname,
            pod_continent.pkey as podcontinentkey,
            pod_continent.name as podcontinentname
        
        FROM '.$this->tableStatus.',
            '.$this->tableName.'

                left join '.$this->tableEmployee.' on  '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey 
                left join '.$this->tableEmployee.' created on  '.$this->tableName.'.createdby = created.pkey   
                left join ' . $this->tableCustomer . '  on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey

                left join '.$this->tablePort.' pol on  '.$this->tableName.'.polkey = pol.pkey 
                left join '.$this->tablePort.' pod on  '.$this->tableName.'.podkey = pod.pkey

                left join '. $this->tableCity .' pol_city on pol.citykey = pol_city.pkey
                left join '. $this->tableCountry .' pol_country on  pol_city.countrykey = pol_country.pkey
                left join '. $this->tableContinent .' pol_continent on pol_country.continentkey = pol_continent.pkey
                    
                left join '. $this->tableCity .' pod_city on pod.citykey = pod_city.pkey
                left join '. $this->tableCountry .' pod_country on  pod_city.countrykey = pod_country.pkey
                left join '. $this->tableContinent .' pod_continent on pod_country.continentkey = pod_continent.pkey

        WHERE 
            '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
        ';

        $sql .= ' ' .$criteria; 
         
        $sql .= $this->getWarehouseCriteria() ;
        $sql .= $this->getCustomerCriteria() ;
        $sql .= $this->getSalesCriteria() ;
        
        $sql .= ' ' .$order;
  
        $result = $this->oDbCon->doQuery($sql);

        return $result;
    
    }
    function getARPrepaidTax23ByJO($pkey)
    {

        $arPrepaidTax23 = new ARPrepaidTax23();    
        $arPayment =  new ARPayment();
        $ar = new AR();
        $emklOrderInvoice = new EMKLOrderInvoice();
         
        //ARPrepaidTax23 -> ARPayment -> AR -> Inv
    
        $sql = '
                select
                    '. $arPrepaidTax23->tableName .'.pkey,
                    '. $arPrepaidTax23->tableName .'.code,
                    '. $arPrepaidTax23->tableName .'.statuskey as statuskey,
                    '. $arPrepaidTax23->tableStatus .'.status as statusname,
                    '. $arPayment->tableName .'.pkey as arpaymentkey,
                    '. $arPayment->tableName .'.code as arpaymentcode,
                    '. $ar->tableName .'.pkey as arkey,
                    '. $ar->tableName .'.code as arcode,
                    '. $emklOrderInvoice->tableName .'.pkey as invoicekey,
                    '. $emklOrderInvoice->tableName .'.code as invoicecode,
                    '. $emklOrderInvoice->tableNameDetail .'.refsalesorderheaderkey
                from
                    '. $arPrepaidTax23->tableName .',
                    '. $arPayment->tableName .',
                    '. $arPayment->tableNameDetail .',
                    '. $ar->tableName .',
                    '. $emklOrderInvoice->tableName .',
                    '. $emklOrderInvoice->tableNameDetail .',
                    '. $arPrepaidTax23->tableStatus .'
                where
                    '. $arPrepaidTax23->tableName .'.refkey = '. $arPayment->tableNameDetail .'.pkey and
                    '. $arPrepaidTax23->tableName .'.statuskey = '. $arPrepaidTax23->tableStatus .'.pkey and
                    '. $arPayment->tableNameDetail .'.refkey = '. $arPayment->tableName .'.pkey and
                    '. $arPayment->tableName.'.statuskey in (2,3) and
                    '. $arPayment->tableNameDetail .'.arkey = '. $ar->tableName .'.pkey and 
                    '. $ar->tableName .'.refkey = '. $emklOrderInvoice->tableName .'.pkey and
                    '. $emklOrderInvoice->tableName .'.pkey = '. $emklOrderInvoice->tableNameDetail .'.refkey and
                    '. $emklOrderInvoice->tableNameDetail .'.refsalesorderheaderkey in ('. $this->oDbCon->paramString($pkey,',') .') and
					'. $emklOrderInvoice->tableName .'.statuskey in (2,3)
				group by '.$emklOrderInvoice->tableName.'.pkey
                ';
        
			//$this->setLog($sql,true);
            $rs = $this->oDbCon->doQuery($sql);
            
            return $rs;

    }
	
 	function getTotalCostCIP($pkey){
    
        $prepaidExpense = new PrepaidExpense();
        $costReconsile = new CostReconsile();
        $emklPurchaseOrder = new EMKLPurchaseOrder();
        
        $tabletype = $this->getTableKeyAndObj($emklPurchaseOrder->tableName,array('key'))['key'];
        $tableTypeJO = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];

        //cost reconsile -> prepaid expense -> purchase order -> jo
        
        $sql = 'select 
                    coalesce(sum('.$costReconsile->tableNameDetail.'.amount * '.$prepaidExpense->tableName.'.rate),0) as amount,
                    '.$emklPurchaseOrder->tableName.'.refkey as joborderkey
                from 
                    '.$costReconsile->tableName.', 
                    '.$costReconsile->tableNameDetail.', 
                    '.$prepaidExpense->tableName.',
                    '.$this->tableName.',
                    '.$emklPurchaseOrder->tableName.'
                where 
                    '.$costReconsile->tableName.'.statuskey in (2,3) and
                    '.$costReconsile->tableName.'.pkey =  '.$costReconsile->tableNameDetail.'.refkey and
                    '.$costReconsile->tableNameDetail.'.refreconsilekey =  '.$prepaidExpense->tableName.'.pkey and 
                    '.$prepaidExpense->tableName.'.reftabletype = '. $tabletype.' and
                    '.$prepaidExpense->tableName.'.refkey = '.$emklPurchaseOrder->tableName.'.pkey and
                    '.$emklPurchaseOrder->tableName.'.refkey in ('.$this->oDbCon->paramString($pkey,',') .')  and 
                    '.$emklPurchaseOrder->tableName.'.reftabletype = '. $tableTypeJO .' and
                    '.$emklPurchaseOrder->tableName.'.refkey = '.$this->tableName.'.pkey
                    group by  '. $emklPurchaseOrder->tableName.'.refkey
                    '; 
        
        $rs = $this->oDbCon->doQuery($sql);
    
        return $rs;
    }

	function isCommissionRequested($arrPkey){
		if(!$this->activeModule['employeecommission']) return array();
		
		if(!is_array($arrPkey)) $arrPkey = array($arrPkey);
		
	 	$sql = 'select 
					'.$this->tableEmployeeCommissionRequestHeader.'.pkey,
					'.$this->tableEmployeeCommissionRequestHeader.'.code, 
					'.$this->tableEmployeeCommissionRequestDetail.'.jokey
				from
					'.$this->tableEmployeeCommissionRequestHeader.',
					'.$this->tableEmployeeCommissionRequestDetail.'
				where 
					'.$this->tableEmployeeCommissionRequestHeader.'.pkey = '.$this->tableEmployeeCommissionRequestDetail.'.refkey and
					'.$this->tableEmployeeCommissionRequestDetail.'.jokey in ('. $this->oDbCon->paramString($arrPkey,',') .') and
					'.$this->tableEmployeeCommissionRequestHeader.'.statuskey <> 4 
				';
		
		$rs = $this->oDbCon->doQuery($sql);
		$rs = $this->reindexDetailCollections($rs,'jokey');   
		
		return $rs;
	}

    function isCommissionPaid($arrPkey){
		if(!$this->activeModule['employeecommission']) return array();
		
		
		 if(!is_array($arrPkey)) $arrPkey = array($arrPkey);
		
		$rs = $this->searchDataRow( array( $this->tableName.'pkey' , $this->tableName.'iscommissionpaid'),
								  ' and '.$this->tableName.'.pkey in ('. $this->oDbCon->paramString($arrPkey,',') .') '
								  ); 
		 
        return array_column($rs,'iscommissionpaid','pkey');
    }
	
}
?>