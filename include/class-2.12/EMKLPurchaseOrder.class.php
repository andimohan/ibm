<?php
  
class EMKLPurchaseOrder extends BaseClass{ 
 
    function __construct($jobType = ''){
		
		parent::__construct();
       
		$this->tableName = 'emkl_purchase_order_header';
		$this->tableNameDetail = 'emkl_purchase_order_detail';
        $this->tableNameDetailItem = 'emkl_purchase_order_detail_item';
        $this->tablePayment = 'emkl_purchase_order_payment';
		$this->tableStatus = 'transaction_status';
		$this->tableItem = 'item';
        $this->tableJobOrder = 'emkl_job_order_header';
        $this->tableJobOrderDetail = 'emkl_job_order_detail';
        $this->tableJobOrderHeader = 'emkl_order_header'; 
        $this->tableAPPaymentHeader = 'ap_payment_header';
        $this->tableAPPaymentDetail = 'ap_payment_detail';
        $this->tableCostReconsile = 'cost_reconsile'; 
        $this->tableAP  = 'ap';
        $this->tableSupplier = 'supplier';
        $this->tableCustomer = 'customer';
        $this->tableEmployee = 'employee';
        $this->tableCurrency = 'currency'; 
        $this->tableContainer = 'container';
        $this->tableContainerType = 'container_type';
        $this->tableInvoiceHeader = 'emkl_order_invoice_header';
        $this->tableInvoiceDetail = 'emkl_order_invoice_detail';
        $this->tableCashAdvanceRealization = 'cash_advance_realization_header';
        $this->tablePort = 'port';
		$this->tableDebitNoteHeader = 'debit_note_header';
        $this->tableDebitNoteDetail = 'debit_note_detail';
        $this->tableServiceCategory = 'service_category';
        $this->tableItemUnit = 'item_unit';        
        $this->tableContact = 'contact_person';
        $this->tableJobType = 'emkl_import_export';
        $this->tableTransportationType = 'emkl_air_sea';
        $this->tableLoadContainer = 'emkl_fcl_lcl';
        $this->tableVolumeUnit = 'emkl_volume_unit';
        $this->tableFreightTerm = 'emkl_freight_term';
        $this->tableWarehouse = 'warehouse';
		$this->securityObject = 'EMKLPurchaseOrder';
        $this->tableFile = 'emkl_purchase_order_file'; 
        $this->uploadFileFolder = 'emkl-job-order/';
        $this->isTransaction = true;
        $this->jobType = $jobType;
        

        $this->tableFile = 'emkl_purchase_order_file';
        $this->uploadFileFolder = 'emkl-purchase-order/'; 
        $this->useStorage = $this->useStorage('S3');
        $this->importUrl = ($jobType  == EMKL['jobType']['import'] ) ? 'import/FFPurchaseOrderImport' : 'import/FFPurchaseOrderExport';
        
        
        $this->arrDataDetail = array();   
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref'); 
        $this->arrDataDetail['itemkey'] = array('hidContainerDetailKey'); 
        $this->arrDataDetail['servicekey'] = array('hidServiceKey'); 
        $this->arrDataDetail['qty'] = array('qty','number');
        $this->arrDataDetail['priceinunit'] = array('priceInUnit','number'); 
        $this->arrDataDetail['subtotal'] = array('detailSubtotal','number'); 
        $this->arrDataDetail['subtotalcurrency'] = array('detailRowCurrencySubtotal','number'); 
 	    $this->arrDataDetail['currencykey'] = array('selCurrencyDetail'); 
 	    $this->arrDataDetail['description'] = array('description'); 
 	    $this->arrDataDetail['pphamount'] = array('detailPPHAmount','number'); 
 	    $this->arrDataDetail['pphtype'] = array('selPPhType'); 
 	    $this->arrDataDetail['refjoborderdetailkey'] = array('selJobOrderDetailKey'); 
 	    $this->arrDataDetail['unitkey'] = array('detailSelUnit'); 
        
        $this->arrPaymentDetail = array(); 
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 
        
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));    
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
        
       
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
        $this->arrData['refkey'] = array('hidJobOrderKey');
        $this->arrData['code'] = array('code');
        $this->arrData['refinvoicecode'] = array('refInvoiceCode');
        $this->arrData['trdate'] = array('trDate','date');   
        $this->arrData['warehousekey'] = array('selWarehouseKey');    
        $this->arrData['supplierkey'] = array('hidSupplierKey');  
        $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal','number');
        $this->arrData['beforetaxtotal'] = array('beforeTaxTotal','number');
        $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        $this->arrData['taxpercentage'] = array('taxPercentage','number');
        $this->arrData['taxvalue'] = array('taxValue','number'); 
        $this->arrData['totalpayment'] = array('totalPayment','number');
        $this->arrData['balance'] = array('balance','number');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['jobtypekey'] = array('selTypeOfJob');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['grandtotal'] = array('total','number'); 
        $this->arrData['rate'] = array('currencyRate','number');
        $this->arrData['reftabletype'] = array('selJOType');
        $this->arrData['refjoheaderkey'] = array('hidJobHeaderKey');
        $this->arrData['customerkey'] = array('hidShipperKey');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['refcashadvancekey'] = array('refCashAdvanceKey');
        $this->arrData['refcashadvancedetailkey'] = array('refCashAdvanceDetailKey'); 
        $this->arrData['cashadvancecoakey'] = array('cashAdvanceCOAKey');
        $this->arrData['isreimburse'] = array('chkIsReimburse');  
        $this->arrData['totalpph'] = array('totalPPH','number');
        $this->arrData['salesordercodecache'] = array('salesordercodecache');
            
            
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code', 'defaut'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'JOCode','title' => 'JOCode','dbfield' => 'jocode','default'=>true,'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'shipper','title' => (($this->jobType ==1) ? 'importir' : 'exportir') ,'dbfield' => 'shippername','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'invoiceReference','title' => 'invoiceReference','dbfield' => 'refinvoicecode','default'=>true,'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etdpol','title' => 'etd','dbfield' => 'etdpol','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'etapod','title' => 'eta','dbfield' => 'etapod','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname','default'=>true,'width' => 60, 'align'=>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true,'width' => 100, 'align'=>'right','format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
 		array_push($this->arrDataListAvailableColumn, array('code' => 'cadr','title' => 'realization','dbfield' => 'cashadvancerealizationcode','default'=>true, 'width' =>150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 80));
 
		 
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/emklPurchaseOrderExport'));
 
        $this->inTELDomain = array('eagle.wintera.co.id','trioeaglelogistic.wintera.co.id','marvel.wintera.co.id','airtel.wintera.co.id');
        
        $this->includeClassDependencies(array(
              'AP.class.php', 
              'Customer.class.php', 
              'Currency.class.php', 
              'Container.class.php', 
              'EMKLJobOrder.class.php', 
              'EMKLJobOrderHeader.class.php', 
              'PaymentMethod.class.php', 
              'Supplier.class.php', 
              'Service.class.php', 
              'TemplateEMKLPurchaseItem.class.php', 
              'TermOfPayment.class.php', 
              'GeneralJournal.class.php',
              'Warehouse.class.php' ,
              'PrepaidExpense.class.php' ,
              'CashAdvanceRealization.class.php' ,
              'CurrencyRate.class.php' ,
              'CashBank.class.php' ,
              'COALink.class.php'  ,
              'Tax.class.php' 
        ));
        
        $this->overwriteConfig();
        
   }
   
  function getQuery(){
	   
        $sql = '
			SELECT '.$this->tableName.'.* ,
              '.$this->tableSupplier.'.name as suppliername, 
              IF('.$this->tableJobOrder.'.code IS NULL OR  '.$this->tableJobOrder.'.code = \'\' ,'.$this->tableJobOrderHeader.'.code ,'.$this->tableJobOrder.'.code  ) as jocode, 
              '.$this->tableJobOrder.'.etdpol,
              '.$this->tableJobOrder.'.etapod,
              pol.name as polname,
              pod.name as podname,
              '.$this->tableJobOrder.'.bookingnumber,
              '.$this->tableWarehouse.'.name as warehousename,
			  '.$this->tableStatus.'.status as statusname,
			  '.$this->tableCustomer.'.name as shippername,
              '.$this->tableJobType.'.name as jobtype ,
			  '.$this->tableTransportationType.'.name as transportationtype,
              '.$this->tableLoadContainer.'.name as loadcontainertype,
			  '.$this->tableCurrency.'.name as currencyname, 
			  '.$this->tableCashAdvanceRealization.'.code as cashadvancerealizationcode,
              IF( containertypejo.name IS NULL OR  containertypejo.name = \'\' , containertypejoheader.name, containertypejo.name ) as containertype 
			FROM 
                 '.$this->tableName.'
                      left join '.$this->tableCurrency.' on  '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey 
                      left join '.$this->tableJobOrder.' on  '.$this->tableName.'.refkey = '.$this->tableJobOrder.'.pkey 
                      left join '.$this->tableCustomer.' on  '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey 
                      left join '.$this->tableJobType.' on  '.$this->tableJobOrder.'.jobtypekey = '.$this->tableJobType.'.pkey  
                      left join '.$this->tableContainerType.' containertypejo on  '.$this->tableJobOrder.'.containertypekey = containertypejo.pkey 
                      left join '.$this->tableTransportationType.' on  '.$this->tableJobOrder.'.transportationtypekey = '.$this->tableTransportationType.'.pkey 
                      left join '.$this->tableLoadContainer.' on  '.$this->tableJobOrder.'.loadcontainertypekey = '.$this->tableLoadContainer.'.pkey 
                      left join '.$this->tablePort.' pol on  '.$this->tableJobOrder.'.polkey = pol.pkey 
                      left join '.$this->tablePort.' pod on  '.$this->tableJobOrder.'.podkey = pod.pkey 
                      left join '.$this->tableJobOrderHeader.' on  '.$this->tableName.'.refjoheaderkey = '.$this->tableJobOrderHeader.'.pkey 
                      left join '.$this->tableContainerType.' containertypejoheader on  '.$this->tableJobOrderHeader.'.containertypekey = containertypejoheader.pkey 
                      left join '.$this->tableEmployee.' on  '.$this->tableJobOrder.'.saleskey = '.$this->tableEmployee.'.pkey
                      left join '.$this->tableCashAdvanceRealization.' on  '.$this->tableName.'.refcashadvancekey = '.$this->tableCashAdvanceRealization.'.pkey, 
			     '.$this->tableSupplier.',
                 '.$this->tableWarehouse.',
                 '.$this->tableStatus.'
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and  
                '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey ' ;

    
      /*                left join '.$this->tableEmployee.' on  '.$this->tableJobOrder.'.saleskey = '.$this->tableEmployee.'.pkey
                      left join '.$this->tableEmployee.' employeeheader on  '.$this->tableJobOrderHeader.'.saleskey = '.$this->tableEmployee.'.pkey ,
      */
        if (!empty($this->jobType))
            $sql .= ' and '.$this->tableName.'.jobtypekey in ('.$this->jobType.')  ';
           
        $sql .= $this->criteria ; 
      
        $sql .= $this->getWarehouseCriteria() ;
        $sql .= $this->getSalesCriteria('',array(),array($this->tableJobOrderHeader.'.saleskey',$this->tableJobOrder.'.saleskey')) ;
        
        return $sql;
    } 
    
    /*function getQuery(){
	   
	   return '
			SELECT '.$this->tableName.'.* ,
              '.$this->tableJobOrder.'.code as jocode,
              '.$this->tableWarehouse.'.name as warehousename,
              '.$this->tableSupplier.'.name as suppliername,
			  '.$this->tableStatus.'.status as statusname 
			FROM '.$this->tableStatus.',
                 '.$this->tableName.',
                 '.$this->tableWarehouse.',  
                 '.$this->tableJobOrder.',
                 '.$this->tableSupplier.'
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and  
                '.$this->tableName.'.refkey = '.$this->tableJobOrder.'.pkey and 
                '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey 
 		' .$this->criteria ; 
		 
    }*/  
     
    /*function addData($arrParam){
        $arrParam['selStatus'] = 1; 
		return parent::addData($arrParam);  
	}
    
    function editData($arrParam){
        unset( $this->arrData['statuskey']);
		return parent::editData($arrParam); 
	}*/
    
    function afterUpdateData($arrParam,$action){ 
        $this->updateRefkey($arrParam['pkey'],$arrParam['hidJobHeaderKey']);
    }
    
    function afterStatusChanged($rsHeader){   
	    $emklJobOrder = new EMKLJobOrder();
        $cashAdvanceRealization = new CashAdvanceRealization();
        
        $rsJOType = $this->getTableKeyAndObj($emklJobOrder->tableName,array('key'));
        
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
         
        if($rsHeader[0]['reftabletype'] == $rsJOType['key']){   
             // dr JO
            $emklJobOrder->updateTotalBuying($rsHeader[0]['refkey']);
        }else{ 
            // dr header
            $this->updateRefkey($rsHeader[0]['pkey'],$rsHeader[0]['refjoheaderkey']); 
        }
        
        if ($rsHeader[0]['statuskey'] == TRANSACTION_STATUS['konfirmasi']){ 
            $this->changeStatus($rsHeader[0]['pkey'],3,'',false,true); // otomatis, jd bypass hak akses
        }else if($rsHeader[0]['statuskey'] == TRANSACTION_STATUS['batal'] && !empty($rsHeader[0]['refcashadvancedetailkey'])){ 
            // utk Cash Advance
            // kalo cancel, update transkey cash advance  
            $cashAdvanceRealization->removeTransactionLink(explode(',',$rsHeader[0]['refcashadvancedetailkey']),$rsHeader[0]['refcashadvancekey'],$rsHeader[0]['pkey']);
        }
        
    }
	
    function validateForm($arr,$pkey = ''){
        
        $service = new Service(SERVICE);
        $emklJobOrder = new EMKLJobOrder();
          
		$arrayToJs = parent::validateForm($arr,$pkey); 
          
        
		$usePrepaidExpense = $this->loadSetting('usePrepaidExpense');
        
		$typeKey = $arr['selJOType'];  
		$refKey = $arr['hidJobOrderKey'];  
		$refHeaderKey = $arr['hidJobHeaderKey'];  
        $selContainerType = $arr['selContainerType'];
        $containerDetailKey = $arr['hidContainerDetailKey'];
        $serviceDetailKey = $arr['hidServiceKey'];
        $supplierKey = $arr['hidSupplierKey'];  
        $shipmentType = $arr['selAirSea'];
        $arrQty = $arr['qty'];
        $arrPrice = $arr['priceInUnit'];
		$warehousekey = $arr['selWarehouseKey']; 
		$rate = $this->unFormatNumber($arr['currencyRate']); 
		$currencykey = $arr['selCurrency']; 
		$detailCurrency = $arr['selCurrencyDetail']; 
		$subtotal = $this->unFormatNumber($arr['subtotal']); 
        $refCode = $arr['refInvoiceCode'];
        $isReimburse = $arr['chkIsReimburse'];
        $arrJODetailKey = $arr['selJobOrderDetailKey'];
        $arrContainerDetailname = $arr['containerDetailName'];
        
        $rs = (!empty($pkey)) ? $this->getDataRowById($pkey) : array() ;
        
        //validasi kalo status gk menunggu / konfirmasi gk bisa edit 
		if (!empty($rs)){ 
			if ($rs[0]['statuskey'] > 4){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
        
           
        if (empty($warehousekey))  
            $this->addErrorList($arrayToJs,false,$this->errorMsg['warehouse'][1]); 

        if ($isReimburse < 0) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['purchaseOrder'][6]);

         $emklJobOrder = new EMKLJobOrder();
         $rsJOType = $this->getTableKeyAndObj($emklJobOrder->tableName,array('key'));
 
        // dr JO
        if($typeKey == $rsJOType['key']){  
            if(empty($refKey)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['jobOrder'][1]); 
        }else{   
            if(empty($refHeaderKey)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['jobOrder'][1]);
            
        }
		 
        if(empty($supplierKey))
            $this->addErrorList($arrayToJs,false, $this->errorMsg['supplier'][1]); 
        
        
        if($rate <= 0)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['rate'][1]); 
        
        foreach($serviceDetailKey as $value){     
           if(empty($value))
               $this->addErrorList($arrayToJs,false,$this->errorMsg['service'][1]);   
        } 
        
        if($currencykey != CURRENCY['idr'] && $rate == 1 )
               $this->addErrorList($arrayToJs,false,$this->errorMsg['rate'][5]);  
            
        $currFlag = false;
        for($i=0;$i<count($arrQty);$i++){
            $rsItem = $service->getDataRowById($serviceDetailKey[$i]);
            if ($this->unFormatNumber($arrQty[$i]) <= 0 || $this->unFormatNumber($arrPrice[$i]) <= 0){ 
                $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[512]);  
            }
            
            if($detailCurrency[$i] <> $currencykey && $rate == 1)
                $currFlag = true;
        }
        
//        if(!in_array(DOMAIN_NAME, $this->inTELDomain)){
            if ($subtotal <= 0)
                $this->addErrorList($arrayToJs,false, $this->errorMsg[503]);   
//        }
        
            
        if($currFlag)
               $this->addErrorList($arrayToJs,false,$this->errorMsg['rate'][5]);  
            
            
        if(in_array($selContainerType, array(EMKL['container']['fcl'],
                                          EMKL['container']['trucking'],
                                          EMKL['container']['freightcustomfcl'],
                                          EMKL['container']['customfcl']
                                        )
                   )
          ){  
              
            // KALO FCL 
             if ( $shipmentType == EMKL['shipping']['sea']){ 
                foreach($containerDetailKey as $value){      
                       if(empty($value))
                           $this->addErrorList($arrayToJs,false,$this->errorMsg['container'][1]); 
                }  
             }
            
        }else{
            
        }
        
        // validasi refcode 

        $emklPurchaseInvoiceValidation = $this->loadSetting('emklPurchaseInvoiceValidation'); 
		
		if($emklPurchaseInvoiceValidation == 1){ 
			if(empty($arr['refInvoiceCode'])) 
				 $this->addErrorList($arrayToJs,false,$this->errorMsg['invoice'][1]); 

			if(!$this->refcodeExist($pkey, $refCode,$supplierKey,$refHeaderKey,$refKey))
			    $this->addErrorList($arrayToJs,false,'<b>'.$refCode.'</b>. '.$this->errorMsg['invoice'][4]); 
		}
      
		if($usePrepaidExpense == 1 && $arr['chkIsReimburse'] == 1 && $arr['taxPercentage'] > 0) {
            $this->addErrorList($arrayToJs,false,$this->errorMsg['purchaseOrder'][5]); 
        }
        
		return $arrayToJs;
	 }

    function refcodeExist($pkey,$refCode,$supplierKey,$JOHeaderKey,$JOKey){
        
        // harusnya selalu ada jo header dan jokey
        if(empty($JOHeaderKey) && empty($JOKey)) return false;
            
        $emklJobOrder = new EMKLJobOrder();
         
        // cari ke JO / Header
        if(!empty($JOHeaderKey)){
            $rs = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey'),
                                              ' and '.$emklJobOrder->tableName.'.headerorderkey = ' . $this->oDbCon->paramString($JOHeaderKey)
                                              );
            
            $JOKey = $rs[0]['pkey'];
        }
        
        if(!empty($JOKey) && empty($JOHeaderKey)){ 
            //$rs = $emklJobOrder->getDataRowById($JOKey); 
            
            $rs = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.headerorderkey'),
                                              ' and '.$emklJobOrder->tableName.'.pkey = ' . $this->oDbCon->paramString($JOKey)
                                              );
            
            $JOHeaderKey = $rs[0]['headerorderkey'];
        }
        
        $criteria = array();
        if(!empty($JOKey))  array_push($criteria, $this->tableName.'.refkey = '.$this->oDbCon->paramString($JOKey));
        if(!empty($JOHeaderKey))  array_push($criteria, $this->tableName.'.refjoheaderkey = '.$this->oDbCon->paramString($JOHeaderKey));

