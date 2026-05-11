<?php  
class TicketSupportWorkOrder extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
        $this->tableName = 'ticket_support_work_order';
        $this->tableTicket = 'ticket_support_header';
        $this->tableEmployee = 'employee';
        $this->tableCustomer = 'customer';
        $this->tableStatus = 'transaction_status'; 
        $this->tableNameDetail = 'ticket_support_work_order_detail'; 
        $this->tableNameDetailTechnician = 'ticket_support_work_order_detail_technician';
        $this->tableItemUnit = 'item_unit';
        $this->tableItem = 'item';
        $this->tableMedia = 'media';
        $this->tableCity = 'city';
        $this->tableWarehouse = 'warehouse';
        $this->securityObject = 'TicketSupportWorkOrder';
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
        /*$this->arrDataDetail['usedqtyinbaseunit'] = array('usedQtyInBaseUnit','number');
        $this->arrDataDetail['usedunitkey'] = array('selUsedUnit');
        $this->arrDataDetail['usedunitconvmultiplier'] = array('unitConvMultiplierUsedQty','number');*/

        $this->arrTechnician = array();  
        $this->arrTechnician['pkey'] = array('hidDetailTechnicianKey');
        $this->arrTechnician['refkey'] = array('pkey','ref');  
        $this->arrTechnician['techniciankey'] = array('hidTechnicianKey'); 
        //$this->arrTechnician['hour'] = array('hour','datetime'); 
        
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableNameDetail));
        array_push($arrDetails, array('dataset' => $this->arrTechnician, 'tableName' => $this->tableNameDetailTechnician));

        

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code'); 
        $this->arrData['refcode'] = array('refCode'); 
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['starttime'] = array('startTime','date');
        $this->arrData['endtime'] = array('endTime','date');
        $this->arrData['workdescription'] = array('workDescription'); 
        $this->arrData['ticketkey'] = array('hidSupportTicketKey');
        $this->arrData['notes'] = array('notes');   
        $this->arrData['statuskey'] = array('selStatus');


        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' =>'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));       
        array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'refCode','dbfield' => 'refcode','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'subject','title' => 'subject','dbfield' => 'subject','default' =>'true','width' => 150));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default' =>'true','width' => 250)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'enddate','title' => 'endDate','dbfield' => 'endtime','default'=>true, 'width' => 100, 'align' =>'center', 'format' =>'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/ticketSupportWorkOrder'));
       
		$this->overwriteConfig();
	    $this->includeClassDependencies(array(
			'TicketSupport.class.php',  
			'Warehouse.class.php',  
			'Customer.class.php', 
		    'Item.class.php', 
		    'ItemOut.class.php', 
		    'ItemIn.class.php', 
		    'ItemUnit.class.php', 
		    'GeneralJournal.class.php', 
			'Media.class.php', 
			'City.class.php', 
		)); 
   }
	 
	 
	 
    function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableTicket.'.code as refcode,
					'.$this->tableTicket.'.subject,
                    '.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableMedia.'.name as medianame,
                    '.$this->tableCity.'.name as cityname,
                    '.$this->tableCustomer.'.name as customername,
                    '.$this->tableCustomer.'.sid,
                    '.$this->tableCustomer.'.attention,
                    '.$this->tableCustomer.'.phone,
                    '.$this->tableCustomer.'.email,
                    '.$this->tableCustomer.'.address,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName. ' ,
					'.$this->tableTicket. '
                    left join '. $this->tableCustomer.' on ' . $this->tableTicket .'.customerkey = ' . $this->tableCustomer .'.pkey
                    left join '. $this->tableMedia.' on ' . $this->tableCustomer .'.mediakey = ' . $this->tableMedia .'.pkey
				    left join '. $this->tableCity.' on ' . $this->tableCustomer .'.citykey = ' . $this->tableCity .'.pkey,
                    '.$this->tableWarehouse. ',
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
					'.$this->tableName . '.ticketkey = '.$this->tableTicket.'.pkey 
 		         ' .$this->criteria ; 
		 
    } 

    function validateForm($arr,$pkey = ''){  
		$arrayToJs = parent::validateForm($arr,$pkey);
        $item = new Item(); 
        $ticketkey = $arr['hidSupportTicketKey']; 
		$arrItemkey = $arr['hidItemKey']; 
		$arrQty = $this->unformatNumber($arr['qty']);
		$arrQtyBaseUnit = $this->unformatNumber($arr['qtyInBaseUnit']);
        $message = $arr['workDescription'];
        $arrTechniciankey = $arr['hidTechnicianKey'];

        if(empty($arrTechniciankey))
            $this->addErrorList($arrayToJs,false, $this->errorMsg['technician'][1]);
        
        if(empty($ticketkey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['ticketSupportWorkOrder'][3]);
        
        if(empty($message)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['ticketSupportWorkOrder'][2]);
        }
		 return $arrayToJs;
	 }
    
	  function generateDefaultQueryForAutoComplete($returnField){ 
      
          $sql = 'select
					'.$returnField['key']. ',
                    '.$returnField['value'].' as value 
				from 
					'.$this->tableName . ','.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey
			';
          
        return $sql;
    }
    function reCountSubtotal($arrParam){
				
        $item = new Item(); 
        
        $arrItemkey = $arrParam['hidItemKey'];
        $arrQty = $arrParam['qty']; 
        $arrTransUnitKey = $arrParam['selUnit'];
        //$arrUsedQty = $arrParam['usedQty'];  
        //$arrUsedTransUnitKey = $arrParam['selUsedUnit']; 

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

     function normalizeParameter($arrParam, $trim = false){ 
            $arrParam = parent::normalizeParameter($arrParam,true); 
            $arrItemkey = $arrParam['hidItemKey']; 
            $reCountResult = $this->reCountSubtotal($arrParam);
            $arrParam['detail'] = $reCountResult['detail'];
             for ($i=0;$i<count($arrItemkey);$i++){ 
                if(empty($arrItemkey[$i]))
                    continue;
                 
                $qtyinbaseunit = $arrParam['detail'][$i]['qtyInBaseUnit'];
                $arrParam['qtyInBaseUnit'][$i] = $qtyinbaseunit;
                $arrParam['unitConvMultiplier'][$i] = $arrParam['detail'][$i]['unitConvMultiplier'];
            }
        return $arrParam;
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
    
    function validateConfirm($rsHeader){
  
        $id = $rsHeader[0]['pkey'];
        $rsTechnician = $this->getDetailTechnicianWithRelatedInformation($id);
        if(empty($rsTechnician))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['technician'][1]);

    }
    
    function confirmTrans($rsHeader){ 
        $id = $rsHeader[0]['pkey']; 
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        if(!empty($rsDetail))
            $this->addItemOut($rsHeader);

    }
    
    function validateClose($rsHeader){
        $arrayToJs = array();

	 	return $arrayToJs;
        
    }
    
    function closeTrans($rsHeader){ 
        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        if(!empty($rsDetail))
            $this->addItemIn($rsHeader);

            
        $sql = ' update '.$this->tableName.' set endtime = now() where pkey = ' . $this->oDbCon->paramString($id) .' ';
        $this->oDbCon->execute($sql);
        
    }
    
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $item = new Item();
        $itemOut = new ItemOut();   
        $itemIn = new ItemIn();   
        
        $rsOutKey = $itemOut->getTableKeyAndObj($this->tableName,array('key'));    
        $arrOutKey = $rsOutKey['key'];
     
        $rsItemOut = $itemOut-> searchDataRow( array(  $itemOut->tableName.'.pkey', $itemOut->tableName.'.code'  ) , 
                                ' and  '.$itemOut->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$arrOutKey.' and '.$itemOut->tableName.'.statuskey in(2,3)'  
                       );
        if(!empty($rsItemOut))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsItemOut[0]['code'].'</strong>. ' .$this->errorMsg[225]);
        
     
        $rsItemIn= $itemIn-> searchDataRow( array(  $itemIn->tableName.'.pkey', $itemIn->tableName.'.code'  ) , 
                                ' and  '.$itemIn->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$arrOutKey.' and '.$itemIn->tableName.'.statuskey in(2,3)'  
                       );
        if(!empty($rsItemIn))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsItemIn[0]['code'].'</strong>. ' .$this->errorMsg[225]);
      
    }
    
	function cancelTrans($rsHeader,$copy){
        $itemMovement = new ItemMovement();
		$id = $rsHeader[0]['pkey'];
        $this->cancelItemOut($rsHeader);
        $this->cancelItemIn($rsHeader);
		 
		if ($copy)
			$this->copyDataOnCancel($id);	  
		   
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
                $arrParam['qty'][$i] =  $rsDetail[$i]['qty'];
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
            $arrParam['trDesc'] = implode(', ',$arrEmployee);
            
            $arrayToJs = $itemOut->addData($arrParam);

            if (!$arrayToJs[0]['valid'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
            }
        
        
    }
    
    function addItemIn($rsHeader){
        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);
        $rsTechnician = $this->getDetailTechnicianWithRelatedInformation($id);
        $arrEmployee = array();
        $arrEmployee = array_column($rsTechnician,'technicianname');
        $technicianKey = $rsTechnician[0]['techniciankey'];
        $itemIn = new ItemIn();
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
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
            $arrParam['refCode'] = $rsHeader[0]['code'];
            $arrParam['refkey'] = $rsHeader[0]['pkey'];
            $arrParam['chkIsFullReceive'] = 1;
            $arrParam['hidEmployeeKey'] = $technicianKey;
            $tablekey =  $this->getTableKeyAndObj($this->tableName, array('key'));
            $tablekey = $tablekey['key'];
            $arrParam['reftabletype'] = $tablekey;
            $arrParam['trDesc'] = implode(', ',$arrEmployee);
            
            $arrayToJs = $itemIn->addData($arrParam);

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
            $itemOut->changeStatus($rsItemOut[$i]['pkey'],4,'',false, true);  
        }
          
    }
    
    function cancelItemIn($rsHeader){
        $itemIn = new ItemIn();   
        
        $rsInKey = $itemIn->getTableKeyAndObj($this->tableName,array('key'));    
        $arrInKey = $rsInKey['key'];
     
        $rsItemIn = $itemIn-> searchDataRow( array(  $itemIn->tableName.'.pkey', $itemIn->tableName.'.code'  ) , 
                                ' and  '.$itemIn->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$arrInKey.' and '.$itemIn->tableName.'.statuskey = 1'  
                       );
        
        $totalItem = count($rsItemIn);
        for($i=0;$i<$totalItem;$i++) { 
            $itemIn->changeStatus($rsItemIn[$i]['pkey'],4,'',false, true);  
        }
          
    }
    
    function backConfirmTrans($rsHeader){
		$this->cancelItemIn($rsHeader);
    }
    
    function validateBackConfirm($rsHeader){ 
        $itemIn = new ItemIn();   
        
        $rsOutKey = $itemIn->getTableKeyAndObj($this->tableName,array('key'));    
        $arrOutKey = $rsOutKey['key'];
        $rsItemIn= $itemIn-> searchDataRow( array(  $itemIn->tableName.'.pkey', $itemIn->tableName.'.code'  ) , 
                                ' and  '.$itemIn->tableName.'.refkey = '.$this->oDbCon->paramString($rsHeader[0]['pkey']).' and reftabletype = '.$arrOutKey.' and '.$itemIn->tableName.'.statuskey in(2,3)'  
                       );
        
        if(!empty($rsItemIn))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' <strong>'.$rsItemIn[0]['code'].'</strong>. ' .$this->errorMsg[225]);

        
    }
        
    
  }

?>
