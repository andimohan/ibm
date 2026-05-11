<?php

class HospitalJobOrder extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'hospital_job_order_header';
        $this->tableNameDetail = 'hospital_job_order_detail';
        $this->tableNameDetailStatus = 'hospital_job_order_detail_status';
        $this->tableSellingCost = 'hospital_job_order_selling_cost';
        $this->tableHeaderCost = 'hospital_job_order_header_cost';
        $this->tableCategory = 'hospital_job_order_category';
        $this->tableCost = 'trucking_service_order_cost';
        $this->tableWorkOrder = 'hospital_work_order';
        $this->tableWorkOrderCarDetail = 'hospital_work_order_car';
        $this->tableWorkOrderCost = 'hospital_work_order_cost';
        $this->tableWarehouse = 'warehouse';
        $this->tableInitialDiagnoseDetail = 'hospital_job_order_initial_diagnose_detail';
        $this->tableInitialDiagnose = 'diagnose';
        $this->tableCustomer = 'customer';
        $this->tableItem = 'item';
        $this->tableCustomerCategory = 'customer_category';
        $this->tableSupplier = 'supplier';
        $this->tableLocation = 'location';
        $this->tableEmployee = 'employee';
        $this->tableStatus = 'hospital_job_order_status';
        $this->tableDetailStatus = 'hospital_job_order_detail_status';
        $this->tableHistory = 'history';
        $this->tableFile = 'hospital_job_order_file';
        $this->tableTruckingCostCashOut = 'hospital_cost_cash_out_header';
        $this->tableAP = 'ap';
        $this->tableAPEmployee = 'ap_employee_commission';
        $this->tableHospitalServiceOrderInvoiceHeader = 'hospital_service_order_invoice_header';
        $this->tableHospitalServiceOrderInvoiceDetail = 'hospital_service_order_invoice_detail';
        $this->tablePartialInvoice = 'hospital_job_order_header_partial_invoice';
        $this->tableRef = 'table_ref';
        $this->tableCustomerInsurancePolicy = 'customer_insurance_policy';
        $this->tableCar = 'car';
        $this->tableContact = 'contact_person';	  

        $this->uploadFileFolder = 'hospital-job-order/';
        $this->isTransaction = true;

        $this->allowedStatusForEdit = array(1, 2, 3, 4);

        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail, $this->tableHeaderCost, $this->tableSellingCost);
        // $this->tableCost gk boleh dimasukin karena br terbentuk pas konfirmasi

        $this->securityObject = 'HospitalJobOrder';
        $this->sellingPriceSecurityObject = 'SellingPrice';
        $this->overwriteContractSecurityObject = 'overwriteContract';


        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['numberkey'] = array('numberkey');
        $this->arrDataDetail['itemkey'] = array('hidItemKey', array('mandatory' => true));
        $this->arrDataDetail['qtyinbaseunit'] = array('qty', array('datatype' => 'number', 'mandatory' => true));
        $this->arrDataDetail['trdate'] = array('trShipmentDate', 'datetime');
        $this->arrDataDetail['priceinunit'] = array('price', 'number');
        $this->arrDataDetail['contractpriceinunit'] = array('contractPrice', 'number');
        $this->arrDataDetail['total'] = array('totalDetails', 'number');
        $this->arrDataDetail['trdesc'] = array('detailNotes');
        $this->arrDataDetail['isgroup'] = array('chkIsGroup');
        // $this->arrDataDetail['requestid'] = array('detailRequestId');

        $this->arrHeaderCost = array();
        $this->arrHeaderCost['pkey'] = array('hidAdditionalKey');
        $this->arrHeaderCost['refkey'] = array('pkey', 'ref');
        $this->arrHeaderCost['costkey'] = array('hidItemKeyHeaderCost', array('mandatory' => true));
        $this->arrHeaderCost['qty'] = array('qtyHeaderCost', array('datatype' => 'number', 'mandatory' => true));
        $this->arrHeaderCost['requestamount'] = array('requestPriceHeaderCost', 'number');
        $this->arrHeaderCost['amount'] = array('priceHeaderCost', 'number');
        $this->arrHeaderCost['subtotal'] = array('subtotalHeaderCost', 'number');
        $this->arrHeaderCost['employeekey'] = array('hidDetailEmployeeKey');
        $this->arrHeaderCost['description'] = array('detailDesc');
        $this->arrHeaderCost['requestid'] = array('headerCostRequestId');


        $this->arrSellingCost = array();
        $this->arrSellingCost['pkey'] = array('hidDetailCostKey');
        $this->arrSellingCost['refkey'] = array('pkey', 'ref');
        $this->arrSellingCost['costkey'] = array('hidItemKeyCost', array('mandatory' => true));
        $this->arrSellingCost['qty'] = array('qtyCost', array('datatype' => 'number', 'mandatory' => true));
        $this->arrSellingCost['price'] = array('priceCost', 'number');
        $this->arrSellingCost['subtotal'] = array('subtotalCost', 'number');
        $this->arrSellingCost['requestid'] = array('sellingCostRequestId');

        $this->arrInitialDiagnoseDetail = array(); 
        $this->arrInitialDiagnoseDetail['pkey'] = array('hidInitialDiagnoseDetailKey');
        $this->arrInitialDiagnoseDetail['refkey'] = array('pkey','ref');
        $this->arrInitialDiagnoseDetail['initialdiagnosekey'] = array('hidInitialDiagnoseKey');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrHeaderCost, 'tableName' => $this->tableHeaderCost));
        array_push($arrDetails, array('dataset' => $this->arrSellingCost, 'tableName' => $this->tableSellingCost));
        array_push($arrDetails, array('dataset' => $this->arrInitialDiagnoseDetail, 'tableName' => $this->tableInitialDiagnoseDetail));   

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['codectr'] = array('codectr');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['patientkey'] = array('hidPatientKey');
        $this->arrData['contractkey'] = array('hidContractKey');
        $this->arrData['caselocationkey'] = array('hidLocationKey');
        $this->arrData['address'] = array('address');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['subtotal'] = array('subtotal', 'number');
        $this->arrData['grandtotal'] = array('grandtotal', 'number');
        $this->arrData['routefrom'] = array('routeFrom');
        $this->arrData['routeto'] = array('routeTo');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['tarifflastmodifiedon'] = array('hidContractLastModifiedOn');
        $this->arrData['totalheadercost'] = array('totalHeaderCost', 'number');
        $this->arrData['totalsellingcost'] = array('totalSellingCost', 'number');
        $this->arrData['autoinvoice'] = array('autoInvoice');
        // $this->arrData['initialdiagnosekey'] = array('hidInitialDiagnoseKey');
        $this->arrData['phonecase'] = array('phoneCase');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['trdesccase'] = array('trDescCase');
        $this->arrData['age'] = array('age');
        $this->arrData['callername'] = array('callerName');
        $this->arrData['relationtoinsured'] = array('relationToInsured');
        $this->arrData['callermobile'] = array('callerMobile');
        $this->arrData['calleremail'] = array('callerEmail');
        $this->arrData['isapi'] = array('_mnv-api');


        $this->arrLinkedTable = array();
        $defaultFieldName = 'refkey';
        array_push($this->arrLinkedTable, array('table' => 'hospital_work_order', 'field' => $defaultFieldName));

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 80, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'companyname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'patient', 'title' => 'patient', 'dbfield' => 'patientname', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total', 'title' => 'total', 'dbfield' => 'grandtotal', 'default' => true, 'width' => 80, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 90));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'width' => 120));
         array_push($this->arrDataListAvailableColumn, array('code' => 'totalcost', 'title' => 'totalCost', 'dbfield' => 'totalcost', 'width' => 80, 'align' => 'right', 'format' => 'number'));
        // array_push($this->arrDataListAvailableColumn, array('code' => 'description', 'title' => 'note', 'dbfield' => 'trdesc',  'width' => 200));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array('Gudang', $this->tableWarehouse . '.name'));
        array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));

        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/hospitalJobOrder'));
        array_push($this->printMenu, array('code' => 'printComplete', 'name' => $this->lang['printSummary'],  'icon' => 'print', 'url' => 'print/hospitalJobOrderComplete'));
        array_push($this->printMenu, array('code' => 'printCashOut', 'name' => $this->lang['printCashOutRequest'],  'icon' => 'print', 'url' => 'print/hospitalJobOrderCostCashOut'));

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
            'HospitalCostCashOut.class.php',
            'HospitalSellingRate.class.php',
            'TruckingServiceOrderCategory.class.php',
            'TruckingServiceOrderInvoice.class.php',
            'HospitalServiceOrderInvoice.class.php',
            'HospitalWorkOrder.class.php',
            'Vessel.class.php',
            'Warehouse.class.php',
            'PaymentMethod.class.php',
            'TermOfPayment.class.php',
            'Diagnose.class.php',
            'CustomerInsurancePolicy.class.php',
            'City.class.php',
            'COALink.class.php'
        ));
        $this->overwriteConfig();
    }


    function getQuery()
    {

        $sql = '
			SELECT ' . $this->tableName . '.* ,
               (' . $this->tableName . '.totalheadercost + ' . $this->tableName . '.totalworkordercost) as totalcost,
               ' . $this->tableName . '.totalsharedprofit,
               (' . $this->tableName . '.grandtotal - (' . $this->tableName . '.totalheadercost + ' . $this->tableName . '.totalworkordercost + ' . $this->tableName . '.totalsharedprofit)) as grossprofit,
			   ' . $this->tableCustomer . '.name as companyname, 
			   ' . $this->tableCustomer . '.code as customercode, 
			   ' . $this->tableCustomer . '.categorykey, 
			   ' . $this->tableCustomerCategory . '.name as categoryname, 
			   ' . $this->tableCustomerInsurancePolicy . '.name as patientname, 
			   ' . $this->tableStatus . '.status as statusname ,
			   ' . $this->tableStatus . '.textcolor as statuscolor ,
               ' . $this->tableWarehouse . '.name as warehousename, 
               ' . $this->tableWarehouse . '.code as warehousecode,
               '.$this->tableLocation .'.name as locationname
			FROM 
                ' . $this->tableStatus . ', 
                ' . $this->tableWarehouse . ',
                ' . $this->tableName . '
                    left join '.$this->tableLocation.' on '.$this->tableName . '.caselocationkey = '.$this->tableLocation.'.pkey 
                    left join '.$this->tableCustomer.' on '.$this->tableName . '.customerkey = '.$this->tableCustomer.'.pkey 
                    left join '.$this->tableCustomerCategory.' on '.$this->tableCustomer . '.categorykey = '.$this->tableCustomerCategory.'.pkey 
                    left join '.$this->tableCustomerInsurancePolicy.' on '.$this->tableName . '.patientkey = '.$this->tableCustomerInsurancePolicy.'.pkey 
			WHERE 
					 ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and 
					 ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey 
 		' . $this->criteria;

        $sql .=  $this->getWarehouseCriteria();

        return $sql;
    }

    function getDetailDiagnose($pkey,$criteria=''){
        
        $sql = 'select
               '.$this->tableInitialDiagnoseDetail .'.*,
               '.$this->tableInitialDiagnose .'.name as initialdiagnose
          from
              '. $this->tableInitialDiagnoseDetail .' 
              left join ' . $this->tableInitialDiagnose . ' on ' . $this->tableInitialDiagnoseDetail . '.initialdiagnosekey = ' . $this->tableInitialDiagnose . '.pkey 
          where  
              '.$this->tableInitialDiagnoseDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
     
            
    $sql .= $criteria;
    return $this->oDbCon->doQuery($sql);
}

    function updateTotalSharedProfit($sokey)
    {
        $totalSharedProfit = 0;
        $rsCommission = $this->getPartnersVehicleInformation($sokey);

        for ($i = 0; $i < count($rsCommission); $i++)
            $totalSharedProfit += $rsCommission[$i]['amount'];

        $sql = ' update ' . $this->tableName . ' set totalsharedprofit = ' . $this->oDbCon->paramString($totalSharedProfit) . '
                 where ' . $this->tableName . '.pkey = ' . $this->oDbCon->paramString($sokey);

        $this->oDbCon->execute($sql);
    }

   /*  function generateCostReport($criteria = '', $order = '')
     {
         // gk bisa join langsung dengan Job Order atau SPK, karean tergantung tabletype

         $arrSQL = array();


         // JO COST
         $rsCashOutKey =  $this->getTableKeyAndObj($this->tableName);
         $sql =  '
	 		SELECT ' . $this->tableName . '.pkey, 
                    ' . $this->tableName . '.code, 
                    ' . $this->tableRef . '.code as refcode, 
                    ' . $this->tableRef . '.donumber, 
                    ' . $this->tableName . '.trdate,  
                    (' . $this->tableCost . '.qty *  ' . $this->tableCost . '.requestamount)  as requestamount,  
                    (' . $this->tableCost . '.qty *  ' . $this->tableCost . '.amount)  as amount,  
                    (' . $this->tableCost . '.qty *  ( ' . $this->tableCost . '.requestamount  -  ' . $this->tableCost . '.amount) )  as balance,  
                    ' . $this->tableItem . '.pkey as costkey, 
                    ' . $this->tableItem . '.name as costname, 
                    ' . $this->tableStatus . '.status as statusname , 
                    ' . $this->tableCategory . '.name as categoryname,
                    ' . $this->tableWarehouse . '.name as warehousename , 
                    ' . $this->tableEmployee . '.name as recipientname,
                    0 as isoutsource,
                    ' . $this->tableTruckingCostCashOut . '.code as cashoutcode,
                    ' . $this->tableCustomer . '.name as customername,
                    ' . $this->tableConsignee . '.name as consigneename,
                    CONCAT_WS(\'\',' . $this->tableRef . '.routefrom,\'-\',' . $this->tableRef . '.routeto)  as route,
                    ' . $this->tableLocation . '.name as locationname,
                    \'\' as carregistrationnumber
	 		FROM 
                 ' . $this->tableName . ', 
                 ' . $this->tableName . ' as  ' . $this->tableRef . ' 
                     left join ' . $this->tableCustomer . ' on ' . $this->tableRef . '.customerkey = ' . $this->tableCustomer . '.pkey
                     left join ' . $this->tableConsignee . ' on ' . $this->tableRef . '.consigneekey = ' . $this->tableConsignee . '.pkey
                     left join ' . $this->tableLocation . ' on ' . $this->tableRef . '.stuffinglocationkey = ' . $this->tableLocation . '.pkey
                     left join ' . $this->tableCategory . ' on ' . $this->tableRef . '.categorykey = ' . $this->tableCategory . '.pkey, 
                 ' . $this->tableHeaderCost . ' as  ' . $this->tableCost . ' 
                     left join  ' . $this->tableTruckingCostCashOut . ' on 
                             ' . $this->tableCost . '.refcashoutkey = ' . $this->tableTruckingCostCashOut . '.pkey and
                             ' . $this->tableTruckingCostCashOut . '.reftabletype = ' . $this->oDbCon->paramString($rsCashOutKey['key']) . ' 
                     left join  ' . $this->tableEmployee . ' on ' . $this->tableTruckingCostCashOut . '.employeekey = ' . $this->tableEmployee . '.pkey,
                 ' . $this->tableItem . ',  
                 ' . $this->tableStatus . ',   
                 ' . $this->tableWarehouse . '  
	 		WHERE     
                 ' . $this->tableName . '.pkey = ' . $this->tableCost . '.refkey and 
                 ' . $this->tableName . '.pkey = ' . $this->tableRef . '.pkey and 
                 ' . $this->tableCost . '.costkey = ' . $this->tableItem . '.pkey and  
                 ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and 
                 ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and
                 ' . $this->tableName . '.statuskey not in(1,7)
 	 	';


         if (!empty($criteria)) $sql .=  ' ' . $criteria;
         array_push($arrSQL, $sql);


         // SPK COST    
         $rsCashOutKey =  $this->getTableKeyAndObj($this->tableWorkOrder);
         $sql =  '
	 		SELECT ' . $this->tableName . '.pkey, 
                    ' . $this->tableName . '.code, 
                    ' . $this->tableRef . '.code as refcode,
                    ' . $this->tableRef . '.donumber,  
                    ' . $this->tableName . '.trdate,   
                    (' . $this->tableCost . '.qty * ' . $this->tableCost . '.requestamount) as requestamount,  
                    (' . $this->tableCost . '.qty * ' . $this->tableCost . '.amount) as amount,  
                    (' . $this->tableCost . '.qty * ' . $this->tableCost . '.requestamount) - (' . $this->tableCost . '.qty * ' . $this->tableCost . '.amount) as balance,  
                    ' . $this->tableItem . '.pkey as costkey, 
                    ' . $this->tableItem . '.name as costname, 
                    ' . $this->tableStatus . '.status as statusname , 
                    ' . $this->tableCategory . '.name as categoryname,
                    ' . $this->tableWarehouse . '.name as warehousename , 
                    CONCAT_WS(\'\',' . $this->tableEmployee . '.name,' . $this->tableSupplier . '.name)  as recipientname,
                    ' . $this->tableName . '.isoutsource,
                    CONCAT_WS(\'\',' . $this->tableTruckingCostCashOut . '.code,' . $this->tableAP . '.code)  as cashoutcode,
                    ' . $this->tableCustomer . '.name as customername,
                    ' . $this->tableConsignee . '.name as consigneename,
                    CONCAT_WS(\'\',' . $this->tableRef . '.routefrom,\'-\',' . $this->tableRef . '.routeto) as route,
                    ' . $this->tableLocation . '.name as locationname,
                    ' . $this->tableCar . '.policenumber as carregistrationnumber
	 		FROM 
                 ' . $this->tableWorkOrder . ' as ' . $this->tableName . '
                     left join ' . $this->tableCar . '  on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey,
                 ' . $this->tableStatus . ',  
                 ' . $this->tableItem . ', 
                 ' . $this->tableWorkOrderCost . ' as  ' . $this->tableCost . '
                     left join ' . $this->tableSupplier . ' on ' . $this->tableCost . '.supplierkey = ' . $this->tableSupplier . '.pkey
                     left join ' . $this->tableAP . ' on ' . $this->tableCost . '.refcashoutkey = ' . $this->tableAP . '.pkey and  ' . $this->tableCost . '.supplierkey <> 0
                     left join ' . $this->tableTruckingCostCashOut . ' on 
                             ' . $this->tableCost . '.refcashoutkey = ' . $this->tableTruckingCostCashOut . '.pkey and  ' . $this->tableCost . '.employeekey <> 0 and
                             ' . $this->tableTruckingCostCashOut . '.reftabletype = ' . $this->oDbCon->paramString($rsCashOutKey['key']) . ' 
                     left join ' . $this->tableEmployee . ' on ' . $this->tableTruckingCostCashOut . '.employeekey = ' . $this->tableEmployee . '.pkey,
                 ' . $this->tableName . ' as  ' . $this->tableRef . '
                     left join ' . $this->tableCustomer . ' on ' . $this->tableRef . '.customerkey = ' . $this->tableCustomer . '.pkey
                     left join ' . $this->tableConsignee . ' on ' . $this->tableRef . '.consigneekey = ' . $this->tableConsignee . '.pkey
                     left join ' . $this->tableLocation . ' on ' . $this->tableRef . '.stuffinglocationkey = ' . $this->tableLocation . '.pkey
                     left join ' . $this->tableCategory . ' on ' . $this->tableRef . '.categorykey = ' . $this->tableCategory . '.pkey,
                 ' . $this->tableWarehouse . '
	 		WHERE     
                 ' . $this->tableName . '.refkey = ' . $this->tableRef . '.pkey and 
                 ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and 
                 ' . $this->tableCost . '.refkey = ' . $this->tableName . '.pkey and 
                 ' . $this->tableCost . '.costkey = ' . $this->tableItem . '.pkey and  
                 ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and 
                 ' . $this->tableName . '.statuskey not in(1,4)
 	 	';


         if (!empty($criteria)) $sql .=  ' ' . $criteria;
         array_push($arrSQL, $sql);

         // SPK OUTSOURCING COST
         $sql =  '
	 		SELECT ' . $this->tableName . '.pkey, 
                    ' . $this->tableName . '.code, 
                    ' . $this->tableRef . '.code as refcode, 
                    ' . $this->tableRef . '.donumber, 
                    ' . $this->tableName . '.trdate,  
                    ' . $this->tableName . '.outsourcecost as requestamount,  
                    ' . $this->tableName . '.outsourcecost as amount,  
                    0 as balance,  
                    \'0\' as costkey, 
                    ' . $this->tableItem . '.name as costname, 
                    ' . $this->tableStatus . '.status as statusname , 
                    ' . $this->tableCategory . '.name as categoryname,
                    ' . $this->tableWarehouse . '.name as warehousename , 
                    ' . $this->tableSupplier . '.name  as recipientname ,
                    ' . $this->tableName . '.isoutsource,
                    ' . $this->tableAP . '.code as cashoutcode,
                    ' . $this->tableCustomer . '.name as customername,
                    ' . $this->tableConsignee . '.name as consigneename,
                    CONCAT_WS(\'\',' . $this->tableRef . '.routefrom,\'-\',' . $this->tableRef . '.routeto)  as route,
                    ' . $this->tableLocation . '.name as locationname,
                    ' . $this->tableCar . '.policenumber as carregistrationnumber
	 		FROM 
                 ' . $this->tableWorkOrder . ' as ' . $this->tableName . ' 
                     left join  ' . $this->tableAP . ' on   ' . $this->tableName . '.refcashoutkey = ' . $this->tableAP . '.pkey
                     left join ' . $this->tableCar . '  on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey,
                 ' . $this->tableName . ' as ' . $this->tableRef . '
                     left join ' . $this->tableCustomer . ' on ' . $this->tableRef . '.customerkey = ' . $this->tableCustomer . '.pkey
                     left join ' . $this->tableConsignee . ' on ' . $this->tableRef . '.consigneekey = ' . $this->tableConsignee . '.pkey
                     left join ' . $this->tableLocation . ' on ' . $this->tableRef . '.stuffinglocationkey = ' . $this->tableLocation . '.pkey
                     left join ' . $this->tableCategory . ' on ' . $this->tableRef . '.categorykey = ' . $this->tableCategory . '.pkey,
                 ' . $this->tableStatus . ',  
                 ' . $this->tableSupplier . ',
                 ' . $this->tableWarehouse . ',
                 ( select 0 as pkey, \'' . $this->lang['truckingFee'] . '\' as name ) ' . $this->tableItem . ' 
	 		WHERE     
                 ' . $this->tableName . '.refkey = ' . $this->tableRef . '.pkey and 
                 ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and 
                 ' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey and 
                 ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and   
                 ' . $this->tableName . '.isoutsource = 1 and
                 ' . $this->tableName . '.statuskey not in(1,4)
 	 	';

         if (!empty($criteria)) $sql .=  ' ' . $criteria;
         array_push($arrSQL, $sql);


         // KOMISI DRIVER DAN  CO DRIVER
         $sql =  '
	 		SELECT ' . $this->tableName . '.pkey, 
                    ' . $this->tableName . '.code, 
                    ' . $this->tableRef . '.code as refcode, 
                    ' . $this->tableRef . '.donumber, 
                    ' . $this->tableName . '.trdate,  
                    ' . $this->tableName . '.drivercommission as requestamount,  
                    ' . $this->tableName . '.drivercommission as amount,  
                    0 as balance,  
                    \'0\' as costkey, 
                    ' . $this->tableItem . '.name as costname,  
                    ' . $this->tableStatus . '.status as statusname , 
                    ' . $this->tableCategory . '.name as categoryname,
                    ' . $this->tableWarehouse . '.name as warehousename , 
                    ' . $this->tableEmployee . '.name  as recipientname ,
                    ' . $this->tableName . '.isoutsource,
                    ' . $this->tableAPEmployee . '.code as cashoutcode,
                    ' . $this->tableCustomer . '.name as customername,
                    ' . $this->tableConsignee . '.name as consigneename,
                    CONCAT_WS(\'\',' . $this->tableRef . '.routefrom,\'-\',' . $this->tableRef . '.routeto)  as route,
                    ' . $this->tableLocation . '.name as locationname,
                    ' . $this->tableCar . '.policenumber as carregistrationnumber
	 		FROM 
                 ' . $this->tableWorkOrder . ' as ' . $this->tableName . ' 
                     left join  ' . $this->tableAPEmployee . ' on   ' . $this->tableName . '.refcashoutdriverkey = ' . $this->tableAPEmployee . '.pkey
                     left join ' . $this->tableCar . '  on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey,
                 ' . $this->tableName . ' as ' . $this->tableRef . '
                     left join ' . $this->tableCustomer . ' on ' . $this->tableRef . '.customerkey = ' . $this->tableCustomer . '.pkey
                     left join ' . $this->tableConsignee . ' on ' . $this->tableRef . '.consigneekey = ' . $this->tableConsignee . '.pkey
                     left join ' . $this->tableLocation . ' on ' . $this->tableRef . '.stuffinglocationkey = ' . $this->tableLocation . '.pkey
                     left join ' . $this->tableCategory . ' on ' . $this->tableRef . '.categorykey = ' . $this->tableCategory . '.pkey,
                 ' . $this->tableStatus . ',  
                 ' . $this->tableEmployee . ',
                 ' . $this->tableWarehouse . ' ,
                 ( select 0 as pkey, \'' . $this->lang['driverCommission'] . '\' as name ) ' . $this->tableItem . ' 
	 		WHERE     
                 ' . $this->tableName . '.refkey = ' . $this->tableRef . '.pkey and 
                 ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and 
                 ' . $this->tableName . '.driverkey = ' . $this->tableEmployee . '.pkey and 
                 ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and  
                 ' . $this->tableName . '.isoutsource = 0 and
                 ' . $this->tableName . '.drivercommission > 0 and
                 ' . $this->tableName . '.statuskey not in(1,4)
 	 	';

         if (!empty($criteria)) $sql .=  ' ' . $criteria;
         array_push($arrSQL, $sql);

         // KOMISI CO DRIVER
         $sql =  '
	 		SELECT ' . $this->tableName . '.pkey, 
                    ' . $this->tableName . '.code, 
                    ' . $this->tableRef . '.code as refcode, 
                    ' . $this->tableRef . '.donumber, 
                    ' . $this->tableName . '.trdate,  
                    ' . $this->tableName . '.codrivercommission as requestamount,  
                    ' . $this->tableName . '.codrivercommission as amount,  
                    0 as balance,  
                    \'0\' as costkey, 
                    ' . $this->tableItem . '.name as costname,   
                    ' . $this->tableStatus . '.status as statusname , 
                    ' . $this->tableCategory . '.name as categoryname,
                    ' . $this->tableWarehouse . '.name as warehousename , 
                    ' . $this->tableEmployee . '.name  as recipientname ,
                    ' . $this->tableName . '.isoutsource,
                    ' . $this->tableAPEmployee . '.code as cashoutcode,
                    ' . $this->tableCustomer . '.name as customername,
                    ' . $this->tableConsignee . '.name as consigneename,
                    CONCAT_WS(\'\',' . $this->tableRef . '.routefrom,\'-\',' . $this->tableRef . '.routeto)  as route,
                    ' . $this->tableLocation . '.name as locationname,
                    ' . $this->tableCar . '.policenumber as carregistrationnumber
	 		FROM 
                 ' . $this->tableWorkOrder . ' as ' . $this->tableName . ' 
                     left join  ' . $this->tableAPEmployee . ' on   ' . $this->tableName . '.refcashoutcodriverkey = ' . $this->tableAPEmployee . '.pkey
                     left join ' . $this->tableCar . '  on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey,
                 ' . $this->tableName . ' as ' . $this->tableRef . '
                     left join ' . $this->tableCustomer . ' on ' . $this->tableRef . '.customerkey = ' . $this->tableCustomer . '.pkey
                     left join ' . $this->tableConsignee . ' on ' . $this->tableRef . '.consigneekey = ' . $this->tableConsignee . '.pkey
                     left join ' . $this->tableLocation . ' on ' . $this->tableRef . '.stuffinglocationkey = ' . $this->tableLocation . '.pkey
                     left join ' . $this->tableCategory . ' on ' . $this->tableRef . '.categorykey = ' . $this->tableCategory . '.pkey,
                 ' . $this->tableStatus . ',  
                 ' . $this->tableEmployee . ',
                 ' . $this->tableWarehouse . ' ,
                 ( select 0 as pkey, \'' . $this->lang['codriverCommission'] . '\' as name ) ' . $this->tableItem . ' 
	 		WHERE     
                 ' . $this->tableName . '.refkey = ' . $this->tableRef . '.pkey and 
                 ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and 
                 ' . $this->tableName . '.codriverkey = ' . $this->tableEmployee . '.pkey and 
                 ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and  
                 ' . $this->tableName . '.isoutsource = 0 and
                 ' . $this->tableName . '.codrivercommission > 0 and
                 ' . $this->tableName . '.statuskey not in(1,4)
 	 	';

         if (!empty($criteria)) $sql .=  ' ' . $criteria;
         array_push($arrSQL, $sql);

         $sql = implode(' UNION ALL ', $arrSQL);

         if (!empty($order))
             $sql .=  ' ' . $order;

         return $this->oDbCon->doQuery($sql);
     }*/


    function reCountSubtotal($arrParam)
    {


        $subtotal = 0;
        $grandtotal = 0;

        $arrItemKey = $arrParam['hidItemKey'];
        $arrPriceinunit = $arrParam['price'];
        $qtyInBaseUnit =  $arrParam['qty'];

        for ($i = 0; $i < count($arrItemKey); $i++) {

            if (empty($arrItemKey[$i]))
                continue;

            $priceInUnit = $this->unFormatNumber($arrPriceinunit[$i]);
            $qty = $this->unFormatNumber($qtyInBaseUnit[$i]);
            $subtotal += ($qty * $priceInUnit);
        }

        $subtotalCost = 0;
        $arrItemKeyCost = $arrParam['hidItemKeyCost'];
        $arrPriceCost = $arrParam['priceCost'];
        $qtyCost =  $arrParam['qtyCost'];

        for ($i = 0; $i < count($arrItemKeyCost); $i++) {

            if (empty($arrItemKeyCost[$i]))
                continue;

            $price = $this->unFormatNumber($arrPriceCost[$i]);
            $qty = $this->unFormatNumber($qtyCost[$i]);
            $subtotalCost += ($qty * $price);
        }

        // Header Cost
        $subtotalHeaderCost = 0;
        $arrItemKeyCost = $arrParam['hidItemKeyHeaderCost'];
        $qtyCost =  $arrParam['qtyHeaderCost'];

        for ($i = 0; $i < count($arrItemKeyCost); $i++) {

            if (empty($arrItemKeyCost[$i]))
                continue;

            $price = $this->getValidHeaderCost($arrParam, $i);

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

    function addCashOut($rsHeader, $rsSalesHeaderCost)
    {
        if (empty($rsSalesHeaderCost)) return;

        $hospitalCostCashOut = new HospitalCostCashOut();
        $warehouse = new Warehouse();
        $item = new Item();
        $coaLink = new COALink();

        // kalo pake planner, yg lama bisa masalah gk ?
        $rsCOALink = $coaLink->getCOALink('cashbankops', $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);
        $coakey = $rsCOALink[0]['coakey'];

        $recipientkey = (!empty($rsSalesHeaderCost[0]['employeekey'])) ? $rsSalesHeaderCost[0]['employeekey'] : $rsHeader[0]['plannerkey'];


        $arr = array();
        $totalCashOut = 0;

        for ($i = 0; $i < count($rsSalesHeaderCost); $i++) {
            $rsItem = $item->getDataRowById($rsSalesHeaderCost[$i]['costkey']);
            $arr['hidDetailKey'][$i] = 0;
            $arr['refheadercostkey'][$i] = $rsSalesHeaderCost[$i]['pkey'];
            $arr['hidCostKey'][$i] = $rsSalesHeaderCost[$i]['costkey'];
            $arr['hidCOAKey'][$i] = $coakey;
            $arr['qty'][$i] = $rsSalesHeaderCost[$i]['qty'];
            $arr['costValue'][$i] = $rsSalesHeaderCost[$i]['requestamount'];
            $arr['amount'][$i] = $rsSalesHeaderCost[$i]['subtotal'];
            $arr['detailDesc'][$i] = '';
            $totalCashOut = $totalCashOut + $rsSalesHeaderCost[$i]['subtotal'];
        }

        $arr['code'] = 'xxxxxx';
        $arr['hidRefKey'] = $rsHeader[0]['pkey'];
        $arr['refCode'] = $rsHeader[0]['code'];
        $arr['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
        $arr['hidEmployeeKey'] = $recipientkey;
        $arr['selWarehouse'] = $rsHeader[0]['warehousekey'];
        $arr['trDesc'] = $rsHeader[0]['trdesc'];
        $arr['subtotal'] = $totalCashOut;
        $arr['total'] = $totalCashOut;
        $rsCashOutKey =  $this->getTableKeyAndObj($this->tableName);
        $arr['hidRefTable'] = $rsCashOutKey['key'];

        $arrayToJs = $hospitalCostCashOut->addData($arr);

        // sementara utk logol
        if ($this->useRealization()) {
            $employee = new Employee();
            $rsEmployee = $employee->getDataRowById($recipientkey);

            // sementara saja pake patokan ini, harusnya pake settingan lg (autoconfirm)
            // kalo gk butuh realisasi, langsung proses kas keluar
            if (!empty($rsEmployee) && $rsEmployee[0]['needrealization'] == 0) {
                $hospitalCostCashOut->changeStatus($arrayToJs[0]['data']['pkey'], 2);
            }
        }


        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
    }

    function validateForm($arr, $pkey = '')
    {
        $service = new Service();
        $security = new Security();
        $hospitalSellingRate  = new HospitalSellingRate();

        // kalo dr API (yg gk ad userkey) / SYSTEM, gk perlu cek kontrak.
        $overwriteContractAllowed = (empty($this->userkey)) ? true :  $security->isAdminLogin($this->overwriteContractSecurityObject, 10);

        $arrayToJs = parent::validateForm($arr, $pkey);

        $customerkey = $arr['hidCustomerKey'];
        $patientKey = $arr['hidPatientKey'];
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


        $rs = (!empty($pkey)) ? $this->getDataRowById($pkey) : array();

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
            // if ($rs[0]['statuskey'] == 2 ){

            //     $costRateIsMandatory = $this->loadSetting('costRateIsMandatory');
            //     if ($costRateIsMandatory == 1) { 
            //         $response = $this->validateFixedCostMustExist(array('code' => $arr['code'],
            //                                                             'warehousekey' => $arr['selWarehouseKey'],
            //                                                             'categorykey' => $arr['hidCategoryKey'],
            //                                                             'stuffinglocationkey' => $arr['hidStuffingLocationKey'],
            //                                                             'cargotypekey' => $arr['hidCargoType'], 
            //                                                             'consigneekey' =>  $arr['hidConsigneeKey']
            //                                                            ), $arr['hidItemKey']);

            //         $arrayToJs += $response;
            //     }

            // }


        } 

        if (empty($patientKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['patient'][1]);
        }

        
        //  error kalo gk punya akses overwrite.
        //  di add / edit sudah narik ulang cargotype, harusnya sih aman
    


        //  if (!$overwriteContractAllowed){
        //      // hanya cek jika add atau edit status 1
        //      if ( (empty($rs) || $rs[0]['statuskey'] == 1) && empty($arr['hidContractKey'])){  
        //              $this->addErrorList($arrayToJs,false, $this->errorMsg['sellingRate'][1]);  
        //      }

        //      // cek kontrak salah pelanggan gk
        //      if(!empty($arr['hidContractKey'])){ 
        //          $rsContract = $hospitalSellingRate->getDataRowById($arr['hidContractKey']);
        //          if ($rsContract[0]['customerkey'] <> $customerkey){
        //               $this->addErrorList($arrayToJs,false, $this->errorMsg['sellingRate'][3]);  
        //          }
        //      }
        //  }

        $hasDetail = false;
        for($i=0;$i<count($arrItemkey);$i++) { 
            if (!empty($arrItemkey[$i]))
                $hasDetail = true;
        }

        for($i=0;$i<count($arrCostKey);$i++) { 
            if (!empty($arrCostKey[$i]))
                $hasDetail = true;
        }


        // if(empty($arr['_mnv-api']) && !$hasDetail)
        //     $this->addErrorList($arrayToJs,false, $this->errorMsg[501]); 


        $arrDetailKeys = array();

        for($i=0;$i<count($arrItemkey);$i++) {  
        	if (!empty($arrItemkey[$i])){  
                if ( $this->unFormatNumber($arrPriceInUnit[$i]) <= 0){  
                    $rsItem = $service->getDataRowById($arrItemkey[$i]); 
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. '. $this->errorMsg[500]);  
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

        // for($i=0;$i<count($arrDetailEmployeeKey);$i++) {  
        //     if (empty($arrDetailEmployeeKey[$i])){   
        //         $rsItem = $service->getDataRowById($arrHeaderCostKey[$i]); 
        //         $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. '. $this->errorMsg['recipient'][1]);   
        //     } 
        // } 

        /*if(empty($plannerkey)){
            for($i=0;$i<count($arrDetailEmployeeKey);$i++) {  
                if (empty($arrDetailEmployeeKey[$i])){   
                    $rsItem = $service->getDataRowById($arrHeaderCostKey[$i]); 
                    $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. '. $this->errorMsg['recipient'][1]);   
                } 
            } 
        }*/

        // validasi qty invoiced
        // if (!empty($pkey)){ 

        //     // ITEM DETAIL 
        //     $arrDetailKey = $arr['hidDetailKey'];
        //     $rsDetail = $this->getDetailById($pkey); 
        //     $rsDetail = array_column($rsDetail,null,'pkey');

        //     for($i=0;$i<count($arrDetailKey);$i++) {  
        //         $detailkey = $arrDetailKey[$i];

        //         if (!empty($arrItemkey[$i])){  
        //             $qty = $this->unFormatNumber($arrQty[$i]);  
        //             $qtyInvoiced = ( isset($rsDetail[$detailkey]) ) ? $rsDetail[$detailkey]['qtyinvoiced'] : 0 ; 
        //             if ( $qty < $qtyInvoiced ){   
        //                 $rsItem = $service->getDataRowById($arrItemkey[$i]); 
        //                 $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[505]);  
        //             } 
        //         } 
        //     }


        // validasi SPK, cari yg SPK sudah konfirmasi, tp di JO nya gk ad itemnya ATAU di JO row nya kehapus
        // utk validasi perubahan layanan setelah diproses

        // if ($rs[0]['statuskey'] > TRANSACTION_STATUS['menunggu']){  
        //     $truckingServiceWorkOrder = new TruckingServiceWorkOrder();  
        //     //$rsWO = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey',$pkey,true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')');
        //     $rsWO = $truckingServiceWorkOrder->searchDataRow( array( $truckingServiceWorkOrder->tableName.'.pkey',
        //                                                              $truckingServiceWorkOrder->tableName.'.code',
        //                                                              $truckingServiceWorkOrder->tableName.'.itemkey',
        //                                                              $truckingServiceWorkOrder->tableName.'.refdetailkey', 
        //                                                            ) , 
        //                                                 '   and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).'
        //                                                     and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
        //                                             ); 

        //     foreach($rsWO as $woRow){      
        //         // kalo SPK sudah diproses, tp detail di JO dihapus
        //         if (!in_array( $woRow['refdetailkey'] ,$arrDetailKey)){
        //              $rsService = $service->getDataRowById($woRow['itemkey']);
        //              $this->addErrorList($arrayToJs,false,'<strong>'.$rsService[0]['name'].' ('.$woRow['code'].')</strong>. '.$this->errorMsg['truckingServiceWorkOrder'][9]); 
        //         }else{ 
        //             // jika detail masih ad, tp item sudah berbeda
        //             for($i=0;$i<count($arrDetailKey);$i++){   
        //                 if ( $woRow['refdetailkey'] == $arrDetailKey[$i] && $woRow['itemkey'] != $arrItemkey[$i]) 
        //                     $this->addErrorList($arrayToJs,false,'<strong>'.$woRow['containername'].' ('.$woRow['code'].')</strong>. '.$this->errorMsg['truckingServiceWorkOrder'][9]); 
        //             } 
        //         }

        //     }

        // } 

        //     // SELLING COST
        //     $arrDetailKey = $arr['hidDetailCostKey'];
        //     $rsDetail = $this->getSellingCostDetail($pkey);
        //     $rsDetail = array_column($rsDetail,null,'pkey');

        //     for($i=0;$i<count($arrDetailKey);$i++) {  
        //         $detailkey = $arrDetailKey[$i];
        //         if (!empty($arrCostKey[$i])){  
        //             $qty = $this->unFormatNumber($arrCostQty[$i]);  
        //             $qtyInvoiced = ( isset($rsDetail[$detailkey]) ) ? $rsDetail[$detailkey]['qtyinvoiced'] : 0 ; 
        //             if ( $qty < $qtyInvoiced ){   
        //                 $rsItem = $service->getDataRowById($arrCostKey[$i]); 
        //                 $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[505]);  
        //             } 
        //         } 
        //     }
        // }


        return $arrayToJs;
    }

    function updateGL($rs)
    {
    }

    function hasConfirmedWorkOrder($detailkey)
    {
        $hospitalWorkOrder = new HospitalWorkOrder();

        $rsWO = $hospitalWorkOrder->searchDataRow(
            array($hospitalWorkOrder->tableName . '.pkey'),
            '   and ' . $hospitalWorkOrder->tableName . '.refdetailkey = ' . $this->oDbCon->paramString($detailkey) . '
                                                                and ' . $hospitalWorkOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['konfirmasi'] . ',' . TRANSACTION_STATUS['selesai'] . ')'
        );


        return (empty($rsWO)) ? false : true;
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   			' . $this->tableNameDetail . '.*, 
                concat ("#", ' . $this->tableNameDetail . '.numberkey, " - ", ' . $this->tableNameDetail . '.qtyinbaseunit,"x ", ' . $this->tableItem . '.name) as label,
                ' . $this->tableItem . '.code as itemcode,
                ' . $this->tableItem . '.name as itemname,
                ' . $this->tableNameDetailStatus . '.status as statusname ,
                ' . $this->tableNameDetailStatus . '.class
              from
			  	' . $this->tableNameDetail . ',
			  	' . $this->tableNameDetailStatus . ',
                ' . $this->tableItem . ' 
			  where
			  	' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey and 
			  	' . $this->tableNameDetail . '.statuskey = ' . $this->tableNameDetailStatus . '.pkey and 
			  	refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        $sql .= ' order by refkey, numberkey asc';

        return $this->oDbCon->doQuery($sql);
    }

    function generateDefaultQueryForAutoComplete($returnField)
    {

        $sql = 'select
					' . $returnField['key'] . ',
					' . $returnField['value'] . ' as value, 
                    '.$this->tableName . '.trdate,
                    '.$this->tableName . '.donumber,
                    '.$this->tableName . '.grandtotal,
                    '.$this->tableCustomerInsurancePolicy . '.name as patientname
				from 
					' . $this->tableName . '
                    left join '.$this->tableCustomerInsurancePolicy.'  on '.$this->tableName.'.patientkey = '.$this->tableCustomerInsurancePolicy.'.pkey,
                    ' . $this->tableStatus . ' 
				where  		 
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
			';

        $sql .=  $this->getWarehouseCriteria();
        return $sql;
    }

    function getCostDetail($pkey, $jobtypekey = '',  $itemkey = '', $costkey = '')
    {
        // gk perlu lokasi, karena sudah ad pkey job order
        // jobtypekey => jenis pekerjaan, bukan kategori pekerjaan misalnya Landing + Tarik Full, Kirim head dst
        // getCostDetail($rsJobType[$k]['jobtypekey'],  $rsHeader[0]['consigneelocationkey'] , $rsDetail[$i]['itemkey'] ); 

        $sql = 'select
			  	' . $this->tableName . '.code, 
	   			' . $this->tableCost . '.* , 
	   			' . $this->tableItem . '.name    
			  from
			  	' . $this->tableName . ',  
			  	' . $this->tableCost . ' , 
			  	' . $this->tableItem . ' 
			  where 
                ' . $this->tableName . '.pkey =  ' . $this->tableCost . '.refkey and 
                ' . $this->tableCost . '.costkey =  ' . $this->tableItem . '.pkey ';

        $criteria  =  array();

        if (!empty($pkey))
            array_push($criteria, $this->tableName . '.pkey = ' . $this->oDbCon->paramString($pkey));

        if (!empty($jobtypekey))
            array_push($criteria, $this->tableCost . '.jobtypekey = ' . $this->oDbCon->paramString($jobtypekey));

        if (!empty($itemkey))
            array_push($criteria, $this->tableCost . '.itemkey = ' . $this->oDbCon->paramString($itemkey));

        if (!empty($costkey))
            array_push($criteria, $this->tableCost . '.costkey = ' . $this->oDbCon->paramString($costkey));

        if (!empty($criteria)) {
            $criteria = implode(' and ', $criteria);
            $sql .= ' and ' . $criteria;
        }

        $rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }

    function getHeaderCost($pkey, $criteria = '')
    {

        $sql = 'select 
	   			' . $this->tableHeaderCost . '.* , 
	   			' . $this->tableItem . '.code  as itemcode , 
	   			' . $this->tableItem . '.name  as itemname , 
	   			' . $this->tableItem . '.reimburse,
                ' . $this->tableTruckingCostCashOut . '.code as refcashoutcode,
                ' . $this->tableEmployee . '.name as recipientname,
                ' . $this->tableEmployee . '.code as recipientcode
			  from 
			  	' . $this->tableHeaderCost . ' 
                    left join ' . $this->tableTruckingCostCashOut . ' on ' . $this->tableHeaderCost . '.refcashoutkey = ' . $this->tableTruckingCostCashOut . '.pkey
                    left join ' . $this->tableEmployee . ' on ' . $this->tableHeaderCost . '.employeekey = ' . $this->tableEmployee . '.pkey,
			  	' . $this->tableItem . ' 
			  where   
                ' . $this->tableHeaderCost . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ')  and
                ' . $this->tableHeaderCost . '.costkey =  ' . $this->tableItem . '.pkey ';

        if (!empty($criteria))
            $sql .=  ' ' . $criteria;

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function getSellingCostDetail($pkey, $criteria = '')
    {

        $sql = 'select 
	   			' . $this->tableSellingCost . '.* , 
	   			' . $this->tableItem . '.code  as itemcode , 
	   			' . $this->tableItem . '.name  as itemname
			  from 
			  	' . $this->tableSellingCost . ' , 
			  	' . $this->tableItem . ' 
			  where   
                ' . $this->tableSellingCost . '.costkey =  ' . $this->tableItem . '.pkey and
                ' . $this->tableSellingCost . '.refkey in(' . $this->oDbCon->paramString($pkey, ',') . ')';

        if (!empty($criteria))
            $sql .=  ' ' . $criteria;

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function getUnInvoicedItemDetail($pkey)
    {

        // asumsi itemkey dan costkey, pasti pkeynya unique, dan masing2 hanya bisa di detail atau di cost
        $sql = '  SELECT trans.*, item.name as itemname,item.istax23,ispriceincludetax,taxpercentage, item.aliasname from ( 
                    select concat(pkey,\'-\',itemkey) as joinkey, pkey, refkey, itemkey, qtyinbaseunit,  (qtyinbaseunit - qtyinvoiced) as outstandingqty, priceinunit, (qtyinbaseunit - qtyinvoiced) * priceinunit as total, \'1\' as orderlist  from  ' . $this->tableNameDetail . ' where refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') UNION
                    select concat(pkey,\'-\',costkey) as joinkey, pkey, refkey, costkey as itemkey, qty as qtyinbaseunit, (qty - qtyinvoiced) as outstandingqty, price as priceinunit, (qty - qtyinvoiced) * price as total, \'2\' as orderlist from ' . $this->tableSellingCost . ' where refkey in (' . $this->oDbCon->paramString($pkey, ',') . ')
                 ) trans, item 
                 where  
                    trans.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') and  
                    trans.itemkey = item.pkey  and outstandingqty > 0  
                 order by orderlist asc, pkey asc
                ';

        $rs = $this->oDbCon->doQuery($sql);

        // overwrite alias
        // harus cari customernya dulu dr header, nanti dilihat berat gk
        $rsHeader = $this->searchDataRow(
            array($this->tableName . '.customerkey'),
            ' and ' . $this->tableName . '.pkey in (' . $this->oDbCon->paramString($pkey, ',') . ')',
            ' limit 1'
        );

        $customer = new Customer();
        $rsItemAlias = $customer->getItemAliasDetail($rsHeader[0]['customerkey']);
        $rsItemAlias = array_column($rsItemAlias, 'alias', 'itemkey');

        $totalRs = count($rs);
        for ($i = 0; $i < $totalRs; $i++) {
            $itemkey = $rs[$i]['itemkey'];
            if (isset($rsItemAlias[$itemkey]) && !empty($rsItemAlias[$itemkey]))
                $rs[$i]['aliasname'] = $rsItemAlias[$itemkey];
        }

        return $rs;
    }

    function updateDetailStatusType2($detailkey)
    {

        $hospitalWorkOrder = new HospitalWorkOrder();

        try {

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            $rsDetail = $this->getDetailByColumn('pkey', $detailkey);

            $sql = 'select 
                        ' . $hospitalWorkOrder->tableName . '.pkey,
                        ' . $hospitalWorkOrder->tableName . '.refkey,
                        ' . $hospitalWorkOrder->tableName . '.statuskey,
                        sum(' . $hospitalWorkOrder->tableWorkOrderCarDetail . '.qty) as totalqty
                    from
                        ' . $hospitalWorkOrder->tableName . ',
                        ' . $hospitalWorkOrder->tableWorkOrderCarDetail . '
                    where
                        ' . $hospitalWorkOrder->tableWorkOrderCarDetail . '.refkey = ' . $hospitalWorkOrder->tableName . '.pkey and
                        ' . $hospitalWorkOrder->tableName . '.refdetailkey = ' . $this->oDbCon->paramString($detailkey) . ' and 
                        ' . $hospitalWorkOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['menunggu'] . ',' . TRANSACTION_STATUS['konfirmasi'] . ',' . TRANSACTION_STATUS['selesai'] . ')
                    group by 
                        ' . $hospitalWorkOrder->tableName . '.statuskey
                    order by 
                        ' . $hospitalWorkOrder->tableName . '.pkey desc
                    ';

            $rs = $this->oDbCon->doQuery($sql);
            $rsSumQty = array_column($rs, 'totalqty', 'statuskey');

            // harus tampung dulu, karena kalo gk ad statusnya, gk ad indexnya
            $arrSumQty = array();
            $rsStatus = $this->getAllStatus($this->tableDetailStatus);
            for ($i = 0; $i < count($rsStatus); $i++)
                $arrSumQty[$rsStatus[$i]['pkey']] = (isset($rsSumQty[$rsStatus[$i]['pkey']])) ?  $rsSumQty[$rsStatus[$i]['pkey']] : 0;

            //status selesai kalo sudah tdk ad yg menunggu dan konfirmasi dan jml partai sama / lebih dr qty
            // bisa saja, jml selesai sudah sesuai dgn jml qty, tp masih ad yg di pending / konfirmasi 1 partai, tetep dianggap blm selesai
            if (
                $arrSumQty[TRANSACTION_STATUS['menunggu']] == 0 &&
                $arrSumQty[TRANSACTION_STATUS['konfirmasi']] == 0 &&
                $arrSumQty[TRANSACTION_STATUS['selesai']] >= $rsDetail[0]['qtyinbaseunit']
            ) {
                $statuskey = 3;
            } else if ($arrSumQty[TRANSACTION_STATUS['konfirmasi']] == 0 && $arrSumQty[TRANSACTION_STATUS['selesai']] == 0) {
                //status menunggu kalo semua qty hanya ad di menunggu
                $statuskey = 1;
            } else {
                $statuskey = 2;
            }

            $sql = 'update ' . $this->tableNameDetail . ' set statuskey = ' . $statuskey . ' where pkey = ' . $this->oDbCon->paramString($detailkey);
            $this->oDbCon->execute($sql);

            //update header kalo semua status sudah selesai 
            // kalo gk ad SPK, gk auto closing
            if (!empty($rs)) {
                // cari detailnya kalo sudah selesai semua, update ke SPK Selesai

                $rsSPK = $this->getDetailById($rsDetail[0]['refkey']);
                $completed = true;
                $arrPartialStatus = array(1, 2);
                foreach ($rsSPK as $row) {
                    // kalo ad salah satu detailnya yg statusnya menunggu / partial
                    if (in_array($row['statuskey'], $arrPartialStatus)) {
                        $completed = false;
                        break;
                    }
                }

                $status = ($completed) ? 3 : 2;
                $rsHeader = $this->getDataRowById($rsDetail[0]['refkey']);

                if ($status <> $rsHeader[0]['statuskey'])
                    $this->changeStatus($rsHeader[0]['pkey'], $status, '', false, true);
            }

            $this->oDbCon->endTrans();
        } catch (Exception $e) {
            $this->oDbCon->rollback();
        }
    }

    function updateDetailStatus($detailkey)
    {
        $truckingType = $this->loadSetting('truckingType');

        // if ($truckingType == 2) {
            $this->updateDetailStatusType2($detailkey);
            // return;
        // }

        // ASUMSI SETIAP JO PASTI MIN ADA 1 SPK, GK BISA !!!

        $hospitalWorkOrder = new HospitalWorkOrder();

        try {

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            // search semua status work order, kalo sudah closed semua, update status 
            //$rs = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refdetailkey',$detailkey,true,' and '. $truckingServiceWorkOrder->tableName.'.statuskey in (1,2,3)','order by pkey desc');

            $rs = $hospitalWorkOrder->searchDataRow(
                array(
                    $hospitalWorkOrder->tableName . '.pkey',
                    $hospitalWorkOrder->tableName . '.refkey',
                    $hospitalWorkOrder->tableName . '.statuskey'
                ),
                '   and ' . $hospitalWorkOrder->tableName . '.refdetailkey = ' . $this->oDbCon->paramString($detailkey) . '
                                                            and ' . $hospitalWorkOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['menunggu'] . ',' . TRANSACTION_STATUS['konfirmasi'] . ',' . TRANSACTION_STATUS['selesai'] . ')',
                'order by ' . $hospitalWorkOrder->tableName . '.pkey desc'
            );


            $totalSPK = count($rs);
            $statusSPK = array();

            $rsStatus = $this->getAllStatus($this->tableDetailStatus);
            for ($i = 0; $i < count($rsStatus); $i++) {
                $statusSPK[$rsStatus[$i]['pkey']] = 0;
            }

            for ($i = 0; $i < count($rs); $i++) {
                $statuskey = $rs[$i]['statuskey'];
                $statusSPK[$statuskey]++;
            }

            // kalo semua status masih open 
            if ($statusSPK[1] == count($rs))
                $statuskey = 1;
            else if ($statusSPK[2] <> 0)
                $statuskey = 2;
            else
                $statuskey = 3;

            $sql = 'update ' . $this->tableNameDetail . ' set statuskey = ' . $statuskey . ' where pkey = ' . $this->oDbCon->paramString($detailkey);
            $this->oDbCon->execute($sql);


            //update header kalo semua status sudah selesai 
            // kalo gk ad SPK, gk auto closing
            if (!empty($rs)) {
                //$rsSPK = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey', $rs[0]['refkey'] ,true,' and '. $truckingServiceWorkOrder->tableName.'.statuskey in (1,2)','order by pkey desc');

                $rsSPK = $hospitalWorkOrder->searchDataRow(
                    array($hospitalWorkOrder->tableName . '.pkey'),
                    '   and ' . $hospitalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($rs[0]['refkey']) . '
                                                                                    and ' . $hospitalWorkOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['menunggu'] . ',' . TRANSACTION_STATUS['konfirmasi'] . ')',
                    'order by ' . $hospitalWorkOrder->tableName . '.pkey desc'
                );



                $status = (empty($rsSPK)) ? 3 : 2;

                $rsDetail = $this->getDetailByColumn('pkey', $detailkey);
                if (!empty($rsDetail)) {
                    $rsHeader = $this->getDataRowById($rsDetail[0]['refkey']);
                    if ($status <> $rsHeader[0]['statuskey'])
                        $this->changeStatus($rs[0]['refkey'], $status, '', false, true);
                }
            }

            $this->oDbCon->endTrans();
        } catch (Exception $e) {
            $this->oDbCon->rollback();
        }
    }

    function updateContainer($pkey)
    {
        $hospitalWorkOrder = new HospitalWorkOrder();
        $arrContainerJO = array();
        $arrContainerSPK = array();

        $rsHeader = $this->getDataRowById($pkey);

        //$rsSPK = $truckingServiceWorkOrder->searchData('','',true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in (2,3) and '.$truckingServiceWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($pkey));
        $rsSPK = $hospitalWorkOrder->searchDataRow(
            array(
                $hospitalWorkOrder->tableName . '.pkey',
                $hospitalWorkOrder->tableName . '.containernumber',
                $hospitalWorkOrder->tableName . '.container2number'
            ),
            '   and ' . $hospitalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . '
                                                                and ' . $hospitalWorkOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['konfirmasi'] . ',' . TRANSACTION_STATUS['selesai'] . ')'
        );


        //tampung semua container dan container2 jadi 1 variabel
        for ($i = 0; $i < count($rsSPK); $i++) {
            if (empty($rsSPK[$i]['containernumber']) && empty($rsSPK[$i]['container2number']))
                continue;

            if (!empty($rsSPK[$i]['containernumber']))
                array_push($arrContainerSPK, $rsSPK[$i]['containernumber']);

            if (!empty($rsSPK[$i]['container2number']))
                array_push($arrContainerSPK, $rsSPK[$i]['container2number']);
        }

        for ($i = 0; $i < count($arrContainerSPK); $i++) {
            if (in_array($arrContainerSPK[$i], $arrContainerJO))
                continue;

            array_push($arrContainerJO, $arrContainerSPK[$i]);
        }

        $container = implode(', ', $arrContainerJO);

        $sql = ' update ' . $this->tableName . ' set containernumber = ' . $this->oDbCon->paramString($container) . '
                 where ' . $this->tableName . '.pkey = ' . $this->oDbCon->paramString($pkey);

        $this->oDbCon->execute($sql);
    }

    function getWorkOrderCostDetail($pkey,  $forOutsource = false, $group = true, $criteria = '', $orderBy = ' order by  workordercode asc, costname asc ')
    {
        if (empty($pkey)) return array(); // biar pas load JO form pertama kali gk berat

        $ap = new AP();

        // untuk form complete order
        $supplier = new Supplier();
        $employee = new Employee();

        $truckingType = $this->loadSetting('truckingType');

        $arrCriteria = array();
        array_push($arrCriteria, $this->tableWorkOrder . '.statuskey in (2,3)');
        array_push($arrCriteria, $this->tableWorkOrder . '.refkey = ' . $this->oDbCon->paramString($pkey));

        $rsAPTypeKey = $this->getTableKeyAndObj($this->tableWorkOrder, array('key'));

        if ($forOutsource) {
            $tableName = $supplier->tableName;
            $fieldName = 'supplierkey';
            $cashOutTable = $ap->tableName;
            $rsWOTypeKey = $ap->getTableKeyAndObj($this->tableWorkOrderCost);
        } else {
            $tableName = $employee->tableName;
            $fieldName = 'employeekey';
            $cashOutTable = $this->tableTruckingCostCashOut;
            $rsWOTypeKey = $ap->getTableKeyAndObj($this->tableWorkOrder);
        }

        $sqlCriteria = ' and ' . implode(' and ', $arrCriteria);

        $selectCount = '';
        $groupBy = '';
        if ($group) {
            $selectCount = ' sum(qty) as qty, ';
            $groupBy = ' group by costkey, amount ';
            $orderBy = ' order by costname asc  ';
        }

        $arrSQL = array();

        $sql = '
            select  
                ' . $this->tableWorkOrder . '.refkey, 
                ' . $this->tableWorkOrderCost . '.qty,
                ' . $this->tableWorkOrderCost . '.costkey,
                ' . $this->tableWorkOrderCost . '.amount,
                ' . $this->tableWorkOrderCost . '.requestamount,
                ' . $this->tableWorkOrderCost . '.taxpercentage,
                ' . $this->tableWorkOrderCost . '.total,
                ' . $this->tableWorkOrderCost . '.isrealization,
                ' . $tableName . '.name as recipientname,
                ' . $this->tableItem . '.name as costname,
                ' . $this->tableItem . '.reimburse, 
                ' . $this->tableWorkOrder . '.code as workordercode, 
                ' . $this->tableWorkOrder . '.statuskey,
                ' . $cashOutTable . '.code as cashoutcode,
                0 as headerrow
            from
                ' . $tableName . ',
                ' . $this->tableWorkOrder . ', 
                ' . $this->tableWorkOrderCost . '  
                    left join ' . $cashOutTable . ' on ' . $this->tableWorkOrderCost . '.refcashoutkey = ' . $cashOutTable . '.pkey and
                        ' . $cashOutTable . '.reftabletype = ' . $rsWOTypeKey['key'] . ',
                ' . $this->tableItem . ' 
            where 
                ' . $this->tableWorkOrder . '.pkey = ' . $this->tableWorkOrderCost . '.refkey  and
                ' . $this->tableItem . '.pkey = ' . $this->tableWorkOrderCost . '.costkey and
                ' . $this->tableWorkOrderCost . '.' . $fieldName . ' = ' . $tableName . '.pkey
        ';
        $sql .= $sqlCriteria;

        array_push($arrSQL, $sql);

        // tambah biaya TL
        if ($forOutsource) {

            if ($truckingType == 2) {
                // model logol  

                $sqlOutsource = '
                    select  
                        ' . $this->tableWorkOrder . '.refkey, 
                        ' . $this->tableWorkOrderCarDetail . '.qty as qty,
                        ' . $this->tableWorkOrderCarDetail . '.itemkey as costkey,
                        ' . $this->tableWorkOrderCarDetail . '.price as amount,  
                        ' . $this->tableWorkOrderCarDetail . '.price as requestamount,  
                        ' . $this->tableWorkOrderCarDetail . '.taxpercentage as taxpercentage,
                        ' . $this->tableWorkOrderCarDetail . '.total as total,  
                        1 as isrealization,
                        ' . $this->tableSupplier . '.name as recipientname,
                        \'' . $this->lang['truckingFee'] . '\' as costname,
                        0 as reimburse, 
                        ' . $this->tableWorkOrder . '.code as workordercode, 
                        ' . $this->tableWorkOrder . '.statuskey,
                        ' . $cashOutTable . '.code as cashoutcode,
                        1 as headerrow
                    from 
                        ' . $this->tableSupplier . ',
                        ' . $this->tableWorkOrderCarDetail . ' ,
                        ' . $this->tableWorkOrder . ' 
                            left join  ' . $cashOutTable . ' on ' . $this->tableWorkOrder . '.refcashoutkey = ' . $cashOutTable . '.pkey and
                        ' . $cashOutTable . '.reftabletype = ' . $rsAPTypeKey['key'] . '
                    where   
                        ' . $this->tableWorkOrder . '.isoutsource = 1 and
                        ' . $this->tableWorkOrder . '.outsourcecost <> 0 and
                        ' . $this->tableWorkOrder . '.pkey = ' . $this->tableWorkOrderCarDetail . '.refkey and
                        ' . $this->tableWorkOrder . '.supplierkey = ' . $this->tableSupplier . '.pkey
                ';
            } else {

                // reguler
                $sqlOutsource = '
                    select  
                        ' . $this->tableWorkOrder . '.refkey, 
                        1 as qty,
                        0 as costkey,
                        ' . $this->tableWorkOrder . '.outsourcecost as amount,  
                        ' . $this->tableWorkOrder . '.outsourcecost as requestamount,  
                        0 as taxpercentage,
                        ' . $this->tableWorkOrder . '.outsourcecost as total,  
                        1 as isrealization,
                        ' . $this->tableSupplier . '.name as recipientname,
                        \'' . $this->lang['truckingFee'] . '\' as costname,
                        0 as reimburse, 
                        ' . $this->tableWorkOrder . '.code as workordercode, 
                        ' . $this->tableWorkOrder . '.statuskey,
                        ' . $cashOutTable . '.code as cashoutcode,
                        1 as headerrow
                    from 
                        ' . $this->tableSupplier . ',
                        ' . $this->tableWorkOrder . '  
                            left join  ' . $cashOutTable . ' on ' . $this->tableWorkOrder . '.refcashoutkey = ' . $cashOutTable . '.pkey and
                        ' . $cashOutTable . '.reftabletype = ' . $rsAPTypeKey['key'] . '
                    where   
                        ' . $this->tableWorkOrder . '.isoutsource = 1 and
                        ' . $this->tableWorkOrder . '.outsourcecost <> 0 and
                        ' . $this->tableWorkOrder . '.supplierkey = ' . $this->tableSupplier . '.pkey
                ';
            }



            $sqlOutsource .= $sqlCriteria;



            array_push($arrSQL, $sqlOutsource);
        } else {
            $sqlCommission = '
                select  
                    ' . $this->tableWorkOrder . '.refkey, 
                    1 as qty,
                    0 as costkey,
                    ' . $this->tableWorkOrder . '.drivercommission as amount,  
                    ' . $this->tableWorkOrder . '.drivercommission as requestamount,  
                    0 as taxpercentage,
                    ' . $this->tableWorkOrder . '.drivercommission as total,  
                    1 as isrealization,
                    ' . $this->tableEmployee . '.name as recipientname,
                    \'' . $this->lang['paramedicService']  . ' 1\' as costname,
                    0 as reimburse, 
                    ' . $this->tableWorkOrder . '.code as workordercode, 
                    ' . $this->tableWorkOrder . '.statuskey,
                    ' . $this->tableAPEmployee . '.code as cashoutcode,
                    1 as headerrow
                from  
                    ' . $this->tableWorkOrder . '  
			            left join ' . $this->tableEmployee . '   on  ' . $this->tableWorkOrder . '.driverkey = ' . $this->tableEmployee . '.pkey
                        left join ' . $this->tableAPEmployee . ' on ' . $this->tableWorkOrder . '.refcashoutdriverkey = ' . $this->tableAPEmployee . '.pkey and
                            ' . $this->tableAPEmployee . '.reftabletype = ' . $rsAPTypeKey['key'] . ' 
                where   
                    ' . $this->tableWorkOrder . '.isoutsource = 0 and
                    ' . $this->tableWorkOrder . '.drivercommission > 0
                   
            ';
            $sqlCommission .= $sqlCriteria;
            array_push($arrSQL, $sqlCommission);

            $sqlCoDriverCommission = '
                select  
                    ' . $this->tableWorkOrder . '.refkey, 
                    1 as qty,
                    0 as costkey,
                    ' . $this->tableWorkOrder . '.codrivercommission as amount,  
                    ' . $this->tableWorkOrder . '.codrivercommission as requestamount,  
                    0 as taxpercentage,
                    ' . $this->tableWorkOrder . '.codrivercommission as total,  
                    1 as isrealization,
                    ' . $this->tableEmployee . '.name as recipientname,
                    \'' . $this->lang['paramedicService'] . ' 2\' as costname,
                    0 as reimburse, 
                    ' . $this->tableWorkOrder . '.code as workordercode, 
                    ' . $this->tableWorkOrder . '.statuskey,
                    ' . $this->tableAPEmployee . '.code as cashoutcode,
                    1 as headerrow
               from  
                    ' . $this->tableWorkOrder . '  
                        left join ' . $this->tableEmployee . '  on  ' . $this->tableWorkOrder . '.codriverkey = ' . $this->tableEmployee . '.pkey 
                        left join ' . $this->tableAPEmployee . ' on ' . $this->tableWorkOrder . '.refcashoutcodriverkey = ' . $this->tableAPEmployee . '.pkey and
                            ' . $this->tableAPEmployee . '.reftabletype = ' . $rsAPTypeKey['key'] . ' 
                where   
                    ' . $this->tableWorkOrder . '.isoutsource = 0 and
                    ' . $this->tableWorkOrder . '.codrivercommission > 0
            ';
            $sqlCoDriverCommission .= $sqlCriteria;
            array_push($arrSQL, $sqlCoDriverCommission);

            $sqlDoctorCommission = '
                select  
                    ' . $this->tableWorkOrder . '.refkey, 
                    1 as qty,
                    0 as costkey,
                    ' . $this->tableWorkOrder . '.doctorcommission as amount,  
                    ' . $this->tableWorkOrder . '.doctorcommission as requestamount,  
                    0 as taxpercentage,
                    ' . $this->tableWorkOrder . '.doctorcommission as total,  
                    1 as isrealization,
                    ' . $this->tableEmployee . '.name as recipientname,
                    \'' . $this->lang['doctorService'] . '\' as costname,
                    0 as reimburse, 
                    ' . $this->tableWorkOrder . '.code as workordercode, 
                    ' . $this->tableWorkOrder . '.statuskey,
                    ' . $this->tableAPEmployee . '.code as cashoutcode,
                    1 as headerrow
               from  
                    ' . $this->tableWorkOrder . '  
                        left join ' . $this->tableEmployee . '  on  ' . $this->tableWorkOrder . '.doctorkey = ' . $this->tableEmployee . '.pkey 
                        left join ' . $this->tableAPEmployee . ' on ' . $this->tableWorkOrder . '.refcashoutdoctorkey = ' . $this->tableAPEmployee . '.pkey and
                            ' . $this->tableAPEmployee . '.reftabletype = ' . $rsAPTypeKey['key'] . ' 
                where   
                    ' . $this->tableWorkOrder . '.isoutsource = 0 and
                    ' . $this->tableWorkOrder . '.doctorcommission > 0
            ';
            $sqlDoctorCommission .= $sqlCriteria;
            array_push($arrSQL, $sqlDoctorCommission);
        }


        $sql = 'select 
                    ' . $selectCount . ' 
                    ' . $this->tableWorkOrder . '.* 
                from (' . implode(' UNION ALL ', $arrSQL) . ') as ' . $this->tableWorkOrder . ' where 1=1 ';

        $sql .= $criteria;
        $sql .= $groupBy;
        $sql .= $orderBy;

        $rs =  $this->oDbCon->doQuery($sql);
        return $rs;
    }

    function getGroupingOutsourceCost($pkey)
    {
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

    function updateTruckingCostCashOut($pkey)
    {

        //header harus reload ulang, karena status sudah berubah (ketika konfirmasi)
        $rsHeader = $this->getDataRowById($pkey);

        $hospitalCostCashOut = new HospitalCostCashOut();

        // get all listed employee
        $arrEmployeeKey = array();

        $sql = 'select distinct(employeekey) as employeekey from ' . $this->tableHeaderCost . ' where refkey = ' . $this->oDbCon->paramString($pkey);
        $rsDetailEmployee = $this->oDbCon->doQuery($sql);
        $arrEmployeeKey = array_column($rsDetailEmployee, 'employeekey');

        $rsKey = $this->getTableKeyAndObj($this->tableName, array('key'));

        // utk  delete karyawan yg sudah gk ad kas keluarnya
        $employeeCriteria = (!empty($arrEmployeeKey)) ? '  and reftabletype = ' . $rsKey['key'] . ' and employeekey not in (' . implode(',', $arrEmployeeKey) . ') ' : '';

        $rsCashOut = $hospitalCostCashOut->searchData('', '', true, $employeeCriteria . '
                                                                    and ' . $hospitalCostCashOut->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . '
                                                                    and ' . $hospitalCostCashOut->tableName . '.statuskey = 1');

        for ($i = 0; $i < count($rsCashOut); $i++)
            $this->cancelCashOut($pkey, $rsCashOut[$i]['employeekey']);

        // kalo status konfirmasi baru lanjut proses 

        if ($rsHeader[0]['statuskey'] >= 2 && $rsHeader[0]['statuskey'] <= 5) {

            // update ulang kas keluar  
            for ($i = 0; $i < count($arrEmployeeKey); $i++) {
                $employeeKey = $arrEmployeeKey[$i];

                // cost di JO 
                $rsCost = $this->getHeaderCost($rsHeader[0]['pkey'], ' and ' . $this->tableHeaderCost . '.refcashoutkey = 0 
                                                                         and ' . $this->tableHeaderCost . '.realizationkey = 0 
                                                                         and ' . $this->tableHeaderCost . '.employeekey = ' . $this->oDbCon->paramString($employeeKey));

                $headerCost = array();
                for ($j = 0; $j < count($rsCost); $j++) {
                    array_push($headerCost, $rsCost[$j]['pkey']);
                    array_push($headerCost, $rsCost[$j]['subtotal']);
                    array_push($headerCost, $rsCost[$j]['costkey']);
                }


                $headerCost = md5(json_encode($headerCost));

                // cost di cash out yg masi pending 
                $rsCashOut = $hospitalCostCashOut->searchData('', '', true, '  and reftabletype = ' . $rsKey['key'] . ' 
                                                                            and ' . $hospitalCostCashOut->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' 
                                                                            and ' . $hospitalCostCashOut->tableName . '.employeekey = ' . $this->oDbCon->paramString($employeeKey) . ' 
                                                                            and ' . $hospitalCostCashOut->tableName . '.statuskey = 1');
                $rsCashOutDetail = (!empty($rsCashOut)) ? $hospitalCostCashOut->getDetailById($rsCashOut[0]['pkey']) : array(); //ambil salah satu cashout aja
                $cashOutDetail = array();
                // harus tambah pkey detail, karena ad kemungkinan itemnya sama pkeynya berubah / pindah posisi, 
                // jdinya nanti pas kas keluar diproses, do JO nya gk keupdate
                for ($j = 0; $j < count($rsCashOutDetail); $j++) {
                    array_push($cashOutDetail, $rsCashOutDetail[$j]['refheadercostkey']);
                    array_push($cashOutDetail, $rsCashOutDetail[$j]['amount']);
                    array_push($cashOutDetail, $rsCashOutDetail[$j]['costkey']);
                }

                $cashOutDetail = md5(json_encode($cashOutDetail));


                $compareResult = ($cashOutDetail == $headerCost) ? true : false;

                if (!$compareResult) {
                    $this->cancelCashOut($pkey, $employeeKey);
                    $this->addCashOut($rsHeader, $rsCost);
                }
            }
        }
    }


    function normalizeParameter($arrParam, $trim = false)
    {

        $hospitalSellingRate = new HospitalSellingRate();
        $item = new Item();

        // agar gk muncul notice / warning , sampe nanti kita bisa trim details
        $arrParam['detailRequestId'] = (isset($arrParam['detailRequestId'])) ? $arrParam['detailRequestId']  : array();
        $arrParam['sellingCostRequestId'] = (isset($arrParam['sellingCostRequestId'])) ? $arrParam['sellingCostRequestId']  : array();
        $arrParam['headerCostRequestId'] = (isset($arrParam['headerCostRequestId'])) ? $arrParam['headerCostRequestId']  : array();


        // harusnya boleh diupdate kalo sudah di save
        if (isset($arrParam['token-item-file-uploader']))
            $arrParam['fileName'] = $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);

        // untuk patokan add / edit
        $pkey = 0;
        $rsHeader = array();
        if (isset($arrParam['hidId']) && !empty($arrParam['hidId'])) {
            $pkey = $arrParam['hidId'];
            $rsHeader = $this->getDataRowById($pkey);
        }

        if (isset($arrParam['autoInvoice']) && !empty($arrParam['autoInvoice']) && is_array($arrParam['autoInvoice']))
            $arrParam['autoInvoice'] = json_encode($arrParam['autoInvoice']);

        // additional cost, kalo tdk ad penerima, default samakan dengan plannerkey
        for ($i = 0; $i < count($arrParam['hidAdditionalKey']); $i++) {
            if (empty($arrParam['hidDetailEmployeeKey'][$i])) {
                $arrParam['hidDetailEmployeeKey'][$i] = $arrParam['hidPlannerKey'];
            }
        }

        // harus sebelum recount, karena ambil ulang harga utk item yg sudah diinvoicce

        if (!empty($rsHeader)) {

            // utk status tertentu, beberapa field di unset

            if ($rsHeader[0]['statuskey'] != 1) {

                unset($this->arrData['code']);
                unset($this->arrData['trdate']);
                unset($this->arrData['contractkey']);
                unset($this->arrData['tarifflastmodifiedon']);
                unset($this->arrData['warehousekey']);
                unset($this->arrData['saleskey']);
            }


       /*     $rsInvoice = $this->getInvoiceInformation($pkey);
            if (!empty($rsInvoice)) {
                unset($this->arrData['customerkey']);
            }*/


            // ====== UPDATE QTY YG SUDAH DIINVOICE 

            // ITEM
            $rsDetail = $this->getDetailById($pkey);
            $arrDetailKey = $arrParam['hidDetailKey'];

            // ====== UPDATE QTY YG SUDAH DIINVOICE  
            $rsDetailKeyIndex = array_column($rsDetail, null, 'pkey');
            for ($i = 0; $i < count($arrDetailKey); $i++) {
                $detailkey = $arrDetailKey[$i];
                $qtyInvoiced = (isset($rsDetailKeyIndex[$detailkey])) ? $rsDetailKeyIndex[$detailkey]['qtyinvoiced'] : 0;

                // kalo sdh pernah diinvoiced, harga ambil ulang dr sistem
                if ($qtyInvoiced > 0) {
                    $arrParam['price'][$i] = $rsDetailKeyIndex[$detailkey]['priceinunit'];
                    $arrParam['hidItemKey'][$i] = $rsDetailKeyIndex[$detailkey]['itemkey'];
                }
            }


            // ====== RESTORE ULANG SEMUA DETAIL YG SUDAH DIINVOICE YG KEDELETE 
            for ($i = 0; $i < count($rsDetail); $i++) {
                $qtyInvoiced = (isset($rsDetail[$i])) ? $rsDetail[$i]['qtyinvoiced'] : 0;
                if ($qtyInvoiced > 0 && !in_array($rsDetail[$i]['pkey'], $arrDetailKey)) {  // Cek utk detailkey ada yg gk ad di table DB gk
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
            $rsDetail = $this->getSellingCostDetail($pkey);
            $arrDetailKey = $arrParam['hidDetailCostKey'];


            // ====== UPDATE QTY YG SUDAH DIINVOICE  
            $rsDetailKeyIndex = array_column($rsDetail, null, 'pkey');
            for ($i = 0; $i < count($arrDetailKey); $i++) {
                $detailkey = $arrDetailKey[$i];

                $qtyInvoiced = (isset($rsDetailKeyIndex[$detailkey])) ? $rsDetailKeyIndex[$detailkey]['qtyinvoiced'] : 0;

                // kalo sdh pernah diinvoiced, harga ambil ulang dr sistem
                if ($qtyInvoiced > 0) {
                    $arrParam['priceCost'][$i] = $rsDetailKeyIndex[$detailkey]['price'];
                    $arrParam['hidItemKeyCost'][$i] = $rsDetailKeyIndex[$detailkey]['costkey'];
                }
            }

            // ====== RESTORE ULANG SEMUA DETAIL YG SUDAH DIINVOICE YG KEDELETE
            for ($i = 0; $i < count($rsDetail); $i++) {
                $qtyInvoiced = (isset($rsDetail[$i])) ? $rsDetail[$i]['qtyinvoiced'] : 0;
                if ($qtyInvoiced > 0 && !in_array($rsDetail[$i]['pkey'], $arrDetailKey)) {  // Cek utk detailkey ada yg gk ad di table DB gk
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
            if ($rsHeader[0]['statuskey'] >= 2) {
                $rsSalesHeaderCost = $this->getHeaderCost($pkey, ' and ' . $this->tableHeaderCost . '.refcashoutkey <> 0');

                $arrHeaderCostKey = $arrParam['hidAdditionalKey'];
                $this->retrieveReadonlyDataRow($arrParam, $rsSalesHeaderCost, $this->arrHeaderCost, 'refcashoutkey', 'hidAdditionalKey');
            }
        }

        // hanya berlaku jika ad harga kontrak
        // harusnya hanya kepanggil kalo add atau statuskey = 1  
        if (empty($rsHeader) || $rsHeader[0]['statuskey'] == 1) {
            if (isset($arrParam['hidContractKey']) && !empty($arrParam['hidContractKey'])) {
                $security = new Security();
                $overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject, 10);

                $rsContract = $hospitalSellingRate->getDataRowById($arrParam['hidContractKey']);
                $arrParam = $this->checkContract($arrParam, $rsContract);

                // hitung ulang subtotal  
                if (empty($rsContract))
                    $modifieddate = '0000-00-00';
                else
                    $modifieddate = (!empty($rsContract[0]['modifiedon'])) ? $rsContract[0]['modifiedon'] : $rsContract[0]['createdon'];
                $arrParam['hidContractLastModifiedOn'] = $modifieddate;



                $rsDetail = array();
                if (isset($arrParam['hidId']) && !empty($arrParam['hidId'])) {
                    $rsDetail = $this->getDetailById($arrParam['hidId']);
                    $rsDetail = array_column($rsDetail, null, 'pkey');
                }

                $rsContractDetail = $hospitalSellingRate->getDetailById($arrParam['hidContractKey']);
                $rsContractDetail = array_column($rsContractDetail, null, 'itemkey');

                $arrItemKey = $arrParam['hidItemKey'];
                for ($i = 0; $i < count($arrItemKey); $i++) {
                    $itemkey = $arrItemKey[$i];

                    $arrParam['contractPrice'][$i] = 0;

                    if ($overwriteContractAllowed) {
                        // kalo punya akses, overwrite
                        $arrParam['contractPrice'][$i] = $arrParam['price'][$i];
                    } else {

                        // kalo gk punya akses,selalu ambil dr kontrak, kecuali sudah pernah diudapte oleh yg punya akses
                        // harusnya hanya terjadi ketika edit

                        // kalo pernah diupdate dr yg punya akses, tetep pake harga yg diupdate
                        $detailkey = $arrParam['hidDetailKey'][$i];
                        if (isset($rsDetail[$detailkey]) &&  $rsDetail[$detailkey]['contractpriceinunit'] > 0) {
                            // harga tetep patokan dr yg supervisor 
                            $arrParam['price'][$i] = $rsDetail[$detailkey]['contractpriceinunit'];
                            $arrParam['contractPrice'][$i] = $arrParam['price'][$i];
                        } else {
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


        // INI PERLU DICEK ULANG KALO PAKE API GK KIRIM priceHeaderCost
        // realisasi biaya
        if ($this->useRealization())
            // priceCost gk boleh diganti, karena nanti ad update dr realisasi
            unset($this->arrHeaderCost['amount']);
        else if (isset($arrParam['requestPriceHeaderCost']))
            // kalo gk pake realisasi, copy semua
            $arrParam['priceHeaderCost'] = $arrParam['requestPriceHeaderCost'];


        // RECALCULATE   
        $arrItemKey = array();
        if (isset($arrParam['hidItemKey'])) {
            $arrItemKey = $arrParam['hidItemKey'];
            $arrQty = $arrParam['qty'];
            $arrPrice = $arrParam['price'];

            for ($i = 0; $i < count($arrItemKey); $i++) {

                $qty = (empty($arrQty[$i])) ? 1 : $this->unformatNumber($arrQty[$i]);
                $priceInUnit =  $this->unFormatNumber($arrPrice[$i]);
                $subtotal = $priceInUnit;
                $total = $qty * $subtotal;

                $arrParam['qty'][$i] = $qty;
                $arrParam['numberkey'][$i] = ($i + 1);
                $arrParam['totalDetails'][$i] =  $total;
            }
        }

        if (isset($arrParam['hidItemKeyCost'])) {
            $arrItemCost =  $arrParam['hidItemKeyCost'];
            $arrCostQty = $arrParam['qtyCost'];
            $arrCostPrice = $arrParam['priceCost'];

            for ($i = 0; $i < count($arrItemCost); $i++) {
                $qty = (empty($arrCostQty[$i])) ? 1 : $this->unformatNumber($arrCostQty[$i]);
                $price = $this->unformatNumber($arrCostPrice[$i]);
                $arrParam['qtyCost'][$i] = $qty;
                $arrParam['subtotalCost'][$i] = $qty * $price;
            }
        }

        if (isset($arrParam['hidItemKeyHeaderCost'])) {
            $arrItemHeaderCost =  $arrParam['hidItemKeyHeaderCost'];
            $arrItemHeaderCostQty = $arrParam['qtyHeaderCost'];

            $arrItemHeaderRequestAmount = $arrParam['requestPriceHeaderCost'];

            $arrLocationCriteria = array();
            $arrLocationCriteria['terminalkey'] = (isset($arrParam['hidTerminalKey']) && !empty($arrParam['hidTerminalKey'])) ? $arrParam['hidTerminalKey'] : '';
            $arrLocationCriteria['depotkey'] = (isset($arrParam['hidDepotKey']) && !empty($arrParam['hidDepotKey'])) ? $arrParam['hidDepotKey'] : '';
            $arrLocationCriteria['jobcategorykey'] = (isset($arrParam['hidCategoryKey']) && !empty($arrParam['hidCategoryKey'])) ? $arrParam['hidCategoryKey'] : '';


            for ($i = 0; $i < count($arrItemHeaderCost); $i++) {
                $requestAmount = $this->unformatNumber($arrItemHeaderRequestAmount[$i]);
                if ($requestAmount == 0) {
                    // set criteria service dan qty
                    $arrLocationCriteria['servicedetail'] = array();

                    for ($j = 0; $j < count($arrItemKey); $j++) {
                        $qty = (empty($arrQty[$j])) ? 1 : $this->unformatNumber($arrQty[$j]);
                        array_push($arrLocationCriteria['servicedetail'], array('qty' => $qty, 'servicekey' => $arrItemKey[$j]));
                    }

                    $rsTruckingCost = $item->getTruckingCostDefaultPrice($arrItemHeaderCost[$i], $arrLocationCriteria);
                    $arrParam['requestPriceHeaderCost'][$i] = $rsTruckingCost['amount'];
                };

                $qty = (empty($arrItemHeaderCostQty[$i])) ? 1 : $arrItemHeaderCostQty[$i];
                $priceInUnit = $this->getValidHeaderCost($arrParam, $i);

                $total = $qty * $priceInUnit;

                $arrParam['qtyHeaderCost'][$i] = $qty;
                $arrParam['subtotalHeaderCost'][$i] =  $total;
            }
        }

        if (isset($arrParam['hidContactPersonDetailKey'])) {
            for ($i = 0; $i < count($arrParam['hidContactPersonDetailKey']); $i++)
                $arrParam['reftable'][$i] = $this->tableName;
        }

        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }

    function getValidHeaderCost($arrParam, $index)
    {


        $arrItemRequestHeaderCostPrice = (isset($arrParam['requestPriceHeaderCost'][$index]) && !empty($arrParam['requestPriceHeaderCost'][$index])) ? $arrParam['requestPriceHeaderCost'][$index] : 0;
        //$arrItemHeaderCostPrice = (isset($arrParam['priceHeaderCost'][$index]) && !empty($arrParam['priceHeaderCost'][$index])) ? $arrParam['priceHeaderCost'][$index] : 0;

        $arrItemRequestHeaderCostPrice =  $this->unFormatNumber($arrItemRequestHeaderCostPrice);
        //$arrItemHeaderCostPrice =  $this->unFormatNumber($arrItemHeaderCostPrice);


        // gk boleh 0, karena ad kemungkinan hasil realisasinya 0  
        // $priceInUnit  = ( $arrItemHeaderCostPrice > 0 ) ? $arrItemHeaderCostPrice :  $arrItemRequestHeaderCostPrice;  

        // kalo row baru, pasti balikin nilai request nya (gk mungkin sudah ad realisasi) 
        // kalo awalnya gk ad biaya, terus di realisasi ad biaya tambahan, harus dicek

        //if(empty($arrParam['requestPriceHeaderCost'][$index])){
        if (empty($arrParam['hidAdditionalKey'][$index])) {
            $priceInUnit = $arrItemRequestHeaderCostPrice;
        } else {
            // query dr table berdasarkan pkey nya
            $detailkey = $arrParam['hidAdditionalKey'][$index];
            $sql = 'select isrealization,amount from ' . $this->tableHeaderCost . ' where pkey = ' . $this->oDbCon->paramString($detailkey);
            $rs =  $this->oDbCon->doQuery($sql);
            $priceInUnit = (empty($rs) || $rs[0]['isrealization'] == 0)    ? $arrItemRequestHeaderCostPrice :  $rs[0]['amount'];
        }

        return $priceInUnit;
    }

    function checkContract($arrParam, $rsContract)
    {
        if (empty($rsContract)) return $arrParam;

        $security = new Security();
        $overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject, 10);

        $customer = new Customer();
        $hospitalSellingRate = new HospitalSellingRate();

        if (!$overwriteContractAllowed) {
  

            $rsConsignee = array();

            $rsContract = $hospitalSellingRate->getDataRowById($rsContract[0]['pkey']);
        }

        return $arrParam;
    }

    function getItemFile($pkey)
    {
        $sql = 'select * from ' . $this->tableFile . ' where refkey = ' . $this->oDbCon->paramString($pkey) . ' order by pkey asc';
        return $this->oDbCon->doQuery($sql);
    }

    function updateFile($pkey, $token, $arrFile)
    {

        if (!empty($arrFile))
            $this->validateDiskUsage();

        $sourcePath = $this->uploadTempDoc . $this->uploadFileFolder . $token;
        $destinationPath = $this->defaultDocUploadPath . $this->uploadFileFolder;

        if (!is_dir($destinationPath))
            mkdir($destinationPath,  0755, true);

        $destinationPath .= $pkey;


        //delete previous files	    
        $this->deleteAll($destinationPath);
        $sql = 'delete from ' . $this->tableFile . ' where refkey = ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);


        if (!is_dir($sourcePath))
            return;

        if (!empty($arrFile)) {
            $arrFile = explode(",", $arrFile);
            for ($i = 0; $i < count($arrFile); $i++) {
                $this->uploadImage($sourcePath, $destinationPath, $arrFile[$i]);

                $imagekey = $this->getNextKey($this->tableFile);

                $sql = 'insert into ' . $this->tableFile . ' (pkey,refkey,file) values (' . $this->oDbCon->paramString($imagekey) . ',' . $this->oDbCon->paramString($pkey) . ',' . $this->oDbCon->paramString($arrFile[$i]) . ')';
                $this->oDbCon->execute($sql);
            }
        }
    }

    function delete($id, $forceDelete = false, $reason = '')
    {

        $arrayToJs =  array();
        // tdk bisa didelete utk transaksi, tp ubah ke cancel
        if (isset($this->tableNameDetail) && !empty($this->tableNameDetail)) {
            $arrayToJs = $this->changeStatus($id, 7, $reason, false, $forceDelete);
            return $arrayToJs;
        }

        try {

            $arrayToJs = $this->validateDelete($id);
            if (!empty($arrayToJs))
                return $arrayToJs;

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            $sql = 'delete from  ' . $this->tableName . ' where pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);

            $this->setTransactionLog(DELETE_DATA, $id);

            $this->oDbCon->endTrans();

            $this->addErrorList($arrayToJs, true, $this->lang['dataHasBeenSuccessfullyUpdated']);
        } catch (Exception $e) {
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs, false, $e->getMessage());
        }

        return $arrayToJs;
    }

    function updateAmountInvoiced($pkey)
    {

        // gk perlu udpate customer AR outstanding karena sudah diupdate di ketika terbentuk AR

        $hospitalServiceOrderInvoice = new HospitalServiceOrderInvoice();

        $sql = 'update ' . $this->tableName . ' set totalinvoiced = (
                    select coalesce(sum(' . $hospitalServiceOrderInvoice->tableNameDetail . '.amount),0) as amount
                    from
                        ' . $hospitalServiceOrderInvoice->tableNameDetail . ',
                        ' . $hospitalServiceOrderInvoice->tableName . '
                    where
                        ' . $hospitalServiceOrderInvoice->tableName . '.statuskey in (2,3) and 
                        ' . $hospitalServiceOrderInvoice->tableName . '.pkey =  ' . $hospitalServiceOrderInvoice->tableNameDetail . '.refkey and
                        ' . $hospitalServiceOrderInvoice->tableNameDetail . '.salesorderkey = ' . $this->oDbCon->paramString($pkey) . '
                ) where pkey = ' . $this->oDbCon->paramString($pkey);

        $this->oDbCon->execute($sql);
    }

    function updateQtyInvoiced($pkey, $isValidated = false)
    {
        $rsHeader = $this->getDataRowById($pkey);

        $arrayToJs = array();

        $rsItemDetail = $this->getDetailById($pkey);
        $rsHeaderCost = $this->getSellingCostDetail($pkey);

        // update setiap SO, sudah brp qty yg ditagih, item dan cost 
        try {

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);


            for ($j = 0; $j < count($rsItemDetail); $j++) {
                if (!$isValidated)
                    $totalInvoiced = $this->getTotalQtyInvoiced($pkey, $rsItemDetail[$j]['pkey'], $rsItemDetail[$j]['itemkey']);
                else
                    $totalInvoiced = $rsItemDetail[$j]['qtyinbaseunit'];


                $sql = 'update 
                            ' . $this->tableNameDetail . '
                        set 
                            qtyinvoiced = ' . $this->oDbCon->paramString($totalInvoiced) . ' 
                        where  
                            pkey = ' . $this->oDbCon->paramString($rsItemDetail[$j]['pkey']) . ' 
                        ';

                $this->oDbCon->execute($sql);
            }

            for ($j = 0; $j < count($rsHeaderCost); $j++) {

                if (!$isValidated)
                    $totalInvoiced = $this->getTotalQtyInvoiced($pkey, $rsHeaderCost[$j]['pkey'], $rsHeaderCost[$j]['costkey']);
                else
                    $totalInvoiced = $rsHeaderCost[$j]['qty'];

                $sql = 'update 
                            ' . $this->tableSellingCost . '
                        set 
                            qtyinvoiced = ' . $this->oDbCon->paramString($totalInvoiced) . ' 
                        where  
                            pkey = ' . $this->oDbCon->paramString($rsHeaderCost[$j]['pkey']) . ' 
                        ';

                $this->oDbCon->execute($sql);
            }

            $this->oDbCon->endTrans();
        } catch (Exception $e) {
            $this->oDbCon->rollback();
            /*  
            if (!empty($e->getMessage()))
                $this->addErrorLog(false,$e->getMessage()); */
        }



        // cek utk SO, semua sudah tertagih atau blm. lalu ubah status 
        $sql = 'SELECT * from ( 
                    select  pkey, itemkey from   ' . $this->tableNameDetail . '  where  refkey = ' . $this->oDbCon->paramString($pkey) . ' and  qtyinbaseunit > qtyinvoiced UNION 
                    select  pkey, costkey as itemkey from   ' . $this->tableSellingCost . '  where  refkey = ' . $this->oDbCon->paramString($pkey) . ' and  qty > qtyinvoiced 
                ) trans ';

        $rs =  $this->oDbCon->doQuery($sql);

        if (empty($rs)) {
            if ($rsHeader[0]['statuskey'] <> 6)
                $arrayToJs = $this->changeStatus($pkey, 6, '', false, true);
        } else {
            if ($rsHeader[0]['statuskey'] == 6)
                $arrayToJs = $this->changeStatus($pkey, 5, '', false, true);
        }
        
        
        return $arrayToJs;
    }


    function getTotalQtyInvoiced($pkey, $detailkey, $itemkey)
    {
        // tambahkan paramter itemkey untuk membedakan dr detail atau selling cost
        // dengan ada item key sudah pasti beda karena detail item dan item cost 1 table, jd pkey pasti beda
        // kenapa $itemkeyny jd gk kepake ??

        $hospitalServiceOrderInvoice = new HospitalServiceOrderInvoice();

        // update setiap SO, sudah brp qty yg ditagih, item dan cost
        $sql = 'select 
                        coalesce(sum(qtyinbaseunit),0) as totalinvoiced
                    from  
                        ' . $hospitalServiceOrderInvoice->tableName . ',  
                        ' . $hospitalServiceOrderInvoice->tableNameDetail . ',
                        ' . $hospitalServiceOrderInvoice->tableNameItemDetail . ' 
                    where 
                        ' . $hospitalServiceOrderInvoice->tableName . '.pkey = ' . $hospitalServiceOrderInvoice->tableNameDetail . '.refkey and
                        ' . $hospitalServiceOrderInvoice->tableNameDetail . '.pkey = ' . $hospitalServiceOrderInvoice->tableNameItemDetail . '.refkey and
                        ' . $hospitalServiceOrderInvoice->tableName . '.statuskey in (2,3) and
                        ' . $hospitalServiceOrderInvoice->tableNameDetail . '.salesorderkey = ' . $this->oDbCon->paramString($pkey) . ' and
                        ' . $hospitalServiceOrderInvoice->tableNameItemDetail . '.refsodetailkey = ' . $this->oDbCon->paramString($detailkey) . ' and
                        ' . $hospitalServiceOrderInvoice->tableNameItemDetail . '.itemkey =  ' . $this->oDbCon->paramString($itemkey) . ' 
                    ';

        $rsTotal = $this->oDbCon->doQuery($sql);

        return $rsTotal[0]['totalinvoiced'];
    }

    function manipulateParamFromApi($arrParam)
    {
        $arrParam['priceHeaderCost'] = array_fill(0, count($arrParam['priceHeaderCost']), '');
        return $arrParam;
    }

    function afterUpdateData($arrParam, $action)
    {
        $customer = new Customer();

        // khusus kalo edit
        if (isset($arrParam['hidId']) && !empty($arrParam['hidId'])) {

            $pkey = $arrParam['hidId'];
            $rs = $this->getDataRowById($pkey);

            // CASH OUT   
            $this->updateTruckingCostCashOut($pkey); 

            // cuma boleh di status proses SPK, di menungggu gk perlu
            if ($rs[0]['statuskey'] == 2) {
                // perlu tambah add cost rate jg  
                $this->updateCostRate($arrParam['hidId']);
                $this->autoAddWorkOrder($arrParam['hidId']);    

            }
        }

        // harusnya cuma dr API 

        if (isset($arrParam['_mnv-api']) && $arrParam['_mnv-api'] == 1) {

            $arrayToJs = array();

            if (isset($arrParam['changestatusto'])) {
                //kalo otomatis ganti status
                $newStatus = $arrParam['changestatusto'];

                // kalo lebih besar dari konfirmasi, konfirmasi dulu
                if (in_array($newStatus, array(2, 3, 4, 5))) {

                    // iterasi satu2 saja, karena tanggung 2 dan 3 gk boleh diskip
                    // cari dulu status skrg, jaga2 kalo dr edit
                    $pkey = $arrParam['pkey'];
                    $rs = $this->getDataRowById($pkey);
                    $startStatus = $rs[0]['statuskey'] + 1;

                    for ($i = $startStatus; $i <= $newStatus; $i++) {
                        $response = $this->changeStatus($pkey, $i);
                        if ($response[0]['valid'] <> 1) {
                            $response[0]['message'] = $i . '=>' . $response[0]['message'];
                            array_push($arrayToJs, $response[0]);
                        }
                    }
                }
            }


            return $arrayToJs; // sementara di return hanya API saja dulu

        }

        $customer->updateAROutstanding($arrParam['hidCustomerKey']);
    }

    function changeStatus($id, $status, $reason = '', $copy = false, $autoChangeStatus = false, $ignoreValidation = false)
    {
        if (empty($_SESSION[$this->loginAdminSession]['id']))
            die;

        $rsHeader = $this->getDataRowById($id);

        try {
            if (!$autoChangeStatus) {
                $security = new Security();
                $coba = $security->isAdminLogin($this->securityObject, $status, false);
                if (!$security->isAdminLogin($this->securityObject, $status, false))
                    $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[252], true);
            }

            // jika status bkn status sendiri dan bukan status terakhir (status cancel)  

            if ($rsHeader[0]['statuskey'] == count($this->getAllStatus()))
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[221], true);

            if ($rsHeader[0]['statuskey'] == $status)
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[224], true);
        } catch (Exception $e) {
            return $this->getErrorLog();
            //$this->addErrorList($arrayToJs,false,$e->getMessage());
        }


        try {

            // ================== VALIDATION

            //$this->resetErrorLog();

            switch ($status) {
                case 1:
                    $this->validateInput($rsHeader);
                    break;
                case 2:
                    if ($rsHeader[0]['statuskey'] < $status)
                        $this->validateConfirm($rsHeader, $autoChangeStatus);
                    else
                        $this->validateBackConfirm($rsHeader);
                    break;
                case 3:
                    $this->validateSPKCompleted($rsHeader, $autoChangeStatus);
                    break;
                case 4:
                    $this->validateReadyToCheck($rsHeader, $autoChangeStatus);
                    break;
                case 5:
                    $this->validateReadyToInvoice($rsHeader, $autoChangeStatus);
                    break;
                case 6:
                    $this->validateInvoiced($rsHeader, $autoChangeStatus);
                    break;
                case 7:
                    $this->validateCancel($rsHeader, $autoChangeStatus);
                    break;
            }

            //make sure we throw error 
            $this->throwIfHasErrorLog();

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);


            switch ($status) {
                case 2:
                    if ($rsHeader[0]['statuskey'] < $status) {
                        $this->confirmTrans($rsHeader);
                        $this->afterConfirmTrans($rsHeader);
                    } else {
                        $this->backConfirmTrans($rsHeader);
                        $this->afterBackConfirmTrans($rsHeader);
                    }
                    break;
                case 7:
                    $this->cancelTrans($rsHeader, $copy);
                    $this->afterCancelTrans($rsHeader);
                    break;
            }

            $sql = 'update ' . $this->tableName . ' set statuskey = ' . $this->oDbCon->paramString($status) . ' where pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);

            $rsStatus = $this->getStatusById($status);
            $this->setTransactionLog($rsStatus[0]['pkey'], $id, '', $reason);

            $this->afterStatusChanged($rsHeader);

            $this->oDbCon->endTrans();


            $this->addErrorLog(true, $this->lang['dataHasBeenSuccessfullyUpdated']);
        } catch (Exception $e) {
            $this->oDbCon->rollback();

            if (!empty($e->getMessage()))
                $this->addErrorLog(false, $e->getMessage());
            //$this->addErrorList($arrayToJs,false,$e->getMessage());
        }


        return $this->getErrorLog();
    }

    function validateBackConfirm($rsHeader)
    {
        if ($rsHeader[0]['statuskey'] >= 6)
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201]);
    }

    function validateConfirm($rsHeader, $autoChangeStatus = false)
    {

        if ($autoChangeStatus)
            return;

        //validasi creditlimit

        $customer = new Customer();
        $security = new Security();

        $customerkey = $rsHeader[0]['customerkey'];
        $rsCustomer = $customer->getDataRowById($customerkey);

        if ($rsCustomer[0]['creditlimit'] > 0) {
            $hasCreditLimitAccess = $security->isAdminLogin($customer->creditLimitSecurityObject, 10);
            $total = $this->unFormatNumber($rsHeader[0]['grandtotal']);
            if (!$hasCreditLimitAccess && $customer->willExceedCreditLimit($customerkey, $total)) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['creditlimit'][1]);
            }
        }

        $security = new Security();
        $overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject, 10);

        $hospitalSellingRate = new HospitalSellingRate();

         if (!$overwriteContractAllowed) {
             $rsTariff = $hospitalSellingRate->getDataRowById($rsHeader[0]['contractkey']);
             $timestamp = (empty($rsTariff[0]['modifiedon'])) ? $rsTariff[0]['createdon'] : $rsTariff[0]['modifiedon'];

             if ($timestamp <> $rsHeader[0]['tarifflastmodifiedon'])
                 $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['sellingRate'][4] .  ' ' . $this->lang['pleaseReopenAndSaveTheData']);
         }

        $costRateIsMandatory = $this->loadSetting('costRateIsMandatory');
        if ($costRateIsMandatory == 1) {
            $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
            $return = $this->validateFixedCostMustExist($rsHeader[0], array_column($rsDetail, 'itemkey'));
        }

        foreach ($return as $row)
            $this->addErrorLog(false, $row['message']);
    }

    function validateFixedCostMustExist($header, $arrServiceKey)
    {

        // header : code,warehousekey,categorykey,stuffinglocationkey,cargotypekey,consigneekey

        $truckingServiceOrderCategory = new TruckingServiceOrderCategory();
        $costRate = new CostRate();
        $truckingService = new Service();
        $truckingCost = new Service(TRUCKING_SERVICE, 1);

        $arrayToJs = array();

        // kalo wajib ad harga

        $rsHeader = $this->getDataRowById($pkey);
        //$rsDetail = $this->getDetailById($pkey);

        $rsJobType = $truckingServiceOrderCategory->getDetailById($header['categorykey']);
        $warehousekey = $header['warehousekey'];

        //$arrServiceKey = array_column($rsDetail,'itemkey');        

        // cari nama item
        $rsServices = $truckingService->searchDataRow(
            array($truckingService->tableName . '.pkey', $truckingService->tableName . '.name'),
            ' and ' . $truckingService->tableName . '.pkey in (' . $this->oDbCon->paramString($arrServiceKey, ',') . ')'
        );
        $arrServices = array_column($rsServices, 'name', 'pkey');


        // cari dulu semua item fixed cost 
        $rsFixedCost = $truckingCost->searchDataRow(
            array($truckingCost->tableName . '.pkey', $truckingCost->tableName . '.name'),
            ' and ' . $truckingCost->tableName . '.fixedcost = 1 and ' . $truckingCost->tableName . '.statuskey = 1'
        );
        $arrFixedCost = array_column($rsFixedCost, 'name', 'pkey');

        // cek setiap item ad gk daftar biayanya

        foreach ($arrServiceKey as $servicekey) { // utk setiap mobil, 20', 40'
            foreach ($rsJobType as $jobType) {   // utk setiap jenis pekerjaan

                // ambil biaya sesuai jenis pekerjaan
                $rsCostRate = $costRate->getCostDetail($warehousekey, $header['stuffinglocationkey'], $header['cargotypekey'], $jobType['jobtypekey'], $servicekey, 0, $header['consigneekey']);
                if (empty($rsCostRate)) {
                    $this->addErrorList($arrayToJs, false, '<strong>' . $header['code'] . '</strong>. ' . $arrServices[$servicekey] . ', ' . $this->errorMsg['costRate'][1], true);
                } else {
                    $registeredCostKey = array_column($rsCostRate, 'costkey');
                    foreach ($arrFixedCost as $fixedCostKey => $fixedCostItem) {
                        if (!in_array($fixedCostKey, $registeredCostKey))
                            $this->addErrorList($arrayToJs, false, '<strong>' . $header['code'] . '</strong>. ' . $arrServices[$servicekey] . ' - ' . $fixedCostItem . ', ' . $this->errorMsg['costRate'][1], true);
                    }
                }
            }
        }

        return $arrayToJs;
    }

    function confirmTrans($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];

        $rsDetail = $this->getDetailById($id);
        $rsSalesHeaderCost = $this->getHeaderCost($rsHeader[0]['pkey'], ' and ' . $this->tableHeaderCost . '.realizationkey = 0');

        $this->updateCostRate('',$rsHeader,$rsDetail);  
        $this->autoAddWorkOrder('', $rsHeader, $rsDetail);
        $this->addCashOut($rsHeader,$rsSalesHeaderCost); 
    }

    function updateCostRate($id = '', $rsHeader = '', $rsDetail = '')
    {

        $truckingServiceOrderCategory = new TruckingServiceOrderCategory();
        $costRate = new CostRate();
        $service = new Service();

        if (!empty($id)) {
            $rsHeader = $this->getDataRowById($id);
            $rsDetail = $this->getDetailById($id);
        }

        $id = $rsHeader[0]['pkey'];
        $warehousekey = $rsHeader[0]['warehousekey'];


        // select service yg blm ad di table cost saja...

        //$arrService = $service->searchData('', '', true, ' and '.$service->tableName.'.statuskey = 1 order by '.$service->tableName.'.name asc');
        $arrServiceKey = array_column($rsDetail, 'itemkey');

        $existingRate = $this->getCostDetail($id);
        $existingRate = array_column($existingRate, 'itemkey');


        //UPDATE COST
        $rsJobType = $truckingServiceOrderCategory->getDetailById($rsHeader[0]['categorykey']);

        foreach ($arrServiceKey as $servicekey) {
            // kalo sudah ad rate nya, gk perlu add lg
            if (in_array($servicekey, $existingRate)) continue;

            for ($j = 0; $j < count($rsJobType); $j++) {

                // ambil biaya sesuai jenis pekerjaan
                $rsCostRate = $costRate->getCostDetail($warehousekey, $rsHeader[0]['stuffinglocationkey'], $rsHeader[0]['cargotypekey'], $rsJobType[$j]['jobtypekey'], $servicekey, 0, $rsHeader[0]['consigneekey']);

                // klao ad nilai kosong, throe    
                if (empty($rsCostRate)) continue;

                for ($ctr = 0; $ctr < count($rsCostRate); $ctr++) {
                    $costkey = $rsCostRate[$ctr]['costkey'];
                    $cost = $rsCostRate[$ctr]['price'];

                    $sql = 'insert into ' . $this->tableCost . ' (
                            refkey, 
                            jobtypekey,
                            costkey,
                            itemkey,
                            price
                         ) values ( 
                            ' . $this->oDbCon->paramString($id) . ', 
                            ' . $this->oDbCon->paramString($rsCostRate[$ctr]['jobtypekey']) . ',
                            ' . $this->oDbCon->paramString($costkey) . ',
                            ' . $this->oDbCon->paramString($rsCostRate[$ctr]['itemkey']) . ',
                            ' . $this->oDbCon->paramString($this->unFormatNumber($cost)) . ' 
                        )';

                    $this->oDbCon->execute($sql);
                }
            }
        }
    }

    function autoAddWorkOrder($id = '', $rsHeader = '', $rsDetail = '')
    {

        // kalo gk ad SPK, skip aj,
        // gk bisa disamakan dengan truckingType jg

        $useSPK = $this->loadSetting('useSPK');
        if ($useSPK == 2) return;

        $truckingType = $this->loadSetting('truckingType');
        $spkDateBasedOn = $this->loadSetting('spkDateBasedOn');

//        $truckingServiceOrderCategory = new TruckingServiceOrderCategory();
        $hospitalWorkOrder = new HospitalWorkOrder();
        $item = new Item();
        $service = new Service(SERVICE,1);

        if (!empty($id)) {
            $rsHeader = $this->getDataRowById($id);
            $rsDetail = $this->getDetailById($id);
        }


        $id = $rsHeader[0]['pkey'];

        $user = base64_decode($_SESSION[$this->loginAdminSession]['id']);

        // jml SPK setiap detail tergantung brp byk step progress. 
        // $rsJobType = $truckingServiceOrderCategory->getDetailWithRelatedInformation($id);
        // $rsJobType = $truckingServiceOrderCategory->getDetailWithRelatedInformation($rsHeader[0]['categorykey']);


        // =====  hapus semua SPK yang sudah gk ad di JO dan status SPK masih MENUNGGU
        // ini perlu VALIDASI

        if ($rsHeader[0]['statuskey'] == 2) {

            $rsWO = $hospitalWorkOrder->searchDataRow(
                array(
                    $hospitalWorkOrder->tableName . '.pkey',
                    $hospitalWorkOrder->tableName . '.refdetailkey',
                    $hospitalWorkOrder->tableName . '.itemkey'
                ),
                '   and ' . $hospitalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($id) . '
                                                                    and ' . $hospitalWorkOrder->tableName . '.statuskey = ' . $this->oDbCon->paramString(TRANSACTION_STATUS['menunggu'])
            );


            foreach ($rsWO as $workOrder) {
                // jika detail dan itemnya sudah gk sama 
                foreach ($rsDetail as $detailRow) {
                    if ($workOrder['refdetailkey'] == $detailRow['pkey'] && $workOrder['itemkey'] != $detailRow['itemkey']) {
                        $arrayToJs = $hospitalWorkOrder->changeStatus($workOrder['pkey'], TRANSACTION_STATUS['batal'], '', false, true);
                        if (!$arrayToJs[0]['valid'])
                            throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' .  $arrayToJs[0]['message']);
                    }
                }
            }
        }

        $arrItemKey = array_column($rsDetail,'itemkey');
        
    
        $rsServices = $service->searchDataRow(array($service->tableName.'.pkey', $service->tableName.'.isworkorder'),
										 ' and '.$service->tableName.'.pkey in ('.$this->oDbCon->paramString($arrItemKey,',').')
                                         and ' . $service->tableName . '.statuskey = 1'
										 );
        
        $arrServices = array_column($rsServices,null,'pkey');
        $rsServices = array_column($rsServices,'pkey');

       
        
        $rsItem = array_column($rsItem,'pkey');    
        // =====  hapus semua SPK yang sudah gk ad di JO dan status SPK masih MENUNGGU 


            // kalo SPK sudah ad satu saja, utk layanan yg sama, continue....

            if ($rsHeader[0]['statuskey'] == 2) {

                //$rsWODetail = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey',$id,true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')' );
                $rsWODetail = $hospitalWorkOrder->searchDataRow(
                    array($hospitalWorkOrder->tableName . '.itemkey'),
                    '   and ' . $hospitalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($id) . '
                                                                        and ' . $hospitalWorkOrder->tableName . '.statuskey in  (' . TRANSACTION_STATUS['menunggu'] . ',' . TRANSACTION_STATUS['konfirmasi'] . ',' . TRANSACTION_STATUS['selesai'] . ')'
                );


                foreach($rsWO as $workOrder){   
                    // jika detail dan itemnya sudah gk sama 
                    foreach($rsDetail as $detailRow) { 
                        if ($workOrder['refdetailkey'] == $detailRow['pkey'] && $workOrder['itemkey'] != $detailRow['itemkey'] ){ 
                            $arrayToJs = $hospitalWorkOrder->changeStatus($workOrder['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
                            if (!$arrayToJs[0]['valid'])
                                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
                        } 
                    } 
                }
            }
        for ($i = 0; $i < count($rsDetail); $i++) {
            $itemkey = $rsDetail[$i]['itemkey'];
            if ($arrServices[$itemkey]['isworkorder'] == 1) {

            if ( $rsHeader[0]['statuskey'] == 2){ 
                
                //$rsWODetail = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey',$id,true,' and '.$truckingServiceWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')' );

                $rsWODetail = $hospitalWorkOrder->searchDataRow( array($hospitalWorkOrder->tableName.'.itemkey') , 
                                                                '   and '.$hospitalWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($id).'
                                                                    and '.$hospitalWorkOrder->tableName.'.statuskey in  ('.TRANSACTION_STATUS['menunggu'].','.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
                                                            );
           

                $rsWODetail = array_column($rsWODetail,'itemkey'); 
                if (in_array($rsDetail[$i]['itemkey'], $rsWODetail)) continue; 
            }

            $arrParam = array();

            $arrParam['hidDetailItemKey'] = array();

            //cek service nya ispacakge atau bukan
            if ($rsDetail[$i]['qty'] > 0 || in_array($rsDetail[$i]['itemkey'], $rsServices)){
            
                $rsServiceDetail = $service->getItemDetail($rsDetail[$i]['itemkey']);
                
                 $item = new Item();  

                //search untuk cek tipe item karena belum di save ke db 
                $arrItemDetailKey = array_column($rsServiceDetail,'itemkey');
                $rsItem = $item->searchDataRow(

                    array($item->tableName . '.pkey'),
                    '   and ' . $item->tableName . '.pkey in (' . $this->oDbCon->paramString(implode(', ',$arrItemDetailKey)) . ')
                    and ' . $item->tableName . '.statuskey = 1 
                    and ' . $item->tableName . '.itemtype = 1 

                    '
                );
                $rsItem = array_column($rsItem,'pkey');

                for($j=0;$j<count($rsServiceDetail);$j++){
                    
                    if(!in_array($rsServiceDetail[$j]['itemkey'],$rsItem)) continue;

                    $arrParam['hidDetailItemKey'][$j] = 0;
                    $arrParam['hidItemDetailKey'][$j] = $rsServiceDetail[$j]['itemkey'];
                    $arrParam['selUnit'][$j] = $rsServiceDetail[$j]['unitkey'];
                    $arrParam['qty'][$j] = $rsServiceDetail[$j]['qty'];

                }
            }
            

            $spkDate = ($spkDateBasedOn == 2) ?  $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y H:i') : date('d / m / Y');

            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidSOKey'] = $id;
            $arrParam['hidSODetailKey'] = $rsDetail[$i]['pkey'];
            $arrParam['hidItemKey'] = $rsDetail[$i]['itemkey'];
            $arrParam['trDate'] = $this->formatDBDate($rsDetail[$i]['trdate'], 'd / m / Y H:i');
            $arrParam['trDesc'] = $rsDetail[$i]['trdesc'];
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
            $arrParam['islinked'] = true;
            $arrParam['createdBy'] = $user;
            $arrParam['_mnv'] = true;

            //cost  
            // $rsCost = $this->getCostDetail($id, $rsJobType[$k]['jobtypekey'],  $rsDetail[$i]['itemkey'] );   

            // $arrCostKey = array();
            // $arrCost = array();
            // $arrParam['hidDetailKey'] = array();
            // $arrParam['hidRefCashOutKey'] = array();
            // for($j=0;$j<count($rsCost);$j++){   

            //     if($rsDetail[$i]['isgroup']==1) 
            //       $rsCost[$j]['price'] *= $rsDetail[$i]['qtyinbaseunit'];

            //     array_push($arrParam['hidDetailKey'], 0);
            //     array_push($arrParam['hidRefCashOutKey'], 0);

            //     array_push($arrCost,$rsCost[$j]['price']);   
            //     array_push($arrCostKey,$rsCost[$j]['costkey']);    
            // }

            // $arrParam['hidCostKey']  = $arrCostKey;
            // $arrParam['requestAmount']  = $arrCost;


            // utk model bisnis seperti logol
            $qtyWO = $rsDetail[$i]['qtyinbaseunit'];
            // if ($truckingType == 2) {
            // $qtyWO = 0; // SPK dr API
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
            // } else {
                if ($rsDetail[$i]['isgroup'] == 1)
                     $qtyWO = 1;
            // }



            for ($z = 0; $z < $qtyWO; $z++) {
                $arrayToJs = $hospitalWorkOrder->addData($arrParam);

                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
            }
            }
        }
    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {

        $hospitalWorkOrder = new HospitalWorkOrder();
         $hospitalServiceOrderInvoice = new HospitalServiceOrderInvoice();
        $hospitalCostCashOut = new HospitalCostCashOut();

        $pkey = $rsHeader[0]['pkey'];

        // if ($rsHeader[0]['statuskey'] == 7)
        //     $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201]);

        // cek SPK sudah ad yg konfirmasi / closed blm
        $rsWO = $hospitalWorkOrder->searchData('', '', true, ' and ' . $hospitalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $hospitalWorkOrder->tableName . '.statuskey in (2,3)');
        if (!empty($rsWO))
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsWO[0]['code'] . '</strong>, ' . $this->errorMsg[225]);

        $rsInvoice = $hospitalServiceOrderInvoice->searchData('','',true,' and '.$hospitalServiceOrderInvoice->tableName.'.refkey =  ' . $this->oDbCon->paramString($pkey) .' and '.$hospitalServiceOrderInvoice->tableName.'.statuskey in (2,3)');
        if (!empty($rsInvoice)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg[900].' <strong>'.$rsInvoice[0]['code'].'</strong>');
        
         $rsInvoiced = $this->getInvoiceInformation($pkey);
         if (!empty($rsInvoiced))
             $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[900] . ' <strong>' . $rsInvoiced[0]['code'] . '</strong>');

        $rsCashOutKey =  $this->getTableKeyAndObj($this->tableName, array('key'));
        $rsCashOut = $hospitalCostCashOut->searchData('', '', true, ' and ' . $hospitalCostCashOut->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $hospitalCostCashOut->tableName . '.reftabletype = ' . $this->oDbCon->paramString($rsCashOutKey['key']) . ' and ' . $hospitalCostCashOut->tableName . '.statuskey in (2,3)');
        if (!empty($rsCashOut)) {
            $errMsg = array();
            foreach ($rsCashOut as $cashOutRow)
                array_push($errMsg, '<b>' . $cashOutRow['code'] . '</b>, ' . $this->errorMsg[225]);

            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>.' . $this->errorMsg[201] . '<br>' . implode('<br>', $errMsg));
        }
    }

    function cancelCashOut($pkey, $employeekey = '')
    {
        // delete cash out
        $hospitalCostCashOut = new HospitalCostCashOut();
        $rsCashOutKey =  $this->getTableKeyAndObj($this->tableName, array('key'));
        $employeeCriteria = ($employeekey !== '') ? ' and ' . $hospitalCostCashOut->tableName . '.employeekey = ' . $this->oDbCon->paramString($employeekey) : '';

        $rsCashOut = $hospitalCostCashOut->searchData('', '', true, ' and ' . $hospitalCostCashOut->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' 
                                                                   and ' . $hospitalCostCashOut->tableName . '.reftabletype = ' . $this->oDbCon->paramString($rsCashOutKey['key']) . '
                                                                   and ' . $hospitalCostCashOut->tableName . '.statuskey = 1 ' . $employeeCriteria);


        for ($i = 0; $i < count($rsCashOut); $i++) {
            $arrayToJs = $hospitalCostCashOut->changeStatus($rsCashOut[$i]['pkey'], 4, '', false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception($arrayToJs[0]['message']);
        }
    }

    function cancelTrans($rsHeader, $copy)
    {
        $service = new Service();

        $hospitalWorkOrder = new HospitalWorkOrder();
        $rsWorkOrder = $hospitalWorkOrder->searchData('', '', true, ' and ' . $hospitalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' and ' . $hospitalWorkOrder->tableName . '.statuskey = 1');
        for ($i = 0; $i < count($rsWorkOrder); $i++)
            $hospitalWorkOrder->changeStatus($rsWorkOrder[$i]['pkey'], 4, '', false, true);


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

        // $this->cancelCashOut($rsHeader[0]['pkey']);

        if ($copy)
            $this->copyDataOnCancel($rsHeader[0]['pkey']);

        // $this->cancelGLByRefkey($rsHeader[0]['pkey'], $this->tableName);
    }

    function validateSPKCompleted($rsHeader, $autoChangeStatus)
    {
        if ($autoChangeStatus)  return;
        $hospitalWorkOrder = new HospitalWorkOrder();
        

        if ($rsHeader[0]['statuskey'] <> 2  && $rsHeader[0]['statuskey'] <> 4  &&  $rsHeader[0]['statuskey'] <> 5) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201]);
        } else {

            //$rsWorkOrder = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey', $rsHeader[0]['pkey'], true,' and ' . $truckingServiceWorkOrder->tableName.'.statuskey in (1,2)');
            $rsWorkOrder = $hospitalWorkOrder->searchDataRow(
                array($hospitalWorkOrder->tableName . '.pkey'),
                '   and ' . $hospitalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . '
                                                                and ' . $hospitalWorkOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['menunggu'] . ',' . TRANSACTION_STATUS['konfirmasi'] . ')'
            );

            if (!empty($rsWorkOrder)) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['truckingServiceWorkOrder'][2]);
            } else {
                // posisi ini, sudah pasti gk ad SPK yg menunggu atau konfirmasi
                // model logol
                $truckingType = $this->loadSetting('truckingType');
                $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
                $totalQty = 0;
                foreach ($rsDetail as $row)
                    $totalQty += $row['qtyinbaseunit'];

                if ($truckingType == 2) {
                    // gagal jika
                    // 1. ada spk yg blm selesai (sudah dihandle di validasi biasa)
                    // 2. jml yg selesai lebih kecil dr partai

                    $sql = 'select 
                                    sum(' . $hospitalWorkOrder->tableWorkOrderCarDetail . '.qty) as qty  
                                from
                                    ' . $hospitalWorkOrder->tableName . ',
                                    ' . $hospitalWorkOrder->tableWorkOrderCarDetail . '
                                where
                                    ' . $hospitalWorkOrder->tableWorkOrderCarDetail . '.refkey = ' . $hospitalWorkOrder->tableName . '.pkey and
                                    ' . $hospitalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' and 
                                    ' . $hospitalWorkOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['selesai'] . ') 
                                ';

                    $rs = $this->oDbCon->doQuery($sql);
                    if ($rs[0]['qty'] < $totalQty)
                        $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['truckingServiceOrder'][4]);
                }
            }
        }
    }

    function validateReadyToCheck($rsHeader, $autoChangeStatus)
    {

        if ($autoChangeStatus)
            return;

        if ($rsHeader[0]['statuskey'] <> 3 &&  $rsHeader[0]['statuskey'] <> 5) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201]);
        }
    }

    function validateReadyToInvoice($rsHeader, $autoChangeStatus)
    {

        if ($autoChangeStatus) return;

        $useSPK = $this->loadSetting('useSPK');
        if ($useSPK == 2) {
            // validasi status nanti aj, coba dilihat
            return;
        }


        if ($rsHeader[0]['statuskey'] <> 4 && $rsHeader[0]['statuskey'] <> 3 && $rsHeader[0]['statuskey'] <> 6)
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201]);
    }

    function validateInvoiced($rsHeader, $autoChangeStatus)
    {

        if ($autoChangeStatus)
            return;

        // jika invoicing normal, semua SPK keupdate
        // harusnya ud gk kepake
        /*       $rsDetail = $this->getUnInvoicedItemDetail($rsHeader[0]['pkey']); 
        if (empty($rsDetail)) return; */


        // pelunasan dr invoice partial
        $rsInvoice = $this->getInvoiceInformation($rsHeader[0]['pkey']);
        $totalInvoice = 0;
        foreach ($rsInvoice as $invoice)
            $totalInvoice += $invoice['amount'];

        if ($rsHeader[0]['grandtotal'] <> $totalInvoice)
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[506]);
        else
            $this->updateQtyInvoiced($rsHeader[0]['pkey'], true);


        return;



        /*        $rsDetail = $this->getUnInvoicedItemDetail($rsHeader[0]['pkey']); 
        if (empty($rsDetail))
            return;*/

        // gk bisa manual change status	
        //$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[506]);

    }



    function afterStatusChanged($rsHeader)
    {
        $this->updateTruckingCostCashOut($rsHeader[0]['pkey']);   

        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);


        //langsung aj biar gk lama, kalo JO minimal sudah diatas SPK selesai
        if ($rsHeader[0]['statuskey'] >= 3) {
            if (
                empty($rsHeader[0]['firstwodate']) || empty($rsHeader[0]['firstwodate']) ||
                in_array($rsHeader[0]['firstwodate'], array('0000-00-00', '1970-01-01')) ||
                in_array($rsHeader[0]['lastwodate'], array('0000-00-00', '1970-01-01'))
            ) {

                $sql = 'update ' . $this->tableName . ' set firstwodate = trdate, lastwodate = trdate where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
                $this->oDbCon->execute($sql);
            }
        }

        //$this->updateStatusDate($rsHeader);

        // cancel dulu, jadinya invoice gk bisa dicancel gara2 kebentuk terus
        //if(!empty($rsHeader[0]['autoinvoice']) && $rsHeader[0]['statuskey'] == 5)
        //	$this->autoAddInvoice($rsHeader);   
    }


    function updateStatusDate($rsHeader)
    {
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
                $statusDate = '';
        }

        if (!empty($statusDate)) {
            $firstDate = '';
            if (empty($rsHeader[0]['first' . $statusDate . '']) || in_array($rsHeader[0]['first' . $statusDate . ''], array('0000-00-00', '1970-01-01')))
                $firstDate = ',first' . $statusDate . ' = now() ';

            $sql = 'update ' . $this->tableName . ' set last' . $statusDate . '= now() ' . $firstDate . ' where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
            $this->oDbCon->execute($sql);
        }
    }


    function updateSalesWorkOrderCost($id)
    {
        $hospitalWorkOrder = new HospitalWorkOrder();

        //$rs = $truckingServiceWorkOrder->searchData($truckingServiceWorkOrder->tableName.'.refkey', $id,true, ' and ' .$truckingServiceWorkOrder->tableName.'.statuskey in (2,3)');

        $rs = $hospitalWorkOrder->searchDataRow(
            array(
                $hospitalWorkOrder->tableName . '.pkey',
                $hospitalWorkOrder->tableName . '.outsourcecost',
                $hospitalWorkOrder->tableName . '.drivercommission',
                $hospitalWorkOrder->tableName . '.codrivercommission'
            ),
            '   and ' . $hospitalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($id) . '
                                                                and ' . $hospitalWorkOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['konfirmasi'] . ',' . TRANSACTION_STATUS['selesai'] . ')'
        );

        $totalCost = 0;
        for ($i = 0; $i < count($rs); $i++) {
            // outsource cost
            $totalCost += $rs[$i]['outsourcecost'];

            // komisi driver dan codriver
            $totalCost += $rs[$i]['drivercommission'];
            $totalCost += $rs[$i]['codrivercommission'];

            // detail cost
            $rsCost = $hospitalWorkOrder->getCostDetail($rs[$i]['pkey']);

            foreach ($rsCost as $cost)
                $totalCost += ($cost['qty'] * $cost['amount']);
            
            
        
        }
        
    

        try {
            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            $sql = 'update ' . $this->tableName . ' set totalworkordercost = ' . $this->oDbCon->paramString($totalCost) . ' where pkey = ' . $this->oDbCon->paramString($id);
            $this->oDbCon->execute($sql);

            $this->oDbCon->endTrans();
        } catch (Exception $e) {
            $this->oDbCon->rollback();
        }
    }


    function getJobOrderByMonth($startPeriod, $endPeriod)
    {
        $sql = 'select 
                    month(trdate) as month,  
                    DATE_FORMAT(trdate, \'%b\')  as monthname, 
                    year(trdate) as year, 
                    sum(grandtotal) as total
                from 
                    ' . $this->tableName . '
                where (statuskey >= 2 and statuskey <= 6) and trdate between \'' . date("Y-m-d", strtotime($startPeriod)) . '\' and LAST_DAY(\'' . date("Y-m-d 23:59", strtotime($endPeriod)) . '\')';

        $sql .=  $this->getWarehouseCriteria();
        $sql .= ' group by year(trdate),month(trdate)';

        return $this->oDbCon->doQuery($sql);
    }


    function getTruckingCostByMonth($startPeriod, $endPeriod)
    {
        $sql = 'select 
                    month(' . $this->tableName . '.trdate) as month,  
                    DATE_FORMAT(' . $this->tableName . '.trdate, \'%b\') as monthname, 
                    year(' . $this->tableName . '.trdate) as year, 
                    sum(' . $this->tableName . '.totalheadercost + ' . $this->tableName . '.totalworkordercost) as total
                from 
                    ' . $this->tableName . ' 
                   where (statuskey >= 2 and statuskey <= 6) and trdate between \'' . date("Y-m-d", strtotime($startPeriod)) . '\' and LAST_DAY(\'' . date("Y-m-d 23:59", strtotime($endPeriod)) . '\') ';

        $sql .=  $this->getWarehouseCriteria();
        $sql .= ' group by year(trdate),month(trdate)';

        return $this->oDbCon->doQuery($sql);
    }

    function getTruckingCostRevenueAmount($startPeriod, $endPeriod)
    {
        // Sales Amount

        $sql = 'select 
                  sum(' . $this->tableName . '.totalheadercost + ' . $this->tableName . '.totalworkordercost ) as costamount, 
                  sum(' . $this->tableName . '.grandtotal)  as revenueamount
                from 
                    ' . $this->tableName . ' 
                where 
                    (' . $this->tableName . '.statuskey >= 2 and ' . $this->tableName . '.statuskey <= 6 ) and
                     trdate between \'' . date("Y-m-01 00:00", strtotime($startPeriod)) . '\' and LAST_DAY(\'' . date("Y-m-d 23:59", strtotime($endPeriod)) . '\')';

        $sql .=  $this->getWarehouseCriteria();

        return $this->oDbCon->doQuery($sql);
    }

    function afterAddDataOnCopy($pkey, $oldkey)
    {
        // reset invoiced qty
        $sql = 'update ' . $this->tableNameDetail . ' set qtyinvoiced = 0,statuskey = 1 where refkey =  ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);

        $sql = 'update ' . $this->tableName . ' set  firstwodate = \'\', lastwodate = \'\' where pkey =  ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);
    }

    function getCustomerUninvoicedAmount($customerkey)
    {

        // utk penambahan nilai AR outstanding
        // statuskey semuanya utk jaga agar ketauan diawal jika melebih outstanding
        // '.$this->tableName.'.statuskey in (2,3,4,5,6) and
        $sql = 'select 
                    coalesce(sum(grandtotal - totalinvoiced),0) as totaluninvoiced 
                from 
                    ' . $this->tableName . ' 
                where  
                    ' . $this->tableName . '.customerkey = ' .  $this->oDbCon->paramString($customerkey);

        $rsJO =  $this->oDbCon->doQuery($sql);
        return $rsJO[0]['totaluninvoiced'];
    }

    function getInvoiceInformation($pkey, $statuskey = array(2, 3))
    {
        if (!is_array($statuskey)) $statuskey = array($statuskey);

        $hospitalServiceOrderInvoice = new HospitalServiceOrderInvoice();

        $sql = 'select
            ' . $hospitalServiceOrderInvoice->tableNameDetail . '.salesorderkey,     
            ' . $hospitalServiceOrderInvoice->tableName . '.pkey,
            ' . $hospitalServiceOrderInvoice->tableName . '.code,    
            ' . $hospitalServiceOrderInvoice->tableName . '.trdate,
            ' . $hospitalServiceOrderInvoice->tableName . '.isdownpayment,
            ' . $hospitalServiceOrderInvoice->tableName . '.customerkey,
            ' . $hospitalServiceOrderInvoice->tableName . '.grandtotal,
            ' . $hospitalServiceOrderInvoice->tableName . '.statuskey,
            ' . $hospitalServiceOrderInvoice->tableName . '.requestid,
            ' . $hospitalServiceOrderInvoice->tableStatus . '.status as statusname,
            ' . $hospitalServiceOrderInvoice->tableNameDetail . '.amount,
            ' . $hospitalServiceOrderInvoice->tableCustomCode . '.pkey as invoicetypekey,
            ' . $hospitalServiceOrderInvoice->tableCustomCode . '.name as invoicetypename
          from 
            ' . $hospitalServiceOrderInvoice->tableName . ',
            ' . $hospitalServiceOrderInvoice->tableStatus . ',
            ' . $hospitalServiceOrderInvoice->tableNameDetail . ',
            ' . $hospitalServiceOrderInvoice->tableCustomCode . '
          where  
            ' . $hospitalServiceOrderInvoice->tableNameDetail . '.salesorderkey in (' . $this->oDbCon->paramString($pkey, ',') . ') and   
            ' . $hospitalServiceOrderInvoice->tableName . '.pkey = ' . $hospitalServiceOrderInvoice->tableNameDetail . '.refkey and
            ' . $hospitalServiceOrderInvoice->tableName . '.statuskey = ' . $hospitalServiceOrderInvoice->tableStatus . '.pkey and
            ' . $hospitalServiceOrderInvoice->tableName . '.statuskey in (' . $this->oDbCon->paramString($statuskey, ',') . ') and
            ' . $hospitalServiceOrderInvoice->tableName . '.customcodekey =  ' . $hospitalServiceOrderInvoice->tableCustomCode . '.pkey';

        return $this->oDbCon->doQuery($sql);
    }


    function getAmountInvoiced($pkey)
    {
        // pisahkan dr yg atas agar tidak mengganggu performance yg lain
        $hospitalServiceOrderInvoice = new HospitalServiceOrderInvoice();

        $rsKey = $this->getTableKeyAndObj($hospitalServiceOrderInvoice->tableName, array('key'));

        $sql = 'select
            ' . $hospitalServiceOrderInvoice->tableName . '.code,    
            ' . $hospitalServiceOrderInvoice->tableName . '.trdate,
            ' . $hospitalServiceOrderInvoice->tableName . '.isdownpayment,
            ' . $hospitalServiceOrderInvoice->tableName . '.customerkey,
            ' . $hospitalServiceOrderInvoice->tableName . '.tax23value,
            ' . $hospitalServiceOrderInvoice->tableName . '.ispriceincludetax,
            ' . $hospitalServiceOrderInvoice->tableName . '.taxpercentage,
            ' . $hospitalServiceOrderInvoice->tableName . '.statuskey,
            ' . $hospitalServiceOrderInvoice->tableName . '.pkey,
            ' . $hospitalServiceOrderInvoice->tableARStatus . '.status as arstatusname,
            ' . $hospitalServiceOrderInvoice->tableARStatus . '.pkey as arstatuskey,
            ' . $hospitalServiceOrderInvoice->tableAR . '.code as arcode,
            coalesce(' . $hospitalServiceOrderInvoice->tableNameDetail . '.amount,0) as amount,
            ' . $hospitalServiceOrderInvoice->tableNameDetail . '.salesorderkey
            from 
            ' . $hospitalServiceOrderInvoice->tableName . '
                left join ' . $hospitalServiceOrderInvoice->tableAR . ' on ' . $hospitalServiceOrderInvoice->tableAR . '.reftabletype = ' . $this->oDbCon->paramString($rsKey['key']) . ' and ' . $hospitalServiceOrderInvoice->tableAR . '.refkey = ' . $hospitalServiceOrderInvoice->tableName . '.pkey 
                left join ' . $hospitalServiceOrderInvoice->tableARStatus . ' on ' . $hospitalServiceOrderInvoice->tableAR . '.statuskey = ' . $hospitalServiceOrderInvoice->tableARStatus . '.pkey and
                          ' . $hospitalServiceOrderInvoice->tableAR . '.statuskey <> 4,
            ' . $hospitalServiceOrderInvoice->tableNameDetail . ' 
          where  
            ' . $hospitalServiceOrderInvoice->tableNameDetail . '.salesorderkey in (' . $this->oDbCon->paramString($pkey, ',') . ') and  
            ' . $hospitalServiceOrderInvoice->tableName . '.pkey = ' . $hospitalServiceOrderInvoice->tableNameDetail . '.refkey and 
            ' . $hospitalServiceOrderInvoice->tableName . '.statuskey in (1,2,3) 
         group by (' . $hospitalServiceOrderInvoice->tableNameDetail . '.pkey)    
        ';
        return $this->oDbCon->doQuery($sql);
    }

    function getMonthlySalesSummary($startPeriod = '', $endPeriod = '',  $criteria = '', $groupby = '')
    {

        // DATE FORMAT => d / m / Y

        if (empty($startPeriod)) $startPeriod = DEFAULT_EMPTY_DATE;
        if (empty($endPeriod)) $endPeriod = date('d / m / Y');


        // be aware, perubahan group harus update ke concat index jg
        if (empty($groupby))
            $groupby = 'customerkey, year(trdate), month(trdate)';

        $sql  = '
                select 
                    ' . $this->tableCustomer . '.name, 
                    customerkey,
                    concat(customerkey,\'-\',DATE_FORMAT(trdate, \'%c%Y\'))  as periodindex,
                    month(trdate) as month,   
                    year(trdate) as year, 
                    sum(' . $this->tableName . '.grandtotal) as grandtotal,
                    sum(' . $this->tableName . '.totalworkordercost + ' . $this->tableName . '.totalheadercost) as totalcost,
                    sum(' . $this->tableName . '.grandtotal - ' . $this->tableName . '.totalworkordercost -  ' . $this->tableName . '.totalheadercost) as grossprofit
                from 
                    ' . $this->tableCustomer . ',
                    ' . $this->tableName . '
                where 
                    ' . $this->tableCustomer . '.pkey = ' . $this->tableName . '.customerkey';

        $sql .= ' and  trdate between ' . $this->oDbCon->paramDate($startPeriod . ' 00:00:00', ' / ') . ' and LAST_DAY(' . $this->oDbCon->paramDate($endPeriod . ' 23:59:59', ' / ') . ')';
        $sql .=  $this->getWarehouseCriteria();

        if (!empty($criteria))  $sql .= ' ' . $criteria;

        $sql .= ' group by ' . $groupby;

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }


    function getMonthlyQtySummary($startPeriod = '', $endPeriod = '',  $criteria = '', $groupby = '')
    {

        // DATE FORMAT => d / m / Y

        if (empty($startPeriod)) $startPeriod = DEFAULT_EMPTY_DATE;
        if (empty($endPeriod)) $endPeriod = date('d / m / Y');


        // be aware, perubahan group harus update ke concat index jg
        if (empty($groupby))
            $groupby = 'customerkey,itemkey, year(trdate), month(trdate)';

        $sql  = '
                select 
                    ' . $this->tableCustomer . '.name as customername, 
                    customerkey,
                    ' . $this->tableItem . '.pkey as itemkey, 
                    ' . $this->tableItem . '.name as itemname, 
                    concat(customerkey,\'-\',DATE_FORMAT(' . $this->tableName . '.trdate, \'%c%Y\'))  as periodindex,
                    concat(customerkey,\'-\',itemkey,\'-\',DATE_FORMAT(' . $this->tableName . '.trdate, \'%c%Y\'))  as perioditemindex,
                    month(' . $this->tableName . '.trdate) as month,   
                    year(' . $this->tableName . '.trdate) as year, 
                    sum(' . $this->tableNameDetail . '.qtyinbaseunit) as total 
                from 
                    ' . $this->tableName . ',
                    ' . $this->tableNameDetail . ',
                    ' . $this->tableCustomer . ',
                    ' . $this->tableItem . '
                where
                    ' . $this->tableName . '.pkey = ' . $this->tableNameDetail . '.refkey and
                    ' . $this->tableName . '.customerkey  = ' . $this->tableCustomer . '.pkey and 
                    ' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey';

        $sql .= ' and  ' . $this->tableName . '.trdate between ' . $this->oDbCon->paramDate($startPeriod . ' 00:00:00', ' / ') . ' and LAST_DAY(' . $this->oDbCon->paramDate($endPeriod . ' 23:59:59', ' / ') . ')';

        if (!empty($criteria))
            $sql .= ' ' . $criteria;

        $sql .=  $this->getWarehouseCriteria();

        $sql .= ' group by ' . $groupby;
        $sql .= ' order by customername asc, itemname asc';

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function updateTotalInvoicedAndOutstandingAmount($id){
        
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
    
    function getTotalInvoicedAndOutstanding($id, $customCodeKey = '')
    {

        $customCodeCriteria = (!empty($customCodeKey)) ? ' and customcodekey = ' . $this->oDbCon->paramString($customCodeKey) : '';

        $sql = 'select pkey, amount  from ' . $this->tablePartialInvoice . ' where refkey = ' . $this->oDbCon->paramString($id) . ' and amount > 0 ' . $customCodeCriteria;
        $rs = $this->oDbCon->doQuery($sql);

        $totalInvoiced = 0;
        foreach ($rs as $row)
            $totalInvoiced += $row['amount'];

        $sql = 'select coalesce(sum(amount),0) as outstanding  from ' . $this->tablePartialInvoice . ' where refkey = ' . $this->oDbCon->paramString($id) . $customCodeCriteria;
        $rsOutstanding = $this->oDbCon->doQuery($sql);

        $arr = array();
        $arr['rsTotalnvoiced'] = $rs;
        $arr['totalInvoiced'] = $totalInvoiced;
        $arr['outstanding'] = $rsOutstanding[0]['outstanding'];

        return $arr;
    }

    function calculateGrossProfitMargin($id)
    {
        $truckingCost = new Service(TRUCKING_SERVICE, 1);

        $rsHeader = $this->getDataRowById($id);

        $rsItem = $truckingCost->searchData($truckingCost->tableName . '.reimburse', 1, true);
        $rsItemReimburse = array_column($rsItem, 'pkey');


        // COST
        $rsHeaderCost = $this->getHeaderCost($id);

        $cost = 0;

        foreach ($rsHeaderCost as $costRow) {
            if (!in_array($costRow['costkey'], $rsItemReimburse))
                $cost += $costRow['subtotal'];
        }

        $rsCostInhouse = $this->getWorkOrderCostDetail($id, false, false);
        foreach ($rsCostInhouse as $costRow) {
            if (!in_array($costRow['costkey'], $rsItemReimburse))
                $cost += $costRow['amount'];
        }


        $rsCostOutsource = $this->getWorkOrderCostDetail($id, true, false);
        foreach ($rsCostOutsource as $costRow) {
            if (!in_array($costRow['costkey'], $rsItemReimburse))

                $cost += ($costRow['qty'] * $costRow['amount']);
        }


        // SELLING
        $selling = $rsHeader[0]['subtotal'];

        // ADDITIONAL SELLING
        $rsDetail = $this->getSellingCostDetail($id);
        foreach ($rsDetail as $costRow) {
            if (!in_array($costRow['costkey'], $rsItemReimburse))
                $selling += $costRow['subtotal'];
        }

        $grossMargin = (($selling - $cost) / $selling) * 100;
        return $grossMargin;
    }

/*    function updateDataAfterRealization($rsHeader, $rsDetail, $action)
    {
        // $action => 1 : confirm, 2: reverse confirm

        $id = $rsHeader[0]['refkey2'];
        $realizationkey = $rsHeader[0]['pkey'];

        // update biaya yagn langsung ditambahkan dr realisasi
        // hapus semua biaya yg berasal dr realisasi (refcashoutkey = 0)
        $sql = 'delete from ' . $this->tableHeaderCost . ' where realizationkey = ' . $this->oDbCon->paramString($realizationkey) . ' and refcashoutkey = 0 and refkey = ' . $this->oDbCon->paramString($id);
        $this->oDbCon->execute($sql);

        // update informasi realisasi
        foreach ($rsDetail as $row) {

            $amount = 0;
            $isrealization = 0;

            if ($action == 1) {
                $amount = $row['realcostvalue'];
                $isrealization = 1;

                // add biaya yang dr realisasi
                if ($row['settlementtypekey'] == 0) {
                    //insert ulang biaya dar realisasi
                    $sql = 'insert into ' . $this->tableHeaderCost . ' (refkey,costkey, qty ,amount, subtotal,isrealization, realizationkey,employeekey ) 
                            values  (' . $this->oDbCon->paramString($id) . ',' . $this->oDbCon->paramString($row['costkey']) . ',' . $this->oDbCon->paramString($row['qty']) . ', ' . $this->oDbCon->paramString($row['realcostvalue']) . ', ' . $this->oDbCon->paramString($row['amount']) . ',1,' . $this->oDbCon->paramString($realizationkey) . ',' . $this->oDbCon->paramString($rsHeader[0]['employeekey']) . ') ';
                    $this->oDbCon->execute($sql);
                }
            }

            $sql = 'update ' . $this->tableHeaderCost . '  set  amount = ' . $this->oDbCon->paramString($amount) . ', isrealization = ' . $this->oDbCon->paramString($isrealization) . ' where  ' . $this->tableHeaderCost . '.pkey = ' . $this->oDbCon->paramString($row['refkey2']);
            $this->oDbCon->execute($sql);
        }


        // UPDATE ULANG TOTAL COST YG TERJADI
        // sekaligus diudpate saja semua gpp harusnya  
        $sql = 'update 
                    ' . $this->tableHeaderCost . ' 
                set 
                   subtotal = CASE
                   WHEN isrealization <> 0 THEN qty * amount
                   ELSE qty * requestamount
                   END
                where 
                    refkey = ' . $this->oDbCon->paramString($id);

        $this->oDbCon->execute($sql);

        $sql = 'update 
                    ' . $this->tableName . ' 
                set   
                    totalHeaderCost = ( 
                            select sum(subtotal) as totalcost from ' . $this->tableHeaderCost . ' where refkey = ' . $this->oDbCon->paramString($id) . ' 
                    ) 
                where pkey = ' . $this->oDbCon->paramString($id);


        $this->oDbCon->execute($sql);
    }*/

    function getPartyDescription($pkey)
    {

        $sql = 'select 
                    ' . $this->tableNameDetail . '.refkey,
                    concat (' . $this->tableNameDetail . '.qtyinbaseunit, "x ", ' . $this->tableItem . '.name) as party 
                  from
                    ' . $this->tableNameDetail . ', 
                    ' . $this->tableItem . ' 
                  where
                    ' . $this->tableNameDetail . '.itemkey = ' . $this->tableItem . '.pkey and  
                    ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $rs = $this->oDbCon->doQuery($sql);


        if (!is_array($pkey)) {
            $rs = array_column($rs, 'party');
            return implode('<br>', $rs);
        } else {
            $returnArr = array();
            $rs = $this->reindexDetailCollections($rs, 'refkey');
            foreach ($rs as $key => $row)
                $returnArr[$key] = implode('<br>', array_column($row, 'party'));

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

    function groupCostAmount($rs)
    {
        // group by costkey and amount

        $arr = array();

        // add yg sudah direalisasi dulu ...
        for ($i = 0; $i < 2; $i++) {

            foreach ($rs as $key => $row) {

                // kalo blm direalisasi, skip
                if ($row['isrealization'] == $i) continue;

                $costkey = $row['costkey'];
                $amount = ($row['isrealization'] == 1) ? $row['amount'] : $row['requestamount'];
                $isrealization = $row['isrealization'];

                $keyIndex = md5($costkey . '-' . $amount) . $isrealization;

                if (!isset($arr[$keyIndex])) {
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

    function groupSupplierAmount($rs)
    {
        // group by costkey and amount

        $arr = array();

        // add yg sudah direalisasi dulu ...

        foreach ($rs as $key => $row) {

            // kalo blm direalisasi, skip
            //			if($row['amount'] <=0 ) continue;

            $supplierkey = $row['supplierkey'];
            $amount =  $row['amount'];

            $keyIndex = md5($supplierkey . '-' . $amount);

            if (!isset($arr[$keyIndex])) {
                $arr[$keyIndex] = $row;
                $arr[$keyIndex]['qty'] = 0;
            }

            $arr[$keyIndex]['qty']++;

            unset($rs[$key]);
        }

        $arr = array_values($arr);
        return $arr;
    }

    function getPartnersVehicleInformation($pkey, $criteria = '')
    {
        $rsObjKeyInvoice = $this->getTableKeyAndObj($this->tableTruckingServiceOrderInvoiceHeader);

        $sql = 'select 
	   			' . $this->tableName . '.code , 
	   			' . $this->tableName . '.pkey , 
	   			' . $this->tableWorkOrder . '.code as wocode, 
	   			' . $this->tableWorkOrder . '.pkey as wokey, 
	   			' . $this->tableSupplier . '.name as vehiclepartnersname, 
                ' . $this->tableAP . '.amount,
			  	' . $this->tableCar . '.policenumber as registrationnumber,
                ' . $this->tableAP . '.supplierkey,
                ' . $this->tableAP . '.code as apcode
			  from 
			  	' . $this->tableName . ',
			  	' . $this->tableCar . ',
			  	' . $this->tableSupplier . ',
			  	' . $this->tableAP . ',
			  	' . $this->tableWorkOrder . ' 
			  where   
                ' . $this->tableWorkOrder . '.refkey =  ' . $this->tableName . '.pkey  and 
                ' . $this->tableWorkOrder . '.isoutsource = 0  and 
                ' . $this->tableWorkOrder . '.carkey =  ' . $this->tableCar . '.pkey  and
                ' . $this->tableAP . '.statuskey in(1,2,3) and 
                ' . $this->tableAP . '.refkey2 =  ' . $this->tableWorkOrder . '.pkey  and 
				' . $this->tableAP . '.aptype =' . AP_TYPE['serviceOutsource'] . '  and 
				' . $this->tableAP . '.reftabletype =' . $rsObjKeyInvoice['key'] . '  and 
				' . $this->tableAP . '.supplierkey =  ' . $this->tableSupplier . '.pkey  and 
				' . $this->tableName . '.pkey = ' . $this->oDbCon->paramString($pkey);

        if (!empty($criteria))
            $sql .=  ' ' . $criteria;

        $sql .= ' order by vehiclepartnersname asc, amount desc';

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function inAllowedStateToUpdateServices($statuskey, $detailpkey = '')
    {

        // cek status 
        if (!in_array($statuskey, array(1, 2))) return false;

        if (!empty($detailpkey)) {
            // cek sudah ad SPK yg diproses blm
            return ($this->hasConfirmedWorkOrder($detailpkey)) ? false : true;
        }


        // kalo gk perlu cek detail, return true
        return true;
    }

    function getBestSalesAmountByGroup($groupBy, $startPeriod, $endPeriod, $limit = 5)
    {
        // VALUE BASED

        $sql = 'select 
                      sum(' . $this->tableName . '.grandtotal) as amount, 
                      ' . $this->tableCustomer . '.name  as customername
                    from 
                        ' . $this->tableName . ', 
                        ' . $this->tableCustomer . ' 
                    where 
                         ' . $this->tableName . '.statuskey in (2,3,4,5,6)  and 
                         ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey and
                         ' . $this->tableName . '.trdate between \'' . date("Y-m-01 00:00", strtotime($startPeriod)) . '\' and LAST_DAY(\'' . date("Y-m-d 23:59", strtotime($endPeriod)) . '\') ';

        $sql .=  $this->getWarehouseCriteria();
        $sql .=  $this->getCompanyCriteria();

        $sql .= 'group by 
                        ' . $groupBy . '
                     order by amount desc limit ' . $limit;

        return $this->oDbCon->doQuery($sql);
    }


    function getDetailForAPI($arrKey, $arrIndex = array())
    {
        $rsDetailsCol = array();

        if (in_array('service_detail', $arrIndex)) {
            $rsDetails = $this->getDetailWithRelatedInformation($arrKey);
            $rsDetails = $this->reindexDetailCollections($rsDetails, 'refkey');
            $rsDetailsCol['service_detail'] = $rsDetails;
        }

        if (in_array('additional_cost_detail', $arrIndex)) {
            $rsDetails = $this->getHeaderCost($arrKey);
            $rsDetails = $this->reindexDetailCollections($rsDetails, 'refkey');
            $rsDetailsCol['additional_cost_detail'] = $rsDetails;
        }

        if (in_array('additional_selling_detail', $arrIndex)) {
            $rsDetails = $this->getSellingCostDetail($arrKey);
            $rsDetails = $this->reindexDetailCollections($rsDetails, 'refkey');
            $rsDetailsCol['additional_selling_detail'] = $rsDetails;
        }

        if (in_array('invoice_detail', $arrIndex)) {
            $rsDetails = $this->getInvoiceInformation($arrKey, array(1, 2, 3));
            $rsDetails = $this->reindexDetailCollections($rsDetails, 'salesorderkey');
            $rsDetailsCol['invoice_detail'] = $rsDetails;
        }

        /*if(in_array('invoice_proforma_detail',$arrIndex)){ 
            $rsDetails = $this->getInvoiceInformation($arrKey,1); 
            $rsDetails = $this->reindexDetailCollections($rsDetails,'salesorderkey'); 
            $rsDetailsCol['invoice_proforma_detail'] = $rsDetails;
        }*/

        return $rsDetailsCol;
    }

    function updateWOActivityDate($sokey)
    {
        $hospitalWorkOrder = new HospitalWorkOrder();
        // LOGOL request dr SPK nya pending sudah diupdate
        //$rsWO = $hospitalWorkOrder->searchData('refkey',$sokey,true,' and '.$hospitalWorkOrder->tableName.'.statuskey in (2,3) order by ');
        $rsWO = $hospitalWorkOrder->searchDataRow(
            array($hospitalWorkOrder->tableName . '.pkey', $hospitalWorkOrder->tableName . '.stuffingdatetime'),
            ' and ' . $hospitalWorkOrder->tableName . '.refkey in (' . $this->oDbCon->paramString($sokey, ',') . ') 
											   and ' . $hospitalWorkOrder->tableName . '.statuskey in(1,2,3) order by stuffingdatetime asc'
        );
        $firstDate = DEFAULT_EMPTY_DATE;
        $lastDate = DEFAULT_EMPTY_DATE;
        if (!empty($rsWO)) {
            //$firstDate = $this->oDbCon->paramDate($rsWO[0]['stuffingdatetime'],' / '); 
            $firstDate = $this->formatDBDate($rsWO[0]['stuffingdatetime'], 'd / m / Y H:i');
            $countWO = count($rsWO);
            $lastDate = $this->formatDBDate($rsWO[($countWO - 1)]['stuffingdatetime'], 'd / m / Y H:i');
        }

        $sql = 'update ' . $this->tableName . ' set firstwodate = ' . $this->oDbCon->paramDate($firstDate, ' / ') . ', lastwodate = ' . $this->oDbCon->paramDate($lastDate, ' / ') . ' where pkey = ' . $this->oDbCon->paramString($sokey);
        $this->oDbCon->execute($sql);
    }


    /*function autoAddInvoice($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];

        $arrayToJs = array();

        $truckingServiceOrderInvoice = new TruckingServiceOrderInvoice();
        $paymentMethod = new PaymentMethod();
        $termOfPayment = new TermOfPayment();
        $customCode = new CustomCode();

        $arrParam = array();
        $rsDetail = array();

        $rsTOP = $termOfPayment->searchData('', '', true, ' and (' . $termOfPayment->tableName . '.systemVariable = 1)');
        $topkey = (!empty($rsTOP)) ? $rsTOP[0]['pkey'] : 0;

        $paymentMethodKey = 0;
        if ($rsTOP[0]['duedays'] == 0) {
            $rsMethod = $paymentMethod->searchData('', '', true, ' and (' . $paymentMethod->tableName . '.systemVariable = 1)');
            $paymentMethodKey = (!empty($rsMethod)) ? $rsMethod[0]['pkey'] : 0;
        }

        $arrInvoiceDecode = (!empty($rsHeader[0]['autoinvoice'])) ? json_decode(htmlspecialchars_decode($rsHeader[0]['autoinvoice'], ENT_COMPAT), true) : array();

        $invoiceCode = (!empty($arrInvoiceDecode['invoice_id'])) ? $arrInvoiceDecode['invoice_id'] : 'xxxxx';

        $invoiceTypeCode = (!empty($arrInvoiceDecode['invoice_type_id'])) ? $arrInvoiceDecode['invoice_type_id'] : '';
        $rsCustomCode = $customCode->searchData($customCode->tableName . '.code', $invoiceTypeCode, true, '', 'limit 1');

        if (!empty($rsCustomCode)) {
            $invoiceTypeKey = $rsCustomCode[0]['pkey'];
        } else {
            $tablekey = $this->getTableKeyAndObj($truckingServiceOrderInvoice->tableName, array('key'))['key'];
            $rsCustomCode = $customCode->searchData($customCode->tableName . '.reftabletype', $tablekey, true, '', 'limit 1');
            $invoiceTypeKey = $rsCustomCode[0]['pkey'];
        }

        $arrParam['code'] =  $invoiceCode;
        $arrParam['selCustomCode'] = $invoiceTypeKey;
        $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
        $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];

        $arrParam['selTermOfPayment'] = $topkey;
        $arrParam['selBank'] = $paymentMethodKey; // nanti baru diupdate, gk terlalu penting
        $arrParam['selInvoiceTo'] = 1;

        if ($rsTOP[0]['duedays'] == 0) {
            $arrParam['selPaymentMethod'][0] = $paymentMethodKey;
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
        foreach ($rsDetail as $detail) {
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

        foreach ($rsDetailAdditional as $detail) {
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
        $arrParam['hidDetailItemKeyTotalRows'][1][0] = count($rsDetail) + count($rsDetailAdditional);


        $arrayToJs = $truckingServiceOrderInvoice->addData($arrParam);

        if (!$arrayToJs[0]['valid'])
            $this->addErrorList($arrayToJs, false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message'], true);
        else {
            $invoicekey = $arrayToJs[0]['data']['pkey'];
            $arrayToJs = $truckingServiceOrderInvoice->changeStatus($invoicekey, TRANSACTION_STATUS['konfirmasi']);
        }


        return $arrayToJs;
    }*/
}
?>