//        $this->setLog( ' and '.$this->tableName.'.pkey <> '. $this->oDbCon->paramString($pkey).' 
//                                    and '.$this->tableName.'.supplierkey = '.$this->oDbCon->paramString($supplierKey).'
//                                    and '.$this->tableName.'.refinvoicecode = '.$this->oDbCon->paramString($refCode).' 
//                                    and ( '.implode(' or ', $criteria).')',true);
        
        $rs = $this->searchDataRow(array($this->tableName.'.pkey'),
                                  ' and '.$this->tableName.'.pkey <> '. $this->oDbCon->paramString($pkey).' 
                                    and '.$this->tableName.'.supplierkey = '.$this->oDbCon->paramString($supplierKey).'
                                    and '.$this->tableName.'.refinvoicecode = '.$this->oDbCon->paramString($refCode).' 
                                    and ( '.implode(' or ', $criteria).')
                                    and statuskey in (1,2,3)
                                    ' 
                                  ); 
        
        return empty($rs) ? true : false;
    }
    
    function reCountSubtotal($arrParam){
        $subtotal = 0 ;
        $grandtotal = 0; 
        $totalPPH23 = 0;
        
        
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; // kalo pake kasbon, udah pasti isCash = true
        $isCashAdvance = (isset($arrParam['refCashAdvanceKey']) && !empty($arrParam['refCashAdvanceKey'])) ? true : false;
  	$rondedType = $this->loadSetting('invoiceTaxRoundType');
              
        
        //$amount = 0;
        
        $arrItemKey = $arrParam['hidServiceKey'];  
        $arrPriceinunit = $arrParam['priceInUnit'];
        $qtyInBaseUnit =  $arrParam['qty'] ; 
        $isPriceIncludeTax = (isset($arrParam['chkIncludeTax'])) ? $arrParam['chkIncludeTax'] : 0;
        $taxValue = $this->unFormatNumber($arrParam['taxValue']);
        $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);
        $currencykey=  $arrParam['selCurrency'] ; 
        $arrPHP23 =  $arrParam['detailPPHAmount'] ; 
         
        $rate =  $this->unFormatNumber($arrParam['currencyRate']) ; 
        
        $arrItemDetail = array();
        for ($i=0;$i<count($arrItemKey);$i++){
					
            if (empty($arrItemKey[$i]))  
				continue; 
                        
            $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);   
            $qty = $this->unFormatNumber($qtyInBaseUnit[$i]);   

            $detailCurrencySubtotal = $qty * $priceInUnit;
            $arrItemDetail[$i]['detailRowCurrencySubtotal'] = $detailCurrencySubtotal; 
            
            $detailSubtotal = $detailCurrencySubtotal;  
            
            // sementara cuma support 2 currency
            if($currencykey==CURRENCY['idr']){
                if($arrParam['selCurrencyDetail'][$i] <> CURRENCY['idr']) 
					$detailSubtotal *= $rate;
            }else{
                if($arrParam['selCurrencyDetail'][$i] == CURRENCY['idr'])
					$detailSubtotal /= $rate; 
            }

            $arrItemDetail[$i]['detailSubtotal'] = $detailSubtotal;
            
            // kalo bukan cash, dan bukan dari kasbon gk boleh isi php23
            // artinya kalo dari kasbon udah pasti gk direset
            if(!$isCash && !$isCashAdvance)  {
                $arrPHP23[$i] = 0;
            }
            
            $arrPHP23[$i] = $this->unFormatNumber($arrPHP23[$i]);
            
            $arrItemDetail[$i]['detailPPHAmount'] = $arrPHP23[$i];
                
            $subtotal += $detailSubtotal; 
            
            $totalPPH23 +=$arrPHP23[$i];
            
        } 
        
        $beforeTaxTotal = $subtotal;  
        $grandtotal = $beforeTaxTotal;
         
        if ($isPriceIncludeTax == false) {
                $taxValue = $beforeTaxTotal * $taxPercentage / 100;
                if($currencykey==CURRENCY['idr'])
	                $taxValue = $this->getInvoiceRoundedTax($taxValue,$rondedType); 
                $grandtotal += $taxValue;
        }else{
                $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $grandtotal;  
                if($currencykey==CURRENCY['idr'])
	                $taxValue = $this->getInvoiceRoundedTax($taxValue,$rondedType);  
                $beforeTaxTotal = $grandtotal - $taxValue ;
        }
        
        $balance = 0;
        $totalPayment = 0; 
                
        if ($isCash){ 
            $payment = $arrParam['paymentMethodValue'] ?? [];
                for($i=0;$i<count($payment);$i++){
                    $totalPayment += $this->unFormatNumber($payment[$i]);
                }
        }
        $balance = $totalPayment - $grandtotal;


	// pembulatan khusus IDR
         
        
        $reCountResult['detailCOGS'] = $arrItemDetail;
        $reCountResult['subtotal'] = $subtotal;  
        $reCountResult['taxValue'] = $taxValue;
        $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
        $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
        $reCountResult['total'] = $grandtotal;
        $reCountResult['totalPayment'] = $totalPayment;
        $reCountResult['balance'] = $balance + $totalPPH23; 
        $reCountResult['totalPPH'] = $totalPPH23; 
        
        return $reCountResult;
    }	
	 
	function validateConfirm($rsHeader){
        
        $id = $rsHeader[0]['pkey'];

        $termOfPayment = new TermOfPayment();

        $rsPayment = $this->getPaymentMethodDetail($id); 
        $rsDetail = $this->getDetailById($id);
  		
        $balance = 0;
        $totalPayment = 0;

        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;  
        
        $rate =  $this->unFormatNumber($rsHeader[0]['rate']) ; 
        $grandtotal =  $this->unFormatNumber($rsHeader[0]['grandtotal']) ; 
        $currencykey =  $rsHeader[0]['currencykey'] ; 
        $totalPPH =  $rsHeader[0]['totalpph'] ; 
        
        $balance = $totalPayment - $grandtotal + $totalPPH;   
        if($currencykey <> CURRENCY['idr']){
            $balance *= $rate; 
        }

        if ($isCash && empty($rsHeader[0]['refcashadvancekey'])){   
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }
        
        
        // hanya boleh konfirmasi utk JO yg sudah konfirmasi
        // dan harus ad JO nya
        if(!empty($rsHeader[0]['refkey'])){
            $emklJobOrder = new EMKLJobOrder();
            $rsEMKL = $emklJobOrder->getDataRowById($rsHeader[0]['refkey']); 
            // sementara pending sudah boleh input buying, karena sdh ad header JO
            // yg selesai tetep boleh diproses, karena TEL byk yg tertinggal buyignya, repot kalo harus buka tutup JO
            if(empty($rsEMKL) || ( $rsEMKL[0]['statuskey'] == 4)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsEMKL[0]['code'].'</strong>. ' . $this->errorMsg[204]);   
   
            //validasi JO kalau komisi sudah di bayarkan tidak boleh ubah status
            $isCommissionPaid = $emklJobOrder->isCommissionRequested($rsHeader[0]['refkey']);
            if(!empty($isCommissionPaid[$rsHeader[0]['refkey']])) 
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '. </strong>  '.$this->errorMsg[201].'<br>'.$rsEMKL[0]['code'].' - ' .$this->errorMsg['emklJobOrder'][9]); 
            
            
        }else{
            $emklJobOrderHeader = new EMKLJobOrderHeader();
            $rsEMKL = $emklJobOrderHeader->getDataRowById($rsHeader[0]['refjoheaderkey']); 
            if(empty($rsEMKL) || ( $rsEMKL[0]['statuskey'] <> 1))
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].' - '.$rsEMKL[0]['code'].'</strong>. ' . $this->errorMsg[204]);   
        }  
		
		// validasi refcode  
        $emklPurchaseInvoiceValidation = $this->loadSetting('emklPurchaseInvoiceValidation'); 
		
		if($emklPurchaseInvoiceValidation == 1){   
			if(!$this->refcodeExist($id, $rsHeader[0]['refinvoicecode'],$rsHeader[0]['supplierkey'],$rsHeader[0]['refjoheaderkey'],$rsHeader[0]['refkey']))
			   $this->addErrorLog(false,'<b>'.$rsHeader[0]['refinvoicecode'].'</b>. '.$this->errorMsg['invoice'][4]); 
		}
      
		
		
		// item tidak boleh ada double buying
		// ambil informasi tentang services di buying
		$service = new Service(SERVICE);             
		$rsServices = $service->searchDataRow(array($service->tableName.'.pkey',$service->tableName.'.name',$service->tableName.'.allowmultiplepurchase'),
											  ' and '.$service->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'servicekey'),',').')'
											 );
		$rsServices = array_column($rsServices,null,'pkey');

		$allPurchasedServices = $this->getAllPurchasedServices($rsHeader[0]['pkey'],$rsHeader[0]['refjoheaderkey'],$rsHeader[0]['refkey']);
		
		foreach($rsDetail as $detailItemRow){   
			$arrItem = $rsServices[$detailItemRow['servicekey']];
			if($arrItem['allowmultiplepurchase'] == 0 && in_array($detailItemRow['servicekey'],$allPurchasedServices))
			 	$this->addErrorLog(false,'<strong>'.$arrItem['name'].'</strong>, '.$this->errorMsg[215]);    
		}

        $autoSellingReimburse = (in_array($this->loadSetting('autoSellingReimburse'),array(1,2)))  ?  true : false;
        if($autoSellingReimburse && $rsHeader[0]['isreimburse'] == 1 && !empty($rsHeader[0]['refkey']) && empty($rsHeader[0]['refcashadvancekey'])) {
            $emklJobOrder = new EMKLJobOrder();
            //$container = new Container();

            //$arrRefJODetailKey = array_column($rsDetail, 'refjoborderdetailkey');
            //$arrContainerKey = array_column($rsDetail, 'itemkey');

            $rsJO = $emklJobOrder->getDataRowById($rsHeader[0]['refkey']); 

            //$rsJODetail = $emklJobOrder->getDetailById($rsHeader[0]['refkey']);
            //$arrJODetailkey = array_column($rsJODetail, 'pkey');

            //$loadContainerType = $rsJO[0]['loadcontainertypekey'];

            if($rsJO[0]['statuskey'] == TRANSACTION_STATUS['selesai']) {
                $this->addErrorLog(false,'<strong>'.$rsJO[0]['code'].'. </strong> '.$this->errorMsg[220]);    
            }

            //cek JO Detail masih ada di job order tidak (level 1)
            //$isNotExistJODetail = array_diff($arrRefJODetailKey, $arrJODetailkey);
            //if (!empty($isNotExistJODetail)) {
            //    $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '. </strong>' . $this->errorMsg[201]. '<br><strong>'.$rsJO[0]['code'].'. </strong>' . $this->errorMsg['purchaseOrder'][7]);
            //}

            //cek container type sama dengan JO atau tidak
            //perlu cek, karena di validateform JO ad pengecekan harus sama dengan header JO. 
            if(($rsJO[0]['transportationtypekey'] == EMKL['shipping']['sea']) && in_array($loadContainerType, array(EMKL['container']['fcl'],EMKL['container']['freightcustomfcl'],EMKL['container']['customfcl']))) {
                    
                $rsContainer = $container->searchData('','',true, ' and ' . $container->tableName.'.statuskey = 1');
                $rsContainerCols = $this->reindexDetailCollections($rsContainer, 'pkey');

                $rsDetailVolume = $emklJobOrder->getDetailVolume($rsJO[0]['pkey']);
                $rsDetailVolumeCol = $this->reindexDetailCollections($rsDetailVolume, 'itemkey');
                    
                for($i=0; $i<count($arrContainerKey);$i++) {
                    if(!isset($rsDetailVolumeCol[$arrContainerKey[$i]])) {
                        $rsContainerCol = $rsContainerCols[$arrContainerKey[$i]];
                        $this->addErrorLog(false, '<strong>'. $rsContainerCol[0]['name'] .'</strong>. ' . $this->errorMsg['purchaseOrder'][8]);
                    }
                }
    
            }

        }
        

         
	 }
	
	function getAllPurchasedServices($pkey,$joheaderkey,$jokey){
		
		$arrServiceKey = array();
		
		if(!empty($joheaderkey)){
			$sql = 'select	
						'.$this->tableNameDetail.'.servicekey
					from 
						'.$this->tableName.','.$this->tableNameDetail.'
					where 
						'.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
						'.$this->tableName.'.statuskey in (2,3) and
						'.$this->tableName.'.pkey != '.$this->oDbCon->paramString($pkey).' and
						'.$this->tableName.'.refjoheaderkey = '.$this->oDbCon->paramString($joheaderkey);
			$rs = $this->oDbCon->doQuery($sql);
			$arrServiceKey = array_merge($arrServiceKey,array_column($rs,'servicekey'));
		}
		
		if(!empty($jokey)){
			$sql = 'select	
						'.$this->tableNameDetail.'.servicekey
					from 
						'.$this->tableName.','.$this->tableNameDetail.'
					where 
						'.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
						'.$this->tableName.'.statuskey in (2,3) and
						'.$this->tableName.'.pkey != '.$this->oDbCon->paramString($pkey).' and
						'.$this->tableName.'.refkey = '.$this->oDbCon->paramString($jokey);
			$rs = $this->oDbCon->doQuery($sql);
			$arrServiceKey = array_merge($arrServiceKey,array_column($rs,'servicekey')); 
		}
		
		return $arrServiceKey; 
	}
	 
	function confirmTrans($rsHeader){ 
            $id = $rsHeader[0]['pkey'];
        
            $ap = new AP();  
            $supplier = new Supplier();  
            //$service = new Service(SERVICE);  
            $warehouse = new Warehouse();
            $termOfPayment = new TermOfPayment();
            $emklPurchaseOrder = new EMKLPurchaseOrder();
            $emklJobOrder = new EMKLJobOrder();
            $container= new Container(); 
        
            $amount = $rsHeader[0]['grandtotal']; 
        
        // harusnya jgn return saja, tp ad error msg
//            if(!in_array(DOMAIN_NAME, $this->inTELDomain))
//                if ($amount <= 0)  return; 
         
        
		    $rsDetail = $this->getDetailWithRelatedInformation($id);
            $rsPayment = $this->getPaymentMethodDetail($id);
            $rsEMKL = $emklJobOrder->getDataRowById($rsHeader[0]['refkey']); 
            $rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']); 
          
            $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();
            $rate = ($rsHeader[0]['currencykey']==CURRENCY['idr']) ? 1 : $rsHeader[0]['rate']; 
         
            $rsARKey = $ap->getTableKeyAndObj($this->tableName,array('key'));

            $termOfPayment = new TermOfPayment();
            $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
            $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
    
            $note = array();
        
            if ($isCash){
				if( $this->isActiveModule('CashBank') ){
					
					$coaLink = new COALink();  
					$cashBank = new CashBank();  
					
					$arrCashBank = array();

					// MENGHITUNG PAYMENT   
					for($i=0;$i<count($rsPayment); $i++){   
						if (USE_GL) {
						   $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
						   $coakey = $rsPaymentCOA[0]['coakey']; 
					   }else{
						   $coakey = $rsPayment[$i]['paymentkey'];
					   }

					   $rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('supplierkey' => $rsHeader[0]['supplierkey'],'coakey' => $coakey, 'amount' => -$rsPayment[$i]['amount'])); 
					   $rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey'];
					}           

				}
            }else{
              	$arrParam = array();	
 
                $rsHeader[0]['refinvoicecode'] = htmlspecialchars_decode($rsHeader[0]['refinvoicecode']);
                
				$arrParam['code'] = 'xxxxxx';
				$arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
				$arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
				$arrParam['hidRefKey2'] = $rsEMKL[0]['pkey']; 
				$arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
				$arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
				$date = new DateTime($rsHeader[0]['trdate']);
				$date->add(new DateInterval('P'.$rsTOP[0]['duedays'].'D'));
				$arrParam['dueDate'] = $date->format('d / m / Y'); 
				$arrParam['hidRefCode'] = $rsHeader[0]['code'];
				$arrParam['hidRefCode2'] = $rsHeader[0]['refinvoicecode']; 
				$arrParam['hidRefDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
				$arrParam['hidRefTable'] = $rsARKey['key'];
				$arrParam['amount'] =  $amount; 
				$arrParam['amountIDR'] =  $amount * $rate; 
				$arrParam['currencyRate'] = 1;
				$arrParam['trDesc'] = implode(chr(13),$note);
				$arrParam['overwriteGL'] = 1;
				$arrParam['islinked'] = 1;
				$arrParam['selAPType'] = AP_TYPE['serviceOutsource'];
				$arrParam['selWarehouse'] = $warehousekey;
				$arrParam['selCurrency'] =  $rsHeader[0]['currencykey']; 
				$arrParam['currencyRate'] = $rate;
				$arrParam['salesordercodecache'] =  $rsHeader[0]['salesordercodecache']; 

				$arrayToJs = $ap->addData($arrParam);  
				if (!$arrayToJs[0]['valid'])
					throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']); 

            }

        // auto add selling
        // kalo bukan berasal dari cash advance (sementara rulesnya seperti ini utk CIF)
        
        //$au"toSellingReimburse = (in_array($this->loadSetting('autoSellingReimburse'),array(1,2)))  ?  true : false;
        // 1 : semua di add
        // 2 : hanya yg dari PO saja
        if($rsHeader[0]['isreimburse'] == 1){
            $autoAddSellingreimburseType = $this->loadSetting('autoSellingReimburse'); 
            $addReimburse = ($autoAddSellingreimburseType == 1 ||
                            ($autoAddSellingreimburseType == 2 && empty($rsHeader[0]['refcashadvancekey']) )
                            ) ? true : false;
            
              if($addReimburse)
                $this->addEMKLJobOrderItemDetail($rsHeader);
        }
        
		$usePrepaidExpense = $this->loadSetting('usePrepaidExpense');
		if($usePrepaidExpense == 1) $this->addPrepaidExpense($rsHeader);
 
        $this->updateGL($rsHeader,$rsPayment);  
            
	}
 
    
    function cancelTrans($rsHeader,$copy){
        $id = $rsHeader[0]['pkey']; 
		
        $this->cancelVendorAP($rsHeader);
		
		$usePrepaidExpense = $this->loadSetting('usePrepaidExpense');
		if($usePrepaidExpense == 1) $this->cancelPrepaidExpense($rsHeader);
        
		if( $this->isActiveModule('CashBank') ){
			$cashBank = new CashBank();
			$cashBank->cancelCashBank($rsHeader,$this->tableName);
		}

        // matiin dulu, karena di CIF detail JO bisa dipindah
        //$autoSellingReimburse = (in_array($this->loadSetting('autoSellingReimburse'),array(1,2)))  ?  true : false;
        //if($autoSellingReimburse && $rsHeader[0]['isreimburse'] == 1) {
        //    $this->cancelEMKLJobOrderItemDetail($rsHeader);
        //}
		
		if ($copy) $this->copyDataOnCancel($id);
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	}  
    
     
    function validateCancel($rsHeader,$autoChangeStatus=false){
        $ap = new AP();
        $prepaidExpense = new PrepaidExpense();
        $emklJobOrder = new EMKLJobOrder();
		
        $pkey = $rsHeader[0]['pkey'];
        
        parent::validateCancel($pkey,$autoChangeStatus); 
        
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'));    
     
        $rsAP = $ap-> searchDataRow( array(  $ap->tableName.'.pkey', $ap->tableName.'.code'  ) , 
                                ' and  '.$ap->tableName.'.refheaderkey = '.$this->oDbCon->paramString($pkey).' and '.$ap->tableName.'.reftabletype = '.$tablekey['key'].' and ('.$ap->tableName.'.statuskey in(2,3))'  
                       );
        
        if(!empty($rsAP))  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);
    
		
		$usePrepaidExpense = $this->loadSetting('usePrepaidExpense');
		if($usePrepaidExpense == 1){

            $rsCostReconsile = $prepaidExpense->searchDataRow( array(  $prepaidExpense->tableName.'.pkey', $prepaidExpense->tableName.'.code'  ) , 
                               ' and  '.$prepaidExpense->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and '.$prepaidExpense->tableName.'.reftabletype = '.$tablekey['key'].' and ('.$prepaidExpense->tableName.'.statuskey in(2,3))'  
								);

            if(!empty($rsCostReconsile))  
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['prepaidExpense'][2]);
        }


        // gk perlu validasi, karena di CIF, detail di JO bisa pindah
        
        //$autoSellingReimburse = ($this->loadSetting('autoSellingReimburse') == 1)  ?  true : false; 
        //if($autoSellingReimburse && !empty($rsHeader[0]['refkey']) && ($rsHeader[0]['isreimburse'] == 1)) {
