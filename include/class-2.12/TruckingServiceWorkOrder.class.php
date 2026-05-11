<?php
  
class TruckingServiceWorkOrder extends BaseClass{ 
  
   function __construct(){
		
		parent::__construct();
	   
       
        // refrequestkey : untuk key dari Trucking Cash Out Request
       
		$this->tableName = 'trucking_service_work_order'; 
        $this->tableWorkOrderCargoDetail = 'trucking_service_work_order_cargo';
        $this->tableWorkOrderCostCargoDetail = 'trucking_service_work_order_cost_cargo';
        $this->tableWorkOrderJobProgressDetail = 'trucking_service_work_order_job_progress';
        $this->tableJobProgressHeader = 'job_progress_header';
        $this->tableJobProgressDetail = 'job_progress_detail';
		$this->tableServiceOrderHeader = 'trucking_service_order_header'; 
		$this->tableServiceOrderDetail = 'trucking_service_order_detail'; 
		$this->tableServiceOrderSellingCost = 'trucking_service_order_selling_cost';
        $this->tableCost = 'trucking_service_work_order_cost';
        $this->tableWorkOrderCarDetail = 'trucking_service_work_order_car';
		$this->tableCategory = 'trucking_service_order_category';   
        $this->tableTruckingCostCashOut = 'trucking_cost_cash_out_header';
        $this->tableTruckingCostCashOutDetail = 'trucking_cost_cash_out_detail';
        $this->tableTruckingServiceOrderInvoiceHeader = 'trucking_service_order_invoice_header';
	    $this->tableDriverCost = 'driver_cost';
        $this->tableCargoType = 'cargo_type';
        $this->tableTruckingJob = 'trucking_job';
        $this->tableItem = 'item';
        $this->tableWarehouse = 'warehouse';
		$this->tableCustomer = 'customer';
		$this->tableLocation = 'location'; 
		$this->tableCar = 'car'; 
		$this->tableCarCategory = 'car_category'; 
		$this->tableChassis = 'chassis'; 
		$this->tableEmployee = 'employee'; 
		$this->tableStatus = 'trucking_service_work_order_status'; 
		$this->tableHistory = 'history';   
		$this->tableDepot = 'depot'; 
		$this->tableTerminal = 'terminal'; 
		$this->tableSupplier = 'supplier'; 
		$this->tableConsignee = 'consignee';  
	    $this->tableGPS = 'gps';
        $this->isTransaction = true;
        
        $this->allowedStatusForEdit = array(1,2);
        $this->useStorage = $this->useStorage('S3');
       
        $this->uploadProgressFileFolder = 'trucking-work-order-progress/';
        $this->uploadQRFolder = 'trucking-work-order-qr/';
       
        $this->activeModule = array_merge($this->activeModule, $this->isActiveModule(array('truckingpurchase', 'jobprogress')));
       
		$this->securityObject = 'TruckingServiceWorkOrder';  
        $this->costSecurityObject = 'TruckingServiceWorkOrderCost'; 
        $this->sellingPriceSecurityObject = 'SellingPrice';
        $this->autoPrintURL = 'print/truckingServiceWorkOrder';
          
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['qty'] = array('qtyCostDetail'); //gk bisa set mandatory, utk SPK yg gk pake qty model reguler
        $this->arrDataDetail['taxpercentage'] = array('taxPercentageCostDetail');
        $this->arrDataDetail['taxvalue'] = array('taxValueCostDetail');
        $this->arrDataDetail['tax23percentage'] = array('tax23PercentageCostDetail');
        $this->arrDataDetail['tax23value'] = array('tax23ValueCostDetail');
        //$this->arrDataDetail['istax23'] = array('chkIsTax23CostDetail');
        $this->arrDataDetail['total'] = array('totalCostDetail');
        $this->arrDataDetail['costkey'] = array('hidCostKey',array('mandatory'=>true));
        $this->arrDataDetail['supplierkey'] = array('hidSupplierDetailKey');
        $this->arrDataDetail['employeekey'] = array('hidEmployeeDetailKey');
        $this->arrDataDetail['amount'] = array('amount',array('datatype'=>'number'));
        $this->arrDataDetail['requestamount'] = array('requestAmount',array('datatype'=>'number'));//gk bisa set mandatory karena kalo edit dr realisasi, jadinya hilang row nya
       	$this->arrDataDetail['isreimburse'] = array('isReimburse'); 
        $this->arrDataDetail['isreceiveddoc'] = array('chkReceivedDoc');
	    $this->arrDataDetail['refadditionalcostkey'] = array('hidRefAdditionalCostkey');
	    
        $this->arrCarDetail = array(); 
        $this->arrCarDetail['pkey'] = array('hidOutsourceVehicleDetailKey');
        $this->arrCarDetail['refkey'] = array('pkey','ref');
        $this->arrCarDetail['itemkey'] = array('hidServiceDetailKey',array('mandatory'=>true));
        $this->arrCarDetail['carregistrationnumber'] = array('carRegistration');
        $this->arrCarDetail['container'] = array('containerDetail');
        $this->arrCarDetail['seal'] = array('sealDetail');
        $this->arrCarDetail['qty'] = array('qtyDetail',array('datatype'=>'number'));
        $this->arrCarDetail['price'] = array('priceDetail',array('datatype'=>'number'));
        $this->arrCarDetail['taxpercentage'] = array('taxPercentageDetail',array('datatype'=>'number')); 
        $this->arrCarDetail['taxvalue'] = array('taxValueDetail',array('datatype'=>'number')); 
        $this->arrCarDetail['total'] = array('subtotalDetail',array('datatype'=>'number'));
        $this->arrCarDetail['ispriceincludetax'] = array('chkIncludeTaxDetail'); 
        //$this->arrCarDetail['istax23'] = array('chkIsTax23Detail');
        $this->arrCarDetail['tax23percentage'] = array('tax23PercentageDetail');
        $this->arrCarDetail['tax23value'] = array('tax23ValueDetail');
   


        $this->arrCostCargoDetail = array();
        $this->arrCostCargoDetail['pkey'] = array('hidCostDetailCargoKey');
        $this->arrCostCargoDetail['refkey'] = array('hidCargoDetailKey','ref');
        $this->arrCostCargoDetail['refheaderkey'] = array('pkey','ref');
        $this->arrCostCargoDetail['costkey'] = array('hidCargoCostKey',array('mandatory' => true));  
        $this->arrCostCargoDetail['price'] = array('costDetailCargo',array('datatype'=>'number'));
        $this->arrCostCargoDetail['sellingprice'] = array('sellingCostDetailCargo',array('datatype'=>'number'));
        $this->arrCostCargoDetail['ismultipliedqty'] = array('isMultipliedQty');

        $this->arrCargoCostDetail = array();
        array_push($this->arrCargoCostDetail, array('dataset' => $this->arrCostCargoDetail, 'tableName' =>  $this->tableWorkOrderCostCargoDetail)); 
       

        $this->arrCargoDetail = array(); 
        $this->arrCargoDetail['pkey'] = array('hidCargoDetailKey', array('dataDetail' => $this->arrCargoCostDetail));
        $this->arrCargoDetail['refkey'] = array('pkey','ref');
        $this->arrCargoDetail['destination'] = array('destinationCargo');
        $this->arrCargoDetail['workorder'] = array('workOrderCargo');
        $this->arrCargoDetail['qty'] = array('qtyDetailCargo',array('datatype'=>'number'));
        $this->arrCargoDetail['unitkey'] = array('selUnitCargo');
        $this->arrCargoDetail['amount'] = array('amountCargo',array('datatype'=>'number'));
        $this->arrCargoDetail['sellingamount'] = array('sellingAmountCargo',array('datatype'=>'number'));
        $this->arrCargoDetail['destinationkey'] = array('hidDestinationDetailKey');


        $this->arrJobProgressDetail = array();
        $this->arrJobProgressDetail['pkey'] = array('hidJobProgressDetailKey');
        $this->arrJobProgressDetail['refkey'] = array('pkey','ref');
        $this->arrJobProgressDetail['number'] = array('jobProgressNumber','number');
        $this->arrJobProgressDetail['jobprogresskey'] = array('hidJobProgressKey',array('mandatory'=>true));
        $this->arrJobProgressDetail['jobprogressheaderkey'] = array('hidJobProgressHeaderKey',array('mandatory'=>true));
        $this->arrJobProgressDetail['completeddate'] = array('trDateCompleted','date');
        $this->arrJobProgressDetail['iscompleted'] = array('chkIsCompleted','number'); 
       
        // REFCASHOUTKEY jgn disave dr form, karena bisa bentrok kalo form lg dibuka, terus kas keluar dicancel, lalu form disave.
        //$this->arrDataDetail['refcashoutkey'] = array('hidRefCashOutKey');
     	
        $this->arrDetails = array();
        array_push($this->arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableCost));
        array_push($this->arrDetails, array('dataset' => $this->arrCarDetail, 'tableName' => $this->tableWorkOrderCarDetail));
        array_push($this->arrDetails, array('dataset' => $this->arrCargoDetail, 'tableName' => $this->tableWorkOrderCargoDetail));
   
        // kalo ad JobProgress saja
        if($this->activeModule['jobprogress'])
            array_push($this->arrDetails, array('dataset' => $this->arrJobProgressDetail, 'tableName' => $this->tableWorkOrderJobProgressDetail));
              
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey',array('dataDetail' => $this->arrDetails));

	    $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['stuffingdatetime'] = array('trDateStuffing','date');
        $this->arrData['stuffingaddress'] = array('stuffingAddress');
        $this->arrData['refkey'] = array('hidSOKey'); 
        $this->arrData['refdetailkey'] = array('hidSODetailKey'); 
        $this->arrData['itemkey'] = array('hidItemKey'); 
        $this->arrData['driverkey'] = array('hidDriverKey'); 
        $this->arrData['codriverkey'] = array('hidCoDriverKey'); 
        $this->arrData['carkey'] = array('hidCarKey');  
        $this->arrData['chassiskey'] = array('hidChassisKey'); 
        $this->arrData['containernumber'] = array('containerNumber'); 
        $this->arrData['container2number'] = array('container2Number'); 
        $this->arrData['sealnumber'] = array('sealNumber'); 
        $this->arrData['seal2number'] = array('seal2Number'); 
        $this->arrData['outsourcecarregistrationnumber'] = array('outsourceCarRegistrationNumber'); 
        $this->arrData['outsourcecost'] = array('outsourceCost','number'); 
        $this->arrData['outsourcecostoutstanding'] = array('outsourceCostOutstanding','number'); 
        $this->arrData['outsourcedownpayment'] = array('outsourceDownpayment','number'); 
        $this->arrData['outsourceap'] = array('outsourceAP','number');  
        $this->arrData['downpaymentemployeekey'] = array('hidDownpaymentRecipientKey');    
        $this->arrData['depotkey'] = array('hidDepotKey'); 
        $this->arrData['terminalkey'] = array('hidTerminalKey'); 
        $this->arrData['cargotypekey'] = array('hidCargoTypeKey'); 
        $this->arrData['categorykey'] = array('hidCategoryKey'); 
        $this->arrData['routefrom'] = array('routeFrom'); 
        $this->arrData['routeto'] = array('routeTo'); 
        $this->arrData['isoutsource'] = array('chkIsOutsource'); 
        $this->arrData['supplierkey'] = array('hidSupplierKey'); 
        $this->arrData['plannerkey'] = array('hidPlannerKey'); 
        $this->arrData['jobtypekey'] = array('selJobType'); 
        $this->arrData['warehousekey'] = array('selWarehouseKey'); 
        $this->arrData['locationkey'] = array('hidLocationKey'); 
        $this->arrData['trdesc'] = array('trDesc'); 
        $this->arrData['statuskey'] = array('selStatus');  
        $this->arrData['purchaseorderkey'] = array('hidPurchaseOrderKey');
        $this->arrData['productdesc'] = array('productDescription');
        $this->arrData['drivercommission'] = array('driverCommission','number');
        $this->arrData['codrivercommission'] = array('codriverCommission','number');
        $this->arrData['ispriceincludetax'] = array('chkIncludeTax'); 
        $this->arrData['taxpercentage'] = array('taxPercentage','number'); 
        $this->arrData['taxvalue'] = array('taxValue','number');
        $this->arrData['total'] = array('total','number');
        $this->arrData['verificationcode'] = array('verificationCode'); 
        $this->arrData['customerkey'] = array('hidCustomerKey'); 
        $this->arrData['consigneekey'] = array('hidConsigneeKey');
        $this->arrData['cargoqty'] = array('cargoQty');
        $this->arrData['cargoqtyunit'] = array('cargoQtyUnit');
        $this->arrData['cargoweight'] = array('cargoWeight');
        $this->arrData['cargoweightunit'] = array('cargoWeightUnit');
        $this->arrData['length'] = array('length');
        $this->arrData['width'] = array('width');
        $this->arrData['height'] = array('height');
        $this->arrData['replacementcarkey'] = array('hidReplacementCarKey');
        $this->arrData['aju'] = array('aju');
        $this->arrData['routelast'] = array('routeLast');
        $this->arrData['outsourcecarcategorykey'] = array('hidOutsourceCarCategoryKey');
        $this->arrData['codrivernotes'] = array('coDriverNotes');
        $this->arrData['drivernotes'] = array('driverNotes');
        $this->arrData['netweight'] = array('netWeight');
        $this->arrData['netweightunit'] = array('netWeightUnit');

