<?php

class TruckingAdditionalCost extends BaseClass
{


    function __construct()
    {

        parent::__construct();

        $this->tableName = 'trucking_additional_cost';
        $this->tableEmployee = 'employee';
        $this->tableWarehouse = 'warehouse';
        $this->tableTruckingServiceWorkOrder = 'trucking_service_work_order';
        $this->tableTruckingServiceOrderHeader = 'trucking_service_order_header';
        $this->tableWorkOrderCost = 'trucking_service_work_order_cost';
        $this->tableTruckingCost = 'item';
        $this->tableEmployee = 'employee';
        $this->tableStatus = 'transaction_status';

        $this->securityObject = 'TruckingAdditionalCost';
        $this->isTransaction = true;
        $this->newLoad = true;

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['refworkorderkey'] = array('hidWorkOrderKey');
        $this->arrData['refjoborderkey'] = array('hidJobOrderKey');
        $this->arrData['employeekey'] = array('hidEmployeeKey');
        $this->arrData['paidto'] = array('paidTo');
        $this->arrData['servicekey'] = array('hidServiceKey');
        $this->arrData['amount'] = array('amount','number');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['containernumber'] = array('containerNumber');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 120, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse', 'title' => 'warehouse', 'dbfield' => 'warehousename', 'width' => 120, 'default'  => true, 'width' => 120,));
        array_push($this->arrDataListAvailableColumn, array('code' => 'employeename', 'title' => 'driver', 'dbfield' => 'employeename', 'width' => 150, 'default'  => true));
        array_push($this->arrDataListAvailableColumn, array('code' => 'workOrderCode', 'title' => 'workOrderCode', 'dbfield' => 'workordercode', 'width' => 150, 'default'  => true));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobOrderCode', 'title' => 'jobOrderCode', 'dbfield' => 'jobordercode', 'width' => 150, 'default'  => true));
        array_push($this->arrDataListAvailableColumn, array('code' => 'service', 'title' => 'service', 'dbfield' => 'servicename', 'width' => 180, 'default'  => true));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount', 'title' => 'amount', 'dbfield' => 'amount', 'width' => 150, 'default'  => true, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdesc', 'title' => 'note', 'dbfield' => 'trdesc', 'width' => 200, 'default'  => true));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'width' => 120, 'default'  => true));

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Tanggal', $this->tableName . '.trdate'));
        array_push($this->arrSearchColumn, array('Total', $this->tableName . '.amount'));
        array_push($this->arrSearchColumn, array('Employee', $this->tableEmployee . '.name'));
        array_push($this->arrSearchColumn, array('Status', $this->tableStatus . '.status'));

        $this->printMenu = array();
        array_push($this->printMenu, array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'], 'icon' => 'print', 'url' => 'print/truckingAdditionalCost'));


        $this->includeClassDependencies(array(
            'Employee.class.php',
            'Warehouse.class.php',
            'TruckingServiceOrder.class.php',
            'TruckingServiceWorkOrder.class.php',
            'Service.class.php'
        ));

        $this->overwriteConfig();

    }

    function getQuery()
    {

        $sql = '
            select 
                '. $this->tableName .'.*,
                '. $this->tableTruckingServiceWorkOrder .'.code as workordercode,
                '. $this->tableTruckingServiceOrderHeader .'.code as jobordercode,
                '. $this->tableStatus .'.status as statusname,
                '. $this->tableWarehouse .'.name as warehousename,
                '. $this->tableTruckingCost  .'.name as servicename,
                '. $this->tableEmployee  .'.name as employeename
            from
                '. $this->tableName .'
                    left join '. $this->tableTruckingServiceWorkOrder .' on '. $this->tableName .'.refworkorderkey = '. $this->tableTruckingServiceWorkOrder .'.pkey
                    left join '. $this->tableTruckingServiceOrderHeader .' on  '. $this->tableName .'.refjoborderkey = '. $this->tableTruckingServiceOrderHeader .'.pkey
                    left join '. $this->tableTruckingCost  .' on '. $this->tableName .'.servicekey = '. $this->tableTruckingCost  .'.pkey
                    left join '. $this->tableEmployee  .' on '. $this->tableName .'.employeekey = '. $this->tableEmployee  .'.pkey,
                ' . $this->tableWarehouse . ',
                '. $this->tableStatus .'
            where
                ' . $this->tableName . '.warehousekey = ' . $this->tableWarehouse . '.pkey and
				' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey 
 		' . $this->criteria;

        return $sql;

    }

    function validateForm($arr, $pkey = '')
    {

        $arrayToJs = parent::validateForm($arr, $pkey);

        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();

        $workOrder = $arr['hidWorkOrderKey'];
        $jobOrder = $arr['hidJobOrderKey'];
        $employee = $arr['hidEmployeeKey'];
        $service = $arr['hidServiceKey'];
        $amount = $this->unFormatNumber($arr['amount']);
        $selWarehouse = $arr['selWarehouse'];


        if(empty($workOrder)) {
            $this->addErrorList($arrayToJs,false, $this->errorMsg['truckingServiceWorkOrder'][1]);
        }else{
            
            // warehouse dan JO overwrite saja
            
//            $rsWO = $truckingServiceWorkOrder->searchDataRow(array(
//                $truckingServiceWorkOrder->tableName . '.pkey',
//                $truckingServiceWorkOrder->tableName . '.code',
//                $truckingServiceWorkOrder->tableName . '.refkey',
//                $truckingServiceWorkOrder->tableName . '.warehousekey',
//                $truckingServiceWorkOrder->tableName . '.statuskey',
//                $truckingServiceWorkOrder->tableName . '.driverkey'
//            ), ' and ' . $truckingServiceWorkOrder->tableName . '.pkey in (' . $this->oDbCon->paramString($workOrder, ',') . ') ');
//    
//             if ($selWarehouse <> $rsWO[0]['warehousekey']) {
//                $this->addErrorList($arrayToJs, false, '<strong>' . $rsWO[0]['code'] . '. </strong> ' . $this->errorMsg['truckingAdditionalCost'][3]);
//            }

//            if($jobOrder <> $rsWO[0]['refkey']) {
//                $this->addErrorList($arrayToJs,false, $this->errorMsg['truckingAdditionalCost'][2]);
//            }


        }

        
//        if(empty($jobOrder)) {
//            $this->addErrorList($arrayToJs,false, $this->errorMsg['jobOrder'][1]);
//        }

//boleh kosong diawal karena blm tentu sudah tau siapa penerimanya      
//        if(empty($employee)) {
//            $this->addErrorList($arrayToJs, false, $this->errorMsg['driver'][1]);
//        }

        if(empty($service)) {
            $this->addErrorList($arrayToJs,false, $this->errorMsg['service'][1]);
        }

        if($amount <= 0) {
            $this->addErrorList($arrayToJs,false, $this->errorMsg[503]);
        }

        return $arrayToJs;
    }

    function validateConfirm($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];
    
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        $truckingServiceOrder = new TruckingServiceOrder();

        $rsWO = $truckingServiceWorkOrder->searchDataRow(array(
                    $truckingServiceWorkOrder->tableName.'.pkey',
                    $truckingServiceWorkOrder->tableName.'.code',
                    $truckingServiceWorkOrder->tableName.'.refkey',
                    $truckingServiceWorkOrder->tableName.'.warehousekey',
                    $truckingServiceWorkOrder->tableName.'.statuskey',
                    $truckingServiceWorkOrder->tableName.'.driverkey'
                ), ' and ' .  $truckingServiceWorkOrder->tableName.'.pkey in ('. $this->oDbCon->paramString($rsHeader[0]['refworkorderkey'],',') .') ');

        if (!in_array($rsWO[0]['statuskey'], array(TRANSACTION_STATUS['konfirmasi']))) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '. </strong>' . $this->errorMsg['truckingAdditionalCost'][1]);
        }

        //cek job order sesuai spk tidak