//
        //    $rsDetail = $this->getDetailWithRelatedInformation($pkey);
//
        //    $arrDetailKey = array_column($rsDetail, 'pkey');
        //    
        //    //cek detail jo sudah ada yang di invoice atau belum
        //    $criteria = ' and ' . $emklJobOrder->tableNameDetailItem.'.qtyinvoiced > 0 and ' . $emklJobOrder->tableNameDetailItem.'.refpurchaseorderdetailkey in ('. $this->oDbCon->paramString($arrDetailKey,',') .')'; 
        //    $rsJOItemDetail = $emklJobOrder->getItemDetail('', '', $rsHeader[0]['refkey'], $criteria);
        //
        //    if (!empty($rsJOItemDetail)) {
        //        
        //        $arrJOKey = array_column($rsJOItemDetail, 'refheaderkey');
//
        //        $rsJOHeader = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey', $emklJobOrder->tableName.'.code'),
        //                                        ' and '.$emklJobOrder->tableName.'.pkey in ('.$this->oDbCon->paramString($arrJOKey,',').')');
        //        $rsJOHeaderCols = $this->reindexDetailCollections($rsJOHeader, 'pkey');
        //        
        //        $arrErrMsg = array();
        //        foreach($rsJOItemDetail as $itemDetailRow) {
        //            $rsJOHeaderCol = $rsJOHeaderCols[$itemDetailRow['refheaderkey']];
        //            array_push($arrErrMsg, '<strong>'.$rsJOHeaderCol[0]['code'] . '. ' . $itemDetailRow['servicename'] . '</strong>. '. $this->errorMsg['purchaseOrder'][9]);
        //        }