        $this->refAutoCode = array( 'param' => 'hidSOKey', 'refField' => 'refkey');
            
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'stuffingDateTime','title' => 'stuffingAndDestuffingDateTime','dbfield' => 'stuffingdatetime','default'=>true, 'width' => 120, 'align' =>'center', 'format' => 'datetime'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobOrderCode','title' => 'jobOrder','dbfield' => 'serviceordercode','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'aju','title' => 'AJU PEB/PIB','dbfield' => 'aju','default'=>false, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'services','title' => 'services','dbfield' => 'containername','default'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobType','title' => 'jobType','dbfield' => 'jobtypename', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'si','title' => 'si','dbfield' => 'donumber','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'consignee','title' => 'consignee','dbfield' => 'consigneename','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'TL','title' => 'TL','dbfield' => 'outsourceicon','default'=>true, 'width' => 50,'align' => 'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bookingNumber','title' => 'bookingNumber','dbfield' => 'shipmentnumber',  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouseName','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'route','title' => 'route','dbfield' => 'route',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'depot','title' => 'depot','dbfield' => 'depotname',  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'terminal','title' => 'terminal','dbfield' => 'terminalname',  'width' => 100)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'locationname',  'width' => 100)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'stuffingAddress','title' => 'address','dbfield' => 'stuffingaddress',  'width' => 250)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'carRegistrationNumber','title' => 'carRegistrationNumber','dbfield' => 'policenumber', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplierName','title' => 'supplier','dbfield' => 'outsourcename', 'width' => 150));
  
              
        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate')); 
        array_push($this->arrSearchColumn, array('Job Order', $this->tableServiceOrderHeader. '.code')); 
        array_push($this->arrSearchColumn, array('Job Order', $this->tableServiceOrderHeader. '.donumber')); 
        array_push($this->arrSearchColumn, array('Job Order', $this->tableServiceOrderHeader. '.shipmentnumber'));   
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer. '.name')); 
        array_push($this->arrSearchColumn, array('Consginee', $this->tableConsignee. '.name')); 
        array_push($this->arrSearchColumn, array('Item', $this->tableItem. '.name')); 
        array_push($this->arrSearchColumn, array('Container', $this->tableName. '.containernumber')); 
        array_push($this->arrSearchColumn, array('Container', $this->tableName. '.container2number')); 
        array_push($this->arrSearchColumn, array('Seal', $this->tableName. '.sealnumber')); 
        array_push($this->arrSearchColumn, array('Seal', $this->tableName. '.seal2number')); 
        array_push($this->arrSearchColumn, array('Total', $this->tableName. '.grandtotal'));
        array_push($this->arrSearchColumn, array('No. Polisi', $this->tableCar. '.policenumber'));
        array_push($this->arrSearchColumn, array('Supir', $this->tableEmployee. '.name'));
        array_push($this->arrSearchColumn, array('Progress', $this->tableTruckingJob. '.name'));
        array_push($this->arrSearchColumn, array('Catatan', $this->tableName. '.trdesc'));
        array_push($this->arrSearchColumn, array('Depot', $this->tableDepot. '.name'));
        array_push($this->arrSearchColumn, array('Termianl', $this->tableTerminal. '.name'));
        array_push($this->arrSearchColumn, array('Rute Asal', $this->tableName. '.routefrom'));
        array_push($this->arrSearchColumn, array('Rute Tujuan', $this->tableName. '.routeto'));
        array_push($this->arrSearchColumn, array('Pemasok', 'vendor.name'));


        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printWorkOrder', 'name' => $this->lang['printWorkOrder'],  'icon' => 'print', 'url' => 'print/truckingServiceWorkOrder'));
        array_push($this->printMenu,array('code' => 'printComplete', 'name' => $this->lang['printSummary'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderCompleteFromWO'));
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
         
       
       if( in_array(DOMAIN_NAME, array('praja.wintera.co.id')) ) { 
            $this->actionMenu = array();  
            $function = '  
                    var phpDataListFile = tabParam[selectedTabId].phpDataListFile; 

                    if (selectedPkey.length == 0){
                        showMsgDialog ("Anda belum memilih data yang hendak di sinkronisasikan."); 
                        break ;
                    }

                    var msg =  "Anda yakin akan melakukan sinkronisasi GPS ?";

                    $( "#dialog-message" ).html(msg);
                    $( "#dialog-message" ).dialog({
                      width: 300,
                      modal: true,
                      title:"Konfirmasi Sinkronisasi", 
                      open: function() {
                          $(this).closest(\'.ui-dialog\').find(\'.ui-dialog-buttonpane button:last\').focus();
                      },
                      buttons : {
                          OK : function (){
                                    
                                     setRowToLoadingState(selectedTabId, selectedPkey);
                                     
                                     $.ajax({
                                        type: "POST",
                                        url:  phpDataListFile,
                                        data:{action:"resyncgps", 
                                            selectedPkey:selectedPkey
                                        },
                                    }).done(function( data ) {  
                                          generateDataRow(selectedTabId, selectedPkey);  
                                    });  

                                    $( this ).dialog( "close" );
                          },
                          Cancel : function (){ 
                            $( this ).dialog( "close" );
                          }
                      },
                      });
            ';

            array_push($this->actionMenu,array('code' => 'resyncGPS', 'name' => $this->lang['syncGPS'],  'icon' => 'resync', 'function' => $function)); 
       }
            
            
        $this->includeClassDependencies(array(
              'Supplier.class.php',  
              'TruckingServiceOrder.class.php',
              'TruckingCostCashOut.class.php',
              'APEmployeeCommission.class.php',
              'APEmployeeCommissionPayment.class.php',
              'CarTurnover.class.php',
              'WorkProgress.class.php',
              'WorkProgressStep.class.php',
              'Warehouse.class.php',
              'Supplier.class.php',
              'Service.class.php',
              'Terminal.class.php',
              'Location.class.php',
              'Depot.class.php',
              'Port.class.php',
              'TruckingJob.class.php',
              'Car.class.php',
              'Chassis.class.php', 
              'CostRate.class.php', 
              'AP.class.php', 
              'APPayment.class.php',  
              'APPayableTax23.class.php',
              'Downpayment.class.php',
              'SupplierDownpayment.class.php',
              'Employee.class.php',
              'GPSConnection.class.php',
			  'GPS.class.php',
			  'COALink.class.php',
              'JobProgress.class.php'
        ));
        
        
	   	$this->consigneeFromWorkOrder = false;
       
        if($this->activeModule['truckingpurchase']){
              $this->includeClassDependencies(array( 
                 'TruckingPurchase.class.php',
                ));
        }
            
		$this->overwriteConfig(); 
       
   }
   
   function getQuery(){
	      
	   $sql = '
			SELECT '.$this->tableName.'.* ,  
			   concat('.$this->tableName.'.routefrom, \' - \', '.$this->tableName.'.routeto) as route ,
			   '.$this->tableStatus.'.status as statusname ,
			   '.$this->tableEmployee.'.name as drivername ,
			   '.$this->tableCar.'.code as policecode ,
			   '.$this->tableCar.'.policenumber ,
			   '.$this->tableCarCategory.'.name as carcategoryname ,
			   '.$this->tableChassis.'.chassisnumber ,
			   '.$this->tableCustomer.'.pkey as customerkey ,
			   '.$this->tableCustomer.'.name as customername ,
               '.$this->tableServiceOrderHeader.'.code as serviceordercode,
               '.$this->tableServiceOrderHeader.'.trdate as serviceorderdate,
               '.$this->tableServiceOrderHeader.'.shipmentnumber,
               '.$this->tableServiceOrderHeader.'.donumber,
               '.$this->tableConsignee.'.name as consigneename,
               '.$this->tableConsignee.'.warehousename as warehouseconsigneename,
               '.$this->tableConsignee.'.address, 
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableItem.'.name as containername,
               '.$this->tableDepot.'.name as depotname,
               '.$this->tableTerminal.'.name as terminalname,
               '.$this->tableCategory.'.pkey as categorykey , 
               '.$this->tableCategory.'.code as categorycode , 
               '.$this->tableCategory.'.name as categoryname , 
			   '.$this->tableCargoType.'.name as cargotype  , 
               '.$this->tableTruckingJob.'.name as jobtypename,
               '.$this->tableServiceOrderDetail.'.priceinunit,
               '.$this->tableServiceOrderDetail.'.isgroup,
               '.$this->tableServiceOrderDetail.'.qtyinbaseunit, 
               '.$this->tableLocation.'.name as locationname,
               '.$this->tableSupplier.'.name as vehiclepartnersname,
               vendor.name as outsourcename,
               IF(isoutsource=1, "TL", "") as TL,
               IF(isoutsource=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as outsourceicon,
               outsourcecarcategory.name as outsourcecarcategoryname
			FROM 
                '.$this->tableStatus.', 
                '.$this->tableServiceOrderHeader.'
                    left join '.$this->tableConsignee.' on '.$this->tableServiceOrderHeader.'.consigneekey = '.$this->tableConsignee.'.pkey,
                '.$this->tableItem.',   
                '.$this->tableCustomer.',    
                '.$this->tableWarehouse.',  
                '.$this->tableCategory.',  
                '.$this->tableCargoType.',   
                '.$this->tableTruckingJob.',   
                '.$this->tableName.' 
                    left join '.$this->tableServiceOrderDetail.' on '.$this->tableName.'.refdetailkey = '.$this->tableServiceOrderDetail.'.pkey 
                    left join '.$this->tableDepot.' on '.$this->tableName.'.depotkey = '.$this->tableDepot.'.pkey 
                    left join '.$this->tableTerminal.' on '.$this->tableName.'.terminalkey = '.$this->tableTerminal.'.pkey   
                    left join '.$this->tableEmployee.' on '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey
                    left join '.$this->tableCar.' on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey   
                    left join '.$this->tableCarCategory.' on '.$this->tableCar.'.categorykey = '.$this->tableCarCategory.'.pkey   
                    left join '.$this->tableSupplier.' on '.$this->tableCar.'.supplierkey = '.$this->tableSupplier.'.pkey   
                    left join '.$this->tableSupplier.' vendor on '.$this->tableName.'.supplierkey = vendor.pkey   
                    left join '.$this->tableChassis.' on '.$this->tableName.'.chassiskey = '.$this->tableChassis.'.pkey
                    left join '.$this->tableLocation.' on  '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey
                    left join '. $this->tableCarCategory .' outsourcecarcategory on '. $this->tableName .'.outsourcecarcategorykey = outsourcecarcategory.pkey
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                  '.$this->tableName.'.refkey = '.$this->tableServiceOrderHeader.'.pkey  and 
                  '.$this->tableName.'.cargotypekey = '.$this->tableCargoType.'.pkey and 
                  '.$this->tableName.'.categorykey  = '.$this->tableCategory.'.pkey and 
                  '.$this->tableName.'.itemkey  = '.$this->tableItem.'.pkey and
                  '.$this->tableName.'.jobtypekey  = '.$this->tableTruckingJob.'.pkey and
                  '.$this->tableServiceOrderHeader.'.customerkey = '.$this->tableCustomer.'.pkey 
 		' .$this->criteria ; 
       
	     $sql .=  $this->getWarehouseCriteria() ;
	   
       return $sql;
    }  
    
    
     function afterAddDataOnCopy($pkey, $oldkey){ 
        //untuk logol
         $truckingServiceOrder = new TruckingServiceOrder();
        $truckingServiceOrder->updateDetailContainer($pkey);   
    }
 
    function afterUpdateData($arrParam, $action){
        $truckingServiceOrder = new TruckingServiceOrder();
        
        $pkey = $arrParam['pkey'];
        $rsHeader = $this->getDataRowById($pkey);
         
//        $this->setTimeLog("start updateTruckingCostCashOut",true);
        $this->updateTruckingCostCashOut($rsHeader[0]['pkey']); 
        
//        $this->setTimeLog("start updateContainer",true);
	    $truckingServiceOrder->updateContainer($rsHeader[0]['refkey']);    
        
//        $this->setTimeLog("start updateSalesWorkOrderCost",true);
        $truckingServiceOrder->updateSalesWorkOrderCost($rsHeader[0]['refkey']);
        
//        $this->setTimeLog("start updateWOActivityDate",true);
        $truckingServiceOrder->updateWOActivityDate($rsHeader[0]['refkey']);


        //untuk logol
        $truckingServiceOrder->updateDetailContainer($rsHeader[0]['pkey']);  

		// untuk model Praja yg ada selling di SPK
		// kalo ad cargo detail aj, agar lebih aman ke yg lain
		if($this->loadSetting('multidropWorkOrder') == 1 && in_array($rsHeader[0]['statuskey'], array(2,3)))  
			$truckingServiceOrder->updateSellingCost($rsHeader[0]['pkey']); 
    
        //update driver progress status
        if($this->activeModule['jobprogress']) {
            $this->updateJobProgresStatus($rsHeader[0]['pkey']);
        }
        
    }
    
    function generateWorkOrderQR($arrContent){   
        // pake arrContent utk jaga2 kalo mau lebih complex
        $this->createQR($arrContent['code'],10, array('useStorage' => true));  
    }
    
    
    function updateTruckingCostCashOut($pkey){
          
        //header harus reload ulang, karena status sudah berubah (ketika konfirmasi)
        $rsHeader = $this->getDataRowById($pkey);
        
        $truckingCostCashOut = new TruckingCostCashOut();
         
        $isMultidropWorkOrder = $this->loadSetting('multidropWorkOrder');
        $isOutsource = $rsHeader[0]['isoutsource'];
        $driverkey = $rsHeader[0]['driverkey'];
        
        
        // get all listed employee
        $arrEmployeeKey = array();  
        
        $sql = 'select distinct(employeekey) as employeekey from ' . $this->tableCost .' where refkey = '.$this->oDbCon->paramString($pkey).' and employeekey <> 0 ';
        $rsDetailEmployee = $this->oDbCon->doQuery($sql);
        $arrEmployeeKey = array_column($rsDetailEmployee, 'employeekey' );
        
        // add outsource downpayment recipient HERE 
        if ($rsHeader[0]['outsourcedownpayment'] > 0) 
            array_push($arrEmployeeKey,$rsHeader[0]['downpaymentemployeekey']);
       
        
        
        // buat nambah driver ke list penerima cash out
        // gk jelas kepake ap gk
        if($isOutsource == 0 && !empty($driverkey) && !in_array($driverkey, $arrEmployeeKey))
            array_push($arrEmployeeKey,$driverkey);
         
        
        /*
        foreach($rsDetailEmployee as $employee)
            if (!empty($employee) && !in_array($employee,$arrEmployeeKey ))
                array_push($arrEmployeeKey,$employee);
        */
        
        
        $rsKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
        
        // utk  delete karyawan yg sudah gk ad kas keluarnya
        $employeeCriteria = (!empty($arrEmployeeKey)) ? '  and reftabletype = '.$rsKey['key'].' and employeekey not in ('.implode(',',$arrEmployeeKey).') ' : ''; 
        
        //$rsCashOut = $truckingCostCashOut->searchData('','',true, $employeeCriteria.' and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and '.$truckingCostCashOut->tableName.'.statuskey = 1');
        $rsCashOut = $truckingCostCashOut->searchData('','',true, $employeeCriteria.' and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and '.$truckingCostCashOut->tableName.'.statuskey = 1 and '.$truckingCostCashOut->tableName . '.iscostcargo = 0 ');
	
	    for($i=0;$i<count($rsCashOut);$i++) { 
            $this->cancelCashOut($pkey,$rsCashOut[$i]['employeekey']);  
        }
         
        // kalo status konfirmasi baru lanjut proses
        // kenapa harus yg statuskeynya 2 ? kalo 1 atau 3 problem gk ?
        
        if ($rsHeader[0]['statuskey'] == 2) {
             // ini jika employee kosong dan HANYA utk bukan TL, masalah
            // hanya jika semua gk ad supplier sama sekali
            // atau ad yg tdk diisi karyawannya / sopirnya 
            $sql = 'select employeekey from ' . $this->tableCost .' where refkey = '.$this->oDbCon->paramString($pkey).' and employeekey = 0 ';
            $rsEmptyRecipient = $this->oDbCon->doQuery($sql); 
            if(!empty($rsEmptyRecipient))   
               array_push($arrEmployeeKey,0);

            /*       
            if (empty($arrEmployeeKey)) 
                array_push($arrEmployeeKey,0);*/


            //$this->setLog($arrEmployeeKey,true);

            // update ulang kas keluar  
            for($i=0;$i<count($arrEmployeeKey);$i++){
                $employeeKey = $arrEmployeeKey[$i];

                // cost di SPK 
                
                $rsCost = $this->getCostDetail($rsHeader[0]['pkey'],'',' and '. $this->tableCost .'.refcashoutkey = 0 and '. $this->tableCost .'.realizationkey = 0 and '. $this->tableCost .'.supplierkey = 0 and '. $this->tableCost .'.employeekey = '.$this->oDbCon->paramString($employeeKey).' ');

                if ($employeeKey == $rsHeader[0]['downpaymentemployeekey'] && $rsHeader[0]['outsourcedownpayment'] > 0 && $rsHeader[0]['refcashoutdownpaymentkey'] == 0){
                    $arrDP = array(); 
                    $arrDP['qty'] = 1;
                    $arrDP['costkey'] = DEFAULT_COST['outsourceDownpayment'];
                    $arrDP['employeekey'] = $employeeKey;
                    $arrDP['requestamount'] = $rsHeader[0]['outsourcedownpayment']; 
                    $arrDP['total'] = $rsHeader[0]['outsourcedownpayment'];  
                    // $arrDP['amount'] = $rsHeader[0]['outsourcedownpayment'];  // <--- ini gk boleh ad realisasi, nabrak, karena gk ad form nya
                    array_push($rsCost,$arrDP);
                } 

                $workOrderCost =array();
                for($j=0;$j<count($rsCost);$j++){
                    //array_push($workOrderCost,$rsCost[$j]['requestamount']);  // <-- sebelum ad qty pake ini
                    array_push($workOrderCost,$rsCost[$j]['pkey']);
                    array_push($workOrderCost,$rsCost[$j]['total']);
                    array_push($workOrderCost,$rsCost[$j]['costkey']);      
                } 
                $workOrderCost = md5(json_encode($workOrderCost));

                // cost di cash out yg masi pending 
                //$rsCashOut = $truckingCostCashOut->searchData('','',true,'  and reftabletype = '.$rsKey['key'].' and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and '.$truckingCostCashOut->tableName.'.employeekey = '.$this->oDbCon->paramString($employeeKey).' and '.$truckingCostCashOut->tableName.'.statuskey = 1');
                $rsCashOut = $truckingCostCashOut->searchData('','',true,'  and reftabletype = '.$rsKey['key'].' and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and '.$truckingCostCashOut->tableName.'.employeekey = '.$this->oDbCon->paramString($employeeKey).' and '.$truckingCostCashOut->tableName.'.statuskey = 1 and '.$truckingCostCashOut->tableName.'.iscostcargo = 0');               
                
                $rsCashOutDetail = (!empty($rsCashOut)) ? $truckingCostCashOut->getDetailById($rsCashOut[0]['pkey']) : array(); //ambil salah satu cashout aja
                
                $cashOutDetail = array();
                for($j=0;$j<count($rsCashOutDetail);$j++){
                    array_push($cashOutDetail,$rsCashOutDetail[$j]['refheadercostkey']); 
                    array_push($cashOutDetail,$rsCashOutDetail[$j]['amount']); 
                    array_push($cashOutDetail,$rsCashOutDetail[$j]['costkey']);      
                }
                $cashOutDetail = md5(json_encode($cashOutDetail));

                $compareResult = ($cashOutDetail == $workOrderCost) ? true : false;

                // kenapa harus yg statuskeynya 2 ? kalo 1 atau 3 problem gk ?
                //$this->setLog($rsHeader[0]['statuskey']);
                if($rsHeader[0]['statuskey'] == 2 && !$compareResult){     
                    $this->cancelCashOut($pkey,$employeeKey);    
                    $this->addCashOut($rsHeader,$rsCost);   
                } 
            }  
        }
 
        //untuk cargo cost   
        if($isMultidropWorkOrder == 1) {
            $this->updateTruckingCostCashOutCargo($pkey);
        }
        
         
       
    }


function updateTruckingCostCashOutCargo($pkey) {

        $rsHeader = $this->getDataRowById($pkey);
        
        $truckingCostCashOut = new TruckingCostCashOut();

        $recipientkey = $rsHeader[0]['driverkey']; // sementara dari driverkey, sedangkan yg di detail bawah, ikut modul UpdateTruckingCostCashOut biasa
     
        
        $rsKey = $this->getTableKeyAndObj($this->tableName, array('key'));

    // nanti direvisi, ini gk bisa pake planner, kalo cargo nempelnya ke sopir PRAJA,
    // nanti coba cari criteria lain
        $arrEmployeeKey = array();
        array_push($arrEmployeeKey, $recipientkey);

//        //utk  delete karyawan yg sudah gk ad kas keluarnya
//        $employeeCriteria = (!empty($arrEmployeeKey)) ? ' and reftabletype = ' . $rsKey['key'] . ' and employeekey not in (' . implode(',', $arrEmployeeKey) . ') ' : '';
//        // $this->setLog($employeeCriteria . ' '. '---', true);
//        $rsCashOuts = $truckingCostCashOut->searchData('', '', true, $employeeCriteria . ' and ' . $truckingCostCashOut->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $truckingCostCashOut->tableName . '.statuskey = 1 and ' .$truckingCostCashOut->tableName . '.iscostcargo = 1');
//        //$this->setLog($rsCashOuts, true);
//        for ($i = 0; $i < count($rsCashOuts); $i++) {
//            $this->cancelCashOutCargo($pkey, $rsCashOuts[$i]['employeekey']);    
//        }

        if ($rsHeader[0]['statuskey'] == 2) {

            //Cargo Cost
            $rsCargo = $this->getCargoDetail($rsHeader[0]['pkey']);
            $arrCargoCostKey = array_column($rsCargo, 'pkey');
            $rsCargoCost = $this->getCargoCostDetail($arrCargoCostKey, $rsHeader[0]['pkey'], '', ' and ' . $this->tableWorkOrderCostCargoDetail.'.refcashoutkey = 0');

            $workOrderCargoCost =array();
            for($k=0;$k<count($rsCargoCost);$k++){
               // $this->setLog($price, true);
                array_push($workOrderCargoCost,$rsCargoCost[$k]['pkey']);
                array_push($workOrderCargoCost,$rsCargoCost[$k]['price']);
                array_push($workOrderCargoCost,$rsCargoCost[$k]['costkey']);      
            } 
//            $this->setLog($workOrderCargoCost, true);
            $workOrderCargoCost = md5(json_encode( $workOrderCargoCost));
            
            //cost di cash out yg masi pending 
//       $this->setLog('start >>',true);
            $rsCashOut = $truckingCostCashOut->searchData('','',true,' 
                        and '.$truckingCostCashOut->tableName.'.reftabletype = '.$rsKey['key'].' 
                        and '.$truckingCostCashOut->tableName.'.employeekey = '. $this->oDbCon->paramString($recipientkey) .' 
                        and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' 
                        and '.$truckingCostCashOut->tableName.'.statuskey = 1 and '.$truckingCostCashOut->tableName.'.iscostcargo = 1');
            

//       $this->setLog('end >>',true);
            //$this->setLog($rsCashOut, true);
//            $this->setLog('  and reftabletype = '.$rsKey['key'].' and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and '.$truckingCostCashOut->tableName.'.employeekey = '. $this->oDbCon->paramString($recipientkey) .' and '.$truckingCostCashOut->tableName.'.statuskey = 1 and '.$truckingCostCashOut->tableName.'.iscostcargo = 1', true);
            
            $rsCashOutDetail = (!empty($rsCashOut)) ? $truckingCostCashOut->getDetailById($rsCashOut[0]['pkey']) : array(); //ambil salah satu cashout aja
            //$this->setLog($rsCashOutDetail, true);
            $cashOutDetail = array();
            for($j=0;$j<count($rsCashOutDetail);$j++){
                array_push($cashOutDetail,$rsCashOutDetail[$j]['refheadercostkey']); 
                array_push($cashOutDetail,$rsCashOutDetail[$j]['costvalue']); 
                array_push($cashOutDetail,$rsCashOutDetail[$j]['costkey']);      
            }
        
//            $this->setLog($cashOutDetail, true);
            $cashOutDetail = md5(json_encode($cashOutDetail));
            
            $compareResult = ($cashOutDetail == $workOrderCargoCost) ? true : false;
        
            // kenapa harus yg statuskeynya 2 ? kalo 1 atau 3 problem gk ?
                //$this->setLog($rsHeader[0]['statuskey']);
            if($rsHeader[0]['statuskey'] == 2 && !$compareResult){     
                $this->cancelCashOutCargo($pkey,$recipientkey);    
                $this->addCashOutCargo($rsHeader, $rsCargoCost);   
            } 

        }
   }
    
    
    function editData($arrParam){ 

          $rsHeader = $this->getDataRowById($arrParam['hidId']);
          if ($rsHeader[0]['statuskey'] <> 1){ 
                unset($this->arrData['code']); 
                unset($this->arrData['refkey']); 
                unset($this->arrData['refdetailkey']); 
                unset($this->arrData['itemkey']); 
                unset($this->arrData['trdate']); 
                unset($this->arrData['stuffingdatetime']); 
                unset($this->arrData['depotkey']); 
                unset($this->arrData['warehousekey']); 
                unset($this->arrData['terminalkey']); 
                unset($this->arrData['locationkey']); 
                unset($this->arrData['categorykey']); 
                unset($this->arrData['routefrom']); 
                unset($this->arrData['routeto']); 
                unset($this->arrData['plannerkey']); 
                unset($this->arrData['jobtypekey']);  
         }
 
          return parent::editData($arrParam);    
	}
	     
     function validateForm($arr,$pkey = ''){  
        $truckingCost = new Service(TRUCKING_SERVICE,1);
        $truckingServiceOrder = new TruckingServiceOrder();    
         
		$arrayToJs = parent::validateForm($arr,$pkey);  
         
		$sokey = $arr['hidSOKey'];  
//		$pokey = $arr['hidPurchaseOrderKey'];  
	
         
        $rsSOHeader = $truckingServiceOrder->getDataRowById($sokey);

		$isoutsource = $arr['chkIsOutsource'];
        $supplierkey = $arr['hidSupplierKey'];  
        $driverkey = $arr['hidDriverKey']; 
        $codriverkey = $arr['hidCoDriverKey']; 
        $carkey = $arr['hidCarKey']; 
        $chassiskey = $arr['hidChassisKey']; 
        $refdetailkey = $arr['hidSODetailKey']; 
        $itemkey = $arr['hidItemKey']; 
        $arrCostKey = $arr['hidCostKey']; 
        $employeekey = $arr['hidEmployeeDetailKey']; 
        $supplierDetailKey = $arr['hidSupplierDetailKey']; 
        $locationkey = $arr['hidLocationKey']; 
        $cargotypekey = $arr['hidCargoTypeKey'];   
        $consigneekey = $rsSOHeader[0]['consigneekey'];   
        $requestAmount = $arr['requestAmount'];
        $outsourceCost = $this->unformatNumber($arr['outsourceCost']);
        $outsourceDownpayment = $this->unformatNumber($arr['outsourceDownpayment']);
        $driverCommission = $this->unformatNumber($arr['driverCommission']);
        $codriverCommission = $this->unformatNumber($arr['codriverCommission']);
        $warehousekey = $arr['selWarehouseKey'];
        $categorykey = $arr['hidCategoryKey'];
        $jobtype = $arr['selJobType'];
        $replacementcarkey = $arr['hidReplacementCarKey'];
        $plannerkey = $arr['hidPlannerKey'];
           
        // karena kalo status konfirmasi, jobType disabled
		 
        for($i=0;$i<count($arrCostKey);$i++){  
            
            $amount =  $this->unformatNumber($requestAmount[$i]); 
			
            if(empty($arrCostKey[$i]) && $amount > 0 ){   
                $this->addErrorList($arrayToJs,false, $this->errorMsg['cost'][1]); 
                return $arrayToJs;
            }
            
            // langsung return agar gk error di bawah
        }  

         
        $rsCost = array(); 
        //$costKeyCriteria = implode(',',$arrCostKey); 
        if (!empty($arrCostKey)){  
            $rsCost = $truckingCost->searchData('','',true,' and '.$truckingCost->tableName.'.pkey in ('.$this->oDbCon->paramString($arrCostKey,',').')');
            $rsCost = array_column($rsCost,null,'pkey');
        }
         
        if($outsourceCost < $outsourceDownpayment) 
              $this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceWorkOrder'][8]); 
          
        // utk EDIT form 
		if (!empty($pkey)){
			
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] > 2) 
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
		 
            if ($rs[0]['statuskey'] > 1){
                for($i=0;$i<count($arrCostKey);$i++){ 
                    if((!empty($employeekey[$i]) && !empty($supplierDetailKey[$i])) ){ 
                        //$rsCost = $truckingCost->getDataRowById($arrCostKey[$i]);
                        $this->addErrorList($arrayToJs,false,$rsCost[$arrCostKey[$i]]['name'].'. ' .$this->errorMsg['truckingServiceWorkOrder'][6]); 
                    }
                }  
            } 
			
			
				// cek kalo ad modul purchase, dan sudah ad purchase order nya 
			 
			if($this->activeModule['truckingpurchase']){
			  
					$rsCarCost = $this->getCarDetail($pkey);
					$rsSPKCost = $this->getCostDetail($pkey);

					$rsSPKCostCol = array_column($rsSPKCost,null,'pkey'); //khusus spk cost

					//khusus SPK Cost  
					foreach($arr['hidDetailKey'] as $key=>$costDetailkey){

						if (!isset($rsSPKCostCol[$costDetailkey])) continue;
							
						$rsSPKCosDetail = $rsSPKCostCol[$costDetailkey];

						if($rsSPKCosDetail['qtyinvoiced'] > 0){ 
								//spk cost versi non logol qty pasti 1 jadi harus pakai total
								// cek nama charges tidak boleh berubah
								//cek request biaya apakah masih sama
								if($arr['totalCostDetail'][$key] <> $rsSPKCosDetail['total'] || 
								   $arr['hidCostKey'][$key] <> $rsSPKCosDetail['costkey'] || 
								   $this->unformatNumber($arr['requestAmount'][$key]) <> $rsSPKCosDetail['requestamount']
								  ) 
									
								 $this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceWorkOrder'][15]);  
							
							
								// hanya jika sebelumnya sudah pilih supplier, terus mau ganti supplier
								// kalo pertama nya bkn supplier, berarti kena validasinya di TCO
								if($rsSPKCosDetail['supplierkey'] <> 0 && $arr['hidSupplierDetailKey'][$key] <> $rsSPKCosDetail['supplierkey'])  
								 $this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceWorkOrder'][16]);  
					   }

					}

					$rsCarCostCol = array_column($rsCarCost,null,'pkey'); 
				   //khusus spk car detail
				   //khusus biaya mobil 
					foreach($arr['hidOutsourceVehicleDetailKey'] as $key=>$carDetailkey){
							 $rsCarCosDetail = $rsCarCostCol[$carDetailkey];

							if($rsCarCosDetail['qtyinvoiced'] > 0){

								// cek total berubah ga
								//cek item / jenis mobil harus sama
								if($arr['subtotalDetail'][$key] <> $rsCarCosDetail['total'] || 
								   $arr['hidItemKey'] <> $rsCarCosDetail['itemkey']
								  ) 
										$this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceWorkOrder'][15]); 

							}

						}

			}
    
			
		}
		 
		if(empty($sokey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['jobOrder'][1]);
		} 
         
        if(empty($categorykey) || empty($refdetailkey) || empty($itemkey) || empty($cargotypekey) || empty($jobtype)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['jobType'][1]);
		} 
          
         
        if (isset($arr['islinked']) && $arr['islinked']){ 
            // utk validasi dr JO
            // dari JO, harusnya gk mungkin outsource,.... skrg mungkin :')
            
            /*
            if($isoutsource == 1){
                 if(empty($supplierkey)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
                 } 
            }else{
                
                 if(empty($driverkey)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['driver'][1]);
                 } 
                 if(empty($carkey)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][1]);
                 } 
                 if(empty($chassiskey)){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['chassis'][1]);
                 } 
                 
            }
            */
        }else{
            
            if($isoutsource == 1){
                if(empty($supplierkey)) 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
                
                // deprecated
//                $truckingPurchaseOrder = new TruckingPurchaseOrder();
//                $rsPurchaseHeader = $truckingPurchaseOrder->getDataRowById($pokey); 
//                
//                if (!empty($rsPurchaseHeader)){
//                    if($rsPurchaseHeader[0]['supplierkey'] <> $supplierkey)
//                        $this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceWorkOrder'][4]); 
//                }
               
            }else{
                if (isset($rs) && $rs[0]['statuskey'] > 1){ 
                     if(empty($driverkey)) 
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['driver'][1]); 
                     if(empty($carkey)) 
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][1]);  
                    
                    //kalo ad komisi kenek. nama kenek harus diisi
                    if( $codriverCommission > 0 && empty($codriverkey)){
                         $this->addErrorList($arrayToJs,false,$this->errorMsg['codriver'][1]); 
                    }

                    if(!empty($replacementcarkey) && ($replacementcarkey == $carkey)) {
                        $this->addErrorList($arrayToJs, false, $this->errorMsg['truckingServiceWorkOrder'][18]);  
                    }
                } 
            } 
         
        }
         
        // validasi jumlah komisi gk boleh melebihi quota 
        // validasi jumlah fixed cost gk boleh melebihi quota
         
        $strictMode = $this->loadSetting('costRateStrict'); // sementara baru untuk komisi sopir
         
        $costRate = new CostRate();
        $rsCostRate = $costRate->getCostDetail($warehousekey, $locationkey, $cargotypekey, $jobtype, $itemkey, 0, $consigneekey);
        $rsCostRate = array_column($rsCostRate,'price','costkey');

        $rsDriverCommission = $costRate->getDriverCommissionRate($warehousekey, $locationkey, $cargotypekey, $jobtype,$itemkey, $consigneekey );
        $rsDriverCommission = array_column($rsDriverCommission,'price','costkey');
             
         // driver
         if($driverCommission > 0){
            $tempValid = true;

             if($strictMode == 1){ 
                 if (!isset($rsDriverCommission[-1]))  $tempValid = false;
                 else if ($driverCommission > $rsDriverCommission[-1]) $tempValid = false;
             }else{
                  if (isset($rsDriverCommission[-1]) && $rsDriverCommission[-1] > 0 && $driverCommission > $rsDriverCommission[-1]) $tempValid = false;
             }

            if(!$tempValid)
                $this->addErrorList($arrayToJs,false, $this->lang['driverCommission'] .'. '.$this->errorMsg['truckingServiceWorkOrder'][7] . ' (' . $this->formatNumber($rsDriverCommission[-1]).')');  
         }
            
         // codriver
         if($codriverCommission > 0){
             $tempValid = true;

             if($strictMode == 1){ 
                 if (!isset($rsDriverCommission[-2]))  $tempValid = false;
                 else if ($codriverCommission > $rsDriverCommission[-2]) $tempValid = false;
             }else{
                  if (isset($rsDriverCommission[-2]) && $rsDriverCommission[-2] > 0 && $codriverCommission > $rsDriverCommission[-2])  $tempValid = false;
             }

            if(!$tempValid)
                $this->addErrorList($arrayToJs,false, $this->lang['codriverCommission'] .'. '.$this->errorMsg['truckingServiceWorkOrder'][7] . ' (' . $this->formatNumber($rsDriverCommission[-2]).')');  

         }
       
                  
        //akumulasikan semua biaya yg sama
        $arrRequestAmount = array(); 
        for($i=0;$i<count($requestAmount);$i++){
            $requestAmount[$i] = $this->unformatNumber($requestAmount[$i]);
            
            if ($requestAmount[$i] <= 0)  continue; 
            $costkey = $arrCostKey[$i];
            if(!$rsCost[$costkey]['fixedcost']) continue;

            if (!isset($arrRequestAmount[$costkey])) $arrRequestAmount[$costkey] = 0;

            $arrRequestAmount[$costkey] += $requestAmount[$i];
        }  

        foreach($arrRequestAmount as $costkey=>$amount){  
            if (!isset($rsCostRate[$costkey])) continue;
 
            if ($amount > $rsCostRate[$costkey] ) 
                 $this->addErrorList($arrayToJs,false, $rsCost[$costkey]['name'] .'. '.$this->errorMsg['truckingServiceWorkOrder'][7] .' (' . $this->formatNumber($rsCostRate[$costkey]).')');  

        } 

		// model PRAJA
        //cek kalau harga jual di JO salah satu sudah di invoice, maka tidak bisa di edit
		// validasi hanya jika ketika edit, pas ADD harusnya selalu bisa (karena blm terbentuk selling cost nya)
		 if (  $this->loadSetting('multidropWorkOrder') == 1  && !empty($pkey) ){ 
				$rsSellingCost = $truckingServiceOrder->getSellingCostDetail($sokey, ' and '. $truckingServiceOrder->tableSellingCost.'.workorderkey = ' . $this->oDbCon->paramString($pkey). ' 
																					   and '. $truckingServiceOrder->tableSellingCost.'.qtyinvoiced > 0'); 
				for($i=0; $i<count($rsSellingCost); $i++)  
					 $this->addErrorList($arrayToJs,false, '<strong>'. $rsSellingCost[$i]['itemname'] .'</strong>. '. $this->errorMsg['truckingServiceWorkOrder'][17]);
			 
		 }

        // ========================= JOB PROGRESS
        if ($this->activeModule['jobprogress']){

            $trDateCompleted = $arr['trDateCompleted'];
            $arrJobProgressName = $arr['jobProgressName'];
            $arrJobProgressNumber = $arr['jobProgressNumber'] ?? array();

            $arrErrMsg = [];

            $total = count($arrJobProgressNumber);
            for ($i = 0; $i < $total; $i++) {
                
                if (empty(trim($trDateCompleted[$i]))) continue;

    
                $currentDate = DateTime::createFromFormat('d / m / Y H:i', trim($trDateCompleted[$i]));
                if (!$currentDate) {
                    $arrErrMsg[] = '<strong>'.$arrJobProgressName[$i].'. </strong>' . 'Format tanggal tidak valid.';
                    continue;
                }

                if ($i > 0 && !empty(trim($trDateCompleted[$i - 1]))) {
                    $prevStr = preg_replace('/\s+/', ' ', trim($trDateCompleted[$i - 1]));
                    $prevDate = DateTime::createFromFormat('d / m / Y H:i', $prevStr);

                    if ($prevDate && $currentDate < $prevDate) {
                        $arrErrMsg[] = '<strong>'.$arrJobProgressName[$i].'. </strong>' . $this->errorMsg['truckingServiceWorkOrder'][22] . ' '.strtolower($this->lang['from']).' <strong>'.$arrJobProgressName[$i - 1].'</strong>';
                    }
                }
            }

            if (!empty($arrErrMsg)) {
                $this->addErrorList($arrayToJs, false, implode('<br>', $arrErrMsg));
            }

        }

        // ========== END JOB PROGRESS


		return $arrayToJs;
	 }
	     
    function getCostDetail($pkey, $costkey = '', $criteria = '', $orderby = ''){
        // gk boleh tambahkan biaya DP Outsouce
        // karena nanti akan pengaruh ke perhitungan biaya di JO dsb
         
        $sql = 'select 
	   			'.$this->tableItem .'.name,
	   			'.$this->tableItem .'.code as itemcode,
	   			'.$this->tableItem .'.isneeddocument,
	   			'.$this->tableItem .'.reimburse,
	   			'.$this->tableSupplier .'.code as suppliercode,
	   			'.$this->tableSupplier .'.name as suppliername,
	   			'.$this->tableEmployee .'.code as employeecode,
	   			'.$this->tableEmployee .'.name as employeename,
	   			'.$this->tableEmployee .'.cashbankcoakey,
	   			'.$this->tableItem .'.fixedcost,
                '.$this->tableTruckingCostCashOut.'.code as refcashoutcode,
	   			'.$this->tableCost .'.* 
			  from
			  	'.$this->tableName.',  
			  	'.$this->tableCost.' 
                    left join '.$this->tableSupplier.' on '.$this->tableCost.'.supplierkey = '.$this->tableSupplier.'.pkey
                    left join '.$this->tableEmployee.' on '.$this->tableCost.'.employeekey = '.$this->tableEmployee.'.pkey
                    left join '.$this->tableTruckingCostCashOut.' on '.$this->tableCost.'.refcashoutkey = '.$this->tableTruckingCostCashOut.'.pkey,
			  	'.$this->tableItem.' 
                
			  where 
                '.$this->tableCost.'.costkey =  '.$this->tableItem .'.pkey and   
                '.$this->tableName.'.pkey =  '.$this->tableCost .'.refkey and   
                '.$this->tableName.'.pkey in ('. $this->oDbCon->paramString($pkey,',') .')' ; 
        
          
        if (!empty($costkey)) 
            $sql .= ' and '. $this->tableCost .'.costkey = '. $this->oDbCon->paramString($costkey);
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($orderby))
            $sql .= ' ' . $orderby; 
        
		$rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }


    function getJobProgressDetail($refkey, $reffield = 'refkey', $criteria = '')
    {
        $sql = '
            select
                '.$this->tableWorkOrderJobProgressDetail.'.*,
                '.$this->tableJobProgressHeader.'.code as jobprogresscode,
                '.$this->tableJobProgressDetail.'.name as jobprogressname,
                '.$this->tableJobProgressDetail.'.needpod
            from
                '.$this->tableWorkOrderJobProgressDetail.'
                left join '.$this->tableJobProgressDetail.' on '.$this->tableWorkOrderJobProgressDetail.'.jobprogresskey = '.$this->tableJobProgressDetail.'.pkey
                left join '.$this->tableJobProgressHeader.' on '.$this->tableWorkOrderJobProgressDetail.'.jobprogressheaderkey = '.$this->tableJobProgressHeader.'.pkey
            where
                '.$this->tableWorkOrderJobProgressDetail.'.'.$reffield.' in  ('.$this->oDbCon->paramString($refkey,',') . ') 
        ';

        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 

    
        $sql .= ' order by '.$this->tableWorkOrderJobProgressDetail.'.number asc '; 
        
		$rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }

    function getCargoDetail($pkey) {
        $sql = 'select
	   			' . $this->tableWorkOrderCargoDetail . '.*,
                '.$this->tableLocation.'.name as destinationname
                
            from
			  	' . $this->tableWorkOrderCargoDetail . '
                    left join '.$this->tableLocation.' on '.$this->tableWorkOrderCargoDetail.'.destinationkey = '.$this->tableLocation.'.pkey,
                ' . $this->tableName . ' 
			where 
                ' . $this->tableName . '.pkey = ' . $this->tableWorkOrderCargoDetail . '.refkey and
			  	' .  $this->tableWorkOrderCargoDetail. '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        //$sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }



    function getCargoCostDetail($pkey=array(),$headerkey=array(), $costkey = array(), $criteria = '', $orderby = '')
    {   
        $sql = '
            select 
				'. $this->tableWorkOrderCargoDetail.'.destination,
				'. $this->tableWorkOrderCargoDetail.'.workorder,
				'. $this->tableWorkOrderCargoDetail.'.qty,
                '. $this->tableLocation.'.name as destinationname,
                '. $this->tableWorkOrderCostCargoDetail.'.*
            from
                '.$this->tableName.', 
                '. $this->tableWorkOrderCargoDetail.'
                left join '.$this->tableLocation.' on '.$this->tableWorkOrderCargoDetail.'.destinationkey = '.$this->tableLocation.'.pkey, 
                '.$this->tableWorkOrderCostCargoDetail.'
            where
                '.$this->tableName.'.pkey = '.$this->tableWorkOrderCargoDetail.'.refkey and
                '. $this->tableWorkOrderCargoDetail.'.pkey = '.$this->tableWorkOrderCostCargoDetail.'.refkey
            ';
		
			if(!empty($pkey))
				$sql .= ' and ' . $this->tableWorkOrderCostCargoDetail.'.refkey in ('.$this->oDbCon->paramString($pkey,',').')'; 
			else if (!empty($headerkey))
				$sql .=  ' and '. $this->tableWorkOrderCostCargoDetail.'.refheaderkey in ('.$this->oDbCon->paramString($headerkey,',').')'; 
				
            if (!empty($costkey))
				$sql .= ' and '.$this->tableWorkOrderCostCargoDetail.'.costkey in ('.$this->oDbCon->paramString($costkey,',').')'; 
        
            if (!empty($criteria))  
                $sql .=  ' ' .$criteria; 
        
            if (!empty($orderby))
                $sql .= ' ' . $orderby; 

       // $this->setLog($sql,true);
            return $this->oDbCon->doQuery($sql);
    }
    
    function getCarDetail($pkey){ 
       
	   $sql = 'select
	   			'.$this->tableWorkOrderCarDetail .'.*, 
	   			'.$this->tableItem.'.name as itemname
                
              from
			  	'.$this->tableWorkOrderCarDetail .', 
                '.$this->tableName.',
			  	'.$this->tableItem .' 
			  where 
                '.$this->tableName .'.pkey = '.$this->tableWorkOrderCarDetail .'.refkey and
			  	'.$this->tableWorkOrderCarDetail .'.itemkey = '.$this->tableItem .'.pkey and 
			  	'.$this->tableWorkOrderCarDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';
       
        //$sql .= $criteria;
           
		return $this->oDbCon->doQuery($sql);
	
   }
        
    function getTruckingCost($id, $criteria = '') {
         
        $sql =  
            'select  
                '.$this->tableTruckingCostCashOutDetail.'.* ,
                '.$this->tableTruckingCostCashOut.'.pkey as cashoutkey ,
                '.$this->tableTruckingCostCashOut.'.refkey as cashoutworkorderkey ,
                '.$this->tableTruckingCostCashOut.'.code as cashoutcode ,
                '.$this->tableTruckingCostCashOut.'.trdate as cashoutdate,
                '.$this->tableItem.'.name as costname  
            FROM 
                '.$this->tableTruckingCostCashOut.',
                '.$this->tableTruckingCostCashOutDetail.',
                '.$this->tableItem.'
            WHERE
                '.$this->tableTruckingCostCashOut.'.pkey = '.$this->tableTruckingCostCashOutDetail.'.refkey and
                '.$this->tableTruckingCostCashOutDetail.'.costkey = '.$this->tableItem.'.pkey and
                '.$this->tableTruckingCostCashOut.'.refkey = '.$this->oDbCon->paramString($id).' and
                '.$this->tableTruckingCostCashOut.'.statuskey in (2,3,4)
                ';
        
        
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria;
         
        return $this->oDbCon->doQuery($sql);
    }
    
    
    function updateGLOutsource($rs){ 
        
        if (!USE_GL) return;
     
        //kalo diaktfin, gl yg sebelumny dari updateGLCommission 
//        $this->cancelGLByRefkey($rs[0]['pkey'],$this->tableName); 
        
        $truckingServiceOrder = new TruckingServiceOrder(); 
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();  
        $generalJournal = new GeneralJournal();
        $supplier = new Supplier();
        $item = new Item();
        $cost = new Service(TRUCKING_SERVICE,1); 
		
        $warehousekey = $rs[0]['warehousekey'];  
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName,array('key'));
         
		$temp = -1;  
        $rsCost = $this->getCostDetail($rs[0]['pkey'],'',' and '. $this->tableCost .'.supplierkey <> 0');  
	    $rsCOA = $coaLink->getCOALink ('outsourcecost', $warehouse->tableName, $warehousekey);  
        $coakey = $rsCOA[0]['coakey'];
        
        $isPriceIncludeTax = $rs[0]['ispriceincludetax'];
        $outsourceCost  = ($isPriceIncludeTax) ? ($rs[0]['outsourcecost'] - $rs[0]['taxvalue']) : $rs[0]['outsourcecost'];
             
        
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
		$arr['refCode'] = $rs[0]['code'];
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
         
        //desc 
         
        //trucking type
        $arrItemName = array();
        $rsItem = $item->searchDataRow( array($item->tableName.'.name'),
                                        ' and '.$item->tableName.'.pkey in ('.$this->oDbCon->paramString($rs[0]['itemkey']).')'
                                    );
        $arrItemName = array_merge($arrItemName,array_column($rsItem,'name')); 
         
        // cost
        $rsItem = $cost->searchDataRow( array($cost->tableName.'.pkey',$cost->tableName.'.name',$cost->tableName.'.costcoakey'),
                                        ' and '.$cost->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsCost,'costkey'),',').')'
                                    );
        $arrItemName= array_merge($arrItemName,array_column($rsItem,'name'));
        
        $arrItemCostCOAKey = array_column($rsItem,'costcoakey','pkey');
          
        $rsJo = $truckingServiceOrder->getDataRowById($rs[0]['refkey']);
            
        $desc = array();
        array_push($desc,$rsJo[0]['code']);
        array_push($desc,$rsSupplier[0]['name']); 
        array_push($desc,$this->lang['truckingFee'].' '.implode(', ',$arrItemName)); 
		$arr['trDesc'] = implode(chr(13),$desc);   
        
        // cost outsource 
        $temp++;
        $arr['hidCOAKey'][$temp] = $coakey;
        $arr['debit'][$temp] = $outsourceCost; 
        $arr['credit'][$temp] = 0; 
         
        $outsourceDownpayment = $rs[0]['outsourcedownpayment'] ;
    
        if ($outsourceDownpayment > 0){ 
            //$rsCostDP = $cost->getDataRowById( DEFAULT_COST['outsourceDownpayment'] ); 
            $rsCostDP = $coaLink->getCOALink ('supplierdownpayment', $warehouse->tableName, $warehousekey);  
            
            $temp++; 
            $arr['hidCOAKey'][$temp] = $rsCostDP[0]['coakey'];
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $outsourceDownpayment; 
        } 
 
        $rsCOA = $coaLink->getCOALink ('taxin', $warehouse->tableName, $warehousekey);  
        $coakey = $rsCOA[0]['coakey'];
        
        $temp++;
        $arr['hidCOAKey'][$temp] = $coakey ;
        $arr['debit'][$temp] = $rs[0]['taxvalue']; 
        $arr['credit'][$temp] = 0; 
       
 
        $temp++; 
        $arr['hidCOAKey'][$temp] =  $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $rs[0]['outsourceap']; //+ $rs[0]['taxvalue'];  

         
        //other cost
        $rsCOA = $coaLink->getCOALink ('operationalcost', $warehouse->tableName, $warehousekey);  
        $coakey = $rsCOA[0]['coakey'];
        
        $rsCOA = $coaLink->getCOALink ('taxin', $warehouse->tableName, $warehousekey);  
        $taxcoakey = $rsCOA[0]['coakey']; 
          
        for($i=0;$i<count($rsCost);$i++){
            if (empty($rsCost[$i]['supplierkey']))
                continue;
             
            $costkey = $rsCost[$i]['costkey'];
            
            // karena hutang, sudah pasti langusng jd biaya
            $amount =  $rsCost[$i]['qty'] * $rsCost[$i]['requestamount'];
            $taxAmount = $rsCost[$i]['taxvalue'];
            
            $temp++;
            $arr['hidCOAKey'][$temp] = $cost->getCostCOAKeyByJobCategory($costkey,$rsJo[0]['categorykey'],$warehousekey); //(!empty($arrItemCostCOAKey[$costkey])) ? $arrItemCostCOAKey[$costkey] : $coakey;
            $arr['debit'][$temp] = $amount; 
            $arr['credit'][$temp] = 0; 
            
            $temp++;
            $arr['hidCOAKey'][$temp] = $taxcoakey;
            $arr['debit'][$temp] =  $taxAmount; 
            $arr['credit'][$temp] = 0; 
            
            //akun hutang vendor 
            $temp++; 
            $arr['hidCOAKey'][$temp] =  $supplier->getAPCOAKey($rsCost[$i]['supplierkey'],$warehousekey);
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $amount + $taxAmount; 

        }
        
		$arrayToJs = $generalJournal->addData($arr); 
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
     
    } 
     
  function updateGLCommission($rs){
        if (!USE_GL) return;
     
        //kalo diaktfin, gl yg sebelumny dari updateGLOutsource 
//        $this->cancelGLByRefkey($rs[0]['pkey'],$this->tableName); 
        
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();  
        $generalJournal = new GeneralJournal();
        $employee = new Employee();
        $cost = new Service(TRUCKING_SERVICE,1); 
		
        $warehousekey = $rs[0]['warehousekey']; 
            
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName,array('key')); 
	    $rsCOA = $coaLink->getCOALink ('commissioncost', $warehouse->tableName, $warehousekey);
        
        if($rs[0]['drivercommission'] > 0){
            $arr = array();
            $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
            $arr['code'] = 'xxxxx';
            $arr['refkey'] = $rs[0]['pkey'];
            $arr['refTableType'] = $rsKey['key'];
            $arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
            $arr['refCode'] = $rs[0]['code'];
            $arr['selWarehouseKey'] = $rs[0]['warehousekey'];

            $temp = -1;   

            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = $rs[0]['drivercommission']; 
            $arr['credit'][$temp] = 0;
            
            $coakey = $employee->getAPCommissionCOAKey($rs[0]['driverkey'],$warehousekey);
            
            //akun hutang 
            $temp++; 
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $rs[0]['drivercommission'];  
 
            $arrayToJs = $generalJournal->addData($arr); 

            if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 

        }
        
        if($rs[0]['codrivercommission'] > 0){
            
            $arr = array();
            $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
            $arr['code'] = 'xxxxx';
            $arr['refkey'] = $rs[0]['pkey'];
            $arr['refTableType'] = $rsKey['key'];
            $arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
            $arr['refCode'] = $rs[0]['code'];

            $temp = -1;   
 
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = $rs[0]['codrivercommission']; 
            $arr['credit'][$temp] = 0;
             
            $coakey = $employee->getAPCommissionCOAKey($rs[0]['codriverkey'],$warehousekey);
            
            //akun hutang 
            $temp++; 
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $rs[0]['codrivercommission']; 

            $arrayToJs = $generalJournal->addData($arr); 

            if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 
       
        }
      
    } 
    
    function addCashOut($rsHeader,$rsCost){
        
        if (empty($rsCost))
            return;
        
        $truckingCostCashOut = new TruckingCostCashOut();
        $truckingServiceOrder = new TruckingServiceOrder();
        $warehouse = new Warehouse();
        $coaLink = new COALink();
	    $employee = new Employee();
        
        // kalo ad planner dan ad cashbankcoakey, pake kas planner
        $recipientkey = (!empty($rsCost[0]['employeekey'])) ? $rsCost[0]['employeekey'] : $rsHeader[0]['driverkey'];
   
        $coakey = 0;
        if (!empty($rsHeader[0]['plannerkey'])){
            $rsEmployee = $employee->getDataRowById($rsHeader[0]['plannerkey']);
            if(!empty($rsEmployee[0]['cashbankcoakey']))
                $coakey = $rsEmployee[0]['cashbankcoakey'];
        }
        
        if(empty($coakey)){
            $rsCOALink = $coaLink->getCOALink ('cashbankops', $warehouse->tableName, $rsHeader[0]['warehousekey'],0); 
            $coakey = $rsCOALink[0]['coakey'];
        }        
        
        $arrParam = array();	
        $totalCashOut = 0; 
        $rsSO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refkey']);
        
        for($i=0;$i<count($rsCost);$i++){ 
            //if(empty($rsCost[$i]['costkey'])) continue;
            $arrParam['hidDetailKey'][$i] = 0;
            $arrParam['refheadercostkey'][$i] = $rsCost[$i]['pkey'];
            $arrParam['hidCostKey'][$i] = $rsCost[$i]['costkey'];
            $arrParam['hidCOAKey'][$i] = $coakey;
            $arrParam['qty'][$i] =  $rsCost[$i]['qty']; // harus bedain, klao di SPK qty nya 0, dari yg model lama, harus diupdate 1 kah ?
            $arrParam['costValue'][$i] =  $rsCost[$i]['requestamount'];
            $arrParam['amount'][$i] = $rsCost[$i]['total'];
            $arrParam['detailDesc'][$i] = '';
            $totalCashOut = $totalCashOut+$rsCost[$i]['total']; 
        }
         
        $arrParam['code'] = 'xxxxxx';
        $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
        $arrParam['refCode'] = $rsHeader[0]['code'];
        $arrParam['hidRefKey2'] = $rsSO[0]['pkey'];
        $arrParam['refCode2'] = $rsSO[0]['code'];
        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        $arrParam['hidEmployeeKey'] = $recipientkey; 
        $arrParam['trDesc'] = $rsHeader[0]['trdesc'];
        $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
        // $arrParam['islinked'] = 1;
        // gk boleh, karena bisa ganti driver / planner
        $arrParam['subtotal'] = $totalCashOut; 
        $arrParam['total'] = $totalCashOut; 
        $rsCashOutKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
        $arrParam['hidRefTable'] = $rsCashOutKey['key']; 
        

        $rsEmployee = $employee->getDataRowById($recipientkey);
        $arrParam['recipientMobile'] = (isset($rsEmployee[0]['mobile'])) ? $rsEmployee[0]['mobile'] : '';
        $arrParam['recipientBankName'] = (isset($rsEmployee[0]['bankname'])) ? $rsEmployee[0]['bankname'] : '';  
        $arrParam['recipientBankAccountName'] = (isset($rsEmployee[0]['bankaccountname'])) ? $rsEmployee[0]['bankaccountname'] : '';  
        $arrParam['recipientBankAccountNumber'] = (isset($rsEmployee[0]['bankaccountnumber'])) ? $rsEmployee[0]['bankaccountnumber'] : ''; 
  
        $arrParam['isCostCargo'] = 0;
        $arrayToJs = $truckingCostCashOut->addData($arrParam); 

        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']); 
     
    }

    function addCashOutCargo($rsHeader, $rsCargoCost) 
    {

        if (empty($rsCargoCost))
            return;

        $truckingCostCashOut = new TruckingCostCashOut();
        $truckingServiceOrder = new TruckingServiceOrder();
        $warehouse = new Warehouse();
        $coaLink = new COALink();
	    $employee = new Employee();


        // kalo ad planner dan ad cashbankcoakey, pake kas planner
        // $plannerkey = $rsHeader[0]['plannerkey']; // ganti jd nama driver dulu
        $plannerkey = $rsHeader[0]['driverkey'];

        $coakey = 0;
        if (!empty($rsHeader[0]['plannerkey'])) {
            $rsEmployee = $employee->getDataRowById($rsHeader[0]['plannerkey']);
            if (!empty($rsEmployee[0]['cashbankcoakey']))
                $coakey = $rsEmployee[0]['cashbankcoakey'];
        }

        if (empty($coakey)) {
            $rsCOALink = $coaLink->getCOALink('cashbankops', $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);
            $coakey = $rsCOALink[0]['coakey'];
        }

        $arrParam = array();	
        $totalCashOut = 0; 
        $rsSO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refkey']);

        $index = 0; //index array $arrParam, agar index berurut
         
        for($j=0; $j<count($rsCargoCost); $j++) {
            
            // kalo amountnya 0, di skip
            $amount = $rsCargoCost[$j]['price'];
            if ($amount  <= 0) continue;
        
            
            
            //if(empty($rsCost[$i]['costkey'])) continue;
            $arrParam['hidDetailKey'][$index] = 0;
            $arrParam['refheadercostkey'][$index] = $rsCargoCost[$j]['pkey'];
            $arrParam['hidCostKey'][$index] = $rsCargoCost[$j]['costkey'];
            $arrParam['hidCOAKey'][$index] = $coakey;
            $arrParam['costValue'][$index] =  $rsCargoCost[$j]['price'];
                 
                
            $qty = 1; //default tidak multiplied
            if($rsCargoCost[$j]['ismultipliedqty'] == 1) {
                $amount = $rsCargoCost[$j]['qty'] * $rsCargoCost[$j]['price'];
                $qty = $rsCargoCost[$j]['qty'];
            }

            $detailDesc = array();
            if(!empty($rsCargoCost[$j]['workorder'])) array_push($detailDesc, $rsCargoCost[$j]['workorder']);

            $destination = ($rsCargoCost[$j]['destination'] ?: $rsCargoCost[$j]['destinationname']);
            if(!empty($destination)) 
                array_push($detailDesc, $destination);
            $arrParam['qty'][$index] =  $qty; 
            $arrParam['amount'][$index] = $amount;
            $arrParam['detailDesc'][$index] = implode(', ',$detailDesc);
            $totalCashOut = $totalCashOut+$amount; 
            
            $index++;
        }

        $arrParam['code'] = 'xxxxxx';
        $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
        $arrParam['refCode'] = $rsHeader[0]['code'];
        $arrParam['hidRefKey2'] = $rsSO[0]['pkey'];
        $arrParam['refCode2'] = $rsSO[0]['code'];
        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        $arrParam['hidEmployeeKey'] = $plannerkey; 
        $arrParam['trDesc'] = '';
        $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
        // $arrParam['islinked'] = 1;
        // gk boleh, karena bisa ganti driver / planner
        $arrParam['subtotal'] = $totalCashOut; 
        $arrParam['total'] = $totalCashOut; 
        $rsCashOutKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
        $arrParam['hidRefTable'] = $rsCashOutKey['key']; 

        $rsEmployee = $employee->getDataRowById($plannerkey);
        $arrParam['recipientMobile'] = (isset($rsEmployee[0]['mobile'])) ? $rsEmployee[0]['mobile'] : '';
        $arrParam['recipientBankName'] = (isset($rsEmployee[0]['bankname'])) ? $rsEmployee[0]['bankname'] : '';  
        $arrParam['recipientBankAccountName'] = (isset($rsEmployee[0]['bankaccountname'])) ? $rsEmployee[0]['bankaccountname'] : '';  
        $arrParam['recipientBankAccountNumber'] = (isset($rsEmployee[0]['bankaccountnumber'])) ? $rsEmployee[0]['bankaccountnumber'] : ''; 
        $arrParam['isCostCargo'] = 1;

        if(!empty($arrParam['hidDetailKey'])){
            $arrayToJs = $truckingCostCashOut->addData($arrParam);
    
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']); 

        }
         
    }

    function cancelCashOutCargo($workOrderKey,$employeekey='')
    {
        // delete cash out
        $truckingCostCashOut = new TruckingCostCashOut();
        $rsCashOutKey = $this->getTableKeyAndObj($this->tableName);
        $employeeCriteria = '';

        if ($employeekey === 0 || $employeekey !== '')
            $employeeCriteria = ' and ' . $truckingCostCashOut->tableName . '.employeekey = ' . $this->oDbCon->paramString($employeekey) . ' ';

        $rsCashOut = $truckingCostCashOut->searchData('', '', true, ' and ' . $truckingCostCashOut->tableName . '.refkey = ' . $this->oDbCon->paramString($workOrderKey) . '  and reftabletype =  ' . $this->oDbCon->paramString($rsCashOutKey['key']) . ' and ' . $truckingCostCashOut->tableName . '.statuskey = 1 and ' . $truckingCostCashOut->tableName . '.iscostcargo = 1 ' . $employeeCriteria);
        //$this->setLog('citeria => '. $employeeCriteria,true); 

        for ($i = 0; $i < count($rsCashOut); $i++)
            $truckingCostCashOut->changeStatus($rsCashOut[$i]['pkey'], 5, '', false, true);
    }


    function addGroupVendorAP($rsHeader){
            $ap = new AP();  
            $supplier = new Supplier();  
            $warehouse = new Warehouse();
            $termOfPayment = new TermOfPayment();
            $truckingServiceOrder = new TruckingServiceOrder();
        
            $totalAP = 0;
            $top = 0;
            $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();

            $rsJO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refkey']);
            
            $arrSupplierAP = array();
            $note = array();
            array_push($note,$rsHeader[0]['code']);
         
        
            // table key hanya ambil dr SPK, 
            // table cost gk dimasukan, ini jg blm tentu kepake kalo grouping
            $rsARKey = $ap->getTableKeyAndObj($this->tableName,array('key')); 
            $refTableKey = $rsARKey['key'];
        
            if ($rsHeader[0]['outsourcecost'] > 0){
                $amount = $rsHeader[0]['outsourceap']; // biar termasuk tax valuenya
                
                $supplierkey = $rsHeader[0]['supplierkey'];  
                $rsTOP = $supplier->getTermOfPayment($supplierkey); 
                $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];

                
                $arrSupplierAP[$supplierkey] = array(
                        'amount' => $amount,
                        'top' => $top,
                        'note' => $note
                );  
                
                array_push($arrSupplierAP[$supplierkey]['note'],$this->lang['truckingFee']. ': ' . $this->formatNumber($amount));
                
                //kalo ad DP
                $downpayment = $rsHeader[0]['outsourcedownpayment'];
                if($downpayment > 0){ 
                    array_push($arrSupplierAP[$supplierkey]['note'],$this->lang['downpayment']. ': ' . $this->formatNumber($downpayment));
                }
                     
            } 
        
        
             $rsCost = $this->getCostDetail($rsHeader[0]['pkey'],'',' and '. $this->tableCost .'.supplierkey <> 0');  
             foreach($rsCost as $costRow){  
                $supplierkey = $costRow['supplierkey'];  
                if (empty($supplierkey))  continue; 
                 
                $amount = $costRow['total']; 
                if ($amount <= 0)  continue; 
                  
                 
                if (!isset($arrSupplierAP[$supplierkey])){ 

                    $arrSupplierAP[$supplierkey] = array(); 

                    $rsTOP = $supplier->getTermOfPayment($supplierkey); 
                    $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];

                    $arrSupplierAP[$supplierkey]['amount'] = 0;
                    $arrSupplierAP[$supplierkey]['top'] = $top; 
                    $arrSupplierAP[$supplierkey]['note'] = $note;
 
                }

                $arrSupplierAP[$supplierkey]['amount'] += $amount;  
                array_push($arrSupplierAP[$supplierkey]['note'],'Biaya '.$costRow['name'].': '. $this->formatNumber($amount)); 
                 
             }
             
        
            foreach($arrSupplierAP as $supplierkey=>$row){  
                $totalAP += $row['amount'];
                  
                $arrParam = array();	

                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidSupplierKey'] = $supplierkey;
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey']; 
                $arrParam['hidRefKey'] = $rsHeader[0]['pkey']; 
                $arrParam['hidRefCode'] =  $rsHeader[0]['code']; 
                $arrParam['hidRefKey2'] = $rsJO[0]['pkey']; 
                $arrParam['hidRefCode2'] = $rsJO[0]['code']; 
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                $arrParam['hidRefTable'] = $refTableKey;
                $arrParam['amount'] = $this->formatNumber($row['amount']);
                $arrParam['trDesc'] = implode(chr(13), $row['note']);
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P'.$top.'D'));
                $arrParam['dueDate'] = $date->format('d / m / Y');
                $arrParam['createdBy'] = 0;
                $arrParam['overwriteGL'] = 1;
                $arrParam['islinked'] = 1;
                $arrParam['selAPType'] = AP_TYPE['serviceOutsource'];
                $arrParam['selWarehouse'] = $warehousekey;

                $arrayToJs = $ap->addData($arrParam); 

                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);
                
                // updaterefcashoutcode
                $newData = $arrayToJs[0]['data'];
                $sql = 'update 
                            '.$this->tableCost.' 
                       	 set isrealization = 1,amount = requestamount, refcashoutkey = ' . $this->oDbCon->paramString($newData['pkey']) .' 
                         where
                            refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']).' and
                            supplierkey = ' . $this->oDbCon->paramString($supplierkey);
                $this->oDbCon->execute($sql);

                $sql = 'update 
                                '.$this->tableName.'
                        set refcashoutkey = ' . $this->oDbCon->paramString($newData['pkey']) .' 
                        where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
                
                $this->oDbCon->execute($sql);

            }
        
            // logol, nanti harus tambahin DP
            if ($totalAP > 0)
                $this->updateGLOutsource($rsHeader);
    }
    
    function addVendorAP($rsHeader){
            $ap = new AP();  
            $supplier = new Supplier();  
            $warehouse = new Warehouse();
            $termOfPayment = new TermOfPayment();
            $truckingServiceOrder = new TruckingServiceOrder();
        
            $totalAP = 0;
            $top = 0;
            $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();

            $rsJO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refkey']);
            
            $note = array();
            array_push($note,$rsHeader[0]['code']);
         
            $downpayment = $rsHeader[0]['outsourcedownpayment'];
            if ($rsHeader[0]['outsourcecost'] > 0){
                $amount = $rsHeader[0]['outsourceap']; // + $rsHeader[0]['taxvalue'] ; //- $rsHeader[0]['outsourcedownpayment'];
                
                //kalo ad DP
                if($downpayment > 0){ 
                    array_push($note,$this->lang['truckingFee']. ': ' . $this->formatNumber($rsHeader[0]['outsourcecost']));
                    array_push($note,$this->lang['downpayment']. ': ' . $this->formatNumber($downpayment));
                }
                    
                if ($amount > 0 ){
                    $rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
                    $topkey = $rsSupplier[0]['termofpaymentkey']; 
                    $rsTOP = $termOfPayment->getDataRowById($topkey);    
                    $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];
                    $totalAP += $amount;
                    
                    $rsARKey = $ap->getTableKeyAndObj($this->tableName,array('key')); 
                    $arrParam = array();	

                    $arrParam['code'] = 'xxxxxx';
                    $arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey']; 
                    $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                    $arrParam['hidRefKey2'] = $rsJO[0]['pkey']; 
                    $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                    $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                    $arrParam['hidRefCode2'] = $rsJO[0]['code'];
                    $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                    $arrParam['hidRefTable'] = $rsARKey['key'];
                    $arrParam['amount'] =  $amount;
                    $arrParam['trDesc'] = implode(chr(13),$note);
                    $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                    $date = new DateTime($rsHeader[0]['trdate']);
                    $date->add(new DateInterval('P'.$top.'D'));
                    $arrParam['dueDate'] = $date->format('d / m / Y');
                    $arrParam['createdBy'] = 0;
                    $arrParam['overwriteGL'] = 1;
                    $arrParam['islinked'] = 1;
                    $arrParam['selAPType'] = AP_TYPE['serviceOutsource'];
                    $arrParam['selWarehouse'] = $warehousekey;
 
                    $arrayToJs = $ap->addData($arrParam);

                    // updaterefcashoutcode
                    $newData = $arrayToJs[0]['data'];
                    $sql = 'update '.$this->tableName.' set refcashoutkey = ' . $this->oDbCon->paramString($newData['pkey']) .' where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
                    $this->oDbCon->execute($sql);
 
                    if (!$arrayToJs[0]['valid'])
                        throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);

                }
            } 
        
        
            $rsCost = $this->getCostDetail($rsHeader[0]['pkey'],'',' and '. $this->tableCost .'.supplierkey <> 0');  
            $rsARKey = $ap->getTableKeyAndObj($this->tableCost,array('key')); 
            for($j=0;$j<count($rsCost);$j++){  
                $amount = $rsCost[$j]['requestamount'];
                
                if ($amount <= 0)
                    continue; 
             
                $totalAP += $amount;
                
                $rsSupplier = $supplier->getDataRowById($rsCost[$j]['supplierkey']);
                $topkey = $rsSupplier[0]['termofpaymentkey']; 
                $rsTOP = $termOfPayment->getDataRowById($topkey);    
                $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];
                
                $arrParam = array();	
            
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidSupplierKey'] = $rsCost[$j]['supplierkey'];
                $arrParam['hidRefKey'] = $rsCost[$j]['pkey'];
                $arrParam['hidRefKey2'] = $rsJO[0]['pkey']; 
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefCode2'] = $rsJO[0]['code'];
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                $arrParam['hidRefTable'] = $rsARKey['key'];
                $arrParam['amount'] = $this->formatNumber($amount);//$arrParam['amount'][$i];
                $arrParam['trDesc'] = $rsHeader[0]['code'].'. Biaya '.$rsCost[$j]['name'];
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P'.$top.'D'));
                $arrParam['dueDate'] = $date->format('d / m / Y');
                $arrParam['createdBy'] = 0;
                $arrParam['overwriteGL'] = 1;
                $arrParam['islinked'] = 1;
                $arrParam['selAPType'] = AP_TYPE['serviceOutsource'];
                $arrParam['selWarehouse'] = $warehousekey;
            
                $arrayToJs = $ap->addData($arrParam); 
                
                // updaterefcashoutcode
                $newData = $arrayToJs[0]['data'];
                $sql = 'update '.$this->tableCost.' set amount = requestamount, refcashoutkey = ' . $this->oDbCon->paramString($newData['pkey']) .' where pkey = ' . $this->oDbCon->paramString($rsCost[$j]['pkey']);
                $this->oDbCon->execute($sql);
                
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);
                
            }
         
            if (($totalAP + $downpayment) > 0)
                $this->updateGLOutsource($rsHeader);
    }
    
 
    function addCommissionAP($rsHeader){
            $apEmployeeCommission = new APEmployeeCommission();   
            $warehouse = new Warehouse();
            $termOfPayment = new TermOfPayment();
            $truckingServiceOrder = new TruckingServiceOrder();
        
            $totalAP = 0;
            $top = 0;
            $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();

            $rsJO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refkey']);
            
            $note = array();
            array_push($note,$rsHeader[0]['code']);
        
			$commissionDateType = $this->loadSetting('driverCommissionBasedOn');
            $commissionDate = ($commissionDateType == 2) ? $this->formatDBDate($rsHeader[0]['trdate']) : date('d / m / Y');
            $commissionDateInDBFormat =  str_replace('\'','',$this->oDbCon->paramDate($commissionDate,' / '));
		
			$rsTOP = $termOfPayment->getDataRowById($topkey);    
			$top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];

			$date = new DateTime($commissionDateInDBFormat);
			$date->add(new DateInterval('P'.$top.'D'));
			$commissionDueDate = $date->format('d / m / Y');

            if ($rsHeader[0]['drivercommission'] > 0 || $rsHeader[0]['codrivercommission'] > 0){
                $driverCommissionAmount = $rsHeader[0]['drivercommission'];
                $codriverCommissionAmount = $rsHeader[0]['codrivercommission'];
                
                $rsARKey = $apEmployeeCommission->getTableKeyAndObj($this->tableName,array('key')); 
              
		        $totalAP = 0;
                $topkey = $termOfPayment->getDefaultData();
                if ($driverCommissionAmount > 0 ){

                    $driverNote = $note;
                    array_push($driverNote, $rsHeader[0]['drivernotes']);

                    $arrParam = array();
                    $arrParam['code'] = 'xxxxxx';
                    $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                    $arrParam['hidRefKey2'] =  $rsJO[0]['pkey'];
                    $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                    $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                    $arrParam['hidRefCode2'] =  $rsJO[0]['code'];
                    $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                    $arrParam['hidRefTable'] = $rsARKey['key'];
                    $arrParam['trDesc'] = implode(chr(13),$driverNote);
                    $arrParam['trDate'] =  $commissionDate;  

                    $arrParam['createdBy'] = 0;
                    $arrParam['overwriteGL'] = 1;
                    $arrParam['islinked'] = 1;
                    $arrParam['selAPType'] = AP_TYPE['driverCommission'];
                    $arrParam['selWarehouse'] = $warehousekey; 
                    $arrParam['dueDate'] = $commissionDueDate; 
                    $arrParam['hidEmployeeKey'] = $rsHeader[0]['driverkey']; 

                    $arrParam['amount'] =  $driverCommissionAmount;
                    $totalAP += $driverCommissionAmount;
                     
                    $arrayToJs = $apEmployeeCommission->addData($arrParam);
                     
                    $newData = $arrayToJs[0]['data'];
                    $sql = 'update '.$this->tableName.' set refcashoutdriverkey = ' . $this->oDbCon->paramString($newData['pkey']) .' where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
                    $this->oDbCon->execute($sql);
 
                    if (!$arrayToJs[0]['valid'])
                        throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);

                }
                
                if ($codriverCommissionAmount > 0 ){  
                    $coDriverNote = $note;
                    array_push($coDriverNote, $rsHeader[0]['codrivernotes']);

                    $arrParam = array();
                    $arrParam['code'] = 'xxxxxx';
                    $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                    $arrParam['hidRefKey2'] =  $rsJO[0]['pkey'];
                    $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                    $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                    $arrParam['hidRefCode2'] =  $rsJO[0]['code'];
                    $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                    $arrParam['hidRefTable'] = $rsARKey['key'];
                    $arrParam['trDesc'] = implode(chr(13),$coDriverNote);
                    $arrParam['trDate'] = $commissionDate;  

                    $arrParam['createdBy'] = 0;
                    $arrParam['overwriteGL'] = 1;
                    $arrParam['islinked'] = 1;
                    $arrParam['selAPType'] = AP_TYPE['driverCommission'];
                    $arrParam['selWarehouse'] = $warehousekey; 
                    $arrParam['dueDate'] = $commissionDueDate; 
                    $arrParam['hidEmployeeKey'] = $rsHeader[0]['codriverkey']; 

                    $arrParam['amount'] =  $codriverCommissionAmount;
                    $totalAP += $codriverCommissionAmount;

                    $arrayToJs = $apEmployeeCommission->addData($arrParam);
 
                    
                    $newData = $arrayToJs[0]['data'];
                    $sql = 'update '.$this->tableName.' set refcashoutcodriverkey = ' . $this->oDbCon->paramString($newData['pkey']) .' where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
                    $this->oDbCon->execute($sql);
                    if (!$arrayToJs[0]['valid'])
                        throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);

                }
            } 
         
            if ($totalAP > 0){ 
				// overwrite tgl
             	$rsHeader[0]['trdate'] = $commissionDateInDBFormat;
				$this->updateGLCommission($rsHeader);
			}
    } 
    
    function addCarTurnover($rsHeader){
        
            // cancel dulu semuanya, karena bisa double update kalo ad realisasi
            // di close diadd, realisasi di ad dlg
            $this->cancelCarTurnover($rsHeader);
        
            $carTurnover = new CarTurnover();   
            $warehouse = new Warehouse();
            $item = new Item();
            $truckingServiceOrder = new TruckingServiceOrder();
          
            $rsJO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refkey']);
         
            $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();

            $rsCost = $this->getCostDetail($rsHeader[0]['pkey'],'',' and '. $this->tableItem .'.reimburse = 0');
            for($i=0;$i<count($rsCost);$i++){  
                $amount = $rsCost[$i]['amount'];
                
                if ($amount == 0)  continue; 
             
                $arrParam = array();	

                
                $rsObjKey = $this->getTableKeyAndObj($this->tableName);   
                $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                $arrParam['refCode'] = $rsHeader[0]['code'];
                $arrParam['hidRefKey1'] = $rsCost[$i]['costkey'];
                $arrParam['refCode1'] = $rsCost[$i]['itemcode'];
                $arrParam['hidRefKey2'] = $rsJO[0]['pkey'];
                $arrParam['refCode2'] = $rsJO[0]['code'];
                $arrParam['joDate'] = $this->formatDBDate( $rsJO[0]['trdate']);
                $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['hidRefTable'] = $rsObjKey['key'];
                $arrParam['hidCarKey'] = $rsHeader[0]['carkey'];   
                $arrParam['amount'] = $amount * -1;    
                $arrParam['selStatus'] = 1;
                $arrParam['trDesc'] = $rsCost[$i]['name'];

                $arrayToJs =  $carTurnover->addData($arrParam); 
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);  
                
            }
        
            if(!$rsHeader[0]['isoutsource']){
                $employee = new Employee();
                $rsObjKey = $this->getTableKeyAndObj($this->tableName);   
                
                if($rsHeader[0]['drivercommission'] > 0){
                    
                    $driverName = '';
                    if(!empty($rsHeader[0]['driverkey'])){
                        $rsEmployee = $employee->getDataRowById($rsHeader[0]['driverkey']);
                        $driverName = $rsEmployee[0]['name'];
                    } 
                    
                    $arrParam = array();	
                    
                    $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                    $arrParam['refCode'] = $rsHeader[0]['code'];
                    $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                    $arrParam['joDate'] =   $this->formatDBDate($rsJO[0]['trdate']); 
                    $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                    $arrParam['hidRefTable'] = $rsObjKey['key'];
                    $arrParam['hidCarKey'] = $rsHeader[0]['carkey'];    
                    $arrParam['trDesc'] = $this->lang['driverCommission'].', '. $driverName.'.';
                    $arrParam['hidRefKey1'] = $rsEmployee[0]['pkey'];
                    $arrParam['refCode1'] = $rsEmployee[0]['code'];
                    $arrParam['hidRefKey2'] = $rsJO[0]['pkey'];
                    $arrParam['refCode2'] = $rsJO[0]['code'];
                    $arrParam['amount'] =  $rsHeader[0]['drivercommission'] * -1;
                    $arrParam['selStatus'] = 1;
                    
                    $arrayToJs =  $carTurnover->addData($arrParam); 
                    if (!$arrayToJs[0]['valid'])
                        throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    

                }
                
                if($rsHeader[0]['codrivercommission'] > 0){
                    
                    $coDriverName = '';
                    if(!empty($rsHeader[0]['codriverkey'])){ 
                        $rsEmployee = $employee->getDataRowById($rsHeader[0]['codriverkey']);
                        $coDriverName = $rsEmployee[0]['name'];
                    } 
                    
                    
                    $arrParam = array();	 
                    
                    $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                    $arrParam['refCode'] = $rsHeader[0]['code'];
                    $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
                    $arrParam['joDate'] =   $this->formatDBDate($rsJO[0]['trdate']); 
                    $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                    $arrParam['hidRefTable'] = $rsObjKey['key'];
                    $arrParam['hidCarKey'] = $rsHeader[0]['carkey'];  
                    $arrParam['trDesc'] = $this->lang['codriverCommission'].', '.$coDriverName.'.'; 
                    $arrParam['hidRefKey1'] = $rsEmployee[0]['pkey'];
                    $arrParam['refCode1'] = $rsEmployee[0]['code'];
                    $arrParam['hidRefKey2'] = $rsJO[0]['pkey'];
                    $arrParam['refCode2'] = $rsJO[0]['code'];
                    $arrParam['amount'] =  $rsHeader[0]['codrivercommission'] * -1; 
                    $arrParam['selStatus'] = 1;    
                    
                    $arrayToJs =  $carTurnover->addData($arrParam); 
                    if (!$arrayToJs[0]['valid'])
                        throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    

                }
                
            }
        
     
    } 

    function reCountOutsourceTax($arrParam){
        $taxValue = 0 ;
        $grandtotal = 0; 
        $outsourceAP = 0; 
        //$amount = 0;
         
        $truckingType = $this->loadSetting('truckingType');
        
        $outSourceCost = $this->unFormatNumber($arrParam['outsourceCost']);
        $outSourceDownpayment = $this->unFormatNumber($arrParam['outsourceDownpayment']);
        $isPriceIncludeTax = (isset($arrParam['chkIncludeTax'])) ? $arrParam['chkIncludeTax'] : 0; 
        $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);
         
        $grandtotal = $outSourceCost;
        
        if ($isPriceIncludeTax == false) {
            
             if($truckingType == 1){  
                $taxValue = $outSourceCost * $taxPercentage / 100;
             }else{
                // model logol
                $arrTaxValue = $arrParam['taxPercentageDetail'];
                $arrQtyDetail = $arrParam['qtyDetail'];
                $arrPriceDetail = $arrParam['priceDetail'];
                 
                for($i=0;$i<count($arrTaxValue);$i++){
                    $tax = $this->unFormatNumber($arrTaxValue[$i]);
                    $qty = $this->unFormatNumber($arrQtyDetail[$i]); 
                    $cost = $this->unFormatNumber($arrPriceDetail[$i]); 
                    $taxValue += ($qty * $cost * $tax / 100);  
                }
             }
            
            $grandtotal += $taxValue;
            
        }else{
            // include blm ad di logol
            if($truckingType == 1){  
                $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $outSourceCost;   
            }else{ 
             
            } 
            
        }
          
        //$outsourceAP = $grandtotal - $outSourceDownpayment;
        
        $reCountResult['taxValue'] = $taxValue; 
        $reCountResult['total'] = $grandtotal; 
        //$reCountResult['outsourceAP'] = $outsourceAP;
        
        return $reCountResult;
    }
    
    function reCountSubtotal($arrParam){ 
		
		// sementara baru utk recount kargo
		
        $cost = new Service(TRUCKING_SERVICE, 1);
        $rsTruckingCost = $cost->searchData($cost->tableName . '.statuskey', 1, true, ' and isdroppointdetailprice = 1', 'order by pkey desc, name asc');

        $cargokey = $arrParam['hidCargoDetailKey'];
        $qtyCost = $this->unFormatNumber($arrParam['qtyDetailCargo']);

        $reCountResult = array();
        $reCountResult['subtotal'] = array();
        $reCountResult['sellingSubtotal'] = array();
		
		
        for($i=0; $i < count($cargokey); $i++) {
            $subtotalRow = 0;
            $sellingSubtotalRow = 0;
            $qty = $qtyCost[$i]; 

            for($c=0; $c<count($rsTruckingCost); $c++) {

                $costCargoDetail = $this->unFormatNumber($arrParam['costCargoDetail_' . $rsTruckingCost[$c]['pkey']][$i]);
                $sellingCostCargoDetail = $this->unFormatNumber($arrParam['sellingCostCargoDetail_' . $rsTruckingCost[$c]['pkey']][$i]);
    			   
				if ( $rsTruckingCost[$c]['ismultipliedbyqty'] == 1) {
                    $subtotalRow += $qty * $costCargoDetail;
                    $sellingSubtotalRow += $qty * $sellingCostCargoDetail;
                } else {
                    $subtotalRow += $costCargoDetail;
                    $sellingSubtotalRow += $sellingCostCargoDetail;
                }

            }
            
            $reCountResult['subtotal'][$i] = $subtotalRow;
            $reCountResult['sellingSubtotal'][$i] = $sellingSubtotalRow;
        }

        return $reCountResult;
    }
    

    function normalizeParameter($arrParam, $trim=false){
        
        $cost = new Service(TRUCKING_SERVICE,1);  
        $security = new Security();
        $truckingServiceOrder = new TruckingServiceOrder();
        
         
        // ========================= JOB PROGRESS
        if ($this->activeModule['jobprogress'] && !empty($arrParam['chkIsCompleted'])){
            // pastikan tidak ad yg kelompat checklistnya
            $totalChecklist = count($arrParam['chkIsCompleted']);
            
            $completed = false;
            for($i=$totalChecklist-1;$i>=0;$i--){ 
                // jika sudah ad yg completed, maka kebelakang ahrus completed semua
                if ($arrParam['chkIsCompleted'][$i] == 1) $completed = true; 
                $arrParam['chkIsCompleted'][$i] = ($completed) ? 1 : 0;
            }
        }
        
        // ========================= AKHIR JOB PROGRESS
        
        
        // ========================= UNSET 
        
        $sellingPriceAllowed = $security->isAdminLogin($truckingServiceOrder->sellingPriceSecurityObject, 10);
        
        if(!$sellingPriceAllowed){
            unset($this->arrCostCargoDetail['sellingprice']);
            unset($this->arrCargoDetail['sellingamount']);

            $this->arrCargoCostDetail = array();
            array_push($this->arrCargoCostDetail, array('dataset' => $this->arrCostCargoDetail, 'tableName' =>  $this->tableWorkOrderCostCargoDetail)); 

            $this->arrCargoDetail['pkey'] = array('hidCargoDetailKey', array('dataDetail' => $this->arrCargoCostDetail));

            // biar gk usah reset semua
            foreach($this->arrDetails as $key=>$row){
                if ($row['tableName'] == $this->tableWorkOrderCargoDetail){
                    unset($this->arrDetails[$key]);
                    array_push($this->arrDetails, array('dataset' => $this->arrCargoDetail, 'tableName' => $this->tableWorkOrderCargoDetail)); 
                    break;
                }
            }      

            $this->arrData['pkey'] = array('pkey',array('dataDetail' => $this->arrDetails));
        }
        
 
        // =========================  AKHIR UNSET
              
        $rsJO = $truckingServiceOrder->getDataRowById($arrParam['hidSOKey']);
        $rsJODetail = $truckingServiceOrder->getDetailById($arrParam['hidSOKey']);
         
        // tampung dulu karena nanti dibawah ke overwrite
        $tempReceivedDoc = $arrParam['chkReceivedDoc'];
        
		// kalo di SPK boleh revisi consignee
		if(!isset($arrParam['hidConsigneeKey'])){
			$arrParam['hidConsigneeKey'] = $rsJO[0]['consigneekey']; 
		}
        
			
		$arrParam['hidCustomerKey'] = $rsJO[0]['customerkey'];
       

		// kalo ad detail cargo di spk
        // semetnara baru praja yg pake
		if (isset($arrParam['hidCargoDetailKey'])){
			
			     $rsTruckingCost = $cost->searchData($cost->tableName . '.statuskey', 1, true, ' and isdroppointdetailprice = 1', 'order by pkey desc, name asc');
        
                //overwrite jika ada cost rate
                 if( in_array(DOMAIN_NAME, array('prajademo.wintera.co.id','praja.wintera.co.id')) ) { 

                    $sokey = $arrParam['hidSOKey'];
                    $rsSOHeader = $truckingServiceOrder->getDataRowById($sokey);
                    $jobTypeContractKey = 8001; //key job type kontrak

                    if($rsSOHeader[0]['categorykey'] == $jobTypeContractKey) {
                        $costRate = new CostRate();
                        $car = new Car();
                        $truckingServiceOrder = new TruckingServiceOrder(); 
        
                        $jobtype = $arrParam['selJobType'];
                        $isoutsource = $arrParam['chkIsOutsource'];
                        $carkey = $arrParam['hidCarKey'] ;
                        $locationkey = $arrParam['hidDestinationDetailKey'];
                        $rsCar = $car->getDataRowById($carkey);
                        $carcategory = $rsCar[0]['categorykey'];
                        
                        if($isoutsource == 1) {
                            $carcategory = $arrParam['hidOutsourceCarCategoryKey'];
                        }
                        
                        $rsCost = $cost->searchDataRow(array($cost->tableName.'.pkey'),
                                                                        ' and '.$cost->tableName.'.statuskey = 1 
                                                                        and showincostrate = 1 and chargetype = 2',
                                                                        'order by fixedcost desc, name asc'
                                                                    );
                      
                        $rsCostRateCol = $costRate->getCostDetail($rsSOHeader[0]['warehousekey'], $locationkey, $rsSOHeader[0]['cargotypekey'], $jobtype, $carcategory , array_column($rsCost,'pkey'),$rsSOHeader[0]['consigneekey']);
         
                        $temp = [];
                        foreach ($rsCostRateCol as $row) {
                            $temp[$row['locationkey']][$row['costkey']] = $row;
                        }
                        $rsCostRateCol = $temp;
        
                        $costRateValue = array();
                        foreach ($locationkey as $lockey) {
                            $costRateValue[$lockey] = [];
        
                            for ($i = 0; $i < count($rsCost); $i++) {
                                $costKey = $rsCost[$i]['pkey'];
                                $rsCostRate = $rsCostRateCol[$lockey][$costKey] ?? null;
        
                                $costRateValue[$lockey][$costKey] = $rsCostRate['price'] ?? 0;
                            }
                        }
                        
                        for($j=0; $j<count($arrParam['hidDestinationDetailKey']); $j++) {
        
                            $destkey = $arrParam['hidDestinationDetailKey'][$j];
                            if(!isset($costRateValue[$destkey])) {
                                continue;
                            }
                            
 				for($c=0; $c<count($rsTruckingCost); $c++) { 
                                $costKey = $rsTruckingCost[$c]['pkey']; 
                                if(($rsTruckingCost[$c]['showincostrate'] == 1 && $rsTruckingCost[$c]['chargetype'] == 2)) {
 if($arrParam['costCargoDetail_' . $costKey][$j] > 0) {
                                        $arrParam['costCargoDetail_' . $costKey][$j] = $costRateValue[$destkey][$costKey]; 
                                    } 
                                }
                            }
                        }
                    }
                } 

            //End Overwrite cost rate 
            
            
			for($i=0; $i < count($arrParam['hidCargoDetailKey']); $i++) {
 
				$arrParam['hidCostDetailCargoKey'][$i] = array();
				$arrParam['hidCargoCostKey'][$i] = array();
				$arrParam['hidSellingCargoCostKey'][$i] = array();
				$arrParam['costDetailCargo'][$i] = array();
				$arrParam['sellingCostDetailCargo'][$i] = array();
			    $arrParam['isMultipliedQty'][$i] = array();

				for($c=0; $c<count($rsTruckingCost); $c++) {

					//if($arrParam['costCargoDetail_'.$rsTruckingCost[$c]['pkey']][$i] <= 0) continue;

                    $costCargoDetail = $arrParam['costCargoDetail_' . $rsTruckingCost[$c]['pkey']][$i];
                    $sellingCostDetailCargo = $arrParam['sellingCostCargoDetail_' . $rsTruckingCost[$c]['pkey']][$i];

            //        if ($costCargoDetail <= 0 && $sellingCostDetailCargo <= 0)    continue; 
                    
//					array_push($arrParam['hidCostDetailCargoKey'][$i], 0);
                    
					array_push($arrParam['hidCostDetailCargoKey'][$i], $arrParam['hidCostDetailCargoKey_'.$rsTruckingCost[$c]['pkey']][$i]);
					array_push($arrParam['hidCargoCostKey'][$i], $rsTruckingCost[$c]['pkey']);
					array_push($arrParam['costDetailCargo'][$i], $arrParam['costCargoDetail_'.$rsTruckingCost[$c]['pkey']][$i]);
					array_push($arrParam['sellingCostDetailCargo'][$i], $arrParam['sellingCostCargoDetail_'.$rsTruckingCost[$c]['pkey']][$i]);
               	    array_push($arrParam['isMultipliedQty'][$i], $rsTruckingCost[$c]['ismultipliedbyqty'] );

				}

			}
			        
			$totalCargoDetail = count($arrParam['hidCargoCostKey']);
			$arrParam['hidCostDetailCargoKeyTotalRows'] = array('1' => array('0' => $totalCargoDetail));

  			// hitung ulang subtotal 
			$reCountResult = $this->reCountSubtotal($arrParam);  
			$arrParam['amountCargo'] = $reCountResult['subtotal'];
			$arrParam['sellingAmountCargo'] = $reCountResult['sellingSubtotal'];

		}
        
		
        // default variable, khususnya utk API 
        if (!isset($arrParam['selJobType'])){ 
            
            //default
            $arrParam['selJobType'] = 1;
            
            if(isset($arrParam['pkey']) && !empty($arrParam['pkey'])){ 
                // kalo edit, karena kalo suda konfirmasi, jd disable, gk kekirim nilainya
                $rs = $this->getDataRowById($arrParam['pkey']);
                $arrParam['selJobType'] = (!empty($rs)) ? $rs[0]['jobtypekey'] : 1 ;
            } 
            
        }
        
        // biasa utk API
        if(!isset($arrParam['hidCategoryKey']))  $arrParam['hidCategoryKey'] = $rsJO[0]['categorykey']; 
        if(!isset($arrParam['hidCargoTypeKey'])) $arrParam['hidCargoTypeKey'] = $rsJO[0]['cargotypekey'];
        if(!empty($rsJODetail)){
            if(!isset($arrParam['hidSODetailKey'])) $arrParam['hidSODetailKey'] = $rsJODetail[0]['pkey']; 
            if(!isset($arrParam['hidItemKey']))  $arrParam['hidItemKey'] = $rsJODetail[0]['itemkey'];

            // pake jml mobil yg disubmit utk test
            if(isset($arrParam['carRegistration'])){
               
                if(!isset($arrParam['hidServiceDetailKey'])) $arrParam['hidServiceDetailKey'] = array();
                
                $totalVehicle = count($arrParam['carRegistration']);
                for($i=0;$i<$totalVehicle;$i++)
                   if(empty($arrParam['hidServiceDetailKey'][$i]))
                        $arrParam['hidServiceDetailKey'][$i] = $rsJODetail[0]['itemkey']; 
            } 
        }
        
        
        // hanya diupdate pertama kali
        if (empty($arrParam['hidId']))
            $arrParam['verificationCode'] = $this->generateStrongPassword(6, '', 'lud');
                        
        $rsRevalidate = $this->reValidateData($arrParam,$rsJO);    
        $arrParam['hidLocationKey'] = $rsRevalidate['hidLocationKey'];
        $arrParam['stuffingAddress'] = $rsRevalidate['stuffingAddress']; 
        $arrParam['hidCargoTypeKey'] = $rsRevalidate['hidCargoTypeKey']; 
        $arrParam['hidCategoryKey'] = $rsRevalidate['hidCategoryKey'];
        $arrParam['hidTerminalKey'] = $rsRevalidate['hidTerminalKey'];
        $arrParam['hidDepotKey'] = $rsRevalidate['hidDepotKey'];
        $arrParam['hidPlannerKey'] = $rsRevalidate['hidPlannerKey'];
        
        // khusus dr api, narik rute dari job, karena gk ad di api nya utk saat ini
        if(isset($arrParam['_mnv-api'])){
            if(!isset($arrParam['hidId']) || empty($arrParam['hidId'])){ 
                $arrParam['routeFrom'] = $rsRevalidate['routeFrom'];
                $arrParam['routeTo'] = $rsRevalidate['routeTo'];
            } 
        }
         
        // realisasi biaya
        if($this->useRealization()){  
            // priceCost gk boleh diganti, karena nanti ad update dr realisasi
            unset($this->arrDataDetail['amount']);  
        }else{ 
           // kalo gk pake realisasi, copy semua
           $arrParam['amount'] = $arrParam['requestAmount'];
        }
        
        //$arrParam['productDescription'] = (isset($arrParam['productDescription'])) ? $arrParam['productDescription'] : '';

        // cek tipe bisnis
        
        $truckingType = $this->loadSetting('truckingType');
         
        // update tax utk cost
        $rsCostDetail = array();    
        if(isset($arrParam['pkey']) && !empty($arrParam['pkey'])){ 
            $rsCostDetail = $this->getCostDetail($arrParam['pkey']);
            $rsCostDetail = array_column($rsCostDetail,null, 'pkey'); 
        }


        if ($truckingType == 1){ 
           
			
            //buat model reguler, yg gk ad inputan qty
            for($i=0;$i<count($arrParam['hidDetailKey']);$i++){   
               $hidDetailKey = $arrParam['hidDetailKey'][$i];
               $arrParam['qtyCostDetail'][$i] = 1;
                
               $price = (isset($rsCostDetail[$hidDetailKey]) && $rsCostDetail[$hidDetailKey]['isrealization'] == 1) ?  $rsCostDetail[$hidDetailKey]['amount'] : $this->unFormatNumber($arrParam['requestAmount'][$i]);
               $arrParam['totalCostDetail'][$i] = $price ; // harus tergantung realisasi jg 
                
            }
            
			
            //add car, jika pake modul purchase,
			if($this->activeModule['truckingpurchase']){ 
				
					$supplierKey = $arrParam['hidSupplierKey'];
					$isOutsource = $arrParam['chkIsOutsource'];

					if(!empty($supplierKey) && ($isOutsource  == 1)){

						$rsCarDetail = $this->getCarDetail($arrParam['pkey']);

						$SODetailkey = $arrParam['hidSODetailKey'];
						$rsSODetail  = $truckingServiceOrder->getDetailByColumn('pkey',$SODetailkey); 

						$qty = 1;

						$containerNumber =  $arrParam['containerNumber'];
						$container2Number =  $arrParam['container2Number'];
						$sealNumber = $arrParam['sealNumber'];
						$seal2Number = $arrParam['seal2Number'];

						$container = $containerNumber . (empty($container2Number) ? '' : ', ' . $container2Number);
						$seal = $sealNumber . (empty($seal2Number) ? '' : ', '. $seal2Number);

						$total = 0;
						$taxValue      = 0;
						$outsourceCost = $this->unFormatNumber($arrParam['outsourceCost']);
						$taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);
						//$taxValue = $this->unFormatNumber($arrParam['taxValue']);
						$includeTax = $this->unFormatNumber($arrParam['chkIncludeTax']);

						$subTotal = $outsourceCost;

						//taxValue
						$taxValue = $subTotal * $taxPercentage / 100;
						$total = $subTotal + $taxValue;

						if($includeTax == 1)
						{
							$taxValue = ($taxPercentage / (100 + $taxPercentage)) * $subTotal;
							$total = $subTotal;
						}

						$arrParam['hidOutsourceVehicleDetailKey'][0] = (empty($rsCarDetail) ? 0 : $rsCarDetail[0]['pkey']);
						$arrParam['qtyDetail'][0] = $qty;
						$arrParam['hidServiceDetailKey'][0] = $rsSODetail[0]['itemkey'];
						$arrParam['carRegistration'][0] = $arrParam['outsourceCarRegistrationNumber'];
						$arrParam['containerDetail'][0] = $container;
						$arrParam['sealDetail'][0] = $seal;
						$arrParam['priceDetail'][0] = $outsourceCost;
						$arrParam['chkIncludeTaxDetail'][0] = $includeTax;
						$arrParam['taxPercentageDetail'][0] = $taxPercentage;
						$arrParam['taxValueDetail'][0] = $taxValue;
						$arrParam['subtotalDetail'][0] = $total;
						$arrParam['tax23PercentageDetail'][0] = $arrParam['tax23Percentage'] ?? 0;
						$arrParam['tax23ValueDetail'][0]   = 0;

					}

				
			}
           

 
        }else{
            // model logol
            $arrParam['chkIsOutsource'] = 1;
            
            $subTotalAP = 0;
            
            $totalCostDetail = count($arrParam['hidServiceDetailKey']);
            for($i=0;$i<$totalCostDetail;$i++){
                if($arrParam['qtyCostDetail'][$i] <= 0 )
                    $arrParam['qtyCostDetail'][$i] = 1;
                    
                $price = $this->unFormatNumber($arrParam['priceDetail'][$i]);
                $qty = $this->unFormatNumber($arrParam['qtyDetail'][$i]);
                $taxPercentageDetail = $this->unFormatNumber($arrParam['taxPercentageDetail'][$i]);
                $tax23PercentageDetail = $this->unFormatNumber($arrParam['tax23PercentageDetail'][$i]);
                
                $subTotalDetail = $price * $qty;
                $taxValueDetail = $subTotalDetail * ($taxPercentageDetail/100);
                $tax23ValueDetail = $subTotalDetail * ($tax23PercentageDetail/100);
                
                $arrParam['taxValueDetail'][$i] = $taxValueDetail;
                $arrParam['tax23ValueDetail'][$i] = $tax23ValueDetail; 
                
                //$subTotalDetail += $taxValueDetail;
                
                $arrParam['subtotalDetail'][$i] = $subTotalDetail + $taxValueDetail;
                
                $subTotalAP += $subTotalDetail;
            }
            
            // khusus AP Trucking saja
            $arrParam['outsourceCost'] = $subTotalAP; 
            
            for($i=0;$i<count($arrParam['hidDetailKey']);$i++){
                
                // kalo ad realisasi, pake realisasi
                // ad kemungkinan amount dr realisasi = 0, gpp.
                $hidDetailKey = $arrParam['hidDetailKey'][$i];
                $price = (isset($rsCostDetail[$hidDetailKey]) && $rsCostDetail[$hidDetailKey]['isrealization'] == 1) ?  $rsCostDetail[$hidDetailKey]['amount'] : $this->unFormatNumber($arrParam['requestAmount'][$i]);
                $qty = $this->unFormatNumber($arrParam['qtyCostDetail'][$i]);
                $taxPercentageDetail = $this->unFormatNumber($arrParam['taxPercentageCostDetail'][$i]);
                $tax23PercentageDetail = $this->unFormatNumber($arrParam['tax23PercentageCostDetail'][$i]);
                 
                $subTotalDetail = $price * $qty;
                $taxValueDetail = $subTotalDetail * ($taxPercentageDetail/100);
                $tax23ValueDetail = $subTotalDetail * ($tax23PercentageDetail/100);
                
                $arrParam['taxValueCostDetail'][$i] = $taxValueDetail;
                $arrParam['tax23ValueCostDetail'][$i] = $tax23ValueDetail;
                $subTotalDetail += $taxValueDetail;
                
                $arrParam['totalCostDetail'][$i] = $subTotalDetail;
                 
            }
            
        }
         
        
        if ($arrParam['chkIsOutsource'] == 1){
            $arrParam['hidDriverKey'] = 0;
            $arrParam['hidCoDriverKey'] = 0;
            $arrParam['hidCarKey'] = 0;
            $arrParam['hidChassisKey'] = 0;  
            $arrParam['driverCommission'] = 0;
            $arrParam['codriverCommission'] = 0;
            
            if ($arrParam['outsourceDownpayment'] == 0){
                $arrParam['hidDownpaymentRecipientKey'] = 0; 
            }else{
                if (empty($arrParam['hidDownpaymentRecipientKey']))
                    $arrParam['hidDownpaymentRecipientKey'] = $arrParam['hidPlannerKey'];

            }
           
    	    $reCountResult = $this->reCountOutsourceTax($arrParam); 
            $arrParam['total'] = $reCountResult['total'];
            $arrParam['taxValue'] = $reCountResult['taxValue']; 
            //$arrParam['outsourceAP'] = $reCountResult['outsourceAP'];
        }else{
            $arrParam['hidSupplierKey'] = 0;
            $arrParam['outsourceCost'] = 0;
            $arrParam['outsourceDownpayment'] = 0;
            //$arrParam['outsourceAP'] = 0;  
            $arrParam['total'] = 0;
        }
        
        // hitung ulang nilai AP outsource
        $outsourceCost = $this->unformatNumber($arrParam['outsourceCost']);
        $outsourceTaxValue = $this->unformatNumber($arrParam['taxValue']);
         
        if($arrParam['chkIncludeTax'] != 1)
            $outsourceCost += $outsourceTaxValue; 
        
        $outsourceDownpayment = $this->unformatNumber($arrParam['outsourceDownpayment']);

        // gk perlu agar bisa divalidasi ketika uang muka sudah keluar
        //if ($outsourceDownpayment > $outsourceCost)  $outsourceDownpayment = $outsourceCost;
        
        // kalo include tax gk perlu tambah taxvalue lg
        $arrParam['outsourceAP'] =  $outsourceCost - $outsourceDownpayment;

            
        // kalo gk punya akses Cost, detach dr detail agar tdk keupdate
        // agar kalo gk ad akses, tetep bisa proses SPK dan biayany adr JO
        
		if(!isset($arrParam['_mnv'])) {  // kalo BUKAN dari JO alias add SPK manual
          $hasCostAccess = $security->isAdminLogin($this->costSecurityObject,10);  
 
		  if (!$hasCostAccess){
              
              // kalo edit
              if(!empty($arrParam['hidId'])){ 
                  //unset costing
                   $arrDataDetails = $this->arrData['pkey'][1]['dataDetail'];
                   for($i=0;$i<count($arrDataDetails);$i++){
                       if ($arrDataDetails[$i]['tableName'] == $this->tableCost){  
                           unset($arrDataDetails[$i]); 
                           break;
                       }
                   }  

                  $this->arrData['pkey'][1]['dataDetail'] = array_values($arrDataDetails);   

                  // unset komisi
                  unset($this->arrData['drivercommission']);
                  unset($this->arrData['codrivercommission']); 
              }else{
                  
                  $costRate = new CostRate();
                  $truckingCost = createObjAndAddToCol(new Service(TRUCKING_SERVICE,1));   
                   
                  // kalo add, ambil ulang dr costing
                  $rsDriverCommission = $costRate->getDriverCommissionRate($rsJO[0]['warehousekey'],$rsJO[0]['stuffinglocationkey'], $rsJO[0]['cargotypekey'], $arrParam['selJobType'],  $arrParam['hidItemKey'] , $rsJO[0]['consigneekey']);  
                  $rsDriverCommission = array_column($rsDriverCommission,'price', 'costkey');
                  
                  $arrParam['driverCommission'] = $rsDriverCommission[-1];
                  $arrParam['codriverCommission'] = $rsDriverCommission[-2]; 
                   
                  // get all cost, baik doc atau per item    
                  $rsCost = $truckingCost->searchDataRow(array($truckingCost->tableName.'.pkey'),
                                                           ' and '.$truckingCost->tableName.'.statuskey = 1 
                                                             and showintrucking = 1 and chargetype = 2',
                                                           'order by fixedcost desc, name asc'
                                                          );
                    
                  $rsCostRateCol = $costRate->getCostDetail($rsJO[0]['warehousekey'],$rsJO[0]['stuffinglocationkey'], $rsJO[0]['cargotypekey'], $arrParam['selJobType'], $arrParam['hidItemKey'] , array_column($rsCost,'pkey'),$rsJO[0]['consigneekey']);
                  $rsCostRateCol = array_column($rsCostRateCol,null,'costkey');
                      
                  for($i=0;$i<count($arrParam['requestAmount']);$i++)
                        $arrParam['requestAmount'][$i] = (isset($rsCostRateCol[ $arrParam['hidCostKey'][$i] ])) ? $rsCostRateCol[ $arrParam['hidCostKey'][$i] ]['price'] : 0; 
                  
              }
              
              
		 }  
		}
         
        
        $rsDetail = $this->getCostDetail($arrParam['pkey'],'',' and '. $this->tableCost .'.refcashoutkey <> 0 and '. $this->tableCost .'.supplierkey = 0');
        $this->retrieveReadonlyDataRow($arrParam, $rsDetail, $this->arrDataDetail,'refcashoutkey' ); // utk jaga data yg sudah keluar uangnya tdk berubah
      
        $rsDetail = $this->getCostDetail($arrParam['pkey'],'',' and '. $this->tableCost .'.refrequestkey <> 0 and '. $this->tableCost .'.supplierkey = 0');
        $this->retrieveReadonlyDataRow($arrParam, $rsDetail, $this->arrDataDetail,'refrequestkey' ); // utk jaga data yg sudah keluar uangnya tdk berubah
      
        $rsDetail = $this->getCostDetail($arrParam['pkey'],'',' and '. $this->tableCost .'.refadditionalcostkey <> 0');
        $this->retrieveReadonlyDataRow($arrParam, $rsDetail, $this->arrDataDetail,'refadditionalcostkey' ); // utk jaga data yg berasal dari additional cost
      
        $costList = $arrParam['hidCostKey'];
        $employeeList = $arrParam['hidEmployeeDetailKey'];
        $suplierList = $arrParam['hidSupplierDetailKey'];
 
        if ($arrParam['chkIsOutsource'] == 0){
               
            $defaultRecipient = (!empty($arrParam['hidDriverKey'])) ? $arrParam['hidDriverKey'] : $arrParam['hidPlannerKey'];
             
            if (!empty($defaultRecipient)){ 
                // update detail cost, penerima harus diisi
                for($i=0;$i<count($costList);$i++){
                    //$this->setLog($costList[$i]);
                    if ( empty($employeeList[$i]) &&  empty($suplierList[$i]) ) 
                        $arrParam['hidEmployeeDetailKey'][$i] = $defaultRecipient;

                }
            }
        }else{
              
            if (!empty($arrParam['hidSupplierKey'])){ 
                // update detail cost, penerima harus diisi 
                for($i=0;$i<count($costList);$i++){ 
                    if ( empty($employeeList[$i]) &&  empty($suplierList[$i]) ) 
                        $arrParam['hidSupplierDetailKey'][$i] = $arrParam['hidSupplierKey'];

                }
            }
            
        }
        
        
        
       $arrParam['outsourceCostOutstanding'] = $arrParam['outsourceCost']; 
        
        
        //update khusus checklist tnda terima dokumen, kalo gk diupdate, gk bisa save kalo gk ad akses overwrite cost
         
        $arrParam = parent::normalizeParameter($arrParam,true);
        
        if(!empty($arrParam['chkReceivedDoc'])){
            for($i=0;$i<count($arrParam['chkReceivedDoc']);$i++)
                $arrParam['chkReceivedDoc'][$i] = $tempReceivedDoc[$i];  
        }
        
        return $arrParam;
    }
    
      function generateDefaultQueryForAutoComplete($returnField){ 
        
        $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value,
                    '.$this->tableName.'.pkey,
                    '.$this->tableName.'.code,
                    '.$this->tableName.'.stuffingdatetime,
                    '.$this->tableName.'.stuffingaddress,
                    '.$this->tableName.'.routefrom,
                    '.$this->tableName.'.routeto,
                    '.$this->tableName.'.statuskey,
                    '.$this->tableTruckingJob.'.name as jobtypename

				from 
					'.$this->tableName . ', 
					'.$this->tableTruckingJob . ', 
                    '.$this->tableStatus.' 
				where  		 
                    '.$this->tableName.'.jobtypekey  = '.$this->tableTruckingJob.'.pkey and                
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
			';
           
         return $sql;
     }
    function reValidateData($arrParam,$rs){ 
         
            //$truckingServiceOrder = new TruckingServiceOrder();

            //$rs = $truckingServiceOrder->getDataRowById($arrParam['hidSOKey']); 
        
            $reCountResult = array(); 
            $reCountResult['hidDepotKey'] = $rs[0]['depotkey']; 
            $reCountResult['hidTerminalKey'] = $rs[0]['terminalkey'];  
            $reCountResult['hidCategoryKey'] = $rs[0]['categorykey'];  
            $reCountResult['hidCargoTypeKey'] = $rs[0]['cargotypekey']; 
            $reCountResult['hidLocationKey'] = $rs[0]['stuffinglocationkey'];
            $reCountResult['stuffingAddress'] = $rs[0]['stuffingaddress'];
            $reCountResult['hidPlannerKey'] = $rs[0]['plannerkey'];
            $reCountResult['routeFrom'] = $rs[0]['routefrom'];
            $reCountResult['routeTo'] = $rs[0]['routeto'];
    
            // cek ulang fixed cost
        
            return $reCountResult;
				
	}
      
    
    
    // ====================== CHANGE STATUS
    
    
    function afterStatusChanged($rsHeader){
        $truckingServiceOrder = new TruckingServiceOrder();
		
		// ambil ulang status terakhir
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        
        // update status detail SO
        
        $this->updateTruckingCostCashOut($rsHeader[0]['pkey']);  
	    $truckingServiceOrder->updateContainer($rsHeader[0]['refkey']);     
        $truckingServiceOrder->updateDetailStatus($rsHeader[0]['refdetailkey']); 
        $truckingServiceOrder->updateSalesWorkOrderCost($rsHeader[0]['refkey']);
        $truckingServiceOrder->updateWOActivityDate($rsHeader[0]['refkey']);

        
        //untuk logol
	    $truckingServiceOrder->updateDetailContainer($rsHeader[0]['pkey']);    
		 
//        if ($this->loadSetting('multidropWorkOrder') == 1 && in_array($rsHeader[0]['statuskey'], array(2, 3))) {
//            $truckingServiceOrder->updateSellingCost($rsHeader[0]['pkey']);
//        }
//        if ($this->loadSetting('multidropWorkOrder') == 1 && in_array($rsHeader[0]['statuskey'], array(2,3,4))) {
//            //update total cost JO
//            $truckingServiceOrder->recountTotalCostJobOrder($rsHeader[0]['refkey']);
//        }
 
       
		if ($this->loadSetting('multidropWorkOrder') == 1 && in_array($rsHeader[0]['statuskey'], array(2,3,4))) {
			 $truckingServiceOrder->updateSellingCost($rsHeader[0]['pkey']); 
        }
		
    }

    
	function validateConfirm($rsHeader){
		     
        $employee = new Employee();
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $truckingServiceOrder = new TruckingServiceOrder();
         
                    
        //cek Job Order statusnya sudah closed / invoiced blm
        $rsSO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refkey']);
        if($rsSO[0]['statuskey'] >= 4){ 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsSO[0]['code'].'</strong>. ' . $this->errorMsg['truckingServiceOrder'][5]);
        }
 
        if (USE_GL){  
            $arrCOA = array();
            array_push($arrCOA, 'cashbankops' , 'payment'); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }    
        }
            
            /*
            DIPINDAHKAN KE VALIDATE CLOSING..
            
            if(empty($rs[0]['driverkey'])){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['driver'][1]);
            }else if (USE_GL){
                    $rsCOA =  $coaLink->getCOALink ('cashbank', $employee->tableName, $rs[0]['driverkey']);
                    if (empty($rsCOA))	
                        $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);

                    $rsCOA =  $coaLink->getCOALink ('cashbankops', $warehouse->tableName, $rs[0]['warehousekey']);
                    if (empty($rsCOA))	
                        $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
            }

            if(empty($rs[0]['carkey'])){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][1]);
            }

            if(empty($rs[0]['chassiskey'])){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['chassis'][1]);
            }  
            */
   

        $validateCar = $this->loadSetting('validateCar');
        if ($validateCar == 1) {
            if($rsHeader[0]['carkey'] == 0){
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['car'][1]);
            }  
        }        
        /*$employee = new Employee();
        $supplier = new Supplier();*/
        
        $rsCost = $this->getCostDetail($rsHeader[0]['pkey']);
        for($i=0;$i<count($rsCost);$i++){
            if((!empty($rsCost[$i]['supplierkey']) && !empty($rsCost[$i]['employeekey'])))
                $this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceWorkOrder'][6]); 
            /*
            if(!empty($supplierDetailkey[$i])){
                $rsSupplier = $supplier->getDataRowById($supplierDetailkey[$i]);
                if(empty($rsSupplier))
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][2]);     
            }
            if(!empty($employeeDetailkey[$i])){
                $rsEmployee = $employee->getDataRowById($employeeDetailkey[$i]);
                if(empty($rsEmployee))
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][4]);     
            }*/
        }
        
	 }
    
    function updateAssignDriver($arr){
                		
    
        $arrayToJs =  array();
        
        
         try{ 

            if (!$this->oDbCon->startTrans())
                throw new Exception($OBJ->errorMsg[100]);
             
                $pkey = $arr['pkey'];
                $spkCode = $arr['spkcode'];
                $driverkey = $arr['driverkey'];
                $driverCode = $arr['drivercode'];
             
        
        
                $rs = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.code',$this->tableName.'.statuskey',$this->tableName.'.warehousekey',$this->tableName.'.driverkey'), ' and '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey).' and '.$this->tableName.'.statuskey in (1,2)');
            
             
                // cek spk ditemukan atau tidak
                 if(empty($rs))
                    $this->addErrorList($arrayToJs,false,'<b>'.$spkCode.'</b>. '.$this->errorMsg[213]); 
             
                
                if(empty($driverkey)){  
                    $this->addErrorList($arrayToJs,false,'<b>'.$driverCode.'</b>. '.$this->errorMsg[213]);  
                }else{
                    $employee = new Employee();
                    $car = new Car();
                                  
                    //cek karyawannya ada atau tidak
                    $rsEmployee = $employee->searchDataRow(array($employee->tableName.'.pkey'),' and '.$employee->tableName.'.isdriver = 1  and '.$employee->tableName.'.statuskey = 2 and '.$employee->tableName.'.pkey = '.$this->oDbCon->paramString($driverkey));
                    if(empty($rsEmployee))
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][1]); 
                    
                     //cari mobil berdasarkan supir
                    $rsCar = $car->searchDataRow(array($car->tableName.'.pkey',$car->tableName.'.policenumber'),'  and '.$car->tableName.'.statuskey = 1 and '.$car->tableName.'.driverkey = '.$this->oDbCon->paramString($driverkey));
                    $carKey= $rsCar[0]['pkey'];
                    
                  /*  if(empty($rsCar))
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][1]); */                    
                    
                 } 
                     
             
                 //biaya yang belum di bayar
                 $sql = 'select ' . $this->tableCost.'.pkey from  ' . $this->tableCost.' where  ' . $this->tableCost.'.refcashoutkey = 0  and  ' . $this->tableCost.'.refkey = '.$this->oDbCon->paramString($rs[0]['pkey']).'  ';
                 $rsCost =   $this->oDbCon->doQuery($sql) ;    
                    
                 $arrCostKey = array_column($rsCost,'pkey');
             
                 if(!empty($arrCostKey)){
                        //update penerima pada detail cost yang belum dibayarkan 
                        $sql = 'update ' . $this->tableCost.' set  employeekey = '.$this->oDbCon->paramString($driverkey).' where ' . $this->tableCost.'. pkey in ( '.$this->oDbCon->paramString($arrCostKey,',').')';
                        $this->oDbCon->execute($sql); 
                 }

                 if(!empty($arrayToJs))  return $arrayToJs;
             
             
                 $sql = 'update ' . $this->tableName.' set  driverkey = '.$this->oDbCon->paramString($driverkey).', carkey = '.$this->oDbCon->paramString($carKey).' where  pkey = '.$this->oDbCon->paramString($pkey);
                 $this->oDbCon->execute($sql);   
    
             
                //validasi kalau sudah konfirmasi biayanya
                if($rs[0]['statuskey'] == TRANSACTION_STATUS['menunggu']){
                   $this->changeStatus($pkey,2,'',false,true); 
                }else{
                  $this->updateTruckingCostCashOut($pkey); 
                }
                
                
             
            $this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);  

		}catch(Exception $e){
			$this->oDbCon->rollback();   
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 

		}	
        
