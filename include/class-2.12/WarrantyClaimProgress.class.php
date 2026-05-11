<?php 
class WarrantyClaimProgress extends BaseClass{ 
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'warranty_claim_progress_header';
		$this->tableNameDetail = 'warranty_claim_progress_detail';
        $this->tableNameDetailStatus = 'warranty_claim_progress_status';
		$this->tableStatus = 'warranty_claim_progress_status';
		$this->tableItem = 'item';
        $this->tableWarrantyClaim = 'warranty_claim_header';
        $this->tableWarrantyClaimDetail = 'warranty_claim_detail';
        $this->tableCustomer = 'customer';
        $this->tableClaimResult = 'warranty_claim_result'; 
        $this->tableSNReplaceLog = 'item_sn_replace_log'; 
        $this->securityObject = 'WarrantyClaimProgress';
        $this->financialAccess = 'WarrantyFinancialAccess';
        $this->tableVendorPartNumber = 'item_vendor_part_number';
        $this->tableWarehouse = 'warehouse';
        $this->isTransaction = true;
		 
        $this->allowedStatusForEdit = array(1,2,3);
        
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['trdate'] = array('progressDate','date');
        $this->arrDataDetail['description'] = array('description', array('mandatory'=>true));
        //$this->arrDataDetail['progresskey'] = array('hidProgressKey');
        
        $this->arrData = array();
        //$this->arrData['pkey'] = array('pkey');
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warrantydate'] = array('warrantyDate','date');
        //$this->arrData['completiondate'] = array('completionDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['refheaderkey'] = array('hidRefHeaderKey');
        $this->arrData['itemkey'] = array('hidItemKey');
        $this->arrData['serialnumber'] = array('serialNumber');
        $this->arrData['vendorpartnumberkey'] = array('hidVendorPartNumberKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['claimresultkey'] = array('selClaimResult');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['buswarehousekey'] = array('selBUSWarehouseKey');
        $this->arrData['newitemkey'] = array('hidNewItemKey');
        $this->arrData['newvendorpartnumberkey'] = array('hidNewVendorPartNumberKey');
        $this->arrData['newserialnumber'] = array('newSerialNumber');
        $this->arrData['newwarrantydate'] = array('newWarrantyDate','date');
        //$this->arrData['reftabletype'] = array('hidRefTable');
        $this->arrData['financialapproval'] = array('hidFinancialApproval');
        $this->arrData['amount'] = array('amount','number');
        
                
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'reference','title' => 'reference','dbfield' => 'warrantycode','default'=>true,'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'serialNumber','title' => 'serialNumber','dbfield' => 'serialnumber','default'=>true,'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'itemName','title' => 'itemName','dbfield' => 'itemname','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'claim','title' => 'claim','dbfield' => 'claimresult','default'=>true,'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/warrantyClaimReceipt'));
     
        
		$this->overwriteConfig();
   }
   
    function getQuery(){
	   
        return '
			SELECT '.$this->tableName.'.* ,
              '.$this->tableWarrantyClaim.'.code as warrantycode,
              '.$this->tableCustomer.'.name as customername,
              '.$this->tableCustomer.'.phone as customerphone,
              '.$this->tableCustomer.'.mobile as customermobile,
               claimitem.name as itemname,
              newitem.name as newitemname,
              claimpart.partnumber,
              newpart.partnumber as newpartnumber,
			  '.$this->tableStatus.'.status as statusname,
              '.$this->tableWarehouse.'.name as warehousename,
              '.$this->tableClaimResult.'.name as claimresult
			FROM '.$this->tableCustomer.',
                 '.$this->tableWarehouse.',
                 '.$this->tableClaimResult.',
                 '.$this->tableName.'
                 left join '.$this->tableWarrantyClaim.' on  '.$this->tableName.'.refheaderkey = '.$this->tableWarrantyClaim.'.pkey 
                 left join '.$this->tableVendorPartNumber.' claimpart on  '.$this->tableName.'.vendorpartnumberkey = claimpart.pkey 
                 left join '.$this->tableVendorPartNumber.' newpart on  '.$this->tableName.'.newvendorpartnumberkey = newpart.pkey 
                 left join '.$this->tableItem.' claimitem on  '.$this->tableName.'.itemkey = claimitem.pkey 
                 left join '.$this->tableItem.' newitem on  '.$this->tableName.'.newitemkey = newitem.pkey ,
                 '.$this->tableStatus.'
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                '.$this->tableName.'.claimresultkey = '.$this->tableClaimResult.'.pkey and
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
 		' .$this->criteria ; 
		 
    }  
     
