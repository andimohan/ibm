<?php

class HospitalWorkOrder extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'hospital_work_order';
        $this->tableJobOrderHeader = 'hospital_job_order_header';
        $this->tableJobOrderDetail = 'hospital_job_order_detail';
        $this->tableCost = 'hospital_work_order_cost';
        $this->tableWorkOrderItemDetail = 'hospital_work_order_item';
        $this->tableWorkOrderAssetDetail = 'hospital_work_order_asset';
        $this->tableWorkOrderCarDetail = 'hospital_work_order_car';
        $this->tableCategory = 'trucking_service_order_category';
        $this->tableHospitalCostCashOut = 'hospital_cost_cash_out_header';
        $this->tableHospitalCostCashOutDetail = 'hospital_cost_cash_out_detail';
        $this->tableCargoType = 'cargo_type';
        $this->tableHospitalJob = 'hospital_job';
        $this->tableItem = 'item';
        $this->tableAsset = 'asset';
        $this->tableItemUnit = 'item_unit';
        $this->tableFile = 'hospital_work_order_file';
        $this->tableWarehouse = 'warehouse';
        $this->tableCustomer = 'customer';
        $this->tableLocation = 'location';
        $this->tableCar = 'car';
        $this->tableCarCategory = 'car_category';
        $this->tableFile = 'hospital_work_order_file';
        $this->tableChassis = 'chassis';
        $this->tableEmployee = 'employee';
        $this->tableStatus = 'hospital_work_order_status';
        $this->tableHistory = 'history';
        $this->tableCustomerInsurancePolicy = 'customer_insurance_policy';
        $this->tableDepot = 'depot';
        $this->tableTerminal = 'terminal';
        $this->tableSupplier = 'supplier';
        $this->tableConsignee = 'consignee';
        $this->isTransaction = true;

        $this->allowedStatusForEdit = array(1, 2);

        $this->securityObject = 'HospitalWorkOrder';
        $this->costSecurityObject = 'TruckingServiceWorkOrderCost';
        // $this->autoPrintURL = 'print/truckingServiceWorkOrder';
        $this->uploadFileFolder = 'hospital-work-order/';

        $this->arrAssetDetail = array(); 
        $this->arrAssetDetail['pkey'] = array('hidAssetDetailKey');
        $this->arrAssetDetail['refkey'] = array('pkey', 'ref');
        $this->arrAssetDetail['assetkey'] = array('hidAssetKey');
        $this->arrAssetDetail['qty'] = array('qtyAsset','number');        

        $this->arrItemDetail = array();  
        $this->arrItemDetail['pkey'] = array('hidDetailItemKey');
        $this->arrItemDetail['refkey'] = array('pkey','ref');
        $this->arrItemDetail['itemkey'] = array('hidItemDetailKey');
        $this->arrItemDetail['qty'] = array('qty','number');
        $this->arrItemDetail['qtyinbaseunit'] = array('qtyInBaseUnit','number');
        $this->arrItemDetail['unitkey'] = array('selUnit');
        $this->arrItemDetail['priceinunit'] = array('priceInUnit','number');
        $this->arrItemDetail['priceinbaseunit'] = array('priceInBaseUnit','number');
        $this->arrItemDetail['unitconvmultiplier'] = array('unitConvMultiplier','number');
        $this->arrItemDetail['total'] = array('detailSubtotal','number');
        $this->arrItemDetail['costinbaseunit'] = array('cogs','number');
        $this->arrItemDetail['receivedqtyinbaseunit'] = array('receivedQtyInBaseUnit','number');



        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['qty'] = array('qtyCostDetail'); //gk bisa set mandatory, utk SPK yg gk pake qty model reguler
        $this->arrDataDetail['taxpercentage'] = array('taxPercentageCostDetail');
        $this->arrDataDetail['taxvalue'] = array('taxValueCostDetail');
        $this->arrDataDetail['tax23percentage'] = array('tax23PercentageCostDetail');
        $this->arrDataDetail['tax23value'] = array('tax23ValueCostDetail');
        //$this->arrDataDetail['istax23'] = array('chkIsTax23CostDetail');
        $this->arrDataDetail['total'] = array('totalCostDetail');
        $this->arrDataDetail['costkey'] = array('hidCostKey', array('mandatory' => true));
        $this->arrDataDetail['supplierkey'] = array('hidSupplierDetailKey');
        $this->arrDataDetail['employeekey'] = array('hidEmployeeDetailKey');
        $this->arrDataDetail['amount'] = array('amount', array('datatype' => 'number'));
        $this->arrDataDetail['requestamount'] = array('requestAmount', array('datatype' => 'number')); //gk bisa set mandatory karena kalo edit dr realisasi, jadinya hilang row nya
        $this->arrDataDetail['isreimburse'] = array('isReimburse');

        $this->arrCarDetail = array();
        $this->arrCarDetail['pkey'] = array('hidOutsourceVehicleDetailKey');
        $this->arrCarDetail['refkey'] = array('pkey', 'ref');
        $this->arrCarDetail['itemkey'] = array('hidServiceDetailKey', array('mandatory' => true));
        $this->arrCarDetail['carregistrationnumber'] = array('carRegistration');
        $this->arrCarDetail['container'] = array('containerDetail');
        $this->arrCarDetail['seal'] = array('sealDetail');
        $this->arrCarDetail['qty'] = array('qtyDetail', array('datatype' => 'number'));
        $this->arrCarDetail['price'] = array('priceDetail', array('datatype' => 'number'));
        $this->arrCarDetail['taxpercentage'] = array('taxPercentageDetail', array('datatype' => 'number'));
        $this->arrCarDetail['taxvalue'] = array('taxValueDetail', array('datatype' => 'number'));
        $this->arrCarDetail['total'] = array('subtotalDetail', array('datatype' => 'number'));
        //$this->arrCarDetail['istax23'] = array('chkIsTax23Detail');
        $this->arrCarDetail['tax23percentage'] = array('tax23PercentageDetail');
        $this->arrCarDetail['tax23value'] = array('tax23ValueDetail');


        // REFCASHOUTKEY jgn disave dr form, karena bisa bentrok kalo form lg dibuka, terus kas keluar dicancel, lalu form disave.
        //$this->arrDataDetail['refcashoutkey'] = array('hidRefCashOutKey');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrItemDetail, 'tableName' => $this->tableWorkOrderItemDetail));
        array_push($arrDetails, array('dataset' => $this->arrAssetDetail, 'tableName' => $this->tableWorkOrderAssetDetail));
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableCost));
        array_push($arrDetails, array('dataset' => $this->arrCarDetail, 'tableName' => $this->tableWorkOrderCarDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));

        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['stuffingdatetime'] = array('trDateStuffing', 'date');
        $this->arrData['refkey'] = array('hidSOKey');
        $this->arrData['refdetailkey'] = array('hidSODetailKey');
        $this->arrData['itemkey'] = array('hidItemKey');
        $this->arrData['driverkey'] = array('hidDriverKey');
        $this->arrData['doctorkey'] = array('hidDoctorKey');
        $this->arrData['codriverkey'] = array('hidCoDriverKey');
        $this->arrData['carkey'] = array('hidCarKey');
        $this->arrData['outsourcecarregistrationnumber'] = array('outsourceCarRegistrationNumber');
        $this->arrData['outsourcecost'] = array('outsourceCost', 'number');
        $this->arrData['outsourcedownpayment'] = array('outsourceDownpayment', 'number');
        $this->arrData['outsourceap'] = array('outsourceAP', 'number');
        $this->arrData['downpaymentemployeekey'] = array('hidDownpaymentRecipientKey');
        $this->arrData['isoutsource'] = array('chkIsOutsource');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['locationkey'] = array('hidLocationKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['purchaseorderkey'] = array('hidPurchaseOrderKey');
        $this->arrData['productdesc'] = array('productDescription');
        $this->arrData['drivercommission'] = array('driverCommission', 'number');
        $this->arrData['codrivercommission'] = array('codriverCommission', 'number');
        $this->arrData['doctorcommission'] = array('doctorCommission', 'number');
        $this->arrData['ispriceincludetax'] = array('chkIncludeTax');
        $this->arrData['taxpercentage'] = array('taxPercentage', 'number');
        $this->arrData['taxvalue'] = array('taxValue', 'number');
        $this->arrData['total'] = array('total', 'number');
        $this->arrData['verificationcode'] = array('verificationCode');
        $this->arrData['customerkey'] = array('hidCustomerKey');

        $this->refAutoCode = array('param' => 'hidSOKey', 'refField' => 'refkey');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 150, 'align' => 'center', 'format' => 'datetime'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobOrderCode', 'title' => 'jobOrder', 'dbfield' => 'serviceordercode', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'services', 'title' => 'services', 'dbfield' => 'containername', 'default' => true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouseName', 'title' => 'warehouse', 'dbfield' => 'warehousename',  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'carRegistrationNumber', 'title' => 'carRegistrationNumber', 'dbfield' => 'policenumber', 'width' => 100));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Job Order', $this->tableJobOrderHeader . '.code'));
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array('Item', $this->tableItem . '.name'));
        array_push($this->arrSearchColumn, array('Total', $this->tableName . '.grandtotal'));
        array_push($this->arrSearchColumn, array('No. Polisi', $this->tableCar . '.policenumber'));
        array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));
         array_push($this->arrSearchColumn, array('Supir', $this->tableEmployee . '.name'));
        // array_push($this->arrSearchColumn, array('Progress', $this->tableHospitalJob . '.name'));
         array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.trdesc'));
     
        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printWorkOrder', 'name' => $this->lang['printWorkOrder'],  'icon' => 'print', 'url' => 'print/hospitalWorkOrder'));
        array_push($this->printMenu, array('code' => 'printComplete', 'name' => $this->lang['printSummary'],  'icon' => 'print', 'url' => 'print/truckingServiceOrderCompleteFromWO'));
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));

        $this->includeClassDependencies(array(
            'Supplier.class.php',
            'TruckingServiceOrder.class.php',
            'HospitalCostCashOut.class.php',
            'APEmployeeCommission.class.php',
            'APEmployeeCommissionPayment.class.php',
            'Asset.class.php',
            'CarTurnover.class.php',
            'WorkProgress.class.php',
            'WorkProgressStep.class.php',
            'Warehouse.class.php',
            'Service.class.php',
            'Location.class.php',
            'TruckingJob.class.php',
            'Car.class.php',
            'Item.class.php',
            'ItemUnit.class.php',
            'ItemMovement.class.php',
            'City.class.php',
            'Diagnose.class.php',
            'HospitalJobOrder.class.php',
            'CostRate.class.php',
            'AP.class.php',
            'APPayment.class.php',
            'APPayableTax23.class.php',
            'Downpayment.class.php',
            'SupplierDownpayment.class.php',
            'CustomerInsurancePolicy.class.php'
        ));

        $this->overwriteConfig();
    }

    function getQuery()
    {

        $sql = '
			SELECT ' . $this->tableName . '.* ,  
			   concat(' . $this->tableName . '.routefrom, \' - \', ' . $this->tableName . '.routeto) as route ,
			   ' . $this->tableStatus . '.status as statusname ,
			   ' . $this->tableEmployee . '.name as drivername ,
			   ' . $this->tableCar . '.code as policecode ,
			   ' . $this->tableCar . '.policenumber ,
			   ' . $this->tableCarCategory . '.name as carcategoryname ,
			   ' . $this->tableCustomer . '.pkey as customerkey ,
			   ' . $this->tableCustomer . '.name as companyname ,
               ' . $this->tableJobOrderHeader . '.code as serviceordercode,
               ' . $this->tableJobOrderHeader . '.trdate as serviceorderdate,
               ' . $this->tableJobOrderHeader . '.shipmentnumber,
               ' . $this->tableJobOrderHeader . '.donumber,
               ' . $this->tableJobOrderHeader . '.patientkey,
               ' . $this->tableCustomerInsurancePolicy . '.name as patientname,
               ' . $this->tableWarehouse . '.name as warehousename,
               ' . $this->tableLocation . '.name as locationname,
               ' . $this->tableItem . '.name as containername,
               ' . $this->tableJobOrderDetail . '.priceinunit,
               ' . $this->tableJobOrderDetail . '.isgroup,
               ' . $this->tableJobOrderDetail . '.qtyinbaseunit, 
               ' . $this->tableSupplier . '.name as vehiclepartnersname,
               IF(isoutsource=1, "TL", "") as TL,
               IF(isoutsource=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as outsourceicon
			FROM 
                ' . $this->tableStatus . ', 
                ' . $this->tableJobOrderHeader . '
                    left join ' . $this->tableCustomerInsurancePolicy . ' on ' . $this->tableJobOrderHeader . '.patientkey = ' . $this->tableCustomerInsurancePolicy . '.pkey  
                    left join ' . $this->tableLocation . ' on ' . $this->tableJobOrderHeader . '.caselocationkey = ' . $this->tableLocation . '.pkey  , 
                ' . $this->tableCustomer . ',    
                ' . $this->tableItem . ',    
                ' . $this->tableWarehouse . ',
                ' . $this->tableName . ' 
                    left join ' . $this->tableJobOrderDetail . ' on ' . $this->tableName . '.refdetailkey = ' . $this->tableJobOrderDetail . '.pkey   
                    left join ' . $this->tableEmployee . ' on ' . $this->tableName . '.driverkey = ' . $this->tableEmployee . '.pkey
                    left join ' . $this->tableCar . ' on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey   
                    left join ' . $this->tableCarCategory . ' on ' . $this->tableCar . '.categorykey = ' . $this->tableCarCategory . '.pkey   
                    left join ' . $this->tableSupplier . ' on ' . $this->tableCar . '.supplierkey = ' . $this->tableSupplier . '.pkey 
			WHERE ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                  ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and
                  ' . $this->tableName . '.refkey = ' . $this->tableJobOrderHeader . '.pkey  and 
                  '.$this->tableName.'.itemkey  = '.$this->tableItem.'.pkey and
                  ' . $this->tableJobOrderHeader . '.customerkey = ' . $this->tableCustomer . '.pkey 
 		' . $this->criteria;


        $sql .=  $this->getWarehouseCriteria();

        return $sql;
    }

    function afterUpdateData($arrParam, $action)
    {
        $hospitalJobOrder = new HospitalJobOrder();

        $pkey = $arrParam['pkey'];
        $rsHeader = $this->getDataRowById($pkey);

        $this->updateTruckingCostCashOut($rsHeader[0]['pkey']); 
        $hospitalJobOrder->updateContainer($rsHeader[0]['refkey']);        
        $hospitalJobOrder->updateSalesWorkOrderCost($rsHeader[0]['refkey']);
        $hospitalJobOrder->updateWOActivityDate($rsHeader[0]['refkey']);
    }

    function updateTruckingCostCashOut($pkey)
    {

        //header harus reload ulang, karena status sudah berubah (ketika konfirmasi)
        $rsHeader = $this->getDataRowById($pkey);

        $hospitalCostCashOut = new HospitalCostCashOut();

        $isOutsource = $rsHeader[0]['isoutsource'];
        $driverkey = $rsHeader[0]['driverkey'];


        // get all listed employee
        $arrEmployeeKey = array();

        $sql = 'select distinct(employeekey) as employeekey from ' . $this->tableCost . ' where refkey = ' . $this->oDbCon->paramString($pkey) . ' and employeekey <> 0 ';
        $rsDetailEmployee = $this->oDbCon->doQuery($sql);
        $arrEmployeeKey = array_column($rsDetailEmployee, 'employeekey');

        // add outsource downpayment recipient HERE 
        // if ($rsHeader[0]['outsourcedownpayment'] > 0)
            // array_push($arrEmployeeKey, $rsHeader[0]['downpaymentemployeekey']);



        // buat nambah driver ke list penerima cash out
        // gk jelas kepake ap gk
        // if ($isOutsource == 0 && !empty($driverkey) && !in_array($driverkey, $arrEmployeeKey))
            // array_push($arrEmployeeKey, $driverkey);


        /*
        foreach($rsDetailEmployee as $employee)
            if (!empty($employee) && !in_array($employee,$arrEmployeeKey ))
                array_push($arrEmployeeKey,$employee);
        */


        $rsKey = $this->getTableKeyAndObj($this->tableName, array('key'));

        // utk  delete karyawan yg sudah gk ad kas keluarnya
        $employeeCriteria = (!empty($arrEmployeeKey)) ? '  and reftabletype = ' . $rsKey['key'] . ' and employeekey not in (' . implode(',', $arrEmployeeKey) . ') ' : '';

        $rsCashOut = $hospitalCostCashOut->searchData('', '', true, $employeeCriteria . ' and ' . $hospitalCostCashOut->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $hospitalCostCashOut->tableName . '.statuskey = 1');
        for ($i = 0; $i < count($rsCashOut); $i++) {
            $this->cancelCashOut($pkey, $rsCashOut[$i]['employeekey']);
        }

        // kalo status konfirmasi baru lanjut proses
        // kenapa harus yg statuskeynya 2 ? kalo 1 atau 3 problem gk ?

        if ($rsHeader[0]['statuskey'] == 2) {
            // ini jika employee kosong dan HANYA utk bukan TL, masalah
            // hanya jika semua gk ad supplier sama sekali
            // atau ad yg tdk diisi karyawannya / sopirnya 
            $sql = 'select employeekey from ' . $this->tableCost . ' where refkey = ' . $this->oDbCon->paramString($pkey) . ' and employeekey = 0 ';
            $rsEmptyRecipient = $this->oDbCon->doQuery($sql);
            if (!empty($rsEmptyRecipient))
                array_push($arrEmployeeKey, 0);

            /*       
            if (empty($arrEmployeeKey)) 
                array_push($arrEmployeeKey,0);*/



            // update ulang kas keluar  
            for ($i = 0; $i < count($arrEmployeeKey); $i++) {
                $employeeKey = $arrEmployeeKey[$i];

                // cost di SPK 

                $rsCost = $this->getCostDetail($rsHeader[0]['pkey'], '', ' and ' . $this->tableCost . '.refcashoutkey = 0 and ' . $this->tableCost . '.realizationkey = 0 and ' . $this->tableCost . '.supplierkey = 0 and ' . $this->tableCost . '.employeekey = ' . $this->oDbCon->paramString($employeeKey) . ' ');

                if ($employeeKey == $rsHeader[0]['downpaymentemployeekey'] && $rsHeader[0]['outsourcedownpayment'] > 0 && $rsHeader[0]['refcashoutdownpaymentkey'] == 0) {
                    $arrDP = array();
                    $arrDP['qty'] = 1;
                    $arrDP['costkey'] = DEFAULT_COST['outsourceDownpayment'];
                    $arrDP['employeekey'] = $employeeKey;
                    $arrDP['requestamount'] = $rsHeader[0]['outsourcedownpayment'];
                    $arrDP['total'] = $rsHeader[0]['outsourcedownpayment'];
                    // $arrDP['amount'] = $rsHeader[0]['outsourcedownpayment'];  // <--- ini gk boleh ad realisasi, nabrak, karena gk ad form nya
                    array_push($rsCost, $arrDP);
                }

                $workOrderCost = array();
                for ($j = 0; $j < count($rsCost); $j++) {
                    //array_push($workOrderCost,$rsCost[$j]['requestamount']);  // <-- sebelum ad qty pake ini
                    array_push($workOrderCost, $rsCost[$j]['pkey']);
                    array_push($workOrderCost, $rsCost[$j]['total']);
                    array_push($workOrderCost, $rsCost[$j]['costkey']);
                }
                $workOrderCost = md5(json_encode($workOrderCost));

                // cost di cash out yg masi pending 
                $rsCashOut = $hospitalCostCashOut->searchData('', '', true, '  and reftabletype = ' . $rsKey['key'] . ' and ' . $hospitalCostCashOut->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $hospitalCostCashOut->tableName . '.employeekey = ' . $this->oDbCon->paramString($employeeKey) . ' and ' . $hospitalCostCashOut->tableName . '.statuskey = 1');
                $rsCashOutDetail = (!empty($rsCashOut)) ? $hospitalCostCashOut->getDetailById($rsCashOut[0]['pkey']) : array(); //ambil salah satu cashout aja
                $cashOutDetail = array();
                for ($j = 0; $j < count($rsCashOutDetail); $j++) {
                    array_push($cashOutDetail, $rsCashOutDetail[$j]['refheadercostkey']);
                    array_push($cashOutDetail, $rsCashOutDetail[$j]['amount']);
                    array_push($cashOutDetail, $rsCashOutDetail[$j]['costkey']);
                }
                $cashOutDetail = md5(json_encode($cashOutDetail));

                $compareResult = ($cashOutDetail == $workOrderCost) ? true : false;

                // kenapa harus yg statuskeynya 2 ? kalo 1 atau 3 problem gk ?
                if ($rsHeader[0]['statuskey'] == 2 && !$compareResult) {
                    $this->cancelCashOut($pkey, $employeeKey);
                    $this->addCashOut($rsHeader, $rsCost);
                }
            }
        }
    }
    
    function editData($arrParam)
    {

        $rsHeader = $this->getDataRowById($arrParam['hidId']);
        if ($rsHeader[0]['statuskey'] <> 1) {
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

    function validateForm($arr, $pkey = '')
    {
        $truckingCost = new Service(TRUCKING_SERVICE, 1);
        $hospitalJobOrder = new HospitalJobOrder();
        $item = new Item();
        $asset = new Asset();

        $arrayToJs = parent::validateForm($arr, $pkey);

        $sokey = $arr['hidSOKey'];
        // $pokey = $arr['hidPurchaseOrderKey'];


        $rsSOHeader = $hospitalJobOrder->getDataRowById($sokey);

        $isoutsource = $arr['chkIsOutsource'];
        $trDate = $arr['trDate'];
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
        $codriverCommission = $this->unformatNumber($arr['codriverCommission']);
        $warehousekey = $arr['selWarehouseKey'];
        $categorykey = $arr['hidCategoryKey'];
        $jobtype = $arr['selJobType'];

        $arrItemkey = $arr['hidItemDetailKey']; 
        $arrAssetkey = $arr['hidAssetKey']; 
        $arrQty = $arr['qty']; 
        $arrQtyAsset = $arr['qtyAsset']; 
        $arrAssetExpired = $arr['assetExpired']; 
//        $arrPriceinunit = $arr['priceInUnit'];
        $arrSelUnit = $arr['selUnit'];
        // karena kalo status konfirmasi, jobType disabled
        // for($i=0;$i<count($arrCostKey);$i++){  

        //     $amount =  $this->unformatNumber($requestAmount[$i]); 
        //     if(empty($arrCostKey[$i]) && $amount > 0 ){   
        //         $this->addErrorList($arrayToJs,false, $this->errorMsg['cost'][1]); 
        //         return $arrayToJs;
        //     }

        //     // langsung return agar gk error di bawah
        // }  


        // $rsCost = array(); 
        // //$costKeyCriteria = implode(',',$arrCostKey); 
        // if (!empty($arrCostKey)){  
        //     $rsCost = $truckingCost->searchData('','',true,' and '.$truckingCost->tableName.'.pkey in ('.$this->oDbCon->paramString($arrCostKey,',').')');
        //     $rsCost = array_column($rsCost,null,'pkey');
        // }

        // // if($outsourceCost < $outsourceDownpayment) 
        // //       $this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceWorkOrder'][8]); 

        // utk EDIT form 
        if (!empty($pkey)) {
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['statuskey'] > 2)
                $this->addErrorList($arrayToJs, false, $this->errorMsg[212]);

            if ($rs[0]['statuskey'] > 1) {
                for ($i = 0; $i < count($arrCostKey); $i++) {
                    if ((!empty($employeekey[$i]) && !empty($supplierDetailKey[$i]))) {
                        //$rsCost = $truckingCost->getDataRowById($arrCostKey[$i]);
                        $this->addErrorList($arrayToJs, false, $rsCost[$arrCostKey[$i]]['name'] . '. ' . $this->errorMsg['truckingServiceWorkOrder'][6]);
                    }
                }
            }
        }

        if (empty($sokey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['jobOrder'][1]);
        }
         // if(empty($categorykey) || empty($refdetailkey) || empty($itemkey) || empty($cargotypekey) || empty($jobtype)){
         // 	$this->addErrorList($arrayToJs,false,$this->errorMsg['jobType'][1]);
         // } 


        // if (isset($arr['islinked']) && $arr['islinked']){ 
        //     // utk validasi dr JO
        //     // dari JO, harusnya gk mungkin outsource,.... skrg mungkin :')

        //     if($isoutsource == 1){
        //          if(empty($supplierkey)){
        //             $this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
        //          } 
        //     }else{

        //          if(empty($driverkey)){
        //             $this->addErrorList($arrayToJs,false,$this->errorMsg['driver'][1]);
        //          } 
        //          if(empty($carkey)){
        //             $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][1]);
        //          }

        //     }
            
        // }
        // else{

        //     if($isoutsource == 1){
        //         if(empty($supplierkey)) 
        //             $this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);

        //         $truckingPurchaseOrder = new TruckingPurchaseOrder();
        //         $rsPurchaseHeader = $truckingPurchaseOrder->getDataRowById($pokey); 

        //         if (!empty($rsPurchaseHeader)){
        //             if($rsPurchaseHeader[0]['supplierkey'] <> $supplierkey)
        //                 $this->addErrorList($arrayToJs,false,$this->errorMsg['truckingServiceWorkOrder'][4]); 
        //         }

        //     }else{
        //         if (isset($rs) && $rs[0]['statuskey'] > 1){ 
        //              if(empty($driverkey)) 
        //                 $this->addErrorList($arrayToJs,false,$this->errorMsg['driver'][1]); 
        //              if(empty($carkey)) 
        //                 $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][1]);  

        //             //kalo ad komisi kenek. nama kenek harus diisi
        //             if( $codriverCommission > 0 && empty($codriverkey)){
        //                  $this->addErrorList($arrayToJs,false,$this->errorMsg['codriver'][1]); 
        //             }
        //         } 
        //     } 

        // }

        // //validasi jumlah fixed cost gk boleh melebihi quota
        // $costRate = new CostRate();
        // $rsCostRate = $costRate->getCostDetail($warehousekey, $locationkey, $cargotypekey, $jobtype, $itemkey, 0, $consigneekey);
        // $rsCostRate = array_column($rsCostRate,'price','costkey');

        // //akumulasikan semua biaya yg sama
         $arrRequestAmount = array(); 
         for($i=0;$i<count($requestAmount);$i++){
             $requestAmount[$i] = $this->unformatNumber($requestAmount[$i]);

             if ($requestAmount[$i] <= 0)
                 continue;

             $costkey = $arrCostKey[$i];
             if(!$rsCost[$costkey]['fixedcost'])
                 continue;

             if (!isset($arrRequestAmount[$costkey])) $arrRequestAmount[$costkey] = 0;

             $arrRequestAmount[$costkey] += $requestAmount[$i];
         } 


         foreach($arrRequestAmount as $costkey=>$amount){  
             if (!isset($rsCostRate[$costkey]))
                 continue;

             //!isset($rsCostRate[$costkey]) ||
             if ($amount > $rsCostRate[$costkey] ) 
                  $this->addErrorList($arrayToJs,false, $rsCost[$costkey]['name'] .'. '.$this->errorMsg['truckingServiceWorkOrder'][7] . ' (' . $this->formatNumber($rsCostRate[$costkey]).')');  

         } 

         $arrDetailKeys = array(); 

         for($i=0;$i<count($arrItemkey);$i++) { 
            

                if (!empty($arrItemkey[$i])){
                 $rsItem = $item->getDataRowById($arrItemkey[$i]);
                 if ($this->unFormatNumber($arrQty[$i]) <= 0){ 
                     $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[510]);  
                 }

             /*    $priceMandatory = $this->loadSetting('priceMandatory');
                 if ($priceMandatory == 1 && $this->unFormatNumber($arrPriceinunit[$i]) <= 0){  
                     $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg[511]);  
                 }  */

                 // cek punya konversi unit utk satuan yg dipilih gk  
                 $conv = $item->getConvMultiplier($arrItemkey[$i],$arrSelUnit[$i]);
                 if (empty($conv)){
                     $rsItem = $item->getDataRowById($arrItemkey[$i]);
                     $this->addErrorList($arrayToJs,false,$rsItem[0]['name']. '. ' . $this->errorMsg['itemUnitConversion'][3]); 
                 }  
             }

             // cek ada detail double gk  
             
                 if (in_array($arrItemkey[$i],$arrDetailKeys)){  
                     $rsItem = $item->getDataRowById($arrItemkey[$i]);
                     $this->addErrorList($arrayToJs,false, $rsItem[0]['name'].'. '.$this->errorMsg[215]); 	 
                 }else{ 
                     array_push($arrDetailKeys, $arrItemkey[$i]);
                 } 

         }
     
              $arrDetailAssetKeys = array(); 

    
     for($j=0;$j<count($arrAssetkey);$j++){
        
         
             if (!empty($arrAssetkey[$j])){
                 $rsAsset = $asset->getDataRowById($arrAssetkey[$j]);
            
                 $dateAssetDetail = $arrAssetExpired[$j];
                 
                 $assetExpiredDays = $this->loadSetting('assetExpiredDays');
                 $date = new DateTime();
                 $date->add(new DateInterval('P'.$assetExpiredDays.'D'));
                 $expdateuntil = $date->format('Y-m-d');
                 
                 $dateAsset = str_replace('\'','',$this->oDbCon->paramDate($dateAssetDetail,' / ','Y-m-d'));    
 
                 //tanggal hari ini
                 
                 //tanggal expired barang;
                 $trExpDate = strtotime($dateAsset);
                 
                 //ini tanggal yang terhitung 7 Hari
                 $expDate = strtotime($expdateuntil);

                 if($trExpDate < $expDate){
                    $this->addErrorList($arrayToJs,false,$rsAsset[0]['name'].'. '.$this->errorMsg['asset'][6]);  

                 }
           

             }    
         
             
              /* if (in_array($arrAssetkey[$j],$arrDetailAssetKeys)){  
                     $rsAsset = $asset->getDataRowById($arrAssetkey[$j]);
                     $this->addErrorList($arrayToJs,false, $rsAsset[0]['name'].'. '.$this->errorMsg[215]); 	 
                 }else{ 
                     array_push($arrDetailAssetKeys, $arrAssetkey[$j]);
                 } */
             
         
     }

        return $arrayToJs;
    }

    function getCostDetail($pkey, $costkey = '', $criteria = '', $orderby = ''){
        // gk boleh tambahkan biaya DP Outsouce
        // karena nanti akan pengaruh ke perhitungan biaya di JO dsb
         
        $sql = 'select 
	   			'.$this->tableItem .'.name,
	   			'.$this->tableItem .'.code as itemcode,
	   			'.$this->tableSupplier .'.name as suppliername,
	   			'.$this->tableEmployee .'.name as employeename,
	   			'.$this->tableEmployee .'.cashbankcoakey,
	   			'.$this->tableItem .'.fixedcost,
                '.$this->tableHospitalCostCashOut.'.code as refcashoutcode,
	   			'.$this->tableCost .'.* 
			  from
			  	'.$this->tableName.',
                '.$this->tableItem.',  
			  	'.$this->tableCost.' 
                    left join '.$this->tableSupplier.' on '.$this->tableCost.'.supplierkey = '.$this->tableSupplier.'.pkey
                    left join '.$this->tableEmployee.' on '.$this->tableCost.'.employeekey = '.$this->tableEmployee.'.pkey
                    left join '.$this->tableHospitalCostCashOut.' on '.$this->tableCost.'.refcashoutkey = '.$this->tableHospitalCostCashOut.'.pkey                
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
    function getCarDetail($pkey)
    {

        $sql = 'select
	   			' . $this->tableWorkOrderCarDetail . '.*, 
	   			' . $this->tableItem . '.name as itemname
                
              from
			  	' . $this->tableWorkOrderCarDetail . ', 
                ' . $this->tableName . ',
			  	' . $this->tableItem . ' 
			  where 
                ' . $this->tableName . '.pkey = ' . $this->tableWorkOrderCarDetail . '.refkey and
			  	' . $this->tableWorkOrderCarDetail . '.itemkey = ' . $this->tableItem . '.pkey and 
			  	' . $this->tableWorkOrderCarDetail . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' ';

        //$sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }

    function getItemDetail ($pkey,$criteria='',$orderby =''){


        $sql = 'select
                '.$this->tableWorkOrderItemDetail .'.*, 
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItem.'.sellingprice,
                '.$this->tableItem.'.deftransunitkey,
                '.$this->tableItemUnit.'.name as unitname,
                 baseunit.name as baseunitname
              from
                '.$this->tableWorkOrderItemDetail .',
                '.$this->tableItemUnit.',
                '.$this->tableItemUnit.' baseunit,
                '.$this->tableItem.'
              where
                '.$this->tableWorkOrderItemDetail .'.itemkey = '.$this->tableItem.'.pkey and
                  '.$this->tableWorkOrderItemDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
			  	'.$this->tableItem.'.baseunitkey = baseunit.pkey and
		        '.$this->tableWorkOrderItemDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' .$orderby;
        
        return $this->oDbCon->doQuery($sql);

    }     
    
     function getAssetDetail ($pkey,$criteria='',$orderby =''){
        
        $sql = 'select
                '.$this->tableWorkOrderAssetDetail.'.*, 
                '.$this->tableAsset.'.name as assetname, 
                '.$this->tableAsset.'.code as assetcode,
                '.$this->tableAsset.'.explicensedate
              from
                '.$this->tableWorkOrderAssetDetail .',
                '.$this->tableAsset.'
              where
                '.$this->tableWorkOrderAssetDetail .'.assetkey = '.$this->tableAsset.'.pkey and
		        '.$this->tableWorkOrderAssetDetail .'.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' .$orderby;
        
        return $this->oDbCon->doQuery($sql);

    }  

    function getTruckingCost($id, $criteria = '')
    {

        $sql =
            'select  
                ' . $this->tableHospitalCostCashOutDetail . '.* ,
                ' . $this->tableHospitalCostCashOut . '.pkey as cashoutkey ,
                ' . $this->tableHospitalCostCashOut . '.refkey as cashoutworkorderkey ,
                ' . $this->tableHospitalCostCashOut . '.code as cashoutcode ,
                ' . $this->tableHospitalCostCashOut . '.trdate as cashoutdate,
                ' . $this->tableItem . '.name as costname  
            FROM 
                ' . $this->tableHospitalCostCashOut . ',
                ' . $this->tableHospitalCostCashOutDetail . ',
                ' . $this->tableItem . '
            WHERE
                ' . $this->tableHospitalCostCashOut . '.pkey = ' . $this->tableHospitalCostCashOutDetail . '.refkey and
                ' . $this->tableHospitalCostCashOutDetail . '.costkey = ' . $this->tableItem . '.pkey and
                ' . $this->tableHospitalCostCashOut . '.refkey = ' . $this->oDbCon->paramString($id) . ' and
                ' . $this->tableHospitalCostCashOut . '.statuskey in (2,3)
                ';



        if (!empty($criteria))
            $sql .=  ' ' . $criteria;

        return $this->oDbCon->doQuery($sql);
    }


    function updateGLOutsource($rs)
    {

        if (!USE_GL) return;

        //kalo amount sama gk perlu cancel
        $this->cancelGLByRefkey($rs[0]['pkey'], $this->tableName);

        $hospitalJobOrder = new HospitalJobOrder();
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $supplier = new Supplier();
        $item = new Item();
        $cost = new Service(TRUCKING_SERVICE, 1);

        $warehousekey = $rs[0]['warehousekey'];
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName, array('key'));

        $temp = -1;
        $rsCost = $this->getCostDetail($rs[0]['pkey'], '', ' and ' . $this->tableCost . '.supplierkey <> 0');
        $rsCOA = $coaLink->getCOALink('outsourcecost', $warehouse->tableName, $warehousekey);
        $coakey = $rsCOA[0]['coakey'];

        $isPriceIncludeTax = $rs[0]['ispriceincludetax'];
        $outsourceCost  = ($isPriceIncludeTax) ? ($rs[0]['outsourcecost'] - $rs[0]['taxvalue']) : $rs[0]['outsourcecost'];


        $arr = array();
        $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
        $arr['code'] = 'xxxxx';
        $arr['refkey'] = $rs[0]['pkey'];
        $arr['refTableType'] = $rsKey['key'];
        $arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'], 'd / m / Y');
        $arr['refCode'] = $rs[0]['code'];
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];

        //desc 

        //trucking type
        $arrItemName = array();
        $rsItem = $item->searchDataRow(
            array($item->tableName . '.name'),
            ' and ' . $item->tableName . '.pkey in (' . $this->oDbCon->paramString($rs[0]['itemkey']) . ')'
        );
        $arrItemName = array_merge($arrItemName, array_column($rsItem, 'name'));

        // cost
        $rsItem = $cost->searchDataRow(
            array($cost->tableName . '.pkey', $cost->tableName . '.name', $cost->tableName . '.costcoakey'),
            ' and ' . $cost->tableName . '.pkey in (' . $this->oDbCon->paramString(array_column($rsCost, 'costkey'), ',') . ')'
        );
        $arrItemName = array_merge($arrItemName, array_column($rsItem, 'name'));

        $arrItemCostCOAKey = array_column($rsItem, 'costcoakey', 'pkey');

        $rsJo = $hospitalJobOrder->getDataRowById($rs[0]['refkey']);

        $desc = array();
        array_push($desc, $rsJo[0]['code']);
        array_push($desc, $rsSupplier[0]['name']);
        array_push($desc, $this->lang['truckingFee'] . ' ' . implode(', ', $arrItemName));
        $arr['trDesc'] = implode(chr(13), $desc);

        // cost outsource 
        $temp++;
        $arr['hidCOAKey'][$temp] = $coakey;
        $arr['debit'][$temp] = $outsourceCost;
        $arr['credit'][$temp] = 0;

        $outsourceDownpayment = $rs[0]['outsourcedownpayment'];

        if ($outsourceDownpayment > 0) {
            //$rsCostDP = $cost->getDataRowById( DEFAULT_COST['outsourceDownpayment'] ); 
            $rsCostDP = $coaLink->getCOALink('supplierdownpayment', $warehouse->tableName, $warehousekey);

            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCostDP[0]['coakey'];
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] = $outsourceDownpayment;
        }

        $rsCOA = $coaLink->getCOALink('taxin', $warehouse->tableName, $warehousekey);
        $coakey = $rsCOA[0]['coakey'];

        $temp++;
        $arr['hidCOAKey'][$temp] = $coakey;
        $arr['debit'][$temp] = $rs[0]['taxvalue'];
        $arr['credit'][$temp] = 0;


        $temp++;
        $arr['hidCOAKey'][$temp] =  $supplier->getAPCOAKey($rs[0]['supplierkey'], $warehousekey);
        $arr['debit'][$temp] = 0;
        $arr['credit'][$temp] = $rs[0]['outsourceap']; //+ $rs[0]['taxvalue'];  


        //other cost
        $rsCOA = $coaLink->getCOALink('operationalcost', $warehouse->tableName, $warehousekey);
        $coakey = $rsCOA[0]['coakey'];

        $rsCOA = $coaLink->getCOALink('taxin', $warehouse->tableName, $warehousekey);
        $taxcoakey = $rsCOA[0]['coakey'];

        for ($i = 0; $i < count($rsCost); $i++) {
            if (empty($rsCost[$i]['supplierkey']))
                continue;

            $costkey = $rsCost[$i]['costkey'];

            // karena hutang, sudah pasti langusng jd biaya
            $amount =  $rsCost[$i]['qty'] * $rsCost[$i]['requestamount'];
            $taxAmount = $rsCost[$i]['taxvalue'];

            $temp++;
            $arr['hidCOAKey'][$temp] = $cost->getCostCOAKeyByJobCategory($costkey, $rsJo[0]['categorykey'], $warehousekey); //(!empty($arrItemCostCOAKey[$costkey])) ? $arrItemCostCOAKey[$costkey] : $coakey;
            $arr['debit'][$temp] = $amount;
            $arr['credit'][$temp] = 0;

            $temp++;
            $arr['hidCOAKey'][$temp] = $taxcoakey;
            $arr['debit'][$temp] =  $taxAmount;
            $arr['credit'][$temp] = 0;

            //akun hutang vendor 
            $temp++;
            $arr['hidCOAKey'][$temp] =  $supplier->getAPCOAKey($rsCost[$i]['supplierkey'], $warehousekey);
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] = $amount + $taxAmount;
        }

        $arrayToJs = $generalJournal->addData($arr);

        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);
    }

    function updateGLCommission($rs)
    {
        if (!USE_GL) return;

        //kalo amount sama gk perlu cancel
        $this->cancelGLByRefkey($rs[0]['pkey'], $this->tableName);

        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $employee = new Employee();
        $cost = new Service(TRUCKING_SERVICE, 1);

        $warehousekey = $rs[0]['warehousekey'];

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName, array('key'));
        $rsCOA = $coaLink->getCOALink('commissioncost', $warehouse->tableName, $warehousekey);

        if ($rs[0]['drivercommission'] > 0) {
            $arr = array();
            $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
            $arr['code'] = 'xxxxx';
            $arr['refkey'] = $rs[0]['pkey'];
            $arr['refTableType'] = $rsKey['key'];
            $arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'], 'd / m / Y');
            $arr['refCode'] = $rs[0]['code'];
            $arr['selWarehouseKey'] = $rs[0]['warehousekey'];

            $temp = -1;

            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = $rs[0]['drivercommission'];
            $arr['credit'][$temp] = 0;

            $coakey = $employee->getAPCommissionCOAKey($rs[0]['driverkey'], $warehousekey);

            //akun hutang 
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] = $rs[0]['drivercommission'];

            $arrayToJs = $generalJournal->addData($arr);

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);
        }

        if ($rs[0]['codrivercommission'] > 0) {

            $arr = array();
            $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
            $arr['code'] = 'xxxxx';
            $arr['refkey'] = $rs[0]['pkey'];
            $arr['refTableType'] = $rsKey['key'];
            $arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'], 'd / m / Y');
            $arr['refCode'] = $rs[0]['code'];

            $temp = -1;

            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = $rs[0]['codrivercommission'];
            $arr['credit'][$temp] = 0;

            $coakey = $employee->getAPCommissionCOAKey($rs[0]['codriverkey'], $warehousekey);

            //akun hutang 
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] = $rs[0]['codrivercommission'];

            $arrayToJs = $generalJournal->addData($arr);

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);
        }
        if ($rs[0]['doctorcommission'] > 0) {

            $arr = array();
            $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
            $arr['code'] = 'xxxxx';
            $arr['refkey'] = $rs[0]['pkey'];
            $arr['refTableType'] = $rsKey['key'];
            $arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'], 'd / m / Y');
            $arr['refCode'] = $rs[0]['code'];

            $temp = -1;

            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = $rs[0]['doctorcommission'];
            $arr['credit'][$temp] = 0;

            $coakey = $employee->getAPCommissionCOAKey($rs[0]['doctorkey'], $warehousekey);

            //akun hutang 
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] = $rs[0]['doctorcommission'];

            $arrayToJs = $generalJournal->addData($arr);

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);
        }
    }

    function addCashOut($rsHeader, $rsCost)
    {

        if (empty($rsCost))
            return;

        $hospitalCostCashOut = new HospitalCostCashOut();
        $hospitalJobOrder = new HospitalJobOrder();
        $warehouse = new Warehouse();
        $coaLink = new COALink();
        $employee = new Employee();

        // kalo ad planner dan ad cashbankcoakey, pake kas planner
        $recipientkey = (!empty($rsCost[0]['employeekey'])) ? $rsCost[0]['employeekey'] : $rsHeader[0]['driverkey'];

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
        $rsSO = $hospitalJobOrder->getDataRowById($rsHeader[0]['refkey']);

        for ($i = 0; $i < count($rsCost); $i++) {
            //if(empty($rsCost[$i]['costkey'])) continue;
            $arrParam['hidDetailKey'][$i] = 0;
            $arrParam['refheadercostkey'][$i] = $rsCost[$i]['pkey'];
            $arrParam['hidCostKey'][$i] = $rsCost[$i]['costkey'];
            $arrParam['hidCOAKey'][$i] = $coakey;
            $arrParam['qty'][$i] =  $rsCost[$i]['qty']; // harus bedain, klao di SPK qty nya 0, dari yg model lama, harus diupdate 1 kah ?
            $arrParam['costValue'][$i] =  $rsCost[$i]['requestamount'];
            $arrParam['amount'][$i] = $rsCost[$i]['total'];
            $arrParam['detailDesc'][$i] = '';
            $totalCashOut = $totalCashOut + $rsCost[$i]['total'];
        }

        $arrParam['code'] = 'xxxxxx';
        $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
        $arrParam['refCode'] = $rsHeader[0]['code'];
        $arrParam['hidRefKey2'] = $rsSO[0]['pkey'];
        $arrParam['refCode2'] = $rsSO[0]['code'];
        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
        $arrParam['hidEmployeeKey'] = $recipientkey;
        $arrParam['trDesc'] = '';
        $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
        // $arrParam['islinked'] = 1;
        // gk boleh, karena bisa ganti driver / planner
        $arrParam['subtotal'] = $totalCashOut;
        $arrParam['total'] = $totalCashOut;
        $rsCashOutKey = $this->getTableKeyAndObj($this->tableName, array('key'));
        $arrParam['hidRefTable'] = $rsCashOutKey['key'];


        $rsEmployee = $employee->getDataRowById($recipientkey);
        $arrParam['recipientMobile'] = (isset($rsEmployee[0]['mobile'])) ? $rsEmployee[0]['mobile'] : '';
        $arrParam['recipientBankName'] = (isset($rsEmployee[0]['bankname'])) ? $rsEmployee[0]['bankname'] : '';
        $arrParam['recipientBankAccountName'] = (isset($rsEmployee[0]['bankaccountname'])) ? $rsEmployee[0]['bankaccountname'] : '';
        $arrParam['recipientBankAccountNumber'] = (isset($rsEmployee[0]['bankaccountnumber'])) ? $rsEmployee[0]['bankaccountnumber'] : '';

        $arrayToJs = $hospitalCostCashOut->addData($arrParam);

        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
    }

    function addGroupVendorAP($rsHeader)
    {
        $ap = new AP();
        $supplier = new Supplier();
        $warehouse = new Warehouse();
        $termOfPayment = new TermOfPayment();
        $hospitalJobOrder = new HospitalJobOrder();

        $totalAP = 0;
        $top = 0;
        $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();

        $rsJO = $hospitalJobOrder->getDataRowById($rsHeader[0]['refkey']);

        $arrSupplierAP = array();
        $note = array();
        array_push($note, $rsHeader[0]['code']);


        // table key hanya ambil dr SPK, 
        // table cost gk dimasukan, ini jg blm tentu kepake kalo grouping
        $rsARKey = $ap->getTableKeyAndObj($this->tableName, array('key'));
        $refTableKey = $rsARKey['key'];

        if ($rsHeader[0]['outsourcecost'] > 0) {
            $amount = $rsHeader[0]['outsourceap']; // biar termasuk tax valuenya

            $supplierkey = $rsHeader[0]['supplierkey'];
            $rsTOP = $supplier->getTermOfPayment($supplierkey);
            $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];


            $arrSupplierAP[$supplierkey] = array(
                'amount' => $amount,
                'top' => $top,
                'note' => $note
            );

            array_push($arrSupplierAP[$supplierkey]['note'], $this->lang['truckingFee'] . ': ' . $this->formatNumber($amount));

            //kalo ad DP
            $downpayment = $rsHeader[0]['outsourcedownpayment'];
            if ($downpayment > 0) {
                array_push($arrSupplierAP[$supplierkey]['note'], $this->lang['downpayment'] . ': ' . $this->formatNumber($downpayment));
            }
        }


        $rsCost = $this->getCostDetail($rsHeader[0]['pkey'], '', ' and ' . $this->tableCost . '.supplierkey <> 0');
        foreach ($rsCost as $costRow) {
            $supplierkey = $costRow['supplierkey'];
            if (empty($supplierkey))  continue;

            $amount = $costRow['total'];
            if ($amount <= 0)  continue;


            if (!isset($arrSupplierAP[$supplierkey])) {

                $arrSupplierAP[$supplierkey] = array();

                $rsTOP = $supplier->getTermOfPayment($supplierkey);
                $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];

                $arrSupplierAP[$supplierkey]['amount'] = 0;
                $arrSupplierAP[$supplierkey]['top'] = $top;
                $arrSupplierAP[$supplierkey]['note'] = $note;
            }

            $arrSupplierAP[$supplierkey]['amount'] += $amount;
            array_push($arrSupplierAP[$supplierkey]['note'], 'Biaya ' . $costRow['name'] . ': ' . $this->formatNumber($amount));
        }


        foreach ($arrSupplierAP as $supplierkey => $row) {
            $totalAP += $row['amount'];

            $arrParam = array();

            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidSupplierKey'] = $supplierkey;
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
            $arrParam['hidRefKey2'] = $rsJO[0]['pkey'];
            $arrParam['hidRefCode2'] = $rsJO[0]['code'];
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['hidRefTable'] = $refTableKey;
            $arrParam['amount'] = $this->formatNumber($row['amount']);
            $arrParam['trDesc'] = implode(chr(13), $row['note']);
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P' . $top . 'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');
            $arrParam['createdBy'] = 0;
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            $arrParam['selAPType'] = AP_TYPE['serviceOutsource'];
            $arrParam['selWarehouse'] = $warehousekey;

            $arrayToJs = $ap->addData($arrParam);

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);

            // updaterefcashoutcode
            $newData = $arrayToJs[0]['data'];
            $sql = 'update 
                            ' . $this->tableCost . ' 
                       	 set isrealization = 1,amount = requestamount, refcashoutkey = ' . $this->oDbCon->paramString($newData['pkey']) . ' 
                         where
                            refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' and
                            supplierkey = ' . $this->oDbCon->paramString($supplierkey);
            $this->oDbCon->execute($sql);

            $sql = 'update 
                                ' . $this->tableName . '
                        set refcashoutkey = ' . $this->oDbCon->paramString($newData['pkey']) . ' 
                        where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);

            $this->oDbCon->execute($sql);
        }

        // logol, nanti harus tambahin DP
        if ($totalAP > 0)
            $this->updateGLOutsource($rsHeader);
    }

    function addVendorAP($rsHeader)
    {
        $ap = new AP();
        $supplier = new Supplier();
        $warehouse = new Warehouse();
        $termOfPayment = new TermOfPayment();
        $hospitalJobOrder = new HospitalJobOrder();

        $totalAP = 0;
        $top = 0;
        $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();

        $rsJO = $hospitalJobOrder->getDataRowById($rsHeader[0]['refkey']);

        $note = array();
        array_push($note, $rsHeader[0]['code']);

        $downpayment = $rsHeader[0]['outsourcedownpayment'];
        if ($rsHeader[0]['outsourcecost'] > 0) {
            $amount = $rsHeader[0]['outsourceap']; // + $rsHeader[0]['taxvalue'] ; //- $rsHeader[0]['outsourcedownpayment'];

            //kalo ad DP
            if ($downpayment > 0) {
                array_push($note, $this->lang['truckingFee'] . ': ' . $this->formatNumber($rsHeader[0]['outsourcecost']));
                array_push($note, $this->lang['downpayment'] . ': ' . $this->formatNumber($downpayment));
            }

            if ($amount > 0) {
                $rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
                $topkey = $rsSupplier[0]['termofpaymentkey'];
                $rsTOP = $termOfPayment->getDataRowById($topkey);
                $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];
                $totalAP += $amount;

                $rsARKey = $ap->getTableKeyAndObj($this->tableName, array('key'));
                $arrParam = array();

                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey'];
                $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefKey2'] = $rsJO[0]['pkey'];
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefCode2'] = $rsJO[0]['code'];
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
                $arrParam['hidRefTable'] = $rsARKey['key'];
                $arrParam['amount'] =  $amount;
                $arrParam['trDesc'] = implode(chr(13), $note);
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
                $date = new DateTime($rsHeader[0]['trdate']);
                $date->add(new DateInterval('P' . $top . 'D'));
                $arrParam['dueDate'] = $date->format('d / m / Y');
                $arrParam['createdBy'] = 0;
                $arrParam['overwriteGL'] = 1;
                $arrParam['islinked'] = 1;
                $arrParam['selAPType'] = AP_TYPE['serviceOutsource'];
                $arrParam['selWarehouse'] = $warehousekey;

                $arrayToJs = $ap->addData($arrParam);

                // updaterefcashoutcode
                $newData = $arrayToJs[0]['data'];
                $sql = 'update ' . $this->tableName . ' set refcashoutkey = ' . $this->oDbCon->paramString($newData['pkey']) . ' where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
                $this->oDbCon->execute($sql);

                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
            }
        }


        $rsCost = $this->getCostDetail($rsHeader[0]['pkey'], '', ' and ' . $this->tableCost . '.supplierkey <> 0');
        $rsARKey = $ap->getTableKeyAndObj($this->tableCost, array('key'));
        for ($j = 0; $j < count($rsCost); $j++) {
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
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['hidRefTable'] = $rsARKey['key'];
            $arrParam['amount'] = $this->formatNumber($amount); //$arrParam['amount'][$i];
            $arrParam['trDesc'] = $rsHeader[0]['code'] . '. Biaya ' . $rsCost[$j]['name'];
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P' . $top . 'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');
            $arrParam['createdBy'] = 0;
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            $arrParam['selAPType'] = AP_TYPE['serviceOutsource'];
            $arrParam['selWarehouse'] = $warehousekey;

            $arrayToJs = $ap->addData($arrParam);

            // updaterefcashoutcode
            $newData = $arrayToJs[0]['data'];
            $sql = 'update ' . $this->tableCost . ' set amount = requestamount, refcashoutkey = ' . $this->oDbCon->paramString($newData['pkey']) . ' where pkey = ' . $this->oDbCon->paramString($rsCost[$j]['pkey']);
            $this->oDbCon->execute($sql);

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
        }

        if (($totalAP + $downpayment) > 0)
            $this->updateGLOutsource($rsHeader);
    }


    function addCommissionAP($rsHeader)
    {
        $apEmployeeCommission = new APEmployeeCommission();
        $warehouse = new Warehouse();
        $termOfPayment = new TermOfPayment();
        $hospitalJobOrder = new HospitalJobOrder();

        $totalAP = 0;
        $top = 0;
        $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();

        $rsJO = $hospitalJobOrder->getDataRowById($rsHeader[0]['refkey']);

        $note = array();
        array_push($note, $rsHeader[0]['code']);

        $commissionDateType = $this->loadSetting('driverCommissionBasedOn');
        $commissionDate = ($commissionDateType == 2) ? $this->formatDBDate($rsHeader[0]['trdate']) : date('d / m / Y');
        $commissionDateInDBFormat =  str_replace('\'', '', $this->oDbCon->paramDate($commissionDate, ' / '));

        $rsTOP = $termOfPayment->getDataRowById($topkey);
        $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];

        $date = new DateTime($commissionDateInDBFormat);
        $date->add(new DateInterval('P' . $top . 'D'));
        $commissionDueDate = $date->format('d / m / Y');

        if ($rsHeader[0]['drivercommission'] > 0 || $rsHeader[0]['codrivercommission'] > 0 || $rsHeader[0]['doctorcommission'] > 0) {
            $driverCommissionAmount = $rsHeader[0]['drivercommission'];
            $codriverCommissionAmount = $rsHeader[0]['codrivercommission'];
            $doctorCommissionAmount = $rsHeader[0]['doctorcommission'];

            $rsARKey = $apEmployeeCommission->getTableKeyAndObj($this->tableName, array('key'));

            $totalAP = 0;
            $topkey = $termOfPayment->getDefaultData();
            if ($driverCommissionAmount > 0) {

                $arrParam = array();
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefKey2'] =  $rsJO[0]['pkey'];
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefCode2'] =  $rsJO[0]['code'];
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
                $arrParam['hidRefTable'] = $rsARKey['key'];
                $arrParam['trDesc'] = implode(chr(13), $note);
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
                $sql = 'update ' . $this->tableName . ' set refcashoutdriverkey = ' . $this->oDbCon->paramString($newData['pkey']) . ' where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
                $this->oDbCon->execute($sql);

                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
            }

            if ($codriverCommissionAmount > 0) {

                $arrParam = array();
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefKey2'] =  $rsJO[0]['pkey'];
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefCode2'] =  $rsJO[0]['code'];
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
                $arrParam['hidRefTable'] = $rsARKey['key'];
                $arrParam['trDesc'] = implode(chr(13), $note);
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
                $sql = 'update ' . $this->tableName . ' set refcashoutcodriverkey = ' . $this->oDbCon->paramString($newData['pkey']) . ' where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
                $this->oDbCon->execute($sql);
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
            }

            if ($doctorCommissionAmount > 0) {

                $arrParam = array();
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefKey2'] =  $rsJO[0]['pkey'];
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] =  $rsHeader[0]['code'];
                $arrParam['hidRefCode2'] =  $rsJO[0]['code'];
                $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
                $arrParam['hidRefTable'] = $rsARKey['key'];
                $arrParam['trDesc'] = implode(chr(13), $note);
                $arrParam['trDate'] = $commissionDate;

                $arrParam['createdBy'] = 0;
                $arrParam['overwriteGL'] = 1;
                $arrParam['islinked'] = 1;
                $arrParam['selAPType'] = AP_TYPE['driverCommission'];
                $arrParam['selWarehouse'] = $warehousekey;
                $arrParam['dueDate'] = $commissionDueDate;
                $arrParam['hidEmployeeKey'] = $rsHeader[0]['doctorkey'];

                $arrParam['amount'] =  $doctorCommissionAmount;
                $totalAP += $doctorCommissionAmount;

                $arrayToJs = $apEmployeeCommission->addData($arrParam);


                $newData = $arrayToJs[0]['data'];
                $sql = 'update ' . $this->tableName . ' set refcashoutdoctorkey = ' . $this->oDbCon->paramString($newData['pkey']) . ' where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
                $this->oDbCon->execute($sql);
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
            }
        }

        if ($totalAP > 0) {
            // overwrite tgl
            $rsHeader[0]['trdate'] = $commissionDateInDBFormat;
            $this->updateGLCommission($rsHeader);
        }
    }

    function addCarTurnover($rsHeader)
    {

        // cancel dulu semuanya, karena bisa double update kalo ad realisasi
        // di close diadd, realisasi di ad dlg
        $this->cancelCarTurnover($rsHeader);

        $carTurnover = new CarTurnover();
        $warehouse = new Warehouse();
        $item = new Item();
        $hospitalJobOrder = new HospitalJobOrder();

        $rsJO = $hospitalJobOrder->getDataRowById($rsHeader[0]['refkey']);

        $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();

        $rsCost = $this->getCostDetail($rsHeader[0]['pkey'], '', ' and ' . $this->tableItem . '.reimburse = 0');
        for ($i = 0; $i < count($rsCost); $i++) {
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
            $arrParam['joDate'] = $this->formatDBDate($rsJO[0]['trdate']);
            $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['hidRefTable'] = $rsObjKey['key'];
            $arrParam['hidCarKey'] = $rsHeader[0]['carkey'];
            $arrParam['amount'] = $amount * -1;
            $arrParam['selStatus'] = 1;
            $arrParam['trDesc'] = $rsCost[$i]['name'];

            $arrayToJs =  $carTurnover->addData($arrParam);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
        }

        if (!$rsHeader[0]['isoutsource']) {
            $employee = new Employee();
            $rsObjKey = $this->getTableKeyAndObj($this->tableName);

            if ($rsHeader[0]['drivercommission'] > 0) {

                $driverName = '';
                if (!empty($rsHeader[0]['driverkey'])) {
                    $rsEmployee = $employee->getDataRowById($rsHeader[0]['driverkey']);
                    $driverName = $rsEmployee[0]['name'];
                }

                $arrParam = array();

                $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                $arrParam['refCode'] = $rsHeader[0]['code'];
                $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
                $arrParam['joDate'] =   $this->formatDBDate($rsJO[0]['trdate']);
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['hidRefTable'] = $rsObjKey['key'];
                $arrParam['hidCarKey'] = $rsHeader[0]['carkey'];
                $arrParam['trDesc'] = $this->lang['driverCommission'] . ', ' . $driverName . '.';
                $arrParam['hidRefKey1'] = $rsEmployee[0]['pkey'];
                $arrParam['refCode1'] = $rsEmployee[0]['code'];
                $arrParam['hidRefKey2'] = $rsJO[0]['pkey'];
                $arrParam['refCode2'] = $rsJO[0]['code'];
                $arrParam['amount'] =  $rsHeader[0]['drivercommission'] * -1;
                $arrParam['selStatus'] = 1;

                $arrayToJs =  $carTurnover->addData($arrParam);
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
            }

            if ($rsHeader[0]['codrivercommission'] > 0) {

                $coDriverName = '';
                if (!empty($rsHeader[0]['codriverkey'])) {
                    $rsEmployee = $employee->getDataRowById($rsHeader[0]['codriverkey']);
                    $coDriverName = $rsEmployee[0]['name'];
                }


                $arrParam = array();

                $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
                $arrParam['refCode'] = $rsHeader[0]['code'];
                $arrParam['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
                $arrParam['joDate'] =   $this->formatDBDate($rsJO[0]['trdate']);
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['hidRefTable'] = $rsObjKey['key'];
                $arrParam['hidCarKey'] = $rsHeader[0]['carkey'];
                $arrParam['trDesc'] = $this->lang['codriverCommission'] . ', ' . $coDriverName . '.';
                $arrParam['hidRefKey1'] = $rsEmployee[0]['pkey'];
                $arrParam['refCode1'] = $rsEmployee[0]['code'];
                $arrParam['hidRefKey2'] = $rsJO[0]['pkey'];
                $arrParam['refCode2'] = $rsJO[0]['code'];
                $arrParam['amount'] =  $rsHeader[0]['codrivercommission'] * -1;
                $arrParam['selStatus'] = 1;

                $arrayToJs =  $carTurnover->addData($arrParam);
                if (!$arrayToJs[0]['valid'])
                    throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);
            }
        }
    }

    function reCountOutsourceTax($arrParam)
    {
        $taxValue = 0;
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

            if ($truckingType == 1) {
                $taxValue = $outSourceCost * $taxPercentage / 100;
            } else {
                // model logol
                $arrTaxValue = $arrParam['taxPercentageDetail'];
                $arrQtyDetail = $arrParam['qtyDetail'];
                $arrPriceDetail = $arrParam['priceDetail'];

                for ($i = 0; $i < count($arrTaxValue); $i++) {
                    $tax = $this->unFormatNumber($arrTaxValue[$i]);
                    $qty = $this->unFormatNumber($arrQtyDetail[$i]);
                    $cost = $this->unFormatNumber($arrPriceDetail[$i]);
                    $taxValue += ($qty * $cost * $tax / 100);
                }
            }

            $grandtotal += $taxValue;
        } else {
            // include blm ad di logol
            if ($truckingType == 1) {
                $taxValue = ($taxPercentage / (100 + $taxPercentage)) * $outSourceCost;
            } else {
            }
        }

        //$outsourceAP = $grandtotal - $outSourceDownpayment;

        $reCountResult['taxValue'] = $taxValue;
        $reCountResult['total'] = $grandtotal;
        //$reCountResult['outsourceAP'] = $outsourceAP;

        return $reCountResult;
    }

    function normalizeParameter($arrParam, $trim = false)
    {

        // $cost = new Service(TRUCKING_SERVICE,1);  
        $security = new Security();
        $hospitalJobOrder = new HospitalJobOrder();
        $rsJO = $hospitalJobOrder->getDataRowById($arrParam['hidSOKey']);
        $rsJODetail = $hospitalJobOrder->getDetailById($arrParam['hidSOKey']);

        // $arrParam['hidCustomerKey'] = $rsJO[0]['customerkey'];
        // $arrParam['hidConsigneeKey'] = $rsJO[0]['consigneekey'];

        // // default variable, khususnya utk API 
        // if (!isset($arrParam['selJobType'])){ 

        //     //default
        //     $arrParam['selJobType'] = 1;

        //     if(isset($arrParam['pkey']) && !empty($arrParam['pkey'])){ 
        //         // kalo edit, karena kalo suda konfirmasi, jd disable, gk kekirim nilainya
        //         $rs = $this->getDataRowById($arrParam['pkey']);
        //         $arrParam['selJobType'] = (!empty($rs)) ? $rs[0]['jobtypekey'] : 1 ;
        //     } 

        // }

        // // biasa utk API
        // if(!isset($arrParam['hidCategoryKey']))  $arrParam['hidCategoryKey'] = $rsJO[0]['categorykey']; 
        // if(!isset($arrParam['hidCargoTypeKey'])) $arrParam['hidCargoTypeKey'] = $rsJO[0]['cargotypekey'];
        if (!empty($rsJODetail)) {
            if (!isset($arrParam['hidSODetailKey'])) $arrParam['hidSODetailKey'] = $rsJODetail[0]['pkey'];
            if (!isset($arrParam['hidItemKey']))  $arrParam['hidItemKey'] = $rsJODetail[0]['itemkey'];

            // // pake jml mobil yg disubmit utk test
            // if(isset($arrParam['carRegistration'])){

            //     if(!isset($arrParam['hidServiceDetailKey'])) $arrParam['hidServiceDetailKey'] = array();

            //     $totalVehicle = count($arrParam['carRegistration']);
            //     for($i=0;$i<$totalVehicle;$i++)
            //        if(empty($arrParam['hidServiceDetailKey'][$i]))
            //             $arrParam['hidServiceDetailKey'][$i] = $rsJODetail[0]['itemkey']; 
            // } 
        }

             // harusnya boleh diupdate kalo sudah di save
        if (isset($arrParam['token-item-file-uploader']))
             $arrParam['fileName'] = $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']); 


        // $arrParam['verificationCode'] = $this->generateStrongPassword(6, '', 'lud');

         // realisasi biaya
         if($this->useRealization()){  
             // priceCost gk boleh diganti, karena nanti ad update dr realisasi
             unset($this->arrDataDetail['amount']);  
         }else{ 
            // kalo gk pake realisasi, copy semua
            $arrParam['amount'] = $arrParam['requestAmount'];
         }

        $arrParam['productDescription'] = (isset($arrParam['productDescription'])) ? $arrParam['productDescription'] : '';

        // // cek tipe bisnis

        // $truckingType = $this->loadSetting('truckingType');

         // update tax utk cost
         $rsCostDetail = array();    
         if(isset($arrParam['pkey']) && !empty($arrParam['pkey'])){ 
             $rsCostDetail = $this->getCostDetail($arrParam['pkey']);
             $rsCostDetail = array_column($rsCostDetail,null, 'pkey'); 
         }

        for($i=0;$i<count($arrParam['hidDetailKey']);$i++){   
            $hidDetailKey = $arrParam['hidDetailKey'][$i];
            $arrParam['qtyCostDetail'][$i] = 1;

            $price = (isset($rsCostDetail[$hidDetailKey]) && $rsCostDetail[$hidDetailKey]['isrealization'] == 1) ?  $rsCostDetail[$hidDetailKey]['amount'] : $this->unFormatNumber($arrParam['requestAmount'][$i]);
            $arrParam['totalCostDetail'][$i] = $price ; // harus tergantung realisasi jg 
         }

         //buat model reguler, yg gk ad inputan qty
         for($i=0;$i<count($arrParam['hidDetailKey']);$i++){   
            $hidDetailKey = $arrParam['hidDetailKey'][$i];
            $arrParam['qtyCostDetail'][$i] = 1;

            $price = (isset($rsCostDetail[$hidDetailKey]) && $rsCostDetail[$hidDetailKey]['isrealization'] == 1) ?  $rsCostDetail[$hidDetailKey]['amount'] : $this->unFormatNumber($arrParam['requestAmount'][$i]);
            $arrParam['totalCostDetail'][$i] = $price ; // harus tergantung realisasi jg 
         }


        // if ($truckingType == 1){
        //     // model reguler
        //     unset($arrParam['hidOutsourceVehicleDetailKey']);

            

        // }
        // else{
        //     // model logol
        //     $arrParam['chkIsOutsource'] = 1;

        //     $subTotalAP = 0;

        //     $totalCostDetail = count($arrParam['hidServiceDetailKey']);
        //     for($i=0;$i<$totalCostDetail;$i++){
        //         if($arrParam['qtyCostDetail'][$i] <= 0 )
        //             $arrParam['qtyCostDetail'][$i] = 1;

        //         $price = $this->unFormatNumber($arrParam['priceDetail'][$i]);
        //         $qty = $this->unFormatNumber($arrParam['qtyDetail'][$i]);
        //         $taxPercentageDetail = $this->unFormatNumber($arrParam['taxPercentageDetail'][$i]);
        //         $tax23PercentageDetail = $this->unFormatNumber($arrParam['tax23PercentageDetail'][$i]);

        //         $subTotalDetail = $price * $qty;
        //         $taxValueDetail = $subTotalDetail * ($taxPercentageDetail/100);
        //         $tax23ValueDetail = $subTotalDetail * ($tax23PercentageDetail/100);

        //         $arrParam['taxValueDetail'][$i] = $taxValueDetail;
        //         $arrParam['tax23ValueDetail'][$i] = $tax23ValueDetail; 

        //         //$subTotalDetail += $taxValueDetail;

        //         $arrParam['subtotalDetail'][$i] = $subTotalDetail + $taxValueDetail;

        //         $subTotalAP += $subTotalDetail;
        //     }

        //     // khusus AP Trucking saja
        //     $arrParam['outsourceCost'] = $subTotalAP; 

        //     for($i=0;$i<count($arrParam['hidDetailKey']);$i++){

        //         // kalo ad realisasi, pake realisasi
        //         // ad kemungkinan amount dr realisasi = 0, gpp.
        //         $hidDetailKey = $arrParam['hidDetailKey'][$i];
        //         $price = (isset($rsCostDetail[$hidDetailKey]) && $rsCostDetail[$hidDetailKey]['isrealization'] == 1) ?  $rsCostDetail[$hidDetailKey]['amount'] : $this->unFormatNumber($arrParam['requestAmount'][$i]);
        //         $qty = $this->unFormatNumber($arrParam['qtyCostDetail'][$i]);
        //         $taxPercentageDetail = $this->unFormatNumber($arrParam['taxPercentageCostDetail'][$i]);
        //         $tax23PercentageDetail = $this->unFormatNumber($arrParam['tax23PercentageCostDetail'][$i]);

        //         $subTotalDetail = $price * $qty;
        //         $taxValueDetail = $subTotalDetail * ($taxPercentageDetail/100);
        //         $tax23ValueDetail = $subTotalDetail * ($tax23PercentageDetail/100);

        //         $arrParam['taxValueCostDetail'][$i] = $taxValueDetail;
        //         $arrParam['tax23ValueCostDetail'][$i] = $tax23ValueDetail;
        //         $subTotalDetail += $taxValueDetail;

        //         $arrParam['totalCostDetail'][$i] = $subTotalDetail;

        //     }

        // }


        // if ($arrParam['chkIsOutsource'] == 1){
        //     $arrParam['hidDriverKey'] = 0;
        //     $arrParam['hidCoDriverKey'] = 0;
        //     $arrParam['hidCarKey'] = 0;
        //     $arrParam['hidChassisKey'] = 0;  
        //     $arrParam['driverCommission'] = 0;
        //     $arrParam['codriverCommission'] = 0;

        //     if ($arrParam['outsourceDownpayment'] == 0){
        //         $arrParam['hidDownpaymentRecipientKey'] = 0; 
        //     }else{
        //         if (empty($arrParam['hidDownpaymentRecipientKey']))
        //             $arrParam['hidDownpaymentRecipientKey'] = $arrParam['hidPlannerKey'];

        //     }

        //     $reCountResult = $this->reCountOutsourceTax($arrParam); 
        //     $arrParam['total'] = $reCountResult['total'];
        //     $arrParam['taxValue'] = $reCountResult['taxValue']; 
        //     //$arrParam['outsourceAP'] = $reCountResult['outsourceAP'];
        // }else{
        //     $arrParam['hidSupplierKey'] = 0;
        //     $arrParam['outsourceCost'] = 0;
        //     $arrParam['outsourceDownpayment'] = 0;
        //     //$arrParam['outsourceAP'] = 0;  
        //     $arrParam['total'] = 0;
        // }

        // // hitung ulang nilai AP outsource
        // $outsourceCost = $this->unformatNumber($arrParam['outsourceCost']);
        // $outsourceTaxValue = $this->unformatNumber($arrParam['taxValue']);

        // if($arrParam['chkIncludeTax'] != 1)
        //     $outsourceCost += $outsourceTaxValue; 

        // $outsourceDownpayment = $this->unformatNumber($arrParam['outsourceDownpayment']);

        // // gk perlu agar bisa divalidasi ketika uang muka sudah keluar
        // //if ($outsourceDownpayment > $outsourceCost)  $outsourceDownpayment = $outsourceCost;

        // // kalo include tax gk perlu tambah taxvalue lg
        // $arrParam['outsourceAP'] =  $outsourceCost - $outsourceDownpayment;


        // kalo gk punya akses Cost, detach dr detail agar tdk keupdate
        // agar kalo gk ad akses, tetep bisa proses SPK dan biayany adr JO
        if(!isset($arrParam['_mnv'])) {       
          $hasCostAccess = $security->isAdminLogin($this->costSecurityObject,10);  

          if (!$hasCostAccess){
        	   $arrDataDetails = $this->arrData['pkey'][1]['dataDetail'];
        	   for($i=0;$i<count($arrDataDetails);$i++){
        		   if ($arrDataDetails[$i]['tableName'] == $this->tableCost){  
        			   unset($arrDataDetails[$i]); 
        			   break;
        		   }
        	   }  

        	  $this->arrData['pkey'][1]['dataDetail'] = array_values($arrDataDetails);   
         }  
        }


         $rsDetail = $this->getCostDetail($arrParam['pkey'],'',' and '. $this->tableCost .'.refcashoutkey <> 0 and '. $this->tableCost .'.supplierkey = 0');
         $this->retrieveReadonlyDataRow($arrParam, $rsDetail, $this->arrDataDetail,'refcashoutkey' ); 

        $costList = $arrParam['hidCostKey'];
        $employeeList = $arrParam['hidEmployeeDetailKey'];
        // $suplierList = $arrParam['hidSupplierDetailKey'];

        if ($arrParam['chkIsOutsource'] == 0){

            $defaultRecipient = (!empty($arrParam['hidDriverKey'])) ? $arrParam['hidDriverKey'] : $arrParam['hidPlannerKey'];

            if (!empty($defaultRecipient)){ 
                // update detail cost, penerima harus diisi
                for($i=0;$i<count($costList);$i++){
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


        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }

    function reValidateData($arrParam, $rs)
    {

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


    function afterStatusChanged($rsHeader)
    {
        $hospitalJobOrder = new HospitalJobOrder();

        // update status detail SO

        $this->updateTruckingCostCashOut($rsHeader[0]['pkey']);
        $hospitalJobOrder->updateContainer($rsHeader[0]['refkey']);
        $hospitalJobOrder->updateDetailStatus($rsHeader[0]['refdetailkey']);
        $hospitalJobOrder->updateSalesWorkOrderCost($rsHeader[0]['refkey']);
        $hospitalJobOrder->updateWOActivityDate($rsHeader[0]['refkey']);
    }



    function validateConfirm($rsHeader)
    {

        $employee = new Employee();
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $hospitalJobOrder = new HospitalJobOrder();
        // $this->setLog($rsHeader, true);
        $driverKey = $rsHeader[0]['driverkey'];
        $coDriverCommission = $rsHeader[0]['codrivercommission'];
        $driverCommission = $rsHeader[0]['drivercommission'];
        $carKey = $rsHeader[0]['carkey'];
        $codriverkey = $rsHeader[0]['codriverkey'];

        if(empty($driverKey)){
            $this->addErrorLog(false, $this->errorMsg['paramedic'][1]);
        }
        // if(($coDriverCommission == 0) || ($driverCommission == 0 )){
        //     $this->addErrorLog(false, $this->errorMsg['commission'][2]);
        // }
        if (empty($carKey)) {
            $this->addErrorLog(false, $this->errorMsg['car'][1]);
        }
        // if (empty($codriverkey)) {
        //     $this->addErrorLog(false, $this->errorMsg['codriver'][1]);
        // }


        //cek Job Order statusnya sudah closed / invoiced blm
        $rsSO = $hospitalJobOrder->getDataRowById($rsHeader[0]['refkey']);
        if ($rsSO[0]['statuskey'] >= 4) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsSO[0]['code'] . '</strong>. ' . $this->errorMsg['truckingServiceOrder'][5]);
        }

        if (USE_GL) {
            $arrCOA = array();
            array_push($arrCOA, 'cashbankops', 'payment');
            for ($i = 0; $i < count($arrCOA); $i++) {
                $rsCOA = $coaLink->getCOALink($arrCOA[$i], $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);
                if (empty($rsCOA))
                    $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $arrCOA[$i] . ' ' . $this->errorMsg['coa'][3]);
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


        /*$employee = new Employee();
        $supplier = new Supplier();*/

        $rsCost = $this->getCostDetail($rsHeader[0]['pkey']);
        for ($i = 0; $i < count($rsCost); $i++) {
            if ((!empty($rsCost[$i]['supplierkey']) && !empty($rsCost[$i]['employeekey'])))
                $this->addErrorList($arrayToJs, false, $this->errorMsg['truckingServiceWorkOrder'][6]);
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

    function validateClose($rsHeader)
    {
        // $rsCOA =  $coaLink->getCOALink ('cashbank', $obj->tableName, $id);

        $employee = new Employee();
        $supplier = new Supplier();
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $hospitalJobOrder = new HospitalJobOrder();
        $hospitalCostCashOut = new HospitalCostCashOut();

        $arrayToJs = array();
        $rsCost =  $this->getCostDetail($rsHeader[0]['pkey'], '', ' and ' . $this->tableCost . '.supplierkey <> 0');

        if ($rsHeader[0]['statuskey'] <> 2)
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[204], true);

        //cek Job Order statusnya sudah closed / invoiced blm
        $rsSO = $hospitalJobOrder->getDataRowById($rsHeader[0]['refkey']);
        if ($rsSO[0]['statuskey'] >= 4)
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . '<br><strong>' . $rsSO[0]['code'] . '</strong>.');


        if ($rsHeader[0]['isoutsource'] == 0) {

            if (empty($rsHeader[0]['driverkey'])) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['driver'][1]);
            }

            if (empty($rsHeader[0]['carkey'])) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['car'][1]);
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
        } else {

            if (empty($rsHeader[0]['supplierkey']))
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['supplier'][1]);

            //if($rsHeader[0]['outsourcecost'] <= 0) 
            //    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['truckingServiceWorkOrder'][10]); 

        }

        if (USE_GL) {
            $arrCOA = array();
            array_push($arrCOA, 'cashbankops', 'outsourcecost',  'operationalcost');
            for ($i = 0; $i < count($arrCOA); $i++) {
                $rsCOA = $coaLink->getCOALink($arrCOA[$i], $warehouse->tableName, $rsHeader[0]['warehousekey'], 0);
                if (empty($rsCOA))
                    $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $arrCOA[$i] . ' ' . $this->errorMsg['coa'][3]);
            }
        }




        for ($i = 0; $i < count($rsCost); $i++) {
            $rsSupplier = $supplier->getDataRowById($rsCost[$i]['supplierkey']);
            if (empty($rsSupplier))
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[213]) . ' (' . $this->lang['supplier'] . ')';
        }



        return $arrayToJs;
    }

    function validateBackConfirm($rsHeader)
    {
        $ap = new AP();
        $apEmployeeCommission = new APEmployeeCommission();
        $hospitalJobOrder = new HospitalJobOrder();

        $pkey = $rsHeader[0]['pkey'];

        // vendor AP cost
        $arrAPKey = array();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName);
        array_push($arrAPKey, $rsAPKey['key']);

        $rsAPKey = $ap->getTableKeyAndObj($this->tableCost);
        array_push($arrAPKey, $rsAPKey['key']);

        $rsAPKey = $ap->getTableKeyAndObj($this->tableCost);
        $rsAP = $ap->searchData('', '', true, ' and  ' . $ap->tableName . '.refheaderkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $ap->tableName . '.reftabletype in (' . implode(',', $arrAPKey) . ')  and (' . $ap->tableName . '.statuskey in(2,3))');
        if (!empty($rsAP))
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['ap'][2]);

        $rsAPCommissionKey = $apEmployeeCommission->getTableKeyAndObj($this->tableName);
        $tableCommissionkey = $rsAPCommissionKey['key'];

        $rsAPCommission = $apEmployeeCommission->searchData('', '', true, ' and  ' . $apEmployeeCommission->tableName . '.refheaderkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $apEmployeeCommission->tableName . '.reftabletype = ' . $tableCommissionkey . '  and (' . $apEmployeeCommission->tableName . '.statuskey in(2,3))');
        if (!empty($rsAPCommission))
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['apCommission'][2]);

        // cek status JO dulu
        $rsSO = $hospitalJobOrder->getDataRowById($rsHeader[0]['refkey']);
        if ($rsSO[0]['statuskey'] >= 4)
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsSO[0]['code'] . '</strong>. ' . $this->errorMsg['truckingServiceOrder'][5]);
    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {

        // SPK boleh cancel, tidak tergantung sudah keluar duit atau blm
        // kecuali ad piutang vendor yg sudah dibayarkan sebagian 

        $ap = new AP();
        $hospitalJobOrder = new HospitalJobOrder();
        $hospitalCostCashOut = new HospitalCostCashOut();
        $cashBankRealization = new CashBankRealization();
        $apEmployeeCommission = new APEmployeeCommission();


        $pkey = $rsHeader[0]['pkey'];

        parent::validateCancel($pkey, $autoChangeStatus);

        // khusus cash out, harus cek pake reftable jg,
        // cek cash out sudah ad yg konfirmasi / closed blm

        // PINDAHKAN KE REALISASI SAJA NANTI
        $rsCashOutKey = $this->getTableKeyAndObj($this->tableName);
        $rsCashOut = $hospitalCostCashOut->searchData('', '', true, ' and ' . $hospitalCostCashOut->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $hospitalCostCashOut->tableName . '.reftabletype = ' . $this->oDbCon->paramString($rsCashOutKey['key']) . ' and ' . $hospitalCostCashOut->tableName . '.statuskey in (2,3)');
        if (!empty($rsCashOut)) {
            $errMsg = array();
            foreach ($rsCashOut as $cashOutRow)
                array_push($errMsg, '<b>' . $cashOutRow['code'] . '</b>, ' . $this->errorMsg[225]);

            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . '<br>' . implode('<br>', $errMsg));
        }

        //cek Job Order statusnya sudah closed blm
        $rsSO = $hospitalJobOrder->getDataRowById($rsHeader[0]['refkey']);
        if ($rsSO[0]['statuskey'] >= 3)
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsSO[0]['code'] . '</strong>, ' . $this->errorMsg[226]);



        // vendor AP cost
        $arrAPKey = array();
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName);
        array_push($arrAPKey, $rsAPKey['key']);

        $rsAPKey = $ap->getTableKeyAndObj($this->tableCost);
        array_push($arrAPKey, $rsAPKey['key']);

        $rsAP = $ap->searchData('', '', true, ' and  ' . $ap->tableName . '.refheaderkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $ap->tableName . '.reftabletype in (' . implode(',', $arrAPKey) . ')  and (' . $ap->tableName . '.statuskey in(2,3))');
        if (!empty($rsAP))
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['ap'][2]);

        $rsAPCommissionKey = $apEmployeeCommission->getTableKeyAndObj($this->tableName);
        $tableCommissionkey = $rsAPCommissionKey['key'];

        $rsAPCommission = $apEmployeeCommission->searchData('', '', true, ' and  ' . $apEmployeeCommission->tableName . '.refheaderkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $apEmployeeCommission->tableName . '.reftabletype = ' . $tableCommissionkey . '  and (' . $apEmployeeCommission->tableName . '.statuskey in(2,3))');
        if (!empty($rsAPCommission))
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['apCommission'][2]);
    }



    function backConfirmTrans($rsHeader)
    {

        $apGroupingType = $this->loadSetting('ungroupWorkOrderAP');
        if ($apGroupingType == 2)
            $this->cancelGroupVendorAP($rsHeader);
        else
            $this->cancelVendorAP($rsHeader);

        $this->cancelCarTurnover($rsHeader);
        $this->cancelCommissionAP($rsHeader);
    }


    function confirmTrans($rsHeader)
    {

        // kalo dr selesai
    }


    function closeTrans($rsHeader)
    {
        //if($rsHeader[0]['isoutsource'] == 1)  

        $apGroupingType = $this->loadSetting('ungroupWorkOrderAP');

        if ($apGroupingType == 2)
            $this->addGroupVendorAP($rsHeader);
        else
            $this->addVendorAP($rsHeader);

        $itemMovement = new ItemMovement();  
        $item = new Item();  
        
        $rsDetail = $this->getItemDetail($rsHeader[0]['pkey']);
        
        //search untuk cek tipe item karena belum di save ke db 
        $arrItemKey = array_column($rsDetail,'itemkey');
        $rsItem = $item->searchDataRow(
                
            array($item->tableName . '.pkey'),
            '   and ' . $item->tableName . '.pkey in (' . $this->oDbCon->paramString(implode(', ',$arrItemKey)) . ')
            and ' . $item->tableName . '.statuskey = 1 
            and ' . $item->tableName . '.itemtype = '.$this->oDbCon->paramString(SERVICE).'
            
            '
        );
        
        $rsItem = array_column($rsItem,'pkey');
        for($i=0;$i<count($rsDetail); $i++){	
            
            if(empty($rsDetail[$i]['itemkey']) || !in_array($rsDetail[$i]['itemkey'],$rsItem))  continue;
            $itemMovement->updateItemMovement($rsHeader[0]['pkey'],$rsDetail[$i]['itemkey'],-$rsDetail[$i]['qty'], $rsDetail[$i]['costinbaseunit'] ,$this->tableName, $rsHeader[0]['warehousekey'], $note,$rsHeader[0]['trdate']);
        }

        $this->addCommissionAP($rsHeader);
        $this->addCarTurnover($rsHeader);
    }

    function cancelTrans($rsHeader, $copy)
    {
        $warehouse = new Warehouse();
        $employee = new Employee();

        $this->cancelCashOut($rsHeader[0]['pkey']);

        $apGroupingType = $this->loadSetting('ungroupWorkOrderAP');
        if ($apGroupingType == 2)
            $this->cancelGroupVendorAP($rsHeader);
        else
            $this->cancelVendorAP($rsHeader);

        $itemMovement = new ItemMovement();  
        $itemMovement->cancelMovement($rsHeader[0]['pkey'],$this->tableName); 
        

        $this->cancelCarTurnover($rsHeader);
        $this->cancelCommissionAP($rsHeader);

        if ($copy)
            $this->copyDataOnCancel($rsHeader[0]['pkey']);
    }

    function cancelCashOut($workOrderKey, $employeekey = '')
    {

        // delete cash out
        $hospitalCostCashOut = new HospitalCostCashOut();
        $rsCashOutKey =  $this->getTableKeyAndObj($this->tableName);
        $employeeCriteria = '';

        if ($employeekey === 0 || $employeekey !== '')
            $employeeCriteria = ' and ' . $hospitalCostCashOut->tableName . '.employeekey = ' . $this->oDbCon->paramString($employeekey) . ' ';

        $rsCashOut = $hospitalCostCashOut->searchData('', '', true, ' and ' . $hospitalCostCashOut->tableName . '.refkey = ' . $this->oDbCon->paramString($workOrderKey) . '  and reftabletype =  ' . $this->oDbCon->paramString($rsCashOutKey['key']) . ' and ' . $hospitalCostCashOut->tableName . '.statuskey = 1 ' . $employeeCriteria);
        for ($i = 0; $i < count($rsCashOut); $i++)
            $hospitalCostCashOut->changeStatus($rsCashOut[$i]['pkey'], 4, '', false, true);
    }

    function cancelCarTurnover($rsHeader)
    {
        $carTurnover = new CarTurnover();
        $rsObjKey = $this->getTableKeyAndObj($this->tableName);
        $carTurnover->cancelMovement($rsHeader[0]['pkey'], $rsObjKey['key']);
    }

    function cancelGroupVendorAP($rsHeader)
    {
        $ap = new AP();

        $rsWorkOrderKey = $ap->getTableKeyAndObj($this->tableName, array('key'));
        $rsAP = $ap->searchDataRow(
            array($ap->tableName . '.pkey', $ap->tableName . '.refkey'),
            ' and ' . $ap->tableName . '.refheaderkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' 
                                      and reftabletype in (' . $this->oDbCon->paramString($rsWorkOrderKey['key']) . ') 
                                      and ' . $ap->tableName . '.statuskey = 1'
        );

        foreach ($rsAP as $apRow) {
            //cancel AP
            $ap->changeStatus($apRow['pkey'], 4, '', false, true);

            $sql = 'update ' . $this->tableCost . ' set  amount = 0, refcashoutkey = 0, isrealization = 0 where refcashoutkey = ' . $this->oDbCon->paramString($apRow['pkey']);
            $this->oDbCon->execute($sql);

            $sql = 'update ' . $this->tableName . ' set refcashoutkey = 0 where refcashoutkey = ' . $this->oDbCon->paramString($apRow['pkey']);
            $this->oDbCon->execute($sql);
        }

        $this->cancelGLByRefkey($rsHeader[0]['pkey'], $this->tableName);
    }




    function cancelVendorAP($rsHeader)
    {
        $ap = new AP();
        $arrAPKey = array();

        $rsWorkOrderKey = $ap->getTableKeyAndObj($this->tableName);
        array_push($arrAPKey, $rsWorkOrderKey['key']);

        $rsCostKey = $ap->getTableKeyAndObj($this->tableCost);
        array_push($arrAPKey, $rsCostKey['key']);

        $rsAP = $ap->searchData('', '', true, ' and  ' . $ap->tableName . '.refheaderkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' and ' . $ap->tableName . '.reftabletype in (' . implode(',', $arrAPKey) . ') and ' . $ap->tableName . '.statuskey = 1');
        for ($i = 0; $i < count($rsAP); $i++) {
            $ap->changeStatus($rsAP[$i]['pkey'], 4, '', false, true);

            // updaterefcashoutcode 

            $amountField = '';
            if ($rsAP[$i]['reftabletype'] == $rsWorkOrderKey['key']) {
                $tableName = $this->tableName;
            } else {
                // ini untuk cost di detail
                $tableName = $this->tableCost;
                $amountField = 'amount = 0,';
            }

            //$tableName = ($rsAP[$i]['reftabletype'] == $rsWorkOrderKey['key']) ? $this->tableName : $this->tableCost;

            $sql = 'update ' . $tableName . ' set ' . $amountField . ' refcashoutkey = 0 where pkey = ' . $this->oDbCon->paramString($rsAP[$i]['refkey']);
            $this->oDbCon->execute($sql);
        }

        $this->cancelGLByRefkey($rsHeader[0]['pkey'], $this->tableName);
    }


    function cancelCommissionAP($rsHeader)
    {
        $apEmployeeCommission = new APEmployeeCommission();

        $rsWorkOrderKey = $this->getTableKeyAndObj($this->tableName);
        $tablekeyWO = $rsWorkOrderKey['key'];

        $rsAPCommission = $apEmployeeCommission->searchData('', '', true, ' and  ' . $apEmployeeCommission->tableName . '.refheaderkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' and reftabletype = ' . $tablekeyWO . ' and ' . $apEmployeeCommission->tableName . '.statuskey = 1');
        for ($i = 0; $i < count($rsAPCommission); $i++) {
            $apEmployeeCommission->changeStatus($rsAPCommission[$i]['pkey'], 4, '', false, true);

            // updaterefcashoutcode 

            $sql = 'update ' . $this->tableName . ' set refcashoutdriverkey = 0, refcashoutcodriverkey = 0 where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
            $this->oDbCon->execute($sql);
        }

        $this->cancelGLByRefkey($rsHeader[0]['pkey'], $this->tableName);
    }

    function getMonthlySummary($startPeriod = '', $endPeriod = '',  $criteria = '', $groupby = '', $reportType = 1)
    {

        // DATE FORMAT => d / m / Y
        if (empty($startPeriod)) $startPeriod = DEFAULT_EMPTY_DATE;
        if (empty($endPeriod)) $endPeriod = date('d / m / Y');


        // be aware, perubahan group harus update ke concat index jg
        if (empty($groupby)) {
            $groupby = ($reportType == 1) ? 'driverkey' : 'carkey';
            $groupby .= ', year(trdate), month(trdate)';
        }

        $periodIndexField = ($reportType == 1) ? 'driverkey' : 'carkey';

        $sql  = '
                select 
                    ' . $this->tableEmployee . '.name as drivername,
                    ' . $this->tableWarehouse . '.name as warehousename,
                    concat(' . $this->tableCar . '.code, \' - \', ' . $this->tableCar . '.policenumber) as carname ,
                    driverkey,
                    carkey,
                    concat(' . $periodIndexField . ',\'-\',DATE_FORMAT(trdate, \'%c%Y\'))  as periodindex, 
                    month(trdate) as month,   
                    year(trdate) as year, 
                    count(' . $this->tableName . '.pkey) as totaltrip , 
                    sum(' . $this->tableName . '.sellingprice) as sellingprice 
                from 
                    ' . $this->tableName . ', 
                    ' . $this->tableCar . ', 
                    ' . $this->tableWarehouse . ', 
                    ' . $this->tableEmployee . ' 
                where  
                    ' . $this->tableName . '.statuskey = 3 and
                    ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey and
                    ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and
                    ' . $this->tableName . '.driverkey = ' . $this->tableEmployee . '.pkey';

        $sql .= ' and  trdate between ' . $this->oDbCon->paramDate($startPeriod . ' 00:00:00', ' / ') . ' and LAST_DAY(' . $this->oDbCon->paramDate($endPeriod . ' 23:59:59', ' / ') . ')';

        if (!empty($criteria))
            $sql .= ' ' . $criteria;

        $sql .= ' group by ' . $groupby;

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function getOutsourceMonthlySummary($startPeriod = '', $endPeriod = '',  $criteria = '', $groupby = '', $reportType = 1)
    {

        // DATE FORMAT => d / m / Y

        if (empty($startPeriod)) $startPeriod = DEFAULT_EMPTY_DATE;
        if (empty($endPeriod)) $endPeriod = date('d / m / Y');


        // be aware, perubahan group harus update ke concat index jg
        if (empty($groupby))
            $groupby = 'supplierkey , year(trdate), month(trdate)';

        $periodIndexField = 'supplierkey';

        $sql  = '
                select 
                    ' . $this->tableSupplier . '.pkey,
                    ' . $this->tableSupplier . '.name,
                    ' . $this->tableSupplier . '.code,
                    ' . $this->tableWarehouse . '.name as warehousename,  
                    concat(' . $periodIndexField . ',\'-\',DATE_FORMAT(trdate, \'%c%Y\'))  as periodindex, 
                    month(trdate) as month,   
                    year(trdate) as year, 
                    count(' . $this->tableName . '.pkey) as totaltrip , 
                    sum(' . $this->tableName . '.sellingprice) as sellingprice , 
                    sum(' . $this->tableName . '.outsourcecost) as outsourcecost 
                from 
                    ' . $this->tableName . ', 
                    ' . $this->tableSupplier . ', 
                    ' . $this->tableWarehouse . '
                where  
                    ' . $this->tableName . '.statuskey = 3 and
                    ' . $this->tableName . '.supplierkey = ' . $this->tableSupplier . '.pkey and
                    ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey';

        $sql .= ' and  trdate between ' . $this->oDbCon->paramDate($startPeriod . ' 00:00:00', ' / ') . ' and LAST_DAY(' . $this->oDbCon->paramDate($endPeriod . ' 23:59:59', ' / ') . ')';

        if (!empty($criteria))
            $sql .= ' ' . $criteria;

        $sql .= ' group by ' . $groupby;

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function generateCarSchedule($carkey = array(), $criteria = '')
    {

        $sql = 'select 
                ' . $this->tableName . '.code,
                ' . $this->tableName . '.trdate,
                date(' . $this->tableName . '.stuffingdatetime) as stuffingdatetimeshort, 
                 concat(' . $this->tableName . '.code, \' - \', ' . $this->tableName . '.trdate) as periode ,
                ' . $this->tableEmployee . '.name as drivername ,
                ' . $this->tableJobOrderHeader . '.code as serviceordercode,
                ' . $this->tableCustomer . '.name as customername,
                ' . $this->tableCar . '.pkey,
                ' . $this->tableCar . '.policenumber
            from
                ' . $this->tableName . '
			         left join ' . $this->tableEmployee . ' on ' . $this->tableName . '.driverkey = ' . $this->tableEmployee . '.pkey ,                
                ' . $this->tableJobOrderHeader . ',
                ' . $this->tableCustomer . ',
                ' . $this->tableCar . '
            where
                ' . $this->tableName . '.statuskey in (2,3) and
                ' . $this->tableName . '.refkey = ' . $this->tableJobOrderHeader . '.pkey  and  
                ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey and
                ' . $this->tableJobOrderHeader . '.customerkey = ' . $this->tableCustomer . '.pkey';

        if (!empty($carkey))
            $sql .=  '  and ' . $this->tableName . '.carkey in (' . $this->oDbCon->paramString($carkey, ',') . ')';

        if (!empty($criteria))
            $sql .= ' ' . $criteria;

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    function updateDataAfterRealization($rsHeader, $rsDetail, $action)
    {
        // $action => 1 : confirm, 2: reverse confirm

        $id = $rsHeader[0]['refkey2'];
        $realizationkey = $rsHeader[0]['pkey'];

        // REALISASI BERASAL DR BIAYA INHOUSE, JD ASUMSI TIDAK AD PPN DAN PPH

        // update biaya yagn langsung ditambahkan dr realisasi
        //hapus semua biaya yg berasal dr realisasi
        $sql = 'delete from ' . $this->tableCost . ' where realizationkey = ' . $this->oDbCon->paramString($realizationkey) . ' and refcashoutkey = 0 and refkey = ' . $this->oDbCon->paramString($id);
        $this->oDbCon->execute($sql);

        foreach ($rsDetail as $row) {

            $realCostValue = 0;
            $isrealization = 0;

            if ($action == 1) {
                $realCostValue = $row['realcostvalue'];
                $isrealization = 1;

                // add biaya yagn dr realisasi
                // sementar aasumsi yg terima adjustment selalu KARYAWAN
                if ($row['settlementtypekey'] == 0) {
                    //insert ulang biaya dar realisasi
                    $sql = 'insert into ' . $this->tableCost . ' (qty,refkey,costkey ,amount, employeekey, isrealization, realizationkey, total ) 
                            values  (' . $this->oDbCon->paramString($row['qty']) . ',
                                     ' . $this->oDbCon->paramString($id) . ',
                                     ' . $this->oDbCon->paramString($row['costkey']) . ',
                                     ' . $this->oDbCon->paramString($realCostValue) . ',
                                     ' . $this->oDbCon->paramString($rsHeader[0]['employeekey']) . ',
                                     1,
                                     ' . $this->oDbCon->paramString($realizationkey) . ',
                                     ' . $this->oDbCon->paramString($row['amount']) . '
                                     ) ';
                    $this->oDbCon->execute($sql);
                }
            }

            $sql = 'update  ' . $this->tableCost . '  
                    set  
                        amount = ' . $this->oDbCon->paramString($realCostValue) . ', 
                        isrealization = ' . $this->oDbCon->paramString($isrealization) . ' , 
                        realizationkey = ' . $this->oDbCon->paramString($realizationkey) . ' , 
                        total = ' . $this->oDbCon->paramString($row['amount']) . ' 
                    where  ' . $this->tableCost . '.pkey = ' . $this->oDbCon->paramString($row['refkey2']);
            $this->oDbCon->execute($sql);
        }



        $hospitalJobOrder = new HospitalJobOrder();
        $rs = $this->getDataRowById($id);
        $hospitalJobOrder->updateSalesWorkOrderCost($rs[0]['refkey']);

        // hanya kalo statuskey nya SELESAI
        if ($rs[0]['statuskey'] == 3)
            $this->addCarTurnover($rs);
    }

    function getWorkProgress($statuskey = array(2))
    {

        $sql = ' select
                    ' . $this->tableJobOrderHeader . '.code as socode,
                    ' . $this->tableName . '.routefrom,
                    ' . $this->tableName . '.routeto,
                    ' . $this->tableJobOrderHeader . '.donumber,
                    ' . $this->tableCustomer . '.name as customername,
                    ' . $this->tableName . '.pkey as wokey,
                    ' . $this->tableName . '.code as wocode,
                    ' . $this->tableCar . '.policenumber,
                    ' . $this->tableCar . '.gpstrackerid,
                    ' . $this->tableEmployee . '.name as drivername
                from  
                    ' . $this->tableName . ' 
                        left join  ' . $this->tableCar . ' on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey
                        left join  ' . $this->tableEmployee . ' on ' . $this->tableName . '.driverkey = ' . $this->tableEmployee . '.pkey,
                    ' . $this->tableCustomer . ',
                    ' . $this->tableJobOrderHeader . '
                where
                    ' . $this->tableName . '.statuskey in (' . implode(',', $statuskey) . ') and
                    ' . $this->tableName . '.refkey = ' . $this->tableJobOrderHeader . '.pkey and
                    ' . $this->tableJobOrderHeader . '.customerkey = ' . $this->tableCustomer . '.pkey and
                    ' . $this->tableName . '.carkey <> \'\'';

        $sql .= $this->getWarehouseCriteria();
        $sql .= ' order by socode asc, wocode asc, ' . $this->tableName . '.trdate asc';

        return $this->oDbCon->doQuery($sql);
    }

    function updateVehicleByRegistrationNumber($id, $employeekey, $carRegistrationNumber)
    {

        try {
            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            $carRegistrationNumber = trim($carRegistrationNumber);

            // validasi
            $arrayToJs = array();

            // pastikan SPK adalah SPK sopir yang masih aktif  
            $rsWorkOrder = $this->searchData($this->tableName . '.pkey', $id, true, ' and ' . $this->tableName . '.driverkey = ' . $this->oDbCon->paramString($employeekey) . ' and ' . $this->tableName . '.statuskey in (1,2)');

            // jika kode verifikasi tidak cocok
            if (empty($rsWorkOrder))
                $this->addErrorList($arrayToJs, false, $this->errorMsg[213]);

            if (empty($carRegistrationNumber)) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg['car'][1]);
            } else {
                $car = new Car();
                $rsCar = $car->searchData($car->tableName . '.policenumber', $carRegistrationNumber);
                if (empty($rsCar))
                    $this->addErrorList($arrayToJs, false, $this->errorMsg['car'][9]);
            }

            if (!empty($arrayToJs))  return $arrayToJs;

            $sql = 'update 
                            ' . $this->tableName . '
                        set carkey = (select pkey from ' . $this->tableCar . ' where policenumber = ' . $this->oDbCon->paramString($carRegistrationNumber) . ')  
                        where 
                            pkey = ' . $this->oDbCon->paramString($id);

            $this->oDbCon->execute($sql);
            $this->setTransactionLog(UPDATE_DATA, $id);

            $this->oDbCon->endTrans();
            $this->addErrorList($arrayToJs, true, $this->lang['dataHasBeenSuccessfullyUpdated']);
        } catch (Exception $e) {
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs, false, $e->getMessage());
        }

        return $arrayToJs;
    }


    function takeWorkOrder($workOrderCode, $employeekey, $verificationCode)
    {

        try {
            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            $verificationCode = trim($verificationCode);
            $workOrderCode = trim($workOrderCode);


            // validasi
            $arrayToJs = array();

            $rsWorkOrder = $this->searchData($this->tableName . '.code', $workOrderCode, true, ' and ' . $this->tableName . '.statuskey in (1,2)');

            // jika kode verifikasi tidak cocok
            if (empty($rsWorkOrder))
                $this->addErrorList($arrayToJs, false, $this->errorMsg[213]);

            if (empty($verificationCode) || $rsWorkOrder[0]['verificationcode'] <> $verificationCode)
                $this->addErrorList($arrayToJs, false, $this->lang['verificationFailed']);



            if (!empty($arrayToJs))  return $arrayToJs;

            $id = $rsWorkOrder[0]['pkey'];

            $sql = 'update 
                            ' . $this->tableName . '
                        set 
                            driverkey = ' . $this->oDbCon->paramString($employeekey) . ' 
                        where 
                            pkey = ' . $this->oDbCon->paramString($id);

            $this->oDbCon->execute($sql);
            $this->setTransactionLog(UPDATE_DATA, $id);

            $this->oDbCon->endTrans();
            $this->addErrorList($arrayToJs, true, $this->lang['dataHasBeenSuccessfullyUpdated']);
        } catch (Exception $e) {
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs, false, $e->getMessage());
        }

        return $arrayToJs;
    }

    /* function getQueryForList(){

          $sql = '
     		SELECT '.$this->tableName.'.* ,  
     		   concat('.$this->tableName.'.routefrom, \' - \', '.$this->tableName.'.routeto) as route ,
     		   '.$this->tableStatus.'.status as statusname ,  
     		   '.$this->tableCar.'.policenumber ,   
     		   '.$this->tableCustomer.'.name as customername ,
                '.$this->tableServiceOrderHeader.'.code as serviceordercode,
                '.$this->tableServiceOrderHeader.'.shipmentnumber,
                '.$this->tableServiceOrderHeader.'.donumber,
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableWarehouse.'.code as warehousecode, 
                '.$this->tableItem.'.name as containername,
                '.$this->tableDepot.'.name as depotname,
                '.$this->tableTerminal.'.name as terminalname,   
                '.$this->tableHospitalJob.'.name as jobtypename, 
                '.$this->tableLocation.'.name as locationname, 
                outsource_supplier.code as outsourcesuppliercode,
                outsource_supplier.name as outsourcesuppliername,
                IF(isoutsource=1, "TL", "") as TL,
                IF(isoutsource=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as outsourceicon
     		FROM 
                 '.$this->tableStatus.', 
                 '.$this->tableServiceOrderHeader.',
                 '.$this->tableItem.',   
                 '.$this->tableCustomer.',    
                 '.$this->tableWarehouse.',   
                 '.$this->tableHospitalJob.',   
                 '.$this->tableName.'  
                     left join '.$this->tableDepot.' on '.$this->tableName.'.depotkey = '.$this->tableDepot.'.pkey 
                     left join '.$this->tableTerminal.' on '.$this->tableName.'.terminalkey = '.$this->tableTerminal.'.pkey   
                     left join '.$this->tableEmployee.' on '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey
                     left join '.$this->tableCar.' on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey     
                     left join '.$this->tableLocation.' on '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey
                     left join '.$this->tableSupplier.' outsource_supplier on '.$this->tableName.'.supplierkey = outsource_supplier.pkey   
     		WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                   '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                   '.$this->tableName.'.refkey = '.$this->tableJobOrderHeader.'.pkey  and   
                   '.$this->tableName.'.itemkey  = '.$this->tableItem.'.pkey and
                   '.$this->tableName.'.jobtypekey  = '.$this->tableHospitalJob.'.pkey and
                   '.$this->tableServiceOrderHeader.'.customerkey = '.$this->tableCustomer.'.pkey 
     	' .$this->criteria ; 


         $sql .=  $this->getWarehouseCriteria() ;

        return $sql;

     }*/

    function getTotalOutsource($statuskey = '')
    {
        $sql = 'select coalesce(count(pkey),0) as total from ' . $this->tableName . ' where isoutsource = 1';
        if (!empty($statuskey))
            $sql .= ' and statuskey = ' . $this->oDbCon->paramString($statuskey);

        $rs = $this->oDbCon->doQuery($sql);

        return $rs[0]['total'];
    }

    function getVehicleDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   			' . $this->tableWorkOrderCarDetail . '.*,
                ' . $this->tableItem . '.code as itemcode,
                ' . $this->tableItem . '.name as itemname 
              from
			  	' . $this->tableWorkOrderCarDetail . ', 
                ' . $this->tableItem . ' 
			  where
			  	' . $this->tableWorkOrderCarDetail . '.itemkey = ' . $this->tableItem . '.pkey and  
			  	refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        $sql .= ' order by pkey asc';

        return $this->oDbCon->doQuery($sql);
    }
    
    function getQueueInformation($criteria,$orderBy,$limit){
        
        $sql = 'select 
                    '.$this->tableName.'.*,
                    '.$this->tableLocation.'.name as locationname,
                    '.$this->tableCar.'.policenumber,
                    '.$this->tableStatus.'.status as statusname,
                    driver.name as drivername,
                    codriver.name as codrivername
                from
                    '.$this->tableStatus.',    
                    '.$this->tableWarehouse.',  
                    '.$this->tableName.'
                        left join '.$this->tableEmployee.' driver on '.$this->tableName.'.driverkey = driver.pkey
                        left join '.$this->tableCar.' on '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey
                        left join '.$this->tableEmployee.' codriver on '.$this->tableName.'.codriverkey = codriver.pkey,
                    '.$this->tableJobOrderHeader . '
                        left join '.$this->tableLocation.' on '.$this->tableJobOrderHeader.'.caselocationkey = '.$this->tableLocation.'.pkey
                where
                    '.$this->tableName.'.statuskey in (1,2) and
                    '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and                    
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and                    
                    '.$this->tableName.'.refkey = '.$this->tableJobOrderHeader.'.pkey
            ';
        
        $sql .= $criteria;
        
        if(!empty($orderBy))
            $sql .= $orderBy;
        
        if(!empty($limit))
            $sql .= $limit;
        
        return $this->oDbCon->doQuery($sql);
        
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

    function getDetailForAPI($arrKey, $arrIndex = array())
    {
        $rsDetailsCol = array();

        $rsDetails = $this->getVehicleDetailWithRelatedInformation($arrKey);
        $rsDetails = $this->reindexDetailCollections($rsDetails, 'refkey');
        $rsDetailsCol['vehicle_detail'] = $rsDetails;

        return $rsDetailsCol;
    }
}