//        $this->setLog($arrayToJs,true);

        return $arrayToJs;
        
        
        
    }

   	 
	function validateClose($rsHeader){
		// $rsCOA =  $coaLink->getCOALink ('cashbank', $obj->tableName, $id);
    
        $employee = new Employee();
        $supplier = new Supplier();
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $truckingServiceOrder = new TruckingServiceOrder();
        $truckingCostCashOut = new TruckingCostCashOut();
         
	 	$arrayToJs = array(); 
        $rsCost =  $this->getCostDetail($rsHeader[0]['pkey'],'',' and '. $this->tableCost .'.supplierkey <> 0');  
            
        if($rsHeader[0]['statuskey'] <> 2) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[204],true);   
        
        //cek Job Order statusnya sudah closed / invoiced blm
        $rsSO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refkey']);
        if($rsSO[0]['statuskey'] >= 4) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg[201].'<br><strong>'.$rsSO[0]['code'].'</strong>.');
        

        if ( $rsHeader[0]['isoutsource'] == 0){ 
            
            if(empty($rsHeader[0]['driverkey'])){
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg[201].' '.$this->errorMsg['driver'][1]);
            } 

            if(empty($rsHeader[0]['carkey'])){
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg[201].' '.$this->errorMsg['car'][1]);
            }

            /*
            if(empty($rsHeader[0]['chassiskey'])){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['chassis'][1]);
            }  
           */ 
            
