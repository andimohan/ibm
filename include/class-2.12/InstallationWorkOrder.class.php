<?php
class InstallationWorkOrder extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'installation_work_order_header'; 
		$this->tableNameDetail = 'installation_work_order_detail'; 
		$this->tableNameDetailTechnician = 'installation_work_order_detail_technician';
	    $this->tableSalesOrder = 'sales_order_subscription_header'; 
        $this->tableItemUnit = 'item_unit';
        $this->tableCustomer = 'customer';
        $this->tableMedia = 'media';
        $this->tableEmployee = 'employee';
        $this->tableItem = 'item';
        $this->tableLocation = 'location';
        $this->tableWarehouse = 'warehouse';
	    $this->tableJob = 'job_details';
	    $this->tableStages = 'stages_process';
        
		$this->tableStatus = 'transaction_status'; 
		$this->securityObject = 'InstallationWorkOrder'; 
        $this->isTransaction = true;
        $this->allowedStatusForEdit = array(1,2);
		
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');  
        $this->arrDataDetail['itemkey'] = array('hidItemKey'); 
        $this->arrDataDetail['qty'] = array('qty','number'); 
        $this->arrDataDetail['qtyinbaseunit'] = array('qtyInBaseUnit','number'); 
        $this->arrDataDetail['unitkey'] = array('selUnit');
        $this->arrDataDetail['unitconvmultiplier'] = array('unitConvMultiplier','number');
	    $this->arrDataDetail['usedqty'] = array('usedQty','number');

        $this->arrTechnician = array();  
        $this->arrTechnician['pkey'] = array('hidDetailTechnicianKey');
        $this->arrTechnician['refkey'] = array('pkey','ref');  
        $this->arrTechnician['techniciankey'] = array('hidTechnicianKey'); 
       
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
        array_push($arrDetails, array('dataset' => $this->arrTechnician, 'tableName' => $this->tableNameDetailTechnician));
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code'); 
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['salesorderkey'] = array('hidSalesOrderKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['employeekey'] = array('hidEmployeeKey');
        $this->arrData['starttime'] = array('startTime','date');
        $this->arrData['endtime'] = array('endTime','date');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');  
        $this->arrData['stagekey'] = array('selStageKey');  
        $this->arrData['isdone'] = array('chkIsDone');  
        $this->arrData['isoutsource'] = array('chkIsOutsource');  
        $this->arrData['supplierkey'] = array('hidSupplierKey');  
         
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 90,  'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'soCode','title' => 'soCode','dbfield' => 'socode','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'media','title' => 'media','dbfield' => 'medianame','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jobDetails','title' => 'jobDetails','dbfield' => 'jobname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outsource','title' => 'outsource','dbfield' => 'outsourceicon','default'=>true, 'width' => 90,'align' => 'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));    
		
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/installationWorkOrder'));
        $this->refAutoCode = array( 'param' => 'hidSalesOrderKey', 'refField' => 'pkey');
	   
	   $this->includeClassDependencies(array(
			'SalesOrderSubscription.class.php',  
			'Warehouse.class.php',  
			'WarehouseTransfer.class.php', 
			'Customer.class.php', 
			'Location.class.php', 
			'Employee.class.php', 
		    'StagesProcess.class.php', 
		    'Supplier.class.php', 
		    'ItemUnit.class.php', 
			'JobDetails.class.php', 
			'Media.class.php', 
		    'Item.class.php', 
		    'ItemOut.class.php', 
		    'ItemIn.class.php', 
		    'InstallationBAST.class.php'
		)); 

        $this->overwriteConfig();
	}
	
	 function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,
                    '.$this->tableWarehouse.'.name as warehousename, 
                    '.$this->tableEmployee.'.name as employeename, 
                    '.$this->tableMedia.'.name as medianame, 
                    '.$this->tableLocation.'.name as locationname, 
                    '.$this->tableCustomer.'.name as customername, 
                    '.$this->tableCustomer.'.phone, 
                    '.$this->tableCustomer.'.address, 
                    '.$this->tableSalesOrder.'.code as socode, 
                    '.$this->tableJob.'.name as jobname, 
                    '.$this->tableStages.'.name as stagename, 
					'.$this->tableStatus.'.status as statusname,
                    IF(isoutsource=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as outsourceicon
				from
					'.$this->tableName.'
                    left join '. $this->tableSalesOrder.' on ' . $this->tableName .'.salesorderkey = ' . $this->tableSalesOrder .'.pkey 
                    left join '. $this->tableStages.' on ' . $this->tableName .'.stagekey = ' . $this->tableStages .'.pkey 
                    left join '. $this->tableJob.' on ' . $this->tableSalesOrder .'.jobdetailskey = ' . $this->tableJob .'.pkey ,
					'.$this->tableCustomer.'
                    left join '. $this->tableEmployee.' on ' . $this->tableCustomer .'.saleskey = ' . $this->tableEmployee .'.pkey
                    left join '. $this->tableMedia.' on ' . $this->tableCustomer .'.mediakey = ' . $this->tableMedia.'.pkey
                    left join '. $this->tableLocation.' on ' . $this->tableCustomer .'.locationkey = ' . $this->tableLocation.'.pkey,
					'.$this->tableWarehouse.',
                    '.$this->tableStatus.' 
                where
					'.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                    '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ;
          
         return $sql;
    }
      
    function validateForm($arr,$pkey = ''){ 
		$arrayToJs = parent::validateForm($arr,$pkey); 
        $salesOrderSubscription = new SalesOrderSubscription(); 
        $item = new Item();
		$sokey = $arr['hidSalesOrderKey']; 
		$isoutsource = $arr['chkIsOutsource']; 
		$arrItemkey = $arr['hidItemKey']; 
		$arrQty = $arr['qty'];
		$arrQtyBaseUnit = $this->unformatNumber($arr['qtyInBaseUnit']);
        $arrTechniciankey = $arr['hidTechnicianKey'];
        $arrUsedQty = $arr['usedQty'];
        $supplierkey = $arr['hidSupplierKey'];
            
        if(empty($sokey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderSubscription'][1]);
        else{
            $rsSO = $salesOrderSubscription->getDataRowById($sokey);
            if($rsSO[0]['statuskey']<>2)
                $this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderSubscription'][2]);
        }
        
        if($isoutsource == 0){
            if(empty($arrTechniciankey))
                $this->addErrorList($arrayToJs,false, $this->errorMsg['technician'][1]);
        } else{
            if(empty($supplierkey))
                $this->addErrorList($arrayToJs,false, $this->errorMsg['supplier'][1]); 
        }   
        
        if(!empty($arrItemkey)){
             
            for($i=0;$i<count($arrItemkey);$i++) {
                
                $rsItem = $item->getDataRowById($arrItemkey[$i]);
				if(!empty($rsItem)){
					if ($arrQty[$i]>0 && ($arrQty[$i]<$arrUsedQty[$i]) )
                    	$this->addErrorList($arrayToJs,false,'<strong>'.$rsItem[0]['name']. ' </strong> '.$this->errorMsg['installationworkorder'][3]); 	
					
					if($arrQty[$i]<=0)
						$this->addErrorList($arrayToJs,false,'<strong>'.$rsItem[0]['name']. ' </strong> '.$this->errorMsg[510]); 
				}
            }
        }
		
		return $arrayToJs;
	 }
    
    function reCountSubtotal($arrParam){
				
        $item = new Item(); 
        
        $arrItemkey = $arrParam['hidItemKey'];
            
        $arrQty = $arrParam['qty']; 
        $arrTransUnitKey = $arrParam['selUnit'];

        $arrItemDetail = array();

        for ($i=0;$i<count($arrItemkey);$i++){
            
            if (empty($arrItemkey[$i]))  
                continue;
            
            $rsItem = $item->getDataRowById($arrItemkey[$i]);
             
            $itemkey = $arrItemkey[$i];
            $transactionUnitKey = $arrTransUnitKey[$i];
            
            $baseunitkey = $rsItem[0]['baseunitkey'];
            $qty =  $this->unFormatNumber($arrQty[$i]);
            $conversionMultiplier = $item->getConvMultiplier($itemkey,$transactionUnitKey,$baseunitkey);
            $qtyinbaseunit = $qty * $conversionMultiplier;
            $arrItemDetail[$i]['baseUnitKey'] = $baseunitkey;
            $arrItemDetail[$i]['unitConvMultiplier'] = $conversionMultiplier;
            $arrItemDetail[$i]['qtyInBaseUnit'] = $qtyinbaseunit ;

               
        }   
        $reCountResult = array();
        $reCountResult['detail'] = $arrItemDetail;
        
        return $reCountResult;
}
     
    function backConfirmTrans($rsHeader){

        $id = $rsHeader[0]['pkey'];
        $warehousekey = $rsHeader[0]['warehousekey'];
     
	   $this->cancelWarehouseTransfer($id,$warehousekey);
        $this->cancelItemOut($rsHeader);
        
    }
    function normalizeParameter($arrParam, $trim=false){
        $arrParam['isAuto'] = (isset($arrParam['isAuto'])) ? $arrParam['isAuto'] : 0;
        $arrParam = parent::normalizeParameter($arrParam,true); 
        $arrItemkey = $arrParam['hidItemKey']; 
        $reCountResult = $this->reCountSubtotal($arrParam);
        $arrParam['detail'] = $reCountResult['detail'];
         for($i=0;$i<count($arrItemkey);$i++){ 
             if(empty($arrItemkey[$i]))
                 continue;
             
            $qtyinbaseunit = $arrParam['detail'][$i]['qtyInBaseUnit'];
            $arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit;
            $arrParam['unitConvMultiplier'][$i] = $arrParam['detail'][$i]['unitConvMultiplier'];

        }
        
        if ($arrParam['chkIsOutsource'] == 1){
           // $arrParam['hidTechnicianKey'] = array();
        }else{
            $arrParam['hidSupplierKey'] = 0; 
        }      
        
        return $arrParam;
    }
    
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $itemOut = new ItemOut();   
        $itemIn = new ItemIn(); 
        $warehouseTransfer = new WarehouseTransfer(); 
		$installationBAST = new InstallationBAST();
		$salesOrderSubscription = new SalesOrderSubscription();
		
		$rsBast = $installationBAST->searchData('','',true,' and '.$installationBAST->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['salesorderkey']).' and ('.$installationBAST->tableName.'.statuskey  in (2,3))');  
        if(!empty($rsBast)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br> <strong>' .$rsBast[0]['code'].'</strong>, '.$this->errorMsg[225]);
		
		$rsSO = $salesOrderSubscription->searchData('','',true,' and '.$salesOrderSubscription->tableName.'.pkey = '.$this->oDbCon->paramString($rsHeader[0]['salesorderkey']).' and ('.$salesOrderSubscription->tableName.'.statuskey  in (3,4,5))');  
        if(!empty($rsSO)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br> <strong>' .$rsSO[0]['code'].'</strong>, '.$this->errorMsg[204]);
		
        $rsOutKey = $itemOut->getTableKeyAndObj($this->tableName,array('key'));    
        $arrOutKey = $rsOutKey['key'];
        
        $rsTransKey = $warehouseTransfer->getTableKeyAndObj($this->tableName,array('key'));    
        $arrTransKey = $rsTransKey['key'];
     
        $rsItemOut = $itemOut-> searchDataRow( array(  $itemOut->tableName.'.pkey', $itemOut->tableName.'.code'  ) , 
                                ' and  '.$itemOut->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$arrOutKey.' and '.$itemOut->tableName.'.statuskey in(2,3)'  
                       );
        if(!empty($rsItemOut))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsItemOut[0]['code'].'</strong>. ' .$this->errorMsg[225]);
        
        
         $rsTransWarehouse = $warehouseTransfer->searchDataRow( array(  $warehouseTransfer->tableName.'.pkey', $warehouseTransfer->tableName.'.code'  ) , 
                                ' and  '.$warehouseTransfer->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$arrTransKey.' and '.$warehouseTransfer->tableName.'.statuskey in(2,3)'  
                       );
        
        if(!empty($rsTransWarehouse))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsTransWarehouse[0]['code'].'</strong>. ' .$this->errorMsg[225]);
        
    
    }
    
	function cancelTrans($rsHeader,$copy){
		$id = $rsHeader[0]['pkey'];
        $this->cancelWarehouseTransfer($id);
        $this->cancelItemOut($rsHeader); 
		 
		if ($copy)
			$this->copyDataOnCancel($id);	  
		   
    }
    
    function closeTrans($rsHeader){ 
        $id = $rsHeader[0]['pkey'];
        
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        $note = $rsHeader[0]['code'];
            if(!empty($rsDetail)){
             $this->rollbackTransferWarehouse($rsHeader);
             $this->addItemOut($rsHeader);
        }        
        $sql = ' update '.$this->tableName.' set endtime = now() where pkey = ' . $this->oDbCon->paramString($id) .' ';
        $this->oDbCon->execute($sql);
    }
    
    function validateConfirm($rsHeader){
        $id = $rsHeader[0]['pkey'];
		$salesOrderSubscription = new SalesOrderSubscription(); 
		
        $rsTechnician = $this->getDetailTechnicianWithRelatedInformation($id);
        if(empty($rsTechnician))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['technician'][1]);
        
        $rsSO = $salesOrderSubscription->searchData($salesOrderSubscription->tableName.'.pkey',$rsHeader[0]['salesorderkey'],true,' and '.$salesOrderSubscription->tableName.'.statuskey in(2) ');
		if(empty($rsSO))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['salesOrderSubscription'][2]);
    }
    
    function confirmTrans($rsHeader){ 
        $id = $rsHeader[0]['pkey'];  
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        if(!empty($rsDetail))
            $this->addTransferWarehouse($rsHeader);
    }
    
 
      function validateBackConfirm($rsHeader){ 
        $warehouseTransfer = new WarehouseTransfer();   
        $rsTransKey = $warehouseTransfer->getTableKeyAndObj($this->tableName,array('key'));    
        $arrTransKey = $rsTransKey['key'];
        $rsTransWarehouse = $warehouseTransfer-> searchDataRow( array(  $warehouseTransfer->tableName.'.pkey', $warehouseTransfer->tableName.'.code'  ) , 
                                ' and  '.$warehouseTransfer->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and '.$warehouseTransfer->tableName.'.towarehousekey = '.$this->oDbCon->paramString($rsHeader[0]['warehousekey']).' and reftabletype = '.$arrTransKey.' and '.$warehouseTransfer->tableName.'.statuskey in(2,3)'  
                       );
        $this->setLog($rsTransWarehouse,true);
        
        if(!empty($rsTransWarehouse))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsTransWarehouse[0]['code'].'</strong>. ' .$this->errorMsg[225]);
               
        
                $itemOut = new ItemOut();

        $rsOutKey = $itemOut->getTableKeyAndObj($this->tableName,array('key'));    
        $arrOutKey = $rsOutKey['key'];
        
        $rsItemOut = $itemOut-> searchDataRow( array(  $itemOut->tableName.'.pkey', $itemOut->tableName.'.code'  ) , 
                                ' and  '.$itemOut->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$arrOutKey.' and '.$itemOut->tableName.'.statuskey in(2,3)'  
                       );
        if(!empty($rsItemOut))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsItemOut[0]['code'].'</strong>. ' .$this->errorMsg[225]);
        
    }
    
    
    function addNextWO($rsHeader){
        $stagesProcess = new StagesProcess();
        $rsHeaderStage = $stagesProcess->getDataRowById($rsHeader[0]['stagekey']);
        $rsStage = $stagesProcess->searchData ('','',true,' and '.$stagesProcess->tableName.'.orderlist > '.$this->oDbCon->paramString($rsHeaderStage[0]['orderlist']).'   and ('.$stagesProcess->tableName.'.statuskey = 1 )',' order by '.$stagesProcess->tableName.'.orderlist asc limit 1');

        if(empty($rsStage))
            return;
        
        
        $arrParam = array();	

        $arrParam['code'] = 'xxxxxx';
        $arrParam['hidSalesOrderKey'] = $rsHeader[0]['salesorderkey']; 
        $arrParam['hidWOKey'] = $rsHeader[0]['pkey'];
        $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
        $arrParam['trDesc'] = $rsHeader[0]['trdesc'];
        $arrParam['trDate'] =  date('d / m / Y');  
        $arrParam['startTime'] =  date('d / m / Y 00:00');  
        $arrParam['endTime'] =  date('d / m / Y 00:00');  
        $arrParam['createdBy'] = 0;
        $arrParam['isAuto'] = 1;
        $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
        $arrParam['selStageKey'] = $rsStage[0]['pkey'];

        $arrayToJs = $this->addData($arrParam);
        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);
    }
    
    function addItemOut($rsHeader){
        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
        $rsTechnician = $this->getDetailTechnicianWithRelatedInformation($id);
        $arrEmployee = array();
        $arrEmployee = array_column($rsTechnician,'technicianname');
        $technicianKey = $rsTechnician[0]['techniciankey'];
        $itemOut = new ItemOut();
        if(!empty($rsDetail)){
            $arrParam = array();
            
            for($i=0;$i<count($rsDetail);$i++){ 
                $arrParam['hidDetailKey'][$i] = 0;
                $arrParam['hidItemKey'][$i] = $rsDetail[$i]['itemkey'];
                $arrParam['qty'][$i] =  $rsDetail[$i]['usedqty'];
                $arrParam['selUnit'][$i] =  $rsDetail[$i]['unitkey'];
                
            }
            $arrParam['code'] = 'xxxxxx';
            $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
            $arrParam['refCode'] = $rsHeader[0]['code'];
            $arrParam['refkey'] = $rsHeader[0]['pkey'];
            $arrParam['chkIsFullDelivered'] = 1;
            $arrParam['chkIsInternal'] = 1;
            $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
            $arrParam['hidEmployeeKey'] = $technicianKey;
            $tablekey =  $this->getTableKeyAndObj($this->tableName, array('key'));
            $tablekey = $tablekey['key'];
            $arrParam['reftabletype'] = $tablekey;
            $arrParam['islinked'] = 1;
            $arrParam['trDesc'] = implode(', ',$arrEmployee);
            
            $arrayToJs = $itemOut->addData($arrParam);

            if (!$arrayToJs[0]['valid'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
            }
        
        
    }
    
    
    function rollbackTransferWarehouse($rsHeader){
        $id = $rsHeader[0]['pkey'];
        
    
        
        $rsDetail = $this->getDetailById($id);
        $warehouseTransfer = new WarehouseTransfer();
        $warehouse = new Warehouse();
        $rsTechnician = $this->getDetailTechnicianWithRelatedInformation($id);
        $arrEmployee = array();
        $arrEmployee = array_column($rsTechnician,'technicianname');
		$trNote = implode(', ',$arrEmployee);
        
        $rsTransferKey = $warehouseTransfer->getTableKeyAndObj($this->tableName,array('key'));    
        $arrTransferKey = $rsTransferKey['key'];
        
        
        $rsWareHouseTrans = $warehouseTransfer->searchDataRow(array($warehouseTransfer->tableName.'.pkey',$warehouseTransfer->tableName.'.fromwarehousekey',$warehouseTransfer->tableName.'.towarehousekey'),
                                                             
                                                                ' and  '.$warehouseTransfer->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$arrTransferKey.' and '.$warehouseTransfer->tableName.'.statuskey in (1,2,3)'  
                                                             );

        
          if(!empty($rsDetail)){
            $arrParam = array();
            
            for($i=0;$i<count($rsDetail);$i++){ 
                $arrParam['hidDetailKey'][$i] = 0;
                $arrParam['hidItemKey'][$i] = $rsDetail[$i]['itemkey'];
                $arrParam['qty'][$i] =  $rsDetail[$i]['qty'];
                $arrParam['selUnit'][$i] =  $rsDetail[$i]['unitkey'];

            }

            $arrParam['code'] = 'xxxxxx';
            $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
            $arrParam['selWarehouseFromKey'] = $rsWareHouseTrans[0]['towarehousekey'];
            $arrParam['selWarehouseToKey'] = $rsWareHouseTrans[0]['fromwarehousekey'];
 //           $arrParam['refCode'] = $rsHeader[0]['code'];
            $arrParam['refkey'] = $rsHeader[0]['pkey'];
            $tablekey =  $this->getTableKeyAndObj($this->tableName, array('key'));
            $tablekey = $tablekey['key'];
            $arrParam['reftabletype'] = $tablekey;
            $arrParam['islinked'] = 1;
            $arrParam['trDesc'] = $trNote;
            
            $arrayToJs = $warehouseTransfer->addData($arrParam);

            if (!$arrayToJs[0]['valid'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
            }
    }
    
  function addTransferWarehouse($rsHeader){
        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
        $warehouseTransfer = new WarehouseTransfer();
        $warehouse = new Warehouse();
        $rsTechnician = $this->getDetailTechnicianWithRelatedInformation($id);
        $arrEmployee = array();
        $arrEmployee = array_column($rsTechnician,'technicianname');
		$trNote = implode(', ',$arrEmployee);
        //search warehouse sementara yang dibuat
        $rsWarehouse = $warehouse->searchDataRow(array($warehouse->tableName.'.pkey'),
                                                 ' and '.$warehouse->tableName.'.statuskey = 1
                                                    and '.$warehouse->tableName.'.systemVariable = 0'
                                                );
        

          if(!empty($rsDetail)){
            $arrParam = array();
            
            for($i=0;$i<count($rsDetail);$i++){ 
                $arrParam['hidDetailKey'][$i] = 0;
                $arrParam['hidItemKey'][$i] = $rsDetail[$i]['itemkey'];
                $arrParam['qty'][$i] =  $rsDetail[$i]['qty'];
                $arrParam['selUnit'][$i] =  $rsDetail[$i]['unitkey'];

            }

            $arrParam['code'] = 'xxxxxx';
            $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
            $arrParam['selWarehouseFromKey'] = $rsHeader[0]['warehousekey'];
            $arrParam['selWarehouseToKey'] = $rsWarehouse[0]['pkey'];
//            $arrParam['refCode'] = $rsHeader[0]['code'];
            $arrParam['refkey'] = $rsHeader[0]['pkey'];
            $tablekey =  $this->getTableKeyAndObj($this->tableName, array('key'));
            $tablekey = $tablekey['key'];
            $arrParam['reftabletype'] = $tablekey;
            $arrParam['islinked'] = 1;
            $arrParam['trDesc'] = $trNote;
            
            $arrayToJs = $warehouseTransfer->addData($arrParam);
            $this->setLog($arrayToJs,true);
            if (!$arrayToJs[0]['valid'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
            }
            

    }
  function cancelItemOut($rsHeader){
        $itemOut = new ItemOut();   
        
        $rsOutKey = $itemOut->getTableKeyAndObj($this->tableName,array('key'));    
        $arrOutKey = $rsOutKey['key'];

        $rsItemOut = $itemOut-> searchDataRow( array(  $itemOut->tableName.'.pkey', $itemOut->tableName.'.code'  ) , 
                                ' and  '.$itemOut->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$arrOutKey.' and '.$itemOut->tableName.'.statuskey = 1'  
                       );
        
        
        
        $totalItem = count($rsItemOut);
        for($i=0;$i<$totalItem;$i++) { 
            $arrayToJs = $itemOut->changeStatus($rsItemOut[$i]['pkey'],4,'',false, true);  
            if(!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']); 
        }
    }
    
    
    function cancelWarehouseTransfer($pkey,$warehouseTo=''){

       $warehouseTransfer = new WarehouseTransfer();  
        
        
        $rsTransferKey = $warehouseTransfer->getTableKeyAndObj($this->tableName,array('key'));    
        $arrTransferKey = $rsTransferKey['key'];
        
        $criteria = '';
        if(!empty($warehouseTo))
            $criteria = ' and '.$warehouseTransfer->tableName.'.towarehousekey = '.$this->oDbCon->paramString($warehouseTo);
            
        $rsTransfer = $warehouseTransfer->searchDataRow( array( $warehouseTransfer->tableName.'.pkey', $warehouseTransfer->tableName.'.code'  ) , 
                                ' and  '.$warehouseTransfer->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).'  and reftabletype = '.$arrTransferKey.' and '.$warehouseTransfer->tableName.'.statuskey = 1'.$criteria  
                       );
        
        
        $totalItem = count($rsTransfer);
        for($i=0;$i<$totalItem;$i++) { 
            $warehouseTransfer->changeStatus($rsTransfer[$i]['pkey'],4,'',false, true);  
        }
    }
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
          $sql = 'select
                '.$this->tableNameDetail.'.*,
                '.$this->tableItemUnit.'.name as unitname,
                '.$this->tableItem.'.name as itemname,
                '.$this->tableItem.'.code as itemcode
            from
                '.$this->tableNameDetail.',
                '.$this->tableItemUnit.',
                '.$this->tableItem.'
            where  
                '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
                '.$this->tableNameDetail .'.unitkey = '.$this->tableItemUnit.'.pkey and
                '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

            $sql .= $criteria;

            return $this->oDbCon->doQuery($sql);
    }
    
     function getDetailTechnicianWithRelatedInformation($pkey,$criteria=''){
        
          $sql = 'select
                '.$this->tableNameDetailTechnician.'.*,
                '.$this->tableEmployee.'.name as technicianname
            from
                '.$this->tableNameDetailTechnician.',
                '.$this->tableEmployee.'
            where  
                '.$this->tableNameDetailTechnician .'.techniciankey = '.$this->tableEmployee.'.pkey and
                '. $this->tableNameDetailTechnician.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

            $sql .= $criteria;
            return $this->oDbCon->doQuery($sql);
    }
}
?>
