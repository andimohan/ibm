<?php 
class WarrantyClaim extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'warranty_claim_header';   
		$this->tableNameDetail = 'warranty_claim_detail'; 
        $this->tableNameIssueDetail = 'warranty_claim_issue_detail';
        $this->tableContentOfPackage = 'warranty_claim_item_content_detail';
        $this->tableVendorPartNumber = 'item_vendor_part_number';
		$this->tableItem = 'item';   
		$this->tableItemChecklist = 'item_checklist';   
        $this->tableIssueCategory = 'issue_category';
		$this->tableStatus = 'warranty_claim_status';   
        $this->tableClaimResult = 'warranty_claim_result'; 
		$this->tableCustomer = 'customer'; 
        $this->tableWarehouse = 'warehouse';
        $this->tableIssue = 'issue_category';
		$this->uploadFolder = 'warranty-claim/';
		$this->securityObject = 'WarrantyClaim';
        $this->isTransaction = true; 
                                
        $this->arrItemContentOfPackage = array();  
        $this->arrItemContentOfPackage['pkey'] = array('hidItemContentDetailKey');
        $this->arrItemContentOfPackage['refkey'] = array('hidDetailKey','ref');  
        $this->arrItemContentOfPackage['refheaderkey'] = array('pkey','ref');  
        $this->arrItemContentOfPackage['itemkey'] = array('hidItemDetailKey');  
        $this->arrItemContentOfPackage['qty'] = array('qtyDetail','number');
        //$this->arrItemContentOfPackage['ischeck'] = array('chkPick');  
       
        $this->arrIssue = array(); 
        $this->arrIssue['pkey'] = array('hidIssueDetailKey');
        $this->arrIssue['refkey'] = array('hidDetailKey','ref');  
        $this->arrIssue['refheaderkey'] = array('pkey','ref');  
        $this->arrIssue['issuekey'] = array('hidIssueKey', array('mandatory'=>true)); 
       
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrItemContentOfPackage, 'tableName' => $this->tableContentOfPackage));
        array_push($arrDetails, array('dataset' => $this->arrIssue, 'tableName' => $this->tableNameIssueDetail));

       
        $this->arrDataDetail = array();   
        //$this->arrDataDetail['pkey'] = array('hidDetailKey', array('dataDetail' => array('dataset' => $this->arrItemContentOfPackage, 'tableName' => $this->tableContentOfPackage)));
        $this->arrDataDetail['pkey'] = array('hidDetailKey', array('dataDetail' =>  $arrDetails));
        $this->arrDataDetail['refkey'] = array('pkey','ref'); 
        $this->arrDataDetail['serialnumber'] = array('serialNumber');
        $this->arrDataDetail['itemkey'] = array('hidItemKey', array('mandatory'=>true)); 
        $this->arrDataDetail['vendorpartnumberkey'] = array('hidVendorPartNumberKey');
        $this->arrDataDetail['itemoutdate'] = array('itemOutDate','date');
        $this->arrDataDetail['warrantyperiodexpireddate'] = array('warrantyPeriodExpiredDate','date');
        $this->arrDataDetail['sellerkey'] = array('hidSellerKey');
        $this->arrDataDetail['trdesc'] = array('detailNotes');  
        $this->arrDataDetail['claimresultkey'] = array('selClaimResult');  
 
       
        $this->arrData = array(); 
        //$this->arrData['pkey'] = array('pkey');
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => array('dataset' => $this->arrDataDetail)));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['customerphone'] = array('customerPhone');
        $this->arrData['customeremail'] = array('customerEmail');
        $this->arrData['trdesc'] = array('trDesc');  
        
             
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/warrantyClaim'));
     
		$this->overwriteConfig();
       
   }
    
    function getQuery(){ 
	   
	   $sql = '
				select
					'.$this->tableName. '.*,
					'.$this->tableCustomer. '.name as customername,
					'.$this->tableStatus.'.status as statusname  
				from 
					'.$this->tableName . ',
                    '.$this->tableCustomer.' ,
                    '.$this->tableStatus. '
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and
					'.$this->tableName . '.customerkey = '.$this->tableCustomer.'.pkey
                    
 		' .$this->criteria ; 
		  
       return $sql;
   }
     
    function updateIssueDetail($arrParam){   
        $headerkey =  $arrParam['pkey'];
        $rsDetail = $this->getDetailById($headerkey); 
        
		$sql = 'delete from '.$this->tableNameIssueDetail.' where refheaderkey = '. $this->oDbCon->paramString($headerkey);
		$this->oDbCon->execute($sql);
        
        for ($i=0;$i<count($rsDetail);$i++){
            $detailkey = $rsDetail[$i]['pkey'];
            $arrIssueKey = explode(',',$arrParam['hidIssueKey'][$i]);
            foreach($arrIssueKey as $issueKey){ 

                 $sql = 'insert into '.$this->tableNameIssueDetail.' ( 
                                refkey,
                                refheaderkey,
                                issuekey 
                             ) values (
                                '.$this->oDbCon->paramString($detailkey).',
                                '.$this->oDbCon->paramString($headerkey).',
                                '.$this->oDbCon->paramString($issueKey).' 
                            )';	  

                    $this->oDbCon->execute($sql);
            }
        }
    }
    
