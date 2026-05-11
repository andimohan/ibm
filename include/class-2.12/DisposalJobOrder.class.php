<?php
class DisposalJobOrder extends BaseClass{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'disposal_job_order_header';
        $this->tableNameDetail = 'disposal_job_order_detail';
        $this->tableStatus = 'disposal_job_order_status';
        $this->tableDetailStatus = 'disposal_job_order_detail_status';
        $this->tableDetailWaste = 'disposal_job_order_waste_detail';
        $this->tableWaste = 'waste';
        $this->tableItem = 'item';
        $this->tableItemUnit = 'item_unit';
        $this->tableCustomer = 'customer';
        $this->tableAR = 'ar';
        $this->tableARStatus = 'ar_status';

        $this->tableCity = 'city';
        $this->tableService = 'item';
        $this->tableContract = 'disposal_contract';
        $this->tableWorkOrder = 'disposal_work_order_header';
        $this->tablePartialInvoice = 'disposal_job_order_partial_invoice';
        $this->securityObject = 'DisposalJobOrder';
        $this->tableFile = 'disposal_job_order_file';
        $this->uploadFileFolder = 'disposal-job-order-file/';
        $this->isTransaction = true;
        $this->newLoad = true;
        $this->overwriteContractSecurityObject = 'overwriteContract';

