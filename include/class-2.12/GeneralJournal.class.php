<?php

class GeneralJournal extends BaseClass{
	 
    function __construct(){
		
		parent::__construct(); 
		
		$this->tableName = 'general_journal_header';
		$this->tableNameDetail = 'general_journal_detail';
		$this->tableCOA = 'chart_of_account';
        $this->tableCOAAmount = 'chart_of_account_amount';
        $this->tableActivePeriode = 'chart_of_account_active_period';
		$this->tableStatus = 'transaction_status';  
        $this->tableCashBank = 'cash_bank';
        $this->tableWarehouse = 'warehouse';
        $this->tableCurrency = 'currency';
        $this->tableJournalBalancing = 'journal_balancing';
        $this->tableFile = 'general_journal_file';
		$this->uploadFileFolder = 'general-journal/'; 
        $this->isTransaction = true;
         
		$this->securityObject = 'GeneralJournal';
        $this->useStorage = $this->useStorage('S3');		
        
        
        // kalo nambah detail, jgn lupa di tambahin di swap di normalize !
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['refcashbankkey'] = array('refCashBankKey');
        $this->arrDataDetail['coakey'] = array('hidCOAKey',array('mandatory'=>true));
        $this->arrDataDetail['debit'] = array('debit','number');
        $this->arrDataDetail['credit'] = array('credit','number');
        $this->arrDataDetail['debitsource'] = array('debitSource','number');
        $this->arrDataDetail['creditsource'] = array('creditSource','number');
        $this->arrDataDetail['currencykey'] = array('selCurrencyKey');
        $this->arrDataDetail['rate'] = array('rate','number');
        $this->arrDataDetail['trdesc'] = array('trdescDetail');
        $this->arrDataDetail['refcode'] = array('refCodeDetail');
          
        
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
        $this->arrData['refkey'] = array('refkey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['reftabletype'] = array('refTableType');
        $this->arrData['refcode'] = array('refCode');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['totaldebit'] = array('totalDebit','number');
        $this->arrData['totalcredit'] = array('totalCredit','number'); 
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['cancelforperiod'] = array('cancelForPeriod'); 
        $this->arrData['reversefor'] = array('reverseFor'); 
        $this->arrData['annualclosingjournal'] = array('annualClosingJournal'); 
        $this->arrData['isbalancing'] = array('isbalancing'); 
        $this->arrData['monthlyclosingkey'] = array('monthlyClosingKey'); 
        $this->arrData['isreval'] = array('isReval'); 
//        $this->arrData['file'] = array('item-file-uploader',array('datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));
      
        
        $this->threshold = 1;
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouseName','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'reference','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 150 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdesc','title' => 'note','dbfield' => 'trdesc','default'=>true,'width' => 200  , 'align' => 'left'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'totaldebit', 'width' => 150, 'align' =>'right', 'format' => 'number' ));
        
  
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'print', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/generalJournal'));
               
        $this->includeClassDependencies(array(
              'ChartOfAccount.class.php',  
              'COALink.class.php',
              'CashOut.class.php',
              'CashIn.class.php',
              'Currency.class.php',
              'APPayment.class.php',
              'ARPayment.class.php',
              'Customer.class.php',
              'Supplier.class.php',
              'SalesOrder.class.php',
              'PurchaseOrder.class.php',
			  'EMKLJobOrder.class.php',
			  'EMKLOrderInvoice.class.php',
			  'EMKLPurchaseOrder.class.php',
              'CashAdvanceRealization.class.php'  
        )); 
        
        
        $this->overwriteConfig();
        
	}
	
	function getQuery(){ 
		$sql = '
			SELECT 
                '.$this->tableName.'.* ,
				'.$this->tableStatus.'.status as statusname,
                '.$this->tableWarehouse.'.name as warehousename
            FROM 
                '.$this->tableStatus.', 
                '.$this->tableName.',
                '.$this->tableWarehouse.' 
			WHERE 
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey
 	      ' .$this->criteria ; 
        
        return $sql;
	} 
       
	function editData($arrParam){ 
		unset( $this->arrData['reftabletype']);  
		unset( $this->arrData['refkey']);  
		unset( $this->arrData['refcode']);  
        return parent::editData($arrParam);
	}
         
	
	function validateForm($arr,$pkey = ''){ 
		   
		$arrayToJs = parent::validateForm($arr,$pkey);  
        
        /*
        // harusnya validasi ketika mau updateGL()
        if (!USE_GL){
          $this->addErrorList($arrayToJs,true,'');   
          return $arrayToJs;
        } */
    
		$chartOfAccount = new ChartOfAccount();   
		$arrCOAkey = $arr['hidCOAKey']; 
		$arrDebit = $arr['debit'];
		$arrCredit = $arr['credit'];
		$arrTotalDebit = $arr['totalDebit'];
		$arrTotalCredit = $arr['totalCredit'];
		
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
            
            if (!empty($rs[0]['refkey'])){ 
				$this->addErrorList($arrayToJs,false,$this->errorMsg[900]);
            }
            
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		} 
	  
 
		for($i=0;$i<count($arrCOAkey);$i++) {    
			if (empty($arrCOAkey[$i])){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['coa'][1]); 	
			}
		}
		     
        if( abs($arrTotalDebit - $arrTotalCredit) > $this->threshold) {   
			$this->addErrorList($arrayToJs,false,$this->errorMsg['generalJournal'][1]);
		}  