/*    	    $rsCashOutKey = $truckingCostCashOut->getTransactionType($this->tableName); // GK BOLEH PAKE TRANSACTINO TYPE LG. harusnya pake getTableKeyAndObj
            $rsCashout = $truckingCostCashOut-> searchData('','',true,' and '.$truckingCostCashOut->tableName.'.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) .' and reftabletype = '.$this->oDbCon->paramString($rsCashOutKey['key']).' and '.$truckingCostCashOut->tableName.'.statuskey = 1');
            if(!empty($rsCashout)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong> '.$this->errorMsg['truckingServiceWorkOrder'][5]);*/
      
        }else{
            
            if(empty($rsHeader[0]['supplierkey'])) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['supplier'][1]);
             
            //if($rsHeader[0]['outsourcecost'] <= 0) 
            //    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['truckingServiceWorkOrder'][10]); 
                
        }
            
         if (USE_GL){  
            $arrCOA = array();
            array_push($arrCOA, 'cashbankops' , 'outsourcecost',  'operationalcost'); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }    
        }


            
        
        
        $totalCost = count($rsCost); 
        for($i=0;$i<$totalCost;$i++){
            $rsSupplier = $supplier->getDataRowById($rsCost[$i]['supplierkey']);
            if(empty($rsSupplier))
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[213]).' ('.$this->lang['supplier'].')';  
        }

        
        // kalo perlu validasi document
        $needDoc = $this->loadSetting('spkDocumentValidation');
        if($needDoc == 1 ){
            // beda sama cost diatas, ini ambil semua cost, baik ke karyawan maupun pe supplier
            $rsSPKCost =  $this->getCostDetail($rsHeader[0]['pkey']);  
            $errCost = array();
            
            $totalCost = count($rsSPKCost); 
            
            for($i=0;$i<$totalCost;$i++){
                if($rsSPKCost[$i]['isneeddocument'] == 1 && $rsSPKCost[$i]['isreceiveddoc'] <> 1)
                   array_push($errCost,$rsSPKCost[$i]['name'] .', '.strtolower($this->errorMsg['truckingServiceWorkOrder'][11])); 
            }     
 

            if(!empty($errCost))
             $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong><br>'.implode('<br>',$errCost));  

        }
      
	 	return $arrayToJs;
        
	 } 
    
    function validateBackConfirm($rsHeader){ 
        $ap = new AP();
        $apEmployeeCommission = new APEmployeeCommission();
        $truckingServiceOrder = new TruckingServiceOrder();
        
        $pkey = $rsHeader[0]['pkey'];
        
        // vendor AP cost
        $arrAPKey = array();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName);   
        array_push($arrAPKey,$rsAPKey['key']);
        
        $rsAPKey = $ap->getTableKeyAndObj($this->tableCost);   
        array_push($arrAPKey,$rsAPKey['key']); 
        
        $rsAPKey = $ap->getTableKeyAndObj($this->tableCost);   
  	    $rsAP = $ap->searchData('','',true,' and  '.$ap->tableName.'.refheaderkey = '.$this->oDbCon->paramString($pkey).' and '.$ap->tableName.'.reftabletype in ('.implode(',',$arrAPKey).')  and ('.$ap->tableName.'.statuskey in(2,3))');
        if(!empty($rsAP))  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);
     
     	$rsAPCommissionKey = $apEmployeeCommission->getTableKeyAndObj($this->tableName);   
        $tableCommissionkey = $rsAPCommissionKey['key'];
        
        $rsAPCommission = $apEmployeeCommission->searchData('','',true,' and  '.$apEmployeeCommission->tableName.'.refheaderkey = '.$this->oDbCon->paramString($pkey).' and '.$apEmployeeCommission->tableName.'.reftabletype = '.$tableCommissionkey.'  and ('.$apEmployeeCommission->tableName.'.statuskey in(2,3))');
        if(!empty($rsAPCommission))  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['apCommission'][2]);
        
        // kalo ad purchae order, cek dulu purchasenya sudah terbayarkan atau blm ?
        // boleh backconfirm, tp selama ad tef purchase order key, gk boleh revisi