    function validateForm($arr,$pkey = ''){ 
         
        
        $item = new Item();
        
		$arrayToJs = parent::validateForm($arr,$pkey);
         
		$arrItemkey = $arr['hidItemKey']; 
        
        $serialNumber = trim($arr['serialNumber']); 
        $newSerialNumber = trim($arr['newSerialNumber']); 
        $newPartNumberKey = trim($arr['hidNewVendorPartNumberKey']); 
        $claimKey = $arr['selClaimResult']; 
        $amount = $this->unFormatNumber($arr['amount']); 
        $refkey = $arr['hidRefHeaderKey'];
        $customer = $arr['hidCustomerKey'];
            
        $itemkey = $this->unFormatNumber($arr['hidItemKey']); 
        $newitemkey = $this->unFormatNumber($arr['hidNewItemKey']); 
         
         if(empty($refkey)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['reference'][1]);
       
         if(empty($customer)) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
       
        
        if($serialNumber == $newSerialNumber){ 
		  $this->addErrorList($arrayToJs,false,$this->errorMsg['warrantyClaim'][6]);
        }
         
        // kalo SN gk sama dengan Part Number
        if(!empty($newSerialNumber) && !empty($newPartNumberKey)){
            $rsSN = $item->getSNInformation($newSerialNumber);  
            if(!empty($rsSN) && $rsSN[0]['vendorpartnumberkey'] != $newPartNumberKey)
                $this->addErrorList($arrayToJs,false,$this->errorMsg['serialnumber'][6]);
        }
   
		return $arrayToJs;
	 }
	  
    function changeStatus($id, $newStatus, $reason='',$copy=false, $autoChangeStatus = false, $ignoreValidation = false){ 
         return $this->changeTransactionStatus($id,$newStatus,$reason,$copy, $autoChangeStatus,$ignoreValidation);
    }

    function changeTransactionStatus($id,$status,$reason='',$copy=false, $autoChangeStatus = false, $ignoreValidation = false){
          
        $security = new Security();
            
        if (empty($_SESSION[$this->loginAdminSession]['id']))
            die;
          
        $rsHeader = $this->getDataRowById($id); 
         
      	try{  
            
            if(!$autoChangeStatus){   
                if(!$security->isAdminLogin($this->securityObject,$status,false))  
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[252],true);   
            }
            
            if ($rsHeader[0]['statuskey'] == count($this->getAllStatus())) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[221],true);   
    
