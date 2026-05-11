<?php

class CashBankTransfer extends BaseClass{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'cash_bank_transfer_header';
		$this->tableNameDetail = 'cash_bank_transfer_detail'; 
		$this->tableStatus = 'transaction_status';
        $this->tableCOA = 'chart_of_account'; 
        $this->tableFile = 'cash_bank_transfer_file';
		$this->uploadFileFolder = 'cash-bank-transfer/'; 
        $this->tableWarehouse = 'warehouse';
		$this->isTransaction = true;  
       
        $this->useStorage = $this->useStorage('S3');

        $this->activeModule = $this->isActiveModule(array('PettyCash'));
	   
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['coafromkey'] = array('hidCOAFromKey');
        $this->arrDataDetail['coatokey'] = array('hidCOAToKey');
        $this->arrDataDetail['amount'] = array('amount','number');
        $this->arrDataDetail['trdesc'] = array('trdesc');
    
	    $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        
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
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['grandtotal'] = array('total','number');
        $this->arrData['statuskey'] = array('selStatus');   
        $this->arrData['warehousekey'] = array('selWarehouseKey');
	    
        $this->securityObject = 'CashBankTransfer'; 
		 	
              
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouseName','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal','default'=>true, 'width' => 100, 'align' =>'right','format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'note','title' => 'note','dbfield' => 'trdesc','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->printMenu = array();  
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/cashBankTransfer')); 
        
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
    	$this->includeClassDependencies(array(
            'CashBank.class.php',
            'COALink.class.php',
            'GeneralJournal.class.php',
            'PettyCash.class.php'

        )); 

