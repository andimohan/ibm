<?php
class DisposalWorkOrder extends BaseClass
{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'disposal_work_order_header';
        $this->tableNameDetail = 'disposal_work_order_detail';
        $this->tableWOListHeader = 'disposal_work_order_dispatcher_header';
        $this->tableService = 'item';
        $this->tableJobOrderHeader = 'disposal_job_order_header';
        $this->tableJobOrderDetail = 'disposal_job_order_detail';
        $this->tableWorkOrderItemDetail = 'disposal_work_order_item';
        $this->tableSupplier = 'supplier';
        $this->tableWorkOrderAssetDetail = 'disposal_work_order_asset';
        $this->tableWorkOrderCarDetail = 'disposal_work_order_car';
        $this->tableWaste = 'waste';
        $this->tableFile = 'disposal_work_order_file';
        $this->uploadFileFolder = 'disposal-work-order-file/';

        $this->allowedStatusForEdit = array(1, 2);
        $this->tableItem = 'item';
        $this->tableAsset = 'asset';
        $this->tableItemUnit = 'item_unit';
        $this->tableWarehouse = 'warehouse';
        $this->tableCustomer = 'customer';
        $this->tableCar = 'car';
        $this->tableCity = 'city';

        $this->tableEmployee = 'employee';
        $this->tableStatus = 'transaction_status';
        $this->isTransaction = true;
        
        $this->useStorage = $this->useStorage('S3');	

        $this->overwriteContractSecurityObject = 'overwriteContract';

        $this->securityObject = 'DisposalWorkOrder';

        $this->arrAssetDetail = array();
        $this->arrAssetDetail['pkey'] = array('hidAssetDetailKey');
        $this->arrAssetDetail['refkey'] = array('pkey', 'ref');
        $this->arrAssetDetail['assetkey'] = array('hidAssetKey');
        $this->arrAssetDetail['explicensedate'] = array('assetExpired', 'date');
        $this->arrAssetDetail['qty'] = array('qtyAsset', 'number');

        $this->arrItemDetail = array();
        $this->arrItemDetail['pkey'] = array('hidDetailItemKey');
        $this->arrItemDetail['refkey'] = array('pkey', 'ref');
        $this->arrItemDetail['itemkey'] = array('hidItemDetailKey');
        $this->arrItemDetail['qty'] = array('qty', 'number');
        $this->arrItemDetail['qtyinbaseunit'] = array('qtyInBaseUnit', 'number');
        $this->arrItemDetail['unitkey'] = array('selUnit');
        $this->arrItemDetail['priceinunit'] = array('priceInUnit', 'number');
        $this->arrItemDetail['priceinbaseunit'] = array('priceInBaseUnit', 'number');
        $this->arrItemDetail['unitconvmultiplier'] = array('unitConvMultiplier', 'number');
        $this->arrItemDetail['total'] = array('detailSubtotal', 'number');
        $this->arrItemDetail['costinbaseunit'] = array('cogs', 'number');
        $this->arrItemDetail['receivedqtyinbaseunit'] = array('receivedQtyInBaseUnit', 'number');