            if ($rsHeader[0]['statuskey'] == $status) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[224],true);   
        
            $hasFinancialAccess = $security->isAdminLogin($this->financialAccess,10);  
            if ($rsHeader[0]['financialapproval'] == 1 && !$hasFinancialAccess){
                 
                if($rsHeader[0]['statuskey'] == 1 && ($status > 2 && $status < 5) ){
                    // harus ubah status ke konfirmasi keuangan dulu dr menunggu
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[201],true);   
       
                } else if($rsHeader[0]['statuskey'] == 2){
                     // gk boleh ubah status yg di konfirmasi keuangan
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[201],true);   
                }
                 
            }
              
            
        }catch(Exception $e){ 
 		     return $this->getErrorLog();  
		}		
				 

		try{ 
            
            // ================== VALIDATION 
             
		  	switch ($status){
				case 1 : $this->validateInput($rsHeader); 
						  break;
                case 2 : $this->validateFinancialApproval($rsHeader); 
						  break;
				case 3 : $this->validateConfirm($rsHeader); 
						  break;
				case 4 : $this->validateClose($rsHeader); 
						  break; 
				case 5 : $this->validateCancel($rsHeader, $autoChangeStatus);
						  break;
			} 
             
             
            //make sure we throw error 
            $this->throwIfHasErrorLog();  
             
            
            // ================== VALIDATION OK !
            
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
					  
            
			switch ($status){ 
				case 2 : $this->financialApprovalTrans($rsHeader);
                         break; 
				case 3 : $this->confirmTrans($rsHeader);
                         break;  
				case 4 : $this->closeTrans($rsHeader);
                         break;  
				case 5 : $this->cancelTrans($rsHeader,$copy);
                         $this->afterCancelTrans($rsHeader);
                         break;  
			}
			
			$sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id); 
            $this->oDbCon->execute($sql);  
             
            $this->setTransactionLog($status,$id); 
            $this->afterStatusChanged($rsHeader);
                
			$this->oDbCon->endTrans();  
			$this->addErrorLog(true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    } catch(Exception $e){ 
             
            $this->oDbCon->rollback(); 
            
            if (!empty($e->getMessage()))
                $this->addErrorLog(false,$e->getMessage());
			//$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 
        return $this->getErrorLog(); 
  }   
    
    function delete($id,$forceDelete = false,$reason = ''){ 
        $arrayToJs = $this->changeStatus($id, 5,'',false,$forceDelete);    
 		return $arrayToJs; 
	}
    
    
    function afterStatusChanged($rsHeader){
        
        $warrantyClaim = new WarrantyClaim();
        
        $id = $rsHeader[0]['pkey'];
        $rsHeader = $this->getDataRowById($id);
        
        //biar gk kepanggil 2x kalo dr proses keuangan
        $warrantyClaim->updateOutstanding($rsHeader[0]['refheaderkey']);
        
        // kalo dr konfirmasi keuangan
        switch ($rsHeader[0]['statuskey']){
            case 2 : // kalo tipenya replace, lanjut ke siap diproses dan gk perlu approval keuangan
                    if ( !$rsHeader[0]['financialapproval']   && $rsHeader[0]['claimresultkey'] == CLAIM_TYPE['replace'])
                        $this->changeStatus($id,3);
                    break;
                 
        }
        
        
    }

    
    function deductItemReplacement($rsHeader){ 
        
        $itemMovement = new ItemMovement();
        $item = new Item();
        
        $id = $rsHeader[0]['pkey'];
        $itemkey = $rsHeader[0]['newitemkey'];
        $claimKey = $rsHeader[0]['claimresultkey'];
        $rmaWarehouseKey = $rsHeader[0]['warehousekey'];
        $busWarehouseKey = $rsHeader[0]['buswarehousekey'];
        $releaseDate = $rsHeader[0]['trdate'];
        
        $note = '';

        $rsItem = $item->getDataRowById($itemkey);

        $itemMovement->updateItemMovement($id,$itemkey,-1, 0,$this->tableName, $busWarehouseKey, $note, $releaseDate);
        
        if(USE_SN){
                // set warranty enddate
                $warrantyPeriod = new WarrantyPeriod();
                $rsWarranty = $warrantyPeriod->searchData($warrantyPeriod->tableName.'.statuskey' ,1, true); 
                $rsWarranty = array_column($rsWarranty,'period', 'pkey' ); 
            
                if ($rsHeader[0]['claimresultkey'] == CLAIM_TYPE['upgrade']) { 
                    $warrantyMonth = $rsWarranty[$rsItem[0]['warrantyperiodkey']];

                    $date = new DateTime($rsHeader[0]['trdate']);
                    $date->add(new DateInterval('P'.$warrantyMonth.'M'));
                    $warrantyEndDate = $date->format('d / m / Y'); 
                }else{
                     $rsSN = $item->getSNInformation($rsHeader[0]['serialnumber']);  
                     $warrantyMonth = $rsWarranty[$rsItem[0]['warrantyperiod']];
                     $warrantyEndDate = $this->formatDBDate($rsSN[0]['warrantyperiodexpireddate']);
                }
            
             
                $itemMovement->updateItemSNMovement( 
                                array(
                                'refkey' => $id,
                                'refheaderkey' => $id,
                                'itemkey' => $itemkey,
                                'vendorpartnumberkey' => $rsHeader[0]['newvendorpartnumberkey'],
                                'sn' => $rsHeader[0]['newserialnumber'],
                                'qtyinbaseunit' => -1,
                                'costinbaseunit' => 0,
                                'reftable' => $this->tableName,
                                'warehousekey' => $rsHeader[0]['buswarehousekey'],
                                'note' => $note,
                                'trdate' => date('Y-m-d'),
                                'warrantyperiodkey' => $rsItem[0]['warrantyperiodkey'],
                                'warrantyperiodtime' => $warrantyMonth,
                                'warrantyperiodexpireddate' => $warrantyEndDate
                                )
                );
        }
         
    }
    
    function financialApprovalTrans($rsHeader){ 
        $this->deductItemReplacement($rsHeader); 
    }
    
    function closeTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
        
        // kalo VOID, keluarin item yg diterima
       
    }
    
    function confirmTrans($rsHeader){
        if($rsHeader[0]['statuskey']==1) 
            $this->deductItemReplacement($rsHeader); 
	}
    
    function cancelTrans($rsHeader,$copy){
		        
		$id = $rsHeader[0]['pkey'];
          
        // harus ditambahin cancel Item Movement utk item yg diterima garansi ??
        // atau harus dr status void ?
        
		if ($rsHeader[0]['statuskey'] == 1 || $rsHeader[0]['statuskey'] == 5)
			return; 
        
        $itemMovement = new ItemMovement();  
        $itemMovement->cancelMovement($id,$this->tableName);
        $itemMovement->cancelSNMovement($id,$this->tableName);
            
		// harus selalu ke copy 
        $this->copyDataOnCancel($id);
	}  
    
    function basicChangeStatusValidation($rsHeader){
        $security = new Security();
        
        $claimKey = $rsHeader[0]['claimresultkey'];
        $amount = $rsHeader[0]['amount'];
        
        $itemkey  =  $rsHeader[0]['itemkey'];
        $newSN = $rsHeader[0]['newserialnumber'];
        $newPartNumberKey = $rsHeader[0]['newvendorpartnumberkey'];
        $newitemkey = $rsHeader[0]['newitemkey'];
         
        if( $claimKey==CLAIM_TYPE['replace']){
            if(empty($newSN)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['warrantyClaim'][8]); 
        }
                
        if( $claimKey==CLAIM_TYPE['upgrade']){
            if(empty($newSN)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['warrantyClaim'][4]);  
            
            if($amount <= 0) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['warrantyClaim'][5]);   
        }
        
        if( $claimKey==CLAIM_TYPE['CN']){
            if($amount <= 0) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['warrantyClaim'][5]);  
        }  
    }
     
	function validateFinancialApproval($rsHeader){  
          // hanya bisa dr status 3
        if($rsHeader[0]['statuskey'] <> 1)   
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[203],true);    
        
        $this->basicChangeStatusValidation($rsHeader); 
        
        $newSN = $rsHeader[0]['newserialnumber']; 
        $newItemKey = $rsHeader[0]['newitemkey']; 
        $busWarehouseKey = $rsHeader[0]['buswarehousekey']; 
        $claimKey = $rsHeader[0]['claimresultkey'];
         
        if(!empty($newSN))
            $this->validateQOH($newItemKey,$newSN,$busWarehouseKey);
   
	 }
    
    function validateClose($rsHeader){   
        
        // hanya bisa dr status 3
        if($rsHeader[0]['statuskey'] <> 3)   
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[201],true);    
        
        $this->basicChangeStatusValidation($rsHeader); 
    }
    
    
    function validateConfirm($rsHeader){  
        $itemMovement = new ItemMovement();
        $item = new Item();
        $this->basicChangeStatusValidation($rsHeader);
         
        $newSN = $rsHeader[0]['newserialnumber']; 
        $newItemKey = $rsHeader[0]['newitemkey']; 
        $busWarehouseKey = $rsHeader[0]['buswarehousekey']; 
        $claimKey = $rsHeader[0]['claimresultkey'];
        
        if($rsHeader[0]['statuskey']==1)   
            $this->validateQOH($newItemKey,$newSN,$busWarehouseKey);
        
   
    }
    
    function validateCancel($rsHeader,$autoChangeStatus=false){
         
        $arrayToJs = array();

        return $arrayToJs;    
    }
    
    function validateQOH($itemkey,$sn,$warehousekey){
        
        $itemMovement = new ItemMovement();
         
        //cek stok harus ada
        $saldoakhir = $itemMovement->getItemSNQOH($itemkey, $sn, $warehousekey); 
        if( ($saldoakhir - 1) < 0) 
            $this->addErrorLog(false,'<strong>'. $sn.'</strong>. '.$this->errorMsg[402]);

    }
    
    function getDetailWithRelatedInformation($pkey,$criteria='', $order =''){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*
              from
			  	'.$this->tableNameDetail .'
			  where
			  	refkey = '.$this->oDbCon->paramString($pkey) . ' ';
       
        $sql .= ' '.$criteria;
        $sql .= ' ' .$order;
         
		return $this->oDbCon->doQuery($sql);
	
   }
    
    function getProgressStatus(){ 
       
	   $sql = 'select
	   			'.$this->tableNameDetailStatus .'.*, 
                '.$this->tableNameDetailStatus.'.name as progressname 
              from
			  	'.$this->tableNameDetailStatus .'
			  where
			  	'.$this->tableNameDetailStatus .'.statuskey = 1 ';
         
		return $this->oDbCon->doQuery($sql);
	
   }
  	
    function normalizeParameter($arrParam, $trim=false){
        
        $item = new Item();
        
        $arrParam = parent::normalizeParameter($arrParam);  
        
        // kalo gk ad progress
        $emptyProgress = true;
        foreach($arrParam['description'] as $row){  
            if(!empty($row)) {
                $emptyProgress = false;
                break;
            }
        }
        
        if ($emptyProgress){ 
            unset($this->arrDataDetail);
            $this->arrData['pkey'] = array('pkey'); 
        }
        
        $arrParam['hidNewItemKey'] = (isset( $arrParam['hidNewItemKey'])) ?  $arrParam['hidNewItemKey'] : 0;
        $arrParam['hidNewVendorPartNumberKey'] = (isset( $arrParam['hidNewVendorPartNumberKey'])) ?  $arrParam['hidNewVendorPartNumberKey'] : 0;
        $arrParam['newSerialNumber'] = (isset( $arrParam['newSerialNumber'])) ?  $arrParam['newSerialNumber'] : '';
        
        
        // kalo CN
        if ($arrParam['selClaimResult'] == CLAIM_TYPE['CN']){
            $arrParam['newSerialNumber'] = '';
            $arrParam['hidNewItemKey'] = 0;
            $arrParam['hidNewVendorPartNumberKey'] = 0;
            $arrParam['newWarrantyDate'] = DEFAULT_EMPTY_DATE;
        }
          
        //ambil ulang vendorpartnumberkey dan itemkey
        
        if (!empty($arrParam['serialNumber'])){
            $rsItem = $item->searchSerialNumber('','', $arrParam['serialNumber']);
            if(!empty($rsItem)){ 
               $arrParam['hidItemKey'] = $rsItem[0]['itemkey'] ;  
               $arrParam['hidVendorPartNumberKey'] = $rsItem[0]['vendorpartnumberkey'] ; 
            }
        }
        
        //kalo ad replace
        if (!empty($arrParam['newSerialNumber'])){
            $rsItem = $item->searchSerialNumber('','', $arrParam['newSerialNumber']);
            if(!empty($rsItem)){ 
               $arrParam['hidNewItemKey'] = $rsItem[0]['itemkey'] ;  
               $arrParam['hidNewVendorPartNumberKey'] = $rsItem[0]['vendorpartnumberkey'] ; 
            }else{ 
               $arrParam['newSerialNumber'] = '';  
               $arrParam['hidNewItemKey'] = 0 ; 
               $arrParam['hidNewVendorPartNumberKey'] = 0;
            }
        }
        
        
        // ganti validasi, kalo item beda baru perlu validasi
        // $arrParam['hidFinancialApproval'] = ($arrParam['selClaimResult'] == CLAIM_TYPE['replace']) ? 0 : 1;
        $arrParam['hidFinancialApproval'] = ($arrParam['hidNewItemKey'] == $arrParam['hidItemKey']) ? 0 : 1;
        //$this->setLog($arrParam['hidNewItemKey'] .' == '.$arrParam['hidItemKey'] . ' ' .$arrParam['hidFinancialApproval'],true);
        
        return $arrParam;
    }
    
    function voidWarranty(){
        // release SN pengganti
         
    }
    
    function updateSNReplaceLog($rsHeader){
       /* $rsTableKey = $this->getTableKeyAndObj($this->tableName); 
        $tablekey = $rsTableKey['key'];
        
        $sql = 'insert into '.$this->tableSNReplaceLog.' ( 
                        refkey,
                        reftabletype,
                        trdate,
                        olditemkey,
                        oldserialnumber,
                        oldvendorpartnumberkey,
                        newitemkey,
                        newvendorpartnumberkey,
                        newserialnumber 
                     ) values ( 
                        '.$this->oDbCon->paramString($rsHeader[0]['pkey']).',
                        '.$this->oDbCon->paramString($tablekey).',
                        '.$this->oDbCon->paramString($rsHeader[0]['trdate']).',
                        '.$this->oDbCon->paramString($rsHeader[0]['itemkey']).',
                        '.$this->oDbCon->paramString($rsHeader[0]['serialnumber']).',
                        '.$this->oDbCon->paramString($rsHeader[0]['vendorpartnumberkey']).',
                        '.$this->oDbCon->paramString($rsHeader[0]['newitemkey']).',
                        '.$this->oDbCon->paramString($rsHeader[0]['newvendorpartnumberkey']).',
                        '.$this->oDbCon->paramString($rsHeader[0]['newserialnumber']).' 
                    )';	 

        $this->oDbCon->execute($sql); */

    }
		
}
?>
