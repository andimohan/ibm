<?php
class DisposalWorkOrderDispatcher extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'disposal_work_order_dispatcher_header';
        $this->tableNameDetail = 'disposal_work_order_dispatcher_detail';
        $this->tableSupplierDetail = 'disposal_work_order_dispatcher_supplier_detail';
        $this->tableJobOrder = 'disposal_job_order_header';
        $this->tableContract = 'disposal_contract';
        $this->tableItem = 'item';
        $this->tableAsset = 'asset';
        $this->tableItemUnit = 'item_unit';
        $this->tableWarehouse = 'warehouse';
        $this->tableCustomer = 'customer';
        $this->tableLocation = 'location';
        $this->tableService = 'item';
        $this->tableCar = 'car';
        $this->tableCustomerCategory = 'customer_category';
        $this->tableCarCategory = 'car_category';
        $this->tableChassis = 'chassis';
        $this->tableEmployee = 'employee';
        $this->tableStatus = 'transaction_status';
        $this->tableDetailStatus = 'disposal_work_detail_status';
        $this->tableSupplier = 'supplier';
        $this->tableCityCategory = 'city_category';
        $this->tableCity = 'city';
        $this->isTransaction = true;
        $this->newLoad = true;

        $this->allowedStatusForEdit = array(1, 2);

        $this->securityObject = 'DisposalWorkOrderDispatcher';

        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['quota'] = array('quota');
        $this->arrDataDetail['joborderkey'] = array('hidJobOrderKey');
        $this->arrDataDetail['customerkey'] = array('hidCustomerKey');
        $this->arrDataDetail['servicekey'] = array('hidServiceKey');
        $this->arrDataDetail['workordercode'] = array('workOrderCode');
        // $this->arrDataDetail['quota'] = array('quota', array('datatype' => 'number'));
        $this->arrDataDetail['customerweight'] = array('customerWeight', 'number');
        $this->arrDataDetail['supplierweight'] = array('supplierWeight', 'number');

        $this->arrSupplierDetail = array();
        $this->arrSupplierDetail['pkey'] = array('hidSupplierDetailKey');
        $this->arrSupplierDetail['refkey'] = array('pkey', 'ref');
        $this->arrSupplierDetail['supplierkey'] = array('hidSupplierKey');
        $this->arrSupplierDetail['refsuppliercode'] = array('refSupplierCode');
        $this->arrSupplierDetail['disposalsupplierweight'] = array('disposalSupplierWeight', 'number');

        // REFCASHOUTKEY jgn disave dr form, karena bisa bentrok kalo form lg dibuka, terus kas keluar dicancel, lalu form disave.
        //$this->arrDataDetail['refcashoutkey'] = array('hidRefCashOutKey');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
        array_push($arrDetails, array('dataset' => $this->arrSupplierDetail, 'tableName' => $this->tableSupplierDetail));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['stuffingdatetime'] = array('trDateStuffing', 'date');
        $this->arrData['refkey'] = array('hidSOKey');
        $this->arrData['refdetailkey'] = array('hidSODetailKey');
        $this->arrData['itemkey'] = array('hidItemKey');
        $this->arrData['driverkey'] = array('hidDriverKey');
        $this->arrData['carkey'] = array('hidCarKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['totalsupplierweight'] = array('totalSupplierWeight', 'number');
        $this->arrData['totalcustomerweight'] = array('totalCustomerWeight', 'number');
        $this->arrData['totalprorate'] = array('totalProRate', 'number');



        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 80, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouseName', 'title' => 'warehouse', 'default' => true, 'dbfield' => 'warehousename', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'car', 'title' => 'car', 'dbfield' => 'policenumber', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'driver', 'title' => 'driver', 'dbfield' => 'drivername', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description', 'title' => 'note', 'dbfield' => 'trdesc', 'default' => true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Total', $this->tableName . '.grandtotal'));
        array_push($this->arrSearchColumn, array('No. Polisi', $this->tableCar . '.policenumber'));
        array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));
        array_push($this->arrSearchColumn, array('Supir', $this->tableEmployee . '.name'));
        array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.trdesc'));

        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printWorkOrder', 'name' => $this->lang['printWorkOrder'], 'icon' => 'print', 'url' => 'print/disposalWorkOrderDispatcher'));
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));

        $this->includeClassDependencies(array(
            'DisposalJobOrder.class.php',
            'Asset.class.php',
            'CarTurnover.class.php',
            'WorkProgress.class.php',
            'WorkProgressStep.class.php',
            'Warehouse.class.php',
            'DisposalWorkOrder.class.php',
            'Service.class.php',
            'APEmployeeCommission.class.php',
            'Location.class.php',
            'DisposalPurchaseOrder.class.php',
            'Car.class.php'
        ));

        $this->overwriteConfig();
    }

    function getQuery()
    {

        $sql = '
			SELECT ' . $this->tableName . '.* ,  
			   ' . $this->tableStatus . '.status as statusname ,
			   ' . $this->tableEmployee . '.name as drivername ,
			   ' . $this->tableCar . '.code as policecode ,
			   ' . $this->tableCar . '.policenumber ,
               ' . $this->tableWarehouse . '.name as warehousename 
			FROM 
                ' . $this->tableStatus . ', 
                ' . $this->tableWarehouse . ',
                ' . $this->tableName . '
                    left join ' . $this->tableEmployee . ' on ' . $this->tableName . '.driverkey = ' . $this->tableEmployee . '.pkey
                    left join ' . $this->tableCar . ' on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey   
			WHERE ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                  ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey 
 		' . $this->criteria;


        $sql .= $this->getWarehouseCriteria();

        return $sql;
    }


    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   			' . $this->tableNameDetail . '.*, 
                ' . $this->tableJobOrder . '.code as label, 
                ' . $this->tableJobOrder . '.code as jobordercode, 
                ' . $this->tableJobOrder . '.maximumweight as quota, 
                ' . $this->tableJobOrder . '.servicekey,
                ' . $this->tableCustomer . '.citykey,
                ' . $this->tableCustomer . '.name as customername,
                ' . $this->tableDetailStatus . '.status as statusname,
                ' . $this->tableDetailStatus . '.class as statuscolor,
                ' . $this->tableCity . '.categorykey as citycategorykey,
                   concat (' . $this->tableCity . '.name, ", ", ' . $this->tableCityCategory . '.name) as cityandcategoryname,
                   concat (' . $this->tableContract . '.code, " - ", ' . $this->tableContract . '.name) as contractname,
                ' . $this->tableService . '.name as servicename
			  from
			  	' . $this->tableNameDetail . '
                  left join ' . $this->tableJobOrder . ' on ' . $this->tableNameDetail . '.joborderkey = ' . $this->tableJobOrder . '.pkey
                  left join ' . $this->tableCustomer . ' on ' . $this->tableJobOrder . '.customerkey = ' . $this->tableCustomer . '.pkey
                  left join ' . $this->tableContract . ' on ' . $this->tableJobOrder . '.contractkey = ' . $this->tableContract . '.pkey
                  left join ' . $this->tableCity . ' on ' . $this->tableCustomer . '.citykey = ' . $this->tableCity . '.pkey 
                  left join ' . $this->tableCityCategory . ' on ' . $this->tableCity . '.categorykey = ' . $this->tableCityCategory . '.pkey 
                  left join ' . $this->tableService . ' on ' . $this->tableJobOrder . '.servicekey = ' . $this->tableService . '.pkey,
			  	' . $this->tableDetailStatus . '
			  where
                ' . $this->tableNameDetail . '.statuskey = ' . $this->tableDetailStatus . '.pkey and
                ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';
        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }

    function getSupplierDetail($pkey, $criteria = '', $orderby = '')
    {

        $sql = 'select
                ' . $this->tableSupplierDetail . '.*, 
                ' . $this->tableSupplier . '.name as suppliername
              from
                ' . $this->tableSupplierDetail . ',
                ' . $this->tableSupplier . '
              where
                ' . $this->tableSupplierDetail . '.supplierkey = ' . $this->tableSupplier . '.pkey and
		        ' . $this->tableSupplierDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' . $orderby;

        return $this->oDbCon->doQuery($sql);
    }

    function validateForm($arr, $pkey = '')
    {
        $arrayToJs = parent::validateForm($arr, $pkey);
        $arrJOKey = $arr['hidJobOrderKey'];
        $arrCustomerKey = $arr['hidCustomerKey'];
        $carKey = $arr['hidCarKey'];
        $driver = $arr['hidDriverKey'];


        $disposalJobOrder = new DisposalJobOrder();
        $customer = new Customer();

        if (empty($carKey))
            $this->addErrorList($arrayToJs, false, $this->errorMsg['car'][1]);

        if (empty($driver))
            $this->addErrorList($arrayToJs, false, $this->errorMsg['driver'][1]);

        $arrDetailKeys = array();
        $arrDetailCustomerKey = array();
        for ($i = 0; $i < count($arrJOKey); $i++) {
            if (empty($arrJOKey[$i])) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg[501]);
            } else {
                // cek detail double JO 
                if (in_array($arrJOKey[$i], $arrDetailKeys)) {
                    $rsJO = $disposalJobOrder->getDataRowById($arrJOKey[$i]);
                    $this->addErrorList($arrayToJs, false, $rsJO[0]['code'] . '. ' . $this->errorMsg[215]);
                } else {
                    array_push($arrDetailKeys, $arrJOKey[$i]);
                }

                // cek detail double Customer 
                // tidak perlu, ternyata ada kasus customer nya sama tapi kontrak/JO nya beda dalam satu kali pengambilan limbah
                // if (in_array($arrCustomerKey[$i], $arrDetailCustomerKey)) {
                //     $rsCustomer = $customer->getDataRowById($arrCustomerKey[$i]);
                //     $this->addErrorList($arrayToJs, false, $rsCustomer[0]['name'] . '. ' . $this->errorMsg[215]);
                // } else {
                //     array_push($arrDetailCustomerKey, $arrCustomerKey[$i]);
                // }
            }
        }
        return $arrayToJs;

    }

    function normalizeParameter($arrParam, $trim = false)
    {

        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }

    function validateConfirm($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];
        $driverKey = $rsHeader[0]['driverkey'];
        $carKey = $rsHeader[0]['carkey'];

        $employee = new Employee();
        $warehouse = new Warehouse();
        $disposalJobOrder = new DisposalJobOrder();
        $rsDetail = $this->getDetailById($id);

        if (empty($driverKey))
            $this->addErrorLog(false, $this->errorMsg['driver'][1]);

        if (empty($carKey))
            $this->addErrorLog(false, $this->errorMsg['car'][1]);

        // JO harus distatus aktif
        // $this->validateLinkedData($disposalJobOrder, array(   'linkedField' => array('field' => $disposalJobOrder->tableName.'.pkey' ,'value' => array_column($rsDetail,'joborderkey')),
        //                                                         'statuskey' => 4,
        //                                                         'negationStatus' => true ,
        //                                                         'errorCode' => 201,
        //                                                         'errorDetailMsg' => $this->errorMsg[206], 
        //                                                         'refCode' => $rsHeader[0]['code'], // KODE SPK LIST
        //                                                       )
        //                           );

        // for ($i = 0; $i < count($rsDetail); $i++){ 
        //     $permission = $disposalJobOrder->getPermissionJO($rsDetail[$i]['customerkey']);
        //     if (!$permission) {
        //         $customer = new Customer();
        //         $rsCustomer = $customer->getDataRowById($rsDetail[$i]['customerkey']);
        //         $this->addErrorLog(false, '<strong>' . $rsCustomer[0]['name'] . '</strong>. ' .$this->errorMsg['disposalJobOrder'][5]); 
        //     }
        // }

    }


    function validateBackConfirm($rsHeader)
    {
        $disposalWorkOrder = new DisposalWorkOrder();
        $apEmployeeCommission = new APEmployeeCommission();
        $pkey = $rsHeader[0]['pkey'];

        $rsDetail = $this->getDetailWithRelatedInformation($pkey);
        $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'));

        $rsAPEmployeeCommission = $apEmployeeCommission->searchData('', '', true, ' and  ' . $apEmployeeCommission->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $apEmployeeCommission->tableName . '.reftabletype = ' . $tablekey['key'] . '  and (' . $apEmployeeCommission->tableName . '.statuskey in(2,3))');
        if (!empty($rsAPEmployeeCommission)) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . $this->errorMsg['apCommission'][2]);
        }
        // for ($i = 0; $i < count($rsDetail); $i++){
        //     $rsWO = $disposalWorkOrder->searchData('', '', true, ' and ' . $disposalWorkOrder->tableName . '.refdetailkey = ' . $this->oDbCon->paramString($rsDetail[$i]['pkey']) . ' and ' . $disposalWorkOrder->tableName . '.statuskey in (2,3)');
        //     $errMsg = $disposalWorkOrder->checkStatusValidation($rsWO);

        //     foreach($errMsg as $row) 
        //         $this->addErrorLog(false, $row);
        // }
    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {
        $disposalWorkOrder = new DisposalWorkOrder();
        $apEmployeeCommission = new APEmployeeCommission();
        $disposalPurchaseOrder = new DisposalPurchaseOrder();
        $pkey = $rsHeader[0]['pkey'];
        $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'));

        $rsDetail = $this->getDetailWithRelatedInformation($pkey);

        $rsAPEmployeeCommission = $apEmployeeCommission->searchData('', '', true, ' and  ' . $apEmployeeCommission->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $apEmployeeCommission->tableName . '.reftabletype = ' . $tablekey['key'] . '  and (' . $apEmployeeCommission->tableName . '.statuskey in(2,3))');
        if (!empty($rsAPEmployeeCommission)) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . $this->errorMsg['apCommission'][225]);
        }

        $rsDisposalPurchaseOrder = $disposalPurchaseOrder->searchData('', '', true, ' and  ' . $disposalPurchaseOrder->tableName . '.dispatchkey = ' . $this->oDbCon->paramString($pkey) . '  and (' . $disposalPurchaseOrder->tableName . '.statuskey in(2,3))');
        if (!empty($rsDisposalPurchaseOrder)) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . '<strong>' . $rsDisposalPurchaseOrder[0]['code'] . '</strong> ' . $this->errorMsg[225]);
        }

        // for ($i = 0; $i < count($rsDetail); $i++){
        //     $rsWO = $disposalWorkOrder->searchData('', '', true, ' and ' . $disposalWorkOrder->tableName . '.refdetailkey = ' . $this->oDbCon->paramString($rsDetail[$i]['pkey']) . ' and ' . $disposalWorkOrder->tableName . '.statuskey in (2,3)');
        //     // $errMsg = $disposalWorkOrder->checkStatusValidation($rsWO);

        //     foreach($errMsg as $row) 
        //         $this->addErrorLog(false, $row);
        // }






        // $rsWorkOrder = $disposalWorkOrder->searchDataRow( array( $disposalWorkOrder->tableName.'.pkey') , 
        //                                                     '   and '.$disposalWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).'
        //                                                         and '.$disposalWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].','.TRANSACTION_STATUS['selesai'].')'  
        //         

        $arrDetailKey = array_column($rsDetail, 'pkey');

        $rsWO = $disposalWorkOrder->searchData('', '', true, ' and ' . $disposalWorkOrder->tableName . '.refdetailkey in (' . $this->oDbCon->paramString($arrDetailKey, ',') . ' ) 
                                                                   and ' . $disposalWorkOrder->tableName . '.statuskey in (2,3) and ' . $disposalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . '');

        for ($i = 0; $i < count($rsWO); $i++) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsWO[$i]['code'] . '</strong>, ' . $this->errorMsg[225]);
        }

    }


    function confirmTrans($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];

        $rsDetail = $this->getDetailById($id);

        $this->autoAddWorkOrder($rsHeader, $rsDetail);
    }

    function validateClose($rsHeader)
    {

        $supplier = new Supplier();
        $pkey = $rsHeader[0]['pkey'];

        $rsSupplierDetail = $this->getSupplierDetail($pkey);
        $rsDetail = $this->getDetailWithRelatedInformation($pkey);

        if ($rsHeader[0]['statuskey'] <> 2) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg[204]);
        } else {
            // kalo ad SPK yg blm selesai, return error
            $disposalWorkOrder = new DisposalWorkOrder();
            $rsWorkOrder = $disposalWorkOrder->searchDataRow(
                array($disposalWorkOrder->tableName . '.pkey'),
                '   and ' . $disposalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . '
                                                                and ' . $disposalWorkOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['menunggu'] . ',' . TRANSACTION_STATUS['konfirmasi'] . ')'
            );

            if (!empty($rsWorkOrder))
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['truckingServiceWorkOrder'][2]);

        }

        //        $arrSupplierDetailKeys = array();