        $this->arrDataDetail = array();
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey', 'ref');
        $this->arrDataDetail['supplierkey'] = array('hidSupplierKey');
        $this->arrDataDetail['supplierweight'] = array('supplierWeight', array('datatype' => 'number'));
        $this->arrDataDetail['customerweight'] = array('customerWeight', array('datatype' => 'number'));
        $this->arrDataDetail['wastekey'] = array('hidWasteKey');
        $this->arrDataDetail['manifestcode'] = array('manifestCode');
        $this->arrDataDetail['ticketnumber'] = array('ticketNumber');

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
        array_push($arrDetails, array('dataset' => $this->arrItemDetail, 'tableName' => $this->tableWorkOrderItemDetail));
        array_push($arrDetails, array('dataset' => $this->arrAssetDetail, 'tableName' => $this->tableWorkOrderAssetDetail));
        
        
        
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
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['refkey'] = array('hidWorkListKey');
        $this->arrData['refdetailkey'] = array('hidWorkListDetailKey');
        $this->arrData['jokey'] = array('hidJobOrderKey');
        $this->arrData['servicekey'] = array('hidServiceKey');
        $this->arrData['disposalweight'] = array('disposalWeight', 'number');
        $this->arrData['prorateweight'] = array('prorateWeight', 'number');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['locationkey'] = array('hidLocationKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['taxvalue'] = array('taxValue', 'number');
        $this->arrData['total'] = array('total', 'number');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['workordercode'] = array('workOrderCode');
        $this->arrData['totaldisposalweight'] = array('totalDisposalWeight');

        $this->refAutoCode = array('param' => 'hidSOKey', 'refField' => 'refkey');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 80, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'workList', 'title' => 'reference', 'dbfield' => 'wolistcode', 'default' => true, 'width' => 130));
        array_push($this->arrDataListAvailableColumn, array('code' => 'JOCode', 'title' => 'jobOrder', 'dbfield' => 'jocode', 'default' => true, 'width' => 130));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouseName', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'service', 'title' => 'service', 'dbfield' => 'servicename', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'workordercode', 'title' => 'workOrderCode', 'dbfield' => 'workordercode', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 180));
        array_push($this->arrDataListAvailableColumn, array('code' => 'carRegistrationNumber', 'title' => 'carRegistrationNumber', 'default' => true, 'dbfield' => 'policenumber', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Job Order', $this->tableJobOrderHeader . '.code'));
        array_push($this->arrSearchColumn, array('Pelanggan', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array('Item', $this->tableItem . '.name'));
        array_push($this->arrSearchColumn, array('Total', $this->tableName . '.grandtotal'));
        array_push($this->arrSearchColumn, array('Total', $this->tableCar . '.policenumber'));
        array_push($this->arrSearchColumn, array('status', $this->tableStatus . '.status'));
        array_push($this->arrSearchColumn, array('Catatan', $this->tableName . '.trdesc'));
        array_push($this->arrSearchColumn, array('workordercode', $this->tableName . '.workordercode'));
        array_push($this->arrSearchColumn, array('workList', $this->tableWOListHeader . '.code'));

        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printWorkOrder', 'name' => $this->lang['printWorkOrder'], 'icon' => 'print', 'url' => 'print/disposalWorkOrder'));

        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));

        $this->includeClassDependencies(array(
            'Asset.class.php',
            'Warehouse.class.php',
            'Service.class.php',
            'Item.class.php',
            'Car.class.php',
            'APEmployeeCommission.class.php',
            'DisposalJobOrder.class.php',
            'DisposalWorkOrderDispatcher.class.php',
            'Supplier.class.php'
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
			   ' . $this->tableCar . '.policenumber, 
			   ' . $this->tableJobOrderHeader . '.code as jocode, 
			   ' . $this->tableJobOrderHeader . '.customerkey,
			   ' . $this->tableJobOrderHeader . '.servicekey,
			   ' . $this->tableJobOrderHeader . '.maximumweight,
			   ' . $this->tableService . '.name as servicename,
			   ' . $this->tableWOListHeader . '.code as wolistcode,
			   ' . $this->tableWarehouse . '.name as warehousename,
               ' . $this->tableCity . '.name as cityname,
               ' . $this->tableCustomer . '.name as customername
			FROM 
                ' . $this->tableStatus . ',
                ' . $this->tableWarehouse . ',
                ' . $this->tableName . ' 
                    left join ' . $this->tableJobOrderHeader . ' on ' . $this->tableName . '.jokey = ' . $this->tableJobOrderHeader . '.pkey   
                    left join ' . $this->tableService . ' on ' . $this->tableJobOrderHeader . '.servicekey = ' . $this->tableService . '.pkey   
                    left join ' . $this->tableWOListHeader . ' on ' . $this->tableName . '.refkey = ' . $this->tableWOListHeader . '.pkey  
                    left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey  
                    left join ' . $this->tableCity . ' on ' . $this->tableCustomer . '.citykey = ' . $this->tableCity . '.pkey 
                    left join ' . $this->tableCar . ' on ' . $this->tableWOListHeader . '.carkey = ' . $this->tableCar . '.pkey  
                    left join ' . $this->tableEmployee . ' on ' . $this->tableWOListHeader . '.driverkey = ' . $this->tableEmployee . '.pkey  
			WHERE ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey and
                  ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey 
 		' . $this->criteria;


        $sql .= $this->getWarehouseCriteria();

        return $sql;
    }

    function validateForm($arr, $pkey = '')
    {
        $security = new Security();
        $disposalWorkOrderDispatcher = new DisposalWorkOrderDispatcher();
        $disposalJobOrder = new DisposalJobOrder();
        $item = new Item();
        $asset = new Asset();

        $arrayToJs = parent::validateForm($arr, $pkey);

        $joKey = $arr['hidJobOrderKey'];
        $customerKey = $arr['hidCustomerKey'];

        if (empty($joKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['reference'][1]);
        }

        if (empty($customerKey)) {
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);
        }

        $overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject, 10);

        if (!$overwriteContractAllowed) {
            $totalPickedUp = $this->getTotalPickedUp($joKey);
            $rsJO = $disposalJobOrder->getDataRowById($joKey);
            if ($totalPickedUp > $rsJO[0]['qtyservice']) {
                $this->addErrorList($arrayToJs, false, '<strong>' . $rsJO[0]['code'] . '</strong>, ' . $this->errorMsg['disposalJobOrder'][4]);
            }
        }

        //        harusnya gk perlu, karena sudah ad SPK list dulu di depan
//        $rsJO = $disposalJobOrder->getDataRowById($joKey);
//        if ($rsJO[0]['statuskey'] <> 3) {
//            $this->addErrorLog($arrayToJs, false, '<strong>' . $rsJO[0]['code'] . '</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsJO[0]['code'] . '</strong>. ' . $this->errorMsg['disposalJobOrder'][2]);
//        }

        return $arrayToJs;
    }

    function getItemDetail($pkey, $criteria = '', $orderby = '')
    {


        $sql = 'select
                ' . $this->tableWorkOrderItemDetail . '.*, 
                ' . $this->tableItem . '.name as itemname, 
                ' . $this->tableItem . '.code as itemcode,
                ' . $this->tableItem . '.sellingprice,
                ' . $this->tableItem . '.deftransunitkey,
                ' . $this->tableItemUnit . '.name as unitname,
                 baseunit.name as baseunitname
              from
                ' . $this->tableWorkOrderItemDetail . ',
                ' . $this->tableItemUnit . ',
                ' . $this->tableItemUnit . ' baseunit,
                ' . $this->tableItem . '
              where
                ' . $this->tableWorkOrderItemDetail . '.itemkey = ' . $this->tableItem . '.pkey and
                  ' . $this->tableWorkOrderItemDetail . '.unitkey = ' . $this->tableItemUnit . '.pkey and
			  	' . $this->tableItem . '.baseunitkey = baseunit.pkey and
		        ' . $this->tableWorkOrderItemDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' . $orderby;

        return $this->oDbCon->doQuery($sql);
    }

    function getAssetDetail($pkey, $criteria = '', $orderby = '')
    {

        $sql = 'select
                ' . $this->tableWorkOrderAssetDetail . '.*, 
                ' . $this->tableAsset . '.name as assetname, 
                ' . $this->tableAsset . '.code as assetcode,
                ' . $this->tableAsset . '.explicensedate
              from
                ' . $this->tableWorkOrderAssetDetail . ',
                ' . $this->tableAsset . '
              where
                ' . $this->tableWorkOrderAssetDetail . '.assetkey = ' . $this->tableAsset . '.pkey and
		        ' . $this->tableWorkOrderAssetDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' . $orderby;

        return $this->oDbCon->doQuery($sql);
    }

    function getDetailWithRelatedInformation($pkey, $criteria = '')
    {

        $sql = 'select
	   			' . $this->tableNameDetail . '.*, 
                ' . $this->tableSupplier . '.name as suppliername,
                concat (' . $this->tableWaste . '.code, " - ", ' . $this->tableWaste . '.name) as waste
			  from
			  	' . $this->tableNameDetail . '
                  left join ' . $this->tableSupplier . ' on ' . $this->tableNameDetail . '.supplierkey = ' . $this->tableSupplier . '.pkey
                  left join ' . $this->tableWaste . ' on ' . $this->tableNameDetail . '.wastekey = ' . $this->tableWaste . '.pkey
			  where
                ' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';
        $sql .= $criteria;

        return $this->oDbCon->doQuery($sql);
    }

    function getInformationForPurchase($dispatchKey, $criteria = '')
    {

        $sql = 'select
	   			' . $this->tableName . '.workordercode, 
	   			' . $this->tableName . '.customerkey, 
	   			' . $this->tableCustomer . '.name as customername, 
	   			' . $this->tableNameDetail . '.customerweight,
	   			' . $this->tableNameDetail . '.manifestcode,
	   			' . $this->tableNameDetail . '.wastekey,
	   			concat (' . $this->tableWaste . '.code, " - ", ' . $this->tableWaste . '.name) as waste
			  from
			  	' . $this->tableName . '
                    left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey,
                ' . $this->tableNameDetail . '
                    left join ' . $this->tableWaste . ' on ' . $this->tableNameDetail . '.wastekey = ' . $this->tableWaste . '.pkey
			  where
              ' . $this->tableName . '.pkey = ' . $this->tableNameDetail . '.refkey and
              ' . $this->tableName . '.statuskey = 3 and
		        ' . $this->tableName . '.refkey in (' . $this->oDbCon->paramString($dispatchKey, ',') . ') ';
        $sql .= $criteria;
        return $this->oDbCon->doQuery($sql);
    }

    function normalizeParameter($arrParam, $trim = false)
    {

        $disposalWorkOrderDispatcher = new DisposalWorkOrderDispatcher();

        // jgn dr workorderlist, tetep bisa beda, ambil ulang berdasarkan 
        $criteria = ' and ' . $disposalWorkOrderDispatcher->tableNameDetail . '.pkey = ' . $this->oDbCon->paramString($arrParam['hidWorkListDetailKey']) ;
        $rsWOListDetail = $disposalWorkOrderDispatcher->getDetailWithRelatedInformation($arrParam['hidWorkListKey'], $criteria);

        $arrParam['hidCustomerKey'] = '';
        $arrParam['hidJobOrderKey'] = '';
        $arrParam['hidServiceKey'] = '';
        $arrParam['hidWorkListDetailKey'] = '';

        if (!empty($rsWOListDetail)) {
            $arrParam['hidCustomerKey'] = $rsWOListDetail[0]['customerkey'];
            $arrParam['hidJobOrderKey'] = $rsWOListDetail[0]['joborderkey'];
            $arrParam['hidServiceKey'] = $rsWOListDetail[0]['servicekey'];
            $arrParam['hidWorkListDetailKey'] = $rsWOListDetail[0]['pkey'];
        }

        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }


    function insertDetailJO($rsHeader, $isValidated = false)
    {

        $disposalJobOrder = new DisposalJobOrder();

        $pkey = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailWithRelatedInformation($pkey);
        $joKey = $rsHeader[0]['jokey'];
        $trDate = $rsHeader[0]['trdate'];
        $disposalWeight = $rsHeader[0]['disposalweight'];

        $rsJO = $disposalJobOrder->getDataRowById($joKey);

        $arrayToJs = array();

        // update setiap SO, sudah brp qty yg ditagih, item dan cost 
        try {

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            if (!empty($rsDetail)) {
                $sql = 'insert into ' . $disposalJobOrder->tableNameDetail . ' (`refkey`,`wokey`, `trdate`, `refdetailwokey`,`disposalweight`, `manifestcode`)
                    values  (' . $this->oDbCon->paramString($joKey) . ', ' . $this->oDbCon->paramString($pkey) . ', 
                    ' . $this->oDbCon->paramString($trDate) . ', ' . $this->oDbCon->paramString($rsDetail[$i]['pkey']) . ', ' . $this->oDbCon->paramString($rsDetail[$i]['customerweight']) . ', ' . $this->oDbCon->paramString($rsDetail[$i]['manifestcode']) . ')';

                $this->oDbCon->execute($sql);
            }

            for ($i = 0; $i < count($rsDetail); $i++) {
                $sql = 'insert into ' . $disposalJobOrder->tableNameDetail . ' (`refkey`,`wokey`, `trdate`, `refdetailwokey`,`disposalweight`, `manifestcode`)
                    values  (' . $this->oDbCon->paramString($joKey) . ', ' . $this->oDbCon->paramString($pkey) . ', 
                    ' . $this->oDbCon->paramString($trDate) . ', ' . $this->oDbCon->paramString($rsDetail[$i]['pkey']) . ', ' . $this->oDbCon->paramString($rsDetail[$i]['customerweight']) . ', ' . $this->oDbCon->paramString($rsDetail[$i]['manifestcode']) . ')';

                $this->oDbCon->execute($sql);
            }


            $this->oDbCon->endTrans();
        } catch (Exception $e) {
            $this->oDbCon->rollback();
        }


        return $arrayToJs;
    }

    function validateBackConfirm($rsHeader)
    {
        $errMsg = $this->checkStatusValidation($rsHeader);

        foreach ($errMsg as $row)
            $this->addErrorLog(false, $row);
    }

    function backConfirmTrans($rsHeader)
    {

        $this->deleteDetailJO($rsHeader[0]['pkey']);
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

    function deleteDetailJO($pkey, $forceDelete = false, $reason = '')
    {

        $arrayToJs = array();
        $disposalJobOrder = new DisposalJobOrder();

        $rsHeader = $this->getDataRowById($pkey);
        $joKey = $rsHeader[0]['jokey'];

        $arrayToJs = array();

        try {

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            $sql = 'delete from ' . $disposalJobOrder->tableNameDetail . ' where wokey = ' . $this->oDbCon->paramString($pkey) . ' and refkey = ' . $this->oDbCon->paramString($joKey);
            $this->oDbCon->execute($sql);

            $this->oDbCon->endTrans();
        } catch (Exception $e) {
            $this->oDbCon->rollback();
        }

        $disposalJobOrder->updateDetailJO($rsHeader[0]['jokey']);

        return $arrayToJs;
    }



    // ====================== CHANGE STATUS


    function afterStatusChanged($rsHeader)
    {
        $disposalJobOrder = new DisposalJobOrder();
        $disposalWorkOrderDispatcher = new DisposalWorkOrderDispatcher();
        $pkey = $rsHeader[0]['pkey'];

        // update status detail SO

        $this->updateQuotaService($pkey);
        // $disposalWorkOrderDispatcher->updateDetailStatus($rsHeader[0]['refkey']);
        // $disposalJobOrder->updateDetailJO($pkey, $rsHeader[0]['jokey']);

    }

    function getTotalPickedUp($jokey, $detailkey = '', $itemkey = '')
    {

        $sql = 'select 
                    coalesce(count(pkey),0) as total
                from  
                    ' . $this->tableName . '
                where   
                    ' . $this->tableName . '.statuskey in (2,3) and
                    ' . $this->tableName . '.jokey = ' . $this->oDbCon->paramString($jokey) . '
                ';

        $rs = $this->oDbCon->doQuery($sql);
        return $rs[0]['total'];
    }

    function updateQuotaService($pkey, $isValidated = false)
    {
        $disposalJobOrder = new DisposalJobOrder();

        $rsHeader = $this->getDataRowById($pkey);
        $joKey = $rsHeader[0]['jokey'];

        $arrayToJs = array();

        // $rsItemDetail = $this->getDetailById($pkey);

        // update setiap SO, sudah brp qty yg ditagih, item dan cost 
        try {

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);


            for ($j = 0; $j < count($rsHeader); $j++) {

                $totalPickedUp = $this->getTotalPickedUp($joKey);

                $sql = 'update 
                            ' . $disposalJobOrder->tableName . '
                        set 
                            quotaserviced = ' . $this->oDbCon->paramString($totalPickedUp) . ' 
                        where  
                            pkey = ' . $this->oDbCon->paramString($joKey) . ' 
                        ';

                $this->oDbCon->execute($sql);
            }

            $this->oDbCon->endTrans();
        } catch (Exception $e) {
            $this->oDbCon->rollback();
        }


        return $arrayToJs;
    }


    function validateConfirm($rsHeader)
    {
        $security = new Security();
        $disposalJobOrder = new DisposalJobOrder();
        $disposalWorkOrderDispatcher = new DisposalWorkOrderDispatcher();
        $driverKey = $rsHeader[0]['driverkey'];
        $carKey = $rsHeader[0]['carkey'];
        $joKey = $rsHeader[0]['jokey'];
        $refkey = $rsHeader[0]['refkey'];
        $customerkey = $rsHeader[0]['customerkey'];
        $refdetailkey = $rsHeader[0]['refdetailkey'];

        // cek Daftar SPK statusnya 
        $rsWorkList = $disposalWorkOrderDispatcher->getDataRowById($refkey);
        if ($rsWorkList[0]['statuskey'] <> 2) {
            // $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsWorkList[0]['code'] . '</strong>. ' . $this->errorMsg[204]);
        }

        // cek JO statusnya 
        $rsJO = $disposalJobOrder->getDataRowById($joKey);
        if ($rsJO[0]['statuskey'] < 4)
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsJO[0]['code'] . '</strong>. ' . $this->errorMsg['disposalJobOrder'][3]);


        $overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject, 10);

        if (!$overwriteContractAllowed) {
            $totalPickedUp = $this->getTotalPickedUp($joKey);
            if ($totalPickedUp >= $rsJO[0]['qtyservice']) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsJO[0]['code'] . '</strong>. ' . $this->errorMsg['disposalJobOrder'][4]);
            }
        }

        //validasi duplikasi dispacth
        //cari spk yang customer / penghasil sama yang satus 2 atau 3
        $rsSPK = $this->searchData('', '', true, ' and ' . $this->tableName . '.refkey = ('. $this->oDbCon->paramString($refkey) .') 
                        and '. $this->tableName .'.refdetailkey = ('. $this->oDbCon->paramString($refdetailkey) .') 
                        and '. $this->tableName .'.customerkey = ('. $this->oDbCon->paramString($customerkey) .') and '. $this->tableName .'.statuskey in (2,3) ');
        if(!empty($rsSPK)) {
            $arrError = array();
            foreach($rsSPK as $spk) {
                array_push($arrError, '<strong>'. $spk['wolistcode'] .'. </strong> '. $spk['customername'] .'. ' . $this->errorMsg[225]);
            }

            if(!empty($arrError)) {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] .'</strong>. ' . $this->errorMsg[201] . '<br>' . implode('<br>', $arrError));
            }
        }
    }

    function validateClose($rsHeader)
    {

        $disposalJobOrder = new DisposalJobOrder();
        $disposalWorkOrderDispatcher = new DisposalWorkOrderDispatcher();

        $joKey = $rsHeader[0]['jokey'];
        $refkey = $rsHeader[0]['refkey'];

        if ($rsHeader[0]['statuskey'] <> 2) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[204], true);
        }

        // cek Daftar SPK statusnya 
        $rsWorkList = $disposalWorkOrderDispatcher->getDataRowById($refkey);
        if ($rsWorkList[0]['statuskey'] <> 2) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsWorkList[0]['code'] . '</strong>. ' . $this->errorMsg[204]);
        }

        // cek JO statusnya 
        $rsJO = $disposalJobOrder->getDataRowById($joKey);
        if ($rsJO[0]['statuskey'] < 4) {
            $this->addErrorLog(false, '<strong>' . $rsJO[0]['code'] . '.</strong> ' . $this->errorMsg['disposalJobOrder'][3]);
        }

        // if ($this->unFormatNumber($rsHeader[0]['disposalweight']) <= 0) {
        //     $this->addErrorLog(false, '<b>'.$rsHeader[0]['code'].'</b>. '.$this->errorMsg['gramasi'][3]); 
        // }
    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {
        $errMsg = $this->checkStatusValidation($rsHeader);

        foreach ($errMsg as $row)
            $this->addErrorLog(false, $row);
    }

    function addErrorMsgArray(&$arr, $content)
    {
        if (!in_array($content, $arr))
            array_push($arr, $content);
    }

    function isWorkOrderInvoiced($wokey)
    {

        $sql = 'select pkey 
                from ' . $this->tableJobOrderDetail . '
                where ' . $this->tableJobOrderDetail . '.wokey = ' . $this->oDbCon->paramString($wokey) . ' and ' . $this->tableJobOrderDetail . '.totalinvoiced > 0 ';

        $rs = $this->oDbCon->doQuery($sql);

        return (empty($rs)) ? false : true;
    }


    function confirmTrans($rsHeader)
    {
        $disposalJobOrder = new DisposalJobOrder();
        // $this->insertDetailJO($rsHeader);
    }

    function closeTrans($rsHeader)
    {

        $apEmployeeCommission = new APEmployeeCommission();
        $disposalJobOrder = new DisposalJobOrder();
        $employee = new Employee();
        $waste = new Waste();
        $service = new Service();
        $pkey = $rsHeader[0]['pkey'];

        $disposalJobOrder->insertJODetail($pkey, $rsHeader[0]['jokey']);


        $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'));
        $rsJO = $disposalJobOrder->getDataRowById($rsHeader[0]['jokey']);
        $rsWasteCategory = $waste->getWasteCategory($rsJO[0]['wastecategorykey']);
        $rsService = $service->getDataRowById($rsJO[0]['servicekey']);
        $rsDetail = $this->getDetailWithRelatedInformation($pkey);
        $amount = 0;

        $commissionDateType = $this->loadSetting('driverCommissionBasedOn');
        $commissionDate = ($commissionDateType == 2) ? $this->formatDBDate($rsHeader[0]['trdate']) : date('d / m / Y');
        if ($rsService[0]['iscommissionpervisit'] == 1) {
            $amount = $rsService[0]['commissionpervisit'];
        } else {
            foreach ($rsDetail as $detail) {
                $rsServiceWaste = $service->getDetailWaste($rsJO[0]['servicedetailwastekey'], 'refkey', $detail['wastekey']);
                $salesCommission = $this->unFormatNumber($rsServiceWaste[0]['salescommission']);
                $salesCommission = (!empty($rsServiceWaste[0]['salescommission'])) ? $this->unFormatNumber($rsServiceWaste[0]['salescommission']) : 0;
                $commission = $salesCommission * $detail['customerweight'];
                $amount += $commission;

            }
        }
        $arStatusKey = 1;
        if ($rsWasteCategory[0]['ismedis'] == 1) {
            $rsJODetail = $disposalJobOrder->getDetailWithRelatedInformation($rsHeader[0]['jokey'], ' and (' . $disposalJobOrder->tableNameDetail . '.refdetailwokey = ' . $rsDetail[0]['pkey'] . ')');
            // $this->setLog($rsJODetail, true);
            $arStatusKey = ($rsJODetail[0]['chargedweight'] == 0) ? 3 : 1;
        }
        // $rsServiceWaste = $service->getDetailWaste($rsJO[0]['servicekey'], 'refheaderkey', '');

        $totalPickedUp = $this->getTotalPickedUp($rsHeader[0]['jokey']);
        $rsTeleSales = $employee->getDetailCommission($rsJO[0]['saleskey'], $rsJO[0]['servicekey']);
        $commissionSales = true;
        if (!empty($rsTeleSales) && $rsTeleSales[0]['commission'] > 0) {
            $commissionSales = false;
        }
        if ($totalPickedUp <= $rsJO[0]['qtyservice'] && $commissionSales && $amount > 0) {
            $note = array();
            array_push($note, $rsHeader[0]['code']);

            $arrParam = array();
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefKey2'] = $rsJO[0]['contractkey'];
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidRefCode'] = $rsHeader[0]['code'];
            $arrParam['hidRefCode2'] = $rsJO[0]['code'];
            $arrParam['hidRefDate'] = $this->formatDBDate($rsHeader[0]['trdate'], 'd / m / Y');
            $arrParam['hidRefTable'] = $tablekey['key'];
            $arrParam['trDesc'] = implode(chr(13), $note);
            $arrParam['trDate'] = $commissionDate;
            $date = new DateTime(date('Y-m-d'));
            $date->add(new DateInterval('P30D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');
            $arrParam['createdBy'] = 0;
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            $arrParam['selAPType'] = AP_TYPE['driverCommission'];
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['hidEmployeeKey'] = $rsJO[0]['saleskey'];
            $arrParam['hidARStatusKey'] = $arStatusKey;

            $arrParam['amount'] = $amount;
            // $arrParam['amount'] =  $rsService[0]['commissionpervisit'];
            // $totalAP += $driverCommissionAmount;


            $arrayToJs = $apEmployeeCommission->addData($arrParam);

            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $arrayToJs[0]['message']);

            $this->updateGLCommission($rsHeader, $amount);
        }


    }

    function updateGLCommission($rs, $commission)
    {
        if (!USE_GL)
            return;

        $disposalJobOrder = new DisposalJobOrder();
        $service = new Service();

        $coaLink = new COALink();
        $warehouse = new Warehouse();
        $generalJournal = new GeneralJournal();
        $employee = new Employee();
        $cost = new Service(TRUCKING_SERVICE, 1);
        $rsJO = $disposalJobOrder->getDataRowById($rs[0]['jokey']);
        $rsService = $service->getDataRowById($rsJO[0]['servicekey']);
        $rsEmployee = $employee->getDataRowById($rsJO[0]['saleskey']);

        $warehousekey = $rs[0]['warehousekey'];

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName, array('key'));
        $rsCOA = $coaLink->getCOALink('commissioncost', $warehouse->tableName, $warehousekey);

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
        // $arr['debit'][$temp] = $rsService[0]['commissionpervisit']; 
        $arr['debit'][$temp] = $commission;
        $arr['credit'][$temp] = 0;

        $coakey = $employee->getAPCommissionCOAKey($rsJO[0]['saleskey'], $warehousekey);

        //akun hutang 
        $temp++;
        $arr['hidCOAKey'][$temp] = $coakey;
        $arr['debit'][$temp] = 0;
        // $arr['credit'][$temp] = $rsService[0]['commissionpervisit']; 
        $arr['credit'][$temp] = $commission;
        $arr['trDesc'] = $this->lang['salesCommission'] . ' ' . $rsEmployee[0]['name'] . '. ' . $rs[0]['workordercode'];

        $arrayToJs = $generalJournal->addData($arr);

        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>' . $rs[0]['code'] . '</strong>. ' . $this->errorMsg[504] . ' ' . $arrayToJs[0]['message']);
    }

    function checkStatusValidation($rsHeader, $arrStatus = array(), $autoChangeStatus = false)
    {
        $errMsg = array();

        $disposalJobOrder = new DisposalJobOrder();
        $disposalWorkOrderDispatcher = new DisposalWorkOrderDispatcher();

        $pkey = $rsHeader[0]['pkey'];
        $jokey = $rsHeader[0]['jokey'];
        $WODispatcherKey = $rsHeader[0]['refkey'];
        $tablekey = $this->getTableKeyAndObj($this->tableName, array('key'));

        $rsWODispatcher = $disposalWorkOrderDispatcher->getDataRowById($WODispatcherKey);
        if ($rsWODispatcher[0]['statuskey'] == 3) {
            $this->addErrorMsgArray($errMsg, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsWODispatcher[0]['code'] . '</strong>, ' . $this->errorMsg[220]);
        }

        // $this->addErrorMsgArray($errMsg, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsJO[0]['code'] . '</strong>, ' . $this->errorMsg[220]);
        //cek Job Order statusnya sudah closed blm
        $rsJO = $disposalJobOrder->getDataRowById($jokey);
        if ($rsJO[0]['statuskey'] == 5) {
            $this->addErrorMsgArray($errMsg, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg[201] . '<br><strong>' . $rsJO[0]['code'] . '</strong>, ' . $this->errorMsg[220]);
        }

        $apEmployeeCommission = new APEmployeeCommission();
        $rsAPEmployeeCommission = $apEmployeeCommission->searchData('', '', true, ' and  ' . $apEmployeeCommission->tableName . '.refkey = ' . $this->oDbCon->paramString($pkey) . ' and ' . $apEmployeeCommission->tableName . '.reftabletype = ' . $tablekey['key'] . '  and (' . $apEmployeeCommission->tableName . '.statuskey in(2,3))');
        if (!empty($rsAPEmployeeCommission))
            $this->addErrorMsgArray($errMsg, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] . ' ' . $this->errorMsg['apCommission'][2]);

        // cek invoice sudah tertagih blm
        // cek sj outstanding di detail jo nya masih sma tdk dgn amount 
        if ($this->isWorkOrderInvoiced($pkey)) {
            $this->addErrorMsgArray($errMsg, '<strong>' . $rsHeader[0]['code'] . '.</strong> ' . $this->errorMsg['disposalWorkOrder'][2]);
        }

        return $errMsg;

    }


    function cancelTrans($rsHeader, $copy)
    {
        // $warehouse = new Warehouse();
        // $employee = new Employee();
        $this->deleteDetailJO($rsHeader[0]['pkey']);

        $this->cancelCommissionAP($rsHeader);
        $this->cancelGLByRefkey($rsHeader[0]['pkey'], $this->tableName);
        if ($copy)
            $this->copyDataOnCancel($rsHeader[0]['pkey']);
    }

    function getDisposalWorkOrderByDispatcher($pkey,  $arrDetailKey = array(), $criteria  = '')
    {
        
        $sql = '
            SELECT
                '. $this->tableName .'.*,
                concat(' . $this->tableName . '.refdetailkey, \'-\', ' . $this->tableName .'.refkey) as indexkey
            FROM
                '. $this->tableName .'
            WHERE
                '. $this->tableName .'.refkey in ('. $this->oDbCon->paramString($pkey,',') .') 
                and ' . $this->tableName . '.statuskey in (1,2,3)
        ';

        if(!empty($arrDetailKey)) {
            $sql .= ' and ' . $this->tableName . '.refdetailkey in (' . $this->oDbCon->paramString($arrDetailKey, ',') . ') ';
        }

        if(!empty($criteria)) {
            $sql .= $criteria; 
        }

        $rs = $this->oDbCon->doQuery($sql);
       
        return $rs;
    }

}
?>