        $this->overwriteConfig();
   }
    
    function getQuery(){
	   
	   $sql = '
			SELECT 
                '.$this->tableName.'.*  , 
			   '.$this->tableStatus.'.status as statusname,
               '.$this->tableWarehouse.'.name as warehousename
			FROM 
                  '.$this->tableName.'  
                        left join '.$this->tableWarehouse.' on '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey,
                  '.$this->tableNameDetail.' ,
                  '.$this->tableStatus.' 
			WHERE 
                '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 	      ' .$this->criteria ; 
		 
        $sql .=  $this->getCOACriteria('', array($this->tableNameDetail.'.coatokey', $this->tableNameDetail.'.coafromkey' )) ;
        $sql .=  $this->getWarehouseCriteria() ;
        
        $sql .=' group by '.$this->tableName.'.pkey ';
        
        return $sql;
    } 
		
     function afterStatusChanged($rsHeader){   
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
         
        // tergantung settingan
        $autoClose = $this->loadSetting('transactionAutoClose');
        if($autoClose == 1){ 
            if ($rsHeader[0]['statuskey'] == 2)
                $this->changeStatus($rsHeader[0]['pkey'],3); 
         }
    }
    
     function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$chartOfAccount = new ChartOfAccount();
		   
		$arrCOAFromkey = $arr['hidCOAFromKey']; 
		$arrCOATokey = $arr['hidCOAToKey'];
		$arrAmount = $arr['amount'];
		
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
		
		for($i=0;$i<count($arrCOAFromkey);$i++) {
			
			if (empty($arrCOAFromkey[$i]) || empty($arrCOATokey[$i])  ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['coa'][1]); 	
			}
			
			$rsCOAFrom = $chartOfAccount->getDataRowById($arrCOAFromkey[$i]);
			$rsCOATo = $chartOfAccount->getDataRowById($arrCOATokey[$i]);
			
			if (!empty($arrCOAFromkey[$i]) && !empty($arrCOATokey[$i])){
				if ($arrCOAFromkey[$i] == $arrCOATokey[$i]){
					$this->addErrorList($arrayToJs,false,$rsCOAFrom[0]['code']. ' - ' .$rsCOAFrom[0]['name'].' ke '.$rsCOATo[0]['code']. ' - ' .$rsCOATo[0]['name'].'. ' . $this->errorMsg['coatransfer'][1]);
				}
				if ($this->unFormatNumber($arrAmount[$i]) <= 0){
					$this->addErrorList($arrayToJs,false,$rsCOAFrom[0]['code']. ' - ' .$rsCOAFrom[0]['name'].' ke '.$rsCOATo[0]['code']. ' - ' .$rsCOATo[0]['name'].'. ' . $this->errorMsg[503]);
				}
			}
		}
		  
		
		return $arrayToJs;
	 }
	  
	function validateConfirm($rsHeader){
        
	 }		

	function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
		//$cashMovement = new CashMovement();  
		//$chartOfAccount = new ChartOfAccount();
		
        
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
		
		/*for($i=0;$i<count($rsDetail); $i++){
			$rsCOAFrom = $chartOfAccount->getDataRowById($rsDetail[$i]['coafromkey']);
			$rsCOATo = $chartOfAccount->getDataRowById($rsDetail[$i]['coatokey']);
			
			$note = $rsHeader[0]['code'] .'. Perpindahan Kas dari '.$rsCOAFrom[0]['name'].' ke ' .$rsCOATo[0]['name'].'. '; 
			$rsDetail[$i]['trdesc'] = htmlspecialchars_decode($rsDetail[$i]['trdesc']);
			
			$cashMovement->updateCashMovement($id,$rsDetail[$i]['coafromkey'],-$rsDetail[$i]['amount'],$this->tableName, 0, $note.$rsDetail[$i]['trdesc'],$rsHeader[0]['trdate']);
			$cashMovement->updateCashMovement($id,$rsDetail[$i]['coatokey'],$rsDetail[$i]['amount'],$this->tableName, 0, $note.$rsDetail[$i]['trdesc'],$rsHeader[0]['trdate']);
		}	*/	
        
        if(ADV_FINANCE){
            
			if( $this->isActiveModule('CashBank') ){
				$cashBank = new CashBank(); 
				// jangan digabung, kalo digabung detail pkey nya jd gk relevan

				//$arrCoaFrom = $this->sumArrayColumnGroup($rsDetail,'coafromkey', array('amount')); 
				foreach ($rsDetail as $key => $row) { 
					$rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('coakey' => $row['coafromkey'], 'desc' => $row['trdesc'], 'amount' => -$row['amount'], 'detailkey' => $row['pkey'])); 
					$rsDetail[$key]['cashOutRefKey'] = $rsCashBank['pkey'];

					$rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('coakey' => $row['coatokey'], 'desc' => $row['trdesc'], 'amount' => $row['amount'], 'detailkey' => $row['pkey'])); 
					$rsDetail[$key]['cashInRefKey'] = $rsCashBank['pkey']; 
				}
			}
            
        }
        if($this->activeModule['pettycash']) {
            $this->addPettyCash($rsHeader);
        }

        
		//update jurnal umum    
        $this->updateGL($rsHeader,$rsDetail);
        
	} 
	
    /*function addCashBank($rsHeader,$rsDetail){ 
            
        if(!ADV_FINANCE) return;
        if(empty($rsDetail)) return;
        
        $cashBank = new CashBank();  
       
        $rsTableKey = $this->getTableKeyAndObj($this->tableName, array('key'));
        $timestampArr = $this->getDateUsedForTimestamp($this->tableName, $rsHeader);
        
        $arrCoaFrom = $this->sumArrayColumnGroup($rsDetail,'coafromkey', array('amount'));
        foreach ($arrCoaFrom as $key => $row) { 
            if($row['amount'] <= 0) continue;
             
            $arrParam = array();
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['refCode'] = $rsHeader[0]['code'];
            $arrParam['trDate'] = $this->formatDBDate($timestampArr['timestamp'],'d / m / Y'); 
            $arrParam['trDesc'] = '';
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey']; 
            $arrParam['reftable'] = $rsTableKey['key'];
            $arrParam['selTransactionTypeKey'] = $rsTableKey['key'];
            $arrParam['hidCOAHeaderKey'] = $key;
            $arrParam['amount'] =  -$row['amount'];
            $arrParam['outstanding'] =  -$row['amount'];
            $arrParam['detailKey'] = $row['pkey'];
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            
            $arrayToJs = $cashBank->addData($arrParam);
             
            if (!$arrayToJs[0]['valid'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
            
            $cashBank->changeStatus($arrayToJs[0]['data']['pkey'],2); 
              
        }
        
        
        
        $arrCoaTo = $this->sumArrayColumnGroup($rsDetail,'coatokey', array('amount'));
        foreach ($arrCoaTo as $key => $row) { 
            if($row['amount'] <= 0) continue;
             
            $arrParam = array();
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidRefKey'] = $rsHeader[0]['pkey'];
            $arrParam['refCode'] = $rsHeader[0]['code'];
            $arrParam['trDate'] = $this->formatDBDate($timestampArr['timestamp'],'d / m / Y'); 
            $arrParam['trDesc'] = '';
            $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey']; 
            $arrParam['reftable'] = $rsTableKey['key'];
            $arrParam['selTransactionTypeKey'] = $rsTableKey['key'];
            $arrParam['hidCOAHeaderKey'] = $key;
            $arrParam['amount'] =  $row['amount'];
            $arrParam['outstanding'] =  $row['amount'];
            $arrParam['detailKey'] = $row['pkey'];
            $arrParam['overwriteGL'] = 1;
            $arrParam['islinked'] = 1;
            
            $arrayToJs = $cashBank->addData($arrParam);
             
            if (!$arrayToJs[0]['valid'])
                $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
            
            $cashBank->changeStatus($arrayToJs[0]['data']['pkey'],2); 
             
            
        }
        

    }*/
    
    function updateGL($rsHeader,$rsDetail){ 
        if (!USE_GL) return;
        
		 $generalJournal = new GeneralJournal();
         $rsKey = $generalJournal->getTableKeyAndObj($this->tableName,array('key'));
		 
		 $arr = array();
		 $arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		 $arr['code'] = 'xxxxx';
		 $arr['refkey'] = $rsHeader[0]['pkey'];
		 $arr['refTableType'] = $rsKey['key'];
		 $arr['trDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
		 $arr['trDesc'] = html_entity_decode($rsHeader[0]['trdesc']);
		 $arr['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
		 $arr['createdBy'] = 0; 
		  
		 $isActiveCashBank = $this->isActiveModule('CashBank');
			 
        $temp = -1; 
        for($i=0;$i<count($rsDetail);$i++){ 
            $amount =  $rsDetail[$i]['amount'];
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsDetail[$i]['coafromkey'];
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $amount;  
            $arr['trdescDetail'][$temp] = html_entity_decode($rsDetail[$i]['trdesc']);
			
			if($isActiveCashBank)
            	$arr['refCashBankKey'][$temp] = (isset($rsDetail[$i]['cashOutRefKey'])) ? $rsDetail[$i]['cashOutRefKey'] : '';
 
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsDetail[$i]['coatokey'];
            $arr['debit'][$temp] = $amount; 
            $arr['credit'][$temp] = 0;  
            $arr['trdescDetail'][$temp] = html_entity_decode($rsDetail[$i]['trdesc']);
			
			
			if($isActiveCashBank)
            	$arr['refCashBankKey'][$temp] = (isset($rsDetail[$i]['cashInRefKey'])) ? $rsDetail[$i]['cashInRefKey'] : '';
        }
          
        
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 
	 }
      
	 
	function cancelTrans($rsHeader,$copy){ 
		  
		$id = $rsHeader[0]['pkey'];
 
		/*$cashMovement = new CashMovement();  
		$cashMovement->cancelMovement($id,$this->tableName);*/
		
		
		if ($copy)
			$this->copyDataOnCancel($id);	
		 
		if( $this->isActiveModule('CashBank') ){
			$cashBank = new CashBank();
			$cashBank->cancelCashBank($rsHeader,$this->tableName);
		}
   if($this->activeModule['pettycash']) {
            $this->deletePettyCash($rsHeader);
        }
        
        
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
  
    function addPettyCash($rsHeader)
    {
        $pettyCash = new PettyCash();

        $rsKey = $pettyCash->getTableKeyAndObj($pettyCash->tableName,array('key'));

        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailWithRelatedInformation($id);
        
        if(empty($rsDetail)) return;

        foreach($rsDetail as $row) {

            $arr = array();

            $arr['code'] = 'xxxxx';
            $arr['trDate'] =   $rsHeader[0]['trdate']; 
            $arr['hidRefKey'] = $row['pkey'];
            $arr['reftablekey'] = $rsKey['key'];
            $arr['trDesc'] = $row['trdesc'];
            $arr['hidCOAKey'] = $row['coatokey'];
            $arr['debit'] = $row['amount'];
            $arr['credit'] = 0; //agar di database tidak null

            $arrayToJs = $pettyCash->addData($arr);

            if(!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']); 

        }

    }

    function deletePettyCash($rsHeader)
    {
        $pettyCash = new PettyCash();

        $rsKey = $pettyCash->getTableKeyAndObj($pettyCash->tableName,array('key'));

        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailWithRelatedInformation($id);    

        if(empty($rsDetail)) return;
        
        $arrKeys = array_column($rsDetail, 'pkey');
        $rsPettyCash = $pettyCash->searchDataRow(array(
            $pettyCash->tableName.'.pkey',
            $pettyCash->tableName.'.refkey',
            $pettyCash->tableName.'.reftablekey'
        ), ' and '. $pettyCash->tableName.'.reftablekey = '.$this->oDbCon->paramString($rsKey['key']). ' and '. $pettyCash->tableName.'.refkey in ('.$this->oDbCon->paramString($arrKeys,',').')');
        
        if(empty($rsPettyCash)) return;

        $arrPettyCashKey = array_column($rsPettyCash, 'pkey');
        
        $sql = '
            delete from
                '.$pettyCash->tableName.'
            where
                pkey in ('.$this->oDbCon->paramString($arrPettyCashKey,',').')
        ';
        $this->oDbCon->execute($sql);

    }
    
    
    function reCountGrandtotal($arrParam){

				$grandtotal = 0;
				$amount = 0;
				
				$arrCOAKey = $arrParam['hidCOAFromKey'];
				$arrAmount = $arrParam['amount']; 
				
				$arrARDetail = array();
				$aR = new AR();
				
				for ($i=0;$i<count($arrCOAKey);$i++){
					
				    $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
					if ( empty($arrCOAKey[$i]) || empty($arrAmount[$i]) )  
						continue;
					
					$amount += $this->unFormatNumber($arrAmount[$i]);
				} 
				
				$grandtotal = $amount; 

				$reCountResult = array();
				$reCountResult['grandtotal'] = $grandtotal; 
				
				return $reCountResult;
				
	}
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                coafrom.name as coafromname,
                coafrom.code as coafromcode,
                concat(coafrom.code, " - " ,coafrom.name ) as codenamefrom,
                coato.name as coatoname,
                coato.code as coatocode,
                concat(coato.code, " - " ,coato.name ) as codenameto
			  from
			  	'. $this->tableNameDetail .',
                '.$this->tableCOA.' coafrom,
                '.$this->tableCOA.' coato
			  where
			  	' . $this->tableNameDetail .'.coafromkey = coafrom.pkey and
			  	' . $this->tableNameDetail .'.coatokey = coato.pkey and
			  	'.$this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
        
        $sql .= $criteria;
		return $this->oDbCon->doQuery($sql);
    } 
	
	 function getTransactionDescription($arrKey,$userkey= ''){
                  
		 return array(); 
        
    }
     
    
}

?>