//
        //        if(!empty($arrErrMsg)) {
        //            $this->addErrorLog(false, '<strong>'. $rsHeader[0]['code'] .'.</strong> '. $this->errorMsg[201] .'<br>'. implode( '<br>', $arrErrMsg));
        //        }
//
        //    }
//
        //}

    }
     
    function cancelVendorAP($rsHeader){
        $ap = new AP();   
        
        $rsEMKLKey = $ap->getTableKeyAndObj($this->tableName,array('key'));    
        $arrAPKey = $rsEMKLKey['key'];
     
        $rsAP = $ap-> searchDataRow( array(  $ap->tableName.'.pkey', $ap->tableName.'.code'  ) , 
                                ' and  '.$ap->tableName.'.refheaderkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$ap->tableName.'.reftabletype = '.$arrAPKey.' and '.$ap->tableName.'.statuskey = 1'  
                       );
        
        $totalAP = count($rsAP);
        for($i=0;$i<$totalAP;$i++) { 
            $ap->changeStatus($rsAP[$i]['pkey'],4,'',false, true);  
        }
          
    }
    
    
 function getDetailWithRelatedInformation($pkey){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*, 
                '.$this->tableName.'.rate,
	   			'.$this->tableItem.'.name as servicename,
                '.$this->tableContainer.'.name as containername,
	   			'.$this->tableCurrency.'.name as currencyname,
	   			'.$this->tableItemUnit.'.name as unitname
                
              from
			  	'.$this->tableNameDetail .' 
                    left join '.$this->tableContainer.' on '.$this->tableNameDetail .'.itemkey = '.$this->tableContainer .'.pkey 
                    left join '.$this->tableItemUnit.' on '.$this->tableNameDetail .'.unitkey = '.$this->tableItemUnit .'.pkey ,
                '.$this->tableName.',
			  	'.$this->tableItem .', 
			  	'.$this->tableCurrency .'   
			  where 
                '.$this->tableName .'.pkey = '.$this->tableNameDetail .'.refkey and
			  	'.$this->tableNameDetail .'.currencykey = '.$this->tableCurrency .'.pkey and  
			  	'.$this->tableNameDetail .'.servicekey = '.$this->tableItem .'.pkey and   
			  	'.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ')  '; 
       
        //$sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
	
   }



    function addEMKLJobOrderItemDetail($rsHeader){

        $emklJobOrder = new EMKLJobOrder();

        $pkey = $rsHeader[0]['pkey'];
        $refkey = $rsHeader[0]['refkey'];
        $isreimburse = $rsHeader[0]['isreimburse'];
        
        if($isreimburse != 1) return;
        
        $rsDetail = $this->getDetailById($pkey);

        if(empty($rsDetail)) return;

        $arrJODetailKey = array_unique(array_column($rsDetail, 'refjoborderdetailkey'));
        
        
        // GK PERLU karena proses hanya ketika konfirmasi, jd sudah ke lock
        // harus cek dulu
        // kalo blm ada, add
        // kalo udah ada, update
        // kalo udah gk ad, delete
         
        $updateTaxAtJobOrder = $this->loadSetting('updateTaxAtJobOrder');
        // utk set isreimburse = 0 kal ogk nanti pas invocie gk ketarik, karena format starndart isereimburse = 0 di form Job Order
         
        if($updateTaxAtJobOrder <> 1) $isreimburse = 0;
        
        for($i=0;$i<count($rsDetail);$i++) {

            $detailKey = $rsDetail[$i]['pkey'];
            $jodetailkey = $rsDetail[$i]['refjoborderdetailkey'];
            $itemkey = $rsDetail[$i]['itemkey'];
            $servicekey = $rsDetail[$i]['servicekey'];  
            $qty = $rsDetail[$i]['qty'];
            $priceinunit = $rsDetail[$i]['priceinunit'];
            $subtotal = $rsDetail[$i]['subtotal'];
            $currencykey = $rsDetail[$i]['currencykey'];
            $subtotalcurrency = $rsDetail[$i]['subtotalcurrency'];
            $description = $rsDetail[$i]['description'];

            $sql = '
                    INSERT INTO 
                        '. $emklJobOrder->tableNameDetailItem .'
                        (refkey,
                        refheaderkey,
                        isreimburse,
                        itemkey,
                        servicekey,
                        qty,
                        priceinunit,
                        subtotal,
                        subtotalcurrency,
                        beforetaxdetailvalue,
                        aftertaxdetailvalue,
                        currencykey,
                        trdesc,
                        refpurchaseorderdetailkey)
                    VALUES (
                        '. $this->oDbCon->paramString($jodetailkey) .',
                        '. $this->oDbCon->paramString($refkey) .',
                        '. $this->oDbCon->paramString($isreimburse) .',
                        '. $this->oDbCon->paramString($itemkey) .',
                        '. $this->oDbCon->paramString($servicekey) .',
                        '. $this->oDbCon->paramString($qty) .',
                        '. $this->oDbCon->paramString($priceinunit) .',
                        '. $this->oDbCon->paramString($subtotal) .',
                        '. $this->oDbCon->paramString($subtotalcurrency) .',
                        '. $this->oDbCon->paramString($subtotalcurrency) .',
                        '. $this->oDbCon->paramString($subtotalcurrency) .',
                        '. $this->oDbCon->paramString($currencykey) .',
                        '. $this->oDbCon->paramString($description) .',
                        '. $this->oDbCon->paramString($detailKey) .'
                    );
            ';

            $this->oDbCon->execute($sql);

            
        }
        
//        $emklJobOrder->updateSubtotalDetail($arrJODetailKey);
        $emklJobOrder->updateTotalSelling($refkey);

    }

    function cancelEMKLJobOrderItemDetail($rsHeader) 
    {

        $emklJobOrder = new EMKLJobOrder();
        $pkey = $rsHeader[0]['pkey'];
        $refkey = $rsHeader[0]['refkey'];
        
        $rsDetail = $this->getDetailById($pkey);

        if(empty($rsDetail)) return;

        $arrDetailKey = array_column($rsDetail, 'pkey'); 

        $sql = '
                DELETE FROM
                    '. $emklJobOrder->tableNameDetailItem .'
                where 
                    '. $emklJobOrder->tableNameDetailItem .'.refpurchaseorderdetailkey in ('. $this->oDbCon->paramString($arrDetailKey,',') .')
            ';

            $this->oDbCon->execute($sql);


//        $emklJobOrder->updateSubtotalDetail($arrJODetailKey);
        $emklJobOrder->updateTotalSelling($refkey);
        
    }

	
    function normalizeParameter($arrParam, $trim=false){
        
        
        $termOfPayment = new TermOfPayment();
        
        if(!isset($arrParam['selTypeOfJob'])) $arrParam['selTypeOfJob'] = $this->jobType; 
     
        $emklJobOrder = new EMKLJobOrder();
        $rsJOType = $this->getTableKeyAndObj($emklJobOrder->tableName,array('key')); 
     
        if($arrParam['selJOType'] == $rsJOType['key']){  
            $arrParam['hidJobHeaderKey'] = 0;
            
            $rsJO = $emklJobOrder->getDataRowById($arrParam['hidJobOrderKey']);
            $arrParam['hidShipperKey'] = (!empty($rsJO[0]['customerkey'])) ? $rsJO[0]['customerkey'] : 0;
        }else{ 
            $emklJobOrderHeader = new EMKLJobOrderHeader();
            $arrParam['hidJobOrderKey'] = 0;
            
            $rsJO = $emklJobOrderHeader->getDataRowById($arrParam['hidJobHeaderKey']);
            $arrParam['hidShipperKey'] = (!empty($rsJO[0]['customerkey'])) ? $rsJO[0]['customerkey'] : 0;
        }

        $arrParam['selWarehouseKey'] = $rsJO[0]['warehousekey'];
        $arrParam['salesordercodecache'] = $rsJO[0]['code'];
        
        // kalo gk ada error ketika call API, karena kepake di recount  
        // hati2, jgn semua di berikan nilai default, karena kalo PUT, ad sebagian yg memang tdk diupate
        // bagaimana jika dr API tidak mau update tax misalnya ??
     
        // misalnya lg, API hanya update tgl Purhase, tanpat merubah detail, bagaimana ?
        // utk API, detail hrs di POST ulang
     
        $arrParam['taxValue'] = (isset($arrParam['taxValue'])) ? $arrParam['taxValue'] : 0;
        $arrParam['taxPercentage'] = (isset($arrParam['taxPercentage'])) ? $arrParam['taxPercentage'] : 0; 
        $arrParam['isPriceIncludeTax'] = (isset($arrParam['isPriceIncludeTax'])) ? $arrParam['isPriceIncludeTax'] : 0;  
     
        // rate kalo IDR pasti 1
        //$arrParam['rate'] =   ($arrParam['selCurrency'] == CURRENCY['idr']) ? 1 : $arrParam['rate'];
            
        if(!isset($arrParam['selTermOfPaymentKey'])) {
            $supplier = new Supplier();
            $rsSupplier = $supplier->getDataRowById($arrParam['hidSupplierKey']); 
            $arrParam['selTermOfPaymentKey'] = $rsSupplier[0]['termofpaymentkey'];
        }
        
        $isCashAdvance = (isset($arrParam['refCashAdvanceKey']) && !empty($arrParam['refCashAdvanceKey'])) ? true : false;
        if( !$isCashAdvance ){  
            foreach($arrParam['paymentMethodValue'] as $key=>$row){ 
                if ($this->unFormatNumber($row) == 0){ 
                    unset($arrParam['selPaymentMethod'][$key]);
                    unset($arrParam['paymentMethodValue'][$key]); 
                    unset($arrParam['hidDetailPaymentKey'][$key]); 
                }
            }
            
            $arrParam['selPaymentMethod'] =  array_values($arrParam['selPaymentMethod']);
            $arrParam['paymentMethodValue'] = array_values($arrParam['paymentMethodValue']); 
            $arrParam['hidDetailPaymentKey'] = array_values($arrParam['hidDetailPaymentKey']); 
        }
        
        $reCountResult = $this->reCountSubtotal($arrParam);  
        $arrParam['subtotal'] = $reCountResult['subtotal']; 
        $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
        $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
        $arrParam['taxValue'] = $reCountResult['taxValue'];
        $arrParam['total'] = $reCountResult['total'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
        $arrParam['balance'] = $reCountResult['balance'];  
        $arrParam['totalPPH'] = $reCountResult['totalPPH'];  

        $arrItemKey = $arrParam['hidServiceKey']; 
     
        for ($i=0;$i<count($arrItemKey);$i++){   
            $arrParam['detailSubtotal'][$i] = $reCountResult['detailCOGS'][$i]['detailSubtotal']; 
            $arrParam['detailRowCurrencySubtotal'][$i] = $reCountResult['detailCOGS'][$i]['detailRowCurrencySubtotal'];   
            $arrParam['detailPPHAmount'][$i] = $reCountResult['detailCOGS'][$i]['detailPPHAmount'];
        } 
         
        
        // overwrite
        if( $isCashAdvance ){  
            $arrParam['selPaymentMethod'] = array('0' => -1);
            $arrParam['paymentMethodValue'] = array('0' => $arrParam['total']);
            $arrParam['totalPayment'] = $arrParam['total'];
            $arrParam['hidDetailPaymentKey'] = array('0' => 0);
            $arrParam['balance'] = 0; 
            $arrParam['selTermOfPaymentKey'] = 1; 
        } 
        
        // kalo bkn cash, atau cash advance, haps semua pph 23
         
        $arrParam = parent::normalizeParameter($arrParam,true);
        
        return $arrParam;
    }
    
     
    function getItemDetail($refkey){
        $sql = 'select * from ' .$this->tableNameDetailItem.' where refkey = ' . $this->oDbCon->paramString($refkey);
        return $this->oDbCon->doQuery($sql);
    }
    
    
     function addPrepaidExpense($rsHeader){
        // klo reimburse gk kebentuk prepaid
         // tetep kebentuk saja utk controlcheck sudah ketagih semua atau blm di cost recon nanti
         // hanya saja di cost recon gk kebentuk jurnal
         // dan kalo mau kedepannya ad settingan bisa pilih kebentuk atau gk
         
//        if ($rsHeader[0]['isreimburse'] == 1) return;
         
        $emklJobOrder = new EMKLJobOrder();
        $prepaidExpense = new PrepaidExpense();
        
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
		 
        $rsJOType = $this->getTableKeyAndObj($emklJobOrder->tableName,array('key'));
        $rsPOType = $this->getTableKeyAndObj($this->tableName,array('key'));
        
        $amount = $rsHeader[0]['grandtotal']; 
        if ($amount <= 0)  return; 
         
        $jokey = $rsHeader[0]['refkey'];
        $joheaderkey = $rsHeader[0]['refjoheaderkey'];
         //$date = date('Y-m-d');
		
		 //$this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
         
        $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();
        $rate = ($rsHeader[0]['currencykey']==CURRENCY['idr']) ? 1 : $rsHeader[0]['rate']; 
         
        for($i=0;$i<count($rsDetail);$i++){
 
            $arrParam = array();	
                
            $arrParam['code'] = 'xxxxx'; 
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidJobOrderKey'] = $jokey; 
            $arrParam['selWarehouseKey'] = $warehousekey; 
            $arrParam['hidRefCode'] = $rsHeader[0]['code'];
            $arrParam['hidJobOrderHeaderKey'] = $joheaderkey;
            $arrParam['refsalesordertabletype'] = $rsJOType['key'];
            $arrParam['reftabletype'] = $rsPOType['key'];
            ///$arrParam['trDate'] = $this->formatDBDate($date,'d / m / Y');
			
			$arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
			

            $beforeTaxTotal = $rsDetail[$i]['subtotal'];
            $isPriceIncludeTax = $rsHeader[0]['ispriceincludetax'];
            $total = $rsDetail[$i]['subtotal'];
            $taxPercentage = $rsHeader[0]['taxpercentage'];
            
            $taxValue = 0;
            if ($isPriceIncludeTax == 1) {
                $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $total;   
                $beforeTaxTotal = $total - $taxValue ;
            }
            
            $arrParam['hidCostKey'] = $rsDetail[$i]['servicekey']; 
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
            $arrParam['currencyRate'] = $rate;
            $arrParam['selCurrency'] = $rsHeader[0]['currencykey'];
            $arrParam['amount'] = $beforeTaxTotal;
            $arrParam['amountIDR'] =  $beforeTaxTotal * $rate;
            $arrParam['overwriteGL'] = 1;
            $arrParam['outstanding'] = $beforeTaxTotal;
            $arrParam['islinked'] = 1;  
            $arrParam['chkIsReimburse'] =  $rsHeader[0]['isreimburse'];
            
            $arrayToJs = $prepaidExpense->addData($arrParam);  
            
            if (!$arrayToJs[0]['valid']){
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);  
            }
        }
      
    }

    function cancelPrepaidExpense($rsHeader){  
        $prepaidExpense = new PrepaidExpense();   
        
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'));  

        $rsCostReconsile = $prepaidExpense->searchDataRow( array( $prepaidExpense->tableName.'.pkey', $prepaidExpense->tableName.'.code'  ) , 
                                '  and '.$prepaidExpense->tableName.'.reftabletype = '.$tablekey['key'].' 
                                  and  '.$prepaidExpense->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$prepaidExpense->tableName.'.statuskey = 1'  
                       );
    
        $totalCostReconsile = count($rsCostReconsile);
        for($i=0;$i<$totalCostReconsile;$i++)  
            $prepaidExpense->changeStatus($rsCostReconsile[$i]['pkey'],4,'',false, true);  
     
    }
	
    function updateRefkey($pkey,$refheaderkey){
        // kalo user isi data pilih header, tp sudah ad job order nya
         
        if(empty($refheaderkey)) return;
        
        $emklJobOrder = new EMKLJobOrder();
        $rsJO = $emklJobOrder->searchDataRow( array($this->tableJobOrder.'.pkey'), 
                                              ' and  '. $this->tableJobOrder.'.headerorderkey = '. $this->oDbCon->paramString($refheaderkey).' and 
                                                     '. $this->tableJobOrder.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'    
                                );
         
        if(empty($rsJO)) return;
            
        $sql = 'update '.$this->tableName.' 
                set  '.$this->tableName.'.refkey = '.$rsJO[0]['pkey'].' 
                where '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey);
        
        $this->oDbCon->execute($sql);
        
        // update total buying 
        $emklJobOrder->updateTotalBuying($rsJO[0]['pkey']);
         
    }
       
