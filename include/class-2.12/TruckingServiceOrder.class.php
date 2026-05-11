<?php
  
class TruckingServiceOrder extends BaseClass{ 
   
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'trucking_service_order_header';
		$this->tableNameDetail = 'trucking_service_order_detail';
        $this->tableNameDetailStatus = 'trucking_service_order_detail_status';
		$this->tableSellingCost = 'trucking_service_order_selling_cost';
        $this->tableHeaderCost = 'trucking_service_order_header_cost';
		$this->tableCategory = 'trucking_service_order_category'; 
        $this->tableCargoType = 'cargo_type'; 
		$this->tableCost = 'trucking_service_order_cost';
		$this->tableContainerDetail = 'trucking_service_order_container';
        $this->tableWorkOrder = 'trucking_service_work_order';
        $this->tableWorkOrderCargo = 'trucking_service_work_order_cargo';
        $this->tableWorkOrderCarDetail = 'trucking_service_work_order_car';
        $this->tableWorkOrderCost = 'trucking_service_work_order_cost';  
        $this->tableWarehouse = 'warehouse';
		$this->tableCustomer = 'customer';
		$this->tableConsignee = 'consignee';
		$this->tableItem = 'item';
		$this->tableServiceCategory = 'service_category';
		$this->tableLocation = 'location'; 
        $this->tableDepot = 'depot'; 
        $this->tableTerminal = 'terminal'; 
		$this->tableEmployee = 'employee'; 
		$this->tableSupplier = 'supplier'; 
		$this->tableStatus = 'trucking_service_order_status'; 
		$this->tableDetailStatus = 'trucking_service_order_detail_status';   
        $this->tableContact = 'contact_person';	  
		$this->tableHistory = 'history'; 
		$this->tableFile = 'trucking_service_order_file';  
        $this->tableTruckingCostCashOut = 'trucking_cost_cash_out_header';  
        $this->tableAP = 'ap';   
		$this->tableAPEmployee = 'ap_employee_commission';
        $this->tableTruckingServiceOrderInvoiceHeader = 'trucking_service_order_invoice_header';    
        $this->tableTruckingServiceOrderInvoiceDetail = 'trucking_service_order_invoice_detail';    
        $this->tablePartialInvoice = 'trucking_service_order_header_partial_invoice';
        $this->tableRef = 'table_ref';
        $this->tableCar = 'car';
        $this->tableCarCategory = 'car_category'; 
        $this->tableCOA = 'chart_of_account'; 
            
        $this->uploadFileFolder = 'trucking-service-order/';  
        $this->isTransaction = true;
       
        $this->allowedStatusForEdit = array(1,2,3,4);
       