        $this->arrWaste = array();
        $this->arrWaste['pkey'] = array('hidWasteDetailKey');
        $this->arrWaste['refkey'] = array('pkey', 'ref');
        $this->arrWaste['wastekey'] = array('hidWasteKey', array('mandatory' => true));
        $this->arrWaste['maxweight'] = array('maxWeight', 'number');
        $this->arrWaste['minweight'] = array('minWeight', 'number');
        $this->arrWaste['weightprice'] = array('weightPrice', 'number');
 
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrWaste, 'tableName' => $this->tableDetailWaste));
        array_push($arrDetails, array(
            'dataset' => $this->arrDataFile, 'tableName' => $this->tableFile,
            'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
            'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'
        ));

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['trdate'] = array('trDate', 'date');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['code'] = array('code');
        $this->arrData['servicekey'] = array('hidServiceKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['contractkey'] = array('hidContractKey');
        $this->arrData['citykey'] = array('hidCityKey');
        $this->arrData['areakey'] = array('hidAreaKey');
        $this->arrData['sellingprice'] = array('sellingPrice', 'number');
        $this->arrData['maximumweight'] = array('maximumWeight', 'number');
        $this->arrData['extraprice'] = array('exceedWeightPriceArea', 'number');
        $this->arrData['exceedprice'] = array('exceedSellingPriceArea', 'number');
        $this->arrData['qtyservice'] = array('qtyService', 'number');
        $this->arrData['duration'] = array('duration', 'number');
        $this->arrData['totaluninvoiced'] = array('totaluninvoiced', 'number');
        $this->arrData['saleskey'] = array('hidSalesKey');
        $this->arrData['wastecategorykey'] = array('hidWasteCategoryKey');
        $this->arrData['isprepaid'] = array('chkIsPrePaid');
        $this->arrData['servicedetailwastekey'] = array('hidServiceDetailWasteKey');
//        $this->arrData['total'] = array('total', 'number'); // ambil dr selling price aj
        $this->arrData['grandtotal'] = array('grandtotal', 'number');
//        $this->arrData['contractduration'] = array('contractDuration', 'number');

        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date', 'title' => 'date', 'dbfield' => 'trdate', 'default' => true, 'width' => 80, 'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'contract', 'title' => 'contract', 'dbfield' => 'contractcode', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer', 'title' => 'customer', 'dbfield' => 'customername', 'default' => true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'service', 'title' => 'service', 'dbfield' => 'servicename', 'default' => true, 'width' => 160));
        array_push($this->arrDataListAvailableColumn, array('code' => 'uninvoiced', 'title' => 'uninvoiced', 'dbfield' => 'totaluninvoiced', 'default' => true, 'width' => 120, 'align' => 'right', 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'selingPrice', 'title' => 'sellingPrice', 'dbfield' => 'sellingprice', 'default' => true, 'width' => 120, 'align' => 'right', 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note', 'title' => 'note', 'dbfield' => 'trdesc',   'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 100));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Customer', $this->tableCustomer . '.name'));
        array_push($this->arrSearchColumn, array('service', $this->tableService . '.name'));
        array_push($this->arrSearchColumn, array('Kode Kontrak', $this->tableContract . '.code'));
        array_push($this->arrSearchColumn, array('ststus', $this->tableStatus . '.status'));

 
        $this->includeClassDependencies(array(
            'Customer.class.php',
            'DisposalWorkOrderDispatcher.class.php',
            'DisposalWorkOrder.class.php',
            'City.class.php',
            'CityCategory.class.php',
            'DisposalContract.class.php',
            'Service.class.php',
            'Item.class.php',
            'Supplier.class.php',
            'TermOfPayment.class.php',
            'DisposalSalesInvoice.class.php',
            'Waste.class.php',
        ));

        $this->overwriteConfig();
    }


    function getQuery()
    {

        $sql = '
                 select
                     ' . $this->tableName . '.*, 
                     ' . $this->tableCustomer . '.code as customercode,
                     ' . $this->tableCustomer . '.name as customername,
                     ' . $this->tableCustomer . '.invoicingtypekey,
                     ' . $this->tableService . '.name as servicename,
                     ' . $this->tableCity . '.name as cityname,
                     ' . $this->tableContract . '.code as contractcode,
                     ' . $this->tableContract . '.name as contractname,
                     ' . $this->tableStatus . '.status as statusname
                 from 
                     ' . $this->tableName . ' 
                     left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey
                     left join ' . $this->tableCity . ' on ' . $this->tableName . '.citykey = ' . $this->tableCity . '.pkey 
                     left join ' . $this->tableContract . ' on ' . $this->tableName . '.contractkey = ' . $this->tableContract . '.pkey
                     left join ' . $this->tableService . ' on ' . $this->tableName . '.servicekey = ' . $this->tableService . '.pkey,
                     ' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey
          ' . $this->criteria;
        return $sql;
    }

    function delete($id,$forceDelete = false, $reason = ''){ 
         $arrayToJs = $this->changeStatus($id, 6,$reason,false,$forceDelete);   
         return $arrayToJs;  
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
                    if ($rsHeader[0]['statuskey'] < $status) {
                        $this->validateConfirm($rsHeader);
                    } else {
                        $this->validateBackConfirm($rsHeader);
                    }
                    break;
                case 3:
                    $this->validateInvoiced($rsHeader);
                    break;
                case 4:
                    $this->validateActive($rsHeader,$autoChangeStatus);
                    break;
                case 5:
                    $this->validateClose($rsHeader);
                    break;
                case 6:
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
                case 6:
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
//        if ($rsHeader[0]['statuskey'] >= 4)
//            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201]);
    }
 
    function updateUninvoicedJO($pkey)
    {
        $service = new Service();
        $customer = new Customer();
        
        $rsHeader = $this->getDataRowById($pkey);
        $rsDetail = $this->getDetailWithRelatedInformation($pkey);
        $rsService = $service->getDataRowById($rsHeader[0]['servicekey']);   
        $rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);   
        
        $totalUnInvoice = 0;
       // $sellingPrice = $rsHeader[0]['grandtotal'] - $rsHeader[0]['totalserviceinvoiced'];
      //$totalUnInvoice += $sellingPrice;

        if ( ($rsService[0]['isprepaid'] == 1) ||
             ($rsService[0]['isprepaid'] <> 1 && !empty($rsDetail))
           ){
            $totalUnInvoice += $rsHeader[0]['sellingprice'];
        } 
             
        
        $totalUnInvoice -= $rsHeader[0]['totalserviceinvoiced'];

        foreach ($rsDetail as $detail) {
            // $weight = ceil($detail['chargedweight']);
            $weight = ($rsCustomer[0]['invoicingtypekey'] == 1) ? ceil($detail['chargedweight']) : $detail['chargedweight'];  
            $amount = ($weight * $detail['priceinunit']) + $detail['exceedprice'] - $detail['totalinvoiced'];
            $totalUnInvoice += $amount;
        }
        $totalUnInvoice = ($totalUnInvoice < 0) ? 0 : $totalUnInvoice; 
        $sql = 'update ' . $this->tableName. ' 
                set
                    totaluninvoiced = '.$this->oDbCon->paramString($totalUnInvoice).'
                where pkey =  ' . $this->oDbCon->paramString($pkey).'
        '; 
        
        $this->oDbCon->execute($sql);
    }
 

   function getInvoiceInformation($pkey, $statuskey = array(2, 3)){
        if (!is_array($statuskey)) $statuskey = array($statuskey);

        $disposalSalesInvoice = new DisposalSalesInvoice();
        $rsKey = $disposalSalesInvoice->getTableKeyAndObj($disposalSalesInvoice->tableName,array('key'));

        $sql = 'select
            ' . $disposalSalesInvoice->tableNameDetail . '.salesorderkey,     
            ' . $disposalSalesInvoice->tableName . '.pkey,
            ' . $disposalSalesInvoice->tableName . '.code,    
            ' . $disposalSalesInvoice->tableName . '.trdate,
            ' . $disposalSalesInvoice->tableName . '.isdownpayment,
            ' . $disposalSalesInvoice->tableName . '.termofpaymentkey,
            ' . $disposalSalesInvoice->tableName . '.customerkey,
            ' . $disposalSalesInvoice->tableName . '.grandtotal,
            ' . $disposalSalesInvoice->tableName . '.statuskey,
            ' . $disposalSalesInvoice->tableName . '.requestid,
            ' . $disposalSalesInvoice->tableStatus . '.status as statusname,
            ' . $disposalSalesInvoice->tableNameDetail . '.pkey as detailpkey,
            ' . $disposalSalesInvoice->tableNameDetail . '.amount,
            ' . $disposalSalesInvoice->tableNameDetail . '.serviceprice,
            ' . $disposalSalesInvoice->tableCustomCode . '.pkey as invoicetypekey,
            ' . $disposalSalesInvoice->tableCustomCode . '.name as invoicetypename,
            ' . $disposalSalesInvoice->tableAR.'.statuskey as arstatuskey,
            ' . $disposalSalesInvoice->tableARStatus.'.status as arstatusname
          from 
            ' . $disposalSalesInvoice->tableName . '
                left join '. $disposalSalesInvoice->tableAR.' on '.$disposalSalesInvoice->tableAR.'.refheaderkey = '.$disposalSalesInvoice->tableName.'.pkey and
                              '.$disposalSalesInvoice->tableAR.'.reftabletype = '. $disposalSalesInvoice->oDbCon->paramString($rsKey['key']).' 
                left join '.$disposalSalesInvoice->tableARStatus.' on '.$disposalSalesInvoice->tableAR.'.statuskey = '.$disposalSalesInvoice->tableARStatus.'.pkey and
                            '.$disposalSalesInvoice->tableAR.'.statuskey <> 4,
            ' . $disposalSalesInvoice->tableStatus . ',
            ' . $disposalSalesInvoice->tableNameDetail . ',
            ' . $disposalSalesInvoice->tableCustomCode . '
          where  
            ' . $disposalSalesInvoice->tableNameDetail . '.salesorderkey in (' . $this->oDbCon->paramString($pkey, ',') . ') and   
            ' . $disposalSalesInvoice->tableName . '.pkey = ' . $disposalSalesInvoice->tableNameDetail . '.refkey and
            ' . $disposalSalesInvoice->tableName . '.statuskey = ' . $disposalSalesInvoice->tableStatus . '.pkey and
            ' . $disposalSalesInvoice->tableName . '.statuskey in (' . $this->oDbCon->paramString($statuskey, ',') . ') and
            ' . $disposalSalesInvoice->tableName . '.customcodekey =  ' . $disposalSalesInvoice->tableCustomCode . '.pkey';

        return $this->oDbCon->doQuery($sql);
    }
    
    function addErrorMsgArray(&$arr,$content){
        if(!in_array($content,$arr ))
             array_push($arr,$content);   
    }
    
    function checkStatusValidation($rsHeader,$arrStatus=array(),$autoChangeStatus = false){
        
        if ($autoChangeStatus)
	 	 return; 

        $errMsg = array();
        
        
//        $security = new Security();
        //$overwriteContractAllowed = $security->hasSecurityAccess( $this->userkey ,$security->getSecurityKey($this->overwriteContractSecurityObject),10);

        
        foreach($arrStatus as $statusRow){
            switch($statusRow){
                    
                case '3' : 
                            
                            $rsInvoice = $this->getInvoiceInformation($rsHeader[0]['pkey']);
                            $totalInvoice = 0;

                            foreach ($rsInvoice as $invoice)
                                   $totalInvoice += $invoice['serviceprice'];

                            // yg penting tagihan service utamanya dulu lunas
                            if ($totalInvoice < $rsHeader[0]['sellingprice'])
                                $this->addErrorMsgArray($errMsg, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[506]); 
                     
                            break;
                    
                case '4' :   
                    
                            $termOfPayment = new TermOfPayment(); 
                            $rsInvoice = $this->getInvoiceInformation($rsHeader[0]['pkey']);

                            // asumsi 1 jo hanya ad 1 invoice 
                            // better jaga2, loop saja setiap invoice outstandingnya brp yg blm byr
                            if(empty($rsInvoice)){
                                // kalo blm pernah diinvoice  
                                $this->addErrorMsgArray($errMsg, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[506]);  
                            } else {
                                    
                                // jumlahin semua tagihan yg sudah dibayar utk service utama
                                $totalInvoice = 0;
                                
                                foreach($rsInvoice as $invoiceRow){
                                    $rsTOP = $termOfPayment->getDataRowById($invoiceRow['termofpaymentkey']); 
                                    $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;  

                                    // kalo tempo,
                                    if ($isCash || (!$isCash && $invoiceRow['arstatuskey'] == 3)) 
                                        $totalInvoice += $invoiceRow['serviceprice'];
                                       
                                }
                              
                                if($totalInvoice < $rsHeader[0]['sellingprice'])
                                 $this->addErrorMsgArray($errMsg, '<strong>' . $invoiceRow['code'] . '</strong>. ' . $this->errorMsg['invoice'][7]);  
                           
                            }  
                    
                    
                                     
                            break;
            }
        }
        
        return $errMsg;
        
    }
  
    function validateInvoiced($rsHeader){
        
        $errMsg = $this->checkStatusValidation($rsHeader,array(3));
            
        foreach($errMsg as $row) 
            $this->addErrorLog(false, $row);
      
    }
    
 
    function validateActive($rsHeader,$autoChangeStatus)  { 
 
        // cek dulu memang valid gk utk aktif
        
        // cek apakaah 
        // 1. sudah diinvoice semua, dari nilai outstanding
        // 2. apakah sudh lunas semua, dr invoice
        
         
        $errMsg = $this->checkStatusValidation($rsHeader,array(3,4),$autoChangeStatus);
        
        foreach($errMsg as $row) 
            $this->addErrorLog(false, $row);
        
//       
//            $security = new Security();
//            $termOfPayment = new TermOfPayment(); 
//
//            $overwriteContractAllowed = $security->hasSecurityAccess( $this->userkey ,$security->getSecurityKey($this->overwriteContractSecurityObject),10);
//
//            // jika punya akses untuk approval JO tanpa pembayaran
//            if (!$overwriteContractAllowed) {
//                $rsInvoice = $this->getInvoiceInformation($rsHeader[0]['pkey']);
//
//                // asumsi 1 jo hanya ad 1 invoice 
//                // better jaga2, loop saja setiap invoice outstandingnya brp yg blm byr
//                if (!empty($rsInvoice)) {
//
//                    $rsTOP = $termOfPayment->getDataRowById($rsInvoice[0]['termofpaymentkey']); 
//                    $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;  
//
//                    // kalo tempo,
//                    if (!$isCash && $rsInvoice[0]['arstatuskey'] <> 3) {
//                        $this->addErrorLog(false, '<strong>' . $rsInvoice[0]['code'] . '</strong>. ' . $this->errorMsg['invoice'][7]);
//                    }else{
//                        // kalo cash harusnya gk perlu validasi apa2    
//                    }
//
//                } else {
//                    // kalo blm pernah diinvoice
//                    $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[506]);
//                }
//            }else{ 
//                // cek memang statusny sudah aktif atau blm 
//
//            }
      
 
    }
 

    function validateConfirm($rsHeader)
    {

        $id = $rsHeader[0]['pkey'];
        $contractKey = $rsHeader[0]['contractkey'];
        $grandtotal = $rsHeader[0]['grandtotal'];

        if (empty($contractKey)) {
            $this->addErrorList($arrayToJs, false,'<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['contract'][1]);
        }else{
            $disposalContract = new DisposalContract();
            $rsContract = $disposalContract->getDataRowById($rsHeader[0]['contractkey']);
            if (!empty($rsContract)) {
                $expDate = strtotime($rsContract[0]['validdate']);
                $validDate = strtotime($rsHeader[0]['trdate']);
                $totalJO = $this->getQtyJO($rsHeader[0]['contractkey']);

                if ($rsContract[0]['statuskey'] <> 2) 
                    $this->addErrorLog(false,'<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['contract'][5]); 

                if ($expDate < $validDate)  
                    $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' .$this->errorMsg['contract'][6]);

                if ($totalJO >= $rsContract[0]['qtyjo']) 
                    $this->addErrorLog(false,  '<strong>' . $rsHeader[0]['code'] . '</strong>. '.$this->errorMsg['disposalJobOrder'][4]);
               
                
            } else {
                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' .$this->errorMsg['contract'][1]); 
            }

        }
        // $permission = $this->getPermissionJO($rsHeader[0]['customerkey']);
        // if (!$permission) {
        //     $customer = new Customer();
        //     $rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);
        //     $this->addErrorLog(false, '<strong>' . $rsCustomer[0]['name'] . '</strong>. ' .$this->errorMsg['disposalJobOrder'][5]); 
        // }
        
    //   if ($grandtotal <= 0) 
    //        $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' .$this->errorMsg['sellingPrice'][1]);  
       
 
    }

    function validateClose($rsHeader)  {
    
        // sepertinya gk perlu, karena user boleh closing job mnual klo periode ny habis
        
//        $security = new Security();
//        $disposalWorkOrder = new DisposalWorkOrder();
//        $overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject, 10);
//
//         if ($rsHeader[0]['statuskey'] <> 4)  
//            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg[201] .' '. $this->errorMsg[206]);
//    
//
//        // jika punya akses untuk closing manual
//        // nanti dicek lg
//        
//        $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['servicePackage'][4]);
//        
//        if (!$overwriteContractAllowed) {
//            $quotaService = $disposalWorkOrder->getTotalQuotaService($rsHeader[0]['pkey']);
//            if ($rsHeader[0]['qtyservice'] > $quotaService) {
//                $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. ' . $this->errorMsg['servicePackage'][4]);
//            }
//        }
    }
 
    function validateCancel($rsHeader, $autoChangeStatus = false)  {
        $disposalSalesInvoice = new DisposalSalesInvoice();
        $disposalWorkOrderDispatcher = new DisposalWorkOrderDispatcher();
         
        $pkey = $rsHeader[0]['pkey'];
    
        
        // validasi spk list berdasarkan job key
        $sql = 'select 
                    '.$disposalWorkOrderDispatcher->tableName.'.pkey ,
                    '.$disposalWorkOrderDispatcher->tableName.'.code 
                from
                    '.$disposalWorkOrderDispatcher->tableName.','.$disposalWorkOrderDispatcher->tableNameDetail.'
                where
                    '.$disposalWorkOrderDispatcher->tableName.'.pkey = '.$disposalWorkOrderDispatcher->tableNameDetail.'.refkey and
                    '.$disposalWorkOrderDispatcher->tableNameDetail.'.joborderkey =  '.$this->oDbCon->paramString($pkey).' and
                    '.$disposalWorkOrderDispatcher->tableName.'.statuskey in (2,3) 
        ';
        
        $rsWorkOrderDispatcher = $this->oDbCon->doQuery($sql); 
        if (!empty($rsWorkOrderDispatcher)) {
            $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. <strong>' .$rsWorkOrderDispatcher[0]['code'].'</strong>, '. $this->errorMsg['disposalJobOrder'][2] .'</strong>');
        }
                           
        //cek invoiced
        //by default sudah cari yg staatus invoicenya 2,3
       $rsInvoiced = $this->getInvoiceInformation($pkey);
       if (!empty($rsInvoiced)) 
           $this->addErrorLog(false, '<strong>' . $rsHeader[0]['code'] . '</strong>. <strong>' .$rsInvoiced[0]['code'].'</strong>, '. $this->errorMsg['disposalJobOrder'][3] .'</strong>');
       
    }



    function validateForm($arr, $pkey = '') {

        // satu case boleh lebih dari satu job (utk job susulan)

        $arrayToJs = parent::validateForm($arr, $pkey);

        $customerKey = $arr['hidCustomerKey'];
        $serviceKey = $arr['hidServiceKey'];
        $contractKey = $arr['hidContractKey'];

        if (empty($contractKey))  
            $this->addErrorList($arrayToJs, false, $this->errorMsg['contract'][1]);
         
        if (empty($customerKey))  
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][1]);

        if (empty($serviceKey))  
            $this->addErrorList($arrayToJs, false, $this->errorMsg['service'][1]);
      

        return $arrayToJs;
    }



     function generateDefaultQueryForAutoComplete($returnField) {

        $sql = 'select
					' . $returnField['key'] . ',
					' . $returnField['value'] . ' as value, 
                    ' . $this->tableName . '.code,
                    ' . $this->tableName . '.trdate,
                    ' . $this->tableName . '.maximumweight,
                    ' . $this->tableName . '.servicekey, 
                    ' . $this->tableName . '.totalinvoiced,
                    ' . $this->tableName . '.customerkey,
                    ' . $this->tableCustomer . '.name as customername,
                    concat ('.$this->tableContract. '.code, " - ", '.$this->tableContract.'.name) as contractname,
                    ' . $this->tableService . '.name as servicename,
                    (' . $this->tableName . '.grandtotal - ' . $this->tableName . '.totalserviceinvoiced) as outstanding
				from 
					' . $this->tableName . '
                    left join ' . $this->tableService . ' on ' . $this->tableName . '.servicekey = ' . $this->tableService . '.pkey
                    left join ' . $this->tableContract . ' on ' . $this->tableName . '.contractkey = ' . $this->tableContract . '.pkey
                    left join ' . $this->tableCustomer . ' on ' . $this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey,
                    ' . $this->tableStatus . ' 
				where  		 
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey  
			';

        $sql .=  $this->getCompanyCriteria();
          
        return $sql;
    }
    
    function getDetailWithRelatedInformation($pkey, $criteria = '', $orderBy='') {

        $sql = 'select
	   			' . $this->tableNameDetail . '.*,
	   			' . $this->tableWorkOrder . '.workordercode as wocode,
	   			' . $this->tableName . '.extraprice as exceedweightpricearea,
	   			' . $this->tableWaste . '.code as wastecode,
	   			' . $this->tableDetailStatus . '.status as statusname
              from
			  	' . $this->tableNameDetail . ',
			  	' . $this->tableName . ',
			  	' . $this->tableWaste . ',
                ' . $this->tableDetailStatus . ',
			  	' . $this->tableWorkOrder . '
			  where
			  	' . $this->tableNameDetail . '.wokey = ' . $this->tableWorkOrder . '.pkey and 
                ' . $this->tableNameDetail . '.statuskey = ' . $this->tableDetailStatus . '.pkey and
                ' . $this->tableNameDetail . '.refkey = ' . $this->tableName . '.pkey and
                ' . $this->tableNameDetail . '.wastekey = ' . $this->tableWaste . '.pkey and
			  	' . $this->tableNameDetail . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;
  
        $sql .= (!empty($orderBy)) ? $orderBy : ' order by '.$this->tableNameDetail.'.trdate asc, '.$this->tableNameDetail.'.pkey asc';
  
//        $this->setLog($sql,true);
        return $this->oDbCon->doQuery($sql);
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

    function getUnInvoicedItemDetail($pkey)  {

        $arrCriteria = array();
        // array_push($arrCriteria, $obj->tableNameDetail . '.statuskey = 2');
         array_push($arrCriteria, $this->tableNameDetail . '.amount > 0');

        // cari jg hanya yg total invoicenya lebih kecil dr outstanding
         array_push($arrCriteria, $this->tableNameDetail . '.totalinvoiced < '.$this->tableNameDetail.'.amount ');

        $criteria = implode(' and ', $arrCriteria);
        $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';

//        $this->setLog($criteria,true);
        // $rsJO = $this->getDataRowById($pkey);
        // $rsJO = $this->searchDataRow( array($this->tableName . '.pkey', $this->tableName . '.invoicingtypekey'), ' and ' . $this->tableName . '.pkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ');
        $rsJO = $this->searchData('', '', true, '  and ' . $this->tableName . '.pkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ');
        $rsJO = $this->reindexDetailCollections($rsJO, 'pkey');
        $rs = $this->getDetailWithRelatedInformation($pkey, $criteria);
        for($i=0;$i<count($rs);$i++){
            $arrJO = $rsJO[$rs[$i]['refkey']];
            $chargedWeight = ($arrJO[0]['invoicingtypekey'] == 1) ? ceil($rs[$i]['chargedweight']) : $rs[$i]['chargedweight']; 
            $rs[$i]['chargedweight'] = $chargedWeight;
        }
        $rs = $this->reindexDetailCollections($rs, 'refkey');

        return $rs;
         
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

    function updateAmountInvoiced($pkey){
        // utk hitung total tagihan services / layanan yg sudh diinvoiced 
        // gk perlu udpate customer AR outstanding karena sudah diupdate di ketika terbentuk AR

        $disposalSalesInvoice = new DisposalSalesInvoice();

        $sql = '
                    select 
                        coalesce(sum(' . $disposalSalesInvoice->tableNameDetail . '.amount),0) as amount,
                        coalesce(sum(' . $disposalSalesInvoice->tableNameDetail . '.serviceprice),0) as serviceamount,
                        coalesce(sum(' . $disposalSalesInvoice->tableNameDetail . '.detailordergrandtotal),0) as detailamount
                    from
                        ' . $disposalSalesInvoice->tableNameDetail . ',
                        ' . $disposalSalesInvoice->tableName . '
                    where
                        ' . $disposalSalesInvoice->tableName . '.statuskey in (2,3) and 
                        ' . $disposalSalesInvoice->tableName . '.pkey =  ' . $disposalSalesInvoice->tableNameDetail . '.refkey and
                        ' . $disposalSalesInvoice->tableNameDetail . '.salesorderkey = ' . $this->oDbCon->paramString($pkey) . '
        ';
         
        $rsAmount = $this->oDbCon->doQuery($sql);
        
        
        $sql = 'update ' . $this->tableName. ' 
                set
                    totalinvoiced = '.$this->oDbCon->paramString($rsAmount[0]['amount']).',
                    totalserviceinvoiced = '.$this->oDbCon->paramString($rsAmount[0]['serviceamount']).',
                    totaldetailinvoiced = '.$this->oDbCon->paramString($rsAmount[0]['detailamount']).'
                where pkey =  ' . $this->oDbCon->paramString($pkey).'
        '; 
        
        $this->oDbCon->execute($sql);
        
        
        // update amount detail
        // loop per jo detail
        $rsDetail = $this->getDetailById($pkey);
        foreach($rsDetail as $row){

            // gk bisa pake amount atau beforetax karena kalo detailnya nanti ad tax, gk tau include atau exclude diawal
            // gk bisa pake qty saja, karena ad tagihan extra layanan jg
            
            /// harus 2kali biar gk null
            $sql = '  select  coalesce(('.$disposalSalesInvoice->tableNameItemDetail.'.totalexceedweightpricearea + '.$disposalSalesInvoice->tableNameItemDetail.'.exceedpricearea),0) as totalinvoiced 
                                        from   '.$disposalSalesInvoice->tableName.', '.$disposalSalesInvoice->tableNameItemDetail.'
                                        where 
                                            '.$disposalSalesInvoice->tableName.'.pkey =  '.$disposalSalesInvoice->tableNameItemDetail.'.refheaderkey and
                                            '.$disposalSalesInvoice->tableName.'.statuskey  in (2,3) and 
                                            '.$disposalSalesInvoice->tableNameItemDetail.'.refsodetailkey = ' . $this->oDbCon->paramString($row['pkey']);
            
            $rsSum = $this->oDbCon->doQuery($sql);
            
            $totalInvoiced = (!empty($rsSum)) ? $rsSum[0]['totalinvoiced'] : 0;
            
            $sql = 'update ' . $this->tableNameDetail . ' 
                    set   ' . $this->tableNameDetail . '.totalinvoiced =  '.$this->oDbCon->paramString($totalInvoiced).'  
                 where ' . $this->tableNameDetail . '.pkey = ' . $this->oDbCon->paramString($row['pkey']);

            $this->oDbCon->execute($sql);
        }
        
        $this->updateUninvoicedJO($pkey); 
     
    }
  
  
  
    function normalizeParameter($arrParam, $trim = false){

        $arrParam['grandtotal'] = $this->unFormatNumber($arrParam['sellingPrice']);

        $security = new Security();
        $disposalContract = new DisposalContract();
        $service = new Service();
        // $overwriteContractAllowed = $security->isAdminLogin($this->overwriteContractSecurityObject, 10);

        $pkey = $arrParam['hidId'];
        $contractKey = $arrParam['hidContractKey'];
        
        $rsContract = $disposalContract->getDataRowById($contractKey);
        $rsService = $service->getDataRowById($rsContract[0]['servicekey']);


        $arrParam['hidContractKey'] = $rsContract[0]['pkey'];
        $arrParam['hidServiceKey'] = $rsContract[0]['servicekey'];
        $arrParam['hidCityKey'] = $rsContract[0]['citykey'];
        $arrParam['hidAreaKey'] = $rsContract[0]['areakey'];
        $arrParam['hidCustomerKey'] = $rsContract[0]['customerkey'];
        $arrParam['duration'] = $rsContract[0]['duration'];
        $arrParam['maximumWeight'] = $rsContract[0]['maximumweight'];
        $arrParam['qtyService'] = $rsContract[0]['qtyservice'];
        $arrParam['sellingPrice'] = $rsContract[0]['sellingprice'];
        $arrParam['exceedSellingPriceArea'] = $rsContract[0]['exceedprice'];
        $arrParam['exceedWeightPriceArea'] = $rsContract[0]['extraprice'];
        $arrParam['hidWasteCategoryKey'] = $rsContract[0]['wastecategorykey'];
        $arrParam['chkIsPrePaid'] = $rsService[0]['isprepaid'];
     

        $arrParam = parent::normalizeParameter($arrParam, true);

        return $arrParam;
    }
 
//   function updateDetailStatus($JOKey) {
//         
//        $disposalWorkOrder = new DisposalWorkOrder();
//        $disposalSalesInvoice = new DisposalSalesInvoice();
//        $rsDetail = $this->getDetailByColumn('refkey', $JOKey);
// 
//        $woKey  = $rsDetail[0]['wokey'];
//        $rsWO  = $disposalWorkOrder->getDataRowById($woKey);
//
//        if ($rsWO[0]['statuskey'] <> 1 || $rsWO[0]['statuskey'] <> 4) {
//
//            try {
//
//                if (!$this->oDbCon->startTrans())
//                    throw new Exception($this->errorMsg[100]);
// 
//                $rs = $disposalWorkOrder->searchDataRow(
//                    array(
//                        $disposalWorkOrder->tableName . '.pkey',
//                        $disposalWorkOrder->tableName . '.statuskey'
//                    ),
//                    '   and ' . $disposalWorkOrder->tableName . '.jokey = ' . $this->oDbCon->paramString($JOKey) . '
//                                                            and ' . $disposalWorkOrder->tableName . '.statuskey in (' . TRANSACTION_STATUS['menunggu'] . ',' . TRANSACTION_STATUS['konfirmasi'] . ',' . TRANSACTION_STATUS['selesai'] . ')',
//                    'order by ' . $disposalWorkOrder->tableName . '.pkey desc'
//                );
//
//
//                $totalSPK = count($rs);
//                $statusSPK = array();
//
//                $rsStatus = $this->getAllStatus($this->tableDetailStatus);
//                for ($i = 0; $i < count($rsStatus); $i++) {
//                    $statusSPK[$rsStatus[$i]['pkey']] = 0;
//                }
//
//                for ($i = 0; $i < count($rs); $i++) {
//                    $statuskey = $rs[$i]['statuskey'];
//                    $statusSPK[$statuskey]++;
//                }
//
//                if ($statusSPK[2] <> 0)
//                    $statuskey = 1;
//                else
//                    $statuskey = 2;
//                    
//                $rsDisposalSalesInvoice = $this->getInvoiceInformation($JOKey);
//                for ($i = 0; $i < count($rsDisposalSalesInvoice); $i++) { 
//                    $rsDisposalSalesInvoiceItemDetail = $disposalSalesInvoice->getItemDetail($rsDisposalSalesInvoice[$i]['detailpkey']);
//                    if (!empty($rsDisposalSalesInvoiceItemDetail)) {
//                        $statuskey = 3 ;
//                    }
//                }
//        
//                $sql = 'update 
//                    ' . $this->tableNameDetail .
//                    ' set 
//                        statuskey = ' . $statuskey . '
//                    where refkey = ' . $this->oDbCon->paramString($JOKey) . ' and wokey = ' . $this->oDbCon->paramString($woKey);
//                $this->oDbCon->execute($sql);
//
//
//                $this->oDbCon->endTrans();
//            } catch (Exception $e) {
//                $this->oDbCon->rollback();
//            }
//        }
//    }

    function afterStatusChanged($rsHeader){ 
        // retrieve latest status
        $service = new Service();
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);

        $id = $rsHeader[0]['pkey']; 
        
        $rsService = $service->getDataRowById($rsHeader[0]['servicekey']);   
 
        // kalo konfirmasi update outstanding
        if ($rsService[0]['isprepaid'] == 1 && $rsHeader[0]['statuskey'] == 2)
             $this->updateUninvoicedJO($id); 
        
        if ($rsService[0]['isprepaid'] <> 1 && $rsHeader[0]['statuskey'] == 2) {
            $this->changeStatus($id, 4,'',false,true); 
        }
         
    }
 
    function insertJODetail($woKey, $joKey) { 
        $disposalWorkOrder = new DisposalWorkOrder();
        $rsJO = $this->getDataRowById($joKey);
        $quotaWeight = $rsJO[0]['maximumweight'];

        $rsWO = $disposalWorkOrder->getDataRowById($woKey);
        $WODate = $rsWO[0]['trdate'];
        $rsWODetail = $disposalWorkOrder->getDetailWithRelatedInformation($woKey);

        for ($i = 0; $i < count($rsWODetail); $i++) {
            $WODetailKey = $rsWODetail[$i]['pkey'];
            
            try {

                if (!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);
                
                $woWeight = $rsWODetail[$i]['customerweight'];
                if ($woWeight > 0) {
                    $sql = 'insert into ' . $this->tableNameDetail . ' (`refkey`,`wokey`, `trdate`, `refdetailwokey`, `disposalweight`, `quotaweight`, `manifestcode`,`wastekey`)
                        values  (' . $this->oDbCon->paramString($joKey) . ', ' . $this->oDbCon->paramString($woKey) . ', 
                        ' . $this->oDbCon->paramString($WODate) . ', ' . $this->oDbCon->paramString($rsWODetail[$i]['pkey']) . ', ' . $this->oDbCon->paramString($woWeight) . ', ' . $this->oDbCon->paramString($quotaWeight) . ', ' . $this->oDbCon->paramString($rsWODetail[$i]['manifestcode']) . ' , ' . $this->oDbCon->paramString($rsWODetail[$i]['wastekey']) . ')';
                    
                    $this->oDbCon->execute($sql);
                }

                $this->oDbCon->endTrans();
            } catch (Exception $e) {
                $this->oDbCon->rollback();
            }
        }
        
        $this->updateDetailJO($joKey);
    }


    function updateDetailJO($joKey) { 
  
        $disposalWorkOrder = new DisposalWorkOrder();
        $customer = new Customer();
        $waste = new Waste();
        $rsJO = $this->getDataRowById($joKey);
        $rsCustomer = $customer->getDataRowById($rsJO[0]['customerkey']);
        
        $rsJODetail = $this->getDetailWithRelatedInformation($joKey);
        $arrWOKey = array_column($rsJODetail, 'wokey');
        $rsJODetail = $this->reindexDetailCollections($rsJODetail,'wokey');

        $rsWasteCategory = $waste->getWasteCategory($rsJO[0]['wastecategorykey']);
        $wasteCategory = $rsWasteCategory[0]['ismedis'];

        $rsWODetail = $disposalWorkOrder->getDetailWithRelatedInformation($arrWOKey);
        $rsWODetail = array_column($rsWODetail, null, 'pkey'); 

        $rsWasteDetail = $this->getWasteDetail($joKey);
        $rsWasteDetail = array_column($rsWasteDetail, null, 'wastekey');

        $quotaLayanan = 0;
        $qtyService = 0;
        
        foreach($rsJODetail as $key=>$itemRow){  
            //mencari spk yg non exceed dan sudah diinvoiced
            if ($itemRow[0]['totalinvoiced'] > 0 && $itemRow[0]['exceedprice'] == 0) {
                $quotaLayanan++ ;
            }
        }

        //looping 
        foreach($rsJODetail as $key=>$itemRow){ 
           
            $workOrderKey = $key;

            $exceedPrice = 0;
            
            // kalo layanan melebihi quota
            if ($quotaLayanan >= $rsJO[0]['qtyservice']) $exceedPrice = $rsJO[0]['exceedprice']; 
          if ($itemRow[0]['totalinvoiced'] > 0) {
                continue;
            }
            

            $totalItemRow =  count($itemRow);
            
            for ($i=0; $i<$totalItemRow ; $i++) { 

                // hanya yg SPK sudah selesai yg diakui
                // hanya yg pertama yg ad nilai exceed nya
                if ($i > 0)  $exceedPrice = 0;
               
                $pkey = $itemRow[$i]['pkey'];
                $WOWeight =  $itemRow[$i]['disposalweight'];
                $WODetailKey =  $itemRow[$i]['refdetailwokey'];
                $wasteKey = $rsWODetail[$WODetailKey]['wastekey'];
                $price = $this->unFormatNumber($rsWasteDetail[$wasteKey]['weightprice']);

                // layanan medis atau umum
                if ($rsWasteDetail[$wasteKey]['maxweight'] <> 0) {
                    
                    $quotaWeight = ($quotaLayanan >= $rsJO[0]['qtyservice']) ? 0 : $rsWasteDetail[$wasteKey]['maxweight'];
                    
                    $weight = $WOWeight - $quotaWeight ;
                    if($weight<0) $weight = 0;
                } else {
                    $quotaWeight = $rsWasteDetail[$wasteKey]['minweight'];

                    $weight = ($WOWeight <= $quotaWeight) ? $quotaWeight : $WOWeight;
                }

                $amount = ($weight * $price) + $this->unFormatNumber($exceedPrice); 
                try {

                    if (!$this->oDbCon->startTrans())
                        throw new Exception($this->errorMsg[100]);
      
                    $sql = 'update 
                            ' . $this->tableNameDetail .
                        ' set 
                                priceinunit = ' . $this->oDbCon->paramString($price) . ', 
                                exceedprice = ' . $this->oDbCon->paramString($exceedPrice) . ', 
                                quotaweight = ' . $this->oDbCon->paramString($quotaWeight) . ', 
                                disposalweight = ' . $this->oDbCon->paramString($WOWeight). ',
                                chargedweight = ' . $this->oDbCon->paramString($weight). ',
                                amount = ' . $this->oDbCon->paramString($amount ). '
                            where pkey = ' . $this->oDbCon->paramString($pkey);
                     
                    $this->oDbCon->execute($sql);
    
                    $this->oDbCon->endTrans();
                } catch (Exception $e) {
                    $this->oDbCon->rollback();
                }
            }

            
            
            
            $quotaLayanan++ ;
        }
        
        $this->updateTotalDetail($joKey, $rsCustomer[0]['invoicingtypekey']);
        
    }
 
    function updateTotalDetail($pkey, $isCeil = 1) { 
        $isCeil = ($isCeil == 1) ? 'ceil' : '';  
        
       $sql = '  select coalesce(sum(' . $this->tableNameDetail . '.priceinunit * '.$isCeil.' (' . $this->tableNameDetail . '.chargedweight)),0) as amount
                     from
                        ' . $this->tableNameDetail . ',
                        ' . $this->tableName . '
                    where
                        ' . $this->tableName . '.pkey =  ' . $this->tableNameDetail . '.refkey and
                        ' . $this->tableName . '.pkey = ' . $this->oDbCon->paramString($pkey) ;
        
        $rs = $this->oDbCon->doQuery($sql);
         
        $sql = 'update ' . $this->tableName . ' set totaldetail = '.$this->oDbCon->paramString($rs[0]['amount']).' where pkey = ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);

        $this->updateUninvoicedJO($pkey); 
    }
    
    
    function getQtyJO($contractkey) {

        $sql = 'select 
                    ' . $this->tableName . '.pkey,
                        coalesce(count(pkey),0) as qtyjo
                    from  
                        ' . $this->tableName . '
                    where 
                        ' . $this->tableName . '.contractkey = ' .  $this->oDbCon->paramString($contractkey) . ' and
                        ' . $this->tableName . '.statuskey in (2,3,4,5) 
                    ';

        $rsTotal = $this->oDbCon->doQuery($sql);

        return $rsTotal[0]['qtyjo'];
    }
    
        
    function getUninvoicedJobOrder(){
        // query seperlunya saja, utk widget
        $sql  = 'select 
                    '.$this->tableName.'.code, 
                    '.$this->tableName.'.totaluninvoiced, 
                    '.$this->tableCustomer.'.name as customername
                 from 
                    '.$this->tableName.', '.$this->tableCustomer.'
                 where
                    '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                    '.$this->tableName.'.totaluninvoiced > 0 and
                    '.$this->tableName.'.statuskey in (2,3,4,5) 
                ';     
        
        return  $this->oDbCon->doQuery($sql);
    }
    
    function getWasteDetail($pkey, $criteria = '', $orderby = '')
    {

        $sql = 'select
                ' . $this->tableDetailWaste . '.*, 
                concat ('.$this->tableWaste. '.code, " - ", '.$this->tableWaste.'.name) as wastecodename
              from
                ' . $this->tableDetailWaste . ',
                ' . $this->tableWaste . '
              where
                ' . $this->tableDetailWaste . '.wastekey = ' . $this->tableWaste . '.pkey and
		        ' . $this->tableDetailWaste . '.refkey in (' . $this->oDbCon->paramString($pkey, ',') . ') ';

        $sql .= $criteria;

        $sql .= ' ' . $orderby;

        return $this->oDbCon->doQuery($sql);
    }
    
    function getPermissionJO($customerKey){

        $return = true;
        $sql  = 'select 
                    '.$this->tableName.'.code, 
                    '.$this->tableName.'.totaluninvoiced, 
                    '.$this->tableCustomer.'.name as customername
                 from 
                    '.$this->tableName.', 
                    '.$this->tableCustomer.'
                 where
                    '.$this->tableName . '.customerkey in (' . $this->oDbCon->paramString($customerKey, ',') . ') and  
                    '.$this->tableName . '.customerkey = ' . $this->tableCustomer . '.pkey and
                    '.$this->tableName.'.totaluninvoiced > 0 and
                    '.$this->tableName.'.statuskey in (2,3,4,5) 
                ';     
        
        $rsJO = $this->oDbCon->doQuery($sql);

        $sql = 'select    
                ' . $this->tableAR.'.statuskey as arstatuskey,
                ' . $this->tableARStatus.'.status as arstatusname
            from 
                ' . $this->tableAR . ',
                ' . $this->tableARStatus . '
            where  
                ' . $this->tableAR . '.customerkey in (' . $this->oDbCon->paramString($customerKey, ',') . ') and  
                ' . $this->tableAR . '.statuskey = ' . $this->tableARStatus . '.pkey and
                ' . $this->tableAR . '.statuskey in (1,2) ';
                $rsAR = $this->oDbCon->doQuery($sql);

        if (!empty($rsJO) || !empty($rsAR)) {
            $return = false;
        }

        return $return;
    }
    
}
    
?>
