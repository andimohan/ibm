<?php
class AP extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ap';   
		$this->tablePaymentHeader = 'ap_payment_header';   
		$this->tablePaymentDetail = 'ap_payment_detail';   
		$this->tableStatus = 'ar_status';
		$this->tableSupplier = 'supplier'; 
		$this->tableSupplierCategory = 'supplier_category'; 
        $this->tableType = 'ap_type';
        $this->tableWarehouse = 'warehouse';
        $this->tableCost = 'trucking_service_work_order_cost';
        $this->tableCar = 'car';
        $this->tableCurrency = 'currency';
        $this->tableSalesWorkOrder = 'trucking_service_work_order';
        $this->tableInvoice = 'trucking_service_order_invoice_header'; 
        $this->tableSalesOrder = 'trucking_service_order_header';
        $this->tableEMKLPurchaseOrder = 'emkl_purchase_order_header';
        $this->tableEMKLPurchaseRefund = 'emkl_commission_header';
        $this->tableEMKLSalesOrder = 'emkl_job_order_header';
        $this->tablePort = 'port';
		$this->securityObject = 'AP'; 
        $this->isTransaction = true;
	 
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['refheaderkey'] = array('hidRefHeaderKey');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['refcode'] = array('hidRefCode');
        $this->arrData['refkey2'] = array('hidRefKey2');
        $this->arrData['refcode2'] = array('hidRefCode2'); 
        $this->arrData['refdate'] = array('hidRefDate','date');
        $this->arrData['reftabletype'] = array('hidRefTable');
        $this->arrData['amount'] = array('amount','number');
        $this->arrData['outstanding'] = array('amount','number');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['duedate'] = array('dueDate','date');
        $this->arrData['aptype'] = array('selAPType');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['overwriteGL'] = array('overwriteGL'); 
        $this->arrData['currencykey'] = array('selCurrency'); 
        $this->arrData['autotax'] = array('autoTax'); 
        
        $this->arrData['rate'] = array('currencyRate','number'); 
        $this->arrData['amountidr'] = array('amountIDR','number');
        $this->arrData['paymentmethodkey'] = array('selPaymentMethod'); 
        $this->arrData['refinvoicecode'] = array('hidRefInvoiceCode');
        $this->arrData['pphtype'] = array('selPPhType');  
        $this->arrData['salesordercodecache'] = array('salesordercodecache');
       

        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'duedate','title' => 'duedate','dbfield' => 'duedate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 120 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refInvoiceCode','title' => 'invoiceReference','dbfield' => 'refinvoicecode', 'width' => 120 ));
        
        if ( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) )
           array_push($this->arrDataListAvailableColumn, array('code' => 'refCode2','title' => 'reference','dbfield' => 'refcode2',  'width' => 120 ));
       
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true, 'width' => 200 ));
       
        if ( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) )
            array_push($this->arrDataListAvailableColumn, array('code' => 'currencyShort','title' => 'currencyShort','dbfield' => 'currencyname', 'width' => 60, 'align' =>'center' ));
       
        array_push($this->arrDataListAvailableColumn, array('code' => 'rate','title' => 'currencyRate','dbfield' => 'rate', 'width' => 80, 'align' =>'right' , 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number')); // gk boleh integer, karena sebagian bisa deccimal dan gk
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'aptype','title' => 'transactionType','dbfield' => 'aptypename',  'width' => 120 ));
       
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/ap'));

        $this->refAutoCode = array( 'tablekey' => 'hidRefTable', 'paramPkey' => 'hidRefHeaderKey');
       
        $this->includeClassDependencies(array( 
            'Currency.class.php', 
            'Supplier.class.php',  
            'APPayment.class.php',
            'TruckingServiceOrder.class.php',   
            'TruckingServiceWorkOrder.class.php',   
            'Warehouse.class.php',  
            'COALink.class.php',
            'GeneralJournal.class.php', 
            'APCommission.class.php', 
            'TruckingPurchase.class.php',
            'DebitNote.class.php'
        ));
 
        $this->overwriteConfig();
        
	}
		
   function getQuery(){
       
        if ( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) ){ 

            $rsObjKeyWorkOrder = $this->getTableKeyAndObj($this->tableSalesWorkOrder,array('key')); 
            $rsObjKeyWorkOrderCost = $this->getTableKeyAndObj($this->tableCost,array('key')); 
            $rsObjKeyInvoice = $this->getTableKeyAndObj($this->tableInvoice,array('key')); // utk shared profit
            
            // kalo dr buying emkl, connect nya langsung ke no JO nya aj
            $rsObjKeyEMKLPurchaseOrder = $this->getTableKeyAndObj($this->tableEMKLPurchaseOrder,array('key')); 
            $rsObjKeyEMKLPurchaseRefund = $this->getTableKeyAndObj($this->tableEMKLPurchaseRefund,array('key'));
                
            $sql = '
				select
					'.$this->tableName. '.*,
                    if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0)  as datediff,
					'.$this->tableSupplier.'.name as suppliername,
					'.$this->tableSupplier.'.code as suppliercode,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename, 
					'.$this->tableCurrency.'.name as currencyname ,
                    '.$this->tableType .'.name as aptypename,  
                    CONCAT_WS(\'\',work_order_header_1.pkey, work_order_header_2.pkey) as wokey,
                    CONCAT_WS(\'\',work_order_header_1.code, work_order_header_2.code) as wocode,
                    CONCAT_WS(\'\',car_1.pkey, car_2.pkey) as carkey,
                    CONCAT_WS(\'\',car_1.policenumber, car_2.policenumber) as policenumber,
                    CONCAT_WS(\'\',job_order_header_1.code, job_order_header_2.code) as socode,
                    CONCAT_WS(\'\',location_1.name, location_2.name) as locationname,
                    CONCAT_WS(\'\',customer_1.pkey, customer_2.pkey) as customerkey,
                    CONCAT_WS(\'\',customer_1.code, customer_2.code) as customercode,
                    CONCAT_WS(\'\',customer_1.name, customer_2.name) as customername ,
                    CONCAT_WS(\'\',emkl_order_header_1.code, emkl_order_header_2.code) as emkljocode ,
                    CONCAT_WS(\'\',emkl_order_header_1.mblnumber, emkl_order_header_2.mblnumber) as mblnumber ,
                    CONCAT_WS(\'\',emkl_order_header_1.bookingnumber, emkl_order_header_2.bookingnumber) as bookingnumber ,
                    CONCAT_WS(\'\',emkl_order_header_1.etdpol, emkl_order_header_2.etdpol) as etdpol ,
                    CONCAT_WS(\'\',shipper_1.name, shipper_2.name) as shippername ,
                    CONCAT_WS(\'\',port_1.name, port_2.name) as podname 
				from 
					'.$this->tableName . ' 
                        left join '.$this->tableSalesWorkOrder.' work_order_header_1 on '.$this->tableName.'.refheaderkey = work_order_header_1.pkey and ('.$this->tableName.'.reftabletype = '.$rsObjKeyWorkOrder['key'].' || '.$this->tableName.'.reftabletype = '.$rsObjKeyWorkOrderCost['key'].')
                        left join '.$this->tableCar.' car_1 on work_order_header_1.carkey = car_1.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyWorkOrder['key'].'
                        left join '.$this->tableSalesOrder.' job_order_header_1 on work_order_header_1.refkey = job_order_header_1.pkey and ('.$this->tableName.'.reftabletype = '.$rsObjKeyWorkOrder['key'].' || '.$this->tableName.'.reftabletype = '.$rsObjKeyWorkOrderCost['key'].')
                        left join '.$this->tableCustomer.' customer_1 on job_order_header_1.customerkey = customer_1.pkey
                        left join '.$this->tableLocation.' location_1 on job_order_header_1.stuffinglocationkey = location_1.pkey
                        
                        left join '.$this->tableSalesWorkOrder.' work_order_header_2 on '.$this->tableName.'.refkey2 = work_order_header_2.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyInvoice['key'].'
                        left join '.$this->tableCar.' car_2 on work_order_header_2.carkey = car_2.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyInvoice['key'].'
                        left join '.$this->tableSalesOrder.' job_order_header_2 on work_order_header_2.refkey = job_order_header_2.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyInvoice['key'].'
                        left join '.$this->tableCustomer.' customer_2 on job_order_header_2.customerkey = customer_2.pkey
                        left join '.$this->tableLocation.' location_2 on job_order_header_2.stuffinglocationkey = location_2.pkey
                      
                        left join '.$this->tableEMKLPurchaseOrder.' on '.$this->tableName.'.refkey = '.$this->tableEMKLPurchaseOrder.'.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyEMKLPurchaseOrder['key'].' 
                        left join '.$this->tableEMKLSalesOrder.' emkl_order_header_1 on '.$this->tableEMKLPurchaseOrder.'.refkey = emkl_order_header_1.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyEMKLPurchaseOrder['key'].' 
                        left join '.$this->tableCustomer.' shipper_1 on emkl_order_header_1.customerkey = shipper_1.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyEMKLPurchaseOrder['key'].' 
                        left join '.$this->tablePort.' port_1 on emkl_order_header_1.podkey = port_1.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyEMKLPurchaseOrder['key'].' 
                     
                        left join '.$this->tableEMKLPurchaseRefund.' on '.$this->tableName.'.refkey = '.$this->tableEMKLPurchaseRefund.'.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyEMKLPurchaseRefund['key'].' 
                        left join '.$this->tableEMKLSalesOrder.' emkl_order_header_2 on '.$this->tableEMKLPurchaseRefund.'.refkey = emkl_order_header_2.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyEMKLPurchaseRefund['key'].' 
                        left join '.$this->tableCustomer.' shipper_2 on emkl_order_header_2.customerkey = shipper_2.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyEMKLPurchaseRefund['key'].' 
                        left join '.$this->tablePort.' port_2 on emkl_order_header_2.podkey = port_2.pkey and '.$this->tableName.'.reftabletype = '.$rsObjKeyEMKLPurchaseRefund['key'].' 
                     
                        left join ' . $this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey
                        left join ' .  $this->tableType .' on  '.$this->tableName.'.aptype = ' . $this->tableType .'.pkey
                       ,
                    '.$this->tableStatus.',
                    '.$this->tableSupplier.',
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey'; 
       
        }else{
            $sql = '
                    select
                        '.$this->tableName. '.*,
                        if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0)  as datediff,
                        '.$this->tableSupplier.'.name as suppliername,
                        '.$this->tableStatus.'.status as statusname,
                        '.$this->tableWarehouse.'.name as warehousename, 
                        '.$this->tableCurrency.'.name as currencyname ,
                        '.$this->tableType .'.name as aptypename
                    from 
                        '.$this->tableName . ' 
                            left join ' . $this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey
                            left join ' .  $this->tableType .' on  '.$this->tableName.'.aptype = ' . $this->tableType .'.pkey,
                        '.$this->tableStatus.',
                        '.$this->tableSupplier.',
                        '.$this->tableWarehouse.' 
                    where  		
                        '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
                        '.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
                        '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey';
        }
 
        
		$sql .= ' ' .$this->criteria ; 
       
        $sql .=  $this->getWarehouseCriteria() ;
        
       return $sql;
	}
	    
    function afterDuplicateData($rsHeader){ 
        $arrParam = array();
        $arrParam['pkey'] = $rsHeader[0]['pkey'];
        $arrParam['oldRs'] = '';  
 
        $this->afterUpdateData($arrParam);   
    }
	
    function afterUpdateData($arrParam, $action){ 
        $generalJournal = new GeneralJournal();
        $supplier = new Supplier();
        $truckingServiceOrder = new TruckingServiceOrder();
        
        //$rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $rs = $this->getDataRowById($arrParam['pkey']);
        $oldRs = $arrParam['oldRs']; 
        
        $arr1 =array();
        array_push($arr1,$rs[0]['aptype']); 
        array_push($arr1,$rs[0]['warehousekey']); 
        array_push($arr1,$rs[0]['supplierkey']); 
        array_push($arr1,$rs[0]['amount']); 
        array_push($arr1,$rs[0]['trdate']); 
        array_push($arr1,$rs[0]['rate']); 
        $arr1 = md5(json_encode($arr1));
         
        $arr2 = array();
        if(!empty($oldRs)){ 
            array_push($arr2,$oldRs[0]['aptype']); 
            array_push($arr2,$oldRs[0]['warehousekey']); 
            array_push($arr2,$oldRs[0]['supplierkey']); 
            array_push($arr2,$oldRs[0]['amount']); 
            array_push($arr2,$oldRs[0]['trdate']); 
            array_push($arr2,$oldRs[0]['rate']); 
        }
        $arr2 = md5(json_encode($arr2));
        
        $same = ($arr1 == $arr2) ? true : false;
	           
        // kalo blm ad jurnal, add
        if (empty($oldRs)){ 
            $this->updateGL($rs);
        }else{
            if (!$same){ 
                //kalo ud ad cek perlu add ulang atau tidak
                $this->cancelGLByRefkey($arrParam['pkey'],$this->tableName);
                $supplier->updateAPOutstanding($oldRs[0]['supplierkey']);
                
                $this->updateGL($rs);
            } 
        }    
            
        $supplier->updateAPOutstanding($rs[0]['supplierkey']);
        
        $rsObjKeyInvoice = $this->getTableKeyAndObj($this->tableInvoice);
        if($rs[0]['reftabletype'] == $rsObjKeyInvoice['key']){
            $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
            $rsWo = $truckingServiceWorkOrder->getDataRowById($rs[0]['refkey2']);
            $truckingServiceOrder->updateTotalSharedProfit($rsWo[0]['refkey']);
            
        }
    }
     
    function afterAddDataOnCopy($pkey, $oldkey){   
//        $rs = $this->getDataRowById($pkey);     
//        $supplier = new Supplier();
//        $supplier->updateAPOutstanding($rs[0]['supplierkey']); 
		
			
		$rsHeader = $this->getDataRowById($pkey);
        $arrParam = array();
        $arrParam['pkey'] = $rsHeader[0]['pkey'];
        $arrParam['oldRs'] = '';  
 
        $this->afterUpdateData($arrParam,INSERT_DATA);  
		
		
    }
    
    function  updateGL($rs){
           
        if (!USE_GL) return;
         
        if ($rs[0]['overwriteGL'] == 1)
            return; 
        
        //kalo amount sama gk perlu cancel
        $this->cancelGLByRefkey($rs[0]['pkey'],$this->tableName); 
        
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();  
        $generalJournal = new GeneralJournal();
        $supplier = new Supplier();
		
        $warehousekey = $rs[0]['warehousekey']; 
            
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
		$arr['refCode'] = $rs[0]['code'];
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		
		$temp = -1; 
        
		// debit   
        switch ($rs[0]['aptype']){ 
             
            case 2 : 
                    $rsCOA = $coaLink->getCOALink ('outsourcecost', $warehouse->tableName, $warehousekey);   

                
                    break;
                
            case 4 : 
                    $rsCOA = $coaLink->getCOALink ('commissioncost', $warehouse->tableName, $warehousekey);   

                
                    break;
                
            // purchase
            default : 
                    $rsCOA = $coaLink->getCOALink ('inventory', $warehouse->tableName, $warehousekey);   
   
                
                    break;
          
        }
		$temp++;
		$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
		$arr['debit'][$temp] = $rs[0]['amountidr']; 
		$arr['credit'][$temp] = 0;  
		$arr['debitSource'][$temp] = $rs[0]['amount'];  
		$arr['creditSource'][$temp] = 0 ; 
		$arr['selCurrencyKey'][$temp] = $rs[0]['currencykey']; 
		$arr['rate'][$temp] = $rs[0]['rate']; 
        
        
        // credit
        switch ($rs[0]['aptype']){ 
                
            case 4 :  $coakey = $coaLink->getCOALink ('commissionap', $warehouse->tableName, $warehousekey)[0]['coakey'];  
                        break;

            default :  $coakey = $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey); 
                        break;

        }

        $temp++; 
        $arr['hidCOAKey'][$temp] = $coakey;
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $rs[0]['amountidr'];  
        $arr['debitSource'][$temp] = 0;  
        $arr['creditSource'][$temp] = $rs[0]['amount']; ; 
        $arr['selCurrencyKey'][$temp] = $rs[0]['currencykey']; 
        $arr['rate'][$temp] = $rs[0]['rate']; 
         
        
		$arrayToJs = $generalJournal->addData($arr); 
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    }
    
	function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
		 
		$supplierkey = $arr['hidSupplierKey']; 
		$amount = $this->unFormatNumber($arr['amount']);
		
         
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
        
		if(empty($supplierkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
		}
		
        if (!is_numeric($amount) || $amount == 0){  // positif negative sdh di normalize
			$this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][1]);
		}
		
		  	
		return $arrayToJs;
	 } 
	 
	 function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
         
		$sql = 'select
					'.$this->tableName. '.pkey,     
                    concat('.$this->tableName.'.code ,  IFNULL(concat(\'-\','.$this->tableName. '.refcode), \'\') ) as value , 
                    '.$this->tableName. '.code as code , 
                    '.$this->tableName.'.refcode, 
                    '.$this->tableName.'.duedate, 
                    '.$this->tableName.'.refcode2,
                    '.$this->tableName.'.refinvoicecode,
                    '.$this->tableName.'.refkey,
                    '.$this->tableName.'.refdate, 
                    '.$this->tableName. '.amount,  
                    '.$this->tableName. '.currencykey,  
                    '.$this->tableName. '.autotax,  
                    '.$this->tableName. '.outstanding
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
	
		if(!empty($fieldname)){
			
			$sql .= ' and ' ;
			
			if($mustmatch)
				$sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$sql .=  '('.$fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%') .' || '. $this->tableName .'.refcode like '. $this->oDbCon->paramString('%'.$searchkey.'%').')';
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

	
    function afterStatusChanged($rsHeader){ 
 
        $supplier = new Supplier();
        $supplier->updateAPOutstanding($rsHeader[0]['supplierkey']);

	   // ambil ulang status terakhir
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if($rsHeader[0]['statuskey']==TRANSACTION_STATUS['batal']){
           $rsObjKeyInvoice = $this->getTableKeyAndObj($this->tableInvoice);
            if($rsHeader[0]['reftabletype'] == $rsObjKeyInvoice['key']){
                $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
                $truckingServiceOrder = new TruckingServiceOrder();
                $rsWo = $truckingServiceWorkOrder->getDataRowById($rsHeader[0]['refkey2']);
                $truckingServiceOrder->updateTotalSharedProfit($rsWo[0]['refkey']); 
            } 
        }

/*
        Dipindahkan ketika add AP
        
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder(); 
        // kalo tipenya utk outsource jasa (SPK) 
        $rsAPKey = $this->getTableKeyAndObj($truckingServiceWorkOrder->tableName);   
        $headerType = $rsAPKey['key'];
        
        $rsAPKey = $this->getTableKeyAndObj($truckingServiceWorkOrder->tableCost);  
        $costType = $rsAPKey['key'];
        
        // get latest statuskey
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        
        if ($rsHeader[0]['aptype'] == AP_TYPE['serviceOutsource']){  
            if ($rsHeader[0]['reftabletype'] == $headerType)
                $tableName = $truckingServiceWorkOrder->tableName;
            else if ($rsHeader[0]['reftabletype'] == $costType)
                $tableName = $truckingServiceWorkOrder->tableCost;
            else
                throw new Exception('<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201]); 

            $refcashoutkey = ($rsHeader[0]['statuskey'] == 2 or $rsHeader[0]['statuskey'] == 3) ? $rsHeader[0]['pkey'] : 0;
            
            $sql = 'update '.$tableName.' set refcashoutkey = ' . $refcashoutkey .' where pkey = ' . $rsHeader[0]['refkey']; 
            $this->oDbCon->execute($sql);  
        }*/
    }
    
	function changeStatus($id,$status,$reason='',$copy=false,$autoChangeStatus=false, $dontValidate = false){
		
		$arrayToJs = array();
		  
		  try{ 
			     $rs = $this->getDataRowById($id);
              
                if(!$dontValidate){
                   switch ($status){
                               case 1 : $arrayToJs = $this->validateOpen($id);
                                         if (!empty($arrayToJs)) 
                                                return $arrayToJs;  
                                          break; 
                                case 2 : $arrayToJs = $this->validatePartial($id);
                                         if (!empty($arrayToJs)) 
                                                return $arrayToJs;  
                                          break; 
                                 case 3 : $arrayToJs = $this->validateClosed($id);
                                             if (!empty($arrayToJs)) 
                                                    return $arrayToJs;  
                                              break; 
                                case 4 : $arrayToJs = $this->validateCancel($id, $autoChangeStatus);
                                         if (!empty($arrayToJs)) 
                                                return $arrayToJs;  
                                          break; 

                    } 
                } 
		  
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
		 	
						 
			switch ($status){  
				case 4 : $this->cancelTrans($id,$copy);
                          $this->afterCancelTrans($id);
                          break;  
			}
            
            $sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id);
			$this->oDbCon->execute($sql);
 
            $rsStatus = $this->getStatusById ($status); 
            $this->setTransactionLog($rsStatus[0]['pkey'],$id);	
               
            $this->afterStatusChanged($rs);
            
			$this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    } catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 
 		return $arrayToJs; 
 	}
	 
	
	
	function delete($id, $forceDelete = false,$reason = ''){ 
		
		 $arrayToJs =  array();  
		 $arrayToJs = $this->changeStatus($id, 4);   // harus ad validasi kalo islinked, gk boleh dihapus
		 return $arrayToJs; 

	}
	
   function cancelTrans($id,$copy){   
        $rsHeader = $this->getDataRowById($id); 
       
       
        $autoCancel = $this->loadSetting('autoCancelAPPayment');
       
        if($autoCancel == 1){
            $paymentObj = $this->getPaymentObj(); 
            $sql = 'select 
                        '.$paymentObj->tableName.'.pkey
                    from
                        '.$paymentObj->tableName.','.$paymentObj->tableNameDetail .'
                    where
                        '.$paymentObj->tableName.'.pkey = '.$paymentObj->tableNameDetail.'.refkey and
                        '.$paymentObj->tableName.'.statuskey = 1 and
                        '.$paymentObj->tableNameDetail.'.apkey = '.$paymentObj->oDbCon->paramString($id).' 
                    ';

            $rs = $paymentObj->oDbCon->doQuery($sql);

            for($i=0;$i<count($rs);$i++) 
                $paymentObj->changeStatus($rs[$i]['pkey'],4,'',false,true);  
        }
        
		if ($copy)
			$this->copyDataOnCancel($id);	  
	  
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
    // ============= MAKE SURE USER CANNOT MANUAL UPDATE STATUS
	 function validateOpen($id){ 
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201]);
		return $arrayToJs;
	 } 	
    
    
	 function validatePartial($id){ 
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg['ap'][3]);
     	return $arrayToJs;
	 } 	
    
    function validateClosed($id){ 
         
        $arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201]);
		return $arrayToJs;
      
	 } 	 
    // ============= MAKE SURE USER CANNOT MANUAL UPDATE STATUS
    
    
	function validateCancel($id,$autoChangeStatus=false){ 
         // perlu cek validasi lg kalo ad payment yg sudah dikonfirmasi bagaimana ?
        // atau gk perlu selama statusnya tdk open 
          
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
           
        if ( !$autoChangeStatus ) {
            if(!empty($rs[0]['islinked'])) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg['ap'][4]);    
        } 
         
        // transaksi tetep tidak boleh dibatalkan jika sudah ad pembayaran (status AR <> open)  
        // meskipun transaksi manual atau transaksi dr sales order 
        if ( $rs[0]['statuskey'] <> 1) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[201]);     
         
        
        // cek sudah pernah dibuatkan DN blm
        if($this->isActiveModule('DebitNote')){ 
            $debitNote = new DebitNote();
            $tablekey =  $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
            $rsDN = $debitNote->getDebitNoteDetailByAP($rs[0]['pkey'],$tablekey, false,array(DN_TYPE['ap']));
            if ( !empty($rsDN)) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong> '. $this->errorMsg['ap'][9] . '('.$rsDN[0]['code'].')');     

        }
        
		return $arrayToJs;
	 } 	
	 
    
    	
    function getAPOutstanding($supplierkey){
        $sql = 'select coalesce(sum(outstanding*rate),0) as outstanding from ' . $this->tableName .' where supplierkey = ' . $this->oDbCon->paramString($supplierkey) .' and (statuskey = 1 or statuskey = 2)' ;
        $rs = $this->oDbCon->doQuery($sql);
        return $rs[0]['outstanding'];
    }
      
    function getPaymentObj(){
        return  new APPayment();
    }
    
    function getSupplierObj(){
        return  new Supplier();
    }
     
    
    function getAPType($additionalType = array(),$overwriteType = false){
          
        $typekey = array();
        if(!$overwriteType){ 
            array_push($typekey, AP_TYPE['itemPurchase']);
            array_push($typekey, AP_TYPE['downPayment']);
            array_push($typekey,AP_TYPE['serviceOutsource'],AP_TYPE['salesCommission'],AP_TYPE['otherCost']);  
        } 
           
        $typekey = array_merge($typekey, $additionalType);
        
        
        $sql = 'select * from '.$this->tableType.' where pkey in ('.$this->oDbCon->paramString($typekey,',').') and statuskey = 1 ';
        $rs = $this->oDbCon->doQuery($sql);	
        
        return $rs;
    }
     
     
    function normalizeParameter($arrParam, $trim = false){
         
        $arrParam['selCurrency'] = (!empty($arrParam['selCurrency'])) ? $arrParam['selCurrency'] : CURRENCY['idr'];
          
        $rsAPType = $this->getAPType(array($arrParam['selAPType']),true);  
        
        $arrParam['amount'] = $this->unFormatNumber($arrParam['amount']);
            
        if($arrParam['selAPType']==AP_TYPE['carServiceMaintenance'] || $arrParam['selAPType']==AP_TYPE['otherCost']){
            $arrParam['amount'] = abs($arrParam['amount']);
            $arrParam['amount'] *= -1;
        }
        
        $rate = (isset($arrParam['currencyRate']) && !empty($arrParam['currencyRate'])) ? $this->unFormatNumber($arrParam['currencyRate']) : 1;
        $arrParam['amountIDR'] =  $arrParam['amount'] * $rate;
        
        $arrParam = parent::normalizeParameter($arrParam,true);  
        //$arrParam['hidRefDate'] = (!empty($arrParam['hidRefDate'])) ? $arrParam['hidRefDate'] : DEFAULT_EMPTY_DATE; 
         
        // old rs
        $oldRs = $this->getDataRowById($arrParam['pkey']);
        $arrParam['oldRs'] = $oldRs;
        
        return $arrParam;
    }
    
     
	function updateAPOutstanding($apkey){
	    $apPaymentObj = $this->getPaymentObj(); 
		$rsAP = $this->getDataRowById($apkey);
        
               // kalo statusnya sudah batal tdk boleh dibalikin lg.
        // case karena terakhir update AP dicancel gk otomatis cancel payment
        if($rsAP[0]['statuskey'] == 4) return;
            
		$sql = 'select 
						coalesce(sum('.$apPaymentObj->tableNameDetail.'.amount + '.$apPaymentObj->tableNameDetail.'.discount),0) as totalPaidAmount
				 from 
				 	' . $apPaymentObj->tableName.','.$apPaymentObj->tableNameDetail. '
				 where ' . $apPaymentObj->tableNameDetail.'.refkey = '.$apPaymentObj->tableName .'.pkey and 
				 	  ('.$apPaymentObj->tableName .'.statuskey = 2 or '.$apPaymentObj->tableName .'.statuskey = 3 )and
					  '.$apPaymentObj->tableNameDetail.'.apkey = '.$apPaymentObj->oDbCon->paramString($apkey).'
				'  ;
         
		$rsAmount =  $this->oDbCon->doQuery($sql); 
		$totalPaidAmount = $rsAmount[0]['totalPaidAmount'];
            
        //cari balancenya saja
        $balance  = $rsAP[0]['amount'] - $totalPaidAmount;
        
        // kalo balance sudah dibawah rounding X rupiah, dianggap lunas
        // tergantung rate, kalo idr kurang dari 1, kalo currency kurang dr 0,01 ?
        
        $balanceRounding = ($rsAP[0]['currencykey'] == CURRENCY['idr']) ? ARAP_BALANCE_ROUNDING['idr'] : ARAP_BALANCE_ROUNDING['currency'];
           
        $tempBalance = round($balance * 100)/100; // buat buletin yg 0.00999999999 semoga saja bisa
        if($tempBalance < $balanceRounding && $tempBalance >  $balanceRounding * -1)   
            $balance = 0;   
            
        // haruss diatas, sebelum balancenya dikali -1 
	    $sql  = 'update '.$this->tableName.' set outstanding =' .$this->oDbCon->paramString($balance) .' where statuskey <> 4 and pkey = ' . $this->oDbCon->paramString($apkey) ;	 
	    $this->oDbCon->execute($sql);  
        
        $arrAPNegative = array(AP_TYPE['carServiceMaintenance'],AP_TYPE['otherCost'],AP_TYPE['debitNote']);
        
        if (in_array($rsAP[0]['aptype'],$arrAPNegative)) $balance *= -1;
        
        $statuskey = AP_STATUS['open']; 
        
        if ($balance <= 0)  // lunas
			$statuskey = AP_STATUS['lunas'];
        else if ($balance > 0 && $balance < abs($rsAP[0]['amount'])) // partial, pake abs utk positifin CN
		    $statuskey =  AP_STATUS['partial'];
       
 

        if($rsAP[0]['statuskey'] <> $statuskey)
            $this->changeStatus($apkey,$statuskey, '', false, true,true);
 
		
	}
	     
    function getAPTypeName($arrTypeKey){ 
        if (!is_array($arrTypeKey))  
            $arrTypeKey = array($arrTypeKey); 
            
        $sql = 'select * from '.$this->tableType.' where pkey in ('.$this->oDbCon->paramString($arrTypeKey,',').') and statuskey = 1 '; 
        return $this->oDbCon->doQuery($sql); 
    }
    
    function groupAPBySupplier($rs, $rsCurrency = array()){
        
        $totalCurrency = count($rsCurrency);
        
	   // bagi per customer
        $arrAPSupplier = array();
        foreach($rs as $row){
            $supplierkey = $row['supplierkey'];
            
            // init
            if (!isset($arrAPSupplier[$supplierkey])){
            
                $arrAPSupplier[$supplierkey] = array('suppliername' => $row['suppliername'], 'detail' => array());
                
                if($totalCurrency <= 1){
                    $arrAPSupplier[$supplierkey]['totalamount'] = 0;
                    $arrAPSupplier[$supplierkey]['totaloutstanding'] = 0;
                }else{
                    foreach($rsCurrency as $currencyRow){ 
                        $arrAPSupplier[$supplierkey]['totalamount'.$currencyRow['pkey']] = 0;
                        $arrAPSupplier[$supplierkey]['totaloutstanding'.$currencyRow['pkey']] = 0;
                    }
                }
              
            } 
            
             
            if($totalCurrency <= 1){
                $arrAPSupplier[$supplierkey]['totalamount'] += $row['amount'];
                $arrAPSupplier[$supplierkey]['totaloutstanding'] += $row['outstanding'];
            }else{ 
                $arrAPSupplier[$supplierkey]['totalamount'.$row['currencykey']] += $row['amount'];
                $arrAPSupplier[$supplierkey]['totaloutstanding'.$row['currencykey']] += $row['outstanding'];
            }

            array_push($arrAPSupplier[$supplierkey]['detail'], $row); 
            
        }

        // agar bisa pake for i di report
        return array_values($arrAPSupplier);
    } 
    
    
 	function generateAPReport($criteria='',$order=''){
        
	   $sql =  '
			SELECT 
					GROUP_CONCAT('.$this->tableName.'.pkey) as pkey,
					coalesce(sum(outstanding),0) as totaloutstanding,
					coalesce(sum(amount),0) as totalamount,
                   '.$this->tableSupplier.'.name as suppliername  ,
                   '.$this->tableCurrency.'.pkey as currencykey,
                   '.$this->tableCurrency.'.name as currencyname 
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableName.',
                '.$this->tableSupplier.',
                '.$this->tableCurrency.',
				'.$this->tableWarehouse.'
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria;  
         
		
		$sql .= ' group by supplierkey, currencykey';
         
        if (!empty($order))  
            $sql .=  ' ' .$order; 
     
        //$this->setLog($sql,true);
       return $this->oDbCon->doQuery($sql);
		 
    } 
    
    function getDetailBySupplierKey($criteria='',$supplierkey=''){
              
	   $sql =  '
			SELECT '.$this->tableName.'.code,
                    if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),'.$this->tableName. '.duedate) , 0)  as datediff,
                   '.$this->tableName.'.refkey, 
                   '.$this->tableName.'.refkey2, 
                   '.$this->tableName.'.refcode, 
                   '.$this->tableName.'.refcode2, 
                   '.$this->tableName.'.refdate, 
                   '.$this->tableName.'.trdate,
                   '.$this->tableName.'.amount, 
                   '.$this->tableName. '.outstanding,
                   '.$this->tableName. '.trdesc,
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableWarehouse.'.name as warehousename, 
                   '.$this->tableSupplier.'.name as suppliername  
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableName.'
                    left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey,
                '.$this->tableWarehouse.'
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($supplierkey))  
            $sql .=  '  and '.$this->tableName.'.supplierkey = ' .$this->oDbCon->paramString($supplierkey); 

         
        //$this->setLog($sql);
       return $this->oDbCon->doQuery($sql);
    }
    
    function searchAPCard($datePeriod, $criteria, $order){
        
        $datePeriod = $this->oDbCon->paramDate($datePeriod,' / ','Y-m-d 23:59');
        
        $sql = 'select 

                    '.$this->tableName.'.pkey,
                    '.$this->tableName.'.code,
                    datediff('.$datePeriod.','.$this->tableName.'.duedate)  as datediff,
                    '.$this->tableName.'.aptype, 
                    '.$this->tableType.'.name as aptypename,
                    '.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableName.'.refcode,
                    '.$this->tableName.'.refcode2,
                    '.$this->tableName.'.refinvoicecode,
                    '.$this->tableName.'.trdate,
                    '.$this->tableName.'.amount, 
                    '.$this->tableName.'.amountidr, 
                    '.$this->tableName.'.currencykey,
                    '.$this->tableName.'.rate,
                    '.$this->tableName.'.trdesc,
                    '.$this->tableName.'.supplierkey,
                    '.$this->tableSupplier.'.name as suppliername,
                    coalesce(sum(ap_payment.amount),0) as paidamount,
                    '.$this->tableName.'.amount - coalesce(sum(ap_payment.amount + ap_payment.discount),0)   as outstanding
                from 
                    '.$this->tableName.'
                        left join (
                             select '.$this->tablePaymentDetail.'.amount, '.$this->tablePaymentDetail.'.discount,  '.$this->tablePaymentDetail.'.apkey
                    		 from '.$this->tablePaymentHeader.',  '.$this->tablePaymentDetail.'  
                    		 where 
                                '.$this->tablePaymentHeader.'.pkey =  '.$this->tablePaymentDetail.'.refkey and
                                '.$this->tablePaymentHeader.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].') and 
                                '.$this->tablePaymentHeader.'.trdate <= '.$datePeriod.'
                        ) ap_payment on  '.$this->tableName.'.pkey = ap_payment.apkey,
                    '.$this->tableType.',
                    '.$this->tableWarehouse.',
                    '.$this->tableSupplier.'  
                where
                    '.$this->tableName.'.supplierkey =  '.$this->tableSupplier.'.pkey and
                    '.$this->tableName.'.aptype =  '.$this->tableType.'.pkey and
                    '.$this->tableName.'.warehousekey =  '.$this->tableWarehouse.'.pkey and
                    '.$this->tableName.'.trdate <= '.$datePeriod.' and
                    '.$this->tableName.'.statuskey <> '. TRANSACTION_STATUS['batal'];
          
		
        $sql .=  $this->getWarehouseCriteria() ;
		
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        $sql .= ' group by '.$this->tableName.'.code';
		
        // nanti perlu diremove manual, transaksi yg dibawah nol koma 
        //$sql .= ' having ( outstanding > '.$this->rounding.' or outstanding <  '. ($this->rounding * -1).' ) '; // CN sepertinya outstandingnya minus, jd gk bisa pake outstanding > 0
        $sql .= ' having ( outstanding <> 0 ) '; // CN sepertinya outstandingnya minus, jd gk bisa pake outstanding > 0
    
        
        if (!empty($order))  
            $sql .=  ' ' .$order; 
         
//        $this->setLog($sql,true);
        
         $rsAP = $this->oDbCon->doQuery($sql);
        
         // hilangin semua AP yg dibawah rounding
         // utk model baru harusya sudah gk perlu, karena semua outstanding sudah di nol kan diawal
         $total = count($rsAP);
         for($i=0;$i<$total;$i++){ 
             $balanceRounding = ($rsAP[$i]['currencykey'] == CURRENCY['idr']) ? ARAP_BALANCE_ROUNDING['idr'] : ARAP_BALANCE_ROUNDING['currency'];
             
             if($rsAP[$i]['outstanding'] < $balanceRounding && $rsAP[$i]['outstanding'] >  $balanceRounding * -1)
                 unset($rsAP[$i]);
         }
        
        $rsAP = array_values($rsAP);
        
         return $rsAP;
        
    }
	
	function getSupplierAPCard($arrSupplierKey = array(),$criteria=''){
		 
		$supplierCriteria = '';
		if(!empty($arrSupplierKey)){ 
			if(!is_array($arrSupplierKey))
				$arrSupplierKey = array($arrSupplierKey);
			
			$supplierCriteria = ' and '.$this->tableName.'.supplierkey in('.$this->oDbCon->paramString($arrSupplierKey,',').')'; 
		}
		
		$sql = '
			select '.$this->tableName.'.*,'.$this->tableCurrency.'.name as currencyname  from (
				select
						'.$this->tableName.'.pkey,
						'.$this->tableName.'.code,
						'.$this->tableName.'.trdate, 
						'.$this->tableName.'.refcode,
						'.$this->tableName.'.amount, 
						'.$this->tableName.'.currencykey,
						'.$this->tableName.'.createdon ,
						'.$this->tableName.'.supplierkey,
						1 as tabletype
				from 
						'.$this->tableName.' '.$this->tableName.'
				where '.$this->tableName.'.statuskey in (1,2,3)
						'.$supplierCriteria.'
				
				union all
				
				select 
					'.$this->tableName.'.pkey,
					'.$this->tableName.'.code as code, 
					'.$this->tableName.'.trdate, 
					'.$this->tableName.'.refcode, 
					'.$this->tableName.'.totalpaid * -1,
					'.$this->tableName.'.currencykey,
					'.$this->tableName.'.createdon,
					'.$this->tableName.'.supplierkey ,
					2 as tabletype
				from 
					'.$this->tablePaymentHeader.' '.$this->tableName.'
				where 
					'.$this->tableName.'.statuskey in (2,3)
					'.$supplierCriteria.'
			) '.$this->tableName.' left join '.$this->tableCurrency.'  on '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey 
			
			where 1=1
		';
		
		$sql .=  $this->getWarehouseCriteria() ;
        
		$sql .=  ' ' .$criteria; 
		$sql .= 'order by trdate asc, createdon asc';
		
		//$this->setLog($sql,true);
		return $this->oDbCon->doQuery($sql);
	}
    
    function generateCashflowReport($criteria=''){
		$rsTrans = array(); 
		$arrSQL = array(); 
					
		$sql = 'SELECT  
					warehousekey,
					currencykey,  
					Year(trdate) as tryear,
					month(trdate) as trmonth,
					CONCAT(MONTHNAME(trdate), \' \' ,YEAR(trdate)) AS trmonthyear,
					CONCAT(YEAR(trdate),\'-\',MONTH(trdate)) AS timeindex,
					SUM(amountidr) AS totalidr, 
					'.$this->tableCurrency.'.name as currencyname
				FROM '.$this->tableName.' 
					 left join ' . $this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey 
				where 
					'.$this->tableName.'.statuskey <> 4 ';
		
		if (!empty($criteria)) $sql .=  ' ' .$criteria;   
		 
		$sql .= ' GROUP BY warehousekey,currencykey,timeindex'; 
		$sql .= ' ORDER BY trdate asc';
         
       return $this->oDbCon->doQuery($sql);
		 
    }	
    
    function getDetailForAPI($arrKey, $arrIndex=array()){ 
        $apPayment = new APPayment();
        $rsDetailsCol = array();
        
        $rs = $this->searchDataRow(array($this->tableName.'.pkey'),
                                   ' and '.$this->tableName.'.pkey in (' .$this->oDbCon->paramString($arrKey,',').')' 
                                  );
        
        $rsDetails = $apPayment->getDetailPaymentCollections($rs, 'apkey'); 
        $rsDetailsCol['payment_detail'] = $rsDetails;
             
        return $rsDetailsCol;
    }


//    function getJobOrderKey($arrKey){
//        // AP bisa terbentuk dr beberapa modul
//        // AP dari purchase item perlu masuk jg kah disini ?
//        
//        // puchase emkl, dan TMS jg bisa berbeda struktur tablenya
//        // sementara purchase EMKL, informasi job di header
//        
//        if (!is_array($arrKey)) $arrKey = array($arrKey);
//         
//        // group per typekey
//        $rsAPCol = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.reftabletype',$this->tableName.'.refkey'),
//                                    ' and '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrKey,',').')');
//        
//        $rsAPCol =  $this->reindexDetailCollections($rsAPCol,'reftabletype');
//         
//        $arrJOKey = array();
//        
//        foreach($rsAPCol as $typeKey=>$rowTableType){
//            
//            $transObj = $this->getObjMapping( '', $typeKey); 
//            
//            $this->setLog($transObj->tableName,true);
//            
//            switch($transObj->tableName){
//                    
//                    default :   // nanti perlu dibedakan kalo PO nya header detail, JO nya di detail
//                                $arrPOKey = array_column($rowTableType,'refkey');
//                                $sql = 'select 
//                                            '.$transObj->tableName.'.refkey,
//                                            '.$transObj->tableName.'.reftabletype 
//                                        from ' . $transObj->tableName.' 
//                                        where '.$transObj->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPOKey,',').')';
//                     
//                                $this->setLog($sql,true);
//                                $rs =  $this->oDbCon->doQuery($sql);
//                    
//            }
//            
//            $arrJOKey = array_merge($arrJOKey,$rs);
//        }
//         
//        return $arrJOKey;
//        
//    }
    
       /* function getInvoiceType($tableName){ 
        
        $purchaseOrder = new PurchaseOrder();
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();  
        $apPayment = new APPayment();
        $salesOrderCarService = new SalesOrderCarService();
        
        $arr = array();
        
        switch ($tableName){ 
            case $truckingServiceWorkOrder->tableName : $arr = array('key' => 2,  
                                                                   'obj' => $truckingServiceWorkOrder 
                                                                  );
                                                      break; 

  	        case $apPayment->tableName : $arr = array('key' => 3,  
                                                       'obj' => $apPayment 
                                                      );
                                                break; 
  	        case $salesOrderCarService->tableName : $arr = array('key' => 4,  
                                                                 'obj' => $salesOrderCarService 
                                                      );
                                                break; 
                
            default : $arr = array('key' => 1,  
                           'obj' => $purchaseOrder 
                          );
        }
        
        return $arr;
        
    }*/
     

    function generateOutstandingAPDashboardSummary($criteria='',$order='', $groupBy = ''){
        
	   $sql =  '
			SELECT 
					GROUP_CONCAT('.$this->tableName.'.pkey) as pkey,
					coalesce(sum(outstanding),0) as totaloutstanding,
					coalesce(sum(amount),0) as totalamount,
                   '.$this->tableSupplier.'.name as suppliername,
                   '.$this->tableSupplier.'.categorykey,
                   '.$this->tableSupplierCategory.'.name as categoryname
			FROM 
                '.$this->tableStatus.',  
                '.$this->tableName.',
                '.$this->tableSupplier.'
                left join '.$this->tableSupplierCategory.' on '.$this->tableSupplier.'.categorykey = '.$this->tableSupplierCategory.'.pkey,
				'.$this->tableWarehouse.'
			WHERE     
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 		'; 
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria;  
         
		if(!empty($groupBy))
		    $sql .= $groupBy;
        
        if (!empty($order))  
            $sql .=  ' ' .$order; 
     
        //$this->setLog($sql, true);
        $rs = $this->oDbCon->doQuery($sql);
        //$this->setLog($rs, true);
       
        $rsData = $this->reindexDetailCollections($rs,'categorykey');
        return $rsData;
		 
    } 
}
 	
?>