		$this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail, $this->tableHeaderCost ,$this->tableSellingCost );
        // $this->tableCost gk boleh dimasukin karena br terbentuk pas konfirmasi
       
		$this->securityObject = 'TruckingServiceOrder';  
        $this->sellingPriceSecurityObject = 'SellingPrice';
        $this->overwriteContractSecurityObject = 'overwriteContract';
        $this->useStorage = $this->useStorage('S3');		  
       
        $security = new Security();
	    $sellingPriceAllowed = $security->isAdminLogin($this->sellingPriceSecurityObject, 10);
	    
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['numberkey'] = array('numberkey');
        $this->arrDataDetail['itemkey'] = array('hidItemKey',array('mandatory'=>true));
        $this->arrDataDetail['qtyinbaseunit'] = array('qty', array('datatype'=>'number','mandatory'=>true));       
        $this->arrDataDetail['trdate'] = array('trShipmentDate','datetime');
        $this->arrDataDetail['priceinunit'] = array('price','number');
        $this->arrDataDetail['contractpriceinunit'] = array('contractPrice','number'); 
        $this->arrDataDetail['total'] = array('totalDetails','number');
        $this->arrDataDetail['trdesc'] = array('detailNotes');
        $this->arrDataDetail['isgroup'] = array('chkIsGroup');
        $this->arrDataDetail['requestid'] = array('detailRequestId');
        $this->arrDataDetail['carcategorykey'] = array('hidCarCategoryKey');
       
        $this->arrHeaderCost = array(); 
        $this->arrHeaderCost['pkey'] = array('hidAdditionalKey');
        $this->arrHeaderCost['refkey'] = array('pkey', 'ref');
        $this->arrHeaderCost['costkey'] = array('hidItemKeyHeaderCost',array('mandatory'=>true));
        $this->arrHeaderCost['qty'] = array('qtyHeaderCost',array('datatype'=>'number','mandatory'=>true));
        $this->arrHeaderCost['requestamount'] = array('requestPriceHeaderCost','number'); 
        $this->arrHeaderCost['amount'] = array('priceHeaderCost','number'); 
        $this->arrHeaderCost['subtotal'] = array('subtotalHeaderCost','number');        
        $this->arrHeaderCost['employeekey'] = array('hidDetailEmployeeKey');
        $this->arrHeaderCost['description'] = array('detailDesc');
        $this->arrHeaderCost['requestid'] = array('headerCostRequestId');

        
        $this->arrSellingCost = array(); 
        $this->arrSellingCost['pkey'] = array('hidDetailCostKey');
        $this->arrSellingCost['refkey'] = array('pkey', 'ref');
        $this->arrSellingCost['costkey'] = array('hidItemKeyCost',array('mandatory'=>true));
        $this->arrSellingCost['qty'] = array('qtyCost',array('datatype'=>'number','mandatory'=>true));
        $this->arrSellingCost['price'] = array('priceCost','number');
        $this->arrSellingCost['subtotal'] = array('subtotalCost','number'); 
        $this->arrSellingCost['requestid'] = array('sellingCostRequestId');
        $this->arrSellingCost['store'] = array('store');
        $this->arrSellingCost['notes'] = array('sellingDesc');
        $this->arrSellingCost['carkey'] = array('hidCarKeyCost');
        $this->arrSellingCost['workorderkey'] = array('hidWorkOrderKeyCost');  
        
        $this->arrContactPerson = array(); 
        $this->arrContactPerson['pkey'] = array('hidContactPersonDetailKey'); 
        $this->arrContactPerson['refkey'] = array('pkey', 'ref');
        $this->arrContactPerson['reftable'] = array('reftable',array('mandatory'=>true));
        $this->arrContactPerson['name'] = array('cpName',array('mandatory'=>true));
        $this->arrContactPerson['position'] = array('cpPosition');
        $this->arrContactPerson['phone'] = array('cpPhone'); 
       
       
        $this->arrContainerDetail = array(); 
        $this->arrContainerDetail['pkey'] = array('hidContainerDetailKey'); 
        $this->arrContainerDetail['refkey'] = array('pkey', 'ref');
        $this->arrContainerDetail['container'] = array('containerDetail',array('mandatory'=>true));
        $this->arrContainerDetail['seal'] = array('sealDetail');
        $this->arrContainerDetail['refspkkey'] = array('hidRefSPKKey');
        $this->arrContainerDetail['refspkdetailkey'] = array('hidRefSPKDetailKey');
        $this->arrContainerDetail['requestid'] = array('containerDetailRequestId');
        
        $this->arrDetails = array();
        array_push($this->arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($this->arrDetails, array('dataset' => $this->arrHeaderCost, 'tableName' => $this->tableHeaderCost));
        array_push($this->arrDetails, array('dataset' => $this->arrSellingCost, 'tableName' => $this->tableSellingCost));
        array_push($this->arrDetails, array('dataset' => $this->arrContactPerson, 'tableName' => $this->tableContact));
        array_push($this->arrDetails, array('dataset' => $this->arrContainerDetail, 'tableName' => $this->tableContainerDetail));
        
       if($this->useStorage){ 
            $this->arrDataFileDetail = array();  
            $this->arrDataFileDetail['pkey'] = array('hidDetailFileKey');
            $this->arrDataFileDetail['refkey'] = array('pkey','ref');
            $this->arrDataFileDetail['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));
 
            array_push($this->arrDetails, array('dataset' => $this->arrDataFileDetail, 'tableName' => $this->tableFile));
       }else{ 
           array_push($this->arrDetails, array(
                'dataset' => $this->arrDataFile, 'tableName' => $this->tableFile,
                'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
                'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'
            ));
       }

		
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey',array('dataDetail' => $this->arrDetails)); 
	    $this->arrData['code'] = array('code');
	    $this->arrData['codectr'] = array('codectr');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['contractkey'] = array('hidContractKey');
        $this->arrData['categorykey'] = array('hidCategoryKey');
        $this->arrData['cargotypekey'] = array('hidCargoType');
        $this->arrData['donumber'] = array('doNumber');
        $this->arrData['shipmentnumber'] = array('shipmentNumber');
        $this->arrData['terminalkey'] = array('hidTerminalKey');
        $this->arrData['depotkey'] = array('hidDepotKey');
        $this->arrData['consigneekey'] = array('hidConsigneeKey');
        $this->arrData['consigneewarehousename'] = array('warehouseName');
        $this->arrData['consigneelocationkey'] = array('hidLocationKey'); 
        $this->arrData['consigneecontactperson'] = array('contactPerson');
        $this->arrData['consigneeaddress'] = array('address');
        $this->arrData['stuffinglocationkey'] = array('hidStuffingLocationKey');
        $this->arrData['stuffingaddress'] = array('stuffingAddress'); 
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal','number');
        $this->arrData['grandtotal'] = array('grandtotal','number');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['tarifflastmodifiedon'] = array('hidContractLastModifiedOn');
        $this->arrData['plannerkey'] = array('hidPlannerKey');
        $this->arrData['routefrom'] = array('routeFrom');
        $this->arrData['routeto'] = array('routeTo');
        $this->arrData['totalheadercost'] = array('totalHeaderCost','number');
        $this->arrData['totalsellingcost'] = array('totalSellingCost','number');
        $this->arrData['saleskey'] = array('hidSalesKey');
        $this->arrData['poreference'] = array('poReference');
        $this->arrData['vesselkey'] = array('hidVesselKey');
        $this->arrData['vesselnumber'] = array('vesselNumber');     
        $this->arrData['autoinvoice'] = array('autoInvoice');     
        $this->arrData['requestid'] = array('requestid');          
        $this->arrData['isapi'] = array('_mnv-api');            
        $this->arrData['aju'] = array('aju');                   
        $this->arrData['mbl'] = array('mbl');      
        $this->arrData['caseid'] = array('caseID'); 
        $this->arrData['stuffinglocationfromkey'] = array('hidStuffingLocationFromKey');     
        $this->arrData['goodsdescription'] = array('goodsDescription'); 
        $this->arrData['shippername'] = array('shipperName');                                                          
     
         
        $this->arrLinkedTable = array(); 
        $defaultFieldName = 'refkey';
        array_push($this->arrLinkedTable, array('table'=>'trucking_service_work_order','field'=>$defaultFieldName));  
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'consignee','title' => 'consignee','dbfield' => 'consigneename','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'si','title' => 'si','dbfield' => 'donumber','default'=>true, 'width' => 130));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bookingnumber','title' => 'bookingNumber','dbfield' => 'shipmentnumber', 'width' => 130));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 120));
        
	    if($sellingPriceAllowed)
	    	array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true, 'width' => 80,'align' =>'right','format' => 'number'));
        
	    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'cargoType','title' => 'type','dbfield' => 'cargotype', 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'totalcost', 'title' => 'totalCost','dbfield' => 'totalcost', 'width' => 80,'align' =>'right','format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'poreference','title' => 'poReference','dbfield' => 'poreference',  'width' => 100 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
    
       
        $this->arrSearchColumn = array(); 
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate')); 
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer. '.name')); 
        array_push($this->arrSearchColumn, array('Consignee', $this->tableConsignee. '.name')); 
        array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse. '.name')); 
        array_push($this->arrSearchColumn, array('No. Booking', $this->tableName. '.shipmentnumber')); 
        array_push($this->arrSearchColumn, array('DO Pelanggan', $this->tableName. '.donumber')); 
        array_push($this->arrSearchColumn, array('Referensi PO', $this->tableName. '.poreference')); 
        array_push($this->arrSearchColumn, array('Kategori', $this->tableCategory. '.name')); 
        array_push($this->arrSearchColumn, array('Jenis', $this->tableCargoType. '.name')); 
        array_push($this->arrSearchColumn, array('Total', $this->tableName. '.grandtotal'));
        array_push($this->arrSearchColumn, array('Catatan', $this->tableName. '.trdesc'));
        array_push($this->arrSearchColumn, array('Alamat Pengiriman', $this->tableName. '.stuffingaddress'));
        array_push($this->arrSearchColumn, array('Lokasi Pengiriman', $this->tableLocation. '.name'));
        array_push($this->arrSearchColumn, array('No. Container', $this->tableName. '.containernumber'));
       
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/truckingServiceOrder'));
        array_push($this->printMenu,array('code' => 'printComplete', 'name' => $this->lang['printSummary'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderComplete'));
        array_push($this->printMenu,array('code' => 'printCashOut', 'name' => $this->lang['printCashOutRequest'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderCostCashOut'));
              
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
          
        $this->includeClassDependencies(array( 
              'AP.class.php',
              'Consignee.class.php',
              'CostRate.class.php',
              'Customer.class.php',
              'Downpayment.class.php',
              'CustomerDownpayment.class.php',
              'Depot.class.php',
              'Item.class.php',
              'Location.class.php',
              'Service.class.php',
              'Supplier.class.php',
              'Terminal.class.php',
              'TruckingCostCashOut.class.php',
              'TruckingSellingRate.class.php',
              'TruckingServiceOrderCategory.class.php',
              'TruckingServiceOrderInvoice.class.php',
              'TruckingServiceWorkOrder.class.php',
              'Vessel.class.php',
              'Warehouse.class.php',
              'PaymentMethod.class.php',
              'TermOfPayment.class.php',
              'COALink.class.php' ,
              'Car.class.php',
              'JobProgress.class.php' 
        ));
        $this->overwriteConfig();
   
   }
   
 
   function getQuery(){
	   
	   $sql = '
			SELECT '.$this->tableName.'.* ,
               ('.$this->tableName.'.totalheadercost + '.$this->tableName.'.totalworkordercost + '.$this->tableName.'.totalrefund) as totalcost,
               '.$this->tableName.'.totalsharedprofit,
               ('.$this->tableName.'.grandtotal - ('.$this->tableName.'.totalheadercost + '.$this->tableName.'.totalworkordercost + '.$this->tableName.'.totalsharedprofit + '.$this->tableName.'.totalrefund)) as grossprofit,
			   '.$this->tableCustomer.'.name as customername, 
			   '.$this->tableCustomer.'.code as customercode, 
			   '.$this->tableStatus.'.status as statusname ,
			   '.$this->tableStatus.'.textcolor as statuscolor ,
			   '.$this->tableEmployee.'.code as salescode,
			   '.$this->tableEmployee.'.name as salesname,
               '.$this->tableCategory.'.name as categoryname , 
			   '.$this->tableCargoType.'.name as cargotype  ,  
			   '.$this->tableDepot.'.code as depotcode ,
			   '.$this->tableDepot.'.name as depotname ,
			   '.$this->tableTerminal.'.code as terminalcode ,
			   '.$this->tableTerminal.'.name as terminalname ,
               '.$this->tableConsignee .'.name as consigneename,
               '.$this->tableConsignee .'.code as consigneecode,
               '.$this->tableConsignee .'.warehousename as consigneewarehousename, 
               '.$this->tableWarehouse .'.name as warehousename, 
               '.$this->tableWarehouse .'.code as warehousecode, 
               '.$this->tableLocation .'.name as locationname,
               stuffinglocationfrom.name as stuffinglocationfromname
			FROM 
                '.$this->tableStatus.', 
                '.$this->tableCustomer.',    
                '.$this->tableWarehouse.',  
                '.$this->tableCategory.', 
                '.$this->tableCargoType .', 
                '.$this->tableName.' 
                    left join '.$this->tableConsignee.' on  '.$this->tableName.'.consigneekey = '.$this->tableConsignee.'.pkey    
                    left join '.$this->tableEmployee.' on  '.$this->tableName.'.saleskey = '.$this->tableEmployee.'.pkey    
                    left join '.$this->tableLocation.' on '.$this->tableName . '.stuffinglocationkey = '.$this->tableLocation.'.pkey 
                    left join '.$this->tableLocation.' stuffinglocationfrom on '.$this->tableName . '.stuffinglocationfromkey = stuffinglocationfrom.pkey
                    left join '.$this->tableDepot.' on '.$this->tableName . '.depotkey = '.$this->tableDepot.'.pkey
                    left join '.$this->tableTerminal.' on '.$this->tableName . '.terminalkey = '.$this->tableTerminal.'.pkey 
			WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
					 '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and   
                     '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey and
                     '.$this->tableName.'.cargotypekey = '.$this->tableCargoType.'.pkey and
					 '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
 		' .$this->criteria ; 
        
        $sql .=  $this->getWarehouseCriteria();
        return $sql;
    }   
	
   function updateTotalSharedProfit($sokey){
        $totalSharedProfit = 0;
        $rsCommission = $this->getPartnersVehicleInformation($sokey);
       
        for($i=0;$i<count($rsCommission);$i++) 
            $totalSharedProfit += $rsCommission[$i]['amount'];
        
        $sql = ' update '.$this->tableName.' set totalsharedprofit = '.$this->oDbCon->paramString($totalSharedProfit).'
                 where '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($sokey);

        $this->oDbCon->execute($sql);
    }    
    
  function generateCostReport($criteria='',$order='', $arrAdditionalCost = array()){
	   // gk bisa join langsung dengan Job Order atau SPK, karean tergantung tabletype
        
        $arrSQL = array();     
         
         // JO COST
        $rsCashOutKey =  $this->getTableKeyAndObj($this->tableName);   
        $sql =  '
			SELECT '.$this->tableName.'.pkey, 
                   '.$this->tableName.'.code, 
                   '.$this->tableRef.'.code as refcode, 
                   '.$this->tableRef.'.donumber, 
                   '.$this->tableName.'.trdate,  
                   ('.$this->tableCost.'.qty *  '.$this->tableCost.'.requestamount)  as requestamount,  
                   ('.$this->tableCost.'.qty *  '.$this->tableCost.'.amount)  as amount,  
                   ('.$this->tableCost.'.qty *  ( '.$this->tableCost.'.requestamount  -  '.$this->tableCost.'.amount) )  as balance,  
                   '.$this->tableItem.'.pkey as costkey, 
                   '.$this->tableItem.'.name as costname, 
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableCategory.'.name as categoryname,
                   '.$this->tableWarehouse.'.name as warehousename , 
                   '.$this->tableEmployee.'.name as recipientname,
                   0 as isoutsource,
                   '.$this->tableTruckingCostCashOut.'.code as cashoutcode,
                   '.$this->tableCustomer.'.name as customername,
                   '.$this->tableConsignee.'.name as consigneename,
                   CONCAT_WS(\'\','.$this->tableRef.'.routefrom,\'-\','.$this->tableRef.'.routeto)  as route,
                   '.$this->tableLocation.'.name as locationname,
                   \'\' as carregistrationnumber
			FROM 
                '.$this->tableName.', 
                '.$this->tableName.' as  '.$this->tableRef.' 
                    left join '.$this->tableCustomer.' on '.$this->tableRef.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tableConsignee.' on '.$this->tableRef.'.consigneekey = '.$this->tableConsignee.'.pkey
                    left join '.$this->tableLocation.' on '.$this->tableRef.'.stuffinglocationkey = '.$this->tableLocation.'.pkey
                    left join '.$this->tableCategory.' on '.$this->tableRef.'.categorykey = '.$this->tableCategory.'.pkey, 
                '.$this->tableHeaderCost.' as  '.$this->tableCost.' 
                    left join  '.$this->tableTruckingCostCashOut.' on 
                            '.$this->tableCost.'.refcashoutkey = '.$this->tableTruckingCostCashOut.'.pkey and
                            '.$this->tableTruckingCostCashOut.'.reftabletype = ' . $this->oDbCon->paramString($rsCashOutKey['key']).' 
                    left join  '.$this->tableEmployee.' on '.$this->tableTruckingCostCashOut.'.employeekey = '.$this->tableEmployee.'.pkey,
                '.$this->tableItem.',  
                '.$this->tableStatus.',   
                '.$this->tableWarehouse.'  
			WHERE     
                '.$this->tableName.'.pkey = '.$this->tableCost.'.refkey and 
                '.$this->tableName.'.pkey = '.$this->tableRef.'.pkey and 
                '.$this->tableCost.'.costkey = '.$this->tableItem.'.pkey and  
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                '.$this->tableName.'.statuskey not in(1,7)
 		'; 
       
        
        if (!empty($criteria)) $sql .=  ' ' .$criteria;  
        array_push($arrSQL,$sql);
      
        
      // SPK COST    
       $rsCashOutKey =  $this->getTableKeyAndObj($this->tableWorkOrder);  
	   $sql =  '
			SELECT '.$this->tableName.'.pkey, 
                   '.$this->tableName.'.code, 
                   '.$this->tableRef.'.code as refcode,
                   '.$this->tableRef.'.donumber,  
                   '.$this->tableName.'.trdate,   
                   ('.$this->tableCost.'.qty * '.$this->tableCost.'.requestamount) as requestamount,  
                   ('.$this->tableCost.'.qty * '.$this->tableCost.'.amount) as amount,  
                   ('.$this->tableCost.'.qty * '.$this->tableCost.'.requestamount) - ('.$this->tableCost.'.qty * '.$this->tableCost.'.amount) as balance,  
                   '.$this->tableItem.'.pkey as costkey, 
                   '.$this->tableItem.'.name as costname, 
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableCategory.'.name as categoryname,
                   '.$this->tableWarehouse.'.name as warehousename , 
                   CONCAT_WS(\'\','.$this->tableEmployee.'.name,'.$this->tableSupplier.'.name)  as recipientname,
                   '.$this->tableName.'.isoutsource,
                   CONCAT_WS(\'\','.$this->tableTruckingCostCashOut.'.code,'.$this->tableAP.'.code)  as cashoutcode,
                   '.$this->tableCustomer.'.name as customername,
                   '.$this->tableConsignee.'.name as consigneename,
                   CONCAT_WS(\'\','.$this->tableRef.'.routefrom,\'-\','.$this->tableRef.'.routeto) as route,
                   '.$this->tableLocation.'.name as locationname,
                   '.$this->tableCar.'.policenumber as carregistrationnumber
			FROM 
                '.$this->tableWorkOrder.' as '.$this->tableName.'
                    left join '.$this->tableCar.'  on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey,
                '.$this->tableStatus.',  
                '.$this->tableItem.', 
                '.$this->tableWorkOrderCost.' as  '.$this->tableCost.'
                    left join '.$this->tableSupplier.' on '.$this->tableCost.'.supplierkey = '.$this->tableSupplier.'.pkey
                    left join '.$this->tableAP.' on '.$this->tableCost.'.refcashoutkey = '.$this->tableAP.'.pkey and  '.$this->tableCost.'.supplierkey <> 0
                    left join '.$this->tableTruckingCostCashOut.' on 
                            '.$this->tableCost.'.refcashoutkey = '.$this->tableTruckingCostCashOut.'.pkey and  '.$this->tableCost.'.employeekey <> 0 and
                            '.$this->tableTruckingCostCashOut.'.reftabletype = ' . $this->oDbCon->paramString($rsCashOutKey['key']).' 
                    left join '.$this->tableEmployee.' on '.$this->tableTruckingCostCashOut.'.employeekey = '.$this->tableEmployee.'.pkey,
                '.$this->tableName.' as  '.$this->tableRef.'
                    left join '.$this->tableCustomer.' on '.$this->tableRef.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tableConsignee.' on '.$this->tableRef.'.consigneekey = '.$this->tableConsignee.'.pkey
                    left join '.$this->tableLocation.' on '.$this->tableRef.'.stuffinglocationkey = '.$this->tableLocation.'.pkey
                    left join '.$this->tableCategory.' on '.$this->tableRef.'.categorykey = '.$this->tableCategory.'.pkey,
                '.$this->tableWarehouse.'
			WHERE     
                '.$this->tableName.'.refkey = '.$this->tableRef.'.pkey and 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableCost.'.refkey = '.$this->tableName.'.pkey and 
                '.$this->tableCost.'.costkey = '.$this->tableItem.'.pkey and  
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and 
                '.$this->tableName.'.statuskey not in(1,4)
 		'; 
       
        
        if (!empty($criteria)) $sql .=  ' ' .$criteria;   
        array_push($arrSQL,$sql);
        
        // Remove "and item.pkey in ( ... )"
        $criteria = preg_replace('/\s*and\s+item\.pkey\s+in\s*\([^)]+\)\s*/i', ' ', $criteria);

      // kalo gk ad criteira cost, ditandakan dengan array(0)
      // maka semua masuk
      if ( empty($arrAdditionalCost) || in_array(-1,$arrAdditionalCost)) {
          // SPK OUTSOURCING COST
        $sql =  '
			SELECT '.$this->tableName.'.pkey, 
                   '.$this->tableName.'.code, 
                   '.$this->tableRef.'.code as refcode, 
                   '.$this->tableRef.'.donumber, 
                   '.$this->tableName.'.trdate,  
                   '.$this->tableName.'.outsourcecost as requestamount,  
                   '.$this->tableName.'.outsourcecost as amount,  
                   0 as balance,  
                   \'-1\' as costkey, 
                   \''.$this->lang['truckingFee'].'\' as costname, 
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableCategory.'.name as categoryname,
                   '.$this->tableWarehouse.'.name as warehousename , 
                   '.$this->tableSupplier.'.name  as recipientname ,
                   '.$this->tableName.'.isoutsource,
                   '.$this->tableAP.'.code as cashoutcode,
                   '.$this->tableCustomer.'.name as customername,
                   '.$this->tableConsignee.'.name as consigneename,
                   CONCAT_WS(\'\','.$this->tableRef.'.routefrom,\'-\','.$this->tableRef.'.routeto)  as route,
                   '.$this->tableLocation.'.name as locationname,
                   '.$this->tableCar.'.policenumber as carregistrationnumber
			FROM 
                '.$this->tableWorkOrder.' as '.$this->tableName.' 
                    left join  '.$this->tableAP.' on   '.$this->tableName.'.refcashoutkey = '.$this->tableAP.'.pkey
                    left join '.$this->tableCar.'  on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey,
                '.$this->tableName.' as '.$this->tableRef.'
                    left join '.$this->tableCustomer.' on '.$this->tableRef.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tableConsignee.' on '.$this->tableRef.'.consigneekey = '.$this->tableConsignee.'.pkey
                    left join '.$this->tableLocation.' on '.$this->tableRef.'.stuffinglocationkey = '.$this->tableLocation.'.pkey
                    left join '.$this->tableCategory.' on '.$this->tableRef.'.categorykey = '.$this->tableCategory.'.pkey,
                '.$this->tableStatus.',  
                '.$this->tableSupplier.',
                '.$this->tableWarehouse.'
			WHERE     
                '.$this->tableName.'.refkey = '.$this->tableRef.'.pkey and 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and   
                '.$this->tableName.'.isoutsource = 1 and
                '.$this->tableName.'.statuskey not in(1,4)
 		'; 
       
        if (!empty($criteria)) $sql .=  ' ' .$criteria;   
        array_push($arrSQL,$sql);
      }
        
       if ( empty($arrAdditionalCost) || in_array(-2,$arrAdditionalCost)) {
              // KOMISI DRIVER DAN  CO DRIVER
                $sql =  '
                    SELECT '.$this->tableName.'.pkey, 
                           '.$this->tableName.'.code, 
                           '.$this->tableRef.'.code as refcode, 
                           '.$this->tableRef.'.donumber, 
                           '.$this->tableName.'.trdate,  
                           '.$this->tableName.'.drivercommission as requestamount,  
                           '.$this->tableName.'.drivercommission as amount,  
                           0 as balance,  
                           \'-2\' as costkey, 
                           \''.$this->lang['driverCommission'].'\' as costname,  
                           '.$this->tableStatus.'.status as statusname , 
                           '.$this->tableCategory.'.name as categoryname,
                           '.$this->tableWarehouse.'.name as warehousename , 
                           '.$this->tableEmployee.'.name  as recipientname ,
                           '.$this->tableName.'.isoutsource,
                           '.$this->tableAPEmployee.'.code as cashoutcode,
                           '.$this->tableCustomer.'.name as customername,
                           '.$this->tableConsignee.'.name as consigneename,
                           CONCAT_WS(\'\','.$this->tableRef.'.routefrom,\'-\','.$this->tableRef.'.routeto)  as route,
                           '.$this->tableLocation.'.name as locationname,
                           '.$this->tableCar.'.policenumber as carregistrationnumber
                    FROM 
                        '.$this->tableWorkOrder.' as '.$this->tableName.' 
                            left join  '.$this->tableAPEmployee.' on   '.$this->tableName.'.refcashoutdriverkey = '.$this->tableAPEmployee.'.pkey
                            left join '.$this->tableCar.'  on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey,
                        '.$this->tableName.' as '.$this->tableRef.'
                            left join '.$this->tableCustomer.' on '.$this->tableRef.'.customerkey = '.$this->tableCustomer.'.pkey
                            left join '.$this->tableConsignee.' on '.$this->tableRef.'.consigneekey = '.$this->tableConsignee.'.pkey
                            left join '.$this->tableLocation.' on '.$this->tableRef.'.stuffinglocationkey = '.$this->tableLocation.'.pkey
                            left join '.$this->tableCategory.' on '.$this->tableRef.'.categorykey = '.$this->tableCategory.'.pkey,
                        '.$this->tableStatus.',  
                        '.$this->tableEmployee.',
                        '.$this->tableWarehouse.'  
                    WHERE     
                        '.$this->tableName.'.refkey = '.$this->tableRef.'.pkey and 
                        '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                        '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey and 
                        '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and  
                        '.$this->tableName.'.isoutsource = 0 and
                        '.$this->tableName.'.drivercommission > 0 and
                        '.$this->tableName.'.statuskey not in(1,4)
                '; 

                if (!empty($criteria)) $sql .=  ' ' .$criteria;  
                array_push($arrSQL,$sql);
       }

          if ( empty($arrAdditionalCost) || in_array(-3,$arrAdditionalCost)) {
        // KOMISI CO DRIVER
        $sql =  '
            SELECT '.$this->tableName.'.pkey, 
                   '.$this->tableName.'.code, 
                   '.$this->tableRef.'.code as refcode, 
                   '.$this->tableRef.'.donumber, 
                   '.$this->tableName.'.trdate,  
                   '.$this->tableName.'.codrivercommission as requestamount,  
                   '.$this->tableName.'.codrivercommission as amount,  
                   0 as balance,  
                   \'-3\' as costkey, 
                   \''.$this->lang['codriverCommission'].'\' as costname,   
                   '.$this->tableStatus.'.status as statusname , 
                   '.$this->tableCategory.'.name as categoryname,
                   '.$this->tableWarehouse.'.name as warehousename , 
                   '.$this->tableEmployee.'.name  as recipientname ,
                   '.$this->tableName.'.isoutsource,
                   '.$this->tableAPEmployee.'.code as cashoutcode,
                   '.$this->tableCustomer.'.name as customername,
                   '.$this->tableConsignee.'.name as consigneename,
                   CONCAT_WS(\'\','.$this->tableRef.'.routefrom,\'-\','.$this->tableRef.'.routeto)  as route,
                   '.$this->tableLocation.'.name as locationname,
                   '.$this->tableCar.'.policenumber as carregistrationnumber
            FROM 
                '.$this->tableWorkOrder.' as '.$this->tableName.' 
                    left join  '.$this->tableAPEmployee.' on   '.$this->tableName.'.refcashoutcodriverkey = '.$this->tableAPEmployee.'.pkey
                    left join '.$this->tableCar.'  on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey,
                '.$this->tableName.' as '.$this->tableRef.'
                    left join '.$this->tableCustomer.' on '.$this->tableRef.'.customerkey = '.$this->tableCustomer.'.pkey
                    left join '.$this->tableConsignee.' on '.$this->tableRef.'.consigneekey = '.$this->tableConsignee.'.pkey
                    left join '.$this->tableLocation.' on '.$this->tableRef.'.stuffinglocationkey = '.$this->tableLocation.'.pkey
                    left join '.$this->tableCategory.' on '.$this->tableRef.'.categorykey = '.$this->tableCategory.'.pkey,
                '.$this->tableStatus.',  
                '.$this->tableEmployee.',
                '.$this->tableWarehouse.'  
            WHERE     
                '.$this->tableName.'.refkey = '.$this->tableRef.'.pkey and 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and 
                '.$this->tableName.'.codriverkey = '.$this->tableEmployee.'.pkey and 
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and  
                '.$this->tableName.'.isoutsource = 0 and
                '.$this->tableName.'.codrivercommission > 0 and
                '.$this->tableName.'.statuskey not in(1,4)
        '; 

        if (!empty($criteria)) $sql .=  ' ' .$criteria;  
        array_push($arrSQL,$sql);
      }
       
       
        $sql = implode ( ' UNION ALL ' , $arrSQL);
        
        if (!empty($order))  
            $sql .=  ' ' .$order; 
          
       //$this->setLog($sql,true);
       return $this->oDbCon->doQuery($sql);
		 
    }
  
    
	function reCountSubtotal($arrParam){ 
         
                $truckingSellingRate = new TruckingSellingRate();
                $service = new Service(TRUCKING_SERVICE,1);
         
		
				$subtotal = 0 ;
				$grandtotal = 0;  
        
				$arrItemKey = $arrParam['hidItemKey'];  
				$arrPriceinunit = $arrParam['price'];    
				$qtyInBaseUnit =  $arrParam['qty'] ; 
        
         		for ($i=0;$i<count($arrItemKey);$i++){
					
					if (empty($arrItemKey[$i]))  
						continue; 
                         
                    $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);   
                    $qty = $this->unFormatNumber($qtyInBaseUnit[$i]);   
                    $subtotal += ($qty * $priceInUnit); 
				} 
        	
                $subtotalCost = 0; 
				$arrItemKeyCost = $arrParam['hidItemKeyCost']; 
				$arrPriceCost = $arrParam['priceCost'];    
				$qtyCost =  $arrParam['qtyCost'] ; 
			
		  
                for ($i=0;$i<count($arrItemKeyCost);$i++){
					
					if (empty($arrItemKeyCost[$i]))  
						continue; 
                         
                    $price = $this->unFormatNumber($arrPriceCost[$i]);   
                    $qty = $this->unFormatNumber($qtyCost[$i]);   
                    $subtotalCost += ($qty * $price); 
				}  
				  
                // Header Cost
                $subtotalHeaderCost = 0; 
				$arrItemKeyCost = $arrParam['hidItemKeyHeaderCost'];     
				$qtyCost =  $arrParam['qtyHeaderCost'] ; 
			
                for ($i=0;$i<count($arrItemKeyCost);$i++){
					
					if (empty($arrItemKeyCost[$i]))  
						continue; 
                          
                    $price = $this->getValidHeaderCost($arrParam,$i);
                     
                    $qty = $this->unFormatNumber($qtyCost[$i]);   
                    $subtotalHeaderCost += ($qty * $price); 
				}  
				  
				$grandtotal = $subtotal + $subtotalCost;  
				$balance = $grandtotal; 
				
				$reCountResult = array();  
				$reCountResult['subtotal'] = $subtotal; 
				$reCountResult['grandtotal'] = $grandtotal; 
				$reCountResult['totalHeaderCost'] = $subtotalHeaderCost; 
				$reCountResult['totalSellingCost'] = $subtotalCost; 
				$reCountResult['balance'] = $balance;
        
				return $reCountResult;
				
	}    
  
    function addCashOut($rsHeader,$rsSalesHeaderCost){
        if (empty($rsSalesHeaderCost)) return;
        
        $truckingCostCashOut = new TruckingCostCashOut();
        $warehouse = new Warehouse();
        $item = new Item();
        $coaLink = new COALink();
        
        // kalo pake planner, yg lama bisa masalah gk ?
        $rsCOALink = $coaLink->getCOALink ('cashbankops', $warehouse->tableName, $rsHeader[0]['warehousekey'],0); 
        $coakey = $rsCOALink[0]['coakey'];
        
        $recipientkey = (!empty($rsSalesHeaderCost[0]['employeekey'])) ? $rsSalesHeaderCost[0]['employeekey'] : $rsHeader[0]['plannerkey'];
        
        
        $arr = array();
        $totalCashOut = 0; 
          
        for($i=0;$i<count($rsSalesHeaderCost);$i++){ 
            $rsItem = $item->getDataRowById($rsSalesHeaderCost[$i]['costkey']);
            $arr['hidDetailKey'][$i] = 0;
            $arr['refheadercostkey'][$i] = $rsSalesHeaderCost[$i]['pkey'];
            $arr['hidCostKey'][$i] = $rsSalesHeaderCost[$i]['costkey'];
            $arr['hidCOAKey'][$i] = $coakey;
            $arr['qty'][$i] = $rsSalesHeaderCost[$i]['qty'];
            $arr['costValue'][$i] = $rsSalesHeaderCost[$i]['requestamount'];
            $arr['amount'][$i] = $rsSalesHeaderCost[$i]['subtotal'];
            $arr['detailDesc'][$i] = '';
            $totalCashOut = $totalCashOut+$rsSalesHeaderCost[$i]['subtotal'];  
        }
         
        $arr['code'] = 'xxxxxx';
        $arr['hidRefKey'] = $rsHeader[0]['pkey'];
        $arr['refCode'] = $rsHeader[0]['code'];
        $arr['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        $arr['hidEmployeeKey'] = $recipientkey;
        $arr['selWarehouse'] = $rsHeader[0]['warehousekey'];
        $arr['trDesc'] = $rsHeader[0]['trdesc'];
        $arr['subtotal'] = $totalCashOut; 
        $arr['total'] = $totalCashOut; 
        $rsCashOutKey =  $this->getTableKeyAndObj($this->tableName); 
        $arr['hidRefTable'] = $rsCashOutKey['key']; 
             
        $arrayToJs = $truckingCostCashOut->addData($arr); 
        
        // sementara utk logol
        if($this->useRealization()){ 
            $employee = new Employee();
            $rsEmployee = $employee->getDataRowById($recipientkey);
            
            // sementara saja pake patokan ini, harusnya pake settingan lg (autoconfirm)
            // kalo gk butuh realisasi, langsung proses kas keluar
            if(!empty($rsEmployee) && $rsEmployee[0]['needrealization'] == 0){ 
                $truckingCostCashOut->changeStatus($arrayToJs[0]['data']['pkey'],3);  
            }
        }
        

        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);
        
        
    }

    function validateForm($arr,$pkey = ''){    
	    $service = new Service(); 
        $security = new Security();
        $truckingSellingRate  = new TruckingSellingRate();
        $item = new Item();
         
        // kalo dr API (yg gk ad userkey) / SYSTEM, gk perlu cek kontrak.
        $overwriteContractAllowed = (empty($this->userkey)) ? true :  $security->isAdminLogin($this->overwriteContractSecurityObject,10);
        $sellingPriceAllowed = $security->isAdminLogin($this->sellingPriceSecurityObject,10);
          
		$allowEmptyAmountSelling = $this->loadSetting('allowEmptyAmountSelling');
			
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$customerkey = $arr['hidCustomerKey'];  
		$plannerkey = $arr['hidPlannerKey'];  
		$categorykey = $arr['hidCategoryKey'];  
		$cargokey = $arr['hidCargoType'];  
		$arrItemkey = $arr['hidItemKey'];  
        $arrCostKey = $arr['hidItemKeyCost'];
        $arrHeaderCostKey = $arr['hidItemKeyHeaderCost'];
        $arrDetailEmployeeKey = $arr['hidDetailEmployeeKey'];
		$arrPriceInUnit = $arr['price']; 
		$arrPriceCostInUnit = $arr['priceCost'];  
          
        $arrQty = $arr['qty'];
        $arrCostQty = $arr['qtyCost'];
        //$arrQtyInvoiced = $arr['qtyInvoiced'];
        //$arrQtyCostInvoiced = $arr['qtyCostInvoiced'];
        
   
        $rs = (!empty($pkey)) ? $this->getDataRowById($pkey) : array() ;
         
        //validasi kalo status gk menunggu / konfirmasi gk bisa edit 
		if (!empty($rs)){ 
			if ($rs[0]['statuskey'] > 5){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
            
            $rsInvoice = $this->getInvoiceInformation($rs[0]['pkey']);
            if(!empty($rsInvoice) && $customerkey <> $rsInvoice[0]['customerkey'])
                $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][3]);
            
            // validasi cost rate
            // khusus ubah ke Proses SPK
            if ($rs[0]['statuskey'] == 2 ){
                
                $costRateIsMandatory = $this->loadSetting('costRateIsMandatory');
                if ($costRateIsMandatory == 1) { 
                    $response = $this->validateFixedCostMustExist(array('code' => $arr['code'],
                                                                        'warehousekey' => $arr['selWarehouseKey'],
                                                                        'categorykey' => $arr['hidCategoryKey'],
                                                                        'stuffinglocationkey' => $arr['hidStuffingLocationKey'],
                                                                        'cargotypekey' => $arr['hidCargoType'], 
                                                                        'consigneekey' =>  $arr['hidConsigneeKey']
                                                                       ), $arr['hidItemKey']);
                    
                    $arrayToJs += $response;
                }
                
            }
            
            
		} 

		if(empty($customerkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
		} 
         
         /*
         error kalo gk punya akses overwrite.
         di add / edit sudah narik ulang cargotype, harusnya sih aman
         
		if(empty($plannerkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][3]);
		} */
         
		if(empty($categorykey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		}
        
        if(empty($cargokey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['jobType'][1]);
		} 
		 
        if (!$overwriteContractAllowed){
            // hanya cek jika add atau edit status 1
            if ( (empty($rs) || $rs[0]['statuskey'] == 1) && empty($arr['hidContractKey'])){  
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['sellingRate'][1]);  
            }
            
            // cek kontrak salah pelanggan gk
            if(!empty($arr['hidContractKey'])){ 
                $rsContract = $truckingSellingRate->getDataRowById($arr['hidContractKey']);
                if ($rsContract[0]['customerkey'] <> $customerkey){
                     $this->addErrorList($arrayToJs,false, $this->errorMsg['sellingRate'][3]);  
                }
            }
        }
              
        $hasDetail = false;
		for($i=0;$i<count($arrItemkey);$i++) { 
            if (!empty($arrItemkey[$i]))
                $hasDetail = true;
        }
        
		for($i=0;$i<count($arrCostKey);$i++) { 
            if (!empty($arrCostKey[$i]))
                $hasDetail = true;
        }
         
        
       /* if(empty($arr['_mnv-api']) && !$hasDetail)
            $this->addErrorList($arrayToJs,false, $this->errorMsg[501]);  */
            
		
        $arrDetailKeys = array();  
         
		
		// kalau gk punya akses liat harga jual, karena masih bisa nambah item
		if ($sellingPriceAllowed) { 
			for($i=0;$i<count($arrItemkey);$i++) {  
				if (!empty($arrItemkey[$i])){   
					
					// sementara khusus praja boleh 0 
					if($allowEmptyAmountSelling <> 1){ 
						if ( $this->unFormatNumber($arrPriceInUnit[$i]) <= 0){  
							$rsItem = $service->getDataRowById($arrItemkey[$i]); 
							$this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. '. $this->errorMsg[500]);  
						}  	
					}
					
				} 
			}
		}

        
        for($i=0;$i<count($arrCostKey);$i++) {  
			if (!empty($arrCostKey[$i])){  
                if ( $this->unFormatNumber($arrPriceCostInUnit[$i]) <= 0){  
                    $rsItem = $service->getDataRowById($arrCostKey[$i]); 
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. '. $this->errorMsg[500]);  
                }  
			} 
		}
        
        /*if(empty($plannerkey)){
            for($i=0;$i<count($arrDetailEmployeeKey);$i++) {  
                if (empty($arrDetailEmployeeKey[$i])){   
                    $rsItem = $service->getDataRowById($arrHeaderCostKey[$i]); 
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. '. $this->errorMsg['recipient'][1]);   
                } 
            } 
        }*/
        
        // validasi qty invoiced
        if (!empty($pkey)){ 
            
            // ITEM DETAIL 
            $arrDetailKey = $arr['hidDetailKey'];
            $rsDetail = $this->getDetailById($pkey); 
            $rsDetail = array_column($rsDetail,null,'pkey');
            
            for($i=0;$i<count($arrDetailKey);$i++) {  
                $detailkey = $arrDetailKey[$i];
                
                if (!empty($arrItemkey[$i])){  
                    $qty = $this->unFormatNumber($arrQty[$i]);  
                    $qtyInvoiced = ( isset($rsDetail[$detailkey]) ) ? $rsDetail[$detailkey]['qtyinvoiced'] : 0 ; 
                    if ( $qty < $qtyInvoiced ){   
                        $rsItem = $service->getDataRowById($arrItemkey[$i]); 
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[505]);  
                    } 
                } 
            }
            
            
            // validasi SPK, cari yg SPK sudah konfirmasi, tp di JO nya gk ad itemnya ATAU di JO row nya kehapus
            // utk validasi perubahan layanan setelah diproses
 
            if ($rs[0]['statuskey'] > TRANSACTION_STATUS['menunggu']){  
                $truckingServiceWorkOrder = new TruckingServiceWorkOrder();  
                //$rsWO = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey',$pkey,true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')');
                $rsWO = $truckingServiceWorkOrder->searchDataRow( array( $truckingServiceWorkOrder->tableName.'.pkey',
                                                                         $truckingServiceWorkOrder->tableName.'.code',
                                                                         $truckingServiceWorkOrder->tableName.'.itemkey',
                                                                         $truckingServiceWorkOrder->tableName.'.refdetailkey', 
                                                                       ) , 
                                                            '   and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).'
                                                                and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
                                                        ); 
                
                foreach($rsWO as $woRow){      
                    // kalo SPK sudah diproses, tp detail di JO dihapus
                    if (!in_array( $woRow['refdetailkey'] ,$arrDetailKey)){
                         $rsService = $service->getDataRowById($woRow['itemkey']);
                         $this->addErrorList($arrayToJs,false,'<strong>'.$rsService[0]['name'].' ('.$woRow['code'].')</strong>. '.$this->errorMsg['truckingServiceWorkOrder'][9]); 
                    }else{ 
                        // jika detail masih ad, tp item sudah berbeda
                        for($i=0;$i<count($arrDetailKey);$i++){   
                            if ( $woRow['refdetailkey'] == $arrDetailKey[$i] && $woRow['itemkey'] != $arrItemkey[$i]) 
                                $this->addErrorList($arrayToJs,false,'<strong>'.$woRow['containername'].' ('.$woRow['code'].')</strong>. '.$this->errorMsg['truckingServiceWorkOrder'][9]); 
                        } 
                    }
                        
                }
 
            } 

            // SELLING COST
            $arrDetailKey = $arr['hidDetailCostKey'];
            $rsDetail = $this->getSellingCostDetail($pkey);
            $rsDetail = array_column($rsDetail,null,'pkey');
            
            for($i=0;$i<count($arrDetailKey);$i++) {  
                $detailkey = $arrDetailKey[$i];
                if (!empty($arrCostKey[$i])){  
                    $qty = $this->unFormatNumber($arrCostQty[$i]);  
                    $qtyInvoiced = ( isset($rsDetail[$detailkey]) ) ? $rsDetail[$detailkey]['qtyinvoiced'] : 0 ; 
                    if ( $qty < $qtyInvoiced ){   
                        $rsItem = $service->getDataRowById($arrCostKey[$i]); 
                        $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[505]);  
                    } 
                } 
            }
        }
        

            //validasi karyawan di detail tambahan biaya wajob di isi
            //jika setting 1
            $isEmployeeJobOrderIsMandatory = $this->loadSetting('cashBankReceiverJobOrderIsMandatory');

            if($isEmployeeJobOrderIsMandatory == 1) {

                $arrItemDetailCostKey = $arr['hidItemKeyHeaderCost'];
                $arrDetailEmployeeKey = $arr['hidDetailEmployeeKey'];

                $rsItemCol = $item->searchDataRow(array($item->tableName.'.pkey',$item->tableName.'.name'),
                                               ' and '. $item->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemDetailCostKey,',').')');
                $rsItemCol = array_column($rsItemCol,null,'pkey');
                
                $arrErrorCost = array();
                for($i=0; $i<count($arrItemDetailCostKey); $i++) {
                    if(!empty($arrItemDetailCostKey[$i]) && empty($arrDetailEmployeeKey[$i])) {
                        $rsItem = $rsItemCol[$arrItemDetailCostKey[$i]]; // $item->getDataRowById($arrItemDetailCostKey[$i]);
                        
                        array_push($arrErrorCost, '<strong>'. $rsItem['name'] .'</strong>. ' . $this->errorMsg['employee'][1]); 
                    }
                }

                if(!empty($arrErrorCost)) {
                    $this->addErrorList($arrayToJs,false, implode('<br>', $arrErrorCost)); 
                }

            }
        
         
		return $arrayToJs;
	 }
	  
    function updateGL($rs){
          
    }
    
    function hasConfirmedWorkOrder($detailkey){
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();       
        //$rsWO = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refdetailkey',$detailkey,true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')');
         
        $rsWO = $truckingServiceWorkOrder->searchDataRow( array( $truckingServiceWorkOrder->tableName.'.pkey') , 
                                                            '   and '.$truckingServiceWorkOrder->tableName.'.refdetailkey = '.$this->oDbCon->paramString($detailkey).'
                                                                and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
                                                        ); 
        
        
        return  (empty($rsWO)) ? false : true;
    }
     
    function getDetailWithRelatedInformation($pkey,$criteria=''){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*, 
				'.$this->tableNameDetail.'.numberkey as numberlabel, 
                concat ("#", '.$this->tableNameDetail.'.numberkey, " - ", '.$this->tableNameDetail.'.qtyinbaseunit,"x ", '.$this->tableItem.'.name) as label,
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItem.'.name as itemname,
                '.$this->tableNameDetailStatus.'.status as statusname ,
                '.$this->tableNameDetailStatus.'.class,
                '.$this->tableCarCategory.'.name as carcategoryname
              from
			  	'.$this->tableNameDetail .'
                 left join ' . $this->tableCarCategory . ' on ' . $this->tableNameDetail . '.carcategorykey = ' . $this->tableCarCategory . '.pkey,			  
	               '.$this->tableNameDetailStatus .',
                '.$this->tableItem.' 
			  where
			  	'.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and 
			  	'.$this->tableNameDetail .'.statuskey = '.$this->tableNameDetailStatus.'.pkey and 
			  	refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';
       
        $sql .= $criteria;
         
        $sql .= ' order by refkey, numberkey asc';
        
		return $this->oDbCon->doQuery($sql);
	
   }
     
    function getContainerDetail($pkey,$refspkkey = '', $criteria=''){ 
       
	   $sql = 'select
	   			'.$this->tableContainerDetail .'.*
              from
			  	'.$this->tableContainerDetail .'
			  where
			  	'.$this->tableContainerDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';
        
        if(!empty($refspkkey))
            $sql .= ' and '.$this->tableContainerDetail .'.refspkkey = '.$this->oDbCon->paramString($refspkkey) . ' ';

       
        $sql .= $criteria;
        
		return $this->oDbCon->doQuery($sql);
	
   }

    function generateDefaultQueryForAutoComplete($returnField){ 
        
        $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value, 
                    trdate,
                    donumber,
					grandtotal ,
                    consigneekey,
                    consigneeaddress,
                    '. $this->tableConsignee .'.name as consigneename
				from 
					'.$this->tableName . '
                            left join '. $this->tableConsignee .' on '. $this->tableName .'.consigneekey = '. $this->tableConsignee .'.pkey, 
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			';
          
        $sql .=  $this->getWarehouseCriteria() ; 
         return $sql;
     }
    
    function getDriverCommissionRate($pkey, $jobtypekey='',  $itemkey = ''){
          $sql = 'select
			  	'.$this->tableName.'.code, 
	   			'.$this->tableCost .'.costkey, 
	   			'.$this->tableCost .'.price  
			  from
			  	'.$this->tableName.',  
			  	'.$this->tableCost.'  
			  where 
                '.$this->tableName.'.pkey =  '.$this->tableCost .'.refkey ' ;
        
        $criteria  =  array();
        array_push( $criteria, $this->tableCost .'.costkey in (-1,-2)');  // utk jenis komisi
        
        if (!empty($pkey))
           array_push( $criteria, $this->tableName .'.pkey = '. $this->oDbCon->paramString($pkey));
        
        if (!empty($jobtypekey))
           array_push( $criteria, $this->tableCost .'.jobtypekey = '. $this->oDbCon->paramString($jobtypekey));
         
        if (!empty($itemkey))
           array_push( $criteria, $this->tableCost .'.itemkey = '. $this->oDbCon->paramString($itemkey));
         
        
        if (!empty($criteria)){
            $criteria = implode( ' and ', $criteria); 
            $sql .= ' and ' .$criteria;
        } 
          
		$rs = $this->oDbCon->doQuery($sql);
          
        return $rs;
    }
    
    function getCostDetail($pkey, $jobtypekey='',  $itemkey = '' ,$costkey = ''){
        // gk perlu lokasi, karena sudah ad pkey job order
        // jobtypekey => jenis pekerjaan, bukan kategori pekerjaan misalnya Landing + Tarik Full, Kirim head dst
        // getCostDetail($rsJobType[$k]['jobtypekey'],  $rsHeader[0]['consigneelocationkey'] , $rsDetail[$i]['itemkey'] ); 
        
        
        // JGN PKE LEFT JOIN,  karena ad jenis itemkey -1 dan -2 utk komisi, kalo mau left join, harus cek di modul lain jg
        
        $sql = 'select
			  	'.$this->tableName.'.code, 
	   			'.$this->tableCost .'.* , 
	   			'.$this->tableItem .'.name 
			  from
			  	'.$this->tableName.',  
			  	'.$this->tableCost.' , 
			  	'.$this->tableItem.' 
			  where 
                '.$this->tableName.'.pkey =  '.$this->tableCost .'.refkey and 
                '.$this->tableCost.'.costkey =  '.$this->tableItem .'.pkey ' ;
        
        $criteria  =  array();
        
        if (!empty($pkey))
           array_push( $criteria, $this->tableName .'.pkey = '. $this->oDbCon->paramString($pkey));
        
        if (!empty($jobtypekey))
           array_push( $criteria, $this->tableCost .'.jobtypekey = '. $this->oDbCon->paramString($jobtypekey));
         
        if (!empty($itemkey))
           array_push( $criteria, $this->tableCost .'.itemkey = '. $this->oDbCon->paramString($itemkey));
         
        if (!empty($costkey))
           array_push( $criteria, $this->tableCost .'.costkey in ('. $this->oDbCon->paramString($costkey,',').')');  
        
        if (!empty($criteria)){
            $criteria = implode( ' and ', $criteria); 
            $sql .= ' and ' .$criteria;
        } 
          
		$rs = $this->oDbCon->doQuery($sql);
          
        return $rs;
    }
    
    function getHeaderCost($pkey,$criteria='', $orderBy=''){ 
        
        $sql = 'select 
	   			'.$this->tableHeaderCost .'.* , 
	   			'.$this->tableItem .'.code  as itemcode , 
	   			'.$this->tableItem .'.name  as itemname , 
	   			'.$this->tableItem .'.reimburse,
                '.$this->tableTruckingCostCashOut.'.code as refcashoutcode,
                '.$this->tableEmployee.'.name as recipientname,
                '.$this->tableEmployee.'.code as recipientcode, 
				'.$this->tableServiceCategory.'.name as categoryname
			  from 
			  	'.$this->tableHeaderCost.' 
                    left join '.$this->tableTruckingCostCashOut.' on '.$this->tableHeaderCost.'.refcashoutkey = '.$this->tableTruckingCostCashOut.'.pkey
                    left join '.$this->tableEmployee.' on '.$this->tableHeaderCost.'.employeekey = '.$this->tableEmployee.'.pkey,
			  	'.$this->tableItem.' 
					left join  '.$this->tableServiceCategory.' on '.$this->tableItem.'.categorykey =  '.$this->tableServiceCategory .'.pkey 
			  where   
                '.$this->tableHeaderCost.'.refkey in ('.$this->oDbCon->paramString($pkey,',').')  and
                '.$this->tableHeaderCost.'.costkey =  '.$this->tableItem .'.pkey ' ;
    
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
  
	   if (!empty($orderBy))  
            $sql .=  ' ' .$orderBy; 
         
		$rs = $this->oDbCon->doQuery($sql);
         
        return $rs;
    } 
     
    function getSellingCostDetail($pkey, $criteria = '',$orderBy = ''){ 
        
        $sql = 'select 
	   			'.$this->tableSellingCost .'.* , 
	   			'.$this->tableItem .'.code  as itemcode, 
	   			'.$this->tableItem .'.name  as itemname, 
	   			'.$this->tableItem .'.reimburse, 
				'.$this->tableServiceCategory.'.name as categoryname,
                '.$this->tableCar.'.policenumber
			  from 
			  	'.$this->tableSellingCost.' 
                  left join '. $this->tableCar .' on '. $this->tableSellingCost .'.carkey = '. $this->tableCar.'.pkey, 
			  	'.$this->tableItem.' 
					left join  '.$this->tableServiceCategory.' on '.$this->tableItem.'.categorykey =  '.$this->tableServiceCategory .'.pkey
			  where 
                '.$this->tableSellingCost.'.costkey =  '.$this->tableItem .'.pkey and
                '.$this->tableSellingCost.'.refkey in('.$this->oDbCon->paramString($pkey,',').')' ;
         
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
           
	   if (!empty($orderBy))  
            $sql .=  ' ' .$orderBy; 
		
		$rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
     
	function getWorkOrderOutsourceCost($pkey){
		// utk informasi biaya seperti SPK logol, ad beberapa detail outsource
		// ambil dr outsource cost agar bisa dipake di logol dan yg lainnya
		
		$sql = '
				select
					'.$this->tableWorkOrder.'.refkey,
					'.$this->tableWorkOrder.'.outsourcecost
				from 
					'.$this->tableWorkOrder.' 
				where
					'.$this->tableWorkOrder.'.refkey in ('.$this->oDbCon->paramString($pkey,',').') and 
					'.$this->tableWorkOrder.'.statuskey in (2,3)
			';
		  
//		if (!empty($criteria))  
//            $sql .=  ' ' .$criteria; 
//           
//	    if (!empty($orderBy))  
//            $sql .=  ' ' .$orderBy; 
//		
		$rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
	}
	
	function getWorkOrderCost($pkey, $criteria = '',$orderBy = ''){ 
		// pishakan dr yg function getWorkOrderCostDetail, agar tdk berat
		// yg dihitung adalah harga dasar sebelum ppn, dan di SPK blm ad informasi include/exclude
		$sql = '
				select
					'.$this->tableWorkOrder.'.refkey,
					'.$this->tableWorkOrderCost.'.qty * '.$this->tableWorkOrderCost.'.amount as subtotal,
					'.$this->tableServiceCategory.'.name as categoryname
				from
					'.$this->tableWorkOrderCost.',
					'.$this->tableWorkOrder.',
					'.$this->tableItem.' 
						left join  '.$this->tableServiceCategory.' on '.$this->tableItem.'.categorykey =  '.$this->tableServiceCategory .'.pkey 
				where
					'.$this->tableWorkOrder.'.refkey in ('.$this->oDbCon->paramString($pkey,',').') and
					'.$this->tableWorkOrder.'.pkey = '.$this->tableWorkOrderCost.'.refkey and
					'.$this->tableWorkOrderCost.'.costkey = '.$this->tableItem.'.pkey and
					'.$this->tableWorkOrder.'.statuskey in (2,3)
			';
		  
		if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
           
	    if (!empty($orderBy))  
            $sql .=  ' ' .$orderBy; 
		
		$rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
	}
		
    function getUnInvoicedItemDetail($pkey){
        
        // asumsi itemkey dan costkey, pasti pkeynya unique, dan masing2 hanya bisa di detail atau di cost
        $sql = '  SELECT trans.*, item.name as itemname,item.istax23,ispriceincludetax,taxpercentage, item.aliasname from ( 
                    select concat(pkey,\'-\',itemkey) as joinkey, pkey, refkey, itemkey, qtyinbaseunit,  (qtyinbaseunit - qtyinvoiced) as outstandingqty, priceinunit, (qtyinbaseunit - qtyinvoiced) * priceinunit as total, \'1\' as orderlist  from  '.$this->tableNameDetail.' where refkey in ('.$this->oDbCon->paramString($pkey,',').') UNION
                    select concat(pkey,\'-\',costkey) as joinkey, pkey, refkey, costkey as itemkey, qty as qtyinbaseunit, (qty - qtyinvoiced) as outstandingqty, price as priceinunit, (qty - qtyinvoiced) * price as total, \'2\' as orderlist from '.$this->tableSellingCost.' where refkey in ('.$this->oDbCon->paramString($pkey,',').')
                 ) trans, item 
                 where  
                    trans.refkey in ('.$this->oDbCon->paramString($pkey,',').') and  
                    trans.itemkey = item.pkey  and outstandingqty > 0  
                 order by orderlist asc, pkey asc
                ';
        
		$rs = $this->oDbCon->doQuery($sql);
		 
		// overwrite alias
		// harus cari customernya dulu dr header, nanti dilihat berat gk
		$rsHeader = $this->searchDataRow(array($this->tableName.'.customerkey'),
										 ' and '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($pkey,',').')',
										 ' limit 1'
										 );
		
		$customer = new Customer();
		$rsItemAlias = $customer->getItemAliasDetail($rsHeader[0]['customerkey']);
		$rsItemAlias = array_column($rsItemAlias,'alias','itemkey');
		
		$totalRs = count($rs);
		for($i=0;$i<$totalRs;$i++){ 
			$itemkey = $rs[$i]['itemkey'];
			if(isset($rsItemAlias[$itemkey]) && !empty($rsItemAlias[$itemkey])) 
				$rs[$i]['aliasname'] = $rsItemAlias[$itemkey]; 
		}
		
        return $rs;
    }
     
    function updateDetailStatusType2($detailkey){
        
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
             
        try{  

			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]); 
			
			$rsDetail = $this->getDetailByColumn('pkey',$detailkey);

            $sql = 'select 
                        '.$truckingServiceWorkOrder->tableName.'.pkey,
                        '.$truckingServiceWorkOrder->tableName.'.refkey,
                        '.$truckingServiceWorkOrder->tableName.'.statuskey,
                        sum('.$truckingServiceWorkOrder->tableWorkOrderCarDetail.'.qty) as totalqty
                    from
                        '.$truckingServiceWorkOrder->tableName.',
                        '.$truckingServiceWorkOrder->tableWorkOrderCarDetail.'
                    where
                        '.$truckingServiceWorkOrder->tableWorkOrderCarDetail.'.refkey = '.$truckingServiceWorkOrder->tableName.'.pkey and
                        '.$truckingServiceWorkOrder->tableName.'.refdetailkey = '.$this->oDbCon->paramString($detailkey).' and 
                        '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')
                    group by 
                        '.$truckingServiceWorkOrder->tableName.'.statuskey
                    order by 
                        '.$truckingServiceWorkOrder->tableName.'.pkey desc
                    ';
             
            $rs = $this->oDbCon->doQuery($sql);
            $rsSumQty = array_column($rs,'totalqty','statuskey');
            
            // harus tampung dulu, karena kalo gk ad statusnya, gk ad indexnya
            $arrSumQty = array();
            $rsStatus = $this->getAllStatus($this->tableDetailStatus);
            for($i=0;$i<count($rsStatus);$i++)
                $arrSumQty[$rsStatus[$i]['pkey']] = (isset($rsSumQty[$rsStatus[$i]['pkey']])) ?  $rsSumQty[$rsStatus[$i]['pkey']] : 0;
               
            //status selesai kalo sudah tdk ad yg menunggu dan konfirmasi dan jml partai sama / lebih dr qty
            // bisa saja, jml selesai sudah sesuai dgn jml qty, tp masih ad yg di pending / konfirmasi 1 partai, tetep dianggap blm selesai
            if($arrSumQty[TRANSACTION_STATUS['menunggu']] == 0 && 
               $arrSumQty[TRANSACTION_STATUS['konfirmasi']] == 0 &&
               $arrSumQty[TRANSACTION_STATUS['selesai']] >= $rsDetail[0]['qtyinbaseunit']
               ){  
                  $statuskey = 3;
            }else if($arrSumQty[TRANSACTION_STATUS['konfirmasi']] == 0 && $arrSumQty[TRANSACTION_STATUS['selesai']] == 0){
                //status menunggu kalo semua qty hanya ad di menunggu
                  $statuskey = 1;
            }else{ 
                  $statuskey = 2;
            }

			$sql = 'update '.$this->tableNameDetail.' set statuskey = '.$statuskey.' where pkey = ' . $this->oDbCon->paramString($detailkey) ;   
			$this->oDbCon->execute($sql);  

			//update header kalo semua status sudah selesai 
            // kalo gk ad SPK, gk auto closing
            if (!empty($rs)){  
                // cari detailnya kalo sudah selesai semua, update ke SPK Selesai
                
                $rsSPK = $this->getDetailById($rsDetail[0]['refkey']);
                $completed = true;
                $arrPartialStatus = array(1,2);
                foreach($rsSPK as $row){
                    // kalo ad salah satu detailnya yg statusnya menunggu / partial
                    if(in_array($row['statuskey'],$arrPartialStatus) ) {
                        $completed = false;
                        break;
                    }
                }

                $status = ($completed) ? 3 : 2;  
                $rsHeader = $this->getDataRowById($rsDetail[0]['refkey']);
                
                if ($status <> $rsHeader[0]['statuskey'])
                    $this->changeStatus($rsHeader[0]['pkey'], $status,'',false,true); 
                
            }  
            
            $this->oDbCon->endTrans(); 

		}catch(Exception $e){
			$this->oDbCon->rollback();  
		}
    }
    
    function updateDetailStatus($detailkey){
        $truckingType = $this->loadSetting('truckingType');
        if($truckingType == 2){
            $this->updateDetailStatusType2($detailkey);
            return;
        } 
        
        // ASUMSI SETIAP JO PASTI MIN ADA 1 SPK, GK BISA !!!
                
         $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
	     
         try{  

                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]); 

                // search semua status work order, kalo sudah closed semua, update status 
                //$rs = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refdetailkey',$detailkey,true,' and '. $truckingServiceWorkOrder->tableName.'.statuskey in (1,2,3)','order by pkey desc');

                $rs = $truckingServiceWorkOrder->searchDataRow( array( $truckingServiceWorkOrder->tableName.'.pkey',
                                                                      $truckingServiceWorkOrder->tableName.'.refkey' ,
                                                                      $truckingServiceWorkOrder->tableName.'.statuskey' 
                                                                   ) , 
                                                        '   and '.$truckingServiceWorkOrder->tableName.'.refdetailkey = '.$this->oDbCon->paramString($detailkey).'
                                                            and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')',
                                                        'order by '.$truckingServiceWorkOrder->tableName.'.pkey desc'
                                                    ); 


                $totalSPK = count($rs);
                $statusSPK = array();

                $rsStatus = $this->getAllStatus($this->tableDetailStatus);
                for($i=0;$i<count($rsStatus);$i++){
                    $statusSPK[$rsStatus[$i]['pkey']] = 0;
                }

                for($i=0;$i<count($rs);$i++){  
                    $statuskey = $rs[$i]['statuskey']; 
                    $statusSPK[$statuskey]++; 
                } 

                // kalo semua status masih open 
                if($statusSPK[1] == count($rs)) 
                    $statuskey = 1;
                else if ($statusSPK[2] <> 0) 
                    $statuskey = 2;
                else 
                    $statuskey = 3; 

                $sql = 'update '.$this->tableNameDetail.' set statuskey = '.$statuskey.' where pkey = ' . $this->oDbCon->paramString($detailkey) ;  
                $this->oDbCon->execute($sql);  


                //update header kalo semua status sudah selesai 
                // kalo gk ad SPK, gk auto closing
                if (!empty($rs)){ 
                    //$rsSPK = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey', $rs[0]['refkey'] ,true,' and '. $truckingServiceWorkOrder->tableName.'.statuskey in (1,2)','order by pkey desc');

                    $rsSPK = $truckingServiceWorkOrder->searchDataRow( array( $truckingServiceWorkOrder->tableName.'.pkey') , 
                                                                                '   and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($rs[0]['refkey']).'
                                                                                    and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].')',
                                                                              'order by '.$truckingServiceWorkOrder->tableName.'.pkey desc'
                                                    ); 



                    $status = (empty($rsSPK)) ? 3 : 2;

                    $rsDetail = $this->getDetailByColumn('pkey',$detailkey);
                    if (!empty($rsDetail)){ 
                        $rsHeader = $this->getDataRowById($rsDetail[0]['refkey']);
                        if ($status <> $rsHeader[0]['statuskey'])
                            $this->changeStatus($rs[0]['refkey'], $status,'',false,true); 
                    }

                } 

                $this->oDbCon->endTrans(); 

            }catch(Exception $e){
                $this->oDbCon->rollback();  
            }
 
    }
    
    function updateDetailContainer($wokey){
         
            // sementara utk logol aj dulu
            $truckingType = $this->loadSetting('truckingType');
        
            if ($truckingType == 1) return;
        
            $truckingServiceWorkOrder = new TruckingServiceWorkOrder();

        
            $rsSPK = $truckingServiceWorkOrder->searchDataRow( array( $truckingServiceWorkOrder->tableName.'.pkey',$truckingServiceWorkOrder->tableName.'.refkey',$truckingServiceWorkOrder->tableName.'.statuskey' ) , 
                                                            '   and '.$truckingServiceWorkOrder->tableName.'.pkey = '.$this->oDbCon->paramString($wokey) 
                                                        ); 
     
        
            $jokey = $rsSPK[0]['refkey'];

            $rsJODetailContainer = $this->getContainerDetail($jokey,$wokey);
            $arrRefSPKKey = array_column($rsJODetailContainer,'refspkdetailkey');
        
            $rsSPKDetailCar = $truckingServiceWorkOrder->getCarDetail($wokey);
            $arrDetailSPKCarKey = array_column($rsSPKDetailCar,'pkey');
       
            // kalo batal, otomatis hapus saja
            if($rsSPK[0]['statuskey'] == 4){
                $sql = 'delete from '.$this->tableContainerDetail.' 
                        where  refspkkey =  ' . $this->oDbCon->paramString($wokey); 
                $this->oDbCon->execute($sql); 

                return;
            }
        
        
             $sql = 'delete from '.$this->tableContainerDetail.' 
                    where
                       refkey = ' . $this->oDbCon->paramString($jokey).' and 
                       refspkkey =  ' . $this->oDbCon->paramString($wokey).' and 
                       refspkdetailkey  not in (' . $this->oDbCon->paramString($arrDetailSPKCarKey,',').')';
                
                
            $this->oDbCon->execute($sql); 
        
            foreach($rsSPKDetailCar as $key => $containerValue){
                    
                    if(in_array($containerValue['pkey'],$arrRefSPKKey)){

                        if($containerValue['container'] == '' && $containerValue['seal'] == ''){
                              $sql = 'delete from '.$this->tableContainerDetail.'  
                                    where 
                                        refkey = ' . $this->oDbCon->paramString($jokey).' and 
                                        refspkkey = ' . $this->oDbCon->paramString($wokey).' and
                                        refspkdetailkey = '.$this->oDbCon->paramString($containerValue['pkey']) ;  
                            
                        }else{ 
                            $sql = 'update '.$this->tableContainerDetail.' 
                                        set container = '.$this->oDbCon->paramString($containerValue['container']).' , seal = '.$this->oDbCon->paramString($containerValue['seal']).'
                                    where 
                                        refkey = ' . $this->oDbCon->paramString($jokey).' and 
                                        refspkkey = ' . $this->oDbCon->paramString($wokey).' and
                                        refspkdetailkey = '.$this->oDbCon->paramString($containerValue['pkey']) ;  
                        }
                        
                            $this->oDbCon->execute($sql);  

                    }else{

                            $sql = 'insert into 
                                        '.$this->tableContainerDetail.' (refkey,refspkkey,refspkdetailkey,container,seal) 
                                    values 
                                        ('.$this->oDbCon->paramString($jokey).','.$this->oDbCon->paramString($wokey).','.$this->oDbCon->paramString($containerValue['pkey']).','.$this->oDbCon->paramString($containerValue['container']).','.$this->oDbCon->paramString($containerValue['seal']).')';	
				          $this->oDbCon->execute($sql);	
                        
                    }
                    
                }
     
        
    }

    function updateContainer($pkey){
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        $arrContainerJO = array();
        $arrContainerSPK = array();
        
        $rsHeader = $this->getDataRowById($pkey);
        
        //$rsSPK = $truckingServiceWorkOrder->searchData('','',true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3) and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($pkey));
        $rsSPK = $truckingServiceWorkOrder->searchDataRow( array( $truckingServiceWorkOrder->tableName.'.pkey',
                                                                         $truckingServiceWorkOrder->tableName.'.containernumber',
                                                                         $truckingServiceWorkOrder->tableName.'.container2number'
                                                                       ) , 
                                                            '   and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).'
                                                                and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
                                                        ); 
        
        
        //tampung semua container dan container2 jadi 1 variabel
        for($i=0;$i<count($rsSPK);$i++){
            if(empty($rsSPK[$i]['containernumber']) && empty($rsSPK[$i]['container2number']))
                continue;
    
            if(!empty($rsSPK[$i]['containernumber']))
                array_push($arrContainerSPK,$rsSPK[$i]['containernumber']);
            
            if(!empty($rsSPK[$i]['container2number']))
                array_push($arrContainerSPK,$rsSPK[$i]['container2number']);    
        }
        
        for($i=0;$i<count($arrContainerSPK);$i++){
            if(in_array($arrContainerSPK[$i],$arrContainerJO))
                    continue;
            
            array_push($arrContainerJO,$arrContainerSPK[$i]); 
        }
        
        $container = implode(', ',$arrContainerJO);
        
        $sql = ' update '.$this->tableName.' set containernumber = '.$this->oDbCon->paramString($container).'
                 where '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey);
         
        $this->oDbCon->execute($sql);
        
    }

    function getWorkOrderCostDetail($pkey,  $forOutsource = false, $group = true, $criteria = '', $orderBy = ' order by  workordercode asc, costname asc ' ){
        if(empty($pkey)) return array(); // biar pas load JO form pertama kali gk berat
        
        $ap = new AP();
         
        // untuk form complete order
        $supplier = new Supplier();
        $employee = new Employee();
        
        $truckingType = $this->loadSetting('truckingType');
        
        $arrCriteria = array();
        array_push($arrCriteria, $this->tableWorkOrder.'.statuskey in (2,3)');
        array_push($arrCriteria, $this->tableWorkOrder.'.refkey = '.$this->oDbCon->paramString($pkey));
        
        $rsAPTypeKey = $this->getTableKeyAndObj($this->tableWorkOrder, array('key')); 
        
        if ($forOutsource){
            $tableName = $supplier->tableName;   
            $fieldName = 'supplierkey'; 
            $cashOutTable = $ap->tableName;  
            $rsWOTypeKey = $ap->getTableKeyAndObj($this->tableWorkOrderCost); 
        }else{
            $tableName = $employee->tableName;   
            $fieldName = 'employeekey'; 
            $cashOutTable = $this->tableTruckingCostCashOut; 
            $rsWOTypeKey = $ap->getTableKeyAndObj($this->tableWorkOrder); 
        }  
          
        $sqlCriteria = ' and ' . implode(' and ', $arrCriteria);
        
        $selectCount = '';
        $groupBy = ''; 
        if($group){
            $selectCount = ' sum(qty) as qty, ';
            $groupBy = ' group by costkey, amount ';
            $orderBy = ' order by costname asc  ';
        }
        
        $arrSQL = array();
            
        $sql = '
            select  
                '.$this->tableWorkOrder.'.refkey, 
                '.$this->tableWorkOrderCost.'.qty,
                '.$this->tableWorkOrderCost.'.costkey,
                '.$this->tableWorkOrderCost.'.amount,
                '.$this->tableWorkOrderCost.'.requestamount,
                '.$this->tableWorkOrderCost.'.taxpercentage,
                '.$this->tableWorkOrderCost.'.total,
                '.$this->tableWorkOrderCost.'.isrealization,
                '.$tableName.'.name as recipientname,
                '.$this->tableItem.'.name as costname,
                '.$this->tableItem.'.reimburse, 
                '.$this->tableWorkOrder.'.code as workordercode, 
                '.$this->tableWorkOrder.'.statuskey,
                '.$cashOutTable.'.code as cashoutcode,
                0 as headerrow
            from
                '.$tableName.',
                '.$this->tableWorkOrder.', 
                '.$this->tableWorkOrderCost.'  
                    left join '.$cashOutTable.' on '.$this->tableWorkOrderCost.'.refcashoutkey = '.$cashOutTable.'.pkey and
                        '.$cashOutTable.'.reftabletype = '.$rsWOTypeKey['key'].',
                '.$this->tableItem.' 
            where 
                '.$this->tableWorkOrder.'.pkey = '.$this->tableWorkOrderCost.'.refkey  and
                '.$this->tableItem.'.pkey = '.$this->tableWorkOrderCost.'.costkey and
                '.$this->tableWorkOrderCost.'.'.$fieldName.' = ' . $tableName.'.pkey
        ';
        $sql .= $sqlCriteria ;
        
        array_push($arrSQL, $sql);
        
        // tambah biaya TL
        if ($forOutsource){
            
            if ($truckingType == 2){ 
                // model logol  
                    
                $sqlOutsource = '
                    select  
                        '.$this->tableWorkOrder.'.refkey, 
                        '.$this->tableWorkOrderCarDetail.'.qty as qty,
                        '.$this->tableWorkOrderCarDetail.'.itemkey as costkey,
                        '.$this->tableWorkOrderCarDetail.'.price as amount,  
                        '.$this->tableWorkOrderCarDetail.'.price as requestamount,  
                        '.$this->tableWorkOrderCarDetail.'.taxpercentage as taxpercentage,
                        '.$this->tableWorkOrderCarDetail.'.total as total,  
                        1 as isrealization,
                        '.$this->tableSupplier.'.name as recipientname,
                        \''.$this->lang['truckingFee'].'\' as costname,
                        0 as reimburse, 
                        '.$this->tableWorkOrder.'.code as workordercode, 
                        '.$this->tableWorkOrder.'.statuskey,
                        '.$cashOutTable.'.code as cashoutcode,
                        1 as headerrow
                    from 
                        '.$this->tableSupplier.',
                        '.$this->tableWorkOrderCarDetail.' ,
                        '.$this->tableWorkOrder.' 
                            left join  '.$cashOutTable.' on '.$this->tableWorkOrder.'.refcashoutkey = '.$cashOutTable.'.pkey and
                        '.$cashOutTable.'.reftabletype = '.$rsAPTypeKey['key'].'
                    where   
                        '.$this->tableWorkOrder.'.isoutsource = 1 and
                        '.$this->tableWorkOrder.'.outsourcecost <> 0 and
                        '.$this->tableWorkOrder.'.pkey = '.$this->tableWorkOrderCarDetail.'.refkey and
                        '.$this->tableWorkOrder.'.supplierkey = '.$this->tableSupplier.'.pkey
                '; 
                
                
            }else{
                
                 // reguler
                $sqlOutsource = '
                    select  
                        '.$this->tableWorkOrder.'.refkey, 
                        1 as qty,
                        0 as costkey,
                        '.$this->tableWorkOrder.'.outsourcecost as amount,  
                        '.$this->tableWorkOrder.'.outsourcecost as requestamount,  
                        0 as taxpercentage,
                        '.$this->tableWorkOrder.'.outsourcecost as total,  
                        1 as isrealization,
                        '.$this->tableSupplier.'.name as recipientname,
                        \''.$this->lang['truckingFee'].'\' as costname,
                        0 as reimburse, 
                        '.$this->tableWorkOrder.'.code as workordercode, 
                        '.$this->tableWorkOrder.'.statuskey,
                        '.$cashOutTable.'.code as cashoutcode,
                        1 as headerrow
                    from 
                        '.$this->tableSupplier.',
                        '.$this->tableWorkOrder.'  
                            left join  '.$cashOutTable.' on '.$this->tableWorkOrder.'.refcashoutkey = '.$cashOutTable.'.pkey and
                        '.$cashOutTable.'.reftabletype = '.$rsAPTypeKey['key'].'
                    where   
                        '.$this->tableWorkOrder.'.isoutsource = 1 and
                        '.$this->tableWorkOrder.'.outsourcecost <> 0 and
                        '.$this->tableWorkOrder.'.supplierkey = '.$this->tableSupplier.'.pkey
                '; 
            }
           
            
            
            $sqlOutsource .= $sqlCriteria ; 
            
            
            
            array_push($arrSQL, $sqlOutsource);
        }else{
             $sqlCommission = '
                select  
                    '.$this->tableWorkOrder.'.refkey, 
                    1 as qty,
                    0 as costkey,
                    '.$this->tableWorkOrder.'.drivercommission as amount,  
                    '.$this->tableWorkOrder.'.drivercommission as requestamount,  
                    0 as taxpercentage,
                    '.$this->tableWorkOrder.'.drivercommission as total,  
                    1 as isrealization,
                    '.$this->tableEmployee.'.name as recipientname,
                    \''.$this->lang['driverCommission'].'\' as costname,
                    0 as reimburse, 
                    '.$this->tableWorkOrder.'.code as workordercode, 
                    '.$this->tableWorkOrder.'.statuskey,
                    '.$this->tableAPEmployee.'.code as cashoutcode,
                    1 as headerrow
                from  
                    '.$this->tableWorkOrder.'  
			            left join '.$this->tableEmployee.'   on  '.$this->tableWorkOrder.'.driverkey = '.$this->tableEmployee.'.pkey
                        left join '.$this->tableAPEmployee.' on '.$this->tableWorkOrder.'.refcashoutdriverkey = '.$this->tableAPEmployee.'.pkey and
                            '.$this->tableAPEmployee.'.reftabletype = '.$rsAPTypeKey['key'].' 
                where   
                    '.$this->tableWorkOrder.'.isoutsource = 0 and
                    '.$this->tableWorkOrder.'.drivercommission > 0
                   
            '; 
            $sqlCommission .= $sqlCriteria ; 
            array_push($arrSQL, $sqlCommission);
            
            $sqlCoDriverCommission = '
                select  
                    '.$this->tableWorkOrder.'.refkey, 
                    1 as qty,
                    0 as costkey,
                    '.$this->tableWorkOrder.'.codrivercommission as amount,  
                    '.$this->tableWorkOrder.'.codrivercommission as requestamount,  
                    0 as taxpercentage,
                    '.$this->tableWorkOrder.'.codrivercommission as total,  
                    1 as isrealization,
                    '.$this->tableEmployee.'.name as recipientname,
                    \''.$this->lang['codriverCommission'].'\' as costname,
                    0 as reimburse, 
                    '.$this->tableWorkOrder.'.code as workordercode, 
                    '.$this->tableWorkOrder.'.statuskey,
                    '.$this->tableAPEmployee.'.code as cashoutcode,
                    1 as headerrow
               from  
                    '.$this->tableWorkOrder.'  
                        left join '.$this->tableEmployee.'  on  '.$this->tableWorkOrder.'.codriverkey = '.$this->tableEmployee.'.pkey 
                        left join '.$this->tableAPEmployee.' on '.$this->tableWorkOrder.'.refcashoutcodriverkey = '.$this->tableAPEmployee.'.pkey and
                            '.$this->tableAPEmployee.'.reftabletype = '.$rsAPTypeKey['key'].' 
                where   
                    '.$this->tableWorkOrder.'.isoutsource = 0 and
                    '.$this->tableWorkOrder.'.codrivercommission > 0
            '; 
            $sqlCoDriverCommission .= $sqlCriteria ;   
            array_push($arrSQL, $sqlCoDriverCommission);
       
        }
    
             
        $sql = 'select 
                    ' .$selectCount. ' 
                    '.$this->tableWorkOrder.'.* 
                from ('.implode(' UNION ALL ', $arrSQL).') as '.$this->tableWorkOrder.' where 1=1 '; 
        
        $sql .= $criteria;
        $sql .= $groupBy;
        $sql .= $orderBy;
         
/*        $this->setLog('=========',true);
        $this->setLog($sql,true);*/
        $rs =  $this->oDbCon->doQuery($sql);    
        return $rs;
    } 
    
    function getGroupingOutsourceCost($pkey){
        // untuk form complete order
        
       /* $arrCriteria = array();
        array_push($arrCriteria, $this->tableWorkOrder.'.statuskey in (2,3)');
        array_push($arrCriteria, $this->tableWorkOrder.'.refkey = '.$this->oDbCon->paramString($pkey));
        array_push($arrCriteria, $this->tableWorkOrder.'.isoutsource = 1'); 
            
        $criteria =  implode(' and ', $arrCriteria); 

         // tambah biaya outsource
         $sql = '
            select  
                '.$this->tableWorkOrder.'.outsourcecost as price, 
                '.$this->tableWorkOrder.'.supplierkey, 
                '.$this->tableSupplier.'.name as suppliername 
            from
                '.$this->tableSupplier.',
                '.$this->tableWorkOrder.' 
            where   
                '.$this->tableWorkOrder.'.supplierkey = '.$this->tableSupplier.'.pkey and
                '.$criteria.' 
            order by 
               '.$this->tableWorkOrder.'.code asc 
        '; 

        $rs =  $this->oDbCon->doQuery($sql);  
 
        return $rs;*/
    } 
    
     function updateTruckingCostCashOut($pkey){
          
        //header harus reload ulang, karena status sudah berubah (ketika konfirmasi)
        $rsHeader = $this->getDataRowById($pkey);
        
        $truckingCostCashOut = new TruckingCostCashOut();
           
        // get all listed employee
        $arrEmployeeKey = array();  
        
        $sql = 'select distinct(employeekey) as employeekey from ' . $this->tableHeaderCost .' where refkey = '.$this->oDbCon->paramString($pkey);
        $rsDetailEmployee = $this->oDbCon->doQuery($sql);
        $arrEmployeeKey = array_column($rsDetailEmployee, 'employeekey' );
           
        $rsKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
        
        // utk  delete karyawan yg sudah gk ad kas keluarnya
        $employeeCriteria = (!empty($arrEmployeeKey)) ? '  and reftabletype = '.$rsKey['key'].' and employeekey not in ('.implode(',',$arrEmployeeKey).') ' : ''; 
        
        $rsCashOut = $truckingCostCashOut->searchData('','',true, $employeeCriteria.'
                                                                    and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).'
                                                                    and '.$truckingCostCashOut->tableName.'.statuskey = 1');
   
	    for($i=0;$i<count($rsCashOut);$i++)
            $this->cancelCashOut($pkey,$rsCashOut[$i]['employeekey']);   
         
        // kalo status konfirmasi baru lanjut proses 
        
        if ($rsHeader[0]['statuskey'] >= 2 && $rsHeader[0]['statuskey'] <=5 ) {
          
            // update ulang kas keluar  
            for($i=0;$i<count($arrEmployeeKey);$i++){
                $employeeKey = $arrEmployeeKey[$i];

                // cost di JO 
                $rsCost = $this->getHeaderCost($rsHeader[0]['pkey'],' and '. $this->tableHeaderCost .'.refcashoutkey = 0 
                                                                      and '. $this->tableHeaderCost .'.refrequestkey = 0 
                                                                         and '. $this->tableHeaderCost .'.realizationkey = 0 
                                                                         and '. $this->tableHeaderCost .'.employeekey = '.$this->oDbCon->paramString($employeeKey));
  
                $headerCost =array();
                for($j=0;$j<count($rsCost);$j++){
                    array_push($headerCost,$rsCost[$j]['pkey']); 
                    array_push($headerCost,$rsCost[$j]['subtotal']); 
                    array_push($headerCost,$rsCost[$j]['costkey']);      
                }  
              
                
                //$this->setLog($headerCost,true);
                $headerCost = md5(json_encode($headerCost));

                // cost di cash out yg masi pending 
                $rsCashOut = $truckingCostCashOut->searchData('','',true,'  and reftabletype = '.$rsKey['key'].' 
                                                                            and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' 
                                                                            and '.$truckingCostCashOut->tableName.'.employeekey = '.$this->oDbCon->paramString($employeeKey).' 
                                                                            and '.$truckingCostCashOut->tableName.'.statuskey = 1');
                $rsCashOutDetail = (!empty($rsCashOut)) ? $truckingCostCashOut->getDetailById($rsCashOut[0]['pkey']) : array(); //ambil salah satu cashout aja
                $cashOutDetail = array();
                // harus tambah pkey detail, karena ad kemungkinan itemnya sama pkeynya berubah / pindah posisi, 
                // jdinya nanti pas kas keluar diproses, do JO nya gk keupdate
                for($j=0;$j<count($rsCashOutDetail);$j++){
                    array_push($cashOutDetail,$rsCashOutDetail[$j]['refheadercostkey']); 
                    array_push($cashOutDetail,$rsCashOutDetail[$j]['amount']); 
                    array_push($cashOutDetail,$rsCashOutDetail[$j]['costkey']);      
                } 
                
                //$this->setLog($cashOutDetail,true);
                $cashOutDetail = md5(json_encode($cashOutDetail));

               /* $this->setLog($cashOutDetail,true);
                $this->setLog($headerCost,true);*/
                
                $compareResult = ($cashOutDetail == $headerCost) ? true : false;
 
                if(!$compareResult){      
                    $this->cancelCashOut($pkey,$employeeKey);   
                    $this->addCashOut($rsHeader,$rsCost);   
                } 
            }  
        }
        
         
       
    }
     
  
    function normalizeParameter($arrParam, $trim=false){ 
        
            $truckingSellingRate = new TruckingSellingRate();
            $item = new Item();
            $security = new Security();

            $sellingPriceAllowed = $security->isAdminLogin($this->sellingPriceSecurityObject,10);
        
        
            // agar gk muncul notice / warning , sampe nanti kita bisa trim details
            $arrParam['detailRequestId'] = ( isset($arrParam['detailRequestId'] ) ) ? $arrParam['detailRequestId']  : array() ;
            $arrParam['sellingCostRequestId'] = ( isset($arrParam['sellingCostRequestId'] ) ) ? $arrParam['sellingCostRequestId']  : array() ;
            $arrParam['headerCostRequestId'] = ( isset($arrParam['headerCostRequestId'] ) ) ? $arrParam['headerCostRequestId']  : array() ;
            $arrParam['containerDetailRequestId'] = ( isset($arrParam['containerDetailRequestId'] ) ) ? $arrParam['containerDetailRequestId']  : array() ;
        
             // untuk patokan add / edit
             $pkey = 0;
             $rsHeader = array();
             if(isset($arrParam['hidId']) && !empty($arrParam['hidId'])){ 
                 $pkey = $arrParam['hidId'];  
                 $rsHeader = $this->getDataRowById($pkey); 
             }
		
			if(isset($arrParam['autoInvoice']) && !empty($arrParam['autoInvoice']) && is_array($arrParam['autoInvoice'])) 
				$arrParam['autoInvoice'] = json_encode($arrParam['autoInvoice']);  
        
            // additional cost, kalo tdk ad penerima, default samakan dengan plannerkey
            for($i=0;$i<count($arrParam['hidAdditionalKey']);$i++){
                if (empty($arrParam['hidDetailEmployeeKey'][$i])){  
                    $arrParam['hidDetailEmployeeKey'][$i] = $arrParam['hidPlannerKey'];
                }
            }
        
         // ambil ulang harga jika tidak ada akses harga jual
            if(!$sellingPriceAllowed){  
                $rsDetail = $this->getDetailById($pkey); 
                $rsDetail = array_column($rsDetail,null,'pkey');
                $arrDetailKey = $arrParam['hidDetailKey']; 
                for($i=0;$i<count($arrDetailKey);$i++) { 
                    $detailKey = $arrDetailKey[$i];
                    $arrParam['price'][$i] = (!empty($rsDetail[$detailKey])) ? $rsDetail[$detailKey]['priceinunit'] : 0;
                }
                
                
                for($i=0;$i<count($this->arrDetails);$i++) { 
                    if ($this->arrDetails[$i]['tableName'] == $this->tableSellingCost) {
                        unset($this->arrDetails[$i]);
                    }
                }
                unset($this->arrData['totalsellingcost']);
                unset($this->arrData['grandtotal']);
                $this->arrDetails = array_values($this->arrDetails);
                $this->arrData['pkey'] = array('pkey',array('dataDetail' => $this->arrDetails)); 
            }

 
         
            if(!empty($rsHeader)){  
 
                // utk status tertentu, beberapa field di unset

                if ($rsHeader[0]['statuskey'] != 1){
                    
                        unset($this->arrData['code']);
                        unset($this->arrData['trdate']);
                        unset($this->arrData['contractkey']);
                        unset($this->arrData['categorykey']);
                        unset($this->arrData['cargotypekey']);
//                        unset($this->arrData['depotkey']);
//                        unset($this->arrData['terminalkey']);
                    
                    
                    // sementara utk TWJ, masih boelh revisi consignee
//                        unset($this->arrData['consigneekey']);
//                        unset($this->arrData['consigneewarehousename']);
//                        unset($this->arrData['consigneelocationkey']);
//                        unset($this->arrData['consigneecontactperson']);
//                        unset($this->arrData['consigneeaddress']);
                    
//                        unset($this->arrData['stuffinglocationkey']);
//                        unset($this->arrData['stuffingaddress']);
                    
                    
                        unset($this->arrData['tarifflastmodifiedon']);
                        unset($this->arrData['plannerkey']);
                        /*unset($this->arrData['routefrom']);
                        unset($this->arrData['routeto']);*/
                        unset($this->arrData['warehousekey']);
                        unset($this->arrData['saleskey']);
                     
                }  


                $rsInvoice = $this->getInvoiceInformation($pkey); 
                if(!empty($rsInvoice)){
                    unset($this->arrData['poreference']);
                    unset($this->arrData['donumber']);
                    unset($this->arrData['shipmentnumber']);
                    unset($this->arrData['customerkey']);  
                    unset($this->arrData['routefrom']);
                    unset($this->arrData['routeto']);
                }

                
                // ====== UPDATE QTY YG SUDAH DIINVOICE 
 
                 // ITEM
                $rsDetail = $this->getDetailById($pkey); 
                $arrDetailKey = $arrParam['hidDetailKey'];  
                
                 // ====== UPDATE QTY YG SUDAH DIINVOICE  
                 $rsDetailKeyIndex = array_column($rsDetail,null,'pkey');
                 for($i=0;$i<count($arrDetailKey);$i++) { 
                    $detailkey = $arrDetailKey[$i];  
                    $qtyInvoiced = ( isset($rsDetailKeyIndex[$detailkey]) ) ? $rsDetailKeyIndex[$detailkey]['qtyinvoiced'] : 0 ; 
                     
                    // kalo sdh pernah diinvoiced, harga ambil ulang dr sistem
                     if($qtyInvoiced > 0){  
                         $arrParam['price'][$i] = $rsDetailKeyIndex[$detailkey]['priceinunit'];
                         $arrParam['hidItemKey'][$i] = $rsDetailKeyIndex[$detailkey]['itemkey']; 
                     }
                 }  
                 
                
                // ====== RESTORE ULANG SEMUA DETAIL YG SUDAH DIINVOICE YG KEDELETE 
                 for($i=0;$i<count($rsDetail);$i++) {  
                    $qtyInvoiced = ( isset($rsDetail[$i]) ) ? $rsDetail[$i]['qtyinvoiced'] : 0 ;
                    if($qtyInvoiced > 0 && !in_array($rsDetail[$i]['pkey'], $arrDetailKey)){  // Cek utk detailkey ada yg gk ad di table DB gk
                        // insert ulang
                        array_push($arrParam['hidDetailKey'], $rsDetail[$i]['pkey']);
                        array_push($arrParam['numberkey'], count($arrDetailKey));
                        array_push($arrParam['hidItemKey'], $rsDetail[$i]['itemkey']);
                        array_push($arrParam['qty'], $rsDetail[$i]['qtyinbaseunit']);
                        array_push($arrParam['trShipmentDate'], $this->formatDBDate($rsDetail[$i]['trdate']));
                        array_push($arrParam['price'], $rsDetail[$i]['priceinunit']); 
                        //array_push($arrParam['subtotalDetails'], $rsDetail[$i]['subtotal']); 
                        array_push($arrParam['totalDetails'], $rsDetail[$i]['total']); 
                        array_push($arrParam['detailNotes'], $rsDetail[$i]['trdesc']); 
                        array_push($arrParam['chkIsGroup'], $rsDetail[$i]['isgroup']);  
                        //array_push($arrParam['refkey'], $pkey); 
                    } 
                 }
                
                
                
                 // COST 
                $rsDetail = $this->getSellingCostDetail ($pkey); 
                $arrDetailKey = $arrParam['hidDetailCostKey'];
                 
                
                 // ====== UPDATE QTY YG SUDAH DIINVOICE  
                $rsDetailKeyIndex = array_column($rsDetail,null,'pkey'); 
                for($i=0;$i<count($arrDetailKey);$i++) { 
                    $detailkey = $arrDetailKey[$i]; 
                    
                    $qtyInvoiced = ( isset($rsDetailKeyIndex[$detailkey]) ) ? $rsDetailKeyIndex[$detailkey]['qtyinvoiced'] : 0 ;
                     
                     // kalo sdh pernah diinvoiced, harga ambil ulang dr sistem
                     if($qtyInvoiced > 0){  
                         $arrParam['priceCost'][$i] = $rsDetailKeyIndex[$detailkey]['price'];
                         $arrParam['hidItemKeyCost'][$i] = $rsDetailKeyIndex[$detailkey]['costkey'];
                     } 
                 }
                
                // ====== RESTORE ULANG SEMUA DETAIL YG SUDAH DIINVOICE YG KEDELETE
                 for($i=0;$i<count($rsDetail);$i++) {  
                    $qtyInvoiced = ( isset($rsDetail[$i]) ) ? $rsDetail[$i]['qtyinvoiced'] : 0 ; 
                    if($qtyInvoiced > 0 && !in_array($rsDetail[$i]['pkey'], $arrDetailKey)){  // Cek utk detailkey ada yg gk ad di table DB gk
                        // insert ulang
                        array_push($arrParam['hidDetailCostKey'], $rsDetail[$i]['pkey']); 
                        array_push($arrParam['hidItemKeyCost'], $rsDetail[$i]['costkey']); 
                        array_push($arrParam['qtyCost'], $rsDetail[$i]['qty']);
                        array_push($arrParam['priceCost'], $rsDetail[$i]['price']);
                        array_push($arrParam['subtotalCost'], $rsDetail[$i]['subtotal']);
                         
                        //array_push($arrParam['refkey'], $pkey);   
                    } 
                 }
                 
 		         // ====== RESTORE ULANG SEMUA BIAYA TAMBAHAN YG SUDAH KEEDIT / KEDELETE  
                if($rsHeader[0]['statuskey'] >= 2){  
                    $rsSalesHeaderCost = $this->getHeaderCost($pkey,' and '.$this->tableHeaderCost.'.refcashoutkey <> 0');  
                    
                    $arrHeaderCostKey = $arrParam['hidAdditionalKey'];
                    $this->retrieveReadonlyDataRow($arrParam, $rsSalesHeaderCost,$this->arrHeaderCost,'refcashoutkey', 'hidAdditionalKey' ); 
             
                
                    $rsSalesHeaderCost = $this->getHeaderCost($pkey,' and '.$this->tableHeaderCost.'.refrequestkey <> 0');  
                    
                    $arrHeaderCostKey = $arrParam['hidAdditionalKey'];
                    $this->retrieveReadonlyDataRow($arrParam, $rsSalesHeaderCost,$this->arrHeaderCost,'refrequestkey', 'hidAdditionalKey' ); 
             
                } 


            }
        
            // hanya berlaku jika ad harga kontrak
            // harusnya hanya kepanggil kalo add atau statuskey = 1  
            if (empty($rsHeader) || $rsHeader[0]['statuskey'] == 1 ){  
                if(isset($arrParam['hidContractKey']) && !empty($arrParam['hidContractKey'])){
                        $overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject,10); 
 
                        $rsContract = $truckingSellingRate->getDataRowById($arrParam['hidContractKey']);   
                        $arrParam = $this->checkContract($arrParam,$rsContract);  
                    
                        // hitung ulang subtotal  
                        if(empty($rsContract)) 
                            $modifieddate = '0000-00-00';
                        else 
                            $modifieddate = (!empty($rsContract[0]['modifiedon'])) ? $rsContract[0]['modifiedon'] : $rsContract[0]['createdon'];  
                        $arrParam['hidContractLastModifiedOn'] = $modifieddate;



                        $rsDetail = array();
                        if(isset($arrParam['hidId']) && !empty($arrParam['hidId'])){  
                            $rsDetail = $this->getDetailById($arrParam['hidId']);
                            $rsDetail = array_column($rsDetail,null,'pkey');
                        }                           

                        $rsContractDetail = $truckingSellingRate->getDetailById($arrParam['hidContractKey']);   
                        $rsContractDetail = array_column($rsContractDetail,null,'itemkey');

                        $arrItemKey = $arrParam['hidItemKey']; 
                        for ($i=0;$i<count($arrItemKey);$i++){
                            $itemkey = $arrItemKey[$i];
                            //$this->setLog($itemkey);

                            $arrParam['contractPrice'][$i] = 0;

                            if($overwriteContractAllowed){
                                // kalo punya akses, overwrite
                                $arrParam['contractPrice'][$i] = $arrParam['price'][$i];
                            }else{

                                // kalo gk punya akses,selalu ambil dr kontrak, kecuali sudah pernah diudapte oleh yg punya akses
                                // harusnya hanya terjadi ketika edit

                                // kalo pernah diupdate dr yg punya akses, tetep pake harga yg diupdate
                                $detailkey = $arrParam['hidDetailKey'][$i];
                                if( isset($rsDetail[$detailkey]) &&  $rsDetail[$detailkey]['contractpriceinunit'] > 0){
                                    // harga tetep patokan dr yg supervisor 
                                    $arrParam['price'][$i] = $rsDetail[$detailkey]['contractpriceinunit'];
                                    $arrParam['contractPrice'][$i] = $arrParam['price'][$i];
                                }else{
                                    //update harga dr kontrak 
                                    $arrParam['price'][$i] = (isset($rsContractDetail[$itemkey])) ?  $rsContractDetail[$itemkey]['price'] : 0; 
                                }


                            }
                        }

                }
            }
        
        	// hitung ulang subtotal 
            $reCountResult = $this->reCountSubtotal($arrParam);  
            $arrParam['subtotal'] = $reCountResult['subtotal'];
            $arrParam['grandtotal'] = $reCountResult['grandtotal'];
            $arrParam['totalHeaderCost'] = $reCountResult['totalHeaderCost'];
            $arrParam['totalSellingCost'] = $reCountResult['totalSellingCost']; 
        
        
            //$this->setLog('recount',true);
            //$this->setLog($reCountResult,true);
             
            // INI PERLU DICEK ULANG KALO PAKE API GK KIRIM priceHeaderCost
            // realisasi biaya
            if($this->useRealization()) 
                // priceCost gk boleh diganti, karena nanti ad update dr realisasi
                unset($this->arrHeaderCost['amount']);
            else if (isset($arrParam['requestPriceHeaderCost']))
               // kalo gk pake realisasi, copy semua
               $arrParam['priceHeaderCost'] = $arrParam['requestPriceHeaderCost'];
              
        
            // RECALCULATE   
            $arrItemKey = array();
            if(isset($arrParam['hidItemKey'])){ 
                $arrItemKey = $arrParam['hidItemKey'];
                $arrQty = $arrParam['qty'];
                $arrPrice = $arrParam['price'];

                for($i=0;$i<count($arrItemKey);$i++){ 

                    $qty = (empty($arrQty[$i])) ? 1 : $this->unformatNumber($arrQty[$i]);
                    $priceInUnit =  $this->unFormatNumber($arrPrice[$i]);   
                    $subtotal = $priceInUnit;
                    $total = $qty * $subtotal;

                    $arrParam['qty'][$i] = $qty;
                    $arrParam['numberkey'][$i] = ($i+1);      
                    $arrParam['totalDetails'][$i] =  $total;  
                }
            } 
        
            if(isset($arrParam['hidItemKeyCost'])){  
                $arrItemCost =  $arrParam['hidItemKeyCost'];
                $arrCostQty = $arrParam['qtyCost'];
                $arrCostPrice = $arrParam['priceCost']; 

                for($i=0;$i<count($arrItemCost);$i++) {
                    $qty = (empty($arrCostQty[$i])) ? 1 : $this->unformatNumber($arrCostQty[$i]); 
                    $price = $this->unformatNumber($arrCostPrice[$i]);
                    $arrParam['qtyCost'][$i] = $qty;
                    $arrParam['subtotalCost'][$i] = $qty * $price;
                }
            }
        
            if(isset($arrParam['hidItemKeyHeaderCost'])){  
                $arrItemHeaderCost =  $arrParam['hidItemKeyHeaderCost'];
                $arrItemHeaderCostQty = $arrParam['qtyHeaderCost'];  
 
                $arrItemHeaderRequestAmount = $arrParam['requestPriceHeaderCost'];
                
                $arrLocationCriteria = array();
                $arrLocationCriteria['terminalkey'] = (isset($arrParam['hidTerminalKey']) && !empty($arrParam['hidTerminalKey'])) ? $arrParam['hidTerminalKey'] : ''; 
                $arrLocationCriteria['depotkey'] = (isset($arrParam['hidDepotKey']) && !empty($arrParam['hidDepotKey'])) ? $arrParam['hidDepotKey'] : ''; 
                $arrLocationCriteria['jobcategorykey'] = (isset($arrParam['hidCategoryKey']) && !empty($arrParam['hidCategoryKey'])) ? $arrParam['hidCategoryKey'] : ''; 
                
				
                for($i=0;$i<count($arrItemHeaderCost);$i++){  
                    $requestAmount = $this->unformatNumber($arrItemHeaderRequestAmount[$i]); 
                    if ($requestAmount == 0) {  
                        // set criteria service dan qty
                        $arrLocationCriteria['servicedetail'] = array();
                        
                        for($j=0;$j<count($arrItemKey);$j++){  
                            $qty = (empty($arrQty[$j])) ? 1 : $this->unformatNumber($arrQty[$j]); 
                            array_push($arrLocationCriteria['servicedetail'], array('qty' => $qty, 'servicekey' => $arrItemKey[$j] )); 
                        }
  
                        $rsTruckingCost = $item->getTruckingCostDefaultPrice($arrItemHeaderCost[$i], $arrLocationCriteria); 
                        $arrParam['requestPriceHeaderCost'][$i]= $rsTruckingCost['amount']; 
                    }; 

                    $qty = (empty($arrItemHeaderCostQty[$i])) ? 1 : $arrItemHeaderCostQty[$i];  
                    $priceInUnit = $this->getValidHeaderCost($arrParam,$i);

                    $total = $qty * $priceInUnit;

                    $arrParam['qtyHeaderCost'][$i] = $qty; 
                    $arrParam['subtotalHeaderCost'][$i] =  $total;
                }
            }
           
           if(isset($arrParam['hidContactPersonDetailKey'])){ 
                for($i=0;$i<count($arrParam['hidContactPersonDetailKey']);$i++) 
                        $arrParam['reftable'][$i] = $this->tableName; 
            }
        
            $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }
               
    function getValidHeaderCost($arrParam, $index){
		
		
        $arrItemRequestHeaderCostPrice = (isset($arrParam['requestPriceHeaderCost'][$index]) && !empty($arrParam['requestPriceHeaderCost'][$index])) ? $arrParam['requestPriceHeaderCost'][$index] : 0;
        //$arrItemHeaderCostPrice = (isset($arrParam['priceHeaderCost'][$index]) && !empty($arrParam['priceHeaderCost'][$index])) ? $arrParam['priceHeaderCost'][$index] : 0;
             
        $arrItemRequestHeaderCostPrice =  $this->unFormatNumber($arrItemRequestHeaderCostPrice);
        //$arrItemHeaderCostPrice =  $this->unFormatNumber($arrItemHeaderCostPrice);
        
		
		// gk boleh 0, karena ad kemungkinan hasil realisasinya 0  
        // $priceInUnit  = ( $arrItemHeaderCostPrice > 0 ) ? $arrItemHeaderCostPrice :  $arrItemRequestHeaderCostPrice;  
		
		// kalo row baru, pasti balikin nilai request nya (gk mungkin sudah ad realisasi) 
		// kalo awalnya gk ad biaya, terus di realisasi ad biaya tambahan, harus dicek
		
		//if(empty($arrParam['requestPriceHeaderCost'][$index])){
		if(empty($arrParam['hidAdditionalKey'][$index])){
			$priceInUnit = $arrItemRequestHeaderCostPrice;
		}else{
			// query dr table berdasarkan pkey nya
			$detailkey = $arrParam['hidAdditionalKey'][$index]; 
			$sql = 'select isrealization,amount from '.$this->tableHeaderCost.' where pkey = ' . $this->oDbCon->paramString($detailkey); 
			$rs =  $this->oDbCon->doQuery($sql);
			$priceInUnit = (empty($rs) || $rs[0]['isrealization'] == 0)	? $arrItemRequestHeaderCostPrice :  $rs[0]['amount'];
		}
		
        return $priceInUnit;
    }
    
    function checkContract($arrParam, $rsContract){ 
        if (empty($rsContract)) return $arrParam;
        
        $security = new Security();
        $overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject,10);
        
        $consignee = new Consignee();
        $truckingSellingRate = new TruckingSellingRate();
        
        if (!$overwriteContractAllowed){ 
            $arrParam ['hidCargoType'] = $rsContract[0]['cargotypekey'];
            $arrParam ['hidCategoryKey'] = $rsContract[0]['categorykey'];
            

            $arrParam ['hidConsigneeKey'] = 0;
            $arrParam ['warehouseName'] = '';
            $arrParam ['hidLocationKey'] = 0;
            $arrParam ['contactPerson'] = '';  

            $rsConsignee = array();
         
            $rsContract = $truckingSellingRate->getDataRowById($rsContract[0]['pkey']);   
            $rsConsignee = $consignee->searchData($consignee->tableName.'.pkey',$rsContract[0]['consigneekey']);

            $arrParam ['hidConsigneeKey'] = $rsConsignee[0]['pkey'];
            $arrParam ['warehouseName'] = $rsConsignee[0]['warehousename'];
            $arrParam ['hidLocationKey'] = $rsConsignee[0]['locationkey'];
            $arrParam ['contactPerson'] = $rsConsignee[0]['contactperson'];
            
        }

        return $arrParam;
        
    } 
    
	function getItemFile($pkey){
		$sql = 'select * from '.$this->tableFile.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql);
    } 
    
    function delete($id,$forceDelete = false,$reason = ''){
		 
		$arrayToJs =  array();
		// tdk bisa didelete utk transaksi, tp ubah ke cancel
		if(isset( $this->tableNameDetail) &&!empty($this->tableNameDetail)){  
             $arrayToJs = $this->changeStatus($id, 7,$reason,false,$forceDelete);  
             return $arrayToJs; 
		} 
		
		try{ 
		
	 		$arrayToJs = $this->validateDelete($id);
			if (!empty($arrayToJs)) 
				return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
			 
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans();
					 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
				 
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}
    
    function updateAmountInvoiced($pkey){
        
        // gk perlu udpate customer AR outstanding karena sudah diupdate di ketika terbentuk AR
        
        $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice(); 
        
        $sql = 'update ' . $this->tableName.' set totalinvoiced = (
                    select coalesce(sum('.$truckingServiceOrderInvoice->tableNameDetail.'.amount),0) as amount
                    from
                        '.$truckingServiceOrderInvoice->tableNameDetail.',
                        '.$truckingServiceOrderInvoice->tableName.'
                    where
                        '.$truckingServiceOrderInvoice->tableName.'.statuskey in (2,3) and 
                        '.$truckingServiceOrderInvoice->tableName.'.pkey =  '.$truckingServiceOrderInvoice->tableNameDetail.'.refkey and
                        '.$truckingServiceOrderInvoice->tableNameDetail.'.salesorderkey = '.$this->oDbCon->paramString($pkey).'
                ) where pkey = '.$this->oDbCon->paramString($pkey);
         
        $this->oDbCon->execute($sql); 
    }
    
    function updateQtyInvoiced($pkey,$isValidated = false){  
        $rsHeader = $this->getDataRowById($pkey);
		
        
        $arrayToJs = array();
        
		// harusnya kalo JO sudah batal, tdk perlu proses lg
		if ($rsHeader[0]['statuskey'] == 7) return $arrayToJs;
		
        $rsItemDetail = $this->getDetailById($pkey);
        $rsHeaderCost = $this->getSellingCostDetail($pkey);
              
        // update setiap SO, sudah brp qty yg ditagih, item dan cost 
        try{
            
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
            
            for($j=0;$j<count($rsItemDetail);$j++){
                if(!$isValidated)
                    $totalInvoiced = $this->getTotalQtyInvoiced($pkey,$rsItemDetail[$j]['pkey'],$rsItemDetail[$j]['itemkey']);
                else
                    $totalInvoiced = $rsItemDetail[$j]['qtyinbaseunit'];


                $sql = 'update 
                            ' . $this->tableNameDetail.'
                        set 
                            qtyinvoiced = '.$this->oDbCon->paramString($totalInvoiced).' 
                        where  
                            pkey = '.$this->oDbCon->paramString($rsItemDetail[$j]['pkey']).' 
                        ';

                //$this->setLog($sql,true);
                $this->oDbCon->execute($sql);

            } 

            for($j=0;$j<count($rsHeaderCost);$j++){

                if(!$isValidated)
                    $totalInvoiced = $this->getTotalQtyInvoiced($pkey,$rsHeaderCost[$j]['pkey'],$rsHeaderCost[$j]['costkey']);
                else
                    $totalInvoiced = $rsHeaderCost[$j]['qty'];

                $sql = 'update 
                            ' . $this->tableSellingCost.'
                        set 
                            qtyinvoiced = '.$this->oDbCon->paramString($totalInvoiced).' 
                        where  
                            pkey = '.$this->oDbCon->paramString($rsHeaderCost[$j]['pkey']).' 
                        ';

                $this->oDbCon->execute($sql);
            }  
            
            $this->oDbCon->endTrans();
             
		
	    }  catch(Exception $e){ 
            $this->oDbCon->rollback(); 
          /*  
            if (!empty($e->getMessage()))
                $this->addErrorLog(false,$e->getMessage()); */
		}		
        
        
        
        // cek utk SO, semua sudah tertagih atau blm. lalu ubah status 
        $sql = 'SELECT * from ( 
                    select  pkey, itemkey from   ' . $this->tableNameDetail.'  where  refkey = '.$this->oDbCon->paramString($pkey).' and  qtyinbaseunit > qtyinvoiced UNION 
                    select  pkey, costkey as itemkey from   ' . $this->tableSellingCost.'  where  refkey = '.$this->oDbCon->paramString($pkey).' and  qty > qtyinvoiced 
                ) trans ';
         
        //$this->setLog($sql,true);
        $rs =  $this->oDbCon->doQuery($sql);
        
        if (empty($rs)) { 
            
            // hanya close jika semua SPK sudah closed jg
            $arrSPKClosed = $this->validateSPKClosed($pkey);
            $arrCashOutClosed = $this->validateTruckingCashOutConfirmed($pkey);   
          
            if($rsHeader[0]['statuskey'] <> 6 && empty($arrSPKClosed) && empty($arrCashOutClosed)){ 
                $arrayToJs = $this->changeStatus($pkey,6,'',false,true);
            }
            
        }else{ 
            if ($rsHeader[0]['statuskey'] == 6) 
                $arrayToJs = $this->changeStatus($pkey,5,'',false,true);
        }
        
        return $arrayToJs;
           
    }
     
     
    function getTotalQtyInvoiced($pkey,$detailkey, $itemkey){ 
        // tambahkan paramter itemkey untuk membedakan dr detail atau selling cost
        // dengan ada item key sudah pasti beda karena detail item dan item cost 1 table, jd pkey pasti beda
        // kenapa $itemkeyny jd gk kepake ??
        
            $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice(); 
        
         // update setiap SO, sudah brp qty yg ditagih, item dan cost
            $sql = 'select 
                        coalesce(sum(qtyinbaseunit),0) as totalinvoiced
                    from  
                        '.$truckingServiceOrderInvoice->tableName.',  
                        '.$truckingServiceOrderInvoice->tableNameDetail.',
                        '.$truckingServiceOrderInvoice->tableNameItemDetail.' 
                    where 
                        '.$truckingServiceOrderInvoice->tableName.'.pkey = '.$truckingServiceOrderInvoice->tableNameDetail.'.refkey and
                        '.$truckingServiceOrderInvoice->tableNameDetail.'.pkey = '.$truckingServiceOrderInvoice->tableNameItemDetail.'.refkey and
                        '.$truckingServiceOrderInvoice->tableName.'.statuskey in (2,3) and
                        '.$truckingServiceOrderInvoice->tableNameDetail.'.salesorderkey = '.$this->oDbCon->paramString($pkey).' and
                        '.$truckingServiceOrderInvoice->tableNameItemDetail.'.refsodetailkey = '.$this->oDbCon->paramString($detailkey).' and
                        '.$truckingServiceOrderInvoice->tableNameItemDetail.'.itemkey =  '.$this->oDbCon->paramString($itemkey).' 
                    ';
  
            //$this->setLog($sql.true);
            $rsTotal = $this->oDbCon->doQuery($sql);
         
            return $rsTotal[0]['totalinvoiced'];
    }
    
    function manipulateParamFromApi($arrParam){
        $arrParam['priceHeaderCost'] = array_fill(0, count($arrParam['priceHeaderCost']), '');
        return $arrParam;
    }
    
    function afterUpdateData($arrParam, $action){
        $customer = new Customer();
        
        // khusus kalo edit
        if(isset($arrParam['hidId']) && !empty($arrParam['hidId'])){ 
             
             $pkey = $arrParam['hidId'];  
             $rs = $this->getDataRowById($pkey); 

            // CASH OUT   
            $this->updateTruckingCostCashOut($pkey); 
 
            // cuma boleh di status proses SPK, di menungggu gk perlu
            if ( $rs[0]['statuskey'] == 2){  
                // perlu tambah add cost rate jg  
                $this->updateCostRate($arrParam['hidId']);     
                $this->autoAddWorkOrder($arrParam['hidId']);    
                
            }
             
        } 
        
        // harusnya cuma dr API 
        
        if(isset($arrParam['_mnv-api']) && $arrParam['_mnv-api'] == 1){ 
            
            $arrayToJs = array();
            
            if(isset($arrParam['changestatusto'])){
                //kalo otomatis ganti status
                $newStatus = $arrParam['changestatusto'];

                // kalo lebih besar dari konfirmasi, konfirmasi dulu
                if (in_array($newStatus,array(2,3,4,5))){ 

                    // iterasi satu2 saja, karena tanggung 2 dan 3 gk boleh diskip
                    // cari dulu status skrg, jaga2 kalo dr edit
                    $pkey = $arrParam['pkey'];
                    $rs = $this->getDataRowById($pkey); 
                    $startStatus = $rs[0]['statuskey'] + 1;

                    for($i=$startStatus;$i<=$newStatus;$i++){
                        $response = $this->changeStatus($pkey,$i); 
                        if($response[0]['valid'] <> 1) {
                            $response[0]['message'] = $i.'=>'.$response[0]['message'];
                            array_push($arrayToJs,$response[0]);
                        }
                    } 

                } 
            }
           
            
            return $arrayToJs; // sementara di return hanya API saja dulu
            
        }
        
        $customer->updateAROutstanding($arrParam['hidCustomerKey']);

    }
    
	function changeStatus($id,$status,$reason='',$copy=false, $autoChangeStatus = false, $ignoreValidation = false){
		
	    if (empty($_SESSION[$this->loginAdminSession]['id']))
            die;
          
        $rsHeader = $this->getDataRowById($id);
         
      	try{ 
            if(!$autoChangeStatus){  
                $security = new Security();
                if(!$security->isAdminLogin($this->securityObject,$status,false))  
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[252],true);   
            }

            // jika status bkn status sendiri dan bukan status terakhir (status cancel)  
            
            if ($rsHeader[0]['statuskey'] == count($this->getAllStatus())) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[221],true);   
    
            if ($rsHeader[0]['statuskey'] == $status) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[224],true);   
   
        }catch(Exception $e){ 
 		     return $this->getErrorLog(); 
			//$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
        
         
		try{
              
            // ================== VALIDATION
            
            //$this->resetErrorLog();
		   
		  	switch ($status){
				case 1 : $this->validateInput($rsHeader); 
						  break;
				case 2 : if ($rsHeader[0]['statuskey'] < $status )
                            $this->validateConfirm($rsHeader, $autoChangeStatus);
                         else
                            $this->validateBackConfirm($rsHeader); 
						  break;
				case 3 :  $this->validateSPKCompleted($rsHeader, $autoChangeStatus); 
						  break;
				case 4 :  $this->validateReadyToCheck($rsHeader, $autoChangeStatus); 
						  break;
				case 5 : if ($rsHeader[0]['statuskey'] < $status )
                            $this->validateReadyToInvoice($rsHeader, $autoChangeStatus); 
                         else
                            $this->validateBackReadyToInvoice($rsHeader); 
						  break; 
				case 6 :  $this->validateInvoiced($rsHeader, $autoChangeStatus); 
						  break; 
				case 7 :  $this->validateCancel($rsHeader, $autoChangeStatus); 
						  break; 
			} 
			 
            //make sure we throw error 
            $this->throwIfHasErrorLog();  
            
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
					 
            
			switch ($status){ 
				case 2 : if ($rsHeader[0]['statuskey'] < $status ){ 
                            $this->confirmTrans($rsHeader); 
                            $this->afterConfirmTrans($rsHeader);
                        }else{ 
                            $this->backConfirmTrans($rsHeader); 
                            $this->afterBackConfirmTrans($rsHeader);
                        }
                         break; 
				case 7 : $this->cancelTrans($rsHeader,$copy);
                          $this->afterCancelTrans($rsHeader);
                          break;  
			}
			
			$sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id);
			$this->oDbCon->execute($sql);
			
            $rsStatus = $this->getStatusById ($status); 
            $this->setTransactionLog($rsStatus[0]['pkey'],$id,'',$reason);
                 
            $this->afterStatusChanged($rsHeader);
            
            $this->oDbCon->endTrans();
            
            
			$this->addErrorLog(true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    }  catch(Exception $e){ 
            $this->oDbCon->rollback(); 
            
            if (!empty($e->getMessage()))
                $this->addErrorLog(false,$e->getMessage());
			//$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 
        
        return $this->getErrorLog(); 
 	}
    
    function validateBackConfirm($rsHeader){ 
 
        if($rsHeader[0]['statuskey'] >=6 )   
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201]); 
       
	 }
     
	function validateConfirm($rsHeader, $autoChangeStatus = false){ 
		    
        if ($autoChangeStatus)
	 	     return; 
		   
        //validasi creditlimit
        
        $customer = new Customer(); 
        $security = new Security();
        
        $customerkey = $rsHeader[0]['customerkey'];
        $rsCustomer = $customer->getDataRowById($customerkey);
 
        if ($rsCustomer[0]['creditlimit'] > 0){  
            $hasCreditLimitAccess = $security->isAdminLogin($customer->creditLimitSecurityObject,10);   
            $total = $this->unFormatNumber($rsHeader[0]['grandtotal']);       
            if (!$hasCreditLimitAccess && $customer->willExceedCreditLimit($customerkey,$total)){
                 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['creditlimit'][1]);
            }
        } 
		
        $security = new Security();
        $overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject,10);
        
        $truckingSellingRate = new TruckingSellingRate();
           
        if (!$overwriteContractAllowed){ 
            $rsTariff = $truckingSellingRate->getDataRowById($rsHeader[0]['contractkey']);   
            $timestamp = (empty($rsTariff[0]['modifiedon'])) ? $rsTariff[0]['createdon'] : $rsTariff[0]['modifiedon']; 
            
            if ($timestamp <> $rsHeader[0]['tarifflastmodifiedon']) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg['sellingRate'][4] .  ' '. $this->lang['pleaseReopenAndSaveTheData']); 
        }
        
        $costRateIsMandatory = $this->loadSetting('costRateIsMandatory');
        if ($costRateIsMandatory == 1) { 
            $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
            $return = $this->validateFixedCostMustExist($rsHeader[0], array_column($rsDetail,'itemkey')); 
        }
        
        foreach($return as $row)
               $this->addErrorLog(false,$row['message']); 
        
	 }
    
    function validateFixedCostMustExist($header, $arrServiceKey){
        
        // header : code,warehousekey,categorykey,stuffinglocationkey,cargotypekey,consigneekey
        
        $truckingServiceOrderCategory = new TruckingServiceOrderCategory(); 
        $costRate = new CostRate();
        $truckingService = new Service();  
        $truckingCost = new Service(TRUCKING_SERVICE,1);   
        
        $arrayToJs = array();
        
         // kalo wajib ad harga
         
        $rsHeader = $this->getDataRowById($pkey);
        //$rsDetail = $this->getDetailById($pkey);
          
        $rsJobType = $truckingServiceOrderCategory->getDetailById($header['categorykey']);
        $warehousekey = $header['warehousekey'];
         
        //$arrServiceKey = array_column($rsDetail,'itemkey');        
        
        // cari nama item
        $rsServices = $truckingService->searchDataRow(array($truckingService->tableName.'.pkey',$truckingService->tableName.'.name'),
                                                   ' and '.$truckingService->tableName.'.pkey in ('.$this->oDbCon->paramString($arrServiceKey,',').')'
                                                   ); 
        $arrServices = array_column($rsServices,'name','pkey');                                    
        
        
        // cari dulu semua item fixed cost 
        $rsFixedCost = $truckingCost->searchDataRow(array($truckingCost->tableName.'.pkey',$truckingCost->tableName.'.name'),
                                                   ' and '.$truckingCost->tableName.'.fixedcost = 1  and isdroppointdetailprice = 0 and '.$truckingCost->tableName.'.statuskey = 1'
                                                   ); 
        $arrFixedCost = array_column($rsFixedCost,'name','pkey');                                    
                
        
        
        // cek setiap item ad gk daftar biayanya
        
        foreach($arrServiceKey as $servicekey){ // utk setiap mobil, 20', 40'
              foreach($rsJobType as $jobType){   // utk setiap jenis pekerjaan
                 
                    // ambil biaya sesuai jenis pekerjaan
                    $rsCostRate = $costRate->getCostDetail( $warehousekey,$header['stuffinglocationkey'], $header['cargotypekey'], $jobType['jobtypekey'], $servicekey,0,$header['consigneekey']);
                    if (empty($rsCostRate)){ 
                        $this->addErrorList($arrayToJs, false, '<strong>'.$header['code'] . '</strong>. '.$arrServices[$servicekey].', '.$this->errorMsg['costRate'][1], true); 
                    }else{
                        $registeredCostKey = array_column($rsCostRate,'costkey');   
                        foreach($arrFixedCost as $fixedCostKey=>$fixedCostItem){
                            if(!in_array($fixedCostKey, $registeredCostKey))
                                 $this->addErrorList($arrayToJs, false, '<strong>'.$header['code'] . '</strong>. '.$arrServices[$servicekey]. ' - '.$fixedCostItem.', '.$this->errorMsg['costRate'][1], true); 
                        }
                       
                    }  
             } 

        }
        
        return $arrayToJs;
    }
    
	function confirmTrans($rsHeader){  
        $id = $rsHeader[0]['pkey']; 
        
        $rsDetail = $this->getDetailById($id);   
	    $rsSalesHeaderCost = $this->getHeaderCost($rsHeader[0]['pkey'],' and '.$this->tableHeaderCost.'.realizationkey = 0');
        
        $this->updateCostRate('',$rsHeader,$rsDetail);  
        $this->autoAddWorkOrder('',$rsHeader,$rsDetail); 
        //$this->addCashOut($rsHeader,$rsSalesHeaderCost); 
	} 
    
    function updateCostRate($id='',$rsHeader='',$rsDetail='' ){
         
        $truckingServiceOrderCategory = new TruckingServiceOrderCategory(); 
        $costRate = new CostRate();
        $service = new Service();  
         
        if (!empty($id)){
            $rsHeader = $this->getDataRowById($id);
            $rsDetail = $this->getDetailById($id);   
        }
        
        $id = $rsHeader[0]['pkey']; 
        $warehousekey = $rsHeader[0]['warehousekey'];
        
        
        // select service yg blm ad di table cost saja...
        
        //$arrService = $service->searchData('', '', true, ' and '.$service->tableName.'.statuskey = 1 order by '.$service->tableName.'.name asc');
        $arrServiceKey = array_column($rsDetail,'itemkey'); 
        
        $existingRate = $this->getCostDetail($id);
        $existingRate = array_column($existingRate,'itemkey');
        
        
        //UPDATE COST
        $rsJobType = $truckingServiceOrderCategory->getDetailById($rsHeader[0]['categorykey']);
        
        foreach($arrServiceKey as $servicekey){ 
                // kalo sudah ad rate nya, gk perlu add lg
                if (in_array($servicekey,$existingRate )) continue;
            
               for($j=0;$j<count($rsJobType);$j++){

                // ambil biaya sesuai jenis pekerjaan
                $rsCostRate = $costRate->getCostDetail( $warehousekey,$rsHeader[0]['stuffinglocationkey'], $rsHeader[0]['cargotypekey'], $rsJobType[$j]['jobtypekey'], $servicekey,0,$rsHeader[0]['consigneekey']);
                   
                // klao ad nilai kosong, throe    
                if (empty($rsCostRate)) continue;

                for ($ctr=0;$ctr<count($rsCostRate);$ctr++){
                    $costkey = $rsCostRate[$ctr]['costkey'];
                    $cost = $rsCostRate[$ctr]['price'];

                    $sql = 'insert into '.$this->tableCost.' (
                            refkey, 
                            jobtypekey,
                            costkey,
                            itemkey,
                            price
                         ) values ( 
                            '.$this->oDbCon->paramString($id).', 
                            '.$this->oDbCon->paramString($rsCostRate[$ctr]['jobtypekey']).',
                            '.$this->oDbCon->paramString($costkey).',
                            '.$this->oDbCon->paramString($rsCostRate[$ctr]['itemkey']).',
                            '.$this->oDbCon->paramString($this->unFormatNumber($cost)).' 
                        )';	 
                         
                    $this->oDbCon->execute($sql); 
                }

            }
        }
     
    }
    
    function autoAddWorkOrder($id = '', $rsHeader = '',$rsDetail = ''){ 
        
        // kalo gk ad SPK, skip aj,
        // gk bisa disamakan dengan truckingType jg
        
        $useSPK = $this->loadSetting('useSPK');
		
        if ($useSPK == 2) return;
        
        $truckingType = $this->loadSetting('truckingType');
        $spkDateBasedOn = $this->loadSetting('spkDateBasedOn');
            
        $truckingServiceOrderCategory = new TruckingServiceOrderCategory(); 
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder(); 
        
        if (!empty($id)){
            $rsHeader = $this->getDataRowById($id);
            $rsDetail = $this->getDetailById($id);   
        }
        
        //$this->setLog($rsDetail,true);
        
        $id = $rsHeader[0]['pkey'];  
         
        $user = base64_decode($_SESSION[$this->loginAdminSession]['id']);
         
        // jml SPK setiap detail tergantung brp byk step progress. 
        $rsJobType = $truckingServiceOrderCategory->getDetailWithRelatedInformation($rsHeader[0]['categorykey']);

         
        // =====  hapus semua SPK yang sudah gk ad di JO dan status SPK masih MENUNGGU
        // ini perlu VALIDASI
             
        if ( $rsHeader[0]['statuskey'] == 2){ 
             
            //$rsWO = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.statuskey',TRANSACTION_STATUS['menunggu'],true,' and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($id));
            $rsWO = $truckingServiceWorkOrder->searchDataRow( array( $truckingServiceWorkOrder->tableName.'.pkey', 
                                                                     $truckingServiceWorkOrder->tableName.'.refdetailkey',
                                                                     $truckingServiceWorkOrder->tableName.'.itemkey'
                                                                   ) , 
                                                                '   and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($id).'
                                                                    and '.$truckingServiceWorkOrder->tableName.'.statuskey = ' . $this->oDbCon->paramString(TRANSACTION_STATUS['menunggu'])  
                                                            );
               

            foreach($rsWO as $workOrder){   
                // jika detail dan itemnya sudah gk sama 
                foreach($rsDetail as $detailRow) { 
                    if ($workOrder['refdetailkey'] == $detailRow['pkey'] && $workOrder['itemkey'] != $detailRow['itemkey'] ){ 
                        $arrayToJs = $truckingServiceWorkOrder->changeStatus($workOrder['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
                        if (!$arrayToJs[0]['valid'])
                            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
                    } 
                } 
            }
               
        }  
            
        
        // =====  hapus semua SPK yang sudah gk ad di JO dan status SPK masih MENUNGGU 
         
        for($i=0;$i<count($rsDetail);$i++){
                
                // kalo SPK sudah ad satu saja, utk layanan yg sama, continue....
            
                if ( $rsHeader[0]['statuskey'] == 2){ 
                
                    //$rsWODetail = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey',$id,true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')' );
                    $rsWODetail = $truckingServiceWorkOrder->searchDataRow( array($truckingServiceWorkOrder->tableName.'.itemkey') , 
                                                                    '   and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($id).'
                                                                        and '.$truckingServiceWorkOrder->tableName.'.statuskey in  ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
                                                                );
               

                    $rsWODetail = array_column($rsWODetail,'itemkey'); 
                    if (in_array($rsDetail[$i]['itemkey'], $rsWODetail)) continue; 
                }
 
             
                for($k=0;$k<count($rsJobType);$k++){
                     
                    $progresskey = ($k + 1);
                    $arrParam = array();	
                    
                    $spkDate = ($spkDateBasedOn == 2) ?  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y H:i') : date('d / m / Y');

                    // utk komisi sopir 
                    
                    $rsDriverCommission = $this->getDriverCommissionRate($id, $rsJobType[$k]['jobtypekey'],  $rsDetail[$i]['itemkey']);  
                    $rsDriverCommission = array_column($rsDriverCommission,'price', 'costkey');
                     
                    
                    $arrParam['code'] = 'xxxxxx'; 
                    $arrParam['hidSOKey'] = $id;
                    $arrParam['hidSODetailKey'] = $rsDetail[$i]['pkey']; 
                    $arrParam['hidItemKey'] = $rsDetail[$i]['itemkey'];
                    $arrParam['trDate'] =  ($k==0) ? $spkDate  : '00 / 00 / 0000'; 
                    $arrParam['trDateStuffing'] =  ($k==0) ? $this->formatDBDate($rsDetail[$i]['trdate'],'d / m / Y H:i') : '00 / 00 / 0000'; 
                    $arrParam['trDesc'] = htmlspecialchars_decode($rsDetail[$i]['trdesc']); 
                    $arrParam['aju'] = $rsHeader[0]['aju'];
                    $arrParam['hidDepotKey'] = $rsHeader[0]['depotkey']; 
                    $arrParam['hidTerminalKey'] = $rsHeader[0]['terminalkey']; 
                    $arrParam['routeFrom'] = $rsHeader[0]['routefrom']; 
                    $arrParam['routeTo'] = $rsHeader[0]['routeto']; 
                    $arrParam['hidPlannerKey'] = $rsHeader[0]['plannerkey'];
                    $arrParam['hidCargoTypeKey'] = $rsHeader[0]['cargotypekey'];
                    $arrParam['selJobType'] = $rsJobType[$k]['jobtypekey'];
                    $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
                    $arrParam['stuffingAddress'] = $rsHeader[0]['stuffingaddress'];
                    $arrParam['driverCommission'] = (isset($rsDriverCommission[-1])) ? $rsDriverCommission[-1] : 0;
                    $arrParam['codriverCommission'] = (isset($rsDriverCommission[-2])) ? $rsDriverCommission[-2] : 0;
                    $arrParam['islinked'] = true;  
                    $arrParam['createdBy'] = $user; 
                    $arrParam['_mnv'] = true; 

                    //cost  
                    $rsCost = $this->getCostDetail($id, $rsJobType[$k]['jobtypekey'],  $rsDetail[$i]['itemkey'] );  
                    
					
                    $arrCostKey = array();
                    $arrCost = array();
                    $arrParam['hidDetailKey'] = array();
                    $arrParam['hidRefCashOutKey'] = array();
                    for($j=0;$j<count($rsCost);$j++){   
                         
					// bentrok dengan kargo nya ETI, karena qty.
					// harusnya aman karena blm ad yg pake group
//                        if($rsDetail[$i]['isgroup']==1) 
//                          $rsCost[$j]['price'] *= $rsDetail[$i]['qtyinbaseunit'];
                        
                        array_push($arrParam['hidDetailKey'], 0);
                        array_push($arrParam['hidRefCashOutKey'], 0);
                        
                        array_push($arrCost,$rsCost[$j]['price']);   
                        array_push($arrCostKey,$rsCost[$j]['costkey']);    
                    }
                    
                    $arrParam['hidCostKey']  = $arrCostKey;
                    $arrParam['requestAmount']  = $arrCost;
			    
                    //driver progress
                    if($this->isActiveModule('jobprogress')) {

                        $jobProgress = new JobProgress();

                        $arrParam['hidJobProgressDetailKey'] = array();
                        $arrParam['jobProgressNumber'] = array();
                        $arrParam['hidJobProgressKey'] = array();
                        $arrParam['hidJobProgressHeaderKey'] = array();
                        $arrParam['trDateCompleted'] = array();
                        $arrParam['hidIsCompleted'] = array();

                        $rsJobProgress = $jobProgress->getJobProgressByCategory($rsHeader[0]['categorykey']);
                    
                        for($d=0; $d<count($rsJobProgress); $d++) {
                            array_push($arrParam['hidJobProgressDetailKey'], 0);
                            array_push($arrParam['jobProgressNumber'], $rsJobProgress[$d]['number']);
                            array_push($arrParam['hidJobProgressKey'], $rsJobProgress[$d]['pkey']);
                            array_push($arrParam['hidJobProgressHeaderKey'], $rsJobProgress[$d]['refkey']);
                            array_push($arrParam['trDateCompleted'], $this->formatDBDate($rsDetail[$i]['trdate'],'d / m / Y H:i'));
                            array_push($arrParam['hidIsCompleted'], 0);
                        }
                    
                    }

					                   
                    // utk model bisnis seperti logol
                    $qtyWO = $rsDetail[$i]['qtyinbaseunit'];
                    if ($truckingType == 2){
                         $qtyWO = 0; // SPK dr API
                        /*$arrParam['chkIsOutsource'] = 1;
                        
                        $arrParam['hidOutsourceVehicleDetailKey'] = array();
                        $arrParam['qtyDetail'] = array();
                        $arrParam['hidServiceDetailKey'] = array();
                        
                        $totalQtyDetail = 1;
                        if($rsDetail[$i]['isgroup']==1){ // kalo group, SPKnya satu saja
                            $totalQtyDetail = $qtyWO;
                            $qtyWO = 1;
                        } 
                    
                        for($totalQty = 0; $totalQty < $totalQtyDetail ; $totalQty++ ){ 
                            array_push($arrParam['hidOutsourceVehicleDetailKey'],0);
                            array_push($arrParam['qtyDetail'],1); 
                            array_push($arrParam['hidServiceDetailKey'],$rsDetail[$i]['itemkey']);  
                        }*/
                        
                     
                    }else{
                       if($rsDetail[$i]['isgroup']==1) 
                           $qtyWO= 1;
                    }
  

                     
                    for($z=0;$z<$qtyWO;$z++){                        
                         $arrayToJs = $truckingServiceWorkOrder->addData($arrParam);  
                
                        if (!$arrayToJs[0]['valid'])
                            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    
                    }
                    
            } 
             
        }
         
    }
     
    function validateCancel($rsHeader,$autoChangeStatus=false){  
      
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
        $truckingCostCashOut = new TruckingCostCashOut();
        
		$pkey = $rsHeader[0]['pkey'];
		   
		if($rsHeader[0]['statuskey'] == 7)  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201]); 
         
        // cek SPK sudah ad yg konfirmasi / closed blm
        $rsWO = $truckingServiceWorkOrder->searchData('','',true,' and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and '. $truckingServiceWorkOrder->tableName.'.statuskey in (2,3)');
        if (!empty($rsWO)) 
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsWO[0]['code'].'</strong>, ' .$this->errorMsg[225] );
 
        /*$rsInvoice = $truckingServiceOrderInvoice->searchData('','',true,' and '.$truckingServiceOrderInvoice->tableName.'.refkey =  ' . $this->oDbCon->paramString($pkey) .' and '.$truckingServiceOrderInvoice->tableName.'.statuskey in (2,3)');
        if (!empty($rsInvoice)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg[900].' <strong>'.$rsInvoice[0]['code'].'</strong>');
        */   
	    $rsInvoiced = $this->getInvoiceInformation($pkey);
        if (!empty($rsInvoiced)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg[900].' <strong>'.$rsInvoiced[0]['code'].'</strong>');
            
        $rsCashOutKey =  $this->getTableKeyAndObj($this->tableName,array('key')); 
        $rsCashOut = $truckingCostCashOut->searchData('','',true,' and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and '.$truckingCostCashOut->tableName.'.reftabletype = ' . $this->oDbCon->paramString($rsCashOutKey['key']) .' and '. $truckingCostCashOut->tableName.'.statuskey in (2,3,4)');
        if (!empty($rsCashOut)){
             $errMsg = array();
             foreach($rsCashOut as $cashOutRow)
                 array_push($errMsg,'<b>'.$cashOutRow['code'].'</b>, ' .$this->errorMsg[225]);
            
			 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>.' . $this->errorMsg[201].'<br>'.implode('<br>',$errMsg)); 
        }
		  
	 }
     
    function cancelCashOut($pkey,$employeekey = ''){
        // delete cash out
        $truckingCostCashOut = new TruckingCostCashOut();
        $rsCashOutKey =  $this->getTableKeyAndObj($this->tableName,array('key'));
        $employeeCriteria = ($employeekey !== '') ? ' and '.$truckingCostCashOut->tableName.'.employeekey = '.$this->oDbCon->paramString($employeekey) : '';
 
        $rsCashOut = $truckingCostCashOut->searchData('','',true,' and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' 
                                                                   and '.$truckingCostCashOut->tableName.'.reftabletype = ' . $this->oDbCon->paramString($rsCashOutKey['key']) .'
                                                                   and '.$truckingCostCashOut->tableName.'.statuskey = 1 ' .$employeeCriteria);
         

		for($i=0;$i<count($rsCashOut);$i++) {  
			$arrayToJs = $truckingCostCashOut->changeStatus($rsCashOut[$i]['pkey'],5,'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception($arrayToJs[0]['message']);    
        }

	 }
      
	function cancelTrans($rsHeader,$copy){  
		$service = new Service(); 
  
		$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
		$rsWorkOrder = $truckingServiceWorkOrder->searchData('','',true,' and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$truckingServiceWorkOrder->tableName.'.statuskey = 1');
		for($i=0;$i<count($rsWorkOrder);$i++) 
          $truckingServiceWorkOrder->changeStatus($rsWorkOrder[$i]['pkey'],4,'',false,true); 
         
        // utk inv jgn cancel otomatis, user perlu keep no inv nya, jd kalo gk dicancel, palig gk mereka ngeh ada yg perlu dihapus
/*		$sql = 'select  
            '.$truckingServiceOrderInvoice->tableName.'.pkey
          from 
            '.$truckingServiceOrderInvoice->tableName.',
            '.$truckingServiceOrderInvoice->tableNameDetail.'
          where  
            '. $truckingServiceOrderInvoice->tableNameDetail.'.salesorderkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']) .' and   
            '. $truckingServiceOrderInvoice->tableName.'.pkey = '. $truckingServiceOrderInvoice->tableNameDetail.'.refkey and
            '. $truckingServiceOrderInvoice->tableName.'.statuskey = 1 ';
 
        $rsInvoice = $this->oDbCon->doQuery($sql);
	 
        for($i=0;$i<count($rsInvoice);$i++) 
            $truckingServiceOrderInvoice->changeStatus($rsInvoice[$i]['pkey'],4,'',false,true);*/

       	 $this->cancelCashOut($rsHeader[0]['pkey']);
        
		if ($copy)
			$this->copyDataOnCancel($rsHeader[0]['pkey']);	  
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
	  
	function validateSPKCompleted($rsHeader,$autoChangeStatus){ 
        if ($autoChangeStatus)  return;
        
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
          
		if($rsHeader[0]['statuskey'] <> 2  && $rsHeader[0]['statuskey'] <> 4  &&  $rsHeader[0]['statuskey'] <> 5){   
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201]);
		}else{  
              
            //$rsWorkOrder = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey', $rsHeader[0]['pkey'], true,' and ' . $truckingServiceWorkOrder->tableName.'.statuskey in (1,2)');
            //$rsWorkOrder = $truckingServiceWorkOrder->searchDataRow( array( $truckingServiceWorkOrder->tableName.'.pkey') , 
            //                                                '   and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).'
            //                                                    and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].')'  
            //                                            ); 
            
            $arrSPKClosed = $this->validateSPKClosed($rsHeader[0]['pkey']);
                
         
            if (!empty($arrSPKClosed)){ 
                $spkUnclosedCode = implode(', ',array_column($arrSPKClosed,'code'));
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><b>'.$spkUnclosedCode.'</b>. '.$this->errorMsg['truckingServiceWorkOrder'][2]);
            }else{ 
                 // posisi ini, sudah pasti gk ad SPK yg menunggu atau konfirmasi
                 // model logol
                 $truckingType = $this->loadSetting('truckingType');
                 $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
                 $totalQty = 0;
                 foreach($rsDetail as $row)
                     $totalQty += $row['qtyinbaseunit'];
                
                 if($truckingType == 2){ 
                     // gagal jika
                     // 1. ada spk yg blm selesai (sudah dihandle di validasi biasa)
                     // 2. jml yg selesai lebih kecil dr partai

                        $sql = 'select 
                                    sum('.$truckingServiceWorkOrder->tableWorkOrderCarDetail.'.qty) as qty  
                                from
                                    '.$truckingServiceWorkOrder->tableName.',
                                    '.$truckingServiceWorkOrder->tableWorkOrderCarDetail.'
                                where
                                    '.$truckingServiceWorkOrder->tableWorkOrderCarDetail.'.refkey = '.$truckingServiceWorkOrder->tableName.'.pkey and
                                    '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and 
                                    '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['selesai'].') 
                                ';
                     
                        $rs = $this->oDbCon->doQuery($sql);  
                        if($rs[0]['qty'] < $totalQty) 
                              $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' '.$this->errorMsg['truckingServiceOrder'][4]);
                       
                 }
                
            }
                 
                 
		} 
		 
	 } 
    
	function validateReadyToCheck($rsHeader,$autoChangeStatus){ 
		   
        if ($autoChangeStatus)
	 	 return; 
         
		if($rsHeader[0]['statuskey'] <> 3 &&  $rsHeader[0]['statuskey'] <> 5 ){   
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201]);
		} 
        
        // harusnay aman kalo ke block pas mau ubah status ke siap diinvoice
        //$arrSPKClosed = $this->validateSPKClosed($rsHeader[0]['pkey']);   
        // if (!empty($arrSPKClosed)){ 
        //    $spkUnclosedCode = implode(', ',array_column($arrSPKClosed,'code'));
        //    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><b>'.$spkUnclosedCode.'</b>. '.$this->errorMsg['truckingServiceWorkOrder'][2]);
        // } 
        //
        //
        // $arrCashOutClosed = $this->validateTruckingCashOutConfirmed($rsHeader[0]['pkey']);   
        // if (!empty($arrCashOutClosed)){ 
        //    $cashOutUnclosedCode = implode(', ',array_column($arrCashOutClosed,'code'));
        //    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><b>'.$cashOutUnclosedCode.'</b>. '.$this->errorMsg['truckingServiceOrder'][8]);
        // }   
        
        
	 }  
    
    function validateBackReadyToInvoice($rsHeader){
        
    }
    
	function validateReadyToInvoice($rsHeader,$autoChangeStatus){ 
		   
        if ($autoChangeStatus) return ;
        
        $useSPK = $this->loadSetting('useSPK');
        if ($useSPK == 2) {
            // validasi status nanti aj, coba dilihat
            return;
        }

        //cek apakah mobil masih ada di spk semua atau tidak
        $rsSelling = $this->getSellingCostDetail($rsHeader[0]['pkey']);
        $arrSellingCarKey = array_column($rsSelling, 'carkey');
        
        if(!empty($arrSellingCarKey)) {
            $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
            $rsWO = $truckingServiceWorkOrder->searchDataRow(
                        array(
                            $truckingServiceWorkOrder->tableName.'.pkey',
                            $truckingServiceWorkOrder->tableName.'.code',
                            $truckingServiceWorkOrder->tableName.'.carkey'
                        ), ' and ' .$truckingServiceWorkOrder->tableName.'.isoutsource = 0 and ' . $truckingServiceWorkOrder->tableName.'.refkey  = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3) ');
           
            $arrWOCarKey = array_column($rsWO, 'carkey');
            
            //bandingkan, carkey selling dan carkey spk
            $arrMissingCarKeyInWO = array_diff($arrSellingCarKey, $arrWOCarKey);
            
            if (!empty($arrMissingCarKeyInWO)) {
                $car = new Car();
                $rsCar = $car->searchDataRow(array($car->tableName.'.pkey',$car->tableName.'.policenumber'),' and ' . $car->tableName.'.pkey in ('.$this->oDbCon->paramString($arrMissingCarKeyInWO,',').')');
                $arrMsg = array();
                foreach($rsCar as $carRow) {
                    array_push($arrMsg, '<strong>'.$carRow['policenumber'].'. </strong>' . $this->errorMsg['truckingServiceOrder'][7]);
                }
                if(!empty($arrMsg)) {
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201]. '<br>'.implode('<br>', $arrMsg));
                }
            }

        }
        
        
		if($rsHeader[0]['statuskey'] <> 4 && $rsHeader[0]['statuskey'] <> 3 && $rsHeader[0]['statuskey'] <> 6)  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201]);
	  
        
         $arrSPKClosed = $this->validateSPKClosed($rsHeader[0]['pkey']);   
         if (!empty($arrSPKClosed)){ 
            $spkUnclosedCode = implode(', ',array_column($arrSPKClosed,'code'));
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><b>'.$spkUnclosedCode.'</b>. '.$this->errorMsg['truckingServiceWorkOrder'][2]);
         }   
        
         $arrCashOutClosed = $this->validateTruckingCashOutConfirmed($rsHeader[0]['pkey']);   
         if (!empty($arrCashOutClosed)){ 
            $cashOutUnclosedCode = implode(', ',array_column($arrCashOutClosed,'code'));
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><b>'.$cashOutUnclosedCode.'</b>. '.$this->errorMsg['truckingServiceOrder'][8]);
         }   
        
	 }
    
	function validateInvoiced($rsHeader,$autoChangeStatus){ 
		   
        if ($autoChangeStatus)
	 	 return;
        
        // jika invoicing normal, semua SPK keupdate
        // harusnya ud gk kepake
 /*       $rsDetail = $this->getUnInvoicedItemDetail($rsHeader[0]['pkey']); 
        if (empty($rsDetail)) return; */
        
        // kalo ad PSK yg blm selesai, JO gk bisa diclose
        $arrSPKClosed = $this->validateSPKClosed($rsHeader[0]['pkey']);   
         if (!empty($arrSPKClosed)){ 
            $spkUnclosedCode = implode(', ',array_column($arrSPKClosed,'code'));
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><b>'.$spkUnclosedCode.'</b>. '.$this->errorMsg['truckingServiceWorkOrder'][2]);
         } 
        
        $arrCashOutClosed = $this->validateTruckingCashOutConfirmed($rsHeader[0]['pkey']);   
         if (!empty($arrCashOutClosed)){ 
            $cashOutUnclosedCode = implode(', ',array_column($arrCashOutClosed,'code'));
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><b>'.$cashOutUnclosedCode.'</b>. '.$this->errorMsg['truckingServiceOrder'][8]);
         }   
        

        
        // pelunasan dr invoice partial
        $rsInvoice = $this->getInvoiceInformation($rsHeader[0]['pkey']);
        $totalInvoice = 0;
        foreach($rsInvoice as $invoice) 
            $totalInvoice += $invoice['amount']; 
         
        if($rsHeader[0]['grandtotal'] <> $totalInvoice)
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[506]); 
        else 
            $this->updateQtyInvoiced($rsHeader[0]['pkey'],true);
        
        
        return;
        

        