/*    function updateDetail($arrParam){
        $truckingServiceOrder = new TruckingServiceOrder();
		$pkey = $arrParam['pkey'];
         
		$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
        
		$sql = 'delete from '.$this->tableContentOfPackage.' where refheaderkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		
		$arrDetailRowsToken = $arrParam['detailRowsToken'];
		$arrItemKey = $arrParam['hidItemKey'];
		$arrVendorPartNumber = $arrParam['hidVendorPartNumberKey']; 
		$arrSN = $arrParam['serialNumber'];
		$arrIssue = $arrParam['hidIssueKey'];
		$arrDesc = $arrParam['detailNotes'];
          
        
		for ($i=0;$i<count($arrDetailRowsToken);$i++){
			    
            $tokenkey = $arrDetailRowsToken[$i];
            
			if ( empty($arrItemKey[$i]) )  
                continue; 
          
            $detailkey = $this->getNextKey($this->tableNameDetail); 
			$sql = 'insert into '.$this->tableNameDetail.' (
                        pkey,
						refkey,
                        itemkey,
						vendorpartnumberkey,
                        serialnumber,
                        issuekey,
						trdesc
					 ) values (
						'.$this->oDbCon->paramString($detailkey).',
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrItemKey[$i]).', 
						'.$this->oDbCon->paramString($arrVendorPartNumber[$i]).', 
                        '.$this->oDbCon->paramString($arrSN[$i]).',
						'.$this->oDbCon->paramString($arrIssue[$i]).', 
						'.$this->oDbCon->paramString($arrDesc[$i]).'
					)';	  
            
			$this->oDbCon->execute($sql);
   
            // content of package   
            $arrItemDetailKey = $arrParam['hidItemDetailKey'][$tokenkey]; 
            $arrQtyDetail = $arrParam['qtyDetail'][$tokenkey]; 
            //$arrPick = $arrParam['chkPick'][$tokenkey]; 
  
            for ($j=0;$j<count($arrItemDetailKey);$j++){  
                
                    $sql = 'insert into '.$this->tableContentOfPackage.' (
                            refkey,
                            refheaderkey,
                            itemkey,
                            qty,
                            ischeck 
                         ) values (
                            '.$this->oDbCon->paramString($detailkey).',
                            '.$this->oDbCon->paramString($pkey).',
                            '.$this->oDbCon->paramString($arrItemDetailKey[$j]).',
                            '.$this->oDbCon->paramString($this->unFormatNumber($arrQtyDetail[$j])).',  
                            '.$this->oDbCon->paramString($arrPick[$j]).' 
                        )';	 
                
                //$this->setLog($sql);
                $this->oDbCon->execute($sql);
            }
           

		}
	} 
    */
    /*
	function updateImage($pkey,$token,$arrImage){	 
		$sourcePath = $this->uploadTempDoc.$this->uploadFolder.$token;
		$destinationPath = $this->defaultDocUploadPath.$this->uploadFolder;
		
		 
		if(!is_dir($sourcePath)) 
			return;
			
		if(!is_dir($destinationPath)) 
			mkdir ($destinationPath,  0755, true);
			
		$destinationPath .= $pkey;  
		  
		//delete previous images	    
		$this->deleteAll($destinationPath);  
		$sql = 'delete from '.$this->tableImage.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
		  
		if (!empty($arrImage))	{
			$arrImage = explode(",",$arrImage);
			for ($i=0;$i<count($arrImage);$i++){   
                
                if ($i >= PLAN_TYPE['maxproductimage'])
                    continue;
				
				$newFileName = $this->hashFileName($sourcePath.$arrImage[$i]);
				$imagekey = $this->getNextKey($this->tableImage);  
				$this->uploadImage($sourcePath, $destinationPath,$arrImage[$i],$newFileName);
				
				$sql = 'insert into '.$this->tableImage.' (pkey,refkey,file) values ('.$this->oDbCon->paramString($imagekey).','.$this->oDbCon->paramString($pkey).', '.$this->oDbCon->paramString($newFileName).')';	
				$this->oDbCon->execute($sql);	 
				 
			}		
		}  
					
	} 
	*/
    
    function validateConfirm($rsHeader){
        $security = new Security(); 
        $item = new Item();
        $ignoreExpiredDate = $security->isAdminLogin('ignoreExpiredDate',10); 
        
        $rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);
        
        if (!$ignoreExpiredDate){ 
            for($i=0;$i<count($rsDetail);$i++){
                if($rsDetail[$i]['warrantyperiodexpireddate']<$rsHeader[0]['trdate'])
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].', '.$rsDetail[$i]['serialnumber'].'.</strong> ' .$this->errorMsg['warrantyClaim'][3]);
            }
        }
        
       for($i=0;$i<count($rsDetail);$i++){
            $rsSNMarket = $item->searchSerialNumber('','', $rsDetail[$i]['serialnumber'], '', ' and warehousekey = 0 ' );
            if(empty($rsSNMarket))
                $this->addErrorLog(false,'<strong>'.$rsSNMarket[0]['code'].'</strong>. '.$this->errorMsg['serialnumber'][4]);
        }
	 }
	 