//        if($rsHeader[0]['refjoborderkey'] <> $rsWO[0]['refkey']) {
//            $rsJO = $truckingServiceOrder->getDataRowById($rsHeader[0]['refjoborderkey']);
//            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '<br> </strong>' . $rsJO[0]['code'] . '. ' . $this->errorMsg['truckingAdditionalCost'][2]);
//        }

//        if($rsHeader[0]['warehousekey'] <> $rsWO[0]['warehousekey']) {
//            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '<br> </strong>' . $rsWO[0]['code'] . '. ' . $this->errorMsg['truckingAdditionalCost'][3]);
//        }

        // ketika konfirmasi nama sopir harus sudah tau
        if (empty($rsHeader[0]['employeekey'])) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] .'</strong> '. $this->errorMsg['driver'][1]);
        } else {
             
            // gk wajib sama
             
        }

    }

    function confirmTrans($rsHeader)
    {
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();

        $id = $rsHeader[0]['pkey'];

        //update detail cost spk
        $this->addWorkOrderCostDetail($rsHeader);
        
    }

    function validateCancel($rsHeader, $autoChangeStatus = false)
    {
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();

        $id = $rsHeader[0]['pkey'];

        $workOrderKey = $rsHeader[0]['refworkorderkey'];
        $serviceKey = $rsHeader[0]['servicekey'];
  
        $rsWorkOrderCost = $truckingServiceWorkOrder->getCostDetail($workOrderKey, $serviceKey,  ' and ' . $this->tableWorkOrderCost . '.refadditionalcostkey = '.$this->oDbCon->paramString($id).'  and ' . $this->tableWorkOrderCost . '.refcashoutkey != 0');
        if(!empty($rsWorkOrderCost))  
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '. </strong>' . $this->errorMsg[201] . '<br> <strong>' . $rsWorkOrderCost[0]['name'] . '.</strong> '  . $this->errorMsg['truckingAdditionalCost'][4]);
        

    }

    function cancelTrans($rsHeader, $copy)
    {
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();

        $id =  $rsHeader[0]['pkey'];

        //cancel detail cost spk
        $this->cancelWorkOrderCostDetail($rsHeader);
        // $truckingServiceWorkOrder->cancelCashOut($workOrderKey);
        
		if ($copy)
			$this->copyDataOnCancel($id);	
        
    }

    function afterStatusChanged($rsHeader)
    {
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        
        $id = $rsHeader[0]['pkey'];
        
		// ambil ulang agar dpt status baru
		$rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
         
        //update ulang TCO 
        if(in_array($rsHeader[0]['statuskey'], array(TRANSACTION_STATUS['konfirmasi'],TRANSACTION_STATUS['batal']))){ 
            
            $truckingServiceOrder = new TruckingServiceOrder();
            
            $truckingServiceWorkOrder->updateTruckingCostCashOut($rsHeader[0]['refworkorderkey']);  
            $truckingServiceOrder->updateSalesWorkOrderCost($rsHeader[0]['refjoborderkey']);
        }
        
    }

    function addWorkOrderCostDetail($rsHeader)
    {

        $qty = 1;
        $id = $rsHeader[0]['pkey'];
        $refkey = $rsHeader[0]['refworkorderkey'];
        $costkey = $rsHeader[0]['servicekey'];
        $amount = $rsHeader[0]['amount'];
        $total = $qty * $amount;

        $employeekey = ($rsHeader[0]['paidto'] == 1 ? $rsHeader[0]['employeekey'] : 0);
 
        $sql = '
            INSERT INTO
                '.  $this->tableWorkOrderCost .'
                (qty,refkey,refadditionalcostkey,costkey,amount,requestamount,total,employeekey) 
            values
                (' . $this->oDbCon->paramString($qty) . ',
                ' . $this->oDbCon->paramString($refkey) . ',
                ' . $this->oDbCon->paramString($id) . ',
                ' . $this->oDbCon->paramString($costkey) . ',
                ' . $this->oDbCon->paramString($amount) . ',
                ' . $this->oDbCon->paramString($amount) . ',
                ' . $this->oDbCon->paramString($total) . ',
                ' . $this->oDbCon->paramString($employeekey) . '
            )';

        $this->oDbCon->execute($sql);

    }

    function cancelWorkOrderCostDetail($rsHeader) 
    {
        $id = $rsHeader[0]['pkey'];
        $refkey = $rsHeader[0]['refworkorderkey'];
        $servicekey = $rsHeader[0]['servicekey'];
        
        $sql = '
            DELETE FROM 
                ' . $this->tableWorkOrderCost . ' 
            WHERE
                ' . $this->tableWorkOrderCost . '.refkey =  ' . $this->oDbCon->paramString($refkey) . ' and
                ' . $this->tableWorkOrderCost . '.costkey =  ' . $this->oDbCon->paramString($servicekey) . ' and
                ' . $this->tableWorkOrderCost . '.refadditionalcostkey = '.$this->oDbCon->paramString($id) .' 
        ';

        $this->oDbCon->execute($sql);

    }

    function normalizeParameter($arrParam, $trim = false)
    {
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        // update ulang jokey dan warehosusekey
        
        $workOrderKey = $arrParam['hidWorkOrderKey'];
            
        $rsSPK =  $truckingServiceWorkOrder->searchDataRow(array(
                  $truckingServiceWorkOrder->tableName . '.pkey', 
                  $truckingServiceWorkOrder->tableName . '.refkey',
                  $truckingServiceWorkOrder->tableName . '.warehousekey' 
            ), ' and ' . $truckingServiceWorkOrder->tableName . '.pkey in (' . $this->oDbCon->paramString($workOrderKey, ',') . ') ');
    
        
         $arrParam['selWarehouse'] = $rsSPK[0]['warehousekey'];
         $arrParam['hidJobOrderKey']= $rsSPK[0]['refkey'];
        
        $arrParam = parent::normalizeParameter($arrParam, true); 
        return $arrParam;
    }

}
?>