/*        $rsDetail = $this->getUnInvoicedItemDetail($rsHeader[0]['pkey']); 
        if (empty($rsDetail))
            return;*/
        
        // gk bisa manual change status	
        //$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[506]);
		 
	 }
    
    
    
    function afterStatusChanged($rsHeader){   
        $this->updateTruckingCostCashOut($rsHeader[0]['pkey']);   

        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
		
         
        //langsung aj biar gk lama, kalo JO minimal sudah diatas SPK selesai
        if($rsHeader[0]['statuskey'] >=3 ){
            if(empty($rsHeader[0]['firstwodate']) || empty($rsHeader[0]['firstwodate']) ||
               in_array($rsHeader[0]['firstwodate'], array('0000-00-00','1970-01-01')) || 
               in_array($rsHeader[0]['lastwodate'], array('0000-00-00','1970-01-01'))
              ){

            $sql = 'update '.$this->tableName.' set firstwodate = trdate, lastwodate = trdate where pkey = ' .$this->oDbCon->paramString($rsHeader[0]['pkey']);
            $this->oDbCon->execute($sql);

            } 
        }
        
		//$this->updateStatusDate($rsHeader);
		
        // cancel dulu, jadinya invoice gk bisa dicancel gara2 kebentuk terus
        //if(!empty($rsHeader[0]['autoinvoice']) && $rsHeader[0]['statuskey'] == 5)
        //	$this->autoAddInvoice($rsHeader);   
    }
    

	function updateStatusDate($rsHeader){ 
		$statuskey = $rsHeader[0]['statuskey'];
		$statusDate = '';
		
		switch ($statuskey) {
		  case 2:
			$statusDate = 'confirmeddate';
			break;
		  case 3:
			$statusDate = 'spkcompleteddate';
			break;
		  case 4:
			$statusDate = 'validationdate';
			break;
		  case 5:
			$statusDate = 'readytoinvoicedate';
			break;
		  case 6:
			$statusDate = 'invoiceddate';
			break;
		  default:
			$statusDate='';
		}
        
		if(!empty($statusDate)){
			$firstDate ='';
			if(empty($rsHeader[0]['first'.$statusDate.'']) || in_array($rsHeader[0]['first'.$statusDate.''], array('0000-00-00', '1970-01-01'))) 
				$firstDate = ',first'.$statusDate.' = now() ';
               
			$sql = 'update '.$this->tableName.' set last'.$statusDate.'= now() '.$firstDate.' where pkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']);
            $this->oDbCon->execute($sql);
			
		}
	}
    

    function updateSalesWorkOrderCost($id){
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        
        //$rs = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey', $id,true, ' and ' .$truckingServiceWorkOrder->tableName.'.statuskey in (2,3)');
        
         $rs = $truckingServiceWorkOrder->searchDataRow( array(  $truckingServiceWorkOrder->tableName.'.pkey',
                                                                 $truckingServiceWorkOrder->tableName.'.outsourcecost',
                                                                 $truckingServiceWorkOrder->tableName.'.drivercommission',
                                                                 $truckingServiceWorkOrder->tableName.'.codrivercommission'
                                                              ) , 
                                                            '   and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($id).'
                                                                and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
                                                        );
        
        $totalCost = 0;
        for($i=0;$i<count($rs);$i++){
            // outsource cost
            $totalCost += $rs[$i]['outsourcecost'];
            
            // komisi driver dan codriver
            $totalCost += $rs[$i]['drivercommission'];
            $totalCost += $rs[$i]['codrivercommission'];
            
            // detail cost
            $rsCost = $truckingServiceWorkOrder->getCostDetail($rs[$i]['pkey']);
            
            foreach ($rsCost as $cost)  
                $totalCost += ($cost['qty'] * $cost['amount']); 
       
//			
//			// kalo ad multidrop
			if($this->loadSetting('multidropWorkOrder') == 1){
				
				// hanya ambil yg SPK nya konfirmasi dan selesai
 				$rsCargoCostDetail = $truckingServiceWorkOrder->getCargoCostDetail('', $rs[$i]['pkey']);
                
                foreach($rsCargoCostDetail as $cargoCost) {
                    $price =  $cargoCost['price']; 
                    $qty = $cargoCost['qty'];
                    $isMultipliedQty = $cargoCost['ismultipliedqty'];

                    if ($isMultipliedQty == 0) $qty = 1;

                    $totalCost += $qty * $price;
                }	
			} 
			
        }
        	
        try{			 
            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            $sql = 'update '.$this->tableName.' set totalworkordercost = ' . $this->oDbCon->paramString($totalCost). ' where pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);	
            
            $this->oDbCon->endTrans(); 

		}catch(Exception $e){
			$this->oDbCon->rollback();   
		}		
		 
    }
    
    function updateTotalPurchaseRefund($id)
    {
//        $truckingPurchaseRefund = new TruckingPurchaseRefund();
//
//        $rs = $truckingPurchaseRefund->searchDataRow(array($truckingPurchaseRefund->tableName.'.pkey', $truckingPurchaseRefund->tableName.'.total'), 
//                        ' and ' . $truckingPurchaseRefund->tableName.'.refjoborderkey = '.$this->oDbCon->paramString($id).'
//                          and '. $truckingPurchaseRefund->tableName .'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')' 
//                    );
//
//        $totalRefund = 0;
//		
//		foreach($rs as $row)
//        	$totalRefund += $row['total'];

		$totalRefund = $this->getTotalRefund($id);
			
        try{			 
            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);
			
            $sql = 'update '.$this->tableName.' set totalrefund = ' . $this->oDbCon->paramString($totalRefund). ' where pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);	
            
            $this->oDbCon->endTrans(); 

		}catch(Exception $e){
			$this->oDbCon->rollback();   
		}	


    
    }
        
    function getJobOrderByMonth($startPeriod, $endPeriod, $warehousekey = ''){
         $sql = 'select 
                    month(trdate) as month,  
                    DATE_FORMAT(trdate, \'%b\')  as monthname, 
                    year(trdate) as year, 
                    sum(grandtotal) as total
                from 
                    '.$this->tableName.'
                where (statuskey >= 2 and statuskey <= 6) and trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')';
        
		 // khusus kalo user pilih warehouse
		 if (!empty($warehousekey))
				$sql .= ' and warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
          $sql .=  $this->getWarehouseCriteria() ;
		 
          $sql .= ' group by year(trdate),month(trdate)';
        
//         $this->setLog($sql,true);
         return $this->oDbCon->doQuery($sql); 
    } 
    	
    	
    function getTruckingCostByMonth($startPeriod, $endPeriod, $warehousekey = ''){
         $sql = 'select 
                    month('.$this->tableName.'.trdate) as month,  
                    DATE_FORMAT('.$this->tableName.'.trdate, \'%b\') as monthname, 
                    year('.$this->tableName.'.trdate) as year, 
                    sum('.$this->tableName.'.totalheadercost + '.$this->tableName.'.totalworkordercost) as total
                from 
                    '.$this->tableName.' 
                   where (statuskey >= 2 and statuskey <= 6) and trdate between \''. date("Y-m-d", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\') ';
                    
		        
		 // khusus kalo user pilih warehouse
		 if (!empty($warehousekey))
				$sql .= ' and warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
		
          $sql .=  $this->getWarehouseCriteria() ;
          $sql .= ' group by year(trdate),month(trdate)';   
        
         //$this->setLog($sql,true);
         return $this->oDbCon->doQuery($sql); 
    } 
	
//	function getSellingGroupByCategory($arrJOkey){
//		$sql ='select 
//				'.$this->tableSellingCost.'.pkey, 
//				'.$this->tableSellingCost.'.refkey, 
//				'.$this->tableSellingCost.'.subtotal,
//				'.$this->tableItem.'.categorykey,
//				'.$this->tableServiceCategory.'.name as categoryname
//			   from 
//				'.$this->tableSellingCost.',
//				'.$this->tableItem.',
//				'.$this->tableServiceCategory.'	
//			   where
//			  	'.$this->tableSellingCost.'.costkey = '.$this->tableItem.'.pkey and
//				'.$this->tableItem.'.categorykey = '.$this->tableServiceCategory.'.pkey and
//				'.$this->tableSellingCost.'.refkey in ('.$this->oDbCon->paramString($arrJOkey,',').')
//			   group by
//			   '.$this->tableSellingCost.'.refkey,
//			   '.$this->tableItem.'.categorykey
//			   order by categoryname asc
//			  ';
//		
//		$this->setLog($sql,true);
//         return $this->oDbCon->doQuery($sql); 
//		
//	}
    
    function getTruckingCostRevenueAmount($startPeriod, $endPeriod,$warehousekey=''){
        // Sales Amount
        
        $sql = 'select 
                  sum('.$this->tableName.'.totalheadercost + '.$this->tableName.'.totalworkordercost ) as costamount, 
                  sum('.$this->tableName.'.grandtotal)  as revenueamount
                from 
                    '.$this->tableName.' 
                where 
                    ('.$this->tableName.'.statuskey >= 2 and '.$this->tableName.'.statuskey <= 6 ) and
                     trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')';    
       
		
		 if (!empty($warehousekey))
				$sql .= ' and warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
        $sql .=  $this->getWarehouseCriteria() ;
        
//        $this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql); 
    }  
    
    function afterAddDataOnCopy($pkey, $oldkey){
        // reset invoiced qty
        $sql = 'update '.$this->tableNameDetail.' set qtyinvoiced = 0,statuskey = 1 where refkey =  ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql); 
          
        $sql = 'update '.$this->tableName.' set  firstwodate = \'\', lastwodate = \'\' where pkey =  ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql); 
        
    }
     
    function getCustomerUninvoicedAmount($customerkey){
        
        // utk penambahan nilai AR outstanding
        // statuskey semuanya utk jaga agar ketauan diawal jika melebih outstanding
        // '.$this->tableName.'.statuskey in (2,3,4,5,6) and
        $sql = 'select 
                    coalesce(sum(grandtotal - totalinvoiced),0) as totaluninvoiced 
                from 
                    '.$this->tableName.' 
                where  
                    '.$this->tableName.'.customerkey = ' .  $this->oDbCon->paramString($customerkey);
        
        $rsJO =  $this->oDbCon->doQuery($sql);
        return $rsJO[0]['totaluninvoiced'];
    }
    
    function getInvoiceInformation($pkey, $statuskey = array(2,3)){
        if(!is_array($statuskey)) $statuskey = array($statuskey);
        
        $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
      
        $sql = 'select
            '.$truckingServiceOrderInvoice->tableNameDetail.'.salesorderkey,     
            '.$truckingServiceOrderInvoice->tableName.'.pkey,
            '.$truckingServiceOrderInvoice->tableName.'.code,    
            '.$truckingServiceOrderInvoice->tableName.'.trdate,
            '.$truckingServiceOrderInvoice->tableName.'.isdownpayment,
            '.$truckingServiceOrderInvoice->tableName.'.customerkey,
            '.$truckingServiceOrderInvoice->tableName.'.grandtotal,
            '.$truckingServiceOrderInvoice->tableName.'.statuskey,
            '.$truckingServiceOrderInvoice->tableName.'.requestid,
            '.$truckingServiceOrderInvoice->tableStatus.'.status as statusname,
            '.$truckingServiceOrderInvoice->tableNameDetail.'.amount,
            '.$truckingServiceOrderInvoice->tableCustomCode.'.pkey as invoicetypekey,
            '.$truckingServiceOrderInvoice->tableCustomCode.'.name as invoicetypename
          from 
            '.$truckingServiceOrderInvoice->tableName.',
            '.$truckingServiceOrderInvoice->tableStatus.',
            '.$truckingServiceOrderInvoice->tableNameDetail.',
            '.$truckingServiceOrderInvoice->tableCustomCode.'
          where  
            '. $truckingServiceOrderInvoice->tableNameDetail.'.salesorderkey in ('.$this->oDbCon->paramString($pkey,',') .') and   
            '. $truckingServiceOrderInvoice->tableName.'.pkey = '. $truckingServiceOrderInvoice->tableNameDetail.'.refkey and
            '. $truckingServiceOrderInvoice->tableName.'.statuskey = '. $truckingServiceOrderInvoice->tableStatus.'.pkey and
            '. $truckingServiceOrderInvoice->tableName.'.statuskey in ('.$this->oDbCon->paramString($statuskey,',').') and
            '. $truckingServiceOrderInvoice->tableName.'.customcodekey =  '. $truckingServiceOrderInvoice->tableCustomCode.'.pkey';
 
        return $this->oDbCon->doQuery($sql);

    }
    

    function getAmountInvoiced($pkey){
        // pisahkan dr yg atas agar tidak mengganggu performance yg lain
        $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();

        $rsKey = $this->getTableKeyAndObj($truckingServiceOrderInvoice->tableName,array('key'));  

        $sql = 'select
            '.$truckingServiceOrderInvoice->tableName.'.code,    
            '.$truckingServiceOrderInvoice->tableName.'.trdate,
            '.$truckingServiceOrderInvoice->tableName.'.isdownpayment,
            '.$truckingServiceOrderInvoice->tableName.'.customerkey,
            '.$truckingServiceOrderInvoice->tableName.'.tax23value,
            '.$truckingServiceOrderInvoice->tableName.'.ispriceincludetax,
            '.$truckingServiceOrderInvoice->tableName.'.taxpercentage,
            '.$truckingServiceOrderInvoice->tableName.'.statuskey,
            '.$truckingServiceOrderInvoice->tableName.'.pkey,
            '.$truckingServiceOrderInvoice->tableARStatus.'.status as arstatusname,
            '.$truckingServiceOrderInvoice->tableARStatus.'.pkey as arstatuskey,
            '.$truckingServiceOrderInvoice->tableAR.'.code as arcode,
            coalesce('.$truckingServiceOrderInvoice->tableNameDetail.'.amount,0) as amount,
            '.$truckingServiceOrderInvoice->tableNameDetail.'.salesorderkey
            from 
            '.$truckingServiceOrderInvoice->tableName.'
                left join '.$truckingServiceOrderInvoice->tableAR.' on '.$truckingServiceOrderInvoice->tableAR.'.reftabletype = '.$this->oDbCon->paramString($rsKey['key']).' and '.$truckingServiceOrderInvoice->tableAR.'.refkey = '.$truckingServiceOrderInvoice->tableName.'.pkey 
                left join '.$truckingServiceOrderInvoice->tableARStatus.' on '.$truckingServiceOrderInvoice->tableAR.'.statuskey = '.$truckingServiceOrderInvoice->tableARStatus.'.pkey and
                          '.$truckingServiceOrderInvoice->tableAR.'.statuskey <> 4,
            '.$truckingServiceOrderInvoice->tableNameDetail.' 
          where  
            '. $truckingServiceOrderInvoice->tableNameDetail.'.salesorderkey in ('.$this->oDbCon->paramString($pkey,',') .') and  
            '. $truckingServiceOrderInvoice->tableName.'.pkey = '. $truckingServiceOrderInvoice->tableNameDetail.'.refkey and 
            '. $truckingServiceOrderInvoice->tableName.'.statuskey in (1,2,3) 
         group by ('. $truckingServiceOrderInvoice->tableNameDetail.'.pkey)    
        ';
        //$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql);

    }  

    function getMonthlySalesSummary($startPeriod = '',$endPeriod ='',  $criteria='',$groupby = ''){
        
        // DATE FORMAT => d / m / Y

        if (empty($startPeriod)) $startPeriod = DEFAULT_EMPTY_DATE; 
        if (empty($endPeriod)) $endPeriod = date('d / m / Y');
         
        
        // be aware, perubahan group harus update ke concat index jg
        if (empty($groupby))
            $groupby = 'customerkey, year(trdate), month(trdate)';
        
        $sql  = '
                select 
                    '.$this->tableCustomer.'.name, 
                    customerkey,
                    concat(customerkey,\'-\',DATE_FORMAT(trdate, \'%c%Y\'))  as periodindex,
                    month(trdate) as month,   
                    year(trdate) as year, 
                    sum('.$this->tableName.'.grandtotal) as grandtotal,
                    sum('.$this->tableName.'.totalworkordercost + '.$this->tableName.'.totalheadercost + '.$this->tableName.'.totalrefund) as totalcost,
                    sum('.$this->tableName.'.grandtotal - '.$this->tableName.'.totalworkordercost -  '.$this->tableName.'.totalheadercost - '.$this->tableName.'.totalrefund) as grossprofit
                from 
                    '.$this->tableCustomer.',
                    '.$this->tableName.'
                where 
                    '.$this->tableCustomer.'.pkey = '.$this->tableName.'.customerkey';
                   
         $sql .= ' and  trdate between '. $this->oDbCon->paramDate($startPeriod.' 00:00:00',' / ') .' and LAST_DAY('. $this->oDbCon->paramDate($endPeriod.' 23:59:59',' / ') .')';
         $sql .=  $this->getWarehouseCriteria() ;
        
        if (!empty($criteria))  $sql .= ' ' .$criteria;
        
        $sql .=' group by ' .$groupby;
        
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
    
    
    function getMonthlyQtySummary($startPeriod = '',$endPeriod ='',  $criteria='',$groupby = ''){
        
        // DATE FORMAT => d / m / Y

        if (empty($startPeriod)) $startPeriod = DEFAULT_EMPTY_DATE; 
        if (empty($endPeriod)) $endPeriod = date('d / m / Y');
         
        
        // be aware, perubahan group harus update ke concat index jg
        if (empty($groupby))
            $groupby = 'customerkey,itemkey, year(trdate), month(trdate)';
        
        $sql  = '
                select 
                    '.$this->tableCustomer.'.name as customername, 
                    customerkey,
                    '.$this->tableItem.'.pkey as itemkey, 
                    '.$this->tableItem.'.name as itemname, 
                    concat(customerkey,\'-\',DATE_FORMAT('.$this->tableName.'.trdate, \'%c%Y\'))  as periodindex,
                    concat(customerkey,\'-\',itemkey,\'-\',DATE_FORMAT('.$this->tableName.'.trdate, \'%c%Y\'))  as perioditemindex,
                    month('.$this->tableName.'.trdate) as month,   
                    year('.$this->tableName.'.trdate) as year, 
                    sum('.$this->tableNameDetail.'.qtyinbaseunit) as total 
                from 
                    '.$this->tableName.',
                    '.$this->tableNameDetail.',
                    '.$this->tableCustomer.',
                    '.$this->tableItem.'
                where
                    '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableName.'.customerkey  = '.$this->tableCustomer.'.pkey and 
                    '.$this->tableNameDetail.'.itemkey = '.$this->tableItem.'.pkey';
                   
         $sql .= ' and  '.$this->tableName.'.trdate between '. $this->oDbCon->paramDate($startPeriod.' 00:00:00',' / ') .' and LAST_DAY('. $this->oDbCon->paramDate($endPeriod.' 23:59:59',' / ') .')';
         
        if (!empty($criteria))
            $sql .= ' ' .$criteria;
        
        $sql .=  $this->getWarehouseCriteria() ;
        
        $sql .=' group by ' .$groupby;
        $sql .=' order by customername asc, itemname asc';
        
        //$this->setLog($sql);
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
    
    /*function updateTotalInvoicedAndOutstandingAmount($id){
        
        try{			 
            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

//            $sql = 'select 
//                sum('.$this->tableTruckingServiceOrderInvoiceDetail.'.amount) as amount 
//            from 
//                '.$this->tableTruckingServiceOrderInvoiceHeader.',
//                '.$this->tableTruckingServiceOrderInvoiceDetail.' 
//            where 
//                '.$this->tableTruckingServiceOrderInvoiceHeader.'.statuskey in (2,3) and
//                '.$this->tableTruckingServiceOrderInvoiceDetail.'.salesorderkey = '.$this->oDbCon->paramString($id).' and
//                '.$this->tableTruckingServiceOrderInvoiceHeader.'.pkey = '.$this->tableTruckingServiceOrderInvoiceDetail.'.refkey   
//            ';
//            
            //'.$this->tableTruckingServiceOrderInvoiceHeader.'.isdownpayment = 1 and
            
            $sql = 'select sum(amount) as amount  from '.$this->tablePartialInvoice.' where refkey = '.$this->oDbCon->paramString($id).' and amount > 0'; 
            $rsAmount = $this->oDbCon->doQuery($sql);

            $sql = 'update '.$this->tableName.' set totalinvoiced  = '.$this->oDbCon->paramString($rsAmount[0]['amount']).' where pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);
            
            $this->oDbCon->endTrans(); 

		}catch(Exception $e){
			$this->oDbCon->rollback();   
		}	 

    }
    */
    function getTotalInvoicedAndOutstanding($id,$customCodeKey = ''){
        
            $customCodeCriteria = (!empty($customCodeKey)) ? ' and customcodekey = ' . $this->oDbCon->paramString($customCodeKey) : '';
        
            $sql = 'select pkey, amount  from '.$this->tablePartialInvoice.' where refkey = '.$this->oDbCon->paramString($id).' and amount > 0 ' . $customCodeCriteria; 
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
         
            return $arr;
		 	 
    }
    
    function calculateGrossProfitMargin($id){
        $truckingCost = new Service(TRUCKING_SERVICE,1);
        
        $rsHeader = $this->getDataRowById($id);
        
        $rsItem = $truckingCost->searchData($truckingCost->tableName.'.reimburse',1,true);
        $rsItemReimburse = array_column($rsItem,'pkey');
         
        
        // COST
        $rsHeaderCost = $this->getHeaderCost($id);
        
        $cost = 0;  
        
        foreach($rsHeaderCost as $costRow){  
            if (!in_array($costRow['costkey'],$rsItemReimburse)) 
                $cost += $costRow['subtotal']; 
        }  
        
        $rsCostInhouse = $this->getWorkOrderCostDetail($id,false, false); 
        foreach($rsCostInhouse as $costRow){ 
            if (!in_array($costRow['costkey'],$rsItemReimburse)) 
                $cost += $costRow['amount']; 
        }  
        
        
        $rsCostOutsource = $this->getWorkOrderCostDetail($id,true, false); 
        foreach($rsCostOutsource as $costRow){ 
            if (!in_array($costRow['costkey'],$rsItemReimburse)) 

                $cost += ($costRow['qty'] * $costRow['amount']); 
        }   
        
		// refund
		$cost += $this->getTotalRefund($id);
        
        // SELLING
        $selling = $rsHeader[0]['subtotal'];
        
        // ADDITIONAL SELLING
        $rsDetail = $this->getSellingCostDetail($id);
        foreach($rsDetail as $costRow){  
            if (!in_array($costRow['costkey'],$rsItemReimburse)) 
                $selling += $costRow['subtotal']; 
        }  
         
        $grossMargin = (($selling - $cost) / $selling) * 100;
        return $grossMargin;
    }
	
	function getTotalRefund($id){
		$truckingPurchaseRefund = new TruckingPurchaseRefund();
		
		$sql = 'select
					coalesce(sum(total),0) as total 
				from '.$truckingPurchaseRefund->tableName.' 
				where 
				 	 ' . $truckingPurchaseRefund->tableName.'.refjoborderkey = '.$this->oDbCon->paramString($id).' and 
					 '. $truckingPurchaseRefund->tableName .'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')';
 	
		$rs = $this->oDbCon->doQuery($sql);
		
 		return $rs[0]['total'];
	}
    
    function updateDataAfterRealization($rsHeader,$rsDetail, $action){ 
        // $action => 1 : confirm, 2: reverse confirm
        
        $id = $rsHeader[0]['refkey2'];
        $realizationkey = $rsHeader[0]['pkey'];
          
        // update biaya yagn langsung ditambahkan dr realisasi
        // hapus semua biaya yg berasal dr realisasi (refcashoutkey = 0)
        $sql = 'delete from '.$this->tableHeaderCost.' where realizationkey = '. $this->oDbCon->paramString($realizationkey).' and refcashoutkey = 0 and refkey = ' . $this->oDbCon->paramString($id);
        $this->oDbCon->execute($sql);  
        
        // update informasi realisasi
        foreach($rsDetail as $row){  
              
            $amount = 0;
            $isrealization = 0;
            
            if($action == 1){
               $amount = $row['realcostvalue'];
               $isrealization = 1;
                
                // add biaya yang dr realisasi
                if($row['settlementtypekey'] == 0){ 
                    //insert ulang biaya dar realisasi
                    $sql = 'insert into '.$this->tableHeaderCost.' (refkey,costkey, qty ,amount, subtotal,isrealization, realizationkey,employeekey ) 
                            values  ('. $this->oDbCon->paramString($id).','. $this->oDbCon->paramString($row['costkey']).','. $this->oDbCon->paramString($row['qty']).', '. $this->oDbCon->paramString($row['realcostvalue']).', '. $this->oDbCon->paramString($row['amount']).',1,'. $this->oDbCon->paramString($realizationkey).','. $this->oDbCon->paramString($rsHeader[0]['employeekey']).') ';
                    $this->oDbCon->execute($sql);   
                }
            }
            
            $sql = 'update '.$this->tableHeaderCost.'  set  amount = '.$this->oDbCon->paramString($amount).', isrealization = '.$this->oDbCon->paramString($isrealization).' where  '.$this->tableHeaderCost.'.pkey = ' . $this->oDbCon->paramString($row['refkey2']);  
            $this->oDbCon->execute($sql);  
        }


        // UPDATE ULANG TOTAL COST YG TERJADI
        // sekaligus diudpate saja semua gpp harusnya  
        $sql = 'update 
                    '.$this->tableHeaderCost.' 
                set 
                   subtotal = CASE
                   WHEN isrealization <> 0 THEN qty * amount
                   ELSE qty * requestamount
                   END
                where 
                    refkey = '. $this->oDbCon->paramString($id);
		
        $this->oDbCon->execute($sql);

        $sql = 'update 
                    '.$this->tableName.' 
                set   
                    totalHeaderCost = ( 
                            select sum(subtotal) as totalcost from '.$this->tableHeaderCost.' where refkey = '. $this->oDbCon->paramString($id).' 
                    ) 
                where pkey = '. $this->oDbCon->paramString($id);
        
         
        $this->oDbCon->execute($sql);
        
    }
    
    function getPartyDescription($pkey){
        
         $sql = 'select 
                    '.$this->tableNameDetail .'.refkey,
					'.$this->tableNameDetail.'.qtyinbaseunit,
					'.$this->tableItem.'.name as itemname
                  from
                    '.$this->tableNameDetail .', 
                    '.$this->tableItem.' 
                  where
                    '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and  
                    '.$this->tableNameDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';
        
        $rs = $this->oDbCon->doQuery($sql);
        
        $partyDecimal = $this->loadSetting('jobOrderPartyDecimal'); 
		if (empty($partyDecimal)) $partyDecimal = 0; // buat jaga2
		
	 	foreach($rs as $key=>$row) 
			$rs[$key]['party'] = $this->formatNumber($row['qtyinbaseunit'],$partyDecimal). 'x '. $row['itemname'];
	 
        if(!is_array($pkey)){
            $rs = array_column($rs,'party');
            return implode('<br>',$rs);
        }else{
            $returnArr = array();
            $rs = $this->reindexDetailCollections($rs,'refkey');    
            foreach($rs as $key=>$row) 
                $returnArr[$key] = implode('<br>',array_column($row,'party'));  
           
            return $returnArr;
        }
        
        //$rs = $this->reindexDetailCollections($rsAllDetail,$indexField);    
        // klao pkey nya bkn array, balikin 1 aj
        
        
     /*   $arrParty = array();
        $rsDetail = $this->getDetailWithRelatedInformation($pkey);

        for($i=0;$i<count($rsDetail);$i++) 
            array_push($arrParty,$rsDetail[$i]['qtyinbaseunit'] . 'x ' . $rsDetail[$i]['itemname'] );
        $party = implode('<br>',$arrParty);
        
        return $party;*/

    }
    
    function groupCostAmount($rs){
        // group by costkey and amount
        
        $arr = array();
        
        // add yg sudah direalisasi dulu ...
        for($i=0;$i<2;$i++){
            
            foreach($rs as $key => $row){

                // kalo blm direalisasi, skip
                if($row['isrealization'] == $i) continue;

                $costkey = $row['costkey'];
                $amount = ($row['isrealization'] == 1) ? $row['amount'] : $row['requestamount'];
                $isrealization = $row['isrealization'];

                $keyIndex = md5($costkey.'-'.$amount).$isrealization; 

                if (!isset($arr[$keyIndex])){
                    $arr[$keyIndex] = $row;
                    $arr[$keyIndex]['qty'] = 0; 
                } 

                $arr[$keyIndex]['qty'] += $row['qty'];

                unset($rs[$key]);
            }
        } 
        
        $arr = array_values($arr);
        
        return $arr;
    }

    function groupSupplierAmount($rs){
        // group by costkey and amount
        
        $arr = array();
        
        // add yg sudah direalisasi dulu ...
            
		foreach($rs as $key => $row){

			// kalo blm direalisasi, skip
//			if($row['amount'] <=0 ) continue;

			$supplierkey = $row['supplierkey'];
			$amount =  $row['amount'] ;

			$keyIndex = md5($supplierkey.'-'.$amount); 

			if (!isset($arr[$keyIndex])){
				$arr[$keyIndex] = $row;
				$arr[$keyIndex]['qty'] = 0; 
			} 

			$arr[$keyIndex]['qty']++;

			unset($rs[$key]);
		}
        
        $arr = array_values($arr); 
        return $arr;
    }
	
	function getPartnersVehicleInformation($pkey,$criteria=''){ 
        $rsObjKeyInvoice = $this->getTableKeyAndObj($this->tableTruckingServiceOrderInvoiceHeader); 
        
        $sql = 'select 
	   			'.$this->tableName .'.code , 
	   			'.$this->tableName .'.pkey , 
	   			'.$this->tableWorkOrder .'.code as wocode, 
	   			'.$this->tableWorkOrder .'.pkey as wokey, 
	   			'.$this->tableSupplier .'.name as vehiclepartnersname, 
                '.$this->tableAP.'.amount,
			  	'.$this->tableCar.'.policenumber as registrationnumber,
                '.$this->tableAP.'.supplierkey,
                '.$this->tableAP.'.code as apcode
			  from 
			  	'.$this->tableName.',
			  	'.$this->tableCar.',
			  	'.$this->tableSupplier.',
			  	'.$this->tableAP.',
			  	'.$this->tableWorkOrder.' 
			  where   
                '.$this->tableWorkOrder.'.refkey =  '.$this->tableName .'.pkey  and 
                '.$this->tableWorkOrder.'.isoutsource = 0  and 
                '.$this->tableWorkOrder.'.carkey =  '.$this->tableCar .'.pkey  and
                '.$this->tableAP.'.statuskey in(1,2,3) and 
                '.$this->tableAP.'.refkey2 =  '.$this->tableWorkOrder .'.pkey  and 
				'.$this->tableAP.'.aptype ='.AP_TYPE['serviceOutsource'].'  and 
				'.$this->tableAP.'.reftabletype ='.$rsObjKeyInvoice['key'].'  and 
				'.$this->tableAP.'.supplierkey =  '.$this->tableSupplier .'.pkey  and 
				'.$this->tableName.'.pkey = '. $this->oDbCon->paramString($pkey);
    
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
		     
        $sql .= ' order by vehiclepartnersname asc, amount desc';
        
		$rs = $this->oDbCon->doQuery($sql);
         
        return $rs;
    }
    
    function inAllowedStateToUpdateServices($statuskey, $detailpkey = ''){
        
        // cek status 
        if (!in_array($statuskey, array(1,2))) return false; 
        
        if(!empty($detailpkey)){ 
            // cek sudah ad SPK yg diproses blm
            return ($this->hasConfirmedWorkOrder($detailpkey)) ? false : true;  
        }
        
        
        // kalo gk perlu cek detail, return true
        return true;
    }
    
    function getBestSalesAmountByGroup($groupBy, $startPeriod, $endPeriod, $limit = 5,$warehousekey=''){
             // VALUE BASED

            $sql = 'select 
                      sum('.$this->tableName.'.grandtotal) as amount, 
                      '.$this->tableCustomer.'.name  as customername
                    from 
                        '.$this->tableName.', 
                        '.$this->tableCustomer.' 
                    where 
                         '.$this->tableName.'.statuskey in (2,3,4,5,6)  and 
                         '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                         '.$this->tableName.'.trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\') ';
            
			if (!empty($warehousekey))
				$sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
		
            $sql .=  $this->getWarehouseCriteria() ;
            $sql .=  $this->getCompanyCriteria() ;         
                     
            $sql .= 'group by 
                        '.$groupBy.'
                     order by amount desc limit ' . $limit;
 
//			$this->setLog($sql,true);
		
            return $this->oDbCon->doQuery($sql); 
    }
  
    
    function getDetailForAPI($arrKey,$arrIndex = array()){
        $rsDetailsCol = array();
         
        if(in_array('service_detail',$arrIndex)){  
            $rsDetails = $this->getDetailWithRelatedInformation($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['service_detail'] = $rsDetails;
        }
        
        if(in_array('additional_cost_detail',$arrIndex)){  
            $rsDetails = $this->getHeaderCost($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['additional_cost_detail'] = $rsDetails;
        }
        
        if(in_array('additional_selling_detail',$arrIndex)){ 
            $rsDetails = $this->getSellingCostDetail($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey'); 
            $rsDetailsCol['additional_selling_detail'] = $rsDetails;
        }
        
        if(in_array('invoice_detail',$arrIndex)){ 
            $rsDetails = $this->getInvoiceInformation($arrKey, array(1,2,3)); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'salesorderkey'); 
            $rsDetailsCol['invoice_detail'] = $rsDetails;
        }
             
        /*if(in_array('invoice_proforma_detail',$arrIndex)){ 
            $rsDetails = $this->getInvoiceInformation($arrKey,1); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'salesorderkey'); 
            $rsDetailsCol['invoice_proforma_detail'] = $rsDetails;
        }*/
        
        return $rsDetailsCol;
    }
     
    function updateWOActivityDate($sokey){
		$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        // LOGOL request dr SPK nya pending sudah diupdate
		//$rsWO = $truckingServiceWorkOrder->searchData('refkey',$sokey,true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3) order by ');
		$rsWO = $truckingServiceWorkOrder->searchDataRow(array($truckingServiceWorkOrder->tableName.'.pkey', $truckingServiceWorkOrder->tableName.'.stuffingdatetime'),
                                             ' and '.$truckingServiceWorkOrder->tableName.'.refkey in ('.$this->oDbCon->paramString($sokey,',').') 
											   and '.$truckingServiceWorkOrder->tableName.'.statuskey in(1,2,3) order by stuffingdatetime asc'
                                            );
		$firstDate = DEFAULT_EMPTY_DATE;
		$lastDate = DEFAULT_EMPTY_DATE;
		if(!empty($rsWO)){			
			//$firstDate = $this->oDbCon->paramDate($rsWO[0]['stuffingdatetime'],' / '); 
			$firstDate = $this->formatDBDate($rsWO[0]['stuffingdatetime'],'d / m / Y H:i'); 
			$countWO = count($rsWO);
			$lastDate = $this->formatDBDate($rsWO[($countWO-1)]['stuffingdatetime'],'d / m / Y H:i'); 	
		}
		
		$sql = 'update '.$this->tableName.' set firstwodate = '.$this->oDbCon->paramDate($firstDate,' / ').', lastwodate = '.$this->oDbCon->paramDate($lastDate,' / ').' where pkey = ' .$this->oDbCon->paramString($sokey);
        $this->oDbCon->execute($sql);  
	}
          

	function autoAddInvoice($rsHeader){
        $id = $rsHeader[0]['pkey'];
        
        $arrayToJs = array();
        
        $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
        $paymentMethod = new PaymentMethod();
        $termOfPayment = new TermOfPayment();
        $customCode = new CustomCode();
        
        $arrParam = array();
        $rsDetail = array();
		
        $rsTOP = $termOfPayment->searchData ('','',true,' and ('.$termOfPayment->tableName.'.systemVariable = 1)');  
        $topkey = (!empty($rsTOP)) ? $rsTOP[0]['pkey'] : 0 ;
        
        $paymentMethodKey = 0;
        if($rsTOP[0]['duedays'] == 0){ 
		  $rsMethod = $paymentMethod->searchData ('','',true,' and ('.$paymentMethod->tableName.'.systemVariable = 1)');
          $paymentMethodKey = (!empty($rsMethod)) ? $rsMethod[0]['pkey'] : 0 ;
        }
        
        $arrInvoiceDecode = (!empty($rsHeader[0]['autoinvoice'])) ? json_decode(htmlspecialchars_decode($rsHeader[0]['autoinvoice'], ENT_COMPAT),true) : array();
         
		$invoiceCode = (!empty($arrInvoiceDecode['invoice_id'])) ? $arrInvoiceDecode['invoice_id'] : 'xxxxx';
        
        $invoiceTypeCode = (!empty($arrInvoiceDecode['invoice_type_id'])) ? $arrInvoiceDecode['invoice_type_id'] : '';
        $rsCustomCode = $customCode->searchData($customCode->tableName.'.code', $invoiceTypeCode, true,'','limit 1');
        
        if(!empty($rsCustomCode)){ 
            $invoiceTypeKey = $rsCustomCode[0]['pkey'];
        }else{
            $tablekey = $this->getTableKeyAndObj($truckingServiceOrderInvoice->tableName, array('key'))['key']; 
            $rsCustomCode = $customCode->searchData($customCode->tableName.'.reftabletype', $tablekey, true,'','limit 1');
            $invoiceTypeKey = $rsCustomCode[0]['pkey'];
        }
        
        $arrParam['code'] =  $invoiceCode; 
        $arrParam['selCustomCode'] = $invoiceTypeKey ;
        $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
        
        $arrParam['selTermOfPayment'] = $topkey;
        $arrParam['selBank'] = $paymentMethodKey ; // nanti baru diupdate, gk terlalu penting
        $arrParam['selInvoiceTo'] = 1;
        
        if($rsTOP[0]['duedays'] == 0){ 
            $arrParam['selPaymentMethod'][0] = $paymentMethodKey ;
            $arrParam['paymentMethodValue'][0] = $rsHeader[0]['grandtotal'];
            $arrParam['hidDetailPaymentKey'][0] = 0;
        }
        
        $arrParam['hidSalesOrderKey'] = array();  
        $arrParam['selInvoiceType'] = array();  
        $arrParam['detailNote'] =  array();  
        $arrParam['salesOrderSubtotal'] =  array();  
        $arrParam['amount'] =  array();  
        $arrParam['hidDetailKey'] =  array();  
        $arrParam['chkPick'] =  array();
        
        $arrParam['hidSalesOrderKey'][0] = $rsHeader[0]['pkey'];
        $arrParam['selInvoiceType'][0] = 1;
        $arrParam['detailNote'][0] = $rsHeader[0]['trdesc'];
        $arrParam['salesOrderSubtotal'][0] = $rsHeader[0]['grandtotal'];
        $arrParam['amount'][0] = $rsHeader[0]['grandtotal'];
        $arrParam['hidDetailKey'][0] = 0;
        $arrParam['chkPick'][0] = 1; 
        
		$arrParam['hidDetailItemKey'] = array();
        $arrParam['hidItemDetailKey'] = array();
        $arrParam['hidRefSODetailKey'] = array();
        $arrParam['qtyDetail'] = array();
        $arrParam['priceInUnitDetail'] = array();
        $arrParam['subtotalDetail'] = array();
        $arrParam['hidDetailItemKey'] = array();
        $arrParam['chkService'] = array();
        $arrParam['beforeTaxDetail'] = array();
        $arrParam['afterTaxDetail'] = array(); 
        
		$rsDetail = $this->getDetailWithRelatedInformation($id); 
		foreach($rsDetail as $detail){  
            array_push($arrParam['hidDetailItemKey'], 0);
            array_push($arrParam['hidItemDetailKey'], $detail['itemkey']);
            array_push($arrParam['hidRefSODetailKey'], $detail['pkey']);
            array_push($arrParam['qtyDetail'], $detail['qtyinbaseunit']);
            array_push($arrParam['priceInUnitDetail'], $detail['priceinunit']);
            array_push($arrParam['subtotalDetail'], $detail['total']);
            array_push($arrParam['chkService'], 1);    
            array_push($arrParam['beforeTaxDetail'], $detail['total']);        
            array_push($arrParam['afterTaxDetail'], $detail['total']);       
		}
         
        $rsDetailAdditional = $this->getSellingCostDetail($id); 
        
		foreach($rsDetailAdditional as $detail){  
            array_push($arrParam['hidDetailItemKey'], 0);
            array_push($arrParam['hidItemDetailKey'], $detail['costkey']);
            array_push($arrParam['hidRefSODetailKey'], $detail['pkey']);
            array_push($arrParam['qtyDetail'], $detail['qty']);
            array_push($arrParam['priceInUnitDetail'], $detail['price']);
            array_push($arrParam['subtotalDetail'], $detail['subtotal']);
            array_push($arrParam['chkService'], 1);  
            array_push($arrParam['beforeTaxDetail'], $detail['subtotal']);        
            array_push($arrParam['afterTaxDetail'], $detail['subtotal']);  
		}
        
		$arrParam['hidTotalRows'] = array(array(1)); 
        $arrParam['hidDetailItemKeyTotalRows'] = array();
        $arrParam['hidDetailItemKeyTotalRows'][1][0]= count($rsDetail) + count($rsDetailAdditional);
        
        
        $arrayToJs = $truckingServiceOrderInvoice->addData($arrParam);

        if (!$arrayToJs[0]['valid'])
            $this->addErrorList($arrayToJs, false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
		else{
			$invoicekey = $arrayToJs[0]['data']['pkey'];
			$arrayToJs = $truckingServiceOrderInvoice->changeStatus($invoicekey,TRANSACTION_STATUS['konfirmasi']); 
            //$this->setLog($arrayToJs,true);
		}
     
            
        return $arrayToJs; 
	}

      function searchAvailableJobOrderForPurchase($supplierkey, $criteria = '')
    {
        $arrCriteriaSPK = array();
        $arrCriteriaSPKCost = array();

        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();

        // cari semua SPK yg supplierkeyna cocok dan status jO nya 2-6 
        // ini dr header, nanti bisa dicombine dr detail jg
 
		$statusCriteria = ' and '.$this->tableName . '.statuskey in (2,3,4,5,6) and ' . $this->tableWorkOrder . '.statuskey in (3)';
		  
		// MOBIL
        array_push($arrCriteriaSPK, $truckingServiceWorkOrder->tableName . '.supplierkey in (' . $truckingServiceWorkOrder->oDbCon->paramString($supplierkey, ',') . ') ');
        $sql = 'select  distinct(' . $this->tableName . '.pkey) as pkey,  ' . $this->tableName . '.code as value
                from   ' . $this->tableName . ',  ' . $this->tableWorkOrder . '
                where ' . $this->tableName . '.pkey = ' . $this->tableWorkOrder . '.refkey';

        $sql .= $statusCriteria;
		$sql .= ' and ' . implode(' and ', $arrCriteriaSPK);

        $sql .= $criteria;
        $sql .= $this->getWarehouseCriteria();
        $rsSPK =  $this->oDbCon->doQuery($sql);

		// BIAYA
        array_push($arrCriteriaSPKCost, $truckingServiceWorkOrder->tableCost . '.supplierkey in (' . $truckingServiceWorkOrder->oDbCon->paramString($supplierkey, ',') . ') ');
		$sql = '
                select  distinct(' . $this->tableName . '.pkey) as pkey, ' . $this->tableName . '.code as value
                from    ' . $this->tableName . ',  ' . $this->tableWorkOrder . ',  ' . $this->tableWorkOrderCost . '
                where
                    ' . $this->tableName . '.pkey = ' . $this->tableWorkOrder . '.refkey and
                    ' . $this->tableWorkOrder . '.pkey = ' . $this->tableWorkOrderCost . '.refkey';
		  
		  
        $sql .= $statusCriteria;
		$sql .= ' and ' . implode(' and ', $arrCriteriaSPKCost);
 
        $sql .= $criteria;
        $sql .= $this->getWarehouseCriteria();
        $rsSPKCost =  $this->oDbCon->doQuery($sql);

		  
		  
        //merge SPK dan SPKCost
        $arrSPK = array_merge($rsSPK, $rsSPKCost);

        $arrSPKData = array();
        $existingValues = array();
        foreach($arrSPK as $spk)
        {
            $pkey = $spk['pkey'];
            //$value = $spk['value'];

            if (!in_array($pkey, $existingValues)) {
                array_push($arrSPKData, $spk);
                $existingValues[] = $pkey;
            }
        }

        return $arrSPKData;

    }
	
	  function getDataForScheduleReport($criteria = '', $orderBy = '') {

        $sql = 'select
	   			' . $this->tableNameDetail . '.*, 
                concat ("#", ' . $this->tableNameDetail . '.numberkey, " - ", ' . $this->tableNameDetail . '.qtyinbaseunit,"x ", ' . $this->tableItem . '.name) as label,
                ' . $this->tableItem . '.code as itemcode,
                ' . $this->tableItem . '.name as itemname, 
                ' . $this->tableName . '.customerkey,
                '.$this->tableName.'.code as serviceordercode,
                '.$this->tableName.'.trdate as serviceorderdate,
                '.$this->tableName.'.shipmentnumber,
                '.$this->tableName.'.donumber,
                '.$this->tableName.'.poreference as siso,
                '.$this->tableName.'.trdesc as serviceorderdescription,
                '.$this->tableName.'.depotkey, 
                '.$this->tableName.'.vesselnumber,  
                '.$this->tableName.'.mbl, 
                '.$this->tableName.'.categorykey,
                '.$this->tableName.'.terminalkey,
                '.$this->tableName.'.depotkey, 
                '.$this->tableName.'.statuskey,
                ' . $this->tableStatus . '.status as statusname ,
                '.$this->tableCategory.'.name as categoryname,
                '.$this->tableDepot.'.name as depotname,
                '.$this->tableTerminal.'.name as terminalname,
                '. $this->tableCustomer . '.name as customername,
                '.$this->tableConsignee.'.name as consigneename 
              from
			  	' . $this->tableNameDetail . '  
                  left join '.$this->tableName.' on '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey 
                  left join '.$this->tableConsignee.' on '.$this->tableName.'.consigneekey = '.$this->tableConsignee.'.pkey 
                  left join '.$this->tableCategory.' on '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey 
                  left join '.$this->tableTerminal.' on '.$this->tableName.'.terminalkey = '.$this->tableTerminal.'.pkey 
                  left join '.$this->tableDepot.' on '.$this->tableName.'.depotkey = '.$this->tableDepot.'.pkey 
                  left join '.$this->tableStatus.' on '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
                  left join '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey, 
                ' . $this->tableItem . ' 
			  where
			  	' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey';
 
        $sql .= ' ' .$criteria;
        $sql .= ' ' .$orderBy;
 
        return $this->oDbCon->doQuery($sql);
    }

	
	function getWorkOrderVolume($dateOpt, $opt=array()){
		
		$dateType = $dateOpt['type'];
	
		$indexKey = 'concat('.$this->tableCategory.'.pkey,\'-\', date('.$this->tableNameDetail.'.trdate))';
		$dateCriteria = 'date('.$this->tableNameDetail.'.trdate) in ('.$this->oDbCon->paramString($dateOpt['date'],',').')';
		$groupBy = 'date ('.$this->tableNameDetail.'.trdate) ,'.$this->tableName.'.categorykey';
		$orderBy = $this->tableNameDetail.'.trdate asc, '.$this->tableCategory.'.name asc';
		
		$criteria = '';
		$limit = '';
		
		if($dateType == 'range')
			$dateCriteria = 'date('.$this->tableNameDetail.'.trdate) between '. $this->oDbCon->paramString($dateOpt['date'][0]) .  ' and '.$this->oDbCon->paramString($dateOpt['date'][1]) ;
		else
			$dateCriteria = 'date('.$this->tableNameDetail.'.trdate) in ('.$this->oDbCon->paramString($dateOpt['date'],',').')';
 
		if (isset($opt['indexKey']) && !empty($opt['indexKey'])) $indexKey =$opt['indexKey'];
		if (isset($opt['groupBy'])) $groupBy =$opt['groupBy']; // boleh empty
		if (isset($opt['criteria']) && !empty($opt['criteria'])) $criteria = $opt['criteria'];
		if (isset($opt['limit']) && !empty($opt['limit'])) $limit = ' limit '. $opt['limit'];
		if (isset($opt['orderBy']) && !empty($opt['orderBy'])) $orderBy =  $opt['orderBy'];
			
        $sql = 'select 
                    '.$this->tableNameDetail.'.pkey, 
                    '.$this->tableNameDetail.'.refkey, 
                    sum(' . $this->tableNameDetail . '.qtyinbaseunit) as total,
                    '.$this->tableNameDetail.'.trdate, 
					'.$this->tableName.'.customerkey, 
                    '.$this->tableName.'.categorykey,
                    '.$this->tableName.'.warehousekey,
                    '.$this->tableName.'.statuskey,
					'.$this->tableCategory.'.pkey as categorykey,  
                    '.$this->tableCategory.'.name as categoryname,
                    '.$this->tableCustomer.'.name as customername,
                    '.$indexKey.' as indexkey
                from '.$this->tableNameDetail.'
                        left join '.$this->tableName.' on '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey
                        left join '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
                        left join '.$this->tableCategory.' on '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey
                where 
                        '.$this->tableName.'.statuskey in (2,3,4,5,6) and 
                        '.$dateCriteria.' ' . $criteria .' 
                
        ';
        
    	//criteria SPK Utama
    	// $sql .= ' AND ' . $this->tableName.'.categorykey = 1';
		
        if (isset($opt['warehousekey']) && !empty($opt['warehousekey']))
			$sql .= ' and warehousekey in ('. $this->oDbCon->paramString($opt['warehousekey'],',').' )';       
        
		if(!empty($groupBy))
        	$sql .= ' group by '.$groupBy;
		
		$sql .= ' order by ' .$orderBy;
        
		$sql .= $limit;
		
		$rs = $this->oDbCon->doQuery($sql);
        return $rs; 
        
    }

	
    function updateTotalDebitNote($arrKey){
        
    }
	
	
    function updateSellingCost($spkkey){
		
		// yg dikirim pkey SPK saja, agar tidak perlu delete semua
		// tp pas hitugn ulang, harus dari JO Key
		
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();     
        $rsTruckingServiceWorkOrder = $truckingServiceWorkOrder->getDataRowById($spkkey); 

        $jokey = $rsTruckingServiceWorkOrder[0]['refkey'];
        $workOrderKey = $rsTruckingServiceWorkOrder[0]['pkey'];
        
        //delete semua data selling cost dulu
        $this->deleteSellingCost($rsTruckingServiceWorkOrder[0]['pkey']);


		// kalo statusnya menunggu/batal jgn insert ulang
 		if (in_array($rsTruckingServiceWorkOrder[0]['statuskey'],array(2,3))){
			   //reinsert ulang data selling cost
				$rsCargoCostDetail = $truckingServiceWorkOrder->getCargoCostDetail('',$spkkey);
				for($i=0; $i<count($rsCargoCostDetail); $i++) { 
					
					$price = $rsCargoCostDetail[$i]['sellingprice'];

					if(($this->unFormatNumber($price) <=0))   continue; 

					$costkey = $rsCargoCostDetail[$i]['costkey'];
 
					$qty = $rsCargoCostDetail[$i]['qty'];
					$workOrderDetailKey = $rsCargoCostDetail[$i]['refkey'];
					$workOrderDetailCostKey = $rsCargoCostDetail[$i]['pkey'];
					$isMultipliedQty = $rsCargoCostDetail[$i]['ismultipliedqty'];

					if ($isMultipliedQty == 0) $qty = 1;

					$this->reinsertSellingCost($jokey, $costkey, $qty, $price, $workOrderKey, $workOrderDetailKey, $workOrderDetailCostKey);

				}
		}
     
 
        //hitung ulang total selling
        $this->recountTotalCostJobOrder($jokey);  

    }
	
	 function reinsertSellingCost($refkey, $costkey, $qty, $price, $workOrderKey, $workOrderDetailKey,$workOrderDetailCostKey) 
    {
        $sql = '
            insert into 
                '. $this->tableSellingCost .' 
                (
                    refkey,
                    costkey,
                    qty,
                    price,
                    subtotal,
                    workorderkey,
                    workorderdetailkey,
                    workorderdetailcostkey,
                    islocked
                ) values (
                    '. $this->oDbCon->paramString($refkey) .',
                    '. $this->oDbCon->paramString($costkey) .',
                    '. $this->oDbCon->paramString($this->unFormatNumber($qty)) .',
                    '. $this->oDbCon->paramString($this->unFormatNumber($price)) .',
                    '. $this->oDbCon->paramString($this->unFormatNumber($qty * $price)) .',
                    '. $this->oDbCon->paramString($workOrderKey) .',
                    '. $this->oDbCon->paramString($workOrderDetailKey) .',
                    '. $this->oDbCon->paramString($workOrderDetailCostKey) .',
                    1
                )
            ';
        
        $this->oDbCon->execute($sql);
    }

    function deleteSellingCost($workorderkey)
    {
        $sql = '
            DELETE FROM
                '. $this->tableSellingCost .'
            WHERE
                '. $this->tableSellingCost .'.workorderkey  = ('. $this->oDbCon->paramString($workorderkey) .')
        ';

        $this->oDbCon->execute($sql);
    }
	

    function recountTotalCostJobOrder($jokey) { 
		
		$sql = 'select  coalesce(sum('.$this->tableSellingCost.'.subtotal),0) as total  from '.$this->tableSellingCost.' where refkey = '.$this->oDbCon->paramString($jokey);
		$rsTotal = $this->oDbCon->doQuery($sql);
		$totalSellingCost = $rsTotal[0]['total'];
			
		$sql = 'select   coalesce(sum('.$this->tableNameDetail.'.total),0) as total  from '.$this->tableNameDetail.'  where refkey = '.$this->oDbCon->paramString($jokey);
		$rsTotal = $this->oDbCon->doQuery($sql);
		$totalSellingDetail = $rsTotal[0]['total'];
		 
		$sql = '
            UPDATE
                '. $this->tableName .'
            SET
                grandtotal = '.$this->oDbCon->paramString($totalSellingDetail+$totalSellingCost).',
                totalsellingcost =   '.$this->oDbCon->paramString($totalSellingCost).'
            WHERE
                '. $this->tableName .'.pkey = '. $this->oDbCon->paramString($jokey) .'
        ';
		
		$this->oDbCon->execute($sql);
		
    }

// SEMENTARA UNTUK PRAJA
     function getDataForPrintInvoice($jokey) {
        $sql = '
            SELECT  
                '. $this->tableNameDetail .'.*,
                '. $this->tableName .'.pkey as joheaderkey,
                '. $this->tableName .'.code,
                '. $this->tableName .'.trdesc,
                '. $this->tableItem .'.name as itemname,
                '. $this->tableWorkOrder .'.code as workordercode,
                '. $this->tableWorkOrder .'.pkey as workorderkey,
                '. $this->tableWorkOrder .'.routefrom,
                '. $this->tableWorkOrder .'.routeto,
                '. $this->tableWorkOrder .'.isoutsource,
                '. $this->tableWorkOrder .'.outsourcecarregistrationnumber,
                '. $this->tableCar .'.policenumber,
                CONCAT('. $this->tableName .'.pkey, \'-\', '. $this->tableWorkOrder .'.pkey) as indexkey,
                '. $this->tableCarCategory .'.name as carcategoryname
            FROM
                '. $this->tableNameDetail .'
                    left join '. $this->tableWorkOrder .' on '. $this->tableNameDetail .'.pkey = '. $this->tableWorkOrder .'.refdetailkey
                    left join '. $this->tableCar .' on '. $this->tableWorkOrder .'.carkey = '. $this->tableCar .'.pkey
                    left join '. $this->tableCarCategory .' on '. $this->tableCar .'.categorykey = '. $this->tableCarCategory .'.pkey,
                '. $this->tableName .',
                '. $this->tableItem .'
            WHERE
                '. $this->tableNameDetail .'.refkey = '. $this->tableName .'.pkey and
                '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                ' . $this->tableWorkOrder .'.statuskey in (2,3) and
                '. $this->tableName .'.pkey in ('. $this->oDbCon->paramString($jokey, ',') .')
        ';

        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }

    function getSellingDataForPrintInvoice($jokey) {

        $sql = '
            SELECT
                '. $this->tableSellingCost .'.*,
                '. $this->tableItem .'.name as costname,
                '. $this->tableItem .'.name as description,
                '. $this->tableName .'.pkey as joheaderkey,
                '. $this->tableName .'.code as code,
                '. $this->tableName .'.trdesc,
                '. $this->tableWorkOrder .'.code as workordercode,
                '. $this->tableCar .'.policenumber,
                '. $this->tableWorkOrderCargo .'.workorder,
                '. $this->tableWorkOrderCargo .'.destination,
                '. $this->tableWorkOrderCargo .'.qty as qtyworkorder,
                '. $this->tableCarCategory .'.name as carcategoryname,
                '.$this->tableLocation.'.name as destinationname,
                CONCAT(' . $this->tableName . '.pkey, \'-\', ' . $this->tableWorkOrder . '.pkey) as indexkey
            FROM
                '. $this->tableSellingCost .'
                    left join '. $this->tableItem .' on '. $this->tableSellingCost .'.costkey = '. $this->tableItem .'.pkey
                    left join '. $this->tableWorkOrder .' on '. $this->tableSellingCost .'.workorderkey = '. $this->tableWorkOrder .'.pkey                    
                    left join '. $this->tableCar .' on '. $this->tableWorkOrder .'.carkey = '. $this->tableCar .'.pkey
                    left join '. $this->tableCarCategory .' on '. $this->tableCar .'.categorykey = '. $this->tableCarCategory .'.pkey
                    left join '. $this->tableWorkOrderCargo .' on '.  $this->tableSellingCost .'.workorderdetailkey = '. $this->tableWorkOrderCargo .'.pkey
                        left join '.$this->tableLocation.' on '.$this->tableWorkOrderCargo.'.destinationkey = '.$this->tableLocation.'.pkey,
                '. $this->tableName .'
            WHERE
                '. $this->tableSellingCost .'.refkey = '. $this->tableName .'.pkey and
                '. $this->tableName .'.pkey in ('. $this->oDbCon->paramString($jokey, ',') .')
        ';

        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;

    }
    function getDataForPrintInvoiceContract($jokey) {
        // untuk praja
        $sql = '
            SELECT 
                '. $this->tableNameDetail .'.refkey as joheaderkey,
                '. $this->tableCar .'.policenumber,
                '. $this->tableWorkOrder .'.carkey,
                '. $this->tableItem .'.name as itemname,
                '. $this->tableName .'.trdesc as description,
                '. $this->tableCarCategory .'.name as carcategoryname
            FROM
                '. $this->tableNameDetail .'
                    left join ' . $this->tableWorkOrder . ' on ' . $this->tableNameDetail . '.pkey = ' . $this->tableWorkOrder . '.refdetailkey
                     and ' . $this->tableWorkOrder . '.statuskey in (1,2,3)
                    left join '. $this->tableCar .' on '. $this->tableWorkOrder .'.carkey = '. $this->tableCar .'.pkey
                    left join '. $this->tableCarCategory .' on '. $this->tableCar .'.categorykey = '. $this->tableCarCategory .'.pkey,
                '. $this->tableName .',
                '. $this->tableItem .'
            WHERE
                '. $this->tableNameDetail .'.itemkey = '. $this->tableItem .'.pkey and
                '. $this->tableNameDetail .'.refkey = '. $this->tableName .'.pkey and
                '. $this->tableName .'.pkey in ('. $this->oDbCon->paramString($jokey, ',') .')
        ';

        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }

    function generateDataForSalesOrderVehicleReport($criteria='',$order=''){

        $sql = '
            select
                '. $this->tableNameDetail .'.*,
                '. $this->tableItem .'.code as itemcode,
                '. $this->tableItem .'.name as itemname,
                '. $this->tableName .'.pkey as sokey,
                '. $this->tableName .'.code,
                '. $this->tableName .'.trdate,
                '. $this->tableName .'.routefrom,
                '. $this->tableName .'.routeto,
                '. $this->tableName .'.trdesc,
                '. $this->tableName .'.statuskey,
                '. $this->tableCustomer .'.name as customername,
                '. $this->tableWorkOrder .'.pkey as workorderkey,
                '. $this->tableWorkOrder .'.code as workordercode,
                '. $this->tableWorkOrder .'.refkey as worefkey,
                '. $this->tableWorkOrder .'.refdetailkey as worefdetailkey,
                '. $this->tableWorkOrder .'.carkey,
                '. $this->tableWorkOrder .'.driverkey,
                '. $this->tableWorkOrder .'.codriverkey,
                '. $this->tableWorkOrder .'.drivercommission,
                '. $this->tableWorkOrder .'.codrivercommission
            from
                '. $this->tableNameDetail .',
                '. $this->tableName .'
                left join '. $this->tableCustomer .' on '. $this->tableName .'.customerkey = '. $this->tableCustomer .'.pkey,
                '. $this->tableItem .',
                '. $this->tableWorkOrder .'
            where
                '. $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                '. $this->tableNameDetail .'.pkey = '. $this->tableWorkOrder .'.refdetailkey and
                '. $this->tableNameDetail .'.refkey = '. $this->tableName .'.pkey and
                '. $this->tableWorkOrder .'.statuskey in (2,3)
        ';

        if (!empty($criteria))  
            $sql .=  ' ' .$criteria;
        
        if (!empty($order))  
            $sql .=  ' ' .$order;  
           
        return $this->oDbCon->doQuery($sql);

    }
    
     function getTruckingRevenueCostAmountByCustomer($startPeriod, $endPeriod, $customerkey, $warehousekey=''){
        // Sales Amount
        
        $sql = 'select 
                    DATE_FORMAT(trucking_service_order_header.trdate, "%Y-%m") as period,
                  sum('.$this->tableName.'.totalheadercost + '.$this->tableName.'.totalworkordercost ) as costamount, 
                  sum('.$this->tableName.'.grandtotal)  as revenueamount,
                  '. $this->tableName .'.customerkey,
                  '. $this->tableCustomer .'.name as customername
                from 
                    '.$this->tableName.'
                    left join '. $this->tableCustomer .' on '. $this->tableName .'.customerkey = '. $this->tableCustomer .'.pkey
                where 
                    '.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey).' and
                    ('.$this->tableName.'.statuskey >= 2 and '.$this->tableName.'.statuskey <= 6 ) and
                     trdate between \''. date("Y-m-01 00:00", strtotime($startPeriod)) .'\' and LAST_DAY(\''. date("Y-m-d 23:59", strtotime($endPeriod)) .'\')';    
        
		 if (!empty($warehousekey))
				$sql .= ' and warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';

            
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=' group by customerkey, period';
         
        //if (!empty($groupby))
        //       $sql .=' group by ' .$groupby.', period';
        
        //$this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql); 
    }  
    
    function calculateTotalSalesPerStatus($criteria = ''){
        $sql = 'select sum('.$this->tableName.'.grandtotal) as total, '.$this->tableName.'.statuskey from ' .$this->tableName;
        $sql .= ' where 1=1 '. $criteria;
        $sql .= ' group by '.$this->tableName.'.statuskey';
        
        $rs = $this->oDbCon->doQuery($sql); 
        
        $rs = array_column($rs,null,'statuskey');
        foreach($rs as $key=>$row)
            $rs[$key]['label'] = $row['total'];
        
        return $rs;
    }

 function manipulateDataBeforeUpdateData($arrParam){

        if(isset($this->domainConfig)){

            if(!empty($this->domainConfig['fgt'])){ 
                $truckingServiceOrderCategory =  new TruckingServiceOrderCategory();

                if(!empty($arrParam['hidId'])){
                        $arrParam['paramPrefixCode'] = $arrParam['code'];
                        return $arrParam;
                }

                //$date = '' ;
                
                $lengthDigit = 4;
                $codeFormat = '';

                /** GET JOB NUMBER PER MONTH */
                $sql = '
                    select
                        '.$this->tableName.'.code
                    from
                        '.$this->tableName.'
                    where
                        '.$this->tableName.'.categorykey = '.$this->oDbCon->paramString($arrParam['hidCategoryKey']).' and 
                        month('.$this->tableName.'.trdate) = month('.$this->oDbCon->paramDate($arrParam['trDate'],' / ').') and 
                        year('.$this->tableName.'.trdate) = year('.$this->oDbCon->paramDate($arrParam['trDate'],' / ').') 
                        order by '.$this->tableName.'.trdate desc, '.$this->tableName.'.pkey desc 
                        limit 1
                ';
                $this->setLog($sql, true);

                $rsJob = $this->oDbCon->doQuery($sql); 
  
                //CODE NUMBER
                if(!empty($rsJob)) {
                    //kalau ada codenya
                    $numberParts = explode('/',$rsJob[0]['code']);
                    $number = isset($numberParts[0]) ? (int)$numberParts[0] : 0;
                    $codeNumber = (int)$number + 1;
                } else {
                    //jika belum ada di bulan ini otomatis nomor 1
                    $codeNumber = 1;
                }

                $codeNumber = str_pad($codeNumber, $lengthDigit, '0', STR_PAD_LEFT);

                /** CUSTOMER */

                //GET CUSTOMER NUMBER
          $sql = '
                    select
                        '.$this->tableName.'.code
                    from
                        '.$this->tableName.'
                    where
                        '.$this->tableName.'.customerkey = '.$this->oDbCon->paramString($arrParam['hidCustomerKey']).' and 
                        year('.$this->tableName.'.trdate) = year('.$this->oDbCon->paramDate($arrParam['trDate'],' / ').') 
                        order by '.$this->tableName.'.trdate desc, '.$this->tableName.'.pkey desc 
                        limit 1
                ';

                $rsJob =  $this->oDbCon->doQuery($sql); 
            
                //CODE NUMBER CUSTOMER
                if(!empty($rsJob)) {
                    //kalau ada codenya
                    $partNumber = explode('/', $rsJob[0]['code']);  
                    $customerPart = isset($partNumber[1]) ? $partNumber[1] : '0000-XXX';

                    $customerNumberRaw = explode('-', $customerPart)[0]; 
                    $customerNumber    = (int)$customerNumberRaw + 1;
                } else {
                    //jika belum ada di tahun ini otomatis nomor 1
                    $customerNumber = 1;
                }

                $customerNumber = str_pad($customerNumber, $lengthDigit, '0', STR_PAD_LEFT);

                //GET CUSTOMER CODE
                $sql = '
                    select
                        '.$this->tableCustomer.'.code
                    from
                        '.$this->tableCustomer.'
                    where
                        '.$this->tableCustomer.'.pkey = '.$this->oDbCon->paramString($arrParam['hidCustomerKey']).'
                ';

                $rsCustomer = $this->oDbCon->doQuery($sql); 
                $customerCode = (!empty($rsCustomer) ? $rsCustomer[0]['code'] : '');


                $customerCodeNumber = $customerNumber.'-'.$customerCode;



                /** JOB CATEGORY CODE */
                $rsJobCategory = $truckingServiceOrderCategory->searchDataRow(array(
                    $truckingServiceOrderCategory->tableName.'.pkey',
                    $truckingServiceOrderCategory->tableName.'.code',
                    $truckingServiceOrderCategory->tableName.'.name'
                ), ' and ' . $truckingServiceOrderCategory->tableName.'.pkey = '.$this->oDbCon->paramString($arrParam['hidCategoryKey']).' ');

                if(!empty($rsJobCategory)) {
                    $jobCategoryCode = $rsJobCategory[0]['code'];
                } else {
                    $jobCategoryCode = '';
                }



                /** MONTH AND YEAR */
                $month = $this->numberToRoman(date('m'));
                $year = date('Y');
                $monthAndYear = $month.'/'.$year;


                //CODE FORMAT: Job Number / Customer Number - Customer Code / Job Category / Month(Roman) / Year
                $codeFormat = $codeNumber.'/'.$customerCodeNumber.'/'.$jobCategoryCode.'/'.$monthAndYear; 

                $arrParam['paramPrefixCode'] = $codeFormat;       

            }

        }

        return $arrParam;
    }
    
    function validateSPKClosed($jokey){
        $arrResult = array();
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
      
        //validasi SPK harus selesai semua
        $rsSPK = $truckingServiceWorkOrder->searchDataRow(array(
            $truckingServiceWorkOrder->tableName . '.pkey',
            $truckingServiceWorkOrder->tableName . '.code'
        ), ' and ' . $truckingServiceWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($jokey ) . ' and 
            ' . $truckingServiceWorkOrder->tableName . '.statuskey in (1,2) ');

        for ($i = 0; $i < count($rsSPK); $i++) {
            array_push($arrResult,array('pkey' => $rsSPK[$i]['pkey'], 'code' => $rsSPK[$i]['code']));
        } 
       
      return $arrResult;
    }
    
    function validateTruckingCashOutConfirmed($jokey){
        
        if($this->loadSetting('allowCashOutAfterJobClosed') == 1) return array();
        
        $arrResult = array();
        $truckingCostCashOut = new TruckingCostCashOut();
      
        //validasi SPK harus selesai semua
        $rsCashOut = $truckingCostCashOut->searchDataRow(array(
            $truckingCostCashOut->tableName . '.pkey',
            $truckingCostCashOut->tableName . '.code'
        ), ' and ' . $truckingCostCashOut->tableName . '.jokey = ' . $this->oDbCon->paramString($jokey ) . ' and 
            ' . $truckingCostCashOut->tableName . '.statuskey in (1,2) ');

        for ($i = 0; $i < count($rsCashOut); $i++) {
            array_push($arrResult,array('pkey' => $rsCashOut[$i]['pkey'], 'code' => $rsCashOut[$i]['code']));
        } 
       
      return $arrResult;
    }
     
}
?>