//        if (empty($rsSupplierDetail))  {
//            $this->addErrorLog(false, $this->errorMsg['supplier'][1]);
//        }
//
//        for ($i = 0; $i < count($rsSupplierDetail); $i++) {
//            $arrRefSupplierCode = $rsSupplierDetail[$i]['refsuppliercode'];
//            $arrDisposalsupplierweight = $rsSupplierDetail[$i]['disposalsupplierweight'];
//
//            if (empty($rsSupplierDetail[$i]['supplierkey'])) {
//                $this->addErrorLog(false, $this->errorMsg['supplier'][1]);
//            } else {
//                // cek detail double Supplier
//                $rsSupplier = $supplier->getDataRowById($rsSupplierDetail[$i]['supplierkey']);
//                if (in_array($rsSupplierDetail[$i]['supplierkey'], $arrSupplierDetailKeys)) {
//                    $this->addErrorLog(false, $rsSupplier[0]['name'] . '. ' . $this->errorMsg[215]);
//                } else {
//                    array_push($arrSupplierDetailKeys, $rsSupplierDetail[$i]['supplierkey']);
//                }
//
//                // cek detail supplier refcode tidak boleh kosong
//                if (empty($arrRefSupplierCode[$i])) {
//                    $this->addErrorLog(false, $rsSupplier[0]['name'] . '. ' . $this->errorMsg['reference'][1]);
//                }
//
//                // cek detail supplier muatan tidak boleh 0
//                if ($arrDisposalsupplierweight[$i] <= 0) {
//                    $this->addErrorLog(false, $rsSupplier[0]['name'] . '. ' . $this->errorMsg['gramasi'][3]);
//                }
//            }
//        }

        //        for ($i = 0; $i < count($rsDetail); $i++) {