//	function updateICAGL($rs,$rsPayment,$rsCustomer){
//		//Biaya ICA pada AP ICA
//		
//        $generalJournal = new GeneralJournal(); 
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
//        $arr['trDesc'] = $this->lang['shipper'] .': '. $rsCustomer[0]['name'];
//		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
//		
//		
//        $temp++;
//        $arr['hidCOAKey'][$temp] = $rsCustomer[0]['icacoakey'];
//        $arr['debit'][$temp] =  $rs[0]['grandtotal'];
//        $arr['credit'][$temp] = 0;
//		$arr['trdescDetail'][$temp] = '';
//     	 
//        $temp++;
//        $arr['hidCOAKey'][$temp] = $rsCustomer[0]['apicacoakey'];
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
	
    function updateGL($rs,$rsPayment){
		// ICA bukan berdasarkan vendor, tp berdasarkan cust yg di tagihnya
		// meskiupn vendornya ICA, tetep dianggap biaya, contoh, SMG beli ke JT, SMG tetep anggap biaya, ICA Pembelianyna ad di JKT
		
       
		
        if (!USE_GL) return;
        
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink();
        $supplier = new Supplier();
        $customer = new Customer();
        $service = new Service(SERVICE);     
        $tax = new Tax();
        
        $headerCurrencyKey = $rs[0]['currencykey'];
             
        $warehousekey = $rs[0]['warehousekey'];
		$rate = ($rs[0]['currencykey']==CURRENCY['idr']) ? 1 : $rs[0]['rate'];
		
		$rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']); 
        $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.name', $customer->tableName.'.isica', $customer->tableName.'.icacoakey'),
											   	' and '.$customer->tableName.'.pkey = '.$this->oDbCon->paramString($rs[0]['customerkey'])
											   ); 

		
		
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName,array('key'));
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
		$arr['createdBy'] = 0;  
		$arr['selWarehouseKey'] = $rs[0]['warehousekey']; 
        $arr['trDesc'] = implode(chr(13), array($rs[0]['code'],html_entity_decode($rsSupplier[0]['name']), $this->lang['shipper'] .': '. html_entity_decode($rsCustomer[0]['name'])));
            
		$detailDesc = $rs[0]['code'];
 
        $temp = -1; 
		
				 
		if($rsCustomer[0]['isica'] == 1){ // TIPE ICA
			
			$temp++;
			$arr['hidCOAKey'][$temp] = $rsCustomer[0]['icacoakey'];
			$arr['debit'][$temp] =  $rs[0]['grandtotal'] * $rate;
			$arr['credit'][$temp] = 0;  
			$arr['debitSource'][$temp] =  $rs[0]['grandtotal'];
			$arr['creditSource'][$temp] = 0; 
			$arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
			$arr['rate'][$temp] = $rate ; 
			$arr['refCashBankKey'][$temp] = ''; 
			$arr['trdescDetail'][$temp] = $detailDesc; 

		}else{
			
			// TIPE NORMAL 
			$rsDetail = $this->getDetailById($rs[0]['pkey']);

			$usePrepaidExpense = $this->loadSetting('usePrepaidExpense');
			$usePrepaidExpense = ($usePrepaidExpense == 1) ? true : false;
			$costByJobCategory = $this->loadSetting('splitCOAByJobCategory');


			$eximTypeKey = 0;
			$jobCategoryKey = 0;

			// hanya jika pake split coa
			if($costByJobCategory == 1){ 

				$emklJobOrder = new EMKLJobOrder();
				$emklJobOrderHeader = new EMKLJobOrderHeader();

				$rsRefJoHeaderKey = $emklJobOrderHeader->getTableKeyAndObj($emklJobOrderHeader->tableName,array('key'))['key'];

				 // cari ke JO / Header 
				if($rs[0]['reftabletype'] == $rsRefJoHeaderKey){
					$rsJO = $emklJobOrderHeader->searchDataRow(array($emklJobOrderHeader->tableName.'.pkey',$emklJobOrderHeader->tableName.'.jobtypekey',$emklJobOrderHeader->tableName.'.loadcontainertypekey'),
													  ' and '.$emklJobOrderHeader->tableName.'.pkey = ' . $this->oDbCon->paramString($rs[0]['refjoheaderkey'])
													  ); 

				}else{ 
					$rsJO = $emklJobOrder->searchDataRow(array($emklJobOrder->tableName.'.pkey',$emklJobOrder->tableName.'.jobtypekey',$emklJobOrder->tableName.'.loadcontainertypekey'),
													  ' and '.$emklJobOrder->tableName.'.pkey = ' . $this->oDbCon->paramString($rs[0]['refkey'])
													  ); 
				}

				$eximTypeKey = $rsJO[0]['jobtypekey'];
				$jobCategoryKey = $rsJO[0]['loadcontainertypekey'];
			}

			$arrItemCOA = array();
			foreach($rsDetail as $detail){
				$itemCOAKey = $service->getCostCOAKey($detail['servicekey'],$warehousekey,'outsourcecost',$usePrepaidExpense,$eximTypeKey,$jobCategoryKey, $rs[0]['isreimburse']); 
				//$this->setLog($itemCOAKey,true);
				$totalItemValue = $detail['subtotal']; 
				if ($rs[0]['ispriceincludetax'] == 1 && $rs[0]['taxpercentage'] > 0) {
					$taxValue  = ($rs[0]['taxpercentage']/(100 + $rs[0]['taxpercentage'])) * $totalItemValue;
					$totalItemValue -= $taxValue;   
				}
				$arrItemCOA[$itemCOAKey] = (!isset($arrItemCOA[$itemCOAKey])) ? $totalItemValue : $arrItemCOA[$itemCOAKey] + $totalItemValue; 
			}

			foreach ($arrItemCOA as $coakey => $coaValue){ 

				// khusus TEL sementara, kalo negatif, lariin ke retur pembelian
	//            if(in_array(DOMAIN_NAME, $this->inTELDomain)){
	//                if($coaValue < 0)
	//                    $coakey = $coaLink->getCOALink('purchaseretaildiscount', $warehouse->tableName,$warehousekey, 0)[0]['coakey'];
	//            }

				$temp++;
				$arr['hidCOAKey'][$temp] = $coakey;
				$arr['debit'][$temp] = $coaValue  * $rate; 
				$arr['credit'][$temp] = 0;  
                $arr['debitSource'][$temp] = $coaValue; 
                $arr['creditSource'][$temp] =  0 ; 
                $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                $arr['rate'][$temp] = $rate ;
				$arr['refCashBankKey'][$temp] = ''; 
				$arr['trdescDetail'][$temp] = $detailDesc; 

			}


			$rsCOA = $coaLink->getCOALink ('taxin', $warehouse->tableName,$warehousekey, 0); 
			$temp++;
			$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
			$arr['debit'][$temp] =  $rs[0]['taxvalue'] * $rate; 
			$arr['credit'][$temp] = 0;
            $arr['debitSource'][$temp] = $rs[0]['taxvalue']; 
            $arr['creditSource'][$temp] =  0 ; 
            $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
            $arr['rate'][$temp] = $rate ;
			$arr['refCashBankKey'][$temp] = ''; 
			$arr['trdescDetail'][$temp] = $detailDesc; 
			
            
                   
            // kalo ad pph, utk tipe ICA harusnya gk ada PPH
            if (!empty($rs[0]['totalpph'])){
                // kumpulin total per pph
                $arrPPH = array();

                foreach($rsDetail as $detail){
                    if(!isset($arrPPH[$detail['pphtype']])) $arrPPH[$detail['pphtype']] = 0;
                    $arrPPH[$detail['pphtype']] += $detail['pphamount'];
                }

                //PPH
                $arrPPHTypeKey = array_unique(array_column($rsDetail,'pphtype'));
                $rsTaxCol = $tax->searchDataRow(array('pkey','taxincoakey', 'taxoutcoakey'), ' and '.$tax->tableName.'.typekey = '. $this->oDbCon->paramString(TAX_TYPE['PPH']).' and '.$tax->tableName.'.pkey in (' . $this->oDbCon->paramString($arrPPHTypeKey,',').')');
                $rsTaxCol = array_column($rsTaxCol,null,'pkey');
                
                foreach($arrPPH as $pphKey=>$pphRow){
                        $rsTax = $rsTaxCol[$pphKey];

                        $taxCOAKey = $rsTax['taxincoakey'];

                        $temp++;
                        $arr['hidCOAKey'][$temp] = $taxCOAKey; 
                        $arr['selBusinessUnitKey'][$temp] = 0; // $rsDetail[$i]['businessunitkey']; 
                        $arr['debit'][$temp] = 0; 
                        $arr['credit'][$temp] = $pphRow * $rate;  
                        $arr['debitSource'][$temp] = 0; 
                        $arr['creditSource'][$temp] =  $pphRow ; 
                        $arr['selCurrencyKey'][$temp] = $currencykey; 
                        $arr['rate'][$temp] = $rate ; 
                        $arr['trdescDetail'][$temp] = '';
                        $arr['refCashBankKey'][$temp] = ''; 
                        $arr['trdescDetail'][$temp] = ''; 
                    
                }
 
            }

		}
		
		  
		//payment COA
        $termOfPayment = new TermOfPayment();
		$rsTOP = $termOfPayment->getDataRowById($rs[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
        
        $totalPayment = 0;
        if(!empty($rs[0]['refcashadvancekey'])){ 
            $temp++; 
            $arr['hidCOAKey'][$temp] = $rs[0]['cashadvancecoakey'];
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] =  ($rs[0]['grandtotal'] - $rs[0]['totalpph'])* $rate; 
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] =   $rs[0]['grandtotal']- $rs[0]['totalpph'] ; 
            $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
            $arr['rate'][$temp] = $rate ;
		 	$arr['refCashBankKey'][$temp] = ''; 
			$arr['trdescDetail'][$temp] = $detailDesc; 
        }else{
            if ($isCash) {
                //$rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
                for($i=0;$i<count($rsPayment); $i++){ 
                     $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey, $rsPayment[$i]['paymentkey']);
                     $temp++;
                     $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                     $arr['debit'][$temp] = 0;
                     $arr['credit'][$temp] =  $rsPayment[$i]['amount'] * $rate;  
                     $arr['debitSource'][$temp] = 0; 
                     $arr['creditSource'][$temp] =    $rsPayment[$i]['amount'] ; 
                     $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                     $arr['rate'][$temp] = $rate ;
					 $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey'];  
					 $arr['trdescDetail'][$temp] = $detailDesc; 
                }

                 //selisih pembayaran  

                if($rs[0]['balance'] != 0){ 
                    $temp++; 
                    if ($rs[0]['balance'] < 0){ 
                        $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
                        $arr['debit'][$temp] = 0; 
                        $arr['credit'][$temp] = abs($rs[0]['balance'])  * $rate; 
                        $arr['debitSource'][$temp] = 0; 
                        $arr['creditSource'][$temp] =   abs($rs[0]['balance']) ; 
                    }else{ 
                        $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                        $arr['debit'][$temp] = abs($rs[0]['balance'])  * $rate;  
                        $arr['credit'][$temp] = 0;
                        $arr['debitSource'][$temp] =  abs($rs[0]['balance']); 
                        $arr['creditSource'][$temp] =  0 ; 
                    }

                    
                     $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                     $arr['rate'][$temp] = $rate ;
                    
                    $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
				    $arr['refCashBankKey'][$temp] = ''; 
					$arr['trdescDetail'][$temp] = $detailDesc; 
                }

            }else {  
                    $temp++;
//                  $arr['hidCOAKey'][$temp] = ($rsCustomer[0]['isica'] == 1) ? $rsCustomer[0]['apicacoakey'] : $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
                    $arr['hidCOAKey'][$temp] = $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
                    $arr['debit'][$temp] = 0; 
                    $arr['credit'][$temp] =  $rs[0]['grandtotal'] * $rate;  
                    $arr['debitSource'][$temp] = 0; 
                    $arr['creditSource'][$temp] =  $rs[0]['grandtotal'] ; 
                    $arr['selCurrencyKey'][$temp] = $headerCurrencyKey ; 
                    $arr['rate'][$temp] = $rate ;
                
				    $arr['refCashBankKey'][$temp] = ''; 
					$arr['trdescDetail'][$temp] = $detailDesc; 
            } 
        }
 
        
		$arrayToJs = $generalJournal->addData($arr);
         
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
    }
   
    function afterAddDataOnCopy($pkey, $oldkey){ 
        $rsHeader = $this->getDataRowById($pkey); 
        if(!empty($rsHeader[0]['refcashadvancedetailkey'])){ 
            $cashAdvanceRealization = new CashAdvanceRealization(); 
            $cashAdvanceRealization->updateTransactionLink(explode(',',$rsHeader[0]['refcashadvancedetailkey']),$rsHeader[0]['pkey']);
        } 
    }
    
    function sumTotalAPPayment($opt){ 
        $startDate = $opt['startDate'];
        $endDate = $opt['endDate'];
        $warehousekey = !empty($opt['warehousekey']) ? $opt['warehousekey'] : array(); 
        $containerTypeKey = !empty($opt['containerTypeKey']) ? $opt['containerTypeKey'] : array();
        
        $poTableType = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
            
        // dari JO / Header nya 
        $arrTableType = array();
        
        $objJobOrder = new EMKLJobOrder();
        $joType = $this->getTableKeyAndObj($objJobOrder->tableName,array('key'))['key'];
        $joTable = $this->tableJobOrder; 
        array_push($arrTableType, array('joType' => $joType, 'joTable' => $joTable));        
         
        $objJobOrder = new EMKLJobOrderHeader();
        $joType = $this->getTableKeyAndObj($objJobOrder->tableName,array('key'))['key'];
        $joTable = $this->tableJobOrderHeader; 
        array_push($arrTableType, array('joType' => $joType, 'joTable' => $joTable));  
        
        $returnRs = array(); 
        foreach($arrTableType as $row){

            $joTable = $row['joTable'];
            $joType = $row['joType'];
            
            // AP Payment -> AP -> PO -> JO
            $sql = 'select 
                    coalesce(sum('.$this->tableAPPaymentDetail.'.amount * '.$this->tableAPPaymentHeader.'.rate),0) as amount, 
                    '.$joTable.'.warehousekey,
                    '.$joTable.'.containertypekey 
                from 
                    '.$this->tableAPPaymentHeader.', 
                    '.$this->tableAPPaymentDetail.', 
                    '.$this->tableAP.',   
                    '.$this->tableName.',   
                    '.$joTable.'
                where 
                    '.$this->tableAPPaymentHeader.'.statuskey in (2,3) and
                    '.$this->tableAPPaymentHeader.'.pkey =  '.$this->tableAPPaymentDetail.'.refkey and
                    '.$this->tableAPPaymentDetail.'.apkey =  '.$this->tableAP.'.pkey and 
                    '.$this->tableAP.'.reftabletype = '. $poTableType.' and
                    '.$this->tableAP.'.refheaderkey = '.$this->tableName.'.pkey and
                    '.$this->tableName.'.reftabletype = '.$joType.' and 
                    '.$this->tableName.'.refkey = '.$joTable.'.pkey  and
                    '.$this->tableAPPaymentHeader.'.trdate between '.$this->oDbCon->paramDate($startDate,' / ').' and '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59'); 


//            if(!empty($warehousekey))
//                $sql .= ' and '.$joTable.'.warehousekey in ('.$this->oDbCon->paramString($warehousekey,',').') ';
//
//            if(!empty($containerTypeKey))
//                $sql .= ' and '.$joTable.'.containertypekey in ('.$this->oDbCon->paramString($containerTypeKey,',').') ';

            $sql .= ' group by '.$joTable.'.warehousekey, '.$joTable.'.containertypekey';
            
            $rs = $this->oDbCon->doQuery($sql);
            
            //$total += $rs[0]['totalap'];
            
            $returnRs = array_merge($returnRs,$rs);
        }
 
        return $returnRs;
    }
    
    function sumTotalPurchase($opt){
        $startDate = $opt['startDate'];
        $endDate = $opt['endDate'];
        $warehousekey = !empty($opt['warehousekey']) ? $opt['warehousekey'] : array();
        $topkey = !empty($opt['termOfPaymentKey']) ? $opt['termOfPaymentKey'] : array();
        $containerTypeKey = !empty($opt['containerTypeKey']) ? $opt['containerTypeKey'] : array();
        
        
        // dari JO / Header nya 
        $arrTableType = array();
        
        $objJobOrder = new EMKLJobOrder();
        $joType = $this->getTableKeyAndObj($objJobOrder->tableName,array('key'))['key'];
        $joTable = $this->tableJobOrder; 
        array_push($arrTableType, array('joType' => $joType, 'joTable' => $joTable));        
         
        $objJobOrder = new EMKLJobOrderHeader();
        $joType = $this->getTableKeyAndObj($objJobOrder->tableName,array('key'))['key'];
        $joTable = $this->tableJobOrderHeader; 
        array_push($arrTableType, array('joType' => $joType, 'joTable' => $joTable));        
        
        $returnRs = array();
        foreach($arrTableType as $row){
            $joTable = $row['joTable'];
            $joType = $row['joType'];
            
            $sql = 'select 
                        coalesce(sum( IF('.$this->tableName.'.currencykey = 1,  '.$this->tableName.'.grandtotal,  '.$this->tableName.'.grandtotal * '.$this->tableName.'.rate)),0) as amount, 
                        '.$this->tableName.'.statuskey in (2,3) and
                        '.$joTable.'.warehousekey,
                        '.$joTable.'.containertypekey 
                    from 
                        '.$this->tableName.', '.$joTable.'
                    where 
                        '.$this->tableName.'.reftabletype = '.$joType.' and
                        '.$this->tableName.'.refkey = '.$joTable.'.pkey  and
                        '.$this->tableName.'.trdate between '.$this->oDbCon->paramDate($startDate,' / ').' and '.$this->oDbCon->paramDate($endDate,' / ', 'Y-m-d 23:59:59') .' and 
                        '.$this->tableName.'.termofpaymentkey = ' . $this->oDbCon->paramString($topkey,','); 

//            if(!empty($warehousekey))
//                $sql .= ' and '.$joTable.'.warehousekey in ('.$this->oDbCon->paramString($warehousekey,',').') ';
//
//            if(!empty($containerTypeKey))
//                $sql .= ' and '.$joTable.'.containertypekey in ('.$this->oDbCon->paramString($containerTypeKey,',').') ';
            
             
            $sql .= ' group by '.$joTable.'.warehousekey, '.$joTable.'.containertypekey';
            
            //$this->setLog($sql,true);
            
            $rs = $this->oDbCon->doQuery($sql);
            $returnRs = array_merge($returnRs,$rs);
            //$total += $rs[0]['grandtotal'];

        }
        
        return $returnRs;
    }

    function getDetailJobOrder($arrKey){
        // sementara purchase isi job nya masih di Header, kedepan akan dipindah ke detail
        
        $rs = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.reftabletype',$this->tableName.'.refkey as sokey'),
                                              ' and '.$this->tableName.'.pkey in (' . $this->oDbCon->paramString($arrKey,',').')'
                                              );
        
        return $rs;
    }
    
    function getDebitNote($jokey,$statuskey=array(2,3)){
         
        // sementara purchase isi job nya masih di Header, kedepan akan dipindah ke detail
        // nanti jika JO ad di PO detail, totaldebit harus di prorate ulang
        
        $tablekey =  $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
            
        $sql = 'select 
                   '.$this->tableName.'.pkey as pokey,
                   '.$this->tableName.'.code as pocode,
                   '.$this->tableDebitNoteDetail.'.refpurchasetabletype as purchasetabletype,
                   '.$this->tableJobOrder.'.pkey as sokey,
                   '.$this->tableJobOrder.'.code as socode,
                   '.$this->tableDebitNoteHeader.'.pkey as debitnotekey,
                   '.$this->tableDebitNoteHeader.'.code as debitnotecode,
                   '.$this->tableDebitNoteHeader.'.statuskey as debitnotestatuskey,
                   '.$this->tableDebitNoteDetail.'.totaldebit,
                   '.$this->tableDebitNoteHeader.'.currencykey,
                   '.$this->tableDebitNoteDetail.'.rate,
                   '.$this->tableSupplier.'.name as suppliername 
                from
                  '.$this->tableName.',
                  '.$this->tableJobOrder.',
                  '.$this->tableSupplier.',
                  '.$this->tableDebitNoteHeader.',
                  '.$this->tableDebitNoteDetail.'
                where
                  '.$this->tableName.'.refkey = '.$this->tableJobOrder.'.pkey  and 
                  '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey  and 
                  '.$this->tableName.'.refkey in (' . $this->oDbCon->paramString($jokey,',').') and
                  '.$this->tableName.'.statuskey in (2,3) and 
                  '.$this->tableDebitNoteDetail.'.refpurchasetabletype = '.$this->oDbCon->paramString($tablekey).' and
                  '.$this->tableDebitNoteDetail.'.refpurchasekey = '.$this->tableName.'.pkey and
                  '.$this->tableDebitNoteDetail.'.refkey =  '.$this->tableDebitNoteHeader.'.pkey and
                  '.$this->tableDebitNoteHeader.'.statuskey in ('.$this->oDbCon->paramString($statuskey,',').')
              ';
        
        return $this->oDbCon->doQuery($sql);
    }
    

    function generateDataForReportPurchaseOrder($criteria = '', $order = '', $pkey = '')
    { 
        
        $sql = 'select
                    '.$this->tableNameDetail .'.*, 
                    '.$this->tableName.'.rate,
                    '.$this->tableName.'.code as code,
                    '.$this->tableName.'.refinvoicecode,
                    '.$this->tableName.'.trdate,
                    '.$this->tableItem.'.name as servicename,
                    '.$this->tableContainer.'.name as containername,
                    '.$this->tableCurrency.'.name as currencyname,
                    '. $this->tableSupplier .'.name as suppliername,
                    ' . $this->tableCustomer . '.name as shippername,
                    ' . $this->tableStatus . '.status as statusname,
                    '. $this->tableJobOrder.'.code as jocode,
                        ' . $this->tableJobOrder . '.etdpol,
                        ' . $this->tableJobOrder . '.etapod,
                        pol.name as polname,
                        pod.name as podname,
                        ' . $this->tableJobOrder . '.bookingnumber,
                        ' . $this->tableWarehouse . '.name as warehousename,
                        IF( containertypejo.name IS NULL OR  containertypejo.name = \'\' , containertypejoheader.name, containertypejo.name ) as containertype 
                from
                    '.$this->tableNameDetail .' 
                        left join '.$this->tableContainer.' on '.$this->tableNameDetail .'.itemkey = '.$this->tableContainer .'.pkey ,
                    '.$this->tableName.'
                        left join ' . $this->tableCustomer . ' on  ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey
                        left join ' . $this->tableJobOrder . ' on  ' . $this->tableName . '.refkey = ' . $this->tableJobOrder . '.pkey
                        left join ' . $this->tableJobType . ' on  ' . $this->tableJobOrder . '.jobtypekey = ' . $this->tableJobType . '.pkey
                        left join ' . $this->tableContainerType . ' containertypejo on  ' . $this->tableJobOrder . '.containertypekey = containertypejo.pkey 
                        left join ' . $this->tablePort . ' pol on  ' . $this->tableJobOrder . '.polkey = pol.pkey 
                        left join ' . $this->tablePort . ' pod on  ' . $this->tableJobOrder . '.podkey = pod.pkey
                        left join ' . $this->tableJobOrderHeader . ' on  ' . $this->tableName . '.refjoheaderkey = ' . $this->tableJobOrderHeader . '.pkey 
                        left join ' . $this->tableContainerType . ' containertypejoheader on  ' . $this->tableJobOrderHeader . '.containertypekey = containertypejoheader.pkey, 
                    '.$this->tableItem.', 
                    '.$this->tableCurrency.',
                    '.$this->tableSupplier.',
                    '.$this->tableWarehouse.',
                    '.$this->tableStatus. '
                where 
                    '.$this->tableName .'.pkey = '.$this->tableNameDetail .'.refkey and
                    '.$this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey and
                    '. $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and
                    '. $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                    '.$this->tableNameDetail .'.currencykey = '.$this->tableCurrency .'.pkey and  
                    '.$this->tableNameDetail .'.servicekey = '.$this->tableItem .'.pkey 
        '; 
        
        if (!empty($this->jobType))
            $sql .= ' and ' . $this->tableName . '.jobtypekey in (' . $this->jobType . ')  ';

        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($pkey))  
            $sql .=  '  and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey);
        
        $sql .=  $this->getWarehouseCriteria();
        
        if (!empty($order))  
            $sql .=  ' ' .$order;  

        $result = $this->oDbCon->doQuery($sql);
        //$this->setLog($result, true);
        return $result;
    }
        function getPurchaseService($arrJOKey, $criteria = ''){
		
		$arrServiceKey = array();
		
		$sql = 'select	
					'.$this->tableNameDetail.'.*,
                    '.$this->tableName.'.pkey as headerkey,
                    '.$this->tableName.'.refkey as jokey,
                    '.$this->tableItem.'.name as servicename,
					'.$this->tableServiceCategory.'.pkey as categorykey,
					'.$this->tableServiceCategory.'.name as categoryname
				from 
                    '.$this->tableNameDetail.',
					'.$this->tableItem.'
                        left join ' . $this->tableServiceCategory . ' on  ' . $this->tableItem . '.categorykey = ' . $this->tableServiceCategory . '.pkey,
					'.$this->tableName.'
				where 
					'.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableNameDetail .'.servicekey = '.$this->tableItem .'.pkey and  
					'.$this->tableName.'.statuskey in (2,3) and
                    '.$this->tableName.'.refkey in ('.$this->oDbCon->paramString($arrJOKey,',').')';

        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
                    
		$rs = $this->oDbCon->doQuery($sql);
		
		return $rs; 
	}
    
    function getJobInformation($arrPkey){
        // untuk laporan buku besar
        // 
        //    
        $sql = 'select distinct
                 '.$this->tableJobOrder.'.pkey as jokey,
                 '.$this->tableJobOrder.'.code as jocode,
                 '.$this->tableName.'.pkey as reftablekey
                from  
                 '.$this->tableJobOrder.', '.$this->tableName.' 
                where  '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').') and
                      '.$this->tableName.'.refkey = '.$this->tableJobOrder.'.pkey 
              ';
        
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
    
}
?>