function confirmTrans($rsHeader){
		$warrantyClaimProgress = new WarrantyClaimProgress();
        $itemMovement = new ItemMovement();
        $item = new Item();
		$rsDetail = $this->getDetailWithRelatedInformation($rsHeader[0]['pkey']);
        $rsTableKey = $this->getTableKeyAndObj($this->tableName); 
        //$this->setLog("test" . count($rsDetail));
    
        for($i=0;$i<count($rsDetail);$i++){
            $user = base64_decode($_SESSION[$this->loginAdminSession]['id']);
        
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidRefKey'] = $rsDetail[$i]['pkey'];
            $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
            $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey'];
            $arrParam['hidItemKey'] = $rsDetail[$i]['itemkey'];
            $arrParam['serialNumber'] = $rsDetail[$i]['serialnumber'];
            $arrParam['hidVendorPartNumberKey'] = $rsDetail[$i]['vendorpartnumberkey'];
            $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
            $arrParam['warrantyDate'] = $this->formatDBDate($rsDetail[$i]['warrantyperiodexpireddate'],'d / m / Y');
            $arrParam['newWarrantyDate'] = $this->formatDBDate($rsDetail[$i]['warrantyperiodexpireddate'],'d / m / Y');
            $arrParam['trDesc'] = '';
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
            $arrParam['selClaimResult'] = $rsDetail[$i]['claimresultkey'];
                
            $rsTableKey = $this->getTableKeyAndObj($this->tableName); 
            $arrParam['hidRefTable'] = $rsTableKey['key'];
            $arrParam['createdBy'] = $user;

            //$this->setLog($arrParam);
            $arrayToJs = $warrantyClaimProgress->addData($arrParam); 

            if (!$arrayToJs[0]['valid'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
             
            $note =  $this->ucFirst($rsHeader[0]['code'].'. '. $this->lang['itemIn']. '.');
            
            $rsItem = $item->getDataRowById($rsDetail[$i]['itemkey']);
		    $itemMovement->updateItemMovement($rsHeader[0]['pkey'],$rsDetail[$i]['itemkey'],1,$rsItem[0]['cogs'],$this->tableName, $rsHeader[0]['warehousekey'], $note ,$rsHeader[0]['trdate'],$rsDetail[$i]['vendorpartnumberkey']);
            
            
            if(USE_SN){ 
                
                $rsSNINfo = $item->getSNInformation($rsDetail[$i]['serialnumber']);
 
                $itemMovement->updateItemSNMovement( 
                        array(
                        'refkey' => $rsHeader[0]['pkey'],
                        'refheaderkey' => $rsHeader[0]['pkey'],
                        'trdate' => $rsHeader[0]['trdate'] ,
                        'itemkey' => $rsDetail[$i]['itemkey'],
                        'vendorpartnumberkey' => $rsDetail[$i]['vendorpartnumberkey'],
                        'sn' => $rsDetail[$i]['serialnumber'],
                        'warehousekey' => $rsHeader[0]['warehousekey'],
                        'qtyinbaseunit' => 1,
                        'reftable' => $this->tableName,  
                        'costinbaseunit' => $rsItem[0]['cogs'],
                        'note' => $note,
                        'supplierkey' => $rsHeader[0]['customerkey'],
                        'warrantyvendorperiodkey' => $rsSNINfo[0]['warrantyvendorperiodkey'],
                        'warrantyvendorperiodtime' => $rsSNINfo[0]['warrantyvendorperiod'],
                        'warrantyvendorperiodexpireddate' => $this->formatDBDate($rsSNINfo[0]['warrantyvendorperiodexpireddate']),
                        'warrantyperiodkey' =>  $rsSNINfo[0]['warrantyperiodkey'],
                        'warrantyperiodtime' =>  $rsSNINfo[0]['warrantyperiod'],
                        'warrantyperiodexpireddate' =>  $this->formatDBDate($rsSNINfo[0]['warrantyperiodexpireddate']),
                )); 
            }
            
        } 
	}

	function validateForm($arr,$pkey = ''){
		       
		$arrayToJs = parent::validateForm($arr,$pkey);
        $customer = new Customer();
        
        $arrDetailKey = $arr['hidDetailKey'];
        $arrCustomerKey = $arr['hidCustomerKey']; 
        $arrCustomerEmail = $arr['customerEmail']; 
        $arrCustomerPhone= $arr['customerPhone']; 
        $arrSerialNumber = $arr['serialNumber'];
        $arrIssueKey = $arr['hidIssueKey'];
         
		$arrVendorPartNumberKey = $arr['hidVendorPartNumberKey'];
        
        if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}
          
        foreach($arrIssueKey as $issue){
            $hasValue = false;
            foreach($issue as $item) 
                if (!empty($item))  $hasValue = true;
           
            if (!$hasValue)
				$this->addErrorList($arrayToJs,false,$this->errorMsg['issue'][1]);
        }
        
        if(empty($arrCustomerKey))
            $this->addErrorList($arrayToJs,false, $this->errorMsg['customer'][1]);
            
        if(empty($arrCustomerEmail)){ 
          
        }else if(!filter_var($arrCustomerEmail, FILTER_VALIDATE_EMAIL)) { 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['email'][3]); 
        
        }
        
        if(empty($arrCustomerPhone))
            $this->addErrorList($arrayToJs,false, $this->errorMsg['phone'][1]);
        
        $arrDetailKey = array();  
        for($i=0;$i<count($arrSerialNumber);$i++) { 
            if (empty($arrSerialNumber[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['serialnumber'][1]); 	
			} else{
                if (in_array($arrSerialNumber[$i],$arrDetailKey)){   
                    $this->addErrorList($arrayToJs,false, $arrSerialNumber[$i].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    if (!empty($arrSerialNumber[$i])) {  
                        array_push($arrDetailKey, $arrSerialNumber[$i]);
                    }
                }
            } 
        } 
		  
        
		return $arrayToJs;
	 }  

    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableVendorPartNumber.'.partnumber,
                '.$this->tableItem.'.name as itemname, 
                '.$this->tableIssue.'.name as issuename,
                '.$this->tableCustomer.'.name as sellername
			  from
			  	'. $this->tableNameDetail .'
                    left join '.$this->tableVendorPartNumber.' on 
                    '.$this->tableNameDetail .'.vendorpartnumberkey =  '.$this->tableVendorPartNumber.'.pkey
                    left join '.$this->tableIssue.' on 
                    '.$this->tableNameDetail .'.issuekey =  '.$this->tableIssue.'.pkey  
                    left join '.$this->tableCustomer.' on 
                    '.$this->tableNameDetail .'.sellerkey =  '.$this->tableCustomer.'.pkey ,
                '.$this->tableItem.'
			  where
			  	' . $this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria; 
         
		return $this->oDbCon->doQuery($sql);
	
   }
    
    function getItemContentDetail($refkey){
        $sql = 'select 
                    '. $this->tableContentOfPackage. '.*, 
                    '.$this->tableItemChecklist.'.name as itemname 
                from 
                    ' . $this->tableContentOfPackage. ',
                    ' . $this->tableItemChecklist. '
                where 
                    refkey = ' . $this->oDbCon->paramString($refkey) . ' and 
                    ' . $this->tableContentOfPackage. '.itemkey = ' . $this->tableItemChecklist. '.pkey'; 
           
        return  $this->oDbCon->doQuery($sql);
    }
     
    function getIssueDetail($refkey){
        $sql = 'select 
                    '. $this->tableNameIssueDetail. '.pkey, 
                    '. $this->tableNameIssueDetail. '.issuekey, 
                    '.$this->tableIssue.'.name as issue 
                from 
                    ' . $this->tableNameIssueDetail. ',
                    ' . $this->tableIssue. '
                where 
                    refkey = ' . $this->oDbCon->paramString($refkey) . ' and 
                    ' . $this->tableNameIssueDetail. '.issuekey = ' . $this->tableIssue. '.pkey'; 
           
        return  $this->oDbCon->doQuery($sql);
    }
      
    function validateCancel($rsHeader,$autoChangeStatus=false){  
        $id = $rsHeader[0]['pkey'];
        $warrantyClaimProgress = new WarrantyClaimProgress();
        $rsProgress = $warrantyClaimProgress->searchData('','',true,' and  '.$warrantyClaimProgress->tableName.'.refheaderkey = '.$this->oDbCon->paramString($id).' and '.$warrantyClaimProgress->tableName.'.statuskey in(2,3,4)');
         
        if(!empty($rsProgress)){
            foreach($rsProgress as $progress) 
                 $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$progress['code'].'</strong>. ' . $this->errorMsg[203]);
        }
     }

   	function cancelTrans($rsHeader,$copy){  
        
        $itemMovement = new ItemMovement();  
        
		$id = $rsHeader[0]['pkey'];
		  	   
		$itemMovement->cancelMovement($id,$this->tableName);
		$itemMovement->cancelSNMovement($id,$this->tableName);  
        
        $warrantyClaimProgress = new WarrantyClaimProgress();
        $rsProgress = $warrantyClaimProgress->searchData($warrantyClaimProgress->tableName.'.refheaderkey',$id,true, ' and '. $warrantyClaimProgress->tableName.'.statuskey = 1');
        foreach($rsProgress as $warrantyProgress)  
            $warrantyClaimProgress->changeStatus($warrantyProgress['pkey'], 5,'',false,true);
           
		if ($copy)
			$this->copyDataOnCancel($id);	  
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
     
    
    function normalizeParameter($arrParam, $trim = false){
        $item = new Item();
        $arrParam = parent::normalizeParameter($arrParam);
        
        $itemkey = $arrParam['hidItemKey'];
        for($i=0;$i<count($itemkey);$i++){ 
                       
            $rsMovement = $item->searchSerialNumber('','', $arrParam['serialNumber'][$i], '', ' and warehousekey = 0' );
            if (empty($rsMovement)){
                 $arrParam['serialNumber'][$i] = '';
                 $arrParam['hidVendorPartNumberKey'][$i] = 0;
                 $arrParam['hidItemKey'][$i] = 0;
                 $arrParam['warrantyPeriodExpiredDate'][$i] = DEFAULT_EMPTY_DATE; 
            }else{ 
                $arrParam['hidVendorPartNumberKey'][$i] = $rsMovement[0]['vendorpartnumberkey'];
                $arrParam['hidItemKey'][$i] = $rsMovement[0]['itemkey'];
                $arrParam['warrantyPeriodExpiredDate'][$i] = $this->formatDBDate($rsMovement[0]['warrantyperiodexpireddate'],'d / m / Y');
            }
        }
            
        $details = array();
        array_push($details,$this->arrItemContentOfPackage);
        array_push($details,$this->arrIssue);

        $arrParam = $this->prepareMultiLevelDetail($arrParam,$details);
        
        return $arrParam;
    }
     
    function getClaimResult(){ 
       
	   $sql = 'select
	   			'.$this->tableClaimResult .'.* 
              from
			  	'.$this->tableClaimResult.'
            where statuskey = 1 ' ;
        
		return $this->oDbCon->doQuery($sql);
	
   }
      
    function updateOutstanding($pkey){ 
        
        $rs = $this->getDataRowById($pkey);
        
	    $warrantyClaimProgress = new WarrantyClaimProgress();
        $rsProgress = $warrantyClaimProgress->searchData($warrantyClaimProgress->tableName.'.refheaderkey',$pkey,true,' and '.$warrantyClaimProgress->tableName.'.statuskey < 4');
        
        $statuskey = (empty($rsProgress)) ? 3 : 2;
           
        if($rs[0]['statuskey'] <> $statuskey)
            $this->changeStatus($pkey,$statuskey, '', false, true,true);
        
	 
    }
    
  }  
?>