//         
		return $arrayToJs;
	 }
	 
    function afterUpdateData($arrParam, $action){    
        // hanya boleh kalo statusnya blm selesai
        //$rsHeader = $this->getDataRowById($arrParam['pkey']); 
        //if($rsHeader[0]['statuskey'] < 3)
        //    $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);
    }
    
	 		   
	function changeStatus($id,$status,$reason='',$copy=false,  $autoChangeStatus = false, $ignoreValidation = false){
		
		$arrayToJs = array();
		
		try{ 
                
                switch ($status){
                    case 1 : $arrayToJs = $this->validateInput($id);
                             if (!empty($arrayToJs)) 
                                    return $arrayToJs;
                            break;
                    case 2 : $arrayToJs = $this->validateConfirm($id);
                             if (!empty($arrayToJs)) 
                                    return $arrayToJs;
                            break;

                    case 3 : $arrayToJs = $this->validateClose($id);
                             if (!empty($arrayToJs)) 
                                    return $arrayToJs;
                            break;

                    case 4 : $arrayToJs = $this->validateCancel($id,$autoChangeStatus);
                         if (!empty($arrayToJs)) 
                                return $arrayToJs; 
                          break; 
                }  
           
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
		   
			switch ($status){ 
				//case 2 : $this->confirmTrans($id); break; 
				case 4 : $this->cancelTrans($id,$copy);
                          $this->afterCancelTrans($id);
                          break;  
			} 
			
            
			$sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id);
			$this->oDbCon->execute($sql);
			 
            $this->updateBalancing($id);
            
            // ====== UPDATE COA AMOUNT 
            
            $coa = new ChartOfAccount(); 
             
            $rsDetail = $this->getDetailById($id);
            for($i=0;$i<count($rsDetail);$i++)  
                $coa->updateCOAAmount($rsDetail[$i]['coakey']); 
            
            $sql = 'select 
                        distinct(rootkey) as pkey
                    from
                         '.$this->tableName.',
                         '.$this->tableNameDetail.',
                         '.$this->tableCOA.'
                    where
                        '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($id).' and
                        '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                        '.$this->tableNameDetail.'.coakey = '.$this->tableCOA.'.pkey  
                    ';
              
            $rs = $this->oDbCon->doQuery($sql);
            
            for($i=0;$i<count($rs);$i++)
                $coa->updateParentAmountFromRoot($rs[$i]['pkey']); 
              
            $coa->updateCurrentYearEarnings();  
            // ====== END OF UPDATE COA AMOUNT
            
             
            $rsStatus = $this->getStatusById($status); 
            $this->setTransactionLog($rsStatus[0]['pkey'],$id);
            
//            $this->afterStatusChanged($rsHeader);
            
			$this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
		
	    } catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage()); 
		}		
				 
 		return $arrayToJs; 
 	}

    function afterStatusChanged($rsHeader){  
        
    }
    
    function updateBalancing($id){ 
        
        // gk perlu, karena bisa saja adjustment karena  jurnal yg dicancel, dan sudah ad Pkeynya, sudah pasti cuma 1 jurnal
        // AND '.$this->tableName.'.statuskey IN (2,3) 
        
        
        $sql = 'SELECT 
                    '.$this->tableCOA.'.debittype,
                    '.$this->tableJournalBalancing.'.pkey,
                    '.$this->tableJournalBalancing.'.amount,
                    '.$this->tableJournalBalancing.'.coakey,
                    '.$this->tableJournalBalancing.'.coatokey,
                    '.$this->tableJournalBalancing.'.trdate
                FROM '.$this->tableName.' 
                INNER JOIN '.$this->tableNameDetail.' ON '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey 
                INNER JOIN '.$this->tableCOA.' ON '.$this->tableNameDetail.'.coakey = '.$this->tableCOA.'.pkey 
                INNER JOIN '.$this->tableJournalBalancing.' ON '.$this->tableCOA.'.pkey = '.$this->tableJournalBalancing.'.coakey 
                WHERE '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($id).' 
                    AND '.$this->tableJournalBalancing.'.statuskey IN (2,3)
                    AND '.$this->tableName.'.trdate < '.$this->tableJournalBalancing.'.trdate
                ORDER BY '.$this->tableJournalBalancing.'.trdate ASC';
         
        $rs = $this->oDbCon->doQuery($sql);
         
        $tablekey = $this->getTableKeyAndObj($this->tableJournalBalancing,array('key'))['key']; 
        foreach($rs as $row){
            $arrStartingBalance = $this->sumAccount($row['coakey'], '', $this->formatDBDate($row['trdate'], 'd / m / Y'));
            $balanceDiff = $row['amount'] - $arrStartingBalance['balance'];
            
            // Tentuin Debit Type 
            $debit = ($balanceDiff > 0) ? abs($balanceDiff) : 0; 
            $credit = ($balanceDiff <= 0) ? abs($balanceDiff) : 0;
             
            // akun counterny aharus diupdate jg
            
            // Update GL header n detail
            $sql = 'UPDATE '.$this->tableName.', '.$this->tableNameDetail.' 
                        SET '.$this->tableNameDetail.'.debit = '.$this->oDbCon->paramString($debit).','.$this->tableNameDetail.'.credit = '.$this->oDbCon->paramString($credit).' 
                    WHERE '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey 
                        AND '.$this->tableName.'.refkey = '.$this->oDbCon->paramString($row['pkey']).'
                        AND '.$this->tableName.'.reftabletype = '.$this->oDbCon->paramString($tablekey).' 
                        AND '.$this->tableNameDetail.'.coakey = '.$this->oDbCon->paramString($row['coakey']);
              
            $this->oDbCon->execute($sql);
            
            
            // update counter
            $totaldebit = 0;
            if($debit != 0){
                $credit  = $debit;
                $totaldebit = $credit;
                $debit = 0;
            }else{
                $debit = $credit;
                $totaldebit = $debit;
                $credit = 0;
            }
                
            $sql = 'UPDATE '.$this->tableName.', '.$this->tableNameDetail.' 
                    SET '.$this->tableNameDetail.'.debit = '.$this->oDbCon->paramString($debit).','.$this->tableNameDetail.'.credit = '.$this->oDbCon->paramString($credit).',
                        '.$this->tableName.'.totaldebit = '.$this->oDbCon->paramString($totaldebit).','.$this->tableName.'.totalcredit = '.$this->oDbCon->paramString($totaldebit).'
                    WHERE '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey 
                        AND '.$this->tableName.'.refkey = '.$this->oDbCon->paramString($row['pkey']).'
                        AND '.$this->tableName.'.reftabletype = '.$this->oDbCon->paramString($tablekey).' 
                        AND '.$this->tableNameDetail.'.coakey = '.$this->oDbCon->paramString($row['coatokey']);
              
            $this->oDbCon->execute($sql);
        }
    }
    
    function cancelTrans($id,$copy){ 
 
		if ($copy)
			$this->copyDataOnCancel($id);	  
		   
	}  
    
	function validateClose($id){
		
		$rs = $this->getDataRowById($id);
    
		$arrayToJs = array(); 
         
        // status hanya berubah menjadi selesai ketika closing
        $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[212]);
        return $arrayToJs;

	 	return $arrayToJs;
	 }		
    
	function validateInput($id){
		
		$rs = $this->getDataRowById($id);
    
		$arrayToJs = array(); 
        
        
        $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[212]);
        return $arrayToJs;

        /*
		if($rs[0]['statuskey'] == 3){  
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg['generalJournal'][5]);
            return $arrayToJs;
		}  
		 
        if(!empty($rs[0]['refkey'])){  
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg['generalJournal'][4]);
            return $arrayToJs;
		} */ 
        
	 	return $arrayToJs;
	 }		
    
	function validateConfirm($id){ 
		$rs = $this->getDataRowById($id); 
        
		$arrayToJs = array(); 
        
        // gk boleh konfirmasi jurnal di periode yg sudah closing 
        $coa = new ChartOfAccount();
        $rsRunningPeriod = $coa->rsRunningPeriod;
        $runningDate =  $this->formatDBDate($rsRunningPeriod[0]['runningmonth'],'Ym01'); 
         
        $trdate =  $this->formatDBDate($rs[0]['trdate'],'Ymd'); 
        
        
		if($rs[0]['statuskey'] == 3){  
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg['generalJournal'][5]);
            return $arrayToJs;
		}   
       
        if ($trdate < $runningDate){
            $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg['generalJournal'][6]);
        } 
        
		if($rs[0]['statuskey'] <> 1){  
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[203]);
		} 
		 
        // kalo konfirmasi, harus sama
        if( abs($rs[0]['totaldebit'] - $rs[0]['totalcredit']) > 0) {   
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg['generalJournal'][1]);
		} 
        
        
	 	return $arrayToJs;
	 }		

    /*
    function isInCurrentRunningPeriod($rs){
        $month = (int) $this->formatDBDate($rs[0]['trdate'],'m');
        $year = (int) $this->formatDBDate($rs[0]['trdate'],'Y');
        
        $coa = new ChartOfAccount();
        $rsRunningPeriod = $coa->rsRunningPeriod;
        $runningPeriodMonth = (int) $this->formatDBDate($rsRunningPeriod[0]['runningmonth'],'m');
        $runningPeriodYear = (int) $this->formatDBDate($rsRunningPeriod[0]['runningmonth'],'Y');
        
        if ($month == $runningPeriodMonth && $year == $runningPeriodYear)
            return true;
        
        return false;
        
    }
     
    
    function sumGroupAccount($parentkey, $endDate=''){
		  
		$criteria = '';
	 
		if (!empty($endDate)){
			$criteria .= ' and trdate < '.$this->oDbCon->paramDate($endDate,' / ');
		}
		$sql = 'select 
                  coalesce(sum(debit-credit),0) as amount
               from
                   '.$this->tableName.', '.$this->tableNameDetail.', '.$this->tableCOA.'
               where 
                    '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and 
                    '.$this->tableNameDetail.'.coakey = '.$this->tableCOA.'.pkey and
                    '.$this->tableCOA.'.statuskey = 1 and 
                    ('.$this->tableName.'.statuskey = 2 or '.$this->tableName.'.statuskey = 3) and 
                    '.$this->tableCOA.'.parentkey = '.$this->oDbCon->paramString($parentkey) . $criteria;		 
        
		$rs =  $this->oDbCon->doQuery($sql); 
	 	
        
        $coa = new ChartOfAccount();
        $rsCOA =  $coa->getDataRowById($parentkey); 
	 	return $rs[0]['amount'] * $rsCOA[0]['debittype'];
	}
    */
    
    /*
    function deleteByRefkey($refkey){
       $rs = $this->searchData('refkey',$refkey,true);   
       if (!empty($rs))
            $this->delete($rs[0]['pkey']);
    } 
    */
    
    function validateCancel($id,$autoChangeStatus=false){
        
        $rs = $this->getDataRowById($id);
		
        $arrayToJs = array();    
        
        if($rs[0]['statuskey'] == 3){  
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg['generalJournal'][5]);
		} else if(!$autoChangeStatus && !empty($rs[0]['refkey'])){  
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg['generalJournal'][4]);
		}  else if ($rs[0]['statuskey'] != 2 && $rs[0]['statuskey'] != 1 ){ 
            $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201]);
        }
        
		return $arrayToJs; 
	 } 
	  
    function reCountSubtotal($arrParam){

    			$decimal = 0;
		        $decimalNumber = $this->loadSetting('decimalTransaction');
		          
	          	if (!empty($decimalNumber))
	              	$decimal = $decimalNumber;

                $arrCOAkey = $arrParam['hidCOAKey']; 
                $arrDebit = $arrParam['debit']; 
                $arrCredit = $arrParam['credit']; 
      
				$totaldebit = 0;
				$totalcredit = 0;
				for ($i=0;$i<count($arrCOAkey);$i++){
				    
                    $coa = new ChartOfAccount();
                    $rsCOA = $coa->getDataRowById($arrCOAkey[$i] ); 
                     
					if (empty($arrCOAkey[$i]))  
						continue;
                     
                    $arrDebit[$i]  = $this->unFormatNumber($arrDebit[$i]);
                    $arrCredit[$i] = $this->unFormatNumber($arrCredit[$i]);
                    
                    $totaldebit += $arrDebit[$i];
                    $totalcredit += $arrCredit[$i];   
				} 
				 
        
                $totaldebit =  round($totaldebit,$decimal);
                $totalcredit =  round($totalcredit,$decimal);
        
				$reCountResult = array();
				$reCountResult['debit'] =  $arrDebit;
				$reCountResult['credit'] =   $arrCredit;
				$reCountResult['totalDebit'] =  $totaldebit;
				$reCountResult['totalCredit'] =   $totalcredit;
				
				return $reCountResult;
				
	}
	
    function getJournalForGL($coakey,$startDate='',$endDate=''){
        
        /*
        $dateMethod = $this->loadSetting('movementDateMethod');
        $datefield = 'createdon';
        if ($dateMethod == 2) 
            $datefield = 'trdate';
        */
        
        $cashOut = new CashOut();
        $cashIn = new CashIn();
        $apPayment = new APPayment(); 
        $arPayment = new ARPayment();
        $customer = new Customer();
        $supplier = new Supplier(); 
        $salesOrder = new SalesOrder();
        $purchaseOrder = new PurchaseOrder();
        
        $datefield = 'trdate'; 
        
        $criteria = '';
        if (!empty($startDate)) 
			$criteria .= ' and trdate >= '.$this->oDbCon->paramDate($startDate,' / ');
		 
        if (!empty($endDate))
			$criteria .= ' and trdate <= '.$this->oDbCon->paramDate($endDate,' / ');
		 
        // harus pake and annualclosingjournal = 0
        // kalo gk nanti gk sama dengan income statement
        $sql = 'select 
                    '.$this->tableName.'.pkey,
                    '.$this->tableName.'.code,
                    '.$this->tableName.'.trdate,
                    '.$this->tableName.'.createdon, 
                    '.$this->tableName.'.refcode, 
                    '.$this->tableName.'.trdesc as headerdesc, 
                    '.$this->tableName.'.refkey,  
                    '.$this->tableName.'.reftabletype, 
                    '.$this->tableName.'.isbalancing, 
                    '.$this->tableNameDetail.'.pkey as detailkey,
                    '.$this->tableNameDetail.'.coakey,
                    '.$this->tableNameDetail.'.debitsource,
                    '.$this->tableNameDetail.'.creditsource,
                    '.$this->tableNameDetail.'.rate,
                    '.$this->tableNameDetail.'.currencykey,
                    '.$this->tableNameDetail.'.debit,
                    '.$this->tableNameDetail.'.credit ,
                    '.$this->tableNameDetail.'.trdesc ,
                    '.$this->tableNameDetail.'.refcode as detailrefcode,
                    '.$this->tableCurrency.'.name as currencyname,
                    '.$this->tableWarehouse.'.name as warehousename
                from 
                    '.$this->tableName.' 
                        left join '.$this->tableWarehouse.' on '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey, 
                    '.$this->tableNameDetail.'
                        left join '.$this->tableCurrency.' on '.$this->tableNameDetail.'.currencykey = '.$this->tableCurrency.'.pkey
                where
                    ('.$this->tableName.'.statuskey = 2 or '.$this->tableName.'.statuskey = 3 ) and
                    '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableNameDetail.'.coakey in ('.$this->oDbCon->paramString($coakey,',').' )
                    '.$criteria.'
                order by '.$datefield.' asc, isbalancing desc,'.$this->tableName.'.code asc
                '; 
        
        // and annualclosingjournal = 0
        
        //$this->setLog($sql,true);
        $rs = $this->oDbCon->doQuery($sql);
        
      /*  // update cust / supplier name
        for($i=0;$i<count($rs);$i++){
            switch ($rs[$i]['reftable']) {
                case 'cash_out_header':
                    $rsRef = $cashOut->getDataRowById($rs[$i]['refkey']);
                    $recipientName = $rsRef[0]['recipientname'];
                    break;
                
                case 'cash_in_header':
                    $rsRef = $cashIn->getDataRowById($rs[$i]['refkey']);
                    $recipientName = $rsRef[0]['recipientname'];
                    break;
                
                case 'ar_payment_header':
                    $rsRef = $arPayment->getDataRowById($rs[$i]['refkey']);
                    $rsRef = $customer->getDataRowById($rsRef[0]['customerkey']);
                    $recipientName = $rsRef[0]['name'];
                    break;
                
                case 'ap_payment_header':
                    $rsRef = $apPayment->getDataRowById($rs[$i]['refkey']);
                    $rsRef = $supplier->getDataRowById($rsRef[0]['supplierkey']);
                    $recipientName = $rsRef[0]['name'];
                    break;
                    
                case 'sales_order_header':
                    $rsRef = $salesOrder->getDataRowById($rs[$i]['refkey']);
                    $rsRef = $customer->getDataRowById($rsRef[0]['customerkey']);
                    $recipientName = $rsRef[0]['name'];
                    break;
                    
                case 'purchase_order_header':
                    $rsRef = $purchaseOrder->getDataRowById($rs[$i]['refkey']);
                    $rsRef = $supplier->getDataRowById($rsRef[0]['supplierkey']);
                    $recipientName = $rsRef[0]['name'];
                    break;
                     
                    
                default: 
                    $recipientName = '';
            }
            
            $rs[$i]['recipientname'] = $recipientName;
        }
        */
        
        return $rs;
    }
	
    /*function sumDebitAndCredit($coakey,$startDate='', $endDate='', $group = false){
		  
        // kedepannya coba ambil dr closingkan masing2 coa amount kalo sudah ada
        
		$criteria = '';
	 
		if (!empty($endDate))  $criteria .= ' and trdate < '.$this->oDbCon->paramDate($endDate,' / '); 
		if (!empty($startDate))  $criteria .= ' and trdate >= '.$this->oDbCon->paramDate($startDate,' / '); 
		  
		$sql = 'select 
                  coalesce(sum(debit-credit),0) as amount,
                  coakey
               from
                   '.$this->tableName.', '.$this->tableNameDetail.'
               where 
                    '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and 
                     ('.$this->tableName.'.statuskey in(2,3)) and 
                    coakey in ('.$this->oDbCon->paramString($coakey,',').')'. $criteria.'
                ';		 
        
        if(is_array($coakey))
            $sql = ' group by coakey '; 
        
		$rs =  $this->oDbCon->doQuery($sql);	  
         
	 	return (!is_array($coakey)) ? $rs[0]['amount'] : array_column($rs,'amount','coakey');  
	}*/
    
    
    function sumAccount($coakey, $startDate='', $endDate='', $groupByDate = false){
		  
        // fungsi ini untuk ambil saldo tgl sebelumnya
        
        // kedepannya coba ambil dr closingkan masing2 coa amount kalo sudah ada
        
		$criteria = '';
	 
		if (!empty($endDate))  $criteria .= ' and trdate < '.$this->oDbCon->paramDate($endDate,' / '); // jgn pake 23:59:59 karena buat ambil saldo tgl sebelumnya
		if (!empty($startDate))  $criteria .= ' and trdate >= '.$this->oDbCon->paramDate($startDate,' / '); 
		  
		$sql = 'select 
                  DATE_FORMAT(trdate, "%c-%Y") as dateindex, 
                  coalesce(sum(debit-credit),0) as balance,
                  coalesce(sum(debit),0) as debit,
                  coalesce(sum(credit),0) as credit,
                  coakey
               from
                   '.$this->tableName.', '.$this->tableNameDetail.'
               where 
                    '.$this->tableNameDetail.'.refkey = '.$this->tableName.'.pkey and 
                     ('.$this->tableName.'.statuskey in(2,3)) and 
                    coakey in ('.$this->oDbCon->paramString($coakey,',').')'. $criteria.'
                    ';		 
        
        $arrGroup = array();
        
        if(is_array($coakey)) array_push($arrGroup,'coakey'); 
        if($groupByDate) array_push($arrGroup,'year(trdate), month(trdate)'); 
        
        if(!empty($arrGroup)) 
            $sql .= ' group by ' . implode(',',$arrGroup);
		
		$rs =  $this->oDbCon->doQuery($sql);
        
         
      /*  if($groupByDate){ 
            return $this->reindexDetailCollections($rs,'coakey');  
        }else{ 
	 	     return (!is_array($coakey)) ? $rs[0] : array_column($rs,null,'coakey'); 
        }*/
        
        return (!is_array($coakey)) ? $rs[0] : $this->reindexDetailCollections($rs,'coakey');  
	}
    
    function normalizeParameter($arrParam, $trim = false){
        $coaLink = new COALink();
        $warehouse = new Warehouse();
        
        // harusnya sesuai dengan warehosue transaksi
//        if(!isset($arrParam['selWarehouseKey']))
//            $this->setLog($arrParam['refTableType'].', '.$arrParam['refkey'],true,'gl');
            
        $warehousekey = (isset($arrParam['selWarehouseKey']) && !empty($arrParam['selWarehouseKey']) ) ? $arrParam['selWarehouseKey'] : $warehouse->getDefaultData();  
        $arrParam['selWarehouseKey'] = $warehousekey; // set nilai default
        
        $isAutoJournal = (!empty( $arrParam['refkey']) && !empty($arrParam['refTableType'])) ? true : false;
        

		$arrCOAkey = $arrParam['hidCOAKey']; 
        
        // kalo user bisa input source
        if(isset($arrParam['debitSource'])){
           // rapiin rate yg 0 dulu 

            for ($i=0;$i<count($arrCOAkey);$i++){

                if ($arrParam['rate'][$i] <=0){
                    $arrParam['selCurrencyKey'][$i] = CURRENCY['idr'];
                    $arrParam['rate'][$i] = 1;
                    $arrParam['debit'][$i] =$arrParam['debitSource'][$i];
                    $arrParam['credit'][$i] =$arrParam['creditSource'][$i];
                }
            } 
            
        }else{
            // kalo gk, dibalik
            $arrParam['debitSource'] = array();
            $arrParam['creditSource'] = array();
            for ($i=0;$i<count($arrCOAkey);$i++){ 
                $arrParam['selCurrencyKey'][$i] = CURRENCY['idr'];
                $arrParam['rate'][$i] = 1;
                $arrParam['debitSource'][$i] =$arrParam['debit'][$i];
                $arrParam['creditSource'][$i] =$arrParam['credit'][$i]; 
            } 
            
        }
	
        
        // otomatis ambil kode transaksi
        // karena ada refkey, baru boleh autojurnal 
        if ($isAutoJournal) {
            
            $rsTable = $this->getTableNameAndObjById($arrParam['refTableType']);
            
            // ambil kode referensi dr transaksi
            $sql = 'select code from `'.$rsTable['tableName'].'` where pkey  = '. $this->oDbCon->paramString($arrParam['refkey']); 
            $rsRef = $this->oDbCon->doQuery($sql);

            if (!empty($rsRef))
             $arrParam['refCode'] = $rsRef[0]['code'];
            
            if($this->isActiveModule('CashBank') && isset($arrParam['refCashBankKey'])){  
                $arrParam['refCodeDetail'] = array();
                foreach ($arrParam['refCashBankKey'] as $key=>$cashBankRow){ 
                        $sql = 'select code from '.$this->tableCashBank.'  where pkey  = '. $this->oDbCon->paramString($cashBankRow); 
                        //$this->setLog($sql,true);
                        $rsRef = $this->oDbCon->doQuery($sql);
                        $arrParam['refCodeDetail'][$key] = (!empty($rsRef[0]['code'])) ? $rsRef[0]['code'] : '';
                        //$this->setLog(' $arrParam[refCodeDetail]['.$key.'] = '.$rsRef[0]['code'] ,true);
                }
            }
            
            $autoConfirm = $this->loadSetting('autoConfirmAutoJournal');
            if ($autoConfirm == 1)
               $arrParam['hidSaveAndProceed'] = 1;
                
        }   
        
  
		$arrDebit = $arrParam['debit'];
		$arrCredit = $arrParam['credit'];
        $arrDebitSource = $arrParam['debitSource'];
		$arrCreditSource = $arrParam['creditSource'];  
   
        $arrParam['totalDebit'] = 0 ;
        $arrParam['totalCredit'] = 0 ;
        
        if (!isset($arrParam['hidDetailKey'])){
              for ($i=0;$i<count($arrCOAkey);$i++)
                  $arrParam['hidDetailKey'][$i] = 0;
        }
        
        for ($i=0;$i<count($arrCOAkey);$i++){
			   
		 /*	$debit =  round($this->unFormatNumber($arrDebit[$i]));
			$credit =  round($this->unFormatNumber($arrCredit[$i]));*/
            	
            $debit =  $this->unFormatNumber($arrDebit[$i]) ;
			$credit =  $this->unFormatNumber($arrCredit[$i]) ;
            $debitSource =  $this->unFormatNumber($arrDebitSource[$i]) ;
			$creditSource =  $this->unFormatNumber($arrCreditSource[$i]) ;
            
            if ($debit < 0){
                $credit = abs($debit);
                $debit = 0;
            }
                
            if ($credit < 0){
                $debit = abs($credit);
                $credit = 0;
            }

            
            if ($debitSource < 0){
                $creditSource = abs($debitSource);
                $debitSource = 0;
            }
                
            if ($creditSource < 0){
                $debitSource = abs($creditSource);
                $creditSource = 0;
            }

            $arrParam['debit'][$i] = $debit;
            $arrParam['credit'][$i] = $credit;
            $arrParam['debitSource'][$i] = $debitSource;
            $arrParam['creditSource'][$i] = $creditSource;    
            
            $arrParam['totalDebit'] += $debit;
            $arrParam['totalCredit'] += $credit;
            
            $arrParam['trdescDetail'][$i] = (isset($arrParam['trdescDetail'][$i])) ? $arrParam['trdescDetail'][$i] : '';
            $arrParam['refCodeDetail'][$i] = (isset($arrParam['refCodeDetail'][$i])) ? $arrParam['refCodeDetail'][$i] : '';
               
            if ($debit == 0 && $credit == 0) {  
                foreach($this->arrDataDetail as $el){
                   if (isset($arrParam[$el[0]]) && is_array($arrParam[$el[0]]))  
                       unset($arrParam[$el[0]][$i]);  
                } 
            }
            
        }
        
        //reasign index
        foreach($this->arrDataDetail as $el){
           if (isset($arrParam[$el[0]]) && is_array($arrParam[$el[0]])) 
              $arrParam[$el[0]] = array_values($arrParam[$el[0]]);   
        }
        
        
        if($isAutoJournal){
            // FRACTION ADJUSTMENT 
            $balance = $arrParam['totalDebit'] - $arrParam['totalCredit']; 
            if($balance <> 0 ){ 

                array_push($arrParam['hidDetailKey'],0); 
                array_push($arrParam['rate'],1);
                array_push($arrParam['trdescDetail'], $this->lang['adjustment']);
                array_push($arrParam['refCodeDetail'],'');

                if ($balance < 0){ 
                    $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
                    array_push($arrParam['debit'],abs($balance)); 
                    array_push($arrParam['debitSource'],abs($balance)); 
                    array_push($arrParam['credit'],0); 
                    array_push($arrParam['creditSource'],0); 
                    $arrParam['totalDebit'] += abs($balance);
                }else{ 
                    $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
                    array_push($arrParam['debit'],0); 
                    array_push($arrParam['debitSource'],0); 
                    array_push($arrParam['credit'],abs($balance));
                    array_push($arrParam['creditSource'],abs($balance));   
                    $arrParam['totalCredit'] += abs($balance);
                }

				array_push($arrParam['selCurrencyKey'],CURRENCY['idr']);  
				array_push($arrParam['rate'],1);  
                array_push($arrParam['hidCOAKey'],$rsCOA[0]['coakey']); 
            }
        }
       
         
        // group by debit credit
        //$this->setLog('Total ' . count($arrParam['hidDetailKey']));
        for($i=0;$i<count($arrParam['hidDetailKey']);$i++){
            if ($arrParam['debit'][$i] == 0){
                //$this->setLog('==>' . $arrParam['debit'][$i] . ' - ' .$arrParam['credit'][$i]);
                //start searching for swaping
                for($j=$i+1;$j<count($arrParam['hidDetailKey']);$j++){
                    if ($arrParam['debit'][$j] != 0){
                        //$this->setLog('swap ' . $arrParam['debit'][$i] . ' - ' . $arrParam['credit'][$i] . ' dgn ' .  $arrParam['debit'][$j] . ' - ' . $arrParam['credit'][$j]);
                        
                        $this->swap($arrParam['refCashBankKey'][$i], $arrParam['refCashBankKey'][$j]);
                        $this->swap($arrParam['hidCOAKey'][$i], $arrParam['hidCOAKey'][$j]);
                        $this->swap($arrParam['debit'][$i], $arrParam['debit'][$j]);
                        $this->swap($arrParam['credit'][$i], $arrParam['credit'][$j]);
                        $this->swap($arrParam['debitSource'][$i], $arrParam['debitSource'][$j]);
                        $this->swap($arrParam['creditSource'][$i], $arrParam['creditSource'][$j]);
                        $this->swap($arrParam['selCurrencyKey'][$i], $arrParam['selCurrencyKey'][$j]);
                        $this->swap($arrParam['rate'][$i], $arrParam['rate'][$j]);
                        $this->swap($arrParam['trdescDetail'][$i], $arrParam['trdescDetail'][$j]);
                        $this->swap($arrParam['refCodeDetail'][$i], $arrParam['refCodeDetail'][$j]);  
              
                        break;
                    }
                }
            }
        }
        
        // group sum
        // HATI2, kalo simpen detailkey / ref cash bank key, akan tidak relevan
        if( empty($arrParam['_mnv_ungroup']) && $this->loadSetting('sumGroupGL')){ 
            $arrCOAKey = array();
            $arrHidDetailKey = array();
            $arrDebit = array();
            $arrCredit = array();
            $arrDebitSource = array();
            $arrCreditSource = array();
            $arrCurrencyKey = array();
            $arrRate = array();
            $arrDesc = array();
            $arrRefCode = array();
            for($i=0;$i<count($arrParam['hidDetailKey']);$i++){
                $coakey = $arrParam['hidCOAKey'][$i]; 
                $indexKey = array_search($coakey, $arrCOAKey);

                if ($indexKey === false) { // harus pake === karena index 0 bisa dianggap false
                    array_push($arrCOAKey,$coakey); 
                    array_push($arrHidDetailKey,0); 
                    array_push($arrDebit,$arrParam['debit'][$i]);
                    array_push($arrCredit,$arrParam['credit'][$i]); 
                    array_push($arrDebitSource,$arrParam['debitSource'][$i]);
                    array_push($arrCreditSource,$arrParam['creditSource'][$i]); 
                    array_push($arrCurrencyKey,$arrParam['selCurrencyKey'][$i]); 
                    array_push($arrRate,$arrParam['rate'][$i]); 
                    array_push($arrDesc,$arrParam['trdescDetail'][$i]); 
                    array_push($arrRefCode,$arrParam['refCodeDetail'][$i]);  
                }else{
                    $arrDebit[$indexKey] += $arrParam['debit'][$i];
                    $arrCredit[$indexKey] += $arrParam['credit'][$i]; 
                    $arrDebitSource[$indexKey] += $arrParam['debitSource'][$i];
                    $arrCreditSource[$indexKey] += $arrParam['creditSource'][$i]; 
                    $arrDesc[$indexKey] .= $arrParam['trdescDetail'][$i];  
                }

            }
            
            
             for($i=0;$i<count($arrParam['hidDetailKey']);$i++){         
                $sumTemp = $arrDebit[$i] - $arrCredit[$i];
                 
                // kalo pas 0
                if($sumTemp == 0){
                    unset($arrHidDetailKey[$i]);
                    $arrHidDetailKey = array_values($arrHidDetailKey);  
                    
                    unset($arrCOAKey[$i]);
                    $arrCOAKey = array_values($arrCOAKey);  
                    
                    unset($arrDebit[$i]);
                    $arrDebit = array_values($arrDebit);  
                    
                    unset($arrCredit[$i]);
                    $arrCredit = array_values($arrCredit);  
                    
                    unset($arrDebitSource[$i]);
                    $arrDebitSource = array_values($arrDebitSource);  
                    
                    unset($arrCreditSource[$i]);
                    $arrCreditSource = array_values($arrCreditSource); 
                    
                    unset($arrCurrencyKey[$i]);
                    $arrCurrencyKey = array_values($arrCurrencyKey);  
                    
                     unset($arrRate[$i]);
                    $arrRate = array_values($arrRate);  
                    unset($arrDesc[$i]);
                    $arrDesc = array_values($arrDesc);  
                    
                    unset($arrRefCode[$i]);
                    $arrRefCode = array_values($arrRefCode);  
                    
                    continue;
                } 
                 
                if($sumTemp > 0){
                    $arrDebit[$i] = abs($sumTemp);
                    $arrCredit[$i] = 0;
                }else{ 
                    $arrDebit[$i] = 0;
                    $arrCredit[$i] = abs($sumTemp);
                }

             }
             
            
            $arrParam['hidDetailKey'] = $arrHidDetailKey;
            $arrParam['hidCOAKey'] = $arrCOAKey;
            $arrParam['debit'] = $arrDebit;
            $arrParam['credit'] = $arrCredit;
            $arrParam['debitSource'] = $arrDebitSource;
            $arrParam['creditSource'] = $arrCreditSource;
            $arrParam['selCurrencyKey'] = $arrCurrencyKey;
            $arrParam['rate'] = $arrRate;
            $arrParam['trdescDetail'] = $arrDesc;
            $arrParam['refCodeDetail'] = $arrRefCode;
        }
         
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    } 
    
    function getDetailWithRelatedInformation($pkey, $criteria = ''){
        
         $sql = 'select
	   			'.$this->tableNameDetail .'.*,  
                '.$this->tableCOA.'.code as coacode, 
                '.$this->tableCOA.'.name as coaname,
				concat('.$this->tableCOA. '.code," - ",'.$this->tableCOA.'.name) as coacodename ,
                '.$this->tableCurrency.'.name as currencyname
			  from
			  	'.$this->tableNameDetail .'
                    left join '.$this->tableCurrency.' on '.$this->tableNameDetail .'.currencykey = '.$this->tableCurrency.'.pkey ,  
                '.$this->tableCOA.'  
			  where 
			  	'.$this->tableNameDetail .'.coakey = '.$this->tableCOA.'.pkey and 
			  	refkey in ('.$this->oDbCon->paramString($pkey,',').')';
        
       
        $sql .= $criteria;
        
        $sql .= ' order by pkey asc';
        
		return $this->oDbCon->doQuery($sql);
    }
    
     function getReportDescription($rs){ 
            
		 
        $transactionDescPattern = '{{TRANSACTION_DESCRIPTION}}';
         
        $tablekey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];   
		$rsSettings = $this->getReportSettings($tablekey);  
         
		 