//            if ($rsDetail[$i]['customerweight'] <= 0) {
//                $this->addErrorLog(false, $rsDetail[0]['customername'] . '. ' . $this->errorMsg['gramasi'][3]);
//            } 
//        }

    }

    function closeTrans($rsHeader)
    {
        $disposalWorkOrder = new DisposalWorkOrder();
        $pkey = $rsHeader[0]['pkey'];
        $this->addCommissionAP($rsHeader);
        // $this->updateWorkOrder($rsHeader);

        // $rsWorkOrder = $disposalWorkOrder->searchDataRow( array( $disposalWorkOrder->tableName.'.pkey') , 
        //                                                     '   and '.$disposalWorkOrder->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).'
        //                                                         and '.$disposalWorkOrder->tableName.'.statuskey in ('.TRANSACTION_STATUS['konfirmasi'].')'  
        //                                                 ); 

        // for ($i = 0; $i < count($rsWorkOrder); $i++){
        //     $disposalWorkOrder->changeStatus($rsWorkOrder[$i]['pkey'], 3, '', false, true);
        // }
    }

    function addCommissionAP($rsHeader)
    {
        $apEmployeeCommission = new APEmployeeCommission();
        $warehouse = new Warehouse();
        $termOfPayment = new TermOfPayment();
        $disposalWorkOrder = new DisposalWorkOrder();
        $service = new Service();
        $pkey = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($pkey);

        $warehousekey = $rsHeader[0]['warehousekey'];
        $rsWO = $disposalWorkOrder->searchDataRow(
            array($disposalWorkOrder->tableName . '.pkey', $disposalWorkOrder->tableName . '.refdetailkey', $disposalWorkOrder->tableName . '.totaldisposalweight'),
            '  and ' . $disposalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . '
           and ' . $disposalWorkOrder->tableName . '.statuskey = 3'
        );
        $rsWO = array_column($rsWO, null, 'refdetailkey');
        $totalAP = 0;
        for ($i = 0; $i < count($rsDetail); $i++) {
            $detailKey = $rsDetail[$i]['pkey'];
            $rsDetail[$i]['drivercommission'] = 0;
            if ($rsWO[$detailKey]['totaldisposalweight'] > 0) {
                $rsService = $service->getDataRowById($rsDetail[$i]['servicekey']);
                $totalAP += $rsService[0]['drivercommission'];
                $rsDetail[$i]['drivercommission'] = $rsService[0]['drivercommission'];
            }
        }
        // $top = 0;
        // $warehousekey =  $rsHeader[0]['warehousekey']; //$warehouse->getDefaultData();

        // $rsJO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refkey']);

        // $note = array();
        // array_push($note,$rsHeader[0]['code']);

        $commissionDateType = $this->loadSetting('driverCommissionBasedOn');
        $commissionDate = ($commissionDateType == 2) ? $this->formatDBDate($rsHeader[0]['trdate']) : date('d / m / Y');
        $commissionDateInDBFormat = str_replace('\'', '', $this->oDbCon->paramDate($commissionDate, ' / '));

        // $rsTOP = $termOfPayment->getDataRowById($topkey);    
        // $top = (empty($rsTOP)) ? 0 : $rsTOP[0]['duedays'];

        $date = new DateTime($commissionDateInDBFormat);
        // $date->add(new DateInterval('P'.$top.'D'));
        $commissionDueDate = $date->format('d / m / Y');

        if ($totalAP > 0) {
            $driverCommissionAmount = $totalAP;

            $rsARKey = $apEmployeeCommission->getTableKeyAndObj($this->tableName, array('key'));

            $arrParam = array();
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefKey2'] = '';
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefCode'] = $rsHeader[0]['code'];
            $arrParam['hidRefCode2'] = '';
            $arrParam['hidRefDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['hidRefTable'] = $rsARKey['key'];
            $arrParam['trDesc'] = implode(chr(13), $note);
            $arrParam['trDate'] = $commissionDate;

            $arrParam['createdBy'] = 0;
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            $arrParam['selAPType'] = AP_TYPE['driverCommission'];
            $arrParam['selWarehouse'] = $warehousekey;
            $arrParam['dueDate'] = $commissionDueDate;
            $arrParam['hidEmployeeKey'] = $rsHeader[0]['driverkey'];
            $arrParam['hidARStatusKey'] = 3;

            $arrParam['amount'] = $driverCommissionAmount;

            $arrayToJs = $apEmployeeCommission->addData($arrParam);

            // $newData = $arrayToJs[0]['data'];
            // $sql = 'update '.$this->tableName.' set refcashoutdriverkey = ' . $this->oDbCon->paramString($newData['pkey']) .' where pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
            // $this->oDbCon->execute($sql);

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);

            $this->updateGLCommission($rsHeader, $rsDetail);
        }

        // if ($totalAP > 0){ 
        //     // overwrite tgl
        //      $rsHeader[0]['trdate'] = $commissionDateInDBFormat;
        //     $this->updateGLCommission($rsHeader);
        // }
    }

    function updateGLCommission($rs, $rsDetail)
    {
        if (!USE_GL)
            return;

        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $employee = new Employee();
        $service = new Service();
        $pkey = $rs[0]['pkey'];
        // $rsDetail = $this->getDetailById($pkey);
        $rsEmployee = $employee->getDataRowById($rs[0]['driverkey']);

        $totalAP = 0;
        for ($i = 0; $i < count($rsDetail); $i++) {
            // $rsService = $service->getDataRowById($rsDetail[$i]['servicekey']);
            $totalAP += $rsDetail[$i]['drivercommission'];
        }


        $warehousekey = $rs[0]['warehousekey'];

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName, array('key'));
        $rsCOA = $coaLink->getCOALink('driverincentive', $warehouse->tableName, $warehousekey);

        if ($totalAP > 0) {
            $driverCommissionAmount = $totalAP;
            $arr = array();
            $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
            $arr['code'] = 'xxxxx';
            $arr['refkey'] = $rs[0]['pkey'];
            $arr['refTableType'] = $rsKey['key'];
            $arr['trDate'] = $this->formatDBDate($rs[0]['trdate'], 'd / m / Y');
            $arr['refCode'] = $rs[0]['code'];
            $arr['selWarehouseKey'] = $rs[0]['warehousekey'];

            $temp = -1;

            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = $driverCommissionAmount;
            $arr['credit'][$temp] = 0;

            $coakey = $employee->getAPCommissionCOAKey($rs[0]['driverkey'], $warehousekey);

            //akun hutang 
            $temp++;
            $arr['hidCOAKey'][$temp] = $coakey;
            $arr['debit'][$temp] = 0;
            $arr['credit'][$temp] = $driverCommissionAmount;
            $arr['trDesc'] = $this->lang['driverIncentive'] . ' ' . $rsEmployee[0]['name'] . '. ' . $rs[0]['code'];

            $arrayToJs = $generalJournal->addData($arr);

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);

        }

    }

    // function updateWorkOrder($rsHeader){

    //     $disposalWorkOrder = new DisposalWorkOrder();
    //     $pkey = $rsHeader[0]['pkey'];
    //     $rsDetail = $this->getDetailWithRelatedInformation($pkey);

    //     try {

    //         if (!$this->oDbCon->startTrans())
    //             throw new Exception($this->errorMsg[100]);

    //             for ($j = 0; $j < count($rsDetail); $j++) {
    //                 $detailPkey = $rsDetail[$j]['pkey'];
    //                 $sql = 'update 
    //                             ' . $disposalWorkOrder->tableName . '
    //                         set 
    //                             disposalweight = ' . $this->oDbCon->paramString($rsDetail[$j]['customerweight']) . ', 
    //                             prorateweight = ' . $this->oDbCon->paramString($rsDetail[$j]['supplierweight']) . ' 
    //                         where  
    //                             ' . $disposalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' and 
    //                             ' . $disposalWorkOrder->tableName . '.refdetailkey = ' . $this->oDbCon->paramString($detailPkey) . '
    //                         ';

    //                 $this->oDbCon->execute($sql);
    //             }
    //         $this->oDbCon->endTrans();
    //     } catch (Exception $e) {
    //         $this->oDbCon->rollback();
    //     }
    // }

    function autoAddWorkOrder($rsHeader = '', $rsDetail = array())
    {

        $spkDateBasedOn = $this->loadSetting('spkDateBasedOn');

        $disposalWorkOrder = new DisposalWorkOrder();
        $disposalJobOrder = new DisposalJobOrder();
        $item = new Item();

        $id = $rsHeader[0]['pkey'];

        $user = base64_decode($_SESSION[$this->loginAdminSession]['id']);

        for ($i = 0; $i < count($rsDetail); $i++) {

            $itemkey = $rsDetail[$i]['itemkey'];
            $rsJO = $disposalJobOrder->getWasteDetail($rsDetail[$i]['joborderkey']);
            $arrParam = array();


            //cek service nya ispacakge atau bukan
//            $arrParam['hidDetailItemKey'] = array();
//            if ($rsDetail[$i]['qty'] > 0 || $arrServices[$itemkey]['ispackage'] == 1){
//            
//                $rsServiceDetail = $service->getItemDetail($rsDetail[$i]['itemkey'],' and '.$service->tableName.'.itemtype = 1');
//
//                for($j=0;$j<count($rsServiceDetail);$j++){
//                    
//                            $arrParam['hidDetailItemKey'][$j] = 0;
//                            $arrParam['hidItemDetailKey'][$j] = $rsServiceDetail[$j]['itemkey'];
//                            $arrParam['selUnit'][$j] = $rsServiceDetail[$j]['unitkey'];
//                            $arrParam['qty'][$j] = $rsServiceDetail[$j]['qty'];
//
//                }
//            }


            $spkDate = ($spkDateBasedOn == 2) ? $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y H:i') : date('d / m / Y');

            $arrParam['code'] = 'xxxxxx';
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
            $arrParam['hidWorkListKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidWorkListDetailKey'] = $rsDetail[$i]['pkey'];
            $arrParam['hidJobOrderKey'] = $rsDetail[$i]['joborderkey'];
            $arrParam['hidServiceKey'] = $rsDetail[$i]['servicekey'];
            $arrParam['hidCustomerKey'] = $rsDetail[$i]['customerkey'];
            $arrParam['workOrderCode'] = $rsDetail[$i]['workordercode'];
            $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['trDesc'] = $rsDetail[$i]['trdesc'];
            $arrParam['islinked'] = true;
            $arrParam['createdBy'] = $user;
            $arrParam['hidSaveAndProceed'] = 1;
            $arrParam['hidDetailKey'] = array();
            $arrParam['hidWasteKey'] = array();
            foreach ($rsJO as $JO) {
                array_push($arrParam['hidDetailKey'], 0);
                array_push($arrParam['hidWasteKey'], $JO['wastekey']);
            }
            //            $arrParam['_mnv'] = true;

            $arrayToJs = $disposalWorkOrder->addData($arrParam);

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);

        }

    }

    function generateDefaultQueryForAutoComplete($returnField)
    {

        $sql = 'select
                ' . $returnField['key'] . ',
                ' . $returnField['value'] . ' as value, 
			   ' . $this->tableEmployee . '.name as drivername ,
			   ' . $this->tableCar . '.code as policecode ,
			   ' . $this->tableCar . '.policenumber ,
                ' . $this->tableName . '.trdate
            from 
                ' . $this->tableName . '
                left join ' . $this->tableEmployee . ' on ' . $this->tableName . '.driverkey = ' . $this->tableEmployee . '.pkey
                    left join ' . $this->tableCar . ' on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey,
                ' . $this->tableStatus . ' 
            where  		 
                ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
        ';

        $sql .= $this->getCompanyCriteria();

        return $sql;
    }

    function afterUpdateData($arrParam, $action)
    {
        $rsHeader = $this->getDataRowById($arrParam['pkey']);
        if ($rsHeader[0]['statuskey'] == 2) {

            $disposalWorkOrder = new DisposalWorkOrder();

            $this->updateDisposalWorkOrder($rsHeader);

            try {

                if (!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);

                $sql = 'update 
                                    ' . $disposalWorkOrder->tableName . ',
                                    ' . $this->tableNameDetail . '
                                set 
                                    ' . $disposalWorkOrder->tableName . '.workordercode = ' . $this->tableNameDetail . '.workordercode' . ',
                                    ' . $disposalWorkOrder->tableName . '.jokey = ' . $this->tableNameDetail . '.joborderkey' . ',
                                    ' . $disposalWorkOrder->tableName . '.customerkey = ' . $this->tableNameDetail . '.customerkey' . '
                                where  
                                    ' . $disposalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' and 
                                    ' . $disposalWorkOrder->tableName . '.refdetailkey = ' . $this->tableNameDetail . '.pkey';

                $this->oDbCon->execute($sql);
                $this->oDbCon->endTrans();
            } catch (Exception $e) {
                $this->oDbCon->rollback();
            }
        }
    }

    function updateDisposalWorkOrder($rsHeader) {
        //add spk ketika di save dan status spk sudah konfirmasi
        //jika detail spk dispatcher belum terbentuk spk 

        $disposalWorkOrder = new DisposalWorkOrder();
        $id = $rsHeader[0]['pkey'];

        $rsDetail = $this->getDetailWithRelatedInformation($id);
        $arrDetailKey = array_column($rsDetail, 'pkey');
        $arrCustomerKey = array_column($rsDetail, 'customer');

        //cari data di spk yang status menunggu dan tidak ada di detail dispatcher
        $rsDisposalWorkOrder = $disposalWorkOrder->searchData('','',true, ' and ' .  $disposalWorkOrder->tableName.'.refkey = '. $this->oDbCon->paramString($id) .' 
                                and '. $disposalWorkOrder->tableName .'.statuskey  = '. $this->oDbCon->paramString(TRANSACTION_STATUS['menunggu']) .' 
                                and '. $disposalWorkOrder->tableName .'.refdetailkey not in ('. $this->oDbCon->paramString($arrDetailKey,',') .') ');
        
        if(!empty($rsDisposalWorkOrder)) {
            //kalau ada hapus
            foreach($rsDisposalWorkOrder as $disposalWO) {  
                $disposalWorkOrder->changeStatus($disposalWO['pkey'], TRANSACTION_STATUS['batal'], '', false, true);
            }
        }

        //get detail disposal work order status 1,2,3
        $rsDisposalWO = $disposalWorkOrder->getDisposalWorkOrderByDispatcher($id);
        $arrRefdetailKey = array_column($rsDisposalWO, 'refdetailkey');
        //ambil detail yang pkey yang tidak sama dengan refdetailkey atau tidak terbentuk spk
        $detailCriteria = ' and ' . $this->tableNameDetail .'.pkey not in ('. $this->oDbCon->paramString($arrRefdetailKey,',') .') ';
        $rsDetails = $this->getDetailWithRelatedInformation($id, $detailCriteria);

        if (!empty($rsDetails)) {
            //add spk jika detail belum terbentuk spk
            $this->autoAddWorkOrder($rsHeader, $rsDetails);
        }
        
    }

    function cancelTrans($rsHeader, $copy)
    {

        $status = 4;
        $this->cancelWorkOrder($rsHeader, $status);

        if ($copy)
            $this->copyDataOnCancel($rsHeader[0]['pkey']);

    }

    function cancelWorkOrder($rsHeader, $status)
    {

        $disposalWorkOrder = new DisposalWorkOrder();
        $rsWorkOrder = $disposalWorkOrder->searchData('', '', true, ' and ' . $disposalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' and ' . $disposalWorkOrder->tableName . '.statuskey in (1)');
        for ($i = 0; $i < count($rsWorkOrder); $i++) {
            $disposalWorkOrder->changeStatus($rsWorkOrder[$i]['pkey'], $status, '', false, true);
        }
    }

    function backConfirmTrans($rsHeader)
    {

        // $status = 2 ;
        // $this->cancelWorkOrder($rsHeader, $status);
        $this->cancelCommissionAP($rsHeader);
        $this->cancelGLByRefkey($rsHeader[0]['pkey'], $this->tableName);
    }

    function cancelCommissionAP($rsHeader)
    {

        $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'));

        $apEmployeeCommission = new APEmployeeCommission();
        $rsApEmployeeCommission = $apEmployeeCommission->searchData('', '', true, ' and  ' . $apEmployeeCommission->tableName . '.refkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']) . ' and ' . $apEmployeeCommission->tableName . '.reftabletype = ' . $tablekey['key'] . ' and ' . $apEmployeeCommission->tableName . '.statuskey = 1');
        for ($i = 0; $i < count($rsApEmployeeCommission); $i++) {
            $apEmployeeCommission->changeStatus($rsApEmployeeCommission[$i]['pkey'], 4, '', false, true);
        }
    }


    function updateDetailStatus($pkey)
    {

        $disposalWorkOrder = new DisposalWorkOrder();

        try {

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            // search semua status work order, kalo sudah closed semua, update status 
            // kalo gk ad SPK (updateDetailStatus kepanggil dr cancel SPK), gk auto closing
            // perlukah ??

            // jika 1 dispatcher ad 3 SPK, maka user boleh mengcancel salah satu SPK (misalnya gk jd jalan ke lokasi tertentu ??), 
            // dan job order tetep selesai.

            $rs = $disposalWorkOrder->searchDataRow(
                array($disposalWorkOrder->tableName . '.pkey', $disposalWorkOrder->tableName . '.refkey', $disposalWorkOrder->tableName . '.statuskey'),
                '  and ' . $disposalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . '
                                                       and ' . $disposalWorkOrder->tableName . '.statuskey  <> 4'
            );


            if (!empty($rs)) {

                $rsSPK = $disposalWorkOrder->searchDataRow(
                    array($disposalWorkOrder->tableName . '.pkey'),
                    '  and ' . $disposalWorkOrder->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . '
                                and ' . $disposalWorkOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['menunggu'] . ',' . TRANSACTION_STATUS['konfirmasi'] . ')'
                );

                $status = (empty($rsSPK)) ? 3 : 2;

                $rsHeader = $this->getDataRowById($pkey);
                if ($status <> $rsHeader[0]['statuskey'])
                    $this->changeStatus($pkey, $status, '', false, true);

            }

            $this->oDbCon->endTrans();
        } catch (Exception $e) {
            $this->oDbCon->rollback();
        }
    }

}
?>