//        if($this->activeModule['truckingpurchase']){ 
//            $rsTruckingPurchase = $this->getTruckingPurchaseInformation($pkey); 
//            if (!empty($rsTruckingPurchase)) 
//                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg[900].' ('.$rsTruckingPurchase[0]['code'].')');
//        }

        // cek status JO dulu
        $rsSO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refkey']);
        if ($rsSO[0]['statuskey'] >= 4)	
           $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsSO[0]['code'].'</strong>. ' . $this->errorMsg['truckingServiceOrder'][5]);
    
    } 
     
    function getTruckingPurchaseInformation($wokey, $statuskey = array(2,3)){
        if(!is_array($statuskey)) $statuskey = array($statuskey);
        
        $truckingPurchase = new TruckingPurchase();
      
        $sql = 'select
            '.$truckingPurchase->tableNameDetail.'.wokey,     
            '.$truckingPurchase->tableName.'.pkey,
            '.$truckingPurchase->tableName.'.code,    
            '.$truckingPurchase->tableName.'.trdate,
            '.$truckingPurchase->tableName.'.supplierkey,
            '.$truckingPurchase->tableName.'.grandtotal,
            '.$truckingPurchase->tableName.'.statuskey,
            '.$truckingPurchase->tableStatus.'.status as statusname,
            '.$truckingPurchase->tableNameDetail.'.total as subtotal
          from 
            '.$truckingPurchase->tableName.',
            '.$truckingPurchase->tableStatus.',
            '.$truckingPurchase->tableNameDetail.'
          where  
            '. $truckingPurchase->tableNameDetail.'.wokey in ('.$this->oDbCon->paramString($wokey,',') .') and   
            '. $truckingPurchase->tableName.'.pkey = '. $truckingPurchase->tableNameDetail.'.refkey and
            '. $truckingPurchase->tableName.'.statuskey = '. $truckingPurchase->tableStatus.'.pkey and
            '. $truckingPurchase->tableName.'.statuskey in ('.$this->oDbCon->paramString($statuskey,',').')';
 
        return $this->oDbCon->doQuery($sql);

    }

    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        
        // SPK boleh cancel, tidak tergantung sudah keluar duit atau blm
        // kecuali ad piutang vendor yg sudah dibayarkan sebagian 
        
        $ap = new AP();
        $truckingServiceOrder = new TruckingServiceOrder();
        $truckingCostCashOut = new TruckingCostCashOut();
        $cashBankRealization = new CashBankRealization(); 
        $apEmployeeCommission = new APEmployeeCommission();
         
		
        $pkey = $rsHeader[0]['pkey'];
        
        parent::validateCancel($pkey,$autoChangeStatus); 
          
        // khusus cash out, harus cek pake reftable jg,
        // cek cash out sudah ad yg konfirmasi / closed blm
        
        // PINDAHKAN KE REALISASI SAJA NANTI
        $rsCashOutKey = $this->getTableKeyAndObj($this->tableName);   
        $rsCashOut = $truckingCostCashOut->searchData('','',true,' and '.$truckingCostCashOut->tableName.'.refkey = ' . $this->oDbCon->paramString($pkey) .' and '.$truckingCostCashOut->tableName.'.reftabletype = ' . $this->oDbCon->paramString($rsCashOutKey['key']) .' and '.$truckingCostCashOut->tableName.'.statuskey in (2,3,4)');
        if (!empty($rsCashOut)) { 
             $errMsg = array();
             foreach($rsCashOut as $cashOutRow)
                 array_push($errMsg,'<b>'.$cashOutRow['code'].'</b>, ' .$this->errorMsg[225]);
                     
            $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br>'.implode('<br>',$errMsg)); 
  
        }
      
        //cek Job Order statusnya sudah closed blm
        // kalo sudha selesai SPK, SPK gk boelh cancel. harus info dulu ke admin JO
        $rsSO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refkey']);
        if($rsSO[0]['statuskey'] >= 3) 
           $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsSO[0]['code'].'</strong>, ' .$this->errorMsg[226] );
       
        

         // vendor AP cost
        $arrAPKey = array();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName);   
        array_push($arrAPKey,$rsAPKey['key']);
        
        $rsAPKey = $ap->getTableKeyAndObj($this->tableCost);   
        array_push($arrAPKey,$rsAPKey['key']); 
          
  	    $rsAP = $ap->searchData('','',true,' and  '.$ap->tableName.'.refheaderkey = '.$this->oDbCon->paramString($pkey).' and '.$ap->tableName.'.reftabletype in ('.implode(',',$arrAPKey).')  and ('.$ap->tableName.'.statuskey in(2,3))');
        if(!empty($rsAP))  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ap'][2]);
   	  
   	    $rsAPCommissionKey = $apEmployeeCommission->getTableKeyAndObj($this->tableName);   
        $tableCommissionkey = $rsAPCommissionKey['key'];
        
        $rsAPCommission = $apEmployeeCommission->searchData('','',true,' and  '.$apEmployeeCommission->tableName.'.refheaderkey = '.$this->oDbCon->paramString($pkey).' and '.$apEmployeeCommission->tableName.'.reftabletype = '.$tableCommissionkey.'  and ('.$apEmployeeCommission->tableName.'.statuskey in(2,3))');
        if(!empty($rsAPCommission))  
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['apCommission'][2]);
        
        
         if($this->activeModule['truckingpurchase']){ 
            $rsTruckingPurchase = $this->getTruckingPurchaseInformation($pkey); 
            if (!empty($rsTruckingPurchase)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg[201].'<br><strong>'.$rsTruckingPurchase[0]['code'].'</strong>, '. $this->errorMsg['truckingServiceWorkOrder'][14]);
        }
   	 } 

    
    function updateRealCostAmount($wokey){
            // untuk update nilai real amount agar bisa ketarik di trucking purchase
            // harusnya realisasi dan amount diupdate nilai nya jika tujuannya ke supplier (ada purchase order), karena kalau ke karyawan harusnya yg set isrealisasi = 1 dan 
            
            $sql = 'update '.$this->tableCost.' set amount = requestamount, isrealization = 1 where refkey = ' . $this->oDbCon->paramString($wokey);
            $this->oDbCon->execute($sql); 
            // harusnya seperti ini
            // $sql = 'update '.$this->tableCost.' set isrealization = 1 where refkey = ' . $this->oDbCon->paramString($wokey);
            // $this->oDbCon->execute($sql);
    }
    
    function cancelRealCostAmount($wokey){
     // untuk cancel nilai real amount agar bisa keupdate dari trucking purchase 
		// harus khusus yg ke vendor aj
		
		// sepertinya gk perlu, karena kalo di set 0 lg gk konsisten. ketika pertama kali input muncul di printan rekap, tp begitu diblikik dari selesi ke proses SPK, jd kehapus
		// toh di purchase jg sudha finakl, tdk bisa merubah harga lg
		
        // amount gk boleh di set = 0, karena yg boleh set amount ketika realisasi di batalkan dan user menuggunakan modul realisasi
        // jika user tidak menggunakan modul realisasi, amount sudah di set = requestamount di normalize
        //$sql = 'update '.$this->tableCost.' set amount = 0, isrealization = 0 where supplierkey <> 0 and refkey = ' . $this->oDbCon->paramString($wokey);
		//$this->oDbCon->execute($sql); 

        $sql = 'update '.$this->tableCost.' set isrealization = 0 where supplierkey = 0 and refkey = ' . $this->oDbCon->paramString($wokey);
		$this->oDbCon->execute($sql); 
    }
    
    function backConfirmTrans($rsHeader){ 
         
        // jika tidak menggunakan purchase, langsung cancel AP
        if(!$this->activeModule['truckingpurchase']){ 
            $apGroupingType = $this->loadSetting('ungroupWorkOrderAP');
            if($apGroupingType == 2) 
                $this->cancelGroupVendorAP($rsHeader);
            else
                 $this->cancelVendorAP($rsHeader);
        }else{ 
            $this->cancelRealCostAmount($rsHeader[0]['pkey']);
        }
        
        
        $this->cancelCarTurnover($rsHeader); 
        $this->cancelCommissionAP($rsHeader);
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    }
    
  
	function confirmTrans($rsHeader){    

        if( in_array(DOMAIN_NAME, array('praja.wintera.co.id')) ) { 
                 $this->createFleetGPS($rsHeader[0]['pkey']);
        }
 
	} 
    
    function createFleetGPS($arrPkey){    
        
        $gps = new GPS();
        $GPSConnection = new GPSConnection();
        $location = new Location();
        $employee = new Employee();
        $car = new Car();
        
        if(!is_array($arrPkey))  $arrPkey = array($arrPkey);
         
        $rsWorkOrderCol = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.driverkey',$this->tableName.'.carkey',$this->tableName.'.requestid'),
                                             ' and ' . $this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').')'
                                             );
        $rsWorkOrderCol = array_column($rsWorkOrderCol,null,'pkey');
            
        $rsCargoCol =  $this->getCargoDetail($arrPkey);
        $arrLocationKey = array_column($rsCargoCol,'destinationkey');
        $rsCargoCol =  $this->reindexDetailCollections($rsCargoCol,'refkey');
            
        
        $rsCarCol = $car->searchDataRow(array($car->tableName.'.pkey',$car->tableName.'.gpskey',$car->tableName.'.gpstrackerid' ),
                                             ' and ' . $car->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsWorkOrderCol,'carkey'),',').')'
                                             );
        $rsCarCol = array_column($rsCarCol,null,'pkey');
        
        $rsEmployeeCol = $employee->searchDataRow(array($employee->tableName.'.pkey',$employee->tableName.'.name',$employee->tableName.'.requestid' ),
                                             ' and ' . $employee->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsWorkOrderCol,'driverkey'),',').')'
                                             );
        $rsEmployeeCol = array_column($rsEmployeeCol,null,'pkey');
        
        
        $rsLocationCol = $location->searchDataRow(array($location->tableName.'.pkey',$location->tableName.'.name',$location->tableName.'.latitude',$location->tableName.'.longitude' ),
                                             ' and ' . $location->tableName.'.pkey in ('.$this->oDbCon->paramString($arrLocationKey,',').')'
                                             );
        $rsLocationCol = array_column($rsLocationCol,null,'pkey');
        
        $rsGPSCol = $gps->searchDataRow(array($gps->tableName.'.pkey',$gps->tableName.'.code',$gps->tableName.'.name'),
                                ' and '.$gps->tableName.'.pkey in ('.$this->oDbCon->paramString( array_column($rsCarCol,'gpskey') , ',').')');
 
        $rsGPSCol =  array_column($rsGPSCol,null,'pkey');
        
        foreach($arrPkey as $pkey){ 
//            $rsHeader = $this->getDataRowById($pkey);   
//            $rsCargo = $this->getCargoDetail($pkey);
            
            $rsCargo = $rsCargoCol[$pkey];
                
            $arrLocation = array();
//            $rsDriver = $employee->getDataRowById($rsHeader['driverkey']);
//            $rsCar = $car->getDataRowById($rsHeader['carkey']);
            
            for ($i=0;$i<count($rsCargo);$i++){ 
//                $rsLocation = $location->getDataRowById($rsCargo[$i]['destinationkey']);
                $rsLocation = $rsLocationCol[$rsCargo[$i]['destinationkey']];
                $arrTemp = array();
                $arrTemp['destinationname'] = $rsLocation['name'];
                $arrTemp['latitude'] = $rsLocation['latitude'];
                $arrTemp['longitude'] = $rsLocation['longitude'];
                $arrTemp['notes'] = $rsCargo[$i]['workorder'].', '.$rsCargo[$i]['destinationname'];
                array_push( $arrLocation,$arrTemp);
            }

            if (!empty($arrLocation)) {

//                $rsGPS = $gps->searchDataRow(array($gps->tableName.'.pkey',$gps->tableName.'.name'),
//                                ' and '.$gps->tableName.'.pkey in ('.$this->oDbCon->paramString($rsCar[0]['gpstrackerid']).')');

                $rsHeader = $rsWorkOrderCol[$pkey]; 
                
                if(!empty($rsHeader['requestid']))  continue;
                
                $rsDriver = $rsEmployeeCol[$rsHeader['driverkey']];
                $rsCar= $rsCarCol[$rsHeader['carkey']]; 
                $rsGPS = $rsGPSCol[$rsCar['gpskey']]; 
                if(!empty($rsGPS['code'])){
                    $gpsObj = $GPSConnection->getGPSObj($rsGPS['code']);
                    $gpsObj->createFleet($pkey, $arrLocation,$rsDriver['requestid'], $rsCar['gpstrackerid']);
                 }
                  
            }
        } 
 
	}
    

 
    function closeTrans($rsHeader){  
        //if($rsHeader[0]['isoutsource'] == 1)  
         
        // jika tidak menggunakan purchase, langsung add AP
        if(!$this->activeModule['truckingpurchase']){ 
            $apGroupingType = $this->loadSetting('ungroupWorkOrderAP');
            if($apGroupingType == 2)
                $this->addGroupVendorAP($rsHeader); 
            else
                $this->addVendorAP($rsHeader); 
        }else{
            $this->updateRealCostAmount($rsHeader[0]['pkey']);
        }
        
        $this->addCommissionAP($rsHeader); 
        $this->addCarTurnover($rsHeader); 
    } 
 
	function cancelTrans($rsHeader,$copy){  
        $warehouse = new Warehouse();
        $employee = new Employee();
        $truckingServiceOrder = new TruckingServiceOrder();
        $isMultidropWorkOrder = $this->loadSetting('multidropWorkOrder');
         
        $this->cancelCashOut($rsHeader[0]['pkey']); 
        
        if($isMultidropWorkOrder == 1) {
            $this->cancelCashOutCargo($rsHeader[0]['pkey']); 
        }

        //DETAILCONTAINER LOGOL
        $truckingServiceOrder->updateDetailContainer($rsHeader[0]['pkey']);
 	     
        // jika tidak menggunakan purchase, langsung cancel AP
        if(!$this->activeModule['truckingpurchase']){ 
            $apGroupingType = $this->loadSetting('ungroupWorkOrderAP');
            if($apGroupingType == 2)
                $this->cancelGroupVendorAP($rsHeader);
            else
                $this->cancelVendorAP($rsHeader);
        }else{ 
            $this->cancelRealCostAmount($rsHeader[0]['pkey']);
        }
        
        
        $this->cancelCarTurnover($rsHeader);
        $this->cancelCommissionAP($rsHeader);
    
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
        
		if ($copy)
			$this->copyDataOnCancel($rsHeader[0]['pkey']);	  
	     
	} 
    
    function cancelCashOut($workOrderKey,$employeekey=''){
          
        // delete cash out
        $truckingCostCashOut = new TruckingCostCashOut();
        $rsCashOutKey =  $this->getTableKeyAndObj($this->tableName); 
        $employeeCriteria = ''; 
        
        if($employeekey === 0 || $employeekey !== '')
            $employeeCriteria = ' and '.$truckingCostCashOut->tableName.'.employeekey = '.$this->oDbCon->paramString($employeekey).' ';
             
		// $rsCashOut = $truckingCostCashOut->searchData('','',true,' and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($workOrderKey).'  and reftabletype =  '.$this->oDbCon->paramString($rsCashOutKey['key']).' and '.$truckingCostCashOut->tableName.'.statuskey = 1 '.$employeeCriteria);
		$rsCashOut = $truckingCostCashOut->searchData('','',true,' and '.$truckingCostCashOut->tableName.'.refkey = '.$this->oDbCon->paramString($workOrderKey).'  and reftabletype =  '.$this->oDbCon->paramString($rsCashOutKey['key']).' and '.$truckingCostCashOut->tableName.'.statuskey = 1 and ' . $truckingCostCashOut->tableName . '.iscostcargo = 0 '.$employeeCriteria);
        //$this->setLog('citeria => '. $employeeCriteria,true); 
        
		for($i=0;$i<count($rsCashOut);$i++)  
		 $truckingCostCashOut->changeStatus($rsCashOut[$i]['pkey'],5,'',false,true); 
    
    }
    
    function cancelCarTurnover($rsHeader){ 
        $carTurnover = new CarTurnover();
        $rsObjKey = $this->getTableKeyAndObj($this->tableName);
        $carTurnover->cancelMovement($rsHeader[0]['pkey'],$rsObjKey['key']);
    }
    
    function cancelGroupVendorAP($rsHeader){
        $ap = new AP();   
        
        $rsWorkOrderKey = $ap->getTableKeyAndObj($this->tableName, array('key'));     
        $rsAP = $ap->searchDataRow( array($ap->tableName.'.pkey',$ap->tableName.'.refkey'),
                                    ' and '.$ap->tableName.'.refheaderkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' 
                                      and reftabletype in ('.$this->oDbCon->paramString($rsWorkOrderKey['key']) .') 
                                      and '.$ap->tableName.'.statuskey = 1'
                                  );
         
        foreach($rsAP as $apRow){ 
            //cancel AP
            $ap->changeStatus($apRow['pkey'],4,'',false, true);  
            
            $sql = 'update '.$this->tableCost.' set  amount = 0, refcashoutkey = 0, isrealization = 0 where refcashoutkey = ' . $this->oDbCon->paramString($apRow['pkey']); 
            $this->oDbCon->execute($sql);
            
            $sql = 'update '.$this->tableName.' set refcashoutkey = 0 where refcashoutkey = ' . $this->oDbCon->paramString($apRow['pkey']); 
            $this->oDbCon->execute($sql); 
        }
         
         // sekalian cancel ketika backconfirmed
//        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    }

    function cancelVendorAP($rsHeader){
        $ap = new AP();  
        $arrAPKey = array();
        
        $rsWorkOrderKey = $ap->getTableKeyAndObj($this->tableName);   
        array_push($arrAPKey,$rsWorkOrderKey['key']);
        
        $rsCostKey = $ap->getTableKeyAndObj($this->tableCost);   
        array_push($arrAPKey,$rsCostKey['key']);
         
        $rsAP = $ap->searchData('','',true,' and  '.$ap->tableName.'.refheaderkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$ap->tableName.'.reftabletype in ('.implode(',',$arrAPKey).') and '.$ap->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAP);$i++) { 
            $ap->changeStatus($rsAP[$i]['pkey'],4,'',false, true); 
             
            // updaterefcashoutcode 
            
            $amountField = '';
            if ($rsAP[$i]['reftabletype'] == $rsWorkOrderKey['key']){
                $tableName = $this->tableName;
            }else{ 
                // ini untuk cost di detail
                $tableName = $this->tableCost;
                $amountField = 'amount = 0,';
            }
            
            //$tableName = ($rsAP[$i]['reftabletype'] == $rsWorkOrderKey['key']) ? $this->tableName : $this->tableCost;
        
            $sql = 'update '.$tableName.' set '.$amountField.' refcashoutkey = 0 where pkey = ' . $this->oDbCon->paramString($rsAP[$i]['refkey']); 
            $this->oDbCon->execute($sql);
        }
         
         // sekalian cancel ketika backconfirmed
//        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    }


    function cancelCommissionAP($rsHeader){
        $apEmployeeCommission = new APEmployeeCommission(); 
        
        $rsWorkOrderKey = $this->getTableKeyAndObj($this->tableName);   
        $tablekeyWO = $rsWorkOrderKey['key'];
         
        $rsAPCommission = $apEmployeeCommission->searchData('','',true,' and  '.$apEmployeeCommission->tableName.'.refheaderkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$tablekeyWO.' and '.$apEmployeeCommission->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAPCommission);$i++) { 
            $apEmployeeCommission->changeStatus($rsAPCommission[$i]['pkey'],4,'',false, true);  
            
            // updaterefcashoutcode 
              
            $sql = 'update '.$this->tableName.' set refcashoutdriverkey = 0, refcashoutcodriverkey = 0 where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']); 
            $this->oDbCon->execute($sql);
        }
         
        // sekalian cancel ketika backconfirmed
//        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    }
    
    function getMonthlySummary($startPeriod = '',$endPeriod ='',  $criteria='',$groupby = '',$reportType = 1){
        
      	// DATE FORMAT => d / m / Y
        if (empty($startPeriod)) $startPeriod = DEFAULT_EMPTY_DATE; 
        if (empty($endPeriod)) $endPeriod = date('d / m / Y');
         
        
        // be aware, perubahan group harus update ke concat index jg
        if (empty($groupby)){
            $groupby = ($reportType==1)?'driverkey':'carkey'; 
            $groupby .= ', year(trdate), month(trdate)';
        }
            
        $periodIndexField = ($reportType==1)? $this->tableName.'.driverkey':$this->tableName.'.carkey'; 
        
        $sql  = '
                select 
                    '.$this->tableEmployee.'.name as drivername,
                    '.$this->tableWarehouse.'.name as warehousename,
                    concat('.$this->tableCar.'.code, \' - \', '.$this->tableCar.'.policenumber) as carname ,
                    '.$this->tableName.'.driverkey,
                    '.$this->tableName.'.carkey,
                    concat('.$periodIndexField.',\'-\',DATE_FORMAT(trdate, \'%c%Y\'))  as periodindex, 
                    month(trdate) as month,   
                    year(trdate) as year, 
                    count('.$this->tableName.'.pkey) as totaltrip , 
                    sum('.$this->tableName.'.sellingprice) as sellingprice 
                from 
                    '.$this->tableName.', 
                    '.$this->tableCar.', 
                    '.$this->tableWarehouse.', 
                    '.$this->tableEmployee.' 
                where  
                    '.$this->tableName.'.statuskey = 3 and
                    '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey and
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                    '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey';
          
        $sql .= ' and  trdate between '. $this->oDbCon->paramDate($startPeriod.' 00:00:00',' / ') .' and LAST_DAY('. $this->oDbCon->paramDate($endPeriod.' 23:59:59',' / ') .')';
         
        if (!empty($criteria))
            $sql .= ' ' .$criteria;
        
        $sql .=' group by ' .$groupby;
            
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
    
    function getMonthlySummaryDetail($startPeriod = '',$endPeriod ='',  $criteria='', $reportType = 1){
        
        $periodIndexField = ($reportType==1)? $this->tableName.'.driverkey':$this->tableName.'.carkey'; 
        $sql = 'select 
                    '.$this->tableTruckingServiceOrderInvoiceHeader.'.code as invoicecode,
                    '.$this->tableName.'.sellingprice , 
                    concat('.$periodIndexField.',\'-\',DATE_FORMAT('.$this->tableName.'.trdate, \'%c%Y\'))  as periodindex
                from
                    '.$this->tableName.','.$this->tableTruckingServiceOrderInvoiceHeader.' 
                where 
                    '.$this->tableName.'.invoicekey = '.$this->tableTruckingServiceOrderInvoiceHeader.'.pkey and
                    '.$this->tableName.'.statuskey = 3
                ';
        
        $sql .= ' and  '.$this->tableName.'.trdate between '. $this->oDbCon->paramDate($startPeriod.' 00:00:00',' / ') .' and LAST_DAY('. $this->oDbCon->paramDate($endPeriod.' 23:59:59',' / ') .')';
        
        
        $rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }
    
    
	 function getOutsourceMonthlySummary($startPeriod = '',$endPeriod ='',  $criteria='',$groupby = '',$reportType = 1){
        
      	// DATE FORMAT => d / m / Y

        if (empty($startPeriod)) $startPeriod = DEFAULT_EMPTY_DATE; 
        if (empty($endPeriod)) $endPeriod = date('d / m / Y');
         
        
        // be aware, perubahan group harus update ke concat index jg
        if (empty($groupby)) 
            $groupby = 'supplierkey , year(trdate), month(trdate)';
   
        $periodIndexField = 'supplierkey'; 
        
        $sql  = '
                select 
                    '.$this->tableSupplier.'.pkey,
                    '.$this->tableSupplier.'.name,
                    '.$this->tableSupplier.'.code,
                    '.$this->tableWarehouse.'.name as warehousename,  
                    concat('.$periodIndexField.',\'-\',DATE_FORMAT(trdate, \'%c%Y\'))  as periodindex, 
                    month(trdate) as month,   
                    year(trdate) as year, 
                    count('.$this->tableName.'.pkey) as totaltrip , 
                    sum('.$this->tableName.'.sellingprice) as sellingprice , 
                    sum('.$this->tableName.'.outsourcecost) as outsourcecost 
                from 
                    '.$this->tableName.', 
                    '.$this->tableSupplier.', 
                    '.$this->tableWarehouse.'
                where  
                    '.$this->tableName.'.statuskey = 3 and
                    '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey and
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey';
          
        $sql .= ' and  trdate between '. $this->oDbCon->paramDate($startPeriod.' 00:00:00',' / ') .' and LAST_DAY('. $this->oDbCon->paramDate($endPeriod.' 23:59:59',' / ') .')';
         
        if (!empty($criteria))
            $sql .= ' ' .$criteria;
        
        $sql .=' group by ' .$groupby;
        
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
	
    function generateCarSchedule($carkey=array(),$criteria=''){

        $sql = 'select 
                '.$this->tableName.'.code,
                '.$this->tableName.'.trdate,
                date('.$this->tableName.'.stuffingdatetime) as stuffingdatetimeshort, 
                 concat('.$this->tableName.'.code, \' - \', '.$this->tableName.'.trdate) as periode ,
                '.$this->tableEmployee.'.name as drivername ,
                '.$this->tableServiceOrderHeader.'.code as serviceordercode,
                '.$this->tableCustomer.'.name as customername,
                '.$this->tableCar.'.pkey,
                '.$this->tableCar.'.policenumber
            from
                '.$this->tableName.'
			         left join '.$this->tableEmployee.' on '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey ,                
                '.$this->tableServiceOrderHeader.',
                '.$this->tableCustomer.',
                '.$this->tableCar.'
            where
                '.$this->tableName.'.statuskey in (2,3) and
                '.$this->tableName.'.refkey = '.$this->tableServiceOrderHeader.'.pkey  and  
                '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey and
                '.$this->tableServiceOrderHeader.'.customerkey = '.$this->tableCustomer.'.pkey';
                
        if (!empty($carkey))  
            $sql .=  '  and '.$this->tableName.'.carkey in (' .$this->oDbCon->paramString($carkey,',').')'; 
        
        if (!empty($criteria))
            $sql .= ' ' .$criteria;
         
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }    
    
     function updateDataAfterRealization($rsHeader,$rsDetail, $action){ 
        // $action => 1 : confirm, 2: reverse confirm
        
        $id = $rsHeader[0]['refkey2'];
        $realizationkey = $rsHeader[0]['pkey'];
           
        // REALISASI BERASAL DR BIAYA INHOUSE, JD ASUMSI TIDAK AD PPN DAN PPH
         
        // update biaya yagn langsung ditambahkan dr realisasi
        //hapus semua biaya yg berasal dr realisasi
        $sql = 'delete from '.$this->tableCost.' where realizationkey = '. $this->oDbCon->paramString($realizationkey).' and refcashoutkey = 0 and refkey = ' . $this->oDbCon->paramString($id);
        $this->oDbCon->execute($sql);  
        
        foreach($rsDetail as $row){ 
             
            $realCostValue = 0;
            $isrealization = 0;
            
            if($action == 1){
               $realCostValue = $row['realcostvalue'];
               $isrealization = 1;
                    
                // add biaya yagn dr realisasi
                // sementar aasumsi yg terima adjustment selalu KARYAWAN
                if($row['settlementtypekey'] == 0){ 
                    //insert ulang biaya dar realisasi
                    $sql = 'insert into '.$this->tableCost.' (qty,refkey,costkey ,amount, employeekey, isrealization, realizationkey, total ) 
                            values  ('. $this->oDbCon->paramString($row['qty']).',
                                     '. $this->oDbCon->paramString($id).',
                                     '. $this->oDbCon->paramString($row['costkey']).',
                                     '. $this->oDbCon->paramString($realCostValue).',
                                     '.$this->oDbCon->paramString($rsHeader[0]['employeekey']).',
                                     1,
                                     '. $this->oDbCon->paramString($realizationkey).',
                                     '. $this->oDbCon->paramString($row['amount']).'
                                     ) ';
                    $this->oDbCon->execute($sql);  
                }
            }
            
            $sql = 'update  '.$this->tableCost.'  
                    set  
                        amount = '.$this->oDbCon->paramString($realCostValue).', 
                        isrealization = '.$this->oDbCon->paramString($isrealization).' , 
                        realizationkey = '.$this->oDbCon->paramString($realizationkey).' , 
                        total = '.$this->oDbCon->paramString($row['amount']).' 
                    where  '.$this->tableCost.'.pkey = ' . $this->oDbCon->paramString($row['refkey2']);  
            $this->oDbCon->execute($sql);  
            
        }
         

         
        $truckingServiceOrder = new TruckingServiceOrder(); 
        $rs = $this->getDataRowById($id);
        $truckingServiceOrder->updateSalesWorkOrderCost($rs[0]['refkey']);
        
        // hanya kalo statuskey nya SELESAI
        if($rs[0]['statuskey'] == 3)
            $this->addCarTurnover($rs);
        
    }
  
	function getWorkOrderInformationForJobOrder($arrJOkey = array(),$criteria='', $orderBy=''){
		
		// karena searchData sudah berat kalo byk, jd dipisah querynya
		// default statusnya 1,2,3
		
		$sql = 'select 
					'.$this->tableName.'.code,
					'.$this->tableName.'.stuffingdatetime,
					'.$this->tableName.'.containernumber,
					'.$this->tableName.'.container2number,
					'.$this->tableName.'.sealnumber,
					'.$this->tableName.'.seal2number,
					'.$this->tableName.'.outsourcecarregistrationnumber,
					'.$this->tableName.'.isoutsource,
					'.$this->tableName.'.trdesc,
					'.$this->tableName.'.statuskey,
					'.$this->tableSupplier.'.name as suppliername,
					'.$this->tableCar.'.code as policecode,
					'.$this->tableCar.'.policenumber,
					'.$this->tableEmployee.'.name  as drivername,
					'.$this->tableStatus.'.status as statusname
				 from 
					 '.$this->tableName.' 
					 left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey
					 left join '.$this->tableCar.' on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey 
				 	 left join '.$this->tableEmployee.' on '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey
				 	 left join '.$this->tableStatus.' on '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
				 where 
				 	'.$this->tableName.' .refdetailkey	in('.$this->oDbCon->paramString($arrJOkey,',').') and
					'.$this->tableName.'.statuskey in (1,2,3)
				';
		
		if(!empty($criteria)) $sql .= ' ' .$criteria;
		if(!empty($orderBy)) $sql .= ' ' .$orderBy;
		 
		return 	$this->oDbCon->doQuery($sql);
	}
	
	
    function getWorkProgress($statuskey = array(2)){
        
        $sql = ' select
                    '.$this->tableServiceOrderHeader.'.code as socode,
                    '.$this->tableName.'.routefrom,
                    '.$this->tableName.'.routeto,
                    '.$this->tableServiceOrderHeader.'.donumber,
                    '.$this->tableCustomer.'.name as customername,
                    '.$this->tableName.'.pkey as wokey,
                    '.$this->tableName.'.code as wocode,
                    '.$this->tableCar.'.pkey as carkey,
                    '.$this->tableCar.'.policenumber,
                    '.$this->tableCar.'.gpstrackerid,
                    '.$this->tableCar.'.gpskey,
                    '.$this->tableEmployee.'.name as drivername,
					lower('.$this->tableGPS.'.name) as providername
                from  
                    '.$this->tableName.' 
                        left join  '.$this->tableCar.' on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey
                        left join  '.$this->tableGPS.' on '.$this->tableCar.'.gpskey = '.$this->tableGPS.'.pkey
                        left join  '.$this->tableEmployee.' on '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey,
                    '.$this->tableCustomer.',
                    '.$this->tableServiceOrderHeader.'
                where
                    '.$this->tableName.'.statuskey in ('.implode(',',$statuskey).') and
					'.$this->tableServiceOrderHeader.'.statuskey in (2) and
                    '.$this->tableName.'.refkey = '.$this->tableServiceOrderHeader.'.pkey and
                    '.$this->tableServiceOrderHeader.'.customerkey = '.$this->tableCustomer.'.pkey and
                    '.$this->tableName.'.carkey <> \'\'';
          
            $sql .= $this->getWarehouseCriteria();
            $sql .= ' order by socode asc, wocode asc, '.$this->tableName.'.trdate asc';
         
        return $this->oDbCon->doQuery($sql);
        
    }
    
    function updateVehicleByRegistrationNumber($id,$employeekey, $carRegistrationNumber){ 
        
         try{  
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);
 
                $carRegistrationNumber = trim($carRegistrationNumber);
             
                // validasi
             	$arrayToJs = array();
           
                // pastikan SPK adalah SPK sopir yang masih aktif  
                $rsWorkOrder = $this->searchData($this->tableName.'.pkey', $id,true,' and ' .$this->tableName.'.driverkey = '.$this->oDbCon->paramString($employeekey).' and '.$this->tableName.'.statuskey in (1,2)' );
              
                // jika kode verifikasi tidak cocok
                if(empty($rsWorkOrder)) 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg[213]);  
             
                if(empty($carRegistrationNumber)){  
                    $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][1]); 
                }else{
                    $car = new Car();
                    $rsCar = $car->searchData($car->tableName.'.policenumber',$carRegistrationNumber);
                    if(empty($rsCar))
                        $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][9]); 
                } 
   
                if(!empty($arrayToJs))  return $arrayToJs;
               
                $sql = 'update 
                            '.$this->tableName.'
                        set carkey = (select pkey from '.$this->tableCar.' where policenumber = '.$this->oDbCon->paramString($carRegistrationNumber).')  
                        where 
                            pkey = ' .$this->oDbCon->paramString($id);
              
                $this->oDbCon->execute($sql);   
                $this->setTransactionLog(UPDATE_DATA,$id);
             
                $this->oDbCon->endTrans();
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
                

        }catch (Exception $e){
            $this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
        }
        
 		return $arrayToJs; 
    }
    
    
     function takeWorkOrder($workOrderCode,$employeekey,$verificationCode){ 
        
         try{  
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);
 
                $verificationCode = trim($verificationCode);
                $workOrderCode = trim($workOrderCode);
                
             
                // validasi
             	$arrayToJs = array();
          
                $rsWorkOrder = $this->searchData($this->tableName.'.code',$workOrderCode,true,' and ' . $this->tableName.'.statuskey in (1,2)');
             
                // jika kode verifikasi tidak cocok
                if(empty($rsWorkOrder)) 
                    $this->addErrorList($arrayToJs,false,$this->errorMsg[213]);  
               
                if(empty($verificationCode) || $rsWorkOrder[0]['verificationcode'] <> $verificationCode) 
                    $this->addErrorList($arrayToJs,false,$this->lang['verificationFailed']); 
             
             
             
                if(!empty($arrayToJs))  return $arrayToJs;
              
                $id = $rsWorkOrder[0]['pkey'];
             
                $sql = 'update 
                            '.$this->tableName.'
                        set 
                            driverkey = '.$this->oDbCon->paramString($employeekey).' 
                        where 
                            pkey = ' .$this->oDbCon->paramString($id);
              
                $this->oDbCon->execute($sql);   
                $this->setTransactionLog(UPDATE_DATA,$id);
             
                $this->oDbCon->endTrans();
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
                

        }catch (Exception $e){
            $this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
        }
        
 		return $arrayToJs; 
    }
    
 function getQueryForList(){
         
		
	  if($this->consigneeFromWorkOrder)
		  $consigneeJoinSQL =  ' left join '.$this->tableConsignee.' on '.$this->tableName.'.consigneekey = '.$this->tableConsignee.'.pkey, '.$this->tableServiceOrderHeader ;
	  else
		  $consigneeJoinSQL = ',' .$this->tableServiceOrderHeader.' left join '.$this->tableConsignee.' on '.$this->tableServiceOrderHeader.'.consigneekey = '.$this->tableConsignee.'.pkey' ;
			
		
         $sql = '
			SELECT '.$this->tableName.'.* ,  
			   concat('.$this->tableName.'.routefrom, \' - \', '.$this->tableName.'.routeto) as route ,
			   '.$this->tableStatus.'.status as statusname ,  
			   '.$this->tableCar.'.code as carcode ,   
			   '.$this->tableCar.'.policenumber ,   
			   '.$this->tableCustomer.'.code as customercode ,
			   '.$this->tableCustomer.'.name as customername ,
               '.$this->tableServiceOrderHeader.'.code as serviceordercode,
               '.$this->tableServiceOrderHeader.'.trdate as serviceorderdate,
               '.$this->tableServiceOrderHeader.'.shipmentnumber,
               '.$this->tableServiceOrderHeader.'.donumber,
               '.$this->tableConsignee.'.code as consigneecode,
               '.$this->tableConsignee.'.name as consigneename,  
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableWarehouse.'.code as warehousecode, 
               '.$this->tableItem.'.code as containercode,
               '.$this->tableItem.'.name as containername,
               '.$this->tableDepot.'.code as depotcode,
               '.$this->tableDepot.'.name as depotname,
               '.$this->tableTerminal.'.code as terminalcode,   
               '.$this->tableTerminal.'.name as terminalname,   
               '.$this->tableTruckingJob.'.name as jobtypename, 
               '.$this->tableLocation.'.code as locationcode, 
               '.$this->tableLocation.'.name as locationname, 
               outsource_supplier.code as outsourcesuppliercode,
               outsource_supplier.name as outsourcesuppliername,
               IF(isoutsource=1, "TL", "") as TL,
               IF(isoutsource=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as outsourceicon,
               vendor.code as outsourcecode,
               vendor.name as outsourcename,
               '.$this->tableCategory.'.code as categorycode, 
               '.$this->tableCategory.'.name as categoryname,
               '.$this->tableEmployee.'.code as drivercode, 
               '.$this->tableEmployee.'.name as drivername, 
               codriver.code as codrivercode, 
               codriver.name as codrivername,
               '.$this->tableChassis.'.code as chassiscode , 
               '.$this->tableChassis.'.chassisnumber
			FROM 
                '.$this->tableStatus.', 
                '.$this->tableItem.',   
                '.$this->tableCustomer.',    
                '.$this->tableWarehouse.',   
                '.$this->tableTruckingJob.',  
                '.$this->tableName.'  
                    left join '.$this->tableDepot.' on '.$this->tableName.'.depotkey = '.$this->tableDepot.'.pkey 
                    left join '.$this->tableTerminal.' on '.$this->tableName.'.terminalkey = '.$this->tableTerminal.'.pkey   
                    left join '.$this->tableEmployee.' on '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey
                    left join '.$this->tableEmployee.' codriver on '.$this->tableName.'.codriverkey = codriver.pkey 
                    left join '.$this->tableCar.' on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey     
                    left join '.$this->tableLocation.' on '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey
                    left join '.$this->tableSupplier.' outsource_supplier on '.$this->tableName.'.supplierkey = outsource_supplier.pkey 
                    left join '.$this->tableSupplier.' vendor on '.$this->tableName.'.supplierkey = vendor.pkey
                    left join '.$this->tableChassis.' on '.$this->tableName.'.chassiskey = '.$this->tableChassis.'.pkey
                    left join '.$this->tableCategory.' on '.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey
                '.$consigneeJoinSQL.'
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                  '.$this->tableName.'.refkey = '.$this->tableServiceOrderHeader.'.pkey  and   
                  '.$this->tableName.'.itemkey  = '.$this->tableItem.'.pkey and
                  '.$this->tableName.'.jobtypekey  = '.$this->tableTruckingJob.'.pkey and
                  '.$this->tableServiceOrderHeader.'.customerkey = '.$this->tableCustomer.'.pkey 
 		' .$this->criteria ; 
		   
       
		
        $sql .=  $this->getWarehouseCriteria() ;
		
       return $sql;
        
    }
    
    function getTotalOutsource($statuskey = ''){
        $sql = 'select coalesce(count(pkey),0) as total from '.$this->tableName.' where isoutsource = 1';
        if (!empty($statuskey))
            $sql .= ' and statuskey = ' . $this->oDbCon->paramString($statuskey);
            
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs[0]['total'];
        
    }
    
    function getVehicleDetailWithRelatedInformation($pkey,$criteria=''){ 
       
	   $sql = 'select
	   			'.$this->tableWorkOrderCarDetail .'.*,
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItem.'.name as itemname 
              from
			  	'.$this->tableWorkOrderCarDetail .', 
                '.$this->tableItem.' 
			  where
			  	'.$this->tableWorkOrderCarDetail .'.itemkey = '.$this->tableItem.'.pkey and  
			  	refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';
       
        $sql .= $criteria;
         
        $sql .= ' order by pkey asc';
           
		return $this->oDbCon->doQuery($sql);
	
   }
    
    function getDetailForAPI($arrKey,$arrIndex = array()){
        $rsDetailsCol = array();
        
        if(in_array('vehicle_detail',$arrIndex)){  
            $rsDetails = $this->getVehicleDetailWithRelatedInformation($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey');
            $rsDetailsCol['vehicle_detail'] = $rsDetails;
        }

        if(in_array('cost_detail',$arrIndex)){  
            $rsDetails = $this->getCostDetail($arrKey); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'refkey');
            $rsDetailsCol['cost_detail'] = $rsDetails;
        }


        if(in_array('job_progress_detail',$arrIndex)){
            $rsJobProgressDetails = $this->getJobProgressDetail($arrKey); 

            // asumsi selalu ad, karena perlu pkey dari table index utk update, agar lebih mudah
            
            //if(empty($rsJobProgressDetails)) {
            //    //jika di spk masih kosong, ambil dari job progrrss by category
            //    $jobProgress = new JobProgress();
            //    
            //    $rsHeader = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.code',$this->tableName.'.refkey',$this->tableName.'.categorykey'), 
            //                                    ' and ' . $this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrKey,',').') ');
//
            //    $rsJobProgressDetail = $jobProgress->getJobProgressByCategory($rsHeader[0]['categorykey']);
 //
            //    //return field samakan dengan dari detail
            //    $arrJobProgressDetails = array();
            //    foreach($rsJobProgressDetail as $jobProgressRow) {
            //        $arrJobProgressDetails[] = [
            //            'pkey' => 0,
            //            'refkey' => $rsHeader[0]['pkey'],
            //            'number' => $jobProgressRow['number'],
            //            'jobprogresskey' =>$jobProgressRow['pkey'],
            //            'jobprogressheaderkey' => $jobProgressRow['refkey'],
            //            'jobprogresscode' => $jobProgressRow['code'],
            //            'jobprogressname' => $jobProgressRow['name'],
            //            'completeddate' => '0000-00-00 00:00:00',
            //            'iscompleted' => 0
            //        ];
            //    }           
            //    $rsJobProgressDetails = $arrJobProgressDetails;
            //}
            
            
            $rsJobProgressDetails = $this->reindexDetailCollections($rsJobProgressDetails,'refkey');
            $rsDetailsCol['job_progress_detail'] = $rsJobProgressDetails;
        }
        
        return $rsDetailsCol;
    }
    
    function updateQtyInvoiced($purchaseOrderKey,$pkey,$supplierKey){  
        
        // sementar utk model LOGOL dulu
          

        $rsCarDetail = $this->getCarDetail($pkey); 
        $rsCost =  $this->getCostDetail($pkey,'',' and '. $this->tableCost .'.supplierkey = ' . $this->oDbCon->paramString($supplierKey));  
        
              
        // update setiap SO, sudah brp qty yg ditagih, item dan cost 
        try{
            
            if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
            
            
            for($j=0;$j<count($rsCarDetail);$j++){

                $totalInvoiced = $this->getTotalQtyInvoiced($pkey,$rsCarDetail[$j]['pkey'],$rsCarDetail[$j]['itemkey'], 1);
                $purchaseOrderKeyReference = ($totalInvoiced > 0) ? $purchaseOrderKey : 0;
                
                $sql = 'update   ' . $this->tableWorkOrderCarDetail.'
                        set  qtyinvoiced = '.$this->oDbCon->paramString($totalInvoiced).',
                             purchaseorderkey = '.$this->oDbCon->paramString($purchaseOrderKeyReference).' 
                        where  pkey = '.$this->oDbCon->paramString($rsCarDetail[$j]['pkey']).' 
                        ';

                $this->oDbCon->execute($sql);

            } 

            
            for($j=0;$j<count($rsCost);$j++){

                $totalInvoiced = $this->getTotalQtyInvoiced($pkey,$rsCost[$j]['pkey'],$rsCost[$j]['costkey'], 2); 
                $purchaseOrderKeyReference = ($totalInvoiced > 0) ? $purchaseOrderKey : 0;
                
                $sql = 'update  ' . $this->tableCost.'
                        set  qtyinvoiced = '.$this->oDbCon->paramString($totalInvoiced).' ,
                             purchaseorderkey = '.$this->oDbCon->paramString($purchaseOrderKeyReference).' 
                        where  pkey = '.$this->oDbCon->paramString($rsCost[$j]['pkey']).' 
                        ';

                $this->oDbCon->execute($sql);
            }  
            
            $this->oDbCon->endTrans();
             
		
	    }  catch(Exception $e){ 
            $this->oDbCon->rollback();  
		}		
         
    }
    
        function getTotalQtyInvoiced($pkey,$detailkey, $itemkey, $type){ 
        // tambahkan paramter itemkey untuk membedakan dr detail atau selling cost
        // dengan ada item key sudah pasti beda karena detail item dan item cost 1 table, jd pkey pasti beda
        // kenapa $itemkeyny jd gk kepake ??
        
            $truckingPurchase = new TruckingPurchase(); 
        
         // update setiap SO, sudah brp qty yg ditagih, item dan cost
            $sql = 'select 
                        coalesce(sum('.$truckingPurchase->tableNameItemDetail.'.qty),0) as totalinvoiced
                    from  
                        '.$truckingPurchase->tableName.',  
                        '.$truckingPurchase->tableNameDetail.',
                        '.$truckingPurchase->tableNameItemDetail.' 
                    where 
                        '.$truckingPurchase->tableName.'.pkey = '.$truckingPurchase->tableNameDetail.'.refkey and
                        '.$truckingPurchase->tableNameDetail.'.pkey = '.$truckingPurchase->tableNameItemDetail.'.refkey and
                        '.$truckingPurchase->tableName.'.statuskey in (2,3) and
                        '.$truckingPurchase->tableNameDetail.'.wokey = '.$this->oDbCon->paramString($pkey).' and
                        '.$truckingPurchase->tableNameItemDetail.'.wodetailkey = '.$this->oDbCon->paramString($detailkey).' and
                        '.$truckingPurchase->tableNameItemDetail.'.detailtype = '.$this->oDbCon->paramString($type).' and
                        '.$truckingPurchase->tableNameItemDetail.'.itemkey =  '.$this->oDbCon->paramString($itemkey).' 
                    ';
  
            //$this->setLog($sql.true);
            $rsTotal = $this->oDbCon->doQuery($sql);
         
            return $rsTotal[0]['totalinvoiced'];
    }
 
    function searchAvailableItemForPurchase($arrSOKey,$supplierkey, $criteria=''){ 
         
         $truckingServiceOrder = new TruckingServiceOrder();
        
         $rs = array(); 
         $arrJOKey = array();
         $arrWOKey = array();
        
        // cari slh satunya ada gk, antara outsource jasa dan biaya detail 
        
        // cari outsource dulu, ad 2 jenis nantinya, detail seperti logol atau bkn
          $sql = 'select 
                   '.$this->tableName .'.pkey,
                   '.$this->tableName .'.code,
                   '.$this->tableServiceOrderHeader .'.pkey as sokey,
                   '.$this->tableServiceOrderHeader .'.code as socode,
                    concat(1,\'-\','.$this->tableWorkOrderCarDetail.'.pkey,\'-\','.$this->tableWorkOrderCarDetail.'.itemkey) as joinkey,
                    '.$this->tableWorkOrderCarDetail.'.pkey as wodetailkey,
                    '.$this->tableWorkOrderCarDetail.'.refkey,
                    '.$this->tableWorkOrderCarDetail.'.total,
                    ('.$this->tableWorkOrderCarDetail.'.qty - '.$this->tableWorkOrderCarDetail.'.qtyinvoiced) as outstandingqty,
                    '.$this->tableWorkOrderCarDetail.'.ispriceincludetax,
                    '.$this->tableWorkOrderCarDetail.'.taxpercentage,
                    '.$this->tableWorkOrderCarDetail.'.tax23percentage,
                    '.$this->tableWorkOrderCarDetail.'.taxvalue,
                    '.$this->tableWorkOrderCarDetail.'.total, 
                    0 as isreimburse, 
                    1 as purchasetype,
                    '.$this->tableWorkOrderCarDetail.'.price as priceinunit,
                    '.$this->tableWorkOrderCarDetail.'.itemkey,
                    '.$this->tableWorkOrderCarDetail.'.carregistrationnumber as remark,
                    '.$this->tableItem.'.name as itemname
               from
                   '.$this->tableWorkOrderCarDetail .', 
                   '.$this->tableName .', 
                   '.$this->tableServiceOrderHeader.',
                   '.$this->tableItem .' 
               where 
                   '.$this->tableName .'.pkey = '.$this->tableWorkOrderCarDetail .'.refkey and
                   '.$this->tableName .'.refkey = '.$this->tableServiceOrderHeader .'.pkey and  
                   '.$this->tableName .'.supplierkey = '.$this->oDbCon->paramString($supplierkey) .' and 
                   '.$this->tableServiceOrderHeader.'.pkey in ('. $this->oDbCon->paramString($arrSOKey,',') .') and
                   ('.$this->tableWorkOrderCarDetail.'.qty - '.$this->tableWorkOrderCarDetail.'.qtyinvoiced) > 0 and
                   '.$this->tableWorkOrderCarDetail .'.itemkey = '.$this->tableItem .'.pkey' ; 
     
         if (!empty($criteria))
             $sql .= $criteria;
        
         $rsVehicleDetail = $this->oDbCon->doQuery($sql);
        
         if(!empty($rsVehicleDetail)){ 
             $arrWOKey = array_merge($arrWOKey, array_column($rsVehicleDetail, 'pkey'));
             $arrJOKey = array_merge($arrJOKey, array_column($rsVehicleDetail, 'sokey'));
         }
        
         $rsVehicleDetail = $this->reindexDetailCollections($rsVehicleDetail,'refkey');
            
        
         // cari detail cost
         $sql = 'select
                   '.$this->tableName .'.pkey,
                   '.$this->tableName .'.code,
                   '.$this->tableServiceOrderHeader .'.pkey as sokey,
                   '.$this->tableServiceOrderHeader .'.code as socode,
                    '.$this->tableCost.'.pkey as wodetailkey,
                    concat(2,\'-\','.$this->tableCost.'.pkey,\'-\','.$this->tableCost.'.costkey) as joinkey,
                    '.$this->tableCost.'.refkey,
                    '.$this->tableCost.'.total,
                    '.$this->tableCost.'.qty,
                    ('.$this->tableCost.'.qty - '.$this->tableCost.'.qtyinvoiced) as outstandingqty,
                    '.$this->tableCost.'.taxpercentage,
                    '.$this->tableCost.'.tax23percentage,
                    '.$this->tableCost.'.taxvalue,
                    '.$this->tableCost.'.total,
                    '.$this->tableCost.'.isreimburse, 
                    2 as purchasetype,
                    '.$this->tableCost.'.amount as priceinunit,
                    '.$this->tableCost.'.costkey as itemkey,
                    '.$this->tableItem.'.name as itemname 
               from
                   '.$this->tableCost .', 
                   '.$this->tableName .', 
                   '.$this->tableServiceOrderHeader.',
                   '.$this->tableItem .' 
               where 
                 '.$this->tableName .'.pkey = '.$this->tableCost .'.refkey and
                 '.$this->tableName .'.refkey = '.$this->tableServiceOrderHeader .'.pkey and 
                 '.$this->tableCost .'.supplierkey = '.$this->oDbCon->paramString($supplierkey) .' and 
                 '.$this->tableServiceOrderHeader.'.pkey in ('. $this->oDbCon->paramString($arrSOKey,',') .') and
                 ('.$this->tableCost.'.qty - '.$this->tableCost.'.qtyinvoiced) > 0  and
                 '.$this->tableItem .'.pkey = '.$this->tableCost .'.costkey' ; 
         
         if (!empty($criteria))
             $sql .= $criteria;
         
          $rsCostDetail = $this->oDbCon->doQuery($sql);
          
        
         if(!empty($rsCostDetail)){  
          $arrWOKey = array_merge($arrWOKey, array_column($rsCostDetail, 'pkey'));
          $arrJOKey = array_merge($arrJOKey, array_column($rsCostDetail, 'sokey'));
         }
        
          $rsCostDetail = $this->reindexDetailCollections($rsCostDetail,'refkey');
          
          $arrWOKey = array_unique($arrWOKey);  
        
         // baru select header nya berdasrakan detailny ad ap aj
         $rsWO = $this->searchDataRow( array($this->tableName.'.pkey',$this->tableName.'.code',$this->tableName.'.refkey',$this->tableName.'.trdate') , 
                                     ' and '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrWOKey,',') .') 
                                       and '.$this->tableName.'.statuskey = 3');
         
        // informasi JO =======================
         $rsJO =  $truckingServiceOrder->searchDataRow( array($truckingServiceOrder->tableName.'.pkey',$truckingServiceOrder->tableName.'.code') , 
                                     ' and '.$truckingServiceOrder->tableName.'.pkey in ('.$this->oDbCon->paramString($arrJOKey,',') .')');
        
         $rsJO = array_column($rsJO,null,'pkey');
            
         
        
         foreach($rsWO as $key=>$row){
             $rsWO[$key]['key'] = $row['pkey'];
             $rsWO[$key]['value'] = $row['code'];
             $rsWO[$key]['socode'] = $rsJO[$row['refkey']]['code'];
             $rsWO[$key]['sokey'] = $rsJO[$row['refkey']]['pkey'];
             
             $arrDetail = array();
             if (!empty($rsVehicleDetail[$row['pkey']])) $arrDetail = array_merge( $arrDetail,$rsVehicleDetail[$row['pkey']]); 
             if (!empty($rsCostDetail[$row['pkey']])) $arrDetail = array_merge( $arrDetail,$rsCostDetail[$row['pkey']]); 
                 
             $rsWO[$key]['detail'] = $arrDetail;
         }
            
         return $rsWO;
         
    }
    
    function  getDetailJobOrder($arrKey){
        // sementara utk DN
        return array();
    }
	
	
    function getDataForUnInvoicedReport($criteria, $order='')
    {
        $rs = array(); 
        $arrJOKey = array();

            $sql = 'select 
                   '.$this->tableName .'.pkey,
                   '.$this->tableName .'.code,
                   '.$this->tableName .'.trdate,
                   '.$this->tableName .'.warehousekey,
                   '.$this->tableName .'.supplierkey,
                   '.$this->tableSupplier .'.name as suppliername,
                   '.$this->tableWarehouse .'.name as warehousename,
                   '.$this->tableServiceOrderHeader .'.pkey as sokey,
                   '.$this->tableServiceOrderHeader .'.code as socode,
                    concat(1,\'-\','.$this->tableWorkOrderCarDetail.'.pkey,\'-\','.$this->tableWorkOrderCarDetail.'.itemkey) as joinkey,
                    '.$this->tableWorkOrderCarDetail.'.pkey as wodetailkey,
                    '.$this->tableWorkOrderCarDetail.'.refkey,
                    '.$this->tableWorkOrderCarDetail.'.total,  
                    ('.$this->tableWorkOrderCarDetail.'.qty - '.$this->tableWorkOrderCarDetail.'.qtyinvoiced) as outstandingqty,
                    '.$this->tableWorkOrderCarDetail.'.total, 
                    '.$this->tableWorkOrderCarDetail.'.price as priceinunit,
                    '.$this->tableWorkOrderCarDetail.'.itemkey,
                    '.$this->tableWorkOrderCarDetail.'.carregistrationnumber as remark,
                    '.$this->tableItem.'.name as itemname
               from
                   '.$this->tableWorkOrderCarDetail .', 
                   '.$this->tableName .'
                        left join '. $this->tableSupplier .' on '. $this->tableName .'.supplierkey = '.  $this->tableSupplier .'.pkey
                        left join '. $this->tableWarehouse .' on '. $this->tableName .'.warehousekey = '.  $this->tableWarehouse .'.pkey,
                   '.$this->tableServiceOrderHeader.',
                   '.$this->tableItem .' 
               where 
                   '.$this->tableName .'.pkey = '.$this->tableWorkOrderCarDetail .'.refkey and
                   '.$this->tableName .'.refkey = '.$this->tableServiceOrderHeader .'.pkey and  
                   ('.$this->tableWorkOrderCarDetail.'.qty - '.$this->tableWorkOrderCarDetail.'.qtyinvoiced) > 0 and
                   '.$this->tableWorkOrderCarDetail .'.itemkey = '.$this->tableItem .'.pkey and 
                   '. $this->tableName .'.supplierkey <> 0' ; 

        if (!empty($criteria))
            $sql .= $criteria;
        if (!empty($order))
            $sql .= $order;
        

        $rsVehicleDetail = $this->oDbCon->doQuery($sql);


         $sql = 'select
                   '.$this->tableName .'.pkey,
                   '.$this->tableName .'.code,
                   '.$this->tableName .'.trdate,
                   '.$this->tableWarehouse.'.name as warehousename,
                   '.$this->tableSupplier.'.name as suppliername,
                   '.$this->tableServiceOrderHeader .'.pkey as sokey,
                   '.$this->tableServiceOrderHeader .'.code as socode,
                    '.$this->tableCost.'.pkey as wodetailkey,
                    concat(2,\'-\','.$this->tableCost.'.pkey,\'-\','.$this->tableCost.'.costkey) as joinkey,
                    '.$this->tableCost.'.refkey,
                    '.$this->tableCost.'.total,
                    ('.$this->tableCost.'.qty - '.$this->tableCost.'.qtyinvoiced) as outstandingqty,
                    '.$this->tableCost.'.total,
                    '.$this->tableCost.'.amount as priceinunit,
                    '.$this->tableCost.'.costkey as itemkey,
                    '.$this->tableItem.'.name as itemname 
               from
                   '.$this->tableCost .'
                        left join '. $this->tableSupplier .' on '. $this->tableCost .'.supplierkey = '. $this->tableSupplier .'.pkey,
                   '.$this->tableName .'
                        left join '. $this->tableWarehouse .' on '. $this->tableName .'.warehousekey = '. $this->tableWarehouse .'.pkey,
                   '.$this->tableServiceOrderHeader.',
                   '.$this->tableItem .'
               where 
                 '.$this->tableName .'.pkey = '.$this->tableCost .'.refkey and
                 '.$this->tableName .'.refkey = '.$this->tableServiceOrderHeader .'.pkey and 
                 ('.$this->tableCost.'.qty - '.$this->tableCost.'.qtyinvoiced) > 0  and
                 '.$this->tableItem .'.pkey = '.$this->tableCost .'.costkey and
                 '. $this->tableCost .'.supplierkey <> 0 '; 
         
         if (!empty($criteria))
             $sql .= $criteria;
         if (!empty($order))
             $sql .= $order;
         
        $rsCostDetail = $this->oDbCon->doQuery($sql);

        $rs = array_merge($rsVehicleDetail, $rsCostDetail);
        
        return $rs;

    }
	


    function getDataForWorkOrderUpdate($criteria = '', $order = '') {
        $sql = 'select
                    ' . $this->tableName . '.pkey,
                    ' . $this->tableName . '.code,
                    ' . $this->tableName . '.stuffingdatetime,
                    ' . $this->tableName . '.stuffingaddress,
                    ' . $this->tableName . '.routefrom,
                    ' . $this->tableName . '.routeto,
                    ' . $this->tableName . '.trdesc,
                    ' . $this->tableName . '.statuskey,
                    ' . $this->tableWarehouse .'.name as wareohusename,
                    ' . $this->tableEmployee.'.name as drivername,
                    ' . $this->tableEmployee.'.pkey as driverkey,
                    ' . $this->tableCustomer .'.name as customername,
                    ' . $this->tableServiceOrderHeader.'.code as serviceordercode,
                    ' . $this->tableServiceOrderHeader.'.trdate as serviceorderdate,
                    ' . $this->tableServiceOrderHeader.'.shipmentnumber,
                    ' . $this->tableConsignee.'.name as consigneename,
                    ' . $this->tableConsignee.'.warehousename as warehouseconsigneename,
                    ' . $this->tableConsignee.'.address, 
                    ' . $this->tableTruckingJob . '.name as jobtypename,
                    ' . $this->tableCar.'.code as policecode ,
                    ' . $this->tableCar.'.pkey as carkey ,
                    ' . $this->tableCar.'.policenumber ,
                    ' . $this->tableChassis.'.chassisnumber ,
                    ' . $this->tableLocation.'.name as locationname,
                    ' . $this->tableDepot.'.name as depotname,
					' . $this->tableItem.'.name as servicename
				from 
                    
					' . $this->tableName . '
                        left join '. $this->tableWarehouse .' on '. $this->tableName .'.warehousekey = '. $this->tableWarehouse .'.pkey
                        left join ' . $this->tableCar . ' on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey   
                        left join '.$this->tableChassis.' on '.$this->tableName.'.chassiskey = '.$this->tableChassis.'.pkey
                        left join '.$this->tableLocation.' on  '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey
                        left join '.$this->tableEmployee.' on '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey 
                        left join '.$this->tableDepot.' on '.$this->tableName.'.depotkey = '.$this->tableDepot.'.pkey
                        left join '.$this->tableServiceOrderDetail.' on '.$this->tableName.'.refdetailkey = '.$this->tableServiceOrderDetail.'.pkey
                        left join '.$this->tableItem.' on '.$this->tableServiceOrderDetail.'.itemkey = '.$this->tableItem.'.pkey,
                    ' .$this->tableServiceOrderHeader.'
                        left join '.$this->tableConsignee.' on '.$this->tableServiceOrderHeader.'.consigneekey = '.$this->tableConsignee.'.pkey, 
					' . $this->tableTruckingJob . ', 
                    ' .$this->tableCustomer.',
                    ' . $this->tableStatus . ' 
				where  		 
                    ' . $this->tableName . '.jobtypekey  = ' . $this->tableTruckingJob . '.pkey and      
                    ' .$this->tableName.'.refkey = '.$this->tableServiceOrderHeader.'.pkey  and 
                    ' .$this->tableServiceOrderHeader.'.customerkey = '.$this->tableCustomer.'.pkey and          
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey   
			';

        if (!empty($criteria))
            $sql .= $criteria;
		
		
		
	    $sql .=  $this->getWarehouseCriteria() ;
		
        if (!empty($order))
            $sql .= $order;
        
        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function updateWorkOrder($arrData,$autoConfirmed = true) {
		 
        $result = [];        

		if (empty($arrData)) $result;
		
        $employee = new Employee();
        $car = new Car();

        try {
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
         

                $woCode = $arrData['workOrderCode'];
                $pkey = $arrData['hidWorkOrderKey'];
                $carkey = $arrData['hidCarKey'];
                $driverkey = $arrData['hidDriverKey'];
                $trDesc = $arrData['trDesc'];
        
				
				//cek data
				$rsWO = $this->getDataRowById($pkey);  
				if(empty($rsWO)) {
					array_push($result, [ 'valid' => false, 'message' =>  $woCode . '. ' . $this->lang['noDataFound']]);
				}else if(in_array($rsWO[0]['statuskey'],array(3,4))){ 
					array_push($result, [ 'valid' => false, 'message' =>  $woCode . '. ' . $this->errorMsg[212] ]);
				} 
				 
                if(empty($carkey)){ 
                    array_push($result, ['valid' => false,'message' =>  $woCode . '. ' . $this->errorMsg['car'][1]]);
				}else{
					$rsCar = $car->getDataRowById($carkey);
					if(empty($rsCar))  
						array_push($result, [ 'valid' => false, 'message' =>  $woCode . '. ' . $this->errorMsg['car'][1]]); 
				}
				
				
			    if(empty($driverkey)){ 
                    array_push($result, ['valid' => false,'message' => $woCode . '. ' . $this->errorMsg['driver'][1]]);
				}else{ 
					$rsDriver = $employee->getDataRowById($driverkey); 
					if(empty($rsDriver) || $rsDriver[0]['isdriver'] == 0){
							array_push($result, ['valid' => false,'message' => $woCode . '. ' . $this->errorMsg['driver'][1]]);
					}
				  
				} 
				 
				
			  if (empty($result)){
				 	$sql = '
						UPDATE
							'. $this->tableName .'
						SET
								' . $this->tableName . '.driverkey = ' . $this->oDbCon->paramString($rsDriver[0]['pkey']) . ',
								' . $this->tableName . '.carkey = ' . $this->oDbCon->paramString($rsCar[0]['pkey']) . ',
								' . $this->tableName . '.trdesc = ' . $this->oDbCon->paramString($trDesc) . ',
								' . $this->tableName . '.modifiedon = '. $this->oDbCon->paramString(date('Y-m-d H:i:s')) .'
							WHERE
								' . $this->tableName . '.pkey = ' . $this->oDbCon->paramString($rsWO[0]['pkey']) .'
					';

					$rs = $this->oDbCon->execute($sql);
				  
				  
				    // update ulang semua detail yg blm keluar duit
				  	// utk biaya yg sudah ada penerimanya, tetep di overwrite ulang.
				 	$sql = 'update 
								'.$this->tableCost.'
							set
								'.$this->tableCost.'.employeekey = '.$this->oDbCon->paramString($rsDriver[0]['pkey']).', 
								'.$this->tableCost.'.supplierkey = 0
							where 
								'.$this->tableCost.'.refkey = '.$this->oDbCon->paramString($rsWO[0]['pkey']).' and
								'.$this->tableCost.'.refcashoutkey = 0 and
								'.$this->tableCost.'.realizationkey = 0
							';
//				    $this->setLog($sql,true);
					$rs = $this->oDbCon->execute($sql);
				  	
				  	// after update data, update ulang TCO kalo ad perubahan
					$this->updateTruckingCostCashOut($rsWO[0]['pkey']);
				  
				  	$this->setTransactionLog(UPDATE_DATA,$rsWO[0]['pkey']);
				  
				  
				  	// konfirmasi otomatis
				    if ($rsWO[0]['statuskey'] == 1 && $autoConfirmed) 
						$this->changeStatus($rsWO[0]['pkey'],2,'',false,true); 
				 
					array_push($result, ['valid' => true,'message' => $rsWO[0]['code'] .' - '.$this->lang['dataHasBeenSuccessfullyUpdated']]); 
				  
//				  $arrData = array();
//				  $arrData['hidId'] = $rsWO[0]['pkey'];
//				  $arrData['hidDriverKey'] = $driverkey;
//				  $arrData['hidCarKey'] = $carkey; 
//				  $arrDatap['hidModifiedOn'] = $rsWO[0]['modifiedon'];
//				  $arrData['_mnv-api'] = 1;
//				  
//				  $editResult = $this->editData($arrData);
//				  $this->setLog($editResult,true);
			  }

		 

            $this->oDbCon->endTrans();

        } catch (Exception $e) {
            $this->oDbCon->rollback();
            array_push($result, [  'valid' => false,  'message' => $e->getMessage()]);
        }

        return $result;
    }


   function getDataForCostByDriverReport($employeeCriteria, $criteria = '', $order=''){
	   
	    // $criteria di setiap select agar yg diunion tdk terlalu berat 
	   
        $truckingServiceOrder = new TruckingServiceOrder();

        $sql = '
            SELECT
                '. $this->tableDriverCost .'.*,
				'. $this->tableEmployee .'.name as employeename,
				'. $this->tableEmployee .'.code as employeecode, 
				'. $this->tableItem .'.name as costname,
				'. $this->tableItem .'.code as costcode
            FROM 
            (
                SELECT 
                    '. $this->tableName .'.code,
                    '. $this->tableName .'.trdate,
                    '. $this->tableCost .'.qty,
                    '. $this->tableCost .'.amount,
                    '. $this->tableCost .'.requestamount,
                    '. $this->tableCost .'.costkey,
                    '. $this->tableCost .'.employeekey,  
                    '. $this->tableName .'.statuskey,
                    '. $this->tableStatus .'.status as statusname
                FROM 
					'. $this->tableCost .', 
					'. $this->tableName .',
					'. $this->tableStatus .'
                WHERE 
                    '. $this->tableCost .'.refkey = '. $this->tableName .'.pkey and
                    '. $this->tableName .'.statuskey = '. $this->tableStatus .'.pkey and
                    '. $this->tableName .'.statuskey in (2,3)
                    '. $employeeCriteria .' 

                UNION ALL

                SELECT  
                    '. $truckingServiceOrder->tableName .'.code,
                    '. $truckingServiceOrder->tableName .'.trdate,
                    '. $truckingServiceOrder->tableHeaderCost .'.qty,
                    '. $truckingServiceOrder->tableHeaderCost .'.amount,
                    '. $truckingServiceOrder->tableHeaderCost .'.requestamount,
                    '. $truckingServiceOrder->tableHeaderCost .'.costkey,
                    '. $truckingServiceOrder->tableHeaderCost .'.employeekey,
                    '. $truckingServiceOrder->tableName .'.statuskey,
                    '. $truckingServiceOrder->tableStatus .'.status as statusname
                FROM
                    '. $truckingServiceOrder->tableHeaderCost .', 
                    '. $truckingServiceOrder->tableName .',
                    '. $truckingServiceOrder->tableStatus .' 
                WHERE
                    '. $truckingServiceOrder->tableHeaderCost .'.refkey =  '. $truckingServiceOrder->tableName .'.pkey and 
                    '. $truckingServiceOrder->tableName .'.statuskey = '. $truckingServiceOrder->tableStatus .'.pkey and
                    '. $truckingServiceOrder->tableName .'.statuskey in (2,3,4,5,6)
                    ' . $employeeCriteria . ' 
                )   
                '. $this->tableDriverCost .'
					left join '. $this->tableItem .' on '. $this->tableDriverCost .'.costkey = '. $this->tableItem .'.pkey,
				'. $this->tableEmployee .'
			WHERE
               '. $this->tableDriverCost .'.employeekey = '. $this->tableEmployee .'.pkey
        ';

	   

        if (!empty($criteria))
            $sql .= ' ' .$criteria;

        if (!empty($order))
            $sql .= ' ' .$order;
 
        $result = $this->oDbCon->doQuery($sql);

        return $result;
        
    }

// SEMENTARA UNTUK PRAJA
    function getTotalQtyCargoDetail($wokey)
    {
        $sql = '
            SELECT 
                '. $this->tableWorkOrderCargoDetail .'.refkey,
                '. $this->tableName .'.code,
                '. $this->tableWorkOrderCargoDetail .'.qty,
                '. $this->tableWorkOrderCargoDetail .'.sellingamount,
                SUM('. $this->tableWorkOrderCargoDetail .'.qty) as totalqty,
                SUM('. $this->tableWorkOrderCargoDetail .'.sellingamount) as totalsellingamount
            FROM
                '. $this->tableWorkOrderCargoDetail .',
                '. $this->tableName .'
            WHERE
                '. $this->tableWorkOrderCargoDetail .'.refkey = '. $this->tableName .'.pkey and
                '. $this->tableName .'.pkey in ('. $this->oDbCon->paramString($wokey,',') .')
                group by '. $this->tableWorkOrderCargoDetail .'.refkey
        ';

        $result = $this->oDbCon->doQuery($sql);
        // $this->setLog($result, true);
        return $result;
    }

   


    function getDataForPrintInvoiceAttachment($jokey) 
    {
        // utk praja
        $sql = '
            SELECT
                '. $this->tableWorkOrderCargoDetail .'.pkey as workorderdetailkey,
                '. $this->tableWorkOrderCargoDetail .'.refkey,
                '. $this->tableWorkOrderCargoDetail .'.destination,
                '. $this->tableWorkOrderCargoDetail .'.workorder,
                '. $this->tableWorkOrderCargoDetail .'.qty,
                '. $this->tableWorkOrderCostCargoDetail .'.pkey as workordercostcargokey,
                '. $this->tableWorkOrderCostCargoDetail .'.costkey,
                '. $this->tableWorkOrderCostCargoDetail .'.sellingprice,
                '. $this->tableWorkOrderCostCargoDetail .'.ismultipliedqty,
                '. $this->tableItem .'.name as costname,
                '. $this->tableName .'.refkey as jokey,
                '. $this->tableName .'.pkey as woheaderkey,
                '. $this->tableName .'.carkey,
                '. $this->tableName .'.trdate,
                '. $this->tableName .'.stuffingdatetime,
                '. $this->tableCar .'.policenumber,
                '. $this->tableCarCategory .'.name as carcategoryname,
                '. $this->tableLocation .'.name as destinationname,
                service.name as servicename,
                '. $this->tableServiceOrderSellingCost .'.pkey as jodetailkey
            FROM
                '. $this->tableWorkOrderCargoDetail .'
                    left join '. $this->tableWorkOrderCostCargoDetail .' on '. $this->tableWorkOrderCargoDetail .'.pkey = '. $this->tableWorkOrderCostCargoDetail .'.refkey
                    left join '. $this->tableItem .' on '. $this->tableWorkOrderCostCargoDetail .'.costkey = '. $this->tableItem .'.pkey
                    left join '. $this->tableServiceOrderSellingCost .' on '.$this->tableWorkOrderCostCargoDetail .'.pkey = '. $this->tableServiceOrderSellingCost .'.workorderdetailcostkey
                    left join '.$this->tableLocation.' on '.$this->tableWorkOrderCargoDetail.'.destinationkey = '.$this->tableLocation.'.pkey,
                '. $this->tableName .',
                '. $this->tableCar .',
                '. $this->tableItem .' as service,
                '. $this->tableCarCategory .'
            WHERE
                '. $this->tableWorkOrderCargoDetail .'.refkey = '. $this->tableName .'.pkey and
                '. $this->tableName .'.carkey = '. $this->tableCar .'.pkey and
                '. $this->tableCar .'.categorykey = '. $this->tableCarCategory .'.pkey and
                '. $this->tableName .'.itemkey = service.pkey and
                '. $this->tableName .'.refkey in ('. $this->oDbCon->paramString($jokey,',') .')
        ';

        $result = $this->oDbCon->doQuery($sql);
        
        return $result;
    }


    function getDataForImport($criteria = '') 
    {
        // utk praja
        // SPK yg boleh diedit harga jual
        $sql = '
            SELECT
                '. $this->tableWorkOrderCargoDetail .'.pkey as workorderdetailkey,
                '. $this->tableWorkOrderCargoDetail .'.refkey,
                '. $this->tableWorkOrderCargoDetail .'.destination,
                '. $this->tableLocation .'.name as destinationlocation,
                '. $this->tableWorkOrderCargoDetail .'.workorder,
                '. $this->tableWorkOrderCargoDetail .'.qty,
                '. $this->tableWorkOrderCostCargoDetail .'.pkey as workordercostcargokey,
                '. $this->tableWorkOrderCostCargoDetail .'.costkey,
                '. $this->tableWorkOrderCostCargoDetail .'.sellingprice,
                '. $this->tableWorkOrderCostCargoDetail .'.ismultipliedqty,
                '. $this->tableCustomer .'.name as customername,
                '. $this->tableName .'.refkey as jokey,
                '. $this->tableName .'.pkey as woheaderkey,
                '. $this->tableName .'.code as wocode,
                '. $this->tableName .'.carkey,
                '. $this->tableName .'.trdate,
                '. $this->tableName .'.stuffingdatetime
            FROM
                '. $this->tableWorkOrderCargoDetail .'
                    left join '. $this->tableWorkOrderCostCargoDetail .' on '. $this->tableWorkOrderCargoDetail .'.pkey = '. $this->tableWorkOrderCostCargoDetail .'.refkey
                    left join '. $this->tableLocation .' on '. $this->tableWorkOrderCargoDetail .'.destinationkey = '. $this->tableLocation .'.pkey,
                '. $this->tableName .'
                    left join '. $this->tableCustomer .' on '. $this->tableName .'.customerkey = '. $this->tableCustomer .'.pkey
            WHERE
                '. $this->tableWorkOrderCargoDetail .'.refkey = '. $this->tableName .'.pkey and
                '. $this->tableName .'.statuskey in (1,2)

        ';

        if (!empty($criteria)) {
            $sql .= $criteria ;
        }

        $result = $this->oDbCon->doQuery($sql);
        
        return $result;
    }
    
    function updateGPSRequestId($workOrderKey, $requestId){

        
          try{ 

            if (!$this->oDbCon->startTrans())
                throw new Exception($OBJ->errorMsg[100]);
             
             $sql = 'update '. $this->tableName.' 
                    set requestid = '.$this->oDbCon->paramString($requestId).'
                    where pkey = '.$this->oDbCon->paramString($workOrderKey);

            $this->oDbCon->execute($sql);
              
            $this->oDbCon->endTrans(); 

		}catch(Exception $e){
			$this->oDbCon->rollback();    

		}	
        
    }

    function searchDataCarForSalesOrder($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){ 
        
        $sql = '
            select 
                '. $this->tableName .'.pkey,
                '. $this->tableName .'.code,    
                '. $this->tableName .'.carkey,
                '. $this->tableCar .'.policenumber as value
            from
                '. $this->tableName .',
                '. $this->tableCar .' 
            where
                '. $this->tableName .'.carkey = '. $this->tableCar .'.pkey and
                '. $this->tableName .'.isoutsource = 0 and 
                '. $this->tableName .'.statuskey in (2,3)
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



    function generateCostForReport($pkey, $criteria = '', $order = '', $arrAdditionalCost = array())
    {
        $arrSQL = array();

        $sql = 'select 
                '.$this->tableCost .'.refkey as wokey,
	   			'.$this->tableItem .'.name,
	   			'.$this->tableItem .'.code as itemcode,
	   			'.$this->tableItem .'.reimburse,
                '.$this->tableCost .'.qty,
                '.$this->tableItem .'.pkey as costkey,
                '.$this->tableCost.'.amount,
                '.$this->tableName.'.carkey,
                '.$this->tableName.'.isoutsource
			from
			  	'.$this->tableName.',  
			  	'.$this->tableCost.',
			  	'.$this->tableItem.'
            where 
                '.$this->tableCost.'.costkey =  '.$this->tableItem .'.pkey and   
                '.$this->tableName.'.pkey =  '.$this->tableCost .'.refkey and   
                '.$this->tableName.'.pkey in ('. $this->oDbCon->paramString($pkey,',') .') and
                 '.$this->tableCost.'.amount > 0 and '.$this->tableItem .'.reimburse = 0
            ';

        if (!empty($criteria)) $sql .=  ' ' .$criteria;  
        array_push($arrSQL, $sql);

        $criteria = preg_replace('/\s*and\s+item\.pkey\s+in\s*\([^)]+\)\s*/i', ' ', $criteria);

        if (empty($arrAdditionalCost) || in_array(-1, $arrAdditionalCost)) {
            //DRIVER COMMISSION
            $sql = 'select 
                    '.$this->tableName .'.pkey as wokey,
                    \''.$this->lang['truckingFee'].'\' as name,
                    '.$this->tableName .'.code as itemcode,
                    \'0\' as reimburse,
                    \'1\' as qty,
                    \'-1\' as costkey,
                    '.$this->tableName.'.outsourcecost as amount,
                    '.$this->tableName.'.carkey,
                    '.$this->tableName.'.isoutsource
                from
                    '.$this->tableName.'
                where  
                    '.$this->tableName.'.pkey in ('. $this->oDbCon->paramString($pkey,',') .') and
                    '.$this->tableName.'.isoutsource = 1 and
                    '.$this->tableName.'.outsourcecost > 0
                ';

            if (!empty($criteria)) $sql .=  ' ' .$criteria;  
            array_push($arrSQL, $sql);
        }

        if (empty($arrAdditionalCost) || in_array(-2, $arrAdditionalCost)) {
            //DRIVER COMMISSION
            $sql = 'select 
                    '.$this->tableName .'.pkey as wokey,
                    \''.$this->lang['driverCommission'].'\' as name,
                    '.$this->tableName .'.code as itemcode,
                    \'0\' as reimburse,
                    \'1\' as qty,
                    \'-2\' as costkey,
                    '.$this->tableName.'.drivercommission as amount,
                    '.$this->tableName.'.carkey,
                    '.$this->tableName.'.isoutsource
                from
                    '.$this->tableName.'
                where  
                    '.$this->tableName.'.pkey in ('. $this->oDbCon->paramString($pkey,',') .') and
                    '.$this->tableName.'.isoutsource = 0 and
                    '.$this->tableName.'.drivercommission > 0
                ';

            if (!empty($criteria)) $sql .=  ' ' .$criteria;  
            array_push($arrSQL, $sql);
        }

        if (empty($arrAdditionalCost) || in_array(-3, $arrAdditionalCost)) {
            //CO DRIVER COMMISSION
            $sql = 'select 
                    '.$this->tableName .'.pkey as wokey,
                    \''.$this->lang['codriverCommission'].'\' as name,
                    '.$this->tableName .'.code as itemcode,
                    \'0\' as reimburse,
                    \'1\' as qty,
                    \'-3\' as costkey,
                    '.$this->tableName.'.codrivercommission as amount,
                    '.$this->tableName.'.carkey,
                    '.$this->tableName.'.isoutsource
                from
                    '.$this->tableName.'
                where 
                    '.$this->tableName.'.pkey in ('. $this->oDbCon->paramString($pkey,',') .') and
                    '.$this->tableName.'.isoutsource = 0 and
                    '.$this->tableName.'.codrivercommission > 0
                ';

            if (!empty($criteria)) $sql .=  ' ' .$criteria;  
            array_push($arrSQL, $sql);
        }

        $sql = implode ( ' UNION ALL ' , $arrSQL);
        
        if (!empty($order))  
            $sql .=  ' ' .$order; 
          
       //$this->setLog($sql,true);
       return $this->oDbCon->doQuery($sql);

    } 


    function getDataForPrintPointHistory($pkey)
    {
        $result = [];

        $gps = new GPS();
        $GPSConnection = new GPSConnection();
        $car = new Car();
        $employee = new Employee();

        $rsWO = $this->searchDataRow(array(
                                                $this->tableName.'.pkey',
                                                $this->tableName.'.code',
                                                $this->tableName.'.trdate',
                                                $this->tableName.'.refkey',
                                                $this->tableName.'.carkey',
                                                $this->tableName.'.driverkey',
                                                $this->tableName.'.requestid',
                                                $this->tableName.'.statuskey'
                                            ), ' and ' . $this->tableName.'.refkey = '.$this->oDbCon->paramString($pkey) .' and '. $this->tableName.'.statuskey in (2,3) ');

        if(empty($rsWO)) {
             return $result;
        }

        $rsWorkOrderCol = array_column($rsWO,null,'pkey');

        $rsCarCol = $car->searchDataRow(array($car->tableName.'.pkey',$car->tableName.'.gpskey',$car->tableName.'.policenumber' ),
                                             ' and ' . $car->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsWorkOrderCol,'carkey'),',').')'
                                             );
        $rsCarCol = array_column($rsCarCol,null,'pkey');

        $rsGPSCol = $gps->searchDataRow(array($gps->tableName.'.pkey',$gps->tableName.'.code',$gps->tableName.'.name'),
                                ' and '.$gps->tableName.'.pkey in ('.$this->oDbCon->paramString( array_column($rsCarCol,'gpskey') , ',').')');
 
        $rsGPSCol =  array_column($rsGPSCol,null,'pkey');

        $rsEmployeeCol = $employee->searchDataRow(array($employee->tableName.'.pkey',$employee->tableName.'.name'),
                                             ' and ' . $employee->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsWorkOrderCol,'driverkey'),',').')'
                                             );
        $rsEmployeeCol = array_column($rsEmployeeCol,null,'pkey');


        foreach($rsWO as $row) {

            if(empty($row['carkey']) || !isset($rsCarCol[$row['carkey']])) continue;
            $rsCar = $rsCarCol[$row['carkey']];

            
            if(empty($row['driverkey']) || !isset($rsEmployeeCol[$row['driverkey']])) continue;
            $rsEmployee = $rsEmployeeCol[$row['driverkey']]; 
            
            if(empty($rsCar['gpskey']) || !isset($rsGPSCol[$rsCar['gpskey']])) continue;
            $rsGPS = $rsGPSCol[$rsCar['gpskey']];

            if(empty($rsGPS['code']) || empty($row['requestid'])) continue;
            
            $gpsObj = $GPSConnection->getGPSObj($rsGPS['code']);

            if (!$gpsObj) continue;

            $rs = $gpsObj->getFleetTaskInstancePoint($row['requestid']);

            $rs = $rs ?? [];

            $result[] = [
                'workorderkey' => $row['pkey'],
                'workordercode'=>$row['code'],
                'workorderdate'=>$row['trdate'],
                'drivername'=> $rsEmployee['name'],
                'policenumber'=> $rsCar['policenumber'],
                'response' => $rs
            ];
            

        }


        return $result;

    }

    
    function updateJobProgressValidation($arrData){
        $jobProgress = new JobProgress();

        $pkey = $arrData['woKey'];
        $jobProgressWODetailKey = $arrData['jobProgressDetailKey'];
        $jobProgressHeaderKey = $arrData['jobProgressHeaderKey'];
        $jobProgressKey = $arrData['jobProgressKey'];

        $rsWO = $this->getDataRowById($pkey);
        if (empty($rsWO)) {
            
        // cek SPK ada atau tdk 
        throw new Exception(json_encode([
                'valid' => false,
                'message' => $this->lang['truckingServiceWorkOrder'] . '. ' . $this->lang['noDataFound']
            ]));
        } else if (!in_array($rsWO[0]['statuskey'], [2])) {
            // cek status SPK
            throw new Exception(json_encode([
                'valid' => false,
                'message' => $rsWO[0]['code'] . '. ' . $this->errorMsg[204]
            ]));
        }

        // cek progress detail
        $rsJobProgressDetail = $this->getJobProgressDetail($pkey, 'refkey');

        // kalo blm ad detail, insert
        if (empty($rsJobProgressDetail)) {
            return [
                'action' => INSERT_DATA,
                'rsWO' => $rsWO,
                'rsJobProgressDetail' => []
            ];
        } else {
            // utk cek master job progress ada atau gk
            //reindex by jobprogress
            $rsJobProgressDetailCols = $this->reindexDetailCollections($rsJobProgressDetail, 'jobprogresskey');

            if(!isset($rsJobProgressDetailCols[$jobProgressKey])) {
                $rsJobProgress = $jobProgress->getDetailByColumn('pkey', $jobProgressKey);
                throw new Exception(json_encode([
                    'valid' => false,
                    'message' =>  $rsWO[0]['code'] . '.  ' . $this->lang['jobProgress'] . '  '. $rsJobProgress[0]['name'].'. ' . $this->errorMsg[213]
                ]));
            }

            $rsJobProgressDetail = $rsJobProgressDetailCols[$jobProgressKey]; 

            // utk cek progress sudah selesai atau blm
            if($rsJobProgressDetail[0]['iscompleted'] == 1) {
                throw new Exception(json_encode([
                    'valid' => false,
                    'message' =>  $rsWO[0]['code'] . '.   ' . $this->errorMsg[212]. ' - '. $this->lang['jobProgress'] . ' ' . $rsJobProgressDetail[0]['jobprogressname'] . '. ' . $this->errorMsg['truckingServiceWorkOrder'][19] 
                ]));
            }

            // utk cek progress melompati stepnya tdk
            $currentNumber = $rsJobProgressDetail[0]['number'];

            $sqlLast = '
                    SELECT 
                        max(number) as lastcompleted
                    FROM 
                        ' . $this->tableWorkOrderJobProgressDetail . '
                    WHERE 
                        ' . $this->tableWorkOrderJobProgressDetail . '.refkey = ' . $this->oDbCon->paramString($pkey) . '
                        AND ' . $this->tableWorkOrderJobProgressDetail . '.iscompleted = 1 
                    ';

        
            $rsLastCompleted = $this->oDbCon->doQuery($sqlLast);
            $lastCompletedNumber = $rsLastCompleted[0]['lastcompleted'] ?? 0;

            if ($currentNumber <= $lastCompletedNumber) {
                throw new Exception(json_encode([
                        'valid' => false,
                        'message' => $rsWO[0]['code'] . '. ' .
                                $rsJobProgressDetail[0]['jobprogressname'] .
                                ' - ' . $this->errorMsg['truckingServiceWorkOrder'][21]
                    ]));
            }else if ($currentNumber > $lastCompletedNumber + 1) {
                throw new Exception(json_encode([
                        'valid' => false,
                        'message' => $rsWO[0]['code'] . '. ' . $rsJobProgressDetail[0]['jobprogressname'] . ' - ' . $this->errorMsg['truckingServiceWorkOrder'][20]
                    ]));

            }

             return [
                'action' => UPDATE_DATA,
                'rsWO' => $rsWO,
                'rsJobProgressDetail' => $rsJobProgressDetail
            ];

        }

    }

    function updateJobProgressWorkOrder($arrData){
        
        // dari API
        
        $jobProgress = new JobProgress();

        $result = [];
        
        if (empty($arrData)) {
            
            return [
                [
                    'valid' => false,
                    'message' => $this->lang['truckingServiceWorkOrder'] . '. ' . $this->lang['noDataFound']
                ]
            ];
        }

        try {
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);  
            
                //validation
                $validation = $this->updateJobProgressValidation($arrData);
                $actionType = $validation['action']; // INSERT_DATA atau UPDATE_DATA
                $rsWO = $validation['rsWO'];
                $rsJobProgressDetail = $validation['rsJobProgressDetail'];
            
                $pkey = $arrData['woKey'];
                $jobProgressWODetailKey = $arrData['jobProgressDetailKey'];
                $jobProgressHeaderKey = $arrData['jobProgressHeaderKey'];
                $jobProgressKey = $arrData['jobProgressKey'];
                $isCompleted = (isset($arrData['isCompleted']) ? $arrData['isCompleted'] : 1);
            
                $latitude = $arrData['latitude'] ?? 0;
                $longitude = $arrData['longitude'] ?? 0;
                $fileName = $arrData['fileName'] ?? '';
                
                $completedDate = $arrData['completedDate'];
                //$completedDateTime = $completedDate; 
                
                if($actionType == INSERT_DATA) {
                    // ini kayanya udah gk kepake, karena diawal terbentuk SPK  kesave dulu progresnya
                    $rsJobProgressList = $jobProgress->getJobProgressByCategory($rsWO[0]['categorykey']) ?? [];

                    if(!empty($rsJobProgressList)){
                            //kalau belum ada di detail maka insert ke detail driver progress
                            foreach ($rsJobProgressList as $row) {

                                $isCompleted = ($row['number'] == 1) ? 1 : 0; //row number 1 otomatis complete, ketika pertama kali insert jika belum ada di detail
                                //$completedDate = $completedDateTime;

                                $sql = '
                                    INSERT INTO 
                                    ' . $this->tableWorkOrderJobProgressDetail . ' (
                                        refkey,
                                        number,
                                        jobprogresskey,
                                        jobprogressheaderkey,
                                        completeddate,
                                        iscompleted
                                    ) VALUES (
                                        ' . $this->oDbCon->paramString($pkey) . ',
                                        ' . $this->oDbCon->paramString($row['number']) . ',
                                        ' . $this->oDbCon->paramString($row['pkey']) . ',
                                        ' . $this->oDbCon->paramString($row['refkey']) . ',
                                        ' . $this->oDbCon->paramDate($completedDate,' / ') . ',
                                        ' . $this->oDbCon->paramString($isCompleted) . '
                                    )
                                ';
                                $this->oDbCon->execute($sql);
                            }

                            $this->setTransactionLog(UPDATE_DATA, $rsWO[0]['pkey']);
                            array_push($result, ['valid' => true,'message' => $rsWO[0]['code'] .' - '.$this->lang['dataHasBeenSuccessfullyUpdated']]); 
                     }
                      

                } else if ($actionType == UPDATE_DATA) {

                    $field = 'pkey';
                    if(empty($jobProgressWODetailKey)) {
                        $field = 'refkey';
                        $jobProgressWODetailKey = $rsWO[0]['pkey'];
                    }
                    
                    $sql = '
                            UPDATE
                                '.$this->tableWorkOrderJobProgressDetail.'
                            SET
                                iscompleted = '.$this->oDbCon->paramString($isCompleted).',
                                completeddate = '.$this->oDbCon->paramDate($completedDate,' / ').',
                                latitude = '.$this->oDbCon->paramString($latitude).',
                                longitude = '.$this->oDbCon->paramString($longitude).',
                                filename = '.$this->oDbCon->paramString($fileName).'
                            WHERE
                                '.$field.' = '.$this->oDbCon->paramString($jobProgressWODetailKey).' and
                                jobprogresskey = '.$this->oDbCon->paramString($jobProgressKey).'
                                
                        ';
                        
                        $rs = $this->oDbCon->execute($sql);

                        if($rs['result']) {
                            $this->setTransactionLog(UPDATE_DATA,$rsWO[0]['pkey']);
                            array_push($result, ['valid' => true,'message' => $rsWO[0]['code'] .'. '.$this->lang['jobProgress'] .' : '.$rsJobProgressDetail[0]['jobprogressname'].'.  '.$this->lang['dataHasBeenSuccessfullyUpdated']]); 
                        } else {
                            array_push($result, ['valid' => false,'message' => $rsWO[0]['code'] .'. '.$this->lang['jobProgress'] .' : '.$rsJobProgressDetail[0]['jobprogressname'].'.  '.$this->errorMsg[212]]); 
                        }
            }
		 
            $this->updateJobProgresStatus($rsWO[0]['pkey']);
            
            $this->oDbCon->endTrans();

        } catch (Exception $e) {
            $this->oDbCon->rollback();
            $err = json_decode($e->getMessage(), true);
            if (json_last_error() === JSON_ERROR_NONE)
                $result[] = $err;
            else
                $result[] = ['valid' => false, 'message' => $e->getMessage()];
        }

        return $result;
    }
    function updateJobProgresStatus($pkey)
    {
        $rsJobProgress = $this->getJobProgressDetail($pkey);

        if(empty($rsJobProgress)) return;

        $totalJobProgress = count($rsJobProgress);

        $sql = '
            select
                coalesce(count(*),0) as totalcompleted
            from
                '.$this->tableWorkOrderJobProgressDetail.'
            where
                '.$this->tableWorkOrderJobProgressDetail.'.iscompleted = 1 and
                '.$this->tableWorkOrderJobProgressDetail.'.refkey = '.$this->oDbCon->paramString($pkey).'
        ';

        $rs = $this->oDbCon->doQuery($sql);
        $totalCompleted = $rs[0]['totalcompleted'];
        
        $driverProgressStatus = ($totalCompleted >= $totalJobProgress) ? 1 : 0;

        $sql = '
            update
                '.$this->tableName.'
            set
                '.$this->tableName.'.driverprogress = '.$this->oDbCon->paramString($driverProgressStatus).'
            where
                '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey).'
        ';
        $this->oDbCon->execute($sql);
        

    }
}
?>