//	    $this->setLog($this->tableName,true);
		 
        if(empty($rsSettings)) return;
        
        $reportPattern = $rsSettings[0]['value']; 
         
         // index masih refkey
         // untuk setiap transaksi, perlu query ke masing2 obj
        $arrCustomDesc = array();
        if (strpos($reportPattern,$transactionDescPattern) !== false){
            
            // split berdasarkan table
            $arrTableKey = array_values(array_unique(array_column($rs,'reftabletype'))); 
            $rsCol = $this->reindexDetailCollections($rs,'reftabletype');
 
            foreach($arrTableKey as $tablekey){
				if (empty($tablekey)) continue; // untuk yg jurnal umum langsung
				
				$arrCustomDesc[$tablekey] = array();
				
                $rsBasedOnTableKey = $rsCol[$tablekey];
  
                $obj = $this->getObjMapping('',$tablekey);
				  
//                $pkey =  array_unique(array_column($rsBasedOnTableKey,'refkey'));   
//                $rsTemp = $obj->getTransactionDescription($pkey);

				// kayanya harus di set agar setiap modul bisa beda2, 
				// misalnya berdasarkan detailkey, atau detail coakey 
				
				$arrKey = array();
				
				switch($obj->tableName){
						// sementara tembak langsung
//					case 'cash_bank_transfer_header' :
//								foreach($rsBasedOnTableKey as $keyRow) 
//									 array_push($arrKey, array('pkey' => $keyRow['refkey'] , 'coakey' =>  $keyRow['coakey'] ));
//							
//								break;
					default : 
								  foreach($rsBasedOnTableKey as $keyRow) {  
                                     $detailkey =  (isset($keyRow['detailkey'])) ? $keyRow['detailkey'] : 0;
                                     array_push($arrKey, array('pkey' => $keyRow['refkey'] , 'detailkey' =>  $detailkey ));
                                  }
									 
								  break;

				}
				
				$rsTemp = $obj->getTransactionDescription($arrKey);
				
				// lupa dulu kenapa array_merge gk bisa, ad hubungannya sama index,
				// kayanya kalo merge, indexnya hilang (reindex)

                if (!empty($rsTemp))
                    $arrCustomDesc[$tablekey] += $rsTemp; 
            }

        } 
          
         
        // yg boleh diakses
        $arrAvailableField = array(  
                                array('code' => 'itemdesc', 'param' => 'ITEM_DESCRIPTION','value' => 'trdesc'), 
                                array('code' => 'headerdesc', 'param' => 'DESCRIPTION','value' => 'headerdesc'), 
                                array('code' => 'transdesc', 'param' => 'TRANSACTION_DESCRIPTION'), 
        );

        $usedParam = array(); 
        foreach($arrAvailableField as $row) 
         if(strpos($reportPattern, '{{'.$row['param'].'}}') !== false) 
             array_push($usedParam,$row); 

         
         // loop setiap jurnal
           
         $totalRs = count($rs);
         $returnArr = array();
         for($i=0;$i<$totalRs;$i++){ 
             
            $transDesc = (isset($arrCustomDesc[$rs[$i]['reftabletype']][$rs[$i]['refkey']])) ? $arrCustomDesc[$rs[$i]['reftabletype']][$rs[$i]['refkey']] : '';
             
            $arrNeedle = array(PHP_EOL); 
            $arrReplacement = array('<br>');

            foreach($usedParam as $row){   
                
                switch ($row['code']){
                    case 'transdesc':   $replacement = $transDesc;
                                        break;
                    default : $replacement = $rs[$i][ $row['value'] ];
                        
                }
                
                array_push ($arrNeedle,'{{'.$row['param'].'}}');
                array_push ($arrReplacement,$replacement); 
            }

            $returnArr[$rs[$i]['pkey']] = str_replace($arrNeedle,$arrReplacement,$reportPattern); 
             
         }
		 
        return $returnArr;
         
    }

    function generateGeneralJournalDetailReport($criteria = '', $order)
    {

        $sql = '
            select
                '. $this->tableNameDetail .'.*,
                '. $this->tableName .'.code,
                '. $this->tableName .'.trdate,
                '. $this->tableName .'.statuskey,
                '. $this->tableName .'.warehousekey,
                '. $this->tableName .'.refkey,
                '. $this->tableName .'.reftabletype,
                '. $this->tableName .'.refcode,
                '. $this->tableWarehouse .'.name as warehousename,
                '. $this->tableStatus .'.status as statusname,
                ' . $this->tableCOA . '.code as coacode, 
                ' . $this->tableCOA . '.name as coaname,
                ' . $this->tableCurrency . '.name as currencyname,
				concat(' . $this->tableCOA . '.code," - ",' . $this->tableCOA . '.name) as coacodename  
            from
                '. $this->tableNameDetail .'
                    left join '. $this->tableCOA .' on '. $this->tableNameDetail .'.coakey = ' . $this->tableCOA . '.pkey
                    left join '.$this->tableCurrency.' on '.$this->tableNameDetail .'.currencykey = '.$this->tableCurrency.'.pkey ,   
                '. $this->tableName .',
                '. $this->tableStatus .',
                '. $this->tableWarehouse .'
            where
                '. $this->tableNameDetail .'.refkey = '. $this->tableName .'.pkey and
                '. $this->tableName .'.warehousekey = '. $this->tableWarehouse .'.pkey and
                '. $this->tableName .'.statuskey = '. $this->tableStatus .'.pkey
        ';

        $sql .= ' ' .$criteria; 

        $sql .= ' ' .$order;

        $result = $this->oDbCon->doQuery($sql);

        return $result;

    }
    
    function isEmptyTrans($arr){ 
        foreach($arr['debit'] as $row) 
            if ($row <> 0) return false; 
        
        
        foreach($arr['credit'] as $row) 
            if ($row <> 0) return false; 
        
        return true;
    }

    function generateDailyCashStatementReport($criteria = '', $order = '')
    {
        $sql = '
            select 
                '. $this->tableNameDetail .'.pkey,
                '. $this->tableNameDetail .'.refkey,
                '. $this->tableNameDetail .'.coakey,
                '. $this->tableNameDetail .'.debit,
                '. $this->tableNameDetail .'.credit,
                '. $this->tableNameDetail .'.currencykey,
                '. $this->tableNameDetail .'.trdesc,
                '. $this->tableCOA .'.code as coacode, 
                '. $this->tableCOA .'.name as coaname,
                '. $this->tableName .'.pkey as glkey,
                '. $this->tableName .'.code,
                '. $this->tableName .'.trdate,
                '. $this->tableName .'.refcode ,
                '. $this->tableName .'.trdesc as headerdesc 
            from
                '. $this->tableNameDetail .'
                    left join '. $this->tableCOA .' on '. $this->tableNameDetail .'.coakey = ' . $this->tableCOA . '.pkey,
                '. $this->tableName .'
            where
                '. $this->tableNameDetail .'.refkey = '. $this->tableName .'.pkey and
                '. $this->tableName .'.statuskey in (2,3)
        ';

        if($criteria != '') {
            $sql .= ' ' .$criteria; 
        }
        
        if($order != '') {
            $sql .= $order;
        }

        $result = $this->oDbCon->doQuery($sql);
        
        return $result;
    }
    
     function getItemFile($pkey){
		$sql = 'select * from '.$this->tableFile.' where refkey = '.$this->oDbCon->paramString($pkey).' order by pkey asc';	
		return $this->oDbCon->doQuery($sql);
    }
    
     function updateFile($pkey, $token, $arrFile)
    {

        if (!empty($arrFile))
            $this->validateDiskUsage();

        $sourcePath = $this->uploadTempDoc . $this->uploadFileFolder . $token;
        $destinationPath = $this->defaultDocUploadPath . $this->uploadFileFolder;


        if (!is_dir($destinationPath))
            mkdir($destinationPath, 0755, true);

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

    function updateDocumentFiles($pkey, $fieldName, $arrFile)
    {

        $arrayToJs = array();

        try {

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            $rsHeader = $this->getDataRowById($pkey);

            if ($rsHeader[0]['statuskey'] == 2) { // khusus kalo status "konfirmasi", TCO ad 5 status
                $this->updateFile($pkey, $arrFile[0]['token'], implode(",", array_column($arrFile, 'fileName')));
            }


            $this->oDbCon->endTrans();
            $this->addErrorList($arrayToJs, true, $this->lang['dataHasBeenSuccessfullyUpdated']);

        } catch (Exception $e) {
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs, false, $e->getMessage());
        }

        return $arrayToJs;
    }
    
    function getDocumentFiles($rs){

        $rsFiles = array();
        
        $refkey = $rs[0]['refkey'];
        $reftabletype = $rs[0]['reftabletype'];

        $obj = $this->getObjMapping('',$reftabletype); 
        if (empty($obj) || !isset($obj->tableFile)) return $rsFiles;
         
        // sementara cuma bisa narik yg punya tableFile saja. 
        
        $rsData = $obj->getFileDetail($refkey);
        if(empty($rsData)) return $rsFiles;
        
        $uploadFolder = $obj->uploadFileFolder;
           
        foreach($rsData as $row) {
            
                if (empty($row['file'])) continue;
            
                if($obj->useStorage){ 
                    $url = $this->createPresignedURL(DOMAIN_NAME.'/'.$uploadFolder.$refkey.'/'.$row['file']); 
                }else{
                    $url = '/download?filename='. $uploadFolder . $refkey.'/'.$row['file']; 
                }
             
                array_push($rsFiles, array(
                    'pkey' => $rs[0]['pkey'],
                    'refkey' => $row['refkey'],
                    'file' => $row['file'],
                    'url' => $url
                ));
        }
        
        //
        //if($obj->useStorage) {
        //     
        //    foreach($rsData as $row) {
        //        array_push($rsFiles, array(
        //            'pkey' => $rs[0]['pkey'],
        //            'refkey' => $row['refkey'],
        //            'file' => $row['file'],
        //            'uploadfolder' => ''
        //        ));
        //    }
//
        //} else {
        //    
        //    $rsData = $refObj->getDataRowById($refkey);
//
        //    array_push($rsFiles, array(
        //            'pkey' => $rs[0]['pkey'],
        //            'refkey' => $rsData[0]['pkey'],
        //            'file' => $rsData[0]['file'],
        //            'uploadfolder' => $refObj->uploadFileFolder
        //        ));
//
        //}
  
        return $rsFiles;

    }
    
}
 